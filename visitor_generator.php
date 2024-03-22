<?php

# http://localhost/join_avenue_visitor_generator/visitor_generator.php

# Create a random, 6-character name for the visitor and its corresponding file
function assignRandomFileName() { 
    $characterSet = "ABCDEFGHIJKLMNOPQRSTUVWXYZabcdefghijklmnopqrstuvwxyz0123456789";
    $randomName = "";
    for ($i = 0; $i < 6; $i++) {
        # Generate a random character from the set
        $newLetter = $characterSet[rand(0, strlen($characterSet) - 1)];
        $randomName .= $newLetter;
    }
    return $randomName;
}

function assignVisitorName($randomString) {
    # Encode string and append terminator
    $terminator = pack("v", 0xFFFF);
    $twoByteName = mb_convert_encoding($randomString, "UTF-16LE");
    $twoByteName .= $terminator;
    return $twoByteName;
}

# Create random name for the visitor and corresponding output file
$newFileName = assignRandomFileName();

function writeVisitorNameToFile($file) {
    global $newFileName;
    $visitorName = assignVisitorName($newFileName);
    fwrite($file, $visitorName);
}

$inputPath = "C:/xampp/htdocs/join_avenue_visitor_generator/Base Visitors/VISITOR _ Jane.pjv";
$outputPath = "C:/xampp/htdocs/join_avenue_visitor_generator/Output Visitors/" . $newFileName . ".pjv";

# Open visitor file, copy it, close original
$inputVisitorFile = fopen($inputPath, "r+b");
$copyFile = copy($inputPath, $outputPath);
fclose($inputVisitorFile);

# Open copy, get its file size
$outputVisitorFile = fopen($outputPath, "r+b");
$visitorSize = filesize($outputPath);

writeVisitorNameToFile($outputVisitorFile);

fclose($outputVisitorFile);

# http://localhost/join_avenue_visitor_generator/visitor_generator.php

?>