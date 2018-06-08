<div class="row">
<div class="col-md-12 col-lg-12">
    <div class="profileBody inline">
        <div class="tab-content featuredBlock" style="text-align:center">
            <!-- <div id="featured_button" class="tab-pane active">
                <div class="collectionPageHeader">
                       <a href="#" class="createBoardBtn">Create A Board</a>
                   </div>
            </div> -->
            <div class="blockContentRow inline collectionrow" id="collectionrow">

                <div class="blockContent collectionBlock" ng-repeat="col in user.collection">
                    <div class="profileCommentBox">
                        <div class="profileCommentBoxTop">
                            <div class="userStatusRow">
                                <div class="catagoryTtl">
                                    <p>{{col.collection_name}}</p>
                                </div>
                            </div>
                            <div class="uploadImage">
                                <div class="collectionitems">
                     	<span>
                        	{{col.collectionPostCount}} Items
                            <small>{{col.collection_text}}</small>
                        </span>
                                </div>
                                <a href="#" ng-if="col.collectionPostCount > 0 && col.post[0].image" style="background:url({{'uploads/post/'+col.post[0].image}}) no-repeat;">
                                </a>
                                <a href="#" ng-if="col.collectionPostCount == 0 || !col.post[0].image" style="background:url(assets/img/profiles/uploadImage1.jpg) no-repeat;">
                                </a>
                            </div>
                            <div class="thumbImageFooter inline">
                                <div class="thumbImage" ng-if="col.collectionPostCount > 1">
                                    <a href="#" style="background:url({{'uploads/post/'+col.post[1].image}}) no-repeat;" ng-if="col.post[1].image">
                                    </a>
                                    <a class="thumbTxt" href="javascript:void(0)" ng-if="!col.post[1].image"><span>{{col.post[1].title}}</span></a>
                                </div>

                                <div class="thumbImage" ng-if="col.collectionPostCount > 2">
                                    <a href="#" style="background:url({{'uploads/post/'+col.post[2].image}}) no-repeat;" ng-if="col.post[2].image">
                                    </a>
                                    <a class="thumbTxt" href="javascript:void(0)" ng-if="!col.post[2].image"><span>{{col.post[2].title}}</span></a>
                                </div>
                                <div class="thumbImage" ng-if="col.collectionPostCount > 3">
                                    <a href="#" style="background:url({{'uploads/post/'+col.post[3].image}}) no-repeat;" ng-if="col.post[3].image">
                                    </a>
                                    <a class="thumbTxt" href="javascript:void(0)" ng-if="!col.post[3].image"><span>{{col.post[3].title}}</span></a>
                                </div>
                                <h2 style="text-align:center;" ng-if="col.collectionPostCount < 1">No post on collection</h2>
                            </div>
                        </div>
                    </div>
                </div>
                <h2 style="text-align:center;" ng-if="userTotalCollection == 0">No collection available</h2>
            </div>
        </div>
    </div>
</div>
</div>