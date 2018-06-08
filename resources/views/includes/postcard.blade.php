<div class="blockContent loaded showNow">
    <div class="profileCommentBox clkProfileCommentBox" style="opacity:1;">
        <div class="cardBoxClk"></div>
        <div class="profileCommentBoxTop">

        <?php
        if(!empty($post['child_user']))
        {
        ?>
        <!--- share post caption start -->
            <div class="catagoryTagStatus">
                <div class="reshareLine">
                    <div class="reshareLineTxt">
                        <a href="{{account(''.$post['post_owner']['username'].'')}}" class="reshareUser">
                            <?php
                            if(!empty($post['post_owner']['profile_image']))
                            {
                            ?>
                            <div class="img" style="background:url({{ generate_profile_image_url('profile/thumbs/'.$user->profile_image) }}) no-repeat;"></div>
                            <?php
                            }
                            else
                            {
                            ?>
                            <div class="img {{ $post['post_owner']['user_color'] }}"
                            >
                                <span class="txt">{{ substr($post['post_owner']['first_name'], 0, 1) }}</span>
                            </div>

                            <?php
                            }
                            ?>

                            {{ $post['post_owner']['first_name'] }}

                        </a>
                        &nbsp;<img src="{{ asset('assets/pages/img/refresh4.png') }}" alt="reshare">&nbsp;
                        <a href="{{ account(''.$post['child_user']['username'].'') }}" class="reshareUser">
                            <?php
                            if(!empty($post['child_user']['profile_image']))
                            {
                            ?>
                            <div class="img" style="background:url({{ generate_profile_image_url('profile/thumbs/'.$post['child_user']['profile_image']) }}) no-repeat;"></div>
                            <?php
                            }
                            else
                            {
                            ?>
                            <div class="img {{ $post['child_user']['user_color'] }}"
                            >
                                <span class="txt">{{ substr($post['child_user']['first_name'], 0, 1) }}</span>
                            </div>

                            <?php
                            }
                            ?>

                            {{$post['child_user']['first_name']."'s Post"}}
                        </a>
                    </div>
                    {{-- <span class="shareTime">
                        <small ng-if="showElapsedTime(post.child_post_created_at)">
                            <time am-time-ago="post.child_post_created_at | amUtc | amLocal"></time>
                        </small>
                        <small ng-if="!showElapsedTime(post.child_post_created_at)"
                               data-toggle="tooltip" ui-jq="tooltip"
                               data-original-title="{{post.child_post_created_at | amDateFormat:'DD MMM, YYYY - HH:mm'}}">
                            <time>{{ post.child_post_created_at | amDateFormat:'DD MMM YYYY' }}</time>
                        </small>
                    </span> --}}
                </div>
                {{-- <p class="card-caption" ng-if="post.child_postCaption!='undefined'">
                    <span class="card-caption" ng-bind-html="post.child_postCaption | markupHTMLTags"></span>
                </p> --}}
            </div>
            <!--- END share post caption -->
            <?php
            }
            ?>

            <div class="userStatusRow smUserStatus">
                <?php
                if(!empty($post['user']['profile_image']))
                {
                ?>
                <div class="userStatusImage">
                    <a href="{{ account(''.$post['user']['username'].'') }}" style="background:url({{ generate_profile_image_url('profile/thumbs/'.$post['user']['profile_image']) }}) no-repeat;"></a>
                </div>
                <?php
                }
                else{
                ?>
                <div class="userStatusImage {{ $post['user']['user_color'] }}"
                >
                    <a href="{{ account(''.$post['user']['username'].'') }}">
                        <span class="txt">{{ substr($post['user']['first_name'], 0, 1) }}</span>
                    </a>
                </div>
                <?php
                }
                ?>

                <div class="userStatusInfo withLocation">
															<span class="userStatusInfoTtl clearfix withLocation">
																<a ui-sref="{{ account(''.$post['user']['username'].'') }}">{{ $post['user']['first_name'].' '.$post['user']['last_name'] }}</a>
															</span>
                    <div class="cardFollow clearfix">
                        <small>
                            <span><?php echo date("d M,Y", strtotime($post['created_at'])); ?></span>
                        </small>

                    </div>
                    <div class="userStatusInfo info">
                        <p class="userAbout">
																	<span class="clearfix">
																		<small>{{ $post['user']['about_me'] }}</small>
																	</span>
                        </p>
                    </div>
                </div>
                <div class="clearfix"></div>
            </div>
            <div class="post_type">
                <span class="noCaptionBx"></span>
                {{--<p class="card-caption">
                    <span class="card-caption">this is weekend
                        <a href="#"><span>#</span><span>music</span></a>
                        <a href="#"><span>#</span><span>coldplay</span></a>
                    </span>
                </p>--}}
                <div class="catagoryTtl">
                    <?php
                    if($post['category'] || isset($post['subCategory']) || isset($post['sub_category']) || $post['post_type'] == 3 &&
                    $post['time_needed']
                    != 0)
                    {
                    ?>
                    <div class="catagoryTagRow">
                        <?php
                        if(!empty($post['category']))
                        {
                        ?>
                        <a href="{{ '/tag/'.$post['category']['category_name_url'] }}" class="catagoryTtlHighLight">{{ $post['category']['category_name'] }}</a>
                        <?php
                        }
                        ?>

                        <?php
                        if(isset($post['subCategory']))
                        {
                        ?>
                        <a href="{{ '/tag/'.$post['subCategory']['subcategory_name_url'] }}" class="catagoryTtlHighLight">{{ $post['subCategory']['category_name'] }}</a>

                        <?php
                        }
                        ?>

                        <?php
                        if(isset($post['sub_category']))
                        {
                        ?>
                        <a href="{{ '/tag/'.$post['sub_category']['subcategory_name_url'] }}" class="catagoryTtlHighLight">{{ $post['sub_category']['category_name'] }}</a>

                        <?php
                        }
                        ?>

                    </div>
                    <?php
                    }
                    ?>

                    <?php
                    if(!empty($post['caption']))
                    {
                    ?>
                    <p class="card-caption">
                        <span class="card-caption"><?php echo str_ireplace("?src=hash", "", trim($post['caption']));
                            ?></span>
                    </p>
                    <?php
                    }
                    ?>


                    <p>{{ $post['title'] }}
                        <?php
                        if($post['location'] != '' && $post['place_url'] && $post['post_type'] == '5')
                        {
                        ?>
                        <a class="cardLoc" href="{{ '/place?'.$post['place_url'] }}">
                            <img src="/assets/pages/img/location1.png" alt="Location Info">
                            <span>{{ showLocation($post['location']) }}</span>
                        </a>
                        <?php
                        }
                        ?>

                    </p>
                </div>
                <?php
                if(!empty($post['short_description']))
                {
                ?>
                <div class="postShortDesc">
                    <p>{{ strlen($post['short_description']) > 100 ? substr($post['short_description'], 0, 100) : $post['short_description'] }}</p>
                </div>
                <?php
                }
                ?>


                {{-- For Image Post / Article Post --}}
                <?php
                if(in_array($post['post_type'], array('1', '3')))
                {
                ?>
                <div>
                    <?php if(!empty($post['source'])) { ?>
                    <p class="postLink">
                        <span>
                            <a href="{{ $post['source'] }}" target="_blank">
                                {{ domainFilter($post['source']) }}
                            </a>
                        </span>
                    </p>
                    <?php } ?>
                    <?php if(!empty($post['image'])) { ?>
                        <div class="uploadImage">

                            <img src="{{ $post['image'] }}">

                        </div>
                    <?php   } ?>

                    <?php if($post['post_type'] == '3') { ?>
                    <div class="postShortDesc"><?php echo strip_tags(trim($post['content'])); ?></div>
                    <?php } ?>
                </div>
                <?php
                }
                ?>

                {{-- For Video Post --}}
                <?php
                if($post['post_type'] == '2')
                {
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
                <?php
                    if(!empty($post['embed_code']) || !empty($post['video'])){
                     //$video_url = !empty($post['embed_code']) ? $post['embed_code'] : generate_post_video_url('video/' . $post['video']);
                     $video_url = !empty($post['embed_code']) ? $post['embed_code'] :  $post['video'];
                ?>
                    <div class="uploadImage">
                        <div class="uploadVidPreview">
                            <iframe src="{{ $video_url }}"></iframe>
                        </div>
                    </div>
                <?php
                }
                ?>
                <?php
                }
                ?>

                {{-- For Link Post --}}
                <?php
                if($post['post_type'] == '4')
                {
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
                }
                ?>

                {{-- For Status Post --}}
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
                        <iframe src="{{ generate_post_video_url('video/' . $post['video']) }}"></iframe>
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

                echo '</div>';
                ?>

                <?php
                }
                ?>

                <?php
                if(!empty($post['tags']))
                {
                ?>
                <div class="profileNewCoverBottomLink">
                    <ul>
                        <?php
                        foreach($post['tags'] as $tag){
                        ?>
                        <li><a href="{{ '/tag/' . $tag['tag_name'] }}">#{{ $tag['tag_name'] }}</a></li>
                        <?php
                        }
                        ?>
                    </ul>
                </div>
                <?php
                }
                ?>

            </div>
            <div>
                <div class="profileCommentFooter addNew noBorderPos">
                    <div class="left">
                        <a class="upvoteIcon">
                            <img src="https://swolk.com/assets/pages/img/arrow2_t.png" alt=""/>
                        </a>
                        <a class="tip">
                            <img src="https://swolk.com/assets/pages/img/speech_bubble4.png" alt=""/>
                        </a>
                        <div class="cardSmNav">
                            <a class="shrClk">
                                <img src="https://swolk.com/assets/pages/img/refresh4.png" alt=""/>
                            </a>
                        </div>
                    </div>
                    <div class="right">
                        <div class="cardSmNav last">
                            <a href="javascript:void(0);" class="moreBtnN">
                                <i class="fa fa-circle" aria-hidden="true"></i>
                                <i class="fa fa-circle" aria-hidden="true"></i>
                                <i class="fa fa-circle" aria-hidden="true"></i>
                            </a>
                            <div class="subOverlay subOverlaysh" style="display:none;"></div>
                            <div class="sub otherSubsh" style="display:none;">
                                <ul>
                                    <li class="withA">
                                        <a href="{{ $post['post_url'] }}" target="_blank">
                                            <img src="/assets/pages/img/log_out.png" alt="" class="ico"> View
                                        </a>
                                    </li>
                                    <li>Downvote </li>
                                    <li>Delete</li>
                                </ul>
                            </div>
                        </div>
                    </div>
                    <div class="clearfix"></div>
                </div>
            </div>
        </div>
    </div>
</div>