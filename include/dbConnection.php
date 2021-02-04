<?php
require_once 'config.inc.php';



    $_SESSION["pdoInstance"] = null;

    /**
     * @brief   Class Constructor - Créer une nouvelle connextion à la database si la connexion n'existe pas
     *          On la met en privé pour que personne puisse créer une nouvelle instance via ' = new KDatabase();'
     */
function __construct() {
        echo "construct";
    }

    /**
     * @brief   Comme le constructeur, on rend __clone privé pour que personne ne puisse cloner l'instance
     */
function __clone() {
        
    }

    /**
     * @brief   Retourne l'instance de la Database ou créer une connexion initiale
     * @return $objInstance;
     */
 function getInstance() {
        if (!$_SESSION["pdoInstance"]) {
            try {
                $dsn = DB_DBTYPE . ':host=' . DB_HOST .  ';dbname=' . DB_NAME;
                $_SESSION["pdoInstance"] = new PDO($dsn, DB_USER, DB_PASS,array(PDO::MYSQL_ATTR_INIT_COMMAND => "SET NAMES utf8"));
                $_SESSION["pdoInstance"]->setAttribute(PDO::ATTR_ERRMODE, PDO::ERRMODE_EXCEPTION);       
            } catch (PDOException $e) {
                echo "EDatabase Error: " . $e->getMessage();
            }
        }
        return $_SESSION["pdoInstance"];
    }

?>