<?php
class Database {

    private $host = DB_HOST;
    private $user = DB_USER;
    private $pass = DB_PASS;
    private $dbname = DB_NAME;

    private $dbh;
    private $error;

    private $stmt;
    
    public function __construct(){
        //Set Database Source Name (DSN)
        $dsn = 'mysql:host=' . $this->host . ';dbname=' . $this->dbname;
        //Set Options
        $options = array(
            PDO::ATTR_PERSISTENT => true,
            PDO::ATTR_ERRMODE => PDO::ERRMODE_EXCEPTION,
          //  PDO::MYSQL_ATTR_USE_BUFFERED_QUERY => false
        );
        //ATTEMPT CREATING CREATING CONNECTION OBJECT
        try {
            $this->dbh = new PDO($dsn, $this->user, $this->pass, $options);
        }
        //CATCH AND ECHO ERRORS
        catch (PDOException $e){
            $this->error = $e->getMessage();
            echo $this->error;
        }
        //$this->dbh->setAttribute(PDO::MYSQL_ATTR_USE_BUFFERED_QUERY, false);
    }
    
    public function query($query){
        //ATTEMPT TO PREPARE QUERY
        try {
            $this->stmt = $this->dbh->prepare($query);
        }
        //CATCH AND ECHO ERRORS
        catch (PDOException $e){
            $this->error = $e->getMessage();
            echo $this->error;
        }
        
    }
    
    public function bind($param, $value, $type = null){
        if (is_null($type)) {
            switch (true) {
                case is_int($value):
                    $type = PDO::PARAM_INT;
                    break;
                case is_bool($value):
                    $type = PDO::PARAM_BOOL;
                    break;
                case is_null($value):
                    $type = PDO::PARAM_NULL;
                    break;
                default:
                    $type = PDO::PARAM_STR;
            }
        }
        $this->stmt->bindValue($param, $value, $type);
    }
    
    public function execute(){
        return $this->stmt->execute();
    }
    
    public function resultset_assoc(){
        //ATTEMPT TO FETCHALL TO ASSOCIATIVE ARRAY
        try {
            $this->execute();
            return $this->stmt->fetchAll(PDO::FETCH_ASSOC);
        }
        //CATCH AND ECHO ERRORS
        catch (PDOException $e){
            $this->error = $e->getMessage();
            echo $this->error;
        }
        
    }
    
    public function resultset_num(){
        $this->execute();
        return $this->stmt->fetchAll(PDO::FETCH_NUM);
    }
    
    public function resultset_both(){
        //ATTEMPT TO FETCHALL TO ARRAY WITH BOTH ASSOC AND NUM
        try {
            $this->execute();
            return $this->stmt->fetchAll(PDO::FETCH_BOTH);
        }
        //CATCH AND ECHO ERRORS
        catch (PDOException $e){
            $this->error = $e->getMessage();
            echo $this->error;
        }
    }
    
    public function single(){
        $this->execute();
        return $this->stmt->fetch(PDO::FETCH_BOTH);
    }
    
    public function rowCount(){
        return $this->stmt->rowCount();
    }
    
    public function lastInsertId(){
        return $this->dbh->lastInsertId();
    }
    
    public function beginTransaction(){
        return $this->dbh->beginTransaction();
    }
    
    public function endTransaction(){
        return $this->dbh->commit();
    }
    
    public function cancelTransaction(){
        return $this->dbh->rollBack();
    }
    
    public function debugDumpParams(){
        return $this->stmt->debugDumpParams();
    }
    public function closeCurs(){
	$this->stmt->closeCursor();
    }
}



?>
