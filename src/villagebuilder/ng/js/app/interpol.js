//http://stackoverflow.com/questions/25651288/angularjs-form-validation-on-dynamically-generated-input-directives-not-working/25652866#25652866

//this attaches Angular validator to forms that are build inside a directive


angular.module('interpol', [])

  .config(function($provide) {

    $provide.decorator('ngModelDirective', function($delegate) {
      var ngModel = $delegate[0], controller = ngModel.controller;
      ngModel.controller = ['$scope', '$element', '$attrs', '$injector', function(scope, element, attrs, $injector) {
        var $interpolate = $injector.get('$interpolate');
        attrs.$set('name', $interpolate(attrs.name || '')(scope));
        $injector.invoke(controller, this, {
          '$scope': scope,
          '$element': element,
          '$attrs': attrs
        });
      }];
      return $delegate;
    });

    $provide.decorator('formDirective', function($delegate) {
      var form = $delegate[0], controller = form.controller;
      form.controller = ['$scope', '$element', '$attrs', '$injector', function(scope, element, attrs, $injector) {
        var $interpolate = $injector.get('$interpolate');
        attrs.$set('name', $interpolate(attrs.name || attrs.ngForm || '')(scope));
        $injector.invoke(controller, this, {
          '$scope': scope,
          '$element': element,
          '$attrs': attrs
        });
      }];
      return $delegate;
    });
  })

  .run(function($rootScope) {
    $rootScope.models = [{
      value: 'foo'
    },{
      value: 'bar'
    },{
      value: 'baz'
    }];
});