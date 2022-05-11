<header class="main-header">
    <!-- Logo -->
    <a href="{{URL::to("/")}}/cp" class="logo">
      <span class="logo-mini">Trips</span>
      <span class="logo-lg">Админка Trips</span>
    </a>
    <!-- Header Navbar: style can be found in header.less -->
    <nav class="navbar navbar-static-top">
      <!-- Sidebar toggle button-->
      <a href="#" class="sidebar-toggle" data-toggle="push-menu" role="button" id="sidebar-toggle">
        <span class="sr-only">Toggle navigation</span>
      </a>

      <div class="navbar-custom-menu">
        <ul class="nav navbar-nav mr-2">

         <!-- Control Sidebar Toggle Button -->
         <!--
          <li class="notifications-menu">
            <a href="{{URL::to('/')}}/cp/download_sql" target="_blank"><i class="fa fa-fw fa-database"></i> Скачать SQL</a>
          </li>
          -->
          <!--
          <li class="notifications-menu">
            <a href="#" data-toggle="control-sidebar"><i class="fa fa-cog"></i> Настройки</a>
          </li>
          -->
          
          <li class="notifications-menu">
          <a href="{{URL::to('/')}}/logout"><i class="menu-icon fa fa-sign-out"></i> Выход</a>
          </li>

          
        </ul>
      </div>
    </nav>
  </header>