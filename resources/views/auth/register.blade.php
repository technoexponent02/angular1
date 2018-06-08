@extends('layouts.public')

@section('pageTitle', 'Create your Swolk Account')

@section('content')

<style>
#form-signin .signin-error {
    background: #f53a3a;
    color: #fff;
    padding: 10px;
    border-radius: 4px;
    margin-bottom: 10px;
    word-wrap: break-word;
}
#form-forgot .forgot-error {
    background: #f53a3a;
    color: #fff;
    padding: 10px;
    border-radius: 4px;
    margin-bottom: 10px;
    word-wrap: break-word;
}
#form-forgot .forgot-success {
    background: #37AB2B;
    color: #fff;
    padding: 10px;
    border-radius: 4px;
    margin-bottom: 10px;
    word-wrap: break-word;
}
</style>

<div>

    <div class="header headerDisable" style="background: rgba(255, 255, 255, 0);">
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
                    <!-- ngIf: includes('app.layouts.horizontal') -->
                    <a href="javascript:void(0);" class="btn-link visible-sm-inline-block visible-xs-inline-block" data-toggle="quickview" data-toggle-element="#quickview" id="qckVw">
                        <span class="icon-set menu-hambuger-plus"></span>
                    </a>
                </div>
                <!-- END ACTION BAR -->
            </div>
        </div>
        <!-- END MOBILE CONTROLS -->



        <div class="customHeader clearfix">
            <!-- <div class="leaderLeftBar">
                <a href="javascript:void(0);" id="smNavbarClick" class="btn-link toggle-sidebar visible-sm-inline-block visible-xs-inline-block padding-5" ng-click="mySmNavClk()">
                    <img src="assets/pages/img/menu.png" alt="">
                </a>			
                <a class="search-link" ng-click="redirecToLogin();"><i class="pg-search"></i>Type anywhere to <span class="bold">search</span></a>
            </div> -->
            <div class="headerMiddle" style="padding:0px;">
                <div class="brand inline">
                    <a href="/">
                        <img src="assets/img/logo.png" alt="logo" data-src="assets/img/logo.png" ui-jq="unveil" data-src-retina="assets/img/logo_2x.png" width="" height="26">
                    </a>
                </div>
            </div>
            <div class="dropdown pull-right userDropdown">
                <a id="sign-in" class="btn btn-primary btn-sm">Sign In</a>
            </div>
        </div>
    </div>

</div>

<div class="register-container sm-p-t-30">
    <div class="container-sm-height">
        <div class="row row-sm-height">
            <div class="col-sm-12 col-sm-height col-middle" style="padding-top: 80px;">
                <h2>Sign up</h2>

                @include('includes/flash')

                {{-- @if(!empty($errors))
                    @foreach($errors->all() as $error_msg)
                    <div style="margin-left: 0px;margin-top: 30px">
                        <div class="alert alert-error" role="alert">
                            <button class="close" data-dismiss="alert"><span aria-hidden="true">&times;</span></button>
                            <p>{{ $error_msg }}</p>
                        </div>
                    </div>
                    @endforeach
                @endif --}}

                <form method="POST" class="p-t-15" role="form" action="{{ url('signup') }}" class="has-form-validation" id="form-signup">
                    <input type="hidden" id="csrf_token" name="_token" value="{{ csrf_token() }}">
                    <div class="row">
                        <div class="col-sm-6 ">
                            <div class="form-group form-group-default {{ $errors->has('first_name') ? ' has-error' : '' }} required">
                                <label>First Name</label>
                                <input type="text" name="first_name" placeholder="First Name" class="form-control" value='{{ old('first_name') }}' data-rule-required="true">
                            </div>
                           @if($errors->has('first_name'))                            
                                <div class="alert alert-error" role="alert">
                                    <button class="close" data-dismiss="alert"><span aria-hidden="true">&times;</span></button>
                                    <p>{{ $errors->first('first_name') }}</p>
                                </div>                            
                            @endif
                        </div>
                        <div class="col-sm-6 ">
                            <div class="form-group form-group-default {{ $errors->has('last_name') ? ' has-error' : '' }}">
                                <label>Last Name</label>
                                <input type="text" name="last_name" placeholder="Last Name" class="form-control" value='{{ old('last_name') }}' data-rule-required="true">
                            </div>
                            @if($errors->has('last_name'))                            
                                <div class="alert alert-error" role="alert">
                                    <button class="close" data-dismiss="alert"><span aria-hidden="true">&times;</span></button>
                                    <p>{{ $errors->first('last_name') }}</p>
                                </div>                            
                            @endif
                        </div>
                    </div>
                    <div class="row">
                        <div class="form-group-attached">
                            <div class="col-sm-12 ">
                                <div class="form-group form-group-default {{ $errors->has('email') ? ' has-error' : '' }} required">
                                    <label>Email</label>
                                    <input type="email" id="email" name="email" placeholder="Email" class="form-control" value='{{ old('email') }}' data-url="{{url('check-email')}}" data-rule-required="true" data-rule-email="true" data-rule-valid-email="true" >
                                </div>
                                @if($errors->has('email'))                            
                                    <div class="alert alert-error" role="alert">
                                        <button class="close" data-dismiss="alert"><span aria-hidden="true">&times;</span></button>
                                        <p>{{ $errors->first('email') }}</p>
                                    </div>                            
                                @endif
                            </div>
                        </div>
                    </div>
                    <div class="row">
                        <div class="form-group-attached">
                            <div class="col-sm-12 ">
                                <div class="form-group form-group-default {{ $errors->has('username') ? ' has-error' : '' }} required">
                                    <label>Username</label>
                                    <input type="text" name="username" id="username" placeholder="Username" value='{{ old('username') }}' class="form-control" data-rule-required="true"  data-rule-minlength="6" data-rule-username="true" data-url="{{url('check-username')}}" >
                                </div>
                                @if($errors->has('username'))                            
                                    <div class="alert alert-error" role="alert" style="margin:9px 0;">
                                        <button class="close" data-dismiss="alert"><span aria-hidden="true">&times;</span></button>
                                        <p>{{ $errors->first('username') }}</p>
                                    </div>                            
                                @endif
                            </div>
                        </div>
                    </div>
                    <div class="row">
                        <div class="col-sm-12 ">
                            <div class="form-group form-group-default {{ $errors->has('password') ? ' has-error' : '' }} required">
                                <label>Password</label>
                                <input type="password" name="password" id="password" placeholder="Password" {{-- value='{{ old('password') }}' --}} value="" class="form-control" data-rule-required="true" data-rule-minlength="8" data-rule-alphanumeric="true">
                            </div>
                            @if($errors->has('password'))                            
                                <div class="alert alert-error" role="alert">
                                    <button class="close" data-dismiss="alert"><span aria-hidden="true">&times;</span></button>
                                    <p>{{ $errors->first('password') }}</p>
                                </div>                            
                            @endif
                        </div>                        
                    </div>
                    <div class="passwordProgress">                        
                        <div class="progress ">
                            <div id="progress" class="progress-bar " style="width:{!! 0 !!}%"></div>
                        </div>
                        <div class="alert " id="progress_msg" role="alert" style="display:none;">
                          <strong id="strength">Weak</strong>
                        </div>
                    </div>

                    <div class="row">
                        <div class="col-sm-12 ">
                            <div class="form-group form-group-default {{ $errors->has('password') ? ' has-error' : '' }} required">
                                <label>Confirm Password</label>
                                <input type="password" name="password_confirmation" placeholder="Confirm Password" class="form-control" value="" {{-- value='{{ old('password_confirmation') }}' --}} data-rule-required="true">
                            </div>
                            @if($errors->has('password_confirmation'))                            
                                <div class="alert alert-error" role="alert">
                                    <button class="close" data-dismiss="alert"><span aria-hidden="true">&times;</span></button>
                                    <p>{{ $errors->first('password_confirmation') }}</p>
                                </div>                            
                            @endif
                        </div>
                    </div>

                    <div class="row">
                        <div class="col-md-12 {{ $errors->has('termsconditions') ? ' has-error' : '' }}">
                            <div class="checkbox regTrms">
                                <input type="checkbox" value="1" name="termsconditions" data-rule-required="true" id="checkbox1">
                                <label for="checkbox1">I agree to Swolk's <a href="{{url('terms-and-conditions')}}">terms</a> of use and <a href="{{url('privacy-policy')}}">privacy policy</a>.</label>
                            </div>
                        </div>
                    </div>

                    <div class="row m-t-10">
                        <div class="col-md-6 {{ $errors->has('termsconditions') ? ' has-error' : '' }}">
                            <?php 
                            if(isset($uniquecode) && $uniquecode != ''){
                            ?>
                                <input type="hidden" name="uniquecode" value="<?php echo $uniquecode; ?>">
                            <?php    
                            }
                            ?>
                            <button class="btn btn-primary btn-cons m-t-10 .btn-loading" type="submit" name="btnSubmit" value="SUBMIT">Sign up</button>
                        </div>
                    </div>

                    <div class="row m-t-10">
                        <div class="col-md-6">
                         <!-- <a class="btn btn-block btn-social btn-facebook btn-cons m-t-10" href="{{url('auth/facebook/'.base64_encode(Request::get('code')))}}"> (14-11-17) changes due to register purpose -->
                         <a class="btn btn-block btn-social btn-facebook btn-cons m-t-10" href="{{url('auth/facebook/bG9naW5fcmVnaXN0ZXI=')}}">
                         
                         {{  Request::get('code') }}
                           
                                <i class="fa fa-facebook"></i> Sign up using Facebook
                            </a>
                        </div>
                        <div class="col-md-6">
                             <!-- <a class="btn btn-block btn-social btn-twitter btn-cons m-t-10" href="{{url('auth/twitter/'.base64_encode(Request::get('code')))}}">  (14-11-17) changes due to register purpose -->
                             <a class="btn btn-block btn-social btn-twitter btn-cons m-t-10" href="{{url('auth/twitter/bG9naW5fcmVnaXN0ZXI=')}}">
                                <i class="fa fa-twitter"></i> Sign up using Twitter
                            </a>
                        </div>
                    </div>
                    <div class="row m-t-10">
                    	<div class="col-md-12">
                        	<p>Already have an account ? <a href="{{url('login')}}">Login here</a></p>
                        </div>
                    </div>
				</div>
            </form>
        </div>
    </div>
</div>






<!-- login form -->

<div class="modal sign" id="signInNotifi" role="dialog">
    <!-- login form -->
    <div class="loginDropWrap">
        <!-- <form class="p-t-15" action="<?php echo url('login') ?>" role="form" method="POST" id="form-signin"> -->
        <form class="p-t-15" id="form-signin">
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
                        
                        <div class="spacer"></div>
                    </div>
                </div>
            </div>

            <div class="row">
                <div class="col-md-12">
                    <button class="btn btn-primary btnLoading btn-cons m-t-10  btn-loading" type="button" name="btnSubmit" value="submit" id="signinformsubmit" style="margin-bottom:10px !important;">Sign in</button>
                </div>
            </div>

            <div class="row m-t-10">
                <div class="col-md-12">
                    <a class="btn btn-block btn-social btn-facebook" onClick="location.href='<?php echo url('auth/facebook/bG9naW5fcmVnaXN0ZXI=') ?>'" >
                        <span class="pull-left" style="top: 8px;"><i class="fa fa-facebook"></i></span>
                        <span class="bold">Log in with Facebook</span>
                    </a>
                </div>
                <div class="col-md-12">
                    <a class="btn btn-block  btn-social btn-twitter" onClick="location.href='<?php echo url('auth/twitter/b25seV9sb2dpbg===') ?>'" >
                        <span class="pull-left" style="top: 8px;"><i class="fa fa-twitter"></i></span>
                        <span class="bold">Log in with Twitter</span>
                    </a>
                </div>
            </div>
        </form>

        <form id="form-signin-h" action="<?php echo url('login') ?>" role="form" method="POST" style="display: none;">
            <input type="text" id="signinh-email" name="email" placeholder="Username / E-mail">
            <input type="password" id="signinh-password" name="password" placeholder="Password">
        </form>


    </div>
    <!-- // login form -->

    <!-- forgot form -->
    <div class="forgotPassWrap" style="display:none;">
        <form class="p-t-15"role="form"  id="form-forgot" >
            <div class="validate">
                <div class="form-group form-group-default">
                    <label>E-MAIL </label>
                    <div class="controls">
                        <input type="text" name="email" placeholder="E-mail" id="forgot_email" class="form-control" value="">
                    </div>
                </div>
            </div>
            
            <div class="row">
                <div class="col-md-12">
                    <div class="checkbox nwChk explorecheck">
                        <p><a href="javascript:void(0);" onClick="signInCome()" id="back-signin">Signin</a></p>
                        <!-- <p class="newToSwolk">New to Swolk ? <a href="javascript:void(0);" id="signUpBack">Sign Up here</a></p> -->
                        <div class="spacer"></div>
                    </div>
                </div>
            </div>

            <div class="row">
                <div class="col-md-12">
                    <button class="btn btn-primary btnLoading btn-cons m-t-10  btn-loading" type="button" name="btnSubmit" value="submit" id="forgotformsubmit" style="margin-bottom:10px !important;">Submit</button>
                </div>
            </div>

        </form>
    </div>
    <!-- // forgot form -->

</div>

<!-- login form -->

@stop

<style type="text/css">
	body{height:auto !important;}
</style>

@section('customScript')
<script type="text/javascript">
// LOGIN POP
function forgotcome(){
    $('.loginDropWrap').fadeOut(100);
    $('.forgotPassWrap').fadeIn(100);
}
function signInCome(){
    $('.forgotPassWrap').fadeOut(100);
    $('.loginDropWrap').fadeIn(100);
}

function objectifyForm(formArray) {//serialize data function
    var returnArray = {};
    for (var i = 0; i < formArray.length; i++){
    returnArray[formArray[i]['name']] = formArray[i]['value'];
    }
    return returnArray;
}

$(document).ready(function(){


    $('#signinformsubmit').on('click', function(){

        // first remove the aerror box
        $('#signerrorbox').remove();

        // add disable effect
        $(this).addClass('disabled');

        // console.log(objectifyForm($("#form-signin").serializeArray()))
        var getsigninformdata = objectifyForm($("#form-signin").serializeArray());

        // set the submit form value
        $('#signinh-email').val(getsigninformdata.email)
        $('#signinh-password').val(getsigninformdata.password)

        // check if not blank
        if(getsigninformdata.email != '' && getsigninformdata.password != ''){

            // make ajax call
            var form = new FormData();
            form.append("email", getsigninformdata.email);
            form.append("password", getsigninformdata.password);

            // console.log(form.entries())
            // for(var pair of form.entries()) {
            //     console.log(pair)
            //     console.log(pair[0]+ ', '+ pair[1]); 
            // }

            var settings = {
                "async": true,
                "crossDomain": true,
                "url": "/loginapi",
                "method": "POST",
                "processData": false,
                "contentType": false,
                "mimeType": "multipart/form-data",
                "data": form
            }

            $.ajax(settings).done(function (response) {
               // alert(response);
                 //console.log(response);
                var respdata = JSON.parse(response);
                
                if(respdata.msg == "error"){
                     //alert('Invalid email and password')
                    $('#form-signin').prepend('<div id="signerrorbox" class="signin-error">Invalid email and password</div>');
                    $('#signinformsubmit').removeClass('disabled');
                } else {
                    // alert('everything ok')
                    document.getElementById('form-signin-h').submit();
                }
            });
        } else {
            // document.getElementById("signSubmit").classList.remove('disabled');
            $('#form-signin').prepend('<div id="signerrorbox" class="signin-error">Please put the email and password</div>');
            $('#signinformsubmit').removeClass('disabled');
        }

    });






    


    

});



 $('#forgotformsubmit').on('click', function(){
    

        // first remove the aerror box
        $('#forgoterrorbox').remove();

        // add disable effect
        $(this).addClass('disabled');

        // console.log(objectifyForm($("#form-signin").serializeArray()))
        var getforgotformdata = objectifyForm($("#form-forgot").serializeArray());

        // set the submit form value
     

        // check if not blank
        if(getforgotformdata.email != ''){

            // make ajax call
            var form = new FormData();
            form.append("email", getforgotformdata.email);
            
           
            // console.log(form.entries())
            // for(var pair of form.entries()) {
            //     console.log(pair)
            //     console.log(pair[0]+ ', '+ pair[1]); 
            // }

            var settings = {
                "async": true,
                "crossDomain": true,
                "url": "/forgotPasswordApi",
                "method": "POST",
                "processData": false,
                "contentType": false,
                "mimeType": "multipart/form-data",
                "data": form
            }
  
            $.ajax(settings).done(function (response) {
                //alert(response);
                 //console.log(response);
                var respdata = JSON.parse(response);
                
                if(respdata.msg == "error"){
                     //alert('Invalid email and password')
                    $('#form-forgot').prepend('<div id="forgoterrorbox" class="forgot-error">Invalid email and password</div>');
                    $('#forgotformsubmit').removeClass('disabled');
                } else {
                    // alert('everything ok')
                    $('#form-forgot').prepend('<div id="forgoterrorbox" class="forgot-success">Please check your email. </div>');
                    $('#forgot_email').val('');
                    $('#forgotformsubmit').removeClass('disabled');
                }
            });
        } else {
            // document.getElementById("signSubmit").classList.remove('disabled');
            $('#form-forgot').prepend('<div id="forgoterrorbox" class="forgot-error">Please put the email and password</div>');
            $('#forgotformsubmit').removeClass('disabled');
        }

    });


/*function checkUsername(value){
	userName = value.trim();
	jQuery.ajax({
			type: "GET",
			url: "check-username",
			cache: false,
			dataType: 'json',
			data: {username: userName},
			beforeSend: function(){
				
			},
			error: function(jqXHR, textStatus, errorThrown) 
			{
				
				alert(''+textStatus+', errorThrown='+errorThrown+'');
			}, 
			success:function(data){
				if(JSON.stringify(data) == '1'){
					
				}
				else{
					alert('Username already registered');
				}
			}
		});
}*/
$(document).ready(function(){
var progressColor = '';
var length = 0;
var progress_msg = '';


// sign in pop up open
$('#sign-in').on('click', function(){
    $('#signInNotifi').modal('show');
    setTimeout(function () {
        //$scope.goToLogin();
        //$("#signInNotifi.in .signIN").trigger("click");
    }, 3000);
    $("body").on("click", "#signInNotifi", function () {
        if ($("#myModal").hasClass("in")) {
            setTimeout(function () {
                $("body").addClass("modal-open");
            }, 500);
        }
    });


    
});



$(document).on('keyup', '#password', function(){

    var strength = checkStrength($(this).val());    
    $('#progress').removeClass(progressColor);
    $('#progress_msg').removeClass(progress_msg);
    $('#progress_msg').hide();

    if(strength == 'Too short'){        
        progressColor = 'progress-bar-danger';
        progress_msg = 'alert-danger';
        length = 25;       
    }
    else if(strength == 'Weak'){        
        progressColor = 'progress-bar-warning';
        progress_msg = 'alert-warning';
        length = 50;       
    }
    else if(strength == 'Good'){        
        progressColor = 'progress-bar-success';
        progress_msg = 'alert-success';
        length = 75;       
    }
    else if(strength == 'Strong'){        
        progressColor = 'progress-bar-success';
        progress_msg = 'alert-success';
        length = 100;
    }  
    $('#progress_msg').show().addClass(progress_msg);
    $('#progress').addClass(progressColor);
    $('#progress').css('width',length+'%');
    $('#strength').text(strength);

});


function checkStrength(password) {
    var strength = 0;
    if (password.length < 8) {
        return 'Too short';
    }
    if (password.length > 7) strength += 1;
    // If password contains both lower and uppercase characters, increase strength value.
    if (password.match(/([a-z].*[A-Z])|([A-Z].*[a-z])/)) strength += 1;
    // If it has numbers and characters, increase strength value.
    if (password.match(/([a-zA-Z])/) && password.match(/([0-9])/)) strength += 1;
    // If it has one special character, increase strength value.
    // If value is less than 2
    if (strength < 2) {
        return 'Weak';
    } else if (strength == 2) {
        return 'Good';
    } else {
        return 'Strong';
    }
}
});
</script>
@endsection