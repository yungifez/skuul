<?php declare(strict_types = 1);

// osfsl-/var/www/html/vendor/composer/../laravel/framework/src/Illuminate/Routing/Route.php-PHPStan\BetterReflection\Reflection\ReflectionClass-Illuminate\Routing\Route
return \PHPStan\Cache\CacheItem::__set_state(array(
   'variableKey' => 'v2-109d8cb2ccf98edcea7ab6d2637b27d4b969c2cf4a957caf757d958c80f4e83e-8.5.9-6.70.0.3',
   'data' => 
  array (
    'locatedSource' => 
    array (
      'class' => 'PHPStan\\BetterReflection\\SourceLocator\\Located\\LocatedSource',
      'data' => 
      array (
        'name' => 'Illuminate\\Routing\\Route',
        'filename' => '/var/www/html/vendor/composer/../laravel/framework/src/Illuminate/Routing/Route.php',
      ),
    ),
    'namespace' => 'Illuminate\\Routing',
    'name' => 'Illuminate\\Routing\\Route',
    'shortName' => 'Route',
    'isInterface' => false,
    'isTrait' => false,
    'isEnum' => false,
    'isBackedEnum' => false,
    'modifiers' => 0,
    'docComment' => NULL,
    'attributes' => 
    array (
    ),
    'startLine' => 35,
    'endLine' => 1573,
    'startColumn' => 1,
    'endColumn' => 1,
    'parentClassName' => NULL,
    'implementsClassNames' => 
    array (
    ),
    'traitClassNames' => 
    array (
      0 => 'Illuminate\\Support\\Traits\\Conditionable',
      1 => 'Illuminate\\Routing\\CreatesRegularExpressionRouteConstraints',
      2 => 'Illuminate\\Routing\\FiltersControllerMiddleware',
      3 => 'Illuminate\\Support\\Traits\\Macroable',
      4 => 'Illuminate\\Routing\\ResolvesRouteDependencies',
    ),
    'immediateConstants' => 
    array (
    ),
    'immediateProperties' => 
    array (
      'uri' => 
      array (
        'declaringClassName' => 'Illuminate\\Routing\\Route',
        'implementingClassName' => 'Illuminate\\Routing\\Route',
        'name' => 'uri',
        'modifiers' => 1,
        'type' => NULL,
        'default' => NULL,
        'docComment' => '/**
 * The URI pattern the route responds to.
 *
 * @var string
 */',
        'attributes' => 
        array (
        ),
        'startLine' => 44,
        'endLine' => 44,
        'startColumn' => 5,
        'endColumn' => 16,
        'isPromoted' => false,
        'declaredAtCompileTime' => true,
        'immediateVirtual' => false,
        'immediateHooks' => 
        array (
        ),
      ),
      'methods' => 
      array (
        'declaringClassName' => 'Illuminate\\Routing\\Route',
        'implementingClassName' => 'Illuminate\\Routing\\Route',
        'name' => 'methods',
        'modifiers' => 1,
        'type' => NULL,
        'default' => NULL,
        'docComment' => '/**
 * The HTTP methods the route responds to.
 *
 * @var array
 */',
        'attributes' => 
        array (
        ),
        'startLine' => 51,
        'endLine' => 51,
        'startColumn' => 5,
        'endColumn' => 20,
        'isPromoted' => false,
        'declaredAtCompileTime' => true,
        'immediateVirtual' => false,
        'immediateHooks' => 
        array (
        ),
      ),
      'action' => 
      array (
        'declaringClassName' => 'Illuminate\\Routing\\Route',
        'implementingClassName' => 'Illuminate\\Routing\\Route',
        'name' => 'action',
        'modifiers' => 1,
        'type' => NULL,
        'default' => NULL,
        'docComment' => '/**
 * The route action array.
 *
 * @var array
 */',
        'attributes' => 
        array (
        ),
        'startLine' => 58,
        'endLine' => 58,
        'startColumn' => 5,
        'endColumn' => 19,
        'isPromoted' => false,
        'declaredAtCompileTime' => true,
        'immediateVirtual' => false,
        'immediateHooks' => 
        array (
        ),
      ),
      'isFallback' => 
      array (
        'declaringClassName' => 'Illuminate\\Routing\\Route',
        'implementingClassName' => 'Illuminate\\Routing\\Route',
        'name' => 'isFallback',
        'modifiers' => 1,
        'type' => NULL,
        'default' => 
        array (
          'code' => 'false',
          'attributes' => 
          array (
            'startLine' => 65,
            'endLine' => 65,
            'startTokenPos' => 213,
            'startFilePos' => 1787,
            'endTokenPos' => 213,
            'endFilePos' => 1791,
          ),
        ),
        'docComment' => '/**
 * Indicates whether the route is a fallback route.
 *
 * @var bool
 */',
        'attributes' => 
        array (
        ),
        'startLine' => 65,
        'endLine' => 65,
        'startColumn' => 5,
        'endColumn' => 31,
        'isPromoted' => false,
        'declaredAtCompileTime' => true,
        'immediateVirtual' => false,
        'immediateHooks' => 
        array (
        ),
      ),
      'controller' => 
      array (
        'declaringClassName' => 'Illuminate\\Routing\\Route',
        'implementingClassName' => 'Illuminate\\Routing\\Route',
        'name' => 'controller',
        'modifiers' => 1,
        'type' => NULL,
        'default' => NULL,
        'docComment' => '/**
 * The controller instance.
 *
 * @var mixed
 */',
        'attributes' => 
        array (
        ),
        'startLine' => 72,
        'endLine' => 72,
        'startColumn' => 5,
        'endColumn' => 23,
        'isPromoted' => false,
        'declaredAtCompileTime' => true,
        'immediateVirtual' => false,
        'immediateHooks' => 
        array (
        ),
      ),
      'defaults' => 
      array (
        'declaringClassName' => 'Illuminate\\Routing\\Route',
        'implementingClassName' => 'Illuminate\\Routing\\Route',
        'name' => 'defaults',
        'modifiers' => 1,
        'type' => NULL,
        'default' => 
        array (
          'code' => '[]',
          'attributes' => 
          array (
            'startLine' => 79,
            'endLine' => 79,
            'startTokenPos' => 231,
            'startFilePos' => 1998,
            'endTokenPos' => 232,
            'endFilePos' => 1999,
          ),
        ),
        'docComment' => '/**
 * The default values for the route.
 *
 * @var array
 */',
        'attributes' => 
        array (
        ),
        'startLine' => 79,
        'endLine' => 79,
        'startColumn' => 5,
        'endColumn' => 26,
        'isPromoted' => false,
        'declaredAtCompileTime' => true,
        'immediateVirtual' => false,
        'immediateHooks' => 
        array (
        ),
      ),
      'wheres' => 
      array (
        'declaringClassName' => 'Illuminate\\Routing\\Route',
        'implementingClassName' => 'Illuminate\\Routing\\Route',
        'name' => 'wheres',
        'modifiers' => 1,
        'type' => NULL,
        'default' => 
        array (
          'code' => '[]',
          'attributes' => 
          array (
            'startLine' => 86,
            'endLine' => 86,
            'startTokenPos' => 243,
            'startFilePos' => 2109,
            'endTokenPos' => 244,
            'endFilePos' => 2110,
          ),
        ),
        'docComment' => '/**
 * The regular expression requirements.
 *
 * @var array
 */',
        'attributes' => 
        array (
        ),
        'startLine' => 86,
        'endLine' => 86,
        'startColumn' => 5,
        'endColumn' => 24,
        'isPromoted' => false,
        'declaredAtCompileTime' => true,
        'immediateVirtual' => false,
        'immediateHooks' => 
        array (
        ),
      ),
      'parameters' => 
      array (
        'declaringClassName' => 'Illuminate\\Routing\\Route',
        'implementingClassName' => 'Illuminate\\Routing\\Route',
        'name' => 'parameters',
        'modifiers' => 1,
        'type' => NULL,
        'default' => NULL,
        'docComment' => '/**
 * The array of matched parameters.
 *
 * @var array|null
 */',
        'attributes' => 
        array (
        ),
        'startLine' => 93,
        'endLine' => 93,
        'startColumn' => 5,
        'endColumn' => 23,
        'isPromoted' => false,
        'declaredAtCompileTime' => true,
        'immediateVirtual' => false,
        'immediateHooks' => 
        array (
        ),
      ),
      'parameterNames' => 
      array (
        'declaringClassName' => 'Illuminate\\Routing\\Route',
        'implementingClassName' => 'Illuminate\\Routing\\Route',
        'name' => 'parameterNames',
        'modifiers' => 1,
        'type' => NULL,
        'default' => NULL,
        'docComment' => '/**
 * The parameter names for the route.
 *
 * @var array|null
 */',
        'attributes' => 
        array (
        ),
        'startLine' => 100,
        'endLine' => 100,
        'startColumn' => 5,
        'endColumn' => 27,
        'isPromoted' => false,
        'declaredAtCompileTime' => true,
        'immediateVirtual' => false,
        'immediateHooks' => 
        array (
        ),
      ),
      'originalParameters' => 
      array (
        'declaringClassName' => 'Illuminate\\Routing\\Route',
        'implementingClassName' => 'Illuminate\\Routing\\Route',
        'name' => 'originalParameters',
        'modifiers' => 2,
        'type' => NULL,
        'default' => NULL,
        'docComment' => '/**
 * The array of the matched parameters\' original values.
 *
 * @var array
 */',
        'attributes' => 
        array (
        ),
        'startLine' => 107,
        'endLine' => 107,
        'startColumn' => 5,
        'endColumn' => 34,
        'isPromoted' => false,
        'declaredAtCompileTime' => true,
        'immediateVirtual' => false,
        'immediateHooks' => 
        array (
        ),
      ),
      'withTrashedBindings' => 
      array (
        'declaringClassName' => 'Illuminate\\Routing\\Route',
        'implementingClassName' => 'Illuminate\\Routing\\Route',
        'name' => 'withTrashedBindings',
        'modifiers' => 2,
        'type' => NULL,
        'default' => 
        array (
          'code' => 'false',
          'attributes' => 
          array (
            'startLine' => 114,
            'endLine' => 114,
            'startTokenPos' => 276,
            'startFilePos' => 2663,
            'endTokenPos' => 276,
            'endFilePos' => 2667,
          ),
        ),
        'docComment' => '/**
 * Indicates "trashed" models can be retrieved when resolving implicit model bindings for this route.
 *
 * @var bool
 */',
        'attributes' => 
        array (
        ),
        'startLine' => 114,
        'endLine' => 114,
        'startColumn' => 5,
        'endColumn' => 43,
        'isPromoted' => false,
        'declaredAtCompileTime' => true,
        'immediateVirtual' => false,
        'immediateHooks' => 
        array (
        ),
      ),
      'lockSeconds' => 
      array (
        'declaringClassName' => 'Illuminate\\Routing\\Route',
        'implementingClassName' => 'Illuminate\\Routing\\Route',
        'name' => 'lockSeconds',
        'modifiers' => 2,
        'type' => NULL,
        'default' => NULL,
        'docComment' => '/**
 * Indicates the maximum number of seconds the route should acquire a session lock for.
 *
 * @var int|null
 */',
        'attributes' => 
        array (
        ),
        'startLine' => 121,
        'endLine' => 121,
        'startColumn' => 5,
        'endColumn' => 27,
        'isPromoted' => false,
        'declaredAtCompileTime' => true,
        'immediateVirtual' => false,
        'immediateHooks' => 
        array (
        ),
      ),
      'waitSeconds' => 
      array (
        'declaringClassName' => 'Illuminate\\Routing\\Route',
        'implementingClassName' => 'Illuminate\\Routing\\Route',
        'name' => 'waitSeconds',
        'modifiers' => 2,
        'type' => NULL,
        'default' => NULL,
        'docComment' => '/**
 * Indicates the maximum number of seconds the route should wait while attempting to acquire a session lock.
 *
 * @var int|null
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
      'computedMiddleware' => 
      array (
        'declaringClassName' => 'Illuminate\\Routing\\Route',
        'implementingClassName' => 'Illuminate\\Routing\\Route',
        'name' => 'computedMiddleware',
        'modifiers' => 1,
        'type' => NULL,
        'default' => NULL,
        'docComment' => '/**
 * The computed gathered middleware.
 *
 * @var array|null
 */',
        'attributes' => 
        array (
        ),
        'startLine' => 135,
        'endLine' => 135,
        'startColumn' => 5,
        'endColumn' => 31,
        'isPromoted' => false,
        'declaredAtCompileTime' => true,
        'immediateVirtual' => false,
        'immediateHooks' => 
        array (
        ),
      ),
      'compiled' => 
      array (
        'declaringClassName' => 'Illuminate\\Routing\\Route',
        'implementingClassName' => 'Illuminate\\Routing\\Route',
        'name' => 'compiled',
        'modifiers' => 1,
        'type' => NULL,
        'default' => NULL,
        'docComment' => '/**
 * The compiled version of the route.
 *
 * @var \\Symfony\\Component\\Routing\\CompiledRoute
 */',
        'attributes' => 
        array (
        ),
        'startLine' => 142,
        'endLine' => 142,
        'startColumn' => 5,
        'endColumn' => 21,
        'isPromoted' => false,
        'declaredAtCompileTime' => true,
        'immediateVirtual' => false,
        'immediateHooks' => 
        array (
        ),
      ),
      'router' => 
      array (
        'declaringClassName' => 'Illuminate\\Routing\\Route',
        'implementingClassName' => 'Illuminate\\Routing\\Route',
        'name' => 'router',
        'modifiers' => 2,
        'type' => NULL,
        'default' => NULL,
        'docComment' => '/**
 * The router instance used by the route.
 *
 * @var \\Illuminate\\Routing\\Router
 */',
        'attributes' => 
        array (
        ),
        'startLine' => 149,
        'endLine' => 149,
        'startColumn' => 5,
        'endColumn' => 22,
        'isPromoted' => false,
        'declaredAtCompileTime' => true,
        'immediateVirtual' => false,
        'immediateHooks' => 
        array (
        ),
      ),
      'container' => 
      array (
        'declaringClassName' => 'Illuminate\\Routing\\Route',
        'implementingClassName' => 'Illuminate\\Routing\\Route',
        'name' => 'container',
        'modifiers' => 2,
        'type' => NULL,
        'default' => NULL,
        'docComment' => '/**
 * The container instance used by the route.
 *
 * @var \\Illuminate\\Container\\Container
 */',
        'attributes' => 
        array (
        ),
        'startLine' => 156,
        'endLine' => 156,
        'startColumn' => 5,
        'endColumn' => 25,
        'isPromoted' => false,
        'declaredAtCompileTime' => true,
        'immediateVirtual' => false,
        'immediateHooks' => 
        array (
        ),
      ),
      'bindingFields' => 
      array (
        'declaringClassName' => 'Illuminate\\Routing\\Route',
        'implementingClassName' => 'Illuminate\\Routing\\Route',
        'name' => 'bindingFields',
        'modifiers' => 2,
        'type' => NULL,
        'default' => 
        array (
          'code' => '[]',
          'attributes' => 
          array (
            'startLine' => 163,
            'endLine' => 163,
            'startTokenPos' => 329,
            'startFilePos' => 3704,
            'endTokenPos' => 330,
            'endFilePos' => 3705,
          ),
        ),
        'docComment' => '/**
 * The fields that implicit binding should use for a given parameter.
 *
 * @var array
 */',
        'attributes' => 
        array (
        ),
        'startLine' => 163,
        'endLine' => 163,
        'startColumn' => 5,
        'endColumn' => 34,
        'isPromoted' => false,
        'declaredAtCompileTime' => true,
        'immediateVirtual' => false,
        'immediateHooks' => 
        array (
        ),
      ),
      'validators' => 
      array (
        'declaringClassName' => 'Illuminate\\Routing\\Route',
        'implementingClassName' => 'Illuminate\\Routing\\Route',
        'name' => 'validators',
        'modifiers' => 17,
        'type' => NULL,
        'default' => NULL,
        'docComment' => '/**
 * The validators used by the routes.
 *
 * @var array
 */',
        'attributes' => 
        array (
        ),
        'startLine' => 170,
        'endLine' => 170,
        'startColumn' => 5,
        'endColumn' => 30,
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
          'methods' => 
          array (
            'name' => 'methods',
            'default' => NULL,
            'type' => NULL,
            'isVariadic' => false,
            'byRef' => false,
            'isPromoted' => false,
            'attributes' => 
            array (
            ),
            'startLine' => 179,
            'endLine' => 179,
            'startColumn' => 33,
            'endColumn' => 40,
            'parameterIndex' => 0,
            'isOptional' => false,
          ),
          'uri' => 
          array (
            'name' => 'uri',
            'default' => NULL,
            'type' => NULL,
            'isVariadic' => false,
            'byRef' => false,
            'isPromoted' => false,
            'attributes' => 
            array (
            ),
            'startLine' => 179,
            'endLine' => 179,
            'startColumn' => 43,
            'endColumn' => 46,
            'parameterIndex' => 1,
            'isOptional' => false,
          ),
          'action' => 
          array (
            'name' => 'action',
            'default' => NULL,
            'type' => NULL,
            'isVariadic' => false,
            'byRef' => false,
            'isPromoted' => false,
            'attributes' => 
            array (
            ),
            'startLine' => 179,
            'endLine' => 179,
            'startColumn' => 49,
            'endColumn' => 55,
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
 * Create a new Route instance.
 *
 * @param  array|string  $methods
 * @param  string  $uri
 * @param  \\Closure|array  $action
 */',
        'startLine' => 179,
        'endLine' => 190,
        'startColumn' => 5,
        'endColumn' => 5,
        'couldThrow' => false,
        'isClosure' => false,
        'isGenerator' => false,
        'isVariadic' => false,
        'modifiers' => 1,
        'namespace' => 'Illuminate\\Routing',
        'declaringClassName' => 'Illuminate\\Routing\\Route',
        'implementingClassName' => 'Illuminate\\Routing\\Route',
        'currentClassName' => 'Illuminate\\Routing\\Route',
        'aliasName' => NULL,
      ),
      'parseAction' => 
      array (
        'name' => 'parseAction',
        'parameters' => 
        array (
          'action' => 
          array (
            'name' => 'action',
            'default' => NULL,
            'type' => NULL,
            'isVariadic' => false,
            'byRef' => false,
            'isPromoted' => false,
            'attributes' => 
            array (
            ),
            'startLine' => 200,
            'endLine' => 200,
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
 * Parse the route action into a standard array.
 *
 * @param  callable|array|null  $action
 * @return array
 *
 * @throws \\UnexpectedValueException
 */',
        'startLine' => 200,
        'endLine' => 203,
        'startColumn' => 5,
        'endColumn' => 5,
        'couldThrow' => false,
        'isClosure' => false,
        'isGenerator' => false,
        'isVariadic' => false,
        'modifiers' => 2,
        'namespace' => 'Illuminate\\Routing',
        'declaringClassName' => 'Illuminate\\Routing\\Route',
        'implementingClassName' => 'Illuminate\\Routing\\Route',
        'currentClassName' => 'Illuminate\\Routing\\Route',
        'aliasName' => NULL,
      ),
      'run' => 
      array (
        'name' => 'run',
        'parameters' => 
        array (
        ),
        'returnsReference' => false,
        'returnType' => NULL,
        'attributes' => 
        array (
        ),
        'docComment' => '/**
 * Run the route action and return the response.
 *
 * @return mixed
 */',
        'startLine' => 210,
        'endLine' => 223,
        'startColumn' => 5,
        'endColumn' => 5,
        'couldThrow' => false,
        'isClosure' => false,
        'isGenerator' => false,
        'isVariadic' => false,
        'modifiers' => 1,
        'namespace' => 'Illuminate\\Routing',
        'declaringClassName' => 'Illuminate\\Routing\\Route',
        'implementingClassName' => 'Illuminate\\Routing\\Route',
        'currentClassName' => 'Illuminate\\Routing\\Route',
        'aliasName' => NULL,
      ),
      'isControllerAction' => 
      array (
        'name' => 'isControllerAction',
        'parameters' => 
        array (
        ),
        'returnsReference' => false,
        'returnType' => NULL,
        'attributes' => 
        array (
        ),
        'docComment' => '/**
 * Checks whether the route\'s action is a controller.
 *
 * @return bool
 */',
        'startLine' => 230,
        'endLine' => 233,
        'startColumn' => 5,
        'endColumn' => 5,
        'couldThrow' => false,
        'isClosure' => false,
        'isGenerator' => false,
        'isVariadic' => false,
        'modifiers' => 2,
        'namespace' => 'Illuminate\\Routing',
        'declaringClassName' => 'Illuminate\\Routing\\Route',
        'implementingClassName' => 'Illuminate\\Routing\\Route',
        'currentClassName' => 'Illuminate\\Routing\\Route',
        'aliasName' => NULL,
      ),
      'runCallable' => 
      array (
        'name' => 'runCallable',
        'parameters' => 
        array (
        ),
        'returnsReference' => false,
        'returnType' => NULL,
        'attributes' => 
        array (
        ),
        'docComment' => '/**
 * Run the route action and return the response.
 *
 * @return mixed
 */',
        'startLine' => 240,
        'endLine' => 255,
        'startColumn' => 5,
        'endColumn' => 5,
        'couldThrow' => false,
        'isClosure' => false,
        'isGenerator' => false,
        'isVariadic' => false,
        'modifiers' => 2,
        'namespace' => 'Illuminate\\Routing',
        'declaringClassName' => 'Illuminate\\Routing\\Route',
        'implementingClassName' => 'Illuminate\\Routing\\Route',
        'currentClassName' => 'Illuminate\\Routing\\Route',
        'aliasName' => NULL,
      ),
      'isSerializedClosure' => 
      array (
        'name' => 'isSerializedClosure',
        'parameters' => 
        array (
        ),
        'returnsReference' => false,
        'returnType' => NULL,
        'attributes' => 
        array (
        ),
        'docComment' => '/**
 * Determine if the route action is a serialized Closure.
 *
 * @return bool
 */',
        'startLine' => 262,
        'endLine' => 265,
        'startColumn' => 5,
        'endColumn' => 5,
        'couldThrow' => false,
        'isClosure' => false,
        'isGenerator' => false,
        'isVariadic' => false,
        'modifiers' => 2,
        'namespace' => 'Illuminate\\Routing',
        'declaringClassName' => 'Illuminate\\Routing\\Route',
        'implementingClassName' => 'Illuminate\\Routing\\Route',
        'currentClassName' => 'Illuminate\\Routing\\Route',
        'aliasName' => NULL,
      ),
      'runController' => 
      array (
        'name' => 'runController',
        'parameters' => 
        array (
        ),
        'returnsReference' => false,
        'returnType' => NULL,
        'attributes' => 
        array (
        ),
        'docComment' => '/**
 * Run the route action and return the response.
 *
 * @return mixed
 *
 * @throws \\Symfony\\Component\\HttpKernel\\Exception\\NotFoundHttpException
 */',
        'startLine' => 274,
        'endLine' => 279,
        'startColumn' => 5,
        'endColumn' => 5,
        'couldThrow' => false,
        'isClosure' => false,
        'isGenerator' => false,
        'isVariadic' => false,
        'modifiers' => 2,
        'namespace' => 'Illuminate\\Routing',
        'declaringClassName' => 'Illuminate\\Routing\\Route',
        'implementingClassName' => 'Illuminate\\Routing\\Route',
        'currentClassName' => 'Illuminate\\Routing\\Route',
        'aliasName' => NULL,
      ),
      'getController' => 
      array (
        'name' => 'getController',
        'parameters' => 
        array (
        ),
        'returnsReference' => false,
        'returnType' => NULL,
        'attributes' => 
        array (
        ),
        'docComment' => '/**
 * Get the controller instance for the route.
 *
 * @return mixed
 *
 * @throws \\Illuminate\\Contracts\\Container\\BindingResolutionException
 */',
        'startLine' => 288,
        'endLine' => 301,
        'startColumn' => 5,
        'endColumn' => 5,
        'couldThrow' => false,
        'isClosure' => false,
        'isGenerator' => false,
        'isVariadic' => false,
        'modifiers' => 1,
        'namespace' => 'Illuminate\\Routing',
        'declaringClassName' => 'Illuminate\\Routing\\Route',
        'implementingClassName' => 'Illuminate\\Routing\\Route',
        'currentClassName' => 'Illuminate\\Routing\\Route',
        'aliasName' => NULL,
      ),
      'getControllerClass' => 
      array (
        'name' => 'getControllerClass',
        'parameters' => 
        array (
        ),
        'returnsReference' => false,
        'returnType' => NULL,
        'attributes' => 
        array (
        ),
        'docComment' => '/**
 * Get the controller class used for the route.
 *
 * @return string|null
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
        'namespace' => 'Illuminate\\Routing',
        'declaringClassName' => 'Illuminate\\Routing\\Route',
        'implementingClassName' => 'Illuminate\\Routing\\Route',
        'currentClassName' => 'Illuminate\\Routing\\Route',
        'aliasName' => NULL,
      ),
      'getControllerMethod' => 
      array (
        'name' => 'getControllerMethod',
        'parameters' => 
        array (
        ),
        'returnsReference' => false,
        'returnType' => NULL,
        'attributes' => 
        array (
        ),
        'docComment' => '/**
 * Get the controller method used for the route.
 *
 * @return string
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
        'namespace' => 'Illuminate\\Routing',
        'declaringClassName' => 'Illuminate\\Routing\\Route',
        'implementingClassName' => 'Illuminate\\Routing\\Route',
        'currentClassName' => 'Illuminate\\Routing\\Route',
        'aliasName' => NULL,
      ),
      'parseControllerCallback' => 
      array (
        'name' => 'parseControllerCallback',
        'parameters' => 
        array (
        ),
        'returnsReference' => false,
        'returnType' => NULL,
        'attributes' => 
        array (
        ),
        'docComment' => '/**
 * Parse the controller.
 *
 * @return array
 */',
        'startLine' => 328,
        'endLine' => 331,
        'startColumn' => 5,
        'endColumn' => 5,
        'couldThrow' => false,
        'isClosure' => false,
        'isGenerator' => false,
        'isVariadic' => false,
        'modifiers' => 2,
        'namespace' => 'Illuminate\\Routing',
        'declaringClassName' => 'Illuminate\\Routing\\Route',
        'implementingClassName' => 'Illuminate\\Routing\\Route',
        'currentClassName' => 'Illuminate\\Routing\\Route',
        'aliasName' => NULL,
      ),
      'flushController' => 
      array (
        'name' => 'flushController',
        'parameters' => 
        array (
        ),
        'returnsReference' => false,
        'returnType' => NULL,
        'attributes' => 
        array (
        ),
        'docComment' => '/**
 * Flush the cached container instance on the route.
 *
 * @return void
 */',
        'startLine' => 338,
        'endLine' => 342,
        'startColumn' => 5,
        'endColumn' => 5,
        'couldThrow' => false,
        'isClosure' => false,
        'isGenerator' => false,
        'isVariadic' => false,
        'modifiers' => 1,
        'namespace' => 'Illuminate\\Routing',
        'declaringClassName' => 'Illuminate\\Routing\\Route',
        'implementingClassName' => 'Illuminate\\Routing\\Route',
        'currentClassName' => 'Illuminate\\Routing\\Route',
        'aliasName' => NULL,
      ),
      'matches' => 
      array (
        'name' => 'matches',
        'parameters' => 
        array (
          'request' => 
          array (
            'name' => 'request',
            'default' => NULL,
            'type' => 
            array (
              'class' => 'PHPStan\\BetterReflection\\Reflection\\ReflectionNamedType',
              'data' => 
              array (
                'name' => 'Illuminate\\Http\\Request',
                'isIdentifier' => false,
              ),
            ),
            'isVariadic' => false,
            'byRef' => false,
            'isPromoted' => false,
            'attributes' => 
            array (
            ),
            'startLine' => 351,
            'endLine' => 351,
            'startColumn' => 29,
            'endColumn' => 44,
            'parameterIndex' => 0,
            'isOptional' => false,
          ),
          'includingMethod' => 
          array (
            'name' => 'includingMethod',
            'default' => 
            array (
              'code' => 'true',
              'attributes' => 
              array (
                'startLine' => 351,
                'endLine' => 351,
                'startTokenPos' => 1051,
                'startFilePos' => 8452,
                'endTokenPos' => 1051,
                'endFilePos' => 8455,
              ),
            ),
            'type' => NULL,
            'isVariadic' => false,
            'byRef' => false,
            'isPromoted' => false,
            'attributes' => 
            array (
            ),
            'startLine' => 351,
            'endLine' => 351,
            'startColumn' => 47,
            'endColumn' => 69,
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
 * Determine if the route matches a given request.
 *
 * @param  \\Illuminate\\Http\\Request  $request
 * @param  bool  $includingMethod
 * @return bool
 */',
        'startLine' => 351,
        'endLine' => 366,
        'startColumn' => 5,
        'endColumn' => 5,
        'couldThrow' => false,
        'isClosure' => false,
        'isGenerator' => false,
        'isVariadic' => false,
        'modifiers' => 1,
        'namespace' => 'Illuminate\\Routing',
        'declaringClassName' => 'Illuminate\\Routing\\Route',
        'implementingClassName' => 'Illuminate\\Routing\\Route',
        'currentClassName' => 'Illuminate\\Routing\\Route',
        'aliasName' => NULL,
      ),
      'compileRoute' => 
      array (
        'name' => 'compileRoute',
        'parameters' => 
        array (
        ),
        'returnsReference' => false,
        'returnType' => NULL,
        'attributes' => 
        array (
        ),
        'docComment' => '/**
 * Compile the route into a Symfony CompiledRoute instance.
 *
 * @return \\Symfony\\Component\\Routing\\CompiledRoute
 */',
        'startLine' => 373,
        'endLine' => 380,
        'startColumn' => 5,
        'endColumn' => 5,
        'couldThrow' => false,
        'isClosure' => false,
        'isGenerator' => false,
        'isVariadic' => false,
        'modifiers' => 2,
        'namespace' => 'Illuminate\\Routing',
        'declaringClassName' => 'Illuminate\\Routing\\Route',
        'implementingClassName' => 'Illuminate\\Routing\\Route',
        'currentClassName' => 'Illuminate\\Routing\\Route',
        'aliasName' => NULL,
      ),
      'bind' => 
      array (
        'name' => 'bind',
        'parameters' => 
        array (
          'request' => 
          array (
            'name' => 'request',
            'default' => NULL,
            'type' => 
            array (
              'class' => 'PHPStan\\BetterReflection\\Reflection\\ReflectionNamedType',
              'data' => 
              array (
                'name' => 'Illuminate\\Http\\Request',
                'isIdentifier' => false,
              ),
            ),
            'isVariadic' => false,
            'byRef' => false,
            'isPromoted' => false,
            'attributes' => 
            array (
            ),
            'startLine' => 388,
            'endLine' => 388,
            'startColumn' => 26,
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
 * Bind the route to a given request for execution.
 *
 * @param  \\Illuminate\\Http\\Request  $request
 * @return $this
 */',
        'startLine' => 388,
        'endLine' => 398,
        'startColumn' => 5,
        'endColumn' => 5,
        'couldThrow' => false,
        'isClosure' => false,
        'isGenerator' => false,
        'isVariadic' => false,
        'modifiers' => 1,
        'namespace' => 'Illuminate\\Routing',
        'declaringClassName' => 'Illuminate\\Routing\\Route',
        'implementingClassName' => 'Illuminate\\Routing\\Route',
        'currentClassName' => 'Illuminate\\Routing\\Route',
        'aliasName' => NULL,
      ),
      'hasParameters' => 
      array (
        'name' => 'hasParameters',
        'parameters' => 
        array (
        ),
        'returnsReference' => false,
        'returnType' => NULL,
        'attributes' => 
        array (
        ),
        'docComment' => '/**
 * Determine if the route has parameters.
 *
 * @return bool
 */',
        'startLine' => 405,
        'endLine' => 408,
        'startColumn' => 5,
        'endColumn' => 5,
        'couldThrow' => false,
        'isClosure' => false,
        'isGenerator' => false,
        'isVariadic' => false,
        'modifiers' => 1,
        'namespace' => 'Illuminate\\Routing',
        'declaringClassName' => 'Illuminate\\Routing\\Route',
        'implementingClassName' => 'Illuminate\\Routing\\Route',
        'currentClassName' => 'Illuminate\\Routing\\Route',
        'aliasName' => NULL,
      ),
      'hasParameter' => 
      array (
        'name' => 'hasParameter',
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
            'startLine' => 416,
            'endLine' => 416,
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
 * Determine a given parameter exists from the route.
 *
 * @param  string  $name
 * @return bool
 */',
        'startLine' => 416,
        'endLine' => 423,
        'startColumn' => 5,
        'endColumn' => 5,
        'couldThrow' => false,
        'isClosure' => false,
        'isGenerator' => false,
        'isVariadic' => false,
        'modifiers' => 1,
        'namespace' => 'Illuminate\\Routing',
        'declaringClassName' => 'Illuminate\\Routing\\Route',
        'implementingClassName' => 'Illuminate\\Routing\\Route',
        'currentClassName' => 'Illuminate\\Routing\\Route',
        'aliasName' => NULL,
      ),
      'parameter' => 
      array (
        'name' => 'parameter',
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
            'startLine' => 432,
            'endLine' => 432,
            'startColumn' => 31,
            'endColumn' => 35,
            'parameterIndex' => 0,
            'isOptional' => false,
          ),
          'default' => 
          array (
            'name' => 'default',
            'default' => 
            array (
              'code' => 'null',
              'attributes' => 
              array (
                'startLine' => 432,
                'endLine' => 432,
                'startTokenPos' => 1338,
                'startFilePos' => 10285,
                'endTokenPos' => 1338,
                'endFilePos' => 10288,
              ),
            ),
            'type' => NULL,
            'isVariadic' => false,
            'byRef' => false,
            'isPromoted' => false,
            'attributes' => 
            array (
            ),
            'startLine' => 432,
            'endLine' => 432,
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
 * Get a given parameter from the route.
 *
 * @param  string  $name
 * @param  string|object|null  $default
 * @return string|object|null
 */',
        'startLine' => 432,
        'endLine' => 435,
        'startColumn' => 5,
        'endColumn' => 5,
        'couldThrow' => false,
        'isClosure' => false,
        'isGenerator' => false,
        'isVariadic' => false,
        'modifiers' => 1,
        'namespace' => 'Illuminate\\Routing',
        'declaringClassName' => 'Illuminate\\Routing\\Route',
        'implementingClassName' => 'Illuminate\\Routing\\Route',
        'currentClassName' => 'Illuminate\\Routing\\Route',
        'aliasName' => NULL,
      ),
      'originalParameter' => 
      array (
        'name' => 'originalParameter',
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
            'startLine' => 444,
            'endLine' => 444,
            'startColumn' => 39,
            'endColumn' => 43,
            'parameterIndex' => 0,
            'isOptional' => false,
          ),
          'default' => 
          array (
            'name' => 'default',
            'default' => 
            array (
              'code' => 'null',
              'attributes' => 
              array (
                'startLine' => 444,
                'endLine' => 444,
                'startTokenPos' => 1380,
                'startFilePos' => 10602,
                'endTokenPos' => 1380,
                'endFilePos' => 10605,
              ),
            ),
            'type' => NULL,
            'isVariadic' => false,
            'byRef' => false,
            'isPromoted' => false,
            'attributes' => 
            array (
            ),
            'startLine' => 444,
            'endLine' => 444,
            'startColumn' => 46,
            'endColumn' => 60,
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
 * Get original value of a given parameter from the route.
 *
 * @param  string  $name
 * @param  string|null  $default
 * @return string|null
 */',
        'startLine' => 444,
        'endLine' => 447,
        'startColumn' => 5,
        'endColumn' => 5,
        'couldThrow' => false,
        'isClosure' => false,
        'isGenerator' => false,
        'isVariadic' => false,
        'modifiers' => 1,
        'namespace' => 'Illuminate\\Routing',
        'declaringClassName' => 'Illuminate\\Routing\\Route',
        'implementingClassName' => 'Illuminate\\Routing\\Route',
        'currentClassName' => 'Illuminate\\Routing\\Route',
        'aliasName' => NULL,
      ),
      'setParameter' => 
      array (
        'name' => 'setParameter',
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
            'startLine' => 456,
            'endLine' => 456,
            'startColumn' => 34,
            'endColumn' => 38,
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
            'startLine' => 456,
            'endLine' => 456,
            'startColumn' => 41,
            'endColumn' => 46,
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
 * Set a parameter to the given value.
 *
 * @param  string  $name
 * @param  string|object|null  $value
 * @return void
 */',
        'startLine' => 456,
        'endLine' => 461,
        'startColumn' => 5,
        'endColumn' => 5,
        'couldThrow' => false,
        'isClosure' => false,
        'isGenerator' => false,
        'isVariadic' => false,
        'modifiers' => 1,
        'namespace' => 'Illuminate\\Routing',
        'declaringClassName' => 'Illuminate\\Routing\\Route',
        'implementingClassName' => 'Illuminate\\Routing\\Route',
        'currentClassName' => 'Illuminate\\Routing\\Route',
        'aliasName' => NULL,
      ),
      'forgetParameter' => 
      array (
        'name' => 'forgetParameter',
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
            'startLine' => 469,
            'endLine' => 469,
            'startColumn' => 37,
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
 * Unset a parameter on the route if it is set.
 *
 * @param  string  $name
 * @return void
 */',
        'startLine' => 469,
        'endLine' => 474,
        'startColumn' => 5,
        'endColumn' => 5,
        'couldThrow' => false,
        'isClosure' => false,
        'isGenerator' => false,
        'isVariadic' => false,
        'modifiers' => 1,
        'namespace' => 'Illuminate\\Routing',
        'declaringClassName' => 'Illuminate\\Routing\\Route',
        'implementingClassName' => 'Illuminate\\Routing\\Route',
        'currentClassName' => 'Illuminate\\Routing\\Route',
        'aliasName' => NULL,
      ),
      'parameters' => 
      array (
        'name' => 'parameters',
        'parameters' => 
        array (
        ),
        'returnsReference' => false,
        'returnType' => NULL,
        'attributes' => 
        array (
        ),
        'docComment' => '/**
 * Get the key / value list of parameters for the route.
 *
 * @return array
 *
 * @throws \\LogicException
 */',
        'startLine' => 483,
        'endLine' => 490,
        'startColumn' => 5,
        'endColumn' => 5,
        'couldThrow' => false,
        'isClosure' => false,
        'isGenerator' => false,
        'isVariadic' => false,
        'modifiers' => 1,
        'namespace' => 'Illuminate\\Routing',
        'declaringClassName' => 'Illuminate\\Routing\\Route',
        'implementingClassName' => 'Illuminate\\Routing\\Route',
        'currentClassName' => 'Illuminate\\Routing\\Route',
        'aliasName' => NULL,
      ),
      'originalParameters' => 
      array (
        'name' => 'originalParameters',
        'parameters' => 
        array (
        ),
        'returnsReference' => false,
        'returnType' => NULL,
        'attributes' => 
        array (
        ),
        'docComment' => '/**
 * Get the key / value list of original parameters for the route.
 *
 * @return array
 *
 * @throws \\LogicException
 */',
        'startLine' => 499,
        'endLine' => 506,
        'startColumn' => 5,
        'endColumn' => 5,
        'couldThrow' => false,
        'isClosure' => false,
        'isGenerator' => false,
        'isVariadic' => false,
        'modifiers' => 1,
        'namespace' => 'Illuminate\\Routing',
        'declaringClassName' => 'Illuminate\\Routing\\Route',
        'implementingClassName' => 'Illuminate\\Routing\\Route',
        'currentClassName' => 'Illuminate\\Routing\\Route',
        'aliasName' => NULL,
      ),
      'parametersWithoutNulls' => 
      array (
        'name' => 'parametersWithoutNulls',
        'parameters' => 
        array (
        ),
        'returnsReference' => false,
        'returnType' => NULL,
        'attributes' => 
        array (
        ),
        'docComment' => '/**
 * Get the key / value list of parameters without null values.
 *
 * @return array
 */',
        'startLine' => 513,
        'endLine' => 516,
        'startColumn' => 5,
        'endColumn' => 5,
        'couldThrow' => false,
        'isClosure' => false,
        'isGenerator' => false,
        'isVariadic' => false,
        'modifiers' => 1,
        'namespace' => 'Illuminate\\Routing',
        'declaringClassName' => 'Illuminate\\Routing\\Route',
        'implementingClassName' => 'Illuminate\\Routing\\Route',
        'currentClassName' => 'Illuminate\\Routing\\Route',
        'aliasName' => NULL,
      ),
      'parameterNames' => 
      array (
        'name' => 'parameterNames',
        'parameters' => 
        array (
        ),
        'returnsReference' => false,
        'returnType' => NULL,
        'attributes' => 
        array (
        ),
        'docComment' => '/**
 * Get all of the parameter names for the route.
 *
 * @return array
 */',
        'startLine' => 523,
        'endLine' => 526,
        'startColumn' => 5,
        'endColumn' => 5,
        'couldThrow' => false,
        'isClosure' => false,
        'isGenerator' => false,
        'isVariadic' => false,
        'modifiers' => 1,
        'namespace' => 'Illuminate\\Routing',
        'declaringClassName' => 'Illuminate\\Routing\\Route',
        'implementingClassName' => 'Illuminate\\Routing\\Route',
        'currentClassName' => 'Illuminate\\Routing\\Route',
        'aliasName' => NULL,
      ),
      'compileParameterNames' => 
      array (
        'name' => 'compileParameterNames',
        'parameters' => 
        array (
        ),
        'returnsReference' => false,
        'returnType' => NULL,
        'attributes' => 
        array (
        ),
        'docComment' => '/**
 * Get the parameter names for the route.
 *
 * @return array
 */',
        'startLine' => 533,
        'endLine' => 538,
        'startColumn' => 5,
        'endColumn' => 5,
        'couldThrow' => false,
        'isClosure' => false,
        'isGenerator' => false,
        'isVariadic' => false,
        'modifiers' => 2,
        'namespace' => 'Illuminate\\Routing',
        'declaringClassName' => 'Illuminate\\Routing\\Route',
        'implementingClassName' => 'Illuminate\\Routing\\Route',
        'currentClassName' => 'Illuminate\\Routing\\Route',
        'aliasName' => NULL,
      ),
      'signatureParameters' => 
      array (
        'name' => 'signatureParameters',
        'parameters' => 
        array (
          'conditions' => 
          array (
            'name' => 'conditions',
            'default' => 
            array (
              'code' => '[]',
              'attributes' => 
              array (
                'startLine' => 546,
                'endLine' => 546,
                'startTokenPos' => 1720,
                'startFilePos' => 12932,
                'endTokenPos' => 1721,
                'endFilePos' => 12933,
              ),
            ),
            'type' => NULL,
            'isVariadic' => false,
            'byRef' => false,
            'isPromoted' => false,
            'attributes' => 
            array (
            ),
            'startLine' => 546,
            'endLine' => 546,
            'startColumn' => 41,
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
 * Get the parameters that are listed in the route / controller signature.
 *
 * @param  array  $conditions
 * @return array
 */',
        'startLine' => 546,
        'endLine' => 553,
        'startColumn' => 5,
        'endColumn' => 5,
        'couldThrow' => false,
        'isClosure' => false,
        'isGenerator' => false,
        'isVariadic' => false,
        'modifiers' => 1,
        'namespace' => 'Illuminate\\Routing',
        'declaringClassName' => 'Illuminate\\Routing\\Route',
        'implementingClassName' => 'Illuminate\\Routing\\Route',
        'currentClassName' => 'Illuminate\\Routing\\Route',
        'aliasName' => NULL,
      ),
      'bindingFieldFor' => 
      array (
        'name' => 'bindingFieldFor',
        'parameters' => 
        array (
          'parameter' => 
          array (
            'name' => 'parameter',
            'default' => NULL,
            'type' => NULL,
            'isVariadic' => false,
            'byRef' => false,
            'isPromoted' => false,
            'attributes' => 
            array (
            ),
            'startLine' => 561,
            'endLine' => 561,
            'startColumn' => 37,
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
 * Get the binding field for the given parameter.
 *
 * @param  string|int  $parameter
 * @return string|null
 */',
        'startLine' => 561,
        'endLine' => 566,
        'startColumn' => 5,
        'endColumn' => 5,
        'couldThrow' => false,
        'isClosure' => false,
        'isGenerator' => false,
        'isVariadic' => false,
        'modifiers' => 1,
        'namespace' => 'Illuminate\\Routing',
        'declaringClassName' => 'Illuminate\\Routing\\Route',
        'implementingClassName' => 'Illuminate\\Routing\\Route',
        'currentClassName' => 'Illuminate\\Routing\\Route',
        'aliasName' => NULL,
      ),
      'bindingFields' => 
      array (
        'name' => 'bindingFields',
        'parameters' => 
        array (
        ),
        'returnsReference' => false,
        'returnType' => NULL,
        'attributes' => 
        array (
        ),
        'docComment' => '/**
 * Get the binding fields for the route.
 *
 * @return array
 */',
        'startLine' => 573,
        'endLine' => 576,
        'startColumn' => 5,
        'endColumn' => 5,
        'couldThrow' => false,
        'isClosure' => false,
        'isGenerator' => false,
        'isVariadic' => false,
        'modifiers' => 1,
        'namespace' => 'Illuminate\\Routing',
        'declaringClassName' => 'Illuminate\\Routing\\Route',
        'implementingClassName' => 'Illuminate\\Routing\\Route',
        'currentClassName' => 'Illuminate\\Routing\\Route',
        'aliasName' => NULL,
      ),
      'setBindingFields' => 
      array (
        'name' => 'setBindingFields',
        'parameters' => 
        array (
          'bindingFields' => 
          array (
            'name' => 'bindingFields',
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
            'startLine' => 584,
            'endLine' => 584,
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
 * Set the binding fields for the route.
 *
 * @param  array  $bindingFields
 * @return $this
 */',
        'startLine' => 584,
        'endLine' => 589,
        'startColumn' => 5,
        'endColumn' => 5,
        'couldThrow' => false,
        'isClosure' => false,
        'isGenerator' => false,
        'isVariadic' => false,
        'modifiers' => 1,
        'namespace' => 'Illuminate\\Routing',
        'declaringClassName' => 'Illuminate\\Routing\\Route',
        'implementingClassName' => 'Illuminate\\Routing\\Route',
        'currentClassName' => 'Illuminate\\Routing\\Route',
        'aliasName' => NULL,
      ),
      'parentOfParameter' => 
      array (
        'name' => 'parentOfParameter',
        'parameters' => 
        array (
          'parameter' => 
          array (
            'name' => 'parameter',
            'default' => NULL,
            'type' => NULL,
            'isVariadic' => false,
            'byRef' => false,
            'isPromoted' => false,
            'attributes' => 
            array (
            ),
            'startLine' => 597,
            'endLine' => 597,
            'startColumn' => 39,
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
 * Get the parent parameter of the given parameter.
 *
 * @param  string  $parameter
 * @return string|null
 */',
        'startLine' => 597,
        'endLine' => 606,
        'startColumn' => 5,
        'endColumn' => 5,
        'couldThrow' => false,
        'isClosure' => false,
        'isGenerator' => false,
        'isVariadic' => false,
        'modifiers' => 1,
        'namespace' => 'Illuminate\\Routing',
        'declaringClassName' => 'Illuminate\\Routing\\Route',
        'implementingClassName' => 'Illuminate\\Routing\\Route',
        'currentClassName' => 'Illuminate\\Routing\\Route',
        'aliasName' => NULL,
      ),
      'withTrashed' => 
      array (
        'name' => 'withTrashed',
        'parameters' => 
        array (
          'withTrashed' => 
          array (
            'name' => 'withTrashed',
            'default' => 
            array (
              'code' => 'true',
              'attributes' => 
              array (
                'startLine' => 614,
                'endLine' => 614,
                'startTokenPos' => 1965,
                'startFilePos' => 14564,
                'endTokenPos' => 1965,
                'endFilePos' => 14567,
              ),
            ),
            'type' => NULL,
            'isVariadic' => false,
            'byRef' => false,
            'isPromoted' => false,
            'attributes' => 
            array (
            ),
            'startLine' => 614,
            'endLine' => 614,
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
 * Allow "trashed" models to be retrieved when resolving implicit model bindings for this route.
 *
 * @param  bool  $withTrashed
 * @return $this
 */',
        'startLine' => 614,
        'endLine' => 619,
        'startColumn' => 5,
        'endColumn' => 5,
        'couldThrow' => false,
        'isClosure' => false,
        'isGenerator' => false,
        'isVariadic' => false,
        'modifiers' => 1,
        'namespace' => 'Illuminate\\Routing',
        'declaringClassName' => 'Illuminate\\Routing\\Route',
        'implementingClassName' => 'Illuminate\\Routing\\Route',
        'currentClassName' => 'Illuminate\\Routing\\Route',
        'aliasName' => NULL,
      ),
      'allowsTrashedBindings' => 
      array (
        'name' => 'allowsTrashedBindings',
        'parameters' => 
        array (
        ),
        'returnsReference' => false,
        'returnType' => NULL,
        'attributes' => 
        array (
        ),
        'docComment' => '/**
 * Determines if the route allows "trashed" models to be retrieved when resolving implicit model bindings.
 *
 * @return bool
 */',
        'startLine' => 626,
        'endLine' => 629,
        'startColumn' => 5,
        'endColumn' => 5,
        'couldThrow' => false,
        'isClosure' => false,
        'isGenerator' => false,
        'isVariadic' => false,
        'modifiers' => 1,
        'namespace' => 'Illuminate\\Routing',
        'declaringClassName' => 'Illuminate\\Routing\\Route',
        'implementingClassName' => 'Illuminate\\Routing\\Route',
        'currentClassName' => 'Illuminate\\Routing\\Route',
        'aliasName' => NULL,
      ),
      'defaults' => 
      array (
        'name' => 'defaults',
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
            'startLine' => 638,
            'endLine' => 638,
            'startColumn' => 30,
            'endColumn' => 33,
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
            'startLine' => 638,
            'endLine' => 638,
            'startColumn' => 36,
            'endColumn' => 41,
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
 * Set a default value for the route.
 *
 * @param  string  $key
 * @param  mixed  $value
 * @return $this
 */',
        'startLine' => 638,
        'endLine' => 643,
        'startColumn' => 5,
        'endColumn' => 5,
        'couldThrow' => false,
        'isClosure' => false,
        'isGenerator' => false,
        'isVariadic' => false,
        'modifiers' => 1,
        'namespace' => 'Illuminate\\Routing',
        'declaringClassName' => 'Illuminate\\Routing\\Route',
        'implementingClassName' => 'Illuminate\\Routing\\Route',
        'currentClassName' => 'Illuminate\\Routing\\Route',
        'aliasName' => NULL,
      ),
      'setDefaults' => 
      array (
        'name' => 'setDefaults',
        'parameters' => 
        array (
          'defaults' => 
          array (
            'name' => 'defaults',
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
            'startLine' => 651,
            'endLine' => 651,
            'startColumn' => 33,
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
 * Set the default values for the route.
 *
 * @param  array  $defaults
 * @return $this
 */',
        'startLine' => 651,
        'endLine' => 656,
        'startColumn' => 5,
        'endColumn' => 5,
        'couldThrow' => false,
        'isClosure' => false,
        'isGenerator' => false,
        'isVariadic' => false,
        'modifiers' => 1,
        'namespace' => 'Illuminate\\Routing',
        'declaringClassName' => 'Illuminate\\Routing\\Route',
        'implementingClassName' => 'Illuminate\\Routing\\Route',
        'currentClassName' => 'Illuminate\\Routing\\Route',
        'aliasName' => NULL,
      ),
      'where' => 
      array (
        'name' => 'where',
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
            'startLine' => 665,
            'endLine' => 665,
            'startColumn' => 27,
            'endColumn' => 31,
            'parameterIndex' => 0,
            'isOptional' => false,
          ),
          'expression' => 
          array (
            'name' => 'expression',
            'default' => 
            array (
              'code' => 'null',
              'attributes' => 
              array (
                'startLine' => 665,
                'endLine' => 665,
                'startTokenPos' => 2088,
                'startFilePos' => 15640,
                'endTokenPos' => 2088,
                'endFilePos' => 15643,
              ),
            ),
            'type' => NULL,
            'isVariadic' => false,
            'byRef' => false,
            'isPromoted' => false,
            'attributes' => 
            array (
            ),
            'startLine' => 665,
            'endLine' => 665,
            'startColumn' => 34,
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
 * Set a regular expression requirement on the route.
 *
 * @param  array|string  $name
 * @param  string|null  $expression
 * @return $this
 */',
        'startLine' => 665,
        'endLine' => 672,
        'startColumn' => 5,
        'endColumn' => 5,
        'couldThrow' => false,
        'isClosure' => false,
        'isGenerator' => false,
        'isVariadic' => false,
        'modifiers' => 1,
        'namespace' => 'Illuminate\\Routing',
        'declaringClassName' => 'Illuminate\\Routing\\Route',
        'implementingClassName' => 'Illuminate\\Routing\\Route',
        'currentClassName' => 'Illuminate\\Routing\\Route',
        'aliasName' => NULL,
      ),
      'parseWhere' => 
      array (
        'name' => 'parseWhere',
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
            'startLine' => 681,
            'endLine' => 681,
            'startColumn' => 35,
            'endColumn' => 39,
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
            'startLine' => 681,
            'endLine' => 681,
            'startColumn' => 42,
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
 * Parse arguments to the where method into an array.
 *
 * @param  array|string  $name
 * @param  string  $expression
 * @return array
 */',
        'startLine' => 681,
        'endLine' => 684,
        'startColumn' => 5,
        'endColumn' => 5,
        'couldThrow' => false,
        'isClosure' => false,
        'isGenerator' => false,
        'isVariadic' => false,
        'modifiers' => 2,
        'namespace' => 'Illuminate\\Routing',
        'declaringClassName' => 'Illuminate\\Routing\\Route',
        'implementingClassName' => 'Illuminate\\Routing\\Route',
        'currentClassName' => 'Illuminate\\Routing\\Route',
        'aliasName' => NULL,
      ),
      'setWheres' => 
      array (
        'name' => 'setWheres',
        'parameters' => 
        array (
          'wheres' => 
          array (
            'name' => 'wheres',
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
            'startLine' => 692,
            'endLine' => 692,
            'startColumn' => 31,
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
 * Set a list of regular expression requirements on the route.
 *
 * @param  array  $wheres
 * @return $this
 */',
        'startLine' => 692,
        'endLine' => 699,
        'startColumn' => 5,
        'endColumn' => 5,
        'couldThrow' => false,
        'isClosure' => false,
        'isGenerator' => false,
        'isVariadic' => false,
        'modifiers' => 1,
        'namespace' => 'Illuminate\\Routing',
        'declaringClassName' => 'Illuminate\\Routing\\Route',
        'implementingClassName' => 'Illuminate\\Routing\\Route',
        'currentClassName' => 'Illuminate\\Routing\\Route',
        'aliasName' => NULL,
      ),
      'fallback' => 
      array (
        'name' => 'fallback',
        'parameters' => 
        array (
        ),
        'returnsReference' => false,
        'returnType' => NULL,
        'attributes' => 
        array (
        ),
        'docComment' => '/**
 * Mark this route as a fallback route.
 *
 * @return $this
 */',
        'startLine' => 706,
        'endLine' => 711,
        'startColumn' => 5,
        'endColumn' => 5,
        'couldThrow' => false,
        'isClosure' => false,
        'isGenerator' => false,
        'isVariadic' => false,
        'modifiers' => 1,
        'namespace' => 'Illuminate\\Routing',
        'declaringClassName' => 'Illuminate\\Routing\\Route',
        'implementingClassName' => 'Illuminate\\Routing\\Route',
        'currentClassName' => 'Illuminate\\Routing\\Route',
        'aliasName' => NULL,
      ),
      'setFallback' => 
      array (
        'name' => 'setFallback',
        'parameters' => 
        array (
          'isFallback' => 
          array (
            'name' => 'isFallback',
            'default' => NULL,
            'type' => NULL,
            'isVariadic' => false,
            'byRef' => false,
            'isPromoted' => false,
            'attributes' => 
            array (
            ),
            'startLine' => 719,
            'endLine' => 719,
            'startColumn' => 33,
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
 * Set the fallback value.
 *
 * @param  bool  $isFallback
 * @return $this
 */',
        'startLine' => 719,
        'endLine' => 724,
        'startColumn' => 5,
        'endColumn' => 5,
        'couldThrow' => false,
        'isClosure' => false,
        'isGenerator' => false,
        'isVariadic' => false,
        'modifiers' => 1,
        'namespace' => 'Illuminate\\Routing',
        'declaringClassName' => 'Illuminate\\Routing\\Route',
        'implementingClassName' => 'Illuminate\\Routing\\Route',
        'currentClassName' => 'Illuminate\\Routing\\Route',
        'aliasName' => NULL,
      ),
      'methods' => 
      array (
        'name' => 'methods',
        'parameters' => 
        array (
        ),
        'returnsReference' => false,
        'returnType' => NULL,
        'attributes' => 
        array (
        ),
        'docComment' => '/**
 * Get the HTTP verbs the route responds to.
 *
 * @return array
 */',
        'startLine' => 731,
        'endLine' => 734,
        'startColumn' => 5,
        'endColumn' => 5,
        'couldThrow' => false,
        'isClosure' => false,
        'isGenerator' => false,
        'isVariadic' => false,
        'modifiers' => 1,
        'namespace' => 'Illuminate\\Routing',
        'declaringClassName' => 'Illuminate\\Routing\\Route',
        'implementingClassName' => 'Illuminate\\Routing\\Route',
        'currentClassName' => 'Illuminate\\Routing\\Route',
        'aliasName' => NULL,
      ),
      'httpOnly' => 
      array (
        'name' => 'httpOnly',
        'parameters' => 
        array (
        ),
        'returnsReference' => false,
        'returnType' => NULL,
        'attributes' => 
        array (
        ),
        'docComment' => '/**
 * Determine if the route only responds to HTTP requests.
 *
 * @return bool
 */',
        'startLine' => 741,
        'endLine' => 744,
        'startColumn' => 5,
        'endColumn' => 5,
        'couldThrow' => false,
        'isClosure' => false,
        'isGenerator' => false,
        'isVariadic' => false,
        'modifiers' => 1,
        'namespace' => 'Illuminate\\Routing',
        'declaringClassName' => 'Illuminate\\Routing\\Route',
        'implementingClassName' => 'Illuminate\\Routing\\Route',
        'currentClassName' => 'Illuminate\\Routing\\Route',
        'aliasName' => NULL,
      ),
      'httpsOnly' => 
      array (
        'name' => 'httpsOnly',
        'parameters' => 
        array (
        ),
        'returnsReference' => false,
        'returnType' => NULL,
        'attributes' => 
        array (
        ),
        'docComment' => '/**
 * Determine if the route only responds to HTTPS requests.
 *
 * @return bool
 */',
        'startLine' => 751,
        'endLine' => 754,
        'startColumn' => 5,
        'endColumn' => 5,
        'couldThrow' => false,
        'isClosure' => false,
        'isGenerator' => false,
        'isVariadic' => false,
        'modifiers' => 1,
        'namespace' => 'Illuminate\\Routing',
        'declaringClassName' => 'Illuminate\\Routing\\Route',
        'implementingClassName' => 'Illuminate\\Routing\\Route',
        'currentClassName' => 'Illuminate\\Routing\\Route',
        'aliasName' => NULL,
      ),
      'secure' => 
      array (
        'name' => 'secure',
        'parameters' => 
        array (
        ),
        'returnsReference' => false,
        'returnType' => NULL,
        'attributes' => 
        array (
        ),
        'docComment' => '/**
 * Determine if the route only responds to HTTPS requests.
 *
 * @return bool
 */',
        'startLine' => 761,
        'endLine' => 764,
        'startColumn' => 5,
        'endColumn' => 5,
        'couldThrow' => false,
        'isClosure' => false,
        'isGenerator' => false,
        'isVariadic' => false,
        'modifiers' => 1,
        'namespace' => 'Illuminate\\Routing',
        'declaringClassName' => 'Illuminate\\Routing\\Route',
        'implementingClassName' => 'Illuminate\\Routing\\Route',
        'currentClassName' => 'Illuminate\\Routing\\Route',
        'aliasName' => NULL,
      ),
      'domain' => 
      array (
        'name' => 'domain',
        'parameters' => 
        array (
          'domain' => 
          array (
            'name' => 'domain',
            'default' => 
            array (
              'code' => 'null',
              'attributes' => 
              array (
                'startLine' => 774,
                'endLine' => 774,
                'startTokenPos' => 2402,
                'startFilePos' => 17886,
                'endTokenPos' => 2402,
                'endFilePos' => 17889,
              ),
            ),
            'type' => NULL,
            'isVariadic' => false,
            'byRef' => false,
            'isPromoted' => false,
            'attributes' => 
            array (
            ),
            'startLine' => 774,
            'endLine' => 774,
            'startColumn' => 28,
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
 * Get or set the domain for the route.
 *
 * @param  \\BackedEnum|string|null  $domain
 * @return ($domain is null ? string|null : $this)
 *
 * @throws \\InvalidArgumentException
 */',
        'startLine' => 774,
        'endLine' => 793,
        'startColumn' => 5,
        'endColumn' => 5,
        'couldThrow' => false,
        'isClosure' => false,
        'isGenerator' => false,
        'isVariadic' => false,
        'modifiers' => 1,
        'namespace' => 'Illuminate\\Routing',
        'declaringClassName' => 'Illuminate\\Routing\\Route',
        'implementingClassName' => 'Illuminate\\Routing\\Route',
        'currentClassName' => 'Illuminate\\Routing\\Route',
        'aliasName' => NULL,
      ),
      'getDomain' => 
      array (
        'name' => 'getDomain',
        'parameters' => 
        array (
        ),
        'returnsReference' => false,
        'returnType' => NULL,
        'attributes' => 
        array (
        ),
        'docComment' => '/**
 * Get the domain defined for the route.
 *
 * @return string|null
 */',
        'startLine' => 800,
        'endLine' => 805,
        'startColumn' => 5,
        'endColumn' => 5,
        'couldThrow' => false,
        'isClosure' => false,
        'isGenerator' => false,
        'isVariadic' => false,
        'modifiers' => 1,
        'namespace' => 'Illuminate\\Routing',
        'declaringClassName' => 'Illuminate\\Routing\\Route',
        'implementingClassName' => 'Illuminate\\Routing\\Route',
        'currentClassName' => 'Illuminate\\Routing\\Route',
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
 * Get the prefix of the route instance.
 *
 * @return string|null
 */',
        'startLine' => 812,
        'endLine' => 815,
        'startColumn' => 5,
        'endColumn' => 5,
        'couldThrow' => false,
        'isClosure' => false,
        'isGenerator' => false,
        'isVariadic' => false,
        'modifiers' => 1,
        'namespace' => 'Illuminate\\Routing',
        'declaringClassName' => 'Illuminate\\Routing\\Route',
        'implementingClassName' => 'Illuminate\\Routing\\Route',
        'currentClassName' => 'Illuminate\\Routing\\Route',
        'aliasName' => NULL,
      ),
      'prefix' => 
      array (
        'name' => 'prefix',
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
            'startLine' => 823,
            'endLine' => 823,
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
 * Add a prefix to the route URI.
 *
 * @param  string|null  $prefix
 * @return $this
 */',
        'startLine' => 823,
        'endLine' => 832,
        'startColumn' => 5,
        'endColumn' => 5,
        'couldThrow' => false,
        'isClosure' => false,
        'isGenerator' => false,
        'isVariadic' => false,
        'modifiers' => 1,
        'namespace' => 'Illuminate\\Routing',
        'declaringClassName' => 'Illuminate\\Routing\\Route',
        'implementingClassName' => 'Illuminate\\Routing\\Route',
        'currentClassName' => 'Illuminate\\Routing\\Route',
        'aliasName' => NULL,
      ),
      'updatePrefixOnAction' => 
      array (
        'name' => 'updatePrefixOnAction',
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
            'startLine' => 840,
            'endLine' => 840,
            'startColumn' => 45,
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
 * Update the "prefix" attribute on the action array.
 *
 * @param  string  $prefix
 * @return void
 */',
        'startLine' => 840,
        'endLine' => 845,
        'startColumn' => 5,
        'endColumn' => 5,
        'couldThrow' => false,
        'isClosure' => false,
        'isGenerator' => false,
        'isVariadic' => false,
        'modifiers' => 2,
        'namespace' => 'Illuminate\\Routing',
        'declaringClassName' => 'Illuminate\\Routing\\Route',
        'implementingClassName' => 'Illuminate\\Routing\\Route',
        'currentClassName' => 'Illuminate\\Routing\\Route',
        'aliasName' => NULL,
      ),
      'uri' => 
      array (
        'name' => 'uri',
        'parameters' => 
        array (
        ),
        'returnsReference' => false,
        'returnType' => NULL,
        'attributes' => 
        array (
        ),
        'docComment' => '/**
 * Get the URI associated with the route.
 *
 * @return string
 */',
        'startLine' => 852,
        'endLine' => 855,
        'startColumn' => 5,
        'endColumn' => 5,
        'couldThrow' => false,
        'isClosure' => false,
        'isGenerator' => false,
        'isVariadic' => false,
        'modifiers' => 1,
        'namespace' => 'Illuminate\\Routing',
        'declaringClassName' => 'Illuminate\\Routing\\Route',
        'implementingClassName' => 'Illuminate\\Routing\\Route',
        'currentClassName' => 'Illuminate\\Routing\\Route',
        'aliasName' => NULL,
      ),
      'setUri' => 
      array (
        'name' => 'setUri',
        'parameters' => 
        array (
          'uri' => 
          array (
            'name' => 'uri',
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
 * Set the URI that the route responds to.
 *
 * @param  string  $uri
 * @return $this
 */',
        'startLine' => 863,
        'endLine' => 868,
        'startColumn' => 5,
        'endColumn' => 5,
        'couldThrow' => false,
        'isClosure' => false,
        'isGenerator' => false,
        'isVariadic' => false,
        'modifiers' => 1,
        'namespace' => 'Illuminate\\Routing',
        'declaringClassName' => 'Illuminate\\Routing\\Route',
        'implementingClassName' => 'Illuminate\\Routing\\Route',
        'currentClassName' => 'Illuminate\\Routing\\Route',
        'aliasName' => NULL,
      ),
      'parseUri' => 
      array (
        'name' => 'parseUri',
        'parameters' => 
        array (
          'uri' => 
          array (
            'name' => 'uri',
            'default' => NULL,
            'type' => NULL,
            'isVariadic' => false,
            'byRef' => false,
            'isPromoted' => false,
            'attributes' => 
            array (
            ),
            'startLine' => 876,
            'endLine' => 876,
            'startColumn' => 33,
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
 * Parse the route URI and normalize / store any implicit binding fields.
 *
 * @param  string  $uri
 * @return string
 */',
        'startLine' => 876,
        'endLine' => 883,
        'startColumn' => 5,
        'endColumn' => 5,
        'couldThrow' => false,
        'isClosure' => false,
        'isGenerator' => false,
        'isVariadic' => false,
        'modifiers' => 2,
        'namespace' => 'Illuminate\\Routing',
        'declaringClassName' => 'Illuminate\\Routing\\Route',
        'implementingClassName' => 'Illuminate\\Routing\\Route',
        'currentClassName' => 'Illuminate\\Routing\\Route',
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
 * Get the name of the route instance.
 *
 * @return string|null
 */',
        'startLine' => 890,
        'endLine' => 893,
        'startColumn' => 5,
        'endColumn' => 5,
        'couldThrow' => false,
        'isClosure' => false,
        'isGenerator' => false,
        'isVariadic' => false,
        'modifiers' => 1,
        'namespace' => 'Illuminate\\Routing',
        'declaringClassName' => 'Illuminate\\Routing\\Route',
        'implementingClassName' => 'Illuminate\\Routing\\Route',
        'currentClassName' => 'Illuminate\\Routing\\Route',
        'aliasName' => NULL,
      ),
      'name' => 
      array (
        'name' => 'name',
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
            'startLine' => 903,
            'endLine' => 903,
            'startColumn' => 26,
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
 * Add or change the route name.
 *
 * @param  \\BackedEnum|string  $name
 * @return $this
 *
 * @throws \\InvalidArgumentException
 */',
        'startLine' => 903,
        'endLine' => 912,
        'startColumn' => 5,
        'endColumn' => 5,
        'couldThrow' => false,
        'isClosure' => false,
        'isGenerator' => false,
        'isVariadic' => false,
        'modifiers' => 1,
        'namespace' => 'Illuminate\\Routing',
        'declaringClassName' => 'Illuminate\\Routing\\Route',
        'implementingClassName' => 'Illuminate\\Routing\\Route',
        'currentClassName' => 'Illuminate\\Routing\\Route',
        'aliasName' => NULL,
      ),
      'named' => 
      array (
        'name' => 'named',
        'parameters' => 
        array (
          'patterns' => 
          array (
            'name' => 'patterns',
            'default' => NULL,
            'type' => NULL,
            'isVariadic' => true,
            'byRef' => false,
            'isPromoted' => false,
            'attributes' => 
            array (
            ),
            'startLine' => 920,
            'endLine' => 920,
            'startColumn' => 27,
            'endColumn' => 38,
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
 * Determine whether the route\'s name matches the given patterns.
 *
 * @param  mixed  ...$patterns
 * @return bool
 */',
        'startLine' => 920,
        'endLine' => 927,
        'startColumn' => 5,
        'endColumn' => 5,
        'couldThrow' => false,
        'isClosure' => false,
        'isGenerator' => false,
        'isVariadic' => true,
        'modifiers' => 1,
        'namespace' => 'Illuminate\\Routing',
        'declaringClassName' => 'Illuminate\\Routing\\Route',
        'implementingClassName' => 'Illuminate\\Routing\\Route',
        'currentClassName' => 'Illuminate\\Routing\\Route',
        'aliasName' => NULL,
      ),
      'uses' => 
      array (
        'name' => 'uses',
        'parameters' => 
        array (
          'action' => 
          array (
            'name' => 'action',
            'default' => NULL,
            'type' => NULL,
            'isVariadic' => false,
            'byRef' => false,
            'isPromoted' => false,
            'attributes' => 
            array (
            ),
            'startLine' => 935,
            'endLine' => 935,
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
 * Set the handler for the route.
 *
 * @param  \\Closure|array|string  $action
 * @return $this
 */',
        'startLine' => 935,
        'endLine' => 947,
        'startColumn' => 5,
        'endColumn' => 5,
        'couldThrow' => false,
        'isClosure' => false,
        'isGenerator' => false,
        'isVariadic' => false,
        'modifiers' => 1,
        'namespace' => 'Illuminate\\Routing',
        'declaringClassName' => 'Illuminate\\Routing\\Route',
        'implementingClassName' => 'Illuminate\\Routing\\Route',
        'currentClassName' => 'Illuminate\\Routing\\Route',
        'aliasName' => NULL,
      ),
      'addGroupNamespaceToStringUses' => 
      array (
        'name' => 'addGroupNamespaceToStringUses',
        'parameters' => 
        array (
          'action' => 
          array (
            'name' => 'action',
            'default' => NULL,
            'type' => NULL,
            'isVariadic' => false,
            'byRef' => false,
            'isPromoted' => false,
            'attributes' => 
            array (
            ),
            'startLine' => 955,
            'endLine' => 955,
            'startColumn' => 54,
            'endColumn' => 60,
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
 * Parse a string based action for the "uses" fluent method.
 *
 * @param  string  $action
 * @return string
 */',
        'startLine' => 955,
        'endLine' => 964,
        'startColumn' => 5,
        'endColumn' => 5,
        'couldThrow' => false,
        'isClosure' => false,
        'isGenerator' => false,
        'isVariadic' => false,
        'modifiers' => 2,
        'namespace' => 'Illuminate\\Routing',
        'declaringClassName' => 'Illuminate\\Routing\\Route',
        'implementingClassName' => 'Illuminate\\Routing\\Route',
        'currentClassName' => 'Illuminate\\Routing\\Route',
        'aliasName' => NULL,
      ),
      'getActionName' => 
      array (
        'name' => 'getActionName',
        'parameters' => 
        array (
        ),
        'returnsReference' => false,
        'returnType' => NULL,
        'attributes' => 
        array (
        ),
        'docComment' => '/**
 * Get the action name for the route.
 *
 * @return string
 */',
        'startLine' => 971,
        'endLine' => 974,
        'startColumn' => 5,
        'endColumn' => 5,
        'couldThrow' => false,
        'isClosure' => false,
        'isGenerator' => false,
        'isVariadic' => false,
        'modifiers' => 1,
        'namespace' => 'Illuminate\\Routing',
        'declaringClassName' => 'Illuminate\\Routing\\Route',
        'implementingClassName' => 'Illuminate\\Routing\\Route',
        'currentClassName' => 'Illuminate\\Routing\\Route',
        'aliasName' => NULL,
      ),
      'getActionMethod' => 
      array (
        'name' => 'getActionMethod',
        'parameters' => 
        array (
        ),
        'returnsReference' => false,
        'returnType' => NULL,
        'attributes' => 
        array (
        ),
        'docComment' => '/**
 * Get the method name of the route action.
 *
 * @return string
 */',
        'startLine' => 981,
        'endLine' => 984,
        'startColumn' => 5,
        'endColumn' => 5,
        'couldThrow' => false,
        'isClosure' => false,
        'isGenerator' => false,
        'isVariadic' => false,
        'modifiers' => 1,
        'namespace' => 'Illuminate\\Routing',
        'declaringClassName' => 'Illuminate\\Routing\\Route',
        'implementingClassName' => 'Illuminate\\Routing\\Route',
        'currentClassName' => 'Illuminate\\Routing\\Route',
        'aliasName' => NULL,
      ),
      'getAction' => 
      array (
        'name' => 'getAction',
        'parameters' => 
        array (
          'key' => 
          array (
            'name' => 'key',
            'default' => 
            array (
              'code' => 'null',
              'attributes' => 
              array (
                'startLine' => 992,
                'endLine' => 992,
                'startTokenPos' => 3329,
                'startFilePos' => 22992,
                'endTokenPos' => 3329,
                'endFilePos' => 22995,
              ),
            ),
            'type' => NULL,
            'isVariadic' => false,
            'byRef' => false,
            'isPromoted' => false,
            'attributes' => 
            array (
            ),
            'startLine' => 992,
            'endLine' => 992,
            'startColumn' => 31,
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
 * Get the action array or one of its properties for the route.
 *
 * @param  string|null  $key
 * @return mixed
 */',
        'startLine' => 992,
        'endLine' => 995,
        'startColumn' => 5,
        'endColumn' => 5,
        'couldThrow' => false,
        'isClosure' => false,
        'isGenerator' => false,
        'isVariadic' => false,
        'modifiers' => 1,
        'namespace' => 'Illuminate\\Routing',
        'declaringClassName' => 'Illuminate\\Routing\\Route',
        'implementingClassName' => 'Illuminate\\Routing\\Route',
        'currentClassName' => 'Illuminate\\Routing\\Route',
        'aliasName' => NULL,
      ),
      'setAction' => 
      array (
        'name' => 'setAction',
        'parameters' => 
        array (
          'action' => 
          array (
            'name' => 'action',
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
            'startLine' => 1003,
            'endLine' => 1003,
            'startColumn' => 31,
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
 * Set the action array for the route.
 *
 * @param  array  $action
 * @return $this
 */',
        'startLine' => 1003,
        'endLine' => 1018,
        'startColumn' => 5,
        'endColumn' => 5,
        'couldThrow' => false,
        'isClosure' => false,
        'isGenerator' => false,
        'isVariadic' => false,
        'modifiers' => 1,
        'namespace' => 'Illuminate\\Routing',
        'declaringClassName' => 'Illuminate\\Routing\\Route',
        'implementingClassName' => 'Illuminate\\Routing\\Route',
        'currentClassName' => 'Illuminate\\Routing\\Route',
        'aliasName' => NULL,
      ),
      'getMissing' => 
      array (
        'name' => 'getMissing',
        'parameters' => 
        array (
        ),
        'returnsReference' => false,
        'returnType' => NULL,
        'attributes' => 
        array (
        ),
        'docComment' => '/**
 * Get the value of the action that should be taken on a missing model exception.
 *
 * @return \\Closure|null
 */',
        'startLine' => 1025,
        'endLine' => 1040,
        'startColumn' => 5,
        'endColumn' => 5,
        'couldThrow' => false,
        'isClosure' => false,
        'isGenerator' => false,
        'isVariadic' => false,
        'modifiers' => 1,
        'namespace' => 'Illuminate\\Routing',
        'declaringClassName' => 'Illuminate\\Routing\\Route',
        'implementingClassName' => 'Illuminate\\Routing\\Route',
        'currentClassName' => 'Illuminate\\Routing\\Route',
        'aliasName' => NULL,
      ),
      'missing' => 
      array (
        'name' => 'missing',
        'parameters' => 
        array (
          'missing' => 
          array (
            'name' => 'missing',
            'default' => NULL,
            'type' => NULL,
            'isVariadic' => false,
            'byRef' => false,
            'isPromoted' => false,
            'attributes' => 
            array (
            ),
            'startLine' => 1048,
            'endLine' => 1048,
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
 * Define the callable that should be invoked on a missing model exception.
 *
 * @param  \\Closure  $missing
 * @return $this
 */',
        'startLine' => 1048,
        'endLine' => 1053,
        'startColumn' => 5,
        'endColumn' => 5,
        'couldThrow' => false,
        'isClosure' => false,
        'isGenerator' => false,
        'isVariadic' => false,
        'modifiers' => 1,
        'namespace' => 'Illuminate\\Routing',
        'declaringClassName' => 'Illuminate\\Routing\\Route',
        'implementingClassName' => 'Illuminate\\Routing\\Route',
        'currentClassName' => 'Illuminate\\Routing\\Route',
        'aliasName' => NULL,
      ),
      'gatherMiddleware' => 
      array (
        'name' => 'gatherMiddleware',
        'parameters' => 
        array (
        ),
        'returnsReference' => false,
        'returnType' => NULL,
        'attributes' => 
        array (
        ),
        'docComment' => '/**
 * Get all middleware, including the ones from the controller.
 *
 * @return array
 */',
        'startLine' => 1060,
        'endLine' => 1071,
        'startColumn' => 5,
        'endColumn' => 5,
        'couldThrow' => false,
        'isClosure' => false,
        'isGenerator' => false,
        'isVariadic' => false,
        'modifiers' => 1,
        'namespace' => 'Illuminate\\Routing',
        'declaringClassName' => 'Illuminate\\Routing\\Route',
        'implementingClassName' => 'Illuminate\\Routing\\Route',
        'currentClassName' => 'Illuminate\\Routing\\Route',
        'aliasName' => NULL,
      ),
      'middleware' => 
      array (
        'name' => 'middleware',
        'parameters' => 
        array (
          'middleware' => 
          array (
            'name' => 'middleware',
            'default' => 
            array (
              'code' => 'null',
              'attributes' => 
              array (
                'startLine' => 1079,
                'endLine' => 1079,
                'startTokenPos' => 3701,
                'startFilePos' => 25449,
                'endTokenPos' => 3701,
                'endFilePos' => 25452,
              ),
            ),
            'type' => NULL,
            'isVariadic' => false,
            'byRef' => false,
            'isPromoted' => false,
            'attributes' => 
            array (
            ),
            'startLine' => 1079,
            'endLine' => 1079,
            'startColumn' => 32,
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
 * Get or set the middlewares attached to the route.
 *
 * @param  array|string|null  $middleware
 * @return ($middleware is null ? array : $this)
 */',
        'startLine' => 1079,
        'endLine' => 1098,
        'startColumn' => 5,
        'endColumn' => 5,
        'couldThrow' => false,
        'isClosure' => false,
        'isGenerator' => false,
        'isVariadic' => true,
        'modifiers' => 1,
        'namespace' => 'Illuminate\\Routing',
        'declaringClassName' => 'Illuminate\\Routing\\Route',
        'implementingClassName' => 'Illuminate\\Routing\\Route',
        'currentClassName' => 'Illuminate\\Routing\\Route',
        'aliasName' => NULL,
      ),
      'can' => 
      array (
        'name' => 'can',
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
            'startLine' => 1107,
            'endLine' => 1107,
            'startColumn' => 25,
            'endColumn' => 32,
            'parameterIndex' => 0,
            'isOptional' => false,
          ),
          'models' => 
          array (
            'name' => 'models',
            'default' => 
            array (
              'code' => '[]',
              'attributes' => 
              array (
                'startLine' => 1107,
                'endLine' => 1107,
                'startTokenPos' => 3848,
                'startFilePos' => 26217,
                'endTokenPos' => 3849,
                'endFilePos' => 26218,
              ),
            ),
            'type' => NULL,
            'isVariadic' => false,
            'byRef' => false,
            'isPromoted' => false,
            'attributes' => 
            array (
            ),
            'startLine' => 1107,
            'endLine' => 1107,
            'startColumn' => 35,
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
 * Specify that the "Authorize" / "can" middleware should be applied to the route with the given options.
 *
 * @param  \\UnitEnum|string  $ability
 * @param  array|string  $models
 * @return $this
 */',
        'startLine' => 1107,
        'endLine' => 1114,
        'startColumn' => 5,
        'endColumn' => 5,
        'couldThrow' => false,
        'isClosure' => false,
        'isGenerator' => false,
        'isVariadic' => false,
        'modifiers' => 1,
        'namespace' => 'Illuminate\\Routing',
        'declaringClassName' => 'Illuminate\\Routing\\Route',
        'implementingClassName' => 'Illuminate\\Routing\\Route',
        'currentClassName' => 'Illuminate\\Routing\\Route',
        'aliasName' => NULL,
      ),
      'controllerMiddleware' => 
      array (
        'name' => 'controllerMiddleware',
        'parameters' => 
        array (
        ),
        'returnsReference' => false,
        'returnType' => NULL,
        'attributes' => 
        array (
        ),
        'docComment' => '/**
 * Get the middleware for the route\'s controller.
 *
 * @return array
 */',
        'startLine' => 1121,
        'endLine' => 1145,
        'startColumn' => 5,
        'endColumn' => 5,
        'couldThrow' => false,
        'isClosure' => false,
        'isGenerator' => false,
        'isVariadic' => false,
        'modifiers' => 1,
        'namespace' => 'Illuminate\\Routing',
        'declaringClassName' => 'Illuminate\\Routing\\Route',
        'implementingClassName' => 'Illuminate\\Routing\\Route',
        'currentClassName' => 'Illuminate\\Routing\\Route',
        'aliasName' => NULL,
      ),
      'staticallyProvidedControllerMiddleware' => 
      array (
        'name' => 'staticallyProvidedControllerMiddleware',
        'parameters' => 
        array (
          'class' => 
          array (
            'name' => 'class',
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
            'startLine' => 1154,
            'endLine' => 1154,
            'startColumn' => 63,
            'endColumn' => 75,
            'parameterIndex' => 0,
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
            'startLine' => 1154,
            'endLine' => 1154,
            'startColumn' => 78,
            'endColumn' => 91,
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
 * Get the statically provided controller middleware for the given class and method.
 *
 * @param  string  $class
 * @param  string  $method
 * @return array
 */',
        'startLine' => 1154,
        'endLine' => 1172,
        'startColumn' => 5,
        'endColumn' => 5,
        'couldThrow' => false,
        'isClosure' => false,
        'isGenerator' => false,
        'isVariadic' => false,
        'modifiers' => 2,
        'namespace' => 'Illuminate\\Routing',
        'declaringClassName' => 'Illuminate\\Routing\\Route',
        'implementingClassName' => 'Illuminate\\Routing\\Route',
        'currentClassName' => 'Illuminate\\Routing\\Route',
        'aliasName' => NULL,
      ),
      'attributeProvidedControllerMiddleware' => 
      array (
        'name' => 'attributeProvidedControllerMiddleware',
        'parameters' => 
        array (
          'class' => 
          array (
            'name' => 'class',
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
            'startLine' => 1179,
            'endLine' => 1179,
            'startColumn' => 62,
            'endColumn' => 74,
            'parameterIndex' => 0,
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
            'startLine' => 1179,
            'endLine' => 1179,
            'startColumn' => 77,
            'endColumn' => 90,
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
 * Get the attribute provided controller middleware for the given class and method.
 *
 * @return array
 */',
        'startLine' => 1179,
        'endLine' => 1216,
        'startColumn' => 5,
        'endColumn' => 5,
        'couldThrow' => false,
        'isClosure' => false,
        'isGenerator' => false,
        'isVariadic' => false,
        'modifiers' => 2,
        'namespace' => 'Illuminate\\Routing',
        'declaringClassName' => 'Illuminate\\Routing\\Route',
        'implementingClassName' => 'Illuminate\\Routing\\Route',
        'currentClassName' => 'Illuminate\\Routing\\Route',
        'aliasName' => NULL,
      ),
      'excludedControllerMiddleware' => 
      array (
        'name' => 'excludedControllerMiddleware',
        'parameters' => 
        array (
        ),
        'returnsReference' => false,
        'returnType' => NULL,
        'attributes' => 
        array (
        ),
        'docComment' => '/**
 * Get the excluded middleware for the route\'s controller.
 *
 * @return array
 */',
        'startLine' => 1223,
        'endLine' => 1235,
        'startColumn' => 5,
        'endColumn' => 5,
        'couldThrow' => false,
        'isClosure' => false,
        'isGenerator' => false,
        'isVariadic' => false,
        'modifiers' => 1,
        'namespace' => 'Illuminate\\Routing',
        'declaringClassName' => 'Illuminate\\Routing\\Route',
        'implementingClassName' => 'Illuminate\\Routing\\Route',
        'currentClassName' => 'Illuminate\\Routing\\Route',
        'aliasName' => NULL,
      ),
      'attributeProvidedControllerMiddlewareExclusions' => 
      array (
        'name' => 'attributeProvidedControllerMiddlewareExclusions',
        'parameters' => 
        array (
          'class' => 
          array (
            'name' => 'class',
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
            'startLine' => 1244,
            'endLine' => 1244,
            'startColumn' => 72,
            'endColumn' => 84,
            'parameterIndex' => 0,
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
            'startLine' => 1244,
            'endLine' => 1244,
            'startColumn' => 87,
            'endColumn' => 100,
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
            'name' => 'array',
            'isIdentifier' => true,
          ),
        ),
        'attributes' => 
        array (
        ),
        'docComment' => '/**
 * Get the attribute provided excluded controller middleware for the given class and method.
 *
 * @param  string  $class
 * @param  string  $method
 * @return array
 */',
        'startLine' => 1244,
        'endLine' => 1281,
        'startColumn' => 5,
        'endColumn' => 5,
        'couldThrow' => false,
        'isClosure' => false,
        'isGenerator' => false,
        'isVariadic' => false,
        'modifiers' => 2,
        'namespace' => 'Illuminate\\Routing',
        'declaringClassName' => 'Illuminate\\Routing\\Route',
        'implementingClassName' => 'Illuminate\\Routing\\Route',
        'currentClassName' => 'Illuminate\\Routing\\Route',
        'aliasName' => NULL,
      ),
      'withoutMiddleware' => 
      array (
        'name' => 'withoutMiddleware',
        'parameters' => 
        array (
          'middleware' => 
          array (
            'name' => 'middleware',
            'default' => NULL,
            'type' => NULL,
            'isVariadic' => false,
            'byRef' => false,
            'isPromoted' => false,
            'attributes' => 
            array (
            ),
            'startLine' => 1289,
            'endLine' => 1289,
            'startColumn' => 39,
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
 * Specify middleware that should be removed from the given route.
 *
 * @param  array|string  $middleware
 * @return $this
 */',
        'startLine' => 1289,
        'endLine' => 1296,
        'startColumn' => 5,
        'endColumn' => 5,
        'couldThrow' => false,
        'isClosure' => false,
        'isGenerator' => false,
        'isVariadic' => false,
        'modifiers' => 1,
        'namespace' => 'Illuminate\\Routing',
        'declaringClassName' => 'Illuminate\\Routing\\Route',
        'implementingClassName' => 'Illuminate\\Routing\\Route',
        'currentClassName' => 'Illuminate\\Routing\\Route',
        'aliasName' => NULL,
      ),
      'excludedMiddleware' => 
      array (
        'name' => 'excludedMiddleware',
        'parameters' => 
        array (
        ),
        'returnsReference' => false,
        'returnType' => NULL,
        'attributes' => 
        array (
        ),
        'docComment' => '/**
 * Get the middleware that should be removed from the route.
 *
 * @return array
 */',
        'startLine' => 1303,
        'endLine' => 1309,
        'startColumn' => 5,
        'endColumn' => 5,
        'couldThrow' => false,
        'isClosure' => false,
        'isGenerator' => false,
        'isVariadic' => false,
        'modifiers' => 1,
        'namespace' => 'Illuminate\\Routing',
        'declaringClassName' => 'Illuminate\\Routing\\Route',
        'implementingClassName' => 'Illuminate\\Routing\\Route',
        'currentClassName' => 'Illuminate\\Routing\\Route',
        'aliasName' => NULL,
      ),
      'scopeBindings' => 
      array (
        'name' => 'scopeBindings',
        'parameters' => 
        array (
        ),
        'returnsReference' => false,
        'returnType' => NULL,
        'attributes' => 
        array (
        ),
        'docComment' => '/**
 * Indicate that the route should enforce scoping of multiple implicit Eloquent bindings.
 *
 * @return $this
 */',
        'startLine' => 1316,
        'endLine' => 1321,
        'startColumn' => 5,
        'endColumn' => 5,
        'couldThrow' => false,
        'isClosure' => false,
        'isGenerator' => false,
        'isVariadic' => false,
        'modifiers' => 1,
        'namespace' => 'Illuminate\\Routing',
        'declaringClassName' => 'Illuminate\\Routing\\Route',
        'implementingClassName' => 'Illuminate\\Routing\\Route',
        'currentClassName' => 'Illuminate\\Routing\\Route',
        'aliasName' => NULL,
      ),
      'withoutScopedBindings' => 
      array (
        'name' => 'withoutScopedBindings',
        'parameters' => 
        array (
        ),
        'returnsReference' => false,
        'returnType' => NULL,
        'attributes' => 
        array (
        ),
        'docComment' => '/**
 * Indicate that the route should not enforce scoping of multiple implicit Eloquent bindings.
 *
 * @return $this
 */',
        'startLine' => 1328,
        'endLine' => 1333,
        'startColumn' => 5,
        'endColumn' => 5,
        'couldThrow' => false,
        'isClosure' => false,
        'isGenerator' => false,
        'isVariadic' => false,
        'modifiers' => 1,
        'namespace' => 'Illuminate\\Routing',
        'declaringClassName' => 'Illuminate\\Routing\\Route',
        'implementingClassName' => 'Illuminate\\Routing\\Route',
        'currentClassName' => 'Illuminate\\Routing\\Route',
        'aliasName' => NULL,
      ),
      'enforcesScopedBindings' => 
      array (
        'name' => 'enforcesScopedBindings',
        'parameters' => 
        array (
        ),
        'returnsReference' => false,
        'returnType' => NULL,
        'attributes' => 
        array (
        ),
        'docComment' => '/**
 * Determine if the route should enforce scoping of multiple implicit Eloquent bindings.
 *
 * @return bool
 */',
        'startLine' => 1340,
        'endLine' => 1343,
        'startColumn' => 5,
        'endColumn' => 5,
        'couldThrow' => false,
        'isClosure' => false,
        'isGenerator' => false,
        'isVariadic' => false,
        'modifiers' => 1,
        'namespace' => 'Illuminate\\Routing',
        'declaringClassName' => 'Illuminate\\Routing\\Route',
        'implementingClassName' => 'Illuminate\\Routing\\Route',
        'currentClassName' => 'Illuminate\\Routing\\Route',
        'aliasName' => NULL,
      ),
      'preventsScopedBindings' => 
      array (
        'name' => 'preventsScopedBindings',
        'parameters' => 
        array (
        ),
        'returnsReference' => false,
        'returnType' => NULL,
        'attributes' => 
        array (
        ),
        'docComment' => '/**
 * Determine if the route should prevent scoping of multiple implicit Eloquent bindings.
 *
 * @return bool
 */',
        'startLine' => 1350,
        'endLine' => 1353,
        'startColumn' => 5,
        'endColumn' => 5,
        'couldThrow' => false,
        'isClosure' => false,
        'isGenerator' => false,
        'isVariadic' => false,
        'modifiers' => 1,
        'namespace' => 'Illuminate\\Routing',
        'declaringClassName' => 'Illuminate\\Routing\\Route',
        'implementingClassName' => 'Illuminate\\Routing\\Route',
        'currentClassName' => 'Illuminate\\Routing\\Route',
        'aliasName' => NULL,
      ),
      'block' => 
      array (
        'name' => 'block',
        'parameters' => 
        array (
          'lockSeconds' => 
          array (
            'name' => 'lockSeconds',
            'default' => 
            array (
              'code' => '10',
              'attributes' => 
              array (
                'startLine' => 1362,
                'endLine' => 1362,
                'startTokenPos' => 5096,
                'startFilePos' => 33916,
                'endTokenPos' => 5096,
                'endFilePos' => 33917,
              ),
            ),
            'type' => NULL,
            'isVariadic' => false,
            'byRef' => false,
            'isPromoted' => false,
            'attributes' => 
            array (
            ),
            'startLine' => 1362,
            'endLine' => 1362,
            'startColumn' => 27,
            'endColumn' => 43,
            'parameterIndex' => 0,
            'isOptional' => true,
          ),
          'waitSeconds' => 
          array (
            'name' => 'waitSeconds',
            'default' => 
            array (
              'code' => '10',
              'attributes' => 
              array (
                'startLine' => 1362,
                'endLine' => 1362,
                'startTokenPos' => 5103,
                'startFilePos' => 33935,
                'endTokenPos' => 5103,
                'endFilePos' => 33936,
              ),
            ),
            'type' => NULL,
            'isVariadic' => false,
            'byRef' => false,
            'isPromoted' => false,
            'attributes' => 
            array (
            ),
            'startLine' => 1362,
            'endLine' => 1362,
            'startColumn' => 46,
            'endColumn' => 62,
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
 * Specify that the route should not allow concurrent requests from the same session.
 *
 * @param  int|null  $lockSeconds
 * @param  int|null  $waitSeconds
 * @return $this
 */',
        'startLine' => 1362,
        'endLine' => 1368,
        'startColumn' => 5,
        'endColumn' => 5,
        'couldThrow' => false,
        'isClosure' => false,
        'isGenerator' => false,
        'isVariadic' => false,
        'modifiers' => 1,
        'namespace' => 'Illuminate\\Routing',
        'declaringClassName' => 'Illuminate\\Routing\\Route',
        'implementingClassName' => 'Illuminate\\Routing\\Route',
        'currentClassName' => 'Illuminate\\Routing\\Route',
        'aliasName' => NULL,
      ),
      'withoutBlocking' => 
      array (
        'name' => 'withoutBlocking',
        'parameters' => 
        array (
        ),
        'returnsReference' => false,
        'returnType' => NULL,
        'attributes' => 
        array (
        ),
        'docComment' => '/**
 * Specify that the route should allow concurrent requests from the same session.
 *
 * @return $this
 */',
        'startLine' => 1375,
        'endLine' => 1378,
        'startColumn' => 5,
        'endColumn' => 5,
        'couldThrow' => false,
        'isClosure' => false,
        'isGenerator' => false,
        'isVariadic' => false,
        'modifiers' => 1,
        'namespace' => 'Illuminate\\Routing',
        'declaringClassName' => 'Illuminate\\Routing\\Route',
        'implementingClassName' => 'Illuminate\\Routing\\Route',
        'currentClassName' => 'Illuminate\\Routing\\Route',
        'aliasName' => NULL,
      ),
      'locksFor' => 
      array (
        'name' => 'locksFor',
        'parameters' => 
        array (
        ),
        'returnsReference' => false,
        'returnType' => NULL,
        'attributes' => 
        array (
        ),
        'docComment' => '/**
 * Get the maximum number of seconds the route\'s session lock should be held for.
 *
 * @return int|null
 */',
        'startLine' => 1385,
        'endLine' => 1388,
        'startColumn' => 5,
        'endColumn' => 5,
        'couldThrow' => false,
        'isClosure' => false,
        'isGenerator' => false,
        'isVariadic' => false,
        'modifiers' => 1,
        'namespace' => 'Illuminate\\Routing',
        'declaringClassName' => 'Illuminate\\Routing\\Route',
        'implementingClassName' => 'Illuminate\\Routing\\Route',
        'currentClassName' => 'Illuminate\\Routing\\Route',
        'aliasName' => NULL,
      ),
      'waitsFor' => 
      array (
        'name' => 'waitsFor',
        'parameters' => 
        array (
        ),
        'returnsReference' => false,
        'returnType' => NULL,
        'attributes' => 
        array (
        ),
        'docComment' => '/**
 * Get the maximum number of seconds to wait while attempting to acquire a session lock.
 *
 * @return int|null
 */',
        'startLine' => 1395,
        'endLine' => 1398,
        'startColumn' => 5,
        'endColumn' => 5,
        'couldThrow' => false,
        'isClosure' => false,
        'isGenerator' => false,
        'isVariadic' => false,
        'modifiers' => 1,
        'namespace' => 'Illuminate\\Routing',
        'declaringClassName' => 'Illuminate\\Routing\\Route',
        'implementingClassName' => 'Illuminate\\Routing\\Route',
        'currentClassName' => 'Illuminate\\Routing\\Route',
        'aliasName' => NULL,
      ),
      'metadata' => 
      array (
        'name' => 'metadata',
        'parameters' => 
        array (
          'metadata' => 
          array (
            'name' => 'metadata',
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
            'startLine' => 1406,
            'endLine' => 1406,
            'startColumn' => 30,
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
 * Add metadata to the route.
 *
 * @param  array  $metadata
 * @return $this
 */',
        'startLine' => 1406,
        'endLine' => 1414,
        'startColumn' => 5,
        'endColumn' => 5,
        'couldThrow' => false,
        'isClosure' => false,
        'isGenerator' => false,
        'isVariadic' => false,
        'modifiers' => 1,
        'namespace' => 'Illuminate\\Routing',
        'declaringClassName' => 'Illuminate\\Routing\\Route',
        'implementingClassName' => 'Illuminate\\Routing\\Route',
        'currentClassName' => 'Illuminate\\Routing\\Route',
        'aliasName' => NULL,
      ),
      'getMetadata' => 
      array (
        'name' => 'getMetadata',
        'parameters' => 
        array (
          'key' => 
          array (
            'name' => 'key',
            'default' => 
            array (
              'code' => 'null',
              'attributes' => 
              array (
                'startLine' => 1423,
                'endLine' => 1423,
                'startTokenPos' => 5268,
                'startFilePos' => 35272,
                'endTokenPos' => 5268,
                'endFilePos' => 35275,
              ),
            ),
            'type' => NULL,
            'isVariadic' => false,
            'byRef' => false,
            'isPromoted' => false,
            'attributes' => 
            array (
            ),
            'startLine' => 1423,
            'endLine' => 1423,
            'startColumn' => 33,
            'endColumn' => 43,
            'parameterIndex' => 0,
            'isOptional' => true,
          ),
          'default' => 
          array (
            'name' => 'default',
            'default' => 
            array (
              'code' => 'null',
              'attributes' => 
              array (
                'startLine' => 1423,
                'endLine' => 1423,
                'startTokenPos' => 5275,
                'startFilePos' => 35289,
                'endTokenPos' => 5275,
                'endFilePos' => 35292,
              ),
            ),
            'type' => NULL,
            'isVariadic' => false,
            'byRef' => false,
            'isPromoted' => false,
            'attributes' => 
            array (
            ),
            'startLine' => 1423,
            'endLine' => 1423,
            'startColumn' => 46,
            'endColumn' => 60,
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
 * Get metadata for the route.
 *
 * @param  string|null  $key
 * @param  mixed  $default
 * @return ($key is null ? array<array-key, mixed> : mixed)
 */',
        'startLine' => 1423,
        'endLine' => 1428,
        'startColumn' => 5,
        'endColumn' => 5,
        'couldThrow' => false,
        'isClosure' => false,
        'isGenerator' => false,
        'isVariadic' => false,
        'modifiers' => 1,
        'namespace' => 'Illuminate\\Routing',
        'declaringClassName' => 'Illuminate\\Routing\\Route',
        'implementingClassName' => 'Illuminate\\Routing\\Route',
        'currentClassName' => 'Illuminate\\Routing\\Route',
        'aliasName' => NULL,
      ),
      'setMetadata' => 
      array (
        'name' => 'setMetadata',
        'parameters' => 
        array (
          'metadata' => 
          array (
            'name' => 'metadata',
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
            'startLine' => 1436,
            'endLine' => 1436,
            'startColumn' => 33,
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
 * Set the metadata for the route, replacing any existing metadata.
 *
 * @param  array  $metadata
 * @return $this
 */',
        'startLine' => 1436,
        'endLine' => 1441,
        'startColumn' => 5,
        'endColumn' => 5,
        'couldThrow' => false,
        'isClosure' => false,
        'isGenerator' => false,
        'isVariadic' => false,
        'modifiers' => 1,
        'namespace' => 'Illuminate\\Routing',
        'declaringClassName' => 'Illuminate\\Routing\\Route',
        'implementingClassName' => 'Illuminate\\Routing\\Route',
        'currentClassName' => 'Illuminate\\Routing\\Route',
        'aliasName' => NULL,
      ),
      'controllerDispatcher' => 
      array (
        'name' => 'controllerDispatcher',
        'parameters' => 
        array (
        ),
        'returnsReference' => false,
        'returnType' => NULL,
        'attributes' => 
        array (
        ),
        'docComment' => '/**
 * Get the dispatcher for the route\'s controller.
 *
 * @return \\Illuminate\\Routing\\Contracts\\ControllerDispatcher
 *
 * @throws \\Illuminate\\Contracts\\Container\\BindingResolutionException
 */',
        'startLine' => 1450,
        'endLine' => 1457,
        'startColumn' => 5,
        'endColumn' => 5,
        'couldThrow' => false,
        'isClosure' => false,
        'isGenerator' => false,
        'isVariadic' => false,
        'modifiers' => 1,
        'namespace' => 'Illuminate\\Routing',
        'declaringClassName' => 'Illuminate\\Routing\\Route',
        'implementingClassName' => 'Illuminate\\Routing\\Route',
        'currentClassName' => 'Illuminate\\Routing\\Route',
        'aliasName' => NULL,
      ),
      'getValidators' => 
      array (
        'name' => 'getValidators',
        'parameters' => 
        array (
        ),
        'returnsReference' => false,
        'returnType' => NULL,
        'attributes' => 
        array (
        ),
        'docComment' => '/**
 * Get the route validators for the instance.
 *
 * @return array
 */',
        'startLine' => 1464,
        'endLine' => 1473,
        'startColumn' => 5,
        'endColumn' => 5,
        'couldThrow' => false,
        'isClosure' => false,
        'isGenerator' => false,
        'isVariadic' => false,
        'modifiers' => 17,
        'namespace' => 'Illuminate\\Routing',
        'declaringClassName' => 'Illuminate\\Routing\\Route',
        'implementingClassName' => 'Illuminate\\Routing\\Route',
        'currentClassName' => 'Illuminate\\Routing\\Route',
        'aliasName' => NULL,
      ),
      'toSymfonyRoute' => 
      array (
        'name' => 'toSymfonyRoute',
        'parameters' => 
        array (
        ),
        'returnsReference' => false,
        'returnType' => NULL,
        'attributes' => 
        array (
        ),
        'docComment' => '/**
 * Convert the route to a Symfony route.
 *
 * @return \\Symfony\\Component\\Routing\\Route
 */',
        'startLine' => 1480,
        'endLine' => 1487,
        'startColumn' => 5,
        'endColumn' => 5,
        'couldThrow' => false,
        'isClosure' => false,
        'isGenerator' => false,
        'isVariadic' => false,
        'modifiers' => 1,
        'namespace' => 'Illuminate\\Routing',
        'declaringClassName' => 'Illuminate\\Routing\\Route',
        'implementingClassName' => 'Illuminate\\Routing\\Route',
        'currentClassName' => 'Illuminate\\Routing\\Route',
        'aliasName' => NULL,
      ),
      'getOptionalParameterNames' => 
      array (
        'name' => 'getOptionalParameterNames',
        'parameters' => 
        array (
        ),
        'returnsReference' => false,
        'returnType' => NULL,
        'attributes' => 
        array (
        ),
        'docComment' => '/**
 * Get the optional parameter names for the route.
 *
 * @return array<string, null>
 */',
        'startLine' => 1494,
        'endLine' => 1499,
        'startColumn' => 5,
        'endColumn' => 5,
        'couldThrow' => false,
        'isClosure' => false,
        'isGenerator' => false,
        'isVariadic' => false,
        'modifiers' => 1,
        'namespace' => 'Illuminate\\Routing',
        'declaringClassName' => 'Illuminate\\Routing\\Route',
        'implementingClassName' => 'Illuminate\\Routing\\Route',
        'currentClassName' => 'Illuminate\\Routing\\Route',
        'aliasName' => NULL,
      ),
      'getCompiled' => 
      array (
        'name' => 'getCompiled',
        'parameters' => 
        array (
        ),
        'returnsReference' => false,
        'returnType' => NULL,
        'attributes' => 
        array (
        ),
        'docComment' => '/**
 * Get the compiled version of the route.
 *
 * @return \\Symfony\\Component\\Routing\\CompiledRoute
 */',
        'startLine' => 1506,
        'endLine' => 1509,
        'startColumn' => 5,
        'endColumn' => 5,
        'couldThrow' => false,
        'isClosure' => false,
        'isGenerator' => false,
        'isVariadic' => false,
        'modifiers' => 1,
        'namespace' => 'Illuminate\\Routing',
        'declaringClassName' => 'Illuminate\\Routing\\Route',
        'implementingClassName' => 'Illuminate\\Routing\\Route',
        'currentClassName' => 'Illuminate\\Routing\\Route',
        'aliasName' => NULL,
      ),
      'setRouter' => 
      array (
        'name' => 'setRouter',
        'parameters' => 
        array (
          'router' => 
          array (
            'name' => 'router',
            'default' => NULL,
            'type' => 
            array (
              'class' => 'PHPStan\\BetterReflection\\Reflection\\ReflectionNamedType',
              'data' => 
              array (
                'name' => 'Illuminate\\Routing\\Router',
                'isIdentifier' => false,
              ),
            ),
            'isVariadic' => false,
            'byRef' => false,
            'isPromoted' => false,
            'attributes' => 
            array (
            ),
            'startLine' => 1517,
            'endLine' => 1517,
            'startColumn' => 31,
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
 * Set the router instance on the route.
 *
 * @param  \\Illuminate\\Routing\\Router  $router
 * @return $this
 */',
        'startLine' => 1517,
        'endLine' => 1522,
        'startColumn' => 5,
        'endColumn' => 5,
        'couldThrow' => false,
        'isClosure' => false,
        'isGenerator' => false,
        'isVariadic' => false,
        'modifiers' => 1,
        'namespace' => 'Illuminate\\Routing',
        'declaringClassName' => 'Illuminate\\Routing\\Route',
        'implementingClassName' => 'Illuminate\\Routing\\Route',
        'currentClassName' => 'Illuminate\\Routing\\Route',
        'aliasName' => NULL,
      ),
      'setContainer' => 
      array (
        'name' => 'setContainer',
        'parameters' => 
        array (
          'container' => 
          array (
            'name' => 'container',
            'default' => NULL,
            'type' => 
            array (
              'class' => 'PHPStan\\BetterReflection\\Reflection\\ReflectionNamedType',
              'data' => 
              array (
                'name' => 'Illuminate\\Container\\Container',
                'isIdentifier' => false,
              ),
            ),
            'isVariadic' => false,
            'byRef' => false,
            'isPromoted' => false,
            'attributes' => 
            array (
            ),
            'startLine' => 1530,
            'endLine' => 1530,
            'startColumn' => 34,
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
 * Set the container instance on the route.
 *
 * @param  \\Illuminate\\Container\\Container  $container
 * @return $this
 */',
        'startLine' => 1530,
        'endLine' => 1535,
        'startColumn' => 5,
        'endColumn' => 5,
        'couldThrow' => false,
        'isClosure' => false,
        'isGenerator' => false,
        'isVariadic' => false,
        'modifiers' => 1,
        'namespace' => 'Illuminate\\Routing',
        'declaringClassName' => 'Illuminate\\Routing\\Route',
        'implementingClassName' => 'Illuminate\\Routing\\Route',
        'currentClassName' => 'Illuminate\\Routing\\Route',
        'aliasName' => NULL,
      ),
      'prepareForSerialization' => 
      array (
        'name' => 'prepareForSerialization',
        'parameters' => 
        array (
        ),
        'returnsReference' => false,
        'returnType' => NULL,
        'attributes' => 
        array (
        ),
        'docComment' => '/**
 * Prepare the route instance for serialization.
 *
 * @return void
 *
 * @throws \\LogicException
 */',
        'startLine' => 1544,
        'endLine' => 1561,
        'startColumn' => 5,
        'endColumn' => 5,
        'couldThrow' => false,
        'isClosure' => false,
        'isGenerator' => false,
        'isVariadic' => false,
        'modifiers' => 1,
        'namespace' => 'Illuminate\\Routing',
        'declaringClassName' => 'Illuminate\\Routing\\Route',
        'implementingClassName' => 'Illuminate\\Routing\\Route',
        'currentClassName' => 'Illuminate\\Routing\\Route',
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
            'startLine' => 1569,
            'endLine' => 1569,
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
 * Dynamically access route parameters.
 *
 * @param  string  $key
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
        'modifiers' => 1,
        'namespace' => 'Illuminate\\Routing',
        'declaringClassName' => 'Illuminate\\Routing\\Route',
        'implementingClassName' => 'Illuminate\\Routing\\Route',
        'currentClassName' => 'Illuminate\\Routing\\Route',
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