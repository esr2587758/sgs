<?php

include('sgs.php');
$xx = new sgs($_POST['pic_path']);
unset($_POST['pic_path']);
$xx->params = $_POST;
$xx->render();
echo    $xx->art_path;exit;
?>

