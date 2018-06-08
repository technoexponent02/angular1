<style type="text/css">
	/* body{background-color:#f6f9fa;} */
	.header{background:#fff !important;}
	.largeWidth{width:100%;}
	.catgttl>span.transformText{text-transform:inherit;}
	.catgttl>span.modifyText{font-size:14px;}
</style>
<div ng-if="showAjaxLoadedPanel">
    <div class="categoryBanner placeBanner" ng-style="{background:'url('+coverPhoto+') no-repeat'}">
        <div class="ovrlay">
            <div class="categoryBnrDesc">
                <span class="tagFollowUsesr" style="padding-bottom: 2px;">PLACE</span>
                <h2 ng-bind-html="pageTtlHtml"></h2>
                <span class="tagFollowUsesr">{{ totalFollower }} followers | {{ totalPost }} posts</span>
                <div class="categoryBtns" ng-if="::user.guest==0">
                    <a ng-click="placeFollowUnfollow()" class="followBtn wh">
                        <span ng-if="!tagFollowStatus">FOLLOW</span>
                        <span class="ico" ng-if="tagFollowStatus">FOLLOWING</span>
                    </a>
                </div>
                <div class="categoryBtns" ng-if="::(user.guest!=0)">
                    <a ng-click="redirecToLogin()" class="followBtn wh">
                        <span ng-if="!tagFollowStatus">FOLLOW</span>
                        <span class="ico" ng-if="tagFollowStatus">FOLLOWING</span>
                    </a>
                </div>
            </div>			
			<div ng-if="showAjaxLoadedPanel">
				<div class="coverFooter" ng-if="::featured_image_user">
					<a class="coverFooterSingle"
					   data-toggle="modal" data-target="#myModal{{::featured_image_post_id}}"
					   postcard="showPostDetails(featured_image_post_id,featured_image_child_post_id,0)">
						<span class="userBy">Featured Image by</span>
						<span class="profImage" ng-if="::featured_image_user.thumb_image_url">
							<span class="image" style="background:url({{::featured_image_user.thumb_image_url}}) no-repeat;">
							</span>
						</span>
						<span class="profImage {{::featured_image_user.user_color}}"
							  ng-if="::(!featured_image_user.thumb_image_url)">
							<span class="image">
								<span class="txt">{{::featured_image_user.first_name.charAt(0)}}</span>
							</span>
						</span>
						<span class="userByname">{{::(featured_image_user.first_name + ' ' + featured_image_user.last_name)}}</span>
					</a>
				</div>
			</div>
		</div>
    </div>
</div>


<div class="container-fluid padding-25 sm-padding-10 profileContainer" style="padding-top:0px !important;">
    <div class="whiteAreaCont" ng-if="showAjaxLoadedPanel">
		<div class="catTagSubcatg" ng-if="related_categories.length">
			<div id="categoryheader">
				<h5 class="catHeadTTL" style="padding-top:16px !important; text-transform: capitalize;">Related Topics</h5>
				<div class="clearfix">
					<div class="pickCatrgorySliderSec loaded">
						<div class="smallLoaderOwl"></div>
						<div class="smallLoader2"></div>
						<div class="pickCatrgorySlider" ng-if="related_categories.length">
							<div class="hide640" style="display:block;">
								<owl-carousel class="owl-carousel categorySlider"
											  id="relatedCategory"
											  data-options="owlCarouselOptions"
											  data-hide-selector=".smallLoader2"
											  data-hide-time="4200"
											  style="display:none;">
									<div class="item"
										 owl-carousel-item
										 hide-loader
										 ng-repeat="category in related_categories | unique:'location_url'">
										<a ng-href="{{::(category.type == 'addr' ? category.location_url :(category.question!='')?'/questions/'+ category.category_name_url: '/tag/' + category.category_name_url)}}"
										   class="catgBx"
										   ng-style="::{'background-image': category.color}">
											<div class="catgBxImage" style="background:url({{::category.featured_post_image}}) no-repeat;">
												<div class="catgttl">
													<!-- <span>{{::category.category_name }}</span> -->
													<span ng-class="{transformText:category.type == 'tag', modifyText:category.tag_text.length >40 && category.type == 'tag' } ">{{(category.type == 'addr' ? category.category_name :(category.question!='')?category.question.length >50 ? category.question.slice(0, 50)+'...?' : category.question :  category.tag_text.length >50 ? category.tag_text.slice(0, 50)+'...' : category.tag_text )}}</span>
												</div>
											</div>
										</a>
									</div>
								</owl-carousel>
							</div>

							<div style="display: block;">
								<div class="flickitySlider"
									 style="display:none;"
									 ng-attr-data-options="{{relCatFlickityOptions | json}}">
									<div class="item"
										 flickity-item
										 ng-repeat="category in related_categories | unique:'location_url'">
										<a ng-href="{{::(category.type == 'addr' ? category.location_url :(category.question!='')?'/questions/'+ category.category_name_url: '/tag/' + category.category_name_url)}}"
										   class="catgBx"
										   ng-style="::{'background-image': category.color}">
											<div class="catgBxImage" style="background:url({{::category.featured_post_image}}) no-repeat;">
												<div class="catgttl">
													<span  ng-class="{transformText:category.type == 'tag' , modifyText:category.tag_text.length >35 && category.type == 'tag'} ">{{(category.type == 'addr' ? category.category_name :(category.question!='')?category.question.length >40 ? category.question.slice(0, 40)+'...?' : category.question :  category.tag_text.length >40 ? category.tag_text.slice(0, 40)+'...' : category.tag_text)}}</span>
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
	</div>
    <!-- END category -->
    <div class="panel newpanel" style="margin-top:0px !important; border:none;">
		<div class="whiteAreaCont" style="padding-top:14px;">
			<div class="scrollTabOuter">
				<div class="scrollTab scrollTabCalc">
					<div class="smallLoader_feature"></div>
					<div class="nav nav-tabs nav-tabs-simple innerTab inlineView explorTabSlider">
						<div class="itm" ng-class="{'active': (post_type == 'recent')}">
							<a ng-click="changeTab('recent')">
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
							<a ng-click="changeTab('trending')">
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
							<a ng-click="changeTab('popular')">
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
							<a ng-click="changeTab('top_channel')">
								<div class="txt">
									<img src="assets/pages/img/featured-topchanel-icon.png" alt=""/>
									<div class="nm">
										<span class="thin">Top Channel</span>
										<span class="strong">Top Channel</span>
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
<?php /* <!-- Details Modal-->
<postcard-modal></postcard-modal> */ ?>

<!-- DELETE POST CARD MODAL -->

<delete-post-modal></delete-post-modal>

<!-- REPORT POST CARD MODAL -->
<report-post-modal></report-post-modal>
<!-- PROMPT SINGIN BOX -->
<prompt-signin-box></prompt-signin-box>


