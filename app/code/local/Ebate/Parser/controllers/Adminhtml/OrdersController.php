<?php

class Ebate_Parser_Adminhtml_OrdersController extends Mage_Adminhtml_Controller_Action {
    
    const FILENAME = 'groupon.csv';
   
    protected $dir;

    public function indexAction() { 

        # test
        $order = Mage::getModel('sales/order')->load(41);
        Mage::getModel('rewardpay/rewardpay')->addCustomerBonuses($order, 10); die();
        # test

	// oid;cashback_sum;
        $this->_dir = Mage::getBaseDir('var') . DS . 'parser/orders';

        $row = 1;
        if (($handle = fopen($this->_dir . DS . self::FILENAME, "r")) !== FALSE) {
          while (($data = fgetcsv($handle, 100000, ",")) !== FALSE) {
            $num = count($data);
            $row++;
            for ($c=0; $c < $num; $c++) {
                echo $data[$c] . "<br />\n";

                // get order data 

                // add cashback to rewardpoints
                #Mage::getModel('rewardpay/rewardpay')->addCustomerBonuses(41, 10);
            }
          }
          fclose($handle);
        }
    }

    public function getOrderData() {}


    
}