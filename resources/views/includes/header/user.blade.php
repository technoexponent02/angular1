<div class="header ">
    <div class="container-fluid relative" style="display:none;">
        <div class="pull-left full-height visible-sm visible-xs">
            <div class="header-inner"></div>
        </div>
        <div class="pull-center hidden-md hidden-lg">
            <div class="header-inner">
                <div class="brand inline">
                    <img src="{{asset('assets/img/logo.png')}}" alt="logo" data-src="{{asset('assets/img/logo.png')}}" data-src-retina="{{asset('assets/img/logo_2x.png')}}" width="78" height="22">
                </div>
            </div>
        </div>
        <div class="pull-right full-height visible-sm visible-xs">
            <div class="header-inner">
                <a href="#" class="btn-link visible-sm-inline-block visible-xs-inline-block" data-toggle="quickview" data-toggle-element="#quickview">
                    <span class="icon-set menu-hambuger-plus"></span>
                </a>
            </div>
        </div>
    </div>
    <div class=" pull-left xs-table">
        <div class="header-inner headrLeft">
            <a href="#" class="btn-link toggle-sidebar visible-sm-inline-block visible-xs-inline-block padding-5 leftNavBtn" data-toggle="sidebar">
                <span class="icon-set menu-hambuger"></span>
            </a>
            <div class="brand inline">
                <img src="{{asset('assets/img/logo.png')}}" alt="logo" data-src="{{asset('assets/img/logo.png')}}" data-src-retina="{{asset('assets/img/logo_2x.png')}}" width="78" height="22">
            </div>
        </div>
    </div>

    <div class=" pull-right">
        <div class="userNav">
            <div class="pull-left p-r-10 p-t-10 fs-16 font-heading">
                <span class="semi-bold">{{ Auth::user()->first_name }}</span>
            </div>
            <div class="dropdown pull-right">
                <button class="profile-dropdown-toggle" type="button" data-toggle="dropdown" aria-haspopup="true" aria-expanded="false">
                <span class="thumbnail-wrapper d32 circular inline m-t-5">
                    <img src="{{ profileImage(Auth::user()->profile_image) }}" alt="" data-src="{{ profileImage(Auth::user()->profile_image) }}" width="32" height="32">
                </span>
                </button>
                <ul class="dropdown-menu profile-dropdown" role="menu">
                    <li>
                        <a href="{{url('account/profile/edit')}}">
                            <i class="pg-settings_small"></i> Settings
                        </a>
                    </li>
                    <li>
                        <a href="{!! url('account/dashboard') !!}">
                            <i class="pg-settings_small"></i> Dashboard
                        </a>
                    </li>
                    <li class="bg-master-lighter">
                        <a href="{{url('logout')}}" class="clearfix">
                            <span class="pull-left">Logout</span>
                            <span class="pull-right"><i class="pg-power"></i></span>
                        </a>
                    </li>
                </ul>
            </div>
        </div>
    </div>
</div>