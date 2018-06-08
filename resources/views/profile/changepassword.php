<div id="securityTab">
	<div class="row">
		<div class="col-sm-12">
			<div class="linkBigTxt">
				<h2>Change Your Password</h2>
			</div>
		</div>
		<form name="changePasswordForm" method="POST" role="form" class="" ng-submit="submitPasswordForm(changePasswordForm.$valid)" novalidate>      
			<div class="row">
				<div class="col-sm-6">
					<div class="form-group form-group-default  required">
						<label for="currentPass">Verify current password</label>
						<input type="password" class="form-control" name="old_password" placeholder="" id="currentPass" ng-model="formData.currentPassword" required>
						<label class="error" ng-show="changePasswordForm.old_password.$error.required && passwordFormSubmitted">Old Password is required</label>
						<label class="error" ng-show="oldPasswordMismatch && passwordFormSubmitted">Old Password does not match</label>
					</div>
				</div>
			</div>
			<div class="row">
				<div class="col-sm-6">
					<div class="form-group form-group-default  required">
						<label for="newPass">Set new password</label>
						<input type="password" class="form-control" name="new_password" placeholder="" id="newPass" ng-model="formData.newPassword" required>
						<label class="error" ng-show="changePasswordForm.new_password.$error.required && passwordFormSubmitted">New Password is required</label>
					</div>
				</div>
				<div class="col-sm-6">
					<div class="form-group form-group-default  required">
						<label for="confPass">Confirm new password</label>
						<input type="password" class="form-control" name="confirm_new_password" placeholder="" id="confPass" ng-model="formData.confirmNewPassword" required>
						<label class="error" ng-show="changePasswordForm.confirm_new_password.$error.required && passwordFormSubmitted">Confirm New Password is required</label>
						<label class="error" ng-show="newPasswordMismatch && passwordFormSubmitted">New Password & confirm new password does not match</label>
					</div>
				</div>
			</div>
			<div class="row">
				<div class="col-sm-12 align-center">
					<button type="submit" class="btn btn-primary btn-cons btn-loading" name="btnSubmit" value="submit" ng-disabled="changePasswordButtonText=='Updating...'">{{changePasswordButtonText}}</button>
				</div>
			</div>
		</form>
	</div>
</div>