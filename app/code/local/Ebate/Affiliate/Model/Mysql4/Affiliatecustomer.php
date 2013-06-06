<?php
     
    class Ebate_Affiliate_Model_Mysql4_Affiliatecustomer extends Mage_Core_Model_Mysql4_Abstract
    {
        public function _construct()
        {   
            $this->_init('affiliate/affiliatecustomer', 'entity_id');
        }
    }