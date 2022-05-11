<!doctype html>
<html class="no-js" lang="ru">
    <head>
        <title>@yield('content_title')</title>
        
        <meta charset="utf-8">
        <meta http-equiv="x-ua-compatible" content="ie=edge">
        <meta content="width=device-width, initial-scale=1, maximum-scale=1, user-scalable=no" name="viewport">
        <meta name="viewport" content="width=device-width, initial-scale=1">
        <meta name="csrf-token" content="{{ csrf_token() }}">
        <link rel="icon" href="{{URL::to("/")}}/favicon.ico" type="image/x-icon" /> 
           
<?php
    $theme_name = 'admin_lte';
    $theme_name = 'lte_advanced';
    
    if($theme_name == 'lte_advanced') {
?>
        <link rel="stylesheet" href="{{URL::to("/")}}/{{$theme_name}}/css/bootstap4_features.css">
        <link rel="stylesheet" href="{{URL::to("/")}}/{{$theme_name}}/css/lte_design_features.css">
<?php
    } else {
?>
        <link rel="stylesheet" href="{{URL::to("/")}}/{{$theme_name}}/css/skins/_all-skins.min.css">
<?php    
    }
?>
          
        <link rel="stylesheet" href="{{URL::to("/")}}/{{$theme_name}}/css/bootstrap.min.css">
        <link rel="stylesheet" href="{{URL::to("/")}}/{{$theme_name}}/css/font-awesome.min.css">
        <link rel="stylesheet" href="{{URL::to("/")}}/{{$theme_name}}/css/AdminLTE.min.css">
        <link rel="stylesheet" href="{{URL::to("/")}}/{{$theme_name}}/css/morris.css">
        <link rel="stylesheet" href="{{URL::to("/")}}/{{$theme_name}}/css/bootstrap-datepicker.min.css">
        <link rel="stylesheet" href="{{URL::to("/")}}/{{$theme_name}}/css/daterangepicker.css">
        <link rel="stylesheet" href="{{URL::to("/")}}/{{$theme_name}}/css/bootstrap3-wysihtml5.min.css">
        <link rel="stylesheet" href="{{URL::to("/")}}/{{$theme_name}}/css/trips_crm.css">

        <script src="{{URL::to("/")}}/{{$theme_name}}/js/ckeditor.js"></script>
    </head>
<body class="hold-transition skin-blue sidebar-mini">
    <div class="wrapper">
       
       @include('cp.cp_header_top')
       
          <!-- Меню слева -->
          <aside class="main-sidebar">
            <section class="sidebar">
                @include('cp.cp_sidebar_left')
            </section>
          </aside>
          <!-- /меню слева -->

    <!-- Content Wrapper. Contains page content -->
    <div class="content-wrapper">

        <!-- Content Header (Page header) -->
        <section class="content-header">
            <div>
                @yield('content_breadcrumb')
            </div>
            <div class="text-center">
                <h1 class="page-title">@yield('content_title')</h1>
                <div class="row">
                    <div class="col-md-12 col-sm-12 col-xs-12 text-center">
                @yield('content_description')
                    </div>
                </div>
            <div>
          
        </section>
        <section class="content">
        <div style="overflow: hidden;">
            @yield('content')
        </div>
        </section>
    </div>
    <!-- /.content-wrapper -->
        
        @include('cp.cp_footer')
        
        @include('cp.cp_sidebar_right')
        <div class="control-sidebar-bg"></div>
      
</div>

<script src="{{URL::to("/")}}/{{$theme_name}}/js/jquery.min.js"></script>
<script src="{{URL::to("/")}}/{{$theme_name}}/js/jquery-ui.min.js"></script>
<!-- Resolve conflict in jQuery UI tooltip with Bootstrap tooltip -->
<script>
  $.widget.bridge('uibutton', $.ui.button);
</script>
<script src="{{URL::to("/")}}/{{$theme_name}}/js/bootstrap.min.js"></script>
<script src="{{URL::to("/")}}/{{$theme_name}}/js/morris.min.js"></script>
<script src="{{URL::to("/")}}/{{$theme_name}}/js/jquery.sparkline.min.js"></script>
<script src="{{URL::to("/")}}/{{$theme_name}}/js/jquery.knob.min.js"></script>
<script src="{{URL::to("/")}}/{{$theme_name}}/js/moment.min.js"></script>
<script src="{{URL::to("/")}}/{{$theme_name}}/js/daterangepicker.js"></script>
<script src="{{URL::to("/")}}/{{$theme_name}}/js/bootstrap-datepicker.min.js"></script>
<script src="{{URL::to("/")}}/{{$theme_name}}/js/bootstrap3-wysihtml5.all.min.js"></script>
<script src="{{URL::to("/")}}/{{$theme_name}}/js/jquery.slimscroll.min.js"></script>
<script src="{{URL::to("/")}}/{{$theme_name}}/js/fastclick.js"></script>
<script src="{{URL::to("/")}}/{{$theme_name}}/js/adminlte.min.js"></script>
<script src="{{URL::to("/")}}/{{$theme_name}}/js/keymaster.js"></script>   
@yield('scripts')

<script>

key('z', function(event, handler){
    $('#sidebar-toggle').click();
});

</script>

    </body>
</html>