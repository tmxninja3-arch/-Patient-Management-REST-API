<?php


/**
 * Responsibility:
 * - All database operations
 * - SQL queries
 * - Data handling
 * Flow: Connect → Prepare → Bind → Execute → Return
 */
class Patient {
    
    private $conn;
    private $table = "patients";

    public function __construct($db) {
        $this->conn = $db;
    }
    
    public function getAllPatients() {
        
        // SQL Query
        $query = "SELECT id, name, age, gender, phone, created_at 
                  FROM " . $this->table . " 
                  ORDER BY created_at DESC";
        
        // Execute query
        $result = $this->conn->query($query);
        
        // Fetch all rows as array
        $patients = [];
        
        if ($result && $result->num_rows > 0) {
            while ($row = $result->fetch_assoc()) {
                $patients[] = $row;
            }
        }
        
        return $patients;
    }
    
    public function getPatientById($id) {
        
        $query = "SELECT id, name, age, gender, phone, created_at 
                  FROM " . $this->table . " 
                  WHERE id = ?";
        
        $stmt = $this->conn->prepare($query);
        
        // Bind parameter
        $stmt->bind_param("i", $id);
        
        // Execute
        $stmt->execute();
        
        // Get result
        $result = $stmt->get_result();
        
        if ($result->num_rows > 0) {
            return $result->fetch_assoc();
        }
        
        return null;
    }
    
    public function createPatient($data) {
        
        $query = "INSERT INTO " . $this->table . " 
                  (name, age, gender, phone) 
                  VALUES (?, ?, ?, ?)";
        
        $stmt = $this->conn->prepare($query);
        
        if (!$stmt) {
            return false;
        }
        
        // Bind parameters

        $stmt->bind_param(
            "siss",
            $data['name'],
            $data['age'],
            $data['gender'],
            $data['phone']
        );
        
        // Execute and return result
        if ($stmt->execute()) {
            return $this->conn->insert_id; // Return new ID
        }
        
        return false;
    }
    
   
    public function updatePatient($id, $data) {
        
        // First check if patient exists
        if (!$this->getPatientById($id)) {
            return null; 
        }
        
        // Prepare UPDATE statement
        $query = "UPDATE " . $this->table . " 
                  SET name = ?, age = ?, gender = ?, phone = ? 
                  WHERE id = ?";
        
        $stmt = $this->conn->prepare($query);
        
        if (!$stmt) {
            return false;
        }
        
        // Bind parameters
        $stmt->bind_param(
            "sissi",
            $data['name'],
            $data['age'],
            $data['gender'],
            $data['phone'],
            $id
        );
        
        return $stmt->execute();
    }
    
    public function deletePatient($id) {
        
        // First check if patient exists
        if (!$this->getPatientById($id)) {
            return null; 
        }
        
        // Prepare DELETE statement
        $query = "DELETE FROM " . $this->table . " WHERE id = ?";
        
        $stmt = $this->conn->prepare($query);
        
        if (!$stmt) {
            return false;
        }
        
        // Bind parameter
        $stmt->bind_param("i", $id);
        
        return $stmt->execute();
    }
}
?>