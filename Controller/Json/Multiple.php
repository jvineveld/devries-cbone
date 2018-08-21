<?php
/**
 * Copyright © 2013-2017 Magento, Inc. All rights reserved.
 * See COPYING.txt for license details.
 */
namespace Xlii\CbOne\Controller\Json;


/**
 * Contact index controller
 */
class Multiple extends \Xlii\CbOne\Controller\Json
{

    public function execute()
    {
        $data = $this->getRequest()->getParam('data');
        $data = array("9789400406254" => 8045, "9789044352894" => 80, "9789041422903" => 8045);
        echo json_encode($this->getAvailability($data));
        die();;
    }
}
