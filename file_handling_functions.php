<?php

include_once "visitor_generator.php";

function writeDataToFile($file, $offset, $data) {
    fseek($file, $offset);
    fwrite($file, $data);
    rewind($file);
}

# Select a random file from the source directory
function chooseRandomFile(
    string $directory
): string
{
    $directoryArray = scandir($directory);
    # Exclude unwanted directory elements and files
    $directoryArray = array_diff($directoryArray, [".", "..", "desktop.ini"]);
    # Randomize the order of array elements
    shuffle($directoryArray);
    return $directoryArray[0];
}

/* Generate a 1 to 6-character name for the visitor and its corresponding file
Gender of name depends on gender created with `generateVisitorGender()` */
function generateFileName(
    string $visitorGender,
    $faker
): string
{
    do {
        if ($visitorGender == "man or boy") {
            $fileName = $faker->firstNameMale();
        } else {
            $fileName = $faker->firstNameFemale();
        }
    } while (strlen($fileName) > 6);

    return $fileName;
}

# Inject encoded visitor name to file
function writeVisitorNameToFile($newFileName, $file, $terminator, $nullCharacter) {
    $nameLength = strlen($newFileName);
    $encodedVisitorName = encodeStringAddFiller($newFileName, $nameLength,
        $terminator, $nullCharacter, false, true);
    writeDataToFile($file, 0x00, $encodedVisitorName);
}

/* Inject visitor gender to file
Gender byte is at offset 0x22
A value of 0x00 is "man", 0x10 is "woman" */
function writeVisitorGenderToFile(
    $file,
    string $gender
)
{
    if ($gender == "man or boy") {
        $gender = 0x00;
    }
    else {
        $gender = 0x10;
    }
    
    writeDataToFile($file, 0x22, $gender);
}

/* Inject the value sprite into the file
Sprite offset is at 0x2A
*/
function writeVisitorSpriteToFile(
    $file,
    int $hexSpriteValue
)
{
    $hexSpriteValue = chr($hexSpriteValue);
    writeDataToFile($file, 0x2A, $hexSpriteValue);
}

/*
Visitor country at offset 0x0E
Visitor subregion at offset 0x0F
*/
function writeVisitorCountryToFile($file, $country, $subRegion) {
    $country = chr($country);
    $subRegion = chr($subRegion);
    writeDataToFile($file, 0x0E, $country);
    writeDataToFile($file, 0x0F, $subRegion);
}

# Visitor shout is at offset 0x10
function writeVisitorShoutToFile($file, $greeting) {
    writeDataToFile($file, 0x10, $greeting);
}

# Visitor greeting is at offset 0x80
function writeVisitorGreetingToFile($file, $greeting) {
    writeDataToFile($file, 0x80, $greeting);
}

# Visitor farewell is at offset 0x90
function writeVisitorFarewellToFile($file, $greeting) {
    writeDataToFile($file, 0x90, $greeting);
}

/* Writes data about the day, month, and year the player met the visitor 
Year met is at offset 0xA3
Month met is at offset 0xA4
Day met is at offset 0xA5
*/
function writeDateMetToFile(
    $file,
    int $yearMet,
    int $monthMet,
    int $dayMet
)
{
    $yearMet = chr($yearMet);
    $monthMet = chr($monthMet);
    $dayMet = chr($dayMet);
    writeDataToFile($file, 0xA3, $yearMet);
    writeDataToFile($file, 0xA4, $monthMet);
    writeDataToFile($file, 0xA5, $dayMet);
}

/* Writes the number of medals the visitor has
Number of medals is at offset 0x39
*/
function writeNumberOfMedalsToFile(
    $file,
    int $visitorMedals
)
{
    $visitorMedals = chr($visitorMedals);
    writeDataToFile($file, 0x39, $visitorMedals);
}

/* Writes to file any number that needs to be a signed integer in the binary file
The provided integer is converted to a 32-bit, little endian signed integer
Function requires hexadecimal offset to specify where to write the data
*/
function writeSignedIntegersToFile(
    $file,
    int $integer,
    int $offset
)
{
    $signedInteger = pack("l", $integer);
    writeDataToFile($file, $offset, $signedInteger);
}

/* Write the visitor's recruitment rank (0 - 255)
Offset is at 0x2C
*/
function writeRecruitmentRankToFile(
    $file,
    int $recruitmentRank,
    int $offset
)
{
    $recruitmentRank = chr($recruitmentRank);
    writeDataToFile($file, $offset, $recruitmentRank);
}

/* Write the visitor's choice of shop
Offset is at 0x2E
*/
function writeShopChoiceToFile(
    $file,
    int $shopChoice,
    int $offset
)
{
    $shopChoice = chr($shopChoice);
    writeDataToFile($file, $offset, $shopChoice);
}

/* Write the visitor's Join Avenue rank in their own Join Avenue
Offset is at 0xAB
*/
function writeJoinAvenueRank(
    $file,
    int $visitorJoinAvenueRank,
    int $offset
)
{
    $visitorJoinAvenueRank = chr($visitorJoinAvenueRank);
    writeDataToFile($file, $offset, $visitorJoinAvenueRank);
}

?>