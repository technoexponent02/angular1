<div class="row postRow">
    <div class="col-md-12 col-lg-12">
        <div class="post_card_container" id="post_card_container">
            <div class="blockContentRow inline" id="blckCntParent"
                angular-grid="commonData.allPosts"
                ag-options="agOptions"
                ag-id="posts"
                ag-infinite-scroll="fetchPostData()"
            >
                <div class="blockContent showNow" 
                    ng-repeat="post in commonData.allPosts track by $index">
                    <test-post-card></test-post-card>
                </div>
            </div>
            <!-- <h2 class="noPostAvailable exploreTestLoader" ng-show="busy"></h2> -->
            <div class="postScrollLoader" ng-show="busy"></div>

            <h2 class="noPostAvailable" ng-if="noPostForTag"> No {{ post_type }} post.</h2>
            <h2 class="noPostAvailable" ng-if="!noPostForTag && noMorePost"> No more {{ post_type }} post.</h2>
        </div>
        <div style="display: none;">
            <div ng-repeat="post in loadEarlyPosts track by $index">
                <div ng-if="post.user.profile_image">
                    <img ng-src="uploads/profile/thumbs/{{post.user.profile_image}}" />
                </div>
                
                <div ng-if="::(post.image)">
                    <img ng-src="uploads/post/thumbs/{{post.image}}" />
                </div>
            </div>
        </div>
    </div>
</div>
<postcard-modal></postcard-modal> 
<!-- REPORT COMMENT  MODAL -->
<report-comment-modal></report-comment-modal>
