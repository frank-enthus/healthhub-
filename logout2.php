<?php
session_start();
session_destroy();
header("Location: doctor.php");
exit();
?>
