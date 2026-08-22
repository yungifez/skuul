<?php declare(strict_types = 1);

// odsl-/var/www/html/app/Traits/HasPeriodLifecycle.php-PHPStan\BetterReflection\Reflection\ReflectionClass-App\Traits\HasPeriodLifecycle
return \PHPStan\Cache\CacheItem::__set_state(array(
   'variableKey' => 'v2-6.70.0.3-8.5.9-b4e38dc353ad6742199d90621eaee18623bc21e5340f17e59ab23744458bd7a8',
   'data' => 
  array (
    'locatedSource' => 
    array (
      'class' => 'PHPStan\\BetterReflection\\SourceLocator\\Located\\LocatedSource',
      'data' => 
      array (
        'name' => 'App\\Traits\\HasPeriodLifecycle',
        'filename' => '/var/www/html/app/Traits/HasPeriodLifecycle.php',
      ),
    ),
    'namespace' => 'App\\Traits',
    'name' => 'App\\Traits\\HasPeriodLifecycle',
    'shortName' => 'HasPeriodLifecycle',
    'isInterface' => false,
    'isTrait' => true,
    'isEnum' => false,
    'isBackedEnum' => false,
    'modifiers' => 0,
    'docComment' => '/**
 * Shared behaviour for academic years and academic periods.
 *
 * Both are academic periods: they open, they close, and the change is kept.
 */',
    'attributes' => 
    array (
    ),
    'startLine' => 15,
    'endLine' => 139,
    'startColumn' => 1,
    'endColumn' => 1,
    'parentClassName' => NULL,
    'implementsClassNames' => 
    array (
    ),
    'traitClassNames' => 
    array (
    ),
    'immediateConstants' => 
    array (
    ),
    'immediateProperties' => 
    array (
    ),
    'immediateMethods' => 
    array (
      'statusChanges' => 
      array (
        'name' => 'statusChanges',
        'parameters' => 
        array (
        ),
        'returnsReference' => false,
        'returnType' => 
        array (
          'class' => 'PHPStan\\BetterReflection\\Reflection\\ReflectionNamedType',
          'data' => 
          array (
            'name' => 'Illuminate\\Database\\Eloquent\\Relations\\MorphMany',
            'isIdentifier' => false,
          ),
        ),
        'attributes' => 
        array (
        ),
        'docComment' => '/**
 * Get every recorded state change of this period.
 */',
        'startLine' => 20,
        'endLine' => 23,
        'startColumn' => 5,
        'endColumn' => 5,
        'couldThrow' => false,
        'isClosure' => false,
        'isGenerator' => false,
        'isVariadic' => false,
        'modifiers' => 1,
        'namespace' => 'App\\Traits',
        'declaringClassName' => 'App\\Traits\\HasPeriodLifecycle',
        'implementingClassName' => 'App\\Traits\\HasPeriodLifecycle',
        'currentClassName' => 'App\\Traits\\HasPeriodLifecycle',
        'aliasName' => NULL,
      ),
      'scopeOpen' => 
      array (
        'name' => 'scopeOpen',
        'parameters' => 
        array (
          'query' => 
          array (
            'name' => 'query',
            'default' => NULL,
            'type' => NULL,
            'isVariadic' => false,
            'byRef' => false,
            'isPromoted' => false,
            'attributes' => 
            array (
            ),
            'startLine' => 30,
            'endLine' => 30,
            'startColumn' => 31,
            'endColumn' => 36,
            'parameterIndex' => 0,
            'isOptional' => false,
          ),
        ),
        'returnsReference' => false,
        'returnType' => 
        array (
          'class' => 'PHPStan\\BetterReflection\\Reflection\\ReflectionNamedType',
          'data' => 
          array (
            'name' => 'Illuminate\\Database\\Eloquent\\Builder',
            'isIdentifier' => false,
          ),
        ),
        'attributes' => 
        array (
        ),
        'docComment' => '/**
 * Limit the query to periods that still accept writes.
 *
 * @param  Builder  $query
 */',
        'startLine' => 30,
        'endLine' => 33,
        'startColumn' => 5,
        'endColumn' => 5,
        'couldThrow' => false,
        'isClosure' => false,
        'isGenerator' => false,
        'isVariadic' => false,
        'modifiers' => 1,
        'namespace' => 'App\\Traits',
        'declaringClassName' => 'App\\Traits\\HasPeriodLifecycle',
        'implementingClassName' => 'App\\Traits\\HasPeriodLifecycle',
        'currentClassName' => 'App\\Traits\\HasPeriodLifecycle',
        'aliasName' => NULL,
      ),
      'scopeClosed' => 
      array (
        'name' => 'scopeClosed',
        'parameters' => 
        array (
          'query' => 
          array (
            'name' => 'query',
            'default' => NULL,
            'type' => NULL,
            'isVariadic' => false,
            'byRef' => false,
            'isPromoted' => false,
            'attributes' => 
            array (
            ),
            'startLine' => 40,
            'endLine' => 40,
            'startColumn' => 33,
            'endColumn' => 38,
            'parameterIndex' => 0,
            'isOptional' => false,
          ),
        ),
        'returnsReference' => false,
        'returnType' => 
        array (
          'class' => 'PHPStan\\BetterReflection\\Reflection\\ReflectionNamedType',
          'data' => 
          array (
            'name' => 'Illuminate\\Database\\Eloquent\\Builder',
            'isIdentifier' => false,
          ),
        ),
        'attributes' => 
        array (
        ),
        'docComment' => '/**
 * Limit the query to finished periods, archived ones included.
 *
 * @param  Builder  $query
 */',
        'startLine' => 40,
        'endLine' => 46,
        'startColumn' => 5,
        'endColumn' => 5,
        'couldThrow' => false,
        'isClosure' => false,
        'isGenerator' => false,
        'isVariadic' => false,
        'modifiers' => 1,
        'namespace' => 'App\\Traits',
        'declaringClassName' => 'App\\Traits\\HasPeriodLifecycle',
        'implementingClassName' => 'App\\Traits\\HasPeriodLifecycle',
        'currentClassName' => 'App\\Traits\\HasPeriodLifecycle',
        'aliasName' => NULL,
      ),
      'scopeArchived' => 
      array (
        'name' => 'scopeArchived',
        'parameters' => 
        array (
          'query' => 
          array (
            'name' => 'query',
            'default' => NULL,
            'type' => NULL,
            'isVariadic' => false,
            'byRef' => false,
            'isPromoted' => false,
            'attributes' => 
            array (
            ),
            'startLine' => 53,
            'endLine' => 53,
            'startColumn' => 35,
            'endColumn' => 40,
            'parameterIndex' => 0,
            'isOptional' => false,
          ),
        ),
        'returnsReference' => false,
        'returnType' => 
        array (
          'class' => 'PHPStan\\BetterReflection\\Reflection\\ReflectionNamedType',
          'data' => 
          array (
            'name' => 'Illuminate\\Database\\Eloquent\\Builder',
            'isIdentifier' => false,
          ),
        ),
        'attributes' => 
        array (
        ),
        'docComment' => '/**
 * Limit the query to periods kept for history only.
 *
 * @param  Builder  $query
 */',
        'startLine' => 53,
        'endLine' => 56,
        'startColumn' => 5,
        'endColumn' => 5,
        'couldThrow' => false,
        'isClosure' => false,
        'isGenerator' => false,
        'isVariadic' => false,
        'modifiers' => 1,
        'namespace' => 'App\\Traits',
        'declaringClassName' => 'App\\Traits\\HasPeriodLifecycle',
        'implementingClassName' => 'App\\Traits\\HasPeriodLifecycle',
        'currentClassName' => 'App\\Traits\\HasPeriodLifecycle',
        'aliasName' => NULL,
      ),
      'scopeOperational' => 
      array (
        'name' => 'scopeOperational',
        'parameters' => 
        array (
          'query' => 
          array (
            'name' => 'query',
            'default' => NULL,
            'type' => NULL,
            'isVariadic' => false,
            'byRef' => false,
            'isPromoted' => false,
            'attributes' => 
            array (
            ),
            'startLine' => 63,
            'endLine' => 63,
            'startColumn' => 38,
            'endColumn' => 43,
            'parameterIndex' => 0,
            'isOptional' => false,
          ),
        ),
        'returnsReference' => false,
        'returnType' => 
        array (
          'class' => 'PHPStan\\BetterReflection\\Reflection\\ReflectionNamedType',
          'data' => 
          array (
            'name' => 'Illuminate\\Database\\Eloquent\\Builder',
            'isIdentifier' => false,
          ),
        ),
        'attributes' => 
        array (
        ),
        'docComment' => '/**
 * Limit the query to periods routine operations may still write to.
 *
 * @param  Builder  $query
 */',
        'startLine' => 63,
        'endLine' => 69,
        'startColumn' => 5,
        'endColumn' => 5,
        'couldThrow' => false,
        'isClosure' => false,
        'isGenerator' => false,
        'isVariadic' => false,
        'modifiers' => 1,
        'namespace' => 'App\\Traits',
        'declaringClassName' => 'App\\Traits\\HasPeriodLifecycle',
        'implementingClassName' => 'App\\Traits\\HasPeriodLifecycle',
        'currentClassName' => 'App\\Traits\\HasPeriodLifecycle',
        'aliasName' => NULL,
      ),
      'scopeScheduled' => 
      array (
        'name' => 'scopeScheduled',
        'parameters' => 
        array (
          'query' => 
          array (
            'name' => 'query',
            'default' => NULL,
            'type' => NULL,
            'isVariadic' => false,
            'byRef' => false,
            'isPromoted' => false,
            'attributes' => 
            array (
            ),
            'startLine' => 76,
            'endLine' => 76,
            'startColumn' => 36,
            'endColumn' => 41,
            'parameterIndex' => 0,
            'isOptional' => false,
          ),
        ),
        'returnsReference' => false,
        'returnType' => 
        array (
          'class' => 'PHPStan\\BetterReflection\\Reflection\\ReflectionNamedType',
          'data' => 
          array (
            'name' => 'Illuminate\\Database\\Eloquent\\Builder',
            'isIdentifier' => false,
          ),
        ),
        'attributes' => 
        array (
        ),
        'docComment' => '/**
 * Limit the query to periods that are dated but have not started.
 *
 * @param  Builder  $query
 */',
        'startLine' => 76,
        'endLine' => 79,
        'startColumn' => 5,
        'endColumn' => 5,
        'couldThrow' => false,
        'isClosure' => false,
        'isGenerator' => false,
        'isVariadic' => false,
        'modifiers' => 1,
        'namespace' => 'App\\Traits',
        'declaringClassName' => 'App\\Traits\\HasPeriodLifecycle',
        'implementingClassName' => 'App\\Traits\\HasPeriodLifecycle',
        'currentClassName' => 'App\\Traits\\HasPeriodLifecycle',
        'aliasName' => NULL,
      ),
      'isOpen' => 
      array (
        'name' => 'isOpen',
        'parameters' => 
        array (
        ),
        'returnsReference' => false,
        'returnType' => 
        array (
          'class' => 'PHPStan\\BetterReflection\\Reflection\\ReflectionNamedType',
          'data' => 
          array (
            'name' => 'bool',
            'isIdentifier' => true,
          ),
        ),
        'attributes' => 
        array (
        ),
        'docComment' => '/**
 * Check if records of this period can still be written.
 */',
        'startLine' => 84,
        'endLine' => 87,
        'startColumn' => 5,
        'endColumn' => 5,
        'couldThrow' => false,
        'isClosure' => false,
        'isGenerator' => false,
        'isVariadic' => false,
        'modifiers' => 1,
        'namespace' => 'App\\Traits',
        'declaringClassName' => 'App\\Traits\\HasPeriodLifecycle',
        'implementingClassName' => 'App\\Traits\\HasPeriodLifecycle',
        'currentClassName' => 'App\\Traits\\HasPeriodLifecycle',
        'aliasName' => NULL,
      ),
      'isClosed' => 
      array (
        'name' => 'isClosed',
        'parameters' => 
        array (
        ),
        'returnsReference' => false,
        'returnType' => 
        array (
          'class' => 'PHPStan\\BetterReflection\\Reflection\\ReflectionNamedType',
          'data' => 
          array (
            'name' => 'bool',
            'isIdentifier' => true,
          ),
        ),
        'attributes' => 
        array (
        ),
        'docComment' => '/**
 * Check if the period is finished.
 *
 * An archived period is closed too: it stopped accepting work when it was
 * closed, and archiving only moved it out of the way.
 */',
        'startLine' => 95,
        'endLine' => 98,
        'startColumn' => 5,
        'endColumn' => 5,
        'couldThrow' => false,
        'isClosure' => false,
        'isGenerator' => false,
        'isVariadic' => false,
        'modifiers' => 1,
        'namespace' => 'App\\Traits',
        'declaringClassName' => 'App\\Traits\\HasPeriodLifecycle',
        'implementingClassName' => 'App\\Traits\\HasPeriodLifecycle',
        'currentClassName' => 'App\\Traits\\HasPeriodLifecycle',
        'aliasName' => NULL,
      ),
      'isClosing' => 
      array (
        'name' => 'isClosing',
        'parameters' => 
        array (
        ),
        'returnsReference' => false,
        'returnType' => 
        array (
          'class' => 'PHPStan\\BetterReflection\\Reflection\\ReflectionNamedType',
          'data' => 
          array (
            'name' => 'bool',
            'isIdentifier' => true,
          ),
        ),
        'attributes' => 
        array (
        ),
        'docComment' => '/**
 * Check if the period is finishing, so only started work may continue.
 */',
        'startLine' => 103,
        'endLine' => 106,
        'startColumn' => 5,
        'endColumn' => 5,
        'couldThrow' => false,
        'isClosure' => false,
        'isGenerator' => false,
        'isVariadic' => false,
        'modifiers' => 1,
        'namespace' => 'App\\Traits',
        'declaringClassName' => 'App\\Traits\\HasPeriodLifecycle',
        'implementingClassName' => 'App\\Traits\\HasPeriodLifecycle',
        'currentClassName' => 'App\\Traits\\HasPeriodLifecycle',
        'aliasName' => NULL,
      ),
      'isArchived' => 
      array (
        'name' => 'isArchived',
        'parameters' => 
        array (
        ),
        'returnsReference' => false,
        'returnType' => 
        array (
          'class' => 'PHPStan\\BetterReflection\\Reflection\\ReflectionNamedType',
          'data' => 
          array (
            'name' => 'bool',
            'isIdentifier' => true,
          ),
        ),
        'attributes' => 
        array (
        ),
        'docComment' => '/**
 * Check if the period is kept for history only.
 */',
        'startLine' => 111,
        'endLine' => 114,
        'startColumn' => 5,
        'endColumn' => 5,
        'couldThrow' => false,
        'isClosure' => false,
        'isGenerator' => false,
        'isVariadic' => false,
        'modifiers' => 1,
        'namespace' => 'App\\Traits',
        'declaringClassName' => 'App\\Traits\\HasPeriodLifecycle',
        'implementingClassName' => 'App\\Traits\\HasPeriodLifecycle',
        'currentClassName' => 'App\\Traits\\HasPeriodLifecycle',
        'aliasName' => NULL,
      ),
      'isOperational' => 
      array (
        'name' => 'isOperational',
        'parameters' => 
        array (
        ),
        'returnsReference' => false,
        'returnType' => 
        array (
          'class' => 'PHPStan\\BetterReflection\\Reflection\\ReflectionNamedType',
          'data' => 
          array (
            'name' => 'bool',
            'isIdentifier' => true,
          ),
        ),
        'attributes' => 
        array (
        ),
        'docComment' => '/**
 * Check if routine operations run against this period.
 */',
        'startLine' => 119,
        'endLine' => 122,
        'startColumn' => 5,
        'endColumn' => 5,
        'couldThrow' => false,
        'isClosure' => false,
        'isGenerator' => false,
        'isVariadic' => false,
        'modifiers' => 1,
        'namespace' => 'App\\Traits',
        'declaringClassName' => 'App\\Traits\\HasPeriodLifecycle',
        'implementingClassName' => 'App\\Traits\\HasPeriodLifecycle',
        'currentClassName' => 'App\\Traits\\HasPeriodLifecycle',
        'aliasName' => NULL,
      ),
      'acceptsNewWork' => 
      array (
        'name' => 'acceptsNewWork',
        'parameters' => 
        array (
        ),
        'returnsReference' => false,
        'returnType' => 
        array (
          'class' => 'PHPStan\\BetterReflection\\Reflection\\ReflectionNamedType',
          'data' => 
          array (
            'name' => 'bool',
            'isIdentifier' => true,
          ),
        ),
        'attributes' => 
        array (
        ),
        'docComment' => '/**
 * Check if the period accepts work that did not exist before.
 */',
        'startLine' => 127,
        'endLine' => 130,
        'startColumn' => 5,
        'endColumn' => 5,
        'couldThrow' => false,
        'isClosure' => false,
        'isGenerator' => false,
        'isVariadic' => false,
        'modifiers' => 1,
        'namespace' => 'App\\Traits',
        'declaringClassName' => 'App\\Traits\\HasPeriodLifecycle',
        'implementingClassName' => 'App\\Traits\\HasPeriodLifecycle',
        'currentClassName' => 'App\\Traits\\HasPeriodLifecycle',
        'aliasName' => NULL,
      ),
      'statusLabel' => 
      array (
        'name' => 'statusLabel',
        'parameters' => 
        array (
        ),
        'returnsReference' => false,
        'returnType' => 
        array (
          'class' => 'PHPStan\\BetterReflection\\Reflection\\ReflectionNamedType',
          'data' => 
          array (
            'name' => 'string',
            'isIdentifier' => true,
          ),
        ),
        'attributes' => 
        array (
        ),
        'docComment' => '/**
 * Get the label to show in the interface.
 */',
        'startLine' => 135,
        'endLine' => 138,
        'startColumn' => 5,
        'endColumn' => 5,
        'couldThrow' => false,
        'isClosure' => false,
        'isGenerator' => false,
        'isVariadic' => false,
        'modifiers' => 1,
        'namespace' => 'App\\Traits',
        'declaringClassName' => 'App\\Traits\\HasPeriodLifecycle',
        'implementingClassName' => 'App\\Traits\\HasPeriodLifecycle',
        'currentClassName' => 'App\\Traits\\HasPeriodLifecycle',
        'aliasName' => NULL,
      ),
    ),
    'traitsData' => 
    array (
      'aliases' => 
      array (
      ),
      'modifiers' => 
      array (
      ),
      'precedences' => 
      array (
      ),
      'hashes' => 
      array (
      ),
    ),
  ),
));