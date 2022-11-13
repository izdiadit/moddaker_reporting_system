<?php 
session_start();
include 'db_connect.php';
if(isset($_GET['id'])){
	$qry = $conn->query("SELECT * FROM task_list  where  id = ".$_GET['id'])->fetch_array();
	foreach($qry as $k => $v){
		$$k = $v;
	}
}
?>
<div class="container-fluid">
	<dl>
		<dt><b class="border-bottom border-primary">Task</b></dt>
		<dd><?php echo ucwords($task) ?></dd>
	</dl>
	<dl>
		<dt><b class="border-bottom border-primary">Status</b></dt>
		<dd>
			<?php 
        	if($status == 1){
		  		echo "<span class='badge badge-secondary'>Pending</span>";
        	}elseif($status == 2){
		  		echo "<span class='badge badge-primary'>On-Progress</span>";
        	}elseif($status == 3){
		  		echo "<span class='badge badge-success'>Done</span>";
        	}elseif($status == 4){
				echo "<span class='badge badge-warning'>under-confirmation</span>";
		  }
        	?>
		</dd>
	</dl>
	<dl>
   
		<dt><b class="border-bottom border-primary">Date</b></dt>
		<dd>start date : <?php echo $start_date ?></dd>
		<dd>end date : <?php echo $end_date ?></dd>
	</dl>
	<dl>
   
		<dt><b class="border-bottom border-primary">Description</b></dt>
		<dd><?php echo html_entity_decode($description) ?></dd>
	</dl>
	<dl> 
	<dt>Comments</dt>
	<?php  
	$comment=$conn->query("SELECT comments.*,concat(users.firstname,' ',users.lastname) as name,avatar FROM task_list join comments join users where comments.employee_id=users.id and task_list.id = comments.task_id and task_list.id = ".$_GET['id']);
	?>
<?php
	// while($row= $comment->fetch_assoc()):?>
		<!-- <p><?php echo $row["name"]." At ".$row["date_created"]; ?></p> -->
		<!-- <p style="font-weight: bold;"><?php echo $row["comment"]; ?></p> -->
		<!-- <div class="dropdown-divider"></div> -->
		<?php //endwhile; ?>
 </dl>
	<!-- <form action="" id="manage-task_comment">
		<dl>
	<input type="hidden" name="task_id" value="<?php echo isset($id) ? $id : '' ?>">
		<input type="hidden" name="employee_id" value="<?php echo isset($_SESSION['login_id']) ? $_SESSION['login_id'] : '' ?>">
	<dt><b class="border-bottom border-primary">Add Comment</b></dt>
	<dd><textarea name="comment" id="comment" cols="50" rows="5" >
			</textarea>
	</dd>
		
		</dl></form> -->
<section style="background-color: aliceblue;">
  <div class="container my-5 py-5">
    <div class="row d-flex justify-start">
      <div class="col-md-12 col-lg-10">
        <div class="card text-dark">
			<?php
			while($row= $comment->fetch_assoc()):?>
          	<div class="card-body p-4 mb-2">
           	 <div class="d-flex flex-start">
				<img class="rounded-circle shadow-1-strong me-3 mr-2" src="assets/uploads/<?php echo $row['avatar'] ?>" alt="User Image" width="60"
                height="60" />
              <!-- <img class="rounded-circle shadow-1-strong me-3 mr-2"
                src="https://thumbs.dreamstime.com/b/rainbow-love-heart-background-red-wood-60045149.jpg" alt="avatar" width="60"
                height="60" /> -->
             	 <div>
                <h6 class="fw-bold mb-1 text-primary text-bold"><?php echo $row["name"] ?></h6>
                <div class="d-flex align-items-center mb-3">
                  <p class="mb-0 font-bold text-info">
				  At
				  <span class="text-sm text-secondary">
						 <?php echo $row["date_created"]; ?>
					</span>
                  </p>
                </div>
                <p class="mb-0">
				<?php echo $row["comment"]; ?>
                </p>
              </div>
            </div>
          </div>
          <hr class="my-0" />
		  <?php endwhile; ?>
        </div>
		<form action="" id="manage-task_comment">
		<div class="form-outline mb-4">
		<input type="hidden" name="task_id" value="<?php echo isset($id) ? $id : '' ?>">
		<input type="hidden" name="employee_id" value="<?php echo isset($_SESSION['login_id']) ? $_SESSION['login_id'] : '' ?>">
		<textarea class="form-control shadow-lg" name="comment" id="comment" cols="50" rows="5" placeholder="Type a Comment" >
			</textarea>
	  </div>
	  <!-- <button type="button" class="btn btn-primary btn-sm">Post comment</button> -->
	  </form>
      </div>
    </div>
  </div>
</section>
<script>
$('#manage-task_comment').submit(function(e){
    	e.preventDefault()
    	start_load()
    	$.ajax({
    		url:'ajax.php?action=save_comment',
			data: new FormData($(this)[0]),
		    cache: false,
		    contentType: false,
		    processData: false,
		    method: 'POST',
		    type: 'POST',
			success:function(resp){
				console.log("hennna"+resp);
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