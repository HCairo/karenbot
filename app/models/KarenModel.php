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

    // Function to send a message to Rasa and get a response
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
        return $this->getDataFromSheet(self::INCIDENTS_SHEET, 'A', 'C'); // Assuming data starts from column B for incidents
    }
    
    public function getAppelsByCategory() {
        return $this->getDataFromSheet(self::APPELS_SHEET, 'B', 'D'); // Adjust if necessary
    }
    
    public function getDemandesByCategory() {
        return $this->getDataFromSheet(self::DEMANDES_SHEET, 'B', 'D'); // Adjust if necessary
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
    
            $currentCategory = null; // Track the current category
    
            // Starting from row 2 to skip header
            for ($row = 2; $row <= $highestRow; $row++) {
                // Get the category from the specified category column
                $category = $sheet->getCell($categoryColumn . $row)->getValue();
    
                // If we encounter a new category, update currentCategory
                if ($category) {
                    $currentCategory = $category; // Update current category if a new one is found
                }
    
                // Only process if we have a current category
                if ($currentCategory) {
                    $dataItems = [];
    
                    // Collect data items from the specified data columns (starting from dataStartColumn)
                    for ($col = $dataStartColumn; $col <= $highestColumn; $col++) {
                        $cell = $sheet->getCell($col . $row);
                        $item = $cell->getValue();
    
                        // Check if this cell has a hyperlink and extract the URL
                        $hyperlink = $cell->getHyperlink()->getUrl();
    
                        if ($item) {
                            if ($hyperlink) {
                                // Store both value and hyperlink if present
                                $dataItems[] = [
                                    'value' => $item,
                                    'hyperlink' => $hyperlink
                                ];
                            } else {
                                // Just store the value if no hyperlink is present
                                $dataItems[] = $item;
                            }
                        }
                    }
    
                    // If there are data items, store them in the categories array
                    if (!empty($dataItems)) {
                        // Initialize the category array if it doesn't exist
                        if (!isset($categories[$currentCategory])) {
                            $categories[$currentCategory] = []; // Initialize if not set
                        }
                        // Merge data items into the category
                        $categories[$currentCategory][] = $dataItems; // Store as an array of items
                    }
                }
            }
    
            // Logging the categories fetched for debugging
            error_log("Fetched data from '$sheetName': " . print_r($categories, true));
    
            return $this->formatDataAsHtml($categories);
        } catch (\Exception $e) {
            return "Error reading the Excel file: " . $e->getMessage();
        }
    }    
    
    // private function formatDataAsHtml($categories) {
    //     $response = "<ul>";
    //     foreach ($categories as $category => $items) {
    //         $response .= "<li><strong>" . htmlspecialchars($category) . "</strong><ul>";
    //         foreach ($items as $dataItems) {
    //             $response .= '<li><ul>'; // Nested list for items
    //             foreach ($dataItems as $item) {
    //                 if (is_array($item) && isset($item['hyperlink'])) {
    //                     // Construct the Excel link properly
    //                     $excelLink = 'http://localhost/karenbot/assets/docs/ITASM.xlsm' . htmlspecialchars($item['hyperlink']);
                        
    //                     // Remove 'sheet://' from the hyperlink
    //                     $excelLink = str_replace('sheet://', '#', $excelLink);
                        
    //                     // Create an anchor tag for the Excel link
    //                     $response .= '<li><a href="' . $excelLink . '" target="_blank">' . htmlspecialchars($item['value']) . '</a></li>';
    //                 } else {
    //                     // If no hyperlink, just display the value
    //                     $response .= '<li>' . htmlspecialchars($item) . '</li>';
    //                 }
    //             }
    //             $response .= '</ul></li>'; // Close nested list
    //         }
    //         $response .= "</ul></li>";
    //     }
    //     $response .= "</ul>";
    //     return $response;
    // }
    
    private function formatDataAsHtml($categories) {
        $response = "<ul>";
        foreach ($categories as $category => $items) {
            $response .= "<li><strong>" . htmlspecialchars($category) . "</strong><ul>";
            foreach ($items as $dataItems) {
                $response .= '<li><ul>'; // Nested list for items
                foreach ($dataItems as $item) {
                    if (is_array($item) && isset($item['hyperlink'])) {
                        // Create a proper Excel link
                        $excelLink = 'excel://localhost/karenbot/assets/docs/ITASM.xlsm#' . htmlspecialchars($item['hyperlink']);
                        // Create an anchor tag for the Excel link
                        $response .= '<li><a href="' . $excelLink . '" target="_blank">' . htmlspecialchars($item['value']) . '</a></li>';
                    } else {
                        // If no hyperlink, just display the value
                        $response .= '<li>' . htmlspecialchars($item) . '</li>';
                    }
                }
                $response .= '</ul></li>'; // Close nested list
            }
            $response .= "</ul></li>";
        }
        $response .= "</ul>";
        return $response;
    }
    

    public function incidentExists($incidentName) {
        return $this->existsInSheet(self::INCIDENTS_SHEET, $incidentName);
    }

    private function existsInSheet($sheetName, $nameToCheck) {
        $sheet = $this->spreadsheet->getSheetByName($sheetName);
        $highestRow = $sheet->getHighestRow();
        $highestColumn = $sheet->getHighestColumn();

        for ($row = 2; $row <= $highestRow; $row++) {
            for ($col = 'B'; $col <= $highestColumn; $col++) {
                $item = $sheet->getCell($col . $row)->getValue();
                if (is_string($item) && strtolower($item) === strtolower($nameToCheck)) {
                    return true;
                }
            }
        }
        return false;
    }

    public function getIncidentDetails($incidentName) {
        return $this->getDetailsFromSheet(self::INCIDENTS_SHEET, $incidentName);
    }

    private function getDetailsFromSheet($sheetName, $nameToFind) {
        $sheet = $this->spreadsheet->getSheetByName($sheetName);
        $highestRow = $sheet->getHighestRow();
        $highestColumn = $sheet->getHighestColumn();
        $details = "";
    
        for ($row = 2; $row <= $highestRow; $row++) {
            for ($col = 'B'; $col <= $highestColumn; $col++) {
                $item = $sheet->getCell($col . $row)->getValue();
                if (is_string($item) && strtolower($item) === strtolower($nameToFind)) {
                    // Constructing details string based on columns in the row
                    for ($detailsCol = 'C'; $detailsCol <= $highestColumn; $detailsCol++) {
                        $detailsValue = $sheet->getCell($detailsCol . $row)->getValue();
                        if ($detailsValue) {
                            // Check if the detailsValue is a link
                            if (preg_match('/^(\w+)!([A-Z]+[0-9]+)$/', $detailsValue, $matches)) {
                                // Fetch linked data
                                $linkedSheetName = $matches[1];
                                $linkedCell = $matches[2];
                                $linkedData = $this->getLinkData($linkedSheetName, $linkedCell);
                                $details .= htmlspecialchars($linkedData) . "<br/>";
                            } else {
                                $details .= htmlspecialchars($detailsValue) . "<br/>";
                            }
                        }
                    }
                    return "<strong>Details for {$nameToFind}:</strong><br/>" . $details;
                }
            }
        }
        return "Incident '$nameToFind' not found.";
    }
    
    private function getLinkData($linkedSheetName, $linkedCell) {
        $linkedSheet = $this->spreadsheet->getSheetByName($linkedSheetName);
        if (!$linkedSheet) {
            return "Sheet '$linkedSheetName' not found.";
        }
    
        $linkValue = $linkedSheet->getCell($linkedCell)->getValue();
        return $linkValue !== null ? $linkValue : "No data found in '$linkedSheetName:$linkedCell'.";
    }    
}