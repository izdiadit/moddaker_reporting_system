<?php 
session_start();
include 'db_connect.php';
if(isset($_GET['id'])){
	$qry = $conn->query("SELECT * FROM task_list where id = ".$_GET['id'])->fetch_array();
	foreach($qry as $k => $v){
		$$k = $v;
	}
}
?>
<div class="container-fluid d-flex flex-column"
style="display: fl;"
>
	<form action="" id="manage-task">
		<input type="hidden" name="id" value="<?php echo isset($id) ? $id : '' ?>">
		<input type="hidden" name="project_id" value="<?php echo isset($_GET['pid']) ? $_GET['pid'] : '' ?>">
		<div class="form-group">
			<label for="">Task</label>
			<input type="text" class="form-control form-control-sm" name="task" value="<?php echo isset($task) ? $task : '' ?>" required <?php echo $_SESSION['login_type'] == 3 ? 'readonly' : '' ?>>
		</div>
		<?php if($_SESSION['login_type'] != 3): ?>
		<div class="form-group">
			<label for="">Description</label>
			<textarea name="description" cols="30" rows="10" class="form-control summernote" required><?php echo isset($description) ? $description : '' ?></textarea>
		</div>	
		<?php endif; ?>
		<?php if($_SESSION['login_type'] == 3): ?>
		<div class="form-group">
			<label for="">Description</label>
			<textarea  name="description" cols="30" class="form-control summernote" disabled wrap=physical required><?php echo isset($description) ? $description : '' ?></textarea>
		</div>
		<?php endif; ?>
		<?php if($_SESSION['login_type'] != 3): ?>
		<div class="form-group">
						<label for="">Start Date</label>
						<!-- <input type="time" class="form-control form-control-sm" name="start_time" value="<?php echo isset($start_date) ? date("H:i",strtotime("2020-01-01 ".$start_date)) : '' ?>" required <?php echo $_SESSION['login_type'] == 3 ? 'readonly' : '' ?>> -->
						<input type="datetime-local" id="start_date"    class="form-control form-control-sm"  name="start_date" value="<?php echo isset($start_date) ? $start_date : '' ?>" >
					</div>
					<div class="form-group">
						<label for="">End Date</label>
						<!-- <input type="time" class="form-control form-control-sm" name="end_time" value="<?php echo isset($end_date) ?  : '' ?>" required <?php echo $_SESSION['login_type'] == 3 ? 'readonly' : '' ?>> -->
						<input type="datetime-local" class="form-control form-control-sm" id="end_date"      name="end_date" value="<?php echo isset($end_date) ? $end_date : '' ?>"  >
					</div>
					<?php endif; ?>
		<div class="form-group"	>
			<label for="">Status</label>
			<select name="status" id="status" class="custom-select custom-select-sm">
				<option value="1" <?php echo isset($status) && $status == 1 ? 'selected' : '' ?>>Pending</option>
				<option value="2" <?php echo isset($status) && $status == 2 ? 'selected' : '' ?>>On-Progress</option>
				<?php 
				if($_SESSION['login_type'] != 3) : 
				?>
				<option value="3" <?php echo isset($status) && $status == 3 ? 'selected' : '' ?>>Done</option>
					<?php endif; ?>
				<option value="4" <?php echo isset($status) && $status == 4 ? 'selected' : '' ?>>under-confirmation</option>
			</select>
			<!-- <div class="form-group">
			<label for="">Employee</label>
			<select name="employee_id" id="employee_id" class="custom-select custom-select-sm">
              	<?php 
              	$employees = $conn->query("SELECT *,concat(firstname,' ',lastname) as name FROM users where type = 3 order by concat(firstname,' ',lastname) asc ");
              	while($row= $employees->fetch_assoc()):
              	?>
              	<option value="<?php echo $row['id'] ?>" <?php  echo isset($_SESSION['login_id']) && $_SESSION['login_id']  == $row['id'] ? 'selected' : '' ?>><?php echo ucwords($row['name']) ?></option>
              	<?php endwhile; ?>
			</select>
		</div> -->
		<?php if($_SESSION['login_type'] != 3): ?>
		<div class="form-group">
      <label for="">Employee</label>
      <select name="employee_id" id="employee_id" class="custom-select custom-select-sm">
                <?php 
        $pid=$_GET["pid"];
				if(!isset($_GET['id'])){
					$employees = $conn->query("SELECT distinct users.*,concat(users.firstname,' ',users.lastname) as name FROM users join project_list  where type = 3  and  FIND_IN_SET(users.id, project_list.user_ids) <> 0 and project_list.id=$pid order by concat(firstname,' ',lastname) asc ");
				}else
                	$employees = $conn->query("SELECT distinct users.*,concat(users.firstname,' ',users.lastname) as name, task_list.employee_id FROM users join project_list join task_list where type = 3 and task_list.id=$id and project_list.id=task_list.project_id and FIND_IN_SET(users.id, project_list.user_ids) <> 0 order by concat(firstname,' ',lastname) asc ");
                while($row= $employees->fetch_assoc()):
					$employee_id='';
					if(isset($_GET['id'])){
						$employee_id=$row['employee_id'];
					}
					if($_SESSION['login_id']==1){?>
						<option value="<?php echo $row['id'] ?>" <?php   echo $row['id']  == $employee_id  ? 'selected' : '' ?>><?php echo ucwords($row['name']) ?></option>
					<?php  }else{?>
                ?>
				<option value="<?php echo $row['id'] ?>" <?php  echo isset($_SESSION['login_id']) && $row['id']  == $_SESSION['login_id'] ? 'selected' : '' ?>><?php echo ucwords($row['name']) ?></option>
                <?php } endwhile; ?>
      </select>
    </div>
	<?php endif; ?>
		</div>
	</form>
</div>

<script>
	// $(document).ready(function(){
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
  //   }).on('summernote.change', function(we, contents, $editable) {
  //   $(this).html(contents);
	// 	});
  //    })
	$(document).ready(function(){
		$('.summernote').summernote()
	})
    $('#manage-task').submit(function(e){
    	e.preventDefault()
    	start_load()
    	$.ajax({
    		url:'ajax.php?action=save_task',
			data: new FormData($(this)[0]),
		    cache: false,
		    contentType: false,
		    processData: false,
		    method: 'POST',
		    type: 'POST',
			success:function(resp){
				console.log(resp);
				if(resp == 1){
					alert_toast('Data successfully saved',"success");
					setTimeout(function(){
						location.reload()
					},1500)
				}
			}
    	})
    })
</script>