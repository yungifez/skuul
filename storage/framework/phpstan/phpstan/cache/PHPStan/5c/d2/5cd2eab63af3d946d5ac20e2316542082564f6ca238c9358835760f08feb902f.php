<?php declare(strict_types = 1);

// osfsl-/var/www/html/vendor/composer/../laravel/jetstream/src/HasProfilePhoto.php-PHPStan\BetterReflection\Reflection\ReflectionClass-Laravel\Jetstream\HasProfilePhoto
return \PHPStan\Cache\CacheItem::__set_state(array(
   'variableKey' => 'v2-0f5fe545f32ea45989365ff1038e705b9955b961bad573710ce3d595ef3def35-8.5.9-6.70.0.3',
   'data' => 
  array (
    'locatedSource' => 
    array (
      'class' => 'PHPStan\\BetterReflection\\SourceLocator\\Located\\LocatedSource',
      'data' => 
      array (
        'name' => 'Laravel\\Jetstream\\HasProfilePhoto',
        'filename' => '/var/www/html/vendor/composer/../laravel/jetstream/src/HasProfilePhoto.php',
      ),
    ),
    'namespace' => 'Laravel\\Jetstream',
    'name' => 'Laravel\\Jetstream\\HasProfilePhoto',
    'shortName' => 'HasProfilePhoto',
    'isInterface' => false,
    'isTrait' => true,
    'isEnum' => false,
    'isBackedEnum' => false,
    'modifiers' => 0,
    'docComment' => NULL,
    'attributes' => 
    array (
    ),
    'startLine' => 9,
    'endLine' => 92,
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
      'updateProfilePhoto' => 
      array (
        'name' => 'updateProfilePhoto',
        'parameters' => 
        array (
          'photo' => 
          array (
            'name' => 'photo',
            'default' => NULL,
            'type' => 
            array (
              'class' => 'PHPStan\\BetterReflection\\Reflection\\ReflectionNamedType',
              'data' => 
              array (
                'name' => 'Illuminate\\Http\\UploadedFile',
                'isIdentifier' => false,
              ),
            ),
            'isVariadic' => false,
            'byRef' => false,
            'isPromoted' => false,
            'attributes' => 
            array (
            ),
            'startLine' => 18,
            'endLine' => 18,
            'startColumn' => 40,
            'endColumn' => 58,
            'parameterIndex' => 0,
            'isOptional' => false,
          ),
          'storagePath' => 
          array (
            'name' => 'storagePath',
            'default' => 
            array (
              'code' => '\'profile-photos\'',
              'attributes' => 
              array (
                'startLine' => 18,
                'endLine' => 18,
                'startTokenPos' => 45,
                'startFilePos' => 433,
                'endTokenPos' => 45,
                'endFilePos' => 448,
              ),
            ),
            'type' => NULL,
            'isVariadic' => false,
            'byRef' => false,
            'isPromoted' => false,
            'attributes' => 
            array (
            ),
            'startLine' => 18,
            'endLine' => 18,
            'startColumn' => 61,
            'endColumn' => 91,
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
 * Update the user\'s profile photo.
 *
 * @param  \\Illuminate\\Http\\UploadedFile  $photo
 * @param  string  $storagePath
 * @return void
 */',
        'startLine' => 18,
        'endLine' => 31,
        'startColumn' => 5,
        'endColumn' => 5,
        'couldThrow' => false,
        'isClosure' => false,
        'isGenerator' => false,
        'isVariadic' => false,
        'modifiers' => 1,
        'namespace' => 'Laravel\\Jetstream',
        'declaringClassName' => 'Laravel\\Jetstream\\HasProfilePhoto',
        'implementingClassName' => 'Laravel\\Jetstream\\HasProfilePhoto',
        'currentClassName' => 'Laravel\\Jetstream\\HasProfilePhoto',
        'aliasName' => NULL,
      ),
      'deleteProfilePhoto' => 
      array (
        'name' => 'deleteProfilePhoto',
        'parameters' => 
        array (
        ),
        'returnsReference' => false,
        'returnType' => NULL,
        'attributes' => 
        array (
        ),
        'docComment' => '/**
 * Delete the user\'s profile photo.
 *
 * @return void
 */',
        'startLine' => 38,
        'endLine' => 53,
        'startColumn' => 5,
        'endColumn' => 5,
        'couldThrow' => false,
        'isClosure' => false,
        'isGenerator' => false,
        'isVariadic' => false,
        'modifiers' => 1,
        'namespace' => 'Laravel\\Jetstream',
        'declaringClassName' => 'Laravel\\Jetstream\\HasProfilePhoto',
        'implementingClassName' => 'Laravel\\Jetstream\\HasProfilePhoto',
        'currentClassName' => 'Laravel\\Jetstream\\HasProfilePhoto',
        'aliasName' => NULL,
      ),
      'profilePhotoUrl' => 
      array (
        'name' => 'profilePhotoUrl',
        'parameters' => 
        array (
        ),
        'returnsReference' => false,
        'returnType' => 
        array (
          'class' => 'PHPStan\\BetterReflection\\Reflection\\ReflectionNamedType',
          'data' => 
          array (
            'name' => 'Illuminate\\Database\\Eloquent\\Casts\\Attribute',
            'isIdentifier' => false,
          ),
        ),
        'attributes' => 
        array (
        ),
        'docComment' => '/**
 * Get the URL to the user\'s profile photo.
 *
 * @return \\Illuminate\\Database\\Eloquent\\Casts\\Attribute
 */',
        'startLine' => 60,
        'endLine' => 67,
        'startColumn' => 5,
        'endColumn' => 5,
        'couldThrow' => false,
        'isClosure' => false,
        'isGenerator' => false,
        'isVariadic' => false,
        'modifiers' => 2,
        'namespace' => 'Laravel\\Jetstream',
        'declaringClassName' => 'Laravel\\Jetstream\\HasProfilePhoto',
        'implementingClassName' => 'Laravel\\Jetstream\\HasProfilePhoto',
        'currentClassName' => 'Laravel\\Jetstream\\HasProfilePhoto',
        'aliasName' => NULL,
      ),
      'defaultProfilePhotoUrl' => 
      array (
        'name' => 'defaultProfilePhotoUrl',
        'parameters' => 
        array (
        ),
        'returnsReference' => false,
        'returnType' => NULL,
        'attributes' => 
        array (
        ),
        'docComment' => '/**
 * Get the default profile photo URL if no profile photo has been uploaded.
 *
 * @return string
 */',
        'startLine' => 74,
        'endLine' => 81,
        'startColumn' => 5,
        'endColumn' => 5,
        'couldThrow' => false,
        'isClosure' => false,
        'isGenerator' => false,
        'isVariadic' => false,
        'modifiers' => 2,
        'namespace' => 'Laravel\\Jetstream',
        'declaringClassName' => 'Laravel\\Jetstream\\HasProfilePhoto',
        'implementingClassName' => 'Laravel\\Jetstream\\HasProfilePhoto',
        'currentClassName' => 'Laravel\\Jetstream\\HasProfilePhoto',
        'aliasName' => NULL,
      ),
      'profilePhotoDisk' => 
      array (
        'name' => 'profilePhotoDisk',
        'parameters' => 
        array (
        ),
        'returnsReference' => false,
        'returnType' => NULL,
        'attributes' => 
        array (
        ),
        'docComment' => '/**
 * Get the disk that profile photos should be stored on.
 *
 * @return string
 */',
        'startLine' => 88,
        'endLine' => 91,
        'startColumn' => 5,
        'endColumn' => 5,
        'couldThrow' => false,
        'isClosure' => false,
        'isGenerator' => false,
        'isVariadic' => false,
        'modifiers' => 2,
        'namespace' => 'Laravel\\Jetstream',
        'declaringClassName' => 'Laravel\\Jetstream\\HasProfilePhoto',
        'implementingClassName' => 'Laravel\\Jetstream\\HasProfilePhoto',
        'currentClassName' => 'Laravel\\Jetstream\\HasProfilePhoto',
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