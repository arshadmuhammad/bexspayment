<?php


namespace MagArs\Bexs\Controller\Payment;


use Magento\Customer\Api\AccountManagementInterface;
use Magento\Customer\Api\CustomerRepositoryInterface;
use Magento\Framework\App\Action\HttpGetActionInterface as HttpGetActionInterface;
use Magento\Framework\Controller\ResultFactory;
use Magento\Sales\Api\OrderRepositoryInterface;

class Create extends \Magento\Checkout\Controller\Onepage implements HttpGetActionInterface {

    private \MagArs\Bexs\Helper\Data $data;

    private \Magento\Sales\Model\OrderFactory $orderFactory;

    private OrderRepositoryInterface $orderRepository;

    public function __construct(
        \Magento\Framework\App\Action\Context $context,
        \Magento\Customer\Model\Session $customerSession,
        CustomerRepositoryInterface $customerRepository,
        AccountManagementInterface $accountManagement,
        \Magento\Framework\Registry $coreRegistry,
        \Magento\Framework\Translate\InlineInterface $translateInline,
        \Magento\Framework\Data\Form\FormKey\Validator $formKeyValidator,
        \Magento\Framework\App\Config\ScopeConfigInterface $scopeConfig,
        \Magento\Framework\View\LayoutFactory $layoutFactory,
        \Magento\Quote\Api\CartRepositoryInterface $quoteRepository,
        \Magento\Framework\View\Result\PageFactory $resultPageFactory,
        \Magento\Framework\View\Result\LayoutFactory $resultLayoutFactory,
        \Magento\Framework\Controller\Result\RawFactory $resultRawFactory,
        \Magento\Framework\Controller\Result\JsonFactory $resultJsonFactory,
        \MagArs\Bexs\Helper\Data $data,
        \Magento\Sales\Model\OrderFactory $orderFactory,
        OrderRepositoryInterface $orderRepository
    ) {
        $this->data = $data;
        $this->orderFactory = $orderFactory;
        $this->orderRepository = $orderRepository;
        parent::__construct($context, $customerSession, $customerRepository, $accountManagement, $coreRegistry, $translateInline, $formKeyValidator, $scopeConfig, $layoutFactory, $quoteRepository, $resultPageFactory, $resultLayoutFactory, $resultRawFactory, $resultJsonFactory);
    }

    public function execute() {
        $session = $this->getOnepage()->getCheckout();
        if(!$session->hasQuote()){
            return $this->_redirect('checkout/cart');
        }
        $national_id  = '00015262197';
        $order = $this->orderFactory->create()->loadByIncrementId($session->getLastRealOrder()->getIncrementId());
        $orderTotal = $order->getBaseGrandTotal();
        $description = "Order ID: " . $order->getIncrementId();
        $consumer = [
            "address" => [
                "city" => $order->getBillingAddress()->getCity(),
                //"country" => $order->getBillingAddress()->getCountryId(),
                "country" => "BRA",
                "full_street_address" => implode(', ', $order->getBillingAddress()->getStreet()),
                "zip_code" => "00000000"
            ],
            "email" => $order->getCustomerEmail(),
            "full_name" => $order->getCustomerFirstname(). " " . $order->getCustomerLastname(),
            "external_id" => "Order ID: " . $order->getIncrementId(),
            "national_id" => $national_id

        ];
        $billing = [
            "national_id" => $national_id,
            "name" => $order->getCustomerFirstname(). " " . $order->getCustomerLastname()
        ];
//        $cartData = [];
//        /** @var \Magento\Sales\Api\Data\OrderItemInterface $item */
//        foreach ($order->getAllVisibleItems() as $item){
            $cartData[] = [
                "description" => "Order ID: " . $order->getIncrementId(),
                "quantity" => 1,
                "unit_price" => (float) $order->getBaseGrandTotal()
            ];
//        }
        $resultData = $this->data->createPayment($orderTotal, $description, $consumer, $billing, $cartData);

        $responseData = [
            'status' => true
        ];
        if(
            $resultData['status'] === "WAITING_CONSUMER"
            && $resultData["events"][0]["status"] === "SUCCESS"
            && $resultData["events"][0]["type"] === "CHECKOUT_CREATION"
        ) {
            $order->setBexsId($resultData['id']);
            $order->setBexsRedirectUrlToken($resultData['redirect_url']);
            $this->orderRepository->save($order);

        }  else{
            $responseData['status'] = false;
        }
        $result = $this->resultFactory->create(ResultFactory::TYPE_JSON);
        return $result->setData($responseData);
    }
}
