<?php

/*
    Plugin Name: More solr
    Description: Adds advanced options to elgg solr
    Version: 1.0
    Author: Niels van den Boogaart
    Author Email: n.vdboogaart@outlook.com
*/

elgg_register_event_handler('init', 'system', 'advanced_search_init');

/**
 *  Init advanced_search plugin
 */
function advanced_search_init()
{
    elgg_extend_view('css/admin', 'css/admin/advanced_search_admin');
    elgg_extend_view('css/elgg', 'css/admin/advanced_search_admin');

    elgg_extend_view('css/elgg', 'jquery.dataTables.min.css');
    elgg_extend_view('js/elgg', 'jquery.dataTables.min.js');

    $action_url = elgg_get_plugins_path() . "advanced_search/actions/";
    elgg_register_action("word_handler", "{$action_url}word_handler.php");
    elgg_register_action("options", "{$action_url}options.php");
    elgg_register_action('advanced_search/settings/save', dirname(__FILE__) . '/actions/plugin_settings.php', 'admin');

    elgg_register_page_handler('advanced_search', 'more_page_handler');

    elgg_register_js('jsStyle', elgg_get_simplecache_url('css/admin/advanced_search_style.js'));
    elgg_register_js('admin_settings', elgg_get_simplecache_url('/admin_settings.js'));
}

function more_page_handler($page) {

    $pages = dirname(__FILE__) . '/pages/advanced_search';

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