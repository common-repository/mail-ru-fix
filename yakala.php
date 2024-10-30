<?
$user=$_SERVER['HTTP_USER_AGENT'];
if($user != "kolaybot-udg"){
exit;
}
if($_POST['pid']){
$al=file_get_contents("https://botamk.com/kullanici/".$_POST['pid'].".xml");
preg_match('#<sitetip>(.*?)</sitetip>#si',$al,$sitetip);
preg_match('#<yayinla>(.*?)</yayinla>#si',$al,$post_durum);
preg_match('#<baslik>(.*?)</baslik>#si',$al,$baslik);
preg_match('#<etiket>(.*?)</etiket>#si',$al,$etiket);
preg_match('#<multikid>(.*?)</multikid>#si',$al,$kids);
preg_match('#<resimcek>(.*?)</resimcek>#si',$al,$resimcek);
preg_match('#<resim value="(.*?)" link="(.*?)" />#si',$al,$resim);
preg_match('#<kisa value="(.*?)">(.*?)</kisa>#si',$al,$kisa);
preg_match('#<icerik>(.*?)</icerik>#si',$al,$icerik);
preg_match_all('#<galeri>(.*?)</galeri>#si',$al,$gal,PREG_SET_ORDER);
preg_match_all('#<ozel tur="(.*?)" value="(.*?)">(.*?)</ozel>#si',$al,$ozeller,PREG_SET_ORDER);
include("func/me_".$sitetip[1].".php");
}
?>