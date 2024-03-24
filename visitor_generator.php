<?php

# http://localhost/join_avenue_visitor_generator/visitor_generator.php

$sourceDirectory = "C:/xampp/htdocs/join_avenue_visitor_generator/Base Visitors/";

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

function writeVisitorNameToFile($file) {
    global $newFileName;
    $visitorName = assignVisitorName($newFileName);
    fwrite($file, $visitorName);
}

# Select a random file from the source directory
function chooseRandomFile($directory) {
    $directoryArray = scandir($directory);
    # Exclude unwanted directory elements and files
    $directoryArray = array_diff($directoryArray, [".", "..", "desktop.ini"]);
    # Randomize the order or array elements 
    shuffle($directoryArray);

    return $directoryArray[0];
}

for ($x = 1; $x <= 8; $x++) {
    # Create random name for the visitor and corresponding output file
    $newFileName = assignRandomFileName();
    
    $inputPath = $sourceDirectory . chooseRandomFile($sourceDirectory);
    $outputPath = "C:/xampp/htdocs/join_avenue_visitor_generator/Output Visitors/" . $newFileName . ".pjv";

    # Open visitor file, copy it, close original
    $inputVisitorFile = fopen($inputPath, "r+b");
    $copyFile = copy($inputPath, $outputPath);
    fclose($inputVisitorFile);

    # Modify visitor's name
    $outputVisitorFile = fopen($outputPath, "r+b");
    writeVisitorNameToFile($outputVisitorFile);
    fclose($outputVisitorFile);
}

# http://localhost/join_avenue_visitor_generator/visitor_generator.php

?>