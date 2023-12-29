<?php

namespace GatewayTests\Utility;

use \Gateway\Entities\AbstractEntity;
use \Gateway\Utility\Validator;

class ValidatorTest extends \GatewayTests\TestCase
{
    public function testOptional()
    {
        $this->assertTrue(Validator::isUrl("", true));
        $this->assertTrue(Validator::isAmount("", false, true));
        $this->assertTrue(Validator::isItemAmount("", true));
        $this->assertTrue(Validator::isEmail("", true));
        $this->assertTrue(Validator::isNum("", null, true));
        $this->assertTrue(Validator::isNumSpace("", true));
        $this->assertTrue(Validator::isAlphaSpace("", true));
        $this->assertTrue(Validator::isAlnumSpecial("", null, true));
        $this->assertTrue(Validator::isAlnumSpecial("", null, true));
    }

    public function testIsUrl()
    {
        $cases =  [
            ["http://subdomain.domain.tld",    true],
            ["https://subdomain.domain.tld",   true],
            ["http//subdomain.domain.tld",     false],
            ["https//subdomain.domain.tld",    false],
            ["subdomain.domain.tld",           false],
            ["//subdomain.domain.tld",         false],
        ];
        foreach ($cases as $specimen) {
            if ($specimen[1]) {
                $this->assertTrue(Validator::isUrl($specimen[0]));
                continue;
            }
            $this->shouldThrowException(function () use ($specimen) {
                Validator::isUrl($specimen[0]);
            });
        }
    }

    public function testIsAmount()
    {
        $this->assertTrue(Validator::isAmount('89.90'));

        $this->shouldThrowException(function () {
            Validator::isAmount('89.909000');
        });
    }

    public function testIsItemAmount()
    {
        $this->assertTrue(Validator::isItemAmount("90.90"));

        $this->shouldThrowException(function () {
            Validator::isItemAmount('89.909000');
        });
    }

    public function testIsEmail()
    {
        $cases =  [
            ["name@domain.com",         true],
            ["check.dot@domain.com",    true],
            ["name@domain.tld",         true],
            ["____@domain.com",         true],
            ["normalstring",            false],
            [".leadingdot@domain.com",  false],
            ["trailingdot.@domain.com", false],
            ["name@1.22.333.4444",      false],
            ["notld@domain",            false],
            ["aいuえo@nouni.code",      false],
        ];
        foreach ($cases as $specimen) {
            if ($specimen[1]) {
                $this->assertTrue(Validator::isEmail($specimen[0]));
                continue;
            }
            $this->shouldThrowException(function () use ($specimen) {
                Validator::isEmail($specimen[0]);
            });
        }
    }

    public function testIsNum()
    {
        $this->assertTrue(Validator::isNum(190, 3));
        $this->assertTrue(Validator::isNum("190", 3));

        $this->shouldThrowException(function () {
            Validator::isNum(12090, 3);
        });
        $this->shouldThrowException(function () {
            Validator::isNum("12090", 3);
        });
    }

    public function testIsNumSpace()
    {
        $this->assertTrue(Validator::isNumSpace("909 009 909 90"));

        $this->shouldThrowException(function () {
            $this->assertTrue(Validator::isNumSpace("909009909-90"));
        });
    }

    public function testIsAlphaSpace()
    {
        $this->assertTrue(Validator::isAlphaSpace("Words with spaces"));
        $this->shouldThrowException(function () {
            $this->assertTrue(Validator::isAlphaSpace("word with 123 and space"));
        });
    }

    public function testIsUniAlnumSpecial()
    {
        $this->assertTrue(Validator::isUniAlnumSpecial("120 abz *.\$_-:！�", 100));
        $this->shouldThrowException(function () {
            $this->assertTrue(Validator::isUniAlnumSpecial("() [] &", 50));
        });
    }

    public function testIsAllSpecial()
    {
        $this->assertTrue(Validator::isAllSpecial("120 abz *.\$_-: () [] ÀÁÿ", 100));
        $this->shouldThrowException(function () {
            $this->assertTrue(Validator::isAllSpecial("Ā�", 50));
        });
    }

    public function testIsReadable()
    {
        $this->assertTrue(Validator::isReadable("composer.json"));
        $this->shouldThrowException(function () {
            Validator::isReadable("F1l3 wh1ch sh0u1d n0t 3x157");
        });
    }

    public function testIsCurl()
    {
        $ch = curl_init();
        $this->assertTrue(Validator::isCurl($ch));

        curl_close($ch);
        $this->shouldThrowException(function () {
            $this->assertTrue(Validator::isCurl($ch));
        });
    }

    public function testIsValidEntity()
    {
        $stub = $this->getMockBuilder(AbstractEntity::class)
                     ->disableOriginalConstructor()
                     ->setMethods(array('validate'))
                     ->getMockForAbstractClass();
        $stub->method('validate')
            ->willReturn(false);
        $this->shouldThrowException(function () use ($stub) {
            Validator::isValidEntity($stub);
        });
    }
}
