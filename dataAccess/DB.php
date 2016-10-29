<?php
    ini_set('display_errors',1);
    error_reporting( E_ALL & ~E_DEPRECATED & ~E_STRICT );
    require_once("Rest.inc.php");

    class DB extends REST{
        public $data = "";

        const DB_SERVER = "localhost";
        const DB_USER = "root";
        const DB_PASSWORD = '';
        const DB = "muebleria";
        public $mysqli_connect = NULL;
        public function __construct(){			// Init parent contructor
            $this->dbConnect();					// Initiate Database connection
        }

        /*
        *  Connect to Database
        */
        public function dbConnect(){
            $this->mysqli_connect = mysqli_connect(self::DB_SERVER, self::DB_USER, self::DB_PASSWORD);
            mysqli_set_charset($this->mysqli_connect, "utf8");
            if($this->mysqli_connect)
            	mysqli_select_db($this->mysqli_connect, self::DB);
            return $this->mysqli_connect;
        }

        public function get($query){
            $r = mysqli_query( $this->mysqli_connect, $query);

            if( $r === false ) {
            die( print_r( mysqli_error($this->mysqli_connect), true));
            }
            $result = array();
            while( $row = mysqli_fetch_array($r, MYSQLI_ASSOC) ) {
            array_push($result, $row);
            }
            $this->response($this->json($result), 200);
        }

        public function post($query) {
            $r = mysqli_query( $this->mysqli_connect, $query);
            if( $r === false ) {
                die( print_r( mysqli_error($this->mysqli_connect), true));
            }
            $this->response("", 200);
        }

		public function json($data){
			if(is_array($data)){
				return json_encode($data);
			}
		}
    }
?>
