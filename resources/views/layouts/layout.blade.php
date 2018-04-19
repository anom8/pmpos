<?php $user = Auth::user(); ?>

<!DOCTYPE html>
<html lang="en">
    <head>
      <meta charset="utf-8">
      <meta name="viewport" content="width=device-width, initial-scale=1.0">
      <meta name="description" content="A fully featured admin theme which can be used to build CRM, CMS, etc.">
      <meta name="author" content="Coderthemes">

      <!-- App favicon -->
      {{-- <link rel="shortcut icon" href="https://shortir.com/assets/img/favicon.png"> --}}

      <title>@yield('title') - PM POS</title>

      <link href="{{ URL::asset('assets/css/bootstrap.min.css') }}" rel="stylesheet">
      {{-- <link href="{{ URL::asset('assets/css/style.css') }}" rel="stylesheet"> --}}
      <link href="{{ URL::asset('assets/css/core.css?v=1') }}" rel="stylesheet">
      <link href="{{ URL::asset('assets/css/components.css') }}" rel="stylesheet">
      <link href="{{ URL::asset('assets/css/icons.css') }}" rel="stylesheet">
      <link href="{{ URL::asset('assets/css/pages.css') }}" rel="stylesheet">
      <link href="{{ URL::asset('assets/css/menu.css') }}" rel="stylesheet">
      <link href="{{ URL::asset('assets/css/responsive.css') }}" rel="stylesheet">
      <link href="{{ URL::asset('assets/css/style.css') }}" rel="stylesheet">
      <link href="{{ URL::asset('assets/plugins/switchery/switchery.min.css') }}" rel="stylesheet">
      <link href="{{ URL::asset('assets/plugins/bootstrap-tagsinput/css/bootstrap-tagsinput.css') }}" rel="stylesheet">
      <link href="{{ URL::asset('assets/plugins/bootstrap-touchspin/css/jquery.bootstrap-touchspin.min.css') }}" rel="stylesheet">
      <link href="{{ URL::asset('assets/plugins/select2/css/select2.min.css') }}" rel="stylesheet">

      <!-- DataTables -->
        <link href="{{ URL::asset('assets/plugins/datatables/jquery.dataTables.min.css') }}" rel="stylesheet">
        <link href="{{ URL::asset('assets/plugins/datatables/buttons.bootstrap.min.css') }}" rel="stylesheet">
        <link href="{{ URL::asset('assets/plugins/datatables/fixedHeader.bootstrap.min.css') }}" rel="stylesheet">
        <link href="{{ URL::asset('assets/plugins/datatables/responsive.bootstrap.min.css') }}" rel="stylesheet">
        <link href="{{ URL::asset('assets/plugins/datatables/scroller.bootstrap.min.css') }}" rel="stylesheet">
        <link href="{{ URL::asset('assets/plugins/datatables/dataTables.colVis.css') }}" rel="stylesheet">
        <link href="{{ URL::asset('assets/plugins/datatables/dataTables.bootstrap.min.css') }}" rel="stylesheet">
        <link href="{{ URL::asset('assets/plugins/datatables/fixedColumns.dataTables.min.css') }}" rel="stylesheet">

      @yield('styles')

      <!--[if lt IE 9]>
        <script src="//oss.maxcdn.com/html5shiv/3.7.2/html5shiv.min.js"></script>
        <script src="//oss.maxcdn.com/respond/1.4.2/respond.min.js"></script>
      <![endif]-->
      
      <script src="{{ URL::asset('assets/js/modernizr.min.js') }}"></script>
      
    </head>
    <body class="fixed-left">

        <div id="wrapper">

            <!-- Top Bar Start -->
            <div class="topbar">

                <!-- LOGO -->
                <div class="topbar-left">
                    <a href="#" class="logo">
                        <span>PM <span>POS</span></span>
                    </a>
                    <!-- Image logo -->
                    <!--<a href="index.html" class="logo">-->
                        <!--<span>-->
                            <!--<img src="assets/images/logo.png" alt="" height="30">-->
                        <!--</span>-->
                        <!--<i>-->
                            <!--<img src="assets/images/logo_sm.png" alt="" height="28">-->
                        <!--</i>-->
                    <!--</a>-->
                </div>

                <!-- Button mobile view to collapse sidebar menu -->
                <div class="navbar navbar-default" role="navigation">
                    <div class="container">

                        <!-- Navbar-left -->
                        <ul class="nav navbar-nav navbar-left">
                            <li>
                                <button class="button-menu-mobile open-left waves-effect">
                                    <i class="mdi mdi-menu"></i>
                                </button>
                            </li>
                        </ul>

                        <!-- Right(Notification) -->
                        <ul class="nav navbar-nav navbar-right">

                            <li class="dropdown user-box">
                                <a href="" class="dropdown-toggle waves-effect user-link" data-toggle="dropdown" aria-expanded="true">
                                    {{ HTML::image('assets/images/default_avatar.png', null, array('width'=>'100%', 'alt' => 'user-img', 'class' => 'img-circle user-img')) }}
                                </a>

                                <ul class="dropdown-menu dropdown-menu-right arrow-dropdown-menu arrow-menu-right user-list notify-list">
                                    <li>
                                        <h5>Hi, {{ $user->name }}</h5>
                                    </li>
                                    <li><a href="{{ URL::to('profile') }}"><i class="ti-user m-r-5"></i> Profile</a></li>
                                    <li><a href="{{ URL::to('logout') }}"><i class="ti-power-off m-r-5"></i> Logout</a></li>
                                </ul>
                            </li>

                        </ul> <!-- end navbar-right -->

                    </div><!-- end container -->
                </div><!-- end navbar -->
            </div>
            <!-- Top Bar End -->


            <!-- ========== Left Sidebar Start ========== -->
            <div class="left side-menu">
                <div class="sidebar-inner slimscrollleft">

                    <!--- Sidemenu -->
                    <div id="sidebar-menu">
                        <ul>
                            <li class="menu-title">Transaction</li>

                            <li><a href="{{ route('/') }}"><i class="mdi mdi-view-dashboard"></i> <span>Dashboard</span></a></li>

                            @if ($user->role_id==1)
                                <li><a href="{{ route('transaction-current') }}"><i class="mdi mdi-alert-circle-outline"></i> <span>Current Order</span></a></li>

                                <li><a href="{{ route('transaction.create') }}"><i class="mdi mdi-plus-circle"></i> <span>New Order</span></a></li>

                                <li><a href="{{ route('transaction-history.index') }}"><i class="mdi mdi-calendar-clock"></i> <span>History Order</span></a></li>
                                
                                <br>
                                <li class="menu-title">Configuration</li>
                                <li class="has_sub">
                                    <a href="javascript:void(0);" class="waves-effect"><i class="mdi mdi mdi-chart-bubble"></i> <span> Product </span> <span class="menu-arrow"></span></a>
                                    <ul class="list-unstyled">
                                        <li><a href="{{ route('product.index') }}">List</a></li>
                                    </ul>
                                </li>

                                <li class="has_sub">
                                    <a href="javascript:void(0);" class="waves-effect"><i class="mdi mdi-account-multiple"></i> <span> User </span> <span class="menu-arrow"></span></a>
                                    <ul class="list-unstyled">
                                        <li><a href="{{ route('user.create') }}">Create</a></li>
                                        <li><a href="{{ route('user.index') }}">List</a></li>
                                    </ul>
                                </li>

                                <li class="has_sub">
                                    <a href="javascript:void(0);" class="waves-effect"><i class="mdi mdi-account-check"></i> <span> Role </span> <span class="menu-arrow"></span></a>
                                    <ul class="list-unstyled">
                                        <li><a href="{{ route('/') }}">Create</a></li>
                                        <li><a href="{{ route('/') }}">List</a></li>
                                    </ul>
                                </li>

                                <li><a href="{{ route('config.index') }}"><i class="mdi mdi-settings"></i> <span>Config</span></a></li>
                            @elseif($user->role_id==2)
                                <li><a href="{{ route('transaction-current') }}"><i class="mdi mdi-alert-circle-outline"></i> <span>Current Order</span></a></li>

                                <li><a href="{{ route('transaction.create') }}"><i class="mdi mdi-plus-circle"></i> <span>New Order</span></a></li>

                                <li><a href="{{ route('transaction-history.index') }}"><i class="mdi mdi-calendar-clock"></i> <span>History Order</span></a></li>  

                                <br>
                                <li class="menu-title">Configuration</li>
                                <li class="has_sub">
                                    <a href="javascript:void(0);" class="waves-effect"><i class="mdi mdi mdi-chart-bubble"></i> <span> Product </span> <span class="menu-arrow"></span></a>
                                    <ul class="list-unstyled">
                                        <li><a href="{{ route('product.index') }}">List</a></li>
                                    </ul>
                                </li>

                            @elseif($user->role_id==5)
                                <li><a href="{{ route('transaction-current') }}"><i class="mdi mdi-alert-circle-outline"></i> <span>Current Order</span></a></li>

                                <li><a href="{{ route('transaction.create') }}"><i class="mdi mdi-plus-circle"></i> <span>New Order</span></a></li>
                            @endif

                            <br>

                            <li class="menu-title">Account</li>
                            <li><a href="{{ URL::to('logout') }}"><i class="ti-power-off"></i> <span>Logout</span></a></li>
                            {{-- <li><a href="{{ URL::to('logout') }}"><i class="ti-power-off m-r-5"></i> Logout</a></ --}}

                        </ul>
                    </div>
                    <!-- Sidebar -->
                    <div class="clearfix"></div>

                    <!-- <div class="help-box">
                        <h5 class="text-muted m-t-0">For Help ?</h5>
                        <p class=""><span class="text-custom">Email:</span> <br/> support@support.com</p>
                        <p class="m-b-0"><span class="text-custom">Call:</span> <br/> (+123) 123 456 789</p>
                    </div> -->

                </div>
                <!-- Sidebar -left -->

            </div>
            <!-- Left Sidebar End -->



            <!-- ============================================================== -->
            <!-- Start right Content here -->
            <!-- ============================================================== -->
            <div class="content-page">
                
                @yield('content')

            </div>


            <!-- ============================================================== -->
            <!-- End Right content here -->
            <!-- ============================================================== -->


            <!-- Right Sidebar -->
            <div class="side-bar right-bar">
                <a href="javascript:void(0);" class="right-bar-toggle">
                    <i class="mdi mdi-close-circle-outline"></i>
                </a>
                <h4 class="">Settings</h4>
                <div class="setting-list nicescroll">
                    <div class="row m-t-20">
                        <div class="col-xs-8">
                            <h5 class="m-0">Notifications</h5>
                            <p class="text-muted m-b-0"><small>Do you need them?</small></p>
                        </div>
                        <div class="col-xs-4 text-right">
                            <input type="checkbox" checked data-plugin="switchery" data-color="#7fc1fc" data-size="small"/>
                        </div>
                    </div>

                    <div class="row m-t-20">
                        <div class="col-xs-8">
                            <h5 class="m-0">API Access</h5>
                            <p class="m-b-0 text-muted"><small>Enable/Disable access</small></p>
                        </div>
                        <div class="col-xs-4 text-right">
                            <input type="checkbox" checked data-plugin="switchery" data-color="#7fc1fc" data-size="small"/>
                        </div>
                    </div>

                    <div class="row m-t-20">
                        <div class="col-xs-8">
                            <h5 class="m-0">Auto Updates</h5>
                            <p class="m-b-0 text-muted"><small>Keep up to date</small></p>
                        </div>
                        <div class="col-xs-4 text-right">
                            <input type="checkbox" checked data-plugin="switchery" data-color="#7fc1fc" data-size="small"/>
                        </div>
                    </div>

                    <div class="row m-t-20">
                        <div class="col-xs-8">
                            <h5 class="m-0">Online Status</h5>
                            <p class="m-b-0 text-muted"><small>Show your status to all</small></p>
                        </div>
                        <div class="col-xs-4 text-right">
                            <input type="checkbox" checked data-plugin="switchery" data-color="#7fc1fc" data-size="small"/>
                        </div>
                    </div>
                </div>
            </div>
            <!-- /Right-bar -->

        </div>
        <!-- END wrapper -->



        <script>
            var resizefunc = [];
        </script>

        {{-- <script src="{{ URL::asset('assets/js/jquery.min.js') }}"></script> --}}
        <script src="{{ URL::asset('assets/js/jquery.min.js') }}"></script>
        <script src="{{ URL::asset('assets/js/bootstrap.min.js') }}"></script>
        <script src="{{ URL::asset('assets/js/detect.js') }}"></script>
        <script src="{{ URL::asset('assets/js/fastclick.js') }}"></script>
        <script src="{{ URL::asset('assets/js/jquery.blockUI.js') }}"></script>
        <script src="{{ URL::asset('assets/js/waves.js') }}"></script>
        <script src="{{ URL::asset('assets/js/jquery.slimscroll.js') }}"></script>
        <script src="{{ URL::asset('assets/js/jquery.scrollTo.min.js') }}"></script>
        <script src="{{ URL::asset('assets/plugins/switchery/switchery.min.js') }}"></script>
        <script src="{{ URL::asset('assets/plugins/select2/js/select2.min.js') }}"></script>

        <!-- Counter JS -->
        <script src="{{ URL::asset('assets/plugins/waypoints/jquery.waypoints.min.js') }}"></script>
        <script src="{{ URL::asset('assets/plugins/counterup/jquery.counterup.min.js') }}"></script>
        <script src="{{ URL::asset('assets/plugins/bootstrap-filestyle/js/bootstrap-filestyle.min.js') }}"></script>

        <!-- App js -->
        <script src="{{ URL::asset('assets/js/jquery.core.js') }}"></script>
        <script src="{{ URL::asset('assets/js/jquery.app.js') }}"></script>

        <!-- Counter JS -->
        <script src="{{ URL::asset('assets/plugins/waypoints/jquery.waypoints.min.js') }}"></script>
        <script src="{{ URL::asset('assets/plugins/counterup/jquery.counterup.min.js') }}"></script>
        <script src="{{ URL::asset('assets/plugins/bootstrap-filestyle/js/bootstrap-filestyle.min.js') }}"></script>

        <script src="{{ URL::asset('assets/plugins/datatables/jquery.dataTables.min.js') }}"></script>
        <script src="{{ URL::asset('assets/plugins/datatables/dataTables.bootstrap.js') }}"></script>

        <script src="{{ URL::asset('assets/plugins/datatables/dataTables.buttons.min.js') }}"></script>
        <script src="{{ URL::asset('assets/plugins/datatables/buttons.bootstrap.min.js') }}"></script>
        <script src="{{ URL::asset('assets/plugins/datatables/jszip.min.js') }}"></script>
        <script src="{{ URL::asset('assets/plugins/datatables/pdfmake.min.js') }}"></script>
        <script src="{{ URL::asset('assets/plugins/datatables/vfs_fonts.js') }}"></script>
        <script src="{{ URL::asset('assets/plugins/datatables/buttons.html5.min.js') }}"></script>
        <script src="{{ URL::asset('assets/plugins/datatables/buttons.print.min.js') }}"></script>
        <script src="{{ URL::asset('assets/plugins/datatables/dataTables.fixedHeader.min.js') }}"></script>
        <script src="{{ URL::asset('assets/plugins/datatables/dataTables.keyTable.min.js') }}"></script>
        <script src="{{ URL::asset('assets/plugins/datatables/dataTables.responsive.min.js') }}"></script>
        <script src="{{ URL::asset('assets/plugins/datatables/responsive.bootstrap.min.js') }}"></script>
        <script src="{{ URL::asset('assets/plugins/datatables/dataTables.scroller.min.js') }}"></script>
        <script src="{{ URL::asset('assets/plugins/datatables/dataTables.colVis.js') }}"></script>
        <script src="{{ URL::asset('assets/plugins/datatables/dataTables.fixedColumns.min.js') }}"></script>

        <script src="{{ URL::asset('assets/plugins/socket/socket.io.js') }}"></script>
        <script type="text/javascript">
            $(document).ready(function() {
                // const socket = io.connect('http://localhost:3003');

                // socket.on('connect', function () {
                //     console.log('Connected');
                // });

                // socket.on('announcement', function (data) {
                //     console.log(data);
                //     // var content = data;
                //     $('.order-list').append(data); 
                //     // $('#video').get(0).play();
                // });

                $('.btn-menu, .btn-close').on('click', function() {
                    if ($('body').hasClass('open')) {
                        $('body').removeClass('open');
                        // $('.btn-menu i').attr('class', 'fa fa-bars');
                    } else {
                        $('body').addClass('open');
                        // $('.btn-menu i').attr('class', 'fa fa-times');
                    }
                });
            });
        </script>

        <script>
            $(document).ready(function() {
                $('[data-toggle="tooltip"]').tooltip();
            });
        </script>


        @yield('scripts')

    </body>
</html>