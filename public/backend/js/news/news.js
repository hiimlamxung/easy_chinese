
// show input edit pinyin
$(document).on('click', 'ruby', function(e){
    e.preventDefault();
    var obj  = $(this)[0];
    var html = obj.innerHTML;
    var id  = obj.id;
    var val = '';
    var wordFix = randomNumber();

    $(this).addClass('word-' + wordFix);

    html.replace(/<rt>(.*?)<\/rt>/g, function(match, g1) {
        val = g1;
    });

    var kanji = html.replace(/<rt>(.*?)<\/rt>/g, '');
    
    $('#input-edit-pinyin').css( 'position', 'absolute' );
    $('#input-edit-pinyin').css( 'top', (e.pageY - 45) );
    $('#input-edit-pinyin').css( 'left', (e.pageX - 20) );

    $('#text-pinyin').val(val);
    $('#text-pinyin').attr('name', id);
    $('#text-change-hidden').val(html);
    $('#text-kanji-hidden').val(kanji);
    
    $('#input-edit-pinyin').removeClass('hidden');
    $('#input-edit-pinyin').data('word', wordFix);
    $('#text-pinyin').focus();
});

// hide input edit pinyin
$(document).on('click', '#delete-pinyin-edit', function(e){
    e.preventDefault();
    $('#input-edit-pinyin').addClass('hidden');
})

// save edit input pinyin
$(document).on('click', '#save-pinyin-edit', function(e){
    e.preventDefault();
    var strOld = $('#text-change-hidden').val();
    var strNew = $('#text-pinyin').val();
    var kanji  = $('#text-kanji-hidden').val();
    var id     = $('#input-edit-pinyin').data('word');
    
    var text   = $('.word-' + id).html();
    text = text.replace(strOld, kanji +'<rt>' + strNew + '</rt>');

    $('.word-'+id).html(text);   
    $('#input-edit-pinyin').addClass('hidden');
    $('#view-news ruby').removeAttr('class');  
});

$('#text-pinyin').focus(function(event){
    event.preventDefault();
    // Make shortcut
    $(this).keyup(function(e){
        // enter auto save
        if(e.keyCode == 13){ 
            $('#save-pinyin-edit').click();
        }
        if(e.keyCode == 27){ 
            $('#delete-pinyin-edit').click();
        }
    });
});

$('.input-search').focus(function(e){
    event.preventDefault();
    // Tao phim tat
    $(this).keyup(function(e){
        // enter auto search
        if(e.keyCode == 13){ 
            $('.btn-search').click();
        }
    });
})
function randomNumber(){
    var text = "";
    var possible = "0123456789";
    for (var i = 0; i < 5; i++)
        text += possible.charAt(Math.floor(Math.random() * possible.length));
    return text;
}