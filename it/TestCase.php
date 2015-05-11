<?php
namespace It;

class TestCase {
    private $component;
    private $callbacks = [];

    public function __construct($component, $description=null, $callback=null) {
        $this->component = $component;

        if ($description !== null) {
            $this->callbacks[$description] = $callback;
        }

        return $this;
    }

    public function __invoke() {
        return $this->run();
    }

    public function run() {
        $failures = [];
        echo $this->component, PHP_EOL;
        foreach ($this->callbacks as $case => $func) {
            if (is_callable($func)) {
                try {
                    $func($this->component);
                    echo "    [P] It should $case\n";
                } catch (\Exception $e) {
                    $failures[$case] = $e->getMessage();
                    echo "    [F] It should $case\n";
                }
            } else if ($func === null) {
                echo "    [ ] It should $case\n";
            } else {
                throw new ItException("$case is not a callable case!");
            }
        }
        echo PHP_EOL;

        return $failures;
    }

    public function itShould($description, $callback=null) {
        $this->callbacks[$description] = $callback;

        return $this;
    }
}
