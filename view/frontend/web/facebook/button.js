define([
    'jquery',
    'DevGenii_SocialConnect/fb-sdk'
], function($, FB) {
    var o = {
        config: null,
        init: function () {
            FB.init({
                appId      : o.config.appId,
                xfbml      : true,
                version    : 'v2.7'
            });
        },
        subscribe: function () {
            FB.Event.subscribe('auth.statusChange', this.authCallback);
        },
        authCallback: function (response) {
            if ('authResponse' in response) {
                var ajaxUrl = o.config.ajaxUrl;
                var state = o.config.state;
                // User accepted
                // Successful login, do ajax request
                $.ajax({
                    type: 'POST',
                    url:  ajaxUrl,
                    data: {
                        // CSRF protection
                        state:  state,

                        // Short lived token
                        access_token: response.authResponse.accessToken,

                        // When this token expires
                        expires_in: response.authResponse.expiresIn
                    },
                    dataType: 'json',
                    success: function(data) {
                        if(data && 'redirect' in data) {
                            window.location.replace(data.redirect);
                        } else {
                            window.location.reload();
                        }
                    }
                });
            }
        },
        'DevGenii_SocialConnect/facebook/button': function (config) {
            this.config = config;
            this.init();
            this.subscribe();
        }
    };
    return o;
});