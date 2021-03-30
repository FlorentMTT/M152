<?php

// Fonction qui se connecte à une base de données distante
function ConnectDB($nom_bd = 'portfolio', $utilisateur = 'root', $mdp = '') {
    static $connection = null;

    if ($connection == null) {
        try {
            $adresse = 'localhost'; // le chemin vers le serveur
            $port = '3306'; // le port du serveur
            // des options pour la connexion, on dit qu'elle est en UTF-8 et persistante, ce qui fait que la connexion n'est pas ré-établie à chaque requête SQL
            $options = array(PDO::MYSQL_ATTR_INIT_COMMAND => "SET NAMES utf8", PDO::ATTR_PERSISTENT => true);
            $connection = new PDO("mysql:host=$adresse;port=$port;dbname=$nom_bd", $utilisateur, $mdp, $options);
        } catch (Exception $e) {
            $connection = FALSE;
        }
    }
    return $connection;
}

function beginTransaction() {
  ConnectDB()->beginTransaction();
}

function commitTransaction() {
  if (ConnectDB()->inTransaction()) {
    ConnectDB()->commit();
  }
}

function rollback() {
  if (ConnectDB()->inTransaction()) {
    ConnectDB()->rollBack();
  }
}

/**
 * @param $commentaire = commentaire récupéré du form
 */
function addPost($commentaire) {
  $connection = ConnectDB();

  try {
    $requete = $connection->prepare('INSERT INTO Post (commentaire) VALUES (:commentaire);');

    $requete->bindParam(':commentaire', $commentaire, PDO::PARAM_STR);

    $requete->execute();
  } catch (Exception $e) {
    echo $e->getMessage();
  }

  return $connection->lastInsertId();
}

/**
 * @param $media = ['typeMedia', 'nomMedia']
 * @param $idPost = id du post lié a ces medias
 */
function addMedia($media, $idPost) {
  $connection = ConnectDB();

  try {
    $requete = $connection->prepare('INSERT INTO Media (typeMedia, nomMedia, idPost) VALUES (:typeMedia, :nomMedia, :idPost);');

    $requete->bindParam(':typeMedia', $media[0], PDO::PARAM_STR);
    $requete->bindParam(':nomMedia', $media[1], PDO::PARAM_STR);
    $requete->bindParam(':idPost', $idPost, PDO::PARAM_INT);

    $requete->execute();
  } catch (Exception $e) {
    echo $e->getMessage();
    return false;
  }

  return true;
}

function getPost($idPost) {
  $connection = ConnectDB();

  $post = FALSE;

  try
  {
    $query = $connection->prepare('SELECT * FROM Post WHERE idPost = :id;');
    $query->bindParam(':id', $idPost, PDO::PARAM_INT);
    $query->execute();

    $post = $query->fetch(PDO::FETCH_NAMED);

    $query = $connection->prepare('SELECT idMedia, typeMedia, nomMedia FROM Media WHERE idPost = :id');
    $query->bindParam(':id', $idPost, PDO::PARAM_INT);
    $query->execute();

    $post['medias'] = $query->fetchAll(PDO::FETCH_NAMED);

  } catch (Exception $e) {
    echo $e->getMessage();
  }
  return $post;
}

/**
 * return the post data and the medias associated with it
 */
function getAllPosts() {
  $connection = ConnectDB();

  $posts = [];

  try
  {
    $postQuery = $connection->prepare('SELECT * FROM Post ORDER BY modificationDate DESC;');
    $postQuery->execute();

    while ($post = $postQuery->fetch(PDO::FETCH_NAMED))
    {
      $mediaQuery = $connection->prepare('SELECT typeMedia, nomMedia, creationDate, modificationDate FROM Media WHERE idPost = :id;');
      $mediaQuery->bindParam(':id', $post['idPost'], PDO::PARAM_INT);
      $mediaQuery->execute();

      $medias = $mediaQuery->fetchAll(PDO::FETCH_NAMED);

      $post['medias'] = $medias;

      $posts[] = $post;
    }
  } catch (Exception $e) {
    echo $e->getMessage();
  }
  return $posts;
}

function deletePost($idPost) {
  $connection = ConnectDB();

  try
  {
    $mediaQuery = $connection->prepare('SELECT nomMedia FROM Media WHERE idPost = :id;');
    $mediaQuery->bindParam(':id', $idPost, PDO::PARAM_INT);
    $mediaQuery->execute();

    $medias = $mediaQuery->fetchAll(PDO::FETCH_NAMED);

    foreach ($medias as $value) {
      $filePath = '../media/' . $value['nomMedia'];
      if (file_exists($filePath)) {
        unlink($filePath);
      }
    }

    $request = $connection->prepare('DELETE FROM Post WHERE idPost = :id;');
    $request->bindParam(':id', $idPost, PDO::PARAM_INT);
    $request->execute();

  } catch (Exception $e) {
    echo $e->getMessage();
  }
}

function modifyPost($idPost, $newValue) {
  $connection = ConnectDB();

  try
  {
    $request = $connection->prepare('UPDATE Post SET commentaire=:commentaire WHERE idPost=:idPost');
    $request->bindParam(':commentaire', $newValue, PDO::PARAM_STR);
    $request->bindParam(':idPost', $idPost, PDO::PARAM_INT);
    $request->execute();

  } catch (Exception $e) {
    echo $e->getMessage();
  }
}

function removeMediaFromPost($medias) {
  $connection = ConnectDB();

  try
  {
    foreach ($medias as $value) {
      $filePath = './media/' . $value;
      if (file_exists($filePath)) {
        unlink($filePath);
      }
    }

    $names = implode('" or nomMedia="', $medias);

    $mediaQuery = $connection->prepare('DELETE FROM Media WHERE nomMedia="' . $names . '";');
    $mediaQuery->execute();

  } catch (Exception $e) {
    echo $e->getMessage();
  }
}
