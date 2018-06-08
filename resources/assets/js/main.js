/* ============================================================
 * File: main.js
 * Main Controller to set global scope variables.
 * Directives, Services, Filters.
 * ============================================================ */

angular.module('app')
    .factory('ajaxService', function ($http) {
        return {
            serverCall: function (url, data) {
                return $http.post(url, data).then(function (response) {
                    return response.data;
                });
            }

        };
    })
    .controller('AppCtrl', ['$scope', '$http','$location', '$rootScope', '$state', '$stateParams', '$sce', '$filter', '$timeout', '$interval', '$location', '$window', '$anchorScroll', 'refreshService', 'userDataService', 'YT_event', 'socket', 'postOpened', 'localStorageService', 'haversineDistanceCalculationService', function ($scope, $http,$location, $rootScope, $state, $stateParams, $sce, $filter, $timeout, $interval, $location, $window, $anchorScroll, refreshService, userDataService, YT_event, socket, postOpened, localStorageService, haversineDistanceCalculationService) {


        $scope.checkUrl=function ()
        {
            var urlArray = $location.path().split("/");
          
           
            if(urlArray[1]!='post-add')
            {
             $scope.checkUrlParam=urlArray[1];
            // alert($scope.checkUrlParam);
            }

             if(urlArray[1]=='tag' || urlArray[1]=='questions')
                {
                    var tagName=urlArray[2]
                    var url = '/api/tag-details/' + tagName;
                    $http.get(url).then(function (response) {
                            $scope.urlParamText=response.data.tag_text;
                                 
                                
                    });
                }
                else if(urlArray[1]=='place')
                {
                        function getParameterByName(name)
                        {
                            url=document.URL;
                           
                            name = name.replace(/[\[\]]/g, "\\$&");
                            var regex = new RegExp("[?&]" + name + "(=([^&#]*)|&|#|$)"),
                                results = regex.exec(url);
                            if (!results) return null;
                            if (!results[2]) return '';
                            return decodeURIComponent(results[2].replace(/\+/g, " "));
                        }

                        var address='';
                        if(getParameterByName('location'))
                        {
                        
                            address=getParameterByName('location');
                        }
                        else if(getParameterByName('city'))
                        {
                        
                            address=getParameterByName('city');
                        }
                        else if(getParameterByName('state'))
                        {
                        
                            address=getParameterByName('state');
                        }
                        else if(getParameterByName('country'))
                        {
                        
                            address=getParameterByName('country');
                        }
                        else if(getParameterByName('region'))
                        {
                        
                            address=getParameterByName('region');
                        }
                        else if(getParameterByName('continent'))
                        {
                        
                            address=getParameterByName('continent');
                        }


                         $scope.urlParamText=address;
                }



            
        }


        
        // App globals
        $scope.app = {
            name: 'Swolk',
            description: 'Social networking website',
            layout: {
                menuPin: false,
                menuBehind: false,
                theme: 'assets/pages/css/pages.css',
                // paceThemeFlash: 'assets/plugins/pace/pace-theme-flash.css',
                bootstrap: 'assets/plugins/bootstrapv3/css/bootstrap.min.css',
                fontAwesome: 'assets/plugins/font-awesome/css/font-awesome.css',
                jqueryScrollbar: 'assets/plugins/jquery-scrollbar/jquery.scrollbar.css',
                pagesIcons: 'assets/pages/css/pages-icons.css',
                customCss: 'assets/pages/css/custom.css',
                owlCarousel: 'assets/plugins/carousel/owl.carousel.css',
                slickCarousel: 'assets/plugins/slick-carousel/slick.css',
                // Othres
                ie9: 'assets/pages/css/ie9.css',
                mapplicIe: 'assets/plugins/mapplic/css/mapplic-ie.css',
                chromeFix: 'assets/pages/css/windows.chrome.fix.css',

            },
            author: 'tuhin.tsm.mandal@gmail.com'

        };

        $scope.mySwitch = false;
        $scope.masonryColumnWidth = '350px';
        //$scope.current_state=$state.current.name;
        //console.log($scope.current_state);


        // User Background Colors
        $scope.usercolors = [
            "#a12c3e",
            "#c73935",
            "#ad2a76",
            "#dc6092",
            "#bfb8da",
            "#1e3a6c",
            "#f5c5d1",
            "#e68f74",
            "#ed8958",
            "#efae6a",
            "#224b3d",
            "#5b1434",
            "#f0ce74",
            "#f9f4bc",
            "#f8dd72",
            "#d6ce83",
            "#4f295a",
            "#76804e",
            "#adca6e",
            "#adca6e",
            "#7ca954",
            "#95cccf",
            "#4385a7",
            "#4295a3",
            "#c9e2cd",
            "#8f5494"
        ];

        // Constants..
        $scope.default_profile_img = 'assets/img/default-profile.png';
        $scope.default_post_img = 'assets/img/post-placeholder.png';
        $scope.default_post_vid = 'assets/img/video_placeholder.jpg';
        $scope.default_cover_img = 'assets/img/default-cover-img.jpg';
        $scope.loadMoreCommentsLimit = 10;

        // Initializing as object to shared through child controllers.
        $scope.commonData = {};

        $scope.userFollowing = [];
        $scope.userFollower = [];
        $scope.isFollowingCategory = [];

        $scope.loggedIn = false;

        $http.get('angular/auth-json').then(function (response) {
            if (response.data && response.data.id > 0) {
                $scope.root_url = ROOT_URL;
                $scope.user = response.data;
                // invoke initialy and load 10 notifications.
                if ($scope.user.guest == 0) {
                    $scope.loadNotifications(1, 10);
                    $scope.loggedIn = true;
                    // Listen for user activity.
                    $scope.emitUserOpened($scope.user.id);
                }

                $scope.userProfileview = response.data.userProfileview;
                $scope.userTotalPost = response.data.userTotalPost;

                $scope.userFollowingData = $scope.user.following;
                $scope.userFollowerData = $scope.user.follower;

                if (angular.isArray($scope.userFollowingData)) {
                    angular.forEach($scope.userFollowingData, function (value, key) {
                        $scope.userFollowing.push(parseInt(value.user_id));

                    });
                }
                if (angular.isArray($scope.userFollowerData)) {
                    angular.forEach($scope.userFollowerData, function (value, key) {
                        $scope.userFollower.push(parseInt(value.user_id));

                    });
                }

                if (angular.isArray($scope.user.category_follow)) {
                    angular.forEach($scope.user.category_follow, function (value, key) {
                        $scope.isFollowingCategory.push(parseInt(value.category_id));
                    });
                }

                if (angular.isArray($scope.user.category_follow)) {
                    angular.forEach($scope.user.category_follow, function (value, key) {
                        $scope.isFollowingCategory.push(parseInt(value.category_id));
                    });
                }
            }

        }, function (response) {
            // $scope.logout();
        });

        $scope.showStatus = function (tags) {
            res=false;
            tags.forEach(function(tag) {
              //  alert(tag.tag_name);
                if(tag.question_tag_created_at)
                {
                
                    if( post.created_at < tag.question_tag_created_at)
                    {
                        res=true; 
                        return res;
                    }
                }
                else
                {
                    res=true;
                    return res;
                }

            

               });
               
           return res;
        };



        /*------ Logout From the app ------*/
        $scope.logout = function () {
            $scope.loggedIn = false;
            localStorageService.remove('userLocationSaved');
            document.cookie = "showHeroImage=; expires=Thu, 01 Jan 1970 00:00:00 UTC";
            $window.location.href = 'logout';
        };

        $scope.refreshUserData = function() {
            refreshService.userData().then(function(data){
                $scope.user = data;

                $scope.userProfileview = data.userProfileview;
                $scope.userTotalPost = data.userTotalPost;

                $scope.userFollowingData = $scope.user.following;
                $scope.userFollowerData = $scope.user.follower;


                if (angular.isArray($scope.userFollowingData)) {
                    var userFollowing = [];
                    angular.forEach($scope.userFollowingData, function (value, key) {
                        userFollowing.push(parseInt(value.user_id));
                    });
                    $scope.userFollowing = userFollowing;
                }
                if (angular.isArray($scope.userFollowerData)) {
                    var userFollower = [];
                    angular.forEach($scope.userFollowerData, function (value, key) {
                        userFollower.push(parseInt(value.user_id));
                    });
                    $scope.userFollower = userFollower;
                }
            });
        };

        $scope.openPostPage = function (post) {
            var id = $stateParams.id;
            if ($state.current.name != 'post-details' || id != post.child_post_id)
                $window.open(post.post_url, '_blank');
        };

        /*$scope.$on(YT_event.STATUS_CHANGE, function(event, data) {
            console.log(data);
        });*/

        //*=================== Refresh common contents ====================*//
        $scope.notifications = [];
        var notificationLoaded = [];
        $scope.loadNotifiBusy = false;
        $scope.noMoreloadNotifi = false;
        /**
         * Loads and populates the notifications
         */
        $scope.loadNotifications = function (page, perpage, type) {
            if ($scope.loadNotifiBusy) {
                return;
            }

            if (type == 'loadMore') {
                // Stop loading more notification through infinite scroll.
                if ($scope.noMoreloadNotifi == true) {
                    return;
                }
                $scope.loadNotifiBusy = true;
            }
            refreshService.notification(page, perpage).then(function (data) {
                var uniqueNotifiID;

                data.notifications.forEach(function (notification, index) {
                    uniqueNotifiID = notification.id + '-' + notification.activity_id;
                    // Push to notifications if not already available.
                    if (notificationLoaded.indexOf(uniqueNotifiID) == -1) {
                        $scope.notifications.push(notification);
                        notificationLoaded.push(uniqueNotifiID);
                    }
                });

                // $scope.notifications = data.notifications;
                $scope.total_notifications = data.total_notifications;
                $scope.loadNotifiBusy = false;
                if (type == 'loadMore') {
                    $scope.notificationPopUpOpen();
                    // Stop showing loader, no more notification for loadMore.
                    if (data.notifications.length > 0) {
                        $scope.noMoreloadNotifi = true;
                    }
                }
            }, function (error) {
                $scope.loadNotifiBusy = false;
            });
        };

        // Trigger every 30 seconds for logged in user.
        $interval(function () {
            if ($scope.user && $scope.user.guest == 0) {
                $scope.loadNotifications(1, 10);
                $scope.refreshUserData();
            }
        }, 30000);
        // invoke initially and load 10 notifications.
        // $scope.loadNotifications(1, 10);

        // Load more notification through infinite scrolling.
        var notificationPage = 1;
        var perpage = 10;
        var isFirstLoadMore = true;
        var type = 'loadMore';
        $scope.loadMoreNotification = function (calledFrom) {
            // Limit max 5 page fetch on popup or prevent for first load more from notification.
            if (calledFrom == 'notification' && (notificationPage > 5 || isFirstLoadMore)) {
                isFirstLoadMore = false;
                return;
            }
            if ($scope.loadNotifiBusy) {
                return;
            }


            $scope.loadNotifications(notificationPage, perpage, type);
            notificationPage++;
        };

        $scope.notificationPopUpOpen = function () {
            /* 
             * Old Code >> Don not DELETE!!
             var notifi = [];
             var n = {};
             angular.forEach($scope.notifications, function(notification, i) {
             if (notification.status != 1) {
             return;
             }
             // Reset object.
             n = {};
             n.id = notification.id;
             n.activity_id = notification.activity_id;
             n.type = notification.type;
             n.total = notification.total;

             if ("comment_id" in notification) {
             n.comment_id = notification.comment_id;
             }
             notifi.push(n);
             });
             var postData = {
             notifi: notifi
             };
             */
            // console.log(postData);
            // Mark popup notifications as seen.
            var url = 'api/mark-notification-seen';
            $http.post(url/*, postData*/)
                .then(function (response) {
                        $scope.loadNotifications();
                    },
                    function (response) {

                    }
                );
        };

        // Mark notification as read.
        $scope.markNotificationRead = function (notification, clear_type, index) {
            /*--- Notifications stays.. ---*/
            // Remove the notification from popup.
            /*setTimeout(function() {
             $scope.notifications.splice(index,1);
             }, 500);*/
            // For notification popup.
            // No need for api call as opening popup will clear
            if (clear_type == 'all') {
                notification.status = 3;
                $timeout(function () {
                    /*--- Notifications stays.. ---*/
                    // $scope.notifications.splice(index,1);
                    $scope.loadNotifications();
                }, 1200);
                return;
            }
            // For all notifications page.
            // No need for api call as opening popup will clear
            else if (clear_type == 'allNotifi') {
                // Make notification as read.
                $('.allNotifi-' + notification.post.id).addClass('notification-read');
                $timeout(function () {
                    $scope.loadNotifications();
                }, 800);
                return;
            }
            else if (clear_type == 'single_ac') {
                // For all notifications page only.
                $('#allNotifi-' + index).addClass('notification-read');
            }

            var url = 'api/mark-notification-read';
            var id = notification.id;
            var total = notification.total;
            var type = notification.type;
            var activity_id = notification.activity_id;

            var postData = {
                id: id,
                total: total,
                type: type,
                activity_id: activity_id,
                clear_type: clear_type
            };
            $http.post(url, postData)
                .then(function (response) {
                        $scope.loadNotifications();
                    },
                    function (response) {
                        $scope.loadNotifications();
                    }
                );
        };

        // Get notification text.
        $scope.getNotificationMsg = function (notification) {
            var message, notificationTotal;
            // Add user first name
            message = $filter('cut')(notification.user.first_name, true, 25);
            // For upvote and downvote.
            if ([1, 2, 3, 4, 5, 6, 7, 12, 13].indexOf(Number(notification.activity_id)) != -1) {
                if (notification.total > 1) {
                    notificationTotal = (notification.total - 1);
                    if (notificationTotal > 9999) {
                        notificationTotal = $filter('thousandSuffix')(notificationTotal);
                    }
                    message += ' and ' + notificationTotal;
                    message += notification.total == 2 ? ' other' : ' others';
                }
                if (notification.activity_id == 1) {
                    if (notification.type == 'comment' || notification.type == 'reply') {
                        message += ' upvote your comment on';
                    }
                    else {
                        message += ' upvote';
                    }
                }
                else if (notification.activity_id == 2) {
                    if (notification.type == 'comment' || notification.type == 'reply') {
                        message += ' downvote your comment on';
                    }
                    else {
                        message += ' downvote';
                    }
                }
                else if (
                    notification.activity_id == 3 ||
                    notification.activity_id == 4 ||
                    notification.activity_id == 5 ||
                    notification.activity_id == 6
                ) {
                    message += ' shared';
                }
                else if (notification.activity_id == 7) {
                    message += ' comment on';
                }
                else if (notification.activity_id == 12) {
                    message += ' replied to your comment on';
                }
                else if (notification.activity_id == 13) {
                    message += ' follows you';
                }
            }
            // Skip for other than post related notifications.
            var postRelatedActivities = [1, 2, 3, 4, 5, 6, 7, 12];
            if (postRelatedActivities.indexOf(Number(notification.activity_id)) != -1) {
                var postName = '';
                if (notification.post.title) {
                    postName = notification.post.title;
                }
                else if (notification.post.post_type == 5 && notification.post.orginal_post) {
                    postName = notification.post.orginal_post.caption;
                }
                else {
                    postName = notification.post.caption;
                }
                // Add post title.
                message += '<i title="' + postName + '"> ' + $filter('cut')(postName, true, 25) + '</i>';
            }

            if (notification.activity_id == 4) {
                message += ' to facebook';
            }
            else if (notification.activity_id == 5) {
                message += ' to twitter';
            }
            else if (notification.activity_id == 6) {
                message += ' to linkedin';
            }

            message = $sce.trustAsHtml(message);
            return message;
        };

        /*
         * Code for web socket
         * @author Tuhin Subhra Mandal
         */
        // Emit user opened event.
        $scope.emitUserOpened = function (user_id) {
            var data = {
                user: {
                    id: user_id
                }
            };
            socket.emit('user opened', data);
        }
        // Emit post opened event.
        $scope.emitPostOpened = function (post, type) {
            var user_id = $scope.user && $scope.user.id > 1 ? $scope.user.id : null;
            var post_id = post.id;
            var relUserIds = [];
            if (post.getUser) {
                post.getUser.forEach(function (user) {
                    relUserIds.push(user.id);
                });
            }
            var opened_post = {
                post_id: post_id,
                relUserIds: relUserIds,
                uuid: browserTabID,
                user_id: user_id,
                type: type
            }
            socket.emit('post opened', opened_post);
        };
        // Emit post closed event.
        $scope.emitPostClosed = function (post_id, type) {
            var user_id = $scope.user && $scope.user.id > 1 ? $scope.user.id : null;
            var opened_post = {
                post_id: post_id,
                uuid: browserTabID,
                user_id: user_id,
                type: type
            }
            socket.emit('post closed', opened_post);
        };
        // Listen to total people here event.
        $scope.listenTotalPeopleEvent = function (callback) {
            socket.on('total_people_here', function (data) {
                callback(data.total);
            });
        };
        /* END Code for web socket */

        $scope.openProfileFollow = function (src, index) {
            $scope.total_notifications = $scope.total_notifications - 1;
            $('#notifi-' + index).addClass('notification-read');
            // $location.path('/profile').search('src', src);
            $state.go('profile', {src: src}, {reload: true});
            /*--- Notifications stays.. ---*/
            /*setTimeout(function () {
             $scope.notifications.splice(index, 1);
             }, 1000);*/

        };

        //*=================================================================*//
        /*----------------------------------------------------------------------*/
        $scope.mySmNavClk = function () {
            if (!angular.element("#sidebarOuter").hasClass("show")) {
                angular.element("html, body").addClass("sidebar-open bodyHidden");
                //angular.element(".page-sidebar").addClass("visible");
                angular.element("#sidebarOuter").addClass("show");
                angular.element(".page-sidebar").addClass("open");
            } else {
                angular.element("html, body").removeClass("sidebar-open");
                // angular.element(".page-sidebar").removeClass("visible");
                angular.element("#sidebarOuter").removeClass("show");
                setTimeout(function () {
                    angular.element(".page-sidebar").removeClass("open");
                    angular.element("html, body").removeClass("bodyHidden");
                }, 600);
            }
        };
        $scope.navsmClose = function () {
            angular.element("body").on("click", "#sidebarOuter", function () {
                angular.element("html, body").removeClass("sidebar-open");
                //angular.element(".page-sidebar").removeClass("visible");
                angular.element("#sidebarOuter").removeClass("show");
                setTimeout(function () {
                    angular.element(".page-sidebar").removeClass("open");
                    angular.element("html, body").removeClass("bodyHidden");
                }, 600);
            });
        };

        // Checks if the given state is the current state
        $scope.is = function (name) {
            return $state.is(name);
        };

        // Checks if the given state/child states are present
        $scope.includes = function (name) {
            return $state.includes(name);
        };

        $scope.closeOverLayout = function () {
            //$scope.popUpDropdrow = 0;
            // $scope.popUpReportDropdrow = 0;
            $(".subOverlaysh , .otherSubsh").hide();


        };

        $scope.bookmark = function (postID, type) {

            // M : Modal ,P:  Postcard

            if (angular.isArray($scope.commonData.allPosts)) {
                var isChange = 0;
                angular.forEach($scope.commonData.allPosts, function (value, key) {
                    if ($scope.commonData.allPosts[key].id == postID) {

                        if ($scope.commonData.allPosts[key].isBookMark == 'Y') {
                            $scope.commonData.allPosts[key].isBookMark = 'N';
                            // For left panel update total book marks
                            // $scope.user.totalBookMarks= $scope.user.totalBookMarks-1;
                            isChange = 1;
                        } else {
                            $scope.commonData.allPosts[key].isBookMark = 'Y';
                            // For left panel update total book marks
                            // $scope.user.totalBookMarks= $scope.user.totalBookMarks+1;
                            isChange = 2;
                        }
                        return;
                    }
                });

                //
                if (isChange == 1) {
                    $scope.user.totalBookMarks = $scope.user.totalBookMarks - 1;
                }
                if (isChange == 2) {
                    $scope.user.totalBookMarks = $scope.user.totalBookMarks + 1;
                }
            }
            if (type == 'M') {  // Dynamick binding  for  postcard modal

                if ($scope.post.isBookMark == 'Y') {
                    $scope.post.isBookMark = 'N';
                    $scope.post.totalBookMark = $scope.post.totalBookMark - 1; // post total bookmark
                    $scope.user.totalBookMarks = $scope.user.totalBookMarks - 1; // user  total bookmark
                } else {
                    $scope.post.isBookMark = 'Y';
                    $scope.post.totalBookMark = $scope.post.totalBookMark + 1; // post total book mark
                    $scope.user.totalBookMarks = $scope.user.totalBookMarks + 1; // user  total bookmark
                }
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

        // Enable scroll lock on notification popup & prevent body scrolling when scrolling notification.

        $scope.notifiScrollLock = function () {
            $("#notifiBody").scrollLock();
        };

        // Broadcasts a message to pgSearch directive to toggle search overlay
        $scope.showSearchOverlay = function () {
            $scope.$broadcast('toggleSearchOverlay', {
                show: true
            });
        };

        $scope.hidePostFilter = false;
        $scope.removeFlikityLoader = false;

        $scope.postTypeNavItems = [
            {
                name: 'All',
                value: 'all'
            },
            {
                name: 'Status',
                value: 5
            },
            {
                name: 'Image',
                value: 1
            },
            {
                name: 'Video',
                value: 2
            },
            {
                name: 'link',
                value: 4
            },
            {
                name: 'Article',
                value: 3
            },
            {
                name: 'Question',
                value: 6
            },
           
        ];

        $scope.flickityTagOptions = {
            prevNextButtons: false,
            pageDots: false,
            wrapAround: true,
            contain: true,
			cellAlign: 'left'
            //freeScroll: true,
        };

        // Calculate post card total column width.
        var positionCards = function () {
            // No need for mobile.
            if ($(window).width() < 567)
                return;
            var $blockContent = $(".blockContent");
            // var $blockContent = $(".masonry-brick");

            var totalPostWidth = 0;
            var loopLimit = 15;
            var post_card_containerWidth = $(".post_card_container").innerWidth();

            if ($blockContent.length) {
                $blockContent.each(function () {
                    if (!loopLimit) {
                        return;
                    }
                    loopLimit--;
                    if ($(this).css('top') == '0px') {
                        totalPostWidth += $(this).innerWidth();
                    }
                });

                var widthDiff = (post_card_containerWidth - totalPostWidth) / 2;

                var $blckCntParent = $("#blckCntParent");
                $blckCntParent.css({"left": widthDiff});
            }
        };

        $("body").on('click', '.modal-body .customPlayPause', function (e) {
            var videoDomObj = $(this).parent().children().children("video").get(0);
            if (videoDomObj.paused) {
                videoDomObj.play();
                $(this).parent().children(".customPlayPause").addClass("out");
            }
        });
        $("body").on('click', '.modal-body .videoTag', function (e) {
            var videoDomObj2 = $(this).parent().children("video").get(0);
            if (videoDomObj2.paused) {
                videoDomObj2.play();
                $(this).parent().parent().children(".customPlayPause").addClass("out");
            } else {
                videoDomObj2.pause();
                //$(this).parent().parent().children(".customPlayPause").removeClass("out");
            }
        });

        var positionCardTimer;
        /* 
         * Start positioning cards.
         * @param time int -- time in milliseconds 
         */
        $scope.startPositionCards = function (time) {
            positionCards();
            if (!time) {
                time = 300;
            }
            positionCardTimer = $interval(function () {
                positionCards();
            }, time);
        };
        /*
         * Stop positioning cards in interval.
         */
        $scope.stopPositionCards = function (time) {
            if (!time) {
                time = 2000;
            }
            setTimeout(function () {
                $interval.cancel(positionCardTimer);
            }, time);
        }

        var reloadCardMasonryTimer;
        /* 
         * Start masonry reloads.
         * @param time int -- time in milliseconds 
         */
        $scope.startReloadCardMasonry = function (time) {
            // No need for mobile.
            if ($(window).width() < 600)
                return;
            // Reload at first.
            $rootScope.$broadcast('masonry.reload');
            if (!time) {
                time = 300;
            }
            reloadCardMasonryTimer = $interval(function () {
                $rootScope.$broadcast('masonry.reload');
            }, time);
        };

        /*
         * Stop positioning cards in interval.
         */
        $scope.stopReloadCardMasonry = function (time) {
            if (!time) {
                time = 2100;
            }
            $timeout(function () {
                $interval.cancel(reloadCardMasonryTimer);
            }, time);
        }

        setTimeout(function () {
            positionCards();
            // $rootScope.$broadcast('masonry.reload');
        }, 100);
        /*-------------------------------------------*/
        /*$interval(function () {
         $rootScope.$broadcast('masonry.reload');
         }, 300);*/
        /*=============================================*/

        /* Call after resizing is done. */
        var isCalled = false;
        var resizeId;
        $(window).resize(function () {
            if (!isCalled) {
                positionCards();
                isCalled = true;
            }
            clearTimeout(resizeId);
            resizeId = setTimeout(doneMyResizing, 500);
        });
        function doneMyResizing() {
            isCalled = false;
            setTimeout(function () {
                positionCards();
            }, 100);
        }

        /*----------------------------------------------------------------------*/

        $scope.showLocation = function (location) {
            var formatted_location = '';
            if (location) {
                var charLimit = 14;
                var indexOfComma = location.indexOf(',');
                // comma not found
                if (indexOfComma === -1) {
                    if (location.length > charLimit) {
                        formatted_location = location.substring(0, charLimit) + "..";
                    }
                    else {
                        formatted_location = location;
                    }
                }
                else {
                    location = location.substring(0, indexOfComma);
                    if (location.length > charLimit) {
                        formatted_location = location.substring(0, charLimit) + "..";
                    }
                    else {
                        formatted_location = location;
                    }
                }
            }
            return formatted_location;
        };

        $scope.showPostViewTxt = function (post, referrer) {
            if (!post) {
                return '';
            }
            else if (post.post_type == 1 || post.post_type == 5 || post.post_type == 6) {
                if (referrer == 'card') {
                    return 'seen';
                }
                return 'viewed';
            }
            else if (post.post_type == 2) {
                return 'played';
            }
            else if (post.post_type == 3) {
                return 'read';
            }
            else if (post.post_type == 4) {
                return 'linked';
            }
        };

        $scope.redirecToLogin = function () {
            $('#signInNotifi, .signInoverlay, .loginDropWrap').css('display','block');
            $('.forgotPassWrap').css('display','none');
            setTimeout(function () {
                //$scope.goToLogin();
                //$("#signInNotifi.in .signIN").trigger("click");
            }, 3000);
            $("body").on("click", "#signInNotifi", function () {
                if ($("#myModal").hasClass("in")) {
                    setTimeout(function () {
                        //$("body").addClass("modal-open");
                    }, 500);
                }
            });
        };
		
		$('body').on('click','.signInoverlay', function(){
			$('#signInNotifi, .signInoverlay').css('display','none');
		});


        $scope.goToLogin = function () {
            window.location = ROOT_URL;
        }


        $scope.showElapsedTime = function (post_date) {
            post_date = moment(post_date).toDate();
            var oldLimit = new Date();
            oldLimit.setDate(oldLimit.getDate() - 7); // 7 days old
            return post_date > oldLimit;
        };

        $scope.$on('$viewContentLoaded', function () {
            notificationWidth();
            angular.element(window).resize(function () {
                notificationWidth();
            });
            //owlCenterAlign();
            $(window).resize(function () {
                setTimeout(function () {
                    $scope.owlCenterAlign();
                }, 600);
            });


        });

        $scope.upvoteDownvoteTxt = function (vote) {
            vote = parseInt(vote);
            if (vote > 1)
                return 'upvotes';
            else if (vote >= 0)
                return 'upvote';
            else if (vote < -1)
                return 'downvotes';
            else if (vote < 0)
                return 'downvote';
        };

        function notificationWidth() {
            if ($(window).width() <= 599) {
                var winWidth = $(window).width();
                $('.notification-panel').css({"width": winWidth});
            } else {
                $('.notification-panel').css({"width": ""});
            }
        }

        $scope.owlCenterAlign = function () {
            var owlCarousel = $(".owl-carousel");
            if (owlCarousel.length > 0) {
                owlCarousel.each(function () {
                    var owlOuterWidth = $(this).children(".owl-stage-outer").width();
                    var owlWidth = $(this).children().children(".owl-stage").innerWidth();
                    if (owlWidth < owlOuterWidth) {
                        $(this).removeClass("centerItem");
                        $(this).addClass("centerItem");
                    } else {
                        $(this).removeClass("centerItem");
                    }
                });
            }
        };

        $scope.initFlickity = function (element) {
            $(element).show();
            var defaultOptions = {
                freeScroll: true,
                contain: true,
                prevNextButtons: false,
                pageDots: false,
                wrapAround: true,
				cellAlign: 'left'
            };
            var customOptions = $scope.$eval($(element).attr('data-options'));
            for (var key in customOptions) {
                defaultOptions[key] = customOptions[key];
            }
            $(element).flickity(defaultOptions);
        };

        $scope.removePostLoading = function (postLoading) {
            /*postLoading = $("#" + postLoading);
             postLoading.removeClass('postLoading');*/
        };

        // For post details popup..
        $scope.postPopupClose = function (post_id) {
            var $detailModal = $("#myModal");
            // Code for mobile device
            if ($.Pages.getUserAgent() == 'mobile') {
                $("html").removeClass("mob-hideModal");
                $(".loaderImage").hide();
                window.scrollTo(0, $scope.winScrollPos);
                setTimeout(function () {
                    if ($detailModal.is(':visible')) {
                        $detailModal.modal('toggle');
                    }
                }, 100);
            }
            $detailModal.unbind();
			
			$("html, body").removeClass("sidebar-open bodyHidden scrollHidden mob-hideModal modal-open");
			

            // Reset the post.
            $scope.post = {};
            // console.log('postPopupClose called: ' + post_id);
            // Code for socket.
            $scope.emitPostClosed(post_id, 1);

            // For Online people here.
            var opened_posts = localStorageService.get('opened_posts');
            opened_posts = JSON.parse(opened_posts);
            if (opened_posts) {
                // Decrease count.
                opened_posts[post_id]--;
                if (opened_posts[post_id] == 0) {
                    var p;
                    for (p in $scope.commonData.allPosts) {
                        if ($scope.commonData.allPosts[p].id == post_id) {
                            // console.log($scope.commonData.allPosts[p].people_here);
                            if ($scope.commonData.allPosts[p].people_here > 0) {
                                $scope.commonData.allPosts[p].people_here -= 1;
                            }
                            break;
                        }
                    }
                    delete opened_posts[post_id];
                }
                localStorageService.set('opened_posts', JSON.stringify(opened_posts));
            }
        };

        $scope.winScrollPos = 0;
        $scope.showPostDetails = function (post_id, childPostId, is_scroll, postCardIndex, comment_id) {

           
            $scope.winScrollPos = $(window).scrollTop();

            var myModal = $("#myModal");
            myModal.on('hide.bs.modal', function () {
                $scope.postPopupClose(post_id);
                $(".barLong").hide();
            });
            myModal.on('show.bs.modal', function () {
                $(".barLong").show();
            });

            $scope.displayBestTab = false;
            myModal.addClass("modalLoader");
            myModal.modal();

            $(".barLong, .mobileBarLong").css({"display": "none"});  // Scroll bar loader
            $(".modalMobileScroll, .modal, .postModalOuter").animate({scrollTop: 0});
            $("html").addClass("scrollHidden");
            $('.scrollbar-inner').scrollbar(); // For Mobile scrollbar 

            $scope.showSendBtn = true;
            // reset post data ...
            $scope.post = {};
            // console.log($scope.post);
            /*------- Code for socket -------*/
            $scope.totalPeopleHere = 0;

            $timeout(function () {
                // Scroll bar loader
                $(".barLong, .mobileBarLong").css({"width": "0px", "display": "block"});
                $(".modalMsg").val('');	// for empty comment box
            }, 1000);

            // Show loader..
            $scope.removePostLoader = false;
            $scope.postCardModal = "tpl_postcardmodal";
            // $scope.comments=[];

            $http({
                method: "POST",
                url: "angular/showPostDetails",
                params: {post_id: post_id, child_post_id: childPostId, is_briefed: 0}
            }).then(function (response) {

                $scope.post = response.data.post;
                //$scope.post.distance = null;

                /*------- Code for socket -------*/
                postOpened.init();
                socket.removeAllListeners('total_people_here');

                $scope.emitPostOpened($scope.post, 1);
                $scope.listenTotalPeopleEvent(function (totalPeopleHere) {
                    $scope.totalPeopleHere = totalPeopleHere;
                    if ($scope.loggedIn) {
                        var p;
                        for (p in $scope.commonData.allPosts) {
                            if ($scope.commonData.allPosts[p].id == post_id) {
                                $scope.commonData.allPosts[p].people_here = $scope.totalPeopleHere;
                                break;
                            }
                        }
                    }
                });
                // For real time data
                if ($scope.loggedIn) {
                    // Track opened post
                    postOpened.trackPostOpened(post_id);

                    // Listen for user followed event
                    postOpened.userFollowedEvent(function (response) {
                        if ($scope.post.getUser) {
                            $scope.post.getUser.forEach(function (user) {
                                if (user.id == response.user_id) {
                                    user.is_follow = response.data.totalFollowers;
                                }
                            });
                        }
                    });
                    // Listen for user viewed event
                    postOpened.userViewedEvent(function (response) {
                        if ($scope.post.getUser) {
                            $scope.post.getUser.forEach(function (user) {
                                if (user.id == response.user_id) {
                                    user.userDataProfileViews = response.data.userDataProfileViews;
                                }
                            });
                        }
                    });
                    // Listen for user point updated event
                    postOpened.userPointUpdatedEvent(function (response) {
                        if ($scope.post.getUser) {
                            $scope.post.getUser.forEach(function (user) {
                                if (user.id == response.user_id) {
                                    user.points = response.data.points;
                                }
                            });
                        }
                    });

                    // Listen for post view updated event
                    postOpened.listenPostViewUpdatedEvent(function (response) {
                        if ($scope.post && $scope.post.id == response.post_id) {
                            $scope.post.totalPostViews = response.data.totalPostViews;
                        }
                    });
                    // Listen for post point updated event
                    postOpened.listenPostPointUpdatedEvent(function (response) {
                        if ($scope.post && $scope.post.id == response.post_id) {
                            $scope.post.points = response.data.points;
                        }
                    });
                    // Listen for post upvoted event
                    postOpened.listenPostUpvotedEvent(function (response) {
                        if ($scope.post && $scope.post.id == response.post_id) {
                            $scope.post.upvotes = response.data.upvotes;
                            $scope.post.downvotes = response.data.downvotes;
                        }
                    });
                    // Listen for post bookmarked event
                    postOpened.listenPostSharedEvent(function (response) {
                        if ($scope.post && $scope.post.id == response.post_id) {
                            $scope.post.normalShare = response.data.normalShare;
                            $scope.post.totalFBshare = response.data.totalFBshare;
                            $scope.post.totalTwittershare = response.data.totalTwittershare;
                            $scope.post.totalShare = $scope.post.normalShare
                                + $scope.post.totalFBshare
                                + $scope.post.totalTwittershare;
                        }
                    });
                    // Listen for usersTypingInChannel event
                    postOpened.listenPostBookmarkedEvent(function (response) {
                        if ($scope.post && $scope.post.id == response.post_id) {
                            $scope.post.totalBookMark = response.data.totalBookMark;
                        }
                    });
                }
                /*------- END Code for socket -------*/

                /*** Calculate user distance ***/
                /*if(navigator.geolocation){
                    navigator.geolocation.getCurrentPosition(function(position){
                        var lat = position.coords.latitude;
                        var lng = position.coords.longitude;
                        $scope.post.distance = haversineDistanceCalculationService.findDistance(lat, lng, $scope.post.lat, $scope.post.lon);

                        console.log($scope.post.distance);
                    }, function(PositionError){
                    },{
                        enableHighAccuracy: true,
                        timeout: 5000,
                        maximumAge: 0
                    });
                }*/
                /*** Calculate user distance ***/

                //  For device and creator name before caption on postcard modal   
                var postModalUser = response.data.post.getUser;
                angular.forEach(postModalUser, function (value, key) {
                    $scope.postUserName = value.first_name;
                    $scope.createdUsername = value.username;
                    $scope.createdUserFirstName = value.first_name;
                    $scope.createdUserLastName = value.last_name;
                    $scope.createdUserAboutMe = value.about_me;
                    $scope.createdUserColor = value.user_color;
                    $scope.createdUserId = value.id;
                    $scope.creatorProfileImage = value.thumb_image_url;
                });
                // end 

                // using for doDownVotes(post.id,post.child_post_id,openFrom) function
                // M  for  modal and C for card .
                $scope.openFrom = 'M';

                // update user points dynamically

                if ($state.current.name == 'profile' || $state.current.name == 'account') {
                    var points = postModalUser[0].points;
                    userDataService.updateUserData(points);
                    userDataService.getData();

                }

                if (!$scope.postData) {
                    $scope.postData = {};
                }

                $scope.postData.post_id = $scope.post.id;

                // Update dynamically total number of post view 
                var p;
                for (p in $scope.commonData.allPosts) {
                    if ($scope.commonData.allPosts[p].id == post_id) {
                        $scope.commonData.allPosts[p].totalPostViews = response.data.post.totalPostViews;
                        // break;                       
                    }
                }
                // reset video
                $("video").load();
                // *** END *** //

                // Hide Loader..
                $scope.removePostLoader = true;
                $timeout(function () {
                    myModal.removeClass("modalLoader");
                    $("html").addClass("mob-hideModal");
                    $('.modalMsg').on("touchstart", function () {
                        $(this).trigger('mouseenter');
                    });
                }, 100);

                if (is_scroll == 1) {
                    $timeout(function () {
                        $location.hash('commentfilter' + post_id);
                        $anchorScroll();
                    }, 2000);
                }
                // For notification.
                else if (is_scroll == 3) {
                    if (comment_id) {
                        $timeout(function () {
                            /*$("#newcomments").trigger('click');
                             $(".more_comment_div .loadMoreBtn").trigger('click');
                             $location.hash('pc' + post_id);*/
                            $location.hash('commentfilter' + post_id);
                            $anchorScroll();
                        }, 2000);
                    }
                }

                offset = 5;
                $timeout(function () {
                    if ($scope.post.bestTab > 0) {
                        $("#bestComments").trigger("click");
                    }
                    else {
                        $("#newcomments").trigger("click");
                    }
                }, 400);
            });


            if (postCardIndex) {
                $scope.postCardIndex = postCardIndex;
            }
            else {
                $scope.postCardIndex = 0;
            }
        };

        $(document).on("click", ".shareModal", function () {
            if (!$('.shareModal').hasClass("in")) {
				var sharelastScrollPos = $(window).scrollTop();
				$("html, body").animate({scrollTop: sharelastScrollPos}, 0);
                $("html").removeClass("mob-hideModal");
            }
        });

        $scope.videoPlay = function (post, index, type) {
            if ($(window).width() > 600) {
                if (type == 'C') {
                    if (post.video) {
                        var vIndex = $("#cardVideo-" + post.cardID);
                        cardVideo = vIndex.find(".videoTag");
                        cardVideo[0].play();

                        vIndex.next(".customPlayPause").hide();
                    }
                    else if (post.embed_code) {
                        var embed_code = post.embed_code;
                        var videoURL = embed_code.toLowerCase();

                        if (videoURL.search('youtube') != -1) {
                            var index = 'yc-' + post.cardID;
                            if (typeof tsmPlayerPool[index] === 'undefined' || typeof tsmPlayerPool[index].playVideo !== 'function') {
                                return;
                            }
                            tsmPlayerPool[index].playVideo();
                        }
                        else if (videoURL.search('vimeo') != -1) {
                            var index = 'vc-' + post.cardID;
                            var iframe = $("#" + index).find('iframe')[0];
                            var player = new Vimeo.Player(iframe);
                            player.play();

                        }
                        else if (videoURL.search('dailymotion') != -1) {
                            var index = 'dc-' + post.cardID;
                            $("#" + index).find('iframe')[0].contentWindow.postMessage('play', "*");
                        }
                    }

                }
                else {
                    if (post.video) {
                        var video = $(".profileNewLeft").find('.videoTag');
                        video[0].play();
                        video.next().hide();
                    }
                    else if (post.embed_code) {
                        var embed_code = post.embed_code;
                        var videoURL = embed_code.toLowerCase();
                        if (videoURL.search('youtube') != -1) {
                            var index = 'yc-' + post.cardID;
                            tsmPlayerPool[index].playVideo();
                        }
                        else if (videoURL.search('vimeo') != -1) {

                            var index = 'vc-' + post.cardID;
                            var iframe = $("#" + index).find('iframe')[0];
                            var player = new Vimeo.Player(iframe);
                            player.play();

                        }
                        else if (videoURL.search('dailymotion') != -1) {
                            var index = 'dc-' + post.cardID;
                            $("#" + index).find('iframe')[0].contentWindow.postMessage('play', "*");
                        }
                    }
                }
            }

        };

        $scope.videoPause = function (post, index, type) {

            if ($(window).width() > 600) {
                if (type == 'C') {
                    if (post.video) {
                        var vIndex = $("#cardVideo-" + post.cardID);

                        var vid = vIndex.find(".videoTag");
                        vid[0].pause();

                        vIndex.next(".customPlayPause").show();
                    }
                    else if (post.embed_code) {
                        var embed_code = post.embed_code;
                        var videoURL = embed_code.toLowerCase();

                        if (videoURL.search('youtube') != -1) {
                            var index = 'yc-' + post.cardID;
                            var currentPlayer = tsmPlayerPool[index];
                            if (
                                !currentPlayer || typeof currentPlayer === 'undefined' ||
                                typeof currentPlayer.getPlayerState !== 'function'
                            ) {
                                return;
                            }
                            var currentPlayerState = currentPlayer.getPlayerState();
                            // Pause if it is playing.
                            if (currentPlayerState == 1 || currentPlayerState == 3) {
                                currentPlayer.pauseVideo();
                            }

                            /*var ct = currentPlayer.getCurrentTime();
                            var vDuration = currentPlayer.getDuration();
                            // var vData = currentPlayer.getVideoData();

                            if (ct >= vDuration * constants.VIDEO_VIEW_PER) {
                                $scope.viewVideoPost(post);
                            }*/

                        }
                        else if (videoURL.search('vimeo') != -1) {
                            var index = 'vc-' + post.cardID;
                            var iframe = $("#" + index).find('iframe')[0];
                            var player = new Vimeo.Player(iframe);
                            player.pause();
                            
                            /*player.getCurrentTime().then(function (seconds) {
                                if (seconds >= 5) {
                                    $scope.viewVideoPost(post);
                                }
                            }).catch(function (error) {
                                console.log(error);
                            });*/
                        }
                        else if (videoURL.search('dailymotion') != -1) {
                            var index = 'dc-' + post.cardID;
                            $("#" + index).find('iframe')[0].contentWindow.postMessage('pause', "*");
                        }
                    }

                }
                else {
                    if (post.video) {
                        var video = $(".profileNewLeft").find(".videoTag");
                        video[0].pause();
                        var currentTime = video[0].currentTime;
                        if (Math.round(currentTime) >= 5) {
                            $scope.viewVideoPost(post);
                        }

                        video.next().show();
                    }
                    else if (post.embed_code) {

                        var embed_code = post.embed_code;
                        var videoURL = embed_code.toLowerCase();

                        if (videoURL.search('youtube') != -1) {
                            var currentPlayer = tsmPlayerPool[index];
                            currentPlayer.pauseVideo();
                        }
                        else if (videoURL.search('vimeo') != -1) {
                            var index = 'vc-' + post.cardID;
                            var iframe = $("#" + index).find('iframe')[0];
                            var player = new Vimeo.Player(iframe);
                            player.pause();
                            
                            /*player.getCurrentTime().then(function (seconds) {
                                if (seconds >= 5) {
                                    $scope.viewVideoPost(post);
                                }
                            }).catch(function (error) {
                                console.log(error);
                            });*/

                        } else if (videoURL.search('dailymotion') != -1) {
                            var index = 'dc-' + post.cardID;
                            $("#" + index).find('iframe')[0].contentWindow.postMessage('pause', "*");
                        }
                    }
                }
            }
        };


        $scope.viewVideoPost = function (post) {

            var postID = post.id;
            var childPostID = post.child_post_id;
            var postType = post.post_type;

            $http({
                method: "POST",
                url: "angular/viewPost",
                data: {
                    postID: postID,
                    childPostID: childPostID,
                    postType: postType
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
                // Update postcard modal ::
                post.points = response.data.post.points;
                post.totalPostViews = response.data.post.totalPostViews;

                if (angular.isArray(post.getUser)) {
                    angular.forEach(post.getUser, function (value, key) {
                        angular.forEach(response.data.post.getUsers, function (val, k) {
                            if (value.id == val.id) {
                                post.getUser[k].points = val.points;
                            }
                        });
                    });
                }

            }, function (error) {
                // console.log('Error occurred.');
            });
        };


        $(window).load(function () {
           
            postCardPosition();
        });
        $(document).on('scroll', function () {
            postCardPosition();
        });

        // var checkArray=[];



        /******* Changes for new activity seen algo(20-12-17)  ********/
 /*       
        function postCardPosition() {
        
            var pCFnoBorder;
            $(".image_status_post.na").each(function () {
                pCFnoBorder = $(this).find(".profileCommentFooter.noBorderPos");
                if (pCFnoBorder.length > 0) {
                    var topPos = pCFnoBorder.offset().top - 0;
                    // var thisHeight = $(this).innerHeight();
                    var screenHeight = $(window).height();
                    var lastPos = topPos - screenHeight;

                    if ($(window).scrollTop() >= lastPos) {
                        var postID = $(this).attr("custompostid");
                        $scope.imageAndStatusView(postID);
                        $(this).removeClass('na');
                        /*if(checkArray.indexOf(postID)==-1 ) {
                         $scope.imageAndStatusView(postID);
                         checkArray.push(postID);
                         }*/
/*                    }
                }
            });
            // .exploreTest
            $(".blockContent").each(function () {
                if (!$(this).hasClass("showNow")) {
                    var thisPos = $(this).offset().top;
                    var screenPos = $(window).scrollTop();
                    var winHeight = $(window).height() - 160;

                    if (thisPos <= (screenPos + winHeight)) {
                        $(".exploreTest .blockContent").addClass("hideNow");
                        $(this).removeClass("hideNow").addClass("showNow");
                    }
                }
            });
        }

*/
 


 function postCardPosition() {
    
        var pCFnoBorder;
        $(".post.na").each(function () {
            pCFnoBorder = $(this).find(".profileCommentFooter.noBorderPos");
            if (pCFnoBorder.length > 0) {
                var topPos = pCFnoBorder.offset().top - 0;
                // var thisHeight = $(this).innerHeight();
                var screenHeight = $(window).height();
                var lastPos = topPos - screenHeight;

                if ($(window).scrollTop() >= lastPos) {
                    var postID = $(this).attr("custompostid");
                    $scope.postSeenView(postID);
                    $(this).removeClass('na');
                    /*if(checkArray.indexOf(postID)==-1 ) {
                     $scope.imageAndStatusView(postID);
                     checkArray.push(postID);
                     }*/
                   }
            }
        });
        // .exploreTest
        $(".blockContent").each(function () {
            if (!$(this).hasClass("showNow")) {
                var thisPos = $(this).offset().top;
                var screenPos = $(window).scrollTop();
                var winHeight = $(window).height() - 160;

                if (thisPos <= (screenPos + winHeight)) {
                    $(".exploreTest .blockContent").addClass("hideNow");
                    $(this).removeClass("hideNow").addClass("showNow");
                }
            }
        });
    }



    $scope.postSeenView = function (postID) {
        // console.log("Recording activity for: " + postID);
        if (angular.isArray($scope.commonData.allPosts)) {
            angular.forEach($scope.commonData.allPosts, function (value, key) {

                if ($scope.commonData.allPosts[key].id == postID) {
                    if ($scope.commonData.allPosts[key].created_by != $scope.user.id) {
                        $http({
                            method: "POST",
                            url: "angular/viewSeenPost",
                            data: {
                                postID: postID,
                                childPostID: $scope.commonData.allPosts[key].child_post_id,
                                postType: $scope.commonData.allPosts[key].post_type
                            }
                        }).then(function (response) {

                            //this block is only accessed profile and account page
                            if ($state.current.name == 'profile' || $state.current.name == 'account') {
                                if (response.data.post) {
                                    var points = response.data.post.getUsers[0].points;
                                    userDataService.updateUserData(points);
                                }
                                userDataService.getData();
                            }

                        }, function (error) {
                            console.log('Error occurred.');
                        });
                    }

                    return;
                }

            });
        }
    };



/******* Changes for new activity seen algo(20-12-17)  ********/









        $scope.imageAndStatusView = function (postID) {
            // console.log("Recording activity for: " + postID);
            if (angular.isArray($scope.commonData.allPosts)) {
                angular.forEach($scope.commonData.allPosts, function (value, key) {

                    if ($scope.commonData.allPosts[key].id == postID) {
                        if ($scope.commonData.allPosts[key].created_by != $scope.user.id) {
                            $http({
                                method: "POST",
                                url: "angular/viewPost",
                                data: {
                                    postID: postID,
                                    childPostID: $scope.commonData.allPosts[key].child_post_id,
                                    postType: $scope.commonData.allPosts[key].post_type
                                }
                            }).then(function (response) {

                                //this block is only accessed profile and account page
                                if ($state.current.name == 'profile' || $state.current.name == 'account') {
                                    if (response.data.post) {
                                        var points = response.data.post.getUsers[0].points;
                                        userDataService.updateUserData(points);
                                    }
                                    userDataService.getData();
                                }

                            }, function (error) {
                                console.log('Error occurred.');
                            });
                        }

                        return;
                    }

                });
            }
        };

        // Direct post to twitter from  create post page (IMAGE, LINK, ARTICLE and VIDEO)

        $scope.connectTwitter = function () {

            if ($("#twitter").prop('checked') == true) {

                var left = screen.width / 2 - 200;
                var top = screen.height / 2 - 250;
                var interval = 1000;
                $window.$scope = $scope;


                $http({
                    method: "POST",
                    url: "accessTokenVerify",
                }).then(function (response) {

                    if (parseInt(response.data) == 0) {
                        var popup = $window.open('auth-twitter', '', 'left=' + left + ',top=' + top + ',width=550,height=450,personalbar=0,toolbar=0,scrollbars=0,resizable=0');
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
                    }

                }, function (response) {
                    console.log('some error is occurred');
                })
            }
        };

        /*** Start Geolocation watchposition ***/
        /*var watchId;

        $scope.watchForPosition = function(){
            if (navigator.geolocation) {
                // timeout at 60000 milliseconds (60 seconds)
                var options = {timeout:60000};
                var geoLoc  = navigator.geolocation;
                watchId = geoLoc.watchPosition(saveLocation, errorHandler, options);
            }
        };

        var saveLocation = function(position){
            console.log("Geolocation : "+JSON.stringify(position));
        };

        var errorHandler = function(err){
            switch(err.code){
                case 1: console.log("Error: Access is denied!");break;
                case 2: console.log("Error: Position is unavailable!");break;
            }
        };

        $scope.watchForPosition();*/

        /*** End Geolocation watchposition ***/

    }])
    .controller('SignInCtrl', ['$scope', '$http', '$rootScope', '$state', '$stateParams', '$sce', '$filter', '$timeout', '$interval', '$location', '$window', '$anchorScroll', 'refreshService', 'userDataService', 'YT_event', 'socket', 'postOpened', 'localStorageService', 'haversineDistanceCalculationService', function ($scope, $http, $rootScope, $state, $stateParams, $sce, $filter, $timeout, $interval, $location, $window, $anchorScroll, refreshService, userDataService, YT_event, socket, postOpened, localStorageService, haversineDistanceCalculationService) {

        $scope.popupsigninH = {
            email: '',
            password: ''
        }

        // this will make the login submit
        $scope.loginPopSubmit = function(e) {

            document.getElementById("signSubmit").classList.add('disabled');

            // console.log('$("#form-signin").serializeArray()')
            // alert('login')
            // this will parse the serialize array data from the form field
            function objectifyForm(formArray) {//serialize data function
                var returnArray = {};
                for (var i = 0; i < formArray.length; i++){
                returnArray[formArray[i]['name']] = formArray[i]['value'];
                }
                return returnArray;
            }
            // make the object so server can accept and give return value
            $scope.loginPopUpUserData = objectifyForm($("#form-signin").serializeArray())


            if($scope.loginPopUpUserData.email != '' && $scope.loginPopUpUserData.password != ''){
                
                var loginPopUp = $http.post('/loginapi', $scope.loginPopUpUserData);
                loginPopUp.success(function(data, status, headers, config) {

                    document.getElementById("signSubmit").classList.remove('disabled');

                    if(data.msg == "error"){
                        $scope.signinerrormsgST = true
                        $scope.signinerrormsg = 'Invalid email and password'
                    } else {
                        // alert('everything ok')
                        document.getElementById('form-signin-h').submit()
                    }

                    // alert('success')
                    $scope.signinsuccessST = true;
                    $scope.signinsuccess = 'success';
                });
                loginPopUp.error(function(data, status, headers, config) {

                    document.getElementById("signSubmit").classList.remove('disabled');
                    // alert( "failure message");
                    $scope.signinerrormsgST = true
                    $scope.signinerrormsg = 'error'
                });

            } else {
                document.getElementById("signSubmit").classList.remove('disabled');

                $scope.signinerrormsgST = true
                $scope.signinerrormsg = 'Please put the email and password'
            }


            // document.getElementById('form-signin').submit()

            // angular sign in form submit
            // var loginPopUp = $http.post('/savecompany_json', $scope.loginPopUpUserData);
            // loginPopUp.success(function(data, status, headers, config) {
            //     // alert('success')
            //     $scope.signinsuccessST = true;
            //     $scope.signinsuccess = 'success';
            // });
            // loginPopUp.error(function(data, status, headers, config) {
            //     // alert( "failure message");
            //     $scope.signinerrormsgST = true
            //     $scope.signinerrormsg = 'error'
            // });

            
        };

    }])
    .controller('ForgotPasswordCtrl', ['$scope', '$http', '$rootScope', '$state', '$stateParams', '$sce', '$filter', '$timeout', '$interval', '$location', '$window', '$anchorScroll', 'refreshService', 'userDataService', 'YT_event', 'socket', 'postOpened', 'localStorageService', 'haversineDistanceCalculationService', function ($scope, $http, $rootScope, $state, $stateParams, $sce, $filter, $timeout, $interval, $location, $window, $anchorScroll, refreshService, userDataService, YT_event, socket, postOpened, localStorageService, haversineDistanceCalculationService) {
        
                // $scope.popupsigninH = {
                //     email: '',
                //     password: ''
                // }
        
                // this will make the login submit
                $scope.forgotPopupSubmit = function(e) {
        
                    document.getElementById("forgotPassSubmit").classList.add('disabled');
        
                    // console.log('$("#form-signin").serializeArray()')
                    // alert('forgot')
                    // this will parse the serialize array data from the form field
                    function objectifyForm(formArray) {//serialize data function
                        var returnArray = {};
                        for (var i = 0; i < formArray.length; i++){
                        returnArray[formArray[i]['name']] = formArray[i]['value'];
                        }
                        return returnArray;
                    }
                    // make the object so server can accept and give return value
                    $scope.forgotPasswordUserData = objectifyForm($("#form-forgot").serializeArray())
                      //  alert($scope.forgotPasswordUserData.email);
        
                    if($scope.forgotPasswordUserData.email != '' ){


                        var forgotPopUp = $http.post('/forgotPasswordApi', $scope.forgotPasswordUserData);
                        forgotPopUp.success(function(data, status, headers, config) {
                            document.getElementById("forgotPassSubmit").classList.remove('disabled');
                            
                         // alert(data.msg)
        
                            if(data.msg == "error"){
                               
                                $scope.forgotpasserrormsgST = true
                                $scope.forgotpasserrormsg = 'Invalid email and password'

                                $scope.forgotpasssuccmsgST = false
                                $scope.forgotpasssuccmsg = ''
                            } else {
                               
                                $scope.forgotpasssuccmsgST = true
                                $scope.forgotpasssuccmsg = 'Please check your email.'
                                $scope.forgotEmail=''
                                $scope.forgotpasserrormsgST = false
                                $scope.forgotpasserrormsg = ''
                            }
                        });
                       
        
                            
        
                            // alert('success')
                           
                        
                       
        
                    } else {
                        document.getElementById("forgotPassSubmit").classList.remove('disabled');
        
                        $scope.forgotpasserrormsgST = true
                        $scope.forgotpasserrormsg = 'Please put the email'
                    }
        
        
                    // document.getElementById('form-signin').submit()
        
                    // angular sign in form submit
                    // var loginPopUp = $http.post('/savecompany_json', $scope.loginPopUpUserData);
                    // loginPopUp.success(function(data, status, headers, config) {
                    //     // alert('success')
                    //     $scope.signinsuccessST = true;
                    //     $scope.signinsuccess = 'success';
                    // });
                    // loginPopUp.error(function(data, status, headers, config) {
                    //     // alert( "failure message");
                    //     $scope.signinerrormsgST = true
                    //     $scope.signinerrormsg = 'error'
                    // });
        
                    
                };
        
            }]);

/*------- Directives ------*/
angular.module('app')
    .directive('loading', function () {
        return {
            restrict: 'E',
            replace: true,
            template: '<div class="coverPhotoLoading"></div>',
            link: function (scope, element, attr) {
                scope.$watch('loading', function (val) {
                    if (val)
                        $(element).show();
                    else
                        $(element).hide();
                });
            }
        }
    })

    .filter('ytVideoId', function () {
        return function (url) {
            return /[^/]*$/.exec(url)[0];
        };
    })

    .filter('tagreplace', function () {
        return function (data) {
            console.log(data);
            return data.replace(/-/g, ' ').replace(/_/g, ' ');
        };
    })

    .directive('imageonload', function () {
        return {
            restrict: 'A',
            link: function (scope, element, attrs) {
                element.one('load', function () {
                    //call the function that was passed
                    scope.$apply(attrs.imageonload);
                }).each(function () {
                    if (this.complete) {
                        scope.$apply(attrs.imageonload);
                    }
                });
                element.one('error', function () {
                    //call the function that was passed
                    scope.$apply(attrs.imageonload);
                }).each(function () {
                    if (this.complete) {
                        scope.$apply(attrs.imageonload);
                    }
                });
            }
        };
    })

    .directive('featureImageOnload', function () {
        return {
            restrict: 'A',
            link: function (scope, element, attrs) {

                element.bind('load', function() {
                    // Remove min-height
                    element.css({ 'min-height' : '' } );

                    // load actual thumb image.
                    var imgLarge = new Image();
                    imgLarge.classList.add('largeFeatImage');
                    imgLarge.src = scope.post.image;
                    imgLarge.onload = function () {
                        imgLarge.classList.add('loaded');
                        element.replaceWith(imgLarge);
                        clearImage();
                    };
                    // element.replaceWith(imgLarge);
                    function clearImage() {
                        imgLarge = null;
                    }
                });

                element.bind('error', function(){
                    attrs.src = $scope.default_post_img;
                });
            }
        };
    })

    .filter('highlightNode', function ($sce) {
        return function (str, theTerms) {
            if (!theTerms) {
                return str;
            }
            // Sort terms by length
            theTerms.sort(function (a, b) {
                return b.length - a.length;
            });
            // Regex to simultaneously replace terms
            var regex = new RegExp("<a href='.*?'>|(" + theTerms.join('|') + ')', 'gi');
            var replaced = str.replace(regex, function (m, group1) {
                if (!group1) return m;
                else return $sce.trustAsHtml('<span class="hlNode">' + group1 + '</span>');
            });
            return replaced;
            // return $sce.trustAsHtml(replaced);
        };
    })

    .filter('highlightTag', function ($sce) {
        return function (str) {
            if (!str) return '[Caption]';
            // Convert new lines to break tag.
            var str = nl2br(str);
            var regex = new RegExp('\#([a-z-A-Z0-9]+)', 'gi');
            var replaced = str.replace(regex, function (m, group1) {
                if (!group1) return m;
                else return $sce.trustAsHtml('<a><span>#</span></span><span class="hlTag">' + group1 + '</span></a>');
            });
            return replaced;
        };
    })

    .directive('postCard', function ($timeout, $window, $http, $state) {
        return {
            restrict: 'AE',
            scope: false,
            templateUrl: "tpl.post-card",
            link: function ($scope, element, attrs) {
                if ($scope.$first === true) {
                    $scope.startPositionCards();
                    $scope.startReloadCardMasonry();
                }


                if ($scope.$last === true) {
                    $scope.stopPositionCards();
                    $scope.stopReloadCardMasonry();
                    $timeout(function () {
                        // Remove class.
                        $('.postCard').removeClass('postCardHide');
                    }, 1200);
                }

                /*
                 * determine post card class.
                 * toLoad => removing needs to be done manually
                 */
                $scope.cardClass = function (post) {
                    var cssClass = '';
                    if (post.post_type === 2 || ((post.post_type === 3 || post.post_type === 4 || post.post_type === 5) && !post.image)) {
                        if (!post.image)
                            cssClass = 'toLoad noImg';
                        else
                            cssClass = 'toLoad';
                    }
                    return cssClass;
                };

                var elm = element.find("div.postLoading");

                $timeout(function () {
                    /* Remove manually(from the house of manual script) */
                    var timeOutTime = $scope.post.post_type === 2 ? 2000 : 500;
                    $timeout(function () {
                        elm.removeClass("postLoading");
                    }, timeOutTime);
                }, 300);

                // DOM has finished rendering
                /*$timeout(function () {
                    elm.removeClass("postCard");
                });*/

                //$scope.popUpReportDropdrow = 0;
                $scope.report = function (post) {
                    $("#otherSub" + post.child_post_id).show();
                    $("#closeOverLayout" + post.child_post_id).show();

                    $scope.reportpostID = post.id;
                    var flag = 0;

                    $scope.openFrom = 'C';

                    if ($state.current.name === 'profile') {

                        if (post.child_post_user_id != $scope.user.id) {
                            flag = 1;
                        } else {
                            flag = 0;
                        }
                    } else if ($state.current.name == 'account') {

                        if (post.created_by != $scope.user.id) {
                            flag = 1;
                        } else {
                            flag = 0;
                        }

                    } else {

                        if (post.created_by != $scope.user.id) {
                            flag = 1;
                        } else {
                            flag = 0;
                        }
                    }
                    $scope.isShowPostReportLink = flag;
                };
            }
        };
    })

    .directive('searchPostCard', function ($timeout, $window, $http, $state) {
        return {
            restrict: 'AE',
            scope: false,
            templateUrl: "tpl.search-post-card",
            link: function ($scope, element, attrs) {
                if ($scope.$first === true) {
                    $scope.startPositionCards();
                    $scope.startReloadCardMasonry();
                }

                if ($scope.$last === true) {
                    $scope.stopPositionCards();
                    $scope.stopReloadCardMasonry();
                }

                /*
                 * determine post card class.
                 * toLoad => removing needs to be done manually
                 */
                $scope.cardClass = function (post) {
                    var cssClass = '';
                    if (post.post_type === 2 || ((post.post_type === 3 || post.post_type === 4 || post.post_type === 5) && !post.image)) {
                        if (!post.image)
                            cssClass = 'toLoad noImg';
                        else
                            cssClass = 'toLoad';
                    }
                    return cssClass;
                };


                $timeout(function () {
                    var elm = element.find("div.postLoading");
                    /* Remove manually */
                    $timeout(function () {
                        elm.removeClass("postLoading");
                    }, 2000);
                }, 300);

                //$scope.popUpReportDropdrow = 0;
                $scope.report = function (post) {
                    $("#otherSub" + post.id).show();
                    $("#closeOverLayout" + post.id).show();

                    $scope.reportpostID = post.id;
                    var flag = 0;
                    $scope.openFrom = 'C';

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

                    } else {
                        if (post.created_by != $scope.user.id) {
                            flag = 1;
                        } else {
                            flag = 0;
                        }
                    }
                    $scope.isShowPostReportLink = flag;
                };
            }
        };
    })

    .directive('testPostCard', function ($timeout, $window, $http, $state) {
        return {
            restrict: 'AE',
            scope: false,
            templateUrl: "tpl.test-post-card",
            link: function ($scope, element, attrs) {
                if ($scope.$first === true) {
                    $scope.startPositionCards();
                    // $scope.startReloadCardMasonry();
                }

                if ($scope.$last === true) {
                    $scope.stopPositionCards();
                    // $scope.stopReloadCardMasonry();
                }

                /*
                 * determine post card class.
                 * toLoad => removing needs to be done manually
                 */
                /*$scope.cardClass = function(post) {
                 var cssClass = '';
                 if (post.post_type === 2 || ((post.post_type === 3 || post.post_type === 4 || post.post_type === 5) &&
                 !post.image))
                 {
                 if (!post.image)
                 cssClass = 'toLoad noImg';
                 else
                 cssClass = 'toLoad';
                 }
                 return cssClass;
                 };*/

                setTimeout(function () {
                    var elm = element.find("div.postLoading");
                    /* Remove manually(from the house of manual script) */
                    setTimeout(function () {
                        elm.css("opacity", 1);
                        elm.removeClass("postLoading");
                    }, 1200);
                    /*setTimeout(function() {
                     elm.removeClass("postLoading");
                     }, 1200);*/
                    /*if (elm.hasClass('toLoad')) {
                     if (elm.hasClass('noImg')) {
                     elm.removeClass("postLoading");
                     }
                     else {
                     $timeout(function() {
                     elm.removeClass("postLoading");
                     }, 2000);
                     }
                     }*/
                }, 300);

                //$scope.popUpReportDropdrow = 0;
                $scope.report = function (post) {
                    // $scope.popUpReportDropdrow = !$scope.popUpReportDropdrow;
                    //$("#otherSub"+post.id).show();
                    //$("#closeOverLayout"+post.id).show();

                    $("#otherSub" + post.id).show();
                    $("#closeOverLayout" + post.id).show();

                    $scope.reportpostID = post.id;
                    var flag = 0;

                    $scope.openFrom = 'C';

                    if ($state.current.name == 'profile') {

                        if (post.child_post_user_id != $scope.user.id) {
                            flag = 1;
                        } else {
                            flag = 0;
                        }


                    } else if ($state.current.name == 'account') {

                        if (post.created_by != $scope.user.id) {
                            flag = 1;
                        } else {
                            flag = 0;
                        }

                    } else {

                        if (post.created_by != $scope.user.id) {
                            flag = 1;
                        } else {
                            flag = 0;
                        }
                    }
                    $scope.isShowPostReportLink = flag;
                };
            }
        };
    })

    .directive('contentLoaded', function ($timeout) {
        return {
            restrict: 'A',
            link: function ($scope, element, attrs) {
                /*$timeout(function() {
                 var elm = $(element);
                 var imgElm = elm.find("img.cardLoadChk");
                 if (imgElm.length > 0) {
                 imgElm.bind('load', function () {
                 console.log('img is loaded.');
                 });
                 }
                 }, 10);*/
            }
        }
    })

    .directive('logCardCreation', function () {
        return {
            restrict: 'A',
            link: function ($scope, element, attrs) {
                console.info(
                    "Card created: " + $scope.$index,
                    $scope.post.title ? $scope.post.title : $scope.post.caption
                    // $scope.post
                );
            }
        }
    })

    .directive('postTypeNav', function ($state, $timeout) {
        return {
            restrict: 'E',
            scope: false,
            templateUrl: function (elem, attrs) {
                return "tpl.post-type-nav/" + attrs.navtype
            },
            link: function ($scope, element, attrs) {
                // OwlCarousel custom options.
                $scope.navTypeOwlOptions = {
                    loop: false,
                    nav: false,
                    margin: 0,
                    navigation: false,
                    //startPosition:1,
                    items: 7
                };
                $scope.getPost = function (postType) {
                    if ($scope.mySwitch == true) {
                        return false;
                    }

                    if ($state.current.name === 'profile' || $state.current.name === 'account') {
                        $scope.resetPostData();
                        $scope.loadMore(postType);
                    }
                    else {
                        $scope.resetData();
                        $scope.setPostType(postType);
                        if ($state.current.name === 'search') {
                            $scope.fetchPostData($scope.currentTab);
                        }
                        else {
                            $scope.fetchPostData();
                        }
                    }
                };
            }
        }
    })
    /*
     * Directive for top channel cards.
     */
    .directive('channelCard', function () {
        return {
            restrict: 'E',
            templateUrl: "tpl.channel-card",
        };
    })

    .directive('searchChannelCard', function () {
        return {
            restrict: 'E',
            templateUrl: "tpl.search-channel-card",
        };
    })

    /*
     * Directive for followingTopics.
     */
    .directive('followingTopics', function () {
        return {
            restrict: 'E',
            templateUrl: "tpl.topics",
        };
    })

    .directive('hitEnter', function () {
        return function (scope, element, attrs) {
            element.bind("keydown keypress", function (event) {
                if (event.which === 13) {
                    scope.$apply(function () {
                        scope.$eval(attrs.hitEnter);
                    });

                    event.preventDefault();
                }
            });
        };
    })

    .directive('fallbackSrc', function () {
        return function (scope, element, attrs) {
            element.bind("error", function () {
                scope.$apply(function () {
                    scope.$eval(attrs.fallbackSrc);
                });
            });
        };
    })

    // Directives for owl carousal..
    .directive("owlCarousel", ['$timeout', '$compile', function ($timeout, $compile) {
        return {
            restrict: 'E',
            transclude: false,
            link: function (scope) {
                scope.initCarousel = function (element) {
                    // provide any default options you want
                    var defaultOptions = {
                        margin: 0
                    };
                    // Set timeout for owl init.
                    var time = scope.$eval($(element).attr('data-owl-time'));
                    if (!time) {
                        time = 0;
                    }

                    var customOptions = scope.$eval($(element).attr('data-options'));
                    // combine the two options objects
                    for (var key in customOptions) {
                        defaultOptions[key] = customOptions[key];
                    }

                    $timeout(function () {
                        // init carousel
                        $(element).owlCarousel(defaultOptions);
                        $(element).show();
                        scope.owlCenterAlign();
                        // Hide loader if any
                        if ($(".smallLoaderOwl").length > 0) {
                            $(".smallLoaderOwl").remove();
                        }
                    }, time);
                };
            }
        };
    }])
    .directive('owlCarouselItem', ['$timeout', function ($timeout) {
        return {
            restrict: 'A',
            transclude: false,
            link: function (scope, element) {
                // wait for the last item in the ng-repeat then call init
                if (scope.$last) {
                    $timeout(function () {
                        scope.initCarousel(element.parent());
                    }, 3000);
                }
            }
        };
    }])
    // Directives for flickity carousal..
    .directive('flickityItem', ['$timeout', function ($timeout) {
        return {
            restrict: 'A',
            transclude: false,
            link: function (scope, element) {
                // wait for the last item in the ng-repeat then call init.
                if (scope.$last) {
                    var time = 1000;
                    var parentElm = element.parent();
                    var dataHideSelector = parentElm.attr('data-hide-selector');
                    var dataHideTime = parentElm.attr('data-hide-time');
                    if (dataHideTime) {
                        time = dataHideTime;
                    }
                    setTimeout(function () {
                        scope.initFlickity(element.parent());
                        // Hide loader element.
                        if (dataHideSelector) {
                            $(dataHideSelector).remove();
                        }
                    }, time);
                }
            }
        };
    }])

    .directive('hideLoader', ['$timeout', function ($timeout) {
        return {
            restrict: 'A',
            link: function (scope, element, attrs) {
                // wait for the last item in the ng-repeat then call init
                if (scope.$last) {
                    var selector = element.parent().attr('data-hide-selector');
                    var time = element.parent().attr('data-hide-time');
                    if (!time) {
                        time = 0;
                    }
                    $timeout(function () {
                        var $selector = $(selector);
                        if ($selector.length > 0) {
                            $selector.hide();
                        }
                    }, time);
                }
            }
        };
    }])

    .directive('vimeoVideo', function ($timeout, $interval, constants) {

        return {
            restrict: "E",
            link: function ($scope, element, attr) {

                var options = {
                    id: attr.vid,
                    width: 640,
                    loop: true
                };
                var isPlayed;
                $timeout(function () {
                    var player = new Vimeo.Player(attr.id, options);

                    /*player.on('play', function () {
                        $scope.viewVideoPost(post_id,child_post_id,type);
                    });*/
                    // player.addEvent('ready', function() { });

                    player.getDuration().then(function(duration) {
                        $interval(function () {
                            player.getCurrentTime().then(function (seconds) {
                                if (seconds >= duration * constants.VIDEO_VIEW_PER && !isPlayed) {
                                    $scope.viewVideoPost($scope.post);
                                    isPlayed = true;
                                }
                            });
                        }, 100);
                    }).catch(function(error) {
                        // an error occurred
                    });

                    player.pause();

                }, 200);

            }
        }
    })

    .directive('dailyMotion', function ($timeout, constants) {

        return {
            restrict: "E",
            link: function ($scope, element, attr) {

                var isPlayed = false;
                $timeout(function () {

                    var player = DM.player(document.getElementById(attr.id), {
                        video: attr.vid,
                        width: '100%',
                        height: '100%',
                        params: {
                            autoplay: false,
                            mute: false
                        }
                    });

                    /* Watch for ads */
                    var isShowingAds = false;
                    player.addEventListener('ad_start', function(event) {
                        isShowingAds = true;
                        player.removeEventListener('ad_start', function () {
                            //    Nothing.
                        });
                    });
                    player.addEventListener('ad_end', function(event) {
                        isShowingAds = false;
                        player.removeEventListener('ad_end', function () {
                            //    Nothing.
                        });
                    });

                    var duration;
                    player.addEventListener('timeupdate', function(event) {
                        setTimeout(function () {
                            if (!duration) {
                                duration = player.duration;
                            }
                            var currentTime = player.currentTime;
                            // console.log('timeupdate: duration: '+duration+' currentTime: '+currentTime);
                            if (
                                currentTime >= duration * constants.VIDEO_VIEW_PER &&
                                !isPlayed &&
                                !isShowingAds
                            ) {
                                console.info('Total duration: '+duration+' currentTime: '+currentTime);
                                $scope.viewVideoPost($scope.post);
                                isPlayed = true;
                            }
                        }, 100);
                    });

                }, 200);
            }
        }
    })

    .directive('manualVideo', function ($sce, constants) {

        var linkFunction = function ($scope, element, attrs) {

            $scope.postcardid = attrs.postcardid;
            $scope.childpostid = attrs.childpostid;
            $scope.posttype = attrs.posttype;
            $scope.type = attrs.type;
            $scope.url = $sce.trustAsResourceUrl(attrs.url);

            var isPlayed = false;
            var video = $(element).find(".videoTag");

            video.bind("timeupdate", function () {
                if (this.currentTime >= this.duration * constants.VIDEO_VIEW_PER && !isPlayed) {
                    $scope.viewVideoPost($scope.post);
                    isPlayed = true;
                }
            });
        };

        return {
            restrict: "E",
            template: '<video class="videoTag na" loaded-meta-data="{{type}}" controls loop  playsinline webkit-playsinline ng-attr-poster="{{post.video_poster}}" postcardid="{{postcardid}}" id="cardVideo-{{post.cardID}}" type="{{type}}" posttype="{{posttype}}"><source ng-src="{{url}}"></video>',
            link: linkFunction
        };
    })

    .directive('loadedMetaData', function ($rootScope, $timeout) {
        return {
            restrict: 'A',
            link: function (scope, element, attrs) {
                element[0].addEventListener("loadedmetadata", function () {
                    if (attrs.loadedMetaData === 'C') {
                        $rootScope.$broadcast('masonry.reload');
                    }
                });
                scope.$on('$destroy', function () {
                    element[0].removeEventListener("loadedmetadata", function(){});
                });
            }
        };
    })

    /*
     * Use this directive together with ng-include to include a
     * template file by replacing the placeholder element
     */
    .directive('includeReplace', function () {
        return {
            require: 'ngInclude',
            restrict: 'A',
            link: function (scope, el, attrs) {
                el.replaceWith(el.children());
            }
        };
    })
    /*
     * Disable click if condition satified.
     */
    .directive('eatClickIf', ['$parse', '$rootScope',
        function ($parse, $rootScope) {
            return {
                // this ensure eatClickIf be compiled before ngClick
                priority: 100,
                restrict: 'A',
                compile: function ($element, attr) {
                    var fn = $parse(attr.eatClickIf);
                    return {
                        pre: function link(scope, element) {
                            var eventName = 'click';
                            element.on(eventName, function (event) {
                                var callback = function () {
                                    if (fn(scope, {$event: event})) {
                                        // prevents ng-click to be executed
                                        event.stopImmediatePropagation();
                                        // prevents href
                                        event.preventDefault();
                                        return false;
                                    }
                                };
                                if ($rootScope.$$phase) {
                                    scope.$evalAsync(callback);
                                } else {
                                    scope.$apply(callback);
                                }
                            });
                        },
                        post: function () {
                        }
                    }
                }
            }
        }
    ])
;
/*------ Filters -------*/
angular.module('app')
    .filter('isEmpty', function () {
        var bar;
        return function (obj) {
            for (bar in obj) {
                if (obj.hasOwnProperty(bar)) {
                    return false;
                }
            }
            return true;
        };
    })

    .filter('trustUrl', function ($sce) {
        return function (url) {
            return $sce.trustAsResourceUrl(url);
        };
    })

    .filter('ucfirst', function () {
        return function (string) {
            if (string && angular.isString(string)) {
                return string.charAt(0).toUpperCase() + string.slice(1);
            }
            return string;
        };
    })

    .filter('limitShortDesc', function () {
        return function (short_description) {
            /*if (short_description && short_description.length > 100) {
                return short_description.substring(0, 100) + "...";
            }*/
            return short_description;
        };
    })

    .filter('limitUserAbout', function () {
        return function (about) {
            if (about && about.length > 100) {
             return about.substring(0, 100) + "...";
             }
            return about;
        };
    })

    .filter('lpArticleContent', function () {
        return function (content) {
            if (content) {
                content = content.replace(/<video.*<\/video>|<(?!p\s*\/?)[^>]+>/g, '');
                if (content.length > 200) {
                    content = content.substring(0, 200) + "...";
                }
            }
            return content;
        };
    })

    .filter('domainFilter', function () {
        return function (url) {
            if (!url) return '';
            var domain;
            // find & remove protocol (http, ftp, etc.) and get domain
            if (url.indexOf("://") > -1) {
                domain = url.split('/')[2];
            }
            else {
                domain = url.split('/')[0];
            }
            // find & remove port number
            domain = domain.split(':')[0];
            domain = domain.replace('www.', '');
            return domain;
        };
    })

    .filter('ageFilter', function () {
        function calculateAge(birthday) { // birthday is a date
            var ageDifMs = Date.now() - birthday.getTime();
            var ageDate = new Date(ageDifMs); // miliseconds from epoch
            return Math.abs(ageDate.getUTCFullYear() - 1970);
        }

        function monthDiff(d1, d2) {
            if (d1 < d2) {
                if (d2.getMonth() > d1.getMonth()) {
                    var months = d2.getMonth() - d1.getMonth();
                }
                else {
                    var months = (12 - d1.getMonth()) + d2.getMonth();
                }
                return months <= 0 ? 0 : months;
            }
            return 0;
        }

        return function (birthdate) {
            if (!birthdate)
                return '';

            if (birthdate.indexOf('-') == 0) {
                return '';
            }

            birthdate = parseDate(birthdate);

            var age = calculateAge(birthdate);
            if (age == 0)
                return monthDiff(birthdate, new Date()) + ' months old';
            return age + ' years old';
        };
    })


    .filter('strLimit', function () {
        return function (content, limit) {
            if (content) {
                content = content.substring(0, limit);
            }
            return content;
        };
    })


    .filter('thousandSuffix', function () {
        return function (number, fractionSize) {

            if (isNaN(number) || number === null) return null;
            if (number === 0) return "0";

            if (number <= 9999) {
                return number;
            }

            if (!fractionSize || fractionSize < 0)
                fractionSize = 2;

            var abs = Math.abs(number);
            var rounder = Math.pow(10, fractionSize);
            var isNegative = number < 0;
            var key = '';
            var powers = [
                {key: "Q", value: Math.pow(10, 15)},
                {key: "T", value: Math.pow(10, 12)},
                {key: "B", value: Math.pow(10, 9)},
                {key: "M", value: Math.pow(10, 6)},
                {key: "K", value: 1000}
            ];

            for (var i = 0; i < powers.length; i++) {

                var reduced = abs / powers[i].value;

                reduced = Math.round(reduced * rounder) / rounder;

                if (reduced >= 1) {
                    abs = reduced;
                    key = powers[i].key;
                    break;
                }
            }

            return (isNegative ? '-' : '') + abs + key;
        };
    })

    /*.filter('thousandSuffix', function () {
     return function (number)  {
     var ret;
     if(number>9999){

     var abs = Math.abs(number);
     var key = '';
     var powers = [
     {key: "q", value: Math.pow(10,15)},
     {key: "t", value: Math.pow(10,12)},
     {key: "b", value: Math.pow(10,9)},
     {key: "m", value: Math.pow(10,6)},
     {key: "k", value: 1000}
     ];
     var num=0;
     var keyType;
     for(var i = 0; i < powers.length; i++) {
     if (number >= powers[i].value) {
     keyType=powers[i].key;
     num=number/powers[i].value;
     break;
     }
     }

     sNumber = num.toString(); //NUMBER CONVERT TO STRING

     if(sNumber.indexOf(".")!=-1) { // IF NUMBER HOLD POINT THEN EXECUTE THIS BLOCK

     var arr= sNumber.split('.');
     var firstValue=arr[0];
     var lastValue='';

     if(firstValue.length<3) {

     if(firstValue.length>1) {
     lastValue=arr[1].substring(0, 1);
     } else {
     var subStr=arr[1].substring(0, 2);

     if(subStr.length<2) {    // SUPPOSE 1.20M
     lastValue=subStr+"0";
     } else {
     lastValue=subStr;
     }
     }
     finalVal=firstValue+"."+lastValue;
     } else  {
     finalVal=firstValue; //SUPPOSE POINT IS AFTER 3 DIGITS   EXAMPLE :: 121.1234
     }
     } else {
     finalVal=sNumber;
     }
     ret=finalVal+""+keyType
     } else  {
     ret=number
     }
     return ret;
     };
     })*/

    .filter('domain', [function () {
        return function (input) {
            var elements = angular.element('<a href="' + input + '"/>');
            return elements[0].hostname;
        };
    }])

    .filter('markupHTMLTags', function ($sce) {
        return function (text) {
            return $sce.trustAsHtml(text);
        };
    })

    .filter('htmlToPlaintext', function () {
        return function (text) {
            return text ? String(text).replace(/<[^>]+>/gm, '') : '';
        }
    })

    .filter('elapsed', function ($filter) {
        return function (date) {
            if (!date) return;
            var time = Date.parse(date),
                timeNow = new Date().getTime(),
                difference = timeNow - time,
                seconds = Math.floor(difference / 1000),
                minutes = Math.floor(seconds / 60),
                hours = Math.floor(minutes / 60),
                days = Math.floor(hours / 24);
            if (days > 7) {
                return $filter('date')(time, "MMMM d 'at' h:mm a");
            } else if (days > 1) {
                return days + " days ago";
            } else if (days == 1) {
                return "1 day ago"
            } else if (hours > 1) {
                return hours + " hours ago";
            } else if (hours == 1) {
                return "an hour ago";
            } else if (minutes > 1) {
                return minutes + " minutes ago";
            } else if (minutes == 1) {
                return "a minute ago";
            } else {
                return "a few seconds ago";
            }
        }
    })


    .filter('dateTimeFormat', function ($filter) {
        return function (date) {
            if (!date) return;
            var time = Date.parse(date),
                timeNow = new Date().getTime(),
                difference = timeNow - time,
                seconds = Math.floor(difference / 1000),
                minutes = Math.floor(seconds / 60),
                hours = Math.floor(minutes / 60),
                days = Math.floor(hours / 24);

            return $filter('date')(time, "MMMM d 'at' h:mm a");
        }
    })

    .filter('formatDistance', function () {
        return function (distance) {
            var formattedDistance = parseFloat(distance);
            if (formattedDistance > 1) {
                formattedDistance = formattedDistance.toFixed(1) + "km";
            }
            else {
                formattedDistance = formattedDistance * 1000;
                formattedDistance = parseInt(formattedDistance) + "m";
            }
            return formattedDistance;
        };
    })

    .filter('cut', function () {
        return function (value, wordwise, max, tail) {
            if (!value) return '';

            max = parseInt(max, 10);
            if (!max) return value;
            if (value.length <= max) return value;

            value = value.substr(0, max);
            if (wordwise) {
                var lastspace = value.lastIndexOf(' ');
                if (lastspace != -1) {
                    //Also remove . and , so its gives a cleaner result.
                    if (value.charAt(lastspace - 1) == '.' || value.charAt(lastspace - 1) == ',') {
                        lastspace = lastspace - 1;
                    }
                    value = value.substr(0, lastspace);
                }
            }

            return value + (tail || ' ...');
        };
    })

    .filter('newlines', function () {
        return function (text) {
            return text.replace(/\n/g, '<br/>');
        }
    })

;

/*------ Application Services ------*/
angular.module('app')
    .factory('refreshService', ['$http', function ($http) {
        return {
            userData: function () {
                var url = 'angular/auth-json';
                return $http.post(url).then(function (response) {
                    return response.data;
                });
            },
            notification: function (page, perpage) {
                perpage = perpage > 1 ? perpage : 10;
                var url = 'api/notification';
                var postData = {
                    page: page,
                    perpage: perpage
                };
                return $http.post(url, postData).then(function (response) {
                    return response.data;
                });
            },
            top_search: function () {
                var url = 'api/top-search';
                var postData = {};
                return $http.post(url, postData).then(function (response) {
                    return response.data;
                });
            }
        };
    }])
    .factory('notify', [function () {
        return function (notification) {
            var type = notification.type ? notification.type : 'flip';
            var timeout = notification.timeout ? notification.timeout : 3000;
            /*
             | Placement of the notification
             | top, top-left, top-right, bottom-right
             */
            var position = notification.position ? notification.position : 'top-right';
            var message = notification.message; // Message to display inside the notification
            // error, info, warning
            var color = notification.color ? notification.color : 'info';

            switch (type) {
                case 'bar':
                    // Show an bar notification attached to top and bottom of the screen
                    $('body').pgNotification({
                        style: 'bar',
                        message: message,
                        position: position,
                        timeout: timeout,
                        type: color
                    }).show();
                    break;
                case 'flip':
                    // Show a flipping notification animated
                    // using CSS3 transforms and animations
                    $('body').pgNotification({
                        style: 'flip',
                        message: message,
                        position: position,
                        timeout: timeout,
                        type: color
                    }).show();
                    break;
                case 'simple':
                    // Simple notification having bootstrap's .alert class
                    $('body').pgNotification({
                        style: 'simple',
                        message: message,
                        position: position,
                        timeout: timeout,
                        type: color
                    }).show();
                    break;
            }
        };


    }])
    //Local Storage Service
    //Author:Alapan Chatterjee; Date:25-01-2017
    .service('myLocalStorage', function (localStorageService) {
        return {
            submit: function (key, val) {
                return localStorageService.set(key, val);
            },
            getItem: function (key) {
                return localStorageService.get(key);
            },
            getAllKeys: function () {
                return localStorageService.keys();
            },
            removeItem: function (key) {
                return localStorageService.remove(key);
            },
            clearAll: function () {
                return localStorageService.clearAll();
            }
        };
    })
    //Distance Calculaton between two given sets of ltitude and longitude
    //Author:Alapan Chatterjee; Date:26-01-2017
    .service('haversineDistanceCalculationService', function () {
        return {

            findDistance: function (latitudeFrom, longitudeFrom, latitudeTo, longitudeTo) {
                var radius = 6371;
                var toRad = function (x) {
                    return (x * Math.PI) / 180;
                };
                if (latitudeFrom == '' || longitudeFrom == '' || latitudeTo == '' || longitudeTo == '') {
                    return null;
                }
                var x1 = latitudeTo - latitudeFrom;
                var dLat = toRad(x1);
                var x2 = longitudeTo - longitudeFrom;
                var dLon = toRad(x2);
                var a = Math.sin(dLat / 2) * Math.sin(dLat / 2) + Math.cos(toRad(latitudeFrom)) * Math.cos(toRad(latitudeTo)) * Math.sin(dLon / 2) * Math.sin(dLon / 2);
                var c = 2 * Math.atan2(Math.sqrt(a), Math.sqrt(1 - a));
                var d = radius * c;
                return d;
            }
        };
    })
    .service('userDataService', function ($stateParams, $http, $state) {   // load user details  and update user date .

        var userData = [];

        return {
            loadUserData: function () {
                var url = 'angular/profileList';
                var data = {}
                if ($stateParams.username) {
                    data = {username: $stateParams.username};
                }
                return $http.post(url, data).then(function (response) {
                    userData = response.data;
                    return userData;
                }, function (error) {

                    window.location.href = "error-404";
                });
            },
            updateUserData: function (user_points) {
                userData.user.points = user_points;
            },
            getData: function () {
                return userData;
            }

        };
    })

    .service('inArray', function () {

        this.arrayIndexOf = function (array, value) {
            var index = -1;
            if (angular.isArray(array)) {
                angular.forEach(array, function (val, key) {
                    if (val == value) {
                        index = key;
                        return false;
                    }
                });
            }
            return index;
        }
    });


/* POST CARD SHARE POPUP SHOW HIDE */
$("body").on("click", ".shrClk", function () {
    var shrClk = $(".shrClk");
    shrClk.parent().children(".subOverlay").hide();
    shrClk.parent().children(".sub").hide();
    $(this).parent().children(".subOverlay").show();
    $(this).parent().children(".sub").show();
});
$("body").on("click", ".subOverlay", function () {
    $(this).hide();
    $(this).parent().children(".sub").hide();
});
$("body").on("click", ".cardSmNav .sub ul li", function () {
    var shrClk = $(".shrClk");
    shrClk.parent().children(".subOverlay").hide();
    shrClk.parent().children(".sub").hide();
});

/* Report comment box  */
$("body").on("click", ".comntInlineModal a", function () {
    if (!$(this).parent().children().children(".reporPopupVW").is(":visible")) {
        $('.reporPopupVW').hide();
        $(this).parent().children().children(".reporPopupVW").show();
    } else {
        $('.reporPopupVW').hide();
    }
});
$(document).click(function (e) {
    var containerList = $('.comntInlineModal a');
    if (!containerList.is(e.target) && containerList.has(e.target).length == 0) {
        $('.reporPopupVW').hide();
    }
});
$("body").on("click", ".cardSmNav.last .moreBtnN", function () {
    $('.profileCommentFooter.addNew .left, .profileCommentFooter.addNew .right').addClass("show2");
    $(this).parent().parent(".right").removeClass("show2");
});
$("body").on("click", ".subOverlay", function () {
    $('.profileCommentFooter.addNew .left, .profileCommentFooter.addNew .right').removeClass("show2");
});

/* VIDEO PLAY PAUSE */
/*
 $("body").on("mouseenter",".blockContent .profileCommentBox", function() {
 //console.log("Video Play");
 if($(this).find(".videoTag").is(":visible")){
 $(this).find(".videoTag").parent().children(".customPlayPause").addClass("out");
 $(this).find(".videoTag")[0].play();
 }else if($(this).find(".iframeTag").is(":visible")){
 //console.log("Youtube Play");
 //$('.iframeTag')[0].contentWindow.postMessage('{"event":"command","func":"' + 'playVideo' + '","args":""}', '*');
 }
 });
 $("body").on("mouseleave",".blockContent .profileCommentBox", function() {
 //console.log("Video Pause");
 $(".videoTag")[0].pause();
 $(".videoTag").parent().children(".customPlayPause").removeClass("out");
 //$('.iframeTag')[0].contentWindow.postMessage('{"event":"command","func":"' + 'pauseVideo' + '","args":""}', '*');
 });
 */
/*
 $("body").on("mouseover",".profileCommentBox", function () {
 $(this).find('.iframeTag')[0].contentWindow.postMessage('{"event":"command","func":"' + 'playVideo' + '","args":""}', '*');
 });
 $("body").on("mouseout",".profileCommentBox", function () {
 $(this).find('.iframeTag')[0].contentWindow.postMessage('{"event":"command","func":"' + 'pauseVideo' + '","args":""}', '*');
 });

 */
/*
 $("body").on("click",".saveDel", function () {
 if(!$(this).children(".saveIcon").is(":visible")){
 $(this).children(".saveIcon").css({"display":"inline-block"});
 $(this).children(".savedIcon").css({"display":"none"});
 }else{
 $(this).children(".saveIcon").css({"display":"none"});
 $(this).children(".savedIcon").css({"display":"inline-block"});
 }
 });
 */

/* Suggested Users Remove */
$("body").on("click", ".suggestedUsers .cls", function () {
    $(".suggestedUsers").remove();
});

$("body").on("click", ".suggestedUsers .floBtn .followBtn", function () {
    if (!$(this).children(".ico").is(":visible")) {
        $(this).children("span").css({"display": "none"});
        $(this).children(".ico").css({"display": "block"});
    } else {
        $(this).children("span").css({"display": "block"});
        $(this).children(".ico").css({"display": "none"});
    }
});

$("body").on("click", ".exploreDropdown .navTl", function () {
    if (!$(this).next("ul").is(":visible")) {
        $(this).next("ul").css({"display": "block"});
    } else {
        $(this).next("ul").css({"display": "none"});
    }
});

$(document).click(function (e) {
    var exploreNAV = $('.exploreDropdown .navTl');
    if (!exploreNAV.is(e.target) && exploreNAV.has(e.target).length == 0) {
        $('.exploreDropdown ul').css({"display": "none"});
    }
});

//Profile userlinks
/* $("body").on("click", ".usrLinkArrow", function () {
    $(this).addClass("sl");
	$(".profileUserShow").addClass("vw");
    $(".userOwnLinksArea").css({"height": "auto"});
});
$("body").on("click", ".usrLinkArrow.sl", function () {
    $(this).removeClass("sl");
	$(".profileUserShow").removeClass("vw");
    $(".userOwnLinksArea").css({"height": "1px"});
}); */

$("body").on("click", ".profileUserShow .userBox", function () {
	$(".profileUserShow").addClass("vw");
	$(".usrLinkArrow").addClass("sl");
	$(".userOwnLinksArea").css({"height": "auto"});
});
$("body").on("click", ".profileUserShow.vw", function () {
	$(".profileUserShow").removeClass("vw");
	$(".usrLinkArrow").removeClass("sl");
	$(".userOwnLinksArea").css({"height": "1px"});
});

$("body").on("click", ".profileUserShow", function () {
	if($(window).width() <= 641){
		$(".profileUserShow").addClass("vw");
		$(".usrLinkArrow").addClass("sl");
		$(".userOwnLinksArea").css({"height": "auto"});
	}
});
$("body").on("click", ".profileUserShow.vw", function () {
	if($(window).width() <= 641){
		$(".profileUserShow").removeClass("vw");
		$(".usrLinkArrow").removeClass("sl");
		$(".userOwnLinksArea").css({"height": "1px"});
	}
});

$("body").on("click", ".customSelbox", function () {
	$(this).next(".customSelectListCont").fadeIn(300);
	$("html").addClass("categoryovr");
});
$("body").on("click", ".selectOvrlay, .customSelectList .cls", function () {
	$(".customSelectListCont").fadeOut(300);
	$("html").removeClass("categoryovr");
});
$("body").on("click", ".customSelectList ul li", function () {
	/*var thisvalue = $(this).html();
	$(this).parent().parent().parent().parent().children().children(".form-control").html(thisvalue);*/
	$(".customSelectListCont").fadeOut(300);
	$("html").removeClass("categoryovr");
});

$(window).on('load', function(){
	var headr = $('.coverPhoto');
	var range = 120;
	$(window).on('scroll', function(){
		var scrollTop = $(this).scrollTop(),
			  height = headr.height(),
			  offset = height / 2,
			  calc = 1 - (scrollTop - offset + range) / range;
			  calc2 = 1 - calc;
			  
		  $('.header').css({'background':'rgba(255,255,255,' +calc2+')'});

		  if (calc2 <= 1) {
			$('.header').css({'background':'rgba(255,255,255,0)'});
		  } else if ( calc2 >= 2 ) {
			$('.header').css({'background':'rgba(255,255,255,1)'});
		  }  
	});
});