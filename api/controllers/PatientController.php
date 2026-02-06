<?php

/**
 * - Receive HTTP requests
 * - Validate input
 * - Call Model methods
 * - Send Response
 */
class PatientController {
    
    private $model;

    public function __construct($db) {
        $this->model = new Patient($db);
    }
    
    /**
     * GET /api/patients
     * Get all patients
     */
    public function index() {
        
        $patients = $this->model->getAllPatients();
        
        Response::success(
            "Patients retrieved successfully",
            $patients
        );
    }
    
    /**
     * GET /api/patients/{id}
     * Get patient by ID
     */
    public function show($id) {
        
        // Validate ID
        if (!$id || !is_numeric($id)) {
            Response::badRequest("Invalid patient ID");
        }
        
        // Call model to get patient
        $patient = $this->model->getPatientById($id);
        
        // Check if patient exists
        if (!$patient) {
            Response::notFound("Patient not found");
        }
        
        // Send success response
        Response::success(
            "Patient retrieved successfully",
            $patient
        );
    }
    
    /**
     * POST /api/patients
     * Create new patient
     */
    public function store() {
        
        // Get JSON input from request body
        $input = json_decode(file_get_contents("php://input"), true);
        
        // Validate input exists
        if (!$input) {
            Response::badRequest("Invalid JSON input");
        }
        
        // Validate required fields
        $required = ['name', 'age', 'gender', 'phone'];
        $errors = $this->validateFields($input, $required);
        
        if (!empty($errors)) {
            Response::json(400, false, "Validation failed", $errors);
        }
        
        // Validate data types
        if (!is_numeric($input['age']) || $input['age'] < 0 || $input['age'] > 150) {
            Response::badRequest("Invalid age value");
        }
        
        // Validate gender
        $validGenders = ['Male', 'Female', 'Other'];
        if (!in_array($input['gender'], $validGenders)) {
            Response::badRequest("Gender must be Male, Female, or Other");
        }
        
        // Call model to create patient
        $newId = $this->model->createPatient($input);
        
        if (!$newId) {
            Response::serverError("Failed to create patient");
        }
        
        // Get created patient data
        $patient = $this->model->getPatientById($newId);
        
        // Send success response
        Response::created(
            "Patient created successfully",
            $patient
        );
    }
    
    /**
     * PUT /api/patients/{id}
     * Update existing patient (full update)
     */
    public function update($id) {
        
        // Validate ID
        if (!$id || !is_numeric($id)) {
            Response::badRequest("Invalid patient ID");
        }
        
        // Get JSON input
        $input = json_decode(file_get_contents("php://input"), true);
        
        // Validate input
        if (!$input) {
            Response::badRequest("Invalid JSON input");
        }
        
        // Validate required fields
        $required = ['name', 'age', 'gender', 'phone'];
        $errors = $this->validateFields($input, $required);
        
        if (!empty($errors)) {
            Response::json(400, false, "Validation failed", $errors);
        }
        
        // Validate age
        if (!is_numeric($input['age']) || $input['age'] < 0 || $input['age'] > 150) {
            Response::badRequest("Invalid age value");
        }
        
        // Call model to update
        $result = $this->model->updatePatient($id, $input);
        
        if ($result === null) {
            Response::notFound("Patient not found");
        }
        
        if (!$result) {
            Response::serverError("Failed to update patient");
        }
        
        // Get updated patient data
        $patient = $this->model->getPatientById($id);
        
        // Send success response
        Response::success(
            "Patient updated successfully",
            $patient
        );
    }
    
    /**
     * PATCH /api/patients/{id}
     * Partially update existing patient
     */
    public function patch($id) {
        
        // Validate ID
        if (!$id || !is_numeric($id)) {
            Response::badRequest("Invalid patient ID");
        }
        
        // Get JSON input
        $input = json_decode(file_get_contents("php://input"), true);
        
        // Validate input
        if (!$input) {
            Response::badRequest("Invalid JSON input");
        }
        
        // Validate provided fields (partial, so no required fields enforced)
        $allowedFields = ['name', 'age', 'gender', 'phone'];
        $errors = [];
        foreach ($input as $field => $value) {
            if (!in_array($field, $allowedFields)) {
                $errors[] = "Invalid field: $field";
            } elseif (empty(trim($value))) {
                $errors[] = "$field cannot be empty";
            }
        }
        
        if (!empty($errors)) {
            Response::json(400, false, "Validation failed", $errors);
        }
        
        // Validate age if provided
        if (isset($input['age']) && (!is_numeric($input['age']) || $input['age'] < 0 || $input['age'] > 150)) {
            Response::badRequest("Invalid age value");
        }
        
        // Validate gender if provided
        if (isset($input['gender'])) {
            $validGenders = ['Male', 'Female', 'Other'];
            if (!in_array($input['gender'], $validGenders)) {
                Response::badRequest("Gender must be Male, Female, or Other");
            }
        }
        
        // Call model to partially update
        $result = $this->model->updatePatientPartial($id, $input);
        
        if ($result === null) {
            Response::notFound("Patient not found");
        }
        
        if (!$result) {
            Response::serverError("Failed to update patient");
        }
        
        // Get updated patient data
        $patient = $this->model->getPatientById($id);
        
        // Send success response
        Response::success(
            "Patient updated successfully",
            $patient
        );
    }
    
    /**
     * DELETE /api/patients/{id}
     * Delete patient
     */
    public function destroy($id) {
        
        // Validate ID
        if (!$id || !is_numeric($id)) {
            Response::badRequest("Invalid patient ID");
        }
        
        // Call model to delete
        $result = $this->model->deletePatient($id);
        
        if ($result === null) {
            Response::notFound("Patient not found");
        }
        
        if (!$result) {
            Response::serverError("Failed to delete patient");
        }
        
        // Send success response
        Response::success("Patient deleted successfully");
    }
    
    /**
     * Validate required fields
     */
    private function validateFields($input, $required) {
        $errors = [];
        
        foreach ($required as $field) {
            if (!isset($input[$field]) || empty(trim($input[$field]))) {
                $errors[] = "$field is required";
            }
        }
        
        return $errors;
    }
}
?>