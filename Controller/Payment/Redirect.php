<?php


namespace MagArs\Bexs\Controller\Payment;


use Magento\Framework\App\Action\HttpGetActionInterface as HttpGetActionInterface;
use Magento\Framework\Controller\ResultFactory;

class Redirect extends \Magento\Checkout\Controller\Onepage implements HttpGetActionInterface {

    public function execute() {
        $session = $this->getOnepage()->getCheckout();
        if(!$session->hasQuote()){
            return $this->_redirect('checkout/cart');
        }
        return $this->resultFactory->create(ResultFactory::TYPE_PAGE);
    }
}
