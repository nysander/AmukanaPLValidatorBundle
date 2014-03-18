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

/**
 * @author Paweł Madej <pawel.madej@amukana.pl>
 */
class IbanValidator extends ConstraintValidator
{
    private $countries = array();

    public function __construct()
    {
        // source: http://en.wikipedia.org/wiki/International_Bank_Account_Number
        $this->countries = array('AL' => 28, 'AD' => 24, 'AT' => 20, 'BE' => 16,
                                 'BA' => 20, 'BG' => 22, 'HR' => 21, 'CY' => 28,
                                 'CZ' => 24, 'DK' => 18, 'DO' => 28, 'EE' => 20,
                                 'FO' => 18, 'FI' => 18, 'FR' => 27, 'GE' => 22,
                                 'PL' => 28, 'DE' => 22, 'GI' => 23, 'GR' => 27,
                                 'GL' => 18, 'HU' => 28, 'IS' => 26, 'IE' => 22,
                                 'IL' => 23, 'IT' => 27, 'KZ' => 20, 'KW' => 30,
                                 'LV' => 21, 'LB' => 28, 'LI' => 21, 'LT' => 20,
                                 'LU' => 20, 'MK' => 19, 'MT' => 31, 'MR' => 27,
                                 'MU' => 30, 'MC' => 27, 'ME' => 22, 'NL' => 18,
                                 'NO' => 15, 'PT' => 25, 'RO' => 24, 'SM' => 27,
                                 'SA' => 24, 'RS' => 22, 'SK' => 24, 'SI' => 19,
                                 'ES' => 24, 'SE' => 24, 'CH' => 21, 'TN' => 24,
                                 'TR' => 26, 'AE' => 23, 'GB' => 22);
    }

    /**
     * {@inheritDoc}
     */
    public function validate($value, Constraint $constraint)
    {
        $iban    = strtoupper(preg_replace('/[ .-]/im', '', $value));
        $length  = strlen($iban);
        $country = substr($iban,0,2);

        if (!array_key_exists($country,$this->countries) || $this->countries[$country] != $length) {
            $this->setMessage($constraint->message);

            return false;
        }

        $iban = substr($iban,4,$length).substr($iban,0,4);
        $iban = str_replace( array('A', 'B', 'C', 'D', 'E', 'F', 'G', 'H', 'I',
                                   'J', 'K', 'L', 'M', 'N', 'O', 'P', 'Q', 'R',
                                   'S', 'T', 'U', 'V', 'W', 'X', 'Y', 'Z'),
                             array( 10,  11,  12,  13,  14,  15,  16,  17,  18,
                                    19,  20,  21,  22,  23,  24,  25,  26,  27,
                                    28,  29,  30,  31,  32,  33,  34,  35),
                             $iban);

        if ((int) bcmod($iban,97) != 1) {
            $this->context->addViolation(
                $constraint->message,
                array('%string%' => $value)
            );
        }
    }
}
