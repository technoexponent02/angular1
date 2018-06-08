<div class="row">
    <div ng-class="isFullContainer() ? 'col-md-10 col-lg-10 col-md-push-1 col-lg-push-1' :'col-md-8 col-lg-8'">
        <div class="tab-content m-t-20" id="tabContentN">
            <div class="profileMiddlesubHeader postingformsubtab newTabStyle sideL" id="newTabStyle"
            ng-if="!hideLeftTabs()">
                 <ul class="inline">
                    <li>
                        <a ng-if="!stateData.editPost" ui-sref="post-add.<?php echo $post_type; ?>.general" ui-sref-active="active">General</a>
                        <a ng-if="stateData.editPost" ui-sref="post-edit.<?php echo $post_type; ?>.general({ id: formData.id })" ui-sref-active="active">General</a>
                    </li>
                    <li ng-if="stateData.postType === 'article'">
                        <a ng-if="!stateData.editPost" ui-sref="post-add.<?php echo $post_type; ?>.content" ui-sref-active="active"
                        eat-click-if="disableGoToContent(postForm)"
                        >Content</a>
                        <a ng-if="stateData.editPost" ui-sref="post-edit.<?php echo $post_type; ?>.content({ id: formData.id })" ui-sref-active="active"
                           eat-click-if="disableGoToContent(postForm)"
                        >Content</a>
                    </li>
                    <li>
                        <a ng-if="!stateData.editPost" ui-sref="post-add.<?php echo $post_type; ?>.advance" ui-sref-active="active"
                        eat-click-if="disableGoToAdvance(postForm)"
                        >Advance</a>
                        <a ng-if="stateData.editPost" ui-sref="post-edit.<?php echo $post_type; ?>.advance({ id: formData.id })" ui-sref-active="active"
                           eat-click-if="disableGoToAdvance(postForm)"
                        >Advance</a>
                    </li>
                    <li>
                        <a ng-if="!stateData.editPost" ui-sref="post-add.<?php echo $post_type; ?>.social({ id: formData.id })" ui-sref-active="active"
                        eat-click-if="disableGoToSocial(postForm)"
                        >Social</a>
                        <a ng-if="stateData.editPost" ui-sref="post-edit.<?php echo $post_type; ?>.social({ id: formData.id })" ui-sref-active="active"
                           eat-click-if="disableGoToSocial(postForm)"
                        >Social</a>
                    </li>
				</ul>
            </div>
            <div class="profileCommentBox full sideR" id="posting_form"
                    ng-class="{'posting_form_full': isFullContainer(), 'hide-border': isStatusPost() || isArticlePost() }">
                <div ng-class="{postingFormSlide: !isFullContainer()}">
                    <div class="profileCommentBoxTop nWprofileCommentBoxTop">
                        <div class="loaderImage" ng-class="{'remove': removePostLoader}"></div>
                        <div class="loaderImage" ng-class="{'remove': formInfo.removePostLoader}"></div>
                        <!-- Progress Bar -->
                        <div class="progress importProgress" ng-if="formInfo.importProgress">
                            <div class="progress-bar progress-bar-success"
                                 ng-style="{'width' : formInfo.importProgress+'%'}"></div>
                        </div>

                        <form name="postForm" class="newFormCont"  role="form" novalidate>
                            <!-- nested state views will be injected here -->
                            <div id="form-views" ui-view></div>
                        </form>
                    </div>
                </div>
            </div>
        </div>
    </div>
    <div class="col-md-4 col-lg-4 hidden-sm hidden-xs" ng-if="!isFullContainer() && !isStatusPost() && !isArticlePost()">
        <div class="postingRgtbx m-t-20 stickyCard">
            <div class="postRightNeW">
				<div class="profileCommentBox clkProfileCommentBox"> 
					<div class="profileCommentBoxTop">
						<div class="profileCommentScroll">							
							<div class="post_type">
								<?php /*
                                <span ng-if="formData.source_domain" class="{{sourceLinkClass()}}">
									<span>
										<a href="{{ formData.link ? formData.link : (formData.source_domain ? formData.source_domain : '')}}" class="{{ $state.$current.parent.name === 'post-add.link' ? 'btn btn-default btn-sm' : '' }}" target="_blank">{{formData.domain ? formData.domain : formData.source_domain }}
										</a>
									</span>
								</span>
                                */ ?>
								<div class="uploadImage">
									<?php if ($post_type == 'video') { ?>
											<div class="uploadImgPreview" ng-if="!formData.videoSrc">
												<img ng-src="{{ defaultVidImage }}"  alt="video placeholder">
											</div>
											<div class="imgUploadPreview videoLP"
								                ng-if="formData.videoSrc && uploadPreviewType === 'video'" 
								                class="imgUploadPreview">
												<video controls loop playsinline webkit-playsinline>
								                    <source ng-src="{{formData.videoSrc}}">
								                </video>
											</div>
								            <div ng-if="formData.videoSrc && uploadPreviewType === 'embed'" 
								                class="imgUploadPreview uploadLocVidview uploadVidview">
								                <iframe
								                    ng-src="{{ formData.videoSrc }}"
								                    webkitallowfullscreen mozallowfullscreen allowfullscreen>
								                </iframe>
								            </div>
									<?php } else if ($post_type == 'link') { ?>
										<div ng-if="!formInfo.showVideoInput">
											<div class="uploadImgPreview" ng-if="showImageLP()">
												<img ng-src="{{ formData.imageSrc ? formData.imageSrc : defaultImage }}"  alt="image placeholder" fallback-src="imgError()">
											</div>
										</div>
										<div ng-if="formInfo.showVideoInput">
											<div class="uploadImgPreview" ng-if="!formData.videoSrc">
												<img ng-src="{{ defaultVidImage }}"  alt="video placeholder">
											</div>
											<div class="imgUploadPreview videoLP"
								                ng-if="formData.videoSrc && uploadPreviewType === 'video'">
												<video controls loop playsinline webkit-playsinline>
								                    <source ng-src="{{formData.videoSrc}}">
								                </video>
											</div>
								            <div ng-if="formData.videoSrc && uploadPreviewType === 'embed'" 
								                class="imgUploadPreview uploadLocVidview uploadVidview">
								                <iframe
								                    ng-src="{{ formData.videoSrc }}"
								                    webkitallowfullscreen mozallowfullscreen allowfullscreen>
								                </iframe>
								            </div>
										</div>
									<?php } else { ?>
										<div class="uploadImgPreview" ng-if="showImageLP()">
											<img ng-src="{{ formData.imageSrc ? formData.imageSrc : defaultImage }}" alt="image placeholder" fallback-src="imgError()">
										</div>
									<?php } ?>
								</div>	
								<div class="catagoryTtl">
									<div class="catagoryTagRow {{ $state.$current.parent.name === 'post-add.article' ? 'withTime' : '' }}">
										<a ng-click="disabled()" class="catagoryTtlHighLight">{{formData.selectedMainCategory ? formData.selectedMainCategory.category_name : "[Category]"}}</a>
										<a ng-click="disabled()" class="catagoryTtlHighLight">{{formData.selectedSubCategory ? formData.selectedSubCategory.category_name : "[Subcategory]"}}</a>
										<div class="postTime"
											 ng-if="$state.$current.parent.name === 'post-add.article'">
											{{ formInfo.time_needed > 1 ? formInfo.time_needed + ' minutes read' : formInfo.time_needed + 'minute read' }}
										</div>
									</div>
									<p>{{ formData.title ? formData.title : '[Title]' }}
										<small class="cardLoc">
											<img src="<?php echo asset('assets/pages/img/location1.png');?>" alt="">
                                            <span data-toggle="tooltip" ui-jq="tooltip" data-original-title="{{formData.location}}">{{ formData.location ? showLocation(formData.location) : '[Location]' }}</span>
										</small>
									</p>
								</div>
                                <p ng-if="formData.source" class="{{sourceLinkClass()}}" ng-bind-html="bindSourceHtml(formData)"></p>								
								<div class="postShortDesc" ng-if="$state.$current.parent.name !== 'post-add.article' && $state.$current.parent.name !== 'post-edit.article'">
									<p class=""> {{ formData.short_description ? (formData.short_description | limitShortDesc) : '[Short Description]' }}</p>
								</div>
								<div class="postShortDesc"
										ng-if="$state.$current.parent.name === 'post-add.article' || $state.current.name === 'post-edit.article.general'"
										ng-bind-html="formData.content ? (formData.content | lpArticleContent) 
										: (formInfo.lp_desc ? formInfo.lp_desc : '[Content]') | markupHTMLTags "
										>
								</div>
								<div class="profileNewCoverBottomLink">
									<ul>
										<li class="" ng-repeat="tag in tags.tags">
											<a href="#"># {{tag.value}} </a>
										</li>
										<li class="" ng-hide="tags.getNumberOfTags() > 0">
											<a href="#"> # [tags] </a>
										</li>
									</ul>
								</div>								
								<p class="card-caption">
									<!-- <span class="card-caption">{{ formData.caption ? formData.caption : '[Caption]' }}</span>-->
									<span class="card-caption" ng-bind-html="formData.caption | highlightTag"></span>
								</p>
							</div>
							<div class="userStatusRow smUserStatus" ng-class="{'noBio': user.about_me=='' || user.about_me==null}">
								<div class="userStatusImage" ng-if="user.profile_image">
                                    <a ng-href="/profile/{{::user.username}}" style="background:url({{user.profile_image}}) no-repeat;"></a>
                                </div>
                                <div ng-if="::(!user.profile_image)" class="userStatusImage {{::user.user_color}}">
                                    <a ng-href="/profile/{{::user.username}}">
                                        <span class="txt">{{::user.first_name.charAt(0)}}</span>
                                    </a>
                                </div>
								<div class="userStatusInfo">
									<span class="userStatusInfoTtl clearfix">
										<a href="javascript:void(0);">{{user.first_name + " " + user.last_name}}</a>
									</span>
									<div class="cardFollow clearfix">
										<small>
											<time>Shared this post 0 seconds ago.</time>
										</small>
									</div>	
									<div class="userStatusInfo info" ng-show="user.about_me">
										<p class="userAbout"> 
											<span class="clearfix">
												<small class="ng-binding">{{user.about_me}}</small>
											</span>
										</p>
									</div>
								</div>
								<div class="clearfix"></div>
							</div>
						</div>
					</div>
				</div>
            </div>
        </div>
    </div>
    <div class="col-md-4 col-lg-4 hidden-sm hidden-xs" ng-if="isStatusPost()">
        <div class="postingRgtbx m-t-20 stickyCard">
            <div class="postRightNeW">
				<div class="profileCommentBox clkProfileCommentBox">    
					<div class="profileCommentBoxTop">
						<div class="profileCommentScroll">
							<div class="post_type">
								<div class="uploadImage">
									<div class="uploadImgPreview imgbxc " 
										ng-if="!formData.videoSrc && uploadPreviewType === 'video'">
										<img ng-src="{{ defaultVidImage }}" alt="video placeholder">
									</div>
									<div class="videoLP imgUploadPreview"
						                ng-if="formData.videoSrc && uploadPreviewType === 'video'" 
						                class="imgUploadPreview">
										<video controls loop playsinline webkit-playsinline>
						                    <source ng-src="{{formData.videoSrc}}">
						                </video>
									</div>
						            <div ng-if="formData.videoSrc && uploadPreviewType === 'embed'" 
						                class="imgUploadPreview uploadLocVidview uploadVidview">
						                <iframe
						                    ng-src="{{ formData.videoSrc }}"
						                    webkitallowfullscreen mozallowfullscreen allowfullscreen>
						                </iframe>
						            </div>
									<div class="uploadImgPreview imgbxc "
											ng-if="uploadPreviewType === 'image'">
										<img ng-src="{{ formData.imageSrc ? formData.imageSrc : defaultImage }}"  alt="image placeholder" fallback-src="imgError()">
									</div>
								</div>
								<p class="card-caption">
                                    <span class="card-caption stsCap" ng-bind-html="formData.caption | highlightTag"></span>
									<span class="cardLoc">
										<img src="/assets/pages/img/location1.png" alt="">
										<span data-toggle="tooltip" ui-jq="tooltip" data-original-title="{{formData.location}}">{{ formData.location ? showLocation(formData.location) : '[Location]' }}</span>
									</span>
								</p>
								<div class="profileNewCoverBottomLink">
									<ul>
										<li class="" ng-repeat="tag in tags.tags">
											<a href="#"># {{tag.value}} </a>
										</li>
										<li class="" ng-hide="tags.getNumberOfTags() > 0">
											<a href="#"> # [tags] </a>
										</li>
									</ul>
								</div>
								<?php /*<p class="postLink" ng-if="formData.source_domain">
									<span>
										<a href="{{ formData.source_domain ? formData.source_domain : ''}}" target="_blank">
											{{ formData.source_domain ? formData.source_domain: '' }}
										</a>
									</span>
								</p> */ ?>
                                <p class="postLink" ng-if="formData.source">
									<span>
										<a href="{{formData.source}}" target="_blank">
											{{formData.source | domainFilter}}
										</a>
									</span>
                                </p>
							</div>
							<div class="userStatusRow smUserStatus">
                                <div class="userStatusImage" ng-if="user.profile_image">
                                    <a href="/profile/{{::user.username}}" style="background:url({{user.profile_image}}) no-repeat;"></a>
                                </div>
                                <div ng-if="::(!user.profile_image)" class="userStatusImage {{::user.user_color}}">
                                    <a href="/profile/{{::user.username}}">
                                        <span class="txt">{{::user.first_name.charAt(0)}}</span>
                                    </a>
                                </div>
								<div class="userStatusInfo">
									<span class="userStatusInfoTtl clearfix">
										<a href="#">{{::(user.first_name + " " + user.last_name)}}</a>
									</span>
									<div class="cardFollow clearfix">
										<small>
											<time>Shared this post 0 seconds ago.</time>
										</small>
									</div>
									<div class="userStatusInfo info">
										<p class="userAbout">  
											<span class="clearfix">
												<small>{{::user.about_me}}</small>
											</span>
										</p>
									</div>
								</div>	
								<div class="clearfix"></div>
							</div>
						</div>
					</div>
				</div>
            </div>
        </div>
    </div>
	<div class="col-md-4 col-lg-4 hidden-sm hidden-xs" ng-if="isArticlePost()">
        <div class="postingRgtbx m-t-20 stickyCard">
            <div class="postRightNeW">
				<div class="profileCommentBox clkProfileCommentBox"> 
					<div class="profileCommentBoxTop">
						<div class="profileCommentScroll">
							<div class="post_type">
								<?php /*
                                <span ng-if="formData.source_domain" class="{{sourceLinkClass()}}">
									<span>
										<a href="{{ formData.link ? formData.link : (formData.source_domain ? formData.source_domain : '')}}" class="{{ $state.$current.parent.name === 'post-add.link' ? 'btn btn-default btn-sm' : '' }}" target="_blank">{{formData.domain ? formData.domain : formData.source_domain }}
										</a>
									</span>
								</span>
                                */ ?>
								<div class="uploadImage">
									<?php if ($post_type == 'video') { ?>
											<div class="uploadImgPreview imgbxc " ng-if="!formData.videoSrc">
												<img ng-src="{{ defaultVidImage }}"  alt="video placeholder">
											</div>
											<div class="imgUploadPreview videoLP"
								                ng-if="formData.videoSrc && uploadPreviewType === 'video'" 
								                class="imgUploadPreview">
												<video controls loop playsinline webkit-playsinline>
								                    <source ng-src="{{formData.videoSrc}}">
								                </video>
											</div>
								            <div ng-if="formData.videoSrc && uploadPreviewType === 'embed'" 
								                class="imgUploadPreview uploadLocVidview uploadVidview">
								                <iframe
								                    ng-src="{{ formData.videoSrc }}"
								                    webkitallowfullscreen mozallowfullscreen allowfullscreen>
								                </iframe>
								            </div>
									<?php } else if ($post_type == 'link') { ?>
										<div ng-if="!formInfo.showVideoInput">
											<div class="uploadImgPreview imgbxc " ng-if="showImageLP()">
												<img ng-src="{{ formData.imageSrc ? formData.imageSrc : defaultImage }}"  alt="image placeholder" fallback-src="imgError()">
											</div>
										</div>
										<div ng-if="formInfo.showVideoInput">
											<div class="uploadImgPreview imgbxc " ng-if="!formData.videoSrc">
												<img ng-src="{{ defaultVidImage }}"  alt="video placeholder">
											</div>
											<div class="imgUploadPreview videoLP"
								                ng-if="formData.videoSrc && uploadPreviewType === 'video'">
												<video controls loop playsinline webkit-playsinline>
								                    <source ng-src="{{formData.videoSrc}}">
								                </video>
											</div>
								            <div ng-if="formData.videoSrc && uploadPreviewType === 'embed'" 
								                class="imgUploadPreview uploadLocVidview uploadVidview">
								                <iframe
								                    ng-src="{{ formData.videoSrc }}"
								                    webkitallowfullscreen mozallowfullscreen allowfullscreen>
								                </iframe>
								            </div>
										</div>
									<?php } else { ?>
										<div class="uploadImgPreview imgbxc " ng-if="showImageLP()">
											<img ng-src="{{ formData.imageSrc ? formData.imageSrc : defaultImage }}" alt="image placeholder" fallback-src="imgError()">
										</div>
									<?php } ?>
								</div>	
                                <p ng-if="formData.source" class="{{sourceLinkClass()}}" ng-bind-html="bindSourceHtml(formData)"></p>
								<div class="catagoryTtl">
									<div class="catagoryTagRow {{ $state.$current.parent.name === 'post-add.article' ? 'withTime' : '' }}">
										<a ng-click="disabled()" class="catagoryTtlHighLight">{{formData.selectedMainCategory ? formData.selectedMainCategory.category_name : "[Category]"}}</a>
										<a ng-click="disabled()" class="catagoryTtlHighLight">{{formData.selectedSubCategory ? formData.selectedSubCategory.category_name : "[Subcategory]"}}</a>
										<div class="postTime"
											 ng-if="$state.$current.parent.name === 'post-add.article'">
											{{ formInfo.time_needed > 1 ? formInfo.time_needed + ' minutes read' : formInfo.time_needed + 'minute read' }}
										</div>
									</div>
									
								</div>
								<div class="catagoryTtl qtl">					
									<p>
										<span>{{ formData.caption ? formData.caption : '[Question]' }}</span>
										<!-- <span class="card-caption" ng-bind-html="formData.caption | highlightTag"></span> -->
										<span class="cardLoc">
											<img src="/assets/pages/img/location1.png" alt="">
											<span data-toggle="tooltip" ui-jq="tooltip" data-original-title="{{formData.location}}">{{ formData.location ? showLocation(formData.location) : '[Location]' }}</span>
										</span>
									</p>
								</div>																
								<div class="postShortDesc" ng-if="$state.$current.parent.name !== 'post-add.article' && $state.$current.parent.name !== 'post-edit.article'">
									<p class=""> {{ formData.short_description ? (formData.short_description | limitShortDesc) : '[Short Description]' }}</p>
								</div>
								<div class="postShortDesc"
										ng-if="$state.$current.parent.name === 'post-add.article' || $state.current.name === 'post-edit.article.general'"
										ng-bind-html="formData.content ? (formData.content | lpArticleContent) 
										: (formInfo.lp_desc ? formInfo.lp_desc : '[Content]') | markupHTMLTags">
								</div>
								<div class="profileNewCoverBottomLink">
									<ul>
										<li class="" ng-repeat="tag in tags.tags">
											<a href="#"># {{tag.value}} </a>
										</li>
										<li class="" ng-hide="tags.getNumberOfTags() > 0">
											<a href="#"> # [tags] </a>
										</li>
									</ul>
								</div>
							</div>
							<div class="userStatusRow smUserStatus" ng-class="{'noBio': user.about_me=='' || user.about_me==null}">
								<div class="userStatusImage" ng-if="user.profile_image">
                                    <a ng-href="/profile/{{::user.username}}" style="background:url({{user.profile_image}}) no-repeat;"></a>
                                </div>
                                <div ng-if="::(!user.profile_image)" class="userStatusImage {{::user.user_color}}">
                                    <a ng-href="/profile/{{::user.username}}">
                                        <span class="txt">{{::user.first_name.charAt(0)}}</span>
                                    </a>
                                </div>
								<div class="userStatusInfo">
									<span class="userStatusInfoTtl clearfix">
										<a href="javascript:void(0);">{{user.first_name + " " + user.last_name}}</a>
									</span>
									<div class="cardFollow clearfix">
										<small>
											<time>Shared this post 0 seconds ago.</time>
										</small>
									</div>	
									<div class="userStatusInfo info" ng-show="user.about_me">
										<p class="userAbout"> 
											<span class="clearfix">
												<small class="ng-binding">{{user.about_me}}</small>
											</span>
										</p>
									</div>
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






<!-- SHARE Modal -->
<sharepost-card></sharepost-card>
<!-- Details Modal-->
<postcard-modal></postcard-modal>
<!-- DELETE POST CARD MODAL -->

<delete-post-modal></delete-post-modal>

<!-- REPORT POST CARD MODAL -->
<report-post-modal></report-post-modal>