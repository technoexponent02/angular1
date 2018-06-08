@extends('layouts.public')
@section('content')
<div class="login-wrapper ">
    <div class="bg-pic"> 
		<div class="login-container bg-white">
			<div class="p-l-50 m-l-20 p-r-50 m-r-20 p-t-50 m-t-20 sm-p-l-15 sm-p-r-15 sm-p-t-40">
				<div class="loginHeader">
					<a  href="{{url('/')}}" style="cursor:pointer;">
						<img src="/assets/img/logo_2x.png" alt="Logo"/>
					</a>
				</div>
				<div class="p-t-15"><h2>Reset Password</h2></div>
				
				@include('includes/flash')
				@if(!empty($errors))
					@foreach($errors->all() as $error_msg)
					<div style="margin-left: 0px;margin-top: 30px">
						<div class="alert alert-error" role="alert">
							<button class="close" data-dismiss="alert"><span aria-hidden="true">&times;</span></button>
							<p>{{ $error_msg }}</p>
						</div>
					</div>
					@endforeach
				@endif

				<form id="form-reset-password" class="p-t-15" role="form" method="POST" action="{{ url('password/reset') }}">
					<input type="hidden" name="_token" value="{{ csrf_token() }}">
					<input type="hidden" name="token" value="{{ $token }}">

					<div class="row">
						<div class="col-sm-12 ">
							<div class="form-group form-group-default {{ $errors->has('password') ? ' has-error' : '' }}">
								<label>Password</label>
								<div class="controls">
									<input type="password" class="form-control" name="password" id="password" placeholder="Password" data-rule-required="true" data-rule-minlength="8">
								</div>
							</div>
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
							<div class="form-group form-group-default {{ $errors->has('password_confirmation') ? ' has-error' : '' }}">
								<label>Confirm Password</label>
								<div class="controls">
									<input type="password" class="form-control" name="password_confirmation" placeholder="Confirm Password" data-rule-required="true" >
								</div>
							</div>
						</div>
					</div>
					
					<button class="btn btn-primary btn-cons m-t-10 .btn-loading" type="submit" name="btnSubmit" value="submit">Reset Password</button>
				</form>

				<br>
			</div>
		</div>
		<div class="loginLeftImage-ch">
			<div class="loginBgtxt-ch">
				<div class="bg-caption pull-bottom sm-pull-bottom text-white">
					<h2 class="semi-bold text-white">
					Pages make it easy to enjoy what matters the most in the life</h2>
					<p class="small">images Displayed are solely for representation purposes only, All work copyright of respective owner, otherwise © 2013-2014 REVOX.</p>
				</div>
			</div>
        </div>
        <!--<div class="bg-caption pull-bottom sm-pull-bottom text-white p-l-20 m-b-20">
            <h2 class="semi-bold text-white">
            Pages make it easy to enjoy what matters the most in the life</h2>
            <p class="small">images Displayed are solely for representation purposes only, All work copyright of respective owner, otherwise © 2013-2014 REVOX.</p>
        </div>-->
    </div>    
</div>
@stop


@section('customScript')
<script type="text/javascript">
$(document).ready(function(){
var progressColor = '';
var length = 0;
var progress_msg = '';

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