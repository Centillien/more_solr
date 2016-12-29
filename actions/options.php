<?php
$search = get_input('search');
$synonym = get_input('synonym');
$category = get_input('category');
$sort = get_input('sort');
$tags = get_input('tags');
$user = get_input('user');
$results = get_input('results');
$date = get_input('date');
$dateSets = get_input('dataRam');

echo '<pre>'; print_r($dateSets); echo '</pre>';

$supra = '';
if($dateSets['day'] != 'no'){
    $supra .= '1';
} else {
    $supra .= '0';
}
if($dateSets['month'] != 'no'){
    $supra .= '1';
} else {
    $supra .= '0';
}
if($dateSets['year'] != 'no'){
    $supra .= '1';
} else {
    $supra .= '0';
}

forward(elgg_get_site_url() . "advanced_search/list" .
    "?search=" . $search .
    "&synonym=" . $synonym .
    "&category=" . $category .
    "&sort=" . $sort .
    "&tags=" . $tags .
    "&user=" . $user .
    "&results=" . $results .
    "&date=" . $date .
    "&dateSets=" . $supra
);