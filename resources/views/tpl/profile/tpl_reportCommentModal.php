<div class="reporPopupVW">
	<div class="deletePostBody">					   
		<span class="txt">Report Comment</span>
        <div style="display:block;position:relative;">
            <div class="reportCommentModalLoader" ng-show="reportCommentModalLoader"></div>
            <a class="followBtn" ng-click="doCommentReport(comment_id,5)">
                <span class="ico" ng-show="report_comment_ids.indexOf(5)==-1">Spam comment</span>
                <span class="ico reported" ng-show="report_comment_ids.indexOf(5)!=-1">Spam comment reported (click to cancel)</span>
            </a>
            <a class="followBtn" ng-click="doCommentReport(comment_id,6)">
                <span class="ico" ng-show="report_comment_ids.indexOf(6)==-1">Inappropriate comment</span>
                <span class="ico reported" ng-show="report_comment_ids.indexOf(6)!=-1">Inappropriate comment reported (click to cancel)</span>
            </a>
        </div>
	</div>
</div>
