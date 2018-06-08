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
                    onEnter: function($location, $state){
                        if (angular.equals($location.search(), {})) {
                            $state.go('profile');
                        }
                    },
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
