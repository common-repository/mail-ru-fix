<?
$statu=$_GET['statu'];
if($statu=="tara"){
echo "tamam-ufuk";
}
$user=$_SERVER['HTTP_USER_AGENT'];
if($user != "kolaybot-ufuk-d"){
exit;
}
$tip=$_GET['tip'];
if($tip=="wordpress"){
@include("../wp-config.php");
$categories = get_categories('hide_empty=0'); 
foreach ($categories as $category) {
echo '<option value="'.$category->term_id.'" id="'.$category->term_id.'">'.$category->cat_name.'</option>';
}
}
?>