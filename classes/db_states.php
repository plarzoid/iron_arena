<?php

/**************************************************
*
*    States Class
*
***************************************************/

/**************************************************
*
*   Table Description:
*
*	id - INT - PRIMARY KEY
*	name - VARCHAR
*	parent - INT
*
**************************************************/
require_once("query.php");

class States {

var $db=NULL;
var $table="states";


/***************************************************

Constructor & Destructor

***************************************************/
public function __construct(){
    $this->db = Query::getInstance();
}

public function __destruct(){}


/**************************************************

Create Function

**************************************************/
public function create($name, $parent){

	//Validate the inputs
	if(!$this->checkName($name)){return false;}
	if(!$this->checkParent($parent)){return false;}

	//Create the values Array
	$values = array(
		":name"=>$name,
 		":parent"=>$parent
	);

	//Build the query
	$sql = "INSERT INTO $this->table (
				name,
				parent
			) VALUES (
				:name,
				:parent)";

	return $this->db->insert($sql, $values);
}


/**************************************************

Delete Function

**************************************************/
public function deleteStates($id){

	//Validate the input
	if(!$this->checkName($name)){return false;}
	if(!$this->checkParent($parent)){return false;}
	//Create the values array
	$values = array(":id"=>$id);

	//Create Query
	$sql = "DELETE FROM $this->table WHERE id=:id";

	return $this->db->delete($sql, $values);
}


/**************************************************

Update Record By ID Function(s)

**************************************************/
private function updateStatesById($id, $columns){

    //Values Array
    $values = array(":id"=>$id);
    foreach($columns as $column=>$value){
        $values[":".$column]=$value;
    }

    //Generate the query
    $sql = "UPDATE $this->table SET ";
    foreach(array_keys($columns) as $column){
        $sql.= "$column=:$column";
        if(strcmp($column, end($array_keys($columns)))){
            $sql.= ", ";
        }
    }
    $sql.= " WHERE id=:id";

    return $this->db->update($sql, $values);
}


/**************************************************

Query Everything

**************************************************/
public function getAll(){

    //Generate the query
    $sql = "SELECT * FROM $this->table";

    return $this->db->query($sql, array());
}


/**************************************************

Query By Column Function(s)

**************************************************/
private function getByColumn($column, $value){

    //inputs are pre-verified by the mapping functions below, so we can trust them

    //Values Array
    $values = array(":$column"=>$value);

    //Generate the query
    $sql = "SELECT * FROM $this->table WHERE $column=:$column";
    
    return $this->db->query($sql, $values);
}


public function getById($id){
	
    //Validate Inputs
    if(!$this->checkId($id)){return false;}

    return $this->getByColumn("id", $id);
}


public function getByName($name){
	
    //Validate Inputs
    if(!$this->checkName($name)){return false;}

    return $this->getByColumn("name", $name);
}


public function getByParent($parent){
	
    //Validate Inputs
    if(!$this->checkParent($parent)){return false;}

    return $this->getByColumn("parent", $parent);
}


/**************************************************
 
Column Validation Function(s)

**************************************************/
function checkId($id){
    //Not allowed to be null
    if(Check::isNull($id)){
        echo "id cannot be null!"; return false;
    }

    if(Check::notInt($id)){
        echo "id was invalid!"; return false;
    }

    return true;
}



function checkName($name){
    //Not allowed to be null
    if(Check::isNull($name)){
        echo "name cannot be null!"; return false;
    }

    if(Check::notString($name)){
        echo "name was invalid!"; return false;
    }

    return true;
}



function checkParent($parent){
    //Not allowed to be null
    if(Check::isNull($parent)){
        echo "parent cannot be null!"; return false;
    }

    if(Check::notInt($parent)){
        echo "parent was invalid!"; return false;
    }

    return true;
}



}//close class

?>
