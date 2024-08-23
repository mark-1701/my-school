<?php
include 'includes/Header.php';
include 'app/core/Connection.php';
include 'app/core/CRUDHelper.php';
?>

<div class="dashboard">
  <p>Estimado visitante,</p>
  <p>Nos complace informarte que has sido aceptado(a) en el sistema web de Hogwarts para la gestión de notas y cursos. Estás invitado(a) a asistir a una reunión de orientación en la Fecha a la Hora. Para evitar ser detectado por muggles, debes simular que estás participando en una actividad en línea relacionada con la magia.</p>
  <p>Se ha organizado para que el profesor Albus Dumbledore te guíe a través de la plataforma web, donde podrás acceder a todas las funciones del sistema, incluyendo la visualización de tus notas y la gestión de tus cursos. Tus padres o tutores pueden acompañarte utilizando la red de floo o cualquier medio de transporte muggle para acceder al sistema desde su ubicación.</p>
  <p>Para que parezca una actividad educativa, te pedimos que tengas a mano tus libros de estudio (en formato digital está bien) para compartir con los otros estudiantes durante la orientación. No se requiere ningún otro material adicional.</p>
  <p>Atentamente,</p>
  <p>Minerva Mc Gonagall,</p>
  <p>Deputy headmistress</p>
</div>

<script>
  // * Carga del documento
  document.addEventListener('DOMContentLoaded', e => {
    const $main =document.querySelector('main');
    $main.classList.add('dashboard-main');
    const $editform = document.querySelector('.edit-form');
    const $links = Array.from(document.querySelectorAll('.link'));
    const $profesoresLink = $links.find(link => link.textContent.trim() === 'Dashboard');
    $profesoresLink.classList.add('selected');

    document.addEventListener('click', e => {
    });
  });
</script>
<?php include 'includes/Footer.php'; ?>