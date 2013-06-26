<?php

class SuperDeal_Parser_Adminhtml_OrdersController extends Mage_Adminhtml_Controller_Action {
    
    const DIR = '';
    const FILENAME = '';
    
    public function indexAction() { 
	// oid;cashback_sum;

        $row = 1;
        if (($handle = fopen(self::DIR . DC . self::FILENAME, "r")) !== FALSE) {
          while (($data = fgetcsv($handle, 100000, ",")) !== FALSE) {
            $num = count($data);
            $row++;
            for ($c=0; $c < $num; $c++) {
                echo $data[$c] . "<br />\n";
            }
          }
          fclose($handle);
        }
    }
    
}