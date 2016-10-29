<?php
    ini_set('display_errors',1);
    error_reporting( E_ALL & ~E_DEPRECATED & ~E_STRICT );
    require_once("Rest.inc.php");
    include_once "DB.php";

    class DAOProduct extends REST {

        public function __construct(){
            parent::__construct();
            $this->db = new DB();
		}

        /**
        * Return entire list of products
        */
        public function getProducts(){
            if($this->get_request_method() != "GET"){
                $this->response('',406);
            }

            $query="SELECT p.id, p.nombre, p.descripcion, p.precio, c.nombre AS categoria, c.id as catId FROM producto p JOIN categoria c on p.categoria = c.id";

            $this->db->get($query);
        }

        /**
        * Return entire list of categories
        */
        public function getCategories(){
            if($this->get_request_method() != "GET"){
            $this->response('',406);
            }

            $query="SELECT `id`, `nombre` FROM `categoria`";

            $this->db->get($query);
        }

        /**
        * Edit product
        */

        public function updateProduct(){
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
            $this->db->post($query);
        }

        /**
        * Add new product
        */


        public function addProduct(){
			if($this->get_request_method() != "POST"){
				$this->response('',406);
			}

            $nombre =  $_POST["data"]["nombre"];
            $descripcion =  $_POST["data"]["descripcion"];
            $precio = $_POST["data"]["precio"];
            $categoria =  $_POST["data"]["catId"];
			$query = "INSERT INTO `producto`(`nombre`, `descripcion`, `precio`, `categoria`) VALUES
            ('".$nombre."',
            '".$descripcion."',
            '".$precio."',
            '".$categoria."')";

            $this->db->post($query);
		}

        /**
        * Remove Product
        */

		public function removeProduct(){
			if($this->get_request_method() != "POST"){
				$this->response('',406);
			}
			$id =  $_REQUEST["id"];
			$query = "DELETE FROM `producto` WHERE `id`=" . $id;
			$this->db->post($query);
		}
    }
?>
