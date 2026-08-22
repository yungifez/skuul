<?php declare(strict_types = 1);

// osfsl-/var/www/html/app/Models/AcademicCycleSection.php-PHPStan\BetterReflection\Reflection\ReflectionClass-App\Models\AcademicCycleSection
return \PHPStan\Cache\CacheItem::__set_state(array(
   'variableKey' => 'v2-ee4d9f2e42f49cefa00c12af57e2c58ece3ab0be3549007badb95a49dbb5059e-8.5.9-6.70.0.3',
   'data' => 
  array (
    'locatedSource' => 
    array (
      'class' => 'PHPStan\\BetterReflection\\SourceLocator\\Located\\LocatedSource',
      'data' => 
      array (
        'name' => 'App\\Models\\AcademicCycleSection',
        'filename' => '/var/www/html/app/Models/AcademicCycleSection.php',
      ),
    ),
    'namespace' => 'App\\Models',
    'name' => 'App\\Models\\AcademicCycleSection',
    'shortName' => 'AcademicCycleSection',
    'isInterface' => false,
    'isTrait' => false,
    'isEnum' => false,
    'isBackedEnum' => false,
    'modifiers' => 0,
    'docComment' => '/**
 * A home section that exists for one exact academic cycle.
 *
 * A section is never reused for another cycle. The optional legacy section
 * identifies the old record it was created alongside, without changing past
 * placement, timetable, attendance, or examination records.
 *
 * @property AcademicStructureStatus $status
 */',
    'attributes' => 
    array (
    ),
    'startLine' => 21,
    'endLine' => 101,
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
        'declaringClassName' => 'App\\Models\\AcademicCycleSection',
        'implementingClassName' => 'App\\Models\\AcademicCycleSection',
        'name' => 'fillable',
        'modifiers' => 2,
        'type' => NULL,
        'default' => 
        array (
          'code' => '[\'school_id\', \'academic_year_id\', \'academic_level_id\', \'legacy_section_id\', \'homeroom_teacher_id\', \'name\', \'label\', \'stream\', \'shift\', \'language\', \'room\', \'capacity\', \'position\', \'status\']',
          'attributes' => 
          array (
            'startLine' => 28,
            'endLine' => 43,
            'startTokenPos' => 67,
            'startFilePos' => 794,
            'endTokenPos' => 111,
            'endFilePos' => 1100,
          ),
        ),
        'docComment' => NULL,
        'attributes' => 
        array (
        ),
        'startLine' => 28,
        'endLine' => 43,
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
        'declaringClassName' => 'App\\Models\\AcademicCycleSection',
        'implementingClassName' => 'App\\Models\\AcademicCycleSection',
        'name' => 'attributes',
        'modifiers' => 2,
        'type' => NULL,
        'default' => 
        array (
          'code' => '[\'status\' => \\App\\Enums\\AcademicStructureStatus::Draft->value, \'position\' => 0]',
          'attributes' => 
          array (
            'startLine' => 48,
            'endLine' => 51,
            'startTokenPos' => 122,
            'startFilePos' => 1181,
            'endTokenPos' => 142,
            'endFilePos' => 1271,
          ),
        ),
        'docComment' => '/**
 * @var array<string, mixed>
 */',
        'attributes' => 
        array (
        ),
        'startLine' => 48,
        'endLine' => 51,
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
        'declaringClassName' => 'App\\Models\\AcademicCycleSection',
        'implementingClassName' => 'App\\Models\\AcademicCycleSection',
        'name' => 'casts',
        'modifiers' => 2,
        'type' => NULL,
        'default' => 
        array (
          'code' => '[\'capacity\' => \'integer\', \'position\' => \'integer\', \'status\' => \\App\\Enums\\AcademicStructureStatus::class]',
          'attributes' => 
          array (
            'startLine' => 56,
            'endLine' => 60,
            'startTokenPos' => 153,
            'startFilePos' => 1348,
            'endTokenPos' => 178,
            'endFilePos' => 1472,
          ),
        ),
        'docComment' => '/**
 * @var array<string, string>
 */',
        'attributes' => 
        array (
        ),
        'startLine' => 56,
        'endLine' => 60,
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
        'startLine' => 65,
        'endLine' => 68,
        'startColumn' => 5,
        'endColumn' => 5,
        'couldThrow' => false,
        'isClosure' => false,
        'isGenerator' => false,
        'isVariadic' => false,
        'modifiers' => 1,
        'namespace' => 'App\\Models',
        'declaringClassName' => 'App\\Models\\AcademicCycleSection',
        'implementingClassName' => 'App\\Models\\AcademicCycleSection',
        'currentClassName' => 'App\\Models\\AcademicCycleSection',
        'aliasName' => NULL,
      ),
      'academicYear' => 
      array (
        'name' => 'academicYear',
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
 * @return BelongsTo<AcademicYear, $this>
 */',
        'startLine' => 73,
        'endLine' => 76,
        'startColumn' => 5,
        'endColumn' => 5,
        'couldThrow' => false,
        'isClosure' => false,
        'isGenerator' => false,
        'isVariadic' => false,
        'modifiers' => 1,
        'namespace' => 'App\\Models',
        'declaringClassName' => 'App\\Models\\AcademicCycleSection',
        'implementingClassName' => 'App\\Models\\AcademicCycleSection',
        'currentClassName' => 'App\\Models\\AcademicCycleSection',
        'aliasName' => NULL,
      ),
      'academicLevel' => 
      array (
        'name' => 'academicLevel',
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
        'startLine' => 81,
        'endLine' => 84,
        'startColumn' => 5,
        'endColumn' => 5,
        'couldThrow' => false,
        'isClosure' => false,
        'isGenerator' => false,
        'isVariadic' => false,
        'modifiers' => 1,
        'namespace' => 'App\\Models',
        'declaringClassName' => 'App\\Models\\AcademicCycleSection',
        'implementingClassName' => 'App\\Models\\AcademicCycleSection',
        'currentClassName' => 'App\\Models\\AcademicCycleSection',
        'aliasName' => NULL,
      ),
      'legacySection' => 
      array (
        'name' => 'legacySection',
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
 * @return BelongsTo<Section, $this>
 */',
        'startLine' => 89,
        'endLine' => 92,
        'startColumn' => 5,
        'endColumn' => 5,
        'couldThrow' => false,
        'isClosure' => false,
        'isGenerator' => false,
        'isVariadic' => false,
        'modifiers' => 1,
        'namespace' => 'App\\Models',
        'declaringClassName' => 'App\\Models\\AcademicCycleSection',
        'implementingClassName' => 'App\\Models\\AcademicCycleSection',
        'currentClassName' => 'App\\Models\\AcademicCycleSection',
        'aliasName' => NULL,
      ),
      'homeroomTeacher' => 
      array (
        'name' => 'homeroomTeacher',
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
 * @return BelongsTo<User, $this>
 */',
        'startLine' => 97,
        'endLine' => 100,
        'startColumn' => 5,
        'endColumn' => 5,
        'couldThrow' => false,
        'isClosure' => false,
        'isGenerator' => false,
        'isVariadic' => false,
        'modifiers' => 1,
        'namespace' => 'App\\Models',
        'declaringClassName' => 'App\\Models\\AcademicCycleSection',
        'implementingClassName' => 'App\\Models\\AcademicCycleSection',
        'currentClassName' => 'App\\Models\\AcademicCycleSection',
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