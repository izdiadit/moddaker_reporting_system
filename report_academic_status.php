<script type="text/javascript" src="/Moddaker_Reporting_System/amcharts5/index.js"></script>
<script type="text/javascript" src="/Moddaker_Reporting_System/amcharts5/percent.js"></script>
<script type="text/javascript" src="/Moddaker_Reporting_System/amcharts5/xy.js"></script>
<script type="text/javascript" src="/Moddaker_Reporting_System/amcharts5/themes/Animated.js"></script>
<script type="text/javascript" src="/Moddaker_Reporting_System/amcharts5/themes/Dataviz.js"></script>
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

?>

<!-- /////////////////////////////////////////////////////      Academic Status Report      //////////////////////////////////////////////////////////////// -->
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
    $cats_with_statuses = []; // An associative array with the structure: categoryid => [isset, preliminary_startdate , fourth_enddate] dates as timestamp integers
    foreach ($decoded_courses as $course) {
      $graduates_count = 0;

      if (!isset($cats_with_statuses[$course['categoryid']])) $cats_with_statuses[$course['categoryid']] = [0, 0, 0]; // 2550000000 -> 2050-10-21 23:10:00
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
        $cats_with_statuses[$course['categoryid']][0] = 1;
        $cats_with_statuses[$course['categoryid']][1] = $course['startdate'];
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
            if ($item['itemtype'] == 'course' && $item['graderaw'] >= 0.6 * $item['grademax']) {
              $graduates_count++;
            }
          }
        }

        $cats_with_graduates[$course['categoryid']] = $graduates_count;

        $cats_with_statuses[$course['categoryid']][0] = 1;
        $cats_with_statuses[$course['categoryid']][2] = $course['enddate'];
        // echo $course['enddate'] . ' ||| ' . $cats_with_statuses[$course['categoryid']][2] .'<br>'.time();
      }
    }

    // echo '<div dir="ltr">';
    // print_r($cats_with_enrolled_studs);
    // echo '</div>';

    // echo  '<div dir="ltr">';
    // print_r($cats_with_graduates);
    // echo '</div>';

    // echo  '<div dir="ltr">';
    // print_r($cats_with_statuses);
    // echo ' ' . time();
    // echo '</div>';
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
        <?php foreach ($decoded_categories as $cat) :

        ?>
          <tr>
            <th><?php echo ++$i; ?></th>
            <td><?php echo $cat['name'] ?? '-'; ?></td>
            <td><?php echo $cats_with_enrolled_studs[$cat['id']] ?? '-'; ?></td>
            <td><?php echo $cats_with_graduates[$cat["id"]] ?? '-'; ?></td>
            <td><?php

                if ($cats_with_statuses[$cat["id"]][0] == 0) {
                  echo '-';
                } else {
                  if ($cats_with_statuses[$cat["id"]][1] > time()) {
                    echo 'تسجيلها مرتقب' . '<br>';
                    echo date('Y-m-d', $cats_with_statuses[$cat["id"]][1]);
                  } elseif (time() > $cats_with_statuses[$cat["id"]][2]) {
                    echo 'منتهية' . '<br>';
                    echo date('Y-m-d', $cats_with_statuses[$cat["id"]][2]); // 
                  } else { // preliminary_startdate > time() > fourth_enddate
                    echo 'قيد الدراسة';
                  }
                }
                // if ($cats_with_statuses[$cat["id"]][0] == 0) {
                //   echo '-';
                // } elseif ($cats_with_statuses[$cat["id"]][1] > time()) {
                //   echo 'تسجيلها مرتقب' . '<br>';
                //   echo date('Y-m-d', $cats_with_statuses[$cat["id"]][1]);
                // } elseif ($cats_with_statuses[$cat["id"]][2] < time()) {
                //   echo 'منتهية' . '<br>';
                //   echo date('Y-m-d', $cats_with_statuses[$cat["id"]][2]);
                // } elseif ($cats_with_statuses[$cat["id"]][1] < time() && $cats_with_statuses[$cat["id"]][2] > time()) {
                //   echo 'قيد الدراسة';
                // }
                ?></td>
          </tr>
        <?php endforeach; ?>
      </tbody>
    </table>
  </div>
</div>

<!-- /////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////// -->
<!-- //////////////////////////////////////////////////      Academic Status Interactive Report      ///////////////////////////////////////////////////////////// -->
<?php
function swap_data_from_dictionary($dict, $data, $old, $new)
{
  $map = [];
  foreach ($dict as $item) {
    $map[$item[$old]] = $item[$new];
  }

  $result = [];
  foreach ($data as $key => $value) {
    $result[$map[$key]] = $value;
  }

  return $result;
}

$cats_studs_for_js = swap_data_from_dictionary($decoded_categories, $cats_with_enrolled_studs, 'id', 'name');
$cats_grads_for_js = swap_data_from_dictionary($decoded_categories, $cats_with_graduates, 'id', 'name');
?>
<div class="col-md-12">
  <div class="card card-outline card-success" dir="rtl">
    <div class="card-header">
      <b>العرض التفاعلي للحالة الأكاديمية</b>
    </div>
    <div class="chartsPanel">
      <div id="chartdivas1" style="width: 50%; margin: auto;"></div>

    </div>


  </div>
</div>
<script>
  // Reading category_studs_data from php:
  var cats_with_enrolled_studs = <?php echo json_encode($cats_studs_for_js); ?>;
  var cats_with_grads = <?php echo json_encode($cats_grads_for_js); ?>;
  // Converting to an object that is suitable for the charts data structure
  var category_studs_data = [];
  for (const key in cats_with_enrolled_studs) {
    if (cats_with_enrolled_studs.hasOwnProperty.call(cats_with_enrolled_studs, key)) {
      category_studs_data.push({
        batch: key,
        studs: (cats_with_enrolled_studs[key]),
        grads: (cats_with_grads[key])
      });
    }
  }


  console.log(category_studs_data)
  var rootas = am5.Root.new("chartdivas1");

  rootas.setThemes([
    am5themes_Animated.new(rootas)
  ]);

  var chartas = rootas.container.children.push(am5xy.XYChart.new(rootas, {
    // panX: true,
    // panY: true,
    // wheelX: "panX",
    // wheelY: "zoomX",
    // pinchZoomX: true,
    layout: rootas.verticalLayout
  }));


  // Add cursor
  // var cursor = chartas.set("cursor", am5xy.XYCursor.new(rootas, {}));
  // cursor.lineY.set("visible", false);


  // Create axes
  var xRenderer = am5xy.AxisRendererX.new(rootas, {
    minGridDistance: 30
  });
  xRenderer.labels.template.setAll({
    rotation: 0,
    centerY: am5.p50,
    centerX: am5.p50,
  });

  var yRenderer = am5xy.AxisRendererY.new(rootas, {
    minGridDistance: 30
  });
  yRenderer.labels.template.setAll({
    paddingLeft: 30,
  });

  var xAxis = chartas.xAxes.push(am5xy.CategoryAxis.new(rootas, {
    maxDeviation: 0.3,
    categoryField: "batch",
    renderer: xRenderer,
    tooltip: am5.Tooltip.new(rootas, {})
  }));
  xAxis.get("renderer").labels.template.setAll({
    oversizedBehavior: "wrap",
    maxWidth: 100,
    textAlign: "center",
  });

  var yAxis = chartas.yAxes.push(am5xy.ValueAxis.new(rootas, {
    maxDeviation: 0.3,
    strictMinMax: true, // Fixed scale even if series availability changes
    extraMax: 0.1, // Increasing by 10% of the max value
    renderer: yRenderer
  }));

  // Legend:
  var legend = chartas.children.push(am5.Legend.new(rootas, {
    centerX: am5.percent(30),
    x: am5.percent(60),
    centerY: am5.percent(100),
    y: am5.percent(100),
  }));

  // Coloring series in this chart:
  chartas.get("colors").set("colors", [
    am5.color(0xaa8e55),
    am5.color(0x637535),
    am5.color(0x5aaa95),
    am5.color(0x86a873),
    am5.color(0xbb9f06)
  ]);
  

  // Create series
  function makeSeries(name, fieldName, color) {
    var myseriesas = chartas.series.push(am5xy.ColumnSeries.new(rootas, {
      name: name,
      xAxis: xAxis,
      yAxis: yAxis,
      valueYField: fieldName,
      sequencedInterpolation: true,
      categoryXField: "batch",
      tooltip: am5.Tooltip.new(rootas, {
        labelText: "{studsY}"
      })
    }));

    // myseriesas.columns.template.adapters.add("fill", () => color);
    // myseriesas.columns.template.adapters.add("stroke", () => color);

    myseriesas.columns.template.setAll({
      cornerRadiusTL: 40,
      cornerRadiusTR: 40,
      width: am5.percent(65)
    });

    myseriesas.columns.template.setAll({
      tooltipText: "{valueY}*"
    });

    xAxis.data.setAll(category_studs_data);
    myseriesas.data.setAll(category_studs_data);

    legend.data.push(myseriesas);
    // legend.data.setAll([{color: color}])

    // Animation on load
    myseriesas.appear(1000);

    // myseriesas.bullets.push(function() {
    //   return am5.Bullet.new(rootas, {
    //     locationY: 0,
    //     sprite: am5.Label.new(rootas, {
    //       text: "{valueY}",
    //       fill: color,
    //       centerY: 0,
    //       centerX: am5.p50,
    //       populateText: true
    //     })
    //   });
    // });
  }

  makeSeries('       عدد الدارسين', 'studs', '#aa8e55');
  makeSeries('       عدد الخريجين', 'grads', '#637535');
  // Animation on load
  chartas.appear(1000, 100);



  // legend.data.setAll(chartas.series.values);
</script>
<!-- /////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////// -->
<!-- report -->
<div class="col-md-12">
  <div class="card card-outline card-success" dir="rtl">
    <div class="card-header">
      <b>شاشة التقارير</b>
    </div>

  </div>
</div>

<!-- /////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////// -->
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
  td {
    direction: rtl;
    text-align: right;
  }

  th {
    text-align: center;
    background-color: #aa8e55;
    color: white;
  }

  .card-header {
    text-align: right;
    color: #28a745;
  }
</style>