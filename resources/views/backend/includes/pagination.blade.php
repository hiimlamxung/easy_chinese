<div class="row" id="paginate">
    <div class="col-sm-6">
        <div class="dataTables_info">
            Đang hiển thị <strong>{!! ($data->total() > $data->perPage()) ? $data->perPage() : $data->total()  !!}</strong> trên tổng số <strong>{!! $data->total() !!}</strong> bản ghi
        </div>
    </div>
    <div class="col-sm-6">
        <div class="dataTables_paginate paging_simple_numbers">
            {!! $data->appends($appended)->render() !!}
        </div>
    </div>
</div>