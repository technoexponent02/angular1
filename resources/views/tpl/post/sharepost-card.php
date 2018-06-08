<div class="sharedSuccessLoader"></div>
<div class="modal fade shareModal" id="shareModal" tabindex="-1" role="dialog" aria-labelledby="myModalLabel"
     aria-hidden="true">
    <div class="modal-dialog customModal feedModal">
        <!-- Modal content-->
        <div class="modal-content">
            <div class="responsiveModalHeader">
                <a href="#" class="cancelBtn" data-dismiss="modal">Close</a>
            </div>
            <div class="modal-body">
                <a href="javascript:void(0);" class="modalCloseBtn responsiveNone" data-dismiss="modal">
                    <img src="assets/pages/img/cross.png" alt="">
                </a>
                <div class="modalBlockContent">
                    <div class="blockContent">
                        <div class="profileCommentBox clkProfileCommentBox" style="opacity:0;">
                            <div class="profileCommentBoxTop">
                                <div class="shareHead">
                                    <div class="post_type">
                                        <div class="cardMessageType shareInput">
                                            <input type="text" ng-model="caption" placeholder="Write caption here"
                                                   class="textBx"/>
                                        </div>
                                    </div>
                                </div>
                                <div class="shareBody">
                                    <div class="userStatusRow smUserStatus">
                                      
                                            <!-- {{ sharedPost.ask_anonymous }} -->
                                        <div ng-if="sharedPost.ask_anonymous!=1">    
                                            <div class="userStatusImage ng-scope"
                                                ng-if="originalPostProfileImage">
                                                <a ui-sref="account({ username: originalPostUserName })"
                                                style="background:url({{originalPostProfileImage}}) no-repeat;"></a>
                                            </div>
                                            <div class="userStatusImage {{originalPostUserColor}}"
                                                ng-if="!originalPostProfileImage">
                                                <a ui-sref="account({ username: originalPostUserName })">
                                                <span class="txt">
                                                    {{ originalPostFirstName.charAt(0)}}</span>
                                                </a>
                                            </div>
                                        </div>

                                        <div ng-if="sharedPost.ask_anonymous==1">    
                                            
                                            <div class="userStatusImage {{originalPostUserColor}}">
                                                <a >
                                                <span class="txt">
                                                    A</span>
                                                </a>
                                            </div>
                                        </div>







                                        <div class="userStatusInfo">
                                            <span class="userStatusInfoTtl clearfix"  ng-if="sharedPost.ask_anonymous!=1">
                                                <a class="ng-binding"
                                                ui-sref="account({ username: originalPostUserName })">
                                                {{ originalPostFirstName+ ' ' + originalPostLastName }}</a>
                                            </span>

                                            <span class="userStatusInfoTtl clearfix"  ng-if="sharedPost.ask_anonymous==1">
                                                <a class="ng-binding">Anonymous </a>
                                            </span>


                                            <div class="cardFollow clearfix">

                                                <small ng-if="showElapsedTime(sharedPost.created_at)">
                                                    <!--Shared this post -->
                                                    <time am-time-ago="sharedPost.created_at | amUtc | amLocal"></time>
                                                </small>
                                                <small ng-if="!showElapsedTime(sharedPost.created_at)">
                                                    <!--Shared this post  -->
                                                    <time>{{ sharedPost.created_at | amDateFormat:'DD MMM, YYYY - HH:mm'
                                                        }}
                                                    </time>
                                                </small>
                                            </div>
                                        </div>
                                        <div class="clearfix"></div>
                                    </div>
                                    <div class="post_type">

                                        <span class="noCaptionBx" ng-class="{'noCaption': sharedPost.caption==''}"></span>
                                        <p class="card-caption ng-scope" ng-if="sharedPost.caption">
                                            <span class="card-caption" ng-bind-html="sharedPost.caption | markupHTMLTags"></span>
                                        </p>
                                        <div class="catagoryTtl ng-scope">
                                            <div class="catagoryTagRow"
                                                 ng-if="(sharedPost.category || sharedPost.sub_category || (sharedPost.post_type == 3 && sharedPost.time_needed!=0))"
                                                 ng-class="{'nocvategorysmallgap': !(sharedPost.category || sharedPost.sub_category), 'withTime' : sharedPost.post_type==3}"
                                                >
                                                <a href=""
                                                   class="catagoryTtlHighLight ng-binding ng-scope"
                                                   ng-if="sharedPost.category.category_name">{{
                                                    sharedPost.category.category_name }}</a>
                                                <a href="" class="catagoryTtlHighLight ng-binding ng-scope"
                                                   ng-if="sharedPost.sub_category.category_name">
                                                    {{ sharedPost.sub_category.category_name }}</a>
                                                <div class="postTime"
                                                     ng-if="sharedPost.post_type == 3 && sharedPost.time_needed!=0">
                                                    {{sharedPost.time_needed}} &nbsp;min read
                                                </div>
                                            </div>
                                            <p class="ng-binding">{{ sharedPost.title }}
                                                <a href="" class="cardLoc ng-scope" ng-if="sharedPost.location">
                                                    <img src="assets/pages/img/location1.png" alt="Location">
                                                    <span data-toggle="tooltip"
                                                          ui-jq="tooltip"
                                                          data-original-title="{{ sharedPost.location }}"
                                                          class="ng-binding">
												{{ showLocation(sharedPost.location) }}
												</span>
                                                </a>
                                            </p>
                                        </div>
                                        <div class="postShortDesc"
                                             ng-show="sharedPost.short_description">
                                            <p class="ng-binding" ng-show="sharedPost.short_description">
                                                {{ sharedPost.short_description | cut:true:100:' ...' }}</p>
                                        </div>

                                        <!-- image post -->
                                        <div ng-if="sharedPost.post_type == 1">
                                            <p class="postLink" ng-if="sharedPost.source">
											<span>
												<a href="{{ sharedPost.source }}" target="_blank">
													<i class="fa fa-external-link"></i>
													{{ sharedPost.source | domainFilter }}
												</a>
											</span>
                                            </p>
                                            <div class="uploadImage">
                                                <a href="javascript:;">
                                                    <img ng-src="{{sharedPost.image}}">
                                                </a>
                                            </div>
                                        </div>
                                        <!-- video post -->
                                        <div ng-if="sharedPost.post_type == 2">
                                            <p class="postLink" ng-if="sharedPost.source">
											<span>
												<a href="{{ sharedPost.embed_code ? sharedPost.embed_code : sharedPost.source }}"
                                                   target="_blank">
													<i class="fa fa-external-link"></i>
													{{ sharedPost.source | domainFilter }}
												</a>
											</span>
                                            </p>
                                            <div class="uploadImage" ng-if="sharedPost.embed_code">
                                                <div class="uploadVidPreview">
                                                    <iframe
                                                            src="{{ sharedPost.embed_code | trustUrl }}"
                                                            height="200"
                                                            webkitallowfullscreen mozallowfullscreen allowfullscreen>
                                                    </iframe>
                                                </div>
                                            </div>
                                            <div class="uploadImage" ng-if="sharedPost.video">
                                                <div class="uploadLocVidPreview">
                                                    <!-- MANUAL VIDEO -->
                                                    <video class="videoTag" controls loop playsinline webkit-playsinline
                                                           ng-attr-poster="{{sharedPost.video_poster}}"
                                                    >
                                                        <source ng-src="{{(sharedPost.video | trustUrl)}}">
                                                    </video>
                                                </div>
                                            </div>
                                        </div>
                                        <!-- Article post -->
                                        <div ng-if="sharedPost.post_type == 3">
                                            <p class="postLink" ng-if="sharedPost.source">
											<span>
												<a href="{{ sharedPost.external_link ? sharedPost.external_link : sharedPost.source }}"
                                                   target="_blank">
													<i class="fa fa-external-link"></i>
													{{ sharedPost.source | domainFilter }}
												</a>
											</span>
                                            </p>
                                            <div class="uploadImage" ng-if="sharedPost.image">
                                                <a href="javascript:;">
                                                    <img ng-src="{{sharedPost.image}}">
                                                </a>
                                            </div>
                                            <div class="uploadBox"
                                                 ng-bind-html="sharedPost.content | markupHTMLTags "></div>
                                        </div>
                                        <!-- Link post -->
                                        <div ng-if="sharedPost.post_type == 4">
                                            <div ng-if="sharedPost.source" class="postLinkBtn">
                                                <a href="{{ sharedPost.external_link ? sharedPost.external_link : sharedPost.source }}"
                                                   target="_blank" class="btn btn-default btn-sm">
                                                    <i class="fa fa-external-link"></i>
                                                    {{ sharedPost.source | domainFilter }}
                                                </a>
                                            </div>
                                            <!-- Linked video -->
                                            <div class="uploadImage" ng-if="sharedPost.embed_code">
                                                <div class="uploadVidPreview">
                                                    <iframe
                                                            src="{{ sharedPost.embed_code | trustUrl }}"
                                                            height="200"
                                                            webkitallowfullscreen mozallowfullscreen allowfullscreen>
                                                    </iframe>
                                                </div>
                                            </div>
                                            <!-- Linked image -->
                                            <div class="uploadImage" ng-if="!sharedPost.embed_code && sharedPost.image">
                                                <a href="javascript:;">
                                                    <img ng-src="{{sharedPost.image}}">
                                                </a>
                                            </div>
                                        </div>
                                        <!-- Status post -->
                                        <div ng-if="sharedPost.post_type == 5">
                                            <p class="postLink" ng-if="sharedPost.source">
											<span>
												<a href="{{ sharedPost.embed_code ? sharedPost.embed_code : sharedPost.source }}"
                                                   target="_blank">
													<i class="fa fa-external-link"></i>
													{{ sharedPost.source | domainFilter }}
												</a>
											</span>
                                            </p>
                                            <!-- Linked video -->
                                            <div class="uploadImage" ng-if="sharedPost.embed_code">
                                                <div class="uploadVidPreview">
                                                    <iframe
                                                            src="{{ sharedPost.embed_code | trustUrl }}"
                                                            height="200"
                                                            webkitallowfullscreen mozallowfullscreen allowfullscreen>
                                                    </iframe>
                                                </div>
                                            </div>
                                            <!-- Uploaded video -->
                                            <div class="uploadImage" ng-if="sharedPost.video">
                                                <div class="uploadLocVidPreview">
                                                    <video class="videoTag" controls loop playsinline webkit-playsinline
                                                           ng-attr-poster="{{sharedPost.video_poster}}"
                                                    >
                                                        <source ng-src="{{(sharedPost.video | trustUrl)}}">
                                                    </video>
                                                </div>
                                            </div>
                                            <!-- Linked image -->
                                            <div class="uploadImage" ng-if="!sharedPost.embed_code && sharedPost.image">
                                                <a data-toggle="modal" ng-click="showPostDetails(sharedPost.id,0)"
                                                   data-target="#myModal{{ sharedPost.id }}">
                                                    <img ng-src="{{sharedPost.image}}">
                                                </a>
                                            </div>
                                        </div>

                                        

                                          <!-- Question post -->
                                          <div ng-if="sharedPost.post_type == 6">
                                            <p class="postLink" ng-if="sharedPost.source">
											<span>
												<a href="{{ sharedPost.embed_code ? sharedPost.embed_code : sharedPost.source }}"
                                                   target="_blank">
													<i class="fa fa-external-link"></i>
													{{ sharedPost.source | domainFilter }}
												</a>
											</span>
                                            </p>
                                            <!-- Linked video -->
                                            <div class="uploadImage" ng-if="sharedPost.embed_code">
                                                <div class="uploadVidPreview">
                                                    <iframe
                                                            src="{{ sharedPost.embed_code | trustUrl }}"
                                                            height="200"
                                                            webkitallowfullscreen mozallowfullscreen allowfullscreen>
                                                    </iframe>
                                                </div>
                                            </div>
                                            <!-- Uploaded video -->
                                            <div class="uploadImage" ng-if="sharedPost.video">
                                                <div class="uploadLocVidPreview">
                                                    <video class="videoTag" controls loop playsinline webkit-playsinline
                                                           ng-attr-poster="{{sharedPost.video_poster}}"
                                                    >
                                                        <source ng-src="{{(sharedPost.video | trustUrl)}}">
                                                    </video>
                                                </div>
                                            </div>
                                            <!-- Linked image -->
                                            <div class="uploadImage" ng-if="!sharedPost.embed_code && sharedPost.image">
                                                <a data-toggle="modal" 
                                                   data-target="#myModal{{ sharedPost.id }}">
                                                    <img ng-src="{{sharedPost.image}}">
                                                </a>
                                            </div>
                                        </div>

                                <!-- End question post share card -->


                                        <div class="profileNewCoverBottomLink ng-scope"
                                             ng-if="sharedPost.tags">
                                            <ul>
                                                <li ng-repeat="tag in sharedPost.tags">
                                                <!-- <a href="{{ '/tag/' + tag.tag_name }}"># {{tag.tag_name}}</a> -->
                                                <div ng-if="::sharedPost.post_type=='6'">
                                                    <!-- <a href="/tag/{{::tag.tag_name}}" ng-if=":: tag.question_tag!=sharedPost.caption">#{{::tag.tag_name | tagreplace}}</a>(22-03-18) tag text change -->

                                                    <a href="/tag/{{::tag.tag_name}}" ng-if=":: tag.question_tag!=sharedPost.caption">#{{::tag.tag_text }}</a> <!-- (22-03-18) tag text change -->

                                                                    
                                                    <a href="/questions/{{::tag.tag_name}}" class="questionButtn" ng-if=":: tag.question_tag==sharedPost.caption" >{{tag.tagCount=='0'|| !tag.tagCount ?'No answer yet': tag.tagCount=='1'? '1 answer': tag.tagCount+' answers' }}</a>
                                                </div>  
                                                <div ng-if="::sharedPost.post_type!='6'">
                                                <!-- <a href="/tag/{{::tag.tag_name}}" >#{{::tag.tag_name | tagreplace}}</a> (22-03-18) tag text change-->
                                                <a href="/tag/{{::tag.tag_name}}" >#{{::tag.tag_text }}</a> <!-- (22-03-18) tag text change-->
                                                    
                                                </div>
                                                </li>
                                            </ul>
                                        </div>

                                    </div>
                                </div>
                                <?php /*
							 <div class="collectionshare">
								<div class="con">
									<label class="saveCollection checkradioarea">
					                    <input type="checkbox" class="js-switch"
					                            ng-init="allow_comment = true"
					                            ui-switch="{color: '#6d5cae', size: 'small'}"
					                            data-switchery="true"
					                            ng-model="allow_comment"
					                            />
					                    <span class="names switchTtl">Allow comment</span>
				                	</label>
								</div>
								<div class="con">
									 <label class="saveCollection checkradioarea">
					                    <input type="checkbox" class="js-switch"
					                            ng-init="allow_share = true"
					                            ui-switch="{color: '#6d5cae', size: 'small'}"
					                            ng-model="allow_share"
					                            />
					                    <span class="names switchTtl">Allow share</span>
					                </label>
								</div>
								*/ ?>
                                <div class="shareselect">
                                    <div class="con">
                                        <div pg-form-group
                                             class="form-group form-group-default form-group-default-select">
                                            <label class="">Privacy</label>
                                            <select class="form-control" name="privacy_id"
                                                    ng-model="privacy_id"
                                                    ng-init="privacy_id = privacies[0]"
                                                    ng-options="item.privacy_name for item in privacies"
                                                    title="Choose privacy">
                                                {{privacy_id}}
                                            </select>
                                        </div>
                                    </div>
                                </div>
                            </div>
                        </div>
                        <!-- OLD POPUP -->
                        <div class="profileCommentBoxTop" style="display:none;">
                            <div class="shareHead">
                                <div class="userStatusRow">
                                    <div class="userStatusImage">
                                        <a ui-sref="account({ username: user.username })">
                                            <img
                                                    ng-src="{{user.profile_image}}"
                                                    alt="profile image">
                                        </a>
                                    </div>
                                    <div class="userStatusInfo">
										<span class="userStatusInfoTtl withLocation clearfix">
											<a ui-sref="account({ username: user.username })">{{ user.first_name
												+ ' ' + user.last_name }}</a>
											<a href="#" class="follwBtn">
												<i class="fa fa-plus-circle" aria-hidden="true"></i>
											</a>
											<small class="right normal" ng-if="post.location">
												<img src="assets/pages/img/location1.png" alt="Location">
												<span data-toggle="tooltip" class="tip m-b-5 m-r-5" ui-jq="tooltip"
                                                      data-original-title="{{ post.location }}">{{ showLocation(post.location) }}</span>
											</small>
										</span>
                                        <p>
											<span class="clearfix">
												<small>&nbsp;{{ post.user.about_me }}</small>
											</span>
                                            <span class="block clearfix"></span>
                                            <!-- <small>Shared this post {{ post.created_at | elapsed }}</small> -->
                                        </p>
                                    </div>
                                    <div class="clearfix"></div>
                                </div>
                                <div class="post_type">
                                    <div class="cardMessageType shareInput">
                                        <input type="text" ng-model="caption" placeholder="What's on your mind?"
                                               class="textBx"/>
                                    </div>
                                    <div class="catagoryTtl">
                                        <div class="catagoryTagRow">
                                            <a href="#" class="catagoryTtlHighLightBlack"
                                               ng-if="post.category.category_name">{{ post.category.category_name }}</a>
                                            <a href="#" class="catagoryTtlHighLight"
                                               ng-if="post.sub_category.category_name">{{
                                                post.sub_category.category_name }}</a>
                                        </div>
                                    </div>
                                </div>
                            </div>
                            <div class="shareBody">
                                <div class="post_type">
                                    <div class="catagoryTtl">
                                        <p>{{ post.title }}</p>
                                    </div>
                                    <div class="postShortDesc" ng-show="post.short_description">
                                        <p ng-show="post.short_description">{{ post.short_description | cut:true:100:'
                                            ...' }}</p>
                                    </div>
                                    <!-- image post -->
                                    <div ng-if="post.post_type == 1">
                                        <p class="postLink" ng-if="post.source">
											<span>
												<a href="{{ post.source }}" target="_blank">
													<i class="fa fa-external-link"></i>
													{{ post.source | domainFilter }}
												</a>
											</span>
                                        </p>
                                        <div class="uploadImage">
                                            <a href="javascript:void(0)">
                                                <img ng-src="{{post.image}}">
                                            </a>
                                        </div>
                                        <div class="uploadImage" ng-if="post.video">
                                            <div class="uploadLocVidPreview">
                                                <video class="videoTag" controls loop playsinline webkit-playsinline
                                                       ng-attr-poster="{{post.video_poster}}"
                                                >
                                                    <source ng-src="{{(post.video | trustUrl)}}">
                                                </video>
                                            </div>
                                        </div>

                                    </div>
                                    <!-- video post -->
                                    <div ng-if="post.post_type == 2">
                                        <p class="postLink" ng-if="post.source">
											<span>
												<a href="{{ post.embed_code ? post.embed_code : post.source }}"
                                                   target="_blank">
													<i class="fa fa-external-link"></i>
													{{ post.source | domainFilter }}
												</a>
											</span>
                                        </p>
                                        <div class="uploadImage" ng-if="post.embed_code">
                                            <div class="uploadVidPreview">
                                                <iframe
                                                        src="{{ post.embed_code | trustUrl }}"
                                                        height="200"
                                                        webkitallowfullscreen mozallowfullscreen allowfullscreen>
                                                </iframe>
                                            </div>
                                        </div>
                                        <div class="uploadImage" ng-if="post.video">
                                            <div class="uploadLocVidPreview">
                                                <video class="videoTag" controls loop playsinline webkit-playsinline
                                                       ng-attr-poster="{{post.video_poster}}"
                                                >
                                                    <source ng-src="{{(post.video | trustUrl)}}">
                                                </video>
                                            </div>
                                        </div>
                                    </div>
                                    <!-- Article post -->
                                    <div ng-if="post.post_type == 3">
                                        <p class="postLink" ng-if="post.source">
											<span>
												<a href="{{ post.external_link ? post.external_link : post.source }}"
                                                   target="_blank">
													<i class="fa fa-external-link"></i>
													{{ post.source | domainFilter }}
												</a>
											</span>
                                        </p>
                                        <div class="uploadImage" ng-if="post.image">
                                            <a href="javascript:;">
                                                <img ng-src="{{post.image}}">
                                            </a>
                                        </div>
                                        <div class="uploadBox" ng-bind-html="post.content | markupHTMLTags "></div>
                                    </div>
                                    <!-- Link post -->
                                    <div ng-if="post.post_type == 4">
                                        <div ng-if="post.source" class="postLinkBtn">
                                            <a href="{{ post.external_link ? post.external_link : post.source }}"
                                               target="_blank" class="btn btn-default btn-sm">
                                                <i class="fa fa-external-link"></i>
                                                {{ post.source | domainFilter }}
                                            </a>
                                        </div>
                                        <!-- Linked video -->
                                        <div class="uploadImage" ng-if="post.embed_code">
                                            <div class="uploadVidPreview">
                                                <iframe
                                                        src="{{ post.embed_code | trustUrl }}"
                                                        height="200"
                                                        webkitallowfullscreen mozallowfullscreen allowfullscreen>
                                                </iframe>
                                            </div>
                                        </div>
                                        <!-- Linked image -->
                                        <div class="uploadImage" ng-if="!post.embed_code && post.image">
                                            <a href="javascript:;">
                                                <img ng-src="{{post.image}}">
                                            </a>
                                        </div>
                                    </div>
                                    <!-- Status post -->
                                    <div ng-if="post.post_type == 5">
                                        <p class="postLink" ng-if="post.source">
											<span>
												<a href="{{ post.embed_code ? post.embed_code : post.source }}"
                                                   target="_blank">
													<i class="fa fa-external-link"></i>
													{{ post.source | domainFilter }}
												</a>
											</span>
                                        </p>
                                        <!-- Linked video -->
                                        <div class="uploadImage" ng-if="post.embed_code">
                                            <div class="uploadVidPreview">
                                                <iframe
                                                        src="{{ post.embed_code | trustUrl }}"
                                                        height="200"
                                                        webkitallowfullscreen mozallowfullscreen allowfullscreen>
                                                </iframe>
                                            </div>
                                        </div>
                                        <!-- Uploaded video -->
                                        <div class="uploadImage" ng-if="post.video">
                                            <div class="uploadLocVidPreview">
                                                <video class="videoTag" controls loop playsinline webkit-playsinline
                                                       ng-attr-poster="{{post.video_poster}}"
                                                >
                                                    <source ng-src="{{(post.video | trustUrl)}}">
                                                </video>
                                            </div>
                                        </div>
                                        <!-- Linked image -->
                                        <div class="uploadImage" ng-if="!post.embed_code && post.image">
                                            <a <?php /*ng-if="!post.external_link" */ ?> data-toggle="modal"
                                                                                         ng-click="showPostDetails(post.id,0)"
                                                                                         data-target="#myModal{{ post.id }}">
                                                <img ng-src="{{post.image}}">
                                            </a>
                                        </div>
                                    </div>
                                    <!-- tags -->
                                    <div class="profileNewCoverBottomLink" ng-if="post.tags">
                                        <ul>
                                            <li ng-repeat="tag in post.tags">
                                               <!-- <a href="{{ '/tag/' + tag.tag_name }}"># {{tag.tag_name}}</a> (22-03-18) tag text change-->
                                               <a href="{{ '/tag/' + tag.tag_name }}"># {{tag.tag_text}}</a> <!-- (22-03-18) tag text change -->
                                            </li>
                                        </ul>
                                    </div>
                                </div>
                            </div>

                            <div class="shareselect">
                                <div class="con">
                                    <div pg-form-group class="form-group form-group-default form-group-default-select">
                                        <label class="">Privacy</label>
                                        <select class="form-control" name="privacy_id"
                                                ng-model="privacy_id"
                                                ng-init="privacy_id = privacies[0]"
                                                ng-options="item.privacy_name for item in privacies"
                                                title="Choose privacy">
                                            {{privacy_id}}
                                        </select>
                                    </div>
                                </div>
                            </div>
                        </div>

                    </div>
                </div>
                <div class="modalShareFooter clearfix">
                    <input type="hidden" ng-model="postId" value="{{sharedPost.id}}"/>
                    <button class="btn btn-success btn-block" type="submit" name="btnSubmit"
                            share="shareThisPost(sharedPost.id,childPostId,postUserId);">SHARE NOW
                    </button>
                </div>
            </div>
        </div>
    </div>
</div>
</div>
</div>