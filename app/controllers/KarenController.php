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
        error_log("Received POST data: " . var_export($rawData, true));
    
        // Decode the JSON
        $data = json_decode($rawData, true);
    
        // Check if decoding was successful
        if (is_null($data)) {
            header('Content-Type: application/json');
            echo json_encode(['error' => 'Invalid JSON format']);
            exit;
        }
    
        if ($_SERVER['REQUEST_METHOD'] === 'POST' && isset($data['message']) && !empty($data['message'])) {
            $userMessage = $data['message'];
    
            // Simulate a chatbot response for now
            $chatbotResponse = $this->model->getChatbotResponse($userMessage);
    
            // Log the response before sending
            error_log("Sending JSON response: " . json_encode(['response' => $chatbotResponse]));
    
            // Send the JSON response
            header('Content-Type: application/json');
            echo json_encode(['response' => $chatbotResponse]);
            exit;
        } else {
            header('Content-Type: application/json');
            echo json_encode(['error' => 'Invalid request']);
            exit;
        }
    }           

    // Méthode pour obtenir la liste des incidents au format JSON
    public function getIncidentList() {
        // Obtenir la liste des incidents depuis le modèle
        $incidents = $this->model->getIncidentsByCategory();

        // Vérifier que la réponse est bien formatée
        if (is_string($incidents)) {
            // Encodage JSON et vérification d'erreurs
            $jsonIncidents = json_encode(['incidents' => $incidents]);
            if ($jsonIncidents === false) {
                header('Content-Type: application/json', true, 500);
                echo json_encode(['error' => 'Erreur de réponse : échec de l\'encodage JSON']);
                exit;
            }
            // Envoyer la réponse JSON
            header('Content-Type: application/json');
            echo $jsonIncidents;
        } else {
            // Envoyer une erreur si le format de réponse n'est pas correct
            header('Content-Type: application/json', true, 500);
            echo json_encode(['error' => 'Internal server error: invalid response format']);
        }

        exit;
    }

    public function getAppelList() {
        // Fetch "Appels" data from the model
        $appels = $this->model->getAppelsByCategory();
    
        // Encode the result as JSON
        if (is_string($appels)) {
            $jsonAppels = json_encode(['appels' => $appels]);
            if ($jsonAppels === false) {
                header('Content-Type: application/json', true, 500);
                echo json_encode(['error' => 'Erreur de réponse : échec de l\'encodage JSON']);
                exit;
            }
            header('Content-Type: application/json');
            echo $jsonAppels;
        } else {
            header('Content-Type: application/json', true, 500);
            echo json_encode(['error' => 'Internal server error: invalid response format']);
        }
    
        exit;
    }  
    
    public function getDemandesList() {
        // Fetch "Appels" data from the model
        $demandes = $this->model->getDemandesByCategory();
    
        // Encode the result as JSON
        if (is_string($appels)) {
            $jsonDemandes = json_encode(['demandes' => $demandes]);
            if ($jsonDemandes === false) {
                header('Content-Type: application/json', true, 500);
                echo json_encode(['error' => 'Erreur de réponse : échec de l\'encodage JSON']);
                exit;
            }
            header('Content-Type: application/json');
            echo $jsonDemandes;
        } else {
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
        }
    }

    // Nouvelle méthode pour rendre la vue HTML
    public function renderView() {
        // Appeler la méthode render() de KarenView pour afficher la vue
        $this->view->render();
    }
}