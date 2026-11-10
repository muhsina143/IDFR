<?php

$refFile = 'C:/Users/muhsi/.gemini/antigravity/brain/a685094e-c789-45c7-bc8e-7e36ccf2d859/.user_uploaded/media_1793946082086_c6d9cb60.html';
$refContent = file_get_contents($refFile);

// Let's inspect sections in refContent
preg_match_all('/<!-- ============ (.*?) ============ -->/', $refContent, $matches);
echo "Sections in reference HTML:\n";
print_r($matches[1]);
