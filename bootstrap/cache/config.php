<?php return array (
  'adminlte' => 
  array (
    'title' => 'Skuul',
    'title_prefix' => '',
    'title_postfix' => '',
    'use_ico_only' => true,
    'use_full_favicon' => false,
    'google_fonts' => 
    array (
      'allowed' => true,
    ),
    'logo' => 'Skuul',
    'logo_img' => 'img/logo/logo.jpg',
    'logo_img_class' => 'brand-image img-circle elevation-3',
    'logo_img_xl' => NULL,
    'logo_img_xl_class' => 'brand-image-xs',
    'logo_img_alt' => 'Skuul',
    'preloader' => 
    array (
      'enabled' => true,
      'img' => 
      array (
        'path' => 'img/logo/logo.jpg',
        'alt' => 'AdminLTE Preloader Image',
        'effect' => 'animation__shake',
        'width' => 60,
        'height' => 60,
      ),
    ),
    'usermenu_enabled' => true,
    'usermenu_header' => false,
    'usermenu_header_class' => 'bg-primary',
    'usermenu_image' => false,
    'usermenu_desc' => false,
    'usermenu_profile_url' => false,
    'layout_topnav' => NULL,
    'layout_boxed' => NULL,
    'layout_fixed_sidebar' => NULL,
    'layout_fixed_navbar' => NULL,
    'layout_fixed_footer' => NULL,
    'layout_dark_mode' => NULL,
    'classes_auth_card' => 'card-outline card-primary',
    'classes_auth_header' => '',
    'classes_auth_body' => '',
    'classes_auth_footer' => '',
    'classes_auth_icon' => '',
    'classes_auth_btn' => 'btn-flat btn-primary',
    'classes_body' => '',
    'classes_brand' => '',
    'classes_brand_text' => '',
    'classes_content_wrapper' => '',
    'classes_content_header' => '',
    'classes_content' => '',
    'classes_sidebar' => 'sidebar-dark-primary elevation-4',
    'classes_sidebar_nav' => '',
    'classes_topnav' => 'navbar-white navbar-light',
    'classes_topnav_nav' => 'navbar-expand',
    'classes_topnav_container' => 'container',
    'sidebar_mini' => 'lg',
    'sidebar_collapse' => false,
    'sidebar_collapse_auto_size' => false,
    'sidebar_collapse_remember' => false,
    'sidebar_collapse_remember_no_transition' => true,
    'sidebar_scrollbar_theme' => 'os-theme-light',
    'sidebar_scrollbar_auto_hide' => 'l',
    'sidebar_nav_accordion' => true,
    'sidebar_nav_animation_speed' => 300,
    'right_sidebar' => false,
    'right_sidebar_icon' => 'fas fa-cogs',
    'right_sidebar_theme' => 'dark',
    'right_sidebar_slide' => true,
    'right_sidebar_push' => true,
    'right_sidebar_scrollbar_theme' => 'os-theme-light',
    'right_sidebar_scrollbar_auto_hide' => 'l',
    'use_route_url' => false,
    'dashboard_url' => 'home',
    'logout_url' => 'logout',
    'login_url' => 'login',
    'register_url' => 'register',
    'password_reset_url' => 'password/reset',
    'password_email_url' => 'password/email',
    'profile_url' => false,
    'enabled_laravel_mix' => false,
    'laravel_mix_css_path' => 'css/app.css',
    'laravel_mix_js_path' => 'js/app.js',
    'menu' => 
    array (
      0 => 
      array (
        'type' => 'fullscreen-widget',
        'topnav_right' => true,
      ),
      1 => 
      array (
        'type' => 'darkmode-widget',
        'topnav_right' => true,
      ),
      2 => 
      array (
        'type' => 'sidebar-menu-search',
        'text' => 'search',
      ),
      3 => 
      array (
        'type' => 'sidebar-menu-item',
        'icon' => 'fas fa-tachometer-alt',
        'text' => 'Dashboard',
        'route' => 'dashboard',
      ),
      4 => 
      array (
        'header' => 'Manage Profile',
      ),
      5 => 
      array (
        'type' => 'sidebar-menu-item',
        'icon' => 'fas fa-user',
        'text' => 'User Profile',
        'route' => 'profile.show',
      ),
      6 => 
      array (
        'header' => 'Multi Schools Management',
        'can' => 'header-schools',
      ),
      7 => 
      array (
        'type' => 'sidebar-menu-item',
        'text' => 'Schools',
        'icon' => 'fas fa-school',
        'can' => 'menu-school',
        'submenu' => 
        array (
          0 => 
          array (
            'type' => 'sidebar-menu-item',
            'text' => 'View Schools',
            'route' => 'schools.index',
            'can' => 'read school',
          ),
          1 => 
          array (
            'type' => 'sidebar-menu-item',
            'text' => 'Create School',
            'route' => 'schools.create',
            'can' => 'create school',
          ),
        ),
      ),
      8 => 
      array (
        'header' => 'Administration',
        'can' => 'header-administrate',
      ),
      9 => 
      array (
        'type' => 'sidebar-menu-item',
        'icon' => 'fas fa-cog',
        'text' => 'School Settings',
        'route' => 'schools.settings',
        'can' => 'manage school settings',
      ),
      10 => 
      array (
        'type' => 'sidebar-menu-item',
        'text' => 'Classes',
        'icon' => 'fas fa-chalkboard',
        'can' => 'menu-class',
        'submenu' => 
        array (
          0 => 
          array (
            'type' => 'sidebar-menu-item',
            'text' => 'View Class Groups',
            'route' => 'class-groups.index',
            'can' => 'read class group',
          ),
          1 => 
          array (
            'type' => 'sidebar-menu-item',
            'text' => 'Create Class Group',
            'route' => 'class-groups.create',
            'can' => 'create class group',
          ),
          2 => 
          array (
            'type' => 'sidebar-menu-item',
            'text' => 'View Classes',
            'route' => 'classes.index',
            'can' => 'read class',
          ),
          3 => 
          array (
            'type' => 'sidebar-menu-item',
            'text' => 'Create Class',
            'route' => 'classes.create',
            'can' => 'create class',
          ),
        ),
      ),
      11 => 
      array (
        'type' => 'sidebar-menu-item',
        'text' => 'sections',
        'icon' => 'fas fa-landmark',
        'can' => 'menu-section',
        'submenu' => 
        array (
          0 => 
          array (
            'type' => 'sidebar-menu-item',
            'text' => 'View sections',
            'route' => 'sections.index',
            'can' => 'read section',
          ),
          1 => 
          array (
            'type' => 'sidebar-menu-item',
            'text' => 'Create section',
            'route' => 'sections.create',
            'can' => 'create section',
          ),
        ),
      ),
      12 => 
      array (
        'type' => 'sidebar-menu-item',
        'text' => 'Students',
        'icon' => 'fas fa-user',
        'can' => 'menu-student',
        'submenu' => 
        array (
          0 => 
          array (
            'type' => 'sidebar-menu-item',
            'text' => 'View students',
            'route' => 'students.index',
            'can' => 'read student',
          ),
          1 => 
          array (
            'type' => 'sidebar-menu-item',
            'text' => 'Create student',
            'route' => 'students.create',
            'can' => 'create student',
          ),
          2 => 
          array (
            'type' => 'sidebar-menu-item',
            'text' => 'Promote students',
            'route' => 'students.promote',
            'can' => 'promote student',
          ),
          3 => 
          array (
            'type' => 'sidebar-menu-item',
            'text' => 'Manage promotions',
            'route' => 'students.promotions',
            'can' => 'read promotion',
          ),
          4 => 
          array (
            'type' => 'sidebar-menu-item',
            'text' => 'Graduate students',
            'route' => 'students.graduate',
            'can' => 'graduate student',
          ),
          5 => 
          array (
            'type' => 'sidebar-menu-item',
            'text' => 'Manage graduations',
            'route' => 'students.graduations',
            'can' => 'view graduations',
          ),
        ),
      ),
      13 => 
      array (
        'type' => 'sidebar-menu-item',
        'text' => 'Teachers',
        'icon' => 'fas fa-user',
        'can' => 'menu-teacher',
        'submenu' => 
        array (
          0 => 
          array (
            'type' => 'sidebar-menu-item',
            'text' => 'View teachers',
            'route' => 'teachers.index',
            'can' => 'read teacher',
          ),
          1 => 
          array (
            'type' => 'sidebar-menu-item',
            'text' => 'Create teacher',
            'route' => 'teachers.create',
            'can' => 'create teacher',
          ),
        ),
      ),
      14 => 
      array (
        'type' => 'sidebar-menu-item',
        'text' => 'Parents',
        'icon' => 'fas fa-user',
        'can' => 'menu-parent',
        'submenu' => 
        array (
          0 => 
          array (
            'type' => 'sidebar-menu-item',
            'text' => 'View parents',
            'route' => 'parents.index',
            'can' => 'read parent',
          ),
          1 => 
          array (
            'type' => 'sidebar-menu-item',
            'text' => 'Create parent',
            'route' => 'parents.create',
            'can' => 'create parent',
          ),
        ),
      ),
      15 => 
      array (
        'type' => 'sidebar-menu-item',
        'text' => 'Admins',
        'icon' => 'fas fa-user',
        'can' => 'menu-admin',
        'submenu' => 
        array (
          0 => 
          array (
            'type' => 'sidebar-menu-item',
            'text' => 'View admins',
            'route' => 'admins.index',
            'can' => 'read admin',
          ),
          1 => 
          array (
            'type' => 'sidebar-menu-item',
            'text' => 'Create admin',
            'route' => 'admins.create',
            'can' => 'create admin',
          ),
        ),
      ),
      16 => 
      array (
        'type' => 'sidebar-menu-item',
        'text' => 'Academic years',
        'icon' => 'fas fa-calendar',
        'can' => 'menu-academic-year',
        'submenu' => 
        array (
          0 => 
          array (
            'type' => 'sidebar-menu-item',
            'text' => 'View academic years',
            'route' => 'academic-years.index',
            'can' => 'read academic year',
          ),
          1 => 
          array (
            'type' => 'sidebar-menu-item',
            'text' => 'Create academic year',
            'route' => 'academic-years.create',
            'can' => 'create academic year',
          ),
        ),
      ),
      17 => 
      array (
        'type' => 'sidebar-menu-item',
        'text' => 'Semesters',
        'icon' => 'fas fa-clock',
        'can' => 'menu-subject',
        'submenu' => 
        array (
          0 => 
          array (
            'type' => 'sidebar-menu-item',
            'text' => 'View semesters',
            'route' => 'semesters.index',
            'can' => 'read semester',
          ),
          1 => 
          array (
            'type' => 'sidebar-menu-item',
            'text' => 'Create semester',
            'route' => 'semesters.create',
            'can' => 'create semester',
          ),
        ),
      ),
      18 => 
      array (
        'type' => 'sidebar-menu-item',
        'text' => 'Subjects',
        'icon' => 'fas fa-lightbulb',
        'can' => 'menu-subject',
        'submenu' => 
        array (
          0 => 
          array (
            'type' => 'sidebar-menu-item',
            'text' => 'View subjects',
            'route' => 'subjects.index',
            'can' => 'read subject',
          ),
          1 => 
          array (
            'type' => 'sidebar-menu-item',
            'text' => 'Create subject',
            'route' => 'subjects.create',
            'can' => 'create subject',
          ),
        ),
      ),
      19 => 
      array (
        'header' => 'Academics',
        'can' => 'header-academics',
      ),
      20 => 
      array (
        'type' => 'sidebar-menu-item',
        'text' => 'Notices',
        'icon' => 'fas fa-bell',
        'can' => 'menu-notice',
        'submenu' => 
        array (
          0 => 
          array (
            'type' => 'sidebar-menu-item',
            'text' => 'View notices',
            'route' => 'notices.index',
            'can' => 'read notice',
          ),
          1 => 
          array (
            'type' => 'sidebar-menu-item',
            'text' => 'Create notice',
            'route' => 'notices.create',
            'can' => 'create notice',
          ),
        ),
      ),
      21 => 
      array (
        'type' => 'sidebar-menu-item',
        'text' => 'Syllabi',
        'icon' => 'fas fa-list-alt',
        'can' => 'menu-syllabus',
        'submenu' => 
        array (
          0 => 
          array (
            'type' => 'sidebar-menu-item',
            'text' => 'View Syllabi',
            'route' => 'syllabi.index',
            'can' => 'read syllabus',
          ),
          1 => 
          array (
            'type' => 'sidebar-menu-item',
            'text' => 'Create Syllabus',
            'route' => 'syllabi.create',
            'can' => 'create syllabus',
          ),
        ),
      ),
      22 => 
      array (
        'type' => 'sidebar-menu-item',
        'text' => 'Timetables',
        'icon' => 'fas fa-tasks',
        'can' => 'menu-timetable',
        'submenu' => 
        array (
          0 => 
          array (
            'type' => 'sidebar-menu-item',
            'text' => 'View Timetables',
            'route' => 'timetables.index',
            'can' => 'read timetable',
          ),
          1 => 
          array (
            'type' => 'sidebar-menu-item',
            'text' => 'Create Timetable',
            'route' => 'timetables.create',
            'can' => 'create timetable',
          ),
        ),
      ),
      23 => 
      array (
        'type' => 'sidebar-menu-item',
        'text' => 'Exams',
        'icon' => 'fas fa-book-open',
        'can' => 'menu-exam',
        'submenu' => 
        array (
          0 => 
          array (
            'type' => 'sidebar-menu-item',
            'text' => 'View Exams',
            'route' => 'exams.index',
            'can' => 'read exam',
          ),
          1 => 
          array (
            'type' => 'sidebar-menu-item',
            'text' => 'Create Exam',
            'route' => 'exams.create',
            'can' => 'create exam',
          ),
          2 => 
          array (
            'type' => 'sidebar-menu-item',
            'text' => 'Manage Exam records',
            'route' => 'exam-records.index',
            'can' => 'update exam record',
          ),
          3 => 
          array (
            'type' => 'sidebar-menu-item',
            'text' => 'Exam tabulation sheet',
            'route' => 'exams.tabulation',
            'can' => 'read exam',
          ),
          4 => 
          array (
            'type' => 'sidebar-menu-item',
            'text' => 'Result tabulation sheet',
            'route' => 'exams.result-tabulation',
            'can' => 'read exam',
          ),
          5 => 
          array (
            'type' => 'sidebar-menu-item',
            'text' => 'Result Checker',
            'route' => 'exams.result-checker',
            'can' => 'check result',
          ),
        ),
      ),
      24 => 
      array (
        'type' => 'sidebar-menu-item',
        'text' => 'Grade systems',
        'icon' => 'fa fa-graduation-cap',
        'can' => 'menu-grade-system',
        'submenu' => 
        array (
          0 => 
          array (
            'type' => 'sidebar-menu-item',
            'text' => 'View Grades',
            'route' => 'grade-systems.index',
            'can' => 'read grade system',
          ),
          1 => 
          array (
            'type' => 'sidebar-menu-item',
            'text' => 'Create Grades',
            'route' => 'grade-systems.create',
            'can' => 'create grade system',
          ),
        ),
      ),
    ),
    'filters' => 
    array (
      0 => 'JeroenNoten\\LaravelAdminLte\\Menu\\Filters\\GateFilter',
      1 => 'JeroenNoten\\LaravelAdminLte\\Menu\\Filters\\HrefFilter',
      2 => 'JeroenNoten\\LaravelAdminLte\\Menu\\Filters\\SearchFilter',
      3 => 'JeroenNoten\\LaravelAdminLte\\Menu\\Filters\\ActiveFilter',
      4 => 'JeroenNoten\\LaravelAdminLte\\Menu\\Filters\\ClassesFilter',
      5 => 'JeroenNoten\\LaravelAdminLte\\Menu\\Filters\\LangFilter',
      6 => 'JeroenNoten\\LaravelAdminLte\\Menu\\Filters\\DataFilter',
    ),
    'plugins' => 
    array (
      'DateRangePicker' => 
      array (
        'active' => false,
        'files' => 
        array (
          0 => 
          array (
            'type' => 'js',
            'asset' => true,
            'location' => 'vendor/moment/moment.min.js',
          ),
          1 => 
          array (
            'type' => 'js',
            'asset' => true,
            'location' => 'vendor/daterangepicker/daterangepicker.js',
          ),
          2 => 
          array (
            'type' => 'css',
            'asset' => true,
            'location' => 'vendor/daterangepicker/daterangepicker.css',
          ),
        ),
      ),
      'Datatables' => 
      array (
        'active' => true,
        'files' => 
        array (
          0 => 
          array (
            'type' => 'js',
            'asset' => true,
            'location' => 'vendor/datatables/js/jquery.dataTables.min.js',
          ),
          1 => 
          array (
            'type' => 'js',
            'asset' => true,
            'location' => 'vendor/datatables/js/dataTables.bootstrap4.min.js',
          ),
          2 => 
          array (
            'type' => 'css',
            'asset' => true,
            'location' => 'vendor/datatables/css/dataTables.bootstrap4.min.css',
          ),
        ),
      ),
      'DatatablesPlugins' => 
      array (
        'active' => true,
        'files' => 
        array (
          0 => 
          array (
            'type' => 'js',
            'asset' => true,
            'location' => 'vendor/datatables-plugins/buttons/js/dataTables.buttons.min.js',
          ),
          1 => 
          array (
            'type' => 'js',
            'asset' => true,
            'location' => 'vendor/datatables-plugins/buttons/js/buttons.bootstrap4.min.js',
          ),
          2 => 
          array (
            'type' => 'js',
            'asset' => true,
            'location' => 'vendor/datatables-plugins/buttons/js/buttons.html5.min.js',
          ),
          3 => 
          array (
            'type' => 'js',
            'asset' => true,
            'location' => 'vendor/datatables-plugins/buttons/js/buttons.print.min.js',
          ),
          4 => 
          array (
            'type' => 'js',
            'asset' => true,
            'location' => 'vendor/datatables-plugins/jszip/jszip.min.js',
          ),
          5 => 
          array (
            'type' => 'js',
            'asset' => true,
            'location' => 'vendor/datatables-plugins/pdfmake/pdfmake.min.js',
          ),
          6 => 
          array (
            'type' => 'js',
            'asset' => true,
            'location' => 'vendor/datatables-plugins/pdfmake/vfs_fonts.js',
          ),
          7 => 
          array (
            'type' => 'css',
            'asset' => true,
            'location' => 'vendor/datatables-plugins/buttons/css/buttons.bootstrap4.min.css',
          ),
        ),
      ),
      'TempusDominusBs4' => 
      array (
        'active' => true,
        'files' => 
        array (
          0 => 
          array (
            'type' => 'js',
            'asset' => true,
            'location' => 'vendor/moment/moment.min.js',
          ),
          1 => 
          array (
            'type' => 'js',
            'asset' => true,
            'location' => 'vendor/tempusdominus-bootstrap-4/js/tempusdominus-bootstrap-4.min.js',
          ),
          2 => 
          array (
            'type' => 'css',
            'asset' => true,
            'location' => 'vendor/tempusdominus-bootstrap-4/css/tempusdominus-bootstrap-4.min.css',
          ),
        ),
      ),
      'Select2' => 
      array (
        'active' => true,
        'files' => 
        array (
          0 => 
          array (
            'type' => 'js',
            'asset' => true,
            'location' => 'vendor/select2/js/select2.full.min.js',
          ),
          1 => 
          array (
            'type' => 'css',
            'asset' => true,
            'location' => 'vendor/select2/css/select2.min.css',
          ),
          2 => 
          array (
            'type' => 'css',
            'asset' => true,
            'location' => 'vendor/select2-bootstrap4-theme/select2-bootstrap4.min.css',
          ),
        ),
      ),
      'Chartjs' => 
      array (
        'active' => false,
        'files' => 
        array (
          0 => 
          array (
            'type' => 'js',
            'asset' => false,
            'location' => '//cdnjs.cloudflare.com/ajax/libs/Chart.js/2.7.0/Chart.bundle.min.js',
          ),
        ),
      ),
      'Sweetalert2' => 
      array (
        'active' => false,
        'files' => 
        array (
          0 => 
          array (
            'type' => 'js',
            'asset' => false,
            'location' => '//cdn.jsdelivr.net/npm/sweetalert2@8',
          ),
        ),
      ),
      'Pace' => 
      array (
        'active' => false,
        'files' => 
        array (
          0 => 
          array (
            'type' => 'css',
            'asset' => false,
            'location' => '//cdnjs.cloudflare.com/ajax/libs/pace/1.0.2/themes/blue/pace-theme-center-radar.min.css',
          ),
          1 => 
          array (
            'type' => 'js',
            'asset' => false,
            'location' => '//cdnjs.cloudflare.com/ajax/libs/pace/1.0.2/pace.min.js',
          ),
        ),
      ),
      'BsCustomFileInput' => 
      array (
        'active' => true,
        'files' => 
        array (
          0 => 
          array (
            'type' => 'js',
            'asset' => true,
            'location' => 'vendor/bs-custom-file-input/bs-custom-file-input.min.js',
          ),
        ),
      ),
    ),
    'iframe' => 
    array (
      'default_tab' => 
      array (
        'url' => NULL,
        'title' => NULL,
      ),
      'buttons' => 
      array (
        'close' => true,
        'close_all' => true,
        'close_all_other' => true,
        'scroll_left' => true,
        'scroll_right' => true,
        'fullscreen' => true,
      ),
      'options' => 
      array (
        'loading_screen' => 1000,
        'auto_show_new_tab' => true,
        'use_navbar_items' => true,
      ),
    ),
    'livewire' => true,
  ),
  'app' => 
  array (
    'name' => 'Skuul',
    'logo' => 'img/logo/logo.jpg',
    'env' => 'production',
    'debug' => false,
    'url' => 'http://localhost',
    'asset_url' => NULL,
    'timezone' => 'UTC',
    'locale' => 'en',
    'fallback_locale' => 'en',
    'faker_locale' => 'en_US',
    'key' => '',
    'cipher' => 'AES-256-CBC',
    'providers' => 
    array (
      0 => 'Illuminate\\Auth\\AuthServiceProvider',
      1 => 'Illuminate\\Broadcasting\\BroadcastServiceProvider',
      2 => 'Illuminate\\Bus\\BusServiceProvider',
      3 => 'Illuminate\\Cache\\CacheServiceProvider',
      4 => 'Illuminate\\Foundation\\Providers\\ConsoleSupportServiceProvider',
      5 => 'Illuminate\\Cookie\\CookieServiceProvider',
      6 => 'Illuminate\\Database\\DatabaseServiceProvider',
      7 => 'Illuminate\\Encryption\\EncryptionServiceProvider',
      8 => 'Illuminate\\Filesystem\\FilesystemServiceProvider',
      9 => 'Illuminate\\Foundation\\Providers\\FoundationServiceProvider',
      10 => 'Illuminate\\Hashing\\HashServiceProvider',
      11 => 'Illuminate\\Mail\\MailServiceProvider',
      12 => 'Illuminate\\Notifications\\NotificationServiceProvider',
      13 => 'Illuminate\\Pagination\\PaginationServiceProvider',
      14 => 'Illuminate\\Pipeline\\PipelineServiceProvider',
      15 => 'Illuminate\\Queue\\QueueServiceProvider',
      16 => 'Illuminate\\Redis\\RedisServiceProvider',
      17 => 'Illuminate\\Auth\\Passwords\\PasswordResetServiceProvider',
      18 => 'Illuminate\\Session\\SessionServiceProvider',
      19 => 'Illuminate\\Translation\\TranslationServiceProvider',
      20 => 'Illuminate\\Validation\\ValidationServiceProvider',
      21 => 'Illuminate\\View\\ViewServiceProvider',
      22 => 'App\\Providers\\AppServiceProvider',
      23 => 'App\\Providers\\AuthServiceProvider',
      24 => 'App\\Providers\\EventServiceProvider',
      25 => 'App\\Providers\\RouteServiceProvider',
      26 => 'App\\Providers\\FortifyServiceProvider',
      27 => 'App\\Providers\\JetstreamServiceProvider',
    ),
    'aliases' => 
    array (
      'App' => 'Illuminate\\Support\\Facades\\App',
      'Arr' => 'Illuminate\\Support\\Arr',
      'Artisan' => 'Illuminate\\Support\\Facades\\Artisan',
      'Auth' => 'Illuminate\\Support\\Facades\\Auth',
      'Blade' => 'Illuminate\\Support\\Facades\\Blade',
      'Broadcast' => 'Illuminate\\Support\\Facades\\Broadcast',
      'Bus' => 'Illuminate\\Support\\Facades\\Bus',
      'Cache' => 'Illuminate\\Support\\Facades\\Cache',
      'Config' => 'Illuminate\\Support\\Facades\\Config',
      'Cookie' => 'Illuminate\\Support\\Facades\\Cookie',
      'Crypt' => 'Illuminate\\Support\\Facades\\Crypt',
      'Date' => 'Illuminate\\Support\\Facades\\Date',
      'DB' => 'Illuminate\\Support\\Facades\\DB',
      'Eloquent' => 'Illuminate\\Database\\Eloquent\\Model',
      'Event' => 'Illuminate\\Support\\Facades\\Event',
      'File' => 'Illuminate\\Support\\Facades\\File',
      'Gate' => 'Illuminate\\Support\\Facades\\Gate',
      'Hash' => 'Illuminate\\Support\\Facades\\Hash',
      'Http' => 'Illuminate\\Support\\Facades\\Http',
      'Lang' => 'Illuminate\\Support\\Facades\\Lang',
      'Log' => 'Illuminate\\Support\\Facades\\Log',
      'Mail' => 'Illuminate\\Support\\Facades\\Mail',
      'Notification' => 'Illuminate\\Support\\Facades\\Notification',
      'Password' => 'Illuminate\\Support\\Facades\\Password',
      'Queue' => 'Illuminate\\Support\\Facades\\Queue',
      'RateLimiter' => 'Illuminate\\Support\\Facades\\RateLimiter',
      'Redirect' => 'Illuminate\\Support\\Facades\\Redirect',
      'Request' => 'Illuminate\\Support\\Facades\\Request',
      'Response' => 'Illuminate\\Support\\Facades\\Response',
      'Route' => 'Illuminate\\Support\\Facades\\Route',
      'Schema' => 'Illuminate\\Support\\Facades\\Schema',
      'Session' => 'Illuminate\\Support\\Facades\\Session',
      'Storage' => 'Illuminate\\Support\\Facades\\Storage',
      'Str' => 'Illuminate\\Support\\Str',
      'URL' => 'Illuminate\\Support\\Facades\\URL',
      'Validator' => 'Illuminate\\Support\\Facades\\Validator',
      'View' => 'Illuminate\\Support\\Facades\\View',
    ),
  ),
  'auth' => 
  array (
    'defaults' => 
    array (
      'guard' => 'web',
      'passwords' => 'users',
    ),
    'guards' => 
    array (
      'web' => 
      array (
        'driver' => 'session',
        'provider' => 'users',
      ),
      'sanctum' => 
      array (
        'driver' => 'sanctum',
        'provider' => NULL,
      ),
    ),
    'providers' => 
    array (
      'users' => 
      array (
        'driver' => 'eloquent',
        'model' => 'App\\Models\\User',
      ),
    ),
    'passwords' => 
    array (
      'users' => 
      array (
        'provider' => 'users',
        'table' => 'password_resets',
        'expire' => 60,
        'throttle' => 60,
      ),
    ),
    'password_timeout' => 10800,
  ),
  'broadcasting' => 
  array (
    'default' => 'log',
    'connections' => 
    array (
      'pusher' => 
      array (
        'driver' => 'pusher',
        'key' => '',
        'secret' => '',
        'app_id' => '',
        'options' => 
        array (
          'cluster' => 'mt1',
          'useTLS' => true,
        ),
        'client_options' => 
        array (
        ),
      ),
      'ably' => 
      array (
        'driver' => 'ably',
        'key' => NULL,
      ),
      'redis' => 
      array (
        'driver' => 'redis',
        'connection' => 'default',
      ),
      'log' => 
      array (
        'driver' => 'log',
      ),
      'null' => 
      array (
        'driver' => 'null',
      ),
    ),
  ),
  'cache' => 
  array (
    'default' => 'file',
    'stores' => 
    array (
      'apc' => 
      array (
        'driver' => 'apc',
      ),
      'array' => 
      array (
        'driver' => 'array',
        'serialize' => false,
      ),
      'database' => 
      array (
        'driver' => 'database',
        'table' => 'cache',
        'connection' => NULL,
        'lock_connection' => NULL,
      ),
      'file' => 
      array (
        'driver' => 'file',
        'path' => '/var/www/html/storage/framework/cache/data',
      ),
      'memcached' => 
      array (
        'driver' => 'memcached',
        'persistent_id' => NULL,
        'sasl' => 
        array (
          0 => NULL,
          1 => NULL,
        ),
        'options' => 
        array (
        ),
        'servers' => 
        array (
          0 => 
          array (
            'host' => '127.0.0.1',
            'port' => 11211,
            'weight' => 100,
          ),
        ),
      ),
      'redis' => 
      array (
        'driver' => 'redis',
        'connection' => 'cache',
        'lock_connection' => 'default',
      ),
      'dynamodb' => 
      array (
        'driver' => 'dynamodb',
        'key' => '',
        'secret' => '',
        'region' => 'us-east-1',
        'table' => 'cache',
        'endpoint' => NULL,
      ),
      'octane' => 
      array (
        'driver' => 'octane',
      ),
    ),
    'prefix' => 'skuul_cache',
  ),
  'cors' => 
  array (
    'paths' => 
    array (
      0 => 'api/*',
      1 => 'sanctum/csrf-cookie',
    ),
    'allowed_methods' => 
    array (
      0 => '*',
    ),
    'allowed_origins' => 
    array (
      0 => '*',
    ),
    'allowed_origins_patterns' => 
    array (
    ),
    'allowed_headers' => 
    array (
      0 => '*',
    ),
    'exposed_headers' => 
    array (
    ),
    'max_age' => 0,
    'supports_credentials' => false,
  ),
  'database' => 
  array (
    'default' => 'mysql',
    'connections' => 
    array (
      'sqlite' => 
      array (
        'driver' => 'sqlite',
        'url' => NULL,
        'database' => 'laravel',
        'prefix' => '',
        'foreign_key_constraints' => true,
      ),
      'mysql' => 
      array (
        'driver' => 'mysql',
        'url' => NULL,
        'host' => 'mysql',
        'port' => '3306',
        'database' => 'laravel',
        'username' => 'root',
        'password' => '',
        'unix_socket' => '',
        'charset' => 'utf8mb4',
        'collation' => 'utf8mb4_unicode_ci',
        'prefix' => '',
        'prefix_indexes' => true,
        'strict' => true,
        'engine' => 'innoDb',
        'options' => 
        array (
        ),
      ),
      'pgsql' => 
      array (
        'driver' => 'pgsql',
        'url' => NULL,
        'host' => 'mysql',
        'port' => '3306',
        'database' => 'laravel',
        'username' => 'root',
        'password' => '',
        'charset' => 'utf8',
        'prefix' => '',
        'prefix_indexes' => true,
        'search_path' => 'public',
        'sslmode' => 'prefer',
      ),
      'sqlsrv' => 
      array (
        'driver' => 'sqlsrv',
        'url' => NULL,
        'host' => 'mysql',
        'port' => '3306',
        'database' => 'laravel',
        'username' => 'root',
        'password' => '',
        'charset' => 'utf8',
        'prefix' => '',
        'prefix_indexes' => true,
      ),
    ),
    'migrations' => 'migrations',
    'redis' => 
    array (
      'client' => 'phpredis',
      'options' => 
      array (
        'cluster' => 'redis',
        'prefix' => 'skuul_database_',
      ),
      'default' => 
      array (
        'url' => NULL,
        'host' => '127.0.0.1',
        'password' => NULL,
        'port' => '6379',
        'database' => '0',
      ),
      'cache' => 
      array (
        'url' => NULL,
        'host' => '127.0.0.1',
        'password' => NULL,
        'port' => '6379',
        'database' => '1',
      ),
    ),
  ),
  'filesystems' => 
  array (
    'default' => 'local',
    'disks' => 
    array (
      'local' => 
      array (
        'driver' => 'local',
        'root' => '/var/www/html/storage/app',
      ),
      'public' => 
      array (
        'driver' => 'local',
        'root' => '/var/www/html/storage/app/public',
        'url' => 'http://localhost/storage',
        'visibility' => 'public',
      ),
      's3' => 
      array (
        'driver' => 's3',
        'key' => '',
        'secret' => '',
        'region' => 'us-east-1',
        'bucket' => '',
        'url' => NULL,
        'endpoint' => NULL,
        'use_path_style_endpoint' => false,
      ),
    ),
    'links' => 
    array (
      '/var/www/html/public/storage' => '/var/www/html/storage/app/public',
    ),
  ),
  'fortify-options' => 
  array (
    'two-factor-authentication' => 
    array (
      'confirmPassword' => true,
    ),
  ),
  'fortify' => 
  array (
    'guard' => 'web',
    'middleware' => 
    array (
      0 => 'web',
    ),
    'auth_middleware' => 'auth',
    'passwords' => 'users',
    'username' => 'email',
    'email' => 'email',
    'views' => true,
    'home' => '/dashboard',
    'prefix' => '',
    'domain' => NULL,
    'limiters' => 
    array (
      'login' => 'login',
      'two-factor' => 'two-factor',
    ),
    'redirects' => 
    array (
      'login' => NULL,
      'logout' => NULL,
      'password-confirmation' => NULL,
      'register' => NULL,
      'email-verification' => NULL,
      'password-reset' => NULL,
    ),
    'features' => 
    array (
      0 => 'reset-passwords',
      1 => 'email-verification',
      2 => 'update-profile-information',
      3 => 'update-passwords',
      4 => 'two-factor-authentication',
    ),
  ),
  'hashing' => 
  array (
    'driver' => 'bcrypt',
    'bcrypt' => 
    array (
      'rounds' => 10,
    ),
    'argon' => 
    array (
      'memory' => 1024,
      'threads' => 2,
      'time' => 2,
    ),
  ),
  'jetstream' => 
  array (
    'stack' => 'livewire',
    'middleware' => 
    array (
      0 => 'web',
    ),
    'features' => 
    array (
      0 => 'profile-photos',
    ),
    'profile_photo_disk' => 'public',
  ),
  'logging' => 
  array (
    'default' => 'stack',
    'deprecations' => NULL,
    'channels' => 
    array (
      'stack' => 
      array (
        'driver' => 'stack',
        'channels' => 
        array (
          0 => 'single',
        ),
        'ignore_exceptions' => false,
      ),
      'single' => 
      array (
        'driver' => 'single',
        'path' => '/var/www/html/storage/logs/laravel.log',
        'level' => 'debug',
      ),
      'daily' => 
      array (
        'driver' => 'daily',
        'path' => '/var/www/html/storage/logs/laravel.log',
        'level' => 'debug',
        'days' => 14,
      ),
      'slack' => 
      array (
        'driver' => 'slack',
        'url' => NULL,
        'username' => 'Laravel Log',
        'emoji' => ':boom:',
        'level' => 'debug',
      ),
      'papertrail' => 
      array (
        'driver' => 'monolog',
        'level' => 'debug',
        'handler' => 'Monolog\\Handler\\SyslogUdpHandler',
        'handler_with' => 
        array (
          'host' => NULL,
          'port' => NULL,
          'connectionString' => 'tls://:',
        ),
      ),
      'stderr' => 
      array (
        'driver' => 'monolog',
        'level' => 'debug',
        'handler' => 'Monolog\\Handler\\StreamHandler',
        'formatter' => NULL,
        'with' => 
        array (
          'stream' => 'php://stderr',
        ),
      ),
      'syslog' => 
      array (
        'driver' => 'syslog',
        'level' => 'debug',
      ),
      'errorlog' => 
      array (
        'driver' => 'errorlog',
        'level' => 'debug',
      ),
      'null' => 
      array (
        'driver' => 'monolog',
        'handler' => 'Monolog\\Handler\\NullHandler',
      ),
      'emergency' => 
      array (
        'path' => '/var/www/html/storage/logs/laravel.log',
      ),
    ),
  ),
  'mail' => 
  array (
    'default' => 'smtp',
    'mailers' => 
    array (
      'smtp' => 
      array (
        'transport' => 'smtp',
        'host' => 'mailhog',
        'port' => '1025',
        'encryption' => NULL,
        'username' => 'laravel',
        'password' => NULL,
        'timeout' => NULL,
      ),
      'ses' => 
      array (
        'transport' => 'ses',
      ),
      'mailgun' => 
      array (
        'transport' => 'mailgun',
      ),
      'postmark' => 
      array (
        'transport' => 'postmark',
      ),
      'sendmail' => 
      array (
        'transport' => 'sendmail',
        'path' => '/usr/sbin/sendmail -t -i',
      ),
      'log' => 
      array (
        'transport' => 'log',
        'channel' => NULL,
      ),
      'array' => 
      array (
        'transport' => 'array',
      ),
      'failover' => 
      array (
        'transport' => 'failover',
        'mailers' => 
        array (
          0 => 'smtp',
          1 => 'log',
        ),
      ),
    ),
    'from' => 
    array (
      'address' => 'contact@Skuul.com',
      'name' => 'Skuul',
    ),
    'markdown' => 
    array (
      'theme' => 'default',
      'paths' => 
      array (
        0 => '/var/www/html/resources/views/vendor/mail',
      ),
    ),
  ),
  'permission' => 
  array (
    'models' => 
    array (
      'permission' => 'Spatie\\Permission\\Models\\Permission',
      'role' => 'Spatie\\Permission\\Models\\Role',
    ),
    'table_names' => 
    array (
      'roles' => 'roles',
      'permissions' => 'permissions',
      'model_has_permissions' => 'model_has_permissions',
      'model_has_roles' => 'model_has_roles',
      'role_has_permissions' => 'role_has_permissions',
    ),
    'column_names' => 
    array (
      'role_pivot_key' => NULL,
      'permission_pivot_key' => NULL,
      'model_morph_key' => 'model_id',
      'team_foreign_key' => 'team_id',
    ),
    'register_permission_check_method' => true,
    'teams' => false,
    'display_permission_in_exception' => false,
    'display_role_in_exception' => false,
    'enable_wildcard_permission' => false,
    'cache' => 
    array (
      'expiration_time' => 
      DateInterval::__set_state(array(
         'y' => 0,
         'm' => 0,
         'd' => 0,
         'h' => 24,
         'i' => 0,
         's' => 0,
         'f' => 0.0,
         'weekday' => 0,
         'weekday_behavior' => 0,
         'first_last_day_of' => 0,
         'invert' => 0,
         'days' => false,
         'special_type' => 0,
         'special_amount' => 0,
         'have_weekday_relative' => 0,
         'have_special_relative' => 0,
      )),
      'key' => 'spatie.permission.cache',
      'store' => 'default',
    ),
  ),
  'queue' => 
  array (
    'default' => 'sync',
    'connections' => 
    array (
      'sync' => 
      array (
        'driver' => 'sync',
      ),
      'database' => 
      array (
        'driver' => 'database',
        'table' => 'jobs',
        'queue' => 'default',
        'retry_after' => 90,
        'after_commit' => false,
      ),
      'beanstalkd' => 
      array (
        'driver' => 'beanstalkd',
        'host' => 'localhost',
        'queue' => 'default',
        'retry_after' => 90,
        'block_for' => 0,
        'after_commit' => false,
      ),
      'sqs' => 
      array (
        'driver' => 'sqs',
        'key' => '',
        'secret' => '',
        'prefix' => 'https://sqs.us-east-1.amazonaws.com/your-account-id',
        'queue' => 'default',
        'suffix' => NULL,
        'region' => 'us-east-1',
        'after_commit' => false,
      ),
      'redis' => 
      array (
        'driver' => 'redis',
        'connection' => 'default',
        'queue' => 'default',
        'retry_after' => 90,
        'block_for' => NULL,
        'after_commit' => false,
      ),
    ),
    'failed' => 
    array (
      'driver' => 'database-uuids',
      'database' => 'mysql',
      'table' => 'failed_jobs',
    ),
  ),
  'sanctum' => 
  array (
    'stateful' => 
    array (
      0 => 'localhost',
      1 => 'localhost:3000',
      2 => '127.0.0.1',
      3 => '127.0.0.1:8000',
      4 => '::1',
      5 => 'localhost',
    ),
    'guard' => 
    array (
      0 => 'web',
    ),
    'expiration' => NULL,
    'middleware' => 
    array (
      'verify_csrf_token' => 'App\\Http\\Middleware\\VerifyCsrfToken',
      'encrypt_cookies' => 'App\\Http\\Middleware\\EncryptCookies',
    ),
  ),
  'services' => 
  array (
    'mailgun' => 
    array (
      'domain' => NULL,
      'secret' => NULL,
      'endpoint' => 'api.mailgun.net',
    ),
    'postmark' => 
    array (
      'token' => NULL,
    ),
    'ses' => 
    array (
      'key' => '',
      'secret' => '',
      'region' => 'us-east-1',
    ),
  ),
  'session' => 
  array (
    'driver' => 'database',
    'lifetime' => '120',
    'expire_on_close' => false,
    'encrypt' => false,
    'files' => '/var/www/html/storage/framework/sessions',
    'connection' => NULL,
    'table' => 'sessions',
    'store' => NULL,
    'lottery' => 
    array (
      0 => 2,
      1 => 100,
    ),
    'cookie' => 'skuul_session',
    'path' => '/',
    'domain' => NULL,
    'secure' => NULL,
    'http_only' => true,
    'same_site' => 'lax',
  ),
  'view' => 
  array (
    'paths' => 
    array (
      0 => '/var/www/html/resources/views',
    ),
    'compiled' => '/var/www/html/storage/framework/views',
  ),
  'world' => 
  array (
    'accepted_locales' => 
    array (
      0 => 'ar',
      1 => 'bn',
      2 => 'br',
      3 => 'de',
      4 => 'en',
      5 => 'es',
      6 => 'fr',
      7 => 'ja',
      8 => 'kr',
      9 => 'nl',
      10 => 'pl',
      11 => 'pt',
      12 => 'ro',
      13 => 'ru',
      14 => 'zh',
    ),
    'modules' => 
    array (
      'states' => true,
      'cities' => true,
      'timezones' => true,
      'currencies' => true,
      'languages' => true,
    ),
    'routes' => false,
    'migrations' => 
    array (
      'countries' => 
      array (
        'table_name' => 'countries',
        'optional_fields' => 
        array (
          'phone_code' => 
          array (
            'required' => true,
            'type' => 'string',
            'length' => 5,
          ),
          'iso3' => 
          array (
            'required' => true,
            'type' => 'string',
            'length' => 3,
          ),
          'native' => 
          array (
            'required' => false,
            'type' => 'string',
          ),
          'region' => 
          array (
            'required' => true,
            'type' => 'string',
          ),
          'subregion' => 
          array (
            'required' => true,
            'type' => 'string',
          ),
          'latitude' => 
          array (
            'required' => false,
            'type' => 'string',
          ),
          'longitude' => 
          array (
            'required' => false,
            'type' => 'string',
          ),
          'emoji' => 
          array (
            'required' => false,
            'type' => 'string',
          ),
          'emojiU' => 
          array (
            'required' => false,
            'type' => 'string',
          ),
        ),
      ),
      'states' => 
      array (
        'table_name' => 'states',
        'optional_fields' => 
        array (
          'country_code' => 
          array (
            'required' => true,
            'type' => 'string',
            'length' => 3,
          ),
          'state_code' => 
          array (
            'required' => false,
            'type' => 'string',
            'length' => 3,
          ),
          'latitude' => 
          array (
            'required' => false,
            'type' => 'string',
          ),
          'longitude' => 
          array (
            'required' => false,
            'type' => 'string',
          ),
        ),
      ),
      'cities' => 
      array (
        'table_name' => 'cities',
        'optional_fields' => 
        array (
          'country_code' => 
          array (
            'required' => true,
            'type' => 'string',
            'length' => 3,
          ),
          'state_code' => 
          array (
            'required' => false,
            'type' => 'string',
            'length' => 3,
          ),
          'latitude' => 
          array (
            'required' => false,
            'type' => 'string',
          ),
          'longitude' => 
          array (
            'required' => false,
            'type' => 'string',
          ),
        ),
      ),
      'timezones' => 
      array (
        'table_name' => 'timezones',
      ),
      'currencies' => 
      array (
        'table_name' => 'currencies',
      ),
      'languages' => 
      array (
        'table_name' => 'languages',
      ),
    ),
  ),
  'debugbar' => 
  array (
    'enabled' => NULL,
    'except' => 
    array (
      0 => 'telescope*',
      1 => 'horizon*',
    ),
    'storage' => 
    array (
      'enabled' => true,
      'driver' => 'file',
      'path' => '/var/www/html/storage/debugbar',
      'connection' => NULL,
      'provider' => '',
      'hostname' => '127.0.0.1',
      'port' => 2304,
    ),
    'editor' => 'phpstorm',
    'remote_sites_path' => '',
    'local_sites_path' => '',
    'include_vendors' => true,
    'capture_ajax' => true,
    'add_ajax_timing' => false,
    'error_handler' => false,
    'clockwork' => false,
    'collectors' => 
    array (
      'phpinfo' => true,
      'messages' => true,
      'time' => true,
      'memory' => true,
      'exceptions' => true,
      'log' => true,
      'db' => true,
      'views' => true,
      'route' => true,
      'auth' => false,
      'gate' => true,
      'session' => true,
      'symfony_request' => true,
      'mail' => true,
      'laravel' => false,
      'events' => false,
      'default_request' => false,
      'logs' => false,
      'files' => false,
      'config' => false,
      'cache' => false,
      'models' => true,
      'livewire' => true,
    ),
    'options' => 
    array (
      'auth' => 
      array (
        'show_name' => true,
      ),
      'db' => 
      array (
        'with_params' => true,
        'backtrace' => true,
        'backtrace_exclude_paths' => 
        array (
        ),
        'timeline' => false,
        'duration_background' => true,
        'explain' => 
        array (
          'enabled' => false,
          'types' => 
          array (
            0 => 'SELECT',
          ),
        ),
        'hints' => false,
        'show_copy' => false,
      ),
      'mail' => 
      array (
        'full_log' => false,
      ),
      'views' => 
      array (
        'timeline' => false,
        'data' => false,
      ),
      'route' => 
      array (
        'label' => true,
      ),
      'logs' => 
      array (
        'file' => NULL,
      ),
      'cache' => 
      array (
        'values' => true,
      ),
    ),
    'inject' => true,
    'route_prefix' => '_debugbar',
    'route_domain' => NULL,
    'theme' => 'auto',
    'debug_backtrace_limit' => 50,
  ),
  'dompdf' => 
  array (
    'show_warnings' => false,
    'public_path' => NULL,
    'convert_entities' => true,
    'options' => 
    array (
      'font_dir' => '/var/www/html/storage/fonts',
      'font_cache' => '/var/www/html/storage/fonts',
      'temp_dir' => '/tmp',
      'chroot' => '/var/www/html',
      'allowed_protocols' => 
      array (
        'file://' => 
        array (
          'rules' => 
          array (
          ),
        ),
        'http://' => 
        array (
          'rules' => 
          array (
          ),
        ),
        'https://' => 
        array (
          'rules' => 
          array (
          ),
        ),
      ),
      'log_output_file' => NULL,
      'enable_font_subsetting' => false,
      'pdf_backend' => 'CPDF',
      'default_media_type' => 'screen',
      'default_paper_size' => 'a4',
      'default_paper_orientation' => 'portrait',
      'default_font' => 'serif',
      'dpi' => 96,
      'enable_php' => false,
      'enable_javascript' => true,
      'enable_remote' => true,
      'font_height_ratio' => 1.1,
      'enable_html5_parser' => true,
    ),
  ),
  'livewire' => 
  array (
    'class_namespace' => 'App\\Http\\Livewire',
    'view_path' => '/var/www/html/resources/views/livewire',
    'layout' => 'layouts.app',
    'asset_url' => NULL,
    'app_url' => NULL,
    'middleware_group' => 'web',
    'temporary_file_upload' => 
    array (
      'disk' => NULL,
      'rules' => NULL,
      'directory' => NULL,
      'middleware' => NULL,
      'preview_mimes' => 
      array (
        0 => 'png',
        1 => 'gif',
        2 => 'bmp',
        3 => 'svg',
        4 => 'wav',
        5 => 'mp4',
        6 => 'mov',
        7 => 'avi',
        8 => 'wmv',
        9 => 'mp3',
        10 => 'm4a',
        11 => 'jpg',
        12 => 'jpeg',
        13 => 'mpga',
        14 => 'webp',
        15 => 'wma',
      ),
      'max_upload_time' => 5,
    ),
    'manifest_path' => NULL,
    'back_button_cache' => false,
    'render_on_redirect' => false,
  ),
  'flare' => 
  array (
    'key' => NULL,
    'flare_middleware' => 
    array (
      0 => 'Spatie\\FlareClient\\FlareMiddleware\\RemoveRequestIp',
      1 => 'Spatie\\FlareClient\\FlareMiddleware\\AddGitInformation',
      2 => 'Spatie\\LaravelIgnition\\FlareMiddleware\\AddNotifierName',
      3 => 'Spatie\\LaravelIgnition\\FlareMiddleware\\AddEnvironmentInformation',
      4 => 'Spatie\\LaravelIgnition\\FlareMiddleware\\AddExceptionInformation',
      5 => 'Spatie\\LaravelIgnition\\FlareMiddleware\\AddDumps',
      'Spatie\\LaravelIgnition\\FlareMiddleware\\AddLogs' => 
      array (
        'maximum_number_of_collected_logs' => 200,
      ),
      'Spatie\\LaravelIgnition\\FlareMiddleware\\AddQueries' => 
      array (
        'maximum_number_of_collected_queries' => 200,
        'report_query_bindings' => true,
      ),
      'Spatie\\LaravelIgnition\\FlareMiddleware\\AddJobs' => 
      array (
        'max_chained_job_reporting_depth' => 5,
      ),
      'Spatie\\FlareClient\\FlareMiddleware\\CensorRequestBodyFields' => 
      array (
        'censor_fields' => 
        array (
          0 => 'password',
          1 => 'password_confirmation',
        ),
      ),
      'Spatie\\FlareClient\\FlareMiddleware\\CensorRequestHeaders' => 
      array (
        'headers' => 
        array (
          0 => 'API-KEY',
        ),
      ),
    ),
    'send_logs_as_events' => true,
  ),
  'ignition' => 
  array (
    'editor' => 'phpstorm',
    'theme' => 'auto',
    'enable_share_button' => true,
    'register_commands' => false,
    'solution_providers' => 
    array (
      0 => 'Spatie\\Ignition\\Solutions\\SolutionProviders\\BadMethodCallSolutionProvider',
      1 => 'Spatie\\Ignition\\Solutions\\SolutionProviders\\MergeConflictSolutionProvider',
      2 => 'Spatie\\Ignition\\Solutions\\SolutionProviders\\UndefinedPropertySolutionProvider',
      3 => 'Spatie\\LaravelIgnition\\Solutions\\SolutionProviders\\IncorrectValetDbCredentialsSolutionProvider',
      4 => 'Spatie\\LaravelIgnition\\Solutions\\SolutionProviders\\MissingAppKeySolutionProvider',
      5 => 'Spatie\\LaravelIgnition\\Solutions\\SolutionProviders\\DefaultDbNameSolutionProvider',
      6 => 'Spatie\\LaravelIgnition\\Solutions\\SolutionProviders\\TableNotFoundSolutionProvider',
      7 => 'Spatie\\LaravelIgnition\\Solutions\\SolutionProviders\\MissingImportSolutionProvider',
      8 => 'Spatie\\LaravelIgnition\\Solutions\\SolutionProviders\\InvalidRouteActionSolutionProvider',
      9 => 'Spatie\\LaravelIgnition\\Solutions\\SolutionProviders\\ViewNotFoundSolutionProvider',
      10 => 'Spatie\\LaravelIgnition\\Solutions\\SolutionProviders\\RunningLaravelDuskInProductionProvider',
      11 => 'Spatie\\LaravelIgnition\\Solutions\\SolutionProviders\\MissingColumnSolutionProvider',
      12 => 'Spatie\\LaravelIgnition\\Solutions\\SolutionProviders\\UnknownValidationSolutionProvider',
      13 => 'Spatie\\LaravelIgnition\\Solutions\\SolutionProviders\\MissingMixManifestSolutionProvider',
      14 => 'Spatie\\LaravelIgnition\\Solutions\\SolutionProviders\\MissingViteManifestSolutionProvider',
      15 => 'Spatie\\LaravelIgnition\\Solutions\\SolutionProviders\\MissingLivewireComponentSolutionProvider',
      16 => 'Spatie\\LaravelIgnition\\Solutions\\SolutionProviders\\UndefinedViewVariableSolutionProvider',
      17 => 'Spatie\\LaravelIgnition\\Solutions\\SolutionProviders\\GenericLaravelExceptionSolutionProvider',
    ),
    'ignored_solution_providers' => 
    array (
    ),
    'enable_runnable_solutions' => false,
    'remote_sites_path' => '/var/www/html',
    'local_sites_path' => '',
    'housekeeping_endpoint_prefix' => '_ignition',
    'settings_file_path' => '',
  ),
  'tinker' => 
  array (
    'commands' => 
    array (
    ),
    'alias' => 
    array (
    ),
    'dont_alias' => 
    array (
      0 => 'App\\Nova',
    ),
  ),
);
