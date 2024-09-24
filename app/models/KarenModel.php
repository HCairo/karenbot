<?php
namespace Models;

class KarenModel {

    private $excelModel;

    public function __construct($excelFilePath) {
        $this->excelModel = new ExcelModel($excelFilePath);
    }

    public function getChatbotResponse($message) {
        // Envoyer la requête à Rasa pour obtenir l'intention
        $intent = $this->getRasaIntent($message);

        // Utiliser l'intention pour récupérer une réponse depuis le fichier Excel
        return $this->excelModel->getResponseFromExcel($intent);
    }

    // Méthode pour envoyer un message à Rasa et obtenir l'intention
    private function getRasaIntent($message) {
        // Rasa server URL
        $url = 'http://localhost:5005/model/parse';

        // Préparer les données pour Rasa
        $data = ['text' => $message];

        // Initialiser une session cURL pour envoyer la requête
        $ch = curl_init($url);
        curl_setopt($ch, CURLOPT_RETURNTRANSFER, true);
        curl_setopt($ch, CURLOPT_POST, true);
        curl_setopt($ch, CURLOPT_POSTFIELDS, json_encode($data));
        curl_setopt($ch, CURLOPT_HTTPHEADER, ['Content-Type: application/json']);

        // Exécuter la requête cURL
        $response = curl_exec($ch);
        curl_close($ch);

        // Décoder la réponse de Rasa (assumant que c'est du JSON)
        $responseDecoded = json_decode($response, true);

        // Récupérer l'intention détectée par Rasa
        return $responseDecoded['intent']['name'] ?? 'unknown';
    }
}
