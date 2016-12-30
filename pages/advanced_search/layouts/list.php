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
            'dateSets' => $_GET['dateSets']);

$countResults = 0;
switch($search['sort']){
    case 'timeon':
            $sort = 'time_created ASC';
        break;
    case 'timeno':
            $sort = 'time_created DESC';
        break;
    case 'abcaz':
            $sort = 'title ASC';
        break;
    case 'abcza':
            $sort = 'title DESC';
        break;
    default:
            $sort = 'time_created DESC';
        break;
}
// TODO:BUG'cannot cat search without setting a search term'
$params = array(
    'type' => 'object',
    'subtype' => ELGG_ENTITIES_ANY_VALUE,
    'order_by' => $sort,
    'limit' => 0,
    'pagination' => true,
);
if ($search['category'] != 'all') {
    $params['subtype'] = $search['category'];
}
$results = elgg_get_entities($params);

$sort = elgg_echo('options:sort');
$sort_bar = elgg_view('input/select', array(
    'name' => 'sort',
    'id' => 'sortDrop',
    'options_values' => array(
        'timeno' => elgg_echo('option:timeno'), // Time new - old
        'timeon' => elgg_echo('option:timeon'), // Time old - new
        'abcaz' => elgg_echo('option:abcaz'),   // Alphabet A - Z
        'abcza' => elgg_echo('option:abcza'),   // Alphabet Z - A
    ),
));

// navigation/pagination can add pagination to page
$content = "<h1 class='sortOptions'>$title</h1><div class='sortOptions'>$sort $sort_bar</div><div id='advancedResults'>";
$content .= '<ul class="elgg-list advancedResults">';
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
            $timeUpdated = gmdate("Y-m-d H:i:s", $result->time_created);
            $d_m_y = gmdate("Y-m-d", $result->time_created);
        }

        $subtype = $result->getSubtype();
        $guid = $result->guid;

        $user = get_user($result->owner_guid);
        $username = $user->username;
        $name = $user->name;

        $description = $result->description;
        $description = strip_tags($description);

        // view
        $int = $search['results'] - 1;
        //  && $countResults <= $int |||||||||add this to the if for limited results
        if(ResultsToShow($search, $result, $d_m_y)){
            $countResults++;
            $content .=  "
            <li class='advancedItem".overResults($countResults, $search['results'])."' id='".$countResults."'>
                <a href='/".$subtype."/view/".$guid."'>
                    <div class='head'>
                            <h4>".$result->title."</h4>
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
                    $content .= elgg_echo('search:results:latest').":".$timeUpdated;
                }
                $content .= "  
                        </div>
                        <div class='info'>\"Owner name\"</div>       
                    </div>  
                </a>
            </li>";
        }
    }
$pages = ceil($countResults / $search['results']);
$content .= "<div>";
for($i=0;$i<$pages;$i++){
    $content .= "<a href='#' class='advancedPage'><div class='advancedPagination'>".($i + 1)."</div></a>";
}
$content .= "</div>";
$content .= "
    <div id='noItems'><h3>".elgg_echo('search:results:none')."</h3></div>
</ul>
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
    //  Gain ability to set a range of viewables
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
function ResultsToShow (&$search, &$result, $date) {
    $dateReturn     = false;
    $titleReturn    = false;
    $userReturn     = false;
    $descReturn     = false;
    $synReturn      = false;
    $boolReturn     = false;
    if(boolean($search, $result)){
        $boolReturn = true;
    }
    $dateEn = false;
    switch($search['dateSets']){
        case '111' :    //  Full date
            $str = substr($date, 0, 10);
            $dated = substr($search['date'], 0, 10);
            break;
        case '110' :    //  No year
            $str = substr($date, 5, 10);
            $dated = substr($search['date'], 5, 10);
            break;
        case '101' :    //  No month
            $str = substr($date, 0, 4);
            $str .= substr($date, 7, 10);
            $dated = substr($search['date'], 0, 4);
            $dated .= substr($search['date'], 7, 10);
            break;
        case '100' :    //  Only day
            $str = substr($date, 8, 10);
            $dated = substr($search['date'], 8, 10);
            break;
        case '011' :    //  No day
            $str = substr($date, 0, 7);
            $dated = substr($search['date'], 0, 7);
            break;
        case '000' :    //  Disable date
            $dateEn = true;
            break;
    }
    if($dated == $str && !$dateEn){
        $dateReturn = true;
    } else {
        $dateReturn = false;
    }

    $searchList = explode(" ", $search['search']);

    $stopwords = getStops();

    $filterSearch = array_diff($searchList, $stopwords);
    foreach($filterSearch as $word){
       if(stripos($result->description, $word) !== false ||
           stripos($result->title, $word) !== false){
           $titleReturn = true;
           $descReturn = true;
       }
    }

    if(stripos($result->owner_guid, $search['users']) !== false)
    {
        $userReturn = true;
    }

    //  Synonym search
    if($search['synonym'] == 'yes'){
        $synonyms = synonymSearch($search['search']);

        $synonyms = explode(",", $synonyms);
        $synonyms = array_diff($synonyms, [$search['search']]);

        foreach ($synonyms as $synonym){
            if( stripos($result->title, $synonym) !== false ||
                stripos($result->description, $synonym) !== false
            )
            {
                $synReturn = true;
            }
        }
    }

    //  Every return has been set into a var in preparation for relevancy system
    if($dateReturn){
        if($titleReturn || $userReturn || $descReturn || $descReturn || $synReturn || $boolReturn){
            return true;
        }
    }
    return false;
}
// Boolean search refers to the AND/NOT/OR tags in the tags search box
function boolean(&$search, &$result) {
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
    if(strposAnd($result->title, $andArr) !== false ||
        strposAnd($result->description, $andArr) !== false){

        if(count($notArr) != null){
            foreach($notArr as $nots){  // Revenge of the nöts(nuts)
                if(stripos($result->title, $nots) == false &&
                    stripos($result->description, $nots) == false){
                    return true;
                }
            }
        } else {
            return true;
        }
    }

    //  Test OR seperately because everything containing OR should be displayed.
    if(count($orArr) != null) {
        if (strposOr($result->title, $orArr) !== false ||
            strposOr($result->description, $orArr) !== false
        ) {
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