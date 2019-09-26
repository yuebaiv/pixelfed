<?php

Route::domain(config('pixelfed.domain.admin'))->prefix('i/admin')->group(function () {
    Route::redirect('/', '/dashboard');
    Route::redirect('timeline', config('app.url').'/timeline');
    Route::get('dashboard', 'AdminController@home')->name('admin.home');
    Route::get('reports', 'AdminController@reports')->name('admin.reports');
    Route::get('reports/show/{id}', 'AdminController@showReport');
    Route::post('reports/show/{id}', 'AdminController@updateReport');
    Route::post('reports/bulk', 'AdminController@bulkUpdateReport');
    Route::redirect('statuses', '/statuses/list');
    Route::get('statuses/list', 'AdminController@statuses')->name('admin.statuses');
    Route::get('statuses/show/{id}', 'AdminController@showStatus');
    Route::redirect('profiles', '/i/admin/profiles/list');
    Route::get('profiles/list', 'AdminController@profiles')->name('admin.profiles');
    Route::get('profiles/edit/{id}', 'AdminController@profileShow');
    Route::redirect('users', '/users/list');
    Route::get('users/list', 'AdminController@users')->name('admin.users');
    Route::get('users/edit/{id}', 'AdminController@editUser');
    Route::get('media', 'AdminController@media')->name('admin.media');
    Route::redirect('media/list', '/i/admin/media');
    Route::get('media/show/{id}', 'AdminController@mediaShow');
    Route::get('settings', 'AdminController@settings')->name('admin.settings');
    Route::post('settings', 'AdminController@settingsHomeStore');
    Route::get('settings/config', 'AdminController@settingsConfig')->name('admin.settings.config');
    Route::post('settings/config', 'AdminController@settingsConfigStore');
    Route::post('settings/config/restore', 'AdminController@settingsConfigRestore');
    Route::get('settings/features', 'AdminController@settingsFeatures')->name('admin.settings.features');
    Route::get('settings/pages', 'AdminController@settingsPages')->name('admin.settings.pages');
    Route::get('settings/pages/edit', 'PageController@edit')->name('admin.settings.pages.edit');
    Route::post('settings/pages/edit', 'PageController@store');
    Route::post('settings/pages/delete', 'PageController@delete');
    Route::post('settings/pages/create', 'PageController@generatePage');
    Route::get('settings/maintenance', 'AdminController@settingsMaintenance')->name('admin.settings.maintenance');
    Route::get('settings/backups', 'AdminController@settingsBackups')->name('admin.settings.backups');
    Route::get('settings/storage', 'AdminController@settingsStorage')->name('admin.settings.storage');
    Route::get('settings/system', 'AdminController@settingsSystem')->name('admin.settings.system');

    Route::get('instances', 'AdminController@instances')->name('admin.instances');
    Route::post('instances', 'AdminController@instanceScan');
    Route::get('instances/show/{id}', 'AdminController@instanceShow');
    Route::post('instances/edit/{id}', 'AdminController@instanceEdit');
    Route::get('apps/home', 'AdminController@appsHome')->name('admin.apps');
    Route::get('hashtags/home', 'AdminController@hashtagsHome')->name('admin.hashtags');
    Route::get('discover/home', 'AdminController@discoverHome')->name('admin.discover');
    Route::get('discover/category/create', 'AdminController@discoverCreateCategory')->name('admin.discover.create-category');
    Route::post('discover/category/create', 'AdminController@discoverCreateCategoryStore');
    Route::get('discover/category/edit/{id}', 'AdminController@discoverCategoryEdit');
    Route::post('discover/category/edit/{id}', 'AdminController@discoverCategoryUpdate');
    Route::post('discover/category/hashtag/create', 'AdminController@discoveryCategoryTagStore')->name('admin.discover.create-hashtag');

    Route::get('messages/home', 'AdminController@messagesHome')->name('admin.messages');
    Route::get('messages/show/{id}', 'AdminController@messagesShow');
    Route::post('messages/mark-read', 'AdminController@messagesMarkRead');
});

Route::domain(config('pixelfed.domain.app'))->middleware(['validemail', 'twofactor', 'localization'])->group(function () {
    Route::get('/', 'SiteController@home')->name('timeline.personal');
    Route::post('/', 'StatusController@store');

    Auth::routes();

    Route::get('.well-known/webfinger', 'FederationController@webfinger')->name('well-known.webfinger');
    Route::get('.well-known/nodeinfo', 'FederationController@nodeinfoWellKnown')->name('well-known.nodeinfo');
    Route::get('.well-known/host-meta', 'FederationController@hostMeta')->name('well-known.hostMeta');
    Route::redirect('.well-known/change-password', '/settings/password');

    Route::get('/home', 'HomeController@index')->name('home');

    Route::get('discover/c/{slug}', 'DiscoverController@showCategory');
    Route::redirect('discover/personal', '/discover');
    Route::get('discover', 'DiscoverController@home')->name('discover');
    Route::get('discover/loops', 'DiscoverController@showLoops');
    
    Route::group(['prefix' => 'api'], function () {
        Route::get('search', 'SearchController@searchAPI');
        Route::get('nodeinfo/2.0.json', 'FederationController@nodeinfo');

        Route::group(['prefix' => 'v1'], function () {
            Route::get('accounts/verify_credentials', 'ApiController@verifyCredentials')->middleware('auth:api');
            Route::patch('accounts/update_credentials', 'Api\ApiV1Controller@accountUpdateCredentials')->middleware('auth:api');
            Route::get('accounts/relationships', 'Api\ApiV1Controller@accountRelationshipsById')->middleware('auth:api');
            Route::get('accounts/search', 'Api\ApiV1Controller@accountSearch')->middleware('auth:api');
            Route::get('accounts/{id}/statuses', 'Api\ApiV1Controller@accountStatusesById')->middleware('auth:api');
            Route::get('accounts/{id}/following', 'Api\ApiV1Controller@accountFollowingById')->middleware('auth:api');
            Route::get('accounts/{id}/followers', 'Api\ApiV1Controller@accountFollowersById')->middleware('auth:api');
            Route::post('accounts/{id}/follow', 'Api\ApiV1Controller@accountFollowById')->middleware('auth:api');
            Route::post('accounts/{id}/unfollow', 'Api\ApiV1Controller@accountUnfollowById')->middleware('auth:api');
            Route::post('accounts/{id}/block', 'Api\ApiV1Controller@accountBlockById')->middleware('auth:api');
            Route::post('accounts/{id}/unblock', 'Api\ApiV1Controller@accountUnblockById')->middleware('auth:api');
            Route::post('accounts/{id}/pin', 'Api\ApiV1Controller@accountEndorsements')->middleware('auth:api');
            Route::post('accounts/{id}/unpin', 'Api\ApiV1Controller@accountEndorsements')->middleware('auth:api');
            // Route::get('accounts/{id}', 'PublicApiController@account');
            Route::post('avatar/update', 'ApiController@avatarUpdate')->middleware('auth:api');
            Route::get('domain_blocks', 'Api\ApiV1Controller@accountDomainBlocks')->middleware('auth:api');
            Route::post('domain_blocks', 'Api\ApiV1Controller@accountDomainBlocks')->middleware('auth:api');
            Route::delete('domain_blocks', 'Api\ApiV1Controller@accountDomainBlocks')->middleware('auth:api');
            Route::get('endorsements', 'Api\ApiV1Controller@accountEndorsements')->middleware('auth:api');
            Route::get('blocks', 'Api\ApiV1Controller@accountBlocks')->middleware('auth:api');
            Route::get('custom_emojis', 'Api\ApiV1Controller@customEmojis');
            Route::get('favourites', 'Api\ApiV1Controller@accountFavourites')->middleware('auth:api');
            Route::post('statuses/{id}/favourite', 'Api\ApiV1Controller@statusFavouriteById')->middleware('auth:api');
            Route::post('statuses/{id}/unfavourite', 'Api\ApiV1Controller@statusUnfavouriteById')->middleware('auth:api');
            Route::get('filters', 'Api\ApiV1Controller@accountFilters')->middleware('auth:api');
            Route::get('follow_requests', 'Api\ApiV1Controller@accountFollowRequests')->middleware('auth:api');
            Route::post('follow_requests/{id}/authorize', 'Api\ApiV1Controller@accountFollowRequestAccept')->middleware('auth:api');
            Route::post('follow_requests/{id}/reject', 'Api\ApiV1Controller@accountFollowRequestReject')->middleware('auth:api');
            Route::get('suggestions', 'Api\ApiV1Controller@accountSuggestions')->middleware('auth:api');
            Route::get('lists', 'Api\ApiV1Controller@accountLists')->middleware('auth:api');
            Route::get('accounts/{id}/lists', 'Api\ApiV1Controller@accountListsById')->middleware('auth:api');
            Route::get('lists/{id}/accounts', 'Api\ApiV1Controller@accountListsById')->middleware('auth:api');
            Route::post('media', 'Api\ApiV1Controller@mediaUpload')->middleware('auth:api');
            Route::put('media/{id}', 'Api\ApiV1Controller@mediaUpdate')->middleware('auth:api');
            Route::get('mutes', 'Api\ApiV1Controller@accountMutes')->middleware('auth:api');
            Route::post('accounts/{id}/mute', 'Api\ApiV1Controller@accountMuteById')->middleware('auth:api');
            Route::post('accounts/{id}/unmute', 'Api\ApiV1Controller@accountUnmuteById')->middleware('auth:api');
            Route::get('notifications', 'Api\ApiV1Controller@accountNotifications')->middleware('auth:api');

            // Route::get('likes', 'ApiController@hydrateLikes');
            // Route::post('media', 'ApiController@uploadMedia')->middleware('auth:api');
            // Route::delete('media', 'ApiController@deleteMedia')->middleware('auth:api');
            // Route::get('notifications', 'ApiController@notifications')->middleware('auth:api');
            // Route::get('timelines/public', 'PublicApiController@publicTimelineApi');
            // Route::get('timelines/home', 'PublicApiController@homeTimelineApi')->middleware('auth:api');
            // Route::post('status', 'Api\ApiV1Controller@createStatus')->middleware('auth:api');
            Route::get('accounts/{id}', 'Api\ApiV1Controller@accountById');
        });
        Route::group(['prefix' => 'v2'], function() {
            Route::get('config', 'ApiController@siteConfiguration');
            Route::get('discover', 'InternalApiController@discover');
            Route::get('discover/posts', 'InternalApiController@discoverPosts');
            Route::get('profile/{username}/status/{postid}', 'PublicApiController@status');
            Route::get('comments/{username}/status/{postId}', 'PublicApiController@statusComments');
            Route::get('likes/profile/{username}/status/{id}', 'PublicApiController@statusLikes');
            Route::get('shares/profile/{username}/status/{id}', 'PublicApiController@statusShares');
            Route::get('status/{id}/replies', 'InternalApiController@statusReplies');
            Route::post('moderator/action', 'InternalApiController@modAction');
            Route::get('discover/categories', 'InternalApiController@discoverCategories');
            Route::get('loops', 'DiscoverController@loopsApi');
            Route::post('loops/watch', 'DiscoverController@loopWatch');
            Route::get('discover/tag', 'DiscoverController@getHashtags');
            Route::post('status/compose', 'InternalApiController@composePost')->middleware('throttle:maxPostsPerHour,60')->middleware('throttle:maxPostsPerDay,1440');
        });
        Route::group(['prefix' => 'pixelfed'], function() {
            Route::group(['prefix' => 'v1'], function() {
                Route::get('accounts/verify_credentials', 'ApiController@verifyCredentials');
                Route::get('accounts/relationships', 'Api\ApiV1Controller@accountRelationshipsById');
                Route::get('accounts/search', 'Api\ApiV1Controller@accountSearch');
                Route::get('accounts/{id}/statuses', 'PublicApiController@accountStatuses');
                Route::get('accounts/{id}/following', 'PublicApiController@accountFollowing');
                Route::get('accounts/{id}/followers', 'PublicApiController@accountFollowers');
                Route::post('accounts/{id}/block', 'Api\ApiV1Controller@accountBlockById');
                Route::post('accounts/{id}/unblock', 'Api\ApiV1Controller@accountUnblockById');
                Route::get('accounts/{id}', 'PublicApiController@account');
                Route::post('avatar/update', 'ApiController@avatarUpdate');
                Route::get('custom_emojis', 'Api\ApiV1Controller@customEmojis');
                Route::get('likes', 'ApiController@hydrateLikes');
                Route::post('media', 'ApiController@uploadMedia');
                Route::delete('media', 'ApiController@deleteMedia');
                Route::get('notifications', 'ApiController@notifications');
                Route::get('timelines/public', 'PublicApiController@publicTimelineApi');
                Route::get('timelines/home', 'PublicApiController@homeTimelineApi');
            });
        });
        Route::group(['prefix' => 'local'], function () {
            // Route::get('accounts/verify_credentials', 'ApiController@verifyCredentials');
            // Route::get('accounts/relationships', 'PublicApiController@relationships');
            // Route::get('accounts/{id}/statuses', 'PublicApiController@accountStatuses');
            // Route::get('accounts/{id}/following', 'PublicApiController@accountFollowing');
            // Route::get('accounts/{id}/followers', 'PublicApiController@accountFollowers');
            // Route::get('accounts/{id}', 'PublicApiController@account');
            // Route::post('avatar/update', 'ApiController@avatarUpdate');
            // Route::get('likes', 'ApiController@hydrateLikes');
            // Route::post('media', 'ApiController@uploadMedia');
            // Route::delete('media', 'ApiController@deleteMedia');
            // Route::get('notifications', 'ApiController@notifications');
            // Route::get('timelines/public', 'PublicApiController@publicTimelineApi');
            // Route::get('timelines/home', 'PublicApiController@homeTimelineApi');

            Route::post('status/compose', 'InternalApiController@composePost')->middleware('throttle:maxPostsPerHour,60')->middleware('throttle:maxPostsPerDay,1440');
            Route::get('exp/rec', 'ApiController@userRecommendations');
            Route::post('discover/tag/subscribe', 'HashtagFollowController@store')->middleware('throttle:maxHashtagFollowsPerHour,60')->middleware('throttle:maxHashtagFollowsPerDay,1440');;
            Route::get('discover/tag/list', 'HashtagFollowController@getTags');
            Route::get('profile/sponsor/{id}', 'ProfileSponsorController@get');
            Route::get('bookmarks', 'InternalApiController@bookmarks');
            Route::get('collection/items/{id}', 'CollectionController@getItems');
            Route::post('collection/item', 'CollectionController@storeId');
            Route::get('collection/{id}', 'CollectionController@get');
            Route::post('collection/{id}', 'CollectionController@store');
            Route::delete('collection/{id}', 'CollectionController@delete')->middleware('throttle:maxCollectionsPerHour,60')->middleware('throttle:maxCollectionsPerDay,1440')->middleware('throttle:maxCollectionsPerMonth,43800');
            Route::post('collection/{id}/publish', 'CollectionController@publish')->middleware('throttle:maxCollectionsPerHour,60')->middleware('throttle:maxCollectionsPerDay,1440')->middleware('throttle:maxCollectionsPerMonth,43800');
            Route::get('profile/collections/{id}', 'CollectionController@getUserCollections');

            Route::post('compose/media/update/{id}', 'MediaController@composeUpdate')->middleware('throttle:maxComposeMediaUpdatesPerHour,60')->middleware('throttle:maxComposeMediaUpdatesPerDay,1440')->middleware('throttle:maxComposeMediaUpdatesPerMonth,43800');
            Route::get('compose/location/search', 'ApiController@composeLocationSearch');
        });
        Route::group(['prefix' => 'admin'], function () {
            Route::post('moderate', 'Api\AdminApiController@moderate');
        });

    });

    Route::get('discover/tags/{hashtag}', 'DiscoverController@showTags');
    Route::get('discover/places', 'PlaceController@directoryHome')->name('discover.places');
    Route::get('discover/places/{id}/{slug}', 'PlaceController@show');
    Route::get('discover/location/country/{country}', 'PlaceController@directoryCities');

    Route::group(['prefix' => 'i'], function () {
        Route::redirect('/', '/');
        Route::get('compose', 'StatusController@compose')->name('compose');
        Route::post('comment', 'CommentController@store')->middleware('throttle:maxCommentsPerHour,60')->middleware('throttle:maxCommentsPerDay,1440');
        Route::post('delete', 'StatusController@delete');
        Route::post('mute', 'AccountController@mute');
        Route::post('unmute', 'AccountController@unmute');
        Route::post('block', 'AccountController@block');
        Route::post('unblock', 'AccountController@unblock');
        Route::post('like', 'LikeController@store')->middleware('throttle:maxLikesPerHour,60')->middleware('throttle:maxLikesPerDay,1440');
        Route::post('share', 'StatusController@storeShare')->middleware('throttle:maxSharesPerHour,60')->middleware('throttle:maxSharesPerDay,1440');
        Route::post('follow', 'FollowerController@store');
        Route::post('bookmark', 'BookmarkController@store');
        Route::get('lang/{locale}', 'SiteController@changeLocale');
        Route::get('restored', 'AccountController@accountRestored');

        Route::get('verify-email', 'AccountController@verifyEmail');
        Route::post('verify-email', 'AccountController@sendVerifyEmail');
        Route::get('confirm-email/{userToken}/{randomToken}', 'AccountController@confirmVerifyEmail');

        Route::get('auth/sudo', 'AccountController@sudoMode');
        Route::post('auth/sudo', 'AccountController@sudoModeVerify');
        Route::get('auth/checkpoint', 'AccountController@twoFactorCheckpoint');
        Route::post('auth/checkpoint', 'AccountController@twoFactorVerify');

        Route::get('media/preview/{profileId}/{mediaId}', 'ApiController@showTempMedia')->name('temp-media');

        Route::get('results', 'SearchController@results');
        Route::post('visibility', 'StatusController@toggleVisibility');

        Route::post('metro/dark-mode', 'SettingsController@metroDarkMode');

        Route::group(['prefix' => 'report'], function () {
            Route::get('/', 'ReportController@showForm')->name('report.form');
            Route::post('/', 'ReportController@formStore');
            Route::get('not-interested', 'ReportController@notInterestedForm')->name('report.not-interested');
            Route::get('spam', 'ReportController@spamForm')->name('report.spam');
            Route::get('spam/comment', 'ReportController@spamCommentForm')->name('report.spam.comment');
            Route::get('spam/post', 'ReportController@spamPostForm')->name('report.spam.post');
            Route::get('spam/profile', 'ReportController@spamProfileForm')->name('report.spam.profile');
            Route::get('sensitive/comment', 'ReportController@sensitiveCommentForm')->name('report.sensitive.comment');
            Route::get('sensitive/post', 'ReportController@sensitivePostForm')->name('report.sensitive.post');
            Route::get('sensitive/profile', 'ReportController@sensitiveProfileForm')->name('report.sensitive.profile');
            Route::get('abusive/comment', 'ReportController@abusiveCommentForm')->name('report.abusive.comment');
            Route::get('abusive/post', 'ReportController@abusivePostForm')->name('report.abusive.post');
            Route::get('abusive/profile', 'ReportController@abusiveProfileForm')->name('report.abusive.profile');
        });

        Route::get('collections/create', 'CollectionController@create');

        Route::get('me', 'ProfileController@meRedirect');
    });

    Route::group(['prefix' => 'account'], function () {
        Route::redirect('/', '/');
        Route::get('activity', 'AccountController@notifications')->name('notifications');
        Route::get('follow-requests', 'AccountController@followRequests')->name('follow-requests');
        Route::post('follow-requests', 'AccountController@followRequestHandle');
    });

    Route::group(['prefix' => 'settings'], function () {
        Route::redirect('/', '/settings/home');
        Route::get('home', 'SettingsController@home')
        ->name('settings');
        Route::post('home', 'SettingsController@homeUpdate');
        Route::get('avatar', 'SettingsController@avatar')->name('settings.avatar');
        Route::post('avatar', 'AvatarController@store');
        Route::delete('avatar', 'AvatarController@deleteAvatar');
        Route::get('password', 'SettingsController@password')->name('settings.password')->middleware('dangerzone');
        Route::post('password', 'SettingsController@passwordUpdate')->middleware('dangerzone');
        Route::get('email', 'SettingsController@email')->name('settings.email');
        Route::post('email', 'SettingsController@emailUpdate');
        Route::get('notifications', 'SettingsController@notifications')->name('settings.notifications');
        Route::get('privacy', 'SettingsController@privacy')->name('settings.privacy');
        Route::post('privacy', 'SettingsController@privacyStore');
        Route::get('privacy/muted-users', 'SettingsController@mutedUsers')->name('settings.privacy.muted-users');
        Route::post('privacy/muted-users', 'SettingsController@mutedUsersUpdate');
        Route::get('privacy/blocked-users', 'SettingsController@blockedUsers')->name('settings.privacy.blocked-users');
        Route::post('privacy/blocked-users', 'SettingsController@blockedUsersUpdate');
        Route::get('privacy/blocked-instances', 'SettingsController@blockedInstances')->name('settings.privacy.blocked-instances');
        Route::post('privacy/blocked-instances', 'SettingsController@blockedInstanceStore')->middleware('throttle:maxInstanceBansPerDay,1440');
        Route::post('privacy/blocked-instances/unblock', 'SettingsController@blockedInstanceUnblock')->name('settings.privacy.blocked-instances.unblock');
        Route::get('privacy/blocked-keywords', 'SettingsController@blockedKeywords')->name('settings.privacy.blocked-keywords');
        Route::post('privacy/account', 'SettingsController@privateAccountOptions')->name('settings.privacy.account');
        Route::get('reports', 'SettingsController@reportsHome')->name('settings.reports');
        Route::group(['prefix' => 'remove', 'middleware' => 'dangerzone'], function() {
            Route::get('request/temporary', 'SettingsController@removeAccountTemporary')->name('settings.remove.temporary');
            Route::post('request/temporary', 'SettingsController@removeAccountTemporarySubmit');
            Route::get('request/permanent', 'SettingsController@removeAccountPermanent')->name('settings.remove.permanent');
            Route::post('request/permanent', 'SettingsController@removeAccountPermanentSubmit');
        });

        Route::group(['prefix' => 'security', 'middleware' => 'dangerzone'], function() {
            Route::get(
                '/', 
                'SettingsController@security'
            )->name('settings.security');
            Route::get(
                '2fa/setup', 
                'SettingsController@securityTwoFactorSetup'
            )->name('settings.security.2fa.setup');
            Route::post(
                '2fa/setup', 
                'SettingsController@securityTwoFactorSetupStore'
            );
            Route::get(
                '2fa/edit', 
                'SettingsController@securityTwoFactorEdit'
            )->name('settings.security.2fa.edit');
            Route::post(
                '2fa/edit', 
                'SettingsController@securityTwoFactorUpdate'
            );
            Route::get(
                '2fa/recovery-codes',
                'SettingsController@securityTwoFactorRecoveryCodes'
            )->name('settings.security.2fa.recovery');
            Route::post(
                '2fa/recovery-codes',
                'SettingsController@securityTwoFactorRecoveryCodesRegenerate'
            );

        });

        Route::get('applications', 'SettingsController@applications')->name('settings.applications')->middleware('dangerzone');
        Route::get('data-export', 'SettingsController@dataExport')->name('settings.dataexport')->middleware('dangerzone');
        Route::post('data-export/following', 'SettingsController@exportFollowing')->middleware('dangerzone');
        Route::post('data-export/followers', 'SettingsController@exportFollowers')->middleware('dangerzone');
        Route::post('data-export/mute-block-list', 'SettingsController@exportMuteBlockList')->middleware('dangerzone');
        Route::post('data-export/account', 'SettingsController@exportAccount')->middleware('dangerzone');
        Route::post('data-export/statuses', 'SettingsController@exportStatuses')->middleware('dangerzone');
        Route::get('developers', 'SettingsController@developers')->name('settings.developers')->middleware('dangerzone');
        Route::get('labs', 'SettingsController@labs')->name('settings.labs');
        Route::post('labs', 'SettingsController@labsStore');

        Route::get('accessibility', 'SettingsController@accessibility')->name('settings.accessibility');
        Route::post('accessibility', 'SettingsController@accessibilityStore');

        Route::group(['prefix' => 'relationships'], function() {
            Route::redirect('/', '/settings/relationships/home');
            Route::get('home', 'SettingsController@relationshipsHome')->name('settings.relationships');
        });
        Route::get('invites/create', 'UserInviteController@create')->name('settings.invites.create');
        Route::post('invites/create', 'UserInviteController@store');
        Route::get('invites', 'UserInviteController@show')->name('settings.invites');
        Route::get('sponsor', 'SettingsController@sponsor')->name('settings.sponsor');
        Route::post('sponsor', 'SettingsController@sponsorStore');
    });

    Route::group(['prefix' => 'site'], function () {
        Route::redirect('/', '/');
        Route::get('about', 'SiteController@about')->name('site.about');
        Route::view('help', 'site.help')->name('site.help');
        Route::view('developer-api', 'site.developer')->name('site.developers');
        Route::view('fediverse', 'site.fediverse')->name('site.fediverse');
        Route::view('open-source', 'site.opensource')->name('site.opensource');
        Route::view('banned-instances', 'site.bannedinstances')->name('site.bannedinstances');
        Route::get('terms', 'SiteController@terms')->name('site.terms');
        Route::get('privacy', 'SiteController@privacy')->name('site.privacy');
        Route::view('platform', 'site.platform')->name('site.platform');
        Route::view('language', 'site.language')->name('site.language');
        Route::get('contact', 'ContactController@show')->name('site.contact');
        Route::post('contact', 'ContactController@store');
        Route::group(['prefix'=>'kb'], function() {
            Route::view('getting-started', 'site.help.getting-started')->name('help.getting-started');
            Route::view('sharing-media', 'site.help.sharing-media')->name('help.sharing-media');
            Route::view('your-profile', 'site.help.your-profile')->name('help.your-profile');
            Route::view('stories', 'site.help.stories')->name('help.stories');
            Route::view('embed', 'site.help.embed')->name('help.embed');
            Route::view('hashtags', 'site.help.hashtags')->name('help.hashtags');
            Route::view('discover', 'site.help.discover')->name('help.discover');
            Route::view('direct-messages', 'site.help.dm')->name('help.dm');
            Route::view('timelines', 'site.help.timelines')->name('help.timelines');
            Route::view('what-is-the-fediverse', 'site.help.what-is-fediverse')->name('help.what-is-fediverse');
            Route::view('safety-tips', 'site.help.safety-tips')->name('help.safety-tips');

            Route::get('community-guidelines', 'SiteController@communityGuidelines')->name('help.community-guidelines');
            Route::view('controlling-visibility', 'site.help.controlling-visibility')->name('help.controlling-visibility');
            Route::view('blocking-accounts', 'site.help.blocking-accounts')->name('help.blocking-accounts');
            Route::view('report-something', 'site.help.report-something')->name('help.report-something');
            Route::view('data-policy', 'site.help.data-policy')->name('help.data-policy');
        });
    });

    Route::group(['prefix' => 'timeline'], function () {
        Route::redirect('/', '/');
        Route::get('public', 'TimelineController@local')->name('timeline.public');
    });

    Route::group(['prefix' => 'users'], function () {
        Route::redirect('/', '/');
        Route::get('{user}.atom', 'ProfileController@showAtomFeed');
        Route::get('{username}/outbox', 'FederationController@userOutbox');
        Route::get('{username}/followers', 'FederationController@userFollowers');
        Route::get('{username}/following', 'FederationController@userFollowing');
        Route::get('{username}', 'ProfileController@permalinkRedirect');
    });

    Route::get('c/{collection}', 'CollectionController@show');
    Route::get('p/{username}/{id}/c', 'CommentController@showAll');
    Route::get('p/{username}/{id}/edit', 'StatusController@edit');
    Route::post('p/{username}/{id}/edit', 'StatusController@editStore');
    Route::get('p/{username}/{id}.json', 'StatusController@showObject');
    Route::get('p/{username}/{id}', 'StatusController@show');
    Route::get('{username}', 'ProfileController@show');
});
