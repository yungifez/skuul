<?php

return [

    /*
    |--------------------------------------------------------------------------
    | Title
    |--------------------------------------------------------------------------
    |
    | Here you can change the default title of your admin panel.
    |
    | For detailed instructions you can look the title section here:
    | https://github.com/jeroennoten/Laravel-AdminLTE/wiki/Basic-Configuration
    |
    */

    'title'         => env('APP_NAME'),
    'title_prefix'  => '',
    'title_postfix' => '',

    /*
    |--------------------------------------------------------------------------
    | Favicon
    |--------------------------------------------------------------------------
    |
    | Here you can activate the favicon.
    |
    | For detailed instructions you can look the favicon section here:
    | https://github.com/jeroennoten/Laravel-AdminLTE/wiki/Basic-Configuration
    |
    */

    'use_ico_only'     => true,
    'use_full_favicon' => false,

    /*
    |--------------------------------------------------------------------------
    | Logo
    |--------------------------------------------------------------------------
    |
    | Here you can change the logo of your admin panel.
    |
    | For detailed instructions you can look the logo section here:
    | https://github.com/jeroennoten/Laravel-AdminLTE/wiki/Basic-Configuration
    |
    */

    'logo'              => env('APP_NAME'),
    'logo_img'          => env('LOGO_PATH'),
    'logo_img_class'    => 'brand-image img-circle elevation-3',
    'logo_img_xl'       => null,
    'logo_img_xl_class' => 'brand-image-xs',
    'logo_img_alt'      => env('APP_NAME'),

    /*
    |--------------------------------------------------------------------------
    | User Menu
    |--------------------------------------------------------------------------
    |
    | Here you can activate and change the user menu.
    |
    | For detailed instructions you can look the user menu section here:
    | https://github.com/jeroennoten/Laravel-AdminLTE/wiki/Basic-Configuration
    |
    */

    'usermenu_enabled'      => true,
    'usermenu_header'       => true,
    'usermenu_header_class' => 'bg-indigo',
    'usermenu_image'        => true,
    'usermenu_desc'         => true,
    'usermenu_profile_url'  => false,

    /*
    |--------------------------------------------------------------------------
    | Layout
    |--------------------------------------------------------------------------
    |
    | Here we change the layout of your admin panel.
    |
    | For detailed instructions you can look the layout section here:
    | https://github.com/jeroennoten/Laravel-AdminLTE/wiki/Layout-and-Styling-Configuration
    |
    */

    'layout_topnav'        => null,
    'layout_boxed'         => null,
    'layout_fixed_sidebar' => true,
    'layout_fixed_navbar'  => null,
    'layout_fixed_footer'  => null,
    'layout_dark_mode'     => null,

    /*
    |--------------------------------------------------------------------------
    | Authentication Views Classes
    |--------------------------------------------------------------------------
    |
    | Here you can change the look and behavior of the authentication views.
    |
    | For detailed instructions you can look the auth classes section here:
    | https://github.com/jeroennoten/Laravel-AdminLTE/wiki/Layout-and-Styling-Configuration
    |
    */

    'classes_auth_card'   => 'card-outline card-primary',
    'classes_auth_header' => '',
    'classes_auth_body'   => '',
    'classes_auth_footer' => '',
    'classes_auth_icon'   => '',
    'classes_auth_btn'    => 'btn-flat btn-primary',

    /*
    |--------------------------------------------------------------------------
    | Admin Panel Classes
    |--------------------------------------------------------------------------
    |
    | Here you can change the look and behavior of the admin panel.
    |
    | For detailed instructions you can look the admin panel classes here:
    | https://github.com/jeroennoten/Laravel-AdminLTE/wiki/Layout-and-Styling-Configuration
    |
    */

    'classes_body'             => '',
    'classes_brand'            => '',
    'classes_brand_text'       => '',
    'classes_content_wrapper'  => '',
    'classes_content_header'   => '',
    'classes_content'          => '',
    'classes_sidebar'          => env('ADMINLTE_CLASSES_SIDEBAR', 'sidebar-dark-primary elevation-4'),
    'classes_sidebar_nav'      => '',
    'classes_topnav'           =>  env('ADMINLTE_CLASSES_TOPNAV', 'navbar-white navbar-light'),
    'classes_topnav_nav'       => 'navbar-expand',
    'classes_topnav_container' => 'container',

    /*
    |--------------------------------------------------------------------------
    | Sidebar
    |--------------------------------------------------------------------------
    |
    | Here we can modify the sidebar of the admin panel.
    |
    | For detailed instructions you can look the sidebar section here:
    | https://github.com/jeroennoten/Laravel-AdminLTE/wiki/Layout-and-Styling-Configuration
    |
    */

    'sidebar_mini'                            => 'lg',
    'sidebar_collapse'                        => false,
    'sidebar_collapse_auto_size'              => false,
    'sidebar_collapse_remember'               => true,
    'sidebar_collapse_remember_no_transition' => true,
    'sidebar_scrollbar_theme'                 => 'os-theme-light',
    'sidebar_scrollbar_auto_hide'             => 'l',
    'sidebar_nav_accordion'                   => true,
    'sidebar_nav_animation_speed'             => 300,

    /*
    |--------------------------------------------------------------------------
    | Control Sidebar (Right Sidebar)
    |--------------------------------------------------------------------------
    |
    | Here we can modify the right sidebar aka control sidebar of the admin panel.
    |
    | For detailed instructions you can look the right sidebar section here:
    | https://github.com/jeroennoten/Laravel-AdminLTE/wiki/Layout-and-Styling-Configuration
    |
    */

    'right_sidebar'                     => false,
    'right_sidebar_icon'                => 'fas fa-cogs',
    'right_sidebar_theme'               => 'dark',
    'right_sidebar_slide'               => true,
    'right_sidebar_push'                => true,
    'right_sidebar_scrollbar_theme'     => 'os-theme-light',
    'right_sidebar_scrollbar_auto_hide' => 'l',

    /*
    |--------------------------------------------------------------------------
    | URLs
    |--------------------------------------------------------------------------
    |
    | Here we can modify the url settings of the admin panel.
    |
    | For detailed instructions you can look the urls section here:
    | https://github.com/jeroennoten/Laravel-AdminLTE/wiki/Basic-Configuration
    |
    */

    'use_route_url'      => false,
    'dashboard_url'      => 'home',
    'logout_url'         => 'logout',
    'login_url'          => 'login',
    'register_url'       => 'register',
    'password_reset_url' => 'password/reset',
    'password_email_url' => 'password/email',
    'profile_url'        => false,

    /*
    |--------------------------------------------------------------------------
    | Preloader Animation
    |--------------------------------------------------------------------------
    |
    | Here you can change the preloader animation configuration.
    |
    | For detailed instructions you can look the preloader section here:
    | https://github.com/jeroennoten/Laravel-AdminLTE/wiki/Basic-Configuration
    |
    */

    'preloader' => [
        'enabled' => false,
        'img'     => [
            'path'   => env('LOGO_PATH', 'vendor/adminlte/dist/img/AdminLTELogo.png'),
            'alt'    => env('APP_NAME'),
            'effect' => 'animation__shake',
            'width'  => 60,
            'height' => 60,
        ],
    ],
    /*
    |--------------------------------------------------------------------------
    | Laravel Mix
    |--------------------------------------------------------------------------
    |
    | Here we can enable the Laravel Mix option for the admin panel.
    |
    | For detailed instructions you can look the laravel mix section here:
    | https://github.com/jeroennoten/Laravel-AdminLTE/wiki/Other-Configuration
    |
    */

    'enabled_laravel_mix'  => false,
    'laravel_mix_css_path' => 'css/app.css',
    'laravel_mix_js_path'  => 'js/app.js',

    /*
    |--------------------------------------------------------------------------
    | Menu Items
    |--------------------------------------------------------------------------
    |
    | Here we can modify the sidebar/top navigation of the admin panel.
    |
    | For detailed instructions you can look here:
    | https://github.com/jeroennoten/Laravel-AdminLTE/wiki/Menu-Configuration
    |
    */

    'menu' => [
        // Navbar items:
        [
            'type'         => 'fullscreen-widget',
            'topnav_right' => true,
        ],
        [
            'type'         => 'darkmode-widget',
            'topnav_right' => true,
        ],

        // Sidebar items:
        [
            'type' => 'sidebar-menu-search',
            'text' => 'search',
        ],
        [
            'type'  => 'sidebar-menu-item',
            'icon'  => 'fas fa-tachometer-alt',
            'text'  => 'Dashboard',
            'route' => 'dashboard',
        ],
        ['header' => 'Manage Profile'],
        [
            'type'  => 'sidebar-menu-item',
            'icon'  => 'fas fa-user',
            'text'  => 'User Profile',
            'route' => 'profile.show',
        ],
        ['header' => 'Multi Schools Management', 'can' => 'header-schools'],
        [
            'type' => 'sidebar-menu-item',
            'text' => 'Schools',
            'icon' => 'fas fa-school',
            'can'  => 'menu-school',

            'submenu' => [[
                'type'  => 'sidebar-menu-item',
                'text'  => 'View Schools',
                'route' => 'schools.index',
                'can'   => 'read school',
            ],
                [
                    'type'  => 'sidebar-menu-item',
                    'text'  => 'Create School',
                    'route' => 'schools.create',
                    'can'   => 'create school',
                ], ],
        ],
        ['header' => 'Administration', 'can' => 'header-administrate'],
        [
            'type'  => 'sidebar-menu-item',
            'icon'  => 'fas fa-cog',
            'text'  => 'School Settings',
            'route' => 'schools.settings',
            'can'   => 'manage school settings',
        ],
        [
            'type'    => 'sidebar-menu-item',
            'text'    => 'Classes',
            'icon'    => 'fas fa-chalkboard',
            'can'     => 'menu-class',
            'submenu' => [
                [
                    'type'  => 'sidebar-menu-item',
                    'text'  => 'View Class Groups',
                    'route' => 'class-groups.index',
                    'can'   => 'read class group',
                ],
                [
                    'type'  => 'sidebar-menu-item',
                    'text'  => 'Create Class Group',
                    'route' => 'class-groups.create',
                    'can'   => 'create class group',
                ],
                [
                    'type'  => 'sidebar-menu-item',
                    'text'  => 'View Classes',
                    'route' => 'classes.index',
                    'can'   => 'read class',
                ],
                [
                    'type'  => 'sidebar-menu-item',
                    'text'  => 'Create Class',
                    'route' => 'classes.create',
                    'can'   => 'create class',
                ],

            ],
        ],
        [
            'type'    => 'sidebar-menu-item',
            'text'    => 'sections',
            'icon'    => 'fas fa-landmark',
            'can'     => 'menu-section',
            'submenu' => [
                [
                    'type'  => 'sidebar-menu-item',
                    'text'  => 'View sections',
                    'route' => 'sections.index',
                    'can'   => 'read section',
                ],
                [
                    'type'  => 'sidebar-menu-item',
                    'text'  => 'Create section',
                    'route' => 'sections.create',
                    'can'   => 'create section',
                ],
            ],
        ],
        [
            'type'    => 'sidebar-menu-item',
            'text'    => 'Students',
            'icon'    => 'fas fa-user',
            'can'     => 'menu-student',
            'submenu' => [
                [
                    'type'  => 'sidebar-menu-item',
                    'text'  => 'View students',
                    'route' => 'students.index',
                    'can'   => 'read student',
                ],
                [
                    'type'  => 'sidebar-menu-item',
                    'text'  => 'Create student',
                    'route' => 'students.create',
                    'can'   => 'create student',
                ],
                [
                    'type'  => 'sidebar-menu-item',
                    'text'  => 'Promote students',
                    'route' => 'students.promote',
                    'can'   => 'promote student',
                ],
                [
                    'type'  => 'sidebar-menu-item',
                    'text'  => 'Manage promotions',
                    'route' => 'students.promotions',
                    'can'   => 'read promotion',
                ],
                [
                    'type'  => 'sidebar-menu-item',
                    'text'  => 'Graduate students',
                    'route' => 'students.graduate',
                    'can'   => 'graduate student',
                ],
                [
                    'type'  => 'sidebar-menu-item',
                    'text'  => 'Manage graduations',
                    'route' => 'students.graduations',
                    'can'   => 'view graduations',
                ],
            ],
        ],
        [
            'type'    => 'sidebar-menu-item',
            'text'    => 'Account Applications',
            'icon'    => 'fas fa-plus',
            'can'     => 'menu-account-application',
            'submenu' => [
                [
                    'type'  => 'sidebar-menu-item',
                    'text'  => 'View account applications',
                    'route' => 'account-applications.index',
                    'can'   => 'read applicant',
                ],
                [
                    'type'  => 'sidebar-menu-item',
                    'text'  => 'View rejected applications',
                    'route' => 'account-applications.rejected-applications',
                    'can'   => 'read applicant',
                ],
            ],
        ],
        [
            'type'    => 'sidebar-menu-item',
            'text'    => 'Teachers',
            'icon'    => 'fas fa-user',
            'can'     => 'menu-teacher',
            'submenu' => [
                [
                    'type'  => 'sidebar-menu-item',
                    'text'  => 'View teachers',
                    'route' => 'teachers.index',
                    'can'   => 'read teacher',
                ],
                [
                    'type'  => 'sidebar-menu-item',
                    'text'  => 'Create teacher',
                    'route' => 'teachers.create',
                    'can'   => 'create teacher',
                ],
            ],
        ],
        [
            'type'    => 'sidebar-menu-item',
            'text'    => 'Parents',
            'icon'    => 'fas fa-user',
            'can'     => 'menu-parent',
            'submenu' => [
                [
                    'type'  => 'sidebar-menu-item',
                    'text'  => 'View parents',
                    'route' => 'parents.index',
                    'can'   => 'read parent',
                ],
                [
                    'type'  => 'sidebar-menu-item',
                    'text'  => 'Create parent',
                    'route' => 'parents.create',
                    'can'   => 'create parent',
                ],
            ],
        ],
        [
            'type'    => 'sidebar-menu-item',
            'text'    => 'Admins',
            'icon'    => 'fas fa-user',
            'can'     => 'menu-admin',
            'submenu' => [
                [
                    'type'  => 'sidebar-menu-item',
                    'text'  => 'View admins',
                    'route' => 'admins.index',
                    'can'   => 'read admin',
                ],
                [
                    'type'  => 'sidebar-menu-item',
                    'text'  => 'Create admin',
                    'route' => 'admins.create',
                    'can'   => 'create admin',
                ],
            ],
        ],
        [
            'type'    => 'sidebar-menu-item',
            'text'    => 'Academic years',
            'icon'    => 'fas fa-calendar',
            'can'     => 'menu-academic-year',
            'submenu' => [
                [
                    'type'  => 'sidebar-menu-item',
                    'text'  => 'View academic years',
                    'route' => 'academic-years.index',
                    'can'   => 'read academic year',
                ],
                [
                    'type'  => 'sidebar-menu-item',
                    'text'  => 'Create academic year',
                    'route' => 'academic-years.create',
                    'can'   => 'create academic year',
                ],
            ],
        ],
        [
            'type'    => 'sidebar-menu-item',
            'text'    => 'Semesters',
            'icon'    => 'fas fa-clock',
            'can'     => 'menu-semester',
            'submenu' => [
                [
                    'type'  => 'sidebar-menu-item',
                    'text'  => 'View semesters',
                    'route' => 'semesters.index',
                    'can'   => 'read semester',
                ],
                [
                    'type'  => 'sidebar-menu-item',
                    'text'  => 'Create semester',
                    'route' => 'semesters.create',
                    'can'   => 'create semester',
                ],
            ],
        ],
        [
            'type'    => 'sidebar-menu-item',
            'text'    => 'Subjects',
            'icon'    => 'fas fa-lightbulb',
            'can'     => 'menu-subject',
            'submenu' => [
                [
                    'type'  => 'sidebar-menu-item',
                    'text'  => 'View subjects',
                    'route' => 'subjects.index',
                    'can'   => 'read subject',
                ],
                [
                    'type'  => 'sidebar-menu-item',
                    'text'  => 'Create subject',
                    'route' => 'subjects.create',
                    'can'   => 'create subject',
                ],
                [
                    'type'  => 'sidebar-menu-item',
                    'text'  => 'Assign teacher to subjects',
                    'route' => 'subjects.assign-teacher',
                    'can'   => 'update subject',
                ],
            ],
        ],
        ['header' => 'Academics', 'can' => 'header-academics'],
        [
            'type'    => 'sidebar-menu-item',
            'text'    => 'Notices',
            'icon'    => 'fas fa-bell',
            'can'     => 'menu-notice',
            'submenu' => [
                [
                    'type'  => 'sidebar-menu-item',
                    'text'  => 'View notices',
                    'route' => 'notices.index',
                    'can'   => 'read notice',
                ],
                [
                    'type'  => 'sidebar-menu-item',
                    'text'  => 'Create notice',
                    'route' => 'notices.create',
                    'can'   => 'create notice',
                ],
            ],
        ],
        [
            'type'    => 'sidebar-menu-item',
            'text'    => 'Syllabi',
            'icon'    => 'fas fa-list-alt',
            'can'     => 'menu-syllabus',
            'submenu' => [
                [
                    'type'  => 'sidebar-menu-item',
                    'text'  => 'View Syllabi',
                    'route' => 'syllabi.index',
                    'can'   => 'read syllabus',
                ],
                [
                    'type'  => 'sidebar-menu-item',
                    'text'  => 'Create Syllabus',
                    'route' => 'syllabi.create',
                    'can'   => 'create syllabus',
                ],
            ],
        ],
        [
            'type'    => 'sidebar-menu-item',
            'text'    => 'Timetables',
            'icon'    => 'fas fa-tasks',
            'can'     => 'menu-timetable',
            'submenu' => [
                [
                    'type'  => 'sidebar-menu-item',
                    'text'  => 'View Timetables',
                    'route' => 'timetables.index',
                    'can'   => 'read timetable',
                ],
                [
                    'type'  => 'sidebar-menu-item',
                    'text'  => 'Create Timetable',
                    'route' => 'timetables.create',
                    'can'   => 'create timetable',
                ],
                [
                    'type'  => 'sidebar-menu-item',
                    'text'  => 'View custom items',
                    'route' => 'custom-timetable-items.index',
                    'can'   => 'read custom timetable items',
                ],
                [
                    'type'  => 'sidebar-menu-item',
                    'text'  => 'Create Custom Items',
                    'route' => 'custom-timetable-items.create',
                    'can'   => 'create custom timetable items',
                ],
            ],
        ],
        [
            'type'    => 'sidebar-menu-item',
            'text'    => 'Exams',
            'icon'    => 'fas fa-book-open',
            'can'     => 'menu-exam',
            'submenu' => [
                [
                    'type'  => 'sidebar-menu-item',
                    'text'  => 'View Exams',
                    'route' => 'exams.index',
                    'can'   => 'read exam',
                ],
                [
                    'type'  => 'sidebar-menu-item',
                    'text'  => 'Create Exam',
                    'route' => 'exams.create',
                    'can'   => 'create exam',
                ],
                [
                    'type'  => 'sidebar-menu-item',
                    'text'  => 'Manage Exam records',
                    'route' => 'exam-records.index',
                    'can'   => 'update exam record',
                ],
                [
                    'type'  => 'sidebar-menu-item',
                    'text'  => 'Exam tabulation sheet',
                    'route' => 'exams.tabulation',
                    'can'   => 'read exam',
                ],
                [
                    'type'  => 'sidebar-menu-item',
                    'text'  => 'Result tabulation sheet',
                    'route' => 'exams.result-tabulation',
                    'can'   => 'read exam',
                ],
                [
                    'type'  => 'sidebar-menu-item',
                    'text'  => 'Result Checker',
                    'route' => 'exams.result-checker',
                    'can'   => 'check result',
                ],
            ],
        ],
        [
            'type'    => 'sidebar-menu-item',
            'text'    => 'Grade systems',
            'icon'    => 'fa fa-graduation-cap',
            'can'     => 'menu-grade-system',
            'submenu' => [
                [
                    'type'  => 'sidebar-menu-item',
                    'text'  => 'View Grades',
                    'route' => 'grade-systems.index',
                    'can'   => 'read grade system',
                ],
                [
                    'type'  => 'sidebar-menu-item',
                    'text'  => 'Create Grades',
                    'route' => 'grade-systems.create',
                    'can'   => 'create grade system',
                ],
            ],
        ],
        [
            'type'  => 'sidebar-menu-item',
            'text'  => 'View Logs',
            'url'   => '/logs',
            'icon'  => 'fa fa-sticky-note',
            //it checks with roles for now so this is to prevent other users from viewing menu item
            'can'   => 'view logs',
        ],
    ],

    /*
    |--------------------------------------------------------------------------
    | Menu Filters
    |--------------------------------------------------------------------------
    |
    | Here we can modify the menu filters of the admin panel.
    |
    | For detailed instructions you can look the menu filters section here:
    | https://github.com/jeroennoten/Laravel-AdminLTE/wiki/Menu-Configuration
    |
    */

    'filters' => [
        JeroenNoten\LaravelAdminLte\Menu\Filters\GateFilter::class,
        JeroenNoten\LaravelAdminLte\Menu\Filters\HrefFilter::class,
        JeroenNoten\LaravelAdminLte\Menu\Filters\SearchFilter::class,
        JeroenNoten\LaravelAdminLte\Menu\Filters\ActiveFilter::class,
        JeroenNoten\LaravelAdminLte\Menu\Filters\ClassesFilter::class,
        JeroenNoten\LaravelAdminLte\Menu\Filters\LangFilter::class,
        JeroenNoten\LaravelAdminLte\Menu\Filters\DataFilter::class,
    ],

    /*
    |--------------------------------------------------------------------------
    | Plugins Initialization
    |--------------------------------------------------------------------------
    |
    | Here we can modify the plugins used inside the admin panel.
    |
    | For detailed instructions you can look the plugins section here:
    | https://github.com/jeroennoten/Laravel-AdminLTE/wiki/Plugins-Configuration
    |
    */

    'plugins' => [
        'DateRangePicker' => [
            'active' => false,
            'files'  => [
                [
                    'type'     => 'js',
                    'asset'    => true,
                    'location' => 'vendor/moment/moment.min.js',
                ],
                [
                    'type'     => 'js',
                    'asset'    => true,
                    'location' => 'vendor/daterangepicker/daterangepicker.js',
                ],
                [
                    'type'     => 'css',
                    'asset'    => true,
                    'location' => 'vendor/daterangepicker/daterangepicker.css',
                ],
            ],
        ],
        'Datatables' => [
            'active' => true,
            'files'  => [
                [
                    'type'     => 'js',
                    'asset'    => true,
                    'location' => 'vendor/datatables/js/jquery.dataTables.min.js',
                ],
                [
                    'type'     => 'js',
                    'asset'    => true,
                    'location' => 'vendor/datatables/js/dataTables.bootstrap4.min.js',
                ],
                [
                    'type'     => 'css',
                    'asset'    => true,
                    'location' => 'vendor/datatables/css/dataTables.bootstrap4.min.css',
                ],
            ],
        ],
        'DatatablesPlugins' => [
            'active' => false,
            'files'  => [
                [
                    'type'     => 'js',
                    'asset'    => true,
                    'location' => 'vendor/datatables-plugins/buttons/js/dataTables.buttons.min.js',
                ],
                [
                    'type'     => 'js',
                    'asset'    => true,
                    'location' => 'vendor/datatables-plugins/buttons/js/buttons.bootstrap4.min.js',
                ],
                [
                    'type'     => 'js',
                    'asset'    => true,
                    'location' => 'vendor/datatables-plugins/buttons/js/buttons.html5.min.js',
                ],
                [
                    'type'     => 'js',
                    'asset'    => true,
                    'location' => 'vendor/datatables-plugins/buttons/js/buttons.print.min.js',
                ],
                [
                    'type'     => 'js',
                    'asset'    => true,
                    'location' => 'vendor/datatables-plugins/jszip/jszip.min.js',
                ],
                [
                    'type'     => 'js',
                    'asset'    => true,
                    'location' => 'vendor/datatables-plugins/pdfmake/pdfmake.min.js',
                ],
                [
                    'type'     => 'js',
                    'asset'    => true,
                    'location' => 'vendor/datatables-plugins/pdfmake/vfs_fonts.js',
                ],
                [
                    'type'     => 'css',
                    'asset'    => true,
                    'location' => 'vendor/datatables-plugins/buttons/css/buttons.bootstrap4.min.css',
                ],
            ],
        ],
        'TempusDominusBs4' => [
            'active' => false,
            'files'  => [
                [
                    'type'     => 'js',
                    'asset'    => true,
                    'location' => 'vendor/moment/moment.min.js',
                ],
                [
                    'type'     => 'js',
                    'asset'    => true,
                    'location' => 'vendor/tempusdominus-bootstrap-4/js/tempusdominus-bootstrap-4.min.js',
                ],
                [
                    'type'     => 'css',
                    'asset'    => true,
                    'location' => 'vendor/tempusdominus-bootstrap-4/css/tempusdominus-bootstrap-4.min.css',
                ],
            ],
        ],

        'Select2' => [
            'active' => false,
            'files'  => [
                [
                    'type'     => 'js',
                    'asset'    => true,
                    'location' => 'vendor/select2/js/select2.full.min.js',
                ],
                [
                    'type'     => 'css',
                    'asset'    => true,
                    'location' => 'vendor/select2/css/select2.min.css',
                ],
                [
                    'type'     => 'css',
                    'asset'    => true,
                    'location' => 'vendor/select2-bootstrap4-theme/select2-bootstrap4.min.css',
                ],
            ],
        ],
        'Chartjs' => [
            'active' => false,
            'files'  => [
                [
                    'type'     => 'js',
                    'asset'    => false,
                    'location' => '//cdnjs.cloudflare.com/ajax/libs/Chart.js/2.7.0/Chart.bundle.min.js',
                ],
            ],
        ],
        'Sweetalert2' => [
            'active' => false,
            'files'  => [
                [
                    'type'     => 'js',
                    'asset'    => false,
                    'location' => '//cdn.jsdelivr.net/npm/sweetalert2@8',
                ],
            ],
        ],
        'Pace' => [
            'active' => false,
            'files'  => [
                [
                    'type'     => 'css',
                    'asset'    => false,
                    'location' => '//cdnjs.cloudflare.com/ajax/libs/pace/1.0.2/themes/blue/pace-theme-center-radar.min.css',
                ],
                [
                    'type'     => 'js',
                    'asset'    => false,
                    'location' => '//cdnjs.cloudflare.com/ajax/libs/pace/1.0.2/pace.min.js',
                ],
            ],
        ],
        'BsCustomFileInput' => [
            'active' => false,
            'files'  => [
                [
                    'type'     => 'js',
                    'asset'    => true,
                    'location' => 'vendor/bs-custom-file-input/bs-custom-file-input.min.js',
                ],
            ],
        ],
    ],

    /*
    |--------------------------------------------------------------------------
    | IFrame
    |--------------------------------------------------------------------------
    |
    | Here we change the IFrame mode configuration. Note these changes will
    | only apply to the view that extends and enable the IFrame mode.
    |
    | For detailed instructions you can look the iframe mode section here:
    | https://github.com/jeroennoten/Laravel-AdminLTE/wiki/IFrame-Mode-Configuration
    |
    */

    'iframe' => [
        'default_tab' => [
            'url'   => null,
            'title' => null,
        ],
        'buttons' => [
            'close'           => true,
            'close_all'       => true,
            'close_all_other' => true,
            'scroll_left'     => true,
            'scroll_right'    => true,
            'fullscreen'      => true,
        ],
        'options' => [
            'loading_screen'    => 1000,
            'auto_show_new_tab' => true,
            'use_navbar_items'  => true,
        ],
    ],

    /*
    |--------------------------------------------------------------------------
    | Livewire
    |--------------------------------------------------------------------------
    |
    | Here we can enable the Livewire support.
    |
    | For detailed instructions you can look the livewire here:
    | https://github.com/jeroennoten/Laravel-AdminLTE/wiki/Other-Configuration
    |
    */

    'livewire' => true,
];
