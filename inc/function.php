<?php 
function msg($msg, $code, $data = null) {
    header("Content-Type: application/json"); 
    http_response_code($code);

    
    if (isset($GLOBALS['stmt']) && $GLOBALS['stmt']) {
        $GLOBALS['stmt']->close();
    }

  
    if (isset($GLOBALS['conn']) && $GLOBALS['conn']) {
        $GLOBALS['conn']->close();
    }

    echo json_encode([
        "status" => $code,
        "message" => $msg,
        "data" => $data
    ]);
    exit;
}