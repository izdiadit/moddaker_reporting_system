<script type="text/javascript" src="/Moddaker_Reporting_System/amcharts5/index.js"></script>
<script type="text/javascript" src="/Moddaker_Reporting_System/amcharts5/percent.js"></script>
<script type="text/javascript" src="/Moddaker_Reporting_System/amcharts5/xy.js"></script>
<script type="text/javascript" src="/Moddaker_Reporting_System/amcharts5/themes/Animated.js"></script>
<script type="text/javascript" src="/Moddaker_Reporting_System/amcharts5/themes/Dataviz.js"></script>
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
      
			$Batch_cat_names = [
				'ar' => 'دفعة ال',
				// '!ar' => '',
				'id' => 'Level Persiapan',
				'en' => 'Batch',
				'fr' => 'Miscellaneous'
			];
			$first_levels = [
				'ar' => 'المستوى الأول',
				// '!ar' => '',
				'id' => 'Hizb Al A\'laa',
				'en' => 'The First Level',
				'fr' => '***'
			];
			$fourth_levels = [
				'ar' => 'المستوى الرابع',
				// '!ar' => '',
				'id' => '***',
				'en' => 'The Fourth Level',
				'fr' => '***'
			];

			// Check the selected langauage/s to get its data:
			$Lang = $_POST['lang'] ?? array_keys($langs)[0];
			$token = $tokens[$Lang];
			?>
			<!-- Language Selection Form -->
			<form id="langFilter" dir="rtl" action="./index.php?page=report_academic_status" method="post">
				<!-- <input type="text" value="report_students" id="page" hidden> -->
				<label for="lang">اختر نسخة برنامج مدكر: </label>
				<select name="lang" id="lang" onchange="this.form.submit()">
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
<!-- /////////////////////////////////////////////////////      Academic Status Report      //////////////////////////////////////////////////////////////// -->
<div class="col-md-12" id="asr-table-card">
  <div class="card card-outline card-success" dir="rtl" style="padding: 5px;">
    <div class="card-header">
      <b>الحالة الأكاديمية</b>
    </div>
    <?php
    // Get all courses:
    // $courses_url = "https://$Lang.moddaker.com/webservice/rest/server.php?moodlewsrestformat=json&wsfunction=local_reports_service_get_course_info&wstoken=$token";
    // $curl = curl_init();
    // curl_setopt_array($curl, [
    //   CURLOPT_URL => $courses_url,
    //   CURLOPT_FOLLOWLOCATION => true,
    //   CURLOPT_RETURNTRANSFER => true
    // ]);

    // $decoded_courses = json_decode(curl_exec($curl), true);
    $data = getData("fetcheddata/$Lang-ac.json");
    $decoded_courses = $data['courses'];
    //  The array structure: $decoded_courses = [
      // [0] => ["courseid": int,
      // "coursename": string,
      // "categoryid": int,
      // "Enroled": int]
      // ]

    // echo '<div dir="ltr">';
    // print_r($decoded_courses);
    // echo '</div>';

    // Prepare the ids parameter that will be passed in the url:
    // $cat_ids = '';

    // foreach ($decoded_courses as $course) {
    //   $cat_ids = $cat_ids . ',' . $course['categoryid'];
    // }
    // $cat_ids = ltrim($cat_ids, ',');
    // echo $cat_ids . '<br>';

    // Get all categories:
    // $categories_url = "https://$Lang.moddaker.com/webservice/rest/server.php?wstoken=$token&wsfunction=core_course_get_categories&moodlewsrestformat=json";
    // $curl = curl_init();
    // curl_setopt_array($curl, [
    //   CURLOPT_URL => $categories_url,
    //   CURLOPT_FOLLOWLOCATION => true,
    //   CURLOPT_RETURNTRANSFER => true
    // ]);

    // $decoded_categories = json_decode(curl_exec($curl), true);
    
    $decoded_categories = $data['categories'];
    // echo '<div dir="ltr">';
    // print_r($decoded_categories);
    // echo '</div>';

    // 1. Get the no. of students enrolled in every category by getting the no. of students enrolled in the 1st course (المستوى التمهيدي)
    // 2. Get the no. of graduates in every category by getting the no. of students ended the 4th course (المستوى الرابع)
    $cats_with_enrolled_studs = []; // An associative array with the structure: categoryid => No. of enrolled studs
    $cats_with_graduates = []; // An associative array with the structure: categoryid => No. of graduates
    $cats_with_statuses = []; // An associative array with the structure: categoryid => [isset, preliminary_startdate , fourth_enddate] dates as timestamp integers

    $special_batches = [6,7,9]; // الدفعة الأولى: 6 - الدفعة الثانية: 7 - الدفعة الثالثة: 9
    foreach ($decoded_courses as $course) {

      if (!isset($cats_with_statuses[$course['categoryid']])) $cats_with_statuses[$course['categoryid']] = [0, 0, 0];
      if (strpos($course['coursename'], 'المستوى التمهيدي') !== false){
        $cats_with_statuses[$course['categoryid']][0] = 1;
        $cats_with_statuses[$course['categoryid']][1] = $course['startdate'];
      }

      if (strpos($course['coursename'], $first_levels[$Lang]) !== false) {
        $cats_with_enrolled_studs[$course['categoryid']] = $course['Enroled'];
      }

      if (strpos($course['coursename'], $fourth_levels[$Lang]) !== false) {
        $cats_with_graduates[$course['categoryid']] = $course['Graduates'];
      }

      if (strpos($course['coursename'], $fourth_levels[$Lang]) !== false) {
        $cats_with_graduates[$course['categoryid']] = $course['Graduates'];
      }
      if (in_array($course['categoryid'], $special_batches) && strpos($course['coursename'], 'المستوى الثالث') !== false) {
        $cats_with_graduates[$course['categoryid']] = $course['Graduates'];
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
          if (!(strpos($cat['name'], $Batch_cat_names[$Lang]) !== false) || $cat['name'] == "مشرفات الدفعة الأولى"){
            unset($cats_with_enrolled_studs[$cat['id']]);
            unset($cats_with_graduates[$cat['id']]);
            continue;
          } 
        ?>
          <tr>
            <th><?php echo ++$i; ?></th>
            <td><?php echo $cat['name'] ?? '-'; ?></td>
            <td><?php echo $cats_with_enrolled_studs[$cat['id']] ?? '-'; ?></td>
            <td><?php echo $cats_with_graduates[$cat["id"]] ?? '-'; ?></td>
            <td><?php

                if (!isset($cats_with_statuses[$cat["id"]]) || $cats_with_statuses[$cat["id"]][0] == 0) {
                  echo '-';
                } else {
                  if ($cats_with_statuses[$cat["id"]][1] > time()) {
                    echo 'تسجيلها مرتقب' . '<br>';
                    echo date('Y-m-d', $cats_with_statuses[$cat["id"]][1]);
                  } elseif (time() > $cats_with_statuses[$cat["id"]][2]) {
                    echo 'منتهية' . '<br>';
                    // echo date('Y-m-d', $cats_with_statuses[$cat["id"]][2]); // 
                  } else { // preliminary_startdate > time() > fourth_enddate
                    echo 'قيد الدراسة';
                  }
                }
                ?></td>
          </tr>
        <?php endforeach; ?>
      </tbody>
    </table>
  </div>
</div>
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
      <div id="chartdivas1" style="height: 400px; width: 75%; margin: auto;"></div>

    </div>


  </div>
</div>
<!-- /////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////// -->

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
    minGridDistance: 30,
    cellStartLocation: 0.2,
      cellEndLocation: 0.8
  });
  xRenderer.labels.template.setAll({
    rotation: 0,
    centerY: am5.p50,
    centerX: am5.p50,
    text: "[fontFamily: calibri; fontSize: 20px;]{category}[/]"
  });

  var yRenderer = am5xy.AxisRendererY.new(rootas, {
    minGridDistance: 30
  });
  yRenderer.labels.template.setAll({
    paddingLeft: 50,
  });

  var xAxis = chartas.xAxes.push(am5xy.CategoryAxis.new(rootas, {
    maxDeviation: 0.3,
    categoryField: "batch",
    renderer: xRenderer,
    tooltip: am5.Tooltip.new(rootas, {
      
    })
  }));
  xAxis.get("renderer").labels.template.setAll({
    oversizedBehavior: "wrap",
    maxWidth: 120,
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
      name: `[fontFamily: calibri; fontSize: 23px]${name}[/]`,
      xAxis: xAxis,
      yAxis: yAxis,
      valueYField: fieldName,
      sequencedInterpolation: true,
      categoryXField: "batch",
    }));

    // myseriesas.columns.template.adapters.add("fill", () => color);
    // myseriesas.columns.template.adapters.add("stroke", () => color);

    myseriesas.columns.template.setAll({
      cornerRadiusTL: 40,
      cornerRadiusTR: 40,
      width: am5.percent(95)
    });


    myseriesas.columns.template.setAll({
      // tooltipText: "{valueY}"
      tooltipHTML: "<div class='customTooltip'>{valueY}</div>"
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
<script>
  // Close academic status report table if the user is neither admin (1) nor manager (2):
    var login_type = <?php echo json_encode($_SESSION['login_type'])?>;
    console.log(login_type);
    console.log(typeof login_type);
    if (!(login_type == '1' || login_type == '2')) {
      var card = document.getElementById("asr-table-card");
      card.style.display = 'none';
    }

  $(document).ready(function() {
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