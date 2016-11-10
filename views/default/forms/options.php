<?php
/**
 * More solr(advanced search) plugin settings
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
// TODO: usersettings default

$category = elgg_echo('options:category');
$cat_bar = elgg_view('input/select', array(
    'name' => 'category',
    'options_values' => array(
        'list of cats' => elgg_echo('cat1'),
    ),
));
// TODO: autotype + dropdown + make array list of cats

$sort = elgg_echo('options:sort');
$sort_bar = elgg_view('input/select', array(
    'name' => 'sort',
    'options_values' => array(
        'timeon' => elgg_echo('option:timeon'), // Time old - new
        'timeno' => elgg_echo('option:timeno'), // Time new - old
        'abcaz' => elgg_echo('option:abcaz'),   // Alphabet A - Z
        'abcza' => elgg_echo('option:abcza'),   // Alphabet Z - A
    ),
));
// TODO: default user setting

$tags = elgg_echo('options:tags');
$tags_bar = elgg_view('input/text', array(
    'name' => 'tags',
    'class' => 'elgg-input-thin',
    'placeholder' => elgg_echo('options:tags:placeholder'),
));

//  TODO:STOPWOORDEN(de het een)
$user = elgg_echo('options:user');
$user_bar = elgg_view('input/text', array(
    'name' => 'user',
    'class' => 'elgg-input-thin',
    'placeholder' => elgg_echo('options:user:placeholder'),
));

$date = elgg_echo('options:date');
// TODO: elgg datepicker

$results = elgg_echo('options:results');
$results_bar = elgg_view('input/select', array(
    'name' => 'results',
    'options_values' => array(
        'timeon' => elgg_echo('option:timeon'), // Time old - new
        'timeno' => elgg_echo('option:timeno'), // Time new - old
        'abcaz' => elgg_echo('option:abcaz'),   // Alphabet A - Z
        'abcza' => elgg_echo('option:abcza'),   // Alphabet Z - A
    ),
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
      </tr>
      <tr>
        <td><label>$synonym</label></td>
        <td>$syn_bar</td>
      </tr>
      <tr>
        <td><label>$category</label></td>
        <td>$cat_bar</td>
      </tr>
      <tr>
        <td><label>$sort</label></td>
        <td>$sort_bar</td>
      </tr>
      <tr>
        <td><label>$tags</label></td>
        <td>$tags_bar</td>
      </tr>
      <tr>
        <td><label>$user</label></td>
        <td>$user_bar</td>
      </tr>
      <tr>
        <td><label>$date</label></td>
        <td></td>
      </tr>
      <tr>
        <td><label>$results</label></td>
        <td>$results_bar</td>
      </tr>
    </table>
    $submit
</div>";

echo $settings;
