
app.controller('GlobalController', function($scope, $state, $http, Ajax, State, Request, Utilities) {
    
    //the entire State service is globally available to all templates
    $scope.State = State;
    $scope.Request = Request;
    $scope.$state = $state;

    
    
    
    $scope.keyArray = Utilities.keyArray;
    
    $scope.addFriend = function($friendId) {
        $participantId = State.activeParticipant.participantId;
        $http.post(Ajax.POST_FRIENDSHIP, {'participant_id': $participantId, 'friend_id': $friendId }).
            success(function(data, status, headers, config) {
                State.debug = data;
                State.authenticate();
            }).
            error(function(data, status, headers, config) {
                State.debug = data;
            });
    }
    
    $scope.changeActiveParticipant = function(participantId) {
        State.activeParticipantId = participantId;
        State.participantDataChanged++;
        $state.go('main.home');
    }
    
    //this can be more efficient
    $scope.alreadyFriends = function(friendId) {
        friend = false;
        for (var i=0; i<State.activeParticipant.friendCollection.length; i++) {
            if (friendId == State.activeParticipant.friendCollection[i].friend_id) {
                friend = true;
            }
        }
        return friend;
    }
    
    $scope.deleteFriend = function($friendId) {
        $participantId = State.activeParticipant.participantId;
        $http.post(Ajax.DELETE_FRIENDSHIP, {'participant_id': $participantId, 'friend_id': $friendId }).
            success(function(data, status, headers, config) {
                State.debug = data;
                State.authenticate();
            }).
            error(function(data, status, headers, config) {
                State.debug = data;
            });
    }
    
   
    

   
 



}); 


