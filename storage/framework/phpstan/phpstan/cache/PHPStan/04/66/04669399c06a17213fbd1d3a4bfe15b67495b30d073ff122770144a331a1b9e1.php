<?php declare(strict_types = 1);

// osfsl-/var/www/html/vendor/composer/../laravel/framework/src/Illuminate/Foundation/Auth/Access/Authorizable.php-PHPStan\BetterReflection\Reflection\ReflectionClass-Illuminate\Foundation\Auth\Access\Authorizable
return \PHPStan\Cache\CacheItem::__set_state(array(
   'variableKey' => 'v2-cf354171da2dc4c29bf05122804ec45d31c90c644403cce6bf724770f453923c-8.5.9-6.70.0.3',
   'data' => 
  array (
    'locatedSource' => 
    array (
      'class' => 'PHPStan\\BetterReflection\\SourceLocator\\Located\\LocatedSource',
      'data' => 
      array (
        'name' => 'Illuminate\\Foundation\\Auth\\Access\\Authorizable',
        'filename' => '/var/www/html/vendor/composer/../laravel/framework/src/Illuminate/Foundation/Auth/Access/Authorizable.php',
      ),
    ),
    'namespace' => 'Illuminate\\Foundation\\Auth\\Access',
    'name' => 'Illuminate\\Foundation\\Auth\\Access\\Authorizable',
    'shortName' => 'Authorizable',
    'isInterface' => false,
    'isTrait' => true,
    'isEnum' => false,
    'isBackedEnum' => false,
    'modifiers' => 0,
    'docComment' => NULL,
    'attributes' => 
    array (
    ),
    'startLine' => 7,
    'endLine' => 56,
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
      'can' => 
      array (
        'name' => 'can',
        'parameters' => 
        array (
          'abilities' => 
          array (
            'name' => 'abilities',
            'default' => NULL,
            'type' => NULL,
            'isVariadic' => false,
            'byRef' => false,
            'isPromoted' => false,
            'attributes' => 
            array (
            ),
            'startLine' => 16,
            'endLine' => 16,
            'startColumn' => 25,
            'endColumn' => 34,
            'parameterIndex' => 0,
            'isOptional' => false,
          ),
          'arguments' => 
          array (
            'name' => 'arguments',
            'default' => 
            array (
              'code' => '[]',
              'attributes' => 
              array (
                'startLine' => 16,
                'endLine' => 16,
                'startTokenPos' => 33,
                'startFilePos' => 352,
                'endTokenPos' => 34,
                'endFilePos' => 353,
              ),
            ),
            'type' => NULL,
            'isVariadic' => false,
            'byRef' => false,
            'isPromoted' => false,
            'attributes' => 
            array (
            ),
            'startLine' => 16,
            'endLine' => 16,
            'startColumn' => 37,
            'endColumn' => 51,
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
 * Determine if the entity has the given abilities.
 *
 * @param  iterable|\\UnitEnum|string  $abilities
 * @param  mixed  $arguments
 * @return bool
 */',
        'startLine' => 16,
        'endLine' => 19,
        'startColumn' => 5,
        'endColumn' => 5,
        'couldThrow' => false,
        'isClosure' => false,
        'isGenerator' => false,
        'isVariadic' => false,
        'modifiers' => 1,
        'namespace' => 'Illuminate\\Foundation\\Auth\\Access',
        'declaringClassName' => 'Illuminate\\Foundation\\Auth\\Access\\Authorizable',
        'implementingClassName' => 'Illuminate\\Foundation\\Auth\\Access\\Authorizable',
        'currentClassName' => 'Illuminate\\Foundation\\Auth\\Access\\Authorizable',
        'aliasName' => NULL,
      ),
      'canAny' => 
      array (
        'name' => 'canAny',
        'parameters' => 
        array (
          'abilities' => 
          array (
            'name' => 'abilities',
            'default' => NULL,
            'type' => NULL,
            'isVariadic' => false,
            'byRef' => false,
            'isPromoted' => false,
            'attributes' => 
            array (
            ),
            'startLine' => 28,
            'endLine' => 28,
            'startColumn' => 28,
            'endColumn' => 37,
            'parameterIndex' => 0,
            'isOptional' => false,
          ),
          'arguments' => 
          array (
            'name' => 'arguments',
            'default' => 
            array (
              'code' => '[]',
              'attributes' => 
              array (
                'startLine' => 28,
                'endLine' => 28,
                'startTokenPos' => 79,
                'startFilePos' => 693,
                'endTokenPos' => 80,
                'endFilePos' => 694,
              ),
            ),
            'type' => NULL,
            'isVariadic' => false,
            'byRef' => false,
            'isPromoted' => false,
            'attributes' => 
            array (
            ),
            'startLine' => 28,
            'endLine' => 28,
            'startColumn' => 40,
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
 * Determine if the entity has any of the given abilities.
 *
 * @param  iterable|\\UnitEnum|string  $abilities
 * @param  mixed  $arguments
 * @return bool
 */',
        'startLine' => 28,
        'endLine' => 31,
        'startColumn' => 5,
        'endColumn' => 5,
        'couldThrow' => false,
        'isClosure' => false,
        'isGenerator' => false,
        'isVariadic' => false,
        'modifiers' => 1,
        'namespace' => 'Illuminate\\Foundation\\Auth\\Access',
        'declaringClassName' => 'Illuminate\\Foundation\\Auth\\Access\\Authorizable',
        'implementingClassName' => 'Illuminate\\Foundation\\Auth\\Access\\Authorizable',
        'currentClassName' => 'Illuminate\\Foundation\\Auth\\Access\\Authorizable',
        'aliasName' => NULL,
      ),
      'cant' => 
      array (
        'name' => 'cant',
        'parameters' => 
        array (
          'abilities' => 
          array (
            'name' => 'abilities',
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
            'startColumn' => 26,
            'endColumn' => 35,
            'parameterIndex' => 0,
            'isOptional' => false,
          ),
          'arguments' => 
          array (
            'name' => 'arguments',
            'default' => 
            array (
              'code' => '[]',
              'attributes' => 
              array (
                'startLine' => 40,
                'endLine' => 40,
                'startTokenPos' => 125,
                'startFilePos' => 1033,
                'endTokenPos' => 126,
                'endFilePos' => 1034,
              ),
            ),
            'type' => NULL,
            'isVariadic' => false,
            'byRef' => false,
            'isPromoted' => false,
            'attributes' => 
            array (
            ),
            'startLine' => 40,
            'endLine' => 40,
            'startColumn' => 38,
            'endColumn' => 52,
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
 * Determine if the entity does not have the given abilities.
 *
 * @param  iterable|\\UnitEnum|string  $abilities
 * @param  mixed  $arguments
 * @return bool
 */',
        'startLine' => 40,
        'endLine' => 43,
        'startColumn' => 5,
        'endColumn' => 5,
        'couldThrow' => false,
        'isClosure' => false,
        'isGenerator' => false,
        'isVariadic' => false,
        'modifiers' => 1,
        'namespace' => 'Illuminate\\Foundation\\Auth\\Access',
        'declaringClassName' => 'Illuminate\\Foundation\\Auth\\Access\\Authorizable',
        'implementingClassName' => 'Illuminate\\Foundation\\Auth\\Access\\Authorizable',
        'currentClassName' => 'Illuminate\\Foundation\\Auth\\Access\\Authorizable',
        'aliasName' => NULL,
      ),
      'cannot' => 
      array (
        'name' => 'cannot',
        'parameters' => 
        array (
          'abilities' => 
          array (
            'name' => 'abilities',
            'default' => NULL,
            'type' => NULL,
            'isVariadic' => false,
            'byRef' => false,
            'isPromoted' => false,
            'attributes' => 
            array (
            ),
            'startLine' => 52,
            'endLine' => 52,
            'startColumn' => 28,
            'endColumn' => 37,
            'parameterIndex' => 0,
            'isOptional' => false,
          ),
          'arguments' => 
          array (
            'name' => 'arguments',
            'default' => 
            array (
              'code' => '[]',
              'attributes' => 
              array (
                'startLine' => 52,
                'endLine' => 52,
                'startTokenPos' => 163,
                'startFilePos' => 1350,
                'endTokenPos' => 164,
                'endFilePos' => 1351,
              ),
            ),
            'type' => NULL,
            'isVariadic' => false,
            'byRef' => false,
            'isPromoted' => false,
            'attributes' => 
            array (
            ),
            'startLine' => 52,
            'endLine' => 52,
            'startColumn' => 40,
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
 * Determine if the entity does not have the given abilities.
 *
 * @param  iterable|\\UnitEnum|string  $abilities
 * @param  mixed  $arguments
 * @return bool
 */',
        'startLine' => 52,
        'endLine' => 55,
        'startColumn' => 5,
        'endColumn' => 5,
        'couldThrow' => false,
        'isClosure' => false,
        'isGenerator' => false,
        'isVariadic' => false,
        'modifiers' => 1,
        'namespace' => 'Illuminate\\Foundation\\Auth\\Access',
        'declaringClassName' => 'Illuminate\\Foundation\\Auth\\Access\\Authorizable',
        'implementingClassName' => 'Illuminate\\Foundation\\Auth\\Access\\Authorizable',
        'currentClassName' => 'Illuminate\\Foundation\\Auth\\Access\\Authorizable',
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