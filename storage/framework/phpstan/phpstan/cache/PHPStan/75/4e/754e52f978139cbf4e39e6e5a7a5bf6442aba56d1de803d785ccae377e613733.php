<?php declare(strict_types = 1);

// osfsl-/var/www/html/vendor/composer/../laravel/framework/src/Illuminate/Reflection/Traits/ReflectsClosures.php-PHPStan\BetterReflection\Reflection\ReflectionClass-Illuminate\Support\Traits\ReflectsClosures
return \PHPStan\Cache\CacheItem::__set_state(array(
   'variableKey' => 'v2-bf84433c121beb6267435ca7ff7bd48fd94d61624e4a8ecde6ed7665e6825fa5-8.5.9-6.70.0.3',
   'data' => 
  array (
    'locatedSource' => 
    array (
      'class' => 'PHPStan\\BetterReflection\\SourceLocator\\Located\\LocatedSource',
      'data' => 
      array (
        'name' => 'Illuminate\\Support\\Traits\\ReflectsClosures',
        'filename' => '/var/www/html/vendor/composer/../laravel/framework/src/Illuminate/Reflection/Traits/ReflectsClosures.php',
      ),
    ),
    'namespace' => 'Illuminate\\Support\\Traits',
    'name' => 'Illuminate\\Support\\Traits\\ReflectsClosures',
    'shortName' => 'ReflectsClosures',
    'isInterface' => false,
    'isTrait' => true,
    'isEnum' => false,
    'isBackedEnum' => false,
    'modifiers' => 0,
    'docComment' => NULL,
    'attributes' => 
    array (
    ),
    'startLine' => 13,
    'endLine' => 125,
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
      'firstClosureParameterType' => 
      array (
        'name' => 'firstClosureParameterType',
        'parameters' => 
        array (
          'closure' => 
          array (
            'name' => 'closure',
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
            'startLine' => 24,
            'endLine' => 24,
            'startColumn' => 50,
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
 * Get the class name of the first parameter of the given Closure.
 *
 * @param  \\Closure  $closure
 * @return string
 *
 * @throws \\ReflectionException
 * @throws \\RuntimeException
 */',
        'startLine' => 24,
        'endLine' => 37,
        'startColumn' => 5,
        'endColumn' => 5,
        'couldThrow' => false,
        'isClosure' => false,
        'isGenerator' => false,
        'isVariadic' => false,
        'modifiers' => 2,
        'namespace' => 'Illuminate\\Support\\Traits',
        'declaringClassName' => 'Illuminate\\Support\\Traits\\ReflectsClosures',
        'implementingClassName' => 'Illuminate\\Support\\Traits\\ReflectsClosures',
        'currentClassName' => 'Illuminate\\Support\\Traits\\ReflectsClosures',
        'aliasName' => NULL,
      ),
      'firstClosureParameterTypes' => 
      array (
        'name' => 'firstClosureParameterTypes',
        'parameters' => 
        array (
          'closure' => 
          array (
            'name' => 'closure',
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
            'startLine' => 48,
            'endLine' => 48,
            'startColumn' => 51,
            'endColumn' => 66,
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
 * Get the class names of the first parameter of the given Closure, including union types.
 *
 * @param  \\Closure  $closure
 * @return array
 *
 * @throws \\ReflectionException
 * @throws \\RuntimeException
 */',
        'startLine' => 48,
        'endLine' => 73,
        'startColumn' => 5,
        'endColumn' => 5,
        'couldThrow' => false,
        'isClosure' => false,
        'isGenerator' => false,
        'isVariadic' => false,
        'modifiers' => 2,
        'namespace' => 'Illuminate\\Support\\Traits',
        'declaringClassName' => 'Illuminate\\Support\\Traits\\ReflectsClosures',
        'implementingClassName' => 'Illuminate\\Support\\Traits\\ReflectsClosures',
        'currentClassName' => 'Illuminate\\Support\\Traits\\ReflectsClosures',
        'aliasName' => NULL,
      ),
      'closureParameterTypes' => 
      array (
        'name' => 'closureParameterTypes',
        'parameters' => 
        array (
          'closure' => 
          array (
            'name' => 'closure',
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
            'startLine' => 83,
            'endLine' => 83,
            'startColumn' => 46,
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
 * Get the class names / types of the parameters of the given Closure.
 *
 * @param  \\Closure  $closure
 * @return array
 *
 * @throws \\ReflectionException
 */',
        'startLine' => 83,
        'endLine' => 96,
        'startColumn' => 5,
        'endColumn' => 5,
        'couldThrow' => false,
        'isClosure' => false,
        'isGenerator' => false,
        'isVariadic' => false,
        'modifiers' => 2,
        'namespace' => 'Illuminate\\Support\\Traits',
        'declaringClassName' => 'Illuminate\\Support\\Traits\\ReflectsClosures',
        'implementingClassName' => 'Illuminate\\Support\\Traits\\ReflectsClosures',
        'currentClassName' => 'Illuminate\\Support\\Traits\\ReflectsClosures',
        'aliasName' => NULL,
      ),
      'closureReturnTypes' => 
      array (
        'name' => 'closureReturnTypes',
        'parameters' => 
        array (
          'closure' => 
          array (
            'name' => 'closure',
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
            'startLine' => 106,
            'endLine' => 106,
            'startColumn' => 43,
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
 * Get the class names / types of the return type of the given Closure.
 *
 * @param  \\Closure  $closure
 * @return list<class-string>
 *
 * @throws \\ReflectionException
 */',
        'startLine' => 106,
        'endLine' => 124,
        'startColumn' => 5,
        'endColumn' => 5,
        'couldThrow' => false,
        'isClosure' => false,
        'isGenerator' => false,
        'isVariadic' => false,
        'modifiers' => 2,
        'namespace' => 'Illuminate\\Support\\Traits',
        'declaringClassName' => 'Illuminate\\Support\\Traits\\ReflectsClosures',
        'implementingClassName' => 'Illuminate\\Support\\Traits\\ReflectsClosures',
        'currentClassName' => 'Illuminate\\Support\\Traits\\ReflectsClosures',
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