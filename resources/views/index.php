
<!DOCTYPE html>
<html lang="en" data-ng-app="app" ng-controller="AppCtrl" >
<head>
    <meta http-equiv="content-type" content="text/html;charset=UTF-8"/>
    <meta charset="utf-8"/>
    <title ng-bind="meta.title | ucfirst" >SWOLK</title>
    <base href="/">
    <meta name="viewport" content="width=device-width, initial-scale=1, maximum-scale=1, user-scalable=no, shrink-to-fit=no"/>
    
    <meta name="apple-mobile-web-app-capable" content="yes">
    <meta name="apple-touch-fullscreen" content="yes">
    <meta name="apple-mobile-web-app-status-bar-style" content="white"> 
	<meta name="apple-mobile-web-app-title" content="Swolk">
    
    <meta property="og:url" content="https://swolk.com" />
	<meta property="og:type" content="website" />
	<meta property="og:title" content="Topic based Social Media"/>
	<meta property="og:image" content="https://swolk.com/assets/img/social-share-screen.jpg"/>
	<meta name="description" content="Discover interesting stories from passionate people"/>
	<meta property="og:description" content="Discover interesting stories from passionate people"/>

    <meta name="apple-mobile-web-app-capable" content="yes">
	
    <link rel="canonical" href="{{ meta.canonical_link }}" ng-if="meta.canonical_link" />
    <link rel="shortcut icon" type="image/x-icon" href="/assets/img/favicon.ico">
    <link id="lazyload_placeholder">
	
	<link rel="apple-touch-icon" href="assets/pages/ico/57.png" />
	<link rel="apple-touch-icon" href="assets/pages/ico/72.png"  sizes="72x72" />
	<link rel="apple-touch-icon" href="assets/pages/ico/114.png" sizes="114x114" />
	
	<link rel="apple-touch-startup-image" href="assets/pages/splash/startup-iphone.png"  sizes="320x460"  media="(max-device-width: 480px) and not (-webkit-min-device-pixel-ratio: 2)" />
	<link rel="apple-touch-startup-image" href="assets/pages/splash/startup-iphone4.png" sizes="640x920"  media="(max-device-width: 480px) and (-webkit-min-device-pixel-ratio: 2)" />
	<link rel="apple-touch-startup-image" href="assets/pages/splash/lmsplash1004.png" sizes="768x1004" media="screen and (min-device-width: 481px) and (max-device-width: 1024px) and (orientation:portrait)" />
	<link rel="apple-touch-startup-image" href="assets/pages/splash/lmsplash748.png" sizes="1024x748" media="screen and (min-device-width: 481px) and (max-device-width: 1024px) and (orientation:landscape)" />

    <link href="https://fonts.googleapis.com/css?family=Montserrat:400,700" rel="stylesheet">
    <link href="https://fonts.googleapis.com/css?family=Open+Sans:300,400,600,700,800" rel="stylesheet">

    <link rel="stylesheet" href="<?php echo elixir('css/swolk.css') ?>" />

    <style>
        .filterColm .filterColmArea.mobistat{
            display: none;
        }
        /* @media screen and (max-width: 450px) {
            html, body {
                height: 100%;
                overflow: hidden;
            }
            .page-container{
                position: absolute;
                top: 0;
                left: 0;
                right: 0;
                bottom: 0;
                overflow: scroll;
                -webkit-overflow-scrolling: touch;
                overflow-scrolling: touch;
                
            }
            .haspostpop .page-container .page-content-wrapper{
                z-index: 999;
            }
            .postModalOuter,
            .mobileModalHeaderMid{
                z-index: 999;
            }
            .modalCloseBtn{
                z-index: 9999;
            }
            .filterColm .filterColmArea.mobistat{
                display: block;
            }
            .filterColm .filterColmArea.dskstat{
                display: none;
            }
        } */
        /* html, body {
            height: 100%;
            overflow: hidden;
        }
        .page-container{
            position: absolute;
            top: 0;
            left: 0;
            right: 0;
            bottom: 0;
            overflow: scroll;
            -webkit-overflow-scrolling: touch;
            overflow-scrolling: touch;
        } */
        /* added by subhojit 061017 */
        .non-login-user .categoryHeadingRow .followBtn.exploreDesktop{
            display: none;
        }
        #hero-image.loginexplorehero .cover-photo{
            background: #fff;
        }
        #hero-image.loginexplorehero .hero-image-text{
            color: #000;
        }
        #hero-image.loginexplorehero .exploreSearch .srchFld{
            background-color: #e2e1e1;
        }
        #hero-image.loginexplorehero .cover-photo #hero-close-button{
            display: none;
        }
        @media (max-width: 400px){
            .non-login-user .nonlogin-jumbotron h1 {
                font-size: 22px !important;
            }
            .non-login-user .nonlogin-jumbotron h5{
                font-size: 17px !important;
            }
        }
        /* added by subhojit 061017 */
        /* added by subhojit 101117 */
        .nonlogin-jumbotron{
			display: none;
		}
		.non-login-user .jumbotron{
			display: none;
		}
		.non-login-user .nonlogin-jumbotron{
			display: block;
		}
        .profileCommentBoxTop.has-top-zindex{
            z-index: 99;
        }
    </style>

    <!--[if lte IE 10]>
    <script type="text/javascript">document.location.href = '/unsupported-browser'</script>
    <![endif]-->

    <script type="text/javascript">
        if (window.location.hash == '#_=_') {
            window.location.hash = ''; // for older browsers, leaves a # behind
            history.pushState('', document.title, window.location.pathname); // nice and clean
            window.location.reload();
            //e.preventDefault(); // no page reload
        }
    </script>

    <script type="text/javascript">
        window.onload = function () {
            // fix for windows 8
            if (navigator.appVersion.indexOf("Windows NT 6.2") != -1)
                document.head.innerHTML += '<link rel="stylesheet" type="text/css" ng-href="{{app.layout.chromeFix}}" />'
        }
    </script>
    <!-- Froala editor -->
    <script id="fr-fek">try{(function (k){localStorage.FEK=k;t=document.getElementById('fr-fek');t.parentNode.removeChild(t);})('LxD-17bbdF1rH-7A-31==')}catch(e){}</script>

<?php if (config('server.google.tag_manager')) { ?>
    <!-- Google Tag Manager -->
	<script>(function(w,d,s,l,i){w[l]=w[l]||[];w[l].push({'gtm.start':
	new Date().getTime(),event:'gtm.js'});var f=d.getElementsByTagName(s)[0],
	j=d.createElement(s),dl=l!='dataLayer'?'&l='+l:'';j.async=true;j.src=
	'https://www.googletagmanager.com/gtm.js?id='+i+dl;f.parentNode.insertBefore(j,f);
	})(window,document,'script','dataLayer','GTM-PWZ6NJG');</script>
	<!-- End Google Tag Manager -->
<?php } ?>

    <script type="text/javascript">
        var ROOT_URL = "<?php echo config('app.url'); ?>";
        var tsmPlayerPool = [];
    </script>
</head>

<!-- <body class="fixed-header" webscrolling2> -->
<?php 
if(Auth::check()){ ?>
    
    <body class="fixed-header" webscrolling2>
   
<?php } else { ?>
    <body class="fixed-header non-login-user" webscrolling2>
<?php } ?>

<?php if (config('server.google.analytics')) { ?>
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
<?php } ?>
<?php if (config('server.google.tag_manager')) { ?>
<!-- Google Tag Manager (noscript) -->
<noscript><iframe src="https://www.googletagmanager.com/ns.html?id=GTM-PWZ6NJG"
height="0" width="0" style="display:none;visibility:hidden"></iframe></noscript>
<!-- End Google Tag Manager (noscript) -->
<?php } ?>
<style>
	.loader {
	  border: 5px solid #f3f3f3;
	  border-radius: 50%;
	  border-top: 5px solid #000;
	  width: 30px;
	  height: 30px;
	  -webkit-animation: spin 0.5s linear infinite;
	  animation: spin 0.5s linear infinite;
	}
	@-webkit-keyframes spin {
	  0% { -webkit-transform: rotate(0deg); }
	  100% { -webkit-transform: rotate(360deg); }
	}
	@keyframes spin {
	  0% { transform: rotate(0deg); }
	  100% { transform: rotate(360deg); }
	}
	.loadermiddle{
		position:absolute; top:50%; left:50%; margin:-10px 0 0 -10px; display: block;
	}
</style>
<div id="loading" style="position:fixed; top:0; left:0; bottom:0; right:0; background:#fff; display: block "><div class="loadermiddle"><div class="loader"></div></div></div>

<div id="sidebarOuter" ng-click="navsmClose()"></div>
<!-- BEGIN SIDEBAR -->
<?php 
if(Auth::check()){ ?>
<div ng-include=" 'tpl.sidebar' " include-replace></div>
<?php } else { ?>
<div ng-include=" 'tpl.non-login-sidebar' " include-replace></div>
<?php } ?>
<!-- END SIDEBAR -->
<!-- START PAGE-CONTAINER -->
<div class="page-container">
    <!-- START PAGE HEADER WRAPPER -->
    <?php 
    if(Auth::check()){  ?>
    <div ng-include=" 'tpl.header' " >
    <?php } else {  ?> 
    <div ng-include=" 'tpl.non-login-header' ">
    <?php } ?>
    </div>
    <!-- END PAGE HEADER WRAPPER -->
    <!-- START PAGE CONTENT WRAPPER -->
    <div class="page-content-wrapper">
		<div class="headerLine"></div>
        <!-- START PAGE CONTENT -->
        <div class="content">
            <div class="full-height full-width" ui-view>
            <!-- <ui-view></ui-view> -->
        </div>
        <!-- END PAGE CONTENT -->
        <div

            ng-include=" 'tpl.footer'">
        </div>
    </div>
    <!-- END PAGE CONTENT WRAPPER -->
</div>
<!-- END PAGE CONTAINER -->
<!--START QUICKVIEW -->
<div ng-include=" 'tpl.quick-view' " include-replace>
</div>
<!-- END QUICKVIEW-->

<!-- START OVERLAY -->
<div ng-include=" 'tpl.quick-search' " include-replace>
</div>
<!-- END OVERLAY -->
<!-- START MODALS -->

<!-- Modal -->
<div class="modal fade" id="verifyMailModal" tabindex="-1" role="dialog" aria-labelledby="memberModalLabel" aria-hidden="true">
    <div class="modal-dialog">
        <div class="modal-content">
            <div class="modal-header">
                <button type="button" class="close" data-dismiss="modal" aria-label="Close"><span aria-hidden="true">&times;</span>
                </button>
                 <h4 class="modal-title" id="memberModalLabel">Thank you for visiting <strong>swolk.com</strong></h4>

            </div>
            <div class="modal-body">
                <h6 style="text-align:center;">Please verify your email before login</h6>
            </div>
            <div class="modal-footer">
                <button type="button" class="btn btn-primary" data-dismiss="modal">Close</button>
            </div>
        </div>
    </div>
</div>


<!-- JS -->
<script src="<?php echo elixir('js/swolk.js') ?>"></script>
<script src="https://cdnjs.cloudflare.com/ajax/libs/typeit/4.4.1/typeit.min.js"></script>

<!-- Unique browser tabID -->
<script>
    var _uuid4 = uuid4();
    var browserTabID = sessionStorage.browserTabID && sessionStorage.closedLastTab !== '2' ? 
                sessionStorage.browserTabID : 
                sessionStorage.browserTabID = _uuid4;
    sessionStorage.closedLastTab = '2';
        
    $(window).on('unload beforeunload', function() {
        sessionStorage.closedLastTab = '1';
    });
</script>
<!-- <script src="https://player.vimeo.com/api/player.js"></script> -->

<!-- For Google Maps -->
<script type="text/javascript"
        src="https://maps.googleapis.com/maps/api/js?key=<?php echo config('constants.GMAPS_BROWSER_KEY');?>&libraries=places"></script>

<!-- API daily motion CDN  -->
<script src='https://api.dmcdn.net/all.js'></script>

<script type="text/javascript">
    emojione.imageType = 'png';
    emojione.ascii = true;
    emojione.unicodeAlt = true;
    emojione.imagePathPNG = 'assets/pages/img/emoji/';
</script>
<script type="text/javascript">
    $(window).load(function () {
        var didScroll;
		var lastScrollTop = 0;
		var delta = 5;
		var navbarHeight = $('.header').outerHeight();

		$(window).scroll(function(event){
			didScroll = true;
		});
		setInterval(function() {
			if (didScroll) {
				hasScrolled();
				didScroll = false;
			}
		}, 250);

		function hasScrolled() {
			var st = $(this).scrollTop();
			if(Math.abs(lastScrollTop - st) <= delta)
				return;
			if (st > lastScrollTop && st > navbarHeight){
				$('.header').removeClass('nav-down').addClass('nav-up');
				$('.scrollTab').addClass('nav-up-now');
				$('.detailsTopbar .mobileModalHeader').addClass('fixeD');
			} else {
				if(st + $(window).height() < $(document).height()) {
    				$('.header').removeClass('nav-up').addClass('nav-down');
    				$('.scrollTab').removeClass('nav-up-now');
					$('.detailsTopbar .mobileModalHeader').removeClass('fixeD');
				}
			}
			lastScrollTop = st;
		}
    });
</script>
<script type="text/javascript">
    $(window).load(function () {
        setTimeout(function(){
            $('#loading').fadeOut("slow");
        }, 2000);
		// facebook API ::
		setTimeout(function(){
			window.fbAsyncInit = function() {
                    FB.init({appId: '1050589168353691', status: true, cookie: true,
                    xfbml: true});
                }; 
                (function() {
                    var e = document.createElement('script'); e.async = true;
                    e.src = document.location.protocol +
                    '//connect.facebook.net/en_US/all.js';
                    document.getElementById('fb-root').appendChild(e);
                }()); 
		}, 2000);		
    });
    // Fastclick
    $(function() {
        var needsClick = FastClick.prototype.needsClick;
        FastClick.prototype.needsClick = function(target) { 
            if ( (target.className || '').indexOf('pac-item') > -1 ) {
              return true;
            } else if ( (target.parentNode.className || '').indexOf('pac-item') > -1) {
              return true;
            } else {
              return needsClick.apply(this, arguments);
            }
        };
        FastClick.attach(document.body);        
    });
    // post card bottom "..." on click
    // post parent make z-index top
    function reportTop(el){
        //$('.profileCommentBoxTop').removeClass('has-top-zindex');
       // $('#'+el).parents('.profileCommentBoxTop').addClass('has-top-zindex');
    }
    //post details close
    function postDetailsClose() {
        $('.subOverlay').hide();
        $('.otherSubsh').hide();
    }
    // nearby location reload
    // function locationReload() { 
    //     setTimeout(function() {
    //         // alert(window.location.href)
    //         window.location.href = '/nearby'
    //     }, 500);
    //  }
    // LOGIN POP
    function forgotcome(){
        $('.loginDropWrap').fadeOut(100);
        $('.forgotPassWrap').fadeIn(100);
    }
    function signInCome(){
        $('.forgotPassWrap').fadeOut(100);
        $('.loginDropWrap').fadeIn(100);
    }
    function signUpScroll(){
        $('#signInNotifi').trigger('click');
        window.scrollTo(0, $('.nonlogin-jumbotron').offset().top, 'smooth');
    }
	//SCROLL REMOVE 
	$('body').on('hidden.bs.modal', function () {
		$("html").removeClass("scrollHidden");     
	})

    Pace.on('done', function() {
        $("body").addClass('paceDisable');
    });

</script>
<script type="text/javascript">
    $.ajaxPrefilter(function (options, originalOptions, jqXHR) {
        options.async = true;
    });	
</script>

<div id="fb-root"></div>
</body>

</html>
