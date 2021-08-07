@extends('frontend.layouts.master')

@section('title', $seoWeb['title'])
@section('description', $seoWeb['desc'])
@section('url', $seoWeb['url'])
@section('key', $seoWeb['key'])
@section('image', $seoWeb['image'])

@section('after-css')
    {!! HTML::style('frontend/css/news.css') !!}
@endsection

@section('main-content')

<div class="row">
    <div class="col-md-9 no-pd-right" id="articles"> 
        <div class="box-news">
            <div class="all-news">
                <a class="first-item" href="{{ route('web.detail',['id' => $firstNews->id, 'hl' => $lang]) }}">
                    <img class="image-new lazy" src="{{ (!empty($firstNews->image)) ? $firstNews->image : url('/frontend/images/default-news.png') }}" 
                        onerror="this.onerror=null;this.src='{{ url('/frontend/images/default-news.png') }}';" alt="{{ trans('label.seo.news_alt') }}"/>
                    <!-- <img class="image-new lazy" src="{{ url('/frontend/images/default-news.png') }}" data-src="{{ (!empty($firstNews->image)) ? $firstNews->image : url('/frontend/images/default-news.png') }}" 
                        onerror="this.onerror=null;this.src='{{ url('/frontend/images/default-news.png') }}';" alt="{{ trans('label.seo.news_alt') }}"/> -->
                    <div class="article-info">
                        <div class="source">
                        </div>
                        <h1 class="title">{!! $firstNews->title !!}</h1>
                        <div>
                            <span class="time-up"> {!! $firstNews->date !!} </span>
                        </div>
                    </div>
                </a>
                <div class="row list-item">
                    @if (sizeof($top) < 5)
                        @foreach($top as $key => $item)
                        <div class="col-md-3">
                            <div class="panel panel-default">
                                <div class="panel-thumbnail">
                                    <a class="news-item" href="{{ route('web.detail',['id' => $item->id, 'hl' => $lang]) }} ">
                                        <div class="item-content">
                                            <!-- <img class="image-carousel lazy" src="{{ url('/frontend/images/default-news.png') }}" data-src="{{ ($item->content->image) ? $item->content->image : url('/frontend/images/default-news.png') }}" 
                                                onerror="this.onerror=null;this.src='{{ url('/frontend/images/default-news.png') }}';" alt="{{ trans('label.seo.news_alt') }}"/> -->
                                            <img class="image-carousel lazy" src="{{ ($item->image) ? $item->image : url('/frontend/images/default-news.png') }}" 
                                                onerror="this.onerror=null;this.src='{{ url('/frontend/images/default-news.png') }}';" alt="{{ trans('label.seo.news_alt') }}"/>
                                            <div class="source"> China Daily </div>
                                        </div>
                                        <div class="news-title">{!! $item->title !!}</div>
                                        <div class="time-up">{{ $item->date }}</div>
                                    </a>
                                </div>
                            </div>
                        </div>
                        @endforeach
                    @else
                    <div class="row">
                        <div id="myCarousel" class="carousel slide" data-ride="carousel" data-interval="4000">
                            <div class="carousel-inner" role="listbox">
                                @foreach($top as $key => $item)
                                <div class="carousel-item {{ ($key == 0) ? 'active' : ''}}" data-key="{{$key + 1}}">
                                    <div class="col-md-3">
                                        <a class="news-item" href="{{ route('web.detail',['id' => $item->id, 'hl' => $lang]) }} ">
                                            <div class="item-content">
                                                <img class="image-carousel lazy" src="{{ ($item->image) ? $item->image : url('/frontend/images/default-news.png') }}" 
                                                    onerror="this.onerror=null;this.src='{{ url('/frontend/images/default-news.png') }}';" alt="{{ trans('label.seo.news_alt') }}"/>
                                                <!-- <img class="image-carousel lazy" src="{{ url('/frontend/images/default-news.png') }}" data-src="{{ ($item->image) ? $item->image : url('/frontend/images/default-news.png') }}" 
                                                    onerror="this.onerror=null;this.src='{{ url('/frontend/images/default-news.png') }}';" alt="{{ trans('label.seo.news_alt') }}"/> -->
                                                <div class="source"> China Daily </div>
                                            </div>
                                            <div class="news-title">{!! $item->title !!}</div>
                                            <div class="time-up">{{ $item->date }}</div>
                                        </a>
                                    </div>
                                </div>
                                @endforeach
                            </div>
                            <a class="carousel-control-prev" href="#myCarousel" role="button" data-slide="prev">
                                <i class="fa fa-chevron-left" aria-hidden="true"></i>
                            </a>
                            <a class="carousel-control-next text-faded" href="#myCarousel" role="button" data-slide="next">
                                <i class="fa fa-chevron-right" aria-hidden="true"></i>
                            </a>
                            <ol class="indicators">
                                @foreach($top as $key => $item)
                                <li id="key-{{ $key + 1 }}" class="indicators-index {{ ($key == 0) ? 'active' : '' }}"></li>
                                @endforeach
                            </ol>
                        </div>
                    </div>
                    
                    @endif
                </div>
            </div>
            <div class="recent-news">
                <ul class="nav nav-pills">
                    <li class="nav-item">
                        <a class="nav-link active" data-toggle="pill" href="#yesterday">
                            <span>{{ $newsAgo->date }}</span>
                            <div class="active-tab"></div>
                        </a>
                    </li>
                    @foreach ($listDateAgo as $key => $item)
                    <li class="nav-item">
                        <a class="nav-link another-news" data-toggle="pill" href="#another" data-time="{{ $item }}">
                            <span>{{ $key }}</span>
                            <div class="active-tab"></div>
                        </a>
                    </li>
                    @endforeach
                </ul>
                        
                <div class="tab-content">
                    <div class="tab-pane active" id="yesterday">
                        @foreach($newsAgo->data as $key => $item)
                            <a class="row no-margin item-recent {{ ($key > 4) ? 'news-more' : '' }}" href="{{ route('web.detail',['id' => $item->id, 'hl' => $lang ]) }}">
                                <div class="col-md-3 no-pd-left">
                                    <!-- <img class="lazy" src="{{ url('/frontend/images/default-news.png') }}" data-src="{{ ($item->image) ? $item->image : url('/frontend/images/default-news.png') }}" 
                                        onerror="this.onerror=null;this.src={{ url('/frontend/images/default-news.png') }}';" alt="{{ trans('label.seo.news_alt') }}"/> -->
                                    <img class="lazy" src="{{ ($item->image) ? $item->image : url('/frontend/images/default-news.png') }}"  
                                        onerror="this.onerror=null;this.src={{ url('/frontend/images/default-news.png') }}';" alt="{{ trans('label.seo.news_alt') }}"/>
                                </div>
                                <div class="col-md-9 new-infor">
                                    <div class="recent-title">{!! $item->title !!}</div>
                                    <div class="time-up">
                                        <span>{{ trans('label.source') }}: China Daily</span>
                                        <span class="f-right">{{ $item->date }}</span>
                                    </div>
                                </div>
                            </a>
                        @endforeach
                        @if( sizeof($newsAgo->data) > 5)
                        <div class="view-more-news"> {{ trans('label.show_more') }} <i class="fas fa-chevron-down"></i></div>
                        @endif
                    </div>
                    <div class="tab-pane" id="another"></div>
                </div>
            </div>
        </div>
    </div>
    @include('frontend.includes.sidebar')
</div>

@endsection
@section('after-script')

@endsection