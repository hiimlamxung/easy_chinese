<div class="col-md-3" id="sidebar">
    <div class="box-proverbs content-result-white">
        <div class="title-list"> Thành ngữ hôm nay </div>
        <div class="content">
            <p>{{ $proverb->example }}</p>
            <div>{{ $proverb->pinyin }}</div>
        </div>
    </div>
    <div class="box-tags">
        <a class="tags-item" target="_blank" href="https://www3.nhk.or.jp/news/easy/article/disaster_rain.html"> #{{ trans('label.tip.heavy_rain') }} </a>
        <a class="tags-item" target="_blank" href="https://www3.nhk.or.jp/news/easy/article/disaster_snow.html"> #{{ trans('label.tip.snow') }} </a>
        <a class="tags-item" target="_blank" href="https://www3.nhk.or.jp/news/easy/article/disaster_tornado.html"> #{{ trans('label.tip.tornado') }} </a>
        <a class="tags-item" target="_blank" href="https://www3.nhk.or.jp/news/easy/article/disaster_typhoon.html"> #{{ trans('label.tip.storm') }} </a>
        <a class="tags-item" target="_blank" href="https://www3.nhk.or.jp/news/easy/article/disaster_tsunami.html"> #{{ trans('label.tip.tsunami') }} </a>
    </div>
</div>