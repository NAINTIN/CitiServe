<?php
session_start();
session_unset();
session_destroy();
header('Location: /CitiServe/public/login.php');
exit;