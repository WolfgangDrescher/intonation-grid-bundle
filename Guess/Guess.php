<?php

/*
 * This file is part of the IntonationGridBundle.
 *
 * (c) Wolfgang Drescher <drescher.wolfgang@gmail.com>
 *
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 */

namespace Intonation\GridBundle\Guess;

use Intonation\GridBundle\Exception\InvalidArgumentException;

abstract class Guess
{
    /**
     * Marks an instance with a value that is extremely likely to be correct.
     */
    const VERY_HIGH_CONFIDENCE = 3;

    /**
     * Marks an instance with a value that is very likely to be correct.
     */
    const HIGH_CONFIDENCE = 2;

    /**
     * Marks an instance with a value that is likely to be correct.
     */
    const MEDIUM_CONFIDENCE = 1;

    /**
     * Marks an instance with a value that may be correct.
     */
    const LOW_CONFIDENCE = 0;

    /**
     * The confidence about the correctness of the value.
     *
     * One of VERY_HIGH_CONFIDENCE, HIGH_CONFIDENCE, MEDIUM_CONFIDENCE
     * and LOW_CONFIDENCE.
     *
     * @var int
     */
    private $confidence;

    /**
     * @param int $confidence The confidence
     *
     * @throws InvalidArgumentException if the given value of confidence is unknown
     */
    public function __construct(int $confidence)
    {
        if (self::VERY_HIGH_CONFIDENCE !== $confidence && self::HIGH_CONFIDENCE !== $confidence &&
            self::MEDIUM_CONFIDENCE !== $confidence && self::LOW_CONFIDENCE !== $confidence) {
            throw new InvalidArgumentException('The confidence should be one of the constants defined in Guess.');
        }

        $this->confidence = $confidence;
    }

    /**
     * Returns the confidence that the guessed value is correct.
     *
     * @return int One of the constants VERY_HIGH_CONFIDENCE, HIGH_CONFIDENCE,
     *             MEDIUM_CONFIDENCE and LOW_CONFIDENCE
     */
    public function getConfidence()
    {
        return $this->confidence;
    }
}
