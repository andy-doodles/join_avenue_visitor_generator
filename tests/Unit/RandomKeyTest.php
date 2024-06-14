<?php

it('returns the value of a random key in an associative array', function () {
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

    $maleSprites = [
        "Sprite #1" => [0x01, "Hilbert walking normally"],
        "Sprite #2" => [0x02, "Hilbert on a bicycle"],
        "Sprite #8" => [0x08, "Kid with red shirt"],
        "Sprite #10" => [0x0A, "Young man in blue shirt"],
        "Sprite #11" => [0x0B, "Youngster"],
        "Sprite #12" => [0x0C, "Young boy"],
        "Sprite #13" => [0x0D, "Rich boy"],
        "Sprite #18" => [0x12, "Man in green"],
        "Sprite #19" => [0x13, "Man in purple"],
        "Sprite #20" => [0x14, "Large man in orange"],
        "Sprite #24" => [0x18, "Middle-aged man in blue"],
        "Sprite #25" => [0x19, "Middle-aged bald man in green"],
        "Sprite #28" => [0x1C, "Bald older man in brown"],
        "Sprite #30" => [0x1E, "Ace Trainer Boy"],
        "Sprite #32" => [0x20, "Veteran Male"],
        "Sprite #34" => [0x22, "Breeder Male"],
        "Sprite #36" => [0x24, "Ranger Male"],
        "Sprite #38" => [0x26, "Cyclist Male"],
        "Sprite #42" => [0x2A, "Waiter"],
        "Sprite #44" => [0x2C, "Gentleman"],
        "Sprite #46" => [0x2E, "Black Belt"],
        "Sprite #48" => [0x30, "Backpacker Male"],
        "Sprite #50" => [0x32, "Doctor Male"],
        "Sprite #52" => [0x34, "Business man"],
        "Sprite #54" => [0x36, "Clown"],
        "Sprite #55" => [0x37, "Dancer"],
        "Sprite #56" => [0x38, "Musician"],
        "Sprite #57" => [0x39, "Infielder"],
        "Sprite #58" => [0x3A, "Striker"],
        "Sprite #59" => [0x3B, "Linebacker"],
        "Sprite #61" => [0x3D, "Roughneck"],
        "Sprite #62" => [0x3E, "Biker"],
        "Sprite #63" => [0x3F, "Fisherman"],
        "Sprite #64" => [0x40, "Hiker"],
        "Sprite #67" => [0x43, "Worker"],
        "Sprite #68" => [0x44, "Train Manager in Green"],
        "Sprite #69" => [0x45, "Janitor"],
        "Sprite #70" => [0x46, "Mailman in Blue"],
        "Sprite #71" => [0x47, "Train Manager in Blue"],
        "Sprite #72" => [0x48, "Police Officer"],
        "Sprite #73" => [0x49, "Scientist Male"],
        "Sprite #75" => [0x4B, "Train Manager in Blue with Red Visor Male"],
        "Sprite #77" => [0x4D, "Pokémon Center Store Clerk"],
        "Sprite #81" => [0x51, "Man in cyan"],
        "Sprite #83" => [0x53, "Man in Black with Black Glasses"],
        "Sprite #85" => [0x55, "Unova leader Cilan"],
        "Sprite #87" => [0x57, "Unova leader Burgh"],
        "Sprite #89" => [0x59, "Unova leader Clay"],
        "Sprite #91" => [0x5B, "Unova leader Brycen"],
        "Sprite #94" => [0x5E, "Unova Elite Four Grimsley"],
        "Sprite #95" => [0x5F, "Unova Elite Four Marshal"],
        "Sprite #97" => [0x61, "Alder"],
        "Sprite #98" => [0x62, "Team Plasma Man"],
        "Sprite #100" => [0x64, "Sage Ghetsis"],
        "Sprite #101" => [0x65, "Sage Giallo"],
        "Sprite #103" => [0x67, "N"],
        "Sprite #104" => [0x68, "Cedric Juniper"],
        "Sprite #106" => [0x6A, "Looker"],
        "Sprite #107" => [0x6B, "Subway Boss"],
        "Sprite #133" => [0x85, "Basketball player"],
        "Sprite #153" => [0x99, "Kindergarten School Boy"],
        "Sprite #155" => [0x9B, "Miner"],
        "Sprite #177" => [0xB1, "Unova leader Cress"],
        "Sprite #178" => [0xB2, "Unova leader Chili"],
        "Sprite #179" => [0xB3, "Unova Leader Drayden"],
        "Sprite #181" => [0xB5, "Shadow Triad"],
        "Sprite #182" => [0xB6, "Psychic"],
        "Sprite #183" => [0xB7, "Psychic"],
        "Sprite #187" => [0xBB, "Gym guide"],
        "Sprite #188" => [0xBC, "Sage Bronius"],
        "Sprite #189" => [0xBD, "Sage Ryouku"],
        "Sprite #190" => [0xBE, "Sage Gorm"],
        "Sprite #191" => [0xBF, "Sage Rood"],
        "Sprite #192" => [0xC0, "Sage Zinzolin"],
        "Sprite #207" => [0xCF, "Subway Boss"],
        "Sprite #225" => [0xE1, "Unova leader Burgh"],
        "Sprite #227" => [0xE3, "Unova leader Clay"],
        "Sprite #229" => [0xE5, "Unova leader Drayden"],
        "Sprite #230" => [0xE6, "Unova leader Marlon"],
        "Sprite #231" => [0xE7, "Nate"],
        "Sprite #232" => [0xE8, "Nate on a bicycle"],
        "Sprite #250" => [0xFA, "Colress"],
        "Sprite #252" => [0xFC, "Kanto leader Brock"],
        "Sprite #254" => [0xFE, "Kanto leader Lt. Surge"]
    ];

    $result = getValueFromRandomKey($maleSprites);

    echo $result;

    expect($result)->toBeArray();
})

?>