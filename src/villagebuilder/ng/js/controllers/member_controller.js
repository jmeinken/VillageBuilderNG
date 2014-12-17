app.controller('MemberController', function($scope, $state, $http, Ajax, State) {
    
    $scope.memberCollection = [];
    $scope.watcherCollection = [];

    $scope.$watch(function() {return State.participantDataChanged}, function() {
            $scope.memberCollection = State.activeParticipant.memberCollection;
            $scope.watcherCollection = State.activeParticipant.watcherCollection;
    });
    
    
    
    
    
});


