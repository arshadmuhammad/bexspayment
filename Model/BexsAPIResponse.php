<?php


namespace MagArs\Bexs\Model;


use MagArs\Bexs\Api\BexsAPIResponseInterface;
use MagArs\Bexs\Logger\Logger;
use Magento\Framework\Webapi\Rest\Request;

class BexsAPIResponse implements BexsAPIResponseInterface {

    /** @var Logger $logger */
    protected Logger $logger;

    /**
     * @var Request
     */
    protected Request $request;

    public function __construct(
        Logger $logger,
        Request $request
    ) {
        $this->logger = $logger;
        $this->request = $request;
    }

    public function getPOSTResponse(): bool {
        $body = $this->request->getBodyParams();
        $this->logger->info("Boday Params ASSIGING CARDS:");
        $this->logger->info(print_r($body, 1));
        return true;
    }
}
