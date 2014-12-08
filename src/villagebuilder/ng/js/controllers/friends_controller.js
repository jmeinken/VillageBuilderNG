app.controller('FriendsController', function($scope, $state, $http, Ajax, State) {
    
    $scope.friendCollection = [];

    $scope.$watch(function() {return State.activeAccount.friendCollection}, function() {
            $scope.friendCollection = State.activeAccount.friendCollection;
    });
    
    
    
    
    
});


