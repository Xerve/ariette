<?php
namespace It;

class Expectation {
    private $item;
    private $description;
    private $comparison;
    private $compareTo = null;
    private $invert = false;

    private $result;

    private $queryString = '';

    public function __construct($item, $description=null) {
        $this->item = $item;

        if ($description === null) {
            if (is_array($item)) {
                $this->description = json_encode($item, true);
            } else {
                $this->description = (string) $item;
            }
        } else {
            $this->description = $description;
        }
    }

    public function __call($name, $args) {
        if (count($args) >= 1) {
            $this->compareTo = $args[0];
        }

        $this->apply($name);

        if (count($args) >= 1) {
            $this->queryString .= ' ' . $args[0];
        }

        $this->run();
    }

    public function __get($name) {
        $this->apply($name);
        return $this;
    }

    public function run() {
        switch ($this->comparison) {
            case '==': $this->doubleEqualCompare(); break;
            case '===': $this->tripleEqualCompare(); break;
            case 'in': $this->inCompare(); break;
        }

        if ($this->invert) {
            $this->result = !$this->result;
        }

        if (!$this->result) {
            throw new ExpectationException("Expect $this->description$this->queryString");
        }
    }

    public function apply($name) {
        $this->queryString .= " $name";
        switch ($name) {
            case 'to': $this->to(); break;
            case 'be': $this->be(); break;
            case 'equal': $this->equal(); break;
            case 'empty': $this->_empty(); break;
            case 'have': $this->have(); break;
            case 'not': $this->not(); break;
            default: throw new ItException("No expectation clause $name!");
        }
    }

    private function to() {
        null;
    }

    private function be() {
        $this->comparison = '==';
    }

    private function equal() {
        $this->comparison = '===';
    }

    private function _empty() {
        $this->comparison = '===';
        $this->compareTo = [];
    }

    private function have() {
        $this->comparison = 'in';
    }

    private function not() {
        $this->invert = !$this->invert;
    }

    private function doubleEqualCompare() {
        $this->result = ($this->item == $this->compareTo);
    }

    private function tripleEqualCompare() {
        $this->result = ($this->item === $this->compareTo);
    }

    private function inCompare() {
        $this->result = array_key_exists($this->compareTo, $this->item);
    }
}
