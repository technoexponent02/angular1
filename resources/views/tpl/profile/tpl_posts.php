<div class="scrollTabOuter">
	<div class="scrollTab scrollTabCalc">
		<div class="smallLoader_feature"></div>
		<div class="profileExploreNav">
			<div class="exploreDropdown">
				<span class="navTl">
					<span>{{allPostFilter ? 'All Post' : 'Original Post'}}</span> <i class="fa fa-caret-down"></i>
				</span>
				<ul>
					<li>
						<a ng-class="{'sel':allPostFilter}" ng-click="allPostFilter=true">
							<i class="fa fa-check"></i> All Post
						</a>
					</li>
					<li>
						<a ng-class="{'sel':!allPostFilter}" ng-click="allPostFilter = false">
							<i class="fa fa-check"></i> Original Post
						</a>
					</li>
				</ul>
			</div>
			<div class="nav nav-tabs nav-tabs-simple innerTab inlineView explorTabSlider">					
				<div class="itm" ng-class="{'active': (post_view_type == 'recent')}">
					<a ng-click="changeTab('recent')" class="tb3" href="javascript:void(0)">
						<div class="txt">
							<img src="assets/pages/img/featured-recent-icon.png" alt=""/>
							<div class="nm">
								<span class="thin">Recent</span>
								<span class="strong">Recent</span>
							</div>
						</div>
					</a>
				</div>
				<div class="itm" ng-class="{'active': (post_view_type == 'popular')}">
					<a ng-click="changeTab('popular')" class="tb2" href="javascript:void(0)">
						<div class="txt">
							<img src="assets/pages/img/featured-popular-icon.png" alt=""/>
							<div class="nm">
								<span class="thin">Popular</span>
								<span class="strong">Popular</span>
							</div>
						</div>
					</a>
				</div>
			</div>
		</div>
	</div>
</div>

<!-- post type nav desk -->
<post-type-nav navtype="desk"></post-type-nav>

<!-- post type nav mobile -->
<post-type-nav navtype="mob"></post-type-nav>

<div class="row postRow">
    <div class="loaderImage2 remove"></div>
    <div class="col-md-12">
        <div class="post_card_container">
            <div class="blockContentRow inline"
            		id="blckCntParent"
            		infinite-scroll="loadMore(param)"
            		infinite-scroll-disabled="busy"
                    infinite-scroll-distance="2"
                    masonry
                    preserve-order
                    item-selector=".blockContent"
                    reload-on-resize
                    <?php /*column-width="350"*/ ?>
                    masonry-options="{ transitionDuration: 0 }"
                    ng-cloak
                    >
                <div class="blockContent masonry-brick" 
                	ng-repeat="post in commonData.allPosts | filter: (allPostFilter ? undefined : { orginal_post_id: '!' }) track by post.cardID">
                    <post-card></post-card>
					<?php /*
					<any ng-if="post.post_type==1 || post.post_type==5"  
						in-view="lineInView($index, $inview, $inviewInfo,post)">
					</any>
                    */?>
                </div>                           
            </div>
             <h2 class="noPostAvailable" ng-if="is_showMorePost == 1">No {{ post_view_type }} post.</h2>
             <h2 class="noPostAvailable" ng-if="is_showMorePost != 1 && is_show == 1">No more {{ post_view_type }} post</h2>
        </div>
    </div>
    
</div>