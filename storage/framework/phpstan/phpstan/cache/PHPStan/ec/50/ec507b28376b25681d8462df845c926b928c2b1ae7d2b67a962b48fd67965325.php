<?php declare(strict_types = 1);

// osfsl-/var/www/html/vendor/composer/../laravel/framework/src/Illuminate/Database/Eloquent/ModelNotFoundException.php-PHPStan\BetterReflection\Reflection\ReflectionClass-Illuminate\Database\Eloquent\ModelNotFoundException
return \PHPStan\Cache\CacheItem::__set_state(array(
   'variableKey' => 'v2-0a351aca2b389f4980bf8b9718025f51eb99a458439edb347b0ea53a005f08d6-8.5.9-6.70.0.3',
   'data' => 
  array (
    'locatedSource' => 
    array (
      'class' => 'PHPStan\\BetterReflection\\SourceLocator\\Located\\LocatedSource',
      'data' => 
      array (
        'name' => 'Illuminate\\Database\\Eloquent\\ModelNotFoundException',
        'filename' => '/var/www/html/vendor/composer/../laravel/framework/src/Illuminate/Database/Eloquent/ModelNotFoundException.php',
      ),
    ),
    'namespace' => 'Illuminate\\Database\\Eloquent',
    'name' => 'Illuminate\\Database\\Eloquent\\ModelNotFoundException',
    'shortName' => 'ModelNotFoundException',
    'isInterface' => false,
    'isTrait' => false,
    'isEnum' => false,
    'isBackedEnum' => false,
    'modifiers' => 0,
    'docComment' => '/**
 * @template TModel of \\Illuminate\\Database\\Eloquent\\Model
 */',
    'attributes' => 
    array (
    ),
    'startLine' => 13,
    'endLine' => 72,
    'startColumn' => 1,
    'endColumn' => 1,
    'parentClassName' => 'Illuminate\\Database\\RecordsNotFoundException',
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
      'model' => 
      array (
        'declaringClassName' => 'Illuminate\\Database\\Eloquent\\ModelNotFoundException',
        'implementingClassName' => 'Illuminate\\Database\\Eloquent\\ModelNotFoundException',
        'name' => 'model',
        'modifiers' => 2,
        'type' => NULL,
        'default' => NULL,
        'docComment' => '/**
 * Name of the affected Eloquent model.
 *
 * @var class-string<TModel>
 */',
        'attributes' => 
        array (
        ),
        'startLine' => 20,
        'endLine' => 20,
        'startColumn' => 5,
        'endColumn' => 21,
        'isPromoted' => false,
        'declaredAtCompileTime' => true,
        'immediateVirtual' => false,
        'immediateHooks' => 
        array (
        ),
      ),
      'ids' => 
      array (
        'declaringClassName' => 'Illuminate\\Database\\Eloquent\\ModelNotFoundException',
        'implementingClassName' => 'Illuminate\\Database\\Eloquent\\ModelNotFoundException',
        'name' => 'ids',
        'modifiers' => 2,
        'type' => NULL,
        'default' => NULL,
        'docComment' => '/**
 * The affected model IDs.
 *
 * @var array<int, int|string>
 */',
        'attributes' => 
        array (
        ),
        'startLine' => 27,
        'endLine' => 27,
        'startColumn' => 5,
        'endColumn' => 19,
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
      'setModel' => 
      array (
        'name' => 'setModel',
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
            'startLine' => 36,
            'endLine' => 36,
            'startColumn' => 30,
            'endColumn' => 35,
            'parameterIndex' => 0,
            'isOptional' => false,
          ),
          'ids' => 
          array (
            'name' => 'ids',
            'default' => 
            array (
              'code' => '[]',
              'attributes' => 
              array (
                'startLine' => 36,
                'endLine' => 36,
                'startTokenPos' => 65,
                'startFilePos' => 780,
                'endTokenPos' => 66,
                'endFilePos' => 781,
              ),
            ),
            'type' => NULL,
            'isVariadic' => false,
            'byRef' => false,
            'isPromoted' => false,
            'attributes' => 
            array (
            ),
            'startLine' => 36,
            'endLine' => 36,
            'startColumn' => 38,
            'endColumn' => 46,
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
 * Set the affected Eloquent model and instance ids.
 *
 * @param  class-string<TModel>  $model
 * @param  array<int, int|string>|int|string  $ids
 * @return $this
 */',
        'startLine' => 36,
        'endLine' => 51,
        'startColumn' => 5,
        'endColumn' => 5,
        'couldThrow' => false,
        'isClosure' => false,
        'isGenerator' => false,
        'isVariadic' => false,
        'modifiers' => 1,
        'namespace' => 'Illuminate\\Database\\Eloquent',
        'declaringClassName' => 'Illuminate\\Database\\Eloquent\\ModelNotFoundException',
        'implementingClassName' => 'Illuminate\\Database\\Eloquent\\ModelNotFoundException',
        'currentClassName' => 'Illuminate\\Database\\Eloquent\\ModelNotFoundException',
        'aliasName' => NULL,
      ),
      'getModel' => 
      array (
        'name' => 'getModel',
        'parameters' => 
        array (
        ),
        'returnsReference' => false,
        'returnType' => NULL,
        'attributes' => 
        array (
        ),
        'docComment' => '/**
 * Get the affected Eloquent model.
 *
 * @return class-string<TModel>
 */',
        'startLine' => 58,
        'endLine' => 61,
        'startColumn' => 5,
        'endColumn' => 5,
        'couldThrow' => false,
        'isClosure' => false,
        'isGenerator' => false,
        'isVariadic' => false,
        'modifiers' => 1,
        'namespace' => 'Illuminate\\Database\\Eloquent',
        'declaringClassName' => 'Illuminate\\Database\\Eloquent\\ModelNotFoundException',
        'implementingClassName' => 'Illuminate\\Database\\Eloquent\\ModelNotFoundException',
        'currentClassName' => 'Illuminate\\Database\\Eloquent\\ModelNotFoundException',
        'aliasName' => NULL,
      ),
      'getIds' => 
      array (
        'name' => 'getIds',
        'parameters' => 
        array (
        ),
        'returnsReference' => false,
        'returnType' => NULL,
        'attributes' => 
        array (
        ),
        'docComment' => '/**
 * Get the affected Eloquent model IDs.
 *
 * @return array<int, int|string>
 */',
        'startLine' => 68,
        'endLine' => 71,
        'startColumn' => 5,
        'endColumn' => 5,
        'couldThrow' => false,
        'isClosure' => false,
        'isGenerator' => false,
        'isVariadic' => false,
        'modifiers' => 1,
        'namespace' => 'Illuminate\\Database\\Eloquent',
        'declaringClassName' => 'Illuminate\\Database\\Eloquent\\ModelNotFoundException',
        'implementingClassName' => 'Illuminate\\Database\\Eloquent\\ModelNotFoundException',
        'currentClassName' => 'Illuminate\\Database\\Eloquent\\ModelNotFoundException',
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