@extends('seo.public')

@section('customStyle')
<link rel="stylesheet" type="text/css" href="{{ asset('assets/seo/common.css') }}" />
@endsection

@section('titleMetaTag')
	<title>{{ $user->first_name.htmlspecialchars_decode('&#039;').'s' }} Profile | SWOLK</title>
	<meta name="description" content="{{ $user->about_me }}"/>
	<meta property="og:title" content="{{ $user->first_name.htmlspecialchars_decode('&#039;')."s Profile | SWOLK" }}" />
	<meta property="og:type" content="" />
	<meta property="og:url" content="{{ url('/profile/'.$user->username) }}" />
	<meta property="og:image" content="{{ (!empty($user->profile_image) ? url(generate_profile_image_url('profile/'.$user->profile_image)) : '') }}" />
	<?php  /* ?><meta property="og:description" content="{{ (!empty($user->about_me) ? $user->about_me : '') }}" /><?pph */ ?>
@endsection

@section('pageTitle', 'Profile Page')

@section('content')
<div class="page-container">
	<div class="header">
		<div class="customHeader clearfix">
			<div class="headerMiddle">
				<div class="brand inline">
					<a href="">
						<img src="https://swolk.com/assets/img/logo_2x.png" alt="logo" height="26"/>
					</a>
				</div>
			</div>
		</div>
	</div>

	<div class="page-content-wrapper">
		<div class="headerLine"></div>
		<div class="content">
			<div class="full-height full-width">
				<?php /* <div class="coverPhotoContaine">
					<div class="coverPhoto">
						<?php
						if(!empty($user->cover_image)){
						?>
							<img src="{{ generate_profile_image_url('profile/cover/'.$user->cover_image) }}" alt="Cover picture"/>
						<?php
						}
						?>

						<div class="clearfix"></div>
					</div>
				</div>
				<div class="profileUserShow">
					<div class="userBox">
						<div class="profilePic">
							<?php if(!empty($user->profile_image)){ ?>
							<a href="#" style="background:url({{ generate_profile_image_url('profile/thumbs/'.$user->profile_image) }}) no-repeat;"></a>
							<?php } else { ?>
							<a href="#" class="img {{ $user->user_color }}">
								<span class="txt">{{ substr($user->first_name, 0, 1) }}</span>
							</a>	
							<?php } ?>								
						</div>
						<div class="boxarea">
							<span class="profileTtl">
								<a href="#" >{{ $user->first_name.' '.$user->last_name }} <span></span></a>
								<span class="profileSmTtl">
									<span>{{ calculateAgeFromDob($user->dob) }}  old</span>
									<!-- <span>
										, <i class="fa fa-map-marker"></i>
									</span> -->
								</span>
							</span>
							<span class="profileSmTtl">{{ $user->about_me }}</span>
						</div>
						<div class="areaFollow"></div>
					</div>
				</div> */ ?>
				<div class="container-fluid profileContainer profilePG" style="padding-top:0 !important;">
					<?php /*<div class="row">
						<div class="col-md-12 col-lg-12"> 
							<div class="profileNavSlider hide640">
								<div class="profileNav">					
									<div id="owl-demo5" class="owl-carousel owl-theme owl-loaded">
										<div class="owl-stage-outer">
											<div class="owl-stage" style="transform: translate3d(0px, 0px, 0px); transition: 0s; width: 828px;">
												<div class="owl-item active" style="width: 165.6px; margin-right: 0px;">
													<div class="item stopClickArea">
														<div class="stopClick"></div>
														<a href="javascript:void(0);">
															<strong>{{ $userDataProfileViews }}</strong>Profile Views
														</a>
													</div>
												</div>
												<div class="owl-item active" style="width: 165.6px; margin-right: 0px;">
													<div class="item stopClickArea">
														<div class="stopClick"></div>
														<a href="javascript:void(0);">
															<strong>{{ thousandsSuffix($user->points) }}</strong>Points  
														</a>
													</div>
												</div>
												<div class="owl-item active" style="width: 165.6px; margin-right: 0px;">
													<div class="item">
														<a href="javascript:void(0);" id="followerTab"><strong>{{ $follower_count }}</strong> Followers</a>
													</div>
												</div>
												<div class="owl-item active" style="width: 165.6px; margin-right: 0px;">
													<div class="item">
														<a href="javascript:void(0);"><strong>{{ $following_count }}</strong> Following</a>
													</div>
												</div>
												<div class="owl-item active" style="width: 165.6px; margin-right: 0px;">
													<div class="item">
														<a href="javascript:void(0);" class="active" id="postView" ><strong>{{ $total_post }}</strong> Posts</a>
													</div>
												</div>
											</div>
										</div>
									</div>	
								</div>	
							</div>			
						</div>
					</div> */ ?>
					<div style="display:block; position:relative;">
						<!-- Commented on Purpose -->
						<!-- <div class="scrollTabOuter">
							<div class="scrollTab scrollTabCalc nav-up-now">									
								<div class="profileExploreNav">
									<div class="exploreDropdown">
										<span class="navTl">
											<span>Original Post</span> <i class="fa fa-caret-down"></i>
										</span>
										<ul>
											<li>
												<a href="" class="sel">
													<i class="fa fa-check"></i> All Post
												</a>
											</li>
											<li>
												<a href="">
													Original Post
												</a>
											</li>
										</ul>
									</div>
									<div class="nav nav-tabs nav-tabs-simple innerTab inlineView explorTabSlider flickity-enabled is-draggable" tabindex="0">
										<div class="flickity-viewport" style="height: 44px;">
											<div class="flickity-slider" style="left: 0px; transform: translateX(0%);">
												<div class="itm active is-selected" style="position: absolute; left: 0%;">
													<a class="tb3" href="javascript:void(0)">
														<div class="txt">
															<img src="https://swolk.com/assets/pages/img/featured-recent-icon.png" alt="">Recent
														</div>
													</a>
												</div>
												<div class="itm" style="position: absolute; left: 50%;">
													<a class="tb2" href="javascript:void(0)">
														<div class="txt">
															<img src="https://swolk.com/assets/pages/img/featured-popular-icon.png" alt="">Popular
														</div>
													</a>
												</div>
											</div>
										</div>
									</div>
								</div>
							</div>
						</div> -->
						<div class="row postRow">
							<div class="col-md-12">			
								<div class="post_card_container">			
									<div class="blockContentRow inline">
									<?php
									foreach($allPosts as $key=>$post){
									?>
										@include("includes.postcard")
									<?php
									}
									?>
										
									</div>					
								</div>					
							</div>					
						</div>						
					</div>						
				</div>				
			</div>
		</div>
	</div>
</div>

@stop

@section('customScript')
{{--<script src="{{ asset('assets/js/jquery-1.9.1.js') }}" type="text/javascript"></script>--}}
<script type="text/javascript">
	$(window).load(function(){
		$("body").on("click",".exploreDropdown .navTl", function () {
			if(!$(this).next("ul").is(":visible")){
				$(this).next("ul").slideDown(200);
			}else{
				 $(this).next("ul").slideUp(200);
			}
		});

		$("body").on("click",".moreBtnN", function () {
			if(!$(this).parent().children(".otherSubsh").is(":visible")){
			$(".otherSubsh").css({"display":"none"});
			$(this).parent().children(".otherSubsh").css({"display":"block"});
			}else{
			$(".otherSubsh").css({"display":"none"});
			}
			});
			$(document).click(function(e) {
			var clickPoint = $('.moreBtnN');
			if(!clickPoint.is(e.target) && clickPoint.has(e.target).length == 0)
			{ 
			$(".otherSubsh").css({"display":"none"});
			}
	   });
	});
	
</script>

@endsection

<?php
function loadHtml($html=''){
	$parsedHtml = '';
	$doc = new \DOMDocument();
	$doc->loadHTML($html);
	$parsedHtml = preg_replace('/^<!DOCTYPE.+?>/', '', str_replace( array('<html>', '</html>', '<body>', '</body>'), array('', '', '', ''), $doc->saveHTML()));
	return $parsedHtml;
}
?>