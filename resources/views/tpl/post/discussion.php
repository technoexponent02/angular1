<ul class="no-padding" ng-if="comments.length>0">
	<li class="item padding-15 highLight" 
		ng-repeat="c in comments | limitTo : (!c.parent_id ? commentLimitTo : '') | orderBy:'id'"
		id="pc{{c.id}}">
		<!-- <pre>
			{{c | json}}
		</pre> -->
		<a class="thumbnail-wrapper d32 circular bordered b-warning" ng-if="c.user.thumb_image_url"
           ui-sref="account({ username: c.user.username })"
           style="background:url({{c.user.thumb_image_url}}) no-repeat;"></a>
		<a class="thumbnail-wrapper d32 circular bordered b-warning {{c.user.user_color}}" ng-if="!c.user.thumb_image_url"
           ui-sref="account({ username: c.user.username })" >
			<span class="txt">{{c.user.first_name.charAt(0)}}</span> 
		</a>

		<div class="inline m-l-15">
			<a class="recipients no-margin hint-text small" ui-sref="account({ username: c.user.username })">
				{{c.user.first_name}} {{c.user.last_name}}
			</a>
			<span ng-if="c.user.id == post.created_by && post.ask_anonymous==0" class="btn btn-danger btn-xs btn-rounded m-l-10 ownerBtn">Owner</span>				
			<div class="datetime" am-time-ago="c.created_at | amUtc | amLocal"></div>
		</div> 
		<div class="modalComments">
			<p class="subject no-margin" ng-bind-html="c.message"></p>
			<div ng-if="user.guest==0">
				<div class="commentUpvote">
					<a class="upVt" href="#" id="upvoteClass_{{c.id}}" ng-click="commentUpAndDownVote(c.id,1,post.child_post_id);" 
							ng-class="{active : c.isUpvote== 'Y'}">
								<i></i>
					</a>
					<span>
						{{(c.upvotes - c.downvotes)>0 ? '+' + (c.upvotes-c.downvotes) :(c.upvotes-c.downvotes) | thousandSuffix }} 
					</span>
					<a href="javascript:void(0);" id="downvoteClass_{{c.id}}" 
						ng-click="commentUpAndDownVote(c.id,2,post.child_post_id);"
						ng-class="{active : c.isDownvote== 'Y'}">
					Downvote
					</a>
					<div class="rply">
						&nbsp;-&nbsp;&nbsp;
						<a ng-click="showReplyBox(c.id,c.post_id,activeItem)"> 
                            <img src="assets/pages/img/reply-icon.png" alt="reply-icon.png"/>
							<span ng-if="c.count_child>0">({{c.count_child}})</span>
						</a>
						<div class="comntInlineModal" ng-if="c.user_id!=user.id">
							&nbsp;-&nbsp;&nbsp;<a class="reportClk" ng-click="openReportCommentModal(c.id);">
                                <img src="assets/pages/img/report-icon.png" alt="report-icon"/>
                            </a>
							<report-comment-modal></report-comment-modal>
						</div>
						<div class="comntInlineModal" ng-if="c.user_id==user.id">
							&nbsp;-&nbsp;&nbsp;<a href="javascript:void(0);" class="reportClk" >
                                    <img src="assets/pages/img/delete-comment-icon.png" alt="delete-comment-icon" style="width:13px;"/>
                                </a>
							<div>
								<div class="reporPopupVW">
									<div class="deletePostBody">					   
										<span class="txt">Delete Comment</span>
										<a class="followBtn"  
											ng-click="deleteComments(c.id,c.parent_id,c.post_id,activeItem)">
											<span class="ico">Yes</span>
										</a>
										<a class="followBtn" ng-click="doCommentReport(comment_id,6);">
											<span class="ico">No</span>
										</a> 
									</div>
								</div>
							</div>

						</div>
					</div>
				</div>
			
				<div  class="discussionTexrarea" id="displayBlock_{{c.id}}"> 				 
					<div style="display:none;" id="cmmtextAreaBlock{{c.id}}" class="cmmtextAreaType" >
					<form name="commentForm{{c.id}}" id="commentfrm{{c.id}}">
						<div class="userStatusRow">
							<div class="userStatusImage" ng-if="user.profile_image">
								<a href="#" class="replyUsr" style="background:url({{user.profile_image}}) no-repeat;">
								</a>
							</div>
							<div class="userStatusImage {{user.user_color}}" ng-if="!user.profile_image">
								<a href="#"> 
									<span class="txt">{{user.first_name.charAt(0)}}</span>
								</a>
							</div>
							<div class="userStatusInfo">
								<div class="userStatusCommentBox">
									
									 <input type="hidden"  name="parent_id" value="{{c.id}}"> 
									 <textarea  name="message" 
									 	class="modalMsg" 
									 	placeholder="Reply to {{c.user.first_name}} {{c.user.last_name}}"  
									 	<?php /*ng-keyup="addBrtag(this.value)"*/ ?>
									 	type-tracking
									 	ng-model="reply_message"
									 	required>
									 	</textarea>
									  <input type="hidden" name="post_id"  value="{{c.post_id}}" />
									   <input type="hidden" name="child_post_id"  value="{{post.child_post_id}}" />
									 
									  <button 
										ng-disabled="commentForm{{c.id}}.message.$invalid" 
										class="btn btn-success" 
										id="sendBtnLoading{{c.id}}" 
										ng-click="postReplyComment(c.id)">
											<span id="send{{c.id}}" class="snd">Send</span>
											<span id="sending{{c.id}}" style="display:none;" class="snd sndng">Sending</span>
									</button>
								</div>
							</div>
						</div>
					</form>
					</div>
				</div>
			</div>
			
			<div ng-if="user.guest!=0">
				<div class="commentUpvote">
					<a class="upVt" ng-click="redirecToLogin();">
						<i></i>
					</a>
					<span>
						{{ (c.upvotes-c.downvotes)>0 ? '+'+(c.upvotes-c.downvotes) :(c.upvotes-c.downvotes) | thousandSuffix }} 
					</span>
					<a ng-click="redirecToLogin();">
					Downvote
					</a>
					<div class="rply">
						&nbsp;-&nbsp;&nbsp;<a ng-click="redirecToLogin();"> 
							 <img src="assets/pages/img/reply-icon.png" alt=""/>
							<span ng-if="c.count_child>0">({{c.count_child}})</span>
						</a> 
						<div class="comntInlineModal">
							&nbsp;-&nbsp;&nbsp;<a ng-click="redirecToLogin();"><img src="assets/pages/img/report-icon.png" alt=""/></a>
						</div>
						<div class="comntInlineModal">
							&nbsp;-&nbsp;&nbsp;<a class="reportClk" ng-click="redirecToLogin();"><img src="assets/pages/img/delete-comment-icon.png" alt="" style="width:13px;"/></a>
						</div>
					</div>
				</div>
			</div>			
		</div>
			
		<div class="clearfix"></div>
		
		<div ng-show="c.show" ng-switch on="c.child_comment.length > 0">
			<div ng-switch-when="true">
			  <!-- <div ng-init="comments = c.child_comment;" ng-include="'tpl.comment'"></div> -->
                <div ng-init="comments = c.child_comment;" ng-include="'tpl.discussion'"></div>
			</div>
		</div>
		
	</li>
</ul>

