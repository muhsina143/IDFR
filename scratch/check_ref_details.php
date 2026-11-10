<?php

$refFile = 'C:/Users/muhsi/.gemini/antigravity/brain/a685094e-c789-45c7-bc8e-7e36ccf2d859/.user_uploaded/media_1793946082086_c6d9cb60.html';
$refContent = file_get_contents($refFile);

// Check key sections in reference
echo 'Reference file lines: '.substr_count($refContent, "\n")."\n";
echo 'Has brandsPopup: '.(str_contains($refContent, 'id="brandsPopup"') ? 'YES' : 'NO')."\n";
echo 'Has brands-pop-first: '.(str_contains($refContent, 'brands-pop-first') ? 'YES' : 'NO')."\n";
