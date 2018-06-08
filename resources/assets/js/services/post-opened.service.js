/*
 * @author <tuhin.tsm.mandal@gmail.com>
 */
(function () {
    'use strict';
 
    angular
        .module('app')
        .factory('postOpened', postOpened);

    function postOpened(socket, localStorageService) {
    	var service = {};

        service.init = init;

        service.listenUsersTypingInChannelEvent = listenUsersTypingInChannelEvent;
        
        service.userFollowedEvent = userFollowedEvent;
        service.userViewedEvent = userViewedEvent;
        service.userPointUpdatedEvent = userPointUpdatedEvent;

        service.trackPostOpened = trackPostOpened;

        service.listenPostViewUpdatedEvent = listenPostViewUpdatedEvent;
        service.listenPostPointUpdatedEvent = listenPostPointUpdatedEvent;
        service.listenPostUpvotedEvent = listenPostUpvotedEvent;
        service.listenPostSharedEvent = listenPostSharedEvent;
        service.listenPostBookmarkedEvent = listenPostBookmarkedEvent;

        service.listenCommentPostedEvent = listenCommentPostedEvent;
        service.listenCommentDeletedEvent = listenCommentDeletedEvent;
        service.listenReplyPostedEvent = listenReplyPostedEvent;
        service.listenCommentVotedEvent = listenCommentVotedEvent;
 
        return service;

        function init() {
            // socket.removeAllListeners('users_typing_here');
            
            socket.removeAllListeners('user-followed');
            socket.removeAllListeners('user-viewed');
            socket.removeAllListeners('user-point-updated');

            socket.removeAllListeners('post-view-updated');
            socket.removeAllListeners('post-point-updated');
            socket.removeAllListeners('post-upvoted');
            socket.removeAllListeners('post-shared');
            socket.removeAllListeners('post-bookmarked');

            /*socket.removeAllListeners('comment-posted');
            socket.removeAllListeners('reply-posted');
            socket.removeAllListeners('comment-deleted');
            socket.removeAllListeners('comment-voted');*/
        }

        function trackPostOpened(post_id) {
            var opened_posts = localStorageService.get('opened_posts');
            opened_posts = JSON.parse(opened_posts);
            if (opened_posts) {
                if (typeof opened_posts[post_id] == 'undefined') {
                    opened_posts[post_id] = 1;
                }
                else {
                    opened_posts[post_id] += 1;
                }
            }
            else {
                opened_posts = {};
                opened_posts[post_id] = 1;
            }
            // Check current key.
            var opened_posts2 = localStorageService.get('opened_posts');
            if (opened_posts2) {
                // console.log(opened_posts2);
                opened_posts2 = JSON.parse(opened_posts2);
                if (opened_posts['sk'] == opened_posts2['sk']) {
                    // Save to local storage.
                    opened_posts['sk'] = uuid4();
                    // console.log(opened_posts);
                    localStorageService.set('opened_posts', JSON.stringify(opened_posts));
                }
                else {
                    // Try again.
                    trackPostOpened(post_id);
                }
            }
            else {
                // Save to local storage.
                opened_posts['sk'] = uuid4();
                // console.log(opened_posts);
                localStorageService.set('opened_posts', JSON.stringify(opened_posts));
            }
        }

        function listenUsersTypingInChannelEvent(callback) {
            socket.on('users_typing_here', function(response) {
                callback(response);
            });
        }

        /*--------- For real time data ---------*/

        function userFollowedEvent(callback) {
            socket.on('user-followed', function(response) {
                callback(response);
            });
        }
        
        function userViewedEvent(callback) {
            socket.on('user-viewed', function(response) {
                callback(response);
            });
        }

        function userPointUpdatedEvent(callback) {
            socket.on('user-point-updated', function(response) {
                callback(response);
            });
        }

        function listenPostViewUpdatedEvent(callback) {
            socket.on('post-view-updated', function(response) {
                callback(response);
            });
        }
        
        function listenPostPointUpdatedEvent (callback) {
            socket.on('post-point-updated', function(response) {
                callback(response);
            });
        }

        function listenPostSharedEvent (callback) {
            socket.on('post-shared', function(response) {
                callback(response);
            });
        }

        function listenPostUpvotedEvent (callback) {
            socket.on('post-upvoted', function(response) {
                callback(response);
            });
        }
        
        function listenPostBookmarkedEvent (callback) {
            socket.on('post-bookmarked', function(response) {
                callback(response);
            });
        }

        /*------- For comments ---------*/
        function listenCommentPostedEvent (callback) {
            socket.on('comment-posted', function(response) {
                callback(response);
            });
        }

        function listenReplyPostedEvent (callback) {
            socket.on('reply-posted', function(response) {
                callback(response);
            });
        }

        function listenCommentDeletedEvent (callback) {
            socket.on('comment-deleted', function(response) {
                callback(response);
            });
        }

        function listenCommentVotedEvent (callback) {
            socket.on('comment-voted', function(response) {
                callback(response);
            });
        }
    }
})();