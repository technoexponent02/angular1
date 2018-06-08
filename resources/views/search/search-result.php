<style type="text/css">
	.panel{background:transparent;}
	/* body{background-color:#f6f9fa;} */
	body.wh{background-color:#fff;}
	.header{background:#fff !important;}
</style>
	<div class="container-fluid padding-25 sm-padding-10 no-heroImage profileContainer searchPg">	
		<label class="needsclick popupsearch">
			<input data-search="searchField" id="overlay-search" class="no-border overlay-search bg-transparent needsclick" placeholder="Search" autocomplete="off" spellcheck="false" ng-model="search.query" <?php /* hit-enter="goToSearch()" */?> ng-change="liveSearch()" ng-model-options="{ updateOn: 'default blur', debounce: { default: 1000, blur: 0 } }">
			<span class="minCharacters">min 3 characters</span>
		</label>
	</div>
	<div class="categoryHeadingRow exploreCatgHead sectionHeading" 
		style="padding-bottom:0;" ng-if="search.query">
		<h5 class="selcatgTTL" style="text-transform: none;">
			<span>Search result: </span>{{search.query}}
		</h5>
		<div class="searchPgFollow">
			<a ng-click="tagFollowUnfollow()" class="followBtn" ng-if="::(user.guest==0)">
				 <span ng-if="!tagFollowStatus">FOLLOW</span>
				<span class="ico" ng-if="tagFollowStatus">FOLLOWING</span>
			</a>
		</div>
		<div class="searchPgFollow" ng-if="::(user.guest!=0)">
			<a ng-click="redirecToLogin()" class="followBtn">
				 <span>FOLLOW</span>
			</a>
		</div>
	</div>
	<div class="scrollTabOuter">
		<div class="scrollTab scrollTabCalc">
			<div class="smallLoader_feature"></div>
			<div class="nav nav-tabs nav-tabs-simple innerTab inlineView explorTabSlider"
				data-hide-time="1000"
				data-hide-selector=".smallLoader_feature"
				ng-attr-data-options="{{::(searchTabFlickityOptions | json)}}">
				<div class="itm"
					flickity-item
					ng-repeat="searchtab in searchNavItems"
					ng-class="{'sel': searchtab.id == currentTab}">
					<a href="" ng-click="changeTab($index);">
						<div class="txt no-img">
							{{::searchtab.name}}
						</div>
					</a>
				</div>
			</div>
		</div>
	</div>
	
	<div class="panel" style="min-height: 300px;">
		<!-- post type nav desk -->
		<post-type-nav navtype="desk" ng-show="currentTab=='post'"></post-type-nav>
		<!-- post type nav mobile -->
		<post-type-nav navtype="mob" ng-show="currentTab=='post'"></post-type-nav>

		<div style="display:block; position:relative;">
			<div class="searchLoader" ng-show="showSearchLoader"></div>
			<div class="searchTabarea" id="searchtab1" ng-show="currentTab=='post'">
				<div class="post_card_container">
					<div class="blockContentRow inline searchBlockcontent"
						id="blckCntParent"
						infinite-scroll="fetchPostData('post')"
						<?php /* infinite-scroll-disabled="busy" */?>
						
						infinite-scroll-distance="2"
						masonry
						preserve-order
						<?php /*item-selector=".blockContent"*/?>
					
						reload-on-resize
						
						masonry-options="{ transitionDuration: 0 }"
						ng-cloak>
						<div class="blockContent masonry-brick" ng-repeat="post in commonData.allPosts track by post.id">
							<post-card></post-card>
							<?php /*<any ng-if="post.post_type==1 || post.post_type==5" 
								in-view="lineInView($index, $inview, $inviewInfo,post)"></any>*/?>
						</div>
					</div>
					<h2 class="noPostAvailable" ng-if="noPostForSearch">No post found. <style>body{background-color:#fff;}</style></h2>
					<h2 class="noPostAvailable" ng-if="!noPostForSearch && noMorePost"> No more post.</h2>
				</div>
			</div>
			<div class="searchTabarea" id="searchtab2" ng-show="currentTab=='channel'">
				<div class="channelRow inline searchChanels"
					infinite-scroll="fetchPostData('channel')"
				    infinite-scroll-distance="2">
					<search-channel-card ng-repeat="channel in channelUsers track by channel.id"></search-channel-card>
					<h2 class="noPostAvailable" ng-if="noChannelForSearch"> No {{ currentTab  == 'top_channel' ? 'Channel' : currentTab }}.</h2>
					<h2 class="noPostAvailable" ng-if="!noChannelForSearch && noMoreChannel"> No more {{ currentTab  == 'top_channel' ? 'Channel' : currentTab }}.</h2>
				</div>
			</div>
			
			<div class="searchTabarea" id="searchtab3" ng-show="currentTab=='tag'">
				<div class="channelUsers srchtagList chanelTabDet"
					infinite-scroll="fetchPostData('tag')"
				    infinite-scroll-distance="2">
					<div class="list" ng-repeat="tag in searchTags">
						<a href="/tag/{{tag.tag_name}}" class="userImg" ng-if="!tag.question">
							<span class="tagHas">#</span>
						</a>
						<a href="/questions/{{tag.tag_name}}" class="userImg" ng-if="tag.question">
							<span class="tagHas">#</span>
						</a>
						<div class="area">
							<a href="/tag/{{tag.tag_name}}" class="userNm" ng-if="!tag.question" >#
							<span ng-bind-html="tag.tag_text | highlightNode:searchResults.tag_query_arr"></span>
							</a>
							<a href="/questions/{{tag.tag_name}}" class="userNm" ng-if="tag.question" >#
							<span ng-bind-html="tag.question | highlightNode:searchResults.tag_query_arr"></span>
							</a>
							<span class="userUNm">{{tag.posts_count > 1 ? tag.posts_count+' posts' : tag.posts_count+' post'}}<span class="srchfollowCountDiv"></span>{{tag.users_count > 1 ? tag.users_count+' followers' : tag.users_count+' follower'}}</span>						
							<label class="followBtn" ng-if="user.guest==0">
								<a ng-click="tagFollowUnfollowEach(tag)">
									<span ng-if="tag.isFollow==0">FOLLOW</span>
									<span ng-if="tag.isFollow!=0" class="ico">FOLLOWING</span>
								</a>
							</label>
							<label class="followBtn" ng-if="user.guest!=0">
								<a ng-click="redirecToLogin()">
									<span>FOLLOW</span>
								</a>
							</label>
						</div>
					</div>
					<h2 class="noPostAvailable" ng-if="noTagForSearch"> No {{ currentTab  == 'tag' ? 'Tag' : currentTab }}.</h2>
					<h2 class="noPostAvailable" ng-if="!noTagForSearch && noMoreTagSearch"> No more {{ currentTab  == 'tag' ? 'Tag' : currentTab }}.</h2>
				</div>
			</div>
			<div class="searchTabarea" id="searchtab4" ng-show="currentTab=='location'">
				<div class="channelUsers srchtagList chanelTabDet searchLocList"
					infinite-scroll="fetchPostData('location')"
				    infinite-scroll-distance="2">
					<div class="list" ng-repeat="place in places track by place.id">
						<a href="/place?{{place.place_url}}" class="userImg">
							<span class="loc"><img src="assets/pages/img/search-location-icon.png" alt="location icon"/></span>
						</a>
						<div class="area">
							<a href="/place?{{place.place_url}}" class="userNm">{{::place.place_level_1}}</a>
							<span class="userBio" ng-if="place.place_level_2">
								{{place.place_level_2 + (place.place_level_3 ? ', '+place.place_level_3 : '') }}
							</span>	
							<span class="userUNm ng-binding">
								{{place.users_count > 1 ? place.users_count+' followers' : place.users_count+' follower'}}
								<span class="srchfollowCountDiv"></span>
								{{place.posts_count > 1 ? place.posts_count+' posts' : place.posts_count+' post'}}
							</span>
							<label class="followBtn" ng-if="user.guest==0">
								<a ng-click="placeFollowUnfollow(place)">
									<span ng-if="place.isFollow==0">FOLLOW</span>
									<span ng-if="place.isFollow!=0" class="ico">FOLLOWING</span>
								</a>
							</label>
							<label class="followBtn" ng-if="user.guest!=0">
								<a ng-click="redirecToLogin()">
									<span>FOLLOW</span>
								</a>
							</label>
						</div>
					</div>
					<h2 class="noPostAvailable" ng-if="noLocationForSearch"> No Location.</h2>
					<h2 class="noPostAvailable" ng-if="!noLocationForSearch && noMoreLocationSearch"> No more Location.</h2>
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