app.controller('FriendsController', function($scope, $state, $http, Ajax, State) {
    
    $scope.friendCollection = [];

    $scope.$watch(function() {return State.activeAccount.participantId}, function() {
            var parameters = {'person_id': State.activeAccount.participantId}
            $http.get(Ajax.GET_COLLECTION_FRIENDSHIP, {params: parameters}).
                    success(function(data, status, headers, config){
                        $scope.friendCollection = data;
                    }).
                    error(function(data, status, headers, config) {
                        State.debug = data;
                    });
    });
    
    
    
    
    
});


