<?php
	// 'client' object
	class Client{
	 
		// database connection and table name
		private $conn;
		private $table_name = "client";
	 
		// object properties
		public $id;
		public $firstname;
		public $lastname;
		public $email;
		public $password;
		public $token;
		public $created;
	 
		// constructor
		public function __construct($db){
			$this->conn = $db;
		}
	 
		// create new client record
		function create(){
			date_default_timezone_set("Asia/Jakarta");
			$date = date("Y-m-d");
			
			// insert query
			$query = "INSERT INTO " . $this->table_name . "
					SET
						firstname = :firstname,
						lastname = :lastname,
						email = :email,
						password = :password,
						token = :token,
						created = :created";
		 
			// prepare the query
			$stmt = $this->conn->prepare($query);
		 
			// sanitize
			$this->firstname=htmlspecialchars(strip_tags($this->firstname));
			$this->lastname=htmlspecialchars(strip_tags($this->lastname));
			$this->email=htmlspecialchars(strip_tags($this->email));
			$this->password=htmlspecialchars(strip_tags($this->password));
			$this->token=htmlspecialchars(strip_tags($this->token));
			$this->created=htmlspecialchars(strip_tags($this->created));
		 
			// bind the values
			$stmt->bindParam(':firstname', $this->firstname);
			$stmt->bindParam(':lastname', $this->lastname);
			$stmt->bindParam(':email', $this->email);
		 
			// hash the password before saving to database
			$password_hash = password_hash($this->password, PASSWORD_BCRYPT);
			$stmt->bindParam(':password', $password_hash);
			
			// hash the password before saving to database
			$token_hash = md5($this->email);
			$stmt->bindParam(':token', $token_hash);
			
			$stmt->bindParam(':created', $date);
		 
			// execute the query, also check if query was successful
			if($stmt->execute()){
				return $token_hash;
			}
		 
			return '';
		}
		// check if given email exist in the database
		function emailExists(){
		 
			// query to check if email exists
			$query = "SELECT id, firstname, lastname, password, token
					FROM " . $this->table_name . "
					WHERE email = ?
					LIMIT 0,1";
		 
			// prepare the query
			$stmt = $this->conn->prepare( $query );
		 
			// sanitize
			$this->email=htmlspecialchars(strip_tags($this->email));
		 
			// bind given email value
			$stmt->bindParam(1, $this->email);
		 
			// execute the query
			$stmt->execute();
		 
			// get number of rows
			$num = $stmt->rowCount();
		 
			// if email exists, assign values to object properties for easy access and use for php sessions
			if($num>0){
		 
				// get record details / values
				$row = $stmt->fetch(PDO::FETCH_ASSOC);
		 
				// assign values to object properties
				$this->id = $row['id'];
				$this->firstname = $row['firstname'];
				$this->lastname = $row['lastname'];
				$this->password = $row['password'];
				$this->token = $row['token'];
		 
				// return true because email exists in the database
				return true;
			}
		 
			// return false if email does not exist in the database
			return false;
		}
		
		// check if given token exist in the database
		function tokenExists(){
		 
			// query to check if token exists
			$query = "SELECT id, firstname, lastname, password, email
					FROM " . $this->table_name . "
					WHERE token = ?
					LIMIT 0,1";
		 
			// prepare the query
			$stmt = $this->conn->prepare( $query );
		 
			// sanitize
			$this->token=htmlspecialchars(strip_tags($this->token));
		 
			// bind given token value
			$stmt->bindParam(1, $this->token);
		 
			// execute the query
			$stmt->execute();
		 
			// get number of rows
			$num = $stmt->rowCount();
		 
			// if token exists, assign values to object properties for easy access and use for php sessions
			if($num>0){
		 
				// get record details / values
				$row = $stmt->fetch(PDO::FETCH_ASSOC);
		 
				// assign values to object properties
				$this->id = $row['id'];
				$this->firstname = $row['firstname'];
				$this->lastname = $row['lastname'];
				$this->password = $row['password'];
				$this->email = $row['email'];
		 
				// return true because token exists in the database
				return true;
			}
		 
			// return false if token does not exist in the database
			return false;
		}
		 
		// update a client record
		public function update(){
		 
			// if password needs to be updated
			$password_set=!empty($this->password) ? ", password = :password" : "";
		 
			// if no posted password, do not update the password
			$query = "UPDATE " . $this->table_name . "
					SET
						firstname = :firstname,
						lastname = :lastname,
						email = :email
						{$password_set}
					WHERE id = :id";
		 
			// prepare the query
			$stmt = $this->conn->prepare($query);
		 
			// sanitize
			$this->firstname=htmlspecialchars(strip_tags($this->firstname));
			$this->lastname=htmlspecialchars(strip_tags($this->lastname));
			$this->email=htmlspecialchars(strip_tags($this->email));
		 
			// bind the values from the form
			$stmt->bindParam(':firstname', $this->firstname);
			$stmt->bindParam(':lastname', $this->lastname);
			$stmt->bindParam(':email', $this->email);
		 
			// hash the password before saving to database
			if(!empty($this->password)){
				$this->password=htmlspecialchars(strip_tags($this->password));
				$password_hash = password_hash($this->password, PASSWORD_BCRYPT);
				$stmt->bindParam(':password', $password_hash);
			}
		 
			// unique ID of record to be edited
			$stmt->bindParam(':id', $this->id);
		 
			// execute the query
			if($stmt->execute()){
				return true;
			}
		 
			return false;
		}
	}
?>