
<!-- Navbar -->
<nav class="main-header navbar navbar-expand navbar-primary navbar-dark " dir="ltr">
  <!-- Left navbar links -->
  <ul class="navbar-nav">
    <?php if (isset($_SESSION['login_id'])) : ?>
      <li class="nav-item">
        <a class="nav-link" data-widget="pushmenu" href="" role="button"><i class="fas fa-bars"></i></a>
      </li>
    <?php endif; ?>
    <li>
      <a class="nav-link text-white header-text" href="./" role="button">
        <large><b>
            <!-- <?php echo $_SESSION['system']['name'] ?> -->
            <img src="" alt="">
          </b></large>
      </a>
    </li>
  </ul>

  <ul class="navbar-nav ml-auto">

    <li class="nav-item" id="notif">
      <div id="notif_refrech">
      <?php
      // $_SESSION['notification_count'] = 0;
      
      $qry = $conn->query("SELECT COUNT(*) as c FROM notifications WHERE recipient = {$_SESSION['login_id']} and unread = true");
      while ($row = $qry->fetch_assoc()) :
        $_SESSION['notification_count'] = $row['c'];

      endwhile;
      ?>
      <span class="fa-stack fa-1x" id="notification_span" data-count="<?php echo $_SESSION['notification_count']; ?>">
      </span>
      </div>
      <a class="nav-link" href="#" role="button" id="notification">
          <i class="fa  fa-stack-2x"></i>
          <i class="fas fa-bell"></i>
        </a>
    </li>
    <li class="nav-item">
      <a class="nav-link" data-widget="fullscreen" href="#" role="button">
        <i class="fas fa-expand-arrows-alt"></i>
      </a>
    </li>
    <li class="nav-item dropdown">
      <a class="nav-link" data-toggle="dropdown" aria-expanded="true" href="javascript:void(0)">
        <span>
          <div class="d-felx badge-pill">
            <span class="fa fa-user mr-2"></span>
            <!-- <span><b><?php echo ucwords($_SESSION['login_firstname']) ?></b></span> -->
            <span class="fa fa-angle-down ml-2"></span>
          </div>
        </span>
      </a>
      <div class="dropdown-menu" aria-labelledby="account_settings" style="left: -2.5em;">
        <a class="dropdown-item" href="javascript:void(0)" id="manage_account"><i class="fa fa-cog"></i> Manage Account</a>
        <a class="dropdown-item" href="ajax.php?action=logout"><i class="fa fa-power-off"></i> Logout</a>
      </div>
    </li>
  </ul>
</nav>
<!-- /.navbar -->
<script>
  $('#manage_account').click(function() {
    uni_modal('Manage Account', 'manage_user.php?id=<?php echo $_SESSION['login_id'] ?>')
  })
  $('#notification').click(function() {
    uni_modal('Notifications', 'notifications.php?id=<?php echo $_SESSION['login_id'] ?>')
  })

  var $notifs = $("#notif_refrech");
  
  setInterval(function() {
    $notifs.load("index.php #notif_refrech");
  }, 3000);
</script>
<style>
  .fa-stack[data-count]:after {
    position: absolute;
    right: 48%;
    top: 1%;
    content: attr(data-count);
    font-size: 70%;
    padding: .6em;
    border-radius: 999px;
    line-height: .75em;
    color: white;
    background: rgba(255, 0, 0, .85);
    text-align: center;
    min-width: 2em;
    font-weight: bold;
  }

  #notif{
    display: flex;
    flex-direction: row-reverse;
    align-items: flex-start ;
  }
a#notification{
  padding-right: 0px;
  padding-left: 5px;
}
</style>