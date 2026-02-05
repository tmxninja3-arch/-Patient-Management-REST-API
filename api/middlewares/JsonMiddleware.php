<?php

/**
 * JSON Middleware
 * 
 * Purpose: Set common headers for JSON API responses
 * 
 * Middleware runs BEFORE the controller logic
 * 
 * Flow:
 *   Request → Middleware → Controller → Response
 */
class JsonMiddleware {
    
    /**
     * Apply JSON headers to response
     * 
     * Headers Set:
     * - Content-Type: application/json
     * - Access-Control-Allow-Origin: * (CORS)
     * - Access-Control-Allow-Methods: GET, POST, PUT, DELETE
     * - Access-Control-Allow-Headers: Content-Type
     */
    public static function handle() {
        
        // Set JSON content type
        header("Content-Type: application/json; charset=UTF-8");
        
        // CORS Headers (Allow frontend access)
        header("Access-Control-Allow-Origin: *");
        header("Access-Control-Allow-Methods: GET, POST, PUT, DELETE, OPTIONS");
        header("Access-Control-Allow-Headers: Content-Type, Authorization");
        
        // Handle preflight OPTIONS request
        if ($_SERVER['REQUEST_METHOD'] === 'OPTIONS') {
            http_response_code(200);
            exit();
        }
    }
}
?>