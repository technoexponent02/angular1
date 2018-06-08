<!DOCTYPE html>
<html>
    <head>
        <meta http-equiv="content-type" content="text/html;charset=UTF-8" />
        <meta charset="utf-8" />
        <meta name="viewport" content="width=device-width, initial-scale=1.0, maximum-scale=1.0, user-scalable=no, shrink-to-fit=no" />
        <link rel="apple-touch-icon" href="pages/ico/60.png">
        <link rel="apple-touch-icon" sizes="76x76" href="{{asset('pages/ico/76.png')}}">
        <link rel="apple-touch-icon" sizes="120x120" href="{{asset('pages/ico/120.png')}}">
        <link rel="apple-touch-icon" sizes="152x152" href="{{asset('pages/ico/152.png')}}">
        <link rel="icon" type="image/x-icon" href="favicon.ico" />
        
        
       @include('includes.header.public')

       @yield('customStyle')

        <script type="text/javascript">
            var NAPBY = NAPBY || {};
            NAPBY.base_url = '{{ url('/') }}';
        </script>

    </head>
    <body class="fixed-header dashboard">
        @include('includes.sidebar')
        <div class="page-container">
            @include('includes.header.user')
            
            <div class="page-content-wrapper">
                @yield('content')

                @include('includes.modal')

                
            </div>
        </div>
    </div>

    @include('includes.footer')
    @yield('customScript')
    @if(Session::has('flash_notification.message'))
        <!--<script type="text/javascript">
            $('body').pgNotification({
                style: 'bar',
                message: '{{ Session::get('flash_notification.message') }}',
                position: 'top',
                timeout: 0,
                type: 'success'
            }).show();
        </script>-->
    @endif
</body>
</html>