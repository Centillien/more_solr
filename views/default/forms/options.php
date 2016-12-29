<?php
/**
 * Advanced search plugin settings
 */

$search = elgg_echo('options:search');
$search_bar = elgg_view('input/text', array(
    'name' => 'search',
    'class' => 'elgg-input-thin',
    'placeholder' => elgg_echo('options:search:placeholder'),
));
// render placeholder separately so it will double-encode if needed
$placeholder = htmlspecialchars(elgg_echo('search'), ENT_QUOTES, 'UTF-8');

$synonym = elgg_echo('options:synonym');
$syn_bar = elgg_view('input/select', array(
    'name' => 'synonym',
    'options_values' => array(
        'yes' => elgg_echo('option:yes'),
        'no' => elgg_echo('option:no'),
    ),
));

$arr = [];
$pizza  = elgg_get_plugin_setting('cat_list', 'advanced_search');
$pieces = explode(",", $pizza);
foreach($pieces as $piece){
    print_r($types['object'][$piece]);
    $arr["$piece"] = elgg_echo($piece);
}
$category = elgg_echo('options:category');
$cat_bar = elgg_view('input/select', array(
    'name' => 'category',
    'options_values' => $arr,
));

$arr = [];
$pizza  = elgg_get_plugin_setting('sort_list', 'advanced_search');
$default  = elgg_get_plugin_setting('sort_def', 'advanced_search');
$pieces = explode(",", $pizza);
foreach($pieces as $piece){
    print_r($types['object'][$piece]);
    $arr["$piece"] = elgg_echo('option:'.$piece);
}
$sort = elgg_echo('options:sort');
$sort_bar = elgg_view('input/select', array(
    'name' => 'sort',
    'value' => $default,
    'options_values' => $arr,
));

$tags = elgg_echo('options:tags');
$tags_bar = elgg_view('input/text', array(
    'name' => 'tags',
    'class' => 'elgg-input-thin',
    'placeholder' => elgg_echo('options:tags:placeholder'),
));

$user = elgg_echo('options:user');
$user_bar = elgg_view('input/text', array(
    'name' => 'user',
    'id' => 'userAuto',
    'class' => 'elgg-input-thin',
    'placeholder' => elgg_echo('options:user:placeholder'),
));

$date = elgg_echo('options:date');
$date_bar = elgg_view('input/date', array(
    'name' => 'date',
    'id' => 'date',
    'class' => 'elgg-input-thin',
    'placeholder' => elgg_echo('options:date:placeholder'),
));

$dateLink = elgg_view('output/url', [
    'href' => '#',
    'text' => 'Show/hide date settings',
    'id' => 'openDateMenu'
]);

$day = elgg_echo('options:day');
$day_bar = elgg_view('input/select', array(
    'name' => 'dataRam[day]',
    'value' => 'no',
    'options_values' => array(
        'yes' => elgg_echo('option:yes'),
        'no' => elgg_echo('option:no'),
    ),
));
$month = elgg_echo('options:month');
$month_bar = elgg_view('input/select', array(
    'name' => 'dataRam[month]',
    'value' => 'no',
    'options_values' => array(
        'yes' => elgg_echo('option:yes'),
        'no' => elgg_echo('option:no'),
    ),
));
$year = elgg_echo('options:year');
$year_bar = elgg_view('input/select', array(
    'name' => 'dataRam[year]',
    'value' => 'no',
    'options_values' => array(
        'yes' => elgg_echo('option:yes'),
        'no' => elgg_echo('option:no'),
    ),
));

$pizza  = elgg_get_plugin_setting('res_am', 'advanced_search');
$carr = [];
$arr = [$pizza - 30, $pizza - 10, $pizza, $pizza + 10, $pizza + 30];
$farr = array_filter($arr, function ($x) { return $x > 0; });
foreach($farr as $f){
    print_r($types['object'][$f]);
    $carr["$f"] = elgg_echo($f);
}
$results = elgg_echo('options:results');
$results_bar = elgg_view('input/select', array(
    'name' => 'results',
    'options_values' => $carr,
));

$userArray = [];
$userResults = elgg_get_entities(array(
        'types' => 'user',
        'limit' => 0,)
);

//  Get admins
$admin_guids = elgg_get_admins(array(
    'limit' => 0,
    'callback' => function ($row) { return $row->guid; }, // no overhead of entity creation
));

foreach($userResults as $v){
    $president  = elgg_get_plugin_setting('usAd_en', 'advanced_search');
    if($president == 'no'){
        if(!in_array($v->guid,$admin_guids))
        {
            array_push($userArray, $v->name.":".$v->guid);
        }
    }
    else {
        array_push($userArray, $v->name.":".$v->guid);
    }
}

$json = json_encode(utf8ize($userArray), JSON_UNESCAPED_UNICODE);
$kappa_bar = elgg_view('input/text', array(
    'name' => 'getUsers',
    'class' => 'hiddenUsers',
    'value' => $json,
));

$submit = elgg_view('input/submit', array('value' => elgg_echo('search:go')));
$optionsTitle = elgg_echo('options:title');
$settings = "
<div class='popup-body'>
        <h1>$optionsTitle</h1>
    <table>
     <thead>
      <tr>
         <th></th>
         <th></th>
      </tr>
     </thead>
      <tr>
        <td><label>$search</label></td>
        <td>$search_bar</td>
      </tr>";
$setting = elgg_get_plugin_setting('syn_en', 'advanced_search');
if($setting != 'no'){
    $settings .= "
      <tr>
        <td><label>$synonym</label></td>
        <td>$syn_bar</td>
      </tr>";
}
$setting = elgg_get_plugin_setting('cat_en', 'advanced_search');
if($setting != 'no'){
    $settings .= "
      <tr>
        <td><label>$category</label></td>
        <td>$cat_bar</td>
      </tr>";
}
$setting = elgg_get_plugin_setting('sort_en', 'advanced_search');
if($setting != 'no'){
    $settings .= "
      <tr>
        <td><label>$sort</label></td>
        <td>$sort_bar</td>
      </tr>";
}
$setting = elgg_get_plugin_setting('tags_en', 'advanced_search');
if($setting != 'no'){
    $settings .= "
      <tr>
        <td><label>$tags</label></td>
        <td>$tags_bar</td>
      </tr>";
}
$setting = elgg_get_plugin_setting('user_en', 'advanced_search');
if($setting != 'no'){
    $settings .= "
      <tr>
        <td><label>$user</label></td>
        <td>$user_bar</td>
      </tr>";
}
$setting = elgg_get_plugin_setting('date_en', 'advanced_search');
if($setting != 'no') {
    $settings .= "
      <tr>
        <td><label>$date</label></td>
        <td>$date_bar</td>
      </tr>
      <tr>
        <td>$dateLink</td>
      </tr>";
}
$settingDay = elgg_get_plugin_setting('day_en', 'advanced_search');
$settingMonth = elgg_get_plugin_setting('mon_en', 'advanced_search');
$settingYear = elgg_get_plugin_setting('year_en', 'advanced_search');
if($settingDay || $settingMonth || $settingYear){
    //  Use the div as a dropdown list link
    if($settingDay != 'no') {
        $settings .= "
          <tr class='hidden dateSets'>
            <td><label>$day</label></td>
            <td>$day_bar</td>
          </tr>";
    }
    if($settingMonth != 'no') {
        $settings .= "
          <tr class='hidden dateSets'>
            <td><label>$month</label></td>
            <td>$month_bar</td>
          </tr>";
    }
    if($settingYear != 'no') {
        $settings .= "
          <tr class='hidden dateSets'>
            <td><label>$year</label></td>
            <td>$year_bar</td>
          </tr>";
    }
}
$setting = elgg_get_plugin_setting('res_en', 'advanced_search');
if($setting != 'no') {
    $settings .= "
      <tr>
        <td><label>$results</label></td>
        <td>$results_bar</td>
        $kappa_bar
      </tr>";
}
    $settings .= "
    </table>
    $submit
</div>";

echo $settings;

function utf8ize($d) {
    if (is_array($d)) {
        foreach ($d as $k => $v) {
            $d[$k] = utf8ize($v);
        }
    } else if (is_string ($d)) {
        return utf8_encode($d);
    }
    return $d;
}
