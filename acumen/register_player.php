<?php

    require_once("classes/page.php");
    require_once("classes/db_players.php");
    require_once("classes/db_states.php");
    require_once("classes/db_countries.php");

    $page = new Page();
	$p_db = new Players();

    /***************************************

    Handle Edits

    ***************************************/
    $action = $_REQUEST["action"];
    $pl_id = $_REQUEST["pl_id"];
    $defaults = array("country"=>244);
   
    if(!strcmp($action, "edit")){
        $defaults = $p_db->getById($pl_id);
        $defaults = $defaults[0];
    }

    /***************************************

    Register some inputs

    ***************************************/

    //store the fact we're editing
    $page->register("edit_id", "hidden", array("default_val"=>$pl_id));
    $page->register("first_name", "textbox", array("required"=>true, "default_val"=>$defaults[first_name]));
    $page->register("last_name", "textbox", array("required"=>true, "default_val"=>$defaults[last_name]));

    $page->register("country", "select", array( "get_choices_array_func"=>"getCountries", 
                                                "reloading"=>1, "default_val"=>$defaults[country]));

    $country_id=$page->getVar("country");
    if(empty($country_id)) $country_id=$defaults[country];
    $page->register("state", "select", array(   "get_choices_array_func"=>"getStates",
                                                "get_choices_array_func_args"=>array($country_id),
                                                "default_val"=>$defaults[state]));

    $page->register("vip", "checkbox", array("on_text"=>"VIP", "off_text"=>"", 
                                             "default_val"=>$defaults[vip]));

    //retrieve the fact that we're editing
    if(empty($pl_id)) $pl_id = $page->getvar("edit_id");

    if($pl_id){
		//set the defaults
		$defaults = $p_db->getById($pl_id);
		$defaults = $defaults[0];
        $page->register("register", "submit", array("value"=>"Update!"));
    	$page->register("delete", "submit", array("value"=>"Delete Player", "confirm"=>"Are you sure you want to delete this player?"));

	} else {
        $page->register("register", "submit", array("value"=>"Register!"));
    }
    $page->getChoices();

    /***************************************

    Listen for the click

    ***************************************/

    if($page->submitIsSet("register")){

        //Retrieve the vars
        $first = $page->getVar("first_name");
        $last = $page->getVar("last_name");
        $country = $page->getVar("country");
        $state = $page->getVar("state");
        $vip = $page->getVar("vip");
        
        if($pl_id){
            $nameChars = "a-zA-Z0-9' -";
            if(!preg_match("~^[$nameChars]+$~", $first)){
                $illegalChars = preg_replace("~[$nameChars]~", "", $first);
                $error = "First Name contains invalid character(s): '$illegalChars'!";
            } else 

            if(!preg_match("~^[$nameChars]+$~", $last)){
                $illegalChars = preg_replace("~[$nameChars]~", "", $last);
                $error = "Last Name contains invalid character(s): '$illegalChars'!";
            } else

            if(($defaults[first_name] != $first) || ($defaults[last_name] != $last)){
				$columns = array("first_name"=>$first, "last_name"=>$last, "country"=>$country);
				if(!empty($state))  $columns["state"]=$state;
                $exists = $p_db->queryByColumns($columns);  //case insensitive due to MySql

				//If our existance check didn't return who we're working on, we've got a problem
                if(count($exists) && ($exists[0]["id"] != $pl_id)){   
                    $error = "Player with that name & location exists!";
                }
            }

            if(empty($error)){
				
				if(empty($state))  $state=null;
                
				$columns = array("first_name"=>$first,
                                 "last_name"=>$last,
                                 "country"=>$country,
                                 "state"=>$state,
                                 "vip"=>$vip);

                $result = $p_db->updatePlayersById($pl_id, $columns);
            }
        } else {

            $nameChars = "a-zA-Z0-9' -";
            if(!preg_match("~^[$nameChars]+$~", $first)){
                $illegalChars = preg_replace("~[$nameChars]~", "", $first);
                $error = "First Name contains invalid character(s): '$illegalChars'!";
            } else

            if(!preg_match("~^[$nameChars]+$~", $last)){
                $illegalChars = preg_replace("~[$nameChars]~", "", $last);
                $error = "Last Name contains invalid character(s): '$illegalChars'!";
            } else
            
            {
                $columns = array("first_name"=>$first, "last_name"=>$last, "country"=>$country);
                if(!empty($state)) $columns["state"] = $state;
                $exists = $p_db->existsByColumns($columns);

                if($exists){
                    $error = "Player with that name & location exists!";
                }
            }


            if(empty($error))
                $result = $p_db->create($first, $last, $country, $state, $vip);
        }
    }

	/**************************************

	Handle player deletion

	**************************************/
	if($page->submitIsSet("delete")){

		try{
			$success = $p_db->deleteById($pl_id);	
			$success_str = "Successfully Deleted ".$defaults["last_name"].", ".$defaults["first_name"]."!";
			$page->setDisplayMode("text");
			$template = "templates/success.html";
		} catch (PDOException $e){
			$error = "Unable to delete player until all games and achievements are removed!";

			$inputs = array("edit_id", "first_name", "last_name", "country", "state", "vip", "register", "delete");
        	$page->setDisplayMode("form");
        	$template = "templates/default_section.html";
		}
	} else
	
    /**************************************
    Create and Show the Page

    **************************************/
    if($page->submitIsSet("register") && ($result != false)){
        
        //Build the Location
        $location = "";
        if($state){
            $states = new States();
            $s = $states->getById($state);
            $location.=$s[0][name].", ";
        }
        
        $countries = new Countries();
        $c = $countries->getById($country);
        $location.=$c[0][name];

        //Build the rest of string
        if($pl_id){
            $success_str = "Updated ";
        } else {
            $success_str = "Registered ";
        } 
        $success_str.= "$first $last from $location";
        if($vip) $success_str.= ", a VIP";
        $success_str.="!";

        $page->setDisplayMode("text");
        $link = array("href"=>"home.php?view=register_player", "text"=>"Register Another Player?");
        $template = "templates/success.html";
    
    } else {
    
        $inputs = array("edit_id", "first_name", "last_name", "country", "state", "vip", "register");
 		if(Session::isAdmin() && $pl_id){ $inputs[] = "delete"; }  //Add in the delete option if an admin is logged in
 		$page->setDisplayMode("form");
        $template = "templates/default_section.html";
    }
    
    $form_method="post";
    $form_action=$_SERVER[PHP_SELF]."?view=$view";
    $title = "Player Registration";

    //display it
    $page->startTemplate($meta);
    $page->doTabs();
    include $template;
    $page->displayFooter();
?>
