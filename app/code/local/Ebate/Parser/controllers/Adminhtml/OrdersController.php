<?php

class Ebate_Parser_Adminhtml_OrdersController extends Mage_Adminhtml_Controller_Action {
    
    const FILENAME = 'groupon.csv';
    const SALT = 'flowers';
   
    protected $dir;

    public function indexAction() { 

	// oid;cashback_sum;
        $this->_dir = Mage::getBaseDir('var') . DS . 'parser/orders';

        $row = 1;
        if (($handle = fopen($this->_dir . DS . self::FILENAME, "r")) !== FALSE) {
          while (($data = fgetcsv($handle, 100000, ",")) !== FALSE) {
            $num = count($data);
            $row++;
            for ($c=0; $c < $num; $c++) {
                $res = explode(";", $data[$c]);
                $order_id = (int) Mage::helper('core')->decrypt(array_shift($res));
                $cashback = array_pop($res);
                // get order data 

                // add cashback to rewardpoints
                if ($order_id > 0) {
                  $order = Mage::getModel('sales/order')->load($order_id);
                  if ($order->getStatus() != "complete") {
                     Mage::getModel('rewardpay/rewardpay')->addCustomerBonuses($order, $cashback * 0.5);

                     $order->setData('state', "complete");
                     $order->setStatus("complete");
                     $history = $order->addStatusHistoryComment('Order was set to Complete by our automation tool.', false);
                     $history->setIsCustomerNotified(false);
                     $order->save();
                  }
                }
            }
          }
          fclose($handle);
        }
    }
}