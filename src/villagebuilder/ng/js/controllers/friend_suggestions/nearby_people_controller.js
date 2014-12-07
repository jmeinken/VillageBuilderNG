app.controller('NearbyPeopleController', function($scope, $http, Ajax, State) {

    $scope.memberCollection = [];

    $scope.loadCollection = function() {
        //State.debug = "teset"
        var parameters = {'person_id': State.activeAccount.participantId}
        $http.get(Ajax.GET_COLLECTION_NEARBY_PEOPLE, {params: parameters}).
                success(function(data, status, headers, config){
                    $scope.memberCollection = data;
                }).
                error(function(data, status, headers, config) {
                    State.debug = data;
                });
    }
   
 



}); 


