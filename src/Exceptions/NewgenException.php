<?php
/**
 * Copyright 2023 Connfido BV. All rights reserved.
 * Distribution of this software without explicit written consent from Newgen is
 * strictly prohibited. No part of this software must not be reverse engineered,
 * copied, reproduced or modified.
 */

namespace Gateway\Exceptions;

use Exception;

/**
 * Base Exception class
 *
 * @see Exception
 */
class NewgenException extends Exception
{
    /**
     * @var string
     */
    private $mitigation;

    /**
     * @param string $message
     * @param string $mitigation
     * @param int $code
     * @param \Exception $previous
     */
    public function __construct($message, $mitigation = null, $code = 0, \Exception $previous = null)
    {
        parent::__construct($message, $code, $previous);
        $this->mitigation = $mitigation;
    }

    public function __toString()
    {
        return $this->getClassName() . ' [' . date("Y-m-d H:i:s T", time()) . '] > ' .
            $this->message .
            (is_scalar($this->mitigation) ? ': '. $this->mitigation : '');
    }

    /**
     * Gets class name
     *
     * @return string
     */
    protected function getClassName()
    {
        return get_called_class();
    }
}
