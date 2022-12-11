<?php

    // Database

    include "dbConnect.inc.php";
    
/*
 * function getStatusCodeMessage($status) --> string status
 * Empty if not found
*/
function getStatusCodeMessage($status) {

    $codes = Array(
                100 => 'Continue',
                101 => 'Switching Protocols',
                200 => 'OK',
                201 => 'Created',
                202 => 'Accepted',
                203 => 'Non-Authoritative Information',
                204 => 'No Content',
                205 => 'Reset Content',
                206 => 'Partial Content',
                300 => 'Multiple Choices',
                301 => 'Moved Permanently',
                302 => 'Found',
                303 => 'See Other',
                304 => 'Not Modified',
                305 => 'Use Proxy',
                306 => '(Unused)',
                307 => 'Temporary Redirect',
                400 => 'Bad Request',
                401 => 'Unauthorized',
                402 => 'Payment Required',
                403 => 'Forbidden',
                404 => 'Not Found',
                405 => 'Method Not Allowed',
                406 => 'Not Acceptable',
                407 => 'Proxy Authentication Required',
                408 => 'Request Timeout',
                409 => 'Conflict',
                410 => 'Gone',
                411 => 'Length Required',
                412 => 'Precondition Failed',
                413 => 'Request Entity Too Large',
                414 => 'Request-URI Too Long',
                415 => 'Unsupported Media Type',
                416 => 'Requested Range Not Satisfiable',
                417 => 'Expectation Failed',
                500 => 'Internal Server Error',
                501 => 'Not Implemented',
                502 => 'Bad Gateway',
                503 => 'Service Unavailable',
                504 => 'Gateway Timeout',
                505 => 'HTTP Version Not Supported'
                );
        
        return (isset($codes[$status])) ? $codes[$status] : '';
    }

    // Sanitaze INput

    $clean = array();
    foreach(array_keys($_REQUEST) as $key)
    {
        $clean[$key] = mysqli_real_escape_string($db, $_REQUEST[$key]);
    }
    
    // Send response

    if (isset($clean['action'])) {
        
        header('HTTP/1.1 200 '.getStatusCodeMessage(200));
        header('Content-Type: application/json; charset=utf8');

        $response = "";
        if (empty($clean['action'])) $response .= "{}";

        switch ($clean['action'])
        {
            case "getProd":
    
                $sql ="select * from diet_product_data where IDproduct = ?";
                $stmt =$db->prepare($sql);
                $stmt->bind_param("i", $clean['prod']);
                $stmt->execute();
                $stmt->bind_result($db_prod, $db_unit, $db_grams, $db_energy, $db_fat, $db_saturates, $db_carbo, $db_sugar, $db_fibre, $db_protein, $db_salt);
                $c = 0;
                $response = '{ "product": [ ';
                while ($stmt->fetch())
                {
                    if ($c) $response .=',';
                    $response .= '{ "grams":"'.$db_grams.'",'.
                                    ' "energy":"'.$db_energy.'",'.
                                        ' "fat":"'.$db_fat.'",'.
                                            ' "saturates":"'.$db_saturates.'",'.
                                                ' "carbo":"'.$db_carbo.'",'.
                                                    ' "sugar":"'.$db_sugar.'",'.
                                                        ' "fibre":"'.$db_fibre.'",'.
                                                            ' "protein":"'.$db_protein.'",'.
                                                                ' "salt":"'.$db_salt.'"'.
                                                                    ' }';
                    $c += 1;
                }
                $stmt->close();
                if ($c == 0) $response = '{ "response":"void" }';
                else $response .= " ] } ";
                
            break;
            }        
        echo $response;
    }
    mysqli_close($db);  
?>
