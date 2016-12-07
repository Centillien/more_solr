<?php
/**
 * Created by PhpStorm.
 * User: niels
 * Date: 2/12/16
 * Time: 9:59
 */
if (!elgg_is_xhr()) {
    register_error('Sorry, Ajax only!');
    forward();
}
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
                system_message($input . ' added, refresh to see changes.');
            } else {
                register_error('Need at least 2 words for synonyms');
            }
        } else{
            register_error('Value already exists');
        }
        $synonyms = implode(PHP_EOL, $synonyms);
        setSyns($synonyms);
    } elseif ($type == 'stopword'){
        $stopwords = getStops();
        if(!in_array($input, $stopwords)){
            array_push($stopwords, $input);
            system_message($input . ' added, refresh to see changes.');
        } else{
            register_error('Value already exists');
        }
        $stopwords = implode(PHP_EOL, $stopwords);
        setStops($stopwords);
    }
}

function editWord ($type, $input, $old) {
    if($type == 'synonym'){
        //  Remove empty values
        $input = array_filter($input, function($value) { return $value !== ''; });
        if(count($input) > 1){
            $input = implode(",", $input);
            $arr = getSyns();
            $arr = implode(PHP_EOL, $arr);
            $test = str_replace($old, $input, $arr);
            system_message($old." successfully changed to ".$input.", refresh to see changes.");
        } else {
            register_error('Need at least 2 words for synonyms');
        }
        setSyns($test);
    } elseif ($type == 'stopword') {
        $arr = getStops();
        $arr = implode(PHP_EOL, $arr);
        $test = str_replace($old, $input, $arr);
        system_message($old." successfully changed to ".$input.", refresh to see changes.");
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
        system_message($old." successfully removed, refresh to see changes.");
        setSyns($test);
    } elseif ($type == 'stopword') {
        $arr = getStops();
        $arr = implode(PHP_EOL, $arr);
        $test = str_replace("\n".$old."\n", "\n", $arr);
        $test = preg_replace("/(^[\r\n]*|[\r\n]+)[\s\t]*[\r\n]+/", "\n", $test);
        system_message($old." successfully removed, refresh to see changes.");
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