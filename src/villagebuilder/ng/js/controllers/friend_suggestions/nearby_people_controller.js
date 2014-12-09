app.controller('NearbyPeopleController', function($scope, $http, Ajax, State) {

    $scope.memberCollection = [];

    $scope.$watch(function() {return State.activeAccount.participantId}, function() {
        if (State.activeAccount.participantId) {
            var parameters = {'person_id': State.activeAccount.participantId}
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

