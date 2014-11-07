app.controller('HomeController', function($scope, $location, State) {
    

    $scope.showView = false;
    
    //check authentication and redirect
    $scope.$watch(function() {return State.authenticated}, function (value) {
        //do if logged out
        if (typeof value !== 'undefined' && value === false) {
            State.intendedLocation = '/home';
            $location.path( "/login" );
        //do if logged in
        } else if (typeof value !== 'undefined' && value === true){
            $scope.showView = true;
        }
    });

}); 



