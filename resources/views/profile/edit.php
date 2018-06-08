<div class="container-fluid padding-25 sm-padding-10 editProfilePG" style="background-color:#fff;">
	<div class="row">
		<div class="col-md-12 col-lg-12">
			<div class="profileMiddle postingform" style="width:100%;">
				<div id="rootwizard">
					<div id="rootwizardHeader">
						<div class="row">
							<div class="col-md-12">
								<ul class="nav nav-tabs nav-tabs-linetriangle nav-tabs-separator nav-stack-sm postingformtab">
									<li ng-class="{'active':currentTab=='editProfile'}">
										<a ng-click="changeTab('editProfile');">
											<span>Profile</span>
										</a>
									</li>
									 <li ng-class="{'active':currentTab=='changePassword'}">
										<a ng-click="changeTab('changePassword');">
											<span>Security</span>
										</a>
									</li>
								</ul>
							</div>
						</div>
						<div class="clearfix"></div>
					</div>
                    <div style="position: relative;">
                        <div class="loaderImage" style="background-color: #fff;" ng-show="loading"></div>
    					<div class="postNewMainsection">
    						<div class="row">
    							<div class="col-md-10 col-lg-10 col-md-push-1 col-lg-push-1">
    								<div class="tab-content m-t-20">
    									<div class="profileCommentBoxTop nWprofileCommentBoxTop">
    										
                                                <div ng-include="dynamicPage" id="dynamic_page"></div>
    									</div>
    								</div>
    							</div>					
    						</div>					
    					</div>	
                    </div>        				
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
