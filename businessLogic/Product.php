<?php
    include("dataAccess/Product.php");

    class product {

        private $daoProduct = NULL;
        public function __construct(){
            $this->daoProduct = new DAOProduct();
		}

        /**
        * Return entire list of products
        */
        public function getProducts(){
            $this->daoProduct->getProducts();
        }

        public function getProductReport(){
            $this->daoProduct->getProductReport();
        }

        public function getProductExcel(){
            $this->daoProduct->getProductExcel();
        }

        /**
        * Return entire list of categories
        */
        public function getCategories(){
            $this->daoProduct->getCategories();
        }

        /**
        * Add product
        */
        public function addProduct(){
            $this->daoProduct->addProduct();
        }

        /**
        * Edit product
        */

        public function updateProduct(){
            $this->daoProduct->updateProduct();
		}

        /**
        * Remove Product
        */

		public function removeProduct(){
			$this->daoProduct->removeProduct();
		}

        /**
        * Get Product Photos
        */

		public function getProductImages(){
			$this->daoProduct->getProductImages();
		}

        /**
        * Get ALL Product Photos
        */

		public function getAllProductImages(){
			$this->daoProduct->getAllProductImages();
		}
    }
?>
