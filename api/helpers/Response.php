<?php

/**
 * Response Helper Class
 * 
 * This is the VIEW layer in MVC
 * 
 * Responsibility: 
 * - Format data as JSON
 * - Set HTTP status codes
 * - Send response to client
 * 
 * Flow: Receive → Format → Headers → JSON → Output
 */
class Response {
    
    /**
     * Send JSON response
     * 
     * @param int    $statusCode  HTTP status code (200, 201, 400, 404, etc.)
     * @param bool   $status      Success or failure status
     * @param string $message     Response message
     * @param mixed  $data        Response data (optional)
     */
    public static function json($statusCode, $status, $message, $data = null) {
        
        // Set HTTP status code
        http_response_code($statusCode);
        
        // Build response array
        $response = [
            "status" => $status,
            "message" => $message
        ];
        
        // Add data if provided
        if ($data !== null) {
            $response["data"] = $data;
        }
        
        // Encode and output JSON
        echo json_encode($response, JSON_PRETTY_PRINT);
        
        // Stop further execution
        exit();
    }
    
    /**
     * Success Response (200 OK)
     */
    public static function success($message, $data = null) {
        self::json(200, true, $message, $data);
    }
    
    /**
     * Created Response (201 Created)
     */
    public static function created($message, $data = null) {
        self::json(201, true, $message, $data);
    }
    
    /**
     * Bad Request Response (400)
     */
    public static function badRequest($message) {
        self::json(400, false, $message);
    }
    
    /**
     * Not Found Response (404)
     */
    public static function notFound($message) {
        self::json(404, false, $message);
    }
    
    /**
     * Server Error Response (500)
     */
    public static function serverError($message) {
        self::json(500, false, $message);
    }
}
?>