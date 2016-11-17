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

        /**
        * Return entire list of products
        */
        public function getUserOrders(){
            $this->daoOrder->getUserOrders();
        }

        /**
        * Change status of the order
        */
        public function changeOrderStatus(){
            $this->daoOrder->changeOrderStatus();
        }

        public function getPendingOrders(){
            $this->daoOrder->getPendingOrders();
        }

        public function contact(){
            $this->daoOrder->contact();
        }

        public function getOrderDetails() {
            $this->daoOrder->getOrderDetails();
        }
    }
?>
