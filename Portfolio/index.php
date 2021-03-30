<?php

$pages = [
    'accueil', 'post', 'modifyPost'
];

$page = filter_input(INPUT_GET, 'page', FILTER_SANITIZE_STRING);

if (!in_array($page, $pages)) {
    $page = 'accueil';
}

require_once './Model/mysql.php';

require_once "./Controlleur/$page.php";
