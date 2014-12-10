app.controller('AlertController', function($scope, $state, $http, Ajax, State) {
    
    $scope.alertCollection = [];
    $scope.newAlertCount = 0;
    
    $scope.$watch(function() {return State.activeAccount.participantId}, function() {

        parameters = { 'participant_id' : State.activeAccount.participantId };
        $http.get(Ajax.GET_COLLECTION_ALERT, {params: parameters}).
            success(function(data, status, headers, config){
                $scope.alertCollection = data;
                for (var i = 0; i<data.length; i++) {
                    if (data[i].viewed == 0) {
                        $scope.newAlertCount++;
                    }
                }
            }).
            error(function(data, status, headers, config) {
                State.debug = data;
            });
        
        
    });
    
    $scope.alertsViewed = function() {
        State.debug = "alerts viewed";
        $participantId = State.activeAccount.participantId;
        $http.post(Ajax.POST_RESET_UNVIEWED_ALERT_COUNT, {'participant_id': $participantId}).
            success(function(data, status, headers, config) {
                State.debug = data;
                //State.authenticate;
            }).
            error(function(data, status, headers, config) {
                State.debug = data;
            });
    }
    
    
    
});


