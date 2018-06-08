<div class="modal fade" id="reportPostModal" role="dialog">
    <div class="modal-dialog postDelete">
        <div class="modal-content">
            <div class="modal-body">
                <a href="#" class="modalCloseBtn responsiveNone" data-dismiss="modal">
                    <img src="<?php echo asset('assets/pages/img/cross.png');?>" alt=""/>
                    <img src="<?php echo asset('assets/pages/img/cross-black.png');?>" alt="" class="black"/>
                </a>                        
                <div class="deletePostBody">
                    <span class="tl">Report Post </span>
                    <div style="display:block;position:relative;">
                        <div class="reportPostModalLoader" ng-show="reportPostModalLoader"></div>
                        <a class="followBtn" ng-click="doPostReport(mypostid,1);">
                            <span class="ico" ng-show="report_post_ids.indexOf(1)==-1">Spam post</span>
                            <span class="ico reported" ng-show="report_post_ids.indexOf(1)!=-1">Spam post reported (click to cancel)</span>
                        </a>
                        <a class="followBtn" ng-click="doPostReport(mypostid,2);">
                            <span class="ico" ng-show="report_post_ids.indexOf(2)==-1">This post shouldn't show here</span>
                            <span class="ico reported" ng-show="report_post_ids.indexOf(2)!=-1">This post shouldn't show here reported (click to cancel)</span>
                        </a>
                        <a class="followBtn" ng-click="doPostReport(mypostid,3);">
                            <span class="ico" ng-show="report_post_ids.indexOf(3)==-1">Invade copyright</span>
                            <span class="ico reported" ng-show="report_post_ids.indexOf(3)!=-1">Invade copyright reported (click to cancel)</span>
                        </a>
                        <a class="followBtn" ng-click="doPostReport(mypostid,4);">
                            <span class="ico" ng-show="report_post_ids.indexOf(4)==-1">Inappropriate post</span>
                            <span class="ico reported" ng-show="report_post_ids.indexOf(4)!=-1">Inappropriate post reported (click to cancel)</span>
                        </a>
                    </div>

                </div>              
            </div>
        </div>
    </div>
</div>
