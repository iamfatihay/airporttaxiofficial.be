//////// MENU /////////
(function ($) {
    //cache nav
    var nav = $("#topNav");
    //add indicator and hovers to submenu parents
    nav.find("li").each(function () {
        if ($(this).find("ul").length > 0) {
            //show subnav on hover
            $(this).mouseenter(function () {
                $(this).find("ul").stop(true, true).slideDown();
            });
            //hide submenus on exit
            $(this).mouseleave(function () {
                $(this).find("ul").stop(true, true).slideUp();
            });
        }
    });
})(jQuery);

//////// SCROLL TO TOP /////////
$(document).ready(function () {
    // hide #back-top first
    $(".back-top").hide();
    // fade in #back-top
    $(function () {
        $(window).scroll(function () {
            if ($(this).scrollTop() > 700) {
                $('.back-top').fadeIn();
            } else {
                $('.back-top').fadeOut();
            }
        });
        // scroll body to 0px on click
        $('.back-top a').click(function () {
            $('body,html').animate({
                scrollTop: 0
            }, 1000);
            return false;
        });
    });
});

/*
 * @module       RD Navbar
 * @description  Enables RD Navbar Plugin
 */
;
(function ($) {
    var o = $('.rd-navbar');
    if (o.length > 0) {
        $(document).ready(function () {
            o.RDNavbar({
                stickUpClone: false,
                stickUpOffset: 105
            });
        });
    }
})(jQuery);

//////// LANGUAGE MENU //////// 
function googleTranslateElementInit() {
    new google.translate.TranslateElement({
        pageLanguage: 'fr',
        autoDisplay: false
    }, 'google_translate_element');
}

function triggerHtmlEvent(element, eventName) {
    var event;
    if (document.createEvent) {
        event = document.createEvent('HTMLEvents');
        event.initEvent(eventName, true, true);
        element.dispatchEvent(event);
    } else {
        event = document.createEventObject();
        event.eventType = eventName;
        element.fireEvent('on' + event.eventType, event);
    }
}

jQuery('.lang-select').click(function () {
    var theLang = jQuery(this).attr('data-lang');
    jQuery('.goog-te-combo').val(theLang);

    //alert(jQuery(this).attr('href'));
    window.location = jQuery(this).attr('href');
    window.location.reload(true);
});
