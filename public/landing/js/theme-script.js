jQuery(document).ready(function ($) {



    /** =============================================================== **/
    /** Menu toogler on mobile hamburger class change **/
    /** =============================================================== **/


    $('.hamburger').on("click", function (e) {
        e.preventDefault();
        $(this).toggleClass('is-active');
    });  

    /** =============================================================== **/
    /** Email fields (option) text show/hide on click **/
    /** =============================================================== **/

    $(".signup-div .input input").on("focus", function() {
        $('.signup-div span').addClass('hide-it');
        $(this).closest('div').find('.float-text').addClass('animate')
        //return false;
    });
    
    $('.signup-div .input input').on("blur", function() {
        if (!$(this).val()) {
            $('.signup-div span').removeClass('hide-it');
            $(this).closest('div').find('.float-text').removeClass('animate')
        }
    });  


    /** =============================================================== **/
    /** Smooth scroll **/
    /** =============================================================== **/    

    $(".how-it-works-link").click(function() {
        $('html, body').animate({
            scrollTop: $(".how-it-works").offset().top
        }, 2000);
    });

    /** =============================================================== **/
    /** Countries slider **/
    /** =============================================================== **/    

    if ($(".countries-slider").length) {
        $('.countries-slider').slick({
            speed: 5000,
            autoplay: true,
            autoplaySpeed: 0,
            cssEase: 'linear',
            slidesToShow: 1,
            slidesToScroll: 1,
            variableWidth: true
        });
    };
});

