<?php

function replace_in_file($FilePath, $OldText, $NewText){
    //$Result = "";
    if(file_exists($FilePath)===TRUE){
        if(is_writeable($FilePath)){
            try{
                $FileContent = file_get_contents($FilePath);
                if (contains($FileContent,$OldText)){
                    $FileContent = str_ireplace($OldText, $NewText, $FileContent);
                    
                    if(file_put_contents($FilePath, $FileContent)){
                        return  "Success\n".$OldText." => ".$NewText."\n".$FilePath."\n\n";
                    }else{
                        return  "Error while writing file ".$FilePath;
                    }
                }
            }catch(Exception $e){
                return 'Error : '.$e;
            }
        }else{
            return 'File '.$FilePath.' is not writable !';
        }
    }else{
        return 'File '.$FilePath.' does not exist !';
    }
    return "";
}

function xreplace($kata){
    $psn="";
    $msg = new message($kata);
    if ($msg->oke){
        $kode=$msg->kode;
        $rep=trim($msg->isi,trim_message());
    }else
    return "Format xreplace salah.";
    
    $kode=str_ireplace("abu_daud","abu-daud",$kode);
    $kode=str_ireplace("ibnu_majah","ibnu-majah",$kode);
    
    $xarr=str_toarray($rep,"/");
    if (count($xarr)==0)
    return "Format xreplace salah.";
    
    $OldText=trim($xarr[0]," ");
    if (count($xarr)==1){
        $NewText="";
    }else{
        $NewText=trim($xarr[1]," ");
    }
    
    if ($kode!="quran"){
        include "data/perawi.php";
        foreach ($perawi as $perawix ){
            for($a=1;$a<50;$a++){  
                
                $file="hadits/".$kode."-".$a.".json";
                
                if (file_exists($file) !== true){
                    break;
                }
                $status=replace_in_file($file,$OldText,$NewText);
                if ($status!="")
                $psn.=$status;
            }
        }
    }else
    if ($kode=="quran"){
        for($a=1;$a<114;$a++){  
            
            $file="quran/".$a.".json";
            if (file_exists($file) !== true){
                break;
            }
            $status=replace_in_file($file,$OldText,$NewText);
            if ($status!="")
            $psn.=$status;
        }
    }
    
    if ($psn==""){
        return "Tidak ada kata dalam '".$kode."' yang mengandung kata '".$OldText."'";
    }
    //$psn=preg_replace("/\s+/", " ", $psn);
    return kirim($psn);
}
/*
function replace_text_onhadits($perawi,$kata){
    $psn="";
    
    $xarr=str_toarray($x);
    if (count($xarr)==0){
        return kirim("Format salah\nContoh:\n/bukhari xreplace -ramadjan yang -ramadhan yang");
    }
    $OldText=$xarr[0];
    if (count($xarr)==1){
        $NewText="";
    }else{
        $NewText=$xarr[1];
    }
    
    for($a=1;$a<50;$a++){  
        
        $file="hadits/".$perawi."-".$a.".json";
        if (file_exists($file) !== true){
            break;
        }
        $psn.= replace_in_file($file,$OldText,$NewText)."\n\n";
    }
    if ($psn=="..."){
        $psn="Tidak ada file yang diedit.";
    }
    $psn=trim(preg_replace("/\s+/", " ", $psn));
    return kirim($psn);
}

function replace_text_onquran($kata){
    $psn="";
    
    $x=substr($kata,stripos($kata,$botreplace)+strlen($botreplace)+1);
    $xarr=get_kode_toarray($x);//explode(" ",$x);
    if (count($xarr)==0){
        return kirim("Format salah\nContoh:\n/quran botreplace -ramadjan yang -ramadhan yang");
    }
    $OldText=$xarr[0];
    if (count($xarr)==1){
        $NewText="";
    }else{
        $NewText=$xarr[1];
    }
    
    for($a=1;$a<114;$a++){  
        
        $file="quran/".$a.".json";
        if (file_exists($file) !== true){
            break;
        }
        $psn.= replace_in_file($file,$OldText,$NewText)."\n\n";
    }
    if (trim($psn," ")==""){
        $psn="Tidak ada file yang diedit.";
    }
    $psn=trim(preg_replace("/\s+/", " ", $psn));
    return kirim($psn);
}
*/
