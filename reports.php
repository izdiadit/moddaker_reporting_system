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

  $url = 'https://moddaker.com/birmingham/webservice/rest/server.php?wstoken=6205b87bf70f63264e85e23200a67b88&wsfunction=core_user_get_users&moodlewsrestformat=json&criteria[0][key]=lastname&criteria[0][value]=%25';

  $curl = curl_init($url);
  curl_setopt($curl, CURLOPT_RETURNTRANSFER, true);
  $response = curl_exec($curl);

  curl_close($curl);
  $result = json_decode($response, true);


  for ($i = 0; $i < count($result); $i++) {
    $row = $result[$i];
    $result[$i]['country'] = $string["$row[country]"];
  }
}
?>
<div class="col-md-12">
  <div class="card card-outline card-success">
    <div class="card-header">
      <b>شاشة التقارير</b>
    </div>
    <div class="chartsPanel">
        <div class="chartCard" id="chartdiv" style="width: 50%;"></div>

        <?php if ($current_user->type == 'مدير نظام') : ?>
            <div class="chartCard" id="chartdiv2" style="width: 50%;"></div>
        <?php endif; ?>
    </div>

    <div class="chartsPanel">
        <div class="chartCard" id="chartCard3" style="width: 50%;">
            <div id="chartdiv3"></div>
        </div>

        <?php if ($current_user->type == 'مدير نظام') : ?>
            <div class="chartCard" id="chartdiv4" style="width: 50%;"></div>
        <?php endif; ?>
    </div>

    <script type="text/javascript" src="/moddaker_report_screen/amcharts5/index.js"></script>
    <script type="text/javascript" src="/moddaker_report_screen/amcharts5/percent.js"></script>
    <script type="text/javascript" src="/moddaker_report_screen/amcharts5/xy.js"></script>
    <script type="text/javascript" src="/moddaker_report_screen/amcharts5/themes/Animated.js"></script>

    <script>
        // Reading country_data from php:
        var result = <?php echo json_encode($result);?>;
        // // var country_data = JSON.parse(result);
        var country_data = [];
        for (const key in result) {
            if (result.hasOwnProperty.call(result, key)) {
                country_data.push(result[key]);
            }
        }
        console.log(result)
        console.log(country_data)
        
        
        
        
        // محاولة غير ناجحة
        // var country_data = [];
        // async function getcountry_data(url) {
            //     let response = await fetch(url);
            //     console.log(response.json())
            //     return response.json();  // 
            // }
            
            
            // getcountry_data('http://localhost/moodle/mapi/api.php').then(
                //     (response) => {
                    //         country_data = response;
                    //     }
                    // );
                    // console.log(country_data)
                    </script>
    
    <script src="report_charts.js"></script>
</div>
</div>
</div>
<script>
  $('#print').click(function() {
    showHide()
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