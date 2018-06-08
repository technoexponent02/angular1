<style type="text/css">
	/* body{background-color:#f6f9fa !important;} */	
	.header{background:#fff !important; box-shadow:none;}
</style>
<div class="container-fluid padding-25 sm-padding-10" style="padding-top:0px !important;">
	<div class="m-t-30">
		<div class="panel newpanel">
			<div class="row">
				<div class="col-md-8 col-lg-8 col-md-push-2 col-lg-push-2">
					<div class="linkBigTxt">
						<h2>Let's build a better community by inviting friends!
							<span ng-hide="adminInvite"><small>You have left {{allowedInvites}} remaining keys for your friends</small></span>
						</h2>
					</div>
					<form name="myForm" novalidate ng-submit="myFunc()">
						<div class="row">
							<div class="col-sm-12">
								<div class="form-group form-group-default required">
									<label for="inviteEmail">Email ID</label>
									<input type="text" name="email" ng-model="emailAddress" class="form-control" placeholder="Type your email ID" id="inviteEmail" required/>
									<span style="color:red" ng-show="emailRequired || invalidEmail">
										<span ng-show="emailRequired">Email is required.</span>
										<span ng-show="invalidEmail">Invalid email address.</span>
									</span>
									<span ng-style="myStyle" ng-show="message !== ''">
										<span ng-show="message !== '' && myStyle.color !== 'green'">{{message}}</span>
									</span>
								</div>
								<div class="form-group form-group-default required">
									<label for="inviteMessage">Message</label>
									<textarea class="form-control" placeholder="Type here..." ng-model="inviteMessage" id="inviteMessage"></textarea>
									<span style="color:red" ng-show="inviteMsgRequired">
										<span ng-show="inviteMsgRequired">Invite Message is required.</span>
									</span>
								</div>
							</div>
						</div>
						<div class="row">
							<div class="col-sm-12 align-center">
								<button type="submit" class="btn btn-primary btn-cons" ng-disabled='allowedInvites < 1 || sendingInvitation'>
									<span ng-if="sendingInvitation">SENDING...</span>
									<span ng-if="!sendingInvitation">SEND</span>
								</button>
								<div class="clearfix"></div>
							</div>

						</div>
					</form>
				</div>
			</div>
		</div>
	</div>
</div>

<!-- SHARE Modal -->
<sharepost-card></sharepost-card>
 <!-- Details Modal-->
<postcard-modal></postcard-modal> 

<!-- DELETE POST CARD MODAL -->
<delete-post-modal></delete-post-modal>

<!-- REPORT POST CARD MODAL -->
<report-post-modal></report-post-modal>
<!-- REPORT COMMENT  MODAL -->
<report-comment-modal></report-comment-modal>

<?php /*<script type="text/javascript" src="<?php echo asset('assets/plugins/bx-slider/flickity.pkgd2.js'); ?>"></script> */?>

<!-- <style type="text/css">
	body,
	body .page-container .page-content-wrapper .content{background-color:#fff;}
</style> -->
