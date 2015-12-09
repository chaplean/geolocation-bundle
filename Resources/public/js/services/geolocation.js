'use strict';

var geolocation = angular.module('chaplean.geolocation');

geolocation.factory('Geolocation', function($http, $q, $cookies) {
    var geolocation = {
        getGeolocation: function () {
            var deffered = $q.defer();
            if (!$cookies.geolocation) {
                $http.get('rest/geolocation')
                    .success(function (response) {
                        geolocation.saveGeolocationCookie(response);
                        deffered.resolve(response);
                    })
                    .error(function () {
                        deffered.reject({});
                    });
            } else {
                deffered.resolve(JSON.parse($cookies.geolocation));
            }
            return deffered.promise;
        },
        saveGeolocationCookie: function (region, department) {
            var geolocation;
            if (typeof region == 'object' && region.hasOwnProperty('region') && region.hasOwnProperty('department')) {
                geolocation = region;
            } else {
                geolocation = {region: region, department: department};
            }
            $cookies.geolocation = JSON.stringify(geolocation);
        },
        getLongitudeLatitude: function (address) {
            var deffered = $q.defer();
            $http.get('rest/geolocation/' + encodeURIComponent(address))
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
            $http.post('rest/geolocation', {address: address})
                .success(function (response) {
                    deffered.resolve(response);
                })
                .error(function () {
                    deffered.reject(null);
                });
            return deffered.promise;
        }
    };

    return geolocation;
});