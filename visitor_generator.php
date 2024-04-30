<?php

# http://localhost/join_avenue_visitor_generator/visitor_generator.php

require_once "C:/xampp/htdocs/join_avenue_visitor_generator/libraries/vendor/autoload.php";
include_once "sprite_arrays.php";
include_once "file_handling_functions.php";
include_once "visitor_data_functions.php";
$faker = Faker\Factory::create();
$sourceDirectory = "C:/xampp/htdocs/join_avenue_visitor_generator/Base Visitors/";

for ($x = 1; $x <= 8; $x++) {
    /* Get visitor gender, country info, file name, and greetings
    */
    $visitorGender = generateVisitorGender();
    [$countryName, $countryIndexDec, $countryIndexHex, $subRegionName,
        $subRegionIndexDec, $subRegionIndexHex] = chooseCountry($countryList);
    $newFileName = generateFileName($visitorGender);
    [$unencodedGreeting, $encodedGreeting] = 
        generateEnglishGreeting($greetingsListEnglish, $stringTerminator, $nullCharacter);

    # Choose a random file from source directory
    $inputPath = $sourceDirectory . chooseRandomFile($sourceDirectory);
    # Path to new file
    $outputPath = "C:/xampp/htdocs/join_avenue_visitor_generator/Output Visitors/" . $newFileName . ".pjv";

    # Open source file, copy it, close original
    $inputVisitorFile = fopen($inputPath, "r+b");
    $copyFile = copy($inputPath, $outputPath);
    fclose($inputVisitorFile);

    # Write visitor's name, gender, country, and greeting to file
    $outputVisitorFile = fopen($outputPath, "r+b");
    writeVisitorGenderToFile($outputVisitorFile, $visitorGender);
    $spriteData = writeVisitorSpriteToFile($outputVisitorFile, $visitorGender);
    writeVisitorCountryToFile($outputVisitorFile, $countryIndexDec, $subRegionIndexDec);
    writeVisitorNameToFile($outputVisitorFile);
    writeVisitorGreetingToFile($outputVisitorFile, $encodedGreeting);

    # Output visitor data for verification and debugging
    echo "<pre>";
    print_r(getVisitorData($newFileName, $visitorGender, $spriteData, $countryName,
        $countryIndexDec, $countryIndexHex, $subRegionName,
        $subRegionIndexDec, $subRegionIndexHex, $unencodedGreeting));
    echo "<pre>";

    fclose($outputVisitorFile);
}

# http://localhost/join_avenue_visitor_generator/visitor_generator.php

?>