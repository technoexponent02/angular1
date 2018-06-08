<div id="profileTab">					
	<div class="row"> 										
		<div class="col-sm-12">
			<div class="linkBigTxt">
				<h2>Edit your profile</h2>
			</div>
		</div>
		<form name="userForm" method="POST" role="form" class="" ng-submit="submitForm(userForm.$valid)" novalidate>                          
			<div class="row">
				<div class="col-sm-6">
					<div class="form-group form-group-default  required">
						<label for="fName">First Name</label>
						<input type="text" class="form-control" name="first_name" placeholder="First name" ng-model="editUser.first_name" id="fName" required>
						<label class="error" ng-show="userForm.first_name.$invalid && !userForm.first_name.$pristine">First name is required.</label>
					</div>
				</div>
				<div class="col-sm-6">
					<div class="form-group form-group-default">
						<label for="lName">Last Name</label>
						<input type="text" class="form-control" name="last_name" placeholder="Last name" ng-model="editUser.last_name" id="lName">
					</div>
				</div>
			</div>
			<div class="upload-holder">
				<div class="row">
					<label class="col-sm-12 control-label">Photo</label>
					<div class="col-sm-12">	
						<label class="error" ng-show="profilePicErr.length > 0">{{profilePicErr}}</label>
						<div class="editProfileIMG">
							<div class="uploadProfilePicprof"
                                 ng-show="user.profile_image!='' || imagePreview == true"
                                 ng-style="{'background-image': 'url(' + profileImage + ')'}" alt="" id="thumbnail-preview no-repeat;">
								<div class="upProfileImgLoad" ng-show="picUploading"></div>

							</div>
							<div class="uploadProfilePicprof userStatusImage {{user.user_color}}" ng-show="editUser.profile_image == '' && imagePreview == false">
								
									<span class="txt">{{editUser.first_name.charAt(0)}}</span>
								
							</div>
							<div class="imagePreviewButton" ng-show="imagePreview">
								<a  href="javascript:void(0);" ng-click="uploadPicture();" class="btn btn-sm btn-primary" style="width:53px; padding-left:0; padding-right:0; margin:0;">Ok</a>
								<a ng-click="restoreProfilePictureDefault();" class="btn  btn-sm btn-danger" style="width:78px; padding-left:0; padding-right:0; margin:0;">Cancel</a>
							</div>											
							<label class="uploadProfilePicBtn" ng-hide="imagePreview">
								<input type="file" id="fileupload" ng-model="myFile" onchange="angular.element(this).scope().file_changed(this)" accept="image/*">
								<span>Change Picture</span>
							</label>
						</div>
					</div>
				</div>
			</div>
			<div class="row" id="username_email">
				<label class="col-sm-12 control-label">Date Of Birth</label>
				<div class="col-sm-6">
					<div pg-form-group class="form-group form-group-default form-group-default-select"
					 area-required="true">
						<label for="month">Month</label>
						<select class="form-control" id="month" name="month" ng-model="editUser.month" ng-required="(!userForm.day.$pristine && editUser.date != '') || (!userForm.year.$pristine && editUser.year != '')">
							<option value="">Select Month</option>
							<option value="{{month.id}}" ng-repeat="month in monthObj track by $index">{{month.name}}</option>
						</select>
						 <label class="error" ng-show="(!userForm.day.$pristine && editUser.date != '' && userForm.month.$invalid) || (!userForm.year.$pristine && editUser.year != '' && userForm.month.$invalid)">Month is required</label>
					</div>
				</div>
				<div class="col-sm-3">
					<div pg-form-group class="form-group form-group-default form-group-default-select" area-required="true">
						<label for="date">Date</label>
						<select class="form-control" id="date" ng-model="editUser.date" name="day" ng-required="(!userForm.month.$pristine && editUser.month != '') || (!userForm.year.$pristine && editUser.year != '')">
							<option value="">Select Day</option>
							 <option value="{{day}}" ng-repeat="day in days track by $index">{{day}}</option>
						</select>
						<label class="error" ng-show="(!userForm.month.$pristine && editUser.month != '' && userForm.day.$invalid) || (!userForm.year.$pristine && editUser.year != '' && userForm.day.$invalid)">Day is required</label> <!-- -->
					</div>
				</div>
				<div class="col-sm-3" >
					<div pg-form-group class="form-group form-group-default form-group-default-select"
					 area-required="true">
						<label for="year">Year</label>
						<select class="form-control" id="year" name="year" ng-model="editUser.year" ng-required="(!userForm.month.$pristine && editUser.month != '') || (!userForm.day.$pristine && editUser.date != '')">
							<option value="">Select Year</option>
							<option value="{{year}}" ng-repeat="year in years track by $index">{{year}}</option>
						</select>
						<label class="error" ng-show="(!userForm.month.$pristine && editUser.month != '' && userForm.year.$invalid) || (!userForm.day.$pristine && editUser.date != '' && userForm.year.$invalid)">Year is required</label><!-- -->
					</div>
				</div>
			</div>
			<div class="row">
				<div class="col-md-6">
					<div class="form-group form-group-default  required">
						<label for="email">Email</label>    	
						<input type="text" ng-keyup="checkValidEmail(editUser.email)" name="email" id="email" ng-model="editUser.email" class="form-control"  ng-blur="checkUniqueUsernameEmail(editUser.email, 'email');"  required>
						<label class="error" ng-show="invalidEmail && !userForm.email.$error.required">Incorrect Email Format.</label>
						<label class="error" ng-show="userForm.email.$error.required">Your email is required.</label>
						<label class="error" ng-show="!uniqueEmail">This email is already registered in another account.</label>
					</div>
				</div>
				<div class="col-md-6">
					<!-- Commented On Purpose
						 Change in profilecontroller saveUserData method to allow username update
					--> 
					<div class="form-group form-group-default required" ng-class="{ 'has-error' : userForm.username.$error.minlength || userForm.username.$error.maxlength || userForm.username.$error.required}">
						<label for="username">Username</label>
						<input type="text" name="username" id="username" ng-disabled="true" ng-model="editUser.username"  class="form-control" ng-minlength="3" ng-maxlength="15" ng-blur="checkUniqueUsernameEmail(editUser.username, 'username');" required>
						<label class="error" ng-show="userForm.username.$error.minlength">Username is too short</label>
						<label class="error" ng-show="userForm.username.$error.maxlength">Username is too long.</label>
						<label class="error" ng-show="userForm.username.$error.required">Username is required.</label>
						<label class="error" ng-show="!uniqueUsername">This username is already registered in another account.</label>
					</div> <!-- -->
				</div>
			</div>
			<div class="row">
				<label class="col-sm-12 control-label">Country &amp; State</label>
				<div class="col-sm-6">
					<div pg-form-group class="form-group form-group-default form-group-default-select"
					 ng-class="{ 'has-error' : userForm.country.$error.required }"
					 area-required="true">
						<label class="">Country </label>
						<select class="form-control" name="country" ng-model="editUser.country_id" ng-change="updateState()">
							<option value="">Country</option>
							<option value="{{countryInfo.id}}" ng-repeat="countryInfo in allCountries track by $index">{{countryInfo.country_name}}</option>
						</select>
						<!--<label class="error" ng-show="userForm.country.$error.required">Country is required</label>-->
					</div>
				</div>
				<div class="col-sm-6">
					<div pg-form-group class="form-group form-group-default form-group-default-select"
					 ng-class="{ 'has-error' : userForm.state.$error.required }"
					 area-required="true">
						<label class="">State </label>
						<select class="form-control" name="state" ng-model="editUser.state_id">
							<option value="">State</option>
							<option value="{{state.id}}" ng-repeat="state in states track by $index">{{state.name}}</option>
						</select>
						<!--<label class="error" ng-show="userForm.state.$error.required">State is required</label>-->
					</div>
				</div>
			</div>										
			<div class="row">
				<label class="col-sm-12 control-label">City &amp; ZIP</label>
				<div class="col-sm-6">
					<div class="form-group form-group-default">
						<label for="City">City</label>
						<input type="text" class="form-control" name="city" placeholder="City" ng-model="editUser.city">
					   <!--  <label class="error" ng-show="userForm.city.$error.required">City is required.</label> -->
					</div>
				</div>
				<div class="col-sm-6">
					<div class="form-group form-group-default">
						<label for="ZIP">ZIP</label>
						<input type="text" class="form-control" name="zipcode" placeholder="ZIP Code" ng-model="editUser.zipcode" class="form-control error">
						 <!-- <label class="error" ng-show="userForm.zipcode.$error.required">Zipcode is required.</label> -->
					</div>
				</div>
			</div>
			<div class="row">
				<div class="col-sm-12">
					<div class="form-group form-group-default has-error">
						<label for="Address">Address</label>
						<textarea class="form-control" ng-model="editUser.address" name="address" rows="3" placeholder="Type here...">{{editUser.address}}</textarea>
						<label class="error"></label>
					</div>
				</div>
			</div>

			<div class="row">
				<div class="col-sm-12">
					<div class="form-group form-group-default has-error aboutFld">
						<label for="AboutMe">About Me
						</label>
						<span class="charRemaining"  ng-style="myStyle">{{ charsRemaining }} characters
							remaining</span>
						<textarea class="form-control" ng-model="editUser.about_me" ng-change="checkAboutMe(editUser
						.about_me)" ng-model-options="{'updateOn': 'default blur','debounce': {'default': 250,'blur': 0}}" name="about_me" rows="3" placeholder="Type here..." maxlength="{{ maxAboutMeChars }}">{{editUser.about_me}}</textarea>
						<label class="error"></label>
					</div>
				</div>
			</div>
            <?php /*
			<div class="row">
				<div class="col-sm-12">
					<div class="form-group form-group-default has-error">
						<label for="Description">Description</label>
						<textarea class="form-control" name="description" ng-model="editUser.description"  rows="3" placeholder="Type here...">{{editUser.description}}</textarea>
						<label class="error"></label>
					</div>
				</div>
			</div>
            */?>
			<div class="row">
				<div class="col-sm-12">
					<div class="form-group form-group-default has-error">
						<label for="ocu">Occupation</label>
						<input type="text" name="occupation" ng-model="editUser.occupation"  class="form-control" placeholder="Type here" id="ocu">
						<label class="error"></label>
					</div>
				</div>
			</div>

			<div class="row">
				<div class="col-sm-12">
					<div class="form-group form-group-default has-error">
						<label for="web">Website</label>
						<input type="text" name="website"  class="form-control" ng-model="editUser.website" placeholder="Type here" id="web">
						<label class="error"></label>
					</div>
				</div>
			</div>

			<div class="row">
				<div class="col-sm-12">
					<div class="form-group form-group-default has-error">
						<label for="fb">Facebook Profile</label>
						<div class="prefixField">
							<label for="fb">facebook.com/</label>
							<input type="text" name="profile_facebook"  class="form-control" ng-model="editUser.profile_facebook" placeholder="Type here" id="fb">
						</div>
					</div>
				</div>
			</div>
			<div class="row">
				<div class="col-sm-12">
					<div class="form-group form-group-default has-error">
						<label for="twt">Twitter Profile</label>
						<div class="prefixField tw">
							<label for="twt">twitter.com/</label>
							<input type="text" name="profile_twitter" class="form-control" ng-model="editUser.profile_twitter" placeholder="Type here" id="twt">
						</div>
					</div>
				</div>
			</div>
			<!--<div class="row">
				<div class="col-sm-12">
					<div class="form-group form-group-default has-error">
						<label for="Occupation">LinkedIn Profile</label>
						<input type="text" name="profile_linkedin" class="form-control" ng-model="editUser.profile_linkedin" placeholder="Type here">
						<label class="error"></label>
					</div>
				</div>
			</div>-->
			<div class="row">
				<div class="col-sm-12 align-center">
					<button type="submit" ng-disabled="userForm.$invalid || invalidEmail" class="btn btn-primary btn-cons btn-loading" name="btnSubmit" value="submit">{{editProfileButtonText}}</button>
				</div>
			</div>
		</form>
	</div>	
</div>

<script type="text/javascript">
	$('.uploadProfilePicBtn input').on( "touchstart", function(){
		$(this).trigger('mouseenter');
	});
	$('.uploadProfilePicBtn').on( "touchstart", function(){
		$(this).find("input").trigger('mouseenter');
	});
</script>