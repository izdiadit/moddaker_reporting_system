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
						<!-- <i class="fa fa-caret-down" aria-hidden="true"></i> -->
						<option value="admin">مدير النظام</option>
						<option value="guest">ضيف</option>
					</select>
				</div>
				<div>
					<label for="starttime">منذ وقت: </label>
					<input type="date" name="starttime" id="starttime" value="<?php echo $_POST['starttime']??date('Y-m-d',time() - 86400*3) // 86400 sec = 1 day?>" required>
				</div>
				<div>
					<label for="endtime">إلى وقت: </label>
					<input type="date" name="endtime" id="endtime" value="<?php echo $_POST['endtime']??date('Y-m-d',time())?>" required>
				</div>
				<input type="submit" value="اعرض التقرير">
				<br>
				<br>
			</form>
			<?php if (isset($_POST['username']) && isset($_POST['starttime']) && isset($_POST['endtime'])):
				$username = $_POST['username'];
				$starttime = strtotime($_POST['starttime']);
				$endtime = strtotime($_POST['endtime']);
			?>
				<table class="table tabe-hover table-bordered">
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
						//$type = array('',"Admin","Project Manager","Employee");
						//$qry = $conn->query("SELECT *,concat(firstname,' ',lastname) as name FROM users order by concat(firstname,' ',lastname) asc");

						$service_url = 'https://moddaker.com/birmingham/webservice/rest/server.php?wstoken=6205b87bf70f63264e85e23200a67b88&moodlewsrestformat=json&wsfunction=local_reports_service_get_logs_interval_by_username&starttime=' . $starttime . '&endtime=' . $endtime . '&username=' . $username;
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
									<td dir="rtl"><b><?php echo date('Y-m-d', $row['timecreated']) . ' ' . date('H:i:s', $row['timecreated']) . ' ' . date('a', $row['timecreated']) ?></b></td>
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

<style>
	#filters {
		margin: auto;
		display: flex;
		flex-direction: column;
		justify-content: center;
		align-items: center;
	}

	#filters input, select{
		border-radius: 25px;
		padding: auto;
	}
</style>