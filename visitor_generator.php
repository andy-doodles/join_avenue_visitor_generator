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
    # Get visitor gender
    $genderArray = ["man", "woman"];
    $visitorGender = generateVisitorGender($genderArray);
    # Generate file name based on gender
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

    # Generate the visitor's recruitment level
    $visitorRecruitmentRank = getWeightedRandomNumber(20, 21, 25, 26, 35, 36, 50, 51, 200, 201, 255);
    
    # Generate the visitor's shop choices (the shops they want to engage with)
    $visitorShopChoice = rand(0, 255);

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
    # Number of customers in the visitor's own Join Avenue (1 - 1000)
    $visitorCustomers = getWeightedRandomNumber(100, 101, 250, 251, 500, 501, 750, 751, 950, 951, 1000);
    # Total money spent by the visitor
    $visitorMoneySpent = getWeightedRandomNumber(2e5, 2e5 + 1, 5e5, 5e5 + 1, 7e5, 7e5 + 1, 1e6, 1e6 + 1, 2e6,
                                                2e6 + 1, 1e7);
    # Number of passersby met by the visitor
    $visitorPassersbyMet = getWeightedRandomNumber();
    # Number of Link Battles the visitor has participated in
    $visitorLinkBattles = getWeightedRandomNumber(100, 101, 250, 251, 500, 501, 750, 751, 950, 951, 1000);
    # Number of Pokémon the visitor has caught
    $visitorPokemonCaught = getWeightedRandomNumber(600, 601, 700, 701, 800, 801, 900, 901, 1500, 1501, 3000);
    # Number of Pokémon eggs the visitor has hatched
    $visitorPokemonEggsHatched = getWeightedRandomNumber(100, 101, 250, 251, 500, 501, 750, 751, 950, 951, 1000);

    # Visitor's Join Avenue rank (in their own Join Avenue, not in one of your shops)
    $visitorJoinAvenueRank = getWeightedRandomNumber(20, 21, 25, 26, 35, 36, 50, 51, 200, 201, 255);

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
    writeVisitorGenderToFile($outputVisitorFile, 0x22, $visitorGender);
    writeVisitorSpriteToFile($outputVisitorFile, 0x2A, hexdec($hexSpriteValue));
    writeRecruitmentRankToFile($outputVisitorFile, 0x2C, $visitorRecruitmentRank);
    writeShopChoiceToFile($outputVisitorFile, 0x2E, $visitorShopChoice);
    writeVisitorCountryToFile($outputVisitorFile, 0x0E, 0x0F, $countryIndexDec, $subRegionIndexDec);
    writeVisitorNameToFile($outputVisitorFile, 0x00, $newFileName, $stringTerminator, $nullCharacter);
    # Inject all dialogues
    writeDataToFile($outputVisitorFile, 0x10, $encodedShout);
    writeDataToFile($outputVisitorFile, 0x80, $encodedGreeting);
    writeDataToFile($outputVisitorFile, 0x90, $encodedFarewell);
    # Make every visitor a human player
    writeDataToFile($outputVisitorFile, 0xA0, 0x01);
    writeDateMetToFile($outputVisitorFile, 0xA3, $yearMet, 0xA4, $monthMet, 0xA5, $dayMet);
    writeNumberOfMedalsToFile($outputVisitorFile, 0x39, $visitorMedals);
    writeSignedIntegersToFile($outputVisitorFile, 0x40, $visitorLinkTrades);
    writeSignedIntegersToFile($outputVisitorFile, 0x44, $visitorNicknamesGiven);
    writeSignedIntegersToFile($outputVisitorFile, 0x48, $visitorCustomers);
    writeSignedIntegersToFile($outputVisitorFile, 0x4C, $visitorMoneySpent);
    writeSignedIntegersToFile($outputVisitorFile, 0x50, $visitorPassersbyMet);
    writeSignedIntegersToFile($outputVisitorFile, 0x54, $visitorLinkBattles);
    writeSignedIntegersToFile($outputVisitorFile, 0x58, $visitorPokemonCaught);
    writeSignedIntegersToFile($outputVisitorFile, 0x5C, $visitorPokemonEggsHatched);
    writeJoinAvenueRank($outputVisitorFile, 0xAB, $visitorJoinAvenueRank);

    # Output visitor data for verification and debugging
    echo "<pre>";
    print_r(getVisitorData($newFileName, $visitorGender, $hexSpriteValue, $decSpriteValue,
        $spriteDescription, $visitorRecruitmentRank, $visitorShopChoice, $countryName, $countryIndexDec,
        $countryIndexHex, $subRegionName, $subRegionIndexDec, $subRegionIndexHex, $unencodedGreeting,
        $unencodedFarewell, $unencodedShout, $formattedDateMet, $visitorMedals, $visitorLinkTrades,
        $visitorNicknamesGiven, $visitorCustomers, $visitorMoneySpent, $visitorPassersbyMet,
        $visitorLinkBattles, $visitorPokemonCaught, $visitorPokemonEggsHatched, $visitorJoinAvenueRank));
    echo "<pre>";

    # Close file
    fclose($outputVisitorFile);
}

# http://localhost/join_avenue_visitor_generator/visitor_generator.php
