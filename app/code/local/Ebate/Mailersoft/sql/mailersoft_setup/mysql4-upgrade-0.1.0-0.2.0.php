<?php
$installer = $this;
 
$installer->startSetup();
$installer->run("ALTER TABLE `mycash_mailersoft` ADD `product_title` VARCHAR( 255 ) NULL");
 
$installer->endSetup();