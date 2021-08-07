<div class="modal fade" id="update-profile" tabindex="-1" role="dialog" aria-labelledby="modalLabel" aria-hidden="true">
    <div class="modal-dialog" role="document">
        <div class="modal-content">
            <form method="POST" action="{{ route('web.user.update_profile') }}">
                {{ csrf_field() }}
                <div class="modal-header">
                    <h5 class="modal-title" id="modalLabel">{{ trans('user.change-info') }}</h5>
                    <button type="button" class="close" data-dismiss="modal" aria-label="Close">
                        <span aria-hidden="true">×</span>
                    </button>
                </div>
                <div class="modal-body">
                    <div class="form-group">
                        <label for="username">{{ trans('user.username') }}</label>
                        <input type="text" class="form-control" id="username" name="username" placeholder="{{ trans('user.username') }}" value="{{ Auth::user()->name }}">
                    </div>
                    <div class="form-group">
                        <label for="exampleFormControlSelect1">{{ trans('user.jlpt-level') }}</label>
                        <select class="form-control" name="level">
                            <option value="N1" {{ (Auth::user()->level == 'N1') ? "selected" : "" }}>N1</option>
                            <option value="N2" {{ (Auth::user()->level == 'N2') ? "selected" : "" }}>N2</option>
                            <option value="N3" {{ (Auth::user()->level == 'N3') ? "selected" : "" }}>N3</option>
                            <option value="N4" {{ (Auth::user()->level == 'N4') ? "selected" : "" }}>N4</option>
                            <option value="N5" {{ (Auth::user()->level == 'N5') ? "selected" : "" }}>N5</option>
                        </select>
                    </div>
                </div>
                <div class="modal-footer">
                    <button type="button" class="btn btn-secondary" data-dismiss="modal">{{ trans('user.cancel') }}</button>
                    <button type="submit" class="btn btn-primary">{{ trans('user.save') }}</button>
                </div>
            </form>
        </div>
    </div>
</div>