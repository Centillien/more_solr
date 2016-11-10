<?php
/**
 * More solr(advanced search) plugin settings
 */

$search = elgg_echo('options:search');
$search_bar = elgg_view('input/text', array(
    'name' => 'params[sea_bar]',
    'class' => 'elgg-input-thin',
    'placeholder' => elgg_echo('options:search:placeholder'),
));
// render placeholder separately so it will double-encode if needed
$placeholder = htmlspecialchars(elgg_echo('search'), ENT_QUOTES, 'UTF-8');

$synonym = elgg_echo('options:synonym');
$syn_bar = elgg_view('input/select', array(
    'name' => 'params[syn_bar]',
    'options_values' => array(
        'yes' => elgg_echo('option:yes'),
        'no' => elgg_echo('option:no'),
    ),
));
// TODO: usersettings default

$category = elgg_echo('options:category');
$cat_bar = elgg_view('input/select', array(
    'name' => 'params[cat_bar]',
    'options_values' => array(
        'list of cats' => elgg_echo('cat1'),
    ),
));
// TODO: autotype + dropdown + make array list of cats

$sort = elgg_echo('options:sort');
$sort_bar = elgg_view('input/select', array(
    'name' => 'params[sor_bar]',
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
    'name' => 'params[tag_bar]',
    'class' => 'elgg-input-thin',
    'placeholder' => elgg_echo('options:tags:placeholder'),
));

//  TODO:STOPWOORDEN(de het een)
$user = elgg_echo('options:user');
$user_bar = elgg_view('input/text', array(
    'name' => 'params[use_bar]',
    'class' => 'elgg-input-thin',
    'placeholder' => elgg_echo('options:user:placeholder'),
));

$date = elgg_echo('options:date');
// TODO: elgg datepicker

$results = elgg_echo('options:results');
$results_bar = elgg_view('input/select', array(
    'name' => 'params[res_bar]',
    'options_values' => array(
        'timeon' => elgg_echo('option:timeon'), // Time old - new
        'timeno' => elgg_echo('option:timeno'), // Time new - old
        'abcaz' => elgg_echo('option:abcaz'),   // Alphabet A - Z
        'abcza' => elgg_echo('option:abcza'),   // Alphabet Z - A
    ),
));

$optionsTitle = elgg_echo('options:title');
$search_button = elgg_echo('search:go');

$class = "elgg-search";
if (isset($vars['class'])) {
    $class = "$class {$vars['class']}";
}
$form_action = elgg_get_site_url()."search";
$settings = "
<div class='popup-body'>
    <fieldset>
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
        <input type='submit' value='$search_button' class='search-submit-button' name='advanced_search'/>
    </fieldset>
</div>";

echo $settings;
