<?php declare(strict_types = 1);

// osfsl-/var/www/html/vendor/composer/../laravel/framework/src/Illuminate/Contracts/Pagination/Paginator.php-PHPStan\BetterReflection\Reflection\ReflectionClass-Illuminate\Contracts\Pagination\Paginator
return \PHPStan\Cache\CacheItem::__set_state(array(
   'variableKey' => 'v2-66a057f737469191317c795f1322a46749b86bb055a9430b17b2494aa92d392b-8.5.9-6.70.0.3',
   'data' => 
  array (
    'locatedSource' => 
    array (
      'class' => 'PHPStan\\BetterReflection\\SourceLocator\\Located\\LocatedSource',
      'data' => 
      array (
        'name' => 'Illuminate\\Contracts\\Pagination\\Paginator',
        'filename' => '/var/www/html/vendor/composer/../laravel/framework/src/Illuminate/Contracts/Pagination/Paginator.php',
      ),
    ),
    'namespace' => 'Illuminate\\Contracts\\Pagination',
    'name' => 'Illuminate\\Contracts\\Pagination\\Paginator',
    'shortName' => 'Paginator',
    'isInterface' => true,
    'isTrait' => false,
    'isEnum' => false,
    'isBackedEnum' => false,
    'modifiers' => 0,
    'docComment' => '/**
 * @template TKey of array-key
 *
 * @template-covariant TValue
 *
 * @method $this through(callable(TValue): mixed $callback)
 */',
    'attributes' => 
    array (
    ),
    'startLine' => 12,
    'endLine' => 138,
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
      'url' => 
      array (
        'name' => 'url',
        'parameters' => 
        array (
          'page' => 
          array (
            'name' => 'page',
            'default' => NULL,
            'type' => NULL,
            'isVariadic' => false,
            'byRef' => false,
            'isPromoted' => false,
            'attributes' => 
            array (
            ),
            'startLine' => 20,
            'endLine' => 20,
            'startColumn' => 25,
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
 * Get the URL for a given page.
 *
 * @param  int  $page
 * @return string
 */',
        'startLine' => 20,
        'endLine' => 20,
        'startColumn' => 5,
        'endColumn' => 31,
        'couldThrow' => false,
        'isClosure' => false,
        'isGenerator' => false,
        'isVariadic' => false,
        'modifiers' => 1,
        'namespace' => 'Illuminate\\Contracts\\Pagination',
        'declaringClassName' => 'Illuminate\\Contracts\\Pagination\\Paginator',
        'implementingClassName' => 'Illuminate\\Contracts\\Pagination\\Paginator',
        'currentClassName' => 'Illuminate\\Contracts\\Pagination\\Paginator',
        'aliasName' => NULL,
      ),
      'appends' => 
      array (
        'name' => 'appends',
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
            'startLine' => 29,
            'endLine' => 29,
            'startColumn' => 29,
            'endColumn' => 32,
            'parameterIndex' => 0,
            'isOptional' => false,
          ),
          'value' => 
          array (
            'name' => 'value',
            'default' => 
            array (
              'code' => 'null',
              'attributes' => 
              array (
                'startLine' => 29,
                'endLine' => 29,
                'startTokenPos' => 42,
                'startFilePos' => 568,
                'endTokenPos' => 42,
                'endFilePos' => 571,
              ),
            ),
            'type' => NULL,
            'isVariadic' => false,
            'byRef' => false,
            'isPromoted' => false,
            'attributes' => 
            array (
            ),
            'startLine' => 29,
            'endLine' => 29,
            'startColumn' => 35,
            'endColumn' => 47,
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
 * Add a set of query string values to the paginator.
 *
 * @param  array|string|null  $key
 * @param  string|null  $value
 * @return $this
 */',
        'startLine' => 29,
        'endLine' => 29,
        'startColumn' => 5,
        'endColumn' => 49,
        'couldThrow' => false,
        'isClosure' => false,
        'isGenerator' => false,
        'isVariadic' => false,
        'modifiers' => 1,
        'namespace' => 'Illuminate\\Contracts\\Pagination',
        'declaringClassName' => 'Illuminate\\Contracts\\Pagination\\Paginator',
        'implementingClassName' => 'Illuminate\\Contracts\\Pagination\\Paginator',
        'currentClassName' => 'Illuminate\\Contracts\\Pagination\\Paginator',
        'aliasName' => NULL,
      ),
      'fragment' => 
      array (
        'name' => 'fragment',
        'parameters' => 
        array (
          'fragment' => 
          array (
            'name' => 'fragment',
            'default' => 
            array (
              'code' => 'null',
              'attributes' => 
              array (
                'startLine' => 37,
                'endLine' => 37,
                'startTokenPos' => 58,
                'startFilePos' => 793,
                'endTokenPos' => 58,
                'endFilePos' => 796,
              ),
            ),
            'type' => NULL,
            'isVariadic' => false,
            'byRef' => false,
            'isPromoted' => false,
            'attributes' => 
            array (
            ),
            'startLine' => 37,
            'endLine' => 37,
            'startColumn' => 30,
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
 * Get / set the URL fragment to be appended to URLs.
 *
 * @param  string|null  $fragment
 * @return ($fragment is null ? string|null : $this)
 */',
        'startLine' => 37,
        'endLine' => 37,
        'startColumn' => 5,
        'endColumn' => 47,
        'couldThrow' => false,
        'isClosure' => false,
        'isGenerator' => false,
        'isVariadic' => false,
        'modifiers' => 1,
        'namespace' => 'Illuminate\\Contracts\\Pagination',
        'declaringClassName' => 'Illuminate\\Contracts\\Pagination\\Paginator',
        'implementingClassName' => 'Illuminate\\Contracts\\Pagination\\Paginator',
        'currentClassName' => 'Illuminate\\Contracts\\Pagination\\Paginator',
        'aliasName' => NULL,
      ),
      'withQueryString' => 
      array (
        'name' => 'withQueryString',
        'parameters' => 
        array (
        ),
        'returnsReference' => false,
        'returnType' => NULL,
        'attributes' => 
        array (
        ),
        'docComment' => '/**
 * Add all current query string values to the paginator.
 *
 * @return $this
 */',
        'startLine' => 44,
        'endLine' => 44,
        'startColumn' => 5,
        'endColumn' => 38,
        'couldThrow' => false,
        'isClosure' => false,
        'isGenerator' => false,
        'isVariadic' => false,
        'modifiers' => 1,
        'namespace' => 'Illuminate\\Contracts\\Pagination',
        'declaringClassName' => 'Illuminate\\Contracts\\Pagination\\Paginator',
        'implementingClassName' => 'Illuminate\\Contracts\\Pagination\\Paginator',
        'currentClassName' => 'Illuminate\\Contracts\\Pagination\\Paginator',
        'aliasName' => NULL,
      ),
      'nextPageUrl' => 
      array (
        'name' => 'nextPageUrl',
        'parameters' => 
        array (
        ),
        'returnsReference' => false,
        'returnType' => NULL,
        'attributes' => 
        array (
        ),
        'docComment' => '/**
 * The URL for the next page, or null.
 *
 * @return string|null
 */',
        'startLine' => 51,
        'endLine' => 51,
        'startColumn' => 5,
        'endColumn' => 34,
        'couldThrow' => false,
        'isClosure' => false,
        'isGenerator' => false,
        'isVariadic' => false,
        'modifiers' => 1,
        'namespace' => 'Illuminate\\Contracts\\Pagination',
        'declaringClassName' => 'Illuminate\\Contracts\\Pagination\\Paginator',
        'implementingClassName' => 'Illuminate\\Contracts\\Pagination\\Paginator',
        'currentClassName' => 'Illuminate\\Contracts\\Pagination\\Paginator',
        'aliasName' => NULL,
      ),
      'previousPageUrl' => 
      array (
        'name' => 'previousPageUrl',
        'parameters' => 
        array (
        ),
        'returnsReference' => false,
        'returnType' => NULL,
        'attributes' => 
        array (
        ),
        'docComment' => '/**
 * Get the URL for the previous page, or null.
 *
 * @return string|null
 */',
        'startLine' => 58,
        'endLine' => 58,
        'startColumn' => 5,
        'endColumn' => 38,
        'couldThrow' => false,
        'isClosure' => false,
        'isGenerator' => false,
        'isVariadic' => false,
        'modifiers' => 1,
        'namespace' => 'Illuminate\\Contracts\\Pagination',
        'declaringClassName' => 'Illuminate\\Contracts\\Pagination\\Paginator',
        'implementingClassName' => 'Illuminate\\Contracts\\Pagination\\Paginator',
        'currentClassName' => 'Illuminate\\Contracts\\Pagination\\Paginator',
        'aliasName' => NULL,
      ),
      'items' => 
      array (
        'name' => 'items',
        'parameters' => 
        array (
        ),
        'returnsReference' => false,
        'returnType' => NULL,
        'attributes' => 
        array (
        ),
        'docComment' => '/**
 * Get all of the items being paginated.
 *
 * @return array<TKey, TValue>
 */',
        'startLine' => 65,
        'endLine' => 65,
        'startColumn' => 5,
        'endColumn' => 28,
        'couldThrow' => false,
        'isClosure' => false,
        'isGenerator' => false,
        'isVariadic' => false,
        'modifiers' => 1,
        'namespace' => 'Illuminate\\Contracts\\Pagination',
        'declaringClassName' => 'Illuminate\\Contracts\\Pagination\\Paginator',
        'implementingClassName' => 'Illuminate\\Contracts\\Pagination\\Paginator',
        'currentClassName' => 'Illuminate\\Contracts\\Pagination\\Paginator',
        'aliasName' => NULL,
      ),
      'firstItem' => 
      array (
        'name' => 'firstItem',
        'parameters' => 
        array (
        ),
        'returnsReference' => false,
        'returnType' => NULL,
        'attributes' => 
        array (
        ),
        'docComment' => '/**
 * Get the "index" of the first item being paginated.
 *
 * @return int|null
 */',
        'startLine' => 72,
        'endLine' => 72,
        'startColumn' => 5,
        'endColumn' => 32,
        'couldThrow' => false,
        'isClosure' => false,
        'isGenerator' => false,
        'isVariadic' => false,
        'modifiers' => 1,
        'namespace' => 'Illuminate\\Contracts\\Pagination',
        'declaringClassName' => 'Illuminate\\Contracts\\Pagination\\Paginator',
        'implementingClassName' => 'Illuminate\\Contracts\\Pagination\\Paginator',
        'currentClassName' => 'Illuminate\\Contracts\\Pagination\\Paginator',
        'aliasName' => NULL,
      ),
      'lastItem' => 
      array (
        'name' => 'lastItem',
        'parameters' => 
        array (
        ),
        'returnsReference' => false,
        'returnType' => NULL,
        'attributes' => 
        array (
        ),
        'docComment' => '/**
 * Get the "index" of the last item being paginated.
 *
 * @return int|null
 */',
        'startLine' => 79,
        'endLine' => 79,
        'startColumn' => 5,
        'endColumn' => 31,
        'couldThrow' => false,
        'isClosure' => false,
        'isGenerator' => false,
        'isVariadic' => false,
        'modifiers' => 1,
        'namespace' => 'Illuminate\\Contracts\\Pagination',
        'declaringClassName' => 'Illuminate\\Contracts\\Pagination\\Paginator',
        'implementingClassName' => 'Illuminate\\Contracts\\Pagination\\Paginator',
        'currentClassName' => 'Illuminate\\Contracts\\Pagination\\Paginator',
        'aliasName' => NULL,
      ),
      'perPage' => 
      array (
        'name' => 'perPage',
        'parameters' => 
        array (
        ),
        'returnsReference' => false,
        'returnType' => NULL,
        'attributes' => 
        array (
        ),
        'docComment' => '/**
 * Determine how many items are being shown per page.
 *
 * @return int
 */',
        'startLine' => 86,
        'endLine' => 86,
        'startColumn' => 5,
        'endColumn' => 30,
        'couldThrow' => false,
        'isClosure' => false,
        'isGenerator' => false,
        'isVariadic' => false,
        'modifiers' => 1,
        'namespace' => 'Illuminate\\Contracts\\Pagination',
        'declaringClassName' => 'Illuminate\\Contracts\\Pagination\\Paginator',
        'implementingClassName' => 'Illuminate\\Contracts\\Pagination\\Paginator',
        'currentClassName' => 'Illuminate\\Contracts\\Pagination\\Paginator',
        'aliasName' => NULL,
      ),
      'currentPage' => 
      array (
        'name' => 'currentPage',
        'parameters' => 
        array (
        ),
        'returnsReference' => false,
        'returnType' => NULL,
        'attributes' => 
        array (
        ),
        'docComment' => '/**
 * Determine the current page being paginated.
 *
 * @return int
 */',
        'startLine' => 93,
        'endLine' => 93,
        'startColumn' => 5,
        'endColumn' => 34,
        'couldThrow' => false,
        'isClosure' => false,
        'isGenerator' => false,
        'isVariadic' => false,
        'modifiers' => 1,
        'namespace' => 'Illuminate\\Contracts\\Pagination',
        'declaringClassName' => 'Illuminate\\Contracts\\Pagination\\Paginator',
        'implementingClassName' => 'Illuminate\\Contracts\\Pagination\\Paginator',
        'currentClassName' => 'Illuminate\\Contracts\\Pagination\\Paginator',
        'aliasName' => NULL,
      ),
      'hasPages' => 
      array (
        'name' => 'hasPages',
        'parameters' => 
        array (
        ),
        'returnsReference' => false,
        'returnType' => NULL,
        'attributes' => 
        array (
        ),
        'docComment' => '/**
 * Determine if there are enough items to split into multiple pages.
 *
 * @return bool
 */',
        'startLine' => 100,
        'endLine' => 100,
        'startColumn' => 5,
        'endColumn' => 31,
        'couldThrow' => false,
        'isClosure' => false,
        'isGenerator' => false,
        'isVariadic' => false,
        'modifiers' => 1,
        'namespace' => 'Illuminate\\Contracts\\Pagination',
        'declaringClassName' => 'Illuminate\\Contracts\\Pagination\\Paginator',
        'implementingClassName' => 'Illuminate\\Contracts\\Pagination\\Paginator',
        'currentClassName' => 'Illuminate\\Contracts\\Pagination\\Paginator',
        'aliasName' => NULL,
      ),
      'hasMorePages' => 
      array (
        'name' => 'hasMorePages',
        'parameters' => 
        array (
        ),
        'returnsReference' => false,
        'returnType' => NULL,
        'attributes' => 
        array (
        ),
        'docComment' => '/**
 * Determine if there are more items in the data store.
 *
 * @return bool
 */',
        'startLine' => 107,
        'endLine' => 107,
        'startColumn' => 5,
        'endColumn' => 35,
        'couldThrow' => false,
        'isClosure' => false,
        'isGenerator' => false,
        'isVariadic' => false,
        'modifiers' => 1,
        'namespace' => 'Illuminate\\Contracts\\Pagination',
        'declaringClassName' => 'Illuminate\\Contracts\\Pagination\\Paginator',
        'implementingClassName' => 'Illuminate\\Contracts\\Pagination\\Paginator',
        'currentClassName' => 'Illuminate\\Contracts\\Pagination\\Paginator',
        'aliasName' => NULL,
      ),
      'path' => 
      array (
        'name' => 'path',
        'parameters' => 
        array (
        ),
        'returnsReference' => false,
        'returnType' => NULL,
        'attributes' => 
        array (
        ),
        'docComment' => '/**
 * Get the base path for paginator generated URLs.
 *
 * @return string|null
 */',
        'startLine' => 114,
        'endLine' => 114,
        'startColumn' => 5,
        'endColumn' => 27,
        'couldThrow' => false,
        'isClosure' => false,
        'isGenerator' => false,
        'isVariadic' => false,
        'modifiers' => 1,
        'namespace' => 'Illuminate\\Contracts\\Pagination',
        'declaringClassName' => 'Illuminate\\Contracts\\Pagination\\Paginator',
        'implementingClassName' => 'Illuminate\\Contracts\\Pagination\\Paginator',
        'currentClassName' => 'Illuminate\\Contracts\\Pagination\\Paginator',
        'aliasName' => NULL,
      ),
      'isEmpty' => 
      array (
        'name' => 'isEmpty',
        'parameters' => 
        array (
        ),
        'returnsReference' => false,
        'returnType' => NULL,
        'attributes' => 
        array (
        ),
        'docComment' => '/**
 * Determine if the list of items is empty or not.
 *
 * @return bool
 */',
        'startLine' => 121,
        'endLine' => 121,
        'startColumn' => 5,
        'endColumn' => 30,
        'couldThrow' => false,
        'isClosure' => false,
        'isGenerator' => false,
        'isVariadic' => false,
        'modifiers' => 1,
        'namespace' => 'Illuminate\\Contracts\\Pagination',
        'declaringClassName' => 'Illuminate\\Contracts\\Pagination\\Paginator',
        'implementingClassName' => 'Illuminate\\Contracts\\Pagination\\Paginator',
        'currentClassName' => 'Illuminate\\Contracts\\Pagination\\Paginator',
        'aliasName' => NULL,
      ),
      'isNotEmpty' => 
      array (
        'name' => 'isNotEmpty',
        'parameters' => 
        array (
        ),
        'returnsReference' => false,
        'returnType' => NULL,
        'attributes' => 
        array (
        ),
        'docComment' => '/**
 * Determine if the list of items is not empty.
 *
 * @return bool
 */',
        'startLine' => 128,
        'endLine' => 128,
        'startColumn' => 5,
        'endColumn' => 33,
        'couldThrow' => false,
        'isClosure' => false,
        'isGenerator' => false,
        'isVariadic' => false,
        'modifiers' => 1,
        'namespace' => 'Illuminate\\Contracts\\Pagination',
        'declaringClassName' => 'Illuminate\\Contracts\\Pagination\\Paginator',
        'implementingClassName' => 'Illuminate\\Contracts\\Pagination\\Paginator',
        'currentClassName' => 'Illuminate\\Contracts\\Pagination\\Paginator',
        'aliasName' => NULL,
      ),
      'render' => 
      array (
        'name' => 'render',
        'parameters' => 
        array (
          'view' => 
          array (
            'name' => 'view',
            'default' => 
            array (
              'code' => 'null',
              'attributes' => 
              array (
                'startLine' => 137,
                'endLine' => 137,
                'startTokenPos' => 217,
                'startFilePos' => 2759,
                'endTokenPos' => 217,
                'endFilePos' => 2762,
              ),
            ),
            'type' => NULL,
            'isVariadic' => false,
            'byRef' => false,
            'isPromoted' => false,
            'attributes' => 
            array (
            ),
            'startLine' => 137,
            'endLine' => 137,
            'startColumn' => 28,
            'endColumn' => 39,
            'parameterIndex' => 0,
            'isOptional' => true,
          ),
          'data' => 
          array (
            'name' => 'data',
            'default' => 
            array (
              'code' => '[]',
              'attributes' => 
              array (
                'startLine' => 137,
                'endLine' => 137,
                'startTokenPos' => 224,
                'startFilePos' => 2773,
                'endTokenPos' => 225,
                'endFilePos' => 2774,
              ),
            ),
            'type' => NULL,
            'isVariadic' => false,
            'byRef' => false,
            'isPromoted' => false,
            'attributes' => 
            array (
            ),
            'startLine' => 137,
            'endLine' => 137,
            'startColumn' => 42,
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
 * Render the paginator using a given view.
 *
 * @param  string|null  $view
 * @param  array  $data
 * @return string
 */',
        'startLine' => 137,
        'endLine' => 137,
        'startColumn' => 5,
        'endColumn' => 53,
        'couldThrow' => false,
        'isClosure' => false,
        'isGenerator' => false,
        'isVariadic' => false,
        'modifiers' => 1,
        'namespace' => 'Illuminate\\Contracts\\Pagination',
        'declaringClassName' => 'Illuminate\\Contracts\\Pagination\\Paginator',
        'implementingClassName' => 'Illuminate\\Contracts\\Pagination\\Paginator',
        'currentClassName' => 'Illuminate\\Contracts\\Pagination\\Paginator',
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