<?php
namespace Models;

use PhpOffice\PhpSpreadsheet\IOFactory;

class ExcelModel
{
    private $filePath;

    public function __construct($filePath)
    {
        $this->filePath = $filePath;
    }

    // Méthode pour obtenir une réponse spécifique en fonction de l'intention
    public function getResponseFromExcel($intent)
    {
        if (!file_exists($this->filePath)) {
            throw new \Exception("Fichier Excel non trouvé au chemin : " . $this->filePath);
        }

        try {
            // Charger le fichier Excel
            $spreadsheet = IOFactory::load($this->filePath);

            // Obtenir la première feuille du fichier Excel
            $sheet = $spreadsheet->getActiveSheet();

            // Lire toutes les données de la feuille sous forme de tableau
            $data = $sheet->toArray();

            // Parcourir les lignes pour trouver la réponse correspondant à l'intention
            foreach ($data as $row) {
                // Vérification que la colonne 0 n'est pas null avant d'utiliser strtolower
                if (isset($row[0]) && is_string($row[0]) && strtolower($row[0]) === strtolower($intent)) {
                    return $row[1] ?? "Aucune réponse disponible pour cette intention."; // Retourner la réponse associée à l'intention
                }
            }

            // Si l'intention n'est pas trouvée
            return "Désolé, je n'ai pas trouvé de réponse à cette question.";

        } catch (\Exception $e) {
            return "Erreur lors du chargement du fichier Excel : " . $e->getMessage();
        }
    }
}
