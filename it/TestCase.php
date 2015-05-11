<?php
namespace It;

class TestCase {
    private $suite;
    private $callbacks = [];

    public function __construct($suite, $description=null, $callback=null) {
        $this->suite = $suite;

        if ($description !== null && $callback !== null) {
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
            echo "    [P] It should $case\n";
        }
        echo "\n";
    }

    public function itShould($description, $callback) {
        $this->callbacks[$description] = $callback;

        return $this;
    }
}
