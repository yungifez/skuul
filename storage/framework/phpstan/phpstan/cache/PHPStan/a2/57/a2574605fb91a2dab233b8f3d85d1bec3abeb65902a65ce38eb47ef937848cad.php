<?php declare(strict_types = 1);

// osfsl-/var/www/html/vendor/composer/../laravel/framework/src/Illuminate/Database/Eloquent/Relations/Relation.php-PHPStan\BetterReflection\Reflection\ReflectionClass-Illuminate\Database\Eloquent\Relations\Relation
return \PHPStan\Cache\CacheItem::__set_state(array(
   'variableKey' => 'v2-30e8d8a056ed866be1ee586003094f9c233e2e1a8d42d732c366ba8f01982cc7-8.5.9-6.70.0.3',
   'data' => 
  array (
    'locatedSource' => 
    array (
      'class' => 'PHPStan\\BetterReflection\\SourceLocator\\Located\\LocatedSource',
      'data' => 
      array (
        'name' => 'Illuminate\\Database\\Eloquent\\Relations\\Relation',
        'filename' => '/var/www/html/vendor/composer/../laravel/framework/src/Illuminate/Database/Eloquent/Relations/Relation.php',
      ),
    ),
    'namespace' => 'Illuminate\\Database\\Eloquent\\Relations',
    'name' => 'Illuminate\\Database\\Eloquent\\Relations\\Relation',
    'shortName' => 'Relation',
    'isInterface' => false,
    'isTrait' => false,
    'isEnum' => false,
    'isBackedEnum' => false,
    'modifiers' => 64,
    'docComment' => '/**
 * @template TRelatedModel of \\Illuminate\\Database\\Eloquent\\Model
 * @template TDeclaringModel of \\Illuminate\\Database\\Eloquent\\Model
 * @template TResult
 *
 * @mixin \\Illuminate\\Database\\Eloquent\\Builder<TRelatedModel>
 */',
    'attributes' => 
    array (
    ),
    'startLine' => 24,
    'endLine' => 637,
    'startColumn' => 1,
    'endColumn' => 1,
    'parentClassName' => NULL,
    'implementsClassNames' => 
    array (
      0 => 'Illuminate\\Contracts\\Database\\Eloquent\\Builder',
    ),
    'traitClassNames' => 
    array (
      0 => 'Illuminate\\Support\\Traits\\ForwardsCalls',
      1 => 'Illuminate\\Support\\Traits\\Macroable',
    ),
    'immediateConstants' => 
    array (
    ),
    'immediateProperties' => 
    array (
      'query' => 
      array (
        'declaringClassName' => 'Illuminate\\Database\\Eloquent\\Relations\\Relation',
        'implementingClassName' => 'Illuminate\\Database\\Eloquent\\Relations\\Relation',
        'name' => 'query',
        'modifiers' => 2,
        'type' => NULL,
        'default' => NULL,
        'docComment' => '/**
 * The Eloquent query builder instance.
 *
 * @var \\Illuminate\\Database\\Eloquent\\Builder<TRelatedModel>
 */',
        'attributes' => 
        array (
        ),
        'startLine' => 35,
        'endLine' => 35,
        'startColumn' => 5,
        'endColumn' => 21,
        'isPromoted' => false,
        'declaredAtCompileTime' => true,
        'immediateVirtual' => false,
        'immediateHooks' => 
        array (
        ),
      ),
      'parent' => 
      array (
        'declaringClassName' => 'Illuminate\\Database\\Eloquent\\Relations\\Relation',
        'implementingClassName' => 'Illuminate\\Database\\Eloquent\\Relations\\Relation',
        'name' => 'parent',
        'modifiers' => 2,
        'type' => NULL,
        'default' => NULL,
        'docComment' => '/**
 * The parent model instance.
 *
 * @var TDeclaringModel
 */',
        'attributes' => 
        array (
        ),
        'startLine' => 42,
        'endLine' => 42,
        'startColumn' => 5,
        'endColumn' => 22,
        'isPromoted' => false,
        'declaredAtCompileTime' => true,
        'immediateVirtual' => false,
        'immediateHooks' => 
        array (
        ),
      ),
      'related' => 
      array (
        'declaringClassName' => 'Illuminate\\Database\\Eloquent\\Relations\\Relation',
        'implementingClassName' => 'Illuminate\\Database\\Eloquent\\Relations\\Relation',
        'name' => 'related',
        'modifiers' => 2,
        'type' => NULL,
        'default' => NULL,
        'docComment' => '/**
 * The related model instance.
 *
 * @var TRelatedModel
 */',
        'attributes' => 
        array (
        ),
        'startLine' => 49,
        'endLine' => 49,
        'startColumn' => 5,
        'endColumn' => 23,
        'isPromoted' => false,
        'declaredAtCompileTime' => true,
        'immediateVirtual' => false,
        'immediateHooks' => 
        array (
        ),
      ),
      'eagerKeysWereEmpty' => 
      array (
        'declaringClassName' => 'Illuminate\\Database\\Eloquent\\Relations\\Relation',
        'implementingClassName' => 'Illuminate\\Database\\Eloquent\\Relations\\Relation',
        'name' => 'eagerKeysWereEmpty',
        'modifiers' => 2,
        'type' => NULL,
        'default' => 
        array (
          'code' => 'false',
          'attributes' => 
          array (
            'startLine' => 56,
            'endLine' => 56,
            'startTokenPos' => 137,
            'startFilePos' => 1497,
            'endTokenPos' => 137,
            'endFilePos' => 1501,
          ),
        ),
        'docComment' => '/**
 * Indicates whether the eagerly loaded relation should implicitly return an empty collection.
 *
 * @var bool
 */',
        'attributes' => 
        array (
        ),
        'startLine' => 56,
        'endLine' => 56,
        'startColumn' => 5,
        'endColumn' => 42,
        'isPromoted' => false,
        'declaredAtCompileTime' => true,
        'immediateVirtual' => false,
        'immediateHooks' => 
        array (
        ),
      ),
      'constraints' => 
      array (
        'declaringClassName' => 'Illuminate\\Database\\Eloquent\\Relations\\Relation',
        'implementingClassName' => 'Illuminate\\Database\\Eloquent\\Relations\\Relation',
        'name' => 'constraints',
        'modifiers' => 18,
        'type' => NULL,
        'default' => 
        array (
          'code' => 'true',
          'attributes' => 
          array (
            'startLine' => 63,
            'endLine' => 63,
            'startTokenPos' => 150,
            'startFilePos' => 1637,
            'endTokenPos' => 150,
            'endFilePos' => 1640,
          ),
        ),
        'docComment' => '/**
 * Indicates if the relation is adding constraints.
 *
 * @var bool
 */',
        'attributes' => 
        array (
        ),
        'startLine' => 63,
        'endLine' => 63,
        'startColumn' => 5,
        'endColumn' => 41,
        'isPromoted' => false,
        'declaredAtCompileTime' => true,
        'immediateVirtual' => false,
        'immediateHooks' => 
        array (
        ),
      ),
      'constraintsForNestedRelations' => 
      array (
        'declaringClassName' => 'Illuminate\\Database\\Eloquent\\Relations\\Relation',
        'implementingClassName' => 'Illuminate\\Database\\Eloquent\\Relations\\Relation',
        'name' => 'constraintsForNestedRelations',
        'modifiers' => 18,
        'type' => NULL,
        'default' => 
        array (
          'code' => 'false',
          'attributes' => 
          array (
            'startLine' => 70,
            'endLine' => 70,
            'startTokenPos' => 163,
            'startFilePos' => 1825,
            'endTokenPos' => 163,
            'endFilePos' => 1829,
          ),
        ),
        'docComment' => '/**
 * Indicates whether constraints should be enabled for nested relation attributes.
 *
 * @var bool
 */',
        'attributes' => 
        array (
        ),
        'startLine' => 70,
        'endLine' => 70,
        'startColumn' => 5,
        'endColumn' => 60,
        'isPromoted' => false,
        'declaredAtCompileTime' => true,
        'immediateVirtual' => false,
        'immediateHooks' => 
        array (
        ),
      ),
      'morphMap' => 
      array (
        'declaringClassName' => 'Illuminate\\Database\\Eloquent\\Relations\\Relation',
        'implementingClassName' => 'Illuminate\\Database\\Eloquent\\Relations\\Relation',
        'name' => 'morphMap',
        'modifiers' => 17,
        'type' => NULL,
        'default' => 
        array (
          'code' => '[]',
          'attributes' => 
          array (
            'startLine' => 77,
            'endLine' => 77,
            'startTokenPos' => 176,
            'startFilePos' => 2036,
            'endTokenPos' => 177,
            'endFilePos' => 2037,
          ),
        ),
        'docComment' => '/**
 * An array to map morph names to their class names in the database.
 *
 * @var array<string, class-string<\\Illuminate\\Database\\Eloquent\\Model>>
 */',
        'attributes' => 
        array (
        ),
        'startLine' => 77,
        'endLine' => 77,
        'startColumn' => 5,
        'endColumn' => 33,
        'isPromoted' => false,
        'declaredAtCompileTime' => true,
        'immediateVirtual' => false,
        'immediateHooks' => 
        array (
        ),
      ),
      'requireMorphMap' => 
      array (
        'declaringClassName' => 'Illuminate\\Database\\Eloquent\\Relations\\Relation',
        'implementingClassName' => 'Illuminate\\Database\\Eloquent\\Relations\\Relation',
        'name' => 'requireMorphMap',
        'modifiers' => 18,
        'type' => NULL,
        'default' => 
        array (
          'code' => 'false',
          'attributes' => 
          array (
            'startLine' => 84,
            'endLine' => 84,
            'startTokenPos' => 190,
            'startFilePos' => 2178,
            'endTokenPos' => 190,
            'endFilePos' => 2182,
          ),
        ),
        'docComment' => '/**
 * Prevents morph relationships without a morph map.
 *
 * @var bool
 */',
        'attributes' => 
        array (
        ),
        'startLine' => 84,
        'endLine' => 84,
        'startColumn' => 5,
        'endColumn' => 46,
        'isPromoted' => false,
        'declaredAtCompileTime' => true,
        'immediateVirtual' => false,
        'immediateHooks' => 
        array (
        ),
      ),
      'selfJoinCount' => 
      array (
        'declaringClassName' => 'Illuminate\\Database\\Eloquent\\Relations\\Relation',
        'implementingClassName' => 'Illuminate\\Database\\Eloquent\\Relations\\Relation',
        'name' => 'selfJoinCount',
        'modifiers' => 18,
        'type' => NULL,
        'default' => 
        array (
          'code' => '0',
          'attributes' => 
          array (
            'startLine' => 91,
            'endLine' => 91,
            'startTokenPos' => 203,
            'startFilePos' => 2295,
            'endTokenPos' => 203,
            'endFilePos' => 2295,
          ),
        ),
        'docComment' => '/**
 * The count of self joins.
 *
 * @var int
 */',
        'attributes' => 
        array (
        ),
        'startLine' => 91,
        'endLine' => 91,
        'startColumn' => 5,
        'endColumn' => 40,
        'isPromoted' => false,
        'declaredAtCompileTime' => true,
        'immediateVirtual' => false,
        'immediateHooks' => 
        array (
        ),
      ),
    ),
    'immediateMethods' => 
    array (
      '__construct' => 
      array (
        'name' => '__construct',
        'parameters' => 
        array (
          'query' => 
          array (
            'name' => 'query',
            'default' => NULL,
            'type' => 
            array (
              'class' => 'PHPStan\\BetterReflection\\Reflection\\ReflectionNamedType',
              'data' => 
              array (
                'name' => 'Illuminate\\Database\\Eloquent\\Builder',
                'isIdentifier' => false,
              ),
            ),
            'isVariadic' => false,
            'byRef' => false,
            'isPromoted' => false,
            'attributes' => 
            array (
            ),
            'startLine' => 99,
            'endLine' => 99,
            'startColumn' => 33,
            'endColumn' => 46,
            'parameterIndex' => 0,
            'isOptional' => false,
          ),
          'parent' => 
          array (
            'name' => 'parent',
            'default' => NULL,
            'type' => 
            array (
              'class' => 'PHPStan\\BetterReflection\\Reflection\\ReflectionNamedType',
              'data' => 
              array (
                'name' => 'Illuminate\\Database\\Eloquent\\Model',
                'isIdentifier' => false,
              ),
            ),
            'isVariadic' => false,
            'byRef' => false,
            'isPromoted' => false,
            'attributes' => 
            array (
            ),
            'startLine' => 99,
            'endLine' => 99,
            'startColumn' => 49,
            'endColumn' => 61,
            'parameterIndex' => 1,
            'isOptional' => false,
          ),
        ),
        'returnsReference' => false,
        'returnType' => NULL,
        'attributes' => 
        array (
        ),
        'docComment' => '/**
 * Create a new relation instance.
 *
 * @param  \\Illuminate\\Database\\Eloquent\\Builder<TRelatedModel>  $query
 * @param  TDeclaringModel  $parent
 */',
        'startLine' => 99,
        'endLine' => 106,
        'startColumn' => 5,
        'endColumn' => 5,
        'couldThrow' => false,
        'isClosure' => false,
        'isGenerator' => false,
        'isVariadic' => false,
        'modifiers' => 1,
        'namespace' => 'Illuminate\\Database\\Eloquent\\Relations',
        'declaringClassName' => 'Illuminate\\Database\\Eloquent\\Relations\\Relation',
        'implementingClassName' => 'Illuminate\\Database\\Eloquent\\Relations\\Relation',
        'currentClassName' => 'Illuminate\\Database\\Eloquent\\Relations\\Relation',
        'aliasName' => NULL,
      ),
      'noConstraints' => 
      array (
        'name' => 'noConstraints',
        'parameters' => 
        array (
          'callback' => 
          array (
            'name' => 'callback',
            'default' => NULL,
            'type' => 
            array (
              'class' => 'PHPStan\\BetterReflection\\Reflection\\ReflectionNamedType',
              'data' => 
              array (
                'name' => 'Closure',
                'isIdentifier' => false,
              ),
            ),
            'isVariadic' => false,
            'byRef' => false,
            'isPromoted' => false,
            'attributes' => 
            array (
            ),
            'startLine' => 116,
            'endLine' => 116,
            'startColumn' => 42,
            'endColumn' => 58,
            'parameterIndex' => 0,
            'isOptional' => false,
          ),
        ),
        'returnsReference' => false,
        'returnType' => NULL,
        'attributes' => 
        array (
        ),
        'docComment' => '/**
 * Run a callback with constraints disabled on the relation.
 *
 * @template TReturn of mixed
 *
 * @param  Closure(): TReturn  $callback
 * @return TReturn
 */',
        'startLine' => 116,
        'endLine' => 119,
        'startColumn' => 5,
        'endColumn' => 5,
        'couldThrow' => false,
        'isClosure' => false,
        'isGenerator' => false,
        'isVariadic' => false,
        'modifiers' => 17,
        'namespace' => 'Illuminate\\Database\\Eloquent\\Relations',
        'declaringClassName' => 'Illuminate\\Database\\Eloquent\\Relations\\Relation',
        'implementingClassName' => 'Illuminate\\Database\\Eloquent\\Relations\\Relation',
        'currentClassName' => 'Illuminate\\Database\\Eloquent\\Relations\\Relation',
        'aliasName' => NULL,
      ),
      'noConstraintsForRelation' => 
      array (
        'name' => 'noConstraintsForRelation',
        'parameters' => 
        array (
          'callback' => 
          array (
            'name' => 'callback',
            'default' => NULL,
            'type' => 
            array (
              'class' => 'PHPStan\\BetterReflection\\Reflection\\ReflectionNamedType',
              'data' => 
              array (
                'name' => 'Closure',
                'isIdentifier' => false,
              ),
            ),
            'isVariadic' => false,
            'byRef' => false,
            'isPromoted' => false,
            'attributes' => 
            array (
            ),
            'startLine' => 129,
            'endLine' => 129,
            'startColumn' => 53,
            'endColumn' => 69,
            'parameterIndex' => 0,
            'isOptional' => false,
          ),
        ),
        'returnsReference' => false,
        'returnType' => NULL,
        'attributes' => 
        array (
        ),
        'docComment' => '/**
 * Run a callback without constraints while preserving them for nested relation attributes.
 *
 * @template TReturn of mixed
 *
 * @param  Closure(): TReturn  $callback
 * @return TReturn
 */',
        'startLine' => 129,
        'endLine' => 132,
        'startColumn' => 5,
        'endColumn' => 5,
        'couldThrow' => false,
        'isClosure' => false,
        'isGenerator' => false,
        'isVariadic' => false,
        'modifiers' => 17,
        'namespace' => 'Illuminate\\Database\\Eloquent\\Relations',
        'declaringClassName' => 'Illuminate\\Database\\Eloquent\\Relations\\Relation',
        'implementingClassName' => 'Illuminate\\Database\\Eloquent\\Relations\\Relation',
        'currentClassName' => 'Illuminate\\Database\\Eloquent\\Relations\\Relation',
        'aliasName' => NULL,
      ),
      'withoutConstraints' => 
      array (
        'name' => 'withoutConstraints',
        'parameters' => 
        array (
          'callback' => 
          array (
            'name' => 'callback',
            'default' => NULL,
            'type' => 
            array (
              'class' => 'PHPStan\\BetterReflection\\Reflection\\ReflectionNamedType',
              'data' => 
              array (
                'name' => 'Closure',
                'isIdentifier' => false,
              ),
            ),
            'isVariadic' => false,
            'byRef' => false,
            'isPromoted' => false,
            'attributes' => 
            array (
            ),
            'startLine' => 143,
            'endLine' => 143,
            'startColumn' => 50,
            'endColumn' => 66,
            'parameterIndex' => 0,
            'isOptional' => false,
          ),
          'constraintsForNestedRelations' => 
          array (
            'name' => 'constraintsForNestedRelations',
            'default' => NULL,
            'type' => NULL,
            'isVariadic' => false,
            'byRef' => false,
            'isPromoted' => false,
            'attributes' => 
            array (
            ),
            'startLine' => 143,
            'endLine' => 143,
            'startColumn' => 69,
            'endColumn' => 98,
            'parameterIndex' => 1,
            'isOptional' => false,
          ),
        ),
        'returnsReference' => false,
        'returnType' => NULL,
        'attributes' => 
        array (
        ),
        'docComment' => '/**
 * Run a callback with the configured relation constraints.
 *
 * @template TReturn of mixed
 *
 * @param  Closure(): TReturn  $callback
 * @param  bool  $constraintsForNestedRelations
 * @return TReturn
 */',
        'startLine' => 143,
        'endLine' => 160,
        'startColumn' => 5,
        'endColumn' => 5,
        'couldThrow' => false,
        'isClosure' => false,
        'isGenerator' => false,
        'isVariadic' => false,
        'modifiers' => 18,
        'namespace' => 'Illuminate\\Database\\Eloquent\\Relations',
        'declaringClassName' => 'Illuminate\\Database\\Eloquent\\Relations\\Relation',
        'implementingClassName' => 'Illuminate\\Database\\Eloquent\\Relations\\Relation',
        'currentClassName' => 'Illuminate\\Database\\Eloquent\\Relations\\Relation',
        'aliasName' => NULL,
      ),
      'withConstraints' => 
      array (
        'name' => 'withConstraints',
        'parameters' => 
        array (
          'callback' => 
          array (
            'name' => 'callback',
            'default' => NULL,
            'type' => 
            array (
              'class' => 'PHPStan\\BetterReflection\\Reflection\\ReflectionNamedType',
              'data' => 
              array (
                'name' => 'Closure',
                'isIdentifier' => false,
              ),
            ),
            'isVariadic' => false,
            'byRef' => false,
            'isPromoted' => false,
            'attributes' => 
            array (
            ),
            'startLine' => 170,
            'endLine' => 170,
            'startColumn' => 44,
            'endColumn' => 60,
            'parameterIndex' => 0,
            'isOptional' => false,
          ),
        ),
        'returnsReference' => false,
        'returnType' => NULL,
        'attributes' => 
        array (
        ),
        'docComment' => '/**
 * Run a callback with constraints enabled on the relation.
 *
 * @template TReturn of mixed
 *
 * @param  Closure(): TReturn  $callback
 * @return TReturn
 */',
        'startLine' => 170,
        'endLine' => 181,
        'startColumn' => 5,
        'endColumn' => 5,
        'couldThrow' => false,
        'isClosure' => false,
        'isGenerator' => false,
        'isVariadic' => false,
        'modifiers' => 17,
        'namespace' => 'Illuminate\\Database\\Eloquent\\Relations',
        'declaringClassName' => 'Illuminate\\Database\\Eloquent\\Relations\\Relation',
        'implementingClassName' => 'Illuminate\\Database\\Eloquent\\Relations\\Relation',
        'currentClassName' => 'Illuminate\\Database\\Eloquent\\Relations\\Relation',
        'aliasName' => NULL,
      ),
      'withConstraintsForNestedRelation' => 
      array (
        'name' => 'withConstraintsForNestedRelation',
        'parameters' => 
        array (
          'callback' => 
          array (
            'name' => 'callback',
            'default' => NULL,
            'type' => 
            array (
              'class' => 'PHPStan\\BetterReflection\\Reflection\\ReflectionNamedType',
              'data' => 
              array (
                'name' => 'Closure',
                'isIdentifier' => false,
              ),
            ),
            'isVariadic' => false,
            'byRef' => false,
            'isPromoted' => false,
            'attributes' => 
            array (
            ),
            'startLine' => 191,
            'endLine' => 191,
            'startColumn' => 61,
            'endColumn' => 77,
            'parameterIndex' => 0,
            'isOptional' => false,
          ),
        ),
        'returnsReference' => false,
        'returnType' => NULL,
        'attributes' => 
        array (
        ),
        'docComment' => '/**
 * Run a callback with constraints when resolving a nested relation attribute.
 *
 * @template TReturn of mixed
 *
 * @param  Closure(): TReturn  $callback
 * @return TReturn
 */',
        'startLine' => 191,
        'endLine' => 196,
        'startColumn' => 5,
        'endColumn' => 5,
        'couldThrow' => false,
        'isClosure' => false,
        'isGenerator' => false,
        'isVariadic' => false,
        'modifiers' => 17,
        'namespace' => 'Illuminate\\Database\\Eloquent\\Relations',
        'declaringClassName' => 'Illuminate\\Database\\Eloquent\\Relations\\Relation',
        'implementingClassName' => 'Illuminate\\Database\\Eloquent\\Relations\\Relation',
        'currentClassName' => 'Illuminate\\Database\\Eloquent\\Relations\\Relation',
        'aliasName' => NULL,
      ),
      'addConstraints' => 
      array (
        'name' => 'addConstraints',
        'parameters' => 
        array (
        ),
        'returnsReference' => false,
        'returnType' => NULL,
        'attributes' => 
        array (
        ),
        'docComment' => '/**
 * Set the base constraints on the relation query.
 *
 * @return void
 */',
        'startLine' => 203,
        'endLine' => 203,
        'startColumn' => 5,
        'endColumn' => 46,
        'couldThrow' => false,
        'isClosure' => false,
        'isGenerator' => false,
        'isVariadic' => false,
        'modifiers' => 65,
        'namespace' => 'Illuminate\\Database\\Eloquent\\Relations',
        'declaringClassName' => 'Illuminate\\Database\\Eloquent\\Relations\\Relation',
        'implementingClassName' => 'Illuminate\\Database\\Eloquent\\Relations\\Relation',
        'currentClassName' => 'Illuminate\\Database\\Eloquent\\Relations\\Relation',
        'aliasName' => NULL,
      ),
      'addEagerConstraints' => 
      array (
        'name' => 'addEagerConstraints',
        'parameters' => 
        array (
          'models' => 
          array (
            'name' => 'models',
            'default' => NULL,
            'type' => 
            array (
              'class' => 'PHPStan\\BetterReflection\\Reflection\\ReflectionNamedType',
              'data' => 
              array (
                'name' => 'array',
                'isIdentifier' => true,
              ),
            ),
            'isVariadic' => false,
            'byRef' => false,
            'isPromoted' => false,
            'attributes' => 
            array (
            ),
            'startLine' => 211,
            'endLine' => 211,
            'startColumn' => 50,
            'endColumn' => 62,
            'parameterIndex' => 0,
            'isOptional' => false,
          ),
        ),
        'returnsReference' => false,
        'returnType' => NULL,
        'attributes' => 
        array (
        ),
        'docComment' => '/**
 * Set the constraints for an eager load of the relation.
 *
 * @param  array<int, TDeclaringModel>  $models
 * @return void
 */',
        'startLine' => 211,
        'endLine' => 211,
        'startColumn' => 5,
        'endColumn' => 64,
        'couldThrow' => false,
        'isClosure' => false,
        'isGenerator' => false,
        'isVariadic' => false,
        'modifiers' => 65,
        'namespace' => 'Illuminate\\Database\\Eloquent\\Relations',
        'declaringClassName' => 'Illuminate\\Database\\Eloquent\\Relations\\Relation',
        'implementingClassName' => 'Illuminate\\Database\\Eloquent\\Relations\\Relation',
        'currentClassName' => 'Illuminate\\Database\\Eloquent\\Relations\\Relation',
        'aliasName' => NULL,
      ),
      'initRelation' => 
      array (
        'name' => 'initRelation',
        'parameters' => 
        array (
          'models' => 
          array (
            'name' => 'models',
            'default' => NULL,
            'type' => 
            array (
              'class' => 'PHPStan\\BetterReflection\\Reflection\\ReflectionNamedType',
              'data' => 
              array (
                'name' => 'array',
                'isIdentifier' => true,
              ),
            ),
            'isVariadic' => false,
            'byRef' => false,
            'isPromoted' => false,
            'attributes' => 
            array (
            ),
            'startLine' => 220,
            'endLine' => 220,
            'startColumn' => 43,
            'endColumn' => 55,
            'parameterIndex' => 0,
            'isOptional' => false,
          ),
          'relation' => 
          array (
            'name' => 'relation',
            'default' => NULL,
            'type' => NULL,
            'isVariadic' => false,
            'byRef' => false,
            'isPromoted' => false,
            'attributes' => 
            array (
            ),
            'startLine' => 220,
            'endLine' => 220,
            'startColumn' => 58,
            'endColumn' => 66,
            'parameterIndex' => 1,
            'isOptional' => false,
          ),
        ),
        'returnsReference' => false,
        'returnType' => NULL,
        'attributes' => 
        array (
        ),
        'docComment' => '/**
 * Initialize the relation on a set of models.
 *
 * @param  array<int, TDeclaringModel>  $models
 * @param  string  $relation
 * @return array<int, TDeclaringModel>
 */',
        'startLine' => 220,
        'endLine' => 220,
        'startColumn' => 5,
        'endColumn' => 68,
        'couldThrow' => false,
        'isClosure' => false,
        'isGenerator' => false,
        'isVariadic' => false,
        'modifiers' => 65,
        'namespace' => 'Illuminate\\Database\\Eloquent\\Relations',
        'declaringClassName' => 'Illuminate\\Database\\Eloquent\\Relations\\Relation',
        'implementingClassName' => 'Illuminate\\Database\\Eloquent\\Relations\\Relation',
        'currentClassName' => 'Illuminate\\Database\\Eloquent\\Relations\\Relation',
        'aliasName' => NULL,
      ),
      'match' => 
      array (
        'name' => 'match',
        'parameters' => 
        array (
          'models' => 
          array (
            'name' => 'models',
            'default' => NULL,
            'type' => 
            array (
              'class' => 'PHPStan\\BetterReflection\\Reflection\\ReflectionNamedType',
              'data' => 
              array (
                'name' => 'array',
                'isIdentifier' => true,
              ),
            ),
            'isVariadic' => false,
            'byRef' => false,
            'isPromoted' => false,
            'attributes' => 
            array (
            ),
            'startLine' => 230,
            'endLine' => 230,
            'startColumn' => 36,
            'endColumn' => 48,
            'parameterIndex' => 0,
            'isOptional' => false,
          ),
          'results' => 
          array (
            'name' => 'results',
            'default' => NULL,
            'type' => 
            array (
              'class' => 'PHPStan\\BetterReflection\\Reflection\\ReflectionNamedType',
              'data' => 
              array (
                'name' => 'Illuminate\\Database\\Eloquent\\Collection',
                'isIdentifier' => false,
              ),
            ),
            'isVariadic' => false,
            'byRef' => false,
            'isPromoted' => false,
            'attributes' => 
            array (
            ),
            'startLine' => 230,
            'endLine' => 230,
            'startColumn' => 51,
            'endColumn' => 77,
            'parameterIndex' => 1,
            'isOptional' => false,
          ),
          'relation' => 
          array (
            'name' => 'relation',
            'default' => NULL,
            'type' => NULL,
            'isVariadic' => false,
            'byRef' => false,
            'isPromoted' => false,
            'attributes' => 
            array (
            ),
            'startLine' => 230,
            'endLine' => 230,
            'startColumn' => 80,
            'endColumn' => 88,
            'parameterIndex' => 2,
            'isOptional' => false,
          ),
        ),
        'returnsReference' => false,
        'returnType' => NULL,
        'attributes' => 
        array (
        ),
        'docComment' => '/**
 * Match the eagerly loaded results to their parents.
 *
 * @param  array<int, TDeclaringModel>  $models
 * @param  \\Illuminate\\Database\\Eloquent\\Collection<int, TRelatedModel>  $results
 * @param  string  $relation
 * @return array<int, TDeclaringModel>
 */',
        'startLine' => 230,
        'endLine' => 230,
        'startColumn' => 5,
        'endColumn' => 90,
        'couldThrow' => false,
        'isClosure' => false,
        'isGenerator' => false,
        'isVariadic' => false,
        'modifiers' => 65,
        'namespace' => 'Illuminate\\Database\\Eloquent\\Relations',
        'declaringClassName' => 'Illuminate\\Database\\Eloquent\\Relations\\Relation',
        'implementingClassName' => 'Illuminate\\Database\\Eloquent\\Relations\\Relation',
        'currentClassName' => 'Illuminate\\Database\\Eloquent\\Relations\\Relation',
        'aliasName' => NULL,
      ),
      'getResults' => 
      array (
        'name' => 'getResults',
        'parameters' => 
        array (
        ),
        'returnsReference' => false,
        'returnType' => NULL,
        'attributes' => 
        array (
        ),
        'docComment' => '/**
 * Get the results of the relationship.
 *
 * @return TResult
 */',
        'startLine' => 237,
        'endLine' => 237,
        'startColumn' => 5,
        'endColumn' => 42,
        'couldThrow' => false,
        'isClosure' => false,
        'isGenerator' => false,
        'isVariadic' => false,
        'modifiers' => 65,
        'namespace' => 'Illuminate\\Database\\Eloquent\\Relations',
        'declaringClassName' => 'Illuminate\\Database\\Eloquent\\Relations\\Relation',
        'implementingClassName' => 'Illuminate\\Database\\Eloquent\\Relations\\Relation',
        'currentClassName' => 'Illuminate\\Database\\Eloquent\\Relations\\Relation',
        'aliasName' => NULL,
      ),
      'getEager' => 
      array (
        'name' => 'getEager',
        'parameters' => 
        array (
        ),
        'returnsReference' => false,
        'returnType' => NULL,
        'attributes' => 
        array (
        ),
        'docComment' => '/**
 * Get the relationship for eager loading.
 *
 * @return \\Illuminate\\Database\\Eloquent\\Collection<int, TRelatedModel>
 */',
        'startLine' => 244,
        'endLine' => 249,
        'startColumn' => 5,
        'endColumn' => 5,
        'couldThrow' => false,
        'isClosure' => false,
        'isGenerator' => false,
        'isVariadic' => false,
        'modifiers' => 1,
        'namespace' => 'Illuminate\\Database\\Eloquent\\Relations',
        'declaringClassName' => 'Illuminate\\Database\\Eloquent\\Relations\\Relation',
        'implementingClassName' => 'Illuminate\\Database\\Eloquent\\Relations\\Relation',
        'currentClassName' => 'Illuminate\\Database\\Eloquent\\Relations\\Relation',
        'aliasName' => NULL,
      ),
      'sole' => 
      array (
        'name' => 'sole',
        'parameters' => 
        array (
          'columns' => 
          array (
            'name' => 'columns',
            'default' => 
            array (
              'code' => '[\'*\']',
              'attributes' => 
              array (
                'startLine' => 260,
                'endLine' => 260,
                'startTokenPos' => 673,
                'startFilePos' => 7227,
                'endTokenPos' => 675,
                'endFilePos' => 7231,
              ),
            ),
            'type' => NULL,
            'isVariadic' => false,
            'byRef' => false,
            'isPromoted' => false,
            'attributes' => 
            array (
            ),
            'startLine' => 260,
            'endLine' => 260,
            'startColumn' => 26,
            'endColumn' => 41,
            'parameterIndex' => 0,
            'isOptional' => true,
          ),
        ),
        'returnsReference' => false,
        'returnType' => NULL,
        'attributes' => 
        array (
        ),
        'docComment' => '/**
 * Execute the query and get the first result if it\'s the sole matching record.
 *
 * @param  array|string  $columns
 * @return TRelatedModel
 *
 * @throws \\Illuminate\\Database\\Eloquent\\ModelNotFoundException<TRelatedModel>
 * @throws \\Illuminate\\Database\\MultipleRecordsFoundException
 */',
        'startLine' => 260,
        'endLine' => 275,
        'startColumn' => 5,
        'endColumn' => 5,
        'couldThrow' => false,
        'isClosure' => false,
        'isGenerator' => false,
        'isVariadic' => false,
        'modifiers' => 1,
        'namespace' => 'Illuminate\\Database\\Eloquent\\Relations',
        'declaringClassName' => 'Illuminate\\Database\\Eloquent\\Relations\\Relation',
        'implementingClassName' => 'Illuminate\\Database\\Eloquent\\Relations\\Relation',
        'currentClassName' => 'Illuminate\\Database\\Eloquent\\Relations\\Relation',
        'aliasName' => NULL,
      ),
      'get' => 
      array (
        'name' => 'get',
        'parameters' => 
        array (
          'columns' => 
          array (
            'name' => 'columns',
            'default' => 
            array (
              'code' => '[\'*\']',
              'attributes' => 
              array (
                'startLine' => 283,
                'endLine' => 283,
                'startTokenPos' => 788,
                'startFilePos' => 7804,
                'endTokenPos' => 790,
                'endFilePos' => 7808,
              ),
            ),
            'type' => NULL,
            'isVariadic' => false,
            'byRef' => false,
            'isPromoted' => false,
            'attributes' => 
            array (
            ),
            'startLine' => 283,
            'endLine' => 283,
            'startColumn' => 25,
            'endColumn' => 40,
            'parameterIndex' => 0,
            'isOptional' => true,
          ),
        ),
        'returnsReference' => false,
        'returnType' => NULL,
        'attributes' => 
        array (
        ),
        'docComment' => '/**
 * Execute the query as a "select" statement.
 *
 * @param  array  $columns
 * @return \\Illuminate\\Database\\Eloquent\\Collection<int, TRelatedModel>
 */',
        'startLine' => 283,
        'endLine' => 286,
        'startColumn' => 5,
        'endColumn' => 5,
        'couldThrow' => false,
        'isClosure' => false,
        'isGenerator' => false,
        'isVariadic' => false,
        'modifiers' => 1,
        'namespace' => 'Illuminate\\Database\\Eloquent\\Relations',
        'declaringClassName' => 'Illuminate\\Database\\Eloquent\\Relations\\Relation',
        'implementingClassName' => 'Illuminate\\Database\\Eloquent\\Relations\\Relation',
        'currentClassName' => 'Illuminate\\Database\\Eloquent\\Relations\\Relation',
        'aliasName' => NULL,
      ),
      'touch' => 
      array (
        'name' => 'touch',
        'parameters' => 
        array (
        ),
        'returnsReference' => false,
        'returnType' => NULL,
        'attributes' => 
        array (
        ),
        'docComment' => '/**
 * Touch all of the related models for the relationship.
 *
 * @return void
 */',
        'startLine' => 293,
        'endLine' => 302,
        'startColumn' => 5,
        'endColumn' => 5,
        'couldThrow' => false,
        'isClosure' => false,
        'isGenerator' => false,
        'isVariadic' => false,
        'modifiers' => 1,
        'namespace' => 'Illuminate\\Database\\Eloquent\\Relations',
        'declaringClassName' => 'Illuminate\\Database\\Eloquent\\Relations\\Relation',
        'implementingClassName' => 'Illuminate\\Database\\Eloquent\\Relations\\Relation',
        'currentClassName' => 'Illuminate\\Database\\Eloquent\\Relations\\Relation',
        'aliasName' => NULL,
      ),
      'rawUpdate' => 
      array (
        'name' => 'rawUpdate',
        'parameters' => 
        array (
          'attributes' => 
          array (
            'name' => 'attributes',
            'default' => 
            array (
              'code' => '[]',
              'attributes' => 
              array (
                'startLine' => 310,
                'endLine' => 310,
                'startTokenPos' => 889,
                'startFilePos' => 8406,
                'endTokenPos' => 890,
                'endFilePos' => 8407,
              ),
            ),
            'type' => 
            array (
              'class' => 'PHPStan\\BetterReflection\\Reflection\\ReflectionNamedType',
              'data' => 
              array (
                'name' => 'array',
                'isIdentifier' => true,
              ),
            ),
            'isVariadic' => false,
            'byRef' => false,
            'isPromoted' => false,
            'attributes' => 
            array (
            ),
            'startLine' => 310,
            'endLine' => 310,
            'startColumn' => 31,
            'endColumn' => 52,
            'parameterIndex' => 0,
            'isOptional' => true,
          ),
        ),
        'returnsReference' => false,
        'returnType' => NULL,
        'attributes' => 
        array (
        ),
        'docComment' => '/**
 * Run a raw update against the base query.
 *
 * @param  array  $attributes
 * @return int
 */',
        'startLine' => 310,
        'endLine' => 313,
        'startColumn' => 5,
        'endColumn' => 5,
        'couldThrow' => false,
        'isClosure' => false,
        'isGenerator' => false,
        'isVariadic' => false,
        'modifiers' => 1,
        'namespace' => 'Illuminate\\Database\\Eloquent\\Relations',
        'declaringClassName' => 'Illuminate\\Database\\Eloquent\\Relations\\Relation',
        'implementingClassName' => 'Illuminate\\Database\\Eloquent\\Relations\\Relation',
        'currentClassName' => 'Illuminate\\Database\\Eloquent\\Relations\\Relation',
        'aliasName' => NULL,
      ),
      'getRelationExistenceCountQuery' => 
      array (
        'name' => 'getRelationExistenceCountQuery',
        'parameters' => 
        array (
          'query' => 
          array (
            'name' => 'query',
            'default' => NULL,
            'type' => 
            array (
              'class' => 'PHPStan\\BetterReflection\\Reflection\\ReflectionNamedType',
              'data' => 
              array (
                'name' => 'Illuminate\\Database\\Eloquent\\Builder',
                'isIdentifier' => false,
              ),
            ),
            'isVariadic' => false,
            'byRef' => false,
            'isPromoted' => false,
            'attributes' => 
            array (
            ),
            'startLine' => 322,
            'endLine' => 322,
            'startColumn' => 52,
            'endColumn' => 65,
            'parameterIndex' => 0,
            'isOptional' => false,
          ),
          'parentQuery' => 
          array (
            'name' => 'parentQuery',
            'default' => NULL,
            'type' => 
            array (
              'class' => 'PHPStan\\BetterReflection\\Reflection\\ReflectionNamedType',
              'data' => 
              array (
                'name' => 'Illuminate\\Database\\Eloquent\\Builder',
                'isIdentifier' => false,
              ),
            ),
            'isVariadic' => false,
            'byRef' => false,
            'isPromoted' => false,
            'attributes' => 
            array (
            ),
            'startLine' => 322,
            'endLine' => 322,
            'startColumn' => 68,
            'endColumn' => 87,
            'parameterIndex' => 1,
            'isOptional' => false,
          ),
        ),
        'returnsReference' => false,
        'returnType' => NULL,
        'attributes' => 
        array (
        ),
        'docComment' => '/**
 * Add the constraints for a relationship count query.
 *
 * @param  \\Illuminate\\Database\\Eloquent\\Builder<TRelatedModel>  $query
 * @param  \\Illuminate\\Database\\Eloquent\\Builder<TDeclaringModel>  $parentQuery
 * @return \\Illuminate\\Database\\Eloquent\\Builder<TRelatedModel>
 */',
        'startLine' => 322,
        'endLine' => 327,
        'startColumn' => 5,
        'endColumn' => 5,
        'couldThrow' => false,
        'isClosure' => false,
        'isGenerator' => false,
        'isVariadic' => false,
        'modifiers' => 1,
        'namespace' => 'Illuminate\\Database\\Eloquent\\Relations',
        'declaringClassName' => 'Illuminate\\Database\\Eloquent\\Relations\\Relation',
        'implementingClassName' => 'Illuminate\\Database\\Eloquent\\Relations\\Relation',
        'currentClassName' => 'Illuminate\\Database\\Eloquent\\Relations\\Relation',
        'aliasName' => NULL,
      ),
      'getRelationExistenceQuery' => 
      array (
        'name' => 'getRelationExistenceQuery',
        'parameters' => 
        array (
          'query' => 
          array (
            'name' => 'query',
            'default' => NULL,
            'type' => 
            array (
              'class' => 'PHPStan\\BetterReflection\\Reflection\\ReflectionNamedType',
              'data' => 
              array (
                'name' => 'Illuminate\\Database\\Eloquent\\Builder',
                'isIdentifier' => false,
              ),
            ),
            'isVariadic' => false,
            'byRef' => false,
            'isPromoted' => false,
            'attributes' => 
            array (
            ),
            'startLine' => 339,
            'endLine' => 339,
            'startColumn' => 47,
            'endColumn' => 60,
            'parameterIndex' => 0,
            'isOptional' => false,
          ),
          'parentQuery' => 
          array (
            'name' => 'parentQuery',
            'default' => NULL,
            'type' => 
            array (
              'class' => 'PHPStan\\BetterReflection\\Reflection\\ReflectionNamedType',
              'data' => 
              array (
                'name' => 'Illuminate\\Database\\Eloquent\\Builder',
                'isIdentifier' => false,
              ),
            ),
            'isVariadic' => false,
            'byRef' => false,
            'isPromoted' => false,
            'attributes' => 
            array (
            ),
            'startLine' => 339,
            'endLine' => 339,
            'startColumn' => 63,
            'endColumn' => 82,
            'parameterIndex' => 1,
            'isOptional' => false,
          ),
          'columns' => 
          array (
            'name' => 'columns',
            'default' => 
            array (
              'code' => '[\'*\']',
              'attributes' => 
              array (
                'startLine' => 339,
                'endLine' => 339,
                'startTokenPos' => 989,
                'startFilePos' => 9589,
                'endTokenPos' => 991,
                'endFilePos' => 9593,
              ),
            ),
            'type' => NULL,
            'isVariadic' => false,
            'byRef' => false,
            'isPromoted' => false,
            'attributes' => 
            array (
            ),
            'startLine' => 339,
            'endLine' => 339,
            'startColumn' => 85,
            'endColumn' => 100,
            'parameterIndex' => 2,
            'isOptional' => true,
          ),
        ),
        'returnsReference' => false,
        'returnType' => NULL,
        'attributes' => 
        array (
        ),
        'docComment' => '/**
 * Add the constraints for an internal relationship existence query.
 *
 * Essentially, these queries compare on column names like whereColumn.
 *
 * @param  \\Illuminate\\Database\\Eloquent\\Builder<TRelatedModel>  $query
 * @param  \\Illuminate\\Database\\Eloquent\\Builder<TDeclaringModel>  $parentQuery
 * @param  mixed  $columns
 * @return \\Illuminate\\Database\\Eloquent\\Builder<TRelatedModel>
 */',
        'startLine' => 339,
        'endLine' => 344,
        'startColumn' => 5,
        'endColumn' => 5,
        'couldThrow' => false,
        'isClosure' => false,
        'isGenerator' => false,
        'isVariadic' => false,
        'modifiers' => 1,
        'namespace' => 'Illuminate\\Database\\Eloquent\\Relations',
        'declaringClassName' => 'Illuminate\\Database\\Eloquent\\Relations\\Relation',
        'implementingClassName' => 'Illuminate\\Database\\Eloquent\\Relations\\Relation',
        'currentClassName' => 'Illuminate\\Database\\Eloquent\\Relations\\Relation',
        'aliasName' => NULL,
      ),
      'getRelationCountHash' => 
      array (
        'name' => 'getRelationCountHash',
        'parameters' => 
        array (
          'incrementJoinCount' => 
          array (
            'name' => 'incrementJoinCount',
            'default' => 
            array (
              'code' => 'true',
              'attributes' => 
              array (
                'startLine' => 352,
                'endLine' => 352,
                'startTokenPos' => 1041,
                'startFilePos' => 9951,
                'endTokenPos' => 1041,
                'endFilePos' => 9954,
              ),
            ),
            'type' => NULL,
            'isVariadic' => false,
            'byRef' => false,
            'isPromoted' => false,
            'attributes' => 
            array (
            ),
            'startLine' => 352,
            'endLine' => 352,
            'startColumn' => 42,
            'endColumn' => 67,
            'parameterIndex' => 0,
            'isOptional' => true,
          ),
        ),
        'returnsReference' => false,
        'returnType' => NULL,
        'attributes' => 
        array (
        ),
        'docComment' => '/**
 * Get a relationship join table hash.
 *
 * @param  bool  $incrementJoinCount
 * @return string
 */',
        'startLine' => 352,
        'endLine' => 355,
        'startColumn' => 5,
        'endColumn' => 5,
        'couldThrow' => false,
        'isClosure' => false,
        'isGenerator' => false,
        'isVariadic' => false,
        'modifiers' => 1,
        'namespace' => 'Illuminate\\Database\\Eloquent\\Relations',
        'declaringClassName' => 'Illuminate\\Database\\Eloquent\\Relations\\Relation',
        'implementingClassName' => 'Illuminate\\Database\\Eloquent\\Relations\\Relation',
        'currentClassName' => 'Illuminate\\Database\\Eloquent\\Relations\\Relation',
        'aliasName' => NULL,
      ),
      'getKeys' => 
      array (
        'name' => 'getKeys',
        'parameters' => 
        array (
          'models' => 
          array (
            'name' => 'models',
            'default' => NULL,
            'type' => 
            array (
              'class' => 'PHPStan\\BetterReflection\\Reflection\\ReflectionNamedType',
              'data' => 
              array (
                'name' => 'array',
                'isIdentifier' => true,
              ),
            ),
            'isVariadic' => false,
            'byRef' => false,
            'isPromoted' => false,
            'attributes' => 
            array (
            ),
            'startLine' => 364,
            'endLine' => 364,
            'startColumn' => 32,
            'endColumn' => 44,
            'parameterIndex' => 0,
            'isOptional' => false,
          ),
          'key' => 
          array (
            'name' => 'key',
            'default' => 
            array (
              'code' => 'null',
              'attributes' => 
              array (
                'startLine' => 364,
                'endLine' => 364,
                'startTokenPos' => 1087,
                'startFilePos' => 10343,
                'endTokenPos' => 1087,
                'endFilePos' => 10346,
              ),
            ),
            'type' => NULL,
            'isVariadic' => false,
            'byRef' => false,
            'isPromoted' => false,
            'attributes' => 
            array (
            ),
            'startLine' => 364,
            'endLine' => 364,
            'startColumn' => 47,
            'endColumn' => 57,
            'parameterIndex' => 1,
            'isOptional' => true,
          ),
        ),
        'returnsReference' => false,
        'returnType' => NULL,
        'attributes' => 
        array (
        ),
        'docComment' => '/**
 * Get all of the primary keys for an array of models.
 *
 * @param  array<int, TDeclaringModel>  $models
 * @param  string|null  $key
 * @return array<int, int|string|null>
 */',
        'startLine' => 364,
        'endLine' => 369,
        'startColumn' => 5,
        'endColumn' => 5,
        'couldThrow' => false,
        'isClosure' => false,
        'isGenerator' => false,
        'isVariadic' => false,
        'modifiers' => 2,
        'namespace' => 'Illuminate\\Database\\Eloquent\\Relations',
        'declaringClassName' => 'Illuminate\\Database\\Eloquent\\Relations\\Relation',
        'implementingClassName' => 'Illuminate\\Database\\Eloquent\\Relations\\Relation',
        'currentClassName' => 'Illuminate\\Database\\Eloquent\\Relations\\Relation',
        'aliasName' => NULL,
      ),
      'getRelationQuery' => 
      array (
        'name' => 'getRelationQuery',
        'parameters' => 
        array (
        ),
        'returnsReference' => false,
        'returnType' => NULL,
        'attributes' => 
        array (
        ),
        'docComment' => '/**
 * Get the query builder that will contain the relationship constraints.
 *
 * @return \\Illuminate\\Database\\Eloquent\\Builder<TRelatedModel>
 */',
        'startLine' => 376,
        'endLine' => 379,
        'startColumn' => 5,
        'endColumn' => 5,
        'couldThrow' => false,
        'isClosure' => false,
        'isGenerator' => false,
        'isVariadic' => false,
        'modifiers' => 2,
        'namespace' => 'Illuminate\\Database\\Eloquent\\Relations',
        'declaringClassName' => 'Illuminate\\Database\\Eloquent\\Relations\\Relation',
        'implementingClassName' => 'Illuminate\\Database\\Eloquent\\Relations\\Relation',
        'currentClassName' => 'Illuminate\\Database\\Eloquent\\Relations\\Relation',
        'aliasName' => NULL,
      ),
      'getQuery' => 
      array (
        'name' => 'getQuery',
        'parameters' => 
        array (
        ),
        'returnsReference' => false,
        'returnType' => NULL,
        'attributes' => 
        array (
        ),
        'docComment' => '/**
 * Get the underlying query for the relation.
 *
 * @return \\Illuminate\\Database\\Eloquent\\Builder<TRelatedModel>
 */',
        'startLine' => 386,
        'endLine' => 389,
        'startColumn' => 5,
        'endColumn' => 5,
        'couldThrow' => false,
        'isClosure' => false,
        'isGenerator' => false,
        'isVariadic' => false,
        'modifiers' => 1,
        'namespace' => 'Illuminate\\Database\\Eloquent\\Relations',
        'declaringClassName' => 'Illuminate\\Database\\Eloquent\\Relations\\Relation',
        'implementingClassName' => 'Illuminate\\Database\\Eloquent\\Relations\\Relation',
        'currentClassName' => 'Illuminate\\Database\\Eloquent\\Relations\\Relation',
        'aliasName' => NULL,
      ),
      'getBaseQuery' => 
      array (
        'name' => 'getBaseQuery',
        'parameters' => 
        array (
        ),
        'returnsReference' => false,
        'returnType' => NULL,
        'attributes' => 
        array (
        ),
        'docComment' => '/**
 * Get the base query builder driving the Eloquent builder.
 *
 * @return \\Illuminate\\Database\\Query\\Builder
 */',
        'startLine' => 396,
        'endLine' => 399,
        'startColumn' => 5,
        'endColumn' => 5,
        'couldThrow' => false,
        'isClosure' => false,
        'isGenerator' => false,
        'isVariadic' => false,
        'modifiers' => 1,
        'namespace' => 'Illuminate\\Database\\Eloquent\\Relations',
        'declaringClassName' => 'Illuminate\\Database\\Eloquent\\Relations\\Relation',
        'implementingClassName' => 'Illuminate\\Database\\Eloquent\\Relations\\Relation',
        'currentClassName' => 'Illuminate\\Database\\Eloquent\\Relations\\Relation',
        'aliasName' => NULL,
      ),
      'toBase' => 
      array (
        'name' => 'toBase',
        'parameters' => 
        array (
        ),
        'returnsReference' => false,
        'returnType' => NULL,
        'attributes' => 
        array (
        ),
        'docComment' => '/**
 * Get a base query builder instance.
 *
 * @return \\Illuminate\\Database\\Query\\Builder
 */',
        'startLine' => 406,
        'endLine' => 409,
        'startColumn' => 5,
        'endColumn' => 5,
        'couldThrow' => false,
        'isClosure' => false,
        'isGenerator' => false,
        'isVariadic' => false,
        'modifiers' => 1,
        'namespace' => 'Illuminate\\Database\\Eloquent\\Relations',
        'declaringClassName' => 'Illuminate\\Database\\Eloquent\\Relations\\Relation',
        'implementingClassName' => 'Illuminate\\Database\\Eloquent\\Relations\\Relation',
        'currentClassName' => 'Illuminate\\Database\\Eloquent\\Relations\\Relation',
        'aliasName' => NULL,
      ),
      'getParent' => 
      array (
        'name' => 'getParent',
        'parameters' => 
        array (
        ),
        'returnsReference' => false,
        'returnType' => NULL,
        'attributes' => 
        array (
        ),
        'docComment' => '/**
 * Get the parent model of the relation.
 *
 * @return TDeclaringModel
 */',
        'startLine' => 416,
        'endLine' => 419,
        'startColumn' => 5,
        'endColumn' => 5,
        'couldThrow' => false,
        'isClosure' => false,
        'isGenerator' => false,
        'isVariadic' => false,
        'modifiers' => 1,
        'namespace' => 'Illuminate\\Database\\Eloquent\\Relations',
        'declaringClassName' => 'Illuminate\\Database\\Eloquent\\Relations\\Relation',
        'implementingClassName' => 'Illuminate\\Database\\Eloquent\\Relations\\Relation',
        'currentClassName' => 'Illuminate\\Database\\Eloquent\\Relations\\Relation',
        'aliasName' => NULL,
      ),
      'getQualifiedParentKeyName' => 
      array (
        'name' => 'getQualifiedParentKeyName',
        'parameters' => 
        array (
        ),
        'returnsReference' => false,
        'returnType' => NULL,
        'attributes' => 
        array (
        ),
        'docComment' => '/**
 * Get the fully-qualified parent key name.
 *
 * @return string
 */',
        'startLine' => 426,
        'endLine' => 429,
        'startColumn' => 5,
        'endColumn' => 5,
        'couldThrow' => false,
        'isClosure' => false,
        'isGenerator' => false,
        'isVariadic' => false,
        'modifiers' => 1,
        'namespace' => 'Illuminate\\Database\\Eloquent\\Relations',
        'declaringClassName' => 'Illuminate\\Database\\Eloquent\\Relations\\Relation',
        'implementingClassName' => 'Illuminate\\Database\\Eloquent\\Relations\\Relation',
        'currentClassName' => 'Illuminate\\Database\\Eloquent\\Relations\\Relation',
        'aliasName' => NULL,
      ),
      'getRelated' => 
      array (
        'name' => 'getRelated',
        'parameters' => 
        array (
        ),
        'returnsReference' => false,
        'returnType' => NULL,
        'attributes' => 
        array (
        ),
        'docComment' => '/**
 * Get the related model of the relation.
 *
 * @return TRelatedModel
 */',
        'startLine' => 436,
        'endLine' => 439,
        'startColumn' => 5,
        'endColumn' => 5,
        'couldThrow' => false,
        'isClosure' => false,
        'isGenerator' => false,
        'isVariadic' => false,
        'modifiers' => 1,
        'namespace' => 'Illuminate\\Database\\Eloquent\\Relations',
        'declaringClassName' => 'Illuminate\\Database\\Eloquent\\Relations\\Relation',
        'implementingClassName' => 'Illuminate\\Database\\Eloquent\\Relations\\Relation',
        'currentClassName' => 'Illuminate\\Database\\Eloquent\\Relations\\Relation',
        'aliasName' => NULL,
      ),
      'getRelatedClass' => 
      array (
        'name' => 'getRelatedClass',
        'parameters' => 
        array (
        ),
        'returnsReference' => false,
        'returnType' => NULL,
        'attributes' => 
        array (
        ),
        'docComment' => '/**
 * Get the class name of the related model.
 *
 * @return class-string<TRelatedModel>
 */',
        'startLine' => 446,
        'endLine' => 449,
        'startColumn' => 5,
        'endColumn' => 5,
        'couldThrow' => false,
        'isClosure' => false,
        'isGenerator' => false,
        'isVariadic' => false,
        'modifiers' => 1,
        'namespace' => 'Illuminate\\Database\\Eloquent\\Relations',
        'declaringClassName' => 'Illuminate\\Database\\Eloquent\\Relations\\Relation',
        'implementingClassName' => 'Illuminate\\Database\\Eloquent\\Relations\\Relation',
        'currentClassName' => 'Illuminate\\Database\\Eloquent\\Relations\\Relation',
        'aliasName' => NULL,
      ),
      'createdAt' => 
      array (
        'name' => 'createdAt',
        'parameters' => 
        array (
        ),
        'returnsReference' => false,
        'returnType' => NULL,
        'attributes' => 
        array (
        ),
        'docComment' => '/**
 * Get the name of the "created at" column.
 *
 * @return string
 */',
        'startLine' => 456,
        'endLine' => 459,
        'startColumn' => 5,
        'endColumn' => 5,
        'couldThrow' => false,
        'isClosure' => false,
        'isGenerator' => false,
        'isVariadic' => false,
        'modifiers' => 1,
        'namespace' => 'Illuminate\\Database\\Eloquent\\Relations',
        'declaringClassName' => 'Illuminate\\Database\\Eloquent\\Relations\\Relation',
        'implementingClassName' => 'Illuminate\\Database\\Eloquent\\Relations\\Relation',
        'currentClassName' => 'Illuminate\\Database\\Eloquent\\Relations\\Relation',
        'aliasName' => NULL,
      ),
      'updatedAt' => 
      array (
        'name' => 'updatedAt',
        'parameters' => 
        array (
        ),
        'returnsReference' => false,
        'returnType' => NULL,
        'attributes' => 
        array (
        ),
        'docComment' => '/**
 * Get the name of the "updated at" column.
 *
 * @return string
 */',
        'startLine' => 466,
        'endLine' => 469,
        'startColumn' => 5,
        'endColumn' => 5,
        'couldThrow' => false,
        'isClosure' => false,
        'isGenerator' => false,
        'isVariadic' => false,
        'modifiers' => 1,
        'namespace' => 'Illuminate\\Database\\Eloquent\\Relations',
        'declaringClassName' => 'Illuminate\\Database\\Eloquent\\Relations\\Relation',
        'implementingClassName' => 'Illuminate\\Database\\Eloquent\\Relations\\Relation',
        'currentClassName' => 'Illuminate\\Database\\Eloquent\\Relations\\Relation',
        'aliasName' => NULL,
      ),
      'relatedUpdatedAt' => 
      array (
        'name' => 'relatedUpdatedAt',
        'parameters' => 
        array (
        ),
        'returnsReference' => false,
        'returnType' => NULL,
        'attributes' => 
        array (
        ),
        'docComment' => '/**
 * Get the name of the related model\'s "updated at" column.
 *
 * @return string
 */',
        'startLine' => 476,
        'endLine' => 479,
        'startColumn' => 5,
        'endColumn' => 5,
        'couldThrow' => false,
        'isClosure' => false,
        'isGenerator' => false,
        'isVariadic' => false,
        'modifiers' => 1,
        'namespace' => 'Illuminate\\Database\\Eloquent\\Relations',
        'declaringClassName' => 'Illuminate\\Database\\Eloquent\\Relations\\Relation',
        'implementingClassName' => 'Illuminate\\Database\\Eloquent\\Relations\\Relation',
        'currentClassName' => 'Illuminate\\Database\\Eloquent\\Relations\\Relation',
        'aliasName' => NULL,
      ),
      'whereInEager' => 
      array (
        'name' => 'whereInEager',
        'parameters' => 
        array (
          'whereIn' => 
          array (
            'name' => 'whereIn',
            'default' => NULL,
            'type' => 
            array (
              'class' => 'PHPStan\\BetterReflection\\Reflection\\ReflectionNamedType',
              'data' => 
              array (
                'name' => 'string',
                'isIdentifier' => true,
              ),
            ),
            'isVariadic' => false,
            'byRef' => false,
            'isPromoted' => false,
            'attributes' => 
            array (
            ),
            'startLine' => 490,
            'endLine' => 490,
            'startColumn' => 37,
            'endColumn' => 51,
            'parameterIndex' => 0,
            'isOptional' => false,
          ),
          'key' => 
          array (
            'name' => 'key',
            'default' => NULL,
            'type' => 
            array (
              'class' => 'PHPStan\\BetterReflection\\Reflection\\ReflectionNamedType',
              'data' => 
              array (
                'name' => 'string',
                'isIdentifier' => true,
              ),
            ),
            'isVariadic' => false,
            'byRef' => false,
            'isPromoted' => false,
            'attributes' => 
            array (
            ),
            'startLine' => 490,
            'endLine' => 490,
            'startColumn' => 54,
            'endColumn' => 64,
            'parameterIndex' => 1,
            'isOptional' => false,
          ),
          'modelKeys' => 
          array (
            'name' => 'modelKeys',
            'default' => NULL,
            'type' => 
            array (
              'class' => 'PHPStan\\BetterReflection\\Reflection\\ReflectionNamedType',
              'data' => 
              array (
                'name' => 'array',
                'isIdentifier' => true,
              ),
            ),
            'isVariadic' => false,
            'byRef' => false,
            'isPromoted' => false,
            'attributes' => 
            array (
            ),
            'startLine' => 490,
            'endLine' => 490,
            'startColumn' => 67,
            'endColumn' => 82,
            'parameterIndex' => 2,
            'isOptional' => false,
          ),
          'query' => 
          array (
            'name' => 'query',
            'default' => 
            array (
              'code' => 'null',
              'attributes' => 
              array (
                'startLine' => 490,
                'endLine' => 490,
                'startTokenPos' => 1454,
                'startFilePos' => 13217,
                'endTokenPos' => 1454,
                'endFilePos' => 13220,
              ),
            ),
            'type' => 
            array (
              'class' => 'PHPStan\\BetterReflection\\Reflection\\ReflectionUnionType',
              'data' => 
              array (
                'types' => 
                array (
                  0 => 
                  array (
                    'class' => 'PHPStan\\BetterReflection\\Reflection\\ReflectionNamedType',
                    'data' => 
                    array (
                      'name' => 'Illuminate\\Database\\Eloquent\\Builder',
                      'isIdentifier' => false,
                    ),
                  ),
                  1 => 
                  array (
                    'class' => 'PHPStan\\BetterReflection\\Reflection\\ReflectionNamedType',
                    'data' => 
                    array (
                      'name' => 'null',
                      'isIdentifier' => true,
                    ),
                  ),
                ),
              ),
            ),
            'isVariadic' => false,
            'byRef' => false,
            'isPromoted' => false,
            'attributes' => 
            array (
            ),
            'startLine' => 490,
            'endLine' => 490,
            'startColumn' => 85,
            'endColumn' => 106,
            'parameterIndex' => 3,
            'isOptional' => true,
          ),
        ),
        'returnsReference' => false,
        'returnType' => NULL,
        'attributes' => 
        array (
        ),
        'docComment' => '/**
 * Add a whereIn eager constraint for the given set of model keys to be loaded.
 *
 * @param  string  $whereIn
 * @param  string  $key
 * @param  array  $modelKeys
 * @param  \\Illuminate\\Database\\Eloquent\\Builder<TRelatedModel>|null  $query
 * @return void
 */',
        'startLine' => 490,
        'endLine' => 497,
        'startColumn' => 5,
        'endColumn' => 5,
        'couldThrow' => false,
        'isClosure' => false,
        'isGenerator' => false,
        'isVariadic' => false,
        'modifiers' => 2,
        'namespace' => 'Illuminate\\Database\\Eloquent\\Relations',
        'declaringClassName' => 'Illuminate\\Database\\Eloquent\\Relations\\Relation',
        'implementingClassName' => 'Illuminate\\Database\\Eloquent\\Relations\\Relation',
        'currentClassName' => 'Illuminate\\Database\\Eloquent\\Relations\\Relation',
        'aliasName' => NULL,
      ),
      'whereInMethod' => 
      array (
        'name' => 'whereInMethod',
        'parameters' => 
        array (
          'model' => 
          array (
            'name' => 'model',
            'default' => NULL,
            'type' => 
            array (
              'class' => 'PHPStan\\BetterReflection\\Reflection\\ReflectionNamedType',
              'data' => 
              array (
                'name' => 'Illuminate\\Database\\Eloquent\\Model',
                'isIdentifier' => false,
              ),
            ),
            'isVariadic' => false,
            'byRef' => false,
            'isPromoted' => false,
            'attributes' => 
            array (
            ),
            'startLine' => 506,
            'endLine' => 506,
            'startColumn' => 38,
            'endColumn' => 49,
            'parameterIndex' => 0,
            'isOptional' => false,
          ),
          'key' => 
          array (
            'name' => 'key',
            'default' => NULL,
            'type' => NULL,
            'isVariadic' => false,
            'byRef' => false,
            'isPromoted' => false,
            'attributes' => 
            array (
            ),
            'startLine' => 506,
            'endLine' => 506,
            'startColumn' => 52,
            'endColumn' => 55,
            'parameterIndex' => 1,
            'isOptional' => false,
          ),
        ),
        'returnsReference' => false,
        'returnType' => NULL,
        'attributes' => 
        array (
        ),
        'docComment' => '/**
 * Get the name of the "where in" method for eager loading.
 *
 * @param  \\Illuminate\\Database\\Eloquent\\Model  $model
 * @param  string  $key
 * @return string
 */',
        'startLine' => 506,
        'endLine' => 512,
        'startColumn' => 5,
        'endColumn' => 5,
        'couldThrow' => false,
        'isClosure' => false,
        'isGenerator' => false,
        'isVariadic' => false,
        'modifiers' => 2,
        'namespace' => 'Illuminate\\Database\\Eloquent\\Relations',
        'declaringClassName' => 'Illuminate\\Database\\Eloquent\\Relations\\Relation',
        'implementingClassName' => 'Illuminate\\Database\\Eloquent\\Relations\\Relation',
        'currentClassName' => 'Illuminate\\Database\\Eloquent\\Relations\\Relation',
        'aliasName' => NULL,
      ),
      'requireMorphMap' => 
      array (
        'name' => 'requireMorphMap',
        'parameters' => 
        array (
          'requireMorphMap' => 
          array (
            'name' => 'requireMorphMap',
            'default' => 
            array (
              'code' => 'true',
              'attributes' => 
              array (
                'startLine' => 520,
                'endLine' => 520,
                'startTokenPos' => 1589,
                'startFilePos' => 14078,
                'endTokenPos' => 1589,
                'endFilePos' => 14081,
              ),
            ),
            'type' => NULL,
            'isVariadic' => false,
            'byRef' => false,
            'isPromoted' => false,
            'attributes' => 
            array (
            ),
            'startLine' => 520,
            'endLine' => 520,
            'startColumn' => 44,
            'endColumn' => 66,
            'parameterIndex' => 0,
            'isOptional' => true,
          ),
        ),
        'returnsReference' => false,
        'returnType' => NULL,
        'attributes' => 
        array (
        ),
        'docComment' => '/**
 * Prevent polymorphic relationships from being used without model mappings.
 *
 * @param  bool  $requireMorphMap
 * @return void
 */',
        'startLine' => 520,
        'endLine' => 523,
        'startColumn' => 5,
        'endColumn' => 5,
        'couldThrow' => false,
        'isClosure' => false,
        'isGenerator' => false,
        'isVariadic' => false,
        'modifiers' => 17,
        'namespace' => 'Illuminate\\Database\\Eloquent\\Relations',
        'declaringClassName' => 'Illuminate\\Database\\Eloquent\\Relations\\Relation',
        'implementingClassName' => 'Illuminate\\Database\\Eloquent\\Relations\\Relation',
        'currentClassName' => 'Illuminate\\Database\\Eloquent\\Relations\\Relation',
        'aliasName' => NULL,
      ),
      'requiresMorphMap' => 
      array (
        'name' => 'requiresMorphMap',
        'parameters' => 
        array (
        ),
        'returnsReference' => false,
        'returnType' => NULL,
        'attributes' => 
        array (
        ),
        'docComment' => '/**
 * Determine if polymorphic relationships require explicit model mapping.
 *
 * @return bool
 */',
        'startLine' => 530,
        'endLine' => 533,
        'startColumn' => 5,
        'endColumn' => 5,
        'couldThrow' => false,
        'isClosure' => false,
        'isGenerator' => false,
        'isVariadic' => false,
        'modifiers' => 17,
        'namespace' => 'Illuminate\\Database\\Eloquent\\Relations',
        'declaringClassName' => 'Illuminate\\Database\\Eloquent\\Relations\\Relation',
        'implementingClassName' => 'Illuminate\\Database\\Eloquent\\Relations\\Relation',
        'currentClassName' => 'Illuminate\\Database\\Eloquent\\Relations\\Relation',
        'aliasName' => NULL,
      ),
      'enforceMorphMap' => 
      array (
        'name' => 'enforceMorphMap',
        'parameters' => 
        array (
          'map' => 
          array (
            'name' => 'map',
            'default' => NULL,
            'type' => 
            array (
              'class' => 'PHPStan\\BetterReflection\\Reflection\\ReflectionNamedType',
              'data' => 
              array (
                'name' => 'array',
                'isIdentifier' => true,
              ),
            ),
            'isVariadic' => false,
            'byRef' => false,
            'isPromoted' => false,
            'attributes' => 
            array (
            ),
            'startLine' => 542,
            'endLine' => 542,
            'startColumn' => 44,
            'endColumn' => 53,
            'parameterIndex' => 0,
            'isOptional' => false,
          ),
          'merge' => 
          array (
            'name' => 'merge',
            'default' => 
            array (
              'code' => 'true',
              'attributes' => 
              array (
                'startLine' => 542,
                'endLine' => 542,
                'startTokenPos' => 1647,
                'startFilePos' => 14706,
                'endTokenPos' => 1647,
                'endFilePos' => 14709,
              ),
            ),
            'type' => NULL,
            'isVariadic' => false,
            'byRef' => false,
            'isPromoted' => false,
            'attributes' => 
            array (
            ),
            'startLine' => 542,
            'endLine' => 542,
            'startColumn' => 56,
            'endColumn' => 68,
            'parameterIndex' => 1,
            'isOptional' => true,
          ),
        ),
        'returnsReference' => false,
        'returnType' => NULL,
        'attributes' => 
        array (
        ),
        'docComment' => '/**
 * Define the morph map for polymorphic relations and require all morphed models to be explicitly mapped.
 *
 * @param  array<array-key, class-string<\\Illuminate\\Database\\Eloquent\\Model>>  $map
 * @param  bool  $merge
 * @return array
 */',
        'startLine' => 542,
        'endLine' => 547,
        'startColumn' => 5,
        'endColumn' => 5,
        'couldThrow' => false,
        'isClosure' => false,
        'isGenerator' => false,
        'isVariadic' => false,
        'modifiers' => 17,
        'namespace' => 'Illuminate\\Database\\Eloquent\\Relations',
        'declaringClassName' => 'Illuminate\\Database\\Eloquent\\Relations\\Relation',
        'implementingClassName' => 'Illuminate\\Database\\Eloquent\\Relations\\Relation',
        'currentClassName' => 'Illuminate\\Database\\Eloquent\\Relations\\Relation',
        'aliasName' => NULL,
      ),
      'morphMap' => 
      array (
        'name' => 'morphMap',
        'parameters' => 
        array (
          'map' => 
          array (
            'name' => 'map',
            'default' => 
            array (
              'code' => 'null',
              'attributes' => 
              array (
                'startLine' => 556,
                'endLine' => 556,
                'startTokenPos' => 1691,
                'startFilePos' => 15142,
                'endTokenPos' => 1691,
                'endFilePos' => 15145,
              ),
            ),
            'type' => 
            array (
              'class' => 'PHPStan\\BetterReflection\\Reflection\\ReflectionUnionType',
              'data' => 
              array (
                'types' => 
                array (
                  0 => 
                  array (
                    'class' => 'PHPStan\\BetterReflection\\Reflection\\ReflectionNamedType',
                    'data' => 
                    array (
                      'name' => 'array',
                      'isIdentifier' => true,
                    ),
                  ),
                  1 => 
                  array (
                    'class' => 'PHPStan\\BetterReflection\\Reflection\\ReflectionNamedType',
                    'data' => 
                    array (
                      'name' => 'null',
                      'isIdentifier' => true,
                    ),
                  ),
                ),
              ),
            ),
            'isVariadic' => false,
            'byRef' => false,
            'isPromoted' => false,
            'attributes' => 
            array (
            ),
            'startLine' => 556,
            'endLine' => 556,
            'startColumn' => 37,
            'endColumn' => 54,
            'parameterIndex' => 0,
            'isOptional' => true,
          ),
          'merge' => 
          array (
            'name' => 'merge',
            'default' => 
            array (
              'code' => 'true',
              'attributes' => 
              array (
                'startLine' => 556,
                'endLine' => 556,
                'startTokenPos' => 1698,
                'startFilePos' => 15157,
                'endTokenPos' => 1698,
                'endFilePos' => 15160,
              ),
            ),
            'type' => NULL,
            'isVariadic' => false,
            'byRef' => false,
            'isPromoted' => false,
            'attributes' => 
            array (
            ),
            'startLine' => 556,
            'endLine' => 556,
            'startColumn' => 57,
            'endColumn' => 69,
            'parameterIndex' => 1,
            'isOptional' => true,
          ),
        ),
        'returnsReference' => false,
        'returnType' => NULL,
        'attributes' => 
        array (
        ),
        'docComment' => '/**
 * Set or get the morph map for polymorphic relations.
 *
 * @param  array<array-key, class-string<\\Illuminate\\Database\\Eloquent\\Model>>|null  $map
 * @param  bool  $merge
 * @return array<string, class-string<\\Illuminate\\Database\\Eloquent\\Model>>
 */',
        'startLine' => 556,
        'endLine' => 567,
        'startColumn' => 5,
        'endColumn' => 5,
        'couldThrow' => false,
        'isClosure' => false,
        'isGenerator' => false,
        'isVariadic' => false,
        'modifiers' => 17,
        'namespace' => 'Illuminate\\Database\\Eloquent\\Relations',
        'declaringClassName' => 'Illuminate\\Database\\Eloquent\\Relations\\Relation',
        'implementingClassName' => 'Illuminate\\Database\\Eloquent\\Relations\\Relation',
        'currentClassName' => 'Illuminate\\Database\\Eloquent\\Relations\\Relation',
        'aliasName' => NULL,
      ),
      'buildMorphMapFromModels' => 
      array (
        'name' => 'buildMorphMapFromModels',
        'parameters' => 
        array (
          'models' => 
          array (
            'name' => 'models',
            'default' => 
            array (
              'code' => 'null',
              'attributes' => 
              array (
                'startLine' => 575,
                'endLine' => 575,
                'startTokenPos' => 1783,
                'startFilePos' => 15767,
                'endTokenPos' => 1783,
                'endFilePos' => 15770,
              ),
            ),
            'type' => 
            array (
              'class' => 'PHPStan\\BetterReflection\\Reflection\\ReflectionUnionType',
              'data' => 
              array (
                'types' => 
                array (
                  0 => 
                  array (
                    'class' => 'PHPStan\\BetterReflection\\Reflection\\ReflectionNamedType',
                    'data' => 
                    array (
                      'name' => 'array',
                      'isIdentifier' => true,
                    ),
                  ),
                  1 => 
                  array (
                    'class' => 'PHPStan\\BetterReflection\\Reflection\\ReflectionNamedType',
                    'data' => 
                    array (
                      'name' => 'null',
                      'isIdentifier' => true,
                    ),
                  ),
                ),
              ),
            ),
            'isVariadic' => false,
            'byRef' => false,
            'isPromoted' => false,
            'attributes' => 
            array (
            ),
            'startLine' => 575,
            'endLine' => 575,
            'startColumn' => 55,
            'endColumn' => 75,
            'parameterIndex' => 0,
            'isOptional' => true,
          ),
        ),
        'returnsReference' => false,
        'returnType' => NULL,
        'attributes' => 
        array (
        ),
        'docComment' => '/**
 * Builds a table-keyed array from model class names.
 *
 * @param  array<array-key, class-string<\\Illuminate\\Database\\Eloquent\\Model>>|null  $models
 * @return array<string, class-string<\\Illuminate\\Database\\Eloquent\\Model>>|null
 */',
        'startLine' => 575,
        'endLine' => 584,
        'startColumn' => 5,
        'endColumn' => 5,
        'couldThrow' => false,
        'isClosure' => false,
        'isGenerator' => false,
        'isVariadic' => false,
        'modifiers' => 18,
        'namespace' => 'Illuminate\\Database\\Eloquent\\Relations',
        'declaringClassName' => 'Illuminate\\Database\\Eloquent\\Relations\\Relation',
        'implementingClassName' => 'Illuminate\\Database\\Eloquent\\Relations\\Relation',
        'currentClassName' => 'Illuminate\\Database\\Eloquent\\Relations\\Relation',
        'aliasName' => NULL,
      ),
      'getMorphedModel' => 
      array (
        'name' => 'getMorphedModel',
        'parameters' => 
        array (
          'alias' => 
          array (
            'name' => 'alias',
            'default' => NULL,
            'type' => NULL,
            'isVariadic' => false,
            'byRef' => false,
            'isPromoted' => false,
            'attributes' => 
            array (
            ),
            'startLine' => 592,
            'endLine' => 592,
            'startColumn' => 44,
            'endColumn' => 49,
            'parameterIndex' => 0,
            'isOptional' => false,
          ),
        ),
        'returnsReference' => false,
        'returnType' => NULL,
        'attributes' => 
        array (
        ),
        'docComment' => '/**
 * Get the model associated with a custom polymorphic type.
 *
 * @param  string|int|null  $alias
 * @return class-string<\\Illuminate\\Database\\Eloquent\\Model>|null
 */',
        'startLine' => 592,
        'endLine' => 599,
        'startColumn' => 5,
        'endColumn' => 5,
        'couldThrow' => false,
        'isClosure' => false,
        'isGenerator' => false,
        'isVariadic' => false,
        'modifiers' => 17,
        'namespace' => 'Illuminate\\Database\\Eloquent\\Relations',
        'declaringClassName' => 'Illuminate\\Database\\Eloquent\\Relations\\Relation',
        'implementingClassName' => 'Illuminate\\Database\\Eloquent\\Relations\\Relation',
        'currentClassName' => 'Illuminate\\Database\\Eloquent\\Relations\\Relation',
        'aliasName' => NULL,
      ),
      'getMorphAlias' => 
      array (
        'name' => 'getMorphAlias',
        'parameters' => 
        array (
          'className' => 
          array (
            'name' => 'className',
            'default' => NULL,
            'type' => 
            array (
              'class' => 'PHPStan\\BetterReflection\\Reflection\\ReflectionNamedType',
              'data' => 
              array (
                'name' => 'string',
                'isIdentifier' => true,
              ),
            ),
            'isVariadic' => false,
            'byRef' => false,
            'isPromoted' => false,
            'attributes' => 
            array (
            ),
            'startLine' => 607,
            'endLine' => 607,
            'startColumn' => 42,
            'endColumn' => 58,
            'parameterIndex' => 0,
            'isOptional' => false,
          ),
        ),
        'returnsReference' => false,
        'returnType' => NULL,
        'attributes' => 
        array (
        ),
        'docComment' => '/**
 * Get the alias associated with a custom polymorphic class.
 *
 * @param  class-string<\\Illuminate\\Database\\Eloquent\\Model>  $className
 * @return int|string
 */',
        'startLine' => 607,
        'endLine' => 610,
        'startColumn' => 5,
        'endColumn' => 5,
        'couldThrow' => false,
        'isClosure' => false,
        'isGenerator' => false,
        'isVariadic' => false,
        'modifiers' => 17,
        'namespace' => 'Illuminate\\Database\\Eloquent\\Relations',
        'declaringClassName' => 'Illuminate\\Database\\Eloquent\\Relations\\Relation',
        'implementingClassName' => 'Illuminate\\Database\\Eloquent\\Relations\\Relation',
        'currentClassName' => 'Illuminate\\Database\\Eloquent\\Relations\\Relation',
        'aliasName' => NULL,
      ),
      '__call' => 
      array (
        'name' => '__call',
        'parameters' => 
        array (
          'method' => 
          array (
            'name' => 'method',
            'default' => NULL,
            'type' => NULL,
            'isVariadic' => false,
            'byRef' => false,
            'isPromoted' => false,
            'attributes' => 
            array (
            ),
            'startLine' => 619,
            'endLine' => 619,
            'startColumn' => 28,
            'endColumn' => 34,
            'parameterIndex' => 0,
            'isOptional' => false,
          ),
          'parameters' => 
          array (
            'name' => 'parameters',
            'default' => NULL,
            'type' => NULL,
            'isVariadic' => false,
            'byRef' => false,
            'isPromoted' => false,
            'attributes' => 
            array (
            ),
            'startLine' => 619,
            'endLine' => 619,
            'startColumn' => 37,
            'endColumn' => 47,
            'parameterIndex' => 1,
            'isOptional' => false,
          ),
        ),
        'returnsReference' => false,
        'returnType' => NULL,
        'attributes' => 
        array (
        ),
        'docComment' => '/**
 * Handle dynamic method calls to the relationship.
 *
 * @param  string  $method
 * @param  array  $parameters
 * @return mixed
 */',
        'startLine' => 619,
        'endLine' => 626,
        'startColumn' => 5,
        'endColumn' => 5,
        'couldThrow' => false,
        'isClosure' => false,
        'isGenerator' => false,
        'isVariadic' => false,
        'modifiers' => 1,
        'namespace' => 'Illuminate\\Database\\Eloquent\\Relations',
        'declaringClassName' => 'Illuminate\\Database\\Eloquent\\Relations\\Relation',
        'implementingClassName' => 'Illuminate\\Database\\Eloquent\\Relations\\Relation',
        'currentClassName' => 'Illuminate\\Database\\Eloquent\\Relations\\Relation',
        'aliasName' => NULL,
      ),
      '__clone' => 
      array (
        'name' => '__clone',
        'parameters' => 
        array (
        ),
        'returnsReference' => false,
        'returnType' => NULL,
        'attributes' => 
        array (
        ),
        'docComment' => '/**
 * Force a clone of the underlying query builder when cloning.
 *
 * @return void
 */',
        'startLine' => 633,
        'endLine' => 636,
        'startColumn' => 5,
        'endColumn' => 5,
        'couldThrow' => false,
        'isClosure' => false,
        'isGenerator' => false,
        'isVariadic' => false,
        'modifiers' => 1,
        'namespace' => 'Illuminate\\Database\\Eloquent\\Relations',
        'declaringClassName' => 'Illuminate\\Database\\Eloquent\\Relations\\Relation',
        'implementingClassName' => 'Illuminate\\Database\\Eloquent\\Relations\\Relation',
        'currentClassName' => 'Illuminate\\Database\\Eloquent\\Relations\\Relation',
        'aliasName' => NULL,
      ),
    ),
    'traitsData' => 
    array (
      'aliases' => 
      array (
        'Illuminate\\Support\\Traits\\Macroable' => 
        array (
          0 => 
          array (
            'alias' => 'macroCall',
            'method' => '__call',
            'hash' => 'illuminate\\support\\traits\\macroable::__call',
          ),
        ),
      ),
      'modifiers' => 
      array (
      ),
      'precedences' => 
      array (
      ),
      'hashes' => 
      array (
        'illuminate\\support\\traits\\macroable::__call' => 'Illuminate\\Support\\Traits\\Macroable::__call',
      ),
    ),
  ),
));