<!-- START HEADER -->
<div class="header headerDisable">
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
                <!-- <span class="icon-set menu-hambuger"></span> -->
				<img src="assets/pages/img/menu.png" alt=""/>
            </a>			
            <a class="search-link"  ng-click="redirecToLogin();"><i class="pg-search"></i>Type anywhere to <span class="bold">search</span></a>
            <!-- END NOTIFICATIONS LIST -->
		</div>
		<div class="headerMiddle">
			<div class="brand inline">
                <a ng-click="goToLogin();">
                    <img src="assets/img/logo.png" alt="logo" data-src="assets/img/logo.png" ui-jq="unveil" data-src-retina="assets/img/logo_2x.png" width="" height="26">
                </a>
            </div>
		</div>
		<div class="dropdown pull-right userDropdown">
			<a ng-click="redirecToLogin();" class="btn btn-sm">Sign In</a>
			<div class="signInoverlay" style="display:none;"></div>
			<div class="sign" id="signInNotifi" style="display:none;">
				<style>
				.signInoverlay{
					display:none;
					position:fixed;
					top:0; 
					left:0;
					width:100%; 
					height:100%;
					min-height:100vh;
					z-index:699;
				}
				.form-group-default label{color:#626262;}
				.modal.sign.fade {
					background-color: transparent;
				}
				.loginDropWrap,
				.forgotPassWrap{
					width: 386px;
					position: absolute;
					top: 100%;
					right: 0;
					background: #fff;
					box-shadow: 0 0 8px 0 rgba(0,0,0,.7);
					border: aliceblue;
					text-shadow: none;
					border-radius: 3px;
					font-size: 13px;
					margin: 0;
					min-width: 50px;
					z-index: 700!important;
					padding: 0 15px 15px;
					margin:10px 0 0 0;
				}
				#form-signin .explorecheck p,
				#form-forgot .explorecheck p{
					float: left;
					display: block;
				}
				#form-signin .explorecheck p.newToSwolk,
				#form-forgot .explorecheck p.newToSwolk{
					float: right;
					display: block;
				}
				.loginDropWrap #signInNotifi .nwChk:after,
				.loginDropWrap #signInNotifi .nwChk:before,
				.forgotPassWrap #signInNotifi .nwChk:after,
				.forgotPassWrap #signInNotifi .nwChk:before{
					content:'';
					display: block;
					clear: both;
				}
				#form-signin .explorecheck a, 
				#form-signin .explorecheck p, 
				#form-signin .explorecheck p a,
				#form-forgot .explorecheck a, 
				#form-forgot .explorecheck p, 
				#form-forgot .explorecheck p a{
					font-weight: 500;
					font-size: 13px;
				}
				.loginDropWrap .btn-facebook, 
				.loginDropWrap .btn-twitter {
					color: #fff!important;
					background-color: #3b5998!important;
					border: none!important;
					font-size: 14px!important;
					line-height: 30px!important;
					height: auto!important;
					padding: 4px 16px!important;
					border-radius: 3px!important;
				}
				.loginDropWrap .btn-facebook{margin-bottom:8px !important;}
				.loginDropWrap .btn-twitter {
					background-color: #2ba9e1!important;
					border: none!important;
				}
				.loginDropWrap .btn-facebook:hover{
					color: #fff!important;
					background-color: #30487b!important;
					border-color: rgba(0,0,0,.2)!important;
				}
				.loginDropWrap .btn-twitter:hover{
					color: #fff!important;
					background-color: #1c92c7!important;
					border-color: rgba(0,0,0,.2)!important;
				}
				.loginDropWrap .btn-social .pull-left{
					top: 1px;
				}

				.loginDropWrap:after, 
				.loginDropWrap:before,
				.forgotPassWrap:after, 
				.forgotPassWrap:before {
					bottom: 100%;
					left: 90%;
					border: solid transparent;
					content: " ";
					height: 0;
					width: 0;
					position: absolute;
					pointer-events: none;
				}
				.loginDropWrap:after,
				.forgotPassWrap:after {
					border-color: rgba(255, 255, 255, 0);
					border-bottom-color: #fff;
					border-width: 7px;
					margin-left: -7px;
				}
				.loginDropWrap:before,
				.forgotPassWrap:before {
					border-color: rgba(194, 194, 194, 0);
					border-bottom-color: #c2c2c2;
					border-width: 8px;
					margin-left: -8px;
				}


				#form-signin .signin-error {
					background: #f53a3a;
					color: #fff;
					padding: 10px;
					border-radius: 4px;
					margin-bottom: 10px;
					word-wrap: break-word;
				}

				#form-forgot .forgotPass-error {
					background: #f53a3a;
					color: #fff;
					padding: 10px;
					border-radius: 4px;
					margin-bottom: 10px;
					word-wrap: break-word;
				}

				#form-forgot .forgotPass-success {
					background: #37AB2B;
					color: #fff;
					padding: 10px;
					border-radius: 4px;
					margin-bottom: 10px;
					word-wrap: break-word;
				}
				#signInNotifi .btn {
					display: inline-block;
					padding: 6px 12px;
					margin-bottom: 0;
					font-size: 14px;
					font-weight: 400;
					line-height: 1.42857143;
					text-align: center;
					white-space: nowrap;
					vertical-align: middle;
					-ms-touch-action: manipulation;
					touch-action: manipulation;
					cursor: pointer;
					-webkit-user-select: none;
					-moz-user-select: none;
					-ms-user-select: none;
					user-select: none;
					background-image: none;
					border: 1px solid transparent;
					border-radius: 4px;
				}
				#signInNotifi .btn-primary, 
				#signInNotifi .btn-primary:focus, 
				#signInNotifi .btn-success {
					color: #fff;
					margin-bottom: 0;
					padding-left: 17px;
					padding-right: 17px;
					position: relative;
					text-align: center;
					text-shadow: none;
					transition: color .1s linear 0s,
					background-color .1s linear 0s,opacity .2s linear 0s!important;
					vertical-align: middle;
				}
				#signInNotifi .btn-primary, 
				#signInNotifi .btn-primary:focus {
					background-color: #6d5cae !important;
					border-color: #6d5cae !important;
					color: #fff !important;
				}
				#signInNotifi .btn-primary.hover, 
				#signInNotifi .btn-primary:hover, 
				#signInNotifi .open .dropdown-toggle.btn-primary {
					background-color: #8a7dbe !important;
					border-color: #8a7dbe !important;
					color: #fff !important;
				}
				.checkbox.nwChk a{color:#3a8fc8;}
				.checkbox.nwChk a:hover,
				.checkbox.nwChk a:active,
				.checkbox.nwChk a:focus{color:#48b0f7;}

				@media screen and (max-width: 1001px) {
					.loginDropWrap,
					.forgotPassWrap{
						right: 20px;
					}
				}
				@media screen and (max-width: 991px) {
					.loginDropWrap,
					.forgotPassWrap{
						right: 10px;
					}
				}
				@media screen and (max-width: 500px) {
					.loginDropWrap,
					.forgotPassWrap{
						width: 100%;
						right: 0;
					}
				}
				@media screen and (max-width: 320px) {
					.loginDropWrap:after, 
					.loginDropWrap:before,
					.forgotPassWrap:after, 
					.forgotPassWrap:before{
						left: 84%;
					}
				}

				</style>

				<!-- login form -->
					
				<!-- <div class="loginDropWrap">
					<form class="p-t-15" action="<?php echo url('login') ?>" role="form" method="POST" id="form-signin">
					
						<div class=" validate">
							<div class="form-group form-group-default <?php echo  $errors->has('email') ? ' has-error' : '' ?>">
								<label>USERNAME / E-MAIL </label>
								<div class="controls">
									<input type="text" name="email" placeholder="Username / E-mail" class="form-control" value="">
								</div>
							</div>
						</div>
						<div class="">
							<div class="form-group form-group-default <?php echo  $errors->has('password') ? ' has-error' : '' ?> ">
								<label>Password</label>
								<div class="controls">
									<input type="password" class="form-control" name="password" placeholder="Password">
								</div>
							</div>
						</div>
						
						<div class="row">
							<div class="col-md-12">
								<div class="checkbox nwChk explorecheck">
									<p><a href="javascript:void(0);" onClick="forgotcome();" id="backtoforgot">Forgot Password?</a></p>
									<p class="newToSwolk">New to Swolk ? <a href="" onClick="location.href='<?php echo url('signup') ?>'" class="signupScroll">Sign Up here</a></p>
									<div class="spacer"></div>
								</div>
							</div>
						</div>

						<div class="row">
							<div class="col-md-12">
								<button class="btn btn-primary btnLoading btn-cons m-t-10  btn-loading" type="submit" name="btnSubmit" value="submit" style="margin-bottom:10px !important;">Sign in</button>
							</div>
						</div>

						<div class="row m-t-10">
							<div class="col-md-12">
								<a class="btn btn-block btn-social btn-facebook" onClick="location.href='<?php echo url('auth/facebook/bG9naW5fcmVnaXN0ZXI=') ?>'" >
									<span class="pull-left"><i class="fa fa-facebook"></i></span>
									<span class="bold">Log in with Facebook</span>
								</a>
							</div>
							<div class="col-md-12">
								<a class="btn btn-block  btn-social btn-twitter" onClick="location.href='<?php echo url('auth/twitter/b25seV9sb2dpbg===') ?>'" >
									<span class="pull-left"><i class="fa fa-twitter"></i></span>
									<span class="bold">Log in with Twitter</span>
								</a>
							</div>
						</div>
					</form>
				</div>

				<div class="forgotPassWrap" style="display:none;">
					<form class="p-t-15"role="form" method="POST" id="form-forgot" action="<?php echo url('password/forgot') ?>">
						<div class="validate">
							<div class="form-group form-group-default">
								<label>E-MAIL </label>
								<div class="controls">
									<input type="email" name="email" placeholder="E-mail" class="form-control" value="">
								</div>
							</div>
						</div>
						
						<div class="row">
							<div class="col-md-12">
								<div class="checkbox nwChk explorecheck">
									<p><a href="javascript:void(0);" onClick="signInCome()" id="back-signin">Signin</a></p>
									<div class="spacer"></div>
								</div>
							</div>
						</div>

						<div class="row">
							<div class="col-md-12">
								<button class="btn btn-primary btnLoading btn-cons m-t-10  btn-loading" type="submit" name="btnSubmit" value="submit" style="margin-bottom:10px !important;">Submit</button>
							</div>
						</div>

					</form>
				</div> -->




				<!-- via http call -->
				<div class="loginDropWrap" ng-controller="SignInCtrl">

					<!-- <form class="p-t-15" action="<?php echo url('login') ?>" method="POST" role="form" id="form-signin" ng-submit="loginPopSubmit()"> -->
					<form class="p-t-15" role="form" id="form-signin" ng-submit="loginPopSubmit()">

						<!-- <pre>{{loginPopUpUserData | json}}</pre>
						<pre>{{popupsigninH | json}}</pre> -->

						<!-- <div id="signsuccessbox">
							{{signinsuccess}}
						</div> -->

						<div id="signerrorbox" ng-class="signinerrormsgST==true ? 'signin-error' : ''">
							{{signinerrormsg}}
						</div>


						<div class=" validate">
							<div class="form-group form-group-default">
								<label>USERNAME / E-MAIL </label>
								<div class="controls">
									<input type="text" name="email" placeholder="Username / E-mail" class="form-control" ng-model="popupsigninH.email">
								</div>
							</div>
						</div>
						<div class="">
							<div class="form-group form-group-default">
								<label>Password</label>
								<div class="controls">
									<input type="password" class="form-control" name="password" placeholder="Password" ng-model="popupsigninH.password">
								</div>
							</div>
						</div>
						
						<div class="row">
							<div class="col-md-12">
								<div class="checkbox nwChk explorecheck">
									<p><a href="javascript:void(0);" onClick="forgotcome();" id="backtoforgot">Forgot Password?</a></p>
									<p class="newToSwolk">New to Swolk ? <a href="" onClick="location.href='<?php echo url('signup') ?>'" class="signupScroll">Sign Up here</a></p>
									<div class="spacer"></div>
								</div>
							</div>
						</div>

						<div class="row">
							<div class="col-md-12">
								<button class="btn btn-primary btnLoading btn-cons m-t-10" id="signSubmit" type="submit" name="btnSubmit" value="submit" style="margin-bottom:10px !important;">Sign in</button>
							</div>
						</div>

						<div class="row m-t-10">
							<div class="col-md-12">
								<a class="btn btn-block btn-social btn-facebook" onClick="location.href='<?php echo url('auth/facebook/bG9naW5fcmVnaXN0ZXI=') ?>'" >
									<span class="pull-left"><i class="fa fa-facebook"></i></span>
									<span class="bold">Log in with Facebook</span>
								</a>
							</div>
							<div class="col-md-12">
								<a class="btn btn-block  btn-social btn-twitter" onClick="location.href='<?php echo url('auth/twitter/b25seV9sb2dpbg===') ?>'" >
									<span class="pull-left"><i class="fa fa-twitter"></i></span>
									<span class="bold">Log in with Twitter</span>
								</a>
							</div>
						</div>
					</form>


					<form class="p-t-15" action="<?php echo url('login') ?>" method="POST" role="form" id="form-signin-h" style="display:none;">
						<input type="text" id="signin-email" name="email" placeholder="Username / E-mail" class="form-control" ng-model="popupsigninH.email">
						<input type="password" id="signin-pass" class="form-control" name="password" placeholder="Password" ng-model="popupsigninH.password">
					</form>


				</div>
				<div class="forgotPassWrap" ng-controller="ForgotPasswordCtrl" style="display:none;">

					<form class="p-t-15"role="form" method="POST" id="form-forgot"  ng-submit="forgotPopupSubmit()">
						<!-- <pre>{{ forgotpasserrormsgST |json }}</pre> -->
						<div id="" ng-class="forgotpasserrormsgST==true ? 'forgotPass-error' :'' ">
							{{forgotpasserrormsg}}
						</div>
						<div id="" ng-class="forgotpasssuccmsgST==true ? 'forgotPass-success' :'' ">
							{{forgotpasssuccmsg}}
						</div>    


						<div class="validate">
							<div class="form-group form-group-default">
								<label>E-MAIL </label>
								<div class="controls">
									<input type="email" name="email" placeholder="E-mail" class="form-control" value="" ng-model="forgotEmail" >
								</div>
							</div>
						</div>
						
						<div class="row">
							<div class="col-md-12">
								<div class="checkbox nwChk explorecheck">
									<p><a href="javascript:void(0);" onClick="signInCome()" id="back-signin">Signin</a></p>
									<div class="spacer"></div>
								</div>
							</div>
						</div>

						<div class="row">
							<div class="col-md-12">
								<button class="btn btn-primary btnLoading btn-cons m-t-10  btn-loading" type="submit" name="btnSubmit" value="submit" style="margin-bottom:10px !important;" id="forgotPassSubmit">Submit</button>
							</div>
						</div>

					</form>
					
				</div>
				<!-- via http call -->

			</div>
		</div>
	</div>
</div>
<!-- END HEADER -->
<!-- Floating pin -->
<a id="instantPin" ng-click="redirecToLogin();">
    <img src="assets/pages/img/pencil.png" alt="Pin"/>
</a>