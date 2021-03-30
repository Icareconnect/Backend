<?php

return [

    /*
    |--------------------------------------------------------------------------
    | Title
    |--------------------------------------------------------------------------
    |
    | Here you can change the default title of your admin panel.
    |
    | For more detailed instructions you can look here:
    | https://github.com/jeroennoten/Laravel-AdminLTE/#61-title
    |
    */

    'title' => 'Consultant',
    'title_prefix' => '',
    'title_postfix' => '',

    /*
    |--------------------------------------------------------------------------
    | Favicon
    |--------------------------------------------------------------------------
    |
    | Here you can activate the favicon.
    |
    | For more detailed instructions you can look here:
    | https://github.com/jeroennoten/Laravel-AdminLTE/#62-favicon
    |
    */

    'use_ico_only' => false,
    'use_full_favicon' => false,

    /*
    |--------------------------------------------------------------------------
    | Logo
    |--------------------------------------------------------------------------
    |
    | Here you can change the logo of your admin panel.
    |
    | For more detailed instructions you can look here:
    | https://github.com/jeroennoten/Laravel-AdminLTE/#63-logo
    |
    */

    'logo' => '<b>Consultant</b>',
    'logo_img' => 'vendor/adminlte/dist/img/AdminLTELogo.png',
    'logo_img_class' => 'brand-image img-circle elevation-3',
    'logo_img_xl' => null,
    'logo_img_xl_class' => 'brand-image-xs',
    'logo_img_alt' => 'Consultant',

    /*
    |--------------------------------------------------------------------------
    | User Menu
    |--------------------------------------------------------------------------
    |
    | Here you can activate and change the user menu.
    |
    | For more detailed instructions you can look here:
    | https://github.com/jeroennoten/Laravel-AdminLTE/#64-user-menu
    |
    */

    'usermenu_enabled' => true,
    'usermenu_header' => false,
    'usermenu_header_class' => 'bg-primary',
    'usermenu_image' => false,
    'usermenu_desc' => false,

    /*
    |--------------------------------------------------------------------------
    | Layout
    |--------------------------------------------------------------------------
    |
    | Here we change the layout of your admin panel.
    |
    | For more detailed instructions you can look here:
    | https://github.com/jeroennoten/Laravel-AdminLTE/#65-layout
    |
    */

    'layout_topnav' => null,
    'layout_boxed' => null,
    'layout_fixed_sidebar' => null,
    'layout_fixed_navbar' => null,
    'layout_fixed_footer' => null,

    /*
    |--------------------------------------------------------------------------
    | Extra Classes
    |--------------------------------------------------------------------------
    |
    | Here you can change the look and behavior of the admin panel.
    |
    | For more detailed instructions you can look here:
    | https://github.com/jeroennoten/Laravel-AdminLTE/#66-classes
    |
    */

    'classes_body' => '',
    'classes_brand' => '',
    'classes_brand_text' => '',
    'classes_content_header' => 'container-fluid',
    'classes_content' => 'container-fluid',
    'classes_sidebar' => 'sidebar-dark-primary elevation-4',
    'classes_sidebar_nav' => '',
    'classes_topnav' => 'navbar-white navbar-light',
    'classes_topnav_nav' => 'navbar-expand-md',
    'classes_topnav_container' => 'container',

    /*
    |--------------------------------------------------------------------------
    | Sidebar
    |--------------------------------------------------------------------------
    |
    | Here we can modify the sidebar of the admin panel.
    |
    | For more detailed instructions you can look here:
    | https://github.com/jeroennoten/Laravel-AdminLTE/#67-sidebar
    |
    */

    'sidebar_mini' => true,
    'sidebar_collapse' => false,
    'sidebar_collapse_auto_size' => false,
    'sidebar_collapse_remember' => false,
    'sidebar_collapse_remember_no_transition' => true,
    'sidebar_scrollbar_theme' => 'os-theme-light',
    'sidebar_scrollbar_auto_hide' => 'l',
    'sidebar_nav_accordion' => true,
    'sidebar_nav_animation_speed' => 300,

    /*
    |--------------------------------------------------------------------------
    | Control Sidebar (Right Sidebar)
    |--------------------------------------------------------------------------
    |
    | Here we can modify the right sidebar aka control sidebar of the admin panel.
    |
    | For more detailed instructions you can look here:
    | https://github.com/jeroennoten/Laravel-AdminLTE/#68-control-sidebar-right-sidebar
    |
    */

    'right_sidebar' => false,
    'right_sidebar_icon' => 'fas fa-cogs',
    'right_sidebar_theme' => 'dark',
    'right_sidebar_slide' => true,
    'right_sidebar_push' => true,
    'right_sidebar_scrollbar_theme' => 'os-theme-light',
    'right_sidebar_scrollbar_auto_hide' => 'l',

    /*
    |--------------------------------------------------------------------------
    | URLs
    |--------------------------------------------------------------------------
    |
    | Here we can modify the url settings of the admin panel.
    |
    | For more detailed instructions you can look here:
    | https://github.com/jeroennoten/Laravel-AdminLTE/#69-urls
    |
    */

    'use_route_url' => false,

    'dashboard_url' => 'admin/dashboard',

    'logout_url' => 'logout',

    'login_url' => 'admin/login',

    'register_url' => 'register',

    'password_reset_url' => 'password/reset',

    'password_email_url' => 'password/email',

    'profile_url' => false,

    /*
    |--------------------------------------------------------------------------
    | Laravel Mix
    |--------------------------------------------------------------------------
    |
    | Here we can enable the Laravel Mix option for the admin panel.
    |
    | For more detailed instructions you can look here:
    | https://github.com/jeroennoten/Laravel-AdminLTE/#610-laravel-mix
    |
    */

    'enabled_laravel_mix' => false,

    /*
    |--------------------------------------------------------------------------
    | Menu Items
    |--------------------------------------------------------------------------
    |
    | Here we can modify the sidebar/top navigation of the admin panel.
    |
    | For more detailed instructions you can look here:
    | https://github.com/jeroennoten/Laravel-AdminLTE/#611-menu
    |
    */

    'menu' => [
        [
            'text' => 'search',
            'search' => true,
            'topnav' => true,
        ],
        [
            'text' => 'blog',
            'url'  => 'admin/blog',
            'can'  => 'manage-blog',
        ],
        [
            'text'       => 'Dashboard',
            'icon_color' => 'red',
            'url'     => 'admin/dashboard',
        ],
        [
            'text'        => 'Services',
            'icon'        => 'far fa-fw fa-check-circle',
            'label_color' => 'success',
            'role'        => 'admin',
            'url'         => 'admin/services',
             'submenu' => [
                [
                    'text' => 'Services',
                    'url'  => 'admin/services',
                ],
                [
                    'text' => 'Add Service',
                    'url'  => 'admin/services/create',
                ],
             ]
        ],
        [
            'text'        => 'Categories',
            'icon'        => 'far fa-fw fa-check-circle',
            'label_color' => 'success',
            'role'        => 'admin',
            'submenu' => [
                [
                    'text' => 'Categories',
                    'url'  => 'admin/categories',
                ],
                [
                    'text' => 'Consultants',
                    'url'  => 'admin/consultants',
                ],
                [
                'text'        => 'Filters',
                'icon'        => 'far fa-fw fa-check-circle',
                'label_color' => 'success',
                'role'        => 'admin',
                'submenu' => [
                    [
                        'text' => 'Filter List',
                        'url'  => 'admin/filters',
                    ],
                    [
                        'text' => 'Add Filter',
                        'url'  => 'admin/filters/create',
                    ]
                ],
            ],
            ],
        ],
        [
            'text'        => 'USERS',
            'icon'        => 'far fa-fw fa-user',
            'label_color' => 'success',
            'role'        => 'admin',
            'url'  => 'admin/customers',
        ],
        [
            'text'        => 'Cluster',
            'icon'        => 'far fa-fw fa-user',
            'label_color' => 'success',
            'role'        => 'admin',
            'url'  => 'admin/cluster',
            'submenu' => [
                    [
                        'text' => 'Clusters',
                        'url'  => 'admin/cluster',
                    ],
                    [
                        'text' => 'Add Cluster',
                        'url'  => 'admin/cluster/create',
                    ]
                ],
        ],
        [
            'text'        => 'Banners',
            'icon'        => 'far fa-fw fa-user',
            'label_color' => 'success',
            'role'        => 'admin',
            'url'  => 'admin/banner',
            'submenu' => [
                    [
                        'text' => 'Banners',
                        'url'  => 'admin/banner',
                    ],
                    [
                        'text' => 'Add Banner',
                        'url'  => 'admin/banner/create',
                    ]
                ],
        ],
        [
            'text'        => 'Other Services',
            'icon'        => 'far fa-fw fa-check-circle',
            'label_color' => 'success',
            'role'        => 'admin',
            'url'         => 'admin/service_enable'
        ],
        [
            'text'        => 'Module',
            'icon'        => 'far fa-fw fa-user',
            'label_color' => 'success',
            'role'        => 'super_admin',
            'submenu' => [
                [
                    'text' => 'Custom Modules',
                    'url'  => 'admin/app-modules',
                ],
                [
                    'text'    => 'Add Custom Module',
                    'url'     => 'admin/app-modules/create',
                ],
            ],
        ],
        [
            'text'        => 'Requests',
            'icon'        => 'far fa-fw fa-check-circle',
            'label_color' => 'success',
            'role'        => 'admin',
            'submenu' => [
                [
                    'text' => 'Chat Requests',
                    'url'  => 'admin/chat-requests',
                ],
                [
                    'text'    => 'Call Requests',
                    'url'     => 'admin/call-requests',
                ],
            ],
        ],

        [
            'text'        => 'Pages',
            'icon'        => 'far fa-fw fa-check-circle',
            'label_color' => 'success',
            'role'        => 'admin',
            'submenu' => [
                [
                    'text' => 'Page List',
                    'url'  => 'admin/pages',
                ],
                [
                    'text'    => 'Add Page',
                    'url'     => 'admin/pages/create',
                ],
            ],
        ],
        [
            'text'        => 'Coupon',
            'icon'        => 'far fa-fw fa-check-circle',
            'label_color' => 'success',
            'role'        => 'admin',
            'submenu' => [
                [
                    'text' => 'Coupon List',
                    'url'  => 'admin/coupon',
                ],
                [
                    'text'    => 'Add Coupon',
                    'url'     => 'admin/coupon/create',
                ],
            ],
        ],
        // ['header' => 'account_settings'],
        // [
        //     'text' => 'profile',
        //     'url'  => 'admin/settings',
        //     'icon' => 'fas fa-fw fa-user',
        // ],
        // [
        //     'text' => 'change_password',
        //     'url'  => 'admin/settings',
        //     'icon' => 'fas fa-fw fa-lock',
        // ],
    ],

    /*
    |--------------------------------------------------------------------------
    | Menu Filters
    |--------------------------------------------------------------------------
    |
    | Here we can modify the menu filters of the admin panel.
    |
    | For more detailed instructions you can look here:
    | https://github.com/jeroennoten/Laravel-AdminLTE/#612-menu-filters
    |
    */

    'filters' => [
        JeroenNoten\LaravelAdminLte\Menu\Filters\HrefFilter::class,
        JeroenNoten\LaravelAdminLte\Menu\Filters\SearchFilter::class,
        JeroenNoten\LaravelAdminLte\Menu\Filters\ActiveFilter::class,
        JeroenNoten\LaravelAdminLte\Menu\Filters\SubmenuFilter::class,
        JeroenNoten\LaravelAdminLte\Menu\Filters\ClassesFilter::class,
        JeroenNoten\LaravelAdminLte\Menu\Filters\GateFilter::class,
        JeroenNoten\LaravelAdminLte\Menu\Filters\LangFilter::class,
    ],

    /*
    |--------------------------------------------------------------------------
    | Plugins Initialization
    |--------------------------------------------------------------------------
    |
    | Here we can modify the plugins used inside the admin panel.
    |
    | For more detailed instructions you can look here:
    | https://github.com/jeroennoten/Laravel-AdminLTE/#613-plugins
    |
    */

    'plugins' => [
        [
            'name' => 'Datatables',
            'active' => true,
            'files' => [
                [
                    'type' => 'js',
                    'asset' => false,
                    'location' => '//cdn.datatables.net/1.10.19/js/jquery.dataTables.min.js',
                ],
                [
                    'type' => 'js',
                    'asset' => false,
                    'location' => '//cdn.datatables.net/1.10.19/js/dataTables.bootstrap4.min.js',
                ],
                [
                    'type' => 'css',
                    'asset' => false,
                    'location' => '//cdn.datatables.net/1.10.19/css/dataTables.bootstrap4.min.css',
                ],
            ],
        ],
        [
            'name' => 'Select2',
            'active' => true,
            'files' => [
                [
                    'type' => 'js',
                    'asset' => false,
                    'location' => '//cdnjs.cloudflare.com/ajax/libs/select2/4.0.3/js/select2.min.js',
                ],
                [
                    'type' => 'css',
                    'asset' => false,
                    'location' => '//cdnjs.cloudflare.com/ajax/libs/select2/4.0.3/css/select2.css',
                ],
            ],
        ],
        [
            'name' => 'Chartjs',
            'active' => true,
            'files' => [
                [
                    'type' => 'js',
                    'asset' => false,
                    'location' => '//cdnjs.cloudflare.com/ajax/libs/Chart.js/2.7.0/Chart.bundle.min.js',
                ],
            ],
        ],
        [
            'name' => 'Sweetalert2',
            'active' => true,
            'files' => [
                [
                    'type' => 'js',
                    'asset' => false,
                    'location' => '//cdn.jsdelivr.net/npm/sweetalert2@8',
                ],
            ],
        ],
        [
            'name' => 'Pace',
            'active' => true,
            'files' => [
                [
                    'type' => 'css',
                    'asset' => false,
                    'location' => '//cdnjs.cloudflare.com/ajax/libs/pace/1.0.2/themes/blue/pace-theme-center-radar.min.css',
                ],
                [
                    'type' => 'js',
                    'asset' => false,
                    'location' => '//cdnjs.cloudflare.com/ajax/libs/pace/1.0.2/pace.min.js',
                ],
            ],
        ],
        [
            'name' => 'Summernote',
            'active' => true,
            'files' => [
                [
                    'type' => 'css',
                    'asset' => true,
                    'location' => 'vendor/adminlte/dist/css/summernote-bs4.css',
                ],
                [
                    'type' => 'js',
                    'asset' => false,
                    'location' => '//cdnjs.cloudflare.com/ajax/libs/summernote/0.8.12/summernote-bs4.min.js',
                ],
            ],
        ],
        [
            'name' => 'Tree',
            'active' => true,
            'files' => [
                [
                    'type' => 'css',
                    'asset' => true,
                    'location' => 'vendor/adminlte/dist/css/tree.css',
                ],
                [
                    'type' => 'js',
                    'asset' => true,
                    'location' => 'vendor/adminlte/dist/js/tree.js',
                ],
                [
                    'type' => 'js',
                    'asset' => true,
                    'location' => 'vendor/adminlte/dist/js/treeitem.js',
                ],
                [
                    'type' => 'js',
                    'asset' => true,
                    'location' => 'vendor/adminlte/dist/js/treeitemClick.js',
                ],

            ],
        ],
        [
            'name' => 'ColorPicker',
            'active' => true,
            'files' => [
                [
                    'type' => 'css',
                    'asset' => true,
                    'location' => '/vendor/adminlte/dist/css/bootstrap-colorpicker.min.css',
                ],
                [
                    'type' => 'js',
                    'asset' => true,
                    'location' => '/vendor/adminlte/dist/js/bootstrap-colorpicker.min.js',
                ],
            ],
        ],
        [
            'name' => 'Slider',
            'active' => true,
            'files' => [
                [
                    'type' => 'css',
                    'asset' => true,
                    'location' => '/vendor/adminlte/dist/css/slider.min.css',
                ],
                [
                    'type' => 'js',
                    'asset' => true,
                    'location' => '/vendor/adminlte/dist/js/slider.min.js',
                ],
            ],
        ],
        [
            'name' => 'DateRange',
            'active' => true,
            'files' => [
                [
                    'type' => 'css',
                    'asset' => true,
                    'location' => '/vendor/adminlte/dist/css/daterange.css',
                ],
                [
                    'type' => 'js',
                    'asset' => true,
                    'location' => '/vendor/adminlte/dist/js/moment.js',
                ],
                [
                    'type' => 'js',
                    'asset' => true,
                    'location' => '/vendor/adminlte/dist/js/daterange.js',
                ],
            ],
        ],
    ],
];
