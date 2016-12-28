/**
 * Copyright © 2016 DevGenii. All rights reserved.
 * See LICENSE.txt for license details.
 */
/*jshint browser:true jquery:true*/
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
                this._super();
            },
            isLoggedIn: function () {
                return customer.isLoggedIn();
            },
            isEnabled: function () {
                return ((((window.checkoutConfig || {}).devGeniiSocialConnect || {}).facebook || {}).enabled == true);
            },
            afterRender: function (element) {
                if(this.isEnabled() && !this.isLoggedIn()) {
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
            }
        });
    }
);