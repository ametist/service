<?php
     
    class Ebate_Mailersoft_Model_Mysql4_Mailersoft extends Mage_Core_Model_Mysql4_Abstract
    {
        public function _construct()
        {   
            $this->_init('mailer/mailersoft', 'entity_id');
        }
    }