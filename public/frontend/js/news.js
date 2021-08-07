$('#myCarousel').carousel({
    interval : 3000
})

$('#myCarousel').on('slide.bs.carousel', function (e) {
    var current = $('div.active').data('key');
    $('.indicators li').removeClass('active')
    $('.indicators #key-'+ current).addClass('active')
})

$('.carousel .carousel-item').each(function(){
    var next = $(this).next();
    if (!next.length) {
        next = $(this).siblings(':first');
    }
    next.children(':first-child').clone().appendTo($(this));
    
    for (var i = 0; i < 2; i++) {
        next = next.next();
        if (!next.length) {
            next = $(this).siblings(':first');
        }
        
        next.children(':first-child').clone().appendTo($(this));
    }
});

// check event button show pinyin
var type = localStorage.getItem('type') ? localStorage.getItem('type') : 'hsk';
var hskStatus = localStorage.getItem('hsk');
var tocflStatus = localStorage.getItem('tocfl');

if (type == 'hsk') {
    typeStatus = hskStatus;
} else {
    typeStatus = tocflStatus;
}

if (typeStatus == 'none') {
    $(".switch").prop("checked", false);
} else {
    $(".switch").prop("checked", true);
}

// event view more news
$('.view-more-news').click(function() {
    $('.item-recent').removeClass('news-more');
    $(this).hide();
});

// event change show pinyin
$('.pinyin-button').click( function() {
    var checked = $(".switch").is(":checked");
    if (checked) {
        $("rt").css('visibility', 'visible');
        level(type, '1')
        $('.select-' + type).val('1');
    } else {
        $("rt").css('visibility', 'visible');
        level(type, 'none')
        $('.select-' + type).val('none');
    }
});