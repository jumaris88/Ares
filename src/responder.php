<?php
namespace ares;

use Terjemah\ClassTerjemah;
use ares\message;

include "config.php";
include "data/echoku.php";
include "myfunc.php";
include "modifdata.php";
include "replacer.php";

class responder{
    
    public $pesan="";
    public $isi="";
    
    public function __construct($message){
        
        $message=strtolower(trim($message,trim_message()));
        
        $message=fixkey($message);
        
        $obj_message=new message($message);
        if ($obj_message->oke){
            $kode=$obj_message->getkode();
            $isi=$obj_message->getvalue();
            $this->isi=$isi;
            if ($kode!= "" or $kode!=null){
                if (strlen($isi)>200 && $kode!="terjemah_ke")
                $this->pesan="";
                else
                $this->pesan = $this->$kode($isi);
            }
        }
    }
    
    public function bukhari($message){ return $this->hadits("bukhari",$message);}
    public function muslim($message){ return $this->hadits("muslim",$message);}
    public function ahmad($message){ return $this->hadits("ahmad",$message);}
    public function malik($message){ return $this->hadits("malik",$message);}
    public function tirmidzi($message){ return $this->hadits("tirmidzi",$message);}
    public function abu_daud($message){ return $this->hadits("abu-daud",$message);}
    public function ibnu_majah($message){ return $this->hadits("ibnu-majah",$message);}
    public function nasai($message){ return $this->hadits("nasai",$message);}
    public function darimi($message){ return $this->hadits("darimi",$message);}
    
    public function xreplace($mss){ return replace($mss); }
    public function list_bug($mss){ return _listbug($mss); }
    public function add_bug($mss){ return _addbug($mss); }
    
    public function add_fix($mss){ return _addfix($mss); }
    public function addfix($mss){ return _addfix($mss); }
    public function del_fix($mss){ return _delfix($mss); }
    public function delfix($mss){ return _delfix($mss); }
    public function add_ignore($mss){ return _addignore($mss); }
    public function del_ignore($mss){ return _delignore($mss); }
    public function list_fix($mss){ return _listfix($mss); }
    public function list_ignore($mss){ return _listignore($mss); }
    
    public function quran($message){
        
        if (contains($message,":") or contains($message,"-")){
            $ar=get_nomor($message);
            if ($ar=="" or $ar==null)
            return $this->cari_quran($message);
            
            $hitung=count($ar);
            
            if ($hitung==2){
                return $this->buka_ayat($ar[0],$ar[1]);
            }else
            if ($hitung>=3){
                return $this->buka_ayat($ar[0],$ar[1],$ar[2]);
            }
            return $this->send(contoh_format_qs());
        }else
        if (is_numeric($message))
        return $this->buka_surah($message);
        else
        return $this->cari_quran($message);
        
        
        return contoh_format_qs();
    }
    
    public function hadits($perawi,$message){
        if (is_numeric($message)!==false)
        return $this->bukaHadits($perawi,$message);
        else
        return $this->cari_hadits($perawi,$message);
    }
    
    public function cari_quran($kata){
        $msg="";
        $mirip="";
        $mirip2="";
        $potong="";
        $jmllist=114;
        $count=0;
        $cari=hapuskata($kata);
        if (strlen($cari)<=2){
            return "*Maaf Keyword pencarian salah.*\n\nContoh keyword:\n/quran 1:5\n/qs 1:5\n/qur'an 112\n/quran demi masa\n/Bukhari mandi junub\n/tirmidzi 150\n/ibnu majah 1242\n\nSupport 9 Imam :\n[ Bukhari, Muslim, Abu Daud, Ahmad, Malik, Darimi, Ibnu Majah, Nasai, Tirmidzi ]\nSupport Pencarian Nomor Surah, Ayat dan Isi Alqur'an";
        }
        for($a=1;$a<$jmllist;$a++){  
            
            $file=dir_quran.$a.".json";
            
            $content = file_get_contents($file);
            $json = json_decode($content,true);
            $ayatPerFile = $json[$a]["number_of_ayah"];
            
            for($b=1;$b<$ayatPerFile;$b++){  
                $isiqs=$json[$a]["translations"]["id"]["text"][$b];
                if (contains($isiqs,$cari) || contains($isiqs,$kata)){
                    $count ++;
                    $namas= $json[$a]["name_latin"];
                    if ($count>=50){
                        $potong.=$namas." ".$a." ayat ".$b."//";
                    }else{
                        if (str_word_count($cari)>=2){
                            $mirip.="★★★★\n";
                        }
                        $mirip.=$namas." [".$a."] ayat ".$b."\n";
                        $mirip.=$json[$a]["translations"]["id"]["text"][$b]."\n\n";
                    }
                    
                } else
                if (contains_perkata($isiqs,$cari)){
                    $count ++;
                    $namas= $json[$a]["name_latin"];
                    if ($count>=50){
                        $potong.=$namas." ".$a." ayat ".$b."//";
                    }else{
                        if (str_word_count($cari)>=2){
                            $mirip2.="★★★\n";
                        }
                        $mirip2.=$namas." [".$a."] ayat ".$b."\n";
                        $mirip2.=$json[$a]["translations"]["id"]["text"][$b]."\n\n";
                    }
                    
                } else
                if (contains_word($isiqs,$cari)){
                    $count ++;
                    $namas= $json[$a]["name_latin"];
                    if ($count>=50){
                        $potong.=$namas." ".$a." ayat ".$b."//";
                    }else{
                        if (str_word_count($cari)>=2){
                            $msg.="★★\n";
                        }
                        $msg.=$namas." [".$a."] ayat ".$b."\n";
                        $msg.=$json[$a]["translations"]["id"]["text"][$b]."\n\n";
                    }
                    
                }
            }
        }
        //$msg.="\n\n"."FINISH...Ditemukan : ".$count." kata yang cocok.\n";
        if ($msg=="" && $mirip=="" && $mirip2=""){
            $err = "Pencarian \"".$kata."\" tidak di temukan.\nSilahkan di ulangi dengan keyword yang benar\nContoh:\n".tanda()."quran demi waktu\n/qs demi waktu\n/qs 112\n/qur'an 85\n/qs 1:5\n/QS 5:10-15";
            return $err;
        }else{
            $msghead="Pencarian \"".$kata."\"\nDitemukan : ".$count." Ayat yang cocok.\n\n";
            if ($count>=50){
                $msghead.=$mirip.$mirip2.$msg;
                $msghead.="Karena pencarian melebihi limit maka pencarian hanya menampilkan nama surah dan ayat yang mengandung keyword pencarian anda dibawah ini:\n\n";
                $msghead.=$potong;
            }else{
                $msghead.=$mirip.$mirip2.$msg;
            }
            
            $msghead=kasi_tanda($msghead,$cari);
            return $msghead;
        }
    }
    
    public function cari_hadits($perawi,$kata){
        //$kata=str_replace("quran","qur'an",$kata);
        $msg="";
        $mirip="";
        $mirip2="";
        $potong="";
        $jmllist=50;
        $count=0;
        
        $cari=hapuskata($kata);
        //return $perawi." & ".$kata." & ".$cari;
        if (mb_strlen($cari)<=2){
            return "*Maaf pencarian Keyword tidak benar.*\n\nContoh keyword:\n/quran 1:5\n/qs 1:5-8\n/qur'an 112\n/quran iman dan taqwa\n/Bukhari mandi junub\n/tirmidzi 150\n/ibnu majah 1242\n\nSupport 9 Imam :\n[ Bukhari, Muslim, Abu Daud, Ahmad, Malik, Darimi, Ibnu Majah, Nasai, Tirmidzi ]\nSupport Pencarian Nomor Surah, Ayat dan Isi Alqur'an";
            exit;
        }
        
        for($a=1;$a<$jmllist;$a++){  
            
            $file=dir_hadits.$perawi."-".$a.".json";
            //echo $file;
            if (file_exists($file) !== true){
                break;
            }
            $hadits = file_get_contents($file);
            $json_a = json_decode($hadits,true);
            $haditsPerFile = $json_a["data"]["total"];
            
            for($b=0;$b<$haditsPerFile;$b++){  
                
                if (contains( $json_a["data"]["hadits"][$b]["id"],"$cari")){
                    $count ++;
                    if ($count>=20){
                        $potong.="No.".$json_a["data"]["hadits"][$b]["number"]."//";
                    }else{
                        if (str_word_count($cari)>=2)
                        $mirip.="★★★★\n";
                        $mirip.="No.".$json_a["data"]["hadits"][$b]["number"]."\n";
                        $mirip.=$json_a["data"]["hadits"][$b]["id"]."\n\n";
                    }
                } else
                if (contains_perkata( $json_a["data"]["hadits"][$b]["id"],"$cari")){
                    $count ++;
                    if ($count>=20){
                        $potong.="No.".$json_a["data"]["hadits"][$b]["number"]."//";
                    }else{
                        if (str_word_count($cari)>=2)
                        $mirip2.="★★★\n";
                        $mirip2.="No.".$json_a["data"]["hadits"][$b]["number"]."\n";
                        $mirip2.=$json_a["data"]["hadits"][$b]["id"]."\n\n";
                    }
                } else
                if (contains_word( $json_a["data"]["hadits"][$b]["id"],"$cari")){
                    $count ++;
                    if ($count>=20){
                        $potong.="No.".$json_a["data"]["hadits"][$b]["number"]."//";
                    }else{
                        if (str_word_count($cari)>=2)
                        $msg.="★★\n";
                        $msg.="No.".$json_a["data"]["hadits"][$b]["number"]."\n";
                        $msg.=$json_a["data"]["hadits"][$b]["id"]."\n\n";
                    }
                }
            }
        }
        if ($msg=="" && $mirip2=="" && $mirip==""){
            $err = "Pencarian \"".$kata."\" dalam hadits \"".strtoupper($perawi)."\" tidak di temukan\n\nContoh:\nbukhari waktu mustajab do'a\nnasai subuh dan junub\n/darimi 270\n/tirmidzi 1520";
            return $err;
        }else{
            $msghead="Pencarian \"".$kata."\"\nDitemukan : ".$count." Hadits ".strtoupper($perawi)." yang cocok.\n";
            $msghead.="support 9 Imam :\n[ Bukhari, Muslim, Abu Daud, Ahmad, Malik, Darimi, Ibnu Majah, Nasai, Tirmidzi ]\n\n";
            if ($count>=20){
                $msghead.=$mirip.$mirip2.$msg;
                $msghead.="Karena pencarian melebihi limit maka pencarian hanya menampilkan Nomor Hadits yang mengandung keyword pencarian anda dibawah ini:\n\n";
                $msghead.=$potong;
            }else{
                $msghead.=$mirip.$mirip2.$msg;
            }
            $msghead=kasi_tanda($msghead,$cari);
            return $msghead;
        }
    }
    
    
    public function buka_surah($nosurah){
        $nosurah=preg_replace("/\s+/", "", $nosurah);
        $msg="";
        $cnt=0;
        $file=__DIR__."/../book/quran/".$nosurah.".json";
        
        if (file_exists($file) !== true){
            $msg="*Nomor Surah ".$nosurah.no_surah_tidak_ditemukan();
        }else{
            $alquran = file_get_contents($file);
            $json_a = json_decode($alquran,true);
            
            $arrgabung = array_combine($json_a["$nosurah"]["text"], $json_a["$nosurah"]["translations"]["id"]["text"]);
            $msg= $json_a["$nosurah"]["name_latin"]." Surah ke-" . $json_a["$nosurah"]["number"]." (".$json_a["$nosurah"]["number_of_ayah"]." ayat)"."\n\n";
            
            foreach($arrgabung as $key => $value){
                $cnt++;
                
                $msg.=$key."\n";
                $msg.="(".$cnt.") ";
                $msg.=$value."\n\n";
            }
        }    
        return $msg;
    }
    
    public function buka_ayat($nosurah,$noayat,$sampaiayat=0){
        $msg="";
        $nmayat="";
        $file=__DIR__."/../book/quran/".$nosurah.".json";
        
        if (file_exists($file) !== true){
            $msg="*Nomor Surah ".$nosurah.no_surah_tidak_ditemukan();
        }else{
            $alq = file_get_contents($file);
            $json_a = json_decode($alq,true);
            
            $jmlhAyat=$json_a[$nosurah]["number_of_ayah"];
            
            if ($sampaiayat==0){
                $nmayat=$noayat;
                $msg.="Surah ".$json_a["$nosurah"]["name_latin"]." Ayat: ".$noayat."\n\n";
            }else{
                $nmayat=$noayat."-".$sampaiayat;
                $msg.="Surah ".$json_a["$nosurah"]["name_latin"]." Ayat: ".$noayat."-".$sampaiayat."\n\n";
            }
            
            if ($noayat<=$jmlhAyat and $sampaiayat!=0 and $sampaiayat<=$jmlhAyat and $sampaiayat>$noayat){
                for($a=$noayat;$a<$sampaiayat+1;$a++){  
                    $arab = $json_a["$nosurah"]["text"]["$a"];
                    $trans = $json_a["$nosurah"]["translations"]["id"]["text"]["$a"];
                    //$msg=$json_a["$nosurah"]["name_latin"]." Ayat ke ".$noayat."\n\n";
                    $msg.=$arab."\n(".$a.") ";
                    $msg.=$trans."\n\n";
                }
            }else
            if ($noayat<=$jmlhAyat and $sampaiayat==0){
                $arab = $json_a["$nosurah"]["text"]["$noayat"];
                $trans = $json_a["$nosurah"]["translations"]["id"]["text"]["$noayat"];
                $msg.=$arab."\n\n";
                $msg.=$trans."\n\n";
            }else{
                $msg="*Nomor Ayat ".$nmayat." Salah.*\n\nContoh:\n/qs 112\n/quran 1:5\n/QS 2:10-15";
            }
            
        }
        return $msg;
    }
    
    public function bukaHadits($perawi,$angka){
        
        $msg="";
        for($a=1;$a<50;$a++){  
            
            $file=__DIR__."/../book/hadits/".$perawi."-".$a.".json";
            if (file_exists($file) !== true){
                break;
            }
            $hadits = file_get_contents($file);
            $json_a = json_decode($hadits,true);
            $haditsPerFile = $json_a["data"]["total"];
            
            for($b=0;$b<$haditsPerFile;$b++){  
                
                $nomor = $json_a["data"]["hadits"][$b]["number"];
                
                if ($angka==$nomor){
                    $msg="Hadits ".strtoupper($perawi)." No.".$nomor.":\n".$json_a["data"]["hadits"][$b]["id"];
                    break 2;
                }
            }
        }
        if ($msg==""){
            $err = "Hadits ".strtoupper($perawi)." dengan nomor ".$angka." tidak di temukan";
            return $err;
        }else
        return $msg;
    }
    
    
    public function myrandhadits($prw){
        $arr = array(
            "abu-daud" => 31,
            "ahmad" => 30,
            "bukhari" => 46,
            "darimi" => 21,
            "ibnu-majah" => 30,
            "malik" => 12,
            "muslim" => 34,
            "nasai" => 36,
            "tirmidzi" => 25
            );
            foreach ($arr as $key => $val){
                if ($key==$prw){
                    return rand(1,$val);
                }
            }
    }
    
    public function myrandquran(){
        return rand(1,114);
    }
    
    public function random_hadits($message){
        include "data/perawi.php";
        $nomor="no detect";
        $rand=array_rand($perawi,1);
        $prw=$perawi[$rand];
        
        $file=__DIR__."/../book/hadits/".$prw."-".$this->myrandhadits($prw).".json";
        if(file_exists($file)){  
            $hadits = file_get_contents($file);
            $json_a = json_decode($hadits,true);
            $haditsPerFile = $json_a["data"]["total"];
            $index=rand(0,$haditsPerFile);
            $nomor = $json_a["data"]["hadits"][$index]["number"];
            return "*Hadits ".strtoupper($prw)." No.".$nomor."*:\n".$json_a["data"]["hadits"][$index]["id"];
        }
        return "Hadits not found.\nSilahkan ulangi kembali.";
    }
    
    public function random_quran($message){
        $i=1;
        while($i<5){
            $ran=$this->myrandquran();
            $file=__DIR__."/../book/quran/".$ran.".json";
            if(file_exists($file)){  
                
                $quran = file_get_contents($file);
                $json_a = json_decode($quran,true);
                
                $nmayat=$json_a[$ran]["name_latin"];
                $jmlhAyat=$json_a[$ran]["number_of_ayah"];
                $no_ayat=rand(1,$jmlhAyat);
                $arab = $json_a["$ran"]["text"]["$no_ayat"];
                $trans = $json_a["$ran"]["translations"]["id"]["text"]["$no_ayat"];
                $tafsir= $json_a["$ran"]["tafsir"]["id"]["kemenag"]["text"]["$no_ayat"];
                
                return "*Surah: ".$nmayat." Ayat: ".$no_ayat."*\n\n".$arab."\n\n_".$trans."_\n\n*Tafsir Oleh Kementrian Agama:*\n".$tafsir;
            }
            $i++;
        }
        return "qur'an not found.\nSilahkan ulangi kembali";
    }
    
    public function terjemah_ke($mess){
        $message=new message($mess,__DIR__."/data/bahasa.php","bahasa");
        if ($message->oke){
            if ($message->value==""){
                return "Format terjemahan salah.\n\nContoh :\n\n".tanda()."terjemah ke arab semoga Allah merahmati kamu.\n\n".tanda()."terjemah ke id how are you\n\n".tanda()."terjemah ke inggris kamu sedang apa";
            }
            $bahasa=fixbahasa($message->kode);
            $tr = new ClassTerjemah();
            #$tr->setSource('en'); // Translate from English
            $tr->setSource(); // Detect language automatically
            $tr->setTarget($bahasa); // Translate to Georgian
            
            return $tr->translate($message->value);
            
        }else{
            return "Format terjemahan salah.\n\nContoh :\n\n".tanda()."terjemah ke arab semoga Allah merahmati kamu.\n\n".tanda()."terjemah ke id how are you\n\n".tanda()."terjemah ke inggris kamu sedang apa";
        }
        
    }
    
    public function send(){
        echo json_encode(array("replies" => array(array("message" => $this->pesan),array(""=>""))));
    }
    
}

