<?php

    ini_set('display_errors',1);
    error_reporting( E_ALL & ~E_DEPRECATED & ~E_STRICT );
    require_once("Rest.inc.php");
    include_once "businessLogic/Product.php";
    include_once "businessLogic/User.php";

	class API extends REST {

		private $mysqli_connect = NULL;
        private $db = NULL;
		public function __construct(){
			parent::__construct();				// Init parent contructor
			//$this->db = new DB();				// Initiate Database connection
            //$this->mysqli_connect = $this->db->dbConnect();
		}

		/*
		 * Dynmically call the method based on the query string
		 */
		public function processApi(){
            $class = strtolower(trim(str_replace("/","",$_REQUEST['v']))); //Class to be instantiated
			$func = strtolower(trim(str_replace("/","",$_REQUEST['x']))); //Method in that class
            $instance = new $class(); // Class instance
            $instance->$func(); //Method executing
		}

	}

	// Initiiate Library

	$api = new API;
	$api->processApi();
?>
