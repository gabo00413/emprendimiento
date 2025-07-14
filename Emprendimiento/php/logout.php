<?php
session_start();
session_destroy();
header("Location: ../sections/galeria.php");
exit;
