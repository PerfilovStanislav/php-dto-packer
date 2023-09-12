<?php
declare(strict_types=1);

namespace DtoPacker;

abstract class AbstractDto implements PackableInterface, \JsonSerializable, \Stringable, \ArrayAccess
{
    protected const
        CACHE_DIR = "/cache";

    protected const
        HANDLERS_FROM           = 1,
        HANDLERS_VECTORS_FROM   = 2,
        HANDLERS_TO_ARRAY       = 3,
        CACHE = [
            self::HANDLERS_FROM         => [],
            self::HANDLERS_VECTORS_FROM => [],
            self::HANDLERS_TO_ARRAY     => [],
        ];

    private const
        TYPE_INT                    = 'int',
        TYPE_STRING                 = 'string',
        TYPE_FLOAT                  = 'float',
        TYPE_BOOL                   = 'bool',
        TYPE_ARRAY                  = 'array',
        TYPE_MIXED                  = 'mixed',
        TYPE_OBJECT                 = 'object',
        TYPE_NULL                   = 'null',
        TYPE_DATETIME               = 'DateTime',
        TYPE_DATETIME_IMMUTABLE     = 'DateTimeImmutable',
        TYPE_DATETIME_INTERFACE     = 'DateTimeInterface';

    protected const IS_SCALAR_TYPES =  [
        self::TYPE_INT    => true,
        self::TYPE_STRING => true,
        self::TYPE_FLOAT  => true,
        self::TYPE_BOOL   => true,
        self::TYPE_ARRAY  => true,
        self::TYPE_MIXED  => true,
    ];

    protected const DATETIME_CLASSES =  [
        self::TYPE_DATETIME             => \DateTime::class,
        self::TYPE_DATETIME_IMMUTABLE   => \DateTimeImmutable::class,
        self::TYPE_DATETIME_INTERFACE   => \DateTimeImmutable::class,
    ];

    private static array $_cache = [];

    public function __construct(string|array $data)
    {
        self::$_cache[static::class] ??= $this->loadProperties();

        if (\is_string($data)) {
            $data = \json_decode($data, true);
        }
        $this->fromArray($data);
    }

    public function toArray(): array
    {
        $result = [];
        $types = self::$_cache[static::class][self::HANDLERS_TO_ARRAY];

        $vars = \get_object_vars($this);
        foreach ($vars as $key => $val) {
            if ($val === null) {
                $result[$key] = null;
            } else {
                $this->{$types[$key]}($result[$key], $val);
            }
        }

        return $result;
    }

    public function fromArray(array $data): static
    {
        $types = self::$_cache[static::class];

        foreach ($types[self::HANDLERS_FROM] as $key => list($fn, $arg)) {
            if (\array_key_exists($key, $data)) {
                self::$fn($data[$key], $key, $arg);
            }
        }

        foreach ($types[self::HANDLERS_VECTORS_FROM] as $key => list($fn, $arg)) {
            if (($data[$key] ?? null) !== null) {
                $this->{$key} = [];
                self::$fn($data[$key], $this->{$key}, $arg);
            } else if (\array_key_exists($key, $data)) {
                $this->{$key} = null;
            }
        }

        return $this;
    }

    /** @return string[] */
    public function initialized(): array
    {
        return \array_keys(\get_object_vars($this));
    }

    public function has(string $key): bool
    {
        return \array_key_exists($key, \get_object_vars($this));
    }

    public function jsonSerialize(): array
    {
        return $this->toArray();
    }

    public function __serialize(): array
    {
        return $this->toArray();
    }

    public function __unserialize(array $data): void
    {
        $this->fromArray($data);
    }

    public function __toString(): string
    {
        return \json_encode($this->toArray(), JSON_UNESCAPED_UNICODE);
    }

    public function __set(string $name, $value): void
    {
        $this->fromArray([$name => $value]);
    }

    public function &__get(string $name)
    {
        return $this->{$name};
    }

    public function __isset(string $name): bool
    {
        return isset($this->{$name});
    }

    public function __unset(string $name): void
    {
        unset($this->{$name});
    }

    public function offsetExists(mixed $offset): bool
    {
        return isset($this->{$offset});
    }

    public function offsetGet(mixed $offset): mixed
    {
        return $this->{$offset};
    }

    public function offsetSet(mixed $offset, mixed $value): void
    {
        $this->fromArray([$offset => $value]);
    }

    public function offsetUnset(mixed $offset): void
    {
        unset($this->{$offset});
    }

    public function __clone(): void
    {
        static $initializedBy;
        $initializedBy ??= static::class;
        static $objects = [];

        foreach (\get_object_vars($this) as $key => $value) {
            if (\is_object($value) && ($value instanceof \UnitEnum) === false) {
                $this->{$key} = $objects[\spl_object_id($value)] ??= clone $value;
            } else if (\is_array($value)) {
                $this->clone($this->{$key}, $value, $objects);
            }
        }

        if ($initializedBy === static::class) {
            unset($initializedBy);
            $objects = [];
        }
    }

    protected function fromScalar(mixed $value, string $key)
    {
        $this->{$key} = $value;
    }

    protected function fromObject(mixed $value, string $key)
    {
        if (\is_object($value) || $value === null) {
            $this->{$key} = $value;
        } else {
            $this->{$key} = (object)$value;
        }
    }

    protected function fromDatetime(mixed $value, string $key, string $class)
    {
        if (\is_object($value) || $value === null) {
            $this->{$key} = $value;
        } else {
            /** @var \DateTimeInterface $class */
            $this->{$key} = $class::createFromFormat(\DateTimeInterface::ATOM, $value);
        }
    }

    protected function fromDto(mixed $value, string $key, string $class)
    {
        if ($value instanceof $class || $value === null) {
            $this->{$key} = $value;
        } else {
            $this->{$key} = new $class($value);
        }
    }

    protected function fromBackedEnum(mixed $value, string $key, string $class)
    {
        if (\is_object($value) || $value === null) {
            $this->{$key} = $value;
        } else {
            /** @var \BackedEnum $class */
            $this->{$key} = $class::from($value);
        }
    }

    protected function fromUnitEnum(mixed $value, string $key, string $class)
    {
        if (\is_object($value) || $value === null) {
            $this->{$key} = $value;
        } else {
            $this->{$key} = \constant("$class::$value");
        }
    }

    protected function fromDtos(array $data, ?array &$link, string $class)
    {
        foreach ($data as $i => $value) {
            if ($value instanceof PackableInterface || empty($value)) {
                $link[] = $value;
            } else if (\array_is_list($value)) {
                $this->fromDtos($value, $link[$i], $class);
            } else {
                $link[] = new $class($value);
            }
        }
    }

    protected function fromBackedEnums(array $data, ?array &$link, string $class)
    {
        foreach ($data as $i => $value) {
            /** @var \BackedEnum $class */
            if (\is_object($value)) {
                $link[] = $class::from($value->value);
            } else if (\is_scalar($value)) {
                $link[] = $class::from($value);
            } else {
                $this->fromBackedEnums($value, $link[$i], $class);
            }
        }
    }

    protected function fromUnitEnums(array $data, ?array &$link, string $class)
    {
        foreach ($data as $i => $value) {
            if ($value instanceof $class) {
                $link[] = $value;
            } else if (\is_scalar($value)) {
                $link[] = \constant("$class::$value");
            } else {
                $this->fromUnitEnums($value, $link[$i], $class);
            }
        }
    }

    protected function fromScalars(array $data, ?array &$link, $fn)
    {
        foreach ($data as $i => $value) {
            if (\is_scalar($value)) {
                $link = self::$fn(...$data);
                return;
            } else {
                $this->fromScalars($value, $link[$i], $fn);
            }
        }
    }

    protected function fromObjects(array $data, ?array &$link)
    {
        foreach ($data as $i => $value) {
            if (\is_object($value)) {
                $link[] = $value;
            } else if (\array_is_list($value)) {
                $this->fromObjects($value, $link[$i]);
            } else {
                $link[] = (object)$value;
            }
        }
    }

    protected function fromDatetimes(array $data, ?array &$link, string $class)
    {
        foreach ($data as $i => $value) {
            if ($value instanceof \DateTimeInterface) {
                $link[] = $value;
            } else if (\is_scalar($value)) {
                /** @var \DateTimeInterface $class */
                $link[] = $class::createFromFormat(\DateTimeInterface::ATOM, $value);
            } else {
                $this->fromDatetimes($value, $link[$i], $class);
            }
        }
    }

    protected function toScalar(?array &$link, mixed $value)
    {
        return $link = $value;
    }

    protected function toBackedEnum(?array &$link, \BackedEnum $value): int|string
    {
        return $link = $value->value;
    }

    protected function toUnitEnum(?array &$link, mixed $value)
    {
        return $link = $value->name;
    }

    protected function toDto(?array &$link, mixed $value)
    {
        return $link = $value->toArray();
    }

    protected function toDatetime(?array &$link, \DateTimeInterface $value)
    {
        return $link = $value->format(\DateTimeInterface::ATOM);
    }

    protected function toObject(?array &$link, Object $value)
    {
        return $link = (array)$value;
    }

    protected function toDtos(?array &$link, array $data)
    {
        if (empty($data)) {
            $link = [];
            return;
        }
        foreach ($data as $i => $value) {
            if ($value instanceof PackableInterface) {
                $link[$i] = $value->toArray();
            } else {
                $this->toDtos($link[$i], $value);
            }
        }
    }

    protected function toBackedEnums(?array &$link, array $data)
    {
        foreach ($data as $i => $value) {
            if ($value instanceof \BackedEnum) {
                $link[$i] = $value->value;
            } else {
                $this->toBackedEnums($link[$i], $value);
            }
        }
    }

    protected function toUnitEnums(?array &$link, array $data)
    {
        foreach ($data as $i => $value) {
            if ($value instanceof \UnitEnum) {
                $link[$i] = $value->name;
            } else {
                $this->toUnitEnums($link[$i], $value);
            }
        }
    }

    protected function toObjects(?array &$link, array $data)
    {
        foreach ($data as $i => $value) {
            if (\is_object($value)) {
                $link[$i] = (array)$value;
            } else {
                $this->toObjects($link[$i], $value);
            }
        }
    }

    protected function toDatetimes(?array &$link, array $data)
    {
        foreach ($data as $i => $value) {
            if ($value instanceof \DateTimeInterface) {
                $link[$i] = $value->format(\DateTimeInterface::ATOM);
            } else {
                $this->toDatetimes($link[$i], $value);
            }
        }
    }

    protected function ints(int ...$v) { return $v; }

    protected function strings(string ...$v) { return $v; }

    protected function bools(bool ...$v) { return $v; }

    protected function floats(float ...$v) { return $v; }

    protected function loadProperties()
    {
        $cacheDir = ($cwd = \getcwd()) . self::CACHE_DIR;

        $absoluteFilename = (new \ReflectionClass($this))->getFileName();
        $filemtime = \filemtime($absoluteFilename);
        $cacheFilename = "$cacheDir/$filemtime" . \substr($absoluteFilename, \strlen($cwd));

        if (\is_file($cacheFilename)) {
            return require_once $cacheFilename;
        } elseif (\is_dir($cacheDir) === false) {
            \mkdir($cacheDir, 0755, true);
            \file_put_contents("$cacheDir/.gitignore", "*\n", FILE_APPEND);
        }

        $properties = $this->getProperties();

        $props = "";
        foreach ($properties as $i => $vars) {
            $val = "";
            foreach ($vars as $key => $var) {
                $val .= "\n\t\t";
                if (\is_array($var)) {
                    $val .= "\"$key\" => [\"$var[0]\", " . ($var[1] === null ? "null" : "\"$var[1]\"") . "],";
                } else {
                    $val .= "\"$key\" => \"$var\",";
                }
            }
            $props .= "\n\t$i => [$val\n\t],";
        }
        $template = "<?php \nreturn [$props\n];";

        $dir = \dirname($cacheFilename);
        if (\is_dir($dir) === false) {
            \mkdir(\dirname($cacheFilename), 0755, true);
        }
        \file_put_contents($cacheFilename, $template, FILE_APPEND | LOCK_EX | FILE_NO_DEFAULT_CONTEXT);

        return $properties;
    }

    protected function getProperties(): array
    {
        $_cache = self::CACHE;

        $class = new \ReflectionClass($this);
        $properties = $class->getProperties(\ReflectionProperty::IS_PUBLIC|\ReflectionProperty::IS_PROTECTED);

        foreach ($properties as $property) {
            $reflectionType = $property->getType() ?? new \stdClass();
            $key = $property->name;
            if ($reflectionType instanceof \ReflectionNamedType) {
                $type = $reflectionType->getName();
                if (self::IS_SCALAR_TYPES[$type] ?? false) {
                    $_cache[self::HANDLERS_FROM][$key] = ["fromScalar", null];                                          /** @see fromScalar */
                    $_cache[self::HANDLERS_TO_ARRAY][$key] = "toScalar";                                                /** @see toScalar */
                } else if ($type === self::TYPE_OBJECT) {
                    $_cache[self::HANDLERS_FROM][$key] = ["fromObject", null];                                          /** @see fromObject */
                    $_cache[self::HANDLERS_TO_ARRAY][$key] = "toObject";                                                /** @see toObject */
                } else if (($dt = self::DATETIME_CLASSES[$type] ?? false) !== false) {
                    $_cache[self::HANDLERS_FROM][$key] = ["fromDatetime", $dt];                                         /** @see fromDatetime */
                    $_cache[self::HANDLERS_TO_ARRAY][$key] = "toDatetime";                                              /** @see toDatetime */
                } else {
                    if ($property->isProtected() === false) {
                        throw new \RuntimeException(static::class . "::$key must be protected");
                    }
                    $variableType = new \ReflectionClass($type);
                    if ($variableType->implementsInterface(PackableInterface::class)) {
                        $_cache[self::HANDLERS_FROM][$key] = ["fromDto", $type];                                        /** @see fromDto */
                        $_cache[self::HANDLERS_TO_ARRAY][$key] = "toDto";                                               /** @see toDto */
                    } else if ($variableType->implementsInterface(\BackedEnum::class)) {
                        $_cache[self::HANDLERS_FROM][$key] = ["fromBackedEnum", $type];                                 /** @see fromBackedEnum */
                        $_cache[self::HANDLERS_TO_ARRAY][$key] = "toBackedEnum";                                        /** @see toBackedEnum */
                    }  else if ($variableType->implementsInterface(\UnitEnum::class)) {
                        $_cache[self::HANDLERS_FROM][$key] = ["fromUnitEnum", $type];                                   /** @see fromUnitEnum */
                        $_cache[self::HANDLERS_TO_ARRAY][$key] = "toUnitEnum";                                          /** @see toUnitEnum */
                    }
                }
            } else if ($reflectionType instanceof \ReflectionUnionType) {
                $reflectionTypes = $reflectionType->getTypes();
                $isVector = false;
                $type = null;
                foreach ($reflectionTypes as $reflectionType) {
                    $tp = $reflectionType->getName();
                    if ($tp === self::TYPE_NULL) {
                        //
                    } else if ($tp === self::TYPE_ARRAY) {
                        $isVector = true;
                    } else {
                        $type = $tp;
                    }
                }
                if ($type === null || $isVector === false) {
                    throw new \RuntimeException(static::class . "::$key unhandled type");
                }
                if ($property->isProtected() === false) {
                    throw new \RuntimeException(static::class . "::$key is arrayable and must be protected");
                }

                if (self::IS_SCALAR_TYPES[$type] ?? false) {
                    $_cache[self::HANDLERS_VECTORS_FROM][$key] = ["fromScalars", "{$type}s"];                           /** @see fromScalars *//** @see ints *//** @see strings *//** @see bools *//** @see floats */
                    $_cache[self::HANDLERS_TO_ARRAY][$key] = "toScalar";                                                /** @see toScalar */
                } else if ($type === self::TYPE_OBJECT) {
                    $_cache[self::HANDLERS_VECTORS_FROM][$key] = ["fromObjects", null];                                 /** @see fromObjects */
                    $_cache[self::HANDLERS_TO_ARRAY][$key] = "toObjects";                                               /** @see toObjects */
                } else if (($dt = self::DATETIME_CLASSES[$type] ?? false) !== false) {
                    $_cache[self::HANDLERS_VECTORS_FROM][$key] = ["fromDatetimes", $dt];                                /** @see fromDatetimes */
                    $_cache[self::HANDLERS_TO_ARRAY][$key] = "toDatetimes";                                             /** @see toDatetimes */
                } else {
                    $variableType = new \ReflectionClass($type);
                    if ($variableType->implementsInterface(PackableInterface::class)) {
                        $_cache[self::HANDLERS_VECTORS_FROM][$key] = ["fromDtos", $type];                               /** @see fromDtos */
                        $_cache[self::HANDLERS_TO_ARRAY][$key] = "toDtos";                                              /** @see toDtos */
                    } else if ($variableType->implementsInterface(\UnitEnum::class)) {
                        if ($variableType->implementsInterface(\BackedEnum::class)) {
                            $_cache[self::HANDLERS_VECTORS_FROM][$key] = ["fromBackedEnums", $type];                    /** @see fromBackedEnums */
                            $_cache[self::HANDLERS_TO_ARRAY][$key] = "toBackedEnums";                                   /** @see toBackedEnums */
                        } else {
                            $_cache[self::HANDLERS_VECTORS_FROM][$key] = ["fromUnitEnums", $type];                      /** @see fromUnitEnums */
                            $_cache[self::HANDLERS_TO_ARRAY][$key] = "toUnitEnums";                                     /** @see toUnitEnums */
                        }
                    } else {
                        throw new \RuntimeException(static::class . "::$key unhandled type: $type");
                    }
                }
            } else {
                throw new \RuntimeException(static::class . "::$key no handler");
            }
        }

        return $_cache;
    }

    protected function clone(?array &$link, array $data, array &$objects)
    {
        foreach ($data as $i => $value) {
            if (\is_object($value) && ($value instanceof \UnitEnum) === false) {
                $link[$i] = $objects[\spl_object_id($value)] ??= clone $value;
            } else if (\is_array($value)) {
                $this->clone($link[$i], $value, $objects);
            }
        }
    }

}
