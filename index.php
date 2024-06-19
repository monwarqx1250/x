<?php
require_once 'Router.php';
require_once 'utils.php';

//Constants
define("HOST","codmaster.activision.com");
define("PORT",20510);
define("QUERIES",["getservers 1 full empty","getstatus"]);
define("PREFIX","ffffffff");


$router = new Router();

$router->get("/",function(){
    include 'view/status.html';
});

$router->get("/servers",function(){
    header("Content-type: application/json");
    echo json_encode(decode_servers(send_udp_request(QUERIES[0],HOST,PORT)));
    
});

$router->get("/server",function($qp){
    if($qp['s'] && $qp['p']){
        header("Content-type: application/json");
        echo json_encode(parseServerInfo(send_udp_request(QUERIES[1],$qp['s'],$qp['p'])));
    }
});

$router->get('/info',function(){
    header('Content-type: application/json');

    $data = array();


    foreach(decode_servers(send_udp_request(QUERIES[0],HOST,PORT)) as $x){
        $data[] = parseServerInfo(send_udp_request(QUERIES[1],$x['ip'],$x['port']));
    }

    echo json_encode($data);
});

$router->run();

