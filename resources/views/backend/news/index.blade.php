@extends('backend.layouts.master')

@section('title', 'Admin-News')
@section('after-css')
<style>
    .box-body {
        padding: 10px;
    }
</style>
@endsection
@section('after-script')
    <script src="https://cdn.ckeditor.com/4.16.0/standard/ckeditor.js"></script>
    <script src="{{ url('backend/js/news/news.js') }}"></script>
    <script>
        $(function () {
            CKEDITOR.replace('news-content');
            CKEDITOR.on('instanceReady', function(evt) {
                var editor = evt.editor;
                editor.on('blur', function(e){
                    var content = CKEDITOR.instances['news-content'].getData();
                    content = content.split('<a href=\"http://a\">').join("<a href='javascript:void(0)' class='dicWin'>");
                    content = content.trim();

                    if (checkCoronaText(content)) {
                        convertPinyin(content, 'view-news');
                    }
                })
            });

            $('.auto-pinyin').focusout(function(){
                var str = $(this).val();
                var name  = $(this).attr('name');
                str = str.replace(/\r\n|\n|\r/g, '<br />');

                if (checkCoronaText(str)) {
                    convertPinyin(str, 'view-pinyin-' + name);
                }
            });

            $('#link').focusout(function(){
                var link = $(this).val();
                $('.view-link a').attr('href', link);
            })
            
            $('#name-link').focusout(function(){
                var name = $(this).val();
                $('.view-link a').html(name);
            })

            // send form
            $('#btn-send-news').click(function(){
                var title = $('#view-pinyin-title').html();
                var des   = $('#view-pinyin-description').html();
                var news  = $('#view-news').html();
                var image = $('#img-news')[0].files[0];
                var pubDate = $('input[name=pub_date]').val();
                var link  = $('input[name=link]').val().trim();
                var video = $('input[name=video]').val();
                var nameLink  = $('input[name=name-link]').val().trim();
                var kind = $('input[name=kind]').val().trim();

                if(title == '' || title == null || news == '' || news == null){
                    swal.fire('Tiêu đề hoặc nội dung rỗng!');
                    return;
                }
                if(des == '' || des == null ){
                    swal.fire('Mô tả rỗng!');
                    return;
                }
                if(nameLink == '' || nameLink == null ){
                    swal.fire('Bạn chưa nhập tên nguồn!');
                    return;
                }
                if(kind == '' || kind == null ){
                    swal.fire('Bạn chưa nhập thể loại!');
                    return;
                }
                if(pubDate == '' || pubDate == null){
                    swal.fire('Nhập ngày đăng bài.');
                    return;
                }
                if(link == '' || link == null){
                    swal.fire('Bạn chưa trích nguồn!');
                    return;
                }
                if(image == null || image == 'undefined' || image == ''){
                    swal.fire('Thêm ảnh bài báo!');
                    return;
                }

                var formData = new FormData();
                formData.append('image', image);
                formData.append('description', des);
                formData.append('title', title);
                formData.append('pub_date', pubDate);
                formData.append('content', news);
                formData.append('link', link);
                formData.append('video', video);
                formData.append('kind', kind);
                formData.append('nameLink', nameLink);
                $.ajax({
                    url: '{{ route("admin.create.news") }}',
                    type: 'POST',
                    data: formData,
                    processData: false,  // tell jQuery not to process the data
                    contentType: false,
                    headers: {'X-CSRF-TOKEN': $('meta[name=csrf-token]').attr('content')},
                    statusCode: {
                        301: function(){
                            swal.fire('Link bài báo đã tồn tại.');
                        },
                        302: function(){
                            swal.fire('Tiêu đề bài báo đã tồn tại.');
                        },
                        401: function(){
                            swal.fire('Thêm ảnh bài báo!');
                        },
                        400: function(){
                            swal.fire('Bài báo có chứa ký tự corona hoặc nội dung về corona!');
                        },
                        500: function(){
                            swal.fire('Có lỗi xảy ra.');
                        }
                    },
                    success: function(res){
                        swal.fire("Good job!", "Thêm bài báo thành công!", "success");
                        window.location.reload();
                    },
                    error: function (data) {
                        let error = $.parseJSON(data.responseText);
                        swal.fire(error);
                    }
                });
            })

            $("#img-news").change(function(){
                readURL(this);
            });

            $('.btn-search').click(function(){
                var link = $('.input-search').val();

                if(link == '' || link == null){
                    swal.fire("Bạn chưa nhập link");
                    return;
                }

                $.ajax({
                    type:'post',
                    url:'{{ route("admin.news.search") }}',
                    headers: {'X-CSRF-TOKEN': $('meta[name="csrf-token"]').attr('content')},
                    data: {
                        link: link
                    },
                    success:function(res){
                        swal.fire(res.trim());
                    }
                });
            });

            function readURL(input) {
                if (input.files && input.files[0]) {
                    var reader = new FileReader();
                    
                    reader.onload = function (e) {
                        $('#img-view-news').attr('src', e.target.result);
                    }
                    
                    reader.readAsDataURL(input.files[0]);
                }else{
                    $('#img-view-news').attr('src', '');
                }
            }

            function checkCoronaText(str) {
                var check = str.match(/疫情|新型冠状病毒|肺炎疫情|确诊病例|死亡病例|接触者|重症病例|疑似病例|接种|疫苗|冠状病毒疫苗接种|冠狀/g);

                if (check) {
                    swal.fire('Nội dung có chứa corona!');
                    return false;
                } else {
                    return true;
                }
            }
        })

        function convertPinyin(str, class_name) {
           
            if (str != '' ) {
                $.ajax({
                    url: "{{ route('admin.pinyin.convert') }}",
                    type: 'POST',
                    data: {
                        text: str
                    },
                    headers: {'X-CSRF-TOKEN': $('meta[name=csrf-token]').attr('content')},
                    success: function(res){
                        $('.' + class_name).html(res);
                    },
                    error: function(e) {
                        console.log(e);
                    }
                });
            } else {
                $('.' + class_name).html('');
            }
        }
    </script>
@endsection

@section('main')
<div class="row">
    <div class="page-title">
        <div class="title_left">
            <h3>Báo Mới</h3>
        </div>
        <div class="title_right">
            <div class="col-md-5 col-sm-5 col-xs-12 form-group pull-right top_search">
                <div class="input-group">
                    <input type="text" name="search" class="form-control input-search" placeholder="Nhập link báo ...">
                    <span class="input-group-btn">
                        <button class="btn btn-default btn-search" type="button">Go!</button>
                    </span>
                </div>
            </div>
        </div>
    </div>
    <form action="{{ route('admin.news.create') }}" method="POST" name="form-news" enctype="multipart/form-data">
        <div class="box-body row">
            <input type="hidden" name="_token" value="{{ csrf_token() }}"> 
            <div class="form-group col-md-6">
                <label>Ngày đăng:</label>
                <div class="input-group date">
                    <div class="input-group-addon"><i class="fa fa-calendar"></i></div>
                    <input type="date" name="pub_date" class="form-control pull-right" placeholder="Ngày đăng">
                </div>
            </div>
            <div class="form-group col-md-6">
                <label>Lĩnh vực:</label>
                <input type="text" class="form-control" placeholder="Lĩnh vực..." id="kind"  name="kind">
            </div>
            <div class="form-group col-md-12">
                <label class="title">Tiêu đề:</label>
                <input type="text" class="form-control auto-pinyin" name="title" id="title" placeholder="Tiêu đề...">
            </div>
            <div class="form-group col-md-12">
                <label>Mô tả:</label>
                <textarea class="form-control none-resize auto-pinyin" placeholder="Mô tả..." id="description"  name="description"></textarea>
            </div>
            <div style="margin-bottom: 20px;" class="form-group col-md-12">
                <label>Link nguồn:</label>
                <input type="text" class="form-control" placeholder="Link nguồn..." id="link"  name="link">
            </div>
            <div  class="form-group col-md-12">
                <label>Tên nguồn:</label>
                <input type="text" class="form-control" placeholder="Tên nguồn..." id="name-link"  name="name-link">
            </div>
        
            <div class="form-group col-md-12">
                <label>Video:</label>
                <input type="text" class="form-control" placeholder="Link video..." id="video"  name="video">
            </div>
            <div class="form-group col-md-12">
                <label>Hình ảnh:</label>
                <input type="file" class="form-control" id="img-news" name="image" placeholder="Hình ảnh">
            </div>
            <div class="bg-news-content col-md-12">
                <label>Nội dung:</label>
                <textarea placeholder="Nội dung..." class="textarea" id="news-content" name="content"></textarea>
            </div>
            <div class="box-show-news form-group col-md-12">
                <label>View:</label>

                <div class="img-view view-pinyin">
                    <div class="box-view-audio-video">
                        <img src="" alt="" id="img-view-news">
                    </div>
                </div>
                <div class="view-title view-pinyin-title text-news" id="view-pinyin-title"></div>

                <div class="view-pinyin-description text-news" id="view-pinyin-description"></div>

                <div class="view-news text-news" id="view-news"></div>

                <div class="view-link" style="float: right;">
                    Nguồn：<a href="" target="_blank"></a>
                </div>
            </div>
        </div>
        <div class="box-footer clearfix">
            <button type="button" class="pull-right btn btn-primary" id="btn-send-news">Thêm bài báo
                <i class="fa fa-arrow-circle-right"></i>
            </button>
        </div>
    </form>
    <div class="input-group hidden" id="input-edit-pinyin">
        <input type="text" name="" class="form-control pull-left" id="text-pinyin">
        <input type="hidden" name="" class="form-control pull-left" id="text-change-hidden">
        <input type="hidden" name="" class="form-control pull-left" id="text-kanji-hidden">
        <div class="input-group-addon" id="save-pinyin-edit">
            <i class="fa fa-check"></i>
        </div>
        <div class="input-group-addon" id="delete-pinyin-edit">
            <i class="fa fa-trash-o"></i>
        </div>
    </div>
</div>

@endsection