<?php declare(strict_types = 1);

// osfsl-/var/www/html/vendor/composer/../laravel/framework/src/Illuminate/Pagination/LengthAwarePaginator.php-PHPStan\BetterReflection\Reflection\ReflectionClass-Illuminate\Pagination\LengthAwarePaginator
return \PHPStan\Cache\CacheItem::__set_state(array(
   'variableKey' => 'v2-66c5d861d081aa78d37be6a2f2406dabec33c17993ea5d10cb5a223c13e209c6-8.5.9-6.70.0.3',
   'data' => 
  array (
    'locatedSource' => 
    array (
      'class' => 'PHPStan\\BetterReflection\\SourceLocator\\Located\\LocatedSource',
      'data' => 
      array (
        'name' => 'Illuminate\\Pagination\\LengthAwarePaginator',
        'filename' => '/var/www/html/vendor/composer/../laravel/framework/src/Illuminate/Pagination/LengthAwarePaginator.php',
      ),
    ),
    'namespace' => 'Illuminate\\Pagination',
    'name' => 'Illuminate\\Pagination\\LengthAwarePaginator',
    'shortName' => 'LengthAwarePaginator',
    'isInterface' => false,
    'isTrait' => false,
    'isEnum' => false,
    'isBackedEnum' => false,
    'modifiers' => 0,
    'docComment' => '/**
 * @template TKey of array-key
 *
 * @template-covariant TValue
 *
 * @extends AbstractPaginator<TKey, TValue>
 *
 * @implements Arrayable<TKey, TValue>
 * @implements ArrayAccess<TKey, TValue>
 * @implements IteratorAggregate<TKey, TValue>
 * @implements LengthAwarePaginatorContract<TKey, TValue>
 */',
    'attributes' => 
    array (
    ),
    'startLine' => 26,
    'endLine' => 256,
    'startColumn' => 1,
    'endColumn' => 1,
    'parentClassName' => 'Illuminate\\Pagination\\AbstractPaginator',
    'implementsClassNames' => 
    array (
      0 => 'Illuminate\\Contracts\\Support\\Arrayable',
      1 => 'ArrayAccess',
      2 => 'Countable',
      3 => 'IteratorAggregate',
      4 => 'Illuminate\\Contracts\\Support\\Jsonable',
      5 => 'JsonSerializable',
      6 => 'Illuminate\\Contracts\\Pagination\\LengthAwarePaginator',
    ),
    'traitClassNames' => 
    array (
    ),
    'immediateConstants' => 
    array (
    ),
    'immediateProperties' => 
    array (
      'total' => 
      array (
        'declaringClassName' => 'Illuminate\\Pagination\\LengthAwarePaginator',
        'implementingClassName' => 'Illuminate\\Pagination\\LengthAwarePaginator',
        'name' => 'total',
        'modifiers' => 2,
        'type' => NULL,
        'default' => NULL,
        'docComment' => '/**
 * The total number of items before slicing.
 *
 * @var int
 */',
        'attributes' => 
        array (
        ),
        'startLine' => 33,
        'endLine' => 33,
        'startColumn' => 5,
        'endColumn' => 21,
        'isPromoted' => false,
        'declaredAtCompileTime' => true,
        'immediateVirtual' => false,
        'immediateHooks' => 
        array (
        ),
      ),
      'lastPage' => 
      array (
        'declaringClassName' => 'Illuminate\\Pagination\\LengthAwarePaginator',
        'implementingClassName' => 'Illuminate\\Pagination\\LengthAwarePaginator',
        'name' => 'lastPage',
        'modifiers' => 2,
        'type' => NULL,
        'default' => NULL,
        'docComment' => '/**
 * The last available page.
 *
 * @var int
 */',
        'attributes' => 
        array (
        ),
        'startLine' => 40,
        'endLine' => 40,
        'startColumn' => 5,
        'endColumn' => 24,
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
          'items' => 
          array (
            'name' => 'items',
            'default' => NULL,
            'type' => NULL,
            'isVariadic' => false,
            'byRef' => false,
            'isPromoted' => false,
            'attributes' => 
            array (
            ),
            'startLine' => 51,
            'endLine' => 51,
            'startColumn' => 33,
            'endColumn' => 38,
            'parameterIndex' => 0,
            'isOptional' => false,
          ),
          'total' => 
          array (
            'name' => 'total',
            'default' => NULL,
            'type' => NULL,
            'isVariadic' => false,
            'byRef' => false,
            'isPromoted' => false,
            'attributes' => 
            array (
            ),
            'startLine' => 51,
            'endLine' => 51,
            'startColumn' => 41,
            'endColumn' => 46,
            'parameterIndex' => 1,
            'isOptional' => false,
          ),
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
            'startLine' => 51,
            'endLine' => 51,
            'startColumn' => 49,
            'endColumn' => 56,
            'parameterIndex' => 2,
            'isOptional' => false,
          ),
          'currentPage' => 
          array (
            'name' => 'currentPage',
            'default' => 
            array (
              'code' => 'null',
              'attributes' => 
              array (
                'startLine' => 51,
                'endLine' => 51,
                'startTokenPos' => 120,
                'startFilePos' => 1419,
                'endTokenPos' => 120,
                'endFilePos' => 1422,
              ),
            ),
            'type' => NULL,
            'isVariadic' => false,
            'byRef' => false,
            'isPromoted' => false,
            'attributes' => 
            array (
            ),
            'startLine' => 51,
            'endLine' => 51,
            'startColumn' => 59,
            'endColumn' => 77,
            'parameterIndex' => 3,
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
                'startLine' => 51,
                'endLine' => 51,
                'startTokenPos' => 129,
                'startFilePos' => 1442,
                'endTokenPos' => 130,
                'endFilePos' => 1443,
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
            'startLine' => 51,
            'endLine' => 51,
            'startColumn' => 80,
            'endColumn' => 98,
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
 * Create a new paginator instance.
 *
 * @param  Collection<TKey, TValue>|Arrayable<TKey, TValue>|iterable<TKey, TValue>|null  $items
 * @param  int  $total
 * @param  int  $perPage
 * @param  int|null  $currentPage
 * @param  array  $options  (path, query, fragment, pageName)
 */',
        'startLine' => 51,
        'endLine' => 65,
        'startColumn' => 5,
        'endColumn' => 5,
        'couldThrow' => false,
        'isClosure' => false,
        'isGenerator' => false,
        'isVariadic' => false,
        'modifiers' => 1,
        'namespace' => 'Illuminate\\Pagination',
        'declaringClassName' => 'Illuminate\\Pagination\\LengthAwarePaginator',
        'implementingClassName' => 'Illuminate\\Pagination\\LengthAwarePaginator',
        'currentClassName' => 'Illuminate\\Pagination\\LengthAwarePaginator',
        'aliasName' => NULL,
      ),
      'setCurrentPage' => 
      array (
        'name' => 'setCurrentPage',
        'parameters' => 
        array (
          'currentPage' => 
          array (
            'name' => 'currentPage',
            'default' => NULL,
            'type' => NULL,
            'isVariadic' => false,
            'byRef' => false,
            'isPromoted' => false,
            'attributes' => 
            array (
            ),
            'startLine' => 74,
            'endLine' => 74,
            'startColumn' => 39,
            'endColumn' => 50,
            'parameterIndex' => 0,
            'isOptional' => false,
          ),
          'pageName' => 
          array (
            'name' => 'pageName',
            'default' => NULL,
            'type' => NULL,
            'isVariadic' => false,
            'byRef' => false,
            'isPromoted' => false,
            'attributes' => 
            array (
            ),
            'startLine' => 74,
            'endLine' => 74,
            'startColumn' => 53,
            'endColumn' => 61,
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
 * Get the current page for the request.
 *
 * @param  int  $currentPage
 * @param  string  $pageName
 * @return int
 */',
        'startLine' => 74,
        'endLine' => 79,
        'startColumn' => 5,
        'endColumn' => 5,
        'couldThrow' => false,
        'isClosure' => false,
        'isGenerator' => false,
        'isVariadic' => false,
        'modifiers' => 2,
        'namespace' => 'Illuminate\\Pagination',
        'declaringClassName' => 'Illuminate\\Pagination\\LengthAwarePaginator',
        'implementingClassName' => 'Illuminate\\Pagination\\LengthAwarePaginator',
        'currentClassName' => 'Illuminate\\Pagination\\LengthAwarePaginator',
        'aliasName' => NULL,
      ),
      'links' => 
      array (
        'name' => 'links',
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
                'startLine' => 88,
                'endLine' => 88,
                'startTokenPos' => 372,
                'startFilePos' => 2604,
                'endTokenPos' => 372,
                'endFilePos' => 2607,
              ),
            ),
            'type' => NULL,
            'isVariadic' => false,
            'byRef' => false,
            'isPromoted' => false,
            'attributes' => 
            array (
            ),
            'startLine' => 88,
            'endLine' => 88,
            'startColumn' => 27,
            'endColumn' => 38,
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
                'startLine' => 88,
                'endLine' => 88,
                'startTokenPos' => 379,
                'startFilePos' => 2618,
                'endTokenPos' => 380,
                'endFilePos' => 2619,
              ),
            ),
            'type' => NULL,
            'isVariadic' => false,
            'byRef' => false,
            'isPromoted' => false,
            'attributes' => 
            array (
            ),
            'startLine' => 88,
            'endLine' => 88,
            'startColumn' => 41,
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
 * Render the paginator using the given view.
 *
 * @param  string|null  $view
 * @param  array  $data
 * @return \\Illuminate\\Contracts\\Support\\Htmlable
 */',
        'startLine' => 88,
        'endLine' => 91,
        'startColumn' => 5,
        'endColumn' => 5,
        'couldThrow' => false,
        'isClosure' => false,
        'isGenerator' => false,
        'isVariadic' => false,
        'modifiers' => 1,
        'namespace' => 'Illuminate\\Pagination',
        'declaringClassName' => 'Illuminate\\Pagination\\LengthAwarePaginator',
        'implementingClassName' => 'Illuminate\\Pagination\\LengthAwarePaginator',
        'currentClassName' => 'Illuminate\\Pagination\\LengthAwarePaginator',
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
                'startLine' => 100,
                'endLine' => 100,
                'startTokenPos' => 412,
                'startFilePos' => 2903,
                'endTokenPos' => 412,
                'endFilePos' => 2906,
              ),
            ),
            'type' => NULL,
            'isVariadic' => false,
            'byRef' => false,
            'isPromoted' => false,
            'attributes' => 
            array (
            ),
            'startLine' => 100,
            'endLine' => 100,
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
                'startLine' => 100,
                'endLine' => 100,
                'startTokenPos' => 419,
                'startFilePos' => 2917,
                'endTokenPos' => 420,
                'endFilePos' => 2918,
              ),
            ),
            'type' => NULL,
            'isVariadic' => false,
            'byRef' => false,
            'isPromoted' => false,
            'attributes' => 
            array (
            ),
            'startLine' => 100,
            'endLine' => 100,
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
 * Render the paginator using the given view.
 *
 * @param  string|null  $view
 * @param  array  $data
 * @return \\Illuminate\\Contracts\\Support\\Htmlable
 */',
        'startLine' => 100,
        'endLine' => 106,
        'startColumn' => 5,
        'endColumn' => 5,
        'couldThrow' => false,
        'isClosure' => false,
        'isGenerator' => false,
        'isVariadic' => false,
        'modifiers' => 1,
        'namespace' => 'Illuminate\\Pagination',
        'declaringClassName' => 'Illuminate\\Pagination\\LengthAwarePaginator',
        'implementingClassName' => 'Illuminate\\Pagination\\LengthAwarePaginator',
        'currentClassName' => 'Illuminate\\Pagination\\LengthAwarePaginator',
        'aliasName' => NULL,
      ),
      'linkCollection' => 
      array (
        'name' => 'linkCollection',
        'parameters' => 
        array (
        ),
        'returnsReference' => false,
        'returnType' => NULL,
        'attributes' => 
        array (
        ),
        'docComment' => '/**
 * Get the paginator links as a collection (for JSON responses).
 *
 * @return \\Illuminate\\Support\\Collection
 */',
        'startLine' => 113,
        'endLine' => 139,
        'startColumn' => 5,
        'endColumn' => 5,
        'couldThrow' => false,
        'isClosure' => false,
        'isGenerator' => false,
        'isVariadic' => false,
        'modifiers' => 1,
        'namespace' => 'Illuminate\\Pagination',
        'declaringClassName' => 'Illuminate\\Pagination\\LengthAwarePaginator',
        'implementingClassName' => 'Illuminate\\Pagination\\LengthAwarePaginator',
        'currentClassName' => 'Illuminate\\Pagination\\LengthAwarePaginator',
        'aliasName' => NULL,
      ),
      'elements' => 
      array (
        'name' => 'elements',
        'parameters' => 
        array (
        ),
        'returnsReference' => false,
        'returnType' => NULL,
        'attributes' => 
        array (
        ),
        'docComment' => '/**
 * Get the array of elements to pass to the view.
 *
 * @return array
 */',
        'startLine' => 146,
        'endLine' => 157,
        'startColumn' => 5,
        'endColumn' => 5,
        'couldThrow' => false,
        'isClosure' => false,
        'isGenerator' => false,
        'isVariadic' => false,
        'modifiers' => 2,
        'namespace' => 'Illuminate\\Pagination',
        'declaringClassName' => 'Illuminate\\Pagination\\LengthAwarePaginator',
        'implementingClassName' => 'Illuminate\\Pagination\\LengthAwarePaginator',
        'currentClassName' => 'Illuminate\\Pagination\\LengthAwarePaginator',
        'aliasName' => NULL,
      ),
      'total' => 
      array (
        'name' => 'total',
        'parameters' => 
        array (
        ),
        'returnsReference' => false,
        'returnType' => NULL,
        'attributes' => 
        array (
        ),
        'docComment' => '/**
 * Get the total number of items being paginated.
 *
 * @return int
 */',
        'startLine' => 164,
        'endLine' => 167,
        'startColumn' => 5,
        'endColumn' => 5,
        'couldThrow' => false,
        'isClosure' => false,
        'isGenerator' => false,
        'isVariadic' => false,
        'modifiers' => 1,
        'namespace' => 'Illuminate\\Pagination',
        'declaringClassName' => 'Illuminate\\Pagination\\LengthAwarePaginator',
        'implementingClassName' => 'Illuminate\\Pagination\\LengthAwarePaginator',
        'currentClassName' => 'Illuminate\\Pagination\\LengthAwarePaginator',
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
 * Determine if there are more items in the data source.
 *
 * @return bool
 */',
        'startLine' => 174,
        'endLine' => 177,
        'startColumn' => 5,
        'endColumn' => 5,
        'couldThrow' => false,
        'isClosure' => false,
        'isGenerator' => false,
        'isVariadic' => false,
        'modifiers' => 1,
        'namespace' => 'Illuminate\\Pagination',
        'declaringClassName' => 'Illuminate\\Pagination\\LengthAwarePaginator',
        'implementingClassName' => 'Illuminate\\Pagination\\LengthAwarePaginator',
        'currentClassName' => 'Illuminate\\Pagination\\LengthAwarePaginator',
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
 * Get the URL for the next page.
 *
 * @return string|null
 */',
        'startLine' => 184,
        'endLine' => 189,
        'startColumn' => 5,
        'endColumn' => 5,
        'couldThrow' => false,
        'isClosure' => false,
        'isGenerator' => false,
        'isVariadic' => false,
        'modifiers' => 1,
        'namespace' => 'Illuminate\\Pagination',
        'declaringClassName' => 'Illuminate\\Pagination\\LengthAwarePaginator',
        'implementingClassName' => 'Illuminate\\Pagination\\LengthAwarePaginator',
        'currentClassName' => 'Illuminate\\Pagination\\LengthAwarePaginator',
        'aliasName' => NULL,
      ),
      'lastPage' => 
      array (
        'name' => 'lastPage',
        'parameters' => 
        array (
        ),
        'returnsReference' => false,
        'returnType' => NULL,
        'attributes' => 
        array (
        ),
        'docComment' => '/**
 * Get the last page.
 *
 * @return int
 */',
        'startLine' => 196,
        'endLine' => 199,
        'startColumn' => 5,
        'endColumn' => 5,
        'couldThrow' => false,
        'isClosure' => false,
        'isGenerator' => false,
        'isVariadic' => false,
        'modifiers' => 1,
        'namespace' => 'Illuminate\\Pagination',
        'declaringClassName' => 'Illuminate\\Pagination\\LengthAwarePaginator',
        'implementingClassName' => 'Illuminate\\Pagination\\LengthAwarePaginator',
        'currentClassName' => 'Illuminate\\Pagination\\LengthAwarePaginator',
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
 * Get the instance as an array.
 *
 * @return array
 */',
        'startLine' => 206,
        'endLine' => 223,
        'startColumn' => 5,
        'endColumn' => 5,
        'couldThrow' => false,
        'isClosure' => false,
        'isGenerator' => false,
        'isVariadic' => false,
        'modifiers' => 1,
        'namespace' => 'Illuminate\\Pagination',
        'declaringClassName' => 'Illuminate\\Pagination\\LengthAwarePaginator',
        'implementingClassName' => 'Illuminate\\Pagination\\LengthAwarePaginator',
        'currentClassName' => 'Illuminate\\Pagination\\LengthAwarePaginator',
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
            'name' => 'array',
            'isIdentifier' => true,
          ),
        ),
        'attributes' => 
        array (
        ),
        'docComment' => '/**
 * Convert the object into something JSON serializable.
 *
 * @return array
 */',
        'startLine' => 230,
        'endLine' => 233,
        'startColumn' => 5,
        'endColumn' => 5,
        'couldThrow' => false,
        'isClosure' => false,
        'isGenerator' => false,
        'isVariadic' => false,
        'modifiers' => 1,
        'namespace' => 'Illuminate\\Pagination',
        'declaringClassName' => 'Illuminate\\Pagination\\LengthAwarePaginator',
        'implementingClassName' => 'Illuminate\\Pagination\\LengthAwarePaginator',
        'currentClassName' => 'Illuminate\\Pagination\\LengthAwarePaginator',
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
                'startLine' => 241,
                'endLine' => 241,
                'startTokenPos' => 1206,
                'startFilePos' => 6690,
                'endTokenPos' => 1206,
                'endFilePos' => 6690,
              ),
            ),
            'type' => NULL,
            'isVariadic' => false,
            'byRef' => false,
            'isPromoted' => false,
            'attributes' => 
            array (
            ),
            'startLine' => 241,
            'endLine' => 241,
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
 * Convert the object to its JSON representation.
 *
 * @param  int  $options
 * @return string
 */',
        'startLine' => 241,
        'endLine' => 244,
        'startColumn' => 5,
        'endColumn' => 5,
        'couldThrow' => false,
        'isClosure' => false,
        'isGenerator' => false,
        'isVariadic' => false,
        'modifiers' => 1,
        'namespace' => 'Illuminate\\Pagination',
        'declaringClassName' => 'Illuminate\\Pagination\\LengthAwarePaginator',
        'implementingClassName' => 'Illuminate\\Pagination\\LengthAwarePaginator',
        'currentClassName' => 'Illuminate\\Pagination\\LengthAwarePaginator',
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
                'startLine' => 252,
                'endLine' => 252,
                'startTokenPos' => 1242,
                'startFilePos' => 6948,
                'endTokenPos' => 1242,
                'endFilePos' => 6948,
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
            'startLine' => 252,
            'endLine' => 252,
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
 * Convert the object to pretty print formatted JSON.
 *
 * @param  int  $options
 * @return string
 */',
        'startLine' => 252,
        'endLine' => 255,
        'startColumn' => 5,
        'endColumn' => 5,
        'couldThrow' => false,
        'isClosure' => false,
        'isGenerator' => false,
        'isVariadic' => false,
        'modifiers' => 1,
        'namespace' => 'Illuminate\\Pagination',
        'declaringClassName' => 'Illuminate\\Pagination\\LengthAwarePaginator',
        'implementingClassName' => 'Illuminate\\Pagination\\LengthAwarePaginator',
        'currentClassName' => 'Illuminate\\Pagination\\LengthAwarePaginator',
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