<?php
    require_once("Rest.inc.php");
    include_once "businessLogic/Product.php";
    include_once "businessLogic/User.php";

	class API extends REST {
        const DB_SERVER = "localhost";
		const DB_USER = "root";
		const DB_PASSWORD = '123456';
		const DB = "muebleria";

		private $mysqli_connect = NULL;
		public function __construct(){
			parent::__construct();				// Init parent contructor
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
