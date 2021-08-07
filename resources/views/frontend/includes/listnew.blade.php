<div class="col-md-3" id="sidebar">
    <div class="box-sidebar">
        @foreach($lists as $item)
        <a class="item-sidebar" href="{{ route('web.detail',['id' => $item->id, 'hl' => $lang]) }}" data-id="{{ $item->id }}" id='{{ $item->id }}'>
            <div class="img-bg-default">
                <img src="{{ (!empty($item->value->image)) ? $item->value->image : url('/frontend/images/default-news.png') }}" alt="" class="img-sidebar-news" 
                    onerror="this.onerror=null;this.src='{{ url('/frontend/images/default-news.png') }}';"/>
                {{-- <img class="country" src='{{( !($item->count == 0)) ? trans ('label.link-country') : ''}}'> --}}
                {{-- <p class="count-first">{{ ( !($item->count == 0)) ? $item->count : ''}}</p> --}}
            </div>
            <div class="link-sidebar" >{!! $item->value->title !!}</div>     
            <div class="more-data">
                @if (isset($item->value->source))
                <span>{{ $item->value->source }}</span>
                @endif
                <span class="time"> {{ $item->key }}</span>
            </div>    
        </a>
        @endforeach
    </div>
</div>