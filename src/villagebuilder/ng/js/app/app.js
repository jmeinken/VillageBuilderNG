
var app = angular.module('app', ['ngSanitize', 'ngRoute'], function($httpProvider) {
  // Use x-www-form-urlencoded Content-Type
  $httpProvider.defaults.headers.post['Content-Type'] = 'application/x-www-form-urlencoded;charset=utf-8';

  /**
   * converts an object to x-www-form-urlencoded serialization.
   * 
   * This code is required to talk to PHP, which does not have built-in
   * JSON serialization decoding for post requests.    
   *             
   * @param {Object} obj
   * @return {String}
   */ 
  var param = function(obj) {
    var query = '', name, value, fullSubName, subName, subValue, innerObj, i;

    for(name in obj) {
      value = obj[name];

      if(value instanceof Array) {
        for(i=0; i<value.length; ++i) {
          subValue = value[i];
          fullSubName = name + '[' + i + ']';
          innerObj = {};
          innerObj[fullSubName] = subValue;
          query += param(innerObj) + '&';
        }
      }
      else if(value instanceof Object) {
        for(subName in value) {
          subValue = value[subName];
          fullSubName = name + '[' + subName + ']';
          innerObj = {};
          innerObj[fullSubName] = subValue;
          query += param(innerObj) + '&';
        }
      }
      else if(value !== undefined && value !== null)
        query += encodeURIComponent(name) + '=' + encodeURIComponent(value) + '&';
    }

    return query.length ? query.substr(0, query.length - 1) : query;
  };

  // Override $http service's default transformRequest
  $httpProvider.defaults.transformRequest = [function(data) {
    return angular.isObject(data) && String(data) !== '[object File]' ? param(data) : data;
  }];
});

/*
 * ROUTES
 */

app.config(function($routeProvider) {
    $routeProvider.
        when('/home', {
            templateUrl: 'templates/home.html?x=1',
            controller: 'HomeController'
        }).
        when('/login', {
            templateUrl: 'templates/login.html?x=2',
            controller: 'LoginController'
        }).
        when('/create_account', {
            templateUrl: 'templates/create_account.html?x=7',
            controller: 'CreateAccountController'
        }).
        when('/manage_account', {
            templateUrl: 'templates/manage_account.html?x=8',
            controller: 'ManageAccountController'
        }).
        when('/manage_password', {
            templateUrl: 'templates/manage_password.html?x=9',
            controller: 'ManagePasswordController'
        }).
        when('/reset_password', {
            templateUrl: 'templates/reset_password.html?x=10',
            controller: 'ResetPasswordController'
        }).
        when('/info', {
            templateUrl: 'templates/info.html?x=5'
        }).   
        when('/activate_account/:code', {
            templateUrl: 'templates/activate_account.html?x=6',
            controller: 'ActivateAccountController'
        }).
        when('/activate_reset_password/:code', {
            templateUrl: 'templates/activate_reset_password.html?x=11',
            controller: 'ActivateResetPasswordController'
        }).
        otherwise({
            redirectTo:'/home', 
            templateUrl:'templates/home.html?x=4',
            controller: 'HomeController'
        }); 
});






/*
 * adds bindUnsafeHtml directive 
 */
app.directive('bindUnsafeHtml', ['$compile', function ($compile) {
      return function(scope, element, attrs) {
          scope.$watch(
            function(scope) {
              // watch the 'bindUnsafeHtml' expression for changes
              return scope.$eval(attrs.bindUnsafeHtml);
            },
            function(value) {
              // when the 'bindUnsafeHtml' expression changes
              // assign it into the current DOM
              element.html(value);

              // compile the new DOM and link it to the current
              // scope.
              // NOTE: we only compile .childNodes so that
              // we don't get into infinite loop compiling ourselves
              $compile(element.contents())(scope);
            }
        );
    };
}]);


var INTEGER_REGEXP = /^\-?\d+$/;
app.directive('integer', function() {
  return {
    require: 'ngModel',
    link: function(scope, elm, attrs, ctrl) {
      ctrl.$parsers.unshift(function(viewValue) {
        if (INTEGER_REGEXP.test(viewValue)) {
          // it is valid
          ctrl.$setValidity('integer', true);
          return viewValue;
        } else {
          // it is invalid, return undefined (no model update)
          ctrl.$setValidity('integer', false);
          return undefined;
        }
      });
    }
  };
});



app.directive('vbBuildForm', function() {
    
    return {
        scope: {
            request : "=vbRequestObject",
            submit : "=vbSubmit"
        },
        templateUrl: 'templates/directive_templates/form.html?x=20',
        link: function(scope){
            scope.keyArray = function(obj) {
                var arr = [];
                var i=0;
                for (var key in obj) {
                    arr[i] = key;
                    i++;
                }
                return arr;
            }
        }
    };
});




