<?php
/*
 | ---------------------------------------------------------------------
 | Routes for partial views
 | ---------------------------------------------------------------------
 */

// Post details page
Route::get('post-details', 'Api\PostController@details');
// Tag page
Route::get('category-tags', 'Api\PostController@categoryTag');
// Place Page
Route::get('tpl/placeIndex', 'Api\PostController@place');

Route::post('loginapi', 'Api\ApiController@loginapi'); 
Route::post('forgotPasswordApi', 'Api\ApiController@forgotPasswordApi'); 

// Authenticated routes...
Route::group(['middleware' => ['api', 'auth']], function () {
    /*--- Post add page ---*/
    Route::get('post-ad', 'Api\PostController@index');
    Route::get('post-ad/container/{post_type}', 'Api\PostController@container');
    Route::get('post-ad/general/{post_type}', 'Api\PostController@generalForm');
    Route::get('post-ad/advance', 'Api\PostController@advanceForm');
    Route::get('post-ad/content', 'Api\PostController@contentForm');
    Route::get('post-ad/social', 'Api\PostController@socialForm');
    Route::get('post-ad/status', 'Api\PostController@statusForm');
    Route::get('post-ad/question', 'Api\PostController@questionForm');
	
	Route::get('auth-twitter', 'Api\PostController@connectTwitter');
    Route::get('twitterCallbackUrl', 'Api\PostController@twitterCallbackUrl');
});

/*---- Routes for angular app ----*/
Route::group(['prefix' => 'api'], function() {

    
    Route::group(['middleware' => 'throttle'], function () {
        Route::match(['get', 'post'], 'mailEarlyAccess', 'HomeController@mailEarlyAccess');
    });

    // Open routes...
    Route::get('main-category', 'Api\CategoryController@parentCat');
    Route::get('sub-category/{id}', 'Api\CategoryController@subCat');
    Route::get('tag', 'Api\ApiController@tag');
    Route::get('privacy', 'Api\ApiController@privacy');
    Route::post('getExternalPageInfo', 'Api\ApiController@getExternalPageInfo');
    Route::get('getExternalPageInfo', 'Api\ApiController@getExternalPageInfo');
    // Get Domain name
    Route::post('getDomainName', 'Api\ApiController@getDomainName');
	// Post details json.
    Route::get('post-details/{id}', 'Api\PostController@detailsJSON');
    // Edit post json.
    Route::get('edit-post-details/{id}', 'Api\PostController@editPostJson');
    Route::get('tag-details/{tag}', 'Api\PostController@tagDetailsJson');

    // Tag page.
    Route::match(['get', 'post'], 'category-tags', 'Api\PostController@categoryTagJson');
    Route::match(['get', 'post'], 'tag-top-channel', 'Api\PostController@tagTopChannel');
    
    // PLace page.
    Route::match(['get', 'post'], 'place', 'Api\PostController@placeJson');
    Route::match(['get', 'post'], 'place-top-channel', 'Api\PostController@placeTopChannel');
    Route::match(['get', 'post'], 'place-text', 'Api\PostController@getPlaceName');

    //  Search
    Route::match(['get', 'post'], 'search', 'Api\SearchController@searchJson');

    //Search Tag
    Route::match(['get', 'post'], 'searchTag', 'Api\PostController@searchTagJson');// (06-03-18)


    Route::get('main-category-with-cover', 'Api\ExploreController@getMainCategoriesWithCover');//(8-11-17)
    Route::get('todays-trending-topics', 'Api\ExploreController@todaysTrendingTopics');//(8-11-17)
    Route::match(['get', 'post'], 'explore', 'Api\ExploreController@exploreJson');//(8-11-17)
    Route::post('user/save-user-location', 'Api\ProfileController@saveUserLocation');//(8-11-17)
    Route::match(['get', 'post'], 'category-top-channel', 'Api\ExploreController@catTopChannel');//(8-11-17)
    	
    // Authenticated routes...
    Route::group(['middleware' => ['web', 'auth']], function () {
        Route::match(['get', 'post'], 'top-search', 'Api\ApiController@topSearchJson');
        // For collection..
        Route::get('user-collection', 'Api\ApiController@userCollection');
        Route::post('post/store-collection', 'Api\PostController@storeCollectionFromPost');
        // For tags that user follows.
        Route::get('user/all-tags', 'UserController@followingTags');
        Route::get('user/all-info', 'Api\ProfileController@getAllInfo');
        Route::post('user/all-states', 'Api\ProfileController@getAllStates');
        Route::post('user/save-userdata', 'Api\ProfileController@saveUserData');
        Route::post('user/profilePictureUpload', 'Api\ProfileController@saveProfilePicture');
        Route::post('user/check-username-email', 'Api\ProfileController@checkUnqiueUsernameEmail');
       // Route::post('user/save-user-location', 'Api\ProfileController@saveUserLocation');//(8-11-17)
        Route::post('user/change-password', 'Api\ProfileController@changePassword');
        Route::get('user/tags', 'UserController@tags');

        Route::post('saveImageToLocal', 'Api\ApiController@saveImageToLocal');

        // Report
        Route::post('fetchReportPostData', 'Api\PostController@fetchReportPostData');
        Route::post('fetchReportCommentData', 'Api\CommentController@fetchReportCommentData');

        // Post Add
        Route::post('post-image', 'Api\PostController@storeImagePost');
        Route::post('post-video', 'Api\PostController@storeVideoPost');
        Route::post('post-article', 'Api\PostController@storeArticlePost');
        Route::post('post-status', 'Api\PostController@storeStatusPost');
        Route::post('post-question', 'Api\PostController@storeQuestionPost');

        // Post Edit
        Route::post('post-edited-image', 'Api\PostController@storeEditedImagePost');
        Route::post('post-edited-video', 'Api\PostController@storeEditedVideoPost');
        Route::post('post-edited-article', 'Api\PostController@storeEditedArticlePost');
        Route::post('post-edited-status', 'Api\PostController@storeEditedStatusPost');
        Route::post('post-edited-question', 'Api\PostController@storeEditedQuestionPost');

         //fetch Analytics Route
        Route::get('fetchAnalytics', 'AnalyticsController@getAllAnalytics');
        Route::post('getParticularAnalyticsReport', 'AnalyticsController@getParticularAnalyticsReport');
        // Category or Tag follow.
        Route::post('tag-follow-unfollow', 'Api\ApiController@tagFollowUnfollow');
        Route::post('all-cat-follow-unfollow', 'Api\ApiController@allCatFollowUnfollow');
        // Place follow.
        Route::post('place-follow-unfollow', 'Api\ApiController@placeFollowUnfollow');
        // Explore page.
        //Route::match(['get', 'post'], 'explore', 'Api\ExploreController@exploreJson');//(8-11-17)
        // Route::match(['get', 'post'], 'category-top-channel', 'Api\ExploreController@catTopChannel');//(8-11-17)

        //Route::get('main-category-with-cover', 'Api\ExploreController@getMainCategoriesWithCover');//(8-11-17)

        // Feeds page.
        Route::match(['get', 'post'], 'feeds', 'FeedController@feedsJson');

        // For testing purpose only.
        // Route::get('test-post-point/{post_id}', 'FeedController@getFavouriteTopicPostTest');

        // Save post.
        Route::match(['get', 'post'], 'savepost', 'Api\ProfileController@savepostJson');

        //Get Nearby Post
        Route::match(['get', 'post'], 'fetchNearbyPost', 'Api\ExploreController@fetchNearbyPostJson');

        // Api for uploading photo to server.
        Route::post('upload-photo', 'Api\PostController@uploadPhoto');
        Route::post('upload-video', 'Api\PostController@uploadVideo');

        // Notification Api
        Route::match(['get', 'post'], 'notification', 'Api\NotificationController@getNotification');
        Route::match(['get', 'post'], 'all-notification', 'Api\NotificationController@getAllNotification');
        Route::post('mark-notification-read', 'Api\NotificationController@markNotificationRead');
        Route::post('mark-notification-seen', 'Api\NotificationController@markNotificationSeen');
    });
});