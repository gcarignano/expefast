<?php

namespace GatewayTests\Entities;

use \Gateway\Common\Fields;
use \Gateway\Common\Report;
use \Gateway\Common\Columns;
use \Gateway\Common\Currency;
use \Gateway\Common\SortOrder;
use \Gateway\Entities\ReportSetting;

class ReportSettingTest extends \GatewayTests\TestCase
{
    public function testConstructor()
    {
        $report = new ReportSetting();
        $this->assertTrue($report->validate());
        $this->assertIsArray($report->getArray());

        foreach ([
            Fields::LOCALE => 'UTC',
            Fields::REPORTTYPE => Report::$TRANSACTION->getValue(),
            Fields::SORTORDER => SortOrder::$ASC->getValue(),
            Fields::SORTCOLUMN => Columns::$TXN_DATE->getValue()
        ] as $k => $v) {
            $this->assertEquals($v, $report->getArray()[$k]);
        }
        $this->assertFalse($report->getArray()[Fields::SHOW_CUSTOM_DATA]);
        $this->assertFalse(isset($report->getArray()[Fields::LIMIT]));
        $this->assertFalse(isset($report->getArray()[Fields::FROM_COUNT]));
    }

    public function testPaginate()
    {
        $report = new ReportSetting();
        $report->paginate(10, 2);
        $this->assertTrue($report->validate());
        $this->assertIsArray($report->getArray());
        $this->assertEquals(10, $report->getArray()[Fields::LIMIT]);
        $this->assertEquals(10, $report->getArray()[Fields::FROM_COUNT]);
    }

    public function testSetTimeZone()
    {
        $report = new ReportSetting();
        foreach (['IST', 'ist', 'est', 'pst', 'gmt'] as $v) {
            $report->setTimeZone(new \DateTimeZone($v));
            $this->assertIsArray($report->getArray());
            $this->assertTrue($report->validate());
        }
    }

    public function testAddFilter()
    {
        $report = new ReportSetting();
        $report->addFilter(Columns::$STATUS, 'value');
        $this->assertTrue($report->validate());
        $this->assertEquals(1, count($report->getArray()[Fields::FILTER]));

        foreach ([
            Fields::FILTER_FIELD => Columns::$STATUS->getValue(),
            Fields::FILTER_VALUE => 'value'
        ] as $k => $v) {
            $this->assertEquals($v, $report->getArray()[Fields::FILTER][0][$k]);
        }

        $report->addFilter(Columns::$STATUS, 'override, not duplicate');
        $this->assertTrue($report->validate());
        $this->assertIsArray($report->getArray());
        $this->assertEquals(1, count($report->getArray()[Fields::FILTER]));
        foreach ([
            Fields::FILTER_FIELD => Columns::$STATUS->getValue(),
            Fields::FILTER_VALUE => 'override, not duplicate'
        ] as $k => $v) {
            $this->assertEquals($v, $report->getArray()[Fields::FILTER][0][$k]);
        }

        $report->addFilter(Columns::$CURRENCY, Currency::$EUR);
        $this->assertTrue($report->validate());
        $this->assertIsArray($report->getArray());
        $this->assertEquals(2, count($report->getArray()[Fields::FILTER]));

        foreach ([
            Fields::FILTER_FIELD => Columns::$CURRENCY->getValue(),
            Fields::FILTER_VALUE => Currency::$EUR->getValue()
        ] as $k => $v) {
            $this->assertEquals($v, $report->getArray()[Fields::FILTER][1][$k]);
        }
    }
}
