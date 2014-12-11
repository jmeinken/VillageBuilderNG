
app.controller('LoggedOutController', function($scope, $location, State) {
    

    $scope.showPage = false;
    
    //check authentication and redirect or show page
    $scope.$watch(function() {return State.authenticated}, function (value) {
            //do if logged in
            if (typeof value !== 'undefined' && value === true) {
                //State.authenticate();
                $location.path( State.intendedLocation );
            //do if logged out    
            } else if (typeof value !== 'undefined' && value === false){
                $scope.showPage = true;
            }
    });

}); 


