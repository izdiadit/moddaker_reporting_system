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
      <b>تقرير بدول الدارسين</b>
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
    <script type="text/javascript" src="/Moddaker_Reporting_System/amcharts5/themes/Dataviz.js"></script>

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