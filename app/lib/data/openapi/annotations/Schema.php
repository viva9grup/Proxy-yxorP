<?php declare(strict_types=1);

/**
 * @license Apache 2.0
 */

namespace yxorP\app\lib\openapi\annotations;

use yxorP\app\lib\openapi\Generator;

/**
 * @Annotation
 * The definition of input and output data types.
 * These types can be objects, but also primitives and arrays.
 * This object is based on the [JSON Schema Specification](http://json-schema.org) and uses a predefined subset of it.
 * On top of this subset, there are extensions provided by this specification to allow for more complete documentation.
 *
 * A "Schema Object": https://github.com/OAI/OpenAPI-Specification/blob/master/versions/3.0.0.md#schemaObject
 * JSON Schema: http://json-schema.org/
 */
class Schema extends AbstractAnnotation
{
    /**
     * @inheritdoc
     */
    public static array $_types = [
        'description' => 'string',
        'required' => '[string]',
        'format' => 'string',
        'collectionFormat' => ['csv', 'ssv', 'tsv', 'pipes', 'multi'],
        'maximum' => 'number',
        'exclusiveMaximum' => 'boolean',
        'minimum' => 'number',
        'exclusiveMinimum' => 'boolean',
        'maxLength' => 'integer',
        'minLength' => 'integer',
        'pattern' => 'string',
        'maxItems' => 'integer',
        'minItems' => 'integer',
        'uniqueItems' => 'boolean',
        'multipleOf' => 'integer',
        'allOf' => '[' . Schema::class . ']',
        'oneOf' => '[' . Schema::class . ']',
        'anyOf' => '[' . Schema::class . ']',
    ];
    /**
     * @inheritdoc
     */
    public static array $_nested = [
        Discriminator::class => 'discriminator',
        Items::class => 'items',
        Property::class => ['properties', 'property'],
        ExternalDocumentation::class => 'externalDocs',
        Xml::class => 'xml',
        AdditionalProperties::class => 'additionalProperties',
        Attachable::class => ['attachables'],
    ];
    /**
     * @inheritdoc
     */
    public static array $_parents = [
        Components::class,
        Parameter::class,
        MediaType::class,
        Header::class,
    ];
    /**
     * $ref See https://swagger.io/docs/specification/using-ref/.
     *
     * @var string
     */
    public string $ref = Generator::UNDEFINED;
    /**
     * The key into Components->schemas array.
     *
     * @var string
     */
    public string $schema = Generator::UNDEFINED;
    /**
     * Can be used to decorate a user interface with information about the data produced by this user interface. preferrably be short.
     *
     * @var string
     */
    public string $title = Generator::UNDEFINED;
    /**
     * A description will provide explanation about the purpose of the instance described by this schema.
     *
     * @var string
     */
    public string $description = Generator::UNDEFINED;
    /**
     * An object instance is valid against "maxProperties" if its number of properties is less than, or equal to, the value of this property.
     *
     * @var int
     */
    public string|int $maxProperties = Generator::UNDEFINED;
    /**
     * An object instance is valid against "minProperties" if its number of properties is greater than, or equal to, the value of this property.
     *
     * @var int
     */
    public string|int $minProperties = Generator::UNDEFINED;
    /**
     * An object instance is valid against this property if its property set contains all elements in this property's array value.
     *
     * @var string[]
     */
    public string|array $required = Generator::UNDEFINED;
    /**
     * @var Property[]
     */
    public string|array $properties = Generator::UNDEFINED;
    /**
     * The type of the schema/property. The value MUST be one of "string", "number", "integer", "boolean", "array" or "object".
     *
     * @var string
     */
    public string $type = Generator::UNDEFINED;
    /**
     * The extending format for the previously mentioned type. See Data Type Formats for further details.
     *
     * @var string
     */
    public string $format = Generator::UNDEFINED;
    /**
     * Required if type is "array". Describes the type of items in the array.
     *
     * @var Items
     */
    public Items|string $items = Generator::UNDEFINED;
    /**
     * @var string Determines the format of the array if type array is used. Possible values are: csv - comma separated values foo,bar. ssv - space separated values foo bar. tsv - tab separated values foo\tbar. pipes - pipe separated values foo|bar. multi - corresponds to multiple parameter instances instead of multiple values for a single instance foo=bar&foo=baz. This is valid only for parameters in "query" or "formData". Default value is csv.
     */
    public string $collectionFormat = Generator::UNDEFINED;
    /**
     * Sets a default value to the parameter. The type of the value depends on the defined type. See http://json-schema.org/latest/json-schema-validation.html#anchor101.
     */
    public string $default = Generator::UNDEFINED;
    /**
     * See http://json-schema.org/latest/json-schema-validation.html#anchor17.
     *
     * @var number
     */
    public number|string $maximum = Generator::UNDEFINED;
    /**
     * See http://json-schema.org/latest/json-schema-validation.html#anchor17.
     *
     * @var bool
     */
    public string|bool $exclusiveMaximum = Generator::UNDEFINED;
    /**
     * See http://json-schema.org/latest/json-schema-validation.html#anchor21.
     *
     * @var number
     */
    public number|string $minimum = Generator::UNDEFINED;
    /**
     * See http://json-schema.org/latest/json-schema-validation.html#anchor21.
     *
     * @var bool
     */
    public string|bool $exclusiveMinimum = Generator::UNDEFINED;
    /**
     * See http://json-schema.org/latest/json-schema-validation.html#anchor26.
     *
     * @var int
     */
    public string|int $maxLength = Generator::UNDEFINED;
    /**
     * See http://json-schema.org/latest/json-schema-validation.html#anchor29.
     *
     * @var int
     */
    public string|int $minLength = Generator::UNDEFINED;
    /**
     * A string instance is considered valid if the regular expression matches the instance successfully.
     *
     * @var string
     */
    public string $pattern = Generator::UNDEFINED;
    /**
     * See http://json-schema.org/latest/json-schema-validation.html#anchor42.
     *
     * @var int
     */
    public string|int $maxItems = Generator::UNDEFINED;
    /**
     * See http://json-schema.org/latest/json-schema-validation.html#anchor45.
     *
     * @var int
     */
    public string|int $minItems = Generator::UNDEFINED;
    /**
     * See http://json-schema.org/latest/json-schema-validation.html#anchor49.
     *
     * @var bool
     */
    public string|bool $uniqueItems = Generator::UNDEFINED;
    /**
     * See http://json-schema.org/latest/json-schema-validation.html#anchor76.
     *
     * @var array
     */
    public string|array $enum = Generator::UNDEFINED;
    /**
     * A numeric instance is valid against "multipleOf" if the result of the division of the instance by this property's value is an integer.
     *
     * @var number
     */
    public number|string $multipleOf = Generator::UNDEFINED;
    /**
     * Adds support for polymorphism.
     * The discriminator is an object name that is used to differentiate between other schemas which may satisfy the payload description.
     * See Composition and Inheritance for more details.
     *
     * @var Discriminator
     */
    public Discriminator|string $discriminator = Generator::UNDEFINED;
    /**
     * Relevant only for Schema "properties" definitions.
     * Declares the property as "read only".
     * This means that it may be sent as part of a response but should not be sent as part of the request.
     * If the property is marked as readOnly being true and is in the required list, the required will take effect on the response only.
     * A property must not be marked as both readOnly and writeOnly being true.
     * Default value is false.
     *
     * @var bool
     */
    public string|bool $readOnly = Generator::UNDEFINED;
    /**
     * Relevant only for Schema "properties" definitions.
     * Declares the property as "write only".
     * Therefore, it may be sent as part of a request but should not be sent as part of the response.
     * If the property is marked as writeOnly being true and is in the required list, the required will take effect on the request only.
     * A property must not be marked as both readOnly and writeOnly being true.
     * Default value is false.
     *
     * @var bool
     */
    public string|bool $writeOnly = Generator::UNDEFINED;
    /**
     * This may be used only on properties schemas.
     * It has no effect on root schemas.
     * Adds additional metadata to describe the XML representation of this property.
     *
     * @var Xml
     */
    public Xml|string $xml = Generator::UNDEFINED;
    /**
     * Additional external documentation for this schema.
     *
     * @var ExternalDocumentation
     */
    public string|ExternalDocumentation $externalDocs = Generator::UNDEFINED;
    /**
     * A free-form property to include an example of an instance for this schema.
     * To represent examples that cannot be naturally represented in JSON or YAML, a string value can be used to contain the example with escaping where necessary.
     */
    public string $example = Generator::UNDEFINED;
    /**
     * Allows sending a null value for the defined schema.
     * Default value is false.
     *
     * @var bool
     */
    public string|bool $nullable = Generator::UNDEFINED;
    /**
     * Specifies that a schema is deprecated and should be transitioned out of usage.
     * Default value is false.
     *
     * @var bool
     */
    public string|bool $deprecated = Generator::UNDEFINED;
    /**
     * An instance validates successfully against this property if it validates successfully against all schemas defined by this property's value.
     *
     * @var Schema[]
     */
    public string|array $allOf = Generator::UNDEFINED;
    /**
     * An instance validates successfully against this property if it validates successfully against at least one schema defined by this property's value.
     *
     * @var Schema[]
     */
    public string|array $anyOf = Generator::UNDEFINED;
    /**
     * An instance validates successfully against this property if it validates successfully against exactly one schema defined by this property's value.
     *
     * @var Schema[]
     */
    public string|array $oneOf = Generator::UNDEFINED;
    /**
     * http://json-schema.org/latest/json-schema-validation.html#rfc.section.6.29.
     */
    public string $not = Generator::UNDEFINED;
    /**
     * http://json-schema.org/latest/json-schema-validation.html#anchor64.
     *
     * @var bool|object
     */
    public string|bool|object $additionalProperties = Generator::UNDEFINED;
    /**
     * http://json-schema.org/latest/json-schema-validation.html#rfc.section.6.10.
     */
    public string $additionalItems = Generator::UNDEFINED;
    /**
     * http://json-schema.org/latest/json-schema-validation.html#rfc.section.6.14.
     */
    public string $contains = Generator::UNDEFINED;
    /**
     * http://json-schema.org/latest/json-schema-validation.html#rfc.section.6.19.
     */
    public string $patternProperties = Generator::UNDEFINED;
    /**
     * http://json-schema.org/latest/json-schema-validation.html#rfc.section.6.21.
     */
    public string $dependencies = Generator::UNDEFINED;
    /**
     * http://json-schema.org/latest/json-schema-validation.html#rfc.section.6.22.
     */
    public string $propertyNames = Generator::UNDEFINED;
    /**
     * http://json-schema.org/latest/json-schema-validation.html#rfc.section.6.24.
     */
    public string $const = Generator::UNDEFINED;

    /**
     * @throws \Exception
     */
    public function validate(array $parents = [], array $skip = [], string $ref = ''): bool
    {
        if ($this->type === 'array' && $this->items === Generator::UNDEFINED) {
            $this->_context->logger->warning('@OA\\Items() is required when ' . $this->identity() . ' has type "array" in ' . $this->_context);

            return false;
        }

        return parent::validate($parents, $skip, $ref);
    }
}