<?php
/**
 * Created by PhpStorm.
 * User: niels
 * Date: 5/12/16
 * Time: 13:07
 */
// arbitrary file on the filestore
$fileStp = new ElggFile();
$fileStp->owner_guid = 7777;
$fileStp->setFilename('settings/stopword/list.txt');

$contents = file_get_contents($fileStp->getFilenameOnFilestore());

$stopWordList = explode(PHP_EOL, $contents);

echo json_encode($stopWordList);