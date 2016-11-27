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

        public function getProductReportQuery(){
            if($this->get_request_method() != "GET"){
                $this->response('',406);
            }

            $query="SELECT p.id,
                    p.nombre,
                    p.descripcion,
                    CONCAT('$', FORMAT(p.precio, 2)) as precio,
                    c.nombre AS categoria,
                    (SELECT COUNT(DISTINCT factura) FROM `detalle_factura` WHERE `producto` = p.id) as ventas
                    FROM producto p
                    JOIN categoria c on p.categoria = c.id
                    ORDER BY `ventas` DESC";

            return $query;
        }

        public function getProductReport(){
            $query = $this->getProductReportQuery();

            $this->db->get($query);
        }

        public function getProductExcel(){
            $query = $this->getProductReportQuery();
            $headers = array('Id', 'Producto', 'Descripción', 'Precio', 'Categoría', 'Ventas');
            $this->db->export($query, 'Reporte de Productos Más Vendidos.xlsx', $headers, 'Reporte de Productos Más Vendidos');
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
            $photos =  isset($_POST["data"]["photos"]) ? $_POST["data"]["photos"] : array();
            $queryProd = "UPDATE `producto` SET `nombre`='".$nombre."',
                             `descripcion`='".$descripcion."',
                             `precio`=".$precio.",
                             `categoria`=".$categoria." WHERE id=".$id.";";
            $queryPhotos = "";
            if(count($photos) > 0) {
                foreach ($photos as $photo) {
                    if(empty($photo["id"])){
                        $queryPhotos .= "INSERT INTO `foto`(`nombre`, `producto`, `principal`) VALUES
                        ('".$photo["nombre"]."',".$id.",".$photo["principal"].");";
                    }else {
                        $queryPhotos .= "UPDATE `foto` SET `nombre`='".$photo["nombre"]."' WHERE producto=". $id." AND id=".$photo["id"].";";
                    }
                }
            }
            $query = $queryProd . $queryPhotos;
            $this->db->post_multiple($query);
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
            $photos =  $_POST["data"]["photos"];
			$queryProd = "INSERT INTO `producto`(`nombre`, `descripcion`, `precio`, `categoria`) VALUES
            ('".$nombre."',
            '".$descripcion."',
            '".$precio."',
            '".$categoria."');";

            $last_id = "SELECT LAST_INSERT_ID() INTO @last_id;";
            $queryPhotos = "";
            foreach ($photos as $photo) {
                $queryPhotos .= "INSERT INTO `foto`(`nombre`, `producto`, `principal`) VALUES
                ('".$photo["nombre"]."',@last_id,".$photo["principal"].");";
            }
            $query = $queryProd . $last_id . $queryPhotos;
            $this->db->post_multiple($query);
		}

        /**
        * Remove Product
        */

		public function removeProduct(){
			if($this->get_request_method() != "POST"){
				$this->response('',406);
			}
			$id =  $_REQUEST["id"];
            $photo = "DELETE FROM foto where producto=" . $id . ";";
			$query = "DELETE FROM `producto` WHERE `id`=" . $id . ";";
			$this->db->post_multiple($photo . $query);
		}

        /**
        * Get Product Photos
        */

		public function getProductImages(){
			if($this->get_request_method() != "GET"){
				$this->response('',406);
			}
			$id =  $_REQUEST["id"];
			$query = "SELECT * FROM `foto` WHERE `producto`=" . $id;
			$this->db->get($query);
		}

        /**
        * Get ALL Product Photos
        */

		public function getAllProductImages(){
			if($this->get_request_method() != "GET"){
				$this->response('',406);
			}
			$query = "SELECT * FROM `foto`";
			$this->db->get($query);
		}
    }
?>
