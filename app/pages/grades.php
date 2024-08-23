<?php
include '../../includes/Header.php';
include '../../app/core/Connection.php';
include '../../app/core/CRUDHelper.php';

$CRUDHelper = new CRUDHelper();
$method = null;
$course = null;
$searchResult = [];

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
  $method = $_POST['method'];
}

$gradeQuery =
  'SELECT 
    g.id,
    g.enrollment_id,
    g.grade,
    g.comment,
    g.grade_date,
    s.first_name AS student_name
FROM grades g
INNER JOIN enrollments e ON g.enrollment_id = e.id
INNER JOIN students s ON e.student_id = s.id;';
$gradeResult = $CRUDHelper->getData($gradeQuery);

// * get enrollments
$enrollmentQuery = 'SELECT * FROM enrollments';
$enrollmentResult = $CRUDHelper->getData($enrollmentQuery);

// * create grade
if (strcasecmp($method, 'post') === 0) {
  $enrollmentId = $_POST['enrollmentId'];
  $grade = $_POST['grade'];
  $comment = $_POST['comment'];
  $gradeDate = $_POST['gradeDate'];

  $query =
    'INSERT INTO grades (
    enrollment_id, 
    grade, 
    comment,
    grade_date)
  VALUES (
    :enrollmentId, 
    :grade, 
    :comment,
    :gradeDate)';
  $statement = $connection->prepare($query);
  $statement->bindParam(':enrollmentId', $enrollmentId);
  $statement->bindParam(':grade', $grade);
  $statement->bindParam(':comment', $comment);
  $statement->bindParam(':gradeDate', $gradeDate);

  if ($statement->execute()) {
    header("Refresh:0");
    exit();
  } else {
    // ! error
  }
}

// * delete grade 
if (strcasecmp($method, 'delete') === 0) {
  $id = $_POST["id"];
  $query = 'DELETE FROM grades WHERE id = :id';
  $statement = $connection->prepare($query);
  $statement->bindParam(':id', $id);

  if ($statement->execute()) {
    header("Refresh:0");
    exit();
  } else {
    // error
  }
}

// * update grade
if (strcasecmp($method, 'put') === 0) {
  $id = $_POST['id'];
  $enrollmentId = $_POST['enrollmentId'];
  $grade = $_POST['grade'];
  $comment = $_POST['comment'];
  $gradeDate = $_POST['gradeDate'];

  $query =
    'UPDATE grades SET
    enrollment_id = :enrollmentId, 
    grade = :grade, 
    comment = :comment,
    grade_date = :gradeDate
  WHERE id = :id';

  $statement = $connection->prepare($query);
  $statement->bindParam(':enrollmentId', $enrollmentId);
  $statement->bindParam(':grade', $grade);
  $statement->bindParam(':comment', $comment);
  $statement->bindParam(':gradeDate', $gradeDate);
  $statement->bindParam(':id', $id, PDO::PARAM_INT);

  if ($statement->execute()) {
    header("Refresh:0");
    exit();
  } else {
    // ! error
  }
}

// * search grade
if (strcasecmp($method, 'patch') === 0) {
  $studentId = $_POST['studentId'];
  $query =
    'SELECT 
      g.id,
      g.enrollment_id,
      g.grade,
      g.comment,
      g.grade_date,
      st.first_name AS student_name,
      en.student_id,
      c.name AS course_name
    FROM grades g
    INNER JOIN enrollments en ON g.enrollment_id = en.id
    INNER JOIN students st ON en.student_id = st.id
    INNER JOIN event_schedule es ON en.event_schedule_id = es.id
    INNER JOIN events ev ON es.event_id = ev.id
    INNER JOIN courses c ON ev.course_id = c.id
    WHERE student_id = :studentId';

  $statement = $connection->prepare($query);
  $statement->bindParam(':studentId', $studentId, PDO::PARAM_INT);

  if ($statement->execute()) {
    $searchResult = $statement->fetchAll(PDO::FETCH_ASSOC);
    // header("Refresh:0");
    // exit();
  } else {
    // ! error
  }
}

?>
<h3>Consulta Rápida</h3>
<div class="" style="">
  <form class="search" method="post">
    <input type="text" name="method" value="patch" class="hidden">
    <label>Id de estudiante
      <input type="number" name="studentId">
    </label>
    <input type="submit" class="button" value="Buscar">
  </form>
</div>

<!-- table1 -->
<table class="hover unstriped">
  <thead>
    <tr>
      <td>Id</td>
      <td>Estudiante</td>
      <td>Curso</td>
      <td>Notas</td>
      <td>Fecha</td>
    </tr>
  </thead>
  <tbody>
    <?php foreach ($searchResult as $row) { ?>
      <tr>
        <td><?php echo $row['id']; ?></td>
        <td><?php echo $row['student_name']; ?></td>
        <td><?php echo $row['course_name']; ?></td>
        <td><?php echo $row['grade']; ?></td>
        <td><?php echo $row['grade_date']; ?></td>
      </tr>
    <?php } ?>
  </tbody>
</table>

<!-- table2 -->
<h3>Tabla de Notas</h3>
<button class="button" data-open="exampleModal1">Agregar nota</button>
<table class="hover unstriped">
  <thead>
    <tr>
      <td>Id</td>
      <td>Estudiante</td>
      <td>Notas</td>
      <td>Fecha</td>
      <td>Acciones</td>
    </tr>
  </thead>
  <tbody>
    <?php foreach ($gradeResult as $row) { ?>
      <tr data-user='<?php echo json_encode($row); ?>'>
        <td><?php echo $row['id']; ?></td>
        <td><?php echo $row['student_name']; ?></td>
        <td><?php echo $row['grade']; ?></td>
        <td><?php echo $row['grade_date']; ?></td>
        <td>
          <button class="edit-btn button warning m-0" data-open="exampleModal2"
            data-grade='<?php echo json_encode($row); ?>'>Editar</button>
          <form method="POST">
            <input type="text" name="method" class="hidden" value="delete">
            <input type="text" name="id" class="hidden" value="<?php echo $row['id']; ?>">
            <input type="submit" class="button alert m-0" value="Eliminar">
          </form>
        </td>
      </tr>
    <?php } ?>
  </tbody>
</table>

<!-- create modal -->
<div class="reveal" id="exampleModal1" data-reveal>
  <h3>Agrega una nota</h3>
  <form method="POST">
    <input type="text" name="method" value="post" class="hidden">
    <label>Inscrición
      <select name="enrollmentId">
        <?php foreach ($enrollmentResult as $row) { ?>
          <option value="<?php echo $row['id']; ?>">
            <?php echo $row['id']; ?>
          </option>
        <?php } ?>
      </select>
    </label>
    <label>Nota
      <input type="number" name="grade">
    </label>
    <label>Comentario
      <textarea name="comment" placeholder="None"></textarea>
    </label>
    <label>Fecha de asignación de nota
      <input type="date" name="gradeDate">
    </label>
    <div class="clearfix">
      <input type="submit" class="button float-right" value="Crear">
      <button class="button secondary float-right" data-close aria-label="Close modal" type="button"
        style="margin-right: 1rem;">Cancelar</button>
    </div>
  </form>

  <button class="close-button" data-close aria-label="Close modal" type="button">
    <span aria-hidden="true">&times;</span>
  </button>
</div>

<!-- edit modal -->
<div class="reveal" id="exampleModal2" data-reveal>
  <h3>Editar una nota</h3>
  <form class="edit-form" method="POST">
    <input type="text" name="method" value="put" class="hidden">
    <label>Id
      <input type="text" name="id" readonly>
    </label>
    <label>Inscrición
      <select name="enrollmentId">
        <?php foreach ($enrollmentResult as $row) { ?>
          <option value="<?php echo $row['id']; ?>">
            <?php echo $row['id']; ?>
          </option>
        <?php } ?>
      </select>
    </label>
    <label>Nota
      <input type="number" name="grade">
    </label>
    <label>Comentario
      <textarea name="comment" placeholder="None"></textarea>
    </label>
    <label>Fecha de asignación de nota
      <input type="date" name="gradeDate">
    </label>
    <div class="clearfix">
      <input type="submit" class="button float-right" value="Editar">
      <button class="button secondary float-right" data-close aria-label="Close modal" type="button"
        style="margin-right: 1rem;">Cancelar</button>
    </div>
  </form>
  <button class="close-button" data-close aria-label="Close modal" type="button">
    <span aria-hidden="true">&times;</span>
  </button>
</div>

<script>
  // * Carga del documento
  document.addEventListener('DOMContentLoaded', e => {
    const $editform = document.querySelector('.edit-form');
    const $links = Array.from(document.querySelectorAll('.link'));
    const $profesoresLink = $links.find(link => link.textContent.trim() === 'Notas');
    $profesoresLink.classList.add('selected');

    document.addEventListener('click', e => {
      // * cargar datos al form
      if (e.target.matches('.edit-btn')) {
        const gradeData = JSON.parse(e.target.dataset.grade);
        $editform.id.value = gradeData.id;
        $editform.enrollmentId.value = gradeData.enrollment_id;
        $editform.grade.value = gradeData.grade;
        $editform.comment.value = gradeData.comment;
        $editform.gradeDate.value = gradeData.grade_date;
      }
    });
  });
</script>

<?php include '../../includes/Footer.php'; ?>