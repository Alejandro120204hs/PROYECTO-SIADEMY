<aside class="sidebar" id="leftSidebar">
    <a class="brand" href="#">
                       <img width="170px" src="<?= BASE_URL ?>/public/assets/extras/img/LOGO-NEGATIVO 1 (1).png" alt="">

    </a>
    <nav class="nav">
        <a class="active" href="<?= BASE_URL ?>/estudiante/dashboard">
            <i class="ri-home-5-line"></i> Panel
        </a>

        <a href="<?= BASE_URL ?>/estudiante-panel-materias">
            <i class="ri-book-2-line"></i> Mis Materias
            <span class="badge">6</span> <!-- COUNT de estudiante_materia -->
        </a>

        <a href="<?= BASE_URL ?>/estudiante-panel-calificaciones">
            <i class="ri-bar-chart-line"></i> Calificaciones
        </a>

        <div class="spacer"></div>
        <div class="section">Académico y seguimiento</div>

        <a href="<?= BASE_URL ?>/estudiante-panel-profesores">
            <i class="ri-user-3-line"></i> Mis Profesores
        </a>

        <a href="recursos.php">
            <i class="ri-folder-2-line"></i> Recursos
        </a>

        <a href="mensajes.php">
            <i class="ri-message-3-line"></i> Mensajes
            <span class="badge bg-danger">2</span> <!-- No leídos -->
        </a>

        <a href="anuncios.php">
            <i class="ri-megaphone-line"></i> Anuncios
        </a>
    </nav>
</aside>