<?php
// api/index.php

/**
 * This file:
 * 1. Receives ALL API requests
 * 2. Applies middleware
 * 3. Parses URL to determine endpoint
 * 4. Routes to correct controller method
 
 *   Client → .htaccess → index.php → Middleware → Controller → Response
 */


require_once 'config/database.php';
require_once 'middlewares/JsonMiddleware.php';
require_once 'helpers/Response.php';
require_once 'models/Patient.php';
require_once 'controllers/PatientController.php';


// APPLY MIDDLEWARE

JsonMiddleware::handle();

// DATABASE CONNECTION

$database = new Database();
$db = $database->getConnection();

// Check connection
if (!$db) {
    Response::serverError("Database connection failed");
}

// GET REQUEST INFORMATION

// Get the request URI from .htaccess rewrite
$request = isset($_GET['request']) ? $_GET['request'] : '';
$request = rtrim($request, '/');
$parts = explode('/', $request);

// Get HTTP method
$method = $_SERVER['REQUEST_METHOD'];


// Check if request is for patients endpoint
if ($parts[0] === 'patients') {
    
    // Create controller instance
    $controller = new PatientController($db);
    
    // Get patient ID if provided
    $id = isset($parts[1]) ? (int)$parts[1] : null;
    
    switch ($method) {
        
      
        case 'GET':
            if ($id) {
               
                $controller->show($id);
            } else {
                
                $controller->index();
            }
            break;
        
        case 'POST':
            if ($id) {
                Response::badRequest("Cannot POST to specific patient ID");
            }
            
            $controller->store();
            break;
        
      
        case 'PUT':
            if (!$id) {
                Response::badRequest("Patient ID required for update");
            }
            
            $controller->update($id);
            break;
        
       
        case 'PATCH':
            if (!$id) {
                Response::badRequest("Patient ID required for partial update");
            }
           
            $controller->patch($id);
            break;
        
        
        case 'DELETE':
            if (!$id) {
                Response::badRequest("Patient ID required for delete");
            }
            
            $controller->destroy($id);
            break;
        

        default:
            Response::json(405, false, "Method not allowed");
    }
    
} else {
    
    Response::notFound("Endpoint not found. Available: /api/patients");
}