<div class="container-fluid padding-25 sm-padding-10 no-heroImage profileContainer">
	<style type="text/css">
		.header{background:#fff !important;}

		.non-login-user .nonlogin-jumbotron h1 {
			font-size:40px;
			font-weight: 400!important;
			margin-bottom: 10px;
			line-height:62px;
			letter-spacing: -1px;
			color: #fff!important;
			font-family: Lato, "Helvetica Neue", Helvetica, Arial, sans-serif;
		}
		.non-login-user .bannerTags {
			display: block;
			margin: 80px 0 0;
			text-align: center;
			color: #222;
		}
		.non-login-user .tagLi {
			display: inline-block;
			vertical-align: top;
			padding: 5px 22px;
			background-color: rgba(255, 255, 255, .2);
			color: #fff!important;
			font-size: 16px;
			border-radius: 100px;
			font-weight: 500;
			margin: 0 3px 8px;
			cursor: pointer;
		}
		.non-login-user .tagLi:hover {
			background-color: rgba(255, 255, 255, .1);
		}
		.non-login-user .tagLi:first-child {
			margin-left: 0;
		}
		.non-login-user .tagLi:last-child {
			margin-right: 0;
		}
		.non-login-user .form-holder {
			padding: 0 15px;
			position: relative;
			width: 100%;
			z-index: 3;
			text-align: center;
			margin: 80px 0 0;
		}
		.non-login-user h6, h6 {
			font-size: 17px;
			line-height: 24px;
			font-weight: 500!important;
			margin-bottom: 16px;
		}
		.non-login-user .bannerForm {
			display: block;
			width: 400px;
			max-width: 100%;
			margin: 0 auto;
			position: relative;
			padding: 0;
		}
		.non-login-user .bannerForm input[type=text], select {
			height: 54px;
			line-height: 48px;
			padding-left: 16px;
			border: 1px solid #eee;
			background: #fff;
			border-radius: 3px;
			font-size: 16px;
			color: #888;
			font-weight: 400;
		}
		.non-login-user .bannerForm .textFld {
			border-radius: 100px !important;
			width: 100%;
		}
		.non-login-user .bannerForm input[type=text] {
			border-color: #fff;
		}
		.non-login-user #hero-image .cover-photo .bannerForm input[type=submit] {
			font-weight: 500!important;
			height: 54px;
			line-height: 48px;
			text-align: center;
			background: #6dc77a;
			border: 1px solid #6dc77a;
			font-size: 18px;
			padding: 0 36px;
			transition: all .3s ease;
			-webkit-transition: all .3s ease;
			-moz-transition: all .3s ease;
			color: #fff;
		}
		.non-login-user #hero-image .cover-photo .bannerForm input[type=submit]:hover {
			background: #5bc069;
			color: #fff;
		}
		.non-login-user #hero-image .cover-photo .bannerForm .btn {
			display: inline-block;
			position: static!important;
			border-radius: 100px !important;
			width: auto;
			padding: 0 44px;
			text-align: center;
			margin: 6px 0 0 0;
			right: 3px;
			top: 1px;
		}
		.non-login-user .categoryHeadingRow.lg_tl{display:none !important;}
		.non-login-user .panel.newpanel.m-t-10{margin-top:0 !important;}
		.btn-default{border-color:#ccc !important;}
	</style>
	<script>
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
	</script>
	<div class="whiteAreaCont">
		<div class="clearfix"></div>		
		<div class="panel newpanel m-t-10" style="margin-bottom:0px;">
			<!--<div class="categoryHeadingRow lg_tl mobileOnly" style="margin-bottom:12px;">
				<h5>Explore</h5>
			</div>
			<div  ng-if="showHeroImage" class="jumbotron" id="hero-image" data-pages="parallax" data-social="cover" 
				style="background:url(/assets/img/profiles/cityImage.jpg);"> -->
			<!-- <div  class="jumbotron" id="hero-image" data-pages="parallax" data-social="cover" 
				style="background:url(/assets/img/profiles/cityImage.jpg);"> -->
			<!-- <div class="jumbotron loginexplorehero" id="hero-image" data-pages="parallax" data-social="cover" style="background:#fff;">
				<div class="cover-photo" style="transform: translateY(0px);">
					<a role="button" id="hero-close-button"><i class="pg-close close-btn"></i></a>
					<h2 class="m-l-400 hero-image-text">Dive Deep to your Passion</h2>
					<div class="exploreSearch">
						<div class="srchCont">
							<input type="text" class="srchFld" placeholder="Search for topics"/>
							<button class="btn btn-danger"><i class="fa fa-search"></i></button>
						</div>
					</div>
				</div>
			</div> -->

			<div class="nonlogin-jumbotron nonloginExploreHead" id="hero-image" data-pages="parallax" data-social="cover" 
				>
				<div class="cover-photo" style="transform: translateY(0px);">

					<div class="container">
						<div class="row">
							<div class="col-md-8 col-sm-12 col-md-push-2 text-center">
								<h1 class="text-white">
									<span id="typewriteText"></span>topics you love</h1>
								<h5 class="text-white">Stories and discussions within your passion.</h5>
							</div>
						</div>
						
						<!-- <div class="bannerTags">
							<a href="/tag/business" target="_blank" class="tagLi">#business</a>
							<a href="/tag/technology" target="_blank" class="tagLi">#technology</a>
							<a href="/tag/sports" target="_blank" class="tagLi">#sports</a>
							<div class="spacer"></div>
							<a href="/tag/travel" target="_blank" class="tagLi">#travel</a>
							<a href="/place?continent=Asia" target="_blank" class="tagLi">#Asia</a>
							<a href="/place?region=North%20America" target="_blank" class="tagLi">#North-America</a>
						</div>-->

						<div class="form-holder">
							<form class="contained-form form-email" data-success="Thanks for your submission, we will be in touch shortly." data-error="Please fill all fields correctly." action="<?php echo  url('signup'); ?>" metod='get'>
								<!--<h6 class="text-white">Swolk is now invite-only</h6>-->
								<div class="bannerForm">
									<!--<input type="text" name="email" class="validate-required validate-email signup-email-field textFld field-error" placeholder="Email Address">-->
							
									<input type="submit" value="Get Started »" class="btn">
							
								</div>
								<!--<p class="sub">
									* We don’t share your personal info with anyone.
								</p>-->
							</form>
						</div>

					</div>
					
				</div>
			</div>
			
			<div class="popularTopicSec">
				<h5>Today’s Trending Topics</h5>
				<div class="popularTopicSliderCont">
					<div class="popularTopicsLoader"></div>
					<div class="popularTopicSlider" >	
						<div class="itm"  ng-repeat="todayTrandingTopic in todayTrandingTopics">
							<a ng-href="{{ todayTrandingTopic.question_tag=='' ? '/tag/'+todayTrandingTopic.tag_name : '/questions/'+todayTrandingTopic.tag_name  }}">{{ todayTrandingTopic.question_tag==''? todayTrandingTopic.tag_text : todayTrandingTopic.question_tag }}</a>
						</div>
						
					</div>
				</div>
			</div>
			
			<!-- Category panel -->
			<div ng-if="showAjaxLoadedPanel">
				<div id="categoryheader" ng-if="mainCategories.length">
					<!-- <h5 class="catHeadTTL exploreLeftalign" style="padding:16px !important; padding-left:10px !important; padding-right:10px !important; text-transform: capitalize;">Select Category</h5>-->
					<div class="clearfix">
						<div class="pickCatrgorySliderSec loaded">
							<div class="smallLoaderOwl"></div>
							<div class="pickCatrgorySlider exploreCatgSlider">
								<div style="display:block;" class="hide640">
									<owl-carousel class="owl-carousel categorySlider "
												  id="owl-demo"
												  data-options="owlCarouselOptions"
												  data-hide-selector=".smallLoader2 "
												  data-owl-time="1000"
												  data-hide-time="3000"
												  style="display:none;">
										<div class="item"
											 owl-carousel-item
											 hide-loader
											 ng-repeat="category in mainCategories">
											<a class="catgBx"
											   ng-class="{'catselected': selectedCategory.value == category.value}"
											   ng-style="::{'background': category.color}"
											   ng-click="changeCategory(category)"
											   data-category="{{::category.value}}"
											>
												<div class="catgBxImage" style="background:url({{::category.featured_post_image}}) no-repeat;">
													<div class="catgttl">
														<span>{{::category.category_name }}</span>
													</div>
												</div>
											</a>
										</div>
									</owl-carousel>
								</div>

								<div style="display:block;">
									<div class="flickitySlider" style="display:none;">
										<div class="item"
											 ng-class="{'sl': selectedCategory.value == category.value}"
											 flickity-item
											 ng-repeat="category in mainCategories">
											<a class="catgBx"
											   ng-style="{'background': category.color}"
											   ng-click="changeCategory(category)">
												<div class="catgBxImage" style="background:url({{::category.featured_post_image}}) no-repeat;">
													<div class="catgttl">
														<span>{{ ::category.category_name }}</span>
													</div>
												</div>
											</a>
										</div>
									</div>
								</div>
							</div>
						</div>
					</div>
				</div>
			</div>


			<div class="categoryHeadingRow exploreCatgHead  exploreLeftalign" id="categoryheader" style="padding-bottom:0;">
				<h5 class="selcatgTTL" style="text-transform: capitalize;">
					<!-- <span>Category:</span> -->{{ selectedCategory.category_name }} 
					<label class="followBtn exploremobile" ng-if="selectedCategory.value != 'all'">
						<a ng-click="catFollowUnfollow(selectedCategory.value)">
							<!-- <span>FOLLOW THIS CATEGORY</span> -->
							<span ng-if="!followStatus">FOLLOW</span>
							<span class="ico" ng-if="followStatus">FOLLOWING</span>
						</a>
					</label>
					<p class="spacer" style="margin:0; padding:0;"></p>
				</h5> 
			<!-- <label class="followBtn">
				<input type="checkbox" value="" name="">
				<span>FOLLOW THIS CATEGORY</span>
			</label> -->
			<label class="followBtn exploreDesktop" ng-if="selectedCategory.value != 'all'">
				<a ng-click="catFollowUnfollow(selectedCategory.value)">
					<!-- <span>FOLLOW THIS CATEGORY</span> -->
					<span ng-if="!followStatus">FOLLOW</span>
					<span class="ico" ng-if="followStatus">FOLLOWING</span>
				</a>
			</label>

			</div>
		</div>
	</div>

<div class="panel newpanel" style="margin-top:0px !important; border:none;">
	<div class="whiteAreaCont" style="padding-top:14px;">
		<div class="scrollTabOuter">
			<div class="scrollTab scrollTabCalc">
				<div class="smallLoader_feature"></div>
				<div class="nav nav-tabs nav-tabs-simple innerTab inlineView explorTabSlider">	
					<div class="itm" ng-class="{'active': (post_type == 'recent')}">
						<a ng-click="changeTab('recent')" >
							<div class="txt">
								<img src="assets/pages/img/featured-recent-icon.png" alt=""/>
								<div class="nm">
									<span class="thin">Recent</span>
									<span class="strong">Recent</span>
								</div>
							</div>
						</a>
					</div>
					<div class="itm" ng-class="{'active': (post_type == 'trending')}">
						<a ng-click="changeTab('trending')" >
							<div class="txt">
								<img src="assets/pages/img/featured-trending-icon.png" alt=""/>
								<div class="nm">
									<span class="thin">Trending</span>
									<span class="strong">Trending</span>
								</div>
							</div>
						</a>
					</div>
					<div class="itm" ng-class="{'active': (post_type == 'popular')}">
						<a ng-click="changeTab('popular')" >
							<div class="txt">
								<img src="assets/pages/img/featured-popular-icon.png" alt=""/>
								<div class="nm">
									<span class="thin">Popular</span>
									<span class="strong">Popular</span>
								</div>
							</div>
						</a>
					</div>
					<div class="itm" ng-class="{'active': (post_type == 'top_channel')}">
						<a ng-click="changeTab('top_channel')" >
							<div class="txt">
								<img src="assets/pages/img/featured-topchanel-icon.png" alt=""/>
								<div class="nm">
									<span class="thin">Top Channel</span>
									<span class="strong">Top Channel</span>
								</div>
							</div>
						</a>
					</div>
					<div class="itm" ng-class="{'active': (post_type == 'location')}">
						<a ng-click="changeTab('location')" class="tb5" >
							<div class="txt">
								<img src="assets/pages/img/featured-location-icon.png" alt=""/>
								<div class="nm">
									<span class="thin">Near Me</span>
									<span class="strong">Near Me</span>
								</div>
							</div>
						</a>
					</div>
				</div>	
			</div>
		</div>
	</div>

	<div ng-hide="hidePostFilter">
		<!-- post type nav desk -->
		<post-type-nav navtype="desk"></post-type-nav>
		<!-- post type nav mobile -->
		<post-type-nav navtype="mob"></post-type-nav>
	</div>

	<div style="display:block; position:relative;">     
		<div class="loaderImage"></div>
		<!--  Tab container   -->
		<div ng-include="currentTabUrl"></div>
	</div>
</div>
</div>

<!-- SHARE Modal -->
<sharepost-card></sharepost-card>
 <!-- Details Modal-->
<postcard-modal></postcard-modal> 

<!-- DELETE POST CARD MODAL -->
<delete-post-modal></delete-post-modal>

<!-- REPORT POST CARD MODAL -->
<report-post-modal></report-post-modal>
<!-- REPORT COMMENT  MODAL -->
<report-comment-modal></report-comment-modal>

<!-- PROMPT SINGIN BOX -->
<prompt-signin-box></prompt-signin-box>
<!-- <div id="blace">blace</div> -->

