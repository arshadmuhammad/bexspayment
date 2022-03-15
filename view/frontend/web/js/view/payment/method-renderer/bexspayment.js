define(
    [
        'jquery',
        'Magento_Checkout/js/view/payment/default',
        'mage/url'
    ],
    function ($, Component, url) {
        'use strict';

        return Component.extend({
            redirectAfterPlaceOrder: false,

            defaults: {
                template: 'MagArs_Bexs/payment/bexspayment'
            },

            initialize: function() {
                this._super();
                self = this;
            },

            getCode: function() {
                return 'bexspayment';
            },

            getData: function() {
                var data = {
                    'method': this.getCode()
                };
                return data;
            },

            afterPlaceOrder: function () {
                // create BEXS Payment
                $.ajax(url.build('bexs/payment/create'), {
                    type: 'GET',
                    beforeSend: function() {

                    },
                    success: function (data, status, xhr){
                        if(data.status === true){
                            window.location.replace(url.build('bexs/payment/redirect'));
                        }else {
                            window.location.replace(url.build('bexs/payment/cancel'));
                        }
                    },
                    error: function (jqXhr, textStatus, errorMessage) {

                    }
                });
                //window.location.replace(url.build('bexs/payment/redirect'));
            },

            getZainLogo: function () {
                return window.checkoutConfig.payment.bexs.bexs_logo;
            },

            getTitle: function () {
                return $.mage.__('Pay with Bexs');
            }

        });
    }
);
