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
			<!-- <div id="filter"></div> -->
		</div>
		<div class="card-body" style="overflow:auto">
			<table class="table tabe-hover table-bordered" id="list" style="display: none; left: 0">
				<thead>
					<tr>
						<th class="text-center">#</th>
						<th>اسم المستخدم</th>
						<th>الاسم الكامل</th>
						<th>البريد الإلكتروني</th>
						<th>وقت أول دخول</th>
						<th>وقت آخر دخول</th>
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

					// $service_url = 'https://en.moddaker.com/webservice/rest/server.php?moodlewsrestformat=json&wsfunction=local_reports_service_get_users&wstoken=5d67dc5eec6b25617c0e55c00c8a9fd6';
					// $curl = curl_init();
					// curl_setopt_array($curl, [
					// 	CURLOPT_URL => $service_url,
					// 	CURLOPT_FOLLOWLOCATION => true,
					// 	CURLOPT_RETURNTRANSFER => true,
					// 	CURLOPT_SSL_VERIFYPEER => 0,
					// 	CURLOPT_HTTPAUTH => CURLAUTH_BASIC,
					// ]);


					// $result = curl_exec($curl);
					$data = getData('fetcheddata/en-students.json');
					$moodle_users = $data['data'];
					echo $data['lastupdate'].'<br>';
					// print_r($moodle_users);
					// exit;
					$custom_fields_data = []; // An associative array with the structure: userid => ['shortname' => 'value']
					//	print_r( $moodle_users );


					require_once 'countries.php';


					$data_rendering_ended = false;
					for ($i; $i < count($moodle_users); $i++) :					
					// echo '<tr>';
					// 	echo ($i + 1).' | '; 
					// 	echo ucwords($moodle_users[$i]['username']).' | ';
					// 	echo $moodle_users[$i]['fullname'].' | '; 
					// 	echo $moodle_users[$i]['email'].' | '; 
					// 	echo $moodle_users[$i]['QuranMemorize'].' | '; 
					// 	echo date('Y-m-d h:i a', $moodle_users[$i]['firstaccess']).' | '; 
					// 	echo date('Y-m-d h:i a', $moodle_users[$i]['lastaccess']).' | ';
					// 	echo $moodle_users[$i]['confirmed'] ? 'مؤكّد' : 'غير مؤكّد'; echo ' | ';
					// 	echo $moodle_users[$i]['suspended'] ? 'معلّق' : 'غير معلّق'; echo ' | ';
					// 	echo $moodle_users[$i]['sex'].' | '; 
					// 	echo $moodle_users[$i]['Age'].' | '; 
					// 	echo $moodle_users[$i]['AcademicQualification'].' | '; 
					// 	echo $string[$moodle_users[$i]['country'] ?? '-'] ?? '-'; echo ' | ';
					// echo '</tr>';
					?>
					<tr>
							<th class="text-center"><?php echo $i + 1 ?></th>
							<td><b><?php echo ucwords($moodle_users[$i]['username']) ?></b></td>
							<td><b><?php echo $moodle_users[$i]['fullname'] ?></b></td>
							<td><b><?php echo $moodle_users[$i]['email'] ?></b></td>
							<td><b dir="ltr"><?php echo date('Y-m-d h:i a', $moodle_users[$i]['firstaccess']) ?></b></td>
							<td><b dir="ltr"><?php echo date('Y-m-d h:i a', $moodle_users[$i]['lastaccess']) ?></b></td>
							<td><b><?php echo $moodle_users[$i]['confirmed'] ? 'مؤكّد' : 'غير مؤكّد' ?></b></td>
							<td><b><?php echo $moodle_users[$i]['suspended'] ? 'معلّق' : 'غير معلّق' ?></b></td>
							<td><b><?php echo $moodle_users[$i]['lang'] ?></b></td>
							<td><b><?php echo $moodle_users[$i]['city'] ?? '-' ?></b></td>
							<td><b><?php echo $string[$moodle_users[$i]['country'] ?? '-'] ?? '-' ?></b></td>
							<td><b><?php echo $moodle_users[$i]['sex'] ?? '-' ?></b></td>
							<td><b><?php echo $moodle_users[$i]['Age'] ?? '-' ?></b></td>
							<td><b><?php echo $moodle_users[$i][''] ?? '-' ?></b></td>
							<td><b><?php echo $moodle_users[$i]['AcademicQualification'] ?? '-' ?></b></td>
							<td><b><?php echo $moodle_users[$i]['QuranMemorize'] ?? '-' ?></b></td>
						</tr>
						
					<?php
					endfor;
					$data_rendering_ended = true;
					?>
				</tbody>
			</table>
		</div>
	</div>
</div>
<script>
	$(document).ready(function() {

		setTimeout(() => {
		table = document.getElementById('list');
		table.style.display = 'table';
	}, 5000);

		// $('#list').dataTable()
		$('#list thead tr')
			.clone(true)
			.addClass('filters')
			.appendTo('#list thead');

		var table = $('#list').DataTable({
			orderCellsTop: true,
			fixedHeader: true,
			// dom: "<'row'<'col-sm-3 col-md-3'l>>" + "<'row'<'col-sm-12 col-md-12 col-lg-12't>>" + "<'row' <'col-sm-5 col-md-5'i><'col-sm-7 col-md-7'p>>",
			searching: false,
			language: {
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
			},
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
							$(cell).html('<input style="border-radius: 30px; text-align: center; margin: auto" type="date" placeholder="YYYY/DD/MM" />');
						} else {
							$(cell).html('<input style="border-radius: 30px; text-align: center; margin: auto" type="text" placeholder="تصفية ب' + title + '" />');
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
								// if (title == 'وقت أول دخول' || title == 'وقت آخر دخول') {
								// 	var date = new Date($(this).val());
								// 	// console.log(pad(date.getDate(), 2) + '-' + pad(date.getMonth() + 1, 2) + '-' + date.getFullYear())
								// }
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

	/** Padding function:
	 * n : the number to be padded.
	 * width: the length of the result.
	 * z: the char to pad with. defult: '0'*/ 
	function pad(n, width, z) {
		z = z || '0';
		n = n + '';
		return n.length >= width ? n : new Array(width - n.length + 1).join(z) + n;
	}

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
<style>
	.col-sm-12, .col-md-6{
		width: fit-content;
	}
</style>