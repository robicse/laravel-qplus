@php
    use Spatie\Permission\Models\Permission;
@endphp
<!-- Main Sidebar Container -->
<aside class="main-sidebar sidebar-dark-primary elevation-4">
    <!-- Brand Logo -->
    <a href="#" class="brand-link">
        <img src="{{ asset('default.png') }}" alt="Logo Logo"
            class="brand-image elevation-3" style="opacity: .8">
        <span class="brand-text font-weight-light">QPLUS</span>
    </a>

    <!-- Sidebar -->
    <div class="sidebar">
        <!-- Sidebar user panel (optional) -->
        {{-- <div class="user-panel mt-3 pb-3 mb-3 d-flex"> --}}
            {{-- <div class="image">
                <img src="{{ asset('backend/dist/img/user2-160x160.jpg') }}" class="img-circle elevation-2"
                    alt="User Image">
            </div> --}}
            {{-- <div class="info"> --}}
                {{-- <a href="#" class="d-block">Name: {{ Auth::User()->name }}</a> --}}
                {{-- <a href="#" class="d-block">Type: {{ Auth::user()->user_type }}</a> --}}
                {{-- @if(Auth::user()->user_type != 'Super Admin')
                    <a href="#" class="d-block">Role: {{ Auth::user()->roles->first()->name }}</a>
                @endif --}}
            {{-- </div> --}}
        {{-- </div> --}}



        <!-- Sidebar Menu -->
        <nav class="mt-2">
             <!-- SidebarSearch Form -->
            <div class="form-inline" id="sidebar-search-sticky">
                <div class="input-group" data-widget="sidebar-search">
                <input class="form-control form-control-sidebar" type="search" placeholder="Search" aria-label="Search">
                <div class="input-group-append">
                    <button class="btn btn-sidebar">
                    <i class="fas fa-search fa-fw"></i>
                    </button>
                </div>
                </div>
            </div>
            <ul class="nav nav-pills nav-sidebar flex-column" data-widget="treeview" role="menu"
                data-accordion="false">


                @php
                    $modules = Helper::getCollapseAndParentModuleList();
                    $segment = Request::segment(1);
                @endphp

                <li class="nav-item">
                    <a href="{{ route($segment.'.dashboard') }}"
                        class="nav-link {{ Request::is($segment.'/dashboard1') ? 'active' : '' }}">
                        <i class="nav-icon fas fa-tachometer-alt"></i>
                        <p>
                            Dashboard
                        </p>
                    </a>
                </li>

                @if (count($modules) > 0)
                    @foreach (@$modules as $module)
                        @php
                            $mainMenuPermission = Permission::where('module_id',@$module->id)->pluck('name')->first();
                        @endphp
                        @if ($module->parent_menu === 'Parent')
                            @can(@$mainMenuPermission)
                                <li class="nav-item">
                                    <a href="{{ $module->parent_menu === 'Parent' ? route(Request::segment(1) . '.' . @$module->slug . '.index') : '#' }}"
                                        class="nav-link {{ $module->parent_menu === 'Parent' ? (Request::is(Request::segment(1) . '/' . @$module->slug . '*') ? 'active' : '') : '' }}">
                                        <i class="{{ @$module->icon }}"></i>
                                        <p>
                                            {{ @$module->name }}
                                        </p>
                                    </a>
                                </li>
                            @endcan
                        @else
                            @php
                                $childModules = Helper::getChildModuleList(@$module->name);
                                $slugList = Helper::getChildModuleSlugList(@$module->name,Request::segment(1));
                                if(in_array(Request::segment(2),@$slugList)){
                                    $active = 'found';
                                }else{
                                    $active = 'not found';
                                }

                                $moduleIds = [];
                                if (count($childModules) > 0){
                                    $nestedData = [];
                                    foreach($childModules as $childModule){
                                        $nestedData[]=@$childModule->id;
                                    }
                                    array_push($moduleIds, $nestedData);
                                }
                                $collapseChildMenuPermission = Helper::collapseChildMenuPermission($moduleIds[0]);
                            @endphp

                            @can($collapseChildMenuPermission)
                            <li class="nav-item has-treeview {{ $active === 'found' ? 'menu-open' : ''}}">
                                <a href="#" class="nav-link">
                                    <i style="color: aliceblue !important" class="{{ @$module->icon }}"></i>
                                    <p>
                                        {{ @$module->name }}
                                        <i class="fas fa-angle-left right"></i>
                                    </p>
                                </a>

                                @if (count($childModules) > 0)
                                    <ul class="nav nav-treeview" style="display: {{ $active === 'found' ? 'block' : ''}}">
                                        @foreach ($childModules as $childModule)
                                        @php
                                        $childMenuPermission = Permission::where('module_id',@$childModule->id)->pluck('name')->first();
                                        @endphp
                                            @can(@$childMenuPermission)
                                            <li class="nav-item">
                                                <a href="{{ route(Request::segment(1) . '.' . @$childModule->slug . '.index') }}"
                                                    class="nav-link {{ Request::is(Request::segment(1) . '/' . @$childModule->slug . '*') ? 'active' : '' }}" target="{{ @$childModule->open_new_tab }}">
                                                    <i style="color: aliceblue !important" class="{{ @$childModule->icon }}"></i>
                                                    <p>{{ @$childModule->name }}</p>
                                                </a>
                                            </li>
                                            @endcan
                                        @endforeach
                                    </ul>
                                @endif
                            </li>
                            @endcan
                        @endif
                    @endforeach
                @endif

            </ul>
        </nav>
        <!-- /.sidebar-menu -->
    </div>
    <!-- /.sidebar -->
</aside>
