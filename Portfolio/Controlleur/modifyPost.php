<?php

$idPost = filter_input(INPUT_GET, 'idPost', FILTER_SANITIZE_NUMBER_INT);

require_once './Model/mysql.php';

$post = getPost($idPost);

$commentaire = filter_input(INPUT_POST, 'commentaire', FILTER_SANITIZE_STRING);

$goAhead = false;

if ($commentaire && $commentaire != $post['commentaire']) {
  modifyPost($idPost, $commentaire);
  $goAhead = true;
}

$names = [];

$index = 0;
foreach ($post['medias'] as $value) {
  $checkbox = $_POST[$index++];
  if ($checkbox) {
    $names[] = $value['nomMedia'];
    $goAhead = true;
  }
}
removeMediaFromPost($names);

$isGoodType = true;
$isTooBig = false;

$invalidMedias = [];

foreach ($_FILES['medias']['error'] as $key => $value) {
  if ($value != 0) {
    $invalidMedias[] = $key;
    $isTooBig = true;
  }
}

$count = count($_FILES['medias']['name']);

beginTransaction();
if (!empty($_FILES) && $count != count($invalidMedias)) {
  for ($i = 0; $i < $count; $i++) {
    if (in_array($i, $invalidMedias)) {
      continue;
    }

    $tmpName = $_FILES['medias']['tmp_name'][$i];
    $name = md5($tmpName . (new DateTime())->getTimestamp());

    $fullType = mime_content_type($tmpName);
    $type = explode('/', $fullType);

    $types = ['img' => 'image', 'vid' => 'video', 'aud' => 'audio'];

    if (in_array($type[0], $types)) {
      $extension = '.' . $type[1];

      $folder = array_search($type[0], $types);

      $destination = $folder . '/' . $name . $extension;
      move_uploaded_file($tmpName, './media/' . $destination);
      addMedia([$fullType, $destination], $idPost);
    } else {
      $isGoodType = false;
    }
  }

  if ($isGoodType && !$isTooBig) {
    commitTransaction();
  } else {
    rollBack();
  }
  $goAhead = true;
}

if ($goAhead) {
  header('Location: ?');
}

require_once './View/modifyPost.php';
