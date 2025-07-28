<?php


    function XmlToJson ($fi,$ff) {

        $url ="https://www.smssoftware.net/tms/manTrafExp?fromDate=$fi&toDate=$ff&interval=1440&hours=0&reqType=tdd&apiKey=C3XS754LDZYPJJTEDZ7MFN19BNQC3QWB&locationId=ROY000";
        $fileContents= file_get_contents($url);
        $fileContents = str_replace(array("\n", "\r", "\t"), '', $fileContents);
        $fileContents = trim(str_replace('"', "'", $fileContents));
        $simpleXml = simplexml_load_string($fileContents);
        $json = json_encode($simpleXml,JSON_UNESCAPED_UNICODE);

        return $json;
    }

    $fi = date("m/d/Y");

    print XmlToJson($fi,$fi);


