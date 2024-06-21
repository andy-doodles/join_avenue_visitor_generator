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

/* 
Function to generate random number within a range while accounting for
probability brackets:
* 90% of numbers would be in the first sub-range
* 5% of numbers would be in the second sub-range
* 2% of numbers would be in the third sub-range
* 1% of numbers would be in the fourth sub-range
* 0.99% of numbers would be in the fifth sub-range
* 0.01% of numbers would be in the sixth sub-range

To achieve this, the function generates a number between 1 and 10000.
Depending on the value of the random number, the function then generates 
a new random number within the corresponding range.

Example: Generate a number between 1 and 2000
* 90% of the time, the number would be in the 1 - 200 range
* 5% of the time, the number would be in the 201 - 500 range
* 2% of the time, the number would be in the 501 - 1000 range
* 1% of the time, the number would be in the 1001 - 1500 range
* 0.99% of the time, the number would be in the 1501 - 1900 range
* 0.01% of the time, the number would be in the 1901 - 2000 range

Sub-ranges are editable
*/
function getWeightedRandomNumber(
    int $rangeOneMax = 200,
    int $rangeTwoMin = 201,
    int $rangeTwoMax = 500,
    int $rangeThreeMin = 501,
    int $rangeThreeMax = 1000,
    int $rangeFourMin = 1001,
    int $rangeFourMax = 1500,
    int $rangeFiveMin = 1501,
    int $rangeFiveMax = 1900,
    int $rangeSixMin = 1901,
    int $rangeSixMax = 2000
)
{
    $randomNumber = rand(1, 10000);
    if ($randomNumber <= 9000) {
        $min = 1;    $max = $rangeOneMax;
    } elseif ($randomNumber <= 9500) {
        $min = $rangeTwoMin;  $max = $rangeTwoMax;
    } elseif ($randomNumber <= 9700) {
        $min = $rangeThreeMin;  $max = $rangeThreeMax;
    } elseif ($randomNumber <= 9800) {
        $min = $rangeFourMin; $max = $rangeFourMax;
    } elseif($randomNumber <= 9999) {
        $min = $rangeFiveMin; $max = $rangeFiveMax;
    } else {
        $min = $rangeSixMin; $max = $rangeSixMax;
    }

    return rand($min,$max);
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
    string $shout,
    string $dateMet,
    int $visitorMedals,
    int $visitorLinkTrades,
    int $visitorNicknamesGiven,
    int $visitorCustomers,
    int $visitorMoneySpent,
    int $visitorPassersbyMet,
    int $visitorLinkBattles,
    int $visitorPokemonCaught,
    int $visitorPokemonEggsHatched
): array
{
    $visitorArray = [
        "name" => $name,
        "Date met" => $dateMet,
        "gender" => $gender,
        "Country" => "$country (Dec: $countryIndexDec, Hex: $countryIndexHex)",
        "Subregion" => "$subRegion (Dec: $subRegionIndexDec, Hex: $subRegionIndexHex)",
        "Sprite description" => "$spriteDescription (Dec: $decSpriteValue, Hex: $hexSpriteValue)",
        "Greeting" => "$greeting",
        "Farewell" => "$farewell",
        "Shout" => "$shout",
        "Number of medals" => $visitorMedals,
        "Number of link trades" => $visitorLinkTrades,
        "Number of nicknames given" => $visitorNicknamesGiven,
        "Number of customers the visitor has received in their own Avenue" => $visitorCustomers,
        "Money spent" => $visitorMoneySpent,
        "Passersby met by the visitor" => $visitorPassersbyMet,
        "Link Battles the visitor has participated in" => $visitorLinkBattles,
        "Pokémon the visitor has caught" => $visitorPokemonCaught,
        "Pokémon Eggs the visitor has hatched" => $visitorPokemonEggsHatched
    ];
    return $visitorArray;
}

?>