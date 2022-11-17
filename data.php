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
		$report_by_value=$_POST["report_by"];
        $total=0;
		$date="and (task_list.date_created BETWEEN '$from_date' AND '$to_date')";
		$group_by='';
		if($report_by_value==3){
			if($project_id!=0){
			$query_project_hours_total = "
			SELECT SUM(prod.time_rendered) AS Total 
			FROM user_productivity prod JOIN users on users.id = prod.user_id where project_id=$project_id  and (prod.date_created  BETWEEN '$from_date' AND '$to_date')
			";


			$result_project_hours = $conn->query($query_project_hours_total);
			foreach($result_project_hours as $row_count)
			{
				$total=$row_count["Total"];
			}

			$query_emp_hours_total = "
			SELECT concat(users.firstname, users.lastname) as name, SUM(prod.time_rendered) AS Total 
			FROM user_productivity prod JOIN users on users.id = prod.user_id where project_id=$project_id  and (prod.date_created  BETWEEN '$from_date' AND '$to_date') GROUP BY prod.user_id
			";

			$result_emp_hours = $conn->query($query_emp_hours_total);

			foreach ($result_emp_hours as $row) {
				$data[] = array(
					'report_by'		=>	$row["name"],
					'total'			=>	round(($row["Total"]/$total)*100,2),
					'color'			=>	'#' . rand(100000, 999999) . '',
					'project_id'		=>	$from_date,
				);
			}
		}else
		{
			$query_project_hours_total = "
			SELECT SUM(prod.time_rendered) AS Total 
			FROM user_productivity prod JOIN users on users.id = prod.user_id where    (prod.date_created  BETWEEN '$from_date' AND '$to_date')
			";


			$result_project_hours = $conn->query($query_project_hours_total);
			foreach($result_project_hours as $row_count)
			{
				$total=$row_count["Total"];
			}

			$query_emp_hours_total = "
			SELECT concat(users.firstname, users.lastname) as name, SUM(prod.time_rendered) AS Total 
			FROM user_productivity prod JOIN users on users.id = prod.user_id where    (prod.date_created  BETWEEN '$from_date' AND '$to_date') GROUP BY prod.user_id
			";

			$result_emp_hours = $conn->query($query_emp_hours_total);

			foreach ($result_emp_hours as $row) {
				$data[] = array(
					'report_by'		=>	$row["name"],
					'total'			=>	round(($row["Total"]/$total)*100,2),
					'color'			=>	'#' . rand(100000, 999999) . '',
					'project_id'		=>	$from_date,
				);
			}
		}
		
			echo json_encode($data);
			return;
		
		}

		if($report_by_value==1){
			$group_by="employee_id";
		}
		else{
			$group_by="status";
		}
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
			GROUP BY $group_by
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
			GROUP BY $group_by
			";}

		}
       
    
		$result = $conn->query($query);

		$data = array();

		foreach($result as $row)
		{
			if($report_by_value==1){
				$report_by=$row["name"];
			}
			else{
				$report_by=$row["st_name"];
			}

			$data[] = array(
				'report_by'		=>	$report_by,
				'total'			=>	round(($row["Total"]/$total)*100,2),
				'color'			=>	'#' . rand(100000, 999999) . '',
				'project_id'		=>	$from_date,
			);
		}

		echo json_encode($data);

		
	}
}


?>