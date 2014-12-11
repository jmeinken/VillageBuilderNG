app.controller('FriendsController', function($scope, $state, $http, Ajax, State) {
    
    $scope.friendCollection = [];

    $scope.$watch(function() {return State.activeParticipant.friendCollection}, function() {
            $scope.friendCollection = State.activeParticipant.friendCollection;
    });
    
    
    
    
    
});


