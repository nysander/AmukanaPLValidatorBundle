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
use Symfony\Component\Validator\ConstraintValidator;
use Symfony\Component\Validator\Exception\UnexpectedTypeException;

/**
 * @author Paweł Madej <pawel.madej@amukana.pl>
 */
class RegonValidator extends ConstraintValidator
{
    /**
     * {@inheritDoc}
     */
    public function validate($value, Constraint $constraint)
    {
        if (null === $value || '' === $value) {
            return;
        }

        if (!is_scalar($value)) {
            throw new UnexpectedTypeException($value, 'string');
        }

        $stringValue = (string) $value;

        $regon = preg_replace('/[ -]/im', '', $stringValue);
        $length = strlen($regon);

        if ($length != 7 && $length != 9 && $length != 14) {
            $this->context->addViolation(
                $constraint->message,
                array('%string%' => $value)
            );

            return;
        }

        $mod = 11;
        $sum = 0;
        $weights[7] = array (2, 3, 4, 5, 6, 7);
        $weights[9] = array (8, 9, 2, 3, 4, 5, 6, 7);
        $weights[14] = array (2, 4, 8, 5, 0, 9, 7, 3, 6, 1, 2, 4, 8);

        $digits = array();
        preg_match_all("/\d/", $regon, $digits) ;

        $digitsArray = $digits[0];
        $weights = $weights[$length];

        foreach ($digitsArray as $digit) {
            $weight = current($weights);
            $sum += $digit * $weight;
            next($weights);
        }

        if ( ( ($sum % $mod == 10) ? 0 : $sum % $mod) != $digitsArray[$length - 1] ) {
            $this->context->addViolation(
                $constraint->message,
                array('%string%' => $value)
            );

        }
    }
}
