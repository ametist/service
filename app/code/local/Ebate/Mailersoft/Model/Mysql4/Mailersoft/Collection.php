<?php
     
    class Ebate_Mailersoft_Model_Mysql4_Mailersoft_Collection extends Mage_Core_Model_Mysql4_Collection_Abstract
    {
        public function _construct()
        {
            //parent::__construct();
            $this->_init('mailer/mailersoft');
        }
    }