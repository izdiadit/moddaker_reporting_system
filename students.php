<?php 
include 'db_connect.php';
if($_SESSION['login_type'] == 3) {
  echo'
  <div class="error-content">
  <h3><i class="fas fa-exclamation-triangle text-danger"></i> Denied! </h3>

  <p>
  You do not have permission to view this page.
    Meanwhile, you may <a href="./">return to dashboard</a>.
  </p>

</div>
  ';
  exit;
}?>
<div class="col-lg-12  w-fit">
	<div class="card card-outline card-success">
		<div class="card-header">
			<div class="card-tools">
				<a class="btn btn-block btn-sm btn-default btn-flat border-primary" href="./index.php?page=new_user"><i class="fa fa-plus"></i> Add New User</a>
			</div>
		</div>
		<div class="card-body">
			<table class="table tabe-hover table-bordered" id="list">
				<thead>
					<tr>
						<th class="text-center">#</th>
						<th>اسم المستخدم</th>
						<th>الاسم الكامل</th>
						<th>البريد الإلكتروني</th>
						<th>وقت أول دخول</th>
						<th>ةقت آخر دخول</th>
						<th>طريقة التسجيل</th>
						<th>حالة الحساب</th>
						<th>اللغة</th>
						<th>المدينة</th>
						<th>البلد</th>
						<th>النوع</th>
					</tr>
				</thead>
				<tbody>
					<?php
					$i = 0;
					//$type = array('',"Admin","Project Manager","Employee");
					//$qry = $conn->query("SELECT *,concat(firstname,' ',lastname) as name FROM users order by concat(firstname,' ',lastname) asc");

					$service_url = 'https://moddaker.com/birmingham/webservice/rest/server.php?wstoken=6205b87bf70f63264e85e23200a67b88&wsfunction=core_user_get_users&moodlewsrestformat=json&criteria[0][key]=lastname&criteria[0][value]=%';
					$curl = curl_init();
					curl_setopt_array($curl, [
					CURLOPT_URL => $service_url,
					CURLOPT_FOLLOWLOCATION => true,
					CURLOPT_RETURNTRANSFER => true]);
				
				
				$result = curl_exec($curl);
				$decoded = json_decode($result,true);

				$moodle_users = $decoded['users'];
				//	print_r( $moodle_users );

					for ($i; $i < count($moodle_users); $i++) :
					?>
					<tr>
						<th class="text-center"><?php echo $i+1 ?></th>
						<td><b><?php echo ucwords($moodle_users[$i]['username']) ?></b></td>
						<td><b><?php echo $moodle_users[$i]['fullname'] ?></b></td>
						<td><b><?php echo $moodle_users[$i]['email'] ?></b></td>
						<td><b><?php echo $moodle_users[$i]['firstaccess'] ?></b></td>
						<td><b><?php echo $moodle_users[$i]['lastaccess'] ?></b></td>
						<td><b><?php echo $moodle_users[$i]['auth'] ?></b></td>
						<td><b><?php echo $moodle_users[$i]['fullname'] ?></b></td>
						<td><b><?php echo $moodle_users[$i]['lang'] ?></b></td>
						<td><b><?php echo $moodle_users[$i]['city']?? 'غير مدخل' ?></b></td>
						<td><b><?php echo $moodle_users[$i]['country']?? 'غير مدخل' ?></b></td>
						<td><b><?php echo $moodle_users[$i]['customfields']['value']?? 'غير مدخل' ?></b></td>
						
						
						<!-- <td class="text-center">
							<button type="button" class="btn btn-default btn-sm btn-flat border-info wave-effect text-info dropdown-toggle" data-toggle="dropdown" aria-expanded="true">
		                      Action
		                    </button>
		                    <div class="dropdown-menu">
		                      <a class="dropdown-item view_user" href="javascript:void(0)" data-id="<?php //echo $row['id'] ?>">View</a>
		                      <div class="dropdown-divider"></div>
		                      <a class="dropdown-item" href="./index.php?page=edit_user&id=<?php // echo $row['id'] ?>">Edit</a>
		                      <div class="dropdown-divider"></div>
		                      <a class="dropdown-item delete_user" href="javascript:void(0)" data-id="<?php //echo $row['id'] ?>">Delete</a>
		                    </div>
						</td> -->
					</tr>	
				<?php endfor; ?>
				</tbody>
			</table>
		</div>
	</div>
</div>
<script>
	$(document).ready(function(){
		$('#list').dataTable()
	$('.view_user').click(function(){
		uni_modal("<i class='fa fa-id-card'></i> User Details","view_user.php?id="+$(this).attr('data-id'))
	})
	$('.delete_user').click(function(){
	_conf("Are you sure to delete this user?","delete_user",[$(this).attr('data-id')])
	})
	})
	function delete_user($id){
		start_load()
		$.ajax({
			url:'ajax.php?action=delete_user',
			method:'POST',
			data:{id:$id},
			success:function(resp){
				if(resp==1){
					alert_toast("Data successfully deleted",'success')
					setTimeout(function(){
						location.reload()
					},1500)

				}
			}
		})
	}
</script>