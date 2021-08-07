@extends('backend.layouts.master')

@section('title', 'Admin-Users')

@section('after-script')
    <script>
        $(function(){
            // change status active or deactive
            $('.js-active-change').change(function(){
                var status = $(this).val();
                var current = $(this);
                var id = $(this).data('id');

                status = (status == 0) ? 1 : 0;

                changeInfor(id, 'active', status);
            })
            
            // change role
            $('select[name=role]').change(function(){
                var role = $(this).val();
                var current = $(this);
                var id = $(this).data('id');

                changeInfor(id, 'role', role);
            })
            
            // delete user
            $('.btn-delete-action').click(function(){
                var current = $(this);
                var id = $(this).data('id');

                $.ajax({
                    url: '',
                    type: 'post',
                    data: {
                        id: id
                    },
                    headers: {'X-CSRF-TOKEN': $('meta[name=csrf-token]').attr('content')},
                    success: function(res){
                        if(res == 1){
                            current.addClass('hidden');
                            swal("Success!", "", "success");
                        }
                    },
                    error: function(e) {
                        console.log(e);
                    }
                });
            });

            function changeInfor(id, type, value) {
                var data = {
                    id,
                    value,
                    type
                }

                $.ajax({
                    url: "{{ route('admin.infor.change') }}",
                    type: 'post',
                    data: data,
                    headers: {'X-CSRF-TOKEN': $('meta[name=csrf-token]').attr('content')},
                    success: function(res){
                        window.location.reload();
                    },
                    error: function(e) {
                        console.log(e);
                    }
                });
            }
        });

    </script>
@endsection

@section('main')
<div class="box-info">
    <div class="box-header with-border">
        <h3 class="box-title">Danh sách.</h3>
        <div class="box-tools">
            <form method="GET" action="">
                <div class="input-group" style="width: 200px;">
                    <input type="text" name="search" class="form-control input-sm pull-right" placeholder="Nhập tên">
                    <div class="input-group-btn">
                    <button type="submit" class="btn btn-sm btn-default"><i class="fa fa-search"></i></button>
                    </div>
                </div>
            </form>
        </div>
    </div>
    <div class="box-body">
        <div class="row">
            <div class="col-sm-12">
                <table class="table table-striped table-bordered table-hover tbl-vertical-middle">
                    <thead>
                        <tr>
                            <th>STT</th>
                            <th>Name</th>
                            <th>Email</th>
                            <th>Status</th>
                            <th>Active</th>
                            <th>Role</th>
                            <th>Created</th>
                            <th>Action</th>
                        </tr>
                    </thead>
                    <tbody>
                        @foreach($users as $key => $user)
                            <tr>
                                <td>{{ $key + 1 }}</td>
                                <td>{{ $user->username }}</td>
                                <td>{{ $user->email }}</td>
                                <td>{{ ($user->active == 0) ? 'Chưa kích hoạt' : (($user->active == 1) ? 'Đã kích hoạt' : 'Đã huỷ') }}</td>
                                <td>
                                    <input data-id="{{ $user->id }}" value="{{ $user->active }}" url="" type="checkbox" class="flat-red js-active-change" {{ ($user->active == 1) ? 'checked' : (($user->active == -1) ? 'disable' : '')}} />
                                </td>
                                <td>
                                    <div class="form-group">
                                        <select class="form-control" name="role" data-id="{{ $user->id }}">
                                            <option value="0" {{ ($user->role == 0) ? 'selected' : '' }}>User</option>
                                            <option value="1" {{ ($user->role == 1) ? 'selected' : '' }}>Admin</option>
                                            <option value="2" {{ ($user->role == 2) ? 'selected' : '' }}>CTV</option>
                                            <option value="3" {{ ($user->role == 3) ? 'selected' : '' }}>Kiểm soát viên</option>
                                        </select>
                                    </div>
                                </td>
                                <td>{{ $user->created_at }}</td>
                                <td>
                                    <a href="javascript:void(0)" class="btn btn-xs btn-danger btn-delete-action {{ ($user->active == -1) ? 'hidden' : ''}}" data-id="{{ $user->id }}">
                                        <i class="fa fa-trash" data-toggle="tooltip" data-placement="top" title="" data-original-title="Xóa tài khoản"></i>
                                    </a>
                                </td>
                            </tr>
                        @endforeach
                    </tbody>
                </table>
            </div>
        </div>
        <!--Phân trang-->
        @include('backend.includes.pagination', ['data' => $users, 'appended' => ['search' => Request::get('search')]])
        <div class="clearfix"></div>
    </div>
</div>
@endsection