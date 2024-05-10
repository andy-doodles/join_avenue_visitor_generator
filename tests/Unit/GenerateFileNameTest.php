<?php

# include_once "file_handling_functions.php";

it('generates a Faker PHP name based on gender', function () {
    function generateFileName(
        string $visitorGender,
        $faker
    ): string
    {
        do {
            if ($visitorGender == "man or boy") {
                $fileName = $faker->firstNameMale();
            } else {
                $fileName = $faker->firstNameFemale();
            }
        } while (strlen($fileName) > 6);

        return $fileName;
    }
    
    $faker = Faker\Factory::create();

    $newFileName = generateFileName("man or boy", $faker);
    $nameLength = strlen($newFileName);
    expect($newFileName)->toBeString()
        -> and ($nameLength)->toBeBetween(1, 6);
});

?>