<?php

/**
 * Generated by PHPUnit_SkeletonGenerator on 2018-11-18 at 22:01:37.
 */
class workforceTest extends PHPUnit_Framework_TestCase {

    /**
     * @var workforce
     */
    protected $object;

    /**
     * Sets up the fixture, for example, opens a network connection.
     * This method is called before a test is executed.
     */
    protected function setUp() {
        $this->object = new workforce('2018-10-01');
    }

    /**
     * Tears down the fixture, for example, closes a network connection.
     * This method is called after a test is executed.
     */
    protected function tearDown() {

    }

    public function test__construct() {
        $this->assertObjectHasAttribute('List_of_employees', $this->object);
        $this->assertObjectHasAttribute('List_of_qualified_pharmacist_employees', $this->object);
        $this->assertObjectHasAttribute('List_of_goods_receipt_employees', $this->object);
        $this->assertObjectHasAttribute('List_of_compounding_employees', $this->object);

        $this->assertCount(18, $this->object->List_of_employees);
        $this->assertSame($this->object->List_of_employees[5]->full_name, "Ma Maier");
        $this->assertSame($this->object->List_of_employees[5]->working_week_days, 5);
        $this->assertSame($this->object->List_of_branch_employees[1], array(
            0 => 2,
            1 => 5,
            2 => 6,
            3 => 7,
            4 => 8,
            5 => 9,
            6 => 12,
            7 => 13,
            8 => 16,
            9 => 18,
            10 => 19,
            11 => 20,
        ));
        $this->assertSame($this->object->List_of_qualified_pharmacist_employees, array(
            0 => 1,
            1 => 2,
            2 => 5,
            3 => 9,
            4 => 13,
            5 => 14,
            6 => 17,
            7 => 20,
        ));
        $this->assertSame($this->object->List_of_goods_receipt_employees, array(
            0 => 6,
            1 => 7,
            2 => 8,
            3 => 10,
            4 => 12,
            5 => 13,
            6 => 14,
            7 => 15,
            8 => 16,
            9 => 17,
            10 => 18,
            11 => 19,
        ));
        $this->assertSame($this->object->List_of_compounding_employees, array(
            0 => 6,
            1 => 7,
            2 => 10,
            3 => 12,
            4 => 16,
        ));
    }

}
