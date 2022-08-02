<?php

declare(strict_types=1);

namespace yxorP\app\lib\graphQL\Validator\Rules;

use yxorP\app\lib\graphQL\Error\Error;
use yxorP\app\lib\graphQL\Language\AST\DirectiveDefinitionNode;
use yxorP\app\lib\graphQL\Language\AST\DirectiveNode;
use yxorP\app\lib\graphQL\Language\AST\InputValueDefinitionNode;
use yxorP\app\lib\graphQL\Language\AST\NodeKind;
use yxorP\app\lib\graphQL\Language\Visitor;
use yxorP\app\lib\graphQL\Language\VisitorOperation;
use yxorP\app\lib\graphQL\Type\Definition\Directive;
use yxorP\app\lib\graphQL\Type\Definition\FieldArgument;
use yxorP\app\lib\graphQL\Utils\Utils;
use yxorP\app\lib\graphQL\Validator\ASTValidationContext;
use yxorP\app\lib\graphQL\Validator\SDLValidationContext;
use yxorP\app\lib\graphQL\Validator\ValidationContext;
use function array_map;
use function in_array;
use function sprintf;

/**
 * Known argument names on directives
 *
 * A GraphQL directive is only valid if all supplied arguments are defined by
 * that field.
 */
class KnownArgumentNamesOnDirectives extends ValidationRule
{
    /**
     * @param string[] $suggestedArgs
     */
    public static function unknownDirectiveArgMessage($argName, $directiveName, array $suggestedArgs)
    {
        $message = sprintf('Unknown argument "%s" on directive "@%s".', $argName, $directiveName);
        if (isset($suggestedArgs[0])) {
            $message .= sprintf(' Did you mean %s?', Utils::quotedOrList($suggestedArgs));
        }

        return $message;
    }

    public function getSDLVisitor(SDLValidationContext $context)
    {
        return $this->getASTVisitor($context);
    }

    public function getASTVisitor(ASTValidationContext $context)
    {
        $directiveArgs = [];
        $schema = $context->getSchema();
        $definedDirectives = $schema !== null ? $schema->getDirectives() : Directive::getInternalDirectives();

        foreach ($definedDirectives as $directive) {
            $directiveArgs[$directive->name] = array_map(
                static function (FieldArgument $arg): string {
                    return $arg->name;
                },
                $directive->args
            );
        }

        $astDefinitions = $context->getDocument()->definitions;
        foreach ($astDefinitions as $def) {
            if (!($def instanceof DirectiveDefinitionNode)) {
                continue;
            }

            $name = $def->name->value;
            if ($def->arguments !== null) {
                $directiveArgs[$name] = Utils::map(
                    $def->arguments ?? [],
                    static function (InputValueDefinitionNode $arg): string {
                        return $arg->name->value;
                    }
                );
            } else {
                $directiveArgs[$name] = [];
            }
        }

        return [
            NodeKind::DIRECTIVE => static function (DirectiveNode $directiveNode) use ($directiveArgs, $context): VisitorOperation {
                $directiveName = $directiveNode->name->value;
                $knownArgs = $directiveArgs[$directiveName] ?? null;

                if ($directiveNode->arguments === null || $knownArgs === null) {
                    return Visitor::skipNode();
                }

                foreach ($directiveNode->arguments as $argNode) {
                    $argName = $argNode->name->value;
                    if (in_array($argName, $knownArgs, true)) {
                        continue;
                    }

                    $suggestions = Utils::suggestionList($argName, $knownArgs);
                    $context->reportError(new Error(
                        self::unknownDirectiveArgMessage($argName, $directiveName, $suggestions),
                        [$argNode]
                    ));
                }

                return Visitor::skipNode();
            },
        ];
    }

    public function getVisitor(ValidationContext $context)
    {
        return $this->getASTVisitor($context);
    }
}