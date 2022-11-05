<?php 

require './vendor/autoload.php';

use GDText\Box;
use GDText\Color;

$company = $_GET['company'];
$category = $_GET['category'];
$question = trim($_GET['question']);
$sm_network = $_GET['sm_network'];
$custom_template_user_id = $_GET['custom_template_user_id'];
$bg_color = ! empty($_GET['bg_color']) ? $_GET['bg_color'] : '#FFFFFF';
$text_color = ! empty($_GET['text_color']) ? $_GET['text_color'] : '#000000';
$font_family = ($_GET['font_family']) ? '/'.urldecode($_GET['font_family']) : '/fonts/ARIAL.TTF' ;

/* Fonts */
$font_bold =  '/home/askthepros/imagecreator.askthepros.com/fonts/Montserrat-Bold-700.ttf';
$font_m =  '/fonts/ARIAL.TTF';
$post_id = 1;

create_member_post_image( $post_id, $company, $question, $category, $bg_color, $text_color, $font_family, $custom_template_user_id, $sm_network );

function create_member_post_image(
	$post_id = 1,
	$company,
	$question,
	$category,
	$bg_color = '#FFFFFF',
	$text_color = '#000000',
	$font_family = '/fonts/ARIAL.TTF',
	$custom_template_user_id,
	$sm_network
) {

	header("Content-Type: image/png"); 

	list($bg_r, $bg_g, $bg_b) = sscanf($bg_color, "#%02x%02x%02x");
	list($txt_r, $txt_g, $txt_b) = sscanf($text_color, "#%02x%02x%02x");

	if ($sm_network == "LinkedIN" || $sm_network == "Facebook") {
		$width = 1200;
		$height = 627;

		$image = imagecreatetruecolor($width, $height);
		$background_color = imagecolorallocate($image, $bg_r, $bg_g, $bg_b);  // Blue
		$text_color = imagecolorallocate($image, $txt_r, $txt_g, $txt_b); // White 
		imagefill($image, 0, 0, $background_color);

		$x = horizontal_align($image, 42, $font_family, $company);

		// Company
		imagettftext($image, 42, 0, $x, ($height-(100-42)),  $text_color,  $font_family, $company);

		// Question
		$image = drawCustomTextBox(
			$image,
			$font_family,
			$txt_r,
			$txt_g,
			$txt_b,
			$question,
			array(
				'text_h' => 1300,
				'left'	=> 125,
				'right'	=> 125,
				'width' => 950,
				'height'=> 380
			)
		);

		$x = horizontal_align($image, 42, $font_family, $category);

		// Category
		imagettftext($image, 42, 0, $x, 100,  $text_color,  $font_family, $category);
	}  else if ($sm_network == "Google") {
		$width = 720;
		$height = 720;

		$image = imagecreatetruecolor($width, $height);
		$background_color = imagecolorallocate($image, $bg_r, $bg_g, $bg_b);  // Blue
		$text_color = imagecolorallocate($image, $txt_r, $txt_g, $txt_b); // White 
		imagefill($image, 0, 0, $background_color);

		// Company
		$company_font_size = 40;
		$x = horizontal_align($image, $company_font_size, $font_family, $company);
		imagettftext($image, $company_font_size, 0, $x, ($height-(100-$company_font_size)),  $text_color,  $font_family, $company);

		// Question
		$image = drawCustomTextBox(
			$image,
			$font_family,
			$txt_r,
			$txt_g,
			$txt_b,
			$question,
			array(
				'text_h' => 1000,
				'left'	=> 100,
				'right'	=> 100,
				'width' => 520,
				'height'=> 500
			)
		);

		// Category
		$x = horizontal_align($image, 40, $font_family, $category);
		imagettftext($image, $company_font_size, 0, $x, 100,  $text_color,  $font_family, $category);
	}

	if (!empty($custom_template_user_id) && file_exists('images/custom/' . $custom_template_user_id . '.png')) {
		$userImage = create_custom_template('images/custom/' . $custom_template_user_id . '.png', $image, 400, 47, 0, 0, 681, 483, $width, $height);
		
		$filename = $post_id . '_' . $custom_template_user_id . '_' . $sm_network .  '_custom.png'; //standard_
		
		header('Content-Disposition: inline; filename="' . $custom_template_user_id . '_custom_template.png"');	
	} else if( ! empty($custom_template_user_id) ) {
		$userImage = $image;
		$filename = $post_id . '_' . $custom_template_user_id . '_' . $sm_network .  '_standard.png';
		header('Content-Disposition: inline; filename="' . $filename . '"');
	} else {
		$userImage = $image;
		$filename = $post_id . '_' . $sm_network .  '_standard.png';
		header('Content-Disposition: inline; filename="' . $filename . '"');
	}
	

	/**
	 * Printing the image in the browser
	 */
	imagepng($userImage);
	imagedestroy($userImage);
}

/**
 * Custom function started from here.
 */

function create_custom_template($user_image, $image, $dst_x, $dst_y, $src_x, $src_y, $dst_width, $dst_height, $width, $height)
{
	$userImage      =  LoadImage($user_image);
	imagecopyresampled($userImage, $image, $dst_x, $dst_y, $src_x, $src_y, $dst_width, $dst_height, $width, $height);
	return $userImage;
}

/**
 * To load the images from the gived directory
 * @param image name
 * @return image if found match else @return false
 */
function LoadImage($name)
{
	$k = exif_imagetype($name);
	if ($k == IMAGETYPE_JPEG)
		return imagecreatefromjpeg($name);
	if ($k == IMAGETYPE_PNG)
		return imagecreatefrompng($name);
	if ($k == IMAGETYPE_GIF)
		return imagecreatefromgif($name);
	if ($k == IMAGETYPE_BMP)
		return imagecreatefromwbmp($name);
	return false;
}

/**
 * Align text horizontally according to the image and text site.
 * @return int x coordinate
 * @param image, size, font_bold, text 
 **/
function horizontal_align(&$image, $size, $font_bold, $text)
{

	// Get image Width and Height
	$image_width = imagesx($image);
	// $image_height = imagesy($image);

	// Get Bounding Box Size
	$text_box = imagettfbbox($size, 0, $font_bold, $text);

	// Get your Text Width and Height
	$text_width = $text_box[2] - $text_box[0];
	//$text_height = $text_box[7]-$text_box[1];

	// Calculate coordinates of the text
	return ($image_width / 2) - ($text_width / 2);
}

function drawCustomTextBox($image, $font_family, $txt_r, $txt_g, $txt_b, $question, $options)
{
	$box = new Box($image);
	$box->setFontFace($font_family); // http://www.dafont.com/minecraftia.font
	$box->setFontColor(new Color($txt_r, $txt_g, $txt_b));
	$box->setTextShadow(new Color(0, 0, 0, 50), 2, 2);

	$font_size = 70;

	extract($options);

	do {
		$font_size--;
		$box->setFontSize($font_size);
	} while ($box->test(trim($question)) >= $text_h);

	$box->setLineHeight(1.5);
	//$box->enableDebug();
	$box->setBox($left, $right, $width, $height);
	$box->setTextAlign('left', 'center');
	$box->draw(trim($question));
	return $image;
}