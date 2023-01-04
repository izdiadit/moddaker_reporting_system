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
    $qry = $conn->query("SELECT *, notifications.id as notif_id, notifications.task_id as tid FROM notifications JOIN project_list on notifications.project_id = project_list.id  WHERE FIND_IN_SET({$_SESSION['login_id']}, recipient)  and unread = true order by notifications.date_created desc");
    while($row= $qry->fetch_assoc()):
			
			if ($row['type'] == 'insert'){
				echo (" 
				<a href='#' id='{$row['notif_id']}p{$row['project_id']}t{$row['tid']}' class='pagerlink' > <p> New task was assigned to you in  {$row['name']}  project
				</p> </a> ");
			}
			elseif($row['type'] == 'delete'){
				echo (" 
				  	<a href='#' id='{$row['notif_id']}p{$row['project_id']}t{$row['tid']}' class='pagerlink' > <p>	Some task which is assigned to you in		{$row['name']}   project 
				has been deleted. </p> </a>");
				
			}
			elseif($row['type'] == 'update'){
				echo ("  <a href='#' id='{$row['notif_id']}p{$row['project_id']}t{$row['tid']}' class='pagerlink' > <p> Some task which is assigned to you in {$row['name']} project has been updated. </p></a> ");
			}
			elseif($row['type'] == 'reassign'){
				echo (" <a href='#' id='{$row['notif_id']}p{$row['project_id']}t{$row['tid']}' class='pagerlink' > <p> Some task in {$row['name']} project has been reassigned from you to another member. </p> </a>");
			}
			elseif($row['type'] == 'reassign2'){
				echo ("  <a href='#' id='{$row['notif_id']}p{$row['project_id']}t{$row['tid']}' class='pagerlink' > <p> Some task in {$row['name']} project has been reassigned to you.</p></a>  ");
			}
		/*	elseif($row['type'] == 'insert_project'){
				echo ("  <a href='#' id='{$row['notif_id']}p{$row['project_id']}t{$row['tid']}' class='pagerlink' > <p> you  were add in {$row['name']} project .</p></a>  ");
			}*/
			
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
	#uni_modal{
  /*  display: block !important;  I added this to see the modal, you don't need this */
}

/* Important part */
.modal-dialog{
    overflow-y: initial !important
}
.modal-body{
    height: 80vh;
    overflow-y: auto;
}
</style>
<script>
	
	$('a.pagerlink').click(function(e) {
		//$("#viewer_modal").modal({backdrop: true});
		e.preventDefault()
		start_load()
    var notif_id=$(this).attr('id').substring(0,$(this).attr('id').indexOf('p'));
		var id=$(this).attr('id').substring($(this).attr('id').indexOf('p')+1,$(this).attr('id').indexOf('t'));
	  var tid=$(this).attr('id').substring($(this).attr('id').indexOf('t')+1,($(this).attr('id')).length);
		console.log("notif_id"+notif_id+"\tid"+id+"\ttid"+tid);
		$.ajax({
			url: 'ajax.php?action=notification',
			data: {	notif_id : notif_id },
			method: 'POST',
			type: 'POST',
			success: function(resp) {
				//console.log("notif_id"+resp);
				if (resp == 1) {
					//alert_toast('Data successfully saved', "success");
					setTimeout(function() {
						location.reload()
					}, 1500)
					//window.location = 'index.php?page=view_project&p=&tid&notif_id=';
					if(tid<=0){
						window.location.href = "index.php?page=project_list&notif_id="+notif_id+"&id="+id+"&tid="+tid;
					}
					else
					window.location.href = "index.php?page=view_project&notif_id="+notif_id+"&id="+id+"&tid="+tid;
				}
			}
		})
	})
</script>