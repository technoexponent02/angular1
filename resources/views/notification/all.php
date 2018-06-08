<div class="container-fluid padding-25 sm-padding-10 no-heroImage">
	<h5 class="catHeadTTL lg" style="padding-top:16px !important;">Notifications</h5>
	<div class="notification-panel full" style="">
		<div class="notification-body">
			<div infinite-scroll="loadAllNotifications('notification')"
				<?php /*infinite-scroll-disabled="loadAllNotifiBusy"*/ ?>
				infinite-scroll-distance="1"
				>
				<!-- One item -->
				<div class="notification-item notifiType-{{notification.activity_id}} allNotifi-{{notification.post.id}} clearfix"
					ng-repeat="notification in allNotifications | orderBy:'-created_at'"
					ng-init="message=getNotificationMsg(notification)"
					id="allNotifi-{{$index}}" 
					ng-class="{'notification-read': notification.status==3}"
					>
					<div class="heading"
						ng-if="notification.type != 'follow'"
						postcard="showPostDetails(notification.post.parent_post_id,notification.post.child_post_id,3,0,notification.comment_id)"
						data-toggle="modal" 
						data-target="#myModal{{ notification.post.parent_post_id }}"
						ng-click="markNotificationRead(notification,'allNotifi', $index)"
						>
						<a class="notifi-text pull-left">
							<span class="icon">
								<img ng-src="assets/img/notification/activity-{{notification.activity_id}}.png" alt="activity-image"/>
							</span>
							<span class="bold" ng-bind-html="message"></span>
						</a>
						<div class="pull-right">
							<span class="time" ng-if="showElapsedTime(notification.created_at)">
								<time am-time-ago="notification.created_at | amUtc | amLocal"></time>

							</span>
							<span class="time" ng-if="!showElapsedTime(notification.created_at)">
								<time>{{ notification.created_at | amDateFormat:'DD MMM, YYYY - HH:mm' }}</time>
							</span>
						</div>
					</div>
					<div class="heading follow-row"
						ng-if="notification.type == 'follow'"
						ng-click="openProfileFollow('notification', $index)"
						>
						<a class="notifi-text pull-left">
							<span class="icon">
								<img ng-src="assets/img/notification/activity-{{notification.activity_id}}.png" alt="activity-image"/>
							</span>
							<span class="bold" ng-bind-html="message"></span>
						</a>
						<div class="pull-right">
							<span class="time" ng-if="showElapsedTime(notification.created_at)">
								<time am-time-ago="notification.created_at | amUtc | amLocal"></time>

							</span>
							<span class="time" ng-if="!showElapsedTime(notification.created_at)">
								<time>{{ notification.created_at | amDateFormat:'DD MMM, YYYY - HH:mm' }}</time>
							</span>
						</div>
					</div>
					<div class="option" data-toggle="tooltip" data-placement="left" title="mark as read">
						<a class="mark" ng-click="markNotificationRead(notification,'single_ac', $index)"></a>
					</div> 
				</div>
				<!-- END One item -->
			</div>	
		</div>
		<div id="notificationLoader" ng-show="loadAllNotifiBusy"></div>
	</div>
</div>
<!-- POST CARD  MODAL -->
<postcard-modal></postcard-modal> 
<!-- REPORT COMMENT  MODAL -->
<report-comment-modal></report-comment-modal>