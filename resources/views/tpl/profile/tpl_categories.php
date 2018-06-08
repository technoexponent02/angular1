<div class="row">
    <div class="col-md-10 col-lg-10 col-md-offset-1">
        <div class="followingContainer followingCatg">
            <div class="postingformtype">
                <div class="followingHeader">
                    <ul>
                        <li><a href="#" ng-click="openTab('following',userData.id);">People</a></li>
                        <li><a href="javascript:;"  class="active" >Categories</a></li>
                    </ul>
                </div>
                <div class="followingBody" id="categoriesTabVW">

                    <div class="profileCommentBox profileCommentBoxNW" ng-repeat="cat in category | orderBy:'category_name'">
                        <div class="profileCommentBoxTop">
                            <div class="followingRWclick"></div>
                            <div class="followingRowImg">
                                <span class="categoryFollowClk"></span>
                            </div>
                            <label class="followBtn">
                                <a href="#" ng-click="followCategory(cat.id)" ng-if="isFollowingCategory.indexOf(cat.id) == -1 && user.guest== 0"><span>FOLLOW</span></a>

                                <a href="#" ng-click="unfollowCategory(cat.id)" ng-if="isFollowingCategory.indexOf(cat.id) != -1"><span class="ico">FOLLOWING</span></a>

                            </label>
                            <label class="followBtn">
                                <a href="#" ng-click="redirecToLogin();" ng-if="isFollowingCategory.indexOf(cat.id) == -1 && user.guest!= 0"><span>FOLLOW</span></a>

                            </label>
                            <div class="followingRow">
                                <div class="followingRowInfo">
                                    <div class="followingRowInfoMiddle">
                                        <div class="followingRowInfoMiddleTop">
									<span class="followingRowInfoTtl">
									<a href="javascript:void(0)">{{cat.category_name}}</a></span>
                                            <!-- <p>&nbsp;Web Developer</p> -->
                                        </div>
                                    </div>
                                    <div class="clearfix"></div>
                                </div>
                                <div class="clearfix"></div>
                            </div>
                            <div class="followingSubRow">
                                <div class="followingRow" ng-repeat="subcat in cat.child_cat | orderBy:'category_name'">
                                    <div class="followingRowInfo">
                                        <div class="subFollowCatg">
                                            <div class="followingRowInfoMiddle">
                                                <div class="followingRowInfoMiddleTop">
											<span class="followingRowInfoTtl">
											<a href="">{{subcat.category_name}}</a></span>
												<label class="followBtn" ng-if="user.guest== 0">
													<a href="#" ng-click="followCategory(subcat.id)" ng-if="isFollowingCategory.indexOf(subcat.id) == -1"><span>FOLLOW</span></a>

													<a href="#" ng-click="unfollowCategory(subcat.id)" ng-if="isFollowingCategory.indexOf(subcat.id) != -1"><span class="ico">FOLLOWING</span></a>

												</label>
												<label class="followBtn" ng-if="user.guest!= 0">
													<a href="#" ng-click="redirecToLogin();"><span>FOLLOW</span></a>
												</label>
                                                </div>
                                            </div>
                                        </div>
                                        <div class="clearfix"></div>
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