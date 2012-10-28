<?php

/*
Plugin Name: MakaleDepo Senkronizasyon Sihirbaz&#305;
Plugin URI: http://makaledepo.com/plugin/
Description: MakaleDepo ile sitenizin senkronizasyonunu sa&#287;lar.
Version: 1.0
License: GPLv2 or later
*/

add_action('admin_menu', 'att_add_options');

function att_add_options() {
	add_options_page('MakaleDepo Tercihleri', 'MakaleDepo Tercihleri', 8, 'makaledepo', 'att_options_page');	
}

function add_settings_link($links, $file) {
static $this_plugin;
if (!$this_plugin) $this_plugin = plugin_basename(__FILE__);
 
if ($file == $this_plugin){
$settings_link = '<a href="./options-general.php?page=makaledepo">'.__("Ayarlar", "makaledepo").'</a>';
 array_unshift($links, $settings_link);
}
return $links;
 }
 
add_filter('plugin_action_links', 'add_settings_link', 10, 2 );
 

function att_options_page() {

$con= file_get_contents("http://api.makaledepo.net/api?public_key=" . get_option('public_key') . "&private_key=" . get_option('private_key') . "&process=getmember");

$id = explode('<id>', $con);
$id = explode('</id>', $id[1]);

$error = explode('<error>', $con);
$error = explode('</error>', $error[1]);

$email = explode('<email>', $con);
$email = explode('</email>', $email[1]);

$username = explode('<username>', $con);
$username = explode('</username>', $username[1]);

$name = explode('<name>', $con);
$name = explode('</name>', $name[1]);

$lastname = explode('<lastname>', $con);
$lastname = explode('</lastname>', $lastname[1]);

$nameandlastname = $name[0].' '.$lastname[0];

$not_used_api = explode('<not_used_api>', $con);
$not_used_api = explode('</not_used_api>', $not_used_api[1]);

?>

		<div class="wrap">
			<?php    echo "<h2>" . __( 'MakaleDepo Senkronizasyonu', 'oscimp_trdom' ) . "</h2>"; ?>
            
			<form name="makaledepo" method="post" action="<?php echo str_replace( '%7E', '~', $_SERVER['REQUEST_URI']); ?>">
				<input type="hidden" name="makaledepo_hidden" value="Y">
				<?php    echo "<h4>" . __( 'API Erişim Bilgileri', 'oscimp_trdom' ) . "</h4>"; ?>
				<p><?php _e("Public (Genel) Anahtarı: " ); ?><input type="text" name="public_key" value="<?php echo get_option('public_key'); ?>" size="100"></p>
				<p><?php _e("Private (Özel) Anahtarı: " ); ?><input type="text" name="private_key" value="<?php echo get_option('private_key'); ?>" size="100"></p>
				<p class="submit">
				<input type="submit" name="Submit" value="<?php _e('Bilgileri Güncelle', 'oscimp_trdom' ) ?>" />
				</p>
            </form>
                
            <?php  if(!($id[0] == "")) { ?> 
            
            <form name="guncelle" method="post" action="<?php echo str_replace( '%7E', '~', $_SERVER['REQUEST_URI']); ?>">
			<input type="hidden" name="guncelle" value="Y">
            <p class="submit">
			<input type="submit" name="Submit" value="<?php _e('Senkronize Et', 'oscimp_trdom' ) ?>" />
			</p>
            </form>
            
            <?php    echo "<font color='darkblue'><h4>" . __( 'Hesap Bilgileri', 'oscimp_trdom' ) . "</h4></font>"; ?>
            
            <p><strong><i><?php _e("Ad / Soyad: " ); ?></strong></i><? echo $nameandlastname; ?></p>
            <p><strong><i><?php _e("E-Posta Adresi: " ); ?></strong></i><? echo $email[0]; ?></p>
            <p><strong><i><?php _e("Kullanıcı Adı: " ); ?></strong></i><? echo $username[0]; ?></p>
            
            <p><i>Hesabınız ile bağlantı başarıyla sağlanmıştır.</i></p>
            
            <?php 
            
            $con2= file_get_contents("http://api.makaledepo.net/api?public_key=" . get_option('public_key') . "&private_key=" . get_option('private_key') . "&process=getarticles");
 
            $articleid = explode('<article id="', $con2);
            $articleid = explode('">', $articleid[1]);
            $articleid = $articleid[0];
            
            $subject = explode('<subject>', $con2);
            $subject = explode('</subject>', $subject[1]);
            $subject = $subject[0];
            
            $keywords = explode('<keywords>', $con2);
            $keywords = explode('</keywords>', $keywords[1]);
            $keywords = $keywords[0];
            
            $keywords = explode('<keywords>', $con2);
            $keywords = explode('</keywords>', $keywords[1]);
            $keywords = $keywords[0];
            
            # Makale Çekiliyor
            
            $con3 = file_get_contents("http://api.makaledepo.net/txt?public_key=" . get_option('public_key') . "&private_key=" . get_option('private_key') . "&process=readarticle&article_id=" . $articleid . "&member_id=".$id[0]);
            
            $text = iconv('ISO-8859-9', 'UTF-8', $con3);
            
            if(!($articleid == "")) {
            
            # Wordpress e Makale Ekleniyor
            
            $new_post = array(
            'post_title'    => $subject,
            'post_content'  => $text,
            'post_status'   => 'pending',
            'tags_input'    => $keywords,
            'post_author'   => 1);

            wp_insert_post( $new_post );    
                
                
            }
            
            ?>
            
            <? } else { ?> <font color="red">Bağlantı Sağlanamadı.</font> <? } ?> 
			
		</div>
	

<? } ?>

<?php

if($_POST['guncelle'] == 'Y') {

$page = $_SERVER['PHP_SELF'];
$sec = $not_used_api;


?>
<meta http-equiv="refresh" content="0; url=<?php echo str_replace( '%7E', '~', $_SERVER['REQUEST_URI']); ?>" /> 
<?   
    
}

?>

<?php
		
if($_POST['makaledepo_hidden'] == 'Y') {
 
if(!($_POST['public_key'] == "" or $_POST['private_key'] == "")) {  
  
  $public_key = $_POST['public_key'];  
  update_option('public_key', $public_key);  
  $private_key = $_POST['private_key'];  
  update_option('private_key', $private_key);

  
?>
   
<div class="updated"><p><strong><?php _e('Ayarlar başarıyla güncellendi.' ); ?></strong></p></div>  		

<?        
} else { 
?>

<div class="error"><p><strong><?php _e('Lütfen boş alan bırakmayınız.' ); ?></strong></p></div>  		
	
<? } } ?>    