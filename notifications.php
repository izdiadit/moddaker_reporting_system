<?php 
session_start();
include 'db_connect.php';

if(isset($_GET['id'])){
	$type_arr = array('',"Admin","Project Manager","Employee");
	$qry = $conn->query("SELECT *,concat(firstname,' ',lastname) as name FROM users where id = ".$_GET['id'])->fetch_array();
foreach($qry as $k => $v){
	$$k = $v;
}
}
?>
<div class="container-fluid">
<?php 
    $count = 0;
    $qry = $conn->query("SELECT * FROM notifications WHERE recipient = {$_SESSION['login_id']}");
    while($row= $qry->fetch_assoc()):
			if ($row['type'] == 'insert') {	
				echo ('أضيفت مهمة جديدة للمشروع رقم ('.$row['project_id'].') وأُسندت لك'.'<br>');
			}
			
    endwhile;
    ?>
</div>
<div class="modal-footer display p-0 m-0">
        <button type="button" class="btn btn-secondary" data-dismiss="modal">Close</button>
</div>
<style>
	#uni_modal .modal-footer{
		display: none
	}
	#uni_modal .modal-footer.display{
		display: flex
	}
</style>