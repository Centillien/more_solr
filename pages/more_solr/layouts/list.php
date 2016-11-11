<?php
elgg_load_js('jsStyle');

$title = elgg_echo('search:results:title');

$search =  array('search' => $_GET['search'],
            'synonym' => $_GET['synonym'],
            'category' => $_GET['category'],
            'tags' => $_GET['tags'],
            'users' => $_GET['user'],
            'results' => $_GET['results'],
            'sort' => $_GET['sort']);


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
$params = array('type' => 'object', 'subtype' => ELGG_ENTITIES_ANY_VALUE, 'order_by' => $sort, 'limit' => 0);
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
        }

        $timeCreated = elgg_get_friendly_time($result->time_updated);
        $timeCreatedArr = preg_split("/[\s]+/", $timeCreated);
        // If is older than a day, display date instead
        if($timeCreatedArr[1] == 'days' && $timeCreatedArr[0] > 1){
            $timeCreated = "
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
        if(ResultsToShow($search, $result)){
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
function ResultsToShow (&$search, &$result) {
    if(strpos($result->title, $search['search']) !== false ||
        strpos($result->description, $search['search']) !== false ||
        strpos($result->owner_guid, $search['users']) !== false)
    {
        return true;
    }
}