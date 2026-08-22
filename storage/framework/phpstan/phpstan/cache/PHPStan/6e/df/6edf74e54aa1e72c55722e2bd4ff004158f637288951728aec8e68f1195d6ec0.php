<?php declare(strict_types = 1);

// ftm-/var/www/html/app/Models/User.php
return \PHPStan\Cache\CacheItem::__set_state(array(
   'variableKey' => 'v5-2.3.3',
   'data' => 
  array (
    0 => 
    array (
      'e8283ac4eb665433f3f809a62f47ea37' => 
      \PHPStan\Analyser\IntermediaryNameScope::__set_state(array(
         'namespace' => 'App\\Models',
         'uses' => 
        array (
          'accountstatus' => 'App\\Enums\\AccountStatus',
          'enrollmentstatus' => 'App\\Enums\\EnrollmentStatus',
          'organizationmembershipstatus' => 'App\\Enums\\OrganizationMembershipStatus',
          'role' => 'App\\Enums\\Role',
          'schoolmembershipstatus' => 'App\\Enums\\SchoolMembershipStatus',
          'carbon' => 'Carbon\\Carbon',
          'mustverifyemail' => 'Illuminate\\Contracts\\Auth\\MustVerifyEmail',
          'builder' => 'Illuminate\\Database\\Eloquent\\Builder',
          'hasfactory' => 'Illuminate\\Database\\Eloquent\\Factories\\HasFactory',
          'belongstomany' => 'Illuminate\\Database\\Eloquent\\Relations\\BelongsToMany',
          'hasmany' => 'Illuminate\\Database\\Eloquent\\Relations\\HasMany',
          'hasone' => 'Illuminate\\Database\\Eloquent\\Relations\\HasOne',
          'softdeletes' => 'Illuminate\\Database\\Eloquent\\SoftDeletes',
          'authenticatable' => 'Illuminate\\Foundation\\Auth\\User',
          'notifiable' => 'Illuminate\\Notifications\\Notifiable',
          'twofactorauthenticatable' => 'Laravel\\Fortify\\TwoFactorAuthenticatable',
          'hasprofilephoto' => 'Laravel\\Jetstream\\HasProfilePhoto',
          'hasapitokens' => 'Laravel\\Sanctum\\HasApiTokens',
          'hasroles' => 'Spatie\\Permission\\Traits\\HasRoles',
        ),
         'className' => 'App\\Models\\User',
         'functionName' => NULL,
         'templatePhpDocNodes' => 
        array (
        ),
         'parent' => NULL,
         'typeAliasesMap' => 
        array (
        ),
         'bypassTypeAliases' => false,
         'constUses' => 
        array (
        ),
         'typeAliasClassName' => NULL,
         'traitData' => NULL,
      )),
      '00cd4587ae5a750d87005b2cb5a4fe76' => 
      \PHPStan\Analyser\IntermediaryNameScope::__set_state(array(
         'namespace' => 'Laravel\\Sanctum',
         'uses' => 
        array (
          'datetimeinterface' => 'DateTimeInterface',
          'str' => 'Illuminate\\Support\\Str',
        ),
         'className' => 'App\\Models\\User',
         'functionName' => NULL,
         'templatePhpDocNodes' => 
        array (
          'TToken' => 
          array (
            0 => '@template',
            1 => 
            \PHPStan\PhpDocParser\Ast\PhpDoc\TemplateTagValueNode::__set_state(array(
               'name' => 'TToken',
               'bound' => 
              \PHPStan\PhpDocParser\Ast\Type\IdentifierTypeNode::__set_state(array(
                 'name' => '\\Laravel\\Sanctum\\Contracts\\HasAbilities',
                 'attributes' => 
                array (
                  'startLine' => 2,
                  'endLine' => 2,
                ),
              )),
               'default' => 
              \PHPStan\PhpDocParser\Ast\Type\IdentifierTypeNode::__set_state(array(
                 'name' => '\\Laravel\\Sanctum\\PersonalAccessToken',
                 'attributes' => 
                array (
                  'startLine' => 2,
                  'endLine' => 2,
                ),
              )),
               'lowerBound' => NULL,
               'description' => '',
               'attributes' => 
              array (
                'startLine' => 2,
                'endLine' => 2,
              ),
            )),
          ),
        ),
         'parent' => NULL,
         'typeAliasesMap' => 
        array (
        ),
         'bypassTypeAliases' => false,
         'constUses' => 
        array (
        ),
         'typeAliasClassName' => 'Laravel\\Sanctum\\HasApiTokens',
         'traitData' => 
        array (
          0 => '/var/www/html/app/Models/User.php',
          1 => 'App\\Models\\User',
          2 => 'Laravel\\Sanctum\\HasApiTokens',
          3 => NULL,
          4 => NULL,
        ),
      )),
      '9d833bebd5156e1adb507d2263b0a9ca' => 
      \PHPStan\Analyser\IntermediaryNameScope::__set_state(array(
         'namespace' => 'Laravel\\Sanctum',
         'uses' => 
        array (
          'datetimeinterface' => 'DateTimeInterface',
          'str' => 'Illuminate\\Support\\Str',
        ),
         'className' => 'App\\Models\\User',
         'functionName' => 'tokens',
         'templatePhpDocNodes' => 
        array (
        ),
         'parent' => 
        \PHPStan\Analyser\IntermediaryNameScope::__set_state(array(
           'namespace' => 'Laravel\\Sanctum',
           'uses' => 
          array (
            'datetimeinterface' => 'DateTimeInterface',
            'str' => 'Illuminate\\Support\\Str',
          ),
           'className' => 'App\\Models\\User',
           'functionName' => NULL,
           'templatePhpDocNodes' => 
          array (
            'TToken' => 
            array (
              0 => '@template',
              1 => 
              \PHPStan\PhpDocParser\Ast\PhpDoc\TemplateTagValueNode::__set_state(array(
                 'name' => 'TToken',
                 'bound' => 
                \PHPStan\PhpDocParser\Ast\Type\IdentifierTypeNode::__set_state(array(
                   'name' => '\\Laravel\\Sanctum\\Contracts\\HasAbilities',
                   'attributes' => 
                  array (
                    'startLine' => 2,
                    'endLine' => 2,
                  ),
                )),
                 'default' => 
                \PHPStan\PhpDocParser\Ast\Type\IdentifierTypeNode::__set_state(array(
                   'name' => '\\Laravel\\Sanctum\\PersonalAccessToken',
                   'attributes' => 
                  array (
                    'startLine' => 2,
                    'endLine' => 2,
                  ),
                )),
                 'lowerBound' => NULL,
                 'description' => '',
                 'attributes' => 
                array (
                  'startLine' => 2,
                  'endLine' => 2,
                ),
              )),
            ),
          ),
           'parent' => NULL,
           'typeAliasesMap' => 
          array (
          ),
           'bypassTypeAliases' => false,
           'constUses' => 
          array (
          ),
           'typeAliasClassName' => 'Laravel\\Sanctum\\HasApiTokens',
           'traitData' => NULL,
        )),
         'typeAliasesMap' => 
        array (
        ),
         'bypassTypeAliases' => false,
         'constUses' => 
        array (
        ),
         'typeAliasClassName' => 'Laravel\\Sanctum\\HasApiTokens',
         'traitData' => 
        array (
          0 => '/var/www/html/app/Models/User.php',
          1 => 'App\\Models\\User',
          2 => 'Laravel\\Sanctum\\HasApiTokens',
          3 => NULL,
          4 => NULL,
        ),
      )),
      'b5c44e81452cd8bec3bea97e66476e20' => 
      \PHPStan\Analyser\IntermediaryNameScope::__set_state(array(
         'namespace' => 'Laravel\\Sanctum',
         'uses' => 
        array (
          'datetimeinterface' => 'DateTimeInterface',
          'str' => 'Illuminate\\Support\\Str',
        ),
         'className' => 'App\\Models\\User',
         'functionName' => 'tokenCan',
         'templatePhpDocNodes' => 
        array (
        ),
         'parent' => 
        \PHPStan\Analyser\IntermediaryNameScope::__set_state(array(
           'namespace' => 'Laravel\\Sanctum',
           'uses' => 
          array (
            'datetimeinterface' => 'DateTimeInterface',
            'str' => 'Illuminate\\Support\\Str',
          ),
           'className' => 'App\\Models\\User',
           'functionName' => NULL,
           'templatePhpDocNodes' => 
          array (
            'TToken' => 
            array (
              0 => '@template',
              1 => 
              \PHPStan\PhpDocParser\Ast\PhpDoc\TemplateTagValueNode::__set_state(array(
                 'name' => 'TToken',
                 'bound' => 
                \PHPStan\PhpDocParser\Ast\Type\IdentifierTypeNode::__set_state(array(
                   'name' => '\\Laravel\\Sanctum\\Contracts\\HasAbilities',
                   'attributes' => 
                  array (
                    'startLine' => 2,
                    'endLine' => 2,
                  ),
                )),
                 'default' => 
                \PHPStan\PhpDocParser\Ast\Type\IdentifierTypeNode::__set_state(array(
                   'name' => '\\Laravel\\Sanctum\\PersonalAccessToken',
                   'attributes' => 
                  array (
                    'startLine' => 2,
                    'endLine' => 2,
                  ),
                )),
                 'lowerBound' => NULL,
                 'description' => '',
                 'attributes' => 
                array (
                  'startLine' => 2,
                  'endLine' => 2,
                ),
              )),
            ),
          ),
           'parent' => NULL,
           'typeAliasesMap' => 
          array (
          ),
           'bypassTypeAliases' => false,
           'constUses' => 
          array (
          ),
           'typeAliasClassName' => 'Laravel\\Sanctum\\HasApiTokens',
           'traitData' => NULL,
        )),
         'typeAliasesMap' => 
        array (
        ),
         'bypassTypeAliases' => false,
         'constUses' => 
        array (
        ),
         'typeAliasClassName' => 'Laravel\\Sanctum\\HasApiTokens',
         'traitData' => 
        array (
          0 => '/var/www/html/app/Models/User.php',
          1 => 'App\\Models\\User',
          2 => 'Laravel\\Sanctum\\HasApiTokens',
          3 => NULL,
          4 => NULL,
        ),
      )),
      '52b28e90ed989cb5386665fdbdaccbed' => 
      \PHPStan\Analyser\IntermediaryNameScope::__set_state(array(
         'namespace' => 'Laravel\\Sanctum',
         'uses' => 
        array (
          'datetimeinterface' => 'DateTimeInterface',
          'str' => 'Illuminate\\Support\\Str',
        ),
         'className' => 'App\\Models\\User',
         'functionName' => 'tokenCant',
         'templatePhpDocNodes' => 
        array (
        ),
         'parent' => 
        \PHPStan\Analyser\IntermediaryNameScope::__set_state(array(
           'namespace' => 'Laravel\\Sanctum',
           'uses' => 
          array (
            'datetimeinterface' => 'DateTimeInterface',
            'str' => 'Illuminate\\Support\\Str',
          ),
           'className' => 'App\\Models\\User',
           'functionName' => NULL,
           'templatePhpDocNodes' => 
          array (
            'TToken' => 
            array (
              0 => '@template',
              1 => 
              \PHPStan\PhpDocParser\Ast\PhpDoc\TemplateTagValueNode::__set_state(array(
                 'name' => 'TToken',
                 'bound' => 
                \PHPStan\PhpDocParser\Ast\Type\IdentifierTypeNode::__set_state(array(
                   'name' => '\\Laravel\\Sanctum\\Contracts\\HasAbilities',
                   'attributes' => 
                  array (
                    'startLine' => 2,
                    'endLine' => 2,
                  ),
                )),
                 'default' => 
                \PHPStan\PhpDocParser\Ast\Type\IdentifierTypeNode::__set_state(array(
                   'name' => '\\Laravel\\Sanctum\\PersonalAccessToken',
                   'attributes' => 
                  array (
                    'startLine' => 2,
                    'endLine' => 2,
                  ),
                )),
                 'lowerBound' => NULL,
                 'description' => '',
                 'attributes' => 
                array (
                  'startLine' => 2,
                  'endLine' => 2,
                ),
              )),
            ),
          ),
           'parent' => NULL,
           'typeAliasesMap' => 
          array (
          ),
           'bypassTypeAliases' => false,
           'constUses' => 
          array (
          ),
           'typeAliasClassName' => 'Laravel\\Sanctum\\HasApiTokens',
           'traitData' => NULL,
        )),
         'typeAliasesMap' => 
        array (
        ),
         'bypassTypeAliases' => false,
         'constUses' => 
        array (
        ),
         'typeAliasClassName' => 'Laravel\\Sanctum\\HasApiTokens',
         'traitData' => 
        array (
          0 => '/var/www/html/app/Models/User.php',
          1 => 'App\\Models\\User',
          2 => 'Laravel\\Sanctum\\HasApiTokens',
          3 => NULL,
          4 => NULL,
        ),
      )),
      'd046f4439609998ea2bc08d7e7e27ade' => 
      \PHPStan\Analyser\IntermediaryNameScope::__set_state(array(
         'namespace' => 'Laravel\\Sanctum',
         'uses' => 
        array (
          'datetimeinterface' => 'DateTimeInterface',
          'str' => 'Illuminate\\Support\\Str',
        ),
         'className' => 'App\\Models\\User',
         'functionName' => 'createToken',
         'templatePhpDocNodes' => 
        array (
        ),
         'parent' => 
        \PHPStan\Analyser\IntermediaryNameScope::__set_state(array(
           'namespace' => 'Laravel\\Sanctum',
           'uses' => 
          array (
            'datetimeinterface' => 'DateTimeInterface',
            'str' => 'Illuminate\\Support\\Str',
          ),
           'className' => 'App\\Models\\User',
           'functionName' => NULL,
           'templatePhpDocNodes' => 
          array (
            'TToken' => 
            array (
              0 => '@template',
              1 => 
              \PHPStan\PhpDocParser\Ast\PhpDoc\TemplateTagValueNode::__set_state(array(
                 'name' => 'TToken',
                 'bound' => 
                \PHPStan\PhpDocParser\Ast\Type\IdentifierTypeNode::__set_state(array(
                   'name' => '\\Laravel\\Sanctum\\Contracts\\HasAbilities',
                   'attributes' => 
                  array (
                    'startLine' => 2,
                    'endLine' => 2,
                  ),
                )),
                 'default' => 
                \PHPStan\PhpDocParser\Ast\Type\IdentifierTypeNode::__set_state(array(
                   'name' => '\\Laravel\\Sanctum\\PersonalAccessToken',
                   'attributes' => 
                  array (
                    'startLine' => 2,
                    'endLine' => 2,
                  ),
                )),
                 'lowerBound' => NULL,
                 'description' => '',
                 'attributes' => 
                array (
                  'startLine' => 2,
                  'endLine' => 2,
                ),
              )),
            ),
          ),
           'parent' => NULL,
           'typeAliasesMap' => 
          array (
          ),
           'bypassTypeAliases' => false,
           'constUses' => 
          array (
          ),
           'typeAliasClassName' => 'Laravel\\Sanctum\\HasApiTokens',
           'traitData' => NULL,
        )),
         'typeAliasesMap' => 
        array (
        ),
         'bypassTypeAliases' => false,
         'constUses' => 
        array (
        ),
         'typeAliasClassName' => 'Laravel\\Sanctum\\HasApiTokens',
         'traitData' => 
        array (
          0 => '/var/www/html/app/Models/User.php',
          1 => 'App\\Models\\User',
          2 => 'Laravel\\Sanctum\\HasApiTokens',
          3 => NULL,
          4 => NULL,
        ),
      )),
      'd03f591279c243e565de2d973d5b29d6' => 
      \PHPStan\Analyser\IntermediaryNameScope::__set_state(array(
         'namespace' => 'Laravel\\Sanctum',
         'uses' => 
        array (
          'datetimeinterface' => 'DateTimeInterface',
          'str' => 'Illuminate\\Support\\Str',
        ),
         'className' => 'App\\Models\\User',
         'functionName' => 'generateTokenString',
         'templatePhpDocNodes' => 
        array (
        ),
         'parent' => 
        \PHPStan\Analyser\IntermediaryNameScope::__set_state(array(
           'namespace' => 'Laravel\\Sanctum',
           'uses' => 
          array (
            'datetimeinterface' => 'DateTimeInterface',
            'str' => 'Illuminate\\Support\\Str',
          ),
           'className' => 'App\\Models\\User',
           'functionName' => NULL,
           'templatePhpDocNodes' => 
          array (
            'TToken' => 
            array (
              0 => '@template',
              1 => 
              \PHPStan\PhpDocParser\Ast\PhpDoc\TemplateTagValueNode::__set_state(array(
                 'name' => 'TToken',
                 'bound' => 
                \PHPStan\PhpDocParser\Ast\Type\IdentifierTypeNode::__set_state(array(
                   'name' => '\\Laravel\\Sanctum\\Contracts\\HasAbilities',
                   'attributes' => 
                  array (
                    'startLine' => 2,
                    'endLine' => 2,
                  ),
                )),
                 'default' => 
                \PHPStan\PhpDocParser\Ast\Type\IdentifierTypeNode::__set_state(array(
                   'name' => '\\Laravel\\Sanctum\\PersonalAccessToken',
                   'attributes' => 
                  array (
                    'startLine' => 2,
                    'endLine' => 2,
                  ),
                )),
                 'lowerBound' => NULL,
                 'description' => '',
                 'attributes' => 
                array (
                  'startLine' => 2,
                  'endLine' => 2,
                ),
              )),
            ),
          ),
           'parent' => NULL,
           'typeAliasesMap' => 
          array (
          ),
           'bypassTypeAliases' => false,
           'constUses' => 
          array (
          ),
           'typeAliasClassName' => 'Laravel\\Sanctum\\HasApiTokens',
           'traitData' => NULL,
        )),
         'typeAliasesMap' => 
        array (
        ),
         'bypassTypeAliases' => false,
         'constUses' => 
        array (
        ),
         'typeAliasClassName' => 'Laravel\\Sanctum\\HasApiTokens',
         'traitData' => 
        array (
          0 => '/var/www/html/app/Models/User.php',
          1 => 'App\\Models\\User',
          2 => 'Laravel\\Sanctum\\HasApiTokens',
          3 => NULL,
          4 => NULL,
        ),
      )),
      'dfd601d98d93fc15fdd76e8799f71e03' => 
      \PHPStan\Analyser\IntermediaryNameScope::__set_state(array(
         'namespace' => 'Laravel\\Sanctum',
         'uses' => 
        array (
          'datetimeinterface' => 'DateTimeInterface',
          'str' => 'Illuminate\\Support\\Str',
        ),
         'className' => 'App\\Models\\User',
         'functionName' => 'currentAccessToken',
         'templatePhpDocNodes' => 
        array (
        ),
         'parent' => 
        \PHPStan\Analyser\IntermediaryNameScope::__set_state(array(
           'namespace' => 'Laravel\\Sanctum',
           'uses' => 
          array (
            'datetimeinterface' => 'DateTimeInterface',
            'str' => 'Illuminate\\Support\\Str',
          ),
           'className' => 'App\\Models\\User',
           'functionName' => NULL,
           'templatePhpDocNodes' => 
          array (
            'TToken' => 
            array (
              0 => '@template',
              1 => 
              \PHPStan\PhpDocParser\Ast\PhpDoc\TemplateTagValueNode::__set_state(array(
                 'name' => 'TToken',
                 'bound' => 
                \PHPStan\PhpDocParser\Ast\Type\IdentifierTypeNode::__set_state(array(
                   'name' => '\\Laravel\\Sanctum\\Contracts\\HasAbilities',
                   'attributes' => 
                  array (
                    'startLine' => 2,
                    'endLine' => 2,
                  ),
                )),
                 'default' => 
                \PHPStan\PhpDocParser\Ast\Type\IdentifierTypeNode::__set_state(array(
                   'name' => '\\Laravel\\Sanctum\\PersonalAccessToken',
                   'attributes' => 
                  array (
                    'startLine' => 2,
                    'endLine' => 2,
                  ),
                )),
                 'lowerBound' => NULL,
                 'description' => '',
                 'attributes' => 
                array (
                  'startLine' => 2,
                  'endLine' => 2,
                ),
              )),
            ),
          ),
           'parent' => NULL,
           'typeAliasesMap' => 
          array (
          ),
           'bypassTypeAliases' => false,
           'constUses' => 
          array (
          ),
           'typeAliasClassName' => 'Laravel\\Sanctum\\HasApiTokens',
           'traitData' => NULL,
        )),
         'typeAliasesMap' => 
        array (
        ),
         'bypassTypeAliases' => false,
         'constUses' => 
        array (
        ),
         'typeAliasClassName' => 'Laravel\\Sanctum\\HasApiTokens',
         'traitData' => 
        array (
          0 => '/var/www/html/app/Models/User.php',
          1 => 'App\\Models\\User',
          2 => 'Laravel\\Sanctum\\HasApiTokens',
          3 => NULL,
          4 => NULL,
        ),
      )),
      '90a30bbac65ff45a530001b49e1600d2' => 
      \PHPStan\Analyser\IntermediaryNameScope::__set_state(array(
         'namespace' => 'Laravel\\Sanctum',
         'uses' => 
        array (
          'datetimeinterface' => 'DateTimeInterface',
          'str' => 'Illuminate\\Support\\Str',
        ),
         'className' => 'App\\Models\\User',
         'functionName' => 'withAccessToken',
         'templatePhpDocNodes' => 
        array (
        ),
         'parent' => 
        \PHPStan\Analyser\IntermediaryNameScope::__set_state(array(
           'namespace' => 'Laravel\\Sanctum',
           'uses' => 
          array (
            'datetimeinterface' => 'DateTimeInterface',
            'str' => 'Illuminate\\Support\\Str',
          ),
           'className' => 'App\\Models\\User',
           'functionName' => NULL,
           'templatePhpDocNodes' => 
          array (
            'TToken' => 
            array (
              0 => '@template',
              1 => 
              \PHPStan\PhpDocParser\Ast\PhpDoc\TemplateTagValueNode::__set_state(array(
                 'name' => 'TToken',
                 'bound' => 
                \PHPStan\PhpDocParser\Ast\Type\IdentifierTypeNode::__set_state(array(
                   'name' => '\\Laravel\\Sanctum\\Contracts\\HasAbilities',
                   'attributes' => 
                  array (
                    'startLine' => 2,
                    'endLine' => 2,
                  ),
                )),
                 'default' => 
                \PHPStan\PhpDocParser\Ast\Type\IdentifierTypeNode::__set_state(array(
                   'name' => '\\Laravel\\Sanctum\\PersonalAccessToken',
                   'attributes' => 
                  array (
                    'startLine' => 2,
                    'endLine' => 2,
                  ),
                )),
                 'lowerBound' => NULL,
                 'description' => '',
                 'attributes' => 
                array (
                  'startLine' => 2,
                  'endLine' => 2,
                ),
              )),
            ),
          ),
           'parent' => NULL,
           'typeAliasesMap' => 
          array (
          ),
           'bypassTypeAliases' => false,
           'constUses' => 
          array (
          ),
           'typeAliasClassName' => 'Laravel\\Sanctum\\HasApiTokens',
           'traitData' => NULL,
        )),
         'typeAliasesMap' => 
        array (
        ),
         'bypassTypeAliases' => false,
         'constUses' => 
        array (
        ),
         'typeAliasClassName' => 'Laravel\\Sanctum\\HasApiTokens',
         'traitData' => 
        array (
          0 => '/var/www/html/app/Models/User.php',
          1 => 'App\\Models\\User',
          2 => 'Laravel\\Sanctum\\HasApiTokens',
          3 => NULL,
          4 => NULL,
        ),
      )),
      'ac078721d2a5307f1a89adaf7ea88cfc' => 
      \PHPStan\Analyser\IntermediaryNameScope::__set_state(array(
         'namespace' => 'Illuminate\\Database\\Eloquent\\Factories',
         'uses' => 
        array (
          'usefactory' => 'Illuminate\\Database\\Eloquent\\Attributes\\UseFactory',
        ),
         'className' => 'App\\Models\\User',
         'functionName' => NULL,
         'templatePhpDocNodes' => 
        array (
          'TFactory' => 
          array (
            0 => '@template',
            1 => 
            \PHPStan\PhpDocParser\Ast\PhpDoc\TemplateTagValueNode::__set_state(array(
               'name' => 'TFactory',
               'bound' => 
              \PHPStan\PhpDocParser\Ast\Type\IdentifierTypeNode::__set_state(array(
                 'name' => '\\Illuminate\\Database\\Eloquent\\Factories\\Factory',
                 'attributes' => 
                array (
                  'startLine' => 2,
                  'endLine' => 2,
                ),
              )),
               'default' => NULL,
               'lowerBound' => NULL,
               'description' => '',
               'attributes' => 
              array (
                'startLine' => 2,
                'endLine' => 2,
              ),
            )),
          ),
        ),
         'parent' => NULL,
         'typeAliasesMap' => 
        array (
        ),
         'bypassTypeAliases' => false,
         'constUses' => 
        array (
        ),
         'typeAliasClassName' => 'Illuminate\\Database\\Eloquent\\Factories\\HasFactory',
         'traitData' => 
        array (
          0 => '/var/www/html/app/Models/User.php',
          1 => 'App\\Models\\User',
          2 => 'Illuminate\\Database\\Eloquent\\Factories\\HasFactory',
          3 => NULL,
          4 => NULL,
        ),
      )),
      'bd09c3c02f3ab3286d738d373badeaaf' => 
      \PHPStan\Analyser\IntermediaryNameScope::__set_state(array(
         'namespace' => 'Illuminate\\Database\\Eloquent\\Factories',
         'uses' => 
        array (
          'usefactory' => 'Illuminate\\Database\\Eloquent\\Attributes\\UseFactory',
        ),
         'className' => 'App\\Models\\User',
         'functionName' => 'factory',
         'templatePhpDocNodes' => 
        array (
        ),
         'parent' => 
        \PHPStan\Analyser\IntermediaryNameScope::__set_state(array(
           'namespace' => 'Illuminate\\Database\\Eloquent\\Factories',
           'uses' => 
          array (
            'usefactory' => 'Illuminate\\Database\\Eloquent\\Attributes\\UseFactory',
          ),
           'className' => 'App\\Models\\User',
           'functionName' => NULL,
           'templatePhpDocNodes' => 
          array (
            'TFactory' => 
            array (
              0 => '@template',
              1 => 
              \PHPStan\PhpDocParser\Ast\PhpDoc\TemplateTagValueNode::__set_state(array(
                 'name' => 'TFactory',
                 'bound' => 
                \PHPStan\PhpDocParser\Ast\Type\IdentifierTypeNode::__set_state(array(
                   'name' => '\\Illuminate\\Database\\Eloquent\\Factories\\Factory',
                   'attributes' => 
                  array (
                    'startLine' => 2,
                    'endLine' => 2,
                  ),
                )),
                 'default' => NULL,
                 'lowerBound' => NULL,
                 'description' => '',
                 'attributes' => 
                array (
                  'startLine' => 2,
                  'endLine' => 2,
                ),
              )),
            ),
          ),
           'parent' => NULL,
           'typeAliasesMap' => 
          array (
          ),
           'bypassTypeAliases' => false,
           'constUses' => 
          array (
          ),
           'typeAliasClassName' => 'Illuminate\\Database\\Eloquent\\Factories\\HasFactory',
           'traitData' => NULL,
        )),
         'typeAliasesMap' => 
        array (
        ),
         'bypassTypeAliases' => false,
         'constUses' => 
        array (
        ),
         'typeAliasClassName' => 'Illuminate\\Database\\Eloquent\\Factories\\HasFactory',
         'traitData' => 
        array (
          0 => '/var/www/html/app/Models/User.php',
          1 => 'App\\Models\\User',
          2 => 'Illuminate\\Database\\Eloquent\\Factories\\HasFactory',
          3 => NULL,
          4 => NULL,
        ),
      )),
      'f42d3715053b5e0f92038836869d95fc' => 
      \PHPStan\Analyser\IntermediaryNameScope::__set_state(array(
         'namespace' => 'Illuminate\\Database\\Eloquent\\Factories',
         'uses' => 
        array (
          'usefactory' => 'Illuminate\\Database\\Eloquent\\Attributes\\UseFactory',
        ),
         'className' => 'App\\Models\\User',
         'functionName' => 'newFactory',
         'templatePhpDocNodes' => 
        array (
        ),
         'parent' => 
        \PHPStan\Analyser\IntermediaryNameScope::__set_state(array(
           'namespace' => 'Illuminate\\Database\\Eloquent\\Factories',
           'uses' => 
          array (
            'usefactory' => 'Illuminate\\Database\\Eloquent\\Attributes\\UseFactory',
          ),
           'className' => 'App\\Models\\User',
           'functionName' => NULL,
           'templatePhpDocNodes' => 
          array (
            'TFactory' => 
            array (
              0 => '@template',
              1 => 
              \PHPStan\PhpDocParser\Ast\PhpDoc\TemplateTagValueNode::__set_state(array(
                 'name' => 'TFactory',
                 'bound' => 
                \PHPStan\PhpDocParser\Ast\Type\IdentifierTypeNode::__set_state(array(
                   'name' => '\\Illuminate\\Database\\Eloquent\\Factories\\Factory',
                   'attributes' => 
                  array (
                    'startLine' => 2,
                    'endLine' => 2,
                  ),
                )),
                 'default' => NULL,
                 'lowerBound' => NULL,
                 'description' => '',
                 'attributes' => 
                array (
                  'startLine' => 2,
                  'endLine' => 2,
                ),
              )),
            ),
          ),
           'parent' => NULL,
           'typeAliasesMap' => 
          array (
          ),
           'bypassTypeAliases' => false,
           'constUses' => 
          array (
          ),
           'typeAliasClassName' => 'Illuminate\\Database\\Eloquent\\Factories\\HasFactory',
           'traitData' => NULL,
        )),
         'typeAliasesMap' => 
        array (
        ),
         'bypassTypeAliases' => false,
         'constUses' => 
        array (
        ),
         'typeAliasClassName' => 'Illuminate\\Database\\Eloquent\\Factories\\HasFactory',
         'traitData' => 
        array (
          0 => '/var/www/html/app/Models/User.php',
          1 => 'App\\Models\\User',
          2 => 'Illuminate\\Database\\Eloquent\\Factories\\HasFactory',
          3 => NULL,
          4 => NULL,
        ),
      )),
      '2771b21513b5f43d111ce232d7b4bcc0' => 
      \PHPStan\Analyser\IntermediaryNameScope::__set_state(array(
         'namespace' => 'Illuminate\\Database\\Eloquent\\Factories',
         'uses' => 
        array (
          'usefactory' => 'Illuminate\\Database\\Eloquent\\Attributes\\UseFactory',
        ),
         'className' => 'App\\Models\\User',
         'functionName' => 'getUseFactoryAttribute',
         'templatePhpDocNodes' => 
        array (
        ),
         'parent' => 
        \PHPStan\Analyser\IntermediaryNameScope::__set_state(array(
           'namespace' => 'Illuminate\\Database\\Eloquent\\Factories',
           'uses' => 
          array (
            'usefactory' => 'Illuminate\\Database\\Eloquent\\Attributes\\UseFactory',
          ),
           'className' => 'App\\Models\\User',
           'functionName' => NULL,
           'templatePhpDocNodes' => 
          array (
            'TFactory' => 
            array (
              0 => '@template',
              1 => 
              \PHPStan\PhpDocParser\Ast\PhpDoc\TemplateTagValueNode::__set_state(array(
                 'name' => 'TFactory',
                 'bound' => 
                \PHPStan\PhpDocParser\Ast\Type\IdentifierTypeNode::__set_state(array(
                   'name' => '\\Illuminate\\Database\\Eloquent\\Factories\\Factory',
                   'attributes' => 
                  array (
                    'startLine' => 2,
                    'endLine' => 2,
                  ),
                )),
                 'default' => NULL,
                 'lowerBound' => NULL,
                 'description' => '',
                 'attributes' => 
                array (
                  'startLine' => 2,
                  'endLine' => 2,
                ),
              )),
            ),
          ),
           'parent' => NULL,
           'typeAliasesMap' => 
          array (
          ),
           'bypassTypeAliases' => false,
           'constUses' => 
          array (
          ),
           'typeAliasClassName' => 'Illuminate\\Database\\Eloquent\\Factories\\HasFactory',
           'traitData' => NULL,
        )),
         'typeAliasesMap' => 
        array (
        ),
         'bypassTypeAliases' => false,
         'constUses' => 
        array (
        ),
         'typeAliasClassName' => 'Illuminate\\Database\\Eloquent\\Factories\\HasFactory',
         'traitData' => 
        array (
          0 => '/var/www/html/app/Models/User.php',
          1 => 'App\\Models\\User',
          2 => 'Illuminate\\Database\\Eloquent\\Factories\\HasFactory',
          3 => NULL,
          4 => NULL,
        ),
      )),
      '98767ec236a480b444b76a515aa4e617' => 
      \PHPStan\Analyser\IntermediaryNameScope::__set_state(array(
         'namespace' => 'Laravel\\Jetstream',
         'uses' => 
        array (
          'attribute' => 'Illuminate\\Database\\Eloquent\\Casts\\Attribute',
          'uploadedfile' => 'Illuminate\\Http\\UploadedFile',
          'storage' => 'Illuminate\\Support\\Facades\\Storage',
        ),
         'className' => 'App\\Models\\User',
         'functionName' => NULL,
         'templatePhpDocNodes' => 
        array (
        ),
         'parent' => NULL,
         'typeAliasesMap' => 
        array (
        ),
         'bypassTypeAliases' => false,
         'constUses' => 
        array (
        ),
         'typeAliasClassName' => 'Laravel\\Jetstream\\HasProfilePhoto',
         'traitData' => 
        array (
          0 => '/var/www/html/app/Models/User.php',
          1 => 'App\\Models\\User',
          2 => 'Laravel\\Jetstream\\HasProfilePhoto',
          3 => NULL,
          4 => NULL,
        ),
      )),
      'aaf8ee211ba2f1bbb99e10197a945d58' => 
      \PHPStan\Analyser\IntermediaryNameScope::__set_state(array(
         'namespace' => 'Laravel\\Jetstream',
         'uses' => 
        array (
          'attribute' => 'Illuminate\\Database\\Eloquent\\Casts\\Attribute',
          'uploadedfile' => 'Illuminate\\Http\\UploadedFile',
          'storage' => 'Illuminate\\Support\\Facades\\Storage',
        ),
         'className' => 'App\\Models\\User',
         'functionName' => 'updateProfilePhoto',
         'templatePhpDocNodes' => 
        array (
        ),
         'parent' => NULL,
         'typeAliasesMap' => 
        array (
        ),
         'bypassTypeAliases' => false,
         'constUses' => 
        array (
        ),
         'typeAliasClassName' => 'Laravel\\Jetstream\\HasProfilePhoto',
         'traitData' => 
        array (
          0 => '/var/www/html/app/Models/User.php',
          1 => 'App\\Models\\User',
          2 => 'Laravel\\Jetstream\\HasProfilePhoto',
          3 => NULL,
          4 => NULL,
        ),
      )),
      '0100d7fa2f70663d25419be8c7aa78fd' => 
      \PHPStan\Analyser\IntermediaryNameScope::__set_state(array(
         'namespace' => 'Laravel\\Jetstream',
         'uses' => 
        array (
          'attribute' => 'Illuminate\\Database\\Eloquent\\Casts\\Attribute',
          'uploadedfile' => 'Illuminate\\Http\\UploadedFile',
          'storage' => 'Illuminate\\Support\\Facades\\Storage',
        ),
         'className' => 'App\\Models\\User',
         'functionName' => 'deleteProfilePhoto',
         'templatePhpDocNodes' => 
        array (
        ),
         'parent' => NULL,
         'typeAliasesMap' => 
        array (
        ),
         'bypassTypeAliases' => false,
         'constUses' => 
        array (
        ),
         'typeAliasClassName' => 'Laravel\\Jetstream\\HasProfilePhoto',
         'traitData' => 
        array (
          0 => '/var/www/html/app/Models/User.php',
          1 => 'App\\Models\\User',
          2 => 'Laravel\\Jetstream\\HasProfilePhoto',
          3 => NULL,
          4 => NULL,
        ),
      )),
      '6b6575097fe15dd1d442c39a537e2b5e' => 
      \PHPStan\Analyser\IntermediaryNameScope::__set_state(array(
         'namespace' => 'Laravel\\Jetstream',
         'uses' => 
        array (
          'attribute' => 'Illuminate\\Database\\Eloquent\\Casts\\Attribute',
          'uploadedfile' => 'Illuminate\\Http\\UploadedFile',
          'storage' => 'Illuminate\\Support\\Facades\\Storage',
        ),
         'className' => 'App\\Models\\User',
         'functionName' => 'profilePhotoUrl',
         'templatePhpDocNodes' => 
        array (
        ),
         'parent' => NULL,
         'typeAliasesMap' => 
        array (
        ),
         'bypassTypeAliases' => false,
         'constUses' => 
        array (
        ),
         'typeAliasClassName' => 'Laravel\\Jetstream\\HasProfilePhoto',
         'traitData' => 
        array (
          0 => '/var/www/html/app/Models/User.php',
          1 => 'App\\Models\\User',
          2 => 'Laravel\\Jetstream\\HasProfilePhoto',
          3 => NULL,
          4 => NULL,
        ),
      )),
      'fdd64c023e1e03b76886a483d541c793' => 
      \PHPStan\Analyser\IntermediaryNameScope::__set_state(array(
         'namespace' => 'Laravel\\Jetstream',
         'uses' => 
        array (
          'attribute' => 'Illuminate\\Database\\Eloquent\\Casts\\Attribute',
          'uploadedfile' => 'Illuminate\\Http\\UploadedFile',
          'storage' => 'Illuminate\\Support\\Facades\\Storage',
        ),
         'className' => 'App\\Models\\User',
         'functionName' => 'defaultProfilePhotoUrl',
         'templatePhpDocNodes' => 
        array (
        ),
         'parent' => NULL,
         'typeAliasesMap' => 
        array (
        ),
         'bypassTypeAliases' => false,
         'constUses' => 
        array (
        ),
         'typeAliasClassName' => 'Laravel\\Jetstream\\HasProfilePhoto',
         'traitData' => 
        array (
          0 => '/var/www/html/app/Models/User.php',
          1 => 'App\\Models\\User',
          2 => 'Laravel\\Jetstream\\HasProfilePhoto',
          3 => NULL,
          4 => NULL,
        ),
      )),
      'f75f65b10d76e9a81a4a86918c4f7b9e' => 
      \PHPStan\Analyser\IntermediaryNameScope::__set_state(array(
         'namespace' => 'Laravel\\Jetstream',
         'uses' => 
        array (
          'attribute' => 'Illuminate\\Database\\Eloquent\\Casts\\Attribute',
          'uploadedfile' => 'Illuminate\\Http\\UploadedFile',
          'storage' => 'Illuminate\\Support\\Facades\\Storage',
        ),
         'className' => 'App\\Models\\User',
         'functionName' => 'profilePhotoDisk',
         'templatePhpDocNodes' => 
        array (
        ),
         'parent' => NULL,
         'typeAliasesMap' => 
        array (
        ),
         'bypassTypeAliases' => false,
         'constUses' => 
        array (
        ),
         'typeAliasClassName' => 'Laravel\\Jetstream\\HasProfilePhoto',
         'traitData' => 
        array (
          0 => '/var/www/html/app/Models/User.php',
          1 => 'App\\Models\\User',
          2 => 'Laravel\\Jetstream\\HasProfilePhoto',
          3 => NULL,
          4 => NULL,
        ),
      )),
      '74a2228ba9a67ed0d94e4def02cf1c8f' => 
      \PHPStan\Analyser\IntermediaryNameScope::__set_state(array(
         'namespace' => 'Spatie\\Permission\\Traits',
         'uses' => 
        array (
          'backedenum' => 'BackedEnum',
          'builder' => 'Illuminate\\Database\\Eloquent\\Builder',
          'model' => 'Illuminate\\Database\\Eloquent\\Model',
          'belongstomany' => 'Illuminate\\Database\\Eloquent\\Relations\\BelongsToMany',
          'pivot' => 'Illuminate\\Database\\Eloquent\\Relations\\Pivot',
          'arr' => 'Illuminate\\Support\\Arr',
          'collection' => 'Illuminate\\Support\\Collection',
          'permission' => 'Spatie\\Permission\\Contracts\\Permission',
          'role' => 'Spatie\\Permission\\Contracts\\Role',
          'roleattachedevent' => 'Spatie\\Permission\\Events\\RoleAttachedEvent',
          'roledetachedevent' => 'Spatie\\Permission\\Events\\RoleDetachedEvent',
          'permissionregistrar' => 'Spatie\\Permission\\PermissionRegistrar',
          'config' => 'Spatie\\Permission\\Support\\Config',
          'typeerror' => 'TypeError',
        ),
         'className' => 'App\\Models\\User',
         'functionName' => NULL,
         'templatePhpDocNodes' => 
        array (
        ),
         'parent' => NULL,
         'typeAliasesMap' => 
        array (
        ),
         'bypassTypeAliases' => false,
         'constUses' => 
        array (
        ),
         'typeAliasClassName' => 'Spatie\\Permission\\Traits\\HasRoles',
         'traitData' => 
        array (
          0 => '/var/www/html/app/Models/User.php',
          1 => 'App\\Models\\User',
          2 => 'Spatie\\Permission\\Traits\\HasRoles',
          3 => NULL,
          4 => NULL,
        ),
      )),
      '4a91e56ee999c08fa4f7cdda04834021' => 
      \PHPStan\Analyser\IntermediaryNameScope::__set_state(array(
         'namespace' => 'Spatie\\Permission\\Traits',
         'uses' => 
        array (
          'backedenum' => 'BackedEnum',
          'builder' => 'Illuminate\\Database\\Eloquent\\Builder',
          'belongstomany' => 'Illuminate\\Database\\Eloquent\\Relations\\BelongsToMany',
          'pivot' => 'Illuminate\\Database\\Eloquent\\Relations\\Pivot',
          'arr' => 'Illuminate\\Support\\Arr',
          'collection' => 'Illuminate\\Support\\Collection',
          'permission' => 'Spatie\\Permission\\Contracts\\Permission',
          'role' => 'Spatie\\Permission\\Contracts\\Role',
          'wildcard' => 'Spatie\\Permission\\Contracts\\Wildcard',
          'permissionattachedevent' => 'Spatie\\Permission\\Events\\PermissionAttachedEvent',
          'permissiondetachedevent' => 'Spatie\\Permission\\Events\\PermissionDetachedEvent',
          'guarddoesnotmatch' => 'Spatie\\Permission\\Exceptions\\GuardDoesNotMatch',
          'permissiondoesnotexist' => 'Spatie\\Permission\\Exceptions\\PermissionDoesNotExist',
          'wildcardpermissioninvalidargument' => 'Spatie\\Permission\\Exceptions\\WildcardPermissionInvalidArgument',
          'wildcardpermissionnotimplementscontract' => 'Spatie\\Permission\\Exceptions\\WildcardPermissionNotImplementsContract',
          'guard' => 'Spatie\\Permission\\Guard',
          'permissionregistrar' => 'Spatie\\Permission\\PermissionRegistrar',
          'config' => 'Spatie\\Permission\\Support\\Config',
        ),
         'className' => 'App\\Models\\User',
         'functionName' => NULL,
         'templatePhpDocNodes' => 
        array (
        ),
         'parent' => NULL,
         'typeAliasesMap' => 
        array (
        ),
         'bypassTypeAliases' => false,
         'constUses' => 
        array (
        ),
         'typeAliasClassName' => 'Spatie\\Permission\\Traits\\HasPermissions',
         'traitData' => 
        array (
          0 => '/var/www/html/app/Models/User.php',
          1 => 'App\\Models\\User',
          2 => 'Spatie\\Permission\\Traits\\HasPermissions',
          3 => 'Spatie\\Permission\\Traits\\HasRoles',
          4 => NULL,
        ),
      )),
      '5f6db8306eebb3967e1ab5ae61a3317e' => 
      \PHPStan\Analyser\IntermediaryNameScope::__set_state(array(
         'namespace' => 'Spatie\\Permission\\Traits',
         'uses' => 
        array (
          'backedenum' => 'BackedEnum',
          'builder' => 'Illuminate\\Database\\Eloquent\\Builder',
          'belongstomany' => 'Illuminate\\Database\\Eloquent\\Relations\\BelongsToMany',
          'pivot' => 'Illuminate\\Database\\Eloquent\\Relations\\Pivot',
          'arr' => 'Illuminate\\Support\\Arr',
          'collection' => 'Illuminate\\Support\\Collection',
          'permission' => 'Spatie\\Permission\\Contracts\\Permission',
          'role' => 'Spatie\\Permission\\Contracts\\Role',
          'wildcard' => 'Spatie\\Permission\\Contracts\\Wildcard',
          'permissionattachedevent' => 'Spatie\\Permission\\Events\\PermissionAttachedEvent',
          'permissiondetachedevent' => 'Spatie\\Permission\\Events\\PermissionDetachedEvent',
          'guarddoesnotmatch' => 'Spatie\\Permission\\Exceptions\\GuardDoesNotMatch',
          'permissiondoesnotexist' => 'Spatie\\Permission\\Exceptions\\PermissionDoesNotExist',
          'wildcardpermissioninvalidargument' => 'Spatie\\Permission\\Exceptions\\WildcardPermissionInvalidArgument',
          'wildcardpermissionnotimplementscontract' => 'Spatie\\Permission\\Exceptions\\WildcardPermissionNotImplementsContract',
          'guard' => 'Spatie\\Permission\\Guard',
          'permissionregistrar' => 'Spatie\\Permission\\PermissionRegistrar',
          'config' => 'Spatie\\Permission\\Support\\Config',
        ),
         'className' => 'App\\Models\\User',
         'functionName' => 'bootHasPermissions',
         'templatePhpDocNodes' => 
        array (
        ),
         'parent' => NULL,
         'typeAliasesMap' => 
        array (
        ),
         'bypassTypeAliases' => false,
         'constUses' => 
        array (
        ),
         'typeAliasClassName' => 'Spatie\\Permission\\Traits\\HasPermissions',
         'traitData' => 
        array (
          0 => '/var/www/html/app/Models/User.php',
          1 => 'App\\Models\\User',
          2 => 'Spatie\\Permission\\Traits\\HasPermissions',
          3 => 'Spatie\\Permission\\Traits\\HasRoles',
          4 => NULL,
        ),
      )),
      '22a9435fb6f192b39a434a9f551c003e' => 
      \PHPStan\Analyser\IntermediaryNameScope::__set_state(array(
         'namespace' => 'Spatie\\Permission\\Traits',
         'uses' => 
        array (
          'backedenum' => 'BackedEnum',
          'builder' => 'Illuminate\\Database\\Eloquent\\Builder',
          'belongstomany' => 'Illuminate\\Database\\Eloquent\\Relations\\BelongsToMany',
          'pivot' => 'Illuminate\\Database\\Eloquent\\Relations\\Pivot',
          'arr' => 'Illuminate\\Support\\Arr',
          'collection' => 'Illuminate\\Support\\Collection',
          'permission' => 'Spatie\\Permission\\Contracts\\Permission',
          'role' => 'Spatie\\Permission\\Contracts\\Role',
          'wildcard' => 'Spatie\\Permission\\Contracts\\Wildcard',
          'permissionattachedevent' => 'Spatie\\Permission\\Events\\PermissionAttachedEvent',
          'permissiondetachedevent' => 'Spatie\\Permission\\Events\\PermissionDetachedEvent',
          'guarddoesnotmatch' => 'Spatie\\Permission\\Exceptions\\GuardDoesNotMatch',
          'permissiondoesnotexist' => 'Spatie\\Permission\\Exceptions\\PermissionDoesNotExist',
          'wildcardpermissioninvalidargument' => 'Spatie\\Permission\\Exceptions\\WildcardPermissionInvalidArgument',
          'wildcardpermissionnotimplementscontract' => 'Spatie\\Permission\\Exceptions\\WildcardPermissionNotImplementsContract',
          'guard' => 'Spatie\\Permission\\Guard',
          'permissionregistrar' => 'Spatie\\Permission\\PermissionRegistrar',
          'config' => 'Spatie\\Permission\\Support\\Config',
        ),
         'className' => 'App\\Models\\User',
         'functionName' => 'getPermissionClass',
         'templatePhpDocNodes' => 
        array (
        ),
         'parent' => NULL,
         'typeAliasesMap' => 
        array (
        ),
         'bypassTypeAliases' => false,
         'constUses' => 
        array (
        ),
         'typeAliasClassName' => 'Spatie\\Permission\\Traits\\HasPermissions',
         'traitData' => 
        array (
          0 => '/var/www/html/app/Models/User.php',
          1 => 'App\\Models\\User',
          2 => 'Spatie\\Permission\\Traits\\HasPermissions',
          3 => 'Spatie\\Permission\\Traits\\HasRoles',
          4 => NULL,
        ),
      )),
      '95dba3ee6c84aa0975f95213562c8e1e' => 
      \PHPStan\Analyser\IntermediaryNameScope::__set_state(array(
         'namespace' => 'Spatie\\Permission\\Traits',
         'uses' => 
        array (
          'backedenum' => 'BackedEnum',
          'builder' => 'Illuminate\\Database\\Eloquent\\Builder',
          'belongstomany' => 'Illuminate\\Database\\Eloquent\\Relations\\BelongsToMany',
          'pivot' => 'Illuminate\\Database\\Eloquent\\Relations\\Pivot',
          'arr' => 'Illuminate\\Support\\Arr',
          'collection' => 'Illuminate\\Support\\Collection',
          'permission' => 'Spatie\\Permission\\Contracts\\Permission',
          'role' => 'Spatie\\Permission\\Contracts\\Role',
          'wildcard' => 'Spatie\\Permission\\Contracts\\Wildcard',
          'permissionattachedevent' => 'Spatie\\Permission\\Events\\PermissionAttachedEvent',
          'permissiondetachedevent' => 'Spatie\\Permission\\Events\\PermissionDetachedEvent',
          'guarddoesnotmatch' => 'Spatie\\Permission\\Exceptions\\GuardDoesNotMatch',
          'permissiondoesnotexist' => 'Spatie\\Permission\\Exceptions\\PermissionDoesNotExist',
          'wildcardpermissioninvalidargument' => 'Spatie\\Permission\\Exceptions\\WildcardPermissionInvalidArgument',
          'wildcardpermissionnotimplementscontract' => 'Spatie\\Permission\\Exceptions\\WildcardPermissionNotImplementsContract',
          'guard' => 'Spatie\\Permission\\Guard',
          'permissionregistrar' => 'Spatie\\Permission\\PermissionRegistrar',
          'config' => 'Spatie\\Permission\\Support\\Config',
        ),
         'className' => 'App\\Models\\User',
         'functionName' => 'getWildcardClass',
         'templatePhpDocNodes' => 
        array (
        ),
         'parent' => NULL,
         'typeAliasesMap' => 
        array (
        ),
         'bypassTypeAliases' => false,
         'constUses' => 
        array (
        ),
         'typeAliasClassName' => 'Spatie\\Permission\\Traits\\HasPermissions',
         'traitData' => 
        array (
          0 => '/var/www/html/app/Models/User.php',
          1 => 'App\\Models\\User',
          2 => 'Spatie\\Permission\\Traits\\HasPermissions',
          3 => 'Spatie\\Permission\\Traits\\HasRoles',
          4 => NULL,
        ),
      )),
      '668e115eca3655e26d6caf9efd05f785' => 
      \PHPStan\Analyser\IntermediaryNameScope::__set_state(array(
         'namespace' => 'Spatie\\Permission\\Traits',
         'uses' => 
        array (
          'backedenum' => 'BackedEnum',
          'builder' => 'Illuminate\\Database\\Eloquent\\Builder',
          'belongstomany' => 'Illuminate\\Database\\Eloquent\\Relations\\BelongsToMany',
          'pivot' => 'Illuminate\\Database\\Eloquent\\Relations\\Pivot',
          'arr' => 'Illuminate\\Support\\Arr',
          'collection' => 'Illuminate\\Support\\Collection',
          'permission' => 'Spatie\\Permission\\Contracts\\Permission',
          'role' => 'Spatie\\Permission\\Contracts\\Role',
          'wildcard' => 'Spatie\\Permission\\Contracts\\Wildcard',
          'permissionattachedevent' => 'Spatie\\Permission\\Events\\PermissionAttachedEvent',
          'permissiondetachedevent' => 'Spatie\\Permission\\Events\\PermissionDetachedEvent',
          'guarddoesnotmatch' => 'Spatie\\Permission\\Exceptions\\GuardDoesNotMatch',
          'permissiondoesnotexist' => 'Spatie\\Permission\\Exceptions\\PermissionDoesNotExist',
          'wildcardpermissioninvalidargument' => 'Spatie\\Permission\\Exceptions\\WildcardPermissionInvalidArgument',
          'wildcardpermissionnotimplementscontract' => 'Spatie\\Permission\\Exceptions\\WildcardPermissionNotImplementsContract',
          'guard' => 'Spatie\\Permission\\Guard',
          'permissionregistrar' => 'Spatie\\Permission\\PermissionRegistrar',
          'config' => 'Spatie\\Permission\\Support\\Config',
        ),
         'className' => 'App\\Models\\User',
         'functionName' => 'permissions',
         'templatePhpDocNodes' => 
        array (
        ),
         'parent' => NULL,
         'typeAliasesMap' => 
        array (
        ),
         'bypassTypeAliases' => false,
         'constUses' => 
        array (
        ),
         'typeAliasClassName' => 'Spatie\\Permission\\Traits\\HasPermissions',
         'traitData' => 
        array (
          0 => '/var/www/html/app/Models/User.php',
          1 => 'App\\Models\\User',
          2 => 'Spatie\\Permission\\Traits\\HasPermissions',
          3 => 'Spatie\\Permission\\Traits\\HasRoles',
          4 => NULL,
        ),
      )),
      'df3a8a89c410fffefdddd18499f50ba0' => 
      \PHPStan\Analyser\IntermediaryNameScope::__set_state(array(
         'namespace' => 'Spatie\\Permission\\Traits',
         'uses' => 
        array (
          'backedenum' => 'BackedEnum',
          'builder' => 'Illuminate\\Database\\Eloquent\\Builder',
          'belongstomany' => 'Illuminate\\Database\\Eloquent\\Relations\\BelongsToMany',
          'pivot' => 'Illuminate\\Database\\Eloquent\\Relations\\Pivot',
          'arr' => 'Illuminate\\Support\\Arr',
          'collection' => 'Illuminate\\Support\\Collection',
          'permission' => 'Spatie\\Permission\\Contracts\\Permission',
          'role' => 'Spatie\\Permission\\Contracts\\Role',
          'wildcard' => 'Spatie\\Permission\\Contracts\\Wildcard',
          'permissionattachedevent' => 'Spatie\\Permission\\Events\\PermissionAttachedEvent',
          'permissiondetachedevent' => 'Spatie\\Permission\\Events\\PermissionDetachedEvent',
          'guarddoesnotmatch' => 'Spatie\\Permission\\Exceptions\\GuardDoesNotMatch',
          'permissiondoesnotexist' => 'Spatie\\Permission\\Exceptions\\PermissionDoesNotExist',
          'wildcardpermissioninvalidargument' => 'Spatie\\Permission\\Exceptions\\WildcardPermissionInvalidArgument',
          'wildcardpermissionnotimplementscontract' => 'Spatie\\Permission\\Exceptions\\WildcardPermissionNotImplementsContract',
          'guard' => 'Spatie\\Permission\\Guard',
          'permissionregistrar' => 'Spatie\\Permission\\PermissionRegistrar',
          'config' => 'Spatie\\Permission\\Support\\Config',
        ),
         'className' => 'App\\Models\\User',
         'functionName' => 'scopePermission',
         'templatePhpDocNodes' => 
        array (
        ),
         'parent' => NULL,
         'typeAliasesMap' => 
        array (
        ),
         'bypassTypeAliases' => false,
         'constUses' => 
        array (
        ),
         'typeAliasClassName' => 'Spatie\\Permission\\Traits\\HasPermissions',
         'traitData' => 
        array (
          0 => '/var/www/html/app/Models/User.php',
          1 => 'App\\Models\\User',
          2 => 'Spatie\\Permission\\Traits\\HasPermissions',
          3 => 'Spatie\\Permission\\Traits\\HasRoles',
          4 => NULL,
        ),
      )),
      '2a81cf3d401552a5cf40e8bfc6d36eaf' => 
      \PHPStan\Analyser\IntermediaryNameScope::__set_state(array(
         'namespace' => 'Spatie\\Permission\\Traits',
         'uses' => 
        array (
          'backedenum' => 'BackedEnum',
          'builder' => 'Illuminate\\Database\\Eloquent\\Builder',
          'belongstomany' => 'Illuminate\\Database\\Eloquent\\Relations\\BelongsToMany',
          'pivot' => 'Illuminate\\Database\\Eloquent\\Relations\\Pivot',
          'arr' => 'Illuminate\\Support\\Arr',
          'collection' => 'Illuminate\\Support\\Collection',
          'permission' => 'Spatie\\Permission\\Contracts\\Permission',
          'role' => 'Spatie\\Permission\\Contracts\\Role',
          'wildcard' => 'Spatie\\Permission\\Contracts\\Wildcard',
          'permissionattachedevent' => 'Spatie\\Permission\\Events\\PermissionAttachedEvent',
          'permissiondetachedevent' => 'Spatie\\Permission\\Events\\PermissionDetachedEvent',
          'guarddoesnotmatch' => 'Spatie\\Permission\\Exceptions\\GuardDoesNotMatch',
          'permissiondoesnotexist' => 'Spatie\\Permission\\Exceptions\\PermissionDoesNotExist',
          'wildcardpermissioninvalidargument' => 'Spatie\\Permission\\Exceptions\\WildcardPermissionInvalidArgument',
          'wildcardpermissionnotimplementscontract' => 'Spatie\\Permission\\Exceptions\\WildcardPermissionNotImplementsContract',
          'guard' => 'Spatie\\Permission\\Guard',
          'permissionregistrar' => 'Spatie\\Permission\\PermissionRegistrar',
          'config' => 'Spatie\\Permission\\Support\\Config',
        ),
         'className' => 'App\\Models\\User',
         'functionName' => 'scopeWithoutPermission',
         'templatePhpDocNodes' => 
        array (
        ),
         'parent' => NULL,
         'typeAliasesMap' => 
        array (
        ),
         'bypassTypeAliases' => false,
         'constUses' => 
        array (
        ),
         'typeAliasClassName' => 'Spatie\\Permission\\Traits\\HasPermissions',
         'traitData' => 
        array (
          0 => '/var/www/html/app/Models/User.php',
          1 => 'App\\Models\\User',
          2 => 'Spatie\\Permission\\Traits\\HasPermissions',
          3 => 'Spatie\\Permission\\Traits\\HasRoles',
          4 => NULL,
        ),
      )),
      'd7e1c7507ad94c92e532a042b4832ea0' => 
      \PHPStan\Analyser\IntermediaryNameScope::__set_state(array(
         'namespace' => 'Spatie\\Permission\\Traits',
         'uses' => 
        array (
          'backedenum' => 'BackedEnum',
          'builder' => 'Illuminate\\Database\\Eloquent\\Builder',
          'belongstomany' => 'Illuminate\\Database\\Eloquent\\Relations\\BelongsToMany',
          'pivot' => 'Illuminate\\Database\\Eloquent\\Relations\\Pivot',
          'arr' => 'Illuminate\\Support\\Arr',
          'collection' => 'Illuminate\\Support\\Collection',
          'permission' => 'Spatie\\Permission\\Contracts\\Permission',
          'role' => 'Spatie\\Permission\\Contracts\\Role',
          'wildcard' => 'Spatie\\Permission\\Contracts\\Wildcard',
          'permissionattachedevent' => 'Spatie\\Permission\\Events\\PermissionAttachedEvent',
          'permissiondetachedevent' => 'Spatie\\Permission\\Events\\PermissionDetachedEvent',
          'guarddoesnotmatch' => 'Spatie\\Permission\\Exceptions\\GuardDoesNotMatch',
          'permissiondoesnotexist' => 'Spatie\\Permission\\Exceptions\\PermissionDoesNotExist',
          'wildcardpermissioninvalidargument' => 'Spatie\\Permission\\Exceptions\\WildcardPermissionInvalidArgument',
          'wildcardpermissionnotimplementscontract' => 'Spatie\\Permission\\Exceptions\\WildcardPermissionNotImplementsContract',
          'guard' => 'Spatie\\Permission\\Guard',
          'permissionregistrar' => 'Spatie\\Permission\\PermissionRegistrar',
          'config' => 'Spatie\\Permission\\Support\\Config',
        ),
         'className' => 'App\\Models\\User',
         'functionName' => 'convertToPermissionModels',
         'templatePhpDocNodes' => 
        array (
        ),
         'parent' => NULL,
         'typeAliasesMap' => 
        array (
        ),
         'bypassTypeAliases' => false,
         'constUses' => 
        array (
        ),
         'typeAliasClassName' => 'Spatie\\Permission\\Traits\\HasPermissions',
         'traitData' => 
        array (
          0 => '/var/www/html/app/Models/User.php',
          1 => 'App\\Models\\User',
          2 => 'Spatie\\Permission\\Traits\\HasPermissions',
          3 => 'Spatie\\Permission\\Traits\\HasRoles',
          4 => NULL,
        ),
      )),
      '22c04ff48a2c8252c46fb0bae8af4774' => 
      \PHPStan\Analyser\IntermediaryNameScope::__set_state(array(
         'namespace' => 'Spatie\\Permission\\Traits',
         'uses' => 
        array (
          'backedenum' => 'BackedEnum',
          'builder' => 'Illuminate\\Database\\Eloquent\\Builder',
          'belongstomany' => 'Illuminate\\Database\\Eloquent\\Relations\\BelongsToMany',
          'pivot' => 'Illuminate\\Database\\Eloquent\\Relations\\Pivot',
          'arr' => 'Illuminate\\Support\\Arr',
          'collection' => 'Illuminate\\Support\\Collection',
          'permission' => 'Spatie\\Permission\\Contracts\\Permission',
          'role' => 'Spatie\\Permission\\Contracts\\Role',
          'wildcard' => 'Spatie\\Permission\\Contracts\\Wildcard',
          'permissionattachedevent' => 'Spatie\\Permission\\Events\\PermissionAttachedEvent',
          'permissiondetachedevent' => 'Spatie\\Permission\\Events\\PermissionDetachedEvent',
          'guarddoesnotmatch' => 'Spatie\\Permission\\Exceptions\\GuardDoesNotMatch',
          'permissiondoesnotexist' => 'Spatie\\Permission\\Exceptions\\PermissionDoesNotExist',
          'wildcardpermissioninvalidargument' => 'Spatie\\Permission\\Exceptions\\WildcardPermissionInvalidArgument',
          'wildcardpermissionnotimplementscontract' => 'Spatie\\Permission\\Exceptions\\WildcardPermissionNotImplementsContract',
          'guard' => 'Spatie\\Permission\\Guard',
          'permissionregistrar' => 'Spatie\\Permission\\PermissionRegistrar',
          'config' => 'Spatie\\Permission\\Support\\Config',
        ),
         'className' => 'App\\Models\\User',
         'functionName' => 'filterPermission',
         'templatePhpDocNodes' => 
        array (
        ),
         'parent' => NULL,
         'typeAliasesMap' => 
        array (
        ),
         'bypassTypeAliases' => false,
         'constUses' => 
        array (
        ),
         'typeAliasClassName' => 'Spatie\\Permission\\Traits\\HasPermissions',
         'traitData' => 
        array (
          0 => '/var/www/html/app/Models/User.php',
          1 => 'App\\Models\\User',
          2 => 'Spatie\\Permission\\Traits\\HasPermissions',
          3 => 'Spatie\\Permission\\Traits\\HasRoles',
          4 => NULL,
        ),
      )),
      '48693770e9fe710cb238e56422af130f' => 
      \PHPStan\Analyser\IntermediaryNameScope::__set_state(array(
         'namespace' => 'Spatie\\Permission\\Traits',
         'uses' => 
        array (
          'backedenum' => 'BackedEnum',
          'builder' => 'Illuminate\\Database\\Eloquent\\Builder',
          'belongstomany' => 'Illuminate\\Database\\Eloquent\\Relations\\BelongsToMany',
          'pivot' => 'Illuminate\\Database\\Eloquent\\Relations\\Pivot',
          'arr' => 'Illuminate\\Support\\Arr',
          'collection' => 'Illuminate\\Support\\Collection',
          'permission' => 'Spatie\\Permission\\Contracts\\Permission',
          'role' => 'Spatie\\Permission\\Contracts\\Role',
          'wildcard' => 'Spatie\\Permission\\Contracts\\Wildcard',
          'permissionattachedevent' => 'Spatie\\Permission\\Events\\PermissionAttachedEvent',
          'permissiondetachedevent' => 'Spatie\\Permission\\Events\\PermissionDetachedEvent',
          'guarddoesnotmatch' => 'Spatie\\Permission\\Exceptions\\GuardDoesNotMatch',
          'permissiondoesnotexist' => 'Spatie\\Permission\\Exceptions\\PermissionDoesNotExist',
          'wildcardpermissioninvalidargument' => 'Spatie\\Permission\\Exceptions\\WildcardPermissionInvalidArgument',
          'wildcardpermissionnotimplementscontract' => 'Spatie\\Permission\\Exceptions\\WildcardPermissionNotImplementsContract',
          'guard' => 'Spatie\\Permission\\Guard',
          'permissionregistrar' => 'Spatie\\Permission\\PermissionRegistrar',
          'config' => 'Spatie\\Permission\\Support\\Config',
        ),
         'className' => 'App\\Models\\User',
         'functionName' => 'hasPermissionTo',
         'templatePhpDocNodes' => 
        array (
        ),
         'parent' => NULL,
         'typeAliasesMap' => 
        array (
        ),
         'bypassTypeAliases' => false,
         'constUses' => 
        array (
        ),
         'typeAliasClassName' => 'Spatie\\Permission\\Traits\\HasPermissions',
         'traitData' => 
        array (
          0 => '/var/www/html/app/Models/User.php',
          1 => 'App\\Models\\User',
          2 => 'Spatie\\Permission\\Traits\\HasPermissions',
          3 => 'Spatie\\Permission\\Traits\\HasRoles',
          4 => NULL,
        ),
      )),
      'dcf8601e9f30c6758003ac32eadb3bdd' => 
      \PHPStan\Analyser\IntermediaryNameScope::__set_state(array(
         'namespace' => 'Spatie\\Permission\\Traits',
         'uses' => 
        array (
          'backedenum' => 'BackedEnum',
          'builder' => 'Illuminate\\Database\\Eloquent\\Builder',
          'belongstomany' => 'Illuminate\\Database\\Eloquent\\Relations\\BelongsToMany',
          'pivot' => 'Illuminate\\Database\\Eloquent\\Relations\\Pivot',
          'arr' => 'Illuminate\\Support\\Arr',
          'collection' => 'Illuminate\\Support\\Collection',
          'permission' => 'Spatie\\Permission\\Contracts\\Permission',
          'role' => 'Spatie\\Permission\\Contracts\\Role',
          'wildcard' => 'Spatie\\Permission\\Contracts\\Wildcard',
          'permissionattachedevent' => 'Spatie\\Permission\\Events\\PermissionAttachedEvent',
          'permissiondetachedevent' => 'Spatie\\Permission\\Events\\PermissionDetachedEvent',
          'guarddoesnotmatch' => 'Spatie\\Permission\\Exceptions\\GuardDoesNotMatch',
          'permissiondoesnotexist' => 'Spatie\\Permission\\Exceptions\\PermissionDoesNotExist',
          'wildcardpermissioninvalidargument' => 'Spatie\\Permission\\Exceptions\\WildcardPermissionInvalidArgument',
          'wildcardpermissionnotimplementscontract' => 'Spatie\\Permission\\Exceptions\\WildcardPermissionNotImplementsContract',
          'guard' => 'Spatie\\Permission\\Guard',
          'permissionregistrar' => 'Spatie\\Permission\\PermissionRegistrar',
          'config' => 'Spatie\\Permission\\Support\\Config',
        ),
         'className' => 'App\\Models\\User',
         'functionName' => 'hasWildcardPermission',
         'templatePhpDocNodes' => 
        array (
        ),
         'parent' => NULL,
         'typeAliasesMap' => 
        array (
        ),
         'bypassTypeAliases' => false,
         'constUses' => 
        array (
        ),
         'typeAliasClassName' => 'Spatie\\Permission\\Traits\\HasPermissions',
         'traitData' => 
        array (
          0 => '/var/www/html/app/Models/User.php',
          1 => 'App\\Models\\User',
          2 => 'Spatie\\Permission\\Traits\\HasPermissions',
          3 => 'Spatie\\Permission\\Traits\\HasRoles',
          4 => NULL,
        ),
      )),
      '415ba174c8a3688b45fcacdfb7d37eab' => 
      \PHPStan\Analyser\IntermediaryNameScope::__set_state(array(
         'namespace' => 'Spatie\\Permission\\Traits',
         'uses' => 
        array (
          'backedenum' => 'BackedEnum',
          'builder' => 'Illuminate\\Database\\Eloquent\\Builder',
          'belongstomany' => 'Illuminate\\Database\\Eloquent\\Relations\\BelongsToMany',
          'pivot' => 'Illuminate\\Database\\Eloquent\\Relations\\Pivot',
          'arr' => 'Illuminate\\Support\\Arr',
          'collection' => 'Illuminate\\Support\\Collection',
          'permission' => 'Spatie\\Permission\\Contracts\\Permission',
          'role' => 'Spatie\\Permission\\Contracts\\Role',
          'wildcard' => 'Spatie\\Permission\\Contracts\\Wildcard',
          'permissionattachedevent' => 'Spatie\\Permission\\Events\\PermissionAttachedEvent',
          'permissiondetachedevent' => 'Spatie\\Permission\\Events\\PermissionDetachedEvent',
          'guarddoesnotmatch' => 'Spatie\\Permission\\Exceptions\\GuardDoesNotMatch',
          'permissiondoesnotexist' => 'Spatie\\Permission\\Exceptions\\PermissionDoesNotExist',
          'wildcardpermissioninvalidargument' => 'Spatie\\Permission\\Exceptions\\WildcardPermissionInvalidArgument',
          'wildcardpermissionnotimplementscontract' => 'Spatie\\Permission\\Exceptions\\WildcardPermissionNotImplementsContract',
          'guard' => 'Spatie\\Permission\\Guard',
          'permissionregistrar' => 'Spatie\\Permission\\PermissionRegistrar',
          'config' => 'Spatie\\Permission\\Support\\Config',
        ),
         'className' => 'App\\Models\\User',
         'functionName' => 'checkPermissionTo',
         'templatePhpDocNodes' => 
        array (
        ),
         'parent' => NULL,
         'typeAliasesMap' => 
        array (
        ),
         'bypassTypeAliases' => false,
         'constUses' => 
        array (
        ),
         'typeAliasClassName' => 'Spatie\\Permission\\Traits\\HasPermissions',
         'traitData' => 
        array (
          0 => '/var/www/html/app/Models/User.php',
          1 => 'App\\Models\\User',
          2 => 'Spatie\\Permission\\Traits\\HasPermissions',
          3 => 'Spatie\\Permission\\Traits\\HasRoles',
          4 => NULL,
        ),
      )),
      'dae52d10b7ec756389726d707bf7d51e' => 
      \PHPStan\Analyser\IntermediaryNameScope::__set_state(array(
         'namespace' => 'Spatie\\Permission\\Traits',
         'uses' => 
        array (
          'backedenum' => 'BackedEnum',
          'builder' => 'Illuminate\\Database\\Eloquent\\Builder',
          'belongstomany' => 'Illuminate\\Database\\Eloquent\\Relations\\BelongsToMany',
          'pivot' => 'Illuminate\\Database\\Eloquent\\Relations\\Pivot',
          'arr' => 'Illuminate\\Support\\Arr',
          'collection' => 'Illuminate\\Support\\Collection',
          'permission' => 'Spatie\\Permission\\Contracts\\Permission',
          'role' => 'Spatie\\Permission\\Contracts\\Role',
          'wildcard' => 'Spatie\\Permission\\Contracts\\Wildcard',
          'permissionattachedevent' => 'Spatie\\Permission\\Events\\PermissionAttachedEvent',
          'permissiondetachedevent' => 'Spatie\\Permission\\Events\\PermissionDetachedEvent',
          'guarddoesnotmatch' => 'Spatie\\Permission\\Exceptions\\GuardDoesNotMatch',
          'permissiondoesnotexist' => 'Spatie\\Permission\\Exceptions\\PermissionDoesNotExist',
          'wildcardpermissioninvalidargument' => 'Spatie\\Permission\\Exceptions\\WildcardPermissionInvalidArgument',
          'wildcardpermissionnotimplementscontract' => 'Spatie\\Permission\\Exceptions\\WildcardPermissionNotImplementsContract',
          'guard' => 'Spatie\\Permission\\Guard',
          'permissionregistrar' => 'Spatie\\Permission\\PermissionRegistrar',
          'config' => 'Spatie\\Permission\\Support\\Config',
        ),
         'className' => 'App\\Models\\User',
         'functionName' => 'hasAnyPermission',
         'templatePhpDocNodes' => 
        array (
        ),
         'parent' => NULL,
         'typeAliasesMap' => 
        array (
        ),
         'bypassTypeAliases' => false,
         'constUses' => 
        array (
        ),
         'typeAliasClassName' => 'Spatie\\Permission\\Traits\\HasPermissions',
         'traitData' => 
        array (
          0 => '/var/www/html/app/Models/User.php',
          1 => 'App\\Models\\User',
          2 => 'Spatie\\Permission\\Traits\\HasPermissions',
          3 => 'Spatie\\Permission\\Traits\\HasRoles',
          4 => NULL,
        ),
      )),
      '0e0e0d72a5cc33a1c53e52d0c6b830bc' => 
      \PHPStan\Analyser\IntermediaryNameScope::__set_state(array(
         'namespace' => 'Spatie\\Permission\\Traits',
         'uses' => 
        array (
          'backedenum' => 'BackedEnum',
          'builder' => 'Illuminate\\Database\\Eloquent\\Builder',
          'belongstomany' => 'Illuminate\\Database\\Eloquent\\Relations\\BelongsToMany',
          'pivot' => 'Illuminate\\Database\\Eloquent\\Relations\\Pivot',
          'arr' => 'Illuminate\\Support\\Arr',
          'collection' => 'Illuminate\\Support\\Collection',
          'permission' => 'Spatie\\Permission\\Contracts\\Permission',
          'role' => 'Spatie\\Permission\\Contracts\\Role',
          'wildcard' => 'Spatie\\Permission\\Contracts\\Wildcard',
          'permissionattachedevent' => 'Spatie\\Permission\\Events\\PermissionAttachedEvent',
          'permissiondetachedevent' => 'Spatie\\Permission\\Events\\PermissionDetachedEvent',
          'guarddoesnotmatch' => 'Spatie\\Permission\\Exceptions\\GuardDoesNotMatch',
          'permissiondoesnotexist' => 'Spatie\\Permission\\Exceptions\\PermissionDoesNotExist',
          'wildcardpermissioninvalidargument' => 'Spatie\\Permission\\Exceptions\\WildcardPermissionInvalidArgument',
          'wildcardpermissionnotimplementscontract' => 'Spatie\\Permission\\Exceptions\\WildcardPermissionNotImplementsContract',
          'guard' => 'Spatie\\Permission\\Guard',
          'permissionregistrar' => 'Spatie\\Permission\\PermissionRegistrar',
          'config' => 'Spatie\\Permission\\Support\\Config',
        ),
         'className' => 'App\\Models\\User',
         'functionName' => 'hasAllPermissions',
         'templatePhpDocNodes' => 
        array (
        ),
         'parent' => NULL,
         'typeAliasesMap' => 
        array (
        ),
         'bypassTypeAliases' => false,
         'constUses' => 
        array (
        ),
         'typeAliasClassName' => 'Spatie\\Permission\\Traits\\HasPermissions',
         'traitData' => 
        array (
          0 => '/var/www/html/app/Models/User.php',
          1 => 'App\\Models\\User',
          2 => 'Spatie\\Permission\\Traits\\HasPermissions',
          3 => 'Spatie\\Permission\\Traits\\HasRoles',
          4 => NULL,
        ),
      )),
      '50d0af8a02f79974b3eacdd50e52e5c9' => 
      \PHPStan\Analyser\IntermediaryNameScope::__set_state(array(
         'namespace' => 'Spatie\\Permission\\Traits',
         'uses' => 
        array (
          'backedenum' => 'BackedEnum',
          'builder' => 'Illuminate\\Database\\Eloquent\\Builder',
          'belongstomany' => 'Illuminate\\Database\\Eloquent\\Relations\\BelongsToMany',
          'pivot' => 'Illuminate\\Database\\Eloquent\\Relations\\Pivot',
          'arr' => 'Illuminate\\Support\\Arr',
          'collection' => 'Illuminate\\Support\\Collection',
          'permission' => 'Spatie\\Permission\\Contracts\\Permission',
          'role' => 'Spatie\\Permission\\Contracts\\Role',
          'wildcard' => 'Spatie\\Permission\\Contracts\\Wildcard',
          'permissionattachedevent' => 'Spatie\\Permission\\Events\\PermissionAttachedEvent',
          'permissiondetachedevent' => 'Spatie\\Permission\\Events\\PermissionDetachedEvent',
          'guarddoesnotmatch' => 'Spatie\\Permission\\Exceptions\\GuardDoesNotMatch',
          'permissiondoesnotexist' => 'Spatie\\Permission\\Exceptions\\PermissionDoesNotExist',
          'wildcardpermissioninvalidargument' => 'Spatie\\Permission\\Exceptions\\WildcardPermissionInvalidArgument',
          'wildcardpermissionnotimplementscontract' => 'Spatie\\Permission\\Exceptions\\WildcardPermissionNotImplementsContract',
          'guard' => 'Spatie\\Permission\\Guard',
          'permissionregistrar' => 'Spatie\\Permission\\PermissionRegistrar',
          'config' => 'Spatie\\Permission\\Support\\Config',
        ),
         'className' => 'App\\Models\\User',
         'functionName' => 'hasPermissionViaRole',
         'templatePhpDocNodes' => 
        array (
        ),
         'parent' => NULL,
         'typeAliasesMap' => 
        array (
        ),
         'bypassTypeAliases' => false,
         'constUses' => 
        array (
        ),
         'typeAliasClassName' => 'Spatie\\Permission\\Traits\\HasPermissions',
         'traitData' => 
        array (
          0 => '/var/www/html/app/Models/User.php',
          1 => 'App\\Models\\User',
          2 => 'Spatie\\Permission\\Traits\\HasPermissions',
          3 => 'Spatie\\Permission\\Traits\\HasRoles',
          4 => NULL,
        ),
      )),
      '160edb2c6833c5d7bc563007bb5b1e36' => 
      \PHPStan\Analyser\IntermediaryNameScope::__set_state(array(
         'namespace' => 'Spatie\\Permission\\Traits',
         'uses' => 
        array (
          'backedenum' => 'BackedEnum',
          'builder' => 'Illuminate\\Database\\Eloquent\\Builder',
          'belongstomany' => 'Illuminate\\Database\\Eloquent\\Relations\\BelongsToMany',
          'pivot' => 'Illuminate\\Database\\Eloquent\\Relations\\Pivot',
          'arr' => 'Illuminate\\Support\\Arr',
          'collection' => 'Illuminate\\Support\\Collection',
          'permission' => 'Spatie\\Permission\\Contracts\\Permission',
          'role' => 'Spatie\\Permission\\Contracts\\Role',
          'wildcard' => 'Spatie\\Permission\\Contracts\\Wildcard',
          'permissionattachedevent' => 'Spatie\\Permission\\Events\\PermissionAttachedEvent',
          'permissiondetachedevent' => 'Spatie\\Permission\\Events\\PermissionDetachedEvent',
          'guarddoesnotmatch' => 'Spatie\\Permission\\Exceptions\\GuardDoesNotMatch',
          'permissiondoesnotexist' => 'Spatie\\Permission\\Exceptions\\PermissionDoesNotExist',
          'wildcardpermissioninvalidargument' => 'Spatie\\Permission\\Exceptions\\WildcardPermissionInvalidArgument',
          'wildcardpermissionnotimplementscontract' => 'Spatie\\Permission\\Exceptions\\WildcardPermissionNotImplementsContract',
          'guard' => 'Spatie\\Permission\\Guard',
          'permissionregistrar' => 'Spatie\\Permission\\PermissionRegistrar',
          'config' => 'Spatie\\Permission\\Support\\Config',
        ),
         'className' => 'App\\Models\\User',
         'functionName' => 'hasDirectPermission',
         'templatePhpDocNodes' => 
        array (
        ),
         'parent' => NULL,
         'typeAliasesMap' => 
        array (
        ),
         'bypassTypeAliases' => false,
         'constUses' => 
        array (
        ),
         'typeAliasClassName' => 'Spatie\\Permission\\Traits\\HasPermissions',
         'traitData' => 
        array (
          0 => '/var/www/html/app/Models/User.php',
          1 => 'App\\Models\\User',
          2 => 'Spatie\\Permission\\Traits\\HasPermissions',
          3 => 'Spatie\\Permission\\Traits\\HasRoles',
          4 => NULL,
        ),
      )),
      'b096a742bc0980f42e9478adfc9da31a' => 
      \PHPStan\Analyser\IntermediaryNameScope::__set_state(array(
         'namespace' => 'Spatie\\Permission\\Traits',
         'uses' => 
        array (
          'backedenum' => 'BackedEnum',
          'builder' => 'Illuminate\\Database\\Eloquent\\Builder',
          'belongstomany' => 'Illuminate\\Database\\Eloquent\\Relations\\BelongsToMany',
          'pivot' => 'Illuminate\\Database\\Eloquent\\Relations\\Pivot',
          'arr' => 'Illuminate\\Support\\Arr',
          'collection' => 'Illuminate\\Support\\Collection',
          'permission' => 'Spatie\\Permission\\Contracts\\Permission',
          'role' => 'Spatie\\Permission\\Contracts\\Role',
          'wildcard' => 'Spatie\\Permission\\Contracts\\Wildcard',
          'permissionattachedevent' => 'Spatie\\Permission\\Events\\PermissionAttachedEvent',
          'permissiondetachedevent' => 'Spatie\\Permission\\Events\\PermissionDetachedEvent',
          'guarddoesnotmatch' => 'Spatie\\Permission\\Exceptions\\GuardDoesNotMatch',
          'permissiondoesnotexist' => 'Spatie\\Permission\\Exceptions\\PermissionDoesNotExist',
          'wildcardpermissioninvalidargument' => 'Spatie\\Permission\\Exceptions\\WildcardPermissionInvalidArgument',
          'wildcardpermissionnotimplementscontract' => 'Spatie\\Permission\\Exceptions\\WildcardPermissionNotImplementsContract',
          'guard' => 'Spatie\\Permission\\Guard',
          'permissionregistrar' => 'Spatie\\Permission\\PermissionRegistrar',
          'config' => 'Spatie\\Permission\\Support\\Config',
        ),
         'className' => 'App\\Models\\User',
         'functionName' => 'getPermissionsViaRoles',
         'templatePhpDocNodes' => 
        array (
        ),
         'parent' => NULL,
         'typeAliasesMap' => 
        array (
        ),
         'bypassTypeAliases' => false,
         'constUses' => 
        array (
        ),
         'typeAliasClassName' => 'Spatie\\Permission\\Traits\\HasPermissions',
         'traitData' => 
        array (
          0 => '/var/www/html/app/Models/User.php',
          1 => 'App\\Models\\User',
          2 => 'Spatie\\Permission\\Traits\\HasPermissions',
          3 => 'Spatie\\Permission\\Traits\\HasRoles',
          4 => NULL,
        ),
      )),
      'c9e9556840abbd657bc3b1b0eb428cf4' => 
      \PHPStan\Analyser\IntermediaryNameScope::__set_state(array(
         'namespace' => 'Spatie\\Permission\\Traits',
         'uses' => 
        array (
          'backedenum' => 'BackedEnum',
          'builder' => 'Illuminate\\Database\\Eloquent\\Builder',
          'belongstomany' => 'Illuminate\\Database\\Eloquent\\Relations\\BelongsToMany',
          'pivot' => 'Illuminate\\Database\\Eloquent\\Relations\\Pivot',
          'arr' => 'Illuminate\\Support\\Arr',
          'collection' => 'Illuminate\\Support\\Collection',
          'permission' => 'Spatie\\Permission\\Contracts\\Permission',
          'role' => 'Spatie\\Permission\\Contracts\\Role',
          'wildcard' => 'Spatie\\Permission\\Contracts\\Wildcard',
          'permissionattachedevent' => 'Spatie\\Permission\\Events\\PermissionAttachedEvent',
          'permissiondetachedevent' => 'Spatie\\Permission\\Events\\PermissionDetachedEvent',
          'guarddoesnotmatch' => 'Spatie\\Permission\\Exceptions\\GuardDoesNotMatch',
          'permissiondoesnotexist' => 'Spatie\\Permission\\Exceptions\\PermissionDoesNotExist',
          'wildcardpermissioninvalidargument' => 'Spatie\\Permission\\Exceptions\\WildcardPermissionInvalidArgument',
          'wildcardpermissionnotimplementscontract' => 'Spatie\\Permission\\Exceptions\\WildcardPermissionNotImplementsContract',
          'guard' => 'Spatie\\Permission\\Guard',
          'permissionregistrar' => 'Spatie\\Permission\\PermissionRegistrar',
          'config' => 'Spatie\\Permission\\Support\\Config',
        ),
         'className' => 'App\\Models\\User',
         'functionName' => 'getAllPermissions',
         'templatePhpDocNodes' => 
        array (
        ),
         'parent' => NULL,
         'typeAliasesMap' => 
        array (
        ),
         'bypassTypeAliases' => false,
         'constUses' => 
        array (
        ),
         'typeAliasClassName' => 'Spatie\\Permission\\Traits\\HasPermissions',
         'traitData' => 
        array (
          0 => '/var/www/html/app/Models/User.php',
          1 => 'App\\Models\\User',
          2 => 'Spatie\\Permission\\Traits\\HasPermissions',
          3 => 'Spatie\\Permission\\Traits\\HasRoles',
          4 => NULL,
        ),
      )),
      '23c41449605d3b2cbd77466da83ca2d7' => 
      \PHPStan\Analyser\IntermediaryNameScope::__set_state(array(
         'namespace' => 'Spatie\\Permission\\Traits',
         'uses' => 
        array (
          'backedenum' => 'BackedEnum',
          'builder' => 'Illuminate\\Database\\Eloquent\\Builder',
          'belongstomany' => 'Illuminate\\Database\\Eloquent\\Relations\\BelongsToMany',
          'pivot' => 'Illuminate\\Database\\Eloquent\\Relations\\Pivot',
          'arr' => 'Illuminate\\Support\\Arr',
          'collection' => 'Illuminate\\Support\\Collection',
          'permission' => 'Spatie\\Permission\\Contracts\\Permission',
          'role' => 'Spatie\\Permission\\Contracts\\Role',
          'wildcard' => 'Spatie\\Permission\\Contracts\\Wildcard',
          'permissionattachedevent' => 'Spatie\\Permission\\Events\\PermissionAttachedEvent',
          'permissiondetachedevent' => 'Spatie\\Permission\\Events\\PermissionDetachedEvent',
          'guarddoesnotmatch' => 'Spatie\\Permission\\Exceptions\\GuardDoesNotMatch',
          'permissiondoesnotexist' => 'Spatie\\Permission\\Exceptions\\PermissionDoesNotExist',
          'wildcardpermissioninvalidargument' => 'Spatie\\Permission\\Exceptions\\WildcardPermissionInvalidArgument',
          'wildcardpermissionnotimplementscontract' => 'Spatie\\Permission\\Exceptions\\WildcardPermissionNotImplementsContract',
          'guard' => 'Spatie\\Permission\\Guard',
          'permissionregistrar' => 'Spatie\\Permission\\PermissionRegistrar',
          'config' => 'Spatie\\Permission\\Support\\Config',
        ),
         'className' => 'App\\Models\\User',
         'functionName' => 'collectPermissions',
         'templatePhpDocNodes' => 
        array (
        ),
         'parent' => NULL,
         'typeAliasesMap' => 
        array (
        ),
         'bypassTypeAliases' => false,
         'constUses' => 
        array (
        ),
         'typeAliasClassName' => 'Spatie\\Permission\\Traits\\HasPermissions',
         'traitData' => 
        array (
          0 => '/var/www/html/app/Models/User.php',
          1 => 'App\\Models\\User',
          2 => 'Spatie\\Permission\\Traits\\HasPermissions',
          3 => 'Spatie\\Permission\\Traits\\HasRoles',
          4 => NULL,
        ),
      )),
      '50ae426724f30546577e9c9802c44ba0' => 
      \PHPStan\Analyser\IntermediaryNameScope::__set_state(array(
         'namespace' => 'Spatie\\Permission\\Traits',
         'uses' => 
        array (
          'backedenum' => 'BackedEnum',
          'builder' => 'Illuminate\\Database\\Eloquent\\Builder',
          'belongstomany' => 'Illuminate\\Database\\Eloquent\\Relations\\BelongsToMany',
          'pivot' => 'Illuminate\\Database\\Eloquent\\Relations\\Pivot',
          'arr' => 'Illuminate\\Support\\Arr',
          'collection' => 'Illuminate\\Support\\Collection',
          'permission' => 'Spatie\\Permission\\Contracts\\Permission',
          'role' => 'Spatie\\Permission\\Contracts\\Role',
          'wildcard' => 'Spatie\\Permission\\Contracts\\Wildcard',
          'permissionattachedevent' => 'Spatie\\Permission\\Events\\PermissionAttachedEvent',
          'permissiondetachedevent' => 'Spatie\\Permission\\Events\\PermissionDetachedEvent',
          'guarddoesnotmatch' => 'Spatie\\Permission\\Exceptions\\GuardDoesNotMatch',
          'permissiondoesnotexist' => 'Spatie\\Permission\\Exceptions\\PermissionDoesNotExist',
          'wildcardpermissioninvalidargument' => 'Spatie\\Permission\\Exceptions\\WildcardPermissionInvalidArgument',
          'wildcardpermissionnotimplementscontract' => 'Spatie\\Permission\\Exceptions\\WildcardPermissionNotImplementsContract',
          'guard' => 'Spatie\\Permission\\Guard',
          'permissionregistrar' => 'Spatie\\Permission\\PermissionRegistrar',
          'config' => 'Spatie\\Permission\\Support\\Config',
        ),
         'className' => 'App\\Models\\User',
         'functionName' => 'detachPermissions',
         'templatePhpDocNodes' => 
        array (
        ),
         'parent' => NULL,
         'typeAliasesMap' => 
        array (
        ),
         'bypassTypeAliases' => false,
         'constUses' => 
        array (
        ),
         'typeAliasClassName' => 'Spatie\\Permission\\Traits\\HasPermissions',
         'traitData' => 
        array (
          0 => '/var/www/html/app/Models/User.php',
          1 => 'App\\Models\\User',
          2 => 'Spatie\\Permission\\Traits\\HasPermissions',
          3 => 'Spatie\\Permission\\Traits\\HasRoles',
          4 => NULL,
        ),
      )),
      '64cb6daca0a72e863818b266ae074d68' => 
      \PHPStan\Analyser\IntermediaryNameScope::__set_state(array(
         'namespace' => 'Spatie\\Permission\\Traits',
         'uses' => 
        array (
          'backedenum' => 'BackedEnum',
          'builder' => 'Illuminate\\Database\\Eloquent\\Builder',
          'belongstomany' => 'Illuminate\\Database\\Eloquent\\Relations\\BelongsToMany',
          'pivot' => 'Illuminate\\Database\\Eloquent\\Relations\\Pivot',
          'arr' => 'Illuminate\\Support\\Arr',
          'collection' => 'Illuminate\\Support\\Collection',
          'permission' => 'Spatie\\Permission\\Contracts\\Permission',
          'role' => 'Spatie\\Permission\\Contracts\\Role',
          'wildcard' => 'Spatie\\Permission\\Contracts\\Wildcard',
          'permissionattachedevent' => 'Spatie\\Permission\\Events\\PermissionAttachedEvent',
          'permissiondetachedevent' => 'Spatie\\Permission\\Events\\PermissionDetachedEvent',
          'guarddoesnotmatch' => 'Spatie\\Permission\\Exceptions\\GuardDoesNotMatch',
          'permissiondoesnotexist' => 'Spatie\\Permission\\Exceptions\\PermissionDoesNotExist',
          'wildcardpermissioninvalidargument' => 'Spatie\\Permission\\Exceptions\\WildcardPermissionInvalidArgument',
          'wildcardpermissionnotimplementscontract' => 'Spatie\\Permission\\Exceptions\\WildcardPermissionNotImplementsContract',
          'guard' => 'Spatie\\Permission\\Guard',
          'permissionregistrar' => 'Spatie\\Permission\\PermissionRegistrar',
          'config' => 'Spatie\\Permission\\Support\\Config',
        ),
         'className' => 'App\\Models\\User',
         'functionName' => 'givePermissionTo',
         'templatePhpDocNodes' => 
        array (
        ),
         'parent' => NULL,
         'typeAliasesMap' => 
        array (
        ),
         'bypassTypeAliases' => false,
         'constUses' => 
        array (
        ),
         'typeAliasClassName' => 'Spatie\\Permission\\Traits\\HasPermissions',
         'traitData' => 
        array (
          0 => '/var/www/html/app/Models/User.php',
          1 => 'App\\Models\\User',
          2 => 'Spatie\\Permission\\Traits\\HasPermissions',
          3 => 'Spatie\\Permission\\Traits\\HasRoles',
          4 => NULL,
        ),
      )),
      'bc480d4663dca450963da4359772fd15' => 
      \PHPStan\Analyser\IntermediaryNameScope::__set_state(array(
         'namespace' => 'Spatie\\Permission\\Traits',
         'uses' => 
        array (
          'backedenum' => 'BackedEnum',
          'builder' => 'Illuminate\\Database\\Eloquent\\Builder',
          'belongstomany' => 'Illuminate\\Database\\Eloquent\\Relations\\BelongsToMany',
          'pivot' => 'Illuminate\\Database\\Eloquent\\Relations\\Pivot',
          'arr' => 'Illuminate\\Support\\Arr',
          'collection' => 'Illuminate\\Support\\Collection',
          'permission' => 'Spatie\\Permission\\Contracts\\Permission',
          'role' => 'Spatie\\Permission\\Contracts\\Role',
          'wildcard' => 'Spatie\\Permission\\Contracts\\Wildcard',
          'permissionattachedevent' => 'Spatie\\Permission\\Events\\PermissionAttachedEvent',
          'permissiondetachedevent' => 'Spatie\\Permission\\Events\\PermissionDetachedEvent',
          'guarddoesnotmatch' => 'Spatie\\Permission\\Exceptions\\GuardDoesNotMatch',
          'permissiondoesnotexist' => 'Spatie\\Permission\\Exceptions\\PermissionDoesNotExist',
          'wildcardpermissioninvalidargument' => 'Spatie\\Permission\\Exceptions\\WildcardPermissionInvalidArgument',
          'wildcardpermissionnotimplementscontract' => 'Spatie\\Permission\\Exceptions\\WildcardPermissionNotImplementsContract',
          'guard' => 'Spatie\\Permission\\Guard',
          'permissionregistrar' => 'Spatie\\Permission\\PermissionRegistrar',
          'config' => 'Spatie\\Permission\\Support\\Config',
        ),
         'className' => 'App\\Models\\User',
         'functionName' => 'forgetWildcardPermissionIndex',
         'templatePhpDocNodes' => 
        array (
        ),
         'parent' => NULL,
         'typeAliasesMap' => 
        array (
        ),
         'bypassTypeAliases' => false,
         'constUses' => 
        array (
        ),
         'typeAliasClassName' => 'Spatie\\Permission\\Traits\\HasPermissions',
         'traitData' => 
        array (
          0 => '/var/www/html/app/Models/User.php',
          1 => 'App\\Models\\User',
          2 => 'Spatie\\Permission\\Traits\\HasPermissions',
          3 => 'Spatie\\Permission\\Traits\\HasRoles',
          4 => NULL,
        ),
      )),
      '7e934055e4a2df6ba5700a22a438ac21' => 
      \PHPStan\Analyser\IntermediaryNameScope::__set_state(array(
         'namespace' => 'Spatie\\Permission\\Traits',
         'uses' => 
        array (
          'backedenum' => 'BackedEnum',
          'builder' => 'Illuminate\\Database\\Eloquent\\Builder',
          'belongstomany' => 'Illuminate\\Database\\Eloquent\\Relations\\BelongsToMany',
          'pivot' => 'Illuminate\\Database\\Eloquent\\Relations\\Pivot',
          'arr' => 'Illuminate\\Support\\Arr',
          'collection' => 'Illuminate\\Support\\Collection',
          'permission' => 'Spatie\\Permission\\Contracts\\Permission',
          'role' => 'Spatie\\Permission\\Contracts\\Role',
          'wildcard' => 'Spatie\\Permission\\Contracts\\Wildcard',
          'permissionattachedevent' => 'Spatie\\Permission\\Events\\PermissionAttachedEvent',
          'permissiondetachedevent' => 'Spatie\\Permission\\Events\\PermissionDetachedEvent',
          'guarddoesnotmatch' => 'Spatie\\Permission\\Exceptions\\GuardDoesNotMatch',
          'permissiondoesnotexist' => 'Spatie\\Permission\\Exceptions\\PermissionDoesNotExist',
          'wildcardpermissioninvalidargument' => 'Spatie\\Permission\\Exceptions\\WildcardPermissionInvalidArgument',
          'wildcardpermissionnotimplementscontract' => 'Spatie\\Permission\\Exceptions\\WildcardPermissionNotImplementsContract',
          'guard' => 'Spatie\\Permission\\Guard',
          'permissionregistrar' => 'Spatie\\Permission\\PermissionRegistrar',
          'config' => 'Spatie\\Permission\\Support\\Config',
        ),
         'className' => 'App\\Models\\User',
         'functionName' => 'syncPermissions',
         'templatePhpDocNodes' => 
        array (
        ),
         'parent' => NULL,
         'typeAliasesMap' => 
        array (
        ),
         'bypassTypeAliases' => false,
         'constUses' => 
        array (
        ),
         'typeAliasClassName' => 'Spatie\\Permission\\Traits\\HasPermissions',
         'traitData' => 
        array (
          0 => '/var/www/html/app/Models/User.php',
          1 => 'App\\Models\\User',
          2 => 'Spatie\\Permission\\Traits\\HasPermissions',
          3 => 'Spatie\\Permission\\Traits\\HasRoles',
          4 => NULL,
        ),
      )),
      'd568397f0730440d8d01a8974e9421a8' => 
      \PHPStan\Analyser\IntermediaryNameScope::__set_state(array(
         'namespace' => 'Spatie\\Permission\\Traits',
         'uses' => 
        array (
          'backedenum' => 'BackedEnum',
          'builder' => 'Illuminate\\Database\\Eloquent\\Builder',
          'belongstomany' => 'Illuminate\\Database\\Eloquent\\Relations\\BelongsToMany',
          'pivot' => 'Illuminate\\Database\\Eloquent\\Relations\\Pivot',
          'arr' => 'Illuminate\\Support\\Arr',
          'collection' => 'Illuminate\\Support\\Collection',
          'permission' => 'Spatie\\Permission\\Contracts\\Permission',
          'role' => 'Spatie\\Permission\\Contracts\\Role',
          'wildcard' => 'Spatie\\Permission\\Contracts\\Wildcard',
          'permissionattachedevent' => 'Spatie\\Permission\\Events\\PermissionAttachedEvent',
          'permissiondetachedevent' => 'Spatie\\Permission\\Events\\PermissionDetachedEvent',
          'guarddoesnotmatch' => 'Spatie\\Permission\\Exceptions\\GuardDoesNotMatch',
          'permissiondoesnotexist' => 'Spatie\\Permission\\Exceptions\\PermissionDoesNotExist',
          'wildcardpermissioninvalidargument' => 'Spatie\\Permission\\Exceptions\\WildcardPermissionInvalidArgument',
          'wildcardpermissionnotimplementscontract' => 'Spatie\\Permission\\Exceptions\\WildcardPermissionNotImplementsContract',
          'guard' => 'Spatie\\Permission\\Guard',
          'permissionregistrar' => 'Spatie\\Permission\\PermissionRegistrar',
          'config' => 'Spatie\\Permission\\Support\\Config',
        ),
         'className' => 'App\\Models\\User',
         'functionName' => 'revokePermissionTo',
         'templatePhpDocNodes' => 
        array (
        ),
         'parent' => NULL,
         'typeAliasesMap' => 
        array (
        ),
         'bypassTypeAliases' => false,
         'constUses' => 
        array (
        ),
         'typeAliasClassName' => 'Spatie\\Permission\\Traits\\HasPermissions',
         'traitData' => 
        array (
          0 => '/var/www/html/app/Models/User.php',
          1 => 'App\\Models\\User',
          2 => 'Spatie\\Permission\\Traits\\HasPermissions',
          3 => 'Spatie\\Permission\\Traits\\HasRoles',
          4 => NULL,
        ),
      )),
      'cd645f053eff62ed982c8521f78be141' => 
      \PHPStan\Analyser\IntermediaryNameScope::__set_state(array(
         'namespace' => 'Spatie\\Permission\\Traits',
         'uses' => 
        array (
          'backedenum' => 'BackedEnum',
          'builder' => 'Illuminate\\Database\\Eloquent\\Builder',
          'belongstomany' => 'Illuminate\\Database\\Eloquent\\Relations\\BelongsToMany',
          'pivot' => 'Illuminate\\Database\\Eloquent\\Relations\\Pivot',
          'arr' => 'Illuminate\\Support\\Arr',
          'collection' => 'Illuminate\\Support\\Collection',
          'permission' => 'Spatie\\Permission\\Contracts\\Permission',
          'role' => 'Spatie\\Permission\\Contracts\\Role',
          'wildcard' => 'Spatie\\Permission\\Contracts\\Wildcard',
          'permissionattachedevent' => 'Spatie\\Permission\\Events\\PermissionAttachedEvent',
          'permissiondetachedevent' => 'Spatie\\Permission\\Events\\PermissionDetachedEvent',
          'guarddoesnotmatch' => 'Spatie\\Permission\\Exceptions\\GuardDoesNotMatch',
          'permissiondoesnotexist' => 'Spatie\\Permission\\Exceptions\\PermissionDoesNotExist',
          'wildcardpermissioninvalidargument' => 'Spatie\\Permission\\Exceptions\\WildcardPermissionInvalidArgument',
          'wildcardpermissionnotimplementscontract' => 'Spatie\\Permission\\Exceptions\\WildcardPermissionNotImplementsContract',
          'guard' => 'Spatie\\Permission\\Guard',
          'permissionregistrar' => 'Spatie\\Permission\\PermissionRegistrar',
          'config' => 'Spatie\\Permission\\Support\\Config',
        ),
         'className' => 'App\\Models\\User',
         'functionName' => 'getPermissionNames',
         'templatePhpDocNodes' => 
        array (
        ),
         'parent' => NULL,
         'typeAliasesMap' => 
        array (
        ),
         'bypassTypeAliases' => false,
         'constUses' => 
        array (
        ),
         'typeAliasClassName' => 'Spatie\\Permission\\Traits\\HasPermissions',
         'traitData' => 
        array (
          0 => '/var/www/html/app/Models/User.php',
          1 => 'App\\Models\\User',
          2 => 'Spatie\\Permission\\Traits\\HasPermissions',
          3 => 'Spatie\\Permission\\Traits\\HasRoles',
          4 => NULL,
        ),
      )),
      '9b217d27e5239e78e94a7b61d5efc516' => 
      \PHPStan\Analyser\IntermediaryNameScope::__set_state(array(
         'namespace' => 'Spatie\\Permission\\Traits',
         'uses' => 
        array (
          'backedenum' => 'BackedEnum',
          'builder' => 'Illuminate\\Database\\Eloquent\\Builder',
          'belongstomany' => 'Illuminate\\Database\\Eloquent\\Relations\\BelongsToMany',
          'pivot' => 'Illuminate\\Database\\Eloquent\\Relations\\Pivot',
          'arr' => 'Illuminate\\Support\\Arr',
          'collection' => 'Illuminate\\Support\\Collection',
          'permission' => 'Spatie\\Permission\\Contracts\\Permission',
          'role' => 'Spatie\\Permission\\Contracts\\Role',
          'wildcard' => 'Spatie\\Permission\\Contracts\\Wildcard',
          'permissionattachedevent' => 'Spatie\\Permission\\Events\\PermissionAttachedEvent',
          'permissiondetachedevent' => 'Spatie\\Permission\\Events\\PermissionDetachedEvent',
          'guarddoesnotmatch' => 'Spatie\\Permission\\Exceptions\\GuardDoesNotMatch',
          'permissiondoesnotexist' => 'Spatie\\Permission\\Exceptions\\PermissionDoesNotExist',
          'wildcardpermissioninvalidargument' => 'Spatie\\Permission\\Exceptions\\WildcardPermissionInvalidArgument',
          'wildcardpermissionnotimplementscontract' => 'Spatie\\Permission\\Exceptions\\WildcardPermissionNotImplementsContract',
          'guard' => 'Spatie\\Permission\\Guard',
          'permissionregistrar' => 'Spatie\\Permission\\PermissionRegistrar',
          'config' => 'Spatie\\Permission\\Support\\Config',
        ),
         'className' => 'App\\Models\\User',
         'functionName' => 'getStoredPermission',
         'templatePhpDocNodes' => 
        array (
        ),
         'parent' => NULL,
         'typeAliasesMap' => 
        array (
        ),
         'bypassTypeAliases' => false,
         'constUses' => 
        array (
        ),
         'typeAliasClassName' => 'Spatie\\Permission\\Traits\\HasPermissions',
         'traitData' => 
        array (
          0 => '/var/www/html/app/Models/User.php',
          1 => 'App\\Models\\User',
          2 => 'Spatie\\Permission\\Traits\\HasPermissions',
          3 => 'Spatie\\Permission\\Traits\\HasRoles',
          4 => NULL,
        ),
      )),
      '47e35c93810e7cf3b318a7d0d275c684' => 
      \PHPStan\Analyser\IntermediaryNameScope::__set_state(array(
         'namespace' => 'Spatie\\Permission\\Traits',
         'uses' => 
        array (
          'backedenum' => 'BackedEnum',
          'builder' => 'Illuminate\\Database\\Eloquent\\Builder',
          'belongstomany' => 'Illuminate\\Database\\Eloquent\\Relations\\BelongsToMany',
          'pivot' => 'Illuminate\\Database\\Eloquent\\Relations\\Pivot',
          'arr' => 'Illuminate\\Support\\Arr',
          'collection' => 'Illuminate\\Support\\Collection',
          'permission' => 'Spatie\\Permission\\Contracts\\Permission',
          'role' => 'Spatie\\Permission\\Contracts\\Role',
          'wildcard' => 'Spatie\\Permission\\Contracts\\Wildcard',
          'permissionattachedevent' => 'Spatie\\Permission\\Events\\PermissionAttachedEvent',
          'permissiondetachedevent' => 'Spatie\\Permission\\Events\\PermissionDetachedEvent',
          'guarddoesnotmatch' => 'Spatie\\Permission\\Exceptions\\GuardDoesNotMatch',
          'permissiondoesnotexist' => 'Spatie\\Permission\\Exceptions\\PermissionDoesNotExist',
          'wildcardpermissioninvalidargument' => 'Spatie\\Permission\\Exceptions\\WildcardPermissionInvalidArgument',
          'wildcardpermissionnotimplementscontract' => 'Spatie\\Permission\\Exceptions\\WildcardPermissionNotImplementsContract',
          'guard' => 'Spatie\\Permission\\Guard',
          'permissionregistrar' => 'Spatie\\Permission\\PermissionRegistrar',
          'config' => 'Spatie\\Permission\\Support\\Config',
        ),
         'className' => 'App\\Models\\User',
         'functionName' => 'ensureModelSharesGuard',
         'templatePhpDocNodes' => 
        array (
        ),
         'parent' => NULL,
         'typeAliasesMap' => 
        array (
        ),
         'bypassTypeAliases' => false,
         'constUses' => 
        array (
        ),
         'typeAliasClassName' => 'Spatie\\Permission\\Traits\\HasPermissions',
         'traitData' => 
        array (
          0 => '/var/www/html/app/Models/User.php',
          1 => 'App\\Models\\User',
          2 => 'Spatie\\Permission\\Traits\\HasPermissions',
          3 => 'Spatie\\Permission\\Traits\\HasRoles',
          4 => NULL,
        ),
      )),
      'f140bcada0c1b473667ef0a941b5960b' => 
      \PHPStan\Analyser\IntermediaryNameScope::__set_state(array(
         'namespace' => 'Spatie\\Permission\\Traits',
         'uses' => 
        array (
          'backedenum' => 'BackedEnum',
          'builder' => 'Illuminate\\Database\\Eloquent\\Builder',
          'belongstomany' => 'Illuminate\\Database\\Eloquent\\Relations\\BelongsToMany',
          'pivot' => 'Illuminate\\Database\\Eloquent\\Relations\\Pivot',
          'arr' => 'Illuminate\\Support\\Arr',
          'collection' => 'Illuminate\\Support\\Collection',
          'permission' => 'Spatie\\Permission\\Contracts\\Permission',
          'role' => 'Spatie\\Permission\\Contracts\\Role',
          'wildcard' => 'Spatie\\Permission\\Contracts\\Wildcard',
          'permissionattachedevent' => 'Spatie\\Permission\\Events\\PermissionAttachedEvent',
          'permissiondetachedevent' => 'Spatie\\Permission\\Events\\PermissionDetachedEvent',
          'guarddoesnotmatch' => 'Spatie\\Permission\\Exceptions\\GuardDoesNotMatch',
          'permissiondoesnotexist' => 'Spatie\\Permission\\Exceptions\\PermissionDoesNotExist',
          'wildcardpermissioninvalidargument' => 'Spatie\\Permission\\Exceptions\\WildcardPermissionInvalidArgument',
          'wildcardpermissionnotimplementscontract' => 'Spatie\\Permission\\Exceptions\\WildcardPermissionNotImplementsContract',
          'guard' => 'Spatie\\Permission\\Guard',
          'permissionregistrar' => 'Spatie\\Permission\\PermissionRegistrar',
          'config' => 'Spatie\\Permission\\Support\\Config',
        ),
         'className' => 'App\\Models\\User',
         'functionName' => 'getGuardNames',
         'templatePhpDocNodes' => 
        array (
        ),
         'parent' => NULL,
         'typeAliasesMap' => 
        array (
        ),
         'bypassTypeAliases' => false,
         'constUses' => 
        array (
        ),
         'typeAliasClassName' => 'Spatie\\Permission\\Traits\\HasPermissions',
         'traitData' => 
        array (
          0 => '/var/www/html/app/Models/User.php',
          1 => 'App\\Models\\User',
          2 => 'Spatie\\Permission\\Traits\\HasPermissions',
          3 => 'Spatie\\Permission\\Traits\\HasRoles',
          4 => NULL,
        ),
      )),
      '5de3cc6ad452d2bb17d3717fd883ccb4' => 
      \PHPStan\Analyser\IntermediaryNameScope::__set_state(array(
         'namespace' => 'Spatie\\Permission\\Traits',
         'uses' => 
        array (
          'backedenum' => 'BackedEnum',
          'builder' => 'Illuminate\\Database\\Eloquent\\Builder',
          'belongstomany' => 'Illuminate\\Database\\Eloquent\\Relations\\BelongsToMany',
          'pivot' => 'Illuminate\\Database\\Eloquent\\Relations\\Pivot',
          'arr' => 'Illuminate\\Support\\Arr',
          'collection' => 'Illuminate\\Support\\Collection',
          'permission' => 'Spatie\\Permission\\Contracts\\Permission',
          'role' => 'Spatie\\Permission\\Contracts\\Role',
          'wildcard' => 'Spatie\\Permission\\Contracts\\Wildcard',
          'permissionattachedevent' => 'Spatie\\Permission\\Events\\PermissionAttachedEvent',
          'permissiondetachedevent' => 'Spatie\\Permission\\Events\\PermissionDetachedEvent',
          'guarddoesnotmatch' => 'Spatie\\Permission\\Exceptions\\GuardDoesNotMatch',
          'permissiondoesnotexist' => 'Spatie\\Permission\\Exceptions\\PermissionDoesNotExist',
          'wildcardpermissioninvalidargument' => 'Spatie\\Permission\\Exceptions\\WildcardPermissionInvalidArgument',
          'wildcardpermissionnotimplementscontract' => 'Spatie\\Permission\\Exceptions\\WildcardPermissionNotImplementsContract',
          'guard' => 'Spatie\\Permission\\Guard',
          'permissionregistrar' => 'Spatie\\Permission\\PermissionRegistrar',
          'config' => 'Spatie\\Permission\\Support\\Config',
        ),
         'className' => 'App\\Models\\User',
         'functionName' => 'getDefaultGuardName',
         'templatePhpDocNodes' => 
        array (
        ),
         'parent' => NULL,
         'typeAliasesMap' => 
        array (
        ),
         'bypassTypeAliases' => false,
         'constUses' => 
        array (
        ),
         'typeAliasClassName' => 'Spatie\\Permission\\Traits\\HasPermissions',
         'traitData' => 
        array (
          0 => '/var/www/html/app/Models/User.php',
          1 => 'App\\Models\\User',
          2 => 'Spatie\\Permission\\Traits\\HasPermissions',
          3 => 'Spatie\\Permission\\Traits\\HasRoles',
          4 => NULL,
        ),
      )),
      '0f3bf64b2b0aaba63da321b1414b3964' => 
      \PHPStan\Analyser\IntermediaryNameScope::__set_state(array(
         'namespace' => 'Spatie\\Permission\\Traits',
         'uses' => 
        array (
          'backedenum' => 'BackedEnum',
          'builder' => 'Illuminate\\Database\\Eloquent\\Builder',
          'belongstomany' => 'Illuminate\\Database\\Eloquent\\Relations\\BelongsToMany',
          'pivot' => 'Illuminate\\Database\\Eloquent\\Relations\\Pivot',
          'arr' => 'Illuminate\\Support\\Arr',
          'collection' => 'Illuminate\\Support\\Collection',
          'permission' => 'Spatie\\Permission\\Contracts\\Permission',
          'role' => 'Spatie\\Permission\\Contracts\\Role',
          'wildcard' => 'Spatie\\Permission\\Contracts\\Wildcard',
          'permissionattachedevent' => 'Spatie\\Permission\\Events\\PermissionAttachedEvent',
          'permissiondetachedevent' => 'Spatie\\Permission\\Events\\PermissionDetachedEvent',
          'guarddoesnotmatch' => 'Spatie\\Permission\\Exceptions\\GuardDoesNotMatch',
          'permissiondoesnotexist' => 'Spatie\\Permission\\Exceptions\\PermissionDoesNotExist',
          'wildcardpermissioninvalidargument' => 'Spatie\\Permission\\Exceptions\\WildcardPermissionInvalidArgument',
          'wildcardpermissionnotimplementscontract' => 'Spatie\\Permission\\Exceptions\\WildcardPermissionNotImplementsContract',
          'guard' => 'Spatie\\Permission\\Guard',
          'permissionregistrar' => 'Spatie\\Permission\\PermissionRegistrar',
          'config' => 'Spatie\\Permission\\Support\\Config',
        ),
         'className' => 'App\\Models\\User',
         'functionName' => 'forgetCachedPermissions',
         'templatePhpDocNodes' => 
        array (
        ),
         'parent' => NULL,
         'typeAliasesMap' => 
        array (
        ),
         'bypassTypeAliases' => false,
         'constUses' => 
        array (
        ),
         'typeAliasClassName' => 'Spatie\\Permission\\Traits\\HasPermissions',
         'traitData' => 
        array (
          0 => '/var/www/html/app/Models/User.php',
          1 => 'App\\Models\\User',
          2 => 'Spatie\\Permission\\Traits\\HasPermissions',
          3 => 'Spatie\\Permission\\Traits\\HasRoles',
          4 => NULL,
        ),
      )),
      '00ccaf5a06860ea68ca4b119786906c9' => 
      \PHPStan\Analyser\IntermediaryNameScope::__set_state(array(
         'namespace' => 'Spatie\\Permission\\Traits',
         'uses' => 
        array (
          'backedenum' => 'BackedEnum',
          'builder' => 'Illuminate\\Database\\Eloquent\\Builder',
          'belongstomany' => 'Illuminate\\Database\\Eloquent\\Relations\\BelongsToMany',
          'pivot' => 'Illuminate\\Database\\Eloquent\\Relations\\Pivot',
          'arr' => 'Illuminate\\Support\\Arr',
          'collection' => 'Illuminate\\Support\\Collection',
          'permission' => 'Spatie\\Permission\\Contracts\\Permission',
          'role' => 'Spatie\\Permission\\Contracts\\Role',
          'wildcard' => 'Spatie\\Permission\\Contracts\\Wildcard',
          'permissionattachedevent' => 'Spatie\\Permission\\Events\\PermissionAttachedEvent',
          'permissiondetachedevent' => 'Spatie\\Permission\\Events\\PermissionDetachedEvent',
          'guarddoesnotmatch' => 'Spatie\\Permission\\Exceptions\\GuardDoesNotMatch',
          'permissiondoesnotexist' => 'Spatie\\Permission\\Exceptions\\PermissionDoesNotExist',
          'wildcardpermissioninvalidargument' => 'Spatie\\Permission\\Exceptions\\WildcardPermissionInvalidArgument',
          'wildcardpermissionnotimplementscontract' => 'Spatie\\Permission\\Exceptions\\WildcardPermissionNotImplementsContract',
          'guard' => 'Spatie\\Permission\\Guard',
          'permissionregistrar' => 'Spatie\\Permission\\PermissionRegistrar',
          'config' => 'Spatie\\Permission\\Support\\Config',
        ),
         'className' => 'App\\Models\\User',
         'functionName' => 'hasAllDirectPermissions',
         'templatePhpDocNodes' => 
        array (
        ),
         'parent' => NULL,
         'typeAliasesMap' => 
        array (
        ),
         'bypassTypeAliases' => false,
         'constUses' => 
        array (
        ),
         'typeAliasClassName' => 'Spatie\\Permission\\Traits\\HasPermissions',
         'traitData' => 
        array (
          0 => '/var/www/html/app/Models/User.php',
          1 => 'App\\Models\\User',
          2 => 'Spatie\\Permission\\Traits\\HasPermissions',
          3 => 'Spatie\\Permission\\Traits\\HasRoles',
          4 => NULL,
        ),
      )),
      'cc69cc0c22e23be4c2959cd0ec73b8e0' => 
      \PHPStan\Analyser\IntermediaryNameScope::__set_state(array(
         'namespace' => 'Spatie\\Permission\\Traits',
         'uses' => 
        array (
          'backedenum' => 'BackedEnum',
          'builder' => 'Illuminate\\Database\\Eloquent\\Builder',
          'belongstomany' => 'Illuminate\\Database\\Eloquent\\Relations\\BelongsToMany',
          'pivot' => 'Illuminate\\Database\\Eloquent\\Relations\\Pivot',
          'arr' => 'Illuminate\\Support\\Arr',
          'collection' => 'Illuminate\\Support\\Collection',
          'permission' => 'Spatie\\Permission\\Contracts\\Permission',
          'role' => 'Spatie\\Permission\\Contracts\\Role',
          'wildcard' => 'Spatie\\Permission\\Contracts\\Wildcard',
          'permissionattachedevent' => 'Spatie\\Permission\\Events\\PermissionAttachedEvent',
          'permissiondetachedevent' => 'Spatie\\Permission\\Events\\PermissionDetachedEvent',
          'guarddoesnotmatch' => 'Spatie\\Permission\\Exceptions\\GuardDoesNotMatch',
          'permissiondoesnotexist' => 'Spatie\\Permission\\Exceptions\\PermissionDoesNotExist',
          'wildcardpermissioninvalidargument' => 'Spatie\\Permission\\Exceptions\\WildcardPermissionInvalidArgument',
          'wildcardpermissionnotimplementscontract' => 'Spatie\\Permission\\Exceptions\\WildcardPermissionNotImplementsContract',
          'guard' => 'Spatie\\Permission\\Guard',
          'permissionregistrar' => 'Spatie\\Permission\\PermissionRegistrar',
          'config' => 'Spatie\\Permission\\Support\\Config',
        ),
         'className' => 'App\\Models\\User',
         'functionName' => 'hasAnyDirectPermission',
         'templatePhpDocNodes' => 
        array (
        ),
         'parent' => NULL,
         'typeAliasesMap' => 
        array (
        ),
         'bypassTypeAliases' => false,
         'constUses' => 
        array (
        ),
         'typeAliasClassName' => 'Spatie\\Permission\\Traits\\HasPermissions',
         'traitData' => 
        array (
          0 => '/var/www/html/app/Models/User.php',
          1 => 'App\\Models\\User',
          2 => 'Spatie\\Permission\\Traits\\HasPermissions',
          3 => 'Spatie\\Permission\\Traits\\HasRoles',
          4 => NULL,
        ),
      )),
      '9c00ad8bd3b2fd12be96cc723386d479' => 
      \PHPStan\Analyser\IntermediaryNameScope::__set_state(array(
         'namespace' => 'Spatie\\Permission\\Traits',
         'uses' => 
        array (
          'backedenum' => 'BackedEnum',
          'builder' => 'Illuminate\\Database\\Eloquent\\Builder',
          'model' => 'Illuminate\\Database\\Eloquent\\Model',
          'belongstomany' => 'Illuminate\\Database\\Eloquent\\Relations\\BelongsToMany',
          'pivot' => 'Illuminate\\Database\\Eloquent\\Relations\\Pivot',
          'arr' => 'Illuminate\\Support\\Arr',
          'collection' => 'Illuminate\\Support\\Collection',
          'permission' => 'Spatie\\Permission\\Contracts\\Permission',
          'role' => 'Spatie\\Permission\\Contracts\\Role',
          'roleattachedevent' => 'Spatie\\Permission\\Events\\RoleAttachedEvent',
          'roledetachedevent' => 'Spatie\\Permission\\Events\\RoleDetachedEvent',
          'permissionregistrar' => 'Spatie\\Permission\\PermissionRegistrar',
          'config' => 'Spatie\\Permission\\Support\\Config',
          'typeerror' => 'TypeError',
        ),
         'className' => 'App\\Models\\User',
         'functionName' => 'bootHasRoles',
         'templatePhpDocNodes' => 
        array (
        ),
         'parent' => NULL,
         'typeAliasesMap' => 
        array (
        ),
         'bypassTypeAliases' => false,
         'constUses' => 
        array (
        ),
         'typeAliasClassName' => 'Spatie\\Permission\\Traits\\HasRoles',
         'traitData' => 
        array (
          0 => '/var/www/html/app/Models/User.php',
          1 => 'App\\Models\\User',
          2 => 'Spatie\\Permission\\Traits\\HasRoles',
          3 => NULL,
          4 => NULL,
        ),
      )),
      '96e082fe3d747d1f898b227aeed387b4' => 
      \PHPStan\Analyser\IntermediaryNameScope::__set_state(array(
         'namespace' => 'Spatie\\Permission\\Traits',
         'uses' => 
        array (
          'backedenum' => 'BackedEnum',
          'builder' => 'Illuminate\\Database\\Eloquent\\Builder',
          'model' => 'Illuminate\\Database\\Eloquent\\Model',
          'belongstomany' => 'Illuminate\\Database\\Eloquent\\Relations\\BelongsToMany',
          'pivot' => 'Illuminate\\Database\\Eloquent\\Relations\\Pivot',
          'arr' => 'Illuminate\\Support\\Arr',
          'collection' => 'Illuminate\\Support\\Collection',
          'permission' => 'Spatie\\Permission\\Contracts\\Permission',
          'role' => 'Spatie\\Permission\\Contracts\\Role',
          'roleattachedevent' => 'Spatie\\Permission\\Events\\RoleAttachedEvent',
          'roledetachedevent' => 'Spatie\\Permission\\Events\\RoleDetachedEvent',
          'permissionregistrar' => 'Spatie\\Permission\\PermissionRegistrar',
          'config' => 'Spatie\\Permission\\Support\\Config',
          'typeerror' => 'TypeError',
        ),
         'className' => 'App\\Models\\User',
         'functionName' => 'getRoleClass',
         'templatePhpDocNodes' => 
        array (
        ),
         'parent' => NULL,
         'typeAliasesMap' => 
        array (
        ),
         'bypassTypeAliases' => false,
         'constUses' => 
        array (
        ),
         'typeAliasClassName' => 'Spatie\\Permission\\Traits\\HasRoles',
         'traitData' => 
        array (
          0 => '/var/www/html/app/Models/User.php',
          1 => 'App\\Models\\User',
          2 => 'Spatie\\Permission\\Traits\\HasRoles',
          3 => NULL,
          4 => NULL,
        ),
      )),
      '6795640718aec9af816dc1412a9011cf' => 
      \PHPStan\Analyser\IntermediaryNameScope::__set_state(array(
         'namespace' => 'Spatie\\Permission\\Traits',
         'uses' => 
        array (
          'backedenum' => 'BackedEnum',
          'builder' => 'Illuminate\\Database\\Eloquent\\Builder',
          'model' => 'Illuminate\\Database\\Eloquent\\Model',
          'belongstomany' => 'Illuminate\\Database\\Eloquent\\Relations\\BelongsToMany',
          'pivot' => 'Illuminate\\Database\\Eloquent\\Relations\\Pivot',
          'arr' => 'Illuminate\\Support\\Arr',
          'collection' => 'Illuminate\\Support\\Collection',
          'permission' => 'Spatie\\Permission\\Contracts\\Permission',
          'role' => 'Spatie\\Permission\\Contracts\\Role',
          'roleattachedevent' => 'Spatie\\Permission\\Events\\RoleAttachedEvent',
          'roledetachedevent' => 'Spatie\\Permission\\Events\\RoleDetachedEvent',
          'permissionregistrar' => 'Spatie\\Permission\\PermissionRegistrar',
          'config' => 'Spatie\\Permission\\Support\\Config',
          'typeerror' => 'TypeError',
        ),
         'className' => 'App\\Models\\User',
         'functionName' => 'roles',
         'templatePhpDocNodes' => 
        array (
        ),
         'parent' => NULL,
         'typeAliasesMap' => 
        array (
        ),
         'bypassTypeAliases' => false,
         'constUses' => 
        array (
        ),
         'typeAliasClassName' => 'Spatie\\Permission\\Traits\\HasRoles',
         'traitData' => 
        array (
          0 => '/var/www/html/app/Models/User.php',
          1 => 'App\\Models\\User',
          2 => 'Spatie\\Permission\\Traits\\HasRoles',
          3 => NULL,
          4 => NULL,
        ),
      )),
      '64a6a7f8cdd570ade56cbee4de61fe8d' => 
      \PHPStan\Analyser\IntermediaryNameScope::__set_state(array(
         'namespace' => 'Spatie\\Permission\\Traits',
         'uses' => 
        array (
          'backedenum' => 'BackedEnum',
          'builder' => 'Illuminate\\Database\\Eloquent\\Builder',
          'model' => 'Illuminate\\Database\\Eloquent\\Model',
          'belongstomany' => 'Illuminate\\Database\\Eloquent\\Relations\\BelongsToMany',
          'pivot' => 'Illuminate\\Database\\Eloquent\\Relations\\Pivot',
          'arr' => 'Illuminate\\Support\\Arr',
          'collection' => 'Illuminate\\Support\\Collection',
          'permission' => 'Spatie\\Permission\\Contracts\\Permission',
          'role' => 'Spatie\\Permission\\Contracts\\Role',
          'roleattachedevent' => 'Spatie\\Permission\\Events\\RoleAttachedEvent',
          'roledetachedevent' => 'Spatie\\Permission\\Events\\RoleDetachedEvent',
          'permissionregistrar' => 'Spatie\\Permission\\PermissionRegistrar',
          'config' => 'Spatie\\Permission\\Support\\Config',
          'typeerror' => 'TypeError',
        ),
         'className' => 'App\\Models\\User',
         'functionName' => 'scopeRole',
         'templatePhpDocNodes' => 
        array (
        ),
         'parent' => NULL,
         'typeAliasesMap' => 
        array (
        ),
         'bypassTypeAliases' => false,
         'constUses' => 
        array (
        ),
         'typeAliasClassName' => 'Spatie\\Permission\\Traits\\HasRoles',
         'traitData' => 
        array (
          0 => '/var/www/html/app/Models/User.php',
          1 => 'App\\Models\\User',
          2 => 'Spatie\\Permission\\Traits\\HasRoles',
          3 => NULL,
          4 => NULL,
        ),
      )),
      '7d90327954777fe1d17471e3731bf376' => 
      \PHPStan\Analyser\IntermediaryNameScope::__set_state(array(
         'namespace' => 'Spatie\\Permission\\Traits',
         'uses' => 
        array (
          'backedenum' => 'BackedEnum',
          'builder' => 'Illuminate\\Database\\Eloquent\\Builder',
          'model' => 'Illuminate\\Database\\Eloquent\\Model',
          'belongstomany' => 'Illuminate\\Database\\Eloquent\\Relations\\BelongsToMany',
          'pivot' => 'Illuminate\\Database\\Eloquent\\Relations\\Pivot',
          'arr' => 'Illuminate\\Support\\Arr',
          'collection' => 'Illuminate\\Support\\Collection',
          'permission' => 'Spatie\\Permission\\Contracts\\Permission',
          'role' => 'Spatie\\Permission\\Contracts\\Role',
          'roleattachedevent' => 'Spatie\\Permission\\Events\\RoleAttachedEvent',
          'roledetachedevent' => 'Spatie\\Permission\\Events\\RoleDetachedEvent',
          'permissionregistrar' => 'Spatie\\Permission\\PermissionRegistrar',
          'config' => 'Spatie\\Permission\\Support\\Config',
          'typeerror' => 'TypeError',
        ),
         'className' => 'App\\Models\\User',
         'functionName' => 'scopeWithoutRole',
         'templatePhpDocNodes' => 
        array (
        ),
         'parent' => NULL,
         'typeAliasesMap' => 
        array (
        ),
         'bypassTypeAliases' => false,
         'constUses' => 
        array (
        ),
         'typeAliasClassName' => 'Spatie\\Permission\\Traits\\HasRoles',
         'traitData' => 
        array (
          0 => '/var/www/html/app/Models/User.php',
          1 => 'App\\Models\\User',
          2 => 'Spatie\\Permission\\Traits\\HasRoles',
          3 => NULL,
          4 => NULL,
        ),
      )),
      '002a3121f4835ad83cebefdf4cc99cc3' => 
      \PHPStan\Analyser\IntermediaryNameScope::__set_state(array(
         'namespace' => 'Spatie\\Permission\\Traits',
         'uses' => 
        array (
          'backedenum' => 'BackedEnum',
          'builder' => 'Illuminate\\Database\\Eloquent\\Builder',
          'model' => 'Illuminate\\Database\\Eloquent\\Model',
          'belongstomany' => 'Illuminate\\Database\\Eloquent\\Relations\\BelongsToMany',
          'pivot' => 'Illuminate\\Database\\Eloquent\\Relations\\Pivot',
          'arr' => 'Illuminate\\Support\\Arr',
          'collection' => 'Illuminate\\Support\\Collection',
          'permission' => 'Spatie\\Permission\\Contracts\\Permission',
          'role' => 'Spatie\\Permission\\Contracts\\Role',
          'roleattachedevent' => 'Spatie\\Permission\\Events\\RoleAttachedEvent',
          'roledetachedevent' => 'Spatie\\Permission\\Events\\RoleDetachedEvent',
          'permissionregistrar' => 'Spatie\\Permission\\PermissionRegistrar',
          'config' => 'Spatie\\Permission\\Support\\Config',
          'typeerror' => 'TypeError',
        ),
         'className' => 'App\\Models\\User',
         'functionName' => 'teams',
         'templatePhpDocNodes' => 
        array (
        ),
         'parent' => NULL,
         'typeAliasesMap' => 
        array (
        ),
         'bypassTypeAliases' => false,
         'constUses' => 
        array (
        ),
         'typeAliasClassName' => 'Spatie\\Permission\\Traits\\HasRoles',
         'traitData' => 
        array (
          0 => '/var/www/html/app/Models/User.php',
          1 => 'App\\Models\\User',
          2 => 'Spatie\\Permission\\Traits\\HasRoles',
          3 => NULL,
          4 => NULL,
        ),
      )),
      '2a439b486756163bad636ff1c156019c' => 
      \PHPStan\Analyser\IntermediaryNameScope::__set_state(array(
         'namespace' => 'Spatie\\Permission\\Traits',
         'uses' => 
        array (
          'backedenum' => 'BackedEnum',
          'builder' => 'Illuminate\\Database\\Eloquent\\Builder',
          'model' => 'Illuminate\\Database\\Eloquent\\Model',
          'belongstomany' => 'Illuminate\\Database\\Eloquent\\Relations\\BelongsToMany',
          'pivot' => 'Illuminate\\Database\\Eloquent\\Relations\\Pivot',
          'arr' => 'Illuminate\\Support\\Arr',
          'collection' => 'Illuminate\\Support\\Collection',
          'permission' => 'Spatie\\Permission\\Contracts\\Permission',
          'role' => 'Spatie\\Permission\\Contracts\\Role',
          'roleattachedevent' => 'Spatie\\Permission\\Events\\RoleAttachedEvent',
          'roledetachedevent' => 'Spatie\\Permission\\Events\\RoleDetachedEvent',
          'permissionregistrar' => 'Spatie\\Permission\\PermissionRegistrar',
          'config' => 'Spatie\\Permission\\Support\\Config',
          'typeerror' => 'TypeError',
        ),
         'className' => 'App\\Models\\User',
         'functionName' => 'scopeTeam',
         'templatePhpDocNodes' => 
        array (
        ),
         'parent' => NULL,
         'typeAliasesMap' => 
        array (
        ),
         'bypassTypeAliases' => false,
         'constUses' => 
        array (
        ),
         'typeAliasClassName' => 'Spatie\\Permission\\Traits\\HasRoles',
         'traitData' => 
        array (
          0 => '/var/www/html/app/Models/User.php',
          1 => 'App\\Models\\User',
          2 => 'Spatie\\Permission\\Traits\\HasRoles',
          3 => NULL,
          4 => NULL,
        ),
      )),
      '5bb1940504ac390d3c72594aa956bfae' => 
      \PHPStan\Analyser\IntermediaryNameScope::__set_state(array(
         'namespace' => 'Spatie\\Permission\\Traits',
         'uses' => 
        array (
          'backedenum' => 'BackedEnum',
          'builder' => 'Illuminate\\Database\\Eloquent\\Builder',
          'model' => 'Illuminate\\Database\\Eloquent\\Model',
          'belongstomany' => 'Illuminate\\Database\\Eloquent\\Relations\\BelongsToMany',
          'pivot' => 'Illuminate\\Database\\Eloquent\\Relations\\Pivot',
          'arr' => 'Illuminate\\Support\\Arr',
          'collection' => 'Illuminate\\Support\\Collection',
          'permission' => 'Spatie\\Permission\\Contracts\\Permission',
          'role' => 'Spatie\\Permission\\Contracts\\Role',
          'roleattachedevent' => 'Spatie\\Permission\\Events\\RoleAttachedEvent',
          'roledetachedevent' => 'Spatie\\Permission\\Events\\RoleDetachedEvent',
          'permissionregistrar' => 'Spatie\\Permission\\PermissionRegistrar',
          'config' => 'Spatie\\Permission\\Support\\Config',
          'typeerror' => 'TypeError',
        ),
         'className' => 'App\\Models\\User',
         'functionName' => 'scopeWithoutTeam',
         'templatePhpDocNodes' => 
        array (
        ),
         'parent' => NULL,
         'typeAliasesMap' => 
        array (
        ),
         'bypassTypeAliases' => false,
         'constUses' => 
        array (
        ),
         'typeAliasClassName' => 'Spatie\\Permission\\Traits\\HasRoles',
         'traitData' => 
        array (
          0 => '/var/www/html/app/Models/User.php',
          1 => 'App\\Models\\User',
          2 => 'Spatie\\Permission\\Traits\\HasRoles',
          3 => NULL,
          4 => NULL,
        ),
      )),
      '70691aee1e867c0275cdc06de0b3b72b' => 
      \PHPStan\Analyser\IntermediaryNameScope::__set_state(array(
         'namespace' => 'Spatie\\Permission\\Traits',
         'uses' => 
        array (
          'backedenum' => 'BackedEnum',
          'builder' => 'Illuminate\\Database\\Eloquent\\Builder',
          'model' => 'Illuminate\\Database\\Eloquent\\Model',
          'belongstomany' => 'Illuminate\\Database\\Eloquent\\Relations\\BelongsToMany',
          'pivot' => 'Illuminate\\Database\\Eloquent\\Relations\\Pivot',
          'arr' => 'Illuminate\\Support\\Arr',
          'collection' => 'Illuminate\\Support\\Collection',
          'permission' => 'Spatie\\Permission\\Contracts\\Permission',
          'role' => 'Spatie\\Permission\\Contracts\\Role',
          'roleattachedevent' => 'Spatie\\Permission\\Events\\RoleAttachedEvent',
          'roledetachedevent' => 'Spatie\\Permission\\Events\\RoleDetachedEvent',
          'permissionregistrar' => 'Spatie\\Permission\\PermissionRegistrar',
          'config' => 'Spatie\\Permission\\Support\\Config',
          'typeerror' => 'TypeError',
        ),
         'className' => 'App\\Models\\User',
         'functionName' => 'collectRoles',
         'templatePhpDocNodes' => 
        array (
        ),
         'parent' => NULL,
         'typeAliasesMap' => 
        array (
        ),
         'bypassTypeAliases' => false,
         'constUses' => 
        array (
        ),
         'typeAliasClassName' => 'Spatie\\Permission\\Traits\\HasRoles',
         'traitData' => 
        array (
          0 => '/var/www/html/app/Models/User.php',
          1 => 'App\\Models\\User',
          2 => 'Spatie\\Permission\\Traits\\HasRoles',
          3 => NULL,
          4 => NULL,
        ),
      )),
      'f6bf9d95faf51519f81abb378af74934' => 
      \PHPStan\Analyser\IntermediaryNameScope::__set_state(array(
         'namespace' => 'Spatie\\Permission\\Traits',
         'uses' => 
        array (
          'backedenum' => 'BackedEnum',
          'builder' => 'Illuminate\\Database\\Eloquent\\Builder',
          'model' => 'Illuminate\\Database\\Eloquent\\Model',
          'belongstomany' => 'Illuminate\\Database\\Eloquent\\Relations\\BelongsToMany',
          'pivot' => 'Illuminate\\Database\\Eloquent\\Relations\\Pivot',
          'arr' => 'Illuminate\\Support\\Arr',
          'collection' => 'Illuminate\\Support\\Collection',
          'permission' => 'Spatie\\Permission\\Contracts\\Permission',
          'role' => 'Spatie\\Permission\\Contracts\\Role',
          'roleattachedevent' => 'Spatie\\Permission\\Events\\RoleAttachedEvent',
          'roledetachedevent' => 'Spatie\\Permission\\Events\\RoleDetachedEvent',
          'permissionregistrar' => 'Spatie\\Permission\\PermissionRegistrar',
          'config' => 'Spatie\\Permission\\Support\\Config',
          'typeerror' => 'TypeError',
        ),
         'className' => 'App\\Models\\User',
         'functionName' => 'detachRoles',
         'templatePhpDocNodes' => 
        array (
        ),
         'parent' => NULL,
         'typeAliasesMap' => 
        array (
        ),
         'bypassTypeAliases' => false,
         'constUses' => 
        array (
        ),
         'typeAliasClassName' => 'Spatie\\Permission\\Traits\\HasRoles',
         'traitData' => 
        array (
          0 => '/var/www/html/app/Models/User.php',
          1 => 'App\\Models\\User',
          2 => 'Spatie\\Permission\\Traits\\HasRoles',
          3 => NULL,
          4 => NULL,
        ),
      )),
      '700fd38e467093d1b103600143f8d4ce' => 
      \PHPStan\Analyser\IntermediaryNameScope::__set_state(array(
         'namespace' => 'Spatie\\Permission\\Traits',
         'uses' => 
        array (
          'backedenum' => 'BackedEnum',
          'builder' => 'Illuminate\\Database\\Eloquent\\Builder',
          'model' => 'Illuminate\\Database\\Eloquent\\Model',
          'belongstomany' => 'Illuminate\\Database\\Eloquent\\Relations\\BelongsToMany',
          'pivot' => 'Illuminate\\Database\\Eloquent\\Relations\\Pivot',
          'arr' => 'Illuminate\\Support\\Arr',
          'collection' => 'Illuminate\\Support\\Collection',
          'permission' => 'Spatie\\Permission\\Contracts\\Permission',
          'role' => 'Spatie\\Permission\\Contracts\\Role',
          'roleattachedevent' => 'Spatie\\Permission\\Events\\RoleAttachedEvent',
          'roledetachedevent' => 'Spatie\\Permission\\Events\\RoleDetachedEvent',
          'permissionregistrar' => 'Spatie\\Permission\\PermissionRegistrar',
          'config' => 'Spatie\\Permission\\Support\\Config',
          'typeerror' => 'TypeError',
        ),
         'className' => 'App\\Models\\User',
         'functionName' => 'assignRole',
         'templatePhpDocNodes' => 
        array (
        ),
         'parent' => NULL,
         'typeAliasesMap' => 
        array (
        ),
         'bypassTypeAliases' => false,
         'constUses' => 
        array (
        ),
         'typeAliasClassName' => 'Spatie\\Permission\\Traits\\HasRoles',
         'traitData' => 
        array (
          0 => '/var/www/html/app/Models/User.php',
          1 => 'App\\Models\\User',
          2 => 'Spatie\\Permission\\Traits\\HasRoles',
          3 => NULL,
          4 => NULL,
        ),
      )),
      '69f65489b0b5dcd5c4159b38d9091f49' => 
      \PHPStan\Analyser\IntermediaryNameScope::__set_state(array(
         'namespace' => 'Spatie\\Permission\\Traits',
         'uses' => 
        array (
          'backedenum' => 'BackedEnum',
          'builder' => 'Illuminate\\Database\\Eloquent\\Builder',
          'model' => 'Illuminate\\Database\\Eloquent\\Model',
          'belongstomany' => 'Illuminate\\Database\\Eloquent\\Relations\\BelongsToMany',
          'pivot' => 'Illuminate\\Database\\Eloquent\\Relations\\Pivot',
          'arr' => 'Illuminate\\Support\\Arr',
          'collection' => 'Illuminate\\Support\\Collection',
          'permission' => 'Spatie\\Permission\\Contracts\\Permission',
          'role' => 'Spatie\\Permission\\Contracts\\Role',
          'roleattachedevent' => 'Spatie\\Permission\\Events\\RoleAttachedEvent',
          'roledetachedevent' => 'Spatie\\Permission\\Events\\RoleDetachedEvent',
          'permissionregistrar' => 'Spatie\\Permission\\PermissionRegistrar',
          'config' => 'Spatie\\Permission\\Support\\Config',
          'typeerror' => 'TypeError',
        ),
         'className' => 'App\\Models\\User',
         'functionName' => 'removeRole',
         'templatePhpDocNodes' => 
        array (
        ),
         'parent' => NULL,
         'typeAliasesMap' => 
        array (
        ),
         'bypassTypeAliases' => false,
         'constUses' => 
        array (
        ),
         'typeAliasClassName' => 'Spatie\\Permission\\Traits\\HasRoles',
         'traitData' => 
        array (
          0 => '/var/www/html/app/Models/User.php',
          1 => 'App\\Models\\User',
          2 => 'Spatie\\Permission\\Traits\\HasRoles',
          3 => NULL,
          4 => NULL,
        ),
      )),
      'd6c5c434b5509f96db7621d75768ea1b' => 
      \PHPStan\Analyser\IntermediaryNameScope::__set_state(array(
         'namespace' => 'Spatie\\Permission\\Traits',
         'uses' => 
        array (
          'backedenum' => 'BackedEnum',
          'builder' => 'Illuminate\\Database\\Eloquent\\Builder',
          'model' => 'Illuminate\\Database\\Eloquent\\Model',
          'belongstomany' => 'Illuminate\\Database\\Eloquent\\Relations\\BelongsToMany',
          'pivot' => 'Illuminate\\Database\\Eloquent\\Relations\\Pivot',
          'arr' => 'Illuminate\\Support\\Arr',
          'collection' => 'Illuminate\\Support\\Collection',
          'permission' => 'Spatie\\Permission\\Contracts\\Permission',
          'role' => 'Spatie\\Permission\\Contracts\\Role',
          'roleattachedevent' => 'Spatie\\Permission\\Events\\RoleAttachedEvent',
          'roledetachedevent' => 'Spatie\\Permission\\Events\\RoleDetachedEvent',
          'permissionregistrar' => 'Spatie\\Permission\\PermissionRegistrar',
          'config' => 'Spatie\\Permission\\Support\\Config',
          'typeerror' => 'TypeError',
        ),
         'className' => 'App\\Models\\User',
         'functionName' => 'syncRoles',
         'templatePhpDocNodes' => 
        array (
        ),
         'parent' => NULL,
         'typeAliasesMap' => 
        array (
        ),
         'bypassTypeAliases' => false,
         'constUses' => 
        array (
        ),
         'typeAliasClassName' => 'Spatie\\Permission\\Traits\\HasRoles',
         'traitData' => 
        array (
          0 => '/var/www/html/app/Models/User.php',
          1 => 'App\\Models\\User',
          2 => 'Spatie\\Permission\\Traits\\HasRoles',
          3 => NULL,
          4 => NULL,
        ),
      )),
      'c026c83413fbf4a944094db1ac8d9a55' => 
      \PHPStan\Analyser\IntermediaryNameScope::__set_state(array(
         'namespace' => 'Spatie\\Permission\\Traits',
         'uses' => 
        array (
          'backedenum' => 'BackedEnum',
          'builder' => 'Illuminate\\Database\\Eloquent\\Builder',
          'model' => 'Illuminate\\Database\\Eloquent\\Model',
          'belongstomany' => 'Illuminate\\Database\\Eloquent\\Relations\\BelongsToMany',
          'pivot' => 'Illuminate\\Database\\Eloquent\\Relations\\Pivot',
          'arr' => 'Illuminate\\Support\\Arr',
          'collection' => 'Illuminate\\Support\\Collection',
          'permission' => 'Spatie\\Permission\\Contracts\\Permission',
          'role' => 'Spatie\\Permission\\Contracts\\Role',
          'roleattachedevent' => 'Spatie\\Permission\\Events\\RoleAttachedEvent',
          'roledetachedevent' => 'Spatie\\Permission\\Events\\RoleDetachedEvent',
          'permissionregistrar' => 'Spatie\\Permission\\PermissionRegistrar',
          'config' => 'Spatie\\Permission\\Support\\Config',
          'typeerror' => 'TypeError',
        ),
         'className' => 'App\\Models\\User',
         'functionName' => 'hasRole',
         'templatePhpDocNodes' => 
        array (
        ),
         'parent' => NULL,
         'typeAliasesMap' => 
        array (
        ),
         'bypassTypeAliases' => false,
         'constUses' => 
        array (
        ),
         'typeAliasClassName' => 'Spatie\\Permission\\Traits\\HasRoles',
         'traitData' => 
        array (
          0 => '/var/www/html/app/Models/User.php',
          1 => 'App\\Models\\User',
          2 => 'Spatie\\Permission\\Traits\\HasRoles',
          3 => NULL,
          4 => NULL,
        ),
      )),
      'ef47b8ac051e634b3b66275de74dba64' => 
      \PHPStan\Analyser\IntermediaryNameScope::__set_state(array(
         'namespace' => 'Spatie\\Permission\\Traits',
         'uses' => 
        array (
          'backedenum' => 'BackedEnum',
          'builder' => 'Illuminate\\Database\\Eloquent\\Builder',
          'model' => 'Illuminate\\Database\\Eloquent\\Model',
          'belongstomany' => 'Illuminate\\Database\\Eloquent\\Relations\\BelongsToMany',
          'pivot' => 'Illuminate\\Database\\Eloquent\\Relations\\Pivot',
          'arr' => 'Illuminate\\Support\\Arr',
          'collection' => 'Illuminate\\Support\\Collection',
          'permission' => 'Spatie\\Permission\\Contracts\\Permission',
          'role' => 'Spatie\\Permission\\Contracts\\Role',
          'roleattachedevent' => 'Spatie\\Permission\\Events\\RoleAttachedEvent',
          'roledetachedevent' => 'Spatie\\Permission\\Events\\RoleDetachedEvent',
          'permissionregistrar' => 'Spatie\\Permission\\PermissionRegistrar',
          'config' => 'Spatie\\Permission\\Support\\Config',
          'typeerror' => 'TypeError',
        ),
         'className' => 'App\\Models\\User',
         'functionName' => 'hasAnyRole',
         'templatePhpDocNodes' => 
        array (
        ),
         'parent' => NULL,
         'typeAliasesMap' => 
        array (
        ),
         'bypassTypeAliases' => false,
         'constUses' => 
        array (
        ),
         'typeAliasClassName' => 'Spatie\\Permission\\Traits\\HasRoles',
         'traitData' => 
        array (
          0 => '/var/www/html/app/Models/User.php',
          1 => 'App\\Models\\User',
          2 => 'Spatie\\Permission\\Traits\\HasRoles',
          3 => NULL,
          4 => NULL,
        ),
      )),
      '37f194a4e654467fed5cb974b3be8061' => 
      \PHPStan\Analyser\IntermediaryNameScope::__set_state(array(
         'namespace' => 'Spatie\\Permission\\Traits',
         'uses' => 
        array (
          'backedenum' => 'BackedEnum',
          'builder' => 'Illuminate\\Database\\Eloquent\\Builder',
          'model' => 'Illuminate\\Database\\Eloquent\\Model',
          'belongstomany' => 'Illuminate\\Database\\Eloquent\\Relations\\BelongsToMany',
          'pivot' => 'Illuminate\\Database\\Eloquent\\Relations\\Pivot',
          'arr' => 'Illuminate\\Support\\Arr',
          'collection' => 'Illuminate\\Support\\Collection',
          'permission' => 'Spatie\\Permission\\Contracts\\Permission',
          'role' => 'Spatie\\Permission\\Contracts\\Role',
          'roleattachedevent' => 'Spatie\\Permission\\Events\\RoleAttachedEvent',
          'roledetachedevent' => 'Spatie\\Permission\\Events\\RoleDetachedEvent',
          'permissionregistrar' => 'Spatie\\Permission\\PermissionRegistrar',
          'config' => 'Spatie\\Permission\\Support\\Config',
          'typeerror' => 'TypeError',
        ),
         'className' => 'App\\Models\\User',
         'functionName' => 'hasAllRoles',
         'templatePhpDocNodes' => 
        array (
        ),
         'parent' => NULL,
         'typeAliasesMap' => 
        array (
        ),
         'bypassTypeAliases' => false,
         'constUses' => 
        array (
        ),
         'typeAliasClassName' => 'Spatie\\Permission\\Traits\\HasRoles',
         'traitData' => 
        array (
          0 => '/var/www/html/app/Models/User.php',
          1 => 'App\\Models\\User',
          2 => 'Spatie\\Permission\\Traits\\HasRoles',
          3 => NULL,
          4 => NULL,
        ),
      )),
      '1400b4d786159c25941df907aae6ba23' => 
      \PHPStan\Analyser\IntermediaryNameScope::__set_state(array(
         'namespace' => 'Spatie\\Permission\\Traits',
         'uses' => 
        array (
          'backedenum' => 'BackedEnum',
          'builder' => 'Illuminate\\Database\\Eloquent\\Builder',
          'model' => 'Illuminate\\Database\\Eloquent\\Model',
          'belongstomany' => 'Illuminate\\Database\\Eloquent\\Relations\\BelongsToMany',
          'pivot' => 'Illuminate\\Database\\Eloquent\\Relations\\Pivot',
          'arr' => 'Illuminate\\Support\\Arr',
          'collection' => 'Illuminate\\Support\\Collection',
          'permission' => 'Spatie\\Permission\\Contracts\\Permission',
          'role' => 'Spatie\\Permission\\Contracts\\Role',
          'roleattachedevent' => 'Spatie\\Permission\\Events\\RoleAttachedEvent',
          'roledetachedevent' => 'Spatie\\Permission\\Events\\RoleDetachedEvent',
          'permissionregistrar' => 'Spatie\\Permission\\PermissionRegistrar',
          'config' => 'Spatie\\Permission\\Support\\Config',
          'typeerror' => 'TypeError',
        ),
         'className' => 'App\\Models\\User',
         'functionName' => 'hasExactRoles',
         'templatePhpDocNodes' => 
        array (
        ),
         'parent' => NULL,
         'typeAliasesMap' => 
        array (
        ),
         'bypassTypeAliases' => false,
         'constUses' => 
        array (
        ),
         'typeAliasClassName' => 'Spatie\\Permission\\Traits\\HasRoles',
         'traitData' => 
        array (
          0 => '/var/www/html/app/Models/User.php',
          1 => 'App\\Models\\User',
          2 => 'Spatie\\Permission\\Traits\\HasRoles',
          3 => NULL,
          4 => NULL,
        ),
      )),
      '43c4ca72fe9ab5d14531ff6c2981d2ed' => 
      \PHPStan\Analyser\IntermediaryNameScope::__set_state(array(
         'namespace' => 'Spatie\\Permission\\Traits',
         'uses' => 
        array (
          'backedenum' => 'BackedEnum',
          'builder' => 'Illuminate\\Database\\Eloquent\\Builder',
          'model' => 'Illuminate\\Database\\Eloquent\\Model',
          'belongstomany' => 'Illuminate\\Database\\Eloquent\\Relations\\BelongsToMany',
          'pivot' => 'Illuminate\\Database\\Eloquent\\Relations\\Pivot',
          'arr' => 'Illuminate\\Support\\Arr',
          'collection' => 'Illuminate\\Support\\Collection',
          'permission' => 'Spatie\\Permission\\Contracts\\Permission',
          'role' => 'Spatie\\Permission\\Contracts\\Role',
          'roleattachedevent' => 'Spatie\\Permission\\Events\\RoleAttachedEvent',
          'roledetachedevent' => 'Spatie\\Permission\\Events\\RoleDetachedEvent',
          'permissionregistrar' => 'Spatie\\Permission\\PermissionRegistrar',
          'config' => 'Spatie\\Permission\\Support\\Config',
          'typeerror' => 'TypeError',
        ),
         'className' => 'App\\Models\\User',
         'functionName' => 'getDirectPermissions',
         'templatePhpDocNodes' => 
        array (
        ),
         'parent' => NULL,
         'typeAliasesMap' => 
        array (
        ),
         'bypassTypeAliases' => false,
         'constUses' => 
        array (
        ),
         'typeAliasClassName' => 'Spatie\\Permission\\Traits\\HasRoles',
         'traitData' => 
        array (
          0 => '/var/www/html/app/Models/User.php',
          1 => 'App\\Models\\User',
          2 => 'Spatie\\Permission\\Traits\\HasRoles',
          3 => NULL,
          4 => NULL,
        ),
      )),
      'fc7077b75fe79650876e61074dde1afb' => 
      \PHPStan\Analyser\IntermediaryNameScope::__set_state(array(
         'namespace' => 'Spatie\\Permission\\Traits',
         'uses' => 
        array (
          'backedenum' => 'BackedEnum',
          'builder' => 'Illuminate\\Database\\Eloquent\\Builder',
          'model' => 'Illuminate\\Database\\Eloquent\\Model',
          'belongstomany' => 'Illuminate\\Database\\Eloquent\\Relations\\BelongsToMany',
          'pivot' => 'Illuminate\\Database\\Eloquent\\Relations\\Pivot',
          'arr' => 'Illuminate\\Support\\Arr',
          'collection' => 'Illuminate\\Support\\Collection',
          'permission' => 'Spatie\\Permission\\Contracts\\Permission',
          'role' => 'Spatie\\Permission\\Contracts\\Role',
          'roleattachedevent' => 'Spatie\\Permission\\Events\\RoleAttachedEvent',
          'roledetachedevent' => 'Spatie\\Permission\\Events\\RoleDetachedEvent',
          'permissionregistrar' => 'Spatie\\Permission\\PermissionRegistrar',
          'config' => 'Spatie\\Permission\\Support\\Config',
          'typeerror' => 'TypeError',
        ),
         'className' => 'App\\Models\\User',
         'functionName' => 'getRoleNames',
         'templatePhpDocNodes' => 
        array (
        ),
         'parent' => NULL,
         'typeAliasesMap' => 
        array (
        ),
         'bypassTypeAliases' => false,
         'constUses' => 
        array (
        ),
         'typeAliasClassName' => 'Spatie\\Permission\\Traits\\HasRoles',
         'traitData' => 
        array (
          0 => '/var/www/html/app/Models/User.php',
          1 => 'App\\Models\\User',
          2 => 'Spatie\\Permission\\Traits\\HasRoles',
          3 => NULL,
          4 => NULL,
        ),
      )),
      'fa4496a818093c95029c4e704133ff73' => 
      \PHPStan\Analyser\IntermediaryNameScope::__set_state(array(
         'namespace' => 'Spatie\\Permission\\Traits',
         'uses' => 
        array (
          'backedenum' => 'BackedEnum',
          'builder' => 'Illuminate\\Database\\Eloquent\\Builder',
          'model' => 'Illuminate\\Database\\Eloquent\\Model',
          'belongstomany' => 'Illuminate\\Database\\Eloquent\\Relations\\BelongsToMany',
          'pivot' => 'Illuminate\\Database\\Eloquent\\Relations\\Pivot',
          'arr' => 'Illuminate\\Support\\Arr',
          'collection' => 'Illuminate\\Support\\Collection',
          'permission' => 'Spatie\\Permission\\Contracts\\Permission',
          'role' => 'Spatie\\Permission\\Contracts\\Role',
          'roleattachedevent' => 'Spatie\\Permission\\Events\\RoleAttachedEvent',
          'roledetachedevent' => 'Spatie\\Permission\\Events\\RoleDetachedEvent',
          'permissionregistrar' => 'Spatie\\Permission\\PermissionRegistrar',
          'config' => 'Spatie\\Permission\\Support\\Config',
          'typeerror' => 'TypeError',
        ),
         'className' => 'App\\Models\\User',
         'functionName' => 'getStoredRole',
         'templatePhpDocNodes' => 
        array (
        ),
         'parent' => NULL,
         'typeAliasesMap' => 
        array (
        ),
         'bypassTypeAliases' => false,
         'constUses' => 
        array (
        ),
         'typeAliasClassName' => 'Spatie\\Permission\\Traits\\HasRoles',
         'traitData' => 
        array (
          0 => '/var/www/html/app/Models/User.php',
          1 => 'App\\Models\\User',
          2 => 'Spatie\\Permission\\Traits\\HasRoles',
          3 => NULL,
          4 => NULL,
        ),
      )),
      '7d24b2012d3c0cb430c9b98edc8934e1' => 
      \PHPStan\Analyser\IntermediaryNameScope::__set_state(array(
         'namespace' => 'Spatie\\Permission\\Traits',
         'uses' => 
        array (
          'backedenum' => 'BackedEnum',
          'builder' => 'Illuminate\\Database\\Eloquent\\Builder',
          'model' => 'Illuminate\\Database\\Eloquent\\Model',
          'belongstomany' => 'Illuminate\\Database\\Eloquent\\Relations\\BelongsToMany',
          'pivot' => 'Illuminate\\Database\\Eloquent\\Relations\\Pivot',
          'arr' => 'Illuminate\\Support\\Arr',
          'collection' => 'Illuminate\\Support\\Collection',
          'permission' => 'Spatie\\Permission\\Contracts\\Permission',
          'role' => 'Spatie\\Permission\\Contracts\\Role',
          'roleattachedevent' => 'Spatie\\Permission\\Events\\RoleAttachedEvent',
          'roledetachedevent' => 'Spatie\\Permission\\Events\\RoleDetachedEvent',
          'permissionregistrar' => 'Spatie\\Permission\\PermissionRegistrar',
          'config' => 'Spatie\\Permission\\Support\\Config',
          'typeerror' => 'TypeError',
        ),
         'className' => 'App\\Models\\User',
         'functionName' => 'convertPipeToArray',
         'templatePhpDocNodes' => 
        array (
        ),
         'parent' => NULL,
         'typeAliasesMap' => 
        array (
        ),
         'bypassTypeAliases' => false,
         'constUses' => 
        array (
        ),
         'typeAliasClassName' => 'Spatie\\Permission\\Traits\\HasRoles',
         'traitData' => 
        array (
          0 => '/var/www/html/app/Models/User.php',
          1 => 'App\\Models\\User',
          2 => 'Spatie\\Permission\\Traits\\HasRoles',
          3 => NULL,
          4 => NULL,
        ),
      )),
      '9526e410d42ceb8c25d0424807503a6a' => 
      \PHPStan\Analyser\IntermediaryNameScope::__set_state(array(
         'namespace' => 'Illuminate\\Notifications',
         'uses' => 
        array (
        ),
         'className' => 'App\\Models\\User',
         'functionName' => NULL,
         'templatePhpDocNodes' => 
        array (
        ),
         'parent' => NULL,
         'typeAliasesMap' => 
        array (
        ),
         'bypassTypeAliases' => false,
         'constUses' => 
        array (
        ),
         'typeAliasClassName' => 'Illuminate\\Notifications\\Notifiable',
         'traitData' => 
        array (
          0 => '/var/www/html/app/Models/User.php',
          1 => 'App\\Models\\User',
          2 => 'Illuminate\\Notifications\\Notifiable',
          3 => NULL,
          4 => NULL,
        ),
      )),
      'f38878a858950d4f43e5248291b41298' => 
      \PHPStan\Analyser\IntermediaryNameScope::__set_state(array(
         'namespace' => 'Illuminate\\Notifications',
         'uses' => 
        array (
        ),
         'className' => 'App\\Models\\User',
         'functionName' => NULL,
         'templatePhpDocNodes' => 
        array (
        ),
         'parent' => NULL,
         'typeAliasesMap' => 
        array (
        ),
         'bypassTypeAliases' => false,
         'constUses' => 
        array (
        ),
         'typeAliasClassName' => 'Illuminate\\Notifications\\HasDatabaseNotifications',
         'traitData' => 
        array (
          0 => '/var/www/html/app/Models/User.php',
          1 => 'App\\Models\\User',
          2 => 'Illuminate\\Notifications\\HasDatabaseNotifications',
          3 => 'Illuminate\\Notifications\\Notifiable',
          4 => NULL,
        ),
      )),
      'eb7cbf233e2fc59b28b319698d803bff' => 
      \PHPStan\Analyser\IntermediaryNameScope::__set_state(array(
         'namespace' => 'Illuminate\\Notifications',
         'uses' => 
        array (
        ),
         'className' => 'App\\Models\\User',
         'functionName' => 'notifications',
         'templatePhpDocNodes' => 
        array (
        ),
         'parent' => NULL,
         'typeAliasesMap' => 
        array (
        ),
         'bypassTypeAliases' => false,
         'constUses' => 
        array (
        ),
         'typeAliasClassName' => 'Illuminate\\Notifications\\HasDatabaseNotifications',
         'traitData' => 
        array (
          0 => '/var/www/html/app/Models/User.php',
          1 => 'App\\Models\\User',
          2 => 'Illuminate\\Notifications\\HasDatabaseNotifications',
          3 => 'Illuminate\\Notifications\\Notifiable',
          4 => NULL,
        ),
      )),
      '51775cb3b5104d18e62dcadcf83be12a' => 
      \PHPStan\Analyser\IntermediaryNameScope::__set_state(array(
         'namespace' => 'Illuminate\\Notifications',
         'uses' => 
        array (
        ),
         'className' => 'App\\Models\\User',
         'functionName' => 'readNotifications',
         'templatePhpDocNodes' => 
        array (
        ),
         'parent' => NULL,
         'typeAliasesMap' => 
        array (
        ),
         'bypassTypeAliases' => false,
         'constUses' => 
        array (
        ),
         'typeAliasClassName' => 'Illuminate\\Notifications\\HasDatabaseNotifications',
         'traitData' => 
        array (
          0 => '/var/www/html/app/Models/User.php',
          1 => 'App\\Models\\User',
          2 => 'Illuminate\\Notifications\\HasDatabaseNotifications',
          3 => 'Illuminate\\Notifications\\Notifiable',
          4 => NULL,
        ),
      )),
      'ebd66a4eb561d51cda31b3492e2dfc25' => 
      \PHPStan\Analyser\IntermediaryNameScope::__set_state(array(
         'namespace' => 'Illuminate\\Notifications',
         'uses' => 
        array (
        ),
         'className' => 'App\\Models\\User',
         'functionName' => 'unreadNotifications',
         'templatePhpDocNodes' => 
        array (
        ),
         'parent' => NULL,
         'typeAliasesMap' => 
        array (
        ),
         'bypassTypeAliases' => false,
         'constUses' => 
        array (
        ),
         'typeAliasClassName' => 'Illuminate\\Notifications\\HasDatabaseNotifications',
         'traitData' => 
        array (
          0 => '/var/www/html/app/Models/User.php',
          1 => 'App\\Models\\User',
          2 => 'Illuminate\\Notifications\\HasDatabaseNotifications',
          3 => 'Illuminate\\Notifications\\Notifiable',
          4 => NULL,
        ),
      )),
      '23c28bdafbc8d530e370b376983a8fac' => 
      \PHPStan\Analyser\IntermediaryNameScope::__set_state(array(
         'namespace' => 'Illuminate\\Notifications',
         'uses' => 
        array (
          'dispatcher' => 'Illuminate\\Contracts\\Notifications\\Dispatcher',
          'str' => 'Illuminate\\Support\\Str',
        ),
         'className' => 'App\\Models\\User',
         'functionName' => NULL,
         'templatePhpDocNodes' => 
        array (
        ),
         'parent' => NULL,
         'typeAliasesMap' => 
        array (
        ),
         'bypassTypeAliases' => false,
         'constUses' => 
        array (
        ),
         'typeAliasClassName' => 'Illuminate\\Notifications\\RoutesNotifications',
         'traitData' => 
        array (
          0 => '/var/www/html/app/Models/User.php',
          1 => 'App\\Models\\User',
          2 => 'Illuminate\\Notifications\\RoutesNotifications',
          3 => 'Illuminate\\Notifications\\Notifiable',
          4 => NULL,
        ),
      )),
      'aaea799af607aa2db7c6d6b6a115a52c' => 
      \PHPStan\Analyser\IntermediaryNameScope::__set_state(array(
         'namespace' => 'Illuminate\\Notifications',
         'uses' => 
        array (
          'dispatcher' => 'Illuminate\\Contracts\\Notifications\\Dispatcher',
          'str' => 'Illuminate\\Support\\Str',
        ),
         'className' => 'App\\Models\\User',
         'functionName' => 'notify',
         'templatePhpDocNodes' => 
        array (
        ),
         'parent' => NULL,
         'typeAliasesMap' => 
        array (
        ),
         'bypassTypeAliases' => false,
         'constUses' => 
        array (
        ),
         'typeAliasClassName' => 'Illuminate\\Notifications\\RoutesNotifications',
         'traitData' => 
        array (
          0 => '/var/www/html/app/Models/User.php',
          1 => 'App\\Models\\User',
          2 => 'Illuminate\\Notifications\\RoutesNotifications',
          3 => 'Illuminate\\Notifications\\Notifiable',
          4 => NULL,
        ),
      )),
      '6e2c91525bc46c6bdd243a398287fdde' => 
      \PHPStan\Analyser\IntermediaryNameScope::__set_state(array(
         'namespace' => 'Illuminate\\Notifications',
         'uses' => 
        array (
          'dispatcher' => 'Illuminate\\Contracts\\Notifications\\Dispatcher',
          'str' => 'Illuminate\\Support\\Str',
        ),
         'className' => 'App\\Models\\User',
         'functionName' => 'notifyNow',
         'templatePhpDocNodes' => 
        array (
        ),
         'parent' => NULL,
         'typeAliasesMap' => 
        array (
        ),
         'bypassTypeAliases' => false,
         'constUses' => 
        array (
        ),
         'typeAliasClassName' => 'Illuminate\\Notifications\\RoutesNotifications',
         'traitData' => 
        array (
          0 => '/var/www/html/app/Models/User.php',
          1 => 'App\\Models\\User',
          2 => 'Illuminate\\Notifications\\RoutesNotifications',
          3 => 'Illuminate\\Notifications\\Notifiable',
          4 => NULL,
        ),
      )),
      'c8abdde33ad08b1381a403de01c0ade0' => 
      \PHPStan\Analyser\IntermediaryNameScope::__set_state(array(
         'namespace' => 'Illuminate\\Notifications',
         'uses' => 
        array (
          'dispatcher' => 'Illuminate\\Contracts\\Notifications\\Dispatcher',
          'str' => 'Illuminate\\Support\\Str',
        ),
         'className' => 'App\\Models\\User',
         'functionName' => 'routeNotificationFor',
         'templatePhpDocNodes' => 
        array (
        ),
         'parent' => NULL,
         'typeAliasesMap' => 
        array (
        ),
         'bypassTypeAliases' => false,
         'constUses' => 
        array (
        ),
         'typeAliasClassName' => 'Illuminate\\Notifications\\RoutesNotifications',
         'traitData' => 
        array (
          0 => '/var/www/html/app/Models/User.php',
          1 => 'App\\Models\\User',
          2 => 'Illuminate\\Notifications\\RoutesNotifications',
          3 => 'Illuminate\\Notifications\\Notifiable',
          4 => NULL,
        ),
      )),
      '2fc9e2dab955d1f0ed7e6e23a2c14a08' => 
      \PHPStan\Analyser\IntermediaryNameScope::__set_state(array(
         'namespace' => 'Illuminate\\Database\\Eloquent',
         'uses' => 
        array (
          'eloquentcollection' => 'Illuminate\\Database\\Eloquent\\Collection',
          'basecollection' => 'Illuminate\\Support\\Collection',
        ),
         'className' => 'App\\Models\\User',
         'functionName' => NULL,
         'templatePhpDocNodes' => 
        array (
        ),
         'parent' => NULL,
         'typeAliasesMap' => 
        array (
        ),
         'bypassTypeAliases' => false,
         'constUses' => 
        array (
        ),
         'typeAliasClassName' => 'Illuminate\\Database\\Eloquent\\SoftDeletes',
         'traitData' => 
        array (
          0 => '/var/www/html/app/Models/User.php',
          1 => 'App\\Models\\User',
          2 => 'Illuminate\\Database\\Eloquent\\SoftDeletes',
          3 => NULL,
          4 => NULL,
        ),
      )),
      'b322c9d314258a3b09c0620d521164ff' => 
      \PHPStan\Analyser\IntermediaryNameScope::__set_state(array(
         'namespace' => 'Illuminate\\Database\\Eloquent',
         'uses' => 
        array (
          'eloquentcollection' => 'Illuminate\\Database\\Eloquent\\Collection',
          'basecollection' => 'Illuminate\\Support\\Collection',
        ),
         'className' => 'App\\Models\\User',
         'functionName' => 'bootSoftDeletes',
         'templatePhpDocNodes' => 
        array (
        ),
         'parent' => 
        \PHPStan\Analyser\IntermediaryNameScope::__set_state(array(
           'namespace' => 'Illuminate\\Database\\Eloquent',
           'uses' => 
          array (
            'eloquentcollection' => 'Illuminate\\Database\\Eloquent\\Collection',
            'basecollection' => 'Illuminate\\Support\\Collection',
          ),
           'className' => 'App\\Models\\User',
           'functionName' => NULL,
           'templatePhpDocNodes' => 
          array (
          ),
           'parent' => NULL,
           'typeAliasesMap' => 
          array (
          ),
           'bypassTypeAliases' => false,
           'constUses' => 
          array (
          ),
           'typeAliasClassName' => 'Illuminate\\Database\\Eloquent\\SoftDeletes',
           'traitData' => NULL,
        )),
         'typeAliasesMap' => 
        array (
        ),
         'bypassTypeAliases' => false,
         'constUses' => 
        array (
        ),
         'typeAliasClassName' => 'Illuminate\\Database\\Eloquent\\SoftDeletes',
         'traitData' => 
        array (
          0 => '/var/www/html/app/Models/User.php',
          1 => 'App\\Models\\User',
          2 => 'Illuminate\\Database\\Eloquent\\SoftDeletes',
          3 => NULL,
          4 => NULL,
        ),
      )),
      '4b5619ddfede94bbe76de7cf39a15d39' => 
      \PHPStan\Analyser\IntermediaryNameScope::__set_state(array(
         'namespace' => 'Illuminate\\Database\\Eloquent',
         'uses' => 
        array (
          'eloquentcollection' => 'Illuminate\\Database\\Eloquent\\Collection',
          'basecollection' => 'Illuminate\\Support\\Collection',
        ),
         'className' => 'App\\Models\\User',
         'functionName' => 'initializeSoftDeletes',
         'templatePhpDocNodes' => 
        array (
        ),
         'parent' => 
        \PHPStan\Analyser\IntermediaryNameScope::__set_state(array(
           'namespace' => 'Illuminate\\Database\\Eloquent',
           'uses' => 
          array (
            'eloquentcollection' => 'Illuminate\\Database\\Eloquent\\Collection',
            'basecollection' => 'Illuminate\\Support\\Collection',
          ),
           'className' => 'App\\Models\\User',
           'functionName' => NULL,
           'templatePhpDocNodes' => 
          array (
          ),
           'parent' => NULL,
           'typeAliasesMap' => 
          array (
          ),
           'bypassTypeAliases' => false,
           'constUses' => 
          array (
          ),
           'typeAliasClassName' => 'Illuminate\\Database\\Eloquent\\SoftDeletes',
           'traitData' => NULL,
        )),
         'typeAliasesMap' => 
        array (
        ),
         'bypassTypeAliases' => false,
         'constUses' => 
        array (
        ),
         'typeAliasClassName' => 'Illuminate\\Database\\Eloquent\\SoftDeletes',
         'traitData' => 
        array (
          0 => '/var/www/html/app/Models/User.php',
          1 => 'App\\Models\\User',
          2 => 'Illuminate\\Database\\Eloquent\\SoftDeletes',
          3 => NULL,
          4 => NULL,
        ),
      )),
      'fab07f317ee021a1d8738708513fc7bf' => 
      \PHPStan\Analyser\IntermediaryNameScope::__set_state(array(
         'namespace' => 'Illuminate\\Database\\Eloquent',
         'uses' => 
        array (
          'eloquentcollection' => 'Illuminate\\Database\\Eloquent\\Collection',
          'basecollection' => 'Illuminate\\Support\\Collection',
        ),
         'className' => 'App\\Models\\User',
         'functionName' => 'forceDelete',
         'templatePhpDocNodes' => 
        array (
        ),
         'parent' => 
        \PHPStan\Analyser\IntermediaryNameScope::__set_state(array(
           'namespace' => 'Illuminate\\Database\\Eloquent',
           'uses' => 
          array (
            'eloquentcollection' => 'Illuminate\\Database\\Eloquent\\Collection',
            'basecollection' => 'Illuminate\\Support\\Collection',
          ),
           'className' => 'App\\Models\\User',
           'functionName' => NULL,
           'templatePhpDocNodes' => 
          array (
          ),
           'parent' => NULL,
           'typeAliasesMap' => 
          array (
          ),
           'bypassTypeAliases' => false,
           'constUses' => 
          array (
          ),
           'typeAliasClassName' => 'Illuminate\\Database\\Eloquent\\SoftDeletes',
           'traitData' => NULL,
        )),
         'typeAliasesMap' => 
        array (
        ),
         'bypassTypeAliases' => false,
         'constUses' => 
        array (
        ),
         'typeAliasClassName' => 'Illuminate\\Database\\Eloquent\\SoftDeletes',
         'traitData' => 
        array (
          0 => '/var/www/html/app/Models/User.php',
          1 => 'App\\Models\\User',
          2 => 'Illuminate\\Database\\Eloquent\\SoftDeletes',
          3 => NULL,
          4 => NULL,
        ),
      )),
      'fb63d125cf428627c098b87b7e3def20' => 
      \PHPStan\Analyser\IntermediaryNameScope::__set_state(array(
         'namespace' => 'Illuminate\\Database\\Eloquent',
         'uses' => 
        array (
          'eloquentcollection' => 'Illuminate\\Database\\Eloquent\\Collection',
          'basecollection' => 'Illuminate\\Support\\Collection',
        ),
         'className' => 'App\\Models\\User',
         'functionName' => 'forceDeleteQuietly',
         'templatePhpDocNodes' => 
        array (
        ),
         'parent' => 
        \PHPStan\Analyser\IntermediaryNameScope::__set_state(array(
           'namespace' => 'Illuminate\\Database\\Eloquent',
           'uses' => 
          array (
            'eloquentcollection' => 'Illuminate\\Database\\Eloquent\\Collection',
            'basecollection' => 'Illuminate\\Support\\Collection',
          ),
           'className' => 'App\\Models\\User',
           'functionName' => NULL,
           'templatePhpDocNodes' => 
          array (
          ),
           'parent' => NULL,
           'typeAliasesMap' => 
          array (
          ),
           'bypassTypeAliases' => false,
           'constUses' => 
          array (
          ),
           'typeAliasClassName' => 'Illuminate\\Database\\Eloquent\\SoftDeletes',
           'traitData' => NULL,
        )),
         'typeAliasesMap' => 
        array (
        ),
         'bypassTypeAliases' => false,
         'constUses' => 
        array (
        ),
         'typeAliasClassName' => 'Illuminate\\Database\\Eloquent\\SoftDeletes',
         'traitData' => 
        array (
          0 => '/var/www/html/app/Models/User.php',
          1 => 'App\\Models\\User',
          2 => 'Illuminate\\Database\\Eloquent\\SoftDeletes',
          3 => NULL,
          4 => NULL,
        ),
      )),
      '5079eced9deeceff67efac507a9eef73' => 
      \PHPStan\Analyser\IntermediaryNameScope::__set_state(array(
         'namespace' => 'Illuminate\\Database\\Eloquent',
         'uses' => 
        array (
          'eloquentcollection' => 'Illuminate\\Database\\Eloquent\\Collection',
          'basecollection' => 'Illuminate\\Support\\Collection',
        ),
         'className' => 'App\\Models\\User',
         'functionName' => 'forceDestroy',
         'templatePhpDocNodes' => 
        array (
        ),
         'parent' => 
        \PHPStan\Analyser\IntermediaryNameScope::__set_state(array(
           'namespace' => 'Illuminate\\Database\\Eloquent',
           'uses' => 
          array (
            'eloquentcollection' => 'Illuminate\\Database\\Eloquent\\Collection',
            'basecollection' => 'Illuminate\\Support\\Collection',
          ),
           'className' => 'App\\Models\\User',
           'functionName' => NULL,
           'templatePhpDocNodes' => 
          array (
          ),
           'parent' => NULL,
           'typeAliasesMap' => 
          array (
          ),
           'bypassTypeAliases' => false,
           'constUses' => 
          array (
          ),
           'typeAliasClassName' => 'Illuminate\\Database\\Eloquent\\SoftDeletes',
           'traitData' => NULL,
        )),
         'typeAliasesMap' => 
        array (
        ),
         'bypassTypeAliases' => false,
         'constUses' => 
        array (
        ),
         'typeAliasClassName' => 'Illuminate\\Database\\Eloquent\\SoftDeletes',
         'traitData' => 
        array (
          0 => '/var/www/html/app/Models/User.php',
          1 => 'App\\Models\\User',
          2 => 'Illuminate\\Database\\Eloquent\\SoftDeletes',
          3 => NULL,
          4 => NULL,
        ),
      )),
      'ebb129a24b6ccdca977056b7074c4a5c' => 
      \PHPStan\Analyser\IntermediaryNameScope::__set_state(array(
         'namespace' => 'Illuminate\\Database\\Eloquent',
         'uses' => 
        array (
          'eloquentcollection' => 'Illuminate\\Database\\Eloquent\\Collection',
          'basecollection' => 'Illuminate\\Support\\Collection',
        ),
         'className' => 'App\\Models\\User',
         'functionName' => 'performDeleteOnModel',
         'templatePhpDocNodes' => 
        array (
        ),
         'parent' => 
        \PHPStan\Analyser\IntermediaryNameScope::__set_state(array(
           'namespace' => 'Illuminate\\Database\\Eloquent',
           'uses' => 
          array (
            'eloquentcollection' => 'Illuminate\\Database\\Eloquent\\Collection',
            'basecollection' => 'Illuminate\\Support\\Collection',
          ),
           'className' => 'App\\Models\\User',
           'functionName' => NULL,
           'templatePhpDocNodes' => 
          array (
          ),
           'parent' => NULL,
           'typeAliasesMap' => 
          array (
          ),
           'bypassTypeAliases' => false,
           'constUses' => 
          array (
          ),
           'typeAliasClassName' => 'Illuminate\\Database\\Eloquent\\SoftDeletes',
           'traitData' => NULL,
        )),
         'typeAliasesMap' => 
        array (
        ),
         'bypassTypeAliases' => false,
         'constUses' => 
        array (
        ),
         'typeAliasClassName' => 'Illuminate\\Database\\Eloquent\\SoftDeletes',
         'traitData' => 
        array (
          0 => '/var/www/html/app/Models/User.php',
          1 => 'App\\Models\\User',
          2 => 'Illuminate\\Database\\Eloquent\\SoftDeletes',
          3 => NULL,
          4 => NULL,
        ),
      )),
      '4c8b6a1c5753925fc0a7c1237a1adb3e' => 
      \PHPStan\Analyser\IntermediaryNameScope::__set_state(array(
         'namespace' => 'Illuminate\\Database\\Eloquent',
         'uses' => 
        array (
          'eloquentcollection' => 'Illuminate\\Database\\Eloquent\\Collection',
          'basecollection' => 'Illuminate\\Support\\Collection',
        ),
         'className' => 'App\\Models\\User',
         'functionName' => 'runSoftDelete',
         'templatePhpDocNodes' => 
        array (
        ),
         'parent' => 
        \PHPStan\Analyser\IntermediaryNameScope::__set_state(array(
           'namespace' => 'Illuminate\\Database\\Eloquent',
           'uses' => 
          array (
            'eloquentcollection' => 'Illuminate\\Database\\Eloquent\\Collection',
            'basecollection' => 'Illuminate\\Support\\Collection',
          ),
           'className' => 'App\\Models\\User',
           'functionName' => NULL,
           'templatePhpDocNodes' => 
          array (
          ),
           'parent' => NULL,
           'typeAliasesMap' => 
          array (
          ),
           'bypassTypeAliases' => false,
           'constUses' => 
          array (
          ),
           'typeAliasClassName' => 'Illuminate\\Database\\Eloquent\\SoftDeletes',
           'traitData' => NULL,
        )),
         'typeAliasesMap' => 
        array (
        ),
         'bypassTypeAliases' => false,
         'constUses' => 
        array (
        ),
         'typeAliasClassName' => 'Illuminate\\Database\\Eloquent\\SoftDeletes',
         'traitData' => 
        array (
          0 => '/var/www/html/app/Models/User.php',
          1 => 'App\\Models\\User',
          2 => 'Illuminate\\Database\\Eloquent\\SoftDeletes',
          3 => NULL,
          4 => NULL,
        ),
      )),
      '2aeb7c7e8579098449b009b4044b8158' => 
      \PHPStan\Analyser\IntermediaryNameScope::__set_state(array(
         'namespace' => 'Illuminate\\Database\\Eloquent',
         'uses' => 
        array (
          'eloquentcollection' => 'Illuminate\\Database\\Eloquent\\Collection',
          'basecollection' => 'Illuminate\\Support\\Collection',
        ),
         'className' => 'App\\Models\\User',
         'functionName' => 'restore',
         'templatePhpDocNodes' => 
        array (
        ),
         'parent' => 
        \PHPStan\Analyser\IntermediaryNameScope::__set_state(array(
           'namespace' => 'Illuminate\\Database\\Eloquent',
           'uses' => 
          array (
            'eloquentcollection' => 'Illuminate\\Database\\Eloquent\\Collection',
            'basecollection' => 'Illuminate\\Support\\Collection',
          ),
           'className' => 'App\\Models\\User',
           'functionName' => NULL,
           'templatePhpDocNodes' => 
          array (
          ),
           'parent' => NULL,
           'typeAliasesMap' => 
          array (
          ),
           'bypassTypeAliases' => false,
           'constUses' => 
          array (
          ),
           'typeAliasClassName' => 'Illuminate\\Database\\Eloquent\\SoftDeletes',
           'traitData' => NULL,
        )),
         'typeAliasesMap' => 
        array (
        ),
         'bypassTypeAliases' => false,
         'constUses' => 
        array (
        ),
         'typeAliasClassName' => 'Illuminate\\Database\\Eloquent\\SoftDeletes',
         'traitData' => 
        array (
          0 => '/var/www/html/app/Models/User.php',
          1 => 'App\\Models\\User',
          2 => 'Illuminate\\Database\\Eloquent\\SoftDeletes',
          3 => NULL,
          4 => NULL,
        ),
      )),
      '458cada751904f2db13fa5cb215ade1f' => 
      \PHPStan\Analyser\IntermediaryNameScope::__set_state(array(
         'namespace' => 'Illuminate\\Database\\Eloquent',
         'uses' => 
        array (
          'eloquentcollection' => 'Illuminate\\Database\\Eloquent\\Collection',
          'basecollection' => 'Illuminate\\Support\\Collection',
        ),
         'className' => 'App\\Models\\User',
         'functionName' => 'restoreQuietly',
         'templatePhpDocNodes' => 
        array (
        ),
         'parent' => 
        \PHPStan\Analyser\IntermediaryNameScope::__set_state(array(
           'namespace' => 'Illuminate\\Database\\Eloquent',
           'uses' => 
          array (
            'eloquentcollection' => 'Illuminate\\Database\\Eloquent\\Collection',
            'basecollection' => 'Illuminate\\Support\\Collection',
          ),
           'className' => 'App\\Models\\User',
           'functionName' => NULL,
           'templatePhpDocNodes' => 
          array (
          ),
           'parent' => NULL,
           'typeAliasesMap' => 
          array (
          ),
           'bypassTypeAliases' => false,
           'constUses' => 
          array (
          ),
           'typeAliasClassName' => 'Illuminate\\Database\\Eloquent\\SoftDeletes',
           'traitData' => NULL,
        )),
         'typeAliasesMap' => 
        array (
        ),
         'bypassTypeAliases' => false,
         'constUses' => 
        array (
        ),
         'typeAliasClassName' => 'Illuminate\\Database\\Eloquent\\SoftDeletes',
         'traitData' => 
        array (
          0 => '/var/www/html/app/Models/User.php',
          1 => 'App\\Models\\User',
          2 => 'Illuminate\\Database\\Eloquent\\SoftDeletes',
          3 => NULL,
          4 => NULL,
        ),
      )),
      '90f5807dfdf2113d5672ffb92074f7f9' => 
      \PHPStan\Analyser\IntermediaryNameScope::__set_state(array(
         'namespace' => 'Illuminate\\Database\\Eloquent',
         'uses' => 
        array (
          'eloquentcollection' => 'Illuminate\\Database\\Eloquent\\Collection',
          'basecollection' => 'Illuminate\\Support\\Collection',
        ),
         'className' => 'App\\Models\\User',
         'functionName' => 'trashed',
         'templatePhpDocNodes' => 
        array (
        ),
         'parent' => 
        \PHPStan\Analyser\IntermediaryNameScope::__set_state(array(
           'namespace' => 'Illuminate\\Database\\Eloquent',
           'uses' => 
          array (
            'eloquentcollection' => 'Illuminate\\Database\\Eloquent\\Collection',
            'basecollection' => 'Illuminate\\Support\\Collection',
          ),
           'className' => 'App\\Models\\User',
           'functionName' => NULL,
           'templatePhpDocNodes' => 
          array (
          ),
           'parent' => NULL,
           'typeAliasesMap' => 
          array (
          ),
           'bypassTypeAliases' => false,
           'constUses' => 
          array (
          ),
           'typeAliasClassName' => 'Illuminate\\Database\\Eloquent\\SoftDeletes',
           'traitData' => NULL,
        )),
         'typeAliasesMap' => 
        array (
        ),
         'bypassTypeAliases' => false,
         'constUses' => 
        array (
        ),
         'typeAliasClassName' => 'Illuminate\\Database\\Eloquent\\SoftDeletes',
         'traitData' => 
        array (
          0 => '/var/www/html/app/Models/User.php',
          1 => 'App\\Models\\User',
          2 => 'Illuminate\\Database\\Eloquent\\SoftDeletes',
          3 => NULL,
          4 => NULL,
        ),
      )),
      '810c17c851a1930713072cf0068425c8' => 
      \PHPStan\Analyser\IntermediaryNameScope::__set_state(array(
         'namespace' => 'Illuminate\\Database\\Eloquent',
         'uses' => 
        array (
          'eloquentcollection' => 'Illuminate\\Database\\Eloquent\\Collection',
          'basecollection' => 'Illuminate\\Support\\Collection',
        ),
         'className' => 'App\\Models\\User',
         'functionName' => 'softDeleted',
         'templatePhpDocNodes' => 
        array (
        ),
         'parent' => 
        \PHPStan\Analyser\IntermediaryNameScope::__set_state(array(
           'namespace' => 'Illuminate\\Database\\Eloquent',
           'uses' => 
          array (
            'eloquentcollection' => 'Illuminate\\Database\\Eloquent\\Collection',
            'basecollection' => 'Illuminate\\Support\\Collection',
          ),
           'className' => 'App\\Models\\User',
           'functionName' => NULL,
           'templatePhpDocNodes' => 
          array (
          ),
           'parent' => NULL,
           'typeAliasesMap' => 
          array (
          ),
           'bypassTypeAliases' => false,
           'constUses' => 
          array (
          ),
           'typeAliasClassName' => 'Illuminate\\Database\\Eloquent\\SoftDeletes',
           'traitData' => NULL,
        )),
         'typeAliasesMap' => 
        array (
        ),
         'bypassTypeAliases' => false,
         'constUses' => 
        array (
        ),
         'typeAliasClassName' => 'Illuminate\\Database\\Eloquent\\SoftDeletes',
         'traitData' => 
        array (
          0 => '/var/www/html/app/Models/User.php',
          1 => 'App\\Models\\User',
          2 => 'Illuminate\\Database\\Eloquent\\SoftDeletes',
          3 => NULL,
          4 => NULL,
        ),
      )),
      'aaedb5618a6fa07e0fe2de70580c66fb' => 
      \PHPStan\Analyser\IntermediaryNameScope::__set_state(array(
         'namespace' => 'Illuminate\\Database\\Eloquent',
         'uses' => 
        array (
          'eloquentcollection' => 'Illuminate\\Database\\Eloquent\\Collection',
          'basecollection' => 'Illuminate\\Support\\Collection',
        ),
         'className' => 'App\\Models\\User',
         'functionName' => 'restoring',
         'templatePhpDocNodes' => 
        array (
        ),
         'parent' => 
        \PHPStan\Analyser\IntermediaryNameScope::__set_state(array(
           'namespace' => 'Illuminate\\Database\\Eloquent',
           'uses' => 
          array (
            'eloquentcollection' => 'Illuminate\\Database\\Eloquent\\Collection',
            'basecollection' => 'Illuminate\\Support\\Collection',
          ),
           'className' => 'App\\Models\\User',
           'functionName' => NULL,
           'templatePhpDocNodes' => 
          array (
          ),
           'parent' => NULL,
           'typeAliasesMap' => 
          array (
          ),
           'bypassTypeAliases' => false,
           'constUses' => 
          array (
          ),
           'typeAliasClassName' => 'Illuminate\\Database\\Eloquent\\SoftDeletes',
           'traitData' => NULL,
        )),
         'typeAliasesMap' => 
        array (
        ),
         'bypassTypeAliases' => false,
         'constUses' => 
        array (
        ),
         'typeAliasClassName' => 'Illuminate\\Database\\Eloquent\\SoftDeletes',
         'traitData' => 
        array (
          0 => '/var/www/html/app/Models/User.php',
          1 => 'App\\Models\\User',
          2 => 'Illuminate\\Database\\Eloquent\\SoftDeletes',
          3 => NULL,
          4 => NULL,
        ),
      )),
      '3a3ca88ab68e21d0ab511666917e7a65' => 
      \PHPStan\Analyser\IntermediaryNameScope::__set_state(array(
         'namespace' => 'Illuminate\\Database\\Eloquent',
         'uses' => 
        array (
          'eloquentcollection' => 'Illuminate\\Database\\Eloquent\\Collection',
          'basecollection' => 'Illuminate\\Support\\Collection',
        ),
         'className' => 'App\\Models\\User',
         'functionName' => 'restored',
         'templatePhpDocNodes' => 
        array (
        ),
         'parent' => 
        \PHPStan\Analyser\IntermediaryNameScope::__set_state(array(
           'namespace' => 'Illuminate\\Database\\Eloquent',
           'uses' => 
          array (
            'eloquentcollection' => 'Illuminate\\Database\\Eloquent\\Collection',
            'basecollection' => 'Illuminate\\Support\\Collection',
          ),
           'className' => 'App\\Models\\User',
           'functionName' => NULL,
           'templatePhpDocNodes' => 
          array (
          ),
           'parent' => NULL,
           'typeAliasesMap' => 
          array (
          ),
           'bypassTypeAliases' => false,
           'constUses' => 
          array (
          ),
           'typeAliasClassName' => 'Illuminate\\Database\\Eloquent\\SoftDeletes',
           'traitData' => NULL,
        )),
         'typeAliasesMap' => 
        array (
        ),
         'bypassTypeAliases' => false,
         'constUses' => 
        array (
        ),
         'typeAliasClassName' => 'Illuminate\\Database\\Eloquent\\SoftDeletes',
         'traitData' => 
        array (
          0 => '/var/www/html/app/Models/User.php',
          1 => 'App\\Models\\User',
          2 => 'Illuminate\\Database\\Eloquent\\SoftDeletes',
          3 => NULL,
          4 => NULL,
        ),
      )),
      '524664670dcaf32ebd905d667ce99302' => 
      \PHPStan\Analyser\IntermediaryNameScope::__set_state(array(
         'namespace' => 'Illuminate\\Database\\Eloquent',
         'uses' => 
        array (
          'eloquentcollection' => 'Illuminate\\Database\\Eloquent\\Collection',
          'basecollection' => 'Illuminate\\Support\\Collection',
        ),
         'className' => 'App\\Models\\User',
         'functionName' => 'forceDeleting',
         'templatePhpDocNodes' => 
        array (
        ),
         'parent' => 
        \PHPStan\Analyser\IntermediaryNameScope::__set_state(array(
           'namespace' => 'Illuminate\\Database\\Eloquent',
           'uses' => 
          array (
            'eloquentcollection' => 'Illuminate\\Database\\Eloquent\\Collection',
            'basecollection' => 'Illuminate\\Support\\Collection',
          ),
           'className' => 'App\\Models\\User',
           'functionName' => NULL,
           'templatePhpDocNodes' => 
          array (
          ),
           'parent' => NULL,
           'typeAliasesMap' => 
          array (
          ),
           'bypassTypeAliases' => false,
           'constUses' => 
          array (
          ),
           'typeAliasClassName' => 'Illuminate\\Database\\Eloquent\\SoftDeletes',
           'traitData' => NULL,
        )),
         'typeAliasesMap' => 
        array (
        ),
         'bypassTypeAliases' => false,
         'constUses' => 
        array (
        ),
         'typeAliasClassName' => 'Illuminate\\Database\\Eloquent\\SoftDeletes',
         'traitData' => 
        array (
          0 => '/var/www/html/app/Models/User.php',
          1 => 'App\\Models\\User',
          2 => 'Illuminate\\Database\\Eloquent\\SoftDeletes',
          3 => NULL,
          4 => NULL,
        ),
      )),
      '0bc658d9b2ad810822c6f416066f16b0' => 
      \PHPStan\Analyser\IntermediaryNameScope::__set_state(array(
         'namespace' => 'Illuminate\\Database\\Eloquent',
         'uses' => 
        array (
          'eloquentcollection' => 'Illuminate\\Database\\Eloquent\\Collection',
          'basecollection' => 'Illuminate\\Support\\Collection',
        ),
         'className' => 'App\\Models\\User',
         'functionName' => 'forceDeleted',
         'templatePhpDocNodes' => 
        array (
        ),
         'parent' => 
        \PHPStan\Analyser\IntermediaryNameScope::__set_state(array(
           'namespace' => 'Illuminate\\Database\\Eloquent',
           'uses' => 
          array (
            'eloquentcollection' => 'Illuminate\\Database\\Eloquent\\Collection',
            'basecollection' => 'Illuminate\\Support\\Collection',
          ),
           'className' => 'App\\Models\\User',
           'functionName' => NULL,
           'templatePhpDocNodes' => 
          array (
          ),
           'parent' => NULL,
           'typeAliasesMap' => 
          array (
          ),
           'bypassTypeAliases' => false,
           'constUses' => 
          array (
          ),
           'typeAliasClassName' => 'Illuminate\\Database\\Eloquent\\SoftDeletes',
           'traitData' => NULL,
        )),
         'typeAliasesMap' => 
        array (
        ),
         'bypassTypeAliases' => false,
         'constUses' => 
        array (
        ),
         'typeAliasClassName' => 'Illuminate\\Database\\Eloquent\\SoftDeletes',
         'traitData' => 
        array (
          0 => '/var/www/html/app/Models/User.php',
          1 => 'App\\Models\\User',
          2 => 'Illuminate\\Database\\Eloquent\\SoftDeletes',
          3 => NULL,
          4 => NULL,
        ),
      )),
      'd7c6066f15a78c17def83b7a0880f8c8' => 
      \PHPStan\Analyser\IntermediaryNameScope::__set_state(array(
         'namespace' => 'Illuminate\\Database\\Eloquent',
         'uses' => 
        array (
          'eloquentcollection' => 'Illuminate\\Database\\Eloquent\\Collection',
          'basecollection' => 'Illuminate\\Support\\Collection',
        ),
         'className' => 'App\\Models\\User',
         'functionName' => 'isForceDeleting',
         'templatePhpDocNodes' => 
        array (
        ),
         'parent' => 
        \PHPStan\Analyser\IntermediaryNameScope::__set_state(array(
           'namespace' => 'Illuminate\\Database\\Eloquent',
           'uses' => 
          array (
            'eloquentcollection' => 'Illuminate\\Database\\Eloquent\\Collection',
            'basecollection' => 'Illuminate\\Support\\Collection',
          ),
           'className' => 'App\\Models\\User',
           'functionName' => NULL,
           'templatePhpDocNodes' => 
          array (
          ),
           'parent' => NULL,
           'typeAliasesMap' => 
          array (
          ),
           'bypassTypeAliases' => false,
           'constUses' => 
          array (
          ),
           'typeAliasClassName' => 'Illuminate\\Database\\Eloquent\\SoftDeletes',
           'traitData' => NULL,
        )),
         'typeAliasesMap' => 
        array (
        ),
         'bypassTypeAliases' => false,
         'constUses' => 
        array (
        ),
         'typeAliasClassName' => 'Illuminate\\Database\\Eloquent\\SoftDeletes',
         'traitData' => 
        array (
          0 => '/var/www/html/app/Models/User.php',
          1 => 'App\\Models\\User',
          2 => 'Illuminate\\Database\\Eloquent\\SoftDeletes',
          3 => NULL,
          4 => NULL,
        ),
      )),
      '9f24dbb1572fb833f60da0eec9ad62c7' => 
      \PHPStan\Analyser\IntermediaryNameScope::__set_state(array(
         'namespace' => 'Illuminate\\Database\\Eloquent',
         'uses' => 
        array (
          'eloquentcollection' => 'Illuminate\\Database\\Eloquent\\Collection',
          'basecollection' => 'Illuminate\\Support\\Collection',
        ),
         'className' => 'App\\Models\\User',
         'functionName' => 'getDeletedAtColumn',
         'templatePhpDocNodes' => 
        array (
        ),
         'parent' => 
        \PHPStan\Analyser\IntermediaryNameScope::__set_state(array(
           'namespace' => 'Illuminate\\Database\\Eloquent',
           'uses' => 
          array (
            'eloquentcollection' => 'Illuminate\\Database\\Eloquent\\Collection',
            'basecollection' => 'Illuminate\\Support\\Collection',
          ),
           'className' => 'App\\Models\\User',
           'functionName' => NULL,
           'templatePhpDocNodes' => 
          array (
          ),
           'parent' => NULL,
           'typeAliasesMap' => 
          array (
          ),
           'bypassTypeAliases' => false,
           'constUses' => 
          array (
          ),
           'typeAliasClassName' => 'Illuminate\\Database\\Eloquent\\SoftDeletes',
           'traitData' => NULL,
        )),
         'typeAliasesMap' => 
        array (
        ),
         'bypassTypeAliases' => false,
         'constUses' => 
        array (
        ),
         'typeAliasClassName' => 'Illuminate\\Database\\Eloquent\\SoftDeletes',
         'traitData' => 
        array (
          0 => '/var/www/html/app/Models/User.php',
          1 => 'App\\Models\\User',
          2 => 'Illuminate\\Database\\Eloquent\\SoftDeletes',
          3 => NULL,
          4 => NULL,
        ),
      )),
      'ffec0ade8799236cee6a94691faf578a' => 
      \PHPStan\Analyser\IntermediaryNameScope::__set_state(array(
         'namespace' => 'Illuminate\\Database\\Eloquent',
         'uses' => 
        array (
          'eloquentcollection' => 'Illuminate\\Database\\Eloquent\\Collection',
          'basecollection' => 'Illuminate\\Support\\Collection',
        ),
         'className' => 'App\\Models\\User',
         'functionName' => 'getQualifiedDeletedAtColumn',
         'templatePhpDocNodes' => 
        array (
        ),
         'parent' => 
        \PHPStan\Analyser\IntermediaryNameScope::__set_state(array(
           'namespace' => 'Illuminate\\Database\\Eloquent',
           'uses' => 
          array (
            'eloquentcollection' => 'Illuminate\\Database\\Eloquent\\Collection',
            'basecollection' => 'Illuminate\\Support\\Collection',
          ),
           'className' => 'App\\Models\\User',
           'functionName' => NULL,
           'templatePhpDocNodes' => 
          array (
          ),
           'parent' => NULL,
           'typeAliasesMap' => 
          array (
          ),
           'bypassTypeAliases' => false,
           'constUses' => 
          array (
          ),
           'typeAliasClassName' => 'Illuminate\\Database\\Eloquent\\SoftDeletes',
           'traitData' => NULL,
        )),
         'typeAliasesMap' => 
        array (
        ),
         'bypassTypeAliases' => false,
         'constUses' => 
        array (
        ),
         'typeAliasClassName' => 'Illuminate\\Database\\Eloquent\\SoftDeletes',
         'traitData' => 
        array (
          0 => '/var/www/html/app/Models/User.php',
          1 => 'App\\Models\\User',
          2 => 'Illuminate\\Database\\Eloquent\\SoftDeletes',
          3 => NULL,
          4 => NULL,
        ),
      )),
      'eddca82c21e2fbf85c267e503b423a94' => 
      \PHPStan\Analyser\IntermediaryNameScope::__set_state(array(
         'namespace' => 'Laravel\\Fortify',
         'uses' => 
        array (
          'rgb' => 'BaconQrCode\\Renderer\\Color\\Rgb',
          'svgimagebackend' => 'BaconQrCode\\Renderer\\Image\\SvgImageBackEnd',
          'imagerenderer' => 'BaconQrCode\\Renderer\\ImageRenderer',
          'fill' => 'BaconQrCode\\Renderer\\RendererStyle\\Fill',
          'rendererstyle' => 'BaconQrCode\\Renderer\\RendererStyle\\RendererStyle',
          'writer' => 'BaconQrCode\\Writer',
          'twofactorauthenticationprovider' => 'Laravel\\Fortify\\Contracts\\TwoFactorAuthenticationProvider',
          'recoverycodereplaced' => 'Laravel\\Fortify\\Events\\RecoveryCodeReplaced',
        ),
         'className' => 'App\\Models\\User',
         'functionName' => NULL,
         'templatePhpDocNodes' => 
        array (
        ),
         'parent' => NULL,
         'typeAliasesMap' => 
        array (
        ),
         'bypassTypeAliases' => false,
         'constUses' => 
        array (
        ),
         'typeAliasClassName' => 'Laravel\\Fortify\\TwoFactorAuthenticatable',
         'traitData' => 
        array (
          0 => '/var/www/html/app/Models/User.php',
          1 => 'App\\Models\\User',
          2 => 'Laravel\\Fortify\\TwoFactorAuthenticatable',
          3 => NULL,
          4 => NULL,
        ),
      )),
      'e8c389700015120393a6c682a8bbe3be' => 
      \PHPStan\Analyser\IntermediaryNameScope::__set_state(array(
         'namespace' => 'Laravel\\Fortify',
         'uses' => 
        array (
          'rgb' => 'BaconQrCode\\Renderer\\Color\\Rgb',
          'svgimagebackend' => 'BaconQrCode\\Renderer\\Image\\SvgImageBackEnd',
          'imagerenderer' => 'BaconQrCode\\Renderer\\ImageRenderer',
          'fill' => 'BaconQrCode\\Renderer\\RendererStyle\\Fill',
          'rendererstyle' => 'BaconQrCode\\Renderer\\RendererStyle\\RendererStyle',
          'writer' => 'BaconQrCode\\Writer',
          'twofactorauthenticationprovider' => 'Laravel\\Fortify\\Contracts\\TwoFactorAuthenticationProvider',
          'recoverycodereplaced' => 'Laravel\\Fortify\\Events\\RecoveryCodeReplaced',
        ),
         'className' => 'App\\Models\\User',
         'functionName' => 'hasEnabledTwoFactorAuthentication',
         'templatePhpDocNodes' => 
        array (
        ),
         'parent' => NULL,
         'typeAliasesMap' => 
        array (
        ),
         'bypassTypeAliases' => false,
         'constUses' => 
        array (
        ),
         'typeAliasClassName' => 'Laravel\\Fortify\\TwoFactorAuthenticatable',
         'traitData' => 
        array (
          0 => '/var/www/html/app/Models/User.php',
          1 => 'App\\Models\\User',
          2 => 'Laravel\\Fortify\\TwoFactorAuthenticatable',
          3 => NULL,
          4 => NULL,
        ),
      )),
      'e42a8258c925c68e8602e0b2280b8d44' => 
      \PHPStan\Analyser\IntermediaryNameScope::__set_state(array(
         'namespace' => 'Laravel\\Fortify',
         'uses' => 
        array (
          'rgb' => 'BaconQrCode\\Renderer\\Color\\Rgb',
          'svgimagebackend' => 'BaconQrCode\\Renderer\\Image\\SvgImageBackEnd',
          'imagerenderer' => 'BaconQrCode\\Renderer\\ImageRenderer',
          'fill' => 'BaconQrCode\\Renderer\\RendererStyle\\Fill',
          'rendererstyle' => 'BaconQrCode\\Renderer\\RendererStyle\\RendererStyle',
          'writer' => 'BaconQrCode\\Writer',
          'twofactorauthenticationprovider' => 'Laravel\\Fortify\\Contracts\\TwoFactorAuthenticationProvider',
          'recoverycodereplaced' => 'Laravel\\Fortify\\Events\\RecoveryCodeReplaced',
        ),
         'className' => 'App\\Models\\User',
         'functionName' => 'recoveryCodes',
         'templatePhpDocNodes' => 
        array (
        ),
         'parent' => NULL,
         'typeAliasesMap' => 
        array (
        ),
         'bypassTypeAliases' => false,
         'constUses' => 
        array (
        ),
         'typeAliasClassName' => 'Laravel\\Fortify\\TwoFactorAuthenticatable',
         'traitData' => 
        array (
          0 => '/var/www/html/app/Models/User.php',
          1 => 'App\\Models\\User',
          2 => 'Laravel\\Fortify\\TwoFactorAuthenticatable',
          3 => NULL,
          4 => NULL,
        ),
      )),
      '49d96d51605e7468c44e2f0a42872502' => 
      \PHPStan\Analyser\IntermediaryNameScope::__set_state(array(
         'namespace' => 'Laravel\\Fortify',
         'uses' => 
        array (
          'rgb' => 'BaconQrCode\\Renderer\\Color\\Rgb',
          'svgimagebackend' => 'BaconQrCode\\Renderer\\Image\\SvgImageBackEnd',
          'imagerenderer' => 'BaconQrCode\\Renderer\\ImageRenderer',
          'fill' => 'BaconQrCode\\Renderer\\RendererStyle\\Fill',
          'rendererstyle' => 'BaconQrCode\\Renderer\\RendererStyle\\RendererStyle',
          'writer' => 'BaconQrCode\\Writer',
          'twofactorauthenticationprovider' => 'Laravel\\Fortify\\Contracts\\TwoFactorAuthenticationProvider',
          'recoverycodereplaced' => 'Laravel\\Fortify\\Events\\RecoveryCodeReplaced',
        ),
         'className' => 'App\\Models\\User',
         'functionName' => 'replaceRecoveryCode',
         'templatePhpDocNodes' => 
        array (
        ),
         'parent' => NULL,
         'typeAliasesMap' => 
        array (
        ),
         'bypassTypeAliases' => false,
         'constUses' => 
        array (
        ),
         'typeAliasClassName' => 'Laravel\\Fortify\\TwoFactorAuthenticatable',
         'traitData' => 
        array (
          0 => '/var/www/html/app/Models/User.php',
          1 => 'App\\Models\\User',
          2 => 'Laravel\\Fortify\\TwoFactorAuthenticatable',
          3 => NULL,
          4 => NULL,
        ),
      )),
      '86039ebd6470517f35692929620721b3' => 
      \PHPStan\Analyser\IntermediaryNameScope::__set_state(array(
         'namespace' => 'Laravel\\Fortify',
         'uses' => 
        array (
          'rgb' => 'BaconQrCode\\Renderer\\Color\\Rgb',
          'svgimagebackend' => 'BaconQrCode\\Renderer\\Image\\SvgImageBackEnd',
          'imagerenderer' => 'BaconQrCode\\Renderer\\ImageRenderer',
          'fill' => 'BaconQrCode\\Renderer\\RendererStyle\\Fill',
          'rendererstyle' => 'BaconQrCode\\Renderer\\RendererStyle\\RendererStyle',
          'writer' => 'BaconQrCode\\Writer',
          'twofactorauthenticationprovider' => 'Laravel\\Fortify\\Contracts\\TwoFactorAuthenticationProvider',
          'recoverycodereplaced' => 'Laravel\\Fortify\\Events\\RecoveryCodeReplaced',
        ),
         'className' => 'App\\Models\\User',
         'functionName' => 'twoFactorQrCodeSvg',
         'templatePhpDocNodes' => 
        array (
        ),
         'parent' => NULL,
         'typeAliasesMap' => 
        array (
        ),
         'bypassTypeAliases' => false,
         'constUses' => 
        array (
        ),
         'typeAliasClassName' => 'Laravel\\Fortify\\TwoFactorAuthenticatable',
         'traitData' => 
        array (
          0 => '/var/www/html/app/Models/User.php',
          1 => 'App\\Models\\User',
          2 => 'Laravel\\Fortify\\TwoFactorAuthenticatable',
          3 => NULL,
          4 => NULL,
        ),
      )),
      '2ea1249e8b29a9ea1c2c6f25bc2d5920' => 
      \PHPStan\Analyser\IntermediaryNameScope::__set_state(array(
         'namespace' => 'Laravel\\Fortify',
         'uses' => 
        array (
          'rgb' => 'BaconQrCode\\Renderer\\Color\\Rgb',
          'svgimagebackend' => 'BaconQrCode\\Renderer\\Image\\SvgImageBackEnd',
          'imagerenderer' => 'BaconQrCode\\Renderer\\ImageRenderer',
          'fill' => 'BaconQrCode\\Renderer\\RendererStyle\\Fill',
          'rendererstyle' => 'BaconQrCode\\Renderer\\RendererStyle\\RendererStyle',
          'writer' => 'BaconQrCode\\Writer',
          'twofactorauthenticationprovider' => 'Laravel\\Fortify\\Contracts\\TwoFactorAuthenticationProvider',
          'recoverycodereplaced' => 'Laravel\\Fortify\\Events\\RecoveryCodeReplaced',
        ),
         'className' => 'App\\Models\\User',
         'functionName' => 'twoFactorQrCodeUrl',
         'templatePhpDocNodes' => 
        array (
        ),
         'parent' => NULL,
         'typeAliasesMap' => 
        array (
        ),
         'bypassTypeAliases' => false,
         'constUses' => 
        array (
        ),
         'typeAliasClassName' => 'Laravel\\Fortify\\TwoFactorAuthenticatable',
         'traitData' => 
        array (
          0 => '/var/www/html/app/Models/User.php',
          1 => 'App\\Models\\User',
          2 => 'Laravel\\Fortify\\TwoFactorAuthenticatable',
          3 => NULL,
          4 => NULL,
        ),
      )),
      '881c2edfd5c648c420853ac5c55586db' => 
      \PHPStan\Analyser\IntermediaryNameScope::__set_state(array(
         'namespace' => 'App\\Models',
         'uses' => 
        array (
          'accountstatus' => 'App\\Enums\\AccountStatus',
          'enrollmentstatus' => 'App\\Enums\\EnrollmentStatus',
          'organizationmembershipstatus' => 'App\\Enums\\OrganizationMembershipStatus',
          'role' => 'App\\Enums\\Role',
          'schoolmembershipstatus' => 'App\\Enums\\SchoolMembershipStatus',
          'carbon' => 'Carbon\\Carbon',
          'mustverifyemail' => 'Illuminate\\Contracts\\Auth\\MustVerifyEmail',
          'builder' => 'Illuminate\\Database\\Eloquent\\Builder',
          'hasfactory' => 'Illuminate\\Database\\Eloquent\\Factories\\HasFactory',
          'belongstomany' => 'Illuminate\\Database\\Eloquent\\Relations\\BelongsToMany',
          'hasmany' => 'Illuminate\\Database\\Eloquent\\Relations\\HasMany',
          'hasone' => 'Illuminate\\Database\\Eloquent\\Relations\\HasOne',
          'softdeletes' => 'Illuminate\\Database\\Eloquent\\SoftDeletes',
          'authenticatable' => 'Illuminate\\Foundation\\Auth\\User',
          'notifiable' => 'Illuminate\\Notifications\\Notifiable',
          'twofactorauthenticatable' => 'Laravel\\Fortify\\TwoFactorAuthenticatable',
          'hasprofilephoto' => 'Laravel\\Jetstream\\HasProfilePhoto',
          'hasapitokens' => 'Laravel\\Sanctum\\HasApiTokens',
          'hasroles' => 'Spatie\\Permission\\Traits\\HasRoles',
        ),
         'className' => 'App\\Models\\User',
         'functionName' => 'booted',
         'templatePhpDocNodes' => 
        array (
        ),
         'parent' => NULL,
         'typeAliasesMap' => 
        array (
        ),
         'bypassTypeAliases' => false,
         'constUses' => 
        array (
        ),
         'typeAliasClassName' => NULL,
         'traitData' => NULL,
      )),
      '14c42ffd98976b1cbd3e872e0e97c5d7' => 
      \PHPStan\Analyser\IntermediaryNameScope::__set_state(array(
         'namespace' => 'App\\Models',
         'uses' => 
        array (
          'accountstatus' => 'App\\Enums\\AccountStatus',
          'enrollmentstatus' => 'App\\Enums\\EnrollmentStatus',
          'organizationmembershipstatus' => 'App\\Enums\\OrganizationMembershipStatus',
          'role' => 'App\\Enums\\Role',
          'schoolmembershipstatus' => 'App\\Enums\\SchoolMembershipStatus',
          'carbon' => 'Carbon\\Carbon',
          'mustverifyemail' => 'Illuminate\\Contracts\\Auth\\MustVerifyEmail',
          'builder' => 'Illuminate\\Database\\Eloquent\\Builder',
          'hasfactory' => 'Illuminate\\Database\\Eloquent\\Factories\\HasFactory',
          'belongstomany' => 'Illuminate\\Database\\Eloquent\\Relations\\BelongsToMany',
          'hasmany' => 'Illuminate\\Database\\Eloquent\\Relations\\HasMany',
          'hasone' => 'Illuminate\\Database\\Eloquent\\Relations\\HasOne',
          'softdeletes' => 'Illuminate\\Database\\Eloquent\\SoftDeletes',
          'authenticatable' => 'Illuminate\\Foundation\\Auth\\User',
          'notifiable' => 'Illuminate\\Notifications\\Notifiable',
          'twofactorauthenticatable' => 'Laravel\\Fortify\\TwoFactorAuthenticatable',
          'hasprofilephoto' => 'Laravel\\Jetstream\\HasProfilePhoto',
          'hasapitokens' => 'Laravel\\Sanctum\\HasApiTokens',
          'hasroles' => 'Spatie\\Permission\\Traits\\HasRoles',
        ),
         'className' => 'App\\Models\\User',
         'functionName' => 'scopeStudents',
         'templatePhpDocNodes' => 
        array (
        ),
         'parent' => NULL,
         'typeAliasesMap' => 
        array (
        ),
         'bypassTypeAliases' => false,
         'constUses' => 
        array (
        ),
         'typeAliasClassName' => NULL,
         'traitData' => NULL,
      )),
      'ef273cf88b07a3f2615f631f10b6bda0' => 
      \PHPStan\Analyser\IntermediaryNameScope::__set_state(array(
         'namespace' => 'App\\Models',
         'uses' => 
        array (
          'accountstatus' => 'App\\Enums\\AccountStatus',
          'enrollmentstatus' => 'App\\Enums\\EnrollmentStatus',
          'organizationmembershipstatus' => 'App\\Enums\\OrganizationMembershipStatus',
          'role' => 'App\\Enums\\Role',
          'schoolmembershipstatus' => 'App\\Enums\\SchoolMembershipStatus',
          'carbon' => 'Carbon\\Carbon',
          'mustverifyemail' => 'Illuminate\\Contracts\\Auth\\MustVerifyEmail',
          'builder' => 'Illuminate\\Database\\Eloquent\\Builder',
          'hasfactory' => 'Illuminate\\Database\\Eloquent\\Factories\\HasFactory',
          'belongstomany' => 'Illuminate\\Database\\Eloquent\\Relations\\BelongsToMany',
          'hasmany' => 'Illuminate\\Database\\Eloquent\\Relations\\HasMany',
          'hasone' => 'Illuminate\\Database\\Eloquent\\Relations\\HasOne',
          'softdeletes' => 'Illuminate\\Database\\Eloquent\\SoftDeletes',
          'authenticatable' => 'Illuminate\\Foundation\\Auth\\User',
          'notifiable' => 'Illuminate\\Notifications\\Notifiable',
          'twofactorauthenticatable' => 'Laravel\\Fortify\\TwoFactorAuthenticatable',
          'hasprofilephoto' => 'Laravel\\Jetstream\\HasProfilePhoto',
          'hasapitokens' => 'Laravel\\Sanctum\\HasApiTokens',
          'hasroles' => 'Spatie\\Permission\\Traits\\HasRoles',
        ),
         'className' => 'App\\Models\\User',
         'functionName' => 'scopeActiveAccounts',
         'templatePhpDocNodes' => 
        array (
        ),
         'parent' => NULL,
         'typeAliasesMap' => 
        array (
        ),
         'bypassTypeAliases' => false,
         'constUses' => 
        array (
        ),
         'typeAliasClassName' => NULL,
         'traitData' => NULL,
      )),
      'af1558ea9a153625c150c3e781142803' => 
      \PHPStan\Analyser\IntermediaryNameScope::__set_state(array(
         'namespace' => 'App\\Models',
         'uses' => 
        array (
          'accountstatus' => 'App\\Enums\\AccountStatus',
          'enrollmentstatus' => 'App\\Enums\\EnrollmentStatus',
          'organizationmembershipstatus' => 'App\\Enums\\OrganizationMembershipStatus',
          'role' => 'App\\Enums\\Role',
          'schoolmembershipstatus' => 'App\\Enums\\SchoolMembershipStatus',
          'carbon' => 'Carbon\\Carbon',
          'mustverifyemail' => 'Illuminate\\Contracts\\Auth\\MustVerifyEmail',
          'builder' => 'Illuminate\\Database\\Eloquent\\Builder',
          'hasfactory' => 'Illuminate\\Database\\Eloquent\\Factories\\HasFactory',
          'belongstomany' => 'Illuminate\\Database\\Eloquent\\Relations\\BelongsToMany',
          'hasmany' => 'Illuminate\\Database\\Eloquent\\Relations\\HasMany',
          'hasone' => 'Illuminate\\Database\\Eloquent\\Relations\\HasOne',
          'softdeletes' => 'Illuminate\\Database\\Eloquent\\SoftDeletes',
          'authenticatable' => 'Illuminate\\Foundation\\Auth\\User',
          'notifiable' => 'Illuminate\\Notifications\\Notifiable',
          'twofactorauthenticatable' => 'Laravel\\Fortify\\TwoFactorAuthenticatable',
          'hasprofilephoto' => 'Laravel\\Jetstream\\HasProfilePhoto',
          'hasapitokens' => 'Laravel\\Sanctum\\HasApiTokens',
          'hasroles' => 'Spatie\\Permission\\Traits\\HasRoles',
        ),
         'className' => 'App\\Models\\User',
         'functionName' => 'scopeActiveStudents',
         'templatePhpDocNodes' => 
        array (
        ),
         'parent' => NULL,
         'typeAliasesMap' => 
        array (
        ),
         'bypassTypeAliases' => false,
         'constUses' => 
        array (
        ),
         'typeAliasClassName' => NULL,
         'traitData' => NULL,
      )),
      'deb022233c4d900d2384b02009570f1d' => 
      \PHPStan\Analyser\IntermediaryNameScope::__set_state(array(
         'namespace' => 'App\\Models',
         'uses' => 
        array (
          'accountstatus' => 'App\\Enums\\AccountStatus',
          'enrollmentstatus' => 'App\\Enums\\EnrollmentStatus',
          'organizationmembershipstatus' => 'App\\Enums\\OrganizationMembershipStatus',
          'role' => 'App\\Enums\\Role',
          'schoolmembershipstatus' => 'App\\Enums\\SchoolMembershipStatus',
          'carbon' => 'Carbon\\Carbon',
          'mustverifyemail' => 'Illuminate\\Contracts\\Auth\\MustVerifyEmail',
          'builder' => 'Illuminate\\Database\\Eloquent\\Builder',
          'hasfactory' => 'Illuminate\\Database\\Eloquent\\Factories\\HasFactory',
          'belongstomany' => 'Illuminate\\Database\\Eloquent\\Relations\\BelongsToMany',
          'hasmany' => 'Illuminate\\Database\\Eloquent\\Relations\\HasMany',
          'hasone' => 'Illuminate\\Database\\Eloquent\\Relations\\HasOne',
          'softdeletes' => 'Illuminate\\Database\\Eloquent\\SoftDeletes',
          'authenticatable' => 'Illuminate\\Foundation\\Auth\\User',
          'notifiable' => 'Illuminate\\Notifications\\Notifiable',
          'twofactorauthenticatable' => 'Laravel\\Fortify\\TwoFactorAuthenticatable',
          'hasprofilephoto' => 'Laravel\\Jetstream\\HasProfilePhoto',
          'hasapitokens' => 'Laravel\\Sanctum\\HasApiTokens',
          'hasroles' => 'Spatie\\Permission\\Traits\\HasRoles',
        ),
         'className' => 'App\\Models\\User',
         'functionName' => 'scopeOfSchool',
         'templatePhpDocNodes' => 
        array (
        ),
         'parent' => NULL,
         'typeAliasesMap' => 
        array (
        ),
         'bypassTypeAliases' => false,
         'constUses' => 
        array (
        ),
         'typeAliasClassName' => NULL,
         'traitData' => NULL,
      )),
      '3298efda026ac9afa0e465a0508069db' => 
      \PHPStan\Analyser\IntermediaryNameScope::__set_state(array(
         'namespace' => 'App\\Models',
         'uses' => 
        array (
          'accountstatus' => 'App\\Enums\\AccountStatus',
          'enrollmentstatus' => 'App\\Enums\\EnrollmentStatus',
          'organizationmembershipstatus' => 'App\\Enums\\OrganizationMembershipStatus',
          'role' => 'App\\Enums\\Role',
          'schoolmembershipstatus' => 'App\\Enums\\SchoolMembershipStatus',
          'carbon' => 'Carbon\\Carbon',
          'mustverifyemail' => 'Illuminate\\Contracts\\Auth\\MustVerifyEmail',
          'builder' => 'Illuminate\\Database\\Eloquent\\Builder',
          'hasfactory' => 'Illuminate\\Database\\Eloquent\\Factories\\HasFactory',
          'belongstomany' => 'Illuminate\\Database\\Eloquent\\Relations\\BelongsToMany',
          'hasmany' => 'Illuminate\\Database\\Eloquent\\Relations\\HasMany',
          'hasone' => 'Illuminate\\Database\\Eloquent\\Relations\\HasOne',
          'softdeletes' => 'Illuminate\\Database\\Eloquent\\SoftDeletes',
          'authenticatable' => 'Illuminate\\Foundation\\Auth\\User',
          'notifiable' => 'Illuminate\\Notifications\\Notifiable',
          'twofactorauthenticatable' => 'Laravel\\Fortify\\TwoFactorAuthenticatable',
          'hasprofilephoto' => 'Laravel\\Jetstream\\HasProfilePhoto',
          'hasapitokens' => 'Laravel\\Sanctum\\HasApiTokens',
          'hasroles' => 'Spatie\\Permission\\Traits\\HasRoles',
        ),
         'className' => 'App\\Models\\User',
         'functionName' => 'schoolMemberships',
         'templatePhpDocNodes' => 
        array (
        ),
         'parent' => NULL,
         'typeAliasesMap' => 
        array (
        ),
         'bypassTypeAliases' => false,
         'constUses' => 
        array (
        ),
         'typeAliasClassName' => NULL,
         'traitData' => NULL,
      )),
      '3d0749288d293a5d6f9af32e6b59538b' => 
      \PHPStan\Analyser\IntermediaryNameScope::__set_state(array(
         'namespace' => 'App\\Models',
         'uses' => 
        array (
          'accountstatus' => 'App\\Enums\\AccountStatus',
          'enrollmentstatus' => 'App\\Enums\\EnrollmentStatus',
          'organizationmembershipstatus' => 'App\\Enums\\OrganizationMembershipStatus',
          'role' => 'App\\Enums\\Role',
          'schoolmembershipstatus' => 'App\\Enums\\SchoolMembershipStatus',
          'carbon' => 'Carbon\\Carbon',
          'mustverifyemail' => 'Illuminate\\Contracts\\Auth\\MustVerifyEmail',
          'builder' => 'Illuminate\\Database\\Eloquent\\Builder',
          'hasfactory' => 'Illuminate\\Database\\Eloquent\\Factories\\HasFactory',
          'belongstomany' => 'Illuminate\\Database\\Eloquent\\Relations\\BelongsToMany',
          'hasmany' => 'Illuminate\\Database\\Eloquent\\Relations\\HasMany',
          'hasone' => 'Illuminate\\Database\\Eloquent\\Relations\\HasOne',
          'softdeletes' => 'Illuminate\\Database\\Eloquent\\SoftDeletes',
          'authenticatable' => 'Illuminate\\Foundation\\Auth\\User',
          'notifiable' => 'Illuminate\\Notifications\\Notifiable',
          'twofactorauthenticatable' => 'Laravel\\Fortify\\TwoFactorAuthenticatable',
          'hasprofilephoto' => 'Laravel\\Jetstream\\HasProfilePhoto',
          'hasapitokens' => 'Laravel\\Sanctum\\HasApiTokens',
          'hasroles' => 'Spatie\\Permission\\Traits\\HasRoles',
        ),
         'className' => 'App\\Models\\User',
         'functionName' => 'organizationMemberships',
         'templatePhpDocNodes' => 
        array (
        ),
         'parent' => NULL,
         'typeAliasesMap' => 
        array (
        ),
         'bypassTypeAliases' => false,
         'constUses' => 
        array (
        ),
         'typeAliasClassName' => NULL,
         'traitData' => NULL,
      )),
      '7b82f9e606552b5cb6187552fa47c53b' => 
      \PHPStan\Analyser\IntermediaryNameScope::__set_state(array(
         'namespace' => 'App\\Models',
         'uses' => 
        array (
          'accountstatus' => 'App\\Enums\\AccountStatus',
          'enrollmentstatus' => 'App\\Enums\\EnrollmentStatus',
          'organizationmembershipstatus' => 'App\\Enums\\OrganizationMembershipStatus',
          'role' => 'App\\Enums\\Role',
          'schoolmembershipstatus' => 'App\\Enums\\SchoolMembershipStatus',
          'carbon' => 'Carbon\\Carbon',
          'mustverifyemail' => 'Illuminate\\Contracts\\Auth\\MustVerifyEmail',
          'builder' => 'Illuminate\\Database\\Eloquent\\Builder',
          'hasfactory' => 'Illuminate\\Database\\Eloquent\\Factories\\HasFactory',
          'belongstomany' => 'Illuminate\\Database\\Eloquent\\Relations\\BelongsToMany',
          'hasmany' => 'Illuminate\\Database\\Eloquent\\Relations\\HasMany',
          'hasone' => 'Illuminate\\Database\\Eloquent\\Relations\\HasOne',
          'softdeletes' => 'Illuminate\\Database\\Eloquent\\SoftDeletes',
          'authenticatable' => 'Illuminate\\Foundation\\Auth\\User',
          'notifiable' => 'Illuminate\\Notifications\\Notifiable',
          'twofactorauthenticatable' => 'Laravel\\Fortify\\TwoFactorAuthenticatable',
          'hasprofilephoto' => 'Laravel\\Jetstream\\HasProfilePhoto',
          'hasapitokens' => 'Laravel\\Sanctum\\HasApiTokens',
          'hasroles' => 'Spatie\\Permission\\Traits\\HasRoles',
        ),
         'className' => 'App\\Models\\User',
         'functionName' => 'organizations',
         'templatePhpDocNodes' => 
        array (
        ),
         'parent' => NULL,
         'typeAliasesMap' => 
        array (
        ),
         'bypassTypeAliases' => false,
         'constUses' => 
        array (
        ),
         'typeAliasClassName' => NULL,
         'traitData' => NULL,
      )),
      '134d664f4484a384837da34900733b3f' => 
      \PHPStan\Analyser\IntermediaryNameScope::__set_state(array(
         'namespace' => 'App\\Models',
         'uses' => 
        array (
          'accountstatus' => 'App\\Enums\\AccountStatus',
          'enrollmentstatus' => 'App\\Enums\\EnrollmentStatus',
          'organizationmembershipstatus' => 'App\\Enums\\OrganizationMembershipStatus',
          'role' => 'App\\Enums\\Role',
          'schoolmembershipstatus' => 'App\\Enums\\SchoolMembershipStatus',
          'carbon' => 'Carbon\\Carbon',
          'mustverifyemail' => 'Illuminate\\Contracts\\Auth\\MustVerifyEmail',
          'builder' => 'Illuminate\\Database\\Eloquent\\Builder',
          'hasfactory' => 'Illuminate\\Database\\Eloquent\\Factories\\HasFactory',
          'belongstomany' => 'Illuminate\\Database\\Eloquent\\Relations\\BelongsToMany',
          'hasmany' => 'Illuminate\\Database\\Eloquent\\Relations\\HasMany',
          'hasone' => 'Illuminate\\Database\\Eloquent\\Relations\\HasOne',
          'softdeletes' => 'Illuminate\\Database\\Eloquent\\SoftDeletes',
          'authenticatable' => 'Illuminate\\Foundation\\Auth\\User',
          'notifiable' => 'Illuminate\\Notifications\\Notifiable',
          'twofactorauthenticatable' => 'Laravel\\Fortify\\TwoFactorAuthenticatable',
          'hasprofilephoto' => 'Laravel\\Jetstream\\HasProfilePhoto',
          'hasapitokens' => 'Laravel\\Sanctum\\HasApiTokens',
          'hasroles' => 'Spatie\\Permission\\Traits\\HasRoles',
        ),
         'className' => 'App\\Models\\User',
         'functionName' => 'administersOrganization',
         'templatePhpDocNodes' => 
        array (
        ),
         'parent' => NULL,
         'typeAliasesMap' => 
        array (
        ),
         'bypassTypeAliases' => false,
         'constUses' => 
        array (
        ),
         'typeAliasClassName' => NULL,
         'traitData' => NULL,
      )),
      '0997a5fad8661a6686225405389a74d3' => 
      \PHPStan\Analyser\IntermediaryNameScope::__set_state(array(
         'namespace' => 'App\\Models',
         'uses' => 
        array (
          'accountstatus' => 'App\\Enums\\AccountStatus',
          'enrollmentstatus' => 'App\\Enums\\EnrollmentStatus',
          'organizationmembershipstatus' => 'App\\Enums\\OrganizationMembershipStatus',
          'role' => 'App\\Enums\\Role',
          'schoolmembershipstatus' => 'App\\Enums\\SchoolMembershipStatus',
          'carbon' => 'Carbon\\Carbon',
          'mustverifyemail' => 'Illuminate\\Contracts\\Auth\\MustVerifyEmail',
          'builder' => 'Illuminate\\Database\\Eloquent\\Builder',
          'hasfactory' => 'Illuminate\\Database\\Eloquent\\Factories\\HasFactory',
          'belongstomany' => 'Illuminate\\Database\\Eloquent\\Relations\\BelongsToMany',
          'hasmany' => 'Illuminate\\Database\\Eloquent\\Relations\\HasMany',
          'hasone' => 'Illuminate\\Database\\Eloquent\\Relations\\HasOne',
          'softdeletes' => 'Illuminate\\Database\\Eloquent\\SoftDeletes',
          'authenticatable' => 'Illuminate\\Foundation\\Auth\\User',
          'notifiable' => 'Illuminate\\Notifications\\Notifiable',
          'twofactorauthenticatable' => 'Laravel\\Fortify\\TwoFactorAuthenticatable',
          'hasprofilephoto' => 'Laravel\\Jetstream\\HasProfilePhoto',
          'hasapitokens' => 'Laravel\\Sanctum\\HasApiTokens',
          'hasroles' => 'Spatie\\Permission\\Traits\\HasRoles',
        ),
         'className' => 'App\\Models\\User',
         'functionName' => 'schools',
         'templatePhpDocNodes' => 
        array (
        ),
         'parent' => NULL,
         'typeAliasesMap' => 
        array (
        ),
         'bypassTypeAliases' => false,
         'constUses' => 
        array (
        ),
         'typeAliasClassName' => NULL,
         'traitData' => NULL,
      )),
      'ea28dfd187909b6fe7f6cedaf6e99054' => 
      \PHPStan\Analyser\IntermediaryNameScope::__set_state(array(
         'namespace' => 'App\\Models',
         'uses' => 
        array (
          'accountstatus' => 'App\\Enums\\AccountStatus',
          'enrollmentstatus' => 'App\\Enums\\EnrollmentStatus',
          'organizationmembershipstatus' => 'App\\Enums\\OrganizationMembershipStatus',
          'role' => 'App\\Enums\\Role',
          'schoolmembershipstatus' => 'App\\Enums\\SchoolMembershipStatus',
          'carbon' => 'Carbon\\Carbon',
          'mustverifyemail' => 'Illuminate\\Contracts\\Auth\\MustVerifyEmail',
          'builder' => 'Illuminate\\Database\\Eloquent\\Builder',
          'hasfactory' => 'Illuminate\\Database\\Eloquent\\Factories\\HasFactory',
          'belongstomany' => 'Illuminate\\Database\\Eloquent\\Relations\\BelongsToMany',
          'hasmany' => 'Illuminate\\Database\\Eloquent\\Relations\\HasMany',
          'hasone' => 'Illuminate\\Database\\Eloquent\\Relations\\HasOne',
          'softdeletes' => 'Illuminate\\Database\\Eloquent\\SoftDeletes',
          'authenticatable' => 'Illuminate\\Foundation\\Auth\\User',
          'notifiable' => 'Illuminate\\Notifications\\Notifiable',
          'twofactorauthenticatable' => 'Laravel\\Fortify\\TwoFactorAuthenticatable',
          'hasprofilephoto' => 'Laravel\\Jetstream\\HasProfilePhoto',
          'hasapitokens' => 'Laravel\\Sanctum\\HasApiTokens',
          'hasroles' => 'Spatie\\Permission\\Traits\\HasRoles',
        ),
         'className' => 'App\\Models\\User',
         'functionName' => 'primarySchool',
         'templatePhpDocNodes' => 
        array (
        ),
         'parent' => NULL,
         'typeAliasesMap' => 
        array (
        ),
         'bypassTypeAliases' => false,
         'constUses' => 
        array (
        ),
         'typeAliasClassName' => NULL,
         'traitData' => NULL,
      )),
      'ea56293f6814b78832d4960ec8f0d0e8' => 
      \PHPStan\Analyser\IntermediaryNameScope::__set_state(array(
         'namespace' => 'App\\Models',
         'uses' => 
        array (
          'accountstatus' => 'App\\Enums\\AccountStatus',
          'enrollmentstatus' => 'App\\Enums\\EnrollmentStatus',
          'organizationmembershipstatus' => 'App\\Enums\\OrganizationMembershipStatus',
          'role' => 'App\\Enums\\Role',
          'schoolmembershipstatus' => 'App\\Enums\\SchoolMembershipStatus',
          'carbon' => 'Carbon\\Carbon',
          'mustverifyemail' => 'Illuminate\\Contracts\\Auth\\MustVerifyEmail',
          'builder' => 'Illuminate\\Database\\Eloquent\\Builder',
          'hasfactory' => 'Illuminate\\Database\\Eloquent\\Factories\\HasFactory',
          'belongstomany' => 'Illuminate\\Database\\Eloquent\\Relations\\BelongsToMany',
          'hasmany' => 'Illuminate\\Database\\Eloquent\\Relations\\HasMany',
          'hasone' => 'Illuminate\\Database\\Eloquent\\Relations\\HasOne',
          'softdeletes' => 'Illuminate\\Database\\Eloquent\\SoftDeletes',
          'authenticatable' => 'Illuminate\\Foundation\\Auth\\User',
          'notifiable' => 'Illuminate\\Notifications\\Notifiable',
          'twofactorauthenticatable' => 'Laravel\\Fortify\\TwoFactorAuthenticatable',
          'hasprofilephoto' => 'Laravel\\Jetstream\\HasProfilePhoto',
          'hasapitokens' => 'Laravel\\Sanctum\\HasApiTokens',
          'hasroles' => 'Spatie\\Permission\\Traits\\HasRoles',
        ),
         'className' => 'App\\Models\\User',
         'functionName' => 'belongsToSchool',
         'templatePhpDocNodes' => 
        array (
        ),
         'parent' => NULL,
         'typeAliasesMap' => 
        array (
        ),
         'bypassTypeAliases' => false,
         'constUses' => 
        array (
        ),
         'typeAliasClassName' => NULL,
         'traitData' => NULL,
      )),
      '3bae5d3352f07fe940e13430c46077bc' => 
      \PHPStan\Analyser\IntermediaryNameScope::__set_state(array(
         'namespace' => 'App\\Models',
         'uses' => 
        array (
          'accountstatus' => 'App\\Enums\\AccountStatus',
          'enrollmentstatus' => 'App\\Enums\\EnrollmentStatus',
          'organizationmembershipstatus' => 'App\\Enums\\OrganizationMembershipStatus',
          'role' => 'App\\Enums\\Role',
          'schoolmembershipstatus' => 'App\\Enums\\SchoolMembershipStatus',
          'carbon' => 'Carbon\\Carbon',
          'mustverifyemail' => 'Illuminate\\Contracts\\Auth\\MustVerifyEmail',
          'builder' => 'Illuminate\\Database\\Eloquent\\Builder',
          'hasfactory' => 'Illuminate\\Database\\Eloquent\\Factories\\HasFactory',
          'belongstomany' => 'Illuminate\\Database\\Eloquent\\Relations\\BelongsToMany',
          'hasmany' => 'Illuminate\\Database\\Eloquent\\Relations\\HasMany',
          'hasone' => 'Illuminate\\Database\\Eloquent\\Relations\\HasOne',
          'softdeletes' => 'Illuminate\\Database\\Eloquent\\SoftDeletes',
          'authenticatable' => 'Illuminate\\Foundation\\Auth\\User',
          'notifiable' => 'Illuminate\\Notifications\\Notifiable',
          'twofactorauthenticatable' => 'Laravel\\Fortify\\TwoFactorAuthenticatable',
          'hasprofilephoto' => 'Laravel\\Jetstream\\HasProfilePhoto',
          'hasapitokens' => 'Laravel\\Sanctum\\HasApiTokens',
          'hasroles' => 'Spatie\\Permission\\Traits\\HasRoles',
        ),
         'className' => 'App\\Models\\User',
         'functionName' => 'belongsToCurrentSchool',
         'templatePhpDocNodes' => 
        array (
        ),
         'parent' => NULL,
         'typeAliasesMap' => 
        array (
        ),
         'bypassTypeAliases' => false,
         'constUses' => 
        array (
        ),
         'typeAliasClassName' => NULL,
         'traitData' => NULL,
      )),
      '76211c38a38e2a0f36b574fb3309f937' => 
      \PHPStan\Analyser\IntermediaryNameScope::__set_state(array(
         'namespace' => 'App\\Models',
         'uses' => 
        array (
          'accountstatus' => 'App\\Enums\\AccountStatus',
          'enrollmentstatus' => 'App\\Enums\\EnrollmentStatus',
          'organizationmembershipstatus' => 'App\\Enums\\OrganizationMembershipStatus',
          'role' => 'App\\Enums\\Role',
          'schoolmembershipstatus' => 'App\\Enums\\SchoolMembershipStatus',
          'carbon' => 'Carbon\\Carbon',
          'mustverifyemail' => 'Illuminate\\Contracts\\Auth\\MustVerifyEmail',
          'builder' => 'Illuminate\\Database\\Eloquent\\Builder',
          'hasfactory' => 'Illuminate\\Database\\Eloquent\\Factories\\HasFactory',
          'belongstomany' => 'Illuminate\\Database\\Eloquent\\Relations\\BelongsToMany',
          'hasmany' => 'Illuminate\\Database\\Eloquent\\Relations\\HasMany',
          'hasone' => 'Illuminate\\Database\\Eloquent\\Relations\\HasOne',
          'softdeletes' => 'Illuminate\\Database\\Eloquent\\SoftDeletes',
          'authenticatable' => 'Illuminate\\Foundation\\Auth\\User',
          'notifiable' => 'Illuminate\\Notifications\\Notifiable',
          'twofactorauthenticatable' => 'Laravel\\Fortify\\TwoFactorAuthenticatable',
          'hasprofilephoto' => 'Laravel\\Jetstream\\HasProfilePhoto',
          'hasapitokens' => 'Laravel\\Sanctum\\HasApiTokens',
          'hasroles' => 'Spatie\\Permission\\Traits\\HasRoles',
        ),
         'className' => 'App\\Models\\User',
         'functionName' => 'studentRecords',
         'templatePhpDocNodes' => 
        array (
        ),
         'parent' => NULL,
         'typeAliasesMap' => 
        array (
        ),
         'bypassTypeAliases' => false,
         'constUses' => 
        array (
        ),
         'typeAliasClassName' => NULL,
         'traitData' => NULL,
      )),
      'c87dc35962884507bcf344f374f5565a' => 
      \PHPStan\Analyser\IntermediaryNameScope::__set_state(array(
         'namespace' => 'App\\Models',
         'uses' => 
        array (
          'accountstatus' => 'App\\Enums\\AccountStatus',
          'enrollmentstatus' => 'App\\Enums\\EnrollmentStatus',
          'organizationmembershipstatus' => 'App\\Enums\\OrganizationMembershipStatus',
          'role' => 'App\\Enums\\Role',
          'schoolmembershipstatus' => 'App\\Enums\\SchoolMembershipStatus',
          'carbon' => 'Carbon\\Carbon',
          'mustverifyemail' => 'Illuminate\\Contracts\\Auth\\MustVerifyEmail',
          'builder' => 'Illuminate\\Database\\Eloquent\\Builder',
          'hasfactory' => 'Illuminate\\Database\\Eloquent\\Factories\\HasFactory',
          'belongstomany' => 'Illuminate\\Database\\Eloquent\\Relations\\BelongsToMany',
          'hasmany' => 'Illuminate\\Database\\Eloquent\\Relations\\HasMany',
          'hasone' => 'Illuminate\\Database\\Eloquent\\Relations\\HasOne',
          'softdeletes' => 'Illuminate\\Database\\Eloquent\\SoftDeletes',
          'authenticatable' => 'Illuminate\\Foundation\\Auth\\User',
          'notifiable' => 'Illuminate\\Notifications\\Notifiable',
          'twofactorauthenticatable' => 'Laravel\\Fortify\\TwoFactorAuthenticatable',
          'hasprofilephoto' => 'Laravel\\Jetstream\\HasProfilePhoto',
          'hasapitokens' => 'Laravel\\Sanctum\\HasApiTokens',
          'hasroles' => 'Spatie\\Permission\\Traits\\HasRoles',
        ),
         'className' => 'App\\Models\\User',
         'functionName' => 'studentRecord',
         'templatePhpDocNodes' => 
        array (
        ),
         'parent' => NULL,
         'typeAliasesMap' => 
        array (
        ),
         'bypassTypeAliases' => false,
         'constUses' => 
        array (
        ),
         'typeAliasClassName' => NULL,
         'traitData' => NULL,
      )),
      'b7402af7d1c8c6f5f0879efc025e843f' => 
      \PHPStan\Analyser\IntermediaryNameScope::__set_state(array(
         'namespace' => 'App\\Models',
         'uses' => 
        array (
          'accountstatus' => 'App\\Enums\\AccountStatus',
          'enrollmentstatus' => 'App\\Enums\\EnrollmentStatus',
          'organizationmembershipstatus' => 'App\\Enums\\OrganizationMembershipStatus',
          'role' => 'App\\Enums\\Role',
          'schoolmembershipstatus' => 'App\\Enums\\SchoolMembershipStatus',
          'carbon' => 'Carbon\\Carbon',
          'mustverifyemail' => 'Illuminate\\Contracts\\Auth\\MustVerifyEmail',
          'builder' => 'Illuminate\\Database\\Eloquent\\Builder',
          'hasfactory' => 'Illuminate\\Database\\Eloquent\\Factories\\HasFactory',
          'belongstomany' => 'Illuminate\\Database\\Eloquent\\Relations\\BelongsToMany',
          'hasmany' => 'Illuminate\\Database\\Eloquent\\Relations\\HasMany',
          'hasone' => 'Illuminate\\Database\\Eloquent\\Relations\\HasOne',
          'softdeletes' => 'Illuminate\\Database\\Eloquent\\SoftDeletes',
          'authenticatable' => 'Illuminate\\Foundation\\Auth\\User',
          'notifiable' => 'Illuminate\\Notifications\\Notifiable',
          'twofactorauthenticatable' => 'Laravel\\Fortify\\TwoFactorAuthenticatable',
          'hasprofilephoto' => 'Laravel\\Jetstream\\HasProfilePhoto',
          'hasapitokens' => 'Laravel\\Sanctum\\HasApiTokens',
          'hasroles' => 'Spatie\\Permission\\Traits\\HasRoles',
        ),
         'className' => 'App\\Models\\User',
         'functionName' => 'graduatedStudentRecord',
         'templatePhpDocNodes' => 
        array (
        ),
         'parent' => NULL,
         'typeAliasesMap' => 
        array (
        ),
         'bypassTypeAliases' => false,
         'constUses' => 
        array (
        ),
         'typeAliasClassName' => NULL,
         'traitData' => NULL,
      )),
      'd14880f1bfd613d89cf110f0f13e6ff3' => 
      \PHPStan\Analyser\IntermediaryNameScope::__set_state(array(
         'namespace' => 'App\\Models',
         'uses' => 
        array (
          'accountstatus' => 'App\\Enums\\AccountStatus',
          'enrollmentstatus' => 'App\\Enums\\EnrollmentStatus',
          'organizationmembershipstatus' => 'App\\Enums\\OrganizationMembershipStatus',
          'role' => 'App\\Enums\\Role',
          'schoolmembershipstatus' => 'App\\Enums\\SchoolMembershipStatus',
          'carbon' => 'Carbon\\Carbon',
          'mustverifyemail' => 'Illuminate\\Contracts\\Auth\\MustVerifyEmail',
          'builder' => 'Illuminate\\Database\\Eloquent\\Builder',
          'hasfactory' => 'Illuminate\\Database\\Eloquent\\Factories\\HasFactory',
          'belongstomany' => 'Illuminate\\Database\\Eloquent\\Relations\\BelongsToMany',
          'hasmany' => 'Illuminate\\Database\\Eloquent\\Relations\\HasMany',
          'hasone' => 'Illuminate\\Database\\Eloquent\\Relations\\HasOne',
          'softdeletes' => 'Illuminate\\Database\\Eloquent\\SoftDeletes',
          'authenticatable' => 'Illuminate\\Foundation\\Auth\\User',
          'notifiable' => 'Illuminate\\Notifications\\Notifiable',
          'twofactorauthenticatable' => 'Laravel\\Fortify\\TwoFactorAuthenticatable',
          'hasprofilephoto' => 'Laravel\\Jetstream\\HasProfilePhoto',
          'hasapitokens' => 'Laravel\\Sanctum\\HasApiTokens',
          'hasroles' => 'Spatie\\Permission\\Traits\\HasRoles',
        ),
         'className' => 'App\\Models\\User',
         'functionName' => 'allStudentRecords',
         'templatePhpDocNodes' => 
        array (
        ),
         'parent' => NULL,
         'typeAliasesMap' => 
        array (
        ),
         'bypassTypeAliases' => false,
         'constUses' => 
        array (
        ),
         'typeAliasClassName' => NULL,
         'traitData' => NULL,
      )),
      'c23e6527b5ce195754f1e7138be86218' => 
      \PHPStan\Analyser\IntermediaryNameScope::__set_state(array(
         'namespace' => 'App\\Models',
         'uses' => 
        array (
          'accountstatus' => 'App\\Enums\\AccountStatus',
          'enrollmentstatus' => 'App\\Enums\\EnrollmentStatus',
          'organizationmembershipstatus' => 'App\\Enums\\OrganizationMembershipStatus',
          'role' => 'App\\Enums\\Role',
          'schoolmembershipstatus' => 'App\\Enums\\SchoolMembershipStatus',
          'carbon' => 'Carbon\\Carbon',
          'mustverifyemail' => 'Illuminate\\Contracts\\Auth\\MustVerifyEmail',
          'builder' => 'Illuminate\\Database\\Eloquent\\Builder',
          'hasfactory' => 'Illuminate\\Database\\Eloquent\\Factories\\HasFactory',
          'belongstomany' => 'Illuminate\\Database\\Eloquent\\Relations\\BelongsToMany',
          'hasmany' => 'Illuminate\\Database\\Eloquent\\Relations\\HasMany',
          'hasone' => 'Illuminate\\Database\\Eloquent\\Relations\\HasOne',
          'softdeletes' => 'Illuminate\\Database\\Eloquent\\SoftDeletes',
          'authenticatable' => 'Illuminate\\Foundation\\Auth\\User',
          'notifiable' => 'Illuminate\\Notifications\\Notifiable',
          'twofactorauthenticatable' => 'Laravel\\Fortify\\TwoFactorAuthenticatable',
          'hasprofilephoto' => 'Laravel\\Jetstream\\HasProfilePhoto',
          'hasapitokens' => 'Laravel\\Sanctum\\HasApiTokens',
          'hasroles' => 'Spatie\\Permission\\Traits\\HasRoles',
        ),
         'className' => 'App\\Models\\User',
         'functionName' => 'enrollmentOfCurrentSchool',
         'templatePhpDocNodes' => 
        array (
        ),
         'parent' => NULL,
         'typeAliasesMap' => 
        array (
        ),
         'bypassTypeAliases' => false,
         'constUses' => 
        array (
        ),
         'typeAliasClassName' => NULL,
         'traitData' => NULL,
      )),
      '17dd55ec47550ec74b07e2dea2872e7a' => 
      \PHPStan\Analyser\IntermediaryNameScope::__set_state(array(
         'namespace' => 'App\\Models',
         'uses' => 
        array (
          'accountstatus' => 'App\\Enums\\AccountStatus',
          'enrollmentstatus' => 'App\\Enums\\EnrollmentStatus',
          'organizationmembershipstatus' => 'App\\Enums\\OrganizationMembershipStatus',
          'role' => 'App\\Enums\\Role',
          'schoolmembershipstatus' => 'App\\Enums\\SchoolMembershipStatus',
          'carbon' => 'Carbon\\Carbon',
          'mustverifyemail' => 'Illuminate\\Contracts\\Auth\\MustVerifyEmail',
          'builder' => 'Illuminate\\Database\\Eloquent\\Builder',
          'hasfactory' => 'Illuminate\\Database\\Eloquent\\Factories\\HasFactory',
          'belongstomany' => 'Illuminate\\Database\\Eloquent\\Relations\\BelongsToMany',
          'hasmany' => 'Illuminate\\Database\\Eloquent\\Relations\\HasMany',
          'hasone' => 'Illuminate\\Database\\Eloquent\\Relations\\HasOne',
          'softdeletes' => 'Illuminate\\Database\\Eloquent\\SoftDeletes',
          'authenticatable' => 'Illuminate\\Foundation\\Auth\\User',
          'notifiable' => 'Illuminate\\Notifications\\Notifiable',
          'twofactorauthenticatable' => 'Laravel\\Fortify\\TwoFactorAuthenticatable',
          'hasprofilephoto' => 'Laravel\\Jetstream\\HasProfilePhoto',
          'hasapitokens' => 'Laravel\\Sanctum\\HasApiTokens',
          'hasroles' => 'Spatie\\Permission\\Traits\\HasRoles',
        ),
         'className' => 'App\\Models\\User',
         'functionName' => 'parents',
         'templatePhpDocNodes' => 
        array (
        ),
         'parent' => NULL,
         'typeAliasesMap' => 
        array (
        ),
         'bypassTypeAliases' => false,
         'constUses' => 
        array (
        ),
         'typeAliasClassName' => NULL,
         'traitData' => NULL,
      )),
      '888f078a09d1e8d563cfc670ba55700e' => 
      \PHPStan\Analyser\IntermediaryNameScope::__set_state(array(
         'namespace' => 'App\\Models',
         'uses' => 
        array (
          'accountstatus' => 'App\\Enums\\AccountStatus',
          'enrollmentstatus' => 'App\\Enums\\EnrollmentStatus',
          'organizationmembershipstatus' => 'App\\Enums\\OrganizationMembershipStatus',
          'role' => 'App\\Enums\\Role',
          'schoolmembershipstatus' => 'App\\Enums\\SchoolMembershipStatus',
          'carbon' => 'Carbon\\Carbon',
          'mustverifyemail' => 'Illuminate\\Contracts\\Auth\\MustVerifyEmail',
          'builder' => 'Illuminate\\Database\\Eloquent\\Builder',
          'hasfactory' => 'Illuminate\\Database\\Eloquent\\Factories\\HasFactory',
          'belongstomany' => 'Illuminate\\Database\\Eloquent\\Relations\\BelongsToMany',
          'hasmany' => 'Illuminate\\Database\\Eloquent\\Relations\\HasMany',
          'hasone' => 'Illuminate\\Database\\Eloquent\\Relations\\HasOne',
          'softdeletes' => 'Illuminate\\Database\\Eloquent\\SoftDeletes',
          'authenticatable' => 'Illuminate\\Foundation\\Auth\\User',
          'notifiable' => 'Illuminate\\Notifications\\Notifiable',
          'twofactorauthenticatable' => 'Laravel\\Fortify\\TwoFactorAuthenticatable',
          'hasprofilephoto' => 'Laravel\\Jetstream\\HasProfilePhoto',
          'hasapitokens' => 'Laravel\\Sanctum\\HasApiTokens',
          'hasroles' => 'Spatie\\Permission\\Traits\\HasRoles',
        ),
         'className' => 'App\\Models\\User',
         'functionName' => 'teacherRecord',
         'templatePhpDocNodes' => 
        array (
        ),
         'parent' => NULL,
         'typeAliasesMap' => 
        array (
        ),
         'bypassTypeAliases' => false,
         'constUses' => 
        array (
        ),
         'typeAliasClassName' => NULL,
         'traitData' => NULL,
      )),
      '43c940ee659876373246c81d7f4e4e54' => 
      \PHPStan\Analyser\IntermediaryNameScope::__set_state(array(
         'namespace' => 'App\\Models',
         'uses' => 
        array (
          'accountstatus' => 'App\\Enums\\AccountStatus',
          'enrollmentstatus' => 'App\\Enums\\EnrollmentStatus',
          'organizationmembershipstatus' => 'App\\Enums\\OrganizationMembershipStatus',
          'role' => 'App\\Enums\\Role',
          'schoolmembershipstatus' => 'App\\Enums\\SchoolMembershipStatus',
          'carbon' => 'Carbon\\Carbon',
          'mustverifyemail' => 'Illuminate\\Contracts\\Auth\\MustVerifyEmail',
          'builder' => 'Illuminate\\Database\\Eloquent\\Builder',
          'hasfactory' => 'Illuminate\\Database\\Eloquent\\Factories\\HasFactory',
          'belongstomany' => 'Illuminate\\Database\\Eloquent\\Relations\\BelongsToMany',
          'hasmany' => 'Illuminate\\Database\\Eloquent\\Relations\\HasMany',
          'hasone' => 'Illuminate\\Database\\Eloquent\\Relations\\HasOne',
          'softdeletes' => 'Illuminate\\Database\\Eloquent\\SoftDeletes',
          'authenticatable' => 'Illuminate\\Foundation\\Auth\\User',
          'notifiable' => 'Illuminate\\Notifications\\Notifiable',
          'twofactorauthenticatable' => 'Laravel\\Fortify\\TwoFactorAuthenticatable',
          'hasprofilephoto' => 'Laravel\\Jetstream\\HasProfilePhoto',
          'hasapitokens' => 'Laravel\\Sanctum\\HasApiTokens',
          'hasroles' => 'Spatie\\Permission\\Traits\\HasRoles',
        ),
         'className' => 'App\\Models\\User',
         'functionName' => 'parentRecord',
         'templatePhpDocNodes' => 
        array (
        ),
         'parent' => NULL,
         'typeAliasesMap' => 
        array (
        ),
         'bypassTypeAliases' => false,
         'constUses' => 
        array (
        ),
         'typeAliasClassName' => NULL,
         'traitData' => NULL,
      )),
      'd5c22b2fc18c7d642b18d35043da7fc1' => 
      \PHPStan\Analyser\IntermediaryNameScope::__set_state(array(
         'namespace' => 'App\\Models',
         'uses' => 
        array (
          'accountstatus' => 'App\\Enums\\AccountStatus',
          'enrollmentstatus' => 'App\\Enums\\EnrollmentStatus',
          'organizationmembershipstatus' => 'App\\Enums\\OrganizationMembershipStatus',
          'role' => 'App\\Enums\\Role',
          'schoolmembershipstatus' => 'App\\Enums\\SchoolMembershipStatus',
          'carbon' => 'Carbon\\Carbon',
          'mustverifyemail' => 'Illuminate\\Contracts\\Auth\\MustVerifyEmail',
          'builder' => 'Illuminate\\Database\\Eloquent\\Builder',
          'hasfactory' => 'Illuminate\\Database\\Eloquent\\Factories\\HasFactory',
          'belongstomany' => 'Illuminate\\Database\\Eloquent\\Relations\\BelongsToMany',
          'hasmany' => 'Illuminate\\Database\\Eloquent\\Relations\\HasMany',
          'hasone' => 'Illuminate\\Database\\Eloquent\\Relations\\HasOne',
          'softdeletes' => 'Illuminate\\Database\\Eloquent\\SoftDeletes',
          'authenticatable' => 'Illuminate\\Foundation\\Auth\\User',
          'notifiable' => 'Illuminate\\Notifications\\Notifiable',
          'twofactorauthenticatable' => 'Laravel\\Fortify\\TwoFactorAuthenticatable',
          'hasprofilephoto' => 'Laravel\\Jetstream\\HasProfilePhoto',
          'hasapitokens' => 'Laravel\\Sanctum\\HasApiTokens',
          'hasroles' => 'Spatie\\Permission\\Traits\\HasRoles',
        ),
         'className' => 'App\\Models\\User',
         'functionName' => 'accountInvitations',
         'templatePhpDocNodes' => 
        array (
        ),
         'parent' => NULL,
         'typeAliasesMap' => 
        array (
        ),
         'bypassTypeAliases' => false,
         'constUses' => 
        array (
        ),
         'typeAliasClassName' => NULL,
         'traitData' => NULL,
      )),
      '528a468ed4361d05843008898029e1f3' => 
      \PHPStan\Analyser\IntermediaryNameScope::__set_state(array(
         'namespace' => 'App\\Models',
         'uses' => 
        array (
          'accountstatus' => 'App\\Enums\\AccountStatus',
          'enrollmentstatus' => 'App\\Enums\\EnrollmentStatus',
          'organizationmembershipstatus' => 'App\\Enums\\OrganizationMembershipStatus',
          'role' => 'App\\Enums\\Role',
          'schoolmembershipstatus' => 'App\\Enums\\SchoolMembershipStatus',
          'carbon' => 'Carbon\\Carbon',
          'mustverifyemail' => 'Illuminate\\Contracts\\Auth\\MustVerifyEmail',
          'builder' => 'Illuminate\\Database\\Eloquent\\Builder',
          'hasfactory' => 'Illuminate\\Database\\Eloquent\\Factories\\HasFactory',
          'belongstomany' => 'Illuminate\\Database\\Eloquent\\Relations\\BelongsToMany',
          'hasmany' => 'Illuminate\\Database\\Eloquent\\Relations\\HasMany',
          'hasone' => 'Illuminate\\Database\\Eloquent\\Relations\\HasOne',
          'softdeletes' => 'Illuminate\\Database\\Eloquent\\SoftDeletes',
          'authenticatable' => 'Illuminate\\Foundation\\Auth\\User',
          'notifiable' => 'Illuminate\\Notifications\\Notifiable',
          'twofactorauthenticatable' => 'Laravel\\Fortify\\TwoFactorAuthenticatable',
          'hasprofilephoto' => 'Laravel\\Jetstream\\HasProfilePhoto',
          'hasapitokens' => 'Laravel\\Sanctum\\HasApiTokens',
          'hasroles' => 'Spatie\\Permission\\Traits\\HasRoles',
        ),
         'className' => 'App\\Models\\User',
         'functionName' => 'pendingAccountInvitation',
         'templatePhpDocNodes' => 
        array (
        ),
         'parent' => NULL,
         'typeAliasesMap' => 
        array (
        ),
         'bypassTypeAliases' => false,
         'constUses' => 
        array (
        ),
         'typeAliasClassName' => NULL,
         'traitData' => NULL,
      )),
      '5ee45dd85cbbaf73ff7043f4d4715de2' => 
      \PHPStan\Analyser\IntermediaryNameScope::__set_state(array(
         'namespace' => 'App\\Models',
         'uses' => 
        array (
          'accountstatus' => 'App\\Enums\\AccountStatus',
          'enrollmentstatus' => 'App\\Enums\\EnrollmentStatus',
          'organizationmembershipstatus' => 'App\\Enums\\OrganizationMembershipStatus',
          'role' => 'App\\Enums\\Role',
          'schoolmembershipstatus' => 'App\\Enums\\SchoolMembershipStatus',
          'carbon' => 'Carbon\\Carbon',
          'mustverifyemail' => 'Illuminate\\Contracts\\Auth\\MustVerifyEmail',
          'builder' => 'Illuminate\\Database\\Eloquent\\Builder',
          'hasfactory' => 'Illuminate\\Database\\Eloquent\\Factories\\HasFactory',
          'belongstomany' => 'Illuminate\\Database\\Eloquent\\Relations\\BelongsToMany',
          'hasmany' => 'Illuminate\\Database\\Eloquent\\Relations\\HasMany',
          'hasone' => 'Illuminate\\Database\\Eloquent\\Relations\\HasOne',
          'softdeletes' => 'Illuminate\\Database\\Eloquent\\SoftDeletes',
          'authenticatable' => 'Illuminate\\Foundation\\Auth\\User',
          'notifiable' => 'Illuminate\\Notifications\\Notifiable',
          'twofactorauthenticatable' => 'Laravel\\Fortify\\TwoFactorAuthenticatable',
          'hasprofilephoto' => 'Laravel\\Jetstream\\HasProfilePhoto',
          'hasapitokens' => 'Laravel\\Sanctum\\HasApiTokens',
          'hasroles' => 'Spatie\\Permission\\Traits\\HasRoles',
        ),
         'className' => 'App\\Models\\User',
         'functionName' => 'hasActiveAccount',
         'templatePhpDocNodes' => 
        array (
        ),
         'parent' => NULL,
         'typeAliasesMap' => 
        array (
        ),
         'bypassTypeAliases' => false,
         'constUses' => 
        array (
        ),
         'typeAliasClassName' => NULL,
         'traitData' => NULL,
      )),
      '2792fd2003b821780211263ff1830490' => 
      \PHPStan\Analyser\IntermediaryNameScope::__set_state(array(
         'namespace' => 'App\\Models',
         'uses' => 
        array (
          'accountstatus' => 'App\\Enums\\AccountStatus',
          'enrollmentstatus' => 'App\\Enums\\EnrollmentStatus',
          'organizationmembershipstatus' => 'App\\Enums\\OrganizationMembershipStatus',
          'role' => 'App\\Enums\\Role',
          'schoolmembershipstatus' => 'App\\Enums\\SchoolMembershipStatus',
          'carbon' => 'Carbon\\Carbon',
          'mustverifyemail' => 'Illuminate\\Contracts\\Auth\\MustVerifyEmail',
          'builder' => 'Illuminate\\Database\\Eloquent\\Builder',
          'hasfactory' => 'Illuminate\\Database\\Eloquent\\Factories\\HasFactory',
          'belongstomany' => 'Illuminate\\Database\\Eloquent\\Relations\\BelongsToMany',
          'hasmany' => 'Illuminate\\Database\\Eloquent\\Relations\\HasMany',
          'hasone' => 'Illuminate\\Database\\Eloquent\\Relations\\HasOne',
          'softdeletes' => 'Illuminate\\Database\\Eloquent\\SoftDeletes',
          'authenticatable' => 'Illuminate\\Foundation\\Auth\\User',
          'notifiable' => 'Illuminate\\Notifications\\Notifiable',
          'twofactorauthenticatable' => 'Laravel\\Fortify\\TwoFactorAuthenticatable',
          'hasprofilephoto' => 'Laravel\\Jetstream\\HasProfilePhoto',
          'hasapitokens' => 'Laravel\\Sanctum\\HasApiTokens',
          'hasroles' => 'Spatie\\Permission\\Traits\\HasRoles',
        ),
         'className' => 'App\\Models\\User',
         'functionName' => 'isAwaitingInvitationAcceptance',
         'templatePhpDocNodes' => 
        array (
        ),
         'parent' => NULL,
         'typeAliasesMap' => 
        array (
        ),
         'bypassTypeAliases' => false,
         'constUses' => 
        array (
        ),
         'typeAliasClassName' => NULL,
         'traitData' => NULL,
      )),
      'feded7ad9962d33cf042aa6bacd787f1' => 
      \PHPStan\Analyser\IntermediaryNameScope::__set_state(array(
         'namespace' => 'App\\Models',
         'uses' => 
        array (
          'accountstatus' => 'App\\Enums\\AccountStatus',
          'enrollmentstatus' => 'App\\Enums\\EnrollmentStatus',
          'organizationmembershipstatus' => 'App\\Enums\\OrganizationMembershipStatus',
          'role' => 'App\\Enums\\Role',
          'schoolmembershipstatus' => 'App\\Enums\\SchoolMembershipStatus',
          'carbon' => 'Carbon\\Carbon',
          'mustverifyemail' => 'Illuminate\\Contracts\\Auth\\MustVerifyEmail',
          'builder' => 'Illuminate\\Database\\Eloquent\\Builder',
          'hasfactory' => 'Illuminate\\Database\\Eloquent\\Factories\\HasFactory',
          'belongstomany' => 'Illuminate\\Database\\Eloquent\\Relations\\BelongsToMany',
          'hasmany' => 'Illuminate\\Database\\Eloquent\\Relations\\HasMany',
          'hasone' => 'Illuminate\\Database\\Eloquent\\Relations\\HasOne',
          'softdeletes' => 'Illuminate\\Database\\Eloquent\\SoftDeletes',
          'authenticatable' => 'Illuminate\\Foundation\\Auth\\User',
          'notifiable' => 'Illuminate\\Notifications\\Notifiable',
          'twofactorauthenticatable' => 'Laravel\\Fortify\\TwoFactorAuthenticatable',
          'hasprofilephoto' => 'Laravel\\Jetstream\\HasProfilePhoto',
          'hasapitokens' => 'Laravel\\Sanctum\\HasApiTokens',
          'hasroles' => 'Spatie\\Permission\\Traits\\HasRoles',
        ),
         'className' => 'App\\Models\\User',
         'functionName' => 'feeInvoices',
         'templatePhpDocNodes' => 
        array (
        ),
         'parent' => NULL,
         'typeAliasesMap' => 
        array (
        ),
         'bypassTypeAliases' => false,
         'constUses' => 
        array (
        ),
         'typeAliasClassName' => NULL,
         'traitData' => NULL,
      )),
      'c0ce7235841a2ffa6a227d53a578db84' => 
      \PHPStan\Analyser\IntermediaryNameScope::__set_state(array(
         'namespace' => 'App\\Models',
         'uses' => 
        array (
          'accountstatus' => 'App\\Enums\\AccountStatus',
          'enrollmentstatus' => 'App\\Enums\\EnrollmentStatus',
          'organizationmembershipstatus' => 'App\\Enums\\OrganizationMembershipStatus',
          'role' => 'App\\Enums\\Role',
          'schoolmembershipstatus' => 'App\\Enums\\SchoolMembershipStatus',
          'carbon' => 'Carbon\\Carbon',
          'mustverifyemail' => 'Illuminate\\Contracts\\Auth\\MustVerifyEmail',
          'builder' => 'Illuminate\\Database\\Eloquent\\Builder',
          'hasfactory' => 'Illuminate\\Database\\Eloquent\\Factories\\HasFactory',
          'belongstomany' => 'Illuminate\\Database\\Eloquent\\Relations\\BelongsToMany',
          'hasmany' => 'Illuminate\\Database\\Eloquent\\Relations\\HasMany',
          'hasone' => 'Illuminate\\Database\\Eloquent\\Relations\\HasOne',
          'softdeletes' => 'Illuminate\\Database\\Eloquent\\SoftDeletes',
          'authenticatable' => 'Illuminate\\Foundation\\Auth\\User',
          'notifiable' => 'Illuminate\\Notifications\\Notifiable',
          'twofactorauthenticatable' => 'Laravel\\Fortify\\TwoFactorAuthenticatable',
          'hasprofilephoto' => 'Laravel\\Jetstream\\HasProfilePhoto',
          'hasapitokens' => 'Laravel\\Sanctum\\HasApiTokens',
          'hasroles' => 'Spatie\\Permission\\Traits\\HasRoles',
        ),
         'className' => 'App\\Models\\User',
         'functionName' => 'defaultProfilePhotoUrl',
         'templatePhpDocNodes' => 
        array (
        ),
         'parent' => NULL,
         'typeAliasesMap' => 
        array (
        ),
         'bypassTypeAliases' => false,
         'constUses' => 
        array (
        ),
         'typeAliasClassName' => NULL,
         'traitData' => NULL,
      )),
      '3f4706527e2ec7ed9502a1a78cae7110' => 
      \PHPStan\Analyser\IntermediaryNameScope::__set_state(array(
         'namespace' => 'App\\Models',
         'uses' => 
        array (
          'accountstatus' => 'App\\Enums\\AccountStatus',
          'enrollmentstatus' => 'App\\Enums\\EnrollmentStatus',
          'organizationmembershipstatus' => 'App\\Enums\\OrganizationMembershipStatus',
          'role' => 'App\\Enums\\Role',
          'schoolmembershipstatus' => 'App\\Enums\\SchoolMembershipStatus',
          'carbon' => 'Carbon\\Carbon',
          'mustverifyemail' => 'Illuminate\\Contracts\\Auth\\MustVerifyEmail',
          'builder' => 'Illuminate\\Database\\Eloquent\\Builder',
          'hasfactory' => 'Illuminate\\Database\\Eloquent\\Factories\\HasFactory',
          'belongstomany' => 'Illuminate\\Database\\Eloquent\\Relations\\BelongsToMany',
          'hasmany' => 'Illuminate\\Database\\Eloquent\\Relations\\HasMany',
          'hasone' => 'Illuminate\\Database\\Eloquent\\Relations\\HasOne',
          'softdeletes' => 'Illuminate\\Database\\Eloquent\\SoftDeletes',
          'authenticatable' => 'Illuminate\\Foundation\\Auth\\User',
          'notifiable' => 'Illuminate\\Notifications\\Notifiable',
          'twofactorauthenticatable' => 'Laravel\\Fortify\\TwoFactorAuthenticatable',
          'hasprofilephoto' => 'Laravel\\Jetstream\\HasProfilePhoto',
          'hasapitokens' => 'Laravel\\Sanctum\\HasApiTokens',
          'hasroles' => 'Spatie\\Permission\\Traits\\HasRoles',
        ),
         'className' => 'App\\Models\\User',
         'functionName' => 'getBirthdayAttribute',
         'templatePhpDocNodes' => 
        array (
        ),
         'parent' => NULL,
         'typeAliasesMap' => 
        array (
        ),
         'bypassTypeAliases' => false,
         'constUses' => 
        array (
        ),
         'typeAliasClassName' => NULL,
         'traitData' => NULL,
      )),
      'bd691bb7483ce3606aae40f9e7d64fab' => 
      \PHPStan\Analyser\IntermediaryNameScope::__set_state(array(
         'namespace' => 'App\\Models',
         'uses' => 
        array (
          'accountstatus' => 'App\\Enums\\AccountStatus',
          'enrollmentstatus' => 'App\\Enums\\EnrollmentStatus',
          'organizationmembershipstatus' => 'App\\Enums\\OrganizationMembershipStatus',
          'role' => 'App\\Enums\\Role',
          'schoolmembershipstatus' => 'App\\Enums\\SchoolMembershipStatus',
          'carbon' => 'Carbon\\Carbon',
          'mustverifyemail' => 'Illuminate\\Contracts\\Auth\\MustVerifyEmail',
          'builder' => 'Illuminate\\Database\\Eloquent\\Builder',
          'hasfactory' => 'Illuminate\\Database\\Eloquent\\Factories\\HasFactory',
          'belongstomany' => 'Illuminate\\Database\\Eloquent\\Relations\\BelongsToMany',
          'hasmany' => 'Illuminate\\Database\\Eloquent\\Relations\\HasMany',
          'hasone' => 'Illuminate\\Database\\Eloquent\\Relations\\HasOne',
          'softdeletes' => 'Illuminate\\Database\\Eloquent\\SoftDeletes',
          'authenticatable' => 'Illuminate\\Foundation\\Auth\\User',
          'notifiable' => 'Illuminate\\Notifications\\Notifiable',
          'twofactorauthenticatable' => 'Laravel\\Fortify\\TwoFactorAuthenticatable',
          'hasprofilephoto' => 'Laravel\\Jetstream\\HasProfilePhoto',
          'hasapitokens' => 'Laravel\\Sanctum\\HasApiTokens',
          'hasroles' => 'Spatie\\Permission\\Traits\\HasRoles',
        ),
         'className' => 'App\\Models\\User',
         'functionName' => 'subjects',
         'templatePhpDocNodes' => 
        array (
        ),
         'parent' => NULL,
         'typeAliasesMap' => 
        array (
        ),
         'bypassTypeAliases' => false,
         'constUses' => 
        array (
        ),
         'typeAliasClassName' => NULL,
         'traitData' => NULL,
      )),
    ),
    1 => 
    array (
      '/var/www/html/app/Models/User.php' => '027df4ecafc76424fd8a6b667fefcf5eb7b26190c4138ecd80efdc6627106e9c',
      '/var/www/html/vendor/composer/../laravel/sanctum/src/HasApiTokens.php' => '7400600b832dc377ac5f51d051a917775f6efc0d2176a1de7bd7826499ae6509',
      '/var/www/html/vendor/composer/../laravel/framework/src/Illuminate/Database/Eloquent/Factories/HasFactory.php' => 'b6cb2b164e90168e80963a5549541f5f3188a3ec8cfd368bf3611bd94fbd46a7',
      '/var/www/html/vendor/composer/../laravel/jetstream/src/HasProfilePhoto.php' => '0f5fe545f32ea45989365ff1038e705b9955b961bad573710ce3d595ef3def35',
      '/var/www/html/vendor/composer/../spatie/laravel-permission/src/Traits/HasRoles.php' => '3412a3f50444dd6c8a836b56c96c8cb47d0c197faad8e0e2963d94326f2fac9a',
      '/var/www/html/vendor/composer/../spatie/laravel-permission/src/Traits/HasPermissions.php' => 'db6fa62ee9ad18299f22e3b0e15372e2913e75828159dffaa0a95eaf220ec2a5',
      '/var/www/html/vendor/composer/../laravel/framework/src/Illuminate/Notifications/Notifiable.php' => '573fa9bb96fa392434450c9cd9deb8d4e40a5bb93c140a648267b48dfa0433ac',
      '/var/www/html/vendor/composer/../laravel/framework/src/Illuminate/Notifications/HasDatabaseNotifications.php' => 'a7a163aa1f98a0ae4cd2135905b6852e29a850beb4296aa72c44c37d22832135',
      '/var/www/html/vendor/composer/../laravel/framework/src/Illuminate/Notifications/RoutesNotifications.php' => '82891713db67f6df9ea3b400c9905d26da7834b51d26f53dd3bdb1d7f6a78497',
      '/var/www/html/vendor/composer/../laravel/framework/src/Illuminate/Database/Eloquent/SoftDeletes.php' => '8b2e86482b44cad900e57bf26b0eb8153a3c32d42b3f7600c06976de305d113c',
      '/var/www/html/vendor/composer/../laravel/fortify/src/TwoFactorAuthenticatable.php' => 'dbea0fc7b4c502b6188e18ef7885fff666f0a32872627ff0a1ff16c98b0ab8bd',
    ),
  ),
));