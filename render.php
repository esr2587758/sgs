<?php
include('sgs.php');
$xx = new sgs();
$xx->params = $_POST;
$xx->render();
?>
    <img src="<?php echo $xx->art_path;?>" />
