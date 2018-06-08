<div class="categoryBanner" style="background:url(assets/pages/img/category-banner.jpg) no-repeat;">
	<div class="ovrlay">
		<div class="categoryBnrDesc">
			<h2>Category Name Here</h2>
			<div class="categoryBtns">
				<a href="#" class="followBtn wh">
					<span>FOLLOW</span>
				</a>
			</div>
		</div>
	</div>
</div>
<div class="container-fluid padding-25 sm-padding-10" style="padding-top:0px !important;">
	<div class="panel newpanel m-t-10">
		<!-- <div class="subheaderOuter">
			<div id="subheader"></div>
		</div> -->
		<div class="scrollTabOuter">
			<div class="scrollTab scrollTabCalc">
				<ul class="nav nav-tabs nav-tabs-simple innerTab inlineView">
					<li class="active">
						<a href="#" ng-click="template='recent'">Recent</a>
					</li>							
					<li>
						<a href="#" ng-click="template='trending'">Trending</a>
					</li>
					<li>
						<a href="#" ng-click="template='popular'">Popular</a>
					</li>
					<li>
						<a href="#" ng-click="template='topchannel'">Top Channel</a>
					</li>
				</ul>
			</div>			
		</div>
		
		<div style="display:block; position:relative;">		
			<div class="loaderImage"></div> 
			
			
			<ng-include src="template"></ng-include>
			
			
			<script type="text/ng-template" id="recent" >
				<div class="row">
					<div class="col-md-12 col-lg-12">
						<div style="display:block;">
							<div style="display:block;">
								<div style="display:block;">
								</div>
								<div class="blockContentRow inline">

									<div class="blockContentRow inline">

										<div class="blockContent ng-scope">

											<div class="profileCommentBox clkProfileCommentBox">

												<div class="cardBoxClk"></div>

												<div class="profileCommentBoxTop">

													<div class="userStatusRow">

														<div class="userStatusImage">

															<a href="/profile/souravdey">

																<img ng-src="http://swolk.com/assets/img/default-profile.png" alt="profile image"/>

															</a>

														</div>



														<div class="userStatusInfo">

															<span class="userStatusInfoTtl withLocation clearfix">

																<a" class="ng-binding" href="/profile/souravdey">Sourav Dey</a>

																<a href="#" class="follwBtn">

																	<i class="fa fa-plus-circle" aria-hidden="true"></i>

																</a>

																<!-- ngIf: post.location -->

															</span>

															<p>

																<span class="clearfix">

																	<small class="ng-binding">&nbsp;Web Developer</small>

																</span>

																<span class="block clearfix"></span>

																<small class="ng-binding">Shared this post 2 hours ago</small>

															</p>

														</div>

														<div class="clearfix"></div>

													</div>

													

													<div class="post_type">

														<p ng-show="post.caption" class="ng-binding ng-hide"></p>

														<div class="catagoryTtl">

															<div class="catagoryTagRow">

																<a href="#" class="catagoryTtlHighLightBlack ng-binding">Business</a>

																<a href="#" class="catagoryTtlHighLight ng-binding">Marketing</a>

															</div>

															<p class="ng-binding">We Need Fun</p>

														</div>

														<div class="postShortDesc ng-hide" ng-show="post.short_description">

															<p ng-show="post.short_description" class="ng-binding ng-hide"></p>

														</div>

														<div>

															<p class="postLink ng-scope">

																<span>

																	<a href="" target="_blank" class="ng-binding">

																		<i class="fa fa-external-link"></i>

																		weneedfun.com

																	</a>

																</span>                        

															</p><!-- end ngIf: post.source -->

															<div class="uploadImage">

																<a>

																	<img ng-src="http://i2.cdn.turner.com/cnn/dam/assets/111114110921-todays-reading-list-c1-main.jpg" />

																</a>

															</div>

														</div>

														<div class="profileNewCoverBottomLink ng-scope" ng-if="post.tags">

															<ul>

																<!-- ngRepeat: tag in post.tags -->

															</ul>

														</div><!-- end ngIf: post.tags -->

													</div>

													

													<div class="profileCommentFooter addNew noBorder">

														<div class="likeCommentBar">

															<div class="left odd">

																<a href="javascript:void(0)">

																	<span id="postUpvotes119" class="ng-binding">0</span>

																	<span class="ng-binding">&nbsp;upvote</span>

																</a>

															</div>

															<div class="left odd">

																<a href="#">

																	<span id="postCommentFor_119" class="ng-binding">0</span>

																	<span class="ng-binding">&nbsp;comment</span>

																</a>

															</div>

															<div class="left odd">

																<a href="#"><span>10</span><span>&nbsp;shares</span></a>

															</div>

															<div class="right odd">

																<a href="#">

																	<span class="ng-binding">2</span>

																	<span class="ng-binding">&nbsp;viewed</span>

																</a>

															</div>

															<div class="clearfix"></div>

														</div>

														<div class="clearfix"></div>

													</div>

													<div class="profileCommentFooter addNew">

														<div class="left">

															<a href="#" class="upvoteIcon">

																<img src="http://swolk.com/assets/pages/img/arrow2_t.png" alt=""/>

															</a>



															<a href="#" class="">

																<img src="http://swolk.com/assets/pages/img/speech_bubble4.png" alt="">

															</a>

															<a href="#" class="ng-scope">

																<img src="http://swolk.com/assets/pages/img/refresh4.png" alt="">

															</a>

															<a href="#">

																<img src="http://swolk.com/assets/pages/img/bookmark.png" alt="">

															</a>

															<a href="#">

																<img src="http://swolk.com/assets/pages/img/log_out.png" alt="">

															</a>

														</div>

														<div class="right">

															<a href="#" class="moreBtnN">

																<i class="fa fa-circle" aria-hidden="true"></i>

																<i class="fa fa-circle" aria-hidden="true"></i>

																<i class="fa fa-circle" aria-hidden="true"></i>

															</a>

														</div>

														<div class="clearfix"></div>

													</div>

												</div>

											</div>

										</div>

										<div class="blockContent ng-scope">

											<div class="profileCommentBox clkProfileCommentBox">

												<div class="cardBoxClk"></div>

												<div class="profileCommentBoxTop">

													<div class="userStatusRow">

														<div class="userStatusImage">

															<a href="/profile/souravdey">

																<img ng-src="http://swolk.com/assets/img/default-profile.png" alt="profile image"/>

															</a>

														</div>



														<div class="userStatusInfo">

															<span class="userStatusInfoTtl withLocation clearfix">

																<a" class="ng-binding" href="/profile/souravdey">Sourav Dey</a>

																<a href="#" class="follwBtn">

																	<i class="fa fa-plus-circle" aria-hidden="true"></i>

																</a>

																<!-- ngIf: post.location -->

															</span>

															<p>

																<span class="clearfix">

																	<small class="ng-binding">&nbsp;Web Developer</small>

																</span>

																<span class="block clearfix"></span>

																<small class="ng-binding">Shared this post 2 hours ago</small>

															</p>

														</div>

														<div class="clearfix"></div>

													</div>

													

													<div class="post_type">

														<p ng-show="post.caption" class="ng-binding ng-hide"></p>

														<div class="catagoryTtl">

															<div class="catagoryTagRow">

																<a href="#" class="catagoryTtlHighLightBlack ng-binding">Business</a>

																<a href="#" class="catagoryTtlHighLight ng-binding">Marketing</a>

															</div>

															<p class="ng-binding">We Need Fun</p>

														</div>

														<div class="postShortDesc ng-hide" ng-show="post.short_description">

															<p ng-show="post.short_description" class="ng-binding ng-hide"></p>

														</div>

														<div>

															<p class="postLink ng-scope">

																<span>

																	<a href="" target="_blank" class="ng-binding">

																		<i class="fa fa-external-link"></i>

																		weneedfun.com

																	</a>

																</span>                        

															</p><!-- end ngIf: post.source -->

															<div class="uploadImage">

																<a>

																	<img ng-src="https://cdn2.omidoo.com/sites/default/files/imagecache/full_width/images/bydate/201407/shutterstock127655501.jpg" />

																</a>

															</div>

														</div>

														<div class="profileNewCoverBottomLink ng-scope" ng-if="post.tags">

															<ul>

																<!-- ngRepeat: tag in post.tags -->

															</ul>

														</div><!-- end ngIf: post.tags -->

													</div>

													

													<div class="profileCommentFooter addNew noBorder">

														<div class="likeCommentBar">

															<div class="left odd">

																<a href="javascript:void(0)">

																	<span id="postUpvotes119" class="ng-binding">0</span>

																	<span class="ng-binding">&nbsp;upvote</span>

																</a>

															</div>

															<div class="left odd">

																<a href="#">

																	<span id="postCommentFor_119" class="ng-binding">0</span>

																	<span class="ng-binding">&nbsp;comment</span>

																</a>

															</div>

															<div class="left odd">

																<a href="#"><span>10</span><span>&nbsp;shares</span></a>

															</div>

															<div class="right odd">

																<a href="#">

																	<span class="ng-binding">2</span>

																	<span class="ng-binding">&nbsp;viewed</span>

																</a>

															</div>

															<div class="clearfix"></div>

														</div>

														<div class="clearfix"></div>

													</div>

													<div class="profileCommentFooter addNew">

														<div class="left">

															<a href="#" class="upvoteIcon">

																<img src="http://swolk.com/assets/pages/img/arrow2_t.png" alt=""/>

															</a>



															<a href="#" class="">

																<img src="http://swolk.com/assets/pages/img/speech_bubble4.png" alt="">

															</a>

															<a href="#" class="ng-scope">

																<img src="http://swolk.com/assets/pages/img/refresh4.png" alt="">

															</a>

															<a href="#">

																<img src="http://swolk.com/assets/pages/img/bookmark.png" alt="">

															</a>

															<a href="#">

																<img src="http://swolk.com/assets/pages/img/log_out.png" alt="">

															</a>

														</div>

														<div class="right">

															<a href="#" class="moreBtnN">

																<i class="fa fa-circle" aria-hidden="true"></i>

																<i class="fa fa-circle" aria-hidden="true"></i>

																<i class="fa fa-circle" aria-hidden="true"></i>

															</a>

														</div>

														<div class="clearfix"></div>

													</div>

												</div>

											</div>

										</div>
										<div class="blockContent ng-scope">

											<div class="profileCommentBox clkProfileCommentBox">

												<div class="cardBoxClk"></div>

												<div class="profileCommentBoxTop">

													<div class="userStatusRow">

														<div class="userStatusImage">

															<a href="/profile/souravdey">

																<img ng-src="http://swolk.com/assets/img/default-profile.png" alt="profile image"/>

															</a>

														</div>



														<div class="userStatusInfo">

															<span class="userStatusInfoTtl withLocation clearfix">

																<a" class="ng-binding" href="/profile/souravdey">Sourav Dey</a>

																<a href="#" class="follwBtn">

																	<i class="fa fa-plus-circle" aria-hidden="true"></i>

																</a>

																<!-- ngIf: post.location -->

															</span>

															<p>

																<span class="clearfix">

																	<small class="ng-binding">&nbsp;Web Developer</small>

																</span>

																<span class="block clearfix"></span>

																<small class="ng-binding">Shared this post 2 hours ago</small>

															</p>

														</div>

														<div class="clearfix"></div>

													</div>

													

													<div class="post_type">

														<p ng-show="post.caption" class="ng-binding ng-hide"></p>

														<div class="catagoryTtl">

															<div class="catagoryTagRow">

																<a href="#" class="catagoryTtlHighLightBlack ng-binding">Business</a>

																<a href="#" class="catagoryTtlHighLight ng-binding">Marketing</a>

															</div>

															<p class="ng-binding">We Need Fun</p>

														</div>

														<div class="postShortDesc ng-hide" ng-show="post.short_description">

															<p ng-show="post.short_description" class="ng-binding ng-hide"></p>

														</div>

														<div>

															<p class="postLink ng-scope">

																<span>

																	<a href="" target="_blank" class="ng-binding">

																		<i class="fa fa-external-link"></i>

																		weneedfun.com

																	</a>

																</span>                        

															</p><!-- end ngIf: post.source -->

															<div class="uploadImage">

																<a>

																	<img ng-src="http://www.thesitsgirls.com/wp-content/uploads/2011/09/P1000931.png.png" />

																</a>

															</div>

														</div>

														<div class="profileNewCoverBottomLink ng-scope" ng-if="post.tags">

															<ul>

																<!-- ngRepeat: tag in post.tags -->

															</ul>

														</div><!-- end ngIf: post.tags -->

													</div>

													

													<div class="profileCommentFooter addNew noBorder">

														<div class="likeCommentBar">

															<div class="left odd">

																<a href="javascript:void(0)">

																	<span id="postUpvotes119" class="ng-binding">0</span>

																	<span class="ng-binding">&nbsp;upvote</span>

																</a>

															</div>

															<div class="left odd">

																<a href="#">

																	<span id="postCommentFor_119" class="ng-binding">0</span>

																	<span class="ng-binding">&nbsp;comment</span>

																</a>

															</div>

															<div class="left odd">

																<a href="#"><span>10</span><span>&nbsp;shares</span></a>

															</div>

															<div class="right odd">

																<a href="#">

																	<span class="ng-binding">2</span>

																	<span class="ng-binding">&nbsp;viewed</span>

																</a>

															</div>

															<div class="clearfix"></div>

														</div>

														<div class="clearfix"></div>

													</div>

													<div class="profileCommentFooter addNew">

														<div class="left">

															<a href="#" class="upvoteIcon">

																<img src="http://swolk.com/assets/pages/img/arrow2_t.png" alt=""/>

															</a>



															<a href="#" class="">

																<img src="http://swolk.com/assets/pages/img/speech_bubble4.png" alt="">

															</a>

															<a href="#" class="ng-scope">

																<img src="http://swolk.com/assets/pages/img/refresh4.png" alt="">

															</a>

															<a href="#">

																<img src="http://swolk.com/assets/pages/img/bookmark.png" alt="">

															</a>

															<a href="#">

																<img src="http://swolk.com/assets/pages/img/log_out.png" alt="">

															</a>

														</div>

														<div class="right">

															<a href="#" class="moreBtnN">

																<i class="fa fa-circle" aria-hidden="true"></i>

																<i class="fa fa-circle" aria-hidden="true"></i>

																<i class="fa fa-circle" aria-hidden="true"></i>

															</a>

														</div>

														<div class="clearfix"></div>

													</div>

												</div>

											</div>

										</div>
										<div class="blockContent ng-scope">

											<div class="profileCommentBox clkProfileCommentBox">

												<div class="cardBoxClk"></div>

												<div class="profileCommentBoxTop">

													<div class="userStatusRow">

														<div class="userStatusImage">

															<a href="/profile/souravdey">

																<img ng-src="http://swolk.com/assets/img/default-profile.png" alt="profile image"/>

															</a>

														</div>



														<div class="userStatusInfo">

															<span class="userStatusInfoTtl withLocation clearfix">

																<a" class="ng-binding" href="/profile/souravdey">Sourav Dey</a>

																<a href="#" class="follwBtn">

																	<i class="fa fa-plus-circle" aria-hidden="true"></i>

																</a>

																<!-- ngIf: post.location -->

															</span>

															<p>

																<span class="clearfix">

																	<small class="ng-binding">&nbsp;Web Developer</small>

																</span>

																<span class="block clearfix"></span>

																<small class="ng-binding">Shared this post 2 hours ago</small>

															</p>

														</div>

														<div class="clearfix"></div>

													</div>

													

													<div class="post_type">

														<p ng-show="post.caption" class="ng-binding ng-hide"></p>

														<div class="catagoryTtl">

															<div class="catagoryTagRow">

																<a href="#" class="catagoryTtlHighLightBlack ng-binding">Business</a>

																<a href="#" class="catagoryTtlHighLight ng-binding">Marketing</a>

															</div>

															<p class="ng-binding">We Need Fun</p>

														</div>

														<div class="postShortDesc ng-hide" ng-show="post.short_description">

															<p ng-show="post.short_description" class="ng-binding ng-hide"></p>

														</div>

														<div>

															<p class="postLink ng-scope">

																<span>

																	<a href="" target="_blank" class="ng-binding">

																		<i class="fa fa-external-link"></i>

																		weneedfun.com

																	</a>

																</span>                        

															</p><!-- end ngIf: post.source -->

															<div class="uploadImage">

																<a>

																	<img ng-src="http://2.bp.blogspot.com/-BjItYL_7ZHw/UpyXrHQ09YI/AAAAAAAAKVs/pqWEwx5Hh3w/s1600/Food+to+Eat.jpg" />

																</a>

															</div>

														</div>

														<div class="profileNewCoverBottomLink ng-scope" ng-if="post.tags">

															<ul>

																<!-- ngRepeat: tag in post.tags -->

															</ul>

														</div><!-- end ngIf: post.tags -->

													</div>

													

													<div class="profileCommentFooter addNew noBorder">

														<div class="likeCommentBar">

															<div class="left odd">

																<a href="javascript:void(0)">

																	<span id="postUpvotes119" class="ng-binding">0</span>

																	<span class="ng-binding">&nbsp;upvote</span>

																</a>

															</div>

															<div class="left odd">

																<a href="#">

																	<span id="postCommentFor_119" class="ng-binding">0</span>

																	<span class="ng-binding">&nbsp;comment</span>

																</a>

															</div>

															<div class="left odd">

																<a href="#"><span>10</span><span>&nbsp;shares</span></a>

															</div>

															<div class="right odd">

																<a href="#">

																	<span class="ng-binding">2</span>

																	<span class="ng-binding">&nbsp;viewed</span>

																</a>

															</div>

															<div class="clearfix"></div>

														</div>

														<div class="clearfix"></div>

													</div>

													<div class="profileCommentFooter addNew">

														<div class="left">

															<a href="#" class="upvoteIcon">

																<img src="http://swolk.com/assets/pages/img/arrow2_t.png" alt=""/>

															</a>



															<a href="#" class="">

																<img src="http://swolk.com/assets/pages/img/speech_bubble4.png" alt="">

															</a>

															<a href="#" class="ng-scope">

																<img src="http://swolk.com/assets/pages/img/refresh4.png" alt="">

															</a>

															<a href="#">

																<img src="http://swolk.com/assets/pages/img/bookmark.png" alt="">

															</a>

															<a href="#">

																<img src="http://swolk.com/assets/pages/img/log_out.png" alt="">

															</a>

														</div>

														<div class="right">

															<a href="#" class="moreBtnN">

																<i class="fa fa-circle" aria-hidden="true"></i>

																<i class="fa fa-circle" aria-hidden="true"></i>

																<i class="fa fa-circle" aria-hidden="true"></i>

															</a>

														</div>

														<div class="clearfix"></div>

													</div>

												</div>

											</div>

										</div>
										<div class="blockContent ng-scope">

											<div class="profileCommentBox clkProfileCommentBox">

												<div class="cardBoxClk"></div>

												<div class="profileCommentBoxTop">

													<div class="userStatusRow">

														<div class="userStatusImage">

															<a href="/profile/souravdey">

																<img ng-src="http://swolk.com/assets/img/default-profile.png" alt="profile image"/>

															</a>

														</div>



														<div class="userStatusInfo">

															<span class="userStatusInfoTtl withLocation clearfix">

																<a" class="ng-binding" href="/profile/souravdey">Sourav Dey</a>

																<a href="#" class="follwBtn">

																	<i class="fa fa-plus-circle" aria-hidden="true"></i>

																</a>

																<!-- ngIf: post.location -->

															</span>

															<p>

																<span class="clearfix">

																	<small class="ng-binding">&nbsp;Web Developer</small>

																</span>

																<span class="block clearfix"></span>

																<small class="ng-binding">Shared this post 2 hours ago</small>

															</p>

														</div>

														<div class="clearfix"></div>

													</div>

													

													<div class="post_type">

														<p ng-show="post.caption" class="ng-binding ng-hide"></p>

														<div class="catagoryTtl">

															<div class="catagoryTagRow">

																<a href="#" class="catagoryTtlHighLightBlack ng-binding">Business</a>

																<a href="#" class="catagoryTtlHighLight ng-binding">Marketing</a>

															</div>

															<p class="ng-binding">We Need Fun</p>

														</div>

														<div class="postShortDesc ng-hide" ng-show="post.short_description">

															<p ng-show="post.short_description" class="ng-binding ng-hide"></p>

														</div>

														<div>

															<p class="postLink ng-scope">

																<span>

																	<a href="" target="_blank" class="ng-binding">

																		<i class="fa fa-external-link"></i>

																		weneedfun.com

																	</a>

																</span>                        

															</p><!-- end ngIf: post.source -->

															<div class="uploadImage">

																<a>

																	<img ng-src="http://www.theonlinemom.com/wp-content/uploads/2014/06/food-photography.jpg" />

																</a>

															</div>

														</div>

														<div class="profileNewCoverBottomLink ng-scope" ng-if="post.tags">

															<ul>

																<!-- ngRepeat: tag in post.tags -->

															</ul>

														</div><!-- end ngIf: post.tags -->

													</div>

													

													<div class="profileCommentFooter addNew noBorder">

														<div class="likeCommentBar">

															<div class="left odd">

																<a href="javascript:void(0)">

																	<span id="postUpvotes119" class="ng-binding">0</span>

																	<span class="ng-binding">&nbsp;upvote</span>

																</a>

															</div>

															<div class="left odd">

																<a href="#">

																	<span id="postCommentFor_119" class="ng-binding">0</span>

																	<span class="ng-binding">&nbsp;comment</span>

																</a>

															</div>

															<div class="left odd">

																<a href="#"><span>10</span><span>&nbsp;shares</span></a>

															</div>

															<div class="right odd">

																<a href="#">

																	<span class="ng-binding">2</span>

																	<span class="ng-binding">&nbsp;viewed</span>

																</a>

															</div>

															<div class="clearfix"></div>

														</div>

														<div class="clearfix"></div>

													</div>

													<div class="profileCommentFooter addNew">

														<div class="left">

															<a href="#" class="upvoteIcon">

																<img src="http://swolk.com/assets/pages/img/arrow2_t.png" alt=""/>

															</a>



															<a href="#" class="">

																<img src="http://swolk.com/assets/pages/img/speech_bubble4.png" alt="">

															</a>

															<a href="#" class="ng-scope">

																<img src="http://swolk.com/assets/pages/img/refresh4.png" alt="">

															</a>

															<a href="#">

																<img src="http://swolk.com/assets/pages/img/bookmark.png" alt="">

															</a>

															<a href="#">

																<img src="http://swolk.com/assets/pages/img/log_out.png" alt="">

															</a>

														</div>

														<div class="right">

															<a href="#" class="moreBtnN">

																<i class="fa fa-circle" aria-hidden="true"></i>

																<i class="fa fa-circle" aria-hidden="true"></i>

																<i class="fa fa-circle" aria-hidden="true"></i>

															</a>

														</div>

														<div class="clearfix"></div>

													</div>

												</div>

											</div>

										</div>
										<div class="blockContent ng-scope">

											<div class="profileCommentBox clkProfileCommentBox">

												<div class="cardBoxClk"></div>

												<div class="profileCommentBoxTop">

													<div class="userStatusRow">

														<div class="userStatusImage">

															<a href="/profile/souravdey">

																<img ng-src="http://swolk.com/assets/img/default-profile.png" alt="profile image"/>

															</a>

														</div>



														<div class="userStatusInfo">

															<span class="userStatusInfoTtl withLocation clearfix">

																<a" class="ng-binding" href="/profile/souravdey">Sourav Dey</a>

																<a href="#" class="follwBtn">

																	<i class="fa fa-plus-circle" aria-hidden="true"></i>

																</a>

																<!-- ngIf: post.location -->

															</span>

															<p>

																<span class="clearfix">

																	<small class="ng-binding">&nbsp;Web Developer</small>

																</span>

																<span class="block clearfix"></span>

																<small class="ng-binding">Shared this post 2 hours ago</small>

															</p>

														</div>

														<div class="clearfix"></div>

													</div>

													

													<div class="post_type">

														<p ng-show="post.caption" class="ng-binding ng-hide"></p>

														<div class="catagoryTtl">

															<div class="catagoryTagRow">

																<a href="#" class="catagoryTtlHighLightBlack ng-binding">Business</a>

																<a href="#" class="catagoryTtlHighLight ng-binding">Marketing</a>

															</div>

															<p class="ng-binding">We Need Fun</p>

														</div>

														<div class="postShortDesc ng-hide" ng-show="post.short_description">

															<p ng-show="post.short_description" class="ng-binding ng-hide"></p>

														</div>

														<div>

															<p class="postLink ng-scope">

																<span>

																	<a href="" target="_blank" class="ng-binding">

																		<i class="fa fa-external-link"></i>

																		weneedfun.com

																	</a>

																</span>                        

															</p><!-- end ngIf: post.source -->

															<div class="uploadImage">

																<a>

																	<img ng-src="http://chefderecipe.com/wp-content/uploads/2016/02/foodpost-adds.jpg" />

																</a>

															</div>

														</div>

														<div class="profileNewCoverBottomLink ng-scope" ng-if="post.tags">

															<ul>

																<!-- ngRepeat: tag in post.tags -->

															</ul>

														</div><!-- end ngIf: post.tags -->

													</div>

													

													<div class="profileCommentFooter addNew noBorder">

														<div class="likeCommentBar">

															<div class="left odd">

																<a href="javascript:void(0)">

																	<span id="postUpvotes119" class="ng-binding">0</span>

																	<span class="ng-binding">&nbsp;upvote</span>

																</a>

															</div>

															<div class="left odd">

																<a href="#">

																	<span id="postCommentFor_119" class="ng-binding">0</span>

																	<span class="ng-binding">&nbsp;comment</span>

																</a>

															</div>

															<div class="left odd">

																<a href="#"><span>10</span><span>&nbsp;shares</span></a>

															</div>

															<div class="right odd">

																<a href="#">

																	<span class="ng-binding">2</span>

																	<span class="ng-binding">&nbsp;viewed</span>

																</a>

															</div>

															<div class="clearfix"></div>

														</div>

														<div class="clearfix"></div>

													</div>

													<div class="profileCommentFooter addNew">

														<div class="left">

															<a href="#" class="upvoteIcon">

																<img src="http://swolk.com/assets/pages/img/arrow2_t.png" alt=""/>

															</a>



															<a href="#" class="">

																<img src="http://swolk.com/assets/pages/img/speech_bubble4.png" alt="">

															</a>

															<a href="#" class="ng-scope">

																<img src="http://swolk.com/assets/pages/img/refresh4.png" alt="">

															</a>

															<a href="#">

																<img src="http://swolk.com/assets/pages/img/bookmark.png" alt="">

															</a>

															<a href="#">

																<img src="http://swolk.com/assets/pages/img/log_out.png" alt="">

															</a>

														</div>

														<div class="right">

															<a href="#" class="moreBtnN">

																<i class="fa fa-circle" aria-hidden="true"></i>

																<i class="fa fa-circle" aria-hidden="true"></i>

																<i class="fa fa-circle" aria-hidden="true"></i>

															</a>

														</div>

														<div class="clearfix"></div>

													</div>

												</div>

											</div>

										</div>
										<div class="blockContent ng-scope">

											<div class="profileCommentBox clkProfileCommentBox">

												<div class="cardBoxClk"></div>

												<div class="profileCommentBoxTop">

													<div class="userStatusRow">

														<div class="userStatusImage">

															<a href="/profile/souravdey">

																<img ng-src="http://swolk.com/assets/img/default-profile.png" alt="profile image"/>

															</a>

														</div>



														<div class="userStatusInfo">

															<span class="userStatusInfoTtl withLocation clearfix">

																<a" class="ng-binding" href="/profile/souravdey">Sourav Dey</a>

																<a href="#" class="follwBtn">

																	<i class="fa fa-plus-circle" aria-hidden="true"></i>

																</a>

																<!-- ngIf: post.location -->

															</span>

															<p>

																<span class="clearfix">

																	<small class="ng-binding">&nbsp;Web Developer</small>

																</span>

																<span class="block clearfix"></span>

																<small class="ng-binding">Shared this post 2 hours ago</small>

															</p>

														</div>

														<div class="clearfix"></div>

													</div>

													

													<div class="post_type">

														<p ng-show="post.caption" class="ng-binding ng-hide"></p>

														<div class="catagoryTtl">

															<div class="catagoryTagRow">

																<a href="#" class="catagoryTtlHighLightBlack ng-binding">Business</a>

																<a href="#" class="catagoryTtlHighLight ng-binding">Marketing</a>

															</div>

															<p class="ng-binding">We Need Fun</p>

														</div>

														<div class="postShortDesc ng-hide" ng-show="post.short_description">

															<p ng-show="post.short_description" class="ng-binding ng-hide"></p>

														</div>

														<div>

															<p class="postLink ng-scope">

																<span>

																	<a href="" target="_blank" class="ng-binding">

																		<i class="fa fa-external-link"></i>

																		weneedfun.com

																	</a>

																</span>                        

															</p><!-- end ngIf: post.source -->

															<div class="uploadImage">

																<a>

																	<img ng-src="https://uproxx.files.wordpress.com/2014/11/bob-dylan-the-band.jpg" />

																</a>

															</div>

														</div>

														<div class="profileNewCoverBottomLink ng-scope" ng-if="post.tags">

															<ul>

																<!-- ngRepeat: tag in post.tags -->

															</ul>

														</div><!-- end ngIf: post.tags -->

													</div>

													

													<div class="profileCommentFooter addNew noBorder">

														<div class="likeCommentBar">

															<div class="left odd">

																<a href="javascript:void(0)">

																	<span id="postUpvotes119" class="ng-binding">0</span>

																	<span class="ng-binding">&nbsp;upvote</span>

																</a>

															</div>

															<div class="left odd">

																<a href="#">

																	<span id="postCommentFor_119" class="ng-binding">0</span>

																	<span class="ng-binding">&nbsp;comment</span>

																</a>

															</div>

															<div class="left odd">

																<a href="#"><span>10</span><span>&nbsp;shares</span></a>

															</div>

															<div class="right odd">

																<a href="#">

																	<span class="ng-binding">2</span>

																	<span class="ng-binding">&nbsp;viewed</span>

																</a>

															</div>

															<div class="clearfix"></div>

														</div>

														<div class="clearfix"></div>

													</div>

													<div class="profileCommentFooter addNew">

														<div class="left">

															<a href="#" class="upvoteIcon">

																<img src="http://swolk.com/assets/pages/img/arrow2_t.png" alt=""/>

															</a>



															<a href="#" class="">

																<img src="http://swolk.com/assets/pages/img/speech_bubble4.png" alt="">

															</a>

															<a href="#" class="ng-scope">

																<img src="http://swolk.com/assets/pages/img/refresh4.png" alt="">

															</a>

															<a href="#">

																<img src="http://swolk.com/assets/pages/img/bookmark.png" alt="">

															</a>

															<a href="#">

																<img src="http://swolk.com/assets/pages/img/log_out.png" alt="">

															</a>

														</div>

														<div class="right">

															<a href="#" class="moreBtnN">

																<i class="fa fa-circle" aria-hidden="true"></i>

																<i class="fa fa-circle" aria-hidden="true"></i>

																<i class="fa fa-circle" aria-hidden="true"></i>

															</a>

														</div>

														<div class="clearfix"></div>

													</div>

												</div>

											</div>

										</div>
										<div class="blockContent ng-scope">

											<div class="profileCommentBox clkProfileCommentBox">

												<div class="cardBoxClk"></div>

												<div class="profileCommentBoxTop">

													<div class="userStatusRow">

														<div class="userStatusImage">

															<a href="/profile/souravdey">

																<img ng-src="http://swolk.com/assets/img/default-profile.png" alt="profile image"/>

															</a>

														</div>



														<div class="userStatusInfo">

															<span class="userStatusInfoTtl withLocation clearfix">

																<a" class="ng-binding" href="/profile/souravdey">Sourav Dey</a>

																<a href="#" class="follwBtn">

																	<i class="fa fa-plus-circle" aria-hidden="true"></i>

																</a>

																<!-- ngIf: post.location -->

															</span>

															<p>

																<span class="clearfix">

																	<small class="ng-binding">&nbsp;Web Developer</small>

																</span>

																<span class="block clearfix"></span>

																<small class="ng-binding">Shared this post 2 hours ago</small>

															</p>

														</div>

														<div class="clearfix"></div>

													</div>

													

													<div class="post_type">

														<p ng-show="post.caption" class="ng-binding ng-hide"></p>

														<div class="catagoryTtl">

															<div class="catagoryTagRow">

																<a href="#" class="catagoryTtlHighLightBlack ng-binding">Business</a>

																<a href="#" class="catagoryTtlHighLight ng-binding">Marketing</a>

															</div>

															<p class="ng-binding">We Need Fun</p>

														</div>

														<div class="postShortDesc ng-hide" ng-show="post.short_description">

															<p ng-show="post.short_description" class="ng-binding ng-hide"></p>

														</div>

														<div>

															<p class="postLink ng-scope">

																<span>

																	<a href="" target="_blank" class="ng-binding">

																		<i class="fa fa-external-link"></i>

																		weneedfun.com

																	</a>

																</span>                        

															</p><!-- end ngIf: post.source -->

															<div class="uploadImage">

																<a>

																	<img ng-src="http://www.wwangle.com/blog/wp-content/uploads/2008/12/no-direction-home-09.jpg" />

																</a>

															</div>

														</div>

														<div class="profileNewCoverBottomLink ng-scope" ng-if="post.tags">

															<ul>

																<!-- ngRepeat: tag in post.tags -->

															</ul>

														</div><!-- end ngIf: post.tags -->

													</div>

													

													<div class="profileCommentFooter addNew noBorder">

														<div class="likeCommentBar">

															<div class="left odd">

																<a href="javascript:void(0)">

																	<span id="postUpvotes119" class="ng-binding">0</span>

																	<span class="ng-binding">&nbsp;upvote</span>

																</a>

															</div>

															<div class="left odd">

																<a href="#">

																	<span id="postCommentFor_119" class="ng-binding">0</span>

																	<span class="ng-binding">&nbsp;comment</span>

																</a>

															</div>

															<div class="left odd">

																<a href="#"><span>10</span><span>&nbsp;shares</span></a>

															</div>

															<div class="right odd">

																<a href="#">

																	<span class="ng-binding">2</span>

																	<span class="ng-binding">&nbsp;viewed</span>

																</a>

															</div>

															<div class="clearfix"></div>

														</div>

														<div class="clearfix"></div>

													</div>

													<div class="profileCommentFooter addNew">

														<div class="left">

															<a href="#" class="upvoteIcon">

																<img src="http://swolk.com/assets/pages/img/arrow2_t.png" alt=""/>

															</a>



															<a href="#" class="">

																<img src="http://swolk.com/assets/pages/img/speech_bubble4.png" alt="">

															</a>

															<a href="#" class="ng-scope">

																<img src="http://swolk.com/assets/pages/img/refresh4.png" alt="">

															</a>

															<a href="#">

																<img src="http://swolk.com/assets/pages/img/bookmark.png" alt="">

															</a>

															<a href="#">

																<img src="http://swolk.com/assets/pages/img/log_out.png" alt="">

															</a>

														</div>

														<div class="right">

															<a href="#" class="moreBtnN">

																<i class="fa fa-circle" aria-hidden="true"></i>

																<i class="fa fa-circle" aria-hidden="true"></i>

																<i class="fa fa-circle" aria-hidden="true"></i>

															</a>

														</div>

														<div class="clearfix"></div>

													</div>

												</div>

											</div>

										</div>
										<div class="blockContent ng-scope">

											<div class="profileCommentBox clkProfileCommentBox">

												<div class="cardBoxClk"></div>

												<div class="profileCommentBoxTop">

													<div class="userStatusRow">

														<div class="userStatusImage">

															<a href="/profile/souravdey">

																<img ng-src="http://swolk.com/assets/img/default-profile.png" alt="profile image"/>

															</a>

														</div>



														<div class="userStatusInfo">

															<span class="userStatusInfoTtl withLocation clearfix">

																<a" class="ng-binding" href="/profile/souravdey">Sourav Dey</a>

																<a href="#" class="follwBtn">

																	<i class="fa fa-plus-circle" aria-hidden="true"></i>

																</a>

																<!-- ngIf: post.location -->

															</span>

															<p>

																<span class="clearfix">

																	<small class="ng-binding">&nbsp;Web Developer</small>

																</span>

																<span class="block clearfix"></span>

																<small class="ng-binding">Shared this post 2 hours ago</small>

															</p>

														</div>

														<div class="clearfix"></div>

													</div>

													

													<div class="post_type">

														<p ng-show="post.caption" class="ng-binding ng-hide"></p>

														<div class="catagoryTtl">

															<div class="catagoryTagRow">

																<a href="#" class="catagoryTtlHighLightBlack ng-binding">Business</a>

																<a href="#" class="catagoryTtlHighLight ng-binding">Marketing</a>

															</div>

															<p class="ng-binding">We Need Fun</p>

														</div>

														<div class="postShortDesc ng-hide" ng-show="post.short_description">

															<p ng-show="post.short_description" class="ng-binding ng-hide"></p>

														</div>

														<div>

															<p class="postLink ng-scope">

																<span>

																	<a href="" target="_blank" class="ng-binding">

																		<i class="fa fa-external-link"></i>

																		weneedfun.com

																	</a>

																</span>                        

															</p><!-- end ngIf: post.source -->

															<div class="uploadImage">

																<a>

																	<img ng-src="http://therugbybusinessnetwork.com/wp-content/uploads/2016/07/article-post-2.png" />

																</a>

															</div>

														</div>

														<div class="profileNewCoverBottomLink ng-scope" ng-if="post.tags">

															<ul>

																<!-- ngRepeat: tag in post.tags -->

															</ul>

														</div><!-- end ngIf: post.tags -->

													</div>

													

													<div class="profileCommentFooter addNew noBorder">

														<div class="likeCommentBar">

															<div class="left odd">

																<a href="javascript:void(0)">

																	<span id="postUpvotes119" class="ng-binding">0</span>

																	<span class="ng-binding">&nbsp;upvote</span>

																</a>

															</div>

															<div class="left odd">

																<a href="#">

																	<span id="postCommentFor_119" class="ng-binding">0</span>

																	<span class="ng-binding">&nbsp;comment</span>

																</a>

															</div>

															<div class="left odd">

																<a href="#"><span>10</span><span>&nbsp;shares</span></a>

															</div>

															<div class="right odd">

																<a href="#">

																	<span class="ng-binding">2</span>

																	<span class="ng-binding">&nbsp;viewed</span>

																</a>

															</div>

															<div class="clearfix"></div>

														</div>

														<div class="clearfix"></div>

													</div>

													<div class="profileCommentFooter addNew">

														<div class="left">

															<a href="#" class="upvoteIcon">

																<img src="http://swolk.com/assets/pages/img/arrow2_t.png" alt=""/>

															</a>



															<a href="#" class="">

																<img src="http://swolk.com/assets/pages/img/speech_bubble4.png" alt="">

															</a>

															<a href="#" class="ng-scope">

																<img src="http://swolk.com/assets/pages/img/refresh4.png" alt="">

															</a>

															<a href="#">

																<img src="http://swolk.com/assets/pages/img/bookmark.png" alt="">

															</a>

															<a href="#">

																<img src="http://swolk.com/assets/pages/img/log_out.png" alt="">

															</a>

														</div>

														<div class="right">

															<a href="#" class="moreBtnN">

																<i class="fa fa-circle" aria-hidden="true"></i>

																<i class="fa fa-circle" aria-hidden="true"></i>

																<i class="fa fa-circle" aria-hidden="true"></i>

															</a>

														</div>

														<div class="clearfix"></div>

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
			</script>

			<script type="text/ng-template" id="trending" >				
				<div class="row">
					<div class="col-md-12 col-lg-12">
						<div style="display:block;">
							<div style="display:block;">
								<div style="display:block;">
								</div>
								<div class="blockContentRow inline">

									<div class="blockContentRow inline">

										<div class="blockContent ng-scope">

											<div class="profileCommentBox clkProfileCommentBox">

												<div class="cardBoxClk"></div>

												<div class="profileCommentBoxTop">

													<div class="userStatusRow">

														<div class="userStatusImage">

															<a href="/profile/souravdey">

																<img ng-src="http://swolk.com/assets/img/default-profile.png" alt="profile image"/>

															</a>

														</div>



														<div class="userStatusInfo">

															<span class="userStatusInfoTtl withLocation clearfix">

																<a" class="ng-binding" href="/profile/souravdey">Sourav Dey</a>

																<a href="#" class="follwBtn">

																	<i class="fa fa-plus-circle" aria-hidden="true"></i>

																</a>

																<!-- ngIf: post.location -->

															</span>

															<p>

																<span class="clearfix">

																	<small class="ng-binding">&nbsp;Web Developer</small>

																</span>

																<span class="block clearfix"></span>

																<small class="ng-binding">Shared this post 2 hours ago</small>

															</p>

														</div>

														<div class="clearfix"></div>

													</div>

													

													<div class="post_type">

														<p ng-show="post.caption" class="ng-binding ng-hide"></p>

														<div class="catagoryTtl">

															<div class="catagoryTagRow">

																<a href="#" class="catagoryTtlHighLightBlack ng-binding">Business</a>

																<a href="#" class="catagoryTtlHighLight ng-binding">Marketing</a>

															</div>

															<p class="ng-binding">We Need Fun</p>

														</div>

														<div class="postShortDesc ng-hide" ng-show="post.short_description">

															<p ng-show="post.short_description" class="ng-binding ng-hide"></p>

														</div>

														<div>

															<p class="postLink ng-scope">

																<span>

																	<a href="http://weneedfun.com/wp-content/uploads/2015/10/All-Games-2.jpg" target="_blank" class="ng-binding">

																		<i class="fa fa-external-link"></i>

																		weneedfun.com

																	</a>

																</span>                        

															</p><!-- end ngIf: post.source -->

															<div class="uploadImage">

																<a>

																	<img ng-src="http://www.burrp.com/know/wp-content/uploads/2015/07/1528553_668317366554125_1671867219_n.jpg?utm_source=article&utm_medium=76064&utm_campaign=mention" />

																</a>

															</div>

														</div>

														<div class="profileNewCoverBottomLink ng-scope" ng-if="post.tags">

															<ul>

																<!-- ngRepeat: tag in post.tags -->

															</ul>

														</div><!-- end ngIf: post.tags -->

													</div>

													

													<div class="profileCommentFooter addNew noBorder">

														<div class="likeCommentBar">

															<div class="left odd">

																<a href="javascript:void(0)">

																	<span id="postUpvotes119" class="ng-binding">0</span>

																	<span class="ng-binding">&nbsp;upvote</span>

																</a>

															</div>

															<div class="left odd">

																<a href="#">

																	<span id="postCommentFor_119" class="ng-binding">0</span>

																	<span class="ng-binding">&nbsp;comment</span>

																</a>

															</div>

															<div class="left odd">

																<a href="#"><span>10</span><span>&nbsp;shares</span></a>

															</div>

															<div class="right odd">

																<a href="#">

																	<span class="ng-binding">2</span>

																	<span class="ng-binding">&nbsp;viewed</span>

																</a>

															</div>

															<div class="clearfix"></div>

														</div>

														<div class="clearfix"></div>

													</div>

													<div class="profileCommentFooter addNew">

														<div class="left">

															<a href="#" class="upvoteIcon">

																<img src="http://swolk.com/assets/pages/img/arrow2_t.png" alt=""/>

															</a>



															<a href="#" class="">

																<img src="http://swolk.com/assets/pages/img/speech_bubble4.png" alt="">

															</a>

															<a href="#" class="ng-scope">

																<img src="http://swolk.com/assets/pages/img/refresh4.png" alt="">

															</a>

															<a href="#">

																<img src="http://swolk.com/assets/pages/img/bookmark.png" alt="">

															</a>

															<a href="#">

																<img src="http://swolk.com/assets/pages/img/log_out.png" alt="">

															</a>

														</div>

														<div class="right">

															<a href="#" class="moreBtnN">

																<i class="fa fa-circle" aria-hidden="true"></i>

																<i class="fa fa-circle" aria-hidden="true"></i>

																<i class="fa fa-circle" aria-hidden="true"></i>

															</a>

														</div>

														<div class="clearfix"></div>

													</div>

												</div>

											</div>

										</div>

										<div class="blockContent ng-scope">

											<div class="profileCommentBox clkProfileCommentBox">

												<div class="cardBoxClk"></div>

												<div class="profileCommentBoxTop">

													<div class="userStatusRow">

														<div class="userStatusImage">

															<a href="/profile/souravdey">

																<img ng-src="http://swolk.com/assets/img/default-profile.png" alt="profile image"/>

															</a>

														</div>



														<div class="userStatusInfo">

															<span class="userStatusInfoTtl withLocation clearfix">

																<a" class="ng-binding" href="/profile/souravdey">Sourav Dey</a>

																<a href="#" class="follwBtn">

																	<i class="fa fa-plus-circle" aria-hidden="true"></i>

																</a>

																<!-- ngIf: post.location -->

															</span>

															<p>

																<span class="clearfix">

																	<small class="ng-binding">&nbsp;Web Developer</small>

																</span>

																<span class="block clearfix"></span>

																<small class="ng-binding">Shared this post 2 hours ago</small>

															</p>

														</div>

														<div class="clearfix"></div>

													</div>

													

													<div class="post_type">

														<p ng-show="post.caption" class="ng-binding ng-hide"></p>

														<div class="catagoryTtl">

															<div class="catagoryTagRow">

																<a href="#" class="catagoryTtlHighLightBlack ng-binding">Business</a>

																<a href="#" class="catagoryTtlHighLight ng-binding">Marketing</a>

															</div>

															<p class="ng-binding">We Need Fun</p>

														</div>

														<div class="postShortDesc ng-hide" ng-show="post.short_description">

															<p ng-show="post.short_description" class="ng-binding ng-hide"></p>

														</div>

														<div>

															<p class="postLink ng-scope">

																<span>

																	<a href="http://weneedfun.com/wp-content/uploads/2015/10/All-Games-2.jpg" target="_blank" class="ng-binding">

																		<i class="fa fa-external-link"></i>

																		weneedfun.com

																	</a>

																</span>                        

															</p><!-- end ngIf: post.source -->

															<div class="uploadImage">

																<a>

																	<img ng-src="http://www.burrp.com/know/wp-content/uploads/2015/07/1528553_668317366554125_1671867219_n.jpg?utm_source=article&utm_medium=76064&utm_campaign=mention" />

																</a>

															</div>

														</div>

														<div class="profileNewCoverBottomLink ng-scope" ng-if="post.tags">

															<ul>

																<!-- ngRepeat: tag in post.tags -->

															</ul>

														</div><!-- end ngIf: post.tags -->

													</div>

													

													<div class="profileCommentFooter addNew noBorder">

														<div class="likeCommentBar">

															<div class="left odd">

																<a href="javascript:void(0)">

																	<span id="postUpvotes119" class="ng-binding">0</span>

																	<span class="ng-binding">&nbsp;upvote</span>

																</a>

															</div>

															<div class="left odd">

																<a href="#">

																	<span id="postCommentFor_119" class="ng-binding">0</span>

																	<span class="ng-binding">&nbsp;comment</span>

																</a>

															</div>

															<div class="left odd">

																<a href="#"><span>10</span><span>&nbsp;shares</span></a>

															</div>

															<div class="right odd">

																<a href="#">

																	<span class="ng-binding">2</span>

																	<span class="ng-binding">&nbsp;viewed</span>

																</a>

															</div>

															<div class="clearfix"></div>

														</div>

														<div class="clearfix"></div>

													</div>

													<div class="profileCommentFooter addNew">

														<div class="left">

															<a href="#" class="upvoteIcon">

																<img src="http://swolk.com/assets/pages/img/arrow2_t.png" alt=""/>

															</a>



															<a href="#" class="">

																<img src="http://swolk.com/assets/pages/img/speech_bubble4.png" alt="">

															</a>

															<a href="#" class="ng-scope">

																<img src="http://swolk.com/assets/pages/img/refresh4.png" alt="">

															</a>

															<a href="#">

																<img src="http://swolk.com/assets/pages/img/bookmark.png" alt="">

															</a>

															<a href="#">

																<img src="http://swolk.com/assets/pages/img/log_out.png" alt="">

															</a>

														</div>

														<div class="right">

															<a href="#" class="moreBtnN">

																<i class="fa fa-circle" aria-hidden="true"></i>

																<i class="fa fa-circle" aria-hidden="true"></i>

																<i class="fa fa-circle" aria-hidden="true"></i>

															</a>

														</div>

														<div class="clearfix"></div>

													</div>

												</div>

											</div>

										</div>

										<div class="blockContent ng-scope">

											<div class="profileCommentBox clkProfileCommentBox">

												<div class="cardBoxClk"></div>

												<div class="profileCommentBoxTop">

													<div class="userStatusRow">

														<div class="userStatusImage">

															<a href="/profile/souravdey">

																<img ng-src="http://swolk.com/assets/img/default-profile.png" alt="profile image"/>

															</a>

														</div>



														<div class="userStatusInfo">

															<span class="userStatusInfoTtl withLocation clearfix">

																<a" class="ng-binding" href="/profile/souravdey">Sourav Dey</a>

																<a href="#" class="follwBtn">

																	<i class="fa fa-plus-circle" aria-hidden="true"></i>

																</a>

																<!-- ngIf: post.location -->

															</span>

															<p>

																<span class="clearfix">

																	<small class="ng-binding">&nbsp;Web Developer</small>

																</span>

																<span class="block clearfix"></span>

																<small class="ng-binding">Shared this post 2 hours ago</small>

															</p>

														</div>

														<div class="clearfix"></div>

													</div>

													

													<div class="post_type">

														<p ng-show="post.caption" class="ng-binding ng-hide"></p>

														<div class="catagoryTtl">

															<div class="catagoryTagRow">

																<a href="#" class="catagoryTtlHighLightBlack ng-binding">Business</a>

																<a href="#" class="catagoryTtlHighLight ng-binding">Marketing</a>

															</div>

															<p class="ng-binding">We Need Fun</p>

														</div>

														<div class="postShortDesc ng-hide" ng-show="post.short_description">

															<p ng-show="post.short_description" class="ng-binding ng-hide"></p>

														</div>

														<div>

															<p class="postLink ng-scope">

																<span>

																	<a href="http://weneedfun.com/wp-content/uploads/2015/10/All-Games-2.jpg" target="_blank" class="ng-binding">

																		<i class="fa fa-external-link"></i>

																		weneedfun.com

																	</a>

																</span>                        

															</p><!-- end ngIf: post.source -->

															<div class="uploadImage">

																<a>

																	<img ng-src="http://www.burrp.com/know/wp-content/uploads/2015/07/1528553_668317366554125_1671867219_n.jpg?utm_source=article&utm_medium=76064&utm_campaign=mention" />

																</a>

															</div>

														</div>

														<div class="profileNewCoverBottomLink ng-scope" ng-if="post.tags">

															<ul>

																<!-- ngRepeat: tag in post.tags -->

															</ul>

														</div><!-- end ngIf: post.tags -->

													</div>

													

													<div class="profileCommentFooter addNew noBorder">

														<div class="likeCommentBar">

															<div class="left odd">

																<a href="javascript:void(0)">

																	<span id="postUpvotes119" class="ng-binding">0</span>

																	<span class="ng-binding">&nbsp;upvote</span>

																</a>

															</div>

															<div class="left odd">

																<a href="#">

																	<span id="postCommentFor_119" class="ng-binding">0</span>

																	<span class="ng-binding">&nbsp;comment</span>

																</a>

															</div>

															<div class="left odd">

																<a href="#"><span>10</span><span>&nbsp;shares</span></a>

															</div>

															<div class="right odd">

																<a href="#">

																	<span class="ng-binding">2</span>

																	<span class="ng-binding">&nbsp;viewed</span>

																</a>

															</div>

															<div class="clearfix"></div>

														</div>

														<div class="clearfix"></div>

													</div>

													<div class="profileCommentFooter addNew">

														<div class="left">

															<a href="#" class="upvoteIcon">

																<img src="http://swolk.com/assets/pages/img/arrow2_t.png" alt=""/>

															</a>



															<a href="#" class="">

																<img src="http://swolk.com/assets/pages/img/speech_bubble4.png" alt="">

															</a>

															<a href="#" class="ng-scope">

																<img src="http://swolk.com/assets/pages/img/refresh4.png" alt="">

															</a>

															<a href="#">

																<img src="http://swolk.com/assets/pages/img/bookmark.png" alt="">

															</a>

															<a href="#">

																<img src="http://swolk.com/assets/pages/img/log_out.png" alt="">

															</a>

														</div>

														<div class="right">

															<a href="#" class="moreBtnN">

																<i class="fa fa-circle" aria-hidden="true"></i>

																<i class="fa fa-circle" aria-hidden="true"></i>

																<i class="fa fa-circle" aria-hidden="true"></i>

															</a>

														</div>

														<div class="clearfix"></div>

													</div>

												</div>

											</div>

										</div>

										<div class="blockContent ng-scope">

											<div class="profileCommentBox clkProfileCommentBox">

												<div class="cardBoxClk"></div>

												<div class="profileCommentBoxTop">

													<div class="userStatusRow">

														<div class="userStatusImage">

															<a href="/profile/souravdey">

																<img ng-src="http://swolk.com/assets/img/default-profile.png" alt="profile image"/>

															</a>

														</div>



														<div class="userStatusInfo">

															<span class="userStatusInfoTtl withLocation clearfix">

																<a" class="ng-binding" href="/profile/souravdey">Sourav Dey</a>

																<a href="#" class="follwBtn">

																	<i class="fa fa-plus-circle" aria-hidden="true"></i>

																</a>

																<!-- ngIf: post.location -->

															</span>

															<p>

																<span class="clearfix">

																	<small class="ng-binding">&nbsp;Web Developer</small>

																</span>

																<span class="block clearfix"></span>

																<small class="ng-binding">Shared this post 2 hours ago</small>

															</p>

														</div>

														<div class="clearfix"></div>

													</div>

													

													<div class="post_type">

														<p ng-show="post.caption" class="ng-binding ng-hide"></p>

														<div class="catagoryTtl">

															<div class="catagoryTagRow">

																<a href="#" class="catagoryTtlHighLightBlack ng-binding">Business</a>

																<a href="#" class="catagoryTtlHighLight ng-binding">Marketing</a>

															</div>

															<p class="ng-binding">We Need Fun</p>

														</div>

														<div class="postShortDesc ng-hide" ng-show="post.short_description">

															<p ng-show="post.short_description" class="ng-binding ng-hide"></p>

														</div>

														<div>

															<p class="postLink ng-scope">

																<span>

																	<a href="http://weneedfun.com/wp-content/uploads/2015/10/All-Games-2.jpg" target="_blank" class="ng-binding">

																		<i class="fa fa-external-link"></i>

																		weneedfun.com

																	</a>

																</span>                        

															</p><!-- end ngIf: post.source -->

															<div class="uploadImage">

																<a>

																	<img ng-src="http://www.burrp.com/know/wp-content/uploads/2015/07/1528553_668317366554125_1671867219_n.jpg?utm_source=article&utm_medium=76064&utm_campaign=mention" />

																</a>

															</div>

														</div>

														<div class="profileNewCoverBottomLink ng-scope" ng-if="post.tags">

															<ul>

																<!-- ngRepeat: tag in post.tags -->

															</ul>

														</div><!-- end ngIf: post.tags -->

													</div>

													

													<div class="profileCommentFooter addNew noBorder">

														<div class="likeCommentBar">

															<div class="left odd">

																<a href="javascript:void(0)">

																	<span id="postUpvotes119" class="ng-binding">0</span>

																	<span class="ng-binding">&nbsp;upvote</span>

																</a>

															</div>

															<div class="left odd">

																<a href="#">

																	<span id="postCommentFor_119" class="ng-binding">0</span>

																	<span class="ng-binding">&nbsp;comment</span>

																</a>

															</div>

															<div class="left odd">

																<a href="#"><span>10</span><span>&nbsp;shares</span></a>

															</div>

															<div class="right odd">

																<a href="#">

																	<span class="ng-binding">2</span>

																	<span class="ng-binding">&nbsp;viewed</span>

																</a>

															</div>

															<div class="clearfix"></div>

														</div>

														<div class="clearfix"></div>

													</div>

													<div class="profileCommentFooter addNew">

														<div class="left">

															<a href="#" class="upvoteIcon">

																<img src="http://swolk.com/assets/pages/img/arrow2_t.png" alt=""/>

															</a>



															<a href="#" class="">

																<img src="http://swolk.com/assets/pages/img/speech_bubble4.png" alt="">

															</a>

															<a href="#" class="ng-scope">

																<img src="http://swolk.com/assets/pages/img/refresh4.png" alt="">

															</a>

															<a href="#">

																<img src="http://swolk.com/assets/pages/img/bookmark.png" alt="">

															</a>

															<a href="#">

																<img src="http://swolk.com/assets/pages/img/log_out.png" alt="">

															</a>

														</div>

														<div class="right">

															<a href="#" class="moreBtnN">

																<i class="fa fa-circle" aria-hidden="true"></i>

																<i class="fa fa-circle" aria-hidden="true"></i>

																<i class="fa fa-circle" aria-hidden="true"></i>

															</a>

														</div>

														<div class="clearfix"></div>

													</div>

												</div>

											</div>

										</div>

										<div class="blockContent ng-scope">

											<div class="profileCommentBox clkProfileCommentBox">

												<div class="cardBoxClk"></div>

												<div class="profileCommentBoxTop">

													<div class="userStatusRow">

														<div class="userStatusImage">

															<a href="/profile/souravdey">

																<img ng-src="http://swolk.com/assets/img/default-profile.png" alt="profile image"/>

															</a>

														</div>



														<div class="userStatusInfo">

															<span class="userStatusInfoTtl withLocation clearfix">

																<a" class="ng-binding" href="/profile/souravdey">Sourav Dey</a>

																<a href="#" class="follwBtn">

																	<i class="fa fa-plus-circle" aria-hidden="true"></i>

																</a>

																<!-- ngIf: post.location -->

															</span>

															<p>

																<span class="clearfix">

																	<small class="ng-binding">&nbsp;Web Developer</small>

																</span>

																<span class="block clearfix"></span>

																<small class="ng-binding">Shared this post 2 hours ago</small>

															</p>

														</div>

														<div class="clearfix"></div>

													</div>

													

													<div class="post_type">

														<p ng-show="post.caption" class="ng-binding ng-hide"></p>

														<div class="catagoryTtl">

															<div class="catagoryTagRow">

																<a href="#" class="catagoryTtlHighLightBlack ng-binding">Business</a>

																<a href="#" class="catagoryTtlHighLight ng-binding">Marketing</a>

															</div>

															<p class="ng-binding">We Need Fun</p>

														</div>

														<div class="postShortDesc ng-hide" ng-show="post.short_description">

															<p ng-show="post.short_description" class="ng-binding ng-hide"></p>

														</div>

														<div>

															<p class="postLink ng-scope">

																<span>

																	<a href="http://weneedfun.com/wp-content/uploads/2015/10/All-Games-2.jpg" target="_blank" class="ng-binding">

																		<i class="fa fa-external-link"></i>

																		weneedfun.com

																	</a>

																</span>                        

															</p><!-- end ngIf: post.source -->

															<div class="uploadImage">

																<a>

																	<img ng-src="http://www.burrp.com/know/wp-content/uploads/2015/07/1528553_668317366554125_1671867219_n.jpg?utm_source=article&utm_medium=76064&utm_campaign=mention" />

																</a>

															</div>

														</div>

														<div class="profileNewCoverBottomLink ng-scope" ng-if="post.tags">

															<ul>

																<!-- ngRepeat: tag in post.tags -->

															</ul>

														</div><!-- end ngIf: post.tags -->

													</div>

													

													<div class="profileCommentFooter addNew noBorder">

														<div class="likeCommentBar">

															<div class="left odd">

																<a href="javascript:void(0)">

																	<span id="postUpvotes119" class="ng-binding">0</span>

																	<span class="ng-binding">&nbsp;upvote</span>

																</a>

															</div>

															<div class="left odd">

																<a href="#">

																	<span id="postCommentFor_119" class="ng-binding">0</span>

																	<span class="ng-binding">&nbsp;comment</span>

																</a>

															</div>

															<div class="left odd">

																<a href="#"><span>10</span><span>&nbsp;shares</span></a>

															</div>

															<div class="right odd">

																<a href="#">

																	<span class="ng-binding">2</span>

																	<span class="ng-binding">&nbsp;viewed</span>

																</a>

															</div>

															<div class="clearfix"></div>

														</div>

														<div class="clearfix"></div>

													</div>

													<div class="profileCommentFooter addNew">

														<div class="left">

															<a href="#" class="upvoteIcon">

																<img src="http://swolk.com/assets/pages/img/arrow2_t.png" alt=""/>

															</a>



															<a href="#" class="">

																<img src="http://swolk.com/assets/pages/img/speech_bubble4.png" alt="">

															</a>

															<a href="#" class="ng-scope">

																<img src="http://swolk.com/assets/pages/img/refresh4.png" alt="">

															</a>

															<a href="#">

																<img src="http://swolk.com/assets/pages/img/bookmark.png" alt="">

															</a>

															<a href="#">

																<img src="http://swolk.com/assets/pages/img/log_out.png" alt="">

															</a>

														</div>

														<div class="right">

															<a href="#" class="moreBtnN">

																<i class="fa fa-circle" aria-hidden="true"></i>

																<i class="fa fa-circle" aria-hidden="true"></i>

																<i class="fa fa-circle" aria-hidden="true"></i>

															</a>

														</div>

														<div class="clearfix"></div>

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
			</script>
			
			<script type="text/ng-template" id="popular" >
				<div class="row">
					<div class="col-md-12 col-lg-12">
						<div style="display:block;">
							<div style="display:block;">
								<div style="display:block;">
								</div>
								<div class="blockContentRow inline">

									<div class="blockContentRow inline">

										<div class="blockContent ng-scope">

											<div class="profileCommentBox clkProfileCommentBox">

												<div class="cardBoxClk"></div>

												<div class="profileCommentBoxTop">

													<div class="userStatusRow">

														<div class="userStatusImage">

															<a href="/profile/souravdey">

																<img ng-src="http://swolk.com/assets/img/default-profile.png" alt="profile image"/>

															</a>

														</div>



														<div class="userStatusInfo">

															<span class="userStatusInfoTtl withLocation clearfix">

																<a" class="ng-binding" href="/profile/souravdey">Sourav Dey</a>

																<a href="#" class="follwBtn">

																	<i class="fa fa-plus-circle" aria-hidden="true"></i>

																</a>

																<!-- ngIf: post.location -->

															</span>

															<p>

																<span class="clearfix">

																	<small class="ng-binding">&nbsp;Web Developer</small>

																</span>

																<span class="block clearfix"></span>

																<small class="ng-binding">Shared this post 2 hours ago</small>

															</p>

														</div>

														<div class="clearfix"></div>

													</div>

													

													<div class="post_type">

														<p ng-show="post.caption" class="ng-binding ng-hide"></p>

														<div class="catagoryTtl">

															<div class="catagoryTagRow">

																<a href="#" class="catagoryTtlHighLightBlack ng-binding">Business</a>

																<a href="#" class="catagoryTtlHighLight ng-binding">Marketing</a>

															</div>

															<p class="ng-binding">We Need Fun</p>

														</div>

														<div class="postShortDesc ng-hide" ng-show="post.short_description">

															<p ng-show="post.short_description" class="ng-binding ng-hide"></p>

														</div>

														<div>

															<p class="postLink ng-scope">

																<span>

																	<a href="http://weneedfun.com/wp-content/uploads/2015/10/All-Games-2.jpg" target="_blank" class="ng-binding">

																		<i class="fa fa-external-link"></i>

																		weneedfun.com

																	</a>

																</span>                        

															</p><!-- end ngIf: post.source -->

															<div class="uploadImage">

																<a>

																	<img ng-src="http://www.zooborns.com/.a/6a010535647bf3970b0120a66e92aa970c-pi" />

																</a>

															</div>

														</div>

														<div class="profileNewCoverBottomLink ng-scope" ng-if="post.tags">

															<ul>

																<!-- ngRepeat: tag in post.tags -->

															</ul>

														</div><!-- end ngIf: post.tags -->

													</div>

													

													<div class="profileCommentFooter addNew noBorder">

														<div class="likeCommentBar">

															<div class="left odd">

																<a href="javascript:void(0)">

																	<span id="postUpvotes119" class="ng-binding">0</span>

																	<span class="ng-binding">&nbsp;upvote</span>

																</a>

															</div>

															<div class="left odd">

																<a href="#">

																	<span id="postCommentFor_119" class="ng-binding">0</span>

																	<span class="ng-binding">&nbsp;comment</span>

																</a>

															</div>

															<div class="left odd">

																<a href="#"><span>10</span><span>&nbsp;shares</span></a>

															</div>

															<div class="right odd">

																<a href="#">

																	<span class="ng-binding">2</span>

																	<span class="ng-binding">&nbsp;viewed</span>

																</a>

															</div>

															<div class="clearfix"></div>

														</div>

														<div class="clearfix"></div>

													</div>

													<div class="profileCommentFooter addNew">

														<div class="left">

															<a href="#" class="upvoteIcon">

																<img src="http://swolk.com/assets/pages/img/arrow2_t.png" alt=""/>

															</a>



															<a href="#" class="">

																<img src="http://swolk.com/assets/pages/img/speech_bubble4.png" alt="">

															</a>

															<a href="#" class="ng-scope">

																<img src="http://swolk.com/assets/pages/img/refresh4.png" alt="">

															</a>

															<a href="#">

																<img src="http://swolk.com/assets/pages/img/bookmark.png" alt="">

															</a>

															<a href="#">

																<img src="http://swolk.com/assets/pages/img/log_out.png" alt="">

															</a>

														</div>

														<div class="right">

															<a href="#" class="moreBtnN">

																<i class="fa fa-circle" aria-hidden="true"></i>

																<i class="fa fa-circle" aria-hidden="true"></i>

																<i class="fa fa-circle" aria-hidden="true"></i>

															</a>

														</div>

														<div class="clearfix"></div>

													</div>

												</div>

											</div>

										</div>

										<div class="blockContent ng-scope">

											<div class="profileCommentBox clkProfileCommentBox">

												<div class="cardBoxClk"></div>

												<div class="profileCommentBoxTop">

													<div class="userStatusRow">

														<div class="userStatusImage">

															<a href="/profile/souravdey">

																<img ng-src="http://swolk.com/assets/img/default-profile.png" alt="profile image"/>

															</a>

														</div>



														<div class="userStatusInfo">

															<span class="userStatusInfoTtl withLocation clearfix">

																<a" class="ng-binding" href="/profile/souravdey">Sourav Dey</a>

																<a href="#" class="follwBtn">

																	<i class="fa fa-plus-circle" aria-hidden="true"></i>

																</a>

																<!-- ngIf: post.location -->

															</span>

															<p>

																<span class="clearfix">

																	<small class="ng-binding">&nbsp;Web Developer</small>

																</span>

																<span class="block clearfix"></span>

																<small class="ng-binding">Shared this post 2 hours ago</small>

															</p>

														</div>

														<div class="clearfix"></div>

													</div>

													

													<div class="post_type">

														<p ng-show="post.caption" class="ng-binding ng-hide"></p>

														<div class="catagoryTtl">

															<div class="catagoryTagRow">

																<a href="#" class="catagoryTtlHighLightBlack ng-binding">Business</a>

																<a href="#" class="catagoryTtlHighLight ng-binding">Marketing</a>

															</div>

															<p class="ng-binding">We Need Fun</p>

														</div>

														<div class="postShortDesc ng-hide" ng-show="post.short_description">

															<p ng-show="post.short_description" class="ng-binding ng-hide"></p>

														</div>

														<div>

															<p class="postLink ng-scope">

																<span>

																	<a href="http://weneedfun.com/wp-content/uploads/2015/10/All-Games-2.jpg" target="_blank" class="ng-binding">

																		<i class="fa fa-external-link"></i>

																		weneedfun.com

																	</a>

																</span>                        

															</p><!-- end ngIf: post.source -->

															<div class="uploadImage">

																<a>

																	<img ng-src="http://www.zooborns.com/.a/6a010535647bf3970b0120a66e92aa970c-pi" />

																</a>

															</div>

														</div>

														<div class="profileNewCoverBottomLink ng-scope" ng-if="post.tags">

															<ul>

																<!-- ngRepeat: tag in post.tags -->

															</ul>

														</div><!-- end ngIf: post.tags -->

													</div>

													

													<div class="profileCommentFooter addNew noBorder">

														<div class="likeCommentBar">

															<div class="left odd">

																<a href="javascript:void(0)">

																	<span id="postUpvotes119" class="ng-binding">0</span>

																	<span class="ng-binding">&nbsp;upvote</span>

																</a>

															</div>

															<div class="left odd">

																<a href="#">

																	<span id="postCommentFor_119" class="ng-binding">0</span>

																	<span class="ng-binding">&nbsp;comment</span>

																</a>

															</div>

															<div class="left odd">

																<a href="#"><span>10</span><span>&nbsp;shares</span></a>

															</div>

															<div class="right odd">

																<a href="#">

																	<span class="ng-binding">2</span>

																	<span class="ng-binding">&nbsp;viewed</span>

																</a>

															</div>

															<div class="clearfix"></div>

														</div>

														<div class="clearfix"></div>

													</div>

													<div class="profileCommentFooter addNew">

														<div class="left">

															<a href="#" class="upvoteIcon">

																<img src="http://swolk.com/assets/pages/img/arrow2_t.png" alt=""/>

															</a>



															<a href="#" class="">

																<img src="http://swolk.com/assets/pages/img/speech_bubble4.png" alt="">

															</a>

															<a href="#" class="ng-scope">

																<img src="http://swolk.com/assets/pages/img/refresh4.png" alt="">

															</a>

															<a href="#">

																<img src="http://swolk.com/assets/pages/img/bookmark.png" alt="">

															</a>

															<a href="#">

																<img src="http://swolk.com/assets/pages/img/log_out.png" alt="">

															</a>

														</div>

														<div class="right">

															<a href="#" class="moreBtnN">

																<i class="fa fa-circle" aria-hidden="true"></i>

																<i class="fa fa-circle" aria-hidden="true"></i>

																<i class="fa fa-circle" aria-hidden="true"></i>

															</a>

														</div>

														<div class="clearfix"></div>

													</div>

												</div>

											</div>

										</div>

										<div class="blockContent ng-scope">

											<div class="profileCommentBox clkProfileCommentBox">

												<div class="cardBoxClk"></div>

												<div class="profileCommentBoxTop">

													<div class="userStatusRow">

														<div class="userStatusImage">

															<a href="/profile/souravdey">

																<img ng-src="http://swolk.com/assets/img/default-profile.png" alt="profile image"/>

															</a>

														</div>



														<div class="userStatusInfo">

															<span class="userStatusInfoTtl withLocation clearfix">

																<a" class="ng-binding" href="/profile/souravdey">Sourav Dey</a>

																<a href="#" class="follwBtn">

																	<i class="fa fa-plus-circle" aria-hidden="true"></i>

																</a>

																<!-- ngIf: post.location -->

															</span>

															<p>

																<span class="clearfix">

																	<small class="ng-binding">&nbsp;Web Developer</small>

																</span>

																<span class="block clearfix"></span>

																<small class="ng-binding">Shared this post 2 hours ago</small>

															</p>

														</div>

														<div class="clearfix"></div>

													</div>

													

													<div class="post_type">

														<p ng-show="post.caption" class="ng-binding ng-hide"></p>

														<div class="catagoryTtl">

															<div class="catagoryTagRow">

																<a href="#" class="catagoryTtlHighLightBlack ng-binding">Business</a>

																<a href="#" class="catagoryTtlHighLight ng-binding">Marketing</a>

															</div>

															<p class="ng-binding">We Need Fun</p>

														</div>

														<div class="postShortDesc ng-hide" ng-show="post.short_description">

															<p ng-show="post.short_description" class="ng-binding ng-hide"></p>

														</div>

														<div>

															<p class="postLink ng-scope">

																<span>

																	<a href="http://weneedfun.com/wp-content/uploads/2015/10/All-Games-2.jpg" target="_blank" class="ng-binding">

																		<i class="fa fa-external-link"></i>

																		weneedfun.com

																	</a>

																</span>                        

															</p><!-- end ngIf: post.source -->

															<div class="uploadImage">

																<a>

																	<img ng-src="http://www.zooborns.com/.a/6a010535647bf3970b0120a66e92aa970c-pi" />

																</a>

															</div>

														</div>

														<div class="profileNewCoverBottomLink ng-scope" ng-if="post.tags">

															<ul>

																<!-- ngRepeat: tag in post.tags -->

															</ul>

														</div><!-- end ngIf: post.tags -->

													</div>

													

													<div class="profileCommentFooter addNew noBorder">

														<div class="likeCommentBar">

															<div class="left odd">

																<a href="javascript:void(0)">

																	<span id="postUpvotes119" class="ng-binding">0</span>

																	<span class="ng-binding">&nbsp;upvote</span>

																</a>

															</div>

															<div class="left odd">

																<a href="#">

																	<span id="postCommentFor_119" class="ng-binding">0</span>

																	<span class="ng-binding">&nbsp;comment</span>

																</a>

															</div>

															<div class="left odd">

																<a href="#"><span>10</span><span>&nbsp;shares</span></a>

															</div>

															<div class="right odd">

																<a href="#">

																	<span class="ng-binding">2</span>

																	<span class="ng-binding">&nbsp;viewed</span>

																</a>

															</div>

															<div class="clearfix"></div>

														</div>

														<div class="clearfix"></div>

													</div>

													<div class="profileCommentFooter addNew">

														<div class="left">

															<a href="#" class="upvoteIcon">

																<img src="http://swolk.com/assets/pages/img/arrow2_t.png" alt=""/>

															</a>



															<a href="#" class="">

																<img src="http://swolk.com/assets/pages/img/speech_bubble4.png" alt="">

															</a>

															<a href="#" class="ng-scope">

																<img src="http://swolk.com/assets/pages/img/refresh4.png" alt="">

															</a>

															<a href="#">

																<img src="http://swolk.com/assets/pages/img/bookmark.png" alt="">

															</a>

															<a href="#">

																<img src="http://swolk.com/assets/pages/img/log_out.png" alt="">

															</a>

														</div>

														<div class="right">

															<a href="#" class="moreBtnN">

																<i class="fa fa-circle" aria-hidden="true"></i>

																<i class="fa fa-circle" aria-hidden="true"></i>

																<i class="fa fa-circle" aria-hidden="true"></i>

															</a>

														</div>

														<div class="clearfix"></div>

													</div>

												</div>

											</div>

										</div>

										<div class="blockContent ng-scope">

											<div class="profileCommentBox clkProfileCommentBox">

												<div class="cardBoxClk"></div>

												<div class="profileCommentBoxTop">

													<div class="userStatusRow">

														<div class="userStatusImage">

															<a href="/profile/souravdey">

																<img ng-src="http://swolk.com/assets/img/default-profile.png" alt="profile image"/>

															</a>

														</div>



														<div class="userStatusInfo">

															<span class="userStatusInfoTtl withLocation clearfix">

																<a" class="ng-binding" href="/profile/souravdey">Sourav Dey</a>

																<a href="#" class="follwBtn">

																	<i class="fa fa-plus-circle" aria-hidden="true"></i>

																</a>

																<!-- ngIf: post.location -->

															</span>

															<p>

																<span class="clearfix">

																	<small class="ng-binding">&nbsp;Web Developer</small>

																</span>

																<span class="block clearfix"></span>

																<small class="ng-binding">Shared this post 2 hours ago</small>

															</p>

														</div>

														<div class="clearfix"></div>

													</div>

													

													<div class="post_type">

														<p ng-show="post.caption" class="ng-binding ng-hide"></p>

														<div class="catagoryTtl">

															<div class="catagoryTagRow">

																<a href="#" class="catagoryTtlHighLightBlack ng-binding">Business</a>

																<a href="#" class="catagoryTtlHighLight ng-binding">Marketing</a>

															</div>

															<p class="ng-binding">We Need Fun</p>

														</div>

														<div class="postShortDesc ng-hide" ng-show="post.short_description">

															<p ng-show="post.short_description" class="ng-binding ng-hide"></p>

														</div>

														<div>

															<p class="postLink ng-scope">

																<span>

																	<a href="http://weneedfun.com/wp-content/uploads/2015/10/All-Games-2.jpg" target="_blank" class="ng-binding">

																		<i class="fa fa-external-link"></i>

																		weneedfun.com

																	</a>

																</span>                        

															</p><!-- end ngIf: post.source -->

															<div class="uploadImage">

																<a>

																	<img ng-src="http://www.zooborns.com/.a/6a010535647bf3970b0120a66e92aa970c-pi" />

																</a>

															</div>

														</div>

														<div class="profileNewCoverBottomLink ng-scope" ng-if="post.tags">

															<ul>

																<!-- ngRepeat: tag in post.tags -->

															</ul>

														</div><!-- end ngIf: post.tags -->

													</div>

													

													<div class="profileCommentFooter addNew noBorder">

														<div class="likeCommentBar">

															<div class="left odd">

																<a href="javascript:void(0)">

																	<span id="postUpvotes119" class="ng-binding">0</span>

																	<span class="ng-binding">&nbsp;upvote</span>

																</a>

															</div>

															<div class="left odd">

																<a href="#">

																	<span id="postCommentFor_119" class="ng-binding">0</span>

																	<span class="ng-binding">&nbsp;comment</span>

																</a>

															</div>

															<div class="left odd">

																<a href="#"><span>10</span><span>&nbsp;shares</span></a>

															</div>

															<div class="right odd">

																<a href="#">

																	<span class="ng-binding">2</span>

																	<span class="ng-binding">&nbsp;viewed</span>

																</a>

															</div>

															<div class="clearfix"></div>

														</div>

														<div class="clearfix"></div>

													</div>

													<div class="profileCommentFooter addNew">

														<div class="left">

															<a href="#" class="upvoteIcon">

																<img src="http://swolk.com/assets/pages/img/arrow2_t.png" alt=""/>

															</a>



															<a href="#" class="">

																<img src="http://swolk.com/assets/pages/img/speech_bubble4.png" alt="">

															</a>

															<a href="#" class="ng-scope">

																<img src="http://swolk.com/assets/pages/img/refresh4.png" alt="">

															</a>

															<a href="#">

																<img src="http://swolk.com/assets/pages/img/bookmark.png" alt="">

															</a>

															<a href="#">

																<img src="http://swolk.com/assets/pages/img/log_out.png" alt="">

															</a>

														</div>

														<div class="right">

															<a href="#" class="moreBtnN">

																<i class="fa fa-circle" aria-hidden="true"></i>

																<i class="fa fa-circle" aria-hidden="true"></i>

																<i class="fa fa-circle" aria-hidden="true"></i>

															</a>

														</div>

														<div class="clearfix"></div>

													</div>

												</div>

											</div>

										</div>

										<div class="blockContent ng-scope">

											<div class="profileCommentBox clkProfileCommentBox">

												<div class="cardBoxClk"></div>

												<div class="profileCommentBoxTop">

													<div class="userStatusRow">

														<div class="userStatusImage">

															<a href="/profile/souravdey">

																<img ng-src="http://swolk.com/assets/img/default-profile.png" alt="profile image"/>

															</a>

														</div>



														<div class="userStatusInfo">

															<span class="userStatusInfoTtl withLocation clearfix">

																<a" class="ng-binding" href="/profile/souravdey">Sourav Dey</a>

																<a href="#" class="follwBtn">

																	<i class="fa fa-plus-circle" aria-hidden="true"></i>

																</a>

																<!-- ngIf: post.location -->

															</span>

															<p>

																<span class="clearfix">

																	<small class="ng-binding">&nbsp;Web Developer</small>

																</span>

																<span class="block clearfix"></span>

																<small class="ng-binding">Shared this post 2 hours ago</small>

															</p>

														</div>

														<div class="clearfix"></div>

													</div>

													

													<div class="post_type">

														<p ng-show="post.caption" class="ng-binding ng-hide"></p>

														<div class="catagoryTtl">

															<div class="catagoryTagRow">

																<a href="#" class="catagoryTtlHighLightBlack ng-binding">Business</a>

																<a href="#" class="catagoryTtlHighLight ng-binding">Marketing</a>

															</div>

															<p class="ng-binding">We Need Fun</p>

														</div>

														<div class="postShortDesc ng-hide" ng-show="post.short_description">

															<p ng-show="post.short_description" class="ng-binding ng-hide"></p>

														</div>

														<div>

															<p class="postLink ng-scope">

																<span>

																	<a href="http://weneedfun.com/wp-content/uploads/2015/10/All-Games-2.jpg" target="_blank" class="ng-binding">

																		<i class="fa fa-external-link"></i>

																		weneedfun.com

																	</a>

																</span>                        

															</p><!-- end ngIf: post.source -->

															<div class="uploadImage">

																<a>

																	<img ng-src="http://www.zooborns.com/.a/6a010535647bf3970b0120a66e92aa970c-pi" />

																</a>

															</div>

														</div>

														<div class="profileNewCoverBottomLink ng-scope" ng-if="post.tags">

															<ul>

																<!-- ngRepeat: tag in post.tags -->

															</ul>

														</div><!-- end ngIf: post.tags -->

													</div>

													

													<div class="profileCommentFooter addNew noBorder">

														<div class="likeCommentBar">

															<div class="left odd">

																<a href="javascript:void(0)">

																	<span id="postUpvotes119" class="ng-binding">0</span>

																	<span class="ng-binding">&nbsp;upvote</span>

																</a>

															</div>

															<div class="left odd">

																<a href="#">

																	<span id="postCommentFor_119" class="ng-binding">0</span>

																	<span class="ng-binding">&nbsp;comment</span>

																</a>

															</div>

															<div class="left odd">

																<a href="#"><span>10</span><span>&nbsp;shares</span></a>

															</div>

															<div class="right odd">

																<a href="#">

																	<span class="ng-binding">2</span>

																	<span class="ng-binding">&nbsp;viewed</span>

																</a>

															</div>

															<div class="clearfix"></div>

														</div>

														<div class="clearfix"></div>

													</div>

													<div class="profileCommentFooter addNew">

														<div class="left">

															<a href="#" class="upvoteIcon">

																<img src="http://swolk.com/assets/pages/img/arrow2_t.png" alt=""/>

															</a>



															<a href="#" class="">

																<img src="http://swolk.com/assets/pages/img/speech_bubble4.png" alt="">

															</a>

															<a href="#" class="ng-scope">

																<img src="http://swolk.com/assets/pages/img/refresh4.png" alt="">

															</a>

															<a href="#">

																<img src="http://swolk.com/assets/pages/img/bookmark.png" alt="">

															</a>

															<a href="#">

																<img src="http://swolk.com/assets/pages/img/log_out.png" alt="">

															</a>

														</div>

														<div class="right">

															<a href="#" class="moreBtnN">

																<i class="fa fa-circle" aria-hidden="true"></i>

																<i class="fa fa-circle" aria-hidden="true"></i>

																<i class="fa fa-circle" aria-hidden="true"></i>

															</a>

														</div>

														<div class="clearfix"></div>

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
			</script>

			<script type="text/ng-template" id="topchannel" >
				<div class="channelRow inline">
					<div class="channelContent">
						<div class="channelContentMiddle">
							<div class="channelContentCover">
								<a href="#"><img src="assets/pages/img/channelContentCover1.jpg" alt=""></a>
							</div>
							<div class="channelContentFooter">
								<div class="channelContentUser">
									<a href="#"><img src="assets/pages/img/channelContentUser1.jpg" alt=""></a>
								</div>
								<label class="followBtn channelBtn">
									<input type="checkbox" value="" name="">
									<span>Follow</span>
								</label>
								<p>Lorem ipsum dolor sit amet, consectetur adipiscing elit ipsum dolor sit amet</p>
							</div>
						</div>
					</div>
					<div class="channelContent">
						<div class="channelContentMiddle">
							<div class="channelContentCover">
								<a href="#"><img src="assets/pages/img/channelContentCover2.jpg" alt=""></a>
							</div>
							<div class="channelContentFooter">
								<div class="channelContentUser">
									<a href="#"><img src="assets/pages/img/channelContentUser2.jpg" alt=""></a>
								</div>
								<label class="followBtn channelBtn">
									<input type="checkbox" value="" name="" >
									<span>Follow</span>
								</label>
								<p>Lorem ipsum dolor sit amet, consectetur adipiscing elit ipsum dolor sit amet</p>
							</div>
						</div>
					</div>
					<div class="channelContent">
						<div class="channelContentMiddle">
							<div class="channelContentCover">
								<a href="#"><img src="assets/pages/img/channelContentCover3.jpg" alt=""></a>
							</div>
							<div class="channelContentFooter">
								<div class="channelContentUser">
									<a href="#"><img src="assets/pages/img/channelContentUser3.jpg" alt=""></a>
								</div>
								<label class="followBtn channelBtn">
									<input type="checkbox" value="" name="" checked >
									<span>Follow</span>
								</label>
								<p>Lorem ipsum dolor sit amet, consectetur adipiscing elit ipsum dolor sit amet</p>
							</div>
						</div>
					</div>
					
					<div class="channelContent">
						<div class="channelContentMiddle">
							<div class="channelContentCover">
								<a href="#"><img src="assets/pages/img/channelContentCover1.jpg" alt=""></a>
							</div>
							<div class="channelContentFooter">
								<div class="channelContentUser">
									<a href="#"><img src="assets/pages/img/channelContentUser1.jpg" alt=""></a>
								</div>
								<label class="followBtn channelBtn">
									<input type="checkbox" value="" name="" checked >
									<span>Follow</span>
								</label>
								<p>Lorem ipsum dolor sit amet, consectetur adipiscing elit ipsum dolor sit amet</p>
							</div>
						</div>
					</div>
					<div class="channelContent">
						<div class="channelContentMiddle">
							<div class="channelContentCover">
								<a href="#"><img src="assets/pages/img/channelContentCover2.jpg" alt=""></a>
							</div>
							<div class="channelContentFooter">
								<div class="channelContentUser">
									<a href="#"><img src="assets/pages/img/channelContentUser2.jpg" alt=""></a>
								</div>
								<label class="followBtn channelBtn">
									<input type="checkbox" value="" name="" checked >
									<span>Follow</span>
								</label>
								<p>Lorem ipsum dolor sit amet, consectetur adipiscing elit ipsum dolor sit amet</p>
							</div>
						</div>
					</div>
					<div class="channelContent">
						<div class="channelContentMiddle">
							<div class="channelContentCover">
								<a href="#"><img src="<?php echo asset('assets/pages/img/channelContentCover3.jpg'); ?>" alt=""></a>
							</div>
							<div class="channelContentFooter">
								<div class="channelContentUser">
									<a href="#"><img src="<?php echo asset('assets/pages/img/channelContentUser3.jpg'); ?>" alt=""></a>
								</div>
								<label class="followBtn channelBtn">
									<input type="checkbox" value="" name="" checked >
									<span>Follow</span>
								</label>
								<p>Lorem ipsum dolor sit amet, consectetur adipiscing elit ipsum dolor sit amet</p>
							</div>
						</div>
					</div>
					
					<div class="channelContent">
						<div class="channelContentMiddle">
							<div class="channelContentCover">
								<a href="#"><img src="<?php echo asset('assets/pages/img/channelContentCover1.jpg'); ?>" alt=""></a>
							</div>
							<div class="channelContentFooter">
								<div class="channelContentUser">
									<a href="#"><img src="<?php echo asset('assets/pages/img/channelContentUser1.jpg'); ?>" alt=""></a>
								</div>
								<label class="followBtn channelBtn">
									<input type="checkbox" value="" name="" checked >
									<span>Follow</span>
								</label>
								<p>Lorem ipsum dolor sit amet, consectetur adipiscing elit ipsum dolor sit amet</p>
							</div>
						</div>
					</div>
					<div class="channelContent">
						<div class="channelContentMiddle">
							<div class="channelContentCover">
								<a href="#"><img src="assets/pages/img/channelContentCover2.jpg" alt=""></a>
							</div>
							<div class="channelContentFooter">
								<div class="channelContentUser">
									<a href="#"><img src="assets/pages/img/channelContentUser2.jpg" alt=""></a>
								</div>
								<label class="followBtn channelBtn">
									<input type="checkbox" value="" name="" checked >
									<span>Follow</span>
								</label>
								<p>Lorem ipsum dolor sit amet, consectetur adipiscing elit ipsum dolor sit amet</p>
							</div>
						</div>
					</div>
					<div class="channelContent">
						<div class="channelContentMiddle">
							<div class="channelContentCover">
								<a href="#"><img src="<?php echo asset('assets/pages/img/channelContentCover3.jpg'); ?>" alt=""></a>
							</div>
							<div class="channelContentFooter">
								<div class="channelContentUser">
									<a href="#"><img src="<?php echo asset('assets/pages/img/channelContentUser3.jpg'); ?>" alt=""></a>
								</div>
								<label class="followBtn channelBtn">
									<input type="checkbox" value="" name="" checked >
									<span>Follow</span>
								</label>
								<p>Lorem ipsum dolor sit amet, consectetur adipiscing elit ipsum dolor sit amet</p>
							</div>
						</div>
					</div>
				</div>
			</script>
		</div>
	</div>
</div>
<script type="text/javascript" src="<?php echo asset('assets/plugins/bx-slider/flickity.pkgd.js'); ?>"></script>

