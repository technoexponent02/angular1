<style type="text/css">
	body{background:#f6f9fa !important;}	
	.header{background:#fff !important;}
</style>
<div class="container-fluid padding-25 sm-padding-10" style="padding-top:0px !important;">	
	<div class="panel newpanel m-t-10">	
		<div class="analyticsPage">
			<div class="categoryHeadingRow lg_tl">
				<h5>Analytics Dashboard</h5>
			</div>				
			<div class="analyticsContainer" style="">
				<div class="rw">
					<div class="colM">
						<div class="analyticsBox">
							<div class="numberArea">
								<span class="number">
									{{totalOnlineUsers}}
									<span class="des">
										<span class="online">
											<i class="fa fa-circle"></i> Online Users
										</span>
									</span>
								</span>
								<!-- <span class="stArea">
									<span class="st"><i class="fa fa-caret-up"></i> 3.1%</span>
								</span> -->
							</div>
							<div class="analyticsTxt">
								<!-- <a href="javascript:void(0);" class="edt"><img src="assets/pages/img/key1.png" alt=""/></a>-->
								<h2 class="tl"><a href="javascript:void(0);">REAL-TIME</a></h2>
								<p>The total amount of online unique users who currently viewing your posts.</p>
							</div>
						</div>
					</div>
					<div class="colM">
						<div class="analyticsBox">
							<div class="numberArea">
								<div class="membersBtns">
									<div class="btn-group btn-group-xs">
                                        <button type="button" class="btn btn-default" ng-class="{'active':allAnalyticCategory.points=='daily'}" ng-click="getAnalyticsReport('points', 'daily');">Day</button>
                                        <button type="button" class="btn btn-default" ng-class="{'active':allAnalyticCategory.points=='weekly'}" ng-click="getAnalyticsReport('points', 'weekly');">Week</button>
                                        <button type="button" class="btn btn-default" ng-class="{'active':allAnalyticCategory.points=='monthly'}" ng-click="getAnalyticsReport('points', 'monthly');">Month</button>
                                    </div>
								</div>
								<span class="number">{{pointLastDay}}<span class="des">Points</span></span>
								 <span class="stArea">
									<span class="no">{{pointDifference >= 0 ? '+' + pointDifference : pointDifference}}</span>
									<span class="st" ng-class="{'down':pointPercentageDifference < 0, 'mid':pointPercentageDifference == 0}">
										<i ng-if="pointPercentageDifference > 0" class="fa fa-caret-up"></i>
										<i ng-if="pointPercentageDifference < 0" class="fa fa-caret-down"></i> {{pointPercentageDifference >= 0 ? pointPercentageDifference : 0-pointPercentageDifference}}%
									</span>
								</span> <!---->
							</div>
							<div class="analyticsTxt">
								<!-- <a href="javascript:void(0);" class="edt"><img src="assets/pages/img/key1.png" alt=""/></a>-->
								<h2 class="tl"><a href="javascript:void(0);">{{allAnalyticCategory.points.toUpperCase()}}</a></h2>
								<p>The total amount of points receive from posts and comments.</p>
							</div>
						</div>
					</div>
					<div class="colM">
						<div class="analyticsBox">
							<div class="numberArea">
								<div class="membersBtns">
									<div class="btn-group btn-group-xs">
                                        <button type="button" class="btn btn-default" ng-class="{'active':allAnalyticCategory.visitors=='daily'}" ng-click="getAnalyticsReport('visitors', 'daily');">Day</button>
                                        <button type="button" class="btn btn-default" ng-class="{'active':allAnalyticCategory.visitors=='weekly'}" ng-click="getAnalyticsReport('visitors', 'weekly');">Week</button>
                                        <button type="button" class="btn btn-default" ng-class="{'active':allAnalyticCategory.visitors=='monthly'}" ng-click="getAnalyticsReport('visitors', 'monthly');">Month</button>
                                    </div>
								</div>
								<span class="number">{{visitorInLastDay}}<span class="des">Visitors</span></span>
								<span class="stArea">
									<span class="no">{{visitorDifference >= 0 ? '+' + visitorDifference : visitorDifference}}</span>
									<span class="st" ng-class="{'down':visiotrPercentageDifference < 0, 'mid':visiotrPercentageDifference == 0}">
										<i ng-if="visiotrPercentageDifference > 0" class="fa fa-caret-up"></i>
										<i ng-if="visiotrPercentageDifference < 0" class="fa fa-caret-down"></i> {{visiotrPercentageDifference >= 0 ? visiotrPercentageDifference : 0-visiotrPercentageDifference}}%
									</span>
								</span>
							</div>
							<div class="analyticsTxt">
								<!-- <a href="javascript:void(0);" class="edt"><img src="assets/pages/img/key1.png" alt=""/></a>-->
								<h2 class="tl"><a href="javascript:void(0);">{{allAnalyticCategory.visitors.toUpperCase()}}</a></h2>
								<p>The total amount of visitors from all your posts.</p>
							</div>
						</div>
					</div>
					
					<div class="colM">
						<div class="analyticsBox">
							<div class="numberArea">
								<div class="membersBtns">
									<div class="btn-group btn-group-xs">
                                        <button type="button" class="btn btn-default" ng-class="{'active':allAnalyticCategory.postviews=='daily'}" ng-click="getAnalyticsReport('postviews', 'daily');">Day</button>
                                        <button type="button" class="btn btn-default" ng-class="{'active':allAnalyticCategory.postviews=='weekly'}" ng-click="getAnalyticsReport('postviews', 'weekly');">Week</button>
                                        <button type="button" class="btn btn-default" ng-class="{'active':allAnalyticCategory.postviews=='monthly'}" ng-click="getAnalyticsReport('postviews', 'monthly');">Month</button>
                                    </div>
								</div>
								<span class="number">{{postviewInLastDay}}<span class="des">Post Click</span></span>
								<span class="stArea">
									<span class="no">{{postviewDifference >= 0 ? '+' + postviewDifference :  postviewDifference}}</span>
									<span class="st" ng-class="{'down':postviewPercentageDifference < 0, 'mid':postviewPercentageDifference == 0}">
										<i class="fa fa-caret-up" ng-if="postviewPercentageDifference > 0"></i>
										<i class="fa fa-caret-down" ng-if="postviewPercentageDifference < 0"></i> 
										{{postviewPercentageDifference >= 0 ? postviewPercentageDifference : 0-postviewPercentageDifference}}%
									</span>
								</span>
							</div>
							<div class="analyticsTxt">
								<!-- <a href="javascript:void(0);" class="edt"><img src="assets/pages/img/key1.png" alt=""/></a>-->
								<h2 class="tl"><a href="javascript:void(0);">{{allAnalyticCategory.postviews.toUpperCase()}}</a></h2>
								<p>The total amount of post views generated from visitors.</p>
							</div>
						</div>
					</div>
					<div class="colM">
						<div class="analyticsBox">
							<div class="numberArea">
								<div class="membersBtns">
									<div class="btn-group btn-group-xs">
										<button type="button" class="btn btn-default" ng-class="{'active':allAnalyticCategory.engagementscore=='daily'}" ng-click="getAnalyticsReport('engagementscore', 'daily');">Day</button>
										<button type="button" class="btn btn-default" ng-class="{'active':allAnalyticCategory.engagementscore=='weekly'}" ng-click="getAnalyticsReport('engagementscore', 'weekly');">Week</button>
										<button type="button" class="btn btn-default" ng-class="{'active':allAnalyticCategory.engagementscore=='monthly'}" ng-click="getAnalyticsReport('engagementscore', 'monthly');">Month</button>
									</div>
								</div>
								<span class="number">{{ engagementScoreInLastDay }}<span class="des">Engagement Score</span></span>
								<span class="stArea">
									<span class="no">{{engagementScoreDifference >= 0 ? '+' + engagementScoreDifference :  engagementScoreDifference}}</span>
									<span class="st" ng-class="{'down':engagementScorePercentageDifference < 0, 'mid':engagementScorePercentageDifference == 0}">
										<i class="fa fa-caret-up" ng-if="engagementScorePercentageDifference > 0"></i>
										<i class="fa fa-caret-down" ng-if="engagementScorePercentageDifference < 0"></i>
										{{engagementScorePercentageDifference >= 0 ? engagementScorePercentageDifference : 0-engagementScorePercentageDifference}}%
									</span>
								</span>
							</div>
							<div class="analyticsTxt">
								<h2 class="tl"><a href="javascript:void(0);">{{allAnalyticCategory.engagementscore.toUpperCase()}}</a></h2>
								<p>Based on your post activities performed by others.</p>
							</div>
						</div>
					</div>
					<div class="colM">
						<div class="analyticsBox">
							<div class="numberArea">
								<div class="membersBtns">
									<div class="btn-group btn-group-xs">
                                        <button type="button" class="btn btn-default" ng-class="{'active':allAnalyticCategory.profileviews=='daily'}" ng-click="getAnalyticsReport('profileviews', 'daily');">Day</button>
                                        <button type="button" class="btn btn-default" ng-class="{'active':allAnalyticCategory.profileviews=='weekly'}" ng-click="getAnalyticsReport('profileviews', 'weekly');">Week</button>
                                        <button type="button" class="btn btn-default" ng-class="{'active':allAnalyticCategory.profileviews=='monthly'}" ng-click="getAnalyticsReport('profileviews', 'monthly');">Month</button>
                                    </div>
								</div>
								<span class="number">{{profileviewInLastDay}}<span class="des">Profile Views</span></span>
								<span class="stArea">
									<span class="no">{{profileviewDifference >= 0 ? '+' + profileviewDifference : profileviewDifference}}</span>
									<span class="st" ng-class="{'down':profileviewPercentageDifference < 0, 'mid':profileviewPercentageDifference == 0}">
										<i class="fa fa-caret-up" ng-if="profileviewPercentageDifference > 0"></i>
										<i class="fa fa-caret-down" ng-if="profileviewPercentageDifference < 0"></i> 
										{{profileviewPercentageDifference >= 0 ? profileviewPercentageDifference : 0-profileviewPercentageDifference}}%
									</span>
								</span>
							</div>
							<div class="analyticsTxt">
								<!-- <a href="javascript:void(0);" class="edt"><img src="assets/pages/img/key1.png" alt=""/></a>-->
								<h2 class="tl"><a href="javascript:void(0);">{{allAnalyticCategory.profileviews.toUpperCase()}}</a></h2>
								<p>The amount of users view your profile page.</p>
							</div>
						</div>
					</div>
					<div class="colM">
						<div class="analyticsBox">
							<div class="numberArea">
								<div class="membersBtns">
									<div class="btn-group btn-group-xs">
                                        <button type="button" class="btn btn-default" ng-class="{'active':allAnalyticCategory.upvote=='daily'}" ng-click="getAnalyticsReport('upvote', 'daily');">Day</button>
                                        <button type="button" class="btn btn-default" ng-class="{'active':allAnalyticCategory.upvote=='weekly'}" ng-click="getAnalyticsReport('upvote', 'weekly');">Week</button>
                                        <button type="button" class="btn btn-default" ng-class="{'active':allAnalyticCategory.upvote=='monthly'}" ng-click="getAnalyticsReport('upvote', 'monthly');">Month</button>
                                    </div>
								</div>
								<span class="number">{{upvote.upvoteInLastDay}}<span class="des">Upvotes</span></span>
								<span class="stArea">
									<span class="no">{{upvote.upvoteDifference >= 0 ? '+' + upvote.upvoteDifference : upvote.upvoteDifference}}</span>
									<span class="st" ng-class="{'down':upvote.upvotePercentageDifference < 0, 'mid':upvote.upvotePercentageDifference == 0}">
										<i class="fa fa-caret-up" ng-if="upvote.upvotePercentageDifference > 0"></i>
										<i class="fa fa-caret-down" ng-if="upvote.upvotePercentageDifference < 0"></i> 
										{{upvote.upvotePercentageDifference >= 0 ? upvote.upvotePercentageDifference : 0-upvote.upvotePercentageDifference}}%
									</span>
								</span>
							</div>
							<div class="analyticsTxt">
								<!-- <a href="javascript:void(0);" class="edt"><img src="assets/pages/img/key1.png" alt=""/></a>-->
								<h2 class="tl"><a href="javascript:void(0);">{{allAnalyticCategory.upvote.toUpperCase()}}</a></h2>
								<p>The amount of upvotes receive from posts and comments.</p>
							</div>
						</div>
					</div>
					<div class="colM">
						<div class="analyticsBox">
							<div class="numberArea">
								<div class="membersBtns">
									<div class="btn-group btn-group-xs">
                                        <button type="button" class="btn btn-default" ng-class="{'active':allAnalyticCategory.downvote=='daily'}" ng-click="getAnalyticsReport('downvote', 'daily');">Day</button>
                                        <button type="button" class="btn btn-default" ng-class="{'active':allAnalyticCategory.downvote=='weekly'}" ng-click="getAnalyticsReport('downvote', 'weekly');">Week</button>
                                        <button type="button" class="btn btn-default" ng-class="{'active':allAnalyticCategory.downvote=='monthly'}" ng-click="getAnalyticsReport('downvote', 'monthly');">Month</button>
                                    </div>
								</div>
								<span class="number">{{downvote.downvoteInLastDay}}<span class="des">Downvotes</span></span>
								<span class="stArea">
									<span class="no">{{downvote.downvoteDifference >= 0 ? '+' + downvote.downvoteDifference : downvote.downvoteDifference}}</span>
									<span class="st" ng-class="{'down':downvote.downvotePercentageDifference > 0,'mid':downvote.downvotePercentageDifference == 0}">
										<i class="fa fa-caret-up" ng-if="downvote.downvotePercentageDifference > 0"></i>
										<i class="fa fa-caret-down" ng-if="downvote.downvotePercentageDifference < 0"></i> 
										{{downvote.downvotePercentageDifference >= 0 ? downvote.downvotePercentageDifference : 0-downvote.downvotePercentageDifference}}%
									</span>
								</span>
							</div>
							<div class="analyticsTxt">
								<!-- <a href="javascript:void(0);" class="edt"><img src="assets/pages/img/key1.png" alt=""/></a>-->
								<h2 class="tl"><a href="javascript:void(0);">{{allAnalyticCategory.downvote.toUpperCase()}}</a></h2>
								<p>The amount of downvotes receive from posts and comments.</p>
							</div>
						</div>
					</div>

					

				</div>
			</div>
		</div>
	</div>
</div>
<style type="text/css">
.categoryHeadingRow{padding-bottom:12px;}
</style>
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