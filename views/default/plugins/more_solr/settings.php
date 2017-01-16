<?php
/**
 * advanced search plugin settings
 */

elgg_load_js('admin_settings');
/** Get stopwords and synonyms file */

//  Get the list of stopwords
$fileStp = new ElggFile();
$fileStp->owner_guid = 7777;
$fileStp->setFilename('settings/stopword/list.txt');

$contents = file_get_contents($fileStp->getFilenameOnFilestore());

$contents = strtolower($contents);
$stopWordList = explode(PHP_EOL, $contents);

//  Get the list of synonyms
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

$adminOnly = elgg_echo('options:admin:only');
$adminOnly_enable = elgg_view('input/select', array(
    'name' => 'params[admin_only]',
    'options_values' => array(
        'yes' => elgg_echo('option:yes'),
        'no' => elgg_echo('option:no'),
    ),
    'value' => $vars['entity']->admin_only ? $vars['entity']->admin_only : elgg_echo('option:no'),
));

//  Enable/disable synonyms
$synonym = elgg_echo('options:synonym');
$synonym_enable = elgg_view('input/select', array(
    'name' => 'params[syn_en]',
    'options_values' => array(
        'yes' => elgg_echo('option:yes'),
        'no' => elgg_echo('option:no'),
    ),
    'value' => $vars['entity']->syn_en ? $vars['entity']->syn_en : elgg_echo('option:no'),
));

//  Enable/disable stopwords
$stopwords = elgg_echo('options:stop');
$stopwords_enable = elgg_view('input/select', array(
    'name' => 'params[stp_en]',
    'options_values' => array(
        'yes' => elgg_echo('option:yes'),
        'no' => elgg_echo('option:no'),
    ),
    'value' => $vars['entity']->stp_en ? $vars['entity']->stp_en : elgg_echo('option:no'),
));

//  Enable/disable categories
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

//  Get the list of category groups

//  Get all the different subtypes
$client = elgg_solr_get_client();
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

$categories = [];
$categories['all'] = elgg_echo('option:all');
foreach($types as $type){
    $categories["$type"] = $type;
}

$vars['entity']->category_groups ? $groupListValue = explode("[",$vars['entity']->category_groups) : $groupListValue = elgg_echo('option:all');
$groupnamelist = [];
$catGroupsList = null;
foreach ($groupListValue as $value){
    $value = explode(",", $value);
    if($value[0]){
        $groupnamelist[] .= $value[0];
    }
    $catGroupsList = $value;
}

$categoriesGroups = array_merge(['all', 'group', 'user'], $groupnamelist);
$category_list = elgg_view('input/select', array(
    'name' => 'params[cat_list]',
    'options_values' => $vars['entity']->cate_en != 'no' ? $categoriesGroups : $categories,
    'value' => $catListValue,
    'multiple' => 'multiple',
));
$catListHelp = elgg_echo('options:cat:list:help');
$catListHelpWarning = elgg_echo('options:cat:list:help:warning');

//  Enable/disable sort
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

//  Sort options
$sorter = array(
    //'popularity' => elgg_echo('option:popularity'),     // TODO: implement popularity
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

//  Set the 'default' of sort
$sort_default = elgg_view('input/select', array(
    'name' => 'params[sort_def]',
    'options_values' => $sorter,
    'value' => $vars['entity']->sort_def ? $vars['entity']->sort_def : 'timeno',
));
$sortDefHelp = elgg_echo('options:sort:list:default:help');

//  Boolean search
$tags = elgg_echo('options:tags');
$tags_enable = elgg_view('input/select', array(
    'name' => 'params[tags_en]',
    'options_values' => array(
        'yes' => elgg_echo('option:yes'),
        'no' => elgg_echo('option:no'),
    ),
    'value' => $vars['entity']->tags_en ? $vars['entity']->tags_en : elgg_echo('option:no'),
));

//  Stopwoorden (de het een)
$user = elgg_echo('options:user');
$user_enable = elgg_view('input/select', array(
    'name' => 'params[user_en]',
    'options_values' => array(
        'yes' => elgg_echo('option:yes'),
        'no' => elgg_echo('option:no'),
    ),
    'value' => $vars['entity']->user_en ? $vars['entity']->user_en : elgg_echo('option:no'),
));

//  Date
$date = elgg_echo('options:date');
$date_enable = elgg_view('input/select', array(
    'name' => 'params[date_en]',
    'options_values' => array(
        'yes' => elgg_echo('option:yes'),
        'no' => elgg_echo('option:no'),
    ),
    'value' => $vars['entity']->date_en ? $vars['entity']->date_en : elgg_echo('option:no'),
));

//  Amount of results
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

$optionsTitle = elgg_echo('options:title');
$categoriesTitle = elgg_echo('categories:title');

$cate = elgg_echo('options:category:groups:enable');
$cate_enable = elgg_view('input/select', array(
    'name' => 'params[cate_en]',
    'options_values' => array(
        'yes' => elgg_echo('option:yes'),
        'no' => elgg_echo('option:no'),
    ),
    'value' => $vars['entity']->cate_en ? $vars['entity']->cate_en : elgg_echo('option:no'),
));
$cate_hint = elgg_echo('options:category:groups:enable:hint');

//  Searchfield for categories  (add autofill from categories list)
$categoriesHidden = elgg_view('input/text', array(
    'id' => 'categories',
    'class' => 'hidden',
    'value' => $categories,
));

$vars['entity']->category_groups ? $catListValue = $vars['entity']->category_groups : $catListValue = '';
//  This is where the array of groups with their categories will be
$categorieGroupHidden = elgg_view('input/text', array(
    'name' => 'params[category_groups]',
    'id' => 'categoryGroups',
    'class' => 'hidden',
    'value' => $catListValue
));

$cateSearchLabel = elgg_echo('options:category:search');
$cateSearch = elgg_view('input/text', array(
    'id' => 'categoryInput',
    'class' => 'elgg-input-thin',
    'placeholder' => elgg_echo('options:category:search:placeholder'),
));

//  Set the groupname of the category list
$cateGroupnameLabel = elgg_echo('options:category:group:name');
$cateGroupname = elgg_view('input/text', array(
    'id' => 'groupName',
    'class' => 'elgg-input-thin category-group-name',
    'placeholder' => elgg_echo('options:category:group:name:placeholder'),
));

//  Get a list of categories that are in said group
$categoriesInGroupLabel = elgg_echo('options:category:group:categories');
$categoriesInGroup = elgg_view('input/longtext', array(
    'id' => 'groupsCategories',
    'disabled' => 'disabled',
    'class' => 'elgg-input-thin category-list',
    'placeholder' => elgg_echo('options:category:group:categories:placeholder'),
));

//  Get the list of categories
$categoriesDisplayListLabel = elgg_echo('options:category:categories:list');
$categoriesDisplayList = elgg_view('input/select', array(
    'options_values' => $categories,
    'id' => 'categoriesDisplayList'
));

$cateGroupListLabel = elgg_echo('options:category:group:list');
$cateGroupList = elgg_view('input/select', array(
    'id' => "groupSelect",
    'options_values' => $groupnamelist, //  Get the list of cat groups here
));

$cateSearchAdd = elgg_view('input/button', array(
    'id' => 'addCate',
    'value' => elgg_echo('option:save:category:Add'),
    'class' => 'elgg-button-submit elgg-button',
));

$cateSearchRemove = elgg_view('input/button', array(
    'id' => 'removeCate',
    'value' => elgg_echo('option:save:category:Remove'),
    'class' => 'elgg-button-submit elgg-button',
));

$saveCategoryGroup = elgg_view('input/button', array(
    'id' => 'saveGroupCate',
    'value' => elgg_echo('option:save:category:group'),
    'class' => 'elgg-button-submit elgg-button',
));

$deleteCategoryGroup = elgg_view('input/button', array(
    'id' => 'deleteGroupCate',
    'value' => elgg_echo('option:delete:category:group'),
    'class' => 'elgg-button-submit elgg-button',
));

$clearCategories = elgg_view('input/button', array(
    'id' => 'clearCate',
    'value' => elgg_echo('option:clear:category'),
    'class' => 'elgg-button-submit elgg-button',
));

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
    <td><label>$adminOnly</label></td>
    <td>$adminOnly_enable</td>
  </tr>
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
</table>
$formSubmit

    <h1>$categoriesTitle</h1>
<table>
$categoriesHidden
$categorieGroupHidden
    <tr>
        <td> $cate </td>
        <td> $cate_enable </td>
    </tr>
    <tr>
        <td></td>
        <td> $cate_hint </td>
    </tr>
    <tr>
        <td> $cateGroupListLabel </td>
        <td> $cateGroupList </td>
    </tr>
    <tr>
        <td> $categoriesDisplayListLabel </td>
        <td> $categoriesDisplayList </td>
    </tr>
    <tr>
        <td> $cateSearchLabel </td>
        <td> $cateSearch </td>
    </tr>
    <tr>
        <td></td>
        <td> $cateSearchAdd $cateSearchRemove </td>
    </tr>
    <tr>
        <td> $cateGroupnameLabel </td>
        <td> $cateGroupname </td>
    </tr>
    <tr>
        <td> $categoriesInGroupLabel </td>
        <td> $categoriesInGroup </td>
    </tr>
    <tr>
        <td> $saveCategoryGroup </td>
        <td> $deleteCategoryGroup $clearCategories</td>
    </tr>
</table>
__HTML;

echo $settings;