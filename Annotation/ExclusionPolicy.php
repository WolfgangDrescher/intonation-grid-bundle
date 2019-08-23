<?php

/*
 * This file is part of the IntonationGridBundle.
 *
 * (c) Wolfgang Drescher <drescher.wolfgang@gmail.com>
 *
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 */

namespace Intonation\GridBundle\Annotation;

use Intonation\GridBundle\Exception\RuntimeException;

/**
 * @Annotation
 * @Target("CLASS")
 */
final class ExclusionPolicy
{
    public const NONE = 'NONE';
    public const ALL = 'ALL';

    /**
     * @var string
     * @Enum({"NONE", "ALL"})
     */
    public $policy;

    public function __construct(array $values)
    {
        if (!\is_string($values['value'])) {
            throw new RuntimeException('"value" must be a string.');
        }

        $this->policy = mb_strtoupper($values['value']);

        if (self::NONE !== $this->policy && self::ALL !== $this->policy) {
            throw new RuntimeException('Exclusion policy must either be "ALL", or "NONE".');
        }
    }
}
