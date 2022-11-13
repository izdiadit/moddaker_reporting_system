<?php

//data.php
include('db_connect.php');
$connect = new PDO("mysql:host=localhost;dbname=testing", "root", "");

if(isset($_POST["action"]))
{
	

	if($_POST["action"] == 'fetch')
	{
		$project_id=$_POST["project"];
		$employee_id=$_POST["employee"];
		$from_date=$_POST["from_date"];
		$to_date=$_POST["to_date"];
        $total=0;
		$date="and (task_list.date_created BETWEEN '$from_date' AND '$to_date')";
		if($project_id!=0){
			if($employee_id!=0){
				$query_count = "
			SELECT COUNT(id) AS Total 
			FROM task_list where project_id=$project_id and employee_id=$employee_id $date
			";
			$result_count = $conn->query($query_count);
			foreach($result_count as $row_count)
			{
				$total=$row_count["Total"];
			}
	
		   
			$query = "
			SELECT status.name as st_name,concat(users.firstname,users.lastname) as name ,COUNT(task_list.id) AS Total 
			FROM task_list join users join status where status.id=task_list.status and users.id=task_list.employee_id and project_id=$project_id and employee_id=$employee_id $date
			GROUP BY status
			";
			}else {
			$query_count = "
			SELECT COUNT(id) AS Total 
			FROM task_list where project_id=$project_id $date
			";
			$result_count = $conn->query($query_count);
			foreach($result_count as $row_count)
			{
				$total=$row_count["Total"];
			}
	
		   
			$query = "
			SELECT status.name as st_name,concat(users.firstname,users.lastname) as name ,COUNT(task_list.id) AS Total 
			FROM task_list join users join status where status.id=task_list.status and  users.id=task_list.employee_id and project_id=$project_id $date
			GROUP BY status
			";}

		}
       
    
		$result = $conn->query($query);

		$data = array();

		foreach($result as $row)
		{
			$data[] = array(
				'language'		=>	$row["st_name"],
				'total'			=>	round(($row["Total"]/$total)*100,2),
				'color'			=>	'#' . rand(100000, 999999) . '',
				'project_id'		=>	$from_date,
			);
		}

		echo json_encode($data);

		
	}
}


?>