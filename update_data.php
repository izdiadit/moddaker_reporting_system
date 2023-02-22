<?php
function updateData($url, $dataClass, $lang)
  {
    // If the updated data is arabic students data it will be handled in a special manner
    if ($lang == 'ar' && $dataClass == 'students') {
      updateArStudentsData();
    }
    else {
      $newfname = "fetcheddata/$lang-$dataClass-new.json";
      
      // Fetching data from url:
      $curl = curl_init();
      curl_setopt_array($curl, [
        CURLOPT_URL => $url,
        CURLOPT_FOLLOWLOCATION => true,
        CURLOPT_RETURNTRANSFER => true,
        CURLOPT_SSL_VERIFYPEER => 0,
        CURLOPT_HTTPAUTH => CURLAUTH_BASIC,
      ]);
      echo "Data is being fetched ...<br>\n";
      $json = curl_exec($curl);
      // If data fetching failed exit:
      if (!$json) {
        echo "FATAL: fetching data from url failed.<br>\n";
        exit;
      }

      // Writing data - if successfully fetched- in a new json file:
      $file = fopen($newfname, 'a+');
    //   ftruncate($file, 0);
      fwrite($file, '{"lastupdate": ' . time() . ',');
      fwrite($file, '"data": ' . $json . '}');
      fclose($file);
      
      echo "New file: ($newfname) has been created<br>\n";
      
      // Replace the old file with the updated one:
      $fname = "fetcheddata/$lang-$dataClass.json";
      if(file_exists($fname)){
          echo "File ($fname) exists and will be replaced with the updated one<br>\n";
          unlink($fname);
      }
      rename($newfname,$fname);
      echo "File ($fname) is up to date ".date('d-m-Y H:i:s a', time())." .\n";
    }
  }

  function updateArStudentsData()
  {
    // Get the total number of users:
    $total_count = file_get_contents("https://ar.moddaker.com/webservice/rest/server.php?moodlewsrestformat=json&wsfunction=local_reports_service_get_total_users&wstoken=26abc81f3a71f2c17ceec76c5d45b465");
    $total_count = json_decode($total_count);

    $students_data = [];
    $start = 0;
    
    // $i = 0;
    while (true) {
      $end = $start + 5000;

      $curl = curl_init("https://ar.moddaker.com/webservice/rest/server.php?moodlewsrestformat=json&wsfunction=local_reports_service_get_users_limited&wstoken=26abc81f3a71f2c17ceec76c5d45b465&startlimit=$start&endlimit=$end");
      curl_setopt_array($curl, [
        CURLOPT_FOLLOWLOCATION => true,
        CURLOPT_RETURNTRANSFER => true,
        CURLOPT_SSL_VERIFYPEER => 0,
        CURLOPT_HTTPAUTH => CURLAUTH_BASIC,
      ]);
      $json = curl_exec($curl);
      if (!$json) {
        echo "FATAL: fetching data from url failed.<br>\n";
        exit;
      }
      $data = json_decode($json, true);
      $students_data = array_merge($students_data, $data);
      // echo $i.' / '.$start.' / '.$end.' / ';
      print_r(count($students_data)." records have been fetched ---<br>\n");

      $start = $end + 1;
      if ($end >= $total_count) {
        break;
      }
      // $i++;
    }

    // echo "The last length: "; print_r(count($students_data)); echo '<br>';

    // Save data in a new json file:
    $students_json = json_encode($students_data);
    $newfname = "fetcheddata/ar-students-new.json";
    $file = fopen($newfname, 'a+');
    ftruncate($file, 0);
    fwrite($file, '{"lastupdate": ' . time() . ',');
    fwrite($file, '"data": ' . $students_json . '}');
    fclose($file);

    echo "New file: ($newfname) has been created<br>\n";

    // Replace the old file with the updated one:
    $fname = "fetcheddata/ar-students.json";
    if (file_exists($fname)) {
        echo "File ($fname) already exists and will be replaced with the updated one<br>\n";
        unlink($fname);
    }
    rename($newfname, $fname);
    echo "File ($fname) is up to date ".date('d-m-Y H:i:s a', time())." <br>\n";
  }



  function updateAcademicStatus($Lang){
    include 'langs.php';

    // Get all courses:
    $courses_url = "https://$Lang.moddaker.com/webservice/rest/server.php?moodlewsrestformat=json&wsfunction=local_reports_service_get_course_info&wstoken=$tokens[$Lang]";
    $curl = curl_init();
    curl_setopt_array($curl, [
      CURLOPT_URL => $courses_url,
      CURLOPT_FOLLOWLOCATION => true,
      CURLOPT_RETURNTRANSFER => true
    ]);


     /*  The array structure: $decoded_courses = [
      [0] => ["courseid": int,
      "coursename": string,
      "startdate": int,
      "enddate": int,
      "categoryid": int,
      "Enroled": int],
      "Graduates": int
      ]
      */
    $courses = curl_exec($curl);
    
    // Get all categories:
    $categories_url = "https://$Lang.moddaker.com/webservice/rest/server.php?wstoken=$tokens[$Lang]&wsfunction=core_course_get_categories&moodlewsrestformat=json";
    $curl = curl_init();
    curl_setopt_array($curl, [
      CURLOPT_URL => $categories_url,
      CURLOPT_FOLLOWLOCATION => true,
      CURLOPT_RETURNTRANSFER => true
    ]);

    $categories = curl_exec($curl);

    // Write data in ajson file:
    $file = fopen("$Lang-ac-new.json", 'a+');
    ftruncate($file, 0);
    fwrite($file, '{"lastupdate": ' . time() . ',');
    fwrite($file, '"courses": ' . $courses . ',');
    fwrite($file, '"categories": ' . $categories . '}');
    fclose($file);

    echo "New file: (fetcheddata/$Lang-ac-new.json) has been created<br>\n";

    // Replace the old file with the updated one:
    $fname = "fetcheddata/$Lang-ac.json";
    if (file_exists($fname)) {
        echo "File ($fname) already exists and will be replaced with the updated one<br>\n";
        unlink($fname);
    }
    rename("$Lang-ac-new.json", $fname);
    echo "File ($fname) is up to date ".date('d-m-Y H:i:s a', time())." <br>\n";
  }
  
  // Id Students:
  // updateData('https://id.moddaker.com/webservice/rest/server.php?moodlewsrestformat=json&wsfunction=local_reports_service_get_users&wstoken=e550100be5197a3e25596068c83ab9d2','students','id');
  
  // En Students:
  // updateData('https://en.moddaker.com/webservice/rest/server.php?moodlewsrestformat=json&wsfunction=local_reports_service_get_users&wstoken=5d67dc5eec6b25617c0e55c00c8a9fd6','students','en');
  
  // Fr Students:
  // updateData('https://fr.moddaker.com/webservice/rest/server.php?moodlewsrestformat=json&wsfunction=local_reports_service_get_users&wstoken=f5a13ccf5b087df6ed67b12afce7dc3a','students','fr');

  // Ar Students:
  updateData('','students','ar');

  // Ar academic status:
  // updateAcademicStatus('ar');
  // Id academic status:
  // updateAcademicStatus('id');
  // En academic status:
  // updateAcademicStatus('en');
  // Fr academic status:
  // updateAcademicStatus('fr');
?>