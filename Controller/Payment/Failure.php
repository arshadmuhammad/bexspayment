<?php


namespace MagArs\Bexs\Controller\Payment;


use Magento\Framework\App\Action\Action;
use Magento\Framework\App\Action\Context;
use Magento\Framework\App\ResponseInterface;
use Magento\Checkout\Model\Session;
use Magento\Framework\Controller\ResultFactory;
use Magento\Sales\Api\OrderRepositoryInterface;

class Failure extends Action {

    /**
     * @var Session
     */
    private $_checkoutSession;

    /**
     * @var OrderRepositoryInterface
     */
    private $orderRepository;


    public function __construct(
        Context $context,
        Session $_checkoutSession,
        OrderRepositoryInterface $orderRepository

    ) {
        $this->_checkoutSession = $_checkoutSession;
        $this->orderRepository = $orderRepository;
        parent::__construct($context);
    }

    public function execute() {
        $session = $this->_checkoutSession;
        $orderId = $session->getLastOrderId();
        $session->clearQuote();
        $order = $this->orderRepository->get($orderId);
        if($order->getStatus() == "pending" && $order->getPayment()->getMethod() == 'bexspayment') {
            $order->addStatusToHistory($order::STATE_CANCELED, 'Payment failed', true);
            $this->orderRepository->save($order);
        }
        return $this->resultFactory->create(ResultFactory::TYPE_PAGE);

    }

}
