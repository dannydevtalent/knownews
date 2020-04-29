<?php

/**
 * This file is part of the contentful/contentful-management package.
 *
 * @copyright 2015-2020 Contentful GmbH
 * @license   MIT
 */

declare(strict_types=1);

namespace Contentful\Management\CodeGenerator;

use Contentful\Core\Api\DateTimeImmutable;
use Contentful\Core\Api\Link;
use Contentful\Core\Resource\ResourceInterface;
use Contentful\Management\Resource\Asset;
use Contentful\Management\Resource\ContentType;
use Contentful\Management\Resource\ContentType\Field\ArrayField;
use Contentful\Management\Resource\ContentType\Field\FieldInterface;
use Contentful\Management\Resource\ContentType\Field\LinkField;
use Contentful\Management\Resource\ContentType\Validation\LinkContentTypeValidation;
use Contentful\Management\Resource\Entry as EntryResource;
use PhpParser\Node;
use PhpParser\Node\Stmt;
use PhpParser\Node\Stmt\Class_;
use PhpParser\Node\Stmt\ClassMethod;

/**
 * Entry class.
 */
class Entry extends BaseCodeGenerator
{
    /**
     * @var array
     */
    private $uses = [];

    /**
     * Restore the uses array to default values.
     */
    private function setDefaultUses()
    {
        $this->uses = [
            'asset' => false,
            'resource_interface' => false,
            'link' => false,
            'date' => false,
        ];
    }

    /**
     * {@inheritdoc}
     */
    public function generate(array $params): string
    {
        $contentType = $params['content_type'];
        $namespace = $params['namespace'];

        $this->setDefaultUses();

        $class = $this->generateClass($contentType);

        /** @var Stmt[] $statements */
        $statements = $this->generateUses([
            EntryResource::class,
            $this->uses['date'] ? DateTimeImmutable::class : null,
            $this->uses['asset'] ? Asset::class : null,
            $this->uses['link'] ? Link::class : null,
            $this->uses['resource_interface'] ? ResourceInterface::class : null,
        ]);

        $statements[] = $class;

        return $this->render(
            new Node\Stmt\Namespace_(new Node\Name($namespace), $statements)
        );
    }

    private function generateClass(ContentType $contentType): Class_
    {
        $className = $this->convertToStudlyCaps($contentType->getId());

        return new Node\Stmt\Class_(
            $className,
            [
                'extends' => new Node\Name('Entry'),
                'stmts' => $this->generateClassMethods($contentType),
            ],
            $this->generateCommentAttributes(\sprintf(
                "\n".'/**
                * %s class.
                *
                * This class was autogenerated.
                */',
                $className
            ))
        );
    }

    /**
     * @return ClassMethod[]
     */
    private function generateClassMethods(ContentType $contentType): array
    {
        $statements = [
            $this->generateConstructor($contentType),
        ];

        foreach ($contentType->getFields() as $field) {
            $type = $this->getFieldType($field);
            if ('Link' === $type || 'Link[]' === $type) {
                $this->uses['link'] = true;
            }
            if ('DateTimeImmutable' === $type) {
                $this->uses['date'] = true;
            }

            $statements[] = $this->generateGetter($field, $type);
            $statements[] = $this->generateSetter($field, $type);

            if ($field instanceof LinkField) {
                $statements[] = $this->generateLinkResolverMethod($field);
            }
            if ($field instanceof ArrayField && 'Link' === $field->getItemsType()) {
                $statements[] = $this->generateArrayLinkResolverMethod($field);
            }
        }

        return $statements;
    }

    /**
     * Generates the following code.
     *
     * ```
     * public function __construct()
     * {
     *     parent::__construct('<contentTypeId>');
     * }
     * ```
     */
    private function generateConstructor(ContentType $contentType): ClassMethod
    {
        return new ClassMethod(
            '__construct',
            [
                'flags' => Node\Stmt\Class_::MODIFIER_PUBLIC,
                'stmts' => [
                    new Node\Expr\StaticCall(
                        new Node\Name('parent'),
                        '__construct',
                        [
                            new Node\Arg(new Node\Scalar\String_($contentType->getId())),
                        ]
                    ),
                ],
            ],
            $this->generateCommentAttributes(\sprintf(
                '/**
                * %s constructor.
                */',
                $this->convertToStudlyCaps($contentType->getId())
            ))
        );
    }

    /**
     * Generates the following code.
     *
     * ```
     * public function getX(string $locale = '<defaultLocale>')
     * {
     *     return $this->getField('x', $locale);
     * }
     * ```
     */
    private function generateGetter(FieldInterface $field, string $type): ClassMethod
    {
        return new ClassMethod(
            'get'.$this->convertToStudlyCaps($field->getId()),
            [
                'flags' => Node\Stmt\Class_::MODIFIER_PUBLIC,
                'params' => [
                    new Node\Param('locale', new Node\Scalar\String_($this->defaultLocale), 'string'),
                ],
                'stmts' => [
                    new Node\Stmt\Return_(
                        new Node\Expr\MethodCall(
                            new Node\Expr\Variable('this'),
                            'getField',
                            [
                                new Node\Arg(new Node\Scalar\String_($field->getId())),
                                new Node\Arg(new Node\Expr\Variable('locale')),
                            ]
                        )
                    ),
                ],
            ],
            $this->generateCommentAttributes(\sprintf(
                "\n".'/**
                * Returns the "%s" field.
                *
                * @param string $locale
                *
                * @return %s|null
                */',
                $field->getId(),
                $type
            ))
        );
    }

    /**
     * Generates the following code.
     *
     * ```
     * public function setX(string $locale = '<defaultLocale>', <type> $value = null)
     * {
     *     return $this->setField('x', $locale, $value);
     * }
     * ```
     */
    private function generateSetter(FieldInterface $field, string $type): ClassMethod
    {
        $methodType = 'mixed' === $type
            ? null
            : (false !== \mb_strpos($type, '[]') ? 'array' : $type);

        return new ClassMethod(
            'set'.$this->convertToStudlyCaps($field->getId()),
            [
                'flags' => Node\Stmt\Class_::MODIFIER_PUBLIC,
                'params' => [
                    new Node\Param('locale', new Node\Scalar\String_($this->defaultLocale), 'string'),
                    new Node\Param('value', new Node\Expr\ConstFetch(new Node\Name('null')), $methodType),
                ],
                'stmts' => [
                    new Node\Stmt\Return_(
                        new Node\Expr\MethodCall(
                            new Node\Expr\Variable('this'),
                            'setField',
                            [
                                new Node\Arg(new Node\Scalar\String_($field->getId())),
                                new Node\Arg(new Node\Expr\Variable('locale')),
                                new Node\Arg(new Node\Expr\Variable('value')),
                            ]
                        )
                    ),
                ],
            ],
            $this->generateCommentAttributes(\sprintf(
                "\n".'/**
                * Sets the "%s" field.
                *
                * @param %s $locale
                * @param %s|null $value
                *
                * @return static
                */',
                $field->getId(),
                \str_pad('string', \mb_strlen($type.'|null'), ' ', \STR_PAD_RIGHT),
                $type
            ))
        );
    }

    /**
     * Generates the following code.
     *
     * ```
     * public function resolveXLink(string $locale = '<defaultLocale>')
     * {
     *     $parameters = [
     *         // Representation of the URI parameters
     *         'space' => $this->sys->getSpace()->getId(),
     *         'environment' => $this->sys->getEnvironment()->getId(),
     *     ];
     *
     *     return $this->client->resolveLink($this->getField('x', $locale), $parameters);
     * }
     * ```
     */
    private function generateLinkResolverMethod(LinkField $field): ClassMethod
    {
        $returnType = $this->determineLinkReturnType($field->getLinkType(), $field->getValidations());

        $resolveLinkParameter = new Node\Expr\MethodCall(
            new Node\Expr\Variable('this'),
            'getField',
            [
                new Node\Arg(new Node\Scalar\String_($field->getId())),
                new Node\Arg(new Node\Expr\Variable('locale')),
            ]
        );

        return new ClassMethod(
            'resolve'.$this->convertToStudlyCaps($field->getId()).'Link',
            [
                'flags' => Node\Stmt\Class_::MODIFIER_PUBLIC,
                'params' => [
                    new Node\Param('locale', new Node\Scalar\String_($this->defaultLocale), 'string'),
                ],
                'stmts' => [
                    $this->generateParametersParameter(),
                    new Node\Stmt\Return_(
                        new Node\Expr\MethodCall(
                            new Node\Expr\PropertyFetch(new Node\Expr\Variable('this'), 'client'),
                            'resolveLink',
                            [
                                new Node\Arg($resolveLinkParameter),
                                new Node\Arg(new Node\Expr\Variable('parameters')),
                            ]
                        ),
                        $this->generateCommentAttributes('')
                    ),
                ],
            ],
            $this->generateCommentAttributes(\sprintf(
                "\n".'/**
                 * Returns the resolved "%s" link.
                 *
                 * @param string $locale
                 *
                 * @return %s
                 */',
                $field->getId(),
                $returnType
            ))
        );
    }

    /**
     * Generates the following code.
     *
     * ```
     * $parameters = [
     *     // Representation of the URI parameters
     *     'space' => $this->sys->getSpace()->getId(),
     *     'environment' => $this->sys->getEnvironment()->getId(),
     * ];
     * ```
     */
    private function generateParametersParameter(): Node\Expr\Assign
    {
        return new Node\Expr\Assign(
            new Node\Expr\Variable('parameters'),
            new Node\Expr\Array_([
                new Node\Expr\ArrayItem(
                    new Node\Expr\MethodCall(
                        new Node\Expr\MethodCall(
                            new Node\Expr\PropertyFetch(new Node\Expr\Variable('this'), 'sys'),
                            'getSpace'
                        ),
                        'getId'
                    ),
                    new Node\Scalar\String_('space'),
                    false,
                    $this->generateCommentAttributes('// Representation of the URI parameters')
                ),
                new Node\Expr\ArrayItem(
                    new Node\Expr\MethodCall(
                        new Node\Expr\MethodCall(
                            new Node\Expr\PropertyFetch(new Node\Expr\Variable('this'), 'sys'),
                            'getEnvironment'
                        ),
                        'getId'
                    ),
                    new Node\Scalar\String_('environment')
                ),
            ])
        );
    }

    private function generateArrayLinkResolverMethod(ArrayField $field): ClassMethod
    {
        $returnTypes = $this->determineLinkReturnType((string) $field->getItemsLinkType(), $field->getItemsValidations());

        return new ClassMethod(
            'resolve'.$this->convertToStudlyCaps($field->getId()).'Links',
            [
                'flags' => Node\Stmt\Class_::MODIFIER_PUBLIC,
                'params' => [
                    new Node\Param('locale', new Node\Scalar\String_($this->defaultLocale), 'string'),
                ],
                'stmts' => [
                    $this->generateParametersParameter(),
                    new Node\Stmt\Return_(
                        new Node\Expr\FuncCall(
                            new Node\Name('\\array_map'),
                            [
                                new Node\Arg($this->generateArrayLinkResolverClosure()),
                                new Node\Arg($this->generateArrayLinkResolverMapArray($field)),
                            ]
                        ),
                        $this->generateCommentAttributes('')
                    ),
                ],
            ],
            $this->generateCommentAttributes(\sprintf(
                "\n".'/**
                 * Returns an array of resolved "%s" links.
                 *
                 * @param string $locale
                 *
                 * @return %s[]
                 */',
                $field->getId(),
                false !== \mb_strpos($returnTypes, '|') ? '('.$returnTypes.')' : $returnTypes
            ))
        );
    }

    /**
     * This method determines which type of link will be returned,
     * and using that information, a "use" statement will be set to true
     * so it will be added to the beginning of the file.
     *
     * @return string Either "Asset", "ResourceInterface", or a list of values with "|" as separator
     */
    private function determineLinkReturnType(string $linkType, array $validations): string
    {
        $returnTypes = ['Asset'];
        $usesAsset = true;
        $usesResource = false;

        if ('Entry' === $linkType) {
            $returnTypes = ['ResourceInterface'];
            $usesAsset = false;
            $usesResource = true;

            foreach ($validations as $validation) {
                if ($validation instanceof LinkContentTypeValidation) {
                    $usesResource = false;
                    $returnTypes = \array_map(function (string $contentType) {
                        return $this->convertToStudlyCaps($contentType);
                    }, $validation->getContentTypes());

                    break;
                }
            }
        }

        if ($usesAsset) {
            $this->uses['asset'] = true;
        }
        if ($usesResource) {
            $this->uses['resource_interface'] = true;
        }

        return \implode('|', $returnTypes);
    }

    private function generateArrayLinkResolverClosure(): Node\Expr
    {
        return new Node\Expr\Closure([
            'params' => [
                new Node\Param('link', null, 'Link'),
            ],
            'stmts' => [
                new Node\Stmt\Return_(
                    new Node\Expr\MethodCall(
                        new Node\Expr\PropertyFetch(new Node\Expr\Variable('this'), 'client'),
                        'resolveLink',
                        [
                            new Node\Arg(new Node\Expr\Variable('link')),
                            new Node\Arg(new Node\Expr\Variable('parameters')),
                        ]
                    )
                ),
            ],
            'uses' => [
                new Node\Expr\ClosureUse('parameters'),
            ],
        ]);
    }

    private function generateArrayLinkResolverMapArray(FieldInterface $field): Node\Expr
    {
        return new Node\Expr\Cast\Array_(
            new Node\Expr\MethodCall(
                new Node\Expr\Variable('this'),
                'getField',
                [
                    new Node\Arg(new Node\Scalar\String_($field->getId())),
                    new Node\Arg(new Node\Expr\Variable('locale')),
                ]
            )
        );
    }
}
