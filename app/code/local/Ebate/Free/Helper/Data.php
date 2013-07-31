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
    
    public function getProductByItems($items)
    {
        foreach ($items as $itemId => $item) {
            $product_id = $item->getProductId(); 
        }

        if ($product_id) {
            return Mage::getModel('catalog/product')->load($product_id)->getData('real_url');
        }
    }
}