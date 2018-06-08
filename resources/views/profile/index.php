<style type="text/css">
	.scrollTab{overflow:visible;}
	/* body{background:#f6f9fa !important;} */
	.header{background:transparent; box-shadow:none;}
	.page-container .page-content-wrapper .content{padding-top:0;}
</style>
<div ng-if="showProfileIndex">
	<div class="coverPhotoContainer">
		<div class="coverPhoto">
			<loading></loading>
			<!-- <img ng-if="userData.cover_image" ng-src="{{userData.cover_image}}"  alt="Cover picture">-->
			<div class="coverImageSH" ng-if="userData.cover_image" style="background:url({{userData.cover_image}}) no-repeat;"></div>
			<div class="clearfix"></div>
		</div>
		<label class="changePhoto" ng-if="userData.id==user.id">
			<input ng-model="myFile" onchange="angular.element(this).scope().file_changed(this)"  type="file" accept="image/*" />
			<span>
			<i class="fa fa-pencil" aria-hidden="true" ></i> Change Cover
			</span>
		</label>
		<div class="coverFooter">
		</div>
	</div>
	<div class="profileUserShow">
		<div class="userBox" ng-class="{'nowFollow':userData.id != user.id}">
			<div class="profilePic">
				<a href="#" ng-if="::userData.thumb_image_url" style="background:url({{::userData.thumb_image_url}}) no-repeat;"></a>
				<a href="#" ng-if="::(!userData.thumb_image_url)" class="img {{::userData.user_color}}">
					<span class="txt">{{::userData.first_name.charAt(0)}}</span>
				</a>
			</div>
			<div class="userBoxCell">
				<div class="areaFollow">
					<ul class="followMessage" ng-if="::(userData.id != user.id && user.guest==0)">
						<li>
							<a allfollowuser="followTab(userData.id,'T','followed')" ng-if="userFollowing.indexOf(userData.id) == -1">
								<i class="fa fa-plus" aria-hidden="true"></i> Follow
							</a>
							<a allfollowuser="followTab(userData.id,'T','followed')" ng-if="userFollowing.indexOf(userData.id) != -1" class="ico">Following</a>
						</li>
					</ul>
					<ul class="followMessage" ng-if="::(userData.id != user.id && user.guest!=0)">
						<li>
							<a ng-click="redirecToLogin()">
								<i class="fa fa-plus" aria-hidden="true"></i> Follow
							</a>
						</li>
					</ul>
				</div>
				<div class="boxarea" ng-style="">
					<span class="profileTtl">
						<a href="#">{{::userData.first_name + ' ' + userData.last_name}}<span ng-show="::userData.dob"></span></a>
					</span>
					<span class="uoccupation">{{::userData.occupation }}</span>
					<span class="profileSmTtl">
						<span ng-if="::(userData.dob != '0000-00-00')">{{::(userData.dob | ageFilter)}}</span>
						<span ng-if="::userData.state.name">
							, <i class="fa fa-map-marker" aria-hidden="true" ng-if="::userData.state_id"></i> {{::userData.state.name}}
						</span>
					</span>
					<span class="profileSmTtl">
						{{::userData.about_me}}
					</span>
					<div class="userOwnLinksBx" ng-if="userData.website.length > 0 || userData.profile_facebook.length > 0 || userData.profile_twitter.length > 0">
						<div class="userOwnLinksBxCont">
							<div class="userOwnLinksArea">
								<div class="userOwnLinks">
									<div class="uLink" ng-if="userData.website.length > 0">
										<a href="{{::userData.website}}" target="_blank">
											<i class="fa fa-globe"></i>
											{{::userData.website}}
										</a>
									</div>
									<div class="uLink" ng-if="userData.profile_facebook.length > 0">
                                        <a href="https://www.facebook.com/{{::userData.profile_facebook}}" target="_blank">
											<i class="fa fa-facebook"></i>
											{{::userData.profile_facebook}}
										</a>
									</div>
									<div class="uLink" ng-if="userData.profile_twitter.length > 0">
                                        <a href="https://twitter.com/{{::userData.profile_twitter}}" target="_blank">
											<i class="fa fa-twitter"></i>
											{{::userData.profile_twitter}}
										</a>
									</div>
								</div>
							</div>
							<div class="usrLinkArrowCont">
								<span class="usrLinkArrow"></span>
							</div>
						</div>
					</div>
				</div>
			</div>
		</div>
	</div>
	<!-- FOR DESKTOP VIEW -->
	<div class="container-fluid profileContainer profilePG" style="padding-top:0 !important;">
		<!-- START ROW -->
		<div class="row">
			<div class="col-md-12 col-lg-12" >
				<div class="profileNavSlider hide640">
					<div class="profileNav">
						<div class="smallLoader"></div>
						<div id="owl-demo5" class="owl-carousel">
							<div class="item stopClickArea">
								<div class="stopClick" data-toggle="tooltip" ui-jq="tooltip" data-original-title="{{userDataProfileViews | thousandSuffix}}"></div>
								<a href="javascript:void(0);">
									<strong>{{userDataProfileViews | thousandSuffix : 2}}</strong>Profile Views
								</a>
							</div>
							<div class="item stopClickArea">
								<div class="stopClick" data-toggle="tooltip" ui-jq="tooltip" data-original-title="{{userData.points>=0 ? userData.points : 0 }}"></div>
								<a href="javascript:void(0);">
									<strong>{{userData.points>=0 ? userData.points : 0 | thousandSuffix : 2}}</strong>Points
								</a>
							</div>
							<div class="item">
								<a id="followerTab" ng-click="openTab('follower',userData.id)" data-toggle="tooltip" ui-jq="tooltip" data-original-title="{{userData.userDataTotalFollower | thousandSuffix}}"><strong>{{userData.userDataTotalFollower | thousandSuffix : 2}}</strong> Followers</a>
							</div>
							<div class="item">
								<a ng-click="openTab('following',userData.id)"  data-toggle="tooltip" ui-jq="tooltip" data-original-title="{{userData.userDataTotalFollowing | thousandSuffix }}"><strong>{{userData.userDataTotalFollowing | thousandSuffix : 2 }}</strong> Following</a>
							</div>
							<div class="item">
								<a class="active" id="postView" ng-click="openTab('post',userData.id)"  data-toggle="tooltip" ui-jq="tooltip" data-original-title="{{userDataTotalPost | thousandSuffix }}"><strong>{{userDataTotalPost | thousandSuffix : 2 }}</strong> Posts</a>
							</div>
						</div>
						<div ng-init="initListingTypeCarousel()"></div>
					</div>
				</div>

				<!-- FOR MOBILE VIEW -->
				<div style="overflow:hidden;">
					<div class="profileNav profileNavCatgMobile flkMobProfnav">
						<div class="smallLoader"></div>
						<div class="flickitySliderST1" style="display: none;">
							<div class="item"
								 ng-class="{'stopClickArea':navItem.name=='Profile Views' || navItem.name=='Points'}"
								 ng-repeat="navItem in profileNavItems"
								 flickity-item
							>
								<div ng-if="navItem.name=='Posts'">
									<a class="active" ng-click="openTab('post',userData.id)" ><strong>{{userDataTotalPost | thousandSuffix}}</strong>
										{{::navItem.name}}</a>
								</div>
								<div ng-if="navItem.name=='Followers'">
									<a ng-click="openTab('follower',userData.id)" ><strong>{{userData.userDataTotalFollower | thousandSuffix }}</strong> {{::navItem.name}}</a>
								</div>
								<div ng-if="navItem.name=='Following'">
									<a ng-click="openTab('following',userData.id)" >
										<strong>{{userData.userDataTotalFollowing | thousandSuffix }}</strong> {{::navItem.name}}</a>
								</div>
								<div ng-if="navItem.name=='Profile Views'">
									<div class="stopClick"></div>
									<a><strong>{{userDataProfileViews | thousandSuffix}}</strong>{{::navItem.name}}</a>
								</div>
								<div ng-if="navItem.name=='Points'">
									<div class="stopClick"></div>
									<a>
										<strong ng-if="userData.points >=0">{{userData.points | thousandSuffix}}</strong>
										<strong ng-if="user.points< 0">0</strong>{{::navItem.name}}
									</a>
								</div>
							</div>
						</div>
					</div>
				</div>

			</div>
		</div>

		<div style="display:block; position:relative;">
			<div class="loaderImage"></div>

			<!--  Tab container   -->
			<div ng-include="currentTab" onload="removeFeatureLoader()"></div>

			<!-- SHARE Modal -->
			<sharepost-card></sharepost-card>
			<!-- Details Modal-->
			<postcard-modal></postcard-modal>
		</div>
	</div>

	<!-- DELETE POST CARD MODAL -->

	<delete-post-modal></delete-post-modal>

	<!-- REPORT POST CARD MODAL -->
	<report-post-modal></report-post-modal>
	<!-- PROMPT SINGIN BOX -->
	<prompt-signin-box></prompt-signin-box>
	<!-- REPORT COMMENT MODAL -->
</div>