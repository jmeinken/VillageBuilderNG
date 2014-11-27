
app.controller('CreateAccountFormController', function($scope, $location, $http, Ajax, State, Request) {
    
    var form = 'createAccount';
    var getUrl = Ajax.GET_ACCOUNT;
    var postUrl = Ajax.POST_ACCOUNT;
   
    $scope.completion = [];
    $scope.completion['account_info'] = false;
    $scope.completion['address'] = false;
    $scope.completion['personal_info'] = false;



    $scope.loadForm = function() {
        Request.loadForm(form, getUrl);
    }

    $scope.resetForm = function() {
        Request.reset(form);
    }

    $scope.validateAccountInfo = function() {
        State.debug="it worked!";
    }
    

    
    
    



    
    
    

   
 



}); 


