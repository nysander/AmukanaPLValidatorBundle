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

/**
 * http://mvc.emailer.pl/zend-framework/walidacja-nip
 * based on Zend_Validate_Nip
 */

namespace Amukana\PLValidatorBundle\Validator\Constraints;

use Symfony\Component\Validator\Constraint;
use Symfony\Component\Validator\ConstraintValidator;
use Symfony\Component\Validator\Exception\UnexpectedTypeException;

/**
 * @author Paweł Madej <pawel.madej@amukana.pl>
 */
class NipValidator extends ConstraintValidator
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

        $nip = preg_replace('/[ -]/im', '', $stringValue);
        $length = strlen($nip);

        if ($length != 10) {
            $this->context->addViolation(
                $constraint->message,
                array('%string%' => $value)
            );
            //$this->context->addViolationAt($subPath, $message, $params)
$this->context->addViolation( 'validation.nip',array('%string%' => $value));
//addViolationAtSubPath('endDate', 'validation.invalid.enddate', array(), null);
            return;
        }

        $mod = 11;
        $sum = 0;
        $weights = array (6, 5, 7, 2, 3, 4, 5, 6, 7);

        $digits = array();
        preg_match_all("/\d/", $nip, $digits) ;

        $digitsArray = $digits[0];

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
