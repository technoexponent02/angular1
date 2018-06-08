<style type="text/css">
	body{background:#f6f9fa !important;}
	.header{background:#fff !important;}
</style>
<div ng-show="isFullContainer()">
    <div class="loaderImage" ng-class="{'remove': removePostLoader}"></div>
    <!-- Image -->
    <div ng-if="$state.current.name === 'post-add.photo.start'">
        <div class="row">
			<div class="col-sm-12">
                <div class="linkBigTxt">
                    <h2>Place image link here to share</h2>
                </div>
            </div>
            <div class="col-sm-12">
				<div pg-form-group class="form-group form-group-default required">
					<label>Add Image URL</label>
					<input type="text" name="image_url" placeholder="Image URL" 
                        class="form-control needsclick"
						ng-model="formData.image_url"
						<?php /*rs ng-blur="imageUrlChange()"*/?>
                        hit-enter="processLink()"
						ng-required="!formData.image_file"/>
				</div>
				<label
					ng-show="(!postForm.image.$pristine || !postForm.image_url.$pristine) && !(postForm.image.$valid || postForm.image_url.$valid)"
					class="error">Please upload a image or enter image url.
				</label>
            </div>
        </div>
        <div class="row">
            <div class="col-sm-12 align-center">
                <button type="button"
                        ng-click="processLink()"
                        ng-disabled="postForm.image.$invalid && postForm.image_url.$invalid"
                        class="btn btn-primary btn-cons">
                    <span>Next</span>
                </button>
                <div class="clearfix"></div>
            </div>
        </div>
		<div class="row">
            <div class="col-sm-12">
				<div class="newUploader">
					<label class="uploadFileNew needsclick" for="post_image">
						<input class="needsclick" type="file" name="image" id="post_image" accept='image/*'
							   file-select="onFileSelect($files)"
							   onchange="angular.element(this).scope().fileChanged(this)"
							   ng-model="formData.image_file"
							   valid-file
							   ng-required="!formData.image_url"
						/>
						<span class="uploadIconArea needsclick">
							<img src="assets/pages/img/photo-upload-icon.png" alt="" class="needsclick"/>
							<p class="needsclick">Snap picture<br/><span>or upload image</span></p>
						</span>
					</label>
				</div>
			</div>
		</div>
    </div>
    <!-- Video -->
    <div ng-if="$state.current.name === 'post-add.video.start'">
        <div class="row">			
			<div class="col-sm-12">
                <div class="linkBigTxt">
                    <h2>Place video link here to share</h2>
                </div>
            </div>
            <div class="col-sm-12">
				<div pg-form-group class="form-group form-group-default required">
					<label>Add Video URL</label>
					<input type="text" name="embed_code" placeholder="Video URL" class="form-control"
						   ng-model="formData.embed_code"
                        <?php /*rs ng-blur="videoUrlChange()"*/?>
						   ng-required="!formData.video_file"/>
				</div>
				<label
					ng-show="(!postForm.video.$pristine || !postForm.embed_code.$pristine) && !(postForm.video.$valid || postForm.embed_code.$valid)"
					class="error">Please upload a video or enter video url.
				</label>
            </div>
        </div>
        <div class="row">
            <div class="col-sm-12 align-center">
                <button type="button"
                        ng-click="processLink()"
                        ng-disabled="postForm.video.$invalid && postForm.embed_code.$invalid"
                        class="btn btn-primary btn-cons">
                    <span>Next</span>
                </button>
                <div class="clearfix"></div>
            </div>
        </div>
		<div class="newUploader">
			<label class="uploadFileNew">
				<input type="file" name="video" id="post_image" accept='video/*'
					   file-select="onFileSelect($files)"
					   onchange="angular.element(this).scope().fileChanged(this)"
					   ng-model="formData.video_file"
					   valid-file
					   ng-required="!formData.embed_code"
				/>
				<span class="uploadIconArea">
					<img src="assets/pages/img/video-upload-icon.png" alt=""/>
					<p>Record now<br/><span>or upload video</span></p>
				</span>				
			</label>
		</div>
    </div>
    <!-- Link -->
    <div ng-if="$state.current.name === 'post-add.link.start'">
        <div class="row">
            <div class="col-sm-12">
                <div class="linkBigTxt">
                    <h2>Place any link here you wish to share</h2>
                </div>
            </div>
            <div class="col-sm-12">
                <div pg-form-group class="form-group form-group-default required"
                     ng-class="{ 'has-error' : postForm.link.$invalid && !postForm.link.$pristine}"
                     area-required="true">
                    <label>Link</label>
                    <input type="text" name="link"
                           ng-model="formData.link"
                           class="form-control"
                           ng-class="{ 'error' : postForm.link.$invalid && !postForm.link.$pristine }"
                           placeholder="<?php echo url('post-link'); ?>"
                           hit-enter="startProcessLink(postForm)"
                           required>
                </div>
                <label id="link-error" class="error" for="link"
                       ng-show="!postForm.link.$pristine && postForm.link.$invalid"
                >Link field is required.
                </label>
            </div>
        </div>
        <div class="row">
            <div class="col-sm-12 align-center">
                <button type="button"
                        ng-click="processLink()"
                        ng-disabled="postForm.link.$invalid"
                        class="btn btn-primary btn-cons">
                    <span>Next</span>
                </button>
                <div class="clearfix"></div>
            </div>
        </div>
    </div>
    <!-- Article -->
    <div ng-if="$state.current.name === 'post-add.article.start'">
        <div class="row">
            <div class="col-sm-12">
                <div class="linkBigTxt">
                    <h2>Paste your article's link to start import
                        <small>You must own the copyright of the blog content in order to import</small>
                    </h2>
                </div>
            </div>
            <div class="col-sm-12">
                <div pg-form-group class="form-group form-group-default"
                     ng-class="{ 'has-error' : postForm.link.$invalid && !postForm.link.$pristine}"
                     area-required="true">
                    <label>Link</label>
                    <input type="text" name="link"
                           ng-model="formData.link"
                           class="form-control"
                           ng-class="{ 'error' : postForm.link.$invalid && !postForm.link.$pristine }"
                           placeholder="<?php echo url('post-link'); ?>"
                           hit-enter="startProcessLink(postForm)">
                </div>
            </div>
        </div>
        <div class="row">
            <div class="col-sm-12 align-center">
                <button type="button"
                        class="btn btn-success btn-cons"
                        ng-click="writeOwnArticle()"
                        ng-disabled="formInfo.importProgress">
                    <span>Write my own article</span>
                </button>
                <button type="button"
                        class="btn btn-primary btn-cons"
                        ng-click="processLink()"
                        ng-disabled="formInfo.importProgress">
                    <span>Next</span>
                </button>
            </div>
        </div>
    </div>

</div>

<div ng-if="!isFullContainer()">
    <div class="row">
        <div class="col-sm-12">
            <div pg-form-group class="form-group form-group-default"
                 ng-class="{ 'has-error' : postForm.caption.$invalid && !postForm.caption.$pristine }">
                <label>Caption</label>
                <textarea type="text"
                          name="caption" onkeypress="removeHash()"
                          class="form-control lg"
                          ng-class="{ 'error' : postForm.caption.$invalid && !postForm.caption.$pristine }"
                          ng-model="formData.caption"
                          ng-model-options="{ updateOn: 'default blur', debounce: { default: 100, blur: 0 } }"
                          placeholder="What's on your mind ?"></textarea>
            </div>
            <label id="Caption-error" ng-show="!postForm.caption.$pristine && postForm.caption.$invalid" class="error"
                   for="caption">Caption field is required.</label>
        </div>
    </div>
    <?php /*
        <div class="row">
        <div class="col-sm-12">
            <div pg-form-group class="form-group form-group-default form-group-default-select"
                 ng-class="{ 'has-error' : !formData.selectedMainCategory && !postForm.category.$pristine }"
                 >
                <label>CATEGORY</label>
                <select class="form-control" name="category"
                        ng-model="formData.selectedMainCategory"
                        ng-options="category.category_name for category in mainCategories track by category.id"
                        ng-change="formData.selectedMainCategory ? update(formData.selectedMainCategory) : clearSubCategories()"
                        title="Category">
                    <option value="">Select Category</option>
                </select>
            </div>
            <label ng-show="!postForm.category.$pristine && postForm.category.$invalid" class="error" for="category">Category
                field is required.</label>
        </div>
    </div>
    */ ?>
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
    <?php /*
    <div class="row" ng-if="subCategories.length > 0">
        <div class="col-sm-12">
            <div pg-form-group class="form-group form-group-default form-group-default-select"
                 ng-class="{ 'has-error' : !formData.selectedSubCategory && !postForm.sub_category.$pristine }">
                <label>SUB CATEGORY</label>
                <select name="sub_category" class="form-control" title="Subcategory"
                        ng-model="formData.selectedSubCategory"
                        ng-options="category.category_name for category in subCategories  track by category.id"
                    <?php /*ng-required="subCategories.length > 0"*//* ?>>
                    <option value="">Select Subcategory</option>
                </select>
            </div>
            <label ng-show="!postForm.sub_category.$pristine && postForm.sub_category.$invalid" class="error"
                   for="sub_category">Sub category field is required.</label>
        </div>
    </div>
*/?>
    <div class="row">
        <div class="col-sm-12">
            <div pg-form-group class="form-group form-group-default required"
                 ng-class="{ 'has-error' : postForm.title.$invalid && !postForm.title.$pristine }" area-required="true">
                <label>Title</label>
                <input type="text" name="title" ng-model="formData.title" placeholder="Enter title" class="form-control"
                       required/>
            </div>
            <label ng-show="!postForm.title.$pristine && postForm.title.$invalid" class="error" for="title">Title field
                is required.</label>
        </div>
    </div>

    <?php /*if ($post_type != 'photo' && $post_type != 'video' || $edit_post) {*/ ?>
        <div class="row" ng-show="formInfo.showGeneralUploader">
            <div class="col-sm-12">
            <div class="uploadBlock inline">
                <div class="uploadBlockLeft upload-holder">
                    <span class="orTxt">
                        Or
                    </span>
                    <label class="uploadFile">
                        <?php if ($post_type == 'video') { ?>
                            <input type="file" name="video" id="post_image" accept='video/*'
                                   file-select="onFileSelect($files)"
                                   onchange="angular.element(this).scope().fileChanged(this)"
                                   ng-model="formData.video_file"
                                   valid-file
                                   ng-required="!formData.embed_code"
                            />
                            <i class="fa fa-film" aria-hidden="true"></i>
                        <?php } else if ($post_type == 'article' || $post_type == 'link') { // Image or video but not mandatory. ?>
                            <div ng-if="!formInfo.showVideoInput">
                                <input type="file" name="image" id="post_image" accept='image/*'
                                       file-select="onFileSelect($files)"
                                       onchange="angular.element(this).scope().fileChanged(this)"
                                       ng-model="formData.image_file"
                                       valid-file
                                />
                                <i class="fa fa-picture-o" aria-hidden="true"></i>
                            </div>
                            <div ng-if="formInfo.showVideoInput">
                                <input type="file" name="video" id="post_image" accept='video/*'
                                       file-select="onFileSelect($files)"
                                       onchange="angular.element(this).scope().fileChanged(this)"
                                       ng-model="formData.video_file"
                                       valid-file/>
                                <i class="fa fa-film" aria-hidden="true"></i>
                            </div>
                        <?php } else /*if ($post_type != 'photo')*/ { ?>
                            <input type="file" name="image" id="post_image" accept='image/*'
                                   file-select="onFileSelect($files)"
                                   onchange="angular.element(this).scope().fileChanged(this)"
                                   ng-model="formData.image_file"
                                   valid-file
                                   ng-required="!formData.image_url"
                            />
                            <i class="fa fa-picture-o" aria-hidden="true"></i>
                        <?php } ?>
                        <span>Upload</span>
                    </label>
                </div>
                <div class="uploadBlockRight">
                    <div class="uploadBlockRightMiddle">
                        <div pg-form-group
                             class="form-group form-group-default <?php echo ($post_type == 'article' || $post_type == 'link') ? '' : 'required'; ?>">
                            <?php if ($post_type == 'video') { ?>
                                <label>Add Video URL</label>
                                <input type="text" name="embed_code" placeholder="Video URL" class="form-control"
                                       ng-model="formData.embed_code"
                                       ng-change="videoUrlChange()"
                                       ng-model-options="{updateOn: 'blur'}"
                                       ng-required="!formData.video_file"/>
                            <?php } else if ($post_type == 'article' || $post_type == 'link') { // Image or Video but not mandatory. ?>
                                <div ng-if="!formInfo.showVideoInput">
                                    <label>Add Image URL</label>
                                    <input type="text" name="image_url" placeholder="Image URL" class="form-control"
                                           ng-model="formData.image_url"
                                           ng-change="imageUrlChange()"
                                           ng-model-options="{updateOn: 'blur'}"/>
                                </div>
                                <div ng-if="formInfo.showVideoInput">
                                    <label>Add Video URL</label>
                                    <input type="text" name="embed_code" placeholder="Video URL" class="form-control"
                                           ng-model="formData.embed_code"
                                           ng-change="videoUrlChange()"
                                           ng-model-options="{updateOn: 'blur'}"
                                           ng-required="!formData.video_file"/>
                                </div>
                            <?php } else /*if ($post_type != 'photo')*/ { ?>
                                <label>Add Image URL</label>
                                <input type="text" name="image_url" placeholder="Image URL" class="form-control"
                                       ng-model="formData.image_url"
                                       ng-change="imageUrlChange()"
                                       ng-model-options="{updateOn: 'blur'}"
                                       <?php /*ng-required="!formData.image_file" */?>/>
                            <?php } ?>
                        </div>
                    </div>
                </div>
            </div>
        </div>
        </div>
    <?php /*}*/ ?>

    <?php /* if ($post_type == 'photo') {*/ ?>
        <div class="row" ng-show="formData.imageSrc">
            <div class="col-sm-12">
                <div class="form-group form-group-default" style="border:none !important; padding:0;">
                    <div class="imgUploadPreview">
                        <a ng-click="clearImage()" class="del"><img src="/assets/pages/img/cross.png"/></a>
                        <img ng-src="{{ formData.imageSrc ? formData.imageSrc : defaultImage }}"  alt="image placeholder">
                    </div>
                </div>
            </div>
        </div>
    <?php /*} else if($post_type == 'video') {*/ ?>
        <div class="row" ng-show="formData.videoSrc">
            <div class="col-sm-12">
                <div class="form-group form-group-default" style="border:none !important; padding:0;">
                    <div class="imgUploadPreview"
                        ng-if="uploadPreviewType === 'video'" >
                        <a ng-click="clearVideo()" class="del"><img src="/assets/pages/img/cross.png"/></a>
                        <video class="videoTag" controls loop  playsinline webkit-playsinline>
                            <source ng-src="{{formData.videoSrc}}">
                        </video>
                    </div>
                    <div ng-if="uploadPreviewType === 'embed'" 
                        class="imgUploadPreview uploadLocVidview uploadVidview">
                        <a ng-click="clearVideo()" class="del"><img src="/assets/pages/img/cross.png"/></a>
                        <iframe
                            ng-src="{{ formData.videoSrc }}"
                            webkitallowfullscreen mozallowfullscreen allowfullscreen>
                        </iframe>
                    </div>
                </div>
            </div>
        </div>
    <?php /*}*/ ?>

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
    <div class="row">
        <div class="col-sm-12">
            <button type="button"
                    ng-if="$state.current.name === 'post-add.article.general' || $state.current.name === 'post-edit.article.general'"
                    ng-click="goToContent()"
                    ng-disabled="disableGeneralBtn(postForm)"
                    class="btn btn-primary btn-cons pull-right">
                <span>Next</span>
            </button>
            <button type="button"
                    ng-click="goToAdvance()"
                    ng-if="$state.current.name !== 'post-add.article.general' && $state.current.name !== 'post-edit.article.general'"
                    ng-disabled="disableGeneralBtn(postForm)"
                    class="btn btn-primary btn-cons pull-right">
                <span>Next</span>
            </button>
            <div class="clearfix"></div>
        </div>
    </div>
</div>
<script>
 function removeHash()
    {
       /*****this portion block for in mobile on keypress is not working.. */
        if(event.keyCode == '35') {
                     event.preventDefault();
             }
        
        
    }
</script>    
