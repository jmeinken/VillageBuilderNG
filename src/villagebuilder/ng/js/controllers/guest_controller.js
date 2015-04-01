
app.controller('GuestController', function($scope, $location, $http, $stateParams, Ajax, State, ErrorHandler) {
    
    //user is not redirected even if they are logged in
    
    $scope.code = $stateParams.code;
    
    
    $scope.activateAccount = function() {
        State.authenticateGuest($stateParams.code);
    }



}); 





