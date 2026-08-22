<?php declare(strict_types = 1);

// odsl-/var/www/html/vendor/composer/../laravel/framework/src/Illuminate/Foundation/helpers.php-PHPStan\BetterReflection\Reflection\ReflectionFunction-redirect
return \PHPStan\Cache\CacheItem::__set_state(array(
   'variableKey' => 'v2-6.70.0.3-8.5.9-84a94be4f68fd0f749a87f05a7cb1b78ddc07c7d2121b56c6f05ff6ef80d34c6',
   'data' => 
  array (
    'name' => 'redirect',
    'parameters' => 
    array (
      'to' => 
      array (
        'name' => 'to',
        'default' => 
        array (
          'code' => '\\null',
          'attributes' => 
          array (
            'startLine' => 707,
            'endLine' => 707,
            'startTokenPos' => 3152,
            'startFilePos' => 19268,
            'endTokenPos' => 3152,
            'endFilePos' => 19271,
          ),
        ),
        'type' => NULL,
        'isVariadic' => false,
        'byRef' => false,
        'isPromoted' => false,
        'attributes' => 
        array (
        ),
        'startLine' => 707,
        'endLine' => 707,
        'startColumn' => 23,
        'endColumn' => 32,
        'parameterIndex' => 0,
        'isOptional' => true,
      ),
      'status' => 
      array (
        'name' => 'status',
        'default' => 
        array (
          'code' => '302',
          'attributes' => 
          array (
            'startLine' => 707,
            'endLine' => 707,
            'startTokenPos' => 3159,
            'startFilePos' => 19284,
            'endTokenPos' => 3159,
            'endFilePos' => 19286,
          ),
        ),
        'type' => NULL,
        'isVariadic' => false,
        'byRef' => false,
        'isPromoted' => false,
        'attributes' => 
        array (
        ),
        'startLine' => 707,
        'endLine' => 707,
        'startColumn' => 35,
        'endColumn' => 47,
        'parameterIndex' => 1,
        'isOptional' => true,
      ),
      'headers' => 
      array (
        'name' => 'headers',
        'default' => 
        array (
          'code' => '[]',
          'attributes' => 
          array (
            'startLine' => 707,
            'endLine' => 707,
            'startTokenPos' => 3166,
            'startFilePos' => 19300,
            'endTokenPos' => 3167,
            'endFilePos' => 19301,
          ),
        ),
        'type' => NULL,
        'isVariadic' => false,
        'byRef' => false,
        'isPromoted' => false,
        'attributes' => 
        array (
        ),
        'startLine' => 707,
        'endLine' => 707,
        'startColumn' => 50,
        'endColumn' => 62,
        'parameterIndex' => 2,
        'isOptional' => true,
      ),
      'secure' => 
      array (
        'name' => 'secure',
        'default' => 
        array (
          'code' => '\\null',
          'attributes' => 
          array (
            'startLine' => 707,
            'endLine' => 707,
            'startTokenPos' => 3174,
            'startFilePos' => 19314,
            'endTokenPos' => 3174,
            'endFilePos' => 19317,
          ),
        ),
        'type' => NULL,
        'isVariadic' => false,
        'byRef' => false,
        'isPromoted' => false,
        'attributes' => 
        array (
        ),
        'startLine' => 707,
        'endLine' => 707,
        'startColumn' => 65,
        'endColumn' => 78,
        'parameterIndex' => 3,
        'isOptional' => true,
      ),
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
              'name' => 'Illuminate\\Routing\\Redirector',
              'isIdentifier' => false,
            ),
          ),
          1 => 
          array (
            'class' => 'PHPStan\\BetterReflection\\Reflection\\ReflectionNamedType',
            'data' => 
            array (
              'name' => 'Illuminate\\Http\\RedirectResponse',
              'isIdentifier' => false,
            ),
          ),
        ),
      ),
    ),
    'attributes' => 
    array (
    ),
    'docComment' => '/**
 * Get an instance of the redirector.
 *
 * @param  string|null  $to
 * @param  int  $status
 * @param  array  $headers
 * @param  bool|null  $secure
 * @return ($to is null ? \\Illuminate\\Routing\\Redirector : \\Illuminate\\Http\\RedirectResponse)
 */',
    'startLine' => 707,
    'endLine' => 714,
    'startColumn' => 5,
    'endColumn' => 5,
    'couldThrow' => false,
    'isClosure' => false,
    'isGenerator' => false,
    'isVariadic' => false,
    'isStatic' => false,
    'namespace' => NULL,
    'locatedSource' => 
    array (
      'class' => 'PHPStan\\BetterReflection\\SourceLocator\\Located\\LocatedSource',
      'data' => 
      array (
        'name' => 'redirect',
        'filename' => '/var/www/html/vendor/composer/../laravel/framework/src/Illuminate/Foundation/helpers.php',
      ),
    ),
  ),
));