
app.controller('MapController', function($scope, $interval, $location, $state, State, Request) {
    
    $scope.userProvidedAddress = "test";
    $scope.map;
    $scope.marker;
    $scope.geocoder = new google.maps.Geocoder();
    $scope.geocodeResults = [];
    $scope.addressIndex = 0;
    $scope.resultExists = false;
    
    //these are used with interval to run lookup until successful
    $scope.promise;
    $scope.addressSubmissionComplete = true;

    $scope.initializeMap = function () {
        initializeMap();
        //this code makes the map show correctly inside Bootstrap modal
        //$("#map-modal").on("shown.bs.modal", function () {
        //    google.maps.event.trigger($scope.map, "resize");
        //});
    }
    
    $scope.resetChangedAddress = function() {
        State.changedAddress = false;
    }
    
    function initializeMap() {
        var mapOptions = {
          center: { lat: 39.83, lng: -98.58},
          zoom: 4
        };
        $scope.map = new google.maps.Map(document.getElementById('map-canvas'),
            mapOptions);
    }
    
    //For some reason, the results function from geocode can run operations
    //on the map but can't interact with any environment variables the first
    //run.  I use $interval combined with a checkStatus() function
    //to run every second until geocodeResults is set.
    $scope.lookupAddress = function() {
        State.changedAddress = false;
        initializeMap();
        if (!$scope.addressSubmissionComplete) {
            return;
        }
        $scope.addressSubmissionComplete = false;
        $scope.addressIndex = 0;
        $scope.geocodeResults = [];
        var geocoderRequest = {
            address: $scope.userProvidedAddress
        }
        State.debug = $scope.userProvidedAddress;
        $scope.promise = $interval(function() {
            $scope.geocoder.geocode(geocoderRequest, function(results, status) {
                if(results == null){
                    checkStatus();
                    return;
                }
                if (results.length > 0) {
                    $scope.geocodeResults = results.slice(0,10);
                    $scope.addressSubmissionComplete = true;
                    $scope.map.setCenter(results[0].geometry.location);
                    $scope.map.setZoom(15);
                    $scope.marker = new google.maps.Marker({ map: $scope.map });
                    $scope.marker.setPosition(results[0].geometry.location);
                    $scope.resultExists = true;
                    checkStatus();
                } else {
                    $scope.geocodeResults = [];
                    $scope.addressSubmissionComplete = true;
                    $scope.resultExists = false;
                    alert('No results found for that address.');
                    checkStatus();
                }
            });
        }, 1100);
    }
    
    function checkStatus() {
        State.debug += $scope.addressSubmissionComplete;
        if ($scope.addressSubmissionComplete) {
            $interval.cancel($scope.promise);
        }
    }
    
    $scope.redrawMap = function(value) {
        $scope.map.setCenter($scope.geocodeResults[value].geometry.location);
        $scope.map.setZoom(15);
        $scope.marker.setMap(null);
        $scope.marker = new google.maps.Marker({ map: $scope.map });
        $scope.marker.setPosition($scope.geocodeResults[value].geometry.location);
    }
    
    //selected address accessed as:
    //$scope.geocodeResults[$scope.addressIndex]
    
    $scope.useSelectedGecodeResult = function(requestObject) {
        State.changedAddress = false;
        var selected = $scope.geocodeResults[$scope.addressIndex];
        requestObject.request.full_address = selected.formatted_address;
        requestObject.request.latitude = selected.geometry.location.k;
        requestObject.request.longitude = selected.geometry.location.B;
        requestObject.request.street = "";
        requestObject.request.neighborhood = "";
        requestObject.request.city = "";
        requestObject.request.state = "";
        requestObject.request.zip_code = "";
        for (var i=0; i<selected.address_components.length; i++) {
            if ($.inArray("route", selected.address_components[i].types) != -1) {
                State.debug = selected.address_components[i].short_name;
                requestObject.request.street = selected.address_components[i].short_name;
            } 
            if ($.inArray("neighborhood", selected.address_components[i].types) != -1) {
                requestObject.request.neighborhood = selected.address_components[i].short_name;
            }
            if ($.inArray("locality", selected.address_components[i].types) != -1) {
                requestObject.request.city = selected.address_components[i].short_name;
            }
            if ($.inArray("administrative_area_level_1", selected.address_components[i].types) != -1) {
                requestObject.request.state = selected.address_components[i].short_name;
            }
            if ($.inArray("postal_code", selected.address_components[i].types) != -1) {
                requestObject.request.zip_code = selected.address_components[i].short_name;
            }
        }
        State.debug = "yes it ran";
        State.changedAddress = true;
        
    }
  




    
    
    

   
 



}); 


