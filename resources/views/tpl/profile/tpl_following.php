<div class="whiteAreaCont"> 
	 <div class="row">
		<div class="col-md-10 col-lg-10 col-md-offset-1">
			<div class="followingContainer">
				<div class="postingformtype">
					<div class="followingBody" id="peopleTabVW">
						<div class="profileCommentBox followingFollower" ng-if="following.length > 0">
							<div class="profileCommentBoxTop">
								<div class="followingRow" ng-repeat="fi in following | orderBy:'-id'" >
									<div class="followingRowImg" ng-if="fi.following_by.thumb_image_url">
										<a href="#" style="background:url({{fi.following_by.thumb_image_url}}) no-repeat;"></a>
									</div>
									<div class="followingRowImg {{fi.following_by.user_color}}" ng-if="!fi.following_by.thumb_image_url">
										<a href="#">
											<span class="txt">{{fi.following_by.first_name.charAt(0)}}</span>
										</a>
									</div>
									<div class="followingRowInfo">
										<div class="followingRowInfoMiddle">
											<div class="followingRowInfoMiddleTop">
												<span class="followingRowInfoTtl"><a ui-sref="account({ username: fi.following_by.username })">{{fi.following_by.first_name+' '+fi.following_by.last_name}}</a></span>
												<p>{{fi.following_by.about_me}}</p>
												<label class="followBtn" ng-if="fi.user_id!=user.id && user.guest== 0">   
													<a href="#" class="followUser" allfollowuser="followUserFromFollowingTab(fi.user_id,'F','followed')" 
													ng-if="checkFollowing(fi.user_id)==false"><span>FOLLOW</span></a>

													<a href="#" allfollowuser="followUserFromFollowingTab(fi.user_id,'F','followed')" 
												ng-if="checkFollowing(fi.user_id)==true"><span class="ico">FOLLOWING</span></a>
												</label>
												<label class="followBtn" ng-if="fi.user_id!=user.id && user.guest!= 0">   
													<a href="#" class="followUser" ng-click="redirecToLogin();"><span>FOLLOW</span></a>
												</label> 
																			
											</div>
										</div>
										<div class="clearfix"></div>
									</div>
									<div class="clearfix"></div>
								</div>
							</div>
						</div>

						<h2 style="text-align:center;" ng-if="following.length == 0">You are following no one</h2>
					</div>
				</div>
			</div>
		</div>
	</div>
</div>