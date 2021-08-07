@if (Session::get('flash_success'))
    <div class="box-message alert alert-success alert-dismissible">
        <button type="button" class="close" data-dismiss="alert" aria-label="Close">
            <span aria-hidden="true">&times;</span>
        </button>
        @if(is_array(json_decode(Session::get('flash_success'),true)))
            {!! implode('', Session::get('flash_success')->all(':message<br/>')) !!}
        @else
            {!! Session::get('flash_success') !!}
        @endif
    </div>
@elseif (Session::get('flash_warning'))
    <div class="box-message alert alert-warning alert-dismissible">
        <button type="button" class="close" data-dismiss="alert" aria-label="Close">
            <span aria-hidden="true">&times;</span>
        </button>
        @if(is_array(json_decode(Session::get('flash_warning'),true)))
            {!! implode('', Session::get('flash_warning')->all(':message<br/>')) !!}
        @else
            {!! Session::get('flash_warning') !!}
        @endif
    </div>
@elseif (Session::get('flash_info'))
    <div class="box-message alert alert-info alert-dismissible">
        <button type="button" class="close" data-dismiss="alert" aria-label="Close">
            <span aria-hidden="true">&times;</span>
        </button>
        @if(is_array(json_decode(Session::get('flash_info'),true)))
            {!! implode('', Session::get('flash_info')->all(':message<br/>')) !!}
        @else
            {!! Session::get('flash_info') !!}
        @endif
    </div>
@elseif (Session::get('flash_danger'))
    <div class="box-message alert alert-danger alert-dismissible">
        <button type="button" class="close" data-dismiss="alert" aria-label="Close">
            <span aria-hidden="true">&times;</span>
        </button>
        @if(is_array(json_decode(Session::get('flash_danger'),true)))
            {!! implode('', Session::get('flash_danger')->all(':message<br/>')) !!}
        @else
            {!! Session::get('flash_danger') !!}
        @endif
    </div>
@elseif (Session::get('flash_message'))
    <div class="box-message alert alert-info alert-dismissible">
        <button type="button" class="close" data-dismiss="alert" aria-label="Close">
            <span aria-hidden="true">&times;</span>
        </button>
        @if(is_array(json_decode(Session::get('flash_message'),true)))
            {!! implode('', Session::get('flash_message')->all(':message<br/>')) !!}
        @else
            {!! Session::get('flash_message') !!}
        @endif
    </div>
@endif
