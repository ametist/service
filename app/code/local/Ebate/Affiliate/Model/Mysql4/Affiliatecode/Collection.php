<?php
     
    class Ebate_Affiliate_Model_Mysql4_Affiliatecode_Collection extends Mage_Core_Model_Mysql4_Collection_Abstract
    {
        public function _construct()
        {
            //parent::__construct();
            $this->_init('affiliate/affiliatecode');
        }
    }