<?php
include '../../includes/Header.php';
include '../../app/core/Connection.php';
include '../../app/core/CRUDHelper.php';

$CRUDHelper = new CRUDHelper();
$method = null;
$course = null;

$scheduleQuery = 'SELECT * FROM event_schedule';
$scheduleResult = $CRUDHelper->getData($scheduleQuery);

// * get pensums
$eventQuery = 'SELECT * FROM events';
$eventResult = $CRUDHelper->getData($eventQuery);

// * get teachers
$teacherQuery = 'SELECT * FROM teachers';
$teacherResult = $CRUDHelper->getData($teacherQuery);

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
  $method = $_POST['method'];
}

// * create schedule
if (strcasecmp($method, 'post') === 0) {
  $eventId = $_POST['eventId'];
  $teacherId = $_POST['teacherId'];
  $days = $_POST['days'];
  $startTime = $_POST['startTime'];
  $endTime = $_POST['endTime'];
  $daysJSON = json_encode($days);

  $query =
    'INSERT INTO event_schedule (
    event_id, 
    teacher_id, 
    day_of_week,
    start_time,
    end_time)
  VALUES (
    :eventId, 
    :teacherId, 
    :daysJSON,
    :startTime,
    :endTime)';
  $statement = $connection->prepare($query);
  $statement->bindParam(':eventId', $eventId);
  $statement->bindParam(':teacherId', $teacherId);
  $statement->bindParam(':daysJSON', $daysJSON);
  $statement->bindParam(':startTime', $startTime);
  $statement->bindParam(':endTime', $endTime);

  if ($statement->execute()) {
    header("Refresh:0");
    exit();
  } else {
    // ! error
  }
}

// * delete student 
if (strcasecmp($method, 'delete') === 0) {
  $id = $_POST["id"];
  $query = 'DELETE FROM event_schedule WHERE id = :id';
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
  $eventId = $_POST['eventId'];
  $teacherId = $_POST['teacherId'];
  $days = $_POST['days'];
  $startTime = $_POST['startTime'];
  $endTime = $_POST['endTime'];
  $daysJSON = json_encode($days);

  $query =
    'UPDATE event_schedule SET
    event_id = :eventId, 
    teacher_id = :teacherId, 
    day_of_week = :daysJSON,
    start_time = :startTime,
    end_time = :endTime
  WHERE id = :id';

  $statement = $connection->prepare($query);
  $statement->bindParam(':eventId', $eventId);
  $statement->bindParam(':teacherId', $teacherId);
  $statement->bindParam(':daysJSON', $daysJSON);
  $statement->bindParam(':startTime', $startTime);
  $statement->bindParam(':endTime', $endTime);
  $statement->bindParam(':id', $id, PDO::PARAM_INT);

  if ($statement->execute()) {
    header("Refresh:0");
    exit();
  } else {
    // ! error
  }
}
?>

<h3>Tabla de Programación de cursos</h3>
<button class="button" data-open="exampleModal1">Agregar programación</button>
<table class="hover unstriped">
  <table class="hover unstriped">
    <thead>
      <tr>
        <td>Id</td>
        <td>Evento</td>
        <td>Hora de comiezo</td>
        <td>Hora de finalización</td>
        <td>Acciones</td>
      </tr>
    </thead>
    <tbody>
      <?php foreach ($scheduleResult as $row) { ?>
        <tr data-user='<?php echo json_encode($row); ?>'>
          <td><?php echo $row['id']; ?></td>
          <td><?php echo $row['event_id']; ?></td>
          <td><?php echo $row['start_time']; ?></td>
          <td><?php echo $row['end_time']; ?></td>
          <td>
            <button class="edit-btn button warning m-0" data-open="exampleModal2"
              data-schedule='<?php echo json_encode($row); ?>'>Editar</button>
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
    <h3>Agrega un curso</h3>
    <form method="POST">
      <input type="text" name="method" value="post" class="hidden">
      <label>Evento
        <select name="eventId">
          <?php foreach ($eventResult as $row) { ?>
            <option value="<?php echo $row['id']; ?>">
              <?php echo $row['id']; ?>
            </option>
          <?php } ?>
        </select>
      </label>
      <label>Profesor
        <select name="teacherId">
          <?php foreach ($teacherResult as $row) { ?>
            <option value="<?php echo $row['id']; ?>">
              <?php echo $row['id'] . ' - ' . $row['first_name']; ?>
            </option>
          <?php } ?>
        </select>
      </label>
      <label>Días por semana
        <select name="days[]" multiple>
          <option value="monday">Lunes</option>
          <option value="tuesday">Martes</option>
          <option value="wednesday">Miércoles</option>
          <option value="thursday">Jueves</option>
          <option value="friday">Viernes</option>
          <option value="saturday">Sábado</option>
          <option value="sunday">Domingo</option>
        </select>
      </label>
      <label>Hora de inicio
        <input type="time" name="startTime">
      </label>
      <label>Hora final
        <input type="time" name="endTime">
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
    <h3>Editar un curso</h3>
    <form class="edit-form" method="POST">
      <input type="text" name="method" value="put" class="hidden">
      <label>Id
        <input type="text" name="id" readonly>
      </label>
      <label>Evento
        <select name="eventId">
          <?php foreach ($eventResult as $row) { ?>
            <option value="<?php echo $row['id']; ?>">
              <?php echo $row['id']; ?>
            </option>
          <?php } ?>
        </select>
      </label>
      <label>Profesor
        <select name="teacherId">
          <?php foreach ($teacherResult as $row) { ?>
            <option value="<?php echo $row['id']; ?>">
              <?php echo $row['id'] . ' - ' . $row['first_name']; ?>
            </option>
          <?php } ?>
        </select>
      </label>
      <label>Días por semana
        <select name="days[]" multiple>
          <option value="monday">Lunes</option>
          <option value="tuesday">Martes</option>
          <option value="wednesday">Miércoles</option>
          <option value="thursday">Jueves</option>
          <option value="friday">Viernes</option>
          <option value="saturday">Sábado</option>
          <option value="sunday">Domingo</option>
        </select>
      </label>
      <label>Hora de inicio
        <input type="time" name="startTime">
      </label>
      <label>Hora final
        <input type="time" name="endTime">
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
      const $profesoresLink = $links.find(link => link.textContent.trim() === 'Programacion');
      $profesoresLink.classList.add('selected');

      document.addEventListener('click', e => {
        // * cargar datos al form
        if (e.target.matches('.edit-btn')) {
          const scheduleData = JSON.parse(e.target.dataset.schedule);
          const selectedDays = JSON.parse(scheduleData.day_of_week);
          $editform.id.value = scheduleData.id;
          $editform.eventId.value = scheduleData.event_id;
          $editform.teacherId.value = scheduleData.teacher_id;
          $editform.startTime.value = scheduleData.start_time;
          $editform.endTime.value = scheduleData.end_time;
          selectedDays.forEach(day => {
            let option = $editform.querySelector(`option[value='${day}']`);
            if (option) option.selected = true;
          });
        }
      });
    });
  </script>

  <?php include '../../includes/Footer.php'; ?>