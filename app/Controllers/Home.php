<?php

namespace App\Controllers;
require_once __DIR__ . '/../../vendor/autoload.php'; 
//require './vendor/autoload.php'; 

use PHPHtmlParser\Dom;

class Home extends BaseController
{
    public function findMissingNumbers($from, $to, $numberString) {
        $allNumbers = range($from, $to); // Generate an array of all numbers from 1 to 59
        $numbers = array_map('intval', explode(',', $numberString)); // Convert the input string to an array of integers
        $missingNumbers = array_diff($allNumbers, $numbers); // Find the missing numbers

        //echo "The numbers not in the list are: " . implode(", ", $missingNumbers);
        return $missingNumbers;
    }

    public function calculatePercentage($numberString) {
        $numbers = explode(',', $numberString); // Convert the input string to an array
        $countMap = array_count_values($numbers); // Count the occurrences of each number
        $totalCount = count($numbers); // Total count of numbers in the string

        $percentageMap = array();
        foreach ($countMap as $number => $count) {
            $percentage = ($count / $totalCount) * 100;
            $percentageMap[$number] = number_format($percentage, 2, '.', '');
        }

        return $percentageMap;
    }

    public function sortListString($numbersStr) {
        // Step 1: Explode the string to get an array of numbers
        $numbersArray = explode(",", $numbersStr);

        // Step 2: Convert the array elements to integers
        $numbersArray = array_map('intval', $numbersArray);

        // Step 3: Sort the array in ascending order
        sort($numbersArray);

        // Step 4: Convert the sorted array back to a comma-separated string
        $sortedNumbersStr = implode(",", $numbersArray);

        return $sortedNumbersStr;
    }

    public function sortPercentageMap($percentageMap) {
        // Step 1: Sort the array based on the percentage values in ascending order
        asort($percentageMap);

        return $percentageMap;
    }

    public function randomNumbersFromPercentage($percentageMap, $count = 5) {
        // Calculate the cumulative sum of percentages
        $cumulativeSum = 0;
        foreach ($percentageMap as $percentage) {
            $cumulativeSum += $percentage;
        }

        // Generate 5 random numbers and find the corresponding numbers based on the generated random numbers
        $randomNumbers = array();
        for ($i = 0; $i < $count; $i++) {
            $randomNumber = mt_rand(1, $cumulativeSum);

            $currentSum = 0;
            foreach ($percentageMap as $number => $percentage) {
                $currentSum += $percentage;
                if ($randomNumber <= $currentSum) {
                    $randomNumbers[] = $number;
                    break;
                }
            }
        }

        return $randomNumbers;
    }

    public function randomNonDuplicatedNumbersFromPercentage($percentageMap, $count = 5) {
        // Calculate the cumulative sum of percentages
        $cumulativeSum = 0;
        foreach ($percentageMap as $percentage) {
            $cumulativeSum += $percentage;
        }

        // Generate 5 random non-duplicated numbers and find the corresponding numbers based on the generated random numbers
        $randomNumbers = array();
        $numbersPool = array_keys($percentageMap);

        for ($i = 0; $i < min($count, count($numbersPool)); $i++) {
            $randomNumber = mt_rand(1, $cumulativeSum);

            $currentSum = 0;
            foreach ($percentageMap as $number => $percentage) {
                $currentSum += $percentage;
                if ($randomNumber <= $currentSum && in_array($number, $numbersPool)) {
                    $randomNumbers[] = $number;
                    $index = array_search($number, $numbersPool);
                    unset($numbersPool[$index]);
                    break;
                }
            }
        }

        return $randomNumbers;
    }

    // public function findMissingNumberInRange($numbersStr, $rangeStart, $rangeEnd) {
    //     // Step 1: Explode the string to get an array of numbers
    //     $numbersArray = explode(",", $numbersStr);

    //     // Step 2: Convert the array elements to integers
    //     $numbersArray = array_map('intval', $numbersArray);

    //     // Step 3: Create an associative array to mark existing numbers in the range
    //     $existingNumbers = array_fill($rangeStart - 1, $rangeEnd - $rangeStart + 1, false);

    //     // Step 4: Mark existing numbers in the array
    //     foreach ($numbersArray as $number) {
    //         if (array_key_exists($number - 1, $existingNumbers)) {
    //             $existingNumbers[$number - 1] = true;
    //         }
    //     }

    //     // Step 5: Find the missing number in the range
    //     $missingNumbers = [];
    //     foreach ($existingNumbers as $number => $exists) {
    //         if (!$exists) {
    //             $missingNumbers[] = $number + 1;
    //         }
    //     }

    //     // Step 6: Convert the missing numbers array to a comma-separated string
    //     $missingNumbersString = implode(",", $missingNumbers);

    //     echo $missingNumbersString;exit();
    //     // Step 7: Return the comma-separated string
    //     return $missingNumbersString;
    // }

    // public function generateMissingSequence($numbersStr, $rangeStart, $rangeEnd):string {
    //     // Step 1: Explode the string to get an array of numbers
    //     $numbersArray = explode(",", $numbersStr);

    //     // Step 2: Convert the array elements to integers
    //     $numbersArray = array_map('intval', $numbersArray);

    //     // Step 3: Create an empty array to store missing numbers
    //     $missingNumbers = [];

    //     // Step 4: Iterate through the range and check for missing numbers
    //     for ($num = $rangeStart; $num <= $rangeEnd; $num++) {
    //         if (!in_array($num, $numbersArray)) {
    //             $missingNumbers[] = $num;
    //         }
    //     }

    //     // Step 5: Convert the missing numbers array to a comma-separated string
    //     $missingNumbersString = implode(",", $missingNumbers);

    //     // Step 6: Return the comma-separated string
    //     return $missingNumbersString;
    // }

    public function index(): string
    {

        $dom = new Dom;
        $dom->loadFromFile('Previous Results _ Powerball.html');
        $gameBallGroup = $dom->find('.game-ball-group');

        $allPowerBallNumber = '';
        $rowPowerBallNumber = [];
        $rangePowerBallNumber = '';
        $allPowerBallSpecialNumber = '';
        $rangePowerBallSpecialNumber = '';

        $groupCount = 0;
        foreach ($gameBallGroup as $itemBall)
        {
            // get the class attr
            $itemPowerballs = $itemBall->find('.item-powerball');
            
            $count = 1;
            $rowPowerBallNumber[$groupCount] = [];

            foreach ($itemPowerballs as $powerballNumberEle) {
                $powerballNumber = $powerballNumberEle->text;

                

                if($count <= 5) {
                    array_push($rowPowerBallNumber[$groupCount], $powerballNumber);
                    if (!in_array($powerballNumber, explode(",", $rangePowerBallNumber))) {
                        if(empty($rangePowerBallNumber)) {
                            $rangePowerBallNumber .= "$powerballNumber";
                        } else {
                            $rangePowerBallNumber .= ",$powerballNumber";
                        }
                    }

                    if(empty($allPowerBallNumber)) {
                        $allPowerBallNumber .= "$powerballNumber";
                    } else {
                        $allPowerBallNumber .= ",$powerballNumber";
                    }
                } else {

                    if($powerballNumber <= 26) {
                        if (!in_array($powerballNumber, explode(",", $rangePowerBallSpecialNumber))) {
                            if(empty($rangePowerBallSpecialNumber)) {
                                $rangePowerBallSpecialNumber .= "$powerballNumber";
                            } else {
                                $rangePowerBallSpecialNumber .= ",$powerballNumber";
                            }
                        }

                        if(empty($allPowerBallSpecialNumber)) {
                            $allPowerBallSpecialNumber .= "$powerballNumber";
                        } else {
                            $allPowerBallSpecialNumber .= ",$powerballNumber";
                        }
                    }
                    
                }
                $count++;
            }
            $groupCount++;
        }



        $missingPowerBallNumbers = $this->findMissingNumbers(1, 69, $rangePowerBallNumber);
        $missingPowerBallSpecialNumbers = $this->findMissingNumbers(1, 26, $rangePowerBallSpecialNumber);

        // Display the missing numbers
        // echo "Missing power ball number are: " . $this->sortListString(implode(", ", $missingPowerBallNumbers));
        // echo "<br/>Missing power ball special are: " . $this->sortListString(implode(", ", $missingPowerBallSpecialNumbers));

        // echo "<br/>Rang power ball number: ", $this->sortListString($rangePowerBallNumber), "<br/>Rang power ball special number: ", $this->sortListString($rangePowerBallSpecialNumber);
        // echo "<br/>All power ball number: ", $this->sortListString($allPowerBallNumber);
        // echo "<br/>All power ball special number: ", $this->sortListString($allPowerBallSpecialNumber);

        $allPowerBallNumberSort = $this->sortListString($allPowerBallNumber);
        $percentageMap = $this->calculatePercentage($allPowerBallNumberSort);

        // foreach ($percentageMap as $number => $percentage) {
        //     echo "<br/>Number $number appears $percentage% of the time.\n";
        // }

        $sortedPercentageMap = $this->sortPercentageMap($percentageMap);
        // print_r($sortedPercentageMap);

        //print with table
        // $html = "<table><tr>";
        // $count = 1;
        // foreach ($sortedPercentageMap as $number => $percentage) {
        //     $html .= "<td style='border: 1px solid;'>$count/ <br/> $number</br>$percentage%</td>";
        //     $count++;
        // }
        // $html .= "<td style='border: 1px solid;'>Total percentage%</td>";
        // $html .= "</tr>";
        // if(count($rowPowerBallNumber) > 0) {
        //     for($i=0; $i< count($rowPowerBallNumber); $i++) {
        //         $html .= "<tr>";
        //         $countSpecipalNumber = 0;
        //         $specialNumber = "";
        //         $totalPercentage = 0;
        //         foreach ($sortedPercentageMap as $number => $percentage) {
        //             if($countSpecipalNumber >= 5) {
        //                 $specialNumber = "*";
        //             }
        //             if(in_array($number, $rowPowerBallNumber[$i])) {
        //                 $totalPercentage += $percentage;
        //                 $countSpecipalNumber++;
        //                 $html .= "<td style='border: 1px solid;color:red;font-weight:bold;'>$number $specialNumber</td>";
        //             } else {
        //                 $html .= "<td style='border: 1px solid;'></td>";
        //             }
        //         }
        //         $html .= "<td style='border: 1px solid;'>$totalPercentage%</td>";
        //         $html .= "</tr>";
        //     }
        // }
        // $html .= "</tr></table>";
        // echo $html;

        //echo $allPowerBallSpecialNumber, '<br/>';

        $randomNumbers = $this->randomNonDuplicatedNumbersFromPercentage($sortedPercentageMap, 5);
        print_r($randomNumbers);

        foreach ($sortedPercentageMap as $number => $percentage) {
            echo "Number: $number and $percentage%<br/>";
            // $count++;
        }

        echo '<br/>--------------------------';
        $allPowerBallSpecialNumberSort = $this->sortListString($allPowerBallSpecialNumber);
        $percentageMapSpecialNumber = $this->calculatePercentage($allPowerBallSpecialNumberSort);
        $sortedPercentageMapSpecialNumber = $this->sortPercentageMap($percentageMapSpecialNumber);

        $randomNumbers = $this->randomNonDuplicatedNumbersFromPercentage($sortedPercentageMapSpecialNumber, 1);
        print_r($randomNumbers);

        foreach ($sortedPercentageMapSpecialNumber as $number => $percentage) {
            echo "Number: $number and $percentage%<br/>";
            // $count++;
        }

        //echo '<br />', $rangePowerBallNumber, '<br />', $rangePowerBallSpecialNumber;
        exit();
        //$this->view7

        
    }
}
