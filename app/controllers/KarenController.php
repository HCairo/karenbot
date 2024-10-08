<?php
namespace Controllers;

use Models\KarenModel;
use Views\KarenView;

class KarenController {
    protected $model;
    protected $view;

    const INCIDENTS_KEY = 'incidents';
    const APPELS_KEY = 'appels';
    const DEMANDES_KEY = 'demandes';

    public function __construct() {
        // Initialize the model
        $this->model = new KarenModel('/var/www/html/karenbot/assets/docs/ITASM.xlsm');
        $this->view = new KarenView();
    }

    // Method to handle AJAX chat requests and return JSON only
    public function handleChat() {
        $data = $this->parseRequest();
        
        if (!$data) {
            return $this->sendErrorResponse('Invalid JSON format');
        }
    
        if (empty($data['message'])) {
            return $this->sendErrorResponse('Invalid request');
        }
    
        $userMessage = $data['message'];
        $chatbotResponse = $this->model->getChatbotResponse($userMessage);
        
        return $this->sendJsonResponse(['response' => $chatbotResponse]);
    }
    
    private function parseRequest() {
        $rawData = file_get_contents("php://input");
        error_log("Received POST data: " . var_export($rawData, true));
        return json_decode($rawData, true);
    }
    
    private function sendJsonResponse($data) {
        header('Content-Type: application/json');
        echo json_encode($data);
        exit;
    }
    
    private function sendErrorResponse($message) {
        header('Content-Type: application/json', true, 400); // Use a proper status code
        echo json_encode(['error' => $message]);
        exit;
    }             

    // Generic method to handle incident, appel, and demande lists
    private function getList($methodName, $key) {
        $data = $this->model->$methodName();
        if (is_string($data)) {
            return $this->sendJsonResponse([$key => $data]);
        } else {
            return $this->sendErrorResponse('Internal server error: invalid response format');
        }
    }

    public function getIncidentList() {
        return $this->getList('getIncidentsByCategory', self::INCIDENTS_KEY);
    }

    public function getAppelList() {
        return $this->getList('getAppelsByCategory', self::APPELS_KEY);
    }  
    
    public function getDemandesList() {
        return $this->getList('getDemandesByCategory', self::DEMANDES_KEY);
    } 

    // Method to test the connection via GET
    public function testConnection() {
        if ($_SERVER['REQUEST_METHOD'] === 'GET') {
            $this->sendJsonResponse(['message' => 'API KarenModel online. Send a POST request for a response.']);
        }
    }

    // New method to render the HTML view
    public function renderView() {
        $this->view->render();
    }
}
