<?php
    include("dataAccess/Order.php");

    class order{

        private $daoOrder = NULL;
        public function __construct(){
            $this->daoOrder = new DAOOrder();
		}

        /**
        * Save order and send confirmation email
        */

        public function saveOrder() {
            $this->daoOrder->saveOrder();
        }
    }
?>
