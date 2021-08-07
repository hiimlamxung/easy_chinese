@extends('frontend.layouts.master')

@section('title', Auth::user()->name )

@section('after-css')
    <link href="{{ asset('frontend/css/profile.css') }}" rel="stylesheet">
@endsection

@section('main-content')
<div class="box-message-user alert alert-success alert-dismissible">
    <button type="button" class="close" data-dismiss="alert" aria-label="Close">
        <span aria-hidden="true">&times;</span>
    </button>
    <span class="user-mess"></span>
</div>
<div class="profile-public">
    <div class="profile-top">
        <div class="avata">
            <div class="media">
                <div class="media-left">
                    <a class="thumnai-avata">
                        @if(Auth::user()->image)
                        <img class="avatar-pic" src="{{ Auth::user()->image }}" onerror="this.src='{{ url('/frontend/images/ic_avatar.png') }}'">
                        @else
                        <img class="avatar-pic" src="{{ url('/frontend/images/ic_avatar.png') }}">
                        @endif
                        <div class="update-avata hidden-xs hidden-sm">
                            <div> <i class="fa fa-camera"></i></div>
                            <p> {{ trans('user.change-avatar') }}</p>
                        </div>
                    </a>
                </div>
                <div class="media-body">
                    <div class="bottom-content">
                        <h4 class="media-heading">
                            <span class="is_name">{{ Auth::user()->name }}</span>
                        </h4>
                        <hr>
                        <div class="status">
                            <p class="text-status">{{ trans('user.level') }} : {{ Auth::user()->level }}</p>
                        </div>
                    </div>
                </div>         
            </div>
        </div>
    </div>
    <p class="btn btn-default btn-sm update-all" data-toggle="modal" data-target="#update-profile">
        <span class="fa fa-cog"></span> {{ trans('user.update-info') }}
    </p>
</div>
<div class="box-streak">
    <ul role="tablist">
        <li role="tab" aria-disabled="false" class="first current" aria-selected="true">
            <a id="wizard-t-0" href="#wizard-h-0" aria-controls="wizard-p-0"></a>
        </li>
        <li role="tab" aria-disabled="false">
            <a id="wizard-t-1" href="#wizard-h-1" aria-controls="wizard-p-1"></a>
        </li>
        <li role="tab" aria-disabled="false">
            <a id="wizard-t-1" href="#wizard-h-1" aria-controls="wizard-p-1"></a>
        </li>
        <li role="tab" aria-disabled="false">
            <a id="wizard-t-1" href="#wizard-h-1" aria-controls="wizard-p-1"></a>
        </li>
        <li role="tab" aria-disabled="false">
            <a id="wizard-t-1" href="#wizard-h-1" aria-controls="wizard-p-1"></a>
        </li>
        <li role="tab" aria-disabled="false">
            <a id="wizard-t-1" href="#wizard-h-1" aria-controls="wizard-p-1"></a>
        </li>
        <li role="tab" aria-disabled="false" class="last">
            <a id="wizard-t-2" href="#wizard-h-2" aria-controls="wizard-p-2"></a>
        </li>
    </ul>
</div>

<input type="file" name="image" class="change-avatar" hidden>
<div class="modal fade" id="modal" tabindex="-1" role="dialog" aria-labelledby="modalLabel" aria-hidden="true">
    <div class="modal-dialog modal-lg" role="document">
        <div class="modal-content">
            <div class="modal-header">
                <h5 class="modal-title" id="modalLabel">{{ trans('user.upload-avatar') }}</h5>
                <button type="button" class="close" data-dismiss="modal" aria-label="Close">
                    <span aria-hidden="true">×</span>
                </button>
            </div>
            <div class="modal-body">
                <div class="img-container">
                    <div class="row">
                        <div class="col-md-8">
                            <img id="image" src="https://avatars0.githubusercontent.com/u/3456749">
                        </div>
                        <div class="col-md-4">
                            <div class="preview"></div>
                        </div>
                    </div>
                </div>
            </div>
            <div class="modal-footer">
                <button type="button" class="btn btn-secondary" data-dismiss="modal">{{ trans('user.cancel') }}</button>
                <button type="button" class="btn btn-primary" id="crop">{{ trans('user.save') }}</button>
            </div>
        </div>
    </div>
</div>
@include('frontend.modal.profile')

@endsection
@section('after-script')
<script>
    $(".media-left").hover(function(){
		$(".update-avata").show();
		$(".update-avata").addClass("animated fadeInUp");

	},function(){
		$(".update-avata").hide();
		$(".update-avata").removeClass("animated fadeInUp");
	
    });

    $('.update-avata').click(function() {
        $('.change-avatar').click();
    })

    var $modal = $('#modal');
    var image = document.getElementById('image');
    var cropper;
    
    $("body").on("change", ".change-avatar", function(e){
        var files = e.target.files;
        var done = function (url) {
            image.src = url;
            $modal.modal('show');
        };
        var reader;
        var file;
        var url;

        if (files && files.length > 0) {
            file = files[0];

            if (URL) {
                done(URL.createObjectURL(file));
            } else if (FileReader) {
                reader = new FileReader();
                reader.onload = function (e) {
                    done(reader.result);
                };
                reader.readAsDataURL(file);
            }
        }
    });

    $modal.on('shown.bs.modal', function () {
        cropper = new Cropper(image, {
            aspectRatio: 1,
            viewMode: 3,
            preview: '.preview'
        });
    }).on('hidden.bs.modal', function () {
        cropper.destroy();
        cropper = null;
    });

    $("#crop").click(function(){
        canvas = cropper.getCroppedCanvas({
            width: 320,
            height: 320,
        });

        canvas.toBlob(function(blob) {
            url = URL.createObjectURL(blob);
            var reader = new FileReader();
            reader.readAsDataURL(blob); 
            reader.onloadend = function() {
                var base64data = reader.result;	

                $.ajax({
                    type: "POST",
                    dataType: "json",
                    headers: {'X-CSRF-TOKEN': $('meta[name=csrf-token]').attr('content')},
                    url: "image-cropper/upload",
                    data: {'_token': $('meta[name="_token"]').attr('content'), 'image': base64data},
                    success: function(data){
                        $modal.modal('hide');

                        $('.user-mess').html(data.message);
                        $('.box-message-user').fadeIn(200);
                        setTimeout(res => {
                            $('.box-message-user').fadeOut(200);
                        }, 5000);
                        $('.avatar-pic').attr('src', data.src);
                        $('.avatar').attr('src', data.src);
                    }
                });
            }
        });
    })
</script>
@endsection