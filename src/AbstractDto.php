<?php

declare(strict_types=1);

namespace DtoPacker;

use DtoPacker\Validators as V;

abstract class AbstractDto implements DtoInterface, \JsonSerializable, \Stringable, \ArrayAccess, \Serializable
{
    protected const
        HANDLERS_FROM    = 1,
        HANDLERS_TO      = 2,
        NAMES            = 3,
        PRE_MUTATORS     = 4,
        DEFAULT_VALUES   = 5,
        FIELD_VALIDATORS = 6,
        ARRAY_VALIDATORS = 7
    ;

    protected const
        CACHE = [
            self::HANDLERS_FROM     => [],
            self::HANDLERS_TO       => [],
            self::NAMES             => [],
            self::PRE_MUTATORS      => [],
            self::FIELD_VALIDATORS  => [],
            self::ARRAY_VALIDATORS  => [],
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

    protected const
        FROM_SCALAR      = 'fromScalar'     , TO_SCALAR      = 'toScalar'     , FROM_SCALARS      = 'fromScalars',
        FROM_OBJECT      = 'fromObject'     , TO_OBJECT      = 'toObject'     , FROM_OBJECTS      = 'fromObjects'     , TO_OBJECTS      = 'toObjects',
        FROM_DTO         = 'fromDto'        , TO_DTO         = 'toDto'        , FROM_DTOS         = 'fromDtos'        , TO_DTOS         = 'toDtos',
        FROM_BACKED_ENUM = 'fromBackedEnum' , TO_BACKED_ENUM = 'toBackedEnum' , FROM_BACKED_ENUMS = 'fromBackedEnums' , TO_BACKED_ENUMS = 'toBackedEnums',
        FROM_UNIT_ENUM   = 'fromUnitEnum'   , TO_UNIT_ENUM   = 'toUnitEnum'   , FROM_UNIT_ENUMS   = 'fromUnitEnums'   , TO_UNIT_ENUMS   = 'toUnitEnums',
        FROM_DATETIME    = 'fromDatetime'   , TO_DATETIME    = 'toDatetime'   , FROM_DATETIMES    = 'fromDatetimes'   , TO_DATETIMES    = 'toDatetimes'
    ;

    protected const IS_SCALAR_TYPES =  [
        self::TYPE_INT    => true,
        self::TYPE_STRING => true,
        self::TYPE_FLOAT  => true,
        self::TYPE_BOOL   => true,
        self::TYPE_ARRAY  => true,
        self::TYPE_MIXED  => true,
    ];

    protected const DATETIME_CLASSES =  [
        self::TYPE_DATETIME           => self::TYPE_DATETIME,
        self::TYPE_DATETIME_IMMUTABLE => self::TYPE_DATETIME_IMMUTABLE,
        self::TYPE_DATETIME_INTERFACE => self::TYPE_DATETIME_IMMUTABLE,
    ];

    public const DT_POSTFIX = '1970-01-01T00:00:00.000+00:00';

    private static array $_cache = [];

    public function __construct(string|array $data, bool $withMutators = true)
    {
        self::$_cache[static::class] ??= $this->getProperties();

        \is_string($data) && ($data = \json_decode($data, true));

        $this->fromArray($data, $withMutators);
    }

    public function toArray(): array
    {
        $result = [];
        $types = self::$_cache[static::class][self::HANDLERS_TO];

        $vars = \get_object_vars($this);

        foreach ($vars as $key => $value) {
            $field = &$result[$key];

            if ($value === null) {
                $field = null;
            } else {
                $fn = $types[$key];

                if ($fn === self::TO_SCALAR) {
                    $field = $value;
                } elseif ($fn === self::TO_OBJECT) {
                    $field = (array) $value;
                } elseif ($fn === self::TO_DTO) {
                    /** @var UnpackableInterface $value */
                    $field = $value->toArray();
                } elseif ($fn === self::TO_BACKED_ENUM) {
                    /** @var \BackedEnum $value */
                    $field = $value->value;
                } elseif ($fn === self::TO_UNIT_ENUM) {
                    /** @var \UnitEnum $value */
                    $field = $value->name;
                } elseif ($fn === self::TO_DATETIME) {
                    /** @var \DateTimeInterface $value */
                    $field = $value->format(\DateTimeInterface::RFC3339_EXTENDED);
                } else {
                    $this->$fn($field, $value);
                }
            }
        }

        return $result;
    }

    public function fromArray(array $data, bool $withMutators = true): static
    {
        $exceptions = null;
        $types = self::$_cache[static::class];

        foreach ($data as $alias => $value) {
            $key = $types[self::NAMES][$alias] ?? null;
            if ($key === null) {
                continue;
            }

            if ($withMutators && isset($types[self::PRE_MUTATORS][$key][0])) {
                /** @var callable $preMutator */
                foreach ($types[self::PRE_MUTATORS][$key] as $preMutator) {
                    $value = $preMutator($value);
                }
            }

            try {
                if ($value === null) {
                    $this->{$key} = null;
                    continue;
                }

                [$fn, $class, $dimension] = $types[self::HANDLERS_FROM][$key];
                if ($dimension === 0) {
                    if ($fn === self::FROM_SCALAR) {
                        $this->{$key} = $value;
                    } elseif (\is_object($value)) {
                        $this->{$key} = $value;
                    } elseif ($fn === self::FROM_DTO) {
                        try {
                            $this->{$key} = new $class($value);
                        } catch (V\ValidationExceptions $e) {
                            $this->setException($key, $exceptions, $e);
                        }
                    } elseif ($fn === self::FROM_BACKED_ENUM) {
                        /** @var \BackedEnum $class */
                        $this->{$key} = $class::from($value);
                    } elseif ($fn === self::FROM_UNIT_ENUM) {
                        $this->{$key} = \constant("$class::$value");
                    } elseif ($fn === self::FROM_DATETIME) {
                        /** @var \DateTimeInterface $class */
                        $this->{$key} = $class::createFromFormat(
                            \DateTimeInterface::RFC3339_EXTENDED,
                            $value . \substr(self::DT_POSTFIX, \strlen($value))
                        );
                    } elseif ($fn === self::FROM_OBJECT) {
                        $this->{$key} = (object) $value;
                    }
                } else {
                    $this->{$key} = [];

                    if ($dimension === 1) {
                        if ($fn === self::FROM_SCALARS) {
                            $this->{$key} = $this->$class(...$value);
                        } elseif ($fn === self::FROM_DTOS) {
                            foreach ($value as $i => $v) {
                                try {
                                    $this->{$key}[] = $v instanceof $class ? $v : new $class((array)$v);
                                } catch (V\ValidationExceptions $e) {
                                    $this->setException($key, $exceptions, $e, [$i]);
                                }
                            }
                        } elseif ($fn === self::FROM_BACKED_ENUMS) {
                            foreach ($value as $v) {
                                $this->{$key}[] = $v instanceof $class ? $v : $class::from($v);
                            }
                        } elseif ($fn === self::FROM_UNIT_ENUMS) {
                            foreach ($value as $v) {
                                $this->{$key}[] = $v instanceof $class ? $v : \constant("$class::$v");
                            }
                        } elseif ($fn === self::FROM_DATETIMES) {
                            foreach ($value as $v) {
                                $this->{$key}[] =
                                    $v instanceof \DateTimeInterface
                                        ? $v
                                        : $class::createFromFormat(
                                            \DateTimeInterface::RFC3339_EXTENDED,
                                            $v . \substr(self::DT_POSTFIX, \strlen($v))
                                        );
                            }
                        } elseif ($fn === self::FROM_OBJECTS) {
                            foreach ($value as $v) {
                                $this->{$key}[] = \is_object($v) ? $v : (object) $v;
                            }
                        }
                    } elseif ($fn === self::FROM_DTOS) {
                        $this->fromDtos($key, $value, $this->{$key}, $dimension, [], $exceptions, 1, $class);
                    } else {
                        $this->$fn($value, $this->{$key}, $dimension, 1, $class);
                    }
                }
            } catch (\TypeError $e) {
                $exceptions ??= new V\ValidationExceptions();
                $exceptions->addFieldException($key, (new V\TypeError($this, $key))->error(), $e);
            } catch (\Throwable $e) {
                $exceptions ??= new V\ValidationExceptions();
                $exceptions->addFieldException($key, (new V\Error($this, $key))->error(), $e);
            }
        }

        /** @var V\AbstractValidator[] $validators */
        foreach ($types[self::FIELD_VALIDATORS] as $key => $validators) {
            $this->handleFieldValidators($key, $exceptions, false, ...$validators);
        }

        /** @var V\AbstractValidator[] $validators */
        foreach ($types[self::ARRAY_VALIDATORS] as $key => $validators) {
            $this->handleArrayValidators(
                $key,
                $this->$key ?? [],
                $types[self::HANDLERS_FROM][$key][2],
                [],
                $exceptions,
                ...$validators
            );
        }

        ($exceptions !== null) && throw $exceptions;

        return $this;
    }

    protected function handleFieldValidators(
        string $key,
        ?V\ValidationExceptions &$exceptions,
        bool $break,
        V\AbstractValidator|array ...$validators,
    ): void {
        foreach ($validators as $validator) {
            if (\is_array($validator)) {
                $this->handleFieldValidators($key, $exceptions, true, ...$validator);
            } else {
                $validator->setData($this, $key);

                foreach ($validator($this->$key ?? null) as $e) {
                    $exceptions ??= new V\ValidationExceptions();
                    $exceptions->addFieldException($key, $e->getMessage(), $e);

                    if ($break) {
                        return;
                    }
                }
            }
        }
    }

    protected function handleArrayValidators(
        string $key,
        array $values,
        int $dimension,
        array $indexes,
        ?V\ValidationExceptions &$exceptions,
        V\AbstractValidator|array ...$validators
    ): void {
        if ($dimension === \count($indexes) + 1) {
            foreach ($values as $index => $value) {
                $this->validateArrayItem($key, $value, $dimension, [...$indexes, $index], $exceptions, false, ...$validators);
            }
        } else {
            foreach ($values as $index => $value) {
                $this->handleArrayValidators($key, $value, $dimension, [...$indexes, $index], $exceptions, ...$validators);
            }
        }
    }

    protected function validateArrayItem(
        string $key,
        mixed $value,
        int $dimension,
        array $indexes,
        ?V\ValidationExceptions &$exceptions,
        bool $break,
        V\AbstractValidator|array ...$validators
    ): void {
        foreach ($validators as $validator) {
            if (\is_array($validator)) {
                $this->validateArrayItem($key, $value, $dimension, $indexes, $exceptions, true, ...$validator);
            } else {
                $validator->setData($this, $key, $indexes);

                foreach ($validator($value) as $e) {
                    $exceptions ??= new V\ValidationExceptions();
                    $exceptions->addArrayException($key, $indexes, $e->getMessage(), $e);

                    if ($break) {
                        return;
                    }
                }
            }
        }
    }

    protected function setException(string $key, ?V\ValidationExceptions &$exceptions, V\ValidationExceptions $e, array $indexes = []): void
    {
        $exceptions ??= new V\ValidationExceptions();

        $i = empty($indexes) ? '' : '[' . \implode('][', $indexes) . ']';
        foreach ($e->errors as $error) {
            $error->path = "$key$i.$error->path";
        }
        $exceptions->errors = [...$exceptions->errors, ...$e->errors];
        $exceptions->exceptions = [...$exceptions->exceptions, ...$e->exceptions];
    }

    public function pack(bool $clone = true): static
    {
        $obj = $clone ? clone $this : $this;

        $defaults = self::$_cache[static::class][self::DEFAULT_VALUES] ??= \get_class_vars(static::class);
        unset($defaults['_cache']);

        foreach ($defaults as $key => $default) {
            $obj->removeDuplicates($key, $default);
        }

        return $obj;
    }

    protected function removeDuplicates(string $key, mixed $default): void
    {
        $val = ($this->$key ?? null);

        if ($val instanceof DtoInterface) {
            if ($val->pack(false)->toArray() === ($default ?? [])) {
                unset($this->$key);
            }
        } elseif (\is_array($val)) {
            \array_is_list($val) && $this->clearList($val);
        }

        if ($val === $default) {
            unset($this->$key);
        }
    }

    protected function clearList(array &$values): void
    {
        foreach ($values as $v) {
            if ($v instanceof DtoInterface) {
                $v->pack(false)->toArray();
            } elseif (\is_scalar($v)) {
                //
            } elseif (\array_is_list($v)) {
                $this->clearList($v);
            }
        }
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
        $this->fromArray($data, false);
    }

    public function __toString(): string
    {
        return \json_encode($this->toArray(), JSON_UNESCAPED_UNICODE);
    }

    public function serialize(): string
    {
        return \json_encode($this->toArray(), JSON_UNESCAPED_UNICODE);
    }

    public function unserialize(string $data): void
    {
        $this->fromArray($data, false);
    }

    public function __set(string $name, $value): void
    {
        $this->fromArray([$name => $value]);
    }

    public function __get(string $name): mixed
    {
        (isset($this->{$name}) === false)
            && $this->fromArray([$name => []]);

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
                $this->{$key} = ($objects[\spl_object_id($value)] ??= clone $value);
            } elseif (\is_array($value)) {
                $this->clone($this->{$key}, $value, $objects);
            }
        }

        if ($initializedBy === static::class) {
            unset($initializedBy);
            $objects = [];
        }
    }

    protected function fromDtos(
        string $key,
        array $data,
        ?array &$link,
        int $dimension,
        array $indexes,
        ?V\ValidationExceptions &$exceptions,
        int $level,
        string $class
    ): void {
        if ($dimension === $level) {
            foreach ($data as $i => $value) {
                if ($value instanceof $class) {
                    $link[] = $value;
                } else {
                    try {
                        $link[] = new $class((array)$value);
                    } catch (V\ValidationExceptions $e) {
                        $this->setException($key, $exceptions, $e, [...$indexes, $i]);
                    }
                }
            }
        } else {
            foreach ($data as $i => $value) {
                $this->fromDtos($key, $value, $link[$i], $dimension, [...$indexes, $i], $exceptions, $level + 1, $class);
            }
        }
    }

    protected function fromBackedEnums(array $data, ?array &$link, int $dimension, int $level, string $class): void
    {
        if ($dimension === $level) {
            foreach ($data as $value) {
                if ($value instanceof $class) {
                    $link[] = $value;
                } else {
                    /** @var \BackedEnum $class */
                    $link[] = $class::from($value);
                }
            }
        } else {
            foreach ($data as $i => $value) {
                $this->fromBackedEnums($value, $link[$i], $dimension, $level + 1, $class);
            }
        }
    }

    protected function fromUnitEnums(array $data, ?array &$link, int $dimension, int $level, string $class): void
    {
        if ($dimension === $level) {
            foreach ($data as $value) {
                if ($value instanceof $class) {
                    $link[] = $value;
                } else {
                    $link[] = \constant("$class::$value");
                }
            }
        } else {
            foreach ($data as $i => $value) {
                $this->fromUnitEnums($value, $link[$i], $dimension, $level + 1, $class);
            }
        }
    }

    protected function fromScalars(array $data, ?array &$link, int $dimension, int $level, string $fn): void
    {
        if ($dimension === $level) {
            $link = $this->$fn(...$data);
        } else {
            foreach ($data as $i => $value) {
                $this->fromScalars($value, $link[$i], $dimension, $level + 1, $fn);
            }
        }
    }

    protected function fromObjects(array $data, ?array &$link, int $dimension, int $level): void
    {
        if ($dimension === $level) {
            foreach ($data as $value) {
                $link[] = (object)$value;
            }
        } else {
            foreach ($data as $i => $value) {
                $this->fromObjects($value, $link[$i], $dimension, $level + 1);
            }
        }
    }

    protected function fromDatetimes(array $data, ?array &$link, int $dimension, int $level, string $class): void
    {
        if ($dimension === $level) {
            foreach ($data as $value) {
                if ($value instanceof \DateTimeInterface) {
                    $link[] = $value;
                } else {
                    /** @var \DateTimeInterface $class */
                    $link[] = $class::createFromFormat(
                        \DateTimeInterface::RFC3339_EXTENDED,
                        $value . \substr(self::DT_POSTFIX, \strlen($value))
                    );
                }
            }
        } else {
            foreach ($data as $i => $value) {
                $this->fromDatetimes($value, $link[$i], $dimension, $level + 1, $class);
            }
        }
    }

    protected function toDtos(?array &$link, mixed $data): void
    {
        if (empty($data)) {
            $link = [];
            return;
        }
        foreach ($data as $i => $value) {
            if ($value instanceof UnpackableInterface) {
                $link[] = $value->toArray();
            } else {
                $this->toDtos($link[$i], $value ?? []);
            }
        }
    }

    protected function toBackedEnums(?array &$link, array $data): void
    {
        foreach ($data as $i => $value) {
            if ($value instanceof \BackedEnum) {
                $link[] = $value->value;
            } else {
                $this->toBackedEnums($link[$i], $value ?? []);
            }
        }
    }

    protected function toUnitEnums(?array &$link, array $data): void
    {
        foreach ($data as $i => $value) {
            if ($value instanceof \UnitEnum) {
                $link[] = $value->name;
            } else {
                $this->toUnitEnums($link[$i], $value ?? []);
            }
        }
    }

    protected function toObjects(?array &$link, array $data): void
    {
        foreach ($data as $i => $value) {
            if (\is_object($value)) {
                $link[] = (array)$value;
            } else {
                $this->toObjects($link[$i], $value ?? []);
            }
        }
    }

    protected function toDatetimes(?array &$link, array $data): void
    {
        foreach ($data as $i => $value) {
            if ($value instanceof \DateTimeInterface) {
                $link[] = $value->format(\DateTimeInterface::RFC3339_EXTENDED);
            } else {
                $this->toDatetimes($link[$i], $value ?? []);
            }
        }
    }

    protected function ints(int ...$v): array
    {
        return $v;
    }

    protected function strings(string ...$v): array
    {
        return $v;
    }

    protected function bools(bool ...$v): array
    {
        return $v;
    }

    protected function floats(float ...$v): array
    {
        return $v;
    }

    protected function getProperties(): array
    {
        $_cache = self::CACHE;

        $class = new \ReflectionClass($this);
        $properties = $class->getProperties(\ReflectionProperty::IS_PUBLIC | \ReflectionProperty::IS_PROTECTED);

        foreach ($properties as $property) {
            $reflectionType = $property->getType() ?? new \stdClass();
            $key = $property->name;

            $protect = true;

            $fieldValidators = $property->getAttributes(V\FieldValidators::class);
            isset($fieldValidators[0])
                && $_cache[self::FIELD_VALIDATORS][$key] = $fieldValidators[0]->getArguments();

            $arrayValidators = $property->getAttributes(V\ArrayValidators::class);
            isset($arrayValidators[0])
                && $_cache[self::ARRAY_VALIDATORS][$key] = $arrayValidators[0]->getArguments();

            $mutators = $property->getAttributes(PreMutator::class);
            isset($mutators[0])
                && $_cache[self::PRE_MUTATORS][$key] = $mutators[0]->getArguments();

            if ($reflectionType instanceof \ReflectionNamedType) {
                $type = $reflectionType->getName();

                if (self::IS_SCALAR_TYPES[$type] ?? false) {
                    $protect = isset($fieldValidators[0]) || isset($arrayValidators[0]) || isset($mutators[0]);

                    $_cache[self::HANDLERS_FROM][$key] = [self::FROM_SCALAR, null, 0];
                    $_cache[self::HANDLERS_TO][$key] = self::TO_SCALAR;
                } elseif ($type === self::TYPE_OBJECT) {
                    $_cache[self::HANDLERS_FROM][$key] = [self::FROM_OBJECT, null, 0];
                    $_cache[self::HANDLERS_TO][$key] = self::TO_OBJECT;
                } elseif ($dt = self::DATETIME_CLASSES[$type] ?? false) {
                    $_cache[self::HANDLERS_FROM][$key] = [self::FROM_DATETIME, $dt, 0];
                    $_cache[self::HANDLERS_TO][$key] = self::TO_DATETIME;
                } elseif (($variableType = new \ReflectionClass($type))->implementsInterface(PackableInterface::class)) {
                    $_cache[self::HANDLERS_FROM][$key] = [self::FROM_DTO, $type, 0];
                    $_cache[self::HANDLERS_TO][$key] = self::TO_DTO;
                } elseif ($variableType->implementsInterface(\BackedEnum::class)) {
                    $_cache[self::HANDLERS_FROM][$key] = [self::FROM_BACKED_ENUM, $type, 0];
                    $_cache[self::HANDLERS_TO][$key] = self::TO_BACKED_ENUM;
                } elseif ($variableType->implementsInterface(\UnitEnum::class)) {
                    $_cache[self::HANDLERS_FROM][$key] = [self::FROM_UNIT_ENUM, $type, 0];
                    $_cache[self::HANDLERS_TO][$key] = self::TO_UNIT_ENUM;
                }
            } elseif ($reflectionType instanceof \ReflectionUnionType) {
                $reflectionTypes = $reflectionType->getTypes();
                $isVector = false;
                $type = null;

                foreach ($reflectionTypes as $reflectionType) {
                    $tp = $reflectionType->getName();
                    if ($tp === self::TYPE_NULL) {
                        //
                    } elseif ($tp === self::TYPE_ARRAY) {
                        $isVector = true;
                    } else {
                        $type = $tp;
                    }
                }
                ($type === null || $isVector === false)
                    && throw new \RuntimeException(static::class . "::$key unhandled type");

                $dimension = ($property->getAttributes(Dimension::class) + [null])[0]?->getArguments()[0] ?? 1;

                if (self::IS_SCALAR_TYPES[$type] ?? false) {
                    $_cache[self::HANDLERS_FROM][$key] = [self::FROM_SCALARS, "{$type}s", $dimension];
                    $_cache[self::HANDLERS_TO][$key] = self::TO_SCALAR;
                } elseif ($type === self::TYPE_OBJECT) {
                    $_cache[self::HANDLERS_FROM][$key] = [self::FROM_OBJECTS, null, $dimension];
                    $_cache[self::HANDLERS_TO][$key] = self::TO_OBJECTS;
                } elseif ($dt = self::DATETIME_CLASSES[$type] ?? false) {
                    $_cache[self::HANDLERS_FROM][$key] = [self::FROM_DATETIMES, $dt, $dimension];
                    $_cache[self::HANDLERS_TO][$key] = self::TO_DATETIMES;
                } elseif (($variableType = new \ReflectionClass($type))->implementsInterface(PackableInterface::class)) {
                    $_cache[self::HANDLERS_FROM][$key] = [self::FROM_DTOS, $type, $dimension];
                    $_cache[self::HANDLERS_TO][$key] = self::TO_DTOS;
                } elseif ($variableType->implementsInterface(\BackedEnum::class)) {
                    $_cache[self::HANDLERS_FROM][$key] = [self::FROM_BACKED_ENUMS, $type, $dimension];
                    $_cache[self::HANDLERS_TO][$key] = self::TO_BACKED_ENUMS;
                } elseif ($variableType->implementsInterface(\UnitEnum::class)) {
                    $_cache[self::HANDLERS_FROM][$key] = [self::FROM_UNIT_ENUMS, $type, $dimension];
                    $_cache[self::HANDLERS_TO][$key] = self::TO_UNIT_ENUMS;
                } else {
                    throw new \RuntimeException(static::class . "::$key unhandled type: $type");
                }
            } else {
                throw new \RuntimeException(static::class . "::$key no handler");
            }

            $protect
                && ($property->isProtected() === false)
                && throw new \RuntimeException(static::class . "::$key must be protected");

            $keys = [$key, ...($property->getAttributes(Alias::class) + [null])[0]?->getArguments() ?? []];
            foreach ($keys as $k) {
                $_cache[self::NAMES][$k] = $key;
            }
        }

        return $_cache;
    }

    protected function clone(?array &$link, array $data, array &$objects): void
    {
        foreach ($data as $i => $value) {
            if (\is_object($value) && ($value instanceof \UnitEnum) === false) {
                $link[$i] = ($objects[\spl_object_id($value)] ??= clone $value);
            } elseif (\is_array($value)) {
                $this->clone($link[$i], $value, $objects);
            }
        }
    }
}
