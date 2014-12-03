app.controller('AccountInfoFormController', function($scope, $location, $state, State, Request) {
    
    $scope.request = {};
    $scope.inputFields = [];
    

    $scope.$watch(function() {return Request[$scope.$parent.form]}, function() {
        $scope.request = Request[$scope.$parent.form];
        State.debug = $state.current;
        if ($state.current.name == 'main.create-group.account-info') {
            $scope.inputFields = ['email','title','description'];
        } else {
            $scope.inputFields = ['email','first_name','last_name','password','password_again'];
        }
    });

    $scope.validateForm = function(isValid) {
        if (isValid) {
            State.debug="validated";
            $scope.completion['account_info'] = true;
            $state.go($scope.mapView);
            //$location.path( "/create-account/map" );
        } else {
            State.debug="validate failed";
            $scope.completion['account_info'] = false;
            $scope.showInputErrors = true;
        }
    }

});




