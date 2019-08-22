<?php

/*
 * This file is part of the IntonationGridBundle.
 *
 * (c) Wolfgang Drescher <drescher.wolfgang@gmail.com>
 *
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 */

namespace Intonation\GridBundle\Utils;

use Doctrine\Common\Persistence\ManagerRegistry;
use Doctrine\Common\Persistence\Mapping\MappingException;
use Doctrine\Common\Persistence\Proxy;
use Doctrine\DBAL\Types\Type;
use Doctrine\ORM\Mapping\MappingException as LegacyMappingException;
use Intonation\GridBundle\Guess\Guess;
use Intonation\GridBundle\Guess\TypeGuess;
use Prezent\Grid\Extension\Core\Type\BooleanType;
use Prezent\Grid\Extension\Core\Type\CollectionType;
use Prezent\Grid\Extension\Core\Type\DateTimeType;
use Prezent\Grid\Extension\Core\Type\StringType;

class ElementTypeGuesser implements ElementTypeGuesserInterface
{
    protected $registry;

    private $cache = [];

    public function __construct(ManagerRegistry $registry)
    {
        $this->registry = $registry;
    }

    public function guessType($class, $property)
    {
        if (!$metadata = $this->getMetadata($class)) {
            return new TypeGuess(StringType::class, [], Guess::LOW_CONFIDENCE);
        }

        if ($metadata->hasAssociation($property)) {
            if($metadata->isCollectionValuedAssociation($property)) {
                return new TypeGuess(CollectionType::class, [], Guess::HIGH_CONFIDENCE);
            }
        }

        switch ($metadata->getTypeOfField($property)) {
            case Type::TARRAY:
            case Type::SIMPLE_ARRAY:
                return new TypeGuess(CollectionType::class, [], Guess::MEDIUM_CONFIDENCE);
            case Type::BOOLEAN:
                return new TypeGuess(BooleanType::class, [], Guess::HIGH_CONFIDENCE);
            case Type::DATETIME:
            case Type::DATE:
            case Type::TIME:
            case 'dateinterval':
                return new TypeGuess(DateTimeType::class, [], Guess::HIGH_CONFIDENCE);
            case Type::DECIMAL:
            case Type::FLOAT:
                return new TypeGuess(StringType::class, [/*'decimals' => null*/], Guess::MEDIUM_CONFIDENCE);
            case Type::INTEGER:
            case Type::BIGINT:
            case Type::SMALLINT:
                return new TypeGuess(StringType::class, [/*'decimals' => 0*/], Guess::MEDIUM_CONFIDENCE);
            case Type::STRING:
                return new TypeGuess(StringType::class, [], Guess::MEDIUM_CONFIDENCE);
            case Type::TEXT:
                return new TypeGuess(StringType::class, ['truncate' => 255], Guess::MEDIUM_CONFIDENCE);
            default:
                return new TypeGuess(StringType::class, [], Guess::LOW_CONFIDENCE);
        }
    }

    private function getMetadata($class)
    {
        // normalize class name
        $class = self::getRealClass(ltrim($class, '\\'));

        if (array_key_exists($class, $this->cache)) {
            return $this->cache[$class];
        }

        $this->cache[$class] = null;
        foreach ($this->registry->getManagers() as $name => $em) {
            try {
                return $this->cache[$class] = $em->getClassMetadata($class);
            } catch (MappingException $e) {
                // not an entity or mapped super class
            } catch (LegacyMappingException $e) {
                // not an entity or mapped super class, using Doctrine ORM 2.2
            }
        }
    }

    private static function getRealClass(string $class): string
    {
        if (false === $pos = mb_strrpos($class, '\\'.Proxy::MARKER.'\\')) {
            return $class;
        }

        return mb_substr($class, $pos + Proxy::MARKER_LENGTH + 2);
    }
}
