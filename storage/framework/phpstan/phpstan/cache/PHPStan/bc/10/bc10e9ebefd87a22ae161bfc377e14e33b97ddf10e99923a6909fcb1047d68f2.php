<?php declare(strict_types = 1);

// osfsl-/var/www/html/app/Models/AcademicLevel.php-PHPStan\BetterReflection\Reflection\ReflectionClass-App\Models\AcademicLevel
return \PHPStan\Cache\CacheItem::__set_state(array(
   'variableKey' => 'v2-e131ecf8a8765944ed509131cf4ac332a8ebbc2874eb3e8071038d0ba978b87d-8.5.9-6.70.0.3',
   'data' => 
  array (
    'locatedSource' => 
    array (
      'class' => 'PHPStan\\BetterReflection\\SourceLocator\\Located\\LocatedSource',
      'data' => 
      array (
        'name' => 'App\\Models\\AcademicLevel',
        'filename' => '/var/www/html/app/Models/AcademicLevel.php',
      ),
    ),
    'namespace' => 'App\\Models',
    'name' => 'App\\Models\\AcademicLevel',
    'shortName' => 'AcademicLevel',
    'isInterface' => false,
    'isTrait' => false,
    'isEnum' => false,
    'isBackedEnum' => false,
    'modifiers' => 0,
    'docComment' => '/**
 * A reusable school level, such as Primary 4, Grade 4, or Form 2.
 *
 * The legacy class link is a compatibility bridge. New operational records
 * use this model, while existing records keep their original class reference.
 *
 * @property AcademicStructureStatus $status
 */',
    'attributes' => 
    array (
    ),
    'startLine' => 21,
    'endLine' => 94,
    'startColumn' => 1,
    'endColumn' => 1,
    'parentClassName' => 'Illuminate\\Database\\Eloquent\\Model',
    'implementsClassNames' => 
    array (
    ),
    'traitClassNames' => 
    array (
      0 => 'Illuminate\\Database\\Eloquent\\Factories\\HasFactory',
      1 => 'App\\Traits\\InSchool',
    ),
    'immediateConstants' => 
    array (
    ),
    'immediateProperties' => 
    array (
      'fillable' => 
      array (
        'declaringClassName' => 'App\\Models\\AcademicLevel',
        'implementingClassName' => 'App\\Models\\AcademicLevel',
        'name' => 'fillable',
        'modifiers' => 2,
        'type' => NULL,
        'default' => 
        array (
          'code' => '[\'school_id\', \'legacy_my_class_id\', \'parent_id\', \'name\', \'label\', \'code\', \'position\', \'status\']',
          'attributes' => 
          array (
            'startLine' => 28,
            'endLine' => 37,
            'startTokenPos' => 72,
            'startFilePos' => 773,
            'endTokenPos' => 98,
            'endFilePos' => 938,
          ),
        ),
        'docComment' => NULL,
        'attributes' => 
        array (
        ),
        'startLine' => 28,
        'endLine' => 37,
        'startColumn' => 5,
        'endColumn' => 6,
        'isPromoted' => false,
        'declaredAtCompileTime' => true,
        'immediateVirtual' => false,
        'immediateHooks' => 
        array (
        ),
      ),
      'attributes' => 
      array (
        'declaringClassName' => 'App\\Models\\AcademicLevel',
        'implementingClassName' => 'App\\Models\\AcademicLevel',
        'name' => 'attributes',
        'modifiers' => 2,
        'type' => NULL,
        'default' => 
        array (
          'code' => '[\'status\' => \\App\\Enums\\AcademicStructureStatus::Active->value, \'position\' => 0]',
          'attributes' => 
          array (
            'startLine' => 42,
            'endLine' => 45,
            'startTokenPos' => 109,
            'startFilePos' => 1019,
            'endTokenPos' => 129,
            'endFilePos' => 1110,
          ),
        ),
        'docComment' => '/**
 * @var array<string, mixed>
 */',
        'attributes' => 
        array (
        ),
        'startLine' => 42,
        'endLine' => 45,
        'startColumn' => 5,
        'endColumn' => 6,
        'isPromoted' => false,
        'declaredAtCompileTime' => true,
        'immediateVirtual' => false,
        'immediateHooks' => 
        array (
        ),
      ),
      'casts' => 
      array (
        'declaringClassName' => 'App\\Models\\AcademicLevel',
        'implementingClassName' => 'App\\Models\\AcademicLevel',
        'name' => 'casts',
        'modifiers' => 2,
        'type' => NULL,
        'default' => 
        array (
          'code' => '[\'position\' => \'integer\', \'status\' => \\App\\Enums\\AcademicStructureStatus::class]',
          'attributes' => 
          array (
            'startLine' => 50,
            'endLine' => 53,
            'startTokenPos' => 140,
            'startFilePos' => 1187,
            'endTokenPos' => 158,
            'endFilePos' => 1278,
          ),
        ),
        'docComment' => '/**
 * @var array<string, string>
 */',
        'attributes' => 
        array (
        ),
        'startLine' => 50,
        'endLine' => 53,
        'startColumn' => 5,
        'endColumn' => 6,
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
      'school' => 
      array (
        'name' => 'school',
        'parameters' => 
        array (
        ),
        'returnsReference' => false,
        'returnType' => 
        array (
          'class' => 'PHPStan\\BetterReflection\\Reflection\\ReflectionNamedType',
          'data' => 
          array (
            'name' => 'Illuminate\\Database\\Eloquent\\Relations\\BelongsTo',
            'isIdentifier' => false,
          ),
        ),
        'attributes' => 
        array (
        ),
        'docComment' => '/**
 * @return BelongsTo<School, $this>
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
        'namespace' => 'App\\Models',
        'declaringClassName' => 'App\\Models\\AcademicLevel',
        'implementingClassName' => 'App\\Models\\AcademicLevel',
        'currentClassName' => 'App\\Models\\AcademicLevel',
        'aliasName' => NULL,
      ),
      'legacyMyClass' => 
      array (
        'name' => 'legacyMyClass',
        'parameters' => 
        array (
        ),
        'returnsReference' => false,
        'returnType' => 
        array (
          'class' => 'PHPStan\\BetterReflection\\Reflection\\ReflectionNamedType',
          'data' => 
          array (
            'name' => 'Illuminate\\Database\\Eloquent\\Relations\\BelongsTo',
            'isIdentifier' => false,
          ),
        ),
        'attributes' => 
        array (
        ),
        'docComment' => '/**
 * @return BelongsTo<MyClass, $this>
 */',
        'startLine' => 66,
        'endLine' => 69,
        'startColumn' => 5,
        'endColumn' => 5,
        'couldThrow' => false,
        'isClosure' => false,
        'isGenerator' => false,
        'isVariadic' => false,
        'modifiers' => 1,
        'namespace' => 'App\\Models',
        'declaringClassName' => 'App\\Models\\AcademicLevel',
        'implementingClassName' => 'App\\Models\\AcademicLevel',
        'currentClassName' => 'App\\Models\\AcademicLevel',
        'aliasName' => NULL,
      ),
      'parent' => 
      array (
        'name' => 'parent',
        'parameters' => 
        array (
        ),
        'returnsReference' => false,
        'returnType' => 
        array (
          'class' => 'PHPStan\\BetterReflection\\Reflection\\ReflectionNamedType',
          'data' => 
          array (
            'name' => 'Illuminate\\Database\\Eloquent\\Relations\\BelongsTo',
            'isIdentifier' => false,
          ),
        ),
        'attributes' => 
        array (
        ),
        'docComment' => '/**
 * @return BelongsTo<AcademicLevel, $this>
 */',
        'startLine' => 74,
        'endLine' => 77,
        'startColumn' => 5,
        'endColumn' => 5,
        'couldThrow' => false,
        'isClosure' => false,
        'isGenerator' => false,
        'isVariadic' => false,
        'modifiers' => 1,
        'namespace' => 'App\\Models',
        'declaringClassName' => 'App\\Models\\AcademicLevel',
        'implementingClassName' => 'App\\Models\\AcademicLevel',
        'currentClassName' => 'App\\Models\\AcademicLevel',
        'aliasName' => NULL,
      ),
      'children' => 
      array (
        'name' => 'children',
        'parameters' => 
        array (
        ),
        'returnsReference' => false,
        'returnType' => 
        array (
          'class' => 'PHPStan\\BetterReflection\\Reflection\\ReflectionNamedType',
          'data' => 
          array (
            'name' => 'Illuminate\\Database\\Eloquent\\Relations\\HasMany',
            'isIdentifier' => false,
          ),
        ),
        'attributes' => 
        array (
        ),
        'docComment' => '/**
 * @return HasMany<AcademicLevel, $this>
 */',
        'startLine' => 82,
        'endLine' => 85,
        'startColumn' => 5,
        'endColumn' => 5,
        'couldThrow' => false,
        'isClosure' => false,
        'isGenerator' => false,
        'isVariadic' => false,
        'modifiers' => 1,
        'namespace' => 'App\\Models',
        'declaringClassName' => 'App\\Models\\AcademicLevel',
        'implementingClassName' => 'App\\Models\\AcademicLevel',
        'currentClassName' => 'App\\Models\\AcademicLevel',
        'aliasName' => NULL,
      ),
      'cycleSections' => 
      array (
        'name' => 'cycleSections',
        'parameters' => 
        array (
        ),
        'returnsReference' => false,
        'returnType' => 
        array (
          'class' => 'PHPStan\\BetterReflection\\Reflection\\ReflectionNamedType',
          'data' => 
          array (
            'name' => 'Illuminate\\Database\\Eloquent\\Relations\\HasMany',
            'isIdentifier' => false,
          ),
        ),
        'attributes' => 
        array (
        ),
        'docComment' => '/**
 * @return HasMany<AcademicCycleSection, $this>
 */',
        'startLine' => 90,
        'endLine' => 93,
        'startColumn' => 5,
        'endColumn' => 5,
        'couldThrow' => false,
        'isClosure' => false,
        'isGenerator' => false,
        'isVariadic' => false,
        'modifiers' => 1,
        'namespace' => 'App\\Models',
        'declaringClassName' => 'App\\Models\\AcademicLevel',
        'implementingClassName' => 'App\\Models\\AcademicLevel',
        'currentClassName' => 'App\\Models\\AcademicLevel',
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