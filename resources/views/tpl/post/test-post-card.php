<div class="profileCommentBox clkProfileCommentBox postLoading<?php /* {{cardClass(post)}}*/?>"
     ng-mouseleave="videoPause(post,$index,'C');"
     id="postCard{{$index}}" style="opacity:0;"
     custompostid="{{post.id}}"
     ng-class="{'image_status_post': post.post_type==1 || post.post_type==5 }"
>

    <div class="cardBoxClk" data-toggle="modal" ng-click="showPostDetails(post.id,post.child_post_id,0,$index)"
         data-target="#myModal{{ post.id }}"></div>
    <div class="profileCommentBoxTop">
        <div class="userStatusRow smUserStatus">
            <div class="userStatusImage" ng-if="::post.user.profile_image">
                <a ui-sref="account({ username: post.user.username })" style="background:url({{ post.user.profile_image ? 'uploads/profile/thumbs/'+post.user.profile_image : default_profile_img }}) no-repeat;"></a>
            </div>
            <div ng-if="!post.user.profile_image"
                 class="userStatusImage {{post.user.user_color}}"
                 >
                <a ui-sref="account({ username: post.user.username })">
                    <span class="txt">{{ ::post.user.first_name.charAt(0)}}</span>
                </a>
            </div>

            <div class="userStatusInfo withLocation" ng-class="{'showFollow':userFollowing.indexOf(post.user.id)==-1 && post.user.id!=user.id}">
                <span class="userStatusInfoTtl clearfix withLocation">
                    <a ui-sref="account({ username: post.user.username })">{{ post.user.first_name
                        + ' ' + post.user.last_name }}</a>
						
					<label class="followBtn" ng-if="post.user.id!=user.id && user.guest==0"
                           allfollowuser="followUser(post.user.id,'C','followed');">
						<span class="" ng-if="userFollowing.indexOf(post.user.id)==-1">FOLLOW</span>
                        <span class="ico" ng-if="userFollowing.indexOf(post.user.id)!=-1">FOLLOWING</span>
					</label>
					
					<label class="followBtn" ng-if="post.user.id!=user.id && user.guest!= 0" ng-click="redirecToLogin();">
						<span class="" ng-if="userFollowing.indexOf(post.user.id)==-1">FOLLOW</span>
                        <span class="ico" ng-if="userFollowing.indexOf(post.user.id)!=-1">FOLLOWING</span>
					</label>
                  <span class="postCardOnlineusr" ng-if="post.people_here">
						<i class="fa fa-circle"></i> {{post.people_here}}
					</span>
                </span>
                <div class="cardFollow clearfix">
                    <small ng-if="showElapsedTime(post.created_at)"><!--Shared this post -->
                        <time am-time-ago="post.created_at | amUtc | amLocal"></time>
                    </small>
                    <small ng-if="!showElapsedTime(post.created_at)"><!--Shared this post  -->
                        <time>{{ post.created_at | amDateFormat:'DD MMM YYYY' }}</time>
                    </small>
                    {{ typeof(post.distance) }}
                    <small class="showNearby" ng-if="post.hasOwnProperty('distance') && post.distance!=null">
                         - {{::post.distance | formatDistance}} away
                    </small>
                </div>
				<div class="userStatusInfo info">
					<p class="userAbout">
						<span class="clearfix" ng-if="post.user.about_me" data-toggle="tooltip" ui-jq="tooltip"
							  data-original-title="{{ post.user.about_me }}">
							<small>{{ ::post.user.about_me }}</small>
						</span>
					</p>
				</div>
            </div>
            <div class="clearfix"></div>
        </div>

        <div class="post_type">
            <span class="noCaptionBx" ng-class="{'noCaption': post.caption==''}"></span>
            <p class="card-caption" ng-if="post.caption">
                <span class="card-caption" ng-bind-html="post.caption | markupHTMLTags"></span>
                <a ng-if="::(post.location && post.place_url && post.post_type == 5)" class="cardLoc"
                    href="/place?{{::post.place_url}}">
                    <img src="assets/pages/img/location1.png" alt="Location">
                    <span data-toggle="tooltip" ui-jq="tooltip" data-original-title="{{ ::post.location }}">{{ showLocation(post.location) }}</span>
                </a>
            </p>

            <div class="catagoryTtl" ng-if="::(post.category || post.sub_category)">
                <div class="catagoryTagRow {{ post.post_type == 3 ? 'withTime' : '' }}">
                    <a ng-if="post.category"
                       href="{{ '/tag/' + post.category.category_name_url }}"
                       class="catagoryTtlHighLight">{{ ::post.category.category_name }}</a>
                    <a ng-if="post.sub_category"
                       href="{{ '/tag/' + post.sub_category.subcategory_name_url }}"
                       class="catagoryTtlHighLight">{{ ::(post.sub_category && post.sub_category.category_name) }}</a>
                    <div class="postTime" ng-if="post.post_type == 3 && post.time_needed!=0">
                        {{::post.time_needed}} &nbsp;min read
                    </div>
                </div>
                <p>{{ ::post.title }}
                    <a ng-if="::(post.location && post.place_url)" class="cardLoc"
                        href="/place?{{::post.place_url}}">
                        <img src="assets/pages/img/location1.png" alt="Location Info">
                        <span data-toggle="tooltip" ui-jq="tooltip" data-original-title="{{ ::post.location }}">{{ showLocation(post.location) }}</span>
                    </a>
                </p>
            </div>
            <div class="postShortDesc" ng-show="::post.short_description">
                <p>{{ ::(post.short_description | limitShortDesc) }}</p>
            </div>
            <!-- image post -->
            <div ng-if="::(post.post_type == 1)">
                <p class="postLink" ng-if="::post.source">
                    <span>
                        <a href="{{ ::post.source }}" target="_blank">
                            {{ ::(post.source | domainFilter) }}
                        </a>
                    </span>
                </p>
                <div class="uploadImage">
                    <a data-toggle="modal" ng-click="showPostDetails(post.id,post.child_post_id,0,$index)"
                       data-target="#myModal{{ ::post.id }}">
                        <img ng-src="{{ post.image ? 'uploads/post/thumbs/'+post.image : default_post_img }}"
                             imageonload="removePostLoading('postCard'+$index)">
                    </a>
                </div>
            </div>
            <!-- video post -->
            <div ng-if="post.post_type == 2"
                 ng-mouseover="videoPlay(post,$index,'C');">
                <p class="postLink" ng-if="::(post.source && post.source!='http://undefined')">
                    <span>
                        <a href="{{ ::(post.embed_code ? post.embed_code : post.source) }}" target="_blank">
                            {{ ::(post.source | domainFilter) }}
                        </a>
                    </span>
                </p>
                <div class="uploadImage" ng-if="::post.embed_code">
                    <div class="uploadVidPreview">
                        <!-- YOUTUBE -->
                        <youtube ng-if="::(post.embed_code_type =='youtube')" 
								videoid="{{::post.videoid}}" 
								postcardid="{{::post.id}}" 
								childpostid="{{::post.child_post_id}}" 
								posttype="{{::post.post_type}}"
								type="C" 
                                index="{{$index}}"> </youtube>

                        <!-- VIMEO -->
                        <div ng-if="::(post.embed_code_type =='vimeo')" >
                            <vimeo-video postcardid="{{::post.id}}" childpostid="{{::post.child_post_id}}" posttype="{{::post.post_type}}" type="C"  id="playermy_item{{$index}}" vid="{{::post.videoid}}"></vimeo-video> 
                        </div> 

                        <!-- DAILYMOTION -->
                        <div ng-if="::(post.embed_code_type =='dailymotion')" 
                         id="selectDailyMotion{{$index}}">
                            <daily-motion id="dmplayer{{$index}}" 
                                    vid="{{::post.videoid}}" 
                                    postcardid="{{::post.id}}" 
                                    childpostid="{{::post.child_post_id}}" 
									posttype="{{::post.post_type}}"
                                    type="C">
                            </daily-motion>
                        </div>
                    </div>
                </div>
                <div class="uploadImage" ng-if="::post.video">
                    <div class="uploadLocVidPreview">
                       <!-- MANUAL VIDEO -->
						<manual-video url="{{ 'uploads/video/' + post.video | trustUrl }}" 
								postcardid="{{::post.id}}"  
								childpostid="{{::post.child_post_id}}" 
								type="C"
								posttype="{{::post.post_type}}"								
								id="postCard{{$index}}"></manual-video>
                        <span class="customPlayPause"><span></span></span>
                        <div <?php /*in-view="$inview&&myLoadingFunction($index,post)"*/?> class="onScreenDiv"
                             style="display:block;position: relative;top: -1px;"></div>
                    </div>
                </div>
            </div>
            <!-- Article post -->
            <div ng-if="post.post_type == 3">
                <p class="postLink" ng-if="::post.source">
                    <span>
                        <a href="{{ post.external_link ? post.external_link : post.source }}" target="_blank">
                            {{ post.source | domainFilter }}
                        </a>
                    </span>
                </p>
                <div class="uploadImage" ng-if="::post.image">
                    <a data-toggle="modal" ng-click="showPostDetails(post.id,post.child_post_id,0,$index)"
                       data-target="#myModal{{ ::post.id }}">
                        <img ng-src="{{ post.image!='' ? 'uploads/post/thumbs/'+post.image : default_post_img }}"
                             imageonload="removePostLoading('postCard'+$index)">
                    </a>
                </div>
                <div class="postShortDesc" ng-bind-html="::(post.content | markupHTMLTags)"></div>

            </div>
            <!-- Link post -->
            <div ng-if="::(post.post_type == 4)">
                <div ng-if="post.source" class="postLinkBtn">
                    <?php /* <a href="{{ post.external_link ? post.external_link : post.source }}" target="_blank" class="btn btn-default btn-sm"> */ ?>
                    <a href="{{::post.external_link}}" target="_blank"
                       viewpost="externalLink(post.id,post.child_post_id);" class="btn btn-default btn-sm">
                        <i class="fa fa-external-link"></i>
                        {{ ::(post.source | domainFilter) }}
                    </a>
                </div>
                <!-- Linked video -->
                <div class="uploadImage" ng-if="::post.embed_code">
                    <div class="uploadVidPreview">
                        <!-- YOUTUBE -->
                        <youtube ng-if="post.embed_code_type =='youtube'" 
									postcardid="{{::post.id}}" 
									childpostid="{{::post.child_post_id}}" 
									type="C" 
									videoid="{{::post.videoid}}" 
									posttype="{{::post.post_type}}"
									index="{{$index}}"></youtube>

                        <!-- VIMEO -->
                        <div ng-if="::(post.embed_code_type =='vimeo')" >
                            <vimeo-video postcardid="{{post.id}}" childpostid="{{::post.child_post_id}}" posttype="{{::post.post_type}}" type="C"  id="playermy_item{{$index+1}}" vid="{{::post.videoid}}"></vimeo-video> 
                        </div> 	

                        <!-- DAILYMOTION -->
                        <div ng-if="post.embed_code_type =='dailymotion'" 
                         id="selectDailyMotion{{$index}}">
                            <daily-motion 
                                    id="dmplayer{{$index}}" 
                                    vid="{{::post.videoid}}" 
                                    postcardid="{{::post.id}}" 
                                    childpostid="{{::post.child_post_id}}"
									posttype="{{::post.post_type}}"									
                                    type="C">
                            </daily-motion>
                        </div>
                        <!-- OTHERS -->
                        <iframe ng-if="::(post.embed_code_type =='unsupported')" class="iframeTag"
                                src="{{ ::(post.embed_code | trustUrl) }} "
                                imageonload="removePostLoading('postCard'+$index)"
                                height="200"
                                webkitallowfullscreen mozallowfullscreen allowfullscreen ad_pause>
                        </iframe>
                    </div>
                </div>
                <!-- Linked image -->
                <div class="uploadImage" ng-if="::(!post.embed_code && post.image)">
                    <a data-toggle="modal"
                        ng-click="showPostDetails(post.id,post.child_post_id,0,$index)"
                        data-target="#myModal{{::post.id }}">
                        <img ng-src="{{ post.image!='' ? 'uploads/post/thumbs/'+post.image : default_post_img }}"
                             imageonload="removePostLoading('postCard'+$index)">
                    </a>
                </div>
            </div>
            <!-- Status post -->
            <div ng-if="::(post.post_type == 5)" ng-mouseover="videoPlay(post,$index,'C');">
                <p class="postLink" ng-if="::post.source">
                    <span>
                        <a href="{{ ::(post.embed_code ? post.embed_code : post.source) }}" target="_blank">
                            {{ ::(post.source | domainFilter) }}
                        </a>
                    </span>
                </p>
                <!-- Status video -->
                <div class="uploadImage" ng-if="post.embed_code">
                    <div class="uploadVidPreview">
                        <!-- YOUTUBE -->
                        <youtube ng-if="::(post.embed_code_type =='youtube')" 
										videoid="{{::post.videoid}}" 
										postcardid="{{::post.id}}"
										childpostid="{{::post.child_post_id}}"
										posttype="{{::post.post_type}}"
										type="C"
                                 index="{{$index}}"></youtube>

                        <!-- VIMEO -->
                        <div ng-if="::(post.embed_code_type =='vimeo')" >
                            <vimeo-video postcardid="{{::post.id}}" childpostid="{{::post.child_post_id}}" posttype="{{::post.post_type}}" type="C"  id="playermy_item{{$index+1}}" vid="{{::post.videoid}}"></vimeo-video> 
                        </div> 	
                        <!-- DAILYMOTION -->
						<div ng-if="::(post.embed_code_type =='dailymotion')" 
                         id="selectDailyMotion{{$index}}">
                            <daily-motion 
                                    id="dmplayer{{$index}}" 
                                    vid="{{::post.videoid}}" 
                                    postcardid="{{::post.id}}" 
                                    childpostid="{{::post.child_post_id}}" 
									posttype="{{::post.post_type}}"
                                    type="C">
                            </daily-motion>
                        </div>
                        <!-- OTHERS -->
                        <iframe ng-if="::(post.embed_code_type =='unsupported')" class="iframeTag"
                                src="{{ ::(post.embed_code | trustUrl) }} "
                                imageonload="removePostLoading('postCard'+$index)"
                                height="200"
                                webkitallowfullscreen mozallowfullscreen allowfullscreen ad_pause>
                        </iframe>
                    </div>
                </div>
                <!-- Uploaded video -->
                <div class="uploadImage" ng-if="::post.video">
                    <div class="uploadLocVidPreview">
                        <!-- MANUAL VIDEO -->
						<manual-video url="{{ ::('uploads/video/' + post.video | trustUrl) }}" 
    						postcardid="{{::post.id}}"  
    						childpostid="{{::post.child_post_id}}" 
    						type="C" 
    						posttype="{{::post.post_type}}"
    						id="postCard{{$index}}">
									</manual-video>
                        <span class="customPlayPause"><span></span></span>
                    </div>
                </div>
                <!-- Linked image -->
                <div class="uploadImage" ng-if="::(!post.embed_code && post.image)">
                    <a data-toggle="modal"
                         ng-click="showPostDetails(post.id,post.child_post_id,0,$index)"
                         data-target="#myModal{{ ::post.id }}">
                        <img ng-src="{{ post.image!='' ? 'uploads/post/thumbs/'+post.image : default_post_img }}"
                             imageonload="removePostLoading('postCard'+$index)">
                    </a>
                </div>
            </div>
            <!-- tags -->
            <div class="profileNewCoverBottomLink" ng-if="::(post.tags.length>0)">
                <ul>
                    <li ng-repeat="tag in ::post.tags"><a href="/tag/{{ ::tag.tag_name }}">#{{::tag.tag_name}}</a></li>
                </ul>
            </div>
        </div>

        <div ng-if="::(user.guest== 0)">
            <div class="profileCommentFooter addNew noBorderPos">
                <div class="left" ng-class="{'show2': popUpReportDropdrow==1}">
                    <a class="upvoteIcon"
                       data-toggle="tooltip"
                       ui-jq="tooltip"
                       data-original-title="{{ post.isUpvote == 'Y' ? 'Remove Upvote' : 'Upvote' }}"
                       upvotes="doUpvotes(post.id,post.child_post_id,'C');">
                        <img src="/assets/pages/img/arrow2_t.png" alt=""
                             ng-if="post.isUpvote == 'N'"/>
                        <img src="/assets/pages/img/arrow2_t_sl.png" alt=""
                             ng-if="post.isUpvote == 'Y'"/>
    					
                    </a>
    				<span class="countN" ng-if="post.upvotes - post.downvotes !=0"> {{ (post.upvotes - post.downvotes) >0 ? '+'+(post.upvotes - post.downvotes) : (post.upvotes - post.downvotes) | thousandSuffix }}</span>
                    <a class="tip"
                       data-toggle="tooltip"
                       ui-jq="tooltip"
                       data-original-title="Comment"
                       ng-if="post.allow_comment==1"
                       ng-click="showPostDetails(post.id,post.child_post_id,1,$index)">
                        <img src="/assets/pages/img/speech_bubble4.png" alt=""/>
                    </a>
    				<span class="countN" ng-if="::(post.totalComments !=0)"> {{ ::(post.totalComments  | thousandSuffix) }}</span>
                    <div class="cardSmNav">
                        <a class="shrClk" ng-if="::(post.allow_share==1)"
                           data-toggle="tooltip" ui-jq="tooltip"
                           data-original-title="Share"
                           ng-click="openSharePostPopUp(post.id);">
                            <img src="/assets/pages/img/refresh4.png" alt=""/>
                        </a>
    					<span class="countN" ng-if="post.totalShare !=0"> {{ post.totalShare | thousandSuffix }}</span>
                        <div id="shareOverlay_{{post.id}}" 
                            ng-if="post.allow_share==1"
                            ng-click="closeOverLayout();" 
                            class="subOverlay"
                            style="display:none;"></div>
                        
                        <div id="shareDropdrow_{{post.id}}" class="sub" style="display:none;">
                            <ul>
                                <li ng-if="post.child_post_user_id!=user.id"
                                    share="sharePopUp(post.id,post.child_post_user_id,post.child_post_id,0);">
                                    <img src="/assets/pages/img/refresh4.png" alt="share post"/>
                                    Share this post
                                </li>
                                <li social-sharing="facebook(post,'C');">
                                    <img src="/assets/pages/img/fb-icon.png"
                                         alt="share to facebook"/> Share to facebook
                                </li>
                                <li social-sharing="twitter(post,'C');">
                                    <img src="/assets/pages/img/twt-icon.png"
                                         alt="share to twitter"/> Share to twitter
                                </li>
                            </ul>
                        </div>
                    </div>
                    <a class="tip saveDel"
                       data-toggle="tooltip"
                       ui-jq="tooltip"
                       data-original-title="Save"
                       ng-if="post.created_by!=user.id && post.child_post_user_id!=user.id"
                       book-mark="bookMarkProcess(post.id,'C');">
                        <img src="/assets/pages/img/bookmark.png"
                             alt="" 
                             ng-if="post.isBookMark=='N'"
                             class="saveIcon"/>
                        <img src="/assets/pages/img/bookmark-fill.png"
                             alt=""
                             ng-if="post.isBookMark=='Y'"
                             class="savedIcon"/>
                    </a>
                </div>
                <div class="right">
                    <div class="cardSmNav last">
    					<span class="countN" ng-if="::post.totalPostViews">
    						{{ ::(post.totalPostViews | thousandSuffix) }}
    						&nbsp;{{ ::(showPostViewTxt(post))}}
    					</span>
    					<a class="moreBtnN" ng-click="report(post);">
    						<i class="fa fa-circle" aria-hidden="true"></i>
    						<i class="fa fa-circle" aria-hidden="true"></i>
    						<i class="fa fa-circle" aria-hidden="true"></i>
    					</a>

                        <post-card-menu></post-card-menu>
                    </div>
                </div>
                <div class="clearfix"></div>
            </div>
        </div>
        <!-- Anonymous user -->
        <div class="profileCommentFooter addNew noBorderPos" ng-if="::(user.guest!=0)">
            <div class="left">
                <a class="upvoteIcon" ng-click="redirecToLogin();">
                    <img src="/assets/pages/img/arrow2_t.png" alt=""
                        />
				</a>
				<span class="countN"> {{ ::((post.upvotes - post.downvotes) >0 ? '+'+(post.upvotes - post.downvotes) : (post.upvotes - post.downvotes) | thousandSuffix) }}</span>
                <a class="tip" ng-click="redirecToLogin()">
                    <img src="/assets/pages/img/speech_bubble4.png" alt="comment"/>
                </a>
				<span class="countN"> {{ ::(post.totalComments  | thousandSuffix) }}</span>
                
				<div class="cardSmNav">
                    <a class="shrClk" ng-if="::(post.allow_share==1)"
                       data-toggle="tooltip" ui-jq="tooltip"
                       data-original-title="Share"
                       ng-click="openSharePostPopUp(post.id);">
                        <img src="/assets/pages/img/refresh4.png" alt=""/>
                    </a>
					<span class="countN"> {{ ::(post.totalShare | thousandSuffix) }}</span>
					<div id="shareOverlay_{{::post.id}}" 
                        ng-if="::(post.allow_share==1)"
                        ng-click="closeOverLayout();" 
                        class="subOverlay"
                        style="display:none;"></div>
                    
                    <div id="shareDropdrow_{{::post.id}}" class="sub" style="display:none;">
                        <ul>   
                            <li social-sharing="facebook(post,'C');">
                                <img src="/assets/pages/img/fb-icon.png"
                                     alt="share to facebook"/> Share to facebook
                            </li>
                            <li social-sharing="twitter(post,'C');">
                                <img src="/assets/pages/img/twt-icon.png"
                                     alt="share to twitter"/> Share to twitter
                            </li>
                        </ul>
                    </div>
                </div>
                <a class="tip" ng-click="redirecToLogin();">
                    <img src="/assets/pages/img/bookmark.png"
                         alt="" class="saveIcon"/>
                </a>
            </div>
            <div class="right">
                <div class="cardSmNav last">
					<span class="countN" ng-if="::post.totalPostViews">
						{{ ::(post.totalPostViews | thousandSuffix) }}
						&nbsp;{{ ::(showPostViewTxt(post))}}
					</span>
                    <a class="moreBtnN" ng-click="redirecToLogin();">
                        <i class="fa fa-circle" aria-hidden="true"></i>
                        <i class="fa fa-circle" aria-hidden="true"></i>
                        <i class="fa fa-circle" aria-hidden="true"></i>
                    </a>
                </div>
            </div>
            <div class="clearfix"></div>
        </div>
    </div>
</div>
