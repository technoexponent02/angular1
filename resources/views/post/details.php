<style type="text/css">
	body{background:#f6f9fa !important;}	
	.header{background:#fff !important;}
</style>
<div class="container-fluid padding-25 sm-padding-10 postDetailsPG" style="padding-top:0px;">

    <div class="cardDetailsPG_modal modal"  ng-class="{'detPostArt':post.post_type == 3}" id="myModal2">
        <div class="detailsTopbar">
            <div class="mobileModalHeader clearfix">
                <div style="display:block; position:absolute; top:0; left:0; width:100%; height:8px; overflow:hidden; z-index:999;" ng-if="post.post_type == 3">
                    <div class="mobileBarLong2"></div>
                </div>
                <a href="javascript:void(0)" class="mobileHeadbookMark saveDel"
                   ng-if="post.created_by!=user.id && post.child_post_user_id!=user.id && user.guest== 0">
                    <img src="assets/pages/img/bookmark.png" ng-if="post.isBookMark=='N'"
                         book-mark="bookMarkProcess(post.id,'M');" alt="" class="saveIcon"/>
                    <img src="assets/pages/img/bookmark-fill.png" ng-if="post.isBookMark=='Y'"
                         book-mark="bookMarkProcess(post.id,'M');" alt="" class="savedIcon">
                </a>
                <a class="mobileHeadbookMark saveDel" ng-if="user.guest!= 0" ng-click="redirecToLogin();">
                    <img src="assets/pages/img/bookmark.png" alt="" class="saveIcon"/>
                </a>
                <div class="mobileModalHeaderMid">
					<span class="postOnlineUsers">
						<i class="fa fa-circle"></i>
						{{totalPeopleHere}} online
					</span>
                </div>
            </div>
        </div>
        <div class="modal-body">
            <div class="row">
                <div class="col-md-9 col-sm-8 col-xs-12">
                    <div class="leftContainerHeight">
                        <div class="container-xs-height">
                            <div class="row-xs-height">
                                <div class="profileNewLeft">
                                    <div class="userStatusRow smUserStatus mobileModalUser">
                                        <div class="userStatusImage" ng-if="createdProfileImage">
                                            <a ui-sref="account({ username: createdUsername })"
                                               style="background:url({{createdProfileImage}}) no-repeat;"></a>
                                        </div>
                                        <div class="userStatusImage {{createdUserColor}}" ng-if="!createdProfileImage">
                                            <a ui-sref="account({ username: createdUsername })">
                                                <span class="txt">{{createdUserFirstName.charAt(0)}}</span>
                                            </a>
                                        </div>
                                        <div class="userStatusInfo">
                                            <div class="userStatusInfoLeft">
												<span class="userStatusInfoTtl">
													<a class="ng-binding" href="">{{createdUserFirstName}} {{createdUserLastName}}</a>
												</span>
                                                <p>
                                                    <small style="display:none;">Shared this post</small>
                                                    <small>
                                                        <time am-time-ago="post.created_at | amUtc | amLocal"></time>
                                                    </small>
                                                    <small class="clearfix block">
														{{createdUserAboutMe | limitUserAbout}}
													</small>
                                                </p>
                                            </div>
                                            <div class="profileRightLink"
                                                 ng-if="createdUserId!=user.id && user.guest== 0"
                                                 allfollowuser="followUser(createdUserId,'C','followed')">
                                                <label class="followBtn"
                                                       ng-if="userFollowing.indexOf(createdUserId)!=-1">
                                                    <span class="ico">FOLLOWING</span>
                                                </label>
                                                <label class="followBtn"
                                                       ng-if="userFollowing.indexOf(createdUserId)==-1">
                                                    <span>FOLLOW</span>
                                                </label>
                                            </div>
                                            <div class="profileRightLink"
                                                 ng-if="createdUserId!=user.id &&  user.guest!= 0"
                                                 allfollowuser="followUser(createdUserId,'C','followed')">
                                                <label class="followBtn"
                                                       ng-if="userFollowing.indexOf(createdUserId)!=-1"
                                                       ng-click="redirecToLogin();">
                                                    <span class="ico">FOLLOWING</span>
                                                </label>
                                                <label class="followBtn"
                                                       ng-if="userFollowing.indexOf(createdUserId)==-1"
                                                       ng-click="redirecToLogin();">
                                                    <span>FOLLOW</span>
                                                </label>
                                            </div>
                                            <div class="clearfix"></div>
                                        </div>
                                        <div class="clearfix"></div>
                                    </div>
                                    <div class="profileNewCoverFooter" ng-class="{'withreadtime': post.post_type == 3 && post.time_needed!=0}">
                                        <div class="catgSubcatg" ng-if="(post.category.category_name || post.sub_category.category_name)">
                                            <a href="{{ '/tag/' + post.category.category_name_url }}"
                                               ng-if="post.category.category_name"
                                               class="catagoryTtlHighLight">{{post.category.category_name}}</a>

                                            <a href="{{ '/tag/' + post.sub_category.subcategory_name_url }}"
                                               ng-if="post.sub_category.category_name" class="catagoryTtlHighLight">{{post.sub_category.category_name}}</a>
                                        </div>
                                        <ul>
                                            <li><a href="javascript:void(0)">
                                                    <strong id="modalpostUpvotes{{post.id}}">
                                                        {{(post.upvotes-post.downvotes)>0 ?
                                                        '+'+(post.upvotes-post.downvotes) :
                                                        (post.upvotes-post.downvotes) | thousandSuffix
                                                        }}
                                                    </strong>
                                                    {{ upvoteDownvoteTxt(post.upvotes - post.downvotes) }}</a>
                                            </li>
                                            <li ng-if="post.allow_comment==1">
                                                <a href="javascript:void(0)">
                                                    <strong>{{post.postParentComment | thousandSuffix}}</strong>
                                                    {{post.postParentComment > 1 ? 'comments' : 'comment'}}</a>
                                            </li>
                                            <li ng-if="post.allow_share==1">
                                                <a href="javascript:void(0)">
                                                    <strong>{{post.totalShare | thousandSuffix }}</strong>
                                                    {{ post.totalShare > 1 ? 'shares' : 'share' }}</a>
                                            </li>
                                            <li ng-if="post.totalPostViews>0">
                                                <a href="javascript:void(0)">
                                                    <strong>{{post.totalPostViews | thousandSuffix }}</strong> &nbsp;{{
                                                    showPostViewTxt(post, 'details') }}</a>
                                            </li>
                                            <li class="right" ng-if="post.totalBookMark > 0">
                                                <a href="javascript:void(0)" class="saveBtn saveDel">
                                                    <strong>{{post.totalBookMark | thousandSuffix}} </strong>
                                                    Save
                                                </a>
                                            </li>
                                            <li ng-if="post.points>=1">
                                                <a href="javascript:void(0)"><strong>
                                                        {{post.points>=1 ? post.points : 0 | thousandSuffix}}</strong>
                                                    Post Points</a></li>
                                            <li>
													<span class="postOnlineUsers">
														<i class="fa fa-circle"></i>
														{{totalPeopleHere}} online
													</span>
                                            </li>
                                        </ul>
                                        <div class="postTime"
                                             ng-if="post.post_type == 3 && post.time_needed!=0">
                                            {{post.time_needed}}&nbsp;min read
                                        </div>
                                        <div class="clearfix"></div>
                                    </div>
									<div class="answering">
										<div ng-repeat="tag in ::post.tags" ng-if="post.created_at > tag.question_tag_created_at && post.post_type!=6">
                                            Answering to: <a href="/questions/{{::tag.tag_name}}" >{{ tag.question_tag }}</a>
                                            <div class="tlflbtn">   
                                                <label class="followBtn">
                                                    <a ng-click="tagFollowUnfollow(tag.tag_name,tag.id)" class="followBtn ">
                                                        <span style="display:{{tag.tagFollowStatus==0 ? 'block':'none'}}" id="follow_{{tag.id}}">FOLLOW</span>
                                                        <span class="ico" style="display:{{tag.tagFollowStatus==1 ? 'block':'none'}}" id="following_{{tag.id}}">FOLLOWING</span>
                                                    </a>    
                                                </label>
                                            </div>
                                        </div>
									</div>

                                    <h2 class="postDetCaption" ng-if="post.caption">
                                        <!-- <span>{{postUserName}} : </span>-->"<strong
                                                ng-bind-html="post.caption | markupHTMLTags"></strong>"
												<!-- <span ng-if="::(post.post_type=='6')" class="tlflbtn">    
													<label class="followBtn" ng-if="::(post.user.id!=user.id && user.guest==0)"
														allfollowuser="followUser(post.user.id,'C','followed');">
														<span>FOLLOW</span>
														<span class="ico" style="display:none;">FOLLOWING</span>
													</label>
												</span> -->
                                        <a href="/place?{{post.place_url}}"
                                           class="cardLoc"
                                           ng-if="post.place_url && post.post_type==5">
                                            <img src="assets/pages/img/location1.png" alt="Location"/>
                                            <span data-toggle="tooltip" ui-jq="tooltip"
                                                  data-original-title="{{post.location}}">{{post.location}}</span>
                                            <span class="distanceSH"
                                                  ng-if="post.hasOwnProperty('distance') && post.distance!=null && post.location && post.place_url && post.post_type==5 && showDistance == true">
                                                  - {{post.distance | formatDistance}} away
                                            </span>
                                        </a>
                                    </h2>
                                    <span class="timeSh" ng-if="post.post_type == 5">
										 <small ng-if="showElapsedTime(post.created_at)"><!--Shared this post -->
                                            <time am-time-ago="post.created_at | amUtc | amLocal"></time>
                                        </small>
                                        <small ng-if="!showElapsedTime(post.created_at)"><!--Shared this post  -->
                                            <time>{{ post.created_at | amDateFormat:'DD MMM, YYYY - HH:mm' }}</time>
                                        </small>
									</span>

                                    <div class="profileCommentBoxTop useForMobile no-top-border">
                                        <div class="userStatusRow">
                                            <div class="userStatusImage">
                                                <a ui-sref="account({ username: user.username })"
                                                   style="background:url({{user.thumb_image_url}}) no-repeat;"></a>
                                            </div>
                                            <div class="userStatusInfo">
                                                <div class="userStatusInfoLeft">
													<span class="userStatusInfoTtl">
															<a ui-sref="account({ username: user.username })">{{user.first_name + ' ' + user.last_name}}</a>
														</span>
                                                    <p>
                                                        <span class="uoccupation" ng-if="user.occupation">{{ user.occupation }}</span>
                                                        <span class="clearfix block">
															<small>&nbsp;{{user.about_me | limitUserAbout}}</small>
														</span>
                                                        <small>Shared this post {{created_at | elapsed}}</small>
                                                    </p>
                                                </div>
                                                <div class="clearfix"></div>
                                            </div>
                                            <div class="clearfix"></div>
                                        </div>
                                    </div>

                                    <!-- start image post -->
                                    <div ng-if="post.post_type == 1" class="postTypeBlock">
                                        <div class="uploadImage">
                                            <img ng-src="{{post.image}}" alt="post image">
                                        </div>
										<p class="postLink" ng-if="::(post.source && post.source!==null)">
											<span>
												<a href="{{ post.source }}" target="_blank">
													<i class="fa fa-external-link"></i>
														{{ post.source | domainFilter }}
												</a>
											</span>
                                        </p>
                                    </div>
                                    <!-- end image post -->

                                    <!-- start video post -->
                                    <div ng-if="post.post_type == 2" class="postTypeBlock">
                                    <?php /* ng-mouseover="detailsvideoPlay(post,postCardIndex,'M');"
										ng-mouseout="detailsvideoPause(post,postCardIndex,'M');">*/ ?>
									<div class="uploadImage" ng-if="post.embed_code">
                                        <div class="uploadVidPreview">

                                            <!-- YOUTUBE -->
                                            <youtube ng-if="post.embed_code_type =='youtube'" videoid="{{post.videoid}}" type="D"></youtube>
                                            <!-- VIMEO -->

                                            <div ng-if="post.embed_code_type =='vimeo'">
                                                <vimeo-video
                                                    type="D"
                                                    id="vo-{{post.cardID}}"
                                                    vid="{{post.videoid}}">
                                                </vimeo-video>
                                            </div>
                                            <!-- DAILYMOTION -->
                                            <div ng-if="post.embed_code_type =='dailymotion'"
                                                 id="do-{{post.cardID}}">
                                                <daily-motion
                                                    type="D"
                                                    id="dmplayer{{post.cardID}}"
                                                    vid="{{::post.videoid}}">
                                                </daily-motion>
                                            </div>
                                        </div>
                                    </div>
                                    <div class="uploadImage" ng-if="post.video">
                                        <div class="uploadLocVidPreview">
                                            <manual-video url='{{post.video | trustUrl}}'
                                                          postcardid="{{post.id}}"
                                                          childpostid="{{post.child_post_id}}"
                                                          posttype="{{post.post_type}}"
                                                          type="M"
                                                          id="cardVideo-{{post.cardID}}">
                                            </manual-video>
                                        </div>
                                    </div>
									<p class="postLink" ng-if="post.source &&  post.source!='http://undefined'">
										<span>
										   <a href="{{ post.embed_code ? post.embed_code : post.source }}"
											  target="_blank">
											<i class="fa fa-external-link"></i>
											{{ post.source | domainFilter }}  
											</a>
										</span>
                                    </p>
                                </div>
                                <!-- end video post -->

                                <!-- start article post -->
                                <div ng-if="post.post_type == 3" class="postTypeBlock">
                                    <!-- <p class="postLink" ng-if="::(post.source && post.source!==null)">
											<span>
												<a href="{{ post.external_link ? post.external_link : post.source }}"
                                                   target="_blank">
												<i class="fa fa-external-link"></i>
												{{ post.source | domainFilter }}
												</a>
											</span>
                                    </p>
									<div ng-if="post.source" class="postLinkBtn sm lft">
										<a href="{{(post.external_link ? post.external_link : post.source)}}" target="_blank" class="btn btn-default btn-sm postLinkBtnTxt">
											This post's content is imported from and belongs to <strong>{{(post.source | domainFilter)}}</strong>
										</a>
									</div>-->
                                    <div class="uploadImage" ng-if="post.image">
                                        <a href="javascript:void(0)">
                                            <img ng-src="{{post.image}}">
                                        </a>
                                    </div>
                                    <div class="catagoryTtl">
                                        <div class="catagoryTagRow">
                                            <p>{{post.title}}
                                                <a href="/place?{{post.place_url}}"
                                                   class="cardLoc" ng-if="post.place_url">
                                                    <img src="assets/pages/img/location1.png"
                                                         alt="Location">
                                                    <span data-toggle="tooltip" ui-jq="tooltip"
                                                          data-original-title="{{post.location}}">{{post.location}}
												</span>
                                                </a>
                                                <span class="distanceSH"
                                                      ng-if="post.hasOwnProperty('distance') && post.distance!=null && showDistance == true">
	                                                 - {{post.distance | formatDistance}} away
	                                            </span>
                                            </p>
                                            <span class="timeSh" ng-if="post.post_type != 5"><time>{{ post.created_at | amDateFormat:'DD MMM, YYYY - HH:mm' }}</time></span>
                                        </div>
                                    </div>
                                    <div class="uploadBox articleContent fr-view"
                                         ng-bind-html="post.content | markupHTMLTags "></div>
                                </div>
                                <!-- end article post -->
                                <!-- start link post -->
                                <div ng-if="post.post_type == 4" class="postTypeBlock"
                                <?php /* ng-mouseover="detailsvideoPlay(post,postCardIndex,'M');"
										ng-mouseout="detailsvideoPause(post,postCardIndex,'M');">*/ ?>
                                <!-- Linked video -->
                                <div class="uploadImage" ng-if="post.embed_code">
                                    <div class="uploadVidPreview">
                                        <!-- YOUTUBE -->
                                        <youtube ng-if="post.embed_code_type =='youtube'" videoid="{{post.videoid}}" type="D"></youtube>
                                        <!-- VIMEO -->

                                        <div ng-if="post.embed_code_type =='vimeo'">
                                            <vimeo-video
                                                    type="D"
                                                    id="vo-{{post.cardID}}"
                                                    vid="{{post.videoid}}">
                                            </vimeo-video>
                                        </div>
                                        <!-- DAILYMOTION -->
                                        <div ng-if="post.embed_code_type =='dailymotion'"
                                             id="do-{{post.cardID}}">
                                            <daily-motion
                                                    type="D"
                                                    id="dmplayer{{post.cardID}}"
                                                    vid="{{::post.videoid}}">
                                            </daily-motion>
                                        </div>
                                        <!-- OTHERS -->
                                        <iframe ng-if="post.embed_code_type =='unsupported'"
                                                class="iframeTag"
                                                src="{{ post.embed_code | trustUrl }} "
                                                imageonload="removePostLoading('postCard'+$index)"
                                                height="200"
                                                webkitallowfullscreen mozallowfullscreen
                                                allowfullscreen ad_pause>
                                        </iframe>

                                    </div>
                                </div>
                                <!-- Linked image -->
                                <div class="uploadImage" ng-if="!post.embed_code && post.image">
                                    <a href="javascript:void(0)">
                                        <img ng-src="{{post.image}}">
                                    </a>
                                </div>
                            </div>
                            <!-- start Status post -->
                            <div ng-if="post.post_type == 5"
                            <?php /* ng-mouseover="detailsvideoPlay(post,postCardIndex,'M');"
                                             ng-mouseout="detailsvideoPause(post,postCardIndex,'M');"*/ ?>>
                            <!-- Linked video -->
                            <div class="uploadImage" ng-if="post.embed_code">
                                <div class="uploadVidPreview">
                                    <!-- YOUTUBE -->
                                    <youtube ng-if="post.embed_code_type =='youtube'" videoid="{{post.videoid}}" type="D"></youtube>
                                    <!-- VIMEO -->

                                    <div ng-if="post.embed_code_type =='vimeo'">
                                        <vimeo-video
                                                type="D"
                                                id="vo-{{post.cardID}}"
                                                vid="{{post.videoid}}">
                                        </vimeo-video>
                                    </div>
                                    <!-- DAILYMOTION -->
                                    <div ng-if="post.embed_code_type =='dailymotion'"
                                         id="do-{{post.cardID}}">
                                        <daily-motion
                                                type="D"
                                                id="dmplayer{{post.cardID}}"
                                                vid="{{post.videoid}}">
                                        </daily-motion>
                                    </div>
                                    <!-- OTHERS -->
                                    <iframe ng-if="post.embed_code_type =='unsupported'"
                                            class="iframeTag"
                                            src="{{ post.embed_code | trustUrl }} "
                                            imageonload="removePostLoading('postCard'+$index)"
                                            height="200"
                                            webkitallowfullscreen mozallowfullscreen
                                            allowfullscreen ad_pause>
                                    </iframe>
                                </div>
                            </div>
                            <!-- Uploaded video -->
                            <div class="uploadImage" ng-if="post.video">
                                <div class="uploadLocVidPreview">
                                    <manual-video
                                            url='{{post.video | trustUrl}}'
                                            postcardid="{{post.id}}"
                                            childpostid="{{post.child_post_id}}"
                                            posttype="{{post.post_type}}"
                                            type="M"
                                            id="cardVideo-{{post.cardID}}">
                                    </manual-video>
                                </div>
                            </div>
                            <!-- Linked image -->
                            <div class="uploadImage" ng-if="!post.embed_code && post.image">
                                <a data-toggle="modal"
                                   ng-click="showPostDetails(post.id,0)"
                                   data-target="#myModal{{ post.id }}">
                                    <img ng-src="{{post.image}}">
                                </a>
                            </div>
							 <p class="postLink" ng-if="::(post.source && post.source!==null)">
								<span>
									<a href="{{ post.embed_code ? post.embed_code : post.source }}"
									   target="_blank">
									<i class="fa fa-external-link"></i>
									{{ post.source | domainFilter }}
									</a>
								</span>
                            </p>
                        </div>


                        <!-- start question post -->

                         <!-- start Status post -->
                         <div ng-if="post.post_type == 6"
                            <?php /* ng-mouseover="detailsvideoPlay(post,postCardIndex,'M');"
                                             ng-mouseout="detailsvideoPause(post,postCardIndex,'M');"*/ ?>>

                            <!-- Linked video -->
                            <div class="uploadImage" ng-if="post.embed_code">
                                <div class="uploadVidPreview">
                                    <!-- YOUTUBE -->
                                    <youtube ng-if="post.embed_code_type =='youtube'" videoid="{{post.videoid}}" type="D"></youtube>
                                    <!-- VIMEO -->

                                    <div ng-if="post.embed_code_type =='vimeo'">
                                        <vimeo-video
                                                type="D"
                                                id="vo-{{post.cardID}}"
                                                vid="{{post.videoid}}">
                                        </vimeo-video>
                                    </div>
                                    <!-- DAILYMOTION -->
                                    <div ng-if="post.embed_code_type =='dailymotion'"
                                         id="do-{{post.cardID}}">
                                        <daily-motion
                                                type="D"
                                                id="dmplayer{{post.cardID}}"
                                                vid="{{post.videoid}}">
                                        </daily-motion>
                                    </div>
                                    <!-- OTHERS -->
                                    <iframe ng-if="post.embed_code_type =='unsupported'"
                                            class="iframeTag"
                                            src="{{ post.embed_code | trustUrl }} "
                                            imageonload="removePostLoading('postCard'+$index)"
                                            height="200"
                                            webkitallowfullscreen mozallowfullscreen
                                            allowfullscreen ad_pause>
                                    </iframe>
                                </div>
                            </div>
                            <!-- Uploaded video -->
                            <div class="uploadImage" ng-if="post.video">
                                <div class="uploadLocVidPreview">
                                    <manual-video
                                            url='{{post.video | trustUrl}}'
                                            postcardid="{{post.id}}"
                                            childpostid="{{post.child_post_id}}"
                                            posttype="{{post.post_type}}"
                                            type="M"
                                            id="cardVideo-{{post.cardID}}">
                                    </manual-video>
                                </div>
                            </div>
                            <!-- Linked image -->
                            <div class="uploadImage" ng-if="!post.embed_code && post.image">
                                <a data-toggle="modal"
                                   ng-click="showPostDetails(post.id,0)"
                                   data-target="#myModal{{ post.id }}">
                                    <img ng-src="{{post.image}}">
                                </a>
                            </div>
							<p class="postLink" ng-if="::(post.source && post.source!==null)">
								<span>
									<a href="{{ post.embed_code ? post.embed_code : post.source }}"
									   target="_blank">
									<i class="fa fa-external-link"></i>
									{{ post.source | domainFilter }}
									</a>
								</span>
                            </p>
                        </div>

                        <div class="postTypeBlock">
                            <div class="catagoryTtl">
                                <div class="catagoryTagRow"
                                     ng-if="post.post_type==1 || post.post_type==2 || post.post_type==4 ||post.post_type==6 ">
                                    <p>{{post.title}}
                                        <a href="/place?{{post.place_url}}"
                                           class="cardLoc" ng-if="post.location">
                                            <img src="assets/pages/img/location1.png"
                                                 alt="Location">
                                            <span data-toggle="tooltip" ui-jq="tooltip"
                                                  data-original-title="{{post.location}}">{{post.location}}</span>
                                        </a>
                                        <span class="distanceSH"
                                              ng-if="post.hasOwnProperty('distance') && post.distance!=null && showDistance == true">
                                                         - {{post.distance | formatDistance}} away
                                                    </span>
                                    </p>
                                    <span class="timeSh" ng-if="post.post_type != 5">
														 <small ng-if="showElapsedTime(post.created_at)"><!--Shared this post -->
	                                                        <time am-time-ago="post.created_at | amUtc | amLocal"></time>
	                                                    </small>
	                                                    <small ng-if="!showElapsedTime(post.created_at)"><!--Shared this post  -->
	                                                        <time>{{ post.created_at | amDateFormat:'DD MMM, YYYY - HH:mm' }}</time>
	                                                    </small>
													</span>
                                </div>
                            </div>
                        </div>
                        <div class="postTypeBlock" ng-if="post.short_description">
                            <div class="uploadBox articleContent">
                                <p>{{post.short_description}}</p>
                            </div>
                        </div>
                        <div ng-if="post.post_type == 4" class="postTypeBlock">
                            <div ng-if="::(post.source && post.source!==null)" class="postLinkBtn">

                                <a href="{{post.external_link}}" target="_blank"
                                   viewpost="externalLink(post.id,post.child_post_id);"
                                   class="btn btn-default btn-sm postLinkBtnTxt">
                                    Discover More @
                                    {{post.source | domainFilter}}
                                </a>
                            </div>
                        </div>
						<div ng-if="post.post_type == 3 && post.source" class="postLinkBtn sm" style="margin-bottom:10px;">
							<a href="{{(post.external_link ? post.external_link : post.source)}}" target="_blank" class="btn btn-default btn-sm postLinkBtnTxt">
                                This post's content is imported from and belongs to <strong>{{ post.source | domainFilter }}</strong>
							</a>
						</div>
                        <div class="profileNewCoverBottom" ng-class="{'nopostTypeBlock': post.post_type !== 4}">
                            <div class="profileNewCoverBottomLink postdetails p-t-10 p-b-10">
                                <ul>
                                    <li ng-repeat="tag in post.tags">
                                        <!-- <a href="{{ '/tag/' + tag.tag_name }}">#{{tag.tag_name}}</a> -->
                                        <div ng-if="tag.question_tag_created_at ? post.created_at < tag.question_tag_created_at :'true'">
                                            <!-- <a href="/tag/{{::tag.tag_name}}" ng-if=":: tag.question_tag!=post.caption">#{{::tag.tag_name | tagreplace}}</a> tag text change (22-3-18)-->
                                            <a href="/tag/{{::tag.tag_name}}" ng-if=":: tag.question_tag!=post.caption">#{{::tag.tag_text }}</a> <!-- tag text change (22-03-18) -->
                                        </div>
                                                                
                                        <a href="/questions/{{::tag.tag_name}}" class="questionButtn" ng-if=":: tag.question_tag==post.caption" >{{tag.tagCount=='0'|| !tag.tagCount ?'No answer yet': tag.tagCount=='1'? '1 answer': tag.tagCount+' answers' }}</a>
                                    </li>
                                </ul>
                            </div>
                            <div class="profileNewCoverLinkBtn profileRightLink profilergtNewLinks clearfix">
                                <div class="newShrBtns" ng-if="user.guest== 0">
                                    <div class="middle">
                                        <a ng-click="" class="upvoteIcon"
                                           data-toggle="tooltip"
                                           ui-jq="tooltip"
                                           data-original-title="{{ post.isUpvote == 'N' ? 'Upvote' : 'Remove Upvote' }}"
                                           upvotes="doUpvotes(post.id,post.child_post_id,'M');">
                                            <img src="/assets/pages/img/arrow2_t.png" ng-if="post.isUpvote == 'N'"/>
                                            <img src="/assets/pages/img/arrow2_t_sl.png" ng-if="post.isUpvote == 'Y'"/>
                                            <span id="postUpvotes{{post.id}}" ng-if="(post.upvotes-post.downvotes)>0">
														{{(post.upvotes-post.downvotes)>0 ? '+'+(post.upvotes-post.downvotes) : (post.upvotes-post.downvotes) | thousandSuffix }}
											</span>
                                            <span ng-if="(post.upvotes-post.downvotes)>0">{{ upvoteDownvoteTxt(post.upvotes-post.downvotes) }}</span>
                                        </a>
                                        <a class="upvoteIcon"
                                           ng-if="post.child_post_user_id!=user.id && post.allow_share==1"
                                           ng-click="sharePopUp(post.id,post.child_post_user_id,post.child_post_id,0);">
                                            <img src="/assets/pages/img/refresh4.png"/>
                                            <span ng-if="post.normalShare>0">{{post.normalShare | thousandSuffix }}</span>
                                        </a>
                                        <a class="upvoteIcon lite"
                                           ng-if="post.child_post_user_id==user.id && post.allow_share==1">
                                            <img src="/assets/pages/img/refresh4.png"/>
                                            <span ng-if="post.normalShare>0">{{post.normalShare | thousandSuffix }}</span>
                                        </a>
                                        <a href="javascript:void(0)" social-sharing="facebook(post,'M');" ng-if="post.allow_share==1"
                                           class="upvoteIcon">
                                            <img src="/assets/pages/img/fb-icon.png" alt=""/>
                                            <span ng-if="post.totalFBshare>0">{{post.totalFBshare | thousandSuffix }}</span>
                                        </a>
                                        <a href="javascript:void(0)" social-sharing="twitter(post,'M');" class="upvoteIcon"
                                           ng-if="post.allow_share==1">
                                            <img src="/assets/pages/img/twt-icon.png"
                                                 alt=""/>
                                            <span ng-if="post.totalTwittershare>0">{{post.totalTwittershare | thousandSuffix }}</span>
                                        </a>
                                        <a href="javascript:void(0)"
                                           class="upvoteIcon "
                                           ng-if="post.created_by!=user.id && post.child_post_user_id!=user.id"
                                           book-mark="bookMarkProcess(post.id,'M');">
                                            <img ng-if="post.isBookMark=='N'" src="/assets/pages/img/bookmark.png"/>
                                            <img ng-if="post.isBookMark=='Y'" src="/assets/pages/img/bookmark-fill.png"/>
                                        </a>
                                        <div class="cardSmNav last">
                                            <post-card-menu></post-card-menu>
                                            <a href="javascript:void(0)" class="upvoteIcon"
                                               postcard="report(post);">
                                                <i class="fa fa-circle"
                                                   aria-hidden="true"></i>
                                                <i class="fa fa-circle"
                                                   aria-hidden="true"></i>
                                                <i class="fa fa-circle"
                                                   aria-hidden="true"></i>
                                            </a>
                                        </div>
                                    </div>
                                </div>
                                <div class="newShrBtns" ng-if="user.guest!= 0">
                                    <div class="middle">
                                        <a ng-click="redirecToLogin();" class="upvoteIcon">
                                            <img src="/assets/pages/img/arrow2_t.png" alt=""/>
                                            <span id="postUpvotes{{post.id}}">
														{{(post.upvotes-post.downvotes)>0 ? '+'+(post.upvotes-post.downvotes) : (post.upvotes-post.downvotes) | thousandSuffix }}
															
															</span>
                                            <span>{{ upvoteDownvoteTxt(post.upvotes-post.downvotes) }}</span>
                                        </a>
                                        <a ng-click="redirecToLogin();" class="upvoteIcon"
                                        >
                                            <img src="assets/pages/img/refresh4.png" alt=""/>
                                            <span>{{post.normalShare | thousandSuffix }}</span>
                                        </a>
                                        <a social-sharing="facebook(post,'M');" ng-if="post.allow_share==1"
                                           class="upvoteIcon">
                                            <img src="assets/pages/img/fb-icon.png" alt=""/>
                                            <span>{{post.totalFBshare | thousandSuffix }}</span>
                                        </a>
                                        <a social-sharing="twitter(post,'M');" class="upvoteIcon"
                                           ng-if="post.allow_share==1">
                                            <img src="assets/pages/img/twt-icon.png"
                                                 alt=""/>
                                            <span>{{post.totalTwittershare | thousandSuffix }}</span>
                                        </a>
                                        <a ng-click="redirecToLogin();" class="upvoteIcon">
                                            <img src="assets/pages/img/bookmark.png" alt=""/>
                                        </a>
                                        <div class="cardSmNav last">
                                            <post-card-menu></post-card-menu>
                                            <a ng-click="redirecToLogin();" class="upvoteIcon">
                                                <i class="fa fa-circle"
                                                   aria-hidden="true"></i>
                                                <i class="fa fa-circle"
                                                   aria-hidden="true"></i>
                                                <i class="fa fa-circle"
                                                   aria-hidden="true"></i>
                                            </a>
                                        </div>
                                    </div>
                                </div>
                            </div>
                        </div>
						<div class="topicsTagList">
							<span class="liTl">Topics tagged in this post</span>
							<div class="tglistCont">
								<div class="li">
									Lorem ipsum dolor sit amet
									<label class="followBtn">
										<span style="display:none;">FOLLOW</span>
										<span class="ico">FOLLOWING</span>
									</label>
								</div>
								<div class="li">
									Consectetur adipiscing elit
									<label class="followBtn">
										<span>FOLLOW</span>
										<span class="ico" style="display:none;">FOLLOWING</span>
									</label>
								</div>
								<div class="li">
									Quisque molestie non elit id elementum maecenas quis imperdiet tortor
									<label class="followBtn">
										<span>FOLLOW</span>
										<span class="ico" style="display:none;">FOLLOWING</span>
									</label>
								</div>
								<div class="li">
									Id elementum nisl
									<label class="followBtn">
										<span>FOLLOW</span>
										<span class="ico" style="display:none;">FOLLOWING</span>
									</label>
								</div>
								<div class="li">
									Curabitur sed lacus et risus eleifend
									<label class="followBtn">
										<span>FOLLOW</span>
										<span class="ico" style="display:none;">FOLLOWING</span>
									</label>
								</div>
								<div class="li">
									 Aenean quis convallis nisi mauris eleifend feugiat
									<label class="followBtn">
										<span>FOLLOW</span>
										<span class="ico" style="display:none;">FOLLOWING</span>
									</label>
								</div>
								<div class="li">
									 Turpis consequat laoreet ex lobortis sed
									<label class="followBtn">
										<span>FOLLOW</span>
										<span class="ico" style="display:none;">FOLLOWING</span>
									</label>
								</div>
							</div>
						</div>
                    </div>
                    <div class="profileCommentBoxTop useForMobile">
                        <div class="userStatusRow">
                            <div class="userStatusImage">
                                <a ui-sref="account({ username: post.user.username })"
                                   style="background:url({{ user.profile_image ? 'uploads/profile/thumbs/'+user.profile_image : default_profile_img}}) no-repeat;"></a>
                            </div>
                            <div class="userStatusInfo">
                                <div class="userStatusInfoLeft">
													<span class="userStatusInfoTtl">
															<a ui-sref="account({ username: user.username })">{{user.first_name + ' ' + user.last_name}}</a>
														</span>
                                    <p>
														<span class="uoccupation">
															<small>&nbsp;{{user.occupation}}</small>
														</span>
                                    </p>

                                    <p>
														<span class="clearfix block">
															<small>&nbsp;{{user.about_me | limitUserAbout}}</small>
														</span>
                                    </p>
                                </div>
                                <div class="clearfix"></div>
                            </div>
                            <div class="clearfix"></div>
                        </div>
                    </div>
                    <!-- start comment section -->
                    <comment-box></comment-box>
                    <!-- end comment section -->
                </div>
            </div>
        </div>
    </div>


    <!-- previous code (16-1-17)code change for anonymous user -->
<!--    <div class="col-md-3 col-sm-4 col-xs-12">
        <div class="widget rightWidget">
            <div class="widget-advanced" ng-repeat="postuser in post.getUser">
                <!-- creator label -->
<!--                <h3 class="creatorTtl" ng-if="$index==1">Creator</h3>
                <!-- Widget Header -->
<!--                <div class="widget-header text-center profileRgtCover"
                     style="background:url({{postuser.cover_image}}) no-repeat;">
                </div>
                <!-- END Widget Header -->
                <!-- Widget Main -->
<!--                <div class="widget-main">
                    <a ui-sref="account({ username: postuser.username })"
                       class="widget-image-container animation-hatch"
                       ng-if="postuser.thumb_image_url"
                       style="background:url({{postuser.thumb_image_url}}) no-repeat;"></a>
                    <a ui-sref="account({ username: postuser.username })"
                       class="widget-image-container animation-hatch {{postuser.user_color}}"
                       ng-if="!postuser.thumb_image_url">
                        <span class="txt">{{postuser.first_name.charAt(0)}}</span>
                    </a>

                    <h3 class="widget-content widget-content-image widget-content-light profileName">
                        <a ui-sref="account({ username: postuser.username })" class="themed-color">{{postuser.first_name
                            + ' '+postuser.last_name}}</a>
                        <span class="uoccupation" ng-if="postuser.occupation">{{ postuser.occupation }}</span>
                        <small ng-if="postuser.about_me">&nbsp;{{postuser.about_me | limitUserAbout}}</small>
                    </h3>

                    <div class="userFollow" ng-if="postuser.id!=user.id && user.guest== 0"
                         allfollowuser="followUser(postuser.id,'C','followed')">
                        <label class="followBtn"
                               ng-if="userFollowing.indexOf(postuser.id)!=-1">
                            <span class="ico">FOLLOWING</span>
                        </label>
                        <label class="followBtn"
                               ng-if="userFollowing.indexOf(postuser.id)==-1">
                            <span>FOLLOW</span>
                        </label>
                    </div>
                    <div class="userFollow" ng-if="postuser.id!=user.id && user.guest!= 0">
                        <label class="followBtn"
                               ng-if="userFollowing.indexOf(postuser.id)!=-1" ng-click="redirecToLogin();">
                            <span class="ico">FOLLOWING</span>
                        </label>
                        <label class="followBtn"
                               ng-if="userFollowing.indexOf(postuser.id)==-1" ng-click="redirecToLogin();">
                            <span>FOLLOW</span>
                        </label>
                    </div>

                    <div class="row text-center animation-fadeIn">
                        <div class="col-xs-4">
                            <h5>
                                <strong>{{postuser.userDataProfileViews | thousandSuffix}}</strong>
                                <br>
                                <small>Views</small>
                            </h5>
                        </div>
                        <div class="col-xs-4">
                            <h5><strong>{{postuser.points>=1 ? postuser.points : 0 | thousandSuffix}}</strong><br>
                                <small>Points</small>
                            </h5>
                        </div>
                        <div class="col-xs-4">
                            <h5><strong>{{postuser.is_follow | thousandSuffix}}</strong><br>
                                <small>Followers</small>
                            </h5>
                        </div>
                    </div>
                </div>
                <!-- END Widget Main -->
<!--            </div>
        </div>
    </div>              -->
 <!-- previous code (16-1-17)code change for anonymous user -->    


                                <div class="col-md-3 col-sm-4 col-xs-12">
                                    <div class="widget rightWidget">
                                        <div class="widget-advanced" ng-repeat="postuser in post.getUser">
                                            <h3 class="creatorTtl" ng-if="$index==1">Creator</h3>
                                            <!-- Widget Header -->
                                            <div ng-if="post.created_by!=postuser.id">
                                                <div class="widget-header text-center profileRgtCover"
                                                ng-style="{background:'url('+postuser.cover_image+') no-repeat'}"
                                                >
                                                </div>
                                            </div>
                                            <div ng-if="post.created_by==postuser.id">
                                                <div class="widget-header text-center profileRgtCover"
                                                ng-style="{background:'url('+postuser.cover_image+') no-repeat'}"
                                                ng-if="post.ask_anonymous!=1">
                                                </div>
                                                <div class="widget-header text-center profileRgtCover"
                                                ng-style=""
                                                ng-if="post.ask_anonymous==1">
                                                </div>
                                            </div>
                                            <!-- END Widget Header -->
                                            <!-- Widget Main -->
                                            <div class="widget-main">

                                            <div ng-if="post.created_by!=postuser.id">
                                                <a href="/profile/{{postuser.username}}"
                                                   class="widget-image-container animation-hatch"
                                                   ng-if="postuser.thumb_image_url"
                                                   style="background:url({{postuser.thumb_image_url}}) no-repeat;">
                                                </a>
                                                
                                                <a href="/profile/{{postuser.username}}"
                                                   class="widget-image-container animation-hatch {{postuser.user_color}}"
                                                   ng-if="(!postuser.thumb_image_url)">
                                                   <span class="txt">{{postuser.first_name.charAt(0)}}</span>
                                                </a>
                                            </div> 
                                            <div ng-if="post.created_by==postuser.id && post.ask_anonymous!=1 ">
                                                <a href="/profile/{{postuser.username}}"
                                                   class="widget-image-container animation-hatch"
                                                   ng-if="postuser.thumb_image_url"
                                                   style="background:url({{postuser.thumb_image_url}}) no-repeat;">
                                                </a>
                                                
                                                <a href="/profile/{{postuser.username}}"
                                                   class="widget-image-container animation-hatch {{postuser.user_color}}"
                                                   ng-if="(!postuser.thumb_image_url)">
                                                   <span class="txt">{{postuser.first_name.charAt(0)}}</span>
                                                </a>
                                            </div>
                                            <div ng-if="post.created_by==postuser.id && post.ask_anonymous==1 ">
                                            <a href=""
                                                   class="widget-image-container animation-hatch {{postuser.user_color}}"
                                                   >
                                                   <span class="txt">A</span>
                                                </a>           
                                            </div>



                                            <div ng-if="post.created_by!=postuser.id">
                                                <h3 class="widget-content widget-content-image widget-content-light profileName" >
                                                    <a ui-sref="account({ username: postuser.username })"
                                                       class="themed-color">{{(postuser.first_name + ' '
                                                        +postuser.last_name)}}</a>
													<span class="uoccupation" ng-if="postuser.occupation">&nbsp;{{postuser.occupation}}</span>
                                                    <small ng-if="postuser.about_me">&nbsp;{{postuser.about_me | limitUserAbout}}</small>
                                                </h3>
                                            </div>

                                            <div ng-if="post.created_by==postuser.id && post.ask_anonymous!=1 ">
                                                <h3 class="widget-content widget-content-image widget-content-light profileName" >
                                                    <a ui-sref="account({ username: postuser.username })"
                                                       class="themed-color">{{(postuser.first_name + ' '
                                                        +postuser.last_name)}}</a>
													<span class="uoccupation" ng-if="postuser.occupation">&nbsp;{{postuser.occupation}}</span>
                                                    <small ng-if="postuser.about_me">&nbsp;{{postuser.about_me | limitUserAbout}}</small>
                                                </h3>
                                            </div>
                                            
                                            <div ng-if="post.created_by==postuser.id && post.ask_anonymous==1 ">
                                                <h3 class="widget-content widget-content-image widget-content-light profileName" >
                                                    <a class="themed-color">Anonymous</a>
													
                                                </h3>
                                            </div>

                                            <div ng-if="post.created_by!=postuser.id || post.ask_anonymous!=1 ">
                                                <div class="userFollow" ng-if="(postuser.id!=user.id && user.guest==0  )"
                                                     allfollowuser="followUser(postuser.id,'C','followed')">
                                                    <label class="followBtn"
                                                           ng-if="userFollowing.indexOf(postuser.id)!=-1">
                                                        <span class="ico">FOLLOWING</span>
                                                    </label>
                                                    <label class="followBtn"
                                                           ng-if="userFollowing.indexOf(postuser.id)==-1">
                                                        <span>FOLLOW</span>
                                                    </label>
                                                </div>
                                                <div class="userFollow" ng-if="(postuser.id!=user.id  && user.guest!=0 )">
                                                    <label class="followBtn"
                                                           ng-if="userFollowing.indexOf(postuser.id)!=-1" ng-click="redirecToLogin();">
                                                        <span class="ico">FOLLOWING</span>
                                                    </label>
                                                    <label class="followBtn"
                                                           ng-if="userFollowing.indexOf(postuser.id)==-1" ng-click="redirecToLogin();">
                                                        <span>FOLLOW</span>
                                                    </label>
                                                </div>
                                            </div>

                                            <div ng-if="post.created_by!=postuser.id || post.ask_anonymous!=1 ">

                                                <div class="row text-center animation-fadeIn">
                                                    <div class="col-xs-4">
                                                        <h5>
                                                            <strong >{{postuser.userDataProfileViews | thousandSuffix}}</strong>
                                                           
                                                            <br>
                                                            <small>Views</small>
                                                        </h5>
                                                    </div>
                                                    <div class="col-xs-4">
                                                        <h5><strong >{{postuser.points>=1 ? postuser.points : 0 |
                                                                thousandSuffix}}</strong>
                                                          
                                                                <br>
                                                            <small>Points</small>
                                                        </h5>
                                                    </div>
                                                    <div class="col-xs-4">
                                                        <h5><strong >{{postuser.is_follow | thousandSuffix}}</strong>
                                                          
                                                        <br>
                                                            
                                                            <small>Followers</small>
                                                        </h5>
                                                    </div>
                                                </div>
                                            </div>

                                             <div ng-if="post.created_by==postuser.id && post.ask_anonymous==1 ">
                                                <div class="row text-center animation-fadeIn">
                                                    <div class="col-xs-4">
                                                        <h5>
                                                            <strong >-</strong>
                                                           
                                                            <br>
                                                            <small>Views</small>
                                                        </h5>
                                                    </div>
                                                    <div class="col-xs-4">
                                                        <h5><strong >-</strong>
                                                          
                                                                <br>
                                                            <small>Points</small>
                                                        </h5>
                                                    </div>
                                                    <div class="col-xs-4">
                                                        <h5><strong >-</strong>
                                                          
                                                        <br>
                                                            
                                                            <small>Followers</small>
                                                        </h5>
                                                    </div>
                                                </div>
                                             <div>    

                                            </div>
                                            <!-- END Widget Main -->
                                        </div>
                                    </div>
                                </div>




</div>
</div>
</div>
</div>

<!-- REPORT COMMENT  MODAL -->
<report-comment-modal></report-comment-modal>
<!-- SHARE Modal -->
<sharepost-card></sharepost-card>
<!-- DELETE POST CARD MODAL -->
<delete-post-modal></delete-post-modal>
<!-- REPORT POST CARD MODAL -->
<report-post-modal></report-post-modal>
<!-- PROMPT SINGIN BOX -->
<prompt-signin-box></prompt-signin-box>