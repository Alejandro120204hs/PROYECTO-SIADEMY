<div class="user-info">
  <div class="user-details">
    <span class="user-name"><?= $usuario['nombres'] ?></span>
    <span class="user-role"><?= $usuario['rol'] ?></span>
  </div>
</div>
<div class="avatar">
  <a href="<?= BASE_URL ?>/dashboard-perfil">
    <img src="<?= BASE_URL ?>/public/uploads/usuarios/<?= $usuario['foto'] ?>" 
         alt="foto" width="40px" height="40px" style="border-radius: 50%;">
  </a>
</div>