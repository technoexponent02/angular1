@extends('seo.public')

@section('customStyle')
	<link rel="stylesheet" type="text/css" href="{{ asset('assets/pages/css/common.css') }}" />
@endsection

@section('titleMetaTag')
	<title> {{ $lowest_region_name }} | SWOLK</title>
	<meta name="description" content="{{ $featured_image_post['short_description'] }}"/>
	<meta property="og:title" content="{{ "$lowest_region_name | SWOLK" }}" />
	<meta property="og:type" content="" />
	<meta property="og:url" content="{{ url($place_url) }}" />
	<meta property="og:image" content="{{ isset($featured_image_post['image']) ? $featured_image_post['image'] : "" }}" />
	<meta property="og:description" content="{{ $featured_image_post['short_description'] }}" /> <?php /* */ ?>
	
@endsection

@section('pageTitle', 'Tag Page')

@section('content')
	<style type="text/css">
		body{background-color:#e5e5e5;}
	</style>
	<div class="page-container">
		<div class="header">
			<div class="customHeader clearfix">
				<div class="headerMiddle">
					<div class="brand inline">
						<a href="">
							<img src="/assets/img/logo_2x.png" alt="logo" height="26"/>
						</a>
					</div>
				</div>
			</div>
		</div>

		<div class="page-content-wrapper">
			<div class="headerLine"></div>
			<div class="content">
				<div class="full-height full-width">
					<!-- <div class="categoryBanner" style="background:url(https://s3-ap-southeast-1.amazonaws.com/swolk/post/1487994317F3MXiwKMEr.dgsdfsg.jpg) no-repeat;">
                        <div class="ovrlay">
                            <div class="categoryBnrDesc">
                                <h2>coldplay</h2>
                                <span class="tagFollowUsesr">2 followers</span>
                                <div class="categoryBtns">
                                    <a class="followBtn wh">
                                        <span>FOLLOW</span>
                                    </a>
                                </div>
                            </div>
                            <div class="coverFooter">
                                <a class="coverFooterSingle">
                                    <span class="userBy">Featured Image by</span>
                                    <span class="profImage">
                                        <span class="image" style="background:url(https://s3-ap-southeast-1.amazonaws.com/swolk/profile/thumbs/1485139006a9QkSGY8Dd.coldplay-wallpaper-picture-1.jpg) no-repeat;"></span>
                                    </span>
                                    <span class="userByname">Ronald Halim (this is original account)</span>
                                </a>
                            </div>
                        </div>
                    </div> -->
					<div class="container-fluid padding-25 sm-padding-10 profileContainer" style="padding-top:0px !important;">
						<div class="whiteAreaCont">
							<div class="catTagSubcatg">
								<div id="categoryheader">
									<div style="display:block; background-color:#fff;">
										<h5 style="display:block; margin:0; padding:10px 0;">Recommend Topic</h5>
									</div>
									<div class="clearfix">
										<div class="pickCatrgorySliderSec loaded">
											<div class="pickCatrgorySlider">
												<div class="" style="display:block;">
													<div class="owl-carousel categorySlider">
														<?php
														foreach($related_categories as $key=>$category){
														?>
														<div class="item">
															<a class="catgBx" href="<?php echo $category['location_url']; ?>" <?php if(isset($category['color'])){ ?> style="background-color:<?php echo $category['color']; ?>" <?php } ?> >
																<div class="catgBxImage" style="background:url(<?php echo $category['featured_post_image']; ?>) no-repeat;">
																	<div class="catgttl">
																		<span class="ng-binding"><?php echo $category['category_name']; ?></span>
																	</div>
																</div>
															</a>
														</div>
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
						<div class="panel newpanel" style="margin-top:0px !important; border:none;">
							<div class="whiteAreaCont" style="padding-top:14px;">

							</div>
							<div class="row postRow">
								<div class="col-md-12">
									<div class="post_card_container">
										<div class="blockContentRow inline">
											<?php
											foreach($posts as $key=>$post){
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
			<div class="container-fluid container-fixed-lg footer">
				<div class="copyright sm-text-center">
					<p class="small no-margin pull-left sm-pull-reset">
						<span class="hint-text">Copyright © 2017</span>
						<span class="font-montserrat">Swolk</span>.
						<span class="hint-text">All rights reserved.</span>
						<span class="sm-block">
							<a class="m-l-10 m-r-10" href="">Terms of use</a> |
							<a class="m-l-10" href="">Privacy Policy</a>
						</span>
					</p>
					<p class="small no-margin pull-right sm-pull-reset">
						<a href="">Hand-crafted</a>
						<span class="hint-text">&amp; Made with Love ®</span>
					</p>
					<div class="clearfix"></div>
				</div>
			</div>
		</div>
	</div>

@stop

@section('customScript')
	{{--<script src="{{ asset('assets/js/jquery-1.9.1.js') }}" type="text/javascript"></script>--}}
	<script src="/assets/plugins/carousel/owl.carousel.js" type="text/javascript"></script>
	<script type="text/javascript">
		$(window).load(function(){
			$('.owl-carousel').owlCarousel({
				loop: true,
				nav: true,
				navigation : true,
				responsive:{
					0:{
						items:2
					},
					540:{
						items:3
					},
					600:{
						items:4
					},
					768:{
						items:5
					},
					1000:{
						items:6
					},
					1200:{
						items:7
					},
					1600:{
						items:9
					},
					1700:{
						items:10
					},
					1800:{
						items:11
					}
				}
			});
		});

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