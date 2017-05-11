<?php
/**
 * User: mfidan
 * Date: 13.02.2017
 * Time: 21:29
 */
Class DB {

    private $config;
    private $dbh;
    public $count;
    private $sql;

    public function __construct () {

        try{

            $this->config = parse_ini_file("config.ini");
            $this->dbh=new PDO("mysql:host=".$this->config['host']."; dbname=".$this->config['dbname']."", $this->config['user'], $this->config['passwd']);
            $this->dbh->setAttribute(PDO::ATTR_ERRMODE, PDO::ERRMODE_EXCEPTION);;
            $this->dbh->setAttribute(PDO::ATTR_EMULATE_PREPARES, false);

           //echo "Connection to succes";
        }


        catch (PDOException $e){

            echo "Failed connection to database";
            echo $e->getMessage();

        }

    }




    public function close() {

        $this->dbh=null;
        echo "Closed Connection";

    }




    public function insert ($table, $array) {

        $keys=implode(", ", array_keys($array));
        $str="?";
        $str .= str_repeat(", ?", count(array_values($array))-1);
        $vals=array_values($array);


        try{
            $sth=$this->dbh->prepare("INSERT INTO $table SET ($keys) VALUES ($str)");
            $sth->execute($vals);

        }

        catch (PDOException $e){

            echo $e->getMessage();
        }

    }




    public  function update ($table, $array, $column, $id) {

        $keys=array_keys($array);

        $str=implode("= ?, ", $keys);
        $str .= " = ?";
        array_push($array,$id);
        $vals=array_values($array);
        print_r($vals);

        try{

            $sth=$this->dbh->prepare("UPDATE $table  SET  $str WHERE $column=?");
            $sth->execute($vals);
        }

        catch (PDOException $e){

            echo $e->getMessage();
        }
    }





    public function delete ($table, $column, $id){

        try{

            $sth=$this->dbh->prepare("DELETE FROM $table WHERE $column=$id");
            $sth->execute();
        }

        catch (PDOException $e) {

           echo $e->getMessage();
        }
    }





    public function select ($table) {

        $sth=$this->dbh->prepare("SELECT * FROM $table");
        $sth->execute();

        $this->count=$sth->rowCount();

        $results=array();
        while($row=$sth->fetch(PDO::FETCH_OBJ)){

            $results[] = $row;
        }
        return $results;
    }



    public function selectAnd ($table, $array) {

        $keys=array_keys($array);

        $str=implode("=? and ", $keys);
        $str .= "=?";
        $vals = array_values($array);

        try {

            $sth=$this->dbh->prepare("SELECT * FROM $table WHERE $str");
            $sth->execute($vals);

            $this->count = $sth->rowCount();

            $results=array();
            while ($row=$sth->fetch(PDO::FETCH_OBJ)){
                $results[]=$row;
            }
            return $results;

        }

        catch (PDOException $e) {

            echo $e->getMessage();
        }

    }




    public function selectOr ($table, $array) {

        $keys=array_keys($array);
        $str=implode("=? Or ", $keys);
        $str .= "=?";
        $vals = array_values($array);

        try {

            $sth=$this->dbh->prepare("SELECT * FROM $table WHERE $str");
            $sth->execute($vals);

            $this->count = $sth->rowCount();

            $results=array();
            while ($row=$sth->fetch(PDO::FETCH_OBJ)){
                $results[]=$row;
            }
            return $results;

        }

        catch (PDOException $e) {
            echo $e->getMessage();
        }
    }




    public function likeAnd ($table, $array) {

        $keys=array_keys($array);

        $str=implode(" LIKE ? AND ", $keys);
        $str .= " LIKE ?";

        foreach ($array as $key => $value) {

            $array[$key] = "%".$value ."%";
        }

        $vals = array_values($array);

        try {

            $sth=$this->dbh->prepare("SELECT * FROM $table WHERE $str");
            $sth->execute($vals);

            $this->count = $sth->rowCount();

            $results=array();
            while ($row=$sth->fetch(PDO::FETCH_OBJ)){
                $results[]=$row;
            }
            return $results;


        }

        catch (PDOException $e) {
            echo $e->getMessage();
        }

    }


    public function likeOr ($table, $array) {

        $keys=array_keys($array);

        $str=implode(" LIKE ? Or ", $keys);
        $str .= " LIKE ?";

        foreach ($array as $key => $value) {

            $array[$key] = "%".$value ."%";

        }

        $vals = array_values($array);

        try {

            $sth=$this->dbh->prepare("SELECT * FROM $table WHERE $str");
            $sth->execute($vals);

            echo $sth;

            $this->count = $sth->rowCount();

            $results=array();
            while ($row=$sth->fetch(PDO::FETCH_OBJ)){
                $results[]=$row;
            }
            return $results;

        }

        catch (PDOException $e) {

            echo $e->getMessage();
        }

    }






//==============================alternative============================//

    // Query
    public function query ($query){

            $this->sql = $this->dbh->prepare($query);
    }


    // Execute
    public function run () {

        return $this->sql->execute();
    }


   // Execute(array)
    public function execute($array){

        $this->sql->execute($array);

    }


    // Fetch
    public function single($mode=null){

        if(is_null($mode)) { $mode=PDO::FETCH_OBJ; }

        $this->execute();

        $row=$this->sql->fetch ($mode);

        return $row;

   }


    // FetchAll
    public function multi($mode=null){

        if(is_null($mode)) { $mode=PDO::FETCH_OBJ; }

        $this->execute();
        return $this->sql->fetchAll ($mode);
    }


    // RowCount
    public function rowCount(){
        return $this->sql->rowCount();
    }


    // LastInsertId
    public function lastId(){
        return $this->dbh->lastInsertId();
    }
}
