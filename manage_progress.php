<?php
session_start();
include 'db_connect.php';
if (isset($_GET['history'])) :
?>
	<div class="col-lg-12">
		<div class="card card-outline card-success">
			<div class="card-header">
			</div>
			<div class="card-body">
				<table class="table tabe-hover table-condensed" id="list">
					<colgroup>
						<col width="5%">
						<col width="15%">
						<col width="20%">
						<col width="15%">
						<col width="15%">
						<col width="10%">
						<col width="10%">
						<col width="10%">
					</colgroup>
					<thead>
						<tr class="text-center">
							<!-- <th class="text-center">#</th> -->
							<th>Employee </th>
							<th>Description</th>
							<th>Status</th>
							<th>Start Date</th>
							<th>End Date</th>
							<th>Modified Date</th>
						</tr>
					</thead>
					<tbody>
						<?php
						$i = 1;
						$where = "";
						if ($_SESSION['login_type'] == 2) {
							$where = " where p.manager_id = '{$_SESSION['login_id']}' ";
						} elseif ($_SESSION['login_type'] == 3) {
							$where = " where concat('[',REPLACE(p.user_ids,',','],['),']') LIKE '%[{$_SESSION['login_id']}]%' ";
						}

						$stat = array("Pending", "Started", "On-Progress", "On-Hold", "Over Due", "Done");
						$qry = $conn->query("SELECT * ,task_log.date_created as date, concat(users.firstname,' ' ,users.lastname) as name FROM task_log join users where task_log.old_employee_id =users.id and task_log.task_id = {$_GET['tid']} ");
						while ($row = $qry->fetch_assoc()) :

							$trans = get_html_translation_table(HTML_ENTITIES, ENT_QUOTES);
							unset($trans["\""], $trans["<"], $trans[">"], $trans["<h2"]);
							$desc = strtr(html_entity_decode($row['old_description']), $trans);
							$desc = str_replace(array("<li>", "</li>"), array("", ", "), $desc);
							$desc = strtr(html_entity_decode($row['old_description']), $trans);
							$desc = str_replace(array("<li>", "</li>"), array("", ", "), $desc);
							// $desc = htmlentities(str_replace("'","&#x2019;",$desc));
							if ($row['old_status'] == 0 && strtotime(date('Y-m-d')) >= strtotime($row['old_start_date'])) :
								if ($prod  > 0  || $cprog > 0)
									$row['old_status'] = 2;
								else
									$row['old_status'] = 1;
							elseif ($row['old_status'] == 0 && strtotime(date('Y-m-d')) > strtotime($row['old_end_date'])) :
								$row['old_status'] = 4;
							endif;


						?>
							<tr>
								<td class="text-center">
									<?php echo ucwords($row['name'])    ?>
								</td>
								<td class="text-center">
									<?php echo strip_tags($desc) ?>
								</td>
								<td class="text-center">
									<?php
									if ($stat[$row['old_status']] == 'Pending') {
										echo "<span class='badge badge-secondary'>{$stat[$row['old_status']]}</span>";
									} elseif ($stat[$row['old_status']] == 'Started') {
										echo "<span class='badge badge-primary'>{$stat[$row['old_status']]}</span>";
									} elseif ($stat[$row['old_status']] == 'On-Progress') {
										echo "<span class='badge badge-info'>{$stat[$row['old_status']]}</span>";
									} elseif ($stat[$row['old_status']] == 'On-Hold') {
										echo "<span class='badge badge-warning'>{$stat[$row['old_status']]}</span>";
									} elseif ($stat[$row['old_status']] == 'Over Due') {
										echo "<span class='badge badge-danger'>{$stat[$row['old_status']]}</span>";
									} elseif ($stat[$row['old_status']] == 'Done') {
										echo "<span class='badge badge-success'>{$stat[$row['old_status']]}</span>";
									}
									?>
								</td>
								<td class="text-center"><b><?php echo date("M d, Y", strtotime($row['old_start_date'])) ?></b></td>
								<td class="text-center"><b><?php echo date("M d, Y", strtotime($row['old_end_date'])) ?></b></td>
								<td class="text-center">
									<?php echo ucwords($row['date']) ?>
								</td>
							</tr>
						<?php endwhile; ?>
					</tbody>
				</table>
			</div>
		</div>
	</div>
	<style>
		table p {
			margin: unset !important;
		}

		table td {
			vertical-align: middle !important
		}
	</style>
<?php
	return;
endif;
if (isset($_GET['id'])) {
	$qry = $conn->query("SELECT * FROM user_productivity where id = " . $_GET['id'])->fetch_array();
	foreach ($qry as $k => $v) {
		$$k = $v;
	}
}
?>
<div class="container-fluid"></div>
<form action="" id="manage-progress">
	<input type="hidden" name="id" value="<?php echo isset($id) ? $id : '' ?>">
	<input type="hidden" name="project_id" value="<?php echo isset($_GET['pid']) ? $_GET['pid'] : '' ?>">
	<div class="col-lg-12">
		<div class="row">
			<div class="col-md-5">
				<?php if (!isset($_GET['tid'])) : ?>
					<div class="form-group">
						<label for="" class="control-label">Task</label>
						<select class="form-control form-control-sm select2" name="task_id">
							<option></option>
							<?php
							if ($_SESSION['login_type'] == 3)
								$tasks = $conn->query("SELECT * FROM task_list where project_id = {$_GET['pid']} and employee_id = {$_SESSION['login_id']}  order by task asc ");
							if ($_SESSION['login_type'] != 3)
								$tasks = $conn->query("SELECT * FROM task_list where project_id = {$_GET['pid']} order by task asc ");
							while ($row = $tasks->fetch_assoc()) :
							?>
								<option value="<?php echo $row['id'] ?>" <?php echo isset($task_id) && $task_id == $row['id'] ? "selected" : '' ?>><?php echo ucwords($row['task']) ?></option>
							<?php endwhile; ?>
						</select>
					</div>
				<?php else : ?>
					<input type="hidden" name="task_id" value="<?php echo isset($_GET['tid']) ? $_GET['tid'] : '' ?>">
				<?php endif; ?>
				<div class="form-group">
					<label for="">Subject</label>
					<input type="text" class="form-control form-control-sm" name="subject" value="<?php echo isset($subject) ? $subject : '' ?>" required>
				</div>
				<div class="form-group">
					<label for="">Date</label>
					<input type="date" id="start_date" class="form-control form-control-sm" name="date"  value="<?php echo isset($date) ? date("Y-m-d", strtotime($date)) : '' ?>" required>
				</div>
				<div class="form-group">
					<label for="">Start Time</label>
					<input type="time" class="form-control form-control-sm" name="start_time" id="start_time"  value="<?php echo isset($start_time) ? date("H:i", strtotime("2020-01-01 " . $start_time)) : '' ?>" required>
				</div>
				<div class="form-group">
					<label for="">End Time</label>
					<input type="time" class="form-control form-control-sm" name="end_time" id="end_time" min="09:00 AM" onchange="onTimeSelect()" value="<?php echo isset($end_time) ? date("H:i", strtotime("2020-01-01 " . $end_time)) : '' ?>" required>
				</div>
			</div>
			<div class="col-md-7">
				<div class="form-group">
					<label for="">Comment/Progress Description</label>
					<textarea name="comment" id="" cols="30" rows="10" id="" class="form-control summernote" required><?php echo isset($comment) ? $comment : '' ?></textarea>
				</div>
			</div>
		</div>
	</div>
</form>
</div>

<script>
	// function onDateSelect() {
	// 	var start_date = new Date($('#start_date').val());
	// 	start_date.min = start_date + Date();
	// }
	function onTimeSelect() {
		var start_time_val = $('#start_time').val();
		var end_time_val = $('#end_time').val();
		
		if (end_time_val  <= start_time_val ) {
			alert("End time must be after Start time.");

			document.getElementById("end_time").value=adder(start_time_val);

			//dateInput=start_time_val;
		}
	}
	/////////////
	function adder(tstring){
		hstring = tstring.split(':')[0];
		mstring = tstring.split(':')[1];

		h = Number(hstring);
		if (h == 23) {
			return "00:" + mstring;
		}
		new_hstring = zeroPad(h + 1, 2);
		
		return new_hstring + ':' + mstring;
		console.log(new_time);
	}

	function zeroPad(num, places) {
		var zero = places - num.toString().length + 1;
		return Array(+(zero > 0 && zero)).join("0") + num;
	}
	/////////////
	$(document).ready(function() {
		// $('.summernote').summernote({
		//       height: 200,
		//       toolbar: [
		//           [ 'style', [ 'style' ] ],
		//           [ 'font', [ 'bold', 'italic', 'underline', 'strikethrough', 'superscript', 'subscript', 'clear'] ],
		//           [ 'fontname', [ 'fontname' ] ],
		//           [ 'fontsize', [ 'fontsize' ] ],
		//           [ 'color', [ 'color' ] ],
		//           [ 'para', [ 'ol', 'ul', 'paragraph', 'height' ] ],
		//           [ 'table', [ 'table' ] ],
		//           [ 'view', [ 'undo', 'redo', 'fullscreen', 'codeview', 'help' ] ]
		//       ]
		//   })

		$('.select2').select2({
			placeholder: "Please select here",
			width: "100%"
		});
	})
	$('#manage-progress').submit(function(e) {
		e.preventDefault()
		start_load()
		$.ajax({
			url: 'ajax.php?action=save_progress',
			data: new FormData($(this)[0]),
			cache: false,
			contentType: false,
			processData: false,
			method: 'POST',
			type: 'POST',
			success: function(resp) {
				if (resp == 1) {
					alert_toast('Data successfully saved', "success");
					setTimeout(function() {
						location.reload()
					}, 1500)
				}
			}
		})
	})
</script>