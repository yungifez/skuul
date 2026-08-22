<?php declare(strict_types = 1);

// osfsl-/var/www/html/vendor/composer/../laravel/framework/src/Illuminate/Pagination/AbstractPaginator.php-PHPStan\BetterReflection\Reflection\ReflectionClass-Illuminate\Pagination\AbstractPaginator
return \PHPStan\Cache\CacheItem::__set_state(array(
   'variableKey' => 'v2-83c0581e5c08b0e7dc5e174bb8671500e8e19ee9e8b3f60f464e47cd9bd55136-8.5.9-6.70.0.3',
   'data' => 
  array (
    'locatedSource' => 
    array (
      'class' => 'PHPStan\\BetterReflection\\SourceLocator\\Located\\LocatedSource',
      'data' => 
      array (
        'name' => 'Illuminate\\Pagination\\AbstractPaginator',
        'filename' => '/var/www/html/vendor/composer/../laravel/framework/src/Illuminate/Pagination/AbstractPaginator.php',
      ),
    ),
    'namespace' => 'Illuminate\\Pagination',
    'name' => 'Illuminate\\Pagination\\AbstractPaginator',
    'shortName' => 'AbstractPaginator',
    'isInterface' => false,
    'isTrait' => false,
    'isEnum' => false,
    'isBackedEnum' => false,
    'modifiers' => 64,
    'docComment' => '/**
 * @template TKey of array-key
 *
 * @template-covariant TValue
 *
 * @mixin \\Illuminate\\Support\\Collection<TKey, TValue>
 */',
    'attributes' => 
    array (
    ),
    'startLine' => 23,
    'endLine' => 830,
    'startColumn' => 1,
    'endColumn' => 1,
    'parentClassName' => NULL,
    'implementsClassNames' => 
    array (
      0 => 'Illuminate\\Contracts\\Support\\CanBeEscapedWhenCastToString',
      1 => 'Illuminate\\Contracts\\Support\\Htmlable',
      2 => 'Stringable',
    ),
    'traitClassNames' => 
    array (
      0 => 'Illuminate\\Support\\Traits\\ForwardsCalls',
      1 => 'Illuminate\\Support\\Traits\\Tappable',
      2 => 'Illuminate\\Support\\Traits\\TransformsToResourceCollection',
    ),
    'immediateConstants' => 
    array (
    ),
    'immediateProperties' => 
    array (
      'items' => 
      array (
        'declaringClassName' => 'Illuminate\\Pagination\\AbstractPaginator',
        'implementingClassName' => 'Illuminate\\Pagination\\AbstractPaginator',
        'name' => 'items',
        'modifiers' => 2,
        'type' => NULL,
        'default' => NULL,
        'docComment' => '/**
 * All of the items being paginated.
 *
 * @var \\Illuminate\\Support\\Collection<TKey, TValue>
 */',
        'attributes' => 
        array (
        ),
        'startLine' => 32,
        'endLine' => 32,
        'startColumn' => 5,
        'endColumn' => 21,
        'isPromoted' => false,
        'declaredAtCompileTime' => true,
        'immediateVirtual' => false,
        'immediateHooks' => 
        array (
        ),
      ),
      'perPage' => 
      array (
        'declaringClassName' => 'Illuminate\\Pagination\\AbstractPaginator',
        'implementingClassName' => 'Illuminate\\Pagination\\AbstractPaginator',
        'name' => 'perPage',
        'modifiers' => 2,
        'type' => NULL,
        'default' => NULL,
        'docComment' => '/**
 * The number of items to be shown per page.
 *
 * @var int
 */',
        'attributes' => 
        array (
        ),
        'startLine' => 39,
        'endLine' => 39,
        'startColumn' => 5,
        'endColumn' => 23,
        'isPromoted' => false,
        'declaredAtCompileTime' => true,
        'immediateVirtual' => false,
        'immediateHooks' => 
        array (
        ),
      ),
      'currentPage' => 
      array (
        'declaringClassName' => 'Illuminate\\Pagination\\AbstractPaginator',
        'implementingClassName' => 'Illuminate\\Pagination\\AbstractPaginator',
        'name' => 'currentPage',
        'modifiers' => 2,
        'type' => NULL,
        'default' => NULL,
        'docComment' => '/**
 * The current page being "viewed".
 *
 * @var int
 */',
        'attributes' => 
        array (
        ),
        'startLine' => 46,
        'endLine' => 46,
        'startColumn' => 5,
        'endColumn' => 27,
        'isPromoted' => false,
        'declaredAtCompileTime' => true,
        'immediateVirtual' => false,
        'immediateHooks' => 
        array (
        ),
      ),
      'path' => 
      array (
        'declaringClassName' => 'Illuminate\\Pagination\\AbstractPaginator',
        'implementingClassName' => 'Illuminate\\Pagination\\AbstractPaginator',
        'name' => 'path',
        'modifiers' => 2,
        'type' => NULL,
        'default' => 
        array (
          'code' => '\'/\'',
          'attributes' => 
          array (
            'startLine' => 53,
            'endLine' => 53,
            'startTokenPos' => 117,
            'startFilePos' => 1170,
            'endTokenPos' => 117,
            'endFilePos' => 1172,
          ),
        ),
        'docComment' => '/**
 * The base path to assign to all URLs.
 *
 * @var string
 */',
        'attributes' => 
        array (
        ),
        'startLine' => 53,
        'endLine' => 53,
        'startColumn' => 5,
        'endColumn' => 26,
        'isPromoted' => false,
        'declaredAtCompileTime' => true,
        'immediateVirtual' => false,
        'immediateHooks' => 
        array (
        ),
      ),
      'query' => 
      array (
        'declaringClassName' => 'Illuminate\\Pagination\\AbstractPaginator',
        'implementingClassName' => 'Illuminate\\Pagination\\AbstractPaginator',
        'name' => 'query',
        'modifiers' => 2,
        'type' => NULL,
        'default' => 
        array (
          'code' => '[]',
          'attributes' => 
          array (
            'startLine' => 60,
            'endLine' => 60,
            'startTokenPos' => 128,
            'startFilePos' => 1288,
            'endTokenPos' => 129,
            'endFilePos' => 1289,
          ),
        ),
        'docComment' => '/**
 * The query parameters to add to all URLs.
 *
 * @var array
 */',
        'attributes' => 
        array (
        ),
        'startLine' => 60,
        'endLine' => 60,
        'startColumn' => 5,
        'endColumn' => 26,
        'isPromoted' => false,
        'declaredAtCompileTime' => true,
        'immediateVirtual' => false,
        'immediateHooks' => 
        array (
        ),
      ),
      'fragment' => 
      array (
        'declaringClassName' => 'Illuminate\\Pagination\\AbstractPaginator',
        'implementingClassName' => 'Illuminate\\Pagination\\AbstractPaginator',
        'name' => 'fragment',
        'modifiers' => 2,
        'type' => NULL,
        'default' => NULL,
        'docComment' => '/**
 * The URL fragment to add to all URLs.
 *
 * @var string|null
 */',
        'attributes' => 
        array (
        ),
        'startLine' => 67,
        'endLine' => 67,
        'startColumn' => 5,
        'endColumn' => 24,
        'isPromoted' => false,
        'declaredAtCompileTime' => true,
        'immediateVirtual' => false,
        'immediateHooks' => 
        array (
        ),
      ),
      'pageName' => 
      array (
        'declaringClassName' => 'Illuminate\\Pagination\\AbstractPaginator',
        'implementingClassName' => 'Illuminate\\Pagination\\AbstractPaginator',
        'name' => 'pageName',
        'modifiers' => 2,
        'type' => NULL,
        'default' => 
        array (
          'code' => '\'page\'',
          'attributes' => 
          array (
            'startLine' => 74,
            'endLine' => 74,
            'startTokenPos' => 147,
            'startFilePos' => 1535,
            'endTokenPos' => 147,
            'endFilePos' => 1540,
          ),
        ),
        'docComment' => '/**
 * The query string variable used to store the page.
 *
 * @var string
 */',
        'attributes' => 
        array (
        ),
        'startLine' => 74,
        'endLine' => 74,
        'startColumn' => 5,
        'endColumn' => 33,
        'isPromoted' => false,
        'declaredAtCompileTime' => true,
        'immediateVirtual' => false,
        'immediateHooks' => 
        array (
        ),
      ),
      'escapeWhenCastingToString' => 
      array (
        'declaringClassName' => 'Illuminate\\Pagination\\AbstractPaginator',
        'implementingClassName' => 'Illuminate\\Pagination\\AbstractPaginator',
        'name' => 'escapeWhenCastingToString',
        'modifiers' => 2,
        'type' => NULL,
        'default' => 
        array (
          'code' => 'false',
          'attributes' => 
          array (
            'startLine' => 81,
            'endLine' => 81,
            'startTokenPos' => 158,
            'startFilePos' => 1733,
            'endTokenPos' => 158,
            'endFilePos' => 1737,
          ),
        ),
        'docComment' => '/**
 * Indicates that the paginator\'s string representation should be escaped when __toString is invoked.
 *
 * @var bool
 */',
        'attributes' => 
        array (
        ),
        'startLine' => 81,
        'endLine' => 81,
        'startColumn' => 5,
        'endColumn' => 49,
        'isPromoted' => false,
        'declaredAtCompileTime' => true,
        'immediateVirtual' => false,
        'immediateHooks' => 
        array (
        ),
      ),
      'onEachSide' => 
      array (
        'declaringClassName' => 'Illuminate\\Pagination\\AbstractPaginator',
        'implementingClassName' => 'Illuminate\\Pagination\\AbstractPaginator',
        'name' => 'onEachSide',
        'modifiers' => 1,
        'type' => NULL,
        'default' => 
        array (
          'code' => '3',
          'attributes' => 
          array (
            'startLine' => 88,
            'endLine' => 88,
            'startTokenPos' => 169,
            'startFilePos' => 1878,
            'endTokenPos' => 169,
            'endFilePos' => 1878,
          ),
        ),
        'docComment' => '/**
 * The number of links to display on each side of current page link.
 *
 * @var int
 */',
        'attributes' => 
        array (
        ),
        'startLine' => 88,
        'endLine' => 88,
        'startColumn' => 5,
        'endColumn' => 27,
        'isPromoted' => false,
        'declaredAtCompileTime' => true,
        'immediateVirtual' => false,
        'immediateHooks' => 
        array (
        ),
      ),
      'options' => 
      array (
        'declaringClassName' => 'Illuminate\\Pagination\\AbstractPaginator',
        'implementingClassName' => 'Illuminate\\Pagination\\AbstractPaginator',
        'name' => 'options',
        'modifiers' => 2,
        'type' => NULL,
        'default' => NULL,
        'docComment' => '/**
 * The paginator options.
 *
 * @var array
 */',
        'attributes' => 
        array (
        ),
        'startLine' => 95,
        'endLine' => 95,
        'startColumn' => 5,
        'endColumn' => 23,
        'isPromoted' => false,
        'declaredAtCompileTime' => true,
        'immediateVirtual' => false,
        'immediateHooks' => 
        array (
        ),
      ),
      'currentPathResolver' => 
      array (
        'declaringClassName' => 'Illuminate\\Pagination\\AbstractPaginator',
        'implementingClassName' => 'Illuminate\\Pagination\\AbstractPaginator',
        'name' => 'currentPathResolver',
        'modifiers' => 18,
        'type' => NULL,
        'default' => NULL,
        'docComment' => '/**
 * The current path resolver callback.
 *
 * @var \\Closure
 */',
        'attributes' => 
        array (
        ),
        'startLine' => 102,
        'endLine' => 102,
        'startColumn' => 5,
        'endColumn' => 42,
        'isPromoted' => false,
        'declaredAtCompileTime' => true,
        'immediateVirtual' => false,
        'immediateHooks' => 
        array (
        ),
      ),
      'currentPageResolver' => 
      array (
        'declaringClassName' => 'Illuminate\\Pagination\\AbstractPaginator',
        'implementingClassName' => 'Illuminate\\Pagination\\AbstractPaginator',
        'name' => 'currentPageResolver',
        'modifiers' => 18,
        'type' => NULL,
        'default' => NULL,
        'docComment' => '/**
 * The current page resolver callback.
 *
 * @var \\Closure
 */',
        'attributes' => 
        array (
        ),
        'startLine' => 109,
        'endLine' => 109,
        'startColumn' => 5,
        'endColumn' => 42,
        'isPromoted' => false,
        'declaredAtCompileTime' => true,
        'immediateVirtual' => false,
        'immediateHooks' => 
        array (
        ),
      ),
      'queryStringResolver' => 
      array (
        'declaringClassName' => 'Illuminate\\Pagination\\AbstractPaginator',
        'implementingClassName' => 'Illuminate\\Pagination\\AbstractPaginator',
        'name' => 'queryStringResolver',
        'modifiers' => 18,
        'type' => NULL,
        'default' => NULL,
        'docComment' => '/**
 * The query string resolver callback.
 *
 * @var \\Closure
 */',
        'attributes' => 
        array (
        ),
        'startLine' => 116,
        'endLine' => 116,
        'startColumn' => 5,
        'endColumn' => 42,
        'isPromoted' => false,
        'declaredAtCompileTime' => true,
        'immediateVirtual' => false,
        'immediateHooks' => 
        array (
        ),
      ),
      'viewFactoryResolver' => 
      array (
        'declaringClassName' => 'Illuminate\\Pagination\\AbstractPaginator',
        'implementingClassName' => 'Illuminate\\Pagination\\AbstractPaginator',
        'name' => 'viewFactoryResolver',
        'modifiers' => 18,
        'type' => NULL,
        'default' => NULL,
        'docComment' => '/**
 * The view factory resolver callback.
 *
 * @var \\Closure
 */',
        'attributes' => 
        array (
        ),
        'startLine' => 123,
        'endLine' => 123,
        'startColumn' => 5,
        'endColumn' => 42,
        'isPromoted' => false,
        'declaredAtCompileTime' => true,
        'immediateVirtual' => false,
        'immediateHooks' => 
        array (
        ),
      ),
      'defaultView' => 
      array (
        'declaringClassName' => 'Illuminate\\Pagination\\AbstractPaginator',
        'implementingClassName' => 'Illuminate\\Pagination\\AbstractPaginator',
        'name' => 'defaultView',
        'modifiers' => 17,
        'type' => NULL,
        'default' => 
        array (
          'code' => '\'pagination::tailwind\'',
          'attributes' => 
          array (
            'startLine' => 130,
            'endLine' => 130,
            'startTokenPos' => 225,
            'startFilePos' => 2613,
            'endTokenPos' => 225,
            'endFilePos' => 2634,
          ),
        ),
        'docComment' => '/**
 * The default pagination view.
 *
 * @var string
 */',
        'attributes' => 
        array (
        ),
        'startLine' => 130,
        'endLine' => 130,
        'startColumn' => 5,
        'endColumn' => 56,
        'isPromoted' => false,
        'declaredAtCompileTime' => true,
        'immediateVirtual' => false,
        'immediateHooks' => 
        array (
        ),
      ),
      'defaultSimpleView' => 
      array (
        'declaringClassName' => 'Illuminate\\Pagination\\AbstractPaginator',
        'implementingClassName' => 'Illuminate\\Pagination\\AbstractPaginator',
        'name' => 'defaultSimpleView',
        'modifiers' => 17,
        'type' => NULL,
        'default' => 
        array (
          'code' => '\'pagination::simple-tailwind\'',
          'attributes' => 
          array (
            'startLine' => 137,
            'endLine' => 137,
            'startTokenPos' => 238,
            'startFilePos' => 2764,
            'endTokenPos' => 238,
            'endFilePos' => 2792,
          ),
        ),
        'docComment' => '/**
 * The default "simple" pagination view.
 *
 * @var string
 */',
        'attributes' => 
        array (
        ),
        'startLine' => 137,
        'endLine' => 137,
        'startColumn' => 5,
        'endColumn' => 69,
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
      'isValidPageNumber' => 
      array (
        'name' => 'isValidPageNumber',
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
            'startLine' => 145,
            'endLine' => 145,
            'startColumn' => 42,
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
 * Determine if the given value is a valid page number.
 *
 * @param  int  $page
 * @return bool
 */',
        'startLine' => 145,
        'endLine' => 148,
        'startColumn' => 5,
        'endColumn' => 5,
        'couldThrow' => false,
        'isClosure' => false,
        'isGenerator' => false,
        'isVariadic' => false,
        'modifiers' => 2,
        'namespace' => 'Illuminate\\Pagination',
        'declaringClassName' => 'Illuminate\\Pagination\\AbstractPaginator',
        'implementingClassName' => 'Illuminate\\Pagination\\AbstractPaginator',
        'currentClassName' => 'Illuminate\\Pagination\\AbstractPaginator',
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
 * Get the URL for the previous page.
 *
 * @return string|null
 */',
        'startLine' => 155,
        'endLine' => 160,
        'startColumn' => 5,
        'endColumn' => 5,
        'couldThrow' => false,
        'isClosure' => false,
        'isGenerator' => false,
        'isVariadic' => false,
        'modifiers' => 1,
        'namespace' => 'Illuminate\\Pagination',
        'declaringClassName' => 'Illuminate\\Pagination\\AbstractPaginator',
        'implementingClassName' => 'Illuminate\\Pagination\\AbstractPaginator',
        'currentClassName' => 'Illuminate\\Pagination\\AbstractPaginator',
        'aliasName' => NULL,
      ),
      'getUrlRange' => 
      array (
        'name' => 'getUrlRange',
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
            'startLine' => 169,
            'endLine' => 169,
            'startColumn' => 33,
            'endColumn' => 38,
            'parameterIndex' => 0,
            'isOptional' => false,
          ),
          'end' => 
          array (
            'name' => 'end',
            'default' => NULL,
            'type' => NULL,
            'isVariadic' => false,
            'byRef' => false,
            'isPromoted' => false,
            'attributes' => 
            array (
            ),
            'startLine' => 169,
            'endLine' => 169,
            'startColumn' => 41,
            'endColumn' => 44,
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
 * Create a range of pagination URLs.
 *
 * @param  int  $start
 * @param  int  $end
 * @return array
 */',
        'startLine' => 169,
        'endLine' => 174,
        'startColumn' => 5,
        'endColumn' => 5,
        'couldThrow' => false,
        'isClosure' => false,
        'isGenerator' => false,
        'isVariadic' => false,
        'modifiers' => 1,
        'namespace' => 'Illuminate\\Pagination',
        'declaringClassName' => 'Illuminate\\Pagination\\AbstractPaginator',
        'implementingClassName' => 'Illuminate\\Pagination\\AbstractPaginator',
        'currentClassName' => 'Illuminate\\Pagination\\AbstractPaginator',
        'aliasName' => NULL,
      ),
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
            'startLine' => 182,
            'endLine' => 182,
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
 * Get the URL for a given page number.
 *
 * @param  int  $page
 * @return string
 */',
        'startLine' => 182,
        'endLine' => 201,
        'startColumn' => 5,
        'endColumn' => 5,
        'couldThrow' => false,
        'isClosure' => false,
        'isGenerator' => false,
        'isVariadic' => false,
        'modifiers' => 1,
        'namespace' => 'Illuminate\\Pagination',
        'declaringClassName' => 'Illuminate\\Pagination\\AbstractPaginator',
        'implementingClassName' => 'Illuminate\\Pagination\\AbstractPaginator',
        'currentClassName' => 'Illuminate\\Pagination\\AbstractPaginator',
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
                'startLine' => 209,
                'endLine' => 209,
                'startTokenPos' => 540,
                'startFilePos' => 4699,
                'endTokenPos' => 540,
                'endFilePos' => 4702,
              ),
            ),
            'type' => NULL,
            'isVariadic' => false,
            'byRef' => false,
            'isPromoted' => false,
            'attributes' => 
            array (
            ),
            'startLine' => 209,
            'endLine' => 209,
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
        'startLine' => 209,
        'endLine' => 218,
        'startColumn' => 5,
        'endColumn' => 5,
        'couldThrow' => false,
        'isClosure' => false,
        'isGenerator' => false,
        'isVariadic' => false,
        'modifiers' => 1,
        'namespace' => 'Illuminate\\Pagination',
        'declaringClassName' => 'Illuminate\\Pagination\\AbstractPaginator',
        'implementingClassName' => 'Illuminate\\Pagination\\AbstractPaginator',
        'currentClassName' => 'Illuminate\\Pagination\\AbstractPaginator',
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
            'startLine' => 227,
            'endLine' => 227,
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
                'startLine' => 227,
                'endLine' => 227,
                'startTokenPos' => 596,
                'startFilePos' => 5078,
                'endTokenPos' => 596,
                'endFilePos' => 5081,
              ),
            ),
            'type' => NULL,
            'isVariadic' => false,
            'byRef' => false,
            'isPromoted' => false,
            'attributes' => 
            array (
            ),
            'startLine' => 227,
            'endLine' => 227,
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
        'startLine' => 227,
        'endLine' => 238,
        'startColumn' => 5,
        'endColumn' => 5,
        'couldThrow' => false,
        'isClosure' => false,
        'isGenerator' => false,
        'isVariadic' => false,
        'modifiers' => 1,
        'namespace' => 'Illuminate\\Pagination',
        'declaringClassName' => 'Illuminate\\Pagination\\AbstractPaginator',
        'implementingClassName' => 'Illuminate\\Pagination\\AbstractPaginator',
        'currentClassName' => 'Illuminate\\Pagination\\AbstractPaginator',
        'aliasName' => NULL,
      ),
      'appendArray' => 
      array (
        'name' => 'appendArray',
        'parameters' => 
        array (
          'keys' => 
          array (
            'name' => 'keys',
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
            'startLine' => 246,
            'endLine' => 246,
            'startColumn' => 36,
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
 * Add an array of query string values.
 *
 * @param  array  $keys
 * @return $this
 */',
        'startLine' => 246,
        'endLine' => 253,
        'startColumn' => 5,
        'endColumn' => 5,
        'couldThrow' => false,
        'isClosure' => false,
        'isGenerator' => false,
        'isVariadic' => false,
        'modifiers' => 2,
        'namespace' => 'Illuminate\\Pagination',
        'declaringClassName' => 'Illuminate\\Pagination\\AbstractPaginator',
        'implementingClassName' => 'Illuminate\\Pagination\\AbstractPaginator',
        'currentClassName' => 'Illuminate\\Pagination\\AbstractPaginator',
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
        'startLine' => 260,
        'endLine' => 267,
        'startColumn' => 5,
        'endColumn' => 5,
        'couldThrow' => false,
        'isClosure' => false,
        'isGenerator' => false,
        'isVariadic' => false,
        'modifiers' => 1,
        'namespace' => 'Illuminate\\Pagination',
        'declaringClassName' => 'Illuminate\\Pagination\\AbstractPaginator',
        'implementingClassName' => 'Illuminate\\Pagination\\AbstractPaginator',
        'currentClassName' => 'Illuminate\\Pagination\\AbstractPaginator',
        'aliasName' => NULL,
      ),
      'addQuery' => 
      array (
        'name' => 'addQuery',
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
            'startLine' => 276,
            'endLine' => 276,
            'startColumn' => 33,
            'endColumn' => 36,
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
            'startLine' => 276,
            'endLine' => 276,
            'startColumn' => 39,
            'endColumn' => 44,
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
 * Add a query string value to the paginator.
 *
 * @param  string  $key
 * @param  string  $value
 * @return $this
 */',
        'startLine' => 276,
        'endLine' => 283,
        'startColumn' => 5,
        'endColumn' => 5,
        'couldThrow' => false,
        'isClosure' => false,
        'isGenerator' => false,
        'isVariadic' => false,
        'modifiers' => 2,
        'namespace' => 'Illuminate\\Pagination',
        'declaringClassName' => 'Illuminate\\Pagination\\AbstractPaginator',
        'implementingClassName' => 'Illuminate\\Pagination\\AbstractPaginator',
        'currentClassName' => 'Illuminate\\Pagination\\AbstractPaginator',
        'aliasName' => NULL,
      ),
      'buildFragment' => 
      array (
        'name' => 'buildFragment',
        'parameters' => 
        array (
        ),
        'returnsReference' => false,
        'returnType' => NULL,
        'attributes' => 
        array (
        ),
        'docComment' => '/**
 * Build the full fragment portion of a URL.
 *
 * @return string
 */',
        'startLine' => 290,
        'endLine' => 293,
        'startColumn' => 5,
        'endColumn' => 5,
        'couldThrow' => false,
        'isClosure' => false,
        'isGenerator' => false,
        'isVariadic' => false,
        'modifiers' => 2,
        'namespace' => 'Illuminate\\Pagination',
        'declaringClassName' => 'Illuminate\\Pagination\\AbstractPaginator',
        'implementingClassName' => 'Illuminate\\Pagination\\AbstractPaginator',
        'currentClassName' => 'Illuminate\\Pagination\\AbstractPaginator',
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
            'startLine' => 302,
            'endLine' => 302,
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
            'startLine' => 302,
            'endLine' => 302,
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
 * Load a set of relationships onto the mixed relationship collection.
 *
 * @param  string  $relation
 * @param  array  $relations
 * @return $this
 */',
        'startLine' => 302,
        'endLine' => 307,
        'startColumn' => 5,
        'endColumn' => 5,
        'couldThrow' => false,
        'isClosure' => false,
        'isGenerator' => false,
        'isVariadic' => false,
        'modifiers' => 1,
        'namespace' => 'Illuminate\\Pagination',
        'declaringClassName' => 'Illuminate\\Pagination\\AbstractPaginator',
        'implementingClassName' => 'Illuminate\\Pagination\\AbstractPaginator',
        'currentClassName' => 'Illuminate\\Pagination\\AbstractPaginator',
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
            'startLine' => 316,
            'endLine' => 316,
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
            'startLine' => 316,
            'endLine' => 316,
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
 * Load a set of relationship counts onto the mixed relationship collection.
 *
 * @param  string  $relation
 * @param  array  $relations
 * @return $this
 */',
        'startLine' => 316,
        'endLine' => 321,
        'startColumn' => 5,
        'endColumn' => 5,
        'couldThrow' => false,
        'isClosure' => false,
        'isGenerator' => false,
        'isVariadic' => false,
        'modifiers' => 1,
        'namespace' => 'Illuminate\\Pagination',
        'declaringClassName' => 'Illuminate\\Pagination\\AbstractPaginator',
        'implementingClassName' => 'Illuminate\\Pagination\\AbstractPaginator',
        'currentClassName' => 'Illuminate\\Pagination\\AbstractPaginator',
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
 * Get the slice of items being paginated.
 *
 * @return array<TKey, TValue>
 */',
        'startLine' => 328,
        'endLine' => 331,
        'startColumn' => 5,
        'endColumn' => 5,
        'couldThrow' => false,
        'isClosure' => false,
        'isGenerator' => false,
        'isVariadic' => false,
        'modifiers' => 1,
        'namespace' => 'Illuminate\\Pagination',
        'declaringClassName' => 'Illuminate\\Pagination\\AbstractPaginator',
        'implementingClassName' => 'Illuminate\\Pagination\\AbstractPaginator',
        'currentClassName' => 'Illuminate\\Pagination\\AbstractPaginator',
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
 * Get the number of the first item in the slice.
 *
 * @return int|null
 */',
        'startLine' => 338,
        'endLine' => 341,
        'startColumn' => 5,
        'endColumn' => 5,
        'couldThrow' => false,
        'isClosure' => false,
        'isGenerator' => false,
        'isVariadic' => false,
        'modifiers' => 1,
        'namespace' => 'Illuminate\\Pagination',
        'declaringClassName' => 'Illuminate\\Pagination\\AbstractPaginator',
        'implementingClassName' => 'Illuminate\\Pagination\\AbstractPaginator',
        'currentClassName' => 'Illuminate\\Pagination\\AbstractPaginator',
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
 * Get the number of the last item in the slice.
 *
 * @return int|null
 */',
        'startLine' => 348,
        'endLine' => 351,
        'startColumn' => 5,
        'endColumn' => 5,
        'couldThrow' => false,
        'isClosure' => false,
        'isGenerator' => false,
        'isVariadic' => false,
        'modifiers' => 1,
        'namespace' => 'Illuminate\\Pagination',
        'declaringClassName' => 'Illuminate\\Pagination\\AbstractPaginator',
        'implementingClassName' => 'Illuminate\\Pagination\\AbstractPaginator',
        'currentClassName' => 'Illuminate\\Pagination\\AbstractPaginator',
        'aliasName' => NULL,
      ),
      'through' => 
      array (
        'name' => 'through',
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
            'startLine' => 363,
            'endLine' => 363,
            'startColumn' => 29,
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
 * Transform each item in the slice of items using a callback.
 *
 * @template TMapValue
 *
 * @param  callable(TValue, TKey): TMapValue  $callback
 * @return $this
 *
 * @phpstan-this-out static<TKey, TMapValue>
 */',
        'startLine' => 363,
        'endLine' => 368,
        'startColumn' => 5,
        'endColumn' => 5,
        'couldThrow' => false,
        'isClosure' => false,
        'isGenerator' => false,
        'isVariadic' => false,
        'modifiers' => 1,
        'namespace' => 'Illuminate\\Pagination',
        'declaringClassName' => 'Illuminate\\Pagination\\AbstractPaginator',
        'implementingClassName' => 'Illuminate\\Pagination\\AbstractPaginator',
        'currentClassName' => 'Illuminate\\Pagination\\AbstractPaginator',
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
 * Get the number of items shown per page.
 *
 * @return int
 */',
        'startLine' => 375,
        'endLine' => 378,
        'startColumn' => 5,
        'endColumn' => 5,
        'couldThrow' => false,
        'isClosure' => false,
        'isGenerator' => false,
        'isVariadic' => false,
        'modifiers' => 1,
        'namespace' => 'Illuminate\\Pagination',
        'declaringClassName' => 'Illuminate\\Pagination\\AbstractPaginator',
        'implementingClassName' => 'Illuminate\\Pagination\\AbstractPaginator',
        'currentClassName' => 'Illuminate\\Pagination\\AbstractPaginator',
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
        'startLine' => 385,
        'endLine' => 388,
        'startColumn' => 5,
        'endColumn' => 5,
        'couldThrow' => false,
        'isClosure' => false,
        'isGenerator' => false,
        'isVariadic' => false,
        'modifiers' => 1,
        'namespace' => 'Illuminate\\Pagination',
        'declaringClassName' => 'Illuminate\\Pagination\\AbstractPaginator',
        'implementingClassName' => 'Illuminate\\Pagination\\AbstractPaginator',
        'currentClassName' => 'Illuminate\\Pagination\\AbstractPaginator',
        'aliasName' => NULL,
      ),
      'onFirstPage' => 
      array (
        'name' => 'onFirstPage',
        'parameters' => 
        array (
        ),
        'returnsReference' => false,
        'returnType' => NULL,
        'attributes' => 
        array (
        ),
        'docComment' => '/**
 * Determine if the paginator is on the first page.
 *
 * @return bool
 */',
        'startLine' => 395,
        'endLine' => 398,
        'startColumn' => 5,
        'endColumn' => 5,
        'couldThrow' => false,
        'isClosure' => false,
        'isGenerator' => false,
        'isVariadic' => false,
        'modifiers' => 1,
        'namespace' => 'Illuminate\\Pagination',
        'declaringClassName' => 'Illuminate\\Pagination\\AbstractPaginator',
        'implementingClassName' => 'Illuminate\\Pagination\\AbstractPaginator',
        'currentClassName' => 'Illuminate\\Pagination\\AbstractPaginator',
        'aliasName' => NULL,
      ),
      'onLastPage' => 
      array (
        'name' => 'onLastPage',
        'parameters' => 
        array (
        ),
        'returnsReference' => false,
        'returnType' => NULL,
        'attributes' => 
        array (
        ),
        'docComment' => '/**
 * Determine if the paginator is on the last page.
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
        'namespace' => 'Illuminate\\Pagination',
        'declaringClassName' => 'Illuminate\\Pagination\\AbstractPaginator',
        'implementingClassName' => 'Illuminate\\Pagination\\AbstractPaginator',
        'currentClassName' => 'Illuminate\\Pagination\\AbstractPaginator',
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
 * Get the current page.
 *
 * @return int
 */',
        'startLine' => 415,
        'endLine' => 418,
        'startColumn' => 5,
        'endColumn' => 5,
        'couldThrow' => false,
        'isClosure' => false,
        'isGenerator' => false,
        'isVariadic' => false,
        'modifiers' => 1,
        'namespace' => 'Illuminate\\Pagination',
        'declaringClassName' => 'Illuminate\\Pagination\\AbstractPaginator',
        'implementingClassName' => 'Illuminate\\Pagination\\AbstractPaginator',
        'currentClassName' => 'Illuminate\\Pagination\\AbstractPaginator',
        'aliasName' => NULL,
      ),
      'getPageName' => 
      array (
        'name' => 'getPageName',
        'parameters' => 
        array (
        ),
        'returnsReference' => false,
        'returnType' => NULL,
        'attributes' => 
        array (
        ),
        'docComment' => '/**
 * Get the query string variable used to store the page.
 *
 * @return string
 */',
        'startLine' => 425,
        'endLine' => 428,
        'startColumn' => 5,
        'endColumn' => 5,
        'couldThrow' => false,
        'isClosure' => false,
        'isGenerator' => false,
        'isVariadic' => false,
        'modifiers' => 1,
        'namespace' => 'Illuminate\\Pagination',
        'declaringClassName' => 'Illuminate\\Pagination\\AbstractPaginator',
        'implementingClassName' => 'Illuminate\\Pagination\\AbstractPaginator',
        'currentClassName' => 'Illuminate\\Pagination\\AbstractPaginator',
        'aliasName' => NULL,
      ),
      'setPageName' => 
      array (
        'name' => 'setPageName',
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
            'startLine' => 436,
            'endLine' => 436,
            'startColumn' => 33,
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
 * Set the query string variable used to store the page.
 *
 * @param  string  $name
 * @return $this
 */',
        'startLine' => 436,
        'endLine' => 441,
        'startColumn' => 5,
        'endColumn' => 5,
        'couldThrow' => false,
        'isClosure' => false,
        'isGenerator' => false,
        'isVariadic' => false,
        'modifiers' => 1,
        'namespace' => 'Illuminate\\Pagination',
        'declaringClassName' => 'Illuminate\\Pagination\\AbstractPaginator',
        'implementingClassName' => 'Illuminate\\Pagination\\AbstractPaginator',
        'currentClassName' => 'Illuminate\\Pagination\\AbstractPaginator',
        'aliasName' => NULL,
      ),
      'withPath' => 
      array (
        'name' => 'withPath',
        'parameters' => 
        array (
          'path' => 
          array (
            'name' => 'path',
            'default' => NULL,
            'type' => NULL,
            'isVariadic' => false,
            'byRef' => false,
            'isPromoted' => false,
            'attributes' => 
            array (
            ),
            'startLine' => 449,
            'endLine' => 449,
            'startColumn' => 30,
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
 * Set the base path to assign to all URLs.
 *
 * @param  string  $path
 * @return $this
 */',
        'startLine' => 449,
        'endLine' => 452,
        'startColumn' => 5,
        'endColumn' => 5,
        'couldThrow' => false,
        'isClosure' => false,
        'isGenerator' => false,
        'isVariadic' => false,
        'modifiers' => 1,
        'namespace' => 'Illuminate\\Pagination',
        'declaringClassName' => 'Illuminate\\Pagination\\AbstractPaginator',
        'implementingClassName' => 'Illuminate\\Pagination\\AbstractPaginator',
        'currentClassName' => 'Illuminate\\Pagination\\AbstractPaginator',
        'aliasName' => NULL,
      ),
      'setPath' => 
      array (
        'name' => 'setPath',
        'parameters' => 
        array (
          'path' => 
          array (
            'name' => 'path',
            'default' => NULL,
            'type' => NULL,
            'isVariadic' => false,
            'byRef' => false,
            'isPromoted' => false,
            'attributes' => 
            array (
            ),
            'startLine' => 460,
            'endLine' => 460,
            'startColumn' => 29,
            'endColumn' => 33,
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
 * Set the base path to assign to all URLs.
 *
 * @param  string  $path
 * @return $this
 */',
        'startLine' => 460,
        'endLine' => 465,
        'startColumn' => 5,
        'endColumn' => 5,
        'couldThrow' => false,
        'isClosure' => false,
        'isGenerator' => false,
        'isVariadic' => false,
        'modifiers' => 1,
        'namespace' => 'Illuminate\\Pagination',
        'declaringClassName' => 'Illuminate\\Pagination\\AbstractPaginator',
        'implementingClassName' => 'Illuminate\\Pagination\\AbstractPaginator',
        'currentClassName' => 'Illuminate\\Pagination\\AbstractPaginator',
        'aliasName' => NULL,
      ),
      'onEachSide' => 
      array (
        'name' => 'onEachSide',
        'parameters' => 
        array (
          'count' => 
          array (
            'name' => 'count',
            'default' => NULL,
            'type' => NULL,
            'isVariadic' => false,
            'byRef' => false,
            'isPromoted' => false,
            'attributes' => 
            array (
            ),
            'startLine' => 473,
            'endLine' => 473,
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
 * Set the number of links to display on each side of current page link.
 *
 * @param  int  $count
 * @return $this
 */',
        'startLine' => 473,
        'endLine' => 478,
        'startColumn' => 5,
        'endColumn' => 5,
        'couldThrow' => false,
        'isClosure' => false,
        'isGenerator' => false,
        'isVariadic' => false,
        'modifiers' => 1,
        'namespace' => 'Illuminate\\Pagination',
        'declaringClassName' => 'Illuminate\\Pagination\\AbstractPaginator',
        'implementingClassName' => 'Illuminate\\Pagination\\AbstractPaginator',
        'currentClassName' => 'Illuminate\\Pagination\\AbstractPaginator',
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
        'startLine' => 485,
        'endLine' => 488,
        'startColumn' => 5,
        'endColumn' => 5,
        'couldThrow' => false,
        'isClosure' => false,
        'isGenerator' => false,
        'isVariadic' => false,
        'modifiers' => 1,
        'namespace' => 'Illuminate\\Pagination',
        'declaringClassName' => 'Illuminate\\Pagination\\AbstractPaginator',
        'implementingClassName' => 'Illuminate\\Pagination\\AbstractPaginator',
        'currentClassName' => 'Illuminate\\Pagination\\AbstractPaginator',
        'aliasName' => NULL,
      ),
      'resolveCurrentPath' => 
      array (
        'name' => 'resolveCurrentPath',
        'parameters' => 
        array (
          'default' => 
          array (
            'name' => 'default',
            'default' => 
            array (
              'code' => '\'/\'',
              'attributes' => 
              array (
                'startLine' => 496,
                'endLine' => 496,
                'startTokenPos' => 1377,
                'startFilePos' => 10590,
                'endTokenPos' => 1377,
                'endFilePos' => 10592,
              ),
            ),
            'type' => NULL,
            'isVariadic' => false,
            'byRef' => false,
            'isPromoted' => false,
            'attributes' => 
            array (
            ),
            'startLine' => 496,
            'endLine' => 496,
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
 * Resolve the current request path or return the default value.
 *
 * @param  string  $default
 * @return string
 */',
        'startLine' => 496,
        'endLine' => 503,
        'startColumn' => 5,
        'endColumn' => 5,
        'couldThrow' => false,
        'isClosure' => false,
        'isGenerator' => false,
        'isVariadic' => false,
        'modifiers' => 17,
        'namespace' => 'Illuminate\\Pagination',
        'declaringClassName' => 'Illuminate\\Pagination\\AbstractPaginator',
        'implementingClassName' => 'Illuminate\\Pagination\\AbstractPaginator',
        'currentClassName' => 'Illuminate\\Pagination\\AbstractPaginator',
        'aliasName' => NULL,
      ),
      'currentPathResolver' => 
      array (
        'name' => 'currentPathResolver',
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
            'startLine' => 511,
            'endLine' => 511,
            'startColumn' => 48,
            'endColumn' => 64,
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
 * Set the current request path resolver callback.
 *
 * @param  \\Closure  $resolver
 * @return void
 */',
        'startLine' => 511,
        'endLine' => 514,
        'startColumn' => 5,
        'endColumn' => 5,
        'couldThrow' => false,
        'isClosure' => false,
        'isGenerator' => false,
        'isVariadic' => false,
        'modifiers' => 17,
        'namespace' => 'Illuminate\\Pagination',
        'declaringClassName' => 'Illuminate\\Pagination\\AbstractPaginator',
        'implementingClassName' => 'Illuminate\\Pagination\\AbstractPaginator',
        'currentClassName' => 'Illuminate\\Pagination\\AbstractPaginator',
        'aliasName' => NULL,
      ),
      'resolveCurrentPage' => 
      array (
        'name' => 'resolveCurrentPage',
        'parameters' => 
        array (
          'pageName' => 
          array (
            'name' => 'pageName',
            'default' => 
            array (
              'code' => '\'page\'',
              'attributes' => 
              array (
                'startLine' => 523,
                'endLine' => 523,
                'startTokenPos' => 1456,
                'startFilePos' => 11245,
                'endTokenPos' => 1456,
                'endFilePos' => 11250,
              ),
            ),
            'type' => NULL,
            'isVariadic' => false,
            'byRef' => false,
            'isPromoted' => false,
            'attributes' => 
            array (
            ),
            'startLine' => 523,
            'endLine' => 523,
            'startColumn' => 47,
            'endColumn' => 64,
            'parameterIndex' => 0,
            'isOptional' => true,
          ),
          'default' => 
          array (
            'name' => 'default',
            'default' => 
            array (
              'code' => '1',
              'attributes' => 
              array (
                'startLine' => 523,
                'endLine' => 523,
                'startTokenPos' => 1463,
                'startFilePos' => 11264,
                'endTokenPos' => 1463,
                'endFilePos' => 11264,
              ),
            ),
            'type' => NULL,
            'isVariadic' => false,
            'byRef' => false,
            'isPromoted' => false,
            'attributes' => 
            array (
            ),
            'startLine' => 523,
            'endLine' => 523,
            'startColumn' => 67,
            'endColumn' => 78,
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
 * Resolve the current page or return the default value.
 *
 * @param  string  $pageName
 * @param  int  $default
 * @return int
 */',
        'startLine' => 523,
        'endLine' => 530,
        'startColumn' => 5,
        'endColumn' => 5,
        'couldThrow' => false,
        'isClosure' => false,
        'isGenerator' => false,
        'isVariadic' => false,
        'modifiers' => 17,
        'namespace' => 'Illuminate\\Pagination',
        'declaringClassName' => 'Illuminate\\Pagination\\AbstractPaginator',
        'implementingClassName' => 'Illuminate\\Pagination\\AbstractPaginator',
        'currentClassName' => 'Illuminate\\Pagination\\AbstractPaginator',
        'aliasName' => NULL,
      ),
      'currentPageResolver' => 
      array (
        'name' => 'currentPageResolver',
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
            'startLine' => 538,
            'endLine' => 538,
            'startColumn' => 48,
            'endColumn' => 64,
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
 * Set the current page resolver callback.
 *
 * @param  \\Closure  $resolver
 * @return void
 */',
        'startLine' => 538,
        'endLine' => 541,
        'startColumn' => 5,
        'endColumn' => 5,
        'couldThrow' => false,
        'isClosure' => false,
        'isGenerator' => false,
        'isVariadic' => false,
        'modifiers' => 17,
        'namespace' => 'Illuminate\\Pagination',
        'declaringClassName' => 'Illuminate\\Pagination\\AbstractPaginator',
        'implementingClassName' => 'Illuminate\\Pagination\\AbstractPaginator',
        'currentClassName' => 'Illuminate\\Pagination\\AbstractPaginator',
        'aliasName' => NULL,
      ),
      'resolveQueryString' => 
      array (
        'name' => 'resolveQueryString',
        'parameters' => 
        array (
          'default' => 
          array (
            'name' => 'default',
            'default' => 
            array (
              'code' => 'null',
              'attributes' => 
              array (
                'startLine' => 549,
                'endLine' => 549,
                'startTokenPos' => 1547,
                'startFilePos' => 11909,
                'endTokenPos' => 1547,
                'endFilePos' => 11912,
              ),
            ),
            'type' => NULL,
            'isVariadic' => false,
            'byRef' => false,
            'isPromoted' => false,
            'attributes' => 
            array (
            ),
            'startLine' => 549,
            'endLine' => 549,
            'startColumn' => 47,
            'endColumn' => 61,
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
 * Resolve the query string or return the default value.
 *
 * @param  string|array|null  $default
 * @return string
 */',
        'startLine' => 549,
        'endLine' => 556,
        'startColumn' => 5,
        'endColumn' => 5,
        'couldThrow' => false,
        'isClosure' => false,
        'isGenerator' => false,
        'isVariadic' => false,
        'modifiers' => 17,
        'namespace' => 'Illuminate\\Pagination',
        'declaringClassName' => 'Illuminate\\Pagination\\AbstractPaginator',
        'implementingClassName' => 'Illuminate\\Pagination\\AbstractPaginator',
        'currentClassName' => 'Illuminate\\Pagination\\AbstractPaginator',
        'aliasName' => NULL,
      ),
      'queryStringResolver' => 
      array (
        'name' => 'queryStringResolver',
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
            'startLine' => 564,
            'endLine' => 564,
            'startColumn' => 48,
            'endColumn' => 64,
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
 * Set with query string resolver callback.
 *
 * @param  \\Closure  $resolver
 * @return void
 */',
        'startLine' => 564,
        'endLine' => 567,
        'startColumn' => 5,
        'endColumn' => 5,
        'couldThrow' => false,
        'isClosure' => false,
        'isGenerator' => false,
        'isVariadic' => false,
        'modifiers' => 17,
        'namespace' => 'Illuminate\\Pagination',
        'declaringClassName' => 'Illuminate\\Pagination\\AbstractPaginator',
        'implementingClassName' => 'Illuminate\\Pagination\\AbstractPaginator',
        'currentClassName' => 'Illuminate\\Pagination\\AbstractPaginator',
        'aliasName' => NULL,
      ),
      'viewFactory' => 
      array (
        'name' => 'viewFactory',
        'parameters' => 
        array (
        ),
        'returnsReference' => false,
        'returnType' => NULL,
        'attributes' => 
        array (
        ),
        'docComment' => '/**
 * Get an instance of the view factory from the resolver.
 *
 * @return \\Illuminate\\Contracts\\View\\Factory
 */',
        'startLine' => 574,
        'endLine' => 577,
        'startColumn' => 5,
        'endColumn' => 5,
        'couldThrow' => false,
        'isClosure' => false,
        'isGenerator' => false,
        'isVariadic' => false,
        'modifiers' => 17,
        'namespace' => 'Illuminate\\Pagination',
        'declaringClassName' => 'Illuminate\\Pagination\\AbstractPaginator',
        'implementingClassName' => 'Illuminate\\Pagination\\AbstractPaginator',
        'currentClassName' => 'Illuminate\\Pagination\\AbstractPaginator',
        'aliasName' => NULL,
      ),
      'viewFactoryResolver' => 
      array (
        'name' => 'viewFactoryResolver',
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
            'startLine' => 585,
            'endLine' => 585,
            'startColumn' => 48,
            'endColumn' => 64,
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
 * Set the view factory resolver callback.
 *
 * @param  \\Closure  $resolver
 * @return void
 */',
        'startLine' => 585,
        'endLine' => 588,
        'startColumn' => 5,
        'endColumn' => 5,
        'couldThrow' => false,
        'isClosure' => false,
        'isGenerator' => false,
        'isVariadic' => false,
        'modifiers' => 17,
        'namespace' => 'Illuminate\\Pagination',
        'declaringClassName' => 'Illuminate\\Pagination\\AbstractPaginator',
        'implementingClassName' => 'Illuminate\\Pagination\\AbstractPaginator',
        'currentClassName' => 'Illuminate\\Pagination\\AbstractPaginator',
        'aliasName' => NULL,
      ),
      'defaultView' => 
      array (
        'name' => 'defaultView',
        'parameters' => 
        array (
          'view' => 
          array (
            'name' => 'view',
            'default' => NULL,
            'type' => NULL,
            'isVariadic' => false,
            'byRef' => false,
            'isPromoted' => false,
            'attributes' => 
            array (
            ),
            'startLine' => 596,
            'endLine' => 596,
            'startColumn' => 40,
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
 * Set the default pagination view.
 *
 * @param  string  $view
 * @return void
 */',
        'startLine' => 596,
        'endLine' => 599,
        'startColumn' => 5,
        'endColumn' => 5,
        'couldThrow' => false,
        'isClosure' => false,
        'isGenerator' => false,
        'isVariadic' => false,
        'modifiers' => 17,
        'namespace' => 'Illuminate\\Pagination',
        'declaringClassName' => 'Illuminate\\Pagination\\AbstractPaginator',
        'implementingClassName' => 'Illuminate\\Pagination\\AbstractPaginator',
        'currentClassName' => 'Illuminate\\Pagination\\AbstractPaginator',
        'aliasName' => NULL,
      ),
      'defaultSimpleView' => 
      array (
        'name' => 'defaultSimpleView',
        'parameters' => 
        array (
          'view' => 
          array (
            'name' => 'view',
            'default' => NULL,
            'type' => NULL,
            'isVariadic' => false,
            'byRef' => false,
            'isPromoted' => false,
            'attributes' => 
            array (
            ),
            'startLine' => 607,
            'endLine' => 607,
            'startColumn' => 46,
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
 * Set the default "simple" pagination view.
 *
 * @param  string  $view
 * @return void
 */',
        'startLine' => 607,
        'endLine' => 610,
        'startColumn' => 5,
        'endColumn' => 5,
        'couldThrow' => false,
        'isClosure' => false,
        'isGenerator' => false,
        'isVariadic' => false,
        'modifiers' => 17,
        'namespace' => 'Illuminate\\Pagination',
        'declaringClassName' => 'Illuminate\\Pagination\\AbstractPaginator',
        'implementingClassName' => 'Illuminate\\Pagination\\AbstractPaginator',
        'currentClassName' => 'Illuminate\\Pagination\\AbstractPaginator',
        'aliasName' => NULL,
      ),
      'useTailwind' => 
      array (
        'name' => 'useTailwind',
        'parameters' => 
        array (
        ),
        'returnsReference' => false,
        'returnType' => NULL,
        'attributes' => 
        array (
        ),
        'docComment' => '/**
 * Indicate that Tailwind styling should be used for generated links.
 *
 * @return void
 */',
        'startLine' => 617,
        'endLine' => 621,
        'startColumn' => 5,
        'endColumn' => 5,
        'couldThrow' => false,
        'isClosure' => false,
        'isGenerator' => false,
        'isVariadic' => false,
        'modifiers' => 17,
        'namespace' => 'Illuminate\\Pagination',
        'declaringClassName' => 'Illuminate\\Pagination\\AbstractPaginator',
        'implementingClassName' => 'Illuminate\\Pagination\\AbstractPaginator',
        'currentClassName' => 'Illuminate\\Pagination\\AbstractPaginator',
        'aliasName' => NULL,
      ),
      'useBootstrap' => 
      array (
        'name' => 'useBootstrap',
        'parameters' => 
        array (
        ),
        'returnsReference' => false,
        'returnType' => NULL,
        'attributes' => 
        array (
        ),
        'docComment' => '/**
 * Indicate that Bootstrap 4 styling should be used for generated links.
 *
 * @return void
 */',
        'startLine' => 628,
        'endLine' => 631,
        'startColumn' => 5,
        'endColumn' => 5,
        'couldThrow' => false,
        'isClosure' => false,
        'isGenerator' => false,
        'isVariadic' => false,
        'modifiers' => 17,
        'namespace' => 'Illuminate\\Pagination',
        'declaringClassName' => 'Illuminate\\Pagination\\AbstractPaginator',
        'implementingClassName' => 'Illuminate\\Pagination\\AbstractPaginator',
        'currentClassName' => 'Illuminate\\Pagination\\AbstractPaginator',
        'aliasName' => NULL,
      ),
      'useBootstrapThree' => 
      array (
        'name' => 'useBootstrapThree',
        'parameters' => 
        array (
        ),
        'returnsReference' => false,
        'returnType' => NULL,
        'attributes' => 
        array (
        ),
        'docComment' => '/**
 * Indicate that Bootstrap 3 styling should be used for generated links.
 *
 * @return void
 */',
        'startLine' => 638,
        'endLine' => 642,
        'startColumn' => 5,
        'endColumn' => 5,
        'couldThrow' => false,
        'isClosure' => false,
        'isGenerator' => false,
        'isVariadic' => false,
        'modifiers' => 17,
        'namespace' => 'Illuminate\\Pagination',
        'declaringClassName' => 'Illuminate\\Pagination\\AbstractPaginator',
        'implementingClassName' => 'Illuminate\\Pagination\\AbstractPaginator',
        'currentClassName' => 'Illuminate\\Pagination\\AbstractPaginator',
        'aliasName' => NULL,
      ),
      'useBootstrapFour' => 
      array (
        'name' => 'useBootstrapFour',
        'parameters' => 
        array (
        ),
        'returnsReference' => false,
        'returnType' => NULL,
        'attributes' => 
        array (
        ),
        'docComment' => '/**
 * Indicate that Bootstrap 4 styling should be used for generated links.
 *
 * @return void
 */',
        'startLine' => 649,
        'endLine' => 653,
        'startColumn' => 5,
        'endColumn' => 5,
        'couldThrow' => false,
        'isClosure' => false,
        'isGenerator' => false,
        'isVariadic' => false,
        'modifiers' => 17,
        'namespace' => 'Illuminate\\Pagination',
        'declaringClassName' => 'Illuminate\\Pagination\\AbstractPaginator',
        'implementingClassName' => 'Illuminate\\Pagination\\AbstractPaginator',
        'currentClassName' => 'Illuminate\\Pagination\\AbstractPaginator',
        'aliasName' => NULL,
      ),
      'useBootstrapFive' => 
      array (
        'name' => 'useBootstrapFive',
        'parameters' => 
        array (
        ),
        'returnsReference' => false,
        'returnType' => NULL,
        'attributes' => 
        array (
        ),
        'docComment' => '/**
 * Indicate that Bootstrap 5 styling should be used for generated links.
 *
 * @return void
 */',
        'startLine' => 660,
        'endLine' => 664,
        'startColumn' => 5,
        'endColumn' => 5,
        'couldThrow' => false,
        'isClosure' => false,
        'isGenerator' => false,
        'isVariadic' => false,
        'modifiers' => 17,
        'namespace' => 'Illuminate\\Pagination',
        'declaringClassName' => 'Illuminate\\Pagination\\AbstractPaginator',
        'implementingClassName' => 'Illuminate\\Pagination\\AbstractPaginator',
        'currentClassName' => 'Illuminate\\Pagination\\AbstractPaginator',
        'aliasName' => NULL,
      ),
      'getIterator' => 
      array (
        'name' => 'getIterator',
        'parameters' => 
        array (
        ),
        'returnsReference' => false,
        'returnType' => 
        array (
          'class' => 'PHPStan\\BetterReflection\\Reflection\\ReflectionNamedType',
          'data' => 
          array (
            'name' => 'Traversable',
            'isIdentifier' => false,
          ),
        ),
        'attributes' => 
        array (
        ),
        'docComment' => '/**
 * Get an iterator for the items.
 *
 * @return \\ArrayIterator<TKey, TValue>
 */',
        'startLine' => 671,
        'endLine' => 674,
        'startColumn' => 5,
        'endColumn' => 5,
        'couldThrow' => false,
        'isClosure' => false,
        'isGenerator' => false,
        'isVariadic' => false,
        'modifiers' => 1,
        'namespace' => 'Illuminate\\Pagination',
        'declaringClassName' => 'Illuminate\\Pagination\\AbstractPaginator',
        'implementingClassName' => 'Illuminate\\Pagination\\AbstractPaginator',
        'currentClassName' => 'Illuminate\\Pagination\\AbstractPaginator',
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
 * Determine if the list of items is empty.
 *
 * @return bool
 */',
        'startLine' => 681,
        'endLine' => 684,
        'startColumn' => 5,
        'endColumn' => 5,
        'couldThrow' => false,
        'isClosure' => false,
        'isGenerator' => false,
        'isVariadic' => false,
        'modifiers' => 1,
        'namespace' => 'Illuminate\\Pagination',
        'declaringClassName' => 'Illuminate\\Pagination\\AbstractPaginator',
        'implementingClassName' => 'Illuminate\\Pagination\\AbstractPaginator',
        'currentClassName' => 'Illuminate\\Pagination\\AbstractPaginator',
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
        'startLine' => 691,
        'endLine' => 694,
        'startColumn' => 5,
        'endColumn' => 5,
        'couldThrow' => false,
        'isClosure' => false,
        'isGenerator' => false,
        'isVariadic' => false,
        'modifiers' => 1,
        'namespace' => 'Illuminate\\Pagination',
        'declaringClassName' => 'Illuminate\\Pagination\\AbstractPaginator',
        'implementingClassName' => 'Illuminate\\Pagination\\AbstractPaginator',
        'currentClassName' => 'Illuminate\\Pagination\\AbstractPaginator',
        'aliasName' => NULL,
      ),
      'count' => 
      array (
        'name' => 'count',
        'parameters' => 
        array (
        ),
        'returnsReference' => false,
        'returnType' => 
        array (
          'class' => 'PHPStan\\BetterReflection\\Reflection\\ReflectionNamedType',
          'data' => 
          array (
            'name' => 'int',
            'isIdentifier' => true,
          ),
        ),
        'attributes' => 
        array (
        ),
        'docComment' => '/**
 * Get the number of items for the current page.
 *
 * @return int
 */',
        'startLine' => 701,
        'endLine' => 704,
        'startColumn' => 5,
        'endColumn' => 5,
        'couldThrow' => false,
        'isClosure' => false,
        'isGenerator' => false,
        'isVariadic' => false,
        'modifiers' => 1,
        'namespace' => 'Illuminate\\Pagination',
        'declaringClassName' => 'Illuminate\\Pagination\\AbstractPaginator',
        'implementingClassName' => 'Illuminate\\Pagination\\AbstractPaginator',
        'currentClassName' => 'Illuminate\\Pagination\\AbstractPaginator',
        'aliasName' => NULL,
      ),
      'getCollection' => 
      array (
        'name' => 'getCollection',
        'parameters' => 
        array (
        ),
        'returnsReference' => false,
        'returnType' => NULL,
        'attributes' => 
        array (
        ),
        'docComment' => '/**
 * Get the paginator\'s underlying collection.
 *
 * @return \\Illuminate\\Support\\Collection<TKey, TValue>
 */',
        'startLine' => 711,
        'endLine' => 714,
        'startColumn' => 5,
        'endColumn' => 5,
        'couldThrow' => false,
        'isClosure' => false,
        'isGenerator' => false,
        'isVariadic' => false,
        'modifiers' => 1,
        'namespace' => 'Illuminate\\Pagination',
        'declaringClassName' => 'Illuminate\\Pagination\\AbstractPaginator',
        'implementingClassName' => 'Illuminate\\Pagination\\AbstractPaginator',
        'currentClassName' => 'Illuminate\\Pagination\\AbstractPaginator',
        'aliasName' => NULL,
      ),
      'setCollection' => 
      array (
        'name' => 'setCollection',
        'parameters' => 
        array (
          'collection' => 
          array (
            'name' => 'collection',
            'default' => NULL,
            'type' => 
            array (
              'class' => 'PHPStan\\BetterReflection\\Reflection\\ReflectionNamedType',
              'data' => 
              array (
                'name' => 'Illuminate\\Support\\Collection',
                'isIdentifier' => false,
              ),
            ),
            'isVariadic' => false,
            'byRef' => false,
            'isPromoted' => false,
            'attributes' => 
            array (
            ),
            'startLine' => 722,
            'endLine' => 722,
            'startColumn' => 35,
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
 * Set the paginator\'s underlying collection.
 *
 * @param  \\Illuminate\\Support\\Collection<TKey, TValue>  $collection
 * @return $this
 */',
        'startLine' => 722,
        'endLine' => 727,
        'startColumn' => 5,
        'endColumn' => 5,
        'couldThrow' => false,
        'isClosure' => false,
        'isGenerator' => false,
        'isVariadic' => false,
        'modifiers' => 1,
        'namespace' => 'Illuminate\\Pagination',
        'declaringClassName' => 'Illuminate\\Pagination\\AbstractPaginator',
        'implementingClassName' => 'Illuminate\\Pagination\\AbstractPaginator',
        'currentClassName' => 'Illuminate\\Pagination\\AbstractPaginator',
        'aliasName' => NULL,
      ),
      'getOptions' => 
      array (
        'name' => 'getOptions',
        'parameters' => 
        array (
        ),
        'returnsReference' => false,
        'returnType' => NULL,
        'attributes' => 
        array (
        ),
        'docComment' => '/**
 * Get the paginator options.
 *
 * @return array
 */',
        'startLine' => 734,
        'endLine' => 737,
        'startColumn' => 5,
        'endColumn' => 5,
        'couldThrow' => false,
        'isClosure' => false,
        'isGenerator' => false,
        'isVariadic' => false,
        'modifiers' => 1,
        'namespace' => 'Illuminate\\Pagination',
        'declaringClassName' => 'Illuminate\\Pagination\\AbstractPaginator',
        'implementingClassName' => 'Illuminate\\Pagination\\AbstractPaginator',
        'currentClassName' => 'Illuminate\\Pagination\\AbstractPaginator',
        'aliasName' => NULL,
      ),
      'offsetExists' => 
      array (
        'name' => 'offsetExists',
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
            'startLine' => 745,
            'endLine' => 745,
            'startColumn' => 34,
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
            'name' => 'bool',
            'isIdentifier' => true,
          ),
        ),
        'attributes' => 
        array (
        ),
        'docComment' => '/**
 * Determine if the given item exists.
 *
 * @param  TKey  $key
 * @return bool
 */',
        'startLine' => 745,
        'endLine' => 748,
        'startColumn' => 5,
        'endColumn' => 5,
        'couldThrow' => false,
        'isClosure' => false,
        'isGenerator' => false,
        'isVariadic' => false,
        'modifiers' => 1,
        'namespace' => 'Illuminate\\Pagination',
        'declaringClassName' => 'Illuminate\\Pagination\\AbstractPaginator',
        'implementingClassName' => 'Illuminate\\Pagination\\AbstractPaginator',
        'currentClassName' => 'Illuminate\\Pagination\\AbstractPaginator',
        'aliasName' => NULL,
      ),
      'offsetGet' => 
      array (
        'name' => 'offsetGet',
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
            'startLine' => 756,
            'endLine' => 756,
            'startColumn' => 31,
            'endColumn' => 34,
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
 * Get the item at the given offset.
 *
 * @param  TKey  $key
 * @return TValue|null
 */',
        'startLine' => 756,
        'endLine' => 759,
        'startColumn' => 5,
        'endColumn' => 5,
        'couldThrow' => false,
        'isClosure' => false,
        'isGenerator' => false,
        'isVariadic' => false,
        'modifiers' => 1,
        'namespace' => 'Illuminate\\Pagination',
        'declaringClassName' => 'Illuminate\\Pagination\\AbstractPaginator',
        'implementingClassName' => 'Illuminate\\Pagination\\AbstractPaginator',
        'currentClassName' => 'Illuminate\\Pagination\\AbstractPaginator',
        'aliasName' => NULL,
      ),
      'offsetSet' => 
      array (
        'name' => 'offsetSet',
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
            'startLine' => 768,
            'endLine' => 768,
            'startColumn' => 31,
            'endColumn' => 34,
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
            'startLine' => 768,
            'endLine' => 768,
            'startColumn' => 37,
            'endColumn' => 42,
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
 * Set the item at the given offset.
 *
 * @param  TKey|null  $key
 * @param  TValue  $value
 * @return void
 */',
        'startLine' => 768,
        'endLine' => 771,
        'startColumn' => 5,
        'endColumn' => 5,
        'couldThrow' => false,
        'isClosure' => false,
        'isGenerator' => false,
        'isVariadic' => false,
        'modifiers' => 1,
        'namespace' => 'Illuminate\\Pagination',
        'declaringClassName' => 'Illuminate\\Pagination\\AbstractPaginator',
        'implementingClassName' => 'Illuminate\\Pagination\\AbstractPaginator',
        'currentClassName' => 'Illuminate\\Pagination\\AbstractPaginator',
        'aliasName' => NULL,
      ),
      'offsetUnset' => 
      array (
        'name' => 'offsetUnset',
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
            'startLine' => 779,
            'endLine' => 779,
            'startColumn' => 33,
            'endColumn' => 36,
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
 * Unset the item at the given key.
 *
 * @param  TKey  $key
 * @return void
 */',
        'startLine' => 779,
        'endLine' => 782,
        'startColumn' => 5,
        'endColumn' => 5,
        'couldThrow' => false,
        'isClosure' => false,
        'isGenerator' => false,
        'isVariadic' => false,
        'modifiers' => 1,
        'namespace' => 'Illuminate\\Pagination',
        'declaringClassName' => 'Illuminate\\Pagination\\AbstractPaginator',
        'implementingClassName' => 'Illuminate\\Pagination\\AbstractPaginator',
        'currentClassName' => 'Illuminate\\Pagination\\AbstractPaginator',
        'aliasName' => NULL,
      ),
      'toHtml' => 
      array (
        'name' => 'toHtml',
        'parameters' => 
        array (
        ),
        'returnsReference' => false,
        'returnType' => NULL,
        'attributes' => 
        array (
        ),
        'docComment' => '/**
 * Render the contents of the paginator to HTML.
 *
 * @return string
 */',
        'startLine' => 789,
        'endLine' => 792,
        'startColumn' => 5,
        'endColumn' => 5,
        'couldThrow' => false,
        'isClosure' => false,
        'isGenerator' => false,
        'isVariadic' => false,
        'modifiers' => 1,
        'namespace' => 'Illuminate\\Pagination',
        'declaringClassName' => 'Illuminate\\Pagination\\AbstractPaginator',
        'implementingClassName' => 'Illuminate\\Pagination\\AbstractPaginator',
        'currentClassName' => 'Illuminate\\Pagination\\AbstractPaginator',
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
            'startLine' => 801,
            'endLine' => 801,
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
            'startLine' => 801,
            'endLine' => 801,
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
 * Make dynamic calls into the collection.
 *
 * @param  string  $method
 * @param  array  $parameters
 * @return mixed
 */',
        'startLine' => 801,
        'endLine' => 804,
        'startColumn' => 5,
        'endColumn' => 5,
        'couldThrow' => false,
        'isClosure' => false,
        'isGenerator' => false,
        'isVariadic' => false,
        'modifiers' => 1,
        'namespace' => 'Illuminate\\Pagination',
        'declaringClassName' => 'Illuminate\\Pagination\\AbstractPaginator',
        'implementingClassName' => 'Illuminate\\Pagination\\AbstractPaginator',
        'currentClassName' => 'Illuminate\\Pagination\\AbstractPaginator',
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
 * Render the contents of the paginator when casting to a string.
 *
 * @return string
 */',
        'startLine' => 811,
        'endLine' => 816,
        'startColumn' => 5,
        'endColumn' => 5,
        'couldThrow' => false,
        'isClosure' => false,
        'isGenerator' => false,
        'isVariadic' => false,
        'modifiers' => 1,
        'namespace' => 'Illuminate\\Pagination',
        'declaringClassName' => 'Illuminate\\Pagination\\AbstractPaginator',
        'implementingClassName' => 'Illuminate\\Pagination\\AbstractPaginator',
        'currentClassName' => 'Illuminate\\Pagination\\AbstractPaginator',
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
                'startLine' => 824,
                'endLine' => 824,
                'startTokenPos' => 2290,
                'startFilePos' => 17970,
                'endTokenPos' => 2290,
                'endFilePos' => 17973,
              ),
            ),
            'type' => NULL,
            'isVariadic' => false,
            'byRef' => false,
            'isPromoted' => false,
            'attributes' => 
            array (
            ),
            'startLine' => 824,
            'endLine' => 824,
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
 * Indicate that the paginator\'s string representation should be escaped when __toString is invoked.
 *
 * @param  bool  $escape
 * @return $this
 */',
        'startLine' => 824,
        'endLine' => 829,
        'startColumn' => 5,
        'endColumn' => 5,
        'couldThrow' => false,
        'isClosure' => false,
        'isGenerator' => false,
        'isVariadic' => false,
        'modifiers' => 1,
        'namespace' => 'Illuminate\\Pagination',
        'declaringClassName' => 'Illuminate\\Pagination\\AbstractPaginator',
        'implementingClassName' => 'Illuminate\\Pagination\\AbstractPaginator',
        'currentClassName' => 'Illuminate\\Pagination\\AbstractPaginator',
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