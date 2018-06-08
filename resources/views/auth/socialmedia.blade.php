@extends('layouts.public')
@section('content')
<div class="register-container full-height sm-p-t-30">
	<div class="container-sm-height full-height">
		<div class="row"> 										
			<div class="col-sm-12">
				<h2>Complete Sign up</h2>
			</div>
			<div class="col-sm-12">
				@include('includes/flash')
				<form method="POST" class="p-t-15" role="form" action="{{ url('auth/facebook/signup/'.$user['media']) }}" class="has-form-validation" enctype="multipart/form-data" id="form-signup">
					<input type="hidden" id="csrf_token" name="_token" value="{{ csrf_token() }}"/>
					<div class="row">
						<div class="form-group-attached">
							<div class="col-sm-6 ">
								<div class="form-group form-group-default {{ $errors->has('first_name') ? ' has-error' : '' }}  required">
									<label>First Name</label>
									<input type="text" name="first_name" placeholder="First Name" class="form-control" value='{{ (isset($user['first_name']))?$user['first_name']:'' }}' data-rule-required="true">
								</div>
								@if($errors->has('first_name'))                            
									<div class="alert alert-error" role="alert">
										<button class="close" data-dismiss="alert"><span aria-hidden="true">&times;</span></button>
										<p>{{ $errors->first('first_name') }}</p>
									</div>                            
								@endif
							</div>
						</div>
						<div class="col-sm-6 ">
							<div class="form-group form-group-default {{ $errors->has('last_name') ? ' has-error' : '' }}">
								<label>Last Name</label>
								<input type="text" name="last_name" placeholder="Last Name" class="form-control" value="{{ (isset($user['last_name']))?$user['last_name']:'' }}" data-rule-required="true">
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
								<div class="form-group form-group-default {{ $errors->has('username') ? ' has-error' : '' }}  required">
									<label>Username</label>
									<input type="text" name="username" id="username" placeholder="Username" value='' class="form-control" data-rule-required="true"  data-rule-minlength="6" data-rule-username="true" data-url="{{url('check-username')}}">
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
					<div class="upload-holder" style="margin-top:10px;">
						<div class="row">
							<label class="col-sm-12 control-label">Profile Picture</label>
							<div class="col-sm-12">	
								<div class="editProfileIMG">
									<div class="uploadProfilePicprof" style="background:url({{isset($user['avatar']) ? $user['avatar'] : ''}}) no-repeat;" alt="" id="thumbnail-preview">
										<!-- <div class="upProfileImgLoad" ng-show="picUploading"></div>-->

									</div>
									<div class="imagePreviewButton" style="display:none;">
										
										<a href="javascript:void(0);" class="btn  btn-sm btn-danger" id="cancelImage" style="width:78px; padding-left:0; padding-right:0; margin:0;">Cancel</a>

									</div>										
									<label class="uploadProfilePicBtn">
										<input type="file" name="profile">
										<span>Change Picture</span>
									</label>
								</div>
							</div>
						</div>
					</div>

					<div class="row">
						<div class="form-group-attached">
							<div class="col-md-12">
								<div class="checkbox {{ $errors->has('termsconditions') ? ' has-error' : '' }}">
									<input type="checkbox" value="1" name="termsconditions"  data-rule-accept="true" id="checkboxTerms">
									<label for="checkboxTerms">I agree to Swolk's <a href="{{url('terms-and-conditions')}}">terms</a> of use and <a href="{{url('privacy-policy')}}">privacy policy</a>.</label>
								</div>
							</div>
						</div>
					</div>
					<div class="row m-t-10">
                        <div class="col-md-6 ">
                               <button class="btn btn-primary btn-cons m-t-10" type="submit" name="btnSubmit" value="SUBMIT">Sign up</button>                             
                        </div>
                    </div>
				</form>
			</div>
		</div>
	</div>
</div>
<style type="text/css">
	/*html, body{background-color:#fff;}*/
</style>
@stop

@section('customScript')
<script type="text/javascript">
$(document).ready(function(){
	var url = $("#thumbnail-preview").css('background-image');
	url 	= url.replace(/^url\(["']?/, '').replace(/["']?\)$/, '');
	//console.log(url);
	$("input[type=file][name=profile]").on("change", function(){
		if (this.files && this.files[0]) {
	        var reader = new FileReader();

	        reader.onload = function (e) {
	            $("#thumbnail-preview").css('background-image', 'url('+e.target.result+')');
	            $("div.imagePreviewButton").show();
	            $("label.uploadProfilePicBtn").hide();
	        }

	        reader.readAsDataURL(this.files[0]);
	    }	 
	});
	$("#cancelImage").on("click", function(){
		$("#thumbnail-preview").css('background-image', 'url('+url+')');
		$("input[type=FILE][name=profile]").val('');
	    $("div.imagePreviewButton").hide();
	    $("label.uploadProfilePicBtn").show();
	});
});	
</script>
@endsection