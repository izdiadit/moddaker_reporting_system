<?php
include 'db_connect.php';
if ($_SESSION['login_type'] != 1) {
  echo '<script> location.replace("./index.php?page=report_general_view"); </script>';
}
$qry = $conn->query("SELECT * FROM users where id = ".$_GET['id'])->fetch_array();
foreach($qry as $k => $v){
	$$k = $v;
}


include 'new_user.php';
?>