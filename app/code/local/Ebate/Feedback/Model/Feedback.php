<?php

class Ebate_Feedback_Model_Feedback extends Mage_Core_Model_Abstract
{
    //const SEND_MAIL = 'support@mycash.in.ua';
    const SEND_MAIL = 'bitrop@mail.ru';
    
    public function sendLetter($data)
    {
            $subject = array(1 => 'Проблема с авторизацией на сайте',
                             2 => 'Вопросы по условиям акции',
                             3 => 'Сложности с оформлением заказа',
                             4 => 'Когда будут перечислены деньги на мой аккаунт?',
                             5 => 'Как вывести деньги со своего счета?');
            
            $title = $subject[$data['typeQuery']];
            
            $body  = "Имя клиента: ".$data['name'].PHP_EOL.
                     "E-mail: ".$data['email']    .PHP_EOL.
                     $data['comment']             .PHP_EOL;
            
            Mage::helper('feedback/data')->sendLetter(self::SEND_MAIL, $body, 'text', $title, null, null, $data['email'], $data['name']);
            Mage::getSingleton('customer/session')->setData('feedbackMessage', true);        
    }

}