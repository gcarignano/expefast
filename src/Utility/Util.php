<?php
/**
 * Copyright 2023 Connfido BV. All rights reserved.
 * Distribution of this software without explicit written consent from Newgen is
 * strictly prohibited. No part of this software must not be reverse engineered,
 * copied, reproduced or modified.
 */

namespace Gateway\Utility;

/**
 * Utilities
 *
 * @abstract
 */
abstract class Util
{
    /**
     * Generates a random Globally Unique ID
     *
     * @return string
     */
    public static function generateGUID()
    {
        $data = openssl_random_pseudo_bytes(16);
        $data[6] = chr(ord($data[6]) & 0x0f | 0x40);
        $data[8] = chr(ord($data[8]) & 0x3f | 0x80);
        return strtoupper(vsprintf('%s%s-%s-%s-%s-%s%s%s', str_split(bin2hex($data), 4)));
    }

    /**
     * Prepends scheme to a URL if absent
     *
     * @param string $url
     * @return string
     */
    public static function prependSchema($url)
    {
        if (!empty($url)) {
            $matches = [];
            preg_match('/^(([a-z0-9]+)(:\/\/|:\/))?(.*)/', $url, $matches);
            $url = (empty($matches[2]) ? 'http' : $matches[2]). '://' . $matches[4];
        }
        return $url;
    }
}
