'use strict';

describe('Geolocation', function() {

    var Geolocation, $httpBackend, $cookies;

    beforeEach(module('chaplean.geolocation', function() {}));

    beforeEach(inject(function($injector, _Geolocation_) {
        $httpBackend = $injector.get('$httpBackend');
        $cookies = $injector.get('$cookies');
        Geolocation = _Geolocation_;
    }));

    it('get longitude latitude', inject(function() {
        $httpBackend.when('GET', '/rest/geolocation/1').respond({longitude: -0.5733138, latitude: 44.8435849});

        var geolocation = {};
        Geolocation.getLongitudeLatitude('1')
            .then(function (response) {
                geolocation = response;
            });
        $httpBackend.flush();

        expect(geolocation.longitude).toEqual(-0.5733138);
        expect(geolocation.latitude).toEqual(44.8435849);
    }));

    it('get longitude latitude', inject(function() {
        $httpBackend.when('GET', '/rest/geolocation/foo').respond(400, {longitude: null, latitude: null});

        var geolocation = {};
        Geolocation.getLongitudeLatitude('foo')
            .then(function (response) {
                geolocation = response;
            });
        $httpBackend.flush();

        expect(geolocation.longitude).toEqual(undefined);
        expect(geolocation.latitude).toEqual(undefined);
    }));

    it('save address', inject(function() {
        $httpBackend.when('POST', '/rest/geolocation', {address: '9 rue de condé, 33000, Bordeaux'}).respond({
            floor: 9,
            block1: 'Rue de Condé',
            cityComplement: 'Bordeaux',
            zipcode: 33000

        });

        var address = {};
        Geolocation.saveGeolocation('9 rue de condé, 33000, Bordeaux')
            .then(function (response) {
                address = response;
            });
        $httpBackend.flush();

        expect(address.floor).toEqual(9);
        expect(address.block1).toEqual('Rue de Condé');
        expect(address.cityComplement).toEqual('Bordeaux');
        expect(address.zipcode).toEqual(33000);
    }));

    it('save address', inject(function() {
        $httpBackend.when('POST', '/rest/geolocation', {address: '9 rue de condé, 33000, Bordeaux'}).respond(400);

        var address = {};
        Geolocation.saveGeolocation('9 rue de condé, 33000, Bordeaux')
            .then(function (response) {
                address = response;
            }, function (error) {
                address = error;
            });
        $httpBackend.flush();

        expect(address).toEqual(null);
    }));

    it('get geolocation', inject(function() {
        $httpBackend.when('GET', '/rest/geolocation').respond({region: 'Aquitaine', department: 'Gironde'});

        var geolocation = {};
        Geolocation.getGeolocation()
            .then(function (response) {
                geolocation = response;
            }, function (error) {
                geolocation = error;
            });
        $httpBackend.flush();

        expect(geolocation).toEqual({region: 'Aquitaine', department: 'Gironde'});
    }));

    it('get geolocation with already cookie', inject(function() {
        $cookies.geolocation = {region: 'Centre', department: 'Cher'};

        var geolocation = {};
        Geolocation.getGeolocation()
            .then(function (response) {
                geolocation = response;
            }, function (error) {
                geolocation = error;
            });

        try {
            $httpBackend.flush();
        } catch (e) {}

        expect(geolocation).toEqual({region: 'Centre', department: 'Cher'});
    }));
});
