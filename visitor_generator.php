<?php

# http://localhost/join_avenue_visitor_generator/visitor_generator.php

require_once "C:/xampp/htdocs/join_avenue_visitor_generator/libraries/vendor/autoload.php";
include "sprite_arrays.php";
include "file_handling_functions.php";
include "visitor_data_functions.php";
$faker = Faker\Factory::create();
$sourceDirectory = "C:/xampp/htdocs/join_avenue_visitor_generator/Base Visitors/";

for ($x = 1; $x <= 8; $x++) {
    $visitorGender = generateVisitorGender();
    $newFileName = generateFileName($visitorGender);

    # Choose a random file from source directory
    $inputPath = $sourceDirectory . chooseRandomFile($sourceDirectory);
    # Path to new file
    $outputPath = "C:/xampp/htdocs/join_avenue_visitor_generator/Output Visitors/" . $newFileName . ".pjv";

    # Open source file, copy it, close original
    $inputVisitorFile = fopen($inputPath, "r+b");
    $copyFile = copy($inputPath, $outputPath);
    fclose($inputVisitorFile);

    # Modify visitor's name
    $outputVisitorFile = fopen($outputPath, "r+b");
    writeVisitorGenderToFile($outputVisitorFile, $visitorGender);
    $spriteData = writeVisitorSpriteToFile($outputVisitorFile, $visitorGender);
    writeVisitorNameToFile($outputVisitorFile);

    echo "<pre>";
    print_r(getVisitorData($newFileName, $visitorGender, $spriteData));
    echo "<pre>";

    fclose($outputVisitorFile);
}

# http://localhost/join_avenue_visitor_generator/visitor_generator.php

?>