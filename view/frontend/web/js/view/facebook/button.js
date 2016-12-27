define(
    [
        'jquery',
        'uiComponent',
        'ko',
        'Magento_Customer/js/model/customer'
    ],
    function($, Component, ko, customer, button) {
        'use strict';
        return Component.extend({
            defaults: {
                template: 'DevGenii_SocialConnect/facebook/button'
            },
            initialize: function () {
                // Initialize button only if enabled
                if((((window.checkoutConfig || {}).devGeniiSocialConnect || {}).facebook || {}).enabled &&
                    !customer.isLoggedIn()) {
                    this._super();
                }
            },
            afterRender: function (element) {
                // Scope is configurable
                $(element).data(
                    'scope',
                    window.checkoutConfig.devGeniiSocialConnect.facebook.scope
                );

                // Initialize button module
                var module = 'DevGenii_SocialConnect/facebook/button';
                require([module], function(button) {
                    if($.isFunction(button[module])) {
                        button[module](window.checkoutConfig.devGeniiSocialConnect.facebook);
                    }
                })
            }
        });
    }
);