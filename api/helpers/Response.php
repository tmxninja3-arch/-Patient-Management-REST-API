<?php

/** 
 * Responsibility: 
 * - Format data as JSON
 * - Set HTTP status codes
 * - Send response to client
 * 
 * Flow: Receive → Format → Headers → JSON → Output
 */
class Response {

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
        echo json_encode($response, JSON_PRETTY_PRINT);
        exit();
    }
    
     //Success Response (200 OK)
    public static function success($message, $data = null) {
        self::json(200, true, $message, $data);
    }
   
     //Created Response (201 Created) 
    public static function created($message, $data = null) {
        self::json(201, true, $message, $data);
    }
    
    //Bad Request Response (400)
    public static function badRequest($message) {
        self::json(400, false, $message);
    }
    
   // Not Found Response (404)
    public static function notFound($message) {
        self::json(404, false, $message);
    }
    
   //Server Error Response (500)
    public static function serverError($message) {
        self::json(500, false, $message);
    }
}
?>