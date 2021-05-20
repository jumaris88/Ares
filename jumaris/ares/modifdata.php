<?php



function delete_ignore_word($OldText){
    if ($OldText=="" or strlen($OldText)<=1)
    return "format salah atau kata terlalu pendek.";
    
    $OldText=trim(strtolower($OldText),trim_message());
    $FilePath="ares/data/hapuskata.php";
    if(file_exists($FilePath)===TRUE){
        if(is_writeable($FilePath)){
            try{
                include "ares/data/hapuskata.php";
                foreach ($hapuskata as &$value){
                    if (empty($value)) continue;
                    if ($value==$OldText){
                        
                        $value="";
                        //array_splice($katasambung, array_search($OldText, $katasambung), 1);
                        
                        if(file_put_contents($FilePath,"<?php $"."hapuskata = ".var_export($hapuskata,true)."; ?>")){
                            return  "sukses terhapus ".$OldText;
                        }else{
                            return  "Error while writing file ".$FilePath;
                        }
                    }
                }
                return "Tidak ditemukan kata '".$OldText."'";
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

function add_ignore_word($NewText){
    if ($NewText=="" or strlen($NewText)<=1)
    return "format addhapuskata salah atau kata terlalu pendek.";
    
    $NewText=trim(strtolower($NewText),trim_message());
    $FilePath="ares/data/hapuskata.php";
    if(file_exists($FilePath)===TRUE){
        if(is_writeable($FilePath)){
            try{
                include "ares/data/hapuskata.php";
                foreach ($hapuskata as $value){
                    if (empty($value)) continue;
                    if ($value==$NewText){
                        return "kata '".$NewText."' sudah ada";
                    }
                    
                }
                $hapuskata[].=$NewText;
                
                if(file_put_contents($FilePath,"<?php $"."hapuskata = ".var_export($hapuskata,true)."; ?>")){
                    return  "sukses menambahkan ".$NewText;
                }else{
                    return  "Error while writing file ".$FilePath;
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
}


function add_fix_word($kata){
    if ($kata=="" or contains($kata,"/")==0)
    return "Format addfix salah";
    
    $xarr=str_toarray($kata,"/");
    if (count($xarr)!=2){
        return "Format salah\nContoh:\n/addfix /shalat /sholat";
    }
    $key=trim($xarr[0],trim_message());
    $val=trim($xarr[1],trim_message());
    
    $FilePath="ares/data/fixkey.php";
    try{
        include "ares/data/fixkey.php";
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

function del_fix_word($kata){
    $kata=trim($kata,trim_message());
    $cek="";
    if ($kata=="")
    return "Format delfix salah";
    
    $FilePath="ares/data/fixkey.php";
    try{
        include "ares/data/fixkey.php";
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

function add_bug($kata){
    if ($kata=="")
    return "Maaf format menambahkan bug salah.\nContoh : \n".tanda()."bug penulisan kata ramadlan yang seharusnya ramadhan di hadits bukhari";
    
    include "ares/file/bug.php";
    $file="ares/file/bug.php";
    
    $addarr= array("$kata"=>"Status belum Fix");
    $finalarr = $bug+$addarr;
    
    if(file_put_contents($file,"<?php $"."bug = ".var_export($finalarr,true)."; ?>")){
        return "sukses menambahkan bug:\n\n'".$kata."\n\nTerimakasih masukannya.\nBug ini akan segera di tinjau oleh pengembang.";
    }else{
        return  "Maaf ada kesalahan penyimpanan Bug";
    }
}
function list_bug($message){
    $xbug="";
    include "ares/file/bug.php";
    foreach ($bug as $key => $val){
        $xbug.="👉".$key."\n👉'".$val."'\n\n";
    }
    return $xbug;
}



?>