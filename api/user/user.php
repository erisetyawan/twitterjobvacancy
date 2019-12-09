<?php
class User{
 
    // database connection and table name
    private $conn;
    private $table_name = "user";
 
    // object properties
    public $id;
    public $name;
    public $location;
 
    // constructor with $db as database connection
    public function __construct($db){
        $this->conn = $db;
    }
	
	// create product
	function create(){
	 
		// query to insert record
		$query = "INSERT INTO
					" . $this->table_name . "
				SET
					name=:name, location=:location, id=:id";
	 
		// prepare query
		$stmt = $this->conn->prepare($query);
	 
		// sanitize
		$this->name=htmlspecialchars(strip_tags($this->name));
		$this->location=htmlspecialchars(strip_tags($this->location));
		$this->id=htmlspecialchars(strip_tags($this->id));
	 
		// bind values
		$stmt->bindParam(":name", $this->name);
		$stmt->bindParam(":location", $this->location);
		$stmt->bindParam(":id", $this->id);
	 
		// execute query
		if($stmt->execute()){
			return true;
		}
	 
		return false;
		 
	}
	
	// update the product
	function update(){
	 
		// update query
		$query = "UPDATE
					" . $this->table_name . "
				SET
					name = :name,
					location = :location,
				WHERE
					id = :id";
	 
		// prepare query statement
		$stmt = $this->conn->prepare($query);
	 
		// sanitize
		$this->name=htmlspecialchars(strip_tags($this->name));
		$this->location=htmlspecialchars(strip_tags($this->location));
		$this->id=htmlspecialchars(strip_tags($this->id));
	 
		// bind new values
		$stmt->bindParam(':name', $this->name);
		$stmt->bindParam(':location', $this->location);
		$stmt->bindParam(':id', $this->id);
	 
		// execute the query
		if($stmt->execute()){
			return true;
		}
	 
		return false;
	}
		
	// read users
	function read(){
	 
		// select all query
		$query = "SELECT
					id, name, location
				FROM
					" . $this->table_name . "
				ORDER BY
					id DESC";
	 
		// prepare query statement
		$stmt = $this->conn->prepare($query);
	 
		// execute query
		$stmt->execute();
	 
		return $stmt;
	}
	
	// used when filling up the update product form
	function readOne(){
	 
		// query to read single record
		$query = "SELECT
					id, name, location
				FROM
					" . $this->table_name . "
				WHERE
					id = ?
				LIMIT
					0,1";
	 
		// prepare query statement
		$stmt = $this->conn->prepare( $query );
	 
		// bind id of product to be updated
		$stmt->bindParam(1, $this->id);
	 
		// execute query
		$stmt->execute();
	 
		// get retrieved row
		$row = $stmt->fetch(PDO::FETCH_ASSOC);
	 
		// set values to object properties
		$this->name = $row['name'];
		$this->location = $row['location'];
		$this->id = $row['id'];
	}
	
	// delete the product
	function delete(){
	 
		// delete query
		$query = "DELETE FROM " . $this->table_name . " WHERE id = ?";
	 
		// prepare query
		$stmt = $this->conn->prepare($query);
	 
		// sanitize
		$this->id=htmlspecialchars(strip_tags($this->id));
	 
		// bind id of record to delete
		$stmt->bindParam(1, $this->id);
	 
		// execute query
		if($stmt->execute()){
			return true;
		}
	 
		return false;
		 
	}
	
	// search products
	function search($keywords){
	 
		// select all query
		$query = "SELECT
					id, name, location
				FROM
					" . $this->table_name . "
				WHERE
					name LIKE ? OR location LIKE ?
				ORDER BY
					id DESC";
	 
		// prepare query statement
		$stmt = $this->conn->prepare($query);
	 
		// sanitize
		$keywords=htmlspecialchars(strip_tags($keywords));
		$keywords = "%{$keywords}%";
	 
		// bind
		$stmt->bindParam(1, $keywords);
		$stmt->bindParam(2, $keywords);
	 
		// execute query
		$stmt->execute();
	 
		return $stmt;
	}
}
?>