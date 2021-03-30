<!-- bundle -->
<!-- Vendor js -->
<div id="sb_widget"></div>
<script src="{{asset('assets/js/vendor.min.js')}}"></script>
@yield('script')
<!-- App js -->
<script src="{{asset('assets/js/app.min.js')}}"></script>
<script src="https://unpkg.com/axios/dist/axios.min.js"></script>
<script src="{{asset('assets/js/widget.SendBird.js')}}"></script>
<script>
  sbWidget.start('B13514F8-4AB5-4ABA-87D6-C3904DA10C96');
</script>
@yield('script-bottom')
