<?php

/*
  |--------------------------------------------------------------------------
  | Application Routes
  |--------------------------------------------------------------------------
  |
  | Here is where you can register all of the routes for an application.
  | It's a breeze. Simply tell Laravel the URIs it should respond to
  | and give it the controller to call when that URI is requested.
  |
 */

Route::get('logout', 'Auth\AuthController@logout');
//Route::get('db-backup', 'TestController@dbBackup'); 
Route::get('change-tag-text', 'HomeController@changeTagText');
Route::get('sync-duplicate-tag', 'HomeController@syncDuplicateTag');


Route::group(['middleware' => 'guest'], function () {
    // For not supported browser..
    Route::get('/unsupported-browser', 'HomeController@unsupported');
    // Login
    Route::get('login', 'Auth\AuthController@getLogin');
    Route::post('login', 'Auth\AuthController@postLogin');

    
   
    
    // Activate email
    Route::get('signup/verify/{token}', 'SignupController@verifyEmail');

    // Routes for signup    
    Route::get('signup', 'SignupController@getRegister');
    // Disable and redirect to 404
    /*Route::get('signup', function () {
        abort(404);
    });*/
    Route::post('signup', 'SignupController@postRegister');    

    Route::get('password/forgot', 'UserController@forgetPassword');
    Route::post('password/forgot', 'UserController@forgotPasswordProcess');
    Route::get('password/reset/{token}', 'UserController@resetPassword');
    Route::post('password/reset', 'UserController@resetPassProcess');

    // Facebbok login
    Route::get('auth/facebook/{param}', 'Auth\AuthController@redirectToProviderFacebook');
    Route::get('auth/facebook/signup/{media}', 'Auth\AuthController@getRegister');
    Route::post('auth/facebook/signup/{media}', 'Auth\AuthController@postRegister');

    // Twitter login
    Route::get('auth/twitter/{param}', 'Auth\AuthController@redirectToProviderTwitter');
    Route::get('auth/twitter/signup/{media}', 'Auth\AuthController@getRegister');
    Route::post('auth/twitter/signup/{media}', 'Auth\AuthController@postRegister');

    //Redirect Page
    //Route::get('auth/social/redirect/{param}', 'Auth\AuthController@redirectToLanding'); //14/11/2017
    
    
   
});

// Social Call Back 
Route::get('auth/facebook/success/callback', 'Auth\AuthController@handleProviderCallbackFacebook');
Route::get('auth/twitter/success/callback', 'Auth\AuthController@handleProviderCallbackTwitter');
Route::post('auth/linkedin/callback', 'Auth\AuthController@handleProviderCallbackLinkedin');
// Username and email checking
Route::get('check-username', 'ValidationController@checkUsername');
Route::get('check-email', 'ValidationController@checkEmail');

/*
 | ---------------------------------------------------------------------
 | Routes for partial views
 | ---------------------------------------------------------------------
 */
Route::group(['middleware' => ['api', 'auth']], function () {
    Route::get('post-add/{any}', 'HomeController@serveAppPostAd');
//    Route::get('post-edit/{any}/{id}', 'HomeController@serveAppEditPost');
    Route::get('post-edit/{any}', 'HomeController@serveAppEditPost');
});

// twitter
Route::get('twitter_connect/{id}', 'Api\ProfileController@twitterConnect');
Route::get('twitter_oauth', 'Api\ProfileController@twitter_oauth');
Route::post('accessTokenVerify', 'Api\ProfileController@accessTokenVerify');
Route::post('postToTwitter', 'Api\ProfileController@postToTwitter');

Route::get('facebookLogin', 'Api\ProfileController@facebookLogin');

Route::get('facebookCallback', 'Api\ProfileController@facebookCallback');

Route::get('tpl.non-login-header', 'HomeController@serveHeaderNonLoginVers');
Route::get('tpl.footer', 'HomeController@serveFooter');
Route::get('tpl.non-login-sidebar', 'HomeController@serveSidebarNonLoginVers');
Route::get('tpl.quick-view', 'HomeController@serveQuickView');
Route::get('tpl.quick-search', 'HomeController@serveQuickSearch');
// For comment box.
Route::get('tpl.comment-box', 'Api\PostController@commentBoxTemplate');
Route::get('tpl.discussion', 'Api\PostController@discussionTemplate');

Route::get('post/{category?}/{subcategory?}/{title?}/{id?}', 'Api\PostController@redirectToPostDetails');

Route::match(['get', 'post'], 'angular/auth-json', 'AngularController@authUserJson');

Route::get('tpl.sharepost-card', 'HomeController@serveSharePostCardView');
Route::get('tpl_deletePostModal', 'Api\ProfileController@deletePostModal');
Route::get('tpl_reportPostModal', 'Api\ProfileController@reportPostModal');
Route::get('tpl.post-card-menu', 'HomeController@servePostCardMenuView');
Route::get('tpl_reportCommentModal', 'Api\ProfileController@reportCommentModal');
Route::get('tpl_promptSinginBox', 'Api\ProfileController@promptSinginBox');

// privacy policy and terms and conditions
Route::get('tpl.privacy_policy', 'HomeController@privacyPolicy'); 
Route::get('tpl.terms-and-conditions', 'HomeController@termsAndConditions');
    
// Profile 

Route::get('profile', 'HomeController@serveApp');
//Route::get('profile/{user_name}', 'HomeController@serveApp');

//Test Profile Page
Route::get('profile/{user_name}', 'Api\ProfileController@viewUserProfile');

Route::get('privacy-policy', 'HomeController@serveApp');
Route::get('terms-and-conditions', 'HomeController@serveApp');
    

Route::get('tpl.profile', 'Api\ProfileController@index');
Route::get('tpl.user-profile', 'Api\ProfileController@userProfile');

Route::get('angular/profileList', 'AngularController@getProfileJson');
Route::post('angular/profileList', 'AngularController@getProfileJson');

// Post..
Route::get('tpl.post-card', 'HomeController@servePostCardView');
Route::get('tpl.search-post-card', 'HomeController@serveSearchPostCardView');
Route::get('tpl.post-type-nav/{templateType}', 'HomeController@postTypeNavView');

Route::get('tpl_posts', 'Api\ProfileController@postsTemplate');
Route::get('tpl_postcardmodal', 'Api\ProfileController@postcardmodal');
Route::get('tpl_follower', 'Api\ProfileController@followerTemplate');
Route::get('tpl_following', 'Api\ProfileController@followingTemplate');
Route::get('tpl_categories', 'Api\ProfileController@followcategories');

Route::match(['get', 'post'], 'angular/loadMorePost', 'AngularController@loadMorePost');
Route::post('angular/profileTabData', 'Api\ProfileController@profileTabData');
Route::post('angular/showPostDetails', 'Api\ProfileController@showPostDetails');
Route::get('angular/showPostDetails', 'Api\ProfileController@showPostDetails');
Route::get('angular/showLoadMoreComments', 'Api\ProfileController@showLoadMoreComments');
Route::post('angular/showLoadMoreComments', 'Api\ProfileController@showLoadMoreComments');
Route::post('angular/loadChildComments', 'Api\ProfileController@loadChildComments');
 // Category-tags page..
Route::get('tpl.category-tag-post', 'HomeController@serveCategoryTagPostView');
Route::get('tpl.category-tag-top-channel', 'HomeController@serveCategoryTagTopChannelView');
Route::get('tpl.channel-card', 'HomeController@serveChannelCardView');
Route::get('tpl.search-channel-card', 'HomeController@serveSearchChannelCardView');

Route::post('angular/viewPost', 'Api\ProfileController@viewPost');

Route::post('angular/viewSeenPost', 'Api\ProfileController@viewSeenPost');//(20-12-17)


Route::post('sharedPostInSocialNetworking', 'Api\ProfileController@sharedPostInSocialNetworking');
Route::post('sharedPostInSocialNetworkingForFacebook', 'Api\ProfileController@sharedPostInSocialNetworkingForFacebook');

// update facebook access token :: 
Route::post('updateFacebookCredentails', 'Api\ProfileController@updateFacebookCredentails');

Route::get('tpl.category-tag-ag-post', function() {
    return view('tpl.category-tag.ag-post');
});

Route::get('tpl.test-post-card', function(){
    return view('tpl.post.test-post-card');
});

// Authenticated routes
Route::group(['middleware' => ['web']], function () {
    Route::get('category', 'HomeController@serveApp');
    Route::get('place', 'Api\PostController@redirectToPlaceDetails');
    Route::get('tag/{name}', 'Api\PostController@redirectToTagDetails');
    Route::get('questions/{name}', 'Api\PostController@redirectToTagDetails');

    Route::get('explore', 'HomeController@serveApp');//(8-11-17)
    Route::get('tpl.explore', 'Api\ExploreController@index');//(8-11-17)

});

Route::group(['middleware' => ['web', 'auth']], function () {
    /*
     * For angular direct refresh (F5)..
     */
    Route::get('post-add', 'HomeController@serveAppPostAd');
    
    //Route::get('explore', 'HomeController@serveApp');(8-11-17)
    Route::get('explore-test', 'HomeController@serveApp');

    //For Edit Profile
    Route::get('edit-profile', 'HomeController@serveApp');
    Route::get('tpl.edit-my-profile', 'Api\ProfileController@editProfile');
    Route::get('tpl.edit-my-account', 'Api\ProfileController@editMyAccount');
    Route::get('tpl.change-my-password', 'Api\ProfileController@editPassword');

    Route::get('search', 'HomeController@serveApp');
    Route::get('following-topics', 'HomeController@serveApp');
    Route::get('my-analytics', 'HomeController@serveApp');
    Route::get('all-notification', 'HomeController@serveApp');
    
    Route::get('exploretest', 'HomeController@serveApp');
    Route::get('saved-post', 'HomeController@serveApp');
    Route::get('nearby', 'HomeController@serveApp');
    Route::get('invite-friend', 'HomeController@serveApp');
    Route::get('send-feedback', 'HomeController@serveApp');
    Route::get('error-404', 'HomeController@error404');
    
    /*-------------------------------------------------------------*/

    Route::get('auth/facebook_connect', 'Auth\AuthController@redirectToProviderFacebookConnect');
    Route::get('auth/twitter_connect', 'Auth\AuthController@redirectToProviderTwitterConnect');
    Route::get('auth/linkedin_connect', 'Auth\AuthController@redirectToProviderLinkedInConnect');

    /*---- New routes ----*/
    Route::get('/', 'HomeController@serveAppFeed');

    /*========= Ajax routes for Template files =========*/
    
    //Routes For Feed
    Route::get('feeds', 'FeedController@index');
    Route::get('feed/recommendation', 'FeedController@getRandomRecommendation');
    
    Route::get('tpl.header', 'HomeController@serveHeader');
    Route::get('tpl.sidebar', 'HomeController@serveSidebar');
    // error 404
    Route::get('tpl.error_404', 'HomeController@error404');
    // Explore..
    //Route::get('tpl.explore', 'Api\ExploreController@index');(8-11-17)
    Route::get('tpl.test', 'Api\ExploreController@test');
    // Save post
    Route::get('tpl.saved-post', 'Api\ExploreController@savepost');
    // nearby
    Route::get('tpl.nearby', 'Api\ExploreController@nearby');

    // Category
    Route::get('tpl.category', 'Api\ExploreController@category');
    Route::get('tpl.search', 'Api\SearchController@searchResult');
    // Following Topics 
    Route::get('tpl.following-topics', 'Api\ExploreController@followingTopics');
    Route::get('tpl.my-analytics', 'AnalyticsController@index');
    Route::get('tpl.allNotification', 'Api\NotificationController@seeAll');
    /*========= END Ajax routes for Template files =========*/

    Route::get('tpl.topics', 'Api\ExploreController@followingTopicGrid');

    Route::post('angular/followUser', 'AngularController@followThisUser');
    Route::post('angular/unfollowUser', 'AngularController@unfollowThisUser');
    
    Route::post('angular/userProfile', 'AngularController@userProfileJson');
    Route::get('angular/userProfile', 'AngularController@userProfileJson');
    Route::post('angular/followOtherUser', 'AngularController@followOtherUser');
    Route::post('angular/unfollowOtherUser', 'AngularController@unfollowOtherUser');
    Route::post('angular/profileCoverUpload', 'AngularController@profileCoverUpload');
    Route::post('angular/upVotePost', 'AngularController@upVotePost');
    Route::get('angular/upVotePost', 'AngularController@upVotePost');
    Route::post('angular/sendInvite', 'AngularController@sendInvite');
    Route::post('angular/sendFeedback', 'AngularController@sendFeedback');
    Route::get('angular/checkNumberOfInvites', 'AngularController@checkNumberOfInvites');
    
    Route::post('angular/downVotePost', 'AngularController@downVotePost');
    // Route::match(['get', 'post'], 'angular/loadMorePost', 'AngularController@loadMorePost');
    Route::get('angular/loadMoreUserPost', 'AngularController@loadMoreUserPost');
    Route::post('angular/followCategory', 'AngularController@followThisCategory');
    Route::post('angular/unfollowCategory', 'AngularController@unfollowThisCategory');
    
    // For comment section.
    Route::post('comments/post-comment', 'Api\CommentController@postComment');
    Route::post('comments/post-child-comment', 'Api\CommentController@postReply');
    Route::post('comments/delete', 'Api\CommentController@deleteComment');
    Route::post('comments/vote', 'Api\CommentController@voteComment');

    Route::post('angular/doPostReport', 'Api\ProfileController@doPostReport');

    Route::get('account/follower-json', 'AngularController@userFollowerJson');
    Route::get('account/following-json', 'AngularController@userFollowingJson');
    Route::get('account/collection-json', 'AngularController@userCollectionJson');
    
    Route::post('angular/selectedUpAndDownVote', 'Api\ProfileController@selectedUpAndDownVote');
    Route::post('angular/shareThisPost', 'Api\ProfileController@shareThisPost');
    Route::post('angular/bookmark', 'Api\ProfileController@bookmark');

    Route::get('tpl_collections', 'Api\ProfileController@collections');
    Route::get('tpl_explore_tab', 'Api\ProfileController@exploreTab');
    Route::post('angular/deleteMyPost', 'Api\ProfileController@deleteMyPost');
    Route::post('angular/doCommentReport', 'Api\ProfileController@doCommentReport');
    Route::get('tpl.invite_friend', 'Api\ProfileController@inviteFriend');
    Route::get('tpl.send_feedback', 'Api\ProfileController@sendFeedback');

});

Route::get('twitterapp', 'Api\ProfileController@twitterapp');

/*Route::get('sudo-tsm', function() {
    $user = App\Models\User::find(3);
    Auth::login($user);
    return redirect('/');
});*/
