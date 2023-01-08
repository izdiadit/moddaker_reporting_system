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
}
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
//	print_r( $moodle_users );

$countries = [];

include 'countries.php';
for ($i = 0; $i < count($moodle_users); $i++) {
  $moodle_users[$i]['country'] = $moodle_users[$i]['country'] ?? '-';
  if (array_key_exists($string[$moodle_users[$i]['country']], $countries)) {
    $countries[$string[$moodle_users[$i]['country']]] += 1;
  } else {
    $countries[$string[$moodle_users[$i]['country']]] = 1;
  }
}

?>
<div class="col-md-12">
  <div class="card card-outline card-success" dir="rtl">
    <div class="card-header">
      <b>شاشة التقارير</b>
    </div>
    <div class="chartsPanel">
      <div class="chartCard" id="chartdiv" style="width: 50%;"></div>
      <div class="chartCard" id="chartdiv2" style="width: 50%;"></div>

    </div>

    <div class="chartsPanel">
      <div class="chartCard" id="chartCard3" style="width: 50%;">
        <div id="chartdiv3"></div>
      </div>
      <div class="chartCard" id="chartdiv4" style="width: 50%;"></div>

    </div>

    <script type="text/javascript" src="/Moddaker_Reporting_System/amcharts5/index.js"></script>
    <script type="text/javascript" src="/Moddaker_Reporting_System/amcharts5/percent.js"></script>
    <script type="text/javascript" src="/Moddaker_Reporting_System/amcharts5/xy.js"></script>
    <script type="text/javascript" src="/Moddaker_Reporting_System/amcharts5/themes/Animated.js"></script>

    <script>
      // Reading country_data from php:
      var result = <?php echo json_encode($countries); ?>;
      // // var country_data = JSON.parse(result);
      var country_data = [];
      for (const key in result) {
        if (result.hasOwnProperty.call(result, key)) {
          country_data.push({
            country: key,
            count: result[key]
          });
        }
      }
      console.log(result)
      console.log(country_data)

    </script>

    <script src="report_charts.js"></script>
  </div>
</div>

<!-- Academic Status Report -->
<div class="col-md-12">
  <div class="card card-outline card-success" dir="rtl" style="padding: 5px;">
    <div class="card-header">
      <b>الحالة الأكاديمية</b>
    </div>
    <?php
    // Get all courses:
    $courses_url = 'https://moddaker.com/birmingham/webservice/rest/server.php?wstoken=6205b87bf70f63264e85e23200a67b88&wsfunction=core_course_get_courses&moodlewsrestformat=json';
    $curl = curl_init();
    curl_setopt_array($curl, [
      CURLOPT_URL => $courses_url,
      CURLOPT_FOLLOWLOCATION => true,
      CURLOPT_RETURNTRANSFER => true
    ]);

    $decoded_courses = json_decode(curl_exec($curl), true);

    // echo '<div dir="ltr">';
    // print_r($decoded_courses);
    // echo '</div>';

    // Prepare the ids parameter that will be passed in the url:
    $cat_ids = '';    
    
    foreach ($decoded_courses as $course) {
      $cat_ids = $cat_ids . ',' . $course['categoryid'];
    }
    $cat_ids = ltrim($cat_ids, ',');
    // echo $cat_ids . '<br>';

    // Get all categories:
    $categories_url = 'https://moddaker.com/birmingham/webservice/rest/server.php?wstoken=6205b87bf70f63264e85e23200a67b88&wsfunction=core_course_get_categories&moodlewsrestformat=json&criteria[0][key]=ids&criteria[0][value]=' . $cat_ids;
    $curl = curl_init();
    curl_setopt_array($curl, [
      CURLOPT_URL => $categories_url,
      CURLOPT_FOLLOWLOCATION => true,
      CURLOPT_RETURNTRANSFER => true
    ]);

    $decoded_categories = json_decode(curl_exec($curl), true);

    // echo '<div dir="ltr">';
    // print_r($decoded_categories);
    // echo '</div>';

    // 1. Get the no. of students enrolled in every category by getting the no. of students enrolled in the 1st course (المستوى التمهيدي)
    // 2. Get the no. of graduates in every category by getting the no. of students ended the 4th course (المستوى الرابع)
    $cats_with_enrolled_studs = []; // An associative array with the structure: categoryid => No. of enrolled studs
    $cats_with_graduates = []; // An associative array with the structure: categoryid => No. of graduates
    $cats_with_statuses = []; // An associative array with the structure: categoryid => [preliminary_startdate , fourth_enddate] dates as timestamp integers
    foreach ($decoded_courses as $course) {
      $graduates_count = 0;

      if (strpos($course['fullname'], 'المستوى التمهيدي') !== false) {
        $course_url =
          'https://moddaker.com/birmingham/webservice/rest/server.php?wstoken=6205b87bf70f63264e85e23200a67b88&wsfunction=core_enrol_get_enrolled_users&moodlewsrestformat=json&courseid=' . $course['id'];
        $curl = curl_init();
        curl_setopt_array($curl, [
          CURLOPT_URL => $course_url,
          CURLOPT_FOLLOWLOCATION => true,
          CURLOPT_RETURNTRANSFER => true
        ]);

        $enrolled_studs = json_decode(curl_exec($curl), true);


        $cats_with_enrolled_studs[$course['categoryid']] = count($enrolled_studs);
        $cats_with_statuses[$course['categoryid']] = [$course['startdate'], 0];
      }


      if (strpos($course['fullname'], 'المستوى الرابع') !== false) {
        $course_url =
          'https://moddaker.com/birmingham/webservice/rest/server.php?wstoken=6205b87bf70f63264e85e23200a67b88&wsfunction=gradereport_user_get_grade_items&moodlewsrestformat=json&courseid=' . $course['id'];
        $curl = curl_init();
        curl_setopt_array($curl, [
          CURLOPT_URL => $course_url,
          CURLOPT_FOLLOWLOCATION => true,
          CURLOPT_RETURNTRANSFER => true
        ]);

        $decoded_grades = json_decode(curl_exec($curl), true);

        foreach ($decoded_grades['usergrades'] as $user_grade) {
          foreach ($user_grade['gradeitems'] as $item) {
            if ($item['itemtype'] == 'course' && $item['graderaw'] >= 0.6*$item['grademax']) {
              $graduates_count++;
            }
          }
        }
        
        $cats_with_graduates[$course['categoryid']] = $graduates_count;
        $cats_with_statuses[$course['categoryid']][1] = $course['enddate'];

      }
    }

    // echo '<div dir="ltr">';
    // print_r($cats_with_enrolled_studs);
    // echo '</div>';

    // echo  '<div dir="ltr">';
    // print_r($cats_with_graduates);
    // echo '</div>';

    echo  '<div dir="ltr">';
    print_r($cats_with_statuses); echo ' '.time();
    echo '</div>';
    ?>
    <table class="table tabe-hover table-bordered" dir="rtl">
      <thead>
        <th class="center">#</th>
        <th>الدفعة</th>
        <th>عدد الدارسين</th>
        <th>عدد الخريجين</th>
        <th>حالة الدفعة</th>
      </thead>
      <tbody>
        <?php $i = 0; ?>
        <?php foreach ($decoded_categories as $cat) : ?>
          <tr>
            <th><?php echo ++$i; ?></th>
            <td><?php echo $cat['name'] ?? '-'; ?></td>
            <td><?php echo $cats_with_enrolled_studs[$cat['id']] ?? '-'; ?></td>
            <td><?php echo $cats_with_graduates[$cat["id"]] ?? '-'; ?></td>
            <td><?php 
                  if (!isset($cats_with_statuses[$cat["id"]])) {
                    echo '-';
                  } elseif ($cats_with_statuses[$cat["id"]][0] > time()) {
                    echo 'تسجيلها مرتقب'.'<br>';
                    echo date('Y-m-d',$cats_with_statuses[$cat["id"]][0]);
                  } elseif ($cats_with_statuses[$cat["id"]][1] < time()) {
                    echo 'منتهية'.'<br>';
                    echo date('Y-m-d',$cats_with_statuses[$cat["id"]][1]);
                  } elseif ($cats_with_statuses[$cat["id"]][0] < time() && $cats_with_statuses[$cat["id"]][1] > time()) {
                    echo 'قيد الدراسة';
                  }
            ?></td>
          </tr>
        <?php endforeach; ?>
      </tbody>
    </table>
  </div>
</div>

<!-- report -->
<div class="col-md-12">
  <div class="card card-outline card-success" dir="rtl">
    <div class="card-header">
      <b>شاشة التقارير</b>
    </div>

  </div>
</div>

<!-- report -->
<div class="col-md-12">
  <div class="card card-outline card-success" dir="rtl">
    <div class="card-header">
      <b>شاشة التقارير</b>
    </div>

  </div>
</div>

<script>
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
  // const showHide=()=>{
  //   var report_item = document.getElementById('report_by').value;
  //   console.log(report_item);
  //   var item = document.getElementById('emploee_list')
  //   console.log(item);
  //   if(report_item == 1 ){
  //     report_item.style.display = 'block'
  //   } else {
  //     report_item.style.display = 'none'
  //   }
  // }
</script>
<script>
  $(document).ready(function() {
    $('#report_by').click(function() {
      var report_item = document.getElementById('report_by').value;
      var item1 = document.getElementById('employee_list')
      if (report_item == 2) {
        item1.style.display = 'block'

      } else {
        item1.style.display = 'none'


      }

    })
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
      var report_by = $('#report_by option:selected').val();
      if (project == "") {
        alert("Please Choose project");
        return;
      }
      if (report_by == 0) {
        alert("Please Choose Report Type");
        return;
      }

      $.ajax({
        url: "data.php",
        method: "POST",
        data: {
          action: 'fetch',
          project: project,
          employee: employee,
          from_date: from_date.toISOString().slice(0, 10),
          to_date: to_date.toISOString().slice(0, 10),
          report_by: report_by
        },
        dataType: "JSON",
        success: function(data) {
          var report_by = [];
          var total = [];
          var color = [];

          for (var count = 0; count < data.length; count++) {
            report_by.push(data[count].report_by);
            total.push(data[count].total);
            color.push(data[count].color);
            // console.log(data[count].project_id)
          }

          var chart_data = {
            labels: report_by,
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

<style>
  /* Chart canvas and div styles */
  .chartCard {
    background-color: whitesmoke;
    box-shadow: -8px 8px 16px 0 rgba(0, 0, 0, 0.2);
    /* h-offset v-offset blur spread color */
    transition: 0.3s;
    border-radius: 15px;
    margin: 2%;
    padding-top: 20px;
    padding-bottom: 20px;
  }

  .chartCard:hover {
    box-shadow: -16px 16px 16px 8px rgba(0, 0, 0, 0.2);
  }

  .chartsPanel {
    display: flex;
    flex-direction: row;
  }

  div[id^="chartdiv"] {
    /* The Operator ^ - Match elements that starts with given value
    The Operator * - Match elements that have an attribute containing a given value: div[id*="chartdiv"] 
    */

    width: 100%;
    height: 300px;
    font-family: 'arabic typesetting';
    font-size: large;
  }

  #chartCard3 {
    padding-top: 10px;
    padding-right: 10px;
    padding-bottom: 10px;
    padding-left: 10px;
  }

  /********************* TABLES ******************* */
  td{
    direction: rtl;
    text-align: right;
  }
  th{
    text-align: center;
  }

  .card-header{
    text-align: right;
    color: #28a745;
  }
</style>