<?php
namespace Models;

class KarenModel {

    public function getChatbotResponse($message) {
        // Rasa server URL
        $url = 'http://localhost:5005/webhooks/rest/webhook';

        // Prepare the data for sending
        $data = ['message' => $message];

        // Initialize cURL session
        $ch = curl_init($url);
        curl_setopt($ch, CURLOPT_RETURNTRANSFER, true);
        curl_setopt($ch, CURLOPT_POST, true);
        curl_setopt($ch, CURLOPT_POSTFIELDS, json_encode($data));
        curl_setopt($ch, CURLOPT_HTTPHEADER, ['Content-Type: application/json']);
        
        // Execute cURL request
        $response = curl_exec($ch);
        curl_close($ch);

        // Decode the response from Rasa (assumes JSON)
        $responseDecoded = json_decode($response, true);
        
        // Extract chatbot's reply
        return $responseDecoded[0]['text'] ?? 'Désolé, je n\'ai pas compris.';
    }
}
