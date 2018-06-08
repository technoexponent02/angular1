/* ============================================================
 * directives.js
 * All common functionality & their respective directive
 * ============================================================ */

angular.module('app')
    .directive('postcardModal', function () {
        return {
            restrict: 'EA',
            templateUrl: "tpl_postcardmodal",
            link: function ($scope, element, attrs) {
                element.bind('click', function (event) {
                    $scope.$apply(attrs.postcardModal);
                });
            }

        };
    });

angular.module('app')
    .directive('postcard', function () {
        return {
            restrict: 'A',
            link: function ($scope, element, attrs) {
                element.bind('click', function (event) {
                    $scope.$apply(attrs.postcard);
                });
            },
            controller: function ($scope, $http, $timeout, inArray, $state) {
                // Open share popup..
                $scope.popUpDropdrow = 0;
                $scope.openSharePostPopUp = function (post_id) {

                };

                /*---------- Open popUp drop down menu when user click on .... sing ----------------------- */
                // This function user for postcard modal and post card details ..
                $scope.report = function (post) {  

                    var $newShrBtns = $(".newShrBtns");
                    $newShrBtns.find(".otherSubsh").show();
                    $newShrBtns.find(".subOverlaysh").show();

                    // alert('clicked');

                    // if($newShrBtns.find(".otherSubsh").parents('.profileCommentBoxTop').hasClass('has-big-zindex')){
                    //     $newShrBtns.find(".otherSubsh").parents('.profileCommentBoxTop').removeClass('has-big-zindex')
                    // } else {
                    //     $newShrBtns.find(".otherSubsh").parents('.profileCommentBoxTop').addClass('has-big-zindex')
                    // }


                    var flag = 0;
                    if ($state.current.name == 'profile') {
                        if (post.child_post_user_id != $scope.user.id) {
                            flag = 1;
                        } else {
                            flag = 0;
                        }
                    }
                    else if ($state.current.name == 'account') {
                        if (post.created_by != $scope.user.id) {
                            flag = 1;
                        } else {
                            flag = 0;
                        }
                    }
                    else {
                        if (post.created_by == $scope.user.id || post.child_post_user_id == $scope.user.id) {
                            flag = 0;
                        }
                        else {
                            flag = 1;
                        }
                    }
                    $scope.isShowPostReportLink = flag;
                };

                $scope.closeOverLayout = function () {
                    $(".subOverlaysh , .otherSubsh").hide();
                };
            }
        };
    });

angular.module('app')
    .directive('webscrolling', function () {
        return {
            restrict: 'A',
            link: function (scope, element, attrs) {
                element.bind("scroll", function () {

                    var scrollTopHeightM = 5;
                    var scrollOuterPosM = $(".modalMobileScroll > .row").offset().top - 38;
                    var scrollOuterPosnewM = Math.abs(scrollOuterPosM)
                    var scrollPercentM = 100 * (scrollOuterPosnewM - scrollTopHeightM) / ($("#myModal .profileNewLeft").height() - $(".modalMobileScroll").height());
                    if ($(".profileNewLeft").innerHeight() >= $(".modalMobileScroll").innerHeight() && scrollOuterPosM <= 0) {
                        $(".mobileBarLong").css("width", scrollPercentM + "%");
                    }
                });
            }
        }
    });
angular.module('app')
    .directive('webscrolling2', function () {
        return {
            restrict: 'A',
            link: function (scope, element, attrs) {
                $(window).bind("scroll", function () {
                    var scrollTopHeightM2 = 5;
                    var scrollOuterPosM2 = $("body").scrollTop() - 38;
                    var scrollOuterPosnewM2 = Math.abs(scrollOuterPosM2)
                    var scrollPercentM2 = 100 * (scrollOuterPosnewM2 - scrollTopHeightM2) / ($(".cardDetailsPG_modal .profileNewLeft").height() - $(window).height());

                    if ($(".cardDetailsPG_modal .profileNewLeft").innerHeight() >= $(window).height() && scrollOuterPosM2 >= 0) {
                        $(".mobileBarLong2").css("width", scrollPercentM2 + "%");
                    }
                });
            }
        }
    });


/* ======================================================================
 * Directive: OTHERS , DELETE POSTCARD MODAL AND REPORT POST CARD MODAL 
 * Prepare Bootstrap dropdowns to match Pages theme
 * ===================================================================== */
angular.module('app')
    .directive('postCardMenu', function ($rootScope, $http) {
        return {
            restrict: 'E',
            link: function ($scope, element, attrs) {
                $scope.openDeletePostModal = function (post_id) {
                    $rootScope.mypostid = post_id;
                    $scope.closeOverLayout();
                };
                // Report post.
                $scope.openReportPostModal = function (post_id) {
                    // Initialize.
                    $rootScope.report_post_ids = [];
                    $rootScope.reportPostModalLoader = true;

                    var url = '/api/fetchReportPostData';
                    var postData = {
                        post_id: post_id
                    };
                    $http.post(url, postData)
                        .then(function (response) {
                            var report_ids = response.data.report_ids;
                            if (report_ids.length > 0) {
                                report_ids.forEach(function (v) {
                                    $rootScope.report_post_ids.push(v);
                                });
                            }
                            $rootScope.reportPostModalLoader = false;
                        },
                        function (response) {
                            $rootScope.reportPostModalLoader = false;
                        }
                    );

                    $rootScope.mypostid = post_id;
                    $scope.closeOverLayout();
                };

                // After clicking downvote then postcard other will be closed 
                $scope.closeOverLayout = function () {
                    $scope.popUpDropdrow = 0;
                    $scope.popUpReportDropdrow = 0;
                };
            },
            templateUrl: 'tpl.post-card-menu'
        };
    });


angular.module('app')
    .directive('deletePostModal', function ($http) {
        return {
            restrict: 'E',
            link: function ($scope, element, attrs) {
                // Delete postcard and also their child postcard also..
                $scope.deleteMyPost = function (post_id) {
                    $scope.disableClick = true;
                    $http({
                        method: 'POST',
                        url: "angular/deleteMyPost",
                        params: {post_id: post_id}
                    }).then(function (response) {
                        location.href = 'profile';
                    }, function (response) {
                        alert('Sorry! we cannot process your request.');
                    });
                }
            },
            templateUrl: 'tpl_deletePostModal'
        };
    });

angular.module('app')
    .directive('reportPostModal', function () {
        return {
            restrict: 'E',
            link: function (scope, element, attrs) {

            },
            templateUrl: 'tpl_reportPostModal',
            controller: function ($scope, $log, $http) {
                $scope.doPostReport = function (post_id, report_id) {

                    $("#reportPostModal").modal("toggle");

                    $http({
                        method: 'POST',
                        url: "angular/doPostReport",
                        params: {
                            post_id: post_id,
                            report_id: report_id
                        }
                    }).then(function (response) {

                    }, function (response) {
                        // $log.info('Some error is occurred.');
                    });
                }
            }
        };
    });


/* ============================================================
 * Directive: POST UPVOTES AND DOWNVOTES 
 * Prepare Bootstrap dropdowns to match Pages theme
 * ============================================================ */

angular.module('app')
    .directive('upvotes', function () {
        return {
            restrict: 'A',
            link: function (scope, element, attrs) {
                element.bind('click', function (event) {
                    scope.$apply(attrs.upvotes);
                });
            },
            controller: function ($scope, $http, inArray, $state, userDataService) {

                $scope.doUpvotes = function (post_id, childPostId, type) {

                    //  Dynamic binding  new edition 24-11-2016 

                    if (angular.isArray($scope.commonData.allPosts)) {
                        angular.forEach($scope.commonData.allPosts, function (value, key) {
                            if (value.id == post_id) {
                                if ($scope.commonData.allPosts[key].isUpvote == 'N') {
                                    $scope.commonData.allPosts[key].isUpvote = 'Y';
                                    $scope.commonData.allPosts[key].upvotes = $scope.commonData.allPosts[key].upvotes + 1;
                                } else {
                                    $scope.commonData.allPosts[key].isUpvote = 'N';
                                    //console.log('2');
                                    $scope.commonData.allPosts[key].upvotes = $scope.commonData.allPosts[key].upvotes - 1;
                                }
                                if ($scope.commonData.allPosts[key].isDownvote == 'Y') {
                                    $scope.commonData.allPosts[key].downvotes = $scope.commonData.allPosts[key].downvotes - 1;
                                    $scope.commonData.allPosts[key].isDownvote = 'N';
                                }

                            }
                        });
                    }

                    // For postcard 
                    if (type == 'M') {
                        if ($scope.post.isUpvote == 'N') {
                            $scope.post.isUpvote = 'Y';
                            $scope.post.upvotes = $scope.post.upvotes + 1
                            // Update post point on modal :: 
                            // if logged in user and post creator is same then point is not update 
                            if ($scope.post.created_by != $scope.user.id) {
                                $scope.post.points = $scope.post.points + 2;
                            }
                        } else {
                            $scope.post.isUpvote = 'N';
                            $scope.post.upvotes = $scope.post.upvotes - 1
                            // Update post point on modal ::
                            // if logged in user and post creator is same then point is not update 
                            if ($scope.post.created_by != $scope.user.id) {
                                $scope.post.points = $scope.post.points - 2;
                            }
                        }

                        if ($scope.post.isDownvote == 'Y') {
                            $scope.post.downvotes = $scope.post.downvotes - 1;
                            $scope.post.isDownvote = 'N';
                            // Update post point on modal :: 
                            // if downvote cancel then execute ...
                            // if logged in user and post creator is same then point is not update 
                            if ($scope.post.created_by != $scope.user.id) {
                                $scope.post.points = $scope.post.points + 2;
                            }
                        }
                    }

                    //  end Dynamic binding  new edition 24-11-2016 
                    $http({
                        method: "POST",
                        url: "angular/upVotePost",
                        params: {post_id: post_id, childPostId: childPostId}
                    }).then(function (response) {

                        if (!$scope.post) {
                            $scope.post = {};
                            $scope.post.user = {};
                        }

                        /* ============================================================
                         * For Updating profile points 
                         * ============================================================ */

                        if (angular.isArray($scope.post.getUser)) {
                            angular.forEach($scope.post.getUser, function (value, key) {

                                angular.forEach(response.data.user, function (val, k) {
                                    if (value.id == val.id) {
                                        $scope.post.getUser[key].points = val.points;
                                    }
                                });
                            });
                        }


                        // For updateing profile points 
                        if ($state.current.name == 'profile' || $state.current.name == 'account') {
                            if ($scope.userData.id != $scope.user.id) {
                                var user_points = response.data.user[0].points;
                                userDataService.updateUserData(user_points);
                                userDataService.getData();
                            }
                        }

                    });
                };

                $scope.doDownVotes = function (post_id, childPostId, type) {

                    //  Dynamic binding  new edition 24-11-2016 
                    if (angular.isArray($scope.commonData.allPosts)) {
                        angular.forEach($scope.commonData.allPosts, function (value, key) {
                            if (value.id == post_id) {

                                if ($scope.commonData.allPosts[key].isDownvote == 'N') {
                                    $scope.commonData.allPosts[key].isDownvote = 'Y';
                                    $scope.commonData.allPosts[key].downvotes = $scope.commonData.allPosts[key].downvotes + 1;
                                } else {
                                    $scope.commonData.allPosts[key].isDownvote = 'N';
                                    $scope.commonData.allPosts[key].downvotes = $scope.commonData.allPosts[key].downvotes - 1;
                                }
                                if ($scope.commonData.allPosts[key].isUpvote == 'Y') {
                                    $scope.commonData.allPosts[key].upvotes = $scope.commonData.allPosts[key].upvotes - 1;
                                    $scope.commonData.allPosts[key].isUpvote = 'N';
                                }

                            }
                        });
                    }

                    // For postcard 
                    if (type == 'M' || type == 'PD') {  // M for modal
                        if ($scope.post.isDownvote == 'N') {
                            $scope.post.isDownvote = 'Y';
                            $scope.post.downvotes = $scope.post.downvotes + 1
                            // Update post point on modal ::
                            // if logged in user and post creator is same then point is not update 
                            if ($scope.post.created_by != $scope.user.id) {
                                $scope.post.points = $scope.post.points - 2;
                            }
                        } else {
                            $scope.post.isDownvote = 'N';
                            $scope.post.downvotes = $scope.post.downvotes - 1
                            // Update post point on modal :: 
                            // if logged in user and post creator is same then point is not update 
                            if ($scope.post.created_by != $scope.user.id) {
                                $scope.post.points = $scope.post.points + 2;
                            }
                        }

                        if ($scope.post.isUpvote == 'Y') {
                            $scope.post.upvotes = $scope.post.upvotes - 1;
                            $scope.post.isUpvote = 'N';
                            // Update post point on modal :: 
                            // if upvote cancel then execute ...
                            // if logged in user and post creator is same then point is not update 
                            if ($scope.post.created_by != $scope.user.id) {
                                $scope.post.points = $scope.post.points + 2;
                            }
                        }
                    }

                    //  Dynamic binding  new edition 24-11-2016 
                    $http({
                        method: "POST",
                        url: "angular/downVotePost",
                        params: {post_id: post_id, childPostId: childPostId}

                    }).then(function (response) {

                        if (!$scope.post) {
                            $scope.post = {};
                            $scope.post.user = {};
                        }

                        /* ============================================================
                         * For Updating profile points 
                         * 
                         * ============================================================ */
                        if (angular.isArray($scope.post.getUser)) {
                            angular.forEach($scope.post.getUser, function (value, key) {

                                angular.forEach(response.data.user, function (val, k) {
                                    if (value.id == val.id) {
                                        $scope.post.getUser[key].points = val.points;
                                    }
                                });
                            });
                        }

                        // For updateing profile points 
                        if ($state.current.name == 'profile' || $state.current.name == 'account') {
                            if ($scope.userData.id != $scope.user.id) {
                                var user_points = response.data.user[0].points;
                                userDataService.updateUserData(user_points);
                                userDataService.getData();
                            }
                        }

                        $(".subOverlaysh , .otherSubsh").hide();
                    });
                };
            }
        };
    });

/* ============================================================
 * Directive: Open share post modal, share post 
 * Prepare Bootstrap dropdowns to match Pages theme
 * ============================================================ */


angular.module('app')
    .directive('sharepostCard', function () {
        return {
            restrict: 'E',
            templateUrl: "tpl.sharepost-card",
            link: function (scope, element, attrs) {

            },
            controller: function ($scope, $http, $timeout) {
                // Initialize privacies..
                $scope.privacies = {};
                // Fetch privacies..
                $http.get('api/privacy').then(function (response) {
                    if (response.data) {
                        $scope.privacies = response.data;
                        $scope.privacy_id = $scope.privacies[0];
                    }
                });

                $scope.sharePopUp = function (post_id, childPostUserId, childPostId, is_openFromPostcardModal) {

                    $scope.sharedPost = {};
                    var url = 'angular/showPostDetails';
                    var postData = {
                        post_id: post_id,
                        child_post_id: childPostId,
                        is_briefed: 1,
                        initiator: 'share_popup'
                    };
                    $http.post(url, postData)
                        .then(function (response) {

                        $scope.sharedPost = response.data.post;
                        //  $scope.loginUser = response.data.user;

                        $scope.popUpDropdrow = 0;
                        $scope.postId = post_id;
                        $scope.caption = '';
                        $scope.postUserId = childPostUserId;
                        $scope.childPostId = childPostId;

                        // find original post creator ::
                        var postModalUser = response.data.post.getUser;
                        angular.forEach(postModalUser, function (value, key) {
                            $scope.originalPostUserName = value.username;
                            $scope.originalPostFirstName = value.first_name;
                            $scope.originalPostLastName = value.last_name;
                            $scope.originalPostProfileImage = value.thumb_image_url;
                            $scope.originalPostUserColor = (value.id % 2 == 0) ? 's1' : 's0';

                        });

                        $timeout(function () {
                            angular.element("#shareModal").modal();
                            angular.element(".subOverlay").triggerHandler("click");
                            $timeout(function () {
                                angular.element("html").addClass("scrollHidden");
                                angular.element("body").addClass("modal-open");
                            }, 1000);
                            if (is_openFromPostcardModal == 1) {
                                $("#myModal").modal("toggle");
                            }
                        }, 100);
                    });
                };


            }
        };
    });

angular.module('app')
    .directive('share', function (notify, $state) {
        return {
            restrict: 'A',
            link: function (scope, element, attrs) {
                element.bind('click', function () {
                    scope.$apply(attrs.share);
                });
            },
            controller: function ($scope, $http, $timeout) {
                $scope.shareThisPost = function (post_id, childPostId, postUserId) {
                    $http({
                        method: "POST",
                        url: "angular/shareThisPost",
                        params: {
                            caption: $scope.caption,
                            post_id: $scope.postId,
                            profile_id: postUserId,
                            childPostId: childPostId,
                            privacy_id: $scope.privacy_id
                        }
                    }).then(function (response) {

                        $("#shareModal").modal('toggle');
                        $(".sharedSuccessLoader").show();
                        $timeout(function () {
                            $(".sharedSuccessLoader").hide();
                        }, 2000);
                        $timeout(function () {
                            var notification = {
                                'message': 'You have successfully re-share this post.',
                                'color': 'success',
                                'timeout': 5000
                            };
                            notify(notification);
                        }, 2100);
                        $timeout(function () {
                            $state.go("profile");
                        }, 3000);

                    });
                };


            }
        };
    });

/* ============================================================
 * Directive: Postview Scrolling 
 * ============================================================ */
angular.module('app')

    .directive('viewpost', function (userDataService) {
        return {
            restrict: "A",
            link: function (scope, element, attrs) {
                element.bind("click", function () {
                    scope.$apply(attrs.viewpost);
                });
            },
            controller: function ($scope, $http, $window, $state) {
                $scope.externalLink = function (postID, childPostID) {
                    $http({
                        method: "POST",
                        url: "angular/viewPost",
                        data: {
                            postID: parseInt(postID),
                            childPostID: childPostID,
                            postType: 4
                        }
                    }).then(function (response) {

                        // userData is update on proilfe or account .
                        if ($state.current.name == 'profile' || $state.current.name == 'account') {
                            var points = response.data.post.getUsers[0].points;
                            userDataService.updateUserData(points);
                            userDataService.getData();
                        }

                        // Update dynamically total number of post view
                        var p;
                        for (p in $scope.commonData.allPosts) {
                            if ($scope.commonData.allPosts[p].id == postID) {
                                $scope.commonData.allPosts[p].totalPostViews = response.data.post.totalPostViews;
                                // break;
                            }
                        }
                        // update user points dynamically on popup........
                        if (angular.isArray($scope.post.getUser)) {
                            angular.forEach($scope.post.getUser, function (value, key) {
                                angular.forEach(response.data.post.getUsers, function (val, k) {
                                    if (value.id == val.id) {
                                        $scope.post.getUser[key].points = val.points;
                                    }
                                });
                            });
                        }
                        // update total access on popup ::
                        $scope.post.totalPostViews = response.data.post.totalPostViews;
                        $scope.post.points = response.data.post.points;

                        // $window.open(external_link, '_blank');
                    }, function (error) {
                        // console.log('Error occurred.');
                    });

                };


            }
        };
    });


/* ============================================================
 * Directive: PROFILE NAV SLIDER
 * ============================================================ */
angular.module('app')
    .directive('exploreTab', function () {
        return {
            restrict: 'E',
            templateUrl: "tpl_explore_tab"
        };
    });


/*angular.module("app")
 .filter('displayBestTab', function () {
 return function (x) {

 };
 });*/

/* ============================================================
 * Directive: Follow users
 * ============================================================ */

angular.module('app')
    .directive('allfollowuser', function () {
        return {
            restrict: 'A',
            link: function ($scope, element, attrs) {
                element.bind('click', function (event) {
                    $scope.$apply(attrs.allfollowuser);
                });
            },
            controller: function ($scope, $http, $timeout, inArray, $state) {
                /* ============================================================
                 * Functionality: Profile page follow,unfollow only postcard modal
                 * ============================================================ */

                $scope.followUser = function (user_id, type, following) {

                    // For instance update
                    if ($state.current.name == 'profile' || $state.current.name == 'account') {
                        if ($scope.userData.username == $scope.user.username) {
                            if (inArray.arrayIndexOf($scope.userFollowing, user_id) != -1) {
                                $scope.userFollowing.splice(inArray.arrayIndexOf($scope.userFollowing, user_id), 1);
                                angular.isArray($scope.userData.following)
                                {
                                    angular.forEach($scope.userData.following, function (val, key) {
                                        if (val.user_id == user_id) {
                                            $scope.userData.following.splice(key, 1);
                                            $scope.userDataTotalFollowing = $scope.userData.following.length;
                                            return false;
                                        }
                                    });
                                }
                                $scope.userData.userDataTotalFollowing = $scope.userFollowing.length;

                                if (angular.isArray($scope.post.getUser)) {
                                    angular.forEach($scope.post.getUser, function (value, key) {

                                        if (value.id === user_id) {
                                            $scope.post.getUser[key].is_follow = $scope.post.getUser[key].is_follow - 1;
                                        }
                                    });
                                }
                            }
                            else {
                                $scope.userFollowing.push(parseInt(user_id));
                                $scope.userData.userDataTotalFollowing = $scope.userData.following.length;

                                if (angular.isArray($scope.post.getUser)) {
                                    angular.forEach($scope.post.getUser, function (value, key) {

                                        if (value.id === user_id) {
                                            $scope.post.getUser[key].is_follow = $scope.post.getUser[key].is_follow + 1;
                                        }
                                    });
                                }
                            }
                        }
                        else {
                            if (inArray.arrayIndexOf($scope.userFollowing, user_id) != -1) {
                                $scope.userFollowing.splice(inArray.arrayIndexOf($scope.userFollowing, user_id), 1);

                                if (angular.isArray($scope.post.getUser)) {
                                    angular.forEach($scope.post.getUser, function (value, key) {

                                        if (value.id === user_id) {
                                            $scope.post.getUser[key].is_follow = $scope.post.getUser[key].is_follow - 1;
                                        }
                                    });
                                }
                                $scope.resetPostData();
                                $scope.loadMore('all');
                            }
                            else {
                                $scope.userFollowing.push(parseInt(user_id));
                                if (angular.isArray($scope.post.getUser)) {
                                    angular.forEach($scope.post.getUser, function (value, key) {

                                        if (value.id === user_id) {
                                            $scope.post.getUser[key].is_follow = $scope.post.getUser[key].is_follow + 1;
                                        }
                                    });
                                }
                                $scope.userData.userDataTotalFollower = $scope.userData.userDataTotalFollower + 1;
                                $scope.resetPostData();
                                $scope.loadMore('all');
                            }
                        }
                    }
                    else {
                        if (inArray.arrayIndexOf($scope.userFollowing, user_id) != -1) {
                            $scope.userFollowing.splice(inArray.arrayIndexOf($scope.userFollowing, user_id), 1);

                            // start this block use for  post detail page
                            if ($scope.post && angular.isArray($scope.post.getUser)) {
                                angular.forEach($scope.post.getUser, function (value, key) {
                                    if (value.id === user_id) {
                                        $scope.post.getUser[key].is_follow = $scope.post.getUser[key].is_follow - 1;
                                    }
                                });
                            }
                        }
                        else {
                            $scope.userFollowing.push(parseInt(user_id));

                            // start this block use for  post detail page
                            if ($scope.post && angular.isArray($scope.post.getUser)) {
                                angular.forEach($scope.post.getUser, function (value, key) {

                                    if (value.id === user_id) {
                                        $scope.post.getUser[key].is_follow = $scope.post.getUser[key].is_follow + 1;
                                    }
                                });
                            }
                        }
                    }
                    // end
                    $http({
                        method: "POST",
                        url: "angular/followUser",
                        params: {user_id: user_id, following: following}
                    }).then(function (response) {

                        if ($state.current.name == 'profile' || $state.current.name == 'account') {
                            if ($scope.userData.username == $scope.user.username) {
                                if (inArray.arrayIndexOf($scope.userFollowing, user_id) != -1) {

                                } else {
                                    $scope.userData.following.push(response.data.getFollower);
                                }
                            }
                        }

                    });
                };

                /* =======================================================================
                 * Functionality: Follow user form follower tab .
                 If login user follow from own profile then increase number of following
                 * ====================================================================== */


                $scope.followUserFromFollowerTab = function (user_id, type, following) {

                    if ($scope.userData.username == $scope.user.username) {


                        if (inArray.arrayIndexOf($scope.userFollowing, user_id) != -1) {
                            $scope.userFollowing.splice(inArray.arrayIndexOf($scope.userFollowing, user_id), 1);
                            $scope.userData.userDataTotalFollowing = $scope.userData.userDataTotalFollowing - 1;
                        }
                        else {

                            $scope.userFollowing.push(parseInt(user_id));
                            $scope.userData.userDataTotalFollowing = $scope.userData.userDataTotalFollowing + 1;
                        }


                    }
                    else {
                        if (inArray.arrayIndexOf($scope.userFollowing, user_id) != -1) {
                            $scope.userFollowing.splice(inArray.arrayIndexOf($scope.userFollowing, user_id), 1);
                        }
                        else {
                            $scope.userFollowing.push(parseInt(user_id));
                        }
                    }


                    $http({
                        method: "POST",
                        url: "angular/followUser",
                        params: {user_id: user_id, following: following}
                    }).then(function (response) {


                    });
                };


                /* =======================================================================
                 Functionality: Follow user form following tab .
                 If login user follow from own profile then increase number of following
                 ====================================================================== */

                $scope.followUserFromFollowingTab = function (user_id, type, following) {

                    if ($scope.userData.username == $scope.user.username) {
                        if (inArray.arrayIndexOf($scope.userFollowing, user_id) != -1) {
                            $scope.userFollowing.splice(inArray.arrayIndexOf($scope.userFollowing, user_id), 1);
                            $scope.userData.userDataTotalFollowing = $scope.userData.userDataTotalFollowing - 1;
                        }
                        else {
                            $scope.userFollowing.push(parseInt(user_id));
                            $scope.userData.userDataTotalFollowing = $scope.userData.userDataTotalFollowing + 1;
                        }
                    }
                    else {
                        if (inArray.arrayIndexOf($scope.userFollowing, user_id) != -1) {
                            $scope.userFollowing.splice(inArray.arrayIndexOf($scope.userFollowing, user_id), 1);
                        }
                        else {
                            $scope.userFollowing.push(parseInt(user_id));
                        }
                    }


                    $http({
                        method: "POST",
                        url: "angular/followUser",
                        params: {user_id: user_id, following: following}
                    }).then(function (response) {

                    });
                }

                /* =======================================================================
                 * Functionality: Follow user form timeline follow tab and tag page follow button
                 * postcard and postcard modal
                 * ====================================================================== */

                $scope.followTab = function (user_id, type, following) {

                    if (type == 'T') {
                        if (inArray.arrayIndexOf($scope.userFollowing, user_id) != -1) {
                            $scope.userFollowing.splice(inArray.arrayIndexOf($scope.userFollowing, user_id), 1);
                            $scope.userData.userDataTotalFollower = $scope.userData.userDataTotalFollower - 1;
                            $scope.resetPostData();
                            //$scope.loadMore('all');
                            setTimeout(function () {
                                $scope.loadMore('all');
                            }, 500);
                        }
                        else {
                            $scope.userFollowing.push(parseInt(user_id));
                            $scope.userData.userDataTotalFollower = $scope.userData.userDataTotalFollower + 1;
                            $scope.resetPostData();
                            //$scope.loadMore('all');
                            setTimeout(function () {
                                $scope.loadMore('all');
                            }, 500);
                        }
                    }
                    $http({
                        method: "POST",
                        url: "angular/followUser",
                        params: {user_id: user_id, following: following}
                    }).then(function (response) {


                    });
                }

            }
        };
    });

/* ============================================================
 * Directive: Report comments 
 * ============================================================ */

angular.module('app')
    .directive('reportCommentModal', function () {
        return {
            restrict: 'E',
            link: function (scope, element, attrs) {

            },
            templateUrl: 'tpl_reportCommentModal',
            controller: function ($scope, $log, $http) {

                $scope.openReportCommentModal = function (comment_id) {
                    // Initialize.
                    $scope.report_comment_ids = [];
                    $scope.reportCommentModalLoader = true;

                    var url = '/api/fetchReportCommentData';
                    var postData = {
                        comment_id: comment_id
                    };
                    $http.post(url, postData)
                    .then(function (response) {
                            var report_ids = response.data.report_ids;
                            if (report_ids.length > 0) {
                                report_ids.forEach(function (v) {
                                    $scope.report_comment_ids.push(v);
                                });
                            }
                            $scope.reportCommentModalLoader = false;
                        },
                        function () {
                            $scope.reportCommentModalLoader = false;
                        }
                    );

                    $scope.comment_id = comment_id;
                    $scope.closeOverLayout();
                };


                $scope.doCommentReport = function (commentId, reportId) {
                    //$("#reportCommentModal").modal("toggle");
                    $('.reporPopupVW').hide();

                    $http({
                        method: 'POST',
                        url: "angular/doCommentReport",
                        data: {
                            commentId: commentId,
                            reportId: reportId
                        }
                    }).then(function (response) {


                    }, function (response) {
                        $log.info('Some error is occurred.');
                    });
                }

            }
        };
    });

/* ============================================================
 * Directive: Social sharing 
 * Facebook Share API
 * ============================================================ */
angular.module('app')
    .directive('socialSharing', function ($window, $http, $interval, notify, $timeout) {
        return {
            restrict: "A",
            link: function ($scope, element, attrs) {
                element.bind('click', function (event) {
                    $scope.$apply(attrs.socialSharing);
                });

                $scope.facebook = function (post, type) {

                    $window.fbAsyncInit = function () {
                        FB.init({
                            appId: '1050589168353691',
                            status: true,
                            cookie: true,
                            xfbml: true,
                            version: 'v2.4'
                        });
                    };

                    var obj = {};
                    var thumbnail = '';

                    obj.method = 'feed';

                    if (post.post_type == 5) {  // :: For status post ::
                        //StrippedString = OriginalString.replace(/(<([^>]+)>)/ig,"");
                        obj.title = post.caption.replace(/(<([^>]+)>)/ig, "");
                    } else {
                        if (post.title != '') {
                            obj.title = post.title.replace(/(<([^>]+)>)/ig, "");
                        } else {
                            obj.title = post.caption.replace(/(<([^>]+)>)/ig, "");
                        }
                    }

                    if (post.post_url != '') {
                        obj.link = post.post_url;
                    }

                    if (post.post_type == 3) {   // :: For article post ::

                        if (post.short_description == '') {
                            var OriginalString = post.content;
                            var cont = OriginalString.replace(/(<([^>]+)>)/ig, "");
                            obj.description = cont.substr(0, 100);
                        } else if (post.short_description != '') {
                            obj.description = post.short_description;
                        } else {
                            obj.description = "  ";
                        }

                    } else {

                        if (post.short_description != '') {
                            obj.description = post.short_description;
                        } else {
                            obj.description = "  ";
                        }
                    }

                    if (post.post_type == 1 || post.post_type == 3 || post.post_type == 4) {
                        if (post.image != '') {
                            obj.picture = post.image;
                        }
                    } else if (post.post_type == 2) {   // For video post ..

                        if (post.embed_code != '') {	 // for embed code
                            if (post.embed_code_type == 'youtube') {

                                var pic = 'https://img.youtube.com/vi/' + post.videoid + '/0.jpg';
                                obj.picture = pic;

                            } else if (post.embed_code_type == 'dailymotion') {

                                thumbnail = 'https://www.dailymotion.com/thumbnail/video/' + post.videoid;
                                obj.picture = thumbnail;

                            } else if (post.embed_code_type == 'vimeo') {
                                thumbnail = 'https://i.vimeocdn.com/video/' + post.videoid + '_640.jpg';
                                obj.picture = thumbnail;
                            }
                            //obj.source=post.embed_code;
                        } else if (post.video != '') { // html5

                            var video_poster = post.video_poster;
                            obj.picture = video_poster;

                            var url = post.video
                            obj.source = url;
                        }
                    } else if (post.post_type == 5) {  // for status post

                        if (post.image != '') {
                            obj.picture = post.image;
                        } else {

                            if (post.embed_code != '') {    // for embed code
                                if (post.embed_code_type == 'youtube') {
                                    var pic = 'https://img.youtube.com/vi/' + post.videoid + '/0.jpg';
                                    obj.picture = pic;
                                }
                                else if (post.embed_code_type == 'dailymotion') {
                                    thumbnail = 'https://www.dailymotion.com/thumbnail/video/' + post.videoid;
                                    obj.picture = thumbnail;
                                }
                                else if (post.embed_code_type == 'vimeo') {
                                    thumbnail = 'https://i.vimeocdn.com/video/' + post.videoid + '_640.jpg';
                                    obj.picture = thumbnail;
                                }
                            }
                            else if (post.video != '') { // html5
                                var video_poster = post.video_poster;
                                obj.picture = video_poster;

                                var url = post.video;
                                obj.source = url;
                            }
                        }
                    }

                    FB.ui(obj, function (response) {
                        if (response && !response.error_message) {
                            // update postcard total share ...
                            if (angular.isArray($scope.commonData.allPosts)) {
                                angular.forEach($scope.commonData.allPosts, function (value, key) {
                                    if (value.id == post.id) {
                                        $scope.commonData.allPosts[key].totalShare = $scope.commonData.allPosts[key].totalShare + 1;
                                        return false;
                                    }
                                });
                            }
                            // update postcard modal
                            if (type == 'M') {
                                if ($scope.user.id != $scope.post.child_post_user_id) {
                                    $scope.post.points = $scope.post.points + 10;
                                }
                                $scope.post.totalShare = $scope.post.totalShare + 1;
                                $scope.post.totalFBshare = $scope.post.totalFBshare + 1;
                            }

                            $http({
                                method: 'POST',
                                url: "sharedPostInSocialNetworkingForFacebook",
                                data: {
                                    post_id: post.id,
                                    child_post_id: post.child_post_id,
                                    activityType: 4,    //  shared to facebook
                                }
                            }).then(function (response) {

                            }, function (response) {
                                console.log('Some error is occurred.');
                            });

                        } else {
                            console.log('Error while posting.');
                        }
                    });
                }; // end of a function
                $window.$scope = $scope;

                $scope.twitter = function (post, card_type) {


                    flag = 0;
                    var callAjax = 0;
                    if ($scope.user.guest == 0) {

                        $http({
                            method: "POST",
                            url: "accessTokenVerify",
                        }).then(function (response) {
                            flag = response.data;
                            $scope.twitterPopUp(flag, post, card_type)
                        }, function (eror) {
                            console.log('some error has occurred');
                        });

                    } else {
                        flag = 0;
                        $scope.twitterPopUp(flag, post, card_type)
                    }
                };  // end of a  twitter function

                $scope.twitterPopUp = function (flag, post, card_type) {

                    if (parseInt(flag) == 0) {
                        var left = screen.width / 2 - 200;
                        var top = screen.height / 2 - 250;

                        var popup = $window.open('twitter_connect/' + 1, '', 'left=' + left + ',top=' + top + ',width=550,height=450,personalbar=0,toolbar=0,scrollbars=0,resizable=0');

                        var interval = 1000;

                        // create an ever increasing interval to check a certain global value getting assigned in the
                        var i = $interval(function () {
                            interval += 500;
                            try {
                                // value is the user_id returned from paypal
                                if (popup.value) {
                                    $interval.cancel(i);
                                    popup.close();
                                    $scope.postToTwitter(post, card_type);
                                }
                            } catch (e) {
                                console.error(e);
                            }
                        }, interval);
                    } else {
                        $scope.postToTwitter(post, card_type);
                    }
                };

                $scope.postToTwitter = function (post, card_type) {

                    // **** start update scope data *****
                    // update postcard total share ...
                    if (angular.isArray($scope.commonData.allPosts)) {
                        angular.forEach($scope.commonData.allPosts, function (value, key) {
                            if (value.id == post.id) {
                                $scope.commonData.allPosts[key].totalShare = $scope.commonData.allPosts[key].totalShare + 1;
                                return false;
                            }
                        });
                    }
                    // update postcard modal
                    if (card_type == 'M') {
                        if ($scope.user.id != $scope.post.child_post_user_id) {
                            $scope.post.points = $scope.post.points + 10;
                        }
                        $scope.post.totalShare = $scope.post.totalShare + 1;
                        $scope.post.totalTwittershare = $scope.post.totalTwittershare + 1;
                    }
                    // ***** end update scope data ******

                    $timeout(function () {
                        var notification = {
                            'message': 'You have successfully shared this post to twitter.',
                            'color': 'success',
                            'timeout': 5000
                        };
                        notify(notification);
                    }, 2100);

                    $http({
                        method: 'POST',
                        url: "postToTwitter",
                        data: {
                            post_id: post.id,
                            child_post_id: post.child_post_id,
                        }
                    }).then(function (response) {

                    }, function (error) {
                        console.log('Some error is occurred.');
                    });
                };


            },
        }
    });

/* ============================================================
 * Directive: Prompt Sign in Box
 * Alert for Non logged in users
 * ============================================================ */
angular.module('app')
    .directive('promptSigninBox', function () {
        return {
            restrict: 'E',
            link: function ($scope, element, attrs) {

            },
            templateUrl: 'tpl_promptSinginBox'
        };
    });

/* ============================================================
 * Directive: Book Mark
 * User can bookmark through
 * ============================================================ */

angular.module('app')
    .directive('bookMark', function ($http) {
        return {
            restrict: "A",
            link: function ($scope, element, attrs) {
                element.bind('click', function (event) {
                    $scope.$apply(attrs.bookMark);
                });
            },
            controller: function ($scope) {
                $scope.bookMarkProcess = function (postID, type) {
                    // M : Modal ,P:  Postcard
                    if (angular.isArray($scope.commonData.allPosts)) {
                        var isChange = 0;
                        angular.forEach($scope.commonData.allPosts, function (value, key) {
                            if ($scope.commonData.allPosts[key].id == postID) {

                                if ($scope.commonData.allPosts[key].isBookMark == 'Y') {
                                    $scope.commonData.allPosts[key].isBookMark = 'N';
                                    // For left panel update total book marks
                                    isChange = 1;
                                } else {
                                    $scope.commonData.allPosts[key].isBookMark = 'Y';
                                    // For left panel update total book marks
                                    isChange = 2;
                                }
                                return;
                            }
                        });
                    }
                    if (type == 'M') {  // Dynamick binding  for  postcard modal

                        if ($scope.post.isBookMark == 'Y') {
                            $scope.post.isBookMark = 'N';
                            $scope.post.totalBookMark = $scope.post.totalBookMark - 1; // post total bookmark
                            isChange = 1;
                        } else {
                            $scope.post.isBookMark = 'Y';
                            $scope.post.totalBookMark = $scope.post.totalBookMark + 1; // post total book mark
                            isChange = 2;
                        }

                    }

                    // user  total bookmark
                    if (isChange == 1) {
                        $scope.user.totalBookMarks = $scope.user.totalBookMarks - 1;
                    }
                    if (isChange == 2) {
                        $scope.user.totalBookMarks = $scope.user.totalBookMarks + 1;
                    }

                    $http({
                        method: 'POST',
                        url: "angular/bookmark",
                        data: {postID: postID}
                    }).then(function (response) {

                    }, function (error) {
                        console.log('Sorry! we cannot process your request.');
                    });

                }
            }
        };
    });
/* ============================================================
 * Directive: Connect to social media
 * facebook 
 * ============================================================ */
angular.module('app')
    .directive('connectSocialMedia', function ($window, $http, $interval) {
        return {
            restrict: 'A',
            link: function ($scope, element, attrs) {

                element.bind('click', function (event) {
                    $scope.$apply(attrs.connectSocialMedia);
                });
                // connect to facebook

                $window.$scope = $scope;
                $scope.connectFacebook = function () {

                    var left = screen.width / 2 - 200;
                    var top = screen.height / 2 - 250;

                    var popup = $window.open('facebookLogin/', '', 'left=' + left + ',top=' + top + ',width=550,height=450,personalbar=0,toolbar=0,scrollbars=0,resizable=0');

                    var interval = 1000;

                    // create an ever increasing interval to check a certain global value getting assigned in the 
                    var i = $interval(function () {
                        interval += 500;
                        try {
                            // value is the user_id returned from paypal 
                            if (popup.value) {
                                $interval.cancel(i);
                                popup.close();

                            }
                        } catch (e) {
                            console.error(e);
                        }
                    }, interval);

                };
            }
        };
    });
