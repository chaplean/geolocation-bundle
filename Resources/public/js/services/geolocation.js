'use strict';

var geolocation = angular.module('chaplean.geolocation');

geolocation.factory('Geolocation', function($http, $q) {
    return {
        getLongitudeLatitude: function (address) {
            var deffered = $q.defer();
            $http.get('/rest/geolocation/' + encodeURIComponent(address))
                .success(function (response) {
                    deffered.resolve(response);
                })
                .error(function () {
                    deffered.reject({longitude: null, latitude: null});
                });
            return deffered.promise;
        },
        saveGeolocation: function (address) {
            var deffered = $q.defer();
            $http.post('/rest/geolocation', {address: address})
                .success(function (response) {
                    deffered.resolve(response);
                })
                .error(function () {
                    deffered.reject(null);
                });
            return deffered.promise;
        }
    };
});