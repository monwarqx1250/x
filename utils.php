<?php
function toHex($input, $prefix = false) {
    if (is_numeric($input)) {
        $hex = dechex($input);
    } elseif (is_string($input)) {
        $hex = bin2hex($input);
    } else {
        return "Input must be a number or a string.";
    }
    
    if ($prefix) {
        return PREFIX. $hex;
    } else {
        return $hex;
    }
}


function send_udp_request($query, $host, $port) {
    $socket = fsockopen("udp://" . $host, $port);

    if (!$socket) {
        return "Unable to open socket.";
    }

    $hexQuery = toHex($query, true);
    $binaryData = hex2bin($hexQuery);
    
    fwrite($socket, $binaryData);
    
    stream_set_timeout($socket, 3);
    
    $response = fread($socket,4096);
    fclose($socket);

    return $response;
}


function decode_servers($stream){
    $servers = [];
    foreach(explode("\\", $stream) as $server) {
        if(strlen($server) == 6) {
            $ip = sprintf("%d.%d.%d.%d", ord($server[0]), ord($server[1]), ord($server[2]), ord($server[3]));
                        $port = ord($server[4]) * 256 + ord($server[5]);
                        $servers[] = ["ip" => $ip, "port" => $port];
        }
    }
    return $servers;
}



 function parseServerInfo($details) {
                $details = explode("\\", $details);
                $serverData = [];
                for ($i = 1; $i < count($details) - 1; $i += 2) {
                    $key = stripper($details[$i]);
                    $value = $details[$i + 1];
                    $value = preg_replace('/\x01+/', "\x01", $value);
                    $serverData[$key] = stripper($value);
                }
                
                return $serverData;
            }

function stripper($string) {
    return preg_replace('/\^\d/', '', $string);
}