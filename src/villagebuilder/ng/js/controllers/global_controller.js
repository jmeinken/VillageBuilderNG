
app.controller('GlobalController', function($scope, State, Request, Utilities) {
    
    //the entire State service is globally available to all templates
    $scope.State = State;
    $scope.Request = Request;

    
    
    
    $scope.keyArray = Utilities.keyArray;
    
   
    

   
 



}); 


