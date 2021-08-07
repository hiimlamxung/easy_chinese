<div class="row no-margin search-filter">
    <div class="col-md-2 no-pd-left">
        <div class="search-button">
            <i class="fas fa-search"></i>
            {{ trans('label.search') }}
        </div>
    </div>
    <div class="col-md-10 no-padding">
        <div class="row box-filter no-margin-all">
            <div class="col-md-2 no-padding-all col-2">{{ trans('label.filter') }}</div>
            <div class="col-md-5 col-5 level">
                <span>{{ trans('label.level') }}:</span>
                <select class="sl-custom select-level">
                    <option value="easy" data-href="{{ route('web.news', ['type' => 'easy', 'hl' => $lang ]) }}">
                        Easy
                    </option>
                    <option value="normal" data-href="{{ route('web.news', ['type' => 'normal', 'hl' => $lang ]) }}">Normal</option>
                </select>
            </div>
            <div class="col-md-5 col-5 from">
                <span>{{ trans('label.source') }}:</span>
                <select class="sl-custom select-source">
                    <option value="all" data-href="{{ route('web.news', ['source' => '', 'hl' => $lang]) }}">
                        {{ trans('label.all') }}
                    </option>
                    <option value="NHK" data-href="{{ route('web.news', ['hl' => $lang]) }}">NHK</option>
                    <option value="Asahi" data-href="{{ route('web.news',[ 'hl' => $lang]) }}">Asahi</option>
                    <option value="BBC" data-href="{{ route('web.news',['hl' => $lang])  }}">BBC</option>
                    <option value="CNN" data-href="{{ route('web.news', ['hl' => $lang]) }}">CNN</option>
                    <option value="TBS" data-href="{{ route('web.news', ['hl' => $lang]) }}">TBS</option>
                    <option value="Google" data-href="{{ route('web.news', ['hl' => $lang]) }}">Google</option>
                    <option value="Chunichi" data-href="{{ route('web.news', ['hl' => $lang]) }}">Chunichi</option>
                </select>
            </div>
        </div>
    </div>
</div>   