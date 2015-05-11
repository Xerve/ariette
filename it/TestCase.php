<?php
namespace It;

class TestCase {
    private $suite;
    private $callbacks = [];

    public function __construct($suite, $description=null, $callback=null) {
        $this->suite = $suite;

        if ($description !== null) {
            $this->callbacks[$description] = $callback;
        }

        return $this;
    }

    public function __invoke() {
        return $this->run();
    }

    public function run() {
        echo "$this->suite\n";
        foreach ($this->callbacks as $case => $func) {
            if (is_callable($func)) {
                try {
                    $func();
                    echo "    [O] It should $case\n";
                } catch (\Exception $e) {
                    $res = $e->getMessage();
                    echo "    [X] It should $case\n";
                }
            } else if ($func === null) {
                echo "    [ ] It should $case\n";
            } else {
                throw new ItException("$case is not a callable case!");
            }
        }
        echo "\n";
    }

    public function itShould($description, $callback=null) {
        $this->callbacks[$description] = $callback;

        return $this;
    }
}
