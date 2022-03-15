<?php


namespace MagArs\Bexs\Observer;


use Magento\Framework\Event\ObserverInterface;

class BeforeOrderPlaceObserver implements ObserverInterface
{
    public function execute(\Magento\Framework\Event\Observer $observer)
    {
        $order = $observer->getOrder();
        $paymentMethod = $order->getPayment()->getMethod();
        if ($paymentMethod == "bexs") {
            $order->setCanSendNewEmailFlag(false);
        }

    }
}
