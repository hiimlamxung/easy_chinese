@extends('frontend.layouts.master')

@section('after-css')
    {!! HTML::style('frontend/css/news.css') !!}
@endsection

@section('title', $news['title'])
@section('description', $news['desc'])
@section('url', $news['url'])
@section('image', $news['image'])
@section('key', $news['key'])

@section('main-content')
<div class="row">
    <div class="col-md-9 box-translate no-padding">
        @include('frontend.includes.filter')
        <div class="back-route">
            <a href="{{ route('web.detail',['id' => $newsId, 'hl' => $lang]) }}"><i class="fas fa-angle-left"></i> {{ trans('label.back') }}</a>
        </div>
        <div class="row box-short-info">
            <div class="col-md-4">
                <img src="{!! $detailNew->content->image !!}" alt="" class="" onerror="this.onerror=null;this.src='{{ url('/frontend/images/default-news.png') }}';" alt="{{ trans('label.seo.news_alt') }}"/>
            </div>
            <div class="col-md-8">
                <div class="short-title">{!! $detailNew->title !!}</div>
                <div class="more-info">
                    {{-- <span>Nguồn: {{ $detailNew }}</span> --}}
                    <span>{{ $detailNew->pubDate }}</span>
                </div>
                <div class="audio-player">
                    <audio id="nolAudio_html5_api" webkit-playsinline="" playsinline="playsinline" preload="auto" tabindex="-1" controls>
                        @if( strpos($detailNew->content->audio, 'mazii.net') === false )
                            <source type="application/x-mpegURL" src="https://nhks-vh.akamaihd.net/i/news/easy/{!! $detailNew->content->audio !!}/master.m3u8">
                        @else
                            <source type="audio/mp3" src="{!! $detailNew->content->audio !!}">
                        @endif
                    </audio>
                </div>
            </div>
        </div>
        <div class="row">
            <div class="col-md-4 no-pd-left">
                <div class="box-user-translate">
                    <div class="translate-title">{{ trans('label.translation') }}</div>
                    @foreach ($result as $key => $value)
                    <div class="user-translate user-{{$key}} {{ ($key == 0) ? 'user-active' : ''}}" data-id="{{ $key }}">
                        <div class="user-item trans-id-{{ $key }}">
                            <div class="info-user">
                                <span class="username">{{ $value['username'] }}</span>
                                <span class="time-user">{{ $value['timestamp'] }}</span>
                            </div>
                            <div class="reaction">
                                <span> <i class="far fa-thumbs-up"></i> {{ $value['news_like'] }}</span>
                                <span><i class="far fa-thumbs-down"></i> {{ $value['news_dislike'] }}</span>
                            </div>
                        </div>
                    </div>
                    @endforeach
                    @if (!$checkUser)
                    <div class="add-translate" data-id="{{ Auth::user() ? Auth::user()->id : 0 }}">
                        <i class="fa fa-plus-circle" aria-hidden="true"></i>
                        <span>{{ trans('label.add_translation') }}</span>
                    </div>
                    @endif
                </div>
            </div>
            <div class="col-md-8 no-pd-left">
                <div class="box-content-translate">
                    @foreach($result as $i => $item)
                        @foreach($str as $key => $value)
                        <div class="box-trans box-trans-{{ $i }} {{ ($i !== 0) ? 'hidden' : ''}} ">
                            <div class="old-data">
                                {!! $value !!}
                            </div>
                            <div id="translate" class="translate-user">
                                <img class="icon-enter icon-14" src="{{ url('frontend/images/ic_enter.png') }}" alt="{{ trans('label.tran_page') }}">   
                                {!! (isset($item['content'][$key])) ? $item['content'][$key] : '' !!}
                            </div>
                            <div id="translate-show"  class="translate-show-{{ $i }} hidden">
                                <img class="icon-enter icon-14" src="{{ url('frontend/images/ic_enter.png') }}" alt="{{ trans('label.tran_page') }}">   
                                {!! (isset($result[$i]['content'][$key])) ? $result[$i]['content'][$key] : '' !!}
                            </div>
                        </div>
                        @endforeach
                    @endforeach
                    @if ( count($result) == 0 )
                    <div class="text-center">{{ trans('label.none_translation') }}</div>
                    @endif
                </div>
                @if(!Auth::guest())
                <div class="box-create-tran">
                    <form method="POST" action="{{ route('web.add.translate') }}">
                        {{ csrf_field() }}
                        <input type="hidden" name="news_id" value="{{ $newsId }}">
                        <p class="title-trans-new">{{ trans('label.translation_of') }}: <b>{{ Auth::user()->name }}</b></p>
                        @foreach($str as $key => $value)
                        <div class="box-trans-new">
                            <div class="old-data">
                                {!! $value !!}
                            </div>
                            <div id="translate-show" class="translate-show">
                                <img class="icon-enter icon-14" src="{{ url('frontend/images/ic_enter.png') }}" alt="{{ trans('label.tran_page') }}">   
                                <input class="input-translate" placeholder="Nhập nội dung bản dịch" name="translate[{{$key}}]">
                            </div>
                        </div>
                        @endforeach
                        <div class="">
                            <button type="submit" class="btn-dictionary">{{ trans('label.add_translation') }}</button>
                        </div>
                    </form>
                </div>
                @endif
            </div>
        </div>
    </div>
    @include('frontend.includes.listnew')
</div>

@endsection