<?php

# http://localhost/join_avenue_visitor_generator/visitor_generator.php

$visitorPath = "C:/xampp/htdocs/join_avenue_visitor_generator/Base Visitors/VISITOR _ Jane.pjv";

# Open visitor file and get file size
$visitorFile = fopen($visitorPath, "r+b");
$visitorSize = filesize($visitorPath);

# Creates a random, 6-character name + 1 terminator. 16-bit, little endian
function randomName() {
    $characterSet = "ABCDEFGHIJKLMNOPQRSTUVWXYZabcdefghijklmnopqrstuvwxyz0123456789";
    $randomName = "";
    $terminator = pack("v", 0xFFFF);

    for ($i = 0; $i < 6; $i++) {
        # Generate a random character from the set and obtain its ASCII number
        $newLetter = $characterSet[rand(0, strlen($characterSet) - 1)];
        $newLetter = mb_convert_encoding($newLetter, "UTF-16LE");
        $randomName .= $newLetter;
    }
    $randomName .= $terminator;
    return $randomName;
}

function writeName($file) {
    $name = randomName();
    fwrite($file, $name);
}

writeName($visitorFile);

fclose($visitorFile);

# http://localhost/join_avenue_visitor_generator/visitor_generator.php

?>