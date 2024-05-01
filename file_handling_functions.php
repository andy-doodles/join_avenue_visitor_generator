<?php

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

/* Generate a 6-character name for the visitor and its corresponding file
Gender of name depends on gender created with `generateVisitorGender()` */
function generateFileName(
    string $visitorGender
): string
{
    global $faker;
    $fileName = "";

    if ($visitorGender == "man or boy") {
        do {
        $fileName = $faker->firstNameMale;
        } while (strlen($fileName) != 6);
    }
    else {
        do {
            $fileName = $faker->firstNameFemale;
        } while (strlen($fileName) != 6);
    }

    return $fileName;
}

# Inject encoded visitor name to file
function writeVisitorNameToFile($file) {
    global $newFileName;
    $visitorName = assignVisitorName($newFileName);
    writeDataToFile($file, 0x00, $visitorName);
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
    string $hexSpriteValue
)
{
    $encodedHexSpriteValue = pack("v", $hexSpriteValue);
    writeDataToFile($file, 0x2A, $encodedHexSpriteValue);
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

# Visitor greeting is at offset 0x80
function writeVisitorGreetingToFile($file, $greeting) {
    writeDataToFile($file, 0x80, $greeting);
}

?>