
app.controller('GlobalController', function($scope, $state, $http, Ajax, State, Request, Utilities) {
    
    //the entire State service is globally available to all templates
    $scope.State = State;
    $scope.Request = Request;
    $scope.$state = $state;

    
    
    
    $scope.keyArray = Utilities.keyArray;
    
    $scope.addFriend = function($friendId) {
        $participantId = State.activeAccount.participantId;
        $http.post(Ajax.POST_FRIENDSHIP, {'person_id': $participantId, 'friend_id': $friendId }).
            success(function(data, status, headers, config) {
                State.debug = data;
            }).
            error(function(data, status, headers, config) {
                State.debug = data;
            });
    }
    
   
    

   
 



}); 


