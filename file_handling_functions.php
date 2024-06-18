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

/* Encode visitor gender created with generateVisitorGender()
Inject encoded visitor name to file
Gender byte is at offset 0x22
A value of 0x00 is "man", 0x10 is "woman" */
function writeVisitorGenderToFile(
    $file,
    string $gender
)
{
    if ($gender == "man or boy") {
        $gender = pack("v", 0x00);
    }
    else {
        $gender = pack("v", 0x10);
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

?>