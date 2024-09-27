<?php
namespace Models;

use PhpOffice\PhpSpreadsheet\IOFactory;

class KarenModel {
    protected $spreadsheet;

    public function __construct($excelFilePath) {
        // Charger le fichier Excel lors de l'initialisation du modèle
        try {
            if (!file_exists($excelFilePath)) {
                throw new \Exception("Le fichier Excel n'existe pas à l'emplacement : $excelFilePath");
            }
            $this->spreadsheet = IOFactory::load($excelFilePath);
        } catch (\Exception $e) {
            // Log the error for debugging
            error_log("Erreur lors de la lecture du fichier Excel : " . $e->getMessage());
            throw new \Exception("Erreur lors de la lecture du fichier Excel : " . $e->getMessage());
        }
    }

    // Fonction pour envoyer un message à Rasa et obtenir une réponse
    public function getChatbotResponse($message) {
        // Si le message contient "incident", afficher les incidents par catégorie
        if (strpos(strtolower($message), 'incident') !== false) {
            return $this->getIncidentsByCategory();
        }

        // Si le message contient un nom d'incident spécifique, afficher les détails
        if ($this->incidentExists($message)) {
            return $this->getIncidentDetails($message);
        }

        // Logique de communication avec Rasa
        $url = 'http://localhost:5005/webhooks/rest/webhook';

        // Préparer les données pour l'envoi à Rasa
        $data = ['message' => $message];

        // Initialiser la session cURL
        $ch = curl_init($url);
        curl_setopt($ch, CURLOPT_RETURNTRANSFER, true);
        curl_setopt($ch, CURLOPT_POST, true);
        curl_setopt($ch, CURLOPT_POSTFIELDS, json_encode($data));
        curl_setopt($ch, CURLOPT_HTTPHEADER, ['Content-Type: application/json']);
        
        // Exécuter la requête cURL
        $response = curl_exec($ch);

        // Gestion des erreurs cURL
        if (curl_errno($ch)) {
            $error_msg = curl_error($ch);
            curl_close($ch);
            error_log("Erreur de communication avec Rasa: $error_msg");
            return "Erreur de communication avec le serveur Rasa: $error_msg";
        }

        curl_close($ch);

        // Vérifier si la réponse est bien formatée JSON
        $responseDecoded = json_decode($response, true);
        if (is_null($responseDecoded)) {
            error_log("Erreur de réponse du serveur Rasa : réponse JSON invalide");
            return 'Erreur de réponse du serveur Rasa. Impossible de traiter.';
        }

        // Vérifier si la réponse de Rasa est valide
        if ($responseDecoded && isset($responseDecoded[0]['text'])) {
            return $responseDecoded[0]['text'];
        } else {
            return 'Désolé, je n\'ai pas compris ou le serveur est inaccessible.';
        }
    }

    public function getIncidentsByCategory() {
        try {
            $sheet = $this->spreadsheet->getSheetByName('Incidents');
            if (!$sheet) {
                throw new \Exception("Feuille 'Incidents' introuvable");
            }
            $highestRow = $sheet->getHighestRow();
            $highestColumn = $sheet->getHighestColumn();
            $categories = [];
    
            for ($row = 2; $row <= $highestRow; $row++) {  
                $category = $sheet->getCell('A' . $row)->getValue(); // Colonne A pour les catégories
                $incidents = [];
    
                // Parcourir les colonnes B, C, D, etc.
                for ($col = 'B'; $col <= $highestColumn; $col++) {
                    $incident = $sheet->getCell($col . $row)->getValue();
                    if ($incident) {
                        $incidents[] = $incident;
                    }
                }
    
                if (!empty($incidents)) {
                    $categories[$category] = $incidents;
                }
            }
    
            // Générer la réponse du chatbot avec une liste de catégories et incidents
            $response = "<ul>";
            foreach ($categories as $category => $incidents) {
                $response .= "<li><strong>" . $category . "</strong><ul>";
                foreach ($incidents as $index => $incident) {
                    // Add clickable elements (links) with the incident name as a data attribute
                    $response .= '<li><a href="#" class="incident-link" data-incident="' . $incident . '">' . $incident . '</a></li>';
                }
                $response .= "</ul></li>";
            }
            $response .= "</ul>";
    
            return $response;
        } catch (\Exception $e) {
            return "Erreur lors de la lecture du fichier Excel : " . $e->getMessage();
        }
    }     

    // Fonction pour vérifier si un incident existe
    public function incidentExists($incidentName) {
        $sheet = $this->spreadsheet->getSheetByName('Incidents');
        $highestRow = $sheet->getHighestRow();
        $highestColumn = $sheet->getHighestColumn();

        for ($row = 2; $row <= $highestRow; $row++) {
            for ($col = 'B'; $col <= $highestColumn; $col++) {
                $incident = $sheet->getCell($col . $row)->getValue();

                if (is_string($incident) && is_string($incidentName)) {
                    if (strtolower($incident) === strtolower($incidentName)) {
                        return true;
                    }
                }
            }
        }
        return false;
    }

    public function getIncidentDetails($incidentName) {
        try {
            $sheet = $this->spreadsheet->getSheetByName('Dictées'); // Feuille des détails (par exemple "Dictées")
            if (!$sheet) {
                throw new \Exception("Feuille 'Dictées' introuvable");
            }
            $highestRow = $sheet->getHighestRow();
    
            for ($row = 2; $row <= $highestRow; $row++) {
                $incident = $sheet->getCell('A' . $row)->getValue(); // Supposons que la colonne A contient les incidents
                if (strtolower($incident) === strtolower($incidentName)) {
                    $details = $sheet->getCell('B' . $row)->getValue(); // Colonne B contient les détails
                    return "Détails pour l'incident '$incidentName' :\n" . $details;
                }
            }
    
            return "Détails de l'incident '$incidentName' non trouvés.";
        } catch (\Exception $e) {
            return "Erreur lors de la récupération des détails de l'incident : " . $e->getMessage();
        }
    }    
}

