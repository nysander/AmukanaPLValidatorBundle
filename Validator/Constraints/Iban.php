<?php

/*
 * This file is part of the AmukanaPLValidatorBundle.
 *
 * (c) Paweł Madej <pawel.madej@amukana.pl>
 *
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 */

/*
 * @todo write Unit tests
 */

namespace Amukana\PLValidatorBundle\Validator\Constraints;

use Symfony\Component\Validator\Constraint;

/**
 * @Annotation
 *
 * @author Paweł Madej <pawel.madej@amukana.pl>
 */
class Iban extends Constraint
{
    public $message = 'To nie jest poprawny numer rachunku IBAN';

    public function validatedBy()
    {
        return get_class($this).'Validator';
    }

    public function getTargets()
    {
        return self::PROPERTY_CONSTRAINT;
    }
}
