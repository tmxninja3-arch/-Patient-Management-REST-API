<?php
// api/index.php

/**
 * This file:
 * 1. Receives ALL API requests
 * 2. Applies middleware
 * 3. Parses URL to determine endpoint
 * 4. Routes to correct controller method
 * 
 * Request Flow:
 *   Client → .htaccess → index.php → Middleware → Controller → Response
 */

// Config
require_once 'config/database.php';

// Middleware
require_once 'middlewares/JsonMiddleware.php';

// Helpers
require_once 'helpers/Response.php';

// Models
require_once 'models/Patient.php';

// Controllers
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

// Remove trailing slash
$request = rtrim($request, '/');

// Split into parts
$parts = explode('/', $request);

// Get HTTP method
$method = $_SERVER['REQUEST_METHOD'];


/**
 * Route Table:
 * 
 * GET    /api/patients        → index()
 * GET    /api/patients/{id}   → show($id)
 * POST   /api/patients        → store()
 * PUT    /api/patients/{id}   → update($id)
 * DELETE /api/patients/{id}   → destroy($id)
 */

// Check if request is for patients endpoint
if ($parts[0] === 'patients') {
    
    // Create controller instance
    $controller = new PatientController($db);
    
    // Get patient ID if provided
    $id = isset($parts[1]) ? (int)$parts[1] : null;
    
    // Route based on HTTP method
    switch ($method) {
        
        // GET Request
        case 'GET':
            if ($id) {
                // GET /api/patients/{id}
                $controller->show($id);
            } else {
                // GET /api/patients
                $controller->index();
            }
            break;
        
        // POST Request
        case 'POST':
            if ($id) {
                Response::badRequest("Cannot POST to specific patient ID");
            }
            // POST /api/patients
            $controller->store();
            break;
        
        // PUT Request
        case 'PUT':
            if (!$id) {
                Response::badRequest("Patient ID required for update");
            }
            // PUT /api/patients/{id}
            $controller->update($id);
            break;
        
        // DELETE Request
        case 'DELETE':
            if (!$id) {
                Response::badRequest("Patient ID required for delete");
            }
            // DELETE /api/patients/{id}
            $controller->destroy($id);
            break;
        
        // Unsupported Method
        default:
            Response::json(405, false, "Method not allowed");
    }
    
} else {
    // Unknown endpoint
    Response::notFound("Endpoint not found. Available: /api/patients");
}
?>