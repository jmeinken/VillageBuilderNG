
app.controller('GlobalController', function($scope, State, Request) {
    
    //the entire State service is globally available to all templates
    $scope.State = State;
    $scope.Request = Request;
    
    $scope.keyArray = function(obj) {  
        var arr = [];
        var i=0;
        for (var key in obj) {
            arr[i] = key;
            i++;
        }
        return arr;
    }
    
   
    

   
 



}); 


