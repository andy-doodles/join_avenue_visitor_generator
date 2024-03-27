<?php

# Select a random file from the source directory
function chooseRandomFile($directory) {
    $directoryArray = scandir($directory);
    # Exclude unwanted directory elements and files
    $directoryArray = array_diff($directoryArray, [".", "..", "desktop.ini"]);
    # Randomize the order of array elements
    shuffle($directoryArray);
    return $directoryArray[0];
}

/* Generate a 6-character name for the visitor and its corresponding file
Gender of name depends on gender created with `generateVisitorGender()` */
function generateFileName($visitorGender) {
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
    fwrite($file, $visitorName);
}

# Inject encoded visitor name to file
function writeVisitorGenderToFile($file, $gender) {
    $visitorGender = assignVisitorGender($gender);
    fseek($file, 0x22);
    fwrite($file, $visitorGender);
    rewind($file);
}

# Assign a sprite that corresponds with the name's gender
function writeVisitorSpriteToFile($file, $gender) {
    global $maleSprites;
    global $femaleSprites;

    if ($gender == "man or boy") {
        $maleSprite = chooseSprite($maleSprites);
        $hexValue = pack("v", $maleSprite[1]);
        fseek($file, 0x2A);
        fwrite($file, $hexValue);
        rewind($file);
    }
    else {
        $femaleSprite = chooseSprite($femaleSprites);
        $hexValue = pack("v", $femaleSprite[1]);
        fseek($file, 0x2A);
        fwrite($file, $hexValue);
        rewind($file);
    }
}

?>