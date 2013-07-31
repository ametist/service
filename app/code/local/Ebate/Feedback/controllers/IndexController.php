<?php

class Ebate_Feedback_IndexController extends Mage_Core_Controller_Front_Action
{
    public function indexAction()
    {
        $data = $this->getRequest()->getParams();
        
        if(isset($data['sendMessage']))
        {
            if(isset($data['hsk']) AND $data['hsk'] == "a024cb70fb3d1ab1206ac6c85b65fe76" AND isset($data['baspm']) AND $data['baspm'] == "")
            {
                $request = $this->getRequest();
                Mage::getModel('feedback/feedback')->sendLetter($data);
            }
            $this->_redirect('about.html');
        }
        
    }
} 