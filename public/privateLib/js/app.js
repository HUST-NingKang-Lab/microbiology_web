/**
 * Created by robin on 5/17/17.
 */
(function () {
    var app=angular.module('myapp',['controller','ui.router','angularFileUpload']);
    app.config(function ($stateProvider,$urlRouterProvider,$locationProvider) {
        $urlRouterProvider.otherwise('/home');
        $stateProvider.state(
            'home',
            {url:'/home',templateUrl:'/views/home.html'}
        ).state(
            'structureAnalysis',
            {url:'/structureAnalysis',templateUrl:'/views/structureAnalysis.html',controller:'structureAnalysisCtl'}
        ).state(
            'search',
            {url:'/search',templateUrl:'/views/search.html'}
        ).state(
            'tools',
            {url:'/tools',templateUrl:'/views/tools.html'}
        ).state(
            'help',
            {url:'/help',templateUrl:'/views/help.html'}
        ).state(
            'about',
            {url:'/about',templateUrl:'/views/about.html'}
        );
        $locationProvider.html5Mode(true);
    });
})();