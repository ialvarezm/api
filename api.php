<?php

  ini_set('display_errors',1);
  error_reporting( E_ALL & ~E_DEPRECATED & ~E_STRICT );
 	require_once("Rest.inc.php");

	class API extends REST {

		public $data = "";

    const DB_SERVER = "localhost";
		const DB_USER = "root";
		const DB_PASSWORD = '123456';
		const DB = "muebleria";

		private $db = NULL;
		private $mysqli_connect = NULL;
		public function __construct(){
			parent::__construct();				// Init parent contructor
			$this->dbConnect();					// Initiate Database connection
		}

		/*
		 *  Connect to Database
		*/
		private function dbConnect(){
			$this->mysqli_connect = mysqli_connect(self::DB_SERVER, self::DB_USER, self::DB_PASSWORD);
			mysqli_set_charset($this->mysqli_connect, "utf8");
			if($this->mysqli_connect)
				mysqli_select_db($this->mysqli_connect, self::DB);
		}

		/*
		 * Dynmically call the method based on the query string
		 */
		public function processApi(){
			$func = strtolower(trim(str_replace("/","",$_REQUEST['x'])));
			if((int)method_exists($this,$func) > 0)
				$this->$func();
			else
				$this->response('',404); // If the method not exist with in this class "Page not found".
		}

    private function rol(){
			if($this->get_request_method() != "GET"){
				$this->response('',406);
			}

			$query="SELECT `id`, `nombre` FROM `rol`";

		  $this->doGet($query);
		}

    private function getProducts(){
        if($this->get_request_method() != "GET"){
        $this->response('',406);
        }

        $query="SELECT p.id, p.nombre, p.descripcion, p.precio, c.nombre AS categoria, c.id as catId FROM producto p JOIN categoria c on p.categoria = c.id";

        $this->doGet($query);
    }

    private function getCategories(){
        if($this->get_request_method() != "GET"){
        $this->response('',406);
        }

        $query="SELECT `id`, `nombre` FROM `categoria`";

        $this->doGet($query);
    }

    private function login(){
        if($this->get_request_method() != "GET"){
        	$this->response('',406);
        }
        $user = $_REQUEST["user"];
        $pass = $_REQUEST["pass"];

        $query="SELECT `nombre_usuario`, `nombre`, `apellidos`, `email`, `rol` FROM `usuario` WHERE nombre_usuario='" . $user . "' AND password='" . $pass . "'";

        $this->doGet($query);
    }

    private function doGet($query){
        $r = mysqli_query( $this->mysqli_connect, $query);

        if( $r === false ) {
        die( print_r( mysqli_error($this->mysqli_connect), true));
        }
        $result = array();
        while( $row = mysqli_fetch_array($r, MYSQLI_ASSOC) ) {
        array_push($result, $row);
        }
        $this->response($this->json($result), 200);
    }

    private function doPost($query) {
        $r = mysqli_query( $this->mysqli_connect, $query);
        if( $r === false ) {
            die( print_r( mysqli_error($this->mysqli_connect), true));
        }
        $this->response("", 200);
    }

		private function json($data){
			if(is_array($data)){
				return json_encode($data);
			}
		}

		private function addUser(){
			if($this->get_request_method() != "POST"){
				$this->response('',406);
			}

			$nombre_usuario =  $_POST["data"]["user_name"];
			$nombre = $_POST["data"]["name"];
            $apellidos = $_POST["data"]["last_name"];
			$password =  $_POST["data"]["password"];
			$email =  $_POST["data"]["email"];
			$direccion =  $_POST["data"]["address"];
			$telefono1 =  $_POST["data"]["phone1"];
            $telefono2 =  $_POST["data"]["phone2"];
			$query = "INSERT INTO `usuario`(`nombre_usuario`, `nombre`, `apellidos`, `password`, `email`, `rol`, `direccion`, `telefono1`, `telefono2`) VALUES
            ('". $nombre_usuario ."',
            '". $nombre ."',
            '".$apellidos."',
            '".$password."',
            '".$email."', 2 ,
            '".$direccion."',
            '".$telefono1."',
            '".$telefono2."')";

            $this->doPost($query);
		}

        private function updateUser(){
			if($this->get_request_method() != "POST"){
				$this->response('',406);
			}
            $id =  $_POST["data"]["id"];
			$nombre_usuario =  $_POST["data"]["user_name"];
			$nombre =  $_POST["data"]["name"];
			$apellidos = $_POST["data"]["last_name"];
			$password =  $_POST["data"]["password"];
			$email =  $_POST["data"]["email"];
			$direccion =  $_POST["data"]["address"];
			$telefono1 =  $_POST["data"]["phone1"];
            $telefono2 =  $_POST["data"]["phone2"];
			$query = "UPDATE `usuario` SET `nombre_usuario`=".$nombre_usuario.",
                                           `nombre`=".$nombre.",
                                           `apellidos`=".$apellidos.",
                                           `password`=".$password.",
                                           `email`=".$email.",
                                           `rol`=".$direccion.",
                                           `direccion`=".$direccion.",
                                           `telefono1`=".$telefono1.",
                                           `telefono2`=".$telefono2." WHERE id=".$id;
			$r = mysqli_query( $this->mysqli_connect, $query);
			if( $r === false ) {
				die( print_r( mysqli_error($this->mysqli_connect), true));
			}
			$this->response("", 200);
		}

        private function updateProduct(){
            if($this->get_request_method() != "POST"){
                $this->response('',406);
            }
            $id =  $_POST["data"]["id"];
            $nombre =  $_POST["data"]["nombre"];
            $descripcion =  $_POST["data"]["descripcion"];
            $precio = $_POST["data"]["precio"];
            $categoria =  $_POST["data"]["catId"];
            $query = "UPDATE `producto` SET `nombre`='".$nombre."',
                             `descripcion`='".$descripcion."',
                             `precio`=".$precio.",
                             `categoria`=".$categoria." WHERE id=".$id;
            $r = mysqli_query( $this->mysqli_connect, $query);
            if( $r === false ) {
                die( print_r( mysqli_error($this->mysqli_connect), true));
            }
            $this->response("", 200);
        }


        private function addProduct(){
			if($this->get_request_method() != "POST"){
				$this->response('',406);
			}

			$nombre = $_POST["data"]["name"];
            $descripcion = $_POST["data"]["description"];
			$precio =  $_POST["data"]["price"];
			$categoria =  $_POST["data"]["category"];
			$query = "INSERT INTO `producto`(`nombre`, `descripcion`, `precio`, `categoria`) VALUES
            ('".$nombre."',
            '".$descripcion."',
            '".$precio."',
            '".$categoria."')";

            $this->doPost($query);
		}



		private function update(){
			if($this->get_request_method() != "POST"){
				$this->response('',406);
			}
			$id =  $_POST["data"]["id"];
			$title =  $_POST["data"]["title"];
			$date = date($_POST["data"]["date"]);
			$body =  $_POST["data"]["body"];
			$type =  $_POST["data"]["type"];
			$category =  $_POST["data"]["category"];
			$link =  $_POST["data"]["link"];
			$query = "UPDATE `news` SET `title`='".$title."',`body`='".$body."',`type`='".$type."',`category`='".$category."',`link`='".$link."' WHERE id = " . $id;
			$r = mysqli_query( $this->mysqli_connect, $query);
			if( $r === false ) {
				die( print_r( mysqli_error($this->mysqli_connect), true));
			}
			$this->response("", 200);
		}

		private function delete(){
			if($this->get_request_method() != "POST"){
				$this->response('',406);
			}
			$id =  $_REQUEST["id"];
			$query = "DELETE FROM `news` WHERE `id`=" . $id;
			$r = mysqli_query( $this->mysqli_connect, $query);
			if( $r === false ) {
				die( print_r( mysqli_error($this->mysqli_connect), true));
			}
			$this->response("", 200);
		}
	}

	// Initiiate Library

	$api = new API;
	$api->processApi();
?>
