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
            $shipping =  $orden["envio"];
            $userEmail =  $orden["userEmail"];
            $mysqltime = date ($fecha);
			$queryOrder = "INSERT INTO factura (usuario, fecha, montoTotal, status, shipping)
                           VALUES (".$usuario.", '".$mysqltime."', ".$montoTotal.", 'Pago Pendiente', " . $shipping . "); ";

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

        $body .= '
        <tr>
            <td style="width: 100px; text-align: left;"><span style="font-size: 10pt;"><span style="font-family: tahoma, arial, helvetica, sans-serif;"><b>Envío: </b></span></span></td>
            <td style="width: 79px; text-align: left;"><span style="font-size: 10pt;"><span style="font-family: tahoma, arial, helvetica, sans-serif;"></span></span></td>
            <td style="width: 89px; text-align: left;"><span style="font-size: 10pt;"><span style="font-family: tahoma, arial, helvetica, sans-serif;"></span></span></td>
            <td style="width: 76px; text-align: left;"><span style="font-size: 10pt;"><span style="font-family: tahoma, arial, helvetica, sans-serif;">$'.$orden['envio'].'</span></span></td>
          </tr>
          </tbody>
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

        /**
        * Return entire list of products
        */
        public function getUserOrders(){
            if($this->get_request_method() != "GET"){
                $this->response('',406);
            }
            $id =  $_REQUEST["user"];
            $start = $_REQUEST["start"];
            $limit = $_REQUEST["limit"];
            $query = "SELECT f.id numeroOrden,
                             f.fecha,
                             f.montoTotal,
                             f.status,
                             f.shipping,
                             f.transferencia,
                             CONCAT(u.nombre, ' ' ,u.apellidos) as usuario,
                             u.direccion,
                             u.telefono1
                      FROM factura f JOIN usuario u ON u.id = f.usuario
                      WHERE f.usuario=" . $id ." LIMIT " . $start . ", " . $limit;

            $this->db->get($query);
        }

        /**
        * Return entire list of pendin orders
        */
        public function getPendingOrders(){
            if($this->get_request_method() != "GET"){
                $this->response('',406);
            }
            $start = $_REQUEST["start"];
            $limit = $_REQUEST["limit"];
            $query = "SELECT f.id numeroOrden,
                             f.fecha,
                             f.montoTotal,
                             f.status,
                             f.shipping,
                             CONCAT(u.nombre, ' ' ,u.apellidos) as usuario,
                             u.direccion,
                             u.telefono1,
                             f.transferencia
                      FROM factura f JOIN usuario u ON u.id = f.usuario
                      WHERE f.status= 'Pago Pendiente' OR f.status = 'Comprobación Pendiente' OR f.status = 'Entrega Pendiente'
                      LIMIT " . $start . ", " . $limit;
            $this->db->get($query);
        }


        public function getOrderReportQuery(){
            if($this->get_request_method() != "GET"){
                $this->response('',406);
            }
            $start = isset($_REQUEST["start"]) ? $_REQUEST["start"] : '';
            $limit = isset($_REQUEST["limit"]) ? $_REQUEST["limit"] : '';
            $status = $_REQUEST["status"];
            $excel = $_REQUEST["excel"];
            $query = "SELECT f.id numeroOrden,
                             f.fecha,
                             f.montoTotal,
                             f.status,
                             f.shipping,
                             CONCAT(u.nombre, ' ' ,u.apellidos) as usuario,
                             u.direccion,
                             u.telefono1,
                             f.transferencia
                      FROM factura f JOIN usuario u ON u.id = f.usuario
                      WHERE f.status= '".$status."'";
            if($excel == '0') $query .= "LIMIT " . $start . ", " . $limit;
            return $query;
        }

        public function getOrderExcel(){
            $query = $this->getOrderReportQuery();
            $headers = array('Número de Orden', 'Fecha', 'Monto Total', 'Status', 'Cobro por envío', 'Cliente', 'Dirección de Envío', 'Teléfono Principal', 'N° de Transferencia Bancaria');
            $this->db->export($query, 'Reporte de Órdenes Entregadas.xlsx', $headers, 'Reporte de Órdenes Entregadas');
        }

        public function getCancelledOrderExcel(){
            $query = $this->getOrderReportQuery();
            $headers = array('Número de Orden', 'Fecha', 'Monto Total', 'Status', 'Cobro por envío', 'Cliente', 'Dirección de Envío', 'Teléfono Principal');
            $this->db->export($query, 'Reporte de Órdenes Canceladas.xlsx', $headers, 'Reporte de Órdenes Canceladas');
        }

        public function getOrderReport(){
            $query = $this->getOrderReportQuery();
            $this->db->get($query);
        }

        /**
        * Change status of the order
        */
        public function changeOrderStatus(){
            if($this->get_request_method() != "POST"){
                $this->response('',406);
            }
            $status =  $_REQUEST["status"];
            $id =  $_REQUEST["id"];
            $query="UPDATE factura SET status='".$status."' WHERE id=".$id;

            $this->db->post($query);
        }

        /**
        * Confirm payment
        */
        public function confirmPayment(){
            if($this->get_request_method() != "POST"){
                $this->response('',406);
            }
            $id =  $_REQUEST["id"];
            $transferencia =  $_REQUEST["transferencia"];
            $query="UPDATE factura SET status='Comprobación Pendiente', transferencia=".$transferencia." WHERE id=".$id;

            $this->db->post($query);
        }

        public function contact(){
            if($this->get_request_method() != "POST"){
                $this->response('',406);
            }
            $email =  $_POST["data"]["email"];
            $msg =  $_POST["data"]["msg"];
            $username =  $_POST["data"]["username"];

            $body = '<h2><span style="color: #23a657;">Mensaje de: ' . $username . ' - ' . $email . '</span></h2>
                     <p style="font-size:15px">' . $msg . '</p>';

            $emailinst = new Email();
            $emailinst->sendMail('muebles.sanjose.123@gmail.com', $body, 'Comentarios de ' . $username);
        }

        public function getOrderDetails() {
            if($this->get_request_method() != "GET"){
                $this->response('',406);
            }
            $id =  $_REQUEST["id"];

            $query="SELECT p.nombre,
                           p.precio,
                           p.descripcion,
                           df.cantidad,
                           df.monto,
                           f.id numeroOrden,
                           u.nombre usuario,
                           u.apellidos
                    FROM detalle_factura df
                    JOIN factura f ON f.id = df.factura
                    JOIN producto p ON p.id = df.producto
                    JOIN usuario u ON u.id = f.usuario
                    WHERE f.id = " . $id;

            $this->db->get($query);
        }
    }
?>
