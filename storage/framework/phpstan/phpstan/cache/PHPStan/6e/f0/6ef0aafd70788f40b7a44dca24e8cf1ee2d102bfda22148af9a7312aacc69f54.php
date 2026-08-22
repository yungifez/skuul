<?php declare(strict_types = 1);

// osfsl-/var/www/html/vendor/composer/../laravel/framework/src/Illuminate/Database/Schema/Blueprint.php-PHPStan\BetterReflection\Reflection\ReflectionClass-Illuminate\Database\Schema\Blueprint
return \PHPStan\Cache\CacheItem::__set_state(array(
   'variableKey' => 'v2-549fe87dc61993b1e91e89f6afe8500ca94c5e43e3dea31e806920300f0332fe-8.5.9-6.70.0.3',
   'data' => 
  array (
    'locatedSource' => 
    array (
      'class' => 'PHPStan\\BetterReflection\\SourceLocator\\Located\\LocatedSource',
      'data' => 
      array (
        'name' => 'Illuminate\\Database\\Schema\\Blueprint',
        'filename' => '/var/www/html/vendor/composer/../laravel/framework/src/Illuminate/Database/Schema/Blueprint.php',
      ),
    ),
    'namespace' => 'Illuminate\\Database\\Schema',
    'name' => 'Illuminate\\Database\\Schema\\Blueprint',
    'shortName' => 'Blueprint',
    'isInterface' => false,
    'isTrait' => false,
    'isEnum' => false,
    'isBackedEnum' => false,
    'modifiers' => 0,
    'docComment' => NULL,
    'attributes' => 
    array (
    ),
    'startLine' => 19,
    'endLine' => 2024,
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
      'connection' => 
      array (
        'declaringClassName' => 'Illuminate\\Database\\Schema\\Blueprint',
        'implementingClassName' => 'Illuminate\\Database\\Schema\\Blueprint',
        'name' => 'connection',
        'modifiers' => 2,
        'type' => 
        array (
          'class' => 'PHPStan\\BetterReflection\\Reflection\\ReflectionNamedType',
          'data' => 
          array (
            'name' => 'Illuminate\\Database\\Connection',
            'isIdentifier' => false,
          ),
        ),
        'default' => NULL,
        'docComment' => '/**
 * The database connection instance.
 */',
        'attributes' => 
        array (
        ),
        'startLine' => 26,
        'endLine' => 26,
        'startColumn' => 5,
        'endColumn' => 37,
        'isPromoted' => false,
        'declaredAtCompileTime' => true,
        'immediateVirtual' => false,
        'immediateHooks' => 
        array (
        ),
      ),
      'grammar' => 
      array (
        'declaringClassName' => 'Illuminate\\Database\\Schema\\Blueprint',
        'implementingClassName' => 'Illuminate\\Database\\Schema\\Blueprint',
        'name' => 'grammar',
        'modifiers' => 2,
        'type' => 
        array (
          'class' => 'PHPStan\\BetterReflection\\Reflection\\ReflectionNamedType',
          'data' => 
          array (
            'name' => 'Illuminate\\Database\\Schema\\Grammars\\Grammar',
            'isIdentifier' => false,
          ),
        ),
        'default' => NULL,
        'docComment' => '/**
 * The schema grammar instance.
 */',
        'attributes' => 
        array (
        ),
        'startLine' => 31,
        'endLine' => 31,
        'startColumn' => 5,
        'endColumn' => 31,
        'isPromoted' => false,
        'declaredAtCompileTime' => true,
        'immediateVirtual' => false,
        'immediateHooks' => 
        array (
        ),
      ),
      'table' => 
      array (
        'declaringClassName' => 'Illuminate\\Database\\Schema\\Blueprint',
        'implementingClassName' => 'Illuminate\\Database\\Schema\\Blueprint',
        'name' => 'table',
        'modifiers' => 2,
        'type' => NULL,
        'default' => NULL,
        'docComment' => '/**
 * The table the blueprint describes.
 *
 * @var string
 */',
        'attributes' => 
        array (
        ),
        'startLine' => 38,
        'endLine' => 38,
        'startColumn' => 5,
        'endColumn' => 21,
        'isPromoted' => false,
        'declaredAtCompileTime' => true,
        'immediateVirtual' => false,
        'immediateHooks' => 
        array (
        ),
      ),
      'columns' => 
      array (
        'declaringClassName' => 'Illuminate\\Database\\Schema\\Blueprint',
        'implementingClassName' => 'Illuminate\\Database\\Schema\\Blueprint',
        'name' => 'columns',
        'modifiers' => 2,
        'type' => NULL,
        'default' => 
        array (
          'code' => '[]',
          'attributes' => 
          array (
            'startLine' => 45,
            'endLine' => 45,
            'startTokenPos' => 113,
            'startFilePos' => 1043,
            'endTokenPos' => 114,
            'endFilePos' => 1044,
          ),
        ),
        'docComment' => '/**
 * The columns that should be added to the table.
 *
 * @var \\Illuminate\\Database\\Schema\\ColumnDefinition[]
 */',
        'attributes' => 
        array (
        ),
        'startLine' => 45,
        'endLine' => 45,
        'startColumn' => 5,
        'endColumn' => 28,
        'isPromoted' => false,
        'declaredAtCompileTime' => true,
        'immediateVirtual' => false,
        'immediateHooks' => 
        array (
        ),
      ),
      'commands' => 
      array (
        'declaringClassName' => 'Illuminate\\Database\\Schema\\Blueprint',
        'implementingClassName' => 'Illuminate\\Database\\Schema\\Blueprint',
        'name' => 'commands',
        'modifiers' => 2,
        'type' => NULL,
        'default' => 
        array (
          'code' => '[]',
          'attributes' => 
          array (
            'startLine' => 52,
            'endLine' => 52,
            'startTokenPos' => 125,
            'startFilePos' => 1192,
            'endTokenPos' => 126,
            'endFilePos' => 1193,
          ),
        ),
        'docComment' => '/**
 * The commands that should be run for the table.
 *
 * @var \\Illuminate\\Support\\Fluent[]
 */',
        'attributes' => 
        array (
        ),
        'startLine' => 52,
        'endLine' => 52,
        'startColumn' => 5,
        'endColumn' => 29,
        'isPromoted' => false,
        'declaredAtCompileTime' => true,
        'immediateVirtual' => false,
        'immediateHooks' => 
        array (
        ),
      ),
      'engine' => 
      array (
        'declaringClassName' => 'Illuminate\\Database\\Schema\\Blueprint',
        'implementingClassName' => 'Illuminate\\Database\\Schema\\Blueprint',
        'name' => 'engine',
        'modifiers' => 1,
        'type' => NULL,
        'default' => NULL,
        'docComment' => '/**
 * The storage engine that should be used for the table.
 *
 * @var string
 */',
        'attributes' => 
        array (
        ),
        'startLine' => 59,
        'endLine' => 59,
        'startColumn' => 5,
        'endColumn' => 19,
        'isPromoted' => false,
        'declaredAtCompileTime' => true,
        'immediateVirtual' => false,
        'immediateHooks' => 
        array (
        ),
      ),
      'charset' => 
      array (
        'declaringClassName' => 'Illuminate\\Database\\Schema\\Blueprint',
        'implementingClassName' => 'Illuminate\\Database\\Schema\\Blueprint',
        'name' => 'charset',
        'modifiers' => 1,
        'type' => NULL,
        'default' => NULL,
        'docComment' => '/**
 * The default character set that should be used for the table.
 *
 * @var string
 */',
        'attributes' => 
        array (
        ),
        'startLine' => 66,
        'endLine' => 66,
        'startColumn' => 5,
        'endColumn' => 20,
        'isPromoted' => false,
        'declaredAtCompileTime' => true,
        'immediateVirtual' => false,
        'immediateHooks' => 
        array (
        ),
      ),
      'collation' => 
      array (
        'declaringClassName' => 'Illuminate\\Database\\Schema\\Blueprint',
        'implementingClassName' => 'Illuminate\\Database\\Schema\\Blueprint',
        'name' => 'collation',
        'modifiers' => 1,
        'type' => NULL,
        'default' => NULL,
        'docComment' => '/**
 * The collation that should be used for the table.
 *
 * @var string
 */',
        'attributes' => 
        array (
        ),
        'startLine' => 73,
        'endLine' => 73,
        'startColumn' => 5,
        'endColumn' => 22,
        'isPromoted' => false,
        'declaredAtCompileTime' => true,
        'immediateVirtual' => false,
        'immediateHooks' => 
        array (
        ),
      ),
      'temporary' => 
      array (
        'declaringClassName' => 'Illuminate\\Database\\Schema\\Blueprint',
        'implementingClassName' => 'Illuminate\\Database\\Schema\\Blueprint',
        'name' => 'temporary',
        'modifiers' => 1,
        'type' => NULL,
        'default' => 
        array (
          'code' => 'false',
          'attributes' => 
          array (
            'startLine' => 80,
            'endLine' => 80,
            'startTokenPos' => 158,
            'startFilePos' => 1683,
            'endTokenPos' => 158,
            'endFilePos' => 1687,
          ),
        ),
        'docComment' => '/**
 * Whether to make the table temporary.
 *
 * @var bool
 */',
        'attributes' => 
        array (
        ),
        'startLine' => 80,
        'endLine' => 80,
        'startColumn' => 5,
        'endColumn' => 30,
        'isPromoted' => false,
        'declaredAtCompileTime' => true,
        'immediateVirtual' => false,
        'immediateHooks' => 
        array (
        ),
      ),
      'after' => 
      array (
        'declaringClassName' => 'Illuminate\\Database\\Schema\\Blueprint',
        'implementingClassName' => 'Illuminate\\Database\\Schema\\Blueprint',
        'name' => 'after',
        'modifiers' => 1,
        'type' => NULL,
        'default' => NULL,
        'docComment' => '/**
 * The column to add new columns after.
 *
 * @var string
 */',
        'attributes' => 
        array (
        ),
        'startLine' => 87,
        'endLine' => 87,
        'startColumn' => 5,
        'endColumn' => 18,
        'isPromoted' => false,
        'declaredAtCompileTime' => true,
        'immediateVirtual' => false,
        'immediateHooks' => 
        array (
        ),
      ),
      'state' => 
      array (
        'declaringClassName' => 'Illuminate\\Database\\Schema\\Blueprint',
        'implementingClassName' => 'Illuminate\\Database\\Schema\\Blueprint',
        'name' => 'state',
        'modifiers' => 2,
        'type' => NULL,
        'default' => NULL,
        'docComment' => '/**
 * The blueprint state instance.
 *
 * @var \\Illuminate\\Database\\Schema\\BlueprintState|null
 */',
        'attributes' => 
        array (
        ),
        'startLine' => 94,
        'endLine' => 94,
        'startColumn' => 5,
        'endColumn' => 21,
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
          'connection' => 
          array (
            'name' => 'connection',
            'default' => NULL,
            'type' => 
            array (
              'class' => 'PHPStan\\BetterReflection\\Reflection\\ReflectionNamedType',
              'data' => 
              array (
                'name' => 'Illuminate\\Database\\Connection',
                'isIdentifier' => false,
              ),
            ),
            'isVariadic' => false,
            'byRef' => false,
            'isPromoted' => false,
            'attributes' => 
            array (
            ),
            'startLine' => 103,
            'endLine' => 103,
            'startColumn' => 33,
            'endColumn' => 54,
            'parameterIndex' => 0,
            'isOptional' => false,
          ),
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
            'startLine' => 103,
            'endLine' => 103,
            'startColumn' => 57,
            'endColumn' => 62,
            'parameterIndex' => 1,
            'isOptional' => false,
          ),
          'callback' => 
          array (
            'name' => 'callback',
            'default' => 
            array (
              'code' => 'null',
              'attributes' => 
              array (
                'startLine' => 103,
                'endLine' => 103,
                'startTokenPos' => 198,
                'startFilePos' => 2230,
                'endTokenPos' => 198,
                'endFilePos' => 2233,
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
                      'name' => 'Closure',
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
            'startLine' => 103,
            'endLine' => 103,
            'startColumn' => 65,
            'endColumn' => 89,
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
 * Create a new schema blueprint.
 *
 * @param  \\Illuminate\\Database\\Connection  $connection
 * @param  string  $table
 * @param  (\\Closure(self): void)|null  $callback
 */',
        'startLine' => 103,
        'endLine' => 112,
        'startColumn' => 5,
        'endColumn' => 5,
        'couldThrow' => false,
        'isClosure' => false,
        'isGenerator' => false,
        'isVariadic' => false,
        'modifiers' => 1,
        'namespace' => 'Illuminate\\Database\\Schema',
        'declaringClassName' => 'Illuminate\\Database\\Schema\\Blueprint',
        'implementingClassName' => 'Illuminate\\Database\\Schema\\Blueprint',
        'currentClassName' => 'Illuminate\\Database\\Schema\\Blueprint',
        'aliasName' => NULL,
      ),
      'build' => 
      array (
        'name' => 'build',
        'parameters' => 
        array (
        ),
        'returnsReference' => false,
        'returnType' => NULL,
        'attributes' => 
        array (
        ),
        'docComment' => '/**
 * Execute the blueprint against the database.
 *
 * @return void
 */',
        'startLine' => 119,
        'endLine' => 124,
        'startColumn' => 5,
        'endColumn' => 5,
        'couldThrow' => false,
        'isClosure' => false,
        'isGenerator' => false,
        'isVariadic' => false,
        'modifiers' => 1,
        'namespace' => 'Illuminate\\Database\\Schema',
        'declaringClassName' => 'Illuminate\\Database\\Schema\\Blueprint',
        'implementingClassName' => 'Illuminate\\Database\\Schema\\Blueprint',
        'currentClassName' => 'Illuminate\\Database\\Schema\\Blueprint',
        'aliasName' => NULL,
      ),
      'toSql' => 
      array (
        'name' => 'toSql',
        'parameters' => 
        array (
        ),
        'returnsReference' => false,
        'returnType' => NULL,
        'attributes' => 
        array (
        ),
        'docComment' => '/**
 * Get the raw SQL statements for the blueprint.
 *
 * @return array
 */',
        'startLine' => 131,
        'endLine' => 161,
        'startColumn' => 5,
        'endColumn' => 5,
        'couldThrow' => false,
        'isClosure' => false,
        'isGenerator' => false,
        'isVariadic' => false,
        'modifiers' => 1,
        'namespace' => 'Illuminate\\Database\\Schema',
        'declaringClassName' => 'Illuminate\\Database\\Schema\\Blueprint',
        'implementingClassName' => 'Illuminate\\Database\\Schema\\Blueprint',
        'currentClassName' => 'Illuminate\\Database\\Schema\\Blueprint',
        'aliasName' => NULL,
      ),
      'ensureCommandsAreValid' => 
      array (
        'name' => 'ensureCommandsAreValid',
        'parameters' => 
        array (
        ),
        'returnsReference' => false,
        'returnType' => NULL,
        'attributes' => 
        array (
        ),
        'docComment' => '/**
 * Ensure the commands on the blueprint are valid for the connection type.
 *
 * @return void
 *
 * @throws \\BadMethodCallException
 */',
        'startLine' => 170,
        'endLine' => 173,
        'startColumn' => 5,
        'endColumn' => 5,
        'couldThrow' => false,
        'isClosure' => false,
        'isGenerator' => false,
        'isVariadic' => false,
        'modifiers' => 2,
        'namespace' => 'Illuminate\\Database\\Schema',
        'declaringClassName' => 'Illuminate\\Database\\Schema\\Blueprint',
        'implementingClassName' => 'Illuminate\\Database\\Schema\\Blueprint',
        'currentClassName' => 'Illuminate\\Database\\Schema\\Blueprint',
        'aliasName' => NULL,
      ),
      'commandsNamed' => 
      array (
        'name' => 'commandsNamed',
        'parameters' => 
        array (
          'names' => 
          array (
            'name' => 'names',
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
            'startLine' => 183,
            'endLine' => 183,
            'startColumn' => 38,
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
 * Get all of the commands matching the given names.
 *
 * @deprecated Will be removed in a future Laravel version.
 *
 * @param  array  $names
 * @return \\Illuminate\\Support\\Collection
 */',
        'startLine' => 183,
        'endLine' => 187,
        'startColumn' => 5,
        'endColumn' => 5,
        'couldThrow' => false,
        'isClosure' => false,
        'isGenerator' => false,
        'isVariadic' => false,
        'modifiers' => 2,
        'namespace' => 'Illuminate\\Database\\Schema',
        'declaringClassName' => 'Illuminate\\Database\\Schema\\Blueprint',
        'implementingClassName' => 'Illuminate\\Database\\Schema\\Blueprint',
        'currentClassName' => 'Illuminate\\Database\\Schema\\Blueprint',
        'aliasName' => NULL,
      ),
      'addImpliedCommands' => 
      array (
        'name' => 'addImpliedCommands',
        'parameters' => 
        array (
        ),
        'returnsReference' => false,
        'returnType' => NULL,
        'attributes' => 
        array (
        ),
        'docComment' => '/**
 * Add the commands that are implied by the blueprint\'s state.
 *
 * @return void
 */',
        'startLine' => 194,
        'endLine' => 209,
        'startColumn' => 5,
        'endColumn' => 5,
        'couldThrow' => false,
        'isClosure' => false,
        'isGenerator' => false,
        'isVariadic' => false,
        'modifiers' => 2,
        'namespace' => 'Illuminate\\Database\\Schema',
        'declaringClassName' => 'Illuminate\\Database\\Schema\\Blueprint',
        'implementingClassName' => 'Illuminate\\Database\\Schema\\Blueprint',
        'currentClassName' => 'Illuminate\\Database\\Schema\\Blueprint',
        'aliasName' => NULL,
      ),
      'addFluentIndexes' => 
      array (
        'name' => 'addFluentIndexes',
        'parameters' => 
        array (
        ),
        'returnsReference' => false,
        'returnType' => NULL,
        'attributes' => 
        array (
        ),
        'docComment' => '/**
 * Add the index commands fluently specified on columns.
 *
 * @return void
 */',
        'startLine' => 216,
        'endLine' => 266,
        'startColumn' => 5,
        'endColumn' => 5,
        'couldThrow' => false,
        'isClosure' => false,
        'isGenerator' => false,
        'isVariadic' => false,
        'modifiers' => 2,
        'namespace' => 'Illuminate\\Database\\Schema',
        'declaringClassName' => 'Illuminate\\Database\\Schema\\Blueprint',
        'implementingClassName' => 'Illuminate\\Database\\Schema\\Blueprint',
        'currentClassName' => 'Illuminate\\Database\\Schema\\Blueprint',
        'aliasName' => NULL,
      ),
      'addFluentCommands' => 
      array (
        'name' => 'addFluentCommands',
        'parameters' => 
        array (
        ),
        'returnsReference' => false,
        'returnType' => NULL,
        'attributes' => 
        array (
        ),
        'docComment' => '/**
 * Add the fluent commands specified on any columns.
 *
 * @return void
 */',
        'startLine' => 273,
        'endLine' => 280,
        'startColumn' => 5,
        'endColumn' => 5,
        'couldThrow' => false,
        'isClosure' => false,
        'isGenerator' => false,
        'isVariadic' => false,
        'modifiers' => 1,
        'namespace' => 'Illuminate\\Database\\Schema',
        'declaringClassName' => 'Illuminate\\Database\\Schema\\Blueprint',
        'implementingClassName' => 'Illuminate\\Database\\Schema\\Blueprint',
        'currentClassName' => 'Illuminate\\Database\\Schema\\Blueprint',
        'aliasName' => NULL,
      ),
      'addAlterCommands' => 
      array (
        'name' => 'addAlterCommands',
        'parameters' => 
        array (
        ),
        'returnsReference' => false,
        'returnType' => NULL,
        'attributes' => 
        array (
        ),
        'docComment' => '/**
 * Add the alter commands if whenever needed.
 *
 * @return void
 */',
        'startLine' => 287,
        'endLine' => 320,
        'startColumn' => 5,
        'endColumn' => 5,
        'couldThrow' => false,
        'isClosure' => false,
        'isGenerator' => false,
        'isVariadic' => false,
        'modifiers' => 1,
        'namespace' => 'Illuminate\\Database\\Schema',
        'declaringClassName' => 'Illuminate\\Database\\Schema\\Blueprint',
        'implementingClassName' => 'Illuminate\\Database\\Schema\\Blueprint',
        'currentClassName' => 'Illuminate\\Database\\Schema\\Blueprint',
        'aliasName' => NULL,
      ),
      'creating' => 
      array (
        'name' => 'creating',
        'parameters' => 
        array (
        ),
        'returnsReference' => false,
        'returnType' => NULL,
        'attributes' => 
        array (
        ),
        'docComment' => '/**
 * Determine if the blueprint has a create command.
 *
 * @return bool
 */',
        'startLine' => 327,
        'endLine' => 331,
        'startColumn' => 5,
        'endColumn' => 5,
        'couldThrow' => false,
        'isClosure' => false,
        'isGenerator' => false,
        'isVariadic' => false,
        'modifiers' => 1,
        'namespace' => 'Illuminate\\Database\\Schema',
        'declaringClassName' => 'Illuminate\\Database\\Schema\\Blueprint',
        'implementingClassName' => 'Illuminate\\Database\\Schema\\Blueprint',
        'currentClassName' => 'Illuminate\\Database\\Schema\\Blueprint',
        'aliasName' => NULL,
      ),
      'create' => 
      array (
        'name' => 'create',
        'parameters' => 
        array (
        ),
        'returnsReference' => false,
        'returnType' => NULL,
        'attributes' => 
        array (
        ),
        'docComment' => '/**
 * Indicate that the table needs to be created.
 *
 * @return \\Illuminate\\Support\\Fluent
 */',
        'startLine' => 338,
        'endLine' => 341,
        'startColumn' => 5,
        'endColumn' => 5,
        'couldThrow' => false,
        'isClosure' => false,
        'isGenerator' => false,
        'isVariadic' => false,
        'modifiers' => 1,
        'namespace' => 'Illuminate\\Database\\Schema',
        'declaringClassName' => 'Illuminate\\Database\\Schema\\Blueprint',
        'implementingClassName' => 'Illuminate\\Database\\Schema\\Blueprint',
        'currentClassName' => 'Illuminate\\Database\\Schema\\Blueprint',
        'aliasName' => NULL,
      ),
      'engine' => 
      array (
        'name' => 'engine',
        'parameters' => 
        array (
          'engine' => 
          array (
            'name' => 'engine',
            'default' => NULL,
            'type' => NULL,
            'isVariadic' => false,
            'byRef' => false,
            'isPromoted' => false,
            'attributes' => 
            array (
            ),
            'startLine' => 349,
            'endLine' => 349,
            'startColumn' => 28,
            'endColumn' => 34,
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
 * Specify the storage engine that should be used for the table.
 *
 * @param  string  $engine
 * @return void
 */',
        'startLine' => 349,
        'endLine' => 352,
        'startColumn' => 5,
        'endColumn' => 5,
        'couldThrow' => false,
        'isClosure' => false,
        'isGenerator' => false,
        'isVariadic' => false,
        'modifiers' => 1,
        'namespace' => 'Illuminate\\Database\\Schema',
        'declaringClassName' => 'Illuminate\\Database\\Schema\\Blueprint',
        'implementingClassName' => 'Illuminate\\Database\\Schema\\Blueprint',
        'currentClassName' => 'Illuminate\\Database\\Schema\\Blueprint',
        'aliasName' => NULL,
      ),
      'innoDb' => 
      array (
        'name' => 'innoDb',
        'parameters' => 
        array (
        ),
        'returnsReference' => false,
        'returnType' => NULL,
        'attributes' => 
        array (
        ),
        'docComment' => '/**
 * Specify that the InnoDB storage engine should be used for the table (MySQL only).
 *
 * @return void
 */',
        'startLine' => 359,
        'endLine' => 362,
        'startColumn' => 5,
        'endColumn' => 5,
        'couldThrow' => false,
        'isClosure' => false,
        'isGenerator' => false,
        'isVariadic' => false,
        'modifiers' => 1,
        'namespace' => 'Illuminate\\Database\\Schema',
        'declaringClassName' => 'Illuminate\\Database\\Schema\\Blueprint',
        'implementingClassName' => 'Illuminate\\Database\\Schema\\Blueprint',
        'currentClassName' => 'Illuminate\\Database\\Schema\\Blueprint',
        'aliasName' => NULL,
      ),
      'charset' => 
      array (
        'name' => 'charset',
        'parameters' => 
        array (
          'charset' => 
          array (
            'name' => 'charset',
            'default' => NULL,
            'type' => NULL,
            'isVariadic' => false,
            'byRef' => false,
            'isPromoted' => false,
            'attributes' => 
            array (
            ),
            'startLine' => 370,
            'endLine' => 370,
            'startColumn' => 29,
            'endColumn' => 36,
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
 * Specify the character set that should be used for the table.
 *
 * @param  string  $charset
 * @return void
 */',
        'startLine' => 370,
        'endLine' => 373,
        'startColumn' => 5,
        'endColumn' => 5,
        'couldThrow' => false,
        'isClosure' => false,
        'isGenerator' => false,
        'isVariadic' => false,
        'modifiers' => 1,
        'namespace' => 'Illuminate\\Database\\Schema',
        'declaringClassName' => 'Illuminate\\Database\\Schema\\Blueprint',
        'implementingClassName' => 'Illuminate\\Database\\Schema\\Blueprint',
        'currentClassName' => 'Illuminate\\Database\\Schema\\Blueprint',
        'aliasName' => NULL,
      ),
      'collation' => 
      array (
        'name' => 'collation',
        'parameters' => 
        array (
          'collation' => 
          array (
            'name' => 'collation',
            'default' => NULL,
            'type' => NULL,
            'isVariadic' => false,
            'byRef' => false,
            'isPromoted' => false,
            'attributes' => 
            array (
            ),
            'startLine' => 381,
            'endLine' => 381,
            'startColumn' => 31,
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
 * Specify the collation that should be used for the table.
 *
 * @param  string  $collation
 * @return void
 */',
        'startLine' => 381,
        'endLine' => 384,
        'startColumn' => 5,
        'endColumn' => 5,
        'couldThrow' => false,
        'isClosure' => false,
        'isGenerator' => false,
        'isVariadic' => false,
        'modifiers' => 1,
        'namespace' => 'Illuminate\\Database\\Schema',
        'declaringClassName' => 'Illuminate\\Database\\Schema\\Blueprint',
        'implementingClassName' => 'Illuminate\\Database\\Schema\\Blueprint',
        'currentClassName' => 'Illuminate\\Database\\Schema\\Blueprint',
        'aliasName' => NULL,
      ),
      'temporary' => 
      array (
        'name' => 'temporary',
        'parameters' => 
        array (
        ),
        'returnsReference' => false,
        'returnType' => NULL,
        'attributes' => 
        array (
        ),
        'docComment' => '/**
 * Indicate that the table needs to be temporary.
 *
 * @return void
 */',
        'startLine' => 391,
        'endLine' => 394,
        'startColumn' => 5,
        'endColumn' => 5,
        'couldThrow' => false,
        'isClosure' => false,
        'isGenerator' => false,
        'isVariadic' => false,
        'modifiers' => 1,
        'namespace' => 'Illuminate\\Database\\Schema',
        'declaringClassName' => 'Illuminate\\Database\\Schema\\Blueprint',
        'implementingClassName' => 'Illuminate\\Database\\Schema\\Blueprint',
        'currentClassName' => 'Illuminate\\Database\\Schema\\Blueprint',
        'aliasName' => NULL,
      ),
      'drop' => 
      array (
        'name' => 'drop',
        'parameters' => 
        array (
        ),
        'returnsReference' => false,
        'returnType' => NULL,
        'attributes' => 
        array (
        ),
        'docComment' => '/**
 * Indicate that the table should be dropped.
 *
 * @return \\Illuminate\\Support\\Fluent
 */',
        'startLine' => 401,
        'endLine' => 404,
        'startColumn' => 5,
        'endColumn' => 5,
        'couldThrow' => false,
        'isClosure' => false,
        'isGenerator' => false,
        'isVariadic' => false,
        'modifiers' => 1,
        'namespace' => 'Illuminate\\Database\\Schema',
        'declaringClassName' => 'Illuminate\\Database\\Schema\\Blueprint',
        'implementingClassName' => 'Illuminate\\Database\\Schema\\Blueprint',
        'currentClassName' => 'Illuminate\\Database\\Schema\\Blueprint',
        'aliasName' => NULL,
      ),
      'dropIfExists' => 
      array (
        'name' => 'dropIfExists',
        'parameters' => 
        array (
        ),
        'returnsReference' => false,
        'returnType' => NULL,
        'attributes' => 
        array (
        ),
        'docComment' => '/**
 * Indicate that the table should be dropped if it exists.
 *
 * @return \\Illuminate\\Support\\Fluent
 */',
        'startLine' => 411,
        'endLine' => 414,
        'startColumn' => 5,
        'endColumn' => 5,
        'couldThrow' => false,
        'isClosure' => false,
        'isGenerator' => false,
        'isVariadic' => false,
        'modifiers' => 1,
        'namespace' => 'Illuminate\\Database\\Schema',
        'declaringClassName' => 'Illuminate\\Database\\Schema\\Blueprint',
        'implementingClassName' => 'Illuminate\\Database\\Schema\\Blueprint',
        'currentClassName' => 'Illuminate\\Database\\Schema\\Blueprint',
        'aliasName' => NULL,
      ),
      'dropColumn' => 
      array (
        'name' => 'dropColumn',
        'parameters' => 
        array (
          'columns' => 
          array (
            'name' => 'columns',
            'default' => NULL,
            'type' => NULL,
            'isVariadic' => false,
            'byRef' => false,
            'isPromoted' => false,
            'attributes' => 
            array (
            ),
            'startLine' => 422,
            'endLine' => 422,
            'startColumn' => 32,
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
 * Indicate that the given columns should be dropped.
 *
 * @param  mixed  $columns
 * @return \\Illuminate\\Support\\Fluent
 */',
        'startLine' => 422,
        'endLine' => 427,
        'startColumn' => 5,
        'endColumn' => 5,
        'couldThrow' => false,
        'isClosure' => false,
        'isGenerator' => false,
        'isVariadic' => true,
        'modifiers' => 1,
        'namespace' => 'Illuminate\\Database\\Schema',
        'declaringClassName' => 'Illuminate\\Database\\Schema\\Blueprint',
        'implementingClassName' => 'Illuminate\\Database\\Schema\\Blueprint',
        'currentClassName' => 'Illuminate\\Database\\Schema\\Blueprint',
        'aliasName' => NULL,
      ),
      'renameColumn' => 
      array (
        'name' => 'renameColumn',
        'parameters' => 
        array (
          'from' => 
          array (
            'name' => 'from',
            'default' => NULL,
            'type' => NULL,
            'isVariadic' => false,
            'byRef' => false,
            'isPromoted' => false,
            'attributes' => 
            array (
            ),
            'startLine' => 436,
            'endLine' => 436,
            'startColumn' => 34,
            'endColumn' => 38,
            'parameterIndex' => 0,
            'isOptional' => false,
          ),
          'to' => 
          array (
            'name' => 'to',
            'default' => NULL,
            'type' => NULL,
            'isVariadic' => false,
            'byRef' => false,
            'isPromoted' => false,
            'attributes' => 
            array (
            ),
            'startLine' => 436,
            'endLine' => 436,
            'startColumn' => 41,
            'endColumn' => 43,
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
 * Indicate that the given columns should be renamed.
 *
 * @param  string  $from
 * @param  string  $to
 * @return \\Illuminate\\Support\\Fluent
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
        'namespace' => 'Illuminate\\Database\\Schema',
        'declaringClassName' => 'Illuminate\\Database\\Schema\\Blueprint',
        'implementingClassName' => 'Illuminate\\Database\\Schema\\Blueprint',
        'currentClassName' => 'Illuminate\\Database\\Schema\\Blueprint',
        'aliasName' => NULL,
      ),
      'dropPrimary' => 
      array (
        'name' => 'dropPrimary',
        'parameters' => 
        array (
          'index' => 
          array (
            'name' => 'index',
            'default' => 
            array (
              'code' => 'null',
              'attributes' => 
              array (
                'startLine' => 447,
                'endLine' => 447,
                'startTokenPos' => 1664,
                'startFilePos' => 11996,
                'endTokenPos' => 1664,
                'endFilePos' => 11999,
              ),
            ),
            'type' => NULL,
            'isVariadic' => false,
            'byRef' => false,
            'isPromoted' => false,
            'attributes' => 
            array (
            ),
            'startLine' => 447,
            'endLine' => 447,
            'startColumn' => 33,
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
 * Indicate that the given primary key should be dropped.
 *
 * @param  string|array|null  $index
 * @return \\Illuminate\\Support\\Fluent
 */',
        'startLine' => 447,
        'endLine' => 450,
        'startColumn' => 5,
        'endColumn' => 5,
        'couldThrow' => false,
        'isClosure' => false,
        'isGenerator' => false,
        'isVariadic' => false,
        'modifiers' => 1,
        'namespace' => 'Illuminate\\Database\\Schema',
        'declaringClassName' => 'Illuminate\\Database\\Schema\\Blueprint',
        'implementingClassName' => 'Illuminate\\Database\\Schema\\Blueprint',
        'currentClassName' => 'Illuminate\\Database\\Schema\\Blueprint',
        'aliasName' => NULL,
      ),
      'dropUnique' => 
      array (
        'name' => 'dropUnique',
        'parameters' => 
        array (
          'index' => 
          array (
            'name' => 'index',
            'default' => NULL,
            'type' => NULL,
            'isVariadic' => false,
            'byRef' => false,
            'isPromoted' => false,
            'attributes' => 
            array (
            ),
            'startLine' => 458,
            'endLine' => 458,
            'startColumn' => 32,
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
 * Indicate that the given unique key should be dropped.
 *
 * @param  string|array  $index
 * @return \\Illuminate\\Support\\Fluent
 */',
        'startLine' => 458,
        'endLine' => 461,
        'startColumn' => 5,
        'endColumn' => 5,
        'couldThrow' => false,
        'isClosure' => false,
        'isGenerator' => false,
        'isVariadic' => false,
        'modifiers' => 1,
        'namespace' => 'Illuminate\\Database\\Schema',
        'declaringClassName' => 'Illuminate\\Database\\Schema\\Blueprint',
        'implementingClassName' => 'Illuminate\\Database\\Schema\\Blueprint',
        'currentClassName' => 'Illuminate\\Database\\Schema\\Blueprint',
        'aliasName' => NULL,
      ),
      'dropIndex' => 
      array (
        'name' => 'dropIndex',
        'parameters' => 
        array (
          'index' => 
          array (
            'name' => 'index',
            'default' => NULL,
            'type' => NULL,
            'isVariadic' => false,
            'byRef' => false,
            'isPromoted' => false,
            'attributes' => 
            array (
            ),
            'startLine' => 469,
            'endLine' => 469,
            'startColumn' => 31,
            'endColumn' => 36,
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
 * Indicate that the given index should be dropped.
 *
 * @param  string|array  $index
 * @return \\Illuminate\\Support\\Fluent
 */',
        'startLine' => 469,
        'endLine' => 472,
        'startColumn' => 5,
        'endColumn' => 5,
        'couldThrow' => false,
        'isClosure' => false,
        'isGenerator' => false,
        'isVariadic' => false,
        'modifiers' => 1,
        'namespace' => 'Illuminate\\Database\\Schema',
        'declaringClassName' => 'Illuminate\\Database\\Schema\\Blueprint',
        'implementingClassName' => 'Illuminate\\Database\\Schema\\Blueprint',
        'currentClassName' => 'Illuminate\\Database\\Schema\\Blueprint',
        'aliasName' => NULL,
      ),
      'dropFullText' => 
      array (
        'name' => 'dropFullText',
        'parameters' => 
        array (
          'index' => 
          array (
            'name' => 'index',
            'default' => NULL,
            'type' => NULL,
            'isVariadic' => false,
            'byRef' => false,
            'isPromoted' => false,
            'attributes' => 
            array (
            ),
            'startLine' => 480,
            'endLine' => 480,
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
 * Indicate that the given fulltext index should be dropped.
 *
 * @param  string|array  $index
 * @return \\Illuminate\\Support\\Fluent
 */',
        'startLine' => 480,
        'endLine' => 483,
        'startColumn' => 5,
        'endColumn' => 5,
        'couldThrow' => false,
        'isClosure' => false,
        'isGenerator' => false,
        'isVariadic' => false,
        'modifiers' => 1,
        'namespace' => 'Illuminate\\Database\\Schema',
        'declaringClassName' => 'Illuminate\\Database\\Schema\\Blueprint',
        'implementingClassName' => 'Illuminate\\Database\\Schema\\Blueprint',
        'currentClassName' => 'Illuminate\\Database\\Schema\\Blueprint',
        'aliasName' => NULL,
      ),
      'dropSpatialIndex' => 
      array (
        'name' => 'dropSpatialIndex',
        'parameters' => 
        array (
          'index' => 
          array (
            'name' => 'index',
            'default' => NULL,
            'type' => NULL,
            'isVariadic' => false,
            'byRef' => false,
            'isPromoted' => false,
            'attributes' => 
            array (
            ),
            'startLine' => 491,
            'endLine' => 491,
            'startColumn' => 38,
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
 * Indicate that the given spatial index should be dropped.
 *
 * @param  string|array  $index
 * @return \\Illuminate\\Support\\Fluent
 */',
        'startLine' => 491,
        'endLine' => 494,
        'startColumn' => 5,
        'endColumn' => 5,
        'couldThrow' => false,
        'isClosure' => false,
        'isGenerator' => false,
        'isVariadic' => false,
        'modifiers' => 1,
        'namespace' => 'Illuminate\\Database\\Schema',
        'declaringClassName' => 'Illuminate\\Database\\Schema\\Blueprint',
        'implementingClassName' => 'Illuminate\\Database\\Schema\\Blueprint',
        'currentClassName' => 'Illuminate\\Database\\Schema\\Blueprint',
        'aliasName' => NULL,
      ),
      'dropForeign' => 
      array (
        'name' => 'dropForeign',
        'parameters' => 
        array (
          'index' => 
          array (
            'name' => 'index',
            'default' => NULL,
            'type' => NULL,
            'isVariadic' => false,
            'byRef' => false,
            'isPromoted' => false,
            'attributes' => 
            array (
            ),
            'startLine' => 502,
            'endLine' => 502,
            'startColumn' => 33,
            'endColumn' => 38,
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
 * Indicate that the given foreign key should be dropped.
 *
 * @param  string|array  $index
 * @return \\Illuminate\\Support\\Fluent
 */',
        'startLine' => 502,
        'endLine' => 505,
        'startColumn' => 5,
        'endColumn' => 5,
        'couldThrow' => false,
        'isClosure' => false,
        'isGenerator' => false,
        'isVariadic' => false,
        'modifiers' => 1,
        'namespace' => 'Illuminate\\Database\\Schema',
        'declaringClassName' => 'Illuminate\\Database\\Schema\\Blueprint',
        'implementingClassName' => 'Illuminate\\Database\\Schema\\Blueprint',
        'currentClassName' => 'Illuminate\\Database\\Schema\\Blueprint',
        'aliasName' => NULL,
      ),
      'dropConstrainedForeignId' => 
      array (
        'name' => 'dropConstrainedForeignId',
        'parameters' => 
        array (
          'column' => 
          array (
            'name' => 'column',
            'default' => NULL,
            'type' => NULL,
            'isVariadic' => false,
            'byRef' => false,
            'isPromoted' => false,
            'attributes' => 
            array (
            ),
            'startLine' => 513,
            'endLine' => 513,
            'startColumn' => 46,
            'endColumn' => 52,
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
 * Indicate that the given column and foreign key should be dropped.
 *
 * @param  string  $column
 * @return \\Illuminate\\Support\\Fluent
 */',
        'startLine' => 513,
        'endLine' => 518,
        'startColumn' => 5,
        'endColumn' => 5,
        'couldThrow' => false,
        'isClosure' => false,
        'isGenerator' => false,
        'isVariadic' => false,
        'modifiers' => 1,
        'namespace' => 'Illuminate\\Database\\Schema',
        'declaringClassName' => 'Illuminate\\Database\\Schema\\Blueprint',
        'implementingClassName' => 'Illuminate\\Database\\Schema\\Blueprint',
        'currentClassName' => 'Illuminate\\Database\\Schema\\Blueprint',
        'aliasName' => NULL,
      ),
      'dropForeignIdFor' => 
      array (
        'name' => 'dropForeignIdFor',
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
            'startLine' => 527,
            'endLine' => 527,
            'startColumn' => 38,
            'endColumn' => 43,
            'parameterIndex' => 0,
            'isOptional' => false,
          ),
          'column' => 
          array (
            'name' => 'column',
            'default' => 
            array (
              'code' => 'null',
              'attributes' => 
              array (
                'startLine' => 527,
                'endLine' => 527,
                'startTokenPos' => 1892,
                'startFilePos' => 14149,
                'endTokenPos' => 1892,
                'endFilePos' => 14152,
              ),
            ),
            'type' => NULL,
            'isVariadic' => false,
            'byRef' => false,
            'isPromoted' => false,
            'attributes' => 
            array (
            ),
            'startLine' => 527,
            'endLine' => 527,
            'startColumn' => 46,
            'endColumn' => 59,
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
 * Indicate that the given foreign key should be dropped.
 *
 * @param  \\Illuminate\\Database\\Eloquent\\Model|string  $model
 * @param  string|null  $column
 * @return \\Illuminate\\Support\\Fluent
 */',
        'startLine' => 527,
        'endLine' => 534,
        'startColumn' => 5,
        'endColumn' => 5,
        'couldThrow' => false,
        'isClosure' => false,
        'isGenerator' => false,
        'isVariadic' => false,
        'modifiers' => 1,
        'namespace' => 'Illuminate\\Database\\Schema',
        'declaringClassName' => 'Illuminate\\Database\\Schema\\Blueprint',
        'implementingClassName' => 'Illuminate\\Database\\Schema\\Blueprint',
        'currentClassName' => 'Illuminate\\Database\\Schema\\Blueprint',
        'aliasName' => NULL,
      ),
      'dropConstrainedForeignIdFor' => 
      array (
        'name' => 'dropConstrainedForeignIdFor',
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
            'startLine' => 543,
            'endLine' => 543,
            'startColumn' => 49,
            'endColumn' => 54,
            'parameterIndex' => 0,
            'isOptional' => false,
          ),
          'column' => 
          array (
            'name' => 'column',
            'default' => 
            array (
              'code' => 'null',
              'attributes' => 
              array (
                'startLine' => 543,
                'endLine' => 543,
                'startTokenPos' => 1955,
                'startFilePos' => 14610,
                'endTokenPos' => 1955,
                'endFilePos' => 14613,
              ),
            ),
            'type' => NULL,
            'isVariadic' => false,
            'byRef' => false,
            'isPromoted' => false,
            'attributes' => 
            array (
            ),
            'startLine' => 543,
            'endLine' => 543,
            'startColumn' => 57,
            'endColumn' => 70,
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
 * Indicate that the given foreign key should be dropped.
 *
 * @param  \\Illuminate\\Database\\Eloquent\\Model|string  $model
 * @param  string|null  $column
 * @return \\Illuminate\\Support\\Fluent
 */',
        'startLine' => 543,
        'endLine' => 550,
        'startColumn' => 5,
        'endColumn' => 5,
        'couldThrow' => false,
        'isClosure' => false,
        'isGenerator' => false,
        'isVariadic' => false,
        'modifiers' => 1,
        'namespace' => 'Illuminate\\Database\\Schema',
        'declaringClassName' => 'Illuminate\\Database\\Schema\\Blueprint',
        'implementingClassName' => 'Illuminate\\Database\\Schema\\Blueprint',
        'currentClassName' => 'Illuminate\\Database\\Schema\\Blueprint',
        'aliasName' => NULL,
      ),
      'renameIndex' => 
      array (
        'name' => 'renameIndex',
        'parameters' => 
        array (
          'from' => 
          array (
            'name' => 'from',
            'default' => NULL,
            'type' => NULL,
            'isVariadic' => false,
            'byRef' => false,
            'isPromoted' => false,
            'attributes' => 
            array (
            ),
            'startLine' => 559,
            'endLine' => 559,
            'startColumn' => 33,
            'endColumn' => 37,
            'parameterIndex' => 0,
            'isOptional' => false,
          ),
          'to' => 
          array (
            'name' => 'to',
            'default' => NULL,
            'type' => NULL,
            'isVariadic' => false,
            'byRef' => false,
            'isPromoted' => false,
            'attributes' => 
            array (
            ),
            'startLine' => 559,
            'endLine' => 559,
            'startColumn' => 40,
            'endColumn' => 42,
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
 * Indicate that the given indexes should be renamed.
 *
 * @param  string  $from
 * @param  string  $to
 * @return \\Illuminate\\Support\\Fluent
 */',
        'startLine' => 559,
        'endLine' => 562,
        'startColumn' => 5,
        'endColumn' => 5,
        'couldThrow' => false,
        'isClosure' => false,
        'isGenerator' => false,
        'isVariadic' => false,
        'modifiers' => 1,
        'namespace' => 'Illuminate\\Database\\Schema',
        'declaringClassName' => 'Illuminate\\Database\\Schema\\Blueprint',
        'implementingClassName' => 'Illuminate\\Database\\Schema\\Blueprint',
        'currentClassName' => 'Illuminate\\Database\\Schema\\Blueprint',
        'aliasName' => NULL,
      ),
      'dropTimestamps' => 
      array (
        'name' => 'dropTimestamps',
        'parameters' => 
        array (
        ),
        'returnsReference' => false,
        'returnType' => NULL,
        'attributes' => 
        array (
        ),
        'docComment' => '/**
 * Indicate that the timestamp columns should be dropped.
 *
 * @return void
 */',
        'startLine' => 569,
        'endLine' => 572,
        'startColumn' => 5,
        'endColumn' => 5,
        'couldThrow' => false,
        'isClosure' => false,
        'isGenerator' => false,
        'isVariadic' => false,
        'modifiers' => 1,
        'namespace' => 'Illuminate\\Database\\Schema',
        'declaringClassName' => 'Illuminate\\Database\\Schema\\Blueprint',
        'implementingClassName' => 'Illuminate\\Database\\Schema\\Blueprint',
        'currentClassName' => 'Illuminate\\Database\\Schema\\Blueprint',
        'aliasName' => NULL,
      ),
      'dropTimestampsTz' => 
      array (
        'name' => 'dropTimestampsTz',
        'parameters' => 
        array (
        ),
        'returnsReference' => false,
        'returnType' => NULL,
        'attributes' => 
        array (
        ),
        'docComment' => '/**
 * Indicate that the timestamp columns should be dropped.
 *
 * @return void
 */',
        'startLine' => 579,
        'endLine' => 582,
        'startColumn' => 5,
        'endColumn' => 5,
        'couldThrow' => false,
        'isClosure' => false,
        'isGenerator' => false,
        'isVariadic' => false,
        'modifiers' => 1,
        'namespace' => 'Illuminate\\Database\\Schema',
        'declaringClassName' => 'Illuminate\\Database\\Schema\\Blueprint',
        'implementingClassName' => 'Illuminate\\Database\\Schema\\Blueprint',
        'currentClassName' => 'Illuminate\\Database\\Schema\\Blueprint',
        'aliasName' => NULL,
      ),
      'dropSoftDeletes' => 
      array (
        'name' => 'dropSoftDeletes',
        'parameters' => 
        array (
          'column' => 
          array (
            'name' => 'column',
            'default' => 
            array (
              'code' => '\'deleted_at\'',
              'attributes' => 
              array (
                'startLine' => 590,
                'endLine' => 590,
                'startTokenPos' => 2105,
                'startFilePos' => 15690,
                'endTokenPos' => 2105,
                'endFilePos' => 15701,
              ),
            ),
            'type' => NULL,
            'isVariadic' => false,
            'byRef' => false,
            'isPromoted' => false,
            'attributes' => 
            array (
            ),
            'startLine' => 590,
            'endLine' => 590,
            'startColumn' => 37,
            'endColumn' => 58,
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
 * Indicate that the soft delete column should be dropped.
 *
 * @param  string  $column
 * @return void
 */',
        'startLine' => 590,
        'endLine' => 593,
        'startColumn' => 5,
        'endColumn' => 5,
        'couldThrow' => false,
        'isClosure' => false,
        'isGenerator' => false,
        'isVariadic' => false,
        'modifiers' => 1,
        'namespace' => 'Illuminate\\Database\\Schema',
        'declaringClassName' => 'Illuminate\\Database\\Schema\\Blueprint',
        'implementingClassName' => 'Illuminate\\Database\\Schema\\Blueprint',
        'currentClassName' => 'Illuminate\\Database\\Schema\\Blueprint',
        'aliasName' => NULL,
      ),
      'dropSoftDeletesTz' => 
      array (
        'name' => 'dropSoftDeletesTz',
        'parameters' => 
        array (
          'column' => 
          array (
            'name' => 'column',
            'default' => 
            array (
              'code' => '\'deleted_at\'',
              'attributes' => 
              array (
                'startLine' => 601,
                'endLine' => 601,
                'startTokenPos' => 2132,
                'startFilePos' => 15938,
                'endTokenPos' => 2132,
                'endFilePos' => 15949,
              ),
            ),
            'type' => NULL,
            'isVariadic' => false,
            'byRef' => false,
            'isPromoted' => false,
            'attributes' => 
            array (
            ),
            'startLine' => 601,
            'endLine' => 601,
            'startColumn' => 39,
            'endColumn' => 60,
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
 * Indicate that the soft delete column should be dropped.
 *
 * @param  string  $column
 * @return void
 */',
        'startLine' => 601,
        'endLine' => 604,
        'startColumn' => 5,
        'endColumn' => 5,
        'couldThrow' => false,
        'isClosure' => false,
        'isGenerator' => false,
        'isVariadic' => false,
        'modifiers' => 1,
        'namespace' => 'Illuminate\\Database\\Schema',
        'declaringClassName' => 'Illuminate\\Database\\Schema\\Blueprint',
        'implementingClassName' => 'Illuminate\\Database\\Schema\\Blueprint',
        'currentClassName' => 'Illuminate\\Database\\Schema\\Blueprint',
        'aliasName' => NULL,
      ),
      'dropRememberToken' => 
      array (
        'name' => 'dropRememberToken',
        'parameters' => 
        array (
        ),
        'returnsReference' => false,
        'returnType' => NULL,
        'attributes' => 
        array (
        ),
        'docComment' => '/**
 * Indicate that the remember token column should be dropped.
 *
 * @return void
 */',
        'startLine' => 611,
        'endLine' => 614,
        'startColumn' => 5,
        'endColumn' => 5,
        'couldThrow' => false,
        'isClosure' => false,
        'isGenerator' => false,
        'isVariadic' => false,
        'modifiers' => 1,
        'namespace' => 'Illuminate\\Database\\Schema',
        'declaringClassName' => 'Illuminate\\Database\\Schema\\Blueprint',
        'implementingClassName' => 'Illuminate\\Database\\Schema\\Blueprint',
        'currentClassName' => 'Illuminate\\Database\\Schema\\Blueprint',
        'aliasName' => NULL,
      ),
      'dropMorphs' => 
      array (
        'name' => 'dropMorphs',
        'parameters' => 
        array (
          'name' => 
          array (
            'name' => 'name',
            'default' => NULL,
            'type' => NULL,
            'isVariadic' => false,
            'byRef' => false,
            'isPromoted' => false,
            'attributes' => 
            array (
            ),
            'startLine' => 623,
            'endLine' => 623,
            'startColumn' => 32,
            'endColumn' => 36,
            'parameterIndex' => 0,
            'isOptional' => false,
          ),
          'indexName' => 
          array (
            'name' => 'indexName',
            'default' => 
            array (
              'code' => 'null',
              'attributes' => 
              array (
                'startLine' => 623,
                'endLine' => 623,
                'startTokenPos' => 2184,
                'startFilePos' => 16439,
                'endTokenPos' => 2184,
                'endFilePos' => 16442,
              ),
            ),
            'type' => NULL,
            'isVariadic' => false,
            'byRef' => false,
            'isPromoted' => false,
            'attributes' => 
            array (
            ),
            'startLine' => 623,
            'endLine' => 623,
            'startColumn' => 39,
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
 * Indicate that the polymorphic columns should be dropped.
 *
 * @param  string  $name
 * @param  string|null  $indexName
 * @return void
 */',
        'startLine' => 623,
        'endLine' => 628,
        'startColumn' => 5,
        'endColumn' => 5,
        'couldThrow' => false,
        'isClosure' => false,
        'isGenerator' => false,
        'isVariadic' => false,
        'modifiers' => 1,
        'namespace' => 'Illuminate\\Database\\Schema',
        'declaringClassName' => 'Illuminate\\Database\\Schema\\Blueprint',
        'implementingClassName' => 'Illuminate\\Database\\Schema\\Blueprint',
        'currentClassName' => 'Illuminate\\Database\\Schema\\Blueprint',
        'aliasName' => NULL,
      ),
      'rename' => 
      array (
        'name' => 'rename',
        'parameters' => 
        array (
          'to' => 
          array (
            'name' => 'to',
            'default' => NULL,
            'type' => NULL,
            'isVariadic' => false,
            'byRef' => false,
            'isPromoted' => false,
            'attributes' => 
            array (
            ),
            'startLine' => 636,
            'endLine' => 636,
            'startColumn' => 28,
            'endColumn' => 30,
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
 * Rename the table to a given name.
 *
 * @param  string  $to
 * @return \\Illuminate\\Support\\Fluent
 */',
        'startLine' => 636,
        'endLine' => 639,
        'startColumn' => 5,
        'endColumn' => 5,
        'couldThrow' => false,
        'isClosure' => false,
        'isGenerator' => false,
        'isVariadic' => false,
        'modifiers' => 1,
        'namespace' => 'Illuminate\\Database\\Schema',
        'declaringClassName' => 'Illuminate\\Database\\Schema\\Blueprint',
        'implementingClassName' => 'Illuminate\\Database\\Schema\\Blueprint',
        'currentClassName' => 'Illuminate\\Database\\Schema\\Blueprint',
        'aliasName' => NULL,
      ),
      'primary' => 
      array (
        'name' => 'primary',
        'parameters' => 
        array (
          'columns' => 
          array (
            'name' => 'columns',
            'default' => NULL,
            'type' => NULL,
            'isVariadic' => false,
            'byRef' => false,
            'isPromoted' => false,
            'attributes' => 
            array (
            ),
            'startLine' => 649,
            'endLine' => 649,
            'startColumn' => 29,
            'endColumn' => 36,
            'parameterIndex' => 0,
            'isOptional' => false,
          ),
          'name' => 
          array (
            'name' => 'name',
            'default' => 
            array (
              'code' => 'null',
              'attributes' => 
              array (
                'startLine' => 649,
                'endLine' => 649,
                'startTokenPos' => 2297,
                'startFilePos' => 17146,
                'endTokenPos' => 2297,
                'endFilePos' => 17149,
              ),
            ),
            'type' => NULL,
            'isVariadic' => false,
            'byRef' => false,
            'isPromoted' => false,
            'attributes' => 
            array (
            ),
            'startLine' => 649,
            'endLine' => 649,
            'startColumn' => 39,
            'endColumn' => 50,
            'parameterIndex' => 1,
            'isOptional' => true,
          ),
          'algorithm' => 
          array (
            'name' => 'algorithm',
            'default' => 
            array (
              'code' => 'null',
              'attributes' => 
              array (
                'startLine' => 649,
                'endLine' => 649,
                'startTokenPos' => 2304,
                'startFilePos' => 17165,
                'endTokenPos' => 2304,
                'endFilePos' => 17168,
              ),
            ),
            'type' => NULL,
            'isVariadic' => false,
            'byRef' => false,
            'isPromoted' => false,
            'attributes' => 
            array (
            ),
            'startLine' => 649,
            'endLine' => 649,
            'startColumn' => 53,
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
 * Specify the primary key(s) for the table.
 *
 * @param  string|array  $columns
 * @param  string|null  $name
 * @param  string|null  $algorithm
 * @return \\Illuminate\\Database\\Schema\\IndexDefinition
 */',
        'startLine' => 649,
        'endLine' => 652,
        'startColumn' => 5,
        'endColumn' => 5,
        'couldThrow' => false,
        'isClosure' => false,
        'isGenerator' => false,
        'isVariadic' => false,
        'modifiers' => 1,
        'namespace' => 'Illuminate\\Database\\Schema',
        'declaringClassName' => 'Illuminate\\Database\\Schema\\Blueprint',
        'implementingClassName' => 'Illuminate\\Database\\Schema\\Blueprint',
        'currentClassName' => 'Illuminate\\Database\\Schema\\Blueprint',
        'aliasName' => NULL,
      ),
      'unique' => 
      array (
        'name' => 'unique',
        'parameters' => 
        array (
          'columns' => 
          array (
            'name' => 'columns',
            'default' => NULL,
            'type' => NULL,
            'isVariadic' => false,
            'byRef' => false,
            'isPromoted' => false,
            'attributes' => 
            array (
            ),
            'startLine' => 662,
            'endLine' => 662,
            'startColumn' => 28,
            'endColumn' => 35,
            'parameterIndex' => 0,
            'isOptional' => false,
          ),
          'name' => 
          array (
            'name' => 'name',
            'default' => 
            array (
              'code' => 'null',
              'attributes' => 
              array (
                'startLine' => 662,
                'endLine' => 662,
                'startTokenPos' => 2345,
                'startFilePos' => 17543,
                'endTokenPos' => 2345,
                'endFilePos' => 17546,
              ),
            ),
            'type' => NULL,
            'isVariadic' => false,
            'byRef' => false,
            'isPromoted' => false,
            'attributes' => 
            array (
            ),
            'startLine' => 662,
            'endLine' => 662,
            'startColumn' => 38,
            'endColumn' => 49,
            'parameterIndex' => 1,
            'isOptional' => true,
          ),
          'algorithm' => 
          array (
            'name' => 'algorithm',
            'default' => 
            array (
              'code' => 'null',
              'attributes' => 
              array (
                'startLine' => 662,
                'endLine' => 662,
                'startTokenPos' => 2352,
                'startFilePos' => 17562,
                'endTokenPos' => 2352,
                'endFilePos' => 17565,
              ),
            ),
            'type' => NULL,
            'isVariadic' => false,
            'byRef' => false,
            'isPromoted' => false,
            'attributes' => 
            array (
            ),
            'startLine' => 662,
            'endLine' => 662,
            'startColumn' => 52,
            'endColumn' => 68,
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
 * Specify a unique index for the table.
 *
 * @param  string|array  $columns
 * @param  string|null  $name
 * @param  string|null  $algorithm
 * @return \\Illuminate\\Database\\Schema\\IndexDefinition
 */',
        'startLine' => 662,
        'endLine' => 665,
        'startColumn' => 5,
        'endColumn' => 5,
        'couldThrow' => false,
        'isClosure' => false,
        'isGenerator' => false,
        'isVariadic' => false,
        'modifiers' => 1,
        'namespace' => 'Illuminate\\Database\\Schema',
        'declaringClassName' => 'Illuminate\\Database\\Schema\\Blueprint',
        'implementingClassName' => 'Illuminate\\Database\\Schema\\Blueprint',
        'currentClassName' => 'Illuminate\\Database\\Schema\\Blueprint',
        'aliasName' => NULL,
      ),
      'index' => 
      array (
        'name' => 'index',
        'parameters' => 
        array (
          'columns' => 
          array (
            'name' => 'columns',
            'default' => NULL,
            'type' => NULL,
            'isVariadic' => false,
            'byRef' => false,
            'isPromoted' => false,
            'attributes' => 
            array (
            ),
            'startLine' => 675,
            'endLine' => 675,
            'startColumn' => 27,
            'endColumn' => 34,
            'parameterIndex' => 0,
            'isOptional' => false,
          ),
          'name' => 
          array (
            'name' => 'name',
            'default' => 
            array (
              'code' => 'null',
              'attributes' => 
              array (
                'startLine' => 675,
                'endLine' => 675,
                'startTokenPos' => 2393,
                'startFilePos' => 17932,
                'endTokenPos' => 2393,
                'endFilePos' => 17935,
              ),
            ),
            'type' => NULL,
            'isVariadic' => false,
            'byRef' => false,
            'isPromoted' => false,
            'attributes' => 
            array (
            ),
            'startLine' => 675,
            'endLine' => 675,
            'startColumn' => 37,
            'endColumn' => 48,
            'parameterIndex' => 1,
            'isOptional' => true,
          ),
          'algorithm' => 
          array (
            'name' => 'algorithm',
            'default' => 
            array (
              'code' => 'null',
              'attributes' => 
              array (
                'startLine' => 675,
                'endLine' => 675,
                'startTokenPos' => 2400,
                'startFilePos' => 17951,
                'endTokenPos' => 2400,
                'endFilePos' => 17954,
              ),
            ),
            'type' => NULL,
            'isVariadic' => false,
            'byRef' => false,
            'isPromoted' => false,
            'attributes' => 
            array (
            ),
            'startLine' => 675,
            'endLine' => 675,
            'startColumn' => 51,
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
 * Specify an index for the table.
 *
 * @param  string|array  $columns
 * @param  string|null  $name
 * @param  string|null  $algorithm
 * @return \\Illuminate\\Database\\Schema\\IndexDefinition
 */',
        'startLine' => 675,
        'endLine' => 678,
        'startColumn' => 5,
        'endColumn' => 5,
        'couldThrow' => false,
        'isClosure' => false,
        'isGenerator' => false,
        'isVariadic' => false,
        'modifiers' => 1,
        'namespace' => 'Illuminate\\Database\\Schema',
        'declaringClassName' => 'Illuminate\\Database\\Schema\\Blueprint',
        'implementingClassName' => 'Illuminate\\Database\\Schema\\Blueprint',
        'currentClassName' => 'Illuminate\\Database\\Schema\\Blueprint',
        'aliasName' => NULL,
      ),
      'fullText' => 
      array (
        'name' => 'fullText',
        'parameters' => 
        array (
          'columns' => 
          array (
            'name' => 'columns',
            'default' => NULL,
            'type' => NULL,
            'isVariadic' => false,
            'byRef' => false,
            'isPromoted' => false,
            'attributes' => 
            array (
            ),
            'startLine' => 688,
            'endLine' => 688,
            'startColumn' => 30,
            'endColumn' => 37,
            'parameterIndex' => 0,
            'isOptional' => false,
          ),
          'name' => 
          array (
            'name' => 'name',
            'default' => 
            array (
              'code' => 'null',
              'attributes' => 
              array (
                'startLine' => 688,
                'endLine' => 688,
                'startTokenPos' => 2441,
                'startFilePos' => 18331,
                'endTokenPos' => 2441,
                'endFilePos' => 18334,
              ),
            ),
            'type' => NULL,
            'isVariadic' => false,
            'byRef' => false,
            'isPromoted' => false,
            'attributes' => 
            array (
            ),
            'startLine' => 688,
            'endLine' => 688,
            'startColumn' => 40,
            'endColumn' => 51,
            'parameterIndex' => 1,
            'isOptional' => true,
          ),
          'algorithm' => 
          array (
            'name' => 'algorithm',
            'default' => 
            array (
              'code' => 'null',
              'attributes' => 
              array (
                'startLine' => 688,
                'endLine' => 688,
                'startTokenPos' => 2448,
                'startFilePos' => 18350,
                'endTokenPos' => 2448,
                'endFilePos' => 18353,
              ),
            ),
            'type' => NULL,
            'isVariadic' => false,
            'byRef' => false,
            'isPromoted' => false,
            'attributes' => 
            array (
            ),
            'startLine' => 688,
            'endLine' => 688,
            'startColumn' => 54,
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
 * Specify a fulltext index for the table.
 *
 * @param  string|array  $columns
 * @param  string|null  $name
 * @param  string|null  $algorithm
 * @return \\Illuminate\\Database\\Schema\\IndexDefinition
 */',
        'startLine' => 688,
        'endLine' => 691,
        'startColumn' => 5,
        'endColumn' => 5,
        'couldThrow' => false,
        'isClosure' => false,
        'isGenerator' => false,
        'isVariadic' => false,
        'modifiers' => 1,
        'namespace' => 'Illuminate\\Database\\Schema',
        'declaringClassName' => 'Illuminate\\Database\\Schema\\Blueprint',
        'implementingClassName' => 'Illuminate\\Database\\Schema\\Blueprint',
        'currentClassName' => 'Illuminate\\Database\\Schema\\Blueprint',
        'aliasName' => NULL,
      ),
      'spatialIndex' => 
      array (
        'name' => 'spatialIndex',
        'parameters' => 
        array (
          'columns' => 
          array (
            'name' => 'columns',
            'default' => NULL,
            'type' => NULL,
            'isVariadic' => false,
            'byRef' => false,
            'isPromoted' => false,
            'attributes' => 
            array (
            ),
            'startLine' => 701,
            'endLine' => 701,
            'startColumn' => 34,
            'endColumn' => 41,
            'parameterIndex' => 0,
            'isOptional' => false,
          ),
          'name' => 
          array (
            'name' => 'name',
            'default' => 
            array (
              'code' => 'null',
              'attributes' => 
              array (
                'startLine' => 701,
                'endLine' => 701,
                'startTokenPos' => 2489,
                'startFilePos' => 18740,
                'endTokenPos' => 2489,
                'endFilePos' => 18743,
              ),
            ),
            'type' => NULL,
            'isVariadic' => false,
            'byRef' => false,
            'isPromoted' => false,
            'attributes' => 
            array (
            ),
            'startLine' => 701,
            'endLine' => 701,
            'startColumn' => 44,
            'endColumn' => 55,
            'parameterIndex' => 1,
            'isOptional' => true,
          ),
          'operatorClass' => 
          array (
            'name' => 'operatorClass',
            'default' => 
            array (
              'code' => 'null',
              'attributes' => 
              array (
                'startLine' => 701,
                'endLine' => 701,
                'startTokenPos' => 2496,
                'startFilePos' => 18763,
                'endTokenPos' => 2496,
                'endFilePos' => 18766,
              ),
            ),
            'type' => NULL,
            'isVariadic' => false,
            'byRef' => false,
            'isPromoted' => false,
            'attributes' => 
            array (
            ),
            'startLine' => 701,
            'endLine' => 701,
            'startColumn' => 58,
            'endColumn' => 78,
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
 * Specify a spatial index for the table.
 *
 * @param  string|array  $columns
 * @param  string|null  $name
 * @param  string|null  $operatorClass
 * @return \\Illuminate\\Database\\Schema\\IndexDefinition
 */',
        'startLine' => 701,
        'endLine' => 704,
        'startColumn' => 5,
        'endColumn' => 5,
        'couldThrow' => false,
        'isClosure' => false,
        'isGenerator' => false,
        'isVariadic' => false,
        'modifiers' => 1,
        'namespace' => 'Illuminate\\Database\\Schema',
        'declaringClassName' => 'Illuminate\\Database\\Schema\\Blueprint',
        'implementingClassName' => 'Illuminate\\Database\\Schema\\Blueprint',
        'currentClassName' => 'Illuminate\\Database\\Schema\\Blueprint',
        'aliasName' => NULL,
      ),
      'vectorIndex' => 
      array (
        'name' => 'vectorIndex',
        'parameters' => 
        array (
          'column' => 
          array (
            'name' => 'column',
            'default' => NULL,
            'type' => NULL,
            'isVariadic' => false,
            'byRef' => false,
            'isPromoted' => false,
            'attributes' => 
            array (
            ),
            'startLine' => 713,
            'endLine' => 713,
            'startColumn' => 33,
            'endColumn' => 39,
            'parameterIndex' => 0,
            'isOptional' => false,
          ),
          'name' => 
          array (
            'name' => 'name',
            'default' => 
            array (
              'code' => 'null',
              'attributes' => 
              array (
                'startLine' => 713,
                'endLine' => 713,
                'startTokenPos' => 2540,
                'startFilePos' => 19114,
                'endTokenPos' => 2540,
                'endFilePos' => 19117,
              ),
            ),
            'type' => NULL,
            'isVariadic' => false,
            'byRef' => false,
            'isPromoted' => false,
            'attributes' => 
            array (
            ),
            'startLine' => 713,
            'endLine' => 713,
            'startColumn' => 42,
            'endColumn' => 53,
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
 * Specify a vector index for the table.
 *
 * @param  string  $column
 * @param  string|null  $name
 * @return \\Illuminate\\Database\\Schema\\IndexDefinition
 */',
        'startLine' => 713,
        'endLine' => 720,
        'startColumn' => 5,
        'endColumn' => 5,
        'couldThrow' => false,
        'isClosure' => false,
        'isGenerator' => false,
        'isVariadic' => false,
        'modifiers' => 1,
        'namespace' => 'Illuminate\\Database\\Schema',
        'declaringClassName' => 'Illuminate\\Database\\Schema\\Blueprint',
        'implementingClassName' => 'Illuminate\\Database\\Schema\\Blueprint',
        'currentClassName' => 'Illuminate\\Database\\Schema\\Blueprint',
        'aliasName' => NULL,
      ),
      'rawIndex' => 
      array (
        'name' => 'rawIndex',
        'parameters' => 
        array (
          'expression' => 
          array (
            'name' => 'expression',
            'default' => NULL,
            'type' => NULL,
            'isVariadic' => false,
            'byRef' => false,
            'isPromoted' => false,
            'attributes' => 
            array (
            ),
            'startLine' => 729,
            'endLine' => 729,
            'startColumn' => 30,
            'endColumn' => 40,
            'parameterIndex' => 0,
            'isOptional' => false,
          ),
          'name' => 
          array (
            'name' => 'name',
            'default' => NULL,
            'type' => NULL,
            'isVariadic' => false,
            'byRef' => false,
            'isPromoted' => false,
            'attributes' => 
            array (
            ),
            'startLine' => 729,
            'endLine' => 729,
            'startColumn' => 43,
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
 * Specify a raw index for the table.
 *
 * @param  string  $expression
 * @param  string  $name
 * @return \\Illuminate\\Database\\Schema\\IndexDefinition
 */',
        'startLine' => 729,
        'endLine' => 732,
        'startColumn' => 5,
        'endColumn' => 5,
        'couldThrow' => false,
        'isClosure' => false,
        'isGenerator' => false,
        'isVariadic' => false,
        'modifiers' => 1,
        'namespace' => 'Illuminate\\Database\\Schema',
        'declaringClassName' => 'Illuminate\\Database\\Schema\\Blueprint',
        'implementingClassName' => 'Illuminate\\Database\\Schema\\Blueprint',
        'currentClassName' => 'Illuminate\\Database\\Schema\\Blueprint',
        'aliasName' => NULL,
      ),
      'foreign' => 
      array (
        'name' => 'foreign',
        'parameters' => 
        array (
          'columns' => 
          array (
            'name' => 'columns',
            'default' => NULL,
            'type' => NULL,
            'isVariadic' => false,
            'byRef' => false,
            'isPromoted' => false,
            'attributes' => 
            array (
            ),
            'startLine' => 741,
            'endLine' => 741,
            'startColumn' => 29,
            'endColumn' => 36,
            'parameterIndex' => 0,
            'isOptional' => false,
          ),
          'name' => 
          array (
            'name' => 'name',
            'default' => 
            array (
              'code' => 'null',
              'attributes' => 
              array (
                'startLine' => 741,
                'endLine' => 741,
                'startTokenPos' => 2658,
                'startFilePos' => 19964,
                'endTokenPos' => 2658,
                'endFilePos' => 19967,
              ),
            ),
            'type' => NULL,
            'isVariadic' => false,
            'byRef' => false,
            'isPromoted' => false,
            'attributes' => 
            array (
            ),
            'startLine' => 741,
            'endLine' => 741,
            'startColumn' => 39,
            'endColumn' => 50,
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
 * Specify a foreign key for the table.
 *
 * @param  string|array  $columns
 * @param  string|null  $name
 * @return \\Illuminate\\Database\\Schema\\ForeignKeyDefinition
 */',
        'startLine' => 741,
        'endLine' => 750,
        'startColumn' => 5,
        'endColumn' => 5,
        'couldThrow' => false,
        'isClosure' => false,
        'isGenerator' => false,
        'isVariadic' => false,
        'modifiers' => 1,
        'namespace' => 'Illuminate\\Database\\Schema',
        'declaringClassName' => 'Illuminate\\Database\\Schema\\Blueprint',
        'implementingClassName' => 'Illuminate\\Database\\Schema\\Blueprint',
        'currentClassName' => 'Illuminate\\Database\\Schema\\Blueprint',
        'aliasName' => NULL,
      ),
      'id' => 
      array (
        'name' => 'id',
        'parameters' => 
        array (
          'column' => 
          array (
            'name' => 'column',
            'default' => 
            array (
              'code' => '\'id\'',
              'attributes' => 
              array (
                'startLine' => 758,
                'endLine' => 758,
                'startTokenPos' => 2732,
                'startFilePos' => 20467,
                'endTokenPos' => 2732,
                'endFilePos' => 20470,
              ),
            ),
            'type' => NULL,
            'isVariadic' => false,
            'byRef' => false,
            'isPromoted' => false,
            'attributes' => 
            array (
            ),
            'startLine' => 758,
            'endLine' => 758,
            'startColumn' => 24,
            'endColumn' => 37,
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
 * Create a new auto-incrementing big integer column on the table (8-byte, 0 to 18,446,744,073,709,551,615).
 *
 * @param  string  $column
 * @return \\Illuminate\\Database\\Schema\\ColumnDefinition
 */',
        'startLine' => 758,
        'endLine' => 761,
        'startColumn' => 5,
        'endColumn' => 5,
        'couldThrow' => false,
        'isClosure' => false,
        'isGenerator' => false,
        'isVariadic' => false,
        'modifiers' => 1,
        'namespace' => 'Illuminate\\Database\\Schema',
        'declaringClassName' => 'Illuminate\\Database\\Schema\\Blueprint',
        'implementingClassName' => 'Illuminate\\Database\\Schema\\Blueprint',
        'currentClassName' => 'Illuminate\\Database\\Schema\\Blueprint',
        'aliasName' => NULL,
      ),
      'increments' => 
      array (
        'name' => 'increments',
        'parameters' => 
        array (
          'column' => 
          array (
            'name' => 'column',
            'default' => NULL,
            'type' => NULL,
            'isVariadic' => false,
            'byRef' => false,
            'isPromoted' => false,
            'attributes' => 
            array (
            ),
            'startLine' => 769,
            'endLine' => 769,
            'startColumn' => 32,
            'endColumn' => 38,
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
 * Create a new auto-incrementing integer column on the table (4-byte, 0 to 4,294,967,295).
 *
 * @param  string  $column
 * @return \\Illuminate\\Database\\Schema\\ColumnDefinition
 */',
        'startLine' => 769,
        'endLine' => 772,
        'startColumn' => 5,
        'endColumn' => 5,
        'couldThrow' => false,
        'isClosure' => false,
        'isGenerator' => false,
        'isVariadic' => false,
        'modifiers' => 1,
        'namespace' => 'Illuminate\\Database\\Schema',
        'declaringClassName' => 'Illuminate\\Database\\Schema\\Blueprint',
        'implementingClassName' => 'Illuminate\\Database\\Schema\\Blueprint',
        'currentClassName' => 'Illuminate\\Database\\Schema\\Blueprint',
        'aliasName' => NULL,
      ),
      'integerIncrements' => 
      array (
        'name' => 'integerIncrements',
        'parameters' => 
        array (
          'column' => 
          array (
            'name' => 'column',
            'default' => NULL,
            'type' => NULL,
            'isVariadic' => false,
            'byRef' => false,
            'isPromoted' => false,
            'attributes' => 
            array (
            ),
            'startLine' => 780,
            'endLine' => 780,
            'startColumn' => 39,
            'endColumn' => 45,
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
 * Create a new auto-incrementing integer column on the table (4-byte, 0 to 4,294,967,295).
 *
 * @param  string  $column
 * @return \\Illuminate\\Database\\Schema\\ColumnDefinition
 */',
        'startLine' => 780,
        'endLine' => 783,
        'startColumn' => 5,
        'endColumn' => 5,
        'couldThrow' => false,
        'isClosure' => false,
        'isGenerator' => false,
        'isVariadic' => false,
        'modifiers' => 1,
        'namespace' => 'Illuminate\\Database\\Schema',
        'declaringClassName' => 'Illuminate\\Database\\Schema\\Blueprint',
        'implementingClassName' => 'Illuminate\\Database\\Schema\\Blueprint',
        'currentClassName' => 'Illuminate\\Database\\Schema\\Blueprint',
        'aliasName' => NULL,
      ),
      'tinyIncrements' => 
      array (
        'name' => 'tinyIncrements',
        'parameters' => 
        array (
          'column' => 
          array (
            'name' => 'column',
            'default' => NULL,
            'type' => NULL,
            'isVariadic' => false,
            'byRef' => false,
            'isPromoted' => false,
            'attributes' => 
            array (
            ),
            'startLine' => 791,
            'endLine' => 791,
            'startColumn' => 36,
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
 * Create a new auto-incrementing tiny integer column on the table (1-byte, 0 to 255).
 *
 * @param  string  $column
 * @return \\Illuminate\\Database\\Schema\\ColumnDefinition
 */',
        'startLine' => 791,
        'endLine' => 794,
        'startColumn' => 5,
        'endColumn' => 5,
        'couldThrow' => false,
        'isClosure' => false,
        'isGenerator' => false,
        'isVariadic' => false,
        'modifiers' => 1,
        'namespace' => 'Illuminate\\Database\\Schema',
        'declaringClassName' => 'Illuminate\\Database\\Schema\\Blueprint',
        'implementingClassName' => 'Illuminate\\Database\\Schema\\Blueprint',
        'currentClassName' => 'Illuminate\\Database\\Schema\\Blueprint',
        'aliasName' => NULL,
      ),
      'smallIncrements' => 
      array (
        'name' => 'smallIncrements',
        'parameters' => 
        array (
          'column' => 
          array (
            'name' => 'column',
            'default' => NULL,
            'type' => NULL,
            'isVariadic' => false,
            'byRef' => false,
            'isPromoted' => false,
            'attributes' => 
            array (
            ),
            'startLine' => 802,
            'endLine' => 802,
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
 * Create a new auto-incrementing small integer column on the table (2-byte, 0 to 65,535).
 *
 * @param  string  $column
 * @return \\Illuminate\\Database\\Schema\\ColumnDefinition
 */',
        'startLine' => 802,
        'endLine' => 805,
        'startColumn' => 5,
        'endColumn' => 5,
        'couldThrow' => false,
        'isClosure' => false,
        'isGenerator' => false,
        'isVariadic' => false,
        'modifiers' => 1,
        'namespace' => 'Illuminate\\Database\\Schema',
        'declaringClassName' => 'Illuminate\\Database\\Schema\\Blueprint',
        'implementingClassName' => 'Illuminate\\Database\\Schema\\Blueprint',
        'currentClassName' => 'Illuminate\\Database\\Schema\\Blueprint',
        'aliasName' => NULL,
      ),
      'mediumIncrements' => 
      array (
        'name' => 'mediumIncrements',
        'parameters' => 
        array (
          'column' => 
          array (
            'name' => 'column',
            'default' => NULL,
            'type' => NULL,
            'isVariadic' => false,
            'byRef' => false,
            'isPromoted' => false,
            'attributes' => 
            array (
            ),
            'startLine' => 813,
            'endLine' => 813,
            'startColumn' => 38,
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
 * Create a new auto-incrementing medium integer column on the table (3-byte, 0 to 16,777,215).
 *
 * @param  string  $column
 * @return \\Illuminate\\Database\\Schema\\ColumnDefinition
 */',
        'startLine' => 813,
        'endLine' => 816,
        'startColumn' => 5,
        'endColumn' => 5,
        'couldThrow' => false,
        'isClosure' => false,
        'isGenerator' => false,
        'isVariadic' => false,
        'modifiers' => 1,
        'namespace' => 'Illuminate\\Database\\Schema',
        'declaringClassName' => 'Illuminate\\Database\\Schema\\Blueprint',
        'implementingClassName' => 'Illuminate\\Database\\Schema\\Blueprint',
        'currentClassName' => 'Illuminate\\Database\\Schema\\Blueprint',
        'aliasName' => NULL,
      ),
      'bigIncrements' => 
      array (
        'name' => 'bigIncrements',
        'parameters' => 
        array (
          'column' => 
          array (
            'name' => 'column',
            'default' => NULL,
            'type' => NULL,
            'isVariadic' => false,
            'byRef' => false,
            'isPromoted' => false,
            'attributes' => 
            array (
            ),
            'startLine' => 824,
            'endLine' => 824,
            'startColumn' => 35,
            'endColumn' => 41,
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
 * Create a new auto-incrementing big integer column on the table (8-byte, 0 to 18,446,744,073,709,551,615).
 *
 * @param  string  $column
 * @return \\Illuminate\\Database\\Schema\\ColumnDefinition
 */',
        'startLine' => 824,
        'endLine' => 827,
        'startColumn' => 5,
        'endColumn' => 5,
        'couldThrow' => false,
        'isClosure' => false,
        'isGenerator' => false,
        'isVariadic' => false,
        'modifiers' => 1,
        'namespace' => 'Illuminate\\Database\\Schema',
        'declaringClassName' => 'Illuminate\\Database\\Schema\\Blueprint',
        'implementingClassName' => 'Illuminate\\Database\\Schema\\Blueprint',
        'currentClassName' => 'Illuminate\\Database\\Schema\\Blueprint',
        'aliasName' => NULL,
      ),
      'char' => 
      array (
        'name' => 'char',
        'parameters' => 
        array (
          'column' => 
          array (
            'name' => 'column',
            'default' => NULL,
            'type' => NULL,
            'isVariadic' => false,
            'byRef' => false,
            'isPromoted' => false,
            'attributes' => 
            array (
            ),
            'startLine' => 836,
            'endLine' => 836,
            'startColumn' => 26,
            'endColumn' => 32,
            'parameterIndex' => 0,
            'isOptional' => false,
          ),
          'length' => 
          array (
            'name' => 'length',
            'default' => 
            array (
              'code' => 'null',
              'attributes' => 
              array (
                'startLine' => 836,
                'endLine' => 836,
                'startTokenPos' => 2932,
                'startFilePos' => 22729,
                'endTokenPos' => 2932,
                'endFilePos' => 22732,
              ),
            ),
            'type' => NULL,
            'isVariadic' => false,
            'byRef' => false,
            'isPromoted' => false,
            'attributes' => 
            array (
            ),
            'startLine' => 836,
            'endLine' => 836,
            'startColumn' => 35,
            'endColumn' => 48,
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
 * Create a new char column on the table.
 *
 * @param  string  $column
 * @param  int|null  $length
 * @return \\Illuminate\\Database\\Schema\\ColumnDefinition
 */',
        'startLine' => 836,
        'endLine' => 841,
        'startColumn' => 5,
        'endColumn' => 5,
        'couldThrow' => false,
        'isClosure' => false,
        'isGenerator' => false,
        'isVariadic' => false,
        'modifiers' => 1,
        'namespace' => 'Illuminate\\Database\\Schema',
        'declaringClassName' => 'Illuminate\\Database\\Schema\\Blueprint',
        'implementingClassName' => 'Illuminate\\Database\\Schema\\Blueprint',
        'currentClassName' => 'Illuminate\\Database\\Schema\\Blueprint',
        'aliasName' => NULL,
      ),
      'string' => 
      array (
        'name' => 'string',
        'parameters' => 
        array (
          'column' => 
          array (
            'name' => 'column',
            'default' => NULL,
            'type' => NULL,
            'isVariadic' => false,
            'byRef' => false,
            'isPromoted' => false,
            'attributes' => 
            array (
            ),
            'startLine' => 850,
            'endLine' => 850,
            'startColumn' => 28,
            'endColumn' => 34,
            'parameterIndex' => 0,
            'isOptional' => false,
          ),
          'length' => 
          array (
            'name' => 'length',
            'default' => 
            array (
              'code' => 'null',
              'attributes' => 
              array (
                'startLine' => 850,
                'endLine' => 850,
                'startTokenPos' => 2998,
                'startFilePos' => 23143,
                'endTokenPos' => 2998,
                'endFilePos' => 23146,
              ),
            ),
            'type' => NULL,
            'isVariadic' => false,
            'byRef' => false,
            'isPromoted' => false,
            'attributes' => 
            array (
            ),
            'startLine' => 850,
            'endLine' => 850,
            'startColumn' => 37,
            'endColumn' => 50,
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
 * Create a new string column on the table.
 *
 * @param  string  $column
 * @param  int|null  $length
 * @return \\Illuminate\\Database\\Schema\\ColumnDefinition
 */',
        'startLine' => 850,
        'endLine' => 855,
        'startColumn' => 5,
        'endColumn' => 5,
        'couldThrow' => false,
        'isClosure' => false,
        'isGenerator' => false,
        'isVariadic' => false,
        'modifiers' => 1,
        'namespace' => 'Illuminate\\Database\\Schema',
        'declaringClassName' => 'Illuminate\\Database\\Schema\\Blueprint',
        'implementingClassName' => 'Illuminate\\Database\\Schema\\Blueprint',
        'currentClassName' => 'Illuminate\\Database\\Schema\\Blueprint',
        'aliasName' => NULL,
      ),
      'tinyText' => 
      array (
        'name' => 'tinyText',
        'parameters' => 
        array (
          'column' => 
          array (
            'name' => 'column',
            'default' => NULL,
            'type' => NULL,
            'isVariadic' => false,
            'byRef' => false,
            'isPromoted' => false,
            'attributes' => 
            array (
            ),
            'startLine' => 863,
            'endLine' => 863,
            'startColumn' => 30,
            'endColumn' => 36,
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
 * Create a new tiny text column on the table (up to 255 characters).
 *
 * @param  string  $column
 * @return \\Illuminate\\Database\\Schema\\ColumnDefinition
 */',
        'startLine' => 863,
        'endLine' => 866,
        'startColumn' => 5,
        'endColumn' => 5,
        'couldThrow' => false,
        'isClosure' => false,
        'isGenerator' => false,
        'isVariadic' => false,
        'modifiers' => 1,
        'namespace' => 'Illuminate\\Database\\Schema',
        'declaringClassName' => 'Illuminate\\Database\\Schema\\Blueprint',
        'implementingClassName' => 'Illuminate\\Database\\Schema\\Blueprint',
        'currentClassName' => 'Illuminate\\Database\\Schema\\Blueprint',
        'aliasName' => NULL,
      ),
      'text' => 
      array (
        'name' => 'text',
        'parameters' => 
        array (
          'column' => 
          array (
            'name' => 'column',
            'default' => NULL,
            'type' => NULL,
            'isVariadic' => false,
            'byRef' => false,
            'isPromoted' => false,
            'attributes' => 
            array (
            ),
            'startLine' => 874,
            'endLine' => 874,
            'startColumn' => 26,
            'endColumn' => 32,
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
 * Create a new text column on the table (up to 65,535 characters / ~64 KB).
 *
 * @param  string  $column
 * @return \\Illuminate\\Database\\Schema\\ColumnDefinition
 */',
        'startLine' => 874,
        'endLine' => 877,
        'startColumn' => 5,
        'endColumn' => 5,
        'couldThrow' => false,
        'isClosure' => false,
        'isGenerator' => false,
        'isVariadic' => false,
        'modifiers' => 1,
        'namespace' => 'Illuminate\\Database\\Schema',
        'declaringClassName' => 'Illuminate\\Database\\Schema\\Blueprint',
        'implementingClassName' => 'Illuminate\\Database\\Schema\\Blueprint',
        'currentClassName' => 'Illuminate\\Database\\Schema\\Blueprint',
        'aliasName' => NULL,
      ),
      'mediumText' => 
      array (
        'name' => 'mediumText',
        'parameters' => 
        array (
          'column' => 
          array (
            'name' => 'column',
            'default' => NULL,
            'type' => NULL,
            'isVariadic' => false,
            'byRef' => false,
            'isPromoted' => false,
            'attributes' => 
            array (
            ),
            'startLine' => 885,
            'endLine' => 885,
            'startColumn' => 32,
            'endColumn' => 38,
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
 * Create a new medium text column on the table (up to 16,777,215 characters / ~16 MB).
 *
 * @param  string  $column
 * @return \\Illuminate\\Database\\Schema\\ColumnDefinition
 */',
        'startLine' => 885,
        'endLine' => 888,
        'startColumn' => 5,
        'endColumn' => 5,
        'couldThrow' => false,
        'isClosure' => false,
        'isGenerator' => false,
        'isVariadic' => false,
        'modifiers' => 1,
        'namespace' => 'Illuminate\\Database\\Schema',
        'declaringClassName' => 'Illuminate\\Database\\Schema\\Blueprint',
        'implementingClassName' => 'Illuminate\\Database\\Schema\\Blueprint',
        'currentClassName' => 'Illuminate\\Database\\Schema\\Blueprint',
        'aliasName' => NULL,
      ),
      'longText' => 
      array (
        'name' => 'longText',
        'parameters' => 
        array (
          'column' => 
          array (
            'name' => 'column',
            'default' => NULL,
            'type' => NULL,
            'isVariadic' => false,
            'byRef' => false,
            'isPromoted' => false,
            'attributes' => 
            array (
            ),
            'startLine' => 896,
            'endLine' => 896,
            'startColumn' => 30,
            'endColumn' => 36,
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
 * Create a new long text column on the table (up to 4,294,967,295 characters / ~4 GB).
 *
 * @param  string  $column
 * @return \\Illuminate\\Database\\Schema\\ColumnDefinition
 */',
        'startLine' => 896,
        'endLine' => 899,
        'startColumn' => 5,
        'endColumn' => 5,
        'couldThrow' => false,
        'isClosure' => false,
        'isGenerator' => false,
        'isVariadic' => false,
        'modifiers' => 1,
        'namespace' => 'Illuminate\\Database\\Schema',
        'declaringClassName' => 'Illuminate\\Database\\Schema\\Blueprint',
        'implementingClassName' => 'Illuminate\\Database\\Schema\\Blueprint',
        'currentClassName' => 'Illuminate\\Database\\Schema\\Blueprint',
        'aliasName' => NULL,
      ),
      'integer' => 
      array (
        'name' => 'integer',
        'parameters' => 
        array (
          'column' => 
          array (
            'name' => 'column',
            'default' => NULL,
            'type' => NULL,
            'isVariadic' => false,
            'byRef' => false,
            'isPromoted' => false,
            'attributes' => 
            array (
            ),
            'startLine' => 910,
            'endLine' => 910,
            'startColumn' => 29,
            'endColumn' => 35,
            'parameterIndex' => 0,
            'isOptional' => false,
          ),
          'autoIncrement' => 
          array (
            'name' => 'autoIncrement',
            'default' => 
            array (
              'code' => 'false',
              'attributes' => 
              array (
                'startLine' => 910,
                'endLine' => 910,
                'startTokenPos' => 3168,
                'startFilePos' => 24891,
                'endTokenPos' => 3168,
                'endFilePos' => 24895,
              ),
            ),
            'type' => NULL,
            'isVariadic' => false,
            'byRef' => false,
            'isPromoted' => false,
            'attributes' => 
            array (
            ),
            'startLine' => 910,
            'endLine' => 910,
            'startColumn' => 38,
            'endColumn' => 59,
            'parameterIndex' => 1,
            'isOptional' => true,
          ),
          'unsigned' => 
          array (
            'name' => 'unsigned',
            'default' => 
            array (
              'code' => 'false',
              'attributes' => 
              array (
                'startLine' => 910,
                'endLine' => 910,
                'startTokenPos' => 3175,
                'startFilePos' => 24910,
                'endTokenPos' => 3175,
                'endFilePos' => 24914,
              ),
            ),
            'type' => NULL,
            'isVariadic' => false,
            'byRef' => false,
            'isPromoted' => false,
            'attributes' => 
            array (
            ),
            'startLine' => 910,
            'endLine' => 910,
            'startColumn' => 62,
            'endColumn' => 78,
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
 * Create a new integer (4-byte) column on the table.
 * Range: -2,147,483,648 to 2,147,483,647 (signed) or 0 to 4,294,967,295 (unsigned).
 *
 * @param  string  $column
 * @param  bool  $autoIncrement
 * @param  bool  $unsigned
 * @return \\Illuminate\\Database\\Schema\\ColumnDefinition
 */',
        'startLine' => 910,
        'endLine' => 913,
        'startColumn' => 5,
        'endColumn' => 5,
        'couldThrow' => false,
        'isClosure' => false,
        'isGenerator' => false,
        'isVariadic' => false,
        'modifiers' => 1,
        'namespace' => 'Illuminate\\Database\\Schema',
        'declaringClassName' => 'Illuminate\\Database\\Schema\\Blueprint',
        'implementingClassName' => 'Illuminate\\Database\\Schema\\Blueprint',
        'currentClassName' => 'Illuminate\\Database\\Schema\\Blueprint',
        'aliasName' => NULL,
      ),
      'tinyInteger' => 
      array (
        'name' => 'tinyInteger',
        'parameters' => 
        array (
          'column' => 
          array (
            'name' => 'column',
            'default' => NULL,
            'type' => NULL,
            'isVariadic' => false,
            'byRef' => false,
            'isPromoted' => false,
            'attributes' => 
            array (
            ),
            'startLine' => 924,
            'endLine' => 924,
            'startColumn' => 33,
            'endColumn' => 39,
            'parameterIndex' => 0,
            'isOptional' => false,
          ),
          'autoIncrement' => 
          array (
            'name' => 'autoIncrement',
            'default' => 
            array (
              'code' => 'false',
              'attributes' => 
              array (
                'startLine' => 924,
                'endLine' => 924,
                'startTokenPos' => 3226,
                'startFilePos' => 25406,
                'endTokenPos' => 3226,
                'endFilePos' => 25410,
              ),
            ),
            'type' => NULL,
            'isVariadic' => false,
            'byRef' => false,
            'isPromoted' => false,
            'attributes' => 
            array (
            ),
            'startLine' => 924,
            'endLine' => 924,
            'startColumn' => 42,
            'endColumn' => 63,
            'parameterIndex' => 1,
            'isOptional' => true,
          ),
          'unsigned' => 
          array (
            'name' => 'unsigned',
            'default' => 
            array (
              'code' => 'false',
              'attributes' => 
              array (
                'startLine' => 924,
                'endLine' => 924,
                'startTokenPos' => 3233,
                'startFilePos' => 25425,
                'endTokenPos' => 3233,
                'endFilePos' => 25429,
              ),
            ),
            'type' => NULL,
            'isVariadic' => false,
            'byRef' => false,
            'isPromoted' => false,
            'attributes' => 
            array (
            ),
            'startLine' => 924,
            'endLine' => 924,
            'startColumn' => 66,
            'endColumn' => 82,
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
 * Create a new tiny integer (1-byte) column on the table.
 * Range: -128 to 127 (signed) or 0 to 255 (unsigned).
 *
 * @param  string  $column
 * @param  bool  $autoIncrement
 * @param  bool  $unsigned
 * @return \\Illuminate\\Database\\Schema\\ColumnDefinition
 */',
        'startLine' => 924,
        'endLine' => 927,
        'startColumn' => 5,
        'endColumn' => 5,
        'couldThrow' => false,
        'isClosure' => false,
        'isGenerator' => false,
        'isVariadic' => false,
        'modifiers' => 1,
        'namespace' => 'Illuminate\\Database\\Schema',
        'declaringClassName' => 'Illuminate\\Database\\Schema\\Blueprint',
        'implementingClassName' => 'Illuminate\\Database\\Schema\\Blueprint',
        'currentClassName' => 'Illuminate\\Database\\Schema\\Blueprint',
        'aliasName' => NULL,
      ),
      'smallInteger' => 
      array (
        'name' => 'smallInteger',
        'parameters' => 
        array (
          'column' => 
          array (
            'name' => 'column',
            'default' => NULL,
            'type' => NULL,
            'isVariadic' => false,
            'byRef' => false,
            'isPromoted' => false,
            'attributes' => 
            array (
            ),
            'startLine' => 938,
            'endLine' => 938,
            'startColumn' => 34,
            'endColumn' => 40,
            'parameterIndex' => 0,
            'isOptional' => false,
          ),
          'autoIncrement' => 
          array (
            'name' => 'autoIncrement',
            'default' => 
            array (
              'code' => 'false',
              'attributes' => 
              array (
                'startLine' => 938,
                'endLine' => 938,
                'startTokenPos' => 3284,
                'startFilePos' => 25936,
                'endTokenPos' => 3284,
                'endFilePos' => 25940,
              ),
            ),
            'type' => NULL,
            'isVariadic' => false,
            'byRef' => false,
            'isPromoted' => false,
            'attributes' => 
            array (
            ),
            'startLine' => 938,
            'endLine' => 938,
            'startColumn' => 43,
            'endColumn' => 64,
            'parameterIndex' => 1,
            'isOptional' => true,
          ),
          'unsigned' => 
          array (
            'name' => 'unsigned',
            'default' => 
            array (
              'code' => 'false',
              'attributes' => 
              array (
                'startLine' => 938,
                'endLine' => 938,
                'startTokenPos' => 3291,
                'startFilePos' => 25955,
                'endTokenPos' => 3291,
                'endFilePos' => 25959,
              ),
            ),
            'type' => NULL,
            'isVariadic' => false,
            'byRef' => false,
            'isPromoted' => false,
            'attributes' => 
            array (
            ),
            'startLine' => 938,
            'endLine' => 938,
            'startColumn' => 67,
            'endColumn' => 83,
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
 * Create a new small integer (2-byte) column on the table.
 * Range: -32,768 to 32,767 (signed) or 0 to 65,535 (unsigned).
 *
 * @param  string  $column
 * @param  bool  $autoIncrement
 * @param  bool  $unsigned
 * @return \\Illuminate\\Database\\Schema\\ColumnDefinition
 */',
        'startLine' => 938,
        'endLine' => 941,
        'startColumn' => 5,
        'endColumn' => 5,
        'couldThrow' => false,
        'isClosure' => false,
        'isGenerator' => false,
        'isVariadic' => false,
        'modifiers' => 1,
        'namespace' => 'Illuminate\\Database\\Schema',
        'declaringClassName' => 'Illuminate\\Database\\Schema\\Blueprint',
        'implementingClassName' => 'Illuminate\\Database\\Schema\\Blueprint',
        'currentClassName' => 'Illuminate\\Database\\Schema\\Blueprint',
        'aliasName' => NULL,
      ),
      'mediumInteger' => 
      array (
        'name' => 'mediumInteger',
        'parameters' => 
        array (
          'column' => 
          array (
            'name' => 'column',
            'default' => NULL,
            'type' => NULL,
            'isVariadic' => false,
            'byRef' => false,
            'isPromoted' => false,
            'attributes' => 
            array (
            ),
            'startLine' => 952,
            'endLine' => 952,
            'startColumn' => 35,
            'endColumn' => 41,
            'parameterIndex' => 0,
            'isOptional' => false,
          ),
          'autoIncrement' => 
          array (
            'name' => 'autoIncrement',
            'default' => 
            array (
              'code' => 'false',
              'attributes' => 
              array (
                'startLine' => 952,
                'endLine' => 952,
                'startTokenPos' => 3342,
                'startFilePos' => 26479,
                'endTokenPos' => 3342,
                'endFilePos' => 26483,
              ),
            ),
            'type' => NULL,
            'isVariadic' => false,
            'byRef' => false,
            'isPromoted' => false,
            'attributes' => 
            array (
            ),
            'startLine' => 952,
            'endLine' => 952,
            'startColumn' => 44,
            'endColumn' => 65,
            'parameterIndex' => 1,
            'isOptional' => true,
          ),
          'unsigned' => 
          array (
            'name' => 'unsigned',
            'default' => 
            array (
              'code' => 'false',
              'attributes' => 
              array (
                'startLine' => 952,
                'endLine' => 952,
                'startTokenPos' => 3349,
                'startFilePos' => 26498,
                'endTokenPos' => 3349,
                'endFilePos' => 26502,
              ),
            ),
            'type' => NULL,
            'isVariadic' => false,
            'byRef' => false,
            'isPromoted' => false,
            'attributes' => 
            array (
            ),
            'startLine' => 952,
            'endLine' => 952,
            'startColumn' => 68,
            'endColumn' => 84,
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
 * Create a new medium integer (3-byte) column on the table.
 * Range: -8,388,608 to 8,388,607 (signed) or 0 to 16,777,215 (unsigned).
 *
 * @param  string  $column
 * @param  bool  $autoIncrement
 * @param  bool  $unsigned
 * @return \\Illuminate\\Database\\Schema\\ColumnDefinition
 */',
        'startLine' => 952,
        'endLine' => 955,
        'startColumn' => 5,
        'endColumn' => 5,
        'couldThrow' => false,
        'isClosure' => false,
        'isGenerator' => false,
        'isVariadic' => false,
        'modifiers' => 1,
        'namespace' => 'Illuminate\\Database\\Schema',
        'declaringClassName' => 'Illuminate\\Database\\Schema\\Blueprint',
        'implementingClassName' => 'Illuminate\\Database\\Schema\\Blueprint',
        'currentClassName' => 'Illuminate\\Database\\Schema\\Blueprint',
        'aliasName' => NULL,
      ),
      'bigInteger' => 
      array (
        'name' => 'bigInteger',
        'parameters' => 
        array (
          'column' => 
          array (
            'name' => 'column',
            'default' => NULL,
            'type' => NULL,
            'isVariadic' => false,
            'byRef' => false,
            'isPromoted' => false,
            'attributes' => 
            array (
            ),
            'startLine' => 966,
            'endLine' => 966,
            'startColumn' => 32,
            'endColumn' => 38,
            'parameterIndex' => 0,
            'isOptional' => false,
          ),
          'autoIncrement' => 
          array (
            'name' => 'autoIncrement',
            'default' => 
            array (
              'code' => 'false',
              'attributes' => 
              array (
                'startLine' => 966,
                'endLine' => 966,
                'startTokenPos' => 3400,
                'startFilePos' => 27065,
                'endTokenPos' => 3400,
                'endFilePos' => 27069,
              ),
            ),
            'type' => NULL,
            'isVariadic' => false,
            'byRef' => false,
            'isPromoted' => false,
            'attributes' => 
            array (
            ),
            'startLine' => 966,
            'endLine' => 966,
            'startColumn' => 41,
            'endColumn' => 62,
            'parameterIndex' => 1,
            'isOptional' => true,
          ),
          'unsigned' => 
          array (
            'name' => 'unsigned',
            'default' => 
            array (
              'code' => 'false',
              'attributes' => 
              array (
                'startLine' => 966,
                'endLine' => 966,
                'startTokenPos' => 3407,
                'startFilePos' => 27084,
                'endTokenPos' => 3407,
                'endFilePos' => 27088,
              ),
            ),
            'type' => NULL,
            'isVariadic' => false,
            'byRef' => false,
            'isPromoted' => false,
            'attributes' => 
            array (
            ),
            'startLine' => 966,
            'endLine' => 966,
            'startColumn' => 65,
            'endColumn' => 81,
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
 * Create a new big integer (8-byte) column on the table.
 * Range: -9,223,372,036,854,775,808 to 9,223,372,036,854,775,807 (signed) or 0 to 18,446,744,073,709,551,615 (unsigned).
 *
 * @param  string  $column
 * @param  bool  $autoIncrement
 * @param  bool  $unsigned
 * @return \\Illuminate\\Database\\Schema\\ColumnDefinition
 */',
        'startLine' => 966,
        'endLine' => 969,
        'startColumn' => 5,
        'endColumn' => 5,
        'couldThrow' => false,
        'isClosure' => false,
        'isGenerator' => false,
        'isVariadic' => false,
        'modifiers' => 1,
        'namespace' => 'Illuminate\\Database\\Schema',
        'declaringClassName' => 'Illuminate\\Database\\Schema\\Blueprint',
        'implementingClassName' => 'Illuminate\\Database\\Schema\\Blueprint',
        'currentClassName' => 'Illuminate\\Database\\Schema\\Blueprint',
        'aliasName' => NULL,
      ),
      'unsignedInteger' => 
      array (
        'name' => 'unsignedInteger',
        'parameters' => 
        array (
          'column' => 
          array (
            'name' => 'column',
            'default' => NULL,
            'type' => NULL,
            'isVariadic' => false,
            'byRef' => false,
            'isPromoted' => false,
            'attributes' => 
            array (
            ),
            'startLine' => 978,
            'endLine' => 978,
            'startColumn' => 37,
            'endColumn' => 43,
            'parameterIndex' => 0,
            'isOptional' => false,
          ),
          'autoIncrement' => 
          array (
            'name' => 'autoIncrement',
            'default' => 
            array (
              'code' => 'false',
              'attributes' => 
              array (
                'startLine' => 978,
                'endLine' => 978,
                'startTokenPos' => 3458,
                'startFilePos' => 27521,
                'endTokenPos' => 3458,
                'endFilePos' => 27525,
              ),
            ),
            'type' => NULL,
            'isVariadic' => false,
            'byRef' => false,
            'isPromoted' => false,
            'attributes' => 
            array (
            ),
            'startLine' => 978,
            'endLine' => 978,
            'startColumn' => 46,
            'endColumn' => 67,
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
 * Create a new unsigned integer column on the table (4-byte, 0 to 4,294,967,295).
 *
 * @param  string  $column
 * @param  bool  $autoIncrement
 * @return \\Illuminate\\Database\\Schema\\ColumnDefinition
 */',
        'startLine' => 978,
        'endLine' => 981,
        'startColumn' => 5,
        'endColumn' => 5,
        'couldThrow' => false,
        'isClosure' => false,
        'isGenerator' => false,
        'isVariadic' => false,
        'modifiers' => 1,
        'namespace' => 'Illuminate\\Database\\Schema',
        'declaringClassName' => 'Illuminate\\Database\\Schema\\Blueprint',
        'implementingClassName' => 'Illuminate\\Database\\Schema\\Blueprint',
        'currentClassName' => 'Illuminate\\Database\\Schema\\Blueprint',
        'aliasName' => NULL,
      ),
      'unsignedTinyInteger' => 
      array (
        'name' => 'unsignedTinyInteger',
        'parameters' => 
        array (
          'column' => 
          array (
            'name' => 'column',
            'default' => NULL,
            'type' => NULL,
            'isVariadic' => false,
            'byRef' => false,
            'isPromoted' => false,
            'attributes' => 
            array (
            ),
            'startLine' => 990,
            'endLine' => 990,
            'startColumn' => 41,
            'endColumn' => 47,
            'parameterIndex' => 0,
            'isOptional' => false,
          ),
          'autoIncrement' => 
          array (
            'name' => 'autoIncrement',
            'default' => 
            array (
              'code' => 'false',
              'attributes' => 
              array (
                'startLine' => 990,
                'endLine' => 990,
                'startTokenPos' => 3496,
                'startFilePos' => 27901,
                'endTokenPos' => 3496,
                'endFilePos' => 27905,
              ),
            ),
            'type' => NULL,
            'isVariadic' => false,
            'byRef' => false,
            'isPromoted' => false,
            'attributes' => 
            array (
            ),
            'startLine' => 990,
            'endLine' => 990,
            'startColumn' => 50,
            'endColumn' => 71,
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
 * Create a new unsigned tiny integer column on the table (1-byte, 0 to 255).
 *
 * @param  string  $column
 * @param  bool  $autoIncrement
 * @return \\Illuminate\\Database\\Schema\\ColumnDefinition
 */',
        'startLine' => 990,
        'endLine' => 993,
        'startColumn' => 5,
        'endColumn' => 5,
        'couldThrow' => false,
        'isClosure' => false,
        'isGenerator' => false,
        'isVariadic' => false,
        'modifiers' => 1,
        'namespace' => 'Illuminate\\Database\\Schema',
        'declaringClassName' => 'Illuminate\\Database\\Schema\\Blueprint',
        'implementingClassName' => 'Illuminate\\Database\\Schema\\Blueprint',
        'currentClassName' => 'Illuminate\\Database\\Schema\\Blueprint',
        'aliasName' => NULL,
      ),
      'unsignedSmallInteger' => 
      array (
        'name' => 'unsignedSmallInteger',
        'parameters' => 
        array (
          'column' => 
          array (
            'name' => 'column',
            'default' => NULL,
            'type' => NULL,
            'isVariadic' => false,
            'byRef' => false,
            'isPromoted' => false,
            'attributes' => 
            array (
            ),
            'startLine' => 1002,
            'endLine' => 1002,
            'startColumn' => 42,
            'endColumn' => 48,
            'parameterIndex' => 0,
            'isOptional' => false,
          ),
          'autoIncrement' => 
          array (
            'name' => 'autoIncrement',
            'default' => 
            array (
              'code' => 'false',
              'attributes' => 
              array (
                'startLine' => 1002,
                'endLine' => 1002,
                'startTokenPos' => 3534,
                'startFilePos' => 28290,
                'endTokenPos' => 3534,
                'endFilePos' => 28294,
              ),
            ),
            'type' => NULL,
            'isVariadic' => false,
            'byRef' => false,
            'isPromoted' => false,
            'attributes' => 
            array (
            ),
            'startLine' => 1002,
            'endLine' => 1002,
            'startColumn' => 51,
            'endColumn' => 72,
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
 * Create a new unsigned small integer column on the table (2-byte, 0 to 65,535).
 *
 * @param  string  $column
 * @param  bool  $autoIncrement
 * @return \\Illuminate\\Database\\Schema\\ColumnDefinition
 */',
        'startLine' => 1002,
        'endLine' => 1005,
        'startColumn' => 5,
        'endColumn' => 5,
        'couldThrow' => false,
        'isClosure' => false,
        'isGenerator' => false,
        'isVariadic' => false,
        'modifiers' => 1,
        'namespace' => 'Illuminate\\Database\\Schema',
        'declaringClassName' => 'Illuminate\\Database\\Schema\\Blueprint',
        'implementingClassName' => 'Illuminate\\Database\\Schema\\Blueprint',
        'currentClassName' => 'Illuminate\\Database\\Schema\\Blueprint',
        'aliasName' => NULL,
      ),
      'unsignedMediumInteger' => 
      array (
        'name' => 'unsignedMediumInteger',
        'parameters' => 
        array (
          'column' => 
          array (
            'name' => 'column',
            'default' => NULL,
            'type' => NULL,
            'isVariadic' => false,
            'byRef' => false,
            'isPromoted' => false,
            'attributes' => 
            array (
            ),
            'startLine' => 1014,
            'endLine' => 1014,
            'startColumn' => 43,
            'endColumn' => 49,
            'parameterIndex' => 0,
            'isOptional' => false,
          ),
          'autoIncrement' => 
          array (
            'name' => 'autoIncrement',
            'default' => 
            array (
              'code' => 'false',
              'attributes' => 
              array (
                'startLine' => 1014,
                'endLine' => 1014,
                'startTokenPos' => 3572,
                'startFilePos' => 28686,
                'endTokenPos' => 3572,
                'endFilePos' => 28690,
              ),
            ),
            'type' => NULL,
            'isVariadic' => false,
            'byRef' => false,
            'isPromoted' => false,
            'attributes' => 
            array (
            ),
            'startLine' => 1014,
            'endLine' => 1014,
            'startColumn' => 52,
            'endColumn' => 73,
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
 * Create a new unsigned medium integer column on the table (3-byte, 0 to 16,777,215).
 *
 * @param  string  $column
 * @param  bool  $autoIncrement
 * @return \\Illuminate\\Database\\Schema\\ColumnDefinition
 */',
        'startLine' => 1014,
        'endLine' => 1017,
        'startColumn' => 5,
        'endColumn' => 5,
        'couldThrow' => false,
        'isClosure' => false,
        'isGenerator' => false,
        'isVariadic' => false,
        'modifiers' => 1,
        'namespace' => 'Illuminate\\Database\\Schema',
        'declaringClassName' => 'Illuminate\\Database\\Schema\\Blueprint',
        'implementingClassName' => 'Illuminate\\Database\\Schema\\Blueprint',
        'currentClassName' => 'Illuminate\\Database\\Schema\\Blueprint',
        'aliasName' => NULL,
      ),
      'unsignedBigInteger' => 
      array (
        'name' => 'unsignedBigInteger',
        'parameters' => 
        array (
          'column' => 
          array (
            'name' => 'column',
            'default' => NULL,
            'type' => NULL,
            'isVariadic' => false,
            'byRef' => false,
            'isPromoted' => false,
            'attributes' => 
            array (
            ),
            'startLine' => 1026,
            'endLine' => 1026,
            'startColumn' => 40,
            'endColumn' => 46,
            'parameterIndex' => 0,
            'isOptional' => false,
          ),
          'autoIncrement' => 
          array (
            'name' => 'autoIncrement',
            'default' => 
            array (
              'code' => 'false',
              'attributes' => 
              array (
                'startLine' => 1026,
                'endLine' => 1026,
                'startTokenPos' => 3610,
                'startFilePos' => 29093,
                'endTokenPos' => 3610,
                'endFilePos' => 29097,
              ),
            ),
            'type' => NULL,
            'isVariadic' => false,
            'byRef' => false,
            'isPromoted' => false,
            'attributes' => 
            array (
            ),
            'startLine' => 1026,
            'endLine' => 1026,
            'startColumn' => 49,
            'endColumn' => 70,
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
 * Create a new unsigned big integer column on the table (8-byte, 0 to 18,446,744,073,709,551,615).
 *
 * @param  string  $column
 * @param  bool  $autoIncrement
 * @return \\Illuminate\\Database\\Schema\\ColumnDefinition
 */',
        'startLine' => 1026,
        'endLine' => 1029,
        'startColumn' => 5,
        'endColumn' => 5,
        'couldThrow' => false,
        'isClosure' => false,
        'isGenerator' => false,
        'isVariadic' => false,
        'modifiers' => 1,
        'namespace' => 'Illuminate\\Database\\Schema',
        'declaringClassName' => 'Illuminate\\Database\\Schema\\Blueprint',
        'implementingClassName' => 'Illuminate\\Database\\Schema\\Blueprint',
        'currentClassName' => 'Illuminate\\Database\\Schema\\Blueprint',
        'aliasName' => NULL,
      ),
      'foreignId' => 
      array (
        'name' => 'foreignId',
        'parameters' => 
        array (
          'column' => 
          array (
            'name' => 'column',
            'default' => NULL,
            'type' => NULL,
            'isVariadic' => false,
            'byRef' => false,
            'isPromoted' => false,
            'attributes' => 
            array (
            ),
            'startLine' => 1037,
            'endLine' => 1037,
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
 * Create a new unsigned big integer column on the table (8-byte, 0 to 18,446,744,073,709,551,615).
 *
 * @param  string  $column
 * @return \\Illuminate\\Database\\Schema\\ForeignIdColumnDefinition
 */',
        'startLine' => 1037,
        'endLine' => 1045,
        'startColumn' => 5,
        'endColumn' => 5,
        'couldThrow' => false,
        'isClosure' => false,
        'isGenerator' => false,
        'isVariadic' => false,
        'modifiers' => 1,
        'namespace' => 'Illuminate\\Database\\Schema',
        'declaringClassName' => 'Illuminate\\Database\\Schema\\Blueprint',
        'implementingClassName' => 'Illuminate\\Database\\Schema\\Blueprint',
        'currentClassName' => 'Illuminate\\Database\\Schema\\Blueprint',
        'aliasName' => NULL,
      ),
      'foreignIdFor' => 
      array (
        'name' => 'foreignIdFor',
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
            'startLine' => 1054,
            'endLine' => 1054,
            'startColumn' => 34,
            'endColumn' => 39,
            'parameterIndex' => 0,
            'isOptional' => false,
          ),
          'column' => 
          array (
            'name' => 'column',
            'default' => 
            array (
              'code' => 'null',
              'attributes' => 
              array (
                'startLine' => 1054,
                'endLine' => 1054,
                'startTokenPos' => 3711,
                'startFilePos' => 29988,
                'endTokenPos' => 3711,
                'endFilePos' => 29991,
              ),
            ),
            'type' => NULL,
            'isVariadic' => false,
            'byRef' => false,
            'isPromoted' => false,
            'attributes' => 
            array (
            ),
            'startLine' => 1054,
            'endLine' => 1054,
            'startColumn' => 42,
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
 * Create a foreign ID column for the given model.
 *
 * @param  \\Illuminate\\Database\\Eloquent\\Model|string  $model
 * @param  string|null  $column
 * @return \\Illuminate\\Database\\Schema\\ForeignIdColumnDefinition
 */',
        'startLine' => 1054,
        'endLine' => 1077,
        'startColumn' => 5,
        'endColumn' => 5,
        'couldThrow' => false,
        'isClosure' => false,
        'isGenerator' => false,
        'isVariadic' => false,
        'modifiers' => 1,
        'namespace' => 'Illuminate\\Database\\Schema',
        'declaringClassName' => 'Illuminate\\Database\\Schema\\Blueprint',
        'implementingClassName' => 'Illuminate\\Database\\Schema\\Blueprint',
        'currentClassName' => 'Illuminate\\Database\\Schema\\Blueprint',
        'aliasName' => NULL,
      ),
      'foreignUuidFor' => 
      array (
        'name' => 'foreignUuidFor',
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
            'startLine' => 1086,
            'endLine' => 1086,
            'startColumn' => 36,
            'endColumn' => 41,
            'parameterIndex' => 0,
            'isOptional' => false,
          ),
          'column' => 
          array (
            'name' => 'column',
            'default' => 
            array (
              'code' => 'null',
              'attributes' => 
              array (
                'startLine' => 1086,
                'endLine' => 1086,
                'startTokenPos' => 3903,
                'startFilePos' => 31031,
                'endTokenPos' => 3903,
                'endFilePos' => 31034,
              ),
            ),
            'type' => NULL,
            'isVariadic' => false,
            'byRef' => false,
            'isPromoted' => false,
            'attributes' => 
            array (
            ),
            'startLine' => 1086,
            'endLine' => 1086,
            'startColumn' => 44,
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
 * Create a foreign UUID column for the given model.
 *
 * @param  \\Illuminate\\Database\\Eloquent\\Model|string  $model
 * @param  string|null  $column
 * @return \\Illuminate\\Database\\Schema\\ForeignIdColumnDefinition
 */',
        'startLine' => 1086,
        'endLine' => 1095,
        'startColumn' => 5,
        'endColumn' => 5,
        'couldThrow' => false,
        'isClosure' => false,
        'isGenerator' => false,
        'isVariadic' => false,
        'modifiers' => 1,
        'namespace' => 'Illuminate\\Database\\Schema',
        'declaringClassName' => 'Illuminate\\Database\\Schema\\Blueprint',
        'implementingClassName' => 'Illuminate\\Database\\Schema\\Blueprint',
        'currentClassName' => 'Illuminate\\Database\\Schema\\Blueprint',
        'aliasName' => NULL,
      ),
      'foreignUlidFor' => 
      array (
        'name' => 'foreignUlidFor',
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
            'startLine' => 1104,
            'endLine' => 1104,
            'startColumn' => 36,
            'endColumn' => 41,
            'parameterIndex' => 0,
            'isOptional' => false,
          ),
          'column' => 
          array (
            'name' => 'column',
            'default' => 
            array (
              'code' => 'null',
              'attributes' => 
              array (
                'startLine' => 1104,
                'endLine' => 1104,
                'startTokenPos' => 3986,
                'startFilePos' => 31600,
                'endTokenPos' => 3986,
                'endFilePos' => 31603,
              ),
            ),
            'type' => NULL,
            'isVariadic' => false,
            'byRef' => false,
            'isPromoted' => false,
            'attributes' => 
            array (
            ),
            'startLine' => 1104,
            'endLine' => 1104,
            'startColumn' => 44,
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
 * Create a foreign ULID column for the given model.
 *
 * @param  \\Illuminate\\Database\\Eloquent\\Model|string  $model
 * @param  string|null  $column
 * @return \\Illuminate\\Database\\Schema\\ForeignIdColumnDefinition
 */',
        'startLine' => 1104,
        'endLine' => 1113,
        'startColumn' => 5,
        'endColumn' => 5,
        'couldThrow' => false,
        'isClosure' => false,
        'isGenerator' => false,
        'isVariadic' => false,
        'modifiers' => 1,
        'namespace' => 'Illuminate\\Database\\Schema',
        'declaringClassName' => 'Illuminate\\Database\\Schema\\Blueprint',
        'implementingClassName' => 'Illuminate\\Database\\Schema\\Blueprint',
        'currentClassName' => 'Illuminate\\Database\\Schema\\Blueprint',
        'aliasName' => NULL,
      ),
      'float' => 
      array (
        'name' => 'float',
        'parameters' => 
        array (
          'column' => 
          array (
            'name' => 'column',
            'default' => NULL,
            'type' => NULL,
            'isVariadic' => false,
            'byRef' => false,
            'isPromoted' => false,
            'attributes' => 
            array (
            ),
            'startLine' => 1122,
            'endLine' => 1122,
            'startColumn' => 27,
            'endColumn' => 33,
            'parameterIndex' => 0,
            'isOptional' => false,
          ),
          'precision' => 
          array (
            'name' => 'precision',
            'default' => 
            array (
              'code' => '53',
              'attributes' => 
              array (
                'startLine' => 1122,
                'endLine' => 1122,
                'startTokenPos' => 4069,
                'startFilePos' => 32105,
                'endTokenPos' => 4069,
                'endFilePos' => 32106,
              ),
            ),
            'type' => NULL,
            'isVariadic' => false,
            'byRef' => false,
            'isPromoted' => false,
            'attributes' => 
            array (
            ),
            'startLine' => 1122,
            'endLine' => 1122,
            'startColumn' => 36,
            'endColumn' => 50,
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
 * Create a new float column on the table.
 *
 * @param  string  $column
 * @param  int  $precision
 * @return \\Illuminate\\Database\\Schema\\ColumnDefinition
 */',
        'startLine' => 1122,
        'endLine' => 1125,
        'startColumn' => 5,
        'endColumn' => 5,
        'couldThrow' => false,
        'isClosure' => false,
        'isGenerator' => false,
        'isVariadic' => false,
        'modifiers' => 1,
        'namespace' => 'Illuminate\\Database\\Schema',
        'declaringClassName' => 'Illuminate\\Database\\Schema\\Blueprint',
        'implementingClassName' => 'Illuminate\\Database\\Schema\\Blueprint',
        'currentClassName' => 'Illuminate\\Database\\Schema\\Blueprint',
        'aliasName' => NULL,
      ),
      'double' => 
      array (
        'name' => 'double',
        'parameters' => 
        array (
          'column' => 
          array (
            'name' => 'column',
            'default' => NULL,
            'type' => NULL,
            'isVariadic' => false,
            'byRef' => false,
            'isPromoted' => false,
            'attributes' => 
            array (
            ),
            'startLine' => 1133,
            'endLine' => 1133,
            'startColumn' => 28,
            'endColumn' => 34,
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
 * Create a new double column on the table.
 *
 * @param  string  $column
 * @return \\Illuminate\\Database\\Schema\\ColumnDefinition
 */',
        'startLine' => 1133,
        'endLine' => 1136,
        'startColumn' => 5,
        'endColumn' => 5,
        'couldThrow' => false,
        'isClosure' => false,
        'isGenerator' => false,
        'isVariadic' => false,
        'modifiers' => 1,
        'namespace' => 'Illuminate\\Database\\Schema',
        'declaringClassName' => 'Illuminate\\Database\\Schema\\Blueprint',
        'implementingClassName' => 'Illuminate\\Database\\Schema\\Blueprint',
        'currentClassName' => 'Illuminate\\Database\\Schema\\Blueprint',
        'aliasName' => NULL,
      ),
      'decimal' => 
      array (
        'name' => 'decimal',
        'parameters' => 
        array (
          'column' => 
          array (
            'name' => 'column',
            'default' => NULL,
            'type' => NULL,
            'isVariadic' => false,
            'byRef' => false,
            'isPromoted' => false,
            'attributes' => 
            array (
            ),
            'startLine' => 1146,
            'endLine' => 1146,
            'startColumn' => 29,
            'endColumn' => 35,
            'parameterIndex' => 0,
            'isOptional' => false,
          ),
          'total' => 
          array (
            'name' => 'total',
            'default' => 
            array (
              'code' => '8',
              'attributes' => 
              array (
                'startLine' => 1146,
                'endLine' => 1146,
                'startTokenPos' => 4141,
                'startFilePos' => 32729,
                'endTokenPos' => 4141,
                'endFilePos' => 32729,
              ),
            ),
            'type' => NULL,
            'isVariadic' => false,
            'byRef' => false,
            'isPromoted' => false,
            'attributes' => 
            array (
            ),
            'startLine' => 1146,
            'endLine' => 1146,
            'startColumn' => 38,
            'endColumn' => 47,
            'parameterIndex' => 1,
            'isOptional' => true,
          ),
          'places' => 
          array (
            'name' => 'places',
            'default' => 
            array (
              'code' => '2',
              'attributes' => 
              array (
                'startLine' => 1146,
                'endLine' => 1146,
                'startTokenPos' => 4148,
                'startFilePos' => 32742,
                'endTokenPos' => 4148,
                'endFilePos' => 32742,
              ),
            ),
            'type' => NULL,
            'isVariadic' => false,
            'byRef' => false,
            'isPromoted' => false,
            'attributes' => 
            array (
            ),
            'startLine' => 1146,
            'endLine' => 1146,
            'startColumn' => 50,
            'endColumn' => 60,
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
 * Create a new decimal column on the table.
 *
 * @param  string  $column
 * @param  int  $total
 * @param  int  $places
 * @return \\Illuminate\\Database\\Schema\\ColumnDefinition
 */',
        'startLine' => 1146,
        'endLine' => 1149,
        'startColumn' => 5,
        'endColumn' => 5,
        'couldThrow' => false,
        'isClosure' => false,
        'isGenerator' => false,
        'isVariadic' => false,
        'modifiers' => 1,
        'namespace' => 'Illuminate\\Database\\Schema',
        'declaringClassName' => 'Illuminate\\Database\\Schema\\Blueprint',
        'implementingClassName' => 'Illuminate\\Database\\Schema\\Blueprint',
        'currentClassName' => 'Illuminate\\Database\\Schema\\Blueprint',
        'aliasName' => NULL,
      ),
      'boolean' => 
      array (
        'name' => 'boolean',
        'parameters' => 
        array (
          'column' => 
          array (
            'name' => 'column',
            'default' => NULL,
            'type' => NULL,
            'isVariadic' => false,
            'byRef' => false,
            'isPromoted' => false,
            'attributes' => 
            array (
            ),
            'startLine' => 1157,
            'endLine' => 1157,
            'startColumn' => 29,
            'endColumn' => 35,
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
 * Create a new boolean column on the table.
 *
 * @param  string  $column
 * @return \\Illuminate\\Database\\Schema\\ColumnDefinition
 */',
        'startLine' => 1157,
        'endLine' => 1160,
        'startColumn' => 5,
        'endColumn' => 5,
        'couldThrow' => false,
        'isClosure' => false,
        'isGenerator' => false,
        'isVariadic' => false,
        'modifiers' => 1,
        'namespace' => 'Illuminate\\Database\\Schema',
        'declaringClassName' => 'Illuminate\\Database\\Schema\\Blueprint',
        'implementingClassName' => 'Illuminate\\Database\\Schema\\Blueprint',
        'currentClassName' => 'Illuminate\\Database\\Schema\\Blueprint',
        'aliasName' => NULL,
      ),
      'enum' => 
      array (
        'name' => 'enum',
        'parameters' => 
        array (
          'column' => 
          array (
            'name' => 'column',
            'default' => NULL,
            'type' => NULL,
            'isVariadic' => false,
            'byRef' => false,
            'isPromoted' => false,
            'attributes' => 
            array (
            ),
            'startLine' => 1169,
            'endLine' => 1169,
            'startColumn' => 26,
            'endColumn' => 32,
            'parameterIndex' => 0,
            'isOptional' => false,
          ),
          'allowed' => 
          array (
            'name' => 'allowed',
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
            'startLine' => 1169,
            'endLine' => 1169,
            'startColumn' => 35,
            'endColumn' => 48,
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
 * Create a new enum column on the table.
 *
 * @param  string  $column
 * @param  array  $allowed
 * @return \\Illuminate\\Database\\Schema\\ColumnDefinition
 */',
        'startLine' => 1169,
        'endLine' => 1174,
        'startColumn' => 5,
        'endColumn' => 5,
        'couldThrow' => false,
        'isClosure' => false,
        'isGenerator' => false,
        'isVariadic' => false,
        'modifiers' => 1,
        'namespace' => 'Illuminate\\Database\\Schema',
        'declaringClassName' => 'Illuminate\\Database\\Schema\\Blueprint',
        'implementingClassName' => 'Illuminate\\Database\\Schema\\Blueprint',
        'currentClassName' => 'Illuminate\\Database\\Schema\\Blueprint',
        'aliasName' => NULL,
      ),
      'set' => 
      array (
        'name' => 'set',
        'parameters' => 
        array (
          'column' => 
          array (
            'name' => 'column',
            'default' => NULL,
            'type' => NULL,
            'isVariadic' => false,
            'byRef' => false,
            'isPromoted' => false,
            'attributes' => 
            array (
            ),
            'startLine' => 1183,
            'endLine' => 1183,
            'startColumn' => 25,
            'endColumn' => 31,
            'parameterIndex' => 0,
            'isOptional' => false,
          ),
          'allowed' => 
          array (
            'name' => 'allowed',
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
            'startLine' => 1183,
            'endLine' => 1183,
            'startColumn' => 34,
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
 * Create a new set column on the table.
 *
 * @param  string  $column
 * @param  array  $allowed
 * @return \\Illuminate\\Database\\Schema\\ColumnDefinition
 */',
        'startLine' => 1183,
        'endLine' => 1186,
        'startColumn' => 5,
        'endColumn' => 5,
        'couldThrow' => false,
        'isClosure' => false,
        'isGenerator' => false,
        'isVariadic' => false,
        'modifiers' => 1,
        'namespace' => 'Illuminate\\Database\\Schema',
        'declaringClassName' => 'Illuminate\\Database\\Schema\\Blueprint',
        'implementingClassName' => 'Illuminate\\Database\\Schema\\Blueprint',
        'currentClassName' => 'Illuminate\\Database\\Schema\\Blueprint',
        'aliasName' => NULL,
      ),
      'json' => 
      array (
        'name' => 'json',
        'parameters' => 
        array (
          'column' => 
          array (
            'name' => 'column',
            'default' => NULL,
            'type' => NULL,
            'isVariadic' => false,
            'byRef' => false,
            'isPromoted' => false,
            'attributes' => 
            array (
            ),
            'startLine' => 1194,
            'endLine' => 1194,
            'startColumn' => 26,
            'endColumn' => 32,
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
 * Create a new json column on the table.
 *
 * @param  string  $column
 * @return \\Illuminate\\Database\\Schema\\ColumnDefinition
 */',
        'startLine' => 1194,
        'endLine' => 1197,
        'startColumn' => 5,
        'endColumn' => 5,
        'couldThrow' => false,
        'isClosure' => false,
        'isGenerator' => false,
        'isVariadic' => false,
        'modifiers' => 1,
        'namespace' => 'Illuminate\\Database\\Schema',
        'declaringClassName' => 'Illuminate\\Database\\Schema\\Blueprint',
        'implementingClassName' => 'Illuminate\\Database\\Schema\\Blueprint',
        'currentClassName' => 'Illuminate\\Database\\Schema\\Blueprint',
        'aliasName' => NULL,
      ),
      'jsonb' => 
      array (
        'name' => 'jsonb',
        'parameters' => 
        array (
          'column' => 
          array (
            'name' => 'column',
            'default' => NULL,
            'type' => NULL,
            'isVariadic' => false,
            'byRef' => false,
            'isPromoted' => false,
            'attributes' => 
            array (
            ),
            'startLine' => 1205,
            'endLine' => 1205,
            'startColumn' => 27,
            'endColumn' => 33,
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
 * Create a new jsonb column on the table.
 *
 * @param  string  $column
 * @return \\Illuminate\\Database\\Schema\\ColumnDefinition
 */',
        'startLine' => 1205,
        'endLine' => 1208,
        'startColumn' => 5,
        'endColumn' => 5,
        'couldThrow' => false,
        'isClosure' => false,
        'isGenerator' => false,
        'isVariadic' => false,
        'modifiers' => 1,
        'namespace' => 'Illuminate\\Database\\Schema',
        'declaringClassName' => 'Illuminate\\Database\\Schema\\Blueprint',
        'implementingClassName' => 'Illuminate\\Database\\Schema\\Blueprint',
        'currentClassName' => 'Illuminate\\Database\\Schema\\Blueprint',
        'aliasName' => NULL,
      ),
      'date' => 
      array (
        'name' => 'date',
        'parameters' => 
        array (
          'column' => 
          array (
            'name' => 'column',
            'default' => NULL,
            'type' => NULL,
            'isVariadic' => false,
            'byRef' => false,
            'isPromoted' => false,
            'attributes' => 
            array (
            ),
            'startLine' => 1216,
            'endLine' => 1216,
            'startColumn' => 26,
            'endColumn' => 32,
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
 * Create a new date column on the table.
 *
 * @param  string  $column
 * @return \\Illuminate\\Database\\Schema\\ColumnDefinition
 */',
        'startLine' => 1216,
        'endLine' => 1219,
        'startColumn' => 5,
        'endColumn' => 5,
        'couldThrow' => false,
        'isClosure' => false,
        'isGenerator' => false,
        'isVariadic' => false,
        'modifiers' => 1,
        'namespace' => 'Illuminate\\Database\\Schema',
        'declaringClassName' => 'Illuminate\\Database\\Schema\\Blueprint',
        'implementingClassName' => 'Illuminate\\Database\\Schema\\Blueprint',
        'currentClassName' => 'Illuminate\\Database\\Schema\\Blueprint',
        'aliasName' => NULL,
      ),
      'dateTime' => 
      array (
        'name' => 'dateTime',
        'parameters' => 
        array (
          'column' => 
          array (
            'name' => 'column',
            'default' => NULL,
            'type' => NULL,
            'isVariadic' => false,
            'byRef' => false,
            'isPromoted' => false,
            'attributes' => 
            array (
            ),
            'startLine' => 1228,
            'endLine' => 1228,
            'startColumn' => 30,
            'endColumn' => 36,
            'parameterIndex' => 0,
            'isOptional' => false,
          ),
          'precision' => 
          array (
            'name' => 'precision',
            'default' => 
            array (
              'code' => 'null',
              'attributes' => 
              array (
                'startLine' => 1228,
                'endLine' => 1228,
                'startTokenPos' => 4419,
                'startFilePos' => 34876,
                'endTokenPos' => 4419,
                'endFilePos' => 34879,
              ),
            ),
            'type' => NULL,
            'isVariadic' => false,
            'byRef' => false,
            'isPromoted' => false,
            'attributes' => 
            array (
            ),
            'startLine' => 1228,
            'endLine' => 1228,
            'startColumn' => 39,
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
 * Create a new date-time column on the table.
 *
 * @param  string  $column
 * @param  int|null  $precision
 * @return \\Illuminate\\Database\\Schema\\ColumnDefinition
 */',
        'startLine' => 1228,
        'endLine' => 1233,
        'startColumn' => 5,
        'endColumn' => 5,
        'couldThrow' => false,
        'isClosure' => false,
        'isGenerator' => false,
        'isVariadic' => false,
        'modifiers' => 1,
        'namespace' => 'Illuminate\\Database\\Schema',
        'declaringClassName' => 'Illuminate\\Database\\Schema\\Blueprint',
        'implementingClassName' => 'Illuminate\\Database\\Schema\\Blueprint',
        'currentClassName' => 'Illuminate\\Database\\Schema\\Blueprint',
        'aliasName' => NULL,
      ),
      'dateTimeTz' => 
      array (
        'name' => 'dateTimeTz',
        'parameters' => 
        array (
          'column' => 
          array (
            'name' => 'column',
            'default' => NULL,
            'type' => NULL,
            'isVariadic' => false,
            'byRef' => false,
            'isPromoted' => false,
            'attributes' => 
            array (
            ),
            'startLine' => 1242,
            'endLine' => 1242,
            'startColumn' => 32,
            'endColumn' => 38,
            'parameterIndex' => 0,
            'isOptional' => false,
          ),
          'precision' => 
          array (
            'name' => 'precision',
            'default' => 
            array (
              'code' => 'null',
              'attributes' => 
              array (
                'startLine' => 1242,
                'endLine' => 1242,
                'startTokenPos' => 4474,
                'startFilePos' => 35304,
                'endTokenPos' => 4474,
                'endFilePos' => 35307,
              ),
            ),
            'type' => NULL,
            'isVariadic' => false,
            'byRef' => false,
            'isPromoted' => false,
            'attributes' => 
            array (
            ),
            'startLine' => 1242,
            'endLine' => 1242,
            'startColumn' => 41,
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
 * Create a new date-time column (with time zone) on the table.
 *
 * @param  string  $column
 * @param  int|null  $precision
 * @return \\Illuminate\\Database\\Schema\\ColumnDefinition
 */',
        'startLine' => 1242,
        'endLine' => 1247,
        'startColumn' => 5,
        'endColumn' => 5,
        'couldThrow' => false,
        'isClosure' => false,
        'isGenerator' => false,
        'isVariadic' => false,
        'modifiers' => 1,
        'namespace' => 'Illuminate\\Database\\Schema',
        'declaringClassName' => 'Illuminate\\Database\\Schema\\Blueprint',
        'implementingClassName' => 'Illuminate\\Database\\Schema\\Blueprint',
        'currentClassName' => 'Illuminate\\Database\\Schema\\Blueprint',
        'aliasName' => NULL,
      ),
      'time' => 
      array (
        'name' => 'time',
        'parameters' => 
        array (
          'column' => 
          array (
            'name' => 'column',
            'default' => NULL,
            'type' => NULL,
            'isVariadic' => false,
            'byRef' => false,
            'isPromoted' => false,
            'attributes' => 
            array (
            ),
            'startLine' => 1256,
            'endLine' => 1256,
            'startColumn' => 26,
            'endColumn' => 32,
            'parameterIndex' => 0,
            'isOptional' => false,
          ),
          'precision' => 
          array (
            'name' => 'precision',
            'default' => 
            array (
              'code' => 'null',
              'attributes' => 
              array (
                'startLine' => 1256,
                'endLine' => 1256,
                'startTokenPos' => 4529,
                'startFilePos' => 35706,
                'endTokenPos' => 4529,
                'endFilePos' => 35709,
              ),
            ),
            'type' => NULL,
            'isVariadic' => false,
            'byRef' => false,
            'isPromoted' => false,
            'attributes' => 
            array (
            ),
            'startLine' => 1256,
            'endLine' => 1256,
            'startColumn' => 35,
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
 * Create a new time column on the table.
 *
 * @param  string  $column
 * @param  int|null  $precision
 * @return \\Illuminate\\Database\\Schema\\ColumnDefinition
 */',
        'startLine' => 1256,
        'endLine' => 1261,
        'startColumn' => 5,
        'endColumn' => 5,
        'couldThrow' => false,
        'isClosure' => false,
        'isGenerator' => false,
        'isVariadic' => false,
        'modifiers' => 1,
        'namespace' => 'Illuminate\\Database\\Schema',
        'declaringClassName' => 'Illuminate\\Database\\Schema\\Blueprint',
        'implementingClassName' => 'Illuminate\\Database\\Schema\\Blueprint',
        'currentClassName' => 'Illuminate\\Database\\Schema\\Blueprint',
        'aliasName' => NULL,
      ),
      'timeTz' => 
      array (
        'name' => 'timeTz',
        'parameters' => 
        array (
          'column' => 
          array (
            'name' => 'column',
            'default' => NULL,
            'type' => NULL,
            'isVariadic' => false,
            'byRef' => false,
            'isPromoted' => false,
            'attributes' => 
            array (
            ),
            'startLine' => 1270,
            'endLine' => 1270,
            'startColumn' => 28,
            'endColumn' => 34,
            'parameterIndex' => 0,
            'isOptional' => false,
          ),
          'precision' => 
          array (
            'name' => 'precision',
            'default' => 
            array (
              'code' => 'null',
              'attributes' => 
              array (
                'startLine' => 1270,
                'endLine' => 1270,
                'startTokenPos' => 4584,
                'startFilePos' => 36121,
                'endTokenPos' => 4584,
                'endFilePos' => 36124,
              ),
            ),
            'type' => NULL,
            'isVariadic' => false,
            'byRef' => false,
            'isPromoted' => false,
            'attributes' => 
            array (
            ),
            'startLine' => 1270,
            'endLine' => 1270,
            'startColumn' => 37,
            'endColumn' => 53,
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
 * Create a new time column (with time zone) on the table.
 *
 * @param  string  $column
 * @param  int|null  $precision
 * @return \\Illuminate\\Database\\Schema\\ColumnDefinition
 */',
        'startLine' => 1270,
        'endLine' => 1275,
        'startColumn' => 5,
        'endColumn' => 5,
        'couldThrow' => false,
        'isClosure' => false,
        'isGenerator' => false,
        'isVariadic' => false,
        'modifiers' => 1,
        'namespace' => 'Illuminate\\Database\\Schema',
        'declaringClassName' => 'Illuminate\\Database\\Schema\\Blueprint',
        'implementingClassName' => 'Illuminate\\Database\\Schema\\Blueprint',
        'currentClassName' => 'Illuminate\\Database\\Schema\\Blueprint',
        'aliasName' => NULL,
      ),
      'timestamp' => 
      array (
        'name' => 'timestamp',
        'parameters' => 
        array (
          'column' => 
          array (
            'name' => 'column',
            'default' => NULL,
            'type' => NULL,
            'isVariadic' => false,
            'byRef' => false,
            'isPromoted' => false,
            'attributes' => 
            array (
            ),
            'startLine' => 1284,
            'endLine' => 1284,
            'startColumn' => 31,
            'endColumn' => 37,
            'parameterIndex' => 0,
            'isOptional' => false,
          ),
          'precision' => 
          array (
            'name' => 'precision',
            'default' => 
            array (
              'code' => 'null',
              'attributes' => 
              array (
                'startLine' => 1284,
                'endLine' => 1284,
                'startTokenPos' => 4639,
                'startFilePos' => 36529,
                'endTokenPos' => 4639,
                'endFilePos' => 36532,
              ),
            ),
            'type' => NULL,
            'isVariadic' => false,
            'byRef' => false,
            'isPromoted' => false,
            'attributes' => 
            array (
            ),
            'startLine' => 1284,
            'endLine' => 1284,
            'startColumn' => 40,
            'endColumn' => 56,
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
 * Create a new timestamp column on the table.
 *
 * @param  string  $column
 * @param  int|null  $precision
 * @return \\Illuminate\\Database\\Schema\\ColumnDefinition
 */',
        'startLine' => 1284,
        'endLine' => 1289,
        'startColumn' => 5,
        'endColumn' => 5,
        'couldThrow' => false,
        'isClosure' => false,
        'isGenerator' => false,
        'isVariadic' => false,
        'modifiers' => 1,
        'namespace' => 'Illuminate\\Database\\Schema',
        'declaringClassName' => 'Illuminate\\Database\\Schema\\Blueprint',
        'implementingClassName' => 'Illuminate\\Database\\Schema\\Blueprint',
        'currentClassName' => 'Illuminate\\Database\\Schema\\Blueprint',
        'aliasName' => NULL,
      ),
      'timestampTz' => 
      array (
        'name' => 'timestampTz',
        'parameters' => 
        array (
          'column' => 
          array (
            'name' => 'column',
            'default' => NULL,
            'type' => NULL,
            'isVariadic' => false,
            'byRef' => false,
            'isPromoted' => false,
            'attributes' => 
            array (
            ),
            'startLine' => 1298,
            'endLine' => 1298,
            'startColumn' => 33,
            'endColumn' => 39,
            'parameterIndex' => 0,
            'isOptional' => false,
          ),
          'precision' => 
          array (
            'name' => 'precision',
            'default' => 
            array (
              'code' => 'null',
              'attributes' => 
              array (
                'startLine' => 1298,
                'endLine' => 1298,
                'startTokenPos' => 4694,
                'startFilePos' => 36959,
                'endTokenPos' => 4694,
                'endFilePos' => 36962,
              ),
            ),
            'type' => NULL,
            'isVariadic' => false,
            'byRef' => false,
            'isPromoted' => false,
            'attributes' => 
            array (
            ),
            'startLine' => 1298,
            'endLine' => 1298,
            'startColumn' => 42,
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
 * Create a new timestamp (with time zone) column on the table.
 *
 * @param  string  $column
 * @param  int|null  $precision
 * @return \\Illuminate\\Database\\Schema\\ColumnDefinition
 */',
        'startLine' => 1298,
        'endLine' => 1303,
        'startColumn' => 5,
        'endColumn' => 5,
        'couldThrow' => false,
        'isClosure' => false,
        'isGenerator' => false,
        'isVariadic' => false,
        'modifiers' => 1,
        'namespace' => 'Illuminate\\Database\\Schema',
        'declaringClassName' => 'Illuminate\\Database\\Schema\\Blueprint',
        'implementingClassName' => 'Illuminate\\Database\\Schema\\Blueprint',
        'currentClassName' => 'Illuminate\\Database\\Schema\\Blueprint',
        'aliasName' => NULL,
      ),
      'timestamps' => 
      array (
        'name' => 'timestamps',
        'parameters' => 
        array (
          'precision' => 
          array (
            'name' => 'precision',
            'default' => 
            array (
              'code' => 'null',
              'attributes' => 
              array (
                'startLine' => 1311,
                'endLine' => 1311,
                'startTokenPos' => 4746,
                'startFilePos' => 37384,
                'endTokenPos' => 4746,
                'endFilePos' => 37387,
              ),
            ),
            'type' => NULL,
            'isVariadic' => false,
            'byRef' => false,
            'isPromoted' => false,
            'attributes' => 
            array (
            ),
            'startLine' => 1311,
            'endLine' => 1311,
            'startColumn' => 32,
            'endColumn' => 48,
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
 * Add nullable creation and update timestamps to the table.
 *
 * @param  int|null  $precision
 * @return \\Illuminate\\Support\\Collection<int, \\Illuminate\\Database\\Schema\\ColumnDefinition>
 */',
        'startLine' => 1311,
        'endLine' => 1317,
        'startColumn' => 5,
        'endColumn' => 5,
        'couldThrow' => false,
        'isClosure' => false,
        'isGenerator' => false,
        'isVariadic' => false,
        'modifiers' => 1,
        'namespace' => 'Illuminate\\Database\\Schema',
        'declaringClassName' => 'Illuminate\\Database\\Schema\\Blueprint',
        'implementingClassName' => 'Illuminate\\Database\\Schema\\Blueprint',
        'currentClassName' => 'Illuminate\\Database\\Schema\\Blueprint',
        'aliasName' => NULL,
      ),
      'nullableTimestamps' => 
      array (
        'name' => 'nullableTimestamps',
        'parameters' => 
        array (
          'precision' => 
          array (
            'name' => 'precision',
            'default' => 
            array (
              'code' => 'null',
              'attributes' => 
              array (
                'startLine' => 1327,
                'endLine' => 1327,
                'startTokenPos' => 4807,
                'startFilePos' => 37900,
                'endTokenPos' => 4807,
                'endFilePos' => 37903,
              ),
            ),
            'type' => NULL,
            'isVariadic' => false,
            'byRef' => false,
            'isPromoted' => false,
            'attributes' => 
            array (
            ),
            'startLine' => 1327,
            'endLine' => 1327,
            'startColumn' => 40,
            'endColumn' => 56,
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
 * Add nullable creation and update timestamps to the table.
 *
 * Alias for self::timestamps().
 *
 * @param  int|null  $precision
 * @return \\Illuminate\\Support\\Collection<int, \\Illuminate\\Database\\Schema\\ColumnDefinition>
 */',
        'startLine' => 1327,
        'endLine' => 1330,
        'startColumn' => 5,
        'endColumn' => 5,
        'couldThrow' => false,
        'isClosure' => false,
        'isGenerator' => false,
        'isVariadic' => false,
        'modifiers' => 1,
        'namespace' => 'Illuminate\\Database\\Schema',
        'declaringClassName' => 'Illuminate\\Database\\Schema\\Blueprint',
        'implementingClassName' => 'Illuminate\\Database\\Schema\\Blueprint',
        'currentClassName' => 'Illuminate\\Database\\Schema\\Blueprint',
        'aliasName' => NULL,
      ),
      'timestampsTz' => 
      array (
        'name' => 'timestampsTz',
        'parameters' => 
        array (
          'precision' => 
          array (
            'name' => 'precision',
            'default' => 
            array (
              'code' => 'null',
              'attributes' => 
              array (
                'startLine' => 1338,
                'endLine' => 1338,
                'startTokenPos' => 4836,
                'startFilePos' => 38241,
                'endTokenPos' => 4836,
                'endFilePos' => 38244,
              ),
            ),
            'type' => NULL,
            'isVariadic' => false,
            'byRef' => false,
            'isPromoted' => false,
            'attributes' => 
            array (
            ),
            'startLine' => 1338,
            'endLine' => 1338,
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
 * Add nullable creation and update timestampTz columns to the table.
 *
 * @param  int|null  $precision
 * @return \\Illuminate\\Support\\Collection<int, \\Illuminate\\Database\\Schema\\ColumnDefinition>
 */',
        'startLine' => 1338,
        'endLine' => 1344,
        'startColumn' => 5,
        'endColumn' => 5,
        'couldThrow' => false,
        'isClosure' => false,
        'isGenerator' => false,
        'isVariadic' => false,
        'modifiers' => 1,
        'namespace' => 'Illuminate\\Database\\Schema',
        'declaringClassName' => 'Illuminate\\Database\\Schema\\Blueprint',
        'implementingClassName' => 'Illuminate\\Database\\Schema\\Blueprint',
        'currentClassName' => 'Illuminate\\Database\\Schema\\Blueprint',
        'aliasName' => NULL,
      ),
      'nullableTimestampsTz' => 
      array (
        'name' => 'nullableTimestampsTz',
        'parameters' => 
        array (
          'precision' => 
          array (
            'name' => 'precision',
            'default' => 
            array (
              'code' => 'null',
              'attributes' => 
              array (
                'startLine' => 1354,
                'endLine' => 1354,
                'startTokenPos' => 4897,
                'startFilePos' => 38774,
                'endTokenPos' => 4897,
                'endFilePos' => 38777,
              ),
            ),
            'type' => NULL,
            'isVariadic' => false,
            'byRef' => false,
            'isPromoted' => false,
            'attributes' => 
            array (
            ),
            'startLine' => 1354,
            'endLine' => 1354,
            'startColumn' => 42,
            'endColumn' => 58,
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
 * Add nullable creation and update timestampTz columns to the table.
 *
 * Alias for self::timestampsTz().
 *
 * @param  int|null  $precision
 * @return \\Illuminate\\Support\\Collection<int, \\Illuminate\\Database\\Schema\\ColumnDefinition>
 */',
        'startLine' => 1354,
        'endLine' => 1357,
        'startColumn' => 5,
        'endColumn' => 5,
        'couldThrow' => false,
        'isClosure' => false,
        'isGenerator' => false,
        'isVariadic' => false,
        'modifiers' => 1,
        'namespace' => 'Illuminate\\Database\\Schema',
        'declaringClassName' => 'Illuminate\\Database\\Schema\\Blueprint',
        'implementingClassName' => 'Illuminate\\Database\\Schema\\Blueprint',
        'currentClassName' => 'Illuminate\\Database\\Schema\\Blueprint',
        'aliasName' => NULL,
      ),
      'datetimes' => 
      array (
        'name' => 'datetimes',
        'parameters' => 
        array (
          'precision' => 
          array (
            'name' => 'precision',
            'default' => 
            array (
              'code' => 'null',
              'attributes' => 
              array (
                'startLine' => 1365,
                'endLine' => 1365,
                'startTokenPos' => 4926,
                'startFilePos' => 39102,
                'endTokenPos' => 4926,
                'endFilePos' => 39105,
              ),
            ),
            'type' => NULL,
            'isVariadic' => false,
            'byRef' => false,
            'isPromoted' => false,
            'attributes' => 
            array (
            ),
            'startLine' => 1365,
            'endLine' => 1365,
            'startColumn' => 31,
            'endColumn' => 47,
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
 * Add creation and update datetime columns to the table.
 *
 * @param  int|null  $precision
 * @return \\Illuminate\\Support\\Collection<int, \\Illuminate\\Database\\Schema\\ColumnDefinition>
 */',
        'startLine' => 1365,
        'endLine' => 1371,
        'startColumn' => 5,
        'endColumn' => 5,
        'couldThrow' => false,
        'isClosure' => false,
        'isGenerator' => false,
        'isVariadic' => false,
        'modifiers' => 1,
        'namespace' => 'Illuminate\\Database\\Schema',
        'declaringClassName' => 'Illuminate\\Database\\Schema\\Blueprint',
        'implementingClassName' => 'Illuminate\\Database\\Schema\\Blueprint',
        'currentClassName' => 'Illuminate\\Database\\Schema\\Blueprint',
        'aliasName' => NULL,
      ),
      'softDeletes' => 
      array (
        'name' => 'softDeletes',
        'parameters' => 
        array (
          'column' => 
          array (
            'name' => 'column',
            'default' => 
            array (
              'code' => '\'deleted_at\'',
              'attributes' => 
              array (
                'startLine' => 1380,
                'endLine' => 1380,
                'startTokenPos' => 4987,
                'startFilePos' => 39542,
                'endTokenPos' => 4987,
                'endFilePos' => 39553,
              ),
            ),
            'type' => NULL,
            'isVariadic' => false,
            'byRef' => false,
            'isPromoted' => false,
            'attributes' => 
            array (
            ),
            'startLine' => 1380,
            'endLine' => 1380,
            'startColumn' => 33,
            'endColumn' => 54,
            'parameterIndex' => 0,
            'isOptional' => true,
          ),
          'precision' => 
          array (
            'name' => 'precision',
            'default' => 
            array (
              'code' => 'null',
              'attributes' => 
              array (
                'startLine' => 1380,
                'endLine' => 1380,
                'startTokenPos' => 4994,
                'startFilePos' => 39569,
                'endTokenPos' => 4994,
                'endFilePos' => 39572,
              ),
            ),
            'type' => NULL,
            'isVariadic' => false,
            'byRef' => false,
            'isPromoted' => false,
            'attributes' => 
            array (
            ),
            'startLine' => 1380,
            'endLine' => 1380,
            'startColumn' => 57,
            'endColumn' => 73,
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
 * Add a "deleted at" timestamp for the table.
 *
 * @param  string  $column
 * @param  int|null  $precision
 * @return \\Illuminate\\Database\\Schema\\ColumnDefinition
 */',
        'startLine' => 1380,
        'endLine' => 1383,
        'startColumn' => 5,
        'endColumn' => 5,
        'couldThrow' => false,
        'isClosure' => false,
        'isGenerator' => false,
        'isVariadic' => false,
        'modifiers' => 1,
        'namespace' => 'Illuminate\\Database\\Schema',
        'declaringClassName' => 'Illuminate\\Database\\Schema\\Blueprint',
        'implementingClassName' => 'Illuminate\\Database\\Schema\\Blueprint',
        'currentClassName' => 'Illuminate\\Database\\Schema\\Blueprint',
        'aliasName' => NULL,
      ),
      'softDeletesTz' => 
      array (
        'name' => 'softDeletesTz',
        'parameters' => 
        array (
          'column' => 
          array (
            'name' => 'column',
            'default' => 
            array (
              'code' => '\'deleted_at\'',
              'attributes' => 
              array (
                'startLine' => 1392,
                'endLine' => 1392,
                'startTokenPos' => 5030,
                'startFilePos' => 39901,
                'endTokenPos' => 5030,
                'endFilePos' => 39912,
              ),
            ),
            'type' => NULL,
            'isVariadic' => false,
            'byRef' => false,
            'isPromoted' => false,
            'attributes' => 
            array (
            ),
            'startLine' => 1392,
            'endLine' => 1392,
            'startColumn' => 35,
            'endColumn' => 56,
            'parameterIndex' => 0,
            'isOptional' => true,
          ),
          'precision' => 
          array (
            'name' => 'precision',
            'default' => 
            array (
              'code' => 'null',
              'attributes' => 
              array (
                'startLine' => 1392,
                'endLine' => 1392,
                'startTokenPos' => 5037,
                'startFilePos' => 39928,
                'endTokenPos' => 5037,
                'endFilePos' => 39931,
              ),
            ),
            'type' => NULL,
            'isVariadic' => false,
            'byRef' => false,
            'isPromoted' => false,
            'attributes' => 
            array (
            ),
            'startLine' => 1392,
            'endLine' => 1392,
            'startColumn' => 59,
            'endColumn' => 75,
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
 * Add a "deleted at" timestampTz for the table.
 *
 * @param  string  $column
 * @param  int|null  $precision
 * @return \\Illuminate\\Database\\Schema\\ColumnDefinition
 */',
        'startLine' => 1392,
        'endLine' => 1395,
        'startColumn' => 5,
        'endColumn' => 5,
        'couldThrow' => false,
        'isClosure' => false,
        'isGenerator' => false,
        'isVariadic' => false,
        'modifiers' => 1,
        'namespace' => 'Illuminate\\Database\\Schema',
        'declaringClassName' => 'Illuminate\\Database\\Schema\\Blueprint',
        'implementingClassName' => 'Illuminate\\Database\\Schema\\Blueprint',
        'currentClassName' => 'Illuminate\\Database\\Schema\\Blueprint',
        'aliasName' => NULL,
      ),
      'softDeletesDatetime' => 
      array (
        'name' => 'softDeletesDatetime',
        'parameters' => 
        array (
          'column' => 
          array (
            'name' => 'column',
            'default' => 
            array (
              'code' => '\'deleted_at\'',
              'attributes' => 
              array (
                'startLine' => 1404,
                'endLine' => 1404,
                'startTokenPos' => 5073,
                'startFilePos' => 40271,
                'endTokenPos' => 5073,
                'endFilePos' => 40282,
              ),
            ),
            'type' => NULL,
            'isVariadic' => false,
            'byRef' => false,
            'isPromoted' => false,
            'attributes' => 
            array (
            ),
            'startLine' => 1404,
            'endLine' => 1404,
            'startColumn' => 41,
            'endColumn' => 62,
            'parameterIndex' => 0,
            'isOptional' => true,
          ),
          'precision' => 
          array (
            'name' => 'precision',
            'default' => 
            array (
              'code' => 'null',
              'attributes' => 
              array (
                'startLine' => 1404,
                'endLine' => 1404,
                'startTokenPos' => 5080,
                'startFilePos' => 40298,
                'endTokenPos' => 5080,
                'endFilePos' => 40301,
              ),
            ),
            'type' => NULL,
            'isVariadic' => false,
            'byRef' => false,
            'isPromoted' => false,
            'attributes' => 
            array (
            ),
            'startLine' => 1404,
            'endLine' => 1404,
            'startColumn' => 65,
            'endColumn' => 81,
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
 * Add a "deleted at" datetime column to the table.
 *
 * @param  string  $column
 * @param  int|null  $precision
 * @return \\Illuminate\\Database\\Schema\\ColumnDefinition
 */',
        'startLine' => 1404,
        'endLine' => 1407,
        'startColumn' => 5,
        'endColumn' => 5,
        'couldThrow' => false,
        'isClosure' => false,
        'isGenerator' => false,
        'isVariadic' => false,
        'modifiers' => 1,
        'namespace' => 'Illuminate\\Database\\Schema',
        'declaringClassName' => 'Illuminate\\Database\\Schema\\Blueprint',
        'implementingClassName' => 'Illuminate\\Database\\Schema\\Blueprint',
        'currentClassName' => 'Illuminate\\Database\\Schema\\Blueprint',
        'aliasName' => NULL,
      ),
      'year' => 
      array (
        'name' => 'year',
        'parameters' => 
        array (
          'column' => 
          array (
            'name' => 'column',
            'default' => NULL,
            'type' => NULL,
            'isVariadic' => false,
            'byRef' => false,
            'isPromoted' => false,
            'attributes' => 
            array (
            ),
            'startLine' => 1415,
            'endLine' => 1415,
            'startColumn' => 26,
            'endColumn' => 32,
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
 * Create a new year column on the table.
 *
 * @param  string  $column
 * @return \\Illuminate\\Database\\Schema\\ColumnDefinition
 */',
        'startLine' => 1415,
        'endLine' => 1418,
        'startColumn' => 5,
        'endColumn' => 5,
        'couldThrow' => false,
        'isClosure' => false,
        'isGenerator' => false,
        'isVariadic' => false,
        'modifiers' => 1,
        'namespace' => 'Illuminate\\Database\\Schema',
        'declaringClassName' => 'Illuminate\\Database\\Schema\\Blueprint',
        'implementingClassName' => 'Illuminate\\Database\\Schema\\Blueprint',
        'currentClassName' => 'Illuminate\\Database\\Schema\\Blueprint',
        'aliasName' => NULL,
      ),
      'binary' => 
      array (
        'name' => 'binary',
        'parameters' => 
        array (
          'column' => 
          array (
            'name' => 'column',
            'default' => NULL,
            'type' => NULL,
            'isVariadic' => false,
            'byRef' => false,
            'isPromoted' => false,
            'attributes' => 
            array (
            ),
            'startLine' => 1428,
            'endLine' => 1428,
            'startColumn' => 28,
            'endColumn' => 34,
            'parameterIndex' => 0,
            'isOptional' => false,
          ),
          'length' => 
          array (
            'name' => 'length',
            'default' => 
            array (
              'code' => 'null',
              'attributes' => 
              array (
                'startLine' => 1428,
                'endLine' => 1428,
                'startTokenPos' => 5147,
                'startFilePos' => 40908,
                'endTokenPos' => 5147,
                'endFilePos' => 40911,
              ),
            ),
            'type' => NULL,
            'isVariadic' => false,
            'byRef' => false,
            'isPromoted' => false,
            'attributes' => 
            array (
            ),
            'startLine' => 1428,
            'endLine' => 1428,
            'startColumn' => 37,
            'endColumn' => 50,
            'parameterIndex' => 1,
            'isOptional' => true,
          ),
          'fixed' => 
          array (
            'name' => 'fixed',
            'default' => 
            array (
              'code' => 'false',
              'attributes' => 
              array (
                'startLine' => 1428,
                'endLine' => 1428,
                'startTokenPos' => 5154,
                'startFilePos' => 40923,
                'endTokenPos' => 5154,
                'endFilePos' => 40927,
              ),
            ),
            'type' => NULL,
            'isVariadic' => false,
            'byRef' => false,
            'isPromoted' => false,
            'attributes' => 
            array (
            ),
            'startLine' => 1428,
            'endLine' => 1428,
            'startColumn' => 53,
            'endColumn' => 66,
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
 * Create a new binary column on the table.
 *
 * @param  string  $column
 * @param  int|null  $length
 * @param  bool  $fixed
 * @return \\Illuminate\\Database\\Schema\\ColumnDefinition
 */',
        'startLine' => 1428,
        'endLine' => 1431,
        'startColumn' => 5,
        'endColumn' => 5,
        'couldThrow' => false,
        'isClosure' => false,
        'isGenerator' => false,
        'isVariadic' => false,
        'modifiers' => 1,
        'namespace' => 'Illuminate\\Database\\Schema',
        'declaringClassName' => 'Illuminate\\Database\\Schema\\Blueprint',
        'implementingClassName' => 'Illuminate\\Database\\Schema\\Blueprint',
        'currentClassName' => 'Illuminate\\Database\\Schema\\Blueprint',
        'aliasName' => NULL,
      ),
      'uuid' => 
      array (
        'name' => 'uuid',
        'parameters' => 
        array (
          'column' => 
          array (
            'name' => 'column',
            'default' => 
            array (
              'code' => '\'uuid\'',
              'attributes' => 
              array (
                'startLine' => 1439,
                'endLine' => 1439,
                'startTokenPos' => 5202,
                'startFilePos' => 41232,
                'endTokenPos' => 5202,
                'endFilePos' => 41237,
              ),
            ),
            'type' => NULL,
            'isVariadic' => false,
            'byRef' => false,
            'isPromoted' => false,
            'attributes' => 
            array (
            ),
            'startLine' => 1439,
            'endLine' => 1439,
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
 * Create a new UUID column on the table.
 *
 * @param  string  $column
 * @return \\Illuminate\\Database\\Schema\\ColumnDefinition
 */',
        'startLine' => 1439,
        'endLine' => 1442,
        'startColumn' => 5,
        'endColumn' => 5,
        'couldThrow' => false,
        'isClosure' => false,
        'isGenerator' => false,
        'isVariadic' => false,
        'modifiers' => 1,
        'namespace' => 'Illuminate\\Database\\Schema',
        'declaringClassName' => 'Illuminate\\Database\\Schema\\Blueprint',
        'implementingClassName' => 'Illuminate\\Database\\Schema\\Blueprint',
        'currentClassName' => 'Illuminate\\Database\\Schema\\Blueprint',
        'aliasName' => NULL,
      ),
      'foreignUuid' => 
      array (
        'name' => 'foreignUuid',
        'parameters' => 
        array (
          'column' => 
          array (
            'name' => 'column',
            'default' => NULL,
            'type' => NULL,
            'isVariadic' => false,
            'byRef' => false,
            'isPromoted' => false,
            'attributes' => 
            array (
            ),
            'startLine' => 1450,
            'endLine' => 1450,
            'startColumn' => 33,
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
 * Create a new UUID foreign ID column on the table.
 *
 * @param  string  $column
 * @return \\Illuminate\\Database\\Schema\\ForeignIdColumnDefinition
 */',
        'startLine' => 1450,
        'endLine' => 1456,
        'startColumn' => 5,
        'endColumn' => 5,
        'couldThrow' => false,
        'isClosure' => false,
        'isGenerator' => false,
        'isVariadic' => false,
        'modifiers' => 1,
        'namespace' => 'Illuminate\\Database\\Schema',
        'declaringClassName' => 'Illuminate\\Database\\Schema\\Blueprint',
        'implementingClassName' => 'Illuminate\\Database\\Schema\\Blueprint',
        'currentClassName' => 'Illuminate\\Database\\Schema\\Blueprint',
        'aliasName' => NULL,
      ),
      'ulid' => 
      array (
        'name' => 'ulid',
        'parameters' => 
        array (
          'column' => 
          array (
            'name' => 'column',
            'default' => 
            array (
              'code' => '\'ulid\'',
              'attributes' => 
              array (
                'startLine' => 1465,
                'endLine' => 1465,
                'startTokenPos' => 5283,
                'startFilePos' => 41920,
                'endTokenPos' => 5283,
                'endFilePos' => 41925,
              ),
            ),
            'type' => NULL,
            'isVariadic' => false,
            'byRef' => false,
            'isPromoted' => false,
            'attributes' => 
            array (
            ),
            'startLine' => 1465,
            'endLine' => 1465,
            'startColumn' => 26,
            'endColumn' => 41,
            'parameterIndex' => 0,
            'isOptional' => true,
          ),
          'length' => 
          array (
            'name' => 'length',
            'default' => 
            array (
              'code' => '26',
              'attributes' => 
              array (
                'startLine' => 1465,
                'endLine' => 1465,
                'startTokenPos' => 5290,
                'startFilePos' => 41938,
                'endTokenPos' => 5290,
                'endFilePos' => 41939,
              ),
            ),
            'type' => NULL,
            'isVariadic' => false,
            'byRef' => false,
            'isPromoted' => false,
            'attributes' => 
            array (
            ),
            'startLine' => 1465,
            'endLine' => 1465,
            'startColumn' => 44,
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
 * Create a new ULID column on the table.
 *
 * @param  string  $column
 * @param  int|null  $length
 * @return \\Illuminate\\Database\\Schema\\ColumnDefinition
 */',
        'startLine' => 1465,
        'endLine' => 1468,
        'startColumn' => 5,
        'endColumn' => 5,
        'couldThrow' => false,
        'isClosure' => false,
        'isGenerator' => false,
        'isVariadic' => false,
        'modifiers' => 1,
        'namespace' => 'Illuminate\\Database\\Schema',
        'declaringClassName' => 'Illuminate\\Database\\Schema\\Blueprint',
        'implementingClassName' => 'Illuminate\\Database\\Schema\\Blueprint',
        'currentClassName' => 'Illuminate\\Database\\Schema\\Blueprint',
        'aliasName' => NULL,
      ),
      'foreignUlid' => 
      array (
        'name' => 'foreignUlid',
        'parameters' => 
        array (
          'column' => 
          array (
            'name' => 'column',
            'default' => NULL,
            'type' => NULL,
            'isVariadic' => false,
            'byRef' => false,
            'isPromoted' => false,
            'attributes' => 
            array (
            ),
            'startLine' => 1477,
            'endLine' => 1477,
            'startColumn' => 33,
            'endColumn' => 39,
            'parameterIndex' => 0,
            'isOptional' => false,
          ),
          'length' => 
          array (
            'name' => 'length',
            'default' => 
            array (
              'code' => '26',
              'attributes' => 
              array (
                'startLine' => 1477,
                'endLine' => 1477,
                'startTokenPos' => 5325,
                'startFilePos' => 42265,
                'endTokenPos' => 5325,
                'endFilePos' => 42266,
              ),
            ),
            'type' => NULL,
            'isVariadic' => false,
            'byRef' => false,
            'isPromoted' => false,
            'attributes' => 
            array (
            ),
            'startLine' => 1477,
            'endLine' => 1477,
            'startColumn' => 42,
            'endColumn' => 53,
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
 * Create a new ULID foreign ID column on the table.
 *
 * @param  string  $column
 * @param  int|null  $length
 * @return \\Illuminate\\Database\\Schema\\ForeignIdColumnDefinition
 */',
        'startLine' => 1477,
        'endLine' => 1484,
        'startColumn' => 5,
        'endColumn' => 5,
        'couldThrow' => false,
        'isClosure' => false,
        'isGenerator' => false,
        'isVariadic' => false,
        'modifiers' => 1,
        'namespace' => 'Illuminate\\Database\\Schema',
        'declaringClassName' => 'Illuminate\\Database\\Schema\\Blueprint',
        'implementingClassName' => 'Illuminate\\Database\\Schema\\Blueprint',
        'currentClassName' => 'Illuminate\\Database\\Schema\\Blueprint',
        'aliasName' => NULL,
      ),
      'ipAddress' => 
      array (
        'name' => 'ipAddress',
        'parameters' => 
        array (
          'column' => 
          array (
            'name' => 'column',
            'default' => 
            array (
              'code' => '\'ip_address\'',
              'attributes' => 
              array (
                'startLine' => 1492,
                'endLine' => 1492,
                'startTokenPos' => 5385,
                'startFilePos' => 42676,
                'endTokenPos' => 5385,
                'endFilePos' => 42687,
              ),
            ),
            'type' => NULL,
            'isVariadic' => false,
            'byRef' => false,
            'isPromoted' => false,
            'attributes' => 
            array (
            ),
            'startLine' => 1492,
            'endLine' => 1492,
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
 * Create a new IP address column on the table.
 *
 * @param  string  $column
 * @return \\Illuminate\\Database\\Schema\\ColumnDefinition
 */',
        'startLine' => 1492,
        'endLine' => 1495,
        'startColumn' => 5,
        'endColumn' => 5,
        'couldThrow' => false,
        'isClosure' => false,
        'isGenerator' => false,
        'isVariadic' => false,
        'modifiers' => 1,
        'namespace' => 'Illuminate\\Database\\Schema',
        'declaringClassName' => 'Illuminate\\Database\\Schema\\Blueprint',
        'implementingClassName' => 'Illuminate\\Database\\Schema\\Blueprint',
        'currentClassName' => 'Illuminate\\Database\\Schema\\Blueprint',
        'aliasName' => NULL,
      ),
      'macAddress' => 
      array (
        'name' => 'macAddress',
        'parameters' => 
        array (
          'column' => 
          array (
            'name' => 'column',
            'default' => 
            array (
              'code' => '\'mac_address\'',
              'attributes' => 
              array (
                'startLine' => 1503,
                'endLine' => 1503,
                'startTokenPos' => 5417,
                'startFilePos' => 42966,
                'endTokenPos' => 5417,
                'endFilePos' => 42978,
              ),
            ),
            'type' => NULL,
            'isVariadic' => false,
            'byRef' => false,
            'isPromoted' => false,
            'attributes' => 
            array (
            ),
            'startLine' => 1503,
            'endLine' => 1503,
            'startColumn' => 32,
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
 * Create a new MAC address column on the table.
 *
 * @param  string  $column
 * @return \\Illuminate\\Database\\Schema\\ColumnDefinition
 */',
        'startLine' => 1503,
        'endLine' => 1506,
        'startColumn' => 5,
        'endColumn' => 5,
        'couldThrow' => false,
        'isClosure' => false,
        'isGenerator' => false,
        'isVariadic' => false,
        'modifiers' => 1,
        'namespace' => 'Illuminate\\Database\\Schema',
        'declaringClassName' => 'Illuminate\\Database\\Schema\\Blueprint',
        'implementingClassName' => 'Illuminate\\Database\\Schema\\Blueprint',
        'currentClassName' => 'Illuminate\\Database\\Schema\\Blueprint',
        'aliasName' => NULL,
      ),
      'geometry' => 
      array (
        'name' => 'geometry',
        'parameters' => 
        array (
          'column' => 
          array (
            'name' => 'column',
            'default' => NULL,
            'type' => NULL,
            'isVariadic' => false,
            'byRef' => false,
            'isPromoted' => false,
            'attributes' => 
            array (
            ),
            'startLine' => 1516,
            'endLine' => 1516,
            'startColumn' => 30,
            'endColumn' => 36,
            'parameterIndex' => 0,
            'isOptional' => false,
          ),
          'subtype' => 
          array (
            'name' => 'subtype',
            'default' => 
            array (
              'code' => 'null',
              'attributes' => 
              array (
                'startLine' => 1516,
                'endLine' => 1516,
                'startTokenPos' => 5452,
                'startFilePos' => 43326,
                'endTokenPos' => 5452,
                'endFilePos' => 43329,
              ),
            ),
            'type' => NULL,
            'isVariadic' => false,
            'byRef' => false,
            'isPromoted' => false,
            'attributes' => 
            array (
            ),
            'startLine' => 1516,
            'endLine' => 1516,
            'startColumn' => 39,
            'endColumn' => 53,
            'parameterIndex' => 1,
            'isOptional' => true,
          ),
          'srid' => 
          array (
            'name' => 'srid',
            'default' => 
            array (
              'code' => '0',
              'attributes' => 
              array (
                'startLine' => 1516,
                'endLine' => 1516,
                'startTokenPos' => 5459,
                'startFilePos' => 43340,
                'endTokenPos' => 5459,
                'endFilePos' => 43340,
              ),
            ),
            'type' => NULL,
            'isVariadic' => false,
            'byRef' => false,
            'isPromoted' => false,
            'attributes' => 
            array (
            ),
            'startLine' => 1516,
            'endLine' => 1516,
            'startColumn' => 56,
            'endColumn' => 64,
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
 * Create a new geometry column on the table.
 *
 * @param  string  $column
 * @param  string|null  $subtype
 * @param  int  $srid
 * @return \\Illuminate\\Database\\Schema\\ColumnDefinition
 */',
        'startLine' => 1516,
        'endLine' => 1519,
        'startColumn' => 5,
        'endColumn' => 5,
        'couldThrow' => false,
        'isClosure' => false,
        'isGenerator' => false,
        'isVariadic' => false,
        'modifiers' => 1,
        'namespace' => 'Illuminate\\Database\\Schema',
        'declaringClassName' => 'Illuminate\\Database\\Schema\\Blueprint',
        'implementingClassName' => 'Illuminate\\Database\\Schema\\Blueprint',
        'currentClassName' => 'Illuminate\\Database\\Schema\\Blueprint',
        'aliasName' => NULL,
      ),
      'geography' => 
      array (
        'name' => 'geography',
        'parameters' => 
        array (
          'column' => 
          array (
            'name' => 'column',
            'default' => NULL,
            'type' => NULL,
            'isVariadic' => false,
            'byRef' => false,
            'isPromoted' => false,
            'attributes' => 
            array (
            ),
            'startLine' => 1529,
            'endLine' => 1529,
            'startColumn' => 31,
            'endColumn' => 37,
            'parameterIndex' => 0,
            'isOptional' => false,
          ),
          'subtype' => 
          array (
            'name' => 'subtype',
            'default' => 
            array (
              'code' => 'null',
              'attributes' => 
              array (
                'startLine' => 1529,
                'endLine' => 1529,
                'startTokenPos' => 5510,
                'startFilePos' => 43730,
                'endTokenPos' => 5510,
                'endFilePos' => 43733,
              ),
            ),
            'type' => NULL,
            'isVariadic' => false,
            'byRef' => false,
            'isPromoted' => false,
            'attributes' => 
            array (
            ),
            'startLine' => 1529,
            'endLine' => 1529,
            'startColumn' => 40,
            'endColumn' => 54,
            'parameterIndex' => 1,
            'isOptional' => true,
          ),
          'srid' => 
          array (
            'name' => 'srid',
            'default' => 
            array (
              'code' => '4326',
              'attributes' => 
              array (
                'startLine' => 1529,
                'endLine' => 1529,
                'startTokenPos' => 5517,
                'startFilePos' => 43744,
                'endTokenPos' => 5517,
                'endFilePos' => 43747,
              ),
            ),
            'type' => NULL,
            'isVariadic' => false,
            'byRef' => false,
            'isPromoted' => false,
            'attributes' => 
            array (
            ),
            'startLine' => 1529,
            'endLine' => 1529,
            'startColumn' => 57,
            'endColumn' => 68,
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
 * Create a new geography column on the table.
 *
 * @param  string  $column
 * @param  string|null  $subtype
 * @param  int  $srid
 * @return \\Illuminate\\Database\\Schema\\ColumnDefinition
 */',
        'startLine' => 1529,
        'endLine' => 1532,
        'startColumn' => 5,
        'endColumn' => 5,
        'couldThrow' => false,
        'isClosure' => false,
        'isGenerator' => false,
        'isVariadic' => false,
        'modifiers' => 1,
        'namespace' => 'Illuminate\\Database\\Schema',
        'declaringClassName' => 'Illuminate\\Database\\Schema\\Blueprint',
        'implementingClassName' => 'Illuminate\\Database\\Schema\\Blueprint',
        'currentClassName' => 'Illuminate\\Database\\Schema\\Blueprint',
        'aliasName' => NULL,
      ),
      'computed' => 
      array (
        'name' => 'computed',
        'parameters' => 
        array (
          'column' => 
          array (
            'name' => 'column',
            'default' => NULL,
            'type' => NULL,
            'isVariadic' => false,
            'byRef' => false,
            'isPromoted' => false,
            'attributes' => 
            array (
            ),
            'startLine' => 1541,
            'endLine' => 1541,
            'startColumn' => 30,
            'endColumn' => 36,
            'parameterIndex' => 0,
            'isOptional' => false,
          ),
          'expression' => 
          array (
            'name' => 'expression',
            'default' => NULL,
            'type' => NULL,
            'isVariadic' => false,
            'byRef' => false,
            'isPromoted' => false,
            'attributes' => 
            array (
            ),
            'startLine' => 1541,
            'endLine' => 1541,
            'startColumn' => 39,
            'endColumn' => 49,
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
 * Create a new generated, computed column on the table.
 *
 * @param  string  $column
 * @param  string  $expression
 * @return \\Illuminate\\Database\\Schema\\ColumnDefinition
 */',
        'startLine' => 1541,
        'endLine' => 1544,
        'startColumn' => 5,
        'endColumn' => 5,
        'couldThrow' => false,
        'isClosure' => false,
        'isGenerator' => false,
        'isVariadic' => false,
        'modifiers' => 1,
        'namespace' => 'Illuminate\\Database\\Schema',
        'declaringClassName' => 'Illuminate\\Database\\Schema\\Blueprint',
        'implementingClassName' => 'Illuminate\\Database\\Schema\\Blueprint',
        'currentClassName' => 'Illuminate\\Database\\Schema\\Blueprint',
        'aliasName' => NULL,
      ),
      'vector' => 
      array (
        'name' => 'vector',
        'parameters' => 
        array (
          'column' => 
          array (
            'name' => 'column',
            'default' => NULL,
            'type' => NULL,
            'isVariadic' => false,
            'byRef' => false,
            'isPromoted' => false,
            'attributes' => 
            array (
            ),
            'startLine' => 1553,
            'endLine' => 1553,
            'startColumn' => 28,
            'endColumn' => 34,
            'parameterIndex' => 0,
            'isOptional' => false,
          ),
          'dimensions' => 
          array (
            'name' => 'dimensions',
            'default' => 
            array (
              'code' => 'null',
              'attributes' => 
              array (
                'startLine' => 1553,
                'endLine' => 1553,
                'startTokenPos' => 5608,
                'startFilePos' => 44468,
                'endTokenPos' => 5608,
                'endFilePos' => 44471,
              ),
            ),
            'type' => NULL,
            'isVariadic' => false,
            'byRef' => false,
            'isPromoted' => false,
            'attributes' => 
            array (
            ),
            'startLine' => 1553,
            'endLine' => 1553,
            'startColumn' => 37,
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
 * Create a new vector column on the table.
 *
 * @param  string  $column
 * @param  int|null  $dimensions
 * @return \\Illuminate\\Database\\Schema\\ColumnDefinition
 */',
        'startLine' => 1553,
        'endLine' => 1558,
        'startColumn' => 5,
        'endColumn' => 5,
        'couldThrow' => false,
        'isClosure' => false,
        'isGenerator' => false,
        'isVariadic' => false,
        'modifiers' => 1,
        'namespace' => 'Illuminate\\Database\\Schema',
        'declaringClassName' => 'Illuminate\\Database\\Schema\\Blueprint',
        'implementingClassName' => 'Illuminate\\Database\\Schema\\Blueprint',
        'currentClassName' => 'Illuminate\\Database\\Schema\\Blueprint',
        'aliasName' => NULL,
      ),
      'tsvector' => 
      array (
        'name' => 'tsvector',
        'parameters' => 
        array (
          'column' => 
          array (
            'name' => 'column',
            'default' => NULL,
            'type' => NULL,
            'isVariadic' => false,
            'byRef' => false,
            'isPromoted' => false,
            'attributes' => 
            array (
            ),
            'startLine' => 1566,
            'endLine' => 1566,
            'startColumn' => 30,
            'endColumn' => 36,
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
 * Create a new tsvector column on the table.
 *
 * @param  string  $column
 * @return \\Illuminate\\Database\\Schema\\ColumnDefinition
 */',
        'startLine' => 1566,
        'endLine' => 1569,
        'startColumn' => 5,
        'endColumn' => 5,
        'couldThrow' => false,
        'isClosure' => false,
        'isGenerator' => false,
        'isVariadic' => false,
        'modifiers' => 1,
        'namespace' => 'Illuminate\\Database\\Schema',
        'declaringClassName' => 'Illuminate\\Database\\Schema\\Blueprint',
        'implementingClassName' => 'Illuminate\\Database\\Schema\\Blueprint',
        'currentClassName' => 'Illuminate\\Database\\Schema\\Blueprint',
        'aliasName' => NULL,
      ),
      'morphs' => 
      array (
        'name' => 'morphs',
        'parameters' => 
        array (
          'name' => 
          array (
            'name' => 'name',
            'default' => NULL,
            'type' => NULL,
            'isVariadic' => false,
            'byRef' => false,
            'isPromoted' => false,
            'attributes' => 
            array (
            ),
            'startLine' => 1579,
            'endLine' => 1579,
            'startColumn' => 28,
            'endColumn' => 32,
            'parameterIndex' => 0,
            'isOptional' => false,
          ),
          'indexName' => 
          array (
            'name' => 'indexName',
            'default' => 
            array (
              'code' => 'null',
              'attributes' => 
              array (
                'startLine' => 1579,
                'endLine' => 1579,
                'startTokenPos' => 5696,
                'startFilePos' => 45136,
                'endTokenPos' => 5696,
                'endFilePos' => 45139,
              ),
            ),
            'type' => NULL,
            'isVariadic' => false,
            'byRef' => false,
            'isPromoted' => false,
            'attributes' => 
            array (
            ),
            'startLine' => 1579,
            'endLine' => 1579,
            'startColumn' => 35,
            'endColumn' => 51,
            'parameterIndex' => 1,
            'isOptional' => true,
          ),
          'after' => 
          array (
            'name' => 'after',
            'default' => 
            array (
              'code' => 'null',
              'attributes' => 
              array (
                'startLine' => 1579,
                'endLine' => 1579,
                'startTokenPos' => 5703,
                'startFilePos' => 45151,
                'endTokenPos' => 5703,
                'endFilePos' => 45154,
              ),
            ),
            'type' => NULL,
            'isVariadic' => false,
            'byRef' => false,
            'isPromoted' => false,
            'attributes' => 
            array (
            ),
            'startLine' => 1579,
            'endLine' => 1579,
            'startColumn' => 54,
            'endColumn' => 66,
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
 * Add the proper columns for a polymorphic table.
 *
 * @param  string  $name
 * @param  string|null  $indexName
 * @param  string|null  $after
 * @return void
 */',
        'startLine' => 1579,
        'endLine' => 1588,
        'startColumn' => 5,
        'endColumn' => 5,
        'couldThrow' => false,
        'isClosure' => false,
        'isGenerator' => false,
        'isVariadic' => false,
        'modifiers' => 1,
        'namespace' => 'Illuminate\\Database\\Schema',
        'declaringClassName' => 'Illuminate\\Database\\Schema\\Blueprint',
        'implementingClassName' => 'Illuminate\\Database\\Schema\\Blueprint',
        'currentClassName' => 'Illuminate\\Database\\Schema\\Blueprint',
        'aliasName' => NULL,
      ),
      'nullableMorphs' => 
      array (
        'name' => 'nullableMorphs',
        'parameters' => 
        array (
          'name' => 
          array (
            'name' => 'name',
            'default' => NULL,
            'type' => NULL,
            'isVariadic' => false,
            'byRef' => false,
            'isPromoted' => false,
            'attributes' => 
            array (
            ),
            'startLine' => 1598,
            'endLine' => 1598,
            'startColumn' => 36,
            'endColumn' => 40,
            'parameterIndex' => 0,
            'isOptional' => false,
          ),
          'indexName' => 
          array (
            'name' => 'indexName',
            'default' => 
            array (
              'code' => 'null',
              'attributes' => 
              array (
                'startLine' => 1598,
                'endLine' => 1598,
                'startTokenPos' => 5805,
                'startFilePos' => 45746,
                'endTokenPos' => 5805,
                'endFilePos' => 45749,
              ),
            ),
            'type' => NULL,
            'isVariadic' => false,
            'byRef' => false,
            'isPromoted' => false,
            'attributes' => 
            array (
            ),
            'startLine' => 1598,
            'endLine' => 1598,
            'startColumn' => 43,
            'endColumn' => 59,
            'parameterIndex' => 1,
            'isOptional' => true,
          ),
          'after' => 
          array (
            'name' => 'after',
            'default' => 
            array (
              'code' => 'null',
              'attributes' => 
              array (
                'startLine' => 1598,
                'endLine' => 1598,
                'startTokenPos' => 5812,
                'startFilePos' => 45761,
                'endTokenPos' => 5812,
                'endFilePos' => 45764,
              ),
            ),
            'type' => NULL,
            'isVariadic' => false,
            'byRef' => false,
            'isPromoted' => false,
            'attributes' => 
            array (
            ),
            'startLine' => 1598,
            'endLine' => 1598,
            'startColumn' => 62,
            'endColumn' => 74,
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
 * Add nullable columns for a polymorphic table.
 *
 * @param  string  $name
 * @param  string|null  $indexName
 * @param  string|null  $after
 * @return void
 */',
        'startLine' => 1598,
        'endLine' => 1607,
        'startColumn' => 5,
        'endColumn' => 5,
        'couldThrow' => false,
        'isClosure' => false,
        'isGenerator' => false,
        'isVariadic' => false,
        'modifiers' => 1,
        'namespace' => 'Illuminate\\Database\\Schema',
        'declaringClassName' => 'Illuminate\\Database\\Schema\\Blueprint',
        'implementingClassName' => 'Illuminate\\Database\\Schema\\Blueprint',
        'currentClassName' => 'Illuminate\\Database\\Schema\\Blueprint',
        'aliasName' => NULL,
      ),
      'numericMorphs' => 
      array (
        'name' => 'numericMorphs',
        'parameters' => 
        array (
          'name' => 
          array (
            'name' => 'name',
            'default' => NULL,
            'type' => NULL,
            'isVariadic' => false,
            'byRef' => false,
            'isPromoted' => false,
            'attributes' => 
            array (
            ),
            'startLine' => 1617,
            'endLine' => 1617,
            'startColumn' => 35,
            'endColumn' => 39,
            'parameterIndex' => 0,
            'isOptional' => false,
          ),
          'indexName' => 
          array (
            'name' => 'indexName',
            'default' => 
            array (
              'code' => 'null',
              'attributes' => 
              array (
                'startLine' => 1617,
                'endLine' => 1617,
                'startTokenPos' => 5914,
                'startFilePos' => 46413,
                'endTokenPos' => 5914,
                'endFilePos' => 46416,
              ),
            ),
            'type' => NULL,
            'isVariadic' => false,
            'byRef' => false,
            'isPromoted' => false,
            'attributes' => 
            array (
            ),
            'startLine' => 1617,
            'endLine' => 1617,
            'startColumn' => 42,
            'endColumn' => 58,
            'parameterIndex' => 1,
            'isOptional' => true,
          ),
          'after' => 
          array (
            'name' => 'after',
            'default' => 
            array (
              'code' => 'null',
              'attributes' => 
              array (
                'startLine' => 1617,
                'endLine' => 1617,
                'startTokenPos' => 5921,
                'startFilePos' => 46428,
                'endTokenPos' => 5921,
                'endFilePos' => 46431,
              ),
            ),
            'type' => NULL,
            'isVariadic' => false,
            'byRef' => false,
            'isPromoted' => false,
            'attributes' => 
            array (
            ),
            'startLine' => 1617,
            'endLine' => 1617,
            'startColumn' => 61,
            'endColumn' => 73,
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
 * Add the proper columns for a polymorphic table using numeric IDs (incremental).
 *
 * @param  string  $name
 * @param  string|null  $indexName
 * @param  string|null  $after
 * @return void
 */',
        'startLine' => 1617,
        'endLine' => 1626,
        'startColumn' => 5,
        'endColumn' => 5,
        'couldThrow' => false,
        'isClosure' => false,
        'isGenerator' => false,
        'isVariadic' => false,
        'modifiers' => 1,
        'namespace' => 'Illuminate\\Database\\Schema',
        'declaringClassName' => 'Illuminate\\Database\\Schema\\Blueprint',
        'implementingClassName' => 'Illuminate\\Database\\Schema\\Blueprint',
        'currentClassName' => 'Illuminate\\Database\\Schema\\Blueprint',
        'aliasName' => NULL,
      ),
      'nullableNumericMorphs' => 
      array (
        'name' => 'nullableNumericMorphs',
        'parameters' => 
        array (
          'name' => 
          array (
            'name' => 'name',
            'default' => NULL,
            'type' => NULL,
            'isVariadic' => false,
            'byRef' => false,
            'isPromoted' => false,
            'attributes' => 
            array (
            ),
            'startLine' => 1636,
            'endLine' => 1636,
            'startColumn' => 43,
            'endColumn' => 47,
            'parameterIndex' => 0,
            'isOptional' => false,
          ),
          'indexName' => 
          array (
            'name' => 'indexName',
            'default' => 
            array (
              'code' => 'null',
              'attributes' => 
              array (
                'startLine' => 1636,
                'endLine' => 1636,
                'startTokenPos' => 6025,
                'startFilePos' => 46987,
                'endTokenPos' => 6025,
                'endFilePos' => 46990,
              ),
            ),
            'type' => NULL,
            'isVariadic' => false,
            'byRef' => false,
            'isPromoted' => false,
            'attributes' => 
            array (
            ),
            'startLine' => 1636,
            'endLine' => 1636,
            'startColumn' => 50,
            'endColumn' => 66,
            'parameterIndex' => 1,
            'isOptional' => true,
          ),
          'after' => 
          array (
            'name' => 'after',
            'default' => 
            array (
              'code' => 'null',
              'attributes' => 
              array (
                'startLine' => 1636,
                'endLine' => 1636,
                'startTokenPos' => 6032,
                'startFilePos' => 47002,
                'endTokenPos' => 6032,
                'endFilePos' => 47005,
              ),
            ),
            'type' => NULL,
            'isVariadic' => false,
            'byRef' => false,
            'isPromoted' => false,
            'attributes' => 
            array (
            ),
            'startLine' => 1636,
            'endLine' => 1636,
            'startColumn' => 69,
            'endColumn' => 81,
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
 * Add nullable columns for a polymorphic table using numeric IDs (incremental).
 *
 * @param  string  $name
 * @param  string|null  $indexName
 * @param  string|null  $after
 * @return void
 */',
        'startLine' => 1636,
        'endLine' => 1647,
        'startColumn' => 5,
        'endColumn' => 5,
        'couldThrow' => false,
        'isClosure' => false,
        'isGenerator' => false,
        'isVariadic' => false,
        'modifiers' => 1,
        'namespace' => 'Illuminate\\Database\\Schema',
        'declaringClassName' => 'Illuminate\\Database\\Schema\\Blueprint',
        'implementingClassName' => 'Illuminate\\Database\\Schema\\Blueprint',
        'currentClassName' => 'Illuminate\\Database\\Schema\\Blueprint',
        'aliasName' => NULL,
      ),
      'uuidMorphs' => 
      array (
        'name' => 'uuidMorphs',
        'parameters' => 
        array (
          'name' => 
          array (
            'name' => 'name',
            'default' => NULL,
            'type' => NULL,
            'isVariadic' => false,
            'byRef' => false,
            'isPromoted' => false,
            'attributes' => 
            array (
            ),
            'startLine' => 1657,
            'endLine' => 1657,
            'startColumn' => 32,
            'endColumn' => 36,
            'parameterIndex' => 0,
            'isOptional' => false,
          ),
          'indexName' => 
          array (
            'name' => 'indexName',
            'default' => 
            array (
              'code' => 'null',
              'attributes' => 
              array (
                'startLine' => 1657,
                'endLine' => 1657,
                'startTokenPos' => 6146,
                'startFilePos' => 47582,
                'endTokenPos' => 6146,
                'endFilePos' => 47585,
              ),
            ),
            'type' => NULL,
            'isVariadic' => false,
            'byRef' => false,
            'isPromoted' => false,
            'attributes' => 
            array (
            ),
            'startLine' => 1657,
            'endLine' => 1657,
            'startColumn' => 39,
            'endColumn' => 55,
            'parameterIndex' => 1,
            'isOptional' => true,
          ),
          'after' => 
          array (
            'name' => 'after',
            'default' => 
            array (
              'code' => 'null',
              'attributes' => 
              array (
                'startLine' => 1657,
                'endLine' => 1657,
                'startTokenPos' => 6153,
                'startFilePos' => 47597,
                'endTokenPos' => 6153,
                'endFilePos' => 47600,
              ),
            ),
            'type' => NULL,
            'isVariadic' => false,
            'byRef' => false,
            'isPromoted' => false,
            'attributes' => 
            array (
            ),
            'startLine' => 1657,
            'endLine' => 1657,
            'startColumn' => 58,
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
 * Add the proper columns for a polymorphic table using UUIDs.
 *
 * @param  string  $name
 * @param  string|null  $indexName
 * @param  string|null  $after
 * @return void
 */',
        'startLine' => 1657,
        'endLine' => 1666,
        'startColumn' => 5,
        'endColumn' => 5,
        'couldThrow' => false,
        'isClosure' => false,
        'isGenerator' => false,
        'isVariadic' => false,
        'modifiers' => 1,
        'namespace' => 'Illuminate\\Database\\Schema',
        'declaringClassName' => 'Illuminate\\Database\\Schema\\Blueprint',
        'implementingClassName' => 'Illuminate\\Database\\Schema\\Blueprint',
        'currentClassName' => 'Illuminate\\Database\\Schema\\Blueprint',
        'aliasName' => NULL,
      ),
      'nullableUuidMorphs' => 
      array (
        'name' => 'nullableUuidMorphs',
        'parameters' => 
        array (
          'name' => 
          array (
            'name' => 'name',
            'default' => NULL,
            'type' => NULL,
            'isVariadic' => false,
            'byRef' => false,
            'isPromoted' => false,
            'attributes' => 
            array (
            ),
            'startLine' => 1676,
            'endLine' => 1676,
            'startColumn' => 40,
            'endColumn' => 44,
            'parameterIndex' => 0,
            'isOptional' => false,
          ),
          'indexName' => 
          array (
            'name' => 'indexName',
            'default' => 
            array (
              'code' => 'null',
              'attributes' => 
              array (
                'startLine' => 1676,
                'endLine' => 1676,
                'startTokenPos' => 6257,
                'startFilePos' => 48119,
                'endTokenPos' => 6257,
                'endFilePos' => 48122,
              ),
            ),
            'type' => NULL,
            'isVariadic' => false,
            'byRef' => false,
            'isPromoted' => false,
            'attributes' => 
            array (
            ),
            'startLine' => 1676,
            'endLine' => 1676,
            'startColumn' => 47,
            'endColumn' => 63,
            'parameterIndex' => 1,
            'isOptional' => true,
          ),
          'after' => 
          array (
            'name' => 'after',
            'default' => 
            array (
              'code' => 'null',
              'attributes' => 
              array (
                'startLine' => 1676,
                'endLine' => 1676,
                'startTokenPos' => 6264,
                'startFilePos' => 48134,
                'endTokenPos' => 6264,
                'endFilePos' => 48137,
              ),
            ),
            'type' => NULL,
            'isVariadic' => false,
            'byRef' => false,
            'isPromoted' => false,
            'attributes' => 
            array (
            ),
            'startLine' => 1676,
            'endLine' => 1676,
            'startColumn' => 66,
            'endColumn' => 78,
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
 * Add nullable columns for a polymorphic table using UUIDs.
 *
 * @param  string  $name
 * @param  string|null  $indexName
 * @param  string|null  $after
 * @return void
 */',
        'startLine' => 1676,
        'endLine' => 1687,
        'startColumn' => 5,
        'endColumn' => 5,
        'couldThrow' => false,
        'isClosure' => false,
        'isGenerator' => false,
        'isVariadic' => false,
        'modifiers' => 1,
        'namespace' => 'Illuminate\\Database\\Schema',
        'declaringClassName' => 'Illuminate\\Database\\Schema\\Blueprint',
        'implementingClassName' => 'Illuminate\\Database\\Schema\\Blueprint',
        'currentClassName' => 'Illuminate\\Database\\Schema\\Blueprint',
        'aliasName' => NULL,
      ),
      'ulidMorphs' => 
      array (
        'name' => 'ulidMorphs',
        'parameters' => 
        array (
          'name' => 
          array (
            'name' => 'name',
            'default' => NULL,
            'type' => NULL,
            'isVariadic' => false,
            'byRef' => false,
            'isPromoted' => false,
            'attributes' => 
            array (
            ),
            'startLine' => 1697,
            'endLine' => 1697,
            'startColumn' => 32,
            'endColumn' => 36,
            'parameterIndex' => 0,
            'isOptional' => false,
          ),
          'indexName' => 
          array (
            'name' => 'indexName',
            'default' => 
            array (
              'code' => 'null',
              'attributes' => 
              array (
                'startLine' => 1697,
                'endLine' => 1697,
                'startTokenPos' => 6378,
                'startFilePos' => 48700,
                'endTokenPos' => 6378,
                'endFilePos' => 48703,
              ),
            ),
            'type' => NULL,
            'isVariadic' => false,
            'byRef' => false,
            'isPromoted' => false,
            'attributes' => 
            array (
            ),
            'startLine' => 1697,
            'endLine' => 1697,
            'startColumn' => 39,
            'endColumn' => 55,
            'parameterIndex' => 1,
            'isOptional' => true,
          ),
          'after' => 
          array (
            'name' => 'after',
            'default' => 
            array (
              'code' => 'null',
              'attributes' => 
              array (
                'startLine' => 1697,
                'endLine' => 1697,
                'startTokenPos' => 6385,
                'startFilePos' => 48715,
                'endTokenPos' => 6385,
                'endFilePos' => 48718,
              ),
            ),
            'type' => NULL,
            'isVariadic' => false,
            'byRef' => false,
            'isPromoted' => false,
            'attributes' => 
            array (
            ),
            'startLine' => 1697,
            'endLine' => 1697,
            'startColumn' => 58,
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
 * Add the proper columns for a polymorphic table using ULIDs.
 *
 * @param  string  $name
 * @param  string|null  $indexName
 * @param  string|null  $after
 * @return void
 */',
        'startLine' => 1697,
        'endLine' => 1706,
        'startColumn' => 5,
        'endColumn' => 5,
        'couldThrow' => false,
        'isClosure' => false,
        'isGenerator' => false,
        'isVariadic' => false,
        'modifiers' => 1,
        'namespace' => 'Illuminate\\Database\\Schema',
        'declaringClassName' => 'Illuminate\\Database\\Schema\\Blueprint',
        'implementingClassName' => 'Illuminate\\Database\\Schema\\Blueprint',
        'currentClassName' => 'Illuminate\\Database\\Schema\\Blueprint',
        'aliasName' => NULL,
      ),
      'nullableUlidMorphs' => 
      array (
        'name' => 'nullableUlidMorphs',
        'parameters' => 
        array (
          'name' => 
          array (
            'name' => 'name',
            'default' => NULL,
            'type' => NULL,
            'isVariadic' => false,
            'byRef' => false,
            'isPromoted' => false,
            'attributes' => 
            array (
            ),
            'startLine' => 1716,
            'endLine' => 1716,
            'startColumn' => 40,
            'endColumn' => 44,
            'parameterIndex' => 0,
            'isOptional' => false,
          ),
          'indexName' => 
          array (
            'name' => 'indexName',
            'default' => 
            array (
              'code' => 'null',
              'attributes' => 
              array (
                'startLine' => 1716,
                'endLine' => 1716,
                'startTokenPos' => 6489,
                'startFilePos' => 49237,
                'endTokenPos' => 6489,
                'endFilePos' => 49240,
              ),
            ),
            'type' => NULL,
            'isVariadic' => false,
            'byRef' => false,
            'isPromoted' => false,
            'attributes' => 
            array (
            ),
            'startLine' => 1716,
            'endLine' => 1716,
            'startColumn' => 47,
            'endColumn' => 63,
            'parameterIndex' => 1,
            'isOptional' => true,
          ),
          'after' => 
          array (
            'name' => 'after',
            'default' => 
            array (
              'code' => 'null',
              'attributes' => 
              array (
                'startLine' => 1716,
                'endLine' => 1716,
                'startTokenPos' => 6496,
                'startFilePos' => 49252,
                'endTokenPos' => 6496,
                'endFilePos' => 49255,
              ),
            ),
            'type' => NULL,
            'isVariadic' => false,
            'byRef' => false,
            'isPromoted' => false,
            'attributes' => 
            array (
            ),
            'startLine' => 1716,
            'endLine' => 1716,
            'startColumn' => 66,
            'endColumn' => 78,
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
 * Add nullable columns for a polymorphic table using ULIDs.
 *
 * @param  string  $name
 * @param  string|null  $indexName
 * @param  string|null  $after
 * @return void
 */',
        'startLine' => 1716,
        'endLine' => 1727,
        'startColumn' => 5,
        'endColumn' => 5,
        'couldThrow' => false,
        'isClosure' => false,
        'isGenerator' => false,
        'isVariadic' => false,
        'modifiers' => 1,
        'namespace' => 'Illuminate\\Database\\Schema',
        'declaringClassName' => 'Illuminate\\Database\\Schema\\Blueprint',
        'implementingClassName' => 'Illuminate\\Database\\Schema\\Blueprint',
        'currentClassName' => 'Illuminate\\Database\\Schema\\Blueprint',
        'aliasName' => NULL,
      ),
      'rememberToken' => 
      array (
        'name' => 'rememberToken',
        'parameters' => 
        array (
        ),
        'returnsReference' => false,
        'returnType' => NULL,
        'attributes' => 
        array (
        ),
        'docComment' => '/**
 * Add the `remember_token` column to the table.
 *
 * @return \\Illuminate\\Database\\Schema\\ColumnDefinition
 */',
        'startLine' => 1734,
        'endLine' => 1737,
        'startColumn' => 5,
        'endColumn' => 5,
        'couldThrow' => false,
        'isClosure' => false,
        'isGenerator' => false,
        'isVariadic' => false,
        'modifiers' => 1,
        'namespace' => 'Illuminate\\Database\\Schema',
        'declaringClassName' => 'Illuminate\\Database\\Schema\\Blueprint',
        'implementingClassName' => 'Illuminate\\Database\\Schema\\Blueprint',
        'currentClassName' => 'Illuminate\\Database\\Schema\\Blueprint',
        'aliasName' => NULL,
      ),
      'rawColumn' => 
      array (
        'name' => 'rawColumn',
        'parameters' => 
        array (
          'column' => 
          array (
            'name' => 'column',
            'default' => NULL,
            'type' => NULL,
            'isVariadic' => false,
            'byRef' => false,
            'isPromoted' => false,
            'attributes' => 
            array (
            ),
            'startLine' => 1746,
            'endLine' => 1746,
            'startColumn' => 31,
            'endColumn' => 37,
            'parameterIndex' => 0,
            'isOptional' => false,
          ),
          'definition' => 
          array (
            'name' => 'definition',
            'default' => NULL,
            'type' => NULL,
            'isVariadic' => false,
            'byRef' => false,
            'isPromoted' => false,
            'attributes' => 
            array (
            ),
            'startLine' => 1746,
            'endLine' => 1746,
            'startColumn' => 40,
            'endColumn' => 50,
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
 * Create a new custom column on the table.
 *
 * @param  string  $column
 * @param  string  $definition
 * @return \\Illuminate\\Database\\Schema\\ColumnDefinition
 */',
        'startLine' => 1746,
        'endLine' => 1749,
        'startColumn' => 5,
        'endColumn' => 5,
        'couldThrow' => false,
        'isClosure' => false,
        'isGenerator' => false,
        'isVariadic' => false,
        'modifiers' => 1,
        'namespace' => 'Illuminate\\Database\\Schema',
        'declaringClassName' => 'Illuminate\\Database\\Schema\\Blueprint',
        'implementingClassName' => 'Illuminate\\Database\\Schema\\Blueprint',
        'currentClassName' => 'Illuminate\\Database\\Schema\\Blueprint',
        'aliasName' => NULL,
      ),
      'comment' => 
      array (
        'name' => 'comment',
        'parameters' => 
        array (
          'comment' => 
          array (
            'name' => 'comment',
            'default' => NULL,
            'type' => NULL,
            'isVariadic' => false,
            'byRef' => false,
            'isPromoted' => false,
            'attributes' => 
            array (
            ),
            'startLine' => 1757,
            'endLine' => 1757,
            'startColumn' => 29,
            'endColumn' => 36,
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
 * Add a comment to the table.
 *
 * @param  string  $comment
 * @return \\Illuminate\\Support\\Fluent
 */',
        'startLine' => 1757,
        'endLine' => 1760,
        'startColumn' => 5,
        'endColumn' => 5,
        'couldThrow' => false,
        'isClosure' => false,
        'isGenerator' => false,
        'isVariadic' => false,
        'modifiers' => 1,
        'namespace' => 'Illuminate\\Database\\Schema',
        'declaringClassName' => 'Illuminate\\Database\\Schema\\Blueprint',
        'implementingClassName' => 'Illuminate\\Database\\Schema\\Blueprint',
        'currentClassName' => 'Illuminate\\Database\\Schema\\Blueprint',
        'aliasName' => NULL,
      ),
      'indexCommand' => 
      array (
        'name' => 'indexCommand',
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
            'startLine' => 1772,
            'endLine' => 1772,
            'startColumn' => 37,
            'endColumn' => 41,
            'parameterIndex' => 0,
            'isOptional' => false,
          ),
          'columns' => 
          array (
            'name' => 'columns',
            'default' => NULL,
            'type' => NULL,
            'isVariadic' => false,
            'byRef' => false,
            'isPromoted' => false,
            'attributes' => 
            array (
            ),
            'startLine' => 1772,
            'endLine' => 1772,
            'startColumn' => 44,
            'endColumn' => 51,
            'parameterIndex' => 1,
            'isOptional' => false,
          ),
          'index' => 
          array (
            'name' => 'index',
            'default' => NULL,
            'type' => NULL,
            'isVariadic' => false,
            'byRef' => false,
            'isPromoted' => false,
            'attributes' => 
            array (
            ),
            'startLine' => 1772,
            'endLine' => 1772,
            'startColumn' => 54,
            'endColumn' => 59,
            'parameterIndex' => 2,
            'isOptional' => false,
          ),
          'algorithm' => 
          array (
            'name' => 'algorithm',
            'default' => 
            array (
              'code' => 'null',
              'attributes' => 
              array (
                'startLine' => 1772,
                'endLine' => 1772,
                'startTokenPos' => 6721,
                'startFilePos' => 50774,
                'endTokenPos' => 6721,
                'endFilePos' => 50777,
              ),
            ),
            'type' => NULL,
            'isVariadic' => false,
            'byRef' => false,
            'isPromoted' => false,
            'attributes' => 
            array (
            ),
            'startLine' => 1772,
            'endLine' => 1772,
            'startColumn' => 62,
            'endColumn' => 78,
            'parameterIndex' => 3,
            'isOptional' => true,
          ),
          'operatorClass' => 
          array (
            'name' => 'operatorClass',
            'default' => 
            array (
              'code' => 'null',
              'attributes' => 
              array (
                'startLine' => 1772,
                'endLine' => 1772,
                'startTokenPos' => 6728,
                'startFilePos' => 50797,
                'endTokenPos' => 6728,
                'endFilePos' => 50800,
              ),
            ),
            'type' => NULL,
            'isVariadic' => false,
            'byRef' => false,
            'isPromoted' => false,
            'attributes' => 
            array (
            ),
            'startLine' => 1772,
            'endLine' => 1772,
            'startColumn' => 81,
            'endColumn' => 101,
            'parameterIndex' => 4,
            'isOptional' => true,
          ),
        ),
        'returnsReference' => false,
        'returnType' => NULL,
        'attributes' => 
        array (
        ),
        'docComment' => '/**
 * Create a new index command on the blueprint.
 *
 * @param  string  $type
 * @param  string|array  $columns
 * @param  string  $index
 * @param  string|null  $algorithm
 * @param  string|null  $operatorClass
 * @return \\Illuminate\\Support\\Fluent
 */',
        'startLine' => 1772,
        'endLine' => 1784,
        'startColumn' => 5,
        'endColumn' => 5,
        'couldThrow' => false,
        'isClosure' => false,
        'isGenerator' => false,
        'isVariadic' => false,
        'modifiers' => 2,
        'namespace' => 'Illuminate\\Database\\Schema',
        'declaringClassName' => 'Illuminate\\Database\\Schema\\Blueprint',
        'implementingClassName' => 'Illuminate\\Database\\Schema\\Blueprint',
        'currentClassName' => 'Illuminate\\Database\\Schema\\Blueprint',
        'aliasName' => NULL,
      ),
      'dropIndexCommand' => 
      array (
        'name' => 'dropIndexCommand',
        'parameters' => 
        array (
          'command' => 
          array (
            'name' => 'command',
            'default' => NULL,
            'type' => NULL,
            'isVariadic' => false,
            'byRef' => false,
            'isPromoted' => false,
            'attributes' => 
            array (
            ),
            'startLine' => 1794,
            'endLine' => 1794,
            'startColumn' => 41,
            'endColumn' => 48,
            'parameterIndex' => 0,
            'isOptional' => false,
          ),
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
            'startLine' => 1794,
            'endLine' => 1794,
            'startColumn' => 51,
            'endColumn' => 55,
            'parameterIndex' => 1,
            'isOptional' => false,
          ),
          'index' => 
          array (
            'name' => 'index',
            'default' => NULL,
            'type' => NULL,
            'isVariadic' => false,
            'byRef' => false,
            'isPromoted' => false,
            'attributes' => 
            array (
            ),
            'startLine' => 1794,
            'endLine' => 1794,
            'startColumn' => 58,
            'endColumn' => 63,
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
 * Create a new drop index command on the blueprint.
 *
 * @param  string  $command
 * @param  string  $type
 * @param  string|array  $index
 * @return \\Illuminate\\Support\\Fluent
 */',
        'startLine' => 1794,
        'endLine' => 1806,
        'startColumn' => 5,
        'endColumn' => 5,
        'couldThrow' => false,
        'isClosure' => false,
        'isGenerator' => false,
        'isVariadic' => false,
        'modifiers' => 2,
        'namespace' => 'Illuminate\\Database\\Schema',
        'declaringClassName' => 'Illuminate\\Database\\Schema\\Blueprint',
        'implementingClassName' => 'Illuminate\\Database\\Schema\\Blueprint',
        'currentClassName' => 'Illuminate\\Database\\Schema\\Blueprint',
        'aliasName' => NULL,
      ),
      'createIndexName' => 
      array (
        'name' => 'createIndexName',
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
            'startLine' => 1815,
            'endLine' => 1815,
            'startColumn' => 40,
            'endColumn' => 44,
            'parameterIndex' => 0,
            'isOptional' => false,
          ),
          'columns' => 
          array (
            'name' => 'columns',
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
            'startLine' => 1815,
            'endLine' => 1815,
            'startColumn' => 47,
            'endColumn' => 60,
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
 * Create a default index name for the table.
 *
 * @param  string  $type
 * @param  array  $columns
 * @return string
 */',
        'startLine' => 1815,
        'endLine' => 1828,
        'startColumn' => 5,
        'endColumn' => 5,
        'couldThrow' => false,
        'isClosure' => false,
        'isGenerator' => false,
        'isVariadic' => false,
        'modifiers' => 2,
        'namespace' => 'Illuminate\\Database\\Schema',
        'declaringClassName' => 'Illuminate\\Database\\Schema\\Blueprint',
        'implementingClassName' => 'Illuminate\\Database\\Schema\\Blueprint',
        'currentClassName' => 'Illuminate\\Database\\Schema\\Blueprint',
        'aliasName' => NULL,
      ),
      'addColumn' => 
      array (
        'name' => 'addColumn',
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
            'startLine' => 1838,
            'endLine' => 1838,
            'startColumn' => 31,
            'endColumn' => 35,
            'parameterIndex' => 0,
            'isOptional' => false,
          ),
          'name' => 
          array (
            'name' => 'name',
            'default' => NULL,
            'type' => NULL,
            'isVariadic' => false,
            'byRef' => false,
            'isPromoted' => false,
            'attributes' => 
            array (
            ),
            'startLine' => 1838,
            'endLine' => 1838,
            'startColumn' => 38,
            'endColumn' => 42,
            'parameterIndex' => 1,
            'isOptional' => false,
          ),
          'parameters' => 
          array (
            'name' => 'parameters',
            'default' => 
            array (
              'code' => '[]',
              'attributes' => 
              array (
                'startLine' => 1838,
                'endLine' => 1838,
                'startTokenPos' => 7067,
                'startFilePos' => 53067,
                'endTokenPos' => 7068,
                'endFilePos' => 53068,
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
            'startLine' => 1838,
            'endLine' => 1838,
            'startColumn' => 45,
            'endColumn' => 66,
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
 * Add a new column to the blueprint.
 *
 * @param  string  $type
 * @param  string  $name
 * @param  array  $parameters
 * @return \\Illuminate\\Database\\Schema\\ColumnDefinition
 */',
        'startLine' => 1838,
        'endLine' => 1843,
        'startColumn' => 5,
        'endColumn' => 5,
        'couldThrow' => false,
        'isClosure' => false,
        'isGenerator' => false,
        'isVariadic' => false,
        'modifiers' => 1,
        'namespace' => 'Illuminate\\Database\\Schema',
        'declaringClassName' => 'Illuminate\\Database\\Schema\\Blueprint',
        'implementingClassName' => 'Illuminate\\Database\\Schema\\Blueprint',
        'currentClassName' => 'Illuminate\\Database\\Schema\\Blueprint',
        'aliasName' => NULL,
      ),
      'addColumnDefinition' => 
      array (
        'name' => 'addColumnDefinition',
        'parameters' => 
        array (
          'definition' => 
          array (
            'name' => 'definition',
            'default' => NULL,
            'type' => NULL,
            'isVariadic' => false,
            'byRef' => false,
            'isPromoted' => false,
            'attributes' => 
            array (
            ),
            'startLine' => 1851,
            'endLine' => 1851,
            'startColumn' => 44,
            'endColumn' => 54,
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
 * Add a new column definition to the blueprint.
 *
 * @param  \\Illuminate\\Database\\Schema\\ColumnDefinition  $definition
 * @return \\Illuminate\\Database\\Schema\\ColumnDefinition
 */',
        'startLine' => 1851,
        'endLine' => 1866,
        'startColumn' => 5,
        'endColumn' => 5,
        'couldThrow' => false,
        'isClosure' => false,
        'isGenerator' => false,
        'isVariadic' => false,
        'modifiers' => 2,
        'namespace' => 'Illuminate\\Database\\Schema',
        'declaringClassName' => 'Illuminate\\Database\\Schema\\Blueprint',
        'implementingClassName' => 'Illuminate\\Database\\Schema\\Blueprint',
        'currentClassName' => 'Illuminate\\Database\\Schema\\Blueprint',
        'aliasName' => NULL,
      ),
      'after' => 
      array (
        'name' => 'after',
        'parameters' => 
        array (
          'column' => 
          array (
            'name' => 'column',
            'default' => NULL,
            'type' => NULL,
            'isVariadic' => false,
            'byRef' => false,
            'isPromoted' => false,
            'attributes' => 
            array (
            ),
            'startLine' => 1875,
            'endLine' => 1875,
            'startColumn' => 27,
            'endColumn' => 33,
            'parameterIndex' => 0,
            'isOptional' => false,
          ),
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
            'startLine' => 1875,
            'endLine' => 1875,
            'startColumn' => 36,
            'endColumn' => 52,
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
 * Add the columns from the callback after the given column.
 *
 * @param  string  $column
 * @param  (\\Closure(self): void)  $callback
 * @return void
 */',
        'startLine' => 1875,
        'endLine' => 1882,
        'startColumn' => 5,
        'endColumn' => 5,
        'couldThrow' => false,
        'isClosure' => false,
        'isGenerator' => false,
        'isVariadic' => false,
        'modifiers' => 1,
        'namespace' => 'Illuminate\\Database\\Schema',
        'declaringClassName' => 'Illuminate\\Database\\Schema\\Blueprint',
        'implementingClassName' => 'Illuminate\\Database\\Schema\\Blueprint',
        'currentClassName' => 'Illuminate\\Database\\Schema\\Blueprint',
        'aliasName' => NULL,
      ),
      'removeColumn' => 
      array (
        'name' => 'removeColumn',
        'parameters' => 
        array (
          'name' => 
          array (
            'name' => 'name',
            'default' => NULL,
            'type' => NULL,
            'isVariadic' => false,
            'byRef' => false,
            'isPromoted' => false,
            'attributes' => 
            array (
            ),
            'startLine' => 1890,
            'endLine' => 1890,
            'startColumn' => 34,
            'endColumn' => 38,
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
 * Remove a column from the schema blueprint.
 *
 * @param  string  $name
 * @return $this
 */',
        'startLine' => 1890,
        'endLine' => 1901,
        'startColumn' => 5,
        'endColumn' => 5,
        'couldThrow' => false,
        'isClosure' => false,
        'isGenerator' => false,
        'isVariadic' => false,
        'modifiers' => 1,
        'namespace' => 'Illuminate\\Database\\Schema',
        'declaringClassName' => 'Illuminate\\Database\\Schema\\Blueprint',
        'implementingClassName' => 'Illuminate\\Database\\Schema\\Blueprint',
        'currentClassName' => 'Illuminate\\Database\\Schema\\Blueprint',
        'aliasName' => NULL,
      ),
      'addCommand' => 
      array (
        'name' => 'addCommand',
        'parameters' => 
        array (
          'name' => 
          array (
            'name' => 'name',
            'default' => NULL,
            'type' => NULL,
            'isVariadic' => false,
            'byRef' => false,
            'isPromoted' => false,
            'attributes' => 
            array (
            ),
            'startLine' => 1910,
            'endLine' => 1910,
            'startColumn' => 35,
            'endColumn' => 39,
            'parameterIndex' => 0,
            'isOptional' => false,
          ),
          'parameters' => 
          array (
            'name' => 'parameters',
            'default' => 
            array (
              'code' => '[]',
              'attributes' => 
              array (
                'startLine' => 1910,
                'endLine' => 1910,
                'startTokenPos' => 7385,
                'startFilePos' => 54913,
                'endTokenPos' => 7386,
                'endFilePos' => 54914,
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
            'startLine' => 1910,
            'endLine' => 1910,
            'startColumn' => 42,
            'endColumn' => 63,
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
 * Add a new command to the blueprint.
 *
 * @param  string  $name
 * @param  array  $parameters
 * @return \\Illuminate\\Support\\Fluent
 */',
        'startLine' => 1910,
        'endLine' => 1915,
        'startColumn' => 5,
        'endColumn' => 5,
        'couldThrow' => false,
        'isClosure' => false,
        'isGenerator' => false,
        'isVariadic' => false,
        'modifiers' => 2,
        'namespace' => 'Illuminate\\Database\\Schema',
        'declaringClassName' => 'Illuminate\\Database\\Schema\\Blueprint',
        'implementingClassName' => 'Illuminate\\Database\\Schema\\Blueprint',
        'currentClassName' => 'Illuminate\\Database\\Schema\\Blueprint',
        'aliasName' => NULL,
      ),
      'createCommand' => 
      array (
        'name' => 'createCommand',
        'parameters' => 
        array (
          'name' => 
          array (
            'name' => 'name',
            'default' => NULL,
            'type' => NULL,
            'isVariadic' => false,
            'byRef' => false,
            'isPromoted' => false,
            'attributes' => 
            array (
            ),
            'startLine' => 1924,
            'endLine' => 1924,
            'startColumn' => 38,
            'endColumn' => 42,
            'parameterIndex' => 0,
            'isOptional' => false,
          ),
          'parameters' => 
          array (
            'name' => 'parameters',
            'default' => 
            array (
              'code' => '[]',
              'attributes' => 
              array (
                'startLine' => 1924,
                'endLine' => 1924,
                'startTokenPos' => 7438,
                'startFilePos' => 55265,
                'endTokenPos' => 7439,
                'endFilePos' => 55266,
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
            'startLine' => 1924,
            'endLine' => 1924,
            'startColumn' => 45,
            'endColumn' => 66,
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
 * Create a new Fluent command.
 *
 * @param  string  $name
 * @param  array  $parameters
 * @return \\Illuminate\\Support\\Fluent
 */',
        'startLine' => 1924,
        'endLine' => 1927,
        'startColumn' => 5,
        'endColumn' => 5,
        'couldThrow' => false,
        'isClosure' => false,
        'isGenerator' => false,
        'isVariadic' => false,
        'modifiers' => 2,
        'namespace' => 'Illuminate\\Database\\Schema',
        'declaringClassName' => 'Illuminate\\Database\\Schema\\Blueprint',
        'implementingClassName' => 'Illuminate\\Database\\Schema\\Blueprint',
        'currentClassName' => 'Illuminate\\Database\\Schema\\Blueprint',
        'aliasName' => NULL,
      ),
      'getTable' => 
      array (
        'name' => 'getTable',
        'parameters' => 
        array (
        ),
        'returnsReference' => false,
        'returnType' => NULL,
        'attributes' => 
        array (
        ),
        'docComment' => '/**
 * Get the table the blueprint describes.
 *
 * @return string
 */',
        'startLine' => 1934,
        'endLine' => 1937,
        'startColumn' => 5,
        'endColumn' => 5,
        'couldThrow' => false,
        'isClosure' => false,
        'isGenerator' => false,
        'isVariadic' => false,
        'modifiers' => 1,
        'namespace' => 'Illuminate\\Database\\Schema',
        'declaringClassName' => 'Illuminate\\Database\\Schema\\Blueprint',
        'implementingClassName' => 'Illuminate\\Database\\Schema\\Blueprint',
        'currentClassName' => 'Illuminate\\Database\\Schema\\Blueprint',
        'aliasName' => NULL,
      ),
      'getPrefix' => 
      array (
        'name' => 'getPrefix',
        'parameters' => 
        array (
        ),
        'returnsReference' => false,
        'returnType' => NULL,
        'attributes' => 
        array (
        ),
        'docComment' => '/**
 * Get the table prefix.
 *
 * @deprecated Use DB::getTablePrefix()
 *
 * @return string
 */',
        'startLine' => 1946,
        'endLine' => 1949,
        'startColumn' => 5,
        'endColumn' => 5,
        'couldThrow' => false,
        'isClosure' => false,
        'isGenerator' => false,
        'isVariadic' => false,
        'modifiers' => 1,
        'namespace' => 'Illuminate\\Database\\Schema',
        'declaringClassName' => 'Illuminate\\Database\\Schema\\Blueprint',
        'implementingClassName' => 'Illuminate\\Database\\Schema\\Blueprint',
        'currentClassName' => 'Illuminate\\Database\\Schema\\Blueprint',
        'aliasName' => NULL,
      ),
      'getColumns' => 
      array (
        'name' => 'getColumns',
        'parameters' => 
        array (
        ),
        'returnsReference' => false,
        'returnType' => NULL,
        'attributes' => 
        array (
        ),
        'docComment' => '/**
 * Get the columns on the blueprint.
 *
 * @return \\Illuminate\\Database\\Schema\\ColumnDefinition[]
 */',
        'startLine' => 1956,
        'endLine' => 1959,
        'startColumn' => 5,
        'endColumn' => 5,
        'couldThrow' => false,
        'isClosure' => false,
        'isGenerator' => false,
        'isVariadic' => false,
        'modifiers' => 1,
        'namespace' => 'Illuminate\\Database\\Schema',
        'declaringClassName' => 'Illuminate\\Database\\Schema\\Blueprint',
        'implementingClassName' => 'Illuminate\\Database\\Schema\\Blueprint',
        'currentClassName' => 'Illuminate\\Database\\Schema\\Blueprint',
        'aliasName' => NULL,
      ),
      'getCommands' => 
      array (
        'name' => 'getCommands',
        'parameters' => 
        array (
        ),
        'returnsReference' => false,
        'returnType' => NULL,
        'attributes' => 
        array (
        ),
        'docComment' => '/**
 * Get the commands on the blueprint.
 *
 * @return \\Illuminate\\Support\\Fluent[]
 */',
        'startLine' => 1966,
        'endLine' => 1969,
        'startColumn' => 5,
        'endColumn' => 5,
        'couldThrow' => false,
        'isClosure' => false,
        'isGenerator' => false,
        'isVariadic' => false,
        'modifiers' => 1,
        'namespace' => 'Illuminate\\Database\\Schema',
        'declaringClassName' => 'Illuminate\\Database\\Schema\\Blueprint',
        'implementingClassName' => 'Illuminate\\Database\\Schema\\Blueprint',
        'currentClassName' => 'Illuminate\\Database\\Schema\\Blueprint',
        'aliasName' => NULL,
      ),
      'hasState' => 
      array (
        'name' => 'hasState',
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
 * Determine if the blueprint has state.
 *
 * @return bool
 */',
        'startLine' => 1976,
        'endLine' => 1979,
        'startColumn' => 5,
        'endColumn' => 5,
        'couldThrow' => false,
        'isClosure' => false,
        'isGenerator' => false,
        'isVariadic' => false,
        'modifiers' => 4,
        'namespace' => 'Illuminate\\Database\\Schema',
        'declaringClassName' => 'Illuminate\\Database\\Schema\\Blueprint',
        'implementingClassName' => 'Illuminate\\Database\\Schema\\Blueprint',
        'currentClassName' => 'Illuminate\\Database\\Schema\\Blueprint',
        'aliasName' => NULL,
      ),
      'getState' => 
      array (
        'name' => 'getState',
        'parameters' => 
        array (
        ),
        'returnsReference' => false,
        'returnType' => NULL,
        'attributes' => 
        array (
        ),
        'docComment' => '/**
 * Get the state of the blueprint.
 *
 * @return \\Illuminate\\Database\\Schema\\BlueprintState
 */',
        'startLine' => 1986,
        'endLine' => 1989,
        'startColumn' => 5,
        'endColumn' => 5,
        'couldThrow' => false,
        'isClosure' => false,
        'isGenerator' => false,
        'isVariadic' => false,
        'modifiers' => 1,
        'namespace' => 'Illuminate\\Database\\Schema',
        'declaringClassName' => 'Illuminate\\Database\\Schema\\Blueprint',
        'implementingClassName' => 'Illuminate\\Database\\Schema\\Blueprint',
        'currentClassName' => 'Illuminate\\Database\\Schema\\Blueprint',
        'aliasName' => NULL,
      ),
      'getAddedColumns' => 
      array (
        'name' => 'getAddedColumns',
        'parameters' => 
        array (
        ),
        'returnsReference' => false,
        'returnType' => NULL,
        'attributes' => 
        array (
        ),
        'docComment' => '/**
 * Get the columns on the blueprint that should be added.
 *
 * @return \\Illuminate\\Database\\Schema\\ColumnDefinition[]
 */',
        'startLine' => 1996,
        'endLine' => 2001,
        'startColumn' => 5,
        'endColumn' => 5,
        'couldThrow' => false,
        'isClosure' => false,
        'isGenerator' => false,
        'isVariadic' => false,
        'modifiers' => 1,
        'namespace' => 'Illuminate\\Database\\Schema',
        'declaringClassName' => 'Illuminate\\Database\\Schema\\Blueprint',
        'implementingClassName' => 'Illuminate\\Database\\Schema\\Blueprint',
        'currentClassName' => 'Illuminate\\Database\\Schema\\Blueprint',
        'aliasName' => NULL,
      ),
      'getChangedColumns' => 
      array (
        'name' => 'getChangedColumns',
        'parameters' => 
        array (
        ),
        'returnsReference' => false,
        'returnType' => NULL,
        'attributes' => 
        array (
        ),
        'docComment' => '/**
 * Get the columns on the blueprint that should be changed.
 *
 * @deprecated Will be removed in a future Laravel version.
 *
 * @return \\Illuminate\\Database\\Schema\\ColumnDefinition[]
 */',
        'startLine' => 2010,
        'endLine' => 2015,
        'startColumn' => 5,
        'endColumn' => 5,
        'couldThrow' => false,
        'isClosure' => false,
        'isGenerator' => false,
        'isVariadic' => false,
        'modifiers' => 1,
        'namespace' => 'Illuminate\\Database\\Schema',
        'declaringClassName' => 'Illuminate\\Database\\Schema\\Blueprint',
        'implementingClassName' => 'Illuminate\\Database\\Schema\\Blueprint',
        'currentClassName' => 'Illuminate\\Database\\Schema\\Blueprint',
        'aliasName' => NULL,
      ),
      'defaultTimePrecision' => 
      array (
        'name' => 'defaultTimePrecision',
        'parameters' => 
        array (
        ),
        'returnsReference' => false,
        'returnType' => 
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
                  'name' => 'int',
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
        'attributes' => 
        array (
        ),
        'docComment' => '/**
 * Get the default time precision.
 */',
        'startLine' => 2020,
        'endLine' => 2023,
        'startColumn' => 5,
        'endColumn' => 5,
        'couldThrow' => false,
        'isClosure' => false,
        'isGenerator' => false,
        'isVariadic' => false,
        'modifiers' => 2,
        'namespace' => 'Illuminate\\Database\\Schema',
        'declaringClassName' => 'Illuminate\\Database\\Schema\\Blueprint',
        'implementingClassName' => 'Illuminate\\Database\\Schema\\Blueprint',
        'currentClassName' => 'Illuminate\\Database\\Schema\\Blueprint',
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