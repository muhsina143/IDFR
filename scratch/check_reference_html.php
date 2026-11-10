<?php

// Compare the uploaded asdreams.html (stored in artifact or read directly) with home.blade.php
$htmlPath = 'C:/Users/muhsi/.gemini/antigravity/brain/a685094e-c789-45c7-bc8e-7e36ccf2d859/.user_uploaded/media_1793946082086_c6d9cb60.html';
if (! file_exists($htmlPath)) {
    // Check all uploaded html files
    $files = glob('C:/Users/muhsi/.gemini/antigravity/brain/a685094e-c789-45c7-bc8e-7e36ccf2d859/.user_uploaded/*.html');
    echo "Found files:\n";
    print_r($files);
} else {
    echo "Master HTML exists at $htmlPath\n";
    echo 'Size: '.filesize($htmlPath)."\n";
}
