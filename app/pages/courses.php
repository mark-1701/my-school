<?php
include '../../includes/Header.php';
include '../../app/core/Connection.php';
include '../../app/core/CRUDHelper.php';

$CRUDHelper = new CRUDHelper();
$method = null;
$course = null;

$courseQuery = 'SELECT * FROM courses';
$courseResult = $CRUDHelper->getData($courseQuery);

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
  $method = $_POST['method'];
}

// * create course
if (strcasecmp($method, 'post') === 0) {
  $name = $_POST['name'];
  $prerequisite = $_POST['prerequisite'];
  $description = $_POST['description'];

  $query =
    'INSERT INTO courses (
    name, 
    prerequisite, 
    description)
  VALUES (
    :name, 
    :prerequisite, 
    :description)';
  $statement = $connection->prepare($query);
  $statement->bindParam(':name', $name);
  $statement->bindParam(':prerequisite', $prerequisite);
  $statement->bindParam(':description', $description);

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
  $query = 'DELETE FROM courses WHERE id = :id';
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
  $name = $_POST['name'];
  $prerequisite = $_POST['prerequisite'];
  $description = $_POST['description'];

  $query =
    'UPDATE courses SET
    name = :name, 
    prerequisite = :prerequisite, 
    description = :description
  WHERE id = :id';

  $statement = $connection->prepare($query);
  $statement->bindParam(':name', $name);
  $statement->bindParam(':prerequisite', $prerequisite);
  $statement->bindParam(':description', $description);
  $statement->bindParam(':id', $id, PDO::PARAM_INT);

  if ($statement->execute()) {
    header("Refresh:0");
    exit();
  } else {
    // ! error
  }
}
?>
<h3>Tabla de Cursos</h3>
<button class="button" data-open="exampleModal1">Agregar curso</button>
<table class="hover unstriped">
  <thead>
    <tr>
      <td>Id</td>
      <td>Prerequisitos</td>
      <td>Nombre</td>
      <td>Descripcion</td>
      <td>Acciones</td>
    </tr>
  </thead>
  <tbody>
    <?php foreach ($courseResult as $row) { ?>
      <tr data-user='<?php echo json_encode($row); ?>'>
        <td><?php echo $row['id']; ?></td>
        <td><?php echo $row['prerequisite']; ?></td>
        <td><?php echo $row['name']; ?></td>
        <td><?php echo $row['description']; ?></td>
        <td>
          <button class="edit-btn button warning m-0" data-open="exampleModal2"
            data-course='<?php echo json_encode($row); ?>'>Editar</button>
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
  <h3>Agrega un cruso</h3>
  <form method="POST">
    <input type="text" name="method" value="post" class="hidden">
    <label>Nombre
      <input type="text" name="name">
    </label>
    <label>Prerequisito
      <select name="prerequisite">
        <option value="none">None</option>
      </select>
    </label>
    <label>Descripción
      <textarea name="description" placeholder="None"></textarea>
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
    <label>Nombre
      <input type="text" name="name">
    </label>
    <label>Prerequisito
      <select name="prerequisite">
        <option value="none">None</option>
      </select>
    </label>
    <label>Descripción
      <textarea name="description" placeholder="None"></textarea>
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
    const $profesoresLink = $links.find(link => link.textContent.trim() === 'Cursos');
    $profesoresLink.classList.add('selected');

    document.addEventListener('click', e => {
      // * cargar datos al form
      if (e.target.matches('.edit-btn')) {
        const courseData = JSON.parse(e.target.dataset.course);
        $editform.id.value = courseData.id;
        $editform.name.value = courseData.name;
        // $editform.prerequisite.value = courseData.prerequisite;
        $editform.description.value = courseData.description;
      }
    });
  });
</script>

<?php include '../../includes/Footer.php'; ?>