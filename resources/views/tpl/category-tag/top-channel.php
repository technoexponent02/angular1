<div class="postRow">	
	<div class="channelRow inline"
		infinite-scroll="fetchTopChannel()"
	    <?php /*infinite-scroll-disabled="busy" */?>
	    infinite-scroll-distance="2">
	    
	    <channel-card ng-repeat="channel in channelUsers"></channel-card>

		<h2 class="noPostAvailable" ng-if="noChannelForTag"> No {{ post_type  == 'top_channel' ? 'Top Channel' : post_type }} post.</h2>
		<h2 class="noPostAvailable" ng-if="noMoreChannel"> No more {{ post_type  == 'top_channel' ? 'Top Channel' : post_type }} post.</h2>
	</div>
</div>

<postcard-modal></postcard-modal>
<!-- REPORT COMMENT  MODAL -->
<report-comment-modal></report-comment-modal>