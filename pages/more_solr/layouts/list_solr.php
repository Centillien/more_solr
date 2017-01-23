<?php
elgg_load_js('jsStyle');

//  Set the title of the results page
$title = elgg_echo('search:results:title');

//  Get the synonyms file
$fileSyn = new ElggFile();
$fileSyn->owner_guid = 7777;
$fileSyn->setFilename('settings/synonym/list.txt');

$contents = file_get_contents($fileSyn->getFilenameOnFilestore());

$synonyms = explode(PHP_EOL, $contents);

//  Get the search limiters from the url
$search =  array('search' => $_GET['search'],
    'synonym' => $_GET['synonym'],
    'category' => $_GET['category'],
    'tags' => $_GET['tags'],
    'users' => $_GET['user'],
    'results' => $_GET['results'],
    'sort' => $_GET['sort'],
    'date' => $_GET['date'],
    'dateTo' => $_GET['dateTo'],
    'page' => $_GET['page']);

$search['search'] = strtolower($search['search']);
$search['tags'] = strtolower($search['tags']);
//  Decide which sort to use in query
switch($search['sort']){
    case 'timeon':
        $sort = 'time_created ASC, title ASC, name ASC, description ASC';
        break;
    case 'timeno':
        $sort = 'time_created DESC, title ASC, name ASC, description ASC';
        break;
    case 'abcaz':
        $sort = 'title ASC, name ASC, description ASC';
        break;
    case 'abcza':
        $sort = 'title DESC, name DESC, description DESC';
        break;
    case 'popularity':
        $sort = 'score DESC, time_created ASC';
        break;
    default:
        $sort = 'time_created DESC';
        break;
}

//  Start of retrieving results
//
$client = elgg_solr_get_client();

//  Get a select query instance
$query = $client->createQuery($client::QUERY_SELECT);

//  Set limit on rows
$startRes = $search['page'] * $search['results'] - $search['results'];
$endRes = $search['page'] * $search['results'];
$query->setStart($startRes)->setRows($endRes);
$query->addSort($sort);

$multiQuery = null;

//  Remove all stopwords from search query
if($search['search'] != ""){
    $searchList = explode(" ", $search['search']);
    $stopwords = getStops();

    $filterSearch = array_diff($searchList, $stopwords);
    $search['search'] = implode(" ", $filterSearch);
}

//  Set the date range of the results
if($search['date']){
    $date = new DateTime($search['date']);
    $dateFrom = $date->getTimestamp();
} else {
    $dateFrom = "*";
}

if($search['dateTo']){
    $date = new DateTime($search['dateTo']);
    $dateTo = $date->getTimestamp();

    if($search['date'] == $search['dateTo']){
        $dateTo += 86400;
    }
} else {
    $dateTo = "*";
}


$multiQuery = "((time_created:[".$dateFrom." TO ".$dateTo."]) AND ";

$multiQuery .= "(";
//  Create base search query
$multiQuery .= "title:*".$search['search'] . "*^3.0 OR " . "name:*".$search['search'] . "*^2.8 OR " . "description:*".$search['search']."*^2.5";

//  Search for all synonyms
if($search['synonym'] == 'yes'){
    $searchList = explode(" ", $search['search']);
    $synonyms = "";
    foreach($searchList as $i) {
        $synonyms .= synonymSearch($i);
    }
    $synonyms = explode(",", $synonyms);
    $synonyms = array_diff($synonyms, [$search['search']]);
    foreach ($synonyms as $synonym){
        //  Add synonym queries
        if($synonym){
            $multiQuery .= " OR title:*".$synonym . "*^2.0 OR " . "name:*".$synonym . "*^1.8 OR " . "description:*".$synonym."*^1.5";
        }
    }
}
$multiQuery .= ")";

if($search['tags']){
    $firstLimiter = substr($search['tags'], 0, 3);      //  Check first 3 characters if user added an "and, or, not" at start of string
    if($firstLimiter == "and" || $firstLimiter == "or " || $firstLimiter == "not") {
        $search['tags'] = $search['search'] . " " . $search['tags'];
    }

    // Collect all boolean values and split to define which limiter every word has, first word always has AND
    $allArr = preg_split("/(?= and)|(?= or)|(?= not)/", $search['tags'], null, PREG_SPLIT_DELIM_CAPTURE);

    $andArr = [];
    $orArr = [];
    $notArr = [];
    foreach($allArr as $all){
        if(strpos($all, 'not')){
            $all = str_replace('not ', '', $all);
            $all = str_replace(' ', '', $all);
            array_push($notArr, $all);
        } elseif (strpos($all, 'or')){
            $all = str_replace('or ', '', $all);
            $all = str_replace(' ', '', $all);
            array_push($orArr, $all);
        } else {
            $all = str_replace('and ', '', $all);
            $all = str_replace(' ', '', $all);
            array_push($andArr, $all);
        }
    }

    foreach($andArr as $ands){
        $multiQuery .= " AND (title:*".$ands . "* OR " . "name:*".$ands . "* OR " . "description:*".$ands."*)";
    }
    foreach($orArr as $ors){
        $multiQuery .= " OR (title:*".$ors . "* OR " . "name:*".$ors . "* OR " . "description:*".$ors."*)";
    }
    foreach($notArr as $nots){
        $multiQuery .= " AND -(title:*".$nots . "* OR " . "name:*".$nots . "* OR " . "description:*".$nots."*)";
    }
}

$multiQuery .= ")";

if($search['users']){
    if($search['users'] != ""){
        $userIdArr = explode(":", $search['users']);
        $size = count($userIdArr) - 1;

        if(is_numeric($userIdArr[$size])){
            $multiQuery .= " AND (owner_guid:" . $userIdArr[$size]." OR id:".$userIdArr[$size].")";
        }
    }
}

$pizza  = elgg_get_plugin_setting('cate_en', 'more_solr');
if($pizza != 'no'){
    $pizza  = elgg_get_plugin_setting('category_groups', 'more_solr');
    $pizza ? $groupListValue = explode("[",$pizza) : $groupListValue = elgg_echo('option:all');

    $groupnamelist = [];
    $catGroupList = [];
    foreach ($groupListValue as $value) {
        $value = explode(",", $value);
        if ($value[0]) {
            $groupnamelist[] .= $value[0];
        }
        $catGroupList[] = $value;
    }
    $categoriesGroups = array_merge(['all', 'group', 'user'], $groupnamelist);

    $id = $search['category'];
    //  Add limiter on types(object, user, group)/subtypes(discussion)
    if($categoriesGroups[$id] != 'all' && $categoriesGroups[$id]){
        if($categoriesGroups[$id] == 'user' || $categoriesGroups[$id] == 'group' ){
            $multiQuery .= " AND (type:".$categoriesGroups[$id].")";
        } else {
            foreach($catGroupList as $group){
                if($group[0] == $categoriesGroups[$id]){
                    foreach($group as $category){
                        if($category != $categoriesGroups[$id]){
                            $category = str_replace("]", "", $category);
                            $categoriesArray[] = $category;
                        }
                    }
                }
            }

            $multiQuery .= " AND (";
            $count = 0;
            foreach($categoriesArray as $word){
                $multiQuery .= $count == 0 ? '' : " OR ";
                if($word == 'user' || $word == 'group'){
                    $multiQuery .= "type:".$word;
                } else {
                    $multiQuery .= "subtype:".$word;
                }
                $count++;
            }
            $multiQuery .= ")";
        }
    }
} else {
    //  Add limiter on types(object, user, group)/subtypes(discussion)
    if($search['category'] != 'all' && $search['category']){
        if($search['category'] == 'user' || $search['category'] == 'group' ){
            $multiQuery .= " AND (type:".$search['category'].")";
        } else {
            $multiQuery .= " AND (subtype:".$search['category'].")";
        }
    }
}



$query->setFields(array('id','type','subtype', 'owner_guid', 'container_guid', 'title', 'name', 'description', 'time_created', 'time_updated_i', 'groups_is', 'members_is', 'username', 'score'));

$query->setQuery($multiQuery);

// This executes the query and returns the result
$resultset = $client->select($query);
// Display the total number of documents found by solr
$totalResults = $resultset->getNumFound();

// Show documents using the resultset iterator
$i = 0;
foreach ($resultset as $document) {
    $result['id'] = $document->id;
    $result['container'] = $document->container_guid;
    $result['type'] = $document->type;
    $result['subtype'] = $document->subtype;
    if($result['type'] == 'user'){
        $result['owner'] = $document->id;
    } else {
        $result['owner'] = $document->owner_guid;
    }
    $result['title'] = $document->title;
    $result['name'] = $document->name;
    $result['description'] = $document->description;
    $result['time_created'] = $document->time_created;
    $result['time_updated_i'] = $document->time_updated_i;
    $result['groups'] = count($document->groups_is);
    $result['members'] = count($document->members_is);
    $result['username'] = $document->username;

    $types[$i] = $result['type'];
    $subtypes[$i] = $result['subtype'];
    $results[$i] = $result;                 //  $results are the results from the query
    $i++;
}
$types = array_merge($types, $subtypes);
$types = array_unique($types);

//
//  End of retrieving results

$arr = [];
$pizza  = elgg_get_plugin_setting('sort_list', 'more_solr');
$pieces = explode(",", $pizza);
foreach($pieces as $piece){
    $arr["$piece"] = elgg_echo('option:'.$piece);
}
$sort = elgg_echo('options:sort');
$sort_bar = elgg_view('input/select', array(
    'name' => 'sort',
    'id' => 'sortDrop',
    'value' => $search['sort'],
    'options_values' => $arr,
));

// navigation/pagination can add pagination to page
$content = "<h1 class='sortOptions'>$title</h1><div class='sortOptions'>$sort $sort_bar</div><div id='advancedResults'>";
$content .= '<div id="paginationHead"></div><ul class="elgg-list advancedResults">';

$resultsArray[] = "";
foreach ($results as $result) {
    if($result['type'] == 'user'){
        $d_m_y = '';
        $time = elgg_get_friendly_time($result['time_created']);
        $timeArr = preg_split("/[\s]+/", $time);
        // If is older than a day, display date instead
        if ($timeArr[1] == 'days' && $timeArr[0] > 1) {
            $time = gmdate("Y-m-d H:i:s", $result['time_created']);
            $d_m_y = gmdate("Y-m-d", $result['time_created']);
        }

        $timeUpdated = elgg_get_friendly_time($result['time_updated_i']);
        $timeUpdatedArr = preg_split("/[\s]+/", $timeUpdated);
        // If is older than a day, display date instead
        if ($timeUpdatedArr[1] == 'days' && $timeUpdatedArr[0] > 1) {
            $timeUpdated = gmdate("Y-m-d H:i:s", $result['time_updated_i']);
            $d_m_y = gmdate("Y-m-d", $result['time_updated_i']);
        }

        $username = $result['username'];
        $name = $result['name'];

        $friendsCount = 0;

        $options = array(
            'relationship' => 'friend',
            'relationship_guid' => $result['id'],
            'inverse_relationship' => FALSE,
            'type' => 'user',
            'full_view' => FALSE
        );
        $friendsCount = count(elgg_get_entities_from_relationship($options));

        $userIcon = elgg_view_entity_icon(get_user($result['id']) ? get_user($result['id']) : get_user(99), 'medium');

        $biography = elgg_view('output/longtext', array('value' => $result['description'] ? $result['description'] : elgg_echo('no:about'), 'class' => 'mtn'));

        $item = "
                <a href='/profile/" . $username . "'>
                    <div class='head'>
                            <h4>" . $result['name'] . "</h4>
                            <table>
                                <tr>
                                    <td>
                                        ".$userIcon."<br>
                                        <div class='userStatus'>
                                            Friends: ".$friendsCount."<br>
                                            Groups: ".$result['groups']."<br>
                                        </div>
                                    </td>
                                    <div class='pull-right'>User</div>
                                    <td width:100%;>
                                        <div class='biography'>
                                            ".$biography."
                                        </div>
                                    </td>
                                </tr>
                            </table>
                    </div>
                </a>
                <a href='/profile/" . $username . "'>
                    <div class='foot'>
                        <div class='one'>" . $name . "</div>
                        <div class='two'>
                            " . elgg_echo('search:results:created') . ":" . $time . "
                        </div>
                        <div class='four'>";
        if ($timeUpdated != $time && $result->last_login != 0) {
            $item .= elgg_echo('search:results:latest:login') . ":" . $timeUpdated;
        }
        $item .= "  
                        </div>
                        <div class='info'>\"Owner name\"</div>       
                    </div>  
                </a>";
        $arr = array($item, $friendsCount + $result['groups'], $result['score']);
        $resultsArray[] = $arr;
    }
    elseif ($result['type'] == 'group') {
        $d_m_y = '';
        $time = elgg_get_friendly_time($result['time_created']);
        $timeArr = preg_split("/[\s]+/", $time);
        // If is older than a day, display date instead
        if($timeArr[1] == 'days' && $timeArr[0] > 1){
            $time = gmdate("Y-m-d H:i:s", $result['time_created']);
            $d_m_y = gmdate("Y-m-d", $result['time_created']);
        }

        if($result['time_updated_i']) {
            $timeUpdated = elgg_get_friendly_time($result['time_updated_i']);
            $timeUpdatedArr = preg_split("/[\s]+/", $timeUpdated);
            // If is older than a day, display date instead
            if ($timeUpdatedArr[1] == 'days' && $timeUpdatedArr[0] > 1) {
                $timeUpdated = gmdate("Y-m-d H:i:s", $result['time_updated_i']);
                $d_m_y = gmdate("Y-m-d", $result['time_updated_i']);
            }
        }

        $subtype = $result['type'];
        $guid = $result['id'];

        $client = elgg_solr_get_client();
        $query = $client->createQuery($client::QUERY_SELECT);

        $query->setFields(array('username, name'));
        $query->setQuery('id:'.$result['owner'].' AND type:user');

        $resultset = $client->select($query);
        foreach ($resultset as $document) {
            $userResult['name'] = $document->name;
            $userResult['username'] = $document->username;
        }
        $username = $userResult['username'];
        $name = $userResult['name'];

        if($result['description']){
            $description = $result['description'];
            $description = strip_tags($description);
        } else {
            $description = elgg_echo('no:description');
        }
        $num_members = 0;
        $group = get_entity($result['id']);

        $base = '';
        $url = $base;
        $result['title'] ? $itemTitle = $result['title'] : $itemTitle = $result['name'];
        $item =  "
            <a href='".$url."/groups/profile/".$guid."/".$result['name']."'>
                <div class='head'>
                        <h4>".$itemTitle."</h4>
                        <div class='pull-right'>".$subtype."</div>
                        <div class='desc'><p>".$description."</p></div>
                </div>
            </a>
            <a href='/profile/".$username."'>
                <div class='foot'>
                    <div class='one'>".$name."</div>
                    <div class='two'>
                        ".elgg_echo('search:results:created').":".$time."
                    </div>
                    <div class='four'>";
        if($timeUpdated && $timeUpdated != $time){
            $item .= elgg_echo('search:results:latest').":".$timeUpdated;
        }
        $item .= "  
                    </div>
                    <div class='info'>\"Owner name\"</div>       
                </div>
            </a>";
        $arr = array($item);
        $resultsArray[] = $arr;
    }
    else {
        $d_m_y = '';
        $time = elgg_get_friendly_time($result['time_created']);
        $timeArr = preg_split("/[\s]+/", $time);
        // If is older than a day, display date instead
        if($timeArr[1] == 'days' && $timeArr[0] > 1){
            $time = gmdate("Y-m-d H:i:s", $result['time_created']);
            $d_m_y = gmdate("Y-m-d", $result['time_created']);
        }
        if($result['time_updated_i']){
            $timeUpdated = elgg_get_friendly_time($result['time_updated_i']);
            $timeUpdatedArr = preg_split("/[\s]+/", $timeUpdated);
            // If is older than a day, display date instead
            if($timeUpdatedArr[1] == 'days' && $timeUpdatedArr[0] > 1){
                $timeUpdated = gmdate("Y-m-d H:i:s", $result['time_updated_i']);
                $d_m_y = gmdate("Y-m-d", $result['time_updated_i']);
            }
        } else {
            $timeUpdated = "";
        }

        $subtype = $result['subtype'];
        $guid = $result['id'];

        $client = elgg_solr_get_client();
        $query = $client->createQuery($client::QUERY_SELECT);

        $query->setFields(array('username, name'));
        $query->setQuery('id:'.$result['owner'].' AND type:user');

        $resultset = $client->select($query);
        foreach ($resultset as $document) {
            $userResult['name'] = $document->name;
            $userResult['username'] = $document->username;
        }
        $username = $userResult['username'];
        $name = $userResult['name'];

        $description = $result['description'];
        $description = strip_tags($description);

        if($result['type'] != 'annotation'){
            $client = elgg_solr_get_client();
            $query = $client->createQuery($client::QUERY_SELECT);

            $query->setFields(array('score'));
            $query->setQuery('(subtype:comment) AND container_guid:'.$result['id']);
            $query->setStart(0)->setRows(50);
            $resultset = $client->select($query);
            $num_replies = 0;

            $num_replies = count($resultset);
        } else {
            $displaySubtype = $subtype;
        }

        $displaySubtype = $subtype;
        $base = '';
        $url = $base;
        $elementLink = "<a ";

        switch($subtype){
            case 'discussion': case 'groupforumtopic':
            $displaySubtype = elgg_echo('type:discussion')."<br> ".elgg_echo('type:replies').": " . $num_replies;
            $url .= "/discussion/view/".$guid;
            break;
            case 'comment': case 'generic_comment':
            $container = $result['container'];
            $url .= '/comment/view/'.$guid.'/'.$container;
            break;
            case 'album': case 'image':
            $displaySubtype = elgg_echo('type:image')."<br> ".elgg_echo('type:replies').": " . $num_replies;
            $url .= "/photos/".$subtype."/".$guid;
            break;
            case 'webdav_file':
                $displaySubtype = elgg_echo('type:file')."<br> ".elgg_echo('type:replies').": " . $num_replies;
                $url .= '/webdav/view/?container_guid=1&p='.$result['title'];
                break;
            case 'file':
                $displaySubtype = elgg_echo('type:file')."<br> ".elgg_echo('type:replies').": " . $num_replies;
                $url .= '/file/view/'.$result['id'];
                break;
            case 'page': case 'page_top':
            $displaySubtype = elgg_echo('type:page')."<br> ".elgg_echo('type:replies').": " . $num_replies;
            $url .= '/pages/view/'.$guid;
            break;
            case 'hjwall':
                $displaySubtype = elgg_echo('type:wallpost')."<br> ".elgg_echo('type:replies').": " . $num_replies;

                $client = elgg_solr_get_client();
                $query = $client->createQuery($client::QUERY_SELECT);

                $query->setFields(array('username'));
                $query->setQuery('id:'.$result['owner'].' AND type:user');

                $resultset = $client->select($query);
                foreach ($resultset as $document) {
                    $usernameUrl = $document->username;
                }

                $url .= '/wall/owner/'.$usernameUrl.'/'.$guid;
                break;
            case 'event':
                $displaySubtype = elgg_echo('type:event')."<br> ".elgg_echo('type:replies').": " . $num_replies;
                $url .= '/events/event/view/'.$guid;
                break;
            case 'task_top': case 'task':
            $displaySubtype = elgg_echo('type:task')."<br> ".elgg_echo('type:replies').": " . $num_replies;
            $url .= '/tasks/view/'.$guid;
            break;
            case 'faq':
                $displaySubtype = elgg_echo('type:faq')."<br> ".elgg_echo('type:replies').": " . $num_replies;
                $url .= '/user_support/faq/'.$guid;
                break;
            default:
                $displaySubtype = $subtype." <br> Replies: " . $num_replies;
                $url .= "/".$subtype."/view/".$guid;
                //$elementLink .= "class='resultItemLink' ";
                break;
        }

        $result['title'] ? $itemTitle = $result['title'] : $result['name'];
        $item =  $elementLink."href='".$url."'>
                <div class='head'>
                        <h4>".$result['title']."</h4>
                        <div class='pull-right subtype'>".$displaySubtype."</div>
                        <div class='desc'><p>".$description."</p></div>
                </div>
            </a>
            <a href='".$base."/profile/".$username."'>
                <div class='foot'>
                    <div class='one'>".$name."</div>
                    <div class='two'>
                        ".elgg_echo('search:results:created').":".$time."
                    </div>
                    <div class='four'>";
        if($timeUpdated && $timeUpdated != $time){
            $item .= elgg_echo('search:results:latest').":".$timeUpdated;
        }
        $item .= "  
                    </div>
                    <div class='info'>\"Owner name\"</div>       
                </div>
            </a>";
        $arr = array($item);
        $resultsArray[] = $arr;
    }
}

for($i=0;$i<count($resultsArray);$i++){
    $contentA = "<li class='advancedItem".overResults($i, $search['results'])."' id='".$i."'>";
    $contentA .= $resultsArray[$i][0];
    $contentA .= "</li>";

    $content .= $contentA;
}

//  Show "no items found" when that's the case
$content .= "
    <div id='noItems'><h3>".elgg_echo('search:results:none')."</h3></div>
</ul>";
//  Get amount of pages by total items / results per page
$pages = ceil($totalResults / $search['results']);
//  Display the pagination
$content .= "<div id='paginationFoot'>";
if($search['page'] != 1){
    $content .= "<a href='#' class='advancedPage'><div class='advancedPagination'>1</div></a>";
    $content .= "<a href='#' class='advancedPage'><div class='advancedPagination'>&lt;</div></a>";
}
$negCounter = $search['page'] - 4;
while($negCounter < ($search['page'] + 3)){
    $negCounter++;
    if($negCounter < $search['page'] && $negCounter > 0){
        $content .= "<a href='#' class='advancedPage'><div class='advancedPagination'>".($negCounter)."</div></a>";
    }
    elseif($negCounter == $search['page']){
        $content .= "<a href='#' id='currentPage' class='currentPage'><div class='currentPagination'>".($search['page'])."</div></a>";
    }
    elseif($negCounter > $search['page'] && $negCounter <= $pages){
        $content .= "<a href='#' class='advancedPage'><div class='advancedPagination'>".($negCounter)."</div></a>";
    }
}
if($search['page'] != $pages){
    $content .= "<a href='#' class='advancedPage'><div class='advancedPagination'>&gt;</div></a>";
    $content .= "<a href='#' class='advancedPage'><div class='advancedPagination'>".$pages."</div></a>";
}
$content .= "</div>
</div>";

//  Display page
$params = array(
    'content' => $content,
    'sidebar' => $sidebar,
);

$body = elgg_view_layout('one_sidebar', $params);

echo elgg_view_page($title, $body);

elgg_require_js('resultHandler');

/*
 * $count counts amount of results
 */
function overResults ($count, $resAm) {
    if($count > $resAm){
        return ' hidden';
    }
}

function synonymSearch ($search){
    $synonyms = getSyns();
    $string = $search;
    $index = -1;
    for ($i=0;$i<count($synonyms);$i++) {
        if (strpos($synonyms[$i], $string) !== false) {
            $index = $i;
        }
    }
    $index = $synonyms[$index];
    return $index;
}

function getStops(){
    $fileStp = new ElggFile();
    $fileStp->owner_guid = 7777;
    $fileStp->setFilename('settings/stopword/list.txt');

    $contents = file_get_contents($fileStp->getFilenameOnFilestore());

    return explode(PHP_EOL, $contents);
}

function getSyns(){
    $fileStp = new ElggFile();
    $fileStp->owner_guid = 7777;
    $fileStp->setFilename('settings/synonym/list.txt');

    $contents = file_get_contents($fileStp->getFilenameOnFilestore());

    return explode(PHP_EOL, $contents);
}