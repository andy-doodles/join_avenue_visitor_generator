<?php

include_once "visitor_generator.php";

# Imported JSON files
$jsonCountryList = file_get_contents("countries.json");
$jsonGreetingsListEnglish = file_get_contents("greetings_english.json");
# JSON files decoded into arrays 
$countryList = json_decode($jsonCountryList, true);
$greetingsListEnglish = json_decode($jsonGreetingsListEnglish, true);

# Encoded character that signals the end of a string in .pjv binary files
$stringTerminator = pack("v", 0xFFFF);
# Encoded null character
$nullCharacter = pack("v", 0x0000);

/* Encodes strings to 16-bit, Little Endian
Visitor-related strings have up to 8 characters (7 char + terminator)
If string length is between 1 and 6 inclusive,
encoded nulls pad length until it's 8 */
function encodeStringAddFiller (
    string $unencodedString,
    int $stringLength,
    string $terminator,
    string $nullCharacter,
    bool $isArrayOutput
): string | array
{
    if ($stringLength >= 1 and $stringLength <= 6) {
        $bufferLength = 7 - $stringLength;
        $encodedString = mb_convert_encoding($unencodedString, "UTF-16LE");
        $encodedString .= $terminator;
        # Adds enough null characters to make greeting length = 8
        for ($i = 0; $i < $bufferLength; $i++) {
            $encodedString .= $nullCharacter;
        }
    } else {
        $encodedString = mb_convert_encoding($unencodedString, "UTF-16LE");
        $encodedString .= $terminator;
    }

    if ($isArrayOutput) {
        return [$unencodedString, $encodedString];
    } else {
        return $encodedString;
    }
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

# Encode visitor gender created with `generateVisitorGender()`
function assignVisitorGender(
    string $gender
): string
{
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
function assignVisitorName(
    string $oneByteVisitorName
): string
{
    $terminator = pack("v", 0xFFFF);
    $twoByteName = mb_convert_encoding($oneByteVisitorName, "UTF-16LE");
    $twoByteName .= $terminator;
    return $twoByteName;
}

# Choose a random sprite from a pool
function chooseSprite(
    array $spriteArray
): array {
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

/* Chooses random greeting and encodes it (16-bit, little endian)
Retains unencoded greeting for output
Greetings are up to 7-character long + terminator
If greeting length between 1 and 6 inclusive, greeting structure should be:
(greeting string) + (terminator) + (enough null characters to make greeting length = 8) */
function generateEnglishGreeting(
    array $greetingsArray,
    string $terminator,
    string $nullCharacter
): string | array
{
    $randomIndex = array_rand($greetingsArray);
    $unencodedGreeting = $greetingsArray[$randomIndex];
    $greetingLength = strlen($unencodedGreeting);
    return encodeStringAddFiller($unencodedGreeting, $greetingLength, $terminator,
                            $nullCharacter, true);
}

function getVisitorData(
    string $name,
    string $gender,
    array $spriteData,
    string $country,
    int $countryIndexDec,
    string $countryIndexHex,
    string $subRegion,
    int $subRegionIndexDec,
    string $subRegionIndexHex,
    string $greeting
): array
{
    $hexSpriteValue = dechex($spriteData[0]);
    $decSpriteValue = $spriteData[0];
    $spriteDescription = $spriteData[1];
    $visitorArray = [
        "name" => $name,
        "gender" => $gender,
        "Country" => "$country (Dec: $countryIndexDec, Hex: $countryIndexHex)",
        "Subregion" => "$subRegion (Dec: $subRegionIndexDec, Hex: $subRegionIndexHex)",
        "Sprite description" => "$spriteDescription (Dec: $decSpriteValue, Hex: $hexSpriteValue)",
        "Greeting" => "$greeting"
    ];
    return $visitorArray;
}

?>