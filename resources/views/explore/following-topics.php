<style type="text/css">
	.header{background:#f6f9fa !important;}
	.header{background:#fff !important;}
	.topicBox .btTtl{
	    word-wrap: break-word;
	}
	.topicBox .btTtl.modifyText{font-size:14px;}
</style>
<div class="container-fluid padding-25 sm-padding-10" style="padding-top:0px !important;">
	<div class="panel newpanel m-t-10">
		<div class="categoryHeadingRow lg_tl">
			<h5>Following Topics</h5>
		</div>
		<div class="followingTopics">

		<div ng-if="errorMsg!=null" style="display:block;text-align: center; padding-top:20px;">
			<div class="alert alert-danger" role="alert"  style="display:inline-block; width:600px; text-align: center; max-width: 100%;">
				<strong>Error: </strong>{{errorMsg}}
			</div>
		</div>

			<div class="row">
				<div class="col-lg-2 col-md-3 col-sm-4 col-xs-6" ng-repeat="tag in followedCategoriesTags" >
					
					<div class="topicBox" style="background:url('{{tag.featureImage}}') no-repeat; height:199px;" ng-init="placeCategoryName = (tag.type == 'category' || tag.type == 'tag') ? tag.name : tag.placeUrl" >
						<div class="ovr">
							<div class="unfollowBtn" data-toggle="tooltip" ui-jq="tooltip" data-original-title="Unfollow">
								<span ng-click="followUnfollow(placeCategoryName, tag.type);"><img src="assets/pages/img/cross.png" alt=""/></span>
							</div>
							<label class="followBtn"  ng-click="followUnfollow(placeCategoryName, tag.type);">
								<span class="">FOLLOW</span>
							</label>
							<a href="{{tag.pageUrl}}" ><div class="btTtl" ng-class="{ modifyText:tag.text.length >40 }">{{tag.type == 'category' || tag.type == 'place' ? tag.name : tag.question_tag==''? tag.text.length >50 ? tag.text.slice(0, 50)+'...' : tag.text : tag.question_tag.length >50 ? tag.question_tag.slice(0, 50)+'...?' : tag.question_tag }}</div></a>
						</div>
					</div>					
				</div>
			</div>
		</div>
	</div>
</div>

<!-- POST CARD  MODAL -->
<postcard-modal></postcard-modal>
<!-- REPORT COMMENT  MODAL -->
<report-comment-modal></report-comment-modal>
<!-- SHARE Modal -->
<sharepost-card></sharepost-card>
<!-- DELETE POST CARD MODAL -->
<delete-post-modal></delete-post-modal>
<!-- REPORT POST CARD MODAL -->
<report-post-modal></report-post-modal>
<!-- PROMPT SINGIN BOX -->
<prompt-signin-box></prompt-signin-box>