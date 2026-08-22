<?php declare(strict_types = 1);

// odsl-/var/www/html/app/Enums/AuditAction.php-PHPStan\BetterReflection\Reflection\ReflectionClass-App\Enums\AuditAction
return \PHPStan\Cache\CacheItem::__set_state(array(
   'variableKey' => 'v2-6.70.0.3-8.5.9-595073ab11718bebd6423b48dc7b3887a388be36179bbffef12e7ae62a99657e',
   'data' => 
  array (
    'locatedSource' => 
    array (
      'class' => 'PHPStan\\BetterReflection\\SourceLocator\\Located\\LocatedSource',
      'data' => 
      array (
        'name' => 'App\\Enums\\AuditAction',
        'filename' => '/var/www/html/app/Enums/AuditAction.php',
      ),
    ),
    'namespace' => 'App\\Enums',
    'name' => 'App\\Enums\\AuditAction',
    'shortName' => 'AuditAction',
    'isInterface' => false,
    'isTrait' => false,
    'isEnum' => true,
    'isBackedEnum' => true,
    'modifiers' => 0,
    'docComment' => '/**
 * The sensitive actions kept in the audit log.
 *
 * Each case is written to `audit_events.action`. Add a case when a new action
 * must be answerable later with "who did this, to what, and when".
 */',
    'attributes' => 
    array (
    ),
    'startLine' => 11,
    'endLine' => 354,
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
      'name' => 
      array (
        'declaringClassName' => 'App\\Enums\\AuditAction',
        'implementingClassName' => 'App\\Enums\\AuditAction',
        'name' => 'name',
        'modifiers' => 2177,
        'type' => 
        array (
          'class' => 'PHPStan\\BetterReflection\\Reflection\\ReflectionNamedType',
          'data' => 
          array (
            'name' => 'string',
            'isIdentifier' => true,
          ),
        ),
        'default' => NULL,
        'docComment' => NULL,
        'attributes' => 
        array (
        ),
        'startLine' => NULL,
        'endLine' => NULL,
        'startColumn' => -1,
        'endColumn' => -1,
        'isPromoted' => false,
        'declaredAtCompileTime' => true,
        'immediateVirtual' => false,
        'immediateHooks' => 
        array (
        ),
      ),
      'value' => 
      array (
        'declaringClassName' => 'App\\Enums\\AuditAction',
        'implementingClassName' => 'App\\Enums\\AuditAction',
        'name' => 'value',
        'modifiers' => 2177,
        'type' => 
        array (
          'class' => 'PHPStan\\BetterReflection\\Reflection\\ReflectionNamedType',
          'data' => 
          array (
            'name' => 'string',
            'isIdentifier' => true,
          ),
        ),
        'default' => NULL,
        'docComment' => NULL,
        'attributes' => 
        array (
        ),
        'startLine' => NULL,
        'endLine' => NULL,
        'startColumn' => -1,
        'endColumn' => -1,
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
      'label' => 
      array (
        'name' => 'label',
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
 * Get the label to show in the interface.
 */',
        'startLine' => 293,
        'endLine' => 353,
        'startColumn' => 5,
        'endColumn' => 5,
        'couldThrow' => false,
        'isClosure' => false,
        'isGenerator' => false,
        'isVariadic' => false,
        'modifiers' => 1,
        'namespace' => 'App\\Enums',
        'declaringClassName' => 'App\\Enums\\AuditAction',
        'implementingClassName' => 'App\\Enums\\AuditAction',
        'currentClassName' => 'App\\Enums\\AuditAction',
        'aliasName' => NULL,
      ),
      'cases' => 
      array (
        'name' => 'cases',
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
        'docComment' => NULL,
        'startLine' => NULL,
        'endLine' => NULL,
        'startColumn' => -1,
        'endColumn' => -1,
        'couldThrow' => false,
        'isClosure' => false,
        'isGenerator' => false,
        'isVariadic' => false,
        'modifiers' => 17,
        'namespace' => 'App\\Enums',
        'declaringClassName' => 'App\\Enums\\AuditAction',
        'implementingClassName' => 'App\\Enums\\AuditAction',
        'currentClassName' => 'App\\Enums\\AuditAction',
        'aliasName' => NULL,
      ),
      'from' => 
      array (
        'name' => 'from',
        'parameters' => 
        array (
          'value' => 
          array (
            'name' => 'value',
            'default' => NULL,
            'type' => 
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
                      'name' => 'string',
                      'isIdentifier' => true,
                    ),
                  ),
                  1 => 
                  array (
                    'class' => 'PHPStan\\BetterReflection\\Reflection\\ReflectionNamedType',
                    'data' => 
                    array (
                      'name' => 'int',
                      'isIdentifier' => true,
                    ),
                  ),
                ),
              ),
            ),
            'isVariadic' => false,
            'byRef' => false,
            'isPromoted' => false,
            'attributes' => 
            array (
            ),
            'startLine' => NULL,
            'endLine' => NULL,
            'startColumn' => -1,
            'endColumn' => -1,
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
            'name' => 'static',
            'isIdentifier' => true,
          ),
        ),
        'attributes' => 
        array (
        ),
        'docComment' => NULL,
        'startLine' => NULL,
        'endLine' => NULL,
        'startColumn' => -1,
        'endColumn' => -1,
        'couldThrow' => false,
        'isClosure' => false,
        'isGenerator' => false,
        'isVariadic' => false,
        'modifiers' => 17,
        'namespace' => 'App\\Enums',
        'declaringClassName' => 'App\\Enums\\AuditAction',
        'implementingClassName' => 'App\\Enums\\AuditAction',
        'currentClassName' => 'App\\Enums\\AuditAction',
        'aliasName' => NULL,
      ),
      'tryFrom' => 
      array (
        'name' => 'tryFrom',
        'parameters' => 
        array (
          'value' => 
          array (
            'name' => 'value',
            'default' => NULL,
            'type' => 
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
                      'name' => 'string',
                      'isIdentifier' => true,
                    ),
                  ),
                  1 => 
                  array (
                    'class' => 'PHPStan\\BetterReflection\\Reflection\\ReflectionNamedType',
                    'data' => 
                    array (
                      'name' => 'int',
                      'isIdentifier' => true,
                    ),
                  ),
                ),
              ),
            ),
            'isVariadic' => false,
            'byRef' => false,
            'isPromoted' => false,
            'attributes' => 
            array (
            ),
            'startLine' => NULL,
            'endLine' => NULL,
            'startColumn' => -1,
            'endColumn' => -1,
            'parameterIndex' => 0,
            'isOptional' => false,
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
                  'name' => 'static',
                  'isIdentifier' => true,
                ),
              ),
              1 => 
              array (
                'class' => 'PHPStan\\BetterReflection\\Reflection\\ReflectionNamedType',
                'data' => 
                array (
                  'name' => 'null',
                  'isIdentifier' => true,
                ),
              ),
            ),
          ),
        ),
        'attributes' => 
        array (
        ),
        'docComment' => NULL,
        'startLine' => NULL,
        'endLine' => NULL,
        'startColumn' => -1,
        'endColumn' => -1,
        'couldThrow' => false,
        'isClosure' => false,
        'isGenerator' => false,
        'isVariadic' => false,
        'modifiers' => 17,
        'namespace' => 'App\\Enums',
        'declaringClassName' => 'App\\Enums\\AuditAction',
        'implementingClassName' => 'App\\Enums\\AuditAction',
        'currentClassName' => 'App\\Enums\\AuditAction',
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
    'backingType' => 
    array (
      'name' => 'string',
      'isIdentifier' => true,
    ),
    'cases' => 
    array (
      'RoleAttached' => 
      array (
        'name' => 'RoleAttached',
        'value' => 
        array (
          'code' => '\'role.attached\'',
          'attributes' => 
          array (
            'startLine' => 16,
            'endLine' => 16,
            'startTokenPos' => 26,
            'startFilePos' => 337,
            'endTokenPos' => 26,
            'endFilePos' => 351,
          ),
        ),
        'attributes' => 
        array (
        ),
        'docComment' => '/**
 * A role was given to a user.
 */',
        'startLine' => 16,
        'endLine' => 16,
        'startColumn' => 5,
        'endColumn' => 40,
      ),
      'RoleDetached' => 
      array (
        'name' => 'RoleDetached',
        'value' => 
        array (
          'code' => '\'role.detached\'',
          'attributes' => 
          array (
            'startLine' => 21,
            'endLine' => 21,
            'startTokenPos' => 37,
            'startFilePos' => 432,
            'endTokenPos' => 37,
            'endFilePos' => 446,
          ),
        ),
        'attributes' => 
        array (
        ),
        'docComment' => '/**
 * A role was taken from a user.
 */',
        'startLine' => 21,
        'endLine' => 21,
        'startColumn' => 5,
        'endColumn' => 40,
      ),
      'PermissionAttached' => 
      array (
        'name' => 'PermissionAttached',
        'value' => 
        array (
          'code' => '\'permission.attached\'',
          'attributes' => 
          array (
            'startLine' => 26,
            'endLine' => 26,
            'startTokenPos' => 48,
            'startFilePos' => 556,
            'endTokenPos' => 48,
            'endFilePos' => 576,
          ),
        ),
        'attributes' => 
        array (
        ),
        'docComment' => '/**
 * A permission was given directly to a user or a role.
 */',
        'startLine' => 26,
        'endLine' => 26,
        'startColumn' => 5,
        'endColumn' => 52,
      ),
      'PermissionDetached' => 
      array (
        'name' => 'PermissionDetached',
        'value' => 
        array (
          'code' => '\'permission.detached\'',
          'attributes' => 
          array (
            'startLine' => 31,
            'endLine' => 31,
            'startTokenPos' => 59,
            'startFilePos' => 679,
            'endTokenPos' => 59,
            'endFilePos' => 699,
          ),
        ),
        'attributes' => 
        array (
        ),
        'docComment' => '/**
 * A permission was taken from a user or a role.
 */',
        'startLine' => 31,
        'endLine' => 31,
        'startColumn' => 5,
        'endColumn' => 52,
      ),
      'AccountStatusChanged' => 
      array (
        'name' => 'AccountStatusChanged',
        'value' => 
        array (
          'code' => '\'account.status_changed\'',
          'attributes' => 
          array (
            'startLine' => 36,
            'endLine' => 36,
            'startTokenPos' => 70,
            'startFilePos' => 798,
            'endTokenPos' => 70,
            'endFilePos' => 821,
          ),
        ),
        'attributes' => 
        array (
        ),
        'docComment' => '/**
 * An account moved between access states.
 */',
        'startLine' => 36,
        'endLine' => 36,
        'startColumn' => 5,
        'endColumn' => 57,
      ),
      'AccountInvitationSent' => 
      array (
        'name' => 'AccountInvitationSent',
        'value' => 
        array (
          'code' => '\'account_invitation.sent\'',
          'attributes' => 
          array (
            'startLine' => 41,
            'endLine' => 41,
            'startTokenPos' => 81,
            'startFilePos' => 936,
            'endTokenPos' => 81,
            'endFilePos' => 960,
          ),
        ),
        'attributes' => 
        array (
        ),
        'docComment' => '/**
 * An invitation link was issued and emailed to a person.
 */',
        'startLine' => 41,
        'endLine' => 41,
        'startColumn' => 5,
        'endColumn' => 59,
      ),
      'AccountInvitationRevoked' => 
      array (
        'name' => 'AccountInvitationRevoked',
        'value' => 
        array (
          'code' => '\'account_invitation.revoked\'',
          'attributes' => 
          array (
            'startLine' => 46,
            'endLine' => 46,
            'startTokenPos' => 92,
            'startFilePos' => 1080,
            'endTokenPos' => 92,
            'endFilePos' => 1107,
          ),
        ),
        'attributes' => 
        array (
        ),
        'docComment' => '/**
 * Every unused invitation link for an account was stopped.
 */',
        'startLine' => 46,
        'endLine' => 46,
        'startColumn' => 5,
        'endColumn' => 65,
      ),
      'EnrollmentStatusChanged' => 
      array (
        'name' => 'EnrollmentStatusChanged',
        'value' => 
        array (
          'code' => '\'enrollment.status_changed\'',
          'attributes' => 
          array (
            'startLine' => 51,
            'endLine' => 51,
            'startTokenPos' => 103,
            'startFilePos' => 1205,
            'endTokenPos' => 103,
            'endFilePos' => 1231,
          ),
        ),
        'attributes' => 
        array (
        ),
        'docComment' => '/**
 * An enrollment moved between states.
 */',
        'startLine' => 51,
        'endLine' => 51,
        'startColumn' => 5,
        'endColumn' => 63,
      ),
      'EnrollmentPlaced' => 
      array (
        'name' => 'EnrollmentPlaced',
        'value' => 
        array (
          'code' => '\'enrollment.placed\'',
          'attributes' => 
          array (
            'startLine' => 56,
            'endLine' => 56,
            'startTokenPos' => 114,
            'startFilePos' => 1331,
            'endTokenPos' => 114,
            'endFilePos' => 1349,
          ),
        ),
        'attributes' => 
        array (
        ),
        'docComment' => '/**
 * A student was placed in a class and section.
 */',
        'startLine' => 56,
        'endLine' => 56,
        'startColumn' => 5,
        'endColumn' => 48,
      ),
      'EnrollmentTransferred' => 
      array (
        'name' => 'EnrollmentTransferred',
        'value' => 
        array (
          'code' => '\'enrollment.transferred\'',
          'attributes' => 
          array (
            'startLine' => 61,
            'endLine' => 61,
            'startTokenPos' => 125,
            'startFilePos' => 1448,
            'endTokenPos' => 125,
            'endFilePos' => 1471,
          ),
        ),
        'attributes' => 
        array (
        ),
        'docComment' => '/**
 * An enrollment moved to another school.
 */',
        'startLine' => 61,
        'endLine' => 61,
        'startColumn' => 5,
        'endColumn' => 58,
      ),
      'TeachingAssignmentCreated' => 
      array (
        'name' => 'TeachingAssignmentCreated',
        'value' => 
        array (
          'code' => '\'teaching_assignment.created\'',
          'attributes' => 
          array (
            'startLine' => 66,
            'endLine' => 66,
            'startTokenPos' => 136,
            'startFilePos' => 1575,
            'endTokenPos' => 136,
            'endFilePos' => 1603,
          ),
        ),
        'attributes' => 
        array (
        ),
        'docComment' => '/**
 * A teacher was given a subject to teach.
 */',
        'startLine' => 66,
        'endLine' => 66,
        'startColumn' => 5,
        'endColumn' => 67,
      ),
      'TeachingAssignmentEnded' => 
      array (
        'name' => 'TeachingAssignmentEnded',
        'value' => 
        array (
          'code' => '\'teaching_assignment.ended\'',
          'attributes' => 
          array (
            'startLine' => 71,
            'endLine' => 71,
            'startTokenPos' => 147,
            'startFilePos' => 1698,
            'endTokenPos' => 147,
            'endFilePos' => 1724,
          ),
        ),
        'attributes' => 
        array (
        ),
        'docComment' => '/**
 * A teaching assignment was ended.
 */',
        'startLine' => 71,
        'endLine' => 71,
        'startColumn' => 5,
        'endColumn' => 63,
      ),
      'CourseOfferingCreated' => 
      array (
        'name' => 'CourseOfferingCreated',
        'value' => 
        array (
          'code' => '\'course_offering.created\'',
          'attributes' => 
          array (
            'startLine' => 76,
            'endLine' => 76,
            'startTokenPos' => 158,
            'startFilePos' => 1843,
            'endTokenPos' => 158,
            'endFilePos' => 1867,
          ),
        ),
        'attributes' => 
        array (
        ),
        'docComment' => '/**
 * A subject offering was configured for one academic period.
 */',
        'startLine' => 76,
        'endLine' => 76,
        'startColumn' => 5,
        'endColumn' => 59,
      ),
      'CourseOfferingStatusChanged' => 
      array (
        'name' => 'CourseOfferingStatusChanged',
        'value' => 
        array (
          'code' => '\'course_offering.status_changed\'',
          'attributes' => 
          array (
            'startLine' => 81,
            'endLine' => 81,
            'startTokenPos' => 169,
            'startFilePos' => 2002,
            'endTokenPos' => 169,
            'endFilePos' => 2033,
          ),
        ),
        'attributes' => 
        array (
        ),
        'docComment' => '/**
 * A subject offering moved between draft, active, and archived states.
 */',
        'startLine' => 81,
        'endLine' => 81,
        'startColumn' => 5,
        'endColumn' => 72,
      ),
      'TimetablePublished' => 
      array (
        'name' => 'TimetablePublished',
        'value' => 
        array (
          'code' => '\'timetable.published\'',
          'attributes' => 
          array (
            'startLine' => 86,
            'endLine' => 86,
            'startTokenPos' => 180,
            'startFilePos' => 2126,
            'endTokenPos' => 180,
            'endFilePos' => 2146,
          ),
        ),
        'attributes' => 
        array (
        ),
        'docComment' => '/**
 * A timetable revision was published.
 */',
        'startLine' => 86,
        'endLine' => 86,
        'startColumn' => 5,
        'endColumn' => 52,
      ),
      'TimetableArchived' => 
      array (
        'name' => 'TimetableArchived',
        'value' => 
        array (
          'code' => '\'timetable.archived\'',
          'attributes' => 
          array (
            'startLine' => 91,
            'endLine' => 91,
            'startTokenPos' => 191,
            'startFilePos' => 2237,
            'endTokenPos' => 191,
            'endFilePos' => 2256,
          ),
        ),
        'attributes' => 
        array (
        ),
        'docComment' => '/**
 * A timetable revision was archived.
 */',
        'startLine' => 91,
        'endLine' => 91,
        'startColumn' => 5,
        'endColumn' => 50,
      ),
      'TimetableRevised' => 
      array (
        'name' => 'TimetableRevised',
        'value' => 
        array (
          'code' => '\'timetable.revised\'',
          'attributes' => 
          array (
            'startLine' => 96,
            'endLine' => 96,
            'startTokenPos' => 202,
            'startFilePos' => 2370,
            'endTokenPos' => 202,
            'endFilePos' => 2388,
          ),
        ),
        'attributes' => 
        array (
        ),
        'docComment' => '/**
 * A new timetable revision was started from a published one.
 */',
        'startLine' => 96,
        'endLine' => 96,
        'startColumn' => 5,
        'endColumn' => 48,
      ),
      'LedgerTransactionPosted' => 
      array (
        'name' => 'LedgerTransactionPosted',
        'value' => 
        array (
          'code' => '\'ledger.posted\'',
          'attributes' => 
          array (
            'startLine' => 101,
            'endLine' => 101,
            'startTokenPos' => 213,
            'startFilePos' => 2495,
            'endTokenPos' => 213,
            'endFilePos' => 2509,
          ),
        ),
        'attributes' => 
        array (
        ),
        'docComment' => '/**
 * A balanced entry was written into the books.
 */',
        'startLine' => 101,
        'endLine' => 101,
        'startColumn' => 5,
        'endColumn' => 51,
      ),
      'NoticePublished' => 
      array (
        'name' => 'NoticePublished',
        'value' => 
        array (
          'code' => '\'notice.published\'',
          'attributes' => 
          array (
            'startLine' => 106,
            'endLine' => 106,
            'startTokenPos' => 224,
            'startFilePos' => 2594,
            'endTokenPos' => 224,
            'endFilePos' => 2611,
          ),
        ),
        'attributes' => 
        array (
        ),
        'docComment' => '/**
 * A notice was put on the board.
 */',
        'startLine' => 106,
        'endLine' => 106,
        'startColumn' => 5,
        'endColumn' => 46,
      ),
      'NoticeScheduled' => 
      array (
        'name' => 'NoticeScheduled',
        'value' => 
        array (
          'code' => '\'notice.scheduled\'',
          'attributes' => 
          array (
            'startLine' => 111,
            'endLine' => 111,
            'startTokenPos' => 235,
            'startFilePos' => 2700,
            'endTokenPos' => 235,
            'endFilePos' => 2717,
          ),
        ),
        'attributes' => 
        array (
        ),
        'docComment' => '/**
 * A notice was held for a later day.
 */',
        'startLine' => 111,
        'endLine' => 111,
        'startColumn' => 5,
        'endColumn' => 46,
      ),
      'NoticeExpired' => 
      array (
        'name' => 'NoticeExpired',
        'value' => 
        array (
          'code' => '\'notice.expired\'',
          'attributes' => 
          array (
            'startLine' => 116,
            'endLine' => 116,
            'startTokenPos' => 246,
            'startFilePos' => 2799,
            'endTokenPos' => 246,
            'endFilePos' => 2814,
          ),
        ),
        'attributes' => 
        array (
        ),
        'docComment' => '/**
 * A notice passed its last day.
 */',
        'startLine' => 116,
        'endLine' => 116,
        'startColumn' => 5,
        'endColumn' => 42,
      ),
      'ReportRequested' => 
      array (
        'name' => 'ReportRequested',
        'value' => 
        array (
          'code' => '\'report.requested\'',
          'attributes' => 
          array (
            'startLine' => 121,
            'endLine' => 121,
            'startTokenPos' => 257,
            'startFilePos' => 2897,
            'endTokenPos' => 257,
            'endFilePos' => 2914,
          ),
        ),
        'attributes' => 
        array (
        ),
        'docComment' => '/**
 * Somebody asked for a report.
 */',
        'startLine' => 121,
        'endLine' => 121,
        'startColumn' => 5,
        'endColumn' => 46,
      ),
      'ReportDownloaded' => 
      array (
        'name' => 'ReportDownloaded',
        'value' => 
        array (
          'code' => '\'report.downloaded\'',
          'attributes' => 
          array (
            'startLine' => 126,
            'endLine' => 126,
            'startTokenPos' => 268,
            'startFilePos' => 3004,
            'endTokenPos' => 268,
            'endFilePos' => 3022,
          ),
        ),
        'attributes' => 
        array (
        ),
        'docComment' => '/**
 * Somebody downloaded a report file.
 */',
        'startLine' => 126,
        'endLine' => 126,
        'startColumn' => 5,
        'endColumn' => 48,
      ),
      'FeatureEnabled' => 
      array (
        'name' => 'FeatureEnabled',
        'value' => 
        array (
          'code' => '\'feature.enabled\'',
          'attributes' => 
          array (
            'startLine' => 131,
            'endLine' => 131,
            'startTokenPos' => 279,
            'startFilePos' => 3113,
            'endTokenPos' => 279,
            'endFilePos' => 3129,
          ),
        ),
        'attributes' => 
        array (
        ),
        'docComment' => '/**
 * A feature was turned on for a school.
 */',
        'startLine' => 131,
        'endLine' => 131,
        'startColumn' => 5,
        'endColumn' => 44,
      ),
      'FeatureDisabled' => 
      array (
        'name' => 'FeatureDisabled',
        'value' => 
        array (
          'code' => '\'feature.disabled\'',
          'attributes' => 
          array (
            'startLine' => 136,
            'endLine' => 136,
            'startTokenPos' => 290,
            'startFilePos' => 3222,
            'endTokenPos' => 290,
            'endFilePos' => 3239,
          ),
        ),
        'attributes' => 
        array (
        ),
        'docComment' => '/**
 * A feature was turned off for a school.
 */',
        'startLine' => 136,
        'endLine' => 136,
        'startColumn' => 5,
        'endColumn' => 46,
      ),
      'IncidentReported' => 
      array (
        'name' => 'IncidentReported',
        'value' => 
        array (
          'code' => '\'incident.reported\'',
          'attributes' => 
          array (
            'startLine' => 141,
            'endLine' => 141,
            'startTokenPos' => 301,
            'startFilePos' => 3341,
            'endTokenPos' => 301,
            'endFilePos' => 3359,
          ),
        ),
        'attributes' => 
        array (
        ),
        'docComment' => '/**
 * A behaviour or safeguarding case was recorded.
 */',
        'startLine' => 141,
        'endLine' => 141,
        'startColumn' => 5,
        'endColumn' => 48,
      ),
      'IncidentStatusChanged' => 
      array (
        'name' => 'IncidentStatusChanged',
        'value' => 
        array (
          'code' => '\'incident.status_changed\'',
          'attributes' => 
          array (
            'startLine' => 146,
            'endLine' => 146,
            'startTokenPos' => 312,
            'startFilePos' => 3448,
            'endTokenPos' => 312,
            'endFilePos' => 3472,
          ),
        ),
        'attributes' => 
        array (
        ),
        'docComment' => '/**
 * A case moved between states.
 */',
        'startLine' => 146,
        'endLine' => 146,
        'startColumn' => 5,
        'endColumn' => 59,
      ),
      'AcademicPeriodStatusChanged' => 
      array (
        'name' => 'AcademicPeriodStatusChanged',
        'value' => 
        array (
          'code' => '\'academic_period.status_changed\'',
          'attributes' => 
          array (
            'startLine' => 151,
            'endLine' => 151,
            'startTokenPos' => 323,
            'startFilePos' => 3607,
            'endTokenPos' => 323,
            'endFilePos' => 3638,
          ),
        ),
        'attributes' => 
        array (
        ),
        'docComment' => '/**
 * An academic year or academic period was opened, closed, or reopened.
 */',
        'startLine' => 151,
        'endLine' => 151,
        'startColumn' => 5,
        'endColumn' => 72,
      ),
      'AcademicCycleGenerated' => 
      array (
        'name' => 'AcademicCycleGenerated',
        'value' => 
        array (
          'code' => '\'academic_cycle.generated\'',
          'attributes' => 
          array (
            'startLine' => 156,
            'endLine' => 156,
            'startTokenPos' => 334,
            'startFilePos' => 3747,
            'endTokenPos' => 334,
            'endFilePos' => 3772,
          ),
        ),
        'attributes' => 
        array (
        ),
        'docComment' => '/**
 * A cycle was generated from a calendar template.
 */',
        'startLine' => 156,
        'endLine' => 156,
        'startColumn' => 5,
        'endColumn' => 61,
      ),
      'AcademicPeriodDatesChanged' => 
      array (
        'name' => 'AcademicPeriodDatesChanged',
        'value' => 
        array (
          'code' => '\'academic_period.dates_changed\'',
          'attributes' => 
          array (
            'startLine' => 161,
            'endLine' => 161,
            'startTokenPos' => 345,
            'startFilePos' => 3897,
            'endTokenPos' => 345,
            'endFilePos' => 3927,
          ),
        ),
        'attributes' => 
        array (
        ),
        'docComment' => '/**
 * The dates of a period that was already in use were changed.
 */',
        'startLine' => 161,
        'endLine' => 161,
        'startColumn' => 5,
        'endColumn' => 70,
      ),
      'CampusCalendarOverridden' => 
      array (
        'name' => 'CampusCalendarOverridden',
        'value' => 
        array (
          'code' => '\'campus_calendar.overridden\'',
          'attributes' => 
          array (
            'startLine' => 166,
            'endLine' => 166,
            'startTokenPos' => 356,
            'startFilePos' => 4061,
            'endTokenPos' => 356,
            'endFilePos' => 4088,
          ),
        ),
        'attributes' => 
        array (
        ),
        'docComment' => '/**
 * A campus stopped following its organization\'s calendar, or resumed it.
 */',
        'startLine' => 166,
        'endLine' => 166,
        'startColumn' => 5,
        'endColumn' => 65,
      ),
      'CalendarTemplateSaved' => 
      array (
        'name' => 'CalendarTemplateSaved',
        'value' => 
        array (
          'code' => '\'calendar_template.saved\'',
          'attributes' => 
          array (
            'startLine' => 171,
            'endLine' => 171,
            'startTokenPos' => 367,
            'startFilePos' => 4192,
            'endTokenPos' => 367,
            'endFilePos' => 4216,
          ),
        ),
        'attributes' => 
        array (
        ),
        'docComment' => '/**
 * A calendar template was created or changed.
 */',
        'startLine' => 171,
        'endLine' => 171,
        'startColumn' => 5,
        'endColumn' => 59,
      ),
      'InstructionalModelChanged' => 
      array (
        'name' => 'InstructionalModelChanged',
        'value' => 
        array (
          'code' => '\'instructional_model.changed\'',
          'attributes' => 
          array (
            'startLine' => 176,
            'endLine' => 176,
            'startTokenPos' => 378,
            'startFilePos' => 4333,
            'endTokenPos' => 378,
            'endFilePos' => 4361,
          ),
        ),
        'attributes' => 
        array (
        ),
        'docComment' => '/**
 * A campus chose the way it teaches an academic cycle.
 */',
        'startLine' => 176,
        'endLine' => 176,
        'startColumn' => 5,
        'endColumn' => 67,
      ),
      'AcademicLevelCreated' => 
      array (
        'name' => 'AcademicLevelCreated',
        'value' => 
        array (
          'code' => '\'academic_level.created\'',
          'attributes' => 
          array (
            'startLine' => 181,
            'endLine' => 181,
            'startTokenPos' => 389,
            'startFilePos' => 4470,
            'endTokenPos' => 389,
            'endFilePos' => 4493,
          ),
        ),
        'attributes' => 
        array (
        ),
        'docComment' => '/**
 * A reusable academic level was added for a campus.
 */',
        'startLine' => 181,
        'endLine' => 181,
        'startColumn' => 5,
        'endColumn' => 57,
      ),
      'AcademicCycleSectionCreated' => 
      array (
        'name' => 'AcademicCycleSectionCreated',
        'value' => 
        array (
          'code' => '\'academic_cycle_section.created\'',
          'attributes' => 
          array (
            'startLine' => 186,
            'endLine' => 186,
            'startTokenPos' => 400,
            'startFilePos' => 4605,
            'endTokenPos' => 400,
            'endFilePos' => 4636,
          ),
        ),
        'attributes' => 
        array (
        ),
        'docComment' => '/**
 * A cycle-specific home section was configured.
 */',
        'startLine' => 186,
        'endLine' => 186,
        'startColumn' => 5,
        'endColumn' => 72,
      ),
      'AcademicCycleSectionStatusChanged' => 
      array (
        'name' => 'AcademicCycleSectionStatusChanged',
        'value' => 
        array (
          'code' => '\'academic_cycle_section.status_changed\'',
          'attributes' => 
          array (
            'startLine' => 191,
            'endLine' => 191,
            'startTokenPos' => 411,
            'startFilePos' => 4770,
            'endTokenPos' => 411,
            'endFilePos' => 4808,
          ),
        ),
        'attributes' => 
        array (
        ),
        'docComment' => '/**
 * A cycle-specific home section moved between lifecycle states.
 */',
        'startLine' => 191,
        'endLine' => 191,
        'startColumn' => 5,
        'endColumn' => 85,
      ),
      'AcademicCycleSectionsRolledForward' => 
      array (
        'name' => 'AcademicCycleSectionsRolledForward',
        'value' => 
        array (
          'code' => '\'academic_cycle_section.rolled_forward\'',
          'attributes' => 
          array (
            'startLine' => 196,
            'endLine' => 196,
            'startTokenPos' => 422,
            'startFilePos' => 4939,
            'endTokenPos' => 422,
            'endFilePos' => 4977,
          ),
        ),
        'attributes' => 
        array (
        ),
        'docComment' => '/**
 * Section structure was copied into another academic cycle.
 */',
        'startLine' => 196,
        'endLine' => 196,
        'startColumn' => 5,
        'endColumn' => 86,
      ),
      'ResultPublished' => 
      array (
        'name' => 'ResultPublished',
        'value' => 
        array (
          'code' => '\'result.published\'',
          'attributes' => 
          array (
            'startLine' => 201,
            'endLine' => 201,
            'startTokenPos' => 433,
            'startFilePos' => 5074,
            'endTokenPos' => 433,
            'endFilePos' => 5091,
          ),
        ),
        'attributes' => 
        array (
        ),
        'docComment' => '/**
 * A result was published from the gradebook.
 */',
        'startLine' => 201,
        'endLine' => 201,
        'startColumn' => 5,
        'endColumn' => 46,
      ),
      'ResultRevised' => 
      array (
        'name' => 'ResultRevised',
        'value' => 
        array (
          'code' => '\'result.revised\'',
          'attributes' => 
          array (
            'startLine' => 206,
            'endLine' => 206,
            'startTokenPos' => 444,
            'startFilePos' => 5197,
            'endTokenPos' => 444,
            'endFilePos' => 5212,
          ),
        ),
        'attributes' => 
        array (
        ),
        'docComment' => '/**
 * A published result was corrected with a new revision.
 */',
        'startLine' => 206,
        'endLine' => 206,
        'startColumn' => 5,
        'endColumn' => 42,
      ),
      'ExamResultPublished' => 
      array (
        'name' => 'ExamResultPublished',
        'value' => 
        array (
          'code' => '\'exam.result_published\'',
          'attributes' => 
          array (
            'startLine' => 211,
            'endLine' => 211,
            'startTokenPos' => 455,
            'startFilePos' => 5326,
            'endTokenPos' => 455,
            'endFilePos' => 5348,
          ),
        ),
        'attributes' => 
        array (
        ),
        'docComment' => '/**
 * Exam results were made visible to students and parents.
 */',
        'startLine' => 211,
        'endLine' => 211,
        'startColumn' => 5,
        'endColumn' => 55,
      ),
      'ExamResultUnpublished' => 
      array (
        'name' => 'ExamResultUnpublished',
        'value' => 
        array (
          'code' => '\'exam.result_unpublished\'',
          'attributes' => 
          array (
            'startLine' => 216,
            'endLine' => 216,
            'startTokenPos' => 466,
            'startFilePos' => 5440,
            'endTokenPos' => 466,
            'endFilePos' => 5464,
          ),
        ),
        'attributes' => 
        array (
        ),
        'docComment' => '/**
 * Exam results were hidden again.
 */',
        'startLine' => 216,
        'endLine' => 216,
        'startColumn' => 5,
        'endColumn' => 59,
      ),
      'SupportPlanOpened' => 
      array (
        'name' => 'SupportPlanOpened',
        'value' => 
        array (
          'code' => '\'support_plan.opened\'',
          'attributes' => 
          array (
            'startLine' => 221,
            'endLine' => 221,
            'startTokenPos' => 477,
            'startFilePos' => 5560,
            'endTokenPos' => 477,
            'endFilePos' => 5580,
          ),
        ),
        'attributes' => 
        array (
        ),
        'docComment' => '/**
 * A support plan was written for a child.
 */',
        'startLine' => 221,
        'endLine' => 221,
        'startColumn' => 5,
        'endColumn' => 51,
      ),
      'SupportPlanStatusChanged' => 
      array (
        'name' => 'SupportPlanStatusChanged',
        'value' => 
        array (
          'code' => '\'support_plan.status_changed\'',
          'attributes' => 
          array (
            'startLine' => 226,
            'endLine' => 226,
            'startTokenPos' => 488,
            'startFilePos' => 5680,
            'endTokenPos' => 488,
            'endFilePos' => 5708,
          ),
        ),
        'attributes' => 
        array (
        ),
        'docComment' => '/**
 * A support plan moved between states.
 */',
        'startLine' => 226,
        'endLine' => 226,
        'startColumn' => 5,
        'endColumn' => 66,
      ),
      'HealthRecordUpdated' => 
      array (
        'name' => 'HealthRecordUpdated',
        'value' => 
        array (
          'code' => '\'health_record.updated\'',
          'attributes' => 
          array (
            'startLine' => 231,
            'endLine' => 231,
            'startTokenPos' => 499,
            'startFilePos' => 5814,
            'endTokenPos' => 499,
            'endFilePos' => 5836,
          ),
        ),
        'attributes' => 
        array (
        ),
        'docComment' => '/**
 * A child\'s health record was written or changed.
 */',
        'startLine' => 231,
        'endLine' => 231,
        'startColumn' => 5,
        'endColumn' => 55,
      ),
      'StaffLeaveRequested' => 
      array (
        'name' => 'StaffLeaveRequested',
        'value' => 
        array (
          'code' => '\'staff_leave.requested\'',
          'attributes' => 
          array (
            'startLine' => 236,
            'endLine' => 236,
            'startTokenPos' => 510,
            'startFilePos' => 5933,
            'endTokenPos' => 510,
            'endFilePos' => 5955,
          ),
        ),
        'attributes' => 
        array (
        ),
        'docComment' => '/**
 * A member of staff asked for days away.
 */',
        'startLine' => 236,
        'endLine' => 236,
        'startColumn' => 5,
        'endColumn' => 55,
      ),
      'StaffLeaveStatusChanged' => 
      array (
        'name' => 'StaffLeaveStatusChanged',
        'value' => 
        array (
          'code' => '\'staff_leave.status_changed\'',
          'attributes' => 
          array (
            'startLine' => 241,
            'endLine' => 241,
            'startTokenPos' => 521,
            'startFilePos' => 6068,
            'endTokenPos' => 521,
            'endFilePos' => 6095,
          ),
        ),
        'attributes' => 
        array (
        ),
        'docComment' => '/**
 * A leave request was answered or recorded as taken.
 */',
        'startLine' => 241,
        'endLine' => 241,
        'startColumn' => 5,
        'endColumn' => 64,
      ),
      'DataSharingRequested' => 
      array (
        'name' => 'DataSharingRequested',
        'value' => 
        array (
          'code' => '\'data_sharing.requested\'',
          'attributes' => 
          array (
            'startLine' => 246,
            'endLine' => 246,
            'startTokenPos' => 532,
            'startFilePos' => 6204,
            'endTokenPos' => 532,
            'endFilePos' => 6227,
          ),
        ),
        'attributes' => 
        array (
        ),
        'docComment' => '/**
 * One school asked another for a student\'s records.
 */',
        'startLine' => 246,
        'endLine' => 246,
        'startColumn' => 5,
        'endColumn' => 57,
      ),
      'DataSharingStatusChanged' => 
      array (
        'name' => 'DataSharingStatusChanged',
        'value' => 
        array (
          'code' => '\'data_sharing.status_changed\'',
          'attributes' => 
          array (
            'startLine' => 251,
            'endLine' => 251,
            'startTokenPos' => 543,
            'startFilePos' => 6345,
            'endTokenPos' => 543,
            'endFilePos' => 6373,
          ),
        ),
        'attributes' => 
        array (
        ),
        'docComment' => '/**
 * A request to share records was answered or taken back.
 */',
        'startLine' => 251,
        'endLine' => 251,
        'startColumn' => 5,
        'endColumn' => 66,
      ),
      'TransferPackageBuilt' => 
      array (
        'name' => 'TransferPackageBuilt',
        'value' => 
        array (
          'code' => '\'transfer_package.built\'',
          'attributes' => 
          array (
            'startLine' => 256,
            'endLine' => 256,
            'startTokenPos' => 554,
            'startFilePos' => 6480,
            'endTokenPos' => 554,
            'endFilePos' => 6503,
          ),
        ),
        'attributes' => 
        array (
        ),
        'docComment' => '/**
 * Records were handed over in a transfer package.
 */',
        'startLine' => 256,
        'endLine' => 256,
        'startColumn' => 5,
        'endColumn' => 57,
      ),
      'TransferPackageReceived' => 
      array (
        'name' => 'TransferPackageReceived',
        'value' => 
        array (
          'code' => '\'transfer_package.received\'',
          'attributes' => 
          array (
            'startLine' => 261,
            'endLine' => 261,
            'startTokenPos' => 565,
            'startFilePos' => 6630,
            'endTokenPos' => 565,
            'endFilePos' => 6656,
          ),
        ),
        'attributes' => 
        array (
        ),
        'docComment' => '/**
 * A transfer package was taken in by the school that asked for it.
 */',
        'startLine' => 261,
        'endLine' => 261,
        'startColumn' => 5,
        'endColumn' => 63,
      ),
      'OrganizationCreated' => 
      array (
        'name' => 'OrganizationCreated',
        'value' => 
        array (
          'code' => '\'organization.created\'',
          'attributes' => 
          array (
            'startLine' => 266,
            'endLine' => 266,
            'startTokenPos' => 576,
            'startFilePos' => 6754,
            'endTokenPos' => 576,
            'endFilePos' => 6775,
          ),
        ),
        'attributes' => 
        array (
        ),
        'docComment' => '/**
 * An organization was created or updated.
 */',
        'startLine' => 266,
        'endLine' => 266,
        'startColumn' => 5,
        'endColumn' => 54,
      ),
      'OrganizationUpdated' => 
      array (
        'name' => 'OrganizationUpdated',
        'value' => 
        array (
          'code' => '\'organization.updated\'',
          'attributes' => 
          array (
            'startLine' => 268,
            'endLine' => 268,
            'startTokenPos' => 585,
            'startFilePos' => 6810,
            'endTokenPos' => 585,
            'endFilePos' => 6831,
          ),
        ),
        'attributes' => 
        array (
        ),
        'docComment' => NULL,
        'startLine' => 268,
        'endLine' => 268,
        'startColumn' => 5,
        'endColumn' => 54,
      ),
      'SchoolOrganizationAssigned' => 
      array (
        'name' => 'SchoolOrganizationAssigned',
        'value' => 
        array (
          'code' => '\'school.organization_assigned\'',
          'attributes' => 
          array (
            'startLine' => 273,
            'endLine' => 273,
            'startTokenPos' => 596,
            'startFilePos' => 6938,
            'endTokenPos' => 596,
            'endFilePos' => 6967,
          ),
        ),
        'attributes' => 
        array (
        ),
        'docComment' => '/**
 * A campus was assigned to an organization.
 */',
        'startLine' => 273,
        'endLine' => 273,
        'startColumn' => 5,
        'endColumn' => 69,
      ),
      'OrganizationMembershipGranted' => 
      array (
        'name' => 'OrganizationMembershipGranted',
        'value' => 
        array (
          'code' => '\'organization_membership.granted\'',
          'attributes' => 
          array (
            'startLine' => 278,
            'endLine' => 278,
            'startTokenPos' => 607,
            'startFilePos' => 7085,
            'endTokenPos' => 607,
            'endFilePos' => 7117,
          ),
        ),
        'attributes' => 
        array (
        ),
        'docComment' => '/**
 * A person was granted organization administration.
 */',
        'startLine' => 278,
        'endLine' => 278,
        'startColumn' => 5,
        'endColumn' => 75,
      ),
      'OrganizationMembershipRevoked' => 
      array (
        'name' => 'OrganizationMembershipRevoked',
        'value' => 
        array (
          'code' => '\'organization_membership.revoked\'',
          'attributes' => 
          array (
            'startLine' => 283,
            'endLine' => 283,
            'startTokenPos' => 618,
            'startFilePos' => 7254,
            'endTokenPos' => 618,
            'endFilePos' => 7286,
          ),
        ),
        'attributes' => 
        array (
        ),
        'docComment' => '/**
 * A person\'s organization scope was taken away. School access is kept.
 */',
        'startLine' => 283,
        'endLine' => 283,
        'startColumn' => 5,
        'endColumn' => 75,
      ),
      'OrganizationMembershipPermissionsChanged' => 
      array (
        'name' => 'OrganizationMembershipPermissionsChanged',
        'value' => 
        array (
          'code' => '\'organization_membership.permissions_changed\'',
          'attributes' => 
          array (
            'startLine' => 288,
            'endLine' => 288,
            'startTokenPos' => 629,
            'startFilePos' => 7431,
            'endTokenPos' => 629,
            'endFilePos' => 7475,
          ),
        ),
        'attributes' => 
        array (
        ),
        'docComment' => '/**
 * The permissions delegated to an organization member were changed.
 */',
        'startLine' => 288,
        'endLine' => 288,
        'startColumn' => 5,
        'endColumn' => 98,
      ),
    ),
  ),
));