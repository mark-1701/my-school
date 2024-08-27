<?php
include '../../includes/Header.php';
include '../../app/core/Connection.php';
include '../../app/core/CRUDHelper.php';

$CRUDHelper = new CRUDHelper();
?>
<h3>Manuales</h3>
<div class="grid-x grid-margin-x" style="">
  <div class="cell large-2" style="width: 300px;">
    <div class="card">
      <div class="card-divider">
        <h4>Manual técnico</h4>
      </div>
      <img style="height: 350px; object-fit: cover;" src="/php-proyects/exercises/myschool/public/assets/img/user_manual_cover.png">
      <div class="card-section">
        <p>Documento con toda la documentación técnica.</p>
        <a href="/php-proyects/exercises/myschool/public/docs/manual_tecnico.pdf" target="_blank">Enlace</a>
      </div>
    </div>
  </div>
  <div class="cell large-2" style="width: 300px;">
    <div class="card">
      <div class="card-divider">
        <h4>Manual de usuario</h4>
      </div>
      <img style="height: 350px; object-fit: cover;" src="/php-proyects/exercises/myschool/public/assets/img/technical_manual_cover.png">
      <div class="card-section">
        <p>Documento para toda la orientación de usuarios.</p>
        <a href="/php-proyects/exercises/myschool/public/docs/manual_usuario.pdf" target="_blank">Enlace</a>
      </div>
    </div>
  </div>
  <div class="cell large-2" style="width: 300px;">
    <div class="card">
      <div class="card-divider">
        <h4>Repositorio</h4>
      </div>
      <img style="height: 350px; object-fit: cover;" src="/php-proyects/exercises/myschool/public/assets/img/github_cover.png">
      <div class="card-section">
        <p>Repositorio del proyecto para los archivos del proyecto.</p>
        <a href="https://github.com/mark-1701/my-school.git" target="_blank">Enlace</a>
      </div>
    </div>
  </div>
</div>
<script>
  // * Carga del documento
  document.addEventListener('DOMContentLoaded', e => {
    const $editform = document.querySelector('.edit-form');

    const $links = Array.from(document.querySelectorAll('.link'));
    const $profesoresLink = $links.find(link => link.textContent.trim() === 'Manuales');
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