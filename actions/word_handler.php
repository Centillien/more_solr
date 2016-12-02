<?php
/**
 * Created by PhpStorm.
 * User: niels
 * Date: 2/12/16
 * Time: 9:59
 */
init();

function init () {
    $method = get_input('method');
    $type = get_input('type');
    switch($method){
        case 'delete':
            deleteWord($type);
            break;
        case 'edit':
            editWord($type);
            break;
        case 'add':
            addWord($type);
            break;
    }
}

function addWord ($type) {
    print_r($type);
    exit();
}

function editWord ($type) {
    print_r($type);
    exit();
}

function deleteWord ($type) {
    print_r($type);
    exit();
}