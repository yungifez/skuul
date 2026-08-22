<?php declare(strict_types = 1);

// osfsl-/var/www/html/vendor/composer/../laravel/framework/src/Illuminate/Foundation/Auth/Access/AuthorizesRequests.php-PHPStan\BetterReflection\Reflection\ReflectionClass-Illuminate\Foundation\Auth\Access\AuthorizesRequests
return \PHPStan\Cache\CacheItem::__set_state(array(
   'variableKey' => 'v2-19d752f2ace60b09e4fd611e5893ad67ee4b87a3d256c284646719f5d97b6b2c-8.5.9-6.70.0.3',
   'data' => 
  array (
    'locatedSource' => 
    array (
      'class' => 'PHPStan\\BetterReflection\\SourceLocator\\Located\\LocatedSource',
      'data' => 
      array (
        'name' => 'Illuminate\\Foundation\\Auth\\Access\\AuthorizesRequests',
        'filename' => '/var/www/html/vendor/composer/../laravel/framework/src/Illuminate/Foundation/Auth/Access/AuthorizesRequests.php',
      ),
    ),
    'namespace' => 'Illuminate\\Foundation\\Auth\\Access',
    'name' => 'Illuminate\\Foundation\\Auth\\Access\\AuthorizesRequests',
    'shortName' => 'AuthorizesRequests',
    'isInterface' => false,
    'isTrait' => true,
    'isEnum' => false,
    'isBackedEnum' => false,
    'modifiers' => 0,
    'docComment' => NULL,
    'attributes' => 
    array (
    ),
    'startLine' => 10,
    'endLine' => 135,
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
      'authorize' => 
      array (
        'name' => 'authorize',
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
            'startLine' => 21,
            'endLine' => 21,
            'startColumn' => 31,
            'endColumn' => 38,
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
                'startLine' => 21,
                'endLine' => 21,
                'startTokenPos' => 45,
                'startFilePos' => 508,
                'endTokenPos' => 46,
                'endFilePos' => 509,
              ),
            ),
            'type' => NULL,
            'isVariadic' => false,
            'byRef' => false,
            'isPromoted' => false,
            'attributes' => 
            array (
            ),
            'startLine' => 21,
            'endLine' => 21,
            'startColumn' => 41,
            'endColumn' => 55,
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
 * Authorize a given action for the current user.
 *
 * @param  mixed  $ability
 * @param  mixed  $arguments
 * @return \\Illuminate\\Auth\\Access\\Response
 *
 * @throws \\Illuminate\\Auth\\Access\\AuthorizationException
 */',
        'startLine' => 21,
        'endLine' => 26,
        'startColumn' => 5,
        'endColumn' => 5,
        'couldThrow' => false,
        'isClosure' => false,
        'isGenerator' => false,
        'isVariadic' => false,
        'modifiers' => 1,
        'namespace' => 'Illuminate\\Foundation\\Auth\\Access',
        'declaringClassName' => 'Illuminate\\Foundation\\Auth\\Access\\AuthorizesRequests',
        'implementingClassName' => 'Illuminate\\Foundation\\Auth\\Access\\AuthorizesRequests',
        'currentClassName' => 'Illuminate\\Foundation\\Auth\\Access\\AuthorizesRequests',
        'aliasName' => NULL,
      ),
      'authorizeForUser' => 
      array (
        'name' => 'authorizeForUser',
        'parameters' => 
        array (
          'user' => 
          array (
            'name' => 'user',
            'default' => NULL,
            'type' => NULL,
            'isVariadic' => false,
            'byRef' => false,
            'isPromoted' => false,
            'attributes' => 
            array (
            ),
            'startLine' => 38,
            'endLine' => 38,
            'startColumn' => 38,
            'endColumn' => 42,
            'parameterIndex' => 0,
            'isOptional' => false,
          ),
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
            'startLine' => 38,
            'endLine' => 38,
            'startColumn' => 45,
            'endColumn' => 52,
            'parameterIndex' => 1,
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
                'startLine' => 38,
                'endLine' => 38,
                'startTokenPos' => 109,
                'startFilePos' => 1066,
                'endTokenPos' => 110,
                'endFilePos' => 1067,
              ),
            ),
            'type' => NULL,
            'isVariadic' => false,
            'byRef' => false,
            'isPromoted' => false,
            'attributes' => 
            array (
            ),
            'startLine' => 38,
            'endLine' => 38,
            'startColumn' => 55,
            'endColumn' => 69,
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
 * Authorize a given action for a user.
 *
 * @param  \\Illuminate\\Contracts\\Auth\\Authenticatable|mixed  $user
 * @param  mixed  $ability
 * @param  mixed  $arguments
 * @return \\Illuminate\\Auth\\Access\\Response
 *
 * @throws \\Illuminate\\Auth\\Access\\AuthorizationException
 */',
        'startLine' => 38,
        'endLine' => 43,
        'startColumn' => 5,
        'endColumn' => 5,
        'couldThrow' => false,
        'isClosure' => false,
        'isGenerator' => false,
        'isVariadic' => false,
        'modifiers' => 1,
        'namespace' => 'Illuminate\\Foundation\\Auth\\Access',
        'declaringClassName' => 'Illuminate\\Foundation\\Auth\\Access\\AuthorizesRequests',
        'implementingClassName' => 'Illuminate\\Foundation\\Auth\\Access\\AuthorizesRequests',
        'currentClassName' => 'Illuminate\\Foundation\\Auth\\Access\\AuthorizesRequests',
        'aliasName' => NULL,
      ),
      'parseAbilityAndArguments' => 
      array (
        'name' => 'parseAbilityAndArguments',
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
            'startLine' => 52,
            'endLine' => 52,
            'startColumn' => 49,
            'endColumn' => 56,
            'parameterIndex' => 0,
            'isOptional' => false,
          ),
          'arguments' => 
          array (
            'name' => 'arguments',
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
            'startColumn' => 59,
            'endColumn' => 68,
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
 * Guesses the ability\'s name if it wasn\'t provided.
 *
 * @param  mixed  $ability
 * @param  mixed  $arguments
 * @return array
 */',
        'startLine' => 52,
        'endLine' => 63,
        'startColumn' => 5,
        'endColumn' => 5,
        'couldThrow' => false,
        'isClosure' => false,
        'isGenerator' => false,
        'isVariadic' => false,
        'modifiers' => 2,
        'namespace' => 'Illuminate\\Foundation\\Auth\\Access',
        'declaringClassName' => 'Illuminate\\Foundation\\Auth\\Access\\AuthorizesRequests',
        'implementingClassName' => 'Illuminate\\Foundation\\Auth\\Access\\AuthorizesRequests',
        'currentClassName' => 'Illuminate\\Foundation\\Auth\\Access\\AuthorizesRequests',
        'aliasName' => NULL,
      ),
      'normalizeGuessedAbilityName' => 
      array (
        'name' => 'normalizeGuessedAbilityName',
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
            'startLine' => 71,
            'endLine' => 71,
            'startColumn' => 52,
            'endColumn' => 59,
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
 * Normalize the ability name that has been guessed from the method name.
 *
 * @param  string  $ability
 * @return string
 */',
        'startLine' => 71,
        'endLine' => 76,
        'startColumn' => 5,
        'endColumn' => 5,
        'couldThrow' => false,
        'isClosure' => false,
        'isGenerator' => false,
        'isVariadic' => false,
        'modifiers' => 2,
        'namespace' => 'Illuminate\\Foundation\\Auth\\Access',
        'declaringClassName' => 'Illuminate\\Foundation\\Auth\\Access\\AuthorizesRequests',
        'implementingClassName' => 'Illuminate\\Foundation\\Auth\\Access\\AuthorizesRequests',
        'currentClassName' => 'Illuminate\\Foundation\\Auth\\Access\\AuthorizesRequests',
        'aliasName' => NULL,
      ),
      'authorizeResource' => 
      array (
        'name' => 'authorizeResource',
        'parameters' => 
        array (
          'model' => 
          array (
            'name' => 'model',
            'default' => NULL,
            'type' => NULL,
            'isVariadic' => false,
            'byRef' => false,
            'isPromoted' => false,
            'attributes' => 
            array (
            ),
            'startLine' => 87,
            'endLine' => 87,
            'startColumn' => 39,
            'endColumn' => 44,
            'parameterIndex' => 0,
            'isOptional' => false,
          ),
          'parameter' => 
          array (
            'name' => 'parameter',
            'default' => 
            array (
              'code' => 'null',
              'attributes' => 
              array (
                'startLine' => 87,
                'endLine' => 87,
                'startTokenPos' => 310,
                'startFilePos' => 2474,
                'endTokenPos' => 310,
                'endFilePos' => 2477,
              ),
            ),
            'type' => NULL,
            'isVariadic' => false,
            'byRef' => false,
            'isPromoted' => false,
            'attributes' => 
            array (
            ),
            'startLine' => 87,
            'endLine' => 87,
            'startColumn' => 47,
            'endColumn' => 63,
            'parameterIndex' => 1,
            'isOptional' => true,
          ),
          'options' => 
          array (
            'name' => 'options',
            'default' => 
            array (
              'code' => '[]',
              'attributes' => 
              array (
                'startLine' => 87,
                'endLine' => 87,
                'startTokenPos' => 319,
                'startFilePos' => 2497,
                'endTokenPos' => 320,
                'endFilePos' => 2498,
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
            'startLine' => 87,
            'endLine' => 87,
            'startColumn' => 66,
            'endColumn' => 84,
            'parameterIndex' => 2,
            'isOptional' => true,
          ),
          'request' => 
          array (
            'name' => 'request',
            'default' => 
            array (
              'code' => 'null',
              'attributes' => 
              array (
                'startLine' => 87,
                'endLine' => 87,
                'startTokenPos' => 327,
                'startFilePos' => 2512,
                'endTokenPos' => 327,
                'endFilePos' => 2515,
              ),
            ),
            'type' => NULL,
            'isVariadic' => false,
            'byRef' => false,
            'isPromoted' => false,
            'attributes' => 
            array (
            ),
            'startLine' => 87,
            'endLine' => 87,
            'startColumn' => 87,
            'endColumn' => 101,
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
 * Authorize a resource action based on the incoming request.
 *
 * @param  string|array  $model
 * @param  string|array|null  $parameter
 * @param  array  $options
 * @param  \\Illuminate\\Http\\Request|null  $request
 * @return void
 */',
        'startLine' => 87,
        'endLine' => 106,
        'startColumn' => 5,
        'endColumn' => 5,
        'couldThrow' => false,
        'isClosure' => false,
        'isGenerator' => false,
        'isVariadic' => false,
        'modifiers' => 1,
        'namespace' => 'Illuminate\\Foundation\\Auth\\Access',
        'declaringClassName' => 'Illuminate\\Foundation\\Auth\\Access\\AuthorizesRequests',
        'implementingClassName' => 'Illuminate\\Foundation\\Auth\\Access\\AuthorizesRequests',
        'currentClassName' => 'Illuminate\\Foundation\\Auth\\Access\\AuthorizesRequests',
        'aliasName' => NULL,
      ),
      'resourceAbilityMap' => 
      array (
        'name' => 'resourceAbilityMap',
        'parameters' => 
        array (
        ),
        'returnsReference' => false,
        'returnType' => NULL,
        'attributes' => 
        array (
        ),
        'docComment' => '/**
 * Get the map of resource methods to ability names.
 *
 * @return array<string, string>
 */',
        'startLine' => 113,
        'endLine' => 124,
        'startColumn' => 5,
        'endColumn' => 5,
        'couldThrow' => false,
        'isClosure' => false,
        'isGenerator' => false,
        'isVariadic' => false,
        'modifiers' => 2,
        'namespace' => 'Illuminate\\Foundation\\Auth\\Access',
        'declaringClassName' => 'Illuminate\\Foundation\\Auth\\Access\\AuthorizesRequests',
        'implementingClassName' => 'Illuminate\\Foundation\\Auth\\Access\\AuthorizesRequests',
        'currentClassName' => 'Illuminate\\Foundation\\Auth\\Access\\AuthorizesRequests',
        'aliasName' => NULL,
      ),
      'resourceMethodsWithoutModels' => 
      array (
        'name' => 'resourceMethodsWithoutModels',
        'parameters' => 
        array (
        ),
        'returnsReference' => false,
        'returnType' => NULL,
        'attributes' => 
        array (
        ),
        'docComment' => '/**
 * Get the list of resource methods which do not have model parameters.
 *
 * @return list<string>
 */',
        'startLine' => 131,
        'endLine' => 134,
        'startColumn' => 5,
        'endColumn' => 5,
        'couldThrow' => false,
        'isClosure' => false,
        'isGenerator' => false,
        'isVariadic' => false,
        'modifiers' => 2,
        'namespace' => 'Illuminate\\Foundation\\Auth\\Access',
        'declaringClassName' => 'Illuminate\\Foundation\\Auth\\Access\\AuthorizesRequests',
        'implementingClassName' => 'Illuminate\\Foundation\\Auth\\Access\\AuthorizesRequests',
        'currentClassName' => 'Illuminate\\Foundation\\Auth\\Access\\AuthorizesRequests',
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