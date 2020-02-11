<?php

/**
 * includes/class.pdo.php
 * Comprend des fonctions pour nous aider à gérer la base de données
 * @author Stephane DEWITTE <stephane.dewitte@gmail.com>
 * @version 1.0
 * @filesource
 * @package bdd
 */
//require_once __DIR__ . '/www/includes/conf.php';

/**
 * Classe bdd
 * @param statement : le statement à éxécuter
 * @return Statement
 * @package bdd
 */
class bddpdo
{

    // Hote
    var $host = SERVER_HOST;
    // User
    var $user = SERVER_USERNAME;
    // Password
    var $password = SERVER_PASSWORD;
    // Databse
    var $database = SERVER_DBNAME;
    // Persistency
    var $persistency = false;
    // Port
    var $port = NULL;
    // DB DSN
    var $dsn = '';
    // PDO
    var $pdo = NULL;
    // Error mode
    var $errorMode = 'WARNING';

    function __construct()
    {
        global $profiler, $debug_mode;
        try
        {
            $temp =
                new PDO('pgsql:host=' . $this->host . ';dbname=' . $this->database, $this->user, $this->password, array(PDO::ATTR_PERSISTENT => true));
            if ($debug_mode)
            {
                $this->pdo = new \Fabfuel\Prophiler\Decorator\PDO\PDO($temp, $profiler);
            } else
            {
                $this->pdo = $temp;
            }

        }
        catch (PDOException $e)
        {
            echo 'Échec lors de la connexion : ' . $e->getMessage();
        }
    }

    function returnType()
    {

        return $this->pdo->getAttribute(PDO::ATTR_DRIVER_NAME);
    }

    /**
     * @desc   : Prépare la requête
     * @param : String $sql
     * @return : Query statement
     */
    function prepare($sql)
    {
        $test = $this->pdo->prepare($sql);
        if ($test === false)
        {
            $this->erreur('');
            die('Erreur SQL, impossible de continuer');
        }
        return $test;
    }

    /*     * *
     * @desc   : Execute une requête SQL
     * @param : array $varArray, statement $stmt
     * @return : Query result
     */

    function execute($varArray, $stmt)
    {
        // Le paramètre doit être un Array
        if (is_array($varArray))
        {
            // Si le tableau des parametre est vide on execute la requête preparé sans paramètre
            if (empty($varArray))
            {
                $ret = $stmt->execute();
                // Sinon on execute la requête préparée avec les paramètres
            } else
            {
                $ret = $stmt->execute($varArray);
            }
            if ($ret === false)
            {
                $this->erreur($stmt);
                $debug = false;

                die('Erreur SQL, impossible de continuer');


            }
            return $stmt;
        } else
        {
            // Mode exception
            if ($this->exception)
            {
                throw new Exception('<pre><strong>FATAL ERROR : ' . __METHOD__ . '</strong> except first given parameter to be an array</pre>', 0);
            } else
            {
                return false;
            }
        }
    }

    /*     * *
     * @desc   : Execute une requête SQL
     * @param : String $sql
     * @return : Query result
     */

    public function query($sql)
    {
        /*if (strpos(strtoupper($sql), "SELECT") == 0)
        {
            return $this->pdo->query($sql);

        } else
        {
            return $this->pdo->exec($sql);

        }*/
        return $this->pdo->query($sql);
    }

    /*     * *
     * @desc   : Gestion des transactions
     * @param : Bool $autocomit
     * @return : Bool
     */

    public function AutoCommit($autocommit = true)
    {
        return $this->pdo->setAttribute(PDO::ATTR_AUTOCOMMIT, $autocommit);
    }

    /*     * *
     * @desc   : Démarre la transaction
     * @param : None
     * @return : Bool
     */

    public function Begin()
    {
        return $this->pdo->beginTransaction();
    }

    /*     * *
     * @desc   : Valide la requête
     * @param : None
     * @return : Bool
     */

    public function Commit()
    {
        return $this->pdo->commit();
    }

    /*     * *
     * @desc   : Annule la requête
     * @param : None
     * @return : Bool
     */

    public function RollBack()
    {
        return $this->pdo->rollBack();
    }

    /*     * *
     * @desc   : Fecth Mode Array
     * @param : Resource query result
     * @return : query result rows
     */

    public function fetchAll($results)
    {
        return $results->fetchAll();
    }

    /*     * *
     * @desc   : Fecth Mode Object
     * @param : Resource query result
     * @return : query result rows
     */

    public function fetchObject($results)
    {
        return $results->fetch(PDO::FETCH_OBJ);
    }

    /*     * *
     * @desc   : Fecth Mode Assoc
     * @param : Resource query result
     * @return : query result rows
     */

    public function fetchAssoc($results)
    {
        return $results->fetch(PDO::FETCH_ASSOC);
    }

    /*     * *
     * @desc   : Fecth Mode Num ROw
     * @param : Resource query result
     * @return : query result rows
     */

    public function fetchRow($results)
    {
        return $results->fetch(PDO::FETCH_NUM);
    }

    /*     * *
     * @desc   : Fecth Mode Array
     * @param : Resource query result
     * @return : query result rows
     */

    public function fetchArray($results)
    {
        return $results->fetch(PDO::FETCH_BOTH);
    }

    /*     * *
     * @desc   : Retourne le nombre de resultat d'une requête
     * @param : None
     * @return : Int numrows
     */

    public function rowCount()
    {
        return $this->pdo->rowCount();
    }

    /*     * *
     * @desc   : Retourne l'id du dernier enregistrement
     * @param : Void
     * @return : Int id
     */

    public function lastInsertId($options = array())
    {
        return $this->pdo->lastInsertId();
    }

    /*     * *
     * @desc   : Librer les resultats de la requête
     * @param : results
     * @return : Bool
     */

    public function closeCursor($results = NULL)
    {
        return $this->pdo->closeCursor();
    }

    /*     * *
     * quote
     */

    public function quote($value, $type = DATABASE::PARAM_STR)
    {
        /* switch($type) {
          case self::PARAM_INT :
          $value = $this->pdo->quote($value,PDO::PARAM_INT);
          break;
          case self::PARAM_FLOAT :
          $value = $this->pdo->quote($value,PDO::PARAM_FLOAT);
          break;
          case self::PARAM_BOOL :
          $value = $this->pdo->quote($value,PDO::PARAM_BOOL);
          break;
          default:
          $value = $this->pdo->quote($value,PDO::PARAM_STR);
          break;
          } */
        $value = $this->pdo->quote($value, PDO::PARAM_STR);
        return $value;
    }

    public function erreur($stmt = '')
    {
        if (defined('DEBUG'))
        {
            $debug = DEBUG;
        } else
        {
            $debug = false;
        }
        if (empty($stmt))
        {
            $msg = print_r($stmt->errorInfo(), true);
            ob_flush();
            ob_start();
            $stmt->debugDumpParams();
            $msg .= ob_get_flush();
            ob_clean();
            $this->log_message($msg);
            if ($debug)
            {
                echo '<pre>' . $msg . '</pre>';
            }
            return $this->pdo->errorInfo();
        } else
        {
            $msg = print_r($stmt->errorInfo(), true);
            $msg .= print_r($stmt, true);
            // on efface ce qui a pu être envoyé
            ob_flush();
            ob_start();
            $stmt->debugDumpParams();
            $msg .= ob_get_flush();
            ob_clean();
            if ($debug)
            {
                echo '<pre>' . $msg . '</pre>';
            }
            $this->log_message($msg);
        }
    }

    function log_message($msg)
    {
        global $auth;
        $ip        = getenv('REMOTE_ADDR');
        $message   = $msg;
        $texte_log =
            chr(10) . '--------' . chr(10) . '   ' . date('y-m-d H:i:s') . chr(10) . '   Page ' . $_SERVER['PHP_SELF'] . '
	IP : ' . $ip . '
	Message : [' . $message . ']
	Erreur :
	Libelle : [ ]
	Compte = ' . ((isset($auth)) ? $auth->compt_cod : '0') . '
	Perso = ' . ((isset($auth)) ? $auth->perso_cod : '0');

        $e         = new Exception;
        $pileAppel = chr(10) . '    Context : [' . chr(10) . $e->getTraceAsString() . ']';
        $logfile   = new logfile();
        $logfile->writelog_class_sql($texte_log . $pileAppel);

    }


}
