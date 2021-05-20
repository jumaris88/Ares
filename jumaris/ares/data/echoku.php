<?php
function no_surah_tidak_ditemukan(){ return " tidak ditemukan.*\n\nContoh:\n".tanda()."qs 112\n".tanda()."quran 1:5\n".tanda()."QS 2:10-15";}
function trim_message(){return " <>{}(][);=/:+#_@!?-,.";};
function kuote($word){ return  '/'.preg_quote($word, '/').'/i';}
function italic($word){ return  " _".$word."_ ";}
function bold($word){ return  " *".mb_strtoupper($word)."* ";}
function echo_error(){return "Maaf ada error, kami akan segera memperbaikinya 🙏";}
function contoh_format_qs(){return "Afwan Format salah...\n\nContoh:\n".tanda()."qs 10:5-9\n".tanda()."qs 114\n".tanda()."qs 2:5\n".tanda()."qs membaca Al-qur'an";}
function tanda(){return ".";};

?>