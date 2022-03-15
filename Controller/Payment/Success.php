<?php


namespace MagArs\Bexs\Controller\Payment;

use Magento\Framework\App\Action\Action;
use Magento\Framework\App\Action\Context;
use Magento\Framework\Controller\ResultFactory;
use Magento\Sales\Model\ResourceModel\Order\CollectionFactory;


class Success extends Action {

    /**
     * @var CollectionFactory
     */
    private $orderCollectionFactory;

    public function __construct(
        CollectionFactory $orderCollectionFactory,
        Context $context
    ) {
        $this->orderCollectionFactory = $orderCollectionFactory;
        parent::__construct($context);
    }

    public function execute() {

        $paymentId = $this->getRequest()->getParam('payment_id');
        /** @var \Magento\Sales\Model\Order $order */
        $order = $this->orderCollectionFactory->create()->addFieldToFilter('bexs_id', $paymentId)->getFirstItem();

        if($order->hasInvoices() && $order->getPayment()->getMethod() == 'bexspayment') {
            return $this->_redirect('checkout/onepage/success');
        } else {
            if($order->getPayment()->getMethod() != 'bexspayment'){
                return $this->_redirect('customer/account');
            }
        }

        return $this->resultFactory->create(ResultFactory::TYPE_PAGE);
    }
}
