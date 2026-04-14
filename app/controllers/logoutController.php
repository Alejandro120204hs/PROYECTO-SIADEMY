<?php

require_once BASE_PATH . '/app/helpers/session_helper.php';

destroySession();
header('Location: ' . BASE_URL . '/login', true, 302);
exit();

?>
