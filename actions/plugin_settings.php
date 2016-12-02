<?php
/**
 * Saves global plugin settings.
 *
 * This action can be overriden for a specific plugin by creating the
 * <plugin_id>/settings/save action in that plugin.
 *
 * @uses array $_REQUEST['params']    A set of key/value pairs to save to the ElggPlugin entity
 * @uses int   $_REQUEST['plugin_id'] The ID of the plugin
 *
 * @package Elgg.Core
 * @subpackage Plugins.Settings
 */

$params = get_input('params');
$params['syn_file'] = $_FILES['synFile']['name'];
$params['stp_file'] = $_FILES['stpFile']['name'];

$fileSyn = new ElggFile();
$fileSyn->owner_guid = 7777;
$fileSyn->setFilename('settings/synonym/list.txt');
$fileSyn->open('write');
$contents = file_get_contents($_FILES['synFile']['tmp_name']);
if($contents != null){
    $fileStp->write($contents);
}
$fileSyn->close();

// to uprade this file to an entity
$fileSyn->subtype = 'file';
$fileSyn->save();

$fileStp = new ElggFile();
$fileStp->owner_guid = 7777;
$fileStp->setFilename('settings/stopword/list.txt');
$fileStp->open('write');
$contents = file_get_contents($_FILES['stpFile']['tmp_name']);
if($contents != ''){
    $fileStp->write($contents);
}
$fileStp->close();

// to uprade this file to an entity
$fileStp->subtype = 'file';
$fileStp->save();

$plugin_id = get_input('plugin_id');
$plugin = elgg_get_plugin_from_id($plugin_id);

if (!($plugin instanceof ElggPlugin)) {
    register_error(elgg_echo('plugins:settings:save:fail', array($plugin_id)));
    forward(REFERER);
}

$plugin_name = $plugin->getManifest()->getName();
$result = false;

// don't filter these values
$params['hl_prefix'] = get_input('hl_prefix', '', false);
$params['hl_suffix'] = get_input('hl_suffix', '', false);

print_r($params);
foreach ($params as $k => $v) {
    $v = is_array($v) ? implode(",", $v) : $v;
    $result = $plugin->setSetting($k, $v);
    if (!$result) {
        register_error(elgg_echo('plugins:settings:save:fail', array($plugin_name)));
        forward(REFERER);
        exit;
    }
}

system_message(elgg_echo('plugins:settings:save:ok', array($plugin_name)));
forward(REFERER);

