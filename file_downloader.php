<?php 

// File Downloader - Scarica file protetti con autenticazione
function downloadFile($filePath) {
    $allowedDirectory = __DIR__ . '/downloads/';
    
    try {
        if (empty($filePath)) {
            throw new Exception("Il percorso del file non è specificato.");
        }

        $realPath = realpath($filePath);
        if (!$realPath || strpos($realPath, $allowedDirectory) !== 0) {
            throw new Exception("Accesso non consentito al file richiesto.");
        }

        if (!is_file($realPath) || !is_readable($realPath)) {
            throw new Exception("Il file richiesto non esiste o non è accessibile.");
        }

        header('Content-Description: File Transfer');
        header('Content-Type: application/octet-stream');
        header('Content-Disposition: attachment; filename="' . basename($realPath) . '"');
        header('Cache-Control: must-revalidate, post-check=0, pre-check=0');
        header('Pragma: public');
        header('Expires: 0');
        header('Content-Length: ' . filesize($realPath));

        $file = fopen($realPath, 'rb');
        if ($file === false) {
            throw new Exception("Errore nell'apertura del file.");
        }
        
        while (!feof($file)) {
            echo fread($file, 8192);
            flush();
        }
        fclose($file);
        exit;
    } catch (Exception $e) {
        http_response_code(400);
        if ($e->getMessage() === "Accesso non consentito al file richiesto.") {
            http_response_code(403);
        } elseif ($e->getMessage() === "Il file richiesto non esiste o non è accessibile.") {
            http_response_code(404);
        }
        echo $e->getMessage();
    }
}

?>
