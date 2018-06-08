<div class="channelContent">
    <div class="channelContentMiddle">
        <div class="channelContentCover">
            <a ui-sref="account({ username: channel.username })" style="background:url({{channel.cover_image}});">
                <div class="chanelFollow"></div>				
            </a>
			<div class="usrProfileLikePost">
				<div class="liTm" ng-if="::channel.original_post_count">
					<span class="tL">{{::channel.original_post_count > 1 ? 'Posts' : 'Post'}}</span>
					<span class="tLCount">{{::channel.original_post_count}}</span>
				</div>
				<div class="liTm" ng-if="channel.follower_count">
					<span class="tL">{{channel.follower_count > 1 ? 'Followers' : 'Follower'}}</span>
					<span class="tLCount">{{channel.follower_count}}</span>
				</div>
			</div>
        </div>
        <div class="channelContentFooter">
			<div class="channelContentUserCont">
				<div class="channelContentUser" ng-if="::channel.thumb_image_url">
					<a ui-sref="account({ username: channel.username })" style="background:url({{::channel.thumb_image_url}}) no-repeat;">
					</a>
				</div>
				<div class="channelContentUser {{::channel.user_color}}" ng-if="::(!channel.thumb_image_url)">
					<a ui-sref="account({ username: channel.username })">
						<span class="txt">{{::channel.first_name.charAt(0)}}</span>
					</a>		
				</div>
				<a href="/profile/{{::channel.username}}" class="nm">
					<span ng-bind-html="::(channel.first_name + ' ' + channel.last_name | highlightNode:searchResults.query_arr)"></span>
				</a>
				<label class="followBtn"
						ng-if="::(showFollowBtn && channel.id!==user.id && user.guest==0)"
						allfollowuser="followUser(channel.id,'C','followed');">
							<span ng-if="userFollowing.indexOf(channel.id)==-1">FOLLOW</span>
							<span class="active" ng-if="userFollowing.indexOf(channel.id)!=-1">FOLLOWING</span>
				</label>		
				<label class="followBtn"
						ng-if="::(showFollowBtn && channel.id!==user.id && user.guest!=0)"
						ng-click="redirecToLogin();">
						<span>FOLLOW</span>
				</label>
			</div>
			<div class="chanelDesc">
				<span class="chUname" ng-bind-html="::(channel.username | highlightNode:searchResults.query_arr)"></span>
				<span class="uoccupation" ng-bind-html="::(channel.occupation | highlightNode:searchResults.query_arr)"></span>
				<p ng-if="::channel.about_me"
					data-toggle="tooltip"
					ui-jq="tooltip"
					data-original-title="{{::channel.about_me}}">
				<span ng-bind-html="::(channel.about_me | highlightNode:searchResults.query_arr)"></span>
				</p>
			</div>
        </div>
    </div>
</div>