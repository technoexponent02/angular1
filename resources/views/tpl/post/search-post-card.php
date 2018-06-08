<div class="profileCommentBox clkProfileCommentBox postLoading<?php /* {{cardClass(post)}}*/ ?>"
     ng-mouseleave="videoPause(post,$index,'C');"
     id="postCard{{$index}}" style="opacity:1;"
     custompostid="{{::post.id}}"
     ng-class="::{'image_status_post na': post.post_type==1 || post.post_type==5 }"
>

    <div class="cardBoxClk" data-toggle="modal" ng-click="showPostDetails(post.id,post.child_post_id,0,$index)"
         data-target="#myModal{{::post.id}}"></div>
    <div class="profileCommentBoxTop">
        <div class="userStatusRow smUserStatus">
            <div class="userStatusImage" ng-if="::post.user.thumb_image_url">
                <a href="/profile/{{::post.post_owner.username}}"
                   style="background:url({{::post.user.thumb_image_url}}) no-repeat;"></a>
            </div>
            <div ng-if="::(!post.user.thumb_image_url)"
                 class="userStatusImage {{::post.user.user_color}}"
            >
                <a href="/profile/{{::post.user.username}}">
                    <span class="txt">{{::post.user.first_name.charAt(0)}}</span>
                </a>
            </div>

            <div class="userStatusInfo withLocation"
                 ng-class="{'showFollow':userFollowing.indexOf(post.user.id)==-1 && post.user.id!=user.id}">
                <span class="userStatusInfoTtl clearfix withLocation">
                    <a href="/profile/{{::post.user.username}}">{{::(post.user.first_name+' '+post.user.last_name)}}</a>
					<label class="followBtn" ng-if="::(post.user.id!=user.id && user.guest==0)"
                           allfollowuser="followUser(post.user.id,'C','followed');">
						<span class="" ng-if="userFollowing.indexOf(post.user.id)==-1">FOLLOW</span>
                        <span class="ico" ng-if="userFollowing.indexOf(post.user.id)!=-1">FOLLOWING</span>
					</label>
					
					<label class="followBtn" ng-if="::(post.user.id!=user.id && user.guest!=0)"
                           ng-click="redirecToLogin();">
						<span class="" ng-if="userFollowing.indexOf(post.user.id)==-1">FOLLOW</span>
                        <span class="ico" ng-if="userFollowing.indexOf(post.user.id)!=-1">FOLLOWING</span>
					</label>
                  <span class="postCardOnlineusr" ng-if="post.people_here">
						<i class="fa fa-circle"></i> {{post.people_here}}
					</span>
                </span>
                <div class="cardFollow clearfix">
                    <small ng-if="::showElapsedTime(post.created_at)"><!--Shared this post -->
                        <time am-time-ago="post.created_at | amUtc | amLocal"></time>
                    </small>
                    <small ng-if="::(!showElapsedTime(post.created_at))"><!--Shared this post  -->
                        <time>{{::(post.created_at | amDateFormat:'DD MMM YYYY')}}</time>
                    </small>
                    {{::typeof(post.distance) }}
                    <small class="showNearby" ng-init="::(post.distance = post.distance > 100 ? null : post.distance)"
                           ng-if="::(post.hasOwnProperty('distance') && post.distance!=null)">
                        - {{::(post.distance | formatDistance)}} away
                    </small>
                </div>
                <div class="userStatusInfo info">
                    <p class="userAbout">
						<span class="clearfix" ng-if="::post.user.about_me" data-toggle="tooltip" ui-jq="tooltip"
                              data-original-title="{{::post.user.about_me }}">
							<small>{{::(post.user.about_me)}}</small>
						</span>
                    </p>
                </div>
            </div>
            <div class="clearfix"></div>
        </div>

        <div class="post_type">
            <span class="noCaptionBx" ng-class="::{'noCaption': post.caption==''}"></span>
            <p class="card-caption" ng-if="::post.caption">
                <span class="card-caption"
                      ng-bind-html="::(post.caption | highlightNode:searchResults.query_arr)"></span>
                <a ng-if="::(post.location && post.place_url && post.post_type == 5)" class="cardLoc"
                   href="/place?{{::post.place_url}}">
                    <img src="assets/pages/img/location1.png" alt="Location">
                    <span data-toggle="tooltip" ui-jq="tooltip"
                          data-original-title="{{::post.location}}"
                          ng-bind-html="::(showLocation(post.location) | highlightNode:searchResults.query_arr)"></span>
                </a>
            </p>

            <div class="catagoryTtl">
                <div class="catagoryTagRow {{::(post.post_type == 3 ? 'withTime' : '')}} {{ ::(!(post.category || post.sub_category) ? 'nocvategorysmallgap' : '') }}"
                     ng-if="::(post.category || post.sub_category || post.post_type == 3 && post.time_needed!=0)">
                    <a ng-if="post.category"
                       href="/tag/{{::post.category.category_name_url}}"
                       class="catagoryTtlHighLight">
                        <span ng-bind-html="::(post.category.category_name | highlightNode:searchResults.query_arr)"></span>
                    </a>
                    <a ng-if="post.sub_category"
                       href="/tag/{{::post.sub_category.subcategory_name_url}}"
                       class="catagoryTtlHighLight">
                        <span ng-bind-html="::(post.sub_category.category_name | highlightNode:searchResults.query_arr)"></span>
                    </a>
                    <div class="postTime" ng-if="::(post.post_type == 3 && post.time_needed!=0)">
                        {{::post.time_needed}} &nbsp;min read
                    </div>
                </div>
                <p>
                    <span ng-bind-html="::(post.title | highlightNode:searchResults.query_arr)"></span>
                    <a ng-if="::(post.location && post.place_url && post.post_type != 5)" class="cardLoc"
                       href="/place?{{::post.place_url}}">
                        <img src="assets/pages/img/location1.png" alt="Location Info">
                        <span data-toggle="tooltip" ui-jq="tooltip"
                              data-original-title="{{::post.location}}"
                              ng-bind-html="::(showLocation(post.location) | highlightNode:searchResults.query_arr)"></span>
                    </a>
                </p>
            </div>
            <div class="postShortDesc" ng-show="::post.short_description">
                <p ng-show="::post.short_description"
                   ng-bind-html="::(post.short_description | limitShortDesc | highlightNode:searchResults.query_arr)"></p>
            </div>
            <!-- image post -->
            <div ng-if="::(post.post_type == 1)">
                <p class="postLink" ng-if="::(post.source && post.source!==null)">
                    <span>
                        <a href="{{::post.source}}" target="_blank">
                            <span ng-bind-html="::(post.source | domainFilter | highlightNode:searchResults.query_arr)"></span>
                        </a>
                    </span>
                </p>
                <div class="uploadImage">
                    <a data-toggle="modal" ng-click="showPostDetails(post.id,post.child_post_id,0,$index)"
                       data-target="#myModal{{::post.id}}">
                        <img ng-src="{{::post.image}}" imageonload="removePostLoading('postCard'+$index)">
                    </a>
                </div>
            </div>
            <!-- video post -->
            <div ng-if="::(post.post_type == 2)"
                 ng-mouseover="videoPlay(post,$index,'C');">
                <p class="postLink" ng-if="::(post.source && post.source!='http://undefined')">
                    <span>
                        <a href="{{::(post.embed_code ? post.embed_code : post.source)}}" target="_blank">
                            <span ng-bind-html="::(post.source | domainFilter | highlightNode:searchResults.query_arr)"></span>
                        </a>
                    </span>
                </p>
                <div class="uploadImage" ng-if="::post.embed_code">
                    <div class="uploadVidPreview">
                        <!-- YOUTUBE -->
                        <youtube ng-if="::(post.embed_code_type =='youtube')" videoid="{{::post.videoid}}" type="C"></youtube>

                        <!-- VIMEO -->
                        <div ng-if="::(post.embed_code_type =='vimeo')">
                            <vimeo-video
                                type="C"
                                id="vc-{{post.cardID}}"
                                vid="{{::post.videoid}}">
                            </vimeo-video>
                        </div>
                        <!-- DAILYMOTION -->
                        <div ng-if="::(post.embed_code_type =='dailymotion')"
                             id="dc-{{post.cardID}}">
                            <daily-motion
                                    type="C"
                                    id="dmplayer{{$index}}"
                                    vid="{{::post.videoid}}">
                            </daily-motion>
                        </div>
                    </div>
                </div>
                <div class="uploadImage" ng-if="::post.video">
                    <div class="uploadLocVidPreview">
                        <!-- MANUAL VIDEO -->
                        <manual-video url="{{::(post.video | trustUrl)}}"
                                      postcardid="{{::post.id}}"
                                      childpostid="{{::post.child_post_id}}"
                                      type="C"
                                      posttype="{{::post.post_type}}"
                                      id="cardVideo-{{post.cardID}}">
                        </manual-video>
                        <span class="customPlayPause"><span></span></span>
                        <div in-view="$inview&&myLoadingFunction($index,post)" class="onScreenDiv"
                             style="display:block;position: relative;top: -1px;"></div>
                    </div>
                </div>
            </div>
            <!-- Article post -->
            <div ng-if="::(post.post_type == 3)">
                <p class="postLink" ng-if="::(post.source && post.source!==null)">
                    <span>
                        <a href="{{::(post.external_link ? post.external_link : post.source)}}" target="_blank">
                            <span ng-bind-html="::(post.source | domainFilter | highlightNode:searchResults.query_arr)"></span>
                        </a>
                    </span>
                </p>
                <div class="uploadImage" ng-if="::post.image">
                    <a data-toggle="modal" ng-click="showPostDetails(post.id,post.child_post_id,0,$index)"
                       data-target="#myModal{{::post.id}}">
                        <img ng-src="{{::post.image}}" imageonload="removePostLoading('postCard'+$index)">
                    </a>
                </div>
                <div class="postShortDesc"
                     ng-bind-html="::(post.content | highlightNode:searchResults.query_arr)"></div>

            </div>
            <!-- Article post -->
            <div ng-if="::(post.post_type == 4)">
                <div ng-if="::post.source" class="postLinkBtn">
                    <a href="{{::post.external_link}}" target="_blank"
                       viewpost="externalLink(post.id,post.child_post_id);" class="btn btn-default btn-sm">
                        <i class="fa fa-external-link"></i>
                        <span ng-bind-html="::(post.source | domainFilter | highlightNode:searchResults.query_arr)"></span>
                    </a>
                </div>
                <!-- Linked video -->
                <div class="uploadImage" ng-if="::post.embed_code">
                    <div class="uploadVidPreview">
                        <!-- YOUTUBE -->
                        <youtube ng-if="::(post.embed_code_type =='youtube')" videoid="{{::post.videoid}}"></youtube>
                        </youtube>

                        <!-- VIMEO -->
                        <div ng-if="::(post.embed_code_type =='vimeo')">
                            <vimeo-video
                                type="C"
                                id="vc-{{post.cardID}}"
                                vid="{{::post.videoid}}">
                            </vimeo-video>
                        </div>
                        <!-- DAILYMOTION -->
                        <div ng-if="::(post.embed_code_type =='dailymotion')"
                             id="dc-{{post.cardID}}">
                            <daily-motion
                                    type="C"
                                    id="dmplayer{{$index}}"
                                    vid="{{::post.videoid}}">
                            </daily-motion>
                        </div>
                        <!-- OTHERS -->
                        <iframe ng-if="::(post.embed_code_type =='unsupported')" class="iframeTag"
                                src="{{::(post.embed_code | trustUrl)}} "
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
                       data-target="#myModal{{::post.id}}">
                        <img ng-src="{{::post.image}}" imageonload="removePostLoading('postCard'+$index)">
                    </a>
                </div>
            </div>
            <!-- Status post -->
            <div ng-if="post.post_type == 5" ng-mouseover="videoPlay(post,$index,'C');">
                <p class="postLink" ng-if="::(post.source && post.source!==null)">
                    <span>
                        <a href="{{::(post.embed_code ? post.embed_code : post.source) }}" target="_blank">
                           <span ng-bind-html="::(post.source | domainFilter | highlightNode:searchResults.query_arr)"></span>
                        </a>
                    </span>
                </p>
                <!-- Status video -->
                <div class="uploadImage" ng-if="::post.embed_code">
                    <div class="uploadVidPreview">
                        <youtube ng-if="::(post.embed_code_type =='youtube')" videoid="{{::post.videoid}}"></youtube>

                        <div ng-if="post.embed_code_type =='vimeo'">
                            <vimeo-video
                                type="C"
                                id="vc-{{post.cardID}}"
                                vid="{{::post.videoid}}">
                            </vimeo-video>
                        </div>
                        <div ng-if="::(post.embed_code_type =='dailymotion')"
                             id="dc-{{::post.cardID}}">
                            <daily-motion
                                    type="C"
                                    id="dmplayer{{::post.cardID}}"
                                    vid="{{::post.videoid}}">
                            </daily-motion>
                        </div>
                        <!-- OTHERS -->
                        <iframe ng-if="::(post.embed_code_type =='unsupported')"
                                class="iframeTag"
                                src="{{::(post.embed_code | trustUrl)}} "
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
                        <manual-video url="{{::(post.video | trustUrl)}}"
                                      postcardid="{{::post.id}}"
                                      childpostid="{{::post.child_post_id}}"
                                      type="C"
                                      posttype="{{::post.post_type}}"
                                      id="cardVideo-{{post.cardID}}">
                        </manual-video>
                        <span class="customPlayPause"><span></span></span>
                    </div>
                </div>
                <!-- Linked image -->
                <div class="uploadImage" ng-if="!post.embed_code && post.image">
                    <a data-toggle="modal"
                       ng-click="showPostDetails(post.id,post.child_post_id,0,$index)"
                       data-target="#myModal{{::post.id}}">
                        <img ng-src="{{::post.image}}" imageonload="removePostLoading('postCard'+$index)">
                    </a>
                </div>
            </div>
            <!-- tags -->
            <div class="profileNewCoverBottomLink" ng-if="::(post.tags.length>0)">
                <ul>
                    <li ng-repeat="tag in ::post.tags">
                        <a href="/tag/{{::tag.tag_name}}">#
                            <span ng-bind-html="::(tag.tag_name | highlightNode:searchResults.query_arr)"></span>
                        </a>
                    </li>
                </ul>
            </div>
        </div>

        <div ng-if="::(user.guest==0)">
            <div class="profileCommentFooter addNew noBorderPos">
                <div class="left" ng-class="{'show2': popUpReportDropdrow==1}">
                    <a class="upvoteIcon"
                       data-toggle="tooltip"
                       ui-jq="tooltip"
                       data-original-title="{{::(post.isUpvote == 'Y' ? 'Remove Upvote' : 'Upvote')}}"
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
                       ng-if="::(post.allow_comment==1)"
                       ng-click="showPostDetails(post.id,post.child_post_id,1,$index)">
                        <img src="/assets/pages/img/speech_bubble4.png" alt=""/>
                    </a>
                    <span class="countN"
                          ng-if="post.totalComments !=0"> {{ post.totalComments  | thousandSuffix }}</span>
                    <div class="cardSmNav">
                        <a class="shrClk" ng-if="::(post.allow_share==1)"
                           data-toggle="tooltip" ui-jq="tooltip"
                           data-original-title="Share"
                           ng-click="openSharePostPopUp(post.id);">
                            <img src="/assets/pages/img/refresh4.png" alt=""/>
                        </a>
                        <span class="countN" ng-if="post.totalShare !=0"> {{ post.totalShare | thousandSuffix }}</span>
                        <div id="shareOverlay_{{::post.id}}"
                             ng-if="::(post.allow_share==1)"
                             ng-click="closeOverLayout();"
                             class="subOverlay"
                             style="display:none;"></div>

                        <div id="shareDropdrow_{{post.id}}" class="sub" style="display:none;">
                            <ul>
                                <li ng-if="::(post.child_post_user_id!=user.id)"
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
                       ng-if="::(post.created_by!=user.id && post.child_post_user_id!=user.id)"
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
					<span class="countN"
                          ng-if="post.totalPostViews && (post.post_type==1 ||post.post_type==5)">
                        {{ ::(post.totalPostViews | thousandSuffix) }}
                        &nbsp;{{showPostViewTxt(post, 'card')}}
                    </span>
                        <span class="countN"
                              ng-if="post.totalPostViews && !(post.post_type==1 ||post.post_type==5)"">
                        {{post.totalPostViews | thousandSuffix}}
                        &nbsp;{{showPostViewTxt(post, 'card')}}
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

        <div class="profileCommentFooter addNew noBorderPos" ng-if="::(user.guest!=0)">
            <div class="left">
                <a class="upvoteIcon" ng-click="redirecToLogin();">
                    <img src="/assets/pages/img/arrow2_t.png" alt=""
                    />
                </a>
                <span class="countN"> {{ (post.upvotes - post.downvotes) >0 ? '+'+(post.upvotes - post.downvotes) : (post.upvotes - post.downvotes) | thousandSuffix }}</span>
                <a class="tip" ng-click="redirecToLogin();">
                    <img src="/assets/pages/img/speech_bubble4.png" alt=""/>
                </a>
                <span class="countN"> {{ post.totalComments  | thousandSuffix }}</span>

                <div class="cardSmNav">
                    <a class="shrClk" ng-if="::(post.allow_share==1)"
                       data-toggle="tooltip" ui-jq="tooltip"
                       data-original-title="Share"
                       ng-click="openSharePostPopUp(post.id);">
                        <img src="/assets/pages/img/refresh4.png" alt=""/>
                    </a>
                    <span class="countN"> {{ post.totalShare | thousandSuffix }}</span>
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
					<span class="countN"
                          ng-if="post.totalPostViews && (post.post_type==1 ||post.post_type==5)">
                        {{::(post.totalPostViews | thousandSuffix)}}
                        &nbsp;{{showPostViewTxt(post, 'card')}}
                    </span>
                    <span class="countN"
                          ng-if="post.totalPostViews && !(post.post_type==1 ||post.post_type==5)">
                        {{post.totalPostViews | thousandSuffix}}
                        &nbsp;{{showPostViewTxt(post, 'card')}}
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
