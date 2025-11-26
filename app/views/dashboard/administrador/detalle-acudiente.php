<?php 
  require_once BASE_PATH . '/app/helpers/session_administrador.php';
?>
<!doctype html>
<html lang="es">

<head>
  <meta charset="utf-8">
  <meta name="viewport" content="width=device-width, initial-scale=1">
  <title>SIADEMY • Detalle Acudiente</title>
  <?php 
    include_once __DIR__ . '/../../layouts/header_coordinador.php'
  ?>
  <link rel="stylesheet" href="<?= BASE_URL ?>/public/assets/dashboard/css/styles-admin.css">
</head>
<body>
  <div class="app" id="appGrid">
    <!-- LEFT SIDEBAR -->
    <?php 
      include_once __DIR__ . '/../../layouts/sidebar_coordinador.php'
    ?>

    <!-- MAIN -->
    <main class="main">
      <div class="topbar">
        <div class="topbar-left">
          <button class="toggle-btn" id="toggleLeft" title="Mostrar/Ocultar menú lateral">
            <i class="ri-menu-2-line"></i>
          </button>
          <button class="btn-back" onclick="window.history.back()">
            <i class="ri-arrow-left-line"></i> Volver
          </button>
          <div class="title">Detalle del Acudiente</div>
        </div>
        <button class="toggle-btn" id="toggleRight" title="Mostrar/Ocultar panel derecho">
          <i class="ri-layout-right-2-line"></i>
        </button>
      </div>

      <!-- GUARDIAN PROFILE HEADER -->
      <div class="guardian-profile-header">
        <div class="profile-main">
          <div class="profile-avatar" style="background: linear-gradient(135deg, #f093fb 0%, #f5576c 100%);">
            MW
          </div>
          <div class="profile-info">
            <h2>Maria William</h2>
            <p class="profile-subtitle">Acudiente • Madre</p>
            <div class="profile-badges">
              <span class="badge-item badge-active">
                <i class="ri-user-heart-line"></i> Acudiente Principal
              </span>
              <span class="badge-item badge-info">
                <i class="ri-group-line"></i> 2 Estudiantes a cargo
              </span>
            </div>
          </div>
        </div>
        <div class="profile-actions">
          <button class="btn-profile-action btn-primary-action">
            <i class="ri-edit-line"></i> <a href="coordinador/editar-acudiente">Editar</a>
          </button>
          <button class="btn-profile-action btn-secondary-action">
            <i class="ri-message-3-line"></i> Enviar Mensaje
          </button>
          <button class="btn-profile-action btn-secondary-action">
            <i class="ri-phone-line"></i> Llamar
          </button>
          <button class="btn-profile-action btn-icon-action">
            <i class="ri-more-2-fill"></i>
          </button>
        </div>
      </div>

      <!-- QUICK STATS -->
      <div class="quick-stats">
        <div class="stat-card">
          <div class="stat-icon" style="background: linear-gradient(135deg, #667eea 0%, #764ba2 100%);">
            <i class="ri-parent-line"></i>
          </div>
          <div class="stat-content">
            <span class="stat-label">Estudiantes a Cargo</span>
            <strong class="stat-value">2</strong>
          </div>
        </div>
        <div class="stat-card">
          <div class="stat-icon" style="background: linear-gradient(135deg, #f093fb 0%, #f5576c 100%);">
            <i class="ri-chat-check-line"></i>
          </div>
          <div class="stat-content">
            <span class="stat-label">Reuniones Asistidas</span>
            <strong class="stat-value">8 / 10</strong>
          </div>
        </div>
        <div class="stat-card">
          <div class="stat-icon" style="background: linear-gradient(135deg, #4facfe 0%, #00f2fe 100%);">
            <i class="ri-calendar-event-line"></i>
          </div>
          <div class="stat-content">
            <span class="stat-label">Última Reunión</span>
            <strong class="stat-value">15 Oct</strong>
          </div>
        </div>
        <div class="stat-card">
          <div class="stat-icon" style="background: linear-gradient(135deg, #43e97b 0%, #38f9d7 100%);">
            <i class="ri-star-line"></i>
          </div>
          <div class="stat-content">
            <span class="stat-label">Nivel de Participación</span>
            <strong class="stat-value grade-excellent">Excelente</strong>
          </div>
        </div>
      </div>

      <!-- TABS NAVIGATION -->
      <div class="tabs-navigation">
        <button class="tab-btn active" data-tab="informacion">
          <i class="ri-user-line"></i> Información Personal
        </button>
        <button class="tab-btn" data-tab="estudiantes">
          <i class="ri-team-line"></i> Estudiantes
        </button>
        <button class="tab-btn" data-tab="comunicacion">
          <i class="ri-message-3-line"></i> Comunicación
        </button>
        <button class="tab-btn" data-tab="reuniones">
          <i class="ri-calendar-line"></i> Reuniones
        </button>
      </div>

      <!-- TAB CONTENT -->
      <div class="tabs-content">
        <!-- INFORMACIÓN PERSONAL TAB -->
        <div class="tab-pane active" id="informacion">
          <div class="info-grid">
            <!-- Información Básica -->
            <div class="info-card">
              <div class="info-card-header">
                <h3><i class="ri-user-3-line"></i> Información Básica</h3>
              </div>
              <div class="info-card-body">
                <div class="info-row">
                  <span class="info-label">Nombres:</span>
                  <span class="info-value">Maria William</span>
                </div>
                <div class="info-row">
                  <span class="info-label">Apellidos:</span>
                  <span class="info-value">Maria William</span>
                </div>
                <div class="info-row">
                  <span class="info-label">N° Identificación:</span>
                  <span class="info-value">#789456123</span>
                </div>
                
                <div class="info-row">
                  <span class="info-label">Edad:</span>
                  <span class="info-value">39 años</span>
                </div>
             
                <div class="info-row">
                  <span class="info-label">Parentesco:</span>
                  <span class="info-value">Madre</span>
                </div>
              </div>
            </div>

            <!-- Información de Contacto -->
            <div class="info-card">
              <div class="info-card-header">
                <h3><i class="ri-contacts-line"></i> Información de Contacto</h3>
              </div>
              <div class="info-card-body">
                
                <div class="info-row">
                  <span class="info-label">Email:</span>
                  <span class="info-value">maria.william@email.com</span>
                </div>
                <div class="info-row">
                  <span class="info-label">Telefono:</span>
                  <span class="info-value">3106081490</span>
                </div>
                <div class="info-row">
                  <span class="info-label">Direccion:</span>
                  <span class="info-value">Calle3#5-72</span>
                </div>
                <div class="info-row">
                  <span class="info-label">Direccion:</span>
                  <span class="info-value">Calle3#5-72</span>
                </div>
              </div>
            </div>

            
          </div>
        </div>

        <!-- ESTUDIANTES TAB -->
        <div class="tab-pane" id="estudiantes">
          <div class="guardian-students-section">
            <h3 class="section-title">Estudiantes a Cargo</h3>
            
            <div class="students-grid-guardian">
              <!-- Estudiante 1 -->
              <div class="student-card-guardian">
                <div class="student-header-guardian">
                  <div class="student-avatar-guardian" style="background: linear-gradient(135deg, #667eea 0%, #764ba2 100%);">
                    SW
                  </div>
                  <div class="student-main-info">
                    <h4>Samanta William</h4>
                    <p class="student-grade">Grado VII A</p>
                    <span class="badge-status status-active">Activo</span>
                  </div>
                </div>

                <div class="student-performance-guardian">
                  <div class="performance-item-guardian">
                    <i class="ri-trophy-line"></i>
                    <div>
                      <span class="perf-label">Promedio General</span>
                      <strong class="grade-excellent">4.2</strong>
                    </div>
                  </div>
                  <div class="performance-item-guardian">
                    <i class="ri-calendar-check-line"></i>
                    <div>
                      <span class="perf-label">Asistencia</span>
                      <strong class="grade-good">95%</strong>
                    </div>
                  </div>
                  <div class="performance-item-guardian">
                    <i class="ri-shield-check-line"></i>
                    <div>
                      <span class="perf-label">Disciplina</span>
                      <strong class="grade-excellent">Excelente</strong>
                    </div>
                  </div>
                </div>

                <div class="student-alerts-guardian">
                  <div class="alert-item alert-success">
                    <i class="ri-checkbox-circle-line"></i>
                    <span>Sin novedades académicas</span>
                  </div>
                </div>

                <div class="student-actions-guardian">
                  <button class="btn-secondary">
                    <i class="ri-file-list-line"></i> Ver Boletín
                  </button>
                  <button class="btn-secondary">
                    <i class="ri-eye-line"></i> Ver Detalle
                  </button>
                </div>
              </div>

              <!-- Estudiante 2 -->
              <div class="student-card-guardian">
                <div class="student-header-guardian">
                  <div class="student-avatar-guardian" style="background: linear-gradient(135deg, #4facfe 0%, #00f2fe 100%);">
                    JW
                  </div>
                  <div class="student-main-info">
                    <h4>John William</h4>
                    <p class="student-grade">Grado IV B</p>
                    <span class="badge-status status-active">Activo</span>
                  </div>
                </div>

                <div class="student-performance-guardian">
                  <div class="performance-item-guardian">
                    <i class="ri-trophy-line"></i>
                    <div>
                      <span class="perf-label">Promedio General</span>
                      <strong class="grade-good">3.8</strong>
                    </div>
                  </div>
                  <div class="performance-item-guardian">
                    <i class="ri-calendar-check-line"></i>
                    <div>
                      <span class="perf-label">Asistencia</span>
                      <strong class="grade-good">92%</strong>
                    </div>
                  </div>
                  <div class="performance-item-guardian">
                    <i class="ri-shield-check-line"></i>
                    <div>
                      <span class="perf-label">Disciplina</span>
                      <strong class="grade-good">Bueno</strong>
                    </div>
                  </div>
                </div>

                <div class="student-alerts-guardian">
                  <div class="alert-item alert-warning">
                    <i class="ri-error-warning-line"></i>
                    <span>Próxima reunión programada</span>
                  </div>
                </div>

                <div class="student-actions-guardian">
                  <button class="btn-secondary">
                    <i class="ri-file-list-line"></i> Ver Boletín
                  </button>
                  <button class="btn-secondary">
                    <i class="ri-eye-line"></i> Ver Detalle
                  </button>
                </div>
              </div>
            </div>
          </div>
        </div>

        <!-- COMUNICACIÓN TAB -->
        <div class="tab-pane" id="comunicacion">
          <div class="communication-section">
            <h3 class="section-title">Historial de Comunicaciones</h3>
            
            <div class="communication-timeline">
              <div class="timeline-item">
                <div class="timeline-marker" style="background: #4f46e5;">
                  <i class="ri-message-3-line"></i>
                </div>
                <div class="timeline-content">
                  <div class="timeline-header">
                    <strong>Mensaje enviado por Prof. Carlos Méndez</strong>
                    <span class="timeline-date">20 Oct 2024 • 3:45 PM</span>
                  </div>
                  <p class="timeline-text">
                    Estimada Sra. Maria, le informo que Samanta ha tenido un excelente desempeño 
                    en el proyecto de ciencias. Felicitaciones por el apoyo brindado en casa.
                  </p>
                  <div class="timeline-actions">
                    <button class="btn-timeline">
                      <i class="ri-reply-line"></i> Responder
                    </button>
                  </div>
                </div>
              </div>

              <div class="timeline-item">
                <div class="timeline-marker" style="background: #10b981;">
                  <i class="ri-phone-line"></i>
                </div>
                <div class="timeline-content">
                  <div class="timeline-header">
                    <strong>Llamada telefónica recibida</strong>
                    <span class="timeline-date">15 Oct 2024 • 10:30 AM</span>
                  </div>
                  <p class="timeline-text">
                    Llamada entrante de Coordinación Académica. Duración: 15 minutos.
                    Tema: Reunión de padres de familia programada para el 25 de octubre.
                  </p>
                </div>
              </div>

              <div class="timeline-item">
                <div class="timeline-marker" style="background: #f59e0b;">
                  <i class="ri-mail-line"></i>
                </div>
                <div class="timeline-content">
                  <div class="timeline-header">
                    <strong>Email enviado por Secretaría Académica</strong>
                    <span class="timeline-date">10 Oct 2024 • 9:00 AM</span>
                  </div>
                  <p class="timeline-text">
                    Recordatorio de pago de pensión correspondiente al mes de octubre.
                    Fecha límite: 15 de octubre de 2024.
                  </p>
                  <div class="timeline-actions">
                    <button class="btn-timeline">
                      <i class="ri-download-line"></i> Descargar PDF
                    </button>
                  </div>
                </div>
              </div>

              <div class="timeline-item">
                <div class="timeline-marker" style="background: #6366f1;">
                  <i class="ri-notification-3-line"></i>
                </div>
                <div class="timeline-content">
                  <div class="timeline-header">
                    <strong>Notificación del sistema</strong>
                    <span class="timeline-date">05 Oct 2024 • 2:15 PM</span>
                  </div>
                  <p class="timeline-text">
                    Boletines del segundo período ya disponibles para descarga en el portal académico.
                  </p>
                </div>
              </div>
            </div>
          </div>
        </div>

        <!-- REUNIONES TAB -->
        <div class="tab-pane" id="reuniones">
          <div class="meetings-section">
            <h3 class="section-title">Registro de Reuniones</h3>
            
            <div class="meetings-stats">
              <div class="meeting-stat-card">
                <div class="meeting-stat-icon" style="background: #10b981;">
                  <i class="ri-check-double-line"></i>
                </div>
                <div class="meeting-stat-info">
                  <span class="meeting-stat-label">Asistidas</span>
                  <strong class="meeting-stat-value">8</strong>
                </div>
              </div>
              <div class="meeting-stat-card">
                <div class="meeting-stat-icon" style="background: #ef4444;">
                  <i class="ri-close-line"></i>
                </div>
                <div class="meeting-stat-info">
                  <span class="meeting-stat-label">No Asistidas</span>
                  <strong class="meeting-stat-value">2</strong>
                </div>
              </div>
              <div class="meeting-stat-card">
                <div class="meeting-stat-icon" style="background: #6366f1;">
                  <i class="ri-calendar-event-line"></i>
                </div>
                <div class="meeting-stat-info">
                  <span class="meeting-stat-label">Programadas</span>
                  <strong class="meeting-stat-value">1</strong>
                </div>
              </div>
              <div class="meeting-stat-card">
                <div class="meeting-stat-icon" style="background: #f59e0b;">
                  <i class="ri-percent-line"></i>
                </div>
                <div class="meeting-stat-info">
                  <span class="meeting-stat-label">Porcentaje</span>
                  <strong class="meeting-stat-value">80%</strong>
                </div>
              </div>
            </div>

            <div class="meetings-list">
              <h4 style="margin: 28px 0 16px 0; font-size: 18px; color: #fff;">Próximas Reuniones</h4>
              
              <div class="meeting-item upcoming">
                <div class="meeting-date-badge">
                  <span class="meeting-day">25</span>
                  <span class="meeting-month">OCT</span>
                </div>
                <div class="meeting-details">
                  <h5>Reunión General de Padres</h5>
                  <p>Presentación de resultados del segundo período académico</p>
                  <div class="meeting-meta">
                    <span><i class="ri-time-line"></i> 4:00 PM - 6:00 PM</span>
                    <span><i class="ri-map-pin-line"></i> Auditorio Principal</span>
                  </div>
                </div>
                <div class="meeting-actions">
                  <button class="btn-meeting btn-confirm">
                    <i class="ri-check-line"></i> Confirmar
                  </button>
                  <button class="btn-meeting btn-calendar">
                    <i class="ri-calendar-line"></i>
                  </button>
                </div>
              </div>

              <h4 style="margin: 28px 0 16px 0; font-size: 18px; color: #fff;">Reuniones Anteriores</h4>
              
              <div class="meeting-item past attended">
                <div class="meeting-date-badge past">
                  <span class="meeting-day">15</span>
                  <span class="meeting-month">SEP</span>
                </div>
                <div class="meeting-details">
                  <h5>Reunión de Coordinación - Grado VII</h5>
                  <p>Seguimiento académico y disciplinario del grupo</p>
                  <div class="meeting-meta">
                    <span><i class="ri-time-line"></i> 3:00 PM - 4:30 PM</span>
                    <span class="meeting-status attended"><i class="ri-check-line"></i> Asistió</span>
                  </div>
                </div>
              </div>

              <div class="meeting-item past attended">
                <div class="meeting-date-badge past">
                  <span class="meeting-day">20</span>
                  <span class="meeting-month">AGO</span>
                </div>
                <div class="meeting-details">
                  <h5>Entrega de Boletines - Período 1</h5>
                  <p>Revisión de calificaciones y recomendaciones académicas</p>
                  <div class="meeting-meta">
                    <span><i class="ri-time-line"></i> 5:00 PM - 7:00 PM</span>
                    <span class="meeting-status attended"><i class="ri-check-line"></i> Asistió</span>
                  </div>
                </div>
              </div>

              <div class="meeting-item past missed">
                <div class="meeting-date-badge past">
                  <span class="meeting-day">05</span>
                  <span class="meeting-month">JUL</span>
                </div>
                <div class="meeting-details">
                  <h5>Reunión de Inicio de Año Escolar</h5>
                  <p>Presentación del equipo docente y planificación anual</p>
                  <div class="meeting-meta">
                    <span><i class="ri-time-line"></i> 4:00 PM - 6:00 PM</span>
                    <span class="meeting-status missed"><i class="ri-close-line"></i> No Asistió</span>
                  </div>
                </div>
              </div>
            </div>
          </div>
        </div>
      </div>

    </main>

    <!-- RIGHT SIDEBAR -->
    <aside class="rightbar" id="rightSidebar">
      <div class="user">
        <button class="btn" title="Notificaciones"><i class="ri-notification-3-line"></i></button>
        <button class="btn" title="Configuración"><i class="ri-settings-3-line"></i></button>
        <div class="avatar" title="Diego A.">DA</div>
      </div>

      <div class="panel-title">Contacto Rápido</div>
      <div class="quick-actions">
        <button class="quick-action-btn">
          <i class="ri-phone-line"></i>
          <span>+62 821-9876-5432</span>
        </button>
        <button class="quick-action-btn">
          <i class="ri-mail-line"></i>
          <span>Enviar Email</span>
        </button>
        <button class="quick-action-btn">
          <i class="ri-message-3-line"></i>
          <span>Mensaje SMS</span>
        </button>
        <button class="quick-action-btn">
          <i class="ri-whatsapp-line"></i>
          <span>WhatsApp</span>
        </button>
      </div>

      <div class="panel-title" style="margin-top:24px">Acudientes Autorizados</div>
      <div class="authorized-list">
        <div class="authorized-item primary">
          <div class="authorized-avatar" style="background: linear-gradient(135deg, #f093fb 0%, #f5576c 100%);">
            MW
          </div>
          <div>
            <strong>Maria William</strong>
            <small>Madre • Principal</small>
          </div>
        </div>
        <div class="authorized-item">
          <div class="authorized-avatar" style="background: linear-gradient(135deg, #667eea 0%, #764ba2 100%);">
            DW
          </div>
          <div>
            <strong>David William</strong>
            <small>Padre • Secundario</small>
          </div>
        </div>
      </div>

      <div class="panel-title" style="margin-top:24px">Recordatorios</div>
      <div class="reminders-list">
        <div class="reminder-item important">
          <i class="ri-calendar-event-fill"></i>
          <div>
            <strong>Reunión programada</strong>
            <small>25 de octubre • 4:00 PM</small>
          </div>
        </div>
        <div class="reminder-item">
          <i class="ri-file-text-line"></i>
          <div>
            <strong>Firma de permisos</strong>
            <small>Pendiente</small>
          </div>
        </div>
      </div>
    </aside>
  </div>

  <!-- SCRIPT -->
  <script src="https://code.jquery.com/jquery-3.7.0.min.js"></script>
  <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/js/bootstrap.bundle.min.js"></script>
  <script src="<?= BASE_URL ?>/public/assets/dashboard/js/main-admin.js"></script>
</body>

</html>