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
    $Lang = $_POST['lang'] ?? array_keys($langs)[0];
    // print_r(array_keys($langs)[0]);
    $token = $tokens[$Lang];
    ?>
    <!-- Language Selection Form -->
    <form id="langFilter" dir="rtl" action="./index.php?page=report_map" method="post">
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

// $data = getData("fetcheddata/$Lang-students.json");
// $moodle_users = $data['data'];
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

// $countries = [];
// foreach ($countries_data as $row) {
//   if (!array_key_exists($row['country'], $string)) $row['country'] = '-';

//   $temp = $row['country'] ?? '-';
//   $countries[$string[$temp]] = $row['count'];
// }


// // Make an estimation of the % of each country by removing unsetted country data elements.
// $unsetted = $countries['-'];
// // echo $unsetted;
// unset($countries['-']);

// // Reform countries data by gathering all minor values in one element:

// arsort($countries); // Sorts the array descendingly by values
// $reformed_countries = array_slice($countries, 0, 7, true);
// if (count($countries) > 8) $reformed_countries['دول أخرى'] = array_sum(array_slice($countries, 7, count($countries), true));

// // echo array_sum($reformed_countries) + $unsetted; for en: 8023


?>
<div class="col-md-12">
  <div class="card card-outline card-success" dir="rtl">
    <div class="card-header">
      <b></b>
    </div>
    <div class="chartsPanel">
      <div class="chartCard" id="chartCard4" style="width: 100%">
        <div class="chartCardHeader">
          <a href="#" onclick="toggleFullscreen('chartCard4')" style="color: #c6c6c6"><i class="fas fa-expand-arrows-alt"></i> ملء الشاشة</a>
          <div class="chartTitle"> دول دارسي النسخة <?php echo $langs[$Lang];?> </div>
          <div style="visibility: hidden"></div>
        </div>
        <br>
        <div id="chartdiv4" style="width: 100%; height: 750px; margin: auto;"></div>
        <br>
      </div>
    </div>

    <script type="text/javascript" src="/Moddaker_Reporting_System/amcharts5/index.js"></script>
    <script type="text/javascript" src="/Moddaker_Reporting_System/amcharts5/percent.js"></script>
    <script type="text/javascript" src="/Moddaker_Reporting_System/amcharts5/xy.js"></script>
    <script type="text/javascript" src="/Moddaker_Reporting_System/amcharts5/themes/Animated.js"></script>
    <script type="text/javascript" src="/Moddaker_Reporting_System/amcharts5/themes/Responsive.js"></script>

    <!-- Sources For Maps -->
    <script type="text/javascript" src="/Moddaker_Reporting_System/amcharts5/map.js"></script>
    <script type="text/javascript" src="/Moddaker_Reporting_System/amcharts5/moddaker_maps/worldLow.js"></script>
    <script type="text/javascript" src="/Moddaker_Reporting_System/amcharts5/moddaker_maps/AR.js"></script>

    <script type="text/javascript" src="countries_map.js"></script>

    <script>
      // Reading country_data from php:
      var result = <?php echo json_encode($countries_data); ?>;
      // // var country_data = JSON.parse(result);
      var country_data = [];
      for (const key in result) {
        if (result.hasOwnProperty.call(result, key)) {
          country_data.push(result[key].country);
        }
      }

      if(country_data.includes('AQ')) country_data.splice(country_data.indexOf('AQ'),1);
      console.log(result);
      console.log(country_data);
    </script>
  </div>
</div>