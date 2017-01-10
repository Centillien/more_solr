<?php
elgg_load_js('jsStyle');

$title = elgg_echo('search:results:title');
// arbitrary file on the filestore
$fileSyn = new ElggFile();
$fileSyn->owner_guid = 7777;
$fileSyn->setFilename('settings/synonym/list.txt');

// option 2
$contents = file_get_contents($fileSyn->getFilenameOnFilestore());

$synonyms = explode(PHP_EOL, $contents);

$search =  array('search' => $_GET['search'],
            'synonym' => $_GET['synonym'],
            'category' => $_GET['category'],
            'tags' => $_GET['tags'],
            'users' => $_GET['user'],
            'results' => $_GET['results'],
            'sort' => $_GET['sort'],
            'date' => $_GET['date'],
            'dateTo' => $_GET['dateTo']);

switch($search['sort']){
    case 'timeon':
            $sort = $userSort = 'time_created ASC';
        break;
    case 'timeno':
            $sort = $userSort = 'time_created DESC';
        break;
    case 'abcaz':
            $sort = 'title ASC';
        break;
    case 'abcza':
            $sort = 'title DESC';
        break;
    case 'popularity':
            $sorter = 'popularity';
        break;
    default:
            $sort = $userSort = 'time_created DESC';
        break;
}
$params = array(
    'type' => 'object',
    'subtype' => ELGG_ENTITIES_ANY_VALUE,
    'order_by' => $sort,
    'limit' => 0,
    'pagination' => true,
);

$userResults = "";

if ($search['category'] != 'all') {
    $params['subtype'] = $search['category'];
} else {
    $userResults = elgg_get_entities(array(
            'types' => 'user',
            'order_by' => $userSort,
            'limit' => 0,)
    );
}
$results = elgg_get_entities($params);

$arr = [];
$pizza  = elgg_get_plugin_setting('sort_list', 'more_solr');
$pieces = explode(",", $pizza);
foreach($pieces as $piece){
    print_r($types['object'][$piece]);
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

    foreach ($userResults as $result) {
    $d_m_y = '';
    $time = elgg_get_friendly_time($result->time_created);
    $timeArr = preg_split("/[\s]+/", $time);
    // If is older than a day, display date instead
    if ($timeArr[1] == 'days' && $timeArr[0] > 1) {
        $time = gmdate("Y-m-d H:i:s", $result->time_created);
        $d_m_y = gmdate("Y-m-d", $result->time_created);
    }

    $timeUpdated = elgg_get_friendly_time($result->last_login);
    $timeUpdatedArr = preg_split("/[\s]+/", $timeUpdated);
    // If is older than a day, display date instead
    if ($timeUpdatedArr[1] == 'days' && $timeUpdatedArr[0] > 1) {
        $timeUpdated = gmdate("Y-m-d H:i:s", $result->last_login);
        $d_m_y = gmdate("Y-m-d", $result->last_login);
    }

    $username = $result->username;
    $name = $result->name;

    // view
    $int = $search['results'] - 1;

    if (ResultsToShow($search, $result, $d_m_y, 'user')) {
        $friendsCount = 0;

        $options = array(
            'relationship' => 'friend',
            'relationship_guid' => $user_guid,
            'inverse_relationship' => FALSE,
            'type' => 'user',
            'full_view' => FALSE
        );
        $friendsCount = count(elgg_get_entities_from_relationship($options));


        $userIcon = elgg_view_entity_icon($result, 'medium');
        $biography = elgg_view('output/longtext', array('value' => $result->description ? $result->description : elgg_echo('no:about'), 'class' => 'mtn'));

        $item = "
                <a href='/profile/" . $username . "'>
                    <div class='head'>
                            <h4>" . $result->name . "</h4>
                            <table>
                                <tr>
                                    <td>
                                        ".$userIcon."<br>
                                        <div class='userStatus'>
                                            ".elgg_echo('results:language').": " . $result->language . "<br> 
                                            Admin: ".$result->admin."<br> 
                                            Friends: ".$friendsCount."<br> 
                                            Banned: ".$result->banned."
                                        </div>
                                    </td>
                                    <td width:100%;>
                                        <div class='biography'>
                                        E-mail: " . $result->email . "<br> <br> 
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
        $arr = array($item, $friendsCount);
        $resultsArray[] = $arr;
    }
}
    foreach ($results as $result) {
        $d_m_y = '';
        $time = elgg_get_friendly_time($result->time_created);
        $timeArr = preg_split("/[\s]+/", $time);
        // If is older than a day, display date instead
        if($timeArr[1] == 'days' && $timeArr[0] > 1){
            $time = gmdate("Y-m-d H:i:s", $result->time_created);
            $d_m_y = gmdate("Y-m-d", $result->time_created);
        }

        $timeUpdated = elgg_get_friendly_time($result->time_updated);
        $timeUpdatedArr = preg_split("/[\s]+/", $timeUpdated);
        // If is older than a day, display date instead
        if($timeUpdatedArr[1] == 'days' && $timeUpdatedArr[0] > 1){
            $timeUpdated = gmdate("Y-m-d H:i:s", $result->time_updated);
            $d_m_y = gmdate("Y-m-d", $result->time_updated);
        }

        $subtype = $result->getSubtype();
        $guid = $result->guid;

        $user = get_user($result->owner_guid);
        $username = $user->username;
        $name = $user->name;

        $description = $result->description;
        $description = strip_tags($description);

        //  Get number of replies
        $num_replies = elgg_get_entities(array(
            'type' => 'object',
            'subtype' => 'discussion_reply',
            'container_guid' => $result->guid,
            'count' => true,
            'distinct' => false,
        ));
        $displaySubtype = $subtype;
        if($subtype == 'discussion'){
            $displaySubtype = $subtype . "<br> Replies: " . $num_replies;
        }
        // view
        $int = $search['results'] - 1;
        //  && $countResults <= $int |||||||||add this to the if for limited results
        if(ResultsToShow($search, $result, $d_m_y, 'object')){
            $item =  "
                <a href='/".$subtype."/view/".$guid."'>
                    <div class='head'>
                            <h4>".$result->title."</h4>
                            <div class='pull-right'>".$displaySubtype."</div>
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
                if($timeUpdated != $time){
                    $item .= elgg_echo('search:results:latest').":".$timeUpdated;
                }
            $item .= "  
                        </div>
                        <div class='info'>\"Owner name\"</div>       
                    </div>
                </a>";
            $arr = array($item, $num_replies);
            $resultsArray[] = $arr;
        }
    }

    function pop($a, $b)
    {
        if ($a[1] == $b[1]) {
            return 0;
        }
        return ($a[1] > $b[1]) ? -1 : 1;
    }
    if($sorter == 'popularity'){
        usort($resultsArray, "pop");
    }

    for($i=0;$i<count($resultsArray);$i++){
        $contentA = "<li class='advancedItem".overResults($i, $search['results'])."' id='".$i."'>";
        $contentA .= $resultsArray[$i][0];
        $contentA .= "</li>";

        $content .= $contentA;
    }

$content .= "
    <div id='noItems'><h3>".elgg_echo('search:results:none')."</h3></div>
</ul>";
$pages = ceil($countResults / $search['results']);
$content .= "<div id='paginationFoot'>";
for($i=0;$i<$pages;$i++){
    $content .= "<a href='#' class='advancedPage'><div class='advancedPagination'>".($i + 1)."</div></a>";
}
$content .= "</div>
</div>";
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

/*
 *  $search contains search query
 *  $results contains current item result
 */
$andArr = [];
$orArr = [];
$notArr = [];
function ResultsToShow (&$search, &$result, $date, $type) {
    $titleReturn    = false;

    $userReturn     = true;
    $dateReturn     = true;

    $search = array_map('strtolower', $search);

    if($type == 'object') {
        $textToSearch = $result->title . " " . $result->description;
        $textToSearch = strtolower($textToSearch);
        $userGuid = $result->owner_guid;
    } elseif($type == 'user'){
        $textToSearch = $result->description . " " . $result->name . " " . $result->email;
        $textToSearch = strtolower($textToSearch);
        $userGuid = $result->guid;
    } else {
        $textToSearch = "";
        $userGuid = "";
    }

    if(boolean($search, $textToSearch)){
        $titleReturn = true;
    }

    $dater = date('Y-m-d', strtotime($date));
    $startDate = $search['date'];
    $endDate = $search['dateTo'];

    if($startDate && $endDate){                             //  If both
        if (($dater >= $startDate) && ($dater <= $endDate)) {
            $dateReturn = true;
        } else {
            $dateReturn = false;
        }
    } elseif ($startDate) {                                 //  If only start
        if($dater >= $startDate){
            $dateReturn = true;
        } else {
            $dateReturn = false;
        }
    } elseif ($endDate) {                                   //  If only end
        if($dater <= $endDate){
            $dateReturn = true;
        } else {
            $dateReturn = false;
        }
    } else {                                                //  If neither
        $dateReturn = true;
    }

    if($search['search'] != ""){
        $searchList = explode(" ", $search['search']);
        $stopwords = getStops();

        $filterSearch = array_diff($searchList, $stopwords);
        $i = array_intersect($filterSearch, explode(" ", preg_replace("/[^A-Za-z0-9' -]/", "", $textToSearch)));

        if(count($i) == count($filterSearch)){
            $titleReturn = true;
        }
    }
    if($search['users'] != ""){
        $userIdArr = explode(":", $search['users']);
        $size = count($userIdArr) - 1;

        if($userGuid != $userIdArr[$size])
        {
            $userReturn = false;
        }
    }

    //  Synonym search
    if($search['synonym'] == 'yes'){
        $searchList = explode(" ", $search['search']);
        $synonyms = "";
        foreach($searchList as $i) {
            $synonyms .= synonymSearch($i);
        }
        $synonyms = explode(",", $synonyms);
        $synonyms = array_diff($synonyms, [$search['search']]);

        foreach ($synonyms as $synonym){
            if(stripos($textToSearch, $synonym) !== false){
                $titleReturn = true;
            }
        }
    }

    //  Every return has been set into a var in preparation for relevancy system
    if($userReturn && $titleReturn && $dateReturn){
        return true;
    } else {
        return false;
    }
}
// Boolean search refers to the AND/NOT/OR tags in the tags search box
function boolean(&$search, $textToSearch) {
    // Collect all boolean values and split
    $allArr = preg_split("/(?= AND)|(?= OR)|(?= NOT)/", $search['tags'], null, PREG_SPLIT_DELIM_CAPTURE);

    $andArr = [];
    $orArr = [];
    $notArr = [];
    foreach($allArr as $all){
        if(strpos($all, 'NOT')){
            $all = str_replace('NOT ', '', $all);
            array_push($notArr, $all);
        } elseif (strpos($all, 'OR')){
            $all = str_replace('OR ', '', $all);
            array_push($orArr, $all);
        } else {
            $all = str_replace('AND ', '', $all);
            array_push($andArr, $all);
        }
    }
    //  First test AND(because the main is AND) Then test NOT(else it will show everything that is NOT)
    if(strposAnd($textToSearch, $andArr) !== false){
        if(count($notArr) != null){
            foreach($notArr as $nots){  // Revenge of the nöts(nuts)
                if(stripos($textToSearch, $nots) == false){
                    return true;
                } else {
                    return false;
                }
            }
        } else {
            return true;
        }
    }

    //  Test OR seperately because everything containing OR should be displayed.
    if(count($orArr) != null) {
        if (strposOr($textToSearch, $orArr) !== false) {
            return true;
        }
    }
}

function strposOr($haystack, $needles=array(), $offset=0) {
    $chr = array();
    foreach($needles as $needle) {
        $res = stripos($haystack, $needle, $offset);
        if ($res !== false) $chr[$needle] = $res;
    }
    if(empty($chr)) return false;
    return min($chr);
}

function strposAnd($haystack, $needles=array(), $offset=0) {
    $validResult = true;
    foreach($needles as $needle) {
        $res = stripos($haystack, $needle, $offset);
        if ($res !== false) {
            $validResult = true;
        }
        else {
            return false;
        }
    }
    return $validResult;
}

function synonymSearch ($search){
    $synonyms = getSyns();
    $string = $search; // insert code here
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
// arbitrary file on the filestore
    $fileStp = new ElggFile();
    $fileStp->owner_guid = 7777;
    $fileStp->setFilename('settings/stopword/list.txt');

    $contents = file_get_contents($fileStp->getFilenameOnFilestore());

    return explode(PHP_EOL, $contents);
}

function getSyns(){
// arbitrary file on the filestore
    $fileStp = new ElggFile();
    $fileStp->owner_guid = 7777;
    $fileStp->setFilename('settings/synonym/list.txt');

    $contents = file_get_contents($fileStp->getFilenameOnFilestore());

    return explode(PHP_EOL, $contents);
}