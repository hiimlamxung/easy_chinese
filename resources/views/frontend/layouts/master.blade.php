<!DOCTYPE html>
<html lang="">
<head>
    <meta charset="utf-8">
    <meta http-equiv="X-UA-Compatible" content="IE=edge">
    <title>@yield('title', 'Easy-Chinese')</title>
    <meta name="description" content="@yield('description')" />
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <meta property="og:title" content="@yield('title')" />
    <meta property="og:image" content="@yield('image')" />
    <meta property="og:url" content="@yield('url')" />
    <meta property="og:site_name" content="Easy Japanese" />
    <meta property="og:description" content="@yield('description')" />
    <meta property="fb:app_id" content="1544160779141926" />
    <meta property="og:type" content="website"/>
    <meta name="Easy Japanese" content="author" />
    <meta name="keywords" content="@yield('key')"/>
    <meta name="robots" content="index,follow" />
    
    @yield('before-styles-end')
    <link href="https://maxcdn.bootstrapcdn.com/bootstrap/4.0.0/css/bootstrap.min.css" rel="stylesheet" id="bootstrap-css">

    <link href="/frontend/css/main.css" rel="stylesheet" >
    <link href="/frontend/css/skin.css" rel="stylesheet" >
    <link href="/frontend/css/header.css" rel="stylesheet" >
    <link href="/frontend/css/news.css" rel="stylesheet" >
    @yield('after-css')

    {!! HTML::style('https://fonts.googleapis.com/css?family=Source+Sans+Pro:300,400,600,700,300italic,400italic,600italic') !!}
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/mediaelement/4.2.9/mediaelementplayer.css">
    <link rel="stylesheet" href="https://use.fontawesome.com/releases/v5.2.0/css/all.css" >
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/cropperjs/1.5.6/cropper.css" />

    <!-- CSRF Token -->
    <meta name="csrf-token" content="{{ csrf_token() }}">
    
    <!-- Global site tag (gtag.js) - Google Analytics -->
    <script async src="https://www.googletagmanager.com/gtag/js?id=UA-62602489-25"></script>
    <script>
        window.dataLayer = window.dataLayer || [];
        function gtag(){dataLayer.push(arguments);}
        gtag('js', new Date());
        gtag('config', 'UA-62602489-25');
    </script>
</head>

<body id="body">

    @include('frontend.includes.header')

    <div class="box-default box-content-default">     
        @include('frontend.includes.mesage')
        @yield('main-content')
        @include('frontend.modal.translate')
    </div>
    
    @include('frontend.modal.login')
    @include('frontend.includes.footer')  
    <a title="Click vào đây để tra từ này" class="fast-searchit" id="search-data"></a>
    <div class="down-app">
        <div class="delete fa fa-times"></div>
        <div class="content-name">
            <div>Easy Chinese App</div>
            <div class="slogan">{{ trans('label.slogan') }}</div>
        </div>
        <div class="btn-down">{{ trans('label.download') }}</div>
    </div>

    <script src='https://cdnjs.cloudflare.com/ajax/libs/jquery/2.1.3/jquery.min.js'></script>
    <script src="https://maxcdn.bootstrapcdn.com/bootstrap/4.0.0/js/bootstrap.min.js"></script>
    <script src="https://cdnjs.cloudflare.com/ajax/libs/cropperjs/1.5.6/cropper.js" ></script>
    <script src="/frontend/js/cufon-yui.js" ></script>
    <script src="/frontend/js/font.font.js" ></script>
    <script src="/frontend/js/setting.js" ></script>
    <script src="/frontend/js/common.js" ></script>
    <script src="/frontend/js/translate.js" ></script>
    <script src="/frontend/js/news.js" ></script>
   
    <script src="https://cdnjs.cloudflare.com/ajax/libs/mediaelement/4.2.9/mediaelement-and-player.min.js"></script> 

    <script type="text/javascript">

        $(function(){
         
            var del = localStorage.getItem('del');
            if (del >= 6) {
                $(".down-app").css('display', 'none');
            }
            $('.delete').on('click', function () {
                $(".down-app").css('display', 'none');
                del++;
                localStorage.setItem('del', del);
                var date = new Date(Date.UTC(2013, 8, 30, 12, 0, 0)); 
            });

            $(window).bind('beforeunload', function(){ 
                del = 6;
                setTimeout(function(){
                    del = 6;
                    localStorage.setItem('del', del);
            }, 10000);
                
            });

            var namePhone = window.navigator.platform;
            if(namePhone == 'iPhone' || namePhone == 'iPad'|| namePhone == 'MacIntel') {
                var typePhone = 'ios';
                $('.btn-down').click(function(){
                    window.location.href = "https://itunes.apple.com/us/app/id1107177166?l=vi&mt=8";
                });
            }else{
                var typePhone = 'android';
                $('.btn-down').click(function(){
                    window.location.href = "https://play.google.com/store/apps/details?id=mobi.eup.jpnews";
                });
            }

            var level = '{{ session('type') }}';
            var source = '{{ session('source') }}';
            if (level) {
                $('.select-level').val(level);
            }
            if (source) {
                $('.select-source').val(source);
            } else {
                $('.select-source').val('all');
            }

            $('.box-message').fadeIn(200);
            setTimeout(res => {
                $('.box-message').fadeOut(200);
                `{{ session()->forget('flash_success')}}`;
                `{{ session()->forget('flash_danger')}}`;
            }, 5000);

            //setting language
            var lang = "{{ Session::get('locale') }}";
            var arrLang = ['ko-KR', 'vi-VN', 'zh-CN', 'en-US', 'zh-TW']
            if (!lang) {
                lang = localStorage.getItem('language');
                if ( lang && !lang.includes()) {
                    lang = null;
                }
            } else {
                localStorage.setItem('language', lang);
            }
            
            if ( arrLang.includes(lang) ) {
                $('.select-show-language').val(lang);
                $('.select-show-language-mb').val(lang);

            } else {
                langUser = navigator.language || navigator.userLanguage;

                localStorage.setItem('language', langUser);

                if (langUser == 'ko-KR' || langUser == 'vi-VN' || langUser == 'zh-CN' || langUser == 'zh-TW') {
                    $('.select-show-language').val(langUser);
                    $('.select-show-language-mb').val(langUser);
                } else {
                    $('.select-show-language').val('en-US');
                    $('.select-show-language-mb').val('en-US');
                }
            }

        });

        function getHtmlTran() {
            return `<div class="box-intro">
                        <div class="text-center">- {{ trans('label.intro.intro1') }}</div>
                            <div class="text-center intro-ex">Ex: "日本", "nihon", "japan"</div>
                            <div class="text-center">- {{ trans('label.intro.intro2') }}</div>
                        <div class="text-center">Ex: "BETONAMU"</div>
                    </div> `;
        }

        function gethtmlKanji() {
            return `<div class="box-intro">
                        <div class="text-center">- {{ trans('label.intro.intro_kanji') }}</div>';
                        <div class="text-center">Ex: "公", "public"</div>
                    </div>`;
        }

        function gethtmlSentence() {
            return `<div class="box-intro">
                        <div class="text-center">- {{ trans('label.intro.intro1') }}</div>
                        <div class="text-center">Ex: "優しい", "yasashii", "kind"</div>
                    </div> `;   
        }

        function boxkanji(result, index){
            var mean = [];
            var boxKanji = '';
            langUser = localStorage.getItem('language');
            boxKanji += `<div class="kanji-bo">{{ trans('label.dictionary.radical') }} `+ result.kanji;
            if (langUser == 'vi') {
                boxKanji += ` - `+ result.mean;
            }
            boxKanji += `</div>`;
            if (result.kun !== null && typeof result.kun !== 'undefined') {
                boxKanji += `<div class="content-kanji kanji-kun">` + '訓: ' + result.kun + `</div>`
            }
            if (result.on !== null && typeof result.on !== 'undefined') {
                boxKanji += `<div class="content-kanji kanji-on">` + '音: ' + result.on + `</div>`;
            }
            if (result.stroke_count !== null && typeof result.stroke_count !== 'undefined') {
                boxKanji += `<div class="content-kanji kanji-count"> {{ trans('label.dictionary.stroke') }}: ` + result.stroke_count + `</div>`;
            }
            
            if (result.level !== null && typeof result.level !== 'undefined') {
                boxKanji += `<div class="content-kanji kanji-level"> JLPT: `+ result.level + `</div>`;
            }
        
            if (result.compDetail !== null  && typeof result.compDetail !== 'undefined') {
                boxKanji += `<div class="kanji-ingredient">{{ trans('label.dictionary.component_set') }}: `;
                for (j = 0; j < result.compDetail.length; j++) {
                    boxKanji += ` ` + result.compDetail[j].w +  ` ` + result.compDetail[j].h;
                }
            }
            
            if (index !== null && typeof index !== 'undefined') {
                boxKanji += `<div class="kanji-mean"> {{ trans('label.dictionary.mean') }}: `;
                for (e = 0; e < index.length; e++) {
                    mean[e] = index[e].split('.')
                    boxKanji +=  mean[e][0] + `, `;
                }
            }
                
            boxKanji += `</div>
                        <div class="kanji-interpret">{{ trans('label.dictionary.explain') }}:`;
            if (index !== null && typeof index !== 'undefined') {
                for (e = 0; e < index.length; e++) {
                    boxKanji+= `<div class="mean-item">`+ index[e] +`</div>`;
                }
            }
                
            boxKanji += `</div> </div>`;

            boxKanji += `<div class="exam-kanji">
            <b style='color: rgba(196,19,19,1); margin-top: 15px;'> {{ trans('label.dictionary.example') }}</b>
            <table>
                <tr>
                    <th class='tr-border exm-pub table-exmKanji'> {{ trans('label.dictionary.vocabulary') }} </th>
                    <th class='tr-border exm-pub table-exmKanji'>Hiragana</th>`
                    if(langUser == 'vi') {
                        boxKanji +=  `<th class='tr-border exm-pub table-exmKanji'>Hán việt </th>`
                    }
                    
                    boxKanji += `<th class='tr-border exm-pub table-exmKanji'> {{ trans('label.dictionary.mean') }}</th>
                </tr>`
                if (result.examples !== null && typeof result.examples !== 'undefined' ) {
                    for ( var i = 0; i < result.examples.length; i++) {
                        boxKanji +=
                            `<tr>
                                <td class='tr-border exm-pub exm-w'>`+ result.examples[i].w + `</td>
                                <td class='tr-border exm-pub exm-p'>`+ result.examples[i].p + `</td>`
                                if(langUser =='vi'){
                                    boxKanji +=`<td class='tr-border exm-pub exm-h'>`+ result.examples[i].h + `</td>`
                                }
                                boxKanji +=`<td class='tr-border exm-pub exm-m'>`+ result.examples[i].m + `</td>
                            </tr>`;
                    }
                }
            boxKanji += `</table> </div>`;

            $('.box-kanji-list').html(boxKanji);
        }
    </script>
    @yield('after-script')
<!-- END PAGE SOURCE -->
</body>
</html>
