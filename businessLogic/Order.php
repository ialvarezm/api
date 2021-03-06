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

        /**
        * Confirm payment
        */
        public function confirmPayment(){
            $this->daoOrder->confirmPayment();
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

        public function getOrderReport(){
            $this->daoOrder->getOrderReport();
        }

        public function getOrderExcel(){
            $this->daoOrder->getOrderExcel();
        }

        public function getCancelledOrderExcel(){
            $this->daoOrder->getCancelledOrderExcel();
        }
    }
?>
