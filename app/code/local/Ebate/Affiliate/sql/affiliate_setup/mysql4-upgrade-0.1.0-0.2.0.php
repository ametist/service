<?php

$installer = $this;

$installer->run("
    ALTER TABLE {$this->getTable('affiliate')} ADD `partner_account` int(10) unsigned NOT NULL COMMENT 'Reference with admin_user.user_id';
");

$installer->endSetup();