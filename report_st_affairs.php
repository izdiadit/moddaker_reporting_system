<?php
include 'db_connect.php';
if ($_SESSION['login_type'] == 3) {
	echo '
  <div class="error-content">
  <h3><i class="fas fa-exclamation-triangle text-danger"></i> Denied! </h3>

  <p>
  You do not have permission to view this page.
    Meanwhile, you may <a href="./">return to dashboard</a>.
  </p>

</div>
  ';
	exit;
} ?>

<div class="col-lg-12  w-fit">
	<div class="card card-outline card-success">
		<div class="card-header">
			<!-- <div class="card-tools">
				<a class="btn btn-block btn-sm btn-default btn-flat border-primary" href="./index.php?page=new_user"><i class="fa fa-plus"></i> Add New User</a>
			</div> -->
		</div>
		<div class="card-body" style="overflow:auto; margin: auto;" dir="rtl">
			<form id="filters" dir="rtl" action="./index.php?page=report_st_affairs" method="POST">
				<div>
					<label for="username">
						<p>اختر اسم المستخدم الذي ترغب في عرض تقرير عن نشاطه: </p>
					</label>
					<select class="form-select" name="username" id="username">
						<!-- Filling the select options after getting the data of role users from the api -->
						<?php 
						$url = 'https://ar.moddaker.com/webservice/rest/server.php?wstoken=ef620eccaf5a9f249e24ee3b6cc30ebf&moodlewsrestformat=json&wsfunction=local_reports_service_get_role_assignments&shortname=manager';
						$curl = curl_init($url);
						curl_setopt($curl, CURLOPT_RETURNTRANSFER, true);

						$result = curl_exec($curl);
						$st_aff_users = json_decode($result, true);

						foreach ($st_aff_users as $user) {
							echo "<option value='$user[username]'>$user[fullname]</option>";
						}
						?>
					</select>
				</div>
				<div>
					<label for="starttime">منذ وقت: </label>
					<input type="date" name="starttime" id="starttime" value="<?php echo $_POST['starttime'] ?? date('Y-m-d', time() - 86400 * 3) // 86400 sec = 1 day
																				?>" required>
				</div>
				<br>
				<div>
					<label for="endtime">إلى وقت: </label>
					<input type="date" name="endtime" id="endtime" value="<?php echo $_POST['endtime'] ?? date('Y-m-d', time()) ?>" required>
				</div>
				<br>
				<input type="submit" value="اعرض التقرير">
				<br>
				<br>
			</form>
			<?php if (isset($_POST['username']) && isset($_POST['starttime']) && isset($_POST['endtime'])) :
				$username = $_POST['username'];
				$starttime = strtotime($_POST['starttime']);
				$endtime = strtotime($_POST['endtime']);
			?>
				<table class="table table-striped dt-responsive" id="list">
					<thead>
						<tr>
							<th class="text-center">#</th>
							<th>اسم المستخدم المنفذ</th>
							<th>النشاط</th>
							<th>الوقت والتاريخ</th>
							<th>رقم المستخدم المتأثّر بالنشاط</th>
						</tr>
					</thead>
					<tbody>
						<?php
						$i = 0;
						// Calling the api to get the logs data:

						$service_url = 'https://ar.moddaker.com/webservice/rest/server.php?wstoken=ef620eccaf5a9f249e24ee3b6cc30ebf&moodlewsrestformat=json&wsfunction=local_reports_service_get_logs_interval_by_username&starttime=' . $starttime . '&endtime=' . $endtime . '&username=' . $username;
						$curl = curl_init();
						curl_setopt_array($curl, [
							CURLOPT_URL => $service_url,
							CURLOPT_FOLLOWLOCATION => true,
							CURLOPT_RETURNTRANSFER => true
						]);


						$result = curl_exec($curl);
						$log_data = json_decode($result, true);
						if (count($log_data) == 0) {
						?>
							<td colspan="5"><b style="margin: auto; text-justify: center;">لم يقم هذا المستخدم بأي نشاط في الفترة المحددة</b></td>
							<?php
						} else {
							foreach ($log_data as $row) :
							?>
								<tr>
									<th class="text-center"><?php echo ++$i ?></th>
									<td><b><?php echo $username ?></b></td>
									<td><b><?php echo $row['eventname'] ?></b></td>
									<td dir="ltr"><b><?php echo date('h:i:s', $row['timecreated']) . ' ' . date('a', $row['timecreated']) . ' ' .  date('Y-m-d', $row['timecreated'])?></b></td>
									<td><b><?php echo $row['relateduserid'] ?></b></td>
								</tr>
						<?php endforeach;
						} ?>
					</tbody>
				</table>
			<?php
			else :
			?>
				<div style="margin: auto; font-size: larger; text-justify: center;" dir="rtl">
					<i class="fa fa-info-circle fa-6" aria-hidden="true"></i>
					اختر اسم مستخدم لعرض تقرير عن نشاطه في الفترة المحددة
				</div>

			<?php endif; ?>

		</div>
	</div>
</div>
<script>
	$(document).ready(function() {
		$('#list').dataTable({
			searching: false,
			"language": {
				"sProcessing": "جارٍ التحميل...",
				"sLengthMenu": "أظهر _MENU_ من الصفوف",
				"sZeroRecords": "لم يعثر على أية سجلات",
				"sInfo": "إظهار _START_ إلى _END_ من أصل _TOTAL_ صفًّا",
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
			}
		})
	})
</script>
<style>
	#filters {
		margin: auto;
		display: flex;
		flex-direction: column;
		justify-content: center;
		align-items: center;
	}

	#filters input,
	select {
		border-radius: 25px;
		padding: 5px;
	}

	/*************************************** */
	/* @import url(https://fonts.googleapis.com/css?family=Tajawal); */

	.card {
		/* direction: rtl; */
		/* background: #DFE7E5 !important; */
		font-family: 'traditional arabic' !important;
		font-size: 25px;
	}

	h3 {
		color: #32c19a;
	}


	div.dataTables_wrapper div.dataTables_info {
		padding-top: 0px !important;
		white-space: nowrap;
		color: #64b99c !important;
	}

	table.dataTable>tbody>tr.child ul.dtr-details {
		display: inline-block;
		list-style-type: none;
		margin: 0;
		padding: 0;
		text-align: right;
	}

	.table.dataTable.dtr-inline.collapsed>tbody>tr[role="row"]>td:first-child:before,
	table.dataTable.dtr-inline.collapsed>tbody>tr[role="row"]>th:first-child:before {
		top: 9px;
		left: 4px;
		height: 14px;
		width: 14px;
		display: block;
		position: absolute;
		color: white;
		border: 2px solid white;
		border-radius: 14px;
		box-shadow: 0 0 3px #444;
		box-sizing: content-box;
		text-align: center;
		text-indent: 0 !important;
		font-family: 'Courier New', Courier, monospace;
		line-height: 14px;
		content: '+';
		background-color: #60b6ab;
	}

	.table.dataTable.dtr-inline.collapsed>tbody>tr.parent>td:first-child:before,
	table.dataTable.dtr-inline.collapsed>tbody>tr.parent>th:first-child:before {
		content: '-';
		background-color: #d33333;
	}

	.page-item.active .page-link {
		z-index: 1;
		color: #fff !important;
		background-color: #42c2a1 !important;
		border-color: #42c2a1 !important;
		border-radius: 8px !important;
	}

	.page-link {
		position: relative;
		display: block;
		padding: .5rem .75rem;
		margin-left: -1px;
		line-height: 1.25;
		color: #42c2a1 !important;
		background-color: #fff;
		border: 1px solid #dee2e6;
	}

	.dataTables_info,
	.dataTables_length {
		float: right;
	}

	#list_paginate,
	.dataTables_filter {
		float: left;
	}
</style>