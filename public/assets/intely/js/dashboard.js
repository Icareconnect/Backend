
jQuery(document).ready(function() {
    jQuery("#sidenavToggler").click(function(e) {
        e.preventDefault();
        jQuery("body").toggleClass("sidenav-toggled");
        jQuery(".navbar-sidenav .nav-link-collapse").addClass("collapsed");
        jQuery(".navbar-sidenav .sidenav-second-level, .navbar-sidenav .sidenav-third-level").removeClass("show");
      }); 
});