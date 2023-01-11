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
			<div class="card-body" style="overflow:auto">
		</div>
			<?php
			$site_info_url = 'https://moddaker.com/birmingham/webservice/rest/server.php?wstoken=6205b87bf70f63264e85e23200a67b88&wsfunction=core_webservice_get_site_info&moodlewsrestformat=json';
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
			<table class="table tabe-hover table-bordered" id="list" style="margin: auto;">
				<tr>
					<td><b><?php echo $site_info['sitename'] ?></b></td>
					<th>المنصة</th>
				</tr>
				<tr>
					<td><b><?php echo $site_info['siteurl'] ?></b></td>
					<th>رابط المنصة</th>
				</tr>
				<tr>
					<td><b><?php echo $site_info['release'] ?></b></td>
					<th>إصدار الموودل</th>
				</tr>
				<tr>
					<td><b><?php echo $site_info['sitecalendartype'] ?></b></td>
					<th>نوع التقويم</th>
				</tr>
				<tr>
					<td><b><?php echo $site_info['theme'] ?></b></td>
					<th>الثيم</th>
				</tr>
			</table>
		</div>
	</div>
</div>
<script>
	$(document).ready(function() {
		$('#list').dataTable()
		$('.view_user').click(function() {
			uni_modal("<i class='fa fa-id-card'></i> User Details", "view_user.php?id=" + $(this).attr('data-id'))
		})
		$('.delete_user').click(function() {
			_conf("Are you sure to delete this user?", "delete_user", [$(this).attr('data-id')])
		})
	})

	function delete_user($id) {
		start_load()
		$.ajax({
			url: 'ajax.php?action=delete_user',
			method: 'POST',
			data: {
				id: $id
			},
			success: function(resp) {
				if (resp == 1) {
					alert_toast("Data successfully deleted", 'success')
					setTimeout(function() {
						location.reload()
					}, 1500)

				}
			}
		})
	}
</script>