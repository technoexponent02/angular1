<div class="row postRow">
    <div class="col-md-12 col-lg-12">
        <div class="post_card_container">
            <div class="blockContentRow inline"
                    id="blckCntParent"
                    infinite-scroll="fetchPostData()"
                    <?php /*infinite-scroll-disabled="busy"*/?>
                    infinite-scroll-distance="2"
                    masonry
                    preserve-order
                    item-selector=".blockContent"
                    reload-on-resize
                    <?php /*column-width="masonryColumnWidth"*/?>
                    masonry-options="{ transitionDuration: 0 }"
                    ng-cloak>
                    <div class="blockContent masonry-brick" ng-repeat="post in commonData.allPosts">
                        <post-card></post-card>
						<?php /*<any ng-if="post.post_type==1 || post.post_type==5" 
                            in-view="lineInView($index, $inview, $inviewInfo,post)"></any>*/?>
                    </div>
            </div>
            <h2 class="noPostAvailable" ng-if="noPostForTag"> No {{ post_type }} post.</h2>
            <h2 class="noPostAvailable" ng-if="!noPostForTag && noMorePost"> No more {{ post_type }} post.<br/><a ui-sref="explore" class="findPeoplebtn" ng-if="$state.current.name === 'feed'">Find people to follow</a></h2>
			
        </div>
    </div>
</div>
<postcard-modal></postcard-modal> 
<!-- REPORT COMMENT  MODAL -->
<report-comment-modal></report-comment-modal>
