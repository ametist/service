<?php
     
    $installer = $this;
     
    $installer->startSetup();
     
    $installer->run("
     
    -- DROP TABLE IF EXISTS {$this->getTable('affiliate')};
    CREATE TABLE {$this->getTable('affiliate')} (
      `affiliate_id` int(11) unsigned NOT NULL auto_increment,
      `title` varchar(255) NOT NULL default '',
      `content` text NOT NULL,
      `status` smallint(6) NOT NULL default '0',
      `created_time` datetime NULL,
      PRIMARY KEY (`affiliate_id`)
    ) ENGINE=InnoDB DEFAULT CHARSET=utf8;

    -- DROP TABLE IF EXISTS {$this->getTable('affiliatecode')};
    CREATE TABLE {$this->getTable('affiliatecode')} (
      `id` int(11) unsigned NOT NULL auto_increment,
      `affiliate_id` int(11) NOT NULL default '0',
      `code` varchar(12) NOT NULL default '',
      `description` text NOT NULL,
      `status` smallint(6) NOT NULL default '1',
      `created_time` datetime NULL,
      `end_time` datetime NULL,
      `amount_add` smallint(8) NOT NULL default '0',
      PRIMARY KEY (`id`)
    ) ENGINE=InnoDB DEFAULT CHARSET=utf8;
     
        ");
     
    $installer->endSetup();