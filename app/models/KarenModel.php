<?php
namespace Models;

use PhpOffice\PhpSpreadsheet\IOFactory;

class KarenModel {
    protected $spreadsheet;

    const INCIDENTS_SHEET = 'Incidents';
    const APPELS_SHEET = 'Appels';
    const DEMANDES_SHEET = 'Demandes';

    public function __construct($excelFilePath) {
        if (!file_exists($excelFilePath)) {
            throw new \Exception("The Excel file does not exist at: $excelFilePath");
        }

        try {
            $this->spreadsheet = IOFactory::load($excelFilePath);
        } catch (\Exception $e) {
            error_log("Error reading the Excel file: " . $e->getMessage());
            throw new \Exception("Error reading the Excel file: " . $e->getMessage());
        }
    }

    public function getChatbotResponse($message) {
        $messageLowered = strtolower($message);

        // Use a mapping array for better maintainability
        $responses = [
            'incidents' => $this->getIncidentsByCategory(),
            'appels' => $this->getAppelsByCategory(),
            'demandes' => $this->getDemandesByCategory()
        ];

        foreach ($responses as $keyword => $response) {
            if (strpos($messageLowered, $keyword) !== false) {
                return $response;
            }
        }

        return $this->incidentExists($message) ? 
            $this->getIncidentDetails($message) : 
            $this->getRasaResponse($message);
    }

    private function getRasaResponse($message) {
        $url = 'http://localhost:5005/webhooks/rest/webhook';
        $data = json_encode(['message' => $message]);

        $ch = curl_init($url);
        curl_setopt_array($ch, [
            CURLOPT_RETURNTRANSFER => true,
            CURLOPT_POST => true,
            CURLOPT_POSTFIELDS => $data,
            CURLOPT_HTTPHEADER => ['Content-Type: application/json']
        ]);

        $response = curl_exec($ch);
        if (curl_errno($ch)) {
            error_log("Communication error with Rasa: " . curl_error($ch));
            return "Communication error with Rasa server.";
        }
        curl_close($ch);

        $responseDecoded = json_decode($response, true);
        if (is_null($responseDecoded)) {
            error_log("Rasa server response error: invalid JSON response");
            return 'Rasa server response error.';
        }

        return $responseDecoded[0]['text'] ?? 'Sorry, I didn\'t understand or the server is unreachable.';
    }

    public function getIncidentsByCategory() {
        return $this->getDataFromSheet(self::INCIDENTS_SHEET, 'A', 'C');
    }

    public function getAppelsByCategory() {
        return $this->getDataFromSheet(self::APPELS_SHEET, 'B', 'D');
    }

    public function getDemandesByCategory() {
        return $this->getDataFromSheet(self::DEMANDES_SHEET, 'B', 'D');
    }

    private function getDataFromSheet($sheetName, $categoryColumn, $dataStartColumn) {
        try {
            $sheet = $this->spreadsheet->getSheetByName($sheetName);
            if (!$sheet) throw new \Exception("Sheet '$sheetName' not found");

            $highestRow = $sheet->getHighestRow();
            $highestColumn = $sheet->getHighestColumn();
            $categories = [];

            for ($row = 2; $row <= $highestRow; $row++) {
                $category = $sheet->getCell($categoryColumn . $row)->getValue();
                if (!$category) continue; // Skip if no category

                $dataItems = [];
                for ($col = $dataStartColumn; $col <= $highestColumn; $col++) {
                    $cell = $sheet->getCell($col . $row);
                    $item = $cell->getValue();
                    $hyperlink = $cell->getHyperlink()->getUrl();

                    if ($item) {
                        $dataItems[] = $hyperlink ? ['value' => $item, 'hyperlink' => $hyperlink] : $item;
                    }
                }

                if ($dataItems) {
                    $categories[$category][] = $dataItems;
                }
            }

            return $this->formatDataAsHtml($categories);
        } catch (\Exception $e) {
            return "Error reading the Excel file: " . $e->getMessage();
        }
    }

    private function formatDataAsHtml($categories) {
        $response = "<ul>";
        foreach ($categories as $category => $items) {
            $response .= "<li><strong>" . htmlspecialchars($category) . "</strong><ul>";
            foreach ($items as $dataItems) {
                $response .= '<li><ul>';
                foreach ($dataItems as $item) {
                    if (is_array($item) && isset($item['hyperlink'])) {
                        $excelLink = 'ms-excel:ofe|u=http://localhost/karenbot/assets/docs/ITASM.xlsm#' . htmlspecialchars($item['hyperlink']);
                        $response .= '<li><a href="' . $excelLink . '" target="_blank">' . htmlspecialchars($item['value']) . '</a></li>';
                    } else {
                        $response .= '<li>' . htmlspecialchars($item) . '</li>';
                    }
                }
                $response .= '</ul></li>';
            }
            $response .= "</ul></li>";
        }
        return $response . "</ul>";
    }

    public function incidentExists($incidentName) {
        return $this->existsInSheet(self::INCIDENTS_SHEET, $incidentName);
    }

    private function existsInSheet($sheetName, $nameToCheck) {
        $sheet = $this->spreadsheet->getSheetByName($sheetName);
        $highestRow = $sheet->getHighestRow();

        for ($row = 2; $row <= $highestRow; $row++) {
            for ($col = 'B'; $col <= $sheet->getHighestColumn(); $col++) {
                $item = $sheet->getCell($col . $row)->getValue();
                if (strcasecmp($item, $nameToCheck) === 0) {
                    return true;
                }
            }
        }
        return false;
    }

    public function getIncidentDetails($incidentName) {
        // Implementation for fetching specific incident details
        return "Details for incident: $incidentName"; // Placeholder for actual implementation
    }
}
