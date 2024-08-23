<?php
include '../../includes/Header.php';
include '../../app/core/Connection.php';
include '../../app/core/CRUDHelper.php';

$CRUDHelper = new CRUDHelper();
$method = null;
$enrollmentQuery = 
'SELECT 
    e.id,
    e.student_id,
    e.event_schedule_id,
    s.first_name,
    es.event_id,
    es.start_time,
    es.end_time,
    c.name AS course_name
FROM enrollments e
INNER JOIN students s ON e.student_id = s.id
INNER JOIN event_schedule es ON e.event_schedule_id = es.id
INNER JOIN events ev ON es.event_id = ev.id
INNER JOIN courses c ON ev.course_id = c.id;';
$enrollmentResult = $CRUDHelper->getData($enrollmentQuery);

// * get students
$studentQuery = 'SELECT * FROM students';
$studentResult = $CRUDHelper->getData($studentQuery);

// * get event_schedule
$eventScheduleQuery =
'SELECT
  es.id,
  c.name AS course_name
FROM event_schedule es
INNER JOIN events ev ON es.event_id = ev.id
INNER JOIN courses c ON ev.course_id = c.id;';
$eventScheduleResult = $CRUDHelper->getData($eventScheduleQuery);

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
  $method = $_POST['method'];
}

// * create teacher
if (strcasecmp($method, 'post') === 0) {
  $studentId = $_POST['studentId'];
  $eventScheduleId = $_POST['eventScheduleId'];
  $query =
    'INSERT INTO enrollments (
    student_id, 
    event_schedule_id)
  VALUES (
    :studentId, 
    :eventScheduleId)';
  $statement = $connection->prepare($query);
  $statement->bindParam(':studentId', $studentId);
  $statement->bindParam(':eventScheduleId', $eventScheduleId);

  if ($statement->execute()) {
    header("Refresh:0");
    exit();
  } else {
    // ! error
  }
}

// * delete teacher 
if (strcasecmp($method, 'delete') === 0) {
  $id = $_POST["id"];
  $query = 'DELETE FROM enrollments WHERE id = :id';
  $statement = $connection->prepare($query);
  $statement->bindParam(':id', $id);

  if ($statement->execute()) {
    header("Refresh:0");
    exit();
  } else {
    // error
  }
}

// * update teacher
if (strcasecmp($method, 'put') === 0) {
  $id = $_POST['id'];
  $studentId = $_POST['studentId'];
  $eventScheduleId = $_POST['eventScheduleId'];
  $query =
    'UPDATE enrollments SET
    student_id = :studentId, 
    event_schedule_id = :eventScheduleId
  WHERE id = :id';

  $statement = $connection->prepare($query);
  $statement->bindParam(':studentId', $studentId);
  $statement->bindParam(':eventScheduleId', $eventScheduleId);
  $statement->bindParam(':id', $id, PDO::PARAM_INT);

  if ($statement->execute()) {
    header("Refresh:0");
    exit();
  } else {
    // ! error
  }
}

?>
<h3>Tabla de Incripciones</h3>
<button class="button" data-open="exampleModal1">Agregar inscripción</button>
<table class="hover unstriped">
  <thead>
    <tr>
      <td>Id</td>
      <td>Estudiante</td>
      <td>Evento</td>
      <td>Horario</td>
      <td>Acciones</td>
    </tr>
  </thead>
  <tbody>
    <?php foreach ($enrollmentResult as $row) { ?>
      <tr data-user='<?php echo json_encode($row); ?>'>
        <td><?php echo $row['id']; ?></td>
        <td><?php echo $row['first_name']; ?></td>
        <td><?php echo $row['event_schedule_id'] . ' - ' . $row['course_name']; ?></td>
        <td><?php echo $row['start_time'] . ' - ' . $row['end_time']; ?></td>
        <td>
          <button class="edit-btn button warning m-0" data-open="exampleModal2"
            data-enrollment='<?php echo json_encode($row); ?>'>Editar</button>
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
  <h3>Agrega una incripción</h3>
  <form method="POST">
    <input type="text" name="method" value="post" class="hidden">
    <label>Estudiante
      <select name="studentId">
        <?php foreach ($studentResult as $row) { ?>
          <option value="<?php echo $row['id']; ?>">
            <?php echo $row['first_name']; ?>
          </option>
        <?php } ?>
      </select>
    </label>
    <label>Evento Programados
      <select name="eventScheduleId">
        <?php foreach ($eventScheduleResult as $row) { ?>
          <option value="<?php echo $row['id']; ?>">
            <?php echo $row['id'] . ' - ' . $row['course_name']; ?>
          </option>
        <?php } ?>
      </select>
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
  <h3>Editar una inscripción</h3>
  <form class="edit-form" method="POST">
    <input type="text" name="method" value="put" class="hidden">
    <label>Id
      <input type="text" name="id" readonly>
    </label>
    <label>Estudiante
      <select name="studentId">
        <?php foreach ($studentResult as $row) { ?>
          <option value="<?php echo $row['id']; ?>">
            <?php echo $row['first_name']; ?>
          </option>
        <?php } ?>
      </select>
    </label>
    <label>Evento Programados
      <select name="eventScheduleId">
        <?php foreach ($eventScheduleResult as $row) { ?>
          <option value="<?php echo $row['id']; ?>">
            <?php echo $row['id'] . ' - ' . $row['course_name']; ?>
          </option>
        <?php } ?>
      </select>
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
    const $profesoresLink = $links.find(link => link.textContent.trim() === 'Incripciones');
    $profesoresLink.classList.add('selected');

    document.addEventListener('click', e => {
      // * cargar datos al form
      if (e.target.matches('.edit-btn')) {
        const enrollmentData = JSON.parse(e.target.dataset.enrollment);
        $editform.id.value = enrollmentData.id;
        $editform.studentId.value = enrollmentData.student_id;
        $editform.eventScheduleId.value = enrollmentData.event_schedule_id;
      }
    });
  });
</script>

<?php include '../../includes/Footer.php'; ?>