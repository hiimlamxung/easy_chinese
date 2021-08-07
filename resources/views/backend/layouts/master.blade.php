<!DOCTYPE html>
<html lang="{{ config('app.locale') }}">
    <head>
        <meta http-equiv="Content-Type" content="text/html; charset=UTF-8">
        <!-- Meta, title, CSS, favicons, etc. -->
        <meta charset="utf-8">
        <meta http-equiv="X-UA-Compatible" content="IE=edge">
        <meta name="viewport" content="width=device-width, initial-scale=1">
        <link rel="icon" href="{{url('favicon.ico')}}" type="image/ico" />
    
        <title>@yield('title', 'Admin')</title>
    
        <!-- Bootstrap -->
        <link href="{{ url('vendors/bootstrap/dist/css/bootstrap.min.css') }}" rel="stylesheet">
        <!-- Font Awesome -->
        <link href="{{ url('vendors/font-awesome/css/font-awesome.min.css') }}" rel="stylesheet">

        <!-- Custom Theme Style -->
        <link href="{{ url('build/css/custom.min.css') }}" rel="stylesheet">
        {!! HTML::style('backend/css/common.css') !!}

        <meta name="csrf-token" content="{{ csrf_token() }}" />
        {{-- after css --}}
        @yield('after-css')
    </head>

    <body class="nav-md">
        <div class="container body">
            <div class="main_container">
                {{-- nav --}}
                @include('backend.includes.nav')

                <!-- top header -->
                @include('backend.includes.header')

                <!-- page content -->
                <div class="right_col" role="main">
                    @yield('main')
                </div>

                <!-- footer content -->
                @include('backend.includes.footer')
            </div>
        </div>
        @include('includes.partials.params')

        <!-- jQuery -->
        <script src="{{url('vendors/jquery/dist/jquery.min.js')}}"></script>
        <!-- Bootstrap -->
        <script src="{{url('vendors/bootstrap/dist/js/bootstrap.min.js')}}"></script>
        <script src="{{url('vendors/bootstrap-notify/bootstrap-notify.min.js')}}"></script>
        <script src="//cdn.jsdelivr.net/npm/sweetalert2@10"></script>
        {{-- before script --}}
        @yield('before-script')
        <!-- Custom Theme Scripts -->
        <script src="{{url('build/js/custom.min.js')}}"></script>
        <script src="{{url('js/common.js')}}"></script>
        {{-- after script --}}
        <script>
            jQuery.ajaxSetup({
                headers: {
                    'X-CSRF-TOKEN': $('meta[name="csrf-token"]').attr('content')
                },
                beforeSend: function(){
                    ajaxindicatorstart('Loading...');
                },
                complete: function(){
                    ajaxindicatorstop();
                }
            })
            function ajaxindicatorstart(text){
                if(jQuery('body').find('#resultLoading').attr('id') != 'resultLoading'){
                    jQuery('body').append('<div id="resultLoading" style="display:none"><div><img src="{{ url("images/loading.gif") }}"><div>'+text+'</div></div><div class="bg"></div></div>');
                }
                jQuery('#resultLoading').css({
                    'width':'100%',
                    'height':'100%',
                    'position':'fixed',
                    'z-index':'10000000',
                    'top':'0',
                    'left':'0',
                    'right':'0',
                    'bottom':'0',
                    'margin':'auto'
                });
    
                jQuery('#resultLoading .bg').css({
                    'background':'#000000',
                    'opacity':'0.7',
                    'width':'100%',
                    'height':'100%',
                    'position':'absolute',
                    'top':'0'
                });
    
                jQuery('#resultLoading>div:first').css({
                    'width': '250px',
                    'height':'75px',
                    'text-align': 'center',
                    'position': 'fixed',
                    'top':'0',
                    'left':'0',
                    'right':'0',
                    'bottom':'0',
                    'margin':'auto',
                    'font-size':'16px',
                    'z-index':'10',
                    'color':'#ffffff'
    
                });
                jQuery('#resultLoading .bg').height('100%');
                jQuery('#resultLoading').fadeIn(300);
                jQuery('body').css('cursor', 'wait');
            }
            function ajaxindicatorstop(){
                jQuery('#resultLoading .bg').height('100%');
                jQuery('#resultLoading').fadeOut(300);
                jQuery('body').css('cursor', 'default');
            }
        </script>
        @yield('after-script')
    </body>
</html>