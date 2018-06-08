<div ng-click="closeOverLayout()"
     class="subOverlay subOverlaysh"
     id="closeOverLayout{{post.child_post_id}}"
     style="display:none;">
</div>
<div class="sub otherSubsh" id="otherSub{{post.child_post_id}}" style="display:none;">
    <ul>
        <li class="withA" ng-if="(openFrom!='PD')">
            <a target="_blank" href="{{post.post_url}}">
                <img src="/assets/pages/img/log_out.png" alt="" class="ico"/> View
            </a>
        </li>
        <li ng-class="{ active : post.isDownvote == 'Y'}"
            upvotes="doDownVotes(post.id,post.child_post_id,openFrom)">Downvote
        </li>
        <li data-toggle="modal" ng-click="openReportPostModal(post.child_post_id);"
            ng-if="isShowPostReportLink" data-target="#reportPostModal">Report This Post
        </li>
        <li class="withA" ng-if="(post.child_post_user_id==user.id) && !post.orginal_post_id && showEditPostLink">
            <a ng-if="post.post_type==1" ui-sref="post-edit.photo.general({ id: post.child_post_id })"> Edit Post</a>
            <a ng-if="post.post_type==2" ui-sref="post-edit.video.general({ id: post.child_post_id })"> Edit Post</a>
            <a ng-if="post.post_type==3" ui-sref="post-edit.article.general({ id: post.child_post_id })"> Edit Post</a>
            <a ng-if="post.post_type==4" ui-sref="post-edit.link.general({ id: post.child_post_id })"> Edit Post</a>
            <a ng-if="post.post_type==5" ui-sref="post-edit.status.start({ id: post.child_post_id })"> Edit Post</a>
            <a ng-if="post.post_type==6" ui-sref="post-edit.question.start({ id: post.child_post_id })"> Edit Post</a>
        </li>
        <li data-toggle="modal" ng-click="openDeletePostModal(post.child_post_id);"
            ng-if="(post.child_post_user_id==user.id)"
            data-target="#deletePostModal">Delete
        </li>
    </ul>
</div>