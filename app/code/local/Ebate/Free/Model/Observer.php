<?php

class Ebate_Free_Model_Observer 
{    
    public function processOrderPlace(Varien_Event_Observer $observer)
    {
        if ('sdfree' != $observer->getEvent()->getOrder()->getPayment()->getMethodInstance()->getCode()) {
            return;
        }

        $order = $observer->getEvent()->getOrder();
        $invoice = $order->prepareInvoice();
        $invoice->register()->pay();
        Mage::getModel('core/resource_transaction')
            ->addObject($invoice)
            ->addObject($invoice->getOrder())
            ->save();                        
    }
    
}

?>
