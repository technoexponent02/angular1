<?php
foreach($post['getUser'] as $key=>$user){
	$postUserName			= $user['first_name'];
    $createdUsername    	= $user['username'];
    $createdUserFirstName   = $user['first_name'];
    $createdUserLastName    = $user['last_name'];
    $createdUserColor       = $user['user_color'];
    $createdUserId 			= $user['id'];
    $createdProfileImage 	= $user['profile_image'];
}

$keywords = '';
if ($post->tags !== null) {
    foreach($post->tags as $tag) {
        $keywords .= ', ' . $tag->tag_name;
    }
    $keywords = ltrim($keywords, ', ');
}

?>
@extends('seo.public')

@section('titleMetaTag')
	<title><?php echo ($post['post_type'] == 5 ? strip_tags($post['caption']) : $post['title']); ?> | SWOLK</title>
	<meta name="description" content="<?php echo ($post['post_type'] == 5 ? strip_tags($post['caption'])."| SWOLK" :
			$post['short_description']); ?>"/>
    <meta name="keywords" content="{{ $keywords }}">
	<meta property="og:title" content="<?php echo ($post['post_type'] == 5 ? strip_tags($post['caption'])." | SWOLK" : $post['title']); ?>" />
	<meta property="og:type" content="<?php echo getPostType($post['post_type']); ?>" />
	<meta property="og:url" content="<?php echo $post['post_url']; ?>" />
	<meta property="og:image" content="<?php echo $post['image']; ?>" />
	<meta property="og:image" content="<?php echo $post['image']; ?>" />
	<?php /* ?><meta property="og:description" content="<?php echo $post['short_description']; ?>" /> <?php */ ?>

	@if($post['post_type'] == 3 && $post['external_link'])
	<link rel="canonical" href="{{ $post['external_link'] }}" />
	@endif
@endsection

@section('content')
	<div class="page-container">
		<div class="header">
			<div class="customHeader clearfix">
				<div class="headerMiddle">
					<div class="brand inline">
						<a href="">
							<img src="/assets/img/logo_2x.png" alt="logo" height="26"/>
						</a>
					</div>
				</div>
			</div>
		</div>

		<div class="page-content-wrapper">
			<div class="headerLine"></div>
			<div class="content">
				<div class="full-height full-width">
					<div class="container-fluid padding-25 sm-padding-10 postDetailsPG" style="padding-top:0px;">
						<div class="cardDetailsPG_modal modal" id="myModal2">
							<div class="detailsTopbar">
								<div class="mobileModalHeader clearfix">
									<a href="javascript:;" class="mobileHeadbookMark saveDel">
										<img src="assets/pages/img/bookmark.png" class="saveIcon ng-scope">
									</a>
									<div class="mobileModalHeaderMid">
										<span class="postOnlineUsers">
											<i class="fa fa-circle"></i>
											1 people here
										</span>
									</div>
								</div>
							</div>
							<div class="modal-body">
								<div class="row">
									<div class="col-md-9 col-sm-8 col-xs-12">
										<div class="leftContainerHeight">
											<div class="container-xs-height">
												<div class="row-xs-height">
													<div class="profileNewLeft">
														<div class="profileNewCoverFooter">
															<div class="catgSubcatg">
																<?php
																if(!empty($post['category']['category_name']))
																{
																?>
																	<a href="{{ '/tag/'.$post['category']['category_name'] }}" class="catagoryTtlHighLight">{{ $post['category']['category_name'] }}</a>
																<?php
																}
																?>
																<?php
																if(!empty($post['subCategory']['category_name']))
																{
																?>
																	<a href="{{ '/tag/'.$post['subCategory']['category_name'] }}" class="catagoryTtlHighLight">{{ $post['subCategory']['category_name'] }}</a>
																<?php
																}
																?>
															</div>
															<ul>
																<li>
																   <a href="javascript:void(0)">
																	<strong>
																	<?php echo ($post['upvotes'] - $post['downvotes'] > 0 ? '+' : '') . ($post['upvotes'] - $post['downvotes']); ?>
																	</strong>
																	<?php echo ($post['upvotes']-$post['downvotes']) >= 0 ? ($post['upvotes']-$post['downvotes'] <= 1 ? 'upvote' : 'upvotes') : ($post['upvotes']-$post['downvotes'] > -1 ? 'downvote' : 'downvotes'); ?>
																	</a>
																</li>
																<li>
																	<a href="javascript:void(0)">
																	<strong>{{ thousandsSuffix($post['postParentComment']) }}</strong> {{ $post['postParentComment'] > 1 ? 'comments' : 'comment' }}</a>
																</li>
																<li>
																	<a href="javascript:void(0)">
																		<strong>{{ thousandsSuffix($post['totalShare']) }}</strong> {{ $post['totalShare'] > 1 ? 'shares' : 'share' }}</a>
																</li>
																{{--<li>
																	<span class="postOnlineUsers">
																		<i class="fa fa-circle"></i> 1 people here
																	</span>
																</li>--}}
																<li>
																	<a href="javascript:void(0)">
																		<strong>{{ thousandsSuffix($post['totalPostViews']) }}</strong> &nbsp;{{ showPostViewTxt($post->post_type, 'details') }}</a>
																</li>
																<li class="right">
																	<a href="javascript:void(0)" class="saveBtn saveDel">
																		<strong>{{ thousandsSuffix($post['totalBookMark']) }}</strong> Save
																	</a>
																</li>
																<li>
																	<a href="javascript:void(0)">
																		<strong>{{ thousandsSuffix($post['points']) }}</strong> Post Points</a>
																</li>
															</ul>
															<?php if($post['post_type'] == 3 && $post['time_needed'] != 0) { ?>
																	<div class="postTime">
																		{{ $post['time_needed'] }}&nbsp;min read
																	</div>
															<?php } ?>

															<div class="clearfix"></div>
														</div>
														<?php if(!empty($post['caption'])){ ?>
															<h2 class="postDetCaption">
																<span>{{ $postUserName }} : </span>
																"<strong><?php echo str_ireplace("?src=hash", "", trim($post['caption'])); ?></strong>"
																<?php
																if(!empty($post['place_url']) && $post['post_type'] == '5'){
																?>
																	<a href="/place?{{ $post['place_url'] }}" class="cardLoc">
																		<img src="/assets/pages/img/location1.png" alt="Location"/>
																		<span data-toggle="tooltip" ui-jq="tooltip"
																			  data-original-title="{{ $post['location'] }}">{{ $post['location'] }}</span>
																	</a>
																<?php
																}
																?>
															</h2>
														<?php } ?>
														<?php
														if($post['post_type'] == '5'){
														?>
														<span class="timeSh">
															<small><?php echo date("d M, Y", strtotime($post['created_at'])); ?></small>
														</span>
														<?php } ?>

														{{-- start image post --}}
														<?php
														if(in_array($post['post_type'], array('1', '3')))
														{
														?>
															<div class="postTypeBlock">
															<?php if(!empty($post['source'])) { ?>
																<p class="postLink" >
																	<span>
																		<a href="{{ $post['source'] }}" target="_blank">
																			<i class="fa fa-external-link"></i>
																				{{ domainFilter($post['source']) }}
																		</a>
																	</span>
																</p>
															 <?php } ?>
																<div class="uploadImage">
																	<img src="{{ $post['image'] }}" alt="post image">
																</div>
															 <?php
															 if($post['post_type'] == '3'){
															 ?>
															 	<div class="catagoryTtl">
																	<div class="catagoryTagRow">
																	<p>{{ $post['title'] }}
																	    <?php if(!empty($post['place_url'])) { ?>
																		<a href="/place?{{$post['place_url']}}"
																		class="cardLoc">
																			<img src="/assets/pages/img/location1.png"
																			alt="Location">
																			<span data-toggle="tooltip" ui-jq="tooltip"
																			data-original-title="{{ $post['location'] }}">{{ $post['location'] }}
																			</span>
																		</a>
																		<?php } ?>
																	</p>
																	<?php
																	if($post['post_type'] !== '5'){
																	?>
																	<span class="timeSh">
																		<small><?php echo date("d M, Y", strtotime($post['created_at'])); ?></small>
																	</span>
																	<?php } ?>
																</div>
																<div class="uploadBox articleContent">
																	<?php echo strip_tags(trim($post['content'])); ?>
																</div>
															 <?php
															 }
															 ?>
															</div>
														<?php
														}
														?>

														{{-- end image post --}}


														{{-- Start Video Post --}}
														<?php
														if($post['post_type'] == '2')
														{
															echo '<div class="postTypeBlock">';
															if(!empty($post['source']) && $post['source']!='http://undefined'){
															?>
																<p class="postLink">
												                    <span>
												                        <a href="{{ !empty($post['embed_code']) ? $post['embed_code'] : $post['source'] }}" target="_blank">
												                            <!-- <i class="fa fa-external-link"></i> -->
												                            {{ domainFilter(''.$post['source'].'') }}
												                        </a>
												                    </span>
												                </p>
															<?php
															}
														?>
															<div class="uploadImage">
                    											<div class="uploadVidPreview">
                    											<iframe src="{{ $post['embed_code'] }}"></iframe>
                    											</div>
                    										</div>
														<?php
															if($post['video'])
															{
															?>
															<div class="uploadImage">
																<div class="uploadVidPreview">
																	<iframe src="{{ $post['video'] }}"></iframe>
																</div>
															</div>
															<?php
															}
															echo '</div>';
														}
														?>
														{{-- End Video Post --}}

														{{-- Start Link Post --}}
														<?php
														if($post['post_type'] == '4')
														{
															echo '<div class="postTypeBlock">';
															if(!empty($post['source']))
															{
															?>
																<div class="postLinkBtn">
												                    <a href="{{ $post['external_link'] }}" target="_blank" class="btn btn-default btn-sm">
												                        <i class="fa fa-external-link"></i>
												                        {{ domainFilter($post['source']) }}
												                    </a>
												                </div>
															<?php
															}

															if($post['embed_code'])
															{
															?>
																<div class="uploadImage">
	                    											<div class="uploadVidPreview">
	                    											<iframe src="{{ $post['embed_code'] }}"></iframe>
	                    											</div>
	                    										</div>
															<?php
															}

															if(empty($post['embed_code']) && !empty($post['image']))
															{
															?>
																<div class="uploadImage">

												                     <img src="{{ $post['image'] }}">

												                </div>
															<?php

															}
														?>

														<?php
															echo '</div>';
														}
														?>

														{{-- End Link Post --}}

														{{-- Start Status Post --}}
														<?php
														if($post['post_type'] == '5')
														{
															echo '<div>';
															if(!empty($post['source']) && $post['source']!='http://undefined'){
															?>
																<p class="postLink">
												                    <span>
												                        <a href="{{ !empty($post['embed_code']) ? $post['embed_code'] : $post['source'] }}" target="_blank">
												                            <!-- <i class="fa fa-external-link"></i> -->
												                            {{ domainFilter(''.$post['source'].'') }}
												                        </a>
												                    </span>
												                </p>
															<?php
															}
															if($post['embed_code'])
															{
															?>
																<div class="uploadImage">
	                    											<div class="uploadVidPreview">
	                    											<iframe src="{{ $post['embed_code'] }}"></iframe>
	                    											</div>
	                    										</div>
															<?php
															}
															if($post['video'])
															{
															?>
																<div class="uploadImage">
	                    											<div class="uploadVidPreview">
	                    											<iframe src="{{ $post['video'] }}"></iframe>
	                    											</div>
	                    										</div>
															<?php
															}
															if(!empty($post['image']))
															{
															?>
																<div class="uploadImage">

												                     <img src="{{ $post['image'] }}">

												                </div>
															<?php
															}

															echo '</div>';
														?>

														<?php
														}
														?>

														{{-- End Status Post --}}

														<?php if(in_array($post['post_type'], array(1, 2, 4))){  ?>

														<div class="postTypeBlock">
															<div class="catagoryTtl">
																<div class="catagoryTagRow">
																	<p>{{ $post['title'] }}

																	<?php if(!empty($post['location'])) { ?>

																	<a href="/place?{{$post['place_url']}}"
																	class="cardLoc">
																	<img src="/assets/pages/img/location1.png"
																	alt="Location">
																		<span data-toggle="tooltip" ui-jq="tooltip"
																		data-original-title="{{$post['location']}}">{{$post['location']}}</span>
																	</a>

																	<?php } ?>

																	</p>
																	<?php if($post['post_type'] != '5'){ ?>
																	<span class="timeSh">
																		<small><?php echo date("d M, Y", strtotime($post['created_at'])); ?>
																		</small>
					                                                </span>
					                                                <?php } ?>
																</div>
															</div>
														</div>
														<?php } ?>

														<?php if(!empty($post['short_description'])) { ?>
														<div class="postTypeBlock" >
															<div class="uploadBox articleContent">
																<p>{{ $post['short_description'] }}</p>
															</div>
														</div>
														<?php } ?>

														<?php if($post['post_type'] == '4'){ ?>
														<div class="postTypeBlock">
															<?php if(!empty($post['source'])){ ?>
																<div  class="postLinkBtn">
															   		<a href="{{ $post['external_link'] }}" target="_blank" class="btn btn-default btn-sm postLinkBtnTxt">
																		Discover More @
																		{{ domainFilter($post['source']) }}
																	</a>
																</div>
															<?php } ?>

														</div>
														<?php } ?>

														<div class="profileNewCoverBottom {{ $post['post_type'] !== '4' ? 'nopostTypeBlock' : '' }}">
															<div class="profileNewCoverBottomLink postdetails p-t-10 p-b-10">
																<ul>
																	<?php
																	foreach($post['tags'] as $key=>$tag){
																	?>
																		<li><a href="/tag/{{ $tag['tag_name'] }}" target="_blank">#{{ $tag['tag_name'] }}</a></li>
																	<?php
																	}
																	?>
																</ul>
															</div>
															<div class="profileNewCoverLinkBtn profileRightLink profilergtNewLinks clearfix">
																<div class="newShrBtns">
																	<div class="middle">
																		<a class="upvoteIcon">
																			<img src="/assets/pages/img/arrow2_t.png">
																			<span>
																				<?php echo ($post['upvotes'] - $post['downvotes'] > 0 ? '+' : '') . ($post['upvotes'] - $post['downvotes']); ?>
																			</span>
																			<span><?php echo ($post['upvotes']-$post['downvotes']) >= 0 ? ($post['upvotes']-$post['downvotes'] <= 1 ? 'upvote' : 'upvotes') : ($post['upvotes']-$post['downvotes'] > -1 ? 'downvote' : 'downvotes'); ?>
																			</span>
																		</a>
																		<a href="#" class="upvoteIcon">
																			<img src="https://swolk.com/assets/pages/img/refresh4.png" alt="">
																			<span>{{ thousandsSuffix($post['normal_share']) }}</span>
																		</a>
																		<a class="upvoteIcon">
																			<img src="https://swolk.com/assets/pages/img/fb-icon.png" alt="">
																			<span>{{ thousandsSuffix($post['totalFBshare']) }}</span>
																		</a>
																		<a href="#" class="upvoteIcon">
																			<img src="https://swolk.com/assets/pages/img/twt-icon.png" alt="">
																			<span>{{ thousandsSuffix($post['totalTwittershare']) }}</span>
																		</a>

																	</div>
																</div>
															</div>
														</div>
													</div>
												</div>
											</div>
										</div>
									</div>
									<div class="col-md-3 col-sm-4 col-xs-12">
										<div class="widget rightWidget">
											<?php foreach($post['getUser'] as $key=>$postuser){
											?>
												<div class="widget-advanced">
													<!-- creator label -->
													<?php if($key == 1){
													?>
														<h3 class="creatorTtl">Creator</h3>
													<?php
													} ?>

													<div class="widget-header text-center profileRgtCover" style="background:url({{ $postuser['cover_image'] }}) no-repeat;">
													</div>
													<div class="widget-main">
														<?php if(!empty($postuser['profile_image'])) {
														?>
															<a href="{{ account($postuser['username']) }}" class="widget-image-container animation-hatch" style="background:url({{  generate_profile_image_url('profile/'.$postuser['profile_image']) }}) no-repeat;" target="_blank"></a>
														<?php
														}else{ ?>
															<a href="{{ account($postuser['username']) }}" class="widget-image-container animation-hatch <?php echo $postuser['user_color']; ?>"  target="_blank">
																<span class="txt"><?php echo substr($postuser['first_name'], 0, 1); ?></span>
															</a>
														<?php } ?>


														<h3 class="widget-content widget-content-image widget-content-light profileName">
															<a class="themed-color" href="{{ account($postuser['username']) }}">{{ $postuser['first_name']." ".$postuser['last_name'] }}</a>
															<?php if($postuser['occupation']){
															?>
															<span class="uoccupation">{{ $postuser['occupation'] }}</span>
															<?php
															} ?>
															<small class="ng-binding ng-scope">&nbsp;{{ $postuser['about_me'] }}</small>
														</h3>

														<div class="row text-center animation-fadeIn">
															<div class="col-xs-4">
																<h5>
																	<strong class="ng-binding">{{ thousandsSuffix($postuser['userDataProfileViews']) }}</strong>
																	<br>
																	<small>Views</small></h5>
															</div>
															<div class="col-xs-4">
																<h5><strong class="ng-binding">{{ thousandsSuffix($postuser['points']) }}</strong><br><small>Points</small></h5>
															</div>
															<div class="col-xs-4">
																<h5><strong class="ng-binding">{{ thousandsSuffix($postuser['is_follow']) }}</strong><br>
																	<small>Followers</small></h5>
															</div>
														</div>
													</div>
												</div>
											<?php
											} ?>


											{{--<div class="widget-advanced">
												<h3 class="creatorTtl">Creator</h3>
												<div class="widget-header text-center profileRgtCover" style="background:url(https://swolk.com/uploads/profile/cover/1483956104ljna7wYQP4.coldplay-wallpaper-picture-1jpg.jpg) no-repeat;">
												</div>
												<div class="widget-main">
													<a class="widget-image-container animation-hatch" style="background:url(https://swolk.com/uploads/profile/thumbs/1485139006a9QkSGY8Dd.coldplay-wallpaper-picture-1.jpg) no-repeat;" href=""></a>
													<h3 class="widget-content widget-content-image widget-content-light profileName">
														<a class="themed-color ng-binding" href="">Ronald Halim (this is original account)</a>
														<small class="ng-binding ng-scope">&nbsp;Have passion about technology, music</small>
													</h3>
													<div class="userFollow ng-scope">
														<label class="followBtn ng-scope">
															<span class="ico">FOLLOWING</span>
														</label>
													</div>
													<div class="row text-center animation-fadeIn">
														<div class="col-xs-4">
															<h5>
															<strong class="ng-binding">330</strong>
															<br>
															<small>Views</small></h5>
														</div>
														<div class="col-xs-4">
															<h5><strong class="ng-binding">19.1K</strong><br><small>Points</small></h5>
														</div>
														<div class="col-xs-4">
															<h5><strong class="ng-binding">5</strong><br>
															<small>Followers</small></h5>
														</div>
													</div>
												</div>
											</div>--}}

										</div>
									</div>
								</div>
							</div>
						</div>
					</div>
				</div>
			</div>
			<div class="container-fluid container-fixed-lg footer">
				<div class="copyright sm-text-center">
					<p class="small no-margin pull-left sm-pull-reset">
						<span class="hint-text">Copyright © 2017</span>
						<span class="font-montserrat">Swolk</span>.
						<span class="hint-text">All rights reserved.</span>
						<span class="sm-block">
							<a class="m-l-10 m-r-10" href="">Terms of use</a> |
							<a class="m-l-10" href="">Privacy Policy</a>
						</span>
					</p>
					<p class="small no-margin pull-right sm-pull-reset">
						<a href="">Hand-crafted</a>
						<span class="hint-text">&amp; Made with Love ®</span>
					</p>
					<div class="clearfix"></div>
				</div>
			</div>
		</div>
	</div>
</div>
@stop

<?php
function showPostViewTxt($post_type='', $referrer=''){
	if(empty($post_type)){
		return '';
	}
	$text = '';
	switch($post_type){
		case 1:
		case 5: if($referrer == 'card'){ $text = 'seen'; }else{ $text = 'viewed'; }
				break;
		case 2: $text = 'played';break;
		case 3: $text = 'read';break;
		case 4: $text = 'accessed';break;		

	}
	return $text;
}

function getPostType($post_int_type=''){
	$post_type = '';
	switch($post_int_type){
		case 1: $post_type = 'Image';break;
		case 2: $post_type = 'Video';break;
		case 3: $post_type = 'Article';break;
		case 4: $post_type = 'Link';break;
		case 5: $post_type = 'Status';break;
	}
	return $post_type;
}

?>