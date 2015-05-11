<?php
namespace It;

class It {
    private static $testCases = [];
    private static $current = '';

    public static function is($description, $test=null) {
        $description = (string) $description;
        self::$current = $description;

        if ($test === null) {
            self::$testCases[] = new TestCase(self::$current);
        } else {
            self::$testCases[] = $test();
        }
    }

    public static function should($description, $test=null) {
        return new TestCase(self::$current, $description, $test);
    }

    public static function lives() {
        foreach (self::$testCases as $case) {
            $case();
        }
    }
}
