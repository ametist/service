<?php

class Ebate_Mailersoft_IndexController extends Mage_Core_Controller_Front_Action
{
    const DELIMETER = ';'; 
    
    public function indexAction()
    {
        $this->loadLayout();
        $this->renderLayout();
    }
    
    /**
     * get all reports  
     */
    public function reportsAction()
    {
        $model = Mage::getModel('mailer/mailersoft');
        $reports = $model->getReports();
        $content = 'mail_id;template_id;name;date;url;total;opened;opened_all;clicked;unsubscribed;declined;declined_soft;fb_like;' . PHP_EOL;
        foreach($reports['reports'] as $report) {
           
           $content .= $report['mail_id'] . self::DELIMETER . $report['name'] . self::DELIMETER . $report['date'] . 
                       self::DELIMETER . $report['url'] . self::DELIMETER . $report['total'] . self::DELIMETER . 
                       $report['opened'] . self::DELIMETER . $report['opened_all'] . self::DELIMETER . 
                       $report['clicked'] . self::DELIMETER . $report['unsubscribed'] . self::DELIMETER . 
                       $report['declined'] . self::DELIMETER . $report['declined_soft'] . self::DELIMETER . 
                       $report['fb_like'] . self::DELIMETER . PHP_EOL; 
        }
        
        Mage::helper('mailer')->_prepareDownloadResponse('mailersoft_report.csv', $content);
    }
    
    /**
     * get select report  
     */
    public function reportAction()
    {
       $mailId = $this->_request->getParam('mailid');       

        if (!empty($mailId)) {
            $model = Mage::getModel('mailer/mailersoft');
            $reportData = $model->getReport($mailId);
            
            $content = "id;link;uniq_clicks;clicks;" . PHP_EOL;
            foreach ($reportData['report']['Links'] as $report) {
                $content .= $report['id'] . self::DELIMETER . $report['link'] . self::DELIMETER . $report['uniq_clicks'] . self::DELIMETER . $report['clicks'] . self::DELIMETER . PHP_EOL;
            }

            Mage::helper('mailer')->_prepareDownloadResponse('mail_report.csv', $content);
        }
    }
    
}