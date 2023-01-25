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


include 'countries.php';

$data = getData('fetcheddata/en-students.json');
$moodle_users = $data['data'];
echo $data['lastupdate'] . '<br>';
	// print_r( $moodle_users[0] );

// Preparing students countries statistics:
$countries = [];

for ($i = 0; $i < count($moodle_users); $i++) {
  if(!array_key_exists($moodle_users[$i]['country'], $string)) $moodle_users[$i]['country'] = '-';

  $temp = $moodle_users[$i]['country'] ?? '-';

  if (array_key_exists($string[$temp], $countries)) {
    $countries[$string[$temp]] += 1;
  } else {
    $countries[$string[$temp]] = 1;
  }
}

// Make an estimation of the % of each country by removing unsetted country data elements.
$unsetted = $countries['-'];
// echo $unsetted;
unset($countries['-']);

// Reform coutries data by gathering all minor values in one element:
  
  arsort($countries); // Sorts the array descendingly by values
  $reformed_countries = array_slice($countries, 0, 7, true);
  $reformed_countries['دول أخرى'] = array_sum(array_slice($countries, 7, count($countries), true));

  // echo array_sum($reformed_countries) + $unsetted; for en: 8023

// Preparing students type (male, femle) statistics:

// $custom_fields_data = []; // An associative array with the structure: userid => ['shortname' => 'value']
// for ($i = 0; $i < count($moodle_users); $i++) {
//   $custom_fields_data[$moodle_users[$i]['id']] = [];
//   foreach ($moodle_users[$i]['customfields'] as $cfield) {
//     $custom_fields_data[$moodle_users[$i]['id']][$cfield['shortname']] = $cfield['value'];
//   }
// }

$types = ['ذكر' => 0, 'أنثى' => 0];
for ($i = 0; $i < count($moodle_users); $i++) {
  if (!isset($moodle_users[$i]['sex'])) {
    continue;
  }
  if ($moodle_users[$i]['sex'] == 'ذكر' || $moodle_users[$i]['sex'] == 'male') {
    $types['ذكر'] += 1;
  }
  if ($moodle_users[$i]['sex'] == 'أنثى' || $moodle_users[$i]['sex'] == 'female') {
    $types['أنثى'] += 1;
  }
}
// print_r($types);
?>
<div class="col-md-12">
  <div class="card card-outline card-success" dir="rtl">
    <div class="card-header">
      <b>مدكر في أرقام</b>
    </div>
    <div class="chartsPanel">
      <div class="chartCard" id="chartCard1">
        <div class="chartCardHeader">
          <a href="#" onclick="toggleFullscreen('chartCard1')" style="color: #c6c6c6"><i class="fas fa-expand-arrows-alt"></i></a>
          <div class="chartTitle"> الدارسون والدارسات </div>
          <div style="visibility: hidden"></div>
        </div>
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
      var result = <?php echo json_encode($reformed_countries); ?>;
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

      function setFont(myelement) {
        descendants = [...myelement.getElementsByTagName('*')];
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