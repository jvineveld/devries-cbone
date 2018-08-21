<?php
/**
 * Copyright © 2013-2017 Magento, Inc. All rights reserved.
 * See COPYING.txt for license details.
 */
namespace Xlii\CbOne\Controller\Json;


/**
 * Contact index controller
 */
class Multiple extends \Magento\Framework\App\Action\Action
{

    public function execute()
    {
        $data = $this->getRequest()->getParam('data');
        $data = json_decode($data, true);
        $returnArray = array();
        foreach($data as $product)
        {
            $returnArray[$product] = rand(0, 1);
        }
        echo json_encode($returnArray);
        die();
    }
}
