<?php
namespace Controllers;

use Models\KarenModel;
use Views\KarenView;

class KarenController {
    protected $model;
    protected $view;

    public function __construct() {
        // Initialisation du modèle
        $this->model = new KarenModel('/var/www/html/karenbot/assets/docs/ITASM.xlsm');
        $this->view = new KarenView();
    }

    // Méthode pour gérer les requêtes AJAX pour le chat et renvoyer uniquement du JSON
    public function handleChat() {
        // Read raw POST data (JSON body)
        $rawData = file_get_contents("php://input");
    
        // Decode the JSON
        $data = json_decode($rawData, true);
    
        // Check if the message key exists and is not empty
        if ($_SERVER['REQUEST_METHOD'] === 'POST' && isset($data['message']) && !empty($data['message'])) {
            $userMessage = $data['message'];
    
            // Get the chatbot response from the model
            $chatbotResponse = $this->model->getChatbotResponse($userMessage);
    
            // Send the JSON response
            header('Content-Type: application/json');
            echo json_encode(['response' => $chatbotResponse]);
            exit;
        } else {
            // Handle the case where the message key is missing or invalid request
            header('Content-Type: application/json', true, 400);
            echo json_encode(['error' => 'Invalid request or missing message.']);
            exit;
        }
    }

    // Méthode pour obtenir la liste des incidents au format JSON
    public function getIncidentList() {
        // Obtenir la liste des incidents depuis le modèle
        $incidents = $this->model->getIncidentsByCategory();

        // Return the incidents in JSON format for Rasa or frontend consumption
        if (is_string($incidents)) {
            // Ensure JSON encoding
            $jsonIncidents = json_encode(['incidents' => $incidents]);
            if ($jsonIncidents === false) {
                // Handle JSON encoding error
                header('Content-Type: application/json', true, 500);
                echo json_encode(['error' => 'Erreur de réponse : échec de l\'encodage JSON']);
                exit;
            }
            // Send JSON response
            header('Content-Type: application/json');
            echo $jsonIncidents;
        } else {
            // Handle incorrect response format
            header('Content-Type: application/json', true, 500);
            echo json_encode(['error' => 'Internal server error: invalid response format']);
        }

        exit;
    }

    // Méthode pour tester la connexion via GET
    public function testConnection() {
        if ($_SERVER['REQUEST_METHOD'] === 'GET') {
            header('Content-Type: application/json');
            echo json_encode(['message' => 'API KarenModel en ligne. Envoyez une requête POST pour obtenir une réponse.']);
            exit;
        } else {
            // Handle invalid request method
            header('Content-Type: application/json', true, 405);
            echo json_encode(['error' => 'Invalid request method.']);
            exit;
        }
    }

    // Nouvelle méthode pour rendre la vue HTML
    public function renderView() {
        // Appeler la méthode render() de KarenView pour afficher la vue
        $this->view->render();
    }
}