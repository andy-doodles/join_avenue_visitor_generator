<?php

# http://localhost/join_avenue_visitor_generator/visitor_generator.php

require_once "C:/xampp/htdocs/join_avenue_visitor_generator/vendor/autoload.php";
include_once "sprite_arrays.php";
include_once "file_handling_functions.php";
include_once "visitor_data_functions.php";
$faker = Faker\Factory::create();
$sourceDirectory = "C:/xampp/htdocs/join_avenue_visitor_generator/Base Visitors/";


/*
- This script modifies an existing Join Avenue Visitor (.pjv) file from
the games Pokémon Black 2 and Pokémon White 2
- Each .pjv file represents a "visitor" that appears in the game's Join Avenue
feature as a passerby interested in engaging with the Avenue's shops
- The script chooses a random existing .pjv file from a source directory, then
copies and pastes its contents into a new file
- The script generates randomized data for the new file, including:
name, gender, country, subregion, greeting, farewell, shout, and sprite
- When all data is generated, the script opens the output file and injects all
the generated data into it
*/

for ($x = 1; $x <= 8; $x++) {
    # Get visitor gender and file name
    $visitorGender = generateVisitorGender();
    $newFileName = generateFileName($visitorGender, $faker);

    # Create and destructure country info
    $countryData = chooseCountry($countryList);
    $countryName = $countryData[0];
    $countryIndexDec = $countryData[1];
    $countryIndexHex = $countryData[2];
    $subRegionName = $countryData[3];
    $subRegionIndexDec = $countryData[4];
    $subRegionIndexHex = $countryData[5];

    # Create and destructure the visitor's greeting
    $visitorGreeting = generateVisitorDialogue($greetingsListEnglish, $stringTerminator, $nullCharacter);
    $unencodedGreeting = $visitorGreeting[0];
    $encodedGreeting = $visitorGreeting[1];
    # Create and destructure the visitor's farewell
    $visitorFarewell = generateVisitorDialogue($farewellListEnglish, $stringTerminator, $nullCharacter);
    $unencodedFarewell = $visitorFarewell[0];
    $encodedFarewell = $visitorFarewell[1];
    # Create and destructure the visitor's shout
    $visitorShout = generateVisitorDialogue($shoutsListEnglish, $stringTerminator, $nullCharacter);
    $unencodedShout = $visitorShout[0];
    $encodedShout = $visitorShout[1];

    # Get sprite number and description based on gender
    $spriteData = chooseSprite($maleSprites, $femaleSprites, $visitorGender);
    # Destructure sprite number (hex and decimal) and sprite description
    $hexSpriteValue = dechex($spriteData[0]);
    $decSpriteValue = $spriteData[0];
    $spriteDescription = $spriteData[1];

    /* Generate data about the day, month, and year the player met the visitor 
    Each piece of data corresponds to one byte in the binary file */
    $dateMet = $faker->dateTimeBetween("-12 years");
    $formattedDateMet = $dateMet->format("Y-m-d");
    $yearMet = $dateMet->format("y");
    $monthMet = $dateMet->format("m");
    $dayMet = $dateMet->format("d");

    # Generate a random amount of medals between 0 and 255
    $visitorMedals = rand(0, 255);

    /* Generate data about the number of Pokémon link trades the visitor has
    participated in. Number is between 1 and 2000
    This number will be converted to a signed integer (32-bit,
    little endian) during the process of writing it to the file */
    $visitorLinkTrades = getWeightedRandomNumber();

    /* All following variables will be converted to a signed integer
    (32-bit, little endian) during the process of writing them to the file */
    # Number of nicknames the visitors has given to caught Pokémon (1 - 1000)
    $visitorNicknamesGiven = getWeightedRandomNumber(100, 101, 250, 251, 500, 501, 750, 751, 950, 951, 1000);

    /* With all the necessary data generated, the script now writes said
    data into an output file */
    
    # Choose a random file from source directory
    $inputPath = $sourceDirectory . chooseRandomFile($sourceDirectory);
    # Establish the path to new, output file
    $outputPath = "C:/xampp/htdocs/join_avenue_visitor_generator/Output Visitors/" . $newFileName . ".pjv";

    # Open source file
    $inputVisitorFile = fopen($inputPath, "r+b");
    # Copy the content of the source file to the output file
    $copyFile = copy($inputPath, $outputPath);
    # Close source file
    fclose($inputVisitorFile);
    
    # Open output file for editing
    $outputVisitorFile = fopen($outputPath, "r+b");

    /* Inject all remaining generated data into the file:
    Gender, sprite, country and subregion, visitor name, shout, greeting, farewell
    and date met*/
    writeVisitorGenderToFile($outputVisitorFile, $visitorGender);
    writeVisitorSpriteToFile($outputVisitorFile, hexdec($hexSpriteValue));
    writeVisitorCountryToFile($outputVisitorFile, $countryIndexDec, $subRegionIndexDec);
    writeVisitorNameToFile($newFileName, $outputVisitorFile, $stringTerminator, $nullCharacter);
    writeVisitorShoutToFile($outputVisitorFile, $encodedShout);
    writeVisitorGreetingToFile($outputVisitorFile, $encodedGreeting);
    writeVisitorFarewellToFile($outputVisitorFile, $encodedFarewell);
    writeDateMetToFile($outputVisitorFile, $yearMet, $monthMet, $dayMet);
    writeNumberOfMedalsToFile($outputVisitorFile, $visitorMedals);
    writeSignedIntegersToFile($outputVisitorFile, $visitorLinkTrades, 0x40);
    writeSignedIntegersToFile($outputVisitorFile, $visitorNicknamesGiven, 0x44);

    # Output visitor data for verification and debugging
    echo "<pre>";
    print_r(getVisitorData($newFileName, $visitorGender, $hexSpriteValue, $decSpriteValue,
        $spriteDescription, $countryName, $countryIndexDec, $countryIndexHex, $subRegionName,
        $subRegionIndexDec, $subRegionIndexHex, $unencodedGreeting, $unencodedFarewell,
        $unencodedShout, $formattedDateMet, $visitorMedals, $visitorLinkTrades,
        $visitorNicknamesGiven));
    echo "<pre>";

    # Close file
    fclose($outputVisitorFile);
}

# http://localhost/join_avenue_visitor_generator/visitor_generator.php

?>