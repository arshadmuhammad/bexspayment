<?php


namespace MagArs\Bexs\Block;


use Magento\Checkout\Model\Session;
use Magento\Framework\View\Element\Template;

class Redirect extends Template {

    protected Session $_checkoutSession;

    public function __construct(
        Session $_checkoutSession,
        Template\Context $context,
        array $data = []
    ) {
        $this->_checkoutSession = $_checkoutSession;
        parent::__construct($context, $data);
    }

    public function getIframeURL() {
        $order = $this->_checkoutSession->getLastRealOrder();
        return  $order->getBexsRedirectUrlToken();
    }

    public function getBexsId(){
        $order = $this->_checkoutSession->getLastRealOrder();
        return  $order->getBexsId();
    }

    public function getSuccessUrl($payment_id) {
        return $this->getUrl('bexs/payment/success', ['payment_id' => $payment_id]);
    }

    public function getFailureUrl($payment_id) {
        return $this->getUrl('bexs/payment/failure', ['payment_id' => $payment_id]);
    }

}
