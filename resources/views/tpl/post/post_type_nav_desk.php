<div class="profileCatgSlider hide640" >	
	<div class="profileMiddleHeader profilesubmenu">
		<owl-carousel class="owl-carousel categorySlider newExploreTabSliderMenu"
					  	data-options="navTypeOwlOptions"
					  	 >
			<div class="item" owl-carousel-item
				 ng-repeat="type in postTypeNavItems"
				 <?php /*ng-if="!($index==1 && $state.current.name=='explore')"*/?>
				 >
				<a ng-click="getPost(type.value);"  ng-class="{'active': type.value == card_post_type}">{{ type.name }} </a> 
			</div>
		</owl-carousel>
	</div>
</div>