<?php

function kirim($psn){
    echo json_encode(array("replies" => array(array("message" => $psn),array(""=>""))));
}

function str_toarray($string,$pemisah){
    $arr=[];
    $tok = strtok($string, $pemisah);
    while ($tok !== false){
        $arr[].=$tok;
        $tok = strtok($pemisah);
    }
    return $arr;
}

function array_tostr($str){
    return implode(" ",$str);
}

function contains_word($string,$search){
    $coun = 0;
    $expl=str_toarray($search," ");
    
    $jmlword=count($expl);
    foreach($expl as $word) {
        if(contains($string, $word)) {
            $coun++;
        }
    }
    if (($jmlword==1 and $coun==1) or
    ($jmlword==2 and $coun>=2) or 
    ($jmlword==3 and $coun>=3) or
    ($jmlword==4 and $coun>=3) or
    ($jmlword==5 and $coun>=4) or
    ($jmlword==6 and $coun>=4) or
    ($jmlword>=7 and $coun>=5) 
    ){
        return 1;
    }else
    return 0;
}

function contains($str,$search){
    if (stripos($str, $search) !== false) 
    return 1;
    else
    return 0;
}

function fixKey($mesage) { 
    include "data/fixkey.php";
    foreach ($fixkey as $incorrect => $correct) {
        $mesage = str_ireplace($incorrect, $correct, $mesage);
    }
    return $mesage;
}

function fixbahasa($mesage) { 
    include "data/fixbahasa.php";
    foreach ($fixbahasa as $incorrect => $correct) {
        $mesage = str_ireplace($incorrect, $correct, $mesage);
    }
    return $mesage;
}

function kasi_tanda($msg,$word){
    $str = $msg;
    $expl=explode(" ", $word);
    foreach($expl as $wor) {
        $str = preg_replace(kuote($wor),bold($wor),$str);
    }
    $s1="Nabi shallallahu 'alaihi wasallam bersabda";
    $s2="Rasulullah shallallahu 'alaihi wasallam bersabda";
    
    $str = preg_replace(kuote($s1),italic($s1),$str);
    $str = preg_replace(kuote($s2),italic($s2),$str);
    
    $str=str_replace(" *  *"," ",$str);
    $str=str_replace("* - *"," ",$str);
    $str=str_replace("**","*",$str);
    
    $str=str_replace("__","_",$str);
    $str=str_replace("_  _"," ",$str);
    $str=str_replace("_ _*"," ",$str);
    return $str;
}

function get_nomor($str) {
    preg_match_all('/\d+/', $str, $matches);
    return $matches[0];
}

function cleanold($string) {
    $string=str_replace("."," ",$string);
    $string=str_replace(","," ",$string);
    $string=str_replace(": ",":",$string);
    $string=str_replace(" :",":",$string);
    $string=str_replace(" : ",":",$string);
    // $string=str_replace(" - ","-",$string);
    // $string=str_replace("- ","-",$string);
    // $string=str_replace(" -","-",$string);
    
    $string=preg_replace('/\s\s+/', ' ', $string);
    
    return preg_replace("/[^A-Za-z0-9\-\s\:\']/", '', $string);
}

function hapuskata($kata){
    include "data/hapuskata.php";
    $expl=explode(" ",$kata);
    foreach ($expl as $word){
        foreach ($hapuskata as $val){
            if ($word==$val or strlen($word)<=2){
                //echo "Found ". $word."---".$val."<br >";
                $kata=str_ireplace_once($word,"",$kata);
                break;
            }
        }
    }
    $kata=trim(preg_replace("/\s+/", " ", $kata));
    return $kata;
}
function str_ireplace_once($search, $replace, $subject, &$count = 0){
    $pos = stripos($subject, $search);
    if (false === $pos)
    return $subject;
    
    $count = 1;
    return substr_replace($subject, $replace, $pos, strlen($search));
}

?>