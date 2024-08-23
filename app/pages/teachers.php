<?php
include '../../includes/Header.php';
include '../../app/core/Connection.php';
include '../../app/core/CRUDHelper.php';

$CRUDHelper = new CRUDHelper();
$method = null;
$teacher = null;

// * get users
$userQuery =
  'SELECT 
  t.id, 
  t.first_name, 
  t.last_name, 
  t.gender, 
  t.address, 
  t.email, 
  t.phone_number,
  t.date_of_birth, 
  t.profession_id, p.name AS profession
FROM teachers t
LEFT JOIN professions p
ON t.profession_Id = p.id';
$userResult = $CRUDHelper->getData($userQuery);

// * get professions
$professionQuery = 'SELECT * FROM professions';
$professionResult = $CRUDHelper->getData($professionQuery);


if ($_SERVER['REQUEST_METHOD'] === 'POST') {
  $method = $_POST['method'];
}

// * create teacher
if (strcasecmp($method, 'post') === 0) {
  $professionId = $_POST['profession'];
  $firstName = $_POST['firstname'];
  $lastName = $_POST['lastname'];
  $gender = $_POST['gender'];
  $address = $_POST['address'];
  $email = $_POST['email'];
  $phoneNumber = $_POST['phoneNumber'];
  $dateOfBirth = $_POST['dateOfBirth'];

  $query =
    'INSERT INTO teachers (
    profession_id, 
    first_name, 
    last_name, 
    gender, 
    address, 
    email, 
    phone_number, 
    date_of_birth)
  VALUES (
    :professionId, 
    :firstName, 
    :lastName, 
    :gender, 
    :address, 
    :email, 
    :phoneNumber, 
    :dateOfBirth)';
  $statement = $connection->prepare($query);
  $statement->bindParam(':professionId', $professionId);
  $statement->bindParam(':firstName', $firstName);
  $statement->bindParam(':lastName', $lastName);
  $statement->bindParam(':gender', $gender);
  $statement->bindParam(':address', $address);
  $statement->bindParam(':email', $email);
  $statement->bindParam(':phoneNumber', $phoneNumber);
  $statement->bindParam(':dateOfBirth', $dateOfBirth);

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
  $query = 'DELETE FROM teachers WHERE id = :id';
  $statement = $connection->prepare($query);
  $statement->bindParam(':id', $id);

  if ($statement->execute()) {
    header("Refresh:0");
    exit();
  } else {
    // error
  }
}

// show teacher
// if (strcasecmp($method, 'get') === 0) {
//   $id = $_POST["id"];
//   $query = "SELECT * FROM teachers WHERE id = :id";
//   $statement = $connection->prepare($query);
//   $statement->bindParam(':id', $id, PDO::PARAM_INT);
//   $statement->execute();
//   $teacher = $statement->fetch(PDO::FETCH_ASSOC);
// }

// * update teacher
if (strcasecmp($method, 'put') === 0) {
  $id = $_POST['id'];
  $professionId = $_POST['profession'];
  $firstName = $_POST['firstname'];
  $lastName = $_POST['lastname'];
  $gender = $_POST['gender'];
  $address = $_POST['address'];
  $email = $_POST['email'];
  $phoneNumber = $_POST['phoneNumber'];
  $dateOfBirth = $_POST['dateOfBirth'];

  $query =
    'UPDATE teachers SET profession_id = :professionId, 
    first_name = :firstName, 
    last_name = :lastName, 
    gender = :gender, 
    address = :address, 
    email = :email, 
    phone_number = :phoneNumber, 
    date_of_birth = :dateOfBirth 
  WHERE id = :id';

  $statement = $connection->prepare($query);
  $statement->bindParam(':professionId', $professionId);
  $statement->bindParam(':firstName', $firstName);
  $statement->bindParam(':lastName', $lastName);
  $statement->bindParam(':gender', $gender);
  $statement->bindParam(':address', $address);
  $statement->bindParam(':email', $email);
  $statement->bindParam(':phoneNumber', $phoneNumber);
  $statement->bindParam(':dateOfBirth', $dateOfBirth);
  $statement->bindParam(':id', $id, PDO::PARAM_INT);

  if ($statement->execute()) {
    header("Refresh:0");
    exit();
  } else {
    // ! error
  }
}
?>
<h3>Tabla de Profesores</h3>
<button class="button" data-open="exampleModal1">Agregar profesor</button>
<table class="hover unstriped">
  <thead>
    <tr>
      <td>Id</td>
      <td>Nombres</td>
      <td>Apellidos</td>
      <td>Profesión</td>
      <td>Acciones</td>
    </tr>
  </thead>
  <tbody>
    <?php foreach ($userResult as $row) { ?>
      <tr data-user='<?php echo json_encode($row); ?>'>
        <td><?php echo $row['id']; ?></td>
        <td><?php echo $row['first_name']; ?></td>
        <td><?php echo $row['last_name']; ?></td>
        <td><?php echo $row['profession']; ?></td>
        <td>
          <button class="edit-btn button warning m-0" data-open="exampleModal2"
            data-user='<?php echo json_encode($row); ?>'>Editar</button>
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
  <h3>Agrega a un profesor</h3>
  <form method="POST">
    <input type="text" name="method" value="post" class="hidden">
    <label>Nombres
      <input type="text" name="firstname">
    </label>
    <label>Apellidos
      <input type="text" name="lastname">
    </label>
    <label>Género
      <select name="gender">
        <option value="1">Hombre</option>
        <option value="2">Mujer</option>
        <option value="3">Otro</option>
      </select>
    </label>
    <label>Dirección
      <input type="text" name="address">
    </label>
    <label>Email
      <input type="email" name="email">
    </label>
    <label>Número telefonico
      <input type="number" name="phoneNumber">
    </label>
    <label>Fecha de Nacimiento
      <input type="date" name="dateOfBirth" value="2003-02-20">
    </label>
    <label>Profesión
      <select name="profession">
        <?php foreach ($professionResult as $row) { ?>
          <option value="<?php echo $row['id']; ?>">
            <?php echo $row['name']; ?>
          </option>
        <?php } ?>
      </select>
    </label>
    <label>Especialización
      <input type="text" placeholder="No disponible" disabled>
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
  <h3>Editar a un profesor</h3>
  <form class="edit-form" method="POST">
    <input type="text" name="method" value="put" class="hidden">
    <label>Id
      <input type="text" name="id" readonly>
    </label>
    <label>Nombres
      <input type="text" name="firstname">
    </label>
    <label>Apellidos
      <input type="text" name="lastname">
    </label>
    <label>Género
      <select name="gender">
        <option value="1">Hombre</option>
        <option value="2">Mujer</option>
        <option value="3">Otro</option>
      </select>
    </label>
    <label>Dirección
      <input type="text" name="address">
    </label>
    <label>Email
      <input type="email" name="email">
    </label>
    <label>Número telefonico
      <input type="number" name="phoneNumber">
    </label>
    <label>Fecha de Nacimiento
      <input type="date" name="dateOfBirth" value="2003-02-20">
    </label>
    <label>Profesión
      <select name="profession">
        <?php foreach ($professionResult as $row) { ?>
          <option value="<?php echo $row['id']; ?>">
            <?php echo $row['name']; ?>
          </option>
        <?php } ?>
      </select>
    </label>
    <label>Especialización
      <input type="text" placeholder="No disponible" disabled>
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
    const $profesoresLink = $links.find(link => link.textContent.trim() === 'Profesores');
    $profesoresLink.classList.add('selected');


    document.addEventListener('click', e => {
      // * cargar datos al form
      if (e.target.matches('.edit-btn')) {
        const userData = JSON.parse(e.target.dataset.user);
        $editform.id.value = userData.id;
        $editform.firstname.value = userData.first_name;
        $editform.lastname.value = userData.last_name;
        $editform.gender.value = userData.gender;
        $editform.address.value = userData.address;
        $editform.email.value = userData.email;
        $editform.phoneNumber.value = userData.phone_number;
        $editform.dateOfBirth.value = userData.date_of_birth;
        $editform.profession.value = userData.profession_id;
      }
    });
  });
</script>


<?php include '../../includes/Footer.php'; ?>