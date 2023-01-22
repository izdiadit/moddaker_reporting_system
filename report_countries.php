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

// Preparing students countries statistics:
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

// Make an estimatio of the % of each country by removing unsetted country data elements.
unset($countries['-']);

// Preparing students type (male, femle) statistics:
$custom_fields_data = []; // An associative array with the structure: userid => ['shortname' => 'value']

for ($i = 0; $i < count($moodle_users); $i++) {
  $custom_fields_data[$moodle_users[$i]['id']] = [];
  foreach ($moodle_users[$i]['customfields'] as $cfield) {
    $custom_fields_data[$moodle_users[$i]['id']][$cfield['shortname']] = $cfield['value'];
  }
}

$types = ['ذكر' => 0, 'أنثى' => 0];
for ($i = 0; $i < count($moodle_users); $i++) {
  if (!isset($custom_fields_data[$moodle_users[$i]['id']]['sex'])) {
    continue;
  }
  if ($custom_fields_data[$moodle_users[$i]['id']]['sex'] == 'ذكر') {
    $types['ذكر'] += 1;
  }
  if ($custom_fields_data[$moodle_users[$i]['id']]['sex'] == 'أنثى') {
    $types['أنثى'] += 1;
  }
}
// print_r($types);
?>
<div class="col-md-12">
  <div class="card card-outline card-success" dir="rtl">
    <div class="card-header">
      <b>تقرير بدول الدارسين</b>
    </div>
    <div class="chartsPanel">
      <div class="chartCard">
        <div class="chartTitle"> الدارسون والدارسات </div>
        <div id="chartdiv" style="width: 100%;"></div>
      </div>
      <div class="chartCard" id="chartCard2">
        <div class="chartCardHeader">
          <a href="#" onclick="toggleFullscreen('chartCard2')" style="color: #c6c6c6"><i class="fas fa-expand-arrows-alt"></i></a>
          <div class="chartTitle"> دول الدارسين </div>
          <div style="visibility: hidden"></div>
        </div>
        <div id="chartdiv2" style="width: 100%;"></div>
      </div>
    </div>

    <div class="chartsPanel">
      <div class="chartCard" id="chartCard3">
        <div class="chartTitle"> دول الدارسين </div>
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

      // Reading type data from php:
      var types_result = <?php echo json_encode($types); ?>;
      var types_data = [];
      for (const key in types_result) {
        if (types_result.hasOwnProperty.call(types_result, key)) {
          types_data.push({
            type: key,
            count: types_result[key]
          });
        }
      }
    </script>

    <script src="report_charts.js"></script>


    <script>
      $(document).ready(
        $('.chartCard').each(function() {
          // setFont(this);
        }),

        $('.am5-tooltip-container').each(
          function() {
            this.style.position = 'fixed';
            this.style.margin = 'auto';
          }
        )
      )

      function setFont(mychart) {
        descendants = [...mychart.getElementsByTagName('*')];
        // console.log(typeof descendants);

        descendants.forEach(element => {
          element.style.fontFamily = 'arabic typesetting'
          element.style.fontSize = 'large'
          // element.style.backgroundColor = 'red'
          // element.style.overflow = 'auto'
          // element.style.margin = 'auto'
          if (element.hasChildNodes()) {
            setFont(element)
          }
        });
      }
    </script>
  </div>
</div>