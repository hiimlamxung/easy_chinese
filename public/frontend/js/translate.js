var dataKanji;
var oldDataSearch = [];
var oldWord;
var textToSearch;
var tabSearch;
var langWeb = localStorage.getItem('language');


$(function() {
    $('.search-button').on('click', function () {
        $('#modal-translate').show();
        $('.text-translate').focus();
    });

    $('.add-translate').on('click', function() {
        var user_id = $(this).data('id');

        if (user_id) {
            $('.box-content-translate').hide();
            $('.box-create-tran').show();

        } else {
            $('#required_login').modal('show');
        }
    });

    $('.icon-search').on('click', function() {
        searchData('word');
    });

    $('.text-translate').on('keypress', function(e) {
        if(e.which == 13) {
            searchData('word');
        }
    });

    $('.tab-word').on('click', function() {
        searchData('word');
    });

    $('.tab-kanji').on('click', function() {
        searchData('kanji');
    });

    $('.tab-sentence').on('click', function() {
        searchData('example');
    });

    $('.kanji-translate').on('click', '.kanji-item', function(){
        var indexKanji = $(this).data('id');
        $('.kanji-item').removeClass('kanji-active');
        $('.kanji-item-' + indexKanji).addClass('kanji-active');
        result = dataKanji[indexKanji];
        index = (result.detail).split('##');
        var detaiKanji = boxkanji(result, index);
        $('.box-kanji-list').html(detaiKanji);
    });

    $('#search-data').click( function() {
        if (textToSearch != '') {
            $(".text-translate").val(textToSearch); 
            $('#modal-translate').show();
            if (tabSearch) {
                searchData(tabSearch);
            } else {
                searchData('word');
            }
        }
    });

    var htmlTran = getHtmlTran();
    var htmlKanji = gethtmlKanji();
    var htmlSentence =  gethtmlSentence();
    
    $('.word-translate').html(htmlTran);
    $('.box-kanji-list').html(htmlKanji);
    $('.exam-translate').html(htmlSentence);
    
    $('#close-search').on('click', function () {
        $('#modal-translate').hide();
        $('.text-translate').val('');
        $('.word-translate').html(htmlTran);
        $('.box-kanji').html('');
        $('.box-kanji-list').html(htmlKanji);
        $('.exam-translate').html(htmlSentence);
        dataKanji = null;
    });

    $(".dicWin").click(function(e) {

        var contentHtml = e.currentTarget.innerHTML;
        var content = '';
        content = contentHtml.replace(/<rt.*?<\/rt>/g, "");
        content = content.replace(/<.*?>/g, "");
        
        if (content == '')
            return;
        $(".text-translate").val(content); 
        $('#modal-translate').show();

        if (tabSearch) {
            searchData(tabSearch);
        } else {
            searchData('word');
        }
    });

    $("#detail .content span").click(function(e) {

        var contentHtml = e.currentTarget.innerHTML;
        var content = '';
        content = contentHtml.replace(/<rt.*?<\/rt>/g, "");
        content = content.replace(/<.*?>/g, "");
        
        if (content == '') return;

        $(".text-translate").val(content); 
        $('#modal-translate').show();

        if (tabSearch) {
            searchData(tabSearch);
        } else {
            searchData('word');
        }
    });

    $('.box-user-translate .user-translate').click(function() {
        var key = $(this).data('id');
        if (key == undefined || key == null) {
            return;
        }

        $('.box-user-translate .user-translate').removeClass('user-active');
        $('.box-user-translate .user-' + key).addClass('user-active');

        $('.box-content-translate .box-trans').addClass('hidden');
        $('.box-content-translate .box-trans-' + key).removeClass('hidden');

    });
});

window.addEventListener('mouseup', function(e){
    var range = window.getSelection().getRangeAt(0)
    content = range.cloneContents()
    span = document.createElement('SPAN');
    span.appendChild(content);

    var htmlContent = span.innerHTML;
    var text = '';

    text = htmlContent.replace(/<rt.*?<\/rt>/g, "");
    text = text.replace(/<.*?>/g, "");

    if (text != '') {
        textToSearch = text;
        $(".fast-searchit").css("display", "block");
        $(".fast-searchit").animate({"left": e.pageX - 2,"top" : e.pageY - 30}, "fast");
        $(".fast-searchit").fadeIn("fast");
    } else {
        $(".fast-searchit").css("display", "none");
        $(".fast-searchit").fadeOut("fast");
    }

});

function getSelectedText(){
    // For Firefox, Safari and other non-IE browsers
    if(window.getSelection) {
        var selectedRange = window.getSelection();
        if (selectedRange['baseNode'] != null && $(selectedRange['baseNode']).hasClass("input-group")) {
            return "";
        } else {
            return window.getSelection();
        }
    }
    else if(document.getSelection) 
        return document.getSelection();
    else {
        // For IE
        var selection = document['selection'] && document['selection'].createRange();
        if(selection.text)
            return selection.text;
        return false;
    }
    return false;
}

function showWord(dataWord) {
    var boxHtml = '';
    if (dataWord.dataTranGG != '') {
        boxHtml += `<div class="box-result-translate">
        <div class="sentence-japan sentence-auto">
            <span class="icon"><i class="fas fa-circle"></i></span>
            <span class="sentence-gg">` + dataWord.dataTranGG + `</span>
        </div>
        `;
        if(langWeb == 'vi') {
            boxHtml +=` <div class="text-right auto-tran">Dịch tự động </div> </div>`;
        } else {
            boxHtml +=` <div class="text-right auto-tran">Auto translate </div> </div>`;
        };
    }

    $.each(dataWord.result, function(key, res){
        boxHtml += `<div class="box-word"> <div class="kanji">` + res.word + `</div> <div class="hira">` + res.phonetic + `</div>`;

        $.each(res.means, function(index, item){
            if(item[0].kind != ''){
                boxHtml +=  `<div class="kind">   &#9679; ` + item[0].kind + `</div>`;
            }

            $.each(item, function(i, val){
                html = `<p class = "mean-detail"> &diams; ` + val.mean + `</p>`;
                if (val.examples != null) {
                    $.each(val.examples, function(x, item){
                        var content  = item.content;
                        var trans    = item.transcription;
                        var tmpDt    = getKanjiAndHiragana(content, trans);
                        var newDt    = '';
                        if(tmpDt && tmpDt.length > 0) {
                            var tmpDt = Object.keys(tmpDt).map(function (key) { return tmpDt[key]; });
                            newDt = mergeKanjiAndHiragana(tmpDt);
                            html  += `<p>` + newDt + `</p>`;

                        }else if(tmpDt == null){
                            html  += `<p>` + content + `</p>`;
                        }
                        html  += `<p>` + item.mean + `</p>`;
                    });
                }
            });
            boxHtml += `<div class="box-mean">
                            <div class="box-mean-show">`+html+`</div>
                        </div>`;
        });
        boxHtml += `</div>`;
    });
    $('.word-translate').html(boxHtml);
}

function showKanji(res) {
    dataKanji = res.result;
    var result = dataKanji[0];
    var index = result.detail.split("##");
    var listCharKanji = '';
        
    for (i = 0; i < dataKanji.length; i++) {
        var kanji = dataKanji[i].kanji;
        listCharKanji += `<div class="kanji-item-`+ i + ` kanji-item" data-id="`+ i +`">` + kanji + `</div>`;
    }

    $('.box-kanji').append(listCharKanji);
    boxkanji(result, index);
    $('.kanji-item-0').addClass('kanji-active');

}

function showSentence(data) {
    $.each(data.result, function(key, res){
        var content = res.content;
        var trans   = res.transcription;
        var mean    = res.mean;
        var tmp     = getKanjiAndHiragana(content, trans);
        var newDt    = '';
        if(tmp && tmp.length > 0) {
            var tmp = Object.keys(tmp).map(function (key) { return tmp[key]; });
            newDt = mergeKanjiAndHiragana(tmp);

        }else if( tmp == null ){
            newDt  += `<p>` + content + `</p>`;
        }
        var boxHtml = '';
        boxHtml += `<div class="box-result-translate">
                        <div class="sentence-japan">
                            <span class="icon"><i class="fas fa-circle"></i></span>
                            ` + newDt + `
                        </div>
                        <div class="sentence-translate">` + mean + `</div>
                    </div>`;
        $('.exam-translate').append(boxHtml);
    })
}
function showTranWithGoogle(data, tab) {
    var boxHtml = '';

    $.each(data.result, function(key, res){
        if (res == null) {
            return false;
        } else {
            boxHtml += `<div class="box-result-translate">
            <div class="sentence-japan">
                <span class="icon"><i class="fas fa-circle"></i></span>
                <span class="sentence-gg">` + res + `</span>
            </div>
            `;
            if(langWeb == 'vi') {
            boxHtml +=` <div class="text-right auto-tran">Dịch tự động </div> </div>`;
            } else {
            boxHtml +=` <div class="text-right auto-tran">Auto translate </div> </div>`;
            };
        }
       
    }); 
    if (tab == 'word') {
        $('.word-translate').html(boxHtml);
    } else if(tab == 'kanji') {
        $('.box-kanji-list').html(boxHtml);
    } else {
        $('.exam-translate').html(boxHtml);
    }
}
function searchData(tab){
    var data = $(".text-translate").val(); 
    var langUser = localStorage.getItem('language');
    if (data == '') {
        return;
    }

    tabSearch = tab;
    var newData = {
        word: data,
        lang: langUser,
        tab: tab
    }
    newData = JSON.stringify(newData);

    if (oldDataSearch[newData] == 1){
        return;
    } else {
        if(data !== oldWord) {
            resetModalSearch();
            oldWord = data;
        }
        oldDataSearch[newData] = 1;
    }

    if ( (tab == 'example' && langUser !== 'vi') || (tab == 'word') ) {
        if(!isJapanese(data)){
            data = wanakana.toKana(data);
            $(".text-translate").val(data);
        }
    }

    $.ajax({
        url: "/getDict",
        type: 'POST',
        data: {
            text: data,
            tab: tab,
            type: 'dict',
            language: langUser
        },
        headers: {'X-CSRF-TOKEN': $('meta[name=csrf-token]').attr('content')},
        success: function(res){
            if (res.status == 200){
                if(res.type == 'mazii') {
                    if (tab == 'word') {
                        showWord(res);
                    } else if (tab == 'kanji') {
                        showKanji(res);
                    } else {
                        showSentence(res);
                    }
                } else {
                    showTranWithGoogle(res, tab);
                }
            }
        },
        error: function(e) {
            console.log(e);
            var html = `<p class="none-data">Không tìm thấy dữ liệu</p>`;
            if (tab == 'word') {
                $('.word-translate').html(html);
            } else if (tab == 'kanji') {
                $('.box-kanji').html('');
                $('.box-kanji-list').html(html);
            } else {
                $('.exam-translate').html(html);
            }
        }
    });
}

function resetModalSearch() {
    oldDataSearch = [];
    tabSearch = '';
    $('.word-translate').html('');
    $('.box-kanji').html('');
    $('.box-kanji-list').html('');
    $('.exam-translate').html('');
}

function getLengthHiragana (kanji) {
    if (kanji == null || kanji.length == 0) {
        return 0;
    }

    var result = 0;
    for (var i = 0; i < kanji.length; i++) {
        var c = kanji.charAt(i);
        if (c == 'ん' ||
            c == 'ぁ' ||
            c == 'ぃ' ||
            c == 'ぇ' ||
            c == 'ぅ' ||
            c == 'ぉ' ||
            c == 'ゅ' ||
            c == 'ょ') {
            continue;
        }

        result++;
    }

    return result;
}