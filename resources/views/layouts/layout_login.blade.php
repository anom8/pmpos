<!DOCTYPE html>
<html lang="en">
    <head>
      <meta charset="utf-8">
      <meta name="viewport" content="width=device-width, initial-scale=1.0">
      <meta name="description" content="A fully featured admin theme which can be used to build CRM, CMS, etc.">
      <meta name="author" content="Coderthemes">
    
      <!-- App favicon -->
      <link rel="shortcut icon" href="https://shortir.com/assets/img/favicon.png">

      <title>@yield('title') | Padang Merdeka</title>

      <link href="{{ URL::asset('assets/css/bootstrap.min.css') }}" rel="stylesheet">
      <link href="{{ URL::asset('assets/css/core.css') }}" rel="stylesheet">
      <link href="{{ URL::asset('assets/css/components.css') }}" rel="stylesheet">
      <link href="{{ URL::asset('assets/css/icons.css') }}" rel="stylesheet">
      <link href="{{ URL::asset('assets/css/pages.css') }}" rel="stylesheet">
      <link href="{{ URL::asset('assets/css/menu.css') }}" rel="stylesheet">
      <link href="{{ URL::asset('assets/css/responsive.css') }}" rel="stylesheet">
      <link href="{{ URL::asset('assets/plugins/switchery/switchery.min.css') }}" rel="stylesheet">
      <link href="{{ URL::asset('assets/plugins/bootstrap-tagsinput/css/bootstrap-tagsinput.css') }}" rel="stylesheet">
      <link href="{{ URL::asset('assets/plugins/bootstrap-touchspin/css/jquery.bootstrap-touchspin.min.css') }}" rel="stylesheet">

      @yield('styles')

      <!--[if lt IE 9]>
        <script src="//oss.maxcdn.com/html5shiv/3.7.2/html5shiv.min.js"></script>
        <script src="//oss.maxcdn.com/respond/1.4.2/respond.min.js"></script>
      <![endif]-->
      
      <script src="{{ URL::asset('assets/js/modernizr.min.js') }}"></script>
      
    </head>
    <body class="bg-transparent">

        @yield('content')


        <script>
            var resizefunc = [];
        </script>

        <!-- jQuery  -->
        <script src="{{ URL::asset('assets/js/jquery.min.js') }}"></script>
        <script src="{{ URL::asset('assets/js/bootstrap.min.js') }}"></script>
        <script src="{{ URL::asset('assets/js/detect.js') }}"></script>
        <script src="{{ URL::asset('assets/js/fastclick.js') }}"></script>
        <script src="{{ URL::asset('assets/js/jquery.blockUI.js') }}"></script>
        <script src="{{ URL::asset('assets/js/waves.js') }}"></script>
        <script src="{{ URL::asset('assets/js/jquery.slimscroll.js') }}"></script>
        <script src="{{ URL::asset('assets/js/jquery.scrollTo.min.js') }}"></script>
        <script src="{{ URL::asset('assets/plugins/switchery/switchery.min.js') }}"></script>

        <!-- Counter JS -->
        <script src="{{ URL::asset('assets/plugins/waypoints/jquery.waypoints.min.js') }}"></script>
        <script src="{{ URL::asset('assets/plugins/counterup/jquery.counterup.min.js') }}"></script>
        <script src="{{ URL::asset('assets/plugins/bootstrap-filestyle/js/bootstrap-filestyle.min.js') }}"></script>

        <!-- Morris Chart -->
        <!-- <script src="{{ URL::asset('assets/plugins/morris/morris.min.js') }}"></script> -->
        <!-- <script src="{{ URL::asset('assets/plugins/raphael/raphael-min.js') }}"></script> -->

        <!-- Dashboard init -->
        <!-- <script src="{{ URL::asset('assets/pages/jquery.dashboard.js') }}"></script> -->

        <!-- App js -->
        <script src="{{ URL::asset('assets/js/jquery.core.js') }}"></script>
        <script src="{{ URL::asset('assets/js/jquery.app.js') }}"></script>


        @yield('scripts')

    </body>
</html>