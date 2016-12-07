<?php
/**
 * Created by PhpStorm.
 * User: niels
 * Date: 5/12/16
 * Time: 13:07
 */
// arbitrary file on the filestore
$fileSyn = new ElggFile();
$fileSyn->owner_guid = 7777;
$fileSyn->setFilename('settings/synonym/list.txt');

$contents = file_get_contents($fileSyn->getFilenameOnFilestore());

$synWordList = explode(PHP_EOL, $contents);

echo json_encode($synWordList);