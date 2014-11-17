//http://ngmodules.org/modules/angular-input-match

app.directive('match', function () {
        return {
            require: 'ngModel',
            restrict: 'A',
            scope: {
                match: '='
            },
            link: function(scope, elem, attrs, ctrl) {
                scope.$watch(function() {
                    var modelValue = ctrl.$modelValue || ctrl.$$invalidModelValue;
                    return (ctrl.$pristine && angular.isUndefined(modelValue)) || scope.match === modelValue;
                }, function(currentValue) {
                    ctrl.$setValidity('match', currentValue);
                });
            }
        };
    });
    
    
//http://stackoverflow.com/questions/24136040/conditionally-add-an-element-attribute-in-angular-js
app.directive('attronoff', function() {
    return {
    link: function($scope, $element, $attrs) {
        $scope.$watch(
            function () { return $element.attr('data-attr-on'); },
            function (newVal) { 
                var attr = $element.attr('data-attr-name');

                if(!eval(newVal)) {
                    $element.removeAttr(attr);
                }
                else {
                    $element.attr(attr, attr);
                }
            }
        );
        }
    };
});