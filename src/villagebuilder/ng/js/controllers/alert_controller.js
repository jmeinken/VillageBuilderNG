app.controller('AlertController', function($scope, $state, $http, $timeout, Ajax, State) {
    
    $scope.alertCollection = {};
    $scope.newAlertCount = 0;
    
    $scope.$watch(function() {return State.userDataChanged}, function() {

        parameters = { 'user_id' : State.userId };
        $http.get(Ajax.GET_COLLECTION_ALERT, {params: parameters}).
            success(function(data, status, headers, config){
                //State.debug = data;
                $scope.alertCollection = data;
                $scope.newAlertCount = 0;
                for (var participant in data) {
                    if (data.hasOwnProperty(participant)) {
                        for (var i = 0; i<data[participant].length; i++) {
                            if (data[participant][i].viewed == 0) {
                                $scope.newAlertCount++;
                            }
                        }
                    }
                }
                
                /*
                for (var i = 0; i<data[State.activeParticipantId].length; i++) {
                    if (data[State.activeParticipantId][i].viewed == 0) {
                        $scope.newAlertCount++;
                    }
                }
                */
            }).
            error(function(data, status, headers, config) {
                State.debug = data;
            });
        
        
    });
    
    $scope.alertsViewed = function() {
        State.debug = "alerts viewed";
        $participantId = State.activeParticipant.participantId;
        $http.post(Ajax.POST_RESET_UNVIEWED_ALERT_COUNT, {'participant_id': $participantId}).
            success(function(data, status, headers, config) {
                //reset the state in order to update alert info after 30 seconds
                $timeout(function() { State.authenticate(); }, 5000);
            }).
            error(function(data, status, headers, config) {
                State.debug = data;
            });
    }
    
    
    
});


