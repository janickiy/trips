<!-- Control Sidebar -->
<aside class="control-sidebar control-sidebar-dark" style="display: none;">
<!-- Create the tabs -->
    <ul class="nav nav-tabs nav-justified control-sidebar-tabs">
        <li><a href="#control-sidebar-settings-tab" data-toggle="tab"><i class="fa fa-gears"></i></a></li>
        <li><a href="#control-sidebar-user-tab" data-toggle="tab"><i class="fa fa-fw fa-user"></i></a></li>
    </ul>
    
    <!-- Tab panes -->
    <div class="tab-content">
  
      <!-- Home tab content -->
      <div id="control-sidebar-settings-tab" class="tab-pane active">
        {{__('cp.settings')}}
      </div>
      <div class="tab-pane" id="control-sidebar-user-tab">
      
        <h3 class="control-sidebar-heading">
            {{__('cp.hello')}}, {{ Auth::user()->name }}!
        </h3>
        
        <ul class="control-sidebar-menu">
          <li>
            <a href="{{URL::to('/')}}/logout">
              <i class="menu-icon fa fa-sign-out bg-red"></i>
              <div class="menu-info">
                <h4 class="control-sidebar-subheading">{{__('cp.logout')}}</h4>
                <p>{{Request::ip()}}</p>
              </div>
            </a>
          </li>
        </ul>
        
      </div>
      
    </div>
</aside>
<!-- /.control-sidebar -->