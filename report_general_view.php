<?php

include 'db_connect.php';
?>

<!-- Language Selection Card -->
<div class="card card-outline card-success">
  <div class="card-header">

  </div>
  <div class="card-body" style="overflow:auto; text-align: right;" dir="rtl">
    <?php
    // The array of languages will be selected by the user, and elements will appear depending on the user type:
    include 'langs.php';

    // Check the selected langauage/s to get its data:
    $Lang = $_POST['lang'] ?? 'ar';
    $token = $tokens[$Lang];
    ?>
    <!-- Language Selection Form -->
    <form id="langFilter" dir="rtl" action="./index.php?page=report_general_view" method="post">
      <!-- <input type="text" value="report_students" id="page" hidden> -->
      <label for="lang">اختر نسخة برنامج مدكر: </label>
      <select class="selectpicker" name="lang" id="lang" onchange="this.form.submit()">
        <?php foreach ($langs as $key => $lang) {
          if ($key == $_POST['lang'] || $key == $_GET['lang']) { // To assure that the submitted option will be selected after submitting form:
            echo "<option value='$key' selected='selected'>$lang</option>";
          } else {
            echo "<option value='$key'>$lang</option>";
          }
        } ?>
      </select>
    </form>
  </div>
</div>
<!-- END OF Language Selection Card -->

<?php
include 'countries.php';

$data = getData("fetcheddata/$Lang-students.json");
$moodle_users = $data['data'];
// echo $data['lastupdate'] . '<br>';

// print_r( $moodle_users[0] );

// Preparing students countries statistics:
$countries_url = "https://$Lang.moddaker.com/webservice/rest/server.php?moodlewsrestformat=json&wsfunction=local_reports_service_get_countries&wstoken=$token";
$curl = curl_init($countries_url);
curl_setopt_array(
  $curl,
  [
    CURLOPT_FOLLOWLOCATION => true,
    CURLOPT_RETURNTRANSFER => true
  ]
);

$result = curl_exec($curl);
$countries_data = json_decode($result, true);

$countries = [];
foreach ($countries_data as $row) {
  if (!array_key_exists($row['country'], $string)) $row['country'] = '-';

  $temp = $row['country'] ?? '-';
  $countries[$string[$temp]] = $row['count'];
}


// Make an estimation of the % of each country by removing unsetted country data elements.
$unsetted = $countries['-'];
// echo $unsetted;
unset($countries['-']);

// Reform coutries data by gathering all minor values in one element:

arsort($countries); // Sorts the array descendingly by values
$reformed_countries = array_slice($countries, 0, 7, true);
if (count($countries) > 8) $reformed_countries['دول أخرى'] = array_sum(array_slice($countries, 7, count($countries), true));

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
  if ($moodle_users[$i]['sex'] == 'ذكر' || $moodle_users[$i]['sex'] == 'male' || $moodle_users[$i]['sex'] == 'Mal') {
    $types['ذكر'] += 1;
  }
  if ($moodle_users[$i]['sex'] == 'أنثى' || $moodle_users[$i]['sex'] == 'female' || $moodle_users[$i]['sex'] == 'Femelle') {
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
      <div class="chartCard" id="chartCard0">
        <div class="chartCardHeader">
          <a href="#" onclick="toggleFullscreen('chartCard0')" style="color: #c6c6c6"><i class="fas fa-expand-arrows-alt"></i>  ملء الشاشة</a>
          <div class="chartTitle"> عدد دارسي مدكّر </div>
          <div style="visibility: hidden"></div>
        </div>
        <?php
        // Get the total number of users:
        
        ?>
        <div style="margin: 5% auto; font-size: 55px; color: #977c47"><?php echo '180000+' ?></div>
      </div>
      <div class="chartCard" id="chartCard1">
        <div class="chartCardHeader">
          <a href="#" onclick="toggleFullscreen('chartCard1')" style="color: #c6c6c6"><i class="fas fa-expand-arrows-alt"></i>  ملء الشاشة</a>
          <div class="chartTitle"> عدد دارسي النسخة <?php echo $langs[$Lang]?> </div>
          <div style="visibility: hidden"></div>
        </div>
        <?php
        // Get the total number of users:
        $total_count = file_get_contents("https://$Lang.moddaker.com/webservice/rest/server.php?moodlewsrestformat=json&wsfunction=local_reports_service_get_total_users&wstoken=$token");
        $total_count = json_decode($total_count);
        ?>
        <div id="counter" style="margin: 5% auto; font-size: 55px; color: #977c47"><?php echo $total_count ?></div>
      </div>
    </div>
    <div class="chartsPanel">
      <div class="chartCard" id="chartCard2">
        <div class="chartCardHeader">
          <a href="#" onclick="toggleFullscreen('chartCard2')" style="color: #c6c6c6"><i class="fas fa-expand-arrows-alt"></i>  ملء الشاشة</a>
          <div class="chartTitle"> الدارسون والدارسات </div>
          <div style="visibility: hidden"></div>
        </div>
        <div id="chartdiv" style="width: 100%;"></div>
      </div>

      <div class="chartCard" id="chartCard3">
        <div class="chartCardHeader">
          <a href="#" onclick="toggleFullscreen('chartCard3')" style="color: #c6c6c6"><i class="fas fa-expand-arrows-alt"></i>  ملء الشاشة</a>
          <div class="chartTitle"> دول الدارسين </div>
          <div style="visibility: hidden"></div>
        </div>
        <div id="chartdiv2" style="width: 100%;"></div>
      </div>
    </div>

    <!-- <div class="chartsPanel">
      <div class="chartCard" id="chartCard3">
        <div class="chartTitle"> دول الدارسين </div>
        <div id="chartdiv3"></div>
      </div>
      <div class="chartCard" id="chartdiv4" style="width: 50%;"></div>
    </div> -->

    <script type="text/javascript" src="/Moddaker_Reporting_System/amcharts5/index.js"></script>
    <script type="text/javascript" src="/Moddaker_Reporting_System/amcharts5/percent.js"></script>
    <script type="text/javascript" src="/Moddaker_Reporting_System/amcharts5/xy.js"></script>
    <script type="text/javascript" src="/Moddaker_Reporting_System/amcharts5/themes/Animated.js"></script>
    <script type="text/javascript" src="/Moddaker_Reporting_System/amcharts5/themes/Responsive.js"></script>

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
            this.style.position = 'relative';
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

    <style>
      #counter ::after {
  /* font: 800 40px system-ui; */
  content: counter(<?php echo $total_count?>);
  animation: <?php echo $total_count?> 5s linear infinite alternate;
  counter-reset: count 0;
}

@keyframes counter {
  0% {
    counter-increment: count 0;
  }
  10% {
    counter-increment: count 1;
  }
  20% {
    counter-increment: count 2;
  }
  30% {
    counter-increment: count 3;
  }
  40% {
    counter-increment: count 4;
  }
  50% {
    counter-increment: count 5;
  }
  60% {
    counter-increment: count 6;
  }
  70% {
    counter-increment: count 7;
  }
  80% {
    counter-increment: count 8;
  }
  90% {
    counter-increment: count 9;
  }
  100% {
    counter-increment: count 10;
  }
}
    </style>
  </div>
</div>