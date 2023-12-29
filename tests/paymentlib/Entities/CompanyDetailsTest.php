<?php

namespace GatewayTests\Entities;

use \Gateway\Common\Fields;
use \Gateway\Common\Currency;
use \Gateway\Entities\CompanyDetails;

class CompanyDetailsTest extends \GatewayTests\TestCase
{
    public function testConstructor()
    {
        $cd = new CompanyDetails('regid', 'country', Currency::$EUR);
        $this->assertTrue($cd->validate());

        foreach ([
            Fields::CURRENCYCODE => Currency::$EUR->getValue(),
            Fields::COUNTRY => 'country',
            Fields::COMPANY_ID => 'regid'
        ] as $k => $v) {
            $this->assertEquals($v, $cd->getArray()[$k]);
        }

        $cd = new CompanyDetails('regid', 'country', Currency::$EUR, 100.95);
        $this->assertTrue($cd->validate());
        $this->assertEquals(100.95, $cd->getArray()[Fields::COMPANY_AMOUNT]);
    }
}
