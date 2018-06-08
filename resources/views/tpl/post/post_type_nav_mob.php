<div id="carouselIsShowing" class="flkMobExplore">
	<div class="postingform exploreTabRow loaded hide641 profilePgexploreTab">
		<div class="exploreTab newExploreTab">
			<div class="explotabList">
				<div class="itemBxCont" style="display:none;">
					<div class="itemBx" flickity-item
						 ng-repeat="type in postTypeNavItems"
						 <?php /*ng-if="!($index==1 && $state.current.name=='explore')"*/?>
						 >
						<a ng-click="getPost(type.value);" ng-class="{'active': type.value == card_post_type}">{{ type.name }}</a>
					</div>
				</div>
			</div>
		</div>
	</div>
</div>