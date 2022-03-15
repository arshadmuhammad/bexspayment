<?php
/**
 * Created by PhpStorm.
 * User: amuh
 * Date: 4/17/2020
 * Time: 2:45 PM
 */

namespace MagArs\Bexs\Logger;

use Monolog\Logger;

class Handler extends \Magento\Framework\Logger\Handler\Base {

    protected $loggerType = Logger::INFO;

    protected $fileName = '/var/log/bexs/api.log';

}
