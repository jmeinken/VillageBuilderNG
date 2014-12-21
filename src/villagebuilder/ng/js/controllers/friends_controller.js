app.controller('FriendsController', function($scope, $state, $http, Ajax, State) {
    
    //not sure if I need this controller since all the data is already stored in the state
    $scope.friendCollection = [];
    $scope.membershipCollection = [];

    $scope.$watch(function() {return State.activeParticipant.friendCollection}, function() {
            $scope.friendCollection = State.activeParticipant.friendCollection;
            $scope.membershipCollection = State.activeParticipant.membershipCollection;
    });
    
    
    
    
    
});


