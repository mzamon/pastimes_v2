<?php
/**
 * includes/TextScanner.php
 * Input sanitization helpers
 */

function sanitizeAndScanText($inputText) {
    if (!is_string($inputText)) return $inputText;
    $cleaned = trim($inputText);
    $cleaned = strip_tags($cleaned);
    return $cleaned;
}

function validateBeforeDatabase(array $rawData) {
    $out = [];
    foreach ($rawData as $key => $value) {
        if (is_array($value)) {
            $out[$key] = validateBeforeDatabase($value);
        } else {
            $val = trim((string)$value);
            $out[$key] = $val === '' ? null : $val;
        }
    }
    return $out;
}
?>
