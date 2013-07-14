<?php
$installer = $this;
 
$installer->startSetup();
$installer->run(" 
    DROP TABLE IF EXISTS `mycash_mailersoft`;
    CREATE TABLE IF NOT EXISTS `mycash_mailersoft` (
        `entity_id` int(11) NOT NULL AUTO_INCREMENT,
        `product_id` int(11) NOT NULL,
        `product_position` int(11) NOT NULL,
        `product_city` int(11) NOT NULL,
        PRIMARY KEY (`entity_id`)
        ) ENGINE=MyISAM DEFAULT CHARSET=utf8 AUTO_INCREMENT=1 ;
    ");
 
$installer->endSetup();