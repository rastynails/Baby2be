<?php
require_once 'securimage.php';

$img = new securimage();

//Change some settings
$img->perturbation = 0.45;
$img->image_bg_color = new Securimage_Color(0xf6, 0xf6, 0xf6);
$img->text_angle_minimum = -5;
$img->text_angle_maximum = 5;
$img->use_transparent_text = true;
$img->text_transparency_percentage = 30; // 100 = completely transparent
$img->num_lines = 7;
$img->line_color = new Securimage_Color("#7B92AA");
$img->signature_color = new Securimage_Color("#7B92AA");
$img->text_color = new Securimage_Color("#7B92AA");
$img->use_wordlist = true;

$img->show();
exit;
