<?php
namespace It;

class It {
    private static $testCases = [];
    private static $current = '';

    public static function is($component, $test=null) {
        self::$current = $component;

        if ($test === null) {
            self::$testCases[] = new TestCase(self::$current);
        } else {
            self::$testCases[] = $test();
        }
    }

    public static function should($description, $test=null) {
        return new TestCase(self::$current, $description, $test);
    }

    public static function expects($item, $description=null) {
        return new Expectation($item, $description);
    }

    public static function lives() {
        $failures = [];
        $success = true;
        foreach (self::$testCases as $case) {
            $failures[] = $case();
        }

        echo PHP_EOL;
        foreach ($failures as $fail) {
            $success = $success && ($fail === []);
            foreach ($fail as $case => $cause) {
                echo "FAILED $case => $cause\n";
            }
        }

        return $success;
    }
}
