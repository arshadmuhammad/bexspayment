define(
    [
        'uiComponent',
        'Magento_Checkout/js/model/payment/renderer-list'
    ],
    function (
        Component,
        rendererList
    ) {
        'use strict';
        rendererList.push(
            {
                type: 'bexspayment',
                component: 'MagArs_Bexs/js/view/payment/method-renderer/bexspayment'
            }
        );
        return Component.extend({});
    }
);
