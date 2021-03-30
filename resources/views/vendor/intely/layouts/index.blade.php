<!DOCTYPE html>
<html lang="en">
    <head>
        @include('vendor.intely.layouts.shared/head', ['title' => $title])
    </head>

    <body data-layout-mode="detached" @yield('body-extra') @if(isset($after_signup)) class="fixed-nav sticky-footer" id="page-top" @endif>

        @include('vendor.intely.layouts.shared/header')
        <!-- Begin page -->
        <div id="wrapper-main">
            <!-- ============================================================== -->
            <!-- Start Page Content here -->
            <!-- ============================================================== -->
            <div class="content-page">
                <div class="content">
                    @yield('content')
                </div>
            </div>
            @include('vendor.intely.layouts.shared/footer')
            @include('vendor.intely.layouts.shared/footer-script')
            
        </div>
    </body>
</html>