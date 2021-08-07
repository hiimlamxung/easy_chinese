@extends('backend.layouts.master')

@section('title', 'Sale off')

@section('after-script')
    <script>
        $(function(){
            $(".change-active").change( function() {
                var status = $(this).val();
                var country = $(this).data('country');

                $.ajax({
                    type:'post',
                    url:'{{route("admin.saleoff.change")}}',
                    headers: {'X-CSRF-TOKEN': $('meta[name="csrf-token"]').attr('content')},
                    data: {
                        country,
                        status
                    },
                    success:function(data){
                        if(data.status != 200) {
                            alert(data.message);
                        } else {
                            location.reload();
                        }
                    }
                });
            });

            $('.change-all').focusout(function(e) {
                var val = $(this)[0].innerText;

                $.ajax({
                    type:'post',
                    url:'{{ route("admin.saleoff.changeAll") }}',
                    headers: {'X-CSRF-TOKEN': $('meta[name="csrf-token"]').attr('content')},
                    data: {
                        sale: val
                    },
                    success:function(data){
                        if(data.status != 200) {
                            alert(data.message);
                        } else {
                            location.reload();
                        }
                    }
                });
            });

        });
    </script>
@endsection
@section('after-css')
<style>
    .mt-15 {
        margin-top: 15px;
    }
</style>
@endsection

@section('main')
<div class="box-info">
    <div class="box-header with-border">
        <h3 class="box-title">Sale off.</h3>
    </div>
    <div class="row box-body">
        <form method="post" enctype="multipart/form-data" action="{{ route('admin.saleoff.edit')}}">
            <input type="hidden" name="_token" value="{{ csrf_token() }}">
            <div class="col-md-4">
                <table class="table table-striped table-bordered table-hover tbl-vertical-middle">
                    <thead>
                        <tr>
                            <th>ID</th>
                            <th>Quốc gia</th>
                            <th>Sale</th>
                            <th>Trạng thái</th>
                        </tr>
                    </thead>
                    <tbody>
                        <tr>
                            <td>0</td>
                            <td>
                                <a href="{{ route('admin.saleoff')}}">Tất cả</a>
                            </td>
                            <td contenteditable="true" class="change-all" data-name="sale"></td>
                            <td>
                                <input class="change-active" type="checkbox" data-country="all" value="{{ $checkAtive }}" {{ ($checkAtive) ? "checked" : ''}}>
                            </td>
                        </tr>
                        @foreach($countries as $key => $item)
                            <tr>
                                <td>{{ $item->id }}</td>
                                <td>
                                    <a href="{{ route('admin.saleoff', ['country' => $item->country]) }}">{{ $item->country }}</a>
                                </td>
                                <td>{{ $item->sale }}</td>
                                <td>
                                    <input class="change-active" type="checkbox" value="{{ $item->active }}" data-country="{{ $item->country }}" {{ ($item->active == 1) ? "checked" : ''}}>
                                </td>
                            </tr>
                        @endforeach
                    </tbody>
                </table>
                <hr>
                <div class="add-country">
                    <button class="btn btn-success btn-sm" style="margin-top: 20px;" type="button" data-toggle="modal" data-target="#add-country">Thêm quốc gia</button>
                </div>
            </div>
            <div class="col-md-8">
                @foreach($data as $item)
                <div style="background-color: #3c8dbc; color: #fff;font-size: 18px;padding-left: 10px; margin-top: 20px;">{{ $item->country }}</div>
                <input name="lang" value="{{ $item->country }}" type="hidden">
                <div class="box-country" style="padding-left: 10px;">
                    <div class="mt-15">
                        <label>Sale</label>
                        <input class="form-control" name="sale" value="{{ $item->sale }}" placeholder="Nhập số % giảm">
                    </div>
                    <div class="mt-15">
                        <label>Version</label>
                        <input class="form-control" name="version" value="{{ $item->version }}" placeholder="Phiên bản">
                    </div>
                    <div class="mt-15">
                        <label>Title IOS</label>
                        <input class="form-control" name="title_ios" value="{{ $item->title_ios }}">
                    </div>
                    <div class="mt-15">
                        <label>Title android</label>
                        <input class="form-control" name="title_android" value="{{ $item->title_android }}">
                    </div>
                    <div class="mt-15">
                        <label>Description IOS</label>
                        <input class="form-control" name="description_ios" value="{{ $item->description_ios }}">
                    </div>
                    <div class="mt-15">
                        <label>Description android</label>
                        <input class="form-control" name="description_android" value="{{ $item->description_android }}">
                    </div>
                    <div class="mt-15">
                        <label>Link IOS</label>
                        <input type="file" name="link_ios">
                        <img class="preview-image" style="margin-top: 20px; border: 1px solid #d9d9d9; border-radius: 5px; max-width: 500px;" src="{{ $item->link_ios ? url($item->link_ios) :''}}">
                    </div>
                    <div class="mt-15">
                        <label>Link android</label>
                        <input type="file" name="link_android">
                        <img class="preview-image" style="margin-top: 20px; border: 1px solid #d9d9d9; border-radius: 5px; max-width: 500px;" src="{{ $item->link_android ? url($item->link_android) : '' }}">
                    </div>
                    <div class="form-line row mt-15">
                        <div class="col-md-6">
                            <div class="mt-15">
                                <label class="name-date" style="min-width: 83px;" for="">Start IOS</label>
                                <input type="datetime-local" name="start_ios"  value="{{ date('Y-m-d\TH:i', strtotime($item->start_ios))}}" />
                            </div>
                            <div class="mt-15">
                                <label class="name-date" style="min-width: 83px;" for="">End IOS</label>
                                <input type="datetime-local" name="end_ios"  value="{{ date('Y-m-d\TH:i', strtotime($item->end_ios)) }}" />
                            </div>
                        </div>
                        <div class="col-md-6">
                            <div class="mt-15">
                                <label class="name-date" style="min-width: 83px;" for="">Start Android</label>
                                <input type="datetime-local" name="start_android" value="{{ date('Y-m-d\TH:i', strtotime($item->start_android)) }}" />
                            </div>
                            <div class="mt-15">
                                <label class="name-date" style="min-width: 83px;" for="">End Android</label>
                                <input type="datetime-local" name="end_android"  value="{{ date('Y-m-d\TH:i', strtotime($item->end_android)) }}" />
                            </div>
                        </div>
                    </div>
                </div>
                @endforeach
            </div>
            @if(sizeof($data) < 2)
            <div class="item form-group text-center">
                <button class="btn btn-success btn-sm" style="margin-top: 20px;" tupe="submit">Edit</button>
            </div>
            @endif
        </form>
    </div>
</div>
<div class="modal fade" id="add-country" tabindex="-1" role="dialog" aria-labelledby="exampleModalLabel" aria-hidden="true">
    <div class="modal-dialog" role="document">
        <div class="modal-content">
            <form method="post" enctype="multipart/form-data" action="{{ route('admin.saleoff.edit')}}">
                {{ csrf_field() }}
                <div class="modal-header">
                    <button type="button" class="close" data-dismiss="modal" aria-label="Close">
                    <span aria-hidden="true">&times;</span>
                    </button>
                </div>
                <div class="modal-body">
                    <div class="">
                        <label>Country</label>
                        <input class="form-control" name="country">
                    </div>
                    <div class="mt-15">
                        <label>Sale</label>
                        <input class="form-control" name="sale" placeholder="Nhập số % giảm">
                    </div>
                    <div class="mt-15">
                        <label>Version</label>
                        <input class="form-control" name="version" placeholder="Phiên bản">
                    </div>
                    <div class="mt-15">
                        <label>Title IOS</label>
                        <input class="form-control" name="title_ios">
                    </div>
                    <div class="mt-15">
                        <label>Title android</label>
                        <input class="form-control" name="title_android">
                    </div>
                    <div class="mt-15">
                        <label>Description IOS</label>
                        <input class="form-control" name="description_ios">
                    </div>
                    <div class="mt-15">
                        <label>Description android</label>
                        <input class="form-control" name="description_android">
                    </div>
                    <div class="mt-15">
                        <label>Link IOS</label>
                        <input type="file" name="link_ios">
                    </div>
                    <div class="mt-15">
                        <label>Link android</label>
                        <input type="file" name="link_android">
                    </div>
                    <div class="form-line row mt-15">
                        <div class="col-md-6">
                            <div class="mt-15">
                                <label class="name-date" style="min-width: 83px;" for="">Start IOS</label>
                                <input type="datetime-local" name="start_ios"/>
                            </div>
                            <div class="mt-15">
                                <label class="name-date" style="min-width: 83px;" for="">End IOS</label>
                                <input type="datetime-local" name="end_ios" />
                            </div>
                        </div>
                        <div class="col-md-6">
                            <div class="mt-15">
                                <label class="name-date" style="min-width: 83px;" for="">Start Android</label>
                                <input type="datetime-local" name="start_android"/>
                            </div>
                            <div class="mt-15">
                                <label class="name-date" style="min-width: 83px;" for="">End Android</label>
                                <input type="datetime-local" name="end_android"/>
                            </div>
                        </div>
                    </div>
                </div>
                <div class="modal-footer">
                    <button type="button" class="btn btn-secondary" data-dismiss="modal">Close</button>
                    <button type="submit" class="btn btn-primary">Add country</button>
                </div>
            </form>
        </div>
    </div>
</div>

@endsection