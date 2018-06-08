/* ============================================================
 * File: app.js
 * Configure global module dependencies.
 * will be loaded on demand using ocLazyLoad
 * ============================================================ */

'use strict';

angular.module('app', [
    'ui.router',
    'ui.utils',
    'oc.lazyLoad',
    'infinite-scroll',
    'angularMoment',
    'wu.masonry',
	'ngSanitize',
	'LocalStorageModule',
	// 'angularGrid'
	
]).config(['$compileProvider', function ($compileProvider) {
  // $compileProvider.debugInfoEnabled(false);
}]);
