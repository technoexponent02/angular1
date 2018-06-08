<!-- START HEADER -->
<div class="header">
    <!-- START MOBILE CONTROLS -->
    <div class="container-fluid relative" style="display:none;">
        <!-- LEFT SIDE -->
        <div class="pull-left full-height visible-sm visible-xs">
            <!-- START ACTION BAR -->

            <!-- END ACTION BAR -->
        </div>
        <!-- RIGHT SIDE -->
        <div class="pull-right full-height visible-sm visible-xs">
            <!-- START ACTION BAR -->
            <div class="header-inner">
                <a ng-if="includes('app.layouts.horizontal')" href="#" class="btn-link visible-xs-inline-block visible-sm-inline-block m-r-10" pg-horizontal-menu-toggle>
                    <span class="pg pg-arrow_minimize"></span>
                </a>
                <a href="javascript:void(0);" class="btn-link visible-sm-inline-block visible-xs-inline-block" data-toggle="quickview" data-toggle-element="#quickview" id="qckVw">
                    <span class="icon-set menu-hambuger-plus"></span>
                </a>
            </div>
            <!-- END ACTION BAR -->
        </div>
    </div>
    <!-- END MOBILE CONTROLS -->
	<div class="customHeader clearfix">
		<div class="leaderLeftBar">
			<a href="javascript:void(0);" id="smNavbarClick" class="btn-link toggle-sidebar visible-sm-inline-block visible-xs-inline-block padding-5" ng-click="mySmNavClk()">
                <img src="assets/pages/img/menu.png" alt=""/>
            </a>			
            <a class="search-link" ng-click="showSearchOverlay()"><i class="pg-search"></i>Type anywhere to <span class="bold">search</span></a>
			<ul class="notification-list">
                <li class="p-r-15 inline">
                    <div class="dropdown notificationDropdown">
                        <a id="notification-center" class="icon-set globe-fill" ng-class="{'sl':total_notifications > 0}" data-toggle="dropdown"
                        	ng-click="notificationPopUpOpen()">
                            <span ng-if="total_notifications <= 0"></span>
                            <span ng-if="total_notifications > 0">{{total_notifications}}</span>
                        </a>
                        <!-- START Notification Dropdown -->
                        <div class="dropdown-menu notification-toggle" role="menu" aria-labelledby="notification-center" pg-notification-center="">
							<div class="notification-panel">
								<div class="notification-header text-center">
									Notification
								</div>
								<div class="notification-body scrollable needsclick"
									id="notifiBody"
									ng-init="notifiScrollLock()">
										<div infinite-scroll="loadMoreNotification('notification')"
										infinite-scroll-container="'#notifiBody'"
										infinite-scroll-parent
										>
											<!-- One item -->
											<div class="notification-item notifiType-{{notification.activity_id}} clearfix"
												ng-repeat="notification in notifications | orderBy:'-created_at'"
												ng-init="message=getNotificationMsg(notification)"
												id="notifi-{{$index}}"
												ng-class="{'notification-read': notification.status==3}"
												>
												<div class="heading"
													 ng-if="notification.type != 'follow' && $state.current.name == 'post-details'"
                                                     data-toggle="modal"
													 data-target="#myModal{{::notification.post.parent_post_id }}"
													 ng-click="markNotificationRead(notification,'all',$index);openPostPage(notification.post)"
													>
													<a class="notifi-text pull-left">
														<span class="icon">
															<img ng-src="assets/img/notification/activity-{{::notification.activity_id}}.png" alt="activity-image"/>
														</span>
														<span class="bold" ng-bind-html="::message"></span>
													</a>
													<div class="pull-right">
														<span class="time" ng-if="::showElapsedTime(notification.created_at)">
															<time am-time-ago="notification.created_at | amUtc | amLocal"></time>

														</span>
														<span class="time" ng-if="::(!showElapsedTime(notification.created_at))">
															<time>{{::(notification.created_at | amDateFormat:'DD MMM, YYYY - HH:mm')}}</time>
														</span>
													</div>
												</div>
												<div class="heading"
													ng-if="notification.type != 'follow' && $state.current.name != 'post-details'"
													postcard="showPostDetails(notification.post.parent_post_id,notification.post.child_post_id,3,0,notification.comment_id)"
													data-toggle="modal" 
													data-target="#myModal{{::notification.post.parent_post_id}}"
													ng-click="markNotificationRead(notification,'all',$index)"
													>
													<a class="notifi-text pull-left">
														<span class="icon">
															<img ng-src="assets/img/notification/activity-{{::notification.activity_id}}.png" alt="activity-image"/>
														</span>
														<span class="bold" ng-bind-html="::message"></span>
													</a>
													<div class="pull-right">
														<span class="time" ng-if="::showElapsedTime(notification.created_at)">
															<time am-time-ago="notification.created_at | amUtc | amLocal"></time>

														</span>
														<span class="time" ng-if="::(!showElapsedTime(notification.created_at))">
															<time>{{::(notification.created_at | amDateFormat:'DD MMM, YYYY - HH:mm')}}</time>
														</span>
													</div>
												</div>
												<div class="heading follow-row"
													ng-if="notification.type == 'follow'"
													ng-click="openProfileFollow('notification', $index)"
													>
													<a class="notifi-text pull-left">
														<span class="icon">
															<img ng-src="assets/img/notification/activity-{{::notification.activity_id}}.png" alt="activity-image"/>
														</span>
														<span class="bold" ng-bind-html="::message"></span>
													</a>
													<div class="pull-right">
														<span class="time" ng-if="::showElapsedTime(notification.created_at)">
															<time am-time-ago="notification.created_at | amUtc | amLocal"></time>

														</span>
														<span class="time" ng-if="::(!showElapsedTime(notification.created_at))">
															<time>{{::(notification.created_at | amDateFormat:'DD MMM, YYYY - HH:mm')}}</time>
														</span>
													</div>
												</div>
												<div class="option" data-toggle="tooltip" data-placement="left" title="mark as read">
													<a class="mark" ng-click="markNotificationRead(notification,'single_ac')"></a>
												</div> 
											</div>
											<!-- One item -->
										</div>
									<div id="notificationLoader" ng-show="loadNotifiBusy"></div>
								</div>
								<div class="notification-footer text-center">
									<a href="/all-notification">Read all notifications</a>
									<span class="no-notification" ng-if="notifications.length == 0">No notifications</span>
								</div>
							</div>
						</div>
                        <!-- END Notification Dropdown -->
                    </div>
                </li>
            </ul>
            <!-- END NOTIFICATIONS LIST -->
		</div>
		<div class="headerMiddle">
			<div class="brand inline">
                <a ui-sref="feed" ng-click="sillyQA()">
                    <img src="assets/img/logo.png" alt="logo" data-src="assets/img/logo.png" ui-jq="unveil"
                         data-src-retina="/assets/img/logo_2x.png" width="" height="26">
                </a>
            </div>
		</div>
		<div class="dropdown pull-right userDropdown">
			<span class="logUserName">{{user.first_name}}</span>
			<button class="profile-dropdown-toggle" type="button" data-toggle="dropdown" aria-haspopup="true" aria-expanded="false">
				<span class="thumbnail-wrapper d32 circular inline" ng-if="user.profile_image" style="background:url({{user.profile_image}}) no-repeat;"></span>
				<span class="thumbnail-wrapper d32 circular inline {{user.user_color}}" ng-if="!user.profile_image">
					<span class="txt">{{user.first_name.charAt(0)}}</span>
				</span>

			</button>
			<ul class="dropdown-menu profile-dropdown" role="menu">
				<li>
					<div class="profileDropdownCont">
						<div class="widget rightWidget">
							<div class="widget-advanced">
								<div class="widget-header text-center profileRgtCover" style="background:url({{user.cover_image}}) no-repeat;">
																
								</div>
								<div class="widget-main">									
									<a class="widget-image-container animation-hatch" href="#" ng-if="user.profile_image" style="background:url({{user.profile_image}}) no-repeat;"></a>
									<a class="widget-image-container animation-hatch {{user.user_color}}" href="#" ng-if="!user.profile_image">
										<span class="txt">{{user.first_name.charAt(0)}}</span>
									</a>
									<h3 class="widget-content widget-content-image widget-content-light profileName">
										{{ user.first_name + ' ' + user.last_name }}<br>
										<small ng-if="user.occupation">{{ user.occupation }}</small>
									</h3>
									<div class="row text-center animation-fadeIn">
										<div class="col-xs-4">
											<h5>
												<strong>{{user.userProfileview}}</strong><br/>
												<small>Views</small>
											</h5>
										</div>
										<div class="col-xs-4">
											<h5>
												<strong>{{user.points>=1 ? user.points : 0 | thousandSuffix}}</strong><br/>
												<small>Points</small>
											</h5>
										</div>
										<div class="col-xs-4">
											<h5>
												<strong>{{userFollower.length | thousandSuffix }}</strong><br/>
												<small>Followers</small>
											</h5>
										</div>
									</div>
								</div>
							</div>
						</div>
					</div>
					<div class="profileDropdownFooter clearfix">
						<div class="colM">
							<a href="/edit-profile"><i class="pg-settings_small"></i> Edit Profile</a>
						</div>
						<div class="colM">
							<a ng-click="logout()" class="clearfix"><i class="pg-power"></i> Logout</a>
						</div>
					</div>
				</li>
			</ul>
		</div>
	</div>
</div>
<!-- END HEADER -->


<!-- Footer Menu (Mobile)-->
<div class="mobilefooterNav">
	<div class="list">
		<a ui-sref="feed" ui-sref-active="sl" class="mhome">
			<span class="ico"></span>
		</a>
	</div>
	<div class="list">
		<a ui-sref="explore" ui-sref-active="sl" class="mexplore">
			<span class="ico"></span>
		</a>
	</div>
	<div class="list"></div>
	<div class="list">
		<a ui-sref="following-topics" ui-sref-active="sl" class="mtopics">
			<span class="ico"></span>
		</a>
	</div>
	<div class="list">
		<a ui-sref="profile" ui-sref-active="sl" class="mprofile">
			<span class="ico"></span>
		</a>
	</div>
	<div class="spacer"></div>
</div>
<!-- Floating pin -->
<a id="instantPin" ng-click="instPin=true ; checkUrl()">
    <img src="assets/pages/img/pencil.png" alt="Pin"/>
</a>
<div class="instantPinContainer" ng-class="{show: instPin}" ng-click="instPin=false">
    <div class="contArea">
        <div class="cont">
            <a ui-sref="post-add.status">
                <img src="assets/pages/img/livejournal-fill.png" alt=""/>
                <span class="tl">Status</span>
            </a>
            <a ui-sref="post-add.photo">
                <img src="assets/pages/img/camera2-fill.png" alt=""/>
                <span class="tl">Photo</span>
            </a>
            <a ui-sref="post-add.video">
                <img src="assets/pages/img/camera1-fill.png" alt=""/>
                <span class="tl">Video</span>
            </a>
            <a ui-sref="post-add.link">
                <img src="assets/pages/img/link2-fill.png" alt=""/>
                <span class="tl">Link</span>
            </a>
            <a ui-sref="post-add.article">
                <img src="assets/pages/img/text-file-fill.png" alt=""/>
                <span class="tl">Article</span>
            </a>
			<a ui-sref="post-add.question" ng-if="checkUrlParam!='questions'">
                <img src="assets/pages/img/question-mark.png" alt=""/>
                <span class="tl">Question</span>
            </a>
			<div ng-if="checkUrlParam =='questions'" class="botTxt">Answering to "{{urlParamText}}?"</div>
			<div ng-if="checkUrlParam =='tag'" class="botTxt">Share post about "{{urlParamText}}"</div>
			<div ng-if="checkUrlParam =='place'" class="botTxt">Share post about "{{urlParamText}}"</div>
        </div>
    </div>
	<div class="contArea forMobile">
        <div class="cont">
            <a ui-sref="post-add.photo">
                <img src="assets/pages/img/camera2-fill.png" alt=""/>
                <span class="tl">Photo</span>
            </a>
            <a ui-sref="post-add.video">
                <img src="assets/pages/img/camera1-fill.png" alt=""/>
                <span class="tl">Video</span>
            </a><br/>
            <a ui-sref="post-add.link">
                <img src="assets/pages/img/link2-fill.png" alt=""/>
                <span class="tl">Link</span>
            </a>
            <a ui-sref="post-add.article">
                <img src="assets/pages/img/text-file-fill.png" alt=""/>
                <span class="tl">Article</span>
            </a><br/>
            <a ui-sref="post-add.status">
                <img src="assets/pages/img/livejournal-fill.png" alt=""/>
                <span class="tl">Status</span>
            </a>
			<a ui-sref="post-add.question" ng-if="checkUrlParam!='questions'">
                <img src="assets/pages/img/question-mark.png" alt=""/>
                <span class="tl">Question</span>
            </a>
			<div ng-if="checkUrlParam =='questions'" class="botTxt">Answering to "{{urlParamText}}?"</div>
			<div ng-if="checkUrlParam =='tag'" class="botTxt">Share post about "{{urlParamText}}"</div>
			<div ng-if="checkUrlParam =='place'" class="botTxt">Share post about "{{urlParamText}}"</div>
        </div>
    </div>
</div>