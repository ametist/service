<?php
class Ebate_Mailersoft_Block_Adminhtml_Mailer extends Mage_Adminhtml_Block_Template
{
    public function __construct()
    {
        parent::__construct();
    }

    public function getGeneralConfig($param = '')
    {
        return Mage::getStoreConfig('mailer/mailer_general/' . $param);
    }

    public function getMailersoftGroups()
    {
        return Mage::getStoreConfig('mailer/mailer_groups');
    }

    public function getGroups()
    {
        return Mage::app()->getGroups();
    }
}