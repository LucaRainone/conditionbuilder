<?php

namespace rain1\ConditionBuilder\test;

use PHPUnit\Framework\TestCase;
use rain1\ConditionBuilder\ConditionBuilder;
use rain1\ConditionBuilder\Operator\OperatorInterface;

class ConditionBuilderTest extends TestCase
{

    public function testEmpty()
    {
        $conditionBuilder = self::getInstanceModeAnd();
        self::assertEquals("(TRUE)", $conditionBuilder->build(), "Empty condition in AND must print (TRUE)");

        $conditionBuilder = self::getInstanceModeOr();
        self::assertEquals("(FALSE)", $conditionBuilder->build(), "Empty condition in OR must print (FALSE)");
    }

    public function testDummyOperator()
    {
        $conditionBuilder = self::getInstanceModeAnd();

        $conditionBuilder->append(new DummyOperator(true));

        self::assertEquals($conditionBuilder->build(), "(a DUMMY_OP b)");
        self::assertEquals($conditionBuilder->values(), [1, 2, 3]);
    }

    public function testDummyOperatorConcatAND()
    {
        $conditionBuilder = self::getInstanceModeAnd();

        $conditionBuilder
            ->append(new DummyOperator(true))
            ->append(new DummyOperator(true));

        self::assertEquals($conditionBuilder->build(), "(a DUMMY_OP b AND a DUMMY_OP b)");
        self::assertEquals($conditionBuilder->values(), [1, 2, 3, 1, 2, 3]);
    }

    public function testDummyOperatorConcatOR()
    {
        $conditionBuilder = self::getInstanceModeOr();

        $conditionBuilder
            ->append(new DummyOperator(true))
            ->append(new DummyOperator(true));

        self::assertEquals($conditionBuilder->build(), "(a DUMMY_OP b OR a DUMMY_OP b)");
        self::assertEquals($conditionBuilder->values(), [1, 2, 3, 1, 2, 3]);
    }

    public function testMultipleArgumentAppend()
    {
        $conditionBuilder = self::getInstanceModeAnd();

        $conditionBuilder
            ->append(new DummyOperator(true), new DummyOperator(true));

        self::assertEquals($conditionBuilder->build(), "(a DUMMY_OP b AND a DUMMY_OP b)");
        self::assertEquals($conditionBuilder->values(), [1, 2, 3, 1, 2, 3]);
    }

    public function testIgnoreNotConfiguredOperator()
    {
        $conditionBuilder = self::getInstanceModeAnd();

        $conditionBuilder
            ->append(new DummyOperator(true), new DummyOperator(false));

        self::assertEquals($conditionBuilder->build(), "(a DUMMY_OP b)");
        self::assertEquals($conditionBuilder->values(), [1, 2, 3]);
    }

    private static function getInstanceModeAnd()
    {
        return new ConditionBuilder(ConditionBuilder::MODE_AND);
    }

    private static function getInstanceModeOr()
    {
        return new ConditionBuilder(ConditionBuilder::MODE_OR);
    }

}

class DummyOperator implements OperatorInterface
{

    protected $_isConfigured;

    public function __construct($isConfigured = true)
    {
        $this->_isConfigured = $isConfigured;
    }

    public function build(): String
    {
        return "a DUMMY_OP b";
    }

    public function values()
    {
        return [1, 2, 3];
    }

    public function not()
    {

    }

    public function isConfigured()
    {
        return $this->_isConfigured;
    }

}