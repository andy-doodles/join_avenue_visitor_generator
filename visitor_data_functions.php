<?php

include_once "visitor_generator.php";

# Imported JSON files
$jsonCountryList = file_get_contents("countries.json");
$jsonGreetingsListEnglish = file_get_contents("greetings_english.json");
$jsonFarewellListEnglish = file_get_contents("farewell_english.json");
$jsonShoutsListEnglish = file_get_contents("shout_english.json");
# JSON files decoded into arrays 
$countryList = json_decode($jsonCountryList, true);
$greetingsListEnglish = json_decode($jsonGreetingsListEnglish, true);
$farewellListEnglish = json_decode($jsonFarewellListEnglish, true);
$shoutsListEnglish = json_decode($jsonShoutsListEnglish, true);

# Encoded character that signals the end of a string in .pjv binary files
$stringTerminator = pack("v", 0xFFFF);
# Encoded null character
$nullCharacter = pack("v", 0x0000);

/* Encodes strings to 16-bit, Little Endian
Visitor-related strings have up to 8 characters (7 char + terminator)
If dialogue length between 1 and 6 inclusive, dialogue structure should be:
(dialogue string) + (terminator) + (enough null characters to make dialogue length = 8)
*/
function encodeStringAddFiller (
    string $unencodedString,
    int $stringLength,
    string $terminator,
    string $nullCharacter,
    bool $isArrayOutput,
    bool $isName
): string | array
{
    if ($isName) {
        # Maximum string length for names is 7 (6 characters + 1 terminator)
        if ($stringLength >= 1 and $stringLength <= 5) {
            $bufferLength = 6 - $stringLength;
            $encodedString = mb_convert_encoding($unencodedString, "UTF-16LE");
            $encodedString .= $terminator;
            # Adds enough null characters to make name length = 7
            for ($i = 0; $i < $bufferLength; $i++) {
                $encodedString .= $nullCharacter;
            }
        } else {
            /* If the string is a name and not 1-5 characters long,
            it can only be 6 characters long */
            $encodedString = mb_convert_encoding($unencodedString, "UTF-16LE");
            $encodedString .= $terminator;
        }
    } else {
        /* If the string is not a name, it's a dialogue (greeting, farewell, shout)
        Dialogues can be up to 8 characters total (7 characters + 1 terminator) */
        if ($stringLength >= 1 and $stringLength <= 6) {
            $bufferLength = 7 - $stringLength;
            $encodedString = mb_convert_encoding($unencodedString, "UTF-16LE");
            $encodedString .= $terminator;
            # Adds enough null characters to make greeting length = 8
            for ($i = 0; $i < $bufferLength; $i++) {
                $encodedString .= $nullCharacter;
            }
        } else {
            /* If the string is a dialogue and not 1-6 characters long,
            it can only be 7 characters long */
            $encodedString = mb_convert_encoding($unencodedString, "UTF-16LE");
            $encodedString .= $terminator;
        }
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

# Choose a random country from the available pool
function chooseCountry(
    array $countryList
): array
{
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

# Get the value of a random key from an array
function getValueFromRandomKey (
    array $array
): mixed
{
    # Get all array keys into an array
    # Returns an array like [0, 1, 2, 3, ...]; or ["Sprite #1", "Sprite #2", "Sprite #3", ...];
    $keys = array_keys($array);
    # Get one random key from the keys array. E.g.: 0 or "Sprite 1"
    $randomKey = $keys[array_rand($keys)];
    # Get the value that corresponds to the random key
    $valueOfKey = $array[$randomKey];
    return $valueOfKey;
}

function chooseSprite(
    array $maleSprites,
    array $femaleSprites,
    string $gender,
) {
    if ($gender == "man or boy") {
        $spriteData = getValueFromRandomKey($maleSprites);
        return $spriteData;
    } else {
        $spriteData = getValueFromRandomKey($femaleSprites);
        return $spriteData;
    }
}

/* Generates all visitor dialogues: greeting, farewell, and shout
Chooses random greeting/farewell/shout and encodes it (16-bit, little endian)
Retains unencoded dialogues for output
Dialogues are up to 7-character long + terminator
*/
function generateVisitorDialogue(
    array $dialogueArray,
    string $terminator,
    string $nullCharacter
): string | array
{
    $randomIndex = array_rand($dialogueArray);
    $unencodedDialogue = $dialogueArray[$randomIndex];
    $dialogueLength = strlen($unencodedDialogue);
    return encodeStringAddFiller($unencodedDialogue, $dialogueLength, $terminator,
                            $nullCharacter, true, false);
}

function getVisitorData(
    string $name,
    string $gender,
    string $hexSpriteValue,
    int $decSpriteValue,
    string $spriteDescription,
    string $country,
    int $countryIndexDec,
    string $countryIndexHex,
    string $subRegion,
    int $subRegionIndexDec,
    string $subRegionIndexHex,
    string $greeting,
    string $farewell,
    string $shout
): array
{
    $visitorArray = [
        "name" => $name,
        "gender" => $gender,
        "Country" => "$country (Dec: $countryIndexDec, Hex: $countryIndexHex)",
        "Subregion" => "$subRegion (Dec: $subRegionIndexDec, Hex: $subRegionIndexHex)",
        "Sprite description" => "$spriteDescription (Dec: $decSpriteValue, Hex: $hexSpriteValue)",
        "Greeting" => "$greeting",
        "Farewell" => "$farewell",
        "Shout" => "$shout"
    ];
    return $visitorArray;
}

?>