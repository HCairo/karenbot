<?php

namespace Controllers;

use Models\ExcelModel;

class ExcelController
{
    private $model;

    // Le contrôleur prend une instance du modèle dans le constructeur
    public function __construct($filePath)
    {
        // Utiliser le modèle pour lire les données Excel
        $this->model = new ExcelModel($filePath);
    }

    // Méthode pour afficher les données Excel
    public function showExcelData()
    {
        try {
            // Récupérer les données du modèle
            $data = $this->model->getData();

            // Afficher les données (ici directement dans la vue)
            foreach ($data as $row) {
                echo "<pre>";
                print_r($row);
                echo "</pre>";
            }

        } catch (\Exception $e) {
            // Gérer les erreurs et les afficher
            echo "Erreur : " . $e->getMessage();
        }
    }
}
