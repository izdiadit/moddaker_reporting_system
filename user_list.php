<?php 
include 'db_connect.php';
if($_SESSION['login_type'] != 1) {
	echo '<script> location.replace("./index.php?page=report_general_view"); </script>';
}?>
<div class="col-lg-12">
	<div class="card card-outline card-success">
		<div class="card-header">
			<div class="card-tools">
				<a class="btn btn-block btn-sm btn-default btn-flat border-primary" href="./index.php?page=new_user"><i class="fa fa-user"></i> أضف مستخدماً</a>
			</div>
		</div>
		<div class="card-body">
			<table class="table tabe-hover table-bordered" id="list">
				<thead>
					<tr>
						<th class="text-center">#</th>
						<th>الاسم</th>
						<th>البريد الإلكتروني</th>
						<th>نوع المستخدم</th>
						<th>تنفيذ إجراء</th>
					</tr>
				</thead>
				<tbody>
					<?php
					$i = 1;
					$type = array('',"مدير نظام","مدير برنامج","مالك","مانح","مانح جزئي","طرف ثالث");
					$qry = $conn->query("SELECT *,concat(firstname,' ',lastname) as name FROM users order by type asc");
					while($row= $qry->fetch_assoc()):
					?>
					<tr>
						<th class="text-center"><?php echo $i++ ?></th>
						<td><b><?php echo ucwords($row['name']) ?></b></td>
						<td><b><?php echo $row['email'] ?></b></td>
						<td><b><?php echo $type[$row['type']] ?></b></td>
						<td class="text-center">
							<button type="button" class="btn btn-default btn-sm btn-flat border-info wave-effect text-info dropdown-toggle" data-toggle="dropdown" aria-expanded="true">
		                      تنفيذ
		                    </button>
		                    <div class="dropdown-menu">
		                      <a class="dropdown-item view_user" href="javascript:void(0)" data-id="<?php echo $row['id'] ?>">عرض</a>
		                      <div class="dropdown-divider"></div>
		                      <a class="dropdown-item" href="./index.php?page=edit_user&id=<?php echo $row['id'] ?>">تعديل</a>
		                      <div class="dropdown-divider"></div>
		                      <a class="dropdown-item delete_user" href="javascript:void(0)" data-id="<?php echo $row['id'] ?>">حذف</a>
		                    </div>
						</td>
					</tr>	
				<?php endwhile; ?>
				</tbody>
			</table>
		</div>
	</div>
</div>
<script>
	$(document).ready(function(){
		$('#list').dataTable({
			language: {
				"sProcessing": "جارٍ التحميل...",
				"sLengthMenu": "أظهر _MENU_ من الصفوف",
				"sZeroRecords": "لم يعثر على أية سجلات",
				"sInfo": "إظهار _START_ إلى _END_ من أصل _TOTAL_ من الصفوف",
				"sInfoEmpty": "يعرض 0 إلى 0 من أصل 0 سجل",
				"sInfoFiltered": "(منتقاة من مجموع _MAX_ مُدخلاً)",
				"sInfoPostFix": "",
				"sSearch": "ابحث:",
				"sUrl": "",
				"oPaginate": {
					"sFirst": "الأول",
					"sPrevious": "السابق",
					"sNext": "التالي",
					"sLast": "الأخير"
				}
			},
		})
	$('.view_user').click(function(){
		uni_modal("<i class='fa fa-id-card'></i> معلومات المستخدم","view_user.php?id="+$(this).attr('data-id'))
	})
	$('.delete_user').click(function(){
	_conf("أترغب حقًّا في حذف هذا المستخدم؟","delete_user",[$(this).attr('data-id')])
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
					alert_toast("نجح حذف البيانات",'success')
					setTimeout(function(){
						location.reload()
					},1500)

				}
			}
		})
	}
</script>