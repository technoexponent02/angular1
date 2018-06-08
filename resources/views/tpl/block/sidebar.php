<!-- BEGIN SIDE BAR PANEL-->
<nav class="page-sidebar" data-pages="sidebar" pg-sidebar>
    <!-- BEGIN SIDEBAR MENU TOP TRAY CONTENT-->
    <div class="sidebar-overlay-slide from-top" id="appMenu">
        <div class="row">
            <div class="col-xs-6 no-padding">
                <a href="" class="p-l-40"><img src="assets/img/demo/social_app.svg" alt="socail">
                </a>
            </div>
            <div class="col-xs-6 no-padding">
                <a href="" class="p-l-10"><img src="assets/img/demo/email_app.svg" alt="socail">
                </a>
            </div>
        </div>
        <div class="row">
            <div class="col-xs-6 m-t-20 no-padding">
                <a href="" class="p-l-40"><img src="assets/img/demo/calendar_app.svg" alt="socail">
                </a>
            </div>
            <div class="col-xs-6 m-t-20 no-padding">
                <a href="" class="p-l-10"><img src="assets/img/demo/add_more.svg" alt="socail">
                </a>
            </div>
        </div>
    </div>
    <!-- END SIDEBAR MENU TOP TRAY CONTENT-->
    <!-- BEGIN SIDEBAR MENU HEADER-->
	<div class="sidebarInner">
		<div class="sidebarInnerCont">
			<div class="sidebar-header">
				<!-- <a ui-sref="feed" ng-click="sillyQA()">
					<img src="assets/img/logo_white.png" alt="logo" class="brand" data-src="assets/img/logo_white.png"
					 ui-jq="unveil" data-src-retina="assets/img/logo_white.png" width="" height="26">
				</a> -->
				<!-- <div class="sidebar-header-controls"> -->
					<!-- <button type="button" class="btn btn-xs sidebar-slide-toggle btn-link m-l-20" data-pages-toggle="#appMenu">
						<i class="fa fa-angle-down fs-16"></i>
					</button> -->
					<!-- <button type="button" class="btn btn-link visible-lg-inline" data-toggle-pin="sidebar" style="margin-left:42px;"><i
							class="fa fs-12"></i>
					</button>
				</div>-->
				<a  ui-sref="profile" class="sidebarUser">
					<span class="thumbnail-wrapper d32 circular inline" ng-if="user.profile_image" style="background:url({{user.profile_image}}) no-repeat;"></span>
					<span class="thumbnail-wrapper d32 circular inline {{user.user_color}}" ng-if="!user.profile_image">
						<span class="txt">{{user.first_name.charAt(0)}}</span>
					</span>
					<span class="nm">{{user.first_name}}</span>
				</a>
			</div>
			<!-- END SIDEBAR MENU HEADER-->
			<!-- START SIDEBAR MENU -->
			<div class="sidebar-menu">			
				<div class="invtBtn m-t-30">
					<a class="btn btn-success btn-sm" ng-click="instPin=true ; location=true ; checkUrl()"><i class="fa fa-fw fa-plus"></i> New Post</a>
				</div>
				<!-- BEGIN SIDEBAR MENU ITEMS-->
				<ul class="menu-items">
					<li class="m-t-10" ui-sref-active="active" id="myFeed">
						<a ui-sref="feed" class="detailed">
							<span class="title">My Feed</span>
							<!-- <span class="details">12 New Updates</span> -->
						</a>
						<span class="icon-thumbnail"><i class="pg-home"></i></span>
					</li>
					<li class="" ui-sref-active="active">
						<a ui-sref="nearby" class="detailed">
							<span class="title">Nearby&nbsp;&nbsp;<span class="badge badge-important"></span></span>
						</a>
						<span class="icon-thumbnail"><i class="fa fa-location-arrow" style="font-size:18px;"></i></span>
					</li>
					<li ui-sref-active="active" id="rtretre">
						<a ui-sref="explore" class="detailed">
							<span class="title">Explore</span>
							<!-- <span class="details">12 New Updates</span> -->
						</a>
						<span class="icon-thumbnail"><i class="pg-search"></i></span>
					</li>
					<li class="borderLi"></li>
					<li class="" ui-sref-active="active">
						<a ui-sref="following-topics" class="detailed">
							<span class="title">Followed Topics</span>
						</a>
						<span class="icon-thumbnail"><i class="pg-menu_lv"></i></span>
					</li>				
					<li class="" ui-sref-active="active">
						<a ui-sref="my-analytics" class="detailed">
							<span class="title">My Analytics</span>
							<!-- <span class="details">12 New Updates</span> -->
						</a>
						<span class="icon-thumbnail"><i class="fa fa-pie-chart"></i></span>
					</li>
					<li class="" ui-sref-active="active">
						<a ui-sref="saved-post" class="detailed">
							<span class="title">Saved Post&nbsp;&nbsp;<span class="badge badge-important">{{user.totalBookMarks}}</span></span>
							<!-- <span class="details">12 New Updates</span> -->
						</a>
						<span class="icon-thumbnail"><i class="fa fa-bookmark"></i></span>
					</li>
					<li ng-class="{'open active':includes('profile')}">
						<a ui-sref="profile" class="detailed">
							<span class="title">Profile</span>
							<!-- <span class="details">12 New Updates</span> -->
						</a>
						<span class="icon-thumbnail"><i class="fa fa-user"></i></span>
					</li>
					<li class="borderLi"></li>					
					<li ui-sref-active="active">
						<a ui-sref="invite-friend" class="detailed">
							<span class="title">Invite Friends</span>
						</a>
						<span class="icon-thumbnail"><i class="fa fa-user-plus"></i></span>
					</li>
					<li ui-sref-active="active">
						<a ui-sref="send-feedback" class="detailed">
							<span class="title">Feedback</span>
						</a>
						<span class="icon-thumbnail"><i class="fa fa-comment"></i></span>
					</li>
				</ul>
				<div class="clearfix"></div>
			</div>
			<div class="sendFeedbackNav nw">
				Copyright &copy; 2017 Swolk.
			</div>
		</div>
	</div>
    <!-- END SIDEBAR MENU -->		
</nav>
<!-- END SIDEBAR -->
<!-- END SIDEBPANEL-->
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
		
			<a ui-sref="post-add.question" ng-if="checkUrlParam!='questions'" >			
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

<script>
$( document ).ready(function() {
	console.log( $(location).attr('pathname'));
});


</script>
