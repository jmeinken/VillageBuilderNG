app.controller('NearbyPeopleController', function($scope, $http, Ajax, State) {

    $scope.nearbyPeopleCollection = [];

    $scope.$watch(function() {return State.participantDataChanged}, function() {
        if (State.activeParticipant.participant_id) {
            var parameters = {'participant_id': State.activeParticipant.participant_id}
            $http.get(Ajax.GET_COLLECTION_NEARBY_PEOPLE, {params: parameters}).
                    success(function(data, status, headers, config){
                        $scope.nearbyPeopleCollection = data;
                        //State.debug = data;
                    }).
                    error(function(data, status, headers, config) {
                        State.debug = "nearby people query failed";
                    });
        }
    });
   
 



}); 


