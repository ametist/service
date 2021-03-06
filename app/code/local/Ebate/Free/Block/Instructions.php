<?php

class Ebate_Free_Block_Instructions extends Mage_Core_Block_Template
{
    public function getInvoiceId()
    {
        $reorderId = Mage::helper('free')->getReorderId();

        if (!empty($reorderId)) {
            return Mage::getSingleton('core/session')->getInvoiceId() ? Mage::getSingleton('core/session')->getInvoiceId() : $reorderId;
        } else {
            return Mage::getSingleton('core/session')->getInvoiceId() ? Mage::getSingleton('core/session')->getInvoiceId() : Mage::getSingleton('checkout/session')->getLastOrderId();
        }
    }
}