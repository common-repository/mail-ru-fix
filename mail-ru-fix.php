<?php
/**
 * Plugin Name: BotEgo Connect
 * Plugin URI: http://botego.xyz
 * Description: Siteniz ve BotEgo.xyz arasındaki gerekli bağlantıyı sağlar.
 * Version: 1.0
 * Author: BotEgo
 * Author URI: http://botego.xyz
 * License: MIT
 */

 
add_action('admin_menu', 'blank_add_pages');
 

function fmd_db_sql($tip=0,$sorgu){
	global $wpdb;
	
	if($tip==0){
		//sadece query sorguları
		$wpdb->query($sorgu);
	}
	if($tip==1){
		//sadece satır sorguları
		return $wpdb->get_row($sorgu);
	}
	if($tip==2){
		//sadece toplu satır sorguları
		return $wpdb->get_results($sorgu);
		
	}
	
	
}

 
function blank_add_pages() {

 
  add_menu_page('Url Değiştirici','Video Değiştirici', 'manage_options', 'video_degistir', 'anafonk');
   
 

}
function fmy_Categ_tree($TermName='', $termID, $separator='', $parent_shown=true ,$katsec){
	
    $args = 'hierarchical=1&taxonomy='.$TermName.'&hide_empty=0&orderby=name&parent=';

            if ($parent_shown) {$term=get_term($termID , $TermName);

             if($separator!="33")

             $output=$separator.$term->name.' ';  

             else

             $output= $term->name.''; 

             $parent_shown=false;

             }

            if($separator!="33")

    $separator .= ' — '; 

     else $separator="";

    $terms = get_terms($TermName, $args . $termID);

    if(count($terms)>0){

        foreach ($terms as $term) {

            //$selected = ($cat->term_id=="22") ? " selected": "";

            //$output .=  '<option value="'.$category->term_id.'" '.$selected .'>'.$separator.$category->cat_name.'</option>';

            $output .= ' <tr>  <td>   <div class="i-checks"> '.$separator." ".$term->name.' </div></td><td><select style="width:100%" name="karsi['.$term->term_id.']"><option value="">< Bu kategoride işlem yapılması için video kaynağını seçin > '.$katsec.'</select></td></tr>';

            // "<input type='checkbox' value='$term->name'>".$separator.$term->name.'<br/>';

            $output .=  fmy_Categ_tree($TermName, $term->term_id, $separator, $parent_shown,$katsec);

        }

    }

    return $output;

}


function anafonk() {
	global $wpdb;
	$tablo1 = $wpdb->prefix . "posts"; 
	$tablo2 = $wpdb->prefix . "postmeta"; 
	if(isset($_REQUEST["fmd_onay_ver"]) and $_REQUEST["fmd_onay_ver"]==1){
		
		
		if(is_array($_REQUEST["karsi"])){
foreach($_REQUEST["karsi"] as $key=>$value)	{
	if($value=="")continue;
	if(isset($_REQUEST["nerede"]) and is_array($_REQUEST["nerede"])){
		$args = array( 'posts_per_page' => 9999999,   'category' => $key );
		
		
		
$myposts = get_posts( $args );
foreach ( $myposts as $post ) : setup_postdata( $post );

//var_dump($post);
		foreach($_REQUEST["nerede"] as $nere){
			if($nere=="yazi"){
				fmd_db_sql("0","update $tablo1 set post_content='".addslashes(fmd_bul_degistir($post->post_content,$value))."' where ID='$post->ID'");
			}
			else{
				
				
			$de=	fmd_db_sql(1,"select * from $tablo2 where post_id='$post->ID' and meta_key='$nere'");
			 	fmd_db_sql(0,"update from $tablo2 set meta_value='".addslashes(fmd_bul_degistir2($de->meta_value,$value))."' where post_id='$post->ID' and meta_key='$nere'");
			
			
			
			
			}
			
			
		}
		
		echo '<script>

    alert("İşlem Tamamlandı!\nSeçtiğiniz kategorideki videolarınız değiştirildi.");
 window.history.back();
</script> ';


 endforeach; 
wp_reset_postdata(); 



		
	}else continue;
	
	
	
}		
		}
		
		
		exit;
	}
	
	$katsec="";
	
	$a=file_get_contents("http://calisma.cbyfmd.com/liste.txt");
$b=explode("\n",$a);
foreach($b as $c){
	$d=explode(":x:",trim($c));
	$katsec.="<option value='$d[1]'>$d[0]";
	
	
}
?>
<div id="welcome-panel" class="welcome-panel">
		<input type="hidden" id="welcomepanelnonce" name="welcomepanelnonce" value="5ddfffee1f">		
			<div class="welcome-panel-content">
	<h2>Botego Video Değiştirici - Nasıl Kullanılır?</h2>
	<p class="about-description">Videolarınızı değiştirmek için öncelikle sitenizde yeni bir kategori oluşturun. Oluşturduğunuz kategori içine video değişimi yapmak istediğiniz konuları ekleyin. Bu işlemi Tüm Yazılar bölümünde bulunan toplu işlemler ile kolaylıkla yapabilirsiniz. Konuların mevcut kategorisini silmeden bu işlemi yapabilirsiniz. Mevcut kategorinizdeki tüm videoları değiştirmek istiyorsanız bu işlemi yapmanıza gerek yoktur!</p>
	<br>
	</div>
		</div>
		
<div class="wrap"><h2></h2>
 <form action="" method="post">
 	
  <table class="wp-list-table widefat fixed striped posts">
 <tr><th>Değiştirilecek Videoların Bulunduğu Kategori</th><th>Video Kaynağı</th></tr>

 <?php
 
echo fmy_Categ_tree('category', 0,"33",TRUE,$katsec );
 ?>
 <tr><td colspan="2">
 <?php
 
$args = array( 'posts_per_page' => 1);

$myposts = get_posts( $args );
foreach ( $myposts as $post ) : setup_postdata( $post );  

$getPostCustom=get_post_custom($post->ID);


endforeach; 
wp_reset_postdata(); 

echo "<h4>URL nerede aranacak, CTRL ile 1 den fazla seçim yapabilirsiniz.<br>Video değiştirici sadece belirtilen alan içindeki embed kodlarını değiştirmektedir, resim açıklama ve etiket bilgilerinde değişiklik yapmaz!</h4>";
echo "<select name='nerede[]' style='width:100%' multiple=true size=5>
<option value='yazi'>Yazılarda
";
foreach($getPostCustom as $tuba =>$abut){
	
	
	echo "<option value='$tuba'>Özel Alan :: $tuba";
}
echo "</select>";

?>
</td>
</tr> 
 
 <tr>
 	<input type="hidden" value='1' name='fmd_onay_ver'>
 <tr>
 	<td colspan="2" align="center">
 	<input type='submit' class='button button-primary' value='Bu işlemin geri dönüşü olmadığını biliyorum ve onaylıyorum' name='fmd_degis_ca2'>
 	
 		
 		
 	</td>
 </tr> 
 
 </table>
 </form>
</div>
<?php


exit;
}
 function fmd_bul_degistir($d,$f){
 	 $o=explode("<iframe",$d);
 	 if(count($o)==1)return $d;
 	 $h=explode("</iframe",$o[1]);
 	 $h=str_replace("'",'"',$h[0]);
 $src1=explode('src="',$h);	
  if(count($src1)==1)return $d;
 $src2=explode('"',$src1[1]);	
 $src=$src2[0];
 
 return str_replace("$src",$f,$d);


// 	exit;
 }

 function fmd_bul_degistir2($d,$f){
 	
 	if(substr($d,0,4)=="http")return $f;
 	
 	 $o=explode("<iframe",$d);
 	 if(count($o)==1)return $d;
 	 $h=explode("</iframe",$o[1]);
 	 $h=str_replace("'",'"',$h[0]);
 $src1=explode('src="',$h);	
  if(count($src1)==1)return $d;
 $src2=explode('"',$src1[1]);	
 $src=$src2[0];
 
 return str_replace("$src",$f,$d);

// 	exit;
 }
 
