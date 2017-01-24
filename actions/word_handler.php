<?php
/**
 * Created by PhpStorm.
 * User: niels
 * Date: 2/12/16
 * Time: 9:59
 */
$type = get_input('type');
$method = get_input('method');
$input = get_input('input');
$old = get_input('old');

init($type, $method, $input, $old);

function init ($type, $method, $input, $old) {
    switch($method){
        case 'del':
            deleteWord($type, $old);
            break;
        case 'edd':
            editWord($type, $input, $old);
            break;
        case 'add':
            addWord($type, $input);
            break;
    }
}

function addWord ($type, $input) {
    if($type == 'synonym'){
        $synonyms = getSyns();
        if(!in_array($input, $synonyms)){
            //  Remove empty values
            $input = array_filter($input, function($value) { return $value !== ''; });
            if(count($input) > 1){
                $input = implode(",", $input);
                array_push($synonyms, $input);
                system_message($input . elgg_echo('handler:newAdd'));
            } else {
                register_error(elgg_echo('handler:minAmWords'));
            }
        } else{
            register_error(elgg_echo('handler:duplicate'));
        }
        $synonyms = implode(PHP_EOL, $synonyms);
        setSyns($synonyms);
    } elseif ($type == 'stopword'){
        $stopwords = getStops();
        if(!in_array($input, $stopwords)){
            array_push($stopwords, $input);
            system_message($input . elgg_echo('handler:newAdd'));
        } else{
            register_error(elgg_echo('handler:duplicate'));
        }
        $stopwords = implode(PHP_EOL, $stopwords);
        setStops($stopwords);
    }
}

function editWord ($type, $input, $old) {
    if($type == 'synonym'){
        $synonyms = getSyns();
        //  Remove empty values
        $input = array_filter($input, function($value) { return $value !== ''; });
        if(count($input) > 1){
            $input = implode(",", $input);
            $old = implode(",", $old);
            $synonyms = implode(PHP_EOL, $synonyms);
            $synonyms = str_replace($old, "", $synonyms);
            $synonyms .= PHP_EOL.$input;
            system_message($old.elgg_echo('handler:successful:change').$input.elgg_echo('handler:refresh'));
        } else {
            register_error(elgg_echo('handler:minAmWords'));
        }
        setSyns($synonyms);
    } elseif ($type == 'stopword') {
        $arr = getStops();
        $arr = implode(PHP_EOL, $arr);
        $test = str_replace($old, $input, $arr);
        system_message($old.elgg_echo('handler:successful:change').$input.elgg_echo('handler:refresh'));
        setStops($test);
    }
    return $input;
}

function deleteWord ($type, $old) {
    if($type == 'synonym'){
        $arr = getSyns();
        $arr = implode(PHP_EOL, $arr);
        $test = str_replace("\n".$old."\n", "\n", $arr);
        $test = preg_replace("/(^[\r\n]*|[\r\n]+)[\s\t]*[\r\n]+/", "\n", $test);
        system_message($old.elgg_echo('handler:successful:remove'));
        setSyns($test);
    } elseif ($type == 'stopword') {
        $arr = getStops();
        $arr = implode(PHP_EOL, $arr);
        $test = str_replace("\n".$old."\n", "\n", $arr);
        $test = preg_replace("/(^[\r\n]*|[\r\n]+)[\s\t]*[\r\n]+/", "\n", $test);
        system_message($old.elgg_echo('handler:successful:remove'));
        setStops($test);
    }
    return $old;
}

function getStops(){
// arbitrary file on the filestore
    $fileStp = new ElggFile();
    $fileStp->owner_guid = 7777;
    $fileStp->setFilename('settings/stopword/list.txt');

    $contents = file_get_contents($fileStp->getFilenameOnFilestore());

    return explode(PHP_EOL, $contents);
}

function getSyns(){
// arbitrary file on the filestore
    $fileStp = new ElggFile();
    $fileStp->owner_guid = 7777;
    $fileStp->setFilename('settings/synonym/list.txt');

    $contents = file_get_contents($fileStp->getFilenameOnFilestore());

    return explode(PHP_EOL, $contents);
}

function setStops($words){
    $fileSyn = new ElggFile();
    $fileSyn->owner_guid = 7777;
    $fileSyn->setFilename('settings/stopword/list.txt');
    $fileSyn->open('write');
    $fileSyn->write($words);
    $fileSyn->close();
}

function setSyns($words){
    $fileSyn = new ElggFile();
    $fileSyn->owner_guid = 7777;
    $fileSyn->setFilename('settings/synonym/list.txt');
    $fileSyn->open('write');
    $fileSyn->write($words);
    $fileSyn->close();
}