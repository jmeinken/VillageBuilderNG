
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
    $scope.joinGroup = function($groupId) {
        $participantId = State.activeParticipant.participantId;
        $http.post(Ajax.POST_GROUP_MEMBERSHIP, 
            {'participant_id': $participantId, 'group_id': $groupId, 'watching_only': 0 }).
            success(function(data, status, headers, config) {
                State.debug = data;
                State.authenticate();
            }).
            error(function(data, status, headers, config) {
                State.debug = data;
            });
    }
    $scope.watchGroup = function($groupId) {
        $participantId = State.activeParticipant.participantId;
        $http.post(Ajax.POST_GROUP_MEMBERSHIP, 
            {'participant_id': $participantId, 'group_id': $groupId, 'watching_only': 1 }).
            success(function(data, status, headers, config) {
                State.debug = data;
                State.authenticate();
            }).
            error(function(data, status, headers, config) {
                State.debug = data;
            });
    }
    $scope.unwatchOrUnjoinGroup = function($groupId) {
        $participantId = State.activeParticipant.participantId;
        $http.post(Ajax.DELETE_GROUP_MEMBERSHIP, 
            {'participant_id': $participantId, 'group_id': $groupId }).
            success(function(data, status, headers, config) {
                State.debug = data;
                State.authenticate();
            }).
            error(function(data, status, headers, config) {
                State.debug = data;
            });
    }
    
    $scope.changeActiveParticipant = function(participantId) {
        //State.activeParticipantId = participantId;
        State.activeParticipant = State.allParticipants[participantId];
        State.participantDataChanged++;
        $state.go('main.home');
    }
    
    //this can be more efficient
    /*
    $scope.alreadyFriends = function(friendId) {
        //State.tester += friendId + " ";
        friend = false;
        for (var i=0; i<State.activeParticipant.friendCollection.length; i++) {
            if (friendId == State.activeParticipant.friendCollection[i].person_id) {
                friend = true;
            }
        }
        return friend;
    }
    */
    
    //this can be more efficient
    $scope.friendStatus = function(friendId) {
        //State.tester += friendId + " ";
        var status = "none";
        for (var i=0; i<State.activeParticipant.friendCollection.length; i++) {
            if (friendId == State.activeParticipant.friendCollection[i].person_id) {
                status = State.activeParticipant.friendCollection[i].relationship_type;
            }
        }
        return status;
    }
    
    
    //look up whether a person belongs to current group
    $scope.memberStatus = function(personId) {
        var status = "none";
        for (var i=0; i<State.activeParticipant.memberCollection.length; i++) {
            if (personId == State.activeParticipant.memberCollection[i].person_id) {
                status = State.activeParticipant.memberCollection[i].relationship_type;
            }
        }
        return status;
    }
    
    //look up whether current person belongs to group
    $scope.membershipStatus = function(groupId) {
        var status = "none";
        for (var i=0; i<State.activeParticipant.membershipCollection.length; i++) {
            if (groupId == State.activeParticipant.membershipCollection[i].group_id) {
                status = State.activeParticipant.membershipCollection[i].relationship_type;
            }
        }
        return status;
    }
    
    
    $scope.inviteMember = function($friendId) {
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
    $scope.removeMember = function($friendId) {
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


