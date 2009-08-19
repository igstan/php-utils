<?php

class NumeralBuilder
{
    /**
     * @var array
     */
    private $steps = array(
        10         => 'units',
        100        => 'tens',
        1000       => 'hundreds',
        1000000    => 'thousands',
        1000000000 => 'millions',
    );

    /**
     * @param  integer $amount
     * @return string
     */
    public function toNumeral($amount)
    {
        return $this->convertRecursive($amount);
    }

    /**
     * @param  integer $amount
     * @param  boolean $compound
     * @return string
     */
    protected function convertRecursive($amount, $compound = false)
    {
        $amount = $this->removeLeadingZeros($amount);

        foreach ($this->steps as $threshold => $callback) {
            if ($amount < $threshold) {
                return $this->$callback($amount, $compound);
            }
        }
    }

    /**
     * @param  integer $amount
     * @param  boolean $compound
     * @return string
     */
    protected function units($amount, $compound)
    {
        if ($compound) {
            $numberals = $this->makeNumberals('', 'UNU', 'DOI', 'SASE');
        } else {
            $numberals = $this->makeNumberals('ZERO', 'UN', 'DOI', 'SASE');
        }

        return $numberals[$amount];
    }

    /**
     * @param  integer $amount
     * @return string
     */
    protected function tens($amount)
    {
        if ($amount == 10) {
            return 'ZECE';
        }

        if ($amount < 20) {
            return $this->specialTens($amount);
        }

        return $this->normalTens($amount);
    }

    /**
     * @param  integer $amount
     * @return string
     */
    protected function specialTens($amount)
    {
        $numberals = $this->makeNumberals('', 'UN', 'DOI', 'SAI');
        return $numberals[$amount - 10] . 'SPREZECE';
    }

    /**
     * @param  integer $amount
     * @return string
     */
    protected function normalTens($amount)
    {
        $numberals = $this->makeNumberals('', '', 'DOUA', 'SAI');

        if ($amount%10) {
            $rest = ' SI ' . $this->convertRecursive($amount%10, true);
        } else {
            $rest = '';
        }

        return $numberals[$amount/10] . 'ZECI' . $rest;
    }

    /**
     * @param  integer $amount
     * @return string
     */
    protected function hundreds($amount)
    {
        if ($amount%100) {
            $rest = ' ' . $this->convertRecursive($amount%100, true);
        } else {
            $rest = '';
        }

        if ($amount < 200) {
            return 'O SUTA' . $rest;
        }

        $numberals = $this->makeNumberals('', '', 'DOUA', 'SASE');
        return $numberals[$amount/100] . ' SUTE' . $rest;
    }

    /**
     * @param  integer $amount
     * @return string
     */
    protected function thousands($amount)
    {
        if (1000 <= $amount && $amount < 10000) {
            if ($amount == 1000) {
                return 'O MIE';
            }

            $numberals = $this->makeNumberals('', '', 'DOUA', 'SASE');

            if ($amount%1000) {
                $rest = ' ' . $this->convertRecursive($amount%1000, true);
            } else {
                $rest = '';
            }

            return $numberals[$amount/1000] . ' MII' . $rest;
        } else {
            $first = $this->convertRecursive(substr($amount, 0, -3));

            if (1<$amount%100000 && $amount%100000<20000) {
                $after = ' MII';
            } else {
                $after = ' DE MII';
            }

            $rest = $this->convertRecursive(substr($amount, -3), true);

            if ($rest) {
                $after .= ", $rest";
            }

            return $first . $after;
        }
    }

    /**
     * @param  integer $amount
     * @return string
     */
    protected function millions($amount)
    {
        if (1000000 <= $amount && $amount < 100000000) {
            if ($amount == 1000000) {
                return 'UN MILION';
            }

            $numberals = $this->makeNumberals('', '', 'DOUA', 'SASE');

            if ($amount%1000000) {
                $rest = ' ' . $this->convertRecursive($amount%1000000, true);
            } else {
                $rest = '';
            }

            return $numberals[$amount/1000000] . ' MILIOANE' . $rest;
        } else {
            $first = $this->convertRecursive(substr($amount, 0, -6));

            if (1<$amount%100000 && $amount%100000<20000) {
                $after = ' MILIOANE';
            } else {
                $after = ' DE MILIOANE';
            }

            $rest = $this->convertRecursive(substr($amount, -6), true);

            if ($rest) {
                $after .= ", $rest";
            }

            return $first . $after;
        }
    }

    /**
     * @param  string $amount
     * @return integer
     */
    protected function removeLeadingZeros($amount)
    {
        return intval($amount);
    }

    /**
     * @param  string $zero
     * @param  string $one
     * @param  string $two
     * @param  string $six
     * @return array
     */
    protected function makeNumberals($zero, $one, $two, $six)
    {
        return array(
            $zero, $one, $two, 'TREI', 'PATRU', 'CINCI', $six, 'SAPTE', 'OPT', 'NOUA',
        );
    }
}

/**
 * @return NumeralBuilder
 */
function NumeralBuilder() {
    return new NumeralBuilder;
}
