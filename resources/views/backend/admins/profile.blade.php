@extends('backend.layouts.master')
@section('main')
@include('backend.includes.previous', ['back_link' => URL::previous()])
<div class="row">
  <div class="col-xs-12">
    <div class="col-xs-4">
      <div class="x_panel">
        <div class="x_content">
          <div class="col-xs-12 profile_left">
            <div class="profile_img">
                <div id="crop-avatar">
                    <!-- Current avatar -->
                    <img class="img-responsive avatar-view" src="{{ (!is_null($admin->image)) ? $admin->image : url('images/default.png') }}" alt="Avatar" title="Change the avatar">
                </div>
            </div>
            <h3>{{ $admin->username }}</h3>
			<form action="{{ route('backend.profile.image', ['admin' => $admin->id]) }}" method="post" enctype="multipart/form-data">
                {{ csrf_field() }}
                <input name="_method" type="hidden" value="PUT">
                <div class="form-group">
                    <input type="file" name="image" class="form-control" required>
                </div>
                <div class="form-group">
                    <input type="hidden" name="id" class="form-control" value="{{ $admin->id }}">
                </div>
                <button type="submit" class="btn btn-success btn-user-update-image">Update Image</button>
            </form>
            <ul class="list-unstyled user_data">
                <li><i class="fa fa-envelope-o"></i> {{ $admin->email }}
                </li>
                <li><i class="fa fa-calendar"></i> {{ $admin->created_at->format("d-m-Y") . " ( " . $admin->created_at->diffForHumans() ." )" }}
                </li>
            </ul>
            @if ($admin->id !== $guard->id)
                <a class="btn btn-danger btn-user-block" data-id="{{ $admin->id }}"><i class="fa fa-lock"></i> Block</a>
            @endif
            <br />

          </div>
        </div>
      </div>
    </div>
    <div class="col-xs-8">
      <div class="x_panel">
        <div class="x_title">
          <h2>Account</h2>
          <div class="clearfix"></div>
        </div>
        <div class="x_content">

          <!-- start form for validation -->
          <form data-parsley-validate action="{{ route('backend.profile.update', ['admin' => $admin->id]) }}" method="POST">
            {{ csrf_field() }}
            <input name="_method" type="hidden" value="PUT">
            <label for="fullname">Name * :</label>
            <input type="text" id="username" class="form-control" name="username" value="{{ $admin->username }}" required />

            <label for="email">Email * :</label>
            <input type="email" id="email" class="form-control" name="email" value="{{ $admin->email }}" disabled />
            
            <label for="email">Password :</label>
            <input type="password" id="password" class="form-control" value="" name="password" />
            
            <br/>
            <button type="submit" class="btn btn-success btn-user-update">Update</button>
        </form>
        <!-- end form for validations -->

        </div>
      </div>
    </div>
  </div>
</div>
@endsection