// Top Button Animation
jQuery(document).ready(function() {

    var btn = $('#up-button');

    $(window).scroll(function() {
        if ($(window).scrollTop() > 30) {
            btn.addClass('show');
        } else {
            btn.removeClass('show');
        }
    });

    btn.on('click', function(e) {
        e.preventDefault();
        $('html, body').animate({scrollTop:0}, 1000);
    });

}); // ready()