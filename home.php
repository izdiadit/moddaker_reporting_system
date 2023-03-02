<?php include('db_connect.php') ?>
<?php
$twhere = "";
if ($_SESSION['login_type'] != 1)
  $twhere = "  ";
?>
<!-- Info boxes -->
<div class="col-12">
  <div class="card">
    <div class="card-body">
      <div class="iz-welcomemessage">
        مرحبًا بـ<?php echo $_SESSION['login_name'] ?>!
      </div>
    </div>
  </div>
</div>
<hr>
<?php

$where = "";
if ($_SESSION['login_type'] == 2) {
  $where = " where manager_id = '{$_SESSION['login_id']}' ";
} elseif ($_SESSION['login_type'] == 3) {
  $where = " where concat('[',REPLACE(user_ids,',','],['),']') LIKE '%[{$_SESSION['login_id']}]%' ";
}
$where2 = "";
if ($_SESSION['login_type'] == 2) {
  $where2 = " where p.manager_id = '{$_SESSION['login_id']}' ";
} elseif ($_SESSION['login_type'] == 3) {
  $where2 = " where concat('[',REPLACE(p.user_ids,',','],['),']') LIKE '%[{$_SESSION['login_id']}]%' ";
}
?>

<div class="row">
  <div class="col-md-12">
    <div class="card card-outline">
      <div class="card-body p-0" id="home-cards-group">
        <div class="home-cards-group-row">
          <div class="homeCard" onclick='location.replace("./index.php?page=report_general_view");'>
            <div class="card-img"><i class="fa fa-chart-bar"></i></div>
            <div class="card-title">مُدّكر في أرقام</div>
          </div>
          <div class="homeCard" onclick='location.replace("./index.php?page=report_map");'>
            <div class="card-img"><img src="assets/uploads/moddaker_map.png" alt="صورة الخريطة" width="150px" height="75px"></div>
            <div class="card-title">خريطة مُدّكر</div>
          </div>
        </div>
        <div class="home-cards-group-row">
          <div class="homeCard" onclick='location.replace("./index.php?page=report_academic_status");'>
            <div class="card-img"><i class="fa fa-solid fa-clipboard"></i></div>
            <div class="card-title">الحالة الأكاديمية</div>
          </div>
        </div>
      </div>
    </div>
  </div>
</div>
<script>
  setInterval(myFunc, 1000);

  function myFunc() {
    let d = new Date();
    document.getElementById("demo").innerHTML =
      d.getHours() + ":" +
      d.getMinutes() + ":" +
      d.getSeconds();
  }
</script>