<div class="whiteAreaCont"> 
	<div class="row">
		<div class="col-md-10 col-lg-10 col-md-offset-1">
			<div class="followingContainer">
				<div class="postingformtype">
					<!-- <div class="followingHeader">
						<ul>
							<li><a href="#" class="active">People</a></li>
							<li><a href="#">Categories</a></li>
						</ul>
					</div> -->

					<div class="followingBody">
						<div class="profileCommentBox followingFollower" ng-if="follower.length > 0">
							<div class="profileCommentBoxTop">
								<div class="followingRow" ng-repeat="fd in follower | orderBy:'-id'">
									<div class="followingRowImg" ng-if="fd.followed_by.thumb_image_url">
										<a href="#" style="background:url({{fd.followed_by.thumb_image_url}}) no-repeat;"></a>
									</div>
									<div class="followingRowImg {{fd.followed_by.user_color}}" ng-if="!fd.followed_by.thumb_image_url">
										<a href="#">
											<span class="txt">{{fd.followed_by.first_name.charAt(0)}}</span>
										</a>
									</div>
									<div class="followingRowInfo">
										<div class="followingRowInfoMiddle">
											<div class="followingRowInfoMiddleTop">
												<span class="followingRowInfoTtl"><a ui-sref="account({ username: fd.followed_by.username })">{{fd.followed_by.first_name+' '+fd.followed_by.last_name}}</a></span>
												<p>{{fd.followed_by.about_me}}</p>
												<label class="followBtn" ng-if="fd.follower_id!=user.id  && user.guest== 0">   
													<a href="#" class="followUser" allfollowuser="followUserFromFollowerTab(fd.follower_id,'F','following')" 
													ng-if="checkFollowing(fd.follower_id)==false"><span>FOLLOW </span></a>
													<a href="#" allfollowuser="followUserFromFollowerTab(fd.follower_id,'F','following')" 
													ng-if="checkFollowing(fd.follower_id)==true"><span class="ico">FOLLOWING</span></a> 
												</label>
												<label class="followBtn" ng-if="fd.follower_id!=user.id  && user.guest!= 0">   
													<a href="#" class="followUser" ng-click="redirecToLogin();">
														<span>FOLLOW </span>
													</a>
												</label>
												
											</div>
										</div>
										<div class="clearfix"></div>
									</div>
									<div class="clearfix"></div>
								</div>
							</div>
						</div>
						<h2 style="text-align:center;" ng-if="follower.length == 0">No one follows you</h2>
					</div>
				</div>
			</div>
		</div>
	</div>
</div>