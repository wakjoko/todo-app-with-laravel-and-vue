<?php

namespace Tests\Unit;

use PHPUnit\Framework\TestCase;

class ExampleTest extends TestCase
{
    /**
     * A basic test example.
     */
    public function test_that_true_is_true(): void
    {
        $this->assertTrue(true);
    }

    function fizzBuzz($n) {    
        $x = 1;
        $results = [];
        $processNumber = function($i) {
            if ($i < 0 || ($i > pow(10, 5) * 2)) {
                return false;
            }
            
            $isMultipleOfThree = $i % 3 === 0;
            $isMultipleOfFive = $i % 5 === 0;
            
            if ($isMultipleOfThree && $isMultipleOfFive) {
                return 'FizzBuzz';
            }
            
            if ($isMultipleOfThree && !$isMultipleOfFive) {
                return 'Fizz';
            }
            
            if (!$isMultipleOfThree && $isMultipleOfFive) {
                return 'Buzz';
            }
            
            if (!$isMultipleOfThree && !$isMultipleOfFive) {
                return $i;
            }
            
            return false;
        };
        
        while ($x <= $n) {
            $result = $processNumber($x);
            
            if ($result) {
                array_push($results, $result);
            }
            
            $x++;
        }
        
        echo implode(PHP_EOL, $results);
    }
}
