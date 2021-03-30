<!-- ========== Left Sidebar Start ========== -->
<div class="left-side-menu">

    <div class="h-100" data-simplebar>

        <!-- User box -->
        <div class="user-box text-center">
            <img src="{{asset('assets/images/users/user-1.jpg')}}" alt="user-img" title="Mat Helme"
                class="rounded-circle avatar-md">
            <div class="dropdown">
                <a href="javascript: void(0);" class="text-dark dropdown-toggle h5 mt-2 mb-1 d-block"
                    data-toggle="dropdown">Geneva Kennedy</a>
                <div class="dropdown-menu user-pro-dropdown">

                    <!-- item-->
                    <a href="javascript:void(0);" class="dropdown-item notify-item">
                        <i class="fe-user mr-1"></i>
                        <span>My Account</span>
                    </a>

                    <!-- item-->
                    <a href="javascript:void(0);" class="dropdown-item notify-item">
                        <i class="fe-settings mr-1"></i>
                        <span>Settings</span>
                    </a>

                    <!-- item-->
                    <a href="javascript:void(0);" class="dropdown-item notify-item">
                        <i class="fe-lock mr-1"></i>
                        <span>Lock Screen</span>
                    </a>

                    <!-- item-->
                    <a href="javascript:void(0);" class="dropdown-item notify-item">
                        <i class="fe-log-out mr-1"></i>
                        <span>Logout</span>
                    </a>

                </div>
            </div>
            <p class="text-muted">Admin Head</p>
        </div>

        <!--- Sidemenu -->
        <div id="sidebar-menu">

            <ul id="side-menu">

                <li class="menu-title">Dashboard</li>
                
                <li>
                    <a href="{{route('godpanel-dashboard')}}">
                        <i data-feather="airplay"></i>
                        <span> Dashboard </span>
                    </a>
                </li>
                <li class="menu-title mt-2">Apps</li>
                <li>
                    <a href="#sidebarEcommerce" data-toggle="collapse">
                        <i data-feather="cpu"></i>
                        <span> Clients </span>
                        <span class="menu-arrow"></span>
                    </a>
                    <div class="collapse" id="sidebarEcommerce">
                        <ul class="nav-second-level">
                            <li>
                                <a href="{{ url('clients')}}">Clients</a>
                            </li>
                            <li>
                                <a href="{{ url('client/create')}}">Add Client</a>
                            </li>
                        </ul>
                    </div>
                </li>
                <li>
                    <a href="#Subscriptions" data-toggle="collapse">
                        <i data-feather="cpu"></i>
                        <span> Subscriptions </span>
                        <span class="menu-arrow"></span>
                    </a>
                    <div class="collapse" id="Subscriptions">
                        <ul class="nav-second-level">
                            <li>
                                <a href="{{ url('subscriptions')}}">Subscriptions</a>
                            </li>
                            <li>
                                <a href="{{ url('subscriptions/new')}}">Add Subscription</a>
                            </li>
                        </ul>
                    </div>
                </li>
                <li>
                    <a href="#Features" data-toggle="collapse">
                        <i data-feather="cpu"></i>
                        <span> Features </span>
                        <span class="menu-arrow"></span>
                    </a>
                    <div class="collapse" id="Features">
                        <ul class="nav-second-level">
                            <li>
                                <a href="{{ url('features')}}">Features</a>
                            </li>
                            <li>
                                <a href="{{ url('features/new')}}">Add New Feature</a>
                            </li>
                        </ul>
                    </div>
                </li>
                <li class="menu-title mt-2">Config</li>
               <li>
                    <a href="{{route('get-godpanel-variables')}}">
                        <i data-feather="airplay"></i>
                        <span> Variables </span>
                    </a>
                </li>
            </ul>

        </div>
        <!-- End Sidebar -->

        <div class="clearfix"></div>

    </div>
    <!-- Sidebar -left -->

</div>
<!-- Left Sidebar End -->