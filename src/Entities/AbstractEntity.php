<?php
/**
 * Copyright 2023 Connfido BV. All rights reserved.
 * Distribution of this software without explicit written consent from Newgen is
 * strictly prohibited. No part of this software must not be reverse engineered,
 * copied, reproduced or modified.
 */

namespace Gateway\Entities;

use Gateway\Utility\Validator;

/**
 * Payload validation and generation
 *
 * @abstract
 */
abstract class AbstractEntity
{

    /**
     * Member variables required for generating payload
     *
     * @var array
     */
    protected $required = [];

    /**
     * ID of the entity
     *
     * @var string
     */
    protected $id;

    /**
     * @param string $id
     */
    public function __construct($id)
    {
        $this->setId($id);
    }

    /**
     * Getter for entity ID
     *
     * @return string
     */
    public function getId()
    {
        return $this->id;
    }

    /**
     * Setter for entity ID
     *
     * @param string $id
     * @return \Gateway\Entities\AbstractEntity
     */
    public function setId($id)
    {
        $this->id = $id;
        return $this;
    }

    /**
     * Returns payload
     *
     * @abstract
     * @return array
     */
    abstract public function getArray();

    /**
     * Validates required fields by recursively invoking validate if the required
     * member variable is an instance of AbstractEntity
     *
     * @throws \Gateway\Exceptions\NewgenException
     * @return boolean
     */
    public function validate()
    {
        if (! empty($this->required)) {
            foreach ($this->required as $property) {

                $condition = !property_exists($this, $property) || ($this->{$property} instanceof AbstractEntity);
                if(!$condition && isset($this->zeroAuth) && $this->zeroAuth) {
                    return true;
                }

                if (
                    !property_exists($this, $property)
                    || (
                        ($this->{$property} instanceof AbstractEntity)
                        ? !Validator::isValidEntity($this->{$property})
                        : empty($this->{$property})
                    )
                ) {
                    return false;
                }
            }
        }
        return true;
    }

    /**
     * Returns member variables required by the entity
     *
     * @return array
     */
    public function requiredFields()
    {
        return $this->required;
    }
}
