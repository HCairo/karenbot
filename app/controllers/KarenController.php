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
        $this->model = new KarenModel('/var/www/html/karenbot/assets/docs/ITASM.xlsm');
        $this->view = new KarenView();
    }

    public function handleChat() {
        $data = $this->parseRequest();
        if (!$data || empty($data['message'])) {
            return $this->sendErrorResponse('Invalid request');
        }

        $chatbotResponse = $this->model->getChatbotResponse($data['message']);
        return $this->sendJsonResponse(['response' => $chatbotResponse]);
    }

    private function parseRequest() {
        $rawData = file_get_contents("php://input");
        error_log("Received POST data: " . var_export($rawData, true));
        return json_decode($rawData, true);
    }

    private function sendJsonResponse($data, $statusCode = 200) {
        http_response_code($statusCode);
        header('Content-Type: application/json');
        echo json_encode($data);
        exit;
    }

    private function sendErrorResponse($message) {
        return $this->sendJsonResponse(['error' => $message], 400);
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

    private function getList($methodName, $key) {
        $data = $this->model->$methodName();
        return is_string($data) 
            ? $this->sendJsonResponse([$key => $data]) 
            : $this->sendErrorResponse('Internal server error: invalid response format');
    }

    public function testConnection() {
        if ($_SERVER['REQUEST_METHOD'] === 'GET') {
            $this->sendJsonResponse(['message' => 'API KarenModel online. Send a POST request for a response.']);
        }
    }

    public function renderView() {
        $this->view->render();
    }
}