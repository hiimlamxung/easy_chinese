@extends('backend.layouts.master')

@section('title', 'Users')

@section('after-script')
<script>
    $(function(){
        $('.change-pass').click(function(){
            var id = $(this).data('id');
            var username = $(this).data('username');
            var email = $(this).data('email');
            if (id) {
                $("#change-password .user").html(username)
                $("#change-password .user-id").val(id)
                $("#change-password .email").html(email)

                $("#change-password").modal('show');
            }
        });
    });
</script>
@endsection

@section('main')
<div class="box-info row">
    @include('backend.includes.search', ['action' => route('admin.user.list')])
    <div class="box-body">
        <div class="row">
            <div class="col-sm-12">
                <table class="table table-striped table-bordered table-hover tbl-vertical-middle">
                    <thead>
                        <tr>
                            <th>ID</th>
                            <th>Username</th>
                            <th>Email</th>
                            <th>Image</th>
                            <th>Premium forever</th>
                            <th>Premium Expired</th>
                            <th>Status</th>
                            <th>Action</th>
                            <th>Created</th>
                        </tr>
                    </thead>
                    <tbody>
                        @foreach ($users as $key => $user)
                        <tr>
                            <td>{{ $user->id }}</td>
                            <td>{{ $user->name }}</td>
                            <td>{{ $user->email }}</td>
                            <td>
                                <img src="{{ $user->image }}" alt="" style="width: 100px; height: 100px;">
                            </td>
                            <td class="text-center">
                                @if ($user->is_premium)
                                <span class="label label-success"><i class="fa fa-check"></i></span>
                                @endif
                            </td>
                            <td>
                                @if ($user->premium_expired)
                                {{ date('d-m-Y', $user->premium_expired) }}
                                @endif
                            </td>
                            <td>
                                <span class="label label-{{ ($user->status) ? 'success' : 'default' }}">{{ ($user->status) ? 'Active' : 'Delete' }}</span>
                            </td>
                            <td>
                                <div class="btn btn-sm btn-default change-pass" data-id="{{ $user->id }}" data-email="{{ $user->email }}" data-username="{{ $user->name }}">Đổi mật khẩu</div>
                            </td>
                            <td>{{ $user->created_at }}</td>
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
<div class="modal fade" id="change-password" tabindex="-1" role="dialog" aria-labelledby="exampleModalLabel" aria-hidden="true">
    <div class="modal-dialog" role="document">
        <form class="modal-content" method="POST" action="{{ route('admin.users.change.pass') }}">
            {{ csrf_field() }}
            <div class="modal-header">
                <span class="modal-title" id="exampleModalLabel">Đổi mật khẩu</span>
                <button type="button" class="close" data-dismiss="modal" aria-label="Close">
                    <span aria-hidden="true">&times;</span>
                </button>
            </div>
            <div class="modal-body">
                <input type="hidden" class="user-id" name="id"> 
                <div class="">
                    <label>Username: </label>
                    <span class="user"></span>
                </div>
                <div class="">
                    <label>Email: </label>
                    <span class="email"></span>
                </div>
                <input class="form-control" style="margin-top: 10px" name="password" placeholder="Mật khẩu mới">

            </div>
            <div class="modal-footer">
                <button type="button" class="btn btn-secondary" data-dismiss="modal">Close</button>
                <button type="submit" class="btn btn-primary">Save changes</button>
            </div>
        </form>
    </div>
</div>
@endsection