/*
 * Code for comment box.
 */
(function () {
    'use strict';

    angular.module('app')
        .directive('commentBox', function () {
            return {
                restrict: 'E',
                templateUrl: 'tpl.comment-box',
                replace: true,
                controller: 'CommentBoxCtrl',
                // controllerAs: 'ctrl'
            };
        })
        .controller('CommentBoxCtrl', CommentBoxCtrl);

    function CommentBoxCtrl($scope, $http, $rootScope, $timeout, inArray, $state, postOpened, notify) {
        // Initialize comments array.
        $scope.comments = [];

        $scope.typingText = '';
        $scope.opened_nodes = [];

        $scope.postComment = function (post_id, child_post_id) {
            var $parentMsg = $("#parentMsg");
            var msg = $parentMsg.val();
            var message = msg.replace(/(?:\r\n|\r|\n)/g, '<br />');

            if (message == '') {
                $parentMsg.attr('required', true);
                alert('Please write something!');
                return false;
            }

            /*if ($scope.activeItem != 2) {
                setTimeout(function () {
                    //  for new comments
                    $scope.sortByComments(2, post_id);
                }, 500);
            }*/
        
            $scope.showSendBtn = false;
            //For Loading effect.
            $scope.showSendingBtn = true;
            
            var postData = {
                parent_id: 0,
                message: message,
                post_id: post_id,
                child_post_id: child_post_id
            };
            $http.post('comments/post-comment', postData)
                .then(function (response) {
                    $("#parentMsg").val('');
                    // for reset message box
                    $scope.message = '';
                    $scope.showSendBtn = true;
                    // Remove loading effect .
                    $scope.showSendingBtn = false;
                    // Load comments.
                    if ($scope.activeItem == 1) {
                        $scope.sortByComments(2, post_id);
                    }                    
                }
            );
        };

        // reply post comments.
        var cnt = 1;
        $scope.postReplyComment = function (id) {
            var elm = angular.element("#commentfrm" + id);

            var $textarea = elm.find("textarea[name='message']");

            var msg = $textarea.val();
            var message = msg.replace(/(?:\r\n|\r|\n)/g, '<br />');

            if (message == '') {
                $textarea.attr('required', true);
                alert('Please write something!');
                return false;
            }

            var parent_id = elm.find("input[name='parent_id']").val();
            var child_post_id = elm.find("input[name='child_post_id']").val();
            var post_id = elm.find("input[name='post_id']").val();

            $("#send" + id).hide();
            $("#sending" + id).show();
            $("#sendBtnLoading" + id).prop('disabled', true);

            var postData = {
                message: message,
                parent_id: parent_id,
                child_post_id: child_post_id,
                post_id: post_id
            };
            $http.post('comments/post-child-comment', postData)
                .then(function (response) {
                    $("#sendBtnLoading" + id).prop('disabled', false);
                    $("#send" + id).show();
                    $("#sending" + id).hide();
                }
            );
            elm.find("textarea[name='message']").val('');
            elm.find("textarea[class='modalMsg']").val('');
            $scope.displayBlock = 0;
        };

        $scope.deleteComments = function (comment_id, parent_id, postId) {            
            var postData = {
                comment_id: comment_id,
                parent_id: parent_id,
                postId: postId
            };
            $http.post('comments/delete', postData)
                .then(function (response) {
                    $('#commentDeleteModal').modal('toggle');
                },
                function (error) {
                    var notification = {
                        'message': 'Sorry! unable to process your request.',
                        'color': 'error',
                        'timeout': 5000
                    };
                    notify(notification);
                }
            );
        };

        $scope.moreComments = function (post_id, sortType) {
            // var totalCommentsOnView = $scope.comments.length < 10 ? $scope.comments.length : 10;
            var totalCommentsOnView = $scope.comments.length;
            var offsetx = totalCommentsOnView;

            $http({
                method: "POST",
                url: "angular/showLoadMoreComments",
                params: {
                    post_id: post_id,
                    offsetx: offsetx,
                    sortType: sortType,
                    showAll: 'showAll'
                }
            }).then(function (response) {
                var getComments = response.data.allComments;
                var existingComments = [];
                if ($scope.comments && $scope.comments.length) {
                    $scope.comments.forEach(function(c){
                        existingComments.push(c.id);
                    })
                }
                if (angular.isArray(getComments)) {
                    angular.forEach(getComments, function (comment) {
                        if (existingComments.indexOf(comment.id) === -1) {
                            comment.message = emojione.toImage(comment.message);
                            $scope.comments.push(comment);
                        }                        
                    });
                }
                $scope.commentLimitTo = response.data.postParentComment;
                $scope.postTotalComment = response.data.postTotalComment;
            });
        };

        $scope.sortComments = '-created_at';
        $scope.displayBlock = 0;
        $scope.showdiv = 0;

        $scope.showReplyBox = function (commentId, postId, activeItem) {

            var dataArray = $scope.comments;			
            // Close the reply box.
			if($("#cmmtextAreaBlock"+commentId).is(":visible")) {
				
				var allFullComments = $scope.spliceNodeAtpos(commentId,$scope.fullComments);
                $scope.comments = allFullComments;
				$("#cmmtextAreaBlock"+commentId).css("display","none");
			}
            // Open the reply box.
            else {
				$http({
                    method: 'POST',
                    url: "angular/loadChildComments",
                    data: {
                        commentId: commentId, 
                        post_id: postId,
                        activeItem : activeItem
                    }
                })
                .then(function (response) {
                    var all_comments = response.data.allComments;
                    var allFullComments = addReplies(commentId, $scope.fullComments, all_comments);
                    $scope.comments = allFullComments;
                    
                }, function (error) {
                    console.log('Error has occurred');
                });
				
				$(".cmmtextAreaType").css("display","none");
				$("#cmmtextAreaBlock"+commentId).css("display","block");
			}
		   
            $scope.commentBoxId = commentId;
           // $scope.displayBlock = !$scope.displayBlock;
        };

        // Recusrively find insertion postion and push data into array.
         function addReplies(nodeId, allCommentObj, objToBePushed) {
            
            angular.forEach(allCommentObj, function(value,index){

                if(value.id == nodeId) {
                    var existingComment = [];
                    if(value.child_comment.length > 0) {
                        value.child_comment.forEach(function(v) {
                            existingComment.push(v.id);
                        })
                    }
                    if (objToBePushed.length > 0) {
                        // Push the reply to child comment.
                        angular.forEach(objToBePushed, function(v, k) {
                            if (existingComment.indexOf(v.id) === -1) {
                                value.child_comment.push(v);
                                value.showRel = true;
                            }
                        });
                    }
                    else {
                        value.showRel = true;
                    }
                }
                else {
                    if(value.child_comment.length > 0) {
                        addReplies(nodeId, value.child_comment, objToBePushed);
                    }
                }                
            });            
            return allCommentObj; 
        }
        
         // Recusrively find child node postion and child node replace with empty data.
        $scope.spliceNodeAtpos = function (nodeId, allCommentObj) {
            
            angular.forEach(allCommentObj, function(value,index) {
                if(value.id == nodeId){                           
                    value.child_comment = [];
                    value.showRel = false;
                }
                else {
                    if(value.child_comment.length > 0){
                        $scope.spliceNodeAtpos(nodeId, value.child_comment);
                    }
                }                
            });            
            return allCommentObj; 
        }

        // Comments load more
        $scope.sortByComments = function (sortType, postId) {
           // $scope.comments = {};
            $scope.commentLimitTo = $scope.loadMoreCommentsLimit;
            $scope.showCommentLoader = 1;
            $scope.fullComments = [];
            var sendData=[];

            switch (sortType) {
                case 1 :
                    $scope.sortComments = "-total_upvotes";

                    $http({
                        method: 'POST',
                        url: 'angular/showLoadMoreComments',
                        params: {sortType: sortType, post_id: postId, offsetx: 0}
                    }).then(function (response) {

                        // for emoji
                        var sendData=[];
                        var responseData=response.data.allComments;
                        if(angular.isArray(responseData)){
                            angular.forEach(responseData,function(val,key)
                            {
                                responseData[key].message = emojione.toImage(responseData[key].message);
                                sendData.push(responseData[key]);
                                $scope.fullComments.push(responseData[key]);
                            });
                        }
                        $scope.comments = sendData;
                        
                        $scope.fullComments=sendData;  // store data temp array 

                        $scope.post.postParentComment = response.data.postParentComment;
                        $scope.postTotalComment = response.data.postTotalComment;

                        $timeout(function () {
                            $(".modal-split-view .split-list .item").removeClass('highLight');
                        }, 100);
                        $timeout(function () {
                            $scope.showCommentLoader = 0;
                        }, 1600);

                    },
                    function (error) {
                        console.log('Error has occurred');
                    });
                $scope.activeItem = 1;
                break;
                case 2 :
                    $scope.sortComments = "-created_at";


                    $http({
                        method: 'POST',
                        url: 'angular/showLoadMoreComments',
                        params: {sortType: sortType, post_id: postId, offsetx: 0}
                    }).then(function (response) {
                        // for emoji
                        var responseData=response.data.allComments;
                        if(angular.isArray(responseData)){
                            angular.forEach(responseData,function(val,key)
                            {
                                responseData[key].message = emojione.toImage(responseData[key].message);
                                sendData.push(responseData[key]);
                                $scope.fullComments.push(responseData[key]);
                            });
                        }
                    
                        $scope.comments = sendData;
                        $scope.fullComments = sendData; // store data temp array 
                        
                        $scope.post.postParentComment = response.data.postParentComment;
                        $scope.postTotalComment = response.data.postTotalComment;

                        setTimeout(function () {
                            $(".modal-split-view .split-list .item").removeClass('highLight');
                        }, 200);

                        /*$timeout(function () {
                            $scope.showCommentLoader = 0;
                        }, 1600);*/
                        $scope.activeItem = 2;
                        $scope.showCommentLoader = 0;
                    },
                    function (error) {
                        $scope.activeItem = 2;
                        $scope.showCommentLoader = 0;
                        // console.log('Error has occurred');
                    }
                );
                // $scope.activeItem = 2;
                break;
            }
        };

        $scope.showCommentDeleteModal = function (comment_id, post_id, activeItem) {

            $rootScope.commentID = comment_id;
            $rootScope.postID = post_id;
            $rootScope.activeItem = activeItem;
        };

        $scope.cancel = function () {
            $('#commentDeleteModal').modal('toggle');
        };

        $scope.commentUpAndDownVote = function (commentID, activityType, post_id) {
            var postData = {
                commentID: commentID,
                activityType: activityType,
                post_id: post_id
            };
            $http.post('comments/vote', postData)
            .then(function (response) {
                // Nothing for now.
            });
        };

        /*------- Code for socket --------*/
        if ($scope.loggedIn) {
            // Listen for usersTypingInChannel event
            postOpened.listenUsersTypingInChannelEvent(function(response) {                
                // console.log(response);
                $scope.typingText = makeUserTypingText(response);
            });
            // Listen for comment post event.
            postOpened.listenCommentPostedEvent(function(response) {                
                // console.log(response);
                CommentPosted(response);
            });
            // Listen for reply posted event.
            postOpened.listenReplyPostedEvent(function(response) {
                // console.log(response);
                ReplyPosted(response);
            });
            // Listen for comment deleted event.
            postOpened.listenCommentDeletedEvent(function(response) {
                // console.log(response);
                CommentDeleted(response);
            });
            // Listen for comment voted event.
            postOpened.listenCommentVotedEvent(function(response) {
                // console.log(response);
                CommentVoted(response);
            });
        }
        /*-------------- Operations -----------------*/
        function makeUserTypingText(response) {
            var typingText = '';
            if (!$scope.loggedIn) {
                return typingText;
            }
            var users = response.users;
            if (users) {
                // Remove current user.
                users.some(function (el, index) {
                    if (el.id == $scope.user.id) {
                        users.splice(index, 1);
                    }
                });
            }
            var userLength = users.length;
            if (userLength > 0) {
                if (userLength == 1) {
                    typingText = response.users[0].name + ' is typing...';
                }
                else if (userLength == 2) {
                    typingText = response.users[0].name + ' and ' + response.users[1].name + ' are typing...';
                }
                else {
                    typingText = response.users[0].name + ' and ' + (userLength - 1) + ' others are typing...';
                }
            }
            return typingText;
        }

        function getRootCommentID(comments, comment_id) {
            var root_comment_id;
            if (comments) {
                comments.every(function(comment) {
                    if (comment.id == comment_id) {
                        root_comment_id = comment.id;
                        return false;
                    }
                    else if(comment.child_comment.length > 0) {
                        // returns id or undefined
                        var childId = getRootCommentID(comment.child_comment, comment_id);
                        if (childId) {
                            root_comment_id = comment.id;
                            return false;
                        }
                    }
                    return true;
                });
            }
            return root_comment_id;
        }

        function CommentPosted(response) {
            // Add last reply to comments array.
            if ($scope.activeItem == 2) {
                var comment = response.data.lastComment;
                var existingComment = [];
                if ($scope.comments.length > 0) {
                    $scope.comments.forEach(function(v) {
                        existingComment.push(v.id);
                    });
                }
                if (existingComment.indexOf(comment.id) === -1) {
                    comment.message = emojione.toImage(comment.message);
                    $scope.comments.unshift(comment);
                }                
            }

            // For postcard number of comments updates
            if (angular.isArray($scope.commonData.allPosts)) {
                angular.forEach($scope.commonData.allPosts, function (value, key) {
                    if (value.id == response.data.lastComment.post_id) {
                        value.totalComments = response.data.postParentComment;
                        return false;
                    }
                });
            }

            $scope.postTotalComment = response.data.postTotalComment;
            if ($scope.post) {
                $scope.post.postParentComment = response.data.postParentComment;
            }
            else {
                $scope.post = {};
                $scope.post.postParentComment = response.data.postParentComment;
            }
        }

        function ReplyPosted(response) {
            var lastComment = response.data.lastComment;
            var isCommenter = $scope.user.id == lastComment.user_id;
            // Add last reply to comments array.
            if ($scope.activeItem == 2 || isCommenter) {                
                lastComment.message = emojione.toImage(lastComment.message);
                var isNotified = pushReplyComment($scope.comments, lastComment, isCommenter);
                // Highlight root comment if not highlighted already
                // in case not parent node is not available.
                // console.log('isNotified: ' + isNotified + ' isCommenter: ' + isCommenter);
                if (!isNotified && !isCommenter) {
                    // Add class for background.
                    if ($scope.comments) {
                        var rootNode = $('#pc' + lastComment.root_comment_id);
                        addAndRemoveBackground(rootNode, 5000);
                    }
                }

            }               

            // For postcard update all posts array.
            if (angular.isArray($scope.commonData.allPosts)) {
                angular.forEach($scope.commonData.allPosts, function (value, key) {
                    if (value.id == response.data.lastComment.post_id) {
                        $scope.commonData.allPosts[key].totalComments = response.data.postParentComment;
                        return false;
                    }
                });
            }

            $scope.postTotalComment = response.data.postTotalComment;
            if ($scope.post) {
                $scope.post.postParentComment = response.data.postParentComment;
            }
            else {
                $scope.post = {};
                $scope.post.postParentComment = response.data.postParentComment;
            }
        }

        function CommentDeleted(response) {
            var comment_id = response.data.comment_id,
                    parent_id = response.data.parent_id,
                    post_id = response.post_id,
                    user_id = response.data.user_id;
            // Check if commenter.
            var isCommenter = $scope.user.id == user_id;
            if ($scope.activeItem == 2 || isCommenter) {
                // Remove comment from array.
                // console.log($scope.comments);
                removeComment($scope.comments, comment_id);
                // update no of child comment to $scope.
                countChildComment($scope.comments, comment_id, parent_id);
            }

            // Update all posts array.
            if (angular.isArray($scope.commonData.allPosts)) {
                angular.forEach($scope.commonData.allPosts, function (value, key) {
                    if (value.id == post_id) {
                        $scope.commonData.allPosts[key].totalComments = response.data.postParentComment;
                        return false;
                    }
                });
            }
            $rootScope.popUpTotalComments = response.data.postTotalComment;
            if ($scope.post) {
                $scope.post.postParentComment = response.data.postParentComment;
            }
            else {
                $scope.post = {};
                $scope.post.postParentComment = response.data.postParentComment;
            }
        }

        function CommentVoted(response) {
            var activityType = response.data.activityType,
                comment = response.data.comment,
                user_id = response.data.user_id;
            // Check if commenter.
            var isCommenter = $scope.user.id == user_id;
            if ($scope.activeItem == 2 || isCommenter) {
                // add vote to comments array.
                voteComment($scope.comments, comment, user_id, activityType);
            }

            /*var commentsArray = $scope.comments;
            if (angular.isArray(commentsArray)) {
                angular.forEach(commentsArray, function (value, key) {
                    if (value.id == comment.id) {
                        $scope.comments[key].total_upvotes = parseInt(comment.upvotes)
                                                            - parseInt(comment.downvotes);
                    }
                });
            }*/
            // Update user total comments on profile tab
            if ($state.current.name == 'profile' || $state.current.name == 'account') {
                if ($scope.userData && $scope.userData.id == comment.user_id) {
                    $scope.userData.points = response.data.post_user_points;
                }
            }

            if (angular.isArray($scope.post.getUser)) {
                angular.forEach($scope.post.getUser, function (value, key) {
                    if (value.id == comment.user.id) {
                        $scope.post.getUser[key].points = comment.user.points;
                    }
                });
            }
        }

        /*------------ functions for comment array ----------------*/
        // Remove comment from comments array.
        function removeComment(comments, comment_id) {
            angular.forEach(comments, function(value, index){
                if(value.id == comment_id) {                           
                    comments.splice(index, 1);                                                       
                }
                else {
                    if(value.child_comment.length > 0){
                        removeComment(value.child_comment, comment_id);
                    }
                }  
            });
            return comments;
        }

        // Update no of child comment to $scope.
        function countChildComment(comments, comments_id, parent_id) {
            angular.forEach(comments, function(value, index){
                if(value.id == parent_id) {
                    value.count_child = value.child_comment.length;                                            
                }
                else {
                    if(value.child_comment.length > 0){
                        countChildComment(value.child_comment, comments_id, parent_id);
                    }
                }                
            });
            return comments;
        }

        // Add reply to comments array.
        function pushReplyComment(comments, lastComment, isCommenter) {
            var isNotified = false;
            angular.forEach(comments, function (comment, index) {
                if(comment.id == lastComment.parent_id) {
                    // Check whether to show in real time.
                    if (comment.showRel || isCommenter) {
                        var existingComment = [];
                        if(comment.child_comment.length > 0) {
                            comment.child_comment.forEach(function(v) {
                                existingComment.push(v.id);
                            })
                        }
                        // Push the reply to child comment.
                        if (existingComment.indexOf(lastComment.id) === -1) {
                            comment.child_comment.unshift(lastComment);
                            // Highlight new comment if parent node expanded.
                            if (!isCommenter) {
                                isNotified = true;
                                setTimeout(function() {
                                    // Add class for background
                                    var commentNode = $('#pc' + lastComment.id);
                                    addAndRemoveBackground(commentNode, 5000);
                                }, 100);
                            }
                        }
                    }
                    else {
                        isNotified = true;
                        // Add class for background
                        var parentNode = $('#pc' + lastComment.parent_id);
                        addAndRemoveBackground(parentNode, 5000);
                    }
                    comment.count_child  =  lastComment.parentCommentTotalchildPost;
                }
                else {
                    if(comment.child_comment.length > 0) {
                        isNotified = pushReplyComment(comment.child_comment, lastComment, isCommenter);
                    }
                }
            });
            return isNotified;
        }
        // Add upvote/downvote to comments array.
        function voteComment(comments, comment, comment_user_id, activityType) {
            var comment_id = comment.id;
            angular.forEach(comments, function(value,index) {
                if(value.id == comment_id) {
                    var isCommenter = $scope.user.id == comment_user_id;
                    if (activityType == 1) {
                        // For cancel comment upvotes ....
                        if(value.isUpvote == 'Y') { 
                            if (isCommenter) {
                                value.isUpvote = 'N';
                            }                            
                            // value.upvotes = parseInt(value.upvotes) - 1;
                        }
                        else { 
                            if (isCommenter) {
                                value.isUpvote = 'Y';
                            }
                            // value.upvotes = parseInt(value.upvotes)+1
                            if(value.isDownvote == 'Y') {
                                if (isCommenter) {
                                    value.isUpvote = 'N';
                                }
                                // value.downvotes = parseInt(value.downvotes)-1;
                            }
                        }

                    } else {
                       // For cancel comment downvotes .... 
                        if(value.isDownvote == 'Y') {
                            if (isCommenter) {
                                value.isUpvote = 'N';
                            }
                            // value.downvotes = parseInt(value.downvotes) - 1;
                        }
                        else {
                            if (isCommenter) {
                                value.isUpvote = 'Y';
                            }
                            // value.downvotes = parseInt(value.downvotes) + 1;                            
                            if(value.isUpvote == 'Y') {
                                if (isCommenter) {
                                    value.isUpvote = 'N';
                                }
                                // value.upvotes = parseInt(value.upvotes) - 1;
                            }
                        }
                    }
                    value.upvotes = comment.upvotes;
                    value.downvotes = comment.downvotes;
                    return '';
                }
                else {
                    if(value.child_comment.length > 0){
                        voteComment(value.child_comment, comment, comment_user_id, activityType);
                    }
                }                
            });
        }

        // Add class for background and remove after specified time.
        function addAndRemoveBackground(selector, time) {
            selector.addClass('sl');
            setTimeout(function(){
                selector.removeClass('sl');
            }, time);
        }
    }
})();
