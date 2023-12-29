<?php
/**
 * Copyright 2023 Connfido BV. All rights reserved.
 * Distribution of this software without explicit written consent from Newgen is
 * strictly prohibited. No part of this software must not be reverse engineered,
 * copied, reproduced or modified.
 */

namespace Gateway\Utility;

use Gateway\Entities\AbstractEntity;
use Gateway\Exceptions\ValidationException;
use Gateway\Exceptions\BadObject;
use Gateway\Exceptions\FileException;
use Gateway\Common\Messages;

/**
 * Validator
 *
 * @final
 */
final class Validator
{
    const URL_MAXLENGTH = 500;

    const AMOUNT_MAXLENGTH = 16;

    const ALLSPECIAL_MAXLENGTH = 255;

    public static function isUrl($url, $optional = false)
    {
        if (($optional && empty($url))
            || (
                ($res = parse_url($url))
                && isset($res['scheme'])
                && isset($res['host'])
                && strlen($url) <= self::URL_MAXLENGTH
            )
        ) {
            return true;
        }
        throw new ValidationException(Messages::URL, $url);
    }

    public static function isAmount($param, $zeroAuth = false, $optional = false)
    {
        if($zeroAuth) {
            if($param == 0) {
                return true;
            }
            throw new ValidationException(Messages::ZERO_AUTH_AMOUNT, $param);
        }

        if (($optional && empty($param))
            || (
                preg_match('/^[+]?[0-9]+\.?[0-9]{0,4}$/', $param)
                && strlen($param) <= self::AMOUNT_MAXLENGTH
            )
        ) {
            return true;
        }
        throw new ValidationException(Messages::AMOUNT, $param);
    }

    public static function isItemAmount($param, $optional = false)
    {
        if (($optional && empty($param))
            || (
                preg_match('/^[-+]?[0-9]+\.?[0-9]{0,4}$/', $param)
                && strlen($param) <= self::AMOUNT_MAXLENGTH
            )
        ) {
            return true;
        }
        throw new ValidationException(Messages::ITEM_AMOUNT, $param);
    }

    public static function isEmail($param, $optional = false)
    {
        if (($optional && empty($param))
            || filter_var($param, FILTER_VALIDATE_EMAIL)
        ) {
            return true;
        }
        throw new ValidationException(Messages::EMAIL, $param);
    }

    public static function isNum($param, $maxLength, $optional = false)
    {
        if (($optional && empty($param))
            || (
                preg_match('/^[0-9]+$/', $param)
                && strlen($param) <= $maxLength
            )
        ) {
            return true;
        }
        throw new ValidationException(Messages::NUMBER, $param);
    }

    public static function isNumSpace($param, $optional = false)
    {
        if (($optional && empty($param))
            || preg_match('/^[0-9\ ]+$/', $param)
        ) {
            return true;
        }
        throw new ValidationException(Messages::INVALID, $param);
    }

    public static function isAlphaSpace($param, $optional = false)
    {
        if (($optional && empty($param))
            || preg_match('/^[a-z\ A-Z_]+$/', $param)
        ) {
            return true;
        }
        throw new ValidationException(Messages::INVALID, $param);
    }

    public static function isAlnumSpecial($param, $maxLength, $optional = false)
    {
        if (($optional && empty($param))
            || (
                preg_match('/^[0-9a-zA-Z\s_\-:.,\'$*+|=\/]+$/', $param)
                && strlen($param) <= $maxLength
            )
        ) {
            return true;
        }
        throw new ValidationException(Messages::INVALID, $param);
    }

    public static function isUniAlnumSpecial($param, $maxLength, $optional = false)
    {
        if (($optional && empty($param))
            || (
                preg_match('/^[0-9a-zA-Z\s_\-:.,\'$*+|=\/\x{80}-\x{FFFF}]+$/u', $param)
                && strlen($param) <= $maxLength
            )
        ) {
            return true;
        }
        throw new ValidationException(Messages::INVALID, $param);
    }

    public static function isAllSpecial($param, $maxLength = self::ALLSPECIAL_MAXLENGTH, $optional = false)
    {
        if (($optional && empty($param))
            || (
                preg_match('/^[0-9a-zA-Z\s\*&.$_\-:\',(){}\[\]"\x{00C0}-\x{00ff}]+$/u', $param)
                && strlen($param) <= $maxLength
            )
        ) {
            return true;
        }
        throw new ValidationException(Messages::INVALID, $param);
    }

    public static function isValidEntity(AbstractEntity $entity)
    {
        if (! $entity->validate()) {
            throw new ValidationException(Messages::ENTITY, get_class($entity));
        }
        return true;
    }

    public static function isReadable($filePath)
    {
        if (is_file($filePath)
            && is_readable($filePath)
        ) {
            return true;
        }
        throw new FileException(Messages::UNREADABLE, $filePath);
    }

    public static function isCurl($curlInstance)
    {
        if ($curlInstance !== false
        ) {
            return true;
        }
        throw new BadObject(Messages::CURL);
    }

    public static function isIPAddress($param, $optional = false)
    {
        if (
            ($optional && empty($param))
            || filter_var($param, FILTER_VALIDATE_IP)
        ) {
            return true;
        }
        throw new ValidationException(Messages::INVALID_IP, $param);
    }

    public static function isDOB($param, $optional = false)
    {
        $dateTime = \DateTime::createFromFormat('Y-m-d', $param);
        if (
            ($optional && empty($param))
            || ($dateTime && $dateTime->format('Y-m-d') === $param)
        ) {
            return true;
        }
        throw new ValidationException(Messages::INVALID_DOB, $param);
    }
}
