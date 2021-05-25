<?php

function _delignore($Old){
    $OldText= str_toarray($Old,"/");
    $ret="";
    
    //$OldText=trim(strtolower($OldText),trim_message());
    $FilePath=file_ignore;
    if(file_exists($FilePath)===TRUE){
        if(is_writeable($FilePath)){
            try{
                include file_ignore;
                foreach ($OldText as $oldval){
                    $sukses=0;
                    foreach ($hapuskata as $value => $key){
                        if ($key==trim($oldval,trim_message())){
                            $sukses=1;
                            $ret.=$oldval."✅\n";
                            unset($hapuskata[$value]);
                        }
                    }
                    if ($sukses==0){
                        $ret.=$oldval."❌\n";
                    }
                }
                if ($ret!=""){
                    if(file_put_contents($FilePath,"<?php $"."hapuskata = ".var_export($hapuskata,true)."; ?>")){
                        return  "sukses terhapus:\n\n".$ret;
                    }else{
                        return  "Error while writing file ".$FilePath;
                    }
                }
                return "Tidak ditemukan kata ".implode(",",$OldText);
            }catch(Exception $e){
                return 'Error : '.$e;
            }
        }else{
            return 'File '.$FilePath.' is not writable !';
        }
    }else{
        return 'File '.$FilePath.' does not exist !';
    }
}

function _addignore($NewT){
    if ($NewT=="" or strlen($NewT)<=1)
    return "format addhapuskata salah atau kata terlalu pendek.";
    $ret="";
    $NewText = str_toarray($NewT,"/");
    $FilePath=file_ignore;
    if(file_exists($FilePath)===TRUE){
        if(is_writeable($FilePath)){
            try{
                include file_ignore;
                $oke=1;
                foreach ($NewText as $newval){
                    foreach ($hapuskata as $value => $key){
                        if ($key==trim($newval,trim_message())){
                            $oke=0;
                            $ret .= $newval."❌\n";
                        }
                    }
                    if ($oke==1){
                        $hapuskata[].=trim($newval,trim_message());
                        $ret .= $newval."✅\n";
                    }
                }
                
                if ($ret!=""){
                    if(file_put_contents($FilePath,"<?php $"."hapuskata = ".var_export($hapuskata,true)."; ?>")){
                        return  "sukses menambahkan:\n".$ret;
                    }else{
                        return  "Error while writing file ".$FilePath;
                    }
                }
                return "Tidak ditemukan kata ".implode(",",$NewText);
            }catch(Exception $e){
                return 'Error : '.$e;
            }
        }else{
            return 'File '.$FilePath.' is not writable !';
        }
    }else{
        return 'File '.$FilePath.' does not exist !';
    }
}


function _addfix($kata){
    if ($kata=="" or contains($kata,"/")==0)
    return "Format addfix salah";
    
    $xarr=str_toarray($kata,"/");
    if (count($xarr)!=2){
        return "Format salah\nContoh:\n/addfix /shalat /sholat";
    }
    $key=trim($xarr[0],trim_message());
    $val=trim($xarr[1],trim_message());
    
    $FilePath=file_fixkey;
    try{
        include file_fixkey;
        foreach ($fixkey as $fixk=>$fixv){
            //if (empty($fixk)) continue;
            if ($fixk==$key or $fixv==$key){
                return "kata '".$key."' sudah ada";
            }
            
        }
        $addarr= array("$key"=>"$val");
        $finalarr=$fixkey+$addarr;
        
        if(file_put_contents($FilePath,"<?php $"."fixkey = ".var_export($finalarr,true)."; ?>")){
            return "sukses menambahkan '".$key."=>".$val."'";
        }else{
            return  "Error while writing file ".$FilePath;
        }
    }catch(Exception $e){
        return 'Error : '.$e;
    }
}

function _delfix($kata){
    $kata=trim($kata,trim_message());
    $cek="";
    if ($kata=="")
    return "Format delfix salah";
    
    $FilePath=file_fixkey;
    try{
        include file_fixkey;
        foreach ($fixkey as $fixk=>$fixv){
            
            if ($fixk==$kata or $fixv==$kata){
                
                unset($fixkey[$fixk]);
                $cek="ok";
                
                if(file_put_contents($FilePath,"<?php $"."fixkey = ".var_export($fixkey,true)."; ?>")){
                    return "sukses menghapus '".$kata."'";
                }else{
                    return  "Error while writing file ".$FilePath;
                }
                
                break;
            }
            
        }
        if ($cek=="")
        return "kata '".$kata."' tidak ditemukan";
    }catch(Exception $e){
        return 'Error : '.$e;
    }
}

function _addbug($kata){
    if ($kata=="")
    return "Maaf format menambahkan bug salah.\nContoh : \n".tanda()."bug penulisan kata ramadlan yang seharusnya ramadhan di hadits bukhari";
    
    $file= file_bug;
    include file_bug;
    
    $addarr= array("$kata"=>"Status belum Fix");
    $finalarr = $bug+$addarr;
    
    if(file_put_contents($file,"<?php $"."bug = ".var_export($finalarr,true)."; ?>")){
        return "sukses menambahkan bug:\n\n'".$kata."\n\nTerimakasih masukannya.\nBug ini akan segera di tinjau oleh pengembang.";
    }else{
        return  "Maaf ada kesalahan penyimpanan Bug";
    }
}
function _listbug($message){
    $xbug="";
    include file_bug;
    foreach ($bug as $key => $val){
        $xbug.="👉".$key."\n👉'".$val."'\n\n";
    }
    return $xbug;
}
function _listfix($message){
    $xbug="";
    include file_fixkey;
    foreach ($fixkey as $key => $val){
        $xbug.="👉".$key." => ".$val."\n\n";
    }
    return $xbug;
}
function _listignore($message){
    $xbug="";
    include file_ignore;
    foreach ($hapuskata as $key => $val){
        $xbug.="👉".$val."\n";
    }
    return $xbug;
}


