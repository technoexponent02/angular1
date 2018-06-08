@extends('layouts.public')
@section('content')
<div class="login-wrapper">
    <div class="bg-pic">
		<div class="login-container bg-white">
			<div class="p-l-50 m-l-20 p-r-50 m-r-20 p-t-50 m-t-20 sm-p-l-15 sm-p-r-15 sm-p-t-40">				
				<div class="loginHeader">
					<a href="{{url('/')}}" style="cursor:pointer;">
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
				
				<form id="form-forgot-password" class="p-t-15" role="form" method="POST" action="{{ url('password/forgot') }}">
					<input type="hidden" name="_token" value="{{ csrf_token() }}">
					<div class="">
						<div class="form-group form-group-default {{ $errors->has('email') ? ' has-error' : '' }} required" >
							<label>Email</label>
							<div class="controls">
								<input type="text" name="email" placeholder="Email" class="form-control" value="{{old('email')}}" data-rule-required="true" data-rule-email="true">
							</div>
						</div>
					</div>
					<button class="btn btn-primary btn-cons m-t-10 btn-loading" type="submit" name="btnSubmit" value="submit">Send Password Reset Link</button>
				</form>
				<br>
			</div>
		</div>
		<div class="loginLeftImage">
			<div class="loginBgtxt">
			   <div class="bg-caption pull-bottom sm-pull-bottom text-white">
					<h2 class="semi-bold text-white">Swolk is a <strong>topic</strong> &amp; <strong>location</strong> based network that connects you and people around the world, who share the similar interest as you.</h2>
					<p class="small">&copy; Copyright {{date('Y')}} Swolk.com - All Rights Reserved</p>
				</div>
			</div>
        </div>
        <!--<div class="bg-caption pull-bottom sm-pull-bottom text-white p-l-20 m-b-20">
            <h2 class="semi-bold text-white">
            Pages make it easy to enjoy what matters the most in the life</h2>
            <p class="small">
                images Displayed are solely for representation purposes only, All work copyright of respective owner, otherwise © 2013-2014 REVOX.
            </p>
        </div>-->		
		<div class="spacer"></div>
    </div>
</div>
@stop
<style type="text/css">
	body{height:auto !important;}
</style>