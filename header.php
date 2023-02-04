<head>
  <meta charset="utf-8">
  <meta name="viewport" content="width=device-width, initial-scale=1">
  <?php 
  date_default_timezone_set("Asia/Riyadh");
  
  ob_start();
  $title = isset($_GET['page']) ? ucwords(str_replace("_", ' ', $_GET['page'])) : "Home";
  ?>
  <title><?php echo $title ?> | <?php echo $_SESSION['system']['name'] ?></title>
  <?php ob_end_flush() ?>

  <?php

  function updateData($url, $dataClass, $lang)
  {
    if ($lang == 'ar' && $dataClass == 'students') {
      updateArStudentsData();
    }
    else {
      $fanme = "fetcheddata/$lang-$dataClass.json";
      // $url = 'https://en.moddaker.com/webservice/rest/server.php?moodlewsrestformat=json&wsfunction=local_reports_service_get_users&wstoken=5d67dc5eec6b25617c0e55c00c8a9fd6';
      $curl = curl_init();
      curl_setopt_array($curl, [
        CURLOPT_URL => $url,
        CURLOPT_FOLLOWLOCATION => true,
        CURLOPT_RETURNTRANSFER => true,
        CURLOPT_SSL_VERIFYPEER => 0,
        CURLOPT_HTTPAUTH => CURLAUTH_BASIC,
      ]);
      $json = curl_exec($curl);
      $file = fopen($fanme, 'a+');
      ftruncate($file, 0);
      fwrite($file, '{"lastupdate": ' . time() . ',');
      fwrite($file, '"data": ' . $json . '}');
      fclose($file);
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
      $data = json_decode($json, true);
      $students_data = array_merge($students_data, $data);
      // echo $i.' / '.$start.' / '.$end.' / ';
      print_r(count($students_data));
      echo '---';

      $start = $end + 1;
      if ($end >= $total_count) {
        break;
      }
      // $i++;
    }

    // echo "The last length: "; print_r(count($students_data)); echo '<br>';

    // Save data in a json file:
    $students_json = json_encode($students_data);
    $fanme = "fetcheddata/ar-students.json";
    $file = fopen($fanme, 'a+');
    ftruncate($file, 0);
    fwrite($file, '{"lastupdate": ' . time() . ',');
    fwrite($file, '"data": ' . $students_json . '}');
    fclose($file);
  }

  function getData($fname)
  {
    $json = file_get_contents($fname);
    $data = json_decode($json, true);

    return $data;
  }


  // Ar Students:
  // updateData('', 'students', 'ar');

  
  // En Students:
  // updateData('https://en.moddaker.com/webservice/rest/server.php?moodlewsrestformat=json&wsfunction=local_reports_service_get_users&wstoken=5d67dc5eec6b25617c0e55c00c8a9fd6', 'students', 'en');
  
  // Id Students:
  // updateData('https://id.moddaker.com/webservice/rest/server.php?moodlewsrestformat=json&wsfunction=local_reports_service_get_users&wstoken=e550100be5197a3e25596068c83ab9d2', 'students', 'id');
  
  // Fr Students:
  // updateData('https://fr.moddaker.com/webservice/rest/server.php?moodlewsrestformat=json&wsfunction=local_reports_service_get_users&wstoken=f5a13ccf5b087df6ed67b12afce7dc3a', 'students', 'fr');
  ?>
  <!-- Google Font: Source Sans Pro -->
  <link rel="stylesheet" href="https://fonts.googleapis.com/css?family=Source+Sans+Pro:300,400,400i,700&display=fallback">
  <!-- Font Awesome Icons -->
  <link rel="stylesheet" href="assets/plugins/fontawesome-free/css/all.min.css">
  <!-- overlayScrollbars -->
  <link rel="stylesheet" href="assets/plugins/overlayScrollbars/css/OverlayScrollbars.min.css">
    <!-- DataTables -->
  <link rel="stylesheet" href="assets/plugins/datatables-bs4/css/dataTables.bootstrap4.min.css">
  <link rel="stylesheet" href="assets/plugins/datatables-responsive/css/responsive.bootstrap4.min.css">
  <link rel="stylesheet" href="assets/plugins/datatables-buttons/css/buttons.bootstrap4.min.css">
   <!-- Select2 -->
  <link rel="stylesheet" href="assets/plugins/select2/css/select2.min.css">
  <link rel="stylesheet" href="assets/plugins/select2-bootstrap4-theme/select2-bootstrap4.min.css">
   <!-- SweetAlert2 -->
  <link rel="stylesheet" href="assets/plugins/sweetalert2-theme-bootstrap-4/bootstrap-4.min.css">
  <!-- Toastr -->
  <link rel="stylesheet" href="assets/plugins/toastr/toastr.min.css">
  <!-- dropzonejs -->
  <link rel="stylesheet" href="assets/plugins/dropzone/min/dropzone.min.css">
  <!-- DateTimePicker -->
  <link rel="stylesheet" href="assets/dist/css/jquery.datetimepicker.min.css">
  <!-- iCheck for checkboxes and radio inputs -->
  <link rel="stylesheet" href="assets/plugins/icheck-bootstrap/icheck-bootstrap.min.css">
  <!-- Switch Toggle -->
  <link rel="stylesheet" href="assets/plugins/bootstrap4-toggle/css/bootstrap4-toggle.min.css">
  <!-- Theme style -->
  <link rel="stylesheet" href="assets/dist/css/adminlte.min.css">
  <link rel="stylesheet" href="assets/dist/css/styles.css">
	<script src="assets/plugins/jquery/jquery.min.js"></script>
  <!-- jQuery UI 1.11.4 -->
  <script src="assets/plugins/jquery-ui/jquery-ui.min.js"></script>
 <!-- summernote -->
  <link rel="stylesheet" href="assets/plugins/summernote/summernote-bs4.min.css">
  <link href="https://cdn.jsdelivr.net/npm/summernote@0.8.18/dist/summernote.min.css" rel="stylesheet">
<script src="https://cdn.jsdelivr.net/npm/summernote@0.8.18/dist/summernote.min.js"></script>
<link rel="icon" href="./assets/uploads/logo.ico">
</head>