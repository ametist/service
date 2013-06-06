<?php

$installer = $this;

$installer->run("
    ALTER TABLE {$this->getTable('affiliate')} ADD `email` varchar(255) NOT NULL default '';
");

$installer->endSetup();