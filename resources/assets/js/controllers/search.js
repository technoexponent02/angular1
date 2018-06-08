'use strict';

/* Search Controllers */

angular.module('app')

.controller('QuickSearchCtrl', ['$scope', '$state', '$timeout', '$window', 'constants', 'notify', 'refreshService', '$interval', function($scope, $state, $timeout, $window, constants, notify, refreshService, $interval) {
	
    setTimeout(function () {
		$('#overlay-search').on( "touchstart", function(){
			$(this).trigger('mouseenter');
		});
		$('.popupsearch').on( "touchstart", function(){
			$(this).children("input").trigger('mouseenter');
		});
    }, 600);

    $scope.showSearchInputLoader = false;
    $scope.search_min_len = constants.SEARCH_MIN_LEN;

    $scope.goToSearch = function() {
        $scope.showSearchInputLoader = true;
        $timeout(function() {
            if ($scope.search.query && $scope.search.query.length >= constants.SEARCH_MIN_LEN) {
                if ($state.current.name == 'search') {
                    $window.location.href = 'search?q=' + $scope.search.query;
                }
                else {
                    $state.go('search', {q: $scope.search.query});
                }
                $timeout(function() {
                    $scope.showSearchInputLoader = false;
                }, 2000);
            }
            else {
                $scope.showSearchInputLoader = false;
                var notification = {
                    'message': 'Please enter atleast ' + constants.SEARCH_MIN_LEN + ' characters to search.',
                    'color': 'error'
                };
                notify(notification);
            }
        }, 1200);
    };

    $scope.goToTopSearch = function (query) {
        $window.location.href = '/search?q=' + query;
    };

    /*-------- Top 10 search ----------*/
    $scope.topSearches = [];
    $scope.loadTopSearch = function () {
        refreshService.top_search().then(function(data) {
            if(data.length) {
                $scope.topSearches = [];
                data.forEach(function (keyword) {
                    $scope.topSearches.push(keyword);
                });
            }

        });
    };
    
    if ($scope.user && $scope.user.guest == 0) {
        // Load initially.
        $timeout(function () {
            $scope.loadTopSearch();
        }, 2000);
    }
    // Trigger every 30 seconds for logged in user.
    $interval(function() {
        if ($scope.user && $scope.user.guest == 0) {
            $scope.loadTopSearch();
        }
    }, 32000);

}])

.controller('SearchCtrl', ['$scope', '$state', '$stateParams', '$http', '$timeout', '$location', 'notify', 'constants', function($scope, $state, $stateParams, $http, $timeout, $location, notify, constants) {
    // set the post id
    if (typeof $scope.search == 'undefined') {
        $scope.search = {};
    }
    if ($stateParams.q) {
        $scope.search.query = $stateParams.q;
    }
    else {
        var notification = {
            'message': 'Please enter at least ' + constants.SEARCH_MIN_LEN + ' characters to search.',
            'color': 'error'
        };
        notify(notification);
    }

    // Search tabs.
    $scope.currentTab = 'post';
    $scope.searchNavItems = [
        {
            name: 'Post',
            id:   'post' 
        },
        {
            name: 'Channel',
            id:   'channel'
        },
        {
            name: 'Tag',
            id:   'tag'
        },
        {
            name: 'Location',
            id:   'location'
        }
    ];
    $scope.changeTab = function(index){
		$("body").removeClass("wh");
        $scope.currentTab = $scope.searchNavItems[index].id;
        if ($scope.currentTab == 'post') {
            $scope.startPositionCards();
            setTimeout(function() {
                $scope.stopPositionCards(300);
            }, 1000);
        }else if ($scope.currentTab == 'tag' || $scope.currentTab == 'location') {
            $("body").addClass("wh");
        }
    };

    $scope.showSearchInputLoader = false;

    $scope.goToSearch = function() {
        $scope.showSearchInputLoader = true;
        $timeout(function() {
            if ($scope.search.query && $scope.search.query.length >= constants.SEARCH_MIN_LEN) {
                $scope.resetData();
                $state.go('search', {q: $scope.search.query});
            }
            else {
                $scope.noPostForSearch = true;
                $scope.showSearchInputLoader = false;
                var notification = {
                    'message': 'Please enter at least ' + constants.SEARCH_MIN_LEN + ' characters to search.',
                    'color': 'error'
                };
                notify(notification);
            }
        }, 1200);
    };

    $scope.searchTabFlickityOptions = {
        // wrapAround: true,
        cellAlign: 'center',
        freeScroll: true,
        wrapAround: false
    };

    // Angulargrid options.
    $scope.agOptions = {
        gridWidth: 350,
        cssGrid: true,
        pageSize: 2,
        infiniteScrollDistance: 100,
        performantScroll: true,
        infiniteScrollDelay: 3000,
        gutterSize: 0,
        refreshOnImgLoad: true,
    };

    // Initialize data.
    $scope.showFollowBtn = true;
    
    $scope.searchResults = [];
    $scope.showSearchLoader = false;
    $scope.noMorePost = false;
    $scope.noPostForSearch = false;

    $scope.tagFollowStatus = false;

    $scope.card_post_type = 'all';
    $scope.hidePostFilter = false;
    $scope.commonData.allPosts = [];
    $scope.channelUsers = [];
    $scope.searchTags = [];
    $scope.places = [];

    var page = 1;

    $scope.resetData = function() {
        page = 1;
        $scope.busy = false;
        $scope.noMorePost = false;
        $scope.hidePostFilter = false;
        $scope.card_post_type = 'all';

        $scope.searchResults = [];
        $scope.commonData.allPosts = [];
        $scope.channelUsers = [];
        $scope.searchTags = [];
        $scope.places = [];
    };

    // Set post type.
    $scope.setPostType = function(card_post_type) {
        $scope.card_post_type = card_post_type;
    };

    $scope.liveSearch = function() {
        $scope.resetData();
        $scope.fetchPostData($scope.currentTab);
    };

    var isFirstLoadMore = true;
    // Fetch the search results.
    $scope.fetchPostData = function(initiator) {
        // Return if initiator is not current tab.
        if (initiator != $scope.currentTab) {
            return;
        }
        // Minimum character needed for search.
        if (!$scope.search.query || $scope.search.query.length < constants.SEARCH_MIN_LEN) {
            $scope.noPostForSearch = true;
            return;
        }
        if ($scope.busy ) {
            isFirstLoadMore = false;
            return;
        }
        $scope.busy = true;
        // console.log('loading more data.. ' + initiator);
        // Do only for 1st page.
        if (page == 1) {
            // show loader.
            $scope.showSearchLoader = true;
        }
        // Fetch data from server.
        var payload = {
            q: $scope.search.query,
            orginal_query:$scope.search.query,
            //q: $scope.search.query.replace(/\s|\s+/g, '-').replace(/-{2,}/, '-').replace(/^\s+|\s+$/g,"").replace(/[^\w\s\-\,]/gi, ''),

            card_post_type: $scope.card_post_type,
            page: page
        };
        $http.post('/api/search', payload)
            .then(function(response) {
                if (response.data.results) {

                    $location.search('q', payload.q);
                    // Change the page title.
                    var title = encodeURIComponent(payload.q).replace(/%20/g, '+');
                    title +=  " | SWOLK SEARCH";
                    $scope.meta.title = title;
                    
                    $scope.searchResults = response.data.results;

                    // Tag following status.
                    $scope.tagFollowStatus = $scope.searchResults.tagFollowStatus == 1;

                    // For posts tab.
                    if ($scope.searchResults.posts.length < 1) {
                        if (page === 1) {
                            $scope.noPostForSearch = true;
                        }
                        else {
                            $scope.noMorePost = true;
                        }
                    }
                    else {
                        // start positioning cards.
                        // $scope.startPositionCards();
                        // start reloading masonry.
                        //$scope.startReloadCardMasonry();

                       /* var allPostsTmp = angular.copy($scope.commonData.allPosts);
                        allPostsTmp = allPostsTmp.concat($scope.searchResults.posts);
                        $scope.commonData.allPosts = allPostsTmp;*/

                        $scope.searchResults.posts.forEach(function(post) {
                            $scope.commonData.allPosts.push(post);
                        });
                    }
                    // For channel tab.
                    if ($scope.searchResults.channelUsers.length < 1) {
                        if (page === 1) {
                            $scope.noChannelForSearch = true;
                        }
                        else {
                            $scope.noMoreChannel = true;
                        }
                    }
                    else {
                        $scope.searchResults.channelUsers.forEach(function(user) {
                            $scope.channelUsers.push(user);
                        });
                    }
                    // For tag tab.
                    if ($scope.searchResults.searchTags.length < 1) {
                        if (page === 1) {
                            $scope.noTagForSearch = true;
                        }
                        else {
                            $scope.noMoreTagSearch = true;
                        }
                    }
                    else {
                        $scope.searchResults.searchTags.forEach(function(user) {
                            $scope.searchTags.push(user);
                        });
                    }
                    // For location tab.
                    if ($scope.searchResults.places.length < 1) {
                        if (page === 1) {
                            $scope.noLocationForSearch = true;
                        }
                        else {
                            $scope.noMoreLocationSearch = true;
                        }
                    }
                    else {
                        $scope.searchResults.places.forEach(function(place) {
                            $scope.places.push(place);
                        });
                    }
                }
                
                if (page == 1) {
                    setTimeout(function() {
                        $scope.showSearchLoader = false;
                    }, 1000);
                }
                page++;
                $scope.busy = false;
            },
            function(response) {
                $scope.busy = false;
                page++;
                $scope.showSearchLoader = false;
            }
        );
    }

    // Follow or unfollow the tag.
    $scope.tagFollowUnfollow = function() {
        if ($scope.tagFollowBusy) {
            return;
        }
        $scope.tagFollowBusy = true;
        var url = 'api/tag-follow-unfollow';
        var postData = {
            name: $scope.search.query
        };
        $http.post(url, postData)
            .then(function(response) {
                var search_lower;
                if ($scope.search) {
                    search_lower = $scope.search.query.toLowerCase();
                    search_lower = search_lower.regReplaceAll(' ', '-');
                }
                if (response.data.status == 1) {
                    $scope.tagFollowStatus = true;
                    
                    if ($scope.searchTags) {
                        $scope.searchTags.forEach(function(tag){
                            if ($scope.search && tag.tag_name.toLowerCase() == search_lower) {
                                tag.isFollow = 1;
                            }
                        });
                    }
                    $scope.totalFollower += 1;
                }
                else {
                    $scope.tagFollowStatus = false;
                    
                    $scope.searchTags.forEach(function(tag){
                        if (tag.tag_name.toLowerCase() == search_lower) {
                            tag.isFollow = 0;
                        }
                    });
                    if ($scope.totalFollower > 0) {
                        $scope.totalFollower -= 1;
                    }
                }
                // Remove lock.
                $scope.tagFollowBusy = false;
            },
            function (response) {
                $scope.tagFollowBusy = false;
            }
        );
    }

    $scope.tagFollowEachBusy = false;
    // Follow or unfollow indivisual tag from tag tab.
    $scope.tagFollowUnfollowEach = function(tag) {
        if ($scope.tagFollowEachBusy) {
            return;
        }
        $scope.tagFollowEachBusy = true;
        var url = 'api/tag-follow-unfollow';
        var postData = {
            name: tag.tag_name
        };
        $http.post(url, postData)
            .then(function(response) {
                var search_lower = $scope.search.query.toLowerCase();
                search_lower = search_lower.regReplaceAll(' ', '-');
                if (response.data.status == 1) {
                    tag.isFollow = 1;
                    if ($scope.search && tag.tag_name.toLowerCase() == search_lower) {
                        $scope.tagFollowStatus = true;
                    }
                    // $scope.users_count += 1;
                    tag.users_count += 1;
                }
                else {
                    tag.isFollow = 0;
                    if ($scope.search && tag.tag_name.toLowerCase() == search_lower) {
                        $scope.tagFollowStatus = false;
                    }
                    if (tag.users_count > 0) {
                        tag.users_count -= 1;
                    }
                }
                // Remove lock.
                $scope.tagFollowEachBusy = false;
            },
            function (response) {
                $scope.tagFollowEachBusy = false;
            }
        );
    }

    // Follow or unfollow the place.
    $scope.placeFollowUnfollow = function(place) {
        if ($scope.placeFollowBusy) {
            return;
        }
        $scope.placeFollowBusy = true;

        var postData = {
            place_url: place.place_url
        };

        var url = 'api/place-follow-unfollow';
        $http.post(url, postData)
        .then(function(response) {
                if (response.data.status == 1) {
                    place.isFollow = 1;
                    place.users_count += 1;
                }
                else {
                     place.isFollow = 0;
                    if (place.users_count > 0) {
                        place.users_count -= 1;
                    }
                }
                // Remove lock.
                $scope.placeFollowBusy = false;
            },
            function (response) {
                $scope.placeFollowBusy = false;
            }
        );
    };

}]);