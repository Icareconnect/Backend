<!DOCTYPE html>
    <html lang="en">

    <head>
        @include('layouts.shared/title-meta', ['title' => $title])
        @include('layouts.shared/head-css')
        {{-- @include('layouts.shared/head-css', ["demo" => "modern"]) --}}
         <meta name="csrf-token" content="{{ csrf_token() }}">
        <script type="text/javascript">
            var base_url = "{{ url('/') }}";
            var timZone = Intl.DateTimeFormat().resolvedOptions().timeZone;
            document.cookie = "royo_timZone="+timZone;
        </script>
    </head>

    <body @yield('body-extra')>
        <!-- Begin page -->
        <div id="wrapper">
            @include('layouts.shared/topbar')

            @if(Auth::user()->hasrole('godadmin'))
                @include('layouts.shared/left-sidebar-godpanel')
            @else
                @include('layouts.shared/left-sidebar')
            @endif

            <!-- ============================================================== -->
            <!-- Start Page Content here -->
            <!-- ============================================================== -->

            <div class="content-page">
                <div class="content">                    
                    @yield('content')
                </div>
                <!-- content -->

                @include('layouts.shared/footer')

            </div>

            <!-- ============================================================== -->
            <!-- End Page content -->
            <!-- ============================================================== -->

        </div>
        <!-- END wrapper -->

        @include('layouts.shared/right-sidebar')

        @include('layouts.shared/footer-script')
        
    </body>
</html>