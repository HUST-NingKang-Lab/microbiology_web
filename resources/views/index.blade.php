<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width,initial-scale=1">
    <base href="/">
    <!--javascript navigation and basic library-->
    <script src="publicLib/bower_components/jquery/dist/jquery.min.js"></script>
    <script src="publicLib/bower_components/bootstrap/dist/js/bootstrap.min.js"></script>
    <script src="publicLib/bower_components/angular/angular.min.js"></script>
    <script src="publicLib/bower_components/angular-ui-router/release/angular-ui-router.js"></script>
    <!--angular for fileupload module-->
    <script src="publicLib/bower_components/angular-file-upload/dist/angular-file-upload.min.js"></script>
    <!-- private javascript for angular-->
    <script src="privateLib/js/app.js"></script>
    <script src="privateLib/js/controller.js"></script>
    <!--for fileupload-->
    <link href="publicLib/bower_components/bootstrap/dist/css/bootstrap.min.css" rel="stylesheet">
    <link href="privateLib/css/style.css" rel="stylesheet">
    <!--bootstrap depend on bootstrap.js and jquery.js-->
    <title>MSE</title>
</head>
<body ng-app="myapp" ng-controller="navCtrl">
<div class="logo">
    <a href="/"><img src="images/logo-5.png"></a>
</div>
<div role="navigation">
    <div class="navigationDiv">
        <ul class="navigationUl">
            <li><a ng-class="{active:$location.path()=='/home'}" href="home">Home</a></li>
            <li><a ng-class="{active:$location.path()=='/structureAnalysis'}" href="structureAnalysis">Structure Analysis</a></li>
            <li><a ng-class="{active:$location.path()=='/search'}" href="search">Search</a></li>
            <li><a ng-class="{active:$location.path()=='/tools'}" href="tools">Tools</a></li>
            <li><a href="http://www.biosino.org/microbiome" target="_blank">Data Portal</a></li>
            <li class="right"><a ng-class="{active:$location.path()=='/help'}" href="help">Help</a></li>
            <li class="right"><a ng-class="{active:$location.path()=='/about'}" href="about">About</a></li>
        </ul>
    </div>
</div>
<div ui-view></div>
<footer class="footer">this is footer</footer>
</body>
</html>