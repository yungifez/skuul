<?php declare(strict_types = 1);

// osfsl-/var/www/html/vendor/composer/../laravel/framework/src/Illuminate/Database/Eloquent/Model.php-PHPStan\BetterReflection\Reflection\ReflectionClass-Illuminate\Database\Eloquent\Model
return \PHPStan\Cache\CacheItem::__set_state(array(
   'variableKey' => 'v2-7862a9d25f55a2e211e978641d7f0d051386fd9cdcfaeaef16f85bb75bedba3f-8.5.9-6.70.0.3',
   'data' => 
  array (
    'locatedSource' => 
    array (
      'class' => 'PHPStan\\BetterReflection\\SourceLocator\\Located\\LocatedSource',
      'data' => 
      array (
        'name' => 'Illuminate\\Database\\Eloquent\\Model',
        'filename' => '/var/www/html/vendor/composer/../laravel/framework/src/Illuminate/Database/Eloquent/Model.php',
      ),
    ),
    'namespace' => 'Illuminate\\Database\\Eloquent',
    'name' => 'Illuminate\\Database\\Eloquent\\Model',
    'shortName' => 'Model',
    'isInterface' => false,
    'isTrait' => false,
    'isEnum' => false,
    'isBackedEnum' => false,
    'modifiers' => 64,
    'docComment' => NULL,
    'attributes' => 
    array (
    ),
    'startLine' => 44,
    'endLine' => 2930,
    'startColumn' => 1,
    'endColumn' => 1,
    'parentClassName' => NULL,
    'implementsClassNames' => 
    array (
      0 => 'Illuminate\\Contracts\\Support\\Arrayable',
      1 => 'ArrayAccess',
      2 => 'Illuminate\\Contracts\\Support\\CanBeEscapedWhenCastToString',
      3 => 'Illuminate\\Contracts\\Broadcasting\\HasBroadcastChannel',
      4 => 'Illuminate\\Contracts\\Support\\Jsonable',
      5 => 'JsonSerializable',
      6 => 'Illuminate\\Contracts\\Queue\\QueueableEntity',
      7 => 'Stringable',
      8 => 'Illuminate\\Contracts\\Routing\\UrlRoutable',
    ),
    'traitClassNames' => 
    array (
      0 => 'Illuminate\\Database\\Eloquent\\Concerns\\HasAttributes',
      1 => 'Illuminate\\Database\\Eloquent\\Concerns\\HasEvents',
      2 => 'Illuminate\\Database\\Eloquent\\Concerns\\HasGlobalScopes',
      3 => 'Illuminate\\Database\\Eloquent\\Concerns\\HasRelationships',
      4 => 'Illuminate\\Database\\Eloquent\\Concerns\\HasTimestamps',
      5 => 'Illuminate\\Database\\Eloquent\\Concerns\\HasUniqueIds',
      6 => 'Illuminate\\Database\\Eloquent\\Concerns\\HidesAttributes',
      7 => 'Illuminate\\Database\\Eloquent\\Concerns\\GuardsAttributes',
      8 => 'Illuminate\\Database\\Eloquent\\Concerns\\PreventsCircularRecursion',
      9 => 'Illuminate\\Database\\Eloquent\\Concerns\\TransformsToResource',
      10 => 'Illuminate\\Support\\Traits\\ForwardsCalls',
      11 => 'Illuminate\\Database\\Eloquent\\HasCollection',
    ),
    'immediateConstants' => 
    array (
      'CREATED_AT' => 
      array (
        'declaringClassName' => 'Illuminate\\Database\\Eloquent\\Model',
        'implementingClassName' => 'Illuminate\\Database\\Eloquent\\Model',
        'name' => 'CREATED_AT',
        'modifiers' => 1,
        'type' => NULL,
        'value' => 
        array (
          'code' => '\'created_at\'',
          'attributes' => 
          array (
            'startLine' => 303,
            'endLine' => 303,
            'startTokenPos' => 703,
            'startFilePos' => 7942,
            'endTokenPos' => 703,
            'endFilePos' => 7953,
          ),
        ),
        'docComment' => '/**
 * The name of the "created at" column.
 *
 * @var string|null
 */',
        'attributes' => 
        array (
        ),
        'startLine' => 303,
        'endLine' => 303,
        'startColumn' => 5,
        'endColumn' => 36,
      ),
      'UPDATED_AT' => 
      array (
        'declaringClassName' => 'Illuminate\\Database\\Eloquent\\Model',
        'implementingClassName' => 'Illuminate\\Database\\Eloquent\\Model',
        'name' => 'UPDATED_AT',
        'modifiers' => 1,
        'type' => NULL,
        'value' => 
        array (
          'code' => '\'updated_at\'',
          'attributes' => 
          array (
            'startLine' => 310,
            'endLine' => 310,
            'startTokenPos' => 714,
            'startFilePos' => 8071,
            'endTokenPos' => 714,
            'endFilePos' => 8082,
          ),
        ),
        'docComment' => '/**
 * The name of the "updated at" column.
 *
 * @var string|null
 */',
        'attributes' => 
        array (
        ),
        'startLine' => 310,
        'endLine' => 310,
        'startColumn' => 5,
        'endColumn' => 36,
      ),
    ),
    'immediateProperties' => 
    array (
      'connection' => 
      array (
        'declaringClassName' => 'Illuminate\\Database\\Eloquent\\Model',
        'implementingClassName' => 'Illuminate\\Database\\Eloquent\\Model',
        'name' => 'connection',
        'modifiers' => 2,
        'type' => NULL,
        'default' => NULL,
        'docComment' => '/**
 * The connection name for the model.
 *
 * @var \\UnitEnum|string|null
 */',
        'attributes' => 
        array (
        ),
        'startLine' => 65,
        'endLine' => 65,
        'startColumn' => 5,
        'endColumn' => 26,
        'isPromoted' => false,
        'declaredAtCompileTime' => true,
        'immediateVirtual' => false,
        'immediateHooks' => 
        array (
        ),
      ),
      'table' => 
      array (
        'declaringClassName' => 'Illuminate\\Database\\Eloquent\\Model',
        'implementingClassName' => 'Illuminate\\Database\\Eloquent\\Model',
        'name' => 'table',
        'modifiers' => 2,
        'type' => NULL,
        'default' => NULL,
        'docComment' => '/**
 * The table associated with the model.
 *
 * @var string|null
 */',
        'attributes' => 
        array (
        ),
        'startLine' => 72,
        'endLine' => 72,
        'startColumn' => 5,
        'endColumn' => 21,
        'isPromoted' => false,
        'declaredAtCompileTime' => true,
        'immediateVirtual' => false,
        'immediateHooks' => 
        array (
        ),
      ),
      'primaryKey' => 
      array (
        'declaringClassName' => 'Illuminate\\Database\\Eloquent\\Model',
        'implementingClassName' => 'Illuminate\\Database\\Eloquent\\Model',
        'name' => 'primaryKey',
        'modifiers' => 2,
        'type' => NULL,
        'default' => 
        array (
          'code' => '\'id\'',
          'attributes' => 
          array (
            'startLine' => 79,
            'endLine' => 79,
            'startTokenPos' => 314,
            'startFilePos' => 2724,
            'endTokenPos' => 314,
            'endFilePos' => 2727,
          ),
        ),
        'docComment' => '/**
 * The primary key for the model.
 *
 * @var string
 */',
        'attributes' => 
        array (
        ),
        'startLine' => 79,
        'endLine' => 79,
        'startColumn' => 5,
        'endColumn' => 33,
        'isPromoted' => false,
        'declaredAtCompileTime' => true,
        'immediateVirtual' => false,
        'immediateHooks' => 
        array (
        ),
      ),
      'keyType' => 
      array (
        'declaringClassName' => 'Illuminate\\Database\\Eloquent\\Model',
        'implementingClassName' => 'Illuminate\\Database\\Eloquent\\Model',
        'name' => 'keyType',
        'modifiers' => 2,
        'type' => NULL,
        'default' => 
        array (
          'code' => '\'int\'',
          'attributes' => 
          array (
            'startLine' => 86,
            'endLine' => 86,
            'startTokenPos' => 325,
            'startFilePos' => 2839,
            'endTokenPos' => 325,
            'endFilePos' => 2843,
          ),
        ),
        'docComment' => '/**
 * The "type" of the primary key ID.
 *
 * @var string
 */',
        'attributes' => 
        array (
        ),
        'startLine' => 86,
        'endLine' => 86,
        'startColumn' => 5,
        'endColumn' => 31,
        'isPromoted' => false,
        'declaredAtCompileTime' => true,
        'immediateVirtual' => false,
        'immediateHooks' => 
        array (
        ),
      ),
      'incrementing' => 
      array (
        'declaringClassName' => 'Illuminate\\Database\\Eloquent\\Model',
        'implementingClassName' => 'Illuminate\\Database\\Eloquent\\Model',
        'name' => 'incrementing',
        'modifiers' => 1,
        'type' => NULL,
        'default' => 
        array (
          'code' => 'true',
          'attributes' => 
          array (
            'startLine' => 93,
            'endLine' => 93,
            'startTokenPos' => 336,
            'startFilePos' => 2965,
            'endTokenPos' => 336,
            'endFilePos' => 2968,
          ),
        ),
        'docComment' => '/**
 * Indicates if the IDs are auto-incrementing.
 *
 * @var bool
 */',
        'attributes' => 
        array (
        ),
        'startLine' => 93,
        'endLine' => 93,
        'startColumn' => 5,
        'endColumn' => 32,
        'isPromoted' => false,
        'declaredAtCompileTime' => true,
        'immediateVirtual' => false,
        'immediateHooks' => 
        array (
        ),
      ),
      'with' => 
      array (
        'declaringClassName' => 'Illuminate\\Database\\Eloquent\\Model',
        'implementingClassName' => 'Illuminate\\Database\\Eloquent\\Model',
        'name' => 'with',
        'modifiers' => 2,
        'type' => NULL,
        'default' => 
        array (
          'code' => '[]',
          'attributes' => 
          array (
            'startLine' => 100,
            'endLine' => 100,
            'startTokenPos' => 347,
            'startFilePos' => 3086,
            'endTokenPos' => 348,
            'endFilePos' => 3087,
          ),
        ),
        'docComment' => '/**
 * The relations to eager load on every query.
 *
 * @var array
 */',
        'attributes' => 
        array (
        ),
        'startLine' => 100,
        'endLine' => 100,
        'startColumn' => 5,
        'endColumn' => 25,
        'isPromoted' => false,
        'declaredAtCompileTime' => true,
        'immediateVirtual' => false,
        'immediateHooks' => 
        array (
        ),
      ),
      'withCount' => 
      array (
        'declaringClassName' => 'Illuminate\\Database\\Eloquent\\Model',
        'implementingClassName' => 'Illuminate\\Database\\Eloquent\\Model',
        'name' => 'withCount',
        'modifiers' => 2,
        'type' => NULL,
        'default' => 
        array (
          'code' => '[]',
          'attributes' => 
          array (
            'startLine' => 107,
            'endLine' => 107,
            'startTokenPos' => 359,
            'startFilePos' => 3234,
            'endTokenPos' => 360,
            'endFilePos' => 3235,
          ),
        ),
        'docComment' => '/**
 * The relationship counts that should be eager loaded on every query.
 *
 * @var array
 */',
        'attributes' => 
        array (
        ),
        'startLine' => 107,
        'endLine' => 107,
        'startColumn' => 5,
        'endColumn' => 30,
        'isPromoted' => false,
        'declaredAtCompileTime' => true,
        'immediateVirtual' => false,
        'immediateHooks' => 
        array (
        ),
      ),
      'preventsLazyLoading' => 
      array (
        'declaringClassName' => 'Illuminate\\Database\\Eloquent\\Model',
        'implementingClassName' => 'Illuminate\\Database\\Eloquent\\Model',
        'name' => 'preventsLazyLoading',
        'modifiers' => 1,
        'type' => NULL,
        'default' => 
        array (
          'code' => 'false',
          'attributes' => 
          array (
            'startLine' => 114,
            'endLine' => 114,
            'startTokenPos' => 371,
            'startFilePos' => 3384,
            'endTokenPos' => 371,
            'endFilePos' => 3388,
          ),
        ),
        'docComment' => '/**
 * Indicates whether lazy loading will be prevented on this model.
 *
 * @var bool
 */',
        'attributes' => 
        array (
        ),
        'startLine' => 114,
        'endLine' => 114,
        'startColumn' => 5,
        'endColumn' => 40,
        'isPromoted' => false,
        'declaredAtCompileTime' => true,
        'immediateVirtual' => false,
        'immediateHooks' => 
        array (
        ),
      ),
      'perPage' => 
      array (
        'declaringClassName' => 'Illuminate\\Database\\Eloquent\\Model',
        'implementingClassName' => 'Illuminate\\Database\\Eloquent\\Model',
        'name' => 'perPage',
        'modifiers' => 2,
        'type' => NULL,
        'default' => 
        array (
          'code' => '15',
          'attributes' => 
          array (
            'startLine' => 121,
            'endLine' => 121,
            'startTokenPos' => 382,
            'startFilePos' => 3510,
            'endTokenPos' => 382,
            'endFilePos' => 3511,
          ),
        ),
        'docComment' => '/**
 * The number of models to return for pagination.
 *
 * @var int
 */',
        'attributes' => 
        array (
        ),
        'startLine' => 121,
        'endLine' => 121,
        'startColumn' => 5,
        'endColumn' => 28,
        'isPromoted' => false,
        'declaredAtCompileTime' => true,
        'immediateVirtual' => false,
        'immediateHooks' => 
        array (
        ),
      ),
      'exists' => 
      array (
        'declaringClassName' => 'Illuminate\\Database\\Eloquent\\Model',
        'implementingClassName' => 'Illuminate\\Database\\Eloquent\\Model',
        'name' => 'exists',
        'modifiers' => 1,
        'type' => NULL,
        'default' => 
        array (
          'code' => 'false',
          'attributes' => 
          array (
            'startLine' => 128,
            'endLine' => 128,
            'startTokenPos' => 393,
            'startFilePos' => 3614,
            'endTokenPos' => 393,
            'endFilePos' => 3618,
          ),
        ),
        'docComment' => '/**
 * Indicates if the model exists.
 *
 * @var bool
 */',
        'attributes' => 
        array (
        ),
        'startLine' => 128,
        'endLine' => 128,
        'startColumn' => 5,
        'endColumn' => 27,
        'isPromoted' => false,
        'declaredAtCompileTime' => true,
        'immediateVirtual' => false,
        'immediateHooks' => 
        array (
        ),
      ),
      'wasRecentlyCreated' => 
      array (
        'declaringClassName' => 'Illuminate\\Database\\Eloquent\\Model',
        'implementingClassName' => 'Illuminate\\Database\\Eloquent\\Model',
        'name' => 'wasRecentlyCreated',
        'modifiers' => 1,
        'type' => NULL,
        'default' => 
        array (
          'code' => 'false',
          'attributes' => 
          array (
            'startLine' => 135,
            'endLine' => 135,
            'startTokenPos' => 404,
            'startFilePos' => 3769,
            'endTokenPos' => 404,
            'endFilePos' => 3773,
          ),
        ),
        'docComment' => '/**
 * Indicates if the model was inserted during the object\'s lifecycle.
 *
 * @var bool
 */',
        'attributes' => 
        array (
        ),
        'startLine' => 135,
        'endLine' => 135,
        'startColumn' => 5,
        'endColumn' => 39,
        'isPromoted' => false,
        'declaredAtCompileTime' => true,
        'immediateVirtual' => false,
        'immediateHooks' => 
        array (
        ),
      ),
      'escapeWhenCastingToString' => 
      array (
        'declaringClassName' => 'Illuminate\\Database\\Eloquent\\Model',
        'implementingClassName' => 'Illuminate\\Database\\Eloquent\\Model',
        'name' => 'escapeWhenCastingToString',
        'modifiers' => 2,
        'type' => NULL,
        'default' => 
        array (
          'code' => 'false',
          'attributes' => 
          array (
            'startLine' => 142,
            'endLine' => 142,
            'startTokenPos' => 415,
            'startFilePos' => 3963,
            'endTokenPos' => 415,
            'endFilePos' => 3967,
          ),
        ),
        'docComment' => '/**
 * Indicates that the object\'s string representation should be escaped when __toString is invoked.
 *
 * @var bool
 */',
        'attributes' => 
        array (
        ),
        'startLine' => 142,
        'endLine' => 142,
        'startColumn' => 5,
        'endColumn' => 49,
        'isPromoted' => false,
        'declaredAtCompileTime' => true,
        'immediateVirtual' => false,
        'immediateHooks' => 
        array (
        ),
      ),
      'resolver' => 
      array (
        'declaringClassName' => 'Illuminate\\Database\\Eloquent\\Model',
        'implementingClassName' => 'Illuminate\\Database\\Eloquent\\Model',
        'name' => 'resolver',
        'modifiers' => 18,
        'type' => NULL,
        'default' => NULL,
        'docComment' => '/**
 * The connection resolver instance.
 *
 * @var \\Illuminate\\Database\\ConnectionResolverInterface
 */',
        'attributes' => 
        array (
        ),
        'startLine' => 149,
        'endLine' => 149,
        'startColumn' => 5,
        'endColumn' => 31,
        'isPromoted' => false,
        'declaredAtCompileTime' => true,
        'immediateVirtual' => false,
        'immediateHooks' => 
        array (
        ),
      ),
      'dispatcher' => 
      array (
        'declaringClassName' => 'Illuminate\\Database\\Eloquent\\Model',
        'implementingClassName' => 'Illuminate\\Database\\Eloquent\\Model',
        'name' => 'dispatcher',
        'modifiers' => 18,
        'type' => NULL,
        'default' => NULL,
        'docComment' => '/**
 * The event dispatcher instance.
 *
 * @var \\Illuminate\\Contracts\\Events\\Dispatcher|null
 */',
        'attributes' => 
        array (
        ),
        'startLine' => 156,
        'endLine' => 156,
        'startColumn' => 5,
        'endColumn' => 33,
        'isPromoted' => false,
        'declaredAtCompileTime' => true,
        'immediateVirtual' => false,
        'immediateHooks' => 
        array (
        ),
      ),
      'booting' => 
      array (
        'declaringClassName' => 'Illuminate\\Database\\Eloquent\\Model',
        'implementingClassName' => 'Illuminate\\Database\\Eloquent\\Model',
        'name' => 'booting',
        'modifiers' => 18,
        'type' => NULL,
        'default' => 
        array (
          'code' => '[]',
          'attributes' => 
          array (
            'startLine' => 163,
            'endLine' => 163,
            'startTokenPos' => 446,
            'startFilePos' => 4406,
            'endTokenPos' => 447,
            'endFilePos' => 4407,
          ),
        ),
        'docComment' => '/**
 * The models that are currently being booted.
 *
 * @var array
 */',
        'attributes' => 
        array (
        ),
        'startLine' => 163,
        'endLine' => 163,
        'startColumn' => 5,
        'endColumn' => 35,
        'isPromoted' => false,
        'declaredAtCompileTime' => true,
        'immediateVirtual' => false,
        'immediateHooks' => 
        array (
        ),
      ),
      'booted' => 
      array (
        'declaringClassName' => 'Illuminate\\Database\\Eloquent\\Model',
        'implementingClassName' => 'Illuminate\\Database\\Eloquent\\Model',
        'name' => 'booted',
        'modifiers' => 18,
        'type' => NULL,
        'default' => 
        array (
          'code' => '[]',
          'attributes' => 
          array (
            'startLine' => 170,
            'endLine' => 170,
            'startTokenPos' => 460,
            'startFilePos' => 4518,
            'endTokenPos' => 461,
            'endFilePos' => 4519,
          ),
        ),
        'docComment' => '/**
 * The array of booted models.
 *
 * @var array
 */',
        'attributes' => 
        array (
        ),
        'startLine' => 170,
        'endLine' => 170,
        'startColumn' => 5,
        'endColumn' => 34,
        'isPromoted' => false,
        'declaredAtCompileTime' => true,
        'immediateVirtual' => false,
        'immediateHooks' => 
        array (
        ),
      ),
      'bootedCallbacks' => 
      array (
        'declaringClassName' => 'Illuminate\\Database\\Eloquent\\Model',
        'implementingClassName' => 'Illuminate\\Database\\Eloquent\\Model',
        'name' => 'bootedCallbacks',
        'modifiers' => 18,
        'type' => NULL,
        'default' => 
        array (
          'code' => '[]',
          'attributes' => 
          array (
            'startLine' => 177,
            'endLine' => 177,
            'startTokenPos' => 474,
            'startFilePos' => 4677,
            'endTokenPos' => 475,
            'endFilePos' => 4678,
          ),
        ),
        'docComment' => '/**
 * The callbacks that should be executed after the model has booted.
 *
 * @var array
 */',
        'attributes' => 
        array (
        ),
        'startLine' => 177,
        'endLine' => 177,
        'startColumn' => 5,
        'endColumn' => 43,
        'isPromoted' => false,
        'declaredAtCompileTime' => true,
        'immediateVirtual' => false,
        'immediateHooks' => 
        array (
        ),
      ),
      'traitInitializers' => 
      array (
        'declaringClassName' => 'Illuminate\\Database\\Eloquent\\Model',
        'implementingClassName' => 'Illuminate\\Database\\Eloquent\\Model',
        'name' => 'traitInitializers',
        'modifiers' => 18,
        'type' => NULL,
        'default' => 
        array (
          'code' => '[]',
          'attributes' => 
          array (
            'startLine' => 184,
            'endLine' => 184,
            'startTokenPos' => 488,
            'startFilePos' => 4846,
            'endTokenPos' => 489,
            'endFilePos' => 4847,
          ),
        ),
        'docComment' => '/**
 * The array of trait initializers that will be called on each new instance.
 *
 * @var array
 */',
        'attributes' => 
        array (
        ),
        'startLine' => 184,
        'endLine' => 184,
        'startColumn' => 5,
        'endColumn' => 45,
        'isPromoted' => false,
        'declaredAtCompileTime' => true,
        'immediateVirtual' => false,
        'immediateHooks' => 
        array (
        ),
      ),
      'globalScopes' => 
      array (
        'declaringClassName' => 'Illuminate\\Database\\Eloquent\\Model',
        'implementingClassName' => 'Illuminate\\Database\\Eloquent\\Model',
        'name' => 'globalScopes',
        'modifiers' => 18,
        'type' => NULL,
        'default' => 
        array (
          'code' => '[]',
          'attributes' => 
          array (
            'startLine' => 191,
            'endLine' => 191,
            'startTokenPos' => 502,
            'startFilePos' => 4977,
            'endTokenPos' => 503,
            'endFilePos' => 4978,
          ),
        ),
        'docComment' => '/**
 * The array of global scopes on the model.
 *
 * @var array
 */',
        'attributes' => 
        array (
        ),
        'startLine' => 191,
        'endLine' => 191,
        'startColumn' => 5,
        'endColumn' => 40,
        'isPromoted' => false,
        'declaredAtCompileTime' => true,
        'immediateVirtual' => false,
        'immediateHooks' => 
        array (
        ),
      ),
      'ignoreOnTouch' => 
      array (
        'declaringClassName' => 'Illuminate\\Database\\Eloquent\\Model',
        'implementingClassName' => 'Illuminate\\Database\\Eloquent\\Model',
        'name' => 'ignoreOnTouch',
        'modifiers' => 18,
        'type' => NULL,
        'default' => 
        array (
          'code' => '[]',
          'attributes' => 
          array (
            'startLine' => 198,
            'endLine' => 198,
            'startTokenPos' => 516,
            'startFilePos' => 5135,
            'endTokenPos' => 517,
            'endFilePos' => 5136,
          ),
        ),
        'docComment' => '/**
 * The list of models classes that should not be affected with touch.
 *
 * @var array
 */',
        'attributes' => 
        array (
        ),
        'startLine' => 198,
        'endLine' => 198,
        'startColumn' => 5,
        'endColumn' => 41,
        'isPromoted' => false,
        'declaredAtCompileTime' => true,
        'immediateVirtual' => false,
        'immediateHooks' => 
        array (
        ),
      ),
      'modelsShouldPreventLazyLoading' => 
      array (
        'declaringClassName' => 'Illuminate\\Database\\Eloquent\\Model',
        'implementingClassName' => 'Illuminate\\Database\\Eloquent\\Model',
        'name' => 'modelsShouldPreventLazyLoading',
        'modifiers' => 18,
        'type' => NULL,
        'default' => 
        array (
          'code' => 'false',
          'attributes' => 
          array (
            'startLine' => 205,
            'endLine' => 205,
            'startTokenPos' => 530,
            'startFilePos' => 5309,
            'endTokenPos' => 530,
            'endFilePos' => 5313,
          ),
        ),
        'docComment' => '/**
 * Indicates whether lazy loading should be restricted on all models.
 *
 * @var bool
 */',
        'attributes' => 
        array (
        ),
        'startLine' => 205,
        'endLine' => 205,
        'startColumn' => 5,
        'endColumn' => 61,
        'isPromoted' => false,
        'declaredAtCompileTime' => true,
        'immediateVirtual' => false,
        'immediateHooks' => 
        array (
        ),
      ),
      'modelsShouldAutomaticallyEagerLoadRelationships' => 
      array (
        'declaringClassName' => 'Illuminate\\Database\\Eloquent\\Model',
        'implementingClassName' => 'Illuminate\\Database\\Eloquent\\Model',
        'name' => 'modelsShouldAutomaticallyEagerLoadRelationships',
        'modifiers' => 18,
        'type' => NULL,
        'default' => 
        array (
          'code' => 'false',
          'attributes' => 
          array (
            'startLine' => 212,
            'endLine' => 212,
            'startTokenPos' => 543,
            'startFilePos' => 5533,
            'endTokenPos' => 543,
            'endFilePos' => 5537,
          ),
        ),
        'docComment' => '/**
 * Indicates whether relations should be automatically loaded on all models when they are accessed.
 *
 * @var bool
 */',
        'attributes' => 
        array (
        ),
        'startLine' => 212,
        'endLine' => 212,
        'startColumn' => 5,
        'endColumn' => 78,
        'isPromoted' => false,
        'declaredAtCompileTime' => true,
        'immediateVirtual' => false,
        'immediateHooks' => 
        array (
        ),
      ),
      'lazyLoadingViolationCallback' => 
      array (
        'declaringClassName' => 'Illuminate\\Database\\Eloquent\\Model',
        'implementingClassName' => 'Illuminate\\Database\\Eloquent\\Model',
        'name' => 'lazyLoadingViolationCallback',
        'modifiers' => 18,
        'type' => NULL,
        'default' => NULL,
        'docComment' => '/**
 * The callback that is responsible for handling lazy loading violations.
 *
 * @var (callable(self, string): mixed)|null
 */',
        'attributes' => 
        array (
        ),
        'startLine' => 219,
        'endLine' => 219,
        'startColumn' => 5,
        'endColumn' => 51,
        'isPromoted' => false,
        'declaredAtCompileTime' => true,
        'immediateVirtual' => false,
        'immediateHooks' => 
        array (
        ),
      ),
      'modelsShouldPreventSilentlyDiscardingAttributes' => 
      array (
        'declaringClassName' => 'Illuminate\\Database\\Eloquent\\Model',
        'implementingClassName' => 'Illuminate\\Database\\Eloquent\\Model',
        'name' => 'modelsShouldPreventSilentlyDiscardingAttributes',
        'modifiers' => 18,
        'type' => NULL,
        'default' => 
        array (
          'code' => 'false',
          'attributes' => 
          array (
            'startLine' => 226,
            'endLine' => 226,
            'startTokenPos' => 565,
            'startFilePos' => 5962,
            'endTokenPos' => 565,
            'endFilePos' => 5966,
          ),
        ),
        'docComment' => '/**
 * Indicates if an exception should be thrown instead of silently discarding non-fillable attributes.
 *
 * @var bool
 */',
        'attributes' => 
        array (
        ),
        'startLine' => 226,
        'endLine' => 226,
        'startColumn' => 5,
        'endColumn' => 78,
        'isPromoted' => false,
        'declaredAtCompileTime' => true,
        'immediateVirtual' => false,
        'immediateHooks' => 
        array (
        ),
      ),
      'discardedAttributeViolationCallback' => 
      array (
        'declaringClassName' => 'Illuminate\\Database\\Eloquent\\Model',
        'implementingClassName' => 'Illuminate\\Database\\Eloquent\\Model',
        'name' => 'discardedAttributeViolationCallback',
        'modifiers' => 18,
        'type' => NULL,
        'default' => NULL,
        'docComment' => '/**
 * The callback that is responsible for handling discarded attribute violations.
 *
 * @var (callable(self, array): mixed)|null
 */',
        'attributes' => 
        array (
        ),
        'startLine' => 233,
        'endLine' => 233,
        'startColumn' => 5,
        'endColumn' => 58,
        'isPromoted' => false,
        'declaredAtCompileTime' => true,
        'immediateVirtual' => false,
        'immediateHooks' => 
        array (
        ),
      ),
      'modelsShouldPreventAccessingMissingAttributes' => 
      array (
        'declaringClassName' => 'Illuminate\\Database\\Eloquent\\Model',
        'implementingClassName' => 'Illuminate\\Database\\Eloquent\\Model',
        'name' => 'modelsShouldPreventAccessingMissingAttributes',
        'modifiers' => 18,
        'type' => NULL,
        'default' => 
        array (
          'code' => 'false',
          'attributes' => 
          array (
            'startLine' => 240,
            'endLine' => 240,
            'startTokenPos' => 587,
            'startFilePos' => 6410,
            'endTokenPos' => 587,
            'endFilePos' => 6414,
          ),
        ),
        'docComment' => '/**
 * Indicates if an exception should be thrown when trying to access a missing attribute on a retrieved model.
 *
 * @var bool
 */',
        'attributes' => 
        array (
        ),
        'startLine' => 240,
        'endLine' => 240,
        'startColumn' => 5,
        'endColumn' => 76,
        'isPromoted' => false,
        'declaredAtCompileTime' => true,
        'immediateVirtual' => false,
        'immediateHooks' => 
        array (
        ),
      ),
      'missingAttributeViolationCallback' => 
      array (
        'declaringClassName' => 'Illuminate\\Database\\Eloquent\\Model',
        'implementingClassName' => 'Illuminate\\Database\\Eloquent\\Model',
        'name' => 'missingAttributeViolationCallback',
        'modifiers' => 18,
        'type' => NULL,
        'default' => NULL,
        'docComment' => '/**
 * The callback that is responsible for handling missing attribute violations.
 *
 * @var (callable(self, string): mixed)|null
 */',
        'attributes' => 
        array (
        ),
        'startLine' => 247,
        'endLine' => 247,
        'startColumn' => 5,
        'endColumn' => 56,
        'isPromoted' => false,
        'declaredAtCompileTime' => true,
        'immediateVirtual' => false,
        'immediateHooks' => 
        array (
        ),
      ),
      'isBroadcasting' => 
      array (
        'declaringClassName' => 'Illuminate\\Database\\Eloquent\\Model',
        'implementingClassName' => 'Illuminate\\Database\\Eloquent\\Model',
        'name' => 'isBroadcasting',
        'modifiers' => 18,
        'type' => NULL,
        'default' => 
        array (
          'code' => 'true',
          'attributes' => 
          array (
            'startLine' => 254,
            'endLine' => 254,
            'startTokenPos' => 609,
            'startFilePos' => 6765,
            'endTokenPos' => 609,
            'endFilePos' => 6768,
          ),
        ),
        'docComment' => '/**
 * Indicates if broadcasting is currently enabled.
 *
 * @var bool
 */',
        'attributes' => 
        array (
        ),
        'startLine' => 254,
        'endLine' => 254,
        'startColumn' => 5,
        'endColumn' => 44,
        'isPromoted' => false,
        'declaredAtCompileTime' => true,
        'immediateVirtual' => false,
        'immediateHooks' => 
        array (
        ),
      ),
      'builder' => 
      array (
        'declaringClassName' => 'Illuminate\\Database\\Eloquent\\Model',
        'implementingClassName' => 'Illuminate\\Database\\Eloquent\\Model',
        'name' => 'builder',
        'modifiers' => 18,
        'type' => 
        array (
          'class' => 'PHPStan\\BetterReflection\\Reflection\\ReflectionNamedType',
          'data' => 
          array (
            'name' => 'string',
            'isIdentifier' => true,
          ),
        ),
        'default' => 
        array (
          'code' => '\\Illuminate\\Database\\Eloquent\\Builder::class',
          'attributes' => 
          array (
            'startLine' => 261,
            'endLine' => 261,
            'startTokenPos' => 624,
            'startFilePos' => 6963,
            'endTokenPos' => 626,
            'endFilePos' => 6976,
          ),
        ),
        'docComment' => '/**
 * The Eloquent query builder class to use for the model.
 *
 * @var class-string<\\Illuminate\\Database\\Eloquent\\Builder<*>>
 */',
        'attributes' => 
        array (
        ),
        'startLine' => 261,
        'endLine' => 261,
        'startColumn' => 5,
        'endColumn' => 54,
        'isPromoted' => false,
        'declaredAtCompileTime' => true,
        'immediateVirtual' => false,
        'immediateHooks' => 
        array (
        ),
      ),
      'collectionClass' => 
      array (
        'declaringClassName' => 'Illuminate\\Database\\Eloquent\\Model',
        'implementingClassName' => 'Illuminate\\Database\\Eloquent\\Model',
        'name' => 'collectionClass',
        'modifiers' => 18,
        'type' => 
        array (
          'class' => 'PHPStan\\BetterReflection\\Reflection\\ReflectionNamedType',
          'data' => 
          array (
            'name' => 'string',
            'isIdentifier' => true,
          ),
        ),
        'default' => 
        array (
          'code' => '\\Illuminate\\Database\\Eloquent\\Collection::class',
          'attributes' => 
          array (
            'startLine' => 268,
            'endLine' => 268,
            'startTokenPos' => 641,
            'startFilePos' => 7182,
            'endTokenPos' => 643,
            'endFilePos' => 7198,
          ),
        ),
        'docComment' => '/**
 * The Eloquent collection class to use for the model.
 *
 * @var class-string<\\Illuminate\\Database\\Eloquent\\Collection<*, *>>
 */',
        'attributes' => 
        array (
        ),
        'startLine' => 268,
        'endLine' => 268,
        'startColumn' => 5,
        'endColumn' => 65,
        'isPromoted' => false,
        'declaredAtCompileTime' => true,
        'immediateVirtual' => false,
        'immediateHooks' => 
        array (
        ),
      ),
      'isSoftDeletable' => 
      array (
        'declaringClassName' => 'Illuminate\\Database\\Eloquent\\Model',
        'implementingClassName' => 'Illuminate\\Database\\Eloquent\\Model',
        'name' => 'isSoftDeletable',
        'modifiers' => 18,
        'type' => 
        array (
          'class' => 'PHPStan\\BetterReflection\\Reflection\\ReflectionNamedType',
          'data' => 
          array (
            'name' => 'array',
            'isIdentifier' => true,
          ),
        ),
        'default' => NULL,
        'docComment' => '/**
 * Cache of soft deletable models.
 *
 * @var array<class-string<self>, bool>
 */',
        'attributes' => 
        array (
        ),
        'startLine' => 275,
        'endLine' => 275,
        'startColumn' => 5,
        'endColumn' => 44,
        'isPromoted' => false,
        'declaredAtCompileTime' => true,
        'immediateVirtual' => false,
        'immediateHooks' => 
        array (
        ),
      ),
      'isPrunable' => 
      array (
        'declaringClassName' => 'Illuminate\\Database\\Eloquent\\Model',
        'implementingClassName' => 'Illuminate\\Database\\Eloquent\\Model',
        'name' => 'isPrunable',
        'modifiers' => 18,
        'type' => 
        array (
          'class' => 'PHPStan\\BetterReflection\\Reflection\\ReflectionNamedType',
          'data' => 
          array (
            'name' => 'array',
            'isIdentifier' => true,
          ),
        ),
        'default' => NULL,
        'docComment' => '/**
 * Cache of prunable models.
 *
 * @var array<class-string<self>, bool>
 */',
        'attributes' => 
        array (
        ),
        'startLine' => 282,
        'endLine' => 282,
        'startColumn' => 5,
        'endColumn' => 39,
        'isPromoted' => false,
        'declaredAtCompileTime' => true,
        'immediateVirtual' => false,
        'immediateHooks' => 
        array (
        ),
      ),
      'isMassPrunable' => 
      array (
        'declaringClassName' => 'Illuminate\\Database\\Eloquent\\Model',
        'implementingClassName' => 'Illuminate\\Database\\Eloquent\\Model',
        'name' => 'isMassPrunable',
        'modifiers' => 18,
        'type' => 
        array (
          'class' => 'PHPStan\\BetterReflection\\Reflection\\ReflectionNamedType',
          'data' => 
          array (
            'name' => 'array',
            'isIdentifier' => true,
          ),
        ),
        'default' => NULL,
        'docComment' => '/**
 * Cache of mass prunable models.
 *
 * @var array<class-string<self>, bool>
 */',
        'attributes' => 
        array (
        ),
        'startLine' => 289,
        'endLine' => 289,
        'startColumn' => 5,
        'endColumn' => 43,
        'isPromoted' => false,
        'declaredAtCompileTime' => true,
        'immediateVirtual' => false,
        'immediateHooks' => 
        array (
        ),
      ),
      'classAttributes' => 
      array (
        'declaringClassName' => 'Illuminate\\Database\\Eloquent\\Model',
        'implementingClassName' => 'Illuminate\\Database\\Eloquent\\Model',
        'name' => 'classAttributes',
        'modifiers' => 18,
        'type' => 
        array (
          'class' => 'PHPStan\\BetterReflection\\Reflection\\ReflectionNamedType',
          'data' => 
          array (
            'name' => 'array',
            'isIdentifier' => true,
          ),
        ),
        'default' => 
        array (
          'code' => '[]',
          'attributes' => 
          array (
            'startLine' => 296,
            'endLine' => 296,
            'startTokenPos' => 691,
            'startFilePos' => 7823,
            'endTokenPos' => 692,
            'endFilePos' => 7824,
          ),
        ),
        'docComment' => '/**
 * Cache of resolved class attributes.
 *
 * @var array<class-string<self>, array<class-string, mixed>>
 */',
        'attributes' => 
        array (
        ),
        'startLine' => 296,
        'endLine' => 296,
        'startColumn' => 5,
        'endColumn' => 49,
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
          'attributes' => 
          array (
            'name' => 'attributes',
            'default' => 
            array (
              'code' => '[]',
              'attributes' => 
              array (
                'startLine' => 317,
                'endLine' => 317,
                'startTokenPos' => 731,
                'startFilePos' => 8255,
                'endTokenPos' => 732,
                'endFilePos' => 8256,
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
            'startLine' => 317,
            'endLine' => 317,
            'startColumn' => 33,
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
 * Create a new Eloquent model instance.
 *
 * @param  array<string, mixed>  $attributes
 */',
        'startLine' => 317,
        'endLine' => 324,
        'startColumn' => 5,
        'endColumn' => 5,
        'couldThrow' => false,
        'isClosure' => false,
        'isGenerator' => false,
        'isVariadic' => false,
        'modifiers' => 1,
        'namespace' => 'Illuminate\\Database\\Eloquent',
        'declaringClassName' => 'Illuminate\\Database\\Eloquent\\Model',
        'implementingClassName' => 'Illuminate\\Database\\Eloquent\\Model',
        'currentClassName' => 'Illuminate\\Database\\Eloquent\\Model',
        'aliasName' => NULL,
      ),
      'bootIfNotBooted' => 
      array (
        'name' => 'bootIfNotBooted',
        'parameters' => 
        array (
        ),
        'returnsReference' => false,
        'returnType' => NULL,
        'attributes' => 
        array (
        ),
        'docComment' => '/**
 * Check if the model needs to be booted and if so, do it.
 *
 * @return void
 *
 * @throws \\LogicException
 */',
        'startLine' => 333,
        'endLine' => 360,
        'startColumn' => 5,
        'endColumn' => 5,
        'couldThrow' => false,
        'isClosure' => false,
        'isGenerator' => false,
        'isVariadic' => false,
        'modifiers' => 2,
        'namespace' => 'Illuminate\\Database\\Eloquent',
        'declaringClassName' => 'Illuminate\\Database\\Eloquent\\Model',
        'implementingClassName' => 'Illuminate\\Database\\Eloquent\\Model',
        'currentClassName' => 'Illuminate\\Database\\Eloquent\\Model',
        'aliasName' => NULL,
      ),
      'booting' => 
      array (
        'name' => 'booting',
        'parameters' => 
        array (
        ),
        'returnsReference' => false,
        'returnType' => NULL,
        'attributes' => 
        array (
        ),
        'docComment' => '/**
 * Perform any actions required before the model boots.
 *
 * @return void
 */',
        'startLine' => 367,
        'endLine' => 370,
        'startColumn' => 5,
        'endColumn' => 5,
        'couldThrow' => false,
        'isClosure' => false,
        'isGenerator' => false,
        'isVariadic' => false,
        'modifiers' => 18,
        'namespace' => 'Illuminate\\Database\\Eloquent',
        'declaringClassName' => 'Illuminate\\Database\\Eloquent\\Model',
        'implementingClassName' => 'Illuminate\\Database\\Eloquent\\Model',
        'currentClassName' => 'Illuminate\\Database\\Eloquent\\Model',
        'aliasName' => NULL,
      ),
      'boot' => 
      array (
        'name' => 'boot',
        'parameters' => 
        array (
        ),
        'returnsReference' => false,
        'returnType' => NULL,
        'attributes' => 
        array (
        ),
        'docComment' => '/**
 * Bootstrap the model and its traits.
 *
 * @return void
 */',
        'startLine' => 377,
        'endLine' => 380,
        'startColumn' => 5,
        'endColumn' => 5,
        'couldThrow' => false,
        'isClosure' => false,
        'isGenerator' => false,
        'isVariadic' => false,
        'modifiers' => 18,
        'namespace' => 'Illuminate\\Database\\Eloquent',
        'declaringClassName' => 'Illuminate\\Database\\Eloquent\\Model',
        'implementingClassName' => 'Illuminate\\Database\\Eloquent\\Model',
        'currentClassName' => 'Illuminate\\Database\\Eloquent\\Model',
        'aliasName' => NULL,
      ),
      'bootTraits' => 
      array (
        'name' => 'bootTraits',
        'parameters' => 
        array (
        ),
        'returnsReference' => false,
        'returnType' => NULL,
        'attributes' => 
        array (
        ),
        'docComment' => '/**
 * Boot all of the bootable traits on the model.
 *
 * @return void
 */',
        'startLine' => 387,
        'endLine' => 417,
        'startColumn' => 5,
        'endColumn' => 5,
        'couldThrow' => false,
        'isClosure' => false,
        'isGenerator' => false,
        'isVariadic' => false,
        'modifiers' => 18,
        'namespace' => 'Illuminate\\Database\\Eloquent',
        'declaringClassName' => 'Illuminate\\Database\\Eloquent\\Model',
        'implementingClassName' => 'Illuminate\\Database\\Eloquent\\Model',
        'currentClassName' => 'Illuminate\\Database\\Eloquent\\Model',
        'aliasName' => NULL,
      ),
      'initializeTraits' => 
      array (
        'name' => 'initializeTraits',
        'parameters' => 
        array (
        ),
        'returnsReference' => false,
        'returnType' => NULL,
        'attributes' => 
        array (
        ),
        'docComment' => '/**
 * Initialize any initializable traits on the model.
 *
 * @return void
 */',
        'startLine' => 424,
        'endLine' => 429,
        'startColumn' => 5,
        'endColumn' => 5,
        'couldThrow' => false,
        'isClosure' => false,
        'isGenerator' => false,
        'isVariadic' => false,
        'modifiers' => 2,
        'namespace' => 'Illuminate\\Database\\Eloquent',
        'declaringClassName' => 'Illuminate\\Database\\Eloquent\\Model',
        'implementingClassName' => 'Illuminate\\Database\\Eloquent\\Model',
        'currentClassName' => 'Illuminate\\Database\\Eloquent\\Model',
        'aliasName' => NULL,
      ),
      'initializeModelAttributes' => 
      array (
        'name' => 'initializeModelAttributes',
        'parameters' => 
        array (
        ),
        'returnsReference' => false,
        'returnType' => NULL,
        'attributes' => 
        array (
        ),
        'docComment' => '/**
 * Initialize the model attributes from class attributes.
 *
 * @return void
 */',
        'startLine' => 436,
        'endLine' => 466,
        'startColumn' => 5,
        'endColumn' => 5,
        'couldThrow' => false,
        'isClosure' => false,
        'isGenerator' => false,
        'isVariadic' => false,
        'modifiers' => 1,
        'namespace' => 'Illuminate\\Database\\Eloquent',
        'declaringClassName' => 'Illuminate\\Database\\Eloquent\\Model',
        'implementingClassName' => 'Illuminate\\Database\\Eloquent\\Model',
        'currentClassName' => 'Illuminate\\Database\\Eloquent\\Model',
        'aliasName' => NULL,
      ),
      'booted' => 
      array (
        'name' => 'booted',
        'parameters' => 
        array (
        ),
        'returnsReference' => false,
        'returnType' => NULL,
        'attributes' => 
        array (
        ),
        'docComment' => '/**
 * Perform any actions required after the model boots.
 *
 * @return void
 */',
        'startLine' => 473,
        'endLine' => 476,
        'startColumn' => 5,
        'endColumn' => 5,
        'couldThrow' => false,
        'isClosure' => false,
        'isGenerator' => false,
        'isVariadic' => false,
        'modifiers' => 18,
        'namespace' => 'Illuminate\\Database\\Eloquent',
        'declaringClassName' => 'Illuminate\\Database\\Eloquent\\Model',
        'implementingClassName' => 'Illuminate\\Database\\Eloquent\\Model',
        'currentClassName' => 'Illuminate\\Database\\Eloquent\\Model',
        'aliasName' => NULL,
      ),
      'whenBooted' => 
      array (
        'name' => 'whenBooted',
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
            'startLine' => 484,
            'endLine' => 484,
            'startColumn' => 42,
            'endColumn' => 58,
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
 * Register a closure to be executed after the model has booted.
 *
 * @param  \\Closure  $callback
 * @return void
 */',
        'startLine' => 484,
        'endLine' => 489,
        'startColumn' => 5,
        'endColumn' => 5,
        'couldThrow' => false,
        'isClosure' => false,
        'isGenerator' => false,
        'isVariadic' => false,
        'modifiers' => 18,
        'namespace' => 'Illuminate\\Database\\Eloquent',
        'declaringClassName' => 'Illuminate\\Database\\Eloquent\\Model',
        'implementingClassName' => 'Illuminate\\Database\\Eloquent\\Model',
        'currentClassName' => 'Illuminate\\Database\\Eloquent\\Model',
        'aliasName' => NULL,
      ),
      'clearBootedModels' => 
      array (
        'name' => 'clearBootedModels',
        'parameters' => 
        array (
        ),
        'returnsReference' => false,
        'returnType' => NULL,
        'attributes' => 
        array (
        ),
        'docComment' => '/**
 * Clear the list of booted models so they will be re-booted.
 *
 * @return void
 */',
        'startLine' => 496,
        'endLine' => 502,
        'startColumn' => 5,
        'endColumn' => 5,
        'couldThrow' => false,
        'isClosure' => false,
        'isGenerator' => false,
        'isVariadic' => false,
        'modifiers' => 17,
        'namespace' => 'Illuminate\\Database\\Eloquent',
        'declaringClassName' => 'Illuminate\\Database\\Eloquent\\Model',
        'implementingClassName' => 'Illuminate\\Database\\Eloquent\\Model',
        'currentClassName' => 'Illuminate\\Database\\Eloquent\\Model',
        'aliasName' => NULL,
      ),
      'withoutTouching' => 
      array (
        'name' => 'withoutTouching',
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
            'startLine' => 510,
            'endLine' => 510,
            'startColumn' => 44,
            'endColumn' => 61,
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
 * Disables relationship model touching for the current class during given callback scope.
 *
 * @param  callable  $callback
 * @return void
 */',
        'startLine' => 510,
        'endLine' => 513,
        'startColumn' => 5,
        'endColumn' => 5,
        'couldThrow' => false,
        'isClosure' => false,
        'isGenerator' => false,
        'isVariadic' => false,
        'modifiers' => 17,
        'namespace' => 'Illuminate\\Database\\Eloquent',
        'declaringClassName' => 'Illuminate\\Database\\Eloquent\\Model',
        'implementingClassName' => 'Illuminate\\Database\\Eloquent\\Model',
        'currentClassName' => 'Illuminate\\Database\\Eloquent\\Model',
        'aliasName' => NULL,
      ),
      'withoutTouchingOn' => 
      array (
        'name' => 'withoutTouchingOn',
        'parameters' => 
        array (
          'models' => 
          array (
            'name' => 'models',
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
            'startLine' => 522,
            'endLine' => 522,
            'startColumn' => 46,
            'endColumn' => 58,
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
            'startLine' => 522,
            'endLine' => 522,
            'startColumn' => 61,
            'endColumn' => 78,
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
 * Disables relationship model touching for the given model classes during given callback scope.
 *
 * @param  array  $models
 * @param  callable  $callback
 * @return void
 */',
        'startLine' => 522,
        'endLine' => 531,
        'startColumn' => 5,
        'endColumn' => 5,
        'couldThrow' => false,
        'isClosure' => false,
        'isGenerator' => false,
        'isVariadic' => false,
        'modifiers' => 17,
        'namespace' => 'Illuminate\\Database\\Eloquent',
        'declaringClassName' => 'Illuminate\\Database\\Eloquent\\Model',
        'implementingClassName' => 'Illuminate\\Database\\Eloquent\\Model',
        'currentClassName' => 'Illuminate\\Database\\Eloquent\\Model',
        'aliasName' => NULL,
      ),
      'isIgnoringTouch' => 
      array (
        'name' => 'isIgnoringTouch',
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
                'startLine' => 539,
                'endLine' => 539,
                'startTokenPos' => 1911,
                'startFilePos' => 14489,
                'endTokenPos' => 1911,
                'endFilePos' => 14492,
              ),
            ),
            'type' => NULL,
            'isVariadic' => false,
            'byRef' => false,
            'isPromoted' => false,
            'attributes' => 
            array (
            ),
            'startLine' => 539,
            'endLine' => 539,
            'startColumn' => 44,
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
 * Determine if the given model is ignoring touches.
 *
 * @param  string|null  $class
 * @return bool
 */',
        'startLine' => 539,
        'endLine' => 556,
        'startColumn' => 5,
        'endColumn' => 5,
        'couldThrow' => false,
        'isClosure' => false,
        'isGenerator' => false,
        'isVariadic' => false,
        'modifiers' => 17,
        'namespace' => 'Illuminate\\Database\\Eloquent',
        'declaringClassName' => 'Illuminate\\Database\\Eloquent\\Model',
        'implementingClassName' => 'Illuminate\\Database\\Eloquent\\Model',
        'currentClassName' => 'Illuminate\\Database\\Eloquent\\Model',
        'aliasName' => NULL,
      ),
      'shouldBeStrict' => 
      array (
        'name' => 'shouldBeStrict',
        'parameters' => 
        array (
          'shouldBeStrict' => 
          array (
            'name' => 'shouldBeStrict',
            'default' => 
            array (
              'code' => 'true',
              'attributes' => 
              array (
                'startLine' => 564,
                'endLine' => 564,
                'startTokenPos' => 2080,
                'startFilePos' => 15348,
                'endTokenPos' => 2080,
                'endFilePos' => 15351,
              ),
            ),
            'type' => 
            array (
              'class' => 'PHPStan\\BetterReflection\\Reflection\\ReflectionNamedType',
              'data' => 
              array (
                'name' => 'bool',
                'isIdentifier' => true,
              ),
            ),
            'isVariadic' => false,
            'byRef' => false,
            'isPromoted' => false,
            'attributes' => 
            array (
            ),
            'startLine' => 564,
            'endLine' => 564,
            'startColumn' => 43,
            'endColumn' => 69,
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
 * Indicate that models should prevent lazy loading, silently discarding attributes, and accessing missing attributes.
 *
 * @param  bool  $shouldBeStrict
 * @return void
 */',
        'startLine' => 564,
        'endLine' => 569,
        'startColumn' => 5,
        'endColumn' => 5,
        'couldThrow' => false,
        'isClosure' => false,
        'isGenerator' => false,
        'isVariadic' => false,
        'modifiers' => 17,
        'namespace' => 'Illuminate\\Database\\Eloquent',
        'declaringClassName' => 'Illuminate\\Database\\Eloquent\\Model',
        'implementingClassName' => 'Illuminate\\Database\\Eloquent\\Model',
        'currentClassName' => 'Illuminate\\Database\\Eloquent\\Model',
        'aliasName' => NULL,
      ),
      'preventLazyLoading' => 
      array (
        'name' => 'preventLazyLoading',
        'parameters' => 
        array (
          'value' => 
          array (
            'name' => 'value',
            'default' => 
            array (
              'code' => 'true',
              'attributes' => 
              array (
                'startLine' => 577,
                'endLine' => 577,
                'startTokenPos' => 2125,
                'startFilePos' => 15743,
                'endTokenPos' => 2125,
                'endFilePos' => 15746,
              ),
            ),
            'type' => NULL,
            'isVariadic' => false,
            'byRef' => false,
            'isPromoted' => false,
            'attributes' => 
            array (
            ),
            'startLine' => 577,
            'endLine' => 577,
            'startColumn' => 47,
            'endColumn' => 59,
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
 * Prevent model relationships from being lazy loaded.
 *
 * @param  bool  $value
 * @return void
 */',
        'startLine' => 577,
        'endLine' => 580,
        'startColumn' => 5,
        'endColumn' => 5,
        'couldThrow' => false,
        'isClosure' => false,
        'isGenerator' => false,
        'isVariadic' => false,
        'modifiers' => 17,
        'namespace' => 'Illuminate\\Database\\Eloquent',
        'declaringClassName' => 'Illuminate\\Database\\Eloquent\\Model',
        'implementingClassName' => 'Illuminate\\Database\\Eloquent\\Model',
        'currentClassName' => 'Illuminate\\Database\\Eloquent\\Model',
        'aliasName' => NULL,
      ),
      'automaticallyEagerLoadRelationships' => 
      array (
        'name' => 'automaticallyEagerLoadRelationships',
        'parameters' => 
        array (
          'value' => 
          array (
            'name' => 'value',
            'default' => 
            array (
              'code' => 'true',
              'attributes' => 
              array (
                'startLine' => 588,
                'endLine' => 588,
                'startTokenPos' => 2155,
                'startFilePos' => 16055,
                'endTokenPos' => 2155,
                'endFilePos' => 16058,
              ),
            ),
            'type' => NULL,
            'isVariadic' => false,
            'byRef' => false,
            'isPromoted' => false,
            'attributes' => 
            array (
            ),
            'startLine' => 588,
            'endLine' => 588,
            'startColumn' => 64,
            'endColumn' => 76,
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
 * Determine if model relationships should be automatically eager loaded when accessed.
 *
 * @param  bool  $value
 * @return void
 */',
        'startLine' => 588,
        'endLine' => 591,
        'startColumn' => 5,
        'endColumn' => 5,
        'couldThrow' => false,
        'isClosure' => false,
        'isGenerator' => false,
        'isVariadic' => false,
        'modifiers' => 17,
        'namespace' => 'Illuminate\\Database\\Eloquent',
        'declaringClassName' => 'Illuminate\\Database\\Eloquent\\Model',
        'implementingClassName' => 'Illuminate\\Database\\Eloquent\\Model',
        'currentClassName' => 'Illuminate\\Database\\Eloquent\\Model',
        'aliasName' => NULL,
      ),
      'handleLazyLoadingViolationUsing' => 
      array (
        'name' => 'handleLazyLoadingViolationUsing',
        'parameters' => 
        array (
          'callback' => 
          array (
            'name' => 'callback',
            'default' => NULL,
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
                      'name' => 'callable',
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
            'isVariadic' => false,
            'byRef' => false,
            'isPromoted' => false,
            'attributes' => 
            array (
            ),
            'startLine' => 599,
            'endLine' => 599,
            'startColumn' => 60,
            'endColumn' => 78,
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
 * Register a callback that is responsible for handling lazy loading violations.
 *
 * @param  (callable(self, string): mixed)|null  $callback
 * @return void
 */',
        'startLine' => 599,
        'endLine' => 602,
        'startColumn' => 5,
        'endColumn' => 5,
        'couldThrow' => false,
        'isClosure' => false,
        'isGenerator' => false,
        'isVariadic' => false,
        'modifiers' => 17,
        'namespace' => 'Illuminate\\Database\\Eloquent',
        'declaringClassName' => 'Illuminate\\Database\\Eloquent\\Model',
        'implementingClassName' => 'Illuminate\\Database\\Eloquent\\Model',
        'currentClassName' => 'Illuminate\\Database\\Eloquent\\Model',
        'aliasName' => NULL,
      ),
      'preventSilentlyDiscardingAttributes' => 
      array (
        'name' => 'preventSilentlyDiscardingAttributes',
        'parameters' => 
        array (
          'value' => 
          array (
            'name' => 'value',
            'default' => 
            array (
              'code' => 'true',
              'attributes' => 
              array (
                'startLine' => 610,
                'endLine' => 610,
                'startTokenPos' => 2214,
                'startFilePos' => 16705,
                'endTokenPos' => 2214,
                'endFilePos' => 16708,
              ),
            ),
            'type' => NULL,
            'isVariadic' => false,
            'byRef' => false,
            'isPromoted' => false,
            'attributes' => 
            array (
            ),
            'startLine' => 610,
            'endLine' => 610,
            'startColumn' => 64,
            'endColumn' => 76,
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
 * Prevent non-fillable attributes from being silently discarded.
 *
 * @param  bool  $value
 * @return void
 */',
        'startLine' => 610,
        'endLine' => 613,
        'startColumn' => 5,
        'endColumn' => 5,
        'couldThrow' => false,
        'isClosure' => false,
        'isGenerator' => false,
        'isVariadic' => false,
        'modifiers' => 17,
        'namespace' => 'Illuminate\\Database\\Eloquent',
        'declaringClassName' => 'Illuminate\\Database\\Eloquent\\Model',
        'implementingClassName' => 'Illuminate\\Database\\Eloquent\\Model',
        'currentClassName' => 'Illuminate\\Database\\Eloquent\\Model',
        'aliasName' => NULL,
      ),
      'handleDiscardedAttributeViolationUsing' => 
      array (
        'name' => 'handleDiscardedAttributeViolationUsing',
        'parameters' => 
        array (
          'callback' => 
          array (
            'name' => 'callback',
            'default' => NULL,
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
                      'name' => 'callable',
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
            'isVariadic' => false,
            'byRef' => false,
            'isPromoted' => false,
            'attributes' => 
            array (
            ),
            'startLine' => 621,
            'endLine' => 621,
            'startColumn' => 67,
            'endColumn' => 85,
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
 * Register a callback that is responsible for handling discarded attribute violations.
 *
 * @param  (callable(self, array): mixed)|null  $callback
 * @return void
 */',
        'startLine' => 621,
        'endLine' => 624,
        'startColumn' => 5,
        'endColumn' => 5,
        'couldThrow' => false,
        'isClosure' => false,
        'isGenerator' => false,
        'isVariadic' => false,
        'modifiers' => 17,
        'namespace' => 'Illuminate\\Database\\Eloquent',
        'declaringClassName' => 'Illuminate\\Database\\Eloquent\\Model',
        'implementingClassName' => 'Illuminate\\Database\\Eloquent\\Model',
        'currentClassName' => 'Illuminate\\Database\\Eloquent\\Model',
        'aliasName' => NULL,
      ),
      'preventAccessingMissingAttributes' => 
      array (
        'name' => 'preventAccessingMissingAttributes',
        'parameters' => 
        array (
          'value' => 
          array (
            'name' => 'value',
            'default' => 
            array (
              'code' => 'true',
              'attributes' => 
              array (
                'startLine' => 632,
                'endLine' => 632,
                'startTokenPos' => 2273,
                'startFilePos' => 17368,
                'endTokenPos' => 2273,
                'endFilePos' => 17371,
              ),
            ),
            'type' => NULL,
            'isVariadic' => false,
            'byRef' => false,
            'isPromoted' => false,
            'attributes' => 
            array (
            ),
            'startLine' => 632,
            'endLine' => 632,
            'startColumn' => 62,
            'endColumn' => 74,
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
 * Prevent accessing missing attributes on retrieved models.
 *
 * @param  bool  $value
 * @return void
 */',
        'startLine' => 632,
        'endLine' => 635,
        'startColumn' => 5,
        'endColumn' => 5,
        'couldThrow' => false,
        'isClosure' => false,
        'isGenerator' => false,
        'isVariadic' => false,
        'modifiers' => 17,
        'namespace' => 'Illuminate\\Database\\Eloquent',
        'declaringClassName' => 'Illuminate\\Database\\Eloquent\\Model',
        'implementingClassName' => 'Illuminate\\Database\\Eloquent\\Model',
        'currentClassName' => 'Illuminate\\Database\\Eloquent\\Model',
        'aliasName' => NULL,
      ),
      'handleMissingAttributeViolationUsing' => 
      array (
        'name' => 'handleMissingAttributeViolationUsing',
        'parameters' => 
        array (
          'callback' => 
          array (
            'name' => 'callback',
            'default' => NULL,
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
                      'name' => 'callable',
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
            'isVariadic' => false,
            'byRef' => false,
            'isPromoted' => false,
            'attributes' => 
            array (
            ),
            'startLine' => 643,
            'endLine' => 643,
            'startColumn' => 65,
            'endColumn' => 83,
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
 * Register a callback that is responsible for handling missing attribute violations.
 *
 * @param  (callable(self, string): mixed)|null  $callback
 * @return void
 */',
        'startLine' => 643,
        'endLine' => 646,
        'startColumn' => 5,
        'endColumn' => 5,
        'couldThrow' => false,
        'isClosure' => false,
        'isGenerator' => false,
        'isVariadic' => false,
        'modifiers' => 17,
        'namespace' => 'Illuminate\\Database\\Eloquent',
        'declaringClassName' => 'Illuminate\\Database\\Eloquent\\Model',
        'implementingClassName' => 'Illuminate\\Database\\Eloquent\\Model',
        'currentClassName' => 'Illuminate\\Database\\Eloquent\\Model',
        'aliasName' => NULL,
      ),
      'withoutBroadcasting' => 
      array (
        'name' => 'withoutBroadcasting',
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
            'startLine' => 656,
            'endLine' => 656,
            'startColumn' => 48,
            'endColumn' => 65,
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
 * Execute a callback without broadcasting any model events for all model types.
 *
 * @template TReturn
 *
 * @param  callable(): TReturn  $callback
 * @return TReturn
 */',
        'startLine' => 656,
        'endLine' => 667,
        'startColumn' => 5,
        'endColumn' => 5,
        'couldThrow' => false,
        'isClosure' => false,
        'isGenerator' => false,
        'isVariadic' => false,
        'modifiers' => 17,
        'namespace' => 'Illuminate\\Database\\Eloquent',
        'declaringClassName' => 'Illuminate\\Database\\Eloquent\\Model',
        'implementingClassName' => 'Illuminate\\Database\\Eloquent\\Model',
        'currentClassName' => 'Illuminate\\Database\\Eloquent\\Model',
        'aliasName' => NULL,
      ),
      'fill' => 
      array (
        'name' => 'fill',
        'parameters' => 
        array (
          'attributes' => 
          array (
            'name' => 'attributes',
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
            'startLine' => 677,
            'endLine' => 677,
            'startColumn' => 26,
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
 * Fill the model with an array of attributes.
 *
 * @param  array<string, mixed>  $attributes
 * @return $this
 *
 * @throws \\Illuminate\\Database\\Eloquent\\MassAssignmentException
 */',
        'startLine' => 677,
        'endLine' => 717,
        'startColumn' => 5,
        'endColumn' => 5,
        'couldThrow' => false,
        'isClosure' => false,
        'isGenerator' => false,
        'isVariadic' => false,
        'modifiers' => 1,
        'namespace' => 'Illuminate\\Database\\Eloquent',
        'declaringClassName' => 'Illuminate\\Database\\Eloquent\\Model',
        'implementingClassName' => 'Illuminate\\Database\\Eloquent\\Model',
        'currentClassName' => 'Illuminate\\Database\\Eloquent\\Model',
        'aliasName' => NULL,
      ),
      'forceFill' => 
      array (
        'name' => 'forceFill',
        'parameters' => 
        array (
          'attributes' => 
          array (
            'name' => 'attributes',
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
            'startLine' => 725,
            'endLine' => 725,
            'startColumn' => 31,
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
 * Fill the model with an array of attributes. Force mass assignment.
 *
 * @param  array<string, mixed>  $attributes
 * @return $this
 */',
        'startLine' => 725,
        'endLine' => 728,
        'startColumn' => 5,
        'endColumn' => 5,
        'couldThrow' => false,
        'isClosure' => false,
        'isGenerator' => false,
        'isVariadic' => false,
        'modifiers' => 1,
        'namespace' => 'Illuminate\\Database\\Eloquent',
        'declaringClassName' => 'Illuminate\\Database\\Eloquent\\Model',
        'implementingClassName' => 'Illuminate\\Database\\Eloquent\\Model',
        'currentClassName' => 'Illuminate\\Database\\Eloquent\\Model',
        'aliasName' => NULL,
      ),
      'qualifyColumn' => 
      array (
        'name' => 'qualifyColumn',
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
            'startLine' => 736,
            'endLine' => 736,
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
 * Qualify the given column name by the model\'s table.
 *
 * @param  string  $column
 * @return string
 */',
        'startLine' => 736,
        'endLine' => 743,
        'startColumn' => 5,
        'endColumn' => 5,
        'couldThrow' => false,
        'isClosure' => false,
        'isGenerator' => false,
        'isVariadic' => false,
        'modifiers' => 1,
        'namespace' => 'Illuminate\\Database\\Eloquent',
        'declaringClassName' => 'Illuminate\\Database\\Eloquent\\Model',
        'implementingClassName' => 'Illuminate\\Database\\Eloquent\\Model',
        'currentClassName' => 'Illuminate\\Database\\Eloquent\\Model',
        'aliasName' => NULL,
      ),
      'qualifyColumns' => 
      array (
        'name' => 'qualifyColumns',
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
            'startLine' => 751,
            'endLine' => 751,
            'startColumn' => 36,
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
 * Qualify the given columns with the model\'s table.
 *
 * @param  array  $columns
 * @return array
 */',
        'startLine' => 751,
        'endLine' => 756,
        'startColumn' => 5,
        'endColumn' => 5,
        'couldThrow' => false,
        'isClosure' => false,
        'isGenerator' => false,
        'isVariadic' => false,
        'modifiers' => 1,
        'namespace' => 'Illuminate\\Database\\Eloquent',
        'declaringClassName' => 'Illuminate\\Database\\Eloquent\\Model',
        'implementingClassName' => 'Illuminate\\Database\\Eloquent\\Model',
        'currentClassName' => 'Illuminate\\Database\\Eloquent\\Model',
        'aliasName' => NULL,
      ),
      'newInstance' => 
      array (
        'name' => 'newInstance',
        'parameters' => 
        array (
          'attributes' => 
          array (
            'name' => 'attributes',
            'default' => 
            array (
              'code' => '[]',
              'attributes' => 
              array (
                'startLine' => 765,
                'endLine' => 765,
                'startTokenPos' => 2820,
                'startFilePos' => 21508,
                'endTokenPos' => 2821,
                'endFilePos' => 21509,
              ),
            ),
            'type' => NULL,
            'isVariadic' => false,
            'byRef' => false,
            'isPromoted' => false,
            'attributes' => 
            array (
            ),
            'startLine' => 765,
            'endLine' => 765,
            'startColumn' => 33,
            'endColumn' => 48,
            'parameterIndex' => 0,
            'isOptional' => true,
          ),
          'exists' => 
          array (
            'name' => 'exists',
            'default' => 
            array (
              'code' => 'false',
              'attributes' => 
              array (
                'startLine' => 765,
                'endLine' => 765,
                'startTokenPos' => 2828,
                'startFilePos' => 21522,
                'endTokenPos' => 2828,
                'endFilePos' => 21526,
              ),
            ),
            'type' => NULL,
            'isVariadic' => false,
            'byRef' => false,
            'isPromoted' => false,
            'attributes' => 
            array (
            ),
            'startLine' => 765,
            'endLine' => 765,
            'startColumn' => 51,
            'endColumn' => 65,
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
 * Create a new instance of the given model.
 *
 * @param  array<string, mixed>  $attributes
 * @param  bool  $exists
 * @return static
 */',
        'startLine' => 765,
        'endLine' => 785,
        'startColumn' => 5,
        'endColumn' => 5,
        'couldThrow' => false,
        'isClosure' => false,
        'isGenerator' => false,
        'isVariadic' => false,
        'modifiers' => 1,
        'namespace' => 'Illuminate\\Database\\Eloquent',
        'declaringClassName' => 'Illuminate\\Database\\Eloquent\\Model',
        'implementingClassName' => 'Illuminate\\Database\\Eloquent\\Model',
        'currentClassName' => 'Illuminate\\Database\\Eloquent\\Model',
        'aliasName' => NULL,
      ),
      'newFromBuilder' => 
      array (
        'name' => 'newFromBuilder',
        'parameters' => 
        array (
          'attributes' => 
          array (
            'name' => 'attributes',
            'default' => 
            array (
              'code' => '[]',
              'attributes' => 
              array (
                'startLine' => 794,
                'endLine' => 794,
                'startTokenPos' => 2922,
                'startFilePos' => 22335,
                'endTokenPos' => 2923,
                'endFilePos' => 22336,
              ),
            ),
            'type' => NULL,
            'isVariadic' => false,
            'byRef' => false,
            'isPromoted' => false,
            'attributes' => 
            array (
            ),
            'startLine' => 794,
            'endLine' => 794,
            'startColumn' => 36,
            'endColumn' => 51,
            'parameterIndex' => 0,
            'isOptional' => true,
          ),
          'connection' => 
          array (
            'name' => 'connection',
            'default' => 
            array (
              'code' => 'null',
              'attributes' => 
              array (
                'startLine' => 794,
                'endLine' => 794,
                'startTokenPos' => 2930,
                'startFilePos' => 22353,
                'endTokenPos' => 2930,
                'endFilePos' => 22356,
              ),
            ),
            'type' => NULL,
            'isVariadic' => false,
            'byRef' => false,
            'isPromoted' => false,
            'attributes' => 
            array (
            ),
            'startLine' => 794,
            'endLine' => 794,
            'startColumn' => 54,
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
 * Create a new model instance that is existing.
 *
 * @param  array<string, mixed>  $attributes
 * @param  \\UnitEnum|string|null  $connection
 * @return static
 */',
        'startLine' => 794,
        'endLine' => 805,
        'startColumn' => 5,
        'endColumn' => 5,
        'couldThrow' => false,
        'isClosure' => false,
        'isGenerator' => false,
        'isVariadic' => false,
        'modifiers' => 1,
        'namespace' => 'Illuminate\\Database\\Eloquent',
        'declaringClassName' => 'Illuminate\\Database\\Eloquent\\Model',
        'implementingClassName' => 'Illuminate\\Database\\Eloquent\\Model',
        'currentClassName' => 'Illuminate\\Database\\Eloquent\\Model',
        'aliasName' => NULL,
      ),
      'on' => 
      array (
        'name' => 'on',
        'parameters' => 
        array (
          'connection' => 
          array (
            'name' => 'connection',
            'default' => 
            array (
              'code' => 'null',
              'attributes' => 
              array (
                'startLine' => 813,
                'endLine' => 813,
                'startTokenPos' => 3012,
                'startFilePos' => 22866,
                'endTokenPos' => 3012,
                'endFilePos' => 22869,
              ),
            ),
            'type' => NULL,
            'isVariadic' => false,
            'byRef' => false,
            'isPromoted' => false,
            'attributes' => 
            array (
            ),
            'startLine' => 813,
            'endLine' => 813,
            'startColumn' => 31,
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
 * Begin querying the model on a given connection.
 *
 * @param  \\UnitEnum|string|null  $connection
 * @return \\Illuminate\\Database\\Eloquent\\Builder<static>
 */',
        'startLine' => 813,
        'endLine' => 819,
        'startColumn' => 5,
        'endColumn' => 5,
        'couldThrow' => false,
        'isClosure' => false,
        'isGenerator' => false,
        'isVariadic' => false,
        'modifiers' => 17,
        'namespace' => 'Illuminate\\Database\\Eloquent',
        'declaringClassName' => 'Illuminate\\Database\\Eloquent\\Model',
        'implementingClassName' => 'Illuminate\\Database\\Eloquent\\Model',
        'currentClassName' => 'Illuminate\\Database\\Eloquent\\Model',
        'aliasName' => NULL,
      ),
      'onWriteConnection' => 
      array (
        'name' => 'onWriteConnection',
        'parameters' => 
        array (
        ),
        'returnsReference' => false,
        'returnType' => NULL,
        'attributes' => 
        array (
        ),
        'docComment' => '/**
 * Begin querying the model on the write connection.
 *
 * @return \\Illuminate\\Database\\Eloquent\\Builder<static>
 */',
        'startLine' => 826,
        'endLine' => 829,
        'startColumn' => 5,
        'endColumn' => 5,
        'couldThrow' => false,
        'isClosure' => false,
        'isGenerator' => false,
        'isVariadic' => false,
        'modifiers' => 17,
        'namespace' => 'Illuminate\\Database\\Eloquent',
        'declaringClassName' => 'Illuminate\\Database\\Eloquent\\Model',
        'implementingClassName' => 'Illuminate\\Database\\Eloquent\\Model',
        'currentClassName' => 'Illuminate\\Database\\Eloquent\\Model',
        'aliasName' => NULL,
      ),
      'all' => 
      array (
        'name' => 'all',
        'parameters' => 
        array (
          'columns' => 
          array (
            'name' => 'columns',
            'default' => 
            array (
              'code' => '[\'*\']',
              'attributes' => 
              array (
                'startLine' => 837,
                'endLine' => 837,
                'startTokenPos' => 3086,
                'startFilePos' => 23693,
                'endTokenPos' => 3088,
                'endFilePos' => 23697,
              ),
            ),
            'type' => NULL,
            'isVariadic' => false,
            'byRef' => false,
            'isPromoted' => false,
            'attributes' => 
            array (
            ),
            'startLine' => 837,
            'endLine' => 837,
            'startColumn' => 32,
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
 * Get all of the models from the database.
 *
 * @param  array|string  $columns
 * @return \\Illuminate\\Database\\Eloquent\\Collection<int, static>
 */',
        'startLine' => 837,
        'endLine' => 842,
        'startColumn' => 5,
        'endColumn' => 5,
        'couldThrow' => false,
        'isClosure' => false,
        'isGenerator' => false,
        'isVariadic' => true,
        'modifiers' => 17,
        'namespace' => 'Illuminate\\Database\\Eloquent',
        'declaringClassName' => 'Illuminate\\Database\\Eloquent\\Model',
        'implementingClassName' => 'Illuminate\\Database\\Eloquent\\Model',
        'currentClassName' => 'Illuminate\\Database\\Eloquent\\Model',
        'aliasName' => NULL,
      ),
      'with' => 
      array (
        'name' => 'with',
        'parameters' => 
        array (
          'relations' => 
          array (
            'name' => 'relations',
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
            'startColumn' => 33,
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
 * Begin querying a model with eager loading.
 *
 * @param  array|string  $relations
 * @return \\Illuminate\\Database\\Eloquent\\Builder<static>
 */',
        'startLine' => 850,
        'endLine' => 855,
        'startColumn' => 5,
        'endColumn' => 5,
        'couldThrow' => false,
        'isClosure' => false,
        'isGenerator' => false,
        'isVariadic' => true,
        'modifiers' => 17,
        'namespace' => 'Illuminate\\Database\\Eloquent',
        'declaringClassName' => 'Illuminate\\Database\\Eloquent\\Model',
        'implementingClassName' => 'Illuminate\\Database\\Eloquent\\Model',
        'currentClassName' => 'Illuminate\\Database\\Eloquent\\Model',
        'aliasName' => NULL,
      ),
      'load' => 
      array (
        'name' => 'load',
        'parameters' => 
        array (
          'relations' => 
          array (
            'name' => 'relations',
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
            'startColumn' => 26,
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
 * Eager load relations on the model.
 *
 * @param  array|string  $relations
 * @return $this
 */',
        'startLine' => 863,
        'endLine' => 872,
        'startColumn' => 5,
        'endColumn' => 5,
        'couldThrow' => false,
        'isClosure' => false,
        'isGenerator' => false,
        'isVariadic' => true,
        'modifiers' => 1,
        'namespace' => 'Illuminate\\Database\\Eloquent',
        'declaringClassName' => 'Illuminate\\Database\\Eloquent\\Model',
        'implementingClassName' => 'Illuminate\\Database\\Eloquent\\Model',
        'currentClassName' => 'Illuminate\\Database\\Eloquent\\Model',
        'aliasName' => NULL,
      ),
      'loadMorph' => 
      array (
        'name' => 'loadMorph',
        'parameters' => 
        array (
          'relation' => 
          array (
            'name' => 'relation',
            'default' => NULL,
            'type' => NULL,
            'isVariadic' => false,
            'byRef' => false,
            'isPromoted' => false,
            'attributes' => 
            array (
            ),
            'startLine' => 881,
            'endLine' => 881,
            'startColumn' => 31,
            'endColumn' => 39,
            'parameterIndex' => 0,
            'isOptional' => false,
          ),
          'relations' => 
          array (
            'name' => 'relations',
            'default' => NULL,
            'type' => NULL,
            'isVariadic' => false,
            'byRef' => false,
            'isPromoted' => false,
            'attributes' => 
            array (
            ),
            'startLine' => 881,
            'endLine' => 881,
            'startColumn' => 42,
            'endColumn' => 51,
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
 * Eager load relationships on the polymorphic relation of a model.
 *
 * @param  string  $relation
 * @param  array  $relations
 * @return $this
 */',
        'startLine' => 881,
        'endLine' => 892,
        'startColumn' => 5,
        'endColumn' => 5,
        'couldThrow' => false,
        'isClosure' => false,
        'isGenerator' => false,
        'isVariadic' => false,
        'modifiers' => 1,
        'namespace' => 'Illuminate\\Database\\Eloquent',
        'declaringClassName' => 'Illuminate\\Database\\Eloquent\\Model',
        'implementingClassName' => 'Illuminate\\Database\\Eloquent\\Model',
        'currentClassName' => 'Illuminate\\Database\\Eloquent\\Model',
        'aliasName' => NULL,
      ),
      'loadMissing' => 
      array (
        'name' => 'loadMissing',
        'parameters' => 
        array (
          'relations' => 
          array (
            'name' => 'relations',
            'default' => NULL,
            'type' => NULL,
            'isVariadic' => false,
            'byRef' => false,
            'isPromoted' => false,
            'attributes' => 
            array (
            ),
            'startLine' => 900,
            'endLine' => 900,
            'startColumn' => 33,
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
 * Eager load relations on the model if they are not already eager loaded.
 *
 * @param  array|string  $relations
 * @return $this
 */',
        'startLine' => 900,
        'endLine' => 907,
        'startColumn' => 5,
        'endColumn' => 5,
        'couldThrow' => false,
        'isClosure' => false,
        'isGenerator' => false,
        'isVariadic' => true,
        'modifiers' => 1,
        'namespace' => 'Illuminate\\Database\\Eloquent',
        'declaringClassName' => 'Illuminate\\Database\\Eloquent\\Model',
        'implementingClassName' => 'Illuminate\\Database\\Eloquent\\Model',
        'currentClassName' => 'Illuminate\\Database\\Eloquent\\Model',
        'aliasName' => NULL,
      ),
      'loadAggregate' => 
      array (
        'name' => 'loadAggregate',
        'parameters' => 
        array (
          'relations' => 
          array (
            'name' => 'relations',
            'default' => NULL,
            'type' => NULL,
            'isVariadic' => false,
            'byRef' => false,
            'isPromoted' => false,
            'attributes' => 
            array (
            ),
            'startLine' => 917,
            'endLine' => 917,
            'startColumn' => 35,
            'endColumn' => 44,
            'parameterIndex' => 0,
            'isOptional' => false,
          ),
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
            'startLine' => 917,
            'endLine' => 917,
            'startColumn' => 47,
            'endColumn' => 53,
            'parameterIndex' => 1,
            'isOptional' => false,
          ),
          'function' => 
          array (
            'name' => 'function',
            'default' => 
            array (
              'code' => 'null',
              'attributes' => 
              array (
                'startLine' => 917,
                'endLine' => 917,
                'startTokenPos' => 3382,
                'startFilePos' => 25676,
                'endTokenPos' => 3382,
                'endFilePos' => 25679,
              ),
            ),
            'type' => NULL,
            'isVariadic' => false,
            'byRef' => false,
            'isPromoted' => false,
            'attributes' => 
            array (
            ),
            'startLine' => 917,
            'endLine' => 917,
            'startColumn' => 56,
            'endColumn' => 71,
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
 * Eager load relation\'s column aggregations on the model.
 *
 * @param  array|string  $relations
 * @param  string  $column
 * @param  string|null  $function
 * @return $this
 */',
        'startLine' => 917,
        'endLine' => 922,
        'startColumn' => 5,
        'endColumn' => 5,
        'couldThrow' => false,
        'isClosure' => false,
        'isGenerator' => false,
        'isVariadic' => false,
        'modifiers' => 1,
        'namespace' => 'Illuminate\\Database\\Eloquent',
        'declaringClassName' => 'Illuminate\\Database\\Eloquent\\Model',
        'implementingClassName' => 'Illuminate\\Database\\Eloquent\\Model',
        'currentClassName' => 'Illuminate\\Database\\Eloquent\\Model',
        'aliasName' => NULL,
      ),
      'loadCount' => 
      array (
        'name' => 'loadCount',
        'parameters' => 
        array (
          'relations' => 
          array (
            'name' => 'relations',
            'default' => NULL,
            'type' => NULL,
            'isVariadic' => false,
            'byRef' => false,
            'isPromoted' => false,
            'attributes' => 
            array (
            ),
            'startLine' => 930,
            'endLine' => 930,
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
 * Eager load relation counts on the model.
 *
 * @param  array|string  $relations
 * @return $this
 */',
        'startLine' => 930,
        'endLine' => 935,
        'startColumn' => 5,
        'endColumn' => 5,
        'couldThrow' => false,
        'isClosure' => false,
        'isGenerator' => false,
        'isVariadic' => true,
        'modifiers' => 1,
        'namespace' => 'Illuminate\\Database\\Eloquent',
        'declaringClassName' => 'Illuminate\\Database\\Eloquent\\Model',
        'implementingClassName' => 'Illuminate\\Database\\Eloquent\\Model',
        'currentClassName' => 'Illuminate\\Database\\Eloquent\\Model',
        'aliasName' => NULL,
      ),
      'loadMax' => 
      array (
        'name' => 'loadMax',
        'parameters' => 
        array (
          'relations' => 
          array (
            'name' => 'relations',
            'default' => NULL,
            'type' => NULL,
            'isVariadic' => false,
            'byRef' => false,
            'isPromoted' => false,
            'attributes' => 
            array (
            ),
            'startLine' => 944,
            'endLine' => 944,
            'startColumn' => 29,
            'endColumn' => 38,
            'parameterIndex' => 0,
            'isOptional' => false,
          ),
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
            'startLine' => 944,
            'endLine' => 944,
            'startColumn' => 41,
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
 * Eager load relation max column values on the model.
 *
 * @param  array|string  $relations
 * @param  string  $column
 * @return $this
 */',
        'startLine' => 944,
        'endLine' => 947,
        'startColumn' => 5,
        'endColumn' => 5,
        'couldThrow' => false,
        'isClosure' => false,
        'isGenerator' => false,
        'isVariadic' => false,
        'modifiers' => 1,
        'namespace' => 'Illuminate\\Database\\Eloquent',
        'declaringClassName' => 'Illuminate\\Database\\Eloquent\\Model',
        'implementingClassName' => 'Illuminate\\Database\\Eloquent\\Model',
        'currentClassName' => 'Illuminate\\Database\\Eloquent\\Model',
        'aliasName' => NULL,
      ),
      'loadMin' => 
      array (
        'name' => 'loadMin',
        'parameters' => 
        array (
          'relations' => 
          array (
            'name' => 'relations',
            'default' => NULL,
            'type' => NULL,
            'isVariadic' => false,
            'byRef' => false,
            'isPromoted' => false,
            'attributes' => 
            array (
            ),
            'startLine' => 956,
            'endLine' => 956,
            'startColumn' => 29,
            'endColumn' => 38,
            'parameterIndex' => 0,
            'isOptional' => false,
          ),
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
            'startLine' => 956,
            'endLine' => 956,
            'startColumn' => 41,
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
 * Eager load relation min column values on the model.
 *
 * @param  array|string  $relations
 * @param  string  $column
 * @return $this
 */',
        'startLine' => 956,
        'endLine' => 959,
        'startColumn' => 5,
        'endColumn' => 5,
        'couldThrow' => false,
        'isClosure' => false,
        'isGenerator' => false,
        'isVariadic' => false,
        'modifiers' => 1,
        'namespace' => 'Illuminate\\Database\\Eloquent',
        'declaringClassName' => 'Illuminate\\Database\\Eloquent\\Model',
        'implementingClassName' => 'Illuminate\\Database\\Eloquent\\Model',
        'currentClassName' => 'Illuminate\\Database\\Eloquent\\Model',
        'aliasName' => NULL,
      ),
      'loadSum' => 
      array (
        'name' => 'loadSum',
        'parameters' => 
        array (
          'relations' => 
          array (
            'name' => 'relations',
            'default' => NULL,
            'type' => NULL,
            'isVariadic' => false,
            'byRef' => false,
            'isPromoted' => false,
            'attributes' => 
            array (
            ),
            'startLine' => 968,
            'endLine' => 968,
            'startColumn' => 29,
            'endColumn' => 38,
            'parameterIndex' => 0,
            'isOptional' => false,
          ),
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
            'startLine' => 968,
            'endLine' => 968,
            'startColumn' => 41,
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
 * Eager load relation\'s column summations on the model.
 *
 * @param  array|string  $relations
 * @param  string  $column
 * @return $this
 */',
        'startLine' => 968,
        'endLine' => 971,
        'startColumn' => 5,
        'endColumn' => 5,
        'couldThrow' => false,
        'isClosure' => false,
        'isGenerator' => false,
        'isVariadic' => false,
        'modifiers' => 1,
        'namespace' => 'Illuminate\\Database\\Eloquent',
        'declaringClassName' => 'Illuminate\\Database\\Eloquent\\Model',
        'implementingClassName' => 'Illuminate\\Database\\Eloquent\\Model',
        'currentClassName' => 'Illuminate\\Database\\Eloquent\\Model',
        'aliasName' => NULL,
      ),
      'loadAvg' => 
      array (
        'name' => 'loadAvg',
        'parameters' => 
        array (
          'relations' => 
          array (
            'name' => 'relations',
            'default' => NULL,
            'type' => NULL,
            'isVariadic' => false,
            'byRef' => false,
            'isPromoted' => false,
            'attributes' => 
            array (
            ),
            'startLine' => 980,
            'endLine' => 980,
            'startColumn' => 29,
            'endColumn' => 38,
            'parameterIndex' => 0,
            'isOptional' => false,
          ),
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
            'startLine' => 980,
            'endLine' => 980,
            'startColumn' => 41,
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
 * Eager load relation average column values on the model.
 *
 * @param  array|string  $relations
 * @param  string  $column
 * @return $this
 */',
        'startLine' => 980,
        'endLine' => 983,
        'startColumn' => 5,
        'endColumn' => 5,
        'couldThrow' => false,
        'isClosure' => false,
        'isGenerator' => false,
        'isVariadic' => false,
        'modifiers' => 1,
        'namespace' => 'Illuminate\\Database\\Eloquent',
        'declaringClassName' => 'Illuminate\\Database\\Eloquent\\Model',
        'implementingClassName' => 'Illuminate\\Database\\Eloquent\\Model',
        'currentClassName' => 'Illuminate\\Database\\Eloquent\\Model',
        'aliasName' => NULL,
      ),
      'loadExists' => 
      array (
        'name' => 'loadExists',
        'parameters' => 
        array (
          'relations' => 
          array (
            'name' => 'relations',
            'default' => NULL,
            'type' => NULL,
            'isVariadic' => false,
            'byRef' => false,
            'isPromoted' => false,
            'attributes' => 
            array (
            ),
            'startLine' => 991,
            'endLine' => 991,
            'startColumn' => 32,
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
 * Eager load related model existence values on the model.
 *
 * @param  array|string  $relations
 * @return $this
 */',
        'startLine' => 991,
        'endLine' => 994,
        'startColumn' => 5,
        'endColumn' => 5,
        'couldThrow' => false,
        'isClosure' => false,
        'isGenerator' => false,
        'isVariadic' => false,
        'modifiers' => 1,
        'namespace' => 'Illuminate\\Database\\Eloquent',
        'declaringClassName' => 'Illuminate\\Database\\Eloquent\\Model',
        'implementingClassName' => 'Illuminate\\Database\\Eloquent\\Model',
        'currentClassName' => 'Illuminate\\Database\\Eloquent\\Model',
        'aliasName' => NULL,
      ),
      'loadMorphAggregate' => 
      array (
        'name' => 'loadMorphAggregate',
        'parameters' => 
        array (
          'relation' => 
          array (
            'name' => 'relation',
            'default' => NULL,
            'type' => NULL,
            'isVariadic' => false,
            'byRef' => false,
            'isPromoted' => false,
            'attributes' => 
            array (
            ),
            'startLine' => 1005,
            'endLine' => 1005,
            'startColumn' => 40,
            'endColumn' => 48,
            'parameterIndex' => 0,
            'isOptional' => false,
          ),
          'relations' => 
          array (
            'name' => 'relations',
            'default' => NULL,
            'type' => NULL,
            'isVariadic' => false,
            'byRef' => false,
            'isPromoted' => false,
            'attributes' => 
            array (
            ),
            'startLine' => 1005,
            'endLine' => 1005,
            'startColumn' => 51,
            'endColumn' => 60,
            'parameterIndex' => 1,
            'isOptional' => false,
          ),
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
            'startLine' => 1005,
            'endLine' => 1005,
            'startColumn' => 63,
            'endColumn' => 69,
            'parameterIndex' => 2,
            'isOptional' => false,
          ),
          'function' => 
          array (
            'name' => 'function',
            'default' => 
            array (
              'code' => 'null',
              'attributes' => 
              array (
                'startLine' => 1005,
                'endLine' => 1005,
                'startTokenPos' => 3654,
                'startFilePos' => 27959,
                'endTokenPos' => 3654,
                'endFilePos' => 27962,
              ),
            ),
            'type' => NULL,
            'isVariadic' => false,
            'byRef' => false,
            'isPromoted' => false,
            'attributes' => 
            array (
            ),
            'startLine' => 1005,
            'endLine' => 1005,
            'startColumn' => 72,
            'endColumn' => 87,
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
 * Eager load relationship column aggregation on the polymorphic relation of a model.
 *
 * @param  string  $relation
 * @param  array  $relations
 * @param  string  $column
 * @param  string|null  $function
 * @return $this
 */',
        'startLine' => 1005,
        'endLine' => 1016,
        'startColumn' => 5,
        'endColumn' => 5,
        'couldThrow' => false,
        'isClosure' => false,
        'isGenerator' => false,
        'isVariadic' => false,
        'modifiers' => 1,
        'namespace' => 'Illuminate\\Database\\Eloquent',
        'declaringClassName' => 'Illuminate\\Database\\Eloquent\\Model',
        'implementingClassName' => 'Illuminate\\Database\\Eloquent\\Model',
        'currentClassName' => 'Illuminate\\Database\\Eloquent\\Model',
        'aliasName' => NULL,
      ),
      'loadMorphCount' => 
      array (
        'name' => 'loadMorphCount',
        'parameters' => 
        array (
          'relation' => 
          array (
            'name' => 'relation',
            'default' => NULL,
            'type' => NULL,
            'isVariadic' => false,
            'byRef' => false,
            'isPromoted' => false,
            'attributes' => 
            array (
            ),
            'startLine' => 1025,
            'endLine' => 1025,
            'startColumn' => 36,
            'endColumn' => 44,
            'parameterIndex' => 0,
            'isOptional' => false,
          ),
          'relations' => 
          array (
            'name' => 'relations',
            'default' => NULL,
            'type' => NULL,
            'isVariadic' => false,
            'byRef' => false,
            'isPromoted' => false,
            'attributes' => 
            array (
            ),
            'startLine' => 1025,
            'endLine' => 1025,
            'startColumn' => 47,
            'endColumn' => 56,
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
 * Eager load relationship counts on the polymorphic relation of a model.
 *
 * @param  string  $relation
 * @param  array  $relations
 * @return $this
 */',
        'startLine' => 1025,
        'endLine' => 1028,
        'startColumn' => 5,
        'endColumn' => 5,
        'couldThrow' => false,
        'isClosure' => false,
        'isGenerator' => false,
        'isVariadic' => false,
        'modifiers' => 1,
        'namespace' => 'Illuminate\\Database\\Eloquent',
        'declaringClassName' => 'Illuminate\\Database\\Eloquent\\Model',
        'implementingClassName' => 'Illuminate\\Database\\Eloquent\\Model',
        'currentClassName' => 'Illuminate\\Database\\Eloquent\\Model',
        'aliasName' => NULL,
      ),
      'loadMorphMax' => 
      array (
        'name' => 'loadMorphMax',
        'parameters' => 
        array (
          'relation' => 
          array (
            'name' => 'relation',
            'default' => NULL,
            'type' => NULL,
            'isVariadic' => false,
            'byRef' => false,
            'isPromoted' => false,
            'attributes' => 
            array (
            ),
            'startLine' => 1038,
            'endLine' => 1038,
            'startColumn' => 34,
            'endColumn' => 42,
            'parameterIndex' => 0,
            'isOptional' => false,
          ),
          'relations' => 
          array (
            'name' => 'relations',
            'default' => NULL,
            'type' => NULL,
            'isVariadic' => false,
            'byRef' => false,
            'isPromoted' => false,
            'attributes' => 
            array (
            ),
            'startLine' => 1038,
            'endLine' => 1038,
            'startColumn' => 45,
            'endColumn' => 54,
            'parameterIndex' => 1,
            'isOptional' => false,
          ),
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
            'startLine' => 1038,
            'endLine' => 1038,
            'startColumn' => 57,
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
 * Eager load relationship max column values on the polymorphic relation of a model.
 *
 * @param  string  $relation
 * @param  array  $relations
 * @param  string  $column
 * @return $this
 */',
        'startLine' => 1038,
        'endLine' => 1041,
        'startColumn' => 5,
        'endColumn' => 5,
        'couldThrow' => false,
        'isClosure' => false,
        'isGenerator' => false,
        'isVariadic' => false,
        'modifiers' => 1,
        'namespace' => 'Illuminate\\Database\\Eloquent',
        'declaringClassName' => 'Illuminate\\Database\\Eloquent\\Model',
        'implementingClassName' => 'Illuminate\\Database\\Eloquent\\Model',
        'currentClassName' => 'Illuminate\\Database\\Eloquent\\Model',
        'aliasName' => NULL,
      ),
      'loadMorphMin' => 
      array (
        'name' => 'loadMorphMin',
        'parameters' => 
        array (
          'relation' => 
          array (
            'name' => 'relation',
            'default' => NULL,
            'type' => NULL,
            'isVariadic' => false,
            'byRef' => false,
            'isPromoted' => false,
            'attributes' => 
            array (
            ),
            'startLine' => 1051,
            'endLine' => 1051,
            'startColumn' => 34,
            'endColumn' => 42,
            'parameterIndex' => 0,
            'isOptional' => false,
          ),
          'relations' => 
          array (
            'name' => 'relations',
            'default' => NULL,
            'type' => NULL,
            'isVariadic' => false,
            'byRef' => false,
            'isPromoted' => false,
            'attributes' => 
            array (
            ),
            'startLine' => 1051,
            'endLine' => 1051,
            'startColumn' => 45,
            'endColumn' => 54,
            'parameterIndex' => 1,
            'isOptional' => false,
          ),
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
            'startLine' => 1051,
            'endLine' => 1051,
            'startColumn' => 57,
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
 * Eager load relationship min column values on the polymorphic relation of a model.
 *
 * @param  string  $relation
 * @param  array  $relations
 * @param  string  $column
 * @return $this
 */',
        'startLine' => 1051,
        'endLine' => 1054,
        'startColumn' => 5,
        'endColumn' => 5,
        'couldThrow' => false,
        'isClosure' => false,
        'isGenerator' => false,
        'isVariadic' => false,
        'modifiers' => 1,
        'namespace' => 'Illuminate\\Database\\Eloquent',
        'declaringClassName' => 'Illuminate\\Database\\Eloquent\\Model',
        'implementingClassName' => 'Illuminate\\Database\\Eloquent\\Model',
        'currentClassName' => 'Illuminate\\Database\\Eloquent\\Model',
        'aliasName' => NULL,
      ),
      'loadMorphSum' => 
      array (
        'name' => 'loadMorphSum',
        'parameters' => 
        array (
          'relation' => 
          array (
            'name' => 'relation',
            'default' => NULL,
            'type' => NULL,
            'isVariadic' => false,
            'byRef' => false,
            'isPromoted' => false,
            'attributes' => 
            array (
            ),
            'startLine' => 1064,
            'endLine' => 1064,
            'startColumn' => 34,
            'endColumn' => 42,
            'parameterIndex' => 0,
            'isOptional' => false,
          ),
          'relations' => 
          array (
            'name' => 'relations',
            'default' => NULL,
            'type' => NULL,
            'isVariadic' => false,
            'byRef' => false,
            'isPromoted' => false,
            'attributes' => 
            array (
            ),
            'startLine' => 1064,
            'endLine' => 1064,
            'startColumn' => 45,
            'endColumn' => 54,
            'parameterIndex' => 1,
            'isOptional' => false,
          ),
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
            'startLine' => 1064,
            'endLine' => 1064,
            'startColumn' => 57,
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
 * Eager load relationship column summations on the polymorphic relation of a model.
 *
 * @param  string  $relation
 * @param  array  $relations
 * @param  string  $column
 * @return $this
 */',
        'startLine' => 1064,
        'endLine' => 1067,
        'startColumn' => 5,
        'endColumn' => 5,
        'couldThrow' => false,
        'isClosure' => false,
        'isGenerator' => false,
        'isVariadic' => false,
        'modifiers' => 1,
        'namespace' => 'Illuminate\\Database\\Eloquent',
        'declaringClassName' => 'Illuminate\\Database\\Eloquent\\Model',
        'implementingClassName' => 'Illuminate\\Database\\Eloquent\\Model',
        'currentClassName' => 'Illuminate\\Database\\Eloquent\\Model',
        'aliasName' => NULL,
      ),
      'loadMorphAvg' => 
      array (
        'name' => 'loadMorphAvg',
        'parameters' => 
        array (
          'relation' => 
          array (
            'name' => 'relation',
            'default' => NULL,
            'type' => NULL,
            'isVariadic' => false,
            'byRef' => false,
            'isPromoted' => false,
            'attributes' => 
            array (
            ),
            'startLine' => 1077,
            'endLine' => 1077,
            'startColumn' => 34,
            'endColumn' => 42,
            'parameterIndex' => 0,
            'isOptional' => false,
          ),
          'relations' => 
          array (
            'name' => 'relations',
            'default' => NULL,
            'type' => NULL,
            'isVariadic' => false,
            'byRef' => false,
            'isPromoted' => false,
            'attributes' => 
            array (
            ),
            'startLine' => 1077,
            'endLine' => 1077,
            'startColumn' => 45,
            'endColumn' => 54,
            'parameterIndex' => 1,
            'isOptional' => false,
          ),
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
            'startLine' => 1077,
            'endLine' => 1077,
            'startColumn' => 57,
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
 * Eager load relationship average column values on the polymorphic relation of a model.
 *
 * @param  string  $relation
 * @param  array  $relations
 * @param  string  $column
 * @return $this
 */',
        'startLine' => 1077,
        'endLine' => 1080,
        'startColumn' => 5,
        'endColumn' => 5,
        'couldThrow' => false,
        'isClosure' => false,
        'isGenerator' => false,
        'isVariadic' => false,
        'modifiers' => 1,
        'namespace' => 'Illuminate\\Database\\Eloquent',
        'declaringClassName' => 'Illuminate\\Database\\Eloquent\\Model',
        'implementingClassName' => 'Illuminate\\Database\\Eloquent\\Model',
        'currentClassName' => 'Illuminate\\Database\\Eloquent\\Model',
        'aliasName' => NULL,
      ),
      'increment' => 
      array (
        'name' => 'increment',
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
            'startLine' => 1090,
            'endLine' => 1090,
            'startColumn' => 34,
            'endColumn' => 40,
            'parameterIndex' => 0,
            'isOptional' => false,
          ),
          'amount' => 
          array (
            'name' => 'amount',
            'default' => 
            array (
              'code' => '1',
              'attributes' => 
              array (
                'startLine' => 1090,
                'endLine' => 1090,
                'startTokenPos' => 3939,
                'startFilePos' => 30359,
                'endTokenPos' => 3939,
                'endFilePos' => 30359,
              ),
            ),
            'type' => NULL,
            'isVariadic' => false,
            'byRef' => false,
            'isPromoted' => false,
            'attributes' => 
            array (
            ),
            'startLine' => 1090,
            'endLine' => 1090,
            'startColumn' => 43,
            'endColumn' => 53,
            'parameterIndex' => 1,
            'isOptional' => true,
          ),
          'extra' => 
          array (
            'name' => 'extra',
            'default' => 
            array (
              'code' => '[]',
              'attributes' => 
              array (
                'startLine' => 1090,
                'endLine' => 1090,
                'startTokenPos' => 3948,
                'startFilePos' => 30377,
                'endTokenPos' => 3949,
                'endFilePos' => 30378,
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
            'startLine' => 1090,
            'endLine' => 1090,
            'startColumn' => 56,
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
 * Increment a column\'s value by a given amount.
 *
 * @param  string  $column
 * @param  float|int  $amount
 * @param  array  $extra
 * @return int
 */',
        'startLine' => 1090,
        'endLine' => 1093,
        'startColumn' => 5,
        'endColumn' => 5,
        'couldThrow' => false,
        'isClosure' => false,
        'isGenerator' => false,
        'isVariadic' => false,
        'modifiers' => 2,
        'namespace' => 'Illuminate\\Database\\Eloquent',
        'declaringClassName' => 'Illuminate\\Database\\Eloquent\\Model',
        'implementingClassName' => 'Illuminate\\Database\\Eloquent\\Model',
        'currentClassName' => 'Illuminate\\Database\\Eloquent\\Model',
        'aliasName' => NULL,
      ),
      'decrement' => 
      array (
        'name' => 'decrement',
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
            'startLine' => 1103,
            'endLine' => 1103,
            'startColumn' => 34,
            'endColumn' => 40,
            'parameterIndex' => 0,
            'isOptional' => false,
          ),
          'amount' => 
          array (
            'name' => 'amount',
            'default' => 
            array (
              'code' => '1',
              'attributes' => 
              array (
                'startLine' => 1103,
                'endLine' => 1103,
                'startTokenPos' => 3990,
                'startFilePos' => 30718,
                'endTokenPos' => 3990,
                'endFilePos' => 30718,
              ),
            ),
            'type' => NULL,
            'isVariadic' => false,
            'byRef' => false,
            'isPromoted' => false,
            'attributes' => 
            array (
            ),
            'startLine' => 1103,
            'endLine' => 1103,
            'startColumn' => 43,
            'endColumn' => 53,
            'parameterIndex' => 1,
            'isOptional' => true,
          ),
          'extra' => 
          array (
            'name' => 'extra',
            'default' => 
            array (
              'code' => '[]',
              'attributes' => 
              array (
                'startLine' => 1103,
                'endLine' => 1103,
                'startTokenPos' => 3999,
                'startFilePos' => 30736,
                'endTokenPos' => 4000,
                'endFilePos' => 30737,
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
            'startLine' => 1103,
            'endLine' => 1103,
            'startColumn' => 56,
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
 * Decrement a column\'s value by a given amount.
 *
 * @param  string  $column
 * @param  float|int  $amount
 * @param  array  $extra
 * @return int
 */',
        'startLine' => 1103,
        'endLine' => 1106,
        'startColumn' => 5,
        'endColumn' => 5,
        'couldThrow' => false,
        'isClosure' => false,
        'isGenerator' => false,
        'isVariadic' => false,
        'modifiers' => 2,
        'namespace' => 'Illuminate\\Database\\Eloquent',
        'declaringClassName' => 'Illuminate\\Database\\Eloquent\\Model',
        'implementingClassName' => 'Illuminate\\Database\\Eloquent\\Model',
        'currentClassName' => 'Illuminate\\Database\\Eloquent\\Model',
        'aliasName' => NULL,
      ),
      'incrementOrDecrement' => 
      array (
        'name' => 'incrementOrDecrement',
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
            'startLine' => 1117,
            'endLine' => 1117,
            'startColumn' => 45,
            'endColumn' => 51,
            'parameterIndex' => 0,
            'isOptional' => false,
          ),
          'amount' => 
          array (
            'name' => 'amount',
            'default' => NULL,
            'type' => NULL,
            'isVariadic' => false,
            'byRef' => false,
            'isPromoted' => false,
            'attributes' => 
            array (
            ),
            'startLine' => 1117,
            'endLine' => 1117,
            'startColumn' => 54,
            'endColumn' => 60,
            'parameterIndex' => 1,
            'isOptional' => false,
          ),
          'extra' => 
          array (
            'name' => 'extra',
            'default' => NULL,
            'type' => NULL,
            'isVariadic' => false,
            'byRef' => false,
            'isPromoted' => false,
            'attributes' => 
            array (
            ),
            'startLine' => 1117,
            'endLine' => 1117,
            'startColumn' => 63,
            'endColumn' => 68,
            'parameterIndex' => 2,
            'isOptional' => false,
          ),
          'method' => 
          array (
            'name' => 'method',
            'default' => NULL,
            'type' => NULL,
            'isVariadic' => false,
            'byRef' => false,
            'isPromoted' => false,
            'attributes' => 
            array (
            ),
            'startLine' => 1117,
            'endLine' => 1117,
            'startColumn' => 71,
            'endColumn' => 77,
            'parameterIndex' => 3,
            'isOptional' => false,
          ),
        ),
        'returnsReference' => false,
        'returnType' => NULL,
        'attributes' => 
        array (
        ),
        'docComment' => '/**
 * Run the increment or decrement method on the model.
 *
 * @param  string  $column
 * @param  float|int  $amount
 * @param  array  $extra
 * @param  string  $method
 * @return int
 */',
        'startLine' => 1117,
        'endLine' => 1144,
        'startColumn' => 5,
        'endColumn' => 5,
        'couldThrow' => false,
        'isClosure' => false,
        'isGenerator' => false,
        'isVariadic' => false,
        'modifiers' => 2,
        'namespace' => 'Illuminate\\Database\\Eloquent',
        'declaringClassName' => 'Illuminate\\Database\\Eloquent\\Model',
        'implementingClassName' => 'Illuminate\\Database\\Eloquent\\Model',
        'currentClassName' => 'Illuminate\\Database\\Eloquent\\Model',
        'aliasName' => NULL,
      ),
      'update' => 
      array (
        'name' => 'update',
        'parameters' => 
        array (
          'attributes' => 
          array (
            'name' => 'attributes',
            'default' => 
            array (
              'code' => '[]',
              'attributes' => 
              array (
                'startLine' => 1153,
                'endLine' => 1153,
                'startTokenPos' => 4305,
                'startFilePos' => 32326,
                'endTokenPos' => 4306,
                'endFilePos' => 32327,
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
            'startLine' => 1153,
            'endLine' => 1153,
            'startColumn' => 28,
            'endColumn' => 49,
            'parameterIndex' => 0,
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
                'startLine' => 1153,
                'endLine' => 1153,
                'startTokenPos' => 4315,
                'startFilePos' => 32347,
                'endTokenPos' => 4316,
                'endFilePos' => 32348,
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
            'startLine' => 1153,
            'endLine' => 1153,
            'startColumn' => 52,
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
 * Update the model in the database.
 *
 * @param  array<string, mixed>  $attributes
 * @param  array<string, mixed>  $options
 * @return bool
 */',
        'startLine' => 1153,
        'endLine' => 1160,
        'startColumn' => 5,
        'endColumn' => 5,
        'couldThrow' => false,
        'isClosure' => false,
        'isGenerator' => false,
        'isVariadic' => false,
        'modifiers' => 1,
        'namespace' => 'Illuminate\\Database\\Eloquent',
        'declaringClassName' => 'Illuminate\\Database\\Eloquent\\Model',
        'implementingClassName' => 'Illuminate\\Database\\Eloquent\\Model',
        'currentClassName' => 'Illuminate\\Database\\Eloquent\\Model',
        'aliasName' => NULL,
      ),
      'updateOrFail' => 
      array (
        'name' => 'updateOrFail',
        'parameters' => 
        array (
          'attributes' => 
          array (
            'name' => 'attributes',
            'default' => 
            array (
              'code' => '[]',
              'attributes' => 
              array (
                'startLine' => 1171,
                'endLine' => 1171,
                'startTokenPos' => 4371,
                'startFilePos' => 32775,
                'endTokenPos' => 4372,
                'endFilePos' => 32776,
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
            'startLine' => 1171,
            'endLine' => 1171,
            'startColumn' => 34,
            'endColumn' => 55,
            'parameterIndex' => 0,
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
                'startLine' => 1171,
                'endLine' => 1171,
                'startTokenPos' => 4381,
                'startFilePos' => 32796,
                'endTokenPos' => 4382,
                'endFilePos' => 32797,
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
            'startLine' => 1171,
            'endLine' => 1171,
            'startColumn' => 58,
            'endColumn' => 76,
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
 * Update the model in the database within a transaction.
 *
 * @param  array<string, mixed>  $attributes
 * @param  array<string, mixed>  $options
 * @return bool
 *
 * @throws \\Throwable
 */',
        'startLine' => 1171,
        'endLine' => 1178,
        'startColumn' => 5,
        'endColumn' => 5,
        'couldThrow' => false,
        'isClosure' => false,
        'isGenerator' => false,
        'isVariadic' => false,
        'modifiers' => 1,
        'namespace' => 'Illuminate\\Database\\Eloquent',
        'declaringClassName' => 'Illuminate\\Database\\Eloquent\\Model',
        'implementingClassName' => 'Illuminate\\Database\\Eloquent\\Model',
        'currentClassName' => 'Illuminate\\Database\\Eloquent\\Model',
        'aliasName' => NULL,
      ),
      'updateQuietly' => 
      array (
        'name' => 'updateQuietly',
        'parameters' => 
        array (
          'attributes' => 
          array (
            'name' => 'attributes',
            'default' => 
            array (
              'code' => '[]',
              'attributes' => 
              array (
                'startLine' => 1187,
                'endLine' => 1187,
                'startTokenPos' => 4437,
                'startFilePos' => 33204,
                'endTokenPos' => 4438,
                'endFilePos' => 33205,
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
            'startLine' => 1187,
            'endLine' => 1187,
            'startColumn' => 35,
            'endColumn' => 56,
            'parameterIndex' => 0,
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
                'startLine' => 1187,
                'endLine' => 1187,
                'startTokenPos' => 4447,
                'startFilePos' => 33225,
                'endTokenPos' => 4448,
                'endFilePos' => 33226,
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
            'startLine' => 1187,
            'endLine' => 1187,
            'startColumn' => 59,
            'endColumn' => 77,
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
 * Update the model in the database without raising any events.
 *
 * @param  array<string, mixed>  $attributes
 * @param  array<string, mixed>  $options
 * @return bool
 */',
        'startLine' => 1187,
        'endLine' => 1194,
        'startColumn' => 5,
        'endColumn' => 5,
        'couldThrow' => false,
        'isClosure' => false,
        'isGenerator' => false,
        'isVariadic' => false,
        'modifiers' => 1,
        'namespace' => 'Illuminate\\Database\\Eloquent',
        'declaringClassName' => 'Illuminate\\Database\\Eloquent\\Model',
        'implementingClassName' => 'Illuminate\\Database\\Eloquent\\Model',
        'currentClassName' => 'Illuminate\\Database\\Eloquent\\Model',
        'aliasName' => NULL,
      ),
      'incrementQuietly' => 
      array (
        'name' => 'incrementQuietly',
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
            'startLine' => 1204,
            'endLine' => 1204,
            'startColumn' => 41,
            'endColumn' => 47,
            'parameterIndex' => 0,
            'isOptional' => false,
          ),
          'amount' => 
          array (
            'name' => 'amount',
            'default' => 
            array (
              'code' => '1',
              'attributes' => 
              array (
                'startLine' => 1204,
                'endLine' => 1204,
                'startTokenPos' => 4504,
                'startFilePos' => 33649,
                'endTokenPos' => 4504,
                'endFilePos' => 33649,
              ),
            ),
            'type' => NULL,
            'isVariadic' => false,
            'byRef' => false,
            'isPromoted' => false,
            'attributes' => 
            array (
            ),
            'startLine' => 1204,
            'endLine' => 1204,
            'startColumn' => 50,
            'endColumn' => 60,
            'parameterIndex' => 1,
            'isOptional' => true,
          ),
          'extra' => 
          array (
            'name' => 'extra',
            'default' => 
            array (
              'code' => '[]',
              'attributes' => 
              array (
                'startLine' => 1204,
                'endLine' => 1204,
                'startTokenPos' => 4513,
                'startFilePos' => 33667,
                'endTokenPos' => 4514,
                'endFilePos' => 33668,
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
            'startLine' => 1204,
            'endLine' => 1204,
            'startColumn' => 63,
            'endColumn' => 79,
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
 * Increment a column\'s value by a given amount without raising any events.
 *
 * @param  string  $column
 * @param  float|int  $amount
 * @param  array  $extra
 * @return int
 */',
        'startLine' => 1204,
        'endLine' => 1209,
        'startColumn' => 5,
        'endColumn' => 5,
        'couldThrow' => false,
        'isClosure' => false,
        'isGenerator' => false,
        'isVariadic' => false,
        'modifiers' => 2,
        'namespace' => 'Illuminate\\Database\\Eloquent',
        'declaringClassName' => 'Illuminate\\Database\\Eloquent\\Model',
        'implementingClassName' => 'Illuminate\\Database\\Eloquent\\Model',
        'currentClassName' => 'Illuminate\\Database\\Eloquent\\Model',
        'aliasName' => NULL,
      ),
      'decrementQuietly' => 
      array (
        'name' => 'decrementQuietly',
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
            'startLine' => 1219,
            'endLine' => 1219,
            'startColumn' => 41,
            'endColumn' => 47,
            'parameterIndex' => 0,
            'isOptional' => false,
          ),
          'amount' => 
          array (
            'name' => 'amount',
            'default' => 
            array (
              'code' => '1',
              'attributes' => 
              array (
                'startLine' => 1219,
                'endLine' => 1219,
                'startTokenPos' => 4569,
                'startFilePos' => 34096,
                'endTokenPos' => 4569,
                'endFilePos' => 34096,
              ),
            ),
            'type' => NULL,
            'isVariadic' => false,
            'byRef' => false,
            'isPromoted' => false,
            'attributes' => 
            array (
            ),
            'startLine' => 1219,
            'endLine' => 1219,
            'startColumn' => 50,
            'endColumn' => 60,
            'parameterIndex' => 1,
            'isOptional' => true,
          ),
          'extra' => 
          array (
            'name' => 'extra',
            'default' => 
            array (
              'code' => '[]',
              'attributes' => 
              array (
                'startLine' => 1219,
                'endLine' => 1219,
                'startTokenPos' => 4578,
                'startFilePos' => 34114,
                'endTokenPos' => 4579,
                'endFilePos' => 34115,
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
            'startLine' => 1219,
            'endLine' => 1219,
            'startColumn' => 63,
            'endColumn' => 79,
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
 * Decrement a column\'s value by a given amount without raising any events.
 *
 * @param  string  $column
 * @param  float|int  $amount
 * @param  array  $extra
 * @return int
 */',
        'startLine' => 1219,
        'endLine' => 1224,
        'startColumn' => 5,
        'endColumn' => 5,
        'couldThrow' => false,
        'isClosure' => false,
        'isGenerator' => false,
        'isVariadic' => false,
        'modifiers' => 2,
        'namespace' => 'Illuminate\\Database\\Eloquent',
        'declaringClassName' => 'Illuminate\\Database\\Eloquent\\Model',
        'implementingClassName' => 'Illuminate\\Database\\Eloquent\\Model',
        'currentClassName' => 'Illuminate\\Database\\Eloquent\\Model',
        'aliasName' => NULL,
      ),
      'incrementEach' => 
      array (
        'name' => 'incrementEach',
        'parameters' => 
        array (
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
            'startLine' => 1233,
            'endLine' => 1233,
            'startColumn' => 38,
            'endColumn' => 51,
            'parameterIndex' => 0,
            'isOptional' => false,
          ),
          'extra' => 
          array (
            'name' => 'extra',
            'default' => 
            array (
              'code' => '[]',
              'attributes' => 
              array (
                'startLine' => 1233,
                'endLine' => 1233,
                'startTokenPos' => 4638,
                'startFilePos' => 34537,
                'endTokenPos' => 4639,
                'endFilePos' => 34538,
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
            'startLine' => 1233,
            'endLine' => 1233,
            'startColumn' => 54,
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
 * Increment each given column\'s value by the given amounts.
 *
 * @param  array<string, float|int>  $columns
 * @param  array<string, mixed>  $extra
 * @return int
 */',
        'startLine' => 1233,
        'endLine' => 1236,
        'startColumn' => 5,
        'endColumn' => 5,
        'couldThrow' => false,
        'isClosure' => false,
        'isGenerator' => false,
        'isVariadic' => false,
        'modifiers' => 2,
        'namespace' => 'Illuminate\\Database\\Eloquent',
        'declaringClassName' => 'Illuminate\\Database\\Eloquent\\Model',
        'implementingClassName' => 'Illuminate\\Database\\Eloquent\\Model',
        'currentClassName' => 'Illuminate\\Database\\Eloquent\\Model',
        'aliasName' => NULL,
      ),
      'decrementEach' => 
      array (
        'name' => 'decrementEach',
        'parameters' => 
        array (
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
            'startLine' => 1245,
            'endLine' => 1245,
            'startColumn' => 38,
            'endColumn' => 51,
            'parameterIndex' => 0,
            'isOptional' => false,
          ),
          'extra' => 
          array (
            'name' => 'extra',
            'default' => 
            array (
              'code' => '[]',
              'attributes' => 
              array (
                'startLine' => 1245,
                'endLine' => 1245,
                'startTokenPos' => 4681,
                'startFilePos' => 34906,
                'endTokenPos' => 4682,
                'endFilePos' => 34907,
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
            'startLine' => 1245,
            'endLine' => 1245,
            'startColumn' => 54,
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
 * Decrement each given column\'s value by the given amounts.
 *
 * @param  array<string, float|int>  $columns
 * @param  array<string, mixed>  $extra
 * @return int
 */',
        'startLine' => 1245,
        'endLine' => 1248,
        'startColumn' => 5,
        'endColumn' => 5,
        'couldThrow' => false,
        'isClosure' => false,
        'isGenerator' => false,
        'isVariadic' => false,
        'modifiers' => 2,
        'namespace' => 'Illuminate\\Database\\Eloquent',
        'declaringClassName' => 'Illuminate\\Database\\Eloquent\\Model',
        'implementingClassName' => 'Illuminate\\Database\\Eloquent\\Model',
        'currentClassName' => 'Illuminate\\Database\\Eloquent\\Model',
        'aliasName' => NULL,
      ),
      'incrementEachQuietly' => 
      array (
        'name' => 'incrementEachQuietly',
        'parameters' => 
        array (
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
            'startLine' => 1257,
            'endLine' => 1257,
            'startColumn' => 45,
            'endColumn' => 58,
            'parameterIndex' => 0,
            'isOptional' => false,
          ),
          'extra' => 
          array (
            'name' => 'extra',
            'default' => 
            array (
              'code' => '[]',
              'attributes' => 
              array (
                'startLine' => 1257,
                'endLine' => 1257,
                'startTokenPos' => 4724,
                'startFilePos' => 35309,
                'endTokenPos' => 4725,
                'endFilePos' => 35310,
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
            'startLine' => 1257,
            'endLine' => 1257,
            'startColumn' => 61,
            'endColumn' => 77,
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
 * Increment each given column\'s value by the given amounts without raising any events.
 *
 * @param  array<string, float|int>  $columns
 * @param  array<string, mixed>  $extra
 * @return int
 */',
        'startLine' => 1257,
        'endLine' => 1262,
        'startColumn' => 5,
        'endColumn' => 5,
        'couldThrow' => false,
        'isClosure' => false,
        'isGenerator' => false,
        'isVariadic' => false,
        'modifiers' => 2,
        'namespace' => 'Illuminate\\Database\\Eloquent',
        'declaringClassName' => 'Illuminate\\Database\\Eloquent\\Model',
        'implementingClassName' => 'Illuminate\\Database\\Eloquent\\Model',
        'currentClassName' => 'Illuminate\\Database\\Eloquent\\Model',
        'aliasName' => NULL,
      ),
      'decrementEachQuietly' => 
      array (
        'name' => 'decrementEachQuietly',
        'parameters' => 
        array (
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
            'startLine' => 1271,
            'endLine' => 1271,
            'startColumn' => 45,
            'endColumn' => 58,
            'parameterIndex' => 0,
            'isOptional' => false,
          ),
          'extra' => 
          array (
            'name' => 'extra',
            'default' => 
            array (
              'code' => '[]',
              'attributes' => 
              array (
                'startLine' => 1271,
                'endLine' => 1271,
                'startTokenPos' => 4781,
                'startFilePos' => 35766,
                'endTokenPos' => 4782,
                'endFilePos' => 35767,
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
            'startLine' => 1271,
            'endLine' => 1271,
            'startColumn' => 61,
            'endColumn' => 77,
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
 * Decrement each given column\'s value by the given amounts without raising any events.
 *
 * @param  array<string, float|int>  $columns
 * @param  array<string, mixed>  $extra
 * @return int
 */',
        'startLine' => 1271,
        'endLine' => 1276,
        'startColumn' => 5,
        'endColumn' => 5,
        'couldThrow' => false,
        'isClosure' => false,
        'isGenerator' => false,
        'isVariadic' => false,
        'modifiers' => 2,
        'namespace' => 'Illuminate\\Database\\Eloquent',
        'declaringClassName' => 'Illuminate\\Database\\Eloquent\\Model',
        'implementingClassName' => 'Illuminate\\Database\\Eloquent\\Model',
        'currentClassName' => 'Illuminate\\Database\\Eloquent\\Model',
        'aliasName' => NULL,
      ),
      'incrementOrDecrementEach' => 
      array (
        'name' => 'incrementOrDecrementEach',
        'parameters' => 
        array (
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
            'startLine' => 1286,
            'endLine' => 1286,
            'startColumn' => 49,
            'endColumn' => 62,
            'parameterIndex' => 0,
            'isOptional' => false,
          ),
          'extra' => 
          array (
            'name' => 'extra',
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
            'startLine' => 1286,
            'endLine' => 1286,
            'startColumn' => 65,
            'endColumn' => 76,
            'parameterIndex' => 1,
            'isOptional' => false,
          ),
          'method' => 
          array (
            'name' => 'method',
            'default' => NULL,
            'type' => 
            array (
              'class' => 'PHPStan\\BetterReflection\\Reflection\\ReflectionNamedType',
              'data' => 
              array (
                'name' => 'string',
                'isIdentifier' => true,
              ),
            ),
            'isVariadic' => false,
            'byRef' => false,
            'isPromoted' => false,
            'attributes' => 
            array (
            ),
            'startLine' => 1286,
            'endLine' => 1286,
            'startColumn' => 79,
            'endColumn' => 92,
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
 * Run the incrementEach or decrementEach method on the model.
 *
 * @param  array<string, float|int>  $columns
 * @param  array<string, mixed>  $extra
 * @param  string  $method
 * @return int
 */',
        'startLine' => 1286,
        'endLine' => 1322,
        'startColumn' => 5,
        'endColumn' => 5,
        'couldThrow' => false,
        'isClosure' => false,
        'isGenerator' => false,
        'isVariadic' => false,
        'modifiers' => 2,
        'namespace' => 'Illuminate\\Database\\Eloquent',
        'declaringClassName' => 'Illuminate\\Database\\Eloquent\\Model',
        'implementingClassName' => 'Illuminate\\Database\\Eloquent\\Model',
        'currentClassName' => 'Illuminate\\Database\\Eloquent\\Model',
        'aliasName' => NULL,
      ),
      'push' => 
      array (
        'name' => 'push',
        'parameters' => 
        array (
        ),
        'returnsReference' => false,
        'returnType' => NULL,
        'attributes' => 
        array (
        ),
        'docComment' => '/**
 * Save the model and all of its relationships.
 *
 * @return bool
 */',
        'startLine' => 1329,
        'endLine' => 1353,
        'startColumn' => 5,
        'endColumn' => 5,
        'couldThrow' => false,
        'isClosure' => false,
        'isGenerator' => false,
        'isVariadic' => false,
        'modifiers' => 1,
        'namespace' => 'Illuminate\\Database\\Eloquent',
        'declaringClassName' => 'Illuminate\\Database\\Eloquent\\Model',
        'implementingClassName' => 'Illuminate\\Database\\Eloquent\\Model',
        'currentClassName' => 'Illuminate\\Database\\Eloquent\\Model',
        'aliasName' => NULL,
      ),
      'pushQuietly' => 
      array (
        'name' => 'pushQuietly',
        'parameters' => 
        array (
        ),
        'returnsReference' => false,
        'returnType' => NULL,
        'attributes' => 
        array (
        ),
        'docComment' => '/**
 * Save the model and all of its relationships without raising any events to the parent model.
 *
 * @return bool
 */',
        'startLine' => 1360,
        'endLine' => 1363,
        'startColumn' => 5,
        'endColumn' => 5,
        'couldThrow' => false,
        'isClosure' => false,
        'isGenerator' => false,
        'isVariadic' => false,
        'modifiers' => 1,
        'namespace' => 'Illuminate\\Database\\Eloquent',
        'declaringClassName' => 'Illuminate\\Database\\Eloquent\\Model',
        'implementingClassName' => 'Illuminate\\Database\\Eloquent\\Model',
        'currentClassName' => 'Illuminate\\Database\\Eloquent\\Model',
        'aliasName' => NULL,
      ),
      'saveQuietly' => 
      array (
        'name' => 'saveQuietly',
        'parameters' => 
        array (
          'options' => 
          array (
            'name' => 'options',
            'default' => 
            array (
              'code' => '[]',
              'attributes' => 
              array (
                'startLine' => 1371,
                'endLine' => 1371,
                'startTokenPos' => 5346,
                'startFilePos' => 38912,
                'endTokenPos' => 5347,
                'endFilePos' => 38913,
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
            'startLine' => 1371,
            'endLine' => 1371,
            'startColumn' => 33,
            'endColumn' => 51,
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
 * Save the model to the database without raising any events.
 *
 * @param  array  $options
 * @return bool
 */',
        'startLine' => 1371,
        'endLine' => 1374,
        'startColumn' => 5,
        'endColumn' => 5,
        'couldThrow' => false,
        'isClosure' => false,
        'isGenerator' => false,
        'isVariadic' => false,
        'modifiers' => 1,
        'namespace' => 'Illuminate\\Database\\Eloquent',
        'declaringClassName' => 'Illuminate\\Database\\Eloquent\\Model',
        'implementingClassName' => 'Illuminate\\Database\\Eloquent\\Model',
        'currentClassName' => 'Illuminate\\Database\\Eloquent\\Model',
        'aliasName' => NULL,
      ),
      'save' => 
      array (
        'name' => 'save',
        'parameters' => 
        array (
          'options' => 
          array (
            'name' => 'options',
            'default' => 
            array (
              'code' => '[]',
              'attributes' => 
              array (
                'startLine' => 1382,
                'endLine' => 1382,
                'startTokenPos' => 5390,
                'startFilePos' => 39154,
                'endTokenPos' => 5391,
                'endFilePos' => 39155,
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
            'startLine' => 1382,
            'endLine' => 1382,
            'startColumn' => 26,
            'endColumn' => 44,
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
 * Save the model to the database.
 *
 * @param  array  $options
 * @return bool
 */',
        'startLine' => 1382,
        'endLine' => 1423,
        'startColumn' => 5,
        'endColumn' => 5,
        'couldThrow' => false,
        'isClosure' => false,
        'isGenerator' => false,
        'isVariadic' => false,
        'modifiers' => 1,
        'namespace' => 'Illuminate\\Database\\Eloquent',
        'declaringClassName' => 'Illuminate\\Database\\Eloquent\\Model',
        'implementingClassName' => 'Illuminate\\Database\\Eloquent\\Model',
        'currentClassName' => 'Illuminate\\Database\\Eloquent\\Model',
        'aliasName' => NULL,
      ),
      'saveOrIgnore' => 
      array (
        'name' => 'saveOrIgnore',
        'parameters' => 
        array (
          'options' => 
          array (
            'name' => 'options',
            'default' => 
            array (
              'code' => '[]',
              'attributes' => 
              array (
                'startLine' => 1432,
                'endLine' => 1432,
                'startTokenPos' => 5595,
                'startFilePos' => 41082,
                'endTokenPos' => 5596,
                'endFilePos' => 41083,
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
            'startLine' => 1432,
            'endLine' => 1432,
            'startColumn' => 34,
            'endColumn' => 52,
            'parameterIndex' => 0,
            'isOptional' => true,
          ),
          'uniqueBy' => 
          array (
            'name' => 'uniqueBy',
            'default' => 
            array (
              'code' => 'null',
              'attributes' => 
              array (
                'startLine' => 1432,
                'endLine' => 1432,
                'startTokenPos' => 5609,
                'startFilePos' => 41116,
                'endTokenPos' => 5609,
                'endFilePos' => 41119,
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
                      'name' => 'array',
                      'isIdentifier' => true,
                    ),
                  ),
                  1 => 
                  array (
                    'class' => 'PHPStan\\BetterReflection\\Reflection\\ReflectionNamedType',
                    'data' => 
                    array (
                      'name' => 'string',
                      'isIdentifier' => true,
                    ),
                  ),
                  2 => 
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
            'startLine' => 1432,
            'endLine' => 1432,
            'startColumn' => 55,
            'endColumn' => 88,
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
 * Save the model to the database, ignoring specific unique constraint conflicts.
 *
 * @param  array  $options
 * @param  array|string|null  $uniqueBy
 * @return bool
 */',
        'startLine' => 1432,
        'endLine' => 1458,
        'startColumn' => 5,
        'endColumn' => 5,
        'couldThrow' => false,
        'isClosure' => false,
        'isGenerator' => false,
        'isVariadic' => false,
        'modifiers' => 1,
        'namespace' => 'Illuminate\\Database\\Eloquent',
        'declaringClassName' => 'Illuminate\\Database\\Eloquent\\Model',
        'implementingClassName' => 'Illuminate\\Database\\Eloquent\\Model',
        'currentClassName' => 'Illuminate\\Database\\Eloquent\\Model',
        'aliasName' => NULL,
      ),
      'saveOrFail' => 
      array (
        'name' => 'saveOrFail',
        'parameters' => 
        array (
          'options' => 
          array (
            'name' => 'options',
            'default' => 
            array (
              'code' => '[]',
              'attributes' => 
              array (
                'startLine' => 1468,
                'endLine' => 1468,
                'startTokenPos' => 5772,
                'startFilePos' => 41993,
                'endTokenPos' => 5773,
                'endFilePos' => 41994,
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
            'startLine' => 1468,
            'endLine' => 1468,
            'startColumn' => 32,
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
 * Save the model to the database within a transaction.
 *
 * @param  array  $options
 * @return bool
 *
 * @throws \\Throwable
 */',
        'startLine' => 1468,
        'endLine' => 1471,
        'startColumn' => 5,
        'endColumn' => 5,
        'couldThrow' => false,
        'isClosure' => false,
        'isGenerator' => false,
        'isVariadic' => false,
        'modifiers' => 1,
        'namespace' => 'Illuminate\\Database\\Eloquent',
        'declaringClassName' => 'Illuminate\\Database\\Eloquent\\Model',
        'implementingClassName' => 'Illuminate\\Database\\Eloquent\\Model',
        'currentClassName' => 'Illuminate\\Database\\Eloquent\\Model',
        'aliasName' => NULL,
      ),
      'finishSave' => 
      array (
        'name' => 'finishSave',
        'parameters' => 
        array (
          'options' => 
          array (
            'name' => 'options',
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
            'startLine' => 1479,
            'endLine' => 1479,
            'startColumn' => 35,
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
 * Perform any actions that are necessary after the model is saved.
 *
 * @param  array  $options
 * @return void
 */',
        'startLine' => 1479,
        'endLine' => 1488,
        'startColumn' => 5,
        'endColumn' => 5,
        'couldThrow' => false,
        'isClosure' => false,
        'isGenerator' => false,
        'isVariadic' => false,
        'modifiers' => 2,
        'namespace' => 'Illuminate\\Database\\Eloquent',
        'declaringClassName' => 'Illuminate\\Database\\Eloquent\\Model',
        'implementingClassName' => 'Illuminate\\Database\\Eloquent\\Model',
        'currentClassName' => 'Illuminate\\Database\\Eloquent\\Model',
        'aliasName' => NULL,
      ),
      'performUpdate' => 
      array (
        'name' => 'performUpdate',
        'parameters' => 
        array (
          'query' => 
          array (
            'name' => 'query',
            'default' => NULL,
            'type' => 
            array (
              'class' => 'PHPStan\\BetterReflection\\Reflection\\ReflectionNamedType',
              'data' => 
              array (
                'name' => 'Illuminate\\Database\\Eloquent\\Builder',
                'isIdentifier' => false,
              ),
            ),
            'isVariadic' => false,
            'byRef' => false,
            'isPromoted' => false,
            'attributes' => 
            array (
            ),
            'startLine' => 1496,
            'endLine' => 1496,
            'startColumn' => 38,
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
 * Perform a model update operation.
 *
 * @param  \\Illuminate\\Database\\Eloquent\\Builder<static>  $query
 * @return bool
 */',
        'startLine' => 1496,
        'endLine' => 1526,
        'startColumn' => 5,
        'endColumn' => 5,
        'couldThrow' => false,
        'isClosure' => false,
        'isGenerator' => false,
        'isVariadic' => false,
        'modifiers' => 2,
        'namespace' => 'Illuminate\\Database\\Eloquent',
        'declaringClassName' => 'Illuminate\\Database\\Eloquent\\Model',
        'implementingClassName' => 'Illuminate\\Database\\Eloquent\\Model',
        'currentClassName' => 'Illuminate\\Database\\Eloquent\\Model',
        'aliasName' => NULL,
      ),
      'setKeysForSelectQuery' => 
      array (
        'name' => 'setKeysForSelectQuery',
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
            'startLine' => 1534,
            'endLine' => 1534,
            'startColumn' => 46,
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
 * Set the keys for a select query.
 *
 * @param  \\Illuminate\\Database\\Eloquent\\Builder<static>  $query
 * @return \\Illuminate\\Database\\Eloquent\\Builder<static>
 */',
        'startLine' => 1534,
        'endLine' => 1539,
        'startColumn' => 5,
        'endColumn' => 5,
        'couldThrow' => false,
        'isClosure' => false,
        'isGenerator' => false,
        'isVariadic' => false,
        'modifiers' => 2,
        'namespace' => 'Illuminate\\Database\\Eloquent',
        'declaringClassName' => 'Illuminate\\Database\\Eloquent\\Model',
        'implementingClassName' => 'Illuminate\\Database\\Eloquent\\Model',
        'currentClassName' => 'Illuminate\\Database\\Eloquent\\Model',
        'aliasName' => NULL,
      ),
      'getKeyForSelectQuery' => 
      array (
        'name' => 'getKeyForSelectQuery',
        'parameters' => 
        array (
        ),
        'returnsReference' => false,
        'returnType' => NULL,
        'attributes' => 
        array (
        ),
        'docComment' => '/**
 * Get the primary key value for a select query.
 *
 * @return mixed
 */',
        'startLine' => 1546,
        'endLine' => 1549,
        'startColumn' => 5,
        'endColumn' => 5,
        'couldThrow' => false,
        'isClosure' => false,
        'isGenerator' => false,
        'isVariadic' => false,
        'modifiers' => 2,
        'namespace' => 'Illuminate\\Database\\Eloquent',
        'declaringClassName' => 'Illuminate\\Database\\Eloquent\\Model',
        'implementingClassName' => 'Illuminate\\Database\\Eloquent\\Model',
        'currentClassName' => 'Illuminate\\Database\\Eloquent\\Model',
        'aliasName' => NULL,
      ),
      'setKeysForSaveQuery' => 
      array (
        'name' => 'setKeysForSaveQuery',
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
            'startLine' => 1557,
            'endLine' => 1557,
            'startColumn' => 44,
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
 * Set the keys for a save update query.
 *
 * @param  \\Illuminate\\Database\\Eloquent\\Builder<static>  $query
 * @return \\Illuminate\\Database\\Eloquent\\Builder<static>
 */',
        'startLine' => 1557,
        'endLine' => 1562,
        'startColumn' => 5,
        'endColumn' => 5,
        'couldThrow' => false,
        'isClosure' => false,
        'isGenerator' => false,
        'isVariadic' => false,
        'modifiers' => 2,
        'namespace' => 'Illuminate\\Database\\Eloquent',
        'declaringClassName' => 'Illuminate\\Database\\Eloquent\\Model',
        'implementingClassName' => 'Illuminate\\Database\\Eloquent\\Model',
        'currentClassName' => 'Illuminate\\Database\\Eloquent\\Model',
        'aliasName' => NULL,
      ),
      'getKeyForSaveQuery' => 
      array (
        'name' => 'getKeyForSaveQuery',
        'parameters' => 
        array (
        ),
        'returnsReference' => false,
        'returnType' => NULL,
        'attributes' => 
        array (
        ),
        'docComment' => '/**
 * Get the primary key value for a save query.
 *
 * @return mixed
 */',
        'startLine' => 1569,
        'endLine' => 1572,
        'startColumn' => 5,
        'endColumn' => 5,
        'couldThrow' => false,
        'isClosure' => false,
        'isGenerator' => false,
        'isVariadic' => false,
        'modifiers' => 2,
        'namespace' => 'Illuminate\\Database\\Eloquent',
        'declaringClassName' => 'Illuminate\\Database\\Eloquent\\Model',
        'implementingClassName' => 'Illuminate\\Database\\Eloquent\\Model',
        'currentClassName' => 'Illuminate\\Database\\Eloquent\\Model',
        'aliasName' => NULL,
      ),
      'performInsert' => 
      array (
        'name' => 'performInsert',
        'parameters' => 
        array (
          'query' => 
          array (
            'name' => 'query',
            'default' => NULL,
            'type' => 
            array (
              'class' => 'PHPStan\\BetterReflection\\Reflection\\ReflectionNamedType',
              'data' => 
              array (
                'name' => 'Illuminate\\Database\\Eloquent\\Builder',
                'isIdentifier' => false,
              ),
            ),
            'isVariadic' => false,
            'byRef' => false,
            'isPromoted' => false,
            'attributes' => 
            array (
            ),
            'startLine' => 1580,
            'endLine' => 1580,
            'startColumn' => 38,
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
 * Perform a model insert operation.
 *
 * @param  \\Illuminate\\Database\\Eloquent\\Builder<static>  $query
 * @return bool
 */',
        'startLine' => 1580,
        'endLine' => 1627,
        'startColumn' => 5,
        'endColumn' => 5,
        'couldThrow' => false,
        'isClosure' => false,
        'isGenerator' => false,
        'isVariadic' => false,
        'modifiers' => 2,
        'namespace' => 'Illuminate\\Database\\Eloquent',
        'declaringClassName' => 'Illuminate\\Database\\Eloquent\\Model',
        'implementingClassName' => 'Illuminate\\Database\\Eloquent\\Model',
        'currentClassName' => 'Illuminate\\Database\\Eloquent\\Model',
        'aliasName' => NULL,
      ),
      'performInsertOrIgnore' => 
      array (
        'name' => 'performInsertOrIgnore',
        'parameters' => 
        array (
          'query' => 
          array (
            'name' => 'query',
            'default' => NULL,
            'type' => 
            array (
              'class' => 'PHPStan\\BetterReflection\\Reflection\\ReflectionNamedType',
              'data' => 
              array (
                'name' => 'Illuminate\\Database\\Eloquent\\Builder',
                'isIdentifier' => false,
              ),
            ),
            'isVariadic' => false,
            'byRef' => false,
            'isPromoted' => false,
            'attributes' => 
            array (
            ),
            'startLine' => 1636,
            'endLine' => 1636,
            'startColumn' => 46,
            'endColumn' => 59,
            'parameterIndex' => 0,
            'isOptional' => false,
          ),
          'uniqueBy' => 
          array (
            'name' => 'uniqueBy',
            'default' => NULL,
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
                      'name' => 'array',
                      'isIdentifier' => true,
                    ),
                  ),
                  1 => 
                  array (
                    'class' => 'PHPStan\\BetterReflection\\Reflection\\ReflectionNamedType',
                    'data' => 
                    array (
                      'name' => 'string',
                      'isIdentifier' => true,
                    ),
                  ),
                  2 => 
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
            'startLine' => 1636,
            'endLine' => 1636,
            'startColumn' => 62,
            'endColumn' => 88,
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
 * Perform a model insert operation, ignoring specific unique constraint conflicts.
 *
 * @param  \\Illuminate\\Database\\Eloquent\\Builder<static>  $query
 * @param  array|string|null  $uniqueBy
 * @return bool
 */',
        'startLine' => 1636,
        'endLine' => 1676,
        'startColumn' => 5,
        'endColumn' => 5,
        'couldThrow' => false,
        'isClosure' => false,
        'isGenerator' => false,
        'isVariadic' => false,
        'modifiers' => 2,
        'namespace' => 'Illuminate\\Database\\Eloquent',
        'declaringClassName' => 'Illuminate\\Database\\Eloquent\\Model',
        'implementingClassName' => 'Illuminate\\Database\\Eloquent\\Model',
        'currentClassName' => 'Illuminate\\Database\\Eloquent\\Model',
        'aliasName' => NULL,
      ),
      'insertAndSetId' => 
      array (
        'name' => 'insertAndSetId',
        'parameters' => 
        array (
          'query' => 
          array (
            'name' => 'query',
            'default' => NULL,
            'type' => 
            array (
              'class' => 'PHPStan\\BetterReflection\\Reflection\\ReflectionNamedType',
              'data' => 
              array (
                'name' => 'Illuminate\\Database\\Eloquent\\Builder',
                'isIdentifier' => false,
              ),
            ),
            'isVariadic' => false,
            'byRef' => false,
            'isPromoted' => false,
            'attributes' => 
            array (
            ),
            'startLine' => 1685,
            'endLine' => 1685,
            'startColumn' => 39,
            'endColumn' => 52,
            'parameterIndex' => 0,
            'isOptional' => false,
          ),
          'attributes' => 
          array (
            'name' => 'attributes',
            'default' => NULL,
            'type' => NULL,
            'isVariadic' => false,
            'byRef' => false,
            'isPromoted' => false,
            'attributes' => 
            array (
            ),
            'startLine' => 1685,
            'endLine' => 1685,
            'startColumn' => 55,
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
 * Insert the given attributes and set the ID on the model.
 *
 * @param  \\Illuminate\\Database\\Eloquent\\Builder<static>  $query
 * @param  array<string, mixed>  $attributes
 * @return void
 */',
        'startLine' => 1685,
        'endLine' => 1690,
        'startColumn' => 5,
        'endColumn' => 5,
        'couldThrow' => false,
        'isClosure' => false,
        'isGenerator' => false,
        'isVariadic' => false,
        'modifiers' => 2,
        'namespace' => 'Illuminate\\Database\\Eloquent',
        'declaringClassName' => 'Illuminate\\Database\\Eloquent\\Model',
        'implementingClassName' => 'Illuminate\\Database\\Eloquent\\Model',
        'currentClassName' => 'Illuminate\\Database\\Eloquent\\Model',
        'aliasName' => NULL,
      ),
      'destroy' => 
      array (
        'name' => 'destroy',
        'parameters' => 
        array (
          'ids' => 
          array (
            'name' => 'ids',
            'default' => NULL,
            'type' => NULL,
            'isVariadic' => false,
            'byRef' => false,
            'isPromoted' => false,
            'attributes' => 
            array (
            ),
            'startLine' => 1698,
            'endLine' => 1698,
            'startColumn' => 36,
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
 * Destroy the models for the given IDs.
 *
 * @param  \\Illuminate\\Support\\Collection|array|int|string  $ids
 * @return int
 */',
        'startLine' => 1698,
        'endLine' => 1728,
        'startColumn' => 5,
        'endColumn' => 5,
        'couldThrow' => false,
        'isClosure' => false,
        'isGenerator' => false,
        'isVariadic' => true,
        'modifiers' => 17,
        'namespace' => 'Illuminate\\Database\\Eloquent',
        'declaringClassName' => 'Illuminate\\Database\\Eloquent\\Model',
        'implementingClassName' => 'Illuminate\\Database\\Eloquent\\Model',
        'currentClassName' => 'Illuminate\\Database\\Eloquent\\Model',
        'aliasName' => NULL,
      ),
      'delete' => 
      array (
        'name' => 'delete',
        'parameters' => 
        array (
        ),
        'returnsReference' => false,
        'returnType' => NULL,
        'attributes' => 
        array (
        ),
        'docComment' => '/**
 * Delete the model from the database.
 *
 * @return bool|null
 *
 * @throws \\LogicException
 */',
        'startLine' => 1737,
        'endLine' => 1769,
        'startColumn' => 5,
        'endColumn' => 5,
        'couldThrow' => false,
        'isClosure' => false,
        'isGenerator' => false,
        'isVariadic' => false,
        'modifiers' => 1,
        'namespace' => 'Illuminate\\Database\\Eloquent',
        'declaringClassName' => 'Illuminate\\Database\\Eloquent\\Model',
        'implementingClassName' => 'Illuminate\\Database\\Eloquent\\Model',
        'currentClassName' => 'Illuminate\\Database\\Eloquent\\Model',
        'aliasName' => NULL,
      ),
      'deleteQuietly' => 
      array (
        'name' => 'deleteQuietly',
        'parameters' => 
        array (
        ),
        'returnsReference' => false,
        'returnType' => NULL,
        'attributes' => 
        array (
        ),
        'docComment' => '/**
 * Delete the model from the database without raising any events.
 *
 * @return bool
 */',
        'startLine' => 1776,
        'endLine' => 1779,
        'startColumn' => 5,
        'endColumn' => 5,
        'couldThrow' => false,
        'isClosure' => false,
        'isGenerator' => false,
        'isVariadic' => false,
        'modifiers' => 1,
        'namespace' => 'Illuminate\\Database\\Eloquent',
        'declaringClassName' => 'Illuminate\\Database\\Eloquent\\Model',
        'implementingClassName' => 'Illuminate\\Database\\Eloquent\\Model',
        'currentClassName' => 'Illuminate\\Database\\Eloquent\\Model',
        'aliasName' => NULL,
      ),
      'deleteOrFail' => 
      array (
        'name' => 'deleteOrFail',
        'parameters' => 
        array (
        ),
        'returnsReference' => false,
        'returnType' => NULL,
        'attributes' => 
        array (
        ),
        'docComment' => '/**
 * Delete the model from the database within a transaction.
 *
 * @return bool|null
 *
 * @throws \\Throwable
 */',
        'startLine' => 1788,
        'endLine' => 1795,
        'startColumn' => 5,
        'endColumn' => 5,
        'couldThrow' => false,
        'isClosure' => false,
        'isGenerator' => false,
        'isVariadic' => false,
        'modifiers' => 1,
        'namespace' => 'Illuminate\\Database\\Eloquent',
        'declaringClassName' => 'Illuminate\\Database\\Eloquent\\Model',
        'implementingClassName' => 'Illuminate\\Database\\Eloquent\\Model',
        'currentClassName' => 'Illuminate\\Database\\Eloquent\\Model',
        'aliasName' => NULL,
      ),
      'forceDelete' => 
      array (
        'name' => 'forceDelete',
        'parameters' => 
        array (
        ),
        'returnsReference' => false,
        'returnType' => NULL,
        'attributes' => 
        array (
        ),
        'docComment' => '/**
 * Force a hard delete on a soft deleted model.
 *
 * This method protects developers from running forceDelete when the trait is missing.
 *
 * @return bool|null
 */',
        'startLine' => 1804,
        'endLine' => 1807,
        'startColumn' => 5,
        'endColumn' => 5,
        'couldThrow' => false,
        'isClosure' => false,
        'isGenerator' => false,
        'isVariadic' => false,
        'modifiers' => 1,
        'namespace' => 'Illuminate\\Database\\Eloquent',
        'declaringClassName' => 'Illuminate\\Database\\Eloquent\\Model',
        'implementingClassName' => 'Illuminate\\Database\\Eloquent\\Model',
        'currentClassName' => 'Illuminate\\Database\\Eloquent\\Model',
        'aliasName' => NULL,
      ),
      'forceDestroy' => 
      array (
        'name' => 'forceDestroy',
        'parameters' => 
        array (
          'ids' => 
          array (
            'name' => 'ids',
            'default' => NULL,
            'type' => NULL,
            'isVariadic' => false,
            'byRef' => false,
            'isPromoted' => false,
            'attributes' => 
            array (
            ),
            'startLine' => 1817,
            'endLine' => 1817,
            'startColumn' => 41,
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
 * Force a hard destroy on a soft deleted model.
 *
 * This method protects developers from running forceDestroy when the trait is missing.
 *
 * @param  \\Illuminate\\Support\\Collection|array|int|string  $ids
 * @return bool|null
 */',
        'startLine' => 1817,
        'endLine' => 1820,
        'startColumn' => 5,
        'endColumn' => 5,
        'couldThrow' => false,
        'isClosure' => false,
        'isGenerator' => false,
        'isVariadic' => false,
        'modifiers' => 17,
        'namespace' => 'Illuminate\\Database\\Eloquent',
        'declaringClassName' => 'Illuminate\\Database\\Eloquent\\Model',
        'implementingClassName' => 'Illuminate\\Database\\Eloquent\\Model',
        'currentClassName' => 'Illuminate\\Database\\Eloquent\\Model',
        'aliasName' => NULL,
      ),
      'performDeleteOnModel' => 
      array (
        'name' => 'performDeleteOnModel',
        'parameters' => 
        array (
        ),
        'returnsReference' => false,
        'returnType' => NULL,
        'attributes' => 
        array (
        ),
        'docComment' => '/**
 * Perform the actual delete query on this model instance.
 *
 * @return void
 */',
        'startLine' => 1827,
        'endLine' => 1832,
        'startColumn' => 5,
        'endColumn' => 5,
        'couldThrow' => false,
        'isClosure' => false,
        'isGenerator' => false,
        'isVariadic' => false,
        'modifiers' => 2,
        'namespace' => 'Illuminate\\Database\\Eloquent',
        'declaringClassName' => 'Illuminate\\Database\\Eloquent\\Model',
        'implementingClassName' => 'Illuminate\\Database\\Eloquent\\Model',
        'currentClassName' => 'Illuminate\\Database\\Eloquent\\Model',
        'aliasName' => NULL,
      ),
      'query' => 
      array (
        'name' => 'query',
        'parameters' => 
        array (
        ),
        'returnsReference' => false,
        'returnType' => NULL,
        'attributes' => 
        array (
        ),
        'docComment' => '/**
 * Begin querying the model.
 *
 * @return \\Illuminate\\Database\\Eloquent\\Builder<static>
 */',
        'startLine' => 1839,
        'endLine' => 1842,
        'startColumn' => 5,
        'endColumn' => 5,
        'couldThrow' => false,
        'isClosure' => false,
        'isGenerator' => false,
        'isVariadic' => false,
        'modifiers' => 17,
        'namespace' => 'Illuminate\\Database\\Eloquent',
        'declaringClassName' => 'Illuminate\\Database\\Eloquent\\Model',
        'implementingClassName' => 'Illuminate\\Database\\Eloquent\\Model',
        'currentClassName' => 'Illuminate\\Database\\Eloquent\\Model',
        'aliasName' => NULL,
      ),
      'newQuery' => 
      array (
        'name' => 'newQuery',
        'parameters' => 
        array (
        ),
        'returnsReference' => false,
        'returnType' => NULL,
        'attributes' => 
        array (
        ),
        'docComment' => '/**
 * Get a new query builder for the model\'s table.
 *
 * @return \\Illuminate\\Database\\Eloquent\\Builder<static>
 */',
        'startLine' => 1849,
        'endLine' => 1852,
        'startColumn' => 5,
        'endColumn' => 5,
        'couldThrow' => false,
        'isClosure' => false,
        'isGenerator' => false,
        'isVariadic' => false,
        'modifiers' => 1,
        'namespace' => 'Illuminate\\Database\\Eloquent',
        'declaringClassName' => 'Illuminate\\Database\\Eloquent\\Model',
        'implementingClassName' => 'Illuminate\\Database\\Eloquent\\Model',
        'currentClassName' => 'Illuminate\\Database\\Eloquent\\Model',
        'aliasName' => NULL,
      ),
      'newModelQuery' => 
      array (
        'name' => 'newModelQuery',
        'parameters' => 
        array (
        ),
        'returnsReference' => false,
        'returnType' => NULL,
        'attributes' => 
        array (
        ),
        'docComment' => '/**
 * Get a new query builder that doesn\'t have any global scopes or eager loading.
 *
 * @return \\Illuminate\\Database\\Eloquent\\Builder<static>
 */',
        'startLine' => 1859,
        'endLine' => 1864,
        'startColumn' => 5,
        'endColumn' => 5,
        'couldThrow' => false,
        'isClosure' => false,
        'isGenerator' => false,
        'isVariadic' => false,
        'modifiers' => 1,
        'namespace' => 'Illuminate\\Database\\Eloquent',
        'declaringClassName' => 'Illuminate\\Database\\Eloquent\\Model',
        'implementingClassName' => 'Illuminate\\Database\\Eloquent\\Model',
        'currentClassName' => 'Illuminate\\Database\\Eloquent\\Model',
        'aliasName' => NULL,
      ),
      'newQueryWithoutRelationships' => 
      array (
        'name' => 'newQueryWithoutRelationships',
        'parameters' => 
        array (
        ),
        'returnsReference' => false,
        'returnType' => NULL,
        'attributes' => 
        array (
        ),
        'docComment' => '/**
 * Get a new query builder with no relationships loaded.
 *
 * @return \\Illuminate\\Database\\Eloquent\\Builder<static>
 */',
        'startLine' => 1871,
        'endLine' => 1874,
        'startColumn' => 5,
        'endColumn' => 5,
        'couldThrow' => false,
        'isClosure' => false,
        'isGenerator' => false,
        'isVariadic' => false,
        'modifiers' => 1,
        'namespace' => 'Illuminate\\Database\\Eloquent',
        'declaringClassName' => 'Illuminate\\Database\\Eloquent\\Model',
        'implementingClassName' => 'Illuminate\\Database\\Eloquent\\Model',
        'currentClassName' => 'Illuminate\\Database\\Eloquent\\Model',
        'aliasName' => NULL,
      ),
      'registerGlobalScopes' => 
      array (
        'name' => 'registerGlobalScopes',
        'parameters' => 
        array (
          'builder' => 
          array (
            'name' => 'builder',
            'default' => NULL,
            'type' => NULL,
            'isVariadic' => false,
            'byRef' => false,
            'isPromoted' => false,
            'attributes' => 
            array (
            ),
            'startLine' => 1882,
            'endLine' => 1882,
            'startColumn' => 42,
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
 * Register the global scopes for this builder instance.
 *
 * @param  \\Illuminate\\Database\\Eloquent\\Builder<static>  $builder
 * @return \\Illuminate\\Database\\Eloquent\\Builder<static>
 */',
        'startLine' => 1882,
        'endLine' => 1889,
        'startColumn' => 5,
        'endColumn' => 5,
        'couldThrow' => false,
        'isClosure' => false,
        'isGenerator' => false,
        'isVariadic' => false,
        'modifiers' => 1,
        'namespace' => 'Illuminate\\Database\\Eloquent',
        'declaringClassName' => 'Illuminate\\Database\\Eloquent\\Model',
        'implementingClassName' => 'Illuminate\\Database\\Eloquent\\Model',
        'currentClassName' => 'Illuminate\\Database\\Eloquent\\Model',
        'aliasName' => NULL,
      ),
      'newQueryWithoutScopes' => 
      array (
        'name' => 'newQueryWithoutScopes',
        'parameters' => 
        array (
        ),
        'returnsReference' => false,
        'returnType' => NULL,
        'attributes' => 
        array (
        ),
        'docComment' => '/**
 * Get a new query builder that doesn\'t have any global scopes.
 *
 * @return \\Illuminate\\Database\\Eloquent\\Builder<static>
 */',
        'startLine' => 1896,
        'endLine' => 1901,
        'startColumn' => 5,
        'endColumn' => 5,
        'couldThrow' => false,
        'isClosure' => false,
        'isGenerator' => false,
        'isVariadic' => false,
        'modifiers' => 1,
        'namespace' => 'Illuminate\\Database\\Eloquent',
        'declaringClassName' => 'Illuminate\\Database\\Eloquent\\Model',
        'implementingClassName' => 'Illuminate\\Database\\Eloquent\\Model',
        'currentClassName' => 'Illuminate\\Database\\Eloquent\\Model',
        'aliasName' => NULL,
      ),
      'newQueryWithoutScope' => 
      array (
        'name' => 'newQueryWithoutScope',
        'parameters' => 
        array (
          'scope' => 
          array (
            'name' => 'scope',
            'default' => NULL,
            'type' => NULL,
            'isVariadic' => false,
            'byRef' => false,
            'isPromoted' => false,
            'attributes' => 
            array (
            ),
            'startLine' => 1909,
            'endLine' => 1909,
            'startColumn' => 42,
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
 * Get a new query instance without a given scope.
 *
 * @param  \\Illuminate\\Database\\Eloquent\\Scope|string  $scope
 * @return \\Illuminate\\Database\\Eloquent\\Builder<static>
 */',
        'startLine' => 1909,
        'endLine' => 1912,
        'startColumn' => 5,
        'endColumn' => 5,
        'couldThrow' => false,
        'isClosure' => false,
        'isGenerator' => false,
        'isVariadic' => false,
        'modifiers' => 1,
        'namespace' => 'Illuminate\\Database\\Eloquent',
        'declaringClassName' => 'Illuminate\\Database\\Eloquent\\Model',
        'implementingClassName' => 'Illuminate\\Database\\Eloquent\\Model',
        'currentClassName' => 'Illuminate\\Database\\Eloquent\\Model',
        'aliasName' => NULL,
      ),
      'newQueryForRestoration' => 
      array (
        'name' => 'newQueryForRestoration',
        'parameters' => 
        array (
          'ids' => 
          array (
            'name' => 'ids',
            'default' => NULL,
            'type' => NULL,
            'isVariadic' => false,
            'byRef' => false,
            'isPromoted' => false,
            'attributes' => 
            array (
            ),
            'startLine' => 1920,
            'endLine' => 1920,
            'startColumn' => 44,
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
 * Get a new query to restore one or more models by their queueable IDs.
 *
 * @param  array|int  $ids
 * @return \\Illuminate\\Database\\Eloquent\\Builder<static>
 */',
        'startLine' => 1920,
        'endLine' => 1923,
        'startColumn' => 5,
        'endColumn' => 5,
        'couldThrow' => false,
        'isClosure' => false,
        'isGenerator' => false,
        'isVariadic' => false,
        'modifiers' => 1,
        'namespace' => 'Illuminate\\Database\\Eloquent',
        'declaringClassName' => 'Illuminate\\Database\\Eloquent\\Model',
        'implementingClassName' => 'Illuminate\\Database\\Eloquent\\Model',
        'currentClassName' => 'Illuminate\\Database\\Eloquent\\Model',
        'aliasName' => NULL,
      ),
      'newEloquentBuilder' => 
      array (
        'name' => 'newEloquentBuilder',
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
            'startLine' => 1931,
            'endLine' => 1931,
            'startColumn' => 40,
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
 * Create a new Eloquent query builder for the model.
 *
 * @param  \\Illuminate\\Database\\Query\\Builder  $query
 * @return \\Illuminate\\Database\\Eloquent\\Builder<*>
 */',
        'startLine' => 1931,
        'endLine' => 1940,
        'startColumn' => 5,
        'endColumn' => 5,
        'couldThrow' => false,
        'isClosure' => false,
        'isGenerator' => false,
        'isVariadic' => false,
        'modifiers' => 1,
        'namespace' => 'Illuminate\\Database\\Eloquent',
        'declaringClassName' => 'Illuminate\\Database\\Eloquent\\Model',
        'implementingClassName' => 'Illuminate\\Database\\Eloquent\\Model',
        'currentClassName' => 'Illuminate\\Database\\Eloquent\\Model',
        'aliasName' => NULL,
      ),
      'resolveCustomBuilderClass' => 
      array (
        'name' => 'resolveCustomBuilderClass',
        'parameters' => 
        array (
        ),
        'returnsReference' => false,
        'returnType' => NULL,
        'attributes' => 
        array (
        ),
        'docComment' => '/**
 * Resolve the custom Eloquent builder class from the model attributes.
 *
 * @return class-string<\\Illuminate\\Database\\Eloquent\\Builder>|false
 */',
        'startLine' => 1947,
        'endLine' => 1955,
        'startColumn' => 5,
        'endColumn' => 5,
        'couldThrow' => false,
        'isClosure' => false,
        'isGenerator' => false,
        'isVariadic' => false,
        'modifiers' => 2,
        'namespace' => 'Illuminate\\Database\\Eloquent',
        'declaringClassName' => 'Illuminate\\Database\\Eloquent\\Model',
        'implementingClassName' => 'Illuminate\\Database\\Eloquent\\Model',
        'currentClassName' => 'Illuminate\\Database\\Eloquent\\Model',
        'aliasName' => NULL,
      ),
      'newBaseQueryBuilder' => 
      array (
        'name' => 'newBaseQueryBuilder',
        'parameters' => 
        array (
        ),
        'returnsReference' => false,
        'returnType' => NULL,
        'attributes' => 
        array (
        ),
        'docComment' => '/**
 * Get a new query builder instance for the connection.
 *
 * @return \\Illuminate\\Database\\Query\\Builder
 */',
        'startLine' => 1962,
        'endLine' => 1965,
        'startColumn' => 5,
        'endColumn' => 5,
        'couldThrow' => false,
        'isClosure' => false,
        'isGenerator' => false,
        'isVariadic' => false,
        'modifiers' => 2,
        'namespace' => 'Illuminate\\Database\\Eloquent',
        'declaringClassName' => 'Illuminate\\Database\\Eloquent\\Model',
        'implementingClassName' => 'Illuminate\\Database\\Eloquent\\Model',
        'currentClassName' => 'Illuminate\\Database\\Eloquent\\Model',
        'aliasName' => NULL,
      ),
      'newPivot' => 
      array (
        'name' => 'newPivot',
        'parameters' => 
        array (
          'parent' => 
          array (
            'name' => 'parent',
            'default' => NULL,
            'type' => 
            array (
              'class' => 'PHPStan\\BetterReflection\\Reflection\\ReflectionNamedType',
              'data' => 
              array (
                'name' => 'self',
                'isIdentifier' => false,
              ),
            ),
            'isVariadic' => false,
            'byRef' => false,
            'isPromoted' => false,
            'attributes' => 
            array (
            ),
            'startLine' => 1977,
            'endLine' => 1977,
            'startColumn' => 30,
            'endColumn' => 41,
            'parameterIndex' => 0,
            'isOptional' => false,
          ),
          'attributes' => 
          array (
            'name' => 'attributes',
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
            'startLine' => 1977,
            'endLine' => 1977,
            'startColumn' => 44,
            'endColumn' => 60,
            'parameterIndex' => 1,
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
            'startLine' => 1977,
            'endLine' => 1977,
            'startColumn' => 63,
            'endColumn' => 68,
            'parameterIndex' => 2,
            'isOptional' => false,
          ),
          'exists' => 
          array (
            'name' => 'exists',
            'default' => NULL,
            'type' => NULL,
            'isVariadic' => false,
            'byRef' => false,
            'isPromoted' => false,
            'attributes' => 
            array (
            ),
            'startLine' => 1977,
            'endLine' => 1977,
            'startColumn' => 71,
            'endColumn' => 77,
            'parameterIndex' => 3,
            'isOptional' => false,
          ),
          'using' => 
          array (
            'name' => 'using',
            'default' => 
            array (
              'code' => 'null',
              'attributes' => 
              array (
                'startLine' => 1977,
                'endLine' => 1977,
                'startTokenPos' => 7644,
                'startFilePos' => 56733,
                'endTokenPos' => 7644,
                'endFilePos' => 56736,
              ),
            ),
            'type' => NULL,
            'isVariadic' => false,
            'byRef' => false,
            'isPromoted' => false,
            'attributes' => 
            array (
            ),
            'startLine' => 1977,
            'endLine' => 1977,
            'startColumn' => 80,
            'endColumn' => 92,
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
 * Create a new pivot model instance.
 *
 * @param  \\Illuminate\\Database\\Eloquent\\Model  $parent
 * @param  array<string, mixed>  $attributes
 * @param  string  $table
 * @param  bool  $exists
 * @param  string|null  $using
 * @return \\Illuminate\\Database\\Eloquent\\Relations\\Pivot
 */',
        'startLine' => 1977,
        'endLine' => 1981,
        'startColumn' => 5,
        'endColumn' => 5,
        'couldThrow' => false,
        'isClosure' => false,
        'isGenerator' => false,
        'isVariadic' => false,
        'modifiers' => 1,
        'namespace' => 'Illuminate\\Database\\Eloquent',
        'declaringClassName' => 'Illuminate\\Database\\Eloquent\\Model',
        'implementingClassName' => 'Illuminate\\Database\\Eloquent\\Model',
        'currentClassName' => 'Illuminate\\Database\\Eloquent\\Model',
        'aliasName' => NULL,
      ),
      'hasNamedScope' => 
      array (
        'name' => 'hasNamedScope',
        'parameters' => 
        array (
          'scope' => 
          array (
            'name' => 'scope',
            'default' => NULL,
            'type' => NULL,
            'isVariadic' => false,
            'byRef' => false,
            'isPromoted' => false,
            'attributes' => 
            array (
            ),
            'startLine' => 1989,
            'endLine' => 1989,
            'startColumn' => 35,
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
 * Determine if the model has a given scope.
 *
 * @param  string  $scope
 * @return bool
 */',
        'startLine' => 1989,
        'endLine' => 1993,
        'startColumn' => 5,
        'endColumn' => 5,
        'couldThrow' => false,
        'isClosure' => false,
        'isGenerator' => false,
        'isVariadic' => false,
        'modifiers' => 1,
        'namespace' => 'Illuminate\\Database\\Eloquent',
        'declaringClassName' => 'Illuminate\\Database\\Eloquent\\Model',
        'implementingClassName' => 'Illuminate\\Database\\Eloquent\\Model',
        'currentClassName' => 'Illuminate\\Database\\Eloquent\\Model',
        'aliasName' => NULL,
      ),
      'callNamedScope' => 
      array (
        'name' => 'callNamedScope',
        'parameters' => 
        array (
          'scope' => 
          array (
            'name' => 'scope',
            'default' => NULL,
            'type' => NULL,
            'isVariadic' => false,
            'byRef' => false,
            'isPromoted' => false,
            'attributes' => 
            array (
            ),
            'startLine' => 2002,
            'endLine' => 2002,
            'startColumn' => 36,
            'endColumn' => 41,
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
                'startLine' => 2002,
                'endLine' => 2002,
                'startTokenPos' => 7749,
                'startFilePos' => 57433,
                'endTokenPos' => 7750,
                'endFilePos' => 57434,
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
            'startLine' => 2002,
            'endLine' => 2002,
            'startColumn' => 44,
            'endColumn' => 65,
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
 * Apply the given named scope if possible.
 *
 * @param  string  $scope
 * @param  array  $parameters
 * @return mixed
 */',
        'startLine' => 2002,
        'endLine' => 2009,
        'startColumn' => 5,
        'endColumn' => 5,
        'couldThrow' => false,
        'isClosure' => false,
        'isGenerator' => false,
        'isVariadic' => false,
        'modifiers' => 1,
        'namespace' => 'Illuminate\\Database\\Eloquent',
        'declaringClassName' => 'Illuminate\\Database\\Eloquent\\Model',
        'implementingClassName' => 'Illuminate\\Database\\Eloquent\\Model',
        'currentClassName' => 'Illuminate\\Database\\Eloquent\\Model',
        'aliasName' => NULL,
      ),
      'isScopeMethodWithAttribute' => 
      array (
        'name' => 'isScopeMethodWithAttribute',
        'parameters' => 
        array (
          'method' => 
          array (
            'name' => 'method',
            'default' => NULL,
            'type' => 
            array (
              'class' => 'PHPStan\\BetterReflection\\Reflection\\ReflectionNamedType',
              'data' => 
              array (
                'name' => 'string',
                'isIdentifier' => true,
              ),
            ),
            'isVariadic' => false,
            'byRef' => false,
            'isPromoted' => false,
            'attributes' => 
            array (
            ),
            'startLine' => 2017,
            'endLine' => 2017,
            'startColumn' => 58,
            'endColumn' => 71,
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
 * Determine if the given method has a scope attribute.
 *
 * @param  string  $method
 * @return bool
 */',
        'startLine' => 2017,
        'endLine' => 2026,
        'startColumn' => 5,
        'endColumn' => 5,
        'couldThrow' => false,
        'isClosure' => false,
        'isGenerator' => false,
        'isVariadic' => false,
        'modifiers' => 18,
        'namespace' => 'Illuminate\\Database\\Eloquent',
        'declaringClassName' => 'Illuminate\\Database\\Eloquent\\Model',
        'implementingClassName' => 'Illuminate\\Database\\Eloquent\\Model',
        'currentClassName' => 'Illuminate\\Database\\Eloquent\\Model',
        'aliasName' => NULL,
      ),
      'toArray' => 
      array (
        'name' => 'toArray',
        'parameters' => 
        array (
        ),
        'returnsReference' => false,
        'returnType' => NULL,
        'attributes' => 
        array (
        ),
        'docComment' => '/**
 * Convert the model instance to an array.
 *
 * @return array
 */',
        'startLine' => 2033,
        'endLine' => 2039,
        'startColumn' => 5,
        'endColumn' => 5,
        'couldThrow' => false,
        'isClosure' => false,
        'isGenerator' => false,
        'isVariadic' => false,
        'modifiers' => 1,
        'namespace' => 'Illuminate\\Database\\Eloquent',
        'declaringClassName' => 'Illuminate\\Database\\Eloquent\\Model',
        'implementingClassName' => 'Illuminate\\Database\\Eloquent\\Model',
        'currentClassName' => 'Illuminate\\Database\\Eloquent\\Model',
        'aliasName' => NULL,
      ),
      'toJson' => 
      array (
        'name' => 'toJson',
        'parameters' => 
        array (
          'options' => 
          array (
            'name' => 'options',
            'default' => 
            array (
              'code' => '0',
              'attributes' => 
              array (
                'startLine' => 2049,
                'endLine' => 2049,
                'startTokenPos' => 7963,
                'startFilePos' => 58684,
                'endTokenPos' => 7963,
                'endFilePos' => 58684,
              ),
            ),
            'type' => NULL,
            'isVariadic' => false,
            'byRef' => false,
            'isPromoted' => false,
            'attributes' => 
            array (
            ),
            'startLine' => 2049,
            'endLine' => 2049,
            'startColumn' => 28,
            'endColumn' => 39,
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
 * Convert the model instance to JSON.
 *
 * @param  int  $options
 * @return string
 *
 * @throws \\Illuminate\\Database\\Eloquent\\JsonEncodingException
 */',
        'startLine' => 2049,
        'endLine' => 2058,
        'startColumn' => 5,
        'endColumn' => 5,
        'couldThrow' => false,
        'isClosure' => false,
        'isGenerator' => false,
        'isVariadic' => false,
        'modifiers' => 1,
        'namespace' => 'Illuminate\\Database\\Eloquent',
        'declaringClassName' => 'Illuminate\\Database\\Eloquent\\Model',
        'implementingClassName' => 'Illuminate\\Database\\Eloquent\\Model',
        'currentClassName' => 'Illuminate\\Database\\Eloquent\\Model',
        'aliasName' => NULL,
      ),
      'toPrettyJson' => 
      array (
        'name' => 'toPrettyJson',
        'parameters' => 
        array (
          'options' => 
          array (
            'name' => 'options',
            'default' => 
            array (
              'code' => '0',
              'attributes' => 
              array (
                'startLine' => 2068,
                'endLine' => 2068,
                'startTokenPos' => 8045,
                'startFilePos' => 59211,
                'endTokenPos' => 8045,
                'endFilePos' => 59211,
              ),
            ),
            'type' => 
            array (
              'class' => 'PHPStan\\BetterReflection\\Reflection\\ReflectionNamedType',
              'data' => 
              array (
                'name' => 'int',
                'isIdentifier' => true,
              ),
            ),
            'isVariadic' => false,
            'byRef' => false,
            'isPromoted' => false,
            'attributes' => 
            array (
            ),
            'startLine' => 2068,
            'endLine' => 2068,
            'startColumn' => 34,
            'endColumn' => 49,
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
 * Convert the model instance to pretty print formatted JSON.
 *
 * @param  int  $options
 * @return string
 *
 * @throws \\Illuminate\\Database\\Eloquent\\JsonEncodingException
 */',
        'startLine' => 2068,
        'endLine' => 2071,
        'startColumn' => 5,
        'endColumn' => 5,
        'couldThrow' => false,
        'isClosure' => false,
        'isGenerator' => false,
        'isVariadic' => false,
        'modifiers' => 1,
        'namespace' => 'Illuminate\\Database\\Eloquent',
        'declaringClassName' => 'Illuminate\\Database\\Eloquent\\Model',
        'implementingClassName' => 'Illuminate\\Database\\Eloquent\\Model',
        'currentClassName' => 'Illuminate\\Database\\Eloquent\\Model',
        'aliasName' => NULL,
      ),
      'jsonSerialize' => 
      array (
        'name' => 'jsonSerialize',
        'parameters' => 
        array (
        ),
        'returnsReference' => false,
        'returnType' => 
        array (
          'class' => 'PHPStan\\BetterReflection\\Reflection\\ReflectionNamedType',
          'data' => 
          array (
            'name' => 'mixed',
            'isIdentifier' => true,
          ),
        ),
        'attributes' => 
        array (
        ),
        'docComment' => '/**
 * Convert the object into something JSON serializable.
 *
 * @return mixed
 */',
        'startLine' => 2078,
        'endLine' => 2081,
        'startColumn' => 5,
        'endColumn' => 5,
        'couldThrow' => false,
        'isClosure' => false,
        'isGenerator' => false,
        'isVariadic' => false,
        'modifiers' => 1,
        'namespace' => 'Illuminate\\Database\\Eloquent',
        'declaringClassName' => 'Illuminate\\Database\\Eloquent\\Model',
        'implementingClassName' => 'Illuminate\\Database\\Eloquent\\Model',
        'currentClassName' => 'Illuminate\\Database\\Eloquent\\Model',
        'aliasName' => NULL,
      ),
      'fresh' => 
      array (
        'name' => 'fresh',
        'parameters' => 
        array (
          'with' => 
          array (
            'name' => 'with',
            'default' => 
            array (
              'code' => '[]',
              'attributes' => 
              array (
                'startLine' => 2089,
                'endLine' => 2089,
                'startTokenPos' => 8104,
                'startFilePos' => 59655,
                'endTokenPos' => 8105,
                'endFilePos' => 59656,
              ),
            ),
            'type' => NULL,
            'isVariadic' => false,
            'byRef' => false,
            'isPromoted' => false,
            'attributes' => 
            array (
            ),
            'startLine' => 2089,
            'endLine' => 2089,
            'startColumn' => 27,
            'endColumn' => 36,
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
 * Reload a fresh model instance from the database.
 *
 * @param  array|string  $with
 * @return static|null
 */',
        'startLine' => 2089,
        'endLine' => 2099,
        'startColumn' => 5,
        'endColumn' => 5,
        'couldThrow' => false,
        'isClosure' => false,
        'isGenerator' => false,
        'isVariadic' => true,
        'modifiers' => 1,
        'namespace' => 'Illuminate\\Database\\Eloquent',
        'declaringClassName' => 'Illuminate\\Database\\Eloquent\\Model',
        'implementingClassName' => 'Illuminate\\Database\\Eloquent\\Model',
        'currentClassName' => 'Illuminate\\Database\\Eloquent\\Model',
        'aliasName' => NULL,
      ),
      'refresh' => 
      array (
        'name' => 'refresh',
        'parameters' => 
        array (
        ),
        'returnsReference' => false,
        'returnType' => NULL,
        'attributes' => 
        array (
        ),
        'docComment' => '/**
 * Reload the current model instance with fresh attributes from the database.
 *
 * @return $this
 */',
        'startLine' => 2106,
        'endLine' => 2127,
        'startColumn' => 5,
        'endColumn' => 5,
        'couldThrow' => false,
        'isClosure' => false,
        'isGenerator' => false,
        'isVariadic' => false,
        'modifiers' => 1,
        'namespace' => 'Illuminate\\Database\\Eloquent',
        'declaringClassName' => 'Illuminate\\Database\\Eloquent\\Model',
        'implementingClassName' => 'Illuminate\\Database\\Eloquent\\Model',
        'currentClassName' => 'Illuminate\\Database\\Eloquent\\Model',
        'aliasName' => NULL,
      ),
      'replicate' => 
      array (
        'name' => 'replicate',
        'parameters' => 
        array (
          'except' => 
          array (
            'name' => 'except',
            'default' => 
            array (
              'code' => 'null',
              'attributes' => 
              array (
                'startLine' => 2135,
                'endLine' => 2135,
                'startTokenPos' => 8332,
                'startFilePos' => 60864,
                'endTokenPos' => 8332,
                'endFilePos' => 60867,
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
                      'name' => 'array',
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
            'isVariadic' => false,
            'byRef' => false,
            'isPromoted' => false,
            'attributes' => 
            array (
            ),
            'startLine' => 2135,
            'endLine' => 2135,
            'startColumn' => 31,
            'endColumn' => 51,
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
 * Clone the model into a new, non-existing instance.
 *
 * @param  array|null  $except
 * @return static
 */',
        'startLine' => 2135,
        'endLine' => 2156,
        'startColumn' => 5,
        'endColumn' => 5,
        'couldThrow' => false,
        'isClosure' => false,
        'isGenerator' => false,
        'isVariadic' => false,
        'modifiers' => 1,
        'namespace' => 'Illuminate\\Database\\Eloquent',
        'declaringClassName' => 'Illuminate\\Database\\Eloquent\\Model',
        'implementingClassName' => 'Illuminate\\Database\\Eloquent\\Model',
        'currentClassName' => 'Illuminate\\Database\\Eloquent\\Model',
        'aliasName' => NULL,
      ),
      'replicateQuietly' => 
      array (
        'name' => 'replicateQuietly',
        'parameters' => 
        array (
          'except' => 
          array (
            'name' => 'except',
            'default' => 
            array (
              'code' => 'null',
              'attributes' => 
              array (
                'startLine' => 2164,
                'endLine' => 2164,
                'startTokenPos' => 8495,
                'startFilePos' => 61756,
                'endTokenPos' => 8495,
                'endFilePos' => 61759,
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
                      'name' => 'array',
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
            'isVariadic' => false,
            'byRef' => false,
            'isPromoted' => false,
            'attributes' => 
            array (
            ),
            'startLine' => 2164,
            'endLine' => 2164,
            'startColumn' => 38,
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
 * Clone the model into a new, non-existing instance without raising any events.
 *
 * @param  array|null  $except
 * @return static
 */',
        'startLine' => 2164,
        'endLine' => 2167,
        'startColumn' => 5,
        'endColumn' => 5,
        'couldThrow' => false,
        'isClosure' => false,
        'isGenerator' => false,
        'isVariadic' => false,
        'modifiers' => 1,
        'namespace' => 'Illuminate\\Database\\Eloquent',
        'declaringClassName' => 'Illuminate\\Database\\Eloquent\\Model',
        'implementingClassName' => 'Illuminate\\Database\\Eloquent\\Model',
        'currentClassName' => 'Illuminate\\Database\\Eloquent\\Model',
        'aliasName' => NULL,
      ),
      'is' => 
      array (
        'name' => 'is',
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
            'startLine' => 2175,
            'endLine' => 2175,
            'startColumn' => 24,
            'endColumn' => 29,
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
 * Determine if two models have the same ID and belong to the same table.
 *
 * @param  \\Illuminate\\Database\\Eloquent\\Model|null  $model
 * @return bool
 */',
        'startLine' => 2175,
        'endLine' => 2181,
        'startColumn' => 5,
        'endColumn' => 5,
        'couldThrow' => false,
        'isClosure' => false,
        'isGenerator' => false,
        'isVariadic' => false,
        'modifiers' => 1,
        'namespace' => 'Illuminate\\Database\\Eloquent',
        'declaringClassName' => 'Illuminate\\Database\\Eloquent\\Model',
        'implementingClassName' => 'Illuminate\\Database\\Eloquent\\Model',
        'currentClassName' => 'Illuminate\\Database\\Eloquent\\Model',
        'aliasName' => NULL,
      ),
      'isNot' => 
      array (
        'name' => 'isNot',
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
            'startLine' => 2189,
            'endLine' => 2189,
            'startColumn' => 27,
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
 * Determine if two models are not the same.
 *
 * @param  \\Illuminate\\Database\\Eloquent\\Model|null  $model
 * @return bool
 */',
        'startLine' => 2189,
        'endLine' => 2192,
        'startColumn' => 5,
        'endColumn' => 5,
        'couldThrow' => false,
        'isClosure' => false,
        'isGenerator' => false,
        'isVariadic' => false,
        'modifiers' => 1,
        'namespace' => 'Illuminate\\Database\\Eloquent',
        'declaringClassName' => 'Illuminate\\Database\\Eloquent\\Model',
        'implementingClassName' => 'Illuminate\\Database\\Eloquent\\Model',
        'currentClassName' => 'Illuminate\\Database\\Eloquent\\Model',
        'aliasName' => NULL,
      ),
      'getConnection' => 
      array (
        'name' => 'getConnection',
        'parameters' => 
        array (
        ),
        'returnsReference' => false,
        'returnType' => NULL,
        'attributes' => 
        array (
        ),
        'docComment' => '/**
 * Get the database connection for the model.
 *
 * @return \\Illuminate\\Database\\Connection
 */',
        'startLine' => 2199,
        'endLine' => 2202,
        'startColumn' => 5,
        'endColumn' => 5,
        'couldThrow' => false,
        'isClosure' => false,
        'isGenerator' => false,
        'isVariadic' => false,
        'modifiers' => 1,
        'namespace' => 'Illuminate\\Database\\Eloquent',
        'declaringClassName' => 'Illuminate\\Database\\Eloquent\\Model',
        'implementingClassName' => 'Illuminate\\Database\\Eloquent\\Model',
        'currentClassName' => 'Illuminate\\Database\\Eloquent\\Model',
        'aliasName' => NULL,
      ),
      'getConnectionName' => 
      array (
        'name' => 'getConnectionName',
        'parameters' => 
        array (
        ),
        'returnsReference' => false,
        'returnType' => NULL,
        'attributes' => 
        array (
        ),
        'docComment' => '/**
 * Get the current connection name for the model.
 *
 * @return string|null
 */',
        'startLine' => 2209,
        'endLine' => 2212,
        'startColumn' => 5,
        'endColumn' => 5,
        'couldThrow' => false,
        'isClosure' => false,
        'isGenerator' => false,
        'isVariadic' => false,
        'modifiers' => 1,
        'namespace' => 'Illuminate\\Database\\Eloquent',
        'declaringClassName' => 'Illuminate\\Database\\Eloquent\\Model',
        'implementingClassName' => 'Illuminate\\Database\\Eloquent\\Model',
        'currentClassName' => 'Illuminate\\Database\\Eloquent\\Model',
        'aliasName' => NULL,
      ),
      'setConnection' => 
      array (
        'name' => 'setConnection',
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
            'startLine' => 2220,
            'endLine' => 2220,
            'startColumn' => 35,
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
 * Set the connection associated with the model.
 *
 * @param  \\UnitEnum|string|null  $name
 * @return $this
 */',
        'startLine' => 2220,
        'endLine' => 2225,
        'startColumn' => 5,
        'endColumn' => 5,
        'couldThrow' => false,
        'isClosure' => false,
        'isGenerator' => false,
        'isVariadic' => false,
        'modifiers' => 1,
        'namespace' => 'Illuminate\\Database\\Eloquent',
        'declaringClassName' => 'Illuminate\\Database\\Eloquent\\Model',
        'implementingClassName' => 'Illuminate\\Database\\Eloquent\\Model',
        'currentClassName' => 'Illuminate\\Database\\Eloquent\\Model',
        'aliasName' => NULL,
      ),
      'resolveConnection' => 
      array (
        'name' => 'resolveConnection',
        'parameters' => 
        array (
          'connection' => 
          array (
            'name' => 'connection',
            'default' => 
            array (
              'code' => 'null',
              'attributes' => 
              array (
                'startLine' => 2233,
                'endLine' => 2233,
                'startTokenPos' => 8719,
                'startFilePos' => 63445,
                'endTokenPos' => 8719,
                'endFilePos' => 63448,
              ),
            ),
            'type' => NULL,
            'isVariadic' => false,
            'byRef' => false,
            'isPromoted' => false,
            'attributes' => 
            array (
            ),
            'startLine' => 2233,
            'endLine' => 2233,
            'startColumn' => 46,
            'endColumn' => 63,
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
 * Resolve a connection instance.
 *
 * @param  \\UnitEnum|string|null  $connection
 * @return \\Illuminate\\Database\\Connection
 */',
        'startLine' => 2233,
        'endLine' => 2236,
        'startColumn' => 5,
        'endColumn' => 5,
        'couldThrow' => false,
        'isClosure' => false,
        'isGenerator' => false,
        'isVariadic' => false,
        'modifiers' => 17,
        'namespace' => 'Illuminate\\Database\\Eloquent',
        'declaringClassName' => 'Illuminate\\Database\\Eloquent\\Model',
        'implementingClassName' => 'Illuminate\\Database\\Eloquent\\Model',
        'currentClassName' => 'Illuminate\\Database\\Eloquent\\Model',
        'aliasName' => NULL,
      ),
      'getConnectionResolver' => 
      array (
        'name' => 'getConnectionResolver',
        'parameters' => 
        array (
        ),
        'returnsReference' => false,
        'returnType' => NULL,
        'attributes' => 
        array (
        ),
        'docComment' => '/**
 * Get the connection resolver instance.
 *
 * @return \\Illuminate\\Database\\ConnectionResolverInterface|null
 */',
        'startLine' => 2243,
        'endLine' => 2246,
        'startColumn' => 5,
        'endColumn' => 5,
        'couldThrow' => false,
        'isClosure' => false,
        'isGenerator' => false,
        'isVariadic' => false,
        'modifiers' => 17,
        'namespace' => 'Illuminate\\Database\\Eloquent',
        'declaringClassName' => 'Illuminate\\Database\\Eloquent\\Model',
        'implementingClassName' => 'Illuminate\\Database\\Eloquent\\Model',
        'currentClassName' => 'Illuminate\\Database\\Eloquent\\Model',
        'aliasName' => NULL,
      ),
      'setConnectionResolver' => 
      array (
        'name' => 'setConnectionResolver',
        'parameters' => 
        array (
          'resolver' => 
          array (
            'name' => 'resolver',
            'default' => NULL,
            'type' => 
            array (
              'class' => 'PHPStan\\BetterReflection\\Reflection\\ReflectionNamedType',
              'data' => 
              array (
                'name' => 'Illuminate\\Database\\ConnectionResolverInterface',
                'isIdentifier' => false,
              ),
            ),
            'isVariadic' => false,
            'byRef' => false,
            'isPromoted' => false,
            'attributes' => 
            array (
            ),
            'startLine' => 2254,
            'endLine' => 2254,
            'startColumn' => 50,
            'endColumn' => 67,
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
 * Set the connection resolver instance.
 *
 * @param  \\Illuminate\\Database\\ConnectionResolverInterface  $resolver
 * @return void
 */',
        'startLine' => 2254,
        'endLine' => 2257,
        'startColumn' => 5,
        'endColumn' => 5,
        'couldThrow' => false,
        'isClosure' => false,
        'isGenerator' => false,
        'isVariadic' => false,
        'modifiers' => 17,
        'namespace' => 'Illuminate\\Database\\Eloquent',
        'declaringClassName' => 'Illuminate\\Database\\Eloquent\\Model',
        'implementingClassName' => 'Illuminate\\Database\\Eloquent\\Model',
        'currentClassName' => 'Illuminate\\Database\\Eloquent\\Model',
        'aliasName' => NULL,
      ),
      'unsetConnectionResolver' => 
      array (
        'name' => 'unsetConnectionResolver',
        'parameters' => 
        array (
        ),
        'returnsReference' => false,
        'returnType' => NULL,
        'attributes' => 
        array (
        ),
        'docComment' => '/**
 * Unset the connection resolver for models.
 *
 * @return void
 */',
        'startLine' => 2264,
        'endLine' => 2267,
        'startColumn' => 5,
        'endColumn' => 5,
        'couldThrow' => false,
        'isClosure' => false,
        'isGenerator' => false,
        'isVariadic' => false,
        'modifiers' => 17,
        'namespace' => 'Illuminate\\Database\\Eloquent',
        'declaringClassName' => 'Illuminate\\Database\\Eloquent\\Model',
        'implementingClassName' => 'Illuminate\\Database\\Eloquent\\Model',
        'currentClassName' => 'Illuminate\\Database\\Eloquent\\Model',
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
 * Get the table associated with the model.
 *
 * @return string
 */',
        'startLine' => 2274,
        'endLine' => 2277,
        'startColumn' => 5,
        'endColumn' => 5,
        'couldThrow' => false,
        'isClosure' => false,
        'isGenerator' => false,
        'isVariadic' => false,
        'modifiers' => 1,
        'namespace' => 'Illuminate\\Database\\Eloquent',
        'declaringClassName' => 'Illuminate\\Database\\Eloquent\\Model',
        'implementingClassName' => 'Illuminate\\Database\\Eloquent\\Model',
        'currentClassName' => 'Illuminate\\Database\\Eloquent\\Model',
        'aliasName' => NULL,
      ),
      'setTable' => 
      array (
        'name' => 'setTable',
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
            'startLine' => 2285,
            'endLine' => 2285,
            'startColumn' => 30,
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
 * Set the table associated with the model.
 *
 * @param  string  $table
 * @return $this
 */',
        'startLine' => 2285,
        'endLine' => 2290,
        'startColumn' => 5,
        'endColumn' => 5,
        'couldThrow' => false,
        'isClosure' => false,
        'isGenerator' => false,
        'isVariadic' => false,
        'modifiers' => 1,
        'namespace' => 'Illuminate\\Database\\Eloquent',
        'declaringClassName' => 'Illuminate\\Database\\Eloquent\\Model',
        'implementingClassName' => 'Illuminate\\Database\\Eloquent\\Model',
        'currentClassName' => 'Illuminate\\Database\\Eloquent\\Model',
        'aliasName' => NULL,
      ),
      'getKeyName' => 
      array (
        'name' => 'getKeyName',
        'parameters' => 
        array (
        ),
        'returnsReference' => false,
        'returnType' => NULL,
        'attributes' => 
        array (
        ),
        'docComment' => '/**
 * Get the primary key for the model.
 *
 * @return string
 */',
        'startLine' => 2297,
        'endLine' => 2300,
        'startColumn' => 5,
        'endColumn' => 5,
        'couldThrow' => false,
        'isClosure' => false,
        'isGenerator' => false,
        'isVariadic' => false,
        'modifiers' => 1,
        'namespace' => 'Illuminate\\Database\\Eloquent',
        'declaringClassName' => 'Illuminate\\Database\\Eloquent\\Model',
        'implementingClassName' => 'Illuminate\\Database\\Eloquent\\Model',
        'currentClassName' => 'Illuminate\\Database\\Eloquent\\Model',
        'aliasName' => NULL,
      ),
      'setKeyName' => 
      array (
        'name' => 'setKeyName',
        'parameters' => 
        array (
          'key' => 
          array (
            'name' => 'key',
            'default' => NULL,
            'type' => NULL,
            'isVariadic' => false,
            'byRef' => false,
            'isPromoted' => false,
            'attributes' => 
            array (
            ),
            'startLine' => 2308,
            'endLine' => 2308,
            'startColumn' => 32,
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
 * Set the primary key for the model.
 *
 * @param  string  $key
 * @return $this
 */',
        'startLine' => 2308,
        'endLine' => 2313,
        'startColumn' => 5,
        'endColumn' => 5,
        'couldThrow' => false,
        'isClosure' => false,
        'isGenerator' => false,
        'isVariadic' => false,
        'modifiers' => 1,
        'namespace' => 'Illuminate\\Database\\Eloquent',
        'declaringClassName' => 'Illuminate\\Database\\Eloquent\\Model',
        'implementingClassName' => 'Illuminate\\Database\\Eloquent\\Model',
        'currentClassName' => 'Illuminate\\Database\\Eloquent\\Model',
        'aliasName' => NULL,
      ),
      'getQualifiedKeyName' => 
      array (
        'name' => 'getQualifiedKeyName',
        'parameters' => 
        array (
        ),
        'returnsReference' => false,
        'returnType' => NULL,
        'attributes' => 
        array (
        ),
        'docComment' => '/**
 * Get the table qualified key name.
 *
 * @return string
 */',
        'startLine' => 2320,
        'endLine' => 2323,
        'startColumn' => 5,
        'endColumn' => 5,
        'couldThrow' => false,
        'isClosure' => false,
        'isGenerator' => false,
        'isVariadic' => false,
        'modifiers' => 1,
        'namespace' => 'Illuminate\\Database\\Eloquent',
        'declaringClassName' => 'Illuminate\\Database\\Eloquent\\Model',
        'implementingClassName' => 'Illuminate\\Database\\Eloquent\\Model',
        'currentClassName' => 'Illuminate\\Database\\Eloquent\\Model',
        'aliasName' => NULL,
      ),
      'getKeyType' => 
      array (
        'name' => 'getKeyType',
        'parameters' => 
        array (
        ),
        'returnsReference' => false,
        'returnType' => NULL,
        'attributes' => 
        array (
        ),
        'docComment' => '/**
 * Get the auto-incrementing key type.
 *
 * @return string
 */',
        'startLine' => 2330,
        'endLine' => 2333,
        'startColumn' => 5,
        'endColumn' => 5,
        'couldThrow' => false,
        'isClosure' => false,
        'isGenerator' => false,
        'isVariadic' => false,
        'modifiers' => 1,
        'namespace' => 'Illuminate\\Database\\Eloquent',
        'declaringClassName' => 'Illuminate\\Database\\Eloquent\\Model',
        'implementingClassName' => 'Illuminate\\Database\\Eloquent\\Model',
        'currentClassName' => 'Illuminate\\Database\\Eloquent\\Model',
        'aliasName' => NULL,
      ),
      'setKeyType' => 
      array (
        'name' => 'setKeyType',
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
            'startLine' => 2341,
            'endLine' => 2341,
            'startColumn' => 32,
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
 * Set the data type for the primary key.
 *
 * @param  string  $type
 * @return $this
 */',
        'startLine' => 2341,
        'endLine' => 2346,
        'startColumn' => 5,
        'endColumn' => 5,
        'couldThrow' => false,
        'isClosure' => false,
        'isGenerator' => false,
        'isVariadic' => false,
        'modifiers' => 1,
        'namespace' => 'Illuminate\\Database\\Eloquent',
        'declaringClassName' => 'Illuminate\\Database\\Eloquent\\Model',
        'implementingClassName' => 'Illuminate\\Database\\Eloquent\\Model',
        'currentClassName' => 'Illuminate\\Database\\Eloquent\\Model',
        'aliasName' => NULL,
      ),
      'getIncrementing' => 
      array (
        'name' => 'getIncrementing',
        'parameters' => 
        array (
        ),
        'returnsReference' => false,
        'returnType' => NULL,
        'attributes' => 
        array (
        ),
        'docComment' => '/**
 * Get the value indicating whether the IDs are incrementing.
 *
 * @return bool
 */',
        'startLine' => 2353,
        'endLine' => 2356,
        'startColumn' => 5,
        'endColumn' => 5,
        'couldThrow' => false,
        'isClosure' => false,
        'isGenerator' => false,
        'isVariadic' => false,
        'modifiers' => 1,
        'namespace' => 'Illuminate\\Database\\Eloquent',
        'declaringClassName' => 'Illuminate\\Database\\Eloquent\\Model',
        'implementingClassName' => 'Illuminate\\Database\\Eloquent\\Model',
        'currentClassName' => 'Illuminate\\Database\\Eloquent\\Model',
        'aliasName' => NULL,
      ),
      'setIncrementing' => 
      array (
        'name' => 'setIncrementing',
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
            'startLine' => 2364,
            'endLine' => 2364,
            'startColumn' => 37,
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
 * Set whether IDs are incrementing.
 *
 * @param  bool  $value
 * @return $this
 */',
        'startLine' => 2364,
        'endLine' => 2369,
        'startColumn' => 5,
        'endColumn' => 5,
        'couldThrow' => false,
        'isClosure' => false,
        'isGenerator' => false,
        'isVariadic' => false,
        'modifiers' => 1,
        'namespace' => 'Illuminate\\Database\\Eloquent',
        'declaringClassName' => 'Illuminate\\Database\\Eloquent\\Model',
        'implementingClassName' => 'Illuminate\\Database\\Eloquent\\Model',
        'currentClassName' => 'Illuminate\\Database\\Eloquent\\Model',
        'aliasName' => NULL,
      ),
      'getKey' => 
      array (
        'name' => 'getKey',
        'parameters' => 
        array (
        ),
        'returnsReference' => false,
        'returnType' => NULL,
        'attributes' => 
        array (
        ),
        'docComment' => '/**
 * Get the value of the model\'s primary key.
 *
 * @return mixed
 */',
        'startLine' => 2376,
        'endLine' => 2379,
        'startColumn' => 5,
        'endColumn' => 5,
        'couldThrow' => false,
        'isClosure' => false,
        'isGenerator' => false,
        'isVariadic' => false,
        'modifiers' => 1,
        'namespace' => 'Illuminate\\Database\\Eloquent',
        'declaringClassName' => 'Illuminate\\Database\\Eloquent\\Model',
        'implementingClassName' => 'Illuminate\\Database\\Eloquent\\Model',
        'currentClassName' => 'Illuminate\\Database\\Eloquent\\Model',
        'aliasName' => NULL,
      ),
      'getQueueableId' => 
      array (
        'name' => 'getQueueableId',
        'parameters' => 
        array (
        ),
        'returnsReference' => false,
        'returnType' => NULL,
        'attributes' => 
        array (
        ),
        'docComment' => '/**
 * Get the queueable identity for the entity.
 *
 * @return mixed
 */',
        'startLine' => 2386,
        'endLine' => 2389,
        'startColumn' => 5,
        'endColumn' => 5,
        'couldThrow' => false,
        'isClosure' => false,
        'isGenerator' => false,
        'isVariadic' => false,
        'modifiers' => 1,
        'namespace' => 'Illuminate\\Database\\Eloquent',
        'declaringClassName' => 'Illuminate\\Database\\Eloquent\\Model',
        'implementingClassName' => 'Illuminate\\Database\\Eloquent\\Model',
        'currentClassName' => 'Illuminate\\Database\\Eloquent\\Model',
        'aliasName' => NULL,
      ),
      'getQueueableRelations' => 
      array (
        'name' => 'getQueueableRelations',
        'parameters' => 
        array (
        ),
        'returnsReference' => false,
        'returnType' => NULL,
        'attributes' => 
        array (
        ),
        'docComment' => '/**
 * Get the queueable relationships for the entity.
 *
 * @return array
 */',
        'startLine' => 2396,
        'endLine' => 2423,
        'startColumn' => 5,
        'endColumn' => 5,
        'couldThrow' => false,
        'isClosure' => false,
        'isGenerator' => false,
        'isVariadic' => false,
        'modifiers' => 1,
        'namespace' => 'Illuminate\\Database\\Eloquent',
        'declaringClassName' => 'Illuminate\\Database\\Eloquent\\Model',
        'implementingClassName' => 'Illuminate\\Database\\Eloquent\\Model',
        'currentClassName' => 'Illuminate\\Database\\Eloquent\\Model',
        'aliasName' => NULL,
      ),
      'getQueueableConnection' => 
      array (
        'name' => 'getQueueableConnection',
        'parameters' => 
        array (
        ),
        'returnsReference' => false,
        'returnType' => NULL,
        'attributes' => 
        array (
        ),
        'docComment' => '/**
 * Get the queueable connection for the entity.
 *
 * @return string|null
 */',
        'startLine' => 2430,
        'endLine' => 2433,
        'startColumn' => 5,
        'endColumn' => 5,
        'couldThrow' => false,
        'isClosure' => false,
        'isGenerator' => false,
        'isVariadic' => false,
        'modifiers' => 1,
        'namespace' => 'Illuminate\\Database\\Eloquent',
        'declaringClassName' => 'Illuminate\\Database\\Eloquent\\Model',
        'implementingClassName' => 'Illuminate\\Database\\Eloquent\\Model',
        'currentClassName' => 'Illuminate\\Database\\Eloquent\\Model',
        'aliasName' => NULL,
      ),
      'getRouteKey' => 
      array (
        'name' => 'getRouteKey',
        'parameters' => 
        array (
        ),
        'returnsReference' => false,
        'returnType' => NULL,
        'attributes' => 
        array (
        ),
        'docComment' => '/**
 * Get the value of the model\'s route key.
 *
 * @return mixed
 */',
        'startLine' => 2440,
        'endLine' => 2443,
        'startColumn' => 5,
        'endColumn' => 5,
        'couldThrow' => false,
        'isClosure' => false,
        'isGenerator' => false,
        'isVariadic' => false,
        'modifiers' => 1,
        'namespace' => 'Illuminate\\Database\\Eloquent',
        'declaringClassName' => 'Illuminate\\Database\\Eloquent\\Model',
        'implementingClassName' => 'Illuminate\\Database\\Eloquent\\Model',
        'currentClassName' => 'Illuminate\\Database\\Eloquent\\Model',
        'aliasName' => NULL,
      ),
      'getRouteKeyName' => 
      array (
        'name' => 'getRouteKeyName',
        'parameters' => 
        array (
        ),
        'returnsReference' => false,
        'returnType' => NULL,
        'attributes' => 
        array (
        ),
        'docComment' => '/**
 * Get the route key for the model.
 *
 * @return string
 */',
        'startLine' => 2450,
        'endLine' => 2453,
        'startColumn' => 5,
        'endColumn' => 5,
        'couldThrow' => false,
        'isClosure' => false,
        'isGenerator' => false,
        'isVariadic' => false,
        'modifiers' => 1,
        'namespace' => 'Illuminate\\Database\\Eloquent',
        'declaringClassName' => 'Illuminate\\Database\\Eloquent\\Model',
        'implementingClassName' => 'Illuminate\\Database\\Eloquent\\Model',
        'currentClassName' => 'Illuminate\\Database\\Eloquent\\Model',
        'aliasName' => NULL,
      ),
      'resolveRouteBinding' => 
      array (
        'name' => 'resolveRouteBinding',
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
            'startLine' => 2462,
            'endLine' => 2462,
            'startColumn' => 41,
            'endColumn' => 46,
            'parameterIndex' => 0,
            'isOptional' => false,
          ),
          'field' => 
          array (
            'name' => 'field',
            'default' => 
            array (
              'code' => 'null',
              'attributes' => 
              array (
                'startLine' => 2462,
                'endLine' => 2462,
                'startTokenPos' => 9406,
                'startFilePos' => 68355,
                'endTokenPos' => 9406,
                'endFilePos' => 68358,
              ),
            ),
            'type' => NULL,
            'isVariadic' => false,
            'byRef' => false,
            'isPromoted' => false,
            'attributes' => 
            array (
            ),
            'startLine' => 2462,
            'endLine' => 2462,
            'startColumn' => 49,
            'endColumn' => 61,
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
 * Retrieve the model for a bound value.
 *
 * @param  mixed  $value
 * @param  string|null  $field
 * @return \\Illuminate\\Database\\Eloquent\\Model|null
 */',
        'startLine' => 2462,
        'endLine' => 2465,
        'startColumn' => 5,
        'endColumn' => 5,
        'couldThrow' => false,
        'isClosure' => false,
        'isGenerator' => false,
        'isVariadic' => false,
        'modifiers' => 1,
        'namespace' => 'Illuminate\\Database\\Eloquent',
        'declaringClassName' => 'Illuminate\\Database\\Eloquent\\Model',
        'implementingClassName' => 'Illuminate\\Database\\Eloquent\\Model',
        'currentClassName' => 'Illuminate\\Database\\Eloquent\\Model',
        'aliasName' => NULL,
      ),
      'resolveSoftDeletableRouteBinding' => 
      array (
        'name' => 'resolveSoftDeletableRouteBinding',
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
            'startLine' => 2474,
            'endLine' => 2474,
            'startColumn' => 54,
            'endColumn' => 59,
            'parameterIndex' => 0,
            'isOptional' => false,
          ),
          'field' => 
          array (
            'name' => 'field',
            'default' => 
            array (
              'code' => 'null',
              'attributes' => 
              array (
                'startLine' => 2474,
                'endLine' => 2474,
                'startTokenPos' => 9448,
                'startFilePos' => 68712,
                'endTokenPos' => 9448,
                'endFilePos' => 68715,
              ),
            ),
            'type' => NULL,
            'isVariadic' => false,
            'byRef' => false,
            'isPromoted' => false,
            'attributes' => 
            array (
            ),
            'startLine' => 2474,
            'endLine' => 2474,
            'startColumn' => 62,
            'endColumn' => 74,
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
 * Retrieve the model for a bound value.
 *
 * @param  mixed  $value
 * @param  string|null  $field
 * @return \\Illuminate\\Database\\Eloquent\\Model|null
 */',
        'startLine' => 2474,
        'endLine' => 2477,
        'startColumn' => 5,
        'endColumn' => 5,
        'couldThrow' => false,
        'isClosure' => false,
        'isGenerator' => false,
        'isVariadic' => false,
        'modifiers' => 1,
        'namespace' => 'Illuminate\\Database\\Eloquent',
        'declaringClassName' => 'Illuminate\\Database\\Eloquent\\Model',
        'implementingClassName' => 'Illuminate\\Database\\Eloquent\\Model',
        'currentClassName' => 'Illuminate\\Database\\Eloquent\\Model',
        'aliasName' => NULL,
      ),
      'resolveChildRouteBinding' => 
      array (
        'name' => 'resolveChildRouteBinding',
        'parameters' => 
        array (
          'childType' => 
          array (
            'name' => 'childType',
            'default' => NULL,
            'type' => NULL,
            'isVariadic' => false,
            'byRef' => false,
            'isPromoted' => false,
            'attributes' => 
            array (
            ),
            'startLine' => 2487,
            'endLine' => 2487,
            'startColumn' => 46,
            'endColumn' => 55,
            'parameterIndex' => 0,
            'isOptional' => false,
          ),
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
            'startLine' => 2487,
            'endLine' => 2487,
            'startColumn' => 58,
            'endColumn' => 63,
            'parameterIndex' => 1,
            'isOptional' => false,
          ),
          'field' => 
          array (
            'name' => 'field',
            'default' => NULL,
            'type' => NULL,
            'isVariadic' => false,
            'byRef' => false,
            'isPromoted' => false,
            'attributes' => 
            array (
            ),
            'startLine' => 2487,
            'endLine' => 2487,
            'startColumn' => 66,
            'endColumn' => 71,
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
 * Retrieve the child model for a bound value.
 *
 * @param  string  $childType
 * @param  mixed  $value
 * @param  string|null  $field
 * @return \\Illuminate\\Database\\Eloquent\\Model|null
 */',
        'startLine' => 2487,
        'endLine' => 2490,
        'startColumn' => 5,
        'endColumn' => 5,
        'couldThrow' => false,
        'isClosure' => false,
        'isGenerator' => false,
        'isVariadic' => false,
        'modifiers' => 1,
        'namespace' => 'Illuminate\\Database\\Eloquent',
        'declaringClassName' => 'Illuminate\\Database\\Eloquent\\Model',
        'implementingClassName' => 'Illuminate\\Database\\Eloquent\\Model',
        'currentClassName' => 'Illuminate\\Database\\Eloquent\\Model',
        'aliasName' => NULL,
      ),
      'resolveSoftDeletableChildRouteBinding' => 
      array (
        'name' => 'resolveSoftDeletableChildRouteBinding',
        'parameters' => 
        array (
          'childType' => 
          array (
            'name' => 'childType',
            'default' => NULL,
            'type' => NULL,
            'isVariadic' => false,
            'byRef' => false,
            'isPromoted' => false,
            'attributes' => 
            array (
            ),
            'startLine' => 2500,
            'endLine' => 2500,
            'startColumn' => 59,
            'endColumn' => 68,
            'parameterIndex' => 0,
            'isOptional' => false,
          ),
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
            'startLine' => 2500,
            'endLine' => 2500,
            'startColumn' => 71,
            'endColumn' => 76,
            'parameterIndex' => 1,
            'isOptional' => false,
          ),
          'field' => 
          array (
            'name' => 'field',
            'default' => NULL,
            'type' => NULL,
            'isVariadic' => false,
            'byRef' => false,
            'isPromoted' => false,
            'attributes' => 
            array (
            ),
            'startLine' => 2500,
            'endLine' => 2500,
            'startColumn' => 79,
            'endColumn' => 84,
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
 * Retrieve the child model for a bound value.
 *
 * @param  string  $childType
 * @param  mixed  $value
 * @param  string|null  $field
 * @return \\Illuminate\\Database\\Eloquent\\Model|null
 */',
        'startLine' => 2500,
        'endLine' => 2503,
        'startColumn' => 5,
        'endColumn' => 5,
        'couldThrow' => false,
        'isClosure' => false,
        'isGenerator' => false,
        'isVariadic' => false,
        'modifiers' => 1,
        'namespace' => 'Illuminate\\Database\\Eloquent',
        'declaringClassName' => 'Illuminate\\Database\\Eloquent\\Model',
        'implementingClassName' => 'Illuminate\\Database\\Eloquent\\Model',
        'currentClassName' => 'Illuminate\\Database\\Eloquent\\Model',
        'aliasName' => NULL,
      ),
      'resolveChildRouteBindingQuery' => 
      array (
        'name' => 'resolveChildRouteBindingQuery',
        'parameters' => 
        array (
          'childType' => 
          array (
            'name' => 'childType',
            'default' => NULL,
            'type' => NULL,
            'isVariadic' => false,
            'byRef' => false,
            'isPromoted' => false,
            'attributes' => 
            array (
            ),
            'startLine' => 2513,
            'endLine' => 2513,
            'startColumn' => 54,
            'endColumn' => 63,
            'parameterIndex' => 0,
            'isOptional' => false,
          ),
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
            'startLine' => 2513,
            'endLine' => 2513,
            'startColumn' => 66,
            'endColumn' => 71,
            'parameterIndex' => 1,
            'isOptional' => false,
          ),
          'field' => 
          array (
            'name' => 'field',
            'default' => NULL,
            'type' => NULL,
            'isVariadic' => false,
            'byRef' => false,
            'isPromoted' => false,
            'attributes' => 
            array (
            ),
            'startLine' => 2513,
            'endLine' => 2513,
            'startColumn' => 74,
            'endColumn' => 79,
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
 * Retrieve the child model query for a bound value.
 *
 * @param  string  $childType
 * @param  mixed  $value
 * @param  string|null  $field
 * @return \\Illuminate\\Database\\Eloquent\\Relations\\Relation<\\Illuminate\\Database\\Eloquent\\Model, $this, *>
 */',
        'startLine' => 2513,
        'endLine' => 2527,
        'startColumn' => 5,
        'endColumn' => 5,
        'couldThrow' => false,
        'isClosure' => false,
        'isGenerator' => false,
        'isVariadic' => false,
        'modifiers' => 2,
        'namespace' => 'Illuminate\\Database\\Eloquent',
        'declaringClassName' => 'Illuminate\\Database\\Eloquent\\Model',
        'implementingClassName' => 'Illuminate\\Database\\Eloquent\\Model',
        'currentClassName' => 'Illuminate\\Database\\Eloquent\\Model',
        'aliasName' => NULL,
      ),
      'childRouteBindingRelationshipName' => 
      array (
        'name' => 'childRouteBindingRelationshipName',
        'parameters' => 
        array (
          'childType' => 
          array (
            'name' => 'childType',
            'default' => NULL,
            'type' => NULL,
            'isVariadic' => false,
            'byRef' => false,
            'isPromoted' => false,
            'attributes' => 
            array (
            ),
            'startLine' => 2535,
            'endLine' => 2535,
            'startColumn' => 58,
            'endColumn' => 67,
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
 * Retrieve the child route model binding relationship name for the given child type.
 *
 * @param  string  $childType
 * @return string
 */',
        'startLine' => 2535,
        'endLine' => 2538,
        'startColumn' => 5,
        'endColumn' => 5,
        'couldThrow' => false,
        'isClosure' => false,
        'isGenerator' => false,
        'isVariadic' => false,
        'modifiers' => 2,
        'namespace' => 'Illuminate\\Database\\Eloquent',
        'declaringClassName' => 'Illuminate\\Database\\Eloquent\\Model',
        'implementingClassName' => 'Illuminate\\Database\\Eloquent\\Model',
        'currentClassName' => 'Illuminate\\Database\\Eloquent\\Model',
        'aliasName' => NULL,
      ),
      'resolveRouteBindingQuery' => 
      array (
        'name' => 'resolveRouteBindingQuery',
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
            'startLine' => 2548,
            'endLine' => 2548,
            'startColumn' => 46,
            'endColumn' => 51,
            'parameterIndex' => 0,
            'isOptional' => false,
          ),
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
            'startLine' => 2548,
            'endLine' => 2548,
            'startColumn' => 54,
            'endColumn' => 59,
            'parameterIndex' => 1,
            'isOptional' => false,
          ),
          'field' => 
          array (
            'name' => 'field',
            'default' => 
            array (
              'code' => 'null',
              'attributes' => 
              array (
                'startLine' => 2548,
                'endLine' => 2548,
                'startTokenPos' => 9753,
                'startFilePos' => 71359,
                'endTokenPos' => 9753,
                'endFilePos' => 71362,
              ),
            ),
            'type' => NULL,
            'isVariadic' => false,
            'byRef' => false,
            'isPromoted' => false,
            'attributes' => 
            array (
            ),
            'startLine' => 2548,
            'endLine' => 2548,
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
 * Retrieve the model for a bound value.
 *
 * @param  \\Illuminate\\Database\\Eloquent\\Model|\\Illuminate\\Contracts\\Database\\Eloquent\\Builder|\\Illuminate\\Database\\Eloquent\\Relations\\Relation  $query
 * @param  mixed  $value
 * @param  string|null  $field
 * @return \\Illuminate\\Contracts\\Database\\Eloquent\\Builder
 */',
        'startLine' => 2548,
        'endLine' => 2551,
        'startColumn' => 5,
        'endColumn' => 5,
        'couldThrow' => false,
        'isClosure' => false,
        'isGenerator' => false,
        'isVariadic' => false,
        'modifiers' => 1,
        'namespace' => 'Illuminate\\Database\\Eloquent',
        'declaringClassName' => 'Illuminate\\Database\\Eloquent\\Model',
        'implementingClassName' => 'Illuminate\\Database\\Eloquent\\Model',
        'currentClassName' => 'Illuminate\\Database\\Eloquent\\Model',
        'aliasName' => NULL,
      ),
      'getForeignKey' => 
      array (
        'name' => 'getForeignKey',
        'parameters' => 
        array (
        ),
        'returnsReference' => false,
        'returnType' => NULL,
        'attributes' => 
        array (
        ),
        'docComment' => '/**
 * Get the default foreign key name for the model.
 *
 * @return string
 */',
        'startLine' => 2558,
        'endLine' => 2561,
        'startColumn' => 5,
        'endColumn' => 5,
        'couldThrow' => false,
        'isClosure' => false,
        'isGenerator' => false,
        'isVariadic' => false,
        'modifiers' => 1,
        'namespace' => 'Illuminate\\Database\\Eloquent',
        'declaringClassName' => 'Illuminate\\Database\\Eloquent\\Model',
        'implementingClassName' => 'Illuminate\\Database\\Eloquent\\Model',
        'currentClassName' => 'Illuminate\\Database\\Eloquent\\Model',
        'aliasName' => NULL,
      ),
      'getPerPage' => 
      array (
        'name' => 'getPerPage',
        'parameters' => 
        array (
        ),
        'returnsReference' => false,
        'returnType' => NULL,
        'attributes' => 
        array (
        ),
        'docComment' => '/**
 * Get the number of models to return per page.
 *
 * @return int
 */',
        'startLine' => 2568,
        'endLine' => 2571,
        'startColumn' => 5,
        'endColumn' => 5,
        'couldThrow' => false,
        'isClosure' => false,
        'isGenerator' => false,
        'isVariadic' => false,
        'modifiers' => 1,
        'namespace' => 'Illuminate\\Database\\Eloquent',
        'declaringClassName' => 'Illuminate\\Database\\Eloquent\\Model',
        'implementingClassName' => 'Illuminate\\Database\\Eloquent\\Model',
        'currentClassName' => 'Illuminate\\Database\\Eloquent\\Model',
        'aliasName' => NULL,
      ),
      'setPerPage' => 
      array (
        'name' => 'setPerPage',
        'parameters' => 
        array (
          'perPage' => 
          array (
            'name' => 'perPage',
            'default' => NULL,
            'type' => NULL,
            'isVariadic' => false,
            'byRef' => false,
            'isPromoted' => false,
            'attributes' => 
            array (
            ),
            'startLine' => 2579,
            'endLine' => 2579,
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
 * Set the number of models to return per page.
 *
 * @param  int  $perPage
 * @return $this
 */',
        'startLine' => 2579,
        'endLine' => 2584,
        'startColumn' => 5,
        'endColumn' => 5,
        'couldThrow' => false,
        'isClosure' => false,
        'isGenerator' => false,
        'isVariadic' => false,
        'modifiers' => 1,
        'namespace' => 'Illuminate\\Database\\Eloquent',
        'declaringClassName' => 'Illuminate\\Database\\Eloquent\\Model',
        'implementingClassName' => 'Illuminate\\Database\\Eloquent\\Model',
        'currentClassName' => 'Illuminate\\Database\\Eloquent\\Model',
        'aliasName' => NULL,
      ),
      'isSoftDeletable' => 
      array (
        'name' => 'isSoftDeletable',
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
 * Determine if the model is soft deletable.
 */',
        'startLine' => 2589,
        'endLine' => 2592,
        'startColumn' => 5,
        'endColumn' => 5,
        'couldThrow' => false,
        'isClosure' => false,
        'isGenerator' => false,
        'isVariadic' => false,
        'modifiers' => 17,
        'namespace' => 'Illuminate\\Database\\Eloquent',
        'declaringClassName' => 'Illuminate\\Database\\Eloquent\\Model',
        'implementingClassName' => 'Illuminate\\Database\\Eloquent\\Model',
        'currentClassName' => 'Illuminate\\Database\\Eloquent\\Model',
        'aliasName' => NULL,
      ),
      'isPrunable' => 
      array (
        'name' => 'isPrunable',
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
 * Determine if the model is prunable.
 */',
        'startLine' => 2597,
        'endLine' => 2600,
        'startColumn' => 5,
        'endColumn' => 5,
        'couldThrow' => false,
        'isClosure' => false,
        'isGenerator' => false,
        'isVariadic' => false,
        'modifiers' => 2,
        'namespace' => 'Illuminate\\Database\\Eloquent',
        'declaringClassName' => 'Illuminate\\Database\\Eloquent\\Model',
        'implementingClassName' => 'Illuminate\\Database\\Eloquent\\Model',
        'currentClassName' => 'Illuminate\\Database\\Eloquent\\Model',
        'aliasName' => NULL,
      ),
      'isMassPrunable' => 
      array (
        'name' => 'isMassPrunable',
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
 * Determine if the model is mass prunable.
 */',
        'startLine' => 2605,
        'endLine' => 2608,
        'startColumn' => 5,
        'endColumn' => 5,
        'couldThrow' => false,
        'isClosure' => false,
        'isGenerator' => false,
        'isVariadic' => false,
        'modifiers' => 2,
        'namespace' => 'Illuminate\\Database\\Eloquent',
        'declaringClassName' => 'Illuminate\\Database\\Eloquent\\Model',
        'implementingClassName' => 'Illuminate\\Database\\Eloquent\\Model',
        'currentClassName' => 'Illuminate\\Database\\Eloquent\\Model',
        'aliasName' => NULL,
      ),
      'preventsLazyLoading' => 
      array (
        'name' => 'preventsLazyLoading',
        'parameters' => 
        array (
        ),
        'returnsReference' => false,
        'returnType' => NULL,
        'attributes' => 
        array (
        ),
        'docComment' => '/**
 * Determine if lazy loading is disabled.
 *
 * @return bool
 */',
        'startLine' => 2615,
        'endLine' => 2618,
        'startColumn' => 5,
        'endColumn' => 5,
        'couldThrow' => false,
        'isClosure' => false,
        'isGenerator' => false,
        'isVariadic' => false,
        'modifiers' => 17,
        'namespace' => 'Illuminate\\Database\\Eloquent',
        'declaringClassName' => 'Illuminate\\Database\\Eloquent\\Model',
        'implementingClassName' => 'Illuminate\\Database\\Eloquent\\Model',
        'currentClassName' => 'Illuminate\\Database\\Eloquent\\Model',
        'aliasName' => NULL,
      ),
      'isAutomaticallyEagerLoadingRelationships' => 
      array (
        'name' => 'isAutomaticallyEagerLoadingRelationships',
        'parameters' => 
        array (
        ),
        'returnsReference' => false,
        'returnType' => NULL,
        'attributes' => 
        array (
        ),
        'docComment' => '/**
 * Determine if relationships are being automatically eager loaded when accessed.
 *
 * @return bool
 */',
        'startLine' => 2625,
        'endLine' => 2628,
        'startColumn' => 5,
        'endColumn' => 5,
        'couldThrow' => false,
        'isClosure' => false,
        'isGenerator' => false,
        'isVariadic' => false,
        'modifiers' => 17,
        'namespace' => 'Illuminate\\Database\\Eloquent',
        'declaringClassName' => 'Illuminate\\Database\\Eloquent\\Model',
        'implementingClassName' => 'Illuminate\\Database\\Eloquent\\Model',
        'currentClassName' => 'Illuminate\\Database\\Eloquent\\Model',
        'aliasName' => NULL,
      ),
      'preventsSilentlyDiscardingAttributes' => 
      array (
        'name' => 'preventsSilentlyDiscardingAttributes',
        'parameters' => 
        array (
        ),
        'returnsReference' => false,
        'returnType' => NULL,
        'attributes' => 
        array (
        ),
        'docComment' => '/**
 * Determine if discarding guarded attribute fills is disabled.
 *
 * @return bool
 */',
        'startLine' => 2635,
        'endLine' => 2638,
        'startColumn' => 5,
        'endColumn' => 5,
        'couldThrow' => false,
        'isClosure' => false,
        'isGenerator' => false,
        'isVariadic' => false,
        'modifiers' => 17,
        'namespace' => 'Illuminate\\Database\\Eloquent',
        'declaringClassName' => 'Illuminate\\Database\\Eloquent\\Model',
        'implementingClassName' => 'Illuminate\\Database\\Eloquent\\Model',
        'currentClassName' => 'Illuminate\\Database\\Eloquent\\Model',
        'aliasName' => NULL,
      ),
      'preventsAccessingMissingAttributes' => 
      array (
        'name' => 'preventsAccessingMissingAttributes',
        'parameters' => 
        array (
        ),
        'returnsReference' => false,
        'returnType' => NULL,
        'attributes' => 
        array (
        ),
        'docComment' => '/**
 * Determine if accessing missing attributes is disabled.
 *
 * @return bool
 */',
        'startLine' => 2645,
        'endLine' => 2648,
        'startColumn' => 5,
        'endColumn' => 5,
        'couldThrow' => false,
        'isClosure' => false,
        'isGenerator' => false,
        'isVariadic' => false,
        'modifiers' => 17,
        'namespace' => 'Illuminate\\Database\\Eloquent',
        'declaringClassName' => 'Illuminate\\Database\\Eloquent\\Model',
        'implementingClassName' => 'Illuminate\\Database\\Eloquent\\Model',
        'currentClassName' => 'Illuminate\\Database\\Eloquent\\Model',
        'aliasName' => NULL,
      ),
      'broadcastChannelRoute' => 
      array (
        'name' => 'broadcastChannelRoute',
        'parameters' => 
        array (
        ),
        'returnsReference' => false,
        'returnType' => NULL,
        'attributes' => 
        array (
        ),
        'docComment' => '/**
 * Get the broadcast channel route definition that is associated with the given entity.
 *
 * @return string
 */',
        'startLine' => 2655,
        'endLine' => 2658,
        'startColumn' => 5,
        'endColumn' => 5,
        'couldThrow' => false,
        'isClosure' => false,
        'isGenerator' => false,
        'isVariadic' => false,
        'modifiers' => 1,
        'namespace' => 'Illuminate\\Database\\Eloquent',
        'declaringClassName' => 'Illuminate\\Database\\Eloquent\\Model',
        'implementingClassName' => 'Illuminate\\Database\\Eloquent\\Model',
        'currentClassName' => 'Illuminate\\Database\\Eloquent\\Model',
        'aliasName' => NULL,
      ),
      'broadcastChannel' => 
      array (
        'name' => 'broadcastChannel',
        'parameters' => 
        array (
        ),
        'returnsReference' => false,
        'returnType' => NULL,
        'attributes' => 
        array (
        ),
        'docComment' => '/**
 * Get the broadcast channel name that is associated with the given entity.
 *
 * @return string
 */',
        'startLine' => 2665,
        'endLine' => 2668,
        'startColumn' => 5,
        'endColumn' => 5,
        'couldThrow' => false,
        'isClosure' => false,
        'isGenerator' => false,
        'isVariadic' => false,
        'modifiers' => 1,
        'namespace' => 'Illuminate\\Database\\Eloquent',
        'declaringClassName' => 'Illuminate\\Database\\Eloquent\\Model',
        'implementingClassName' => 'Illuminate\\Database\\Eloquent\\Model',
        'currentClassName' => 'Illuminate\\Database\\Eloquent\\Model',
        'aliasName' => NULL,
      ),
      'resolveClassAttribute' => 
      array (
        'name' => 'resolveClassAttribute',
        'parameters' => 
        array (
          'attributeClass' => 
          array (
            'name' => 'attributeClass',
            'default' => NULL,
            'type' => 
            array (
              'class' => 'PHPStan\\BetterReflection\\Reflection\\ReflectionNamedType',
              'data' => 
              array (
                'name' => 'string',
                'isIdentifier' => true,
              ),
            ),
            'isVariadic' => false,
            'byRef' => false,
            'isPromoted' => false,
            'attributes' => 
            array (
            ),
            'startLine' => 2680,
            'endLine' => 2680,
            'startColumn' => 53,
            'endColumn' => 74,
            'parameterIndex' => 0,
            'isOptional' => false,
          ),
          'property' => 
          array (
            'name' => 'property',
            'default' => 
            array (
              'code' => 'null',
              'attributes' => 
              array (
                'startLine' => 2680,
                'endLine' => 2680,
                'startTokenPos' => 10212,
                'startFilePos' => 74760,
                'endTokenPos' => 10212,
                'endFilePos' => 74763,
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
                      'name' => 'string',
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
            'isVariadic' => false,
            'byRef' => false,
            'isPromoted' => false,
            'attributes' => 
            array (
            ),
            'startLine' => 2680,
            'endLine' => 2680,
            'startColumn' => 77,
            'endColumn' => 100,
            'parameterIndex' => 1,
            'isOptional' => true,
          ),
          'class' => 
          array (
            'name' => 'class',
            'default' => 
            array (
              'code' => 'null',
              'attributes' => 
              array (
                'startLine' => 2680,
                'endLine' => 2680,
                'startTokenPos' => 10222,
                'startFilePos' => 74783,
                'endTokenPos' => 10222,
                'endFilePos' => 74786,
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
                      'name' => 'string',
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
            'isVariadic' => false,
            'byRef' => false,
            'isPromoted' => false,
            'attributes' => 
            array (
            ),
            'startLine' => 2680,
            'endLine' => 2680,
            'startColumn' => 103,
            'endColumn' => 123,
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
 * Resolve a class attribute value from the model.
 *
 * @template TAttribute of object
 *
 * @param  class-string<TAttribute>  $attributeClass
 * @param  string|null  $property
 * @param  string|null  $class
 * @return mixed
 */',
        'startLine' => 2680,
        'endLine' => 2717,
        'startColumn' => 5,
        'endColumn' => 5,
        'couldThrow' => false,
        'isClosure' => false,
        'isGenerator' => false,
        'isVariadic' => false,
        'modifiers' => 18,
        'namespace' => 'Illuminate\\Database\\Eloquent',
        'declaringClassName' => 'Illuminate\\Database\\Eloquent\\Model',
        'implementingClassName' => 'Illuminate\\Database\\Eloquent\\Model',
        'currentClassName' => 'Illuminate\\Database\\Eloquent\\Model',
        'aliasName' => NULL,
      ),
      '__get' => 
      array (
        'name' => '__get',
        'parameters' => 
        array (
          'key' => 
          array (
            'name' => 'key',
            'default' => NULL,
            'type' => NULL,
            'isVariadic' => false,
            'byRef' => false,
            'isPromoted' => false,
            'attributes' => 
            array (
            ),
            'startLine' => 2725,
            'endLine' => 2725,
            'startColumn' => 27,
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
 * Dynamically retrieve attributes on the model.
 *
 * @param  string  $key
 * @return mixed
 */',
        'startLine' => 2725,
        'endLine' => 2728,
        'startColumn' => 5,
        'endColumn' => 5,
        'couldThrow' => false,
        'isClosure' => false,
        'isGenerator' => false,
        'isVariadic' => false,
        'modifiers' => 1,
        'namespace' => 'Illuminate\\Database\\Eloquent',
        'declaringClassName' => 'Illuminate\\Database\\Eloquent\\Model',
        'implementingClassName' => 'Illuminate\\Database\\Eloquent\\Model',
        'currentClassName' => 'Illuminate\\Database\\Eloquent\\Model',
        'aliasName' => NULL,
      ),
      '__set' => 
      array (
        'name' => '__set',
        'parameters' => 
        array (
          'key' => 
          array (
            'name' => 'key',
            'default' => NULL,
            'type' => NULL,
            'isVariadic' => false,
            'byRef' => false,
            'isPromoted' => false,
            'attributes' => 
            array (
            ),
            'startLine' => 2737,
            'endLine' => 2737,
            'startColumn' => 27,
            'endColumn' => 30,
            'parameterIndex' => 0,
            'isOptional' => false,
          ),
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
            'startLine' => 2737,
            'endLine' => 2737,
            'startColumn' => 33,
            'endColumn' => 38,
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
 * Dynamically set attributes on the model.
 *
 * @param  string  $key
 * @param  mixed  $value
 * @return void
 */',
        'startLine' => 2737,
        'endLine' => 2740,
        'startColumn' => 5,
        'endColumn' => 5,
        'couldThrow' => false,
        'isClosure' => false,
        'isGenerator' => false,
        'isVariadic' => false,
        'modifiers' => 1,
        'namespace' => 'Illuminate\\Database\\Eloquent',
        'declaringClassName' => 'Illuminate\\Database\\Eloquent\\Model',
        'implementingClassName' => 'Illuminate\\Database\\Eloquent\\Model',
        'currentClassName' => 'Illuminate\\Database\\Eloquent\\Model',
        'aliasName' => NULL,
      ),
      'offsetExists' => 
      array (
        'name' => 'offsetExists',
        'parameters' => 
        array (
          'offset' => 
          array (
            'name' => 'offset',
            'default' => NULL,
            'type' => NULL,
            'isVariadic' => false,
            'byRef' => false,
            'isPromoted' => false,
            'attributes' => 
            array (
            ),
            'startLine' => 2748,
            'endLine' => 2748,
            'startColumn' => 34,
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
            'name' => 'bool',
            'isIdentifier' => true,
          ),
        ),
        'attributes' => 
        array (
        ),
        'docComment' => '/**
 * Determine if the given attribute exists.
 *
 * @param  mixed  $offset
 * @return bool
 */',
        'startLine' => 2748,
        'endLine' => 2759,
        'startColumn' => 5,
        'endColumn' => 5,
        'couldThrow' => false,
        'isClosure' => false,
        'isGenerator' => false,
        'isVariadic' => false,
        'modifiers' => 1,
        'namespace' => 'Illuminate\\Database\\Eloquent',
        'declaringClassName' => 'Illuminate\\Database\\Eloquent\\Model',
        'implementingClassName' => 'Illuminate\\Database\\Eloquent\\Model',
        'currentClassName' => 'Illuminate\\Database\\Eloquent\\Model',
        'aliasName' => NULL,
      ),
      'offsetGet' => 
      array (
        'name' => 'offsetGet',
        'parameters' => 
        array (
          'offset' => 
          array (
            'name' => 'offset',
            'default' => NULL,
            'type' => NULL,
            'isVariadic' => false,
            'byRef' => false,
            'isPromoted' => false,
            'attributes' => 
            array (
            ),
            'startLine' => 2767,
            'endLine' => 2767,
            'startColumn' => 31,
            'endColumn' => 37,
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
            'name' => 'mixed',
            'isIdentifier' => true,
          ),
        ),
        'attributes' => 
        array (
        ),
        'docComment' => '/**
 * Get the value for a given offset.
 *
 * @param  mixed  $offset
 * @return mixed
 */',
        'startLine' => 2767,
        'endLine' => 2770,
        'startColumn' => 5,
        'endColumn' => 5,
        'couldThrow' => false,
        'isClosure' => false,
        'isGenerator' => false,
        'isVariadic' => false,
        'modifiers' => 1,
        'namespace' => 'Illuminate\\Database\\Eloquent',
        'declaringClassName' => 'Illuminate\\Database\\Eloquent\\Model',
        'implementingClassName' => 'Illuminate\\Database\\Eloquent\\Model',
        'currentClassName' => 'Illuminate\\Database\\Eloquent\\Model',
        'aliasName' => NULL,
      ),
      'offsetSet' => 
      array (
        'name' => 'offsetSet',
        'parameters' => 
        array (
          'offset' => 
          array (
            'name' => 'offset',
            'default' => NULL,
            'type' => NULL,
            'isVariadic' => false,
            'byRef' => false,
            'isPromoted' => false,
            'attributes' => 
            array (
            ),
            'startLine' => 2779,
            'endLine' => 2779,
            'startColumn' => 31,
            'endColumn' => 37,
            'parameterIndex' => 0,
            'isOptional' => false,
          ),
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
            'startLine' => 2779,
            'endLine' => 2779,
            'startColumn' => 40,
            'endColumn' => 45,
            'parameterIndex' => 1,
            'isOptional' => false,
          ),
        ),
        'returnsReference' => false,
        'returnType' => 
        array (
          'class' => 'PHPStan\\BetterReflection\\Reflection\\ReflectionNamedType',
          'data' => 
          array (
            'name' => 'void',
            'isIdentifier' => true,
          ),
        ),
        'attributes' => 
        array (
        ),
        'docComment' => '/**
 * Set the value for a given offset.
 *
 * @param  mixed  $offset
 * @param  mixed  $value
 * @return void
 */',
        'startLine' => 2779,
        'endLine' => 2782,
        'startColumn' => 5,
        'endColumn' => 5,
        'couldThrow' => false,
        'isClosure' => false,
        'isGenerator' => false,
        'isVariadic' => false,
        'modifiers' => 1,
        'namespace' => 'Illuminate\\Database\\Eloquent',
        'declaringClassName' => 'Illuminate\\Database\\Eloquent\\Model',
        'implementingClassName' => 'Illuminate\\Database\\Eloquent\\Model',
        'currentClassName' => 'Illuminate\\Database\\Eloquent\\Model',
        'aliasName' => NULL,
      ),
      'offsetUnset' => 
      array (
        'name' => 'offsetUnset',
        'parameters' => 
        array (
          'offset' => 
          array (
            'name' => 'offset',
            'default' => NULL,
            'type' => NULL,
            'isVariadic' => false,
            'byRef' => false,
            'isPromoted' => false,
            'attributes' => 
            array (
            ),
            'startLine' => 2790,
            'endLine' => 2790,
            'startColumn' => 33,
            'endColumn' => 39,
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
            'name' => 'void',
            'isIdentifier' => true,
          ),
        ),
        'attributes' => 
        array (
        ),
        'docComment' => '/**
 * Unset the value for a given offset.
 *
 * @param  mixed  $offset
 * @return void
 */',
        'startLine' => 2790,
        'endLine' => 2798,
        'startColumn' => 5,
        'endColumn' => 5,
        'couldThrow' => false,
        'isClosure' => false,
        'isGenerator' => false,
        'isVariadic' => false,
        'modifiers' => 1,
        'namespace' => 'Illuminate\\Database\\Eloquent',
        'declaringClassName' => 'Illuminate\\Database\\Eloquent\\Model',
        'implementingClassName' => 'Illuminate\\Database\\Eloquent\\Model',
        'currentClassName' => 'Illuminate\\Database\\Eloquent\\Model',
        'aliasName' => NULL,
      ),
      '__isset' => 
      array (
        'name' => '__isset',
        'parameters' => 
        array (
          'key' => 
          array (
            'name' => 'key',
            'default' => NULL,
            'type' => NULL,
            'isVariadic' => false,
            'byRef' => false,
            'isPromoted' => false,
            'attributes' => 
            array (
            ),
            'startLine' => 2806,
            'endLine' => 2806,
            'startColumn' => 29,
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
 * Determine if an attribute or relation exists on the model.
 *
 * @param  string  $key
 * @return bool
 */',
        'startLine' => 2806,
        'endLine' => 2809,
        'startColumn' => 5,
        'endColumn' => 5,
        'couldThrow' => false,
        'isClosure' => false,
        'isGenerator' => false,
        'isVariadic' => false,
        'modifiers' => 1,
        'namespace' => 'Illuminate\\Database\\Eloquent',
        'declaringClassName' => 'Illuminate\\Database\\Eloquent\\Model',
        'implementingClassName' => 'Illuminate\\Database\\Eloquent\\Model',
        'currentClassName' => 'Illuminate\\Database\\Eloquent\\Model',
        'aliasName' => NULL,
      ),
      '__unset' => 
      array (
        'name' => '__unset',
        'parameters' => 
        array (
          'key' => 
          array (
            'name' => 'key',
            'default' => NULL,
            'type' => NULL,
            'isVariadic' => false,
            'byRef' => false,
            'isPromoted' => false,
            'attributes' => 
            array (
            ),
            'startLine' => 2817,
            'endLine' => 2817,
            'startColumn' => 29,
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
 * Unset an attribute on the model.
 *
 * @param  string  $key
 * @return void
 */',
        'startLine' => 2817,
        'endLine' => 2820,
        'startColumn' => 5,
        'endColumn' => 5,
        'couldThrow' => false,
        'isClosure' => false,
        'isGenerator' => false,
        'isVariadic' => false,
        'modifiers' => 1,
        'namespace' => 'Illuminate\\Database\\Eloquent',
        'declaringClassName' => 'Illuminate\\Database\\Eloquent\\Model',
        'implementingClassName' => 'Illuminate\\Database\\Eloquent\\Model',
        'currentClassName' => 'Illuminate\\Database\\Eloquent\\Model',
        'aliasName' => NULL,
      ),
      '__call' => 
      array (
        'name' => '__call',
        'parameters' => 
        array (
          'method' => 
          array (
            'name' => 'method',
            'default' => NULL,
            'type' => NULL,
            'isVariadic' => false,
            'byRef' => false,
            'isPromoted' => false,
            'attributes' => 
            array (
            ),
            'startLine' => 2829,
            'endLine' => 2829,
            'startColumn' => 28,
            'endColumn' => 34,
            'parameterIndex' => 0,
            'isOptional' => false,
          ),
          'parameters' => 
          array (
            'name' => 'parameters',
            'default' => NULL,
            'type' => NULL,
            'isVariadic' => false,
            'byRef' => false,
            'isPromoted' => false,
            'attributes' => 
            array (
            ),
            'startLine' => 2829,
            'endLine' => 2829,
            'startColumn' => 37,
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
 * Handle dynamic method calls into the model.
 *
 * @param  string  $method
 * @param  array  $parameters
 * @return mixed
 */',
        'startLine' => 2829,
        'endLine' => 2845,
        'startColumn' => 5,
        'endColumn' => 5,
        'couldThrow' => false,
        'isClosure' => false,
        'isGenerator' => false,
        'isVariadic' => false,
        'modifiers' => 1,
        'namespace' => 'Illuminate\\Database\\Eloquent',
        'declaringClassName' => 'Illuminate\\Database\\Eloquent\\Model',
        'implementingClassName' => 'Illuminate\\Database\\Eloquent\\Model',
        'currentClassName' => 'Illuminate\\Database\\Eloquent\\Model',
        'aliasName' => NULL,
      ),
      '__callStatic' => 
      array (
        'name' => '__callStatic',
        'parameters' => 
        array (
          'method' => 
          array (
            'name' => 'method',
            'default' => NULL,
            'type' => NULL,
            'isVariadic' => false,
            'byRef' => false,
            'isPromoted' => false,
            'attributes' => 
            array (
            ),
            'startLine' => 2854,
            'endLine' => 2854,
            'startColumn' => 41,
            'endColumn' => 47,
            'parameterIndex' => 0,
            'isOptional' => false,
          ),
          'parameters' => 
          array (
            'name' => 'parameters',
            'default' => NULL,
            'type' => NULL,
            'isVariadic' => false,
            'byRef' => false,
            'isPromoted' => false,
            'attributes' => 
            array (
            ),
            'startLine' => 2854,
            'endLine' => 2854,
            'startColumn' => 50,
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
 * Handle dynamic static method calls into the model.
 *
 * @param  string  $method
 * @param  array  $parameters
 * @return mixed
 */',
        'startLine' => 2854,
        'endLine' => 2861,
        'startColumn' => 5,
        'endColumn' => 5,
        'couldThrow' => false,
        'isClosure' => false,
        'isGenerator' => false,
        'isVariadic' => false,
        'modifiers' => 17,
        'namespace' => 'Illuminate\\Database\\Eloquent',
        'declaringClassName' => 'Illuminate\\Database\\Eloquent\\Model',
        'implementingClassName' => 'Illuminate\\Database\\Eloquent\\Model',
        'currentClassName' => 'Illuminate\\Database\\Eloquent\\Model',
        'aliasName' => NULL,
      ),
      '__toString' => 
      array (
        'name' => '__toString',
        'parameters' => 
        array (
        ),
        'returnsReference' => false,
        'returnType' => NULL,
        'attributes' => 
        array (
        ),
        'docComment' => '/**
 * Convert the model to its string representation.
 *
 * @return string
 */',
        'startLine' => 2868,
        'endLine' => 2873,
        'startColumn' => 5,
        'endColumn' => 5,
        'couldThrow' => false,
        'isClosure' => false,
        'isGenerator' => false,
        'isVariadic' => false,
        'modifiers' => 1,
        'namespace' => 'Illuminate\\Database\\Eloquent',
        'declaringClassName' => 'Illuminate\\Database\\Eloquent\\Model',
        'implementingClassName' => 'Illuminate\\Database\\Eloquent\\Model',
        'currentClassName' => 'Illuminate\\Database\\Eloquent\\Model',
        'aliasName' => NULL,
      ),
      'escapeWhenCastingToString' => 
      array (
        'name' => 'escapeWhenCastingToString',
        'parameters' => 
        array (
          'escape' => 
          array (
            'name' => 'escape',
            'default' => 
            array (
              'code' => 'true',
              'attributes' => 
              array (
                'startLine' => 2881,
                'endLine' => 2881,
                'startTokenPos' => 11090,
                'startFilePos' => 80109,
                'endTokenPos' => 11090,
                'endFilePos' => 80112,
              ),
            ),
            'type' => NULL,
            'isVariadic' => false,
            'byRef' => false,
            'isPromoted' => false,
            'attributes' => 
            array (
            ),
            'startLine' => 2881,
            'endLine' => 2881,
            'startColumn' => 47,
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
 * Indicate that the object\'s string representation should be escaped when __toString is invoked.
 *
 * @param  bool  $escape
 * @return $this
 */',
        'startLine' => 2881,
        'endLine' => 2886,
        'startColumn' => 5,
        'endColumn' => 5,
        'couldThrow' => false,
        'isClosure' => false,
        'isGenerator' => false,
        'isVariadic' => false,
        'modifiers' => 1,
        'namespace' => 'Illuminate\\Database\\Eloquent',
        'declaringClassName' => 'Illuminate\\Database\\Eloquent\\Model',
        'implementingClassName' => 'Illuminate\\Database\\Eloquent\\Model',
        'currentClassName' => 'Illuminate\\Database\\Eloquent\\Model',
        'aliasName' => NULL,
      ),
      '__sleep' => 
      array (
        'name' => '__sleep',
        'parameters' => 
        array (
        ),
        'returnsReference' => false,
        'returnType' => NULL,
        'attributes' => 
        array (
        ),
        'docComment' => '/**
 * Prepare the object for serialization.
 *
 * @return array
 */',
        'startLine' => 2893,
        'endLine' => 2913,
        'startColumn' => 5,
        'endColumn' => 5,
        'couldThrow' => false,
        'isClosure' => false,
        'isGenerator' => false,
        'isVariadic' => false,
        'modifiers' => 1,
        'namespace' => 'Illuminate\\Database\\Eloquent',
        'declaringClassName' => 'Illuminate\\Database\\Eloquent\\Model',
        'implementingClassName' => 'Illuminate\\Database\\Eloquent\\Model',
        'currentClassName' => 'Illuminate\\Database\\Eloquent\\Model',
        'aliasName' => NULL,
      ),
      '__wakeup' => 
      array (
        'name' => '__wakeup',
        'parameters' => 
        array (
        ),
        'returnsReference' => false,
        'returnType' => NULL,
        'attributes' => 
        array (
        ),
        'docComment' => '/**
 * When a model is being unserialized, check if it needs to be booted.
 *
 * @return void
 */',
        'startLine' => 2920,
        'endLine' => 2929,
        'startColumn' => 5,
        'endColumn' => 5,
        'couldThrow' => false,
        'isClosure' => false,
        'isGenerator' => false,
        'isVariadic' => false,
        'modifiers' => 1,
        'namespace' => 'Illuminate\\Database\\Eloquent',
        'declaringClassName' => 'Illuminate\\Database\\Eloquent\\Model',
        'implementingClassName' => 'Illuminate\\Database\\Eloquent\\Model',
        'currentClassName' => 'Illuminate\\Database\\Eloquent\\Model',
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