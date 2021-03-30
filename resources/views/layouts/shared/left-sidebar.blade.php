<?php
$category_permission = json_decode(Auth::user()->permission);
$permission = (isset($category_permission->module) && $category_permission->module=='category')?true:false;
$admin = Auth::user()->hasRole('admin');
$service_provider = Auth::user()->hasRole('service_provider');
 $tx_dash = 'Nurses';
?>
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
                    <a href="{{route('admin_dashboard')}}">
                        <i data-feather="airplay"></i>
                            <span>Dashboard</span>
                    </a>
                </li>
                <li class="menu-title mt-2">Apps</li>
                <li>
                    <a href="#sidebarEcommerce" data-toggle="collapse">
                        <i data-feather="cpu"></i>
                        <span> Configuration </span>
                        <span class="menu-arrow"></span>
                    </a>
                    <div class="collapse" id="sidebarEcommerce">
                        <ul class="nav-second-level">
                            <li>
                                <a href="{{ url('admin/categories')}}">Categories</a>
                                
                            </li>
                            <li>
                                <a href="#VendorCustomField" data-toggle="collapse">
                                    {{ __('text.Vendor') }} Custom Fields <span class="menu-arrow"></span>
                                </a>
                                <div class="collapse" id="VendorCustomField">
                                    <ul class="nav-second-level">
                                        <li>
                                            <a href="{{ url('admin/vendor/custom-fields') }}">Listing</a>
                                        </li>
                                        <li>
                                            <a href="{{ url('admin/vendor/custom-fields/create') }}">Add Field</a>
                                        </li>
                                    </ul>
                                </div>
                            </li>
                            <li>
                                <a href="#UserCustomField" data-toggle="collapse">
                                    {{ __('text.User') }} Custom Fields <span class="menu-arrow"></span>
                                </a>
                                <div class="collapse" id="UserCustomField">
                                    <ul class="nav-second-level">
                                        <li>
                                            <a href="{{ url('admin/user/custom-fields') }}">Listing</a>
                                        </li>
                                        <li>
                                            <a href="{{ url('admin/user/custom-fields/create') }}">Add Field</a>
                                        </li>
                                    </ul>
                                </div>
                            </li>
                            <li>
                                <a href="#Preferences" data-toggle="collapse">
                                    {{ __('text.Master Preferences') }}<span class="menu-arrow"></span>
                                </a>
                                <div class="collapse" id="Preferences">
                                    <ul class="nav-second-level">
                                        <li>
                                            <a href="{{ url('admin/master/preferences') }}">Listing</a>
                                        </li>
                                        <li>
                                            <a href="{{ url('admin/master/preferences/create') }}">Add</a>
                                        </li>
                                    </ul>
                                </div>
                            </li>
                            <li>
                                <a href="#Duties" data-toggle="collapse">
                                    {{ __('text.Custom Master Preferences') }}<span class="menu-arrow"></span>
                                </a>
                                <div class="collapse" id="Duties">
                                    <ul class="nav-second-level">
                                        <li>
                                            <a href="{{ url('admin/master/duties') }}">Listing</a>
                                        </li>
                                        <li>
                                            <a href="{{ url('admin/master/duties/create') }}">Add</a>
                                        </li>
                                    </ul>
                                </div>
                            </li>
                        </ul>
                    </div>
                </li>
                <li>
                    <a href="#Users" data-toggle="collapse">
                        <i data-feather="users"></i>
                        <span> Users </span>
                        <span class="menu-arrow"></span>
                    </a>
                    <div class="collapse" id="Users">
                        <ul class="nav-second-level">
                            <li>
                                <a href="{{url('admin/consultants')}}">{{ __('text.Vendors') }}</a>
                            </li>
                            <li>
                                <a href="{{url('admin/customers')}}">{{ __('text.Users') }}</a>
                            </li>
                        </ul>
                    </div>
                </li>
                <li>
                    <a href="#Marketing" data-toggle="collapse">
                        <i data-feather="briefcase"></i>
                        <span> Marketing </span>
                        <span class="menu-arrow"></span>
                    </a>
                    <div class="collapse" id="Marketing">
                        <ul class="nav-second-level">
                            <li>
                                <a href="{{url('admin/banner')}}">Banners</a>
                            </li>
                            <?php $exits=App\Helpers\Helper::checkFeatureExist([
                                'client_id'=>Config::get('client_id'),
                                'feature_name'=>'Packages']) ?>
                            @if(!config('client_connected') || $exits)
                            <li>
                                <a href="{{url('admin/package')}}">Packages</a>
                            </li>
                            @endif
                            <?php $exits=App\Helpers\Helper::checkFeatureExist([
                                'client_id'=>Config::get('client_id'),
                                'feature_name'=>'Master Interval']) ?>
                            @if(!config('client_connected') || $exits)
                            <li>
                                <a href="{{url('admin/master_slot')}}">Master Interval</a>
                            </li>
                            @endif
                            
                            <li>
                                <a href="{{url('admin/coupon')}}">Coupons</a>
                            </li>
                        </ul>
                    </div>
                </li>
                <li>
                    <a href="#Settings" data-toggle="collapse">
                        <i data-feather="settings"></i>
                        <span> Settings </span>
                        <span class="menu-arrow"></span>
                    </a>
                    <div class="collapse" id="Settings">
                        <ul class="nav-second-level">
                            <li>
                                <a href="{{url('admin/pages')}}">Pages</a>
                            </li>
                            <li>
                                <a href="{{url('admin/app_detail')}}">App Setting</a>
                            </li>
                            <li>
                                <a href="{{url('admin/service_enable')}}">Variables</a>
                            </li>
                            
                            <li>
                                <a href="{{url('admin/faq')}}">FAQs</a>
                            </li>
                            <li>
                                <a href="{{url('admin/app_version')}}">App Version</a>
                            </li>
                        </ul>
                    </div>
                </li>
                <li>
                    <a href="#Features" data-toggle="collapse">
                        <i data-feather="settings"></i>
                        <span> Features </span>
                        <span class="menu-arrow"></span>
                    </a>
                    <div class="collapse" id="Features">
                        <ul class="nav-second-level">
                            @if(Config::get('client_features'))
                                @foreach(Config::get('client_features') as $index => $client_feature)
                                <li>
                                    <a href="{{url('admin/feature-types/'.$client_feature->feature_type->id)}}">{{ $client_feature->feature_type->name }}</a>
                                </li>
                                @endforeach
                            @endif
                        </ul>
                    </div>
                </li>
            
            </ul>

        </div>
        <!-- End Sidebar -->

        <div class="clearfix"></div>

    </div>
    <!-- Sidebar -left -->

</div>
<!-- Left Sidebar End -->