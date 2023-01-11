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
			<div id="filter"></div>
		</div>
		<div class="card-body" style="overflow:auto">
			<table class="table tabe-hover table-bordered" id="list" style="margin: auto;">
				<thead>
					<tr>
						<th class="text-center">#</th>
						<th>اسم المستخدم</th>
						<th>الاسم الكامل</th>
						<th>البريد الإلكتروني</th>
						<th>وقت أول دخول</th>
						<th>وقت آخر دخول</th>
						<th>طريقة التسجيل</th>
						<th>تأكيد الحساب</th>
						<th>تعليق الحساب</th>
						<th>اللغة</th>
						<th>المدينة</th>
						<th>البلد</th>
						<th>النوع</th>
						<th>العمر</th>
						<th>اللغة الأمّ</th>
						<th>المؤهل الأكاديمي</th>
						<th>كم جزءًا يحفظ؟</th>
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
						CURLOPT_RETURNTRANSFER => true
					]);


					$result = curl_exec($curl);
					$decoded = json_decode($result, true);

					$moodle_users = $decoded['users'];
					$custom_fields_data = []; // An associative array with the structure: userid => ['shortname' => 'value']
					//	print_r( $moodle_users );


					require_once 'countries.php';

					for ($i; $i < count($moodle_users); $i++) :
						$custom_fields_data[$moodle_users[$i]['id']] = [];
						foreach ($moodle_users[$i]['customfields'] as $cfield) {
							$custom_fields_data[$moodle_users[$i]['id']][$cfield['shortname']] = $cfield['value'];
						}
					?>
						<tr>
							<th class="text-center"><?php echo $i + 1 ?></th>
							<td><b><?php echo ucwords($moodle_users[$i]['username']) ?></b></td>
							<td><b><?php echo $moodle_users[$i]['fullname'] ?></b></td>
							<td><b><?php echo $moodle_users[$i]['email'] ?></b></td>
							<td><b><?php echo date('Y/m/d h:i a', $moodle_users[$i]['firstaccess']) ?></b></td>
							<td><b><?php echo date('Y-m-d h:i a', $moodle_users[$i]['lastaccess']) ?></b></td>
							<td><b><?php echo $moodle_users[$i]['auth'] ?></b></td>
							<td><b><?php echo $moodle_users[$i]['confirmed'] ? 'مؤكّد' : 'غير مؤكّد' ?></b></td>
							<td><b><?php echo $moodle_users[$i]['suspended'] ? 'معلّق' : 'غير معلّق' ?></b></td>
							<td><b><?php echo $moodle_users[$i]['lang'] ?></b></td>
							<td><b><?php echo $moodle_users[$i]['city'] ?? '-' ?></b></td>
							<td><b><?php echo $string[$moodle_users[$i]['country'] ?? '-'] ?></b></td>
							<td><b><?php echo $custom_fields_data[$moodle_users[$i]['id']]['sex'] ?? '-' ?></b></td>
							<td><b><?php echo $custom_fields_data[$moodle_users[$i]['id']]['FullNameForCertificate'] ?? '-' ?></b></td>
							<td><b><?php echo $custom_fields_data[$moodle_users[$i]['id']][''] ?? '-' ?></b></td>
							<td><b><?php echo $custom_fields_data[$moodle_users[$i]['id']][''] ?? '-' ?></b></td>
							<td><b><?php echo $custom_fields_data[$moodle_users[$i]['id']][''] ?? '-' ?></b></td>


							<!-- <td class="text-center">
							<button type="button" class="btn btn-default btn-sm btn-flat border-info wave-effect text-info dropdown-toggle" data-toggle="dropdown" aria-expanded="true">
		                      Action
		                    </button>
		                    <div class="dropdown-menu">
		                      <a class="dropdown-item view_user" href="javascript:void(0)" data-id="<?php //echo $row['id'] 
																									?>">View</a>
		                      <div class="dropdown-divider"></div>
		                      <a class="dropdown-item" href="./index.php?page=edit_user&id=<?php // echo $row['id'] 
																							?>">Edit</a>
		                      <div class="dropdown-divider"></div>
		                      <a class="dropdown-item delete_user" href="javascript:void(0)" data-id="<?php //echo $row['id'] 
																										?>">Delete</a>
		                    </div>
						</td> -->
						</tr>
					<?php endfor; ?>
				</tbody>
			</table>
			<!-- <?php //print_r( $custom_fields_data);
					?> -->
		</div>
	</div>
</div>
<script>
	$(document).ready(function() {
		//$('#list').dataTable()
		$('#list thead tr')
			.clone(true)
			.addClass('filters')
			.appendTo('#list thead');

		var table = $('#list').DataTable({
			orderCellsTop: true,
			fixedHeader: true,
			initComplete: function() {
				var api = this.api();

				// For each column
				api
					.columns()
					.eq(0)
					.each(function(colIdx) {
						// Set the header cell to contain the input element
						var cell = $('.filters th').eq(
							$(api.column(colIdx).header()).index()
						);
						var title = $(cell).text();
						if (title == '#') {
							$(cell).html('تصفية');
						} else if (title == 'وقت أول دخول' || title == 'وقت آخر دخول') {
							$(cell).html('<input style="border-radius: 30px; text-align: center; margin: auto" type="date" placeholder="YYYY/MM/DD" />');
						}else{
							$(cell).html('<input style="border-radius: 30px; text-align: center; margin: auto" type="text" placeholder="' + title + '" />');
						}

						// On every keypress in this input
						$(
								'input',
								$('.filters th').eq($(api.column(colIdx).header()).index())
							)
							.off('keyup change')
							.on('change', function(e) {
								// Get the search value
								$(this).attr('title', $(this).val());
								var regexr = '({search})'; //$(this).parents('th').find('select').val();

								var cursorPosition = this.selectionStart;
								// Search the column for that value
								api
									.column(colIdx)
									.search(
										this.value != '' ?
										regexr.replace('{search}', '(((' + this.value + ')))') :
										'',
										this.value != '',
										this.value == ''
									)
									.draw();
							})
							.on('keyup', function(e) {
								e.stopPropagation();

								$(this).trigger('change');
								$(this)
									.focus()[0]
									.setSelectionRange(cursorPosition, cursorPosition);
							});
					});
			},
		});

		////////////////////////////////////////////////////////////////////////////////////////////////////
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