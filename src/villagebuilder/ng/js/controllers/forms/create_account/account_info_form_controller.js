app.controller('AccountInfoFormController', function($scope, $location, State, Request) {
    
    $scope.request = {};
    $scope.inputFields = [];
    $scope.showInputErrors = false;

    $scope.$watch(function() {return Request.createAccount}, function() {
        $scope.request = Request.createAccount;
        $scope.inputFields = ['email','password','password_again','first_name','last_name'];
    });

    $scope.validateForm = function(isValid) {
        if (isValid) {
            State.debug="validated";
            $location.path( "/create-account/map" );
        } else {
            State.debug="validate failed";
            $scope.showInputErrors = true;
        }
    }

});




