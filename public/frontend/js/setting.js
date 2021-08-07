
$(function() {
    // ---------Font-size----------
    var sizeChange = localStorage.getItem('sizeChange');

    if (sizeChange) {
        $('.size-options').val(sizeChange);
        $('#body').addClass(sizeChange);
    }

    $(".size-options").on('change', function(){
        var sizeChange = localStorage.getItem('sizeChange');
        var sizeChangeNew = $(".size-options option:selected").attr('value');
        $('#body').removeClass('size-' + sizeChange);
        $('#body').addClass('size-' + sizeChangeNew);
        localStorage.setItem('sizeChange', sizeChangeNew)
    });
    // ---------Font-size----------

    // ---------Show pinyin--------
    var type = localStorage.getItem('type');
    var hskStatus = localStorage.getItem('hsk');
    var tocflStatus = localStorage.getItem('tocfl');
    var hskUnderline = localStorage.getItem('hsk-under');
    var tocflUnderline = localStorage.getItem('tocfl-under');

    if (!type) {
        type = 'hsk';
        localStorage.setItem('type', type);
    } 

    typeLearn(type);

    $(".select-type").on('change', function(){
        var typeLearning = $(".select-type option:selected").attr('value');
        typeLearn(typeLearning);
        localStorage.setItem('type', typeLearning);
    });

    // pinyin hsk
    $(".select-hsk").on('change', function(){
        var level = $(".select-hsk option:selected").attr('value');
        $("rt").css('visibility', 'visible');
        levelPinyin('hsk', level);
    });

    // pinyin tocfl
    $(".select-tocfl").on('change', function(){
        var level = $(".select-tocfl option:selected").attr('value');
        $("rt").css('visibility', 'visible');
        levelPinyin('tocfl', level);
    });

    //change show underline
    $(".select-tocfl-underline").on('change', function(){
        var level = $(".select-tocfl-underline option:selected").attr('value');
        changeUnderline('tocfl', level);
    });

    $(".select-hsk-underline").on('change', function(){
        var level = $(".select-hsk-underline option:selected").attr('value');
        changeUnderline('hsk', level);
    });


    // change language
    $(".select-show-language").on('change', function() { 
        var lang = $(".select-show-language option:selected").attr('value');
        $('.select-show-language').val(lang);
        settingLang(lang)
    });

    function typeLearn(type) {
        $('.select-type').val(type);
        console.log(type)
        console.log(tocflStatus)

        if (type == 'tocfl') {
            $('.select-hsk').hide()
            $('.select-tocfl').show();
            $('.select-hsk-underline').hide()
            $('.select-tocfl-underline').show();
    
            if (tocflStatus == null) {
                $('.select-tocfl').val('1');
                $("rt").css('visibility', 'visible');
            } else {
                $('.select-tocfl').val(tocflStatus);
                levelPinyin('tocfl', tocflStatus);
            }

            console.log(tocflUnderline)
    
            if (tocflUnderline == null) {
                $('.select-tocfl-underline').val('1');
                $("span").css('border-bottom', 'block');
            } else{

                $('.select-tocfl-underline').val(tocflUnderline);
                changeUnderline('tocfl', tocflUnderline);
            }
        } else {
            $('.select-hsk').show()
            $('.select-tocfl').hide()
            $('.select-hsk-underline').show()
            $('.select-tocfl-underline').hide();
    
            if (hskStatus == null) {
                $('.select-hsk').val('1');
                $("rt").css('visibility', 'visible');
            } else {
                $('.select-hsk').val(hskStatus);
                levelPinyin('hsk', hskStatus);
            }

            console.log(hskUnderline)
    
            if (hskUnderline == null) {
                console.log('wtffffff')
                $('.select-hsk-underline').val('1');
                $("span").css('border-bottom', 'block');
            } else{
                console.log('wthhhhhhhhhhhhhh')
                $('.select-hsk-underline').val(hskUnderline);
                changeUnderline('hsk', hskUnderline);
            }
        }
    }
});

function settingLang(lang) {
    localStorage.setItem('language', lang);
    $.ajax({
        url: '/locale?locale=' + lang,
        type: 'GET',
        headers: {'X-CSRF-TOKEN': $('meta[name=csrf-token]').attr('content')},
        success: function(res){
            var url = updateQueryStringParameter(window.location.href, 'hl', lang);
            window.location.href = url;
        },
        error: function(e) {
            console.log(e);
        }
    });
}

function levelPinyin(type, level) {

    if (level == '6') {
        $("." + type + "-5 rt").css('visibility', 'hidden');
        $("." + type + "-4 rt").css('visibility', 'hidden');
        $("." + type + "-3 rt").css('visibility', 'hidden');
        $("." + type + "-2 rt").css('visibility', 'hidden');
        $("." + type + "-1 rt").css('visibility', 'hidden');
    }
    if (level == '5') {
        $("." + type + "-2 rt").css('visibility', 'hidden');
        $("." + type + "-4 rt").css('visibility', 'hidden');
        $("." + type + "-3 rt").css('visibility', 'hidden');
        $("." + type + "-1 rt").css('visibility', 'hidden');
    }
    if (level == '4') {
        $("." + type + "-3 rt").css('visibility', 'hidden');
        $("." + type + "-2 rt").css('visibility', 'hidden');
        $("." + type + "-1 rt").css('visibility', 'hidden');
    }
    if (level == '3') {
        $("." + type + "-2 rt").css('visibility', 'hidden');
        $("." + type + "-1 rt").css('visibility', 'hidden');
    }
    if (level == '2') {
        $("." + type + "-1 rt").css('visibility', 'hidden');
    }
    if (level == 'none') {
        $("rt").css('visibility', 'hidden');
    }

    localStorage.setItem(type, level);
}

// change underline
function changeUnderline(type, level) {
    console.log(type)
    console.log(level)
    if (level == "6") {
        $("." + type + "-6").css('border-bottom', '1px solid #e74c3c');
        $("." + type + "-1").css('border-bottom', 'none');
        $("." + type + "-5").css('border-bottom', 'none');
        $("." + type + "-4").css('border-bottom', 'none');
        $("." + type + "-3").css('border-bottom', 'none');
        $("." + type + "-2").css('border-bottom', 'none');
    }
    if (level == "5") {
        $("." + type + "-6").css('border-bottom', '1px solid #e74c3c');
        $("." + type + "-5").css('border-bottom', '1px solid #f39c12');
        $("." + type + "-4").css('border-bottom', 'none');
        $("." + type + "-3").css('border-bottom', 'none');
        $("." + type + "-2").css('border-bottom', 'none');
        $("." + type + "-1").css('border-bottom', 'none');
    }
    if (level == "4") {
        $("." + type + "-6").css('border-bottom', '1px solid #e74c3c');
        $("." + type + "-5").css('border-bottom', '1px solid #f39c12');
        $("." + type + "-4").css('border-bottom', '1px solid #f1c40f');
        $("." + type + "-3").css('border-bottom', 'none');
        $("." + type + "-2").css('border-bottom', 'none');
        $("." + type + "-1").css('border-bottom', 'none');
    }
    if (level == "3") {
        $("." + type + "-6").css('border-bottom', '1px solid #e74c3c');
        $("." + type + "-5").css('border-bottom', '1px solid #f39c12');
        $("." + type + "-4").css('border-bottom', '1px solid #f1c40f');
        $("." + type + "-3").css('border-bottom', '1px solid #2ecc71');
        $("." + type + "-2").css('border-bottom', 'none');
        $("." + type + "-1").css('border-bottom', 'none');
    }
   
    if (level == "2") {
        $("." + type + "-6").css('border-bottom', '1px solid #e74c3c');
        $("." + type + "-5").css('border-bottom', '1px solid #f39c12');
        $("." + type + "-4").css('border-bottom', '1px solid #f1c40f');
        $("." + type + "-3").css('border-bottom', '1px solid #2ecc71');
        $("." + type + "-2").css('border-bottom', '1px solid #3498db');
        $("." + type + "-1").css('border-bottom', 'none');
    }
    if (level == '1') {
        $("." + type + "-6").css('border-bottom', '1px solid #e74c3c');
        $("." + type + "-5").css('border-bottom', '1px solid #f39c12');
        $("." + type + "-4").css('border-bottom', '1px solid #f1c40f');
        $("." + type + "-3").css('border-bottom', '1px solid #2ecc71');
        $("." + type + "-2").css('border-bottom', '1px solid #3498db');
        $("." + type + "-1").css('border-bottom', '1px solid #BC8DFE');
    }
    if (level == "none") {
        $("span").css('border-bottom', 'none');
    }

    localStorage.setItem(type + '-under', level);
}