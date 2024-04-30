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

# Inject encoded visitor name to file
function writeVisitorGenderToFile($file, $gender) {
    $visitorGender = assignVisitorGender($gender);
    writeDataToFile($file, 0x22, $visitorGender);
}

# Assign a sprite that corresponds with the name's gender
function writeVisitorSpriteToFile($file, $gender) {
    global $maleSprites;
    global $femaleSprites;

    if ($gender == "man or boy") {
        $maleSprite = chooseSprite($maleSprites);
        $spriteHexValue = pack("v", $maleSprite[0]);
        writeDataToFile($file, 0x2A, $spriteHexValue);
        return $maleSprite;
    }
    else {
        $femaleSprite = chooseSprite($femaleSprites);
        $spriteHexValue = pack("v", $femaleSprite[0]);
        writeDataToFile($file, 0x2A, $spriteHexValue);
        return $femaleSprite;
    }
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