 <!--  DELETE POST CARD MODAL  -->
<div class="modal fade" id="deletePostModal" role="dialog">
    <div class="modal-dialog postDelete">
        <div class="modal-content">
            <div class="modal-body">
                <a href="#" class="modalCloseBtn responsiveNone" data-dismiss="modal">
                    <img src="/assets/pages/img/cross.png"/>
                    <img src="/assets/pages/img/cross-black.png" class="black"/>
                </a>                        
                <div class="deletePostBody">
                    <span class="tl">Delete Post </span>
                    <a class="followBtn" ng-click="deleteMyPost(mypostid)" eat-click-if="disableClick">
                        <span class="ico">Delete my post </span>
                    </a>
                </div>              
            </div>
        </div>
    </div>
</div>
