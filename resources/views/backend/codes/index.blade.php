@extends('backend.layouts.master')
@section('page_title')
    Code active
@endsection
@section('after-script')
    <script>
        $(function(){
            $('.btn-code-active').click(function(){
                var code = $(this).data('code');
                var email = $('.email-user-'+code).val();
                var id = $('.id-user-'+code).val();

                if((email == '' || email == null) && (id == '' || id == null)){
                    $.notify({
                        message: 'Bạn phải nhập vào email hoặc id user'
                    },{
                        type: 'warning'
                    });
                    $('.email-user-'+code).focus();
                    return;
                }
                $.ajax({
                    type:'POST',
                    url:'{{ route("admin.premium.active") }}',
                    headers: {'X-CSRF-TOKEN': $('meta[name="csrf-token"]').attr('content')},
                    data: {
                        code: code,
                        email: email,
                        id: id
                    },
                    success:function(res){
                        var type = '';
                        if(res.status == 200){
                            type = 'success';
                            setTimeout(function(){
                                window.location.reload(1);
                            }, 3000);
                        }else{
                            type = 'danger';
                        }
                        $.notify({
                            message: res.message
                        },{
                            type: type
                        })
                    },
                    error: function(e){
                        $.notify({
                            message: 'Error'
                        },{
                            type: 'danger'
                        })
                    }
                });
            })
        })
    </script>
@endsection
@section('main')
    {{-- search title --}}
    <div class="row">
        <div class="x_content">
            <div class="col-md-12">
                <span class="col-md-2">Loại mã: </span>
                <div class="btn-group">
                    @foreach (config('code.months') as $key => $day)
                    <a href="{{ route('admin.code.list', ['month' => $key, 'status' => $status]) }}">
                        <button type="button" class="btn btn-sm btn-{{($month == $key) ? 'primary' : 'default'}}">
                            {{ ($key) ? $key . ' month' : 'Lifetime' }}
                        </button>
                    </a>
                    @endforeach
                </div>
            </div>
            <hr />
            <div class="col-md-12">
                <span class="col-md-2">Trạng thái: </span>
                <div class="btn-group">
                    <a href="{{ route('admin.code.list', ['month' => $month, 'status' => 0]) }}">
                        <button type="button" class="btn btn-sm btn-{{($status == 0) ? 'primary' : 'default'}}">
                            New
                        </button>
                    </a>
                    <a href="{{ route('admin.code.list', ['month' => $month, 'status' => 2]) }}">
                        <button type="button" class="btn btn-sm btn-{{($status == 2) ? 'primary' : 'default'}}">
                            Actived
                        </button>
                    </a>
                </div>
            </div>
        </div>
    </div>
    <div class="clear-fix"></div>
    <div class="row">
        <div class="col-md-12">
            <div class="x_content">
                <div class="table-responsive">
                    <table class="table table-striped jambo_table bulk_action">
                        <thead>
                            <tr>
                                <th>STT</th>
                                <th>Code</th>
                                <th>Status</th>
                                <th>Time</th>
                                <th>Active</th>
                                @if ($status == 0)
                                <th>ID</th>
                                @endif
                                <th>Action</th>
                                @if ($status == 2)
                                <th>Admin</th>
                                <th>Time active</th>
                                @endif
                            </tr>
                        </thead>
                        <tbody>
                            @foreach ($codes as $key => $code)
                            <tr>
                                <td>{{ ($codes->currentpage()-1)*$codes->perpage()+$key+1 }}</td>
                                <td>{{ strtoupper($code->code) }}</td>
                                <td><span class="label label-{{ (!$code->status) ? 'success' : 'default' }}">{{ config('code.status.'.$code->status) }}</span></td>
                                <td>
                                    @if ($code->day == 0)
                                    Lifetime
                                    @elseif($code->day == 90)
                                    3 tháng
                                    @else
                                    12 tháng
                                    @endif
                                </td>
                                <td>
                                    @if (!$status)
                                    <input type="text" class="form-control email-user-{{$code->code}}" placeholder="Email...">
                                    @else
                                    {{ isset($code['premium']['user']->email) ? $code['premium']['user']->email : '' }}
                                    @endif
                                </td>
                                @if ($status == 0)
                                <td>
                                    <input type="text" class="form-control id-user-{{$code->code}}" style="width: 80px;" placeholder="ID...">
                                </td>
                                @endif
                                <td>
                                    @if (!$status)
                                    <button type="button" class="btn btn-sm btn-primary btn-code-active" data-code="{{$code->code}}"><i class="fa fa-check"></i> Active</button>
                                    @endif
                                </td>
                                @if ($status == 2)
                                <td>
                                    {{ isset($code['premium']['admin']->username) ? $code['premium']['admin']->username : '' }}
                                </td>
                                <td>
                                    {{ $code->updated_at }}
                                </td>
                                @endif
                            </tr>
                            @endforeach
                        </tbody>
                    </table>
                </div>
                <!--Phân trang-->
                @include('backend.includes.pagination', ['data' => $codes, 'appended' => ['month' => $month, 'status' => $status,'search' => Request::get('search')]])
            </div>
        </div>
    </div>
@endsection