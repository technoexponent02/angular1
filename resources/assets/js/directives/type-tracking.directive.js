angular.module('app').directive('typeTracking', function (TypingIndicator, $timeout, $stateParams) {
    return {

        restrict: 'A',
        //scope: false,

        link: function (scope, element, attrs) {

            element.bind('keyup', function (event) {
                if (event.keyCode == 13 && event.shiftKey) {
                    event.stopPropagation();
                }
                else if (event.keyCode == 13) {
                    var $btn = element.parent('div').children('button');

                    if (!$btn.is(":disabled")) {
                        element.parent('div').children('button').trigger('click');
                    }
                    else {
                        $(this).val('');
                        scope.$digest();
                        var c = this.selectionStart;
                        c--;
                        this.setSelectionRange(c, c);
                    }
                }

                element.parent('div').children('button').click(function () {
                    scope.message = '';
                    if (scope.isCurrentlyTyping) {
                        // console.log('Flush the scheduler and stop typing');
                        // Stop typing immediatly
                        scope.stopTypingScheduler.flush();
                        scope.isCurrentlyTyping = false;
                    }
                });

            });

            scope.$watch(attrs['ngModel'], function (input) {

                // When to start Typing ?
                // Content is not empty and was not typing before
                if (!_.isEmpty(input) && !scope.isCurrentlyTyping) {
                    // console.log('startTyping()');
                    TypingIndicator.startTyping(scope.conversationChannel, scope.channelUser);
                    scope.isCurrentlyTyping = true;
                    scope.stopTypingScheduler();
                    // console.log('SCHEDULE stopTypingScheduler() in 5 seconds');
                }
                // When to reschedule ?
                // when the input is not empty and you are typing
                else if (!_.isEmpty(input) && scope.isCurrentlyTyping) {
                    // console.log('RE-SCHEDULE call to stopTypingScheduler() in 5 seconds');
                    scope.stopTypingScheduler();
                    scope.isCurrentlyTyping = true;
                }
                // When to stop typing ?
                // You erase the input : You were typing and the input is now empty
                else if (scope.isCurrentlyTyping && _.isEmpty(input)) {
                    // console.log('Flush the scheduler and stop typing');
                    // Stop typing immediatly
                    scope.stopTypingScheduler.flush();
                    scope.isCurrentlyTyping = false;
                }
            });
        },

        controller: function ($scope) {

            $scope.conversationChannel = $scope.post.id;
            $scope.channelUser = {
                id: $scope.user.id,
                name: $scope.user.first_name + ' ' + $scope.user.last_name
            };
            // Time before the stop typing event is fired after stopping to type.
            $scope.stopTypingTime = 5000

            // Keep track of the last action
            // This boolean is useful in order to know if we should send a stopTyping event in case the user was previously typing.
            $scope.isCurrentlyTyping = false;

            // Scheduler that trigger stopTyping if the function has not been invoced after stopTypingTime
            $scope.stopTypingScheduler = _.debounce(function () {
                TypingIndicator.stopTyping($scope.conversationChannel, $scope.channelUser);
                $scope.isCurrentlyTyping = false;
            }, $scope.stopTypingTime)

        }
    };
});