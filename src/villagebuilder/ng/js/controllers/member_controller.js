app.controller('MemberController', function($scope, $state, $http, Ajax, State) {
    
    $scope.memberCollection = [];

    $scope.$watch(function() {return State.participantDataChanged}, function() {
            $scope.memberCollection = State.activeParticipant.memberCollection;
    });
    
    
    
    
    
});


