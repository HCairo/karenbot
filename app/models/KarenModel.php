<?php
namespace Models;

use PhpOffice\PhpSpreadsheet\IOFactory;

class KarenModel {
    protected $spreadsheet;

    const INCIDENTS_SHEET = 'Incidents';
    const APPELS_SHEET = 'Appels';
    const DEMANDES_SHEET = 'Demandes';

    public function __construct($excelFilePath) {
        try {
            if (!file_exists($excelFilePath)) {
                throw new \Exception("The Excel file does not exist at: $excelFilePath");
            }
            $this->spreadsheet = IOFactory::load($excelFilePath);
        } catch (\Exception $e) {
            error_log("Error reading the Excel file: " . $e->getMessage());
            throw new \Exception("Error reading the Excel file: " . $e->getMessage());
        }
    }

    public function getChatbotResponse($message) {
        $lowerMessage = strtolower($message);
        
        if (strpos($lowerMessage, 'incidents') !== false) {
            return $this->getIncidentsByCategory();
        }
        if (strpos($lowerMessage, 'appels') !== false) {
            return $this->getAppelsByCategory();
        }
        if (strpos($lowerMessage, 'demandes') !== false) {
            return $this->getDemandesByCategory();
        }
        if ($this->incidentExists($message)) {
            return $this->getIncidentDetails($message);
        }

        return $this->getRasaResponse($message);
    }

    private function getRasaResponse($message) {
        $url = 'http://localhost:5005/webhooks/rest/webhook';
        $data = ['message' => $message];

        $ch = curl_init($url);
        curl_setopt($ch, CURLOPT_RETURNTRANSFER, true);
        curl_setopt($ch, CURLOPT_POST, true);
        curl_setopt($ch, CURLOPT_POSTFIELDS, json_encode($data));
        curl_setopt($ch, CURLOPT_HTTPHEADER, ['Content-Type: application/json']);

        $response = curl_exec($ch);

        if (curl_errno($ch)) {
            $error_msg = curl_error($ch);
            curl_close($ch);
            error_log("Communication error with Rasa: $error_msg");
            return "Communication error with Rasa server: $error_msg";
        }

        curl_close($ch);
        $responseDecoded = json_decode($response, true);
        if (is_null($responseDecoded)) {
            error_log("Rasa server response error: invalid JSON response");
            return 'Rasa server response error. Unable to process.';
        }

        return isset($responseDecoded[0]['text']) ? $responseDecoded[0]['text'] : 'Sorry, I didn\'t understand or the server is unreachable.';
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
            if (!$sheet) {
                throw new \Exception("Sheet '$sheetName' not found");
            }

            $highestRow = $sheet->getHighestRow();
            $highestColumn = $sheet->getHighestColumn();
            $categories = [];
            $currentCategory = null;

            for ($row = 2; $row <= $highestRow; $row++) {
                $category = $sheet->getCell($categoryColumn . $row)->getValue();
                if ($category) $currentCategory = $category;
                if ($currentCategory) {
                    $dataItems = [];
                    for ($col = $dataStartColumn; $col <= $highestColumn; $col++) {
                        $cell = $sheet->getCell($col . $row);
                        $item = $cell->getValue();
                        $hyperlink = $cell->getHyperlink()->getUrl();
                        if ($item) {
                            $dataItems[] = $hyperlink ? ['value' => $item, 'hyperlink' => $hyperlink] : $item;
                        }
                    }
                    if (!empty($dataItems)) {
                        $categories[$currentCategory][] = $dataItems;
                    }
                }
            }

            error_log("Fetched data from '$sheetName': " . print_r($categories, true));
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
        $response .= "</ul>";
        return $response;
    }

    public function searchKeyword($keyword) {
        $sheets = [$this->getIncidentsByCategory(), $this->getAppelsByCategory(), $this->getDemandesByCategory()];
        $results = [];

        foreach ($sheets as $sheet) {
            foreach ($sheet as $category => $data) {
                foreach ($data as $item) {
                    if (strpos(strtolower($item), strtolower($keyword)) !== false) {
                        $results[$category][] = $item;
                    }
                }
            }
        }

        return $this->formatDataAsHtml($results);
    }

    // Get a specific cell's hyperlink data
    public function getLinkData($sheetName, $cell) {
        try {
            $sheet = $this->spreadsheet->getSheetByName($sheetName);
            $cellValue = $sheet->getCell($cell)->getValue();
            $hyperlink = $sheet->getCell($cell)->getHyperlink()->getUrl();
            
            if ($hyperlink) {
                return [
                    'value' => $cellValue,
                    'hyperlink' => 'ms-excel:ofe|u=http://localhost/karenbot/assets/docs/ITASM.xlsm#' . $hyperlink
                ];
            }
            return $cellValue;
        } catch (\Exception $e) {
            return "Error retrieving link data: " . $e->getMessage();
        }
    }
}
