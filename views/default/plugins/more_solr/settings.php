<?php
/**
 * advanced search plugin settings
 */

elgg_load_js('admin_settings');
/** Get stopwords and synonyms file */

// arbitrary file on the filestore
$fileStp = new ElggFile();
$fileStp->owner_guid = 7777;
$fileStp->setFilename('settings/stopword/list.txt');

$contents = file_get_contents($fileStp->getFilenameOnFilestore());

$contents = strtolower($contents);
$stopWordList = explode(PHP_EOL, $contents);

// arbitrary file on the filestore
$fileSyn = new ElggFile();
$fileSyn->owner_guid = 7777;
$fileSyn->setFilename('settings/synonym/list.txt');

$contents = file_get_contents($fileSyn->getFilenameOnFilestore());

$contents = strtolower($contents);
$synWordList = explode(PHP_EOL, $contents);

$chatEntities = elgg_get_entities_from_relationship([
    'type' => 'object',
    'subtype' => 'discussion',
    'limit' => false
]);

$synonym = elgg_echo('options:synonym');
$synonym_enable = elgg_view('input/select', array(
    'name' => 'params[syn_en]',
    'options_values' => array(
        'yes' => elgg_echo('option:yes'),
        'no' => elgg_echo('option:no'),
    ),
    'value' => $vars['entity']->syn_en ? $vars['entity']->syn_en : elgg_echo('option:no'),
));
$synonym_link = elgg_view('input/file', array(
    'name' => 'synFile',
    'class' => 'elgg-input-thin',
));
$synLinkHelp = elgg_echo('options:synonym:help').". Uploaded file: ";
$synLinkHelp .= $vars['entity']->syn_file ? $vars['entity']->syn_file : elgg_echo('no:file');

$stopwords = elgg_echo('options:stop');
$stopwords_enable = elgg_view('input/select', array(
    'name' => 'params[stp_en]',
    'options_values' => array(
        'yes' => elgg_echo('option:yes'),
        'no' => elgg_echo('option:no'),
    ),
    'value' => $vars['entity']->stp_en ? $vars['entity']->stp_en : elgg_echo('option:no'),
));
$stopwords_link = elgg_view('input/file', array(
    'name' => 'stpFile',
    'class' => 'elgg-input-thin',
));
$stopLinkHelp = elgg_echo('options:stop:help');
$stopLinkHelp .= $vars['entity']->stp_file ? $vars['entity']->stp_file : elgg_echo('no:file');

$category = elgg_echo('options:category');
$category_enable = elgg_view('input/select', array(
    'name' => 'params[cat_en]',
    'options_values' => array(
        'yes' => elgg_echo('option:yes'),
        'no' => elgg_echo('option:no'),
    ),
    'value' => $vars['entity']->cat_en ? $vars['entity']->cat_en : elgg_echo('option:no'),
));
$vars['entity']->cat_list ? $catListValue = explode(",",$vars['entity']->cat_list) : $catListValue = elgg_echo('option:all');

$client = elgg_solr_get_client();

// get a select query instance
$query = $client->createSelect();

$facetSet = $query->getFacetSet();
$facetSet->createFacetField('subtypes')->setField('subtype');

$resultset = $client->select($query);

$facet = $resultset->getFacetSet()->getFacet('subtypes');
$types = ['group', 'user'];
foreach ($facet as $value => $count) {
    if($value){
        $types[] .= $value;
    }
}

$arr = [];
$arr['all'] = elgg_echo('option:all');
foreach($types as $type){
    $arr["$type"] = $type;
}
$category_list = elgg_view('input/select', array(
    'name' => 'params[cat_list]',
    'options_values' => $arr,
    'value' => $catListValue,
    'multiple' => 'multiple',
));
$catListHelp = elgg_echo('options:cat:list:help');
$catListHelpWarning = elgg_echo('options:cat:list:help:warning');

$sort = elgg_echo('options:sort');
$sort_enable = elgg_view('input/select', array(
    'name' => 'params[sort_en]',
    'options_values' => array(
        'yes' => elgg_echo('option:yes'),
        'no' => elgg_echo('option:no'),
    ),
    'value' => $vars['entity']->sort_en ? $vars['entity']->sort_en : elgg_echo('option:no'),
));
$vars['entity']->sort_list ? $sortListValue = explode(",",$vars['entity']->sort_list) : $sortListValue = 'timeno';


$setting = elgg_get_plugin_setting('search_en', 'advanced_search');

$sorter = array(
    //'popularity' => elgg_echo('option:popularity'),     // Aantal vrienden, aantal replies
    'relevancy' => elgg_echo('option:relevancy'),       // Hoogste score(solr)
    'timeon' => elgg_echo('option:timeon'),             // Time old - new
    'timeno' => elgg_echo('option:timeno'),             // Time new - old
    'abcaz' => elgg_echo('option:abcaz'),               // Alphabet A - Z
    'abcza' => elgg_echo('option:abcza'));              // Alphabet Z - A

$sort_list = elgg_view('input/select', array(
    'name' => 'params[sort_list]',
    'options_values' => $sorter,
    'value' => $sortListValue,
    'multiple' => 'multiple',
));
$sortListHelp = elgg_echo('options:sort:list:help');
$sort_default = elgg_view('input/select', array(
    'name' => 'params[sort_def]',
    'options_values' => $sorter,
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
    'value' => $vars['entity']->tags_en ? $vars['entity']->tags_en : elgg_echo('option:no'),
));

//  STOPWOORDEN(de het een)
$user = elgg_echo('options:user');
$user_enable = elgg_view('input/select', array(
    'name' => 'params[user_en]',
    'options_values' => array(
        'yes' => elgg_echo('option:yes'),
        'no' => elgg_echo('option:no'),
    ),
    'value' => $vars['entity']->user_en ? $vars['entity']->user_en : elgg_echo('option:no'),
));
/*
 *  Removed for incompatibility with elgg_solr
$userAd = elgg_echo('options:user:admin');
$userAd_enable = elgg_view('input/select', array(
    'name' => 'params[usAd_en]',
    'options_values' => array(
        'yes' => elgg_echo('option:yes'),
        'no' => elgg_echo('option:no'),
    ),
    'value' => $vars['entity']->usAd_en ? $vars['entity']->usAd_en : elgg_echo('option:no'),
));
$userAdHelp = elgg_echo('options:user:admin:help');
*/

$date = elgg_echo('options:date');
$date_enable = elgg_view('input/select', array(
    'name' => 'params[date_en]',
    'options_values' => array(
        'yes' => elgg_echo('option:yes'),
        'no' => elgg_echo('option:no'),
    ),
    'value' => $vars['entity']->date_en ? $vars['entity']->date_en : elgg_echo('option:no'),
));
$dateDay = elgg_echo('options:date:day');
$dateDay_en = elgg_view('input/select', array(
    'name' => 'params[day_en]',
    'options_values' => array(
        'yes' => elgg_echo('option:yes'),
        'no' => elgg_echo('option:no'),
    ),
    'value' => $vars['entity']->day_en ? $vars['entity']->day_en : elgg_echo('option:no'),
));
$dateDayHelp = elgg_echo('options:date:day:help');
$dateMonth = elgg_echo('options:date:month');
$dateMonth_enable = elgg_view('input/select', array(
    'name' => 'params[mon_en]',
    'options_values' => array(
        'yes' => elgg_echo('option:yes'),
        'no' => elgg_echo('option:no'),
    ),
    'value' => $vars['entity']->mon_en ? $vars['entity']->mon_en : elgg_echo('option:no'),
));
$dateMonthHelp = elgg_echo('options:date:month:help');
$dateYear = elgg_echo('options:date:year');
$dateYear_enable = elgg_view('input/select', array(
    'name' => 'params[year_en]',
    'options_values' => array(
        'yes' => elgg_echo('option:yes'),
        'no' => elgg_echo('option:no'),
    ),
    'value' => $vars['entity']->year_en ? $vars['entity']->year_en : elgg_echo('option:no'),
));
$dateYearHelp = elgg_echo('options:date:year:help');

$results = elgg_echo('options:results');
$results_enable = elgg_view('input/select', array(
    'name' => 'params[res_en]',
    'options_values' => array(
        'yes' => elgg_echo('option:yes'),
        'no' => elgg_echo('option:no'),
    ),
    'value' => $vars['entity']->res_en ? $vars['entity']->res_en : elgg_echo('option:no'),
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

//  Hidden pop-up options(for add/edit)
//  synwords
$synAdd = elgg_view('input/text', array(
    'name' => 'addSins[1]',
    'value' => $vars['entity']->synAdd == '' ? '' : $vars['entity']->synAdd,
    'class' => 'elgg-input-thin listInputs',
    'placeholder' => $vars['entity']->synAdd ? $vars['entity']->synAdd : elgg_echo('options:synAdd:placeholder'),
));
$synAddButt = elgg_view('input/button', array(
    'value' => elgg_echo('options:buttons:synAdd'),
    'id' => 'syn-add'
));
$synEdd = elgg_view('input/text', array(
    'name' => 'eddSins[1]',
    'value' => 'display to edit value here',
    'id' => 'synEddInput',
    'class' => 'elgg-input-thin listInputs',
    'placeholder' => $vars['entity']->synEdit ? $vars['entity']->synEdit : elgg_echo('options:synEdd:placeholder'),
));
$synEddButt = elgg_view('input/button', array(
    'value' => elgg_echo('options:buttons:synEdd'),
    'id' => 'syn-edit'
));

//  stopwords
$stpAdd = elgg_view('input/text', array(
    'name' => 'params[stpAdd]',
    'value' => $vars['entity']->stpAdd == '' ? '' : $vars['entity']->stpAdd,
    'class' => 'elgg-input-thin',
    'placeholder' => $vars['entity']->stpAdd ? $vars['entity']->stpAdd : elgg_echo('options:stpAdd:placeholder'),
));
$stpAddButt = elgg_view('input/button', array(
    'value' => elgg_echo('options:buttons:stpAdd'),
    'id' => 'stp-add'
));
$stpEdd = elgg_view('input/text', array(
    'name' => 'params[stpEdit]',
    'value' => 'display to edit value here',
    'id' => 'stpEddInput',
    'class' => 'elgg-input-thin',
    'placeholder' => $vars['entity']->stpEdit ? $vars['entity']->stpEdit : elgg_echo('options:stpEdd:placeholder'),
));
$stpEddButt = elgg_view('input/button', array(
    'value' => elgg_echo('options:buttons:stpEdd'),
    'id' => 'stp-edit'
));

//  Back buttons
$stpBackButt = elgg_view('input/button', array(
    'value' => elgg_echo('options:buttons:back'),
    'class' => 'stp-back'
));
$synBackButt = elgg_view('input/button', array(
    'value' => elgg_echo('options:buttons:back'),
    'class' => 'syn-back'
));
$syn_AddAddButt = elgg_view('input/button', array(
    'value' => elgg_echo('options:buttons:addField'),
    'id' => 'syn-add-add'
));
$syn_AddEddButt = elgg_view('input/button', array(
    'value' => elgg_echo('options:buttons:addField'),
    'id' => 'syn-add-edd'
));

//  Submit button
$formSubmit = elgg_view('input/submit', array(
    'value' => elgg_echo('option:save'),
    'class' => 'elgg-button-submit elgg-button',
));

//  Popup link open
$synPopup = elgg_view('output/url', [
    'href' => '#popup-syn-open',
    'text' => elgg_echo('options:buttons:synonym'),
    'rel' => 'popup',
    'class' => 'elgg-lightbox',
    'data-position' => json_encode([
        'my' => 'center bottom',
        'at' => 'center top',
    ]),
]);

$stpPopup = elgg_view('output/url', [
    'href' => '#popup-stop-open',
    'text' => elgg_echo('options:buttons:stopword'),
    'rel' => 'popup',
    'class' => 'elgg-lightbox',
    'data-position' => json_encode([
        'my' => 'center bottom',
        'at' => 'center top',
    ]),
]);

//  Stop options
$newStop = elgg_view('output/url', [
    'href' => '#',
    'text' => elgg_echo('new:word'),
    'id' => 'stpNewWord',
    'class' => 'hideStpTable'
]);
$addStop = elgg_view('output/url', [
    'href' => '#',
    'text' => elgg_echo('add'),
    'class' => 'hideStpTable stpAddWord'
]);
$delStop = elgg_view('output/url', [
    'href' => '#',
    'text' => elgg_echo('delete'),
    'class' => 'stpDelWord'
]);
//  Syn options
$newSyn = elgg_view('output/url', [
    'href' => '#',
    'text' => elgg_echo('new:word'),
    'id' => 'synNewWord',
    'class' => 'hideSynTable'
]);
$addSyn = elgg_view('output/url', [
    'href' => '#',
    'text' => elgg_echo('add'),
    'class' => 'hideSynTable synAddWord'
]);
$delSyn = elgg_view('output/url', [
    'href' => '#',
    'text' => elgg_echo('delete'),
    'class' => 'synDelWord'
]);

//  Lists of words tables
$stopTable = '<div class="popup-content">
                <table id="stopwordTable" class="scrollTable">
                    <th><h2>'.elgg_echo("stopwords:title").'</h2></th>
                    <th>'.$newStop.'</th>';
//  Sort array alphabetically
sort($stopWordList);
//  Remove empty strings from array
$stopWordList = array_filter($stopWordList, function($value) { return $value !== ''; });
foreach($stopWordList as $word){
    $stopTable .= ' <tr>
                        <td>'.$word.'</td>
                        <td>'.$delStop.'/'.$addStop.'</td>
                    </tr>';
}
$stopTable .= ' </table>
                    <div id="newStop" class="hidden">
                        <h2>'.elgg_echo("stopword:new").'</h2><br>
                        '.$stpAdd.'<br>
                        '.$stpAddButt.$stpBackButt.'
                    </div>
                    <div id="addStop" class="hidden">
                        <h2>'.elgg_echo("stopword:edit").'</h2><br>
                        '.$stpEdd.'<br>
                        '.$stpEddButt.$stpBackButt.'
                    </div>
               </div>';

$synTable = '<div class="popup-content">
                <table id="synwordTable" class="scrollTable">
                    <th><h2>'.elgg_echo("synonyms:title").'</h2></th>
                    <th>'.$newSyn.'</th>';
//  Sort array alphabetically
sort($synWordList);
//  Remove empty strings from array
$synWordList = array_filter($synWordList, function($value) { return $value !== ''; });
foreach($synWordList as $word){
    $synTable .= '  <tr>
                        <td>'.$word.'</td>
                        <td>'.$delSyn.'/'.$addSyn.'</td>
                    </tr>';
}
$synTable .= '  </table>
                    <div id="newSyn" class="hidden">
                        <h2>'.elgg_echo("synonym:new").'</h2><p id="addcounter">1 / 5</p>
                        <div id="addInputList">
                            '.$synAdd.'<br>
                        </div>
                        '.$synAddButt.$synBackButt.$syn_AddAddButt.'
                    </div>
                    <div id="addSyn" class="hidden">
                        <h2>'.elgg_echo("synonym:edit").'</h2><p id="eddcounter">1 / 5</p>
                        <div id="eddInputList">
                            '.$synEdd.'<br>
                        </div>
                        '.$synEddButt.$synBackButt.$syn_AddEddButt.'
                    </div>
              </div>';

//  Popups
$popupStp = elgg_format_element('div', [
    'class' => 'elgg-module-popup hidden',
    'id' => 'popup-stop-open',
], $stopTable);

$popupSyn = elgg_format_element('div', [
    'class' => 'elgg-module-popup hidden',
    'id' => 'popup-syn-open',
], $synTable);


$relevancyTitle = elgg_echo('options:relevancy:title');
$relevancyInfo = elgg_echo('options:relevancy:info');
$optionsTitle = elgg_echo('options:title');

elgg_extend_view('css/admin', 'css/admin/advanced_search');

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
    <td><label>$synonym</label></td>
    <td>$synonym_enable</td>
    <td>$popupSyn $synPopup</td>
  </tr>
  <tr>
    <td><label>$stopwords</label></td>
    <td>$stopwords_enable</td>
    <td>$popupStp $stpPopup</td>
  </tr>
  <tr>
    <td><label>$category</label></td>
    <td>$category_enable</td>
    <td>$category_list <br> $catListHelp <br> $catListHelpWarning</td>
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
  </tr>
  <tr>
    <td><label>$results</label></td>
    <td>$results_enable</td>
    <td>$results_amount <br> $resultsAmountHelp</td>
  </tr>
</table><!--
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
</table>-->

<div class='popup-body'>
</div>

<div class='popup-body'>
</div>
__HTML;

echo $settings;
?>