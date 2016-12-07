<?php
/**
 * Created by PhpStorm.
 * User: niels
 * Date: 2/12/16
 * Time: 13:37
 */
//  Synadd form
function synAdd (&$vars) {
    $settings = elgg_view('input/text', array(
        'name' => 'params[synAdd]',
        'value' => $vars['entity']->synAdd == '' ? '' : $vars['entity']->synAdd,
        'class' => 'elgg-input-thin',
        'placeholder' => $vars['entity']->synAdd ? $vars['entity']->synAdd : elgg_echo('options:synAdd:placeholder'),
    ));
    $settings .= elgg_view('input/button', array(
        'value' => elgg_echo('options:buttons:synAdd'),
        'id' => 'syn-add'
    ));
    $settings .= elgg_view('input/button', array(
        'value' => elgg_echo('options:buttons:back'),
        'class' => 'syn-back'
    ));
    return $settings;
}
//  Synedd form
function synEdd (&$vars) {
    $settings = elgg_view('input/text', array(
        'name' => 'params[synEdit]',
        'value' => $vars['entity']->synEdit == '' ? 'display to edit value here' : $vars['entity']->synEdit,
        'class' => 'elgg-input-thin',
        'placeholder' => $vars['entity']->synEdit ? $vars['entity']->synEdit : elgg_echo('options:synEdd:placeholder'),
    ));
    $settings .= elgg_view('input/button', array(
        'value' => elgg_echo('options:buttons:synEdd'),
        'id' => 'syn-edit'
    ));
    $settings .= elgg_view('input/button', array(
        'value' => elgg_echo('options:buttons:back'),
        'class' => 'syn-back'
    ));
    return $settings;
}

//  Stpadd form
function stpAdd (&$vars) {
    $settings = elgg_view('input/text', array(
        'name' => 'params[stpAdd]',
        'value' => $vars['entity']->stpAdd == '' ? '' : $vars['entity']->stpAdd,
        'class' => 'elgg-input-thin',
        'placeholder' => $vars['entity']->stpAdd ? $vars['entity']->stpAdd : elgg_echo('options:stpAdd:placeholder'),
    ));
    $settings .= elgg_view('input/button', array(
        'value' => elgg_echo('options:buttons:stpAdd'),
        'id' => 'stp-add'
    ));
    $settings .= elgg_view('input/button', array(
        'value' => elgg_echo('options:buttons:back'),
        'class' => 'stp-back'
    ));
    return $settings;
}

//  Stpedd form
function stpEdd (&$vars) {
    $settings = elgg_view('input/text', array(
        'name' => 'params[stpEdit]',
        'value' => $vars['entity']->stpEdit == '' ? 'display to edit value here' : $vars['entity']->stpEdit,
        'class' => 'elgg-input-thin',
        'placeholder' => $vars['entity']->stpEdit ? $vars['entity']->stpEdit : elgg_echo('options:stpEdd:placeholder'),
    ));
    $settings .= elgg_view('input/button', array(
        'value' => elgg_echo('options:buttons:stpEdd'),
        'id' => 'stp-edit'
    ));
    $settings .= elgg_view('input/button', array(
        'value' => elgg_echo('options:buttons:back'),
        'class' => 'stp-back'
    ));
    return $settings;
}



echo $settings;