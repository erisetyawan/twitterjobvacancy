<?php
class Job{
 
    // database connection and table name
    private $conn;
    private $table_name = "job";
 
    // object properties
    public $id;
    public $id_user;
    public $text;
    public $created;
 
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
					id=:id, id_user=:id_user, text=:text, created=:created";
	 
		// prepare query
		$stmt = $this->conn->prepare($query);
	 
		// sanitize
		$this->id=htmlspecialchars(strip_tags($this->id));
		$this->id_user=htmlspecialchars(strip_tags($this->id_user));
		$this->text=htmlspecialchars(strip_tags($this->text));
		$this->created=htmlspecialchars(strip_tags($this->created));
	 
		// bind values
		$stmt->bindParam(":id", $this->id);
		$stmt->bindParam(":id_user", $this->id_user);
		$stmt->bindParam(":text", $this->text);
		$stmt->bindParam(":created", $this->created);
	 
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
					id_user = :id_user,
					text = :text,
				WHERE
					id = :id";
	 
		// prepare query statement
		$stmt = $this->conn->prepare($query);
	 
		// sanitize
		$this->id_user=htmlspecialchars(strip_tags($this->id_user));
		$this->text=htmlspecialchars(strip_tags($this->text));
		$this->id=htmlspecialchars(strip_tags($this->id));
	 
		// bind new values
		$stmt->bindParam(':id_user', $this->id_user);
		$stmt->bindParam(':text', $this->text);
		$stmt->bindParam(':id', $this->id);
	 
		// execute the query
		if($stmt->execute()){
			return true;
		}
	 
		return false;
	}
		
	// read jobs
	function read(){
	 
		// select all query
		$query = "SELECT
					p.id, u.name, u.location, p.text, p.created
				FROM
					" . $this->table_name . " p, user u
				WHERE
					p.id_user = u.id
				ORDER BY
					p.created DESC";
	 
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
					p.id, u.name, u.location, p.text, p.created
				FROM
					" . $this->table_name . " p, user u
				WHERE
					p.id_user = u.id AND p.id = ?
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
		$this->text = $row['text'];
		$this->created = $row['created'];
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
					p.id, u.name, u.location, p.text, p.created
				FROM
					" . $this->table_name . " p, user u
				WHERE
					p.id_user = u.id AND (u.name LIKE ? OR u.location LIKE ? OR p.text LIKE ?)
				ORDER BY
					p.created DESC";
	 
		// prepare query statement
		$stmt = $this->conn->prepare($query);
	 
		// sanitize
		$keywords=htmlspecialchars(strip_tags($keywords));
		$keywords = "%{$keywords}%";
	 
		// bind
		$stmt->bindParam(1, $keywords);
		$stmt->bindParam(2, $keywords);
		$stmt->bindParam(3, $keywords);
	 
		// execute query
		$stmt->execute();
	 
		return $stmt;
	}
}
?>