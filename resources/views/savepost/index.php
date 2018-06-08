<style type="text/css">
	/* body{background:#f6f9fa !important;} */	
	.header{background:#fff !important;}
</style>
<div class="container-fluid padding-25 sm-padding-10" style="padding-top:0px !important;">
	<div class="panel newpanel m-t-10">
		<div class="categoryHeadingRow lg_tl">
			<h5>Saved Post</h5>
		</div>
		<div ng-hide="hidePostFilter">
			<!-- post type nav desk -->
			<post-type-nav navtype="desk"></post-type-nav>
			<!-- post type nav mobile -->
			<post-type-nav navtype="mob"></post-type-nav>
		</div>

		<div style="display:block; position:relative;">     
			<div class="loaderImage"></div>
			<!--  Tab container   -->
			<div ng-include="currentTabUrl"></div>
		</div>
	</div>
</div>

<!-- SHARE Modal -->
<sharepost-card></sharepost-card>
 <!-- Details Modal-->
<!-- <postcard-modal></postcard-modal>  -->

<!-- DELETE POST CARD MODAL -->
<delete-post-modal></delete-post-modal>

<!-- REPORT POST CARD MODAL -->
<report-post-modal></report-post-modal>
<!-- REPORT COMMENT  MODAL -->
<report-comment-modal></report-comment-modal>

<?php /*<script type="text/javascript" src="<?php echo asset('assets/plugins/bx-slider/flickity.pkgd2.js'); ?>"></script> */?>