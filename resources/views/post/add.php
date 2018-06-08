<!-- START JUMBOTRON -->
<?php /*<div class="postSuccess">
	Your post has been successfully submitted.
</div> */?>
<div class="jumbotron" data-pages="parallax">
    <div class="container-fluid padding-25 sm-padding-10" style="background-color:#fff;">
        <div class="row">
            <div class="col-md-12 col-lg-12">
                <div class="profileMiddle postingform" style="width:100%;">
                    <div id="rootwizard">
                        <div id="rootwizardHeader">
                            <div class="row">
                                <div class="col-md-8 col-lg-8">
                                    <ul class="nav nav-tabs nav-tabs-linetriangle nav-tabs-separator nav-stack-sm postingformtab">
                                        <li ui-sref-active="active" ng-hide="formData.edit_post">
                                            <a ui-sref="post-add.photo">
                                                <span>Photo</span>
                                            </a>
                                        </li>
                                        <li ui-sref-active="active" ng-hide="formData.edit_post">
                                            <a ui-sref="post-add.video">
                                                <span>Video</span>
                                            </a>
                                        </li>
                                        <li ui-sref-active="active" ng-hide="formData.edit_post">
                                            <a ui-sref="post-add.article">
                                                <span>Article</span>
                                            </a>
                                        </li>
                                        <li ui-sref-active="active" ng-hide="formData.edit_post">
                                            <a ui-sref="post-add.link">
                                                <span>Link</span>
                                            </a>
                                        </li>
                                        <li ui-sref-active="active" ng-hide="formData.edit_post">
                                            <a ui-sref="post-add.status">
                                                <span>Status</span>
                                            </a>
                                        </li>
                                        <li ui-sref-active="active" ng-hide="formData.edit_post" ng-if="checkUrlParam!='questions'">
                                            <a ui-sref="post-add.question">
                                                <span>Question</span>
                                            </a>
                                        </li>
                                    </ul>
                                </div>
                                <div class="col-md-4 col-lg-4 hidden-sm hidden-xs" ng-if="!isFullContainer()">
                                    <span class="cardPreviewTxt">Card Preview</span>
                                </div>
                            </div>
                            <div class="clearfix"></div>
                        </div>
						<div class="postNewMainsection">
							<div ui-view></div>
						</div>
                    </div>
                </div>
            </div>
        </div>
    </div>
</div>