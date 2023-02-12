<?php
include 'db_connect.php';
if (!($_SESSION['login_type'] == 1 || $_SESSION['login_type'] == 2)) {

	//header("Location: ./index.php?page=report_general_view"); // header doesn't work because of previous output

	// An alternative solution is to use javascript:

	echo '<script> location.replace("./index.php?page=report_general_view"); </script>';
} ?>

<div class="col-lg-12  w-fit">
	<!-- Language Selection Card -->
	<div class="card card-outline card-success">
		<div class="card-header">

		</div>
		<div class="card-body" style="overflow:auto; text-align: right;" dir="rtl">
			<?php
			// The array of languages will be selected by the user, and elements will appear depending on the user type:
			include 'langs.php';

			// Check the selected langauage/s to get its data:
			$Lang = $_POST['lang'] ?? 'ar';
			$token = $tokens[$Lang];
			?>
			<!-- Language Selection Form -->
			<form id="langFilter" dir="rtl" action="./index.php?page=report_st_affairs" method="post">
				<!-- <input type="text" value="report_students" id="page" hidden> -->
				<label for="lang">اختر نسخة برنامج مدكر: </label>
				<select name="lang" id="lang" onchange="this.form.submit()">
					<?php foreach ($langs as $key => $lang) {
						if ($key == $_POST['lang'] || $key == $_GET['lang']) { // To assure that the submitted option will be selected after submitting form:
							echo "<option value='$key' selected='selected'>$lang</option>";
						} else {
							echo "<option value='$key'>$lang</option>";
						}
					} ?>
				</select>
			</form>
		</div>
	</div>
	<div class="card card-outline card-success" style="overflow: auto;">
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
						$url = "https://$Lang.moddaker.com/webservice/rest/server.php?wstoken=$token&moodlewsrestformat=json&wsfunction=local_reports_service_get_role_assignments&shortname=manager";
						$curl = curl_init($url);
						curl_setopt($curl, CURLOPT_RETURNTRANSFER, true);

						$result = curl_exec($curl);
						$st_aff_users = json_decode($result, true);

						foreach ($st_aff_users as $user) {
							if ($user['username'] == $_POST['username']) {
								echo "<option value='$user[username]' selected='selected'>$user[fullname]</option>";
							} else {
								echo "<option value='$user[username]'>$user[fullname]</option>";
							}
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

				 <!-- To assure the language is set even after submitting this form: -->
				<input type="hidden" name="lang" id="lang" value="<?php echo $Lang ?>">
				<br>
				<br>
			</form>
			<?php if (isset($_POST['username']) && isset($_POST['starttime']) && isset($_POST['endtime'])) :
				$username = $_POST['username'];
				$starttime = strtotime($_POST['starttime']);
				$endtime = strtotime($_POST['endtime']);
			?>
				<table class="table" id="list">
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

						$service_url = "https://$Lang.moddaker.com/webservice/rest/server.php?wstoken=$token&moodlewsrestformat=json&wsfunction=local_reports_service_get_logs_interval_by_username&starttime=" . $starttime . '&endtime=' . $endtime . '&username=' . $username;
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
									<td class="text-center"><?php echo ++$i ?></td>
									<td><?php echo $username ?></td>
									<td><?php echo $row['eventname'] ?></td>
									<td dir="ltr"><?php echo date('h:i:s', $row['timecreated']) . ' ' . date('a', $row['timecreated']) . ' ' .  date('Y-m-d', $row['timecreated']) ?></td>
									<td><?php echo $row['relateduserid'] ?></td>
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
	// Start date and end date validation:
	var starttime = document.getElementById('starttime');
	var endtime = document.getElementById('endtime');

	function validatedates(){
		if (starttime.value > endtime.value) {
			console.log(`${starttime.value} > ${endtime.value}`)
		}
	}

	$(document).ready(function() {
		validatedates();
		$('#list').dataTable({
			searching: false,
			"language": {
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
			}
		})
	})
</script>