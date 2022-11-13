<?php include 'db_connect.php' ?>
<div class="col-md-12">
  <div class="card card-outline card-success">
    <div class="card-header">
      <b>Project Progress</b>
      <div class="card-tools">
        <button class="btn btn-flat btn-sm bg-gradient-success btn-success" id="print"><i class="fa fa-print"></i> Print</button>
      </div>
    </div>
    <div class="card-body p-0">
      <div class="table-responsive" id="printable">
        <table class="table m-0 table-bordered">
          <!--  <colgroup>
                  <col width="5%">
                  <col width="30%">
                  <col width="35%">
                  <col width="15%">
                  <col width="15%">
                </colgroup> -->
          <thead>
            <th>#</th>
            <th>Project</th>
            <th>Task</th>
            <th>Completed Task</th>
            <th>Work Duration</th>
            <th>Progress</th>
            <th>Status</th>
          </thead>
          <tbody>
            <?php
            $i = 1;
            $stat = array("Pending", "Started", "On-Progress", "On-Hold", "Over Due", "Done");
            $where = "";
            if ($_SESSION['login_type'] == 2) {
              $where = " where manager_id = '{$_SESSION['login_id']}' ";
            } elseif ($_SESSION['login_type'] == 3) {
              $where = " where concat('[',REPLACE(user_ids,',','],['),']') LIKE '%[{$_SESSION['login_id']}]%' ";
            }
            $qry = $conn->query("SELECT * FROM project_list $where order by name asc");
            while ($row = $qry->fetch_assoc()) :
              $tprog = $conn->query("SELECT * FROM task_list where project_id = {$row['id']}")->num_rows;
              $cprog = $conn->query("SELECT * FROM task_list where project_id = {$row['id']} and status = 3")->num_rows;
              $prog = $tprog > 0 ? ($cprog / $tprog) * 100 : 0;
              $prog = $prog > 0 ?  number_format($prog, 2) : $prog;
              $prod = $conn->query("SELECT * FROM user_productivity where project_id = {$row['id']}")->num_rows;
              $dur = $conn->query("SELECT sum(time_rendered) as duration FROM user_productivity where project_id = {$row['id']}");
              $dur = $dur->num_rows > 0 ? $dur->fetch_assoc()['duration'] : 0;
              if ($row['status'] == 0 && strtotime(date('Y-m-d')) >= strtotime($row['start_date'])) :
                if ($prod  > 0  || $cprog > 0)
                  $row['status'] = 2;
                else
                  $row['status'] = 1;
              elseif ($row['status'] == 0 && strtotime(date('Y-m-d')) > strtotime($row['end_date'])) :
                $row['status'] = 4;
              endif;
            ?>
              <tr>
                <td>
                  <?php echo $i++ ?>
                </td>
                <td>
                  <a>
                    <?php echo ucwords($row['name']) ?>
                  </a>
                  <br>
                  <small>
                    Due: <?php echo date("Y-m-d", strtotime($row['end_date'])) ?>
                  </small>
                </td>
                <td class="text-center">
                  <?php echo number_format($tprog) ?>
                </td>
                <td class="text-center">
                  <?php echo number_format($cprog) ?>
                </td>
                <td class="text-center">
                  <?php echo number_format($dur) . ' Hr/s.' ?>
                </td>
                <td class="project_progress">
                  <div class="progress progress-sm">
                    <div class="progress-bar bg-green" role="progressbar" aria-valuenow="57" aria-valuemin="0" aria-valuemax="100" style="width: <?php echo $prog ?>%">
                    </div>
                  </div>
                  <small>
                    <?php echo $prog ?>% Complete
                  </small>
                </td>
                <td class="project-state">
                  <?php
                  if ($stat[$row['status']] == 'Pending') {
                    echo "<span class='badge badge-secondary'>{$stat[$row['status']]}</span>";
                  } elseif ($stat[$row['status']] == 'Started') {
                    echo "<span class='badge badge-primary'>{$stat[$row['status']]}</span>";
                  } elseif ($stat[$row['status']] == 'On-Progress') {
                    echo "<span class='badge badge-info'>{$stat[$row['status']]}</span>";
                  } elseif ($stat[$row['status']] == 'On-Hold') {
                    echo "<span class='badge badge-warning'>{$stat[$row['status']]}</span>";
                  } elseif ($stat[$row['status']] == 'Over Due') {
                    echo "<span class='badge badge-danger'>{$stat[$row['status']]}</span>";
                  } elseif ($stat[$row['status']] == 'Done') {
                    echo "<span class='badge badge-success'>{$stat[$row['status']]}</span>";
                  }
                  ?>
                </td>
              </tr>
            <?php endwhile; ?>
          </tbody>
        </table>
      </div>

    </div>

  </div>

  <div class="card-body p-0">

  <div class="container">
			<!-- <h2 class="text-center mt-4 mb-3">How to Create Dynamic Chart in PHP using Chart.js</a></h2> -->
                  
			<div class="card" >
				<div class="card-header">Sample Survey</div>
        <div style="display: flex; justify-content: center; gap:10px; margin: 5px;">
          <div style="display: flex; flex-direction: column;">

            <label for="start" style="display: block;  font: 1rem 'Fira Sans', sans-serif;margin: .4rem 0; ">From date</label>
            <input type="date" id="from_date" name="trip-start" value="2022-01-01"  min="2022-01-01"  style="width: fit-content; margin: .4rem 0;">
          </div>
          <div style="display: flex; flex-direction: column;">

            <label for="start" style="display: block;  font: 1rem 'Fira Sans', sans-serif;margin: .4rem 0; ">To date</label>
            <input type="date" id="to_date" name="trip-start" value="2022-12-01"  min="2022-01-01"  style="width: fit-content; margin: .4rem 0;">
          </div>
        </div>

        <div class="form-group" style="padding: 30px;">
        <label for="">Project</label>
        <select name="project_id" id="project_id" class="custom-select custom-select-sm" >
        <option value="0" selected>Select a Project</option>
          <?php
          $projects = $conn->query("SELECT *  FROM project_list ");
          while ($row = $projects->fetch_assoc()) :
          ?>
            <option value="<?php echo $row['id'] ?>" >
              <?php echo ucwords($row['name']) ?></option>
          <?php endwhile; ?>
        </select>
      </div>
      <div class="form-group" style="padding: 30px;">
        <label for="">Employees</label>
        <select name="employee_id" id="employee_id" class="custom-select custom-select-sm" >
        <option value="0" selected>Select an employee</option>
          <?php
          $employees = $conn->query("SELECT * , concat(users.firstname,' ',users.lastname) as name FROM users ");
          while ($row = $employees->fetch_assoc()) :
          ?>
            <option value="<?php echo $row['id'] ?>" >
              <?php echo ucwords($row['name']) ?></option>
          <?php endwhile; ?>
        </select>
      </div>
				<div class="card-body">
					<div class="form-group" style="padding: 11px;">
						<button type="button" name="submit_data" class="btn btn-primary" id="submit_data">Submit</button>
					</div>
				</div>
			</div>
		</div>
		<div class="container-fluid">
			<div class="row">
				<div class="col-md-4">
					<div class="card mt-4">
						<div class="card-header">Pie Chart</div>
						<div class="card-body">
							<div class="chart-container pie-chart">
								<canvas id="pie_chart"></canvas>
							</div>
						</div>
					</div>
				</div>
				<div class="col-md-4">
					<div class="card mt-4">
						<div class="card-header">Doughnut Chart</div>
						<div class="card-body">
							<div class="chart-container pie-chart">
								<canvas id="doughnut_chart"></canvas>
							</div>
						</div>
					</div>
				</div>
				<div class="col-md-4">
					<div class="card mt-4 mb-4">
						<div class="card-header">Bar Chart</div>
						<div class="card-body">
							<div class="chart-container pie-chart">
								<canvas id="bar_chart"></canvas>
							</div>
						</div>
					</div>
				</div>
			</div>
		</div>



  </div>
  
</div>
<script>
  $('#print').click(function() {
    start_load()
    var _h = $('head').clone()
    var _p = $('#printable').clone()
    var _d = "<p class='text-center'><b>Project Progress Report as of (<?php echo date("F d, Y") ?>)</b></p>"
    _p.prepend(_d)
    _p.prepend(_h)
    var nw = window.open("", "", "width=900,height=600")
    nw.document.write(_p.html())
    nw.document.close()
    nw.print()
    setTimeout(function() {
      nw.close()
      end_load()
    }, 750)
  })

  window.onload = function() {


    var chart = new CanvasJS.Chart("chartContainer", {
      theme: "light2",
      animationEnabled: true,
      title: {
        text: "World Energy Consumption by Sector - 2012"
      },
      data: [{
        type: "pie",
        indexLabel: "{y}",
        yValueFormatString: "#,##0.00\"%\"",
        indexLabelPlacement: "inside",
        indexLabelFontColor: "#36454F",
        indexLabelFontSize: 18,
        indexLabelFontWeight: "bolder",
        showInLegend: true,
        legendText: "{label}",
        dataPoints: <?php echo json_encode($dataPoints, JSON_NUMERIC_CHECK); ?>
      }]
    });
    chart.render();

  }
</script>
<script>
  $(document).ready(function() {
  

    $('#submit_data').click(function() {
      // const  rm_pie_chart=()=>{
      //  let rm_canva = document.getElementById("pie_chart")
      //  rm_canva.remove()
      //   let new_canva = document.createElement("canva")
      //   new_canva.id = ("pie_chart")
      //   new_canva.getContext("2d")
      //   $("pie-chart").append(new_canva)
      // }
      var language = $('#project_id option:selected').val();
      makechart();



    });

   
    
    function makechart() {
      // ظبطتتتتتتتتتتتتتتتتتتتتتتتتت
      $('#pie_chart').replaceWith($('<canvas id="pie_chart"></canvas>'));
      $('#doughnut_chart').replaceWith($('<canvas id="doughnut_chart"></canvas>'));
      $('#bar_chart').replaceWith($('<canvas id="bar_chart"></canvas>'));
      var project = $('#project_id option:selected').val();
      var employee = $('#employee_id option:selected').val();
      var from_date = new Date($('#from_date').val());
      var to_date = new Date($('#to_date').val());
      // console.log(to_date.toLocaleDateString() )
      // console.log(dateFormater(to_date, '-'));
      // console.log(""+from_date,"\n",""+to_date)
      // ظبطتتتتتتتتتتتتتتتتتتتتتتتتت
      $.ajax({
        url: "data.php",
        method: "POST",
        data: {
          action: 'fetch',
          project: project,
          employee: employee,
          from_date: from_date.toISOString().slice(0, 10),
          to_date: to_date.toISOString().slice(0, 10)
        },
        dataType: "JSON",
        success: function(data) {
          
          var language = [];
          var total = [];
          var color = [];

          for (var count = 0; count < data.length; count++) {
            language.push(data[count].language);
            total.push(data[count].total);
            color.push(data[count].color);
            console.log(data[count].project_id)
          }

          var chart_data = {
            labels: language,
            datasets: [{
              label: 'Vote',
              backgroundColor: color,
              color: '#fff',
              data: total
            }]
          };

          var options = {
            responsive: true,
            scales: {
              yAxes: [{
                ticks: {
                  min: 0
                }
              }]
            }
          };

          var group_chart1 = $('#pie_chart');

          var graph1 = new Chart(group_chart1, {
            type: "pie",
            data: chart_data
          });

          var group_chart2 = $('#doughnut_chart');

          var graph2 = new Chart(group_chart2, {
            type: "doughnut",
            data: chart_data
          });

          var group_chart3 = $('#bar_chart');

          var graph3 = new Chart(group_chart3, {
            type: 'bar',
            data: chart_data,
            options: options
          });
          
        }
      })
    }
    function dateFormater(date, separator) {
  var day = date.getDate();
  // add +1 to month because getMonth() returns month from 0 to 11
  var month = date.getMonth() + 1;
  var year = date.getFullYear();

  // show date and month in two digits
  // if month is less than 10, add a 0 before it
  if (day < 10) {
    day = '0' + day;
  }
  if (month < 10) {
    month = '0' + month;
  }

  // now we have day, month and year
  // use the separator to join them
  return day + separator + month + separator + year;
}

  });
</script>