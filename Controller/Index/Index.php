<?php


namespace MagArs\Bexs\Controller\Index;



use Magento\Framework\App\Action\Action;
use MagArs\Bexs\Helper\Data;
use Magento\Framework\App\Action\Context;

class Index extends Action{

    protected $data;

    public function __construct(
        Context $context,
        Data $data
    ) {
        $this->data = $data;
        parent::__construct($context);
    }

    public function execute() {
        echo "<pre>";
        print_r($this->data->createPayment(2,'',''));
    }
}
