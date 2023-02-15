<?php
include 'db_connect.php';
if ($_SESSION['login_type'] != 1) {
  echo '<script> location.replace("./index.php?page=report_general_view"); </script>';
}
$qry = $conn->query("SELECT * FROM users where id = ".$_GET['id'])->fetch_array();
foreach($qry as $k => $v){
	$$k = $v;
}


// Set the languages of the user: ////////////////////////////////////////////////
// Donot put the include before the above if block. I don't know why!
include 'langs.php';

$user_langs = (empty($languages))? array_keys($langs) : explode(',',$languages);

print_r($user_langs);
//////////////////////////////////////////////////////////////////////////////////

include 'new_user.php';
?>