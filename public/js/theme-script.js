jQuery(document).ready(function ($) {


    /** =============================================================== **/
    /** Menu toogler on mobile hamburger class change **/
    /** =============================================================== **/


    $('.hamburger').on("click", function (e) {
        e.preventDefault();
        $(this).toggleClass('is-active');
        $('.main-app .sidebar').toggleClass('opened');
    });  


    if ($("#add-users").length) {
        $("#add-users").slider({
            tooltip: 'always'
        });  
    };


    $(".progress-circle").each(function() {

        var value = $(this).attr('data-value');
        var left = $(this).find('.progress-left .progress-bar');
        var right = $(this).find('.progress-right .progress-bar');

        if (value > 0) {
          if (value <= 50) {
            right.css('transform', 'rotate(' + percentageToDegrees(value) + 'deg)')
          } else {
            right.css('transform', 'rotate(180deg)')
            left.css('transform', 'rotate(' + percentageToDegrees(value - 50) + 'deg)')
          }
        }

      })

      function percentageToDegrees(percentage) {

        return percentage / 100 * 360

      }

    $( ".open-agreement-details" ).on( "click", function() {
        $(this).closest('.single').toggleClass('active');
    });

});

