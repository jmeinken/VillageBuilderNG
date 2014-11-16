
app.controller('MapController', function($scope, $interval, State, Request) {
    
    $scope.userProvidedAddress = "";
    $scope.map;
    $scope.marker;
    $scope.geocoder = new google.maps.Geocoder();
    $scope.geocodeResults = [];
    $scope.addressIndex = 0;
    
    //these are used with interval to run lookup until successful
    $scope.promise;
    $scope.addressSubmissionComplete = true;

    $scope.initializeMap = function () {
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
        if (!$scope.addressSubmissionComplete) {
            return;
        }
        $scope.addressSubmissionComplete = false;
        $scope.addressIndex = 0;
        $scope.geocodeResults = [];
        var geocoderRequest = {
            address: $scope.userProvidedAddress
        }
        $scope.promise = $interval(function() {
            $scope.geocoder.geocode(geocoderRequest, function(results, status) {
                if(results == null){
                    checkStatus();
                    return;
                }
                if (results.length > 0) {
                    $scope.geocodeResults = results;
                    $scope.addressSubmissionComplete = true;
                    $scope.map.setCenter(results[0].geometry.location);
                    $scope.map.setZoom(15);
                    $scope.marker = new google.maps.Marker({ map: $scope.map });
                    $scope.marker.setPosition(results[0].geometry.location);
                    checkStatus();
                } else {
                    $scope.geocodeResults = [];
                    $scope.addressSubmissionComplete = true;
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
    
    $scope.useSelectedGecodeResult = function() {
        State.debug = $scope.addressIndex;
        var selected = $scope.geocodeResults[$scope.addressIndex];
        
    }
  




    
    
    

   
 



}); 


