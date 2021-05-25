<?php

namespace ares;

class message
{
    public $message;
    public $kode;
    public $value;
    public $oke=0;
    
    public function getkode(){
        return $this->kode;
    }
    
    public function getvalue(){
        return $this->value;
    }
    
    public function __construct($message,$include=file_key,$namaarray="key") {
        
        include $include;
        $arrmessage = $this->str_toarray($message," ");
        if (count($arrmessage)>=2)
        $duakata=$arrmessage[0]." ".$arrmessage[1];
        else
        $duakata="";
        if (count($arrmessage)>=3)
        $tigakata=$arrmessage[0]." ".$arrmessage[1]." ".$arrmessage[2];
        else
        $tigakata="";
        
        if (count($arrmessage)>=1)
        //foreach ( $arrmessage as $inkey ){
        foreach ($$namaarray as $val){
            if (trim($arrmessage[0],$this->mytrim())==$val or $duakata==$val or $tigakata==$val){
                $this->oke=1;
                $this->kode=$this->fixkode($val);
                $isi=trim($this->str_ireplace_once($val,"",$message)," ");
                $this->value=$isi;
                break;
            }
        }
        //}
    }
    
    private function contains($str,$search){
        if (stripos($str, $search) !== false) 
        return 1;
        else
        return 0;
    }
    
    private function str_ireplace_once($search, $replace, $subject, &$count = 0){
        $pos = stripos($subject, $search);
        if (false === $pos)
        return $subject;
        
        $count = 1;
        return substr_replace($subject, $replace, $pos, strlen($search));
    }
    
    private function mytrim(){
        return " <>{}(][);=/:+#_@!?-,.";
        
    }
    
    private function fixkode($kode){
        $s=str_replace("abu-daud","abu_daud",$kode);
        $s=str_replace("ibnu-majah","ibnu_majah",$s);
        $s=str_replace("abu daud","abu_daud",$s);
        $s=str_replace("ibnu majah","ibnu_majah",$s);
        $s=str_replace("abudaud","abu_daud",$s);
        $s=str_replace("ibnumajah","ibnu_majah",$s);
        
        $s=str_replace("qur'an","quran",$s);
        $s=str_replace("al quran","quran",$s);
        $s=str_replace("al-quran","quran",$s);
        $s=str_replace("alquran","quran",$s);
        $s=str_replace("qs","quran",$s);
        
        $s=str_replace(" ","_",$s);
        
        return trim($s,$this->mytrim());
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
    
    
}

?>
