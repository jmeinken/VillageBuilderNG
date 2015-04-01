
app.controller('GlobalController', function($scope, $state, $http, Ajax, State, Request, Utilities) {
    
    //the entire State service is globally available to all templates
    $scope.State = State;
    $scope.Request = Request;
    $scope.$state = $state;
    
    $scope.participantImagePath = 'assets/images/user_images/';
    $scope.participantImageDefault = 'assets/images/generic-user.png';

    $scope.getImage = function(imageName) {
        if (imageName) {
            return $scope.participantImagePath + imageName;
        } else {
            return $scope.participantImageDefault;
        }
    }
    
    
    
    $scope.keyArray = Utilities.keyArray;
    
    $scope.addFriend = function($participantId, $friendId) {
        $http.post(Ajax.POST_FRIENDSHIP, {'participant_id': $participantId, 'friend_id': $friendId }).
            success(function(data, status, headers, config) {
                State.debug = data;
                State.authenticate();
            }).
            error(function(data, status, headers, config) {
                State.debug = data;
            });
    }
    $scope.deleteFriend = function($participantId, $friendId) {
        //$participantId = State.activeParticipant.participant_id;
        $http.post(Ajax.DELETE_FRIENDSHIP, {'participant_id': $participantId, 'friend_id': $friendId }).
            success(function(data, status, headers, config) {
                State.debug = data;
                State.authenticate();
            }).
            error(function(data, status, headers, config) {
                State.debug = data;
            });
    }
    $scope.deleteAlert = function($alertId) {
        $http.post(Ajax.DELETE_ALERT, {'alert_id': $alertId}).
            success(function(data, status, headers, config) {
                State.debug = data;
                State.authenticate();
            }).
            error(function(data, status, headers, config) {
                State.debug = data;
            });
    }
    $scope.joinGroup = function($participantId, $groupId) {
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
    $scope.watchGroup = function($participantId, $groupId) {
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
    $scope.unwatchOrUnjoinGroup = function($participantId, $groupId) {
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
    $scope.approveGroupMember = function($participantId, $groupId) {
        $http.post(Ajax.PUT_APPROVE_MEMBERSHIP, 
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
        for (var i = 0; i < State.allParticipants.length; i++) {
            if (State.allParticipants[i].participant_id == participantId) {
                State.activeParticipant = State.allParticipants[i];
            }
        }
        State.participantDataChanged++;
        $state.go('main.home');
    };
    
   
    $scope.friendStatus = function(friendId) {
        if (friendId == State.activeParticipant.participant_id) {
            return "self";
        }
        friendships = State.activeParticipant.friendships;
        if ($.inArray(friendId, friendships.reciprocated)!=-1) {
            return "reciprocated";
        }
        if ($.inArray(friendId, friendships.requesting)!=-1) {
            return "requesting";
        }
        if ($.inArray(friendId, friendships.requestReceived)!=-1) {
            return "requestReceived";
        }
        if ($.inArray(friendId, friendships.guest)!=-1) {
            return "guest";
        }
        return "none";
    }
    
    
    //look up whether a person belongs to current group
    $scope.memberStatusOld = function(personId) {
        for (var participant in State.allParticipants) {
            if (personId == State.allParticipants[participant].participantId) {
                return "owner";
            }
        }
        for (var i=0; i<State.activeParticipant.memberCollection.length; i++) {
            if (personId == State.activeParticipant.memberCollection[i].person_id) {
                return State.activeParticipant.memberCollection[i].relationship_type;
            }
        }
        return "none";
    }
    
    $scope.memberStatus = function(personId) {
        members = State.activeParticipant.members;
        if ($.inArray(personId, members.member)!=-1) {
            return "member";
        }
        if ($.inArray(personId, members.watching)!=-1) {
            return "watcher";
        }
        if ($.inArray(personId, members.membershipRequested)!=-1) {
            return "membershipRequested";
        }
        return "none";
    }
    
    //look up whether current person belongs to group
    $scope.membershipStatusOld = function(groupId) {
        for (var participant in State.allParticipants) {
            if (groupId == State.allParticipants[participant].participantId) {
                return "owner";
            }
        }
        for (var i=0; i<State.activeParticipant.membershipCollection.length; i++) {
            if (groupId == State.activeParticipant.membershipCollection[i].group_id) {
                return State.activeParticipant.membershipCollection[i].relationship_type;
            }
        }
        return "none";
    }
    
    $scope.membershipStatus = function(groupId) {
        memberships = State.activeParticipant.memberships;
        ownerships = State.activeParticipant.ownerships;
        if ($.inArray(groupId, ownerships.owner)!=-1) {
            return "owner";
        }
        if ($.inArray(groupId, memberships.member)!=-1) {
            return "member";
        }
        if ($.inArray(groupId, memberships.watching)!=-1) {
            return "watcher";
        }
        if ($.inArray(groupId, memberships.membershipRequested)!=-1) {
            return "membershipRequested";
        }
        return "none";
    }
    
    
    $scope.inviteMember = function($friendId) {
        $participantId = State.activeParticipant.participant_id;
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
        $participantId = State.activeParticipant.participant_id;
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


