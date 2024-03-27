<?php

# http://localhost/join_avenue_visitor_generator/visitor_generator.php

require_once "C:/xampp/htdocs/join_avenue_visitor_generator/libraries/vendor/autoload.php";
$faker = Faker\Factory::create();
$sourceDirectory = "C:/xampp/htdocs/join_avenue_visitor_generator/Base Visitors/";

# Select a random file from the source directory
function chooseRandomFile($directory) {
    $directoryArray = scandir($directory);
    # Exclude unwanted directory elements and files
    $directoryArray = array_diff($directoryArray, [".", "..", "desktop.ini"]);
    # Randomize the order of array elements 
    shuffle($directoryArray);
    return $directoryArray[0];
}

function generateVisitorGender() {
    $genderArray = ["man", "woman"];
    shuffle($genderArray);

    if ($genderArray[0] == "man") {
        $visitorGender = "man or boy";
        return $visitorGender;
    }
    else {
        $visitorGender = "woman or girl";
        return $visitorGender;
    }
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

/* Encode visitor name string created with `generateFileName()`
Append encoded terminator */
function assignVisitorName($oneByteVisitorName) {
    $terminator = pack("v", 0xFFFF);
    $twoByteName = mb_convert_encoding($oneByteVisitorName, "UTF-16LE");
    $twoByteName .= $terminator;
    return $twoByteName;
}

# Inject encoded visitor name to file
function writeVisitorNameToFile($file) {
    global $newFileName;
    $visitorName = assignVisitorName($newFileName);
    fwrite($file, $visitorName);
}

# Encode visitor gender created with `generateVisitorGender()`
function assignVisitorGender($gender) {
    if ($gender == "man or boy") {
        $visitorGender = pack("v", 0x00);
        return $visitorGender;
    }
    else {
        $visitorGender = pack("v", 0x10);
        return $visitorGender;
    }
}

# Inject encoded visitor name to file
function writeVisitorGenderToFile($file, $gender) {
    $visitorGender = assignVisitorGender($gender);
    fseek($file, 0x22);
    fwrite($file, $visitorGender);
    rewind($file);
}

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
    writeVisitorNameToFile($outputVisitorFile);
    fclose($outputVisitorFile);
}

# http://localhost/join_avenue_visitor_generator/visitor_generator.php

?>