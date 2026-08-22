<?php declare(strict_types = 1);

// osfsl-/var/www/html/vendor/composer/../laravel/framework/src/Illuminate/Database/Eloquent/Concerns/HasTimestamps.php-PHPStan\BetterReflection\Reflection\ReflectionClass-Illuminate\Database\Eloquent\Concerns\HasTimestamps
return \PHPStan\Cache\CacheItem::__set_state(array(
   'variableKey' => 'v2-25ec1bf4a0be4595f82064e534fd42fdaa62013ef6ce5a79d5a136304f44aa7a-8.5.9-6.70.0.3',
   'data' => 
  array (
    'locatedSource' => 
    array (
      'class' => 'PHPStan\\BetterReflection\\SourceLocator\\Located\\LocatedSource',
      'data' => 
      array (
        'name' => 'Illuminate\\Database\\Eloquent\\Concerns\\HasTimestamps',
        'filename' => '/var/www/html/vendor/composer/../laravel/framework/src/Illuminate/Database/Eloquent/Concerns/HasTimestamps.php',
      ),
    ),
    'namespace' => 'Illuminate\\Database\\Eloquent\\Concerns',
    'name' => 'Illuminate\\Database\\Eloquent\\Concerns\\HasTimestamps',
    'shortName' => 'HasTimestamps',
    'isInterface' => false,
    'isTrait' => true,
    'isEnum' => false,
    'isBackedEnum' => false,
    'modifiers' => 0,
    'docComment' => NULL,
    'attributes' => 
    array (
    ),
    'startLine' => 11,
    'endLine' => 255,
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
      'timestamps' => 
      array (
        'declaringClassName' => 'Illuminate\\Database\\Eloquent\\Concerns\\HasTimestamps',
        'implementingClassName' => 'Illuminate\\Database\\Eloquent\\Concerns\\HasTimestamps',
        'name' => 'timestamps',
        'modifiers' => 1,
        'type' => NULL,
        'default' => 
        array (
          'code' => 'true',
          'attributes' => 
          array (
            'startLine' => 18,
            'endLine' => 18,
            'startTokenPos' => 46,
            'startFilePos' => 433,
            'endTokenPos' => 46,
            'endFilePos' => 436,
          ),
        ),
        'docComment' => '/**
 * Indicates if the model should be timestamped.
 *
 * @var bool
 */',
        'attributes' => 
        array (
        ),
        'startLine' => 18,
        'endLine' => 18,
        'startColumn' => 5,
        'endColumn' => 30,
        'isPromoted' => false,
        'declaredAtCompileTime' => true,
        'immediateVirtual' => false,
        'immediateHooks' => 
        array (
        ),
      ),
      'ignoreTimestampsOn' => 
      array (
        'declaringClassName' => 'Illuminate\\Database\\Eloquent\\Concerns\\HasTimestamps',
        'implementingClassName' => 'Illuminate\\Database\\Eloquent\\Concerns\\HasTimestamps',
        'name' => 'ignoreTimestampsOn',
        'modifiers' => 18,
        'type' => NULL,
        'default' => 
        array (
          'code' => '[]',
          'attributes' => 
          array (
            'startLine' => 25,
            'endLine' => 25,
            'startTokenPos' => 59,
            'startFilePos' => 601,
            'endTokenPos' => 60,
            'endFilePos' => 602,
          ),
        ),
        'docComment' => '/**
 * The list of models classes that have timestamps temporarily disabled.
 *
 * @var array
 */',
        'attributes' => 
        array (
        ),
        'startLine' => 25,
        'endLine' => 25,
        'startColumn' => 5,
        'endColumn' => 46,
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
      'initializeHasTimestamps' => 
      array (
        'name' => 'initializeHasTimestamps',
        'parameters' => 
        array (
        ),
        'returnsReference' => false,
        'returnType' => NULL,
        'attributes' => 
        array (
          0 => 
          array (
            'name' => 'Illuminate\\Database\\Eloquent\\Attributes\\Initialize',
            'isRepeated' => false,
            'arguments' => 
            array (
            ),
          ),
        ),
        'docComment' => '/**
 * Initialize the HasTimestamps trait.
 *
 * @return void
 */',
        'startLine' => 32,
        'endLine' => 42,
        'startColumn' => 5,
        'endColumn' => 5,
        'couldThrow' => false,
        'isClosure' => false,
        'isGenerator' => false,
        'isVariadic' => false,
        'modifiers' => 1,
        'namespace' => 'Illuminate\\Database\\Eloquent\\Concerns',
        'declaringClassName' => 'Illuminate\\Database\\Eloquent\\Concerns\\HasTimestamps',
        'implementingClassName' => 'Illuminate\\Database\\Eloquent\\Concerns\\HasTimestamps',
        'currentClassName' => 'Illuminate\\Database\\Eloquent\\Concerns\\HasTimestamps',
        'aliasName' => NULL,
      ),
      'touch' => 
      array (
        'name' => 'touch',
        'parameters' => 
        array (
          'attribute' => 
          array (
            'name' => 'attribute',
            'default' => 
            array (
              'code' => 'null',
              'attributes' => 
              array (
                'startLine' => 50,
                'endLine' => 50,
                'startTokenPos' => 183,
                'startFilePos' => 1300,
                'endTokenPos' => 183,
                'endFilePos' => 1303,
              ),
            ),
            'type' => NULL,
            'isVariadic' => false,
            'byRef' => false,
            'isPromoted' => false,
            'attributes' => 
            array (
            ),
            'startLine' => 50,
            'endLine' => 50,
            'startColumn' => 27,
            'endColumn' => 43,
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
 * Update the model\'s update timestamp.
 *
 * @param  array|string|null  $attribute
 * @return bool
 */',
        'startLine' => 50,
        'endLine' => 69,
        'startColumn' => 5,
        'endColumn' => 5,
        'couldThrow' => false,
        'isClosure' => false,
        'isGenerator' => false,
        'isVariadic' => false,
        'modifiers' => 1,
        'namespace' => 'Illuminate\\Database\\Eloquent\\Concerns',
        'declaringClassName' => 'Illuminate\\Database\\Eloquent\\Concerns\\HasTimestamps',
        'implementingClassName' => 'Illuminate\\Database\\Eloquent\\Concerns\\HasTimestamps',
        'currentClassName' => 'Illuminate\\Database\\Eloquent\\Concerns\\HasTimestamps',
        'aliasName' => NULL,
      ),
      'touchQuietly' => 
      array (
        'name' => 'touchQuietly',
        'parameters' => 
        array (
          'attribute' => 
          array (
            'name' => 'attribute',
            'default' => 
            array (
              'code' => 'null',
              'attributes' => 
              array (
                'startLine' => 77,
                'endLine' => 77,
                'startTokenPos' => 299,
                'startFilePos' => 1899,
                'endTokenPos' => 299,
                'endFilePos' => 1902,
              ),
            ),
            'type' => NULL,
            'isVariadic' => false,
            'byRef' => false,
            'isPromoted' => false,
            'attributes' => 
            array (
            ),
            'startLine' => 77,
            'endLine' => 77,
            'startColumn' => 34,
            'endColumn' => 50,
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
 * Update the model\'s update timestamp without raising any events.
 *
 * @param  array|string|null  $attribute
 * @return bool
 */',
        'startLine' => 77,
        'endLine' => 80,
        'startColumn' => 5,
        'endColumn' => 5,
        'couldThrow' => false,
        'isClosure' => false,
        'isGenerator' => false,
        'isVariadic' => false,
        'modifiers' => 1,
        'namespace' => 'Illuminate\\Database\\Eloquent\\Concerns',
        'declaringClassName' => 'Illuminate\\Database\\Eloquent\\Concerns\\HasTimestamps',
        'implementingClassName' => 'Illuminate\\Database\\Eloquent\\Concerns\\HasTimestamps',
        'currentClassName' => 'Illuminate\\Database\\Eloquent\\Concerns\\HasTimestamps',
        'aliasName' => NULL,
      ),
      'updateTimestamps' => 
      array (
        'name' => 'updateTimestamps',
        'parameters' => 
        array (
        ),
        'returnsReference' => false,
        'returnType' => NULL,
        'attributes' => 
        array (
        ),
        'docComment' => '/**
 * Update the creation and update timestamps.
 *
 * @return $this
 */',
        'startLine' => 87,
        'endLine' => 104,
        'startColumn' => 5,
        'endColumn' => 5,
        'couldThrow' => false,
        'isClosure' => false,
        'isGenerator' => false,
        'isVariadic' => false,
        'modifiers' => 1,
        'namespace' => 'Illuminate\\Database\\Eloquent\\Concerns',
        'declaringClassName' => 'Illuminate\\Database\\Eloquent\\Concerns\\HasTimestamps',
        'implementingClassName' => 'Illuminate\\Database\\Eloquent\\Concerns\\HasTimestamps',
        'currentClassName' => 'Illuminate\\Database\\Eloquent\\Concerns\\HasTimestamps',
        'aliasName' => NULL,
      ),
      'setCreatedAt' => 
      array (
        'name' => 'setCreatedAt',
        'parameters' => 
        array (
          'value' => 
          array (
            'name' => 'value',
            'default' => NULL,
            'type' => NULL,
            'isVariadic' => false,
            'byRef' => false,
            'isPromoted' => false,
            'attributes' => 
            array (
            ),
            'startLine' => 112,
            'endLine' => 112,
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
 * Set the value of the "created at" attribute.
 *
 * @param  mixed  $value
 * @return $this
 */',
        'startLine' => 112,
        'endLine' => 117,
        'startColumn' => 5,
        'endColumn' => 5,
        'couldThrow' => false,
        'isClosure' => false,
        'isGenerator' => false,
        'isVariadic' => false,
        'modifiers' => 1,
        'namespace' => 'Illuminate\\Database\\Eloquent\\Concerns',
        'declaringClassName' => 'Illuminate\\Database\\Eloquent\\Concerns\\HasTimestamps',
        'implementingClassName' => 'Illuminate\\Database\\Eloquent\\Concerns\\HasTimestamps',
        'currentClassName' => 'Illuminate\\Database\\Eloquent\\Concerns\\HasTimestamps',
        'aliasName' => NULL,
      ),
      'setUpdatedAt' => 
      array (
        'name' => 'setUpdatedAt',
        'parameters' => 
        array (
          'value' => 
          array (
            'name' => 'value',
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
 * Set the value of the "updated at" attribute.
 *
 * @param  mixed  $value
 * @return $this
 */',
        'startLine' => 125,
        'endLine' => 130,
        'startColumn' => 5,
        'endColumn' => 5,
        'couldThrow' => false,
        'isClosure' => false,
        'isGenerator' => false,
        'isVariadic' => false,
        'modifiers' => 1,
        'namespace' => 'Illuminate\\Database\\Eloquent\\Concerns',
        'declaringClassName' => 'Illuminate\\Database\\Eloquent\\Concerns\\HasTimestamps',
        'implementingClassName' => 'Illuminate\\Database\\Eloquent\\Concerns\\HasTimestamps',
        'currentClassName' => 'Illuminate\\Database\\Eloquent\\Concerns\\HasTimestamps',
        'aliasName' => NULL,
      ),
      'freshTimestamp' => 
      array (
        'name' => 'freshTimestamp',
        'parameters' => 
        array (
        ),
        'returnsReference' => false,
        'returnType' => NULL,
        'attributes' => 
        array (
        ),
        'docComment' => '/**
 * Get a fresh timestamp for the model.
 *
 * @return \\Illuminate\\Support\\Carbon
 */',
        'startLine' => 137,
        'endLine' => 140,
        'startColumn' => 5,
        'endColumn' => 5,
        'couldThrow' => false,
        'isClosure' => false,
        'isGenerator' => false,
        'isVariadic' => false,
        'modifiers' => 1,
        'namespace' => 'Illuminate\\Database\\Eloquent\\Concerns',
        'declaringClassName' => 'Illuminate\\Database\\Eloquent\\Concerns\\HasTimestamps',
        'implementingClassName' => 'Illuminate\\Database\\Eloquent\\Concerns\\HasTimestamps',
        'currentClassName' => 'Illuminate\\Database\\Eloquent\\Concerns\\HasTimestamps',
        'aliasName' => NULL,
      ),
      'freshTimestampString' => 
      array (
        'name' => 'freshTimestampString',
        'parameters' => 
        array (
        ),
        'returnsReference' => false,
        'returnType' => NULL,
        'attributes' => 
        array (
        ),
        'docComment' => '/**
 * Get a fresh timestamp for the model.
 *
 * @return string
 */',
        'startLine' => 147,
        'endLine' => 150,
        'startColumn' => 5,
        'endColumn' => 5,
        'couldThrow' => false,
        'isClosure' => false,
        'isGenerator' => false,
        'isVariadic' => false,
        'modifiers' => 1,
        'namespace' => 'Illuminate\\Database\\Eloquent\\Concerns',
        'declaringClassName' => 'Illuminate\\Database\\Eloquent\\Concerns\\HasTimestamps',
        'implementingClassName' => 'Illuminate\\Database\\Eloquent\\Concerns\\HasTimestamps',
        'currentClassName' => 'Illuminate\\Database\\Eloquent\\Concerns\\HasTimestamps',
        'aliasName' => NULL,
      ),
      'usesTimestamps' => 
      array (
        'name' => 'usesTimestamps',
        'parameters' => 
        array (
        ),
        'returnsReference' => false,
        'returnType' => NULL,
        'attributes' => 
        array (
        ),
        'docComment' => '/**
 * Determine if the model uses timestamps.
 *
 * @return bool
 */',
        'startLine' => 157,
        'endLine' => 160,
        'startColumn' => 5,
        'endColumn' => 5,
        'couldThrow' => false,
        'isClosure' => false,
        'isGenerator' => false,
        'isVariadic' => false,
        'modifiers' => 1,
        'namespace' => 'Illuminate\\Database\\Eloquent\\Concerns',
        'declaringClassName' => 'Illuminate\\Database\\Eloquent\\Concerns\\HasTimestamps',
        'implementingClassName' => 'Illuminate\\Database\\Eloquent\\Concerns\\HasTimestamps',
        'currentClassName' => 'Illuminate\\Database\\Eloquent\\Concerns\\HasTimestamps',
        'aliasName' => NULL,
      ),
      'getCreatedAtColumn' => 
      array (
        'name' => 'getCreatedAtColumn',
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
 * @return string|null
 */',
        'startLine' => 167,
        'endLine' => 170,
        'startColumn' => 5,
        'endColumn' => 5,
        'couldThrow' => false,
        'isClosure' => false,
        'isGenerator' => false,
        'isVariadic' => false,
        'modifiers' => 1,
        'namespace' => 'Illuminate\\Database\\Eloquent\\Concerns',
        'declaringClassName' => 'Illuminate\\Database\\Eloquent\\Concerns\\HasTimestamps',
        'implementingClassName' => 'Illuminate\\Database\\Eloquent\\Concerns\\HasTimestamps',
        'currentClassName' => 'Illuminate\\Database\\Eloquent\\Concerns\\HasTimestamps',
        'aliasName' => NULL,
      ),
      'getUpdatedAtColumn' => 
      array (
        'name' => 'getUpdatedAtColumn',
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
 * @return string|null
 */',
        'startLine' => 177,
        'endLine' => 180,
        'startColumn' => 5,
        'endColumn' => 5,
        'couldThrow' => false,
        'isClosure' => false,
        'isGenerator' => false,
        'isVariadic' => false,
        'modifiers' => 1,
        'namespace' => 'Illuminate\\Database\\Eloquent\\Concerns',
        'declaringClassName' => 'Illuminate\\Database\\Eloquent\\Concerns\\HasTimestamps',
        'implementingClassName' => 'Illuminate\\Database\\Eloquent\\Concerns\\HasTimestamps',
        'currentClassName' => 'Illuminate\\Database\\Eloquent\\Concerns\\HasTimestamps',
        'aliasName' => NULL,
      ),
      'getQualifiedCreatedAtColumn' => 
      array (
        'name' => 'getQualifiedCreatedAtColumn',
        'parameters' => 
        array (
        ),
        'returnsReference' => false,
        'returnType' => NULL,
        'attributes' => 
        array (
        ),
        'docComment' => '/**
 * Get the fully-qualified "created at" column.
 *
 * @return string|null
 */',
        'startLine' => 187,
        'endLine' => 192,
        'startColumn' => 5,
        'endColumn' => 5,
        'couldThrow' => false,
        'isClosure' => false,
        'isGenerator' => false,
        'isVariadic' => false,
        'modifiers' => 1,
        'namespace' => 'Illuminate\\Database\\Eloquent\\Concerns',
        'declaringClassName' => 'Illuminate\\Database\\Eloquent\\Concerns\\HasTimestamps',
        'implementingClassName' => 'Illuminate\\Database\\Eloquent\\Concerns\\HasTimestamps',
        'currentClassName' => 'Illuminate\\Database\\Eloquent\\Concerns\\HasTimestamps',
        'aliasName' => NULL,
      ),
      'getQualifiedUpdatedAtColumn' => 
      array (
        'name' => 'getQualifiedUpdatedAtColumn',
        'parameters' => 
        array (
        ),
        'returnsReference' => false,
        'returnType' => NULL,
        'attributes' => 
        array (
        ),
        'docComment' => '/**
 * Get the fully-qualified "updated at" column.
 *
 * @return string|null
 */',
        'startLine' => 199,
        'endLine' => 204,
        'startColumn' => 5,
        'endColumn' => 5,
        'couldThrow' => false,
        'isClosure' => false,
        'isGenerator' => false,
        'isVariadic' => false,
        'modifiers' => 1,
        'namespace' => 'Illuminate\\Database\\Eloquent\\Concerns',
        'declaringClassName' => 'Illuminate\\Database\\Eloquent\\Concerns\\HasTimestamps',
        'implementingClassName' => 'Illuminate\\Database\\Eloquent\\Concerns\\HasTimestamps',
        'currentClassName' => 'Illuminate\\Database\\Eloquent\\Concerns\\HasTimestamps',
        'aliasName' => NULL,
      ),
      'withoutTimestamps' => 
      array (
        'name' => 'withoutTimestamps',
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
                'name' => 'callable',
                'isIdentifier' => true,
              ),
            ),
            'isVariadic' => false,
            'byRef' => false,
            'isPromoted' => false,
            'attributes' => 
            array (
            ),
            'startLine' => 214,
            'endLine' => 214,
            'startColumn' => 46,
            'endColumn' => 63,
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
 * Disable timestamps for the current class during the given callback scope.
 *
 * @template TReturn
 *
 * @param  (callable(): TReturn)  $callback
 * @return TReturn
 */',
        'startLine' => 214,
        'endLine' => 217,
        'startColumn' => 5,
        'endColumn' => 5,
        'couldThrow' => false,
        'isClosure' => false,
        'isGenerator' => false,
        'isVariadic' => false,
        'modifiers' => 17,
        'namespace' => 'Illuminate\\Database\\Eloquent\\Concerns',
        'declaringClassName' => 'Illuminate\\Database\\Eloquent\\Concerns\\HasTimestamps',
        'implementingClassName' => 'Illuminate\\Database\\Eloquent\\Concerns\\HasTimestamps',
        'currentClassName' => 'Illuminate\\Database\\Eloquent\\Concerns\\HasTimestamps',
        'aliasName' => NULL,
      ),
      'withoutTimestampsOn' => 
      array (
        'name' => 'withoutTimestampsOn',
        'parameters' => 
        array (
          'models' => 
          array (
            'name' => 'models',
            'default' => NULL,
            'type' => NULL,
            'isVariadic' => false,
            'byRef' => false,
            'isPromoted' => false,
            'attributes' => 
            array (
            ),
            'startLine' => 228,
            'endLine' => 228,
            'startColumn' => 48,
            'endColumn' => 54,
            'parameterIndex' => 0,
            'isOptional' => false,
          ),
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
            'startLine' => 228,
            'endLine' => 228,
            'startColumn' => 57,
            'endColumn' => 65,
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
 * Disable timestamps for the given model classes during the given callback scope.
 *
 * @template TReturn
 *
 * @param  array  $models
 * @param  callable(): TReturn  $callback
 * @return TReturn
 */',
        'startLine' => 228,
        'endLine' => 241,
        'startColumn' => 5,
        'endColumn' => 5,
        'couldThrow' => false,
        'isClosure' => false,
        'isGenerator' => false,
        'isVariadic' => false,
        'modifiers' => 17,
        'namespace' => 'Illuminate\\Database\\Eloquent\\Concerns',
        'declaringClassName' => 'Illuminate\\Database\\Eloquent\\Concerns\\HasTimestamps',
        'implementingClassName' => 'Illuminate\\Database\\Eloquent\\Concerns\\HasTimestamps',
        'currentClassName' => 'Illuminate\\Database\\Eloquent\\Concerns\\HasTimestamps',
        'aliasName' => NULL,
      ),
      'isIgnoringTimestamps' => 
      array (
        'name' => 'isIgnoringTimestamps',
        'parameters' => 
        array (
          'class' => 
          array (
            'name' => 'class',
            'default' => 
            array (
              'code' => 'null',
              'attributes' => 
              array (
                'startLine' => 249,
                'endLine' => 249,
                'startTokenPos' => 904,
                'startFilePos' => 5950,
                'endTokenPos' => 904,
                'endFilePos' => 5953,
              ),
            ),
            'type' => NULL,
            'isVariadic' => false,
            'byRef' => false,
            'isPromoted' => false,
            'attributes' => 
            array (
            ),
            'startLine' => 249,
            'endLine' => 249,
            'startColumn' => 49,
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
 * Determine if the given model is ignoring timestamps / touches.
 *
 * @param  string|null  $class
 * @return bool
 */',
        'startLine' => 249,
        'endLine' => 254,
        'startColumn' => 5,
        'endColumn' => 5,
        'couldThrow' => false,
        'isClosure' => false,
        'isGenerator' => false,
        'isVariadic' => false,
        'modifiers' => 17,
        'namespace' => 'Illuminate\\Database\\Eloquent\\Concerns',
        'declaringClassName' => 'Illuminate\\Database\\Eloquent\\Concerns\\HasTimestamps',
        'implementingClassName' => 'Illuminate\\Database\\Eloquent\\Concerns\\HasTimestamps',
        'currentClassName' => 'Illuminate\\Database\\Eloquent\\Concerns\\HasTimestamps',
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