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
			<!-- <div class="sidebar-header">
				<a ui-sref="feed">
					<img src="assets/img/logo_white.png" alt="logo" class="brand" data-src="assets/img/logo_white.png"
					 ui-jq="unveil" data-src-retina="assets/img/logo_white.png" width="" height="26">
				</a>
				<div class="sidebar-header-controls">
					<button type="button" class="btn btn-link visible-lg-inline" data-toggle-pin="sidebar" style="margin-left:42px;"><i
							class="fa fs-12"></i>
					</button>
				</div>
			</div>-->
			
			<!-- END SIDEBAR MENU HEADER-->
			<!-- START SIDEBAR MENU -->
			<div class="sidebar-menu disableSidebar">
				<span class="leftbarReq">You are required to 
				<a ng-click="redirecToLogin();">sign in</a> to access these pages</span>
				<div class="invtBtn m-t-20">
					<span class="btn btn-success btn-sm" style="cursor:default;"><i class="fa fa-fw fa-plus"></i> New Post</span>
				</div>
				<!-- BEGIN SIDEBAR MENU ITEMS-->
				<ul class="menu-items">
					<li ui-sref-active="active" id="myFeed">
						<div class="disA">
							<span class="title">My Feed</span>
							<!-- <span class="details">12 New Updates</span> -->
						</div>
						<span class="icon-thumbnail"><i class="pg-home"></i></span>
					</li>
					<li class="" ui-sref-active="active">
						<div class="disA">
							<span class="title">Nearby&nbsp;&nbsp;<span class="badge badge-important"></span></span>
							<!-- <span class="details">12 New Updates</span> -->
						</div>
						<span class="icon-thumbnail"><i class="fa fa-location-arrow" style="font-size:18px;"></i></span>
					</li>
					<li ui-sref-active="active" id="rtretre">
						<div class="disA">
							<span class="title">Explore</span>
							<!-- <span class="details">12 New Updates</span> -->
						</div>
						<span class="icon-thumbnail"><i class="pg-search"></i></span>
					</li>
					<li class="borderLi"></li>
					<li class="" ui-sref-active="active">
						<div class="disA">
							<span class="title">Followed Topics</span>
						</div>
						<span class="icon-thumbnail"><i class="pg-menu_lv"></i></span>
					</li>
					<li class="" ui-sref-active="active">
						<div class="disA">
							<span class="title">My Analytics</span>
							<!-- <span class="details">12 New Updates</span> -->
						</div>
						<span class="icon-thumbnail"><i class="fa fa-pie-chart"></i></span>
					</li>
					<li class="" ui-sref-active="active">
						<div class="disA">
							<span class="title">Saved Post&nbsp;&nbsp;<span class="badge badge-important">{{user.totalBookMarks}}</span></span>
							<!-- <span class="details">12 New Updates</span> -->
						</div>
						<span class="icon-thumbnail"><i class="fa fa-bookmark"></i></span>
					</li>
					<li ng-class="{'open active':includes('profile')}">
						<div class="disA">
							<span class="title">Profile</span>
							<!-- <span class="details">12 New Updates</span> -->
						</div>
						<span class="icon-thumbnail"><i class="fa fa-user"></i></span>
					</li>
					<li class="borderLi"></li>
					<li ui-sref-active="active">
						<div class="disA">
							<span class="title">Invite Friends</span>
						</div>
						<span class="icon-thumbnail"><i class="fa fa-user-plus"></i></span>
					</li>
					<li ui-sref-active="active">
						<div class="disA">
							<span class="title">Feedback</span>
						</div>
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
