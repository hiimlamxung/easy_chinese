@extends('backend.layouts.master')

@section('title', 'Manager')

@section('after-css')
<style>
    .margin-bottom {
        margin-bottom: 20px;
    }
</style>
@endsection

@section('after-script')
    <script>
        $(function(){
            $('.btn-search').click(function(){
                var link = $('.input-search').val();
                if(link == '' || link == null) {
                    swal.fire("bạn chưa nhập link");
                    return;
                }
                $.ajax({
                    type:'post',
                    url:'{{ route("admin.news.search") }}',
                    headers: {'X-CSRF-TOKEN': $('meta[name="csrf-token"]').attr('content')},
                    data: {
                        link: link
                    },
                    success:function(res){
                        swal.fire(res);
                    }
                });
            })

            $('.detail-des').click(function(){
                var des = $(this).data('des');
                $('.modal-title').html('Mô tả');
                $('.modal-body').html(des);
                $('#modal-detail').modal('show');
              
            })
            
            $('.detail-cont').click(function(){
                var content = $(this).data('content');
                $('.modal-title').html('Nội dung');
                $('.modal-body').html(content);
                $('#modal-detail').modal('show');
            });

            // action post or delete news
            $('.btn-action-news').click(function(){
                var id = $(this).data('id');
                var status = $(this).data('type');
                var element = $(this).closest("tr");
                $.ajax({
                    url: '{{ route("admin.news.change_status") }}',
                    type: 'post',
                    data: {
                        id: id,
                        status: status
                    },
                    headers: {'X-CSRF-TOKEN': $('meta[name=csrf-token]').attr('content')},
                    success: function(res) {
                        swal.fire('Thành công.');
                        element.addClass('hidden');
                    },
                    error: function(e) {
                        console.log(e);
                    }
                });
            })
            
            $('.input-edit-date').on('change', function(){
                var id = $(this).data('id');
                var pubDate = $(this).val();
                
                $.ajax({
                    url: '{{ route("admin.ajax.pubDate") }}',
                    type: 'post',
                    data: {
                        pub_date: pubDate,
                        id: id
                    },
                    headers: {'X-CSRF-TOKEN': $('meta[name=csrf-token]').attr('content')},
                    success: function(res){
                        // window.location.reload();
                    },
                    error: function(e) {
                        console.log(e);
                    }
                });
            })
        })
    
    </script>
@endsection

@section('main')
<div class="row">
    <div class="page-title">
        <div class="title_left">
            <h3>Quản lý bài báo</h3>
        </div>
        <div class="title_right">
            <div class="col-md-5 col-sm-5 col-xs-12 form-group pull-right top_search">
                <div class="input-group">
                    <input type="text" name="search" class="form-control input-search" placeholder="Nhập link bài báo">
                    <span class="input-group-btn">
                        <button class="btn btn-default btn-search" type="button">Go!</button>
                    </span>
                </div>
            </div>
        </div>
    </div>
    <div class="box-body">
        <div class="margin-bottom">
            <form class="form-inline">
                <input type="hidden" name="_token" value="{{ csrf_token() }}">
                <div class="form-group">
                    <label class="sr-only">
                        Trạng thái
                    </label>
                    <select class="form-control fillter_width_url">
                        <option {{ ($module == "new") ? "selected":"" }} 
                        value="new">Mới thêm</option>
                        <option {{ ($module == "posted") ? "selected":"" }} 
                        value="posted">Phát hành</option>
                        <option {{ ($module == "success") ? "selected":"" }} 
                        value="success">Đã phát hành</option>
                        <option {{ ($module == "all") ? "selected":"" }} 
                        value="all">Tất cả</option>
                        @if(in_array(Auth::guard('admin')->user()->role, [1,2]))
                            <option {{ ($module == "deleted") ? "selected":"" }} 
                            value="deleted">Tạm ngừng</option>
                        @endif
                    </select>
                </div>
            </form>
        </div>
        <div class="row">
            <div class="col-sm-12">
                <table class="table table-striped table-bordered table-hover tbl-vertical-middle">
                    <thead>
                        <tr>
                            <th>STT</th>
                            <th>Người viết</th>
                            <th>Tiêu đề</th>
                            <th>Mô tả</th>
                            <th>Ảnh</th>
                            <th>Nội dung</th>
                            <th>Lĩnh vực</th>
                            @if ($module == 'all')
                            <th>Trạng thái</th>
                            @else
                            <th>Ngày đăng</th>
                            @if($module == 'new')
                            <th style="width: 131px;">Thao tác</th>
                            @endif
                            @endif
                        </tr>
                    </thead>
                    <tbody>
                        @foreach($news as $key => $item)
                            <tr>
                                <td>{{ $item->id }}</td>
                                <td>{{ $item['admins']->username }}</td>
                                <td class="text-hanzi">{!! $item->title !!}</td>
                                <td class="text-hanzi">{!! $item->des_short !!} ... <a href="javascript:void(0)" class="detail-des" data-des="{{ $item->description }}">Chi tiết</a></td>
                                <td>
                                    <img style="width: 116px;height: auto;border-radius: 10px;" src="{{$item->image}}">
                                </td>
                                <td><a href="javascript:void(0)" class="detail-cont" data-content="{{ $item->content }}">Chi tiết</a></td>
                                <td>{{ $item->kind }}</td>
                                @if ($module == 'all')
                                <td>
                                    @if ($item->status == 0)
                                    <button class="btn btn-sm btn-block btn-default">
                                        Mới thêm
                                    </button>
                                    @endif
                                    @if ($item->status == 1)
                                    <button class="btn btn-sm btn-block btn-info">
                                        Chờ phát hành
                                    </button>
                                    @endif
                                    @if ($item->status == 2)
                                    <button class="btn btn-sm btn-block btn-success">
                                        Đã phát hành
                                    </button>
                                    @endif
                                </td>
                                @else
                                <td style="width: 112px;">
                                    <input type="date" name="pubDate" data-id="{{ $item->id }}" class="form-control pull-right datepicker input-edit-date" placeholder="Ngày đăng" value="{{ $item->pub_date }}">
                                    @if ($module == 'posted')
                                    <button type="button" class="btn btn-danger btn-sm btn-action-news" data-id="{{ $item->id }}" data-type="-1" title="Ngừng phát hành" data-toggle="tooltip" style="margin-top: 5px;" ><i class="fa fa-ban"></i></button>
                                    @endif
                                </td>   
                                @if($module == 'new')
                                <td>
                                    @if($item->status == 0)
                                        <div class="btn-group">

                                            @if(in_array(Auth::guard('admin')->user()->role, [1,3]))
                                            <button type="button" class="btn btn-info btn-sm btn-post-news btn-action-news" data-id="{{ $item->id }}" data-type="1" data-toggle="tooltip" title="Đăng bài"><i class="fa fa-upload"></i></button>
                                            @endif
                                            @if( in_array(Auth::guard('admin')->user()->role, [1,3]) || ( (Auth::guard('admin')->user()->role == 2) && ($item['admins']->id == Auth::guard('admin')->user()->id)) )
                                            <a href="{{ route('admin.news.edit', ['id' => $item->id ]) }}" type="button" class="btn btn-primary btn-sm" data-toggle="tooltip" title="Sửa bài"><i class="fa fa-pencil-square-o"></i></a>
                                            <button type="button" class="btn btn-danger btn-sm btn-del-news btn-action-news" data-id="{{ $item->id }}" data-type="-1" data-toggle="tooltip" title="Xóa bài"><i class="fa fa-trash-o"></i></button>
                                            @endif
                                        </div>
                                    @elseif($item->status == -1)
                                    <div class="btn-group">
                                        <button type="button" class="btn btn-info btn-sm btn-undel-news btn-action-news" data-id="{{ $item->id }}" data-type="0" data-toggle="tooltip" title="Khôi phục"><i class="fa fa-refresh"></i></button>
                                    </div>
                                    @endif
                                </td>
                                @endif
                                @endif
                            </tr>
                        @endforeach
                    </tbody>
                </table>
            </div>
        </div>
        <!--Phân trang-->
        @include('backend.includes.pagination', ['data' => $news, 'appended' => ['search' => Request::get('search')]])
        <div class="clearfix"></div>
    </div>
</div>
{{-- modal --}}
<div class="modal fade" tabindex="-1" role="dialog" id="modal-detail">
    <div class="modal-dialog custom-modal-detail-news modal-lg" role="document">
        <div class="modal-content">
            <div class="modal-header">
                <button type="button" class="close" data-dismiss="modal" aria-label="Close"><span aria-hidden="true">&times;</span></button>
                <h4 class="modal-title"></h4>
            </div>
            <div class="modal-body text-news"></div>
        </div>
    </div>
</div>
@endsection