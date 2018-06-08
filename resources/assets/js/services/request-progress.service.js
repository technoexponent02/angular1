/*
 * @author <tuhin.tsm.mandal@gmail.com>
 */
(function () {
    'use strict';
 
    angular
        .module('app')
        .factory('requestProgress', requestProgress);

    function requestProgress(socket) {
        return {
            init: init,
            subscribe: subscribe,
            progress: progress

        };

        function init() {
            // socket.removeAllListeners('subscribe_script_progress');
        }

        function subscribe() {
            var data = {
                uuid: _uuid4
            };
            socket.emit('subscribe_script_progress', data);
        }

        function progress(callback) {
            socket.on('script-progressed', function(response) {
                callback(response);
            });
        }
    }
})();