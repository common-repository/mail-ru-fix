<?
include("../wp-config.php");
require ( ABSPATH . 'wp-admin/includes/image.php' );
mysql_connect(DB_HOST, DB_USER,DB_PASSWORD) or die ("Hata oluştu ! Kullanıcı adı veya şifre yanlış");
mysql_select_db(DB_NAME) or die ("Hata oluştu ! Belirttiğiniz veritabanı ismi hatalı.");
mysql_query("SET NAMES 'utf8'");

$adi=$baslik[1];
$adi_s=sanitize_title($adi);

$kidler=explode(",",$kids[1]);
for($i=0; $i<count($kidler); $i++){
if($kidler[$i]!=0){
$kid[]=$kidler[$i];
}
}


if($resimcek[1]==1){
	for($m=0; $m<count($gal); $m++){
	if(substr($gal[$m][1],0,2)=="//"){
	$fazla_resim="http:".$gal[$m][1];
	} else {
	$fazla_resim=$gal[$m][1];
	}
	$fazla_isim=$adi_s."-extra-".$m."";
	resimal($fazla_resim,$fazla_isim);
	$bunu[]=$gal[$m][1];
	$bunla[]="/wp-content/uploads/".$adi_s."-extra-".$m.".jpg";
	}
}



$kontrol=mysql_num_rows(mysql_query("SELECT * FROM ".$table_prefix."posts WHERE post_name='$adi_s'"));
if ($kontrol=='0') 
{
$v_content=str_replace($bunu,$bunla,str_replace(array("&lt;","&gt;",),array("<",">"),$icerik[1]));
$insert_post = array(); 

$insert_post['post_title'] = $adi; 

$insert_post['post_name'] = $adi_s; 

$insert_post['post_status'] = $post_durum[1]; 

$insert_post['post_content'] = $v_content;

$insert_post['post_excerpt'] = $kisa[1]; 

$insert_post['tags_input'] = $etiket[1]; 

$insert_post['post_author'] = 1; //Admin 

$insert_post['post_category'] = $kid;

if ($inserted_id = wp_insert_post($insert_post)) {
add_post_meta($inserted_id,'_aioseop_description',$des);	
add_post_meta($inserted_id,'_aioseop_title',$v_adis);	
add_post_meta($inserted_id,'_aioseop_keywords',$v_etiket);
if($resimcek[1]==1){
	if($resim[1]=="one_cikan_gorsel"){
	wp_resim_ekle($resim[2], $inserted_id, $adi_s,'kucukresim');
	} else {
	$bu=resimal($resim[2],$adi_s);
	add_post_meta($inserted_id,$resim[1],$bu);
	}
} else {
add_post_meta($inserted_id,$resim[1],$resim[2]);
}
for($m=0; $m<count($ozeller); $m++){
	if($ozeller[$m][1]=="ozel_alan" && !empty($ozeller[$m][3])){
	add_post_meta($inserted_id,$ozeller[$m][2],str_replace(array("&lt;","&gt;",),array("<",">"),urldecode($ozeller[$m][3])));
	} else if($ozeller[$m][1]=="taxonomy" && !empty($ozeller[$m][3])){
	$nez=explode(",",str_replace(array("&lt;","&gt;",),array("<",">"),urldecode($ozeller[$m][3])));
	for($i=0; $i<=count($nez)-1; $i++){
	if($nez[$i] !== ""){
	$bir=trim($nez[$i]);
	$iki=trsil($bir);
	$k = mysql_fetch_array(mysql_query("Select * from ".$table_prefix."terms where slug ='$iki'"));
	if($k['term_id']){
	$id1 =$k['term_id'];
	$kk = mysql_fetch_array(mysql_query("Select * from ".$table_prefix."term_taxonomy where term_id ='$id1'"));
	$id2 = $kk['term_taxonomy_id'];	
	} else {
	mysql_query("Insert Into ".$table_prefix."terms (name,slug) values ('$bir','$iki')");	
	$id1 = mysql_insert_id();
	mysql_query("Insert Into ".$table_prefix."term_taxonomy (term_id,taxonomy) values ('$id1','$ozeller[$m][2]')");
	$id2 = mysql_insert_id();	
	}
	mysql_query("Insert Into ".$table_prefix."term_relationships (object_id,term_taxonomy_id) values ('$inserted_id','$id2')");
	}
	}
	}
}
$v_content=mysql_real_escape_string($v_content);
mysql_query("Update ".$table_prefix."posts set post_content='$v_content' where ID='$inserted_id'");
echo '<statu value="1">';
} else {
echo '<statu value="2">';
}
} else {
echo '<statu value="0">';
}


function trsil($tr)
{
$bul = array ('Z', 'X', 'C', 'V', 'B', 'N', 'M', 'A', 'S', 'D', 'F', 'G', 'H', 'J', 'K', 'L', 'Q', 'W', 'E', 'R', 'T', 'Y', 'U', 'I', 'O', 'P', '|'); 
		$degis = array ('z', 'x', 'c', 'v', 'b', 'n', 'm', 'a', 's', 'd', 'f', 'g', 'h', 'j', 'k', 'l', 'q', 'w', 'e', 'r', 't', 'y', 'u', 'ı', 'o', 'p',' '); 
		$sef=str_replace($bul, $degis,$tr );
	
	$bul   = array ('â€™', '.', 'Ä°', 'Ã‡', 'Ãœ', 'Ã¼', 'Ã–', 'Ã¶', 'ÅŸ', 'Å', 'ÄŸ', 'Ä', 'Ã§', 'Ä±', ' ', 'ı', 'İ', 'ç', 'Ç', 'Ü', 'ü', 'Ö', 'ö', 'ş', 'Ş', 'ğ', 'Ğ', '(',')','&'); 
		$degis = array ('-', '-', 'i', 'c', 'u', 'u', 'o', 'o', 's', 's', 'g', 'g', 'c', 'i', '-', 'i', 'i', 'c', 'c', 'u', 'u', 'o', 'o', 's', 's', 'g', 'g','-','-','-'); 
		$sef = str_replace($bul, $degis, $sef); 
		
   	    $bul = array('&quot;', '&amp;', '\r\n', '\n', '/', '\\', '+', '<', '>');
    $sef = str_replace ($bul, '-', $sef);
	    $bul = array('é', 'è', 'ë', 'ê', 'É', 'È', 'Ë', 'Ê');
    $sef = str_replace ($bul, 'e', $sef);
	
	 $bul = array('/[^a-z0-9\-<>]/', '/[\-]+/', '/<[^>]*>/');
    $repl = array('', '-', '');
   $sef = preg_replace ($bul, $repl, $sef);

   $bul=array('---','--','----','-----','-----');
   $degis=array('-','-','-','-','-');
   $sef = str_replace($bul, $degis, $sef);
   return $sef;
}
function get_data($url)
{
	$url = preg_replace('/ /', '%20', $url);
		if(function_exists('curl_init')){
			
			$curl = curl_init($url);
			curl_setopt ($curl, CURLOPT_TIMEOUT, "50");
			curl_setopt ($curl, CURLOPT_USERAGENT, "Mozilla/5.0 (Windows NT 6.1) AppleWebKit/534.30 (KHTML, like Gecko) Chrome/12.0.742.122 Safari/534.30");
			curl_setopt ($curl, CURLOPT_RETURNTRANSFER, TRUE);
			curl_setopt ($curl, CURLOPT_HEADER, 0);
			curl_setopt ($curl, CURLOPT_SSL_VERIFYPEER, FALSE);
			@curl_setopt ($curl, CURLOPT_FOLLOWLOCATION, true);
			@curl_setopt ($curl, CURLOPT_MAXREDIRS, 10);
			
			$curlResult = curl_exec($curl);
	        curl_close($curl);
	        return $curlResult;
		} else {
			$veri=file_get_contents($url);
			return $veri;
		}
}

function wp_resim_ekle($url, $pid, $bas = null, $ozelalan = null){
$filename = $bas.".jpg";
$filename = remove_accents($filename);
if (function_exists('mb_strtolower')) {
$filename = mb_strtolower($filename, 'UTF-8');
}
$filename = utf8_uri_encode($filename);
$filetype = wp_check_filetype($url);
extract($filetype);
if (!$type) $type = "";
$upload = wp_upload_bits($filename, $filetype, @get_data($url));
if ( !empty($upload['error']) ) {
return "Resim eklenemedi!<br />Hata: ".$upload['error']."<br />";
} else {
$neyim = $upload['file'];
$attachment = array(
'guid' => $upload['url'],
'post_mime_type' => $type,
'post_title' => $bas,
'post_content' => '',
'post_type' => 'attachment',
'post_parent' => $pid
);

$attach_id = wp_insert_attachment($attachment, $neyim, $pid);
$attach_data = wp_generate_attachment_metadata($attach_id, $neyim);
wp_update_attachment_metadata($attach_id, $attach_data);
add_post_meta($pid, '_thumbnail_id', $attach_id, true);
$nane=$neyim;
return $nane;
}
}
function resimal($baglanti,$game){
$nereye="/wp-content/uploads/";
$nereye = $_SERVER[DOCUMENT_ROOT].$nereye;
$olustur = $nereye.$game.".jpg";
$veri=get_data($baglanti);
$dosya = fopen($olustur, 'w+');
$ayar_yaz =(''.$veri.'');
fwrite($dosya, $ayar_yaz);
fclose($dosya);
return "/wp-content/uploads/".game.".jpg";
}


?>