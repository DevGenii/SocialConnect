define(
    [
        'uiComponent',
        'ko',
        'Magento_Customer/js/model/customer',
        'DevGenii_SocialConnect/facebook/button'
    ],
    function(Component, ko, customer, button) {
        'use strict';
        return Component.extend({
            defaults: {
                template: 'DevGenii_SocialConnect/facebook/button'
            },
            isCustomerLoggedIn: customer.isLoggedIn,
            initialize: function () {
                this._super();
            }
        });
    }
);