<?php
     
    class Ebate_Affiliate_Model_Mysql4_Affiliate extends Mage_Core_Model_Mysql4_Abstract
    {
        public function _construct()
        {   
            $this->_init('affiliate/affiliate', 'affiliate_id');
        }
    }