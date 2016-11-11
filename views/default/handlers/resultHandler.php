<?php

function getUsers() {
    $users = elgg_get_entities(array(
        'types' => 'user',
        'limit' => 0,
    ));
    return $users;
}