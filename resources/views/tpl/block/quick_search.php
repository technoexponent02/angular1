<!-- START OVERLAY -->
<div ng-controller="QuickSearchCtrl" class="overlay searchFullcontainer" style="display:none;" pg-search webscrolling2>
    <!-- BEGIN Overlay Content !-->
    <div class="overlay-content has-results">
        <!-- BEGIN Overlay Header !-->
        <div class="container-fluid">
            <!-- BEGIN Overlay Logo !-->
			<div class="searchAreaLogo">
				<img class="overlay-brand" src="assets/img/logo.png" alt="logo" data-src="assets/img/logo.png" ui-jq="unveil" data-src-retina="assets/img/logo_2x.png" height="22">
			</div>
            <!-- END Overlay Logo !-->
            <!-- BEGIN Overlay Close !-->
            <a href="" class="close-icon-light overlay-close text-black fs-16" id="searchCloseBtn" data-search="closeButton">
                <!-- <i class="pg-close"></i> -->
				<img src="assets/pages/img/cross-black.png" alt=""/>
            </a>
            <!-- END Overlay Close !-->
        </div>
        <!-- END Overlay Header !-->
        <div class="container-fluid">
            <!-- BEGIN Overlay Controls !-->
				<label class="needsclick popupsearch">
					<input data-search="searchField" id="overlay-search" class="no-border overlay-search bg-transparent needsclick" placeholder="Search" autocomplete="off" spellcheck="false" ng-model="search.query" hit-enter="goToSearch()">
					<span class="minCharacters">min 3 characters</span>
				</label>
				<div class="srchPgSrchBtn">
					<button class="btn btn-default btn-lg btn-rounded"
                         ng-click="goToSearch()"
                         ng-disabled="!(search.query.length >= search_min_len)">
						Search
						<span class="srchBtnloader" ng-show="showSearchInputLoader"></span>
					</button>
				</div>
				<ul class="searchTagList">
					<li ng-repeat="search in topSearches">
						<a ng-click="goToTopSearch(search.keyword)">{{::search.keyword}}</a>
					</li>
				</ul>
				<!-- END Overlay Controls !-->
        </div>
    </div>
    <!-- END Overlay Content !-->
</div>
<!-- END OVERLAY -->