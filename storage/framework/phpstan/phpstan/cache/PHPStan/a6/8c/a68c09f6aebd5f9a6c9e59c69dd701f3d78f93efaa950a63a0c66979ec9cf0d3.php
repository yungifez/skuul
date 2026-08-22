<?php declare(strict_types = 1);

// osfsl-/var/www/html/vendor/composer/../laravel/framework/src/Illuminate/Contracts/Auth/MustVerifyEmail.php-PHPStan\BetterReflection\Reflection\ReflectionClass-Illuminate\Contracts\Auth\MustVerifyEmail
return \PHPStan\Cache\CacheItem::__set_state(array(
   'variableKey' => 'v2-ed65fdc0b44cfd0decd1c719a43583ddf1f0e6e921862a7011efc88c289cba5f-8.5.9-6.70.0.3',
   'data' => 
  array (
    'locatedSource' => 
    array (
      'class' => 'PHPStan\\BetterReflection\\SourceLocator\\Located\\LocatedSource',
      'data' => 
      array (
        'name' => 'Illuminate\\Contracts\\Auth\\MustVerifyEmail',
        'filename' => '/var/www/html/vendor/composer/../laravel/framework/src/Illuminate/Contracts/Auth/MustVerifyEmail.php',
      ),
    ),
    'namespace' => 'Illuminate\\Contracts\\Auth',
    'name' => 'Illuminate\\Contracts\\Auth\\MustVerifyEmail',
    'shortName' => 'MustVerifyEmail',
    'isInterface' => true,
    'isTrait' => false,
    'isEnum' => false,
    'isBackedEnum' => false,
    'modifiers' => 0,
    'docComment' => NULL,
    'attributes' => 
    array (
    ),
    'startLine' => 5,
    'endLine' => 41,
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
      'hasVerifiedEmail' => 
      array (
        'name' => 'hasVerifiedEmail',
        'parameters' => 
        array (
        ),
        'returnsReference' => false,
        'returnType' => NULL,
        'attributes' => 
        array (
        ),
        'docComment' => '/**
 * Determine if the user has verified their email address.
 *
 * @return bool
 */',
        'startLine' => 12,
        'endLine' => 12,
        'startColumn' => 5,
        'endColumn' => 39,
        'couldThrow' => false,
        'isClosure' => false,
        'isGenerator' => false,
        'isVariadic' => false,
        'modifiers' => 1,
        'namespace' => 'Illuminate\\Contracts\\Auth',
        'declaringClassName' => 'Illuminate\\Contracts\\Auth\\MustVerifyEmail',
        'implementingClassName' => 'Illuminate\\Contracts\\Auth\\MustVerifyEmail',
        'currentClassName' => 'Illuminate\\Contracts\\Auth\\MustVerifyEmail',
        'aliasName' => NULL,
      ),
      'markEmailAsVerified' => 
      array (
        'name' => 'markEmailAsVerified',
        'parameters' => 
        array (
        ),
        'returnsReference' => false,
        'returnType' => NULL,
        'attributes' => 
        array (
        ),
        'docComment' => '/**
 * Mark the given user\'s email as verified.
 *
 * @return bool
 */',
        'startLine' => 19,
        'endLine' => 19,
        'startColumn' => 5,
        'endColumn' => 42,
        'couldThrow' => false,
        'isClosure' => false,
        'isGenerator' => false,
        'isVariadic' => false,
        'modifiers' => 1,
        'namespace' => 'Illuminate\\Contracts\\Auth',
        'declaringClassName' => 'Illuminate\\Contracts\\Auth\\MustVerifyEmail',
        'implementingClassName' => 'Illuminate\\Contracts\\Auth\\MustVerifyEmail',
        'currentClassName' => 'Illuminate\\Contracts\\Auth\\MustVerifyEmail',
        'aliasName' => NULL,
      ),
      'markEmailAsUnverified' => 
      array (
        'name' => 'markEmailAsUnverified',
        'parameters' => 
        array (
        ),
        'returnsReference' => false,
        'returnType' => NULL,
        'attributes' => 
        array (
        ),
        'docComment' => '/**
 * Mark the given user\'s email as unverified.
 *
 * @return bool
 */',
        'startLine' => 26,
        'endLine' => 26,
        'startColumn' => 5,
        'endColumn' => 44,
        'couldThrow' => false,
        'isClosure' => false,
        'isGenerator' => false,
        'isVariadic' => false,
        'modifiers' => 1,
        'namespace' => 'Illuminate\\Contracts\\Auth',
        'declaringClassName' => 'Illuminate\\Contracts\\Auth\\MustVerifyEmail',
        'implementingClassName' => 'Illuminate\\Contracts\\Auth\\MustVerifyEmail',
        'currentClassName' => 'Illuminate\\Contracts\\Auth\\MustVerifyEmail',
        'aliasName' => NULL,
      ),
      'sendEmailVerificationNotification' => 
      array (
        'name' => 'sendEmailVerificationNotification',
        'parameters' => 
        array (
        ),
        'returnsReference' => false,
        'returnType' => NULL,
        'attributes' => 
        array (
        ),
        'docComment' => '/**
 * Send the email verification notification.
 *
 * @return void
 */',
        'startLine' => 33,
        'endLine' => 33,
        'startColumn' => 5,
        'endColumn' => 56,
        'couldThrow' => false,
        'isClosure' => false,
        'isGenerator' => false,
        'isVariadic' => false,
        'modifiers' => 1,
        'namespace' => 'Illuminate\\Contracts\\Auth',
        'declaringClassName' => 'Illuminate\\Contracts\\Auth\\MustVerifyEmail',
        'implementingClassName' => 'Illuminate\\Contracts\\Auth\\MustVerifyEmail',
        'currentClassName' => 'Illuminate\\Contracts\\Auth\\MustVerifyEmail',
        'aliasName' => NULL,
      ),
      'getEmailForVerification' => 
      array (
        'name' => 'getEmailForVerification',
        'parameters' => 
        array (
        ),
        'returnsReference' => false,
        'returnType' => NULL,
        'attributes' => 
        array (
        ),
        'docComment' => '/**
 * Get the email address that should be used for verification.
 *
 * @return string
 */',
        'startLine' => 40,
        'endLine' => 40,
        'startColumn' => 5,
        'endColumn' => 46,
        'couldThrow' => false,
        'isClosure' => false,
        'isGenerator' => false,
        'isVariadic' => false,
        'modifiers' => 1,
        'namespace' => 'Illuminate\\Contracts\\Auth',
        'declaringClassName' => 'Illuminate\\Contracts\\Auth\\MustVerifyEmail',
        'implementingClassName' => 'Illuminate\\Contracts\\Auth\\MustVerifyEmail',
        'currentClassName' => 'Illuminate\\Contracts\\Auth\\MustVerifyEmail',
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