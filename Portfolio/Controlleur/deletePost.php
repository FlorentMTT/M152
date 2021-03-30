<?php

require_once '../Model/mysql.php';

$id = filter_input(INPUT_GET, 'idPost', FILTER_SANITIZE_NUMBER_INT);

deletePost($id);

header('Location: ../');
