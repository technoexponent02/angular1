<div ng-if="post.allow_comment==1" id="commentfilter{{post.id}}"
    class="discussionCont">
    <div class="feedesubHeader commentfilter" style="margin-bottom:0px;">
        <div class="left">
			<span class="feedModalSmTtl">
			{{post.postParentComment |  thousandSuffix }} {{ post.postParentComment > 1 ? 'Responses' : 'Response' }}
			</span>
        </div>
        <div class="middle">
			<span class="postOnlineUsers">
				<i class="fa fa-circle"></i>
				{{totalPeopleHere}} online
			</span>
        </div>
        <div class="right">
            <ul class="inline  detailsCommentTab">
                <li>
                    <a href="#"
                       ng-class="{'active': 1 == activeItem}"
                       ng-click="sortByComments(1,post.id);"
                       id="bestComments">Best</a>
                </li>
                <li>
                    <a href="#"
                       ng-class="{'active': 2 == activeItem}"
                       ng-click="sortByComments(2,post.id);"
                       id="newcomments">All ({{ post.postParentComment |
                        thousandSuffix }})</a>
                </li>
            </ul>
        </div>
        <div class="clearfix"></div>
    </div>
    <div class="modalMsgCont">
        <div class="modalAllMsg">

            <div class="postMainCommentFld" ng-if="user.guest== 0">
				<div class="userIsTyping" ng-if="typingText">{{ typingText }}</div>
                <div class="userStatusRow mainCommentField"
                     style="border-bottom:none;">
                    <div class="userStatusImage {{user.user_color}}" ng-if="!user.profile_image">
                        <a ui-sref="account({ username: user.username })">
                           <span class="txt">{{user.first_name.charAt(0)}}</span>
                        </a>
                    </div>
					
					<div class="userStatusImage" ng-if="user.profile_image">
                        <a ui-sref="account({ username: user.username })" class="mainUsr" style="background:url({{user.profile_image}}) no-repeat;"></a>
                    </div>
					
					
                    <div class="userStatusInfo">
                        <form name="commentForm" novalidate>
                            <div class="userStatusCommentBox">
                                <textarea  class="modalMsg"
                                    id="parentMsg" name="message"
                                    placeholder="Type message..."
                                    ng-model="message"
                                    type-tracking
                                    required></textarea>
                                <button
                                    ng-disabled="commentForm.message.$invalid"
                                    class="btn btn-success"
                                    id="sendParent"
                                    ng-click="postComment(post.id,post.child_post_id)">
                                    <sapn ng-if="showSendBtn"
                                          class="snd">Send
                                    </sapn>
                                    <sapn ng-if="showSendingBtn"
                                          class="snd sndng">Sending
                                    </sapn>
                                </button>
                            </div>
                        </form>
                    </div>
                    <div class="clearfix"></div>
                </div>
            </div>
            <div class="profileCommentBoxTop new" style="padding:0;">
                <div class="modalMsgLoader"
                     ng-if="showCommentLoader == 1"></div>
                <div class="split-view modal-split-view">
                    <div class="split-list">
                        <div class="list-view">
                            <div class="list-view-wrapper">
                                <div class="list-view-group-container">
                                    <!-- <div ng-include="'tpl.comment'"></div> -->
                                    <div ng-include="'tpl.discussion'"></div>

                                    <div ng-show="postTotalComment==0"
                                         class="noDiscussion">No
                                        Discussion
                                        Added</div>
                                </div>
                            </div>
                            <div class="more_comment_div">
                                <a class="loadMoreBtn"
                                   style="width:200px;"
                                   ng-if="activeItem==1"
                                   ng-show="postTotalComment>commentLimitTo"
                                   ng-click="moreComments(post.id,activeItem)">SHOW
                                    ALL COMMENTS </a>
                                <a class="loadMoreBtn"
                                   style="width:200px;"
                                   ng-if="activeItem==2"
                                   <?php /* ng-show="(postTotalComment>commentLimitTo)"*/?>
                                   ng-show="post.postParentComment>commentLimitTo"
                                   ng-click="moreComments(post.id,activeItem)">SHOW
                                    ALL COMMENTS </a>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>
</div>