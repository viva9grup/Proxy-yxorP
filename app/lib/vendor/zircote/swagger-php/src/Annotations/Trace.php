<?php declare(strict_types=1);

/**
 * @license Apache 2.0
 */

namespace OpenApi\Annotations;

/**
 * @Annotation
 */
class Trace extends Operation
{
    /**
     * @inheritdoc
     */
    public static $_parents = [
        PathItem::class,
    ];
    /**
     * @inheritdoc
     */
    public $method = 'trace';
}
