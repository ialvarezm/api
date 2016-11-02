<?php
    ini_set('display_errors',1);
    error_reporting( E_ALL & ~E_DEPRECATED & ~E_STRICT );
    require_once("Rest.inc.php");
    include_once "DB.php";
    include_once "Email.php";

    class DAOOrder extends REST {

        public function __construct(){
            parent::__construct();
            $this->db = new DB();
		}

        /**
        * Save order and send confirmation email
        */

        public function saveOrder() {
            if($this->get_request_method() != "POST"){
                $this->response('',406);
            }
            $orden =  $_POST["data"]["order"];
            $fecha =  $orden["fecha"];
            $montoTotal =  $orden["montoTotal"];
            $usuario =  $orden["usuario"];
            $userEmail =  $orden["userEmail"];
			$queryOrder = "INSERT INTO factura (usuario, fecha, montoTotal, status)
                           VALUES (".$usuario.", '".$fecha."', ".$montoTotal.", 'No entregado'); ";

            $order_details = $_POST["data"]["order_details"];
            $queryDetails = "";

            foreach ($order_details as $detail) {
                $queryDetails .= "INSERT INTO detalle_factura (producto, factura, monto, cantidad)
                 VALUES (".$detail['producto'].",@last_id,".$detail['monto'].",".$detail['cantidad']."); ";
            }
            $last_id = "SELECT LAST_INSERT_ID() INTO @last_id;";
            $query = $queryOrder . $last_id . $queryDetails;

            //Send confirmation email
            $body = $this->buildHTMLTemplate($orden, $order_details);
            $email = new Email();
            $email->sendMail($userEmail, $body);

            $this->db->post_multiple($query);
        }

        private function buildHTMLTemplate($orden, $detalles) {
            $body = '<!DOCTYPE html>
<html>
  <head>
    <style>
      @media only screen and (max-device-width: 480px) {
        /* mobile-specific CSS styles go here */
      }
    </style>
  </head>
  <body>
    <div class="main">
      <p><span style="font-size: 16pt;"><strong>Hola '.$orden["cliente"].'!</strong></span></p>
      <p><span style="font-size: 12pt;">A continuación encontrará un detalle de su compra en Muebles Rústicos San José:</span></p>
    <div class="main"><span style="font-size: 12pt;"><strong>Fecha:</strong> '.$orden["fecha"].'</span></div>
    <div class="main"><span style="font-size: 12pt;"><strong>Cliente:</strong> '.$orden["cliente"].'</span></div>
    <p></p>
    <div class="main"><span style="text-decoration: underline; font-size: 14pt; color: #23a657;"><strong>Detalles de la compra</strong></span></div>
    <div class="main">
      <table width="432" height="32">
        <tbody>
          <tr>
            <td style="width: 88px;"><strong><span style="font-family: tahoma, arial, helvetica, sans-serif; font-size: 12pt;">Producto</span></strong></td>
            <td style="width: 79px;"><strong><span style="font-family: tahoma, arial, helvetica, sans-serif; font-size: 12pt;">Precio</span></strong></td>
            <td style="width: 89px;"><strong><span style="font-family: tahoma, arial, helvetica, sans-serif; font-size: 12pt;">Cantidad</span></strong></td>
            <td style="width: 76px;"><strong><span style="font-family: tahoma, arial, helvetica, sans-serif; font-size: 12pt;">Total</span></strong></td>
          </tr>';
        foreach ($detalles as $detalle) {
            $body .=   '<tr>
                <td style="width: 100px; text-align: left;"><span style="font-size: 10pt;"><span style="font-family: tahoma, arial, helvetica, sans-serif;">'.$detalle['nombreProd'].'</span></span></td>
                <td style="width: 79px; text-align: left;"><span style="font-size: 10pt;"><span style="font-family: tahoma, arial, helvetica, sans-serif;">$'.$detalle['precio'].'</span></span></td>
                <td style="width: 89px; text-align: left;"><span style="font-size: 10pt;"><span style="font-family: tahoma, arial, helvetica, sans-serif;">'.$detalle['cantidad'].'</span></span></td>
                <td style="width: 76px; text-align: left;"><span style="font-size: 10pt;"><span style="font-family: tahoma, arial, helvetica, sans-serif;">$'.$detalle['monto'].'</span></span></td>
              </tr>';
        }

        $body .= '</tbody>
      </table>
      <p></p>
      <p><strong><span style="font-size: 14pt;">Total a pagar: $'.$orden["montoTotal"].'</span></strong></p>
      <p><strong><span style="font-size: 14pt;"></span></strong></p>
      <p><span style="font-size: 8pt;"><strong>Recuerde hacer el depósito bancario en las siguientes 48 horas para que su compra no sea cancelada.</strong></span></p>
    </div>
  </body>
</html>';
            return $body;
        }

        // /**
        // * Return entire list of products
        // */
        // public function getProducts(){
        //     if($this->get_request_method() != "GET"){
        //         $this->response('',406);
        //     }
        //
        //     $query="SELECT p.id, p.nombre, p.descripcion, p.precio, c.nombre AS categoria, c.id as catId FROM producto p JOIN categoria c on p.categoria = c.id";
        //
        //     $this->db->get($query);
        // }
        //
        // /**
        // * Return entire list of categories
        // */
        // public function getCategories(){
        //     if($this->get_request_method() != "GET"){
        //     $this->response('',406);
        //     }
        //
        //     $query="SELECT 'id', 'nombre' FROM 'categoria'";
        //
        //     $this->db->get($query);
        // }
        //
        // /**
        // * Edit product
        // */
        //
        // public function updateProduct(){
        //     if($this->get_request_method() != "POST"){
        //         $this->response('',406);
        //     }
        //     $id =  $_POST["data"]["id"];
        //     $nombre =  $_POST["data"]["nombre"];
        //     $descripcion =  $_POST["data"]["descripcion"];
        //     $precio = $_POST["data"]["precio"];
        //     $categoria =  $_POST["data"]["catId"];
        //     $query = "UPDATE 'producto' SET 'nombre'='".$nombre."',
        //                      'descripcion'='".$descripcion."',
        //                      'precio'=".$precio.",
        //                      'categoria'=".$categoria." WHERE id=".$id;
        //     $this->db->post($query);
        // }
        //
        // /**
        // * Add new product
        // */
        //
        //
        // public function addProduct(){
		// 	if($this->get_request_method() != "POST"){
		// 		$this->response('',406);
		// 	}
        //
        //     $nombre =  $_POST["data"]["nombre"];
        //     $descripcion =  $_POST["data"]["descripcion"];
        //     $precio = $_POST["data"]["precio"];
        //     $categoria =  $_POST["data"]["catId"];
		// 	$query = "INSERT INTO 'producto'('nombre', 'descripcion', 'precio', 'categoria') VALUES
        //     ('".$nombre."',
        //     '".$descripcion."',
        //     '".$precio."',
        //     '".$categoria."')";
        //
        //     $this->db->post($query);
		// }
        //
        // /**
        // * Remove Product
        // */
        //
		// public function removeProduct(){
		// 	if($this->get_request_method() != "POST"){
		// 		$this->response('',406);
		// 	}
		// 	$id =  $_REQUEST["id"];
		// 	$query = "DELETE FROM 'producto' WHERE 'id'=" . $id;
		// 	$this->db->post($query);
		// }
    }
?>
