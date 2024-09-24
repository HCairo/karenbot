<?php
namespace Controllers;

use Models\KarenModel;
use Views\KarenView;

class KarenController {
    protected $model;
    protected $view;

    public function __construct($excelFilePath) {
        // Passer le chemin du fichier Excel au modèle
        $this->model = new KarenModel($excelFilePath);
        $this->view = new KarenView();
    }

    // Handles the main chatbot interaction
    public function handleChat() {
        if (isset($_POST['message']) && !empty($_POST['message'])) {
            $userMessage = $_POST['message'];
            
            // Get the chatbot response from the model
            $chatbotResponse = $this->model->getChatbotResponse($userMessage);
    
            // Return the response for the AJAX call
            echo $chatbotResponse;
            exit;
        } else {
            // Render empty chat if no message is sent yet
            $this->view->render();
        }
    }
}
