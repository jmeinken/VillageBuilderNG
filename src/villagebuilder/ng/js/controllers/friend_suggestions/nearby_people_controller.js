app.controller('NearbyPeopleController', function($scope, $http, Ajax, State) {

    $scope.memberCollection = [];

    $scope.$watch(function() {return State.activeParticipant.participantId}, function() {
        if (State.activeParticipant.participantId) {
            var parameters = {'participant_id': State.activeParticipant.participantId}
            $http.get(Ajax.GET_COLLECTION_NEARBY_PEOPLE, {params: parameters}).
                    success(function(data, status, headers, config){
                        $scope.memberCollection = data;
                    }).
                    error(function(data, status, headers, config) {
                        State.debug = "it was this";
                    });
        }
    });
   
 



}); 


