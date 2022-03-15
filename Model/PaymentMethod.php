<?php


namespace MagArs\Bexs\Model;


class PaymentMethod extends \Magento\Payment\Model\Method\AbstractMethod {

    /**
     * Payment code
     *
     * @var string
     */
    protected $_code = 'bexspayment';

    /**
     * @var bool
     */
    protected $_canAuthorize = false;

    /**
     * @var bool
     */
    protected $_canCapture = true;

    protected $_canRefund = false;

    protected $_canRefundInvoicePartial = true;

    /**
     * @var bool
     */
    protected $_canUseInternal = false;

    /**
     * @var bool
     */
    protected $_isInitializeNeeded = true;


}
