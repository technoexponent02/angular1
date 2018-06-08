@extends('layouts.public')

@section('pageTitle', 'Swolk | Interest based Social Media Platform')

@section('notSupported')
    <!--[if lte IE 10]>
    <script type="text/javascript">document.location.href = '/unsupported-browser'</script>
    <![endif]-->
@endsection

@section('customStyle')
	<link href="/assets/login-page/css/icons.min.css" rel="stylesheet" type="text/css" media="all"/>
	<!--<link href="/assets/login-page/css/flexslider.min.css" rel="stylesheet" type="text/css" media="all"/> -->
	<link href="/assets/login-page/css/theme.css" rel="stylesheet" type="text/css" media="all"/>
	<link href="/assets/login-page/css/custom.css" rel="stylesheet" type="text/css" media="all"/>
	<link href='https://fonts.googleapis.com/css?family=Lato:300,400,700,300italic,400italic' rel='stylesheet' type='text/css'>
	<script>
		(function(i,s,o,g,r,a,m){i['GoogleAnalyticsObject']=r;i[r]=i[r]||function(){
		 (i[r].q=i[r].q||[]).push(arguments)},i[r].l=1*new Date();a=s.createElement(o),
		 m=s.getElementsByTagName(o)[0];a.async=1;a.src=g;m.parentNode.insertBefore(a,m)
		 })(window,document,'script','https://www.google-analytics.com/analytics.js','ga');

		 ga('create', 'UA-90345571-2', 'auto');
		 ga('send', 'pageview');

	</script>
@endsection

@section('content')
<div class="loginHeader">
	<a href="{{url('/')}}" style="cursor:pointer;" class="txtLogo">
		<img src="assets/img/logo_2x_txt.png" alt="Logo"/>
	</a>
	<a href="{{url('/')}}" style="cursor:pointer;" class="iconLogo">
		<img src="assets/img/logo.png" alt="Logo"/>
	</a>
	<a href="javascript:void(0);" class="headLogReg ">Sign In</a>
	<!-- btn btn-primary btn-sm-->
	<div class="spacer"></div>
</div>
<div class="login-wrapper item_fade_in"> 
    <div class="bg-pic">
		<div class="login-container bg-white">
			<div class="p-l-50 m-l-20 p-r-50 m-r-20 p-t-50 sm-p-l-15 sm-p-r-15 sm-p-t-40">
				<!-- "onlyLogin"- parent class
				 <a href="{{url('/beta/landing')}}" class="whySwolkbtn">WHY SWOLK?</a>-->
				<div class="spacer"></div>				
				<div class="login-containerM">
				<!-- <div class="p-t-15"><h2>Login</h2></div>-->
					<div class="p-t-15"></div>
					
					@include('includes/flash')
				
					<form class="p-t-15" role="form" method="POST" action="{{ url('login') }}" id="form-signin">
						<input type="hidden" name="_token" value="{{ csrf_token() }}">
						<div class=" validate">
							<div class="form-group form-group-default {{ $errors->has('email') ? ' has-error' : '' }}">
								<label>USERNAME / E-MAIL</label>
								<div class="controls">
									<input type="text" name="email" placeholder="Username / E-mail" class="form-control" value="{{old('email')}}" data-rule-required="true" data-rule-email="true">
								</div>
							</div>
						</div>
						<div class="">
							<div class="form-group form-group-default {{ $errors->has('password') ? ' has-error' : '' }} ">
								<label>Password</label>
								<div class="controls">
									<input type="password" class="form-control" name="password" placeholder="Password" data-rule-required="true" >
								</div>
							</div>
						</div>
						<?php /*
						<div class="row">
							<div class="col-md-6">
								<div class="checkbox ">
									<input type="checkbox" name="remember" id="remember">
									<label for="remember">Remember me</label>
								</div>
							</div>
						</div> */?>

						<div class="row">
							<div class="col-md-12">
								<div class="checkbox nwChk">
									<p><a href="{{url('password/forgot')}}">Forgot Password?</a></p>
									<p class="newToSwolk">New to Swolk ? <a href="javascript:void(0);" class="signupScroll">Sign Up here</a></p>
									<div class="spacer"></div>
								</div>
							</div>
						</div>

						<div class="row">
							<div class="col-md-12">
								<button class="btn btn-primary btnLoading btn-cons m-t-10  btn-loading" type="submit" name="btnSubmit" value="submit" style="margin-bottom:10px !important;">Sign in</button>
							</div>
						</div>

						<div class="row m-t-10">
							<div class="col-md-12">
								<a class="btn btn-block btn-social btn-facebook" href="{{url('auth/facebook/'.base64_encode('only_login'))}}">
									<span class="pull-left"><i class="fa fa-facebook"></i></span>
									<span class="bold">Log in with Facebook</span>
								</a>
							</div>
							<div class="col-md-12">
								<a class="btn btn-block  btn-social btn-twitter" href="{{url('auth/twitter/'.base64_encode('only_login'))}}">
									<span class="pull-left"><i class="fa fa-twitter"></i></span>
									<span class="bold">Log in with Twitter</span>
								</a>
							</div>
						</div>
					</form>
					<div class="scrollDownMore">
						<a href="javascript:void(0);" class="scrollTxt">
							<!-- <span class="txt">Scroll down for more information</span>-->
							<span class="icon">
								<img src="assets/login-page/img/bottom-arrow2.png" alt=""/>
							</span>
						</a>
					</div>
					<br/>
				</div>
			</div>
		</div>
		<div class="loginLeftImage">
			<div class="loginBgtxt">
				<div class="loginLogo">
					<img src="assets/img/logo.png" alt=""/>
				</div>
			   <div class="bg-caption pull-bottom sm-pull-bottom text-white">
					<h2 class="semi-bold text-white">Swolk is a <strong>topic</strong> &amp; <strong>location</strong> based network that connects you and people around the world, who share the similar interest as you.</h2>
					<p class="small">&copy; Copyright {{date('Y')}} Swolk.com - All Rights Reserved</p>
				</div>
				<div class="scrollDownMore">
					<a href="javascript:void(0);" class="scrollTxt">
						<!-- <span class="txt">Scroll down for more information</span>-->
						<span class="icon">
							<img src="assets/login-page/img/bottom-arrow.png" alt=""/>
						</span>
					</a>
				</div>
			</div>
        </div>
		<div class="spacer"></div>
		<div class="scrollDownMore bgPic">
			<a href="javascript:void(0);" class="scrollTxt">
				<!-- <span class="txt">Scroll down for more information</span>-->
				<span class="icon">
					<img src="assets/login-page/img/bottom-arrow.png" alt=""/>
				</span>
			</a>
		</div>
    </div>
</div>


<!--main-container--> 
<div class="landingPGSEC" id="landingPGstart">
	<a id="home" class="in-page-link"></a>	
	<section class="cta cta-5 parallax" style="padding:0px;">
		<div class="background-image-holder" style="background:url(assets/login-page/img/bkg-sec3.jpg) no-repeat;">
			<div class="bgHolderOverlay"></div>
			<!-- <img alt="Background Image" class="background-image" src="assets/login-page/img/bkg-sec3.jpg">-->
		</div>
	</section>
	<section class="header header-5 fixed-header parallax item_fade_in inviteSection">
		<div class="background-image-holder" style="background:url(assets/login-page/img/banner2.jpg) no-repeat;">
			<div class="bannerOvrlay"></div>
			<!-- <img alt="Background Image" class="background-image" src="assets/login-page/img/banner2.jpg">-->
		</div>
		
		<div class="container">
			<div class="row">
				<div class="col-md-8 col-sm-12 col-md-push-2 text-center">
					<h1 class="text-white">
						<span id="typewriteText"></span> Topics You Love</h1>
					<h5 class="text-white">Swolk connects you with any stories and topics that matters to you freely.</h5>
				</div>
			</div>
			
			<div class="bannerTags">
				<a href="/tag/business" target="_blank" class="tagLi">#business</a>
				<a href="/tag/technology" target="_blank" class="tagLi">#technology</a>
				<a href="/tag/sports" target="_blank" class="tagLi">#sports</a>
				<div class="spacer"></div>
				<a href="/tag/travel" target="_blank" class="tagLi">#travel</a>
				<a href="/place?continent=Asia" target="_blank" class="tagLi">#Asia</a>
				<a href="/place?region=North%20America" target="_blank" class="tagLi">#North-America</a>
			</div>
		</div>
		
		<div class="form-holder">
			<form class="contained-form form-email" data-success="Thanks for your submission, we will be in touch shortly." data-error="Please fill all fields correctly.">
				<h6 class="text-white">Swolk is now invite-only</h6>
				<div class="bannerForm">
					<input type="text" name="email" class="validate-required validate-email signup-email-field textFld" placeholder="Email Address">
					<input type="submit" value="Invite Me »" class="btn">
				</div>
				<!--<p class="sub">
					* We don’t share your personal info with anyone.
				</p>-->
			</form>
			<!-- SOCIAL SHARE BUTTONS -->
			<div class="socialShareCont">
				<h6 class="text-white">Ask your friends to join</h6>
				<!-- Facebook -->
				<a href="http://www.facebook.com/sharer.php?u=https://swolk.com" target="_blank" class="facebookI">
					<span class="icon icon-facebook"></span>
					{{--<span class="no">10</span>--}}
				</a>
				<!-- Twitter -->
				<a href="https://twitter.com/share?url=https://swolk.com" target="_blank" class="twitterI">
					<span class="icon icon-twitter"></span>
					{{--<span class="no">8</span>--}}
				</a>
				 <!-- LinkedIn -->
				<a href="http://www.linkedin.com/shareArticle?mini=true&amp;url=https://swolk.com" target="_blank" class="linkedinI">
					<span class="icon icon-linkedin"></span>
					{{--<span class="no">124</span>--}}
				</a>
				<!-- Google+ -->
				<a href="https://plus.google.com/share?url=https://swolk.com" target="_blank" class="googleI">
					<span class="icon icon-google"></span>
				</a>    
				<!-- Email -->
				<a href="mailto:?Subject=Simple Share Buttons&amp;Body=I%20saw%20this%20and%20thought%20of%20you!%20 https://swolk.com" class="emailI">
					<span class="icon icon-email-mail-streamline"></span>
					{{--<span class="no">62</span>--}}
				</a>
			</div>
		</div>			
	</section>
	
	<!--<a id="features" class="in-page-link"></a>			
	<section class="features features-2">
		<div class="container">
			<div class="row item_fade_in">
				<div class="col-md-10 col-md-offset-1 col-sm-12 text-center">
					<h4>Swolk is a <strong>topic</strong> &amp; <strong>location</strong> based social network that connects like-minded people, who share the similar interest as you</h4>							
					<p>A list of benefits of using Swolk as part of your lifestyle.</p>
				</div>
			</div>
		
			<div class="row">
				<div class="col-sm-4 text-center feature item_fade_in">
					<img class="lazy" alt="Feature Image" data-original="assets/login-page/img/box-image1.jpg">
					<h5>Explore made easier</h5>
					<p>Find recent, trending, and popular posts of any topics.</p>
				</div>						
			
				<div class="col-sm-4 text-center feature item_fade_in">
					<img class="lazy" alt="Feature Image" data-original="assets/login-page/img/box-image2.gif">
					<h5>Follow any topics and places</h5>
					<p>Get curated posts tailored just for you.</p>
				</div>
			
				<div class="col-sm-4 text-center feature item_fade_in">
					<img class="lazy" alt="Feature Image" data-original="assets/login-page/img/box-image3.jpg">
					<h5>What's happening nearby</h5>
					<p>Always get to know what's going on around your place.</p>
				</div>
			
				<div class="col-sm-4 text-center feature item_fade_in">
					<img class="lazy" alt="Feature Image" data-original="assets/login-page/img/box-image4.gif">
					<h5>Quick Search</h5>
					<p>Search anything you love from posts, channels, tags and locations.</p>
				</div>
				
				<div class="col-sm-4 text-center feature item_fade_in">
					<img class="lazy" alt="Feature Image" data-original="assets/login-page/img/box-image5.gif">
					<h5>Saved post to read later</h5>
					<p>You can simply saved any posts for you to read later or future reference.</p>
				</div>
			
				<div class="col-sm-4 text-center feature item_fade_in">
					<img class="lazy" alt="Feature Image" data-original="assets/login-page/img/box-image6.jpg">
					<h5>Mobile friendly</h5>
					<p>Swolk is user friendly at any device include desktop, tablet, and mobile.</p>
				</div>
			</div>
		</div>
	</section>
	
	<section class="cta cta-5 parallax item_fade_in item_fade_in">
		<div class="background-image-holder" style="background:url(assets/login-page/img/bkg-sec3.jpg) no-repeat;">
			<div class="bgHolderOverlay"></div>
		</div>
	
		<div class="container item_fade_in">
			<div class="row">
				<div class="col-sm-12 text-center">
					<h2 class="text-white">For all Content Creators</h2>
					<h5 class="text-white">A list of benefits for using Swolk as content creation platform</h5>
				</div>
			</div>
		</div>
	</section>
	
	<section class="features features-2">
		<div class="container">				
			<div class="row">
				<div class="col-sm-4 text-center feature item_fade_in">
					<img class="lazy" alt="Feature Image" src="assets/login-page/img/box-image7.jpg">
					<h5>SEO friendly</h5>
					<p>We have covered everything you need for your SEO purpose.</p>
				</div>
				
				<div class="col-sm-4 text-center feature item_fade_in">
					<img class="lazy" alt="Feature Image" src="assets/login-page/img/box-image8.jpg">
					<h5>Multiple posting type</h5>
					<p>You can post with status, photo, video, link and article.</p>
				</div>
			
				<div class="col-sm-4 text-center feature item_fade_in">
					<img class="lazy" alt="Feature Image" data-original="assets/login-page/img/box-image9.jpg">
					<h5>Video embedding</h5>
					<p>We support video uploads from YouTube, Vimeo, DailyMotion and MP4.</p>
				</div>
				
				<div class="col-sm-4 text-center feature item_fade_in">
					<img class="lazy" alt="Feature Image" data-original="assets/login-page/img/box-image10.jpg">
					<h5>Analytics Dashboard</h5>
					<p>Get to know your influence level from our dashboard real-time.</p>
				</div>
			
				<div class="col-sm-4 text-center feature item_fade_in">
					<img class="lazy" alt="Feature Image" data-original="assets/login-page/img/box-image11.jpg">
					<h5>Article import</h5>
					<p>Simply submit a blog URL to import your blog content to Swolk.</p>
				</div>
			
				<div class="col-sm-4 text-center feature item_fade_in">
					<img class="lazy" alt="Feature Image" data-original="assets/login-page/img/box-image12.jpg">
					<h5>Social sharing</h5>
					<p>We are now supporting social sharing of posts to Facebook and Twitter.</p>
				</div>
			</div>
		</div>
	</section>
	<section class="cta cta-6 parallax item_fade_in ">
		<div class="background-image-holder" style="background:url(assets/login-page/img/bkg-sec5.jpg) no-repeat;">
			<div class="bgHolderOverlay"></div>
		</div>
	
		<div class="container">
			<div class="row">
				<div class="col-sm-12 text-center">
					<h2 class="text-white">Let's build a healthy community together</h2>
					<div class="secPara">
						<p>Swolk will never be in better place without you</p>
					</div>
				</div>
			</div>
		</div>
	</section>			
	
	<a id="signup" class="in-page-link"></a>			
	<section class="cta cta-3 item_fade_in">
		<div class="container">
			<div class="row v-align-children">
				<div class="col-sm-4 col-md-5 text-left">
					<h3>Join us and be part of this community!</h3>
					<p>
						We are giving away invitation for the first 300 users who sign up for early access to Swolk's beta launch.
					</p>
				</div>
			
				<div class="col-md-6 col-sm-8">
					<form class="form-email" data-success="Thanks for your submission, we will be in touch shortly." data-error="Please fill all fields correctly.">
						<h6>SIGN UP NOW FOR EARLY BETA ACCESS</h6>
						<div class="signup2">
							<input type="text" name="email" class="validate-required validate-email signup-email-field txtFld" placeholder="Email Address">
							<input type="submit" value="Request Access Now »" class="butn">
						</div>
						<p class="sub signup2Sub">
							* We don’t share your personal info with anyone.
						</p>
					</form>
				</div>
			</div>
		</div>
	</section>-->
	
	<div class="spacer"></div>
	<footer class="footer bg-dark footer-4 item_fade_in">
		<ul class="social-links">
			<li>
				<a href="https://www.facebook.com/swolkapp/" target="_blank">
					<i class="icon-facebook"></i>
				</a>
			</li>

			<li>
				<a href="https://twitter.com/swolk_com" target="_blank">
					<i class="icon-twitter"></i>
				</a>
			</li>
		</ul>
	
		<div class="container">
			<div class="row">
				<div class="col-sm-12 text-center">
					<div class="loginPGterms">
						<a href="{{url('/terms-and-conditions')}}">Terms of use</a>&nbsp;&nbsp;|&nbsp;&nbsp;<a href="{{url('/privacy-policy')}}">Privacy policy</a>
					</div>
				</div>
				<div class="col-sm-12 text-center">
					<a href="#">
						<img class="lazy" alt="Logo" data-original="assets/login-page/img/swolk-logo-white.png">
						<p class="sub text-white">
							&copy; Copyright 2017 Swolk.com - All Rights Reserved
						</p>
					</a>
				</div>
			</div>
		</div>
	</footer>
</div>



<style type="text/css">
	body{height:auto !important;}
</style>
@endsection
@section('landingScripts')
	<script src="https://cdnjs.cloudflare.com/ajax/libs/gsap/latest/TweenMax.min.js"></script>
	<script src="https://cdnjs.cloudflare.com/ajax/libs/gsap/latest/plugins/ScrollToPlugin.min.js"></script>
	<script src="/assets/login-page/js/smooth-scroll.min.js"></script>
	<script src="/assets/login-page/js/placeholders.min.js"></script>
	<script src="/assets/login-page/js/twitterfetcher.min.js"></script>
	<script src="/assets/login-page/js/spectragram.min.js"></script>
	<script src="/assets/login-page/js/scripts.js"></script>
	<script src="/assets/login-page/js/typeit.js"></script>
	<script src="assets/plugins/jquery-lazyload/jquery.lazyload.js"></script>

	<script>
        $("img.lazy").lazyload({
            threshold : 200,
            effect : "fadeIn"
        });
	</script>

	{{--https://macarthur.me/typeit/--}}
	<script type="text/javascript">
	$(document).ready(function(){
		$("body").on("click",".scrollTxt, .signupScroll", function(){
			$('html, body').animate({
				scrollTop: $("#landingPGstart").offset().top
			}, 600);
			return false;
		});
		$("body").on("click",".headLogReg", function(){
			if(!$(".login-wrapper .bg-pic").is(":visible")){
				$(".login-wrapper .bg-pic").slideDown(500);
			}else{
				$(".login-wrapper .bg-pic").slideUp(500);
			}
			$('html, body').animate({ scrollTop: 0 },600);
		});
		
		$('#typewriteText').typeIt({
			speed:180,
			deleteSpeed:50,
			startDelay: 1250,
			loop:true
		})
		.tiType('Explore')
		.tiPause(2000)
		.tiDelete(7)
		.tiType('Read')
		.tiPause(2000)
		.tiDelete(4)
		.tiType('Share')
		.tiPause(2000)
		.tiDelete(5)
		.tiType('Follow')
		.tiPause(2000)
		.tiDelete(6)
		.tiType('Discuss')
		.tiPause(2000)
		.tiDelete(7)
	});
	
	$(window).on("load", function (){
		boxShow();
	});
	$(window).on("scroll", function (){
		boxShow();
	});

	function boxShow(){
		if($(window).scrollTop() >= 200) {
		   $(".socialShareCont").addClass("showNow");
	   }else{
		   $(".socialShareCont").removeClass("showNow");
	   }
		var thisPos = $(".footer").offset().top ;
		var screenPos = $(window).scrollTop();
		var screenHeight = $(window).height() - 160;		
		if(thisPos <= (screenPos + screenHeight) ){
			$(".socialShareCont").removeClass("showNow");
		}
	}
</script>

@endsection

