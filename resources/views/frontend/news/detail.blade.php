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
    <div class="col-md-9 no-pd-right" id="detail" >
        <div class="back-route">
            <a href="{{ route('web.news', ['type' => Session::get('type'), 'hl' => $lang] ) }}">
                <i class="fas fa-angle-left"></i> {{ trans('label.back') }}
            </a>
        </div>
        <div class="box-detail">
            <div id="h3-bold-detail">{!! $detail->title !!}</div>
           
            <div class="public-date">
                <span>{!! $detail->date !!}</span>
                <div class="hide-furigana">
                    <span class="fuji">Pinyin</span>
                    <input type="checkbox" name="hide-fuji" class="switch pinyin-button">
                </div>
            </div>
            <div class="content-media">
                @if(($detail->content->video)!=null)
                    <video style="width: 100%; height: 100%;" preload="auto" src="{!! $detail->content->video !!}">
                    </video>
                @else
                    <img src="{!! $detail->content->image !!}" onerror="this.onerror=null;this.src='{{ url('/frontend/images/default-news.png') }}';" alt="{{ trans('label.seo.news_alt') }}" class="" />
                @endif
            </div>
            <div class="audio-player">
                <audio id="nolAudio_html5_api" webkit-playsinline="" playsinline="playsinline" preload="auto" tabindex="-1" controls>
                    @if($detail->content->audio)
                        <source type="application/x-mpegURL" src="">
                    @endif
                </audio>
            </div>
            <div class="content">
                {!! $detail->content->body !!}
            </div>
            <div class="level-intro">
                <div class="box-level"> 
                    <div class="line-level level-5"></div>
                    <span>: N5</span>
                </div>
                <div class="box-level"> 
                    <div class="line-level level-4"></div>
                    <span>: N4</span>
                </div>
                <div class="box-level"> 
                    <div class="line-level level-3"></div>
                    <span>: N3</span>
                </div>
                <div class="box-level"> 
                    <div class="line-level level-2"></div>
                    <span>: N2</span>
                </div>
                <div class="box-level"> 
                    <div class="line-level level-1"></div>
                    <span>: N1</span>
                </div>
            </div>
            <div class="text-center translate-button">
            </div>
        </div>
        @if(sizeof($listRelated))
        <label class="related-title">{{ trans('label.related_new') }}</label>
        @endif
        @foreach($listRelated as $item)
        <div class="row no-margin box-short-info">
            <div class="col-md-4">
                <div class="img-wrapper">
                    <img src="{!! $item->image !!}" alt="" class="" onerror="this.onerror=null;this.src='{{ url('/frontend/images/default-news.png') }}';" alt="{{ trans('label.seo.news_alt') }}"/>
                </div>
            </div>
            <div class="col-md-8">
                <a class="short-title" href="{{ route('web.detail',['id' => $item->id, 'hl' => $lang ]) }}">{!! $item->title !!}</a>
                <div>{!! $item->desc !!}</div>
                <div class="more-info">
                    <span>{{ trans('label.source') }}: {{ $item->source }}</span>
                    <span class="publish-date">{{ $item->key }}</span>
                </div>
            </div>
        </div>
        @endforeach
    </div> 
</div>

@endsection
