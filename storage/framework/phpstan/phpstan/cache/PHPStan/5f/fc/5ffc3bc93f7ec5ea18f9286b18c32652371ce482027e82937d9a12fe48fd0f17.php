<?php declare(strict_types = 1);

// osfsl-/var/www/html/vendor/composer/../laravel/framework/src/Illuminate/Validation/Rule.php-PHPStan\BetterReflection\Reflection\ReflectionClass-Illuminate\Validation\Rule
return \PHPStan\Cache\CacheItem::__set_state(array(
   'variableKey' => 'v2-b1e6c929528196ee69500fc26be0c0b6e7ae93de895c1eb7e3b15457b0c6eab0-8.5.9-6.70.0.3',
   'data' => 
  array (
    'locatedSource' => 
    array (
      'class' => 'PHPStan\\BetterReflection\\SourceLocator\\Located\\LocatedSource',
      'data' => 
      array (
        'name' => 'Illuminate\\Validation\\Rule',
        'filename' => '/var/www/html/vendor/composer/../laravel/framework/src/Illuminate/Validation/Rule.php',
      ),
    ),
    'namespace' => 'Illuminate\\Validation',
    'name' => 'Illuminate\\Validation\\Rule',
    'shortName' => 'Rule',
    'isInterface' => false,
    'isTrait' => false,
    'isEnum' => false,
    'isBackedEnum' => false,
    'modifiers' => 0,
    'docComment' => NULL,
    'attributes' => 
    array (
    ),
    'startLine' => 31,
    'endLine' => 388,
    'startColumn' => 1,
    'endColumn' => 1,
    'parentClassName' => NULL,
    'implementsClassNames' => 
    array (
    ),
    'traitClassNames' => 
    array (
      0 => 'Illuminate\\Support\\Traits\\Macroable',
    ),
    'immediateConstants' => 
    array (
    ),
    'immediateProperties' => 
    array (
    ),
    'immediateMethods' => 
    array (
      'can' => 
      array (
        'name' => 'can',
        'parameters' => 
        array (
          'ability' => 
          array (
            'name' => 'ability',
            'default' => NULL,
            'type' => NULL,
            'isVariadic' => false,
            'byRef' => false,
            'isPromoted' => false,
            'attributes' => 
            array (
            ),
            'startLine' => 42,
            'endLine' => 42,
            'startColumn' => 32,
            'endColumn' => 39,
            'parameterIndex' => 0,
            'isOptional' => false,
          ),
          'arguments' => 
          array (
            'name' => 'arguments',
            'default' => NULL,
            'type' => NULL,
            'isVariadic' => true,
            'byRef' => false,
            'isPromoted' => false,
            'attributes' => 
            array (
            ),
            'startLine' => 42,
            'endLine' => 42,
            'startColumn' => 42,
            'endColumn' => 54,
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
 * Get a can constraint builder instance.
 *
 * @param  string  $ability
 * @param  mixed  ...$arguments
 * @return \\Illuminate\\Validation\\Rules\\Can
 */',
        'startLine' => 42,
        'endLine' => 45,
        'startColumn' => 5,
        'endColumn' => 5,
        'couldThrow' => false,
        'isClosure' => false,
        'isGenerator' => false,
        'isVariadic' => true,
        'modifiers' => 17,
        'namespace' => 'Illuminate\\Validation',
        'declaringClassName' => 'Illuminate\\Validation\\Rule',
        'implementingClassName' => 'Illuminate\\Validation\\Rule',
        'currentClassName' => 'Illuminate\\Validation\\Rule',
        'aliasName' => NULL,
      ),
      'when' => 
      array (
        'name' => 'when',
        'parameters' => 
        array (
          'condition' => 
          array (
            'name' => 'condition',
            'default' => NULL,
            'type' => NULL,
            'isVariadic' => false,
            'byRef' => false,
            'isPromoted' => false,
            'attributes' => 
            array (
            ),
            'startLine' => 55,
            'endLine' => 55,
            'startColumn' => 33,
            'endColumn' => 42,
            'parameterIndex' => 0,
            'isOptional' => false,
          ),
          'rules' => 
          array (
            'name' => 'rules',
            'default' => NULL,
            'type' => NULL,
            'isVariadic' => false,
            'byRef' => false,
            'isPromoted' => false,
            'attributes' => 
            array (
            ),
            'startLine' => 55,
            'endLine' => 55,
            'startColumn' => 45,
            'endColumn' => 50,
            'parameterIndex' => 1,
            'isOptional' => false,
          ),
          'defaultRules' => 
          array (
            'name' => 'defaultRules',
            'default' => 
            array (
              'code' => '[]',
              'attributes' => 
              array (
                'startLine' => 55,
                'endLine' => 55,
                'startTokenPos' => 197,
                'startFilePos' => 2021,
                'endTokenPos' => 198,
                'endFilePos' => 2022,
              ),
            ),
            'type' => NULL,
            'isVariadic' => false,
            'byRef' => false,
            'isPromoted' => false,
            'attributes' => 
            array (
            ),
            'startLine' => 55,
            'endLine' => 55,
            'startColumn' => 53,
            'endColumn' => 70,
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
 * Apply the given rules if the given condition is truthy.
 *
 * @param  callable|bool  $condition
 * @param  \\Illuminate\\Contracts\\Validation\\ValidationRule|\\Illuminate\\Contracts\\Validation\\InvokableRule|\\Illuminate\\Contracts\\Validation\\Rule|\\Closure|array|string  $rules
 * @param  \\Illuminate\\Contracts\\Validation\\ValidationRule|\\Illuminate\\Contracts\\Validation\\InvokableRule|\\Illuminate\\Contracts\\Validation\\Rule|\\Closure|array|string  $defaultRules
 * @return \\Illuminate\\Validation\\ConditionalRules
 */',
        'startLine' => 55,
        'endLine' => 58,
        'startColumn' => 5,
        'endColumn' => 5,
        'couldThrow' => false,
        'isClosure' => false,
        'isGenerator' => false,
        'isVariadic' => false,
        'modifiers' => 17,
        'namespace' => 'Illuminate\\Validation',
        'declaringClassName' => 'Illuminate\\Validation\\Rule',
        'implementingClassName' => 'Illuminate\\Validation\\Rule',
        'currentClassName' => 'Illuminate\\Validation\\Rule',
        'aliasName' => NULL,
      ),
      'unless' => 
      array (
        'name' => 'unless',
        'parameters' => 
        array (
          'condition' => 
          array (
            'name' => 'condition',
            'default' => NULL,
            'type' => NULL,
            'isVariadic' => false,
            'byRef' => false,
            'isPromoted' => false,
            'attributes' => 
            array (
            ),
            'startLine' => 68,
            'endLine' => 68,
            'startColumn' => 35,
            'endColumn' => 44,
            'parameterIndex' => 0,
            'isOptional' => false,
          ),
          'rules' => 
          array (
            'name' => 'rules',
            'default' => NULL,
            'type' => NULL,
            'isVariadic' => false,
            'byRef' => false,
            'isPromoted' => false,
            'attributes' => 
            array (
            ),
            'startLine' => 68,
            'endLine' => 68,
            'startColumn' => 47,
            'endColumn' => 52,
            'parameterIndex' => 1,
            'isOptional' => false,
          ),
          'defaultRules' => 
          array (
            'name' => 'defaultRules',
            'default' => 
            array (
              'code' => '[]',
              'attributes' => 
              array (
                'startLine' => 68,
                'endLine' => 68,
                'startTokenPos' => 241,
                'startFilePos' => 2724,
                'endTokenPos' => 242,
                'endFilePos' => 2725,
              ),
            ),
            'type' => NULL,
            'isVariadic' => false,
            'byRef' => false,
            'isPromoted' => false,
            'attributes' => 
            array (
            ),
            'startLine' => 68,
            'endLine' => 68,
            'startColumn' => 55,
            'endColumn' => 72,
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
 * Apply the given rules if the given condition is falsy.
 *
 * @param  callable|bool  $condition
 * @param  \\Illuminate\\Contracts\\Validation\\ValidationRule|\\Illuminate\\Contracts\\Validation\\InvokableRule|\\Illuminate\\Contracts\\Validation\\Rule|\\Closure|array|string  $rules
 * @param  \\Illuminate\\Contracts\\Validation\\ValidationRule|\\Illuminate\\Contracts\\Validation\\InvokableRule|\\Illuminate\\Contracts\\Validation\\Rule|\\Closure|array|string  $defaultRules
 * @return \\Illuminate\\Validation\\ConditionalRules
 */',
        'startLine' => 68,
        'endLine' => 71,
        'startColumn' => 5,
        'endColumn' => 5,
        'couldThrow' => false,
        'isClosure' => false,
        'isGenerator' => false,
        'isVariadic' => false,
        'modifiers' => 17,
        'namespace' => 'Illuminate\\Validation',
        'declaringClassName' => 'Illuminate\\Validation\\Rule',
        'implementingClassName' => 'Illuminate\\Validation\\Rule',
        'currentClassName' => 'Illuminate\\Validation\\Rule',
        'aliasName' => NULL,
      ),
      'array' => 
      array (
        'name' => 'array',
        'parameters' => 
        array (
          'keys' => 
          array (
            'name' => 'keys',
            'default' => 
            array (
              'code' => 'null',
              'attributes' => 
              array (
                'startLine' => 79,
                'endLine' => 79,
                'startTokenPos' => 279,
                'startFilePos' => 3007,
                'endTokenPos' => 279,
                'endFilePos' => 3010,
              ),
            ),
            'type' => NULL,
            'isVariadic' => false,
            'byRef' => false,
            'isPromoted' => false,
            'attributes' => 
            array (
            ),
            'startLine' => 79,
            'endLine' => 79,
            'startColumn' => 34,
            'endColumn' => 45,
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
 * Get an array rule builder instance.
 *
 * @param  array|null  $keys
 * @return \\Illuminate\\Validation\\Rules\\ArrayRule
 */',
        'startLine' => 79,
        'endLine' => 82,
        'startColumn' => 5,
        'endColumn' => 5,
        'couldThrow' => false,
        'isClosure' => false,
        'isGenerator' => false,
        'isVariadic' => true,
        'modifiers' => 17,
        'namespace' => 'Illuminate\\Validation',
        'declaringClassName' => 'Illuminate\\Validation\\Rule',
        'implementingClassName' => 'Illuminate\\Validation\\Rule',
        'currentClassName' => 'Illuminate\\Validation\\Rule',
        'aliasName' => NULL,
      ),
      'arrayKeys' => 
      array (
        'name' => 'arrayKeys',
        'parameters' => 
        array (
          'keys' => 
          array (
            'name' => 'keys',
            'default' => NULL,
            'type' => NULL,
            'isVariadic' => false,
            'byRef' => false,
            'isPromoted' => false,
            'attributes' => 
            array (
            ),
            'startLine' => 90,
            'endLine' => 90,
            'startColumn' => 38,
            'endColumn' => 42,
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
 * Get an array keys rule builder instance.
 *
 * @param  \\Illuminate\\Contracts\\Support\\Arrayable|array|string  $keys
 * @return \\Illuminate\\Validation\\Rules\\ArrayKeys
 */',
        'startLine' => 90,
        'endLine' => 93,
        'startColumn' => 5,
        'endColumn' => 5,
        'couldThrow' => false,
        'isClosure' => false,
        'isGenerator' => false,
        'isVariadic' => true,
        'modifiers' => 17,
        'namespace' => 'Illuminate\\Validation',
        'declaringClassName' => 'Illuminate\\Validation\\Rule',
        'implementingClassName' => 'Illuminate\\Validation\\Rule',
        'currentClassName' => 'Illuminate\\Validation\\Rule',
        'aliasName' => NULL,
      ),
      'forEach' => 
      array (
        'name' => 'forEach',
        'parameters' => 
        array (
          'callback' => 
          array (
            'name' => 'callback',
            'default' => NULL,
            'type' => NULL,
            'isVariadic' => false,
            'byRef' => false,
            'isPromoted' => false,
            'attributes' => 
            array (
            ),
            'startLine' => 101,
            'endLine' => 101,
            'startColumn' => 36,
            'endColumn' => 44,
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
 * Create a new nested rule set.
 *
 * @param  callable  $callback
 * @return \\Illuminate\\Validation\\NestedRules
 */',
        'startLine' => 101,
        'endLine' => 104,
        'startColumn' => 5,
        'endColumn' => 5,
        'couldThrow' => false,
        'isClosure' => false,
        'isGenerator' => false,
        'isVariadic' => false,
        'modifiers' => 17,
        'namespace' => 'Illuminate\\Validation',
        'declaringClassName' => 'Illuminate\\Validation\\Rule',
        'implementingClassName' => 'Illuminate\\Validation\\Rule',
        'currentClassName' => 'Illuminate\\Validation\\Rule',
        'aliasName' => NULL,
      ),
      'unique' => 
      array (
        'name' => 'unique',
        'parameters' => 
        array (
          'table' => 
          array (
            'name' => 'table',
            'default' => NULL,
            'type' => NULL,
            'isVariadic' => false,
            'byRef' => false,
            'isPromoted' => false,
            'attributes' => 
            array (
            ),
            'startLine' => 113,
            'endLine' => 113,
            'startColumn' => 35,
            'endColumn' => 40,
            'parameterIndex' => 0,
            'isOptional' => false,
          ),
          'column' => 
          array (
            'name' => 'column',
            'default' => 
            array (
              'code' => '\'NULL\'',
              'attributes' => 
              array (
                'startLine' => 113,
                'endLine' => 113,
                'startTokenPos' => 373,
                'startFilePos' => 3866,
                'endTokenPos' => 373,
                'endFilePos' => 3871,
              ),
            ),
            'type' => NULL,
            'isVariadic' => false,
            'byRef' => false,
            'isPromoted' => false,
            'attributes' => 
            array (
            ),
            'startLine' => 113,
            'endLine' => 113,
            'startColumn' => 43,
            'endColumn' => 58,
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
 * Get a unique constraint builder instance.
 *
 * @param  string  $table
 * @param  string  $column
 * @return \\Illuminate\\Validation\\Rules\\Unique
 */',
        'startLine' => 113,
        'endLine' => 116,
        'startColumn' => 5,
        'endColumn' => 5,
        'couldThrow' => false,
        'isClosure' => false,
        'isGenerator' => false,
        'isVariadic' => false,
        'modifiers' => 17,
        'namespace' => 'Illuminate\\Validation',
        'declaringClassName' => 'Illuminate\\Validation\\Rule',
        'implementingClassName' => 'Illuminate\\Validation\\Rule',
        'currentClassName' => 'Illuminate\\Validation\\Rule',
        'aliasName' => NULL,
      ),
      'exists' => 
      array (
        'name' => 'exists',
        'parameters' => 
        array (
          'table' => 
          array (
            'name' => 'table',
            'default' => NULL,
            'type' => NULL,
            'isVariadic' => false,
            'byRef' => false,
            'isPromoted' => false,
            'attributes' => 
            array (
            ),
            'startLine' => 125,
            'endLine' => 125,
            'startColumn' => 35,
            'endColumn' => 40,
            'parameterIndex' => 0,
            'isOptional' => false,
          ),
          'column' => 
          array (
            'name' => 'column',
            'default' => 
            array (
              'code' => '\'NULL\'',
              'attributes' => 
              array (
                'startLine' => 125,
                'endLine' => 125,
                'startTokenPos' => 410,
                'startFilePos' => 4168,
                'endTokenPos' => 410,
                'endFilePos' => 4173,
              ),
            ),
            'type' => NULL,
            'isVariadic' => false,
            'byRef' => false,
            'isPromoted' => false,
            'attributes' => 
            array (
            ),
            'startLine' => 125,
            'endLine' => 125,
            'startColumn' => 43,
            'endColumn' => 58,
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
 * Get an exists constraint builder instance.
 *
 * @param  string  $table
 * @param  string  $column
 * @return \\Illuminate\\Validation\\Rules\\Exists
 */',
        'startLine' => 125,
        'endLine' => 128,
        'startColumn' => 5,
        'endColumn' => 5,
        'couldThrow' => false,
        'isClosure' => false,
        'isGenerator' => false,
        'isVariadic' => false,
        'modifiers' => 17,
        'namespace' => 'Illuminate\\Validation',
        'declaringClassName' => 'Illuminate\\Validation\\Rule',
        'implementingClassName' => 'Illuminate\\Validation\\Rule',
        'currentClassName' => 'Illuminate\\Validation\\Rule',
        'aliasName' => NULL,
      ),
      'in' => 
      array (
        'name' => 'in',
        'parameters' => 
        array (
          'values' => 
          array (
            'name' => 'values',
            'default' => NULL,
            'type' => NULL,
            'isVariadic' => false,
            'byRef' => false,
            'isPromoted' => false,
            'attributes' => 
            array (
            ),
            'startLine' => 136,
            'endLine' => 136,
            'startColumn' => 31,
            'endColumn' => 37,
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
 * Get an in rule builder instance.
 *
 * @param  \\Illuminate\\Contracts\\Support\\Arrayable|\\UnitEnum|array|string  $values
 * @return \\Illuminate\\Validation\\Rules\\In
 */',
        'startLine' => 136,
        'endLine' => 143,
        'startColumn' => 5,
        'endColumn' => 5,
        'couldThrow' => false,
        'isClosure' => false,
        'isGenerator' => false,
        'isVariadic' => true,
        'modifiers' => 17,
        'namespace' => 'Illuminate\\Validation',
        'declaringClassName' => 'Illuminate\\Validation\\Rule',
        'implementingClassName' => 'Illuminate\\Validation\\Rule',
        'currentClassName' => 'Illuminate\\Validation\\Rule',
        'aliasName' => NULL,
      ),
      'notIn' => 
      array (
        'name' => 'notIn',
        'parameters' => 
        array (
          'values' => 
          array (
            'name' => 'values',
            'default' => NULL,
            'type' => NULL,
            'isVariadic' => false,
            'byRef' => false,
            'isPromoted' => false,
            'attributes' => 
            array (
            ),
            'startLine' => 151,
            'endLine' => 151,
            'startColumn' => 34,
            'endColumn' => 40,
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
 * Get a not_in rule builder instance.
 *
 * @param  \\Illuminate\\Contracts\\Support\\Arrayable|\\UnitEnum|array|string  $values
 * @return \\Illuminate\\Validation\\Rules\\NotIn
 */',
        'startLine' => 151,
        'endLine' => 158,
        'startColumn' => 5,
        'endColumn' => 5,
        'couldThrow' => false,
        'isClosure' => false,
        'isGenerator' => false,
        'isVariadic' => true,
        'modifiers' => 17,
        'namespace' => 'Illuminate\\Validation',
        'declaringClassName' => 'Illuminate\\Validation\\Rule',
        'implementingClassName' => 'Illuminate\\Validation\\Rule',
        'currentClassName' => 'Illuminate\\Validation\\Rule',
        'aliasName' => NULL,
      ),
      'requiredIf' => 
      array (
        'name' => 'requiredIf',
        'parameters' => 
        array (
          'callback' => 
          array (
            'name' => 'callback',
            'default' => NULL,
            'type' => NULL,
            'isVariadic' => false,
            'byRef' => false,
            'isPromoted' => false,
            'attributes' => 
            array (
            ),
            'startLine' => 166,
            'endLine' => 166,
            'startColumn' => 39,
            'endColumn' => 47,
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
 * Get a required_if rule builder instance.
 *
 * @param  (\\Closure(): bool)|bool  $callback
 * @return \\Illuminate\\Validation\\Rules\\RequiredIf
 */',
        'startLine' => 166,
        'endLine' => 169,
        'startColumn' => 5,
        'endColumn' => 5,
        'couldThrow' => false,
        'isClosure' => false,
        'isGenerator' => false,
        'isVariadic' => false,
        'modifiers' => 17,
        'namespace' => 'Illuminate\\Validation',
        'declaringClassName' => 'Illuminate\\Validation\\Rule',
        'implementingClassName' => 'Illuminate\\Validation\\Rule',
        'currentClassName' => 'Illuminate\\Validation\\Rule',
        'aliasName' => NULL,
      ),
      'requiredUnless' => 
      array (
        'name' => 'requiredUnless',
        'parameters' => 
        array (
          'callback' => 
          array (
            'name' => 'callback',
            'default' => NULL,
            'type' => NULL,
            'isVariadic' => false,
            'byRef' => false,
            'isPromoted' => false,
            'attributes' => 
            array (
            ),
            'startLine' => 177,
            'endLine' => 177,
            'startColumn' => 43,
            'endColumn' => 51,
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
 * Get a required_unless rule builder instance.
 *
 * @param  (\\Closure(): bool)|bool|null  $callback
 * @return \\Illuminate\\Validation\\Rules\\RequiredUnless
 */',
        'startLine' => 177,
        'endLine' => 180,
        'startColumn' => 5,
        'endColumn' => 5,
        'couldThrow' => false,
        'isClosure' => false,
        'isGenerator' => false,
        'isVariadic' => false,
        'modifiers' => 17,
        'namespace' => 'Illuminate\\Validation',
        'declaringClassName' => 'Illuminate\\Validation\\Rule',
        'implementingClassName' => 'Illuminate\\Validation\\Rule',
        'currentClassName' => 'Illuminate\\Validation\\Rule',
        'aliasName' => NULL,
      ),
      'excludeIf' => 
      array (
        'name' => 'excludeIf',
        'parameters' => 
        array (
          'callback' => 
          array (
            'name' => 'callback',
            'default' => NULL,
            'type' => NULL,
            'isVariadic' => false,
            'byRef' => false,
            'isPromoted' => false,
            'attributes' => 
            array (
            ),
            'startLine' => 188,
            'endLine' => 188,
            'startColumn' => 38,
            'endColumn' => 46,
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
 * Get an exclude_if rule builder instance.
 *
 * @param  (\\Closure(): bool)|bool  $callback
 * @return \\Illuminate\\Validation\\Rules\\ExcludeIf
 */',
        'startLine' => 188,
        'endLine' => 191,
        'startColumn' => 5,
        'endColumn' => 5,
        'couldThrow' => false,
        'isClosure' => false,
        'isGenerator' => false,
        'isVariadic' => false,
        'modifiers' => 17,
        'namespace' => 'Illuminate\\Validation',
        'declaringClassName' => 'Illuminate\\Validation\\Rule',
        'implementingClassName' => 'Illuminate\\Validation\\Rule',
        'currentClassName' => 'Illuminate\\Validation\\Rule',
        'aliasName' => NULL,
      ),
      'excludeUnless' => 
      array (
        'name' => 'excludeUnless',
        'parameters' => 
        array (
          'callback' => 
          array (
            'name' => 'callback',
            'default' => NULL,
            'type' => NULL,
            'isVariadic' => false,
            'byRef' => false,
            'isPromoted' => false,
            'attributes' => 
            array (
            ),
            'startLine' => 199,
            'endLine' => 199,
            'startColumn' => 42,
            'endColumn' => 50,
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
 * Get an exclude_unless rule builder instance.
 *
 * @param  (\\Closure(): bool)|bool  $callback
 * @return \\Illuminate\\Validation\\Rules\\ExcludeUnless
 */',
        'startLine' => 199,
        'endLine' => 202,
        'startColumn' => 5,
        'endColumn' => 5,
        'couldThrow' => false,
        'isClosure' => false,
        'isGenerator' => false,
        'isVariadic' => false,
        'modifiers' => 17,
        'namespace' => 'Illuminate\\Validation',
        'declaringClassName' => 'Illuminate\\Validation\\Rule',
        'implementingClassName' => 'Illuminate\\Validation\\Rule',
        'currentClassName' => 'Illuminate\\Validation\\Rule',
        'aliasName' => NULL,
      ),
      'prohibitedIf' => 
      array (
        'name' => 'prohibitedIf',
        'parameters' => 
        array (
          'callback' => 
          array (
            'name' => 'callback',
            'default' => NULL,
            'type' => NULL,
            'isVariadic' => false,
            'byRef' => false,
            'isPromoted' => false,
            'attributes' => 
            array (
            ),
            'startLine' => 210,
            'endLine' => 210,
            'startColumn' => 41,
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
 * Get a prohibited_if rule builder instance.
 *
 * @param  (\\Closure(): bool)|bool  $callback
 * @return \\Illuminate\\Validation\\Rules\\ProhibitedIf
 */',
        'startLine' => 210,
        'endLine' => 213,
        'startColumn' => 5,
        'endColumn' => 5,
        'couldThrow' => false,
        'isClosure' => false,
        'isGenerator' => false,
        'isVariadic' => false,
        'modifiers' => 17,
        'namespace' => 'Illuminate\\Validation',
        'declaringClassName' => 'Illuminate\\Validation\\Rule',
        'implementingClassName' => 'Illuminate\\Validation\\Rule',
        'currentClassName' => 'Illuminate\\Validation\\Rule',
        'aliasName' => NULL,
      ),
      'prohibitedUnless' => 
      array (
        'name' => 'prohibitedUnless',
        'parameters' => 
        array (
          'callback' => 
          array (
            'name' => 'callback',
            'default' => NULL,
            'type' => NULL,
            'isVariadic' => false,
            'byRef' => false,
            'isPromoted' => false,
            'attributes' => 
            array (
            ),
            'startLine' => 221,
            'endLine' => 221,
            'startColumn' => 45,
            'endColumn' => 53,
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
 * Get a prohibited_unless rule builder instance.
 *
 * @param  (\\Closure(): bool)|bool  $callback
 * @return \\Illuminate\\Validation\\Rules\\ProhibitedUnless
 */',
        'startLine' => 221,
        'endLine' => 224,
        'startColumn' => 5,
        'endColumn' => 5,
        'couldThrow' => false,
        'isClosure' => false,
        'isGenerator' => false,
        'isVariadic' => false,
        'modifiers' => 17,
        'namespace' => 'Illuminate\\Validation',
        'declaringClassName' => 'Illuminate\\Validation\\Rule',
        'implementingClassName' => 'Illuminate\\Validation\\Rule',
        'currentClassName' => 'Illuminate\\Validation\\Rule',
        'aliasName' => NULL,
      ),
      'date' => 
      array (
        'name' => 'date',
        'parameters' => 
        array (
        ),
        'returnsReference' => false,
        'returnType' => NULL,
        'attributes' => 
        array (
        ),
        'docComment' => '/**
 * Get a date rule builder instance.
 *
 * @return \\Illuminate\\Validation\\Rules\\Date
 */',
        'startLine' => 231,
        'endLine' => 234,
        'startColumn' => 5,
        'endColumn' => 5,
        'couldThrow' => false,
        'isClosure' => false,
        'isGenerator' => false,
        'isVariadic' => false,
        'modifiers' => 17,
        'namespace' => 'Illuminate\\Validation',
        'declaringClassName' => 'Illuminate\\Validation\\Rule',
        'implementingClassName' => 'Illuminate\\Validation\\Rule',
        'currentClassName' => 'Illuminate\\Validation\\Rule',
        'aliasName' => NULL,
      ),
      'dateTime' => 
      array (
        'name' => 'dateTime',
        'parameters' => 
        array (
        ),
        'returnsReference' => false,
        'returnType' => 
        array (
          'class' => 'PHPStan\\BetterReflection\\Reflection\\ReflectionNamedType',
          'data' => 
          array (
            'name' => 'Illuminate\\Validation\\Rules\\Date',
            'isIdentifier' => false,
          ),
        ),
        'attributes' => 
        array (
        ),
        'docComment' => '/**
 * Get a datetime rule builder instance.
 */',
        'startLine' => 239,
        'endLine' => 242,
        'startColumn' => 5,
        'endColumn' => 5,
        'couldThrow' => false,
        'isClosure' => false,
        'isGenerator' => false,
        'isVariadic' => false,
        'modifiers' => 17,
        'namespace' => 'Illuminate\\Validation',
        'declaringClassName' => 'Illuminate\\Validation\\Rule',
        'implementingClassName' => 'Illuminate\\Validation\\Rule',
        'currentClassName' => 'Illuminate\\Validation\\Rule',
        'aliasName' => NULL,
      ),
      'email' => 
      array (
        'name' => 'email',
        'parameters' => 
        array (
        ),
        'returnsReference' => false,
        'returnType' => NULL,
        'attributes' => 
        array (
        ),
        'docComment' => '/**
 * Get an email rule builder instance.
 *
 * @return \\Illuminate\\Validation\\Rules\\Email
 */',
        'startLine' => 249,
        'endLine' => 252,
        'startColumn' => 5,
        'endColumn' => 5,
        'couldThrow' => false,
        'isClosure' => false,
        'isGenerator' => false,
        'isVariadic' => false,
        'modifiers' => 17,
        'namespace' => 'Illuminate\\Validation',
        'declaringClassName' => 'Illuminate\\Validation\\Rule',
        'implementingClassName' => 'Illuminate\\Validation\\Rule',
        'currentClassName' => 'Illuminate\\Validation\\Rule',
        'aliasName' => NULL,
      ),
      'enum' => 
      array (
        'name' => 'enum',
        'parameters' => 
        array (
          'type' => 
          array (
            'name' => 'type',
            'default' => NULL,
            'type' => NULL,
            'isVariadic' => false,
            'byRef' => false,
            'isPromoted' => false,
            'attributes' => 
            array (
            ),
            'startLine' => 260,
            'endLine' => 260,
            'startColumn' => 33,
            'endColumn' => 37,
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
 * Get an enum rule builder instance.
 *
 * @param  class-string  $type
 * @return \\Illuminate\\Validation\\Rules\\Enum
 */',
        'startLine' => 260,
        'endLine' => 263,
        'startColumn' => 5,
        'endColumn' => 5,
        'couldThrow' => false,
        'isClosure' => false,
        'isGenerator' => false,
        'isVariadic' => false,
        'modifiers' => 17,
        'namespace' => 'Illuminate\\Validation',
        'declaringClassName' => 'Illuminate\\Validation\\Rule',
        'implementingClassName' => 'Illuminate\\Validation\\Rule',
        'currentClassName' => 'Illuminate\\Validation\\Rule',
        'aliasName' => NULL,
      ),
      'file' => 
      array (
        'name' => 'file',
        'parameters' => 
        array (
        ),
        'returnsReference' => false,
        'returnType' => NULL,
        'attributes' => 
        array (
        ),
        'docComment' => '/**
 * Get a file rule builder instance.
 *
 * @return \\Illuminate\\Validation\\Rules\\File
 */',
        'startLine' => 270,
        'endLine' => 273,
        'startColumn' => 5,
        'endColumn' => 5,
        'couldThrow' => false,
        'isClosure' => false,
        'isGenerator' => false,
        'isVariadic' => false,
        'modifiers' => 17,
        'namespace' => 'Illuminate\\Validation',
        'declaringClassName' => 'Illuminate\\Validation\\Rule',
        'implementingClassName' => 'Illuminate\\Validation\\Rule',
        'currentClassName' => 'Illuminate\\Validation\\Rule',
        'aliasName' => NULL,
      ),
      'imageFile' => 
      array (
        'name' => 'imageFile',
        'parameters' => 
        array (
          'allowSvg' => 
          array (
            'name' => 'allowSvg',
            'default' => 
            array (
              'code' => 'false',
              'attributes' => 
              array (
                'startLine' => 281,
                'endLine' => 281,
                'startTokenPos' => 865,
                'startFilePos' => 7986,
                'endTokenPos' => 865,
                'endFilePos' => 7990,
              ),
            ),
            'type' => NULL,
            'isVariadic' => false,
            'byRef' => false,
            'isPromoted' => false,
            'attributes' => 
            array (
            ),
            'startLine' => 281,
            'endLine' => 281,
            'startColumn' => 38,
            'endColumn' => 54,
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
 * Get an image file rule builder instance.
 *
 * @param  bool  $allowSvg
 * @return \\Illuminate\\Validation\\Rules\\ImageFile
 */',
        'startLine' => 281,
        'endLine' => 284,
        'startColumn' => 5,
        'endColumn' => 5,
        'couldThrow' => false,
        'isClosure' => false,
        'isGenerator' => false,
        'isVariadic' => false,
        'modifiers' => 17,
        'namespace' => 'Illuminate\\Validation',
        'declaringClassName' => 'Illuminate\\Validation\\Rule',
        'implementingClassName' => 'Illuminate\\Validation\\Rule',
        'currentClassName' => 'Illuminate\\Validation\\Rule',
        'aliasName' => NULL,
      ),
      'dimensions' => 
      array (
        'name' => 'dimensions',
        'parameters' => 
        array (
          'constraints' => 
          array (
            'name' => 'constraints',
            'default' => 
            array (
              'code' => '[]',
              'attributes' => 
              array (
                'startLine' => 292,
                'endLine' => 292,
                'startTokenPos' => 898,
                'startFilePos' => 8266,
                'endTokenPos' => 899,
                'endFilePos' => 8267,
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
            'startLine' => 292,
            'endLine' => 292,
            'startColumn' => 39,
            'endColumn' => 61,
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
 * Get a dimensions rule builder instance.
 *
 * @param  array  $constraints
 * @return \\Illuminate\\Validation\\Rules\\Dimensions
 */',
        'startLine' => 292,
        'endLine' => 295,
        'startColumn' => 5,
        'endColumn' => 5,
        'couldThrow' => false,
        'isClosure' => false,
        'isGenerator' => false,
        'isVariadic' => false,
        'modifiers' => 17,
        'namespace' => 'Illuminate\\Validation',
        'declaringClassName' => 'Illuminate\\Validation\\Rule',
        'implementingClassName' => 'Illuminate\\Validation\\Rule',
        'currentClassName' => 'Illuminate\\Validation\\Rule',
        'aliasName' => NULL,
      ),
      'string' => 
      array (
        'name' => 'string',
        'parameters' => 
        array (
        ),
        'returnsReference' => false,
        'returnType' => NULL,
        'attributes' => 
        array (
        ),
        'docComment' => '/**
 * Get a string rule builder instance.
 *
 * @return \\Illuminate\\Validation\\Rules\\StringRule
 */',
        'startLine' => 302,
        'endLine' => 305,
        'startColumn' => 5,
        'endColumn' => 5,
        'couldThrow' => false,
        'isClosure' => false,
        'isGenerator' => false,
        'isVariadic' => false,
        'modifiers' => 17,
        'namespace' => 'Illuminate\\Validation',
        'declaringClassName' => 'Illuminate\\Validation\\Rule',
        'implementingClassName' => 'Illuminate\\Validation\\Rule',
        'currentClassName' => 'Illuminate\\Validation\\Rule',
        'aliasName' => NULL,
      ),
      'numeric' => 
      array (
        'name' => 'numeric',
        'parameters' => 
        array (
        ),
        'returnsReference' => false,
        'returnType' => NULL,
        'attributes' => 
        array (
        ),
        'docComment' => '/**
 * Get a numeric rule builder instance.
 *
 * @return \\Illuminate\\Validation\\Rules\\Numeric
 */',
        'startLine' => 312,
        'endLine' => 315,
        'startColumn' => 5,
        'endColumn' => 5,
        'couldThrow' => false,
        'isClosure' => false,
        'isGenerator' => false,
        'isVariadic' => false,
        'modifiers' => 17,
        'namespace' => 'Illuminate\\Validation',
        'declaringClassName' => 'Illuminate\\Validation\\Rule',
        'implementingClassName' => 'Illuminate\\Validation\\Rule',
        'currentClassName' => 'Illuminate\\Validation\\Rule',
        'aliasName' => NULL,
      ),
      'anyOf' => 
      array (
        'name' => 'anyOf',
        'parameters' => 
        array (
          'rules' => 
          array (
            'name' => 'rules',
            'default' => NULL,
            'type' => NULL,
            'isVariadic' => false,
            'byRef' => false,
            'isPromoted' => false,
            'attributes' => 
            array (
            ),
            'startLine' => 325,
            'endLine' => 325,
            'startColumn' => 34,
            'endColumn' => 39,
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
 * Get an "any of" rule builder instance.
 *
 * @param  array  $rules
 * @return \\Illuminate\\Validation\\Rules\\AnyOf
 *
 * @throws \\InvalidArgumentException
 */',
        'startLine' => 325,
        'endLine' => 328,
        'startColumn' => 5,
        'endColumn' => 5,
        'couldThrow' => false,
        'isClosure' => false,
        'isGenerator' => false,
        'isVariadic' => false,
        'modifiers' => 17,
        'namespace' => 'Illuminate\\Validation',
        'declaringClassName' => 'Illuminate\\Validation\\Rule',
        'implementingClassName' => 'Illuminate\\Validation\\Rule',
        'currentClassName' => 'Illuminate\\Validation\\Rule',
        'aliasName' => NULL,
      ),
      'contains' => 
      array (
        'name' => 'contains',
        'parameters' => 
        array (
          'values' => 
          array (
            'name' => 'values',
            'default' => NULL,
            'type' => NULL,
            'isVariadic' => false,
            'byRef' => false,
            'isPromoted' => false,
            'attributes' => 
            array (
            ),
            'startLine' => 336,
            'endLine' => 336,
            'startColumn' => 37,
            'endColumn' => 43,
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
 * Get a contains rule builder instance.
 *
 * @param  \\Illuminate\\Contracts\\Support\\Arrayable|\\UnitEnum|array|string  $values
 * @return \\Illuminate\\Validation\\Rules\\Contains
 */',
        'startLine' => 336,
        'endLine' => 343,
        'startColumn' => 5,
        'endColumn' => 5,
        'couldThrow' => false,
        'isClosure' => false,
        'isGenerator' => false,
        'isVariadic' => true,
        'modifiers' => 17,
        'namespace' => 'Illuminate\\Validation',
        'declaringClassName' => 'Illuminate\\Validation\\Rule',
        'implementingClassName' => 'Illuminate\\Validation\\Rule',
        'currentClassName' => 'Illuminate\\Validation\\Rule',
        'aliasName' => NULL,
      ),
      'doesntContain' => 
      array (
        'name' => 'doesntContain',
        'parameters' => 
        array (
          'values' => 
          array (
            'name' => 'values',
            'default' => NULL,
            'type' => NULL,
            'isVariadic' => false,
            'byRef' => false,
            'isPromoted' => false,
            'attributes' => 
            array (
            ),
            'startLine' => 351,
            'endLine' => 351,
            'startColumn' => 42,
            'endColumn' => 48,
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
 * Get a "does not contain" rule builder instance.
 *
 * @param  \\Illuminate\\Contracts\\Support\\Arrayable|\\UnitEnum|array|string  $values
 * @return \\Illuminate\\Validation\\Rules\\DoesntContain
 */',
        'startLine' => 351,
        'endLine' => 358,
        'startColumn' => 5,
        'endColumn' => 5,
        'couldThrow' => false,
        'isClosure' => false,
        'isGenerator' => false,
        'isVariadic' => true,
        'modifiers' => 17,
        'namespace' => 'Illuminate\\Validation',
        'declaringClassName' => 'Illuminate\\Validation\\Rule',
        'implementingClassName' => 'Illuminate\\Validation\\Rule',
        'currentClassName' => 'Illuminate\\Validation\\Rule',
        'aliasName' => NULL,
      ),
      'compile' => 
      array (
        'name' => 'compile',
        'parameters' => 
        array (
          'attribute' => 
          array (
            'name' => 'attribute',
            'default' => NULL,
            'type' => NULL,
            'isVariadic' => false,
            'byRef' => false,
            'isPromoted' => false,
            'attributes' => 
            array (
            ),
            'startLine' => 368,
            'endLine' => 368,
            'startColumn' => 36,
            'endColumn' => 45,
            'parameterIndex' => 0,
            'isOptional' => false,
          ),
          'rules' => 
          array (
            'name' => 'rules',
            'default' => NULL,
            'type' => NULL,
            'isVariadic' => false,
            'byRef' => false,
            'isPromoted' => false,
            'attributes' => 
            array (
            ),
            'startLine' => 368,
            'endLine' => 368,
            'startColumn' => 48,
            'endColumn' => 53,
            'parameterIndex' => 1,
            'isOptional' => false,
          ),
          'data' => 
          array (
            'name' => 'data',
            'default' => 
            array (
              'code' => 'null',
              'attributes' => 
              array (
                'startLine' => 368,
                'endLine' => 368,
                'startTokenPos' => 1139,
                'startFilePos' => 10187,
                'endTokenPos' => 1139,
                'endFilePos' => 10190,
              ),
            ),
            'type' => NULL,
            'isVariadic' => false,
            'byRef' => false,
            'isPromoted' => false,
            'attributes' => 
            array (
            ),
            'startLine' => 368,
            'endLine' => 368,
            'startColumn' => 56,
            'endColumn' => 67,
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
 * Compile a set of rules for an attribute.
 *
 * @param  string  $attribute
 * @param  array  $rules
 * @param  array|null  $data
 * @return object|\\stdClass
 */',
        'startLine' => 368,
        'endLine' => 387,
        'startColumn' => 5,
        'endColumn' => 5,
        'couldThrow' => false,
        'isClosure' => false,
        'isGenerator' => false,
        'isVariadic' => false,
        'modifiers' => 17,
        'namespace' => 'Illuminate\\Validation',
        'declaringClassName' => 'Illuminate\\Validation\\Rule',
        'implementingClassName' => 'Illuminate\\Validation\\Rule',
        'currentClassName' => 'Illuminate\\Validation\\Rule',
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