@extends('backend.layouts.master')

@section('title', 'Admin-News')
@section('after-script')
    <script src="{{ url('backend/js/news/news.js') }}"></script>
    <script>

        $('.auto-furi').focusout(function(){
            var str = $(this).html().trim();
            var name  = $(this).attr('name');
            str = str.replace(/\r\n|\n|\r/g, '<br />');
            $.ajax({
                url: "",
                type: 'POST',
                data: {
                    text: str
                },
                headers: {'X-CSRF-TOKEN': $('meta[name=csrf-token]').attr('content')},
                success: function(res){
                    $('.view-pinyin-' + name).html(res);
                },
                error: function(e) {
                    console.log(e);
                }
            });
        })
        
        // send form edit
        $('#btn-send-news').click(function(){
            var title = $('#view-pinyin-title').html().trim();
            var description = $('#view-pinyin-description').html().trim();
            var content = $('#view-pinyin-content').html().trim();

            var data = new FormData();
            data.append('title', title);
            data.append('description', description);
            data.append('content', content);

            $.ajax({
                url: "{{ route('admin.news.edit', $id) }}",
                type: 'POST',
                data: data,
                processData: false,
                contentType: false,
                headers: {'X-CSRF-TOKEN': $('meta[name=csrf-token]').attr('content')},
                success: function(res){
                    res = res.trim();
                    if (res == 'success') {
                        alert('Success');
                        window.location.reload(true);
                    } else {
                        alert('Có lỗi xảy ra.');
                    }
                    
                },
                error: function(e) {
                    console.log(e);
                }
            });
        })

        // comment
        // Make shortcut
        $('input[name="message"]').focus(function(e){
            e.preventDefault();
            $(this).keyup(function(event){
                if(event.keyCode == 13){
                    $('.btn-send').click();
                }
            })
        })

        $('.btn-send-comment').click(function(){
            var message = $('input[name="message"]').val();
            var news = $(this).data('id');
            var error = $(this).data('type');

            if(message == ''){
                return;
            }
            $.ajax({
                type:'post',
                url:'{{ route("admin.comment.add") }}',
                headers: {'X-CSRF-TOKEN': $('meta[name="csrf-token"]').attr('content')},
                data: {
                    message: message,
                    news: news,
                    error: error
                },
                success: function(data) {
                    window.location.reload();
                },
                error: function(e) {
                    console.log(e);
                }
            });
        })
       
    </script>
@endsection

@section('main')
<div class="row box-infor">
    <div class="page-title">
        <div class="title_left">
            <h3>Báo Mới</h3>
        </div>
    </div>
    
    <form class="box-body box-detail-news" action="" method="POST" name="form-news" enctype="multipart/form-data">
        <input type="hidden" name="_token" value="{{ csrf_token() }}"> 

        <div class=" box-show-news form-group" id="view-content">
            <label>View:</label>
            <div class="view-title view-pinyin view-pinyin-title text-news" id="view-pinyin-title" name="title">
                {!! $news->title !!}
            </div>

            <div class="view-pinyin view-pinyin-description text-news" id="view-pinyin-description" name="description">
                {!! $news->description !!}
            </div>

            <div class="view-pinyin view-news view-pinyin-content text-news" id="view-pinyin-content" name="content">
                {!! $news->content !!}
            </div>

            <div class="view-link" style="float: right;">
                Nguồn：<a href="{{ $news->link }}" target="_blank">{{ $news->name_link }}</a>
            </div>

            <div class="clearfix"></div>

            <div class="btn-edit m-t-10">
                <button type="button" class="pull-right btn btn-primary" id="btn-send-news" style="margin-top: 30px;">
                    Sửa bài báo
                </button>
            </div>
        </div>
    </form>

    <div class="box-comments">
        @foreach ($news['comments'] as $comment)
        <div class="box-comment">
            <img class="img-circle img-sm image-user" src="{{ (!empty($guard->image)) ? $guard->image : url('images/img.jpg') }}" alt="User Image">

            <div class="comment-text">
                <div class="username">
                    {{ $comment['user']->username }}
                    <span class="text-muted pull-right">{{ $comment->created_at->format('d-m-Y H:i:s') }}</span>
                </div>
                <div style="color: {{ ($comment->errors) ? 'red' : '' }};">
                        {{ $comment->comment }}
                </div>
            </div>
        </div>
        @endforeach
    </div>
    <div class="box-add-comment">
        <img class="img-circle img-sm image-user" src="{{ (!empty($guard->image)) ? $guard->image : url('images/img.jpg') }}" alt="Alt Text">
        
        <div class="input-group input-comment">
            <input type="text" name="message" placeholder="Comment ..." class="form-control">
            <span class="input-group-btn">
                <button type="submit" class="btn btn-primary btn-flat btn-send-comment btn-send" data-type="0" data-id="{{ $news->id }}">Gửi <i class="fa fa-send"></i></button>
                @if (in_array(Auth::guard('admin')->user()->role, [1,3]))
                <button type="submit" class="btn btn-warning btn-flat btn-send-comment" data-type="1" data-id="{{ $news->id }}">Báo lỗi <i class="fa fa-warning"></i></button>
                @endif
            </span>
        </div>
    </div>
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