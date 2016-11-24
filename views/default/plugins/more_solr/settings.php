<?php
/**
 * More solr(advanced search) plugin settings
 */

$chatEntities = elgg_get_entities_from_relationship([
    'type' => 'object',
    'subtype' => 'discussion',
    'limit' => false
]);

$search = elgg_echo('options:search');
$search_enable = elgg_view('input/select', array(
    'name' => 'params[search_en]',
    'options_values' => array(
        'yes' => elgg_echo('option:yes'),
        'no' => elgg_echo('option:no'),
    ),
    'value' => $vars['entity']->search_en ? $vars['entity']->search_en : 'no',
));

$synonym = elgg_echo('options:synonym');
$synonym_enable = elgg_view('input/select', array(
    'name' => 'params[syn_en]',
    'options_values' => array(
        'yes' => elgg_echo('option:yes'),
        'no' => elgg_echo('option:no'),
    ),
    'value' => $vars['entity']->syn_en ? $vars['entity']->syn_en : 'no',
));
$synonym_link = elgg_view('input/file', array(
    'name' => 'synFile',
    'class' => 'elgg-input-thin',
));
$synLinkHelp = elgg_echo('options:synonym:help').". Uploaded file: ";
$synLinkHelp .= $vars['entity']->syn_file ? $vars['entity']->syn_file : 'no';

$category = elgg_echo('options:category');
$category_enable = elgg_view('input/select', array(
    'name' => 'params[cat_en]',
    'options_values' => array(
        'yes' => elgg_echo('option:yes'),
        'no' => elgg_echo('option:no'),
    ),
    'value' => $vars['entity']->cat_en ? $vars['entity']->cat_en : 'no',
));
$vars['entity']->cat_list ? $catListValue = explode(",",$vars['entity']->cat_list) : $catListValue = 'all';

$arr = [];
$arr['all'] = 'all';
$types = get_registered_entity_types();
foreach($types['object'] as $type){
    print_r($types['object'][$type]);
    $arr["$type"] = $type;
}
$category_list = elgg_view('input/select', array(
    'name' => 'params[cat_list]',
    'options_values' => $arr,
    'value' => $catListValue,
    'multiple' => 'multiple',
));
$catListHelp = elgg_echo('options:cat:list:help');

$sort = elgg_echo('options:sort');
$sort_enable = elgg_view('input/select', array(
    'name' => 'params[sort_en]',
    'options_values' => array(
        'yes' => elgg_echo('option:yes'),
        'no' => elgg_echo('option:no'),
    ),
    'value' => $vars['entity']->sort_en ? $vars['entity']->sort_en : 'no',
));
$vars['entity']->sort_list ? $sortListValue = explode(",",$vars['entity']->sort_list) : $sortListValue = 'timeno';

$sort_list = elgg_view('input/select', array(
    'name' => 'params[sort_list]',
    'options_values' => array(
        'timeon' => elgg_echo('option:timeon'), // Time old - new
        'timeno' => elgg_echo('option:timeno'), // Time new - old
        'abcaz' => elgg_echo('option:abcaz'),   // Alphabet A - Z
        'abcza' => elgg_echo('option:abcza'),   // Alphabet Z - A
    ),
    'value' => $sortListValue,
    'multiple' => 'multiple',
));
$sortListHelp = elgg_echo('options:sort:list:help');
$sort_default = elgg_view('input/select', array(
    'name' => 'params[sort_def]',
    'options_values' => array(
        'timeon' => elgg_echo('option:timeon'), // Time old - new
        'timeno' => elgg_echo('option:timeno'), // Time new - old
        'abcaz' => elgg_echo('option:abcaz'),   // Alphabet A - Z
        'abcza' => elgg_echo('option:abcza'),   // Alphabet Z - A
    ),
    'value' => $vars['entity']->sort_def ? $vars['entity']->sort_def : 'timeno',
));
$sortDefHelp = elgg_echo('options:sort:list:default:help');

$tags = elgg_echo('options:tags');
$tags_enable = elgg_view('input/select', array(
    'name' => 'params[tags_en]',
    'options_values' => array(
        'yes' => elgg_echo('option:yes'),
        'no' => elgg_echo('option:no'),
    ),
    'value' => $vars['entity']->tags_en ? $vars['entity']->tags_en : 'no',
));

//  STOPWOORDEN(de het een)
$user = elgg_echo('options:user');
$user_enable = elgg_view('input/select', array(
    'name' => 'params[user_en]',
    'options_values' => array(
        'yes' => elgg_echo('option:yes'),
        'no' => elgg_echo('option:no'),
    ),
    'value' => $vars['entity']->user_en ? $vars['entity']->user_en : 'no',
));
$userAd = elgg_echo('options:user:admin');
$userAd_enable = elgg_view('input/select', array(
    'name' => 'params[usAd_en]',
    'options_values' => array(
        'yes' => elgg_echo('option:yes'),
        'no' => elgg_echo('option:no'),
    ),
    'value' => $vars['entity']->usAd_en ? $vars['entity']->usAd_en : 'no',
));
$userAdHelp = elgg_echo('options:user:admin:help');

$date = elgg_echo('options:date');
$date_enable = elgg_view('input/select', array(
    'name' => 'params[date_en]',
    'options_values' => array(
        'yes' => elgg_echo('option:yes'),
        'no' => elgg_echo('option:no'),
    ),
    'value' => $vars['entity']->date_en ? $vars['entity']->date_en : 'no',
));
$dateDay = elgg_echo('options:date:day');
$dateDay_en = elgg_view('input/select', array(
    'name' => 'params[day_en]',
    'options_values' => array(
        'yes' => elgg_echo('option:yes'),
        'no' => elgg_echo('option:no'),
    ),
    'value' => $vars['entity']->day_en ? $vars['entity']->day_en : 'no',
));
$dateDayHelp = elgg_echo('options:date:day:help');
$dateMonth = elgg_echo('options:date:month');
$dateMonth_enable = elgg_view('input/select', array(
    'name' => 'params[mon_en]',
    'options_values' => array(
        'yes' => elgg_echo('option:yes'),
        'no' => elgg_echo('option:no'),
    ),
    'value' => $vars['entity']->mon_en ? $vars['entity']->mon_en : 'no',
));
$dateMonthHelp = elgg_echo('options:date:month:help');
$dateYear = elgg_echo('options:date:year');
$dateYear_enable = elgg_view('input/select', array(
    'name' => 'params[year_en]',
    'options_values' => array(
        'yes' => elgg_echo('option:yes'),
        'no' => elgg_echo('option:no'),
    ),
    'value' => $vars['entity']->year_en ? $vars['entity']->year_en : 'no',
));
$dateYearHelp = elgg_echo('options:date:year:help');

$results = elgg_echo('options:results');
$results_enable = elgg_view('input/select', array(
    'name' => 'params[res_en]',
    'options_values' => array(
        'yes' => elgg_echo('option:yes'),
        'no' => elgg_echo('option:no'),
    ),
    'value' => $vars['entity']->res_en ? $vars['entity']->res_en : 'no',
));
$results_amount = elgg_view('input/text', array(
    'name' => 'params[res_am]',
    'value' => $vars['entity']->res_am == '' ? elgg_echo('options:results:placeholder') : $vars['entity']->res_am,
    'class' => 'elgg-input-thin',
    'placeholder' => $vars['entity']->res_am ? $vars['entity']->res_am : elgg_echo('options:results:placeholder'),
));
$resultsAmountHelp = elgg_echo('options:results:help');


// Relevance options

$searchRel = elgg_view('input/text', array(
    'name' => 'params[searchRel]',
    'value' => $vars['entity']->searchRel == '' ? elgg_echo('options:results:placeholder') : $vars['entity']->searchRel,
    'class' => 'elgg-input-thin',
    'placeholder' => $vars['entity']->searchRel ? $vars['entity']->searchRel : elgg_echo('options:results:placeholder'),
));
$synonymRel = elgg_view('input/text', array(
    'name' => 'params[synonymRel]',
    'value' => $vars['entity']->synonymRel == '' ? elgg_echo('options:results:placeholder') : $vars['entity']->synonymRel,
    'class' => 'elgg-input-thin',
    'placeholder' => $vars['entity']->synonymRel ? $vars['entity']->synonymRel : elgg_echo('options:results:placeholder'),
));
$categoryRel = elgg_view('input/text', array(
    'name' => 'params[categoryRel]',
    'value' => $vars['entity']->categoryRel == '' ? elgg_echo('options:results:placeholder') : $vars['entity']->categoryRel,
    'class' => 'elgg-input-thin',
    'placeholder' => $vars['entity']->categoryRel ? $vars['entity']->categoryRel : elgg_echo('options:results:placeholder'),
));
$sortRel = elgg_view('input/text', array(
    'name' => 'params[sortRel]',
    'value' => $vars['entity']->sortRel == '' ? elgg_echo('options:results:placeholder') : $vars['entity']->sortRel,
    'class' => 'elgg-input-thin',
    'placeholder' => $vars['entity']->sortRel ? $vars['entity']->sortRel : elgg_echo('options:results:placeholder'),
));
$tagsRel = elgg_view('input/text', array(
    'name' => 'params[tagsRel]',
    'value' => $vars['entity']->tagsRel == '' ? elgg_echo('options:results:placeholder') : $vars['entity']->tagsRel,
    'class' => 'elgg-input-thin',
    'placeholder' => $vars['entity']->tagsRel ? $vars['entity']->tagsRel : elgg_echo('options:results:placeholder'),
));
$userRel = elgg_view('input/text', array(
    'name' => 'params[userRel]',
    'value' => $vars['entity']->userRel == '' ? elgg_echo('options:results:placeholder') : $vars['entity']->userRel,
    'class' => 'elgg-input-thin',
    'placeholder' => $vars['entity']->userRel ? $vars['entity']->userRel : elgg_echo('options:results:placeholder'),
));
$dateRel = elgg_view('input/text', array(
    'name' => 'params[dateRel]',
    'value' => $vars['entity']->dateRel == '' ? elgg_echo('options:results:placeholder') : $vars['entity']->dateRel,
    'class' => 'elgg-input-thin',
    'placeholder' => $vars['entity']->dateRel ? $vars['entity']->dateRel : elgg_echo('options:results:placeholder'),
));
$resultsRel = elgg_view('input/text', array(
    'name' => 'params[resultsRel]',
    'value' => $vars['entity']->resultsRel == '' ? elgg_echo('options:results:placeholder') : $vars['entity']->resultsRel,
    'class' => 'elgg-input-thin',
    'placeholder' => $vars['entity']->resultsRel ? $vars['entity']->resultsRel : elgg_echo('options:results:placeholder'),
));
$formSubmit = elgg_view('input/submit', array(
    'value' => 'Save',
    'class' => 'elgg-button-submit elgg-button',
));



$relevancyTitle = elgg_echo('options:relevancy:title');
$relevancyInfo = elgg_echo('options:relevancy:info');
$optionsTitle = elgg_echo('options:title');

elgg_extend_view('css/admin', 'css/admin/more_solr');

$settings = <<<__HTML
    <h1>$optionsTitle</h1>
<table>
 <thead>
  <tr>
     <th></th>
     <th>Enable function?</th>
     <th></th>
  </tr>
 </thead>
  <tr>
    <td><label>$search</label></td>
    <td>$search_enable</td>
  </tr>
  <tr>
    <td><label>$synonym</label></td>
    <td>$synonym_enable</td>
    <td>$synonym_link <br> $synLinkHelp</td>
  </tr>
  <tr>
    <td><label>$category</label></td>
    <td>$category_enable</td>
    <td>$category_list <br> $catListHelp</td>
  </tr>
  <tr>
    <td><label>$sort</label></td>
    <td>$sort_enable</td>
    <td>
        <table>
            <tr>
                <td>$sort_list <br> $sortListHelp</td>
                <td>$sort_default <br> $sortDefHelp</td>
            </tr>
        </table>
     </td>
  </tr>
  <tr>
    <td><label>$tags</label></td>
    <td>$tags_enable</td>
  </tr>
  <tr>
    <td><label>$user</label></td>
    <td>$user_enable</td>
    <td>
        <table>
            <tr>
                <td><label>$userAd</label></td>
                <td>$userAd_enable $userAdHelp</td>
            </tr>
        </table>
    </td>
  </tr>
  <tr>
    <td><label>$date</label></td>
    <td>$date_enable</td>
    <td>
        <table>
            <tr>
                <td><label>$dateDay</label></td>
                <td>$dateDay_en $dateDayHelp</td>
                <td><label>$dateMonth</label></td>
                <td>$dateMonth_enable $dateMonthHelp</td>
                <td><label>$dateYear</label></td>
                <td>$dateYear_enable $dateYearHelp</td>
            </tr>
        </table>
  </tr>
  <tr>
    <td><label>$results</label></td>
    <td>$results_enable</td>
    <td>$results_amount <br> $resultsAmountHelp</td>
  </tr>
</table>
$formSubmit

    <h1>$relevancyTitle</h1>
    <p>$relevancyInfo</p>
<table>
  <tr>
    <td><label>$search</label></td>
    <td>$searchRel <br></td>
  </tr>
  <tr>
    <td><label>$synonym</label></td>
    <td>$synonymRel <br></td>
  </tr>
  <tr>
    <td><label>$category</label></td>
    <td>$categoryRel <br></td>
  </tr>
  <tr>
    <td><label>$sort</label></td>
    <td>$sortRel <br></td>
  </tr>
  <tr>
    <td><label>$tags</label></td>
    <td>$tagsRel <br></td>
  </tr>
  <tr>
    <td><label>$user</label></td>
    <td>$userRel <br></td>
  </tr>
  <tr>
    <td><label>$date</label></td>
    <td>$dateRel <br></td>
  </tr>
  <tr>
    <td><label>$results</label></td>
    <td>$resultsRel <br></td>
  </tr>
</table>
__HTML;

echo $settings;
?>
<script type="text/javascript">
document.getElementById( "more_solr-settings" )
    .setAttribute("enctype", "multipart/form-data")
    .setAttribute("encoding", "multipart/form-data");
</script>