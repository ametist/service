<?php

class Ebate_Free_Helper_Data extends Mage_Core_Helper_Abstract
{

    public function getReorderId()
    {
        $orderId = Mage::getSingleton('checkout/session')->getData('orderId');
        if (!empty($orderId)) {
            return $orderId;
        }
    }
    
}