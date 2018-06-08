<nav class="page-sidebar" data-pages="sidebar">
    <!-- BEGIN SIDEBAR MENU TOP TRAY CONTENT-->
    <div class="sidebar-overlay-slide from-top" id="appMenu">
        <div class="row">
            <div class="col-xs-6 no-padding">
                <a href="#" class="p-l-40">
                <img src="{{asset('assets/img/demo/social_app.svg')}}" alt="socail">
                </a>
            </div>
            <div class="col-xs-6 no-padding">
                <a href="#" class="p-l-10">
                <img src="{{asset('assets/img/demo/email_app.svg')}}" alt="socail">
                </a>
            </div>
        </div>
        <div class="row">
            <div class="col-xs-6 m-t-20 no-padding">
                <a href="#" class="p-l-40">
                <img src="{{asset('assets/img/demo/calendar_app.svg')}}" alt="socail">
                </a>
            </div>
            <div class="col-xs-6 m-t-20 no-padding">
                <a href="#" class="p-l-10">
                <img src="{{asset('assets/img/demo/add_more.svg')}}" alt="socail">
                </a>
            </div>
        </div>
    </div>
    <!-- END SIDEBAR MENU TOP TRAY CONTENT-->
    <!-- BEGIN SIDEBAR MENU HEADER-->
    <div class="sidebar-header">
        <img src="{{asset('assets/img/logo_white.png')}}" alt="logo" class="brand" data-src="{{asset('assets/img/logo_white.png')}}" data-src-retina="{{asset('assets/img/logo_white_2x.png')}}" width="78" height="22">
        <div class="sidebar-header-controls">
            <button type="button" class="btn btn-xs sidebar-slide-toggle btn-link m-l-20" data-pages-toggle="#appMenu"><i class="fa fa-angle-down fs-16"></i>
            </button>
            <button type="button" class="btn btn-link visible-lg-inline" data-toggle-pin="sidebar"><i class="fa fs-12"></i>
            </button>
        </div>
    </div>
    <!-- END SIDEBAR MENU HEADER-->
    <!-- START SIDEBAR MENU -->
    <div class="sidebar-menu">
        <!-- BEGIN SIDEBAR MENU ITEMS-->
        <ul class="menu-items">
            <li class="m-t-30 ">
                <a href="{!! url('account/profile') !!}" class="detailed">
                    <span class="title">My Feed</span>
                    <!-- <span class="details">12 New Updates</span> -->
                </a>
                <span class="bg-success icon-thumbnail"><i class="pg-home"></i></span>
            </li>
            <li>
                <a href="javascript:void(0);"><span class="title">Discover</span>
                    <span class=" arrow"></span></a>
                <span class="icon-thumbnail">D</span>
                <ul class="sub-menu">
                    <li class="">
                        <a href="javascript:void(0);">Featured</a>
                        <span class="icon-thumbnail">F</span>
                    </li>
                    <li class="">
                        <a href="javascript:void(0);">Popular</a>
                        <span class="icon-thumbnail">P</span>
                    </li>
                    <li class="">
                        <a href="javascript:void(0);">Trending</a>
                        <span class="icon-thumbnail">T</span>
                    </li>
                    <li class="">
                        <a href="javascript:void(0);">Recent</a>
                        <span class="icon-thumbnail">R</span>
                    </li>
                    <li class="">
                        <a href="javascript:void(0);">Top Channels</a>
                        <span class="icon-thumbnail">T</span>
                    </li>                     
                </ul>
            </li>
            <li>
                <a href="javascript:void(0);"><span class="title">Categories</span></a>
                <span class="icon-thumbnail">C</span>
            </li>
            <li>
                <a href="javascript:void(0);"><span class="title">Profile</span>
                    <span class=" arrow"></span></a>
                <span class="icon-thumbnail">P</span>
                <ul class="sub-menu">
                    <li class="">
                        <a href="javascript:void(0);">Activities</a>
                        <span class="icon-thumbnail">A</span>
                    </li>
                    <li class="">
                        <a href="javascript:void(0);">Followers</a>
                        <span class="icon-thumbnail">F</span>
                    </li>
                    <li class="">
                        <a href="javascript:void(0);">Following</a>
                        <span class="icon-thumbnail">FO</span>
                    </li>                     
                </ul>
            </li>
            <li>
                <a href="javascript:void(0);"><span class="title">Saved Media</span></a>
                <span class="icon-thumbnail">S</span>
            </li>
            <li>
                <a href="javascript:void(0);"><span class="title">Collection</span></a>
                <span class="icon-thumbnail">C</span>
            </li>
            <li>
                <a href="javascript:void(0);"><span class="title">Analytics</span></a>
                <span class="icon-thumbnail">A</span>
            </li>
            <li>
                <a href="javascript:void(0);"><span class="title">Log Out</span></a>
                <span class="icon-thumbnail">L</span>
            </li>
            <li>
                <a href="{!! url('user/posting') !!}"><span class="title">Add Post</span></a>
                <span class="icon-thumbnail">A</span>
            </li>
        </ul>
        <div class="clearfix"></div>
    </div>
    <!-- END SIDEBAR MENU -->
</nav>