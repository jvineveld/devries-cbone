<?php
/**
 * Copyright © 2013-2017 Magento, Inc. All rights reserved.
 * See COPYING.txt for license details.
 */
namespace Xlii\CbOne\Controller\Json;


/**
 * Contact index controller
 */
class Single extends \Xlii\CbOne\Controller\Json
{

    public function execute()
    {
        $sku = $this->getRequest()->getParam('sku');
        echo json_encode($this->getAvailability(array($sku => 1)));
        die();
    }
}
