@extends('backend.layouts.master')

@section('title', 'Admin-Users')

@section('after-script')
    <script>
        $(function(){
            $('.fillter_month').change(function(){
                var month = $(this).val();
                var year = $('.fillter_year').val();
                var url = '{{ route("admin.statistical.ctv") }}' + '?month=' + month + '&year=' + year + '&id={{$user->id}}';
                window.location.href = url;
            })
            $('.fillter_year').change(function(){
                var year = $(this).val();
                var month = $('.fillter_month').val();
                var url = '{{ route("admin.statistical.ctv") }}' + '?month=' + month + '&year=' + year + '&id={{$user->id}}';
                window.location.href = url;
            })
            
            $('.fillter_ctv').change(function(){
                var id = $(this).val();
                var url = '{{ route("admin.statistical.ctv") }}' + '?month={{ $month }}&year={{ $year}}&id=' + id;
                window.location.href = url;
            })
        })
    </script>
@endsection

@section('main')
<section class="content-header">
    <h4>
        Thống Kê Số Bài Báo:
        <select name="month" id="" class="fillter_month">
            @for ($i = 1; $i < 13; $i++)
                <option value="{{ $i }}" {{ ($i == $month) ? 'selected' : ''}}>Tháng {{ $i }}</option>
            @endfor
        </select>
        <select name="year" id="" class="fillter_year">
            @for ($i = 2021; $i < 2026; $i++)
            <option value="{{ $i }}" {{ ($i == $year) ? 'selected' : ''}}>Năm {{ $i }}</option>
            @endfor
        </select>
    </h4>
    @if (Auth::guard('admin')->user()->role == 1)
    <h4>
        CTV: 
        <select name="ctv" id="" class="fillter_ctv">
            <option value="0">Chọn cộng tác viên</option>
            @foreach ($listCTV as $value)
                <option value="{{ $value->id }}" {{ ($value->id == $user->id) ? 'selected' : ''}}>{{ $value->username }}</option>
            @endforeach
        </select>
    </h4>
    @endif
</section>
<div class="box-info">
    <div class="box-header with-border">
        <div class="col-md-2">
            <p>Số bài báo: <strong>{{ $statis['total'] }}</strong></p>
        </div>
        <div class="col-md-3">
            <p>Số bài đã phát hành: <strong>{{ $statis['success'] }}</strong></p>
        </div>
        <div class="col-md-3">
            <p>Số bài chờ phát hành: <strong>{{ $statis['posted'] }}</strong></p>
        </div>
        <div class="col-md-2">
            <p>Số bài bị xoá: <strong>{{ $statis['deleted'] }}</strong></p>
        </div>
        <div class="col-md-2">
            <p>Số bài có lỗi: <strong>{{ $statis['error'] }}</strong></p>
        </div>
    </div>
    <div class="box-body">
        <div class="row">
            <div class="col-sm-12">
                <table class="table table-striped table-bordered table-hover tbl-vertical-middle">
                    <thead>
                        <tr>
                            <th>STT</th>
                            <th>Người Viết</th>
                            <th>Tiêu Đề</th>
                            <th>Ngày Viết</th>
                            <th>Trạng Thái</th>
                            <th>Lỗi</th>
                            <th>Đơn Vị (VNĐ)</th>
                            <th>Chiết Khấu (VNĐ)</th>
                            <th>Thành Tiền</th>
                        </tr>
                    </thead>
                    <tbody>
                        @foreach ($news['news'] as $key => $new)
                            <tr>
                                <td>{{$key + 1}}</td>
                                <td>{{$user->username}}</td>
                                <td>
                                    <div style="word-break: break-all;" class="info">
                                        <a href="{{route('admin.news.edit', $new->id)}}">{!!$new->title!!}</a>
                                    </div>
                                </td>
                                <td>
                                    {{$new->created_at->format('d-m-Y')}}
                                </td>
                                <td>
                                    @if ($new->status == -1)
                                    <button class="btn btn-sm btn-block btn-danger">
                                        Đã xoá
                                    </button>
                                    @endif
                                    @if ($new->status == 0)
                                    <button class="btn btn-sm btn-block btn-{{(count($new['comments'])) ? 'warning' : 'default'}}">
                                        Mới viết
                                    </button>
                                    @endif
                                    @if ($new->status == 1)
                                    <button class="btn btn-sm btn-block btn-{{(count($new['comments'])) ? 'warning' : 'info'}}">
                                        Chờ phát hành
                                    </button>
                                    @endif
                                    @if ($new->status == 2)
                                    <button class="btn btn-sm btn-block btn-{{(count($new['comments'])) ? 'warning' : 'success'}}">
                                        Đã phát hành
                                    </button>
                                    @endif
                                </td>
                                <td>
                                    @if (count($new['comments']))
                                        @foreach ($new['comments'] as $k => $comment)
                                            <p>{{ $k+1 }}. {{ $comment->comment }}</p>
                                        @endforeach
                                    @endif
                                </td>
                                <td class="">
                                    {{ number_format($price, 0, '.', '.')}}
                                </td>
                                <td class="">
                                    {{ number_format($new->sale, 0, '.', '.') }}
                                </td>
                                <td class="">
                                    {{ number_format($new->price, 0, '.', '.') }}
                                </td>
                            </tr>
                        @endforeach
                        <tr>
                            <td colspan="6"><strong>Tổng</strong> (Thưởng 10% của tháng nếu số bài không lỗi/số bài phát hành >= 70%)</td>
                            <td colspan="3" class="">
                                <strong>{{ number_format($statis['price'], 0, '.', '.') }} + {{ number_format($statis['offer'], 0, '.', '.')}} = {{ number_format($statis['price']+$statis['offer'], 0, '.', '.') }}</strong>
                            </td>
                        </tr>
                    </tbody>
                </table>
            </div>
        </div>
    </div>
</div>
@endsection