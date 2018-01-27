
<!DOCTYPE html>
<html lang="en">
<head>
    <meta http-equiv="Content-Type" content="text/html; charset=UTF-8">
    <!-- Meta, title, CSS, favicons, etc. -->
    <meta charset="utf-8">
    <meta http-equiv="X-UA-Compatible" content="IE=edge">
    <meta name="viewport" content="width=device-width, initial-scale=1">

    <title>Gentelella Alela! | </title>

    <!-- Bootstrap -->
    <link href="{{URL::asset('css2/bootstrap/dist/css/bootstrap.min.css')}}" rel="stylesheet">
    <!-- Font Awesome -->
    <link href="{{URL::asset('css2/font-awesome/css/font-awesome.min.css')}}" rel="stylesheet">
    <!-- NProgress -->
    <link href="{{URL::asset('css2/nprogress/nprogress.css')}}" rel="stylesheet">
    <!-- iCheck -->
    <link href="{{URL::asset('css2/iCheck/skins/flat/green.css')}}" rel="stylesheet">

    <!-- bootstrap-progressbar -->
    <link href="{{URL::asset('css2/bootstrap-progressbar/css/bootstrap-progressbar-3.3.4.min.css')}}" rel="stylesheet">
    <!-- JQVMap -->
    <link href="{{URL::asset('css2/jqvmap/dist/jqvmap.min.css')}}" rel="stylesheet"/>
    <!-- bootstrap-daterangepicker -->
    <link href="{{URL::asset('css2/bootstrap-daterangepicker/daterangepicker.css')}}" rel="stylesheet">

    <!-- Custom Theme Style -->
    <link href="{{URL::asset('css2/build/css/custom.min.css')}}" rel="stylesheet">
</head>

<body class="nav-md">
<div class="container body">
    <div class="main_container">
        <div class="col-md-3 left_col">
            <div class="left_col scroll-view">
                <div class="navbar nav_title" style="border: 0;">
                    <a href="index.html" class="site_title"><i class="fa fa-paw"></i> <span>Gentelella Alela!</span></a>
                </div>

                <div class="clearfix"></div>

                <!-- menu profile quick info -->
                <div class="profile clearfix">
                    <div class="profile_pic">
                        <img src="{{URL::asset('css2/images/img.jpg')}}" alt="..." class="img-circle profile_img">
                    </div>
                    <div class="profile_info">
                        <span>Welcome,</span>
                        <h2>John Doe</h2>
                    </div>
                </div>
                <!-- /menu profile quick info -->

                <br />

                <!-- sidebar menu -->
                <div id="sidebar-menu" class="main_menu_side hidden-print main_menu">
                    <div class="menu_section">
                        <h3>General</h3>
                        <ul class="nav side-menu">
                            <li><a><i class="fa fa-home"></i> Home <span class="fa fa-chevron-down"></span></a>
                                <ul class="nav child_menu">
                                    <li><a href="index.html">Dashboard</a></li>
                                    <li><a href="index2.html">Dashboard2</a></li>
                                    <li><a href="index3.html">Dashboard3</a></li>
                                </ul>
                            </li>
                            <li><a><i class="fa fa-edit"></i> Forms <span class="fa fa-chevron-down"></span></a>
                                <ul class="nav child_menu">
                                    <li><a href="form.html">General Form</a></li>
                                    <li><a href="form_advanced.html">Advanced Components</a></li>
                                    <li><a href="form_validation.html">Form Validation</a></li>
                                    <li><a href="form_wizards.html">Form Wizard</a></li>
                                    <li><a href="form_upload.html">Form Upload</a></li>
                                    <li><a href="form_buttons.html">Form Buttons</a></li>
                                </ul>
                            </li>
                            <li><a><i class="fa fa-desktop"></i> UI Elements <span class="fa fa-chevron-down"></span></a>
                                <ul class="nav child_menu">
                                    <li><a href="general_elements.html">General Elements</a></li>
                                    <li><a href="media_gallery.html">Media Gallery</a></li>
                                    <li><a href="typography.html">Typography</a></li>
                                    <li><a href="icons.html">Icons</a></li>
                                    <li><a href="glyphicons.html">Glyphicons</a></li>
                                    <li><a href="widgets.html">Widgets</a></li>
                                    <li><a href="invoice.html">Invoice</a></li>
                                    <li><a href="inbox.html">Inbox</a></li>
                                    <li><a href="calendar.html">Calendar</a></li>
                                </ul>
                            </li>
                            <li><a><i class="fa fa-table"></i> Tables <span class="fa fa-chevron-down"></span></a>
                                <ul class="nav child_menu">
                                    <li><a href="tables.html">Tables</a></li>
                                    <li><a href="tables_dynamic.html">Table Dynamic</a></li>
                                </ul>
                            </li>
                            <li><a><i class="fa fa-bar-chart-o"></i> Data Presentation <span class="fa fa-chevron-down"></span></a>
                                <ul class="nav child_menu">
                                    <li><a href="chartjs.html">Chart JS</a></li>
                                    <li><a href="chartjs2.html">Chart JS2</a></li>
                                    <li><a href="morisjs.html">Moris JS</a></li>
                                    <li><a href="echarts.html">ECharts</a></li>
                                    <li><a href="other_charts.html">Other Charts</a></li>
                                </ul>
                            </li>
                            <li><a><i class="fa fa-clone"></i>Layouts <span class="fa fa-chevron-down"></span></a>
                                <ul class="nav child_menu">
                                    <li><a href="fixed_sidebar.html">Fixed Sidebar</a></li>
                                    <li><a href="fixed_footer.html">Fixed Footer</a></li>
                                </ul>
                            </li>
                        </ul>
                    </div>
                    <div class="menu_section">
                        <h3>Live On</h3>
                        <ul class="nav side-menu">
                            <li><a><i class="fa fa-bug"></i> Additional Pages <span class="fa fa-chevron-down"></span></a>
                                <ul class="nav child_menu">
                                    <li><a href="e_commerce.html">E-commerce</a></li>
                                    <li><a href="projects.html">Projects</a></li>
                                    <li><a href="project_detail.html">Project Detail</a></li>
                                    <li><a href="contacts.html">Contacts</a></li>
                                    <li><a href="profile.html">Profile</a></li>
                                </ul>
                            </li>
                            <li><a><i class="fa fa-windows"></i> Extras <span class="fa fa-chevron-down"></span></a>
                                <ul class="nav child_menu">
                                    <li><a href="page_403.html">403 Error</a></li>
                                    <li><a href="page_404.html">404 Error</a></li>
                                    <li><a href="page_500.html">500 Error</a></li>
                                    <li><a href="plain_page.html">Plain Page</a></li>
                                    <li><a href="login.html">Login Page</a></li>
                                    <li><a href="pricing_tables.html">Pricing Tables</a></li>
                                </ul>
                            </li>
                            <li><a><i class="fa fa-sitemap"></i> Multilevel Menu <span class="fa fa-chevron-down"></span></a>
                                <ul class="nav child_menu">
                                    <li><a href="#level1_1">Level One</a>
                                    <li><a>Level One<span class="fa fa-chevron-down"></span></a>
                                        <ul class="nav child_menu">
                                            <li class="sub_menu"><a href="level2.html">Level Two</a>
                                            </li>
                                            <li><a href="#level2_1">Level Two</a>
                                            </li>
                                            <li><a href="#level2_2">Level Two</a>
                                            </li>
                                        </ul>
                                    </li>
                                    <li><a href="#level1_2">Level One</a>
                                    </li>
                                </ul>
                            </li>
                            <li><a href="javascript:void(0)"><i class="fa fa-laptop"></i> Landing Page <span class="label label-success pull-right">Coming Soon</span></a></li>
                        </ul>
                    </div>

                </div>
                <!-- /sidebar menu -->

                <!-- /menu footer buttons -->
                <div class="sidebar-footer hidden-small">
                    <a data-toggle="tooltip" data-placement="top" title="Settings">
                        <span class="glyphicon glyphicon-cog" aria-hidden="true"></span>
                    </a>
                    <a data-toggle="tooltip" data-placement="top" title="FullScreen">
                        <span class="glyphicon glyphicon-fullscreen" aria-hidden="true"></span>
                    </a>
                    <a data-toggle="tooltip" data-placement="top" title="Lock">
                        <span class="glyphicon glyphicon-eye-close" aria-hidden="true"></span>
                    </a>
                    <a data-toggle="tooltip" data-placement="top" title="Logout" href="login.html">
                        <span class="glyphicon glyphicon-off" aria-hidden="true"></span>
                    </a>
                </div>
                <!-- /menu footer buttons -->
            </div>
        </div>

        <!-- top navigation -->
        <div class="top_nav">
            <div class="nav_menu">
                <nav>
                    <div class="nav toggle">
                        <a id="menu_toggle"><i class="fa fa-bars"></i></a>
                    </div>

                    <ul class="nav navbar-nav navbar-right">
                        <li class="">
                            <a href="javascript:;" class="user-profile dropdown-toggle" data-toggle="dropdown" aria-expanded="false">
                                <img src="{{URL::asset('css2/images/img.jpg')}}" alt="">John Doe
                                <span class=" fa fa-angle-down"></span>
                            </a>
                            <ul class="dropdown-menu dropdown-usermenu pull-right">
                                <li><a href="javascript:;"> Profile</a></li>
                                <li>
                                    <a href="javascript:;">
                                        <span class="badge bg-red pull-right">50%</span>
                                        <span>Settings</span>
                                    </a>
                                </li>
                                <li><a href="javascript:;">Help</a></li>
                                <li><a href="login.html"><i class="fa fa-sign-out pull-right"></i> Log Out</a></li>
                            </ul>
                        </li>

                        <li role="presentation" class="dropdown">
                            <a href="javascript:;" class="dropdown-toggle info-number" data-toggle="dropdown" aria-expanded="false">
                                <i class="fa fa-envelope-o"></i>
                                <span class="badge bg-green">6</span>
                            </a>
                            <ul id="menu1" class="dropdown-menu list-unstyled msg_list" role="menu">
                                <li>
                                    <a>
                                        <span class="image"><img src="{{URL::asset('css2/images/img.jpg')}}" alt="Profile Image" /></span>
                        <span>
                          <span>John Smith</span>
                          <span class="time">3 mins ago</span>
                        </span>
                        <span class="message">
                          Film festivals used to be do-or-die moments for movie makers. They were where...
                        </span>
                                    </a>
                                </li>
                                <li>
                                    <a>
                                        <span class="image"><img src="{{URL::asset('css2/images/img.jpg')}}" alt="Profile Image" /></span>
                        <span>
                          <span>John Smith</span>
                          <span class="time">3 mins ago</span>
                        </span>
                        <span class="message">
                          Film festivals used to be do-or-die moments for movie makers. They were where...
                        </span>
                                    </a>
                                </li>
                                <li>
                                    <a>
                                        <span class="image"><img src="{{URL::asset('css2/images/img.jpg')}}" alt="Profile Image" /></span>
                        <span>
                          <span>John Smith</span>
                          <span class="time">3 mins ago</span>
                        </span>
                        <span class="message">
                          Film festivals used to be do-or-die moments for movie makers. They were where...
                        </span>
                                    </a>
                                </li>
                                <li>
                                    <a>
                                        <span class="image"><img src="{{URL::asset('css2/images/img.jpg')}}" alt="Profile Image" /></span>
                        <span>
                          <span>John Smith</span>
                          <span class="time">3 mins ago</span>
                        </span>
                        <span class="message">
                          Film festivals used to be do-or-die moments for movie makers. They were where...
                        </span>
                                    </a>
                                </li>
                                <li>
                                    <div class="text-center">
                                        <a>
                                            <strong>See All Alerts</strong>
                                            <i class="fa fa-angle-right"></i>
                                        </a>
                                    </div>
                                </li>
                            </ul>
                        </li>
                    </ul>
                </nav>
            </div>
        </div>
        <!-- /top navigation -->

        <!-- page content -->
        <div class="right_col" role="main">
            <!-- top tiles -->
            <div class="row tile_count">
                <div class="col-md-2 col-sm-4 col-xs-6 tile_stats_count">
                    <span class="count_top"><i class="fa fa-user"></i> تعداد کل کاربران</span>
                    <div class="count">{{$totalUsers}}</div>
                    <span class="count_bottom"><i class="green">{{$inLastWeek}} ({{round($inLastWeek * 100 / $totalUsers, 2)}} %) </i> از هفته گذشته</span>
                </div>
                <div class="col-md-2 col-sm-4 col-xs-6 tile_stats_count">
                    <span class="count_top"><i class="fa fa-clock-o"></i> تعداد کاربران تکراری</span>
                    <div class="count">{{$duplicate}}</div>
                </div>
                <div class="col-md-2 col-sm-4 col-xs-6 tile_stats_count">
                    <span class="count_top"><i class="fa fa-user"></i> تعداد کاربران آقا</span>
                    <div class="count green">{{$boys}}</div>
                    <span class="count_bottom"><i class="green"><i class="fa fa-sort-asc"></i>({{round($boys * 100 / $totalUsers, 2)}} %) </i> از کل کاربران</span>
                </div>
                <div class="col-md-2 col-sm-4 col-xs-6 tile_stats_count">
                    <span class="count_top"><i class="fa fa-user"></i> تعداد کاربران خانم</span>
                    <div class="count">{{$girls}}</div>
                    <span class="count_bottom"><i class="red"><i class="fa fa-sort-desc"></i>({{round($girls * 100 / $totalUsers, 2)}} %) </i> از کل کاربران</span>
                </div>
                <div class="col-md-2 col-sm-4 col-xs-6 tile_stats_count">
                    <span class="count_top"><i class="fa fa-user"></i> میزان تراکنش در کل</span>
                    <div class="count">{{$totalAmount}}</div>
                    <span class="count_bottom"><i class="green"><i class="fa fa-sort-asc"></i>{{$lastWeekAmount}} ({{round($lastWeekAmount * 100 / $totalAmount, 2)}} %) </i> از هفته گذشته</span>
                </div>
                <div class="col-md-2 col-sm-4 col-xs-6 tile_stats_count">
                    <span class="count_top"><i class="fa fa-user"></i>کل بازید ها</span>
                    <div class="count">{{$totalSeen}}</div>
                    <span class="count_bottom"><i class="green"><i class="fa fa-sort-asc"></i>{{$lastWeekSeen}} </i> هفته گذشته</span>
                </div>
            </div>
            <!-- /top tiles -->

            <div class="row">
                <div class="col-md-4 col-sm-4 col-xs-12">
                    <div class="x_panel">
                        <div class="x_title">
                            <h2>Recent Activities <small>Sessions</small></h2>
                            <ul class="nav navbar-right panel_toolbox">
                                <li><a class="collapse-link"><i class="fa fa-chevron-up"></i></a>
                                </li>
                                <li class="dropdown">
                                    <a href="#" class="dropdown-toggle" data-toggle="dropdown" role="button" aria-expanded="false"><i class="fa fa-wrench"></i></a>
                                    <ul class="dropdown-menu" role="menu">
                                        <li><a href="#">Settings 1</a>
                                        </li>
                                        <li><a href="#">Settings 2</a>
                                        </li>
                                    </ul>
                                </li>
                                <li><a class="close-link"><i class="fa fa-close"></i></a>
                                </li>
                            </ul>
                            <div class="clearfix"></div>
                        </div>
                        <div class="x_content">
                            <div class="dashboard-widget-content">

                                <ul id="errTests" class="list-unstyled timeline widget"></ul>
                            </div>
                        </div>
                    </div>
                </div>


                <div class="col-md-8 col-sm-8 col-xs-12">


                    <div class="row">
                        <!-- Start to do list -->
                        <div class="col-md-6 col-sm-6 col-xs-12">
                            <div class="x_panel">
                                <div class="x_title">
                                    <h2>Tests <small>Sample tasks</small></h2>
                                    <ul class="nav navbar-right panel_toolbox">
                                        <li><a class="collapse-link"><i class="fa fa-chevron-up"></i></a>
                                        </li>
                                        <li class="dropdown">
                                            <a href="#" class="dropdown-toggle" data-toggle="dropdown" role="button" aria-expanded="false"><i class="fa fa-wrench"></i></a>
                                            <ul class="dropdown-menu" role="menu">
                                                <li><a href="#">Settings 1</a>
                                                </li>
                                                <li><a href="#">Settings 2</a>
                                                </li>
                                            </ul>
                                        </li>
                                        <li><a class="close-link"><i class="fa fa-close"></i></a>
                                        </li>
                                    </ul>
                                    <div class="clearfix"></div>
                                </div>
                                <div class="x_content">
                                    <div>
                                        <ul class="to_do">
                                            <div class="widget_summary">
                                                <div class="w_left w_25">
                                                    <span>done</span>
                                                </div>
                                                <div class="w_center w_55">
                                                    <div class="progress">
                                                        <div id="testProgressBar" class="progress-bar bg-green" role="progressbar"></div>
                                                    </div>
                                                </div>
                                                <div class="w_right w_20">
                                                    <span id="doneTests"></span>
                                                </div>
                                                <div class="clearfix"></div>
                                            </div>
                                        </ul>
                                    </div>
                                </div>
                            </div>
                        </div>
                        <!-- End to do list -->

                        <div class="col-md-6 col-sm-6 col-xs-12">
                            <div class="row tile_count">
                                <div class="col-md-4 col-sm-4 col-xs-4 tile_stats_count">
                                    <span class="count_top"><i class="fa fa-line-chart"></i> Total tests</span>
                                    <div class="count">{{count($methods)}}</div>
                                </div>
                                <div class="col-md-4 col-sm-4 col-xs-4 tile_stats_count">
                                    <span class="count_top"><i class="fa fa-check"></i> Successful</span>
                                    <div class="count" id="successfulTests"></div>
                                    <span class="count_bottom"><i class="green" id="percentSuccessfulTests"><i class="fa fa-percent"></i></i></span>
                                </div>
                                <div class="col-md-4 col-sm-4 col-xs-4 tile_stats_count">
                                    <span class="count_top"><i class="fa fa-remove"></i> Fail</span>
                                    <div class="count green" id="failedTest"></div>
                                    <span class="count_bottom"><i class="green" id="percentFailedTest"><i class="fa fa-percent"></i></i></span>
                                </div>
                            </div>
                        </div>

                    </div>

                    <div class="row">

                        <!-- start of weather widget -->
                        <div class="col-md-4 col-sm-4 col-xs-4">
                            <div class="x_panel">
                                <div class="x_title">
                                    <h2>Daily active users <small>Sessions</small></h2>
                                    <ul class="nav navbar-right panel_toolbox">
                                        <li><a class="collapse-link"><i class="fa fa-chevron-up"></i></a>
                                        </li>
                                        <li class="dropdown">
                                            <a href="#" class="dropdown-toggle" data-toggle="dropdown" role="button" aria-expanded="false"><i class="fa fa-wrench"></i></a>
                                            <ul class="dropdown-menu" role="menu">
                                                <li><a href="#">Settings 1</a>
                                                </li>
                                                <li><a href="#">Settings 2</a>
                                                </li>
                                            </ul>
                                        </li>
                                        <li><a class="close-link"><i class="fa fa-close"></i></a>
                                        </li>
                                    </ul>
                                    <div class="clearfix"></div>
                                </div>
                                <div class="x_content">
                                    <div class="row">
                                        <div class="col-sm-12">
                                            <div class="temperature"><b>Monday</b>, 07:30 AM
                                                <span>F</span>
                                                <span><b>C</b></span>
                                            </div>
                                        </div>
                                    </div>
                                    <div class="row">
                                        <div class="col-sm-4">
                                            <div class="weather-icon">
                                                <canvas height="84" width="84" id="partly-cloudy-day"></canvas>
                                            </div>
                                        </div>
                                        <div class="col-sm-8">
                                            <div class="weather-text">
                                                <h2>Texas <br><i>Partly Cloudy Day</i></h2>
                                            </div>
                                        </div>
                                    </div>
                                    <div class="col-sm-12">
                                        <div class="weather-text pull-right">
                                            <h3 class="degrees">23</h3>
                                        </div>
                                    </div>

                                    <div class="clearfix"></div>

                                    <div class="row weather-days">
                                        <div class="col-sm-2">
                                            <div class="daily-weather">
                                                <h2 class="day">Mon</h2>
                                                <h3 class="degrees">25</h3>
                                                <canvas id="clear-day" width="32" height="32"></canvas>
                                                <h5>15 <i>km/h</i></h5>
                                            </div>
                                        </div>
                                        <div class="col-sm-2">
                                            <div class="daily-weather">
                                                <h2 class="day">Tue</h2>
                                                <h3 class="degrees">25</h3>
                                                <canvas height="32" width="32" id="rain"></canvas>
                                                <h5>12 <i>km/h</i></h5>
                                            </div>
                                        </div>
                                        <div class="col-sm-2">
                                            <div class="daily-weather">
                                                <h2 class="day">Wed</h2>
                                                <h3 class="degrees">27</h3>
                                                <canvas height="32" width="32" id="snow"></canvas>
                                                <h5>14 <i>km/h</i></h5>
                                            </div>
                                        </div>
                                        <div class="col-sm-2">
                                            <div class="daily-weather">
                                                <h2 class="day">Thu</h2>
                                                <h3 class="degrees">28</h3>
                                                <canvas height="32" width="32" id="sleet"></canvas>
                                                <h5>15 <i>km/h</i></h5>
                                            </div>
                                        </div>
                                        <div class="col-sm-2">
                                            <div class="daily-weather">
                                                <h2 class="day">Fri</h2>
                                                <h3 class="degrees">28</h3>
                                                <canvas height="32" width="32" id="wind"></canvas>
                                                <h5>11 <i>km/h</i></h5>
                                            </div>
                                        </div>
                                        <div class="col-sm-2">
                                            <div class="daily-weather">
                                                <h2 class="day">Sat</h2>
                                                <h3 class="degrees">26</h3>
                                                <canvas height="32" width="32" id="cloudy"></canvas>
                                                <h5>10 <i>km/h</i></h5>
                                            </div>
                                        </div>
                                        <div class="clearfix"></div>
                                    </div>
                                </div>
                            </div>

                        </div>
                        <!-- end of weather widget -->

                        <div class="col-md-8 col-sm-8 col-xs-8">
                            <div class="x_panel">
                                <div class="x_title">
                                    <h2>Visitors location <small>geo-presentation</small></h2>
                                    <ul class="nav navbar-right panel_toolbox">
                                        <li><a class="collapse-link"><i class="fa fa-chevron-up"></i></a>
                                        </li>
                                        <li class="dropdown">
                                            <a href="#" class="dropdown-toggle" data-toggle="dropdown" role="button" aria-expanded="false"><i class="fa fa-wrench"></i></a>
                                            <ul class="dropdown-menu" role="menu">
                                                <li><a href="#">Settings 1</a>
                                                </li>
                                                <li><a href="#">Settings 2</a>
                                                </li>
                                            </ul>
                                        </li>
                                        <li><a class="close-link"><i class="fa fa-close"></i></a>
                                        </li>
                                    </ul>
                                    <div class="clearfix"></div>
                                </div>
                                <div class="x_content">
                                    <div class="dashboard-widget-content">
                                        <div class="col-md-4 hidden-small">
                                            <h2 class="line_30">125.7k Views from 60 countries</h2>

                                            <table class="countries_list">
                                                <tbody>
                                                <tr>
                                                    <td>United States</td>
                                                    <td class="fs15 fw700 text-right">33%</td>
                                                </tr>
                                                <tr>
                                                    <td>France</td>
                                                    <td class="fs15 fw700 text-right">27%</td>
                                                </tr>
                                                <tr>
                                                    <td>Germany</td>
                                                    <td class="fs15 fw700 text-right">16%</td>
                                                </tr>
                                                <tr>
                                                    <td>Spain</td>
                                                    <td class="fs15 fw700 text-right">11%</td>
                                                </tr>
                                                <tr>
                                                    <td>Britain</td>
                                                    <td class="fs15 fw700 text-right">10%</td>
                                                </tr>
                                                </tbody>
                                            </table>
                                        </div>
                                        <div id="world-map-gdp" class="col-md-8 col-sm-12 col-xs-12" style="height:230px;"></div>
                                    </div>
                                </div>
                            </div>
                        </div>

                    </div>

                </div>
            </div>

            <div class="row">
                <div class="col-md-12 col-sm-12 col-xs-12">
                    <div class="dashboard_graph">

                        <div class="row x_title">
                            <div class="col-md-6">
                                <h3>Network Activities <small>Graph title sub-title</small></h3>
                            </div>
                            <div class="col-md-6">
                                <div id="reportrange" class="pull-right" style="background: #fff; cursor: pointer; padding: 5px 10px; border: 1px solid #ccc">
                                    <i class="glyphicon glyphicon-calendar fa fa-calendar"></i>
                                    <span>December 30, 2014 - January 28, 2015</span> <b class="caret"></b>
                                </div>
                            </div>
                        </div>

                        <div class="col-md-9 col-sm-9 col-xs-12">
                            <div id="chart_plot_01" class="demo-placeholder"></div>
                        </div>
                        <div class="col-md-3 col-sm-3 col-xs-12 bg-white">
                            <div class="x_title">
                                <h2>Top Campaign Performance</h2>
                                <div class="clearfix"></div>
                            </div>

                            <div class="col-md-12 col-sm-12 col-xs-6">
                                <div>
                                    <p>Facebook Campaign</p>
                                    <div class="">
                                        <div class="progress progress_sm" style="width: 76%;">
                                            <div class="progress-bar bg-green" role="progressbar" data-transitiongoal="80"></div>
                                        </div>
                                    </div>
                                </div>
                                <div>
                                    <p>Twitter Campaign</p>
                                    <div class="">
                                        <div class="progress progress_sm" style="width: 76%;">
                                            <div class="progress-bar bg-green" role="progressbar" data-transitiongoal="60"></div>
                                        </div>
                                    </div>
                                </div>
                            </div>
                            <div class="col-md-12 col-sm-12 col-xs-6">
                                <div>
                                    <p>Conventional Media</p>
                                    <div class="">
                                        <div class="progress progress_sm" style="width: 76%;">
                                            <div class="progress-bar bg-green" role="progressbar" data-transitiongoal="40"></div>
                                        </div>
                                    </div>
                                </div>
                                <div>
                                    <p>Bill boards</p>
                                    <div class="">
                                        <div class="progress progress_sm" style="width: 76%;">
                                            <div class="progress-bar bg-green" role="progressbar" data-transitiongoal="50"></div>
                                        </div>
                                    </div>
                                </div>
                            </div>

                        </div>

                        <div class="clearfix"></div>
                    </div>
                </div>

            </div>
            <br />

            <div class="row">


                <div class="col-md-4 col-sm-4 col-xs-12">
                    <div class="x_panel tile fixed_height_320">
                        <div class="x_title">
                            <h2>بازدید های اخیر (ماه اخیر)</h2>
                            <ul class="nav navbar-right panel_toolbox">
                                <li><a class="collapse-link"><i class="fa fa-chevron-up"></i></a>
                                </li>
                                <li class="dropdown">
                                    <a href="#" class="dropdown-toggle" data-toggle="dropdown" role="button" aria-expanded="false"><i class="fa fa-wrench"></i></a>
                                    <ul class="dropdown-menu" role="menu">
                                        <li><a href="#">Settings 1</a>
                                        </li>
                                        <li><a href="#">Settings 2</a>
                                        </li>
                                    </ul>
                                </li>
                                <li><a class="close-link"><i class="fa fa-close"></i></a>
                                </li>
                            </ul>
                            <div class="clearfix"></div>
                        </div>
                        <div class="x_content">

                            @foreach($logs as $log)
                                <h4>{{$log->url}}</h4>
                                <div class="widget_summary">
                                    <div class="w_left w_25">
                                        <span>تعداد بازدید: {{$log->counter}}</span>
                                    </div>
                                    <div class="w_center w_55">
                                        <div class="progress">
                                            <div style="width: {{round($log->counter * 100 / $totalSeen, 2)}}%; background-color: {{'#' . rand(100000, 999999)}}" class="progress-bar" role="progressbar" aria-valuenow="60" aria-valuemin="0" aria-valuemax="100">
                                                <span class="sr-only">60% Complete</span>
                                            </div>
                                        </div>
                                    </div>
                                    <div class="w_right w_20">
                                        <span>{{round($log->counter * 100 / $totalSeen, 2)}}%</span>
                                    </div>
                                    <div class="clearfix"></div>
                                </div>
                            @endforeach

                        </div>
                    </div>
                </div>

                <div class="col-md-4 col-sm-4 col-xs-12">
                    <div class="x_panel tile fixed_height_320 overflow_hidden">
                        <div class="x_title">
                            <h2>Device Usage</h2>
                            <ul class="nav navbar-right panel_toolbox">
                                <li><a class="collapse-link"><i class="fa fa-chevron-up"></i></a>
                                </li>
                                <li class="dropdown">
                                    <a href="#" class="dropdown-toggle" data-toggle="dropdown" role="button" aria-expanded="false"><i class="fa fa-wrench"></i></a>
                                    <ul class="dropdown-menu" role="menu">
                                        <li><a href="#">Settings 1</a>
                                        </li>
                                        <li><a href="#">Settings 2</a>
                                        </li>
                                    </ul>
                                </li>
                                <li><a class="close-link"><i class="fa fa-close"></i></a>
                                </li>
                            </ul>
                            <div class="clearfix"></div>
                        </div>
                        <div class="x_content">
                            <table class="" style="width:100%">
                                <tr>
                                    <th style="width:37%;">
                                        <p>Top 5</p>
                                    </th>
                                </tr>
                                <tr>
                                    <td>
                                        <canvas class="canvasDoughnut" height="140" width="140" style="margin: 15px 10px 10px 0"></canvas>
                                    </td>
                                    <td>
                                        <table class="tile_info">

                                            @foreach($logs as $log)
                                                <tr>
                                                    <td>
                                                        <p><i style="color: {{'#' . rand(100000, 999999)}}" class="fa fa-square"></i>{{$log->url}} </p>
                                                    </td>
                                                    <td>{{$log->counter}}%</td>
                                                </tr>
                                            @endforeach

                                        </table>
                                    </td>
                                </tr>
                            </table>
                        </div>
                    </div>
                </div>


                <div class="col-md-4 col-sm-4 col-xs-12">
                    <div class="x_panel tile fixed_height_320">
                        <div class="x_title">
                            <h2>Quick Settings</h2>
                            <ul class="nav navbar-right panel_toolbox">
                                <li><a class="collapse-link"><i class="fa fa-chevron-up"></i></a>
                                </li>
                                <li class="dropdown">
                                    <a href="#" class="dropdown-toggle" data-toggle="dropdown" role="button" aria-expanded="false"><i class="fa fa-wrench"></i></a>
                                    <ul class="dropdown-menu" role="menu">
                                        <li><a href="#">Settings 1</a>
                                        </li>
                                        <li><a href="#">Settings 2</a>
                                        </li>
                                    </ul>
                                </li>
                                <li><a class="close-link"><i class="fa fa-close"></i></a>
                                </li>
                            </ul>
                            <div class="clearfix"></div>
                        </div>
                        <div class="x_content">
                            <div class="dashboard-widget-content">
                                <ul class="quick-list">
                                    <li><i class="fa fa-calendar-o"></i><a href="#">Settings</a>
                                    </li>
                                    <li><i class="fa fa-bars"></i><a href="#">Subscription</a>
                                    </li>
                                    <li><i class="fa fa-bar-chart"></i><a href="#">Auto Renewal</a> </li>
                                    <li><i class="fa fa-line-chart"></i><a href="#">Achievements</a>
                                    </li>
                                    <li><i class="fa fa-bar-chart"></i><a href="#">Auto Renewal</a> </li>
                                    <li><i class="fa fa-line-chart"></i><a href="#">Achievements</a>
                                    </li>
                                    <li><i class="fa fa-area-chart"></i><a href="#">Logout</a>
                                    </li>
                                </ul>

                                <div class="sidebar-widget">
                                    <h4>Profile Completion</h4>
                                    <canvas width="150" height="80" id="chart_gauge_01" class="" style="width: 160px; height: 100px;"></canvas>
                                    <div class="goal-wrapper">
                                        <span id="gauge-text" class="gauge-value pull-left">0</span>
                                        <span class="gauge-value pull-left">%</span>
                                        <span id="goal-text" class="goal-value pull-right">100%</span>
                                    </div>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>

            </div>

        </div>
        <!-- /page content -->

        <!-- footer content -->
        <footer>
            <div class="pull-right">
                Gentelella - Bootstrap Admin Template by <a href="https://colorlib.com">Colorlib</a>
            </div>
            <div class="clearfix"></div>
        </footer>
        <!-- /footer content -->
    </div>
</div>

<!-- jQuery -->
<script src="{{URL::asset('css2/jquery/dist/jquery.min.js')}}"></script>
<!-- Bootstrap -->
<script src="{{URL::asset('css2/bootstrap/dist/js/bootstrap.min.js')}}"></script>
<!-- FastClick -->
<script src="{{URL::asset('css2/fastclick/lib/fastclick.js')}}"></script>
<!-- NProgress -->
<script src="{{URL::asset('css2/nprogress/nprogress.js')}}"></script>
<!-- Chart.js -->
<script src="{{URL::asset('css2/Chart.js/dist/Chart.min.js')}}"></script>
<!-- gauge.js -->
<script src="{{URL::asset('css2/gauge.js/dist/gauge.min.js')}}"></script>
<!-- bootstrap-progressbar -->
<script src="{{URL::asset('css2/bootstrap-progressbar/bootstrap-progressbar.min.js')}}"></script>
<!-- iCheck -->
<script src="{{URL::asset('css2/iCheck/icheck.min.js')}}"></script>
<!-- Skycons -->
<script src="{{URL::asset('css2/skycons/skycons.js')}}"></script>
<!-- Flot -->
<script src="{{URL::asset('css2/Flot/jquery.flot.js')}}"></script>
<script src="{{URL::asset('css2/Flot/jquery.flot.pie.js')}}"></script>
<script src="{{URL::asset('css2/Flot/jquery.flot.time.js')}}"></script>
<script src="{{URL::asset('css2/Flot/jquery.flot.stack.js')}}"></script>
<script src="{{URL::asset('css2/Flot/jquery.flot.resize.js')}}"></script>
<!-- Flot plugins -->
<script src="{{URL::asset('css2/flot.orderbars/js/jquery.flot.orderBars.js')}}"></script>
<script src="{{URL::asset('css2/flot-spline/js/jquery.flot.spline.min.js')}}"></script>
<script src="{{URL::asset('css2/flot.curvedlines/curvedLines.js')}}"></script>
<!-- DateJS -->
<script src="{{URL::asset('css2/DateJS/build/date.js')}}"></script>
<!-- JQVMap -->
<script src="{{URL::asset('css2/jqvmap/dist/jquery.vmap.js')}}"></script>
<script src="{{URL::asset('css2/jqvmap/dist/maps/jquery.vmap.world.js')}}"></script>
<script src="{{URL::asset('css2/jqvmap/examples/js/jquery.vmap.sampledata.js')}}"></script>
<script src="{{URL::asset('css2/moment/min/moment.min.js')}}"></script>
<script src="{{URL::asset('css2/bootstrap-daterangepicker/daterangepicker.js')}}"></script>

<!-- Custom Theme Scripts -->
<script src="{{URL::asset('css2/build/js/custom.min.js')}}"></script>
<!-- Google Analytics -->
<script>
    (function(i,s,o,g,r,a,m){i['GoogleAnalyticsObject']=r;i[r]=i[r]||function(){
                (i[r].q=i[r].q||[]).push(arguments)},i[r].l=1*new Date();a=s.createElement(o),
            m=s.getElementsByTagName(o)[0];a.async=1;a.src=g;m.parentNode.insertBefore(a,m)
    })(window,document,'script','https://www.google-analytics.com/analytics.js','ga');

    ga('create', 'UA-23581568-13', 'auto');
    ga('send', 'pageview');

</script>


<script>

    var methods = {!! json_encode($methods) !!};
    var done = 0;
    var total = 0;
    var failed = 0;
    var cookie = '{{$cookie}}';

    $(document).ready(function () {

        total = methods.length;

        for(i = 0; i < total; i++)
            getTestStatus(methods[i]);

    });

    function getTestStatus(methodName) {

        $.ajax({
            'type': 'post',
            'url': '{{route('testMethod')}}',
            'data': {
                'method': methodName,
                'cookie': cookie
            },
            success: function(response) {

                if(response == "ok") {
                    $(".to_do").append('<li><p><input readonly checked type="checkbox" class="flat">' + methodName + '</p></li>');
                }
                else if(response.length > 0) {
                    failed++;
                    $(".to_do").append('<li><p><input readonly type="checkbox" class="flat">' + methodName + '</p></li>');
                    $("#errTests").append('<li><div class="block"><div class="block_content"><h2 class="title">' + '<a>' + methodName + '</a>' + '</h2><p style="overflow: auto" class="excerpt">' + response + '</p></div></div></li>');
                }

                $("#doneTests").empty().append(++done);
                $("#testProgressBar").css("width", (done * 100 / total) + '%');

                $("#successfulTests").empty().append(total - failed);
                $("#percentSuccessfulTests").empty().append((total - failed) * 100 / total);
                $("#failedTest").empty().append(failed);
                $("#percentFailedTest").empty().append(failed * 100 / total);
            }
        });
    }

</script>

</body>
</html>