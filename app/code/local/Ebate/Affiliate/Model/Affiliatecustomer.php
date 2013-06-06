<?php
     
    class Ebate_Affiliate_Model_Affiliatecustomer extends Mage_Core_Model_Abstract
    {
        public function _construct()
        {
            parent::_construct();
            $this->_init('affiliate/affiliatecustomer');
        }
        
        public function loadByAffiliateId($affiliateId)
        {
            $this->_getResource()->load($this, $affiliateId, 'affiliate_id');
            return $this;
        }
    }