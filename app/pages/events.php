<?php
include '../../includes/Header.php';
include '../../app/core/Connection.php';
include '../../app/core/CRUDHelper.php';

$CRUDHelper = new CRUDHelper();
$method = null;
$student = null;

$eventQuery =
'SELECT 
    e.id,
    e.pensum_id,
    e.course_id,
    p.name AS pensum_name,
    c.name AS course_name,
    c.prerequisite
FROM events e
INNER JOIN pensums p ON e.pensum_id = p.id
INNER JOIN courses c ON e.course_id = c.id;';
$eventResult = $CRUDHelper->getData($eventQuery);

// * get pensums
$pensumQuery = 'SELECT * FROM pensums';
$pensumResult = $CRUDHelper->getData($pensumQuery);

// * get courses
$courseQuery = 'SELECT * FROM courses';
$courseResult = $CRUDHelper->getData($courseQuery);


if ($_SERVER['REQUEST_METHOD'] === 'POST') {
  $method = $_POST['method'];
}

// * create teacher
if (strcasecmp($method, 'post') === 0) {
  $pensumId = $_POST['pensumId'];
  $courseId = $_POST['courseId'];
  $query =
    'INSERT INTO events (
    pensum_id, 
    course_id)
  VALUES (
    :pensumId, 
    :courseId)';
  $statement = $connection->prepare($query);
  $statement->bindParam(':pensumId', $pensumId);
  $statement->bindParam(':courseId', $courseId);

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
  $query = 'DELETE FROM events WHERE id = :id';
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
  $pensumId = $_POST['pensumId'];
  $courseId = $_POST['courseId'];
  $query =
    'UPDATE events SET
    pensum_id = :pensumId, 
    course_id = :courseId
  WHERE id = :id';

  $statement = $connection->prepare($query);
  $statement->bindParam(':pensumId', $pensumId);
  $statement->bindParam(':courseId', $courseId);
  $statement->bindParam(':id', $id, PDO::PARAM_INT);

  if ($statement->execute()) {
    header("Refresh:0");
    exit();
  } else {
    // ! error
  }
}

?>
<h3>Tabla de Eventos</h3>
<button class="button" data-open="exampleModal1">Agregar evento</button>
<table class="hover unstriped">
  <thead>
    <tr>
      <td>Id</td>
      <td>Pensum</td>
      <td>Curso</td>
      <td>Prerrequisito</td>
      <td>Acciones</td>
    </tr>
  </thead>
  <tbody>
    <?php foreach ($eventResult as $row) { ?>
      <tr data-user='<?php echo json_encode($row); ?>'>
        <td><?php echo $row['id']; ?></td>
        <td><?php echo $row['pensum_name']; ?></td>
        <td><?php echo $row['course_name']; ?></td>
        <td><?php echo $row['prerequisite']; ?></td>
        <td>
          <button class="edit-btn button warning m-0" data-open="exampleModal2"
            data-event='<?php echo json_encode($row); ?>'>Editar</button>
          <form method="POST">
            <input type="text" name="method" class="hidden" value="delete">
            <input type="text" name="id" class="hidden" value="<?php echo $row['id']; ?>">
            <input type="submit" class="button alert m-0" value="Eliminar">
          </form>
        </td>
      <?php } ?>
    </tr>
  </tbody>
</table>

<!-- create modal -->
<div class="reveal" id="exampleModal1" data-reveal>
  <h3>Agrega un cruso</h3>
  <form method="POST">
    <input type="text" name="method" value="post" class="hidden">
    <label>Pensum
      <select name="pensumId">
        <?php foreach ($pensumResult as $row) { ?>
          <option value="<?php echo $row['id']; ?>">
            <?php echo $row['name']; ?>
          </option>
        <?php } ?>
      </select>
    </label>
    <label>Curso
      <select name="courseId">
        <?php foreach ($courseResult as $row) { ?>
          <option value="<?php echo $row['id']; ?>">
            <?php echo $row['name']; ?>
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
  <h3>Editar un evento</h3>
  <form class="edit-form" method="POST">
    <input type="text" name="method" value="put" class="hidden">
    <label>Id
      <input type="text" name="id" readonly>
    </label>
    <label>Pensum
      <select name="pensumId">
        <?php foreach ($pensumResult as $row) { ?>
          <option value="<?php echo $row['id']; ?>">
            <?php echo $row['name']; ?>
          </option>
        <?php } ?>
      </select>
    </label>
    <label>Curso
      <select name="courseId">
        <?php foreach ($courseResult as $row) { ?>
          <option value="<?php echo $row['id']; ?>">
            <?php echo $row['name']; ?>
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
    const $profesoresLink = $links.find(link => link.textContent.trim() === 'Eventos');
    $profesoresLink.classList.add('selected');

    document.addEventListener('click', e => {
      // * cargar datos al form
      if (e.target.matches('.edit-btn')) {
        const eventData = JSON.parse(e.target.dataset.event);
        $editform.id.value = eventData.id;
        $editform.pensumId.value = eventData.pensum_id;
        $editform.courseId.value = eventData.course_id;
      }
    });
  });
</script>

<?php include '../../includes/Footer.php'; ?>