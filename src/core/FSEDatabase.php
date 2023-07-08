<?php

namespace FseThemeSyncher\core;

use FseThemeSyncher\core\WordPressDatabaseUtiliyClass;

defined('WPINC') or die();

/**
 * In this class we will define the table name we will be using
 * throughout the plugin for the various use cases.
 */
class FSEDatabase extends WordPressDatabaseUtiliyClass
{
    const FSE_THEME_SYNC_TABLE_NAME = 'theme_sync';

    public function __construct()
    {
        global $wpdb;
        $this->tableName = $wpdb->prefix . self::FSE_THEME_SYNC_TABLE_NAME;
    }
}