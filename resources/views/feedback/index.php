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
                        <h2>Send us your valuable feedback
                            <span><small>Let us know how can we improve or reporting bug here.</small></span>
                        </h2>
                    </div>
                    <form name="myForm"  method="POST" role="form" class="" ng-submit="submitForm(myForm.$valid)" novalidate>
                        <div class="row">
                            <div class="col-sm-12">
                                <div class="form-group form-group-default required">
                                    <label for="topic">Topic</label>
                                    <input type="text" name="topic" ng-model="topic" ng-keyup="topic.length > 0 ? topicRequired=false : topicRequired=true" class="form-control" placeholder="Topic" id="topic" required/>
                                    <label class="error" ng-show="topicRequired && submitted">Topic is
                                        required</label>
                                </div>
                                <div class="form-group form-group-default required">
                                    <label for="description">Description</label>
                                    <textarea class="form-control" name="description" placeholder="Type here..."
                                              ng-model="feedbackMessage" ng-keyup="feedbackMessage.length > 0 ? feedbackMsgRequired=false : feedbackMsgRequired=true" required></textarea>
                                    <label class="error" ng-show="feedbackMsgRequired && submitted">Description is required.</label>
                                </div>
                            </div>
                        </div>
                        <div class="row">
                            <div class="col-sm-12 align-center">
                                <button type="submit" class="btn btn-primary btn-cons" ng-disabled="buttonText!='Send'">{{ buttonText }}</button>
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
