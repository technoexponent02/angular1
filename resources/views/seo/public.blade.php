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
        
        @yield('notSupported')

        @yield('titleMetaTag')

        <link rel="shortcut icon" type="image/x-icon" href="<?php echo asset('assets/img/favicon.ico'); ?>">

        <link href="https://fonts.googleapis.com/css?family=Montserrat:400,700" rel="stylesheet">
        <link href="https://fonts.googleapis.com/css?family=Open+Sans:300,400,600,700,800" rel="stylesheet">

        <link rel="stylesheet" href="<?php echo elixir('css/swolk.css') ?>" />

        @yield('customStyle')

    </head>
    <body class="fixed-header">
        <!-- Google analytics -->
        <script>
          (function(i,s,o,g,r,a,m){i['GoogleAnalyticsObject']=r;i[r]=i[r]||function(){
          (i[r].q=i[r].q||[]).push(arguments)},i[r].l=1*new Date();a=s.createElement(o),
          m=s.getElementsByTagName(o)[0];a.async=1;a.src=g;m.parentNode.insertBefore(a,m)
          })(window,document,'script','https://www.google-analytics.com/analytics.js','ga');

          ga('create', 'UA-90345571-1', 'auto');
          ga('send', 'pageview');

        </script>
        <!-- END Google analytics -->
        <!-- Google Tag Manager (noscript) -->
        <noscript><iframe src="https://www.googletagmanager.com/ns.html?id=GTM-PWZ6NJG"
        height="0" width="0" style="display:none;visibility:hidden"></iframe></noscript>
        <!-- End Google Tag Manager (noscript) -->
        @yield('content')

        @include('includes.footer')
      
        @yield('customScript')
    </body>
</html>