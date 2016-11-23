<?php
elgg_load_js('jsStyle');

$title = elgg_echo('search:results:title');

$search =  array('search' => $_GET['search'],
            'synonym' => $_GET['synonym'],
            'category' => $_GET['category'],
            'tags' => $_GET['tags'],
            'users' => $_GET['user'],
            'results' => $_GET['results'],
            'sort' => $_GET['sort'],
            'date' => $_GET['date']);

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
// Userpicker, Elgg heeft een datepicker
// TODO:BUG'cannot cat search without setting a search term'
$params = array(
    'type' => 'object',
    'subtype' => ELGG_ENTITIES_ANY_VALUE,
    'order_by' => $sort,
    'limit' => 0,
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

$content = "<h1 class='sortOptions'>$title</h1><div class='sortOptions'>$sort $sort_bar</div><div id='advancedResults'>";
$content .= '<ul class="elgg-list advancedResults">';
    foreach ($results as $result) {
        $d_m_y = '';
        $time = elgg_get_friendly_time($result->time_created);
        $timeArr = preg_split("/[\s]+/", $time);
        // If is older than a day, display date instead
        if($timeArr[1] == 'days' && $timeArr[0] > 1){
            $time = "
                <script>
                    var date = new Date(".$result->time_created.");
                    document.write(date.getDate()+1+'-'+date.getMonth()+1+'-'+date.getYear()+' '
                        +date.getHours()+':'+date.getMinutes());
                </script>";
            $d_m_y = "
                <script>
                    var date = new Date(".$result->time_created.");
                    document.write(date.getDate()+1+'-'+date.getMonth()+1+'-'+date.getYear());
                </script>";
        }

        $timeUpdated = elgg_get_friendly_time($result->time_updated);
        $timeUpdatedArr = preg_split("/[\s]+/", $timeUpdated);
        // If is older than a day, display date instead
        if($timeUpdatedArr[1] == 'days' && $timeUpdatedArr[0] > 1){
            $timeUpdated = "
                <script>
                    var date = new Date(".$result->time_updated.");
                    document.write(date.getDate()+1+'-'+date.getMonth()+1+'-'+date.getYear()+' '
                        +date.getHours()+':'+date.getMinutes());
                </script>";
        }

        $subtype = $result->getSubtype();
        $guid = $result->guid;

        $user = get_user($result->owner_guid);
        $username = $user->username;
        $name = $user->name;
        $name = substr($name,0,10).'...';

        $description = $result->description;
        $description = strip_tags($description);

        // view
        if(ResultsToShow($search, $result, $d_m_y) && $countResults <= $search['results']){
            $countResults++;
            $content .=  "
            <li class='advancedItem'>
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
                        </div>";
                if($timeCreated != $time){
                    $content .= "
                        <div class='four'>
                            ".elgg_echo('search:results:latest').":".$timeCreated."
                        </div>";
                }
                $content .= "         
                    </div>  
                </a>
            </li>";
        }
    }
$content .= "
    <div id='info'>some text here</div>
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
 *  $search contains search query
 *  $results contains current item result
 */
$andArr = [];
$orArr = [];
$notArr = [];
function ResultsToShow (&$search, &$result, $date) {
    if(boolean($search, $result)){
        return true;
    }

    $str = substr($date, 2);

    if($search['date'] == $str){
        return true;
    }

    if(stripos($result->title, $search['search']) !== false ||
        stripos($result->description, $search['search']) !== false ||
        stripos($result->owner_guid, $search['users']) !== false        //  User Search
    )
    {
        return true;
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