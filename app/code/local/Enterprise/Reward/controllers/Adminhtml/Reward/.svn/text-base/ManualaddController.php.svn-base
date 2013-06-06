<?php

/*
 * To change this template, choose Tools | Templates
 * and open the template in the editor.
 */

class Enterprise_Reward_Adminhtml_Reward_ManualaddController extends Mage_Adminhtml_Controller_Action
{
   const admin_namespace = 'mmanager';
   const KIEV = 1;
    
   public function indexAction()
   {
       $this->loadLayout();
       $this->renderLayout();
   }
     
   public function saveAction()
   {
      $data = $this->getRequest()->getParams();
      
      $allEmails = explode(",", trim($data['emails']));
      
        foreach($allEmails as $email)
        {
          try {
                $userResource = Mage::getResourceModel('customer/customer');
                $email = trim($email); 

                if(empty($email) OR filter_var($email, FILTER_VALIDATE_EMAIL) == false) continue;

                if(Mage::helper('customer/register')->checkIsSetCustomer($email) == true) 
                {
                    $emailConf = Mage::helper('customer')->emailConformation($email);

                    $user = Mage::getModel('customer/customer')->setWebsiteId(1)->loadByEmail($email);
                    $userId = $user->getId();
                    

                    if (empty($emailConf))
                    {
                        $body  = Mage::getModel('core/email_template')
                                ->loadDefault('mass_reg_email_template')
                                ->getProcessedTemplate(array('url' => Mage::getModel('core/url')->setType('web')->getUrl(), 
                                                            'cust_body' => $data['letter2']));
                    }
                    else
                    {
                        $password = substr(md5(microtime()), 0, 6);

//                        $user->setConfirmation();
//                        $user->setPassword($password);
//                        $user->save();
                        
                        $userResource->saveAttribute($user->setConfirmation(), 'confirmation');
                        $userResource->saveAttribute($user->setPassword($password), 'password_hash');

                        $body  = Mage::getModel('core/email_template')
                                ->loadDefault('mass_unreg_email_template')
                                ->getProcessedTemplate(array('url' => Mage::getModel('core/url')->setType('web')->getUrl(), 
                                                            'cust_body' => $data['letter1'], 'login' => $email, 'password' => $password));
                    }
                }
                else
                {
                    $password = substr(md5(microtime()), 0, 6);
                    $lastInsertId = Mage::helper('customer/register')->addNewCustomer($email, $password, self::KIEV);
                    $userId = $lastInsertId;

                    if(!empty($lastInsertId))
                    {
                        $user = Mage::getModel('customer/customer')->setWebsiteId(1)->load($lastInsertId);
//                        $user->setConfirmation();
//                        $user->save();
                        
                        $userResource->saveAttribute($user->setConfirmation(), 'confirmation');

                        $body  = Mage::getModel('core/email_template')
                                ->loadDefault('mass_unreg_email_template')
                                ->getProcessedTemplate(array('url' => Mage::getModel('core/url')->setType('web')->getUrl(), 
                                                            'cust_body' => $data['letter1'], 'login' => $email, 'password' => $password));

                        Mage::helper('customer/register')->addAffiliateToCustomer($userId, $data['affiliate']);
                    }
                }

                Mage::helper('landing/data')->sendLetter($email, $body, 'html', 'Начисление бонусов на SuperDeal.ua');

                $reward = Mage::getModel('enterprise_reward/reward');
                $reward ->setCustomerId($userId)
                        ->setWebsiteId('1')
                        ->setPointsDelta($data['bonus'])
                        ->setAction(Enterprise_Reward_Model_Reward::REWARD_ACTION_ADMIN)
                        ->setComment($data['comment'])
                        ->updateRewardPoints();

                $this->_redirect(self::admin_namespace . DS .'reward_manualadd' . DS . 'index' . DS . 'status' . DS . 'ok');

            } catch (Exception $e){
                Mage::logException($e);
                $this->addLog($e, $email);
            }
        }
      
   }
   
   public function addLog($exception, $email)
   {
       $data = date('d.m.Y H:i:s') . " Email: " . $email . "\n Ошибка: " . $exception->getMessage() . "\n" . " CallStack: \n" . $exception->getTraceAsString() . "\n";
       file_put_contents('var/log/bonuses.log', $data, FILE_APPEND);
   }
}