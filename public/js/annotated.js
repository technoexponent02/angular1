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

/* ============================================================
 * File: config.js
 * Configure routing
 * ============================================================ */
angular.module('app')
    .constant('constants', {
        SEARCH_MIN_LEN: 3,
        VIDEO_VIEW_PER: 0.2
    })
    .config(['$stateProvider', '$urlRouterProvider', '$ocLazyLoadProvider', '$locationProvider',

        function ($stateProvider, $urlRouterProvider, $ocLazyLoadProvider, $locationProvider) {

            $urlRouterProvider.otherwise('/');

            $stateProvider
                .state('feed', {
                    url: "/",
                    templateUrl: 'feeds',
                    controller: 'ShowFeedCtrl',
                    resolve: {
                        meta: ['$rootScope', function ($rootScope) {
                            var title = "Feeds | SWOLK";
                            $rootScope.meta = {title: title};
                        }],
                        deps: ['$ocLazyLoad', function ($ocLazyLoad) {
                            $("html, body").removeClass("sidebar-open bodyHidden");
                            $("#sidebarOuter").removeClass("show");
                            return $ocLazyLoad.load([
                                'nvd3',
                                'mapplic',
                                'rickshaw',
                                'metrojs',
                                'sparkline',
                                'skycons',
                                'switchery'
                            ], {
                                insertBefore: '#lazyload_placeholder'
                            })
                            .then(function () {
                                return $ocLazyLoad.load([
                                    'assets/js/controllers/feed/show.js?rev='+_uuid4
                                ]);
                            });
                        }]
                    }
                })

                .state('profile', {
                    url: "/profile?src",
                    templateUrl: "tpl.profile",
                    controller: 'ProfileCtrl',
                    resolve: {
                        meta: ['$rootScope', function ($rootScope) {
                            var title = "Profile | SWOLK";
                            $rootScope.meta = {title: title};
                        }],
                        deps: ['$ocLazyLoad', function ($ocLazyLoad) {
                            $("html, body").removeClass("sidebar-open bodyHidden scrollHidden modal-open");
                            $("#sidebarOuter").removeClass("show");
                            return $ocLazyLoad.load([
                                'mapplic'
                            ], {
                                insertBefore: '#lazyload_placeholder'
                            })
                            .then(function () {
                                return $ocLazyLoad.load([
                                    'assets/js/controllers/profile.js?rev='+_uuid4
                                ]);
                            });
                        }]
                    }
                })

                .state('account', {
                    url: "/profile/:username",
                     templateUrl: "tpl.profile",
                    controller: 'ProfileCtrl',
                    resolve: {
                        meta: ['$rootScope', '$stateParams', function ($rootScope, $stateParams) {
                            var title = "Profile | SWOLK";
                            $rootScope.meta = {title: title};
                        }],
                        deps: ['$ocLazyLoad', function ($ocLazyLoad) {
							$("html, body").removeClass("scrollHidden modal-open");
                            return $ocLazyLoad.load([
                                'mapplic',
								'switchery',
                                'select'
                            ], {
                                insertBefore: '#lazyload_placeholder'
                            })
                            .then(function () {
                                return $ocLazyLoad.load([
                                    'assets/js/controllers/profile.js?rev='+_uuid4
                                ]);
                            });
                        }]
                    }
                })
                /*------- Post Add start -------*/
                .state('post-add', {
                    url: '/post-add',
                    templateUrl: 'post-ad',
                    controller: 'AddPostCtrl',
                    redirectTo: 'post-add.photo',
                    resolve: {
                        deps: ['$ocLazyLoad', function ($ocLazyLoad) {
                            $("html, body").removeClass("sidebar-open bodyHidden");
							$("#sidebarOuter").removeClass("show");
                            return $ocLazyLoad.load([
                                'switchery',
                                'select',
                                'inputMask',
                                'js-tag'
                            ], {
                                insertBefore: '#lazyload_placeholder'
                            })
                            .then(function () {
                                return $ocLazyLoad.load([
                                    'assets/js/controllers/post/add-post.js?rev='+_uuid4,
                                    'assets/js/modules/uploader.js?rev='+_uuid4
                                ]);
                            });
                        }]
                    }
                })
                // All post types..
                .state('post-add.photo', {
                    templateUrl: 'post-ad/container/photo',
                    controller: 'AddImagePostCtrl',
                    redirectTo: 'post-add.photo.start',
                    resolve: {
                        stateData: ['$rootScope', function ($rootScope) {
                            $rootScope.stateData = {
                                postType: 'photo'
                            };
                        }],
                        deps: ['$ocLazyLoad', function ($ocLazyLoad) {
                            return $ocLazyLoad.load([
                                'assets/js/controllers/post/add-image.js?rev='+_uuid4
                            ]);
                        }]
                    }
                })
                .state('post-add.video', {
                    templateUrl: 'post-ad/container/video',
                    controller: 'AddVideoPostCtrl',
                    redirectTo: 'post-add.video.start',                    
                    resolve: {
                        stateData: ['$rootScope', function ($rootScope) {
                            $rootScope.stateData = {
                                postType: 'video'
                            };
                        }],
                        deps: ['$ocLazyLoad', function ($ocLazyLoad) {
                            return $ocLazyLoad.load([
                                'assets/js/controllers/post/add-video.js?rev='+_uuid4
                            ]);
                        }]
                    }
                })
                .state('post-add.article', {
                    templateUrl: 'post-ad/container/article',
                    controller: 'AddArticlePostCtrl',
                    redirectTo: 'post-add.article.start',
                    resolve: {
                        stateData: ['$rootScope', function ($rootScope) {
                            $rootScope.stateData = {
                                postType: 'article'
                            };
                        }],
                        deps: ['$ocLazyLoad', function ($ocLazyLoad) {
                            return $ocLazyLoad.load([
                                'assets/js/controllers/post/add-article.js?rev='+_uuid4
                            ]);
                        }]
                    }
                })
                .state('post-add.link', {
                    templateUrl: 'post-ad/container/link',
                    controller: 'AddLinkPostCtrl',
                    redirectTo: 'post-add.link.start',
                    resolve: {
                        stateData: ['$rootScope', function ($rootScope) {
                            $rootScope.stateData = {
                                postType: 'link'
                            };
                        }],
                        deps: ['$ocLazyLoad', function ($ocLazyLoad) {
                            return $ocLazyLoad.load([
                                'assets/js/controllers/post/add-link.js?rev='+_uuid4
                            ]);
                        }]
                    }
                })
                .state('post-add.status', {
                    templateUrl: 'post-ad/container/status',
                    controller: 'AddStatusPostCtrl',
                    redirectTo: 'post-add.status.start',
                    resolve: {
                        stateData: ['$rootScope', function ($rootScope) {
                            $rootScope.stateData = {
                                postType: 'status'
                            };
                        }],
                        deps: ['$ocLazyLoad', function ($ocLazyLoad) {
							setTimeout(function () {
								$('.uploadFileNew .needsclick, .uploadFile input, .form-group input, label input').on( "touchstart", function(){
									$(this).trigger('mouseenter');
								});
								$('.form-group, label').on( "touchstart", function(){
									$(this).find("input").trigger('mouseenter');
								});
							}, 200);
                            return $ocLazyLoad.load([
                                'assets/js/controllers/post/add-status.js?rev='+_uuid4
                            ]);
                        }]
                    }
                })
    /*****************************write code for question sction(26-12-17)*******************************************/

                .state('post-add.question', {
                    templateUrl: 'post-ad/container/question',
                    controller: 'AddQuestionPostCtrl',
                    redirectTo: 'post-add.question.start',
                    resolve: {
                        stateData: ['$rootScope', function ($rootScope) {
                            $rootScope.stateData = {
                                postType: 'question'
                            };
                        }],
                        deps: ['$ocLazyLoad', function ($ocLazyLoad) {
							setTimeout(function () {
								$('.uploadFileNew .needsclick, .uploadFile input, .form-group input, label input').on( "touchstart", function(){
									$(this).trigger('mouseenter');
								});
								$('.form-group, label').on( "touchstart", function(){
									$(this).find("input").trigger('mouseenter');
								});
							}, 200);
                            return $ocLazyLoad.load([
                                'assets/js/controllers/post/add-question.js?rev='+_uuid4
                            ]);
                        }]
                    }
                })
                .state('post-add.question.start', {
                    url: '/question',
                    templateUrl: 'post-ad/question',
                    resolve: {
                        meta: ['$rootScope', function ($rootScope) {
                            var title = "Post new question | SWOLK";
                            $rootScope.meta = {title: title};
                        }]
                    }
                })

    /********************************write code for question sction(26-12-17)*******************************************/



                // For photo post..
                .state('post-add.photo.start', {
                    url: '/general',
                    templateUrl: 'post-ad/general/photo',
                    resolve: {
                        meta: ['$rootScope', function ($rootScope) {
                            var title = "Post new photo - General | SWOLK";
                            $rootScope.meta = {title: title};
                        }]
                    }
                })
                .state('post-add.photo.general', {
                    url: '/general',
                    templateUrl: 'post-ad/general/photo',
                    resolve: {
                        meta: ['$rootScope', function ($rootScope) {
                            var title = "Post new photo - General | SWOLK";
                            $rootScope.meta = {title: title};
                        }]
                    }
                })
                .state('post-add.photo.advance', {
                    url: '/advance',
                    templateUrl: 'post-ad/advance',
                    resolve: {
                        meta: ['$rootScope', function ($rootScope) {
                            var title = "Post new photo - Advance | SWOLK";
                            $rootScope.meta = {title: title};
                        }]
                    }
                })
                .state('post-add.photo.social', {
                    url: '/social',
                    templateUrl: 'post-ad/social',
                    resolve: {
                        meta: ['$rootScope', function ($rootScope) {
                            var title = "Post new photo - Social | SWOLK";
                            $rootScope.meta = {title: title};
                        }],
                        deps: ['$ocLazyLoad', function ($ocLazyLoad) {
                            return $ocLazyLoad.load([
                                // 'js-tag',
                                // 'typehead'
                            ], {
                                insertBefore: '#lazyload_placeholder'
                            });
                        }]
                    }
                })
                // For video post..
                .state('post-add.video.start', {
                    url: '/general',
                    templateUrl: 'post-ad/general/video',
                    resolve: {
                        meta: ['$rootScope', function ($rootScope) {
                            var title = "Post new video - General | SWOLK";
                            $rootScope.meta = {title: title};
                        }]
                    }
                })
                .state('post-add.video.general', {
                    url: '/general',
                    templateUrl: 'post-ad/general/video',
                    resolve: {
                        meta: ['$rootScope', function ($rootScope) {
                            var title = "Post new video - General | SWOLK";
                            $rootScope.meta = {title: title};
                        }]
                    }
                })
                .state('post-add.video.advance', {
                    url: '/advance',
                    templateUrl: 'post-ad/advance',
                    resolve: {
                        meta: ['$rootScope', function ($rootScope) {
                            var title = "Post new video - Advance | SWOLK";
                            $rootScope.meta = {title: title};
                        }]
                    }
                })
                .state('post-add.video.social', {
                    url: '/social',
                    templateUrl: 'post-ad/social',
                    resolve: {
                        meta: ['$rootScope', function ($rootScope) {
                            var title = "Post new video - Social | SWOLK";
                            $rootScope.meta = {title: title};
                        }]
                    }
                })
                // For Article post..
                .state('post-add.article.start', {
                    url: '/general',
                    templateUrl: 'post-ad/general/article',
                    resolve: {
                        meta: ['$rootScope', function ($rootScope) {
                            var title = "Post new article - General | SWOLK";
                            $rootScope.meta = {title: title};
                        }]
                    }
                })
                .state('post-add.article.general', {
                    url: '/general',
                    templateUrl: 'post-ad/general/article',
                    resolve: {
                        meta: ['$rootScope', function ($rootScope) {
                            var title = "Post new article - General | SWOLK";
                            $rootScope.meta = {title: title};
                        }]
                    }
                })
                .state('post-add.article.content', {
                    url: '/content',
                    templateUrl: 'post-ad/content',
                    resolve: {
                        meta: ['$rootScope', function ($rootScope) {
                            var title = "Post new article - Content | SWOLK";
                            $rootScope.meta = {title: title};
                        }],
                        deps: ['$ocLazyLoad', function ($ocLazyLoad) {
                            return $ocLazyLoad.load([
                                'angular-froala'
                            ], {
                                insertBefore: '#lazyload_placeholder'
                            });
                        }]
                    }
                })
                .state('post-add.article.advance', {
                    url: '/advance',
                    templateUrl: 'post-ad/advance',
                    resolve: {
                        meta: ['$rootScope', function ($rootScope) {
                            var title = "Post new article - Advance | SWOLK";
                            $rootScope.meta = {title: title};
                        }]
                    }
                })
                .state('post-add.article.social', {
                    url: '/social',
                    templateUrl: 'post-ad/social',
                    resolve: {
                        meta: ['$rootScope', function ($rootScope) {
                            var title = "Post new article - Social | SWOLK";
                            $rootScope.meta = {title: title};
                        }],
                        deps: ['$ocLazyLoad', function ($ocLazyLoad) {
                            return $ocLazyLoad.load([
                                // 'js-tag',
                                // 'typehead'
                            ], {
                                insertBefore: '#lazyload_placeholder'
                            });
                        }]
                    }
                })
                // For Link post..
                .state('post-add.link.start', {
                    url: '/general',
                    templateUrl: 'post-ad/general/link',
                    resolve: {
                        meta: ['$rootScope', function ($rootScope) {
                            var title = "Post new link - General | SWOLK";
                            $rootScope.meta = {title: title};
                        }]
                    }
                })
                .state('post-add.link.general', {
                    url: '/general',
                    templateUrl: 'post-ad/general/link',
                    controller: 'AddLinkPostCtrl',
                    resolve: {
                        meta: ['$rootScope', function ($rootScope) {
                            var title = "Post new link - General | SWOLK";
                            $rootScope.meta = {title: title};
                        }]
                    }
                })
                .state('post-add.link.advance', {
                    url: '/advance',
                    templateUrl: 'post-ad/advance',
                    resolve: {
                        meta: ['$rootScope', function ($rootScope) {
                            var title = "Post new link - Advance | SWOLK";
                            $rootScope.meta = {title: title};
                        }]
                    }
                })
                .state('post-add.link.social', {
                    url: '/social',
                    templateUrl: 'post-ad/social',
                    resolve: {
                        meta: ['$rootScope', function ($rootScope) {
                            var title = "Post new link - Social | SWOLK";
                            $rootScope.meta = {title: title};
                        }]
                    }
                })
                .state('post-add.status.start', {
                    url: '/status',
                    templateUrl: 'post-ad/status',
                    resolve: {
                        meta: ['$rootScope', function ($rootScope) {
                            var title = "Post new status | SWOLK";
                            $rootScope.meta = {title: title};
                        }]
                    }
                })
                /*------- Post Add end -------*/

                /*------- Post Edit start -------*/
                .state('post-edit', {
                    url: '/post-edit?id',
                    templateUrl: 'post-ad',
                    controller: 'AddPostCtrl',
                    //redirectTo: 'post-edit.photo',
                    resolve: {
                        deps: ['$ocLazyLoad', function ($ocLazyLoad) {
                            $("html, body").removeClass("sidebar-open bodyHidden");
                            $("#sidebarOuter").removeClass("show");
                            return $ocLazyLoad.load([
                                'switchery',
                                'select',
                                'inputMask',
                                'js-tag'
                            ], {
                                insertBefore: '#lazyload_placeholder'
                            })
                                .then(function () {
                                    return $ocLazyLoad.load([
                                        'assets/js/controllers/post/add-post.js?rev='+_uuid4,
                                        'assets/js/modules/uploader.js?rev='+_uuid4
                                    ]);
                                });
                        }]
                    }
                })
                // All post types..
                .state('post-edit.photo', {
                    templateUrl: 'post-ad/container/photo',
                    controller: 'EditImageCtrl',
                    redirectTo: 'post-edit.photo.general',
                    resolve: {
                        stateData: ['$rootScope', function ($rootScope) {
                            $rootScope.stateData = {
                                postType: 'photo',
                                editPost: true
                            };
                        }],
                        deps: ['$ocLazyLoad', function ($ocLazyLoad) {
                            return $ocLazyLoad.load([
                                'assets/js/controllers/post/edit/image.js?rev='+_uuid4
                            ]);
                        }]
                    }
                })
                .state('post-edit.video', {
                    templateUrl: 'post-ad/container/video',
                    controller: 'EditVideoCtrl',
                    redirectTo: 'post-edit.video.general',
                    resolve: {
                        stateData: ['$rootScope', function ($rootScope) {
                            $rootScope.stateData = {
                                postType: 'video',
                                editPost: true
                            };
                        }],
                        deps: ['$ocLazyLoad', function ($ocLazyLoad) {
                            return $ocLazyLoad.load([
                                'assets/js/controllers/post/edit/video.js?rev='+_uuid4
                            ]);
                        }]
                    }
                })
                .state('post-edit.link', {
                    templateUrl: 'post-ad/container/link',
                    controller: 'EditLinkCtrl',
                    redirectTo: 'post-edit.link.general',
                    resolve: {
                        stateData: ['$rootScope', function ($rootScope) {
                            $rootScope.stateData = {
                                postType: 'link',
                                editPost: true
                            };
                        }],
                        deps: ['$ocLazyLoad', function ($ocLazyLoad) {
                            return $ocLazyLoad.load([
                                'assets/js/controllers/post/edit/link.js?rev='+_uuid4
                            ]);
                        }]
                    }
                })
                .state('post-edit.article', {
                    templateUrl: 'post-ad/container/article',
                    controller: 'EditArticleCtrl',
                    redirectTo: 'post-edit.article.general',
                    resolve: {
                        stateData: ['$rootScope', function ($rootScope) {
                            $rootScope.stateData = {
                                postType: 'article',
                                editPost: true
                            };
                        }],
                        deps: ['$ocLazyLoad', function ($ocLazyLoad) {
                            return $ocLazyLoad.load([
                                'assets/js/controllers/post/edit/article.js?rev='+_uuid4
                            ]);
                        }]
                    }
                })
                .state('post-edit.status', {
                    templateUrl: 'post-ad/container/status',
                    controller: 'EditStatusCtrl',
                    redirectTo: 'post-edit.status.start',
                    resolve: {
                        stateData: ['$rootScope', function ($rootScope) {
                            $rootScope.stateData = {
                                postType: 'status',
                                editPost: true
                            };
                        }],
                        deps: ['$ocLazyLoad', function ($ocLazyLoad) {
                            setTimeout(function () {
                                $('.uploadFileNew .needsclick, .uploadFile input, .form-group input, label input').on( "touchstart", function(){
                                    $(this).trigger('mouseenter');
                                });
                                $('.form-group, label').on( "touchstart", function(){
                                    $(this).find("input").trigger('mouseenter');
                                });
                            }, 200);
                            return $ocLazyLoad.load([
                                'assets/js/controllers/post/edit/status.js?rev='+_uuid4
                            ]);
                        }]
                    }
                })
    /**************************************Add for edit question(04-01-17) start*****************************************/
                .state('post-edit.question', {
                    templateUrl: 'post-ad/container/question',
                    controller: 'EditQuestionCtrl',
                    redirectTo: 'post-edit.question.start',
                    resolve: {
                        stateData: ['$rootScope', function ($rootScope) {
                            $rootScope.stateData = {
                                postType: 'question',
                                editPost: true
                            };
                        }],
                        deps: ['$ocLazyLoad', function ($ocLazyLoad) {
                            setTimeout(function () {
                                $('.uploadFileNew .needsclick, .uploadFile input, .form-group input, label input').on( "touchstart", function(){
                                    $(this).trigger('mouseenter');
                                });
                                $('.form-group, label').on( "touchstart", function(){
                                    $(this).find("input").trigger('mouseenter');
                                });
                            }, 200);
                            return $ocLazyLoad.load([
                                'assets/js/controllers/post/edit/question.js?rev='+_uuid4
                            ]);
                        }]
                    }
                })
                .state('post-edit.question.start', {
                    
                    url: '/question',
                    templateUrl: 'post-ad/question',
                    resolve: {
                        meta: ['$rootScope', function ($rootScope) {
                            var title = "Edit question | SWOLK";
                            $rootScope.meta = {title: title};
                        }]
                    }
                })
    /**************************************Add for edit question(04-01-17) end*****************************************/




                // For photo post..
                .state('post-edit.photo.general', {
                    url: '/general',
                    templateUrl: 'post-ad/general/edit-photo',
                    resolve: {
                        meta: ['$rootScope', function ($rootScope) {
                            var title = "Edit post - General | SWOLK";
                            $rootScope.meta = {title: title};
                        }]
                    }
                })
                .state('post-edit.photo.advance', {
                    url: '/advance',
                    templateUrl: 'post-ad/advance',
                    resolve: {
                        meta: ['$rootScope', function ($rootScope) {
                            var title = "Edit post - Advance | SWOLK";
                            $rootScope.meta = {title: title};
                        }]
                    }
                })
                .state('post-edit.photo.social', {
                    url: '/social',
                    templateUrl: 'post-ad/social',
                    resolve: {
                        meta: ['$rootScope', function ($rootScope) {
                            var title = "Edit post - Social | SWOLK";
                            $rootScope.meta = {title: title};
                        }],
                        deps: ['$ocLazyLoad', function ($ocLazyLoad) {
                            return $ocLazyLoad.load([
                                // 'js-tag',
                                // 'typehead'
                            ], {
                                insertBefore: '#lazyload_placeholder'
                            });
                        }]
                    }
                })
                // For video post..
                .state('post-edit.video.general', {
                    url: '/general',
                    templateUrl: 'post-ad/general/edit-video',
                    resolve: {
                        meta: ['$rootScope', function ($rootScope) {
                            var title = "Edit video - General | SWOLK";
                            $rootScope.meta = {title: title};
                        }]
                    }
                })
                .state('post-edit.video.advance', {
                    url: '/advance',
                    templateUrl: 'post-ad/advance',
                    resolve: {
                        meta: ['$rootScope', function ($rootScope) {
                            var title = "Edit video - Advance | SWOLK";
                            $rootScope.meta = {title: title};
                        }]
                    }
                })
                .state('post-edit.video.social', {
                    url: '/social',
                    templateUrl: 'post-ad/social',
                    resolve: {
                        meta: ['$rootScope', function ($rootScope) {
                            var title = "Edit video - Social | SWOLK";
                            $rootScope.meta = {title: title};
                        }]
                    }
                })
                // For Article post..
                .state('post-edit.article.general', {
                    url: '/general',
                    templateUrl: 'post-ad/general/edit-article',
                    resolve: {
                        meta: ['$rootScope', function ($rootScope) {
                            var title = "Edit article - General | SWOLK";
                            $rootScope.meta = {title: title};
                        }]
                    }
                })
                .state('post-edit.article.content', {
                    url: '/content',
                    templateUrl: 'post-ad/content',
                    resolve: {
                        meta: ['$rootScope', function ($rootScope) {
                            var title = "Edit article - Content | SWOLK";
                            $rootScope.meta = {title: title};
                        }],
                        deps: ['$ocLazyLoad', function ($ocLazyLoad) {
                            return $ocLazyLoad.load([
                                'angular-froala'
                            ], {
                                insertBefore: '#lazyload_placeholder'
                            });
                        }]
                    }
                })
                .state('post-edit.article.advance', {
                    url: '/advance',
                    templateUrl: 'post-ad/advance',
                    resolve: {
                        meta: ['$rootScope', function ($rootScope) {
                            var title = "Edit article - Advance | SWOLK";
                            $rootScope.meta = {title: title};
                        }]
                    }
                })
                .state('post-edit.article.social', {
                    url: '/social',
                    templateUrl: 'post-ad/social',
                    resolve: {
                        meta: ['$rootScope', function ($rootScope) {
                            var title = "Edit article - Social | SWOLK";
                            $rootScope.meta = {title: title};
                        }],
                        deps: ['$ocLazyLoad', function ($ocLazyLoad) {
                            return $ocLazyLoad.load([
                                // 'js-tag',
                                // 'typehead'
                            ], {
                                insertBefore: '#lazyload_placeholder'
                            });
                        }]
                    }
                })
                // For Link post..
                .state('post-edit.link.general', {
                    url: '/general',
                    templateUrl: 'post-ad/general/edit-link',
                    controller: 'EditLinkCtrl',
                    resolve: {
                        meta: ['$rootScope', function ($rootScope) {
                            var title = "Edit link - General | SWOLK";
                            $rootScope.meta = {title: title};
                        }]
                    }
                })
                .state('post-edit.link.advance', {
                    url: '/advance',
                    templateUrl: 'post-ad/advance',
                    resolve: {
                        meta: ['$rootScope', function ($rootScope) {
                            var title = "Edit link - Advance | SWOLK";
                            $rootScope.meta = {title: title};
                        }]
                    }
                })
                .state('post-edit.link.social', {
                    url: '/social',
                    templateUrl: 'post-ad/social',
                    resolve: {
                        meta: ['$rootScope', function ($rootScope) {
                            var title = "Edit link - Social | SWOLK";
                            $rootScope.meta = {title: title};
                        }]
                    }
                })
                .state('post-edit.status.start', {
                    url: '/status',
                    templateUrl: 'post-ad/status',
                    resolve: {
                        meta: ['$rootScope', function ($rootScope) {
                            var title = "Edit status | SWOLK";
                            $rootScope.meta = {title: title};
                        }]
                    }
                })
                /*------- Post Edit end -------*/

                // Post details page..
                .state('post-details', {
                    // category is caption when post type is status..
                    url: "/post/:category/:subcategory/:title/{id:int}",
                    templateUrl: "post-details",
                    controller: 'PostDetailsCtrl',
                    params: {
                        category: { squash: true, value: null },
                        subcategory: { squash: true, value: null },
                        title: { squash: true, value: null },
                    },
                    resolve: {
                        meta: ['$rootScope', '$stateParams', function ($rootScope, $stateParams) {
                            if ($stateParams.title) {
                                var title = $stateParams.title + " | SWOLK";
                            }
                            else {
                                // category is placeholder for caption
                                var title = $stateParams.category + " | SWOLK";
                            }
                            $rootScope.meta = {title: title};
                        }],
                        deps: ['$ocLazyLoad', function ($ocLazyLoad) {
                            return $ocLazyLoad.load([
                                'mapplic',
                            ], {
                                insertBefore: '#lazyload_placeholder'
                            })
                            .then(function () {
                                return $ocLazyLoad.load([
                                    'assets/js/controllers/post/details.js?rev='+_uuid4
                                ]);
                            });
                        }]
                    }
                })

                // Category-tags page.
                .state('category-tags', {
                    url: "/tag/:name",
                    templateUrl: "category-tags",
                    controller: 'CategoryTagsListingCtrl',
                    params: {
                        subcategory: { squash: true, value: null },
                    },
                    resolve: {
                        meta: ['$rootScope', '$stateParams', function ($rootScope, $stateParams) {

                            var title = $stateParams.name + " | SWOLK";
                            $rootScope.meta = {title: title};
                        }],
                     
                        deps: ['$ocLazyLoad', function ($ocLazyLoad) {
							 $("html, body").removeClass("scrollHidden modal-open mob-hideModal").css({"paddingRight":""});
							 $(".header").removeClass("nav-up");
                            return $ocLazyLoad.load([
                                'switchery',
                                'select'
                            ], {
                                insertBefore: '#lazyload_placeholder'
                            })
                            .then(function () {
                                
                                return $ocLazyLoad.load([
                                    'assets/js/controllers/post/category-tags.js?rev='+_uuid4
                                ]);
                            });
                        }]
                    }
                })

                /******************************* for question tag (31-1-18) done by ps */
                .state('question-tags', {
                    url: "/questions/:name",
                    templateUrl: "category-tags",
                    controller: 'CategoryTagsListingCtrl',
                    params: {
                        subcategory: { squash: true, value: null },
                    },
                    resolve: {
                        meta: ['$rootScope', '$stateParams', function ($rootScope, $stateParams) {

                            var title = $stateParams.name + " | SWOLK";
                            $rootScope.meta = {title: title};
                        }],
                        deps: ['$ocLazyLoad', function ($ocLazyLoad) {
							 $("html, body").removeClass("scrollHidden modal-open mob-hideModal").css({"paddingRight":""});
							 $(".header").removeClass("nav-up");
                            return $ocLazyLoad.load([
                                'switchery',
                                'select'
                            ], {
                                insertBefore: '#lazyload_placeholder'
                            })
                            .then(function () {
                                
                                return $ocLazyLoad.load([
                                    'assets/js/controllers/post/category-tags.js?rev='+_uuid4
                                ]);
                            });
                        }]
                    }
                })

                /******************************* for question tag (31-1-18) done by ps */

















                // Category-tags page.
                .state('place', {
                    url: "/place?location&city&state&country&region&continent",
                    templateUrl: "tpl/placeIndex",
                    controller: 'PlaceCtrl',
                    onEnter: ["$location", "$state", function($location, $state){
                        if (angular.equals($location.search(), {})) {
                            $state.go('profile');
                        }
                    }],
                    /*params: {
                        subcategory: { squash: true, value: null }
                    },*/
                    resolve: {
                        meta: ['$rootScope', '$stateParams', function ($rootScope, $stateParams) {
                            var title = "SWOLK";
                            $rootScope.meta = {title: title};
                        }],
                        deps: ['$ocLazyLoad', function ($ocLazyLoad) {
							$("html, body").removeClass("scrollHidden modal-open mob-hideModal").css({"paddingRight":""});
							$(".header").removeClass("nav-up");
                            return $ocLazyLoad.load([
                                'switchery',
                                'select'
                            ], {
                                insertBefore: '#lazyload_placeholder'
                            })
                                .then(function () {
                                    return $ocLazyLoad.load([
                                        'assets/js/controllers/place/show.js?rev='+_uuid4
                                    ]);
                                });
                        }]
                    }
                })

                .state('edit-profile', {
                    url: '/edit-profile',
                    templateUrl: 'tpl.edit-my-profile',
                    controller: 'EditProfileCtrl',
                    resolve: {
                        meta: ['$rootScope', function ($rootScope) {
                            var title = "SWOLK";
                            $rootScope.meta = {title: title};
                        }],
                        deps: ['$ocLazyLoad', function ($ocLazyLoad) {
                            $("html, body").removeClass("sidebar-open bodyHidden");
                            $("#sidebarOuter").removeClass("show");
							setTimeout(function () {
								$('.uploadProfilePicBtn input').on( "touchstart", function(){
									alert("1");
									$(this).trigger('mouseenter');
								});
								$('.uploadProfilePicBtn').on( "touchstart", function(){
									$(this).find("input").trigger('mouseenter');
								});
							}, 200);
                            return $ocLazyLoad.load([
                                'switchery',
                                'select',
                                'moment',
                                'datepicker',
                                'daterangepicker',
                                'timepicker',
                                'inputMask',
                                'autonumeric',
                                'wysihtml5',
                                'summernote',
                                'tagsInput',
                                'dropzone',
                                'typehead'
                            ], {
                                insertBefore: '#lazyload_placeholder'
                            })
                            .then(function () {
                                return $ocLazyLoad.load('assets/js/controllers/edit_profile.js?rev='+_uuid4);
                            });
                        }]
                    }
                })
                
                .state('feedmodal', {
                    url: "/feedmodal",
                    templateUrl: "tpl/feed-modal.html",
                    controller: 'ProfileCtrl',
                    resolve: {
                        meta: ['$rootScope', function ($rootScope) {
                            var title = "SWOLK";
                            $rootScope.meta = {title: title};
                        }],
                        deps: ['$ocLazyLoad', function ($ocLazyLoad) {
                            return $ocLazyLoad.load([
                                    'mapplic',
                                ], {
                                    insertBefore: '#lazyload_placeholder'
                                })
                                .then(function () {
                                    return $ocLazyLoad.load([
                                        'assets/js/controllers/profile.js?rev='+_uuid4
                                    ]);
                                });
                        }]
                    }
                })

                .state('explore', {
                    url: "/explore",
                    templateUrl: "tpl.explore",
                    controller: 'ExploreCtrl',
                    resolve: {
                        meta: ['$rootScope', function ($rootScope) {
                            var title = "Explore | SWOLK";
                            $rootScope.meta = {title: title};
                        }],
                        deps: ['$ocLazyLoad', function ($ocLazyLoad) {
                            $("html, body").removeClass("sidebar-open bodyHidden");
							$("#sidebarOuter").removeClass("show");
                            return $ocLazyLoad.load([
                                    'mapplic',
                                ], {
                                    insertBefore: '#lazyload_placeholder'
                                })
                                .then(function () {
                                    return $ocLazyLoad.load([
                                        'assets/js/controllers/explore.js?rev='+_uuid4
                                    ]);
                                });
                        }]
                    }
                })

                /*.state('test', {
                    url: "/explore-test",
                    templateUrl: "tpl.test",
                    controller: 'TestCtrl',
                    resolve: {
                        meta: ['$rootScope', function ($rootScope) {
                            var title = "Test | SWOLK";
                            $rootScope.meta = {title: title};
                        }],
                        deps: ['$ocLazyLoad', function ($ocLazyLoad) {
                            $("html, body").removeClass("sidebar-open bodyHidden");
                            $("#sidebarOuter").removeClass("show");
                            return $ocLazyLoad.load([
                                'mapplic',
                            ], {
                                insertBefore: '#lazyload_placeholder'
                            })
                                .then(function () {
                                    return $ocLazyLoad.load([
                                        'assets/js/controllers/test.js'
                                    ]);
                                });
                        }]
                    }
                })*/

                .state('allNotification', {
                    url: "/all-notification",
                    templateUrl: "tpl.allNotification",
                    controller: 'AllNotificationCtrl',
                    resolve: {
                        meta: ['$rootScope', function ($rootScope) {
                            var title = "All Notification | SWOLK";
                            $rootScope.meta = {title: title};
                        }],
                        deps: ['$ocLazyLoad', function ($ocLazyLoad) {
                            $("html, body").removeClass("sidebar-open bodyHidden");
                            $("#sidebarOuter").removeClass("show");
                            return $ocLazyLoad.load([
                                    'mapplic',
                                ], {
                                    insertBefore: '#lazyload_placeholder'
                                })
                                .then(function () {
                                    return $ocLazyLoad.load([
                                        'assets/js/controllers/allNotification.js?rev='+_uuid4
                                    ]);
                                });
                        }]
                    }
                })

                 .state('my-analytics', {  
                    url: "/my-analytics",
                    templateUrl: "tpl.my-analytics",
                    controller: 'analyticsCtrl',
                    resolve: {
                        meta: ['$rootScope', function ($rootScope) {   
                            var title = "Analytics | SWOLK";
                            $rootScope.meta = {title: title};
                        }],
                        deps: ['$ocLazyLoad', function ($ocLazyLoad) {
                            $("html, body").removeClass("sidebar-open bodyHidden");
                            $("#sidebarOuter").removeClass("show");
                            return $ocLazyLoad.load([
                                    'nvd3',
                                    'mapplic',
                                    'rickshaw',
                                    'metrojs',
                                    'sparkline',
                                    'skycons',
                                    'switchery'
                                ], {
                                    insertBefore: '#lazyload_placeholder'
                                })
                                .then(function () {
                                    return $ocLazyLoad.load([
                                        'assets/js/controllers/analytics.js?rev='+_uuid4
                                    ]);
                                });
                        }]
                    }
                })

				.state('search', {
                    url: '/search?q&ref',
                    templateUrl: "tpl.search",
                    controller: 'SearchCtrl',
                    reloadOnSearch: false,
                    resolve: {
                        meta: ['$rootScope', '$stateParams', function ($rootScope, $stateParams) {
                            if ($stateParams.q) {
                                var title = encodeURIComponent($stateParams.q).replace(/%20/g, '+');
                                title +=  " | SWOLK SEARCH";
                            }
                            else {
                                // category is placeholder for caption
                                var title = "Enter atleast 4 characters  | SWOLK SEARCH";
                            }
                            $rootScope.meta = {title: title};
                        }],
                        deps: ['$ocLazyLoad', function ($ocLazyLoad) {
                            $("html, body").removeClass("sidebar-open bodyHidden wh");
                            $("#sidebarOuter").removeClass("show");
                            /*return $ocLazyLoad.load([
                                'mapplic',
                            ], {
                                insertBefore: '#lazyload_placeholder'
                            })
                            .then(function () {
                                return $ocLazyLoad.load([
                                    'assets/js/controllers/search.js?rev='+_uuid4
                                ]);
                            });*/
                        }]
                    }
                })
				
				
				.state('following-topics', {
                    url: "/following-topics",
                    templateUrl: "tpl.following-topics",
                    controller: 'CategoryCtrl',
                    resolve: {
                        meta: ['$rootScope', function ($rootScope) {
                            var title = "Following Topics | SWOLK";
                            $rootScope.meta = {title: title};
                        }],
                        deps: ['$ocLazyLoad', function ($ocLazyLoad) {
                            $("html, body").removeClass("sidebar-open bodyHidden");
                            $("#sidebarOuter").removeClass("show");
                            return $ocLazyLoad.load([
                                    'mapplic',
                                ], {
                                    insertBefore: '#lazyload_placeholder'
                                })
                                .then(function () {
                                    return $ocLazyLoad.load([
                                        'assets/js/controllers/category.js?rev='+_uuid4
                                    ]);
                                });
                        }]
                    }
                })
                // Saved post..
                .state('saved-post', {
                    url: "/saved-post",
                    templateUrl: "tpl.saved-post",
                    controller: 'SavePostCtrl',
                    resolve: {
                        meta: ['$rootScope', function ($rootScope) {
                            var title = "Save Post | SWOLK";
                            $rootScope.meta = {title: title};
                        }],
                        deps: ['$ocLazyLoad', function ($ocLazyLoad) {
                            $("html, body").removeClass("sidebar-open bodyHidden");
                            $("#sidebarOuter").removeClass("show");
                            return $ocLazyLoad.load([
                                    'mapplic'
                                ], {
                                    insertBefore: '#lazyload_placeholder'
                                })
                                .then(function () {
                                    return $ocLazyLoad.load([
                                        'assets/js/controllers/savepost.js?rev='+_uuid4
                                    ]);
                                });
                        }]
                    }
                })
				// nearby..
				 .state('nearby', {
                    url: "/nearby",
                    templateUrl: "tpl.nearby",
                    controller: 'NearByCtrl',
                    resolve: {
                        meta: ['$rootScope', function ($rootScope) {
                            var title = "Nearby | SWOLK";
                            $rootScope.meta = {title: title};
                        }],
                        deps: ['$ocLazyLoad', function ($ocLazyLoad) {
                            $("html, body").removeClass("sidebar-open bodyHidden");
                            $("#sidebarOuter").removeClass("show");

                            // window.location.href = '/nearby'

                            return $ocLazyLoad.load([
                                    'mapplic'
                                ], {
                                    insertBefore: '#lazyload_placeholder'
                                })
                                .then(function () {
                                    return $ocLazyLoad.load([
                                        'assets/js/controllers/nearby.js?rev='+_uuid4
                                    ]);
                                });
                        }]
                    }
                })
				
				 // invite friend..
                .state('invite-friend', {
                    url: "/invite-friend",
                    templateUrl: "tpl.invite_friend",
                    controller: 'InviteFriendCtrl',
                    resolve: {
                        meta: ['$rootScope', function ($rootScope) {
                            var title = "Invite Friend | SWOLK";
                            $rootScope.meta = {title: title};
                        }],
                        deps: ['$ocLazyLoad', function ($ocLazyLoad) {
                            $("html, body").removeClass("sidebar-open bodyHidden");
                            $("#sidebarOuter").removeClass("show");
                            return $ocLazyLoad.load([
                                    'mapplic'
                                ], {
                                    insertBefore: '#lazyload_placeholder'
                                })
                                .then(function () {
                                    return $ocLazyLoad.load([
                                        'assets/js/controllers/invite_friend.js?rev='+_uuid4
                                    ]);
                                });
                        }]
                    }
                })

                // copy invite friend ..
                .state('send-feedback', {
                    url: "/send-feedback",
                    templateUrl: "tpl.send_feedback",
                    controller: 'FeedbackCtrl',
                    resolve: {
                        meta: ['$rootScope', function ($rootScope) {
                            var title = "Feedback | SWOLK";
                            $rootScope.meta = {title: title};
                        }],
                        deps: ['$ocLazyLoad', function ($ocLazyLoad) {
                            $("html, body").removeClass("sidebar-open bodyHidden");
                            $("#sidebarOuter").removeClass("show");
                            return $ocLazyLoad.load([
                                'mapplic'
                            ], {
                                insertBefore: '#lazyload_placeholder'
                            })
                                .then(function () {
                                    return $ocLazyLoad.load([
                                        'assets/js/controllers/feedback.js'
                                    ]);
                                });
                        }]
                    }
                })
				
				 // Error 404 
                .state('error-404', {
                    url: "/error-404",
                    templateUrl: "tpl.error_404",
                    resolve: {
                        meta: ['$rootScope', function ($rootScope) {
                            var title = "404 | SWOLK";
                            $rootScope.meta = {title: title};
                        }],
                        deps: ['$ocLazyLoad', function ($ocLazyLoad) {
                            $("html, body").removeClass("sidebar-open bodyHidden");
                            $("#sidebarOuter").removeClass("show");
                            return $ocLazyLoad.load([
                                    'mapplic'
                                ], {
                                    insertBefore: '#lazyload_placeholder'
                                });
                                
                        }]
                    }
                })
				
				// privacy policy..
                 .state('privacy-policy', {
                    url: "/privacy-policy",
                    templateUrl: "tpl.privacy_policy",
                    //controller: 'NearByCtrl',
                    resolve: {
                        meta: ['$rootScope', function ($rootScope) {
                            var title = "Privacy Policy | SWOLK";
                            $rootScope.meta = {title: title};
                        }],
                        deps: ['$ocLazyLoad', function ($ocLazyLoad) {
                            $("html, body").removeClass("sidebar-open bodyHidden");
                            $("#sidebarOuter").removeClass("show");
                            return $ocLazyLoad.load([
                                    'mapplic'
                                ], {
                                    insertBefore: '#lazyload_placeholder'
                                })
                                .then(function () {
                                    /*return $ocLazyLoad.load([
                                        
                                    ]);*/
                                });
                        }]
                    }
                })

                 // terms and conditions..
                 .state('terms-and-conditions', {
                    url: "/terms-and-conditions",
                    templateUrl: "tpl.terms-and-conditions",
                   // controller: 'NearByCtrl',
                    resolve: {
                        meta: ['$rootScope', function ($rootScope) {
                            var title = "Terms And Conditions | SWOLK";
                            $rootScope.meta = {title: title};
                        }],
                        deps: ['$ocLazyLoad', function ($ocLazyLoad) {
                            $("html, body").removeClass("sidebar-open bodyHidden");
                            $("#sidebarOuter").removeClass("show");
                            return $ocLazyLoad.load([
                                    'mapplic'
                                ], {
                                    insertBefore: '#lazyload_placeholder'
                                })
                                .then(function () {
                                    /*return $ocLazyLoad.load([
                                    
                                    ]);*/
                                });
                        }]
                    }
                });

            $locationProvider.html5Mode(true);
        }
    ]);

/*
 | ---------------------------------------------------------------------
 | <Tuhin Subhra Mandal> Load child stat
 | ---------------------------------------------------------------------
 */
angular.module('app').run(['$rootScope', '$state', '$stateParams', '$window', '$location', 'socket', 'localStorageService', function ($rootScope, $state, $stateParams, $window, $location, socket, localStorageService) {
    $rootScope.$state = $state;
    $rootScope.$stateParams = $stateParams;

    $rootScope.$on('$stateChangeStart', function(evt, to, params, fromState, fromParams, options) {
        /*Pace.stop();
        Pace.bar.render();*/
        // Remove paceDisable class from <body>
        $("body").removeClass('paceDisable');
        
        // Handle session start event.
        if (fromState.name === '') {
            localStorageService.remove('opened_posts');
        }
        
        if (to.redirectTo) {
            evt.preventDefault();
            $state.go(to.redirectTo, params, {location: 'replace'});
        }
        if (tsmPlayerPool != undefined) {
            tsmPlayerPool = [];
        }
    });
    $rootScope.$on('$stateChangeSuccess', function(evt, toState, toParams, fromState) {
        // Call modal close event if post details modal is open.
        var $detailModal = $('#myModal');
        if ($detailModal.is(':visible')) {
            $detailModal.modal('toggle');
        }
        // Hide search modal.
        $('#searchCloseBtn').trigger('click');
        // Broadcast post closed event.
        var post_id = sessionStorage.o_pid;
        if (post_id && toState.name != 'post-details') {
            var user_id = sessionStorage.o_uid ? sessionStorage.o_uid : null;
            var opened_post = {
                post_id: post_id,
                uuid: browserTabID,
                user_id: user_id,
                type: 'leave'
            }
            socket.emit('post closed', opened_post);
            sessionStorage.removeItem('o_pid');
            sessionStorage.removeItem('o_uid');
            // console.log('post closed');
        }

        $("body").animate({scrollTop: 0},600);
        // Add paceDisable class to <body> and disable for the page for future.
        setTimeout(function() {
            $("body").addClass('paceDisable');
        }, 20000);

    });
    
    $rootScope.$on('$stateChangeError', function(event, toState, toParams, fromState, fromParams, error) {        
        if (error.status == 401) {
            // Logout.
            document.cookie = "showHeroImage=; expires=Thu, 01 Jan 1970 00:00:00 UTC";
            $window.location.href = 'logout';
        }
        event.preventDefault();
    });
	$rootScope.sillyQA = function() {
        if($state.current.name === 'feed') {
            $state.go('feed', {}, { reload: true });
        }
    }

}]);

/* ============================================================
 * File: config.lazyload.js
 * Configure modules for ocLazyLoader. These are grouped by
 * vendor libraries.
 * ============================================================ */

angular.module('app')
    .config(['$ocLazyLoadProvider', function($ocLazyLoadProvider) {
        $ocLazyLoadProvider.config({
            debug: false,
            events: true,
            modules: [{
                    name: 'isotope',
                    files: [
                        'assets/plugins/imagesloaded/imagesloaded.pkgd.min.js',
                        'assets/plugins/jquery-isotope/isotope.pkgd.min.js'
                    ]
                }, {
                    name: 'codropsDialogFx',
                    files: [
                        'assets/plugins/codrops-dialogFx/dialogFx.js',
                        'assets/plugins/codrops-dialogFx/dialog.css',
                        'assets/plugins/codrops-dialogFx/dialog-sandra.css'
                    ]
                }, {
                    name: 'metrojs',
                    files: [
                        'assets/plugins/jquery-metrojs/MetroJs.min.js',
                        'assets/plugins/jquery-metrojs/MetroJs.css'
                    ]
                }, {
                    name: 'owlCarousel',
                    files: [
                        'assets/plugins/owl-carousel/owl.carousel.min.js',
                        'assets/plugins/owl-carousel/assets/owl.carousel.css'
                    ]
                }, {
                    name: 'noUiSlider',
                    files: [
                        'assets/plugins/jquery-nouislider/jquery.nouislider.min.js',
                        'assets/plugins/jquery-nouislider/jquery.liblink.js',
                        'assets/plugins/jquery-nouislider/jquery.nouislider.css'
                    ]
                }, {
                    name: 'nvd3',
                    files: [
                        'assets/plugins/nvd3/lib/d3.v3.js',
                        'assets/plugins/nvd3/nv.d3.min.js',
                        'assets/plugins/nvd3/src/utils.js',
                        'assets/plugins/nvd3/src/tooltip.js',
                        'assets/plugins/nvd3/src/interactiveLayer.js',
                        'assets/plugins/nvd3/src/models/axis.js',
                        'assets/plugins/nvd3/src/models/line.js',
                        'assets/plugins/nvd3/src/models/lineWithFocusChart.js',
                        'assets/plugins/angular-nvd3/angular-nvd3.js',
                        'assets/plugins/nvd3/nv.d3.min.css'
                    ],
                    serie: true // load in the exact order
                }, {
                    name: 'rickshaw',
                    files: [
                        'assets/plugins/nvd3/lib/d3.v3.js',
                        'assets/plugins/rickshaw/rickshaw.min.js',
                        'assets/plugins/angular-rickshaw/rickshaw.js',
                        'assets/plugins/rickshaw/rickshaw.min.css',
                    ],
                    serie: true
                }, {
                    name: 'sparkline',
                    files: [
                    'assets/plugins/jquery-sparkline/jquery.sparkline.min.js',
                    'assets/plugins/angular-sparkline/angular-sparkline.js'
                    ]
                }, {
                    name: 'mapplic',
                    files: [
                        'assets/plugins/mapplic/js/hammer.js',
                        'assets/plugins/mapplic/js/jquery.mousewheel.js',
                        'assets/plugins/mapplic/js/mapplic.js',
                        'assets/plugins/mapplic/css/mapplic.css'
                    ]
                }, {
                    name: 'skycons',
                    files: ['assets/plugins/skycons/skycons.js']
                }, {
                    name: 'switchery',
                    files: [
                        'assets/plugins/switchery/js/switchery.min.js',
                        'assets/plugins/ng-switchery/ng-switchery.js',
                        'assets/plugins/switchery/css/switchery.min.css',
                    ]
                }, {
                    name: 'menuclipper',
                    files: [
                        'assets/plugins/jquery-menuclipper/jquery.menuclipper.css',
                        'assets/plugins/jquery-menuclipper/jquery.menuclipper.js'
                    ]
                }, {
                    name: 'wysihtml5',
                    files: [
                        'assets/plugins/bootstrap3-wysihtml5/bootstrap3-wysihtml5.min.css',
                        'assets/plugins/bootstrap3-wysihtml5/bootstrap3-wysihtml5.all.min.js'
                    ]
                }, {
                    name: 'stepsForm',
                    files: [
                        'assets/plugins/codrops-stepsform/css/component.css',
                        'assets/plugins/codrops-stepsform/js/stepsForm.js'
                    ]
                }, {
                    name: 'jquery-ui',
                    files: ['assets/plugins/jquery-ui-touch/jquery.ui.touch-punch.min.js']
                }, {
                    name: 'moment',
                    files: ['assets/plugins/moment/moment.min.js',
                        'assets/plugins/moment/moment-with-locales.min.js'
                    ]
                }, {
                    name: 'moment-locales',
                    files: ['assets/plugins/moment/moment-with-locales.min.js'
                    ]
                }, {
                    name: 'hammer',
                    files: ['assets/plugins/hammer.min.js']
                }, {
                    name: 'sieve',
                    files: ['assets/plugins/jquery.sieve.min.js']
                }, {
                    name: 'line-icons',
                    files: ['assets/plugins/simple-line-icons/simple-line-icons.css']
                }, {
                    name: 'ionRangeSlider',
                    files: [
                        'assets/plugins/ion-slider/css/ion.rangeSlider.css',
                        'assets/plugins/ion-slider/css/ion.rangeSlider.skinFlat.css',
                        'assets/plugins/ion-slider/js/ion.rangeSlider.min.js'
                    ]
                }, {
                    name: 'navTree',
                    files: [
                        'assets/plugins/angular-bootstrap-nav-tree/abn_tree_directive.js',
                        'assets/plugins/angular-bootstrap-nav-tree/abn_tree.css'
                    ]
                }, {
                    name: 'nestable',
                    files: [
                        'assets/plugins/jquery-nestable/jquery.nestable.css',
                        'assets/plugins/jquery-nestable/jquery.nestable.js',
                        'assets/plugins/angular-nestable/angular-nestable.js'
                    ]
                }, {
                    //https://github.com/angular-ui/ui-select
                    name: 'select',
                    files: [
                        'assets/plugins/bootstrap-select2/select2.css',
                        'assets/plugins/angular-ui-select/select.min.css',
                        'assets/plugins/angular-ui-select/select.min.js'
                    ]
                }, {
                    name: 'datepicker',
                    files: [
                        'assets/plugins/bootstrap-datepicker/css/datepicker3.css',
                        'assets/plugins/bootstrap-datepicker/js/bootstrap-datepicker.js',
                    ]
                }, {
                    name: 'daterangepicker',
                    files: [
                        'assets/plugins/moment/moment.min.js',
                        'assets/plugins/bootstrap-daterangepicker/daterangepicker-bs3.css',
                        'assets/plugins/bootstrap-daterangepicker/daterangepicker.js',
                        'assets/plugins/angular-daterangepicker/angular-daterangepicker.min.js'
                    ],
                    serie: true
                }, {
                    name: 'timepicker',
                    files: [
                        'assets/plugins/bootstrap-timepicker/bootstrap-timepicker.min.css',
                        'assets/plugins/bootstrap-timepicker/bootstrap-timepicker.min.js'
                    ]
                }, {
                    name: 'inputMask',
                    files: [
                        'assets/plugins/jquery-inputmask/jquery.inputmask.min.js'
                    ]
                }, {
                    name: 'autonumeric',
                    files: [
                        'assets/plugins/jquery-autonumeric/autoNumeric.js'
                    ]
                }, {
                    name: 'summernote',
                    files: [
                        'assets/plugins/summernote/css/summernote.css',
                        'assets/plugins/summernote/js/summernote.min.js',
                        'assets/plugins/angular-summernote/angular-summernote.min.js'
                    ],
                    serie: true // load in the exact order
                }, {
                    name: 'tagsInput',
                    files: [
                        'assets/plugins/bootstrap-tag/bootstrap-tagsinput.css',
                        'assets/plugins/bootstrap-tag/bootstrap-tagsinput.min.js'
                    ]
                }, {
                    name: 'dropzone',
                    files: [
                        'assets/plugins/dropzone/css/dropzone.css',
                        'assets/plugins/dropzone/dropzone.min.js',
                        'assets/plugins/angular-dropzone/angular-dropzone.js'
                    ],
                    serie: true
                }, {
                    name: 'wizard',
                    files: [
                        'assets/plugins/lodash/lodash.min.js',
                        'assets/plugins/angular-wizard/angular-wizard.min.css',
                        'assets/plugins/angular-wizard/angular-wizard.min.js'
                    ]
                }, {
                    name: 'dataTables',
                    files: [
                        'assets/plugins/jquery-datatable/media/css/dataTables.bootstrap.min.css',
                        'assets/plugins/jquery-datatable/extensions/FixedColumns/css/dataTables.fixedColumns.min.css',
                        'assets/plugins/datatables-responsive/css/datatables.responsive.css',
                        'assets/plugins/jquery-datatable/media/js/jquery.dataTables.min.js',
                        'assets/plugins/jquery-datatable/extensions/TableTools/js/dataTables.tableTools.min.js',
                        'assets/plugins/jquery-datatable/media/js/dataTables.bootstrap.js',
                        'assets/plugins/jquery-datatable/extensions/Bootstrap/jquery-datatable-bootstrap.js',
                        'assets/plugins/datatables-responsive/js/datatables.responsive.js',
                        'assets/plugins/datatables-responsive/js/lodash.min.js'
                    ],
                    serie: true // load in the exact order
                }, {
                    name: 'google-map',
                    files: [
                        'assets/plugins/angular-google-map-loader/google-map-loader.js',
                        'assets/plugins/angular-google-map-loader/google-maps.js'
                    ]
                },  {
                    name: 'interact',
                    files: [
                        'assets/plugins/interactjs/interact.min.js'
                    ]
                }, {
                    name: 'tabcollapse',
                    files: [
                        'assets/plugins/bootstrap-collapse/bootstrap-tabcollapse.js'
                    ]
                },  {
                    name: 'ui-grid',
                    files: [
                        'assets/plugins/angular-ui-grid/ui-grid.min.css',
                        'assets/plugins/angular-ui-grid/ui-grid.min.js']

                },{
                    name: 'typehead',
                    files: [
                        'assets/plugins/angular-typehead/typeahead.bundle.min.js',
                        'assets/plugins/angular-typehead/angular-typeahead.min.js'
                    ]
                },{
                    name: 'js-tag',
                    files: [
                        'assets/plugins/angular-js-tags/jsTag.js',
                        'assets/plugins/angular-js-tags/jsTag.css'
                    ]
                },{
                    name: 'angular-froala',
                    files: [
                        'assets/plugins/froala/css/froala_editor.min.css',
                        // 'assets/plugins/froala/css/froala_style.css',
                        'assets/plugins/froala/css/plugins/image.min.css',
                        'assets/plugins/froala/css/plugins/video.min.css',
                        'assets/plugins/froala/css/plugins/line_breaker.min.css',
                        'assets/plugins/froala/css/plugins/quick_insert.min.css',
                        'assets/plugins/froala/css/plugins/code_view.min.css',
                        'assets/plugins/froala/css/plugins/fullscreen.min.css',
                        
                        'assets/plugins/froala/angular/src/angular-froala.js',
                    ]
                }

            ]
        });
    }]);

/* ============================================================
 * File: main.js
 * Main Controller to set global scope variables.
 * Directives, Services, Filters.
 * ============================================================ */

angular.module('app')
    .factory('ajaxService', ["$http", function ($http) {
        return {
            serverCall: function (url, data) {
                return $http.post(url, data).then(function (response) {
                    return response.data;
                });
            }

        };
    }])
    .controller('AppCtrl', ['$scope', '$http','$location', '$rootScope', '$state', '$stateParams', '$sce', '$filter', '$timeout', '$interval', '$location', '$window', '$anchorScroll', 'refreshService', 'userDataService', 'YT_event', 'socket', 'postOpened', 'localStorageService', 'haversineDistanceCalculationService', function ($scope, $http,$location, $rootScope, $state, $stateParams, $sce, $filter, $timeout, $interval, $location, $window, $anchorScroll, refreshService, userDataService, YT_event, socket, postOpened, localStorageService, haversineDistanceCalculationService) {


        $scope.checkUrl=function ()
        {
            var urlArray = $location.path().split("/");
          
           
            if(urlArray[1]!='post-add')
            {
             $scope.checkUrlParam=urlArray[1];
            // alert($scope.checkUrlParam);
            }

             if(urlArray[1]=='tag' || urlArray[1]=='questions')
                {
                    var tagName=urlArray[2]
                    var url = '/api/tag-details/' + tagName;
                    $http.get(url).then(function (response) {
                            $scope.urlParamText=response.data.tag_text;
                                 
                                
                    });
                }
                else if(urlArray[1]=='place')
                {
                        function getParameterByName(name)
                        {
                            url=document.URL;
                           
                            name = name.replace(/[\[\]]/g, "\\$&");
                            var regex = new RegExp("[?&]" + name + "(=([^&#]*)|&|#|$)"),
                                results = regex.exec(url);
                            if (!results) return null;
                            if (!results[2]) return '';
                            return decodeURIComponent(results[2].replace(/\+/g, " "));
                        }

                        var address='';
                        if(getParameterByName('location'))
                        {
                        
                            address=getParameterByName('location');
                        }
                        else if(getParameterByName('city'))
                        {
                        
                            address=getParameterByName('city');
                        }
                        else if(getParameterByName('state'))
                        {
                        
                            address=getParameterByName('state');
                        }
                        else if(getParameterByName('country'))
                        {
                        
                            address=getParameterByName('country');
                        }
                        else if(getParameterByName('region'))
                        {
                        
                            address=getParameterByName('region');
                        }
                        else if(getParameterByName('continent'))
                        {
                        
                            address=getParameterByName('continent');
                        }


                         $scope.urlParamText=address;
                }



            
        }


        
        // App globals
        $scope.app = {
            name: 'Swolk',
            description: 'Social networking website',
            layout: {
                menuPin: false,
                menuBehind: false,
                theme: 'assets/pages/css/pages.css',
                // paceThemeFlash: 'assets/plugins/pace/pace-theme-flash.css',
                bootstrap: 'assets/plugins/bootstrapv3/css/bootstrap.min.css',
                fontAwesome: 'assets/plugins/font-awesome/css/font-awesome.css',
                jqueryScrollbar: 'assets/plugins/jquery-scrollbar/jquery.scrollbar.css',
                pagesIcons: 'assets/pages/css/pages-icons.css',
                customCss: 'assets/pages/css/custom.css',
                owlCarousel: 'assets/plugins/carousel/owl.carousel.css',
                slickCarousel: 'assets/plugins/slick-carousel/slick.css',
                // Othres
                ie9: 'assets/pages/css/ie9.css',
                mapplicIe: 'assets/plugins/mapplic/css/mapplic-ie.css',
                chromeFix: 'assets/pages/css/windows.chrome.fix.css',

            },
            author: 'tuhin.tsm.mandal@gmail.com'

        };

        $scope.mySwitch = false;
        $scope.masonryColumnWidth = '350px';
        //$scope.current_state=$state.current.name;
        //console.log($scope.current_state);


        // User Background Colors
        $scope.usercolors = [
            "#a12c3e",
            "#c73935",
            "#ad2a76",
            "#dc6092",
            "#bfb8da",
            "#1e3a6c",
            "#f5c5d1",
            "#e68f74",
            "#ed8958",
            "#efae6a",
            "#224b3d",
            "#5b1434",
            "#f0ce74",
            "#f9f4bc",
            "#f8dd72",
            "#d6ce83",
            "#4f295a",
            "#76804e",
            "#adca6e",
            "#adca6e",
            "#7ca954",
            "#95cccf",
            "#4385a7",
            "#4295a3",
            "#c9e2cd",
            "#8f5494"
        ];

        // Constants..
        $scope.default_profile_img = 'assets/img/default-profile.png';
        $scope.default_post_img = 'assets/img/post-placeholder.png';
        $scope.default_post_vid = 'assets/img/video_placeholder.jpg';
        $scope.default_cover_img = 'assets/img/default-cover-img.jpg';
        $scope.loadMoreCommentsLimit = 10;

        // Initializing as object to shared through child controllers.
        $scope.commonData = {};

        $scope.userFollowing = [];
        $scope.userFollower = [];
        $scope.isFollowingCategory = [];

        $scope.loggedIn = false;

        $http.get('angular/auth-json').then(function (response) {
            if (response.data && response.data.id > 0) {
                $scope.root_url = ROOT_URL;
                $scope.user = response.data;
                // invoke initialy and load 10 notifications.
                if ($scope.user.guest == 0) {
                    $scope.loadNotifications(1, 10);
                    $scope.loggedIn = true;
                    // Listen for user activity.
                    $scope.emitUserOpened($scope.user.id);
                }

                $scope.userProfileview = response.data.userProfileview;
                $scope.userTotalPost = response.data.userTotalPost;

                $scope.userFollowingData = $scope.user.following;
                $scope.userFollowerData = $scope.user.follower;

                if (angular.isArray($scope.userFollowingData)) {
                    angular.forEach($scope.userFollowingData, function (value, key) {
                        $scope.userFollowing.push(parseInt(value.user_id));

                    });
                }
                if (angular.isArray($scope.userFollowerData)) {
                    angular.forEach($scope.userFollowerData, function (value, key) {
                        $scope.userFollower.push(parseInt(value.user_id));

                    });
                }

                if (angular.isArray($scope.user.category_follow)) {
                    angular.forEach($scope.user.category_follow, function (value, key) {
                        $scope.isFollowingCategory.push(parseInt(value.category_id));
                    });
                }

                if (angular.isArray($scope.user.category_follow)) {
                    angular.forEach($scope.user.category_follow, function (value, key) {
                        $scope.isFollowingCategory.push(parseInt(value.category_id));
                    });
                }
            }

        }, function (response) {
            // $scope.logout();
        });

        $scope.showStatus = function (tags) {
            res=false;
            tags.forEach(function(tag) {
              //  alert(tag.tag_name);
                if(tag.question_tag_created_at)
                {
                
                    if( post.created_at < tag.question_tag_created_at)
                    {
                        res=true; 
                        return res;
                    }
                }
                else
                {
                    res=true;
                    return res;
                }

            

               });
               
           return res;
        };



        /*------ Logout From the app ------*/
        $scope.logout = function () {
            $scope.loggedIn = false;
            localStorageService.remove('userLocationSaved');
            document.cookie = "showHeroImage=; expires=Thu, 01 Jan 1970 00:00:00 UTC";
            $window.location.href = 'logout';
        };

        $scope.refreshUserData = function() {
            refreshService.userData().then(function(data){
                $scope.user = data;

                $scope.userProfileview = data.userProfileview;
                $scope.userTotalPost = data.userTotalPost;

                $scope.userFollowingData = $scope.user.following;
                $scope.userFollowerData = $scope.user.follower;


                if (angular.isArray($scope.userFollowingData)) {
                    var userFollowing = [];
                    angular.forEach($scope.userFollowingData, function (value, key) {
                        userFollowing.push(parseInt(value.user_id));
                    });
                    $scope.userFollowing = userFollowing;
                }
                if (angular.isArray($scope.userFollowerData)) {
                    var userFollower = [];
                    angular.forEach($scope.userFollowerData, function (value, key) {
                        userFollower.push(parseInt(value.user_id));
                    });
                    $scope.userFollower = userFollower;
                }
            });
        };

        $scope.openPostPage = function (post) {
            var id = $stateParams.id;
            if ($state.current.name != 'post-details' || id != post.child_post_id)
                $window.open(post.post_url, '_blank');
        };

        /*$scope.$on(YT_event.STATUS_CHANGE, function(event, data) {
            console.log(data);
        });*/

        //*=================== Refresh common contents ====================*//
        $scope.notifications = [];
        var notificationLoaded = [];
        $scope.loadNotifiBusy = false;
        $scope.noMoreloadNotifi = false;
        /**
         * Loads and populates the notifications
         */
        $scope.loadNotifications = function (page, perpage, type) {
            if ($scope.loadNotifiBusy) {
                return;
            }

            if (type == 'loadMore') {
                // Stop loading more notification through infinite scroll.
                if ($scope.noMoreloadNotifi == true) {
                    return;
                }
                $scope.loadNotifiBusy = true;
            }
            refreshService.notification(page, perpage).then(function (data) {
                var uniqueNotifiID;

                data.notifications.forEach(function (notification, index) {
                    uniqueNotifiID = notification.id + '-' + notification.activity_id;
                    // Push to notifications if not already available.
                    if (notificationLoaded.indexOf(uniqueNotifiID) == -1) {
                        $scope.notifications.push(notification);
                        notificationLoaded.push(uniqueNotifiID);
                    }
                });

                // $scope.notifications = data.notifications;
                $scope.total_notifications = data.total_notifications;
                $scope.loadNotifiBusy = false;
                if (type == 'loadMore') {
                    $scope.notificationPopUpOpen();
                    // Stop showing loader, no more notification for loadMore.
                    if (data.notifications.length > 0) {
                        $scope.noMoreloadNotifi = true;
                    }
                }
            }, function (error) {
                $scope.loadNotifiBusy = false;
            });
        };

        // Trigger every 30 seconds for logged in user.
        $interval(function () {
            if ($scope.user && $scope.user.guest == 0) {
                $scope.loadNotifications(1, 10);
                $scope.refreshUserData();
            }
        }, 30000);
        // invoke initially and load 10 notifications.
        // $scope.loadNotifications(1, 10);

        // Load more notification through infinite scrolling.
        var notificationPage = 1;
        var perpage = 10;
        var isFirstLoadMore = true;
        var type = 'loadMore';
        $scope.loadMoreNotification = function (calledFrom) {
            // Limit max 5 page fetch on popup or prevent for first load more from notification.
            if (calledFrom == 'notification' && (notificationPage > 5 || isFirstLoadMore)) {
                isFirstLoadMore = false;
                return;
            }
            if ($scope.loadNotifiBusy) {
                return;
            }


            $scope.loadNotifications(notificationPage, perpage, type);
            notificationPage++;
        };

        $scope.notificationPopUpOpen = function () {
            /* 
             * Old Code >> Don not DELETE!!
             var notifi = [];
             var n = {};
             angular.forEach($scope.notifications, function(notification, i) {
             if (notification.status != 1) {
             return;
             }
             // Reset object.
             n = {};
             n.id = notification.id;
             n.activity_id = notification.activity_id;
             n.type = notification.type;
             n.total = notification.total;

             if ("comment_id" in notification) {
             n.comment_id = notification.comment_id;
             }
             notifi.push(n);
             });
             var postData = {
             notifi: notifi
             };
             */
            // console.log(postData);
            // Mark popup notifications as seen.
            var url = 'api/mark-notification-seen';
            $http.post(url/*, postData*/)
                .then(function (response) {
                        $scope.loadNotifications();
                    },
                    function (response) {

                    }
                );
        };

        // Mark notification as read.
        $scope.markNotificationRead = function (notification, clear_type, index) {
            /*--- Notifications stays.. ---*/
            // Remove the notification from popup.
            /*setTimeout(function() {
             $scope.notifications.splice(index,1);
             }, 500);*/
            // For notification popup.
            // No need for api call as opening popup will clear
            if (clear_type == 'all') {
                notification.status = 3;
                $timeout(function () {
                    /*--- Notifications stays.. ---*/
                    // $scope.notifications.splice(index,1);
                    $scope.loadNotifications();
                }, 1200);
                return;
            }
            // For all notifications page.
            // No need for api call as opening popup will clear
            else if (clear_type == 'allNotifi') {
                // Make notification as read.
                $('.allNotifi-' + notification.post.id).addClass('notification-read');
                $timeout(function () {
                    $scope.loadNotifications();
                }, 800);
                return;
            }
            else if (clear_type == 'single_ac') {
                // For all notifications page only.
                $('#allNotifi-' + index).addClass('notification-read');
            }

            var url = 'api/mark-notification-read';
            var id = notification.id;
            var total = notification.total;
            var type = notification.type;
            var activity_id = notification.activity_id;

            var postData = {
                id: id,
                total: total,
                type: type,
                activity_id: activity_id,
                clear_type: clear_type
            };
            $http.post(url, postData)
                .then(function (response) {
                        $scope.loadNotifications();
                    },
                    function (response) {
                        $scope.loadNotifications();
                    }
                );
        };

        // Get notification text.
        $scope.getNotificationMsg = function (notification) {
            var message, notificationTotal;
            // Add user first name
            message = $filter('cut')(notification.user.first_name, true, 25);
            // For upvote and downvote.
            if ([1, 2, 3, 4, 5, 6, 7, 12, 13].indexOf(Number(notification.activity_id)) != -1) {
                if (notification.total > 1) {
                    notificationTotal = (notification.total - 1);
                    if (notificationTotal > 9999) {
                        notificationTotal = $filter('thousandSuffix')(notificationTotal);
                    }
                    message += ' and ' + notificationTotal;
                    message += notification.total == 2 ? ' other' : ' others';
                }
                if (notification.activity_id == 1) {
                    if (notification.type == 'comment' || notification.type == 'reply') {
                        message += ' upvote your comment on';
                    }
                    else {
                        message += ' upvote';
                    }
                }
                else if (notification.activity_id == 2) {
                    if (notification.type == 'comment' || notification.type == 'reply') {
                        message += ' downvote your comment on';
                    }
                    else {
                        message += ' downvote';
                    }
                }
                else if (
                    notification.activity_id == 3 ||
                    notification.activity_id == 4 ||
                    notification.activity_id == 5 ||
                    notification.activity_id == 6
                ) {
                    message += ' shared';
                }
                else if (notification.activity_id == 7) {
                    message += ' comment on';
                }
                else if (notification.activity_id == 12) {
                    message += ' replied to your comment on';
                }
                else if (notification.activity_id == 13) {
                    message += ' follows you';
                }
            }
            // Skip for other than post related notifications.
            var postRelatedActivities = [1, 2, 3, 4, 5, 6, 7, 12];
            if (postRelatedActivities.indexOf(Number(notification.activity_id)) != -1) {
                var postName = '';
                if (notification.post.title) {
                    postName = notification.post.title;
                }
                else if (notification.post.post_type == 5 && notification.post.orginal_post) {
                    postName = notification.post.orginal_post.caption;
                }
                else {
                    postName = notification.post.caption;
                }
                // Add post title.
                message += '<i title="' + postName + '"> ' + $filter('cut')(postName, true, 25) + '</i>';
            }

            if (notification.activity_id == 4) {
                message += ' to facebook';
            }
            else if (notification.activity_id == 5) {
                message += ' to twitter';
            }
            else if (notification.activity_id == 6) {
                message += ' to linkedin';
            }

            message = $sce.trustAsHtml(message);
            return message;
        };

        /*
         * Code for web socket
         * @author Tuhin Subhra Mandal
         */
        // Emit user opened event.
        $scope.emitUserOpened = function (user_id) {
            var data = {
                user: {
                    id: user_id
                }
            };
            socket.emit('user opened', data);
        }
        // Emit post opened event.
        $scope.emitPostOpened = function (post, type) {
            var user_id = $scope.user && $scope.user.id > 1 ? $scope.user.id : null;
            var post_id = post.id;
            var relUserIds = [];
            if (post.getUser) {
                post.getUser.forEach(function (user) {
                    relUserIds.push(user.id);
                });
            }
            var opened_post = {
                post_id: post_id,
                relUserIds: relUserIds,
                uuid: browserTabID,
                user_id: user_id,
                type: type
            }
            socket.emit('post opened', opened_post);
        };
        // Emit post closed event.
        $scope.emitPostClosed = function (post_id, type) {
            var user_id = $scope.user && $scope.user.id > 1 ? $scope.user.id : null;
            var opened_post = {
                post_id: post_id,
                uuid: browserTabID,
                user_id: user_id,
                type: type
            }
            socket.emit('post closed', opened_post);
        };
        // Listen to total people here event.
        $scope.listenTotalPeopleEvent = function (callback) {
            socket.on('total_people_here', function (data) {
                callback(data.total);
            });
        };
        /* END Code for web socket */

        $scope.openProfileFollow = function (src, index) {
            $scope.total_notifications = $scope.total_notifications - 1;
            $('#notifi-' + index).addClass('notification-read');
            // $location.path('/profile').search('src', src);
            $state.go('profile', {src: src}, {reload: true});
            /*--- Notifications stays.. ---*/
            /*setTimeout(function () {
             $scope.notifications.splice(index, 1);
             }, 1000);*/

        };

        //*=================================================================*//
        /*----------------------------------------------------------------------*/
        $scope.mySmNavClk = function () {
            if (!angular.element("#sidebarOuter").hasClass("show")) {
                angular.element("html, body").addClass("sidebar-open bodyHidden");
                //angular.element(".page-sidebar").addClass("visible");
                angular.element("#sidebarOuter").addClass("show");
                angular.element(".page-sidebar").addClass("open");
            } else {
                angular.element("html, body").removeClass("sidebar-open");
                // angular.element(".page-sidebar").removeClass("visible");
                angular.element("#sidebarOuter").removeClass("show");
                setTimeout(function () {
                    angular.element(".page-sidebar").removeClass("open");
                    angular.element("html, body").removeClass("bodyHidden");
                }, 600);
            }
        };
        $scope.navsmClose = function () {
            angular.element("body").on("click", "#sidebarOuter", function () {
                angular.element("html, body").removeClass("sidebar-open");
                //angular.element(".page-sidebar").removeClass("visible");
                angular.element("#sidebarOuter").removeClass("show");
                setTimeout(function () {
                    angular.element(".page-sidebar").removeClass("open");
                    angular.element("html, body").removeClass("bodyHidden");
                }, 600);
            });
        };

        // Checks if the given state is the current state
        $scope.is = function (name) {
            return $state.is(name);
        };

        // Checks if the given state/child states are present
        $scope.includes = function (name) {
            return $state.includes(name);
        };

        $scope.closeOverLayout = function () {
            //$scope.popUpDropdrow = 0;
            // $scope.popUpReportDropdrow = 0;
            $(".subOverlaysh , .otherSubsh").hide();


        };

        $scope.bookmark = function (postID, type) {

            // M : Modal ,P:  Postcard

            if (angular.isArray($scope.commonData.allPosts)) {
                var isChange = 0;
                angular.forEach($scope.commonData.allPosts, function (value, key) {
                    if ($scope.commonData.allPosts[key].id == postID) {

                        if ($scope.commonData.allPosts[key].isBookMark == 'Y') {
                            $scope.commonData.allPosts[key].isBookMark = 'N';
                            // For left panel update total book marks
                            // $scope.user.totalBookMarks= $scope.user.totalBookMarks-1;
                            isChange = 1;
                        } else {
                            $scope.commonData.allPosts[key].isBookMark = 'Y';
                            // For left panel update total book marks
                            // $scope.user.totalBookMarks= $scope.user.totalBookMarks+1;
                            isChange = 2;
                        }
                        return;
                    }
                });

                //
                if (isChange == 1) {
                    $scope.user.totalBookMarks = $scope.user.totalBookMarks - 1;
                }
                if (isChange == 2) {
                    $scope.user.totalBookMarks = $scope.user.totalBookMarks + 1;
                }
            }
            if (type == 'M') {  // Dynamick binding  for  postcard modal

                if ($scope.post.isBookMark == 'Y') {
                    $scope.post.isBookMark = 'N';
                    $scope.post.totalBookMark = $scope.post.totalBookMark - 1; // post total bookmark
                    $scope.user.totalBookMarks = $scope.user.totalBookMarks - 1; // user  total bookmark
                } else {
                    $scope.post.isBookMark = 'Y';
                    $scope.post.totalBookMark = $scope.post.totalBookMark + 1; // post total book mark
                    $scope.user.totalBookMarks = $scope.user.totalBookMarks + 1; // user  total bookmark
                }
            }

            $http({
                method: 'POST',
                url: "angular/bookmark",
                data: {postID: postID}
            }).then(function (response) {

            }, function (error) {
                console.log('Sorry! we cannot process your request.');
            });

        }

        // Enable scroll lock on notification popup & prevent body scrolling when scrolling notification.

        $scope.notifiScrollLock = function () {
            $("#notifiBody").scrollLock();
        };

        // Broadcasts a message to pgSearch directive to toggle search overlay
        $scope.showSearchOverlay = function () {
            $scope.$broadcast('toggleSearchOverlay', {
                show: true
            });
        };

        $scope.hidePostFilter = false;
        $scope.removeFlikityLoader = false;

        $scope.postTypeNavItems = [
            {
                name: 'All',
                value: 'all'
            },
            {
                name: 'Status',
                value: 5
            },
            {
                name: 'Image',
                value: 1
            },
            {
                name: 'Video',
                value: 2
            },
            {
                name: 'link',
                value: 4
            },
            {
                name: 'Article',
                value: 3
            },
            {
                name: 'Question',
                value: 6
            },
           
        ];

        $scope.flickityTagOptions = {
            prevNextButtons: false,
            pageDots: false,
            wrapAround: true,
            contain: true,
			cellAlign: 'left'
            //freeScroll: true,
        };

        // Calculate post card total column width.
        var positionCards = function () {
            // No need for mobile.
            if ($(window).width() < 567)
                return;
            var $blockContent = $(".blockContent");
            // var $blockContent = $(".masonry-brick");

            var totalPostWidth = 0;
            var loopLimit = 15;
            var post_card_containerWidth = $(".post_card_container").innerWidth();

            if ($blockContent.length) {
                $blockContent.each(function () {
                    if (!loopLimit) {
                        return;
                    }
                    loopLimit--;
                    if ($(this).css('top') == '0px') {
                        totalPostWidth += $(this).innerWidth();
                    }
                });

                var widthDiff = (post_card_containerWidth - totalPostWidth) / 2;

                var $blckCntParent = $("#blckCntParent");
                $blckCntParent.css({"left": widthDiff});
            }
        };

        $("body").on('click', '.modal-body .customPlayPause', function (e) {
            var videoDomObj = $(this).parent().children().children("video").get(0);
            if (videoDomObj.paused) {
                videoDomObj.play();
                $(this).parent().children(".customPlayPause").addClass("out");
            }
        });
        $("body").on('click', '.modal-body .videoTag', function (e) {
            var videoDomObj2 = $(this).parent().children("video").get(0);
            if (videoDomObj2.paused) {
                videoDomObj2.play();
                $(this).parent().parent().children(".customPlayPause").addClass("out");
            } else {
                videoDomObj2.pause();
                //$(this).parent().parent().children(".customPlayPause").removeClass("out");
            }
        });

        var positionCardTimer;
        /* 
         * Start positioning cards.
         * @param time int -- time in milliseconds 
         */
        $scope.startPositionCards = function (time) {
            positionCards();
            if (!time) {
                time = 300;
            }
            positionCardTimer = $interval(function () {
                positionCards();
            }, time);
        };
        /*
         * Stop positioning cards in interval.
         */
        $scope.stopPositionCards = function (time) {
            if (!time) {
                time = 2000;
            }
            setTimeout(function () {
                $interval.cancel(positionCardTimer);
            }, time);
        }

        var reloadCardMasonryTimer;
        /* 
         * Start masonry reloads.
         * @param time int -- time in milliseconds 
         */
        $scope.startReloadCardMasonry = function (time) {
            // No need for mobile.
            if ($(window).width() < 600)
                return;
            // Reload at first.
            $rootScope.$broadcast('masonry.reload');
            if (!time) {
                time = 300;
            }
            reloadCardMasonryTimer = $interval(function () {
                $rootScope.$broadcast('masonry.reload');
            }, time);
        };

        /*
         * Stop positioning cards in interval.
         */
        $scope.stopReloadCardMasonry = function (time) {
            if (!time) {
                time = 2100;
            }
            $timeout(function () {
                $interval.cancel(reloadCardMasonryTimer);
            }, time);
        }

        setTimeout(function () {
            positionCards();
            // $rootScope.$broadcast('masonry.reload');
        }, 100);
        /*-------------------------------------------*/
        /*$interval(function () {
         $rootScope.$broadcast('masonry.reload');
         }, 300);*/
        /*=============================================*/

        /* Call after resizing is done. */
        var isCalled = false;
        var resizeId;
        $(window).resize(function () {
            if (!isCalled) {
                positionCards();
                isCalled = true;
            }
            clearTimeout(resizeId);
            resizeId = setTimeout(doneMyResizing, 500);
        });
        function doneMyResizing() {
            isCalled = false;
            setTimeout(function () {
                positionCards();
            }, 100);
        }

        /*----------------------------------------------------------------------*/

        $scope.showLocation = function (location) {
            var formatted_location = '';
            if (location) {
                var charLimit = 14;
                var indexOfComma = location.indexOf(',');
                // comma not found
                if (indexOfComma === -1) {
                    if (location.length > charLimit) {
                        formatted_location = location.substring(0, charLimit) + "..";
                    }
                    else {
                        formatted_location = location;
                    }
                }
                else {
                    location = location.substring(0, indexOfComma);
                    if (location.length > charLimit) {
                        formatted_location = location.substring(0, charLimit) + "..";
                    }
                    else {
                        formatted_location = location;
                    }
                }
            }
            return formatted_location;
        };

        $scope.showPostViewTxt = function (post, referrer) {
            if (!post) {
                return '';
            }
            else if (post.post_type == 1 || post.post_type == 5 || post.post_type == 6) {
                if (referrer == 'card') {
                    return 'seen';
                }
                return 'viewed';
            }
            else if (post.post_type == 2) {
                return 'played';
            }
            else if (post.post_type == 3) {
                return 'read';
            }
            else if (post.post_type == 4) {
                return 'linked';
            }
        };

        $scope.redirecToLogin = function () {
            $('#signInNotifi, .signInoverlay, .loginDropWrap').css('display','block');
            $('.forgotPassWrap').css('display','none');
            setTimeout(function () {
                //$scope.goToLogin();
                //$("#signInNotifi.in .signIN").trigger("click");
            }, 3000);
            $("body").on("click", "#signInNotifi", function () {
                if ($("#myModal").hasClass("in")) {
                    setTimeout(function () {
                        //$("body").addClass("modal-open");
                    }, 500);
                }
            });
        };
		
		$('body').on('click','.signInoverlay', function(){
			$('#signInNotifi, .signInoverlay').css('display','none');
		});


        $scope.goToLogin = function () {
            window.location = ROOT_URL;
        }


        $scope.showElapsedTime = function (post_date) {
            post_date = moment(post_date).toDate();
            var oldLimit = new Date();
            oldLimit.setDate(oldLimit.getDate() - 7); // 7 days old
            return post_date > oldLimit;
        };

        $scope.$on('$viewContentLoaded', function () {
            notificationWidth();
            angular.element(window).resize(function () {
                notificationWidth();
            });
            //owlCenterAlign();
            $(window).resize(function () {
                setTimeout(function () {
                    $scope.owlCenterAlign();
                }, 600);
            });


        });

        $scope.upvoteDownvoteTxt = function (vote) {
            vote = parseInt(vote);
            if (vote > 1)
                return 'upvotes';
            else if (vote >= 0)
                return 'upvote';
            else if (vote < -1)
                return 'downvotes';
            else if (vote < 0)
                return 'downvote';
        };

        function notificationWidth() {
            if ($(window).width() <= 599) {
                var winWidth = $(window).width();
                $('.notification-panel').css({"width": winWidth});
            } else {
                $('.notification-panel').css({"width": ""});
            }
        }

        $scope.owlCenterAlign = function () {
            var owlCarousel = $(".owl-carousel");
            if (owlCarousel.length > 0) {
                owlCarousel.each(function () {
                    var owlOuterWidth = $(this).children(".owl-stage-outer").width();
                    var owlWidth = $(this).children().children(".owl-stage").innerWidth();
                    if (owlWidth < owlOuterWidth) {
                        $(this).removeClass("centerItem");
                        $(this).addClass("centerItem");
                    } else {
                        $(this).removeClass("centerItem");
                    }
                });
            }
        };

        $scope.initFlickity = function (element) {
            $(element).show();
            var defaultOptions = {
                freeScroll: true,
                contain: true,
                prevNextButtons: false,
                pageDots: false,
                wrapAround: true,
				cellAlign: 'left'
            };
            var customOptions = $scope.$eval($(element).attr('data-options'));
            for (var key in customOptions) {
                defaultOptions[key] = customOptions[key];
            }
            $(element).flickity(defaultOptions);
        };

        $scope.removePostLoading = function (postLoading) {
            /*postLoading = $("#" + postLoading);
             postLoading.removeClass('postLoading');*/
        };

        // For post details popup..
        $scope.postPopupClose = function (post_id) {
            var $detailModal = $("#myModal");
            // Code for mobile device
            if ($.Pages.getUserAgent() == 'mobile') {
                $("html").removeClass("mob-hideModal");
                $(".loaderImage").hide();
                window.scrollTo(0, $scope.winScrollPos);
                setTimeout(function () {
                    if ($detailModal.is(':visible')) {
                        $detailModal.modal('toggle');
                    }
                }, 100);
            }
            $detailModal.unbind();
			
			$("html, body").removeClass("sidebar-open bodyHidden scrollHidden mob-hideModal modal-open");
			

            // Reset the post.
            $scope.post = {};
            // console.log('postPopupClose called: ' + post_id);
            // Code for socket.
            $scope.emitPostClosed(post_id, 1);

            // For Online people here.
            var opened_posts = localStorageService.get('opened_posts');
            opened_posts = JSON.parse(opened_posts);
            if (opened_posts) {
                // Decrease count.
                opened_posts[post_id]--;
                if (opened_posts[post_id] == 0) {
                    var p;
                    for (p in $scope.commonData.allPosts) {
                        if ($scope.commonData.allPosts[p].id == post_id) {
                            // console.log($scope.commonData.allPosts[p].people_here);
                            if ($scope.commonData.allPosts[p].people_here > 0) {
                                $scope.commonData.allPosts[p].people_here -= 1;
                            }
                            break;
                        }
                    }
                    delete opened_posts[post_id];
                }
                localStorageService.set('opened_posts', JSON.stringify(opened_posts));
            }
        };

        $scope.winScrollPos = 0;
        $scope.showPostDetails = function (post_id, childPostId, is_scroll, postCardIndex, comment_id) {

           
            $scope.winScrollPos = $(window).scrollTop();

            var myModal = $("#myModal");
            myModal.on('hide.bs.modal', function () {
                $scope.postPopupClose(post_id);
                $(".barLong").hide();
            });
            myModal.on('show.bs.modal', function () {
                $(".barLong").show();
            });

            $scope.displayBestTab = false;
            myModal.addClass("modalLoader");
            myModal.modal();

            $(".barLong, .mobileBarLong").css({"display": "none"});  // Scroll bar loader
            $(".modalMobileScroll, .modal, .postModalOuter").animate({scrollTop: 0});
            $("html").addClass("scrollHidden");
            $('.scrollbar-inner').scrollbar(); // For Mobile scrollbar 

            $scope.showSendBtn = true;
            // reset post data ...
            $scope.post = {};
            // console.log($scope.post);
            /*------- Code for socket -------*/
            $scope.totalPeopleHere = 0;

            $timeout(function () {
                // Scroll bar loader
                $(".barLong, .mobileBarLong").css({"width": "0px", "display": "block"});
                $(".modalMsg").val('');	// for empty comment box
            }, 1000);

            // Show loader..
            $scope.removePostLoader = false;
            $scope.postCardModal = "tpl_postcardmodal";
            // $scope.comments=[];

            $http({
                method: "POST",
                url: "angular/showPostDetails",
                params: {post_id: post_id, child_post_id: childPostId, is_briefed: 0}
            }).then(function (response) {

                $scope.post = response.data.post;
                //$scope.post.distance = null;

                /*------- Code for socket -------*/
                postOpened.init();
                socket.removeAllListeners('total_people_here');

                $scope.emitPostOpened($scope.post, 1);
                $scope.listenTotalPeopleEvent(function (totalPeopleHere) {
                    $scope.totalPeopleHere = totalPeopleHere;
                    if ($scope.loggedIn) {
                        var p;
                        for (p in $scope.commonData.allPosts) {
                            if ($scope.commonData.allPosts[p].id == post_id) {
                                $scope.commonData.allPosts[p].people_here = $scope.totalPeopleHere;
                                break;
                            }
                        }
                    }
                });
                // For real time data
                if ($scope.loggedIn) {
                    // Track opened post
                    postOpened.trackPostOpened(post_id);

                    // Listen for user followed event
                    postOpened.userFollowedEvent(function (response) {
                        if ($scope.post.getUser) {
                            $scope.post.getUser.forEach(function (user) {
                                if (user.id == response.user_id) {
                                    user.is_follow = response.data.totalFollowers;
                                }
                            });
                        }
                    });
                    // Listen for user viewed event
                    postOpened.userViewedEvent(function (response) {
                        if ($scope.post.getUser) {
                            $scope.post.getUser.forEach(function (user) {
                                if (user.id == response.user_id) {
                                    user.userDataProfileViews = response.data.userDataProfileViews;
                                }
                            });
                        }
                    });
                    // Listen for user point updated event
                    postOpened.userPointUpdatedEvent(function (response) {
                        if ($scope.post.getUser) {
                            $scope.post.getUser.forEach(function (user) {
                                if (user.id == response.user_id) {
                                    user.points = response.data.points;
                                }
                            });
                        }
                    });

                    // Listen for post view updated event
                    postOpened.listenPostViewUpdatedEvent(function (response) {
                        if ($scope.post && $scope.post.id == response.post_id) {
                            $scope.post.totalPostViews = response.data.totalPostViews;
                        }
                    });
                    // Listen for post point updated event
                    postOpened.listenPostPointUpdatedEvent(function (response) {
                        if ($scope.post && $scope.post.id == response.post_id) {
                            $scope.post.points = response.data.points;
                        }
                    });
                    // Listen for post upvoted event
                    postOpened.listenPostUpvotedEvent(function (response) {
                        if ($scope.post && $scope.post.id == response.post_id) {
                            $scope.post.upvotes = response.data.upvotes;
                            $scope.post.downvotes = response.data.downvotes;
                        }
                    });
                    // Listen for post bookmarked event
                    postOpened.listenPostSharedEvent(function (response) {
                        if ($scope.post && $scope.post.id == response.post_id) {
                            $scope.post.normalShare = response.data.normalShare;
                            $scope.post.totalFBshare = response.data.totalFBshare;
                            $scope.post.totalTwittershare = response.data.totalTwittershare;
                            $scope.post.totalShare = $scope.post.normalShare
                                + $scope.post.totalFBshare
                                + $scope.post.totalTwittershare;
                        }
                    });
                    // Listen for usersTypingInChannel event
                    postOpened.listenPostBookmarkedEvent(function (response) {
                        if ($scope.post && $scope.post.id == response.post_id) {
                            $scope.post.totalBookMark = response.data.totalBookMark;
                        }
                    });
                }
                /*------- END Code for socket -------*/

                /*** Calculate user distance ***/
                /*if(navigator.geolocation){
                    navigator.geolocation.getCurrentPosition(function(position){
                        var lat = position.coords.latitude;
                        var lng = position.coords.longitude;
                        $scope.post.distance = haversineDistanceCalculationService.findDistance(lat, lng, $scope.post.lat, $scope.post.lon);

                        console.log($scope.post.distance);
                    }, function(PositionError){
                    },{
                        enableHighAccuracy: true,
                        timeout: 5000,
                        maximumAge: 0
                    });
                }*/
                /*** Calculate user distance ***/

                //  For device and creator name before caption on postcard modal   
                var postModalUser = response.data.post.getUser;
                angular.forEach(postModalUser, function (value, key) {
                    $scope.postUserName = value.first_name;
                    $scope.createdUsername = value.username;
                    $scope.createdUserFirstName = value.first_name;
                    $scope.createdUserLastName = value.last_name;
                    $scope.createdUserAboutMe = value.about_me;
                    $scope.createdUserColor = value.user_color;
                    $scope.createdUserId = value.id;
                    $scope.creatorProfileImage = value.thumb_image_url;
                });
                // end 

                // using for doDownVotes(post.id,post.child_post_id,openFrom) function
                // M  for  modal and C for card .
                $scope.openFrom = 'M';

                // update user points dynamically

                if ($state.current.name == 'profile' || $state.current.name == 'account') {
                    var points = postModalUser[0].points;
                    userDataService.updateUserData(points);
                    userDataService.getData();

                }

                if (!$scope.postData) {
                    $scope.postData = {};
                }

                $scope.postData.post_id = $scope.post.id;

                // Update dynamically total number of post view 
                var p;
                for (p in $scope.commonData.allPosts) {
                    if ($scope.commonData.allPosts[p].id == post_id) {
                        $scope.commonData.allPosts[p].totalPostViews = response.data.post.totalPostViews;
                        // break;                       
                    }
                }
                // reset video
                $("video").load();
                // *** END *** //

                // Hide Loader..
                $scope.removePostLoader = true;
                $timeout(function () {
                    myModal.removeClass("modalLoader");
                    $("html").addClass("mob-hideModal");
                    $('.modalMsg').on("touchstart", function () {
                        $(this).trigger('mouseenter');
                    });
                }, 100);

                if (is_scroll == 1) {
                    $timeout(function () {
                        $location.hash('commentfilter' + post_id);
                        $anchorScroll();
                    }, 2000);
                }
                // For notification.
                else if (is_scroll == 3) {
                    if (comment_id) {
                        $timeout(function () {
                            /*$("#newcomments").trigger('click');
                             $(".more_comment_div .loadMoreBtn").trigger('click');
                             $location.hash('pc' + post_id);*/
                            $location.hash('commentfilter' + post_id);
                            $anchorScroll();
                        }, 2000);
                    }
                }

                offset = 5;
                $timeout(function () {
                    if ($scope.post.bestTab > 0) {
                        $("#bestComments").trigger("click");
                    }
                    else {
                        $("#newcomments").trigger("click");
                    }
                }, 400);
            });


            if (postCardIndex) {
                $scope.postCardIndex = postCardIndex;
            }
            else {
                $scope.postCardIndex = 0;
            }
        };

        $(document).on("click", ".shareModal", function () {
            if (!$('.shareModal').hasClass("in")) {
				var sharelastScrollPos = $(window).scrollTop();
				$("html, body").animate({scrollTop: sharelastScrollPos}, 0);
                $("html").removeClass("mob-hideModal");
            }
        });

        $scope.videoPlay = function (post, index, type) {
            if ($(window).width() > 600) {
                if (type == 'C') {
                    if (post.video) {
                        var vIndex = $("#cardVideo-" + post.cardID);
                        cardVideo = vIndex.find(".videoTag");
                        cardVideo[0].play();

                        vIndex.next(".customPlayPause").hide();
                    }
                    else if (post.embed_code) {
                        var embed_code = post.embed_code;
                        var videoURL = embed_code.toLowerCase();

                        if (videoURL.search('youtube') != -1) {
                            var index = 'yc-' + post.cardID;
                            if (typeof tsmPlayerPool[index] === 'undefined' || typeof tsmPlayerPool[index].playVideo !== 'function') {
                                return;
                            }
                            tsmPlayerPool[index].playVideo();
                        }
                        else if (videoURL.search('vimeo') != -1) {
                            var index = 'vc-' + post.cardID;
                            var iframe = $("#" + index).find('iframe')[0];
                            var player = new Vimeo.Player(iframe);
                            player.play();

                        }
                        else if (videoURL.search('dailymotion') != -1) {
                            var index = 'dc-' + post.cardID;
                            $("#" + index).find('iframe')[0].contentWindow.postMessage('play', "*");
                        }
                    }

                }
                else {
                    if (post.video) {
                        var video = $(".profileNewLeft").find('.videoTag');
                        video[0].play();
                        video.next().hide();
                    }
                    else if (post.embed_code) {
                        var embed_code = post.embed_code;
                        var videoURL = embed_code.toLowerCase();
                        if (videoURL.search('youtube') != -1) {
                            var index = 'yc-' + post.cardID;
                            tsmPlayerPool[index].playVideo();
                        }
                        else if (videoURL.search('vimeo') != -1) {

                            var index = 'vc-' + post.cardID;
                            var iframe = $("#" + index).find('iframe')[0];
                            var player = new Vimeo.Player(iframe);
                            player.play();

                        }
                        else if (videoURL.search('dailymotion') != -1) {
                            var index = 'dc-' + post.cardID;
                            $("#" + index).find('iframe')[0].contentWindow.postMessage('play', "*");
                        }
                    }
                }
            }

        };

        $scope.videoPause = function (post, index, type) {

            if ($(window).width() > 600) {
                if (type == 'C') {
                    if (post.video) {
                        var vIndex = $("#cardVideo-" + post.cardID);

                        var vid = vIndex.find(".videoTag");
                        vid[0].pause();

                        vIndex.next(".customPlayPause").show();
                    }
                    else if (post.embed_code) {
                        var embed_code = post.embed_code;
                        var videoURL = embed_code.toLowerCase();

                        if (videoURL.search('youtube') != -1) {
                            var index = 'yc-' + post.cardID;
                            var currentPlayer = tsmPlayerPool[index];
                            if (
                                !currentPlayer || typeof currentPlayer === 'undefined' ||
                                typeof currentPlayer.getPlayerState !== 'function'
                            ) {
                                return;
                            }
                            var currentPlayerState = currentPlayer.getPlayerState();
                            // Pause if it is playing.
                            if (currentPlayerState == 1 || currentPlayerState == 3) {
                                currentPlayer.pauseVideo();
                            }

                            /*var ct = currentPlayer.getCurrentTime();
                            var vDuration = currentPlayer.getDuration();
                            // var vData = currentPlayer.getVideoData();

                            if (ct >= vDuration * constants.VIDEO_VIEW_PER) {
                                $scope.viewVideoPost(post);
                            }*/

                        }
                        else if (videoURL.search('vimeo') != -1) {
                            var index = 'vc-' + post.cardID;
                            var iframe = $("#" + index).find('iframe')[0];
                            var player = new Vimeo.Player(iframe);
                            player.pause();
                            
                            /*player.getCurrentTime().then(function (seconds) {
                                if (seconds >= 5) {
                                    $scope.viewVideoPost(post);
                                }
                            }).catch(function (error) {
                                console.log(error);
                            });*/
                        }
                        else if (videoURL.search('dailymotion') != -1) {
                            var index = 'dc-' + post.cardID;
                            $("#" + index).find('iframe')[0].contentWindow.postMessage('pause', "*");
                        }
                    }

                }
                else {
                    if (post.video) {
                        var video = $(".profileNewLeft").find(".videoTag");
                        video[0].pause();
                        var currentTime = video[0].currentTime;
                        if (Math.round(currentTime) >= 5) {
                            $scope.viewVideoPost(post);
                        }

                        video.next().show();
                    }
                    else if (post.embed_code) {

                        var embed_code = post.embed_code;
                        var videoURL = embed_code.toLowerCase();

                        if (videoURL.search('youtube') != -1) {
                            var currentPlayer = tsmPlayerPool[index];
                            currentPlayer.pauseVideo();
                        }
                        else if (videoURL.search('vimeo') != -1) {
                            var index = 'vc-' + post.cardID;
                            var iframe = $("#" + index).find('iframe')[0];
                            var player = new Vimeo.Player(iframe);
                            player.pause();
                            
                            /*player.getCurrentTime().then(function (seconds) {
                                if (seconds >= 5) {
                                    $scope.viewVideoPost(post);
                                }
                            }).catch(function (error) {
                                console.log(error);
                            });*/

                        } else if (videoURL.search('dailymotion') != -1) {
                            var index = 'dc-' + post.cardID;
                            $("#" + index).find('iframe')[0].contentWindow.postMessage('pause', "*");
                        }
                    }
                }
            }
        };


        $scope.viewVideoPost = function (post) {

            var postID = post.id;
            var childPostID = post.child_post_id;
            var postType = post.post_type;

            $http({
                method: "POST",
                url: "angular/viewPost",
                data: {
                    postID: postID,
                    childPostID: childPostID,
                    postType: postType
                }
            }).then(function (response) {

                // userData is update on proilfe or account .
                if ($state.current.name == 'profile' || $state.current.name == 'account') {
                    var points = response.data.post.getUsers[0].points;
                    userDataService.updateUserData(points);
                    userDataService.getData();
                }

                // Update dynamically total number of post view
                var p;
                for (p in $scope.commonData.allPosts) {
                    if ($scope.commonData.allPosts[p].id == postID) {
                        $scope.commonData.allPosts[p].totalPostViews = response.data.post.totalPostViews;
                        // break;
                    }
                }
                // Update postcard modal ::
                post.points = response.data.post.points;
                post.totalPostViews = response.data.post.totalPostViews;

                if (angular.isArray(post.getUser)) {
                    angular.forEach(post.getUser, function (value, key) {
                        angular.forEach(response.data.post.getUsers, function (val, k) {
                            if (value.id == val.id) {
                                post.getUser[k].points = val.points;
                            }
                        });
                    });
                }

            }, function (error) {
                // console.log('Error occurred.');
            });
        };


        $(window).load(function () {
           
            postCardPosition();
        });
        $(document).on('scroll', function () {
            postCardPosition();
        });

        // var checkArray=[];



        /******* Changes for new activity seen algo(20-12-17)  ********/
 /*       
        function postCardPosition() {
        
            var pCFnoBorder;
            $(".image_status_post.na").each(function () {
                pCFnoBorder = $(this).find(".profileCommentFooter.noBorderPos");
                if (pCFnoBorder.length > 0) {
                    var topPos = pCFnoBorder.offset().top - 0;
                    // var thisHeight = $(this).innerHeight();
                    var screenHeight = $(window).height();
                    var lastPos = topPos - screenHeight;

                    if ($(window).scrollTop() >= lastPos) {
                        var postID = $(this).attr("custompostid");
                        $scope.imageAndStatusView(postID);
                        $(this).removeClass('na');
                        /*if(checkArray.indexOf(postID)==-1 ) {
                         $scope.imageAndStatusView(postID);
                         checkArray.push(postID);
                         }*/
/*                    }
                }
            });
            // .exploreTest
            $(".blockContent").each(function () {
                if (!$(this).hasClass("showNow")) {
                    var thisPos = $(this).offset().top;
                    var screenPos = $(window).scrollTop();
                    var winHeight = $(window).height() - 160;

                    if (thisPos <= (screenPos + winHeight)) {
                        $(".exploreTest .blockContent").addClass("hideNow");
                        $(this).removeClass("hideNow").addClass("showNow");
                    }
                }
            });
        }

*/
 


 function postCardPosition() {
    
        var pCFnoBorder;
        $(".post.na").each(function () {
            pCFnoBorder = $(this).find(".profileCommentFooter.noBorderPos");
            if (pCFnoBorder.length > 0) {
                var topPos = pCFnoBorder.offset().top - 0;
                // var thisHeight = $(this).innerHeight();
                var screenHeight = $(window).height();
                var lastPos = topPos - screenHeight;

                if ($(window).scrollTop() >= lastPos) {
                    var postID = $(this).attr("custompostid");
                    $scope.postSeenView(postID);
                    $(this).removeClass('na');
                    /*if(checkArray.indexOf(postID)==-1 ) {
                     $scope.imageAndStatusView(postID);
                     checkArray.push(postID);
                     }*/
                   }
            }
        });
        // .exploreTest
        $(".blockContent").each(function () {
            if (!$(this).hasClass("showNow")) {
                var thisPos = $(this).offset().top;
                var screenPos = $(window).scrollTop();
                var winHeight = $(window).height() - 160;

                if (thisPos <= (screenPos + winHeight)) {
                    $(".exploreTest .blockContent").addClass("hideNow");
                    $(this).removeClass("hideNow").addClass("showNow");
                }
            }
        });
    }



    $scope.postSeenView = function (postID) {
        // console.log("Recording activity for: " + postID);
        if (angular.isArray($scope.commonData.allPosts)) {
            angular.forEach($scope.commonData.allPosts, function (value, key) {

                if ($scope.commonData.allPosts[key].id == postID) {
                    if ($scope.commonData.allPosts[key].created_by != $scope.user.id) {
                        $http({
                            method: "POST",
                            url: "angular/viewSeenPost",
                            data: {
                                postID: postID,
                                childPostID: $scope.commonData.allPosts[key].child_post_id,
                                postType: $scope.commonData.allPosts[key].post_type
                            }
                        }).then(function (response) {

                            //this block is only accessed profile and account page
                            if ($state.current.name == 'profile' || $state.current.name == 'account') {
                                if (response.data.post) {
                                    var points = response.data.post.getUsers[0].points;
                                    userDataService.updateUserData(points);
                                }
                                userDataService.getData();
                            }

                        }, function (error) {
                            console.log('Error occurred.');
                        });
                    }

                    return;
                }

            });
        }
    };



/******* Changes for new activity seen algo(20-12-17)  ********/









        $scope.imageAndStatusView = function (postID) {
            // console.log("Recording activity for: " + postID);
            if (angular.isArray($scope.commonData.allPosts)) {
                angular.forEach($scope.commonData.allPosts, function (value, key) {

                    if ($scope.commonData.allPosts[key].id == postID) {
                        if ($scope.commonData.allPosts[key].created_by != $scope.user.id) {
                            $http({
                                method: "POST",
                                url: "angular/viewPost",
                                data: {
                                    postID: postID,
                                    childPostID: $scope.commonData.allPosts[key].child_post_id,
                                    postType: $scope.commonData.allPosts[key].post_type
                                }
                            }).then(function (response) {

                                //this block is only accessed profile and account page
                                if ($state.current.name == 'profile' || $state.current.name == 'account') {
                                    if (response.data.post) {
                                        var points = response.data.post.getUsers[0].points;
                                        userDataService.updateUserData(points);
                                    }
                                    userDataService.getData();
                                }

                            }, function (error) {
                                console.log('Error occurred.');
                            });
                        }

                        return;
                    }

                });
            }
        };

        // Direct post to twitter from  create post page (IMAGE, LINK, ARTICLE and VIDEO)

        $scope.connectTwitter = function () {

            if ($("#twitter").prop('checked') == true) {

                var left = screen.width / 2 - 200;
                var top = screen.height / 2 - 250;
                var interval = 1000;
                $window.$scope = $scope;


                $http({
                    method: "POST",
                    url: "accessTokenVerify",
                }).then(function (response) {

                    if (parseInt(response.data) == 0) {
                        var popup = $window.open('auth-twitter', '', 'left=' + left + ',top=' + top + ',width=550,height=450,personalbar=0,toolbar=0,scrollbars=0,resizable=0');
                        // create an ever increasing interval to check a certain global value getting assigned in the 
                        var i = $interval(function () {
                            interval += 500;
                            try {
                                // value is the user_id returned from paypal 
                                if (popup.value) {
                                    $interval.cancel(i);
                                    popup.close();
                                }
                            } catch (e) {
                                console.error(e);
                            }
                        }, interval);
                    }

                }, function (response) {
                    console.log('some error is occurred');
                })
            }
        };

        /*** Start Geolocation watchposition ***/
        /*var watchId;

        $scope.watchForPosition = function(){
            if (navigator.geolocation) {
                // timeout at 60000 milliseconds (60 seconds)
                var options = {timeout:60000};
                var geoLoc  = navigator.geolocation;
                watchId = geoLoc.watchPosition(saveLocation, errorHandler, options);
            }
        };

        var saveLocation = function(position){
            console.log("Geolocation : "+JSON.stringify(position));
        };

        var errorHandler = function(err){
            switch(err.code){
                case 1: console.log("Error: Access is denied!");break;
                case 2: console.log("Error: Position is unavailable!");break;
            }
        };

        $scope.watchForPosition();*/

        /*** End Geolocation watchposition ***/

    }])
    .controller('SignInCtrl', ['$scope', '$http', '$rootScope', '$state', '$stateParams', '$sce', '$filter', '$timeout', '$interval', '$location', '$window', '$anchorScroll', 'refreshService', 'userDataService', 'YT_event', 'socket', 'postOpened', 'localStorageService', 'haversineDistanceCalculationService', function ($scope, $http, $rootScope, $state, $stateParams, $sce, $filter, $timeout, $interval, $location, $window, $anchorScroll, refreshService, userDataService, YT_event, socket, postOpened, localStorageService, haversineDistanceCalculationService) {

        $scope.popupsigninH = {
            email: '',
            password: ''
        }

        // this will make the login submit
        $scope.loginPopSubmit = function(e) {

            document.getElementById("signSubmit").classList.add('disabled');

            // console.log('$("#form-signin").serializeArray()')
            // alert('login')
            // this will parse the serialize array data from the form field
            function objectifyForm(formArray) {//serialize data function
                var returnArray = {};
                for (var i = 0; i < formArray.length; i++){
                returnArray[formArray[i]['name']] = formArray[i]['value'];
                }
                return returnArray;
            }
            // make the object so server can accept and give return value
            $scope.loginPopUpUserData = objectifyForm($("#form-signin").serializeArray())


            if($scope.loginPopUpUserData.email != '' && $scope.loginPopUpUserData.password != ''){
                
                var loginPopUp = $http.post('/loginapi', $scope.loginPopUpUserData);
                loginPopUp.success(function(data, status, headers, config) {

                    document.getElementById("signSubmit").classList.remove('disabled');

                    if(data.msg == "error"){
                        $scope.signinerrormsgST = true
                        $scope.signinerrormsg = 'Invalid email and password'
                    } else {
                        // alert('everything ok')
                        document.getElementById('form-signin-h').submit()
                    }

                    // alert('success')
                    $scope.signinsuccessST = true;
                    $scope.signinsuccess = 'success';
                });
                loginPopUp.error(function(data, status, headers, config) {

                    document.getElementById("signSubmit").classList.remove('disabled');
                    // alert( "failure message");
                    $scope.signinerrormsgST = true
                    $scope.signinerrormsg = 'error'
                });

            } else {
                document.getElementById("signSubmit").classList.remove('disabled');

                $scope.signinerrormsgST = true
                $scope.signinerrormsg = 'Please put the email and password'
            }


            // document.getElementById('form-signin').submit()

            // angular sign in form submit
            // var loginPopUp = $http.post('/savecompany_json', $scope.loginPopUpUserData);
            // loginPopUp.success(function(data, status, headers, config) {
            //     // alert('success')
            //     $scope.signinsuccessST = true;
            //     $scope.signinsuccess = 'success';
            // });
            // loginPopUp.error(function(data, status, headers, config) {
            //     // alert( "failure message");
            //     $scope.signinerrormsgST = true
            //     $scope.signinerrormsg = 'error'
            // });

            
        };

    }])
    .controller('ForgotPasswordCtrl', ['$scope', '$http', '$rootScope', '$state', '$stateParams', '$sce', '$filter', '$timeout', '$interval', '$location', '$window', '$anchorScroll', 'refreshService', 'userDataService', 'YT_event', 'socket', 'postOpened', 'localStorageService', 'haversineDistanceCalculationService', function ($scope, $http, $rootScope, $state, $stateParams, $sce, $filter, $timeout, $interval, $location, $window, $anchorScroll, refreshService, userDataService, YT_event, socket, postOpened, localStorageService, haversineDistanceCalculationService) {
        
                // $scope.popupsigninH = {
                //     email: '',
                //     password: ''
                // }
        
                // this will make the login submit
                $scope.forgotPopupSubmit = function(e) {
        
                    document.getElementById("forgotPassSubmit").classList.add('disabled');
        
                    // console.log('$("#form-signin").serializeArray()')
                    // alert('forgot')
                    // this will parse the serialize array data from the form field
                    function objectifyForm(formArray) {//serialize data function
                        var returnArray = {};
                        for (var i = 0; i < formArray.length; i++){
                        returnArray[formArray[i]['name']] = formArray[i]['value'];
                        }
                        return returnArray;
                    }
                    // make the object so server can accept and give return value
                    $scope.forgotPasswordUserData = objectifyForm($("#form-forgot").serializeArray())
                      //  alert($scope.forgotPasswordUserData.email);
        
                    if($scope.forgotPasswordUserData.email != '' ){


                        var forgotPopUp = $http.post('/forgotPasswordApi', $scope.forgotPasswordUserData);
                        forgotPopUp.success(function(data, status, headers, config) {
                            document.getElementById("forgotPassSubmit").classList.remove('disabled');
                            
                         // alert(data.msg)
        
                            if(data.msg == "error"){
                               
                                $scope.forgotpasserrormsgST = true
                                $scope.forgotpasserrormsg = 'Invalid email and password'

                                $scope.forgotpasssuccmsgST = false
                                $scope.forgotpasssuccmsg = ''
                            } else {
                               
                                $scope.forgotpasssuccmsgST = true
                                $scope.forgotpasssuccmsg = 'Please check your email.'
                                $scope.forgotEmail=''
                                $scope.forgotpasserrormsgST = false
                                $scope.forgotpasserrormsg = ''
                            }
                        });
                       
        
                            
        
                            // alert('success')
                           
                        
                       
        
                    } else {
                        document.getElementById("forgotPassSubmit").classList.remove('disabled');
        
                        $scope.forgotpasserrormsgST = true
                        $scope.forgotpasserrormsg = 'Please put the email'
                    }
        
        
                    // document.getElementById('form-signin').submit()
        
                    // angular sign in form submit
                    // var loginPopUp = $http.post('/savecompany_json', $scope.loginPopUpUserData);
                    // loginPopUp.success(function(data, status, headers, config) {
                    //     // alert('success')
                    //     $scope.signinsuccessST = true;
                    //     $scope.signinsuccess = 'success';
                    // });
                    // loginPopUp.error(function(data, status, headers, config) {
                    //     // alert( "failure message");
                    //     $scope.signinerrormsgST = true
                    //     $scope.signinerrormsg = 'error'
                    // });
        
                    
                };
        
            }]);

/*------- Directives ------*/
angular.module('app')
    .directive('loading', function () {
        return {
            restrict: 'E',
            replace: true,
            template: '<div class="coverPhotoLoading"></div>',
            link: function (scope, element, attr) {
                scope.$watch('loading', function (val) {
                    if (val)
                        $(element).show();
                    else
                        $(element).hide();
                });
            }
        }
    })

    .filter('ytVideoId', function () {
        return function (url) {
            return /[^/]*$/.exec(url)[0];
        };
    })

    .filter('tagreplace', function () {
        return function (data) {
            console.log(data);
            return data.replace(/-/g, ' ').replace(/_/g, ' ');
        };
    })

    .directive('imageonload', function () {
        return {
            restrict: 'A',
            link: function (scope, element, attrs) {
                element.one('load', function () {
                    //call the function that was passed
                    scope.$apply(attrs.imageonload);
                }).each(function () {
                    if (this.complete) {
                        scope.$apply(attrs.imageonload);
                    }
                });
                element.one('error', function () {
                    //call the function that was passed
                    scope.$apply(attrs.imageonload);
                }).each(function () {
                    if (this.complete) {
                        scope.$apply(attrs.imageonload);
                    }
                });
            }
        };
    })

    .directive('featureImageOnload', function () {
        return {
            restrict: 'A',
            link: function (scope, element, attrs) {

                element.bind('load', function() {
                    // Remove min-height
                    element.css({ 'min-height' : '' } );

                    // load actual thumb image.
                    var imgLarge = new Image();
                    imgLarge.classList.add('largeFeatImage');
                    imgLarge.src = scope.post.image;
                    imgLarge.onload = function () {
                        imgLarge.classList.add('loaded');
                        element.replaceWith(imgLarge);
                        clearImage();
                    };
                    // element.replaceWith(imgLarge);
                    function clearImage() {
                        imgLarge = null;
                    }
                });

                element.bind('error', function(){
                    attrs.src = $scope.default_post_img;
                });
            }
        };
    })

    .filter('highlightNode', ["$sce", function ($sce) {
        return function (str, theTerms) {
            if (!theTerms) {
                return str;
            }
            // Sort terms by length
            theTerms.sort(function (a, b) {
                return b.length - a.length;
            });
            // Regex to simultaneously replace terms
            var regex = new RegExp("<a href='.*?'>|(" + theTerms.join('|') + ')', 'gi');
            var replaced = str.replace(regex, function (m, group1) {
                if (!group1) return m;
                else return $sce.trustAsHtml('<span class="hlNode">' + group1 + '</span>');
            });
            return replaced;
            // return $sce.trustAsHtml(replaced);
        };
    }])

    .filter('highlightTag', ["$sce", function ($sce) {
        return function (str) {
            if (!str) return '[Caption]';
            // Convert new lines to break tag.
            var str = nl2br(str);
            var regex = new RegExp('\#([a-z-A-Z0-9]+)', 'gi');
            var replaced = str.replace(regex, function (m, group1) {
                if (!group1) return m;
                else return $sce.trustAsHtml('<a><span>#</span></span><span class="hlTag">' + group1 + '</span></a>');
            });
            return replaced;
        };
    }])

    .directive('postCard', ["$timeout", "$window", "$http", "$state", function ($timeout, $window, $http, $state) {
        return {
            restrict: 'AE',
            scope: false,
            templateUrl: "tpl.post-card",
            link: function ($scope, element, attrs) {
                if ($scope.$first === true) {
                    $scope.startPositionCards();
                    $scope.startReloadCardMasonry();
                }


                if ($scope.$last === true) {
                    $scope.stopPositionCards();
                    $scope.stopReloadCardMasonry();
                    $timeout(function () {
                        // Remove class.
                        $('.postCard').removeClass('postCardHide');
                    }, 1200);
                }

                /*
                 * determine post card class.
                 * toLoad => removing needs to be done manually
                 */
                $scope.cardClass = function (post) {
                    var cssClass = '';
                    if (post.post_type === 2 || ((post.post_type === 3 || post.post_type === 4 || post.post_type === 5) && !post.image)) {
                        if (!post.image)
                            cssClass = 'toLoad noImg';
                        else
                            cssClass = 'toLoad';
                    }
                    return cssClass;
                };

                var elm = element.find("div.postLoading");

                $timeout(function () {
                    /* Remove manually(from the house of manual script) */
                    var timeOutTime = $scope.post.post_type === 2 ? 2000 : 500;
                    $timeout(function () {
                        elm.removeClass("postLoading");
                    }, timeOutTime);
                }, 300);

                // DOM has finished rendering
                /*$timeout(function () {
                    elm.removeClass("postCard");
                });*/

                //$scope.popUpReportDropdrow = 0;
                $scope.report = function (post) {
                    $("#otherSub" + post.child_post_id).show();
                    $("#closeOverLayout" + post.child_post_id).show();

                    $scope.reportpostID = post.id;
                    var flag = 0;

                    $scope.openFrom = 'C';

                    if ($state.current.name === 'profile') {

                        if (post.child_post_user_id != $scope.user.id) {
                            flag = 1;
                        } else {
                            flag = 0;
                        }
                    } else if ($state.current.name == 'account') {

                        if (post.created_by != $scope.user.id) {
                            flag = 1;
                        } else {
                            flag = 0;
                        }

                    } else {

                        if (post.created_by != $scope.user.id) {
                            flag = 1;
                        } else {
                            flag = 0;
                        }
                    }
                    $scope.isShowPostReportLink = flag;
                };
            }
        };
    }])

    .directive('searchPostCard', ["$timeout", "$window", "$http", "$state", function ($timeout, $window, $http, $state) {
        return {
            restrict: 'AE',
            scope: false,
            templateUrl: "tpl.search-post-card",
            link: function ($scope, element, attrs) {
                if ($scope.$first === true) {
                    $scope.startPositionCards();
                    $scope.startReloadCardMasonry();
                }

                if ($scope.$last === true) {
                    $scope.stopPositionCards();
                    $scope.stopReloadCardMasonry();
                }

                /*
                 * determine post card class.
                 * toLoad => removing needs to be done manually
                 */
                $scope.cardClass = function (post) {
                    var cssClass = '';
                    if (post.post_type === 2 || ((post.post_type === 3 || post.post_type === 4 || post.post_type === 5) && !post.image)) {
                        if (!post.image)
                            cssClass = 'toLoad noImg';
                        else
                            cssClass = 'toLoad';
                    }
                    return cssClass;
                };


                $timeout(function () {
                    var elm = element.find("div.postLoading");
                    /* Remove manually */
                    $timeout(function () {
                        elm.removeClass("postLoading");
                    }, 2000);
                }, 300);

                //$scope.popUpReportDropdrow = 0;
                $scope.report = function (post) {
                    $("#otherSub" + post.id).show();
                    $("#closeOverLayout" + post.id).show();

                    $scope.reportpostID = post.id;
                    var flag = 0;
                    $scope.openFrom = 'C';

                    if ($state.current.name == 'profile') {
                        if (post.child_post_user_id != $scope.user.id) {
                            flag = 1;
                        } else {
                            flag = 0;
                        }
                    }
                    else if ($state.current.name == 'account') {

                        if (post.created_by != $scope.user.id) {
                            flag = 1;
                        } else {
                            flag = 0;
                        }

                    } else {
                        if (post.created_by != $scope.user.id) {
                            flag = 1;
                        } else {
                            flag = 0;
                        }
                    }
                    $scope.isShowPostReportLink = flag;
                };
            }
        };
    }])

    .directive('testPostCard', ["$timeout", "$window", "$http", "$state", function ($timeout, $window, $http, $state) {
        return {
            restrict: 'AE',
            scope: false,
            templateUrl: "tpl.test-post-card",
            link: function ($scope, element, attrs) {
                if ($scope.$first === true) {
                    $scope.startPositionCards();
                    // $scope.startReloadCardMasonry();
                }

                if ($scope.$last === true) {
                    $scope.stopPositionCards();
                    // $scope.stopReloadCardMasonry();
                }

                /*
                 * determine post card class.
                 * toLoad => removing needs to be done manually
                 */
                /*$scope.cardClass = function(post) {
                 var cssClass = '';
                 if (post.post_type === 2 || ((post.post_type === 3 || post.post_type === 4 || post.post_type === 5) &&
                 !post.image))
                 {
                 if (!post.image)
                 cssClass = 'toLoad noImg';
                 else
                 cssClass = 'toLoad';
                 }
                 return cssClass;
                 };*/

                setTimeout(function () {
                    var elm = element.find("div.postLoading");
                    /* Remove manually(from the house of manual script) */
                    setTimeout(function () {
                        elm.css("opacity", 1);
                        elm.removeClass("postLoading");
                    }, 1200);
                    /*setTimeout(function() {
                     elm.removeClass("postLoading");
                     }, 1200);*/
                    /*if (elm.hasClass('toLoad')) {
                     if (elm.hasClass('noImg')) {
                     elm.removeClass("postLoading");
                     }
                     else {
                     $timeout(function() {
                     elm.removeClass("postLoading");
                     }, 2000);
                     }
                     }*/
                }, 300);

                //$scope.popUpReportDropdrow = 0;
                $scope.report = function (post) {
                    // $scope.popUpReportDropdrow = !$scope.popUpReportDropdrow;
                    //$("#otherSub"+post.id).show();
                    //$("#closeOverLayout"+post.id).show();

                    $("#otherSub" + post.id).show();
                    $("#closeOverLayout" + post.id).show();

                    $scope.reportpostID = post.id;
                    var flag = 0;

                    $scope.openFrom = 'C';

                    if ($state.current.name == 'profile') {

                        if (post.child_post_user_id != $scope.user.id) {
                            flag = 1;
                        } else {
                            flag = 0;
                        }


                    } else if ($state.current.name == 'account') {

                        if (post.created_by != $scope.user.id) {
                            flag = 1;
                        } else {
                            flag = 0;
                        }

                    } else {

                        if (post.created_by != $scope.user.id) {
                            flag = 1;
                        } else {
                            flag = 0;
                        }
                    }
                    $scope.isShowPostReportLink = flag;
                };
            }
        };
    }])

    .directive('contentLoaded', ["$timeout", function ($timeout) {
        return {
            restrict: 'A',
            link: function ($scope, element, attrs) {
                /*$timeout(function() {
                 var elm = $(element);
                 var imgElm = elm.find("img.cardLoadChk");
                 if (imgElm.length > 0) {
                 imgElm.bind('load', function () {
                 console.log('img is loaded.');
                 });
                 }
                 }, 10);*/
            }
        }
    }])

    .directive('logCardCreation', function () {
        return {
            restrict: 'A',
            link: function ($scope, element, attrs) {
                console.info(
                    "Card created: " + $scope.$index,
                    $scope.post.title ? $scope.post.title : $scope.post.caption
                    // $scope.post
                );
            }
        }
    })

    .directive('postTypeNav', ["$state", "$timeout", function ($state, $timeout) {
        return {
            restrict: 'E',
            scope: false,
            templateUrl: function (elem, attrs) {
                return "tpl.post-type-nav/" + attrs.navtype
            },
            link: function ($scope, element, attrs) {
                // OwlCarousel custom options.
                $scope.navTypeOwlOptions = {
                    loop: false,
                    nav: false,
                    margin: 0,
                    navigation: false,
                    //startPosition:1,
                    items: 7
                };
                $scope.getPost = function (postType) {
                    if ($scope.mySwitch == true) {
                        return false;
                    }

                    if ($state.current.name === 'profile' || $state.current.name === 'account') {
                        $scope.resetPostData();
                        $scope.loadMore(postType);
                    }
                    else {
                        $scope.resetData();
                        $scope.setPostType(postType);
                        if ($state.current.name === 'search') {
                            $scope.fetchPostData($scope.currentTab);
                        }
                        else {
                            $scope.fetchPostData();
                        }
                    }
                };
            }
        }
    }])
    /*
     * Directive for top channel cards.
     */
    .directive('channelCard', function () {
        return {
            restrict: 'E',
            templateUrl: "tpl.channel-card",
        };
    })

    .directive('searchChannelCard', function () {
        return {
            restrict: 'E',
            templateUrl: "tpl.search-channel-card",
        };
    })

    /*
     * Directive for followingTopics.
     */
    .directive('followingTopics', function () {
        return {
            restrict: 'E',
            templateUrl: "tpl.topics",
        };
    })

    .directive('hitEnter', function () {
        return function (scope, element, attrs) {
            element.bind("keydown keypress", function (event) {
                if (event.which === 13) {
                    scope.$apply(function () {
                        scope.$eval(attrs.hitEnter);
                    });

                    event.preventDefault();
                }
            });
        };
    })

    .directive('fallbackSrc', function () {
        return function (scope, element, attrs) {
            element.bind("error", function () {
                scope.$apply(function () {
                    scope.$eval(attrs.fallbackSrc);
                });
            });
        };
    })

    // Directives for owl carousal..
    .directive("owlCarousel", ['$timeout', '$compile', function ($timeout, $compile) {
        return {
            restrict: 'E',
            transclude: false,
            link: function (scope) {
                scope.initCarousel = function (element) {
                    // provide any default options you want
                    var defaultOptions = {
                        margin: 0
                    };
                    // Set timeout for owl init.
                    var time = scope.$eval($(element).attr('data-owl-time'));
                    if (!time) {
                        time = 0;
                    }

                    var customOptions = scope.$eval($(element).attr('data-options'));
                    // combine the two options objects
                    for (var key in customOptions) {
                        defaultOptions[key] = customOptions[key];
                    }

                    $timeout(function () {
                        // init carousel
                        $(element).owlCarousel(defaultOptions);
                        $(element).show();
                        scope.owlCenterAlign();
                        // Hide loader if any
                        if ($(".smallLoaderOwl").length > 0) {
                            $(".smallLoaderOwl").remove();
                        }
                    }, time);
                };
            }
        };
    }])
    .directive('owlCarouselItem', ['$timeout', function ($timeout) {
        return {
            restrict: 'A',
            transclude: false,
            link: function (scope, element) {
                // wait for the last item in the ng-repeat then call init
                if (scope.$last) {
                    $timeout(function () {
                        scope.initCarousel(element.parent());
                    }, 3000);
                }
            }
        };
    }])
    // Directives for flickity carousal..
    .directive('flickityItem', ['$timeout', function ($timeout) {
        return {
            restrict: 'A',
            transclude: false,
            link: function (scope, element) {
                // wait for the last item in the ng-repeat then call init.
                if (scope.$last) {
                    var time = 1000;
                    var parentElm = element.parent();
                    var dataHideSelector = parentElm.attr('data-hide-selector');
                    var dataHideTime = parentElm.attr('data-hide-time');
                    if (dataHideTime) {
                        time = dataHideTime;
                    }
                    setTimeout(function () {
                        scope.initFlickity(element.parent());
                        // Hide loader element.
                        if (dataHideSelector) {
                            $(dataHideSelector).remove();
                        }
                    }, time);
                }
            }
        };
    }])

    .directive('hideLoader', ['$timeout', function ($timeout) {
        return {
            restrict: 'A',
            link: function (scope, element, attrs) {
                // wait for the last item in the ng-repeat then call init
                if (scope.$last) {
                    var selector = element.parent().attr('data-hide-selector');
                    var time = element.parent().attr('data-hide-time');
                    if (!time) {
                        time = 0;
                    }
                    $timeout(function () {
                        var $selector = $(selector);
                        if ($selector.length > 0) {
                            $selector.hide();
                        }
                    }, time);
                }
            }
        };
    }])

    .directive('vimeoVideo', ["$timeout", "$interval", "constants", function ($timeout, $interval, constants) {

        return {
            restrict: "E",
            link: function ($scope, element, attr) {

                var options = {
                    id: attr.vid,
                    width: 640,
                    loop: true
                };
                var isPlayed;
                $timeout(function () {
                    var player = new Vimeo.Player(attr.id, options);

                    /*player.on('play', function () {
                        $scope.viewVideoPost(post_id,child_post_id,type);
                    });*/
                    // player.addEvent('ready', function() { });

                    player.getDuration().then(function(duration) {
                        $interval(function () {
                            player.getCurrentTime().then(function (seconds) {
                                if (seconds >= duration * constants.VIDEO_VIEW_PER && !isPlayed) {
                                    $scope.viewVideoPost($scope.post);
                                    isPlayed = true;
                                }
                            });
                        }, 100);
                    }).catch(function(error) {
                        // an error occurred
                    });

                    player.pause();

                }, 200);

            }
        }
    }])

    .directive('dailyMotion', ["$timeout", "constants", function ($timeout, constants) {

        return {
            restrict: "E",
            link: function ($scope, element, attr) {

                var isPlayed = false;
                $timeout(function () {

                    var player = DM.player(document.getElementById(attr.id), {
                        video: attr.vid,
                        width: '100%',
                        height: '100%',
                        params: {
                            autoplay: false,
                            mute: false
                        }
                    });

                    /* Watch for ads */
                    var isShowingAds = false;
                    player.addEventListener('ad_start', function(event) {
                        isShowingAds = true;
                        player.removeEventListener('ad_start', function () {
                            //    Nothing.
                        });
                    });
                    player.addEventListener('ad_end', function(event) {
                        isShowingAds = false;
                        player.removeEventListener('ad_end', function () {
                            //    Nothing.
                        });
                    });

                    var duration;
                    player.addEventListener('timeupdate', function(event) {
                        setTimeout(function () {
                            if (!duration) {
                                duration = player.duration;
                            }
                            var currentTime = player.currentTime;
                            // console.log('timeupdate: duration: '+duration+' currentTime: '+currentTime);
                            if (
                                currentTime >= duration * constants.VIDEO_VIEW_PER &&
                                !isPlayed &&
                                !isShowingAds
                            ) {
                                console.info('Total duration: '+duration+' currentTime: '+currentTime);
                                $scope.viewVideoPost($scope.post);
                                isPlayed = true;
                            }
                        }, 100);
                    });

                }, 200);
            }
        }
    }])

    .directive('manualVideo', ["$sce", "constants", function ($sce, constants) {

        var linkFunction = function ($scope, element, attrs) {

            $scope.postcardid = attrs.postcardid;
            $scope.childpostid = attrs.childpostid;
            $scope.posttype = attrs.posttype;
            $scope.type = attrs.type;
            $scope.url = $sce.trustAsResourceUrl(attrs.url);

            var isPlayed = false;
            var video = $(element).find(".videoTag");

            video.bind("timeupdate", function () {
                if (this.currentTime >= this.duration * constants.VIDEO_VIEW_PER && !isPlayed) {
                    $scope.viewVideoPost($scope.post);
                    isPlayed = true;
                }
            });
        };

        return {
            restrict: "E",
            template: '<video class="videoTag na" loaded-meta-data="{{type}}" controls loop  playsinline webkit-playsinline ng-attr-poster="{{post.video_poster}}" postcardid="{{postcardid}}" id="cardVideo-{{post.cardID}}" type="{{type}}" posttype="{{posttype}}"><source ng-src="{{url}}"></video>',
            link: linkFunction
        };
    }])

    .directive('loadedMetaData', ["$rootScope", "$timeout", function ($rootScope, $timeout) {
        return {
            restrict: 'A',
            link: function (scope, element, attrs) {
                element[0].addEventListener("loadedmetadata", function () {
                    if (attrs.loadedMetaData === 'C') {
                        $rootScope.$broadcast('masonry.reload');
                    }
                });
                scope.$on('$destroy', function () {
                    element[0].removeEventListener("loadedmetadata", function(){});
                });
            }
        };
    }])

    /*
     * Use this directive together with ng-include to include a
     * template file by replacing the placeholder element
     */
    .directive('includeReplace', function () {
        return {
            require: 'ngInclude',
            restrict: 'A',
            link: function (scope, el, attrs) {
                el.replaceWith(el.children());
            }
        };
    })
    /*
     * Disable click if condition satified.
     */
    .directive('eatClickIf', ['$parse', '$rootScope',
        function ($parse, $rootScope) {
            return {
                // this ensure eatClickIf be compiled before ngClick
                priority: 100,
                restrict: 'A',
                compile: function ($element, attr) {
                    var fn = $parse(attr.eatClickIf);
                    return {
                        pre: function link(scope, element) {
                            var eventName = 'click';
                            element.on(eventName, function (event) {
                                var callback = function () {
                                    if (fn(scope, {$event: event})) {
                                        // prevents ng-click to be executed
                                        event.stopImmediatePropagation();
                                        // prevents href
                                        event.preventDefault();
                                        return false;
                                    }
                                };
                                if ($rootScope.$$phase) {
                                    scope.$evalAsync(callback);
                                } else {
                                    scope.$apply(callback);
                                }
                            });
                        },
                        post: function () {
                        }
                    }
                }
            }
        }
    ])
;
/*------ Filters -------*/
angular.module('app')
    .filter('isEmpty', function () {
        var bar;
        return function (obj) {
            for (bar in obj) {
                if (obj.hasOwnProperty(bar)) {
                    return false;
                }
            }
            return true;
        };
    })

    .filter('trustUrl', ["$sce", function ($sce) {
        return function (url) {
            return $sce.trustAsResourceUrl(url);
        };
    }])

    .filter('ucfirst', function () {
        return function (string) {
            if (string && angular.isString(string)) {
                return string.charAt(0).toUpperCase() + string.slice(1);
            }
            return string;
        };
    })

    .filter('limitShortDesc', function () {
        return function (short_description) {
            /*if (short_description && short_description.length > 100) {
                return short_description.substring(0, 100) + "...";
            }*/
            return short_description;
        };
    })

    .filter('limitUserAbout', function () {
        return function (about) {
            if (about && about.length > 100) {
             return about.substring(0, 100) + "...";
             }
            return about;
        };
    })

    .filter('lpArticleContent', function () {
        return function (content) {
            if (content) {
                content = content.replace(/<video.*<\/video>|<(?!p\s*\/?)[^>]+>/g, '');
                if (content.length > 200) {
                    content = content.substring(0, 200) + "...";
                }
            }
            return content;
        };
    })

    .filter('domainFilter', function () {
        return function (url) {
            if (!url) return '';
            var domain;
            // find & remove protocol (http, ftp, etc.) and get domain
            if (url.indexOf("://") > -1) {
                domain = url.split('/')[2];
            }
            else {
                domain = url.split('/')[0];
            }
            // find & remove port number
            domain = domain.split(':')[0];
            domain = domain.replace('www.', '');
            return domain;
        };
    })

    .filter('ageFilter', function () {
        function calculateAge(birthday) { // birthday is a date
            var ageDifMs = Date.now() - birthday.getTime();
            var ageDate = new Date(ageDifMs); // miliseconds from epoch
            return Math.abs(ageDate.getUTCFullYear() - 1970);
        }

        function monthDiff(d1, d2) {
            if (d1 < d2) {
                if (d2.getMonth() > d1.getMonth()) {
                    var months = d2.getMonth() - d1.getMonth();
                }
                else {
                    var months = (12 - d1.getMonth()) + d2.getMonth();
                }
                return months <= 0 ? 0 : months;
            }
            return 0;
        }

        return function (birthdate) {
            if (!birthdate)
                return '';

            if (birthdate.indexOf('-') == 0) {
                return '';
            }

            birthdate = parseDate(birthdate);

            var age = calculateAge(birthdate);
            if (age == 0)
                return monthDiff(birthdate, new Date()) + ' months old';
            return age + ' years old';
        };
    })


    .filter('strLimit', function () {
        return function (content, limit) {
            if (content) {
                content = content.substring(0, limit);
            }
            return content;
        };
    })


    .filter('thousandSuffix', function () {
        return function (number, fractionSize) {

            if (isNaN(number) || number === null) return null;
            if (number === 0) return "0";

            if (number <= 9999) {
                return number;
            }

            if (!fractionSize || fractionSize < 0)
                fractionSize = 2;

            var abs = Math.abs(number);
            var rounder = Math.pow(10, fractionSize);
            var isNegative = number < 0;
            var key = '';
            var powers = [
                {key: "Q", value: Math.pow(10, 15)},
                {key: "T", value: Math.pow(10, 12)},
                {key: "B", value: Math.pow(10, 9)},
                {key: "M", value: Math.pow(10, 6)},
                {key: "K", value: 1000}
            ];

            for (var i = 0; i < powers.length; i++) {

                var reduced = abs / powers[i].value;

                reduced = Math.round(reduced * rounder) / rounder;

                if (reduced >= 1) {
                    abs = reduced;
                    key = powers[i].key;
                    break;
                }
            }

            return (isNegative ? '-' : '') + abs + key;
        };
    })

    /*.filter('thousandSuffix', function () {
     return function (number)  {
     var ret;
     if(number>9999){

     var abs = Math.abs(number);
     var key = '';
     var powers = [
     {key: "q", value: Math.pow(10,15)},
     {key: "t", value: Math.pow(10,12)},
     {key: "b", value: Math.pow(10,9)},
     {key: "m", value: Math.pow(10,6)},
     {key: "k", value: 1000}
     ];
     var num=0;
     var keyType;
     for(var i = 0; i < powers.length; i++) {
     if (number >= powers[i].value) {
     keyType=powers[i].key;
     num=number/powers[i].value;
     break;
     }
     }

     sNumber = num.toString(); //NUMBER CONVERT TO STRING

     if(sNumber.indexOf(".")!=-1) { // IF NUMBER HOLD POINT THEN EXECUTE THIS BLOCK

     var arr= sNumber.split('.');
     var firstValue=arr[0];
     var lastValue='';

     if(firstValue.length<3) {

     if(firstValue.length>1) {
     lastValue=arr[1].substring(0, 1);
     } else {
     var subStr=arr[1].substring(0, 2);

     if(subStr.length<2) {    // SUPPOSE 1.20M
     lastValue=subStr+"0";
     } else {
     lastValue=subStr;
     }
     }
     finalVal=firstValue+"."+lastValue;
     } else  {
     finalVal=firstValue; //SUPPOSE POINT IS AFTER 3 DIGITS   EXAMPLE :: 121.1234
     }
     } else {
     finalVal=sNumber;
     }
     ret=finalVal+""+keyType
     } else  {
     ret=number
     }
     return ret;
     };
     })*/

    .filter('domain', [function () {
        return function (input) {
            var elements = angular.element('<a href="' + input + '"/>');
            return elements[0].hostname;
        };
    }])

    .filter('markupHTMLTags', ["$sce", function ($sce) {
        return function (text) {
            return $sce.trustAsHtml(text);
        };
    }])

    .filter('htmlToPlaintext', function () {
        return function (text) {
            return text ? String(text).replace(/<[^>]+>/gm, '') : '';
        }
    })

    .filter('elapsed', ["$filter", function ($filter) {
        return function (date) {
            if (!date) return;
            var time = Date.parse(date),
                timeNow = new Date().getTime(),
                difference = timeNow - time,
                seconds = Math.floor(difference / 1000),
                minutes = Math.floor(seconds / 60),
                hours = Math.floor(minutes / 60),
                days = Math.floor(hours / 24);
            if (days > 7) {
                return $filter('date')(time, "MMMM d 'at' h:mm a");
            } else if (days > 1) {
                return days + " days ago";
            } else if (days == 1) {
                return "1 day ago"
            } else if (hours > 1) {
                return hours + " hours ago";
            } else if (hours == 1) {
                return "an hour ago";
            } else if (minutes > 1) {
                return minutes + " minutes ago";
            } else if (minutes == 1) {
                return "a minute ago";
            } else {
                return "a few seconds ago";
            }
        }
    }])


    .filter('dateTimeFormat', ["$filter", function ($filter) {
        return function (date) {
            if (!date) return;
            var time = Date.parse(date),
                timeNow = new Date().getTime(),
                difference = timeNow - time,
                seconds = Math.floor(difference / 1000),
                minutes = Math.floor(seconds / 60),
                hours = Math.floor(minutes / 60),
                days = Math.floor(hours / 24);

            return $filter('date')(time, "MMMM d 'at' h:mm a");
        }
    }])

    .filter('formatDistance', function () {
        return function (distance) {
            var formattedDistance = parseFloat(distance);
            if (formattedDistance > 1) {
                formattedDistance = formattedDistance.toFixed(1) + "km";
            }
            else {
                formattedDistance = formattedDistance * 1000;
                formattedDistance = parseInt(formattedDistance) + "m";
            }
            return formattedDistance;
        };
    })

    .filter('cut', function () {
        return function (value, wordwise, max, tail) {
            if (!value) return '';

            max = parseInt(max, 10);
            if (!max) return value;
            if (value.length <= max) return value;

            value = value.substr(0, max);
            if (wordwise) {
                var lastspace = value.lastIndexOf(' ');
                if (lastspace != -1) {
                    //Also remove . and , so its gives a cleaner result.
                    if (value.charAt(lastspace - 1) == '.' || value.charAt(lastspace - 1) == ',') {
                        lastspace = lastspace - 1;
                    }
                    value = value.substr(0, lastspace);
                }
            }

            return value + (tail || ' ...');
        };
    })

    .filter('newlines', function () {
        return function (text) {
            return text.replace(/\n/g, '<br/>');
        }
    })

;

/*------ Application Services ------*/
angular.module('app')
    .factory('refreshService', ['$http', function ($http) {
        return {
            userData: function () {
                var url = 'angular/auth-json';
                return $http.post(url).then(function (response) {
                    return response.data;
                });
            },
            notification: function (page, perpage) {
                perpage = perpage > 1 ? perpage : 10;
                var url = 'api/notification';
                var postData = {
                    page: page,
                    perpage: perpage
                };
                return $http.post(url, postData).then(function (response) {
                    return response.data;
                });
            },
            top_search: function () {
                var url = 'api/top-search';
                var postData = {};
                return $http.post(url, postData).then(function (response) {
                    return response.data;
                });
            }
        };
    }])
    .factory('notify', [function () {
        return function (notification) {
            var type = notification.type ? notification.type : 'flip';
            var timeout = notification.timeout ? notification.timeout : 3000;
            /*
             | Placement of the notification
             | top, top-left, top-right, bottom-right
             */
            var position = notification.position ? notification.position : 'top-right';
            var message = notification.message; // Message to display inside the notification
            // error, info, warning
            var color = notification.color ? notification.color : 'info';

            switch (type) {
                case 'bar':
                    // Show an bar notification attached to top and bottom of the screen
                    $('body').pgNotification({
                        style: 'bar',
                        message: message,
                        position: position,
                        timeout: timeout,
                        type: color
                    }).show();
                    break;
                case 'flip':
                    // Show a flipping notification animated
                    // using CSS3 transforms and animations
                    $('body').pgNotification({
                        style: 'flip',
                        message: message,
                        position: position,
                        timeout: timeout,
                        type: color
                    }).show();
                    break;
                case 'simple':
                    // Simple notification having bootstrap's .alert class
                    $('body').pgNotification({
                        style: 'simple',
                        message: message,
                        position: position,
                        timeout: timeout,
                        type: color
                    }).show();
                    break;
            }
        };


    }])
    //Local Storage Service
    //Author:Alapan Chatterjee; Date:25-01-2017
    .service('myLocalStorage', ["localStorageService", function (localStorageService) {
        return {
            submit: function (key, val) {
                return localStorageService.set(key, val);
            },
            getItem: function (key) {
                return localStorageService.get(key);
            },
            getAllKeys: function () {
                return localStorageService.keys();
            },
            removeItem: function (key) {
                return localStorageService.remove(key);
            },
            clearAll: function () {
                return localStorageService.clearAll();
            }
        };
    }])
    //Distance Calculaton between two given sets of ltitude and longitude
    //Author:Alapan Chatterjee; Date:26-01-2017
    .service('haversineDistanceCalculationService', function () {
        return {

            findDistance: function (latitudeFrom, longitudeFrom, latitudeTo, longitudeTo) {
                var radius = 6371;
                var toRad = function (x) {
                    return (x * Math.PI) / 180;
                };
                if (latitudeFrom == '' || longitudeFrom == '' || latitudeTo == '' || longitudeTo == '') {
                    return null;
                }
                var x1 = latitudeTo - latitudeFrom;
                var dLat = toRad(x1);
                var x2 = longitudeTo - longitudeFrom;
                var dLon = toRad(x2);
                var a = Math.sin(dLat / 2) * Math.sin(dLat / 2) + Math.cos(toRad(latitudeFrom)) * Math.cos(toRad(latitudeTo)) * Math.sin(dLon / 2) * Math.sin(dLon / 2);
                var c = 2 * Math.atan2(Math.sqrt(a), Math.sqrt(1 - a));
                var d = radius * c;
                return d;
            }
        };
    })
    .service('userDataService', ["$stateParams", "$http", "$state", function ($stateParams, $http, $state) {   // load user details  and update user date .

        var userData = [];

        return {
            loadUserData: function () {
                var url = 'angular/profileList';
                var data = {}
                if ($stateParams.username) {
                    data = {username: $stateParams.username};
                }
                return $http.post(url, data).then(function (response) {
                    userData = response.data;
                    return userData;
                }, function (error) {

                    window.location.href = "error-404";
                });
            },
            updateUserData: function (user_points) {
                userData.user.points = user_points;
            },
            getData: function () {
                return userData;
            }

        };
    }])

    .service('inArray', function () {

        this.arrayIndexOf = function (array, value) {
            var index = -1;
            if (angular.isArray(array)) {
                angular.forEach(array, function (val, key) {
                    if (val == value) {
                        index = key;
                        return false;
                    }
                });
            }
            return index;
        }
    });


/* POST CARD SHARE POPUP SHOW HIDE */
$("body").on("click", ".shrClk", function () {
    var shrClk = $(".shrClk");
    shrClk.parent().children(".subOverlay").hide();
    shrClk.parent().children(".sub").hide();
    $(this).parent().children(".subOverlay").show();
    $(this).parent().children(".sub").show();
});
$("body").on("click", ".subOverlay", function () {
    $(this).hide();
    $(this).parent().children(".sub").hide();
});
$("body").on("click", ".cardSmNav .sub ul li", function () {
    var shrClk = $(".shrClk");
    shrClk.parent().children(".subOverlay").hide();
    shrClk.parent().children(".sub").hide();
});

/* Report comment box  */
$("body").on("click", ".comntInlineModal a", function () {
    if (!$(this).parent().children().children(".reporPopupVW").is(":visible")) {
        $('.reporPopupVW').hide();
        $(this).parent().children().children(".reporPopupVW").show();
    } else {
        $('.reporPopupVW').hide();
    }
});
$(document).click(function (e) {
    var containerList = $('.comntInlineModal a');
    if (!containerList.is(e.target) && containerList.has(e.target).length == 0) {
        $('.reporPopupVW').hide();
    }
});
$("body").on("click", ".cardSmNav.last .moreBtnN", function () {
    $('.profileCommentFooter.addNew .left, .profileCommentFooter.addNew .right').addClass("show2");
    $(this).parent().parent(".right").removeClass("show2");
});
$("body").on("click", ".subOverlay", function () {
    $('.profileCommentFooter.addNew .left, .profileCommentFooter.addNew .right').removeClass("show2");
});

/* VIDEO PLAY PAUSE */
/*
 $("body").on("mouseenter",".blockContent .profileCommentBox", function() {
 //console.log("Video Play");
 if($(this).find(".videoTag").is(":visible")){
 $(this).find(".videoTag").parent().children(".customPlayPause").addClass("out");
 $(this).find(".videoTag")[0].play();
 }else if($(this).find(".iframeTag").is(":visible")){
 //console.log("Youtube Play");
 //$('.iframeTag')[0].contentWindow.postMessage('{"event":"command","func":"' + 'playVideo' + '","args":""}', '*');
 }
 });
 $("body").on("mouseleave",".blockContent .profileCommentBox", function() {
 //console.log("Video Pause");
 $(".videoTag")[0].pause();
 $(".videoTag").parent().children(".customPlayPause").removeClass("out");
 //$('.iframeTag')[0].contentWindow.postMessage('{"event":"command","func":"' + 'pauseVideo' + '","args":""}', '*');
 });
 */
/*
 $("body").on("mouseover",".profileCommentBox", function () {
 $(this).find('.iframeTag')[0].contentWindow.postMessage('{"event":"command","func":"' + 'playVideo' + '","args":""}', '*');
 });
 $("body").on("mouseout",".profileCommentBox", function () {
 $(this).find('.iframeTag')[0].contentWindow.postMessage('{"event":"command","func":"' + 'pauseVideo' + '","args":""}', '*');
 });

 */
/*
 $("body").on("click",".saveDel", function () {
 if(!$(this).children(".saveIcon").is(":visible")){
 $(this).children(".saveIcon").css({"display":"inline-block"});
 $(this).children(".savedIcon").css({"display":"none"});
 }else{
 $(this).children(".saveIcon").css({"display":"none"});
 $(this).children(".savedIcon").css({"display":"inline-block"});
 }
 });
 */

/* Suggested Users Remove */
$("body").on("click", ".suggestedUsers .cls", function () {
    $(".suggestedUsers").remove();
});

$("body").on("click", ".suggestedUsers .floBtn .followBtn", function () {
    if (!$(this).children(".ico").is(":visible")) {
        $(this).children("span").css({"display": "none"});
        $(this).children(".ico").css({"display": "block"});
    } else {
        $(this).children("span").css({"display": "block"});
        $(this).children(".ico").css({"display": "none"});
    }
});

$("body").on("click", ".exploreDropdown .navTl", function () {
    if (!$(this).next("ul").is(":visible")) {
        $(this).next("ul").css({"display": "block"});
    } else {
        $(this).next("ul").css({"display": "none"});
    }
});

$(document).click(function (e) {
    var exploreNAV = $('.exploreDropdown .navTl');
    if (!exploreNAV.is(e.target) && exploreNAV.has(e.target).length == 0) {
        $('.exploreDropdown ul').css({"display": "none"});
    }
});

//Profile userlinks
/* $("body").on("click", ".usrLinkArrow", function () {
    $(this).addClass("sl");
	$(".profileUserShow").addClass("vw");
    $(".userOwnLinksArea").css({"height": "auto"});
});
$("body").on("click", ".usrLinkArrow.sl", function () {
    $(this).removeClass("sl");
	$(".profileUserShow").removeClass("vw");
    $(".userOwnLinksArea").css({"height": "1px"});
}); */

$("body").on("click", ".profileUserShow .userBox", function () {
	$(".profileUserShow").addClass("vw");
	$(".usrLinkArrow").addClass("sl");
	$(".userOwnLinksArea").css({"height": "auto"});
});
$("body").on("click", ".profileUserShow.vw", function () {
	$(".profileUserShow").removeClass("vw");
	$(".usrLinkArrow").removeClass("sl");
	$(".userOwnLinksArea").css({"height": "1px"});
});

$("body").on("click", ".profileUserShow", function () {
	if($(window).width() <= 641){
		$(".profileUserShow").addClass("vw");
		$(".usrLinkArrow").addClass("sl");
		$(".userOwnLinksArea").css({"height": "auto"});
	}
});
$("body").on("click", ".profileUserShow.vw", function () {
	if($(window).width() <= 641){
		$(".profileUserShow").removeClass("vw");
		$(".usrLinkArrow").removeClass("sl");
		$(".userOwnLinksArea").css({"height": "1px"});
	}
});

$("body").on("click", ".customSelbox", function () {
	$(this).next(".customSelectListCont").fadeIn(300);
	$("html").addClass("categoryovr");
});
$("body").on("click", ".selectOvrlay, .customSelectList .cls", function () {
	$(".customSelectListCont").fadeOut(300);
	$("html").removeClass("categoryovr");
});
$("body").on("click", ".customSelectList ul li", function () {
	/*var thisvalue = $(this).html();
	$(this).parent().parent().parent().parent().children().children(".form-control").html(thisvalue);*/
	$(".customSelectListCont").fadeOut(300);
	$("html").removeClass("categoryovr");
});

$(window).on('load', function(){
	var headr = $('.coverPhoto');
	var range = 120;
	$(window).on('scroll', function(){
		var scrollTop = $(this).scrollTop(),
			  height = headr.height(),
			  offset = height / 2,
			  calc = 1 - (scrollTop - offset + range) / range;
			  calc2 = 1 - calc;
			  
		  $('.header').css({'background':'rgba(255,255,255,' +calc2+')'});

		  if (calc2 <= 1) {
			$('.header').css({'background':'rgba(255,255,255,0)'});
		  } else if ( calc2 >= 2 ) {
			$('.header').css({'background':'rgba(255,255,255,1)'});
		  }  
	});
});
/*
 * Code for comment box.
 */
(function () {
    'use strict';

    CommentBoxCtrl.$inject = ["$scope", "$http", "$rootScope", "$timeout", "inArray", "$state", "postOpened", "notify"];
    angular.module('app')
        .directive('commentBox', function () {
            return {
                restrict: 'E',
                templateUrl: 'tpl.comment-box',
                replace: true,
                controller: 'CommentBoxCtrl',
                // controllerAs: 'ctrl'
            };
        })
        .controller('CommentBoxCtrl', CommentBoxCtrl);

    function CommentBoxCtrl($scope, $http, $rootScope, $timeout, inArray, $state, postOpened, notify) {
        // Initialize comments array.
        $scope.comments = [];

        $scope.typingText = '';
        $scope.opened_nodes = [];

        $scope.postComment = function (post_id, child_post_id) {
            var $parentMsg = $("#parentMsg");
            var msg = $parentMsg.val();
            var message = msg.replace(/(?:\r\n|\r|\n)/g, '<br />');

            if (message == '') {
                $parentMsg.attr('required', true);
                alert('Please write something!');
                return false;
            }

            /*if ($scope.activeItem != 2) {
                setTimeout(function () {
                    //  for new comments
                    $scope.sortByComments(2, post_id);
                }, 500);
            }*/
        
            $scope.showSendBtn = false;
            //For Loading effect.
            $scope.showSendingBtn = true;
            
            var postData = {
                parent_id: 0,
                message: message,
                post_id: post_id,
                child_post_id: child_post_id
            };
            $http.post('comments/post-comment', postData)
                .then(function (response) {
                    $("#parentMsg").val('');
                    // for reset message box
                    $scope.message = '';
                    $scope.showSendBtn = true;
                    // Remove loading effect .
                    $scope.showSendingBtn = false;
                    // Load comments.
                    if ($scope.activeItem == 1) {
                        $scope.sortByComments(2, post_id);
                    }                    
                }
            );
        };

        // reply post comments.
        var cnt = 1;
        $scope.postReplyComment = function (id) {
            var elm = angular.element("#commentfrm" + id);

            var $textarea = elm.find("textarea[name='message']");

            var msg = $textarea.val();
            var message = msg.replace(/(?:\r\n|\r|\n)/g, '<br />');

            if (message == '') {
                $textarea.attr('required', true);
                alert('Please write something!');
                return false;
            }

            var parent_id = elm.find("input[name='parent_id']").val();
            var child_post_id = elm.find("input[name='child_post_id']").val();
            var post_id = elm.find("input[name='post_id']").val();

            $("#send" + id).hide();
            $("#sending" + id).show();
            $("#sendBtnLoading" + id).prop('disabled', true);

            var postData = {
                message: message,
                parent_id: parent_id,
                child_post_id: child_post_id,
                post_id: post_id
            };
            $http.post('comments/post-child-comment', postData)
                .then(function (response) {
                    $("#sendBtnLoading" + id).prop('disabled', false);
                    $("#send" + id).show();
                    $("#sending" + id).hide();
                }
            );
            elm.find("textarea[name='message']").val('');
            elm.find("textarea[class='modalMsg']").val('');
            $scope.displayBlock = 0;
        };

        $scope.deleteComments = function (comment_id, parent_id, postId) {            
            var postData = {
                comment_id: comment_id,
                parent_id: parent_id,
                postId: postId
            };
            $http.post('comments/delete', postData)
                .then(function (response) {
                    $('#commentDeleteModal').modal('toggle');
                },
                function (error) {
                    var notification = {
                        'message': 'Sorry! unable to process your request.',
                        'color': 'error',
                        'timeout': 5000
                    };
                    notify(notification);
                }
            );
        };

        $scope.moreComments = function (post_id, sortType) {
            // var totalCommentsOnView = $scope.comments.length < 10 ? $scope.comments.length : 10;
            var totalCommentsOnView = $scope.comments.length;
            var offsetx = totalCommentsOnView;

            $http({
                method: "POST",
                url: "angular/showLoadMoreComments",
                params: {
                    post_id: post_id,
                    offsetx: offsetx,
                    sortType: sortType,
                    showAll: 'showAll'
                }
            }).then(function (response) {
                var getComments = response.data.allComments;
                var existingComments = [];
                if ($scope.comments && $scope.comments.length) {
                    $scope.comments.forEach(function(c){
                        existingComments.push(c.id);
                    })
                }
                if (angular.isArray(getComments)) {
                    angular.forEach(getComments, function (comment) {
                        if (existingComments.indexOf(comment.id) === -1) {
                            comment.message = emojione.toImage(comment.message);
                            $scope.comments.push(comment);
                        }                        
                    });
                }
                $scope.commentLimitTo = response.data.postParentComment;
                $scope.postTotalComment = response.data.postTotalComment;
            });
        };

        $scope.sortComments = '-created_at';
        $scope.displayBlock = 0;
        $scope.showdiv = 0;

        $scope.showReplyBox = function (commentId, postId, activeItem) {

            var dataArray = $scope.comments;			
            // Close the reply box.
			if($("#cmmtextAreaBlock"+commentId).is(":visible")) {
				
				var allFullComments = $scope.spliceNodeAtpos(commentId,$scope.fullComments);
                $scope.comments = allFullComments;
				$("#cmmtextAreaBlock"+commentId).css("display","none");
			}
            // Open the reply box.
            else {
				$http({
                    method: 'POST',
                    url: "angular/loadChildComments",
                    data: {
                        commentId: commentId, 
                        post_id: postId,
                        activeItem : activeItem
                    }
                })
                .then(function (response) {
                    var all_comments = response.data.allComments;
                    var allFullComments = addReplies(commentId, $scope.fullComments, all_comments);
                    $scope.comments = allFullComments;
                    
                }, function (error) {
                    console.log('Error has occurred');
                });
				
				$(".cmmtextAreaType").css("display","none");
				$("#cmmtextAreaBlock"+commentId).css("display","block");
			}
		   
            $scope.commentBoxId = commentId;
           // $scope.displayBlock = !$scope.displayBlock;
        };

        // Recusrively find insertion postion and push data into array.
         function addReplies(nodeId, allCommentObj, objToBePushed) {
            
            angular.forEach(allCommentObj, function(value,index){

                if(value.id == nodeId) {
                    var existingComment = [];
                    if(value.child_comment.length > 0) {
                        value.child_comment.forEach(function(v) {
                            existingComment.push(v.id);
                        })
                    }
                    if (objToBePushed.length > 0) {
                        // Push the reply to child comment.
                        angular.forEach(objToBePushed, function(v, k) {
                            if (existingComment.indexOf(v.id) === -1) {
                                value.child_comment.push(v);
                                value.showRel = true;
                            }
                        });
                    }
                    else {
                        value.showRel = true;
                    }
                }
                else {
                    if(value.child_comment.length > 0) {
                        addReplies(nodeId, value.child_comment, objToBePushed);
                    }
                }                
            });            
            return allCommentObj; 
        }
        
         // Recusrively find child node postion and child node replace with empty data.
        $scope.spliceNodeAtpos = function (nodeId, allCommentObj) {
            
            angular.forEach(allCommentObj, function(value,index) {
                if(value.id == nodeId){                           
                    value.child_comment = [];
                    value.showRel = false;
                }
                else {
                    if(value.child_comment.length > 0){
                        $scope.spliceNodeAtpos(nodeId, value.child_comment);
                    }
                }                
            });            
            return allCommentObj; 
        }

        // Comments load more
        $scope.sortByComments = function (sortType, postId) {
           // $scope.comments = {};
            $scope.commentLimitTo = $scope.loadMoreCommentsLimit;
            $scope.showCommentLoader = 1;
            $scope.fullComments = [];
            var sendData=[];

            switch (sortType) {
                case 1 :
                    $scope.sortComments = "-total_upvotes";

                    $http({
                        method: 'POST',
                        url: 'angular/showLoadMoreComments',
                        params: {sortType: sortType, post_id: postId, offsetx: 0}
                    }).then(function (response) {

                        // for emoji
                        var sendData=[];
                        var responseData=response.data.allComments;
                        if(angular.isArray(responseData)){
                            angular.forEach(responseData,function(val,key)
                            {
                                responseData[key].message = emojione.toImage(responseData[key].message);
                                sendData.push(responseData[key]);
                                $scope.fullComments.push(responseData[key]);
                            });
                        }
                        $scope.comments = sendData;
                        
                        $scope.fullComments=sendData;  // store data temp array 

                        $scope.post.postParentComment = response.data.postParentComment;
                        $scope.postTotalComment = response.data.postTotalComment;

                        $timeout(function () {
                            $(".modal-split-view .split-list .item").removeClass('highLight');
                        }, 100);
                        $timeout(function () {
                            $scope.showCommentLoader = 0;
                        }, 1600);

                    },
                    function (error) {
                        console.log('Error has occurred');
                    });
                $scope.activeItem = 1;
                break;
                case 2 :
                    $scope.sortComments = "-created_at";


                    $http({
                        method: 'POST',
                        url: 'angular/showLoadMoreComments',
                        params: {sortType: sortType, post_id: postId, offsetx: 0}
                    }).then(function (response) {
                        // for emoji
                        var responseData=response.data.allComments;
                        if(angular.isArray(responseData)){
                            angular.forEach(responseData,function(val,key)
                            {
                                responseData[key].message = emojione.toImage(responseData[key].message);
                                sendData.push(responseData[key]);
                                $scope.fullComments.push(responseData[key]);
                            });
                        }
                    
                        $scope.comments = sendData;
                        $scope.fullComments = sendData; // store data temp array 
                        
                        $scope.post.postParentComment = response.data.postParentComment;
                        $scope.postTotalComment = response.data.postTotalComment;

                        setTimeout(function () {
                            $(".modal-split-view .split-list .item").removeClass('highLight');
                        }, 200);

                        /*$timeout(function () {
                            $scope.showCommentLoader = 0;
                        }, 1600);*/
                        $scope.activeItem = 2;
                        $scope.showCommentLoader = 0;
                    },
                    function (error) {
                        $scope.activeItem = 2;
                        $scope.showCommentLoader = 0;
                        // console.log('Error has occurred');
                    }
                );
                // $scope.activeItem = 2;
                break;
            }
        };

        $scope.showCommentDeleteModal = function (comment_id, post_id, activeItem) {

            $rootScope.commentID = comment_id;
            $rootScope.postID = post_id;
            $rootScope.activeItem = activeItem;
        };

        $scope.cancel = function () {
            $('#commentDeleteModal').modal('toggle');
        };

        $scope.commentUpAndDownVote = function (commentID, activityType, post_id) {
            var postData = {
                commentID: commentID,
                activityType: activityType,
                post_id: post_id
            };
            $http.post('comments/vote', postData)
            .then(function (response) {
                // Nothing for now.
            });
        };

        /*------- Code for socket --------*/
        if ($scope.loggedIn) {
            // Listen for usersTypingInChannel event
            postOpened.listenUsersTypingInChannelEvent(function(response) {                
                // console.log(response);
                $scope.typingText = makeUserTypingText(response);
            });
            // Listen for comment post event.
            postOpened.listenCommentPostedEvent(function(response) {                
                // console.log(response);
                CommentPosted(response);
            });
            // Listen for reply posted event.
            postOpened.listenReplyPostedEvent(function(response) {
                // console.log(response);
                ReplyPosted(response);
            });
            // Listen for comment deleted event.
            postOpened.listenCommentDeletedEvent(function(response) {
                // console.log(response);
                CommentDeleted(response);
            });
            // Listen for comment voted event.
            postOpened.listenCommentVotedEvent(function(response) {
                // console.log(response);
                CommentVoted(response);
            });
        }
        /*-------------- Operations -----------------*/
        function makeUserTypingText(response) {
            var typingText = '';
            if (!$scope.loggedIn) {
                return typingText;
            }
            var users = response.users;
            if (users) {
                // Remove current user.
                users.some(function (el, index) {
                    if (el.id == $scope.user.id) {
                        users.splice(index, 1);
                    }
                });
            }
            var userLength = users.length;
            if (userLength > 0) {
                if (userLength == 1) {
                    typingText = response.users[0].name + ' is typing...';
                }
                else if (userLength == 2) {
                    typingText = response.users[0].name + ' and ' + response.users[1].name + ' are typing...';
                }
                else {
                    typingText = response.users[0].name + ' and ' + (userLength - 1) + ' others are typing...';
                }
            }
            return typingText;
        }

        function getRootCommentID(comments, comment_id) {
            var root_comment_id;
            if (comments) {
                comments.every(function(comment) {
                    if (comment.id == comment_id) {
                        root_comment_id = comment.id;
                        return false;
                    }
                    else if(comment.child_comment.length > 0) {
                        // returns id or undefined
                        var childId = getRootCommentID(comment.child_comment, comment_id);
                        if (childId) {
                            root_comment_id = comment.id;
                            return false;
                        }
                    }
                    return true;
                });
            }
            return root_comment_id;
        }

        function CommentPosted(response) {
            // Add last reply to comments array.
            if ($scope.activeItem == 2) {
                var comment = response.data.lastComment;
                var existingComment = [];
                if ($scope.comments.length > 0) {
                    $scope.comments.forEach(function(v) {
                        existingComment.push(v.id);
                    });
                }
                if (existingComment.indexOf(comment.id) === -1) {
                    comment.message = emojione.toImage(comment.message);
                    $scope.comments.unshift(comment);
                }                
            }

            // For postcard number of comments updates
            if (angular.isArray($scope.commonData.allPosts)) {
                angular.forEach($scope.commonData.allPosts, function (value, key) {
                    if (value.id == response.data.lastComment.post_id) {
                        value.totalComments = response.data.postParentComment;
                        return false;
                    }
                });
            }

            $scope.postTotalComment = response.data.postTotalComment;
            if ($scope.post) {
                $scope.post.postParentComment = response.data.postParentComment;
            }
            else {
                $scope.post = {};
                $scope.post.postParentComment = response.data.postParentComment;
            }
        }

        function ReplyPosted(response) {
            var lastComment = response.data.lastComment;
            var isCommenter = $scope.user.id == lastComment.user_id;
            // Add last reply to comments array.
            if ($scope.activeItem == 2 || isCommenter) {                
                lastComment.message = emojione.toImage(lastComment.message);
                var isNotified = pushReplyComment($scope.comments, lastComment, isCommenter);
                // Highlight root comment if not highlighted already
                // in case not parent node is not available.
                // console.log('isNotified: ' + isNotified + ' isCommenter: ' + isCommenter);
                if (!isNotified && !isCommenter) {
                    // Add class for background.
                    if ($scope.comments) {
                        var rootNode = $('#pc' + lastComment.root_comment_id);
                        addAndRemoveBackground(rootNode, 5000);
                    }
                }

            }               

            // For postcard update all posts array.
            if (angular.isArray($scope.commonData.allPosts)) {
                angular.forEach($scope.commonData.allPosts, function (value, key) {
                    if (value.id == response.data.lastComment.post_id) {
                        $scope.commonData.allPosts[key].totalComments = response.data.postParentComment;
                        return false;
                    }
                });
            }

            $scope.postTotalComment = response.data.postTotalComment;
            if ($scope.post) {
                $scope.post.postParentComment = response.data.postParentComment;
            }
            else {
                $scope.post = {};
                $scope.post.postParentComment = response.data.postParentComment;
            }
        }

        function CommentDeleted(response) {
            var comment_id = response.data.comment_id,
                    parent_id = response.data.parent_id,
                    post_id = response.post_id,
                    user_id = response.data.user_id;
            // Check if commenter.
            var isCommenter = $scope.user.id == user_id;
            if ($scope.activeItem == 2 || isCommenter) {
                // Remove comment from array.
                // console.log($scope.comments);
                removeComment($scope.comments, comment_id);
                // update no of child comment to $scope.
                countChildComment($scope.comments, comment_id, parent_id);
            }

            // Update all posts array.
            if (angular.isArray($scope.commonData.allPosts)) {
                angular.forEach($scope.commonData.allPosts, function (value, key) {
                    if (value.id == post_id) {
                        $scope.commonData.allPosts[key].totalComments = response.data.postParentComment;
                        return false;
                    }
                });
            }
            $rootScope.popUpTotalComments = response.data.postTotalComment;
            if ($scope.post) {
                $scope.post.postParentComment = response.data.postParentComment;
            }
            else {
                $scope.post = {};
                $scope.post.postParentComment = response.data.postParentComment;
            }
        }

        function CommentVoted(response) {
            var activityType = response.data.activityType,
                comment = response.data.comment,
                user_id = response.data.user_id;
            // Check if commenter.
            var isCommenter = $scope.user.id == user_id;
            if ($scope.activeItem == 2 || isCommenter) {
                // add vote to comments array.
                voteComment($scope.comments, comment, user_id, activityType);
            }

            /*var commentsArray = $scope.comments;
            if (angular.isArray(commentsArray)) {
                angular.forEach(commentsArray, function (value, key) {
                    if (value.id == comment.id) {
                        $scope.comments[key].total_upvotes = parseInt(comment.upvotes)
                                                            - parseInt(comment.downvotes);
                    }
                });
            }*/
            // Update user total comments on profile tab
            if ($state.current.name == 'profile' || $state.current.name == 'account') {
                if ($scope.userData && $scope.userData.id == comment.user_id) {
                    $scope.userData.points = response.data.post_user_points;
                }
            }

            if (angular.isArray($scope.post.getUser)) {
                angular.forEach($scope.post.getUser, function (value, key) {
                    if (value.id == comment.user.id) {
                        $scope.post.getUser[key].points = comment.user.points;
                    }
                });
            }
        }

        /*------------ functions for comment array ----------------*/
        // Remove comment from comments array.
        function removeComment(comments, comment_id) {
            angular.forEach(comments, function(value, index){
                if(value.id == comment_id) {                           
                    comments.splice(index, 1);                                                       
                }
                else {
                    if(value.child_comment.length > 0){
                        removeComment(value.child_comment, comment_id);
                    }
                }  
            });
            return comments;
        }

        // Update no of child comment to $scope.
        function countChildComment(comments, comments_id, parent_id) {
            angular.forEach(comments, function(value, index){
                if(value.id == parent_id) {
                    value.count_child = value.child_comment.length;                                            
                }
                else {
                    if(value.child_comment.length > 0){
                        countChildComment(value.child_comment, comments_id, parent_id);
                    }
                }                
            });
            return comments;
        }

        // Add reply to comments array.
        function pushReplyComment(comments, lastComment, isCommenter) {
            var isNotified = false;
            angular.forEach(comments, function (comment, index) {
                if(comment.id == lastComment.parent_id) {
                    // Check whether to show in real time.
                    if (comment.showRel || isCommenter) {
                        var existingComment = [];
                        if(comment.child_comment.length > 0) {
                            comment.child_comment.forEach(function(v) {
                                existingComment.push(v.id);
                            })
                        }
                        // Push the reply to child comment.
                        if (existingComment.indexOf(lastComment.id) === -1) {
                            comment.child_comment.unshift(lastComment);
                            // Highlight new comment if parent node expanded.
                            if (!isCommenter) {
                                isNotified = true;
                                setTimeout(function() {
                                    // Add class for background
                                    var commentNode = $('#pc' + lastComment.id);
                                    addAndRemoveBackground(commentNode, 5000);
                                }, 100);
                            }
                        }
                    }
                    else {
                        isNotified = true;
                        // Add class for background
                        var parentNode = $('#pc' + lastComment.parent_id);
                        addAndRemoveBackground(parentNode, 5000);
                    }
                    comment.count_child  =  lastComment.parentCommentTotalchildPost;
                }
                else {
                    if(comment.child_comment.length > 0) {
                        isNotified = pushReplyComment(comment.child_comment, lastComment, isCommenter);
                    }
                }
            });
            return isNotified;
        }
        // Add upvote/downvote to comments array.
        function voteComment(comments, comment, comment_user_id, activityType) {
            var comment_id = comment.id;
            angular.forEach(comments, function(value,index) {
                if(value.id == comment_id) {
                    var isCommenter = $scope.user.id == comment_user_id;
                    if (activityType == 1) {
                        // For cancel comment upvotes ....
                        if(value.isUpvote == 'Y') { 
                            if (isCommenter) {
                                value.isUpvote = 'N';
                            }                            
                            // value.upvotes = parseInt(value.upvotes) - 1;
                        }
                        else { 
                            if (isCommenter) {
                                value.isUpvote = 'Y';
                            }
                            // value.upvotes = parseInt(value.upvotes)+1
                            if(value.isDownvote == 'Y') {
                                if (isCommenter) {
                                    value.isUpvote = 'N';
                                }
                                // value.downvotes = parseInt(value.downvotes)-1;
                            }
                        }

                    } else {
                       // For cancel comment downvotes .... 
                        if(value.isDownvote == 'Y') {
                            if (isCommenter) {
                                value.isUpvote = 'N';
                            }
                            // value.downvotes = parseInt(value.downvotes) - 1;
                        }
                        else {
                            if (isCommenter) {
                                value.isUpvote = 'Y';
                            }
                            // value.downvotes = parseInt(value.downvotes) + 1;                            
                            if(value.isUpvote == 'Y') {
                                if (isCommenter) {
                                    value.isUpvote = 'N';
                                }
                                // value.upvotes = parseInt(value.upvotes) - 1;
                            }
                        }
                    }
                    value.upvotes = comment.upvotes;
                    value.downvotes = comment.downvotes;
                    return '';
                }
                else {
                    if(value.child_comment.length > 0){
                        voteComment(value.child_comment, comment, comment_user_id, activityType);
                    }
                }                
            });
        }

        // Add class for background and remove after specified time.
        function addAndRemoveBackground(selector, time) {
            selector.addClass('sl');
            setTimeout(function(){
                selector.removeClass('sl');
            }, time);
        }
    }
})();

/*
 * @author <tuhin.tsm.mandal@gmail.com>
 */
(function () {
    'use strict';
 
    socket.$inject = ["$rootScope"];
    angular
        .module('app')
        .factory('socket', socket);
 
    function socket($rootScope) {
        var socket = io.connect();
        
        var service = {};
        service.init = init;
        service.on = on;
        service.emit = emit;
        service.removeAllListeners = removeAllListeners;
 
        return service;

        function init() {
            socket.removeAllListeners();
        }
 
        function on(eventName, callback) {
            socket.on(eventName, function () {  
                var args = arguments;
                $rootScope.$apply(function () {
                    callback.apply(socket, args);
                });
            });
        }
 
        function emit(eventName, data, callback) {
            socket.emit(eventName, data, function () {
                var args = arguments;
                $rootScope.$apply(function () {
                    if (callback) {
                        callback.apply(socket, args);
                    }
                });
            })
        }

        function removeAllListeners(eventName) {
            socket.removeAllListeners(eventName);
        }
    }
})();
/*
 * @author <tuhin.tsm.mandal@gmail.com>
 */
(function () {
    'use strict';
 
    postOpened.$inject = ["socket", "localStorageService"];
    angular
        .module('app')
        .factory('postOpened', postOpened);

    function postOpened(socket, localStorageService) {
    	var service = {};

        service.init = init;

        service.listenUsersTypingInChannelEvent = listenUsersTypingInChannelEvent;
        
        service.userFollowedEvent = userFollowedEvent;
        service.userViewedEvent = userViewedEvent;
        service.userPointUpdatedEvent = userPointUpdatedEvent;

        service.trackPostOpened = trackPostOpened;

        service.listenPostViewUpdatedEvent = listenPostViewUpdatedEvent;
        service.listenPostPointUpdatedEvent = listenPostPointUpdatedEvent;
        service.listenPostUpvotedEvent = listenPostUpvotedEvent;
        service.listenPostSharedEvent = listenPostSharedEvent;
        service.listenPostBookmarkedEvent = listenPostBookmarkedEvent;

        service.listenCommentPostedEvent = listenCommentPostedEvent;
        service.listenCommentDeletedEvent = listenCommentDeletedEvent;
        service.listenReplyPostedEvent = listenReplyPostedEvent;
        service.listenCommentVotedEvent = listenCommentVotedEvent;
 
        return service;

        function init() {
            // socket.removeAllListeners('users_typing_here');
            
            socket.removeAllListeners('user-followed');
            socket.removeAllListeners('user-viewed');
            socket.removeAllListeners('user-point-updated');

            socket.removeAllListeners('post-view-updated');
            socket.removeAllListeners('post-point-updated');
            socket.removeAllListeners('post-upvoted');
            socket.removeAllListeners('post-shared');
            socket.removeAllListeners('post-bookmarked');

            /*socket.removeAllListeners('comment-posted');
            socket.removeAllListeners('reply-posted');
            socket.removeAllListeners('comment-deleted');
            socket.removeAllListeners('comment-voted');*/
        }

        function trackPostOpened(post_id) {
            var opened_posts = localStorageService.get('opened_posts');
            opened_posts = JSON.parse(opened_posts);
            if (opened_posts) {
                if (typeof opened_posts[post_id] == 'undefined') {
                    opened_posts[post_id] = 1;
                }
                else {
                    opened_posts[post_id] += 1;
                }
            }
            else {
                opened_posts = {};
                opened_posts[post_id] = 1;
            }
            // Check current key.
            var opened_posts2 = localStorageService.get('opened_posts');
            if (opened_posts2) {
                // console.log(opened_posts2);
                opened_posts2 = JSON.parse(opened_posts2);
                if (opened_posts['sk'] == opened_posts2['sk']) {
                    // Save to local storage.
                    opened_posts['sk'] = uuid4();
                    // console.log(opened_posts);
                    localStorageService.set('opened_posts', JSON.stringify(opened_posts));
                }
                else {
                    // Try again.
                    trackPostOpened(post_id);
                }
            }
            else {
                // Save to local storage.
                opened_posts['sk'] = uuid4();
                // console.log(opened_posts);
                localStorageService.set('opened_posts', JSON.stringify(opened_posts));
            }
        }

        function listenUsersTypingInChannelEvent(callback) {
            socket.on('users_typing_here', function(response) {
                callback(response);
            });
        }

        /*--------- For real time data ---------*/

        function userFollowedEvent(callback) {
            socket.on('user-followed', function(response) {
                callback(response);
            });
        }
        
        function userViewedEvent(callback) {
            socket.on('user-viewed', function(response) {
                callback(response);
            });
        }

        function userPointUpdatedEvent(callback) {
            socket.on('user-point-updated', function(response) {
                callback(response);
            });
        }

        function listenPostViewUpdatedEvent(callback) {
            socket.on('post-view-updated', function(response) {
                callback(response);
            });
        }
        
        function listenPostPointUpdatedEvent (callback) {
            socket.on('post-point-updated', function(response) {
                callback(response);
            });
        }

        function listenPostSharedEvent (callback) {
            socket.on('post-shared', function(response) {
                callback(response);
            });
        }

        function listenPostUpvotedEvent (callback) {
            socket.on('post-upvoted', function(response) {
                callback(response);
            });
        }
        
        function listenPostBookmarkedEvent (callback) {
            socket.on('post-bookmarked', function(response) {
                callback(response);
            });
        }

        /*------- For comments ---------*/
        function listenCommentPostedEvent (callback) {
            socket.on('comment-posted', function(response) {
                callback(response);
            });
        }

        function listenReplyPostedEvent (callback) {
            socket.on('reply-posted', function(response) {
                callback(response);
            });
        }

        function listenCommentDeletedEvent (callback) {
            socket.on('comment-deleted', function(response) {
                callback(response);
            });
        }

        function listenCommentVotedEvent (callback) {
            socket.on('comment-voted', function(response) {
                callback(response);
            });
        }
    }
})();
(function () {
    'use strict';
 
        TypingIndicator.$inject = ["socket"];
    angular
        .module('app')
        .factory('TypingIndicator', TypingIndicator);

        function TypingIndicator(socket) {

            var setTypingState = _.debounce(function(channel, user, isTyping) {
                // console.info('set typing state: '+ isTyping +' through socket for post: ' +channel);
                socket.emit('user typing', {
                    user: user,
                    channel: channel,
                    isTyping: isTyping
                });
            }, 400);

            var startTyping = function(channel, user) {

                setTypingState(channel, user, true)
            };

            var stopTyping = function(channel, user) {

                setTypingState(channel, user, false)
            };

            return {
                startTyping: startTyping,
                stopTyping: stopTyping
            }
        }
})();
/*
 * @author <tuhin.tsm.mandal@gmail.com>
 */
(function () {
    'use strict';
 
    requestProgress.$inject = ["socket"];
    angular
        .module('app')
        .factory('requestProgress', requestProgress);

    function requestProgress(socket) {
        return {
            init: init,
            subscribe: subscribe,
            progress: progress

        };

        function init() {
            // socket.removeAllListeners('subscribe_script_progress');
        }

        function subscribe() {
            var data = {
                uuid: _uuid4
            };
            socket.emit('subscribe_script_progress', data);
        }

        function progress(callback) {
            socket.on('script-progressed', function(response) {
                callback(response);
            });
        }
    }
})();
/**
 * Created by tuhin on 21/3/17.
 */
(function () {
    'use strict';

    saveToLocalService.$inject = ["$http"];
    angular
        .module('app')
        .factory('saveToLocalService', saveToLocalService);

    function saveToLocalService($http) {
        // interface
        var service = {
            image: image
        };
        return service;

        // implementation
        function image(link) {
            var data = {link: link};
            return $http.post('/api/saveImageToLocal', data).then(function (response) {
                return response.data;
            },
            function (response) {
                return response.data;
            });
        }
    }
})();
angular.module('app').constant('YT_event', {
    STOP: 0,
    PLAY: 1,
    PAUSE: 2,
    STATUS_CHANGE: 3
});

angular.module('app').directive('youtube', ["YT_event", "youTubeApiService", "$interval", "constants", function (YT_event, youTubeApiService, $interval, constants) {
    return {
        restrict: "E",
        /*
         $scope: {
         height: "@",
         width: "@",
         videoid: "@",
         index: "@",
         viewVideoPost : '&viewVideoPost'
         },
         */

        template: '<div></div>',

        link: function ($scope, element, attrs, $rootScope) {

            console.log('Hello Youtube Iframe');

            var tag = document.createElement('script');
            tag.src = "https://www.youtube.com/iframe_api";
            var firstScriptTag = document.getElementsByTagName('script')[0];
            firstScriptTag.parentNode.insertBefore(tag, firstScriptTag);

            console.log(firstScriptTag);

            /*------------------*/
            // var index = attrs.index;
            // var playerName = $parse('player' + index);
            var prefix = attrs.type == 'C' ? 'yc-' : 'yo-';
            var index = prefix + $scope.post.cardID;
            var player, playerTimer;

            youTubeApiService.onReady(function () {
                // console.log(index);
                player = tsmPlayerPool[index] = setupPlayer($scope, element);
            });

            function setupPlayer($scope, element) {
                return new YT.Player(element.children()[0], {
                    playerVars: {
                        autoplay: 0,
                        html5: 1,
                        theme: "light",
                        modesbranding: 0,
                        color: "white",
                        iv_load_policy: 3,
                        showinfo: 1,
                        controls: 1
                    },

                    height: $scope.height,
                    width: $scope.width,
                    videoId: attrs.videoid,

                    events: {
                        'onStateChange': function (event) {

                            var message = {
                                event: YT_event.STATUS_CHANGE,
                                data: ""
                            };

                            switch (event.data) {
                                case YT.PlayerState.PLAYING:
                                    message.data = "PLAYING";
                                    playerTimer = $interval(function () {
                                        videoPaused();
                                    }, 100);
                                    break;
                                case YT.PlayerState.ENDED:
                                    message.data = "ENDED";
                                    $interval.cancel(playerTimer);
                                    break;
                                case YT.PlayerState.UNSTARTED:
                                    message.data = "NOT PLAYING";
                                    break;
                                case YT.PlayerState.PAUSED:
                                    message.data = "PAUSED";
                                    $interval.cancel(playerTimer);
                                    break;
                            }

                            /*$scope.$apply(function() {
                             $scope.$emit(message.event, message.data);
                             });*/
                        }
                    }
                });
            }

            $scope.$on("$destroy", function() {
                $interval.cancel(playerTimer);
                player.destroy();
            });

            /*$scope.$watch('height + width', function (newValue, oldValue) {
                if (newValue == oldValue) {
                    return;
                }
                player.setSize($scope.width, $scope.height);
            });

            $scope.$watch('videoid', function (newValue, oldValue) {
                if (newValue == oldValue) {
                    return;
                }
                player.cueVideoById($scope.videoid);
            });

            $scope.$on(YT_event.STOP, function () {
             player.seekTo(0);
             player.stopVideo();
             });

             $scope.$on(YT_event.PLAY, function () {
             player.playVideo();


             });

             $scope.$on(YT_event.PAUSE, function () {
             player.pauseVideo();
             });
             */
            var isPlayed = false;
            function videoPaused() {
                var ct = player.getCurrentTime();
                var vDuration = player.getDuration();
                // var vData = player.getVideoData();
                if (ct >= vDuration * constants.VIDEO_VIEW_PER && !isPlayed) {
                    console.log('recording..');
                    $scope.viewVideoPost($scope.post);
                    isPlayed = true;
                }
            }
        }
    };
}]);

angular.module('app').factory("youTubeApiService", ["$q", "$window", function ($q, $window) {

    var deferred = $q.defer();
    var apiReady = deferred.promise;

    $window.onYouTubeIframeAPIReady = function () {
        deferred.resolve();
    }

    return {
        onReady: function (callback) {
            apiReady.then(callback);
        }
    }

}]);
angular.module('app').directive('typeTracking', ["TypingIndicator", "$timeout", "$stateParams", function (TypingIndicator, $timeout, $stateParams) {
    return {

        restrict: 'A',
        //scope: false,

        link: function (scope, element, attrs) {

            element.bind('keyup', function (event) {
                if (event.keyCode == 13 && event.shiftKey) {
                    event.stopPropagation();
                }
                else if (event.keyCode == 13) {
                    var $btn = element.parent('div').children('button');

                    if (!$btn.is(":disabled")) {
                        element.parent('div').children('button').trigger('click');
                    }
                    else {
                        $(this).val('');
                        scope.$digest();
                        var c = this.selectionStart;
                        c--;
                        this.setSelectionRange(c, c);
                    }
                }

                element.parent('div').children('button').click(function () {
                    scope.message = '';
                    if (scope.isCurrentlyTyping) {
                        // console.log('Flush the scheduler and stop typing');
                        // Stop typing immediatly
                        scope.stopTypingScheduler.flush();
                        scope.isCurrentlyTyping = false;
                    }
                });

            });

            scope.$watch(attrs['ngModel'], function (input) {

                // When to start Typing ?
                // Content is not empty and was not typing before
                if (!_.isEmpty(input) && !scope.isCurrentlyTyping) {
                    // console.log('startTyping()');
                    TypingIndicator.startTyping(scope.conversationChannel, scope.channelUser);
                    scope.isCurrentlyTyping = true;
                    scope.stopTypingScheduler();
                    // console.log('SCHEDULE stopTypingScheduler() in 5 seconds');
                }
                // When to reschedule ?
                // when the input is not empty and you are typing
                else if (!_.isEmpty(input) && scope.isCurrentlyTyping) {
                    // console.log('RE-SCHEDULE call to stopTypingScheduler() in 5 seconds');
                    scope.stopTypingScheduler();
                    scope.isCurrentlyTyping = true;
                }
                // When to stop typing ?
                // You erase the input : You were typing and the input is now empty
                else if (scope.isCurrentlyTyping && _.isEmpty(input)) {
                    // console.log('Flush the scheduler and stop typing');
                    // Stop typing immediatly
                    scope.stopTypingScheduler.flush();
                    scope.isCurrentlyTyping = false;
                }
            });
        },

        controller: ["$scope", function ($scope) {

            $scope.conversationChannel = $scope.post.id;
            $scope.channelUser = {
                id: $scope.user.id,
                name: $scope.user.first_name + ' ' + $scope.user.last_name
            };
            // Time before the stop typing event is fired after stopping to type.
            $scope.stopTypingTime = 5000

            // Keep track of the last action
            // This boolean is useful in order to know if we should send a stopTyping event in case the user was previously typing.
            $scope.isCurrentlyTyping = false;

            // Scheduler that trigger stopTyping if the function has not been invoced after stopTypingTime
            $scope.stopTypingScheduler = _.debounce(function () {
                TypingIndicator.stopTyping($scope.conversationChannel, $scope.channelUser);
                $scope.isCurrentlyTyping = false;
            }, $scope.stopTypingTime)

        }]
    };
}]);
/* ============================================================
 * Directive: pgSidebar
 * AngularJS directive for Pages Sidebar jQuery plugin
 * ============================================================ */

angular.module('app')
    .directive('pgSidebar', function() {
        return {
            restrict: 'A',
            link: function(scope, element, attrs) {
                var $sidebar = $(element);
            	$sidebar.sidebar($sidebar.data());

            	// Bind events
                // Toggle sub menus
                $('body').on('click', '.sidebar-menu a', function(e) {

                    if ($(this).parent().children('.sub-menu') === false) {
                         return;
                     }
                     var el = $(this);
                     var parent = $(this).parent().parent();
                     var li = $(this).parent();
                     var sub = $(this).parent().children('.sub-menu');

                     if(li.hasClass("active open")){
                        el.children('.arrow').removeClass("active open");
                        sub.slideUp(200, function() {
                            li.removeClass("active open"); 
                        });
                        
                     }else{
                        parent.children('li.open').children('.sub-menu').slideUp(200);
                        parent.children('li.open').children('a').children('.arrow').removeClass('active open');
                        parent.children('li.open').removeClass("open active");
                        el.children('.arrow').addClass("active open");
                        sub.slideDown(200, function() {
                            li.addClass("active open");

                        });
                     }
                });

            }
        }
    });
/* ============================================================
 * Directive: csSelect
 * AngularJS directive for SelectFx jQuery plugin
 * https://github.com/codrops/SelectInspiration
 * ============================================================ */

angular.module('app')
    .directive('csSelect', function() {
        return {
            restrict: 'A',
            link: function(scope, el, attrs) {
                if (!window.SelectFx) return;

                var el = $(el).get(0);
                $(el).wrap('<div class="cs-wrapper"></div>');
                new SelectFx(el);

            }
        };
    });
/* ============================================================
 * Directive: pgDropdown
 * Prepare Bootstrap dropdowns to match Pages theme
 * ============================================================ */

angular.module('app')
    .directive('pgDropdown', function() {
        return {
            restrict: 'A',
            link: function(scope, element, attrs) {

                var btn = $(element).find('.dropdown-menu').siblings('.dropdown-toggle');
                var offset = 0;

                var padding = btn.actual('innerWidth') - btn.actual('width');
                var menuWidth = $(element).find('.dropdown-menu').actual('outerWidth');

                if (btn.actual('outerWidth') < menuWidth) {
                    btn.width(menuWidth - offset);
                    $(element).find('.dropdown-menu').width(btn.actual('outerWidth'));
                } else {
                    $(element).find('.dropdown-menu').width(btn.actual('outerWidth'));
                }

            }
        }
    });
/* ============================================================
 * Directive: pgFormGroup
 * Apply Pages default form effects
 * ============================================================ */

angular.module('app')
    .directive('pgFormGroup', function() {
        return {
            restrict: 'A',
            link: function(scope, element, attrs) {
                $(element).on('click', function() {
                    $(this).find(':input').focus();
                });
                
                $('body').on('focus', '.form-group.form-group-default :input', function() {
                    $('.form-group.form-group-default').removeClass('focused');
                    $(this).parents('.form-group').addClass('focused');
                });

                $('body').on('blur', '.form-group.form-group-default :input', function() {
                    $(this).parents('.form-group').removeClass('focused');
                    if ($(this).val()) {
                        $(this).closest('.form-group').find('label').addClass('fade');
                    } else {
                        $(this).closest('.form-group').find('label').removeClass('fade');
                    }
                });

                $(element).find('.checkbox, .radio').hover(function() {
                    $(this).parents('.form-group').addClass('focused');
                }, function() {
                    $(this).parents('.form-group').removeClass('focused');
                });
            }
        }
    });
/* ============================================================
 * Directive: pgNavigate
 * Pre-made view ports to be used for HTML5 mobile hybrid apps
 * ============================================================ */

angular.module('app')
    .directive('pgNavigate', function() {
        return {
            restrict: 'A',
            link: function(scope, element, attrs) {

                $(element).click(function() {
                    var el = $(this).attr('data-view-port');
                    if ($(this).attr('data-toggle-view') != null) {
                        $(el).children().last().children('.view').hide();
                        $($(this).attr('data-toggle-view')).show();
                    }
                    $(el).toggleClass($(this).attr('data-view-animation'));
                    return false;
                });


            }
        }
    });
/* ============================================================
 * Directive: pgPortlet
 * AngularJS directive for Pages Portlets jQuery plugin
 * ============================================================ */

angular.module('app')
    .directive('pgPortlet', ['$parse', function($parse) {
        return {
            restrict: 'A',
            scope: true,
            link: function(scope, element, attrs) {

                var onRefresh = $parse(attrs.onRefresh);

                var options = {};

                if (attrs.progress) options.progress = attrs.progress;
                if (attrs.overlayOpacity) options.overlayOpacity = attrs.overlayOpacity;
                if (attrs.overlayColor) options.overlayColor = attrs.overlayColor;
                if (attrs.progressColor) options.progressColor = attrs.progressColor;
                if (attrs.onRefresh) options.onRefresh = function() {
                    onRefresh(scope);
                };

                element.portlet(options);

            }
        }
    }]);
/* ============================================================
 * Directive: pgTab
 * Makes Bootstrap Tabs compatible with AngularJS and add sliding
 * effect for tab transitions. 
 * ============================================================ */

angular.module('app')
    .directive('pgTab', ['$parse', function($parse) {
        return {
            link: function(scope, element, attrs) {
                var slide = attrs.slide;
                var onShown = $parse(attrs.onShown);
                // Sliding effect for tabs
                $(element).on('show.bs.tab', function(e) {
                    e = $(e.target).parent().find('a[data-toggle=tab]');

                    var hrefCurrent = e.attr('href');

                    if ($(hrefCurrent).is('.slide-left, .slide-right')) {
                        $(hrefCurrent).addClass('sliding');

                        setTimeout(function() {
                            $(hrefCurrent).removeClass('sliding');
                        }, 100);
                    }
                });

                $(element).on('shown.bs.tab', {
                    onShown: onShown
                }, function(e) {
                    if (e.data.onShown) {
                        e.data.onShown(scope);
                    }
                });

                $(element).click(function(e) {
                    e.preventDefault();
                    $(element).tab('show');
                });
            }
        };
    }]);
/* ============================================================
 * Directive: pgSearch
 * AngularJS directive for Pages Overlay Search jQuery plugin
 * ============================================================ */

angular.module('app')
    .directive('pgSearch', ['$parse', function($parse) {
        return {
            restrict: 'A',
            link: function(scope, element, attrs) {
                $(element).search();

                scope.$on('toggleSearchOverlay', function(scopeDetails, status) {
                    if(status.show){
                        $(element).data('pg.search').toggleOverlay('show');
                    } else {
                        $(element).data('pg.search').toggleOverlay('hide');
                    }
                })

            }
        }
    }]);
/* ============================================================
 * Directive: pgQuickview
 * AngularJS directive for Pages Overlay Search jQuery plugin
 * ============================================================ */

angular.module('app')
    .directive('pgQuickview', ['$parse', function($parse) {
        return {
            restrict: 'A',
            link: function(scope, element, attrs) {
                var $quickview = $(element)
                $quickview.quickview($quickview.data())

            }
        }
    }]);
/* ============================================================
 * Directive: pgNotificationCenter
 * Shows a list of notifications in a dropdown in header
 * ============================================================ */

angular.module('app')
    .directive('pgNotificationCenter', function() {
        return {
            restrict: 'A',
            link: function(scope, element, attrs) {
                $(element).on('click', function(event) {
                    event.stopPropagation();
                });
                $(element).find('.toggle-more-details').on('click', function(event) {
                    var p = $(this).closest('.heading');
                    p.closest('.heading').children('.more-details').stop().slideToggle('fast', function() {
                        p.toggleClass('open');
                    });
                });

            }
        }
    });
/* ============================================================
 * Directive: pgHorizontalMenu
 * AngularJS directive for Pages Horizontal Menu
 * ============================================================ */

angular.module('app')
    .directive('pgHorizontalMenu', ["$parse", function($parse) {
        return {
            restrict: 'A',
            link: function(scope, element, attrs) {
                
                $(document).on('click', '.horizontal-menu .bar-inner > ul > li', function(){
                    $(this).toggleClass('open').siblings().removeClass('open');
                });
               
                $('.content').on('click', function () {
                    $('.horizontal-menu .bar-inner > ul > li').removeClass('open');
                });
            }
        }
    }]);
  
       
        
angular.module('app')
    .directive('pgHorizontalMenuToggle', ["$parse", function($parse) {
        return {
            restrict: 'A',
            link: function(scope, element, attrs) {
                
                $(element).click(function(e) {
                    e.preventDefault();
                    $('body').toggleClass('menu-opened');
                });

            }
        }
    }]);
  
       
        
/* ============================================================
 * Directive: pgTabDropdownfx
 * Responsive Tabs with dropdown effect
 * effect for tab transitions. 
 * ============================================================ */

angular.module('app')
    .directive('pgTabDropdownfx', function() {
        return {
            link: function(scope, element, attrs) {

                var drop = $(element);
                drop.addClass("hidden-sm hidden-xs");
                var content = '<select class="cs-select cs-skin-slide full-width" data-init-plugin="cs-select">'
                for(var i = 1; i <= drop.children("li").length; i++){
                    var li = drop.children("li:nth-child("+i+")");
                    var selected ="";
                    if(li.hasClass("active")){    
                        selected="selected";
                    }
                    content +='<option value="'+ li.children('a').attr('href')+'" '+selected+'>';
                    content += li.children('a').text();
                    content += '</option>';
                }
                content +='</select>'
                drop.after(content);
                var select = drop.next()[0];
                $(select).on('change', function (e) {
                    var optionSelected = $("option:selected", this);
                    var valueSelected = this.value;
                    drop.find('a[href="'+valueSelected+'"]').tab('show') 
                })
                $(select).wrap('<div class="nav-tab-dropdown cs-wrapper full-width p-t-10 visible-xs visible-sm"></div>');
                new SelectFx(select);

            }
        };
    });
/* ============================================================
 * directives.js
 * All common functionality & their respective directive
 * ============================================================ */

angular.module('app')
    .directive('postcardModal', function () {
        return {
            restrict: 'EA',
            templateUrl: "tpl_postcardmodal",
            link: function ($scope, element, attrs) {
                element.bind('click', function (event) {
                    $scope.$apply(attrs.postcardModal);
                });
            }

        };
    });

angular.module('app')
    .directive('postcard', function () {
        return {
            restrict: 'A',
            link: function ($scope, element, attrs) {
                element.bind('click', function (event) {
                    $scope.$apply(attrs.postcard);
                });
            },
            controller: ["$scope", "$http", "$timeout", "inArray", "$state", function ($scope, $http, $timeout, inArray, $state) {
                // Open share popup..
                $scope.popUpDropdrow = 0;
                $scope.openSharePostPopUp = function (post_id) {

                };

                /*---------- Open popUp drop down menu when user click on .... sing ----------------------- */
                // This function user for postcard modal and post card details ..
                $scope.report = function (post) {  

                    var $newShrBtns = $(".newShrBtns");
                    $newShrBtns.find(".otherSubsh").show();
                    $newShrBtns.find(".subOverlaysh").show();

                    // alert('clicked');

                    // if($newShrBtns.find(".otherSubsh").parents('.profileCommentBoxTop').hasClass('has-big-zindex')){
                    //     $newShrBtns.find(".otherSubsh").parents('.profileCommentBoxTop').removeClass('has-big-zindex')
                    // } else {
                    //     $newShrBtns.find(".otherSubsh").parents('.profileCommentBoxTop').addClass('has-big-zindex')
                    // }


                    var flag = 0;
                    if ($state.current.name == 'profile') {
                        if (post.child_post_user_id != $scope.user.id) {
                            flag = 1;
                        } else {
                            flag = 0;
                        }
                    }
                    else if ($state.current.name == 'account') {
                        if (post.created_by != $scope.user.id) {
                            flag = 1;
                        } else {
                            flag = 0;
                        }
                    }
                    else {
                        if (post.created_by == $scope.user.id || post.child_post_user_id == $scope.user.id) {
                            flag = 0;
                        }
                        else {
                            flag = 1;
                        }
                    }
                    $scope.isShowPostReportLink = flag;
                };

                $scope.closeOverLayout = function () {
                    $(".subOverlaysh , .otherSubsh").hide();
                };
            }]
        };
    });

angular.module('app')
    .directive('webscrolling', function () {
        return {
            restrict: 'A',
            link: function (scope, element, attrs) {
                element.bind("scroll", function () {

                    var scrollTopHeightM = 5;
                    var scrollOuterPosM = $(".modalMobileScroll > .row").offset().top - 38;
                    var scrollOuterPosnewM = Math.abs(scrollOuterPosM)
                    var scrollPercentM = 100 * (scrollOuterPosnewM - scrollTopHeightM) / ($("#myModal .profileNewLeft").height() - $(".modalMobileScroll").height());
                    if ($(".profileNewLeft").innerHeight() >= $(".modalMobileScroll").innerHeight() && scrollOuterPosM <= 0) {
                        $(".mobileBarLong").css("width", scrollPercentM + "%");
                    }
                });
            }
        }
    });
angular.module('app')
    .directive('webscrolling2', function () {
        return {
            restrict: 'A',
            link: function (scope, element, attrs) {
                $(window).bind("scroll", function () {
                    var scrollTopHeightM2 = 5;
                    var scrollOuterPosM2 = $("body").scrollTop() - 38;
                    var scrollOuterPosnewM2 = Math.abs(scrollOuterPosM2)
                    var scrollPercentM2 = 100 * (scrollOuterPosnewM2 - scrollTopHeightM2) / ($(".cardDetailsPG_modal .profileNewLeft").height() - $(window).height());

                    if ($(".cardDetailsPG_modal .profileNewLeft").innerHeight() >= $(window).height() && scrollOuterPosM2 >= 0) {
                        $(".mobileBarLong2").css("width", scrollPercentM2 + "%");
                    }
                });
            }
        }
    });


/* ======================================================================
 * Directive: OTHERS , DELETE POSTCARD MODAL AND REPORT POST CARD MODAL 
 * Prepare Bootstrap dropdowns to match Pages theme
 * ===================================================================== */
angular.module('app')
    .directive('postCardMenu', ["$rootScope", "$http", function ($rootScope, $http) {
        return {
            restrict: 'E',
            link: function ($scope, element, attrs) {
                $scope.openDeletePostModal = function (post_id) {
                    $rootScope.mypostid = post_id;
                    $scope.closeOverLayout();
                };
                // Report post.
                $scope.openReportPostModal = function (post_id) {
                    // Initialize.
                    $rootScope.report_post_ids = [];
                    $rootScope.reportPostModalLoader = true;

                    var url = '/api/fetchReportPostData';
                    var postData = {
                        post_id: post_id
                    };
                    $http.post(url, postData)
                        .then(function (response) {
                            var report_ids = response.data.report_ids;
                            if (report_ids.length > 0) {
                                report_ids.forEach(function (v) {
                                    $rootScope.report_post_ids.push(v);
                                });
                            }
                            $rootScope.reportPostModalLoader = false;
                        },
                        function (response) {
                            $rootScope.reportPostModalLoader = false;
                        }
                    );

                    $rootScope.mypostid = post_id;
                    $scope.closeOverLayout();
                };

                // After clicking downvote then postcard other will be closed 
                $scope.closeOverLayout = function () {
                    $scope.popUpDropdrow = 0;
                    $scope.popUpReportDropdrow = 0;
                };
            },
            templateUrl: 'tpl.post-card-menu'
        };
    }]);


angular.module('app')
    .directive('deletePostModal', ["$http", function ($http) {
        return {
            restrict: 'E',
            link: function ($scope, element, attrs) {
                // Delete postcard and also their child postcard also..
                $scope.deleteMyPost = function (post_id) {
                    $scope.disableClick = true;
                    $http({
                        method: 'POST',
                        url: "angular/deleteMyPost",
                        params: {post_id: post_id}
                    }).then(function (response) {
                        location.href = 'profile';
                    }, function (response) {
                        alert('Sorry! we cannot process your request.');
                    });
                }
            },
            templateUrl: 'tpl_deletePostModal'
        };
    }]);

angular.module('app')
    .directive('reportPostModal', function () {
        return {
            restrict: 'E',
            link: function (scope, element, attrs) {

            },
            templateUrl: 'tpl_reportPostModal',
            controller: ["$scope", "$log", "$http", function ($scope, $log, $http) {
                $scope.doPostReport = function (post_id, report_id) {

                    $("#reportPostModal").modal("toggle");

                    $http({
                        method: 'POST',
                        url: "angular/doPostReport",
                        params: {
                            post_id: post_id,
                            report_id: report_id
                        }
                    }).then(function (response) {

                    }, function (response) {
                        // $log.info('Some error is occurred.');
                    });
                }
            }]
        };
    });


/* ============================================================
 * Directive: POST UPVOTES AND DOWNVOTES 
 * Prepare Bootstrap dropdowns to match Pages theme
 * ============================================================ */

angular.module('app')
    .directive('upvotes', function () {
        return {
            restrict: 'A',
            link: function (scope, element, attrs) {
                element.bind('click', function (event) {
                    scope.$apply(attrs.upvotes);
                });
            },
            controller: ["$scope", "$http", "inArray", "$state", "userDataService", function ($scope, $http, inArray, $state, userDataService) {

                $scope.doUpvotes = function (post_id, childPostId, type) {

                    //  Dynamic binding  new edition 24-11-2016 

                    if (angular.isArray($scope.commonData.allPosts)) {
                        angular.forEach($scope.commonData.allPosts, function (value, key) {
                            if (value.id == post_id) {
                                if ($scope.commonData.allPosts[key].isUpvote == 'N') {
                                    $scope.commonData.allPosts[key].isUpvote = 'Y';
                                    $scope.commonData.allPosts[key].upvotes = $scope.commonData.allPosts[key].upvotes + 1;
                                } else {
                                    $scope.commonData.allPosts[key].isUpvote = 'N';
                                    //console.log('2');
                                    $scope.commonData.allPosts[key].upvotes = $scope.commonData.allPosts[key].upvotes - 1;
                                }
                                if ($scope.commonData.allPosts[key].isDownvote == 'Y') {
                                    $scope.commonData.allPosts[key].downvotes = $scope.commonData.allPosts[key].downvotes - 1;
                                    $scope.commonData.allPosts[key].isDownvote = 'N';
                                }

                            }
                        });
                    }

                    // For postcard 
                    if (type == 'M') {
                        if ($scope.post.isUpvote == 'N') {
                            $scope.post.isUpvote = 'Y';
                            $scope.post.upvotes = $scope.post.upvotes + 1
                            // Update post point on modal :: 
                            // if logged in user and post creator is same then point is not update 
                            if ($scope.post.created_by != $scope.user.id) {
                                $scope.post.points = $scope.post.points + 2;
                            }
                        } else {
                            $scope.post.isUpvote = 'N';
                            $scope.post.upvotes = $scope.post.upvotes - 1
                            // Update post point on modal ::
                            // if logged in user and post creator is same then point is not update 
                            if ($scope.post.created_by != $scope.user.id) {
                                $scope.post.points = $scope.post.points - 2;
                            }
                        }

                        if ($scope.post.isDownvote == 'Y') {
                            $scope.post.downvotes = $scope.post.downvotes - 1;
                            $scope.post.isDownvote = 'N';
                            // Update post point on modal :: 
                            // if downvote cancel then execute ...
                            // if logged in user and post creator is same then point is not update 
                            if ($scope.post.created_by != $scope.user.id) {
                                $scope.post.points = $scope.post.points + 2;
                            }
                        }
                    }

                    //  end Dynamic binding  new edition 24-11-2016 
                    $http({
                        method: "POST",
                        url: "angular/upVotePost",
                        params: {post_id: post_id, childPostId: childPostId}
                    }).then(function (response) {

                        if (!$scope.post) {
                            $scope.post = {};
                            $scope.post.user = {};
                        }

                        /* ============================================================
                         * For Updating profile points 
                         * ============================================================ */

                        if (angular.isArray($scope.post.getUser)) {
                            angular.forEach($scope.post.getUser, function (value, key) {

                                angular.forEach(response.data.user, function (val, k) {
                                    if (value.id == val.id) {
                                        $scope.post.getUser[key].points = val.points;
                                    }
                                });
                            });
                        }


                        // For updateing profile points 
                        if ($state.current.name == 'profile' || $state.current.name == 'account') {
                            if ($scope.userData.id != $scope.user.id) {
                                var user_points = response.data.user[0].points;
                                userDataService.updateUserData(user_points);
                                userDataService.getData();
                            }
                        }

                    });
                };

                $scope.doDownVotes = function (post_id, childPostId, type) {

                    //  Dynamic binding  new edition 24-11-2016 
                    if (angular.isArray($scope.commonData.allPosts)) {
                        angular.forEach($scope.commonData.allPosts, function (value, key) {
                            if (value.id == post_id) {

                                if ($scope.commonData.allPosts[key].isDownvote == 'N') {
                                    $scope.commonData.allPosts[key].isDownvote = 'Y';
                                    $scope.commonData.allPosts[key].downvotes = $scope.commonData.allPosts[key].downvotes + 1;
                                } else {
                                    $scope.commonData.allPosts[key].isDownvote = 'N';
                                    $scope.commonData.allPosts[key].downvotes = $scope.commonData.allPosts[key].downvotes - 1;
                                }
                                if ($scope.commonData.allPosts[key].isUpvote == 'Y') {
                                    $scope.commonData.allPosts[key].upvotes = $scope.commonData.allPosts[key].upvotes - 1;
                                    $scope.commonData.allPosts[key].isUpvote = 'N';
                                }

                            }
                        });
                    }

                    // For postcard 
                    if (type == 'M' || type == 'PD') {  // M for modal
                        if ($scope.post.isDownvote == 'N') {
                            $scope.post.isDownvote = 'Y';
                            $scope.post.downvotes = $scope.post.downvotes + 1
                            // Update post point on modal ::
                            // if logged in user and post creator is same then point is not update 
                            if ($scope.post.created_by != $scope.user.id) {
                                $scope.post.points = $scope.post.points - 2;
                            }
                        } else {
                            $scope.post.isDownvote = 'N';
                            $scope.post.downvotes = $scope.post.downvotes - 1
                            // Update post point on modal :: 
                            // if logged in user and post creator is same then point is not update 
                            if ($scope.post.created_by != $scope.user.id) {
                                $scope.post.points = $scope.post.points + 2;
                            }
                        }

                        if ($scope.post.isUpvote == 'Y') {
                            $scope.post.upvotes = $scope.post.upvotes - 1;
                            $scope.post.isUpvote = 'N';
                            // Update post point on modal :: 
                            // if upvote cancel then execute ...
                            // if logged in user and post creator is same then point is not update 
                            if ($scope.post.created_by != $scope.user.id) {
                                $scope.post.points = $scope.post.points + 2;
                            }
                        }
                    }

                    //  Dynamic binding  new edition 24-11-2016 
                    $http({
                        method: "POST",
                        url: "angular/downVotePost",
                        params: {post_id: post_id, childPostId: childPostId}

                    }).then(function (response) {

                        if (!$scope.post) {
                            $scope.post = {};
                            $scope.post.user = {};
                        }

                        /* ============================================================
                         * For Updating profile points 
                         * 
                         * ============================================================ */
                        if (angular.isArray($scope.post.getUser)) {
                            angular.forEach($scope.post.getUser, function (value, key) {

                                angular.forEach(response.data.user, function (val, k) {
                                    if (value.id == val.id) {
                                        $scope.post.getUser[key].points = val.points;
                                    }
                                });
                            });
                        }

                        // For updateing profile points 
                        if ($state.current.name == 'profile' || $state.current.name == 'account') {
                            if ($scope.userData.id != $scope.user.id) {
                                var user_points = response.data.user[0].points;
                                userDataService.updateUserData(user_points);
                                userDataService.getData();
                            }
                        }

                        $(".subOverlaysh , .otherSubsh").hide();
                    });
                };
            }]
        };
    });

/* ============================================================
 * Directive: Open share post modal, share post 
 * Prepare Bootstrap dropdowns to match Pages theme
 * ============================================================ */


angular.module('app')
    .directive('sharepostCard', function () {
        return {
            restrict: 'E',
            templateUrl: "tpl.sharepost-card",
            link: function (scope, element, attrs) {

            },
            controller: ["$scope", "$http", "$timeout", function ($scope, $http, $timeout) {
                // Initialize privacies..
                $scope.privacies = {};
                // Fetch privacies..
                $http.get('api/privacy').then(function (response) {
                    if (response.data) {
                        $scope.privacies = response.data;
                        $scope.privacy_id = $scope.privacies[0];
                    }
                });

                $scope.sharePopUp = function (post_id, childPostUserId, childPostId, is_openFromPostcardModal) {

                    $scope.sharedPost = {};
                    var url = 'angular/showPostDetails';
                    var postData = {
                        post_id: post_id,
                        child_post_id: childPostId,
                        is_briefed: 1,
                        initiator: 'share_popup'
                    };
                    $http.post(url, postData)
                        .then(function (response) {

                        $scope.sharedPost = response.data.post;
                        //  $scope.loginUser = response.data.user;

                        $scope.popUpDropdrow = 0;
                        $scope.postId = post_id;
                        $scope.caption = '';
                        $scope.postUserId = childPostUserId;
                        $scope.childPostId = childPostId;

                        // find original post creator ::
                        var postModalUser = response.data.post.getUser;
                        angular.forEach(postModalUser, function (value, key) {
                            $scope.originalPostUserName = value.username;
                            $scope.originalPostFirstName = value.first_name;
                            $scope.originalPostLastName = value.last_name;
                            $scope.originalPostProfileImage = value.thumb_image_url;
                            $scope.originalPostUserColor = (value.id % 2 == 0) ? 's1' : 's0';

                        });

                        $timeout(function () {
                            angular.element("#shareModal").modal();
                            angular.element(".subOverlay").triggerHandler("click");
                            $timeout(function () {
                                angular.element("html").addClass("scrollHidden");
                                angular.element("body").addClass("modal-open");
                            }, 1000);
                            if (is_openFromPostcardModal == 1) {
                                $("#myModal").modal("toggle");
                            }
                        }, 100);
                    });
                };


            }]
        };
    });

angular.module('app')
    .directive('share', ["notify", "$state", function (notify, $state) {
        return {
            restrict: 'A',
            link: function (scope, element, attrs) {
                element.bind('click', function () {
                    scope.$apply(attrs.share);
                });
            },
            controller: ["$scope", "$http", "$timeout", function ($scope, $http, $timeout) {
                $scope.shareThisPost = function (post_id, childPostId, postUserId) {
                    $http({
                        method: "POST",
                        url: "angular/shareThisPost",
                        params: {
                            caption: $scope.caption,
                            post_id: $scope.postId,
                            profile_id: postUserId,
                            childPostId: childPostId,
                            privacy_id: $scope.privacy_id
                        }
                    }).then(function (response) {

                        $("#shareModal").modal('toggle');
                        $(".sharedSuccessLoader").show();
                        $timeout(function () {
                            $(".sharedSuccessLoader").hide();
                        }, 2000);
                        $timeout(function () {
                            var notification = {
                                'message': 'You have successfully re-share this post.',
                                'color': 'success',
                                'timeout': 5000
                            };
                            notify(notification);
                        }, 2100);
                        $timeout(function () {
                            $state.go("profile");
                        }, 3000);

                    });
                };


            }]
        };
    }]);

/* ============================================================
 * Directive: Postview Scrolling 
 * ============================================================ */
angular.module('app')

    .directive('viewpost', ["userDataService", function (userDataService) {
        return {
            restrict: "A",
            link: function (scope, element, attrs) {
                element.bind("click", function () {
                    scope.$apply(attrs.viewpost);
                });
            },
            controller: ["$scope", "$http", "$window", "$state", function ($scope, $http, $window, $state) {
                $scope.externalLink = function (postID, childPostID) {
                    $http({
                        method: "POST",
                        url: "angular/viewPost",
                        data: {
                            postID: parseInt(postID),
                            childPostID: childPostID,
                            postType: 4
                        }
                    }).then(function (response) {

                        // userData is update on proilfe or account .
                        if ($state.current.name == 'profile' || $state.current.name == 'account') {
                            var points = response.data.post.getUsers[0].points;
                            userDataService.updateUserData(points);
                            userDataService.getData();
                        }

                        // Update dynamically total number of post view
                        var p;
                        for (p in $scope.commonData.allPosts) {
                            if ($scope.commonData.allPosts[p].id == postID) {
                                $scope.commonData.allPosts[p].totalPostViews = response.data.post.totalPostViews;
                                // break;
                            }
                        }
                        // update user points dynamically on popup........
                        if (angular.isArray($scope.post.getUser)) {
                            angular.forEach($scope.post.getUser, function (value, key) {
                                angular.forEach(response.data.post.getUsers, function (val, k) {
                                    if (value.id == val.id) {
                                        $scope.post.getUser[key].points = val.points;
                                    }
                                });
                            });
                        }
                        // update total access on popup ::
                        $scope.post.totalPostViews = response.data.post.totalPostViews;
                        $scope.post.points = response.data.post.points;

                        // $window.open(external_link, '_blank');
                    }, function (error) {
                        // console.log('Error occurred.');
                    });

                };


            }]
        };
    }]);


/* ============================================================
 * Directive: PROFILE NAV SLIDER
 * ============================================================ */
angular.module('app')
    .directive('exploreTab', function () {
        return {
            restrict: 'E',
            templateUrl: "tpl_explore_tab"
        };
    });


/*angular.module("app")
 .filter('displayBestTab', function () {
 return function (x) {

 };
 });*/

/* ============================================================
 * Directive: Follow users
 * ============================================================ */

angular.module('app')
    .directive('allfollowuser', function () {
        return {
            restrict: 'A',
            link: function ($scope, element, attrs) {
                element.bind('click', function (event) {
                    $scope.$apply(attrs.allfollowuser);
                });
            },
            controller: ["$scope", "$http", "$timeout", "inArray", "$state", function ($scope, $http, $timeout, inArray, $state) {
                /* ============================================================
                 * Functionality: Profile page follow,unfollow only postcard modal
                 * ============================================================ */

                $scope.followUser = function (user_id, type, following) {

                    // For instance update
                    if ($state.current.name == 'profile' || $state.current.name == 'account') {
                        if ($scope.userData.username == $scope.user.username) {
                            if (inArray.arrayIndexOf($scope.userFollowing, user_id) != -1) {
                                $scope.userFollowing.splice(inArray.arrayIndexOf($scope.userFollowing, user_id), 1);
                                angular.isArray($scope.userData.following)
                                {
                                    angular.forEach($scope.userData.following, function (val, key) {
                                        if (val.user_id == user_id) {
                                            $scope.userData.following.splice(key, 1);
                                            $scope.userDataTotalFollowing = $scope.userData.following.length;
                                            return false;
                                        }
                                    });
                                }
                                $scope.userData.userDataTotalFollowing = $scope.userFollowing.length;

                                if (angular.isArray($scope.post.getUser)) {
                                    angular.forEach($scope.post.getUser, function (value, key) {

                                        if (value.id === user_id) {
                                            $scope.post.getUser[key].is_follow = $scope.post.getUser[key].is_follow - 1;
                                        }
                                    });
                                }
                            }
                            else {
                                $scope.userFollowing.push(parseInt(user_id));
                                $scope.userData.userDataTotalFollowing = $scope.userData.following.length;

                                if (angular.isArray($scope.post.getUser)) {
                                    angular.forEach($scope.post.getUser, function (value, key) {

                                        if (value.id === user_id) {
                                            $scope.post.getUser[key].is_follow = $scope.post.getUser[key].is_follow + 1;
                                        }
                                    });
                                }
                            }
                        }
                        else {
                            if (inArray.arrayIndexOf($scope.userFollowing, user_id) != -1) {
                                $scope.userFollowing.splice(inArray.arrayIndexOf($scope.userFollowing, user_id), 1);

                                if (angular.isArray($scope.post.getUser)) {
                                    angular.forEach($scope.post.getUser, function (value, key) {

                                        if (value.id === user_id) {
                                            $scope.post.getUser[key].is_follow = $scope.post.getUser[key].is_follow - 1;
                                        }
                                    });
                                }
                                $scope.resetPostData();
                                $scope.loadMore('all');
                            }
                            else {
                                $scope.userFollowing.push(parseInt(user_id));
                                if (angular.isArray($scope.post.getUser)) {
                                    angular.forEach($scope.post.getUser, function (value, key) {

                                        if (value.id === user_id) {
                                            $scope.post.getUser[key].is_follow = $scope.post.getUser[key].is_follow + 1;
                                        }
                                    });
                                }
                                $scope.userData.userDataTotalFollower = $scope.userData.userDataTotalFollower + 1;
                                $scope.resetPostData();
                                $scope.loadMore('all');
                            }
                        }
                    }
                    else {
                        if (inArray.arrayIndexOf($scope.userFollowing, user_id) != -1) {
                            $scope.userFollowing.splice(inArray.arrayIndexOf($scope.userFollowing, user_id), 1);

                            // start this block use for  post detail page
                            if ($scope.post && angular.isArray($scope.post.getUser)) {
                                angular.forEach($scope.post.getUser, function (value, key) {
                                    if (value.id === user_id) {
                                        $scope.post.getUser[key].is_follow = $scope.post.getUser[key].is_follow - 1;
                                    }
                                });
                            }
                        }
                        else {
                            $scope.userFollowing.push(parseInt(user_id));

                            // start this block use for  post detail page
                            if ($scope.post && angular.isArray($scope.post.getUser)) {
                                angular.forEach($scope.post.getUser, function (value, key) {

                                    if (value.id === user_id) {
                                        $scope.post.getUser[key].is_follow = $scope.post.getUser[key].is_follow + 1;
                                    }
                                });
                            }
                        }
                    }
                    // end
                    $http({
                        method: "POST",
                        url: "angular/followUser",
                        params: {user_id: user_id, following: following}
                    }).then(function (response) {

                        if ($state.current.name == 'profile' || $state.current.name == 'account') {
                            if ($scope.userData.username == $scope.user.username) {
                                if (inArray.arrayIndexOf($scope.userFollowing, user_id) != -1) {

                                } else {
                                    $scope.userData.following.push(response.data.getFollower);
                                }
                            }
                        }

                    });
                };

                /* =======================================================================
                 * Functionality: Follow user form follower tab .
                 If login user follow from own profile then increase number of following
                 * ====================================================================== */


                $scope.followUserFromFollowerTab = function (user_id, type, following) {

                    if ($scope.userData.username == $scope.user.username) {


                        if (inArray.arrayIndexOf($scope.userFollowing, user_id) != -1) {
                            $scope.userFollowing.splice(inArray.arrayIndexOf($scope.userFollowing, user_id), 1);
                            $scope.userData.userDataTotalFollowing = $scope.userData.userDataTotalFollowing - 1;
                        }
                        else {

                            $scope.userFollowing.push(parseInt(user_id));
                            $scope.userData.userDataTotalFollowing = $scope.userData.userDataTotalFollowing + 1;
                        }


                    }
                    else {
                        if (inArray.arrayIndexOf($scope.userFollowing, user_id) != -1) {
                            $scope.userFollowing.splice(inArray.arrayIndexOf($scope.userFollowing, user_id), 1);
                        }
                        else {
                            $scope.userFollowing.push(parseInt(user_id));
                        }
                    }


                    $http({
                        method: "POST",
                        url: "angular/followUser",
                        params: {user_id: user_id, following: following}
                    }).then(function (response) {


                    });
                };


                /* =======================================================================
                 Functionality: Follow user form following tab .
                 If login user follow from own profile then increase number of following
                 ====================================================================== */

                $scope.followUserFromFollowingTab = function (user_id, type, following) {

                    if ($scope.userData.username == $scope.user.username) {
                        if (inArray.arrayIndexOf($scope.userFollowing, user_id) != -1) {
                            $scope.userFollowing.splice(inArray.arrayIndexOf($scope.userFollowing, user_id), 1);
                            $scope.userData.userDataTotalFollowing = $scope.userData.userDataTotalFollowing - 1;
                        }
                        else {
                            $scope.userFollowing.push(parseInt(user_id));
                            $scope.userData.userDataTotalFollowing = $scope.userData.userDataTotalFollowing + 1;
                        }
                    }
                    else {
                        if (inArray.arrayIndexOf($scope.userFollowing, user_id) != -1) {
                            $scope.userFollowing.splice(inArray.arrayIndexOf($scope.userFollowing, user_id), 1);
                        }
                        else {
                            $scope.userFollowing.push(parseInt(user_id));
                        }
                    }


                    $http({
                        method: "POST",
                        url: "angular/followUser",
                        params: {user_id: user_id, following: following}
                    }).then(function (response) {

                    });
                }

                /* =======================================================================
                 * Functionality: Follow user form timeline follow tab and tag page follow button
                 * postcard and postcard modal
                 * ====================================================================== */

                $scope.followTab = function (user_id, type, following) {

                    if (type == 'T') {
                        if (inArray.arrayIndexOf($scope.userFollowing, user_id) != -1) {
                            $scope.userFollowing.splice(inArray.arrayIndexOf($scope.userFollowing, user_id), 1);
                            $scope.userData.userDataTotalFollower = $scope.userData.userDataTotalFollower - 1;
                            $scope.resetPostData();
                            //$scope.loadMore('all');
                            setTimeout(function () {
                                $scope.loadMore('all');
                            }, 500);
                        }
                        else {
                            $scope.userFollowing.push(parseInt(user_id));
                            $scope.userData.userDataTotalFollower = $scope.userData.userDataTotalFollower + 1;
                            $scope.resetPostData();
                            //$scope.loadMore('all');
                            setTimeout(function () {
                                $scope.loadMore('all');
                            }, 500);
                        }
                    }
                    $http({
                        method: "POST",
                        url: "angular/followUser",
                        params: {user_id: user_id, following: following}
                    }).then(function (response) {


                    });
                }

            }]
        };
    });

/* ============================================================
 * Directive: Report comments 
 * ============================================================ */

angular.module('app')
    .directive('reportCommentModal', function () {
        return {
            restrict: 'E',
            link: function (scope, element, attrs) {

            },
            templateUrl: 'tpl_reportCommentModal',
            controller: ["$scope", "$log", "$http", function ($scope, $log, $http) {

                $scope.openReportCommentModal = function (comment_id) {
                    // Initialize.
                    $scope.report_comment_ids = [];
                    $scope.reportCommentModalLoader = true;

                    var url = '/api/fetchReportCommentData';
                    var postData = {
                        comment_id: comment_id
                    };
                    $http.post(url, postData)
                    .then(function (response) {
                            var report_ids = response.data.report_ids;
                            if (report_ids.length > 0) {
                                report_ids.forEach(function (v) {
                                    $scope.report_comment_ids.push(v);
                                });
                            }
                            $scope.reportCommentModalLoader = false;
                        },
                        function () {
                            $scope.reportCommentModalLoader = false;
                        }
                    );

                    $scope.comment_id = comment_id;
                    $scope.closeOverLayout();
                };


                $scope.doCommentReport = function (commentId, reportId) {
                    //$("#reportCommentModal").modal("toggle");
                    $('.reporPopupVW').hide();

                    $http({
                        method: 'POST',
                        url: "angular/doCommentReport",
                        data: {
                            commentId: commentId,
                            reportId: reportId
                        }
                    }).then(function (response) {


                    }, function (response) {
                        $log.info('Some error is occurred.');
                    });
                }

            }]
        };
    });

/* ============================================================
 * Directive: Social sharing 
 * Facebook Share API
 * ============================================================ */
angular.module('app')
    .directive('socialSharing', ["$window", "$http", "$interval", "notify", "$timeout", function ($window, $http, $interval, notify, $timeout) {
        return {
            restrict: "A",
            link: function ($scope, element, attrs) {
                element.bind('click', function (event) {
                    $scope.$apply(attrs.socialSharing);
                });

                $scope.facebook = function (post, type) {

                    $window.fbAsyncInit = function () {
                        FB.init({
                            appId: '1050589168353691',
                            status: true,
                            cookie: true,
                            xfbml: true,
                            version: 'v2.4'
                        });
                    };

                    var obj = {};
                    var thumbnail = '';

                    obj.method = 'feed';

                    if (post.post_type == 5) {  // :: For status post ::
                        //StrippedString = OriginalString.replace(/(<([^>]+)>)/ig,"");
                        obj.title = post.caption.replace(/(<([^>]+)>)/ig, "");
                    } else {
                        if (post.title != '') {
                            obj.title = post.title.replace(/(<([^>]+)>)/ig, "");
                        } else {
                            obj.title = post.caption.replace(/(<([^>]+)>)/ig, "");
                        }
                    }

                    if (post.post_url != '') {
                        obj.link = post.post_url;
                    }

                    if (post.post_type == 3) {   // :: For article post ::

                        if (post.short_description == '') {
                            var OriginalString = post.content;
                            var cont = OriginalString.replace(/(<([^>]+)>)/ig, "");
                            obj.description = cont.substr(0, 100);
                        } else if (post.short_description != '') {
                            obj.description = post.short_description;
                        } else {
                            obj.description = "  ";
                        }

                    } else {

                        if (post.short_description != '') {
                            obj.description = post.short_description;
                        } else {
                            obj.description = "  ";
                        }
                    }

                    if (post.post_type == 1 || post.post_type == 3 || post.post_type == 4) {
                        if (post.image != '') {
                            obj.picture = post.image;
                        }
                    } else if (post.post_type == 2) {   // For video post ..

                        if (post.embed_code != '') {	 // for embed code
                            if (post.embed_code_type == 'youtube') {

                                var pic = 'https://img.youtube.com/vi/' + post.videoid + '/0.jpg';
                                obj.picture = pic;

                            } else if (post.embed_code_type == 'dailymotion') {

                                thumbnail = 'https://www.dailymotion.com/thumbnail/video/' + post.videoid;
                                obj.picture = thumbnail;

                            } else if (post.embed_code_type == 'vimeo') {
                                thumbnail = 'https://i.vimeocdn.com/video/' + post.videoid + '_640.jpg';
                                obj.picture = thumbnail;
                            }
                            //obj.source=post.embed_code;
                        } else if (post.video != '') { // html5

                            var video_poster = post.video_poster;
                            obj.picture = video_poster;

                            var url = post.video
                            obj.source = url;
                        }
                    } else if (post.post_type == 5) {  // for status post

                        if (post.image != '') {
                            obj.picture = post.image;
                        } else {

                            if (post.embed_code != '') {    // for embed code
                                if (post.embed_code_type == 'youtube') {
                                    var pic = 'https://img.youtube.com/vi/' + post.videoid + '/0.jpg';
                                    obj.picture = pic;
                                }
                                else if (post.embed_code_type == 'dailymotion') {
                                    thumbnail = 'https://www.dailymotion.com/thumbnail/video/' + post.videoid;
                                    obj.picture = thumbnail;
                                }
                                else if (post.embed_code_type == 'vimeo') {
                                    thumbnail = 'https://i.vimeocdn.com/video/' + post.videoid + '_640.jpg';
                                    obj.picture = thumbnail;
                                }
                            }
                            else if (post.video != '') { // html5
                                var video_poster = post.video_poster;
                                obj.picture = video_poster;

                                var url = post.video;
                                obj.source = url;
                            }
                        }
                    }

                    FB.ui(obj, function (response) {
                        if (response && !response.error_message) {
                            // update postcard total share ...
                            if (angular.isArray($scope.commonData.allPosts)) {
                                angular.forEach($scope.commonData.allPosts, function (value, key) {
                                    if (value.id == post.id) {
                                        $scope.commonData.allPosts[key].totalShare = $scope.commonData.allPosts[key].totalShare + 1;
                                        return false;
                                    }
                                });
                            }
                            // update postcard modal
                            if (type == 'M') {
                                if ($scope.user.id != $scope.post.child_post_user_id) {
                                    $scope.post.points = $scope.post.points + 10;
                                }
                                $scope.post.totalShare = $scope.post.totalShare + 1;
                                $scope.post.totalFBshare = $scope.post.totalFBshare + 1;
                            }

                            $http({
                                method: 'POST',
                                url: "sharedPostInSocialNetworkingForFacebook",
                                data: {
                                    post_id: post.id,
                                    child_post_id: post.child_post_id,
                                    activityType: 4,    //  shared to facebook
                                }
                            }).then(function (response) {

                            }, function (response) {
                                console.log('Some error is occurred.');
                            });

                        } else {
                            console.log('Error while posting.');
                        }
                    });
                }; // end of a function
                $window.$scope = $scope;

                $scope.twitter = function (post, card_type) {


                    flag = 0;
                    var callAjax = 0;
                    if ($scope.user.guest == 0) {

                        $http({
                            method: "POST",
                            url: "accessTokenVerify",
                        }).then(function (response) {
                            flag = response.data;
                            $scope.twitterPopUp(flag, post, card_type)
                        }, function (eror) {
                            console.log('some error has occurred');
                        });

                    } else {
                        flag = 0;
                        $scope.twitterPopUp(flag, post, card_type)
                    }
                };  // end of a  twitter function

                $scope.twitterPopUp = function (flag, post, card_type) {

                    if (parseInt(flag) == 0) {
                        var left = screen.width / 2 - 200;
                        var top = screen.height / 2 - 250;

                        var popup = $window.open('twitter_connect/' + 1, '', 'left=' + left + ',top=' + top + ',width=550,height=450,personalbar=0,toolbar=0,scrollbars=0,resizable=0');

                        var interval = 1000;

                        // create an ever increasing interval to check a certain global value getting assigned in the
                        var i = $interval(function () {
                            interval += 500;
                            try {
                                // value is the user_id returned from paypal
                                if (popup.value) {
                                    $interval.cancel(i);
                                    popup.close();
                                    $scope.postToTwitter(post, card_type);
                                }
                            } catch (e) {
                                console.error(e);
                            }
                        }, interval);
                    } else {
                        $scope.postToTwitter(post, card_type);
                    }
                };

                $scope.postToTwitter = function (post, card_type) {

                    // **** start update scope data *****
                    // update postcard total share ...
                    if (angular.isArray($scope.commonData.allPosts)) {
                        angular.forEach($scope.commonData.allPosts, function (value, key) {
                            if (value.id == post.id) {
                                $scope.commonData.allPosts[key].totalShare = $scope.commonData.allPosts[key].totalShare + 1;
                                return false;
                            }
                        });
                    }
                    // update postcard modal
                    if (card_type == 'M') {
                        if ($scope.user.id != $scope.post.child_post_user_id) {
                            $scope.post.points = $scope.post.points + 10;
                        }
                        $scope.post.totalShare = $scope.post.totalShare + 1;
                        $scope.post.totalTwittershare = $scope.post.totalTwittershare + 1;
                    }
                    // ***** end update scope data ******

                    $timeout(function () {
                        var notification = {
                            'message': 'You have successfully shared this post to twitter.',
                            'color': 'success',
                            'timeout': 5000
                        };
                        notify(notification);
                    }, 2100);

                    $http({
                        method: 'POST',
                        url: "postToTwitter",
                        data: {
                            post_id: post.id,
                            child_post_id: post.child_post_id,
                        }
                    }).then(function (response) {

                    }, function (error) {
                        console.log('Some error is occurred.');
                    });
                };


            },
        }
    }]);

/* ============================================================
 * Directive: Prompt Sign in Box
 * Alert for Non logged in users
 * ============================================================ */
angular.module('app')
    .directive('promptSigninBox', function () {
        return {
            restrict: 'E',
            link: function ($scope, element, attrs) {

            },
            templateUrl: 'tpl_promptSinginBox'
        };
    });

/* ============================================================
 * Directive: Book Mark
 * User can bookmark through
 * ============================================================ */

angular.module('app')
    .directive('bookMark', ["$http", function ($http) {
        return {
            restrict: "A",
            link: function ($scope, element, attrs) {
                element.bind('click', function (event) {
                    $scope.$apply(attrs.bookMark);
                });
            },
            controller: ["$scope", function ($scope) {
                $scope.bookMarkProcess = function (postID, type) {
                    // M : Modal ,P:  Postcard
                    if (angular.isArray($scope.commonData.allPosts)) {
                        var isChange = 0;
                        angular.forEach($scope.commonData.allPosts, function (value, key) {
                            if ($scope.commonData.allPosts[key].id == postID) {

                                if ($scope.commonData.allPosts[key].isBookMark == 'Y') {
                                    $scope.commonData.allPosts[key].isBookMark = 'N';
                                    // For left panel update total book marks
                                    isChange = 1;
                                } else {
                                    $scope.commonData.allPosts[key].isBookMark = 'Y';
                                    // For left panel update total book marks
                                    isChange = 2;
                                }
                                return;
                            }
                        });
                    }
                    if (type == 'M') {  // Dynamick binding  for  postcard modal

                        if ($scope.post.isBookMark == 'Y') {
                            $scope.post.isBookMark = 'N';
                            $scope.post.totalBookMark = $scope.post.totalBookMark - 1; // post total bookmark
                            isChange = 1;
                        } else {
                            $scope.post.isBookMark = 'Y';
                            $scope.post.totalBookMark = $scope.post.totalBookMark + 1; // post total book mark
                            isChange = 2;
                        }

                    }

                    // user  total bookmark
                    if (isChange == 1) {
                        $scope.user.totalBookMarks = $scope.user.totalBookMarks - 1;
                    }
                    if (isChange == 2) {
                        $scope.user.totalBookMarks = $scope.user.totalBookMarks + 1;
                    }

                    $http({
                        method: 'POST',
                        url: "angular/bookmark",
                        data: {postID: postID}
                    }).then(function (response) {

                    }, function (error) {
                        console.log('Sorry! we cannot process your request.');
                    });

                }
            }]
        };
    }]);
/* ============================================================
 * Directive: Connect to social media
 * facebook 
 * ============================================================ */
angular.module('app')
    .directive('connectSocialMedia', ["$window", "$http", "$interval", function ($window, $http, $interval) {
        return {
            restrict: 'A',
            link: function ($scope, element, attrs) {

                element.bind('click', function (event) {
                    $scope.$apply(attrs.connectSocialMedia);
                });
                // connect to facebook

                $window.$scope = $scope;
                $scope.connectFacebook = function () {

                    var left = screen.width / 2 - 200;
                    var top = screen.height / 2 - 250;

                    var popup = $window.open('facebookLogin/', '', 'left=' + left + ',top=' + top + ',width=550,height=450,personalbar=0,toolbar=0,scrollbars=0,resizable=0');

                    var interval = 1000;

                    // create an ever increasing interval to check a certain global value getting assigned in the 
                    var i = $interval(function () {
                        interval += 500;
                        try {
                            // value is the user_id returned from paypal 
                            if (popup.value) {
                                $interval.cancel(i);
                                popup.close();

                            }
                        } catch (e) {
                            console.error(e);
                        }
                    }, interval);

                };
            }
        };
    }]);

'use strict';

/* Search Controllers */

angular.module('app')

.controller('QuickSearchCtrl', ['$scope', '$state', '$timeout', '$window', 'constants', 'notify', 'refreshService', '$interval', function($scope, $state, $timeout, $window, constants, notify, refreshService, $interval) {
	
    setTimeout(function () {
		$('#overlay-search').on( "touchstart", function(){
			$(this).trigger('mouseenter');
		});
		$('.popupsearch').on( "touchstart", function(){
			$(this).children("input").trigger('mouseenter');
		});
    }, 600);

    $scope.showSearchInputLoader = false;
    $scope.search_min_len = constants.SEARCH_MIN_LEN;

    $scope.goToSearch = function() {
        $scope.showSearchInputLoader = true;
        $timeout(function() {
            if ($scope.search.query && $scope.search.query.length >= constants.SEARCH_MIN_LEN) {
                if ($state.current.name == 'search') {
                    $window.location.href = 'search?q=' + $scope.search.query;
                }
                else {
                    $state.go('search', {q: $scope.search.query});
                }
                $timeout(function() {
                    $scope.showSearchInputLoader = false;
                }, 2000);
            }
            else {
                $scope.showSearchInputLoader = false;
                var notification = {
                    'message': 'Please enter atleast ' + constants.SEARCH_MIN_LEN + ' characters to search.',
                    'color': 'error'
                };
                notify(notification);
            }
        }, 1200);
    };

    $scope.goToTopSearch = function (query) {
        $window.location.href = '/search?q=' + query;
    };

    /*-------- Top 10 search ----------*/
    $scope.topSearches = [];
    $scope.loadTopSearch = function () {
        refreshService.top_search().then(function(data) {
            if(data.length) {
                $scope.topSearches = [];
                data.forEach(function (keyword) {
                    $scope.topSearches.push(keyword);
                });
            }

        });
    };
    
    if ($scope.user && $scope.user.guest == 0) {
        // Load initially.
        $timeout(function () {
            $scope.loadTopSearch();
        }, 2000);
    }
    // Trigger every 30 seconds for logged in user.
    $interval(function() {
        if ($scope.user && $scope.user.guest == 0) {
            $scope.loadTopSearch();
        }
    }, 32000);

}])

.controller('SearchCtrl', ['$scope', '$state', '$stateParams', '$http', '$timeout', '$location', 'notify', 'constants', function($scope, $state, $stateParams, $http, $timeout, $location, notify, constants) {
    // set the post id
    if (typeof $scope.search == 'undefined') {
        $scope.search = {};
    }
    if ($stateParams.q) {
        $scope.search.query = $stateParams.q;
    }
    else {
        var notification = {
            'message': 'Please enter at least ' + constants.SEARCH_MIN_LEN + ' characters to search.',
            'color': 'error'
        };
        notify(notification);
    }

    // Search tabs.
    $scope.currentTab = 'post';
    $scope.searchNavItems = [
        {
            name: 'Post',
            id:   'post' 
        },
        {
            name: 'Channel',
            id:   'channel'
        },
        {
            name: 'Tag',
            id:   'tag'
        },
        {
            name: 'Location',
            id:   'location'
        }
    ];
    $scope.changeTab = function(index){
		$("body").removeClass("wh");
        $scope.currentTab = $scope.searchNavItems[index].id;
        if ($scope.currentTab == 'post') {
            $scope.startPositionCards();
            setTimeout(function() {
                $scope.stopPositionCards(300);
            }, 1000);
        }else if ($scope.currentTab == 'tag' || $scope.currentTab == 'location') {
            $("body").addClass("wh");
        }
    };

    $scope.showSearchInputLoader = false;

    $scope.goToSearch = function() {
        $scope.showSearchInputLoader = true;
        $timeout(function() {
            if ($scope.search.query && $scope.search.query.length >= constants.SEARCH_MIN_LEN) {
                $scope.resetData();
                $state.go('search', {q: $scope.search.query});
            }
            else {
                $scope.noPostForSearch = true;
                $scope.showSearchInputLoader = false;
                var notification = {
                    'message': 'Please enter at least ' + constants.SEARCH_MIN_LEN + ' characters to search.',
                    'color': 'error'
                };
                notify(notification);
            }
        }, 1200);
    };

    $scope.searchTabFlickityOptions = {
        // wrapAround: true,
        cellAlign: 'center',
        freeScroll: true,
        wrapAround: false
    };

    // Angulargrid options.
    $scope.agOptions = {
        gridWidth: 350,
        cssGrid: true,
        pageSize: 2,
        infiniteScrollDistance: 100,
        performantScroll: true,
        infiniteScrollDelay: 3000,
        gutterSize: 0,
        refreshOnImgLoad: true,
    };

    // Initialize data.
    $scope.showFollowBtn = true;
    
    $scope.searchResults = [];
    $scope.showSearchLoader = false;
    $scope.noMorePost = false;
    $scope.noPostForSearch = false;

    $scope.tagFollowStatus = false;

    $scope.card_post_type = 'all';
    $scope.hidePostFilter = false;
    $scope.commonData.allPosts = [];
    $scope.channelUsers = [];
    $scope.searchTags = [];
    $scope.places = [];

    var page = 1;

    $scope.resetData = function() {
        page = 1;
        $scope.busy = false;
        $scope.noMorePost = false;
        $scope.hidePostFilter = false;
        $scope.card_post_type = 'all';

        $scope.searchResults = [];
        $scope.commonData.allPosts = [];
        $scope.channelUsers = [];
        $scope.searchTags = [];
        $scope.places = [];
    };

    // Set post type.
    $scope.setPostType = function(card_post_type) {
        $scope.card_post_type = card_post_type;
    };

    $scope.liveSearch = function() {
        $scope.resetData();
        $scope.fetchPostData($scope.currentTab);
    };

    var isFirstLoadMore = true;
    // Fetch the search results.
    $scope.fetchPostData = function(initiator) {
        // Return if initiator is not current tab.
        if (initiator != $scope.currentTab) {
            return;
        }
        // Minimum character needed for search.
        if (!$scope.search.query || $scope.search.query.length < constants.SEARCH_MIN_LEN) {
            $scope.noPostForSearch = true;
            return;
        }
        if ($scope.busy ) {
            isFirstLoadMore = false;
            return;
        }
        $scope.busy = true;
        // console.log('loading more data.. ' + initiator);
        // Do only for 1st page.
        if (page == 1) {
            // show loader.
            $scope.showSearchLoader = true;
        }
        // Fetch data from server.
        var payload = {
            q: $scope.search.query,
            orginal_query:$scope.search.query,
            //q: $scope.search.query.replace(/\s|\s+/g, '-').replace(/-{2,}/, '-').replace(/^\s+|\s+$/g,"").replace(/[^\w\s\-\,]/gi, ''),

            card_post_type: $scope.card_post_type,
            page: page
        };
        $http.post('/api/search', payload)
            .then(function(response) {
                if (response.data.results) {

                    $location.search('q', payload.q);
                    // Change the page title.
                    var title = encodeURIComponent(payload.q).replace(/%20/g, '+');
                    title +=  " | SWOLK SEARCH";
                    $scope.meta.title = title;
                    
                    $scope.searchResults = response.data.results;

                    // Tag following status.
                    $scope.tagFollowStatus = $scope.searchResults.tagFollowStatus == 1;

                    // For posts tab.
                    if ($scope.searchResults.posts.length < 1) {
                        if (page === 1) {
                            $scope.noPostForSearch = true;
                        }
                        else {
                            $scope.noMorePost = true;
                        }
                    }
                    else {
                        // start positioning cards.
                        // $scope.startPositionCards();
                        // start reloading masonry.
                        //$scope.startReloadCardMasonry();

                       /* var allPostsTmp = angular.copy($scope.commonData.allPosts);
                        allPostsTmp = allPostsTmp.concat($scope.searchResults.posts);
                        $scope.commonData.allPosts = allPostsTmp;*/

                        $scope.searchResults.posts.forEach(function(post) {
                            $scope.commonData.allPosts.push(post);
                        });
                    }
                    // For channel tab.
                    if ($scope.searchResults.channelUsers.length < 1) {
                        if (page === 1) {
                            $scope.noChannelForSearch = true;
                        }
                        else {
                            $scope.noMoreChannel = true;
                        }
                    }
                    else {
                        $scope.searchResults.channelUsers.forEach(function(user) {
                            $scope.channelUsers.push(user);
                        });
                    }
                    // For tag tab.
                    if ($scope.searchResults.searchTags.length < 1) {
                        if (page === 1) {
                            $scope.noTagForSearch = true;
                        }
                        else {
                            $scope.noMoreTagSearch = true;
                        }
                    }
                    else {
                        $scope.searchResults.searchTags.forEach(function(user) {
                            $scope.searchTags.push(user);
                        });
                    }
                    // For location tab.
                    if ($scope.searchResults.places.length < 1) {
                        if (page === 1) {
                            $scope.noLocationForSearch = true;
                        }
                        else {
                            $scope.noMoreLocationSearch = true;
                        }
                    }
                    else {
                        $scope.searchResults.places.forEach(function(place) {
                            $scope.places.push(place);
                        });
                    }
                }
                
                if (page == 1) {
                    setTimeout(function() {
                        $scope.showSearchLoader = false;
                    }, 1000);
                }
                page++;
                $scope.busy = false;
            },
            function(response) {
                $scope.busy = false;
                page++;
                $scope.showSearchLoader = false;
            }
        );
    }

    // Follow or unfollow the tag.
    $scope.tagFollowUnfollow = function() {
        if ($scope.tagFollowBusy) {
            return;
        }
        $scope.tagFollowBusy = true;
        var url = 'api/tag-follow-unfollow';
        var postData = {
            name: $scope.search.query
        };
        $http.post(url, postData)
            .then(function(response) {
                var search_lower;
                if ($scope.search) {
                    search_lower = $scope.search.query.toLowerCase();
                    search_lower = search_lower.regReplaceAll(' ', '-');
                }
                if (response.data.status == 1) {
                    $scope.tagFollowStatus = true;
                    
                    if ($scope.searchTags) {
                        $scope.searchTags.forEach(function(tag){
                            if ($scope.search && tag.tag_name.toLowerCase() == search_lower) {
                                tag.isFollow = 1;
                            }
                        });
                    }
                    $scope.totalFollower += 1;
                }
                else {
                    $scope.tagFollowStatus = false;
                    
                    $scope.searchTags.forEach(function(tag){
                        if (tag.tag_name.toLowerCase() == search_lower) {
                            tag.isFollow = 0;
                        }
                    });
                    if ($scope.totalFollower > 0) {
                        $scope.totalFollower -= 1;
                    }
                }
                // Remove lock.
                $scope.tagFollowBusy = false;
            },
            function (response) {
                $scope.tagFollowBusy = false;
            }
        );
    }

    $scope.tagFollowEachBusy = false;
    // Follow or unfollow indivisual tag from tag tab.
    $scope.tagFollowUnfollowEach = function(tag) {
        if ($scope.tagFollowEachBusy) {
            return;
        }
        $scope.tagFollowEachBusy = true;
        var url = 'api/tag-follow-unfollow';
        var postData = {
            name: tag.tag_name
        };
        $http.post(url, postData)
            .then(function(response) {
                var search_lower = $scope.search.query.toLowerCase();
                search_lower = search_lower.regReplaceAll(' ', '-');
                if (response.data.status == 1) {
                    tag.isFollow = 1;
                    if ($scope.search && tag.tag_name.toLowerCase() == search_lower) {
                        $scope.tagFollowStatus = true;
                    }
                    // $scope.users_count += 1;
                    tag.users_count += 1;
                }
                else {
                    tag.isFollow = 0;
                    if ($scope.search && tag.tag_name.toLowerCase() == search_lower) {
                        $scope.tagFollowStatus = false;
                    }
                    if (tag.users_count > 0) {
                        tag.users_count -= 1;
                    }
                }
                // Remove lock.
                $scope.tagFollowEachBusy = false;
            },
            function (response) {
                $scope.tagFollowEachBusy = false;
            }
        );
    }

    // Follow or unfollow the place.
    $scope.placeFollowUnfollow = function(place) {
        if ($scope.placeFollowBusy) {
            return;
        }
        $scope.placeFollowBusy = true;

        var postData = {
            place_url: place.place_url
        };

        var url = 'api/place-follow-unfollow';
        $http.post(url, postData)
        .then(function(response) {
                if (response.data.status == 1) {
                    place.isFollow = 1;
                    place.users_count += 1;
                }
                else {
                     place.isFollow = 0;
                    if (place.users_count > 0) {
                        place.users_count -= 1;
                    }
                }
                // Remove lock.
                $scope.placeFollowBusy = false;
            },
            function (response) {
                $scope.placeFollowBusy = false;
            }
        );
    };

}]);
/* ng-infinite-scroll - v1.3.0 - 2016-06-30 */
angular.module('infinite-scroll', []).value('THROTTLE_MILLISECONDS', null).directive('infiniteScroll', [
  '$rootScope', '$window', '$interval', 'THROTTLE_MILLISECONDS', function($rootScope, $window, $interval, THROTTLE_MILLISECONDS) {
    return {
      scope: {
        infiniteScroll: '&',
        infiniteScrollContainer: '=',
        infiniteScrollDistance: '=',
        infiniteScrollDisabled: '=',
        infiniteScrollUseDocumentBottom: '=',
        infiniteScrollListenForEvent: '@'
      },
      link: function(scope, elem, attrs) {
        var changeContainer, checkInterval, checkWhenEnabled, container, handleInfiniteScrollContainer, handleInfiniteScrollDisabled, handleInfiniteScrollDistance, handleInfiniteScrollUseDocumentBottom, handler, height, immediateCheck, offsetTop, pageYOffset, scrollDistance, scrollEnabled, throttle, unregisterEventListener, useDocumentBottom, windowElement;
        windowElement = angular.element($window);
        scrollDistance = null;
        scrollEnabled = null;
        checkWhenEnabled = null;
        container = null;
        immediateCheck = true;
        useDocumentBottom = false;
        unregisterEventListener = null;
        checkInterval = false;
        height = function(elem) {
          elem = elem[0] || elem;
          if (isNaN(elem.offsetHeight)) {
            return elem.document.documentElement.clientHeight;
          } else {
            return elem.offsetHeight;
          }
        };
        offsetTop = function(elem) {
          if (!elem[0].getBoundingClientRect || elem.css('none')) {
            return;
          }
          return elem[0].getBoundingClientRect().top + pageYOffset(elem);
        };
        pageYOffset = function(elem) {
          elem = elem[0] || elem;
          if (isNaN(window.pageYOffset)) {
            return elem.document.documentElement.scrollTop;
          } else {
            return elem.ownerDocument.defaultView.pageYOffset;
          }
        };
        handler = function() {
          var containerBottom, containerTopOffset, elementBottom, remaining, shouldScroll;
          if (container === windowElement) {
            containerBottom = height(container) + pageYOffset(container[0].document.documentElement);
            elementBottom = offsetTop(elem) + height(elem);
          } else {
            containerBottom = height(container);
            containerTopOffset = 0;
            if (offsetTop(container) !== void 0) {
              containerTopOffset = offsetTop(container);
            }
            elementBottom = offsetTop(elem) - containerTopOffset + height(elem);
          }
          if (useDocumentBottom) {
            elementBottom = height((elem[0].ownerDocument || elem[0].document).documentElement);
          }
          remaining = elementBottom - containerBottom + 750;// 750 added customly to make it 250px.
		 // console.log(remaining);
		 //  console.log(height(container) * scrollDistance + 1);
          shouldScroll = remaining <= height(container) * scrollDistance + 1;
          if (shouldScroll) {
            checkWhenEnabled = true;
            if (scrollEnabled) {
              if (scope.$$phase || $rootScope.$$phase) {
                return scope.infiniteScroll();
              } else {
                return scope.$apply(scope.infiniteScroll);
              }
            }
          } else {
            if (checkInterval) {
              $interval.cancel(checkInterval);
            }
            return checkWhenEnabled = false;
          }
        };
        throttle = function(func, wait) {
          var later, previous, timeout;
          timeout = null;
          previous = 0;
          later = function() {
            previous = new Date().getTime();
            $interval.cancel(timeout);
            timeout = null;
            return func.call();
          };
          return function() {
            var now, remaining;
            now = new Date().getTime();
            remaining = wait - (now - previous);
            if (remaining <= 0) {
              $interval.cancel(timeout);
              timeout = null;
              previous = now;
              return func.call();
            } else {
              if (!timeout) {
                return timeout = $interval(later, remaining, 1);
              }
            }
          };
        };
        if (THROTTLE_MILLISECONDS != null) {
          handler = throttle(handler, THROTTLE_MILLISECONDS);
        }
        scope.$on('$destroy', function() {
          container.unbind('scroll', handler);
          if (unregisterEventListener != null) {
            unregisterEventListener();
            unregisterEventListener = null;
          }
          if (checkInterval) {
            return $interval.cancel(checkInterval);
          }
        });
        handleInfiniteScrollDistance = function(v) {
          return scrollDistance = parseFloat(v) || 0;
        };
        scope.$watch('infiniteScrollDistance', handleInfiniteScrollDistance);
        handleInfiniteScrollDistance(scope.infiniteScrollDistance);
        handleInfiniteScrollDisabled = function(v) {
          scrollEnabled = !v;
          if (scrollEnabled && checkWhenEnabled) {
            checkWhenEnabled = false;
            return handler();
          }
        };
        scope.$watch('infiniteScrollDisabled', handleInfiniteScrollDisabled);
        handleInfiniteScrollDisabled(scope.infiniteScrollDisabled);
        handleInfiniteScrollUseDocumentBottom = function(v) {
          return useDocumentBottom = v;
        };
        scope.$watch('infiniteScrollUseDocumentBottom', handleInfiniteScrollUseDocumentBottom);
        handleInfiniteScrollUseDocumentBottom(scope.infiniteScrollUseDocumentBottom);
        changeContainer = function(newContainer) {
          if (container != null) {
            container.unbind('scroll', handler);
          }
          container = newContainer;
          if (newContainer != null) {
            return container.bind('scroll', handler);
          }
        };
        changeContainer(windowElement);
        if (scope.infiniteScrollListenForEvent) {
          unregisterEventListener = $rootScope.$on(scope.infiniteScrollListenForEvent, handler);
        }
        handleInfiniteScrollContainer = function(newContainer) {
          if ((newContainer == null) || newContainer.length === 0) {
            return;
          }
          if (newContainer.nodeType && newContainer.nodeType === 1) {
            newContainer = angular.element(newContainer);
          } else if (typeof newContainer.append === 'function') {
            newContainer = angular.element(newContainer[newContainer.length - 1]);
          } else if (typeof newContainer === 'string') {
            newContainer = angular.element(document.querySelector(newContainer));
          }
          if (newContainer != null) {
            return changeContainer(newContainer);
          } else {
            throw new Error("invalid infinite-scroll-container attribute.");
          }
        };
        scope.$watch('infiniteScrollContainer', handleInfiniteScrollContainer);
        handleInfiniteScrollContainer(scope.infiniteScrollContainer || []);
        if (attrs.infiniteScrollParent != null) {
          changeContainer(angular.element(elem.parent()));
        }
        if (attrs.infiniteScrollImmediateCheck != null) {
          immediateCheck = scope.$eval(attrs.infiniteScrollImmediateCheck);
        }
        return checkInterval = $interval((function() {
          if (immediateCheck) {
            handler();
          }
          return $interval.cancel(checkInterval);
        }));
      }
    };
  }
]);

if (typeof module !== "undefined" && typeof exports !== "undefined" && module.exports === exports) {
  module.exports = 'infinite-scroll';
}
