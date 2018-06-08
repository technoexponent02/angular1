<title>@yield('pageTitle')</title>

<meta name="apple-mobile-web-app-capable" content="yes">
<meta name="apple-touch-fullscreen" content="yes">
<meta name="apple-mobile-web-app-status-bar-style" content="white">

<meta property="fb:app_id" content="1050589168353691"/>
<meta property="og:url" content="https://swolk.com" />
<meta property="og:type" content="website" />
<meta property="og:title" content="Topic based Social Media"/>
<meta property="og:image" content="https://swolk.com/assets/img/social-share-screen.jpg"/>
<meta name="description" content="Discover interesting stories from passionate people"/>
<meta property="og:description" content="Discover interesting stories from passionate people"/>
<meta name="apple-mobile-web-app-title" content="Swolk">

<meta name="twitter:card" content="summary_large_image">
<meta name="twitter:site" content="@swolk_com">
<meta name="twitter:title" content="Topic based Social Media">
<meta name="twitter:description" content="Discover interesting stories from passionate people">
<meta name="twitter:image" content="https://swolk.com/assets/img/social-share-screen.jpg">

<link rel="shortcut icon" type="image/x-icon" href="<?php echo asset('assets/img/favicon.ico'); ?>">

<link href="https://fonts.googleapis.com/css?family=Montserrat:400,700" rel="stylesheet">
<link href="https://fonts.googleapis.com/css?family=Open+Sans:300,400,600,700,800" rel="stylesheet">
<link href="/assets/css/style.css" rel="stylesheet" type="text/css">

<link rel="stylesheet" href="<?php echo elixir('css/swolk.css') ?>" />

<link rel="apple-touch-icon" href="assets/pages/ico/57.png" />
<link rel="apple-touch-icon" href="assets/pages/ico/72.png"  sizes="72x72" />
<link rel="apple-touch-icon" href="assets/pages/ico/114.png" sizes="114x114" />

<link rel="apple-touch-startup-image" href="assets/pages/splash/startup-iphone.png"  sizes="320x460"  media="(max-device-width: 480px) and not (-webkit-min-device-pixel-ratio: 2)" />
<link rel="apple-touch-startup-image" href="assets/pages/splash/startup-iphone4.png" sizes="640x920"  media="(max-device-width: 480px) and (-webkit-min-device-pixel-ratio: 2)" />
<link rel="apple-touch-startup-image" href="assets/pages/splash/lmsplash1004.png" sizes="768x1004" media="screen and (min-device-width: 481px) and (max-device-width: 1024px) and (orientation:portrait)" />
<link rel="apple-touch-startup-image" href="assets/pages/splash/lmsplash748.png" sizes="1024x748" media="screen and (min-device-width: 481px) and (max-device-width: 1024px) and (orientation:landscape)" />


<script type="text/javascript" async src="https://platform.twitter.com/widgets.js"></script>

<!--[if lte IE 9]>
<link href="{{ asset('assets/pages/css/ie9.css') }}" rel="stylesheet" type="text/css" />
<![endif]-->

<!-- Code to remove hash from url after fcebook callback -->
<script type="text/javascript">
    if (window.location.hash && window.location.hash == '#_=_') {
        if (window.history && history.pushState) {
            window.history.pushState("", document.title, window.location.pathname);
        } else {
            // Prevent scrolling by storing the page's current scroll offset
            var scroll = {
                top: document.body.scrollTop,
                left: document.body.scrollLeft
            };
            window.location.hash = '';
            // Restore the scroll offset, should be flicker free
            document.body.scrollTop = scroll.top;
            document.body.scrollLeft = scroll.left;
        }
    }
</script>
<!-- Code to remove hash from url after fcebook callback -->

<!-- Google Tag Manager -->
<script>(function(w,d,s,l,i){w[l]=w[l]||[];w[l].push({'gtm.start':
new Date().getTime(),event:'gtm.js'});var f=d.getElementsByTagName(s)[0],
j=d.createElement(s),dl=l!='dataLayer'?'&l='+l:'';j.async=true;j.src=
'https://www.googletagmanager.com/gtm.js?id='+i+dl;f.parentNode.insertBefore(j,f);
})(window,document,'script','dataLayer','GTM-PWZ6NJG');</script>
<!-- End Google Tag Manager -->
