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
class IdentityCardValidator extends ConstraintValidator
{
    private $weigthMap = array();

    public function __construct()
    {
        $this->weigthMap = array('A' => 10, 'B' => 11, 'C' => 12, 'D' => 13,
                                 'E' => 14, 'F' => 15, 'G' => 16, 'H' => 17,
                                 'I' => 18, 'J' => 19, 'K' => 20, 'L' => 21,
                                 'M' => 22, 'N' => 23, 'O' => 24, 'P' => 25,
                                 'Q' => 26, 'R' => 27, 'S' => 28, 'T' => 29,
                                 'U' => 30, 'V' => 31, 'W' => 32, 'X' => 33,
                                 'Y' => 34, 'Z' => 35);
    }

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

        $identityCard = strtoupper(preg_replace('/[ .-]/im', '', $stringValue));
        $length = strlen($identityCard);

        if ($length != 9) {
            $this->context->addViolation(
                $constraint->message,
                array('%string%' => $value)
            );

            return;
        }

        $idCardArray = str_split($identityCard);

        $mod = 10;
        $sum = 0;

        // weight for 4th char is 0 as it is checksum
        $weights = array (7, 3, 1, 0, 7, 3, 1, 7, 3);

        foreach ($idCardArray as $char) {
            $weight = current($weights);
            if (key_exists($char, $this->weigthMap)) {
                $sum += $this->weigthMap[$char] * $weight;
            } else {
                $sum += $char * $weight;
            }
            next($weights);
        }

        if ( ( ($sum % $mod == 10) ? 0 : $sum % $mod) != substr($identityCard, 3, 1) ) {
            $this->context->addViolation(
                $constraint->message,
                array('%string%' => $value)
            );
        }
    }
}
/*
Jako ciekawostkę można podać, że w samym numerze i serii dowodu osobistego zastosowano cyfrę kontrolną. Dla zmyłki cyfra kontrolna nie jest na końcu numeru, ale na początku. Seria dowodu też wchodzi do obliczenia cyfry kontrolnej w taki sam sposób jak określa to norma ISO/IEC 7501-1:1997.

Literom można przypisać liczby:

 A  B  C  D  E  F  G  H  I  J  K  L  M  N  O  P  Q  R  S  T  U  V  W  X  Y  Z
10 11 12 13 14 15 16 17 18 19 20 21 22 23 24 25 26 27 28 29 30 31 32 33 34 35
Przykład obliczenia dla serii ABS i numeru 123456

Dane:      A    B    S   1   2   3   4   5   6
Wartości: 10   11   28  [1]  2   3   4   5   6
Wagi:      7    3    1       7   3   1   7   3
Iloczyny: 70   33   28      14   9   4  35  18
Suma:     70 + 33 + 28 +   +14 + 9 + 4 +35 +18 = 211

Reszta z dzielenia  211 MOD 10 = 1
Jeśli reszta z dzielenia zgadza się z pierwszą cyfrą numeru to seria i numer są formalnie poprawne.
*/
