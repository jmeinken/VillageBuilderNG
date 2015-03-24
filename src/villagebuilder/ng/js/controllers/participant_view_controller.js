
app.controller('ParticipantViewController', function($scope, $stateParams, State, Request, Ajax, $http) {
    
    
    $scope.participantId = $stateParams.participant_id;
    $scope.participant = {};
    
    $scope.loadParticipant = function() {
        parameters = { 'participant_id' : $stateParams.participant_id };
        $http.get(Ajax.GET_PARTICIPANT, {params: parameters}).
            success(function(data, status, headers, config) {
                $scope.participant = data;
            }).
            error(function(data, status, headers, config) {
                State.debug = data;
            });
    }
    
});


