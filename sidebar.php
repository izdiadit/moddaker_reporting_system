  <aside class="main-sidebar sidebar-dark-primary elevation-4">
    <div class="dropdown">
      <a href="./" class="brand-link">
        <!-- <?php if ($_SESSION['login_type'] == 1) : ?>
        <h3 class="text-center p-0 m-0"><b>ADMIN</b></h3>
        <?php else : ?>
        <h3 class="text-center p-0 m-0"><b>USER</b></h3>
        <?php endif; ?> -->
        <img style="width: 40px; height: 40px; border-radius:10px; position: sticky; left:10px;" src="assets/uploads/profile.jpg" alt="">
      </a>
    </div>
    <div class="sidebar pb-4 mb-4">
      <nav class="mt-2">
        <ul class="nav nav-pills nav-sidebar flex-column nav-flat" data-widget="treeview" role="menu" data-accordion="false">
          <li class="nav-item dropdown">
            <a href="./" class="nav-link nav-home">
              <i class="nav-icon fas fa-tachometer-alt"></i>
              <p>
                الرئيسية
              </p>
            </a>
          </li>
          <li class="nav-item">
            <a href="#" class="nav-link nav-reports">
              <i class="fas fa-th-list nav-icon"></i>
              <p>التقارير</p>
              <i class="right fas fa-angle-left"></i>
            </a>
            <ul class="nav nav-treeview">
              <li class="nav-item">
                <a href="./index.php?page=report_general_view" class="nav-link nav-report_general_view tree-item">
                  <i class="fas fa-angle-right nav-icon"></i>
                  <p>مدكر في أرقام</p>
                </a>
              </li>
              <li class="nav-item">
                <a href="./index.php?page=report_map" class="nav-link nav-report_map tree-item">
                  <i class="fas fa-angle-right nav-icon"></i>
                  <p>خريطة مدكّر</p>
                </a>
              </li>
              <li class="nav-item">
                <a href="./index.php?page=report_academic_status" class="nav-link nav-report_academic_status tree-item">
                  <i class="fas fa-angle-right nav-icon"></i>
                  <p>الحالة الأكاديمية</p>
                </a>
              </li>
              <?php if ($_SESSION['login_type'] == 1 || $_SESSION['login_type'] == 2) : ?>
                <li class="nav-item">
                  <a href="./index.php?page=report_moodle_info" class="nav-link nav-report_moodle_info tree-item">
                    <i class="fas fa-angle-right nav-icon"></i>
                    <p>تقرير معلومات المنصة</p>
                  </a>
                </li>
                <li class="nav-item">
                  <a href="./index.php?page=report_students" class="nav-link nav-report_students tree-item">
                    <i class="fas fa-angle-right nav-icon"></i>
                    <p>الطلاب</p>
                  </a>
                </li>
                <li class="nav-item">
                  <a href="./index.php?page=report_st_affairs" class="nav-link nav-report_st_affairs tree-item">
                    <i class="fas fa-angle-right nav-icon"></i>
                    <p>تقرير شؤون الدارسين</p>
                  </a>
                </li>
                <?php endif; ?>
            </ul>
          </li>
        <?php if ($_SESSION['login_type'] == 1) : ?>
          <li class="nav-item">
            <a href="#" class="nav-link nav-edit_user">
              <i class="nav-icon fas fa-users"></i>
              <p>
                إدارة المستخدمين
                <i class="right fas fa-angle-left"></i>
              </p>
            </a>
            <ul class="nav nav-treeview">
              <li class="nav-item">
                <a href="./index.php?page=new_user" class="nav-link nav-new_user tree-item">
                  <i class="fas fa-angle-right nav-icon"></i>
                  <p>مستخدم جديد</p>
                </a>
              </li>
              <li class="nav-item">
                <a href="./index.php?page=user_list" class="nav-link nav-user_list tree-item">
                  <i class="fas fa-angle-right nav-icon"></i>
                  <p>قائمة المستخدمين</p>
                </a>
              </li>
            </ul>
          </li>
        <?php endif; ?>
        </ul>
      </nav>
    </div>
  </aside>
  <script>
    $(document).ready(function() {
      var page = '<?php echo isset($_GET['page']) ? $_GET['page'] : 'home' ?>';
      var s = '<?php echo isset($_GET['s']) ? $_GET['s'] : '' ?>';
      if (s != '')
        page = page + '_' + s;
      if ($('.nav-link.nav-' + page).length > 0) {
        $('.nav-link.nav-' + page).addClass('active')
        if ($('.nav-link.nav-' + page).hasClass('tree-item') == true) {
          $('.nav-link.nav-' + page).closest('.nav-treeview').siblings('a').addClass('active')
          $('.nav-link.nav-' + page).closest('.nav-treeview').parent().addClass('menu-open')
        }
        if ($('.nav-link.nav-' + page).hasClass('nav-is-tree') == true) {
          $('.nav-link.nav-' + page).parent().addClass('menu-open')
        }
      }

    })
  </script>