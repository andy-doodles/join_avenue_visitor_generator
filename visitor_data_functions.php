<?php

include_once "visitor_generator.php";
include_once "json_country_list.php";

$countryList = json_decode($jsonCountryList, true);

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

# Choose a random country from the available pool
function chooseCountry() {
    global $countryList;
    # Choose a random country from the JSON file
    $visitorCountry = $countryList["countries"][array_rand($countryList["countries"])];
    # Get the country's index, and name
    $countryIndexDec = $visitorCountry["index"];
    $countryIndexHex = dechex($visitorCountry["index"]);
    $countryName = $visitorCountry["name"];
    # Get the country's subregions or empty array if no subregions exist
    $countrySubRegions = $visitorCountry["subregions"];

    if (!empty($countrySubRegions)) {
        # Randomly pick a subregion
        $visitorSubRegion = $countrySubRegions[array_rand($countrySubRegions)];
        # Get the subregion's index and name
        $subRegionIndexDec = $visitorSubRegion["index"];
        $subRegionIndexHex = dechex($visitorSubRegion["index"]);
        $subRegionName = $visitorSubRegion["name"];
    } else {
        # Nullify subregion
        $subRegionIndexHex = 0x00;
        $subRegionIndexDec = 0;
        $visitorSubRegion = "None";
        $subRegionName = "None";
    }

    $countryArray = [$countryName, $countryIndexDec, $countryIndexHex,
        $subRegionName, $subRegionIndexDec, $subRegionIndexHex,];
    return $countryArray;
}

function getVisitorData($name, $gender, $spriteData, $country, $countryIndexDec,
    $countryIndexHex, $subRegion, $subRegionIndexDec,
    $subRegionIndexHex) {
    $hexSpriteValue = dechex($spriteData[0]);
    $decSpriteValue = $spriteData[0];
    $spriteDescription = $spriteData[1];
    $visitorArray = [
        "name" => $name,
        "gender" => $gender,
        "Country" => "$country (Dec: $countryIndexDec, Hex: $countryIndexHex)",
        "Subregion" => "$subRegion (Dec: $subRegionIndexDec, Hex: $subRegionIndexHex)",
        "Sprite description" => "$spriteDescription (Dec: $decSpriteValue, Hex: $hexSpriteValue)"
    ];
    return $visitorArray;
}

?>