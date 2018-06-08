<div class="row">
    <div class="col-sm-12">
        <div class="form-group form-group-default" id="editorPlusVisible">
            <label>Content</label>
            <textarea name="content" 
                        froala="froalaOptions"
                        ng-model="formData.content"
                        ng-change="formInfo.lp_desc=''"
                        required></textarea>
        </div>
    </div>
</div>

<div class="row fullscreenBtnHide">
    <div class="col-sm-12">
        <button type="button"
                ng-click="goToAdvance()"
                ng-disabled="disableContentBtn(postForm)"
            class="btn btn-primary btn-cons pull-right">
            <span>Next</span>
        </button>
        <button type="button" ng-click="goToGeneral()" class="btn btn-success btn-cons pull-right"> <span>Previous</span></button>
        <div class="clearfix"></div>
    </div>
</div>