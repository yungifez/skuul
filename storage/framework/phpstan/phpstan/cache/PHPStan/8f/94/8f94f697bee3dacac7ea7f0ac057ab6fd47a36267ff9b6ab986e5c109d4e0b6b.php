<?php declare(strict_types = 1);

// odsl-/var/www/html/app/Models/CalendarTemplate.php-PHPStan\BetterReflection\Reflection\ReflectionClass-App\Models\CalendarTemplate
return \PHPStan\Cache\CacheItem::__set_state(array(
   'variableKey' => 'v2-6.70.0.3-8.5.9-0315c9661d29b3ffa705bbf10cce4a0076d558c8d4a3c6cb4b2c8511ea8d9b7c',
   'data' => 
  array (
    'locatedSource' => 
    array (
      'class' => 'PHPStan\\BetterReflection\\SourceLocator\\Located\\LocatedSource',
      'data' => 
      array (
        'name' => 'App\\Models\\CalendarTemplate',
        'filename' => '/var/www/html/app/Models/CalendarTemplate.php',
      ),
    ),
    'namespace' => 'App\\Models',
    'name' => 'App\\Models\\CalendarTemplate',
    'shortName' => 'CalendarTemplate',
    'isInterface' => false,
    'isTrait' => false,
    'isEnum' => false,
    'isBackedEnum' => false,
    'modifiers' => 0,
    'docComment' => '/**
 * A description of how an organization divides its academic year.
 *
 * The template holds shape, not dates: how many periods, in what order, of
 * what kind, and how long each runs. A campus generates a cycle from it by
 * naming one start date.
 *
 * @property string $name
 * @property string|null $description
 * @property bool $is_default
 * @property int $cycle_length_days
 * @property int $organization_id
 */',
    'attributes' => 
    array (
    ),
    'startLine' => 23,
    'endLine' => 112,
    'startColumn' => 1,
    'endColumn' => 1,
    'parentClassName' => 'Illuminate\\Database\\Eloquent\\Model',
    'implementsClassNames' => 
    array (
    ),
    'traitClassNames' => 
    array (
      0 => 'Illuminate\\Database\\Eloquent\\Factories\\HasFactory',
    ),
    'immediateConstants' => 
    array (
    ),
    'immediateProperties' => 
    array (
      'fillable' => 
      array (
        'declaringClassName' => 'App\\Models\\CalendarTemplate',
        'implementingClassName' => 'App\\Models\\CalendarTemplate',
        'name' => 'fillable',
        'modifiers' => 2,
        'type' => NULL,
        'default' => 
        array (
          'code' => '[\'organization_id\', \'name\', \'description\', \'is_default\', \'cycle_length_days\', \'auto_open\', \'generate_ahead_weeks\', \'remind_days_before\', \'created_by\']',
          'attributes' => 
          array (
            'startLine' => 27,
            'endLine' => 37,
            'startTokenPos' => 50,
            'startFilePos' => 741,
            'endTokenPos' => 79,
            'endFilePos' => 969,
          ),
        ),
        'docComment' => NULL,
        'attributes' => 
        array (
        ),
        'startLine' => 27,
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
      'casts' => 
      array (
        'declaringClassName' => 'App\\Models\\CalendarTemplate',
        'implementingClassName' => 'App\\Models\\CalendarTemplate',
        'name' => 'casts',
        'modifiers' => 2,
        'type' => NULL,
        'default' => 
        array (
          'code' => '[\'is_default\' => \'boolean\', \'cycle_length_days\' => \'integer\', \'auto_open\' => \'boolean\', \'generate_ahead_weeks\' => \'integer\', \'remind_days_before\' => \'integer\']',
          'attributes' => 
          array (
            'startLine' => 44,
            'endLine' => 50,
            'startTokenPos' => 90,
            'startFilePos' => 1096,
            'endTokenPos' => 127,
            'endFilePos' => 1301,
          ),
        ),
        'docComment' => '/**
 * The attributes that should be cast.
 *
 * @var array<string, string>
 */',
        'attributes' => 
        array (
        ),
        'startLine' => 44,
        'endLine' => 50,
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
      'generatesAhead' => 
      array (
        'name' => 'generatesAhead',
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
 * Check if the next cycle is built without anyone asking.
 */',
        'startLine' => 55,
        'endLine' => 58,
        'startColumn' => 5,
        'endColumn' => 5,
        'couldThrow' => false,
        'isClosure' => false,
        'isGenerator' => false,
        'isVariadic' => false,
        'modifiers' => 1,
        'namespace' => 'App\\Models',
        'declaringClassName' => 'App\\Models\\CalendarTemplate',
        'implementingClassName' => 'App\\Models\\CalendarTemplate',
        'currentClassName' => 'App\\Models\\CalendarTemplate',
        'aliasName' => NULL,
      ),
      'organization' => 
      array (
        'name' => 'organization',
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
 * Get the organization that owns the template.
 *
 * @return BelongsTo<Organization, $this>
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
        'declaringClassName' => 'App\\Models\\CalendarTemplate',
        'implementingClassName' => 'App\\Models\\CalendarTemplate',
        'currentClassName' => 'App\\Models\\CalendarTemplate',
        'aliasName' => NULL,
      ),
      'periods' => 
      array (
        'name' => 'periods',
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
 * Get every period in the template, sub-periods included, in order.
 *
 * @return HasMany<CalendarTemplatePeriod, $this>
 */',
        'startLine' => 75,
        'endLine' => 78,
        'startColumn' => 5,
        'endColumn' => 5,
        'couldThrow' => false,
        'isClosure' => false,
        'isGenerator' => false,
        'isVariadic' => false,
        'modifiers' => 1,
        'namespace' => 'App\\Models',
        'declaringClassName' => 'App\\Models\\CalendarTemplate',
        'implementingClassName' => 'App\\Models\\CalendarTemplate',
        'currentClassName' => 'App\\Models\\CalendarTemplate',
        'aliasName' => NULL,
      ),
      'topLevelPeriods' => 
      array (
        'name' => 'topLevelPeriods',
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
 * Get the periods that divide the cycle, without their sub-periods.
 *
 * @return HasMany<CalendarTemplatePeriod, $this>
 */',
        'startLine' => 85,
        'endLine' => 88,
        'startColumn' => 5,
        'endColumn' => 5,
        'couldThrow' => false,
        'isClosure' => false,
        'isGenerator' => false,
        'isVariadic' => false,
        'modifiers' => 1,
        'namespace' => 'App\\Models',
        'declaringClassName' => 'App\\Models\\CalendarTemplate',
        'implementingClassName' => 'App\\Models\\CalendarTemplate',
        'currentClassName' => 'App\\Models\\CalendarTemplate',
        'aliasName' => NULL,
      ),
      'createdBy' => 
      array (
        'name' => 'createdBy',
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
 * Get the person who created the template.
 *
 * @return BelongsTo<User, $this>
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
        'namespace' => 'App\\Models',
        'declaringClassName' => 'App\\Models\\CalendarTemplate',
        'implementingClassName' => 'App\\Models\\CalendarTemplate',
        'currentClassName' => 'App\\Models\\CalendarTemplate',
        'aliasName' => NULL,
      ),
      'schools' => 
      array (
        'name' => 'schools',
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
 * Get the campuses that follow this template by name.
 *
 * A campus that follows the organization default is not listed here: it
 * has no template of its own.
 *
 * @return HasMany<School, $this>
 */',
        'startLine' => 108,
        'endLine' => 111,
        'startColumn' => 5,
        'endColumn' => 5,
        'couldThrow' => false,
        'isClosure' => false,
        'isGenerator' => false,
        'isVariadic' => false,
        'modifiers' => 1,
        'namespace' => 'App\\Models',
        'declaringClassName' => 'App\\Models\\CalendarTemplate',
        'implementingClassName' => 'App\\Models\\CalendarTemplate',
        'currentClassName' => 'App\\Models\\CalendarTemplate',
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