<div class="modal fade" id="required_login" tabindex="-1" role="dialog" aria-labelledby="exampleModalLabel" aria-hidden="true">
    <div class="modal-dialog modal-dialog-centered" role="document">
        <div class="modal-content">
            <div class="modal-body">
                <div class="note-login">
                    <span class="icon"><i class="fas fa-exclamation-triangle"></i></span>
                    <span>{{ trans('label.login_require') }}!</span>
                    <a href="{{ route('web.user.login') }}">{{ trans('user.login') }}</a>
                    <span>{{ trans('label.or') }}</span>
                    <a href="{{ route('web.user.register') }}">{{ trans('user.register') }}</a>
                    <button type="button" class="close" data-dismiss="modal" aria-label="Close">
                        <span aria-hidden="true">&times;</span>
                    </button>
                </div>
            </div>
        </div>
    </div>
</div>