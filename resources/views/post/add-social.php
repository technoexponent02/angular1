<div class="row">

    <div class="col-sm-12">
        <div class="form-group form-group-default noBorder " style="overflow:visible;">
			<label>Topics <small>(Maximum of 5)</small></label>
			<div class="typefield">
                <!-- <input class="form-control" id="searchText" type="text" placeholder="Type here..." style="height:34px;"  autocomplete="off" spellcheck="false" ng-model="searchText"  ng-model-options='{ debounce: 1000 }' ng-change="liveSearch(searchText,1)" ng-keydown="$event.keyCode === 13 && addSearchTag(searchText)" /> -->

                 <input class="form-control" id="searchText" type="text" placeholder="Type here..." style="height:34px;"  autocomplete="off" spellcheck="false" ng-model="searchText"    ng-keydown="$event.keyCode === 13 && addSearchTag(searchText)"  />


                <div class="typefieldList">
                    <ul style="display:block; padding:0; margin:0;" >
                        
                        <li style="display:block;"  ng-click="addSearchTag(searchText)" > <span  class="tagName" id="defaultTag"> {{ searchText }} </span> </li>
                        <li class="nw" ng-repeat="tag in fetchSearchTags" style="display:block;" ng-click="addSearchTag(tag.tag_text)" >
							<span ng-bind-html="tag.tag_text | tagreplace" class="tagName" ng-if="!tag.question" ></span>
							<span ng-bind-html="tag.question | tagreplace" class="tagName" ng-if="tag.question"  ></span>
							<span class="userUNm" ng-if="tag.posts_count > 0 ||  tag.users_count > 0" >{{tag.posts_count > 1 ? tag.posts_count+' posts' : tag.posts_count+' post'}}
								<span class="srchfollowCountDiv"></span>{{tag.users_count > 1 ? tag.users_count+' followers' : tag.users_count+' follower'}}
							</span>
							<span class="nwbt" ng-if="tag.posts_count==0 &&  tag.users_count== 0" >{{'new'}}
							</span>                            
                        </li>
                        
                    </ul>
                    <div class="searchLoader" ng-show="showSearchLoader"></div>
                </div>
                <input type="hidden" id="page" value='2'>
                <input type="hidden" id="fetch_next_data_status" value='true'>
			</div>
        </div>
		<div class="form-group form-group-default noBorder newtagCont">
			<js-tag js-tag-options="jsTagOptions" ng-init="initJsTags()"></js-tag>
			<?php /* Number of tags: {{tags.getNumberOfTags()}} */?>
		</div>
    </div>
   <!--  <div class="col-sm-12">
        <div pg-form-group class="form-group form-group-default noBorder">
            <label>Topics <small>(Maximum 5)</small></label>
            
            <?php /* Number of tags: {{tags.getNumberOfTags()}} */?>
        </div>
    </div>-->
    <div class="col-sm-12">
        <div class="form-group form-group-default noBorder">
            <label><small>Recently used</small></label>
            <div class="allCustomTags">
                <a ng-click="addTag(tag)" ng-repeat="tag in recentTags" id="recent_tag_{{ tag.id }}" ng-init="isActive=checkActiveTag(tag)"  ng-class="{active : isActive}">{{tag.tag_text }}</a>
            </div>
        </div>
    </div>
    <div class="col-sm-12">
        <div class="form-group form-group-default noBorder">
            <label><small>Following Topic</small></label>
            <div class="allCustomTags">
                <a ng-click="addTag(tag)" ng-repeat="tag in followingTags" id="following_tag_{{ tag.id }}" ng-init="isActive=checkActiveTag(tag)"  ng-class="{active : isActive}">{{tag.tag_text }}</a>
            </div>
        </div>
    </div>
</div>
<?php /*
<div class="row">
    <div class="col-sm-12">
        <p class="catagorySubTtl">Share to</p>
        <div class="sharepostbox">
            <div class="checkbox check-primary">
                <input type="checkbox"  ng-model="formData.facebook_connect" connect-social-media="connectFacebook()" name="facebook_connect" value="1" id="facebook">
                <label for="facebook"><i class="fa  fa-facebook" aria-hidden="true"></i> Facebook</label>
            </div>
            <div class="checkbox check-primary">
                <input type="checkbox"  ng-model="formData.twitter_connect" ng-click="connectTwitter();" name="twitter_connect" id="twitter">
                <label for="twitter"><i class="fa fa-twitter" aria-hidden="true"></i> Twitter</label>
            </div>
        </div>
    </div>
</div>
*/ ?>
<div class="row">
    <div class="col-sm-12">
        <div class="collectionshare">
            <div class="con">
                <label class="saveCollection checkradioarea">
                    <input type="checkbox" class="js-switch"
                            ng-init="formData.allow_comment = !formData.edit_post ? true : formData.allow_comment"
                            ui-switch="{color: '#6d5cae', size: 'small'}"
                            data-switchery="true"
                            ng-model="formData.allow_comment"
                            />
                    <span class="names switchTtl">Allow comment</span>
                </label>
            </div>
            <div class="con">
                <label class="saveCollection checkradioarea">
                    <input type="checkbox" class="js-switch"
                           ng-init="formData.allow_share = !formData.edit_post ? true : formData.allow_share"
                            ui-switch="{color: '#6d5cae', size: 'small'}"
                            ng-model="formData.allow_share"
                            />
                    <span class="names switchTtl">Allow share</span>
                </label>
            </div>
            <?php /*
            <div class="con">
                <label class="saveCollection checkradioarea">
                    <input type="checkbox" class="js-switch"
                            ng-init="initCollection()"
                            ng-model="formData.saveCollection"
                            ng-show="!formData.saveToCollection"
                            ng-click="formData.saveToCollection = !formData.saveToCollection"
                            ui-switch="{color: '#6d5cae', size: 'small'}" />
                    <span class="names switchTtl">Save to Collection</span>
                </label>
            </div>
            */ ?>
        </div>
    </div>
</div>
<?php /*
<div class="row" ng-show="formData.saveToCollection" ng-controller="CollectionCtrl as collection">
    <div class="col-sm-12">
        <div class="collectionslist">
            <p>
                <a class="graybtn" ng-show="!collection.formVisible" ng-click="collection.showCollectionForm()">
                    <i class="fa fa-plus"></i> Create A New Collection
                </a>
            </p>

            <div ng-show="collectionFormVisible">
                <div class="form-group form-group-default  required">
                    <label>Collection Title</label>
                    <input type="text" class="form-control" id="collection_name" ng-model="collection.collection_name">
                </div>
                <label id="Caption-error" class="error" for="collection_name"
                        ng-show="collection.collection_nameError"> Collection Title field is required.</label>
                <div class="form-group form-group-default">
                    <label>Collection Description</label>
                    <input type="text" class="form-control" ng-model="collection.collection_text">
                </div>
                <button type="button" ng-click="collection.addCollection()" class="btn btn-primary">Add Collection</button>
            </div>
            <h5>Save to Existing Collection</h5>
            <div class="sharepostbox">
                <div class="checkbox check-primary" ng-repeat="collection in collections">
                    <input type="checkbox" name="collection_id[]" id="chk{{collection.id}}"
                            value="{{collection.id}}"
                            ng-checked="formData.selectedCollection.indexOf(collection.id) > -1"
                            ng-click="toggleCollection(collection.id)"
                    >
                    <label for="chk{{collection.id}}">{{collection.collection_name}}</label>
                </div>
            </div>
        </div>
    </div>
</div>
 */?>
<div class="row">
    <div class="col-sm-4" ng-if="privacies.length > 0">
        <div pg-form-group class="form-group form-group-default form-group-default-select required">
            <label class="">Privacy</label>
            <select class="form-control" name="privacy_id"
                    ng-init="formData.privacy_id = !formData.edit_post ? privacies[0] : formData.privacy_id"
                    ng-model="formData.privacy_id"
                    ng-options="privacy.privacy_name for privacy in privacies track by privacy.id" title="Choose privacy">
                    <?php /*{{privacy_id}}*/?>
            </select>
        </div>
    </div>
    <div class="col-sm-8">
        <button type="button" id="PostingSubmit" 
                class="btn btn-primary btn-cons pull-right" ng-click="processPostForm()">
                <span>SUBMIT</span>
        </button>
        <button type="button" ng-click="goToAdvance()" class="btn btn-success btn-cons pull-right"> <span>Previous</span></button>
        <div class="clearfix"></div>
    </div>
</div>

<script>
    var newlist = '<li>1</li><li>2</li><li>3</li><li>4</li><li>5</li><li>6</li><li>7</li><li>8</li><li>9</li><li>10</li>';
    $('.typefieldList').on('scroll', function(){
        var fetch_next_data_status=$('#fetch_next_data_status').val();
        if(fetch_next_data_status=='true')
        {
                var allListContHeight = $('.typefieldList').innerHeight();
                var allListHeight = $('.typefieldList ul').innerHeight();
                var scrollPos = $(this).scrollTop();
                var newpos = allListHeight - allListContHeight;
                
                if(allListHeight > allListContHeight){
                    if(scrollPos >=  newpos){
                    //  $('.typefieldList ul').append(newlist);
                        page=$('#page').val();
                        var data=$('#searchText').val();
                        angular.element(document.getElementById('searchText')).scope().fetchPostData(data,page);
                        page=parseInt(page) + 1;
                    //alert(page);
                        $('#page').val(page);
                    }
                
                }
        }
    });


        //setup before functions
        var typingTimer;                //timer identifier
    var doneTypingInterval = 1000;  //time in ms, 5 second for example
    var $input = $('#searchText');

     $( "#searchText" ).keyup(function() {
        clearTimeout(typingTimer);

        
        typingTimer = setTimeout(doneTyping,doneTypingInterval);
     });

     //on keydown, clear the countdown 
    $input.on('keydown', function (event) {
        clearTimeout(typingTimer);
       
    });

    //user is "finished typing," do something
    function doneTyping () {
    //do something
    var data=$('#searchText').val();
    angular.element(document.getElementById('searchText')).scope().liveSearch(data,1);
    }


</script>
