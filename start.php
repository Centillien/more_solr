<?php

/*
    Plugin Name: More solr
    Description: Adds advanced options to elgg solr
    Version: 1.0
    Author: Niels van den Boogaart
    Author Email: n.vdboogaart@outlook.com
*/

$setting = elgg_get_plugin_setting('admin_only', 'more_solr');
if($setting != 'no'){
    if(elgg_is_admin_logged_in()){
        elgg_register_event_handler('init', 'system', 'more_solr_init');
    }
} else {
    elgg_register_event_handler('init', 'system', 'more_solr_init');
}

/**
 *  Init more_solr plugin
 */
function more_solr_init()
{
    elgg_extend_view('css/admin', 'css/admin/more_solr_admin.css');
    elgg_extend_view('css/elgg', 'css/admin/more_solr_admin.css');

    $action_url = elgg_get_plugins_path() . "more_solr/actions/";
    elgg_register_action("word_handler", "{$action_url}word_handler.php");

    elgg_register_action("options", "{$action_url}options.php");

    elgg_register_action('more_solr/settings/save', dirname(__FILE__) . '/actions/plugin_settings.php', 'admin');

    elgg_register_page_handler('more_solr', 'more_page_handler');

    elgg_register_js('jsStyle', elgg_get_simplecache_url('css/admin/more_solr_style.js'));
    elgg_register_js('admin_settings', elgg_get_simplecache_url('/admin_settings.js'));
}

function more_page_handler($page) {

    $pages = dirname(__FILE__) . '/pages/more_solr';

    if (!isset($page[0])) {
        $page[0] = 'all';
    }

    $page_type = $page[0];
    switch ($page_type) {
        default:
                include "$pages/layouts/list_solr.php";
            break;
    }
    return true;

}