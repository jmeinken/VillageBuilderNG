app.config(function($stateProvider, $urlRouterProvider) {
    $urlRouterProvider.otherwise('/home');
    $stateProvider
        .state('home', {
            url: '/home',
            templateUrl: 'templates/home.html?x=2',
            controller: 'HomeController'
        })
        .state('login', {
            url: '/login',
            controller: 'LoginController',
            templateUrl: 'templates/login.html?x=1'
        })
        .state('create-account', {
            url: '/create-account',
            templateUrl: 'templates/create_account.html?x=7',
            controller: 'CreateAccountController'
        })
        .state('manage-account', {
            url: '/manage-account',
            templateUrl: 'templates/manage_account.html?x=8',
            controller: 'ManageAccountController'
        })
        .state('manage-password', {
            url: '/manage-password',
            templateUrl: 'templates/manage_password.html?x=9',
            controller: 'ManagePasswordController'
        })
        .state('reset-password', {
            url: '/reset-password',
            templateUrl: 'templates/reset_password.html?x=10',
            controller: 'ResetPasswordController'
        })
        .state('info', {
            url: '/info',
            templateUrl: 'templates/info.html?x=5'
        })
        .state('activate-account', {
            url: '/activate-account/:code',
            templateUrl: 'templates/activate_account.html?x=6',
            controller: 'ActivateAccountController'
        })
        .state('activate-reset-password', {
            url: '/activate-reset-password/:code',
            templateUrl: 'templates/activate_reset_password.html?x=11',
            controller: 'ActivateResetPasswordController'
        })
});


