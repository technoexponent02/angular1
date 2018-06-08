/**
 * Created by tuhin on 21/3/17.
 */
(function () {
    'use strict';

    angular
        .module('app')
        .factory('saveToLocalService', saveToLocalService);

    function saveToLocalService($http) {
        // interface
        var service = {
            image: image
        };
        return service;

        // implementation
        function image(link) {
            var data = {link: link};
            return $http.post('/api/saveImageToLocal', data).then(function (response) {
                return response.data;
            },
            function (response) {
                return response.data;
            });
        }
    }
})();