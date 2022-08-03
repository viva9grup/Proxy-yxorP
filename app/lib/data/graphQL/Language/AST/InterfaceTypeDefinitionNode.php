<?php

declare(strict_types=1);

namespace yxorP\app\lib\data\graphQL\Language\AST;

class InterfaceTypeDefinitionNode extends Node implements TypeDefinitionNodeInterface
{
    /** @var string */
    public string $kind = NodeKind::INTERFACE_TYPE_DEFINITION;

    /** @var NameNode */
    public $name;

    /** @var NodeList<DirectiveNode> */
    public NodeList $directives;

    /** @var NodeList<NamedTypeNode> */
    public NodeList $interfaces;

    /** @var NodeList<FieldDefinitionNode> */
    public NodeList $fields;

    /** @var StringValueNode|null */
    public ?StringValueNode $description;
}
