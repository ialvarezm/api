<?php
    include('dataAccess/User.php');

    class user {
        private $daoUser = NULL;
        public function __construct(){
            $this->daoUser = new DAOUser();
		}

        /**
        * Get list of users by type (1: Admin, 2:Client)
        */

        public function getUsers(){
            $this->daoUser->getUsers();
        }

        /**
        * Get role list
        */

        public function rol(){
            $this->daoUser->rol();
        }

        /**
        * Perform login
        */

        public function login(){
            $this->daoUser->login();
        }

        public function addAdmin(){
			$this->daoUser->addAdmin();
		}

        /**
        * Add new user
        */

        public function addUser(){
			$this->daoUser->addUser();
		}

        /**
        * Update admin user
        */

        public function updateUser(){
			$this->daoUser->updateUser();
		}

        /**
        * Update client
        */

        public function updateClient(){
			$this->daoUser->updateClient();
		}

        /**
        * Remove user
        */

        public function removeUser(){
			$this->daoUser->removeUser();
		}
    }
?>
