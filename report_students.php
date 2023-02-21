<?php
include 'db_connect.php';
if (!($_SESSION['login_type'] == 1 || $_SESSION['login_type'] == 2)) {

	//header("Location: ./index.php?page=report_general_view"); // header doesn't work because of previous output

	// An alternative solution is to use javascript:

	echo '<script> location.replace("./index.php?page=report_general_view"); </script>';
} ?>
<!-- ///////////////////////////////// The Preloader Script ///////////////////////////////////////////////// -->
<script>
	// Hide the content of the page:
	wrapper = document.getElementsByClassName('content-wrapper')[0];
	wrapper.style.display = "none";
	// Start the loader:
	window.onloadstart = start_load();
</script>
<!-- //////////////////////////////////////////////////////////////////////////////////////////////////////// -->
<div class="col-lg-12  w-fit" dir="rtl">
	<div class="card card-outline card-success">
		<div class="card-header">

		</div>
		<div class="card-body" style="overflow:auto; text-align: right;" dir="rtl">
			<?php
			// The array of languages will be sliced depending on the user:
			include 'langs.php';

			// Check the selected langauage/s to get its data:
			$Lang = $_POST['lang'] ?? array_keys($langs)[0];

			$data = getData("fetcheddata/$Lang-students.json");
			echo count($data['data']);
			echo '<br>';
			$moodle_users = $data['data'];
			echo date('Y/m/d h:i:s', $data['lastupdate']);
			// print_r($moodle_users);
			// exit;
			?>
			<form id="langFilter" dir="rtl" action="./index.php?page=report_students" method="post">
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
	<div class="card card-outline card-success" id="tableCard">
		<div class="card-header">
			<ul class="data-slicing-btns">
				<?php
				if (count($moodle_users) > 25000) {
					// prepare slices:
					$length = count($moodle_users);
					for ($i = 0; $i < $length; $i += 25000) {
						$sliceStr = $i;
						$sliceEnd = $i + 25000;

						if ($sliceEnd > $length) {
							$sliceEnd = $length;
						}
						echo "<li><a href='http://localhost/moddaker_reporting_system/index.php?page=report_students&lang=$Lang&sliceStr=$sliceStr&sliceEnd=$sliceEnd'>$sliceStr - $sliceEnd</a></li>";
					}
					// echo "<li><a href='http://localhost/moddaker_reporting_system/index.php?page=report_students&lang=$Lang&sliceStr=2000&sliceEnd=50000'>25000-50000</a></li>";
					// echo "<li><a href='http://localhost/moddaker_reporting_system/index.php?page=report_students&lang=$Lang&sliceStr=50000&sliceEnd=75000'>50000-75000</a></li>";
				}
				?>
			</ul>
		</div>
		<div class="card-body" style="overflow:auto">
			<table class="table tabe-hover table-bordered" id="list" style="display: table;">
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
					require_once 'countries.php';
					$data_rendering_ended = false;
					// Determine the currently displayed portion of the array if it is too long:
					$sliceStr = $_GET['sliceStr'] ?? 0;
					$sliceEnd = $_GET['sliceEnd'] ?? 25000;
					echo (count($moodle_users) > 25500) ? "<br><p dir='rtl' style='margin: auto'>عرض النتائج من $sliceStr إلى $sliceEnd </p><br>" : "";
					$current_displayed = array_slice($moodle_users, $sliceStr, $sliceEnd);


					for ($i = 0; $i < count($current_displayed); $i++) :
					?>
						<tr>
							<td class="text-center"><?php echo $i + 1 ?></td>
							<td><b><?php echo ucwords($current_displayed[$i]['username']) ?></b></td>
							<td><b><?php echo $current_displayed[$i]['fullname'] ?></b></td>
							<td><b><?php echo $current_displayed[$i]['email'] ?></b></td>
							<td><b dir="ltr"><?php echo date('Y-m-d h:i a', $current_displayed[$i]['firstaccess']) ?></b></td>
							<td><b dir="ltr"><?php echo date('Y-m-d h:i a', $current_displayed[$i]['lastaccess']) ?></b></td>
							<td><b><?php echo $current_displayed[$i]['confirmed'] ? 'مؤكّد' : 'غير مؤكّد' ?></b></td>
							<td><b><?php echo $current_displayed[$i]['suspended'] ? 'معلّق' : 'غير معلّق' ?></b></td>
							<td><b><?php echo $current_displayed[$i]['lang'] ?></b></td>
							<td><b><?php echo $current_displayed[$i]['city'] ?? '-' ?></b></td>
							<td><b><?php echo $string[$current_displayed[$i]['country'] ?? '-'] ?? '-' ?></b></td>
							<td><b><?php echo $current_displayed[$i]['sex'] ?? '-' ?></b></td>
							<td><b><?php echo $current_displayed[$i]['Age'] ?? '-' ?></b></td>
							<td><b><?php echo $current_displayed[$i][''] ?? '-' ?></b></td>
							<td><b><?php echo $current_displayed[$i]['AcademicQualification'] ?? '-' ?></b></td>
							<td><b><?php echo $current_displayed[$i]['QuranMemorize'] ?? '-' ?></b></td>
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
		start_load();
		setTimeout(() => {
			wrapper = document.getElementsByClassName('content-wrapper')[0];
			wrapper.style.display = 'block';
			end_load();
		}, 3500);

		var lang = <?php echo json_encode($Lang) ?>; // don't forget to replace with Lang 
		console.log(lang)
		// $('#list').dataTable()
		$('#list thead tr')
			.clone(true)
			.addClass('filters')
			.appendTo('#list thead');

		var table = $('#list').DataTable({
			orderCellsTop: true,
			fixedHeader: true,
			// dom: "<'row'<'col-sm-3 col-md-3'l>>" + "<'row'<'col-sm-12 col-md-12 col-lg-12't>>" + "<'row' <'col-sm-5 col-md-5'i><'col-sm-7 col-md-7'p>>",
			searching: true,
			// serverSide: (lang == 'ar')? true : false,
			// ajax: (lang != 'ar')? null: {
			// 	url: 'http://localhost/moddaker_reporting_system/fetcheddata/ar-students.json',
			// 	dataSrc: function (json) {
			// 		// data = JSON.parse(json);
			// 		mydata = Object.values(json.data);
			// 		mydata.slice(0,5).forEach(el => {

			// 			console.log(`Length: ${el.username}`)
			// 		});
			// 		return Object.values(json.data);
			// 	},
			// 	// dataSrc: 'data',
			// },
			// columns: [
			// 	{data: 'username', defaultContent: "-"},
			// 	{data: 'username', defaultContent: "-"},
			// 	{data: 'fullname', defaultContent: "-"},
			// 	{data: 'email', defaultContent: "-"},
			// 	{data: 'firstaccess', defaultContent: "-"},
			// 	{data: 'lastaccess', defaultContent: "-"},
			// 	{data: 'confirmed', defaultContent: "-"},
			// 	{data: 'suspended', defaultContent: "-"},
			// 	{data: 'lang', defaultContent: "-"},
			// 	{data: 'city', defaultContent: "-"},
			// 	{data: 'country', defaultContent: "-"},
			// 	{data: 'sex', defaultContent: "-"},
			// 	{data: 'Age', defaultContent: "-"},
			// 	{data: 'Age', defaultContent: "-"},
			// 	{data: 'AcademicQualification', defaultContent: "-"},
			// 	{data: 'QuranMemorize', defaultContent: "-"}
			// ],
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
			initComplete: function() { //(lang == 'ar')? null : function() {
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
					alert_toast("حُذفت البيانات بنجاح", 'success')
					setTimeout(function() {
						location.reload()
					}, 1500)

				}
			}
		})
	}
</script>
<style>
	.col-sm-12,
	.col-md-6 {
		width: fit-content;
	}
</style>