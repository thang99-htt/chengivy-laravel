(function ($) {
    "use strict";

    // Sidebar Toggler
    $('.sidebar-toggler').click(function () {
        $('.sidebar, .content').toggleClass("open");
        return false;
    });

    var path = window.location.href;
    $('.admin .nav-item').each(function() {
        if (this.href === path) {
           $(this).addClass('active');
        }
    });
    
})(jQuery);

