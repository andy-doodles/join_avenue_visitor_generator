<?php

# http://localhost/join_avenue_visitor_generator/visitor_generator.php

require_once "C:/xampp/htdocs/join_avenue_visitor_generator/libraries/vendor/autoload.php";
include_once "sprite_arrays.php";
include_once "file_handling_functions.php";
include_once "visitor_data_functions.php";
$faker = Faker\Factory::create();
$sourceDirectory = "C:/xampp/htdocs/join_avenue_visitor_generator/Base Visitors/";

for ($x = 1; $x <= 8; $x++) {
    # Get visitor gender and file name
    $visitorGender = generateVisitorGender();
    $newFileName = generateFileName($visitorGender);
    # Destructure country info
    [$countryName, $countryIndexDec, $countryIndexHex, $subRegionName,
        $subRegionIndexDec, $subRegionIndexHex] = chooseCountry($countryList);

    # Destructure the visitor's greeting
    [$unencodedGreeting, $encodedGreeting] = 
        generateVisitorDialogue($greetingsListEnglish, $stringTerminator, $nullCharacter);
    # Destructure the visitor's farewell
    [$unencodedFarewell, $encodedFarewell] = 
        generateVisitorDialogue($farewellListEnglish, $stringTerminator, $nullCharacter);
    # Destructure the visitor's shout
    [$unencodedShout, $encodedShout] = 
        generateVisitorDialogue($shoutsListEnglish, $stringTerminator, $nullCharacter);

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
    
    # Get sprite number and description based on gender
    if ($visitorGender == "man or boy") {
        $spriteData = getValueFromRandomKey($maleSprites);
    } else {
        $spriteData = getValueFromRandomKey($femaleSprites);
    }
    # Destructure sprite number (hex and decimal) and sprite description
    $hexSpriteValue = dechex($spriteData[0]);
    $decSpriteValue = $spriteData[0];
    $spriteDescription = $spriteData[1];

    # Inject all generated into the file
    writeVisitorSpriteToFile($outputVisitorFile, $hexSpriteValue);
    writeVisitorCountryToFile($outputVisitorFile, $countryIndexDec, $subRegionIndexDec);
    writeVisitorNameToFile($outputVisitorFile);
    writeVisitorShoutToFile($outputVisitorFile, $encodedShout);
    writeVisitorGreetingToFile($outputVisitorFile, $encodedGreeting);
    writeVisitorFarewellToFile($outputVisitorFile, $encodedFarewell);

    # Output visitor data for verification and debugging
    echo "<pre>";
    print_r(getVisitorData($newFileName, $visitorGender, $hexSpriteValue, $decSpriteValue,
        $spriteDescription, $countryName, $countryIndexDec, $countryIndexHex, $subRegionName,
        $subRegionIndexDec, $subRegionIndexHex, $unencodedGreeting, $unencodedFarewell,
        $unencodedShout));
    echo "<pre>";

    fclose($outputVisitorFile);
}

# http://localhost/join_avenue_visitor_generator/visitor_generator.php

?>