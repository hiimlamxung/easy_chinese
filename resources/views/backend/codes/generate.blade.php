@extends('backend.layouts.master')
@section('page_title')
    Tạo mã code
@endsection
@section('after-script')
    <script>
        $(function(){
            //Update report
            $('.btn-create').click(function(){
                var $this = $(this);
                $this.button('loading');
                $.ajax({
                    type:'get',
                    url:'{{ route("admin.code.create") }}',
                    headers: {'X-CSRF-TOKEN': $('meta[name="csrf-token"]').attr('content')},
                    success:function(data){
                        $.notify({
                            message: 'Success'
                        }, {
                            type: 'success'
                        })
                        $this.button('reset');
                    },
                    error: function(data){
                        $.notify({
                            message: 'Error'
                        }, {
                            type: 'danger'
                        })
                        $this.button('reset');
                    }
                });
            })
        })
    </script>
@endsection
@section('main')
    <div class="clear-fix"></div>
    <div class="row">
        <div class="col-md-12">
            <div class="x_content">
                <button type="button" class="btn btn-primary btn-create" id="load" data-loading-text="<i class='fa fa-spinner fa-spin'></i> Processing">Generate code</button>
            </div>
        </div>
    </div>
@endsection