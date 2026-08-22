<?php declare(strict_types = 1);

// osfsl-/var/www/html/vendor/composer/../laravel/framework/src/Illuminate/Database/Connection.php-PHPStan\BetterReflection\Reflection\ReflectionClass-Illuminate\Database\Connection
return \PHPStan\Cache\CacheItem::__set_state(array(
   'variableKey' => 'v2-76518dbde992d5bf23974190252cc4a86a3b684364e72bfcc8246d950eb059a8-8.5.9-6.70.0.3',
   'data' => 
  array (
    'locatedSource' => 
    array (
      'class' => 'PHPStan\\BetterReflection\\SourceLocator\\Located\\LocatedSource',
      'data' => 
      array (
        'name' => 'Illuminate\\Database\\Connection',
        'filename' => '/var/www/html/vendor/composer/../laravel/framework/src/Illuminate/Database/Connection.php',
      ),
    ),
    'namespace' => 'Illuminate\\Database',
    'name' => 'Illuminate\\Database\\Connection',
    'shortName' => 'Connection',
    'isInterface' => false,
    'isTrait' => false,
    'isEnum' => false,
    'isBackedEnum' => false,
    'modifiers' => 0,
    'docComment' => NULL,
    'attributes' => 
    array (
    ),
    'startLine' => 30,
    'endLine' => 1898,
    'startColumn' => 1,
    'endColumn' => 1,
    'parentClassName' => NULL,
    'implementsClassNames' => 
    array (
      0 => 'Illuminate\\Database\\ConnectionInterface',
    ),
    'traitClassNames' => 
    array (
      0 => 'Illuminate\\Database\\DetectsConcurrencyErrors',
      1 => 'Illuminate\\Database\\DetectsLostConnections',
      2 => 'Illuminate\\Database\\Concerns\\ManagesTransactions',
      3 => 'Illuminate\\Support\\InteractsWithTime',
      4 => 'Illuminate\\Support\\Traits\\Macroable',
    ),
    'immediateConstants' => 
    array (
    ),
    'immediateProperties' => 
    array (
      'pdo' => 
      array (
        'declaringClassName' => 'Illuminate\\Database\\Connection',
        'implementingClassName' => 'Illuminate\\Database\\Connection',
        'name' => 'pdo',
        'modifiers' => 2,
        'type' => NULL,
        'default' => NULL,
        'docComment' => '/**
 * The active PDO connection.
 *
 * @var \\PDO|(\\Closure(): \\PDO)
 */',
        'attributes' => 
        array (
        ),
        'startLine' => 43,
        'endLine' => 43,
        'startColumn' => 5,
        'endColumn' => 19,
        'isPromoted' => false,
        'declaredAtCompileTime' => true,
        'immediateVirtual' => false,
        'immediateHooks' => 
        array (
        ),
      ),
      'readPdo' => 
      array (
        'declaringClassName' => 'Illuminate\\Database\\Connection',
        'implementingClassName' => 'Illuminate\\Database\\Connection',
        'name' => 'readPdo',
        'modifiers' => 2,
        'type' => NULL,
        'default' => NULL,
        'docComment' => '/**
 * The active PDO connection used for reads.
 *
 * @var \\PDO|(\\Closure(): \\PDO)
 */',
        'attributes' => 
        array (
        ),
        'startLine' => 50,
        'endLine' => 50,
        'startColumn' => 5,
        'endColumn' => 23,
        'isPromoted' => false,
        'declaredAtCompileTime' => true,
        'immediateVirtual' => false,
        'immediateHooks' => 
        array (
        ),
      ),
      'readPdoConfig' => 
      array (
        'declaringClassName' => 'Illuminate\\Database\\Connection',
        'implementingClassName' => 'Illuminate\\Database\\Connection',
        'name' => 'readPdoConfig',
        'modifiers' => 2,
        'type' => NULL,
        'default' => 
        array (
          'code' => '[]',
          'attributes' => 
          array (
            'startLine' => 57,
            'endLine' => 57,
            'startTokenPos' => 185,
            'startFilePos' => 1533,
            'endTokenPos' => 186,
            'endFilePos' => 1534,
          ),
        ),
        'docComment' => '/**
 * The database connection configuration options for reading.
 *
 * @var array
 */',
        'attributes' => 
        array (
        ),
        'startLine' => 57,
        'endLine' => 57,
        'startColumn' => 5,
        'endColumn' => 34,
        'isPromoted' => false,
        'declaredAtCompileTime' => true,
        'immediateVirtual' => false,
        'immediateHooks' => 
        array (
        ),
      ),
      'directPdo' => 
      array (
        'declaringClassName' => 'Illuminate\\Database\\Connection',
        'implementingClassName' => 'Illuminate\\Database\\Connection',
        'name' => 'directPdo',
        'modifiers' => 2,
        'type' => NULL,
        'default' => NULL,
        'docComment' => '/**
 * The active PDO connection used for direct connections.
 *
 * @var \\PDO|(\\Closure(): \\PDO)
 */',
        'attributes' => 
        array (
        ),
        'startLine' => 64,
        'endLine' => 64,
        'startColumn' => 5,
        'endColumn' => 25,
        'isPromoted' => false,
        'declaredAtCompileTime' => true,
        'immediateVirtual' => false,
        'immediateHooks' => 
        array (
        ),
      ),
      'directPdoConfig' => 
      array (
        'declaringClassName' => 'Illuminate\\Database\\Connection',
        'implementingClassName' => 'Illuminate\\Database\\Connection',
        'name' => 'directPdoConfig',
        'modifiers' => 2,
        'type' => NULL,
        'default' => 
        array (
          'code' => '[]',
          'attributes' => 
          array (
            'startLine' => 71,
            'endLine' => 71,
            'startTokenPos' => 204,
            'startFilePos' => 1837,
            'endTokenPos' => 205,
            'endFilePos' => 1838,
          ),
        ),
        'docComment' => '/**
 * The database connection configuration options for direct connections.
 *
 * @var array
 */',
        'attributes' => 
        array (
        ),
        'startLine' => 71,
        'endLine' => 71,
        'startColumn' => 5,
        'endColumn' => 36,
        'isPromoted' => false,
        'declaredAtCompileTime' => true,
        'immediateVirtual' => false,
        'immediateHooks' => 
        array (
        ),
      ),
      'database' => 
      array (
        'declaringClassName' => 'Illuminate\\Database\\Connection',
        'implementingClassName' => 'Illuminate\\Database\\Connection',
        'name' => 'database',
        'modifiers' => 2,
        'type' => NULL,
        'default' => NULL,
        'docComment' => '/**
 * The name of the connected database.
 *
 * @var string
 */',
        'attributes' => 
        array (
        ),
        'startLine' => 78,
        'endLine' => 78,
        'startColumn' => 5,
        'endColumn' => 24,
        'isPromoted' => false,
        'declaredAtCompileTime' => true,
        'immediateVirtual' => false,
        'immediateHooks' => 
        array (
        ),
      ),
      'readWriteType' => 
      array (
        'declaringClassName' => 'Illuminate\\Database\\Connection',
        'implementingClassName' => 'Illuminate\\Database\\Connection',
        'name' => 'readWriteType',
        'modifiers' => 2,
        'type' => NULL,
        'default' => NULL,
        'docComment' => '/**
 * The type of the connection.
 *
 * @var string|null
 */',
        'attributes' => 
        array (
        ),
        'startLine' => 85,
        'endLine' => 85,
        'startColumn' => 5,
        'endColumn' => 29,
        'isPromoted' => false,
        'declaredAtCompileTime' => true,
        'immediateVirtual' => false,
        'immediateHooks' => 
        array (
        ),
      ),
      'tablePrefix' => 
      array (
        'declaringClassName' => 'Illuminate\\Database\\Connection',
        'implementingClassName' => 'Illuminate\\Database\\Connection',
        'name' => 'tablePrefix',
        'modifiers' => 2,
        'type' => NULL,
        'default' => 
        array (
          'code' => '\'\'',
          'attributes' => 
          array (
            'startLine' => 92,
            'endLine' => 92,
            'startTokenPos' => 230,
            'startFilePos' => 2181,
            'endTokenPos' => 230,
            'endFilePos' => 2182,
          ),
        ),
        'docComment' => '/**
 * The table prefix for the connection.
 *
 * @var string
 */',
        'attributes' => 
        array (
        ),
        'startLine' => 92,
        'endLine' => 92,
        'startColumn' => 5,
        'endColumn' => 32,
        'isPromoted' => false,
        'declaredAtCompileTime' => true,
        'immediateVirtual' => false,
        'immediateHooks' => 
        array (
        ),
      ),
      'config' => 
      array (
        'declaringClassName' => 'Illuminate\\Database\\Connection',
        'implementingClassName' => 'Illuminate\\Database\\Connection',
        'name' => 'config',
        'modifiers' => 2,
        'type' => NULL,
        'default' => 
        array (
          'code' => '[]',
          'attributes' => 
          array (
            'startLine' => 99,
            'endLine' => 99,
            'startTokenPos' => 241,
            'startFilePos' => 2305,
            'endTokenPos' => 242,
            'endFilePos' => 2306,
          ),
        ),
        'docComment' => '/**
 * The database connection configuration options.
 *
 * @var array
 */',
        'attributes' => 
        array (
        ),
        'startLine' => 99,
        'endLine' => 99,
        'startColumn' => 5,
        'endColumn' => 27,
        'isPromoted' => false,
        'declaredAtCompileTime' => true,
        'immediateVirtual' => false,
        'immediateHooks' => 
        array (
        ),
      ),
      'reconnector' => 
      array (
        'declaringClassName' => 'Illuminate\\Database\\Connection',
        'implementingClassName' => 'Illuminate\\Database\\Connection',
        'name' => 'reconnector',
        'modifiers' => 2,
        'type' => NULL,
        'default' => NULL,
        'docComment' => '/**
 * The reconnector instance for the connection.
 *
 * @var (callable(\\Illuminate\\Database\\Connection): mixed)
 */',
        'attributes' => 
        array (
        ),
        'startLine' => 106,
        'endLine' => 106,
        'startColumn' => 5,
        'endColumn' => 27,
        'isPromoted' => false,
        'declaredAtCompileTime' => true,
        'immediateVirtual' => false,
        'immediateHooks' => 
        array (
        ),
      ),
      'queryGrammar' => 
      array (
        'declaringClassName' => 'Illuminate\\Database\\Connection',
        'implementingClassName' => 'Illuminate\\Database\\Connection',
        'name' => 'queryGrammar',
        'modifiers' => 2,
        'type' => NULL,
        'default' => NULL,
        'docComment' => '/**
 * The query grammar implementation.
 *
 * @var \\Illuminate\\Database\\Query\\Grammars\\Grammar
 */',
        'attributes' => 
        array (
        ),
        'startLine' => 113,
        'endLine' => 113,
        'startColumn' => 5,
        'endColumn' => 28,
        'isPromoted' => false,
        'declaredAtCompileTime' => true,
        'immediateVirtual' => false,
        'immediateHooks' => 
        array (
        ),
      ),
      'schemaGrammar' => 
      array (
        'declaringClassName' => 'Illuminate\\Database\\Connection',
        'implementingClassName' => 'Illuminate\\Database\\Connection',
        'name' => 'schemaGrammar',
        'modifiers' => 2,
        'type' => NULL,
        'default' => NULL,
        'docComment' => '/**
 * The schema grammar implementation.
 *
 * @var \\Illuminate\\Database\\Schema\\Grammars\\Grammar
 */',
        'attributes' => 
        array (
        ),
        'startLine' => 120,
        'endLine' => 120,
        'startColumn' => 5,
        'endColumn' => 29,
        'isPromoted' => false,
        'declaredAtCompileTime' => true,
        'immediateVirtual' => false,
        'immediateHooks' => 
        array (
        ),
      ),
      'postProcessor' => 
      array (
        'declaringClassName' => 'Illuminate\\Database\\Connection',
        'implementingClassName' => 'Illuminate\\Database\\Connection',
        'name' => 'postProcessor',
        'modifiers' => 2,
        'type' => NULL,
        'default' => NULL,
        'docComment' => '/**
 * The query post processor implementation.
 *
 * @var \\Illuminate\\Database\\Query\\Processors\\Processor
 */',
        'attributes' => 
        array (
        ),
        'startLine' => 127,
        'endLine' => 127,
        'startColumn' => 5,
        'endColumn' => 29,
        'isPromoted' => false,
        'declaredAtCompileTime' => true,
        'immediateVirtual' => false,
        'immediateHooks' => 
        array (
        ),
      ),
      'events' => 
      array (
        'declaringClassName' => 'Illuminate\\Database\\Connection',
        'implementingClassName' => 'Illuminate\\Database\\Connection',
        'name' => 'events',
        'modifiers' => 2,
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
        'startLine' => 134,
        'endLine' => 134,
        'startColumn' => 5,
        'endColumn' => 22,
        'isPromoted' => false,
        'declaredAtCompileTime' => true,
        'immediateVirtual' => false,
        'immediateHooks' => 
        array (
        ),
      ),
      'fetchMode' => 
      array (
        'declaringClassName' => 'Illuminate\\Database\\Connection',
        'implementingClassName' => 'Illuminate\\Database\\Connection',
        'name' => 'fetchMode',
        'modifiers' => 2,
        'type' => NULL,
        'default' => 
        array (
          'code' => '\\PDO::FETCH_OBJ',
          'attributes' => 
          array (
            'startLine' => 141,
            'endLine' => 141,
            'startTokenPos' => 288,
            'startFilePos' => 3199,
            'endTokenPos' => 290,
            'endFilePos' => 3212,
          ),
        ),
        'docComment' => '/**
 * The default fetch mode of the connection.
 *
 * @var int
 */',
        'attributes' => 
        array (
        ),
        'startLine' => 141,
        'endLine' => 141,
        'startColumn' => 5,
        'endColumn' => 42,
        'isPromoted' => false,
        'declaredAtCompileTime' => true,
        'immediateVirtual' => false,
        'immediateHooks' => 
        array (
        ),
      ),
      'transactions' => 
      array (
        'declaringClassName' => 'Illuminate\\Database\\Connection',
        'implementingClassName' => 'Illuminate\\Database\\Connection',
        'name' => 'transactions',
        'modifiers' => 2,
        'type' => NULL,
        'default' => 
        array (
          'code' => '0',
          'attributes' => 
          array (
            'startLine' => 148,
            'endLine' => 148,
            'startTokenPos' => 301,
            'startFilePos' => 3327,
            'endTokenPos' => 301,
            'endFilePos' => 3327,
          ),
        ),
        'docComment' => '/**
 * The number of active transactions.
 *
 * @var int
 */',
        'attributes' => 
        array (
        ),
        'startLine' => 148,
        'endLine' => 148,
        'startColumn' => 5,
        'endColumn' => 32,
        'isPromoted' => false,
        'declaredAtCompileTime' => true,
        'immediateVirtual' => false,
        'immediateHooks' => 
        array (
        ),
      ),
      'transactionsManager' => 
      array (
        'declaringClassName' => 'Illuminate\\Database\\Connection',
        'implementingClassName' => 'Illuminate\\Database\\Connection',
        'name' => 'transactionsManager',
        'modifiers' => 2,
        'type' => NULL,
        'default' => NULL,
        'docComment' => '/**
 * The transaction manager instance.
 *
 * @var \\Illuminate\\Database\\DatabaseTransactionsManager|null
 */',
        'attributes' => 
        array (
        ),
        'startLine' => 155,
        'endLine' => 155,
        'startColumn' => 5,
        'endColumn' => 35,
        'isPromoted' => false,
        'declaredAtCompileTime' => true,
        'immediateVirtual' => false,
        'immediateHooks' => 
        array (
        ),
      ),
      'recordsModified' => 
      array (
        'declaringClassName' => 'Illuminate\\Database\\Connection',
        'implementingClassName' => 'Illuminate\\Database\\Connection',
        'name' => 'recordsModified',
        'modifiers' => 2,
        'type' => NULL,
        'default' => 
        array (
          'code' => 'false',
          'attributes' => 
          array (
            'startLine' => 162,
            'endLine' => 162,
            'startTokenPos' => 319,
            'startFilePos' => 3631,
            'endTokenPos' => 319,
            'endFilePos' => 3635,
          ),
        ),
        'docComment' => '/**
 * Indicates if changes have been made to the database.
 *
 * @var bool
 */',
        'attributes' => 
        array (
        ),
        'startLine' => 162,
        'endLine' => 162,
        'startColumn' => 5,
        'endColumn' => 39,
        'isPromoted' => false,
        'declaredAtCompileTime' => true,
        'immediateVirtual' => false,
        'immediateHooks' => 
        array (
        ),
      ),
      'readOnWriteConnection' => 
      array (
        'declaringClassName' => 'Illuminate\\Database\\Connection',
        'implementingClassName' => 'Illuminate\\Database\\Connection',
        'name' => 'readOnWriteConnection',
        'modifiers' => 2,
        'type' => NULL,
        'default' => 
        array (
          'code' => 'false',
          'attributes' => 
          array (
            'startLine' => 169,
            'endLine' => 169,
            'startTokenPos' => 330,
            'startFilePos' => 3792,
            'endTokenPos' => 330,
            'endFilePos' => 3796,
          ),
        ),
        'docComment' => '/**
 * Indicates if the connection should use the "write" PDO connection.
 *
 * @var bool
 */',
        'attributes' => 
        array (
        ),
        'startLine' => 169,
        'endLine' => 169,
        'startColumn' => 5,
        'endColumn' => 45,
        'isPromoted' => false,
        'declaredAtCompileTime' => true,
        'immediateVirtual' => false,
        'immediateHooks' => 
        array (
        ),
      ),
      'queryLog' => 
      array (
        'declaringClassName' => 'Illuminate\\Database\\Connection',
        'implementingClassName' => 'Illuminate\\Database\\Connection',
        'name' => 'queryLog',
        'modifiers' => 2,
        'type' => NULL,
        'default' => 
        array (
          'code' => '[]',
          'attributes' => 
          array (
            'startLine' => 176,
            'endLine' => 176,
            'startTokenPos' => 341,
            'startFilePos' => 3973,
            'endTokenPos' => 342,
            'endFilePos' => 3974,
          ),
        ),
        'docComment' => '/**
 * All of the queries run against the connection.
 *
 * @var array{query: string, bindings: array, time: float|null}[]
 */',
        'attributes' => 
        array (
        ),
        'startLine' => 176,
        'endLine' => 176,
        'startColumn' => 5,
        'endColumn' => 29,
        'isPromoted' => false,
        'declaredAtCompileTime' => true,
        'immediateVirtual' => false,
        'immediateHooks' => 
        array (
        ),
      ),
      'loggingQueries' => 
      array (
        'declaringClassName' => 'Illuminate\\Database\\Connection',
        'implementingClassName' => 'Illuminate\\Database\\Connection',
        'name' => 'loggingQueries',
        'modifiers' => 2,
        'type' => NULL,
        'default' => 
        array (
          'code' => 'false',
          'attributes' => 
          array (
            'startLine' => 183,
            'endLine' => 183,
            'startTokenPos' => 353,
            'startFilePos' => 4101,
            'endTokenPos' => 353,
            'endFilePos' => 4105,
          ),
        ),
        'docComment' => '/**
 * Indicates whether queries are being logged.
 *
 * @var bool
 */',
        'attributes' => 
        array (
        ),
        'startLine' => 183,
        'endLine' => 183,
        'startColumn' => 5,
        'endColumn' => 38,
        'isPromoted' => false,
        'declaredAtCompileTime' => true,
        'immediateVirtual' => false,
        'immediateHooks' => 
        array (
        ),
      ),
      'totalQueryDuration' => 
      array (
        'declaringClassName' => 'Illuminate\\Database\\Connection',
        'implementingClassName' => 'Illuminate\\Database\\Connection',
        'name' => 'totalQueryDuration',
        'modifiers' => 2,
        'type' => NULL,
        'default' => 
        array (
          'code' => '0.0',
          'attributes' => 
          array (
            'startLine' => 190,
            'endLine' => 190,
            'startTokenPos' => 364,
            'startFilePos' => 4247,
            'endTokenPos' => 364,
            'endFilePos' => 4249,
          ),
        ),
        'docComment' => '/**
 * The duration of all executed queries in milliseconds.
 *
 * @var float
 */',
        'attributes' => 
        array (
        ),
        'startLine' => 190,
        'endLine' => 190,
        'startColumn' => 5,
        'endColumn' => 40,
        'isPromoted' => false,
        'declaredAtCompileTime' => true,
        'immediateVirtual' => false,
        'immediateHooks' => 
        array (
        ),
      ),
      'queryDurationHandlers' => 
      array (
        'declaringClassName' => 'Illuminate\\Database\\Connection',
        'implementingClassName' => 'Illuminate\\Database\\Connection',
        'name' => 'queryDurationHandlers',
        'modifiers' => 2,
        'type' => NULL,
        'default' => 
        array (
          'code' => '[]',
          'attributes' => 
          array (
            'startLine' => 197,
            'endLine' => 197,
            'startTokenPos' => 375,
            'startFilePos' => 4522,
            'endTokenPos' => 376,
            'endFilePos' => 4523,
          ),
        ),
        'docComment' => '/**
 * All of the registered query duration handlers.
 *
 * @var array{has_run: bool, handler: (callable(\\Illuminate\\Database\\Connection, class-string<\\Illuminate\\Database\\Events\\QueryExecuted>): mixed)}[]
 */',
        'attributes' => 
        array (
        ),
        'startLine' => 197,
        'endLine' => 197,
        'startColumn' => 5,
        'endColumn' => 42,
        'isPromoted' => false,
        'declaredAtCompileTime' => true,
        'immediateVirtual' => false,
        'immediateHooks' => 
        array (
        ),
      ),
      'pretending' => 
      array (
        'declaringClassName' => 'Illuminate\\Database\\Connection',
        'implementingClassName' => 'Illuminate\\Database\\Connection',
        'name' => 'pretending',
        'modifiers' => 2,
        'type' => NULL,
        'default' => 
        array (
          'code' => 'false',
          'attributes' => 
          array (
            'startLine' => 204,
            'endLine' => 204,
            'startTokenPos' => 387,
            'startFilePos' => 4649,
            'endTokenPos' => 387,
            'endFilePos' => 4653,
          ),
        ),
        'docComment' => '/**
 * Indicates if the connection is in a "dry run".
 *
 * @var bool
 */',
        'attributes' => 
        array (
        ),
        'startLine' => 204,
        'endLine' => 204,
        'startColumn' => 5,
        'endColumn' => 34,
        'isPromoted' => false,
        'declaredAtCompileTime' => true,
        'immediateVirtual' => false,
        'immediateHooks' => 
        array (
        ),
      ),
      'beforeStartingTransaction' => 
      array (
        'declaringClassName' => 'Illuminate\\Database\\Connection',
        'implementingClassName' => 'Illuminate\\Database\\Connection',
        'name' => 'beforeStartingTransaction',
        'modifiers' => 2,
        'type' => NULL,
        'default' => 
        array (
          'code' => '[]',
          'attributes' => 
          array (
            'startLine' => 211,
            'endLine' => 211,
            'startTokenPos' => 398,
            'startFilePos' => 4830,
            'endTokenPos' => 399,
            'endFilePos' => 4831,
          ),
        ),
        'docComment' => '/**
 * All of the callbacks that should be invoked before a transaction is started.
 *
 * @var \\Closure[]
 */',
        'attributes' => 
        array (
        ),
        'startLine' => 211,
        'endLine' => 211,
        'startColumn' => 5,
        'endColumn' => 46,
        'isPromoted' => false,
        'declaredAtCompileTime' => true,
        'immediateVirtual' => false,
        'immediateHooks' => 
        array (
        ),
      ),
      'beforeExecutingCallbacks' => 
      array (
        'declaringClassName' => 'Illuminate\\Database\\Connection',
        'implementingClassName' => 'Illuminate\\Database\\Connection',
        'name' => 'beforeExecutingCallbacks',
        'modifiers' => 2,
        'type' => NULL,
        'default' => 
        array (
          'code' => '[]',
          'attributes' => 
          array (
            'startLine' => 218,
            'endLine' => 218,
            'startTokenPos' => 410,
            'startFilePos' => 5059,
            'endTokenPos' => 411,
            'endFilePos' => 5060,
          ),
        ),
        'docComment' => '/**
 * All of the callbacks that should be invoked before a query is executed.
 *
 * @var (\\Closure(string, array, \\Illuminate\\Database\\Connection): mixed)[]
 */',
        'attributes' => 
        array (
        ),
        'startLine' => 218,
        'endLine' => 218,
        'startColumn' => 5,
        'endColumn' => 45,
        'isPromoted' => false,
        'declaredAtCompileTime' => true,
        'immediateVirtual' => false,
        'immediateHooks' => 
        array (
        ),
      ),
      'resolvers' => 
      array (
        'declaringClassName' => 'Illuminate\\Database\\Connection',
        'implementingClassName' => 'Illuminate\\Database\\Connection',
        'name' => 'resolvers',
        'modifiers' => 18,
        'type' => NULL,
        'default' => 
        array (
          'code' => '[]',
          'attributes' => 
          array (
            'startLine' => 225,
            'endLine' => 225,
            'startTokenPos' => 424,
            'startFilePos' => 5177,
            'endTokenPos' => 425,
            'endFilePos' => 5178,
          ),
        ),
        'docComment' => '/**
 * The connection resolvers.
 *
 * @var \\Closure[]
 */',
        'attributes' => 
        array (
        ),
        'startLine' => 225,
        'endLine' => 225,
        'startColumn' => 5,
        'endColumn' => 37,
        'isPromoted' => false,
        'declaredAtCompileTime' => true,
        'immediateVirtual' => false,
        'immediateHooks' => 
        array (
        ),
      ),
      'latestPdoTypeRetrieved' => 
      array (
        'declaringClassName' => 'Illuminate\\Database\\Connection',
        'implementingClassName' => 'Illuminate\\Database\\Connection',
        'name' => 'latestPdoTypeRetrieved',
        'modifiers' => 2,
        'type' => NULL,
        'default' => 
        array (
          'code' => 'null',
          'attributes' => 
          array (
            'startLine' => 232,
            'endLine' => 232,
            'startTokenPos' => 436,
            'startFilePos' => 5335,
            'endTokenPos' => 436,
            'endFilePos' => 5338,
          ),
        ),
        'docComment' => '/**
 * The last retrieved PDO read / write type.
 *
 * @var null|\'read\'|\'write\'|\'direct\'
 */',
        'attributes' => 
        array (
        ),
        'startLine' => 232,
        'endLine' => 232,
        'startColumn' => 5,
        'endColumn' => 45,
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
          'pdo' => 
          array (
            'name' => 'pdo',
            'default' => NULL,
            'type' => NULL,
            'isVariadic' => false,
            'byRef' => false,
            'isPromoted' => false,
            'attributes' => 
            array (
            ),
            'startLine' => 242,
            'endLine' => 242,
            'startColumn' => 33,
            'endColumn' => 36,
            'parameterIndex' => 0,
            'isOptional' => false,
          ),
          'database' => 
          array (
            'name' => 'database',
            'default' => 
            array (
              'code' => '\'\'',
              'attributes' => 
              array (
                'startLine' => 242,
                'endLine' => 242,
                'startTokenPos' => 454,
                'startFilePos' => 5609,
                'endTokenPos' => 454,
                'endFilePos' => 5610,
              ),
            ),
            'type' => NULL,
            'isVariadic' => false,
            'byRef' => false,
            'isPromoted' => false,
            'attributes' => 
            array (
            ),
            'startLine' => 242,
            'endLine' => 242,
            'startColumn' => 39,
            'endColumn' => 52,
            'parameterIndex' => 1,
            'isOptional' => true,
          ),
          'tablePrefix' => 
          array (
            'name' => 'tablePrefix',
            'default' => 
            array (
              'code' => '\'\'',
              'attributes' => 
              array (
                'startLine' => 242,
                'endLine' => 242,
                'startTokenPos' => 461,
                'startFilePos' => 5628,
                'endTokenPos' => 461,
                'endFilePos' => 5629,
              ),
            ),
            'type' => NULL,
            'isVariadic' => false,
            'byRef' => false,
            'isPromoted' => false,
            'attributes' => 
            array (
            ),
            'startLine' => 242,
            'endLine' => 242,
            'startColumn' => 55,
            'endColumn' => 71,
            'parameterIndex' => 2,
            'isOptional' => true,
          ),
          'config' => 
          array (
            'name' => 'config',
            'default' => 
            array (
              'code' => '[]',
              'attributes' => 
              array (
                'startLine' => 242,
                'endLine' => 242,
                'startTokenPos' => 470,
                'startFilePos' => 5648,
                'endTokenPos' => 471,
                'endFilePos' => 5649,
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
            'startLine' => 242,
            'endLine' => 242,
            'startColumn' => 74,
            'endColumn' => 91,
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
 * Create a new database connection instance.
 *
 * @param  \\PDO|(\\Closure(): \\PDO)  $pdo
 * @param  string  $database
 * @param  string  $tablePrefix
 * @param  array  $config
 */',
        'startLine' => 242,
        'endLine' => 261,
        'startColumn' => 5,
        'endColumn' => 5,
        'couldThrow' => false,
        'isClosure' => false,
        'isGenerator' => false,
        'isVariadic' => false,
        'modifiers' => 1,
        'namespace' => 'Illuminate\\Database',
        'declaringClassName' => 'Illuminate\\Database\\Connection',
        'implementingClassName' => 'Illuminate\\Database\\Connection',
        'currentClassName' => 'Illuminate\\Database\\Connection',
        'aliasName' => NULL,
      ),
      'useDefaultQueryGrammar' => 
      array (
        'name' => 'useDefaultQueryGrammar',
        'parameters' => 
        array (
        ),
        'returnsReference' => false,
        'returnType' => NULL,
        'attributes' => 
        array (
        ),
        'docComment' => '/**
 * Set the query grammar to the default implementation.
 *
 * @return void
 */',
        'startLine' => 268,
        'endLine' => 271,
        'startColumn' => 5,
        'endColumn' => 5,
        'couldThrow' => false,
        'isClosure' => false,
        'isGenerator' => false,
        'isVariadic' => false,
        'modifiers' => 1,
        'namespace' => 'Illuminate\\Database',
        'declaringClassName' => 'Illuminate\\Database\\Connection',
        'implementingClassName' => 'Illuminate\\Database\\Connection',
        'currentClassName' => 'Illuminate\\Database\\Connection',
        'aliasName' => NULL,
      ),
      'getDefaultQueryGrammar' => 
      array (
        'name' => 'getDefaultQueryGrammar',
        'parameters' => 
        array (
        ),
        'returnsReference' => false,
        'returnType' => NULL,
        'attributes' => 
        array (
        ),
        'docComment' => '/**
 * Get the default query grammar instance.
 *
 * @return \\Illuminate\\Database\\Query\\Grammars\\Grammar
 */',
        'startLine' => 278,
        'endLine' => 281,
        'startColumn' => 5,
        'endColumn' => 5,
        'couldThrow' => false,
        'isClosure' => false,
        'isGenerator' => false,
        'isVariadic' => false,
        'modifiers' => 2,
        'namespace' => 'Illuminate\\Database',
        'declaringClassName' => 'Illuminate\\Database\\Connection',
        'implementingClassName' => 'Illuminate\\Database\\Connection',
        'currentClassName' => 'Illuminate\\Database\\Connection',
        'aliasName' => NULL,
      ),
      'useDefaultSchemaGrammar' => 
      array (
        'name' => 'useDefaultSchemaGrammar',
        'parameters' => 
        array (
        ),
        'returnsReference' => false,
        'returnType' => NULL,
        'attributes' => 
        array (
        ),
        'docComment' => '/**
 * Set the schema grammar to the default implementation.
 *
 * @return void
 */',
        'startLine' => 288,
        'endLine' => 291,
        'startColumn' => 5,
        'endColumn' => 5,
        'couldThrow' => false,
        'isClosure' => false,
        'isGenerator' => false,
        'isVariadic' => false,
        'modifiers' => 1,
        'namespace' => 'Illuminate\\Database',
        'declaringClassName' => 'Illuminate\\Database\\Connection',
        'implementingClassName' => 'Illuminate\\Database\\Connection',
        'currentClassName' => 'Illuminate\\Database\\Connection',
        'aliasName' => NULL,
      ),
      'getDefaultSchemaGrammar' => 
      array (
        'name' => 'getDefaultSchemaGrammar',
        'parameters' => 
        array (
        ),
        'returnsReference' => false,
        'returnType' => NULL,
        'attributes' => 
        array (
        ),
        'docComment' => '/**
 * Get the default schema grammar instance.
 *
 * @return \\Illuminate\\Database\\Schema\\Grammars\\Grammar|null
 */',
        'startLine' => 298,
        'endLine' => 301,
        'startColumn' => 5,
        'endColumn' => 5,
        'couldThrow' => false,
        'isClosure' => false,
        'isGenerator' => false,
        'isVariadic' => false,
        'modifiers' => 2,
        'namespace' => 'Illuminate\\Database',
        'declaringClassName' => 'Illuminate\\Database\\Connection',
        'implementingClassName' => 'Illuminate\\Database\\Connection',
        'currentClassName' => 'Illuminate\\Database\\Connection',
        'aliasName' => NULL,
      ),
      'useDefaultPostProcessor' => 
      array (
        'name' => 'useDefaultPostProcessor',
        'parameters' => 
        array (
        ),
        'returnsReference' => false,
        'returnType' => NULL,
        'attributes' => 
        array (
        ),
        'docComment' => '/**
 * Set the query post processor to the default implementation.
 *
 * @return void
 */',
        'startLine' => 308,
        'endLine' => 311,
        'startColumn' => 5,
        'endColumn' => 5,
        'couldThrow' => false,
        'isClosure' => false,
        'isGenerator' => false,
        'isVariadic' => false,
        'modifiers' => 1,
        'namespace' => 'Illuminate\\Database',
        'declaringClassName' => 'Illuminate\\Database\\Connection',
        'implementingClassName' => 'Illuminate\\Database\\Connection',
        'currentClassName' => 'Illuminate\\Database\\Connection',
        'aliasName' => NULL,
      ),
      'getDefaultPostProcessor' => 
      array (
        'name' => 'getDefaultPostProcessor',
        'parameters' => 
        array (
        ),
        'returnsReference' => false,
        'returnType' => NULL,
        'attributes' => 
        array (
        ),
        'docComment' => '/**
 * Get the default post processor instance.
 *
 * @return \\Illuminate\\Database\\Query\\Processors\\Processor
 */',
        'startLine' => 318,
        'endLine' => 321,
        'startColumn' => 5,
        'endColumn' => 5,
        'couldThrow' => false,
        'isClosure' => false,
        'isGenerator' => false,
        'isVariadic' => false,
        'modifiers' => 2,
        'namespace' => 'Illuminate\\Database',
        'declaringClassName' => 'Illuminate\\Database\\Connection',
        'implementingClassName' => 'Illuminate\\Database\\Connection',
        'currentClassName' => 'Illuminate\\Database\\Connection',
        'aliasName' => NULL,
      ),
      'getSchemaBuilder' => 
      array (
        'name' => 'getSchemaBuilder',
        'parameters' => 
        array (
        ),
        'returnsReference' => false,
        'returnType' => NULL,
        'attributes' => 
        array (
        ),
        'docComment' => '/**
 * Get a schema builder instance for the connection.
 *
 * @return \\Illuminate\\Database\\Schema\\Builder
 */',
        'startLine' => 328,
        'endLine' => 335,
        'startColumn' => 5,
        'endColumn' => 5,
        'couldThrow' => false,
        'isClosure' => false,
        'isGenerator' => false,
        'isVariadic' => false,
        'modifiers' => 1,
        'namespace' => 'Illuminate\\Database',
        'declaringClassName' => 'Illuminate\\Database\\Connection',
        'implementingClassName' => 'Illuminate\\Database\\Connection',
        'currentClassName' => 'Illuminate\\Database\\Connection',
        'aliasName' => NULL,
      ),
      'table' => 
      array (
        'name' => 'table',
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
            'startLine' => 344,
            'endLine' => 344,
            'startColumn' => 27,
            'endColumn' => 32,
            'parameterIndex' => 0,
            'isOptional' => false,
          ),
          'as' => 
          array (
            'name' => 'as',
            'default' => 
            array (
              'code' => 'null',
              'attributes' => 
              array (
                'startLine' => 344,
                'endLine' => 344,
                'startTokenPos' => 743,
                'startFilePos' => 8359,
                'endTokenPos' => 743,
                'endFilePos' => 8362,
              ),
            ),
            'type' => NULL,
            'isVariadic' => false,
            'byRef' => false,
            'isPromoted' => false,
            'attributes' => 
            array (
            ),
            'startLine' => 344,
            'endLine' => 344,
            'startColumn' => 35,
            'endColumn' => 44,
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
 * Begin a fluent query against a database table.
 *
 * @param  \\Closure|\\Illuminate\\Database\\Query\\Builder|\\Illuminate\\Contracts\\Database\\Query\\Expression|\\UnitEnum|string  $table
 * @param  string|null  $as
 * @return \\Illuminate\\Database\\Query\\Builder
 */',
        'startLine' => 344,
        'endLine' => 347,
        'startColumn' => 5,
        'endColumn' => 5,
        'couldThrow' => false,
        'isClosure' => false,
        'isGenerator' => false,
        'isVariadic' => false,
        'modifiers' => 1,
        'namespace' => 'Illuminate\\Database',
        'declaringClassName' => 'Illuminate\\Database\\Connection',
        'implementingClassName' => 'Illuminate\\Database\\Connection',
        'currentClassName' => 'Illuminate\\Database\\Connection',
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
 * Get a new query builder instance.
 *
 * @return \\Illuminate\\Database\\Query\\Builder
 */',
        'startLine' => 354,
        'endLine' => 359,
        'startColumn' => 5,
        'endColumn' => 5,
        'couldThrow' => false,
        'isClosure' => false,
        'isGenerator' => false,
        'isVariadic' => false,
        'modifiers' => 1,
        'namespace' => 'Illuminate\\Database',
        'declaringClassName' => 'Illuminate\\Database\\Connection',
        'implementingClassName' => 'Illuminate\\Database\\Connection',
        'currentClassName' => 'Illuminate\\Database\\Connection',
        'aliasName' => NULL,
      ),
      'selectOne' => 
      array (
        'name' => 'selectOne',
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
            'startLine' => 369,
            'endLine' => 369,
            'startColumn' => 31,
            'endColumn' => 36,
            'parameterIndex' => 0,
            'isOptional' => false,
          ),
          'bindings' => 
          array (
            'name' => 'bindings',
            'default' => 
            array (
              'code' => '[]',
              'attributes' => 
              array (
                'startLine' => 369,
                'endLine' => 369,
                'startTokenPos' => 825,
                'startFilePos' => 8957,
                'endTokenPos' => 826,
                'endFilePos' => 8958,
              ),
            ),
            'type' => NULL,
            'isVariadic' => false,
            'byRef' => false,
            'isPromoted' => false,
            'attributes' => 
            array (
            ),
            'startLine' => 369,
            'endLine' => 369,
            'startColumn' => 39,
            'endColumn' => 52,
            'parameterIndex' => 1,
            'isOptional' => true,
          ),
          'useReadPdo' => 
          array (
            'name' => 'useReadPdo',
            'default' => 
            array (
              'code' => 'true',
              'attributes' => 
              array (
                'startLine' => 369,
                'endLine' => 369,
                'startTokenPos' => 833,
                'startFilePos' => 8975,
                'endTokenPos' => 833,
                'endFilePos' => 8978,
              ),
            ),
            'type' => NULL,
            'isVariadic' => false,
            'byRef' => false,
            'isPromoted' => false,
            'attributes' => 
            array (
            ),
            'startLine' => 369,
            'endLine' => 369,
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
 * Run a select statement and return a single result.
 *
 * @param  string  $query
 * @param  array  $bindings
 * @param  bool  $useReadPdo
 * @return mixed
 */',
        'startLine' => 369,
        'endLine' => 374,
        'startColumn' => 5,
        'endColumn' => 5,
        'couldThrow' => false,
        'isClosure' => false,
        'isGenerator' => false,
        'isVariadic' => false,
        'modifiers' => 1,
        'namespace' => 'Illuminate\\Database',
        'declaringClassName' => 'Illuminate\\Database\\Connection',
        'implementingClassName' => 'Illuminate\\Database\\Connection',
        'currentClassName' => 'Illuminate\\Database\\Connection',
        'aliasName' => NULL,
      ),
      'scalar' => 
      array (
        'name' => 'scalar',
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
            'startLine' => 386,
            'endLine' => 386,
            'startColumn' => 28,
            'endColumn' => 33,
            'parameterIndex' => 0,
            'isOptional' => false,
          ),
          'bindings' => 
          array (
            'name' => 'bindings',
            'default' => 
            array (
              'code' => '[]',
              'attributes' => 
              array (
                'startLine' => 386,
                'endLine' => 386,
                'startTokenPos' => 881,
                'startFilePos' => 9437,
                'endTokenPos' => 882,
                'endFilePos' => 9438,
              ),
            ),
            'type' => NULL,
            'isVariadic' => false,
            'byRef' => false,
            'isPromoted' => false,
            'attributes' => 
            array (
            ),
            'startLine' => 386,
            'endLine' => 386,
            'startColumn' => 36,
            'endColumn' => 49,
            'parameterIndex' => 1,
            'isOptional' => true,
          ),
          'useReadPdo' => 
          array (
            'name' => 'useReadPdo',
            'default' => 
            array (
              'code' => 'true',
              'attributes' => 
              array (
                'startLine' => 386,
                'endLine' => 386,
                'startTokenPos' => 889,
                'startFilePos' => 9455,
                'endTokenPos' => 889,
                'endFilePos' => 9458,
              ),
            ),
            'type' => NULL,
            'isVariadic' => false,
            'byRef' => false,
            'isPromoted' => false,
            'attributes' => 
            array (
            ),
            'startLine' => 386,
            'endLine' => 386,
            'startColumn' => 52,
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
 * Run a select statement and return the first column of the first row.
 *
 * @param  string  $query
 * @param  array  $bindings
 * @param  bool  $useReadPdo
 * @return mixed
 *
 * @throws \\Illuminate\\Database\\MultipleColumnsSelectedException
 */',
        'startLine' => 386,
        'endLine' => 401,
        'startColumn' => 5,
        'endColumn' => 5,
        'couldThrow' => false,
        'isClosure' => false,
        'isGenerator' => false,
        'isVariadic' => false,
        'modifiers' => 1,
        'namespace' => 'Illuminate\\Database',
        'declaringClassName' => 'Illuminate\\Database\\Connection',
        'implementingClassName' => 'Illuminate\\Database\\Connection',
        'currentClassName' => 'Illuminate\\Database\\Connection',
        'aliasName' => NULL,
      ),
      'selectFromWriteConnection' => 
      array (
        'name' => 'selectFromWriteConnection',
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
            'startLine' => 410,
            'endLine' => 410,
            'startColumn' => 47,
            'endColumn' => 52,
            'parameterIndex' => 0,
            'isOptional' => false,
          ),
          'bindings' => 
          array (
            'name' => 'bindings',
            'default' => 
            array (
              'code' => '[]',
              'attributes' => 
              array (
                'startLine' => 410,
                'endLine' => 410,
                'startTokenPos' => 988,
                'startFilePos' => 10009,
                'endTokenPos' => 989,
                'endFilePos' => 10010,
              ),
            ),
            'type' => NULL,
            'isVariadic' => false,
            'byRef' => false,
            'isPromoted' => false,
            'attributes' => 
            array (
            ),
            'startLine' => 410,
            'endLine' => 410,
            'startColumn' => 55,
            'endColumn' => 68,
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
 * Run a select statement against the database.
 *
 * @param  string  $query
 * @param  array  $bindings
 * @return array
 */',
        'startLine' => 410,
        'endLine' => 413,
        'startColumn' => 5,
        'endColumn' => 5,
        'couldThrow' => false,
        'isClosure' => false,
        'isGenerator' => false,
        'isVariadic' => false,
        'modifiers' => 1,
        'namespace' => 'Illuminate\\Database',
        'declaringClassName' => 'Illuminate\\Database\\Connection',
        'implementingClassName' => 'Illuminate\\Database\\Connection',
        'currentClassName' => 'Illuminate\\Database\\Connection',
        'aliasName' => NULL,
      ),
      'select' => 
      array (
        'name' => 'select',
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
            'startLine' => 424,
            'endLine' => 424,
            'startColumn' => 28,
            'endColumn' => 33,
            'parameterIndex' => 0,
            'isOptional' => false,
          ),
          'bindings' => 
          array (
            'name' => 'bindings',
            'default' => 
            array (
              'code' => '[]',
              'attributes' => 
              array (
                'startLine' => 424,
                'endLine' => 424,
                'startTokenPos' => 1027,
                'startFilePos' => 10354,
                'endTokenPos' => 1028,
                'endFilePos' => 10355,
              ),
            ),
            'type' => NULL,
            'isVariadic' => false,
            'byRef' => false,
            'isPromoted' => false,
            'attributes' => 
            array (
            ),
            'startLine' => 424,
            'endLine' => 424,
            'startColumn' => 36,
            'endColumn' => 49,
            'parameterIndex' => 1,
            'isOptional' => true,
          ),
          'useReadPdo' => 
          array (
            'name' => 'useReadPdo',
            'default' => 
            array (
              'code' => 'true',
              'attributes' => 
              array (
                'startLine' => 424,
                'endLine' => 424,
                'startTokenPos' => 1035,
                'startFilePos' => 10372,
                'endTokenPos' => 1035,
                'endFilePos' => 10375,
              ),
            ),
            'type' => NULL,
            'isVariadic' => false,
            'byRef' => false,
            'isPromoted' => false,
            'attributes' => 
            array (
            ),
            'startLine' => 424,
            'endLine' => 424,
            'startColumn' => 52,
            'endColumn' => 69,
            'parameterIndex' => 2,
            'isOptional' => true,
          ),
          'fetchUsing' => 
          array (
            'name' => 'fetchUsing',
            'default' => 
            array (
              'code' => '[]',
              'attributes' => 
              array (
                'startLine' => 424,
                'endLine' => 424,
                'startTokenPos' => 1044,
                'startFilePos' => 10398,
                'endTokenPos' => 1045,
                'endFilePos' => 10399,
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
            'startLine' => 424,
            'endLine' => 424,
            'startColumn' => 72,
            'endColumn' => 93,
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
 * Run a select statement against the database.
 *
 * @param  string  $query
 * @param  array  $bindings
 * @param  bool  $useReadPdo
 * @param  array  $fetchUsing
 * @return array
 */',
        'startLine' => 424,
        'endLine' => 444,
        'startColumn' => 5,
        'endColumn' => 5,
        'couldThrow' => false,
        'isClosure' => false,
        'isGenerator' => false,
        'isVariadic' => false,
        'modifiers' => 1,
        'namespace' => 'Illuminate\\Database',
        'declaringClassName' => 'Illuminate\\Database\\Connection',
        'implementingClassName' => 'Illuminate\\Database\\Connection',
        'currentClassName' => 'Illuminate\\Database\\Connection',
        'aliasName' => NULL,
      ),
      'selectResultSets' => 
      array (
        'name' => 'selectResultSets',
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
            'startLine' => 455,
            'endLine' => 455,
            'startColumn' => 38,
            'endColumn' => 43,
            'parameterIndex' => 0,
            'isOptional' => false,
          ),
          'bindings' => 
          array (
            'name' => 'bindings',
            'default' => 
            array (
              'code' => '[]',
              'attributes' => 
              array (
                'startLine' => 455,
                'endLine' => 455,
                'startTokenPos' => 1187,
                'startFilePos' => 11490,
                'endTokenPos' => 1188,
                'endFilePos' => 11491,
              ),
            ),
            'type' => NULL,
            'isVariadic' => false,
            'byRef' => false,
            'isPromoted' => false,
            'attributes' => 
            array (
            ),
            'startLine' => 455,
            'endLine' => 455,
            'startColumn' => 46,
            'endColumn' => 59,
            'parameterIndex' => 1,
            'isOptional' => true,
          ),
          'useReadPdo' => 
          array (
            'name' => 'useReadPdo',
            'default' => 
            array (
              'code' => 'true',
              'attributes' => 
              array (
                'startLine' => 455,
                'endLine' => 455,
                'startTokenPos' => 1195,
                'startFilePos' => 11508,
                'endTokenPos' => 1195,
                'endFilePos' => 11511,
              ),
            ),
            'type' => NULL,
            'isVariadic' => false,
            'byRef' => false,
            'isPromoted' => false,
            'attributes' => 
            array (
            ),
            'startLine' => 455,
            'endLine' => 455,
            'startColumn' => 62,
            'endColumn' => 79,
            'parameterIndex' => 2,
            'isOptional' => true,
          ),
          'fetchUsing' => 
          array (
            'name' => 'fetchUsing',
            'default' => 
            array (
              'code' => '[]',
              'attributes' => 
              array (
                'startLine' => 455,
                'endLine' => 455,
                'startTokenPos' => 1204,
                'startFilePos' => 11534,
                'endTokenPos' => 1205,
                'endFilePos' => 11535,
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
            'startLine' => 455,
            'endLine' => 455,
            'startColumn' => 82,
            'endColumn' => 103,
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
 * Run a select statement against the database and returns all of the result sets.
 *
 * @param  string  $query
 * @param  array  $bindings
 * @param  bool  $useReadPdo
 * @param  array  $fetchUsing
 * @return array
 */',
        'startLine' => 455,
        'endLine' => 478,
        'startColumn' => 5,
        'endColumn' => 5,
        'couldThrow' => false,
        'isClosure' => false,
        'isGenerator' => false,
        'isVariadic' => false,
        'modifiers' => 1,
        'namespace' => 'Illuminate\\Database',
        'declaringClassName' => 'Illuminate\\Database\\Connection',
        'implementingClassName' => 'Illuminate\\Database\\Connection',
        'currentClassName' => 'Illuminate\\Database\\Connection',
        'aliasName' => NULL,
      ),
      'cursor' => 
      array (
        'name' => 'cursor',
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
            'startLine' => 489,
            'endLine' => 489,
            'startColumn' => 28,
            'endColumn' => 33,
            'parameterIndex' => 0,
            'isOptional' => false,
          ),
          'bindings' => 
          array (
            'name' => 'bindings',
            'default' => 
            array (
              'code' => '[]',
              'attributes' => 
              array (
                'startLine' => 489,
                'endLine' => 489,
                'startTokenPos' => 1375,
                'startFilePos' => 12491,
                'endTokenPos' => 1376,
                'endFilePos' => 12492,
              ),
            ),
            'type' => NULL,
            'isVariadic' => false,
            'byRef' => false,
            'isPromoted' => false,
            'attributes' => 
            array (
            ),
            'startLine' => 489,
            'endLine' => 489,
            'startColumn' => 36,
            'endColumn' => 49,
            'parameterIndex' => 1,
            'isOptional' => true,
          ),
          'useReadPdo' => 
          array (
            'name' => 'useReadPdo',
            'default' => 
            array (
              'code' => 'true',
              'attributes' => 
              array (
                'startLine' => 489,
                'endLine' => 489,
                'startTokenPos' => 1383,
                'startFilePos' => 12509,
                'endTokenPos' => 1383,
                'endFilePos' => 12512,
              ),
            ),
            'type' => NULL,
            'isVariadic' => false,
            'byRef' => false,
            'isPromoted' => false,
            'attributes' => 
            array (
            ),
            'startLine' => 489,
            'endLine' => 489,
            'startColumn' => 52,
            'endColumn' => 69,
            'parameterIndex' => 2,
            'isOptional' => true,
          ),
          'fetchUsing' => 
          array (
            'name' => 'fetchUsing',
            'default' => 
            array (
              'code' => '[]',
              'attributes' => 
              array (
                'startLine' => 489,
                'endLine' => 489,
                'startTokenPos' => 1392,
                'startFilePos' => 12535,
                'endTokenPos' => 1393,
                'endFilePos' => 12536,
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
            'startLine' => 489,
            'endLine' => 489,
            'startColumn' => 72,
            'endColumn' => 93,
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
 * Run a select statement against the database and returns a generator.
 *
 * @param  string  $query
 * @param  array  $bindings
 * @param  bool  $useReadPdo
 * @param  array  $fetchUsing
 * @return \\Generator<int, \\stdClass>
 */',
        'startLine' => 489,
        'endLine' => 517,
        'startColumn' => 5,
        'endColumn' => 5,
        'couldThrow' => false,
        'isClosure' => false,
        'isGenerator' => true,
        'isVariadic' => false,
        'modifiers' => 1,
        'namespace' => 'Illuminate\\Database',
        'declaringClassName' => 'Illuminate\\Database\\Connection',
        'implementingClassName' => 'Illuminate\\Database\\Connection',
        'currentClassName' => 'Illuminate\\Database\\Connection',
        'aliasName' => NULL,
      ),
      'prepared' => 
      array (
        'name' => 'prepared',
        'parameters' => 
        array (
          'statement' => 
          array (
            'name' => 'statement',
            'default' => NULL,
            'type' => 
            array (
              'class' => 'PHPStan\\BetterReflection\\Reflection\\ReflectionNamedType',
              'data' => 
              array (
                'name' => 'PDOStatement',
                'isIdentifier' => false,
              ),
            ),
            'isVariadic' => false,
            'byRef' => false,
            'isPromoted' => false,
            'attributes' => 
            array (
            ),
            'startLine' => 525,
            'endLine' => 525,
            'startColumn' => 33,
            'endColumn' => 55,
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
 * Configure the PDO prepared statement.
 *
 * @param  \\PDOStatement  $statement
 * @return \\PDOStatement
 */',
        'startLine' => 525,
        'endLine' => 532,
        'startColumn' => 5,
        'endColumn' => 5,
        'couldThrow' => false,
        'isClosure' => false,
        'isGenerator' => false,
        'isVariadic' => false,
        'modifiers' => 2,
        'namespace' => 'Illuminate\\Database',
        'declaringClassName' => 'Illuminate\\Database\\Connection',
        'implementingClassName' => 'Illuminate\\Database\\Connection',
        'currentClassName' => 'Illuminate\\Database\\Connection',
        'aliasName' => NULL,
      ),
      'getPdoForSelect' => 
      array (
        'name' => 'getPdoForSelect',
        'parameters' => 
        array (
          'useReadPdo' => 
          array (
            'name' => 'useReadPdo',
            'default' => 
            array (
              'code' => 'true',
              'attributes' => 
              array (
                'startLine' => 540,
                'endLine' => 540,
                'startTokenPos' => 1605,
                'startFilePos' => 14204,
                'endTokenPos' => 1605,
                'endFilePos' => 14207,
              ),
            ),
            'type' => NULL,
            'isVariadic' => false,
            'byRef' => false,
            'isPromoted' => false,
            'attributes' => 
            array (
            ),
            'startLine' => 540,
            'endLine' => 540,
            'startColumn' => 40,
            'endColumn' => 57,
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
 * Get the PDO connection to use for a select query.
 *
 * @param  bool  $useReadPdo
 * @return \\PDO
 */',
        'startLine' => 540,
        'endLine' => 543,
        'startColumn' => 5,
        'endColumn' => 5,
        'couldThrow' => false,
        'isClosure' => false,
        'isGenerator' => false,
        'isVariadic' => false,
        'modifiers' => 2,
        'namespace' => 'Illuminate\\Database',
        'declaringClassName' => 'Illuminate\\Database\\Connection',
        'implementingClassName' => 'Illuminate\\Database\\Connection',
        'currentClassName' => 'Illuminate\\Database\\Connection',
        'aliasName' => NULL,
      ),
      'insert' => 
      array (
        'name' => 'insert',
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
            'startLine' => 552,
            'endLine' => 552,
            'startColumn' => 28,
            'endColumn' => 33,
            'parameterIndex' => 0,
            'isOptional' => false,
          ),
          'bindings' => 
          array (
            'name' => 'bindings',
            'default' => 
            array (
              'code' => '[]',
              'attributes' => 
              array (
                'startLine' => 552,
                'endLine' => 552,
                'startTokenPos' => 1648,
                'startFilePos' => 14496,
                'endTokenPos' => 1649,
                'endFilePos' => 14497,
              ),
            ),
            'type' => NULL,
            'isVariadic' => false,
            'byRef' => false,
            'isPromoted' => false,
            'attributes' => 
            array (
            ),
            'startLine' => 552,
            'endLine' => 552,
            'startColumn' => 36,
            'endColumn' => 49,
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
 * Run an insert statement against the database.
 *
 * @param  string  $query
 * @param  array  $bindings
 * @return bool
 */',
        'startLine' => 552,
        'endLine' => 555,
        'startColumn' => 5,
        'endColumn' => 5,
        'couldThrow' => false,
        'isClosure' => false,
        'isGenerator' => false,
        'isVariadic' => false,
        'modifiers' => 1,
        'namespace' => 'Illuminate\\Database',
        'declaringClassName' => 'Illuminate\\Database\\Connection',
        'implementingClassName' => 'Illuminate\\Database\\Connection',
        'currentClassName' => 'Illuminate\\Database\\Connection',
        'aliasName' => NULL,
      ),
      'update' => 
      array (
        'name' => 'update',
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
            'startLine' => 564,
            'endLine' => 564,
            'startColumn' => 28,
            'endColumn' => 33,
            'parameterIndex' => 0,
            'isOptional' => false,
          ),
          'bindings' => 
          array (
            'name' => 'bindings',
            'default' => 
            array (
              'code' => '[]',
              'attributes' => 
              array (
                'startLine' => 564,
                'endLine' => 564,
                'startTokenPos' => 1684,
                'startFilePos' => 14769,
                'endTokenPos' => 1685,
                'endFilePos' => 14770,
              ),
            ),
            'type' => NULL,
            'isVariadic' => false,
            'byRef' => false,
            'isPromoted' => false,
            'attributes' => 
            array (
            ),
            'startLine' => 564,
            'endLine' => 564,
            'startColumn' => 36,
            'endColumn' => 49,
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
 * Run an update statement against the database.
 *
 * @param  string  $query
 * @param  array  $bindings
 * @return int
 */',
        'startLine' => 564,
        'endLine' => 567,
        'startColumn' => 5,
        'endColumn' => 5,
        'couldThrow' => false,
        'isClosure' => false,
        'isGenerator' => false,
        'isVariadic' => false,
        'modifiers' => 1,
        'namespace' => 'Illuminate\\Database',
        'declaringClassName' => 'Illuminate\\Database\\Connection',
        'implementingClassName' => 'Illuminate\\Database\\Connection',
        'currentClassName' => 'Illuminate\\Database\\Connection',
        'aliasName' => NULL,
      ),
      'delete' => 
      array (
        'name' => 'delete',
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
            'startLine' => 576,
            'endLine' => 576,
            'startColumn' => 28,
            'endColumn' => 33,
            'parameterIndex' => 0,
            'isOptional' => false,
          ),
          'bindings' => 
          array (
            'name' => 'bindings',
            'default' => 
            array (
              'code' => '[]',
              'attributes' => 
              array (
                'startLine' => 576,
                'endLine' => 576,
                'startTokenPos' => 1720,
                'startFilePos' => 15050,
                'endTokenPos' => 1721,
                'endFilePos' => 15051,
              ),
            ),
            'type' => NULL,
            'isVariadic' => false,
            'byRef' => false,
            'isPromoted' => false,
            'attributes' => 
            array (
            ),
            'startLine' => 576,
            'endLine' => 576,
            'startColumn' => 36,
            'endColumn' => 49,
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
 * Run a delete statement against the database.
 *
 * @param  string  $query
 * @param  array  $bindings
 * @return int
 */',
        'startLine' => 576,
        'endLine' => 579,
        'startColumn' => 5,
        'endColumn' => 5,
        'couldThrow' => false,
        'isClosure' => false,
        'isGenerator' => false,
        'isVariadic' => false,
        'modifiers' => 1,
        'namespace' => 'Illuminate\\Database',
        'declaringClassName' => 'Illuminate\\Database\\Connection',
        'implementingClassName' => 'Illuminate\\Database\\Connection',
        'currentClassName' => 'Illuminate\\Database\\Connection',
        'aliasName' => NULL,
      ),
      'statement' => 
      array (
        'name' => 'statement',
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
            'startLine' => 588,
            'endLine' => 588,
            'startColumn' => 31,
            'endColumn' => 36,
            'parameterIndex' => 0,
            'isOptional' => false,
          ),
          'bindings' => 
          array (
            'name' => 'bindings',
            'default' => 
            array (
              'code' => '[]',
              'attributes' => 
              array (
                'startLine' => 588,
                'endLine' => 588,
                'startTokenPos' => 1756,
                'startFilePos' => 15346,
                'endTokenPos' => 1757,
                'endFilePos' => 15347,
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
            'startColumn' => 39,
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
 * Execute an SQL statement and return the boolean result.
 *
 * @param  string  $query
 * @param  array  $bindings
 * @return bool
 */',
        'startLine' => 588,
        'endLine' => 603,
        'startColumn' => 5,
        'endColumn' => 5,
        'couldThrow' => false,
        'isClosure' => false,
        'isGenerator' => false,
        'isVariadic' => false,
        'modifiers' => 1,
        'namespace' => 'Illuminate\\Database',
        'declaringClassName' => 'Illuminate\\Database\\Connection',
        'implementingClassName' => 'Illuminate\\Database\\Connection',
        'currentClassName' => 'Illuminate\\Database\\Connection',
        'aliasName' => NULL,
      ),
      'affectingStatement' => 
      array (
        'name' => 'affectingStatement',
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
            'startLine' => 612,
            'endLine' => 612,
            'startColumn' => 40,
            'endColumn' => 45,
            'parameterIndex' => 0,
            'isOptional' => false,
          ),
          'bindings' => 
          array (
            'name' => 'bindings',
            'default' => 
            array (
              'code' => '[]',
              'attributes' => 
              array (
                'startLine' => 612,
                'endLine' => 612,
                'startTokenPos' => 1873,
                'startFilePos' => 15990,
                'endTokenPos' => 1874,
                'endFilePos' => 15991,
              ),
            ),
            'type' => NULL,
            'isVariadic' => false,
            'byRef' => false,
            'isPromoted' => false,
            'attributes' => 
            array (
            ),
            'startLine' => 612,
            'endLine' => 612,
            'startColumn' => 48,
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
 * Run an SQL statement and get the number of rows affected.
 *
 * @param  string  $query
 * @param  array  $bindings
 * @return int
 */',
        'startLine' => 612,
        'endLine' => 634,
        'startColumn' => 5,
        'endColumn' => 5,
        'couldThrow' => false,
        'isClosure' => false,
        'isGenerator' => false,
        'isVariadic' => false,
        'modifiers' => 1,
        'namespace' => 'Illuminate\\Database',
        'declaringClassName' => 'Illuminate\\Database\\Connection',
        'implementingClassName' => 'Illuminate\\Database\\Connection',
        'currentClassName' => 'Illuminate\\Database\\Connection',
        'aliasName' => NULL,
      ),
      'unprepared' => 
      array (
        'name' => 'unprepared',
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
            'startLine' => 642,
            'endLine' => 642,
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
 * Run a raw, unprepared query against the PDO connection.
 *
 * @param  literal-string  $query
 * @return bool
 */',
        'startLine' => 642,
        'endLine' => 655,
        'startColumn' => 5,
        'endColumn' => 5,
        'couldThrow' => false,
        'isClosure' => false,
        'isGenerator' => false,
        'isVariadic' => false,
        'modifiers' => 1,
        'namespace' => 'Illuminate\\Database',
        'declaringClassName' => 'Illuminate\\Database\\Connection',
        'implementingClassName' => 'Illuminate\\Database\\Connection',
        'currentClassName' => 'Illuminate\\Database\\Connection',
        'aliasName' => NULL,
      ),
      'threadCount' => 
      array (
        'name' => 'threadCount',
        'parameters' => 
        array (
        ),
        'returnsReference' => false,
        'returnType' => NULL,
        'attributes' => 
        array (
        ),
        'docComment' => '/**
 * Get the number of open connections for the database.
 *
 * @return int|null
 */',
        'startLine' => 662,
        'endLine' => 667,
        'startColumn' => 5,
        'endColumn' => 5,
        'couldThrow' => false,
        'isClosure' => false,
        'isGenerator' => false,
        'isVariadic' => false,
        'modifiers' => 1,
        'namespace' => 'Illuminate\\Database',
        'declaringClassName' => 'Illuminate\\Database\\Connection',
        'implementingClassName' => 'Illuminate\\Database\\Connection',
        'currentClassName' => 'Illuminate\\Database\\Connection',
        'aliasName' => NULL,
      ),
      'pretend' => 
      array (
        'name' => 'pretend',
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
            'startLine' => 675,
            'endLine' => 675,
            'startColumn' => 29,
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
 * Execute the given callback in "dry run" mode.
 *
 * @param  (\\Closure(\\Illuminate\\Database\\Connection): mixed)  $callback
 * @return array{query: string, bindings: array, time: float|null}[]
 */',
        'startLine' => 675,
        'endLine' => 691,
        'startColumn' => 5,
        'endColumn' => 5,
        'couldThrow' => false,
        'isClosure' => false,
        'isGenerator' => false,
        'isVariadic' => false,
        'modifiers' => 1,
        'namespace' => 'Illuminate\\Database',
        'declaringClassName' => 'Illuminate\\Database\\Connection',
        'implementingClassName' => 'Illuminate\\Database\\Connection',
        'currentClassName' => 'Illuminate\\Database\\Connection',
        'aliasName' => NULL,
      ),
      'withoutPretending' => 
      array (
        'name' => 'withoutPretending',
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
            'startLine' => 701,
            'endLine' => 701,
            'startColumn' => 39,
            'endColumn' => 55,
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
 * Execute the given callback without "pretending".
 *
 * @template TReturn
 *
 * @param  \\Closure(): TReturn  $callback
 * @return TReturn
 */',
        'startLine' => 701,
        'endLine' => 714,
        'startColumn' => 5,
        'endColumn' => 5,
        'couldThrow' => false,
        'isClosure' => false,
        'isGenerator' => false,
        'isVariadic' => false,
        'modifiers' => 1,
        'namespace' => 'Illuminate\\Database',
        'declaringClassName' => 'Illuminate\\Database\\Connection',
        'implementingClassName' => 'Illuminate\\Database\\Connection',
        'currentClassName' => 'Illuminate\\Database\\Connection',
        'aliasName' => NULL,
      ),
      'withFreshQueryLog' => 
      array (
        'name' => 'withFreshQueryLog',
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
            'startLine' => 722,
            'endLine' => 722,
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
 * Execute the given callback in "dry run" mode.
 *
 * @param  (\\Closure(): array{query: string, bindings: array, time: float|null}[])  $callback
 * @return array{query: string, bindings: array, time: float|null}[]
 */',
        'startLine' => 722,
        'endLine' => 741,
        'startColumn' => 5,
        'endColumn' => 5,
        'couldThrow' => false,
        'isClosure' => false,
        'isGenerator' => false,
        'isVariadic' => false,
        'modifiers' => 2,
        'namespace' => 'Illuminate\\Database',
        'declaringClassName' => 'Illuminate\\Database\\Connection',
        'implementingClassName' => 'Illuminate\\Database\\Connection',
        'currentClassName' => 'Illuminate\\Database\\Connection',
        'aliasName' => NULL,
      ),
      'bindValues' => 
      array (
        'name' => 'bindValues',
        'parameters' => 
        array (
          'statement' => 
          array (
            'name' => 'statement',
            'default' => NULL,
            'type' => NULL,
            'isVariadic' => false,
            'byRef' => false,
            'isPromoted' => false,
            'attributes' => 
            array (
            ),
            'startLine' => 750,
            'endLine' => 750,
            'startColumn' => 32,
            'endColumn' => 41,
            'parameterIndex' => 0,
            'isOptional' => false,
          ),
          'bindings' => 
          array (
            'name' => 'bindings',
            'default' => NULL,
            'type' => NULL,
            'isVariadic' => false,
            'byRef' => false,
            'isPromoted' => false,
            'attributes' => 
            array (
            ),
            'startLine' => 750,
            'endLine' => 750,
            'startColumn' => 44,
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
 * Bind values to their parameters in the given statement.
 *
 * @param  \\PDOStatement  $statement
 * @param  array  $bindings
 * @return void
 */',
        'startLine' => 750,
        'endLine' => 763,
        'startColumn' => 5,
        'endColumn' => 5,
        'couldThrow' => false,
        'isClosure' => false,
        'isGenerator' => false,
        'isVariadic' => false,
        'modifiers' => 1,
        'namespace' => 'Illuminate\\Database',
        'declaringClassName' => 'Illuminate\\Database\\Connection',
        'implementingClassName' => 'Illuminate\\Database\\Connection',
        'currentClassName' => 'Illuminate\\Database\\Connection',
        'aliasName' => NULL,
      ),
      'prepareBindings' => 
      array (
        'name' => 'prepareBindings',
        'parameters' => 
        array (
          'bindings' => 
          array (
            'name' => 'bindings',
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
            'startLine' => 771,
            'endLine' => 771,
            'startColumn' => 37,
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
 * Prepare the query bindings for execution.
 *
 * @param  array  $bindings
 * @return array
 */',
        'startLine' => 771,
        'endLine' => 787,
        'startColumn' => 5,
        'endColumn' => 5,
        'couldThrow' => false,
        'isClosure' => false,
        'isGenerator' => false,
        'isVariadic' => false,
        'modifiers' => 1,
        'namespace' => 'Illuminate\\Database',
        'declaringClassName' => 'Illuminate\\Database\\Connection',
        'implementingClassName' => 'Illuminate\\Database\\Connection',
        'currentClassName' => 'Illuminate\\Database\\Connection',
        'aliasName' => NULL,
      ),
      'run' => 
      array (
        'name' => 'run',
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
            'startLine' => 799,
            'endLine' => 799,
            'startColumn' => 28,
            'endColumn' => 33,
            'parameterIndex' => 0,
            'isOptional' => false,
          ),
          'bindings' => 
          array (
            'name' => 'bindings',
            'default' => NULL,
            'type' => NULL,
            'isVariadic' => false,
            'byRef' => false,
            'isPromoted' => false,
            'attributes' => 
            array (
            ),
            'startLine' => 799,
            'endLine' => 799,
            'startColumn' => 36,
            'endColumn' => 44,
            'parameterIndex' => 1,
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
            'startLine' => 799,
            'endLine' => 799,
            'startColumn' => 47,
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
 * Run a SQL statement and log its execution context.
 *
 * @param  string  $query
 * @param  array  $bindings
 * @param  \\Closure  $callback
 * @return mixed
 *
 * @throws \\Illuminate\\Database\\QueryException
 */',
        'startLine' => 799,
        'endLine' => 828,
        'startColumn' => 5,
        'endColumn' => 5,
        'couldThrow' => false,
        'isClosure' => false,
        'isGenerator' => false,
        'isVariadic' => false,
        'modifiers' => 2,
        'namespace' => 'Illuminate\\Database',
        'declaringClassName' => 'Illuminate\\Database\\Connection',
        'implementingClassName' => 'Illuminate\\Database\\Connection',
        'currentClassName' => 'Illuminate\\Database\\Connection',
        'aliasName' => NULL,
      ),
      'runQueryCallback' => 
      array (
        'name' => 'runQueryCallback',
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
            'startLine' => 840,
            'endLine' => 840,
            'startColumn' => 41,
            'endColumn' => 46,
            'parameterIndex' => 0,
            'isOptional' => false,
          ),
          'bindings' => 
          array (
            'name' => 'bindings',
            'default' => NULL,
            'type' => NULL,
            'isVariadic' => false,
            'byRef' => false,
            'isPromoted' => false,
            'attributes' => 
            array (
            ),
            'startLine' => 840,
            'endLine' => 840,
            'startColumn' => 49,
            'endColumn' => 57,
            'parameterIndex' => 1,
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
            'startLine' => 840,
            'endLine' => 840,
            'startColumn' => 60,
            'endColumn' => 76,
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
 * Run a SQL statement.
 *
 * @param  string  $query
 * @param  array  $bindings
 * @param  \\Closure  $callback
 * @return mixed
 *
 * @throws \\Illuminate\\Database\\QueryException
 */',
        'startLine' => 840,
        'endLine' => 874,
        'startColumn' => 5,
        'endColumn' => 5,
        'couldThrow' => false,
        'isClosure' => false,
        'isGenerator' => false,
        'isVariadic' => false,
        'modifiers' => 2,
        'namespace' => 'Illuminate\\Database',
        'declaringClassName' => 'Illuminate\\Database\\Connection',
        'implementingClassName' => 'Illuminate\\Database\\Connection',
        'currentClassName' => 'Illuminate\\Database\\Connection',
        'aliasName' => NULL,
      ),
      'isUniqueConstraintError' => 
      array (
        'name' => 'isUniqueConstraintError',
        'parameters' => 
        array (
          'exception' => 
          array (
            'name' => 'exception',
            'default' => NULL,
            'type' => 
            array (
              'class' => 'PHPStan\\BetterReflection\\Reflection\\ReflectionNamedType',
              'data' => 
              array (
                'name' => 'Exception',
                'isIdentifier' => false,
              ),
            ),
            'isVariadic' => false,
            'byRef' => false,
            'isPromoted' => false,
            'attributes' => 
            array (
            ),
            'startLine' => 882,
            'endLine' => 882,
            'startColumn' => 48,
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
 * Determine if the given database exception was caused by a unique constraint violation.
 *
 * @param  \\Exception  $exception
 * @return bool
 */',
        'startLine' => 882,
        'endLine' => 885,
        'startColumn' => 5,
        'endColumn' => 5,
        'couldThrow' => false,
        'isClosure' => false,
        'isGenerator' => false,
        'isVariadic' => false,
        'modifiers' => 2,
        'namespace' => 'Illuminate\\Database',
        'declaringClassName' => 'Illuminate\\Database\\Connection',
        'implementingClassName' => 'Illuminate\\Database\\Connection',
        'currentClassName' => 'Illuminate\\Database\\Connection',
        'aliasName' => NULL,
      ),
      'parseUniqueConstraintViolation' => 
      array (
        'name' => 'parseUniqueConstraintViolation',
        'parameters' => 
        array (
          'exception' => 
          array (
            'name' => 'exception',
            'default' => NULL,
            'type' => 
            array (
              'class' => 'PHPStan\\BetterReflection\\Reflection\\ReflectionNamedType',
              'data' => 
              array (
                'name' => 'Exception',
                'isIdentifier' => false,
              ),
            ),
            'isVariadic' => false,
            'byRef' => false,
            'isPromoted' => false,
            'attributes' => 
            array (
            ),
            'startLine' => 893,
            'endLine' => 893,
            'startColumn' => 55,
            'endColumn' => 74,
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
            'name' => 'array',
            'isIdentifier' => true,
          ),
        ),
        'attributes' => 
        array (
        ),
        'docComment' => '/**
 * Extract the index and columns that caused a unique constraint violation.
 *
 * @param  Exception  $exception
 * @return array{index: string|null, columns: list<string>}
 */',
        'startLine' => 893,
        'endLine' => 896,
        'startColumn' => 5,
        'endColumn' => 5,
        'couldThrow' => false,
        'isClosure' => false,
        'isGenerator' => false,
        'isVariadic' => false,
        'modifiers' => 2,
        'namespace' => 'Illuminate\\Database',
        'declaringClassName' => 'Illuminate\\Database\\Connection',
        'implementingClassName' => 'Illuminate\\Database\\Connection',
        'currentClassName' => 'Illuminate\\Database\\Connection',
        'aliasName' => NULL,
      ),
      'logQuery' => 
      array (
        'name' => 'logQuery',
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
            'startLine' => 906,
            'endLine' => 906,
            'startColumn' => 30,
            'endColumn' => 35,
            'parameterIndex' => 0,
            'isOptional' => false,
          ),
          'bindings' => 
          array (
            'name' => 'bindings',
            'default' => NULL,
            'type' => NULL,
            'isVariadic' => false,
            'byRef' => false,
            'isPromoted' => false,
            'attributes' => 
            array (
            ),
            'startLine' => 906,
            'endLine' => 906,
            'startColumn' => 38,
            'endColumn' => 46,
            'parameterIndex' => 1,
            'isOptional' => false,
          ),
          'time' => 
          array (
            'name' => 'time',
            'default' => 
            array (
              'code' => 'null',
              'attributes' => 
              array (
                'startLine' => 906,
                'endLine' => 906,
                'startTokenPos' => 3040,
                'startFilePos' => 25379,
                'endTokenPos' => 3040,
                'endFilePos' => 25382,
              ),
            ),
            'type' => NULL,
            'isVariadic' => false,
            'byRef' => false,
            'isPromoted' => false,
            'attributes' => 
            array (
            ),
            'startLine' => 906,
            'endLine' => 906,
            'startColumn' => 49,
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
 * Log a query in the connection\'s query log.
 *
 * @param  string  $query
 * @param  array  $bindings
 * @param  float|null  $time
 * @return void
 */',
        'startLine' => 906,
        'endLine' => 921,
        'startColumn' => 5,
        'endColumn' => 5,
        'couldThrow' => false,
        'isClosure' => false,
        'isGenerator' => false,
        'isVariadic' => false,
        'modifiers' => 1,
        'namespace' => 'Illuminate\\Database',
        'declaringClassName' => 'Illuminate\\Database\\Connection',
        'implementingClassName' => 'Illuminate\\Database\\Connection',
        'currentClassName' => 'Illuminate\\Database\\Connection',
        'aliasName' => NULL,
      ),
      'getElapsedTime' => 
      array (
        'name' => 'getElapsedTime',
        'parameters' => 
        array (
          'start' => 
          array (
            'name' => 'start',
            'default' => NULL,
            'type' => NULL,
            'isVariadic' => false,
            'byRef' => false,
            'isPromoted' => false,
            'attributes' => 
            array (
            ),
            'startLine' => 929,
            'endLine' => 929,
            'startColumn' => 39,
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
 * Get the elapsed time in milliseconds since a given starting point.
 *
 * @param  float  $start
 * @return float
 */',
        'startLine' => 929,
        'endLine' => 932,
        'startColumn' => 5,
        'endColumn' => 5,
        'couldThrow' => false,
        'isClosure' => false,
        'isGenerator' => false,
        'isVariadic' => false,
        'modifiers' => 2,
        'namespace' => 'Illuminate\\Database',
        'declaringClassName' => 'Illuminate\\Database\\Connection',
        'implementingClassName' => 'Illuminate\\Database\\Connection',
        'currentClassName' => 'Illuminate\\Database\\Connection',
        'aliasName' => NULL,
      ),
      'whenQueryingForLongerThan' => 
      array (
        'name' => 'whenQueryingForLongerThan',
        'parameters' => 
        array (
          'threshold' => 
          array (
            'name' => 'threshold',
            'default' => NULL,
            'type' => NULL,
            'isVariadic' => false,
            'byRef' => false,
            'isPromoted' => false,
            'attributes' => 
            array (
            ),
            'startLine' => 941,
            'endLine' => 941,
            'startColumn' => 47,
            'endColumn' => 56,
            'parameterIndex' => 0,
            'isOptional' => false,
          ),
          'handler' => 
          array (
            'name' => 'handler',
            'default' => NULL,
            'type' => NULL,
            'isVariadic' => false,
            'byRef' => false,
            'isPromoted' => false,
            'attributes' => 
            array (
            ),
            'startLine' => 941,
            'endLine' => 941,
            'startColumn' => 59,
            'endColumn' => 66,
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
 * Register a callback to be invoked when the connection queries for longer than a given amount of time.
 *
 * @param  \\DateTimeInterface|\\Carbon\\CarbonInterval|float|int  $threshold
 * @param  (callable(\\Illuminate\\Database\\Connection, \\Illuminate\\Database\\Events\\QueryExecuted): mixed)  $handler
 * @return void
 */',
        'startLine' => 941,
        'endLine' => 965,
        'startColumn' => 5,
        'endColumn' => 5,
        'couldThrow' => false,
        'isClosure' => false,
        'isGenerator' => false,
        'isVariadic' => false,
        'modifiers' => 1,
        'namespace' => 'Illuminate\\Database',
        'declaringClassName' => 'Illuminate\\Database\\Connection',
        'implementingClassName' => 'Illuminate\\Database\\Connection',
        'currentClassName' => 'Illuminate\\Database\\Connection',
        'aliasName' => NULL,
      ),
      'allowQueryDurationHandlersToRunAgain' => 
      array (
        'name' => 'allowQueryDurationHandlersToRunAgain',
        'parameters' => 
        array (
        ),
        'returnsReference' => false,
        'returnType' => NULL,
        'attributes' => 
        array (
        ),
        'docComment' => '/**
 * Allow all the query duration handlers to run again, even if they have already run.
 *
 * @return void
 */',
        'startLine' => 972,
        'endLine' => 977,
        'startColumn' => 5,
        'endColumn' => 5,
        'couldThrow' => false,
        'isClosure' => false,
        'isGenerator' => false,
        'isVariadic' => false,
        'modifiers' => 1,
        'namespace' => 'Illuminate\\Database',
        'declaringClassName' => 'Illuminate\\Database\\Connection',
        'implementingClassName' => 'Illuminate\\Database\\Connection',
        'currentClassName' => 'Illuminate\\Database\\Connection',
        'aliasName' => NULL,
      ),
      'totalQueryDuration' => 
      array (
        'name' => 'totalQueryDuration',
        'parameters' => 
        array (
        ),
        'returnsReference' => false,
        'returnType' => NULL,
        'attributes' => 
        array (
        ),
        'docComment' => '/**
 * Get the duration of all run queries in milliseconds.
 *
 * @return float
 */',
        'startLine' => 984,
        'endLine' => 987,
        'startColumn' => 5,
        'endColumn' => 5,
        'couldThrow' => false,
        'isClosure' => false,
        'isGenerator' => false,
        'isVariadic' => false,
        'modifiers' => 1,
        'namespace' => 'Illuminate\\Database',
        'declaringClassName' => 'Illuminate\\Database\\Connection',
        'implementingClassName' => 'Illuminate\\Database\\Connection',
        'currentClassName' => 'Illuminate\\Database\\Connection',
        'aliasName' => NULL,
      ),
      'resetTotalQueryDuration' => 
      array (
        'name' => 'resetTotalQueryDuration',
        'parameters' => 
        array (
        ),
        'returnsReference' => false,
        'returnType' => NULL,
        'attributes' => 
        array (
        ),
        'docComment' => '/**
 * Reset the duration of all run queries.
 *
 * @return void
 */',
        'startLine' => 994,
        'endLine' => 997,
        'startColumn' => 5,
        'endColumn' => 5,
        'couldThrow' => false,
        'isClosure' => false,
        'isGenerator' => false,
        'isVariadic' => false,
        'modifiers' => 1,
        'namespace' => 'Illuminate\\Database',
        'declaringClassName' => 'Illuminate\\Database\\Connection',
        'implementingClassName' => 'Illuminate\\Database\\Connection',
        'currentClassName' => 'Illuminate\\Database\\Connection',
        'aliasName' => NULL,
      ),
      'handleQueryException' => 
      array (
        'name' => 'handleQueryException',
        'parameters' => 
        array (
          'e' => 
          array (
            'name' => 'e',
            'default' => NULL,
            'type' => 
            array (
              'class' => 'PHPStan\\BetterReflection\\Reflection\\ReflectionNamedType',
              'data' => 
              array (
                'name' => 'Illuminate\\Database\\QueryException',
                'isIdentifier' => false,
              ),
            ),
            'isVariadic' => false,
            'byRef' => false,
            'isPromoted' => false,
            'attributes' => 
            array (
            ),
            'startLine' => 1010,
            'endLine' => 1010,
            'startColumn' => 45,
            'endColumn' => 61,
            'parameterIndex' => 0,
            'isOptional' => false,
          ),
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
            'startLine' => 1010,
            'endLine' => 1010,
            'startColumn' => 64,
            'endColumn' => 69,
            'parameterIndex' => 1,
            'isOptional' => false,
          ),
          'bindings' => 
          array (
            'name' => 'bindings',
            'default' => NULL,
            'type' => NULL,
            'isVariadic' => false,
            'byRef' => false,
            'isPromoted' => false,
            'attributes' => 
            array (
            ),
            'startLine' => 1010,
            'endLine' => 1010,
            'startColumn' => 72,
            'endColumn' => 80,
            'parameterIndex' => 2,
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
            'startLine' => 1010,
            'endLine' => 1010,
            'startColumn' => 83,
            'endColumn' => 99,
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
 * Handle a query exception.
 *
 * @param  \\Illuminate\\Database\\QueryException  $e
 * @param  string  $query
 * @param  array  $bindings
 * @param  \\Closure  $callback
 * @return mixed
 *
 * @throws \\Illuminate\\Database\\QueryException
 */',
        'startLine' => 1010,
        'endLine' => 1019,
        'startColumn' => 5,
        'endColumn' => 5,
        'couldThrow' => false,
        'isClosure' => false,
        'isGenerator' => false,
        'isVariadic' => false,
        'modifiers' => 2,
        'namespace' => 'Illuminate\\Database',
        'declaringClassName' => 'Illuminate\\Database\\Connection',
        'implementingClassName' => 'Illuminate\\Database\\Connection',
        'currentClassName' => 'Illuminate\\Database\\Connection',
        'aliasName' => NULL,
      ),
      'tryAgainIfCausedByLostConnection' => 
      array (
        'name' => 'tryAgainIfCausedByLostConnection',
        'parameters' => 
        array (
          'e' => 
          array (
            'name' => 'e',
            'default' => NULL,
            'type' => 
            array (
              'class' => 'PHPStan\\BetterReflection\\Reflection\\ReflectionNamedType',
              'data' => 
              array (
                'name' => 'Illuminate\\Database\\QueryException',
                'isIdentifier' => false,
              ),
            ),
            'isVariadic' => false,
            'byRef' => false,
            'isPromoted' => false,
            'attributes' => 
            array (
            ),
            'startLine' => 1032,
            'endLine' => 1032,
            'startColumn' => 57,
            'endColumn' => 73,
            'parameterIndex' => 0,
            'isOptional' => false,
          ),
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
            'startLine' => 1032,
            'endLine' => 1032,
            'startColumn' => 76,
            'endColumn' => 81,
            'parameterIndex' => 1,
            'isOptional' => false,
          ),
          'bindings' => 
          array (
            'name' => 'bindings',
            'default' => NULL,
            'type' => NULL,
            'isVariadic' => false,
            'byRef' => false,
            'isPromoted' => false,
            'attributes' => 
            array (
            ),
            'startLine' => 1032,
            'endLine' => 1032,
            'startColumn' => 84,
            'endColumn' => 92,
            'parameterIndex' => 2,
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
            'startLine' => 1032,
            'endLine' => 1032,
            'startColumn' => 95,
            'endColumn' => 111,
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
 * Handle a query exception that occurred during query execution.
 *
 * @param  \\Illuminate\\Database\\QueryException  $e
 * @param  string  $query
 * @param  array  $bindings
 * @param  \\Closure  $callback
 * @return mixed
 *
 * @throws \\Illuminate\\Database\\QueryException
 */',
        'startLine' => 1032,
        'endLine' => 1041,
        'startColumn' => 5,
        'endColumn' => 5,
        'couldThrow' => false,
        'isClosure' => false,
        'isGenerator' => false,
        'isVariadic' => false,
        'modifiers' => 2,
        'namespace' => 'Illuminate\\Database',
        'declaringClassName' => 'Illuminate\\Database\\Connection',
        'implementingClassName' => 'Illuminate\\Database\\Connection',
        'currentClassName' => 'Illuminate\\Database\\Connection',
        'aliasName' => NULL,
      ),
      'reconnect' => 
      array (
        'name' => 'reconnect',
        'parameters' => 
        array (
        ),
        'returnsReference' => false,
        'returnType' => NULL,
        'attributes' => 
        array (
        ),
        'docComment' => '/**
 * Reconnect to the database.
 *
 * @return mixed|false
 *
 * @throws \\Illuminate\\Database\\LostConnectionException
 */',
        'startLine' => 1050,
        'endLine' => 1057,
        'startColumn' => 5,
        'endColumn' => 5,
        'couldThrow' => false,
        'isClosure' => false,
        'isGenerator' => false,
        'isVariadic' => false,
        'modifiers' => 1,
        'namespace' => 'Illuminate\\Database',
        'declaringClassName' => 'Illuminate\\Database\\Connection',
        'implementingClassName' => 'Illuminate\\Database\\Connection',
        'currentClassName' => 'Illuminate\\Database\\Connection',
        'aliasName' => NULL,
      ),
      'reconnectIfMissingConnection' => 
      array (
        'name' => 'reconnectIfMissingConnection',
        'parameters' => 
        array (
        ),
        'returnsReference' => false,
        'returnType' => NULL,
        'attributes' => 
        array (
        ),
        'docComment' => '/**
 * Reconnect to the database if a PDO connection is missing.
 *
 * @return void
 */',
        'startLine' => 1064,
        'endLine' => 1069,
        'startColumn' => 5,
        'endColumn' => 5,
        'couldThrow' => false,
        'isClosure' => false,
        'isGenerator' => false,
        'isVariadic' => false,
        'modifiers' => 1,
        'namespace' => 'Illuminate\\Database',
        'declaringClassName' => 'Illuminate\\Database\\Connection',
        'implementingClassName' => 'Illuminate\\Database\\Connection',
        'currentClassName' => 'Illuminate\\Database\\Connection',
        'aliasName' => NULL,
      ),
      'disconnect' => 
      array (
        'name' => 'disconnect',
        'parameters' => 
        array (
        ),
        'returnsReference' => false,
        'returnType' => NULL,
        'attributes' => 
        array (
        ),
        'docComment' => '/**
 * Disconnect from the underlying PDO connection.
 *
 * @return void
 */',
        'startLine' => 1076,
        'endLine' => 1081,
        'startColumn' => 5,
        'endColumn' => 5,
        'couldThrow' => false,
        'isClosure' => false,
        'isGenerator' => false,
        'isVariadic' => false,
        'modifiers' => 1,
        'namespace' => 'Illuminate\\Database',
        'declaringClassName' => 'Illuminate\\Database\\Connection',
        'implementingClassName' => 'Illuminate\\Database\\Connection',
        'currentClassName' => 'Illuminate\\Database\\Connection',
        'aliasName' => NULL,
      ),
      'beforeStartingTransaction' => 
      array (
        'name' => 'beforeStartingTransaction',
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
            'startLine' => 1089,
            'endLine' => 1089,
            'startColumn' => 47,
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
 * Register a hook to be run just before a database transaction is started.
 *
 * @param  \\Closure  $callback
 * @return $this
 */',
        'startLine' => 1089,
        'endLine' => 1094,
        'startColumn' => 5,
        'endColumn' => 5,
        'couldThrow' => false,
        'isClosure' => false,
        'isGenerator' => false,
        'isVariadic' => false,
        'modifiers' => 1,
        'namespace' => 'Illuminate\\Database',
        'declaringClassName' => 'Illuminate\\Database\\Connection',
        'implementingClassName' => 'Illuminate\\Database\\Connection',
        'currentClassName' => 'Illuminate\\Database\\Connection',
        'aliasName' => NULL,
      ),
      'beforeExecuting' => 
      array (
        'name' => 'beforeExecuting',
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
            'startLine' => 1102,
            'endLine' => 1102,
            'startColumn' => 37,
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
 * Register a hook to be run just before a database query is executed.
 *
 * @param  \\Closure  $callback
 * @return $this
 */',
        'startLine' => 1102,
        'endLine' => 1107,
        'startColumn' => 5,
        'endColumn' => 5,
        'couldThrow' => false,
        'isClosure' => false,
        'isGenerator' => false,
        'isVariadic' => false,
        'modifiers' => 1,
        'namespace' => 'Illuminate\\Database',
        'declaringClassName' => 'Illuminate\\Database\\Connection',
        'implementingClassName' => 'Illuminate\\Database\\Connection',
        'currentClassName' => 'Illuminate\\Database\\Connection',
        'aliasName' => NULL,
      ),
      'listen' => 
      array (
        'name' => 'listen',
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
            'startLine' => 1115,
            'endLine' => 1115,
            'startColumn' => 28,
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
 * Register a database query listener with the connection.
 *
 * @param  \\Closure(\\Illuminate\\Database\\Events\\QueryExecuted)  $callback
 * @return void
 */',
        'startLine' => 1115,
        'endLine' => 1118,
        'startColumn' => 5,
        'endColumn' => 5,
        'couldThrow' => false,
        'isClosure' => false,
        'isGenerator' => false,
        'isVariadic' => false,
        'modifiers' => 1,
        'namespace' => 'Illuminate\\Database',
        'declaringClassName' => 'Illuminate\\Database\\Connection',
        'implementingClassName' => 'Illuminate\\Database\\Connection',
        'currentClassName' => 'Illuminate\\Database\\Connection',
        'aliasName' => NULL,
      ),
      'fireConnectionEvent' => 
      array (
        'name' => 'fireConnectionEvent',
        'parameters' => 
        array (
          'event' => 
          array (
            'name' => 'event',
            'default' => NULL,
            'type' => NULL,
            'isVariadic' => false,
            'byRef' => false,
            'isPromoted' => false,
            'attributes' => 
            array (
            ),
            'startLine' => 1126,
            'endLine' => 1126,
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
 * Fire an event for this connection.
 *
 * @param  string  $event
 * @return array|null
 */',
        'startLine' => 1126,
        'endLine' => 1135,
        'startColumn' => 5,
        'endColumn' => 5,
        'couldThrow' => false,
        'isClosure' => false,
        'isGenerator' => false,
        'isVariadic' => false,
        'modifiers' => 2,
        'namespace' => 'Illuminate\\Database',
        'declaringClassName' => 'Illuminate\\Database\\Connection',
        'implementingClassName' => 'Illuminate\\Database\\Connection',
        'currentClassName' => 'Illuminate\\Database\\Connection',
        'aliasName' => NULL,
      ),
      'event' => 
      array (
        'name' => 'event',
        'parameters' => 
        array (
          'event' => 
          array (
            'name' => 'event',
            'default' => NULL,
            'type' => NULL,
            'isVariadic' => false,
            'byRef' => false,
            'isPromoted' => false,
            'attributes' => 
            array (
            ),
            'startLine' => 1143,
            'endLine' => 1143,
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
 * Fire the given event if possible.
 *
 * @param  mixed  $event
 * @return void
 */',
        'startLine' => 1143,
        'endLine' => 1146,
        'startColumn' => 5,
        'endColumn' => 5,
        'couldThrow' => false,
        'isClosure' => false,
        'isGenerator' => false,
        'isVariadic' => false,
        'modifiers' => 2,
        'namespace' => 'Illuminate\\Database',
        'declaringClassName' => 'Illuminate\\Database\\Connection',
        'implementingClassName' => 'Illuminate\\Database\\Connection',
        'currentClassName' => 'Illuminate\\Database\\Connection',
        'aliasName' => NULL,
      ),
      'raw' => 
      array (
        'name' => 'raw',
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
            'startLine' => 1154,
            'endLine' => 1154,
            'startColumn' => 25,
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
 * Get a new raw query expression.
 *
 * @param  literal-string|int|float  $value
 * @return \\Illuminate\\Contracts\\Database\\Query\\Expression
 */',
        'startLine' => 1154,
        'endLine' => 1157,
        'startColumn' => 5,
        'endColumn' => 5,
        'couldThrow' => false,
        'isClosure' => false,
        'isGenerator' => false,
        'isVariadic' => false,
        'modifiers' => 1,
        'namespace' => 'Illuminate\\Database',
        'declaringClassName' => 'Illuminate\\Database\\Connection',
        'implementingClassName' => 'Illuminate\\Database\\Connection',
        'currentClassName' => 'Illuminate\\Database\\Connection',
        'aliasName' => NULL,
      ),
      'escape' => 
      array (
        'name' => 'escape',
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
            'startLine' => 1168,
            'endLine' => 1168,
            'startColumn' => 28,
            'endColumn' => 33,
            'parameterIndex' => 0,
            'isOptional' => false,
          ),
          'binary' => 
          array (
            'name' => 'binary',
            'default' => 
            array (
              'code' => 'false',
              'attributes' => 
              array (
                'startLine' => 1168,
                'endLine' => 1168,
                'startTokenPos' => 4042,
                'startFilePos' => 32517,
                'endTokenPos' => 4042,
                'endFilePos' => 32521,
              ),
            ),
            'type' => NULL,
            'isVariadic' => false,
            'byRef' => false,
            'isPromoted' => false,
            'attributes' => 
            array (
            ),
            'startLine' => 1168,
            'endLine' => 1168,
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
 * Escape a value for safe SQL embedding.
 *
 * @param  string|float|int|bool|null  $value
 * @param  bool  $binary
 * @return string
 *
 * @throws \\RuntimeException
 */',
        'startLine' => 1168,
        'endLine' => 1191,
        'startColumn' => 5,
        'endColumn' => 5,
        'couldThrow' => false,
        'isClosure' => false,
        'isGenerator' => false,
        'isVariadic' => false,
        'modifiers' => 1,
        'namespace' => 'Illuminate\\Database',
        'declaringClassName' => 'Illuminate\\Database\\Connection',
        'implementingClassName' => 'Illuminate\\Database\\Connection',
        'currentClassName' => 'Illuminate\\Database\\Connection',
        'aliasName' => NULL,
      ),
      'escapeString' => 
      array (
        'name' => 'escapeString',
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
            'startLine' => 1199,
            'endLine' => 1199,
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
 * Escape a string value for safe SQL embedding.
 *
 * @param  string  $value
 * @return string
 */',
        'startLine' => 1199,
        'endLine' => 1202,
        'startColumn' => 5,
        'endColumn' => 5,
        'couldThrow' => false,
        'isClosure' => false,
        'isGenerator' => false,
        'isVariadic' => false,
        'modifiers' => 2,
        'namespace' => 'Illuminate\\Database',
        'declaringClassName' => 'Illuminate\\Database\\Connection',
        'implementingClassName' => 'Illuminate\\Database\\Connection',
        'currentClassName' => 'Illuminate\\Database\\Connection',
        'aliasName' => NULL,
      ),
      'escapeBool' => 
      array (
        'name' => 'escapeBool',
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
            'startLine' => 1210,
            'endLine' => 1210,
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
 * Escape a boolean value for safe SQL embedding.
 *
 * @param  bool  $value
 * @return string
 */',
        'startLine' => 1210,
        'endLine' => 1213,
        'startColumn' => 5,
        'endColumn' => 5,
        'couldThrow' => false,
        'isClosure' => false,
        'isGenerator' => false,
        'isVariadic' => false,
        'modifiers' => 2,
        'namespace' => 'Illuminate\\Database',
        'declaringClassName' => 'Illuminate\\Database\\Connection',
        'implementingClassName' => 'Illuminate\\Database\\Connection',
        'currentClassName' => 'Illuminate\\Database\\Connection',
        'aliasName' => NULL,
      ),
      'escapeBinary' => 
      array (
        'name' => 'escapeBinary',
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
            'startLine' => 1223,
            'endLine' => 1223,
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
 * Escape a binary value for safe SQL embedding.
 *
 * @param  string  $value
 * @return string
 *
 * @throws \\RuntimeException
 */',
        'startLine' => 1223,
        'endLine' => 1226,
        'startColumn' => 5,
        'endColumn' => 5,
        'couldThrow' => false,
        'isClosure' => false,
        'isGenerator' => false,
        'isVariadic' => false,
        'modifiers' => 2,
        'namespace' => 'Illuminate\\Database',
        'declaringClassName' => 'Illuminate\\Database\\Connection',
        'implementingClassName' => 'Illuminate\\Database\\Connection',
        'currentClassName' => 'Illuminate\\Database\\Connection',
        'aliasName' => NULL,
      ),
      'hasModifiedRecords' => 
      array (
        'name' => 'hasModifiedRecords',
        'parameters' => 
        array (
        ),
        'returnsReference' => false,
        'returnType' => NULL,
        'attributes' => 
        array (
        ),
        'docComment' => '/**
 * Determine if the database connection has modified any database records.
 *
 * @return bool
 */',
        'startLine' => 1233,
        'endLine' => 1236,
        'startColumn' => 5,
        'endColumn' => 5,
        'couldThrow' => false,
        'isClosure' => false,
        'isGenerator' => false,
        'isVariadic' => false,
        'modifiers' => 1,
        'namespace' => 'Illuminate\\Database',
        'declaringClassName' => 'Illuminate\\Database\\Connection',
        'implementingClassName' => 'Illuminate\\Database\\Connection',
        'currentClassName' => 'Illuminate\\Database\\Connection',
        'aliasName' => NULL,
      ),
      'recordsHaveBeenModified' => 
      array (
        'name' => 'recordsHaveBeenModified',
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
                'startLine' => 1244,
                'endLine' => 1244,
                'startTokenPos' => 4348,
                'startFilePos' => 34593,
                'endTokenPos' => 4348,
                'endFilePos' => 34596,
              ),
            ),
            'type' => NULL,
            'isVariadic' => false,
            'byRef' => false,
            'isPromoted' => false,
            'attributes' => 
            array (
            ),
            'startLine' => 1244,
            'endLine' => 1244,
            'startColumn' => 45,
            'endColumn' => 57,
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
 * Indicate if any records have been modified.
 *
 * @param  bool  $value
 * @return void
 */',
        'startLine' => 1244,
        'endLine' => 1249,
        'startColumn' => 5,
        'endColumn' => 5,
        'couldThrow' => false,
        'isClosure' => false,
        'isGenerator' => false,
        'isVariadic' => false,
        'modifiers' => 1,
        'namespace' => 'Illuminate\\Database',
        'declaringClassName' => 'Illuminate\\Database\\Connection',
        'implementingClassName' => 'Illuminate\\Database\\Connection',
        'currentClassName' => 'Illuminate\\Database\\Connection',
        'aliasName' => NULL,
      ),
      'setRecordModificationState' => 
      array (
        'name' => 'setRecordModificationState',
        'parameters' => 
        array (
          'value' => 
          array (
            'name' => 'value',
            'default' => NULL,
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
            'startLine' => 1257,
            'endLine' => 1257,
            'startColumn' => 48,
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
 * Set the record modification state.
 *
 * @param  bool  $value
 * @return $this
 */',
        'startLine' => 1257,
        'endLine' => 1262,
        'startColumn' => 5,
        'endColumn' => 5,
        'couldThrow' => false,
        'isClosure' => false,
        'isGenerator' => false,
        'isVariadic' => false,
        'modifiers' => 1,
        'namespace' => 'Illuminate\\Database',
        'declaringClassName' => 'Illuminate\\Database\\Connection',
        'implementingClassName' => 'Illuminate\\Database\\Connection',
        'currentClassName' => 'Illuminate\\Database\\Connection',
        'aliasName' => NULL,
      ),
      'forgetRecordModificationState' => 
      array (
        'name' => 'forgetRecordModificationState',
        'parameters' => 
        array (
        ),
        'returnsReference' => false,
        'returnType' => NULL,
        'attributes' => 
        array (
        ),
        'docComment' => '/**
 * Reset the record modification state.
 *
 * @return void
 */',
        'startLine' => 1269,
        'endLine' => 1272,
        'startColumn' => 5,
        'endColumn' => 5,
        'couldThrow' => false,
        'isClosure' => false,
        'isGenerator' => false,
        'isVariadic' => false,
        'modifiers' => 1,
        'namespace' => 'Illuminate\\Database',
        'declaringClassName' => 'Illuminate\\Database\\Connection',
        'implementingClassName' => 'Illuminate\\Database\\Connection',
        'currentClassName' => 'Illuminate\\Database\\Connection',
        'aliasName' => NULL,
      ),
      'useWriteConnectionWhenReading' => 
      array (
        'name' => 'useWriteConnectionWhenReading',
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
                'startLine' => 1280,
                'endLine' => 1280,
                'startTokenPos' => 4444,
                'startFilePos' => 35364,
                'endTokenPos' => 4444,
                'endFilePos' => 35367,
              ),
            ),
            'type' => NULL,
            'isVariadic' => false,
            'byRef' => false,
            'isPromoted' => false,
            'attributes' => 
            array (
            ),
            'startLine' => 1280,
            'endLine' => 1280,
            'startColumn' => 51,
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
 * Indicate that the connection should use the write PDO connection for reads.
 *
 * @param  bool  $value
 * @return $this
 */',
        'startLine' => 1280,
        'endLine' => 1285,
        'startColumn' => 5,
        'endColumn' => 5,
        'couldThrow' => false,
        'isClosure' => false,
        'isGenerator' => false,
        'isVariadic' => false,
        'modifiers' => 1,
        'namespace' => 'Illuminate\\Database',
        'declaringClassName' => 'Illuminate\\Database\\Connection',
        'implementingClassName' => 'Illuminate\\Database\\Connection',
        'currentClassName' => 'Illuminate\\Database\\Connection',
        'aliasName' => NULL,
      ),
      'getPdo' => 
      array (
        'name' => 'getPdo',
        'parameters' => 
        array (
        ),
        'returnsReference' => false,
        'returnType' => NULL,
        'attributes' => 
        array (
        ),
        'docComment' => '/**
 * Get the current PDO connection.
 *
 * @return \\PDO
 */',
        'startLine' => 1292,
        'endLine' => 1301,
        'startColumn' => 5,
        'endColumn' => 5,
        'couldThrow' => false,
        'isClosure' => false,
        'isGenerator' => false,
        'isVariadic' => false,
        'modifiers' => 1,
        'namespace' => 'Illuminate\\Database',
        'declaringClassName' => 'Illuminate\\Database\\Connection',
        'implementingClassName' => 'Illuminate\\Database\\Connection',
        'currentClassName' => 'Illuminate\\Database\\Connection',
        'aliasName' => NULL,
      ),
      'getRawPdo' => 
      array (
        'name' => 'getRawPdo',
        'parameters' => 
        array (
        ),
        'returnsReference' => false,
        'returnType' => NULL,
        'attributes' => 
        array (
        ),
        'docComment' => '/**
 * Get the current PDO connection parameter without executing any reconnect logic.
 *
 * @return \\PDO|\\Closure|null
 */',
        'startLine' => 1308,
        'endLine' => 1311,
        'startColumn' => 5,
        'endColumn' => 5,
        'couldThrow' => false,
        'isClosure' => false,
        'isGenerator' => false,
        'isVariadic' => false,
        'modifiers' => 1,
        'namespace' => 'Illuminate\\Database',
        'declaringClassName' => 'Illuminate\\Database\\Connection',
        'implementingClassName' => 'Illuminate\\Database\\Connection',
        'currentClassName' => 'Illuminate\\Database\\Connection',
        'aliasName' => NULL,
      ),
      'getReadPdo' => 
      array (
        'name' => 'getReadPdo',
        'parameters' => 
        array (
        ),
        'returnsReference' => false,
        'returnType' => NULL,
        'attributes' => 
        array (
        ),
        'docComment' => '/**
 * Get the current PDO connection used for reading.
 *
 * @return \\PDO
 */',
        'startLine' => 1318,
        'endLine' => 1336,
        'startColumn' => 5,
        'endColumn' => 5,
        'couldThrow' => false,
        'isClosure' => false,
        'isGenerator' => false,
        'isVariadic' => false,
        'modifiers' => 1,
        'namespace' => 'Illuminate\\Database',
        'declaringClassName' => 'Illuminate\\Database\\Connection',
        'implementingClassName' => 'Illuminate\\Database\\Connection',
        'currentClassName' => 'Illuminate\\Database\\Connection',
        'aliasName' => NULL,
      ),
      'getRawReadPdo' => 
      array (
        'name' => 'getRawReadPdo',
        'parameters' => 
        array (
        ),
        'returnsReference' => false,
        'returnType' => NULL,
        'attributes' => 
        array (
        ),
        'docComment' => '/**
 * Get the current read PDO connection parameter without executing any reconnect logic.
 *
 * @return \\PDO|\\Closure|null
 */',
        'startLine' => 1343,
        'endLine' => 1346,
        'startColumn' => 5,
        'endColumn' => 5,
        'couldThrow' => false,
        'isClosure' => false,
        'isGenerator' => false,
        'isVariadic' => false,
        'modifiers' => 1,
        'namespace' => 'Illuminate\\Database',
        'declaringClassName' => 'Illuminate\\Database\\Connection',
        'implementingClassName' => 'Illuminate\\Database\\Connection',
        'currentClassName' => 'Illuminate\\Database\\Connection',
        'aliasName' => NULL,
      ),
      'getDirectPdo' => 
      array (
        'name' => 'getDirectPdo',
        'parameters' => 
        array (
        ),
        'returnsReference' => false,
        'returnType' => NULL,
        'attributes' => 
        array (
        ),
        'docComment' => '/**
 * Get the current PDO connection used for direct connections.
 *
 * @return \\PDO
 */',
        'startLine' => 1353,
        'endLine' => 1362,
        'startColumn' => 5,
        'endColumn' => 5,
        'couldThrow' => false,
        'isClosure' => false,
        'isGenerator' => false,
        'isVariadic' => false,
        'modifiers' => 1,
        'namespace' => 'Illuminate\\Database',
        'declaringClassName' => 'Illuminate\\Database\\Connection',
        'implementingClassName' => 'Illuminate\\Database\\Connection',
        'currentClassName' => 'Illuminate\\Database\\Connection',
        'aliasName' => NULL,
      ),
      'getRawDirectPdo' => 
      array (
        'name' => 'getRawDirectPdo',
        'parameters' => 
        array (
        ),
        'returnsReference' => false,
        'returnType' => NULL,
        'attributes' => 
        array (
        ),
        'docComment' => '/**
 * Get the current direct PDO connection parameter without executing any reconnect logic.
 *
 * @return \\PDO|\\Closure|null
 */',
        'startLine' => 1369,
        'endLine' => 1372,
        'startColumn' => 5,
        'endColumn' => 5,
        'couldThrow' => false,
        'isClosure' => false,
        'isGenerator' => false,
        'isVariadic' => false,
        'modifiers' => 1,
        'namespace' => 'Illuminate\\Database',
        'declaringClassName' => 'Illuminate\\Database\\Connection',
        'implementingClassName' => 'Illuminate\\Database\\Connection',
        'currentClassName' => 'Illuminate\\Database\\Connection',
        'aliasName' => NULL,
      ),
      'setPdo' => 
      array (
        'name' => 'setPdo',
        'parameters' => 
        array (
          'pdo' => 
          array (
            'name' => 'pdo',
            'default' => NULL,
            'type' => NULL,
            'isVariadic' => false,
            'byRef' => false,
            'isPromoted' => false,
            'attributes' => 
            array (
            ),
            'startLine' => 1380,
            'endLine' => 1380,
            'startColumn' => 28,
            'endColumn' => 31,
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
 * Set the PDO connection.
 *
 * @param  \\PDO|\\Closure|null  $pdo
 * @return $this
 */',
        'startLine' => 1380,
        'endLine' => 1387,
        'startColumn' => 5,
        'endColumn' => 5,
        'couldThrow' => false,
        'isClosure' => false,
        'isGenerator' => false,
        'isVariadic' => false,
        'modifiers' => 1,
        'namespace' => 'Illuminate\\Database',
        'declaringClassName' => 'Illuminate\\Database\\Connection',
        'implementingClassName' => 'Illuminate\\Database\\Connection',
        'currentClassName' => 'Illuminate\\Database\\Connection',
        'aliasName' => NULL,
      ),
      'setReadPdo' => 
      array (
        'name' => 'setReadPdo',
        'parameters' => 
        array (
          'pdo' => 
          array (
            'name' => 'pdo',
            'default' => NULL,
            'type' => NULL,
            'isVariadic' => false,
            'byRef' => false,
            'isPromoted' => false,
            'attributes' => 
            array (
            ),
            'startLine' => 1395,
            'endLine' => 1395,
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
 * Set the PDO connection used for reading.
 *
 * @param  \\PDO|\\Closure|null  $pdo
 * @return $this
 */',
        'startLine' => 1395,
        'endLine' => 1400,
        'startColumn' => 5,
        'endColumn' => 5,
        'couldThrow' => false,
        'isClosure' => false,
        'isGenerator' => false,
        'isVariadic' => false,
        'modifiers' => 1,
        'namespace' => 'Illuminate\\Database',
        'declaringClassName' => 'Illuminate\\Database\\Connection',
        'implementingClassName' => 'Illuminate\\Database\\Connection',
        'currentClassName' => 'Illuminate\\Database\\Connection',
        'aliasName' => NULL,
      ),
      'setReadPdoConfig' => 
      array (
        'name' => 'setReadPdoConfig',
        'parameters' => 
        array (
          'config' => 
          array (
            'name' => 'config',
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
            'startLine' => 1408,
            'endLine' => 1408,
            'startColumn' => 38,
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
 * Set the read PDO connection configuration.
 *
 * @param  array  $config
 * @return $this
 */',
        'startLine' => 1408,
        'endLine' => 1413,
        'startColumn' => 5,
        'endColumn' => 5,
        'couldThrow' => false,
        'isClosure' => false,
        'isGenerator' => false,
        'isVariadic' => false,
        'modifiers' => 1,
        'namespace' => 'Illuminate\\Database',
        'declaringClassName' => 'Illuminate\\Database\\Connection',
        'implementingClassName' => 'Illuminate\\Database\\Connection',
        'currentClassName' => 'Illuminate\\Database\\Connection',
        'aliasName' => NULL,
      ),
      'setDirectPdo' => 
      array (
        'name' => 'setDirectPdo',
        'parameters' => 
        array (
          'pdo' => 
          array (
            'name' => 'pdo',
            'default' => NULL,
            'type' => NULL,
            'isVariadic' => false,
            'byRef' => false,
            'isPromoted' => false,
            'attributes' => 
            array (
            ),
            'startLine' => 1421,
            'endLine' => 1421,
            'startColumn' => 34,
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
 * Set the PDO connection used for direct connections.
 *
 * @param  \\PDO|\\Closure|null  $pdo
 * @return $this
 */',
        'startLine' => 1421,
        'endLine' => 1426,
        'startColumn' => 5,
        'endColumn' => 5,
        'couldThrow' => false,
        'isClosure' => false,
        'isGenerator' => false,
        'isVariadic' => false,
        'modifiers' => 1,
        'namespace' => 'Illuminate\\Database',
        'declaringClassName' => 'Illuminate\\Database\\Connection',
        'implementingClassName' => 'Illuminate\\Database\\Connection',
        'currentClassName' => 'Illuminate\\Database\\Connection',
        'aliasName' => NULL,
      ),
      'setDirectPdoConfig' => 
      array (
        'name' => 'setDirectPdoConfig',
        'parameters' => 
        array (
          'config' => 
          array (
            'name' => 'config',
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
            'startLine' => 1434,
            'endLine' => 1434,
            'startColumn' => 40,
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
 * Set the direct PDO connection configuration.
 *
 * @param  array  $config
 * @return $this
 */',
        'startLine' => 1434,
        'endLine' => 1439,
        'startColumn' => 5,
        'endColumn' => 5,
        'couldThrow' => false,
        'isClosure' => false,
        'isGenerator' => false,
        'isVariadic' => false,
        'modifiers' => 1,
        'namespace' => 'Illuminate\\Database',
        'declaringClassName' => 'Illuminate\\Database\\Connection',
        'implementingClassName' => 'Illuminate\\Database\\Connection',
        'currentClassName' => 'Illuminate\\Database\\Connection',
        'aliasName' => NULL,
      ),
      'getDirectPdoConfig' => 
      array (
        'name' => 'getDirectPdoConfig',
        'parameters' => 
        array (
        ),
        'returnsReference' => false,
        'returnType' => NULL,
        'attributes' => 
        array (
        ),
        'docComment' => '/**
 * Get the direct PDO connection configuration.
 *
 * @return array
 */',
        'startLine' => 1446,
        'endLine' => 1449,
        'startColumn' => 5,
        'endColumn' => 5,
        'couldThrow' => false,
        'isClosure' => false,
        'isGenerator' => false,
        'isVariadic' => false,
        'modifiers' => 1,
        'namespace' => 'Illuminate\\Database',
        'declaringClassName' => 'Illuminate\\Database\\Connection',
        'implementingClassName' => 'Illuminate\\Database\\Connection',
        'currentClassName' => 'Illuminate\\Database\\Connection',
        'aliasName' => NULL,
      ),
      'hasDirectConnection' => 
      array (
        'name' => 'hasDirectConnection',
        'parameters' => 
        array (
        ),
        'returnsReference' => false,
        'returnType' => NULL,
        'attributes' => 
        array (
        ),
        'docComment' => '/**
 * Determine if this connection has a direct PDO connection configured.
 *
 * @return bool
 */',
        'startLine' => 1456,
        'endLine' => 1459,
        'startColumn' => 5,
        'endColumn' => 5,
        'couldThrow' => false,
        'isClosure' => false,
        'isGenerator' => false,
        'isVariadic' => false,
        'modifiers' => 1,
        'namespace' => 'Illuminate\\Database',
        'declaringClassName' => 'Illuminate\\Database\\Connection',
        'implementingClassName' => 'Illuminate\\Database\\Connection',
        'currentClassName' => 'Illuminate\\Database\\Connection',
        'aliasName' => NULL,
      ),
      'setReconnector' => 
      array (
        'name' => 'setReconnector',
        'parameters' => 
        array (
          'reconnector' => 
          array (
            'name' => 'reconnector',
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
            'startLine' => 1467,
            'endLine' => 1467,
            'startColumn' => 36,
            'endColumn' => 56,
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
 * Set the reconnect instance on the connection.
 *
 * @param  (callable(\\Illuminate\\Database\\Connection): mixed)  $reconnector
 * @return $this
 */',
        'startLine' => 1467,
        'endLine' => 1472,
        'startColumn' => 5,
        'endColumn' => 5,
        'couldThrow' => false,
        'isClosure' => false,
        'isGenerator' => false,
        'isVariadic' => false,
        'modifiers' => 1,
        'namespace' => 'Illuminate\\Database',
        'declaringClassName' => 'Illuminate\\Database\\Connection',
        'implementingClassName' => 'Illuminate\\Database\\Connection',
        'currentClassName' => 'Illuminate\\Database\\Connection',
        'aliasName' => NULL,
      ),
      'getName' => 
      array (
        'name' => 'getName',
        'parameters' => 
        array (
        ),
        'returnsReference' => false,
        'returnType' => NULL,
        'attributes' => 
        array (
        ),
        'docComment' => '/**
 * Get the database connection name.
 *
 * @return string|null
 */',
        'startLine' => 1479,
        'endLine' => 1482,
        'startColumn' => 5,
        'endColumn' => 5,
        'couldThrow' => false,
        'isClosure' => false,
        'isGenerator' => false,
        'isVariadic' => false,
        'modifiers' => 1,
        'namespace' => 'Illuminate\\Database',
        'declaringClassName' => 'Illuminate\\Database\\Connection',
        'implementingClassName' => 'Illuminate\\Database\\Connection',
        'currentClassName' => 'Illuminate\\Database\\Connection',
        'aliasName' => NULL,
      ),
      'getNameWithReadWriteType' => 
      array (
        'name' => 'getNameWithReadWriteType',
        'parameters' => 
        array (
        ),
        'returnsReference' => false,
        'returnType' => NULL,
        'attributes' => 
        array (
        ),
        'docComment' => '/**
 * Get the database connection with its read / write type.
 *
 * @return string|null
 */',
        'startLine' => 1489,
        'endLine' => 1494,
        'startColumn' => 5,
        'endColumn' => 5,
        'couldThrow' => false,
        'isClosure' => false,
        'isGenerator' => false,
        'isVariadic' => false,
        'modifiers' => 1,
        'namespace' => 'Illuminate\\Database',
        'declaringClassName' => 'Illuminate\\Database\\Connection',
        'implementingClassName' => 'Illuminate\\Database\\Connection',
        'currentClassName' => 'Illuminate\\Database\\Connection',
        'aliasName' => NULL,
      ),
      'getConfig' => 
      array (
        'name' => 'getConfig',
        'parameters' => 
        array (
          'option' => 
          array (
            'name' => 'option',
            'default' => 
            array (
              'code' => 'null',
              'attributes' => 
              array (
                'startLine' => 1502,
                'endLine' => 1502,
                'startTokenPos' => 5126,
                'startFilePos' => 40087,
                'endTokenPos' => 5126,
                'endFilePos' => 40090,
              ),
            ),
            'type' => NULL,
            'isVariadic' => false,
            'byRef' => false,
            'isPromoted' => false,
            'attributes' => 
            array (
            ),
            'startLine' => 1502,
            'endLine' => 1502,
            'startColumn' => 31,
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
 * Get an option from the configuration options.
 *
 * @param  string|null  $option
 * @return mixed
 */',
        'startLine' => 1502,
        'endLine' => 1505,
        'startColumn' => 5,
        'endColumn' => 5,
        'couldThrow' => false,
        'isClosure' => false,
        'isGenerator' => false,
        'isVariadic' => false,
        'modifiers' => 1,
        'namespace' => 'Illuminate\\Database',
        'declaringClassName' => 'Illuminate\\Database\\Connection',
        'implementingClassName' => 'Illuminate\\Database\\Connection',
        'currentClassName' => 'Illuminate\\Database\\Connection',
        'aliasName' => NULL,
      ),
      'getConnectionDetails' => 
      array (
        'name' => 'getConnectionDetails',
        'parameters' => 
        array (
        ),
        'returnsReference' => false,
        'returnType' => NULL,
        'attributes' => 
        array (
        ),
        'docComment' => '/**
 * Get the basic connection information as an array for debugging.
 *
 * @return array
 */',
        'startLine' => 1512,
        'endLine' => 1528,
        'startColumn' => 5,
        'endColumn' => 5,
        'couldThrow' => false,
        'isClosure' => false,
        'isGenerator' => false,
        'isVariadic' => false,
        'modifiers' => 2,
        'namespace' => 'Illuminate\\Database',
        'declaringClassName' => 'Illuminate\\Database\\Connection',
        'implementingClassName' => 'Illuminate\\Database\\Connection',
        'currentClassName' => 'Illuminate\\Database\\Connection',
        'aliasName' => NULL,
      ),
      'getDriverName' => 
      array (
        'name' => 'getDriverName',
        'parameters' => 
        array (
        ),
        'returnsReference' => false,
        'returnType' => NULL,
        'attributes' => 
        array (
        ),
        'docComment' => '/**
 * Get the PDO driver name.
 *
 * @return string
 */',
        'startLine' => 1535,
        'endLine' => 1538,
        'startColumn' => 5,
        'endColumn' => 5,
        'couldThrow' => false,
        'isClosure' => false,
        'isGenerator' => false,
        'isVariadic' => false,
        'modifiers' => 1,
        'namespace' => 'Illuminate\\Database',
        'declaringClassName' => 'Illuminate\\Database\\Connection',
        'implementingClassName' => 'Illuminate\\Database\\Connection',
        'currentClassName' => 'Illuminate\\Database\\Connection',
        'aliasName' => NULL,
      ),
      'getDriverTitle' => 
      array (
        'name' => 'getDriverTitle',
        'parameters' => 
        array (
        ),
        'returnsReference' => false,
        'returnType' => NULL,
        'attributes' => 
        array (
        ),
        'docComment' => '/**
 * Get a human-readable name for the given connection driver.
 *
 * @return string
 */',
        'startLine' => 1545,
        'endLine' => 1548,
        'startColumn' => 5,
        'endColumn' => 5,
        'couldThrow' => false,
        'isClosure' => false,
        'isGenerator' => false,
        'isVariadic' => false,
        'modifiers' => 1,
        'namespace' => 'Illuminate\\Database',
        'declaringClassName' => 'Illuminate\\Database\\Connection',
        'implementingClassName' => 'Illuminate\\Database\\Connection',
        'currentClassName' => 'Illuminate\\Database\\Connection',
        'aliasName' => NULL,
      ),
      'getQueryGrammar' => 
      array (
        'name' => 'getQueryGrammar',
        'parameters' => 
        array (
        ),
        'returnsReference' => false,
        'returnType' => NULL,
        'attributes' => 
        array (
        ),
        'docComment' => '/**
 * Get the query grammar used by the connection.
 *
 * @return \\Illuminate\\Database\\Query\\Grammars\\Grammar
 */',
        'startLine' => 1555,
        'endLine' => 1558,
        'startColumn' => 5,
        'endColumn' => 5,
        'couldThrow' => false,
        'isClosure' => false,
        'isGenerator' => false,
        'isVariadic' => false,
        'modifiers' => 1,
        'namespace' => 'Illuminate\\Database',
        'declaringClassName' => 'Illuminate\\Database\\Connection',
        'implementingClassName' => 'Illuminate\\Database\\Connection',
        'currentClassName' => 'Illuminate\\Database\\Connection',
        'aliasName' => NULL,
      ),
      'setQueryGrammar' => 
      array (
        'name' => 'setQueryGrammar',
        'parameters' => 
        array (
          'grammar' => 
          array (
            'name' => 'grammar',
            'default' => NULL,
            'type' => 
            array (
              'class' => 'PHPStan\\BetterReflection\\Reflection\\ReflectionNamedType',
              'data' => 
              array (
                'name' => 'Illuminate\\Database\\Query\\Grammars\\Grammar',
                'isIdentifier' => false,
              ),
            ),
            'isVariadic' => false,
            'byRef' => false,
            'isPromoted' => false,
            'attributes' => 
            array (
            ),
            'startLine' => 1566,
            'endLine' => 1566,
            'startColumn' => 37,
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
 * Set the query grammar used by the connection.
 *
 * @param  \\Illuminate\\Database\\Query\\Grammars\\Grammar  $grammar
 * @return $this
 */',
        'startLine' => 1566,
        'endLine' => 1571,
        'startColumn' => 5,
        'endColumn' => 5,
        'couldThrow' => false,
        'isClosure' => false,
        'isGenerator' => false,
        'isVariadic' => false,
        'modifiers' => 1,
        'namespace' => 'Illuminate\\Database',
        'declaringClassName' => 'Illuminate\\Database\\Connection',
        'implementingClassName' => 'Illuminate\\Database\\Connection',
        'currentClassName' => 'Illuminate\\Database\\Connection',
        'aliasName' => NULL,
      ),
      'getSchemaGrammar' => 
      array (
        'name' => 'getSchemaGrammar',
        'parameters' => 
        array (
        ),
        'returnsReference' => false,
        'returnType' => NULL,
        'attributes' => 
        array (
        ),
        'docComment' => '/**
 * Get the schema grammar used by the connection.
 *
 * @return \\Illuminate\\Database\\Schema\\Grammars\\Grammar
 */',
        'startLine' => 1578,
        'endLine' => 1581,
        'startColumn' => 5,
        'endColumn' => 5,
        'couldThrow' => false,
        'isClosure' => false,
        'isGenerator' => false,
        'isVariadic' => false,
        'modifiers' => 1,
        'namespace' => 'Illuminate\\Database',
        'declaringClassName' => 'Illuminate\\Database\\Connection',
        'implementingClassName' => 'Illuminate\\Database\\Connection',
        'currentClassName' => 'Illuminate\\Database\\Connection',
        'aliasName' => NULL,
      ),
      'setSchemaGrammar' => 
      array (
        'name' => 'setSchemaGrammar',
        'parameters' => 
        array (
          'grammar' => 
          array (
            'name' => 'grammar',
            'default' => NULL,
            'type' => 
            array (
              'class' => 'PHPStan\\BetterReflection\\Reflection\\ReflectionNamedType',
              'data' => 
              array (
                'name' => 'Illuminate\\Database\\Schema\\Grammars\\Grammar',
                'isIdentifier' => false,
              ),
            ),
            'isVariadic' => false,
            'byRef' => false,
            'isPromoted' => false,
            'attributes' => 
            array (
            ),
            'startLine' => 1589,
            'endLine' => 1589,
            'startColumn' => 38,
            'endColumn' => 69,
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
 * Set the schema grammar used by the connection.
 *
 * @param  \\Illuminate\\Database\\Schema\\Grammars\\Grammar  $grammar
 * @return $this
 */',
        'startLine' => 1589,
        'endLine' => 1594,
        'startColumn' => 5,
        'endColumn' => 5,
        'couldThrow' => false,
        'isClosure' => false,
        'isGenerator' => false,
        'isVariadic' => false,
        'modifiers' => 1,
        'namespace' => 'Illuminate\\Database',
        'declaringClassName' => 'Illuminate\\Database\\Connection',
        'implementingClassName' => 'Illuminate\\Database\\Connection',
        'currentClassName' => 'Illuminate\\Database\\Connection',
        'aliasName' => NULL,
      ),
      'getPostProcessor' => 
      array (
        'name' => 'getPostProcessor',
        'parameters' => 
        array (
        ),
        'returnsReference' => false,
        'returnType' => NULL,
        'attributes' => 
        array (
        ),
        'docComment' => '/**
 * Get the query post processor used by the connection.
 *
 * @return \\Illuminate\\Database\\Query\\Processors\\Processor
 */',
        'startLine' => 1601,
        'endLine' => 1604,
        'startColumn' => 5,
        'endColumn' => 5,
        'couldThrow' => false,
        'isClosure' => false,
        'isGenerator' => false,
        'isVariadic' => false,
        'modifiers' => 1,
        'namespace' => 'Illuminate\\Database',
        'declaringClassName' => 'Illuminate\\Database\\Connection',
        'implementingClassName' => 'Illuminate\\Database\\Connection',
        'currentClassName' => 'Illuminate\\Database\\Connection',
        'aliasName' => NULL,
      ),
      'setPostProcessor' => 
      array (
        'name' => 'setPostProcessor',
        'parameters' => 
        array (
          'processor' => 
          array (
            'name' => 'processor',
            'default' => NULL,
            'type' => 
            array (
              'class' => 'PHPStan\\BetterReflection\\Reflection\\ReflectionNamedType',
              'data' => 
              array (
                'name' => 'Illuminate\\Database\\Query\\Processors\\Processor',
                'isIdentifier' => false,
              ),
            ),
            'isVariadic' => false,
            'byRef' => false,
            'isPromoted' => false,
            'attributes' => 
            array (
            ),
            'startLine' => 1612,
            'endLine' => 1612,
            'startColumn' => 38,
            'endColumn' => 57,
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
 * Set the query post processor used by the connection.
 *
 * @param  \\Illuminate\\Database\\Query\\Processors\\Processor  $processor
 * @return $this
 */',
        'startLine' => 1612,
        'endLine' => 1617,
        'startColumn' => 5,
        'endColumn' => 5,
        'couldThrow' => false,
        'isClosure' => false,
        'isGenerator' => false,
        'isVariadic' => false,
        'modifiers' => 1,
        'namespace' => 'Illuminate\\Database',
        'declaringClassName' => 'Illuminate\\Database\\Connection',
        'implementingClassName' => 'Illuminate\\Database\\Connection',
        'currentClassName' => 'Illuminate\\Database\\Connection',
        'aliasName' => NULL,
      ),
      'getEventDispatcher' => 
      array (
        'name' => 'getEventDispatcher',
        'parameters' => 
        array (
        ),
        'returnsReference' => false,
        'returnType' => NULL,
        'attributes' => 
        array (
        ),
        'docComment' => '/**
 * Get the event dispatcher used by the connection.
 *
 * @return \\Illuminate\\Contracts\\Events\\Dispatcher|null
 */',
        'startLine' => 1624,
        'endLine' => 1627,
        'startColumn' => 5,
        'endColumn' => 5,
        'couldThrow' => false,
        'isClosure' => false,
        'isGenerator' => false,
        'isVariadic' => false,
        'modifiers' => 1,
        'namespace' => 'Illuminate\\Database',
        'declaringClassName' => 'Illuminate\\Database\\Connection',
        'implementingClassName' => 'Illuminate\\Database\\Connection',
        'currentClassName' => 'Illuminate\\Database\\Connection',
        'aliasName' => NULL,
      ),
      'setEventDispatcher' => 
      array (
        'name' => 'setEventDispatcher',
        'parameters' => 
        array (
          'events' => 
          array (
            'name' => 'events',
            'default' => NULL,
            'type' => 
            array (
              'class' => 'PHPStan\\BetterReflection\\Reflection\\ReflectionNamedType',
              'data' => 
              array (
                'name' => 'Illuminate\\Contracts\\Events\\Dispatcher',
                'isIdentifier' => false,
              ),
            ),
            'isVariadic' => false,
            'byRef' => false,
            'isPromoted' => false,
            'attributes' => 
            array (
            ),
            'startLine' => 1635,
            'endLine' => 1635,
            'startColumn' => 40,
            'endColumn' => 57,
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
 * Set the event dispatcher instance on the connection.
 *
 * @param  \\Illuminate\\Contracts\\Events\\Dispatcher  $events
 * @return $this
 */',
        'startLine' => 1635,
        'endLine' => 1640,
        'startColumn' => 5,
        'endColumn' => 5,
        'couldThrow' => false,
        'isClosure' => false,
        'isGenerator' => false,
        'isVariadic' => false,
        'modifiers' => 1,
        'namespace' => 'Illuminate\\Database',
        'declaringClassName' => 'Illuminate\\Database\\Connection',
        'implementingClassName' => 'Illuminate\\Database\\Connection',
        'currentClassName' => 'Illuminate\\Database\\Connection',
        'aliasName' => NULL,
      ),
      'unsetEventDispatcher' => 
      array (
        'name' => 'unsetEventDispatcher',
        'parameters' => 
        array (
        ),
        'returnsReference' => false,
        'returnType' => NULL,
        'attributes' => 
        array (
        ),
        'docComment' => '/**
 * Unset the event dispatcher for this connection.
 *
 * @return void
 */',
        'startLine' => 1647,
        'endLine' => 1650,
        'startColumn' => 5,
        'endColumn' => 5,
        'couldThrow' => false,
        'isClosure' => false,
        'isGenerator' => false,
        'isVariadic' => false,
        'modifiers' => 1,
        'namespace' => 'Illuminate\\Database',
        'declaringClassName' => 'Illuminate\\Database\\Connection',
        'implementingClassName' => 'Illuminate\\Database\\Connection',
        'currentClassName' => 'Illuminate\\Database\\Connection',
        'aliasName' => NULL,
      ),
      'executeBeginTransactionStatement' => 
      array (
        'name' => 'executeBeginTransactionStatement',
        'parameters' => 
        array (
        ),
        'returnsReference' => false,
        'returnType' => NULL,
        'attributes' => 
        array (
        ),
        'docComment' => '/**
 * Run the statement to start a new transaction.
 *
 * @return void
 */',
        'startLine' => 1657,
        'endLine' => 1660,
        'startColumn' => 5,
        'endColumn' => 5,
        'couldThrow' => false,
        'isClosure' => false,
        'isGenerator' => false,
        'isVariadic' => false,
        'modifiers' => 2,
        'namespace' => 'Illuminate\\Database',
        'declaringClassName' => 'Illuminate\\Database\\Connection',
        'implementingClassName' => 'Illuminate\\Database\\Connection',
        'currentClassName' => 'Illuminate\\Database\\Connection',
        'aliasName' => NULL,
      ),
      'setTransactionManager' => 
      array (
        'name' => 'setTransactionManager',
        'parameters' => 
        array (
          'manager' => 
          array (
            'name' => 'manager',
            'default' => NULL,
            'type' => NULL,
            'isVariadic' => false,
            'byRef' => false,
            'isPromoted' => false,
            'attributes' => 
            array (
            ),
            'startLine' => 1668,
            'endLine' => 1668,
            'startColumn' => 43,
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
 * Set the transaction manager instance on the connection.
 *
 * @param  \\Illuminate\\Database\\DatabaseTransactionsManager  $manager
 * @return $this
 */',
        'startLine' => 1668,
        'endLine' => 1673,
        'startColumn' => 5,
        'endColumn' => 5,
        'couldThrow' => false,
        'isClosure' => false,
        'isGenerator' => false,
        'isVariadic' => false,
        'modifiers' => 1,
        'namespace' => 'Illuminate\\Database',
        'declaringClassName' => 'Illuminate\\Database\\Connection',
        'implementingClassName' => 'Illuminate\\Database\\Connection',
        'currentClassName' => 'Illuminate\\Database\\Connection',
        'aliasName' => NULL,
      ),
      'unsetTransactionManager' => 
      array (
        'name' => 'unsetTransactionManager',
        'parameters' => 
        array (
        ),
        'returnsReference' => false,
        'returnType' => NULL,
        'attributes' => 
        array (
        ),
        'docComment' => '/**
 * Unset the transaction manager for this connection.
 *
 * @return void
 */',
        'startLine' => 1680,
        'endLine' => 1683,
        'startColumn' => 5,
        'endColumn' => 5,
        'couldThrow' => false,
        'isClosure' => false,
        'isGenerator' => false,
        'isVariadic' => false,
        'modifiers' => 1,
        'namespace' => 'Illuminate\\Database',
        'declaringClassName' => 'Illuminate\\Database\\Connection',
        'implementingClassName' => 'Illuminate\\Database\\Connection',
        'currentClassName' => 'Illuminate\\Database\\Connection',
        'aliasName' => NULL,
      ),
      'pretending' => 
      array (
        'name' => 'pretending',
        'parameters' => 
        array (
        ),
        'returnsReference' => false,
        'returnType' => NULL,
        'attributes' => 
        array (
        ),
        'docComment' => '/**
 * Determine if the connection is in a "dry run".
 *
 * @return bool
 */',
        'startLine' => 1690,
        'endLine' => 1693,
        'startColumn' => 5,
        'endColumn' => 5,
        'couldThrow' => false,
        'isClosure' => false,
        'isGenerator' => false,
        'isVariadic' => false,
        'modifiers' => 1,
        'namespace' => 'Illuminate\\Database',
        'declaringClassName' => 'Illuminate\\Database\\Connection',
        'implementingClassName' => 'Illuminate\\Database\\Connection',
        'currentClassName' => 'Illuminate\\Database\\Connection',
        'aliasName' => NULL,
      ),
      'getQueryLog' => 
      array (
        'name' => 'getQueryLog',
        'parameters' => 
        array (
        ),
        'returnsReference' => false,
        'returnType' => NULL,
        'attributes' => 
        array (
        ),
        'docComment' => '/**
 * Get the connection query log.
 *
 * @return array{query: string, bindings: array, time: float|null}[]
 */',
        'startLine' => 1700,
        'endLine' => 1703,
        'startColumn' => 5,
        'endColumn' => 5,
        'couldThrow' => false,
        'isClosure' => false,
        'isGenerator' => false,
        'isVariadic' => false,
        'modifiers' => 1,
        'namespace' => 'Illuminate\\Database',
        'declaringClassName' => 'Illuminate\\Database\\Connection',
        'implementingClassName' => 'Illuminate\\Database\\Connection',
        'currentClassName' => 'Illuminate\\Database\\Connection',
        'aliasName' => NULL,
      ),
      'getRawQueryLog' => 
      array (
        'name' => 'getRawQueryLog',
        'parameters' => 
        array (
        ),
        'returnsReference' => false,
        'returnType' => NULL,
        'attributes' => 
        array (
        ),
        'docComment' => '/**
 * Get the connection query log with embedded bindings.
 *
 * @return array
 */',
        'startLine' => 1710,
        'endLine' => 1719,
        'startColumn' => 5,
        'endColumn' => 5,
        'couldThrow' => false,
        'isClosure' => false,
        'isGenerator' => false,
        'isVariadic' => false,
        'modifiers' => 1,
        'namespace' => 'Illuminate\\Database',
        'declaringClassName' => 'Illuminate\\Database\\Connection',
        'implementingClassName' => 'Illuminate\\Database\\Connection',
        'currentClassName' => 'Illuminate\\Database\\Connection',
        'aliasName' => NULL,
      ),
      'flushQueryLog' => 
      array (
        'name' => 'flushQueryLog',
        'parameters' => 
        array (
        ),
        'returnsReference' => false,
        'returnType' => NULL,
        'attributes' => 
        array (
        ),
        'docComment' => '/**
 * Clear the query log.
 *
 * @return void
 */',
        'startLine' => 1726,
        'endLine' => 1729,
        'startColumn' => 5,
        'endColumn' => 5,
        'couldThrow' => false,
        'isClosure' => false,
        'isGenerator' => false,
        'isVariadic' => false,
        'modifiers' => 1,
        'namespace' => 'Illuminate\\Database',
        'declaringClassName' => 'Illuminate\\Database\\Connection',
        'implementingClassName' => 'Illuminate\\Database\\Connection',
        'currentClassName' => 'Illuminate\\Database\\Connection',
        'aliasName' => NULL,
      ),
      'enableQueryLog' => 
      array (
        'name' => 'enableQueryLog',
        'parameters' => 
        array (
        ),
        'returnsReference' => false,
        'returnType' => NULL,
        'attributes' => 
        array (
        ),
        'docComment' => '/**
 * Enable the query log on the connection.
 *
 * @return void
 */',
        'startLine' => 1736,
        'endLine' => 1739,
        'startColumn' => 5,
        'endColumn' => 5,
        'couldThrow' => false,
        'isClosure' => false,
        'isGenerator' => false,
        'isVariadic' => false,
        'modifiers' => 1,
        'namespace' => 'Illuminate\\Database',
        'declaringClassName' => 'Illuminate\\Database\\Connection',
        'implementingClassName' => 'Illuminate\\Database\\Connection',
        'currentClassName' => 'Illuminate\\Database\\Connection',
        'aliasName' => NULL,
      ),
      'disableQueryLog' => 
      array (
        'name' => 'disableQueryLog',
        'parameters' => 
        array (
        ),
        'returnsReference' => false,
        'returnType' => NULL,
        'attributes' => 
        array (
        ),
        'docComment' => '/**
 * Disable the query log on the connection.
 *
 * @return void
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
        'namespace' => 'Illuminate\\Database',
        'declaringClassName' => 'Illuminate\\Database\\Connection',
        'implementingClassName' => 'Illuminate\\Database\\Connection',
        'currentClassName' => 'Illuminate\\Database\\Connection',
        'aliasName' => NULL,
      ),
      'logging' => 
      array (
        'name' => 'logging',
        'parameters' => 
        array (
        ),
        'returnsReference' => false,
        'returnType' => NULL,
        'attributes' => 
        array (
        ),
        'docComment' => '/**
 * Determine whether we\'re logging queries.
 *
 * @return bool
 */',
        'startLine' => 1756,
        'endLine' => 1759,
        'startColumn' => 5,
        'endColumn' => 5,
        'couldThrow' => false,
        'isClosure' => false,
        'isGenerator' => false,
        'isVariadic' => false,
        'modifiers' => 1,
        'namespace' => 'Illuminate\\Database',
        'declaringClassName' => 'Illuminate\\Database\\Connection',
        'implementingClassName' => 'Illuminate\\Database\\Connection',
        'currentClassName' => 'Illuminate\\Database\\Connection',
        'aliasName' => NULL,
      ),
      'getDatabaseName' => 
      array (
        'name' => 'getDatabaseName',
        'parameters' => 
        array (
        ),
        'returnsReference' => false,
        'returnType' => NULL,
        'attributes' => 
        array (
        ),
        'docComment' => '/**
 * Get the name of the connected database.
 *
 * @return string
 */',
        'startLine' => 1766,
        'endLine' => 1769,
        'startColumn' => 5,
        'endColumn' => 5,
        'couldThrow' => false,
        'isClosure' => false,
        'isGenerator' => false,
        'isVariadic' => false,
        'modifiers' => 1,
        'namespace' => 'Illuminate\\Database',
        'declaringClassName' => 'Illuminate\\Database\\Connection',
        'implementingClassName' => 'Illuminate\\Database\\Connection',
        'currentClassName' => 'Illuminate\\Database\\Connection',
        'aliasName' => NULL,
      ),
      'setDatabaseName' => 
      array (
        'name' => 'setDatabaseName',
        'parameters' => 
        array (
          'database' => 
          array (
            'name' => 'database',
            'default' => NULL,
            'type' => NULL,
            'isVariadic' => false,
            'byRef' => false,
            'isPromoted' => false,
            'attributes' => 
            array (
            ),
            'startLine' => 1777,
            'endLine' => 1777,
            'startColumn' => 37,
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
 * Set the name of the connected database.
 *
 * @param  string  $database
 * @return $this
 */',
        'startLine' => 1777,
        'endLine' => 1782,
        'startColumn' => 5,
        'endColumn' => 5,
        'couldThrow' => false,
        'isClosure' => false,
        'isGenerator' => false,
        'isVariadic' => false,
        'modifiers' => 1,
        'namespace' => 'Illuminate\\Database',
        'declaringClassName' => 'Illuminate\\Database\\Connection',
        'implementingClassName' => 'Illuminate\\Database\\Connection',
        'currentClassName' => 'Illuminate\\Database\\Connection',
        'aliasName' => NULL,
      ),
      'setReadWriteType' => 
      array (
        'name' => 'setReadWriteType',
        'parameters' => 
        array (
          'readWriteType' => 
          array (
            'name' => 'readWriteType',
            'default' => NULL,
            'type' => NULL,
            'isVariadic' => false,
            'byRef' => false,
            'isPromoted' => false,
            'attributes' => 
            array (
            ),
            'startLine' => 1790,
            'endLine' => 1790,
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
 * Set the read / write type of the connection.
 *
 * @param  string|null  $readWriteType
 * @return $this
 */',
        'startLine' => 1790,
        'endLine' => 1795,
        'startColumn' => 5,
        'endColumn' => 5,
        'couldThrow' => false,
        'isClosure' => false,
        'isGenerator' => false,
        'isVariadic' => false,
        'modifiers' => 1,
        'namespace' => 'Illuminate\\Database',
        'declaringClassName' => 'Illuminate\\Database\\Connection',
        'implementingClassName' => 'Illuminate\\Database\\Connection',
        'currentClassName' => 'Illuminate\\Database\\Connection',
        'aliasName' => NULL,
      ),
      'latestReadWriteTypeUsed' => 
      array (
        'name' => 'latestReadWriteTypeUsed',
        'parameters' => 
        array (
        ),
        'returnsReference' => false,
        'returnType' => NULL,
        'attributes' => 
        array (
        ),
        'docComment' => '/**
 * Retrieve the latest read / write type used.
 *
 * @return \'read\'|\'write\'|\'direct\'|null
 */',
        'startLine' => 1802,
        'endLine' => 1805,
        'startColumn' => 5,
        'endColumn' => 5,
        'couldThrow' => false,
        'isClosure' => false,
        'isGenerator' => false,
        'isVariadic' => false,
        'modifiers' => 2,
        'namespace' => 'Illuminate\\Database',
        'declaringClassName' => 'Illuminate\\Database\\Connection',
        'implementingClassName' => 'Illuminate\\Database\\Connection',
        'currentClassName' => 'Illuminate\\Database\\Connection',
        'aliasName' => NULL,
      ),
      'getTablePrefix' => 
      array (
        'name' => 'getTablePrefix',
        'parameters' => 
        array (
        ),
        'returnsReference' => false,
        'returnType' => NULL,
        'attributes' => 
        array (
        ),
        'docComment' => '/**
 * Get the table prefix for the connection.
 *
 * @return string
 */',
        'startLine' => 1812,
        'endLine' => 1815,
        'startColumn' => 5,
        'endColumn' => 5,
        'couldThrow' => false,
        'isClosure' => false,
        'isGenerator' => false,
        'isVariadic' => false,
        'modifiers' => 1,
        'namespace' => 'Illuminate\\Database',
        'declaringClassName' => 'Illuminate\\Database\\Connection',
        'implementingClassName' => 'Illuminate\\Database\\Connection',
        'currentClassName' => 'Illuminate\\Database\\Connection',
        'aliasName' => NULL,
      ),
      'setTablePrefix' => 
      array (
        'name' => 'setTablePrefix',
        'parameters' => 
        array (
          'prefix' => 
          array (
            'name' => 'prefix',
            'default' => NULL,
            'type' => NULL,
            'isVariadic' => false,
            'byRef' => false,
            'isPromoted' => false,
            'attributes' => 
            array (
            ),
            'startLine' => 1823,
            'endLine' => 1823,
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
 * Set the table prefix in use by the connection.
 *
 * @param  string  $prefix
 * @return $this
 */',
        'startLine' => 1823,
        'endLine' => 1828,
        'startColumn' => 5,
        'endColumn' => 5,
        'couldThrow' => false,
        'isClosure' => false,
        'isGenerator' => false,
        'isVariadic' => false,
        'modifiers' => 1,
        'namespace' => 'Illuminate\\Database',
        'declaringClassName' => 'Illuminate\\Database\\Connection',
        'implementingClassName' => 'Illuminate\\Database\\Connection',
        'currentClassName' => 'Illuminate\\Database\\Connection',
        'aliasName' => NULL,
      ),
      'withoutTablePrefix' => 
      array (
        'name' => 'withoutTablePrefix',
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
            'startLine' => 1838,
            'endLine' => 1838,
            'startColumn' => 40,
            'endColumn' => 56,
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
 * Execute the given callback without table prefix.
 *
 * @template TReturn
 *
 * @param  (\\Closure($this): TReturn)  $callback
 * @return TReturn
 */',
        'startLine' => 1838,
        'endLine' => 1849,
        'startColumn' => 5,
        'endColumn' => 5,
        'couldThrow' => false,
        'isClosure' => false,
        'isGenerator' => false,
        'isVariadic' => false,
        'modifiers' => 1,
        'namespace' => 'Illuminate\\Database',
        'declaringClassName' => 'Illuminate\\Database\\Connection',
        'implementingClassName' => 'Illuminate\\Database\\Connection',
        'currentClassName' => 'Illuminate\\Database\\Connection',
        'aliasName' => NULL,
      ),
      'getServerVersion' => 
      array (
        'name' => 'getServerVersion',
        'parameters' => 
        array (
        ),
        'returnsReference' => false,
        'returnType' => 
        array (
          'class' => 'PHPStan\\BetterReflection\\Reflection\\ReflectionNamedType',
          'data' => 
          array (
            'name' => 'string',
            'isIdentifier' => true,
          ),
        ),
        'attributes' => 
        array (
        ),
        'docComment' => '/**
 * Get the server version for the connection.
 *
 * @return string
 */',
        'startLine' => 1856,
        'endLine' => 1859,
        'startColumn' => 5,
        'endColumn' => 5,
        'couldThrow' => false,
        'isClosure' => false,
        'isGenerator' => false,
        'isVariadic' => false,
        'modifiers' => 1,
        'namespace' => 'Illuminate\\Database',
        'declaringClassName' => 'Illuminate\\Database\\Connection',
        'implementingClassName' => 'Illuminate\\Database\\Connection',
        'currentClassName' => 'Illuminate\\Database\\Connection',
        'aliasName' => NULL,
      ),
      'resolverFor' => 
      array (
        'name' => 'resolverFor',
        'parameters' => 
        array (
          'driver' => 
          array (
            'name' => 'driver',
            'default' => NULL,
            'type' => NULL,
            'isVariadic' => false,
            'byRef' => false,
            'isPromoted' => false,
            'attributes' => 
            array (
            ),
            'startLine' => 1868,
            'endLine' => 1868,
            'startColumn' => 40,
            'endColumn' => 46,
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
            'startLine' => 1868,
            'endLine' => 1868,
            'startColumn' => 49,
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
 * Register a connection resolver.
 *
 * @param  string  $driver
 * @param  \\Closure  $callback
 * @return void
 */',
        'startLine' => 1868,
        'endLine' => 1871,
        'startColumn' => 5,
        'endColumn' => 5,
        'couldThrow' => false,
        'isClosure' => false,
        'isGenerator' => false,
        'isVariadic' => false,
        'modifiers' => 17,
        'namespace' => 'Illuminate\\Database',
        'declaringClassName' => 'Illuminate\\Database\\Connection',
        'implementingClassName' => 'Illuminate\\Database\\Connection',
        'currentClassName' => 'Illuminate\\Database\\Connection',
        'aliasName' => NULL,
      ),
      'getResolver' => 
      array (
        'name' => 'getResolver',
        'parameters' => 
        array (
          'driver' => 
          array (
            'name' => 'driver',
            'default' => NULL,
            'type' => NULL,
            'isVariadic' => false,
            'byRef' => false,
            'isPromoted' => false,
            'attributes' => 
            array (
            ),
            'startLine' => 1879,
            'endLine' => 1879,
            'startColumn' => 40,
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
 * Get the connection resolver for the given driver.
 *
 * @param  string  $driver
 * @return \\Closure|null
 */',
        'startLine' => 1879,
        'endLine' => 1882,
        'startColumn' => 5,
        'endColumn' => 5,
        'couldThrow' => false,
        'isClosure' => false,
        'isGenerator' => false,
        'isVariadic' => false,
        'modifiers' => 17,
        'namespace' => 'Illuminate\\Database',
        'declaringClassName' => 'Illuminate\\Database\\Connection',
        'implementingClassName' => 'Illuminate\\Database\\Connection',
        'currentClassName' => 'Illuminate\\Database\\Connection',
        'aliasName' => NULL,
      ),
      '__clone' => 
      array (
        'name' => '__clone',
        'parameters' => 
        array (
        ),
        'returnsReference' => false,
        'returnType' => NULL,
        'attributes' => 
        array (
        ),
        'docComment' => '/**
 * Prepare the instance for cloning.
 *
 * @return void
 */',
        'startLine' => 1889,
        'endLine' => 1897,
        'startColumn' => 5,
        'endColumn' => 5,
        'couldThrow' => false,
        'isClosure' => false,
        'isGenerator' => false,
        'isVariadic' => false,
        'modifiers' => 1,
        'namespace' => 'Illuminate\\Database',
        'declaringClassName' => 'Illuminate\\Database\\Connection',
        'implementingClassName' => 'Illuminate\\Database\\Connection',
        'currentClassName' => 'Illuminate\\Database\\Connection',
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