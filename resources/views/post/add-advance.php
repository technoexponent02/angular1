<div style="position:relative;">
    <span class="fieldCross" ng-click="resetLocationData()">
        <!-- <i class="fa fa-close"></i> -->
		<img src="assets/pages/img/delete-field-icon.png" alt=""/> Clear
    </span>    
    <div class="row" ng-init="initializeMap()">
        <div class="col-sm-12">
            <div pg-form-group class="form-group form-group-default ">
                <label>Location</label>
				<div class="locInput nwlocInput">
					<span class="lodr" ng-if="showLocLoader"></span>
					<!-- <input name="location" id="location" type="text" ng-model="formData.location" placeholder="{{ placeHolderMsg }}" class="form-control" ng-init="getGeoLocation()"> -->
                    <input name="location" id="location" type="text" ng-model="formData.location" placeholder="{{ placeHolderMsg }}" class="form-control" >
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
        <div pg-form-group class="form-group form-group-default">
            <label>Source</label>
            <input type="text"
                    name="source"
                    placeholder="Source url"
                    ng-if="formData.embed_code"
                    ng-model="formData.embed_code"
                    class="form-control"
                    disabled 
                    >
            <input type="text"
                    name="source"
                    placeholder="Source url"
                    ng-if="!formData.embed_code && formData.link"
                    ng-model="formData.source"
                    <?php /*ng-init="formData.source = !formData.edit_post ? formData.link : formData.source"*/?>
                    class="form-control"
                    ng-disabled="disableSourceInput()"
                    >                    
            <input type="text"
                    name="source"
                    placeholder="Source url"
                    ng-if="!formData.link && !formData.embed_code"
                    ng-model="formData.source"
                    class="form-control"
                    ng-disabled="formInfo.type == 'link'"
                    >
        </div>
    </div>
</div>
<div class="row">
    <div class="col-sm-12">
        <button type="button"
                ng-click="goToSocial()"
                ng-disabled="disableAdvanceBtn(postForm)"
                class="btn btn-primary btn-cons pull-right">
            <span>Next</span>
        </button>
        <button type="button" 
                ng-if="$state.current.name !== 'post-add.article.advance' && $state.current.name !== 'post-edit.article.advance'"
                ng-click="goToGeneral()"
                class="btn btn-success btn-cons pull-right">
            <span>Previous</span>
        </button>
        <button type="button"
                ng-if="$state.current.name === 'post-add.article.advance' || $state.current.name === 'post-edit.article.advance'"
                ng-click="goToContent()"
                class="btn btn-success btn-cons pull-right">
            <span>Previous</span>
        </button>
        <div class="clearfix"></div>
    </div>
</div>
