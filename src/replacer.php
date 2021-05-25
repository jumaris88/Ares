<?php

use ares\message;

function replace_in_file($FilePath, $OldText, $NewText){
    //$Result = "";
    if(file_exists($FilePath)===TRUE){
        if(is_writeable($FilePath)){
            try{
                $FileContent = file_get_contents($FilePath);
                if (contains($FileContent,$OldText)){
                    $FileContent = str_ireplace($OldText, $NewText, $FileContent);
                    
                    if(file_put_contents($FilePath, $FileContent)){
                        return  "Success\n".$OldText." => ".$NewText."\n".$FilePath."\n";
                    }else{
                        return "";// "Error while writing file ".$FilePath;
                    }
                }
                return "";
            }catch(Exception $e){
                return "";//'Error : '.$e;
            }
        }else{
            return "";//'File '.$FilePath.' is not writable !';
        }
    }else{
        return "";//'File '.$FilePath.' does not exist !';
    }
    return "";
}

function replace($kata){
    $psn="";
    $ret="";
    $Headpsn="";
    $hadits_yg_diedit="";
    $msg = new message($kata);
    if ($msg->oke){
        $kode=$msg->kode;
        $rep=$msg->value;
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
    
    $header="Replace ".$kode." '".$OldText."' -> '".$NewText."'\n";
    
    if ($kode=="hadits"){
        include file_perawi;
        foreach ($perawi as $perawix ){
            for($a=1;$a<50;$a++){  
                $cod="";
                $file=dir_hadits.$perawix."-".$a.".json";
                if (file_exists($file) !== true){
                    break;
                }
                
                $Headpsn = "File ".$perawix."-".$a.".json\nNo.Hadits yang di edit:\n";
                $hadits = file_get_contents($file);
                $json_a = json_decode($hadits,true);
                $haditsPerFile = $json_a["data"]["total"];
                
                for($b=0;$b<$haditsPerFile;$b++){
                    $isihadits=$json_a["data"]["hadits"][$b]["id"];
                    $nomorhadits=$json_a["data"]["hadits"][$b]["number"];
                    if (contains($isihadits,$OldText)){
                        $cod.=$perawix." No.".$nomorhadits."\n";
                    }
                }
                if ($cod!=""){
                    replace_in_file($file,$OldText,$NewText);
                    $ret.= $Headpsn.$cod."\n\n";
                }
            }
        }
    }else
    if ($kode=="quran"){
        for($a=1;$a<114;$a++){  
            $cod="";
            $file=dir_quran.$a.".json";
            if (file_exists($file) !== true){
                break;
            }
            
            $content = file_get_contents($file);
            $json = json_decode($content,true);
            $ayatPerFile = $json[$a]["number_of_ayah"];
            $namasurah = $json[$a]["name_latin"];
            
            $Headpsn = "Qur'an Surah ".$namasurah."[".$a."]\n";
            
            for($b=1;$b<$ayatPerFile;$b++){  
                $isiqs=$json[$a]["translations"]["id"]["text"][$b];
                
                if (contains($isiqs,$OldText)){
                    $cod.= "Ayat ".$b."\n";
                }
            }
            
            if ($cod!=""){
                replace_in_file($file,$OldText,$NewText);
                $ret.= $Headpsn.$cod."\n";
            }
        }
    }
    
    
    if ($ret==""){
        return "Tidak ada kata dalam '".$kode."' yang mengandung kata '".$OldText."'";
    }
    $psn.=$header.$ret."=========================";
    _simpanhistori($psn);
    
    return $psn;
}
function _simpanhistori($histori){
    $file = file_history_replacer;
    $FileContent = file_get_contents($file);
    $FileContent .= $histori;
    file_put_contents($file, $FileContent);
}