<?php
include 'db_connect.php';
if (!($_SESSION['login_type'] == 1 || $_SESSION['login_type'] == 2)) {

	//header("Location: ./index.php?page=report_general_view"); // header doesn't work because of previous output

	// An alternative solution is to use javascript:

	echo '<script> location.replace("./index.php?page=report_general_view"); </script>';
}
?>
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
			<form id="langFilter" dir="rtl" action="./index.php?page=report_moodle_info" method="post">
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
	<!-- END OF Language Selection Card -->
	<div class="card card-outline card-success">
		<div class="card-header">
			<!-- <div class="card-tools">
				<a class="btn btn-block btn-sm btn-default btn-flat border-primary" href="./index.php?page=new_user"><i class="fa fa-plus"></i> Add New User</a>
			</div> -->
			<div class="card-body" style="overflow:auto">
			</div>
			<?php
			$site_info_url = "https://$Lang.moddaker.com/webservice/rest/server.php?wstoken=$token&wsfunction=core_webservice_get_site_info&moodlewsrestformat=json";
			$curl = curl_init($site_info_url);
			curl_setopt_array(
				$curl,
				[
					CURLOPT_FOLLOWLOCATION => true,
					CURLOPT_RETURNTRANSFER => true
				]
			);

			$result = curl_exec($curl);
			$site_info = json_decode($result, true);

			// print_r($site_info);
			?>
			<style>
				td {
					text-align: center !important;
				}
			</style>
			<table class="table tabe-hover table-bordered" id="list" style="margin: auto;">
				<tr>
					<th>المنصة</th>
					<td class="rounded"><b><?php echo $site_info['sitename'] ?></b></td>
				</tr>
				<tr>
					<th>رابط المنصة</th>
					<td class="rounded"><b><?php echo "<a href='$site_info[siteurl]'>$site_info[siteurl]</a>" ?></b></td>
				</tr>
				<tr>
					<th>إصدار الموودل</th>
					<td class="rounded"><b><?php echo $site_info['release'] ?></b></td>
				</tr>
				<tr>
					<th>نوع التقويم</th>
					<td class="rounded"><b><?php echo $site_info['sitecalendartype'] ?></b></td>
				</tr>
				<tr>
					<th>الثيم</th>
					<td class="rounded"><b><?php echo $site_info['theme'] ?></b></td>
				</tr>
			</table>
		</div>
	</div>
</div>