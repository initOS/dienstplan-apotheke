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
        $this->assertSame(serialize($this->object->List_of_employees[5]), 'O:8:"employee":13:{s:11:"employee_id";i:5;s:10:"first_name";s:2:"Ma";s:9:"last_name";s:5:"Maier";s:9:"full_name";s:8:"Ma Maier";s:19:"principle_branch_id";i:1;s:18:"working_week_hours";d:40;s:17:"working_week_days";i:5;s:19:"lunch_break_minutes";d:30;s:19:"start_of_employment";s:10:"2015-01-01";s:17:"end_of_employment";N;s:8:"holidays";i:28;s:16:"Principle_roster";a:5:{i:1;a:1:{i:0;O:11:"roster_item":18:{s:8:"date_sql";s:10:"2018-11-19";s:9:"date_unix";i:1542582000;s:11:"employee_id";i:5;s:9:"branch_id";i:1;s:7:"comment";N;s:17:"' . "\0" . '*' . "\0" . 'duty_start_int";i:39600;s:17:"' . "\0" . '*' . "\0" . 'duty_start_sql";s:5:"11:00";s:15:"' . "\0" . '*' . "\0" . 'duty_end_int";i:72000;s:15:"' . "\0" . '*' . "\0" . 'duty_end_sql";s:5:"20:00";s:18:"' . "\0" . '*' . "\0" . 'break_start_int";i:52200;s:18:"' . "\0" . '*' . "\0" . 'break_start_sql";s:5:"14:30";s:16:"' . "\0" . '*' . "\0" . 'break_end_int";i:54000;s:16:"' . "\0" . '*' . "\0" . 'break_end_sql";s:5:"15:00";s:13:"working_hours";d:8.5;s:14:"break_duration";i:1800;s:13:"duty_duration";i:32400;s:15:"working_seconds";i:30600;s:7:"weekday";s:1:"1";}}i:2;a:1:{i:0;O:11:"roster_item":18:{s:8:"date_sql";s:10:"2018-11-20";s:9:"date_unix";i:1542668400;s:11:"employee_id";i:5;s:9:"branch_id";i:1;s:7:"comment";N;s:17:"' . "\0" . '*' . "\0" . 'duty_start_int";i:28800;s:17:"' . "\0" . '*' . "\0" . 'duty_start_sql";s:5:"08:00";s:15:"' . "\0" . '*' . "\0" . 'duty_end_int";i:59400;s:15:"' . "\0" . '*' . "\0" . 'duty_end_sql";s:5:"16:30";s:18:"' . "\0" . '*' . "\0" . 'break_start_int";i:41400;s:18:"' . "\0" . '*' . "\0" . 'break_start_sql";s:5:"11:30";s:16:"' . "\0" . '*' . "\0" . 'break_end_int";i:43200;s:16:"' . "\0" . '*' . "\0" . 'break_end_sql";s:5:"12:00";s:13:"working_hours";d:8;s:14:"break_duration";i:1800;s:13:"duty_duration";i:30600;s:15:"working_seconds";i:28800;s:7:"weekday";s:1:"2";}}i:3;a:1:{i:0;O:11:"roster_item":18:{s:8:"date_sql";s:10:"2018-11-21";s:9:"date_unix";i:1542754800;s:11:"employee_id";i:5;s:9:"branch_id";i:1;s:7:"comment";N;s:17:"' . "\0" . '*' . "\0" . 'duty_start_int";i:34200;s:17:"' . "\0" . '*' . "\0" . 'duty_start_sql";s:5:"09:30";s:15:"' . "\0" . '*' . "\0" . 'duty_end_int";i:66600;s:15:"' . "\0" . '*' . "\0" . 'duty_end_sql";s:5:"18:30";s:18:"' . "\0" . '*' . "\0" . 'break_start_int";i:48600;s:18:"' . "\0" . '*' . "\0" . 'break_start_sql";s:5:"13:30";s:16:"' . "\0" . '*' . "\0" . 'break_end_int";i:50400;s:16:"' . "\0" . '*' . "\0" . 'break_end_sql";s:5:"14:00";s:13:"working_hours";d:8.5;s:14:"break_duration";i:1800;s:13:"duty_duration";i:32400;s:15:"working_seconds";i:30600;s:7:"weekday";s:1:"3";}}i:4;a:1:{i:0;O:11:"roster_item":18:{s:8:"date_sql";s:10:"2018-11-22";s:9:"date_unix";i:1542841200;s:11:"employee_id";i:5;s:9:"branch_id";i:1;s:7:"comment";s:0:"";s:17:"' . "\0" . '*' . "\0" . 'duty_start_int";i:32400;s:17:"' . "\0" . '*' . "\0" . 'duty_start_sql";s:5:"09:00";s:15:"' . "\0" . '*' . "\0" . 'duty_end_int";i:54000;s:15:"' . "\0" . '*' . "\0" . 'duty_end_sql";s:5:"15:00";s:18:"' . "\0" . '*' . "\0" . 'break_start_int";N;s:18:"' . "\0" . '*' . "\0" . 'break_start_sql";N;s:16:"' . "\0" . '*' . "\0" . 'break_end_int";N;s:16:"' . "\0" . '*' . "\0" . 'break_end_sql";N;s:13:"working_hours";d:6;s:14:"break_duration";i:0;s:13:"duty_duration";i:21600;s:15:"working_seconds";i:21600;s:7:"weekday";s:1:"4";}}i:5;a:1:{i:0;O:11:"roster_item":18:{s:8:"date_sql";s:10:"2018-11-23";s:9:"date_unix";i:1542927600;s:11:"employee_id";i:5;s:9:"branch_id";i:1;s:7:"comment";N;s:17:"' . "\0" . '*' . "\0" . 'duty_start_int";i:32400;s:17:"' . "\0" . '*' . "\0" . 'duty_start_sql";s:5:"09:00";s:15:"' . "\0" . '*' . "\0" . 'duty_end_int";i:63000;s:15:"' . "\0" . '*' . "\0" . 'duty_end_sql";s:5:"17:30";s:18:"' . "\0" . '*' . "\0" . 'break_start_int";i:45000;s:18:"' . "\0" . '*' . "\0" . 'break_start_sql";s:5:"12:30";s:16:"' . "\0" . '*' . "\0" . 'break_end_int";i:46800;s:16:"' . "\0" . '*' . "\0" . 'break_end_sql";s:5:"13:00";s:13:"working_hours";d:8;s:14:"break_duration";i:1800;s:13:"duty_duration";i:30600;s:15:"working_seconds";i:28800;s:7:"weekday";s:1:"5";}}}s:10:"profession";s:9:"Apotheker";}');
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
