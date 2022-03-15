<?php


namespace MagArs\Bexs\Model;


use Magento\Checkout\Model\ConfigProviderInterface;
use Magento\Framework\View\Element\Template;

class BexsLogoConfig implements ConfigProviderInterface {

    /**
     * @var Template
     */
    private $templateBlock;

    public function __construct(
        Template $templateBlock
    ) {
        $this->templateBlock = $templateBlock;
    }

    /**
     * @return array
     */
    public function getConfig() {
        return [
            'payment' => [
                'bexs' => [
                    'bexs_logo' => $this->templateBlock->getViewFileUrl('MagArs_Bexs::images/bexs_logo.jpg')
                ]
            ]
        ];
    }
}
