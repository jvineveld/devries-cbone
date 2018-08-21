<?php
/**
 * Copyright © 2013-2017 Magento, Inc. All rights reserved.
 * See COPYING.txt for license details.
 */
namespace Xlii\CbOne\Controller;


/**
 * Contact index controller
 */
abstract class Json extends \Magento\Framework\App\Action\Action
{

    public function getAvailability($productIds)
    {
        $body = array();
        $body['Product'] = array();
        foreach($productIds as $productId => $qty)
        {
            $product = ['ProductId' => $productId, "ProductIdType" => "EAN"];
            array_push($body['Product'], $product);
        }
        $body = json_encode($body);
        $ch = curl_init();

        curl_setopt($ch, CURLOPT_URL,            "https://services.cb.nl/rest/api/v1/PhysicalProductStockService/getStockAvailability" );
        curl_setopt($ch, CURLOPT_RETURNTRANSFER, 1 );
        curl_setopt($ch, CURLOPT_POST,           1 );
        curl_setopt($ch, CURLOPT_POSTFIELDS,     $body );
        curl_setopt($ch, CURLOPT_HTTPHEADER,     array('Content-Type: application/json'));

        $result=json_decode(curl_exec ($ch));
        foreach($result->StockAvailability as $stock)
        {

            $returnArray[$stock->ProductId] = ($stock->QuantityAvailable24hrs > $productIds[$stock->ProductId]) ? 1 : 0;
        }
        return $returnArray;
    }
}
