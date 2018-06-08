<div class="row">
    <div class="col-sm-12" >
        <div pg-form-group class="form-group form-group-default required" ng-class="{ 'has-error' : postForm.caption.$invalid && !postForm.caption.$pristine }">
            <label>Question</label>
            <!-- <textarea type="text" name="caption" id="caption"
                    ng-model="formData.caption"
                    class="form-control lg" ng-class="{ 'error' : postForm.caption.$invalid && !postForm.caption.$pristine }"
                    placeholder="what's your question ?" onkeypress="angular.element(this).scope().question(this)"  onfocusout="angular.element(this).scope().compactText(this)" ng-change="onEditChange()" focus-me="focusInput" 
                    required >
			</textarea> -->
            <textarea type="text" name="caption" id="caption"  onkeypress="removeHash()" onkeyup="editQuestion()" onfocusout="compactText()"
                    ng-model="formData.caption"
                    class="form-control lg" ng-class="{ 'error' : postForm.caption.$invalid && !postForm.caption.$pristine }"
                    placeholder="what's your question ?"  
                    required >
			</textarea>
        </div>
        <!-- <label id="Caption-error" ng-show="!postForm.caption.$pristine && postForm.caption.$invalid" class="error" for="caption">Question field is required.</label> -->
             <label id="captionError"  class="error" style="display:none;">Question field is required.</label>
    </div>
</div>

    <div class="row">
        <div class="col-sm-12">
            <div class="form-group form-group-default form-group-default-select customSelbox">
                <label>CATEGORY</label>
                <span class="form-control">{{ formData.selectedMainCategory ? formData.selectedMainCategory.category_name : "Select Category" }}</span>
            </div>
			<div class="customSelectListCont">
				<div class="selectOvrlay"></div>
				<div class="customSelectList">						
					<span class="tl">
						Category
						<a href="javascript:void(0);" class="cls"><img src="/assets/pages/img/cross-black.png"/></a>
					</span>
					<ul>		
						<li ng-click="clearMainCategories()">Select Category</li>
						<li ng-repeat="category in mainCategories track by category.id"
                            ng-click="updateCategory(category)">{{ category.category_name }}
                        </li>
					</ul>
				</div>
			</div>
        </div>
    </div>
    <div class="row" ng-if="subCategories.length > 0">
        <div class="col-sm-12">
            <div class="form-group form-group-default form-group-default-select customSelbox">
                <label>SUB CATEGORY</label>
                <span class="form-control">{{ formData.selectedSubCategory ? formData.selectedSubCategory.category_name : "Select Sub Category" }}</span>
            </div>
            <div class="customSelectListCont">
                <div class="selectOvrlay"></div>
                <div class="customSelectList">
					<span class="tl">
						Sub Category
						<a href="javascript:void(0);" class="cls"><img src="/assets/pages/img/cross-black.png"/></a>
					</span>
                    <ul>
                        <!-- <li ng-click="clearSubCategories()">Select Category</li> -->
                        <li >Select Category</li>
                        <li ng-repeat="category in subCategories track by category.id"
                            ng-click="updateSubCategory(category)">{{ category.category_name }}
                        </li>
                    </ul>
                </div>
            </div>
        </div>
    </div>


<div class="row">
    <div class="col-sm-12">
        <div class="uploadBlock inline">
            <div class="uploadBlockLeft upload-holder">
                <span class="orTxt">
                    Or
                </span>
                <label class="uploadFile">
                    <input type="file" name="file" id="post_image" accept='image/*,video/*'
                            file-select="onFileSelect($files)"
                            onchange="angular.element(this).scope().fileChanged(this)"
                            ng-model="formData.upload_file"
                            valid-file
                             />
                    <i class="fa fa-file" aria-hidden="true"></i>
                    <span>Upload</span>
                </label>
            </div>
            <div class="uploadBlockRight">
                <div class="uploadBlockRightMiddle">
                    <div pg-form-group class="form-group form-group-default">
                            <label>Add URL</label>
                            <input type="text" name="upload_url" placeholder="URL" class="form-control"
                                   ng-model="formData.upload_url"
                                   ng-change="uploadUrlChange()"
                                   ng-model-options="{updateOn: 'blur'}"
                            />
                    </div>
                </div>
            </div>
        </div>
    </div>
</div>
<div class="row" ng-show="formData.imageSrc || formData.videoSrc">
    <div class="col-sm-12">
        <div class="form-group form-group-default" style="border:none !important; padding:0;">
			<div ng-show="formData.imageSrc" class="imgUploadPreview" ng-if="uploadPreviewType === 'image'">
				<a ng-click="clearFile()" class="del"><img src="/assets/pages/img/cross.png"/></a>
				<img ng-src="{{ formData.imageSrc ? formData.imageSrc : defaultImage }}"  alt="image placeholder">
			</div>
			<div ng-if="formData.videoSrc && uploadPreviewType === 'video'" 
                class="imgUploadPreview">
                <a ng-click="clearFile()" class="del"><img src="/assets/pages/img/cross.png"/></a>
				<video class="videoTag" controls loop  playsinline webkit-playsinline>
                    <source ng-src="{{formData.videoSrc}}">
                </video>
			</div>
            <div ng-if="formData.videoSrc && uploadPreviewType === 'embed'" 
                class="imgUploadPreview uploadLocVidview uploadVidview">
                <a ng-click="clearFile()" class="del"><img src="/assets/pages/img/cross.png"/></a>
                <iframe
                    ng-src="{{ formData.videoSrc }}"
                    webkitallowfullscreen mozallowfullscreen allowfullscreen>
                </iframe>
            </div>
        </div>
    </div>
</div>


    <div class="row" ng-if="$state.current.name !== 'post-add.article.general' && $state.current.name !== 'post-edit.article.general'">
        <div class="col-sm-12">
            <div pg-form-group class="form-group form-group-default">
                <label>Short Description</label>
                <textarea name="short_description"
                          class="form-control lg"
                          ng-model="formData.short_description"
                          placeholder="Briefly describe your story as short as possible"
                ></textarea>
            </div>
        </div>
    </div>


<div style="position:relative;">
	<span class="fieldCross" ng-click="resetLocationData()">
		<img src="assets/pages/img/delete-field-icon.png" alt=""/> Clear
        <!-- <i class="fa fa-close"></i> -->
    </span>
	<div class="row" ng-init="initializeMap()">
		<div class="col-sm-12">
			<div pg-form-group class="form-group form-group-default">
				<label>Location</label>
				<div class="locInput nwlocInput">
					<span class="lodr" ng-if="showLocLoader"></span>
					<input name="location" id="location" type="text"
						ng-model="formData.location"
						placeholder="{{ placeHolderMsg }}"
                           class="form-control">
					   <a href="" class="questionButtn btn btn-success btn-cons btn-xs pull-right" ng-click="getGeoLocation()" ng-if="getLocationButtonStatus==true">Get my location</a>
				</div>

				<input type="hidden" name="lat" id="lat" ng-model="formData.lat">
				<input type="hidden" name="lon" id="lon" ng-model="formData.lon">
			</div>
			<label ng-show="!postForm.location.$pristine && postForm.location.$invalid" class="error" for="title">Location field is required.</label>
		</div>        
	</div>
</div>

<div class="row">
    <div class="col-sm-12">
        <div class="form-group form-group-default noBorder " style="overflow:visible;">
			<label>Topics <small>(Maximum of 5)</small></label>
			<div class="typefield">
                <!-- <input class="form-control" id="searchText" type="text" placeholder="Type here..." style="height:34px;"  autocomplete="off" spellcheck="false" ng-model="searchText"  ng-model-options='{ debounce: 1000 }' ng-change="liveSearch(searchText,1)" ng-keydown="$event.keyCode === 13 && addSearchTag(searchText)" /> -->

                <input class="form-control" id="searchText" type="text" placeholder="Type here..." style="height:34px;"  autocomplete="off" spellcheck="false" ng-model="searchText"    ng-keydown="$event.keyCode === 13 && addSearchTag(searchText)"  />

                <div class="typefieldList">
                    <ul style="display:block; padding:0; margin:0;" >
                        
                        <li style="display:block;"  ng-click="addSearchTag(searchText)" > <span  class="tagName" id="defaultTag"> {{ searchText}} </span> </li>
                        <li class="nw" ng-repeat="tag in fetchSearchTags" style="display:block;" ng-click="addSearchTag(tag.tag_text)" >
							<span ng-bind-html="tag.tag_text  | tagreplace" class="tagName" ng-if="!tag.question" ></span>
							<span ng-bind-html="tag.question | tagreplace" class="tagName" ng-if="tag.question"  ></span>
							<span class="userUNm" ng-if="tag.posts_count > 0 ||  tag.users_count > 0" >
								{{tag.posts_count > 1 ? tag.posts_count+' posts' : tag.posts_count+' post'}}
								<span class="srchfollowCountDiv"></span>{{tag.users_count > 1 ? tag.users_count+' followers' : tag.users_count+' follower'}}
							</span>
							<span class="nwbt" ng-if="tag.posts_count==0 &&  tag.users_count== 0" >		{{'new'}}</span>                            
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
    <!-- <div class="col-sm-12">
        <div pg-form-group class="form-group form-group-default noBorder">
            <label>Topics <small>(Maximum 5)</small></label>            
            <?php /* Number of tags: {{tags.getNumberOfTags()}} */?>
        </div>
    </div> -->
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



<div class="row">
    <div class="col-sm-12">
        <div class="collectionshare">
            <!-- <div class="con">
                <label class="saveCollection checkradioarea"> -->
                    <input type="hidden" class="js-switch"
                           ng-init="formData.allow_comment = !formData.edit_post ? true : formData.allow_comment"
                            ui-switch="{color: '#6d5cae', size: 'small'}"
                            data-switchery="true"
                            ng-model="formData.allow_comment"
                            />
                    <!-- <span class="names switchTtl">Allow comment</span> -->
                <!-- </label>
            </div>
            <div class="con">
                <label class="saveCollection checkradioarea"> -->
                    <input type="hidden" class="js-switch"
                           ng-init="formData.allow_share = !formData.edit_post ? true : formData.allow_share"
                            ui-switch="{color: '#6d5cae', size: 'small'}"
                            ng-model="formData.allow_share"
                            />
                    <!-- <span class="names switchTtl">Allow share</span> -->
                <!-- </label>
            </div> -->

            <div class="con">
                <label class="saveCollection checkradioarea">
                    <input type="checkbox" class="js-switch"
                           ng-init="formData.ask_anonymous = !formData.edit_post ? false : formData.ask_anonymous"
                            ui-switch="{color: '#6d5cae', size: 'small'}"
                            ng-model="formData.ask_anonymous"
                            />
                     <span class="names switchTtl">Ask Anonymously</span> 
                </label>
            </div>

        </div>
    </div>
</div>
<div class="row" ng-if="privacies.length > 0">
    <div class="col-sm-4">
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
</div>
<div class="row">
    <div class="col-sm-12">
        <button type="button" id="PostingSubmit" 
                class="btn btn-primary btn-cons pull-right"
                ng-disabled="disableStatusSubmit(postForm)" ng-click="processPostForm()">
                <span>SUBMIT</span>
        </button>
    </div>
</div>

<script>

function ucfirst(str,force){
          str=force ? str.toLowerCase() : str;
          return str.replace(/(\b)([a-zA-Z])/,
                   function(firstLetter){
                      return   firstLetter.toUpperCase();
                   });

      
     }

function removeHash()
{
   /*****this portion block for in mobile on keypress is not working.. */
    // if(event.keyCode == '35') {
    //              event.preventDefault();
    //      }
    // var text= $('#caption').val();
    
        
}

function editQuestion() {

     
    
    var text= $('#caption').val();
            

/******add this code for in mobile onkeypress not working in mobile. */
    if(text.search("#")>-1)
    {
        
        var hashpos=text.search("#");
        text=text.replace(/#/g ,'');
        $('#caption').val(text);
        
        input = document.getElementById("caption");
        input.focus();
        input.setSelectionRange(hashpos,hashpos);
    }
/******add this code for in mobile onkeypress not working in mobile. */
   
                
  

    $('#PostingSubmit').prop('disabled', false);     
         if(text.slice(-1)!='?' && text!='')
        {
        
        
                text=ucfirst($('#caption').val(),true) ;
                         
                text=text +'?';   
                $('#caption').val(text);
                var pos=$('#caption').val().length-1;
               //console.log(pos);
                input = document.getElementById("caption");
                input.focus();
                input.setSelectionRange(pos,pos);
        }
        else if(text=='?')
        {
            $('#caption').val('');  
            $('#PostingSubmit').prop('disabled', true); 
            $('#captionError').show();
        }

       
          
        
        //console.log($('#caption').val().length);
        if($('#caption').val().length!=0)
        {
            
            $('#captionError').hide();
        }

        

}

function compactText()
{
    
    var text= $('#caption').val();
    /********* For change capitalize all first letter of questions start **********/

        //     var re = /(^|[.!?]\s+|[.!?])([a-z])/g;
        // 	var text = text.replace(re, function (m, $1, $2) {
        // 		return $1 + $2.toUpperCase() 
        //   });
    /********* For change capitalize all first letter of questions end*************/

    text= text.replace(/\s+(\?)/, "$1");
   
    $('#caption').val(text);

}
</script>

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