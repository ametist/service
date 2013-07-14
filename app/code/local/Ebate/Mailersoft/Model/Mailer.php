<?php
require_once('nusoap_client.php');

class Ebate_Mailersoft_Model_Mailer extends Mage_Core_Model_Abstract
{
    public function _construct()
    {
        parent::_construct();
    }

    const MAILER_SUCCESS = 0;

    const MAILER_INVALID_EMAIL = 1;
    const MAILER_INVALID_API_KEY = 100;
    const MAILER_INVALID_GROUP_ID = 101;

    const MAILER_IS_UNSUBSCRIBED = 201;
    const MAILER_IS_BOUNCED = 202;

    

}
