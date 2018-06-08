<div class="container-fluid padding-25 sm-padding-10 no-heroImage profileContainer exploreTest">
    <div class="whiteAreaCont">
        <div  ng-if="showHeroImage" class="jumbotron" id="hero-image" data-pages="parallax" data-social="cover"
              style="background:url(/assets/img/profiles/cityImage.jpg);">
            <div class="cover-photo" style="transform: translateY(0px);">
                <a role="button" id="hero-close-button"><i class="pg-close close-btn"></i></a>
                <h2 class="m-l-400 hero-image-text">Dive Deep to your Passion</h2>
                <a class="btn btn-default btn-rounded m-l-500" role="button">Get Started</a>
                <a class="btn btn-default btn-rounded m-l-10" role="button">Learn more</a>
            </div>
        </div>
        <div id="categoryheader" ng-if="mainCategories.length > 0">
            <h5 class="catHeadTTL" style="padding-top:16px !important; text-transform: capitalize;">Select Category</h5>
            <div class="clearfix">
                <div class="pickCatrgorySliderSec loaded">
                    <div class="smallLoaderOwl"></div>
                    <div class="pickCatrgorySlider">
                        <div style="display:block;" class="hide640">
                            <owl-carousel class="owl-carousel categorySlider"
                                          id="owl-demo"
                                          data-options="owlCarouselOptions"
                                          data-hide-selector=".smallLoader2 "
                                          data-owl-time="1000"
                                          data-hide-time="3000"
                                          style="display:none;">
                                <div class="item"
                                     owl-carousel-item
                                     hide-loader
                                     ng-repeat="category in mainCategories">
                                    <a class="catgBx"
                                       ng-class="::{'catselected': selectedCategory.value == category.value}"
                                       ng-style="::{'background-color': category.color}"
                                       ng-click="changeCategory(category)"
                                       data-category="{{::category.value}}"
                                    >
                                        <div class="catgBxImage" style="background:url({{ category.featured_post_image ? 'uploads/post/thumbs/' + category.featured_post_image : '' }}) no-repeat;">
                                            <div class="catgttl">
                                                <span>{{ ::category.category_name }}</span>
                                            </div>
                                        </div>
                                    </a>
                                </div>
                            </owl-carousel>
                        </div>

                        <div style="display:block;">
                            <div class="flickitySlider" style="display:none;">
                                <div class="item"
                                     ng-class="{'sl': selectedCategory.value == category.value}"
                                     flickity-item
                                     ng-repeat="category in mainCategories">
                                    <a class="catgBx"
                                       ng-style="{'background-color': category.color}"
                                       ng-click="changeCategory(category)">
                                        <div class="catgBxImage" style="background:url({{ category.featured_post_image ? 'uploads/post/thumbs/' + category.featured_post_image : '' }}) no-repeat;">
                                            <div class="catgttl">
                                                <span>{{ ::category.category_name }}</span>
                                            </div>
                                        </div>
                                    </a>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </div>
        <div class="categoryHeadingRow exploreCatgHead" id="categoryheader" style="padding-bottom:0;">
            <h5 class="selcatgTTL" style="text-transform: capitalize;">
                <span>Category:</span> {{ selectedCategory.category_name }} 
            </h5>
            <label class="followBtn" ng-if="selectedCategory.value != 'all'">
                <a ng-click="catFollowUnfollow(selectedCategory.value)">
                    <!-- <span>FOLLOW THIS CATEGORY</span> -->
                    <span ng-if="!followStatus">FOLLOW</span>
                    <span class="ico" ng-if="followStatus">FOLLOWING</span>
                </a>
            </label>

        </div>
    </div>

    <div class="panel newpanel" style="margin-top:0px !important; border:none;">
        <div class="whiteAreaCont" style="padding-top:14px;">
            <div class="scrollTabOuter">
                <div class="scrollTab scrollTabCalc">
                    <div class="smallLoader_feature"></div>
                    <div class="nav nav-tabs nav-tabs-simple innerTab inlineView explorTabSlider">
                        <div class="itm" ng-class="{'active': (post_type == 'recent')}">
                            <a ng-click="changeTab('recent')" >
                                <div class="txt">
                                    <img src="assets/pages/img/featured-recent-icon.png" alt=""/>Recent
                                </div>
                            </a>
                        </div>
                        <div class="itm" ng-class="{'active': (post_type == 'trending')}">
                            <a ng-click="changeTab('trending')" >
                                <div class="txt">
                                    <img src="assets/pages/img/featured-trending-icon.png" alt=""/>Trending
                                </div>
                            </a>
                        </div>
                        <div class="itm" ng-class="{'active': (post_type == 'popular')}">
                            <a ng-click="changeTab('popular')" >
                                <div class="txt">
                                    <img src="assets/pages/img/featured-popular-icon.png" alt=""/>Popular
                                </div>
                            </a>
                        </div>
                        <div class="itm" ng-class="{'active': (post_type == 'top_channel')}">
                            <a ng-click="changeTab('top_channel')" >
                                <div class="txt">
                                    <img src="assets/pages/img/featured-topchanel-icon.png" alt=""/>Top Channel
                                </div>
                            </a>
                        </div>
                        <div class="itm" ng-class="{'active': (post_type == 'location')}">
                            <a ng-click="changeTab('location')" class="tb5" >
                                <div class="txt">
                                    <img src="assets/pages/img/featured-location-icon.png" alt=""/>Location
                                </div>
                            </a>
                        </div>
                    </div>
                </div>
            </div>
        </div>

        <div ng-hide="hidePostFilter">
            <!-- post type nav desk -->
            <post-type-nav navtype="desk"></post-type-nav>
            <!-- post type nav mobile -->
            <post-type-nav navtype="mob"></post-type-nav>
        </div>

        <div style="display:block; position:relative;">
            <div class="loaderImage"></div>
            <!--  Tab container   -->
            <div ng-include="currentTabUrl"></div>
        </div>
    </div>
</div>

<!-- SHARE Modal -->
<sharepost-card></sharepost-card>
<!-- Details Modal-->
<!-- <postcard-modal></postcard-modal>  -->

<!-- DELETE POST CARD MODAL -->
<delete-post-modal></delete-post-modal>

<!-- REPORT POST CARD MODAL -->
<report-post-modal></report-post-modal>
<!-- REPORT COMMENT  MODAL -->
<report-comment-modal></report-comment-modal>

<?php /*<script type="text/javascript" src="<?php echo asset('assets/plugins/bx-slider/flickity.pkgd2.js'); ?>"></script> */?>

