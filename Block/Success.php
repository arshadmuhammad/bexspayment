<?php


namespace MagArs\Bexs\Block;


use Magento\Framework\View\Element\Template;
use MagArs\Paysafe\Helper\Config;
use MagArs\Paysafe\Logger\Logger;
use MagArs\Paysafe\Model\PaysafeLogger;
use MagArs\Paysafe\Model\PaysafePayment;
use Magento\Sales\Api\OrderRepositoryInterface;

class Success extends Template {

    /**
     * @var Logger
     */
    protected $logger;

    /**
     * @var \Magento\Sales\Model\Order\Email\Sender\OrderSender
     */
    private $orderSender;

    /**
     * @var Config
     */
    private $configHelper;

    /**
     * @var \Magento\Sales\Model\ResourceModel\Order\CollectionFactory
     */
    private $orderCollectionFactory;

    /**
     * @var OrderRepositoryInterface
     */
    private $orderRepository;

    public function __construct(
        Logger $logger,
        Config $configHelper,
        \Magento\Sales\Model\Order\Email\Sender\OrderSender $orderSender,
        \Magento\Sales\Model\ResourceModel\Order\CollectionFactory $orderCollectionFactory,
        OrderRepositoryInterface $orderRepository,
        Template\Context $context,
        array $data = []
    ) {
        $this->logger = $logger;
        $this->orderCollectionFactory = $orderCollectionFactory;
        $this->orderRepository = $orderRepository;
        $this->orderSender = $orderSender;
        $this->configHelper = $configHelper;
        parent::__construct($context, $data);
    }

    public function getPaymentStatus() {

        $paymentId = $this->getRequest()->getParam('payment_id');
        $this->logger->info("Success URL With Payment ID: " . $paymentId);
        $config = $this->configHelper->getConfiguration();

        if ($config['logging']) {
            $logger = new PaysafeLogger();
        }
        $pscpayment = new PaysafePayment($config['psc_key'], $config['environment']);
        $retrieveResult = $pscpayment->retrievePayment($paymentId);

        if($retrieveResult['object'] === "PAYMENT"){
            $order = $this->orderCollectionFactory->create()->addFieldToFilter('bexs_id', $retrieveResult["id"])->getFirstItem();
            if($retrieveResult['status'] === "AUTHORIZED"){
                $this->logger->info('Order Status: '. $order->getStatus());
                $this->logger->info('Order Payment Method: '. $order->getPayment()->getMethod());
                if($order->getStatus() == "pending" && $order->getPayment()->getMethod() == 'paysafe') {
                    $order->addStatusToHistory($order::STATE_PAYMENT_REVIEW, 'Paysafe Payment AUTHORIZED', true);
                    $order->getPayment()->setAdditionalInformation(
                        [
                            "payment_id" => $retrieveResult['id'] ?? "",
                            "payment_type" => $retrieveResult['type'] ?? "",
                            "payment_instrument" => $retrieveResult['payment_instrument'] ?? "",
                            "payment_instrument_subtype" => $retrieveResult['payment_instrument_subtype'] ?? ""
                        ]
                    );
                    $this->orderRepository->save($order);
                    return $retrieveResult['status'];
                }
            }
            if($retrieveResult['status'] === "SUCCESS"){
                header("Location: " . $this->getBaseUrl(). 'checkout/onepage/success');
                exit;
            }
        }
    }

}
