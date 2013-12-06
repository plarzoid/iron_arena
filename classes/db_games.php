<?php

/**************************************************
*
*    Games Class
*
***************************************************/

/**************************************************
*
*   Table Description:
*
*	id - INT - PRIMARY KEY
*	time - TIMESTAMP
*	game_system_id - INT
*	game_size_id - INT
*	scenario - TINYINT
*
**************************************************/
require_once("query.php");

class Games {

var $db=NULL;
var $table="games";


/***************************************************

Constructor & Destructor

***************************************************/
public function __construct(){
    $this->db = new Query();
}

public function __destruct(){}


/**************************************************

Create Function

**************************************************/
public function createGames($time, $game_system_id, $game_size_id, $scenario){

	//Validate the inputs
	if(Check::isNull($time)){return false;}
	if(Check::notInt($game_system_id)){return false;}
	if(Check::notInt($game_size_id)){return false;}
	if(Check::notBool($scenario)){return false;}

	//Create the values Array
	$values = array(
		":time"=>$time,
 		":game_system_id"=>$game_system_id,
 		":game_size_id"=>$game_size_id,
 		":scenario"=>$scenario
	);

	//Build the query
	$sql = "INSERT INTO $this->table (
				time,
				game_system_id,
				game_size_id,
				scenario
			) VALUES (
				:time,
				:game_system_id,
				:game_size_id,
				:scenario)";

	return $this->db->insert($sql, $values);
}


/**************************************************

Delete Function

**************************************************/
public function deleteGames($id){

	//Validate the input
	if(Check::isInt($id)){return false;}

	//Create the values array
	$values = array(":id"=>$id);

	//Create Query
	$sql = "DELETE FROM $this->table WHERE id=:id";

	return $this->db->delete($sql, $values);
}


/**************************************************

Update Record By ID Function(s)

**************************************************/
private function updateGamesById($id, $columns){

    //Values Array
    $values = array(":id"=>$id);
    foreach($columns as $column=>$value){
        $values[":".$column]=$value;
    }

    //Generate the query
    $sql = "UPDATE $this->table SET ";
    foreach(array_keys($columns) as $column){
        $sql.= "$column=:$column";
        if(strcmp($column, end($array_keys($columns))){
            $sql.= ", ";
        }
    }
    $sql.= " WHERE id=:id";

    return $this->db->update($sql, $values);
}


/**************************************************

Query By Column Function(s)

**************************************************/
private function getGamesByColumn($column, $value){

    //inputs are pre-verified by the mapping functions below, so we can trust them

    //Values Array
    $values = array(":$column"=>$value);

    //Generate the query
    $sql = "SELECT * FROM $this->table WHERE $column=:$column";
    
    return $this->db->query($sql, $values);
}


public function getGamesById($id){
	
    //Validate Inputs
    if(Check::notInt($id)){return false;}

    return getGamesByColumn("id", $id.);
}


public function getGamesByTime($time){
	
    //Validate Inputs
    if(Check::isNull($time)){return false;}

    return getGamesByColumn("time", $time.);
}


public function getGamesByGameSystemId($game_system_id){
	
    //Validate Inputs
    if(Check::notInt($game_system_id)){return false;}

    return getGamesByColumn("game_system_id", $game_system_id.);
}


public function getGamesByGameSizeId($game_size_id){
	
    //Validate Inputs
    if(Check::notInt($game_size_id)){return false;}

    return getGamesByColumn("game_size_id", $game_size_id.);
}


public function getGamesByScenario($scenario){
	
    //Validate Inputs
    if(Check::notBool($scenario)){return false;}

    return getGamesByColumn("scenario", $scenario.);
}

}//close class

?>