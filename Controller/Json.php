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
        if(!$this->isWorkDay())
        {
            foreach($productIds as $returnProductId => $qty)
            {
                    $returnArray[$returnProductId] = 0;
            }
            return $returnArray;
        }
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
        $returnArray = array();
        foreach($result->StockAvailability as $stock)
        {
            $returnArray[$stock->ProductId] = ($stock->QuantityAvailable24hrs > $productIds[$stock->ProductId]) ? 1 : 0;
        }
        foreach($productIds as $returnProductId => $qty)
        {
            if(!key_exists($returnProductId, $returnArray))
            {
                $returnArray[$returnProductId] = 0;
            }
        }
        return $returnArray;
    }

    public function isWorkDay()
    {
        if(date('N') >= 6)
            return false;

        $date = date('d-m-Y');
        foreach($this->getHolidays() as $holiday)
        {
            if($date == $holiday)
            {
                return false;
            }
        }
        return true;

    }

    public function getHolidays()
    {
        $holidays = array();
        for ($i=0;$i<10;$i++) {
            $holidays[] = $this->_holidays(date('Y'),$i);
        }
        return $holidays;
    }

    private function _holidays($jaar,$feestdag){
        $a = $jaar % 19;
        $b = intval($jaar/100);
        $c = $jaar % 100;
        $d = intval($b/4);
        $e = $b % 4;
        $g = intval((8 *  $b + 13) / 25);
        $theta = intval((11 * ($b - $d - $g) - 4) / 30);
        $phi = intval((7 * $a + $theta + 6) / 11);
        $psi = (19 * $a + ($b - $d - $g) + 15 -$phi) % 29;
        $i = intval($c / 4);
        $k = $c % 4;
        $lamda = ((32 + 2 * $e) + 2 * $i - $k - $psi) % 7;
        $maand = intval((90 + ($psi + $lamda)) / 25);
        $dag = (19 + ($psi + $lamda) + $maand) % 32;
        Switch( $feestdag ){
            Case 0: $datumfeestdag = mktime (1,1,1,1,1,$jaar); break;            // Nieuwjaarsdag
            Case 1: $datumfeestdag = mktime (0,0,0,$maand,$dag-2,$jaar); break;  // Goede Vrijdag
            Case 2: $datumfeestdag = mktime (0,0,0,$maand,$dag,$jaar); break;    // 1e Paasdag
            Case 3: $datumfeestdag = mktime (0,0,0,$maand,$dag+1,$jaar); break;  // 2e Paasdag
            Case 4: $datumfeestdag = mktime (0,0,0,4,27,$jaar); break;           // Koningsdag
            Case 5: $datumfeestdag = mktime (0,0,0,$maand,$dag+39,$jaar); break; // Hemelvaart
            Case 6: $datumfeestdag = mktime (0,0,0,$maand,$dag+49,$jaar); break; // 1e Pinksterdag
            Case 7: $datumfeestdag = mktime (0,0,0,$maand,$dag+50,$jaar); break; // 2e Pinksterdag
            Case 8: $datumfeestdag = mktime (0,0,0,12,25,$jaar); break;          // 1e Kerstdag
            Case 9: $datumfeestdag = mktime (0,0,0,12,26,$jaar); break;          // 2e Kerstdag
        }
        Return Date("d-m-Y",$datumfeestdag);
    }
}
