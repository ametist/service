<?php
     
    class Ebate_Affiliate_Model_Affiliate extends Mage_Core_Model_Abstract
    {
        public function _construct()
        {
            parent::_construct();
            $this->_init('affiliate/affiliate');
        }
    }