<?php
    ini_set('display_errors',1);
    error_reporting( E_ALL & ~E_DEPRECATED & ~E_STRICT );
    require_once("Rest.inc.php");
    include_once "DB.php";

    class DAOUser extends REST {
        public function __construct(){
            parent::__construct();
            $this->db = new DB();
		}

        /**
        * Get list of users by type (1: Admin, 2:Client)
        */

        public function getUsers(){
            if($this->get_request_method() != "GET"){
            $this->response('',406);
            }
            $role =  $_REQUEST["role"];

            $query="SELECT * FROM `usuario` WHERE `rol` = " . $role;

            $this->db->get($query);
        }

        /**
        * Get role list
        */

        public function rol(){
            if($this->get_request_method() != "GET"){
                $this->response('',406);
            }

            $query="SELECT `id`, `nombre` FROM `rol`";

            $this->db->get($query);
        }

        /**
        * Perform login
        */

        public function login(){
            if($this->get_request_method() != "GET"){
            	$this->response('',406);
            }
            $user = $_REQUEST["user"];
            $pass = $_REQUEST["pass"];

            $query="SELECT `id`, `nombre_usuario`, `nombre`, `apellidos`, `email`, `rol` FROM `usuario` WHERE nombre_usuario='" . $user . "' AND password='" . $pass . "'";

            $this->db->get($query);
        }

        public function addAdmin(){
			if($this->get_request_method() != "POST"){
				$this->response('',406);
			}
            $nombre_usuario =  $_POST["data"]["nombre_usuario"];
			$nombre =  $_POST["data"]["nombre"];
			$apellidos = $_POST["data"]["apellidos"];
			$password =  $_POST["data"]["password"];
			$email =  $_POST["data"]["email"];
			$query = "INSERT INTO `usuario`(`nombre_usuario`, `nombre`, `apellidos`, `password`, `email`, `rol`) VALUES
            ('". $nombre_usuario ."',
            '". $nombre ."',
            '".$apellidos."',
            '".$password."',
            '".$email."', 1)";

            $this->db->post($query);
		}

        /**
        * Add new client
        */

        public function addUser(){
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
            $rol =  $_POST["data"]["rol"];
			$query = "INSERT INTO `usuario`(`nombre_usuario`, `nombre`, `apellidos`, `password`, `email`, `rol`, `direccion`, `telefono1`, `telefono2`) VALUES
            ('". $nombre_usuario ."',
            '". $nombre ."',
            '".$apellidos."',
            '".$password."',
            '".$email."', " . $rol ." ,
            '".$direccion."',
            '".$telefono1."',
            '".$telefono2."')";

            $this->db->post($query);
		}

        /**
        * Update admin user
        */

        public function updateUser(){
			if($this->get_request_method() != "POST"){
				$this->response('',406);
			}
            $id =  $_POST["data"]["id"];
			$nombre_usuario =  $_POST["data"]["nombre_usuario"];
			$nombre =  $_POST["data"]["nombre"];
			$apellidos = $_POST["data"]["apellidos"];
			$email =  $_POST["data"]["email"];
			$query = "UPDATE `usuario` SET `nombre_usuario`='".$nombre_usuario."',
                                           `nombre`='".$nombre."',
                                           `apellidos`='".$apellidos."',
                                           `email`='".$email."' WHERE id=".$id;
			$this->db->post($query);
		}

        /**
        * Update client
        */

        public function updateClient(){
			if($this->get_request_method() != "POST"){
				$this->response('',406);
			}
            $id =  $_POST["data"]["id"];
			$nombre =  $_POST["data"]["nombre"];
			$apellidos = $_POST["data"]["apellidos"];
			$email =  $_POST["data"]["email"];
			$direccion =  $_POST["data"]["direccion"];
			$telefono1 =  $_POST["data"]["telefono1"];
            $telefono2 =  $_POST["data"]["telefono2"];
			$query = "UPDATE `usuario` SET `nombre`='".$nombre."',
                                           `apellidos`='".$apellidos."',
                                           `email`='".$email."',
                                           `direccion`='".$direccion."',
                                           `telefono1`='".$telefono1."',
                                           `telefono2`='".$telefono2."' WHERE id=".$id;
			$this->db->post($query);
		}

        /**
        * Remove user
        */

        public function removeUser(){
			if($this->get_request_method() != "POST"){
				$this->response('',406);
			}
			$id =  $_REQUEST["id"];
			$query = "DELETE FROM `usuario` WHERE `id`=" . $id;

			$this->db->post($query);
		}

    }
?>
