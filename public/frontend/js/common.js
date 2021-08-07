$(function() {

    $('audio,video').mediaelementplayer({
        translations:['es','ar','yi','zh-cn'],
        translationSelector: true,
        startLanguage: 'en',
        audioWidth: 620
    });

    // save news had read
    $('.item-sidebar').on('click', function() {
        var news_id = $(this).data('id');
    });

    $('.select-level').change(function() {
        var href = $(".select-level option:selected").data('href');
        window.location.href = href;
    });

    $('.select-source').change(function() {
        var href = $(".select-source option:selected").data('href');
        window.location.href = href;
    });
    
    // js of header
    $('.setting').on('click', function() {
        event.stopPropagation();
        $('.box-user').removeClass('open');
        $('.box-menu').removeClass('open');
        $('.box-setting').addClass('open');
    });

    $('.user-setting').on('click', function() {
        event.stopPropagation();
        $('.box-setting').removeClass('open');
        $('.box-menu').removeClass('open');
        $('.box-user').addClass('open');
    });

    $('.item-more').on('click', function() {
        event.stopPropagation();
        $('.box-setting').removeClass('open');
        $('.box-user').removeClass('open');
        $('.box-menu').addClass('open');
    });

    $('.setting-icon').click(function() {
        event.stopPropagation();
        $('.setting-panel').addClass('open-setting-panel');
    });

    $(window).click(function() {

        var setting_open = $(".box-setting").hasClass("open");
        var user_open = $(".box-user").hasClass("open");
        var menu_open = $(".box-menu").hasClass("open");
        var setting_open_mb = $(".setting-panel").hasClass("open-setting-panel");

        if (setting_open || user_open || menu_open) {
            $('.box-setting').removeClass('open');
            $('.box-user').removeClass('open');
            $('.box-menu').removeClass('open');
        }

        if (setting_open_mb) {
            $('.setting-panel').removeClass('open-setting-panel');
        }
    });

    $('.box-setting').click(function(event) {
        event.stopPropagation();
    });

    $('.box-user').click(function(event) {
        event.stopPropagation();
    });

    $('.setting-panel').click(function(event) {
        event.stopPropagation();
    });

    $('.open-menu').click(function() {
        $('.menu-left').addClass('open-menu-left');
        $('.cover').css('display', 'block');
    });

    $('.cover').click(function() {
        closePanel();
    });

    $('.btn-register').prop('disabled', true);
    $('.check-accept').change(function() {
        if($(this).is(':checked')) {
            $('.btn-register').prop('disabled', false);
        }
    });

    // ----header-------

});

function updateQueryStringParameter(uri, key, value) {
    var re = new RegExp("([?&])" + key + "=.*?(&|$)", "i");
    var separator = uri.indexOf('?') !== -1 ? "&" : "?";
    if (uri.match(re)) {
      return uri.replace(re, '$1' + key + "=" + value + '$2');
    }
    else {
      return uri + separator + key + "=" + value;
    }
}

function closePanel () {
    $('.menu-left').removeClass('open-menu-left');
    // $('.setting-panel').removeClass('open-setting-panel');
    $('.cover').css('display', 'none'); 
}