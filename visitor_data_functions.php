<?php

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

/* Encode visitor name string created with `generateFileName()`
Append encoded terminator */
function assignVisitorName($oneByteVisitorName) {
    $terminator = pack("v", 0xFFFF);
    $twoByteName = mb_convert_encoding($oneByteVisitorName, "UTF-16LE");
    $twoByteName .= $terminator;
    return $twoByteName;
}

# Choose a random sprite from a pool
function chooseSprite($spriteArray) {
    $keys = array_keys($spriteArray);
    $randomKey = $keys[array_rand($keys)];
    $sprite = $spriteArray[$randomKey];
    return $sprite;
}

function getVisitorData($name, $gender, $spriteData) {
    $visitorArray = [
        "name" => $name,
        "gender" => $gender,
        "hex value for sprite" => [dechex($spriteData[0]),
        "decimal value for sprite" => $spriteData[0]],
        "description" => $spriteData[1]
    ];
    return $visitorArray;
}

?>