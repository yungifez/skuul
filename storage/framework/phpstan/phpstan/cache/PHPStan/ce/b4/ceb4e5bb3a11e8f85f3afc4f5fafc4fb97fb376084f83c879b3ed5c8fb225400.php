<?php declare(strict_types = 1);

// odsl-/var/www/html/app
return \PHPStan\Cache\CacheItem::__set_state(array(
   'variableKey' => 'v1-enums',
   'data' => 
  array (
    '/var/www/html/app/Actions/Academic/ChangeAcademicPeriodDates.php' => 
    array (
      0 => '90f4b670813b3fd893dbfa504ceb3bdee27a326bfc747088da4686a008897e8a',
      1 => 
      array (
        0 => 'app\\actions\\academic\\changeacademicperioddates',
      ),
      2 => 
      array (
        0 => 'app\\actions\\academic\\__construct',
        1 => 'app\\actions\\academic\\change',
        2 => 'app\\actions\\academic\\refusemovingoutsidethecycle',
      ),
      3 => 
      array (
      ),
    ),
    '/var/www/html/app/Actions/Academic/ChangeAcademicPeriodStatus.php' => 
    array (
      0 => '495bed605a9db723bfa681dd5847dee0d143f61a6df20982cc46a1f4f08cfb2c',
      1 => 
      array (
        0 => 'app\\actions\\academic\\changeacademicperiodstatus',
      ),
      2 => 
      array (
        0 => 'app\\actions\\academic\\__construct',
        1 => 'app\\actions\\academic\\change',
        2 => 'app\\actions\\academic\\refuseimpossiblemove',
        3 => 'app\\actions\\academic\\cascade',
        4 => 'app\\actions\\academic\\beginclosing',
        5 => 'app\\actions\\academic\\close',
        6 => 'app\\actions\\academic\\reopen',
        7 => 'app\\actions\\academic\\archive',
        8 => 'app\\actions\\academic\\schedule',
        9 => 'app\\actions\\academic\\open',
      ),
      3 => 
      array (
      ),
    ),
    '/var/www/html/app/Actions/Attendance/RecordAttendance.php' => 
    array (
      0 => '2c4872e09f5d4cef194a3ac0a097d9f3c3335981fe8c222b33c8225c4bd34c07',
      1 => 
      array (
        0 => 'app\\actions\\attendance\\recordattendance',
      ),
      2 => 
      array (
        0 => 'app\\actions\\attendance\\record',
        1 => 'app\\actions\\attendance\\recordmany',
        2 => 'app\\actions\\attendance\\failifrecordsdonotfit',
      ),
      3 => 
      array (
      ),
    ),
    '/var/www/html/app/Actions/Audit/RecordAuditEvent.php' => 
    array (
      0 => 'ef8c3c4b300e7c4f4a44484572456f66f8baaf25ce3b16c487d3dfa03679417b',
      1 => 
      array (
        0 => 'app\\actions\\audit\\recordauditevent',
      ),
      2 => 
      array (
        0 => 'app\\actions\\audit\\record',
        1 => 'app\\actions\\audit\\schoolof',
      ),
      3 => 
      array (
      ),
    ),
    '/var/www/html/app/Actions/Authorization/GrantSystemRole.php' => 
    array (
      0 => '3cef2efb17589b2f20b27ba4f454230047ae51aaf062e7f8f97375c862719427',
      1 => 
      array (
        0 => 'app\\actions\\authorization\\grantsystemrole',
      ),
      2 => 
      array (
        0 => 'app\\actions\\authorization\\__construct',
        1 => 'app\\actions\\authorization\\grant',
      ),
      3 => 
      array (
      ),
    ),
    '/var/www/html/app/Actions/Authorization/RevokeSystemRole.php' => 
    array (
      0 => '75a64f904a6fdebf84a41394d7b162bb6cc458f59de2696ee6ea256d8a936f66',
      1 => 
      array (
        0 => 'app\\actions\\authorization\\revokesystemrole',
      ),
      2 => 
      array (
        0 => 'app\\actions\\authorization\\__construct',
        1 => 'app\\actions\\authorization\\revoke',
      ),
      3 => 
      array (
      ),
    ),
    '/var/www/html/app/Actions/Calendar/GenerateAcademicCycle.php' => 
    array (
      0 => '50b58845ad34f5f9319d26f4559ccda2632b7aad1698db1b435670c1ea96d2f5',
      1 => 
      array (
        0 => 'app\\actions\\calendar\\generateacademiccycle',
      ),
      2 => 
      array (
        0 => 'app\\actions\\calendar\\__construct',
        1 => 'app\\actions\\calendar\\generate',
        2 => 'app\\actions\\calendar\\createperiods',
        3 => 'app\\actions\\calendar\\refuseoverlap',
      ),
      3 => 
      array (
      ),
    ),
    '/var/www/html/app/Actions/Calendar/SaveCalendarTemplate.php' => 
    array (
      0 => '3c31e57fc565b83b058104cf4b58e4814cef72fd8f7f41387f229b8b5c2587b3',
      1 => 
      array (
        0 => 'app\\actions\\calendar\\savecalendartemplate',
      ),
      2 => 
      array (
        0 => 'app\\actions\\calendar\\__construct',
        1 => 'app\\actions\\calendar\\save',
        2 => 'app\\actions\\calendar\\periods',
        3 => 'app\\actions\\calendar\\writeperiods',
      ),
      3 => 
      array (
      ),
    ),
    '/var/www/html/app/Actions/Calendar/SetCampusCalendarTemplate.php' => 
    array (
      0 => '21213da0301b9f6cb58596e8366b244bcc4de05b47fe30b8e3e621074064fbdd',
      1 => 
      array (
        0 => 'app\\actions\\calendar\\setcampuscalendartemplate',
      ),
      2 => 
      array (
        0 => 'app\\actions\\calendar\\__construct',
        1 => 'app\\actions\\calendar\\override',
        2 => 'app\\actions\\calendar\\inherit',
        3 => 'app\\actions\\calendar\\apply',
      ),
      3 => 
      array (
      ),
    ),
    '/var/www/html/app/Actions/Cohort/ChangeCohortMembership.php' => 
    array (
      0 => '04aa856016bdd1393a1b402b7d546b5c9d299253902555453063b81c9f575913',
      1 => 
      array (
        0 => 'app\\actions\\cohort\\changecohortmembership',
      ),
      2 => 
      array (
        0 => 'app\\actions\\cohort\\addstudent',
        1 => 'app\\actions\\cohort\\addperson',
        2 => 'app\\actions\\cohort\\remove',
      ),
      3 => 
      array (
      ),
    ),
    '/var/www/html/app/Actions/Cohort/ChangeProgramParticipation.php' => 
    array (
      0 => 'c1d468de895fc0e1172faa541c328a43c2339ebe75da39341720e2536eca5d31',
      1 => 
      array (
        0 => 'app\\actions\\cohort\\changeprogramparticipation',
      ),
      2 => 
      array (
        0 => 'app\\actions\\cohort\\join',
        1 => 'app\\actions\\cohort\\changestatus',
      ),
      3 => 
      array (
      ),
    ),
    '/var/www/html/app/Actions/Curriculum/AssignTeacher.php' => 
    array (
      0 => '34242b423c1fa54a896bc4460fa4e66c418e7e294afdda8845fcf31ad89c38a8',
      1 => 
      array (
        0 => 'app\\actions\\curriculum\\assignteacher',
      ),
      2 => 
      array (
        0 => 'app\\actions\\curriculum\\__construct',
        1 => 'app\\actions\\curriculum\\assign',
        2 => 'app\\actions\\curriculum\\end',
        3 => 'app\\actions\\curriculum\\failifrecordsdonotfit',
        4 => 'app\\actions\\curriculum\\failifcourseofferingdoesnotfit',
      ),
      3 => 
      array (
      ),
    ),
    '/var/www/html/app/Actions/Curriculum/ChangeAcademicCycleSectionStatus.php' => 
    array (
      0 => '4b0d0b6be4dae29bd858d46d4aa18f043e32ff138097bcf9c9fd4670fbcf9f76',
      1 => 
      array (
        0 => 'app\\actions\\curriculum\\changeacademiccyclesectionstatus',
      ),
      2 => 
      array (
        0 => 'app\\actions\\curriculum\\__construct',
        1 => 'app\\actions\\curriculum\\change',
      ),
      3 => 
      array (
      ),
    ),
    '/var/www/html/app/Actions/Curriculum/ChangeCourseOfferingStatus.php' => 
    array (
      0 => '5ec8c96f4f8cf3f535ed94af4550c221688950e2e3a0c123f480da764c0b433e',
      1 => 
      array (
        0 => 'app\\actions\\curriculum\\changecourseofferingstatus',
      ),
      2 => 
      array (
        0 => 'app\\actions\\curriculum\\__construct',
        1 => 'app\\actions\\curriculum\\change',
      ),
      3 => 
      array (
      ),
    ),
    '/var/www/html/app/Actions/Curriculum/CreateAcademicCycleSection.php' => 
    array (
      0 => 'e8f404be305952111eb4044a2187d0e204ad6ed8eed219b2b7a774764631fb4d',
      1 => 
      array (
        0 => 'app\\actions\\curriculum\\createacademiccyclesection',
      ),
      2 => 
      array (
        0 => 'app\\actions\\curriculum\\__construct',
        1 => 'app\\actions\\curriculum\\create',
        2 => 'app\\actions\\curriculum\\failifrecordsdonotfit',
      ),
      3 => 
      array (
      ),
    ),
    '/var/www/html/app/Actions/Curriculum/CreateAcademicLevel.php' => 
    array (
      0 => 'b8de1365de5ad792e5f4e17ae14a4d4b1f2f9d8576067915f413f77109c3f753',
      1 => 
      array (
        0 => 'app\\actions\\curriculum\\createacademiclevel',
      ),
      2 => 
      array (
        0 => 'app\\actions\\curriculum\\__construct',
        1 => 'app\\actions\\curriculum\\create',
      ),
      3 => 
      array (
      ),
    ),
    '/var/www/html/app/Actions/Curriculum/CreateCourseOffering.php' => 
    array (
      0 => 'f907fe186251b719d5d113807a49cef2143cc7d84b19a8eb02d84c3d9896f9cf',
      1 => 
      array (
        0 => 'app\\actions\\curriculum\\createcourseoffering',
      ),
      2 => 
      array (
        0 => 'app\\actions\\curriculum\\__construct',
        1 => 'app\\actions\\curriculum\\create',
        2 => 'app\\actions\\curriculum\\failifrecordsdonotfit',
        3 => 'app\\actions\\curriculum\\requirerostercount',
        4 => 'app\\actions\\curriculum\\requireatleastrostercount',
        5 => 'app\\actions\\curriculum\\requireemptyroster',
        6 => 'app\\actions\\curriculum\\requireindividualroster',
      ),
      3 => 
      array (
      ),
    ),
    '/var/www/html/app/Actions/Curriculum/RollForwardAcademicCycleSections.php' => 
    array (
      0 => '3e2e70f57810fc832cf34317eda84a71f7449ab7b15a7e3ad2bd478454eb65c4',
      1 => 
      array (
        0 => 'app\\actions\\curriculum\\rollforwardacademiccyclesections',
      ),
      2 => 
      array (
        0 => 'app\\actions\\curriculum\\__construct',
        1 => 'app\\actions\\curriculum\\rollforward',
      ),
      3 => 
      array (
      ),
    ),
    '/var/www/html/app/Actions/Curriculum/SetInstructionalModel.php' => 
    array (
      0 => '7b1293cf9334d9454735d247c1d96cc1b8a879dd65287a356a999ee6e81842c2',
      1 => 
      array (
        0 => 'app\\actions\\curriculum\\setinstructionalmodel',
      ),
      2 => 
      array (
        0 => 'app\\actions\\curriculum\\__construct',
        1 => 'app\\actions\\curriculum\\set',
        2 => 'app\\actions\\curriculum\\isfuturecycle',
        3 => 'app\\actions\\curriculum\\refusecyclethatstarted',
      ),
      3 => 
      array (
      ),
    ),
    '/var/www/html/app/Actions/Discipline/ReportIncident.php' => 
    array (
      0 => 'e0a9c44bdfc59d044d5b5e401605d848a021ea56b327f4a8e27b0c54f7deaddc',
      1 => 
      array (
        0 => 'app\\actions\\discipline\\reportincident',
      ),
      2 => 
      array (
        0 => 'app\\actions\\discipline\\__construct',
        1 => 'app\\actions\\discipline\\report',
        2 => 'app\\actions\\discipline\\changestatus',
        3 => 'app\\actions\\discipline\\addaction',
        4 => 'app\\actions\\discipline\\reference',
      ),
      3 => 
      array (
      ),
    ),
    '/var/www/html/app/Actions/Enrollment/ChangeEnrollmentPlacement.php' => 
    array (
      0 => 'f2865e0ce071de9f2fe87bfb40533ebb712c7992b1f5a986f3955c46e993bdf5',
      1 => 
      array (
        0 => 'app\\actions\\enrollment\\changeenrollmentplacement',
      ),
      2 => 
      array (
        0 => 'app\\actions\\enrollment\\__construct',
        1 => 'app\\actions\\enrollment\\place',
        2 => 'app\\actions\\enrollment\\failifrecordsdonotfit',
        3 => 'app\\actions\\enrollment\\alreadyplaced',
      ),
      3 => 
      array (
      ),
    ),
    '/var/www/html/app/Actions/Enrollment/ChangeEnrollmentStatus.php' => 
    array (
      0 => 'a4828ea7de0311fe881c7debdd968327bcafbb19faada19a3cf89fed34856034',
      1 => 
      array (
        0 => 'app\\actions\\enrollment\\changeenrollmentstatus',
      ),
      2 => 
      array (
        0 => 'app\\actions\\enrollment\\__construct',
        1 => 'app\\actions\\enrollment\\change',
        2 => 'app\\actions\\enrollment\\graduate',
        3 => 'app\\actions\\enrollment\\returntoattendance',
      ),
      3 => 
      array (
      ),
    ),
    '/var/www/html/app/Actions/Enrollment/TransferEnrollment.php' => 
    array (
      0 => '2a7be33a3901e52aa047d6cd4be5ddcf7e845c077f6a0119451db865a4e9d22b',
      1 => 
      array (
        0 => 'app\\actions\\enrollment\\transferenrollment',
      ),
      2 => 
      array (
        0 => 'app\\actions\\enrollment\\__construct',
        1 => 'app\\actions\\enrollment\\transfer',
      ),
      3 => 
      array (
      ),
    ),
    '/var/www/html/app/Actions/Finance/ChargeStudent.php' => 
    array (
      0 => '3b1215328618a6cd21d1759910472f244106eef8b863356f5f65e3a163a0d500',
      1 => 
      array (
        0 => 'app\\actions\\finance\\chargestudent',
      ),
      2 => 
      array (
        0 => 'app\\actions\\finance\\__construct',
        1 => 'app\\actions\\finance\\charge',
      ),
      3 => 
      array (
      ),
    ),
    '/var/www/html/app/Actions/Finance/PostLedgerTransaction.php' => 
    array (
      0 => '5ad4fb8078c3b7a6e80f03742dccf02a5dda91e8cea4adbd37d8568f49445ac9',
      1 => 
      array (
        0 => 'app\\actions\\finance\\postledgertransaction',
      ),
      2 => 
      array (
        0 => 'app\\actions\\finance\\__construct',
        1 => 'app\\actions\\finance\\post',
        2 => 'app\\actions\\finance\\prepare',
      ),
      3 => 
      array (
      ),
    ),
    '/var/www/html/app/Actions/Finance/RecordStudentPayment.php' => 
    array (
      0 => '7b09cdb38f1edf4eecced39263980cfdfa6cce192802f53c15ae5c0f652169fc',
      1 => 
      array (
        0 => 'app\\actions\\finance\\recordstudentpayment',
      ),
      2 => 
      array (
        0 => 'app\\actions\\finance\\__construct',
        1 => 'app\\actions\\finance\\record',
      ),
      3 => 
      array (
      ),
    ),
    '/var/www/html/app/Actions/Finance/RelieveStudentFees.php' => 
    array (
      0 => 'da9d4339f9c36a16d1d77b9c881f8a87e83195d19746be1eb4b5cfe96322c3dd',
      1 => 
      array (
        0 => 'app\\actions\\finance\\relievestudentfees',
      ),
      2 => 
      array (
        0 => 'app\\actions\\finance\\__construct',
        1 => 'app\\actions\\finance\\waive',
        2 => 'app\\actions\\finance\\writeoff',
        3 => 'app\\actions\\finance\\relieve',
      ),
      3 => 
      array (
      ),
    ),
    '/var/www/html/app/Actions/Finance/ReverseLedgerTransaction.php' => 
    array (
      0 => '8540105d2844e511d1e91e53d8669e22f317b60b91395999002c608796733573',
      1 => 
      array (
        0 => 'app\\actions\\finance\\reverseledgertransaction',
      ),
      2 => 
      array (
        0 => 'app\\actions\\finance\\__construct',
        1 => 'app\\actions\\finance\\reverse',
      ),
      3 => 
      array (
      ),
    ),
    '/var/www/html/app/Actions/Fortify/PasswordValidationRules.php' => 
    array (
      0 => '1f5bc332a46eb28368a018a593dacb2ffcb1631713b814f346c0dc395a00e054',
      1 => 
      array (
        0 => 'app\\actions\\fortify\\passwordvalidationrules',
      ),
      2 => 
      array (
        0 => 'app\\actions\\fortify\\passwordrules',
      ),
      3 => 
      array (
      ),
    ),
    '/var/www/html/app/Actions/Fortify/ResetUserPassword.php' => 
    array (
      0 => '9a9737f689e1da7f9fdd06110521446d5a733604d79f0056750fb3d04c2eef8b',
      1 => 
      array (
        0 => 'app\\actions\\fortify\\resetuserpassword',
      ),
      2 => 
      array (
        0 => 'app\\actions\\fortify\\reset',
      ),
      3 => 
      array (
      ),
    ),
    '/var/www/html/app/Actions/Fortify/UpdateUserPassword.php' => 
    array (
      0 => 'd6968101c6b05a4cfc071aa93830abfe046246ea1fac891cd10b6ba84bf4ec15',
      1 => 
      array (
        0 => 'app\\actions\\fortify\\updateuserpassword',
      ),
      2 => 
      array (
        0 => 'app\\actions\\fortify\\update',
      ),
      3 => 
      array (
      ),
    ),
    '/var/www/html/app/Actions/Fortify/UpdateUserProfileInformation.php' => 
    array (
      0 => '39864981e3faddb1009a38f1e5c5ce6fdc105cb9bd02b99fbf6fb263006e26b8',
      1 => 
      array (
        0 => 'app\\actions\\fortify\\updateuserprofileinformation',
      ),
      2 => 
      array (
        0 => 'app\\actions\\fortify\\update',
        1 => 'app\\actions\\fortify\\updateverifieduser',
      ),
      3 => 
      array (
      ),
    ),
    '/var/www/html/app/Actions/Gradebook/PublishResult.php' => 
    array (
      0 => 'eee36f4f3a7f6b0b37d23163463026460ce2bbea13aecc5b02b96f9f7ce587f6',
      1 => 
      array (
        0 => 'app\\actions\\gradebook\\publishresult',
      ),
      2 => 
      array (
        0 => 'app\\actions\\gradebook\\__construct',
        1 => 'app\\actions\\gradebook\\publish',
        2 => 'app\\actions\\gradebook\\current',
      ),
      3 => 
      array (
      ),
    ),
    '/var/www/html/app/Actions/Gradebook/RecordGrade.php' => 
    array (
      0 => '2dd067c12cdf20ae7456903d622151f3208b802d4286754c9c16c0d0d032e36e',
      1 => 
      array (
        0 => 'app\\actions\\gradebook\\recordgrade',
      ),
      2 => 
      array (
        0 => 'app\\actions\\gradebook\\record',
        1 => 'app\\actions\\gradebook\\failifrecordsdonotfit',
      ),
      3 => 
      array (
      ),
    ),
    '/var/www/html/app/Actions/Identity/AcceptAccountInvitation.php' => 
    array (
      0 => '533c39292d89069904768849866d3b52511ffd2b500736d8965b68a35c9b115a',
      1 => 
      array (
        0 => 'app\\actions\\identity\\acceptaccountinvitation',
      ),
      2 => 
      array (
        0 => 'app\\actions\\identity\\findpendinginvitation',
        1 => 'app\\actions\\identity\\accept',
      ),
      3 => 
      array (
      ),
    ),
    '/var/www/html/app/Actions/Identity/ChangeAccountStatus.php' => 
    array (
      0 => '97cdeafe32955a42f55b5f3dbb2c7016e75ff719602436035a5de58db3f5211a',
      1 => 
      array (
        0 => 'app\\actions\\identity\\changeaccountstatus',
      ),
      2 => 
      array (
        0 => 'app\\actions\\identity\\__construct',
        1 => 'app\\actions\\identity\\suspend',
        2 => 'app\\actions\\identity\\reinstate',
        3 => 'app\\actions\\identity\\archive',
        4 => 'app\\actions\\identity\\changeto',
      ),
      3 => 
      array (
      ),
    ),
    '/var/www/html/app/Actions/Identity/ProvisionAccount.php' => 
    array (
      0 => '273244a2367f483aa18f8d63b1d097005ccab128329a988d3a3be03879818556',
      1 => 
      array (
        0 => 'app\\actions\\identity\\provisionaccount',
      ),
      2 => 
      array (
        0 => 'app\\actions\\identity\\__construct',
        1 => 'app\\actions\\identity\\provision',
      ),
      3 => 
      array (
      ),
    ),
    '/var/www/html/app/Actions/Identity/RevokeAccountInvitation.php' => 
    array (
      0 => 'fbc2a1090876a3ad468446476bff815536e7a82da4c9f93ed4edfc2d5b2f9cab',
      1 => 
      array (
        0 => 'app\\actions\\identity\\revokeaccountinvitation',
      ),
      2 => 
      array (
        0 => 'app\\actions\\identity\\__construct',
        1 => 'app\\actions\\identity\\revoke',
      ),
      3 => 
      array (
      ),
    ),
    '/var/www/html/app/Actions/Identity/SendAccountInvitation.php' => 
    array (
      0 => '9dc3080b2365b860b3f06293547956b6809edc0cd5b3bd7da545e9d8d9a2f45f',
      1 => 
      array (
        0 => 'app\\actions\\identity\\sendaccountinvitation',
      ),
      2 => 
      array (
        0 => 'app\\actions\\identity\\__construct',
        1 => 'app\\actions\\identity\\send',
      ),
      3 => 
      array (
      ),
    ),
    '/var/www/html/app/Actions/Jetstream/DeleteUser.php' => 
    array (
      0 => '7f5df4b73231beec3e16036c4ba99047b6c91fd580d6291e3950eb482fafdd50',
      1 => 
      array (
        0 => 'app\\actions\\jetstream\\deleteuser',
      ),
      2 => 
      array (
        0 => 'app\\actions\\jetstream\\delete',
      ),
      3 => 
      array (
      ),
    ),
    '/var/www/html/app/Actions/Notice/PublishNotice.php' => 
    array (
      0 => 'd7b6d30e62f6877778fc67a43c23c1436d34669da61713a15260fafc600df09f',
      1 => 
      array (
        0 => 'app\\actions\\notice\\publishnotice',
      ),
      2 => 
      array (
        0 => 'app\\actions\\notice\\__construct',
        1 => 'app\\actions\\notice\\publish',
        2 => 'app\\actions\\notice\\schedule',
        3 => 'app\\actions\\notice\\expire',
      ),
      3 => 
      array (
      ),
    ),
    '/var/www/html/app/Actions/Organization/AssignSchoolToOrganization.php' => 
    array (
      0 => 'c2a92fe1dbfe09468911c566efb6e73e568a24ab9231e536248b0bfcfaa90e2c',
      1 => 
      array (
        0 => 'app\\actions\\organization\\assignschooltoorganization',
      ),
      2 => 
      array (
        0 => 'app\\actions\\organization\\__construct',
        1 => 'app\\actions\\organization\\assign',
      ),
      3 => 
      array (
      ),
    ),
    '/var/www/html/app/Actions/Organization/CreateOrganization.php' => 
    array (
      0 => 'f429036c354f4864241319bb8d6ca31ee6d09bfbffba58da50b16c7023cf820d',
      1 => 
      array (
        0 => 'app\\actions\\organization\\createorganization',
      ),
      2 => 
      array (
        0 => 'app\\actions\\organization\\__construct',
        1 => 'app\\actions\\organization\\create',
      ),
      3 => 
      array (
      ),
    ),
    '/var/www/html/app/Actions/Organization/GrantOrganizationMembership.php' => 
    array (
      0 => '3fa251a9225be4dc645c73e39e2ee4c8c59e71675b4fac5ba03f31113dcd5e81',
      1 => 
      array (
        0 => 'app\\actions\\organization\\grantorganizationmembership',
      ),
      2 => 
      array (
        0 => 'app\\actions\\organization\\__construct',
        1 => 'app\\actions\\organization\\grant',
      ),
      3 => 
      array (
      ),
    ),
    '/var/www/html/app/Actions/Organization/RevokeOrganizationMembership.php' => 
    array (
      0 => '1f529d633882bea4a65403437674157034e666bad5c37e9617604db63ebdb71e',
      1 => 
      array (
        0 => 'app\\actions\\organization\\revokeorganizationmembership',
      ),
      2 => 
      array (
        0 => 'app\\actions\\organization\\__construct',
        1 => 'app\\actions\\organization\\revoke',
      ),
      3 => 
      array (
      ),
    ),
    '/var/www/html/app/Actions/Organization/SetOrganizationMemberPermissions.php' => 
    array (
      0 => 'efaba7fde0e63fb5d10bc76a80643d5095ce151f8f75e228ee83be20f07c2d75',
      1 => 
      array (
        0 => 'app\\actions\\organization\\setorganizationmemberpermissions',
      ),
      2 => 
      array (
        0 => 'app\\actions\\organization\\__construct',
        1 => 'app\\actions\\organization\\set',
        2 => 'app\\actions\\organization\\normalize',
      ),
      3 => 
      array (
      ),
    ),
    '/var/www/html/app/Actions/Organization/UpdateOrganization.php' => 
    array (
      0 => '93e7cb0aca48f06c073f2fa5ad340529115c06d63607cafb4fb8492d86a0bf6a',
      1 => 
      array (
        0 => 'app\\actions\\organization\\updateorganization',
      ),
      2 => 
      array (
        0 => 'app\\actions\\organization\\__construct',
        1 => 'app\\actions\\organization\\update',
      ),
      3 => 
      array (
      ),
    ),
    '/var/www/html/app/Actions/Portal/SubmitPortalRequest.php' => 
    array (
      0 => 'ad75e2e1cc3151be556c8255a92564e886495ed01570cbb2651a1d5bd15b779d',
      1 => 
      array (
        0 => 'app\\actions\\portal\\submitportalrequest',
      ),
      2 => 
      array (
        0 => 'app\\actions\\portal\\__construct',
        1 => 'app\\actions\\portal\\submit',
        2 => 'app\\actions\\portal\\answer',
        3 => 'app\\actions\\portal\\changestatus',
      ),
      3 => 
      array (
      ),
    ),
    '/var/www/html/app/Actions/Report/RequestReport.php' => 
    array (
      0 => '8fba1efdb1343bcb26222bbb55f051ef4b36fbd596b98f10e6eaca388b75b820',
      1 => 
      array (
        0 => 'app\\actions\\report\\requestreport',
      ),
      2 => 
      array (
        0 => 'app\\actions\\report\\__construct',
        1 => 'app\\actions\\report\\request',
      ),
      3 => 
      array (
      ),
    ),
    '/var/www/html/app/Actions/School/EndSchoolMembership.php' => 
    array (
      0 => 'e89818545f2b70d65ec22b43fc49eea74b1c61e7b9e9d8cb3c701bdab3e83cd7',
      1 => 
      array (
        0 => 'app\\actions\\school\\endschoolmembership',
      ),
      2 => 
      array (
        0 => 'app\\actions\\school\\end',
        1 => 'app\\actions\\school\\promoteanotherprimary',
        2 => 'app\\actions\\school\\endorfail',
      ),
      3 => 
      array (
      ),
    ),
    '/var/www/html/app/Actions/School/GrantSchoolMembership.php' => 
    array (
      0 => 'd1649dca42fbcc3ff7f754134fa11e384d18be7f826792ce470e533b9959de7a',
      1 => 
      array (
        0 => 'app\\actions\\school\\grantschoolmembership',
      ),
      2 => 
      array (
        0 => 'app\\actions\\school\\grant',
      ),
      3 => 
      array (
      ),
    ),
    '/var/www/html/app/Actions/Sharing/FulfilDataSharingRequest.php' => 
    array (
      0 => '3a4f3260fc7f9d4abdd97c9b22a3768dfcda33debc84994111e679aced1878b9',
      1 => 
      array (
        0 => 'app\\actions\\sharing\\fulfildatasharingrequest',
      ),
      2 => 
      array (
        0 => 'app\\actions\\sharing\\__construct',
        1 => 'app\\actions\\sharing\\fulfil',
        2 => 'app\\actions\\sharing\\receive',
      ),
      3 => 
      array (
      ),
    ),
    '/var/www/html/app/Actions/Sharing/RequestDataSharing.php' => 
    array (
      0 => 'a5e83ac690d623e604295c69c9e5c8b2df398b8cd1d3c9023b536ef7117d527f',
      1 => 
      array (
        0 => 'app\\actions\\sharing\\requestdatasharing',
      ),
      2 => 
      array (
        0 => 'app\\actions\\sharing\\__construct',
        1 => 'app\\actions\\sharing\\request',
        2 => 'app\\actions\\sharing\\approve',
        3 => 'app\\actions\\sharing\\decline',
        4 => 'app\\actions\\sharing\\revoke',
        5 => 'app\\actions\\sharing\\changestatus',
      ),
      3 => 
      array (
      ),
    ),
    '/var/www/html/app/Actions/Staff/ManageStaffLeave.php' => 
    array (
      0 => '6e1a5419d53b9e5f632b80636d00a55cfb8068c10a268bff253ddd3d28ee45c2',
      1 => 
      array (
        0 => 'app\\actions\\staff\\managestaffleave',
      ),
      2 => 
      array (
        0 => 'app\\actions\\staff\\__construct',
        1 => 'app\\actions\\staff\\request',
        2 => 'app\\actions\\staff\\changestatus',
        3 => 'app\\actions\\staff\\approve',
        4 => 'app\\actions\\staff\\decline',
        5 => 'app\\actions\\staff\\cancel',
      ),
      3 => 
      array (
      ),
    ),
    '/var/www/html/app/Actions/Timetable/PublishTimetable.php' => 
    array (
      0 => '20f98e0c82c512afd04b9e732e804bc93ed23bfce0a528ae9f24931cf6b68c72',
      1 => 
      array (
        0 => 'app\\actions\\timetable\\publishtimetable',
      ),
      2 => 
      array (
        0 => 'app\\actions\\timetable\\__construct',
        1 => 'app\\actions\\timetable\\publish',
        2 => 'app\\actions\\timetable\\archive',
      ),
      3 => 
      array (
      ),
    ),
    '/var/www/html/app/Actions/Timetable/ReviseTimetable.php' => 
    array (
      0 => 'ece0435c7ec9a0ab7d36df44d7bf86236d8bb942595b2c2e982b40df74576668',
      1 => 
      array (
        0 => 'app\\actions\\timetable\\revisetimetable',
      ),
      2 => 
      array (
        0 => 'app\\actions\\timetable\\__construct',
        1 => 'app\\actions\\timetable\\revise',
      ),
      3 => 
      array (
      ),
    ),
    '/var/www/html/app/Actions/Wellbeing/ManageSupportPlan.php' => 
    array (
      0 => 'e7d4d31e480de109a0527dc0ed75a79d09e900e704b27da0b047fddd57bea5f3',
      1 => 
      array (
        0 => 'app\\actions\\wellbeing\\managesupportplan',
      ),
      2 => 
      array (
        0 => 'app\\actions\\wellbeing\\__construct',
        1 => 'app\\actions\\wellbeing\\open',
        2 => 'app\\actions\\wellbeing\\changestatus',
        3 => 'app\\actions\\wellbeing\\addaction',
        4 => 'app\\actions\\wellbeing\\completeaction',
        5 => 'app\\actions\\wellbeing\\addnote',
      ),
      3 => 
      array (
      ),
    ),
    '/var/www/html/app/Actions/Wellbeing/RecordHealthInformation.php' => 
    array (
      0 => '44834fe67fbc6e33e58afbb988075766bc9eb6a4eeec783340a2ee1d7f822080',
      1 => 
      array (
        0 => 'app\\actions\\wellbeing\\recordhealthinformation',
      ),
      2 => 
      array (
        0 => 'app\\actions\\wellbeing\\__construct',
        1 => 'app\\actions\\wellbeing\\record',
      ),
      3 => 
      array (
      ),
    ),
    '/var/www/html/app/Casts/Money.php' => 
    array (
      0 => 'a650f468104d7142bab4d5f40a18d9f8db42ab24febdf403b2634eee79ca8adf',
      1 => 
      array (
        0 => 'app\\casts\\money',
      ),
      2 => 
      array (
        0 => 'app\\casts\\get',
        1 => 'app\\casts\\set',
      ),
      3 => 
      array (
      ),
    ),
    '/var/www/html/app/Console/Commands/AdvanceAcademicCalendar.php' => 
    array (
      0 => '3996b85bafd19aa619a1ff0c55e7e4f2890337d8e2e7b9df29c6c997e5d2ce5f',
      1 => 
      array (
        0 => 'app\\console\\commands\\advanceacademiccalendar',
      ),
      2 => 
      array (
        0 => 'app\\console\\commands\\handle',
        1 => 'app\\console\\commands\\campusesthatautoopen',
        2 => 'app\\console\\commands\\dueperiods',
      ),
      3 => 
      array (
      ),
    ),
    '/var/www/html/app/Console/Commands/CheckBackup.php' => 
    array (
      0 => 'fec1520c740c5ffb4eb6979533eb897339b0349abe1ccdb9cd56662631cd7e0e',
      1 => 
      array (
        0 => 'app\\console\\commands\\checkbackup',
      ),
      2 => 
      array (
        0 => 'app\\console\\commands\\handle',
        1 => 'app\\console\\commands\\reportfailure',
      ),
      3 => 
      array (
      ),
    ),
    '/var/www/html/app/Console/Commands/CreateSuperAdmin.php' => 
    array (
      0 => 'e8f04a204a1d6467ec7142e1c648829f61351e93bcfdc25d9955afb48a603533',
      1 => 
      array (
        0 => 'app\\console\\commands\\createsuperadmin',
      ),
      2 => 
      array (
        0 => 'app\\console\\commands\\handle',
      ),
      3 => 
      array (
      ),
    ),
    '/var/www/html/app/Console/Commands/GenerateUpcomingAcademicCycles.php' => 
    array (
      0 => '0a4802e83d7b53b5fb2e09bae79260c6f82c88e434423f44ce0a41b1ef4f5c10',
      1 => 
      array (
        0 => 'app\\console\\commands\\generateupcomingacademiccycles',
      ),
      2 => 
      array (
        0 => 'app\\console\\commands\\handle',
        1 => 'app\\console\\commands\\nextcyclestart',
      ),
      3 => 
      array (
      ),
    ),
    '/var/www/html/app/Console/Commands/InitCommand.php' => 
    array (
      0 => 'c68f7437dbb47e099f4a1732e5bc317590c1497772cc76fca93ea130300226b4',
      1 => 
      array (
        0 => 'app\\console\\commands\\initcommand',
      ),
      2 => 
      array (
        0 => 'app\\console\\commands\\handle',
        1 => 'app\\console\\commands\\clearcaches',
        2 => 'app\\console\\commands\\generateenv',
        3 => 'app\\console\\commands\\generateappkey',
        4 => 'app\\console\\commands\\buildnodedependencies',
        5 => 'app\\console\\commands\\setappenvironmentdetails',
        6 => 'app\\console\\commands\\setdatabasecredentials',
        7 => 'app\\console\\commands\\setmailcredentials',
        8 => 'app\\console\\commands\\seeddatabase',
        9 => 'app\\console\\commands\\createsuperadmin',
        10 => 'app\\console\\commands\\finishingtouches',
      ),
      3 => 
      array (
      ),
    ),
    '/var/www/html/app/Console/Commands/ProcessNotices.php' => 
    array (
      0 => 'de86aa9ccfa2f2ccd14665d4d742ea94d08a32f31d556a7751bbd889b31056d9',
      1 => 
      array (
        0 => 'app\\console\\commands\\processnotices',
      ),
      2 => 
      array (
        0 => 'app\\console\\commands\\handle',
      ),
      3 => 
      array (
      ),
    ),
    '/var/www/html/app/Console/Commands/PruneExpiredInvitations.php' => 
    array (
      0 => '3b263363aa7b1946e3932d44d6edc02e094b1c9d2b4ab538a977905d5bf5e533',
      1 => 
      array (
        0 => 'app\\console\\commands\\pruneexpiredinvitations',
      ),
      2 => 
      array (
        0 => 'app\\console\\commands\\handle',
      ),
      3 => 
      array (
      ),
    ),
    '/var/www/html/app/Console/Commands/SendAcademicCalendarReminders.php' => 
    array (
      0 => '2202265cd559669e93031a021fbb69dc5efa77f2806b83c981d05c29d4dfd589',
      1 => 
      array (
        0 => 'app\\console\\commands\\sendacademiccalendarreminders',
      ),
      2 => 
      array (
        0 => 'app\\console\\commands\\handle',
        1 => 'app\\console\\commands\\duereminders',
        2 => 'app\\console\\commands\\periodsstartingon',
        3 => 'app\\console\\commands\\periodsendingon',
        4 => 'app\\console\\commands\\overdueperiods',
        5 => 'app\\console\\commands\\claim',
      ),
      3 => 
      array (
      ),
    ),
    '/var/www/html/app/Console/Commands/UpdateApplicationCommand.php' => 
    array (
      0 => 'b705201519e7ad290ab276052edf7a6a54da90857dd24a30d5020e74dafa5150',
      1 => 
      array (
        0 => 'app\\console\\commands\\updateapplicationcommand',
      ),
      2 => 
      array (
        0 => 'app\\console\\commands\\handle',
        1 => 'app\\console\\commands\\intro',
        2 => 'app\\console\\commands\\fetchlatestcode',
        3 => 'app\\console\\commands\\buildnodedependencies',
        4 => 'app\\console\\commands\\runupdatecommands',
        5 => 'app\\console\\commands\\optimize',
        6 => 'app\\console\\commands\\splitversionnumber',
      ),
      3 => 
      array (
      ),
    ),
    '/var/www/html/app/Contracts/Importer.php' => 
    array (
      0 => '1b37dc8ac4a87e6890812455977c67c2c0c9fa45c0560ce50e6385c842efa006',
      1 => 
      array (
        0 => 'app\\contracts\\importer',
      ),
      2 => 
      array (
        0 => 'app\\contracts\\key',
        1 => 'app\\contracts\\title',
        2 => 'app\\contracts\\requiredcolumns',
        3 => 'app\\contracts\\optionalcolumns',
        4 => 'app\\contracts\\rules',
        5 => 'app\\contracts\\apply',
      ),
      3 => 
      array (
      ),
    ),
    '/var/www/html/app/Contracts/Report.php' => 
    array (
      0 => '402d16e43517761c41d2c5276255a6650062d7ba23c8042db1dfbd3f204496be',
      1 => 
      array (
        0 => 'app\\contracts\\report',
      ),
      2 => 
      array (
        0 => 'app\\contracts\\key',
        1 => 'app\\contracts\\title',
        2 => 'app\\contracts\\columns',
        3 => 'app\\contracts\\rows',
      ),
      3 => 
      array (
      ),
    ),
    '/var/www/html/app/Enums/AcademicPeriodStatus.php' => 
    array (
      0 => 'bf36f8d3eec5671fc2091cc1b5338460ec33e94bff0e70668238622fd651d84d',
      1 => 
      array (
        0 => 'app\\enums\\academicperiodstatus',
      ),
      2 => 
      array (
        0 => 'app\\enums\\label',
        1 => 'app\\enums\\acceptswrites',
        2 => 'app\\enums\\acceptsnewwork',
        3 => 'app\\enums\\isoperational',
        4 => 'app\\enums\\isfrozen',
        5 => 'app\\enums\\allowednext',
        6 => 'app\\enums\\canmoveto',
        7 => 'app\\enums\\requiresreasontoreach',
        8 => 'app\\enums\\values',
      ),
      3 => 
      array (
      ),
    ),
    '/var/www/html/app/Enums/AcademicPeriodType.php' => 
    array (
      0 => 'abd30701db4e2dce97a92933045c3d3880c491721aac6af10d2796b86a2cd828',
      1 => 
      array (
        0 => 'app\\enums\\academicperiodtype',
      ),
      2 => 
      array (
        0 => 'app\\enums\\label',
        1 => 'app\\enums\\isteaching',
        2 => 'app\\enums\\isprimarydivision',
        3 => 'app\\enums\\subperiodtypes',
        4 => 'app\\enums\\values',
      ),
      3 => 
      array (
      ),
    ),
    '/var/www/html/app/Enums/AcademicStructureStatus.php' => 
    array (
      0 => '02972a6df05e2f410975859a3f832755ea47804aa9ef268f8e55e0d9967732b2',
      1 => 
      array (
        0 => 'app\\enums\\academicstructurestatus',
      ),
      2 => 
      array (
        0 => 'app\\enums\\label',
        1 => 'app\\enums\\canmoveto',
      ),
      3 => 
      array (
      ),
    ),
    '/var/www/html/app/Enums/AccountInvitationStatus.php' => 
    array (
      0 => 'ed2bd856498a53998e9b4d1986ac0633050b47335e722cba87f85f42d40a894e',
      1 => 
      array (
        0 => 'app\\enums\\accountinvitationstatus',
      ),
      2 => 
      array (
        0 => 'app\\enums\\label',
        1 => 'app\\enums\\description',
        2 => 'app\\enums\\badgevariant',
        3 => 'app\\enums\\icon',
        4 => 'app\\enums\\tabs',
      ),
      3 => 
      array (
      ),
    ),
    '/var/www/html/app/Enums/AccountStatus.php' => 
    array (
      0 => '73e16175a3a1c27f151846831f24d48ee7d718ca208474248216619274a326ad',
      1 => 
      array (
        0 => 'app\\enums\\accountstatus',
      ),
      2 => 
      array (
        0 => 'app\\enums\\label',
        1 => 'app\\enums\\canaccessapplication',
        2 => 'app\\enums\\accessdeniedmessage',
      ),
      3 => 
      array (
      ),
    ),
    '/var/www/html/app/Enums/AttendanceKind.php' => 
    array (
      0 => '6baefe020d9a6cc00efd20807d9c6b10c8e63f8de9106b56524903e23dfcae4b',
      1 => 
      array (
        0 => 'app\\enums\\attendancekind',
      ),
      2 => 
      array (
        0 => 'app\\enums\\label',
        1 => 'app\\enums\\values',
      ),
      3 => 
      array (
      ),
    ),
    '/var/www/html/app/Enums/AttendanceStatus.php' => 
    array (
      0 => 'e76cbd3311a4480644ee0b8b537e03ecd6ad34be99fec55c902608242a85a304',
      1 => 
      array (
        0 => 'app\\enums\\attendancestatus',
      ),
      2 => 
      array (
        0 => 'app\\enums\\label',
        1 => 'app\\enums\\countsaspresent',
        2 => 'app\\enums\\countsinrate',
        3 => 'app\\enums\\values',
      ),
      3 => 
      array (
      ),
    ),
    '/var/www/html/app/Enums/AuditAction.php' => 
    array (
      0 => '595073ab11718bebd6423b48dc7b3887a388be36179bbffef12e7ae62a99657e',
      1 => 
      array (
        0 => 'app\\enums\\auditaction',
      ),
      2 => 
      array (
        0 => 'app\\enums\\label',
      ),
      3 => 
      array (
      ),
    ),
    '/var/www/html/app/Enums/CalendarEventType.php' => 
    array (
      0 => '25b8921e3923fe5c6ebbaed7416b06d267fa3c4095a91832648fdd981886e1fa',
      1 => 
      array (
        0 => 'app\\enums\\calendareventtype',
      ),
      2 => 
      array (
        0 => 'app\\enums\\label',
        1 => 'app\\enums\\isteachingday',
        2 => 'app\\enums\\values',
      ),
      3 => 
      array (
      ),
    ),
    '/var/www/html/app/Enums/CohortType.php' => 
    array (
      0 => 'fdfc98ce2f6a49e2f14d453d36e77c88fd5ff4204566041497dbfcd0ca3820ca',
      1 => 
      array (
        0 => 'app\\enums\\cohorttype',
      ),
      2 => 
      array (
        0 => 'app\\enums\\label',
        1 => 'app\\enums\\isrestricted',
        2 => 'app\\enums\\values',
      ),
      3 => 
      array (
      ),
    ),
    '/var/www/html/app/Enums/CourseOfferingStatus.php' => 
    array (
      0 => 'c50431a6b0ab1970989a5a50b0487ac0b05e4e0c1c0fcfd34e36bb35a9ebdf30',
      1 => 
      array (
        0 => 'app\\enums\\courseofferingstatus',
      ),
      2 => 
      array (
        0 => 'app\\enums\\label',
        1 => 'app\\enums\\canmoveto',
      ),
      3 => 
      array (
      ),
    ),
    '/var/www/html/app/Enums/DataCategory.php' => 
    array (
      0 => '2e85f18b27f7a02e634fb54d6be09599ecac98ae77c9c25bd9021f11644fc64c',
      1 => 
      array (
        0 => 'app\\enums\\datacategory',
      ),
      2 => 
      array (
        0 => 'app\\enums\\label',
        1 => 'app\\enums\\isrestricted',
        2 => 'app\\enums\\ordinary',
        3 => 'app\\enums\\values',
      ),
      3 => 
      array (
      ),
    ),
    '/var/www/html/app/Enums/DataSharingStatus.php' => 
    array (
      0 => 'e201a46f400662663edb33426be5d2665d9eee9ec9f9ffe9e690c631db6e71c7',
      1 => 
      array (
        0 => 'app\\enums\\datasharingstatus',
      ),
      2 => 
      array (
        0 => 'app\\enums\\label',
        1 => 'app\\enums\\allowsfulfilment',
        2 => 'app\\enums\\allowednext',
        3 => 'app\\enums\\canmoveto',
        4 => 'app\\enums\\values',
      ),
      3 => 
      array (
      ),
    ),
    '/var/www/html/app/Enums/EmploymentType.php' => 
    array (
      0 => 'b9921dba728ab3cfa059fd2dc05a6df69b2122f1e9ff859d4b673abe79d13101',
      1 => 
      array (
        0 => 'app\\enums\\employmenttype',
      ),
      2 => 
      array (
        0 => 'app\\enums\\label',
        1 => 'app\\enums\\values',
      ),
      3 => 
      array (
      ),
    ),
    '/var/www/html/app/Enums/EnrollmentStatus.php' => 
    array (
      0 => 'be884189f3256fb5840aba8e2652063a2a9c468dcd54992775009dde9ba7c3f8',
      1 => 
      array (
        0 => 'app\\enums\\enrollmentstatus',
      ),
      2 => 
      array (
        0 => 'app\\enums\\label',
        1 => 'app\\enums\\isattending',
        2 => 'app\\enums\\isclosed',
        3 => 'app\\enums\\allowednext',
        4 => 'app\\enums\\canmoveto',
      ),
      3 => 
      array (
      ),
    ),
    '/var/www/html/app/Enums/Feature.php' => 
    array (
      0 => '8f01a5f89c74027cebedeedb7b0f3ba5e6bc362b5841c031a9613be2a1cd6715',
      1 => 
      array (
        0 => 'app\\enums\\feature',
      ),
      2 => 
      array (
        0 => 'app\\enums\\label',
        1 => 'app\\enums\\defaultstoon',
        2 => 'app\\enums\\values',
      ),
      3 => 
      array (
      ),
    ),
    '/var/www/html/app/Enums/GradeAggregation.php' => 
    array (
      0 => '758972d49d5e57310787face2c1a060407f7e64e8d6b3aee4a49d390bcfa9c5f',
      1 => 
      array (
        0 => 'app\\enums\\gradeaggregation',
      ),
      2 => 
      array (
        0 => 'app\\enums\\label',
        1 => 'app\\enums\\values',
      ),
      3 => 
      array (
      ),
    ),
    '/var/www/html/app/Enums/GradeEntryState.php' => 
    array (
      0 => '858b969c90ce97797eccd00c86793b2884e20640301067b81c31664ea4af8036',
      1 => 
      array (
        0 => 'app\\enums\\gradeentrystate',
      ),
      2 => 
      array (
        0 => 'app\\enums\\label',
        1 => 'app\\enums\\countsintotal',
        2 => 'app\\enums\\needspoints',
        3 => 'app\\enums\\values',
      ),
      3 => 
      array (
      ),
    ),
    '/var/www/html/app/Enums/GradeItemType.php' => 
    array (
      0 => '4c6fbb305b7330b46d064f8d24d006f3d1e87a4a44633616a86078b03cc8f5a6',
      1 => 
      array (
        0 => 'app\\enums\\gradeitemtype',
      ),
      2 => 
      array (
        0 => 'app\\enums\\label',
        1 => 'app\\enums\\carriespoints',
        2 => 'app\\enums\\values',
      ),
      3 => 
      array (
      ),
    ),
    '/var/www/html/app/Enums/ImportRowState.php' => 
    array (
      0 => 'f2687792ab7a97a17b085838b305f65c1118e32df58761b697bb9dcaa7ba772b',
      1 => 
      array (
        0 => 'app\\enums\\importrowstate',
      ),
      2 => 
      array (
        0 => 'app\\enums\\label',
        1 => 'app\\enums\\values',
      ),
      3 => 
      array (
      ),
    ),
    '/var/www/html/app/Enums/ImportStatus.php' => 
    array (
      0 => 'c5510864af11a5d123b18c5e24063262683a7229e170ca357cbd8bdf679f0733',
      1 => 
      array (
        0 => 'app\\enums\\importstatus',
      ),
      2 => 
      array (
        0 => 'app\\enums\\label',
        1 => 'app\\enums\\canbeapplied',
        2 => 'app\\enums\\values',
      ),
      3 => 
      array (
      ),
    ),
    '/var/www/html/app/Enums/IncidentCategory.php' => 
    array (
      0 => 'd3d640b215bd49945c1129bf0b94c6c76cb043f6863cbd86a6d74eafe9cce02a',
      1 => 
      array (
        0 => 'app\\enums\\incidentcategory',
      ),
      2 => 
      array (
        0 => 'app\\enums\\label',
        1 => 'app\\enums\\isrestricted',
        2 => 'app\\enums\\readpermission',
        3 => 'app\\enums\\values',
      ),
      3 => 
      array (
      ),
    ),
    '/var/www/html/app/Enums/IncidentParticipantRole.php' => 
    array (
      0 => '70ad3f88ac84f6c348ac5e8acb2288d4245850a380d4a6fdbb642f572a1b2d92',
      1 => 
      array (
        0 => 'app\\enums\\incidentparticipantrole',
      ),
      2 => 
      array (
        0 => 'app\\enums\\label',
        1 => 'app\\enums\\values',
      ),
      3 => 
      array (
      ),
    ),
    '/var/www/html/app/Enums/IncidentStatus.php' => 
    array (
      0 => 'aa2162bac0fc19ea27e4116cd8a5b294e2e37c111e9cbad13c4794965215b3bf',
      1 => 
      array (
        0 => 'app\\enums\\incidentstatus',
      ),
      2 => 
      array (
        0 => 'app\\enums\\label',
        1 => 'app\\enums\\isopen',
        2 => 'app\\enums\\allowednext',
        3 => 'app\\enums\\canmoveto',
        4 => 'app\\enums\\values',
      ),
      3 => 
      array (
      ),
    ),
    '/var/www/html/app/Enums/InstructionalModel.php' => 
    array (
      0 => 'c9437b76a3bffc801e738c9b4fb0e8b8a2b2ffaf601b144316b3ab4a43a07c62',
      1 => 
      array (
        0 => 'app\\enums\\instructionalmodel',
      ),
      2 => 
      array (
        0 => 'app\\enums\\default',
        1 => 'app\\enums\\label',
        2 => 'app\\enums\\setupanswer',
        3 => 'app\\enums\\description',
        4 => 'app\\enums\\example',
        5 => 'app\\enums\\defaultrostermode',
        6 => 'app\\enums\\allowscombinedsections',
        7 => 'app\\enums\\allowsindividualrosters',
        8 => 'app\\enums\\allowsrostermode',
        9 => 'app\\enums\\rostermodes',
        10 => 'app\\enums\\keepslearnerstogether',
        11 => 'app\\enums\\values',
      ),
      3 => 
      array (
      ),
    ),
    '/var/www/html/app/Enums/LeaveStatus.php' => 
    array (
      0 => '88e95061c8bb0b006298191fe6eda8cd20da9b78b083cc61a5c775d6368fe3fa',
      1 => 
      array (
        0 => 'app\\enums\\leavestatus',
      ),
      2 => 
      array (
        0 => 'app\\enums\\label',
        1 => 'app\\enums\\holdsthedays',
        2 => 'app\\enums\\allowednext',
        3 => 'app\\enums\\canmoveto',
        4 => 'app\\enums\\values',
      ),
      3 => 
      array (
      ),
    ),
    '/var/www/html/app/Enums/LeaveType.php' => 
    array (
      0 => 'fca9b3650bcef07d35ac309703d4ffc8157c65f85934ec47985e8fdf2ebf6937',
      1 => 
      array (
        0 => 'app\\enums\\leavetype',
      ),
      2 => 
      array (
        0 => 'app\\enums\\label',
        1 => 'app\\enums\\needsnotice',
        2 => 'app\\enums\\values',
      ),
      3 => 
      array (
      ),
    ),
    '/var/www/html/app/Enums/LedgerAccountType.php' => 
    array (
      0 => 'c6140157fff132c2be575f09889aeb36f8026d27c943055f0b8d46b8385d431f',
      1 => 
      array (
        0 => 'app\\enums\\ledgeraccounttype',
      ),
      2 => 
      array (
        0 => 'app\\enums\\label',
        1 => 'app\\enums\\normalbalance',
        2 => 'app\\enums\\values',
      ),
      3 => 
      array (
      ),
    ),
    '/var/www/html/app/Enums/NoticeRecipientState.php' => 
    array (
      0 => 'a0c7cf6c171a8a655112cec37eb8657542af7148436fe9630e3c0c3bb7c7c580',
      1 => 
      array (
        0 => 'app\\enums\\noticerecipientstate',
      ),
      2 => 
      array (
        0 => 'app\\enums\\label',
      ),
      3 => 
      array (
      ),
    ),
    '/var/www/html/app/Enums/NoticeStatus.php' => 
    array (
      0 => 'aff912d4483200eed255013bd0eb3b5f1a2a0f4dd19100ab60e5624d8db652b3',
      1 => 
      array (
        0 => 'app\\enums\\noticestatus',
      ),
      2 => 
      array (
        0 => 'app\\enums\\label',
        1 => 'app\\enums\\isvisible',
        2 => 'app\\enums\\allowednext',
        3 => 'app\\enums\\canmoveto',
        4 => 'app\\enums\\values',
      ),
      3 => 
      array (
      ),
    ),
    '/var/www/html/app/Enums/OrganizationMembershipRole.php' => 
    array (
      0 => '3ef9fc058c1731c030422a50ec0482f8ca7a0fd901f11e685291a1cf4889af60',
      1 => 
      array (
        0 => 'app\\enums\\organizationmembershiprole',
      ),
      2 => 
      array (
      ),
      3 => 
      array (
      ),
    ),
    '/var/www/html/app/Enums/OrganizationMembershipStatus.php' => 
    array (
      0 => '67dc7a5ff7961744d29a6f369f9e869dfbaa39d47b9da4b1dace7409f3ac29ed',
      1 => 
      array (
        0 => 'app\\enums\\organizationmembershipstatus',
      ),
      2 => 
      array (
        0 => 'app\\enums\\grantsaccess',
      ),
      3 => 
      array (
      ),
    ),
    '/var/www/html/app/Enums/OrganizationPermission.php' => 
    array (
      0 => 'a568d484079a6d68d589517963bca7deccf6b5e12b86b471cf6dcaeb12c65b45',
      1 => 
      array (
        0 => 'app\\enums\\organizationpermission',
      ),
      2 => 
      array (
        0 => 'app\\enums\\all',
        1 => 'app\\enums\\delegable',
        2 => 'app\\enums\\label',
        3 => 'app\\enums\\description',
      ),
      3 => 
      array (
      ),
    ),
    '/var/www/html/app/Enums/ParticipationStatus.php' => 
    array (
      0 => '5ebd462284871f530bb7d9b01dec09fd61a90abadc84b390a2d06e862265d520',
      1 => 
      array (
        0 => 'app\\enums\\participationstatus',
      ),
      2 => 
      array (
        0 => 'app\\enums\\label',
        1 => 'app\\enums\\isrunning',
        2 => 'app\\enums\\allowednext',
        3 => 'app\\enums\\canmoveto',
        4 => 'app\\enums\\values',
      ),
      3 => 
      array (
      ),
    ),
    '/var/www/html/app/Enums/PlatformPermission.php' => 
    array (
      0 => '5eb80da8de9f74b8117655265e0442a32bb3db4c622ed6a3bd77524dcf8fc383',
      1 => 
      array (
        0 => 'app\\enums\\platformpermission',
      ),
      2 => 
      array (
      ),
      3 => 
      array (
      ),
    ),
    '/var/www/html/app/Enums/PortalArea.php' => 
    array (
      0 => 'f4852ab5586ca675eba33ab9acf98258783731b04c6b54721c225bed52450122',
      1 => 
      array (
        0 => 'app\\enums\\portalarea',
      ),
      2 => 
      array (
        0 => 'app\\enums\\label',
        1 => 'app\\enums\\values',
      ),
      3 => 
      array (
      ),
    ),
    '/var/www/html/app/Enums/PortalRequestStatus.php' => 
    array (
      0 => 'ed666806027a762fc598b004f3831c975cedc41b0a7b7895a72e0618a794956b',
      1 => 
      array (
        0 => 'app\\enums\\portalrequeststatus',
      ),
      2 => 
      array (
        0 => 'app\\enums\\label',
        1 => 'app\\enums\\isopen',
        2 => 'app\\enums\\allowednext',
        3 => 'app\\enums\\canmoveto',
        4 => 'app\\enums\\values',
      ),
      3 => 
      array (
      ),
    ),
    '/var/www/html/app/Enums/PortalRequestType.php' => 
    array (
      0 => 'd58b56e919186b1e0b2980b73202e1e0995f2768b061de613bead864073e3d96',
      1 => 
      array (
        0 => 'app\\enums\\portalrequesttype',
      ),
      2 => 
      array (
        0 => 'app\\enums\\label',
        1 => 'app\\enums\\values',
      ),
      3 => 
      array (
      ),
    ),
    '/var/www/html/app/Enums/ProgramType.php' => 
    array (
      0 => 'c3bf582ba5f1b4012530557f758d1cbacee2fe971b8efe29cdba28467c29058a',
      1 => 
      array (
        0 => 'app\\enums\\programtype',
      ),
      2 => 
      array (
        0 => 'app\\enums\\label',
        1 => 'app\\enums\\values',
      ),
      3 => 
      array (
      ),
    ),
    '/var/www/html/app/Enums/ReportStatus.php' => 
    array (
      0 => 'fecd68f9685240da0ba581ced85e78b0650ec8f11985bd3cc43f135b95d126cd',
      1 => 
      array (
        0 => 'app\\enums\\reportstatus',
      ),
      2 => 
      array (
        0 => 'app\\enums\\label',
      ),
      3 => 
      array (
      ),
    ),
    '/var/www/html/app/Enums/Role.php' => 
    array (
      0 => '0865a31eb6c530a55ee00f4d91ab11f8084c41d2750a78c38ed7b2236aefb4c3',
      1 => 
      array (
        0 => 'app\\enums\\role',
      ),
      2 => 
      array (
        0 => 'app\\enums\\issystemscoped',
        1 => 'app\\enums\\label',
      ),
      3 => 
      array (
      ),
    ),
    '/var/www/html/app/Enums/RosterMode.php' => 
    array (
      0 => '6724d165e6ee88a1a58424ecb0aaf908f803740fb2383ae8de6781ebbcc69045',
      1 => 
      array (
        0 => 'app\\enums\\rostermode',
      ),
      2 => 
      array (
        0 => 'app\\enums\\label',
        1 => 'app\\enums\\description',
        2 => 'app\\enums\\useshomesections',
        3 => 'app\\enums\\values',
      ),
      3 => 
      array (
      ),
    ),
    '/var/www/html/app/Enums/SchoolMembershipStatus.php' => 
    array (
      0 => '712043f10f480e4f05efe187ad48714ad69887faa50358b5fe5a4d955e5634ae',
      1 => 
      array (
        0 => 'app\\enums\\schoolmembershipstatus',
      ),
      2 => 
      array (
        0 => 'app\\enums\\label',
        1 => 'app\\enums\\grantsaccess',
      ),
      3 => 
      array (
      ),
    ),
    '/var/www/html/app/Enums/StaffStatus.php' => 
    array (
      0 => '8289176680ca1fb910b1351b412de8006a95777190ee9a0146b648cabe99e347',
      1 => 
      array (
        0 => 'app\\enums\\staffstatus',
      ),
      2 => 
      array (
        0 => 'app\\enums\\label',
        1 => 'app\\enums\\canbegivenwork',
        2 => 'app\\enums\\values',
      ),
      3 => 
      array (
      ),
    ),
    '/var/www/html/app/Enums/SupportCategory.php' => 
    array (
      0 => '45e23ee65999adf9ad4d3cee0884f76ac2778600506358272d9bad5e7666289f',
      1 => 
      array (
        0 => 'app\\enums\\supportcategory',
      ),
      2 => 
      array (
        0 => 'app\\enums\\label',
        1 => 'app\\enums\\isconfidential',
        2 => 'app\\enums\\readpermission',
        3 => 'app\\enums\\values',
      ),
      3 => 
      array (
      ),
    ),
    '/var/www/html/app/Enums/SupportPlanStatus.php' => 
    array (
      0 => '14db23bf8518bb0d32248e63f81f5d5e85514bef7151fcbe81b368d920fbeddc',
      1 => 
      array (
        0 => 'app\\enums\\supportplanstatus',
      ),
      2 => 
      array (
        0 => 'app\\enums\\label',
        1 => 'app\\enums\\isopen',
        2 => 'app\\enums\\allowednext',
        3 => 'app\\enums\\canmoveto',
        4 => 'app\\enums\\values',
      ),
      3 => 
      array (
      ),
    ),
    '/var/www/html/app/Enums/TeachingRole.php' => 
    array (
      0 => 'c9c30db3eece4601fa20e33dab52b8b219cd7898d3efdaf2ec45f46cc41aaa09',
      1 => 
      array (
        0 => 'app\\enums\\teachingrole',
      ),
      2 => 
      array (
        0 => 'app\\enums\\label',
        1 => 'app\\enums\\values',
      ),
      3 => 
      array (
      ),
    ),
    '/var/www/html/app/Enums/TimetableStatus.php' => 
    array (
      0 => '74072e6eecd0532559fe34c0188b432e67a9826aa86490c39ee38a6a27c6e9a1',
      1 => 
      array (
        0 => 'app\\enums\\timetablestatus',
      ),
      2 => 
      array (
        0 => 'app\\enums\\label',
        1 => 'app\\enums\\acceptschanges',
        2 => 'app\\enums\\allowednext',
        3 => 'app\\enums\\canmoveto',
      ),
      3 => 
      array (
      ),
    ),
    '/var/www/html/app/Events/AccountStatusChanged.php' => 
    array (
      0 => '7bfa637dd082faa7829443415ec57b82f9cd9fa2f4a959f21c053f7f2dd95059',
      1 => 
      array (
        0 => 'app\\events\\accountstatuschanged',
      ),
      2 => 
      array (
        0 => 'app\\events\\__construct',
      ),
      3 => 
      array (
      ),
    ),
    '/var/www/html/app/Exceptions/ApplicationException.php' => 
    array (
      0 => '9f867557691132dc45de54c5cd372e6a8c759de985ff5756d46264b89bf6f393',
      1 => 
      array (
        0 => 'app\\exceptions\\applicationexception',
      ),
      2 => 
      array (
        0 => 'app\\exceptions\\render',
      ),
      3 => 
      array (
      ),
    ),
    '/var/www/html/app/Exceptions/ClosedPeriodException.php' => 
    array (
      0 => 'aff0a5f2253251610aac86797492aa45452af0152dcf75cd6f6d7eef262e6197',
      1 => 
      array (
        0 => 'app\\exceptions\\closedperiodexception',
      ),
      2 => 
      array (
      ),
      3 => 
      array (
      ),
    ),
    '/var/www/html/app/Exceptions/DuplicateRangeException.php' => 
    array (
      0 => 'aff3020534981e3899934fbf449905652747b7f9f77536ced6396bb783e2f422',
      1 => 
      array (
        0 => 'app\\exceptions\\duplicaterangeexception',
      ),
      2 => 
      array (
      ),
      3 => 
      array (
      ),
    ),
    '/var/www/html/app/Exceptions/EmptyRecordsException.php' => 
    array (
      0 => '983bfa3de155c1a5290cfc80d7308a3fb950fc6eeaa9778700af8f3723892779',
      1 => 
      array (
        0 => 'app\\exceptions\\emptyrecordsexception',
      ),
      2 => 
      array (
      ),
      3 => 
      array (
      ),
    ),
    '/var/www/html/app/Exceptions/InvalidClassException.php' => 
    array (
      0 => '1579a77ac4c5703b887186403c75129f3225b61c8b16f8427357ae378f10ed30',
      1 => 
      array (
        0 => 'app\\exceptions\\invalidclassexception',
      ),
      2 => 
      array (
      ),
      3 => 
      array (
      ),
    ),
    '/var/www/html/app/Exceptions/InvalidUserException.php' => 
    array (
      0 => '375961580e85ef99e6aeeff41cafa0dd9b1a52af835bedd2707eb97aa75e0c62',
      1 => 
      array (
        0 => 'app\\exceptions\\invaliduserexception',
      ),
      2 => 
      array (
      ),
      3 => 
      array (
      ),
    ),
    '/var/www/html/app/Exceptions/InvalidValueException.php' => 
    array (
      0 => '75220293653bff529a05a21802c4c9bdab3894f915fffaacada5fab759befe6b',
      1 => 
      array (
        0 => 'app\\exceptions\\invalidvalueexception',
      ),
      2 => 
      array (
      ),
      3 => 
      array (
      ),
    ),
    '/var/www/html/app/Exceptions/ResourceNotEmptyException.php' => 
    array (
      0 => '2dcd2d152e1b8420b8f2e87e59e5d4e883854577b516dda5fa76513add02a1ed',
      1 => 
      array (
        0 => 'app\\exceptions\\resourcenotemptyexception',
      ),
      2 => 
      array (
      ),
      3 => 
      array (
      ),
    ),
    '/var/www/html/app/Exceptions/TimetableConflictException.php' => 
    array (
      0 => '757c36d2cedceb579941a2f89f8562328defe88090205dd6f3eaaead2ef50ebc',
      1 => 
      array (
        0 => 'app\\exceptions\\timetableconflictexception',
      ),
      2 => 
      array (
        0 => 'app\\exceptions\\__construct',
        1 => 'app\\exceptions\\conflicts',
      ),
      3 => 
      array (
      ),
    ),
    '/var/www/html/app/Http/Controllers/AcademicCycleController.php' => 
    array (
      0 => '9de1bb873cfd3b133da311c15ab69518e9f60849e5e916db1de6aeb0298c0e36',
      1 => 
      array (
        0 => 'app\\http\\controllers\\academiccyclecontroller',
      ),
      2 => 
      array (
        0 => 'app\\http\\controllers\\__construct',
        1 => 'app\\http\\controllers\\store',
      ),
      3 => 
      array (
      ),
    ),
    '/var/www/html/app/Http/Controllers/AcademicCycleSectionController.php' => 
    array (
      0 => 'b6008bcc5ddda69246df89ae7051ce509de5c1e88722626c624caa6d6ceea927',
      1 => 
      array (
        0 => 'app\\http\\controllers\\academiccyclesectioncontroller',
      ),
      2 => 
      array (
        0 => 'app\\http\\controllers\\__construct',
        1 => 'app\\http\\controllers\\index',
        2 => 'app\\http\\controllers\\create',
        3 => 'app\\http\\controllers\\store',
        4 => 'app\\http\\controllers\\changestatus',
        5 => 'app\\http\\controllers\\rollforward',
      ),
      3 => 
      array (
      ),
    ),
    '/var/www/html/app/Http/Controllers/AcademicLevelController.php' => 
    array (
      0 => '7a552b4e84240421fcc8a09f966b3328300aa61fd39da1b23381102405cb70ed',
      1 => 
      array (
        0 => 'app\\http\\controllers\\academiclevelcontroller',
      ),
      2 => 
      array (
        0 => 'app\\http\\controllers\\__construct',
        1 => 'app\\http\\controllers\\index',
        2 => 'app\\http\\controllers\\create',
        3 => 'app\\http\\controllers\\store',
      ),
      3 => 
      array (
      ),
    ),
    '/var/www/html/app/Http/Controllers/AcademicPeriodController.php' => 
    array (
      0 => 'ad3b77862c4c0a5e34bedf2991ab313a37fa1cd765864fac2dd38263b0cece0e',
      1 => 
      array (
        0 => 'app\\http\\controllers\\academicperiodcontroller',
      ),
      2 => 
      array (
        0 => 'app\\http\\controllers\\__construct',
        1 => 'app\\http\\controllers\\index',
        2 => 'app\\http\\controllers\\create',
        3 => 'app\\http\\controllers\\store',
        4 => 'app\\http\\controllers\\show',
        5 => 'app\\http\\controllers\\edit',
        6 => 'app\\http\\controllers\\update',
        7 => 'app\\http\\controllers\\destroy',
        8 => 'app\\http\\controllers\\close',
        9 => 'app\\http\\controllers\\reopen',
        10 => 'app\\http\\controllers\\beginclosing',
        11 => 'app\\http\\controllers\\setacademicperiod',
      ),
      3 => 
      array (
      ),
    ),
    '/var/www/html/app/Http/Controllers/AcademicYearController.php' => 
    array (
      0 => '94e0d4ab7276239d3fbdcc6776bb5fd56d4786b51c63a2d19e041de80d533db1',
      1 => 
      array (
        0 => 'app\\http\\controllers\\academicyearcontroller',
      ),
      2 => 
      array (
        0 => 'app\\http\\controllers\\__construct',
        1 => 'app\\http\\controllers\\index',
        2 => 'app\\http\\controllers\\create',
        3 => 'app\\http\\controllers\\store',
        4 => 'app\\http\\controllers\\show',
        5 => 'app\\http\\controllers\\edit',
        6 => 'app\\http\\controllers\\update',
        7 => 'app\\http\\controllers\\destroy',
        8 => 'app\\http\\controllers\\close',
        9 => 'app\\http\\controllers\\reopen',
        10 => 'app\\http\\controllers\\beginclosing',
        11 => 'app\\http\\controllers\\setacademicyear',
      ),
      3 => 
      array (
      ),
    ),
    '/var/www/html/app/Http/Controllers/AccountInvitationController.php' => 
    array (
      0 => 'b537d8f8452a6b1c88b09ad3bee3ce747fbff80b53d0cef3f7ff91365ea5eeea',
      1 => 
      array (
        0 => 'app\\http\\controllers\\accountinvitationcontroller',
      ),
      2 => 
      array (
        0 => 'app\\http\\controllers\\index',
        1 => 'app\\http\\controllers\\show',
        2 => 'app\\http\\controllers\\accept',
        3 => 'app\\http\\controllers\\send',
        4 => 'app\\http\\controllers\\revoke',
      ),
      3 => 
      array (
      ),
    ),
    '/var/www/html/app/Http/Controllers/AccountStatusController.php' => 
    array (
      0 => '2b4dc6e7a2eb6bcf50ffbba972c90a5778c4facea50372771be32fd10e20b69d',
      1 => 
      array (
        0 => 'app\\http\\controllers\\accountstatuscontroller',
      ),
      2 => 
      array (
        0 => 'app\\http\\controllers\\update',
      ),
      3 => 
      array (
      ),
    ),
    '/var/www/html/app/Http/Controllers/AdminController.php' => 
    array (
      0 => 'd91804ed43e5e9b9f0738a43bd9c453e68ecc431b48461c8797f2687561b3879',
      1 => 
      array (
        0 => 'app\\http\\controllers\\admincontroller',
      ),
      2 => 
      array (
        0 => 'app\\http\\controllers\\__construct',
        1 => 'app\\http\\controllers\\index',
        2 => 'app\\http\\controllers\\create',
        3 => 'app\\http\\controllers\\store',
        4 => 'app\\http\\controllers\\show',
        5 => 'app\\http\\controllers\\edit',
        6 => 'app\\http\\controllers\\update',
        7 => 'app\\http\\controllers\\destroy',
      ),
      3 => 
      array (
      ),
    ),
    '/var/www/html/app/Http/Controllers/CalendarTemplateController.php' => 
    array (
      0 => '354a2419f62f94951540791ab9c2537c491a61fac0e6ffdac16fb15a8371b293',
      1 => 
      array (
        0 => 'app\\http\\controllers\\calendartemplatecontroller',
      ),
      2 => 
      array (
        0 => 'app\\http\\controllers\\__construct',
        1 => 'app\\http\\controllers\\index',
        2 => 'app\\http\\controllers\\create',
        3 => 'app\\http\\controllers\\store',
        4 => 'app\\http\\controllers\\edit',
        5 => 'app\\http\\controllers\\update',
        6 => 'app\\http\\controllers\\overridecampus',
        7 => 'app\\http\\controllers\\inheritcampus',
      ),
      3 => 
      array (
      ),
    ),
    '/var/www/html/app/Http/Controllers/ClassGroupController.php' => 
    array (
      0 => 'dc1ba8f7ab7c56e772c8fbe48f6ab25803b8d51ee76c7b84639e32a4d85fc6e0',
      1 => 
      array (
        0 => 'app\\http\\controllers\\classgroupcontroller',
      ),
      2 => 
      array (
        0 => 'app\\http\\controllers\\__construct',
        1 => 'app\\http\\controllers\\index',
        2 => 'app\\http\\controllers\\create',
        3 => 'app\\http\\controllers\\store',
        4 => 'app\\http\\controllers\\show',
        5 => 'app\\http\\controllers\\edit',
        6 => 'app\\http\\controllers\\update',
        7 => 'app\\http\\controllers\\destroy',
      ),
      3 => 
      array (
      ),
    ),
    '/var/www/html/app/Http/Controllers/Controller.php' => 
    array (
      0 => '431e628a8aedd2bd08fa3201493d4c40db69adad70c01857ea4aebf79eefe7c8',
      1 => 
      array (
        0 => 'app\\http\\controllers\\controller',
      ),
      2 => 
      array (
      ),
      3 => 
      array (
      ),
    ),
    '/var/www/html/app/Http/Controllers/CourseOfferingController.php' => 
    array (
      0 => '21dd6a90efa85ea1bb6b011f1ce1c955790d60651b1e274ee76eb87049e8762b',
      1 => 
      array (
        0 => 'app\\http\\controllers\\courseofferingcontroller',
      ),
      2 => 
      array (
        0 => 'app\\http\\controllers\\__construct',
        1 => 'app\\http\\controllers\\index',
        2 => 'app\\http\\controllers\\create',
        3 => 'app\\http\\controllers\\store',
        4 => 'app\\http\\controllers\\activate',
        5 => 'app\\http\\controllers\\assignteacher',
      ),
      3 => 
      array (
      ),
    ),
    '/var/www/html/app/Http/Controllers/CustomTimetableItemController.php' => 
    array (
      0 => '7a4aef9c3ed4ef6073a78b6e2849030bd0dce455e40e5a876a1c314fe87aa131',
      1 => 
      array (
        0 => 'app\\http\\controllers\\customtimetableitemcontroller',
      ),
      2 => 
      array (
        0 => 'app\\http\\controllers\\__construct',
        1 => 'app\\http\\controllers\\index',
        2 => 'app\\http\\controllers\\create',
        3 => 'app\\http\\controllers\\store',
        4 => 'app\\http\\controllers\\show',
        5 => 'app\\http\\controllers\\edit',
        6 => 'app\\http\\controllers\\update',
        7 => 'app\\http\\controllers\\destroy',
      ),
      3 => 
      array (
      ),
    ),
    '/var/www/html/app/Http/Controllers/ExamController.php' => 
    array (
      0 => '005a27ba2b9990df73ae155dd304aa45be5352a56cdbc3a7a2b5d87e994e1f3a',
      1 => 
      array (
        0 => 'app\\http\\controllers\\examcontroller',
      ),
      2 => 
      array (
        0 => 'app\\http\\controllers\\__construct',
        1 => 'app\\http\\controllers\\index',
        2 => 'app\\http\\controllers\\create',
        3 => 'app\\http\\controllers\\store',
        4 => 'app\\http\\controllers\\show',
        5 => 'app\\http\\controllers\\edit',
        6 => 'app\\http\\controllers\\update',
        7 => 'app\\http\\controllers\\destroy',
        8 => 'app\\http\\controllers\\examtabulation',
        9 => 'app\\http\\controllers\\academicperiodresulttabulation',
        10 => 'app\\http\\controllers\\academicyearresulttabulation',
        11 => 'app\\http\\controllers\\resultchecker',
        12 => 'app\\http\\controllers\\setexamactivestatus',
        13 => 'app\\http\\controllers\\setpublishresultstatus',
      ),
      3 => 
      array (
      ),
    ),
    '/var/www/html/app/Http/Controllers/ExamRecordController.php' => 
    array (
      0 => '0d2362efeeeee5772eebbe836cc35da3782043bb426163481343509e3178f348',
      1 => 
      array (
        0 => 'app\\http\\controllers\\examrecordcontroller',
      ),
      2 => 
      array (
        0 => 'app\\http\\controllers\\__construct',
        1 => 'app\\http\\controllers\\index',
        2 => 'app\\http\\controllers\\create',
        3 => 'app\\http\\controllers\\store',
        4 => 'app\\http\\controllers\\show',
        5 => 'app\\http\\controllers\\edit',
        6 => 'app\\http\\controllers\\update',
        7 => 'app\\http\\controllers\\destroy',
      ),
      3 => 
      array (
      ),
    ),
    '/var/www/html/app/Http/Controllers/ExamSlotController.php' => 
    array (
      0 => 'b49d42df540d4e83db6a9675bf4c59e5d32e5c41a9f06edaddc51950c96f121f',
      1 => 
      array (
        0 => 'app\\http\\controllers\\examslotcontroller',
      ),
      2 => 
      array (
        0 => 'app\\http\\controllers\\__construct',
        1 => 'app\\http\\controllers\\index',
        2 => 'app\\http\\controllers\\create',
        3 => 'app\\http\\controllers\\store',
        4 => 'app\\http\\controllers\\show',
        5 => 'app\\http\\controllers\\edit',
        6 => 'app\\http\\controllers\\update',
        7 => 'app\\http\\controllers\\destroy',
      ),
      3 => 
      array (
      ),
    ),
    '/var/www/html/app/Http/Controllers/FeeCategoryController.php' => 
    array (
      0 => 'a9f05964d5b5f0bdb717b023a927d76ba26d9f0af1aad7de7968418b940d37df',
      1 => 
      array (
        0 => 'app\\http\\controllers\\feecategorycontroller',
      ),
      2 => 
      array (
        0 => 'app\\http\\controllers\\__construct',
        1 => 'app\\http\\controllers\\index',
        2 => 'app\\http\\controllers\\create',
        3 => 'app\\http\\controllers\\store',
        4 => 'app\\http\\controllers\\show',
        5 => 'app\\http\\controllers\\edit',
        6 => 'app\\http\\controllers\\update',
        7 => 'app\\http\\controllers\\destroy',
      ),
      3 => 
      array (
      ),
    ),
    '/var/www/html/app/Http/Controllers/FeeController.php' => 
    array (
      0 => '3ebc81c6cfb727ea95dc937018fb30b16e88e9be81a35cb1f5b94525baef9612',
      1 => 
      array (
        0 => 'app\\http\\controllers\\feecontroller',
      ),
      2 => 
      array (
        0 => 'app\\http\\controllers\\__construct',
        1 => 'app\\http\\controllers\\index',
        2 => 'app\\http\\controllers\\create',
        3 => 'app\\http\\controllers\\store',
        4 => 'app\\http\\controllers\\show',
        5 => 'app\\http\\controllers\\edit',
        6 => 'app\\http\\controllers\\update',
        7 => 'app\\http\\controllers\\destroy',
      ),
      3 => 
      array (
      ),
    ),
    '/var/www/html/app/Http/Controllers/FeeInvoiceController.php' => 
    array (
      0 => '85f04f1523374dccb8f610d6a99f6a8f323a8cbb178d40ca5c049b19f0f9ab7c',
      1 => 
      array (
        0 => 'app\\http\\controllers\\feeinvoicecontroller',
      ),
      2 => 
      array (
        0 => 'app\\http\\controllers\\__construct',
        1 => 'app\\http\\controllers\\index',
        2 => 'app\\http\\controllers\\create',
        3 => 'app\\http\\controllers\\store',
        4 => 'app\\http\\controllers\\show',
        5 => 'app\\http\\controllers\\print',
        6 => 'app\\http\\controllers\\edit',
        7 => 'app\\http\\controllers\\update',
        8 => 'app\\http\\controllers\\destroy',
        9 => 'app\\http\\controllers\\payview',
      ),
      3 => 
      array (
      ),
    ),
    '/var/www/html/app/Http/Controllers/FeeInvoiceRecordController.php' => 
    array (
      0 => '96c1629111498bf1f141414aba4c28e503d3782564b2e740cbe7d2c620e26d91',
      1 => 
      array (
        0 => 'app\\http\\controllers\\feeinvoicerecordcontroller',
      ),
      2 => 
      array (
        0 => 'app\\http\\controllers\\__construct',
        1 => 'app\\http\\controllers\\index',
        2 => 'app\\http\\controllers\\create',
        3 => 'app\\http\\controllers\\store',
        4 => 'app\\http\\controllers\\show',
        5 => 'app\\http\\controllers\\edit',
        6 => 'app\\http\\controllers\\update',
        7 => 'app\\http\\controllers\\destroy',
        8 => 'app\\http\\controllers\\pay',
      ),
      3 => 
      array (
      ),
    ),
    '/var/www/html/app/Http/Controllers/GradeSystemController.php' => 
    array (
      0 => 'ed4db50480e1e85d0e4536d07393ff438eac94f3b2b3d3cc25d3774629059c3f',
      1 => 
      array (
        0 => 'app\\http\\controllers\\gradesystemcontroller',
      ),
      2 => 
      array (
        0 => 'app\\http\\controllers\\__construct',
        1 => 'app\\http\\controllers\\index',
        2 => 'app\\http\\controllers\\create',
        3 => 'app\\http\\controllers\\store',
        4 => 'app\\http\\controllers\\show',
        5 => 'app\\http\\controllers\\edit',
        6 => 'app\\http\\controllers\\update',
        7 => 'app\\http\\controllers\\destroy',
      ),
      3 => 
      array (
      ),
    ),
    '/var/www/html/app/Http/Controllers/GraduationController.php' => 
    array (
      0 => 'f70175d94bb32d9bddd72ec78ddbf170c752ef19b793aa9d4f6742215e7cbc1f',
      1 => 
      array (
        0 => 'app\\http\\controllers\\graduationcontroller',
      ),
      2 => 
      array (
        0 => 'app\\http\\controllers\\__construct',
        1 => 'app\\http\\controllers\\index',
        2 => 'app\\http\\controllers\\graduateview',
        3 => 'app\\http\\controllers\\graduate',
        4 => 'app\\http\\controllers\\resetgraduation',
      ),
      3 => 
      array (
      ),
    ),
    '/var/www/html/app/Http/Controllers/HealthController.php' => 
    array (
      0 => '49196a12409a84d89134b4fd41871fc30b3ed9edb8f65983743c439a08f30b64',
      1 => 
      array (
        0 => 'app\\http\\controllers\\healthcontroller',
      ),
      2 => 
      array (
        0 => 'app\\http\\controllers\\__invoke',
        1 => 'app\\http\\controllers\\check',
        2 => 'app\\http\\controllers\\schedulerisfresh',
      ),
      3 => 
      array (
      ),
    ),
    '/var/www/html/app/Http/Controllers/ImportController.php' => 
    array (
      0 => 'eacf7f44160362ddbecbd4bbd8d69f9f09553cf631937939b0f58b6a18d5218d',
      1 => 
      array (
        0 => 'app\\http\\controllers\\importcontroller',
      ),
      2 => 
      array (
        0 => 'app\\http\\controllers\\__construct',
        1 => 'app\\http\\controllers\\store',
        2 => 'app\\http\\controllers\\apply',
        3 => 'app\\http\\controllers\\cancel',
      ),
      3 => 
      array (
      ),
    ),
    '/var/www/html/app/Http/Controllers/InstructionalModelController.php' => 
    array (
      0 => '16ccc001a94f3fba21d7bb67e8667b8a1859df27fa8e9a96f30a3b0a220e8675',
      1 => 
      array (
        0 => 'app\\http\\controllers\\instructionalmodelcontroller',
      ),
      2 => 
      array (
        0 => 'app\\http\\controllers\\__construct',
        1 => 'app\\http\\controllers\\edit',
        2 => 'app\\http\\controllers\\update',
      ),
      3 => 
      array (
      ),
    ),
    '/var/www/html/app/Http/Controllers/MyClassController.php' => 
    array (
      0 => '772cfc7ca1cc48e40e3b1e82060b4babcb879bc99a72f4abfe81674aef89c8e5',
      1 => 
      array (
        0 => 'app\\http\\controllers\\myclasscontroller',
      ),
      2 => 
      array (
        0 => 'app\\http\\controllers\\__construct',
        1 => 'app\\http\\controllers\\index',
        2 => 'app\\http\\controllers\\create',
        3 => 'app\\http\\controllers\\store',
        4 => 'app\\http\\controllers\\show',
        5 => 'app\\http\\controllers\\edit',
        6 => 'app\\http\\controllers\\update',
        7 => 'app\\http\\controllers\\destroy',
      ),
      3 => 
      array (
      ),
    ),
    '/var/www/html/app/Http/Controllers/NoticeController.php' => 
    array (
      0 => '2acccb60d1538d147ce8e114c3aaf44e6e43cf3d29bde88b1d97cad855eb030e',
      1 => 
      array (
        0 => 'app\\http\\controllers\\noticecontroller',
      ),
      2 => 
      array (
        0 => 'app\\http\\controllers\\__construct',
        1 => 'app\\http\\controllers\\index',
        2 => 'app\\http\\controllers\\create',
        3 => 'app\\http\\controllers\\store',
        4 => 'app\\http\\controllers\\show',
        5 => 'app\\http\\controllers\\edit',
        6 => 'app\\http\\controllers\\update',
        7 => 'app\\http\\controllers\\destroy',
      ),
      3 => 
      array (
      ),
    ),
    '/var/www/html/app/Http/Controllers/OrganizationController.php' => 
    array (
      0 => '73b1754f9015782f6f39b0f02827e4c30398940b93548104f74ce0c992f17153',
      1 => 
      array (
        0 => 'app\\http\\controllers\\organizationcontroller',
      ),
      2 => 
      array (
        0 => 'app\\http\\controllers\\__construct',
        1 => 'app\\http\\controllers\\index',
        2 => 'app\\http\\controllers\\create',
        3 => 'app\\http\\controllers\\store',
        4 => 'app\\http\\controllers\\show',
        5 => 'app\\http\\controllers\\edit',
        6 => 'app\\http\\controllers\\update',
        7 => 'app\\http\\controllers\\destroy',
      ),
      3 => 
      array (
      ),
    ),
    '/var/www/html/app/Http/Controllers/OrganizationDashboardController.php' => 
    array (
      0 => '44b9fc5146c9b82527175b56409239f1f1171e3d60717af81b5952ee6b3f7242',
      1 => 
      array (
        0 => 'app\\http\\controllers\\organizationdashboardcontroller',
      ),
      2 => 
      array (
        0 => 'app\\http\\controllers\\__invoke',
      ),
      3 => 
      array (
      ),
    ),
    '/var/www/html/app/Http/Controllers/OrganizationMemberController.php' => 
    array (
      0 => '232c509bfea44f0f9fd8da1f664ecea017d7de9e3a26f397a52d0485931b1ffa',
      1 => 
      array (
        0 => 'app\\http\\controllers\\organizationmembercontroller',
      ),
      2 => 
      array (
        0 => 'app\\http\\controllers\\index',
      ),
      3 => 
      array (
      ),
    ),
    '/var/www/html/app/Http/Controllers/ParentController.php' => 
    array (
      0 => '74eafad13aa0cd5f8005bf8c9e056a5dc405529b7023b971a5b1aa76c7b545f6',
      1 => 
      array (
        0 => 'app\\http\\controllers\\parentcontroller',
      ),
      2 => 
      array (
        0 => 'app\\http\\controllers\\__construct',
        1 => 'app\\http\\controllers\\index',
        2 => 'app\\http\\controllers\\create',
        3 => 'app\\http\\controllers\\store',
        4 => 'app\\http\\controllers\\show',
        5 => 'app\\http\\controllers\\edit',
        6 => 'app\\http\\controllers\\update',
        7 => 'app\\http\\controllers\\destroy',
        8 => 'app\\http\\controllers\\assignstudentsview',
        9 => 'app\\http\\controllers\\assignstudent',
      ),
      3 => 
      array (
      ),
    ),
    '/var/www/html/app/Http/Controllers/PromotionController.php' => 
    array (
      0 => 'de71acc5afeb4a570f940cddeeaa4f5050d772b5c0fc8aedf0f26c02dccbf65b',
      1 => 
      array (
        0 => 'app\\http\\controllers\\promotioncontroller',
      ),
      2 => 
      array (
        0 => 'app\\http\\controllers\\__construct',
        1 => 'app\\http\\controllers\\index',
        2 => 'app\\http\\controllers\\promoteview',
        3 => 'app\\http\\controllers\\promote',
        4 => 'app\\http\\controllers\\resetpromotion',
        5 => 'app\\http\\controllers\\show',
      ),
      3 => 
      array (
      ),
    ),
    '/var/www/html/app/Http/Controllers/ReportController.php' => 
    array (
      0 => '69d8535978197d63ea4909484fc3d8da7ff06bd90862bb771646037fac266025',
      1 => 
      array (
        0 => 'app\\http\\controllers\\reportcontroller',
      ),
      2 => 
      array (
        0 => 'app\\http\\controllers\\__construct',
        1 => 'app\\http\\controllers\\store',
        2 => 'app\\http\\controllers\\download',
      ),
      3 => 
      array (
      ),
    ),
    '/var/www/html/app/Http/Controllers/SchoolController.php' => 
    array (
      0 => '5ceb7dc305158ce3b04405d17cb62d22ce202997ef8c5c55322df09977b622ff',
      1 => 
      array (
        0 => 'app\\http\\controllers\\schoolcontroller',
      ),
      2 => 
      array (
        0 => 'app\\http\\controllers\\__construct',
        1 => 'app\\http\\controllers\\index',
        2 => 'app\\http\\controllers\\create',
        3 => 'app\\http\\controllers\\store',
        4 => 'app\\http\\controllers\\show',
        5 => 'app\\http\\controllers\\edit',
        6 => 'app\\http\\controllers\\update',
        7 => 'app\\http\\controllers\\destroy',
        8 => 'app\\http\\controllers\\settings',
        9 => 'app\\http\\controllers\\setschool',
      ),
      3 => 
      array (
      ),
    ),
    '/var/www/html/app/Http/Controllers/SectionController.php' => 
    array (
      0 => 'bfc4d5780dbc9ebf3e3717bf8f6ba48682208a4f4c906a722b31c18b8dd6c9e1',
      1 => 
      array (
        0 => 'app\\http\\controllers\\sectioncontroller',
      ),
      2 => 
      array (
        0 => 'app\\http\\controllers\\__construct',
        1 => 'app\\http\\controllers\\index',
        2 => 'app\\http\\controllers\\create',
        3 => 'app\\http\\controllers\\store',
        4 => 'app\\http\\controllers\\show',
        5 => 'app\\http\\controllers\\edit',
        6 => 'app\\http\\controllers\\update',
        7 => 'app\\http\\controllers\\destroy',
      ),
      3 => 
      array (
      ),
    ),
    '/var/www/html/app/Http/Controllers/StudentController.php' => 
    array (
      0 => '2af8c22f2d24796f74c8886d634f264fc1b01f8d4fcccc9929c6827b57083175',
      1 => 
      array (
        0 => 'app\\http\\controllers\\studentcontroller',
      ),
      2 => 
      array (
        0 => 'app\\http\\controllers\\__construct',
        1 => 'app\\http\\controllers\\index',
        2 => 'app\\http\\controllers\\create',
        3 => 'app\\http\\controllers\\store',
        4 => 'app\\http\\controllers\\show',
        5 => 'app\\http\\controllers\\printprofile',
        6 => 'app\\http\\controllers\\edit',
        7 => 'app\\http\\controllers\\update',
        8 => 'app\\http\\controllers\\destroy',
      ),
      3 => 
      array (
      ),
    ),
    '/var/www/html/app/Http/Controllers/SubjectController.php' => 
    array (
      0 => '4bc90d4522660c34c019faa14e2f0ec401309707cdfb25a6a30e18e81e63c701',
      1 => 
      array (
        0 => 'app\\http\\controllers\\subjectcontroller',
      ),
      2 => 
      array (
        0 => 'app\\http\\controllers\\__construct',
        1 => 'app\\http\\controllers\\index',
        2 => 'app\\http\\controllers\\create',
        3 => 'app\\http\\controllers\\store',
        4 => 'app\\http\\controllers\\show',
        5 => 'app\\http\\controllers\\edit',
        6 => 'app\\http\\controllers\\update',
        7 => 'app\\http\\controllers\\destroy',
        8 => 'app\\http\\controllers\\assignteacherview',
        9 => 'app\\http\\controllers\\assignteacher',
      ),
      3 => 
      array (
      ),
    ),
    '/var/www/html/app/Http/Controllers/SyllabusController.php' => 
    array (
      0 => '377289cfd7e343ee15b89dd09177418f44d585e83b32759b175ba893b14315ed',
      1 => 
      array (
        0 => 'app\\http\\controllers\\syllabuscontroller',
      ),
      2 => 
      array (
        0 => 'app\\http\\controllers\\__construct',
        1 => 'app\\http\\controllers\\index',
        2 => 'app\\http\\controllers\\create',
        3 => 'app\\http\\controllers\\store',
        4 => 'app\\http\\controllers\\show',
        5 => 'app\\http\\controllers\\edit',
        6 => 'app\\http\\controllers\\update',
        7 => 'app\\http\\controllers\\destroy',
      ),
      3 => 
      array (
      ),
    ),
    '/var/www/html/app/Http/Controllers/TeacherController.php' => 
    array (
      0 => '663183f456193a4fe5d111fe4655f3999d848094d299bce7de62b6ecb52788ad',
      1 => 
      array (
        0 => 'app\\http\\controllers\\teachercontroller',
      ),
      2 => 
      array (
        0 => 'app\\http\\controllers\\__construct',
        1 => 'app\\http\\controllers\\index',
        2 => 'app\\http\\controllers\\create',
        3 => 'app\\http\\controllers\\store',
        4 => 'app\\http\\controllers\\show',
        5 => 'app\\http\\controllers\\edit',
        6 => 'app\\http\\controllers\\update',
        7 => 'app\\http\\controllers\\destroy',
      ),
      3 => 
      array (
      ),
    ),
    '/var/www/html/app/Http/Controllers/TimetableController.php' => 
    array (
      0 => '53b503cd36915e6382208ec4244e1cccc6e71fdd5f45084747cf7fc689d48b07',
      1 => 
      array (
        0 => 'app\\http\\controllers\\timetablecontroller',
      ),
      2 => 
      array (
        0 => 'app\\http\\controllers\\__construct',
        1 => 'app\\http\\controllers\\index',
        2 => 'app\\http\\controllers\\create',
        3 => 'app\\http\\controllers\\store',
        4 => 'app\\http\\controllers\\show',
        5 => 'app\\http\\controllers\\print',
        6 => 'app\\http\\controllers\\edit',
        7 => 'app\\http\\controllers\\update',
        8 => 'app\\http\\controllers\\destroy',
        9 => 'app\\http\\controllers\\manage',
        10 => 'app\\http\\controllers\\publish',
        11 => 'app\\http\\controllers\\revise',
      ),
      3 => 
      array (
      ),
    ),
    '/var/www/html/app/Http/Controllers/TimetableTimeSlotController.php' => 
    array (
      0 => '17826bf8f055be9d6865ff7035a31f34fdf9067d89da2acd2593d9181b9e97b2',
      1 => 
      array (
        0 => 'app\\http\\controllers\\timetabletimeslotcontroller',
      ),
      2 => 
      array (
        0 => 'app\\http\\controllers\\__construct',
        1 => 'app\\http\\controllers\\index',
        2 => 'app\\http\\controllers\\create',
        3 => 'app\\http\\controllers\\store',
        4 => 'app\\http\\controllers\\show',
        5 => 'app\\http\\controllers\\edit',
        6 => 'app\\http\\controllers\\update',
        7 => 'app\\http\\controllers\\destroy',
        8 => 'app\\http\\controllers\\addtimetablerecord',
      ),
      3 => 
      array (
      ),
    ),
    '/var/www/html/app/Http/Middleware/CreateCurrentAcademicYearRecord.php' => 
    array (
      0 => '1cb0f761e22ddca3850e72f7700b87d2ef6b5c1df9c36a9fbd103cf5da58faec',
      1 => 
      array (
        0 => 'app\\http\\middleware\\createcurrentacademicyearrecord',
      ),
      2 => 
      array (
        0 => 'app\\http\\middleware\\__construct',
        1 => 'app\\http\\middleware\\handle',
      ),
      3 => 
      array (
      ),
    ),
    '/var/www/html/app/Http/Middleware/EnsureAcademicPeriodIsSet.php' => 
    array (
      0 => '7c05f0f07478546c9b6d96440446c880900d8eff6ac187eb74e8b85e9ac60e96',
      1 => 
      array (
        0 => 'app\\http\\middleware\\ensureacademicperiodisset',
      ),
      2 => 
      array (
        0 => 'app\\http\\middleware\\handle',
      ),
      3 => 
      array (
      ),
    ),
    '/var/www/html/app/Http/Middleware/EnsureAcademicYearIsSet.php' => 
    array (
      0 => '4c3282b68720bddc5719cdc33b6e23cff8b5cc1517181b3628e162743b922a52',
      1 => 
      array (
        0 => 'app\\http\\middleware\\ensureacademicyearisset',
      ),
      2 => 
      array (
        0 => 'app\\http\\middleware\\handle',
      ),
      3 => 
      array (
      ),
    ),
    '/var/www/html/app/Http/Middleware/EnsureAccountIsActive.php' => 
    array (
      0 => '2213b1742615e18b1a8f727f7b3fe0d104e84ac2b6f6e552d8bb7cacacdcd425',
      1 => 
      array (
        0 => 'app\\http\\middleware\\ensureaccountisactive',
      ),
      2 => 
      array (
        0 => 'app\\http\\middleware\\handle',
      ),
      3 => 
      array (
      ),
    ),
    '/var/www/html/app/Http/Middleware/EnsureFeatureIsEnabled.php' => 
    array (
      0 => 'c9d9074660d2729c3700642f6ba80ceb1701218fb146fddf99a71f5241e0379f',
      1 => 
      array (
        0 => 'app\\http\\middleware\\ensurefeatureisenabled',
      ),
      2 => 
      array (
        0 => 'app\\http\\middleware\\handle',
      ),
      3 => 
      array (
      ),
    ),
    '/var/www/html/app/Http/Middleware/PreventGraduatedStudent.php' => 
    array (
      0 => '7c3bf4b91d01686db5d83d7b6866c095f6cf01f2ac4898925fa491f4e7359315',
      1 => 
      array (
        0 => 'app\\http\\middleware\\preventgraduatedstudent',
      ),
      2 => 
      array (
        0 => 'app\\http\\middleware\\handle',
      ),
      3 => 
      array (
      ),
    ),
    '/var/www/html/app/Http/Middleware/RequireActiveSchool.php' => 
    array (
      0 => '43e96099af1e0efa5833f8fa1d6f15aac2eafa3555c103605de6f8e291b1ad0b',
      1 => 
      array (
        0 => 'app\\http\\middleware\\requireactiveschool',
      ),
      2 => 
      array (
        0 => 'app\\http\\middleware\\__construct',
        1 => 'app\\http\\middleware\\handle',
      ),
      3 => 
      array (
      ),
    ),
    '/var/www/html/app/Http/Middleware/SetActiveAcademicPeriod.php' => 
    array (
      0 => 'edd625756fa57b37be5faddadd322439d0a47135665780a876ba07e90c6555ef',
      1 => 
      array (
        0 => 'app\\http\\middleware\\setactiveacademicperiod',
      ),
      2 => 
      array (
        0 => 'app\\http\\middleware\\__construct',
        1 => 'app\\http\\middleware\\handle',
      ),
      3 => 
      array (
      ),
    ),
    '/var/www/html/app/Http/Middleware/SetActiveSchool.php' => 
    array (
      0 => '45dd579bdf9443ade897ff7a9f5b73aafce98d8e83cb09a98fe72291305df9a4',
      1 => 
      array (
        0 => 'app\\http\\middleware\\setactiveschool',
      ),
      2 => 
      array (
        0 => 'app\\http\\middleware\\__construct',
        1 => 'app\\http\\middleware\\handle',
      ),
      3 => 
      array (
      ),
    ),
    '/var/www/html/app/Http/Middleware/TrustHosts.php' => 
    array (
      0 => '99458e7167ed710e0ac8d450a496e3c12d5a3f66b5586d0da04a4ad86720c4fe',
      1 => 
      array (
        0 => 'app\\http\\middleware\\trusthosts',
      ),
      2 => 
      array (
        0 => 'app\\http\\middleware\\hosts',
      ),
      3 => 
      array (
      ),
    ),
    '/var/www/html/app/Http/Requests/AcademicPeriodStoreRequest.php' => 
    array (
      0 => '09c3e28ecdcedba79371b6d5d25a6da0721a73151c6eddfb6d57b3d5dd9638a6',
      1 => 
      array (
        0 => 'app\\http\\requests\\academicperiodstorerequest',
      ),
      2 => 
      array (
        0 => 'app\\http\\requests\\rules',
      ),
      3 => 
      array (
      ),
    ),
    '/var/www/html/app/Http/Requests/AcademicYearStoreRequest.php' => 
    array (
      0 => '4f0faf79fb9f6da8091b88ca17a5087e8ccc5f3163f7359317cb822760e11e3c',
      1 => 
      array (
        0 => 'app\\http\\requests\\academicyearstorerequest',
      ),
      2 => 
      array (
        0 => 'app\\http\\requests\\rules',
      ),
      3 => 
      array (
      ),
    ),
    '/var/www/html/app/Http/Requests/AcceptAccountInvitationRequest.php' => 
    array (
      0 => '6d658c5e5ac9f40c1d76b5700bd5abe7ddf5371b3539effff995be31eb58b411',
      1 => 
      array (
        0 => 'app\\http\\requests\\acceptaccountinvitationrequest',
      ),
      2 => 
      array (
        0 => 'app\\http\\requests\\authorize',
        1 => 'app\\http\\requests\\rules',
      ),
      3 => 
      array (
      ),
    ),
    '/var/www/html/app/Http/Requests/AssignStudentRequest.php' => 
    array (
      0 => 'e646ba4a6622efeacbeb397a43d8a496097951fd8cbadaf6e9b19a18b3f3ae53',
      1 => 
      array (
        0 => 'app\\http\\requests\\assignstudentrequest',
      ),
      2 => 
      array (
        0 => 'app\\http\\requests\\rules',
      ),
      3 => 
      array (
      ),
    ),
    '/var/www/html/app/Http/Requests/AssignTeacherToCourseOfferingRequest.php' => 
    array (
      0 => '2e46f00275d40c012c08bd359e209e2ba39c815b3a5c8e66c2cd9c302b21d94c',
      1 => 
      array (
        0 => 'app\\http\\requests\\assignteachertocourseofferingrequest',
      ),
      2 => 
      array (
        0 => 'app\\http\\requests\\authorize',
        1 => 'app\\http\\requests\\rules',
      ),
      3 => 
      array (
      ),
    ),
    '/var/www/html/app/Http/Requests/AssignTeacherToSubjectRequest.php' => 
    array (
      0 => '3e5cccb5e55c9007a953829dbbbaa272a02bd153beed48c2a51046dff8f39b38',
      1 => 
      array (
        0 => 'app\\http\\requests\\assignteachertosubjectrequest',
      ),
      2 => 
      array (
        0 => 'app\\http\\requests\\rules',
      ),
      3 => 
      array (
      ),
    ),
    '/var/www/html/app/Http/Requests/ChangeAcademicCycleSectionStatusRequest.php' => 
    array (
      0 => '6a494ec19cf678de69b23402daf434563152223bda0b6bb6870d5afeb35c85c6',
      1 => 
      array (
        0 => 'app\\http\\requests\\changeacademiccyclesectionstatusrequest',
      ),
      2 => 
      array (
        0 => 'app\\http\\requests\\authorize',
        1 => 'app\\http\\requests\\rules',
      ),
      3 => 
      array (
      ),
    ),
    '/var/www/html/app/Http/Requests/ChangeAcademicPeriodStatusRequest.php' => 
    array (
      0 => '0e37ebbd5a9a97e4e02f676eaaea249fb4fd115cb1bc33b599ab5adcf7164681',
      1 => 
      array (
        0 => 'app\\http\\requests\\changeacademicperiodstatusrequest',
      ),
      2 => 
      array (
        0 => 'app\\http\\requests\\authorize',
        1 => 'app\\http\\requests\\rules',
      ),
      3 => 
      array (
      ),
    ),
    '/var/www/html/app/Http/Requests/ChangeAccountStatusRequest.php' => 
    array (
      0 => '23207d59ba1ad4f599c3084fd6ec05e903578090b5761550e9aa45f295acc2bb',
      1 => 
      array (
        0 => 'app\\http\\requests\\changeaccountstatusrequest',
      ),
      2 => 
      array (
        0 => 'app\\http\\requests\\authorize',
        1 => 'app\\http\\requests\\rules',
      ),
      3 => 
      array (
      ),
    ),
    '/var/www/html/app/Http/Requests/ChangeCourseOfferingStatusRequest.php' => 
    array (
      0 => '3adea62f1d60b42c57ae95729fcd654d134f003846237c278d6c5f5cbecc9a03',
      1 => 
      array (
        0 => 'app\\http\\requests\\changecourseofferingstatusrequest',
      ),
      2 => 
      array (
        0 => 'app\\http\\requests\\authorize',
        1 => 'app\\http\\requests\\rules',
      ),
      3 => 
      array (
      ),
    ),
    '/var/www/html/app/Http/Requests/ClassGroupStoreRequest.php' => 
    array (
      0 => 'b3a56eafe53f48d6ed03833ef531f228b9e7db59e31a13d97455a410de52a497',
      1 => 
      array (
        0 => 'app\\http\\requests\\classgroupstorerequest',
      ),
      2 => 
      array (
        0 => 'app\\http\\requests\\prepareforvalidation',
        1 => 'app\\http\\requests\\rules',
      ),
      3 => 
      array (
      ),
    ),
    '/var/www/html/app/Http/Requests/GenerateAcademicCycleRequest.php' => 
    array (
      0 => '59e4727c90192a9e3e48c0b97115161cf2c12e8b5ddbb91b1860b6de5dca6806',
      1 => 
      array (
        0 => 'app\\http\\requests\\generateacademiccyclerequest',
      ),
      2 => 
      array (
        0 => 'app\\http\\requests\\authorize',
        1 => 'app\\http\\requests\\rules',
      ),
      3 => 
      array (
      ),
    ),
    '/var/www/html/app/Http/Requests/MyClassStoreRequest.php' => 
    array (
      0 => 'ec5a2b4f231929b24659e2224a50817073d4605a60125e73d8d3edd32491656e',
      1 => 
      array (
        0 => 'app\\http\\requests\\myclassstorerequest',
      ),
      2 => 
      array (
        0 => 'app\\http\\requests\\rules',
        1 => 'app\\http\\requests\\messages',
      ),
      3 => 
      array (
      ),
    ),
    '/var/www/html/app/Http/Requests/MyClassUpdateRequest.php' => 
    array (
      0 => 'b39944d219b7ee095bc75c711372bd9d8125cda633d109ea7cceefbb8fa60779',
      1 => 
      array (
        0 => 'app\\http\\requests\\myclassupdaterequest',
      ),
      2 => 
      array (
        0 => 'app\\http\\requests\\rules',
      ),
      3 => 
      array (
      ),
    ),
    '/var/www/html/app/Http/Requests/OrganizationStoreRequest.php' => 
    array (
      0 => '9b202f8498cfe01f09f5e85ee8af39d61c956555c4a7e67711db40891693d78f',
      1 => 
      array (
        0 => 'app\\http\\requests\\organizationstorerequest',
      ),
      2 => 
      array (
        0 => 'app\\http\\requests\\authorize',
        1 => 'app\\http\\requests\\rules',
      ),
      3 => 
      array (
      ),
    ),
    '/var/www/html/app/Http/Requests/OrganizationUpdateRequest.php' => 
    array (
      0 => '0936f8fec483c62762380d4a4d81c1bf74f5b17529a3adda223e46eb30439d9b',
      1 => 
      array (
        0 => 'app\\http\\requests\\organizationupdaterequest',
      ),
      2 => 
      array (
        0 => 'app\\http\\requests\\authorize',
        1 => 'app\\http\\requests\\rules',
      ),
      3 => 
      array (
      ),
    ),
    '/var/www/html/app/Http/Requests/PayFeeInvoiceRequest.php' => 
    array (
      0 => '48f37ba1aba1efccba02a7f60adc5acf1f339bfade8494fc4cf8f944191760cb',
      1 => 
      array (
        0 => 'app\\http\\requests\\payfeeinvoicerequest',
      ),
      2 => 
      array (
        0 => 'app\\http\\requests\\rules',
      ),
      3 => 
      array (
      ),
    ),
    '/var/www/html/app/Http/Requests/RollForwardAcademicCycleSectionsRequest.php' => 
    array (
      0 => 'ff0e0fd807ed88536be274275f3f3bb097d6c148b80e71e00c077b207cd66eee',
      1 => 
      array (
        0 => 'app\\http\\requests\\rollforwardacademiccyclesectionsrequest',
      ),
      2 => 
      array (
        0 => 'app\\http\\requests\\authorize',
        1 => 'app\\http\\requests\\rules',
      ),
      3 => 
      array (
      ),
    ),
    '/var/www/html/app/Http/Requests/SchoolSetRequest.php' => 
    array (
      0 => '3939606f0d49f22fbdc1a52e45f3f74d4ed8b4f3ec6126520dba48e76b18cfcf',
      1 => 
      array (
        0 => 'app\\http\\requests\\schoolsetrequest',
      ),
      2 => 
      array (
        0 => 'app\\http\\requests\\authorize',
        1 => 'app\\http\\requests\\rules',
      ),
      3 => 
      array (
      ),
    ),
    '/var/www/html/app/Http/Requests/SchoolStoreRequest.php' => 
    array (
      0 => '2b318f74a3b30fe6b084dba65a152ab3239e1e335abfa8fba5045f8b4bf96586',
      1 => 
      array (
        0 => 'app\\http\\requests\\schoolstorerequest',
      ),
      2 => 
      array (
        0 => 'app\\http\\requests\\rules',
      ),
      3 => 
      array (
      ),
    ),
    '/var/www/html/app/Http/Requests/SchoolUpdateRequest.php' => 
    array (
      0 => '3f11606eb232b80d3511297a4039dd46ce684bc48165b093cfe9273e03448ada',
      1 => 
      array (
        0 => 'app\\http\\requests\\schoolupdaterequest',
      ),
      2 => 
      array (
        0 => 'app\\http\\requests\\rules',
      ),
      3 => 
      array (
      ),
    ),
    '/var/www/html/app/Http/Requests/SectionStoreRequest.php' => 
    array (
      0 => '9f2af2f26d9e7764a6572a7362ae8277d83a86dbda1c11f58728c16868765455',
      1 => 
      array (
        0 => 'app\\http\\requests\\sectionstorerequest',
      ),
      2 => 
      array (
        0 => 'app\\http\\requests\\rules',
        1 => 'app\\http\\requests\\messages',
      ),
      3 => 
      array (
      ),
    ),
    '/var/www/html/app/Http/Requests/SectionUpdateRequest.php' => 
    array (
      0 => '2cb307b15d313679402f679dd7596cb00fd1af430bb1485da5ba3f657e720690',
      1 => 
      array (
        0 => 'app\\http\\requests\\sectionupdaterequest',
      ),
      2 => 
      array (
        0 => 'app\\http\\requests\\rules',
      ),
      3 => 
      array (
      ),
    ),
    '/var/www/html/app/Http/Requests/SetAcademicPeriodRequest.php' => 
    array (
      0 => '4b929a4a87b802dc7e2be957274fd6468f5726a5bf2a64a5b5b7dde6e1600844',
      1 => 
      array (
        0 => 'app\\http\\requests\\setacademicperiodrequest',
      ),
      2 => 
      array (
        0 => 'app\\http\\requests\\rules',
      ),
      3 => 
      array (
      ),
    ),
    '/var/www/html/app/Http/Requests/SetCampusCalendarTemplateRequest.php' => 
    array (
      0 => '92720826e8791a5f02019f7aa736234cf7a9e5e8e76961884fb0780151b58e80',
      1 => 
      array (
        0 => 'app\\http\\requests\\setcampuscalendartemplaterequest',
      ),
      2 => 
      array (
        0 => 'app\\http\\requests\\authorize',
        1 => 'app\\http\\requests\\rules',
      ),
      3 => 
      array (
      ),
    ),
    '/var/www/html/app/Http/Requests/SetInstructionalModelRequest.php' => 
    array (
      0 => '41e9919b34b14f51ae2e32a04aab3a6758774468651081f9d4752d347d6919a1',
      1 => 
      array (
        0 => 'app\\http\\requests\\setinstructionalmodelrequest',
      ),
      2 => 
      array (
        0 => 'app\\http\\requests\\authorize',
        1 => 'app\\http\\requests\\rules',
        2 => 'app\\http\\requests\\messages',
      ),
      3 => 
      array (
      ),
    ),
    '/var/www/html/app/Http/Requests/StoreAcademicCycleSectionRequest.php' => 
    array (
      0 => '0df0f94d9c39ebc756b3b342738caa70122d3ab5b0f063fc54ce2639a26bd642',
      1 => 
      array (
        0 => 'app\\http\\requests\\storeacademiccyclesectionrequest',
      ),
      2 => 
      array (
        0 => 'app\\http\\requests\\authorize',
        1 => 'app\\http\\requests\\rules',
      ),
      3 => 
      array (
      ),
    ),
    '/var/www/html/app/Http/Requests/StoreAcademicLevelRequest.php' => 
    array (
      0 => '618b895f5fcaa30b3fc73389b032707bc407846094b16dc5994c944fcf5655f9',
      1 => 
      array (
        0 => 'app\\http\\requests\\storeacademiclevelrequest',
      ),
      2 => 
      array (
        0 => 'app\\http\\requests\\authorize',
        1 => 'app\\http\\requests\\rules',
      ),
      3 => 
      array (
      ),
    ),
    '/var/www/html/app/Http/Requests/StoreCalendarTemplateRequest.php' => 
    array (
      0 => '448c23171beb58a3dfe7e950fabc47f41217e0ce4d5e74eba3cb86113bb735bc',
      1 => 
      array (
        0 => 'app\\http\\requests\\storecalendartemplaterequest',
      ),
      2 => 
      array (
        0 => 'app\\http\\requests\\authorize',
        1 => 'app\\http\\requests\\rules',
      ),
      3 => 
      array (
      ),
    ),
    '/var/www/html/app/Http/Requests/StoreCourseOfferingRequest.php' => 
    array (
      0 => '7deee9d9e65555a8e57455e75d3dd34ee679c23239e0b2806b100efded2c2227',
      1 => 
      array (
        0 => 'app\\http\\requests\\storecourseofferingrequest',
      ),
      2 => 
      array (
        0 => 'app\\http\\requests\\authorize',
        1 => 'app\\http\\requests\\rules',
      ),
      3 => 
      array (
      ),
    ),
    '/var/www/html/app/Http/Requests/StoreCustomTimetableItemRequest.php' => 
    array (
      0 => '57c6a55beba7c0349db561996f3a633792c4107b974467404899da176b2285d2',
      1 => 
      array (
        0 => 'app\\http\\requests\\storecustomtimetableitemrequest',
      ),
      2 => 
      array (
        0 => 'app\\http\\requests\\prepareforvalidation',
        1 => 'app\\http\\requests\\rules',
      ),
      3 => 
      array (
      ),
    ),
    '/var/www/html/app/Http/Requests/StoreExamRecordRequest.php' => 
    array (
      0 => '1c42c28ded5f81aae5508d9e5222ba66e3742bbed56391e9e08039c92ef6234e',
      1 => 
      array (
        0 => 'app\\http\\requests\\storeexamrecordrequest',
      ),
      2 => 
      array (
        0 => 'app\\http\\requests\\rules',
      ),
      3 => 
      array (
      ),
    ),
    '/var/www/html/app/Http/Requests/StoreExamRequest.php' => 
    array (
      0 => '44df53f5c20195e21efc0fc62b13ad1b2cda96bf1d23c4cd347d7488adcbb4e5',
      1 => 
      array (
        0 => 'app\\http\\requests\\storeexamrequest',
      ),
      2 => 
      array (
        0 => 'app\\http\\requests\\rules',
      ),
      3 => 
      array (
      ),
    ),
    '/var/www/html/app/Http/Requests/StoreExamSlotRequest.php' => 
    array (
      0 => '3545f310bbdaa2262cdcffbead3f5defe42fa5611cab66cddf1a1de619c6799e',
      1 => 
      array (
        0 => 'app\\http\\requests\\storeexamslotrequest',
      ),
      2 => 
      array (
        0 => 'app\\http\\requests\\rules',
      ),
      3 => 
      array (
      ),
    ),
    '/var/www/html/app/Http/Requests/StoreFeeCategoryRequest.php' => 
    array (
      0 => '2162460b07cc22c68ce4ca42d825623e4a196dc71b2ebc728aa9883a2811fc40',
      1 => 
      array (
        0 => 'app\\http\\requests\\storefeecategoryrequest',
      ),
      2 => 
      array (
        0 => 'app\\http\\requests\\prepareforvalidation',
        1 => 'app\\http\\requests\\rules',
      ),
      3 => 
      array (
      ),
    ),
    '/var/www/html/app/Http/Requests/StoreFeeInvoiceRecordRequest.php' => 
    array (
      0 => '594a88d51cf04717e1175de59811c2bd63e533be0cf1880c101920ebcdee9173',
      1 => 
      array (
        0 => 'app\\http\\requests\\storefeeinvoicerecordrequest',
      ),
      2 => 
      array (
        0 => 'app\\http\\requests\\rules',
      ),
      3 => 
      array (
      ),
    ),
    '/var/www/html/app/Http/Requests/StoreFeeInvoiceRequest.php' => 
    array (
      0 => '707207bf17b83a089583155c54637a541ebd577f078613f9b08008a2014f2f28',
      1 => 
      array (
        0 => 'app\\http\\requests\\storefeeinvoicerequest',
      ),
      2 => 
      array (
        0 => 'app\\http\\requests\\rules',
        1 => 'app\\http\\requests\\messages',
      ),
      3 => 
      array (
      ),
    ),
    '/var/www/html/app/Http/Requests/StoreFeeRequest.php' => 
    array (
      0 => '9d8b8463af26227ac335d5f69c5d634ccfb4e6a2a9aa40cf7672d3eb7122dcfa',
      1 => 
      array (
        0 => 'app\\http\\requests\\storefeerequest',
      ),
      2 => 
      array (
        0 => 'app\\http\\requests\\rules',
      ),
      3 => 
      array (
      ),
    ),
    '/var/www/html/app/Http/Requests/StoreGradeSystemRequest.php' => 
    array (
      0 => '58c46f54315bfbc10b0d79ca27526c63a87768e6b42abb55ce86f743cd293c65',
      1 => 
      array (
        0 => 'app\\http\\requests\\storegradesystemrequest',
      ),
      2 => 
      array (
        0 => 'app\\http\\requests\\rules',
      ),
      3 => 
      array (
      ),
    ),
    '/var/www/html/app/Http/Requests/StoreImportBatchRequest.php' => 
    array (
      0 => 'd0676b762b5804e0678b773fc690d8e60ef79dd0e61f920b420b8fd5eb70814d',
      1 => 
      array (
        0 => 'app\\http\\requests\\storeimportbatchrequest',
      ),
      2 => 
      array (
        0 => 'app\\http\\requests\\rules',
      ),
      3 => 
      array (
      ),
    ),
    '/var/www/html/app/Http/Requests/StoreNoticeRequest.php' => 
    array (
      0 => 'f11c9a5db194ea5ad702695f6f7b8f7c5d821a25a8eeefe32d5d2987c3f8ea36',
      1 => 
      array (
        0 => 'app\\http\\requests\\storenoticerequest',
      ),
      2 => 
      array (
        0 => 'app\\http\\requests\\rules',
      ),
      3 => 
      array (
      ),
    ),
    '/var/www/html/app/Http/Requests/StoreReportRunRequest.php' => 
    array (
      0 => 'db3c89e31ce958e209752adaf7188f337620d6013a81eace2baf879c2dc8684e',
      1 => 
      array (
        0 => 'app\\http\\requests\\storereportrunrequest',
      ),
      2 => 
      array (
        0 => 'app\\http\\requests\\rules',
      ),
      3 => 
      array (
      ),
    ),
    '/var/www/html/app/Http/Requests/StoreSyllabusRequest.php' => 
    array (
      0 => '35680e24a69d0ee5b28cff41eddfd50ded950229c7588cd74f402e48cabbe2af',
      1 => 
      array (
        0 => 'app\\http\\requests\\storesyllabusrequest',
      ),
      2 => 
      array (
        0 => 'app\\http\\requests\\rules',
      ),
      3 => 
      array (
      ),
    ),
    '/var/www/html/app/Http/Requests/StoreTimetableRecordRequest.php' => 
    array (
      0 => 'fc84ae434131ddb1423412e9c6be5504bfea0e5ef98e425958bfa6a7dea32eec',
      1 => 
      array (
        0 => 'app\\http\\requests\\storetimetablerecordrequest',
      ),
      2 => 
      array (
        0 => 'app\\http\\requests\\authorize',
        1 => 'app\\http\\requests\\rules',
      ),
      3 => 
      array (
      ),
    ),
    '/var/www/html/app/Http/Requests/StoreTimetableTimeSlotRequest.php' => 
    array (
      0 => 'a9481026331190f14ded12cfdd82b4be48b05ea1196d31301b398c125b27d4fb',
      1 => 
      array (
        0 => 'app\\http\\requests\\storetimetabletimeslotrequest',
      ),
      2 => 
      array (
        0 => 'app\\http\\requests\\rules',
      ),
      3 => 
      array (
      ),
    ),
    '/var/www/html/app/Http/Requests/StudentGraduateRequest.php' => 
    array (
      0 => '90eadd428245ffa425dafb207f2400fe8e0c1bd354c183c392d5a810800b7dcc',
      1 => 
      array (
        0 => 'app\\http\\requests\\studentgraduaterequest',
      ),
      2 => 
      array (
        0 => 'app\\http\\requests\\rules',
      ),
      3 => 
      array (
      ),
    ),
    '/var/www/html/app/Http/Requests/StudentPromoteRequest.php' => 
    array (
      0 => '7baf22c25fb382cacdaf47e583c02210ef953f8285819103a3c5c583358cdc35',
      1 => 
      array (
        0 => 'app\\http\\requests\\studentpromoterequest',
      ),
      2 => 
      array (
        0 => 'app\\http\\requests\\rules',
      ),
      3 => 
      array (
      ),
    ),
    '/var/www/html/app/Http/Requests/StudentStoreRequest.php' => 
    array (
      0 => '1b69dee2d6928d6bf4ddff2a0cde7b7239c60e7116397e49136f434d1acdd840',
      1 => 
      array (
        0 => 'app\\http\\requests\\studentstorerequest',
      ),
      2 => 
      array (
        0 => 'app\\http\\requests\\rules',
        1 => 'app\\http\\requests\\messages',
        2 => 'app\\http\\requests\\attributes',
      ),
      3 => 
      array (
      ),
    ),
    '/var/www/html/app/Http/Requests/SubjectStoreRequest.php' => 
    array (
      0 => '92de60fc59daf4130c7ed7b91e5934c97ff2421071620e5ac0d61ac55b8c687a',
      1 => 
      array (
        0 => 'app\\http\\requests\\subjectstorerequest',
      ),
      2 => 
      array (
        0 => 'app\\http\\requests\\rules',
      ),
      3 => 
      array (
      ),
    ),
    '/var/www/html/app/Http/Requests/TimetableStoreRequest.php' => 
    array (
      0 => '815a63783cd0d992bec15a32a62d5cc1927a09a081ef5bd662414bdc41e80a04',
      1 => 
      array (
        0 => 'app\\http\\requests\\timetablestorerequest',
      ),
      2 => 
      array (
        0 => 'app\\http\\requests\\rules',
      ),
      3 => 
      array (
      ),
    ),
    '/var/www/html/app/Http/Requests/TimetableUpdateRequest.php' => 
    array (
      0 => '9c4c2d1eaaf601331dc9d7f27145d700c184a36ebca8b20c194eaad9fd0352f6',
      1 => 
      array (
        0 => 'app\\http\\requests\\timetableupdaterequest',
      ),
      2 => 
      array (
        0 => 'app\\http\\requests\\rules',
      ),
      3 => 
      array (
      ),
    ),
    '/var/www/html/app/Http/Requests/UpdateCalendarTemplateRequest.php' => 
    array (
      0 => '7a0a75ecd488fbcbcd424cbd5331e4a79160d1d2e34a815f5d3f3a2a7087896c',
      1 => 
      array (
        0 => 'app\\http\\requests\\updatecalendartemplaterequest',
      ),
      2 => 
      array (
      ),
      3 => 
      array (
      ),
    ),
    '/var/www/html/app/Http/Requests/UpdateClassGroupRequest.php' => 
    array (
      0 => '6a681ae30967ccb6b35d1c74fb1193dde89b7b156c2edc80be13e9e4b1acc924',
      1 => 
      array (
        0 => 'app\\http\\requests\\updateclassgrouprequest',
      ),
      2 => 
      array (
        0 => 'app\\http\\requests\\prepareforvalidation',
        1 => 'app\\http\\requests\\rules',
      ),
      3 => 
      array (
      ),
    ),
    '/var/www/html/app/Http/Requests/UpdateCustomTimetableItemRequest.php' => 
    array (
      0 => '3889b9f202298d90f1c9f1446671ab501e51ca884b17bdca2579b317e2a4c7da',
      1 => 
      array (
        0 => 'app\\http\\requests\\updatecustomtimetableitemrequest',
      ),
      2 => 
      array (
        0 => 'app\\http\\requests\\prepareforvalidation',
        1 => 'app\\http\\requests\\rules',
      ),
      3 => 
      array (
      ),
    ),
    '/var/www/html/app/Http/Requests/UpdateExamRecordRequest.php' => 
    array (
      0 => 'da26920f1e3f5c421edcf766d287aa45d3351985d71bbc17abb17e34051b033f',
      1 => 
      array (
        0 => 'app\\http\\requests\\updateexamrecordrequest',
      ),
      2 => 
      array (
        0 => 'app\\http\\requests\\authorize',
        1 => 'app\\http\\requests\\rules',
      ),
      3 => 
      array (
      ),
    ),
    '/var/www/html/app/Http/Requests/UpdateExamRequest.php' => 
    array (
      0 => '9eeb3554a5b54d51d15ec2b3bd8f94a9d77d7a9eab553ec1cc1a3fec9a32bf21',
      1 => 
      array (
        0 => 'app\\http\\requests\\updateexamrequest',
      ),
      2 => 
      array (
        0 => 'app\\http\\requests\\rules',
      ),
      3 => 
      array (
      ),
    ),
    '/var/www/html/app/Http/Requests/UpdateExamSlotRequest.php' => 
    array (
      0 => '10ec9e90a3ebecd6a0d9091dc4f63f77cb7cd0db13ffdf6ea389e892f200215b',
      1 => 
      array (
        0 => 'app\\http\\requests\\updateexamslotrequest',
      ),
      2 => 
      array (
        0 => 'app\\http\\requests\\rules',
      ),
      3 => 
      array (
      ),
    ),
    '/var/www/html/app/Http/Requests/UpdateExamStatusRequest.php' => 
    array (
      0 => '4b7dca37518c5d1b4a6dd59d58fc59c1adaaf599cc7fdffa9d881ddcaa6e9748',
      1 => 
      array (
        0 => 'app\\http\\requests\\updateexamstatusrequest',
      ),
      2 => 
      array (
        0 => 'app\\http\\requests\\prepareforvalidation',
        1 => 'app\\http\\requests\\rules',
      ),
      3 => 
      array (
      ),
    ),
    '/var/www/html/app/Http/Requests/UpdateFeeCategoryRequest.php' => 
    array (
      0 => '956629c7a4d2b1cf0dbc2f119514ffccfd005dccfd12c1892482d3710184b838',
      1 => 
      array (
        0 => 'app\\http\\requests\\updatefeecategoryrequest',
      ),
      2 => 
      array (
        0 => 'app\\http\\requests\\prepareforvalidation',
        1 => 'app\\http\\requests\\rules',
      ),
      3 => 
      array (
      ),
    ),
    '/var/www/html/app/Http/Requests/UpdateFeeInvoiceRecordRequest.php' => 
    array (
      0 => 'eb5fcb3b69e7358851a86231220c40c56299deb1aa60198ed87209d28507f882',
      1 => 
      array (
        0 => 'app\\http\\requests\\updatefeeinvoicerecordrequest',
      ),
      2 => 
      array (
        0 => 'app\\http\\requests\\rules',
      ),
      3 => 
      array (
      ),
    ),
    '/var/www/html/app/Http/Requests/UpdateFeeInvoiceRequest.php' => 
    array (
      0 => '98681e1152708ea4a3a50ac9a7473da371cab89dcaad72b4b4b151bb46f07f5e',
      1 => 
      array (
        0 => 'app\\http\\requests\\updatefeeinvoicerequest',
      ),
      2 => 
      array (
        0 => 'app\\http\\requests\\rules',
      ),
      3 => 
      array (
      ),
    ),
    '/var/www/html/app/Http/Requests/UpdateFeeRequest.php' => 
    array (
      0 => '21a8aaf8904a4c4713d1917489ac67f980aacafb91df98afe68675ffcb2a5ff3',
      1 => 
      array (
        0 => 'app\\http\\requests\\updatefeerequest',
      ),
      2 => 
      array (
        0 => 'app\\http\\requests\\rules',
      ),
      3 => 
      array (
      ),
    ),
    '/var/www/html/app/Http/Requests/UpdateGradeSystemRequest.php' => 
    array (
      0 => 'a95db9e35ddf91bfd509974dca60c2d17239de0a7a639a07360d2029bb1e0323',
      1 => 
      array (
        0 => 'app\\http\\requests\\updategradesystemrequest',
      ),
      2 => 
      array (
        0 => 'app\\http\\requests\\rules',
      ),
      3 => 
      array (
      ),
    ),
    '/var/www/html/app/Http/Requests/UpdateNoticeRequest.php' => 
    array (
      0 => 'b11d313988735e32796a154ca2bdc954d65c13c24382d843e421a55e2c5ecb54',
      1 => 
      array (
        0 => 'app\\http\\requests\\updatenoticerequest',
      ),
      2 => 
      array (
        0 => 'app\\http\\requests\\authorize',
        1 => 'app\\http\\requests\\rules',
      ),
      3 => 
      array (
      ),
    ),
    '/var/www/html/app/Http/Requests/UpdateSyllabusRequest.php' => 
    array (
      0 => '239c0f22f061609b4ff75d85546fa7866f3e88e631c799321960d05c446871b9',
      1 => 
      array (
        0 => 'app\\http\\requests\\updatesyllabusrequest',
      ),
      2 => 
      array (
        0 => 'app\\http\\requests\\authorize',
        1 => 'app\\http\\requests\\rules',
      ),
      3 => 
      array (
      ),
    ),
    '/var/www/html/app/Http/Requests/UpdateTimetableTimeSlotRequest.php' => 
    array (
      0 => '5081789d92dcf6199e2aa5d0e4bb7b2608d693752d7345b6a1413f182f5b928f',
      1 => 
      array (
        0 => 'app\\http\\requests\\updatetimetabletimeslotrequest',
      ),
      2 => 
      array (
        0 => 'app\\http\\requests\\authorize',
        1 => 'app\\http\\requests\\rules',
      ),
      3 => 
      array (
      ),
    ),
    '/var/www/html/app/Imports/StaffImporter.php' => 
    array (
      0 => 'bef062efd06a7dd0c3f14fb1811090be9f743f622418cf7d7bf0f55fd997f69c',
      1 => 
      array (
        0 => 'app\\imports\\staffimporter',
      ),
      2 => 
      array (
        0 => 'app\\imports\\__construct',
        1 => 'app\\imports\\key',
        2 => 'app\\imports\\title',
        3 => 'app\\imports\\requiredcolumns',
        4 => 'app\\imports\\optionalcolumns',
        5 => 'app\\imports\\rules',
        6 => 'app\\imports\\apply',
        7 => 'app\\imports\\accountfor',
      ),
      3 => 
      array (
      ),
    ),
    '/var/www/html/app/Imports/StudentImporter.php' => 
    array (
      0 => 'd21f088930f5a73c63010167027c39f6b9c78270cd82e2e80c55651d77d747c0',
      1 => 
      array (
        0 => 'app\\imports\\studentimporter',
      ),
      2 => 
      array (
        0 => 'app\\imports\\__construct',
        1 => 'app\\imports\\key',
        2 => 'app\\imports\\title',
        3 => 'app\\imports\\requiredcolumns',
        4 => 'app\\imports\\optionalcolumns',
        5 => 'app\\imports\\rules',
        6 => 'app\\imports\\apply',
        7 => 'app\\imports\\accountfor',
      ),
      3 => 
      array (
      ),
    ),
    '/var/www/html/app/Jobs/BuildReport.php' => 
    array (
      0 => '0f74ef4974b9ca680c263267d8f9e08679dcdf4c90e75633df39ef9d7123a69d',
      1 => 
      array (
        0 => 'app\\jobs\\buildreport',
      ),
      2 => 
      array (
        0 => 'app\\jobs\\__construct',
        1 => 'app\\jobs\\handle',
        2 => 'app\\jobs\\tocsv',
      ),
      3 => 
      array (
      ),
    ),
    '/var/www/html/app/Jobs/SendNoticeEmails.php' => 
    array (
      0 => '2a9f67b32392820c8f81eb8375647b189faec9ca03fb77cb45e1f15f993cf89a',
      1 => 
      array (
        0 => 'app\\jobs\\sendnoticeemails',
      ),
      2 => 
      array (
        0 => 'app\\jobs\\__construct',
        1 => 'app\\jobs\\handle',
      ),
      3 => 
      array (
      ),
    ),
    '/var/www/html/app/Listeners/RecordAccountStatusChange.php' => 
    array (
      0 => 'd9aedd93223795a2837959347ddbeb1bf59946432fab40add433e5078d3d7435',
      1 => 
      array (
        0 => 'app\\listeners\\recordaccountstatuschange',
      ),
      2 => 
      array (
        0 => 'app\\listeners\\__construct',
        1 => 'app\\listeners\\handle',
      ),
      3 => 
      array (
      ),
    ),
    '/var/www/html/app/Listeners/RecordPermissionChanges.php' => 
    array (
      0 => '56b705ed50a6abfe60cc722d56a00d8e7e8137b39bf8d62c60138ab6c4f83b59',
      1 => 
      array (
        0 => 'app\\listeners\\recordpermissionchanges',
      ),
      2 => 
      array (
        0 => 'app\\listeners\\__construct',
        1 => 'app\\listeners\\subscribe',
        2 => 'app\\listeners\\handleroleattached',
        3 => 'app\\listeners\\handleroledetached',
        4 => 'app\\listeners\\handlepermissionattached',
        5 => 'app\\listeners\\handlepermissiondetached',
        6 => 'app\\listeners\\record',
        7 => 'app\\listeners\\names',
      ),
      3 => 
      array (
      ),
    ),
    '/var/www/html/app/Livewire/AcademicPeriodResultTabulation.php' => 
    array (
      0 => '1cf7112d4863fd729868d43b626779a903e8157fff476363b3aef16092933a0f',
      1 => 
      array (
        0 => 'app\\livewire\\academicperiodresulttabulation',
      ),
      2 => 
      array (
        0 => 'app\\livewire\\mount',
        1 => 'app\\livewire\\updatedclass',
        2 => 'app\\livewire\\tabulate',
        3 => 'app\\livewire\\print',
        4 => 'app\\livewire\\render',
      ),
      3 => 
      array (
      ),
    ),
    '/var/www/html/app/Livewire/AcademicYearResultTabulation.php' => 
    array (
      0 => '3050702cb218fc476e83af464268f5aa70bdc197a50c927de2c34601e48c52a2',
      1 => 
      array (
        0 => 'app\\livewire\\academicyearresulttabulation',
      ),
      2 => 
      array (
        0 => 'app\\livewire\\mount',
        1 => 'app\\livewire\\updatedclass',
        2 => 'app\\livewire\\tabulate',
        3 => 'app\\livewire\\print',
        4 => 'app\\livewire\\render',
      ),
      3 => 
      array (
      ),
    ),
    '/var/www/html/app/Livewire/AssignStudentsToParent.php' => 
    array (
      0 => '34d56fd889e336ea3b89e1ba831590b627d7286c1e91e1249dcfa2730c51836b',
      1 => 
      array (
        0 => 'app\\livewire\\assignstudentstoparent',
      ),
      2 => 
      array (
        0 => 'app\\livewire\\mount',
        1 => 'app\\livewire\\updatedclass',
        2 => 'app\\livewire\\updatedsection',
        3 => 'app\\livewire\\render',
      ),
      3 => 
      array (
      ),
    ),
    '/var/www/html/app/Livewire/AssignTeacherToSubjects.php' => 
    array (
      0 => '723b65231776467cd122ae881bfab781cb0a292df5ebd65dc9285abb872ac6e1',
      1 => 
      array (
        0 => 'app\\livewire\\assignteachertosubjects',
      ),
      2 => 
      array (
        0 => 'app\\livewire\\mount',
        1 => 'app\\livewire\\fetchsubjects',
        2 => 'app\\livewire\\render',
      ),
      3 => 
      array (
      ),
    ),
    '/var/www/html/app/Livewire/Auth/AcceptInvitationForm.php' => 
    array (
      0 => '4163d474d083fe658a1331b910d97950555564a4aba63ef47587685d19f6d62e',
      1 => 
      array (
        0 => 'app\\livewire\\auth\\acceptinvitationform',
      ),
      2 => 
      array (
        0 => 'app\\livewire\\auth\\mount',
        1 => 'app\\livewire\\auth\\render',
      ),
      3 => 
      array (
      ),
    ),
    '/var/www/html/app/Livewire/Auth/ConfirmPasswordForm.php' => 
    array (
      0 => '3f8eb7a71a9fcaf850a9f95c8b4601c9bca5da313323d4bbcd1aea1cbd426410',
      1 => 
      array (
        0 => 'app\\livewire\\auth\\confirmpasswordform',
      ),
      2 => 
      array (
        0 => 'app\\livewire\\auth\\render',
      ),
      3 => 
      array (
      ),
    ),
    '/var/www/html/app/Livewire/Auth/ForgotPasswordForm.php' => 
    array (
      0 => '41378fe4e4e3801bd95f59883c1f33900315191b2e8dd6805e95ef1dc8c16af8',
      1 => 
      array (
        0 => 'app\\livewire\\auth\\forgotpasswordform',
      ),
      2 => 
      array (
        0 => 'app\\livewire\\auth\\render',
      ),
      3 => 
      array (
      ),
    ),
    '/var/www/html/app/Livewire/Auth/LoginForm.php' => 
    array (
      0 => 'd444e2ba3d188f2c163fad71e4dcd8738bc0fd8aa6a93f452db0ff8df3799f40',
      1 => 
      array (
        0 => 'app\\livewire\\auth\\loginform',
      ),
      2 => 
      array (
        0 => 'app\\livewire\\auth\\render',
      ),
      3 => 
      array (
      ),
    ),
    '/var/www/html/app/Livewire/Auth/ResetPasswordForm.php' => 
    array (
      0 => 'e663bbb7f1af522d741cbab24bab4792a986feb5c7448c275f07761dbd42da6c',
      1 => 
      array (
        0 => 'app\\livewire\\auth\\resetpasswordform',
      ),
      2 => 
      array (
        0 => 'app\\livewire\\auth\\mount',
        1 => 'app\\livewire\\auth\\render',
      ),
      3 => 
      array (
      ),
    ),
    '/var/www/html/app/Livewire/Auth/TwoFactorChallengeForm.php' => 
    array (
      0 => '14312bfb67a3e5a1dc91e7ef20cb68f1010e030e071f82d49956bc4b623dbe53',
      1 => 
      array (
        0 => 'app\\livewire\\auth\\twofactorchallengeform',
      ),
      2 => 
      array (
        0 => 'app\\livewire\\auth\\render',
      ),
      3 => 
      array (
      ),
    ),
    '/var/www/html/app/Livewire/Auth/VerifyEmailForm.php' => 
    array (
      0 => '868354b0d5a3ada6eb71a9327e125db3cf968e1fb41950eecd9683504be4a692',
      1 => 
      array (
        0 => 'app\\livewire\\auth\\verifyemailform',
      ),
      2 => 
      array (
        0 => 'app\\livewire\\auth\\render',
      ),
      3 => 
      array (
      ),
    ),
    '/var/www/html/app/Livewire/CreateAcademicPeriodForm.php' => 
    array (
      0 => 'c05e5b39b25d1679d9e4a8d8b97dd87bd6eb5f7218c4bb1e5658d791ff2236c4',
      1 => 
      array (
        0 => 'app\\livewire\\createacademicperiodform',
      ),
      2 => 
      array (
        0 => 'app\\livewire\\render',
      ),
      3 => 
      array (
      ),
    ),
    '/var/www/html/app/Livewire/CreateAcademicYearForm.php' => 
    array (
      0 => '7e697f4dbaa771e0b90e40e809120b10ac44bd96c91347f89d59dd5a7b9e0eae',
      1 => 
      array (
        0 => 'app\\livewire\\createacademicyearform',
      ),
      2 => 
      array (
        0 => 'app\\livewire\\render',
      ),
      3 => 
      array (
      ),
    ),
    '/var/www/html/app/Livewire/CreateAdminForm.php' => 
    array (
      0 => '2b523061d0cf934c86a3cb69aff84219e66de3fb26104ebff0714706ff156e80',
      1 => 
      array (
        0 => 'app\\livewire\\createadminform',
      ),
      2 => 
      array (
        0 => 'app\\livewire\\render',
      ),
      3 => 
      array (
      ),
    ),
    '/var/www/html/app/Livewire/CreateClassForm.php' => 
    array (
      0 => '42947d63600468bdee1055bc7f8f641d50cc6f8cf8e5d0b340ff22e6d978105d',
      1 => 
      array (
        0 => 'app\\livewire\\createclassform',
      ),
      2 => 
      array (
        0 => 'app\\livewire\\mount',
        1 => 'app\\livewire\\render',
      ),
      3 => 
      array (
      ),
    ),
    '/var/www/html/app/Livewire/CreateClassGroupForm.php' => 
    array (
      0 => '31ba4150e0780a98a72b610261ee19bf7d5afed1435c1aee41c964a0b42f966a',
      1 => 
      array (
        0 => 'app\\livewire\\createclassgroupform',
      ),
      2 => 
      array (
        0 => 'app\\livewire\\render',
      ),
      3 => 
      array (
      ),
    ),
    '/var/www/html/app/Livewire/CreateCustomTimetableItemForm.php' => 
    array (
      0 => '6603ec6c05a09165b83f35f0e38be412bae498cf2acf260b87b6409305cefc5b',
      1 => 
      array (
        0 => 'app\\livewire\\createcustomtimetableitemform',
      ),
      2 => 
      array (
        0 => 'app\\livewire\\render',
      ),
      3 => 
      array (
      ),
    ),
    '/var/www/html/app/Livewire/CreateExamForm.php' => 
    array (
      0 => 'ca18dcef388b663b3dbb4f3e3046d232c8fbaa50ee7c7c263d17551484bf8434',
      1 => 
      array (
        0 => 'app\\livewire\\createexamform',
      ),
      2 => 
      array (
        0 => 'app\\livewire\\mount',
        1 => 'app\\livewire\\render',
      ),
      3 => 
      array (
      ),
    ),
    '/var/www/html/app/Livewire/CreateExamSlotForm.php' => 
    array (
      0 => 'f1cd5024df75fb58f938fae3a0359df4e941bbdc2979cc33f2f2abca09a60cf3',
      1 => 
      array (
        0 => 'app\\livewire\\createexamslotform',
      ),
      2 => 
      array (
        0 => 'app\\livewire\\render',
      ),
      3 => 
      array (
      ),
    ),
    '/var/www/html/app/Livewire/CreateFeeCategoryForm.php' => 
    array (
      0 => 'ab0fe70b403ad212feb1cd4dc3aeacb4c7843a56ed2ab96f5fe9e6488e51d3ee',
      1 => 
      array (
        0 => 'app\\livewire\\createfeecategoryform',
      ),
      2 => 
      array (
        0 => 'app\\livewire\\render',
      ),
      3 => 
      array (
      ),
    ),
    '/var/www/html/app/Livewire/CreateFeeForm.php' => 
    array (
      0 => '9cea1063ced685a988dea94d1f2833d7305e9ca24d773df385629e4667676e21',
      1 => 
      array (
        0 => 'app\\livewire\\createfeeform',
      ),
      2 => 
      array (
        0 => 'app\\livewire\\mount',
        1 => 'app\\livewire\\render',
      ),
      3 => 
      array (
      ),
    ),
    '/var/www/html/app/Livewire/CreateFeeInvoiceForm.php' => 
    array (
      0 => 'ee827ee9320c8a1e2acd94c0712a068e02b5d3cb4b5ec4dc2e1e8e7eb7c97a8c',
      1 => 
      array (
        0 => 'app\\livewire\\createfeeinvoiceform',
      ),
      2 => 
      array (
        0 => 'app\\livewire\\mount',
        1 => 'app\\livewire\\updatedclass',
        2 => 'app\\livewire\\updatedsection',
        3 => 'app\\livewire\\updatedfeecategory',
        4 => 'app\\livewire\\addfee',
        5 => 'app\\livewire\\addstudent',
        6 => 'app\\livewire\\removestudent',
        7 => 'app\\livewire\\removefee',
        8 => 'app\\livewire\\setoldvalues',
        9 => 'app\\livewire\\render',
      ),
      3 => 
      array (
      ),
    ),
    '/var/www/html/app/Livewire/CreateGradeSystemForm.php' => 
    array (
      0 => '02c2c0a646690a6994d9c6402acc28812c6cbdf559c6379ff71b7b4c72aca739',
      1 => 
      array (
        0 => 'app\\livewire\\creategradesystemform',
      ),
      2 => 
      array (
        0 => 'app\\livewire\\mount',
        1 => 'app\\livewire\\render',
      ),
      3 => 
      array (
      ),
    ),
    '/var/www/html/app/Livewire/CreateNoticeForm.php' => 
    array (
      0 => '22e2b6e40d623943df756aada9c57db39504226b59022a0691e033e7f6f4df7b',
      1 => 
      array (
        0 => 'app\\livewire\\createnoticeform',
      ),
      2 => 
      array (
        0 => 'app\\livewire\\render',
      ),
      3 => 
      array (
      ),
    ),
    '/var/www/html/app/Livewire/CreateParentForm.php' => 
    array (
      0 => '10e54b7f0d31f13dac0da5e2f60ecea5ca997bf2b5194e250242d26dfda55ec8',
      1 => 
      array (
        0 => 'app\\livewire\\createparentform',
      ),
      2 => 
      array (
        0 => 'app\\livewire\\render',
      ),
      3 => 
      array (
      ),
    ),
    '/var/www/html/app/Livewire/CreateSchoolForm.php' => 
    array (
      0 => '73c2f105754a6b61787858fcb4d2e1b546c0111e79a6d478a94608216f9aad02',
      1 => 
      array (
        0 => 'app\\livewire\\createschoolform',
      ),
      2 => 
      array (
        0 => 'app\\livewire\\mount',
        1 => 'app\\livewire\\render',
      ),
      3 => 
      array (
      ),
    ),
    '/var/www/html/app/Livewire/CreateSectionForm.php' => 
    array (
      0 => 'f24a00b0f56f5e8261782bb31e615f166f9ea1a2332d50d4b3fa0d84783a1d2a',
      1 => 
      array (
        0 => 'app\\livewire\\createsectionform',
      ),
      2 => 
      array (
        0 => 'app\\livewire\\mount',
        1 => 'app\\livewire\\render',
      ),
      3 => 
      array (
      ),
    ),
    '/var/www/html/app/Livewire/CreateStudentForm.php' => 
    array (
      0 => 'c6ae31a0e4f016f998171697e8fed23bd8e46bdf907e84fed347e64a35b5c07a',
      1 => 
      array (
        0 => 'app\\livewire\\createstudentform',
      ),
      2 => 
      array (
        0 => 'app\\livewire\\render',
      ),
      3 => 
      array (
      ),
    ),
    '/var/www/html/app/Livewire/CreateStudentRecordFields.php' => 
    array (
      0 => 'f00e1272c5ebb4cc1415dc88c21c8e9af0adee778d6b46b29c65faaa5f495910',
      1 => 
      array (
        0 => 'app\\livewire\\createstudentrecordfields',
      ),
      2 => 
      array (
        0 => 'app\\livewire\\mount',
        1 => 'app\\livewire\\updatedmyclass',
        2 => 'app\\livewire\\render',
      ),
      3 => 
      array (
      ),
    ),
    '/var/www/html/app/Livewire/CreateSubjectForm.php' => 
    array (
      0 => '8371493ff99c748aaf43619e604cd46bf583d6b80791ea2a46be33e3e96f71cd',
      1 => 
      array (
        0 => 'app\\livewire\\createsubjectform',
      ),
      2 => 
      array (
        0 => 'app\\livewire\\mount',
        1 => 'app\\livewire\\render',
      ),
      3 => 
      array (
      ),
    ),
    '/var/www/html/app/Livewire/CreateSyllabusForm.php' => 
    array (
      0 => '5bd890b80dfff51f2c9ba911b58f4c25f8dbc1e65be79f9a92f77383ee6f034c',
      1 => 
      array (
        0 => 'app\\livewire\\createsyllabusform',
      ),
      2 => 
      array (
        0 => 'app\\livewire\\mount',
        1 => 'app\\livewire\\updatedclass',
        2 => 'app\\livewire\\loadinitialsubjects',
        3 => 'app\\livewire\\render',
      ),
      3 => 
      array (
      ),
    ),
    '/var/www/html/app/Livewire/CreateTeacherForm.php' => 
    array (
      0 => 'e0f3b8a424be200b8243fb74899380e73134f1051c72975b3677cc0eda00d687',
      1 => 
      array (
        0 => 'app\\livewire\\createteacherform',
      ),
      2 => 
      array (
        0 => 'app\\livewire\\render',
      ),
      3 => 
      array (
      ),
    ),
    '/var/www/html/app/Livewire/CreateTimetableForm.php' => 
    array (
      0 => '3897f9ffe03c8cf2a78bca68ab72c3f3e8703f2e4ca3705a0067c06c794c7772',
      1 => 
      array (
        0 => 'app\\livewire\\createtimetableform',
      ),
      2 => 
      array (
        0 => 'app\\livewire\\mount',
        1 => 'app\\livewire\\render',
      ),
      3 => 
      array (
      ),
    ),
    '/var/www/html/app/Livewire/CreateUserFields.php' => 
    array (
      0 => '8a10e905c0b10398da29a0e6fbacbdf47644e0127dcc47a430c18dbf29c141bd',
      1 => 
      array (
        0 => 'app\\livewire\\createuserfields',
      ),
      2 => 
      array (
        0 => 'app\\livewire\\render',
      ),
      3 => 
      array (
      ),
    ),
    '/var/www/html/app/Livewire/DashboardDataCards.php' => 
    array (
      0 => '2031ed0fb20e3ac431538cca78adccbc410c17600be52b5730347e1a13674687',
      1 => 
      array (
        0 => 'app\\livewire\\dashboarddatacards',
      ),
      2 => 
      array (
        0 => 'app\\livewire\\mount',
        1 => 'app\\livewire\\render',
      ),
      3 => 
      array (
      ),
    ),
    '/var/www/html/app/Livewire/Datatable.php' => 
    array (
      0 => '629cc11e3bc2b099571e02dcd24dc3d262aa6a5d88e5ba6bd539a39184f7d97d',
      1 => 
      array (
        0 => 'app\\livewire\\datatable',
      ),
      2 => 
      array (
        0 => 'app\\livewire\\mount',
        1 => 'app\\livewire\\verifyismodel',
        2 => 'app\\livewire\\buildpagination',
        3 => 'app\\livewire\\addsearchfilter',
        4 => 'app\\livewire\\encryptvalues',
        5 => 'app\\livewire\\decryptvalues',
        6 => 'app\\livewire\\updatedperpage',
        7 => 'app\\livewire\\updatedsearch',
        8 => 'app\\livewire\\paginationview',
        9 => 'app\\livewire\\render',
      ),
      3 => 
      array (
      ),
    ),
    '/var/www/html/app/Livewire/DisplayStatus.php' => 
    array (
      0 => '52a333c92d41447604151d9630768a07022c4fda827450ad297d83cf2441a824',
      1 => 
      array (
        0 => 'app\\livewire\\displaystatus',
      ),
      2 => 
      array (
        0 => 'app\\livewire\\render',
      ),
      3 => 
      array (
      ),
    ),
    '/var/www/html/app/Livewire/EditAcademicPeriodForm.php' => 
    array (
      0 => '50e578f478ce5454a8e76f3f56aa3843472570a811d276a3e3597e7cc8657972',
      1 => 
      array (
        0 => 'app\\livewire\\editacademicperiodform',
      ),
      2 => 
      array (
        0 => 'app\\livewire\\render',
      ),
      3 => 
      array (
      ),
    ),
    '/var/www/html/app/Livewire/EditAcademicYearForm.php' => 
    array (
      0 => '9e6178371e611f62a47745ba74f6a647155be3cd27f6f1dd97f003060ddbdd7c',
      1 => 
      array (
        0 => 'app\\livewire\\editacademicyearform',
      ),
      2 => 
      array (
        0 => 'app\\livewire\\render',
      ),
      3 => 
      array (
      ),
    ),
    '/var/www/html/app/Livewire/EditAdminForm.php' => 
    array (
      0 => 'b864859d16f03947111b440eb1610edf557e9a0b6e9480f08bd7a74b6a8c62ee',
      1 => 
      array (
        0 => 'app\\livewire\\editadminform',
      ),
      2 => 
      array (
        0 => 'app\\livewire\\render',
      ),
      3 => 
      array (
      ),
    ),
    '/var/www/html/app/Livewire/EditClassForm.php' => 
    array (
      0 => 'e7a8be590ac4298a2620306c8d43a943c3d6969deba4259c93a58bb80c4f87a2',
      1 => 
      array (
        0 => 'app\\livewire\\editclassform',
      ),
      2 => 
      array (
        0 => 'app\\livewire\\mount',
        1 => 'app\\livewire\\render',
      ),
      3 => 
      array (
      ),
    ),
    '/var/www/html/app/Livewire/EditClassGroupForm.php' => 
    array (
      0 => 'de23255dcfa654b6922536b119ce22d8c956252fda94d7855cab20b49038aa1c',
      1 => 
      array (
        0 => 'app\\livewire\\editclassgroupform',
      ),
      2 => 
      array (
        0 => 'app\\livewire\\render',
      ),
      3 => 
      array (
      ),
    ),
    '/var/www/html/app/Livewire/EditCustomTimetableItemForm.php' => 
    array (
      0 => '8189aafab8cad4926ffcf86df52462df0ba891c3f5c983a18709c6259eb654e0',
      1 => 
      array (
        0 => 'app\\livewire\\editcustomtimetableitemform',
      ),
      2 => 
      array (
        0 => 'app\\livewire\\render',
      ),
      3 => 
      array (
      ),
    ),
    '/var/www/html/app/Livewire/EditExamForm.php' => 
    array (
      0 => '18ddd2e05296a85fd5881fa4e7fb7ea4e1b070ccf448172dbd4fbeb0a840f25e',
      1 => 
      array (
        0 => 'app\\livewire\\editexamform',
      ),
      2 => 
      array (
        0 => 'app\\livewire\\mount',
        1 => 'app\\livewire\\render',
      ),
      3 => 
      array (
      ),
    ),
    '/var/www/html/app/Livewire/EditExamSlotForm.php' => 
    array (
      0 => '56838f8f0ec0082b0d0539fda52c5d56c6615b2d3c5ee64b43648516c401b6e6',
      1 => 
      array (
        0 => 'app\\livewire\\editexamslotform',
      ),
      2 => 
      array (
        0 => 'app\\livewire\\render',
      ),
      3 => 
      array (
      ),
    ),
    '/var/www/html/app/Livewire/EditFeeCategoryForm.php' => 
    array (
      0 => 'fa690558a44363eb51f78bc57e1dfd2307fb19f23c53b545f3f6cc543ccdd78a',
      1 => 
      array (
        0 => 'app\\livewire\\editfeecategoryform',
      ),
      2 => 
      array (
        0 => 'app\\livewire\\render',
      ),
      3 => 
      array (
      ),
    ),
    '/var/www/html/app/Livewire/EditFeeForm.php' => 
    array (
      0 => 'cd2c79d5c69ae9f40cff0071a78f8ddb35974beef9aecbbb9a1a64a7cea6ba3c',
      1 => 
      array (
        0 => 'app\\livewire\\editfeeform',
      ),
      2 => 
      array (
        0 => 'app\\livewire\\render',
      ),
      3 => 
      array (
      ),
    ),
    '/var/www/html/app/Livewire/EditFeeInvoiceForm.php' => 
    array (
      0 => 'f2ab74873f58da0778e749c7e7b10756d8ced099fe47b6a3631a127c4b9c6615',
      1 => 
      array (
        0 => 'app\\livewire\\editfeeinvoiceform',
      ),
      2 => 
      array (
        0 => 'app\\livewire\\mount',
        1 => 'app\\livewire\\updatedfeecategory',
        2 => 'app\\livewire\\render',
      ),
      3 => 
      array (
      ),
    ),
    '/var/www/html/app/Livewire/EditGradeSystemForm.php' => 
    array (
      0 => '5781b7962e4b4117327c64dbbbcc9344dd5630d811006aa68b249d08acc618ed',
      1 => 
      array (
        0 => 'app\\livewire\\editgradesystemform',
      ),
      2 => 
      array (
        0 => 'app\\livewire\\mount',
        1 => 'app\\livewire\\render',
      ),
      3 => 
      array (
      ),
    ),
    '/var/www/html/app/Livewire/EditParentForm.php' => 
    array (
      0 => 'b55be2da85192aa7d2853f099a79db180f1e05c94b8a4649be8b8bd7798f6939',
      1 => 
      array (
        0 => 'app\\livewire\\editparentform',
      ),
      2 => 
      array (
        0 => 'app\\livewire\\render',
      ),
      3 => 
      array (
      ),
    ),
    '/var/www/html/app/Livewire/EditSchoolForm.php' => 
    array (
      0 => 'ca6ba02ec70d3d21ad77445114f509c293361d2ad354e86bd1bd68c761c9c1bb',
      1 => 
      array (
        0 => 'app\\livewire\\editschoolform',
      ),
      2 => 
      array (
        0 => 'app\\livewire\\render',
      ),
      3 => 
      array (
      ),
    ),
    '/var/www/html/app/Livewire/EditSectionForm.php' => 
    array (
      0 => 'abdd535a72971d341daab236ecf3716525eeacaab13aa2d877b739c07e4d56ec',
      1 => 
      array (
        0 => 'app\\livewire\\editsectionform',
      ),
      2 => 
      array (
        0 => 'app\\livewire\\render',
      ),
      3 => 
      array (
      ),
    ),
    '/var/www/html/app/Livewire/EditStudentForm.php' => 
    array (
      0 => '7cea9387853c67edb52447c934452088571aa314e52145b3754e732732606223',
      1 => 
      array (
        0 => 'app\\livewire\\editstudentform',
      ),
      2 => 
      array (
        0 => 'app\\livewire\\render',
      ),
      3 => 
      array (
      ),
    ),
    '/var/www/html/app/Livewire/EditSubjectForm.php' => 
    array (
      0 => '1280f05e9ecf7bc565ec7dfcd84a02559d3b09d1f13661e22538434081e9462c',
      1 => 
      array (
        0 => 'app\\livewire\\editsubjectform',
      ),
      2 => 
      array (
        0 => 'app\\livewire\\mount',
        1 => 'app\\livewire\\render',
      ),
      3 => 
      array (
      ),
    ),
    '/var/www/html/app/Livewire/EditTeacherForm.php' => 
    array (
      0 => '2a77eb1844647cad5c49c33b9b3608cf6a92f1ea7be268866e8b2b8ba40cb05d',
      1 => 
      array (
        0 => 'app\\livewire\\editteacherform',
      ),
      2 => 
      array (
        0 => 'app\\livewire\\render',
      ),
      3 => 
      array (
      ),
    ),
    '/var/www/html/app/Livewire/EditTimetableForm.php' => 
    array (
      0 => '2beb4b32d6608969c89141b049a16a1e67de68407771299a2695e4ee6331be8f',
      1 => 
      array (
        0 => 'app\\livewire\\edittimetableform',
      ),
      2 => 
      array (
        0 => 'app\\livewire\\mount',
        1 => 'app\\livewire\\render',
      ),
      3 => 
      array (
      ),
    ),
    '/var/www/html/app/Livewire/EditUserFields.php' => 
    array (
      0 => '955e7a51654f1f5846086cb0ec68120e7ad5dccc473ac8e27e8a0179ba9b4a93',
      1 => 
      array (
        0 => 'app\\livewire\\edituserfields',
      ),
      2 => 
      array (
        0 => 'app\\livewire\\render',
      ),
      3 => 
      array (
      ),
    ),
    '/var/www/html/app/Livewire/ExamTabulation.php' => 
    array (
      0 => 'efd44411835940a1fc60809576269b57397b9987648635b1454dbda18ba852e0',
      1 => 
      array (
        0 => 'app\\livewire\\examtabulation',
      ),
      2 => 
      array (
        0 => 'app\\livewire\\mount',
        1 => 'app\\livewire\\updatedclass',
        2 => 'app\\livewire\\tabulate',
        3 => 'app\\livewire\\print',
        4 => 'app\\livewire\\render',
      ),
      3 => 
      array (
      ),
    ),
    '/var/www/html/app/Livewire/GraduateStudents.php' => 
    array (
      0 => '10fd685e2f9583615731e1ed0bad8e9e1c1c632c65da1fc1c4ab586bd1471a00',
      1 => 
      array (
        0 => 'app\\livewire\\graduatestudents',
      ),
      2 => 
      array (
        0 => 'app\\livewire\\mount',
        1 => 'app\\livewire\\updatedclass',
        2 => 'app\\livewire\\loadinitialsections',
        3 => 'app\\livewire\\loadstudents',
        4 => 'app\\livewire\\render',
      ),
      3 => 
      array (
      ),
    ),
    '/var/www/html/app/Livewire/Layouts/Header.php' => 
    array (
      0 => 'e0a7b61c0ff5ef7b39a54829f16e0f16b1df1d029c9d64abaad34a679cf5dcf1',
      1 => 
      array (
        0 => 'app\\livewire\\layouts\\header',
      ),
      2 => 
      array (
        0 => 'app\\livewire\\layouts\\render',
      ),
      3 => 
      array (
      ),
    ),
    '/var/www/html/app/Livewire/Layouts/Menu.php' => 
    array (
      0 => 'd61f42c7ef8d73a032d3a783d36181e3724fce697bdcd47c7390cb55bb21eabd',
      1 => 
      array (
        0 => 'app\\livewire\\layouts\\menu',
      ),
      2 => 
      array (
        0 => 'app\\livewire\\layouts\\mount',
        1 => 'app\\livewire\\layouts\\render',
        2 => 'app\\livewire\\layouts\\withvisibility',
      ),
      3 => 
      array (
      ),
    ),
    '/var/www/html/app/Livewire/ListAcademicPeriodsTable.php' => 
    array (
      0 => '2f48f1041bdef6ebddd492f1e8a44ec27f643ef42c32cdea08a41b380d684fa4',
      1 => 
      array (
        0 => 'app\\livewire\\listacademicperiodstable',
      ),
      2 => 
      array (
        0 => 'app\\livewire\\render',
      ),
      3 => 
      array (
      ),
    ),
    '/var/www/html/app/Livewire/ListAcademicYearsTable.php' => 
    array (
      0 => '28deb0bdaedcf51002a579050af1242adbe0cc7ce37560ff2037a3e86587c423',
      1 => 
      array (
        0 => 'app\\livewire\\listacademicyearstable',
      ),
      2 => 
      array (
        0 => 'app\\livewire\\mount',
        1 => 'app\\livewire\\render',
      ),
      3 => 
      array (
      ),
    ),
    '/var/www/html/app/Livewire/ListAccountInvitations.php' => 
    array (
      0 => '86ccc0270387763ce337ebb7a76f173b73926fef61b14430f932373ad71f4404',
      1 => 
      array (
        0 => 'app\\livewire\\listaccountinvitations',
      ),
      2 => 
      array (
        0 => 'app\\livewire\\boot',
        1 => 'app\\livewire\\mount',
        2 => 'app\\livewire\\currentstatus',
        3 => 'app\\livewire\\selectstatus',
        4 => 'app\\livewire\\updatingsearch',
        5 => 'app\\livewire\\resend',
        6 => 'app\\livewire\\revoke',
        7 => 'app\\livewire\\render',
        8 => 'app\\livewire\\invitations',
        9 => 'app\\livewire\\counts',
        10 => 'app\\livewire\\visiblequery',
        11 => 'app\\livewire\\rowsfor',
        12 => 'app\\livewire\\unavailablereason',
      ),
      3 => 
      array (
      ),
    ),
    '/var/www/html/app/Livewire/ListAdminsTable.php' => 
    array (
      0 => '8c28dcf122cf14043fbdb3292f278b1adda2ba46b4159d93969d7d294b567edb',
      1 => 
      array (
        0 => 'app\\livewire\\listadminstable',
      ),
      2 => 
      array (
        0 => 'app\\livewire\\mount',
        1 => 'app\\livewire\\render',
      ),
      3 => 
      array (
      ),
    ),
    '/var/www/html/app/Livewire/ListClassGroupsTable.php' => 
    array (
      0 => '7195a632cfd273d9054ab6d4b3c3159a8d51a83268e651b837e4c155945a725e',
      1 => 
      array (
        0 => 'app\\livewire\\listclassgroupstable',
      ),
      2 => 
      array (
        0 => 'app\\livewire\\render',
      ),
      3 => 
      array (
      ),
    ),
    '/var/www/html/app/Livewire/ListClassesTable.php' => 
    array (
      0 => '5f9f38091cf58762b4d332b25c5167188babdfcaa8ceb99ef169e04f12443609',
      1 => 
      array (
        0 => 'app\\livewire\\listclassestable',
      ),
      2 => 
      array (
        0 => 'app\\livewire\\render',
      ),
      3 => 
      array (
      ),
    ),
    '/var/www/html/app/Livewire/ListCustomTimetableItemsTable.php' => 
    array (
      0 => '63b95c4d9837b8a61254a50661dccbf27f6e0d8b6b58637b4711c5cfccf9baf1',
      1 => 
      array (
        0 => 'app\\livewire\\listcustomtimetableitemstable',
      ),
      2 => 
      array (
        0 => 'app\\livewire\\render',
      ),
      3 => 
      array (
      ),
    ),
    '/var/www/html/app/Livewire/ListExamRecordsTable.php' => 
    array (
      0 => '3b32d272f883926fb9ddfee9b59aa0592beb5bb869c82b5417da6941418b2909',
      1 => 
      array (
        0 => 'app\\livewire\\listexamrecordstable',
      ),
      2 => 
      array (
        0 => 'app\\livewire\\mount',
        1 => 'app\\livewire\\updatedclass',
        2 => 'app\\livewire\\fetchexamrecords',
        3 => 'app\\livewire\\render',
      ),
      3 => 
      array (
      ),
    ),
    '/var/www/html/app/Livewire/ListExamSlotsTable.php' => 
    array (
      0 => '3e56b0ac0a8c226dfe6b58e6996e84e161a6702c994a3208e698edb0c6cc1e57',
      1 => 
      array (
        0 => 'app\\livewire\\listexamslotstable',
      ),
      2 => 
      array (
        0 => 'app\\livewire\\render',
      ),
      3 => 
      array (
      ),
    ),
    '/var/www/html/app/Livewire/ListExamsTable.php' => 
    array (
      0 => '6f54c049338affe43af79b5c97619931fcee2884590870823c61e0ff1e4dbb0e',
      1 => 
      array (
        0 => 'app\\livewire\\listexamstable',
      ),
      2 => 
      array (
        0 => 'app\\livewire\\render',
      ),
      3 => 
      array (
      ),
    ),
    '/var/www/html/app/Livewire/ListFeeCategoriesTable.php' => 
    array (
      0 => 'd9f79cda9c2d94400206b18590d2fcb9e7ff87948d6b8cb36653048975572198',
      1 => 
      array (
        0 => 'app\\livewire\\listfeecategoriestable',
      ),
      2 => 
      array (
        0 => 'app\\livewire\\render',
      ),
      3 => 
      array (
      ),
    ),
    '/var/www/html/app/Livewire/ListFeeInvoicesTable.php' => 
    array (
      0 => '695da1d574849b56b0326d532250da0b17c334a7ab9a46d1839c7fc00eba8307',
      1 => 
      array (
        0 => 'app\\livewire\\listfeeinvoicestable',
      ),
      2 => 
      array (
        0 => 'app\\livewire\\mount',
        1 => 'app\\livewire\\updatedstatus',
        2 => 'app\\livewire\\render',
      ),
      3 => 
      array (
      ),
    ),
    '/var/www/html/app/Livewire/ListFeesTable.php' => 
    array (
      0 => 'a7efe7c4d70b6dea8be0450bc1b4c6642245bfbea45e546eefde401784a9cace',
      1 => 
      array (
        0 => 'app\\livewire\\listfeestable',
      ),
      2 => 
      array (
        0 => 'app\\livewire\\render',
      ),
      3 => 
      array (
      ),
    ),
    '/var/www/html/app/Livewire/ListGradeSystemsTable.php' => 
    array (
      0 => '41abf2cd5915dcee0d48a129b61134ed0fdef661c17f83484b0ce087a23c9995',
      1 => 
      array (
        0 => 'app\\livewire\\listgradesystemstable',
      ),
      2 => 
      array (
        0 => 'app\\livewire\\mount',
        1 => 'app\\livewire\\updatedclassgroup',
        2 => 'app\\livewire\\render',
      ),
      3 => 
      array (
      ),
    ),
    '/var/www/html/app/Livewire/ListGraduationsTable.php' => 
    array (
      0 => '6033e05b1f650ba7084f02b3354fb123496bc082a7f6041173bba5e6fb9dd0e5',
      1 => 
      array (
        0 => 'app\\livewire\\listgraduationstable',
      ),
      2 => 
      array (
        0 => 'app\\livewire\\render',
      ),
      3 => 
      array (
      ),
    ),
    '/var/www/html/app/Livewire/ListNoticesTable.php' => 
    array (
      0 => '6d3173be057257b6a9e506bd8fb6e8fc27494e07a726117975c859d1fefae5bf',
      1 => 
      array (
        0 => 'app\\livewire\\listnoticestable',
      ),
      2 => 
      array (
        0 => 'app\\livewire\\render',
      ),
      3 => 
      array (
      ),
    ),
    '/var/www/html/app/Livewire/ListParentsTable.php' => 
    array (
      0 => '95f0a93218e679dd54b392e35ab9ea7d8395ef7ecf2fa9dc460dc4ede9e8bb8e',
      1 => 
      array (
        0 => 'app\\livewire\\listparentstable',
      ),
      2 => 
      array (
        0 => 'app\\livewire\\render',
      ),
      3 => 
      array (
      ),
    ),
    '/var/www/html/app/Livewire/ListPromotionsTable.php' => 
    array (
      0 => '40ba9488e66e77622d33bd25290429da1e7fbb9fd768c456e5464eb15345d417',
      1 => 
      array (
        0 => 'app\\livewire\\listpromotionstable',
      ),
      2 => 
      array (
        0 => 'app\\livewire\\mount',
        1 => 'app\\livewire\\render',
      ),
      3 => 
      array (
      ),
    ),
    '/var/www/html/app/Livewire/ListSchoolsTable.php' => 
    array (
      0 => 'e47212a687eafacc3ab9d18b2524194a408f840c824d6a900417ee37290ebe74',
      1 => 
      array (
        0 => 'app\\livewire\\listschoolstable',
      ),
      2 => 
      array (
        0 => 'app\\livewire\\render',
      ),
      3 => 
      array (
      ),
    ),
    '/var/www/html/app/Livewire/ListSectionsTable.php' => 
    array (
      0 => '87d8ca91ca35e1b0dac769b4ccece4ce80f573c8d1ce4d61374a85e0e9421fcf',
      1 => 
      array (
        0 => 'app\\livewire\\listsectionstable',
      ),
      2 => 
      array (
        0 => 'app\\livewire\\mount',
        1 => 'app\\livewire\\updatedclass',
        2 => 'app\\livewire\\render',
      ),
      3 => 
      array (
      ),
    ),
    '/var/www/html/app/Livewire/ListStudentFeeInvoices.php' => 
    array (
      0 => '090fd06188dcaf1b46e47c8e58bd48af51e328a85305b09a870246472b651368',
      1 => 
      array (
        0 => 'app\\livewire\\liststudentfeeinvoices',
      ),
      2 => 
      array (
        0 => 'app\\livewire\\render',
      ),
      3 => 
      array (
      ),
    ),
    '/var/www/html/app/Livewire/ListStudentsTable.php' => 
    array (
      0 => '1e137681ea8399a9cf080bdfacd47c20c1517ae31a767676eff7bc2b42aff921',
      1 => 
      array (
        0 => 'app\\livewire\\liststudentstable',
      ),
      2 => 
      array (
        0 => 'app\\livewire\\mount',
        1 => 'app\\livewire\\render',
      ),
      3 => 
      array (
      ),
    ),
    '/var/www/html/app/Livewire/ListSubjectsTable.php' => 
    array (
      0 => '2b26b4b76e2661a10bbc9f1657fbe4c828e2f2340b0a9dd903e4002abb244654',
      1 => 
      array (
        0 => 'app\\livewire\\listsubjectstable',
      ),
      2 => 
      array (
        0 => 'app\\livewire\\mount',
        1 => 'app\\livewire\\updatedclass',
        2 => 'app\\livewire\\render',
      ),
      3 => 
      array (
      ),
    ),
    '/var/www/html/app/Livewire/ListSyllabiTable.php' => 
    array (
      0 => 'd8b0d44023581d58109577f391f75c1fd073aa29451558aa3fc366deed66fcfe',
      1 => 
      array (
        0 => 'app\\livewire\\listsyllabitable',
      ),
      2 => 
      array (
        0 => 'app\\livewire\\mount',
        1 => 'app\\livewire\\updatedclass',
        2 => 'app\\livewire\\render',
      ),
      3 => 
      array (
      ),
    ),
    '/var/www/html/app/Livewire/ListTeachersTable.php' => 
    array (
      0 => 'f7fc874e125b73f90858f2ddf4b474a3dec7cc7172c45944197c93b09c4f2d1f',
      1 => 
      array (
        0 => 'app\\livewire\\listteacherstable',
      ),
      2 => 
      array (
        0 => 'app\\livewire\\mount',
        1 => 'app\\livewire\\render',
      ),
      3 => 
      array (
      ),
    ),
    '/var/www/html/app/Livewire/ListTimetablesTable.php' => 
    array (
      0 => 'ac6e74fc41099d4e983d3852466a9e3cf09881f97abff4b9f6a73b3290aec47f',
      1 => 
      array (
        0 => 'app\\livewire\\listtimetablestable',
      ),
      2 => 
      array (
        0 => 'app\\livewire\\mount',
        1 => 'app\\livewire\\updatedclass',
        2 => 'app\\livewire\\render',
      ),
      3 => 
      array (
      ),
    ),
    '/var/www/html/app/Livewire/ManageTimetable.php' => 
    array (
      0 => '87819bfce3637c4b8bfacf33f9d4a03d40543d6936fe101782431c6d2810111b',
      1 => 
      array (
        0 => 'app\\livewire\\managetimetable',
      ),
      2 => 
      array (
        0 => 'app\\livewire\\mount',
        1 => 'app\\livewire\\setselectfields',
        2 => 'app\\livewire\\render',
      ),
      3 => 
      array (
      ),
    ),
    '/var/www/html/app/Livewire/MarkTabulation.php' => 
    array (
      0 => '1cd53d5caf494fed721355ad2a49bd6646d666b0416b2566383b6c50eecbea7c',
      1 => 
      array (
        0 => 'app\\livewire\\marktabulation',
      ),
      2 => 
      array (
        0 => 'app\\livewire\\render',
      ),
      3 => 
      array (
      ),
    ),
    '/var/www/html/app/Livewire/NationalityAndStateInputFields.php' => 
    array (
      0 => '9294e4d498c5357b18cf6ca18bfcc2f319f00d25ac3d6075d3929fb18535650f',
      1 => 
      array (
        0 => 'app\\livewire\\nationalityandstateinputfields',
      ),
      2 => 
      array (
        0 => 'app\\livewire\\mount',
        1 => 'app\\livewire\\updatednationality',
        2 => 'app\\livewire\\loadinitialstates',
        3 => 'app\\livewire\\updatedstate',
        4 => 'app\\livewire\\render',
      ),
      3 => 
      array (
      ),
    ),
    '/var/www/html/app/Livewire/OrganizationDashboard.php' => 
    array (
      0 => 'e6890bb5cb575df4823851d20b54f23101e200681f843f13d93008b1f51fba58',
      1 => 
      array (
        0 => 'app\\livewire\\organizationdashboard',
      ),
      2 => 
      array (
        0 => 'app\\livewire\\mount',
        1 => 'app\\livewire\\loaddashboard',
        2 => 'app\\livewire\\hasrequiredacademicsetup',
        3 => 'app\\livewire\\academicperiodstatus',
        4 => 'app\\livewire\\render',
        5 => 'app\\livewire\\authorizeorganization',
      ),
      3 => 
      array (
      ),
    ),
    '/var/www/html/app/Livewire/OrganizationMembers.php' => 
    array (
      0 => 'b0dbb79102aa135dd98c2552e45afdd55bd470ab1c3955bc53542d3c5131226f',
      1 => 
      array (
        0 => 'app\\livewire\\organizationmembers',
      ),
      2 => 
      array (
        0 => 'app\\livewire\\mount',
        1 => 'app\\livewire\\grant',
        2 => 'app\\livewire\\confirmremoval',
        3 => 'app\\livewire\\cancelremoval',
        4 => 'app\\livewire\\revoke',
        5 => 'app\\livewire\\edit',
        6 => 'app\\livewire\\stopediting',
        7 => 'app\\livewire\\savepermissions',
        8 => 'app\\livewire\\getdelegablepermissionsproperty',
        9 => 'app\\livewire\\render',
        10 => 'app\\livewire\\memberships',
      ),
      3 => 
      array (
      ),
    ),
    '/var/www/html/app/Livewire/PayInvoiceForm.php' => 
    array (
      0 => '236da6bb9fc7b0a1f49feb5ff9403ae5eaf7ebc11de690abe02a594a4e44a413',
      1 => 
      array (
        0 => 'app\\livewire\\payinvoiceform',
      ),
      2 => 
      array (
        0 => 'app\\livewire\\mount',
        1 => 'app\\livewire\\render',
      ),
      3 => 
      array (
      ),
    ),
    '/var/www/html/app/Livewire/PromoteStudents.php' => 
    array (
      0 => '4012dc072d5482da2d929514c54dc157d38d87705d137f912f633250e907024c',
      1 => 
      array (
        0 => 'app\\livewire\\promotestudents',
      ),
      2 => 
      array (
        0 => 'app\\livewire\\mount',
        1 => 'app\\livewire\\updatedoldclass',
        2 => 'app\\livewire\\updatednewclass',
        3 => 'app\\livewire\\loadinitialoldsections',
        4 => 'app\\livewire\\loadinitialnewsections',
        5 => 'app\\livewire\\loadstudents',
        6 => 'app\\livewire\\render',
      ),
      3 => 
      array (
      ),
    ),
    '/var/www/html/app/Livewire/ResultChecker.php' => 
    array (
      0 => '20ea04aec1b82408e5f5fba0f6230f0ffd162013694b75c5f80cdf77a81fe833',
      1 => 
      array (
        0 => 'app\\livewire\\resultchecker',
      ),
      2 => 
      array (
        0 => 'app\\livewire\\mount',
        1 => 'app\\livewire\\updatedacademicyear',
        2 => 'app\\livewire\\updatedclass',
        3 => 'app\\livewire\\updatedsection',
        4 => 'app\\livewire\\checkresult',
        5 => 'app\\livewire\\render',
      ),
      3 => 
      array (
      ),
    ),
    '/var/www/html/app/Livewire/SetAcademicPeriod.php' => 
    array (
      0 => '41791cd0a888a907529c7571f5be1dd2262625e377383bf59573bbd013ff88fb',
      1 => 
      array (
        0 => 'app\\livewire\\setacademicperiod',
      ),
      2 => 
      array (
        0 => 'app\\livewire\\mount',
        1 => 'app\\livewire\\render',
      ),
      3 => 
      array (
      ),
    ),
    '/var/www/html/app/Livewire/SetAcademicYear.php' => 
    array (
      0 => 'b1838f5001b2241c1077e8e25127651e5576a443c15e914fa5e67da327385430',
      1 => 
      array (
        0 => 'app\\livewire\\setacademicyear',
      ),
      2 => 
      array (
        0 => 'app\\livewire\\mount',
        1 => 'app\\livewire\\render',
      ),
      3 => 
      array (
      ),
    ),
    '/var/www/html/app/Livewire/SetSchool.php' => 
    array (
      0 => '97d575513b6caca4d967c0d2da11d75743e7fe9d8114b3050fcd0b5c8e937609',
      1 => 
      array (
        0 => 'app\\livewire\\setschool',
      ),
      2 => 
      array (
        0 => 'app\\livewire\\mount',
        1 => 'app\\livewire\\render',
      ),
      3 => 
      array (
      ),
    ),
    '/var/www/html/app/Livewire/ShowAcademicYear.php' => 
    array (
      0 => '262e5a16401d64ca7795ae78495ab896f9812d446e3a2c55d933e76410cf9317',
      1 => 
      array (
        0 => 'app\\livewire\\showacademicyear',
      ),
      2 => 
      array (
        0 => 'app\\livewire\\mount',
        1 => 'app\\livewire\\render',
      ),
      3 => 
      array (
      ),
    ),
    '/var/www/html/app/Livewire/ShowAdminProfile.php' => 
    array (
      0 => '39a118005188d0cb9403ce4289b984cd70d9f83eee9f56fcd4ae10f5b91c060c',
      1 => 
      array (
        0 => 'app\\livewire\\showadminprofile',
      ),
      2 => 
      array (
        0 => 'app\\livewire\\mount',
        1 => 'app\\livewire\\render',
      ),
      3 => 
      array (
      ),
    ),
    '/var/www/html/app/Livewire/ShowClass.php' => 
    array (
      0 => '6140f2c951d2bb3479ff58ece786c67830061060765c34186d76ea39f171f396',
      1 => 
      array (
        0 => 'app\\livewire\\showclass',
      ),
      2 => 
      array (
        0 => 'app\\livewire\\render',
      ),
      3 => 
      array (
      ),
    ),
    '/var/www/html/app/Livewire/ShowClassGroup.php' => 
    array (
      0 => '02f1bdbfed69b392d7929a58e3fb40c5db2484399d93a0c8d4233de606b04049',
      1 => 
      array (
        0 => 'app\\livewire\\showclassgroup',
      ),
      2 => 
      array (
        0 => 'app\\livewire\\mount',
        1 => 'app\\livewire\\render',
      ),
      3 => 
      array (
      ),
    ),
    '/var/www/html/app/Livewire/ShowFeeInvoice.php' => 
    array (
      0 => 'a93cf1c435ee6d5ddb798a04796e9368a60614efe1628808d0e652186aec0237',
      1 => 
      array (
        0 => 'app\\livewire\\showfeeinvoice',
      ),
      2 => 
      array (
        0 => 'app\\livewire\\mount',
        1 => 'app\\livewire\\render',
      ),
      3 => 
      array (
      ),
    ),
    '/var/www/html/app/Livewire/ShowNotice.php' => 
    array (
      0 => '88c321593ff67f94a27cd92fbdc02d646f1429a71d0c639fec1a96b645a12af7',
      1 => 
      array (
        0 => 'app\\livewire\\shownotice',
      ),
      2 => 
      array (
        0 => 'app\\livewire\\render',
      ),
      3 => 
      array (
      ),
    ),
    '/var/www/html/app/Livewire/ShowParentProfile.php' => 
    array (
      0 => 'a4da82bb32ae4bf63f7c4caa234e6e660e0ac4b5b8c439ec867af565bf89fd2d',
      1 => 
      array (
        0 => 'app\\livewire\\showparentprofile',
      ),
      2 => 
      array (
        0 => 'app\\livewire\\render',
      ),
      3 => 
      array (
      ),
    ),
    '/var/www/html/app/Livewire/ShowPromotion.php' => 
    array (
      0 => '1c24f0cd0cc97d91649d2beaab0622a62f65ab3e03d653f9e849c8c8c1fe876a',
      1 => 
      array (
        0 => 'app\\livewire\\showpromotion',
      ),
      2 => 
      array (
        0 => 'app\\livewire\\mount',
        1 => 'app\\livewire\\render',
      ),
      3 => 
      array (
      ),
    ),
    '/var/www/html/app/Livewire/ShowSchool.php' => 
    array (
      0 => '4cf270b9316ac1569b62d3fa650f8ef883abd5f884137cd926ed3bef2a2c6ca0',
      1 => 
      array (
        0 => 'app\\livewire\\showschool',
      ),
      2 => 
      array (
        0 => 'app\\livewire\\render',
      ),
      3 => 
      array (
      ),
    ),
    '/var/www/html/app/Livewire/ShowSection.php' => 
    array (
      0 => '9f72519803a29e49a09d365808c98ac9e62e688df4ab634dd8f41588afe5a998',
      1 => 
      array (
        0 => 'app\\livewire\\showsection',
      ),
      2 => 
      array (
        0 => 'app\\livewire\\mount',
        1 => 'app\\livewire\\render',
      ),
      3 => 
      array (
      ),
    ),
    '/var/www/html/app/Livewire/ShowStudentProfile.php' => 
    array (
      0 => 'b665ff65036c1276cb424314a4c19e3d2b5c398c7a17c37fd29f21110c0a469f',
      1 => 
      array (
        0 => 'app\\livewire\\showstudentprofile',
      ),
      2 => 
      array (
        0 => 'app\\livewire\\mount',
        1 => 'app\\livewire\\updatedplacementclassid',
        2 => 'app\\livewire\\changestatus',
        3 => 'app\\livewire\\changeplacement',
        4 => 'app\\livewire\\render',
        5 => 'app\\livewire\\refreshenrollment',
        6 => 'app\\livewire\\loadclasses',
        7 => 'app\\livewire\\classsections',
      ),
      3 => 
      array (
      ),
    ),
    '/var/www/html/app/Livewire/ShowSyllabus.php' => 
    array (
      0 => '8814fcfc83fa95c5baf60486d3dd6af3d8b9b22a1beb6ca1bd95e03afc2e6f47',
      1 => 
      array (
        0 => 'app\\livewire\\showsyllabus',
      ),
      2 => 
      array (
        0 => 'app\\livewire\\render',
      ),
      3 => 
      array (
      ),
    ),
    '/var/www/html/app/Livewire/ShowTeacherProfile.php' => 
    array (
      0 => '0035c76438c55eb107a917dbd43ea897a0263b3131d1da64584064d1de218ac2',
      1 => 
      array (
        0 => 'app\\livewire\\showteacherprofile',
      ),
      2 => 
      array (
        0 => 'app\\livewire\\render',
      ),
      3 => 
      array (
      ),
    ),
    '/var/www/html/app/Livewire/ShowTimetable.php' => 
    array (
      0 => '6a331286d1959127eb33097d3883b37a05782a7c15df8977d13606c33e792f64',
      1 => 
      array (
        0 => 'app\\livewire\\showtimetable',
      ),
      2 => 
      array (
        0 => 'app\\livewire\\mount',
        1 => 'app\\livewire\\emitcellinformationdetail',
        2 => 'app\\livewire\\render',
      ),
      3 => 
      array (
      ),
    ),
    '/var/www/html/app/Livewire/ShowUserProfile.php' => 
    array (
      0 => '38213f1cabb0335862005cff8dbc3d049cbf4aa81afc5c6f422a507efa17c82b',
      1 => 
      array (
        0 => 'app\\livewire\\showuserprofile',
      ),
      2 => 
      array (
        0 => 'app\\livewire\\render',
      ),
      3 => 
      array (
      ),
    ),
    '/var/www/html/app/Models/AcademicCycleSection.php' => 
    array (
      0 => 'ee4d9f2e42f49cefa00c12af57e2c58ece3ab0be3549007badb95a49dbb5059e',
      1 => 
      array (
        0 => 'app\\models\\academiccyclesection',
      ),
      2 => 
      array (
        0 => 'app\\models\\school',
        1 => 'app\\models\\academicyear',
        2 => 'app\\models\\academiclevel',
        3 => 'app\\models\\legacysection',
        4 => 'app\\models\\homeroomteacher',
      ),
      3 => 
      array (
      ),
    ),
    '/var/www/html/app/Models/AcademicLevel.php' => 
    array (
      0 => 'e131ecf8a8765944ed509131cf4ac332a8ebbc2874eb3e8071038d0ba978b87d',
      1 => 
      array (
        0 => 'app\\models\\academiclevel',
      ),
      2 => 
      array (
        0 => 'app\\models\\school',
        1 => 'app\\models\\legacymyclass',
        2 => 'app\\models\\parent',
        3 => 'app\\models\\children',
        4 => 'app\\models\\cyclesections',
      ),
      3 => 
      array (
      ),
    ),
    '/var/www/html/app/Models/AcademicPeriod.php' => 
    array (
      0 => '1c7d3dabcca8c7bcc95325d1a953591c1c5fd5efbe6bf2c3192d837e55aa2d58',
      1 => 
      array (
        0 => 'app\\models\\academicperiod',
      ),
      2 => 
      array (
        0 => 'app\\models\\booted',
        1 => 'app\\models\\scopetoplevel',
        2 => 'app\\models\\scopeoftype',
        3 => 'app\\models\\scopecovering',
        4 => 'app\\models\\scopeordered',
        5 => 'app\\models\\covers',
        6 => 'app\\models\\typelabel',
        7 => 'app\\models\\displayname',
        8 => 'app\\models\\lengthindays',
        9 => 'app\\models\\isteachingperiod',
        10 => 'app\\models\\parent',
        11 => 'app\\models\\children',
        12 => 'app\\models\\academicyear',
        13 => 'app\\models\\school',
        14 => 'app\\models\\exams',
        15 => 'app\\models\\examslots',
      ),
      3 => 
      array (
      ),
    ),
    '/var/www/html/app/Models/AcademicPeriodStatusChange.php' => 
    array (
      0 => 'c227c900b8cde0dcaf28fd2bd720c835e1879812b451e3d2418311793421d423',
      1 => 
      array (
        0 => 'app\\models\\academicperiodstatuschange',
      ),
      2 => 
      array (
        0 => 'app\\models\\booted',
        1 => 'app\\models\\period',
        2 => 'app\\models\\changedby',
      ),
      3 => 
      array (
      ),
    ),
    '/var/www/html/app/Models/AcademicYear.php' => 
    array (
      0 => '47e532990f145d12dfe26d66ad60eb893f7c7488533d829d087ee2f20affe02a',
      1 => 
      array (
        0 => 'app\\models\\academicyear',
      ),
      2 => 
      array (
        0 => 'app\\models\\name',
        1 => 'app\\models\\school',
        2 => 'app\\models\\academicperiods',
        3 => 'app\\models\\toplevelperiods',
        4 => 'app\\models\\periodfordate',
        5 => 'app\\models\\subperiodfordate',
        6 => 'app\\models\\exams',
        7 => 'app\\models\\studentrecords',
        8 => 'app\\models\\cyclesections',
      ),
      3 => 
      array (
      ),
    ),
    '/var/www/html/app/Models/AcademicYearStudentRecord.php' => 
    array (
      0 => 'd8ae00d7160b2414a0455cf2df45db6a4b0242eab94a91f615df0e1c3ef2a254',
      1 => 
      array (
        0 => 'app\\models\\academicyearstudentrecord',
      ),
      2 => 
      array (
        0 => 'app\\models\\studentrecord',
        1 => 'app\\models\\class',
        2 => 'app\\models\\section',
      ),
      3 => 
      array (
      ),
    ),
    '/var/www/html/app/Models/AccountInvitation.php' => 
    array (
      0 => '2215131daa61c88392124340d563ab2a07848654c071ba9e612bc5ea59880ef5',
      1 => 
      array (
        0 => 'app\\models\\accountinvitation',
      ),
      2 => 
      array (
        0 => 'app\\models\\casts',
        1 => 'app\\models\\user',
        2 => 'app\\models\\invitedby',
        3 => 'app\\models\\scopepending',
        4 => 'app\\models\\scopeaccepted',
        5 => 'app\\models\\scoperevoked',
        6 => 'app\\models\\scopeexpired',
        7 => 'app\\models\\scopewithstatus',
        8 => 'app\\models\\hashtoken',
        9 => 'app\\models\\ispending',
        10 => 'app\\models\\isexpired',
        11 => 'app\\models\\status',
        12 => 'app\\models\\expiresat',
      ),
      3 => 
      array (
      ),
    ),
    '/var/www/html/app/Models/AttendanceChange.php' => 
    array (
      0 => 'e022312d0f17ccd179560a3b185e9973c28b1f79b061e26ce9ca3a156afea95e',
      1 => 
      array (
        0 => 'app\\models\\attendancechange',
      ),
      2 => 
      array (
        0 => 'app\\models\\booted',
        1 => 'app\\models\\attendancerecord',
        2 => 'app\\models\\changedby',
      ),
      3 => 
      array (
      ),
    ),
    '/var/www/html/app/Models/AttendanceRecord.php' => 
    array (
      0 => '6ba6bf1e37fc78bdd4a2ddfa2b7d7401372457d9f48d9f6452649dbe6948ecfc',
      1 => 
      array (
        0 => 'app\\models\\attendancerecord',
      ),
      2 => 
      array (
        0 => 'app\\models\\scopeondate',
        1 => 'app\\models\\scopeofkind',
        2 => 'app\\models\\studentrecord',
        3 => 'app\\models\\subject',
        4 => 'app\\models\\recordedby',
        5 => 'app\\models\\changes',
      ),
      3 => 
      array (
      ),
    ),
    '/var/www/html/app/Models/AuditEvent.php' => 
    array (
      0 => 'a992862ef209acd66d14711782cb0dc162fba2f85a4f7952548b5b228c21298e',
      1 => 
      array (
        0 => 'app\\models\\auditevent',
      ),
      2 => 
      array (
        0 => 'app\\models\\booted',
        1 => 'app\\models\\actor',
        2 => 'app\\models\\subject',
        3 => 'app\\models\\school',
        4 => 'app\\models\\scopeofaction',
        5 => 'app\\models\\scopeforsubject',
      ),
      3 => 
      array (
      ),
    ),
    '/var/www/html/app/Models/CalendarEvent.php' => 
    array (
      0 => '8c8e5d9bc660df5511fca00198d02009fb5f6bef3961f41fde6311924bf7f80f',
      1 => 
      array (
        0 => 'app\\models\\calendarevent',
      ),
      2 => 
      array (
        0 => 'app\\models\\scopecovering',
        1 => 'app\\models\\scopebetween',
        2 => 'app\\models\\scopepublished',
        3 => 'app\\models\\audiences',
        4 => 'app\\models\\createdby',
        5 => 'app\\models\\isteachingday',
        6 => 'app\\models\\isforeverybody',
      ),
      3 => 
      array (
      ),
    ),
    '/var/www/html/app/Models/CalendarEventAudience.php' => 
    array (
      0 => '0b7a254e86fc0c8a82d591cf248abbaefe6de11be247f26da900be0ae748b984',
      1 => 
      array (
        0 => 'app\\models\\calendareventaudience',
      ),
      2 => 
      array (
        0 => 'app\\models\\calendarevent',
        1 => 'app\\models\\myclass',
        2 => 'app\\models\\section',
        3 => 'app\\models\\user',
      ),
      3 => 
      array (
      ),
    ),
    '/var/www/html/app/Models/CalendarTemplate.php' => 
    array (
      0 => '0315c9661d29b3ffa705bbf10cce4a0076d558c8d4a3c6cb4b2c8511ea8d9b7c',
      1 => 
      array (
        0 => 'app\\models\\calendartemplate',
      ),
      2 => 
      array (
        0 => 'app\\models\\generatesahead',
        1 => 'app\\models\\organization',
        2 => 'app\\models\\periods',
        3 => 'app\\models\\toplevelperiods',
        4 => 'app\\models\\createdby',
        5 => 'app\\models\\schools',
      ),
      3 => 
      array (
      ),
    ),
    '/var/www/html/app/Models/CalendarTemplatePeriod.php' => 
    array (
      0 => 'c51499a0562c0b5d28bcd15ee047af6f9f9affc8cd1ed73f2f49b72e8982fd56',
      1 => 
      array (
        0 => 'app\\models\\calendartemplateperiod',
      ),
      2 => 
      array (
        0 => 'app\\models\\scopeordered',
        1 => 'app\\models\\calendartemplate',
        2 => 'app\\models\\parent',
        3 => 'app\\models\\children',
        4 => 'app\\models\\startson',
        5 => 'app\\models\\endson',
      ),
      3 => 
      array (
      ),
    ),
    '/var/www/html/app/Models/ClassGroup.php' => 
    array (
      0 => 'a4820865a8d7edd37277e13e04fc148f018a4c1a0f8db6e5ff3fbb18bc654417',
      1 => 
      array (
        0 => 'app\\models\\classgroup',
      ),
      2 => 
      array (
        0 => 'app\\models\\school',
        1 => 'app\\models\\classes',
        2 => 'app\\models\\gradesystem',
      ),
      3 => 
      array (
      ),
    ),
    '/var/www/html/app/Models/Cohort.php' => 
    array (
      0 => 'b25001713cb5de909157c4872ee26029b87e80ecc1d9bf6e6a74f993adbc6248',
      1 => 
      array (
        0 => 'app\\models\\cohort',
      ),
      2 => 
      array (
        0 => 'app\\models\\booted',
        1 => 'app\\models\\scopeactive',
        2 => 'app\\models\\members',
        3 => 'app\\models\\studentrecords',
      ),
      3 => 
      array (
      ),
    ),
    '/var/www/html/app/Models/CohortMember.php' => 
    array (
      0 => '9c0bfd95de2dc0f04bff33de4a2c2ce82414bad1950f584560e5519a55cc1570',
      1 => 
      array (
        0 => 'app\\models\\cohortmember',
      ),
      2 => 
      array (
        0 => 'app\\models\\isheldon',
        1 => 'app\\models\\scopecurrent',
        2 => 'app\\models\\cohort',
        3 => 'app\\models\\studentrecord',
        4 => 'app\\models\\user',
      ),
      3 => 
      array (
      ),
    ),
    '/var/www/html/app/Models/CourseOffering.php' => 
    array (
      0 => '6592e16aac302fa10893d67cc8a494ac26e3b3b6f7bb0068f911390cbba683d8',
      1 => 
      array (
        0 => 'app\\models\\courseoffering',
      ),
      2 => 
      array (
        0 => 'app\\models\\activekeyforroster',
        1 => 'app\\models\\school',
        2 => 'app\\models\\academicyear',
        3 => 'app\\models\\academicperiod',
        4 => 'app\\models\\subject',
        5 => 'app\\models\\academiclevel',
        6 => 'app\\models\\sections',
        7 => 'app\\models\\studentrecords',
        8 => 'app\\models\\teachingassignments',
      ),
      3 => 
      array (
      ),
    ),
    '/var/www/html/app/Models/CustomTimetableItem.php' => 
    array (
      0 => '99e177577a122205318daff01b1d82ada5b63364ced28a31c935e1392627ad73',
      1 => 
      array (
        0 => 'app\\models\\customtimetableitem',
      ),
      2 => 
      array (
        0 => 'app\\models\\school',
        1 => 'app\\models\\timetablerecord',
      ),
      3 => 
      array (
      ),
    ),
    '/var/www/html/app/Models/DataSharingRequest.php' => 
    array (
      0 => 'f6bffe6a2c4e512286dcc2eccc65ad0a45f7262ab39581695e8c81212a93e960',
      1 => 
      array (
        0 => 'app\\models\\datasharingrequest',
      ),
      2 => 
      array (
        0 => 'app\\models\\categories',
        1 => 'app\\models\\hasexpired',
        2 => 'app\\models\\isusable',
        3 => 'app\\models\\scopeawaiting',
        4 => 'app\\models\\studentrecord',
        5 => 'app\\models\\requestingschool',
        6 => 'app\\models\\holdingschool',
      ),
      3 => 
      array (
      ),
    ),
    '/var/www/html/app/Models/EnrollmentPlacement.php' => 
    array (
      0 => 'cd99b7dd689fe125befdc6829ff60a6b837277395d32ffe9f15a5a6f6322cb4a',
      1 => 
      array (
        0 => 'app\\models\\enrollmentplacement',
      ),
      2 => 
      array (
        0 => 'app\\models\\booted',
        1 => 'app\\models\\studentrecord',
        2 => 'app\\models\\academicyear',
        3 => 'app\\models\\academicperiod',
        4 => 'app\\models\\myclass',
        5 => 'app\\models\\section',
        6 => 'app\\models\\changedby',
      ),
      3 => 
      array (
      ),
    ),
    '/var/www/html/app/Models/EnrollmentStatusChange.php' => 
    array (
      0 => 'c3d790c1a8be530b6d7c7c8cd2be6db97f3199e92f7122c661722cf6a3041f5a',
      1 => 
      array (
        0 => 'app\\models\\enrollmentstatuschange',
      ),
      2 => 
      array (
        0 => 'app\\models\\booted',
        1 => 'app\\models\\studentrecord',
        2 => 'app\\models\\changedby',
      ),
      3 => 
      array (
      ),
    ),
    '/var/www/html/app/Models/Exam.php' => 
    array (
      0 => '6aafe2a7043944f6b25592276a62c4f0854c29b8bc8bd3dcabad75f2c55c1c7d',
      1 => 
      array (
        0 => 'app\\models\\exam',
      ),
      2 => 
      array (
        0 => 'app\\models\\academicperiod',
        1 => 'app\\models\\examslots',
        2 => 'app\\models\\gettotalattainablemarksinasubjectattribute',
        3 => 'app\\models\\calculatestudenttotalmarkinsubjectforacademicperiod',
      ),
      3 => 
      array (
      ),
    ),
    '/var/www/html/app/Models/ExamRecord.php' => 
    array (
      0 => '4993b16fe211f18aeb3d6abbd75b2a4368efaa0882d224023fa140a80c9e8d2a',
      1 => 
      array (
        0 => 'app\\models\\examrecord',
      ),
      2 => 
      array (
        0 => 'app\\models\\subject',
        1 => 'app\\models\\examslot',
        2 => 'app\\models\\governingacademicperiod',
        3 => 'app\\models\\scopeinsubject',
        4 => 'app\\models\\scopeinsection',
      ),
      3 => 
      array (
      ),
    ),
    '/var/www/html/app/Models/ExamSlot.php' => 
    array (
      0 => 'b1e04cdc43cf2d400d17f60b065ba68437ba24999fd8a12861759206f15e3d93',
      1 => 
      array (
        0 => 'app\\models\\examslot',
      ),
      2 => 
      array (
        0 => 'app\\models\\exam',
        1 => 'app\\models\\governingacademicperiod',
        2 => 'app\\models\\examrecords',
      ),
      3 => 
      array (
      ),
    ),
    '/var/www/html/app/Models/FeatureSetting.php' => 
    array (
      0 => 'd0b54232e7b8ccf7a9b7fac4d1e91dae54b6a28b87550de9d4c981a8458019bd',
      1 => 
      array (
        0 => 'app\\models\\featuresetting',
      ),
      2 => 
      array (
        0 => 'app\\models\\school',
      ),
      3 => 
      array (
      ),
    ),
    '/var/www/html/app/Models/Fee.php' => 
    array (
      0 => 'e4d6deb9e05aae3bef2c9000a196d97281b4be3abf670dfcba188dd428cdd926',
      1 => 
      array (
        0 => 'app\\models\\fee',
      ),
      2 => 
      array (
        0 => 'app\\models\\feecategory',
        1 => 'app\\models\\school',
      ),
      3 => 
      array (
      ),
    ),
    '/var/www/html/app/Models/FeeCategory.php' => 
    array (
      0 => '5c49085bfb5d43232f0b3cbc4bcaa6fbdb5a847d5c3728e1bb37f4fe554dcd3a',
      1 => 
      array (
        0 => 'app\\models\\feecategory',
      ),
      2 => 
      array (
        0 => 'app\\models\\school',
        1 => 'app\\models\\fees',
      ),
      3 => 
      array (
      ),
    ),
    '/var/www/html/app/Models/FeeInvoice.php' => 
    array (
      0 => '882eada070442134b308f6b99e6050db6e13ba65c650d194f1f0a3184acb05f0',
      1 => 
      array (
        0 => 'app\\models\\feeinvoice',
      ),
      2 => 
      array (
        0 => 'app\\models\\user',
        1 => 'app\\models\\feeinvoicerecords',
        2 => 'app\\models\\scopeofschool',
        3 => 'app\\models\\scopeisdue',
        4 => 'app\\models\\scopeispaid',
        5 => 'app\\models\\getsumoffieldfromrecords',
        6 => 'app\\models\\getamountattribute',
        7 => 'app\\models\\getpaidattribute',
        8 => 'app\\models\\getwaiverattribute',
        9 => 'app\\models\\getfineattribute',
        10 => 'app\\models\\getbalanceattribute',
      ),
      3 => 
      array (
      ),
    ),
    '/var/www/html/app/Models/FeeInvoiceRecord.php' => 
    array (
      0 => '960ce49c75cd0621abe71dea1907fa559dd82b5eb60fdd893f2bf90c47b52526',
      1 => 
      array (
        0 => 'app\\models\\feeinvoicerecord',
      ),
      2 => 
      array (
        0 => 'app\\models\\scopeisdue',
        1 => 'app\\models\\scopeispaid',
        2 => 'app\\models\\fee',
        3 => 'app\\models\\feeinvoice',
      ),
      3 => 
      array (
      ),
    ),
    '/var/www/html/app/Models/GradeCategory.php' => 
    array (
      0 => 'f54d23c432643ca0dc2543489475f9882d8f162bff07250c3febd1d6fe39b991',
      1 => 
      array (
        0 => 'app\\models\\gradecategory',
      ),
      2 => 
      array (
        0 => 'app\\models\\subject',
        1 => 'app\\models\\parent',
        2 => 'app\\models\\children',
        3 => 'app\\models\\items',
      ),
      3 => 
      array (
      ),
    ),
    '/var/www/html/app/Models/GradeEntry.php' => 
    array (
      0 => '3e83b2fd90ae9986153fc15b8b3fa71b3da5f0821b455764cc516b019c3833da',
      1 => 
      array (
        0 => 'app\\models\\gradeentry',
      ),
      2 => 
      array (
        0 => 'app\\models\\gradeitem',
        1 => 'app\\models\\studentrecord',
        2 => 'app\\models\\gradedby',
      ),
      3 => 
      array (
      ),
    ),
    '/var/www/html/app/Models/GradeItem.php' => 
    array (
      0 => 'a1c02eb4701d2f97887d6101525e43f785b2c12db388c966981a9986f4435e87',
      1 => 
      array (
        0 => 'app\\models\\gradeitem',
      ),
      2 => 
      array (
        0 => 'app\\models\\scopeforsubject',
        1 => 'app\\models\\subject',
        2 => 'app\\models\\category',
        3 => 'app\\models\\academicyear',
        4 => 'app\\models\\academicperiod',
        5 => 'app\\models\\entries',
      ),
      3 => 
      array (
      ),
    ),
    '/var/www/html/app/Models/GradeSystem.php' => 
    array (
      0 => '0696da2c38a45176ab47791e7551e63023e8e92142ee47c6456a84a9dd99a9c5',
      1 => 
      array (
        0 => 'app\\models\\gradesystem',
      ),
      2 => 
      array (
        0 => 'app\\models\\classgroup',
      ),
      3 => 
      array (
      ),
    ),
    '/var/www/html/app/Models/Graduation.php' => 
    array (
      0 => 'af4f957c9ffd2fb57ef27f5b5d891fba6933a3693b5cf264c20a9eecc53676c1',
      1 => 
      array (
        0 => 'app\\models\\graduation',
      ),
      2 => 
      array (
      ),
      3 => 
      array (
      ),
    ),
    '/var/www/html/app/Models/GraduationExemption.php' => 
    array (
      0 => '3d1fc33dbaac3e049760c94c84bebef6e7e9d19035b804987ab40a09440e641a',
      1 => 
      array (
        0 => 'app\\models\\graduationexemption',
      ),
      2 => 
      array (
        0 => 'app\\models\\graduationrequirement',
        1 => 'app\\models\\studentrecord',
      ),
      3 => 
      array (
      ),
    ),
    '/var/www/html/app/Models/GraduationPlan.php' => 
    array (
      0 => 'e8fae0c47c11be21e65f37b83376138e82f1797ca0288de067cd7294feb0fef7',
      1 => 
      array (
        0 => 'app\\models\\graduationplan',
      ),
      2 => 
      array (
        0 => 'app\\models\\scopeactive',
        1 => 'app\\models\\requirements',
        2 => 'app\\models\\cohort',
      ),
      3 => 
      array (
      ),
    ),
    '/var/www/html/app/Models/GraduationRequirement.php' => 
    array (
      0 => '05631270aaab9c1816cec1bde2576c2edf81b5ada67d80cbe0fcdb641e2c2dba',
      1 => 
      array (
        0 => 'app\\models\\graduationrequirement',
      ),
      2 => 
      array (
        0 => 'app\\models\\graduationplan',
        1 => 'app\\models\\subject',
        2 => 'app\\models\\exemptions',
      ),
      3 => 
      array (
      ),
    ),
    '/var/www/html/app/Models/ImportBatch.php' => 
    array (
      0 => '61166373ca29fda0f97a2f6259553a0e16af8df6ae0070f396ff26d9f0d4ab43',
      1 => 
      array (
        0 => 'app\\models\\importbatch',
      ),
      2 => 
      array (
        0 => 'app\\models\\rows',
        1 => 'app\\models\\createdby',
      ),
      3 => 
      array (
      ),
    ),
    '/var/www/html/app/Models/ImportRow.php' => 
    array (
      0 => '8f058b2393ebd9c96b2d11c793f22a4a97993546ebb0d2cb36f89cb9a365c799',
      1 => 
      array (
        0 => 'app\\models\\importrow',
      ),
      2 => 
      array (
        0 => 'app\\models\\scopeready',
        1 => 'app\\models\\scopebroken',
        2 => 'app\\models\\importbatch',
        3 => 'app\\models\\subject',
      ),
      3 => 
      array (
      ),
    ),
    '/var/www/html/app/Models/ImportedRecord.php' => 
    array (
      0 => '6427777675b9517bfa055afb058bbd713061ad3cec6ab414aaff0292f216fd56',
      1 => 
      array (
        0 => 'app\\models\\importedrecord',
      ),
      2 => 
      array (
        0 => 'app\\models\\subject',
      ),
      3 => 
      array (
      ),
    ),
    '/var/www/html/app/Models/Incident.php' => 
    array (
      0 => '24a1b437a089b69cc2ee6b35ac1dc67c30aea8a08e8876921b75668f07ee3b10',
      1 => 
      array (
        0 => 'app\\models\\incident',
      ),
      2 => 
      array (
        0 => 'app\\models\\booted',
        1 => 'app\\models\\scopereadableby',
        2 => 'app\\models\\scopeopen',
        3 => 'app\\models\\participants',
        4 => 'app\\models\\actions',
        5 => 'app\\models\\statuschanges',
        6 => 'app\\models\\reportedby',
        7 => 'app\\models\\assignedto',
      ),
      3 => 
      array (
      ),
    ),
    '/var/www/html/app/Models/IncidentAction.php' => 
    array (
      0 => 'e5d24e69c3eace0434947522a542282bdbc4941f1199695d549b95ea8a826273',
      1 => 
      array (
        0 => 'app\\models\\incidentaction',
      ),
      2 => 
      array (
        0 => 'app\\models\\incident',
        1 => 'app\\models\\assignedto',
        2 => 'app\\models\\isoutstanding',
        3 => 'app\\models\\complete',
      ),
      3 => 
      array (
      ),
    ),
    '/var/www/html/app/Models/IncidentParticipant.php' => 
    array (
      0 => '5ade25e2a26b3859976de7a1af9915f2615441d917f768bb7b7e6d5d6925bf2e',
      1 => 
      array (
        0 => 'app\\models\\incidentparticipant',
      ),
      2 => 
      array (
        0 => 'app\\models\\incident',
        1 => 'app\\models\\user',
        2 => 'app\\models\\studentrecord',
      ),
      3 => 
      array (
      ),
    ),
    '/var/www/html/app/Models/IncidentStatusChange.php' => 
    array (
      0 => '4faf2e5e89bc4f48377331f5afd8221dcbd13cbd8544e547127ce30342fa5db9',
      1 => 
      array (
        0 => 'app\\models\\incidentstatuschange',
      ),
      2 => 
      array (
        0 => 'app\\models\\booted',
        1 => 'app\\models\\incident',
        2 => 'app\\models\\changedby',
      ),
      3 => 
      array (
      ),
    ),
    '/var/www/html/app/Models/InstructionalModelSetting.php' => 
    array (
      0 => 'e623ef7b96f2c77013414a99058be2a3409a052f9546f7a6fdb748b1c3c6aa67',
      1 => 
      array (
        0 => 'app\\models\\instructionalmodelsetting',
      ),
      2 => 
      array (
        0 => 'app\\models\\school',
        1 => 'app\\models\\academicyear',
        2 => 'app\\models\\updatedby',
      ),
      3 => 
      array (
      ),
    ),
    '/var/www/html/app/Models/LedgerAccount.php' => 
    array (
      0 => '2d9504067f10669463b5d5a72dface1886dd8acdebb49d8a07354683ec6c9d4f',
      1 => 
      array (
        0 => 'app\\models\\ledgeraccount',
      ),
      2 => 
      array (
        0 => 'app\\models\\scopeforpurpose',
        1 => 'app\\models\\lines',
        2 => 'app\\models\\balance',
      ),
      3 => 
      array (
      ),
    ),
    '/var/www/html/app/Models/LedgerLine.php' => 
    array (
      0 => '541841c3912b4e2d6363a7c1fe02774b67d2d41f0ec67691af12be098caf9950',
      1 => 
      array (
        0 => 'app\\models\\ledgerline',
      ),
      2 => 
      array (
        0 => 'app\\models\\booted',
        1 => 'app\\models\\transaction',
        2 => 'app\\models\\account',
        3 => 'app\\models\\studentrecord',
      ),
      3 => 
      array (
      ),
    ),
    '/var/www/html/app/Models/LedgerTransaction.php' => 
    array (
      0 => '551b322fe2e061648a81985daf5c4c3e81ac2c09496016a9bc11784463e72e87',
      1 => 
      array (
        0 => 'app\\models\\ledgertransaction',
      ),
      2 => 
      array (
        0 => 'app\\models\\booted',
        1 => 'app\\models\\lines',
        2 => 'app\\models\\source',
        3 => 'app\\models\\reversalof',
        4 => 'app\\models\\reversals',
        5 => 'app\\models\\postedby',
        6 => 'app\\models\\isreversed',
        7 => 'app\\models\\total',
      ),
      3 => 
      array (
      ),
    ),
    '/var/www/html/app/Models/MyClass.php' => 
    array (
      0 => 'a78ea5ff45a946a051c9529178e0ce66490178bcea33614bc5516f72c48e4633',
      1 => 
      array (
        0 => 'app\\models\\myclass',
      ),
      2 => 
      array (
        0 => 'app\\models\\classgroup',
        1 => 'app\\models\\sections',
        2 => 'app\\models\\studentrecords',
        3 => 'app\\models\\subjects',
        4 => 'app\\models\\students',
        5 => 'app\\models\\syllabi',
        6 => 'app\\models\\timetables',
      ),
      3 => 
      array (
      ),
    ),
    '/var/www/html/app/Models/Notice.php' => 
    array (
      0 => '12ac827a016ecdd8fe6f25230f6bd7b93810c4d03c8da5b5c1e083c1930cf514',
      1 => 
      array (
        0 => 'app\\models\\notice',
      ),
      2 => 
      array (
        0 => 'app\\models\\scopepublished',
        1 => 'app\\models\\recipients',
        2 => 'app\\models\\publishedby',
        3 => 'app\\models\\revisionof',
        4 => 'app\\models\\ispublished',
        5 => 'app\\models\\hasrunout',
        6 => 'app\\models\\scopeactive',
        7 => 'app\\models\\getstartdateforhumansattribute',
        8 => 'app\\models\\getstopdateforhumansattribute',
      ),
      3 => 
      array (
      ),
    ),
    '/var/www/html/app/Models/NoticeRecipient.php' => 
    array (
      0 => '47bb987fe0566f6f234639130139ff2662f8ce9b6583eb19ce3b31d7f05153ad',
      1 => 
      array (
        0 => 'app\\models\\noticerecipient',
      ),
      2 => 
      array (
        0 => 'app\\models\\notice',
        1 => 'app\\models\\user',
        2 => 'app\\models\\markread',
        3 => 'app\\models\\dismiss',
      ),
      3 => 
      array (
      ),
    ),
    '/var/www/html/app/Models/Organization.php' => 
    array (
      0 => 'e7cea26967f5097922925671cf708e86519019b6e5ddf3c6b5592dda1444957d',
      1 => 
      array (
        0 => 'app\\models\\organization',
      ),
      2 => 
      array (
        0 => 'app\\models\\schools',
        1 => 'app\\models\\calendartemplates',
        2 => 'app\\models\\defaultcalendartemplate',
        3 => 'app\\models\\memberships',
        4 => 'app\\models\\hasanothermembermanager',
        5 => 'app\\models\\members',
      ),
      3 => 
      array (
      ),
    ),
    '/var/www/html/app/Models/OrganizationMembership.php' => 
    array (
      0 => '60db8771803c486136e98922804e7ef904515f9ad4e254ffda5f1b3fb91f8cee',
      1 => 
      array (
        0 => 'app\\models\\organizationmembership',
      ),
      2 => 
      array (
        0 => 'app\\models\\casts',
        1 => 'app\\models\\organization',
        2 => 'app\\models\\user',
        3 => 'app\\models\\scopeactive',
        4 => 'app\\models\\hasfullauthority',
        5 => 'app\\models\\grantedpermissions',
        6 => 'app\\models\\grants',
      ),
      3 => 
      array (
      ),
    ),
    '/var/www/html/app/Models/ParentRecord.php' => 
    array (
      0 => '565d2d4595ff783e1b45b2b58f2d08086be66a5a45017c305ddedb00a0eeebcb',
      1 => 
      array (
        0 => 'app\\models\\parentrecord',
      ),
      2 => 
      array (
        0 => 'app\\models\\user',
        1 => 'app\\models\\students',
      ),
      3 => 
      array (
      ),
    ),
    '/var/www/html/app/Models/PortalRequest.php' => 
    array (
      0 => '9749fd68479f1be93acb45eae894a29038045f8371c70774c863cfe32efc920a',
      1 => 
      array (
        0 => 'app\\models\\portalrequest',
      ),
      2 => 
      array (
        0 => 'app\\models\\scopeopen',
        1 => 'app\\models\\studentrecord',
        2 => 'app\\models\\requestedby',
        3 => 'app\\models\\answeredby',
      ),
      3 => 
      array (
      ),
    ),
    '/var/www/html/app/Models/Program.php' => 
    array (
      0 => '3abcc5e16d2cfa9a42a414f568deecd29e36f82e10eadb3255acf41fba0a231a',
      1 => 
      array (
        0 => 'app\\models\\program',
      ),
      2 => 
      array (
        0 => 'app\\models\\scopeactive',
        1 => 'app\\models\\participations',
      ),
      3 => 
      array (
      ),
    ),
    '/var/www/html/app/Models/ProgramParticipation.php' => 
    array (
      0 => 'f6813d23def23fc77c947085e9abd0e9fa87d07800d52df5029213b7cf0b2ee0',
      1 => 
      array (
        0 => 'app\\models\\programparticipation',
      ),
      2 => 
      array (
        0 => 'app\\models\\scoperunning',
        1 => 'app\\models\\program',
        2 => 'app\\models\\studentrecord',
        3 => 'app\\models\\staff',
      ),
      3 => 
      array (
      ),
    ),
    '/var/www/html/app/Models/Promotion.php' => 
    array (
      0 => '06e6cb81eb07a30f1f88ddc22cd440275d3d562e235a781cdb00e550e94a66cd',
      1 => 
      array (
        0 => 'app\\models\\promotion',
      ),
      2 => 
      array (
        0 => 'app\\models\\getlabelattribute',
        1 => 'app\\models\\oldclass',
        2 => 'app\\models\\newclass',
        3 => 'app\\models\\oldsection',
        4 => 'app\\models\\newsection',
        5 => 'app\\models\\academicyear',
      ),
      3 => 
      array (
      ),
    ),
    '/var/www/html/app/Models/ReportRun.php' => 
    array (
      0 => '81be430edcf6ac370b69c61b0113d67920af9a008cdf1a5fa229288240ce35cc',
      1 => 
      array (
        0 => 'app\\models\\reportrun',
      ),
      2 => 
      array (
        0 => 'app\\models\\requestedby',
        1 => 'app\\models\\isready',
      ),
      3 => 
      array (
      ),
    ),
    '/var/www/html/app/Models/ResultSnapshot.php' => 
    array (
      0 => '6f906318920d8442522ab2efd41675d97450c02e8ca7bee0d061ee191d491e23',
      1 => 
      array (
        0 => 'app\\models\\resultsnapshot',
      ),
      2 => 
      array (
        0 => 'app\\models\\booted',
        1 => 'app\\models\\scopelatestrevision',
        2 => 'app\\models\\studentrecord',
        3 => 'app\\models\\subject',
        4 => 'app\\models\\publishedby',
      ),
      3 => 
      array (
      ),
    ),
    '/var/www/html/app/Models/School.php' => 
    array (
      0 => 'e9a0afb3da3d9eea0812e692981e31c521cf576041a6809efba602ce43c8cb17',
      1 => 
      array (
        0 => 'app\\models\\school',
      ),
      2 => 
      array (
        0 => 'app\\models\\organization',
        1 => 'app\\models\\calendartemplate',
        2 => 'app\\models\\effectivecalendartemplate',
        3 => 'app\\models\\overridesorganizationcalendar',
        4 => 'app\\models\\getlogourlattribute',
        5 => 'app\\models\\classgroups',
        6 => 'app\\models\\memberships',
        7 => 'app\\models\\users',
        8 => 'app\\models\\myclasses',
        9 => 'app\\models\\academicyears',
        10 => 'app\\models\\academiclevels',
        11 => 'app\\models\\academiccyclesections',
        12 => 'app\\models\\academicyear',
        13 => 'app\\models\\academicperiod',
      ),
      3 => 
      array (
      ),
    ),
    '/var/www/html/app/Models/SchoolMembership.php' => 
    array (
      0 => '6d72604de41f74e8b993d1e463f64d9a1b3d69b7ffd89e298bc23c51504082e3',
      1 => 
      array (
        0 => 'app\\models\\schoolmembership',
      ),
      2 => 
      array (
        0 => 'app\\models\\user',
        1 => 'app\\models\\school',
        2 => 'app\\models\\scopeactive',
        3 => 'app\\models\\scopeprimary',
        4 => 'app\\models\\grantsaccess',
      ),
      3 => 
      array (
      ),
    ),
    '/var/www/html/app/Models/Section.php' => 
    array (
      0 => 'ae218270c599f58a8031692e06214a53a93a576cddcc90e4dcdb98e1d16f84de',
      1 => 
      array (
        0 => 'app\\models\\section',
      ),
      2 => 
      array (
        0 => 'app\\models\\myclass',
        1 => 'app\\models\\studentrecords',
        2 => 'app\\models\\students',
      ),
      3 => 
      array (
      ),
    ),
    '/var/www/html/app/Models/StaffAvailability.php' => 
    array (
      0 => 'cfcbc2b3fc9ea4c2d8444eaf45d72e8f0dd09e3ad5e39d63f7c5891aa491a17c',
      1 => 
      array (
        0 => 'app\\models\\staffavailability',
      ),
      2 => 
      array (
        0 => 'app\\models\\covers',
        1 => 'app\\models\\staffprofile',
      ),
      3 => 
      array (
      ),
    ),
    '/var/www/html/app/Models/StaffCredential.php' => 
    array (
      0 => '967095c56c0d8b6980cb95228f8cbb820a3f258afecdc9e0adec39493aadafb7',
      1 => 
      array (
        0 => 'app\\models\\staffcredential',
      ),
      2 => 
      array (
        0 => 'app\\models\\isverified',
        1 => 'app\\models\\hasexpired',
        2 => 'app\\models\\scopeexpiringbefore',
        3 => 'app\\models\\staffprofile',
      ),
      3 => 
      array (
      ),
    ),
    '/var/www/html/app/Models/StaffLeaveRequest.php' => 
    array (
      0 => '038551dd639242b2a1e7fb33a897ef8bd9c304b367c66b34df1de30ae3eb28f3',
      1 => 
      array (
        0 => 'app\\models\\staffleaverequest',
      ),
      2 => 
      array (
        0 => 'app\\models\\days',
        1 => 'app\\models\\covers',
        2 => 'app\\models\\scopeholding',
        3 => 'app\\models\\scopeoverlapping',
        4 => 'app\\models\\staffprofile',
        5 => 'app\\models\\statuschanges',
        6 => 'app\\models\\decidedby',
      ),
      3 => 
      array (
      ),
    ),
    '/var/www/html/app/Models/StaffLeaveStatusChange.php' => 
    array (
      0 => '0f739cc8389484a706040b36d762b1f83b6a013fea19a410943d421fb564fd5d',
      1 => 
      array (
        0 => 'app\\models\\staffleavestatuschange',
      ),
      2 => 
      array (
        0 => 'app\\models\\booted',
        1 => 'app\\models\\staffleaverequest',
        2 => 'app\\models\\changedby',
      ),
      3 => 
      array (
      ),
    ),
    '/var/www/html/app/Models/StaffProfile.php' => 
    array (
      0 => '2f21bbd207e2858e091c6913eed8e07310595b38bea18dcec381f1b64caef08f',
      1 => 
      array (
        0 => 'app\\models\\staffprofile',
      ),
      2 => 
      array (
        0 => 'app\\models\\scopeemployed',
        1 => 'app\\models\\scopeawayon',
        2 => 'app\\models\\user',
        3 => 'app\\models\\credentials',
        4 => 'app\\models\\availabilities',
        5 => 'app\\models\\leaverequests',
      ),
      3 => 
      array (
      ),
    ),
    '/var/www/html/app/Models/StudentHealthRecord.php' => 
    array (
      0 => 'cfbd461bf8fbbf79971566bd91c493e37ebdc5d5f757d653bfa1afcb0136b106',
      1 => 
      array (
        0 => 'app\\models\\studenthealthrecord',
      ),
      2 => 
      array (
        0 => 'app\\models\\studentrecord',
        1 => 'app\\models\\updatedby',
      ),
      3 => 
      array (
      ),
    ),
    '/var/www/html/app/Models/StudentRecord.php' => 
    array (
      0 => 'd6858459b0fbf4dc47f62af8008199a45d08cb371d83fbcda86bd6f878541e07',
      1 => 
      array (
        0 => 'app\\models\\studentrecord',
      ),
      2 => 
      array (
        0 => 'app\\models\\booted',
        1 => 'app\\models\\scopeattending',
        2 => 'app\\models\\scopewithstatus',
        3 => 'app\\models\\scopeprimary',
        4 => 'app\\models\\isgraduated',
        5 => 'app\\models\\getadmissiondateattribute',
        6 => 'app\\models\\myclass',
        7 => 'app\\models\\section',
        8 => 'app\\models\\user',
        9 => 'app\\models\\statuschanges',
        10 => 'app\\models\\placements',
        11 => 'app\\models\\currentplacement',
        12 => 'app\\models\\school',
        13 => 'app\\models\\transferredfrom',
        14 => 'app\\models\\academicyears',
        15 => 'app\\models\\currentacademicyear',
      ),
      3 => 
      array (
      ),
    ),
    '/var/www/html/app/Models/Subject.php' => 
    array (
      0 => 'e4266d1f4f518bb5d14d5e181f4a02cd9142a5de88a902727dba1042d924c96b',
      1 => 
      array (
        0 => 'app\\models\\subject',
      ),
      2 => 
      array (
        0 => 'app\\models\\myclass',
        1 => 'app\\models\\teachers',
        2 => 'app\\models\\timetablerecord',
      ),
      3 => 
      array (
      ),
    ),
    '/var/www/html/app/Models/SupportPlan.php' => 
    array (
      0 => '3f0e56eb2ab99bb42f72308fadcc51e64aa502368fabcf6dff21635f16bb5659',
      1 => 
      array (
        0 => 'app\\models\\supportplan',
      ),
      2 => 
      array (
        0 => 'app\\models\\booted',
        1 => 'app\\models\\scopereadableby',
        2 => 'app\\models\\scopeopen',
        3 => 'app\\models\\scopedueforreview',
        4 => 'app\\models\\studentrecord',
        5 => 'app\\models\\actions',
        6 => 'app\\models\\notes',
        7 => 'app\\models\\statuschanges',
        8 => 'app\\models\\createdby',
        9 => 'app\\models\\assignedto',
      ),
      3 => 
      array (
      ),
    ),
    '/var/www/html/app/Models/SupportPlanAction.php' => 
    array (
      0 => '286f4c195b6042f2269a51bcccfe7ca55365f95d620b00f1bb270235a1d0f47a',
      1 => 
      array (
        0 => 'app\\models\\supportplanaction',
      ),
      2 => 
      array (
        0 => 'app\\models\\isdone',
        1 => 'app\\models\\islate',
        2 => 'app\\models\\supportplan',
        3 => 'app\\models\\assignedto',
      ),
      3 => 
      array (
      ),
    ),
    '/var/www/html/app/Models/SupportPlanNote.php' => 
    array (
      0 => '80f148eda71b43de5aa5285394b22d83e338ccea66664860c7f04036c0fffd40',
      1 => 
      array (
        0 => 'app\\models\\supportplannote',
      ),
      2 => 
      array (
        0 => 'app\\models\\booted',
        1 => 'app\\models\\supportplan',
        2 => 'app\\models\\writtenby',
      ),
      3 => 
      array (
      ),
    ),
    '/var/www/html/app/Models/SupportPlanStatusChange.php' => 
    array (
      0 => '08b1b5b9e6619fd12991e4f162cacac89ff4166b34a2c22f4248ab3b6098f265',
      1 => 
      array (
        0 => 'app\\models\\supportplanstatuschange',
      ),
      2 => 
      array (
        0 => 'app\\models\\booted',
        1 => 'app\\models\\supportplan',
        2 => 'app\\models\\changedby',
      ),
      3 => 
      array (
      ),
    ),
    '/var/www/html/app/Models/Syllabus.php' => 
    array (
      0 => 'a6105f7e1622ab013ec3b8efbe12a699436ce4b7d1fad53e22482f1558ebd905',
      1 => 
      array (
        0 => 'app\\models\\syllabus',
      ),
      2 => 
      array (
        0 => 'app\\models\\subject',
        1 => 'app\\models\\academicperiod',
      ),
      3 => 
      array (
      ),
    ),
    '/var/www/html/app/Models/TeacherRecord.php' => 
    array (
      0 => 'e3bafc1cda6ba8b7398219be2cfb8ef5edfaf88fb1927872db98b2f04e26ca09',
      1 => 
      array (
        0 => 'app\\models\\teacherrecord',
      ),
      2 => 
      array (
      ),
      3 => 
      array (
      ),
    ),
    '/var/www/html/app/Models/TeachingAssignment.php' => 
    array (
      0 => 'a575a3972455faf04c2487f2a6cc1c2db9fef3b657bd1f722e2018b047c94ff3',
      1 => 
      array (
        0 => 'app\\models\\teachingassignment',
      ),
      2 => 
      array (
        0 => 'app\\models\\scoperunningon',
        1 => 'app\\models\\scopeforteacher',
        2 => 'app\\models\\isrunningon',
        3 => 'app\\models\\subject',
        4 => 'app\\models\\teacher',
        5 => 'app\\models\\academicyear',
        6 => 'app\\models\\academicperiod',
        7 => 'app\\models\\courseoffering',
        8 => 'app\\models\\section',
        9 => 'app\\models\\school',
      ),
      3 => 
      array (
      ),
    ),
    '/var/www/html/app/Models/Timetable.php' => 
    array (
      0 => '2f1a6e1b4ea84d3faa31dc572317d76d8a8bd58acfc28f6bfb4451a13225067b',
      1 => 
      array (
        0 => 'app\\models\\timetable',
      ),
      2 => 
      array (
        0 => 'app\\models\\booted',
        1 => 'app\\models\\getdirtykeys',
        2 => 'app\\models\\scopewithstatus',
        3 => 'app\\models\\scopepublished',
        4 => 'app\\models\\ispublished',
        5 => 'app\\models\\acceptschanges',
        6 => 'app\\models\\academicperiod',
        7 => 'app\\models\\myclass',
        8 => 'app\\models\\section',
        9 => 'app\\models\\publishedby',
        10 => 'app\\models\\revisionof',
        11 => 'app\\models\\revisions',
        12 => 'app\\models\\timeslots',
      ),
      3 => 
      array (
      ),
    ),
    '/var/www/html/app/Models/TimetableRecord.php' => 
    array (
      0 => '722b6ccbda54188e9abeadca0fc65407f1a753e73c411e7284a27fe5556300c4',
      1 => 
      array (
        0 => 'app\\models\\timetablerecord',
      ),
      2 => 
      array (
        0 => 'app\\models\\timetablerecordabletype',
        1 => 'app\\models\\timetablerecordableid',
        2 => 'app\\models\\timetablerecordable',
      ),
      3 => 
      array (
      ),
    ),
    '/var/www/html/app/Models/TimetableTimeSlot.php' => 
    array (
      0 => '31b43716be57321483124a3c52aa238ae8a2de876af9320591f0f3de7679f4df',
      1 => 
      array (
        0 => 'app\\models\\timetabletimeslot',
      ),
      2 => 
      array (
        0 => 'app\\models\\booted',
        1 => 'app\\models\\starttime',
        2 => 'app\\models\\stoptime',
        3 => 'app\\models\\name',
        4 => 'app\\models\\timetable',
        5 => 'app\\models\\governingacademicperiod',
        6 => 'app\\models\\weekdays',
      ),
      3 => 
      array (
      ),
    ),
    '/var/www/html/app/Models/TransferPackage.php' => 
    array (
      0 => '7703e4157e96ed77c4650103b12ca5294829bdcebb1ee876c2484bdf23cb80ed',
      1 => 
      array (
        0 => 'app\\models\\transferpackage',
      ),
      2 => 
      array (
        0 => 'app\\models\\booted',
        1 => 'app\\models\\wasreceived',
        2 => 'app\\models\\datasharingrequest',
        3 => 'app\\models\\studentrecord',
        4 => 'app\\models\\sourceschool',
        5 => 'app\\models\\destinationschool',
      ),
      3 => 
      array (
      ),
    ),
    '/var/www/html/app/Models/User.php' => 
    array (
      0 => '027df4ecafc76424fd8a6b667fefcf5eb7b26190c4138ecd80efdc6627106e9c',
      1 => 
      array (
        0 => 'app\\models\\user',
      ),
      2 => 
      array (
        0 => 'app\\models\\booted',
        1 => 'app\\models\\scopestudents',
        2 => 'app\\models\\scopeactiveaccounts',
        3 => 'app\\models\\scopeactivestudents',
        4 => 'app\\models\\scopeofschool',
        5 => 'app\\models\\schoolmemberships',
        6 => 'app\\models\\organizationmemberships',
        7 => 'app\\models\\organizations',
        8 => 'app\\models\\administersorganization',
        9 => 'app\\models\\schools',
        10 => 'app\\models\\primaryschool',
        11 => 'app\\models\\belongstoschool',
        12 => 'app\\models\\belongstocurrentschool',
        13 => 'app\\models\\studentrecords',
        14 => 'app\\models\\studentrecord',
        15 => 'app\\models\\graduatedstudentrecord',
        16 => 'app\\models\\allstudentrecords',
        17 => 'app\\models\\enrollmentofcurrentschool',
        18 => 'app\\models\\parents',
        19 => 'app\\models\\teacherrecord',
        20 => 'app\\models\\parentrecord',
        21 => 'app\\models\\accountinvitations',
        22 => 'app\\models\\pendingaccountinvitation',
        23 => 'app\\models\\hasactiveaccount',
        24 => 'app\\models\\isawaitinginvitationacceptance',
        25 => 'app\\models\\feeinvoices',
        26 => 'app\\models\\defaultprofilephotourl',
        27 => 'app\\models\\getbirthdayattribute',
        28 => 'app\\models\\subjects',
      ),
      3 => 
      array (
      ),
    ),
    '/var/www/html/app/Models/Weekday.php' => 
    array (
      0 => '17914679e860e05cf0d879ead8cda3cab844aa80199e8b63be0850ab7adf1842',
      1 => 
      array (
        0 => 'app\\models\\weekday',
      ),
      2 => 
      array (
      ),
      3 => 
      array (
      ),
    ),
    '/var/www/html/app/Notifications/AcademicCalendarReminder.php' => 
    array (
      0 => '5442d721f2c8d169e1ca4f4dfaa8c2a54b7181108ec567adf1bbd800cd790129',
      1 => 
      array (
        0 => 'app\\notifications\\academiccalendarreminder',
      ),
      2 => 
      array (
        0 => 'app\\notifications\\__construct',
        1 => 'app\\notifications\\via',
        2 => 'app\\notifications\\tomail',
        3 => 'app\\notifications\\toarray',
        4 => 'app\\notifications\\message',
      ),
      3 => 
      array (
      ),
    ),
    '/var/www/html/app/Notifications/AccountInvitationNotification.php' => 
    array (
      0 => '8a67106927869552b5d0431ff5a1ff508d2b3ad404e4b3850176bd642c159540',
      1 => 
      array (
        0 => 'app\\notifications\\accountinvitationnotification',
      ),
      2 => 
      array (
        0 => 'app\\notifications\\__construct',
        1 => 'app\\notifications\\via',
        2 => 'app\\notifications\\tomail',
      ),
      3 => 
      array (
      ),
    ),
    '/var/www/html/app/Policies/AcademicCycleSectionPolicy.php' => 
    array (
      0 => '8d02bb13df97150abb6b355b5e0f01e5ed862821a89fdf86370e08552d8ca0e8',
      1 => 
      array (
        0 => 'app\\policies\\academiccyclesectionpolicy',
      ),
      2 => 
      array (
        0 => 'app\\policies\\viewany',
        1 => 'app\\policies\\view',
        2 => 'app\\policies\\create',
        3 => 'app\\policies\\update',
        4 => 'app\\policies\\delete',
        5 => 'app\\policies\\restore',
        6 => 'app\\policies\\forcedelete',
      ),
      3 => 
      array (
      ),
    ),
    '/var/www/html/app/Policies/AcademicLevelPolicy.php' => 
    array (
      0 => '5727e15045b5a84507805dd6069fbaeb200f98540ad4de11e17c52b7cc899976',
      1 => 
      array (
        0 => 'app\\policies\\academiclevelpolicy',
      ),
      2 => 
      array (
        0 => 'app\\policies\\viewany',
        1 => 'app\\policies\\view',
        2 => 'app\\policies\\create',
        3 => 'app\\policies\\update',
        4 => 'app\\policies\\delete',
        5 => 'app\\policies\\restore',
        6 => 'app\\policies\\forcedelete',
      ),
      3 => 
      array (
      ),
    ),
    '/var/www/html/app/Policies/AcademicPeriodPolicy.php' => 
    array (
      0 => '2b1c3d0b243190e692dadcbd0be53f789d3d0de926b91a5a83d347fcc6e487c8',
      1 => 
      array (
        0 => 'app\\policies\\academicperiodpolicy',
      ),
      2 => 
      array (
        0 => 'app\\policies\\viewany',
        1 => 'app\\policies\\view',
        2 => 'app\\policies\\create',
        3 => 'app\\policies\\update',
        4 => 'app\\policies\\delete',
        5 => 'app\\policies\\restore',
        6 => 'app\\policies\\forcedelete',
        7 => 'app\\policies\\setacademicperiod',
        8 => 'app\\policies\\close',
        9 => 'app\\policies\\reopen',
      ),
      3 => 
      array (
      ),
    ),
    '/var/www/html/app/Policies/AcademicYearPolicy.php' => 
    array (
      0 => '8ea7823fb261bae89f58ca02e7c5ea728281f59aa9bf97f0dbd6961684685e50',
      1 => 
      array (
        0 => 'app\\policies\\academicyearpolicy',
      ),
      2 => 
      array (
        0 => 'app\\policies\\__construct',
        1 => 'app\\policies\\viewany',
        2 => 'app\\policies\\view',
        3 => 'app\\policies\\create',
        4 => 'app\\policies\\update',
        5 => 'app\\policies\\delete',
        6 => 'app\\policies\\restore',
        7 => 'app\\policies\\forcedelete',
        8 => 'app\\policies\\setacademicyear',
        9 => 'app\\policies\\close',
        10 => 'app\\policies\\reopen',
        11 => 'app\\policies\\viewinstructionalmodel',
        12 => 'app\\policies\\setinstructionalmodel',
        13 => 'app\\policies\\administerscampus',
      ),
      3 => 
      array (
      ),
    ),
    '/var/www/html/app/Policies/AccountInvitationPolicy.php' => 
    array (
      0 => '2ab977eeff8529836531043205027ac1076e080f8d00e86049a5d43fd7875738',
      1 => 
      array (
        0 => 'app\\policies\\accountinvitationpolicy',
      ),
      2 => 
      array (
        0 => 'app\\policies\\__construct',
        1 => 'app\\policies\\viewany',
        2 => 'app\\policies\\view',
        3 => 'app\\policies\\resend',
        4 => 'app\\policies\\revoke',
        5 => 'app\\policies\\canact',
      ),
      3 => 
      array (
      ),
    ),
    '/var/www/html/app/Policies/CalendarTemplatePolicy.php' => 
    array (
      0 => '20d1cf59a8402a2c350c20f2425407a52f72e90dad12dd1e7d2abfc6f52a0c53',
      1 => 
      array (
        0 => 'app\\policies\\calendartemplatepolicy',
      ),
      2 => 
      array (
        0 => 'app\\policies\\__construct',
        1 => 'app\\policies\\viewany',
        2 => 'app\\policies\\view',
        3 => 'app\\policies\\create',
        4 => 'app\\policies\\update',
        5 => 'app\\policies\\delete',
        6 => 'app\\policies\\restore',
        7 => 'app\\policies\\forcedelete',
      ),
      3 => 
      array (
      ),
    ),
    '/var/www/html/app/Policies/ClassGroupPolicy.php' => 
    array (
      0 => '07bd4f0dd6041acf59961f1165b53bbd1eb4d0599f25a3667a6d07a321900f70',
      1 => 
      array (
        0 => 'app\\policies\\classgrouppolicy',
      ),
      2 => 
      array (
        0 => 'app\\policies\\viewany',
        1 => 'app\\policies\\view',
        2 => 'app\\policies\\create',
        3 => 'app\\policies\\update',
        4 => 'app\\policies\\delete',
        5 => 'app\\policies\\restore',
        6 => 'app\\policies\\forcedelete',
      ),
      3 => 
      array (
      ),
    ),
    '/var/www/html/app/Policies/CohortPolicy.php' => 
    array (
      0 => 'c62bc58af32e024a861ba62356058c90b58d24470bf4e02f5f1ec2d0ac9119a7',
      1 => 
      array (
        0 => 'app\\policies\\cohortpolicy',
      ),
      2 => 
      array (
        0 => 'app\\policies\\viewany',
        1 => 'app\\policies\\view',
        2 => 'app\\policies\\create',
        3 => 'app\\policies\\update',
        4 => 'app\\policies\\delete',
      ),
      3 => 
      array (
      ),
    ),
    '/var/www/html/app/Policies/CourseOfferingPolicy.php' => 
    array (
      0 => 'fddb4d52b1351c33b973b8c581f4fc57708c0ce1f91031c79b94dd08e5c2caf4',
      1 => 
      array (
        0 => 'app\\policies\\courseofferingpolicy',
      ),
      2 => 
      array (
        0 => 'app\\policies\\viewany',
        1 => 'app\\policies\\view',
        2 => 'app\\policies\\create',
        3 => 'app\\policies\\update',
        4 => 'app\\policies\\delete',
        5 => 'app\\policies\\restore',
        6 => 'app\\policies\\forcedelete',
      ),
      3 => 
      array (
      ),
    ),
    '/var/www/html/app/Policies/CustomTimetableItemPolicy.php' => 
    array (
      0 => 'efaee8f0a1ac43e0e99247b1f9b3dc4e3eeb9f5d9130431534426beae0fe8463',
      1 => 
      array (
        0 => 'app\\policies\\customtimetableitempolicy',
      ),
      2 => 
      array (
        0 => 'app\\policies\\viewany',
        1 => 'app\\policies\\view',
        2 => 'app\\policies\\create',
        3 => 'app\\policies\\update',
        4 => 'app\\policies\\delete',
        5 => 'app\\policies\\restore',
        6 => 'app\\policies\\forcedelete',
      ),
      3 => 
      array (
      ),
    ),
    '/var/www/html/app/Policies/DataSharingRequestPolicy.php' => 
    array (
      0 => 'e4f72ee017bc2b36b862e50a4cde407e2f06daf8121aad2617f7ccbf48ff2f04',
      1 => 
      array (
        0 => 'app\\policies\\datasharingrequestpolicy',
      ),
      2 => 
      array (
        0 => 'app\\policies\\viewany',
        1 => 'app\\policies\\view',
        2 => 'app\\policies\\create',
        3 => 'app\\policies\\decide',
        4 => 'app\\policies\\fulfil',
      ),
      3 => 
      array (
      ),
    ),
    '/var/www/html/app/Policies/ExamPolicy.php' => 
    array (
      0 => 'ed20e52edc8fee576a2b725bfcc4631ef629d8de66f54566d3b537ee636b6989',
      1 => 
      array (
        0 => 'app\\policies\\exampolicy',
      ),
      2 => 
      array (
        0 => 'app\\policies\\viewany',
        1 => 'app\\policies\\view',
        2 => 'app\\policies\\create',
        3 => 'app\\policies\\update',
        4 => 'app\\policies\\delete',
        5 => 'app\\policies\\restore',
        6 => 'app\\policies\\forcedelete',
        7 => 'app\\policies\\checkresult',
      ),
      3 => 
      array (
      ),
    ),
    '/var/www/html/app/Policies/ExamRecordPolicy.php' => 
    array (
      0 => '4991abfe6f4d7e8b7ddbe6dd9f9866e7fd9ace9174a00d9e6d2a10eb842f7fc4',
      1 => 
      array (
        0 => 'app\\policies\\examrecordpolicy',
      ),
      2 => 
      array (
        0 => 'app\\policies\\viewany',
        1 => 'app\\policies\\view',
        2 => 'app\\policies\\create',
        3 => 'app\\policies\\update',
        4 => 'app\\policies\\delete',
        5 => 'app\\policies\\restore',
        6 => 'app\\policies\\forcedelete',
      ),
      3 => 
      array (
      ),
    ),
    '/var/www/html/app/Policies/ExamSlotPolicy.php' => 
    array (
      0 => '692b624061c28326a39323856b51845e1c338cf9c468b3bf2279147026f71164',
      1 => 
      array (
        0 => 'app\\policies\\examslotpolicy',
      ),
      2 => 
      array (
        0 => 'app\\policies\\viewany',
        1 => 'app\\policies\\view',
        2 => 'app\\policies\\create',
        3 => 'app\\policies\\update',
        4 => 'app\\policies\\delete',
        5 => 'app\\policies\\restore',
        6 => 'app\\policies\\forcedelete',
      ),
      3 => 
      array (
      ),
    ),
    '/var/www/html/app/Policies/FeeCategoryPolicy.php' => 
    array (
      0 => '96c5a29682ebf9fec05f372748af9e5c2d02b7b0ad29533f8aed54ee52daa134',
      1 => 
      array (
        0 => 'app\\policies\\feecategorypolicy',
      ),
      2 => 
      array (
        0 => 'app\\policies\\viewany',
        1 => 'app\\policies\\view',
        2 => 'app\\policies\\create',
        3 => 'app\\policies\\update',
        4 => 'app\\policies\\delete',
        5 => 'app\\policies\\restore',
        6 => 'app\\policies\\forcedelete',
      ),
      3 => 
      array (
      ),
    ),
    '/var/www/html/app/Policies/FeeInvoicePolicy.php' => 
    array (
      0 => '2cc9406bed1aeeb506209046d5b38e89538f5190c68fd742f51e0de9edd37a51',
      1 => 
      array (
        0 => 'app\\policies\\feeinvoicepolicy',
      ),
      2 => 
      array (
        0 => 'app\\policies\\viewany',
        1 => 'app\\policies\\view',
        2 => 'app\\policies\\create',
        3 => 'app\\policies\\update',
        4 => 'app\\policies\\delete',
        5 => 'app\\policies\\restore',
        6 => 'app\\policies\\forcedelete',
      ),
      3 => 
      array (
      ),
    ),
    '/var/www/html/app/Policies/FeeInvoiceRecordPolicy.php' => 
    array (
      0 => '82e0ec82870e9628a1467f68c74c9bf3c3823819790151e6ecda2094f0d070ff',
      1 => 
      array (
        0 => 'app\\policies\\feeinvoicerecordpolicy',
      ),
      2 => 
      array (
        0 => 'app\\policies\\viewany',
        1 => 'app\\policies\\view',
        2 => 'app\\policies\\create',
        3 => 'app\\policies\\update',
        4 => 'app\\policies\\delete',
        5 => 'app\\policies\\restore',
        6 => 'app\\policies\\forcedelete',
      ),
      3 => 
      array (
      ),
    ),
    '/var/www/html/app/Policies/FeePolicy.php' => 
    array (
      0 => 'd01d4bdbc0bcec90e3efd00d24b65bf8c3f9845f035b679cbb23447d83b4c3cc',
      1 => 
      array (
        0 => 'app\\policies\\feepolicy',
      ),
      2 => 
      array (
        0 => 'app\\policies\\viewany',
        1 => 'app\\policies\\view',
        2 => 'app\\policies\\create',
        3 => 'app\\policies\\update',
        4 => 'app\\policies\\delete',
        5 => 'app\\policies\\restore',
        6 => 'app\\policies\\forcedelete',
      ),
      3 => 
      array (
      ),
    ),
    '/var/www/html/app/Policies/GradeSystemPolicy.php' => 
    array (
      0 => '232a1dd864aebdde4345be62ddb40a0d23cbeea0cd939747e43a10b1fad75a8e',
      1 => 
      array (
        0 => 'app\\policies\\gradesystempolicy',
      ),
      2 => 
      array (
        0 => 'app\\policies\\viewany',
        1 => 'app\\policies\\view',
        2 => 'app\\policies\\create',
        3 => 'app\\policies\\update',
        4 => 'app\\policies\\delete',
        5 => 'app\\policies\\restore',
        6 => 'app\\policies\\forcedelete',
      ),
      3 => 
      array (
      ),
    ),
    '/var/www/html/app/Policies/GraduationPolicy.php' => 
    array (
      0 => 'b27c8f9c10406ccfb78a358f1cb218fa6ec69d73d307104929bd878c5b54cbda',
      1 => 
      array (
        0 => 'app\\policies\\graduationpolicy',
      ),
      2 => 
      array (
        0 => 'app\\policies\\viewany',
        1 => 'app\\policies\\graduate',
        2 => 'app\\policies\\resetgraduation',
      ),
      3 => 
      array (
      ),
    ),
    '/var/www/html/app/Policies/ImportBatchPolicy.php' => 
    array (
      0 => 'bc6dd98c4d0900484e6e014a36eed4f9aee72534092aa9aeaf9b831a681044fd',
      1 => 
      array (
        0 => 'app\\policies\\importbatchpolicy',
      ),
      2 => 
      array (
        0 => 'app\\policies\\viewany',
        1 => 'app\\policies\\view',
        2 => 'app\\policies\\create',
        3 => 'app\\policies\\apply',
      ),
      3 => 
      array (
      ),
    ),
    '/var/www/html/app/Policies/IncidentPolicy.php' => 
    array (
      0 => 'a5baf94bf94a8ec299e887f1eca7e58a4d18ae585b925198f6893c2efaf55ce2',
      1 => 
      array (
        0 => 'app\\policies\\incidentpolicy',
      ),
      2 => 
      array (
        0 => 'app\\policies\\viewany',
        1 => 'app\\policies\\view',
        2 => 'app\\policies\\create',
        3 => 'app\\policies\\update',
        4 => 'app\\policies\\delete',
      ),
      3 => 
      array (
      ),
    ),
    '/var/www/html/app/Policies/MyClassPolicy.php' => 
    array (
      0 => 'dc8e0ff5c5e1f1316d77715166c1d232af3e4e27df81d3214f22cdc215dd9393',
      1 => 
      array (
        0 => 'app\\policies\\myclasspolicy',
      ),
      2 => 
      array (
        0 => 'app\\policies\\viewany',
        1 => 'app\\policies\\view',
        2 => 'app\\policies\\create',
        3 => 'app\\policies\\update',
        4 => 'app\\policies\\delete',
        5 => 'app\\policies\\restore',
        6 => 'app\\policies\\forcedelete',
      ),
      3 => 
      array (
      ),
    ),
    '/var/www/html/app/Policies/NoticePolicy.php' => 
    array (
      0 => '1dcb3f93832335493564f96d42363545cddfb96b44f53bfa6d6c921a3ff5177a',
      1 => 
      array (
        0 => 'app\\policies\\noticepolicy',
      ),
      2 => 
      array (
        0 => 'app\\policies\\viewany',
        1 => 'app\\policies\\view',
        2 => 'app\\policies\\create',
        3 => 'app\\policies\\update',
        4 => 'app\\policies\\delete',
        5 => 'app\\policies\\restore',
        6 => 'app\\policies\\forcedelete',
      ),
      3 => 
      array (
      ),
    ),
    '/var/www/html/app/Policies/OrganizationPolicy.php' => 
    array (
      0 => '3014e718bf503d30d8a19143c636e36202f656401db1b57b4182df4e1aae5861',
      1 => 
      array (
        0 => 'app\\policies\\organizationpolicy',
      ),
      2 => 
      array (
        0 => 'app\\policies\\__construct',
        1 => 'app\\policies\\viewany',
        2 => 'app\\policies\\view',
        3 => 'app\\policies\\create',
        4 => 'app\\policies\\update',
        5 => 'app\\policies\\managemembers',
        6 => 'app\\policies\\managecampuses',
        7 => 'app\\policies\\managecalendar',
        8 => 'app\\policies\\viewreports',
        9 => 'app\\policies\\delete',
        10 => 'app\\policies\\restore',
        11 => 'app\\policies\\forcedelete',
      ),
      3 => 
      array (
      ),
    ),
    '/var/www/html/app/Policies/PortalRequestPolicy.php' => 
    array (
      0 => '36ea3d91aebb25ac4f65175308685242ad888901569547ea27a4bbab1a0501aa',
      1 => 
      array (
        0 => 'app\\policies\\portalrequestpolicy',
      ),
      2 => 
      array (
        0 => 'app\\policies\\__construct',
        1 => 'app\\policies\\viewany',
        2 => 'app\\policies\\view',
        3 => 'app\\policies\\answer',
      ),
      3 => 
      array (
      ),
    ),
    '/var/www/html/app/Policies/PromotionPolicy.php' => 
    array (
      0 => 'bd0f8ec662834d4d9b3968ed88a80bb0adc62065263e27a5936262f3c922a845',
      1 => 
      array (
        0 => 'app\\policies\\promotionpolicy',
      ),
      2 => 
      array (
        0 => 'app\\policies\\viewany',
        1 => 'app\\policies\\promote',
        2 => 'app\\policies\\reset',
        3 => 'app\\policies\\view',
      ),
      3 => 
      array (
      ),
    ),
    '/var/www/html/app/Policies/ReportRunPolicy.php' => 
    array (
      0 => '7e54acfc96fcb5b1809440a605131e87490d949ac372bba8d8e36f44ae8c879d',
      1 => 
      array (
        0 => 'app\\policies\\reportrunpolicy',
      ),
      2 => 
      array (
        0 => 'app\\policies\\viewany',
        1 => 'app\\policies\\view',
        2 => 'app\\policies\\create',
        3 => 'app\\policies\\download',
      ),
      3 => 
      array (
      ),
    ),
    '/var/www/html/app/Policies/SchoolPolicy.php' => 
    array (
      0 => '83e2009af328b8e86f5e71cf1ce8d8c6a6a688ce02e346f3be13ab0b037470be',
      1 => 
      array (
        0 => 'app\\policies\\schoolpolicy',
      ),
      2 => 
      array (
        0 => 'app\\policies\\__construct',
        1 => 'app\\policies\\viewany',
        2 => 'app\\policies\\view',
        3 => 'app\\policies\\create',
        4 => 'app\\policies\\update',
        5 => 'app\\policies\\delete',
        6 => 'app\\policies\\restore',
        7 => 'app\\policies\\forcedelete',
        8 => 'app\\policies\\setschool',
        9 => 'app\\policies\\createfororganization',
        10 => 'app\\policies\\canmanageorganization',
      ),
      3 => 
      array (
      ),
    ),
    '/var/www/html/app/Policies/SectionPolicy.php' => 
    array (
      0 => 'b5fd925df1ea3b54f4c86259ecd3b9d10cf2b8549c22772630cff09609766ccf',
      1 => 
      array (
        0 => 'app\\policies\\sectionpolicy',
      ),
      2 => 
      array (
        0 => 'app\\policies\\viewany',
        1 => 'app\\policies\\view',
        2 => 'app\\policies\\create',
        3 => 'app\\policies\\update',
        4 => 'app\\policies\\delete',
        5 => 'app\\policies\\restore',
        6 => 'app\\policies\\forcedelete',
      ),
      3 => 
      array (
      ),
    ),
    '/var/www/html/app/Policies/StaffLeaveRequestPolicy.php' => 
    array (
      0 => '57619478251f6a66b0f89693dae9067e8f16c476d0cdc8a26ab064e92a15cfe7',
      1 => 
      array (
        0 => 'app\\policies\\staffleaverequestpolicy',
      ),
      2 => 
      array (
        0 => 'app\\policies\\viewany',
        1 => 'app\\policies\\view',
        2 => 'app\\policies\\create',
        3 => 'app\\policies\\decide',
        4 => 'app\\policies\\update',
      ),
      3 => 
      array (
      ),
    ),
    '/var/www/html/app/Policies/StaffProfilePolicy.php' => 
    array (
      0 => '6448ecc902c3b6c5b8011027ef4734818af3362904fa2126dbac65b6fe59ac7c',
      1 => 
      array (
        0 => 'app\\policies\\staffprofilepolicy',
      ),
      2 => 
      array (
        0 => 'app\\policies\\viewany',
        1 => 'app\\policies\\view',
        2 => 'app\\policies\\create',
        3 => 'app\\policies\\update',
        4 => 'app\\policies\\delete',
      ),
      3 => 
      array (
      ),
    ),
    '/var/www/html/app/Policies/StudentHealthRecordPolicy.php' => 
    array (
      0 => 'c2c5c2d0697aa7de46d375242a6dfca6129312817495ff7274c094fc2efe5bc0',
      1 => 
      array (
        0 => 'app\\policies\\studenthealthrecordpolicy',
      ),
      2 => 
      array (
        0 => 'app\\policies\\viewany',
        1 => 'app\\policies\\view',
        2 => 'app\\policies\\create',
        3 => 'app\\policies\\update',
        4 => 'app\\policies\\delete',
      ),
      3 => 
      array (
      ),
    ),
    '/var/www/html/app/Policies/SubjectPolicy.php' => 
    array (
      0 => '98c6c2a51551bfc56fcfeda61366f369a2f8bb7392f268de1d2def15f6c42fee',
      1 => 
      array (
        0 => 'app\\policies\\subjectpolicy',
      ),
      2 => 
      array (
        0 => 'app\\policies\\viewany',
        1 => 'app\\policies\\view',
        2 => 'app\\policies\\create',
        3 => 'app\\policies\\update',
        4 => 'app\\policies\\delete',
        5 => 'app\\policies\\restore',
        6 => 'app\\policies\\forcedelete',
        7 => 'app\\policies\\assignteacher',
      ),
      3 => 
      array (
      ),
    ),
    '/var/www/html/app/Policies/SupportPlanPolicy.php' => 
    array (
      0 => '3a4623b31b68b2b7c7d4df6b2ebef15c13e9b0b9f579926f80fcc8faa7805143',
      1 => 
      array (
        0 => 'app\\policies\\supportplanpolicy',
      ),
      2 => 
      array (
        0 => 'app\\policies\\viewany',
        1 => 'app\\policies\\view',
        2 => 'app\\policies\\create',
        3 => 'app\\policies\\update',
        4 => 'app\\policies\\delete',
      ),
      3 => 
      array (
      ),
    ),
    '/var/www/html/app/Policies/SyllabusPolicy.php' => 
    array (
      0 => '11baf27749a0dfb77d26005d47194800479566f67f578f9cb6a9d6e5d3a56290',
      1 => 
      array (
        0 => 'app\\policies\\syllabuspolicy',
      ),
      2 => 
      array (
        0 => 'app\\policies\\viewany',
        1 => 'app\\policies\\view',
        2 => 'app\\policies\\create',
        3 => 'app\\policies\\update',
        4 => 'app\\policies\\delete',
        5 => 'app\\policies\\restore',
        6 => 'app\\policies\\forcedelete',
      ),
      3 => 
      array (
      ),
    ),
    '/var/www/html/app/Policies/TimetablePolicy.php' => 
    array (
      0 => 'b8791b25146e9d281ae485df512ce2d09b0569fbe906e24b1c00b2aa35f90be2',
      1 => 
      array (
        0 => 'app\\policies\\timetablepolicy',
      ),
      2 => 
      array (
        0 => 'app\\policies\\viewany',
        1 => 'app\\policies\\view',
        2 => 'app\\policies\\create',
        3 => 'app\\policies\\update',
        4 => 'app\\policies\\delete',
        5 => 'app\\policies\\publish',
        6 => 'app\\policies\\revise',
        7 => 'app\\policies\\restore',
        8 => 'app\\policies\\forcedelete',
      ),
      3 => 
      array (
      ),
    ),
    '/var/www/html/app/Policies/TimetableTimeSlotPolicy.php' => 
    array (
      0 => '4c15b0add45b7c16e707c529859b95b4cc6fc782df63e9445eb6e55a265ac1bc',
      1 => 
      array (
        0 => 'app\\policies\\timetabletimeslotpolicy',
      ),
      2 => 
      array (
        0 => 'app\\policies\\viewany',
        1 => 'app\\policies\\view',
        2 => 'app\\policies\\create',
        3 => 'app\\policies\\update',
        4 => 'app\\policies\\delete',
        5 => 'app\\policies\\restore',
        6 => 'app\\policies\\forcedelete',
      ),
      3 => 
      array (
      ),
    ),
    '/var/www/html/app/Policies/UserPolicy.php' => 
    array (
      0 => 'ad37fed98c61d54b64ba7595dd97c1e789d5457a716bc5817dfa96c8e15825a0',
      1 => 
      array (
        0 => 'app\\policies\\userpolicy',
      ),
      2 => 
      array (
        0 => 'app\\policies\\viewany',
        1 => 'app\\policies\\view',
        2 => 'app\\policies\\create',
        3 => 'app\\policies\\update',
        4 => 'app\\policies\\delete',
        5 => 'app\\policies\\restore',
        6 => 'app\\policies\\forcedelete',
        7 => 'app\\policies\\manageaccountaccess',
      ),
      3 => 
      array (
      ),
    ),
    '/var/www/html/app/Providers/AppServiceProvider.php' => 
    array (
      0 => '92028954af546b61bd48a5b422e0a95907083f1371d0ca45f7f8e8e0721cda21',
      1 => 
      array (
        0 => 'app\\providers\\appserviceprovider',
      ),
      2 => 
      array (
        0 => 'app\\providers\\register',
        1 => 'app\\providers\\boot',
      ),
      3 => 
      array (
      ),
    ),
    '/var/www/html/app/Providers/BroadcastServiceProvider.php' => 
    array (
      0 => '6dade8c1ce5b4f91204e5c56f821f658f3e24d3d5bbae1a625c6914995ad81d2',
      1 => 
      array (
        0 => 'app\\providers\\broadcastserviceprovider',
      ),
      2 => 
      array (
        0 => 'app\\providers\\boot',
      ),
      3 => 
      array (
      ),
    ),
    '/var/www/html/app/Providers/MonitoringServiceProvider.php' => 
    array (
      0 => '295eb2c51e25bfe133167f81c143bcc8502a5362ff03c44770d6c6c32ad8d996',
      1 => 
      array (
        0 => 'app\\providers\\monitoringserviceprovider',
      ),
      2 => 
      array (
        0 => 'app\\providers\\boot',
        1 => 'app\\providers\\reportslowqueries',
        2 => 'app\\providers\\reportslowrequests',
        3 => 'app\\providers\\reportfailedjobs',
      ),
      3 => 
      array (
      ),
    ),
    '/var/www/html/app/Reports/ClassListReport.php' => 
    array (
      0 => '811f9cd8e946bc018ce32ad8a2b9ef121fab845940b7b3fe16891dfcfc7a5abb',
      1 => 
      array (
        0 => 'app\\reports\\classlistreport',
      ),
      2 => 
      array (
        0 => 'app\\reports\\key',
        1 => 'app\\reports\\title',
        2 => 'app\\reports\\columns',
        3 => 'app\\reports\\rows',
      ),
      3 => 
      array (
      ),
    ),
    '/var/www/html/app/Reports/StudentBalancesReport.php' => 
    array (
      0 => '4805ede618f95f2fd53f998cbeaf3d58a441e9697dab0cb6da1555c70f41fe8d',
      1 => 
      array (
        0 => 'app\\reports\\studentbalancesreport',
      ),
      2 => 
      array (
        0 => 'app\\reports\\__construct',
        1 => 'app\\reports\\key',
        2 => 'app\\reports\\title',
        3 => 'app\\reports\\columns',
        4 => 'app\\reports\\rows',
      ),
      3 => 
      array (
      ),
    ),
    '/var/www/html/app/Services/Academic/AcademicPeriodContext.php' => 
    array (
      0 => 'fcf252bf7ab98a7b08aab9b6ab8351a8d548c947e683585c4c421f9113f10026',
      1 => 
      array (
        0 => 'app\\services\\academic\\academicperiodcontext',
      ),
      2 => 
      array (
        0 => 'app\\services\\academic\\academicyear',
        1 => 'app\\services\\academic\\academicyearid',
        2 => 'app\\services\\academic\\academicperiod',
        3 => 'app\\services\\academic\\academicperiodid',
        4 => 'app\\services\\academic\\academicyearorfail',
        5 => 'app\\services\\academic\\academicperiodorfail',
        6 => 'app\\services\\academic\\setacademicyear',
        7 => 'app\\services\\academic\\setacademicperiod',
        8 => 'app\\services\\academic\\forget',
        9 => 'app\\services\\academic\\isresolved',
        10 => 'app\\services\\academic\\resolvefor',
        11 => 'app\\services\\academic\\allowedacademicyear',
        12 => 'app\\services\\academic\\allowedacademicperiod',
        13 => 'app\\services\\academic\\remember',
      ),
      3 => 
      array (
      ),
    ),
    '/var/www/html/app/Services/AcademicPeriod/AcademicPeriodService.php' => 
    array (
      0 => '2ab16ef4b1c6e3e0907481b67057bb1275ee66ed1e2c2a4f1b9b7bf6645d17e2',
      1 => 
      array (
        0 => 'app\\services\\academicperiod\\academicperiodservice',
      ),
      2 => 
      array (
        0 => 'app\\services\\academicperiod\\getallacademicperiods',
        1 => 'app\\services\\academicperiod\\getallacademicperiodsinacademicyear',
        2 => 'app\\services\\academicperiod\\getacademicperiodbyid',
        3 => 'app\\services\\academicperiod\\createacademicperiod',
        4 => 'app\\services\\academicperiod\\setacademicperiod',
        5 => 'app\\services\\academicperiod\\updateacademicperiod',
        6 => 'app\\services\\academicperiod\\date',
        7 => 'app\\services\\academicperiod\\failifdatesdonotfit',
        8 => 'app\\services\\academicperiod\\deleteacademicperiod',
      ),
      3 => 
      array (
      ),
    ),
    '/var/www/html/app/Services/AcademicYear/AcademicYearService.php' => 
    array (
      0 => '1efd6cb7936267b8409f84a52bf9ddfbe835de343dbe11e12efd3e583f217ef4',
      1 => 
      array (
        0 => 'app\\services\\academicyear\\academicyearservice',
      ),
      2 => 
      array (
        0 => 'app\\services\\academicyear\\__construct',
        1 => 'app\\services\\academicyear\\getallacademicyears',
        2 => 'app\\services\\academicyear\\getacademicyearbyid',
        3 => 'app\\services\\academicyear\\createacademicyear',
        4 => 'app\\services\\academicyear\\updateacademicyear',
        5 => 'app\\services\\academicyear\\deleteacademicyear',
        6 => 'app\\services\\academicyear\\setacademicyear',
        7 => 'app\\services\\academicyear\\setschooldefaultacademicyear',
      ),
      3 => 
      array (
      ),
    ),
    '/var/www/html/app/Services/Admin/AdminService.php' => 
    array (
      0 => 'af2cdbfe47878653f2e224faa055c94bce148a20b73b08b75e6c683bbccef503',
      1 => 
      array (
        0 => 'app\\services\\admin\\adminservice',
      ),
      2 => 
      array (
        0 => 'app\\services\\admin\\__construct',
        1 => 'app\\services\\admin\\getalladmins',
        2 => 'app\\services\\admin\\createadmin',
        3 => 'app\\services\\admin\\updateadmin',
        4 => 'app\\services\\admin\\deleteadmin',
      ),
      3 => 
      array (
      ),
    ),
    '/var/www/html/app/Services/Attendance/AttendanceSummary.php' => 
    array (
      0 => 'e31bc88ae1b212adeed75cad38a84561099406395853461478cf6b019a2257d1',
      1 => 
      array (
        0 => 'app\\services\\attendance\\attendancesummary',
      ),
      2 => 
      array (
        0 => 'app\\services\\attendance\\forstudent',
      ),
      3 => 
      array (
      ),
    ),
    '/var/www/html/app/Services/Authorization/OrganizationPermissionScope.php' => 
    array (
      0 => 'e0bf2583c866c9296b7628a78e0c8f012b0091b9d6008d4e161f0ef7a50e6bfd',
      1 => 
      array (
        0 => 'app\\services\\authorization\\organizationpermissionscope',
      ),
      2 => 
      array (
        0 => 'app\\services\\authorization\\__construct',
        1 => 'app\\services\\authorization\\allows',
        2 => 'app\\services\\authorization\\permissionsfor',
        3 => 'app\\services\\authorization\\forget',
        4 => 'app\\services\\authorization\\membershipfor',
      ),
      3 => 
      array (
      ),
    ),
    '/var/www/html/app/Services/Authorization/SystemPermissionScope.php' => 
    array (
      0 => 'a00af45730d3d8654df8f80ff9c3fa1f0fd6b501fee1418e8712a6f42eb88db7',
      1 => 
      array (
        0 => 'app\\services\\authorization\\systempermissionscope',
      ),
      2 => 
      array (
        0 => 'app\\services\\authorization\\__construct',
        1 => 'app\\services\\authorization\\allows',
        2 => 'app\\services\\authorization\\forget',
        3 => 'app\\services\\authorization\\withinuserscope',
        4 => 'app\\services\\authorization\\permissionsfor',
        5 => 'app\\services\\authorization\\within',
      ),
      3 => 
      array (
      ),
    ),
    '/var/www/html/app/Services/Calendar/ClosureFinding.php' => 
    array (
      0 => 'efd29fe9e499185d17e7ae5b4772b9be03db7a78f7f2b2309995cc3597eb7bc4',
      1 => 
      array (
        0 => 'app\\services\\calendar\\closurefinding',
      ),
      2 => 
      array (
        0 => 'app\\services\\calendar\\__construct',
        1 => 'app\\services\\calendar\\toarray',
      ),
      3 => 
      array (
      ),
    ),
    '/var/www/html/app/Services/Calendar/ClosureReadinessCheck.php' => 
    array (
      0 => 'a3a2286b7b56c83688e42a8a49ffca689b8c1f1965fa227065add490151636d2',
      1 => 
      array (
        0 => 'app\\services\\calendar\\closurereadinesscheck',
      ),
      2 => 
      array (
        0 => 'app\\services\\calendar\\for',
        1 => 'app\\services\\calendar\\isready',
        2 => 'app\\services\\calendar\\snapshot',
        3 => 'app\\services\\calendar\\forperiod',
        4 => 'app\\services\\calendar\\foryear',
        5 => 'app\\services\\calendar\\merge',
        6 => 'app\\services\\calendar\\unpublishedtimetables',
        7 => 'app\\services\\calendar\\ungradeditems',
        8 => 'app\\services\\calendar\\unpublishedexamresults',
      ),
      3 => 
      array (
      ),
    ),
    '/var/www/html/app/Services/Calendar/SchoolCalendar.php' => 
    array (
      0 => '6dc07a5e5d4c94e94a55da05a71665658782a6c1c85f92e934e746b91ae47a20',
      1 => 
      array (
        0 => 'app\\services\\calendar\\schoolcalendar',
      ),
      2 => 
      array (
        0 => 'app\\services\\calendar\\between',
        1 => 'app\\services\\calendar\\isteachingday',
        2 => 'app\\services\\calendar\\closures',
        3 => 'app\\services\\calendar\\limittoperson',
      ),
      3 => 
      array (
      ),
    ),
    '/var/www/html/app/Services/Curriculum/InstructionalModelResolver.php' => 
    array (
      0 => '351361d0a045ccdbe18248ca560ee6e71dbcfbe2883f115ce1bd37757a9ac22a',
      1 => 
      array (
        0 => 'app\\services\\curriculum\\instructionalmodelresolver',
      ),
      2 => 
      array (
        0 => 'app\\services\\curriculum\\for',
        1 => 'app\\services\\curriculum\\settingfor',
        2 => 'app\\services\\curriculum\\ischosen',
        3 => 'app\\services\\curriculum\\forget',
        4 => 'app\\services\\curriculum\\academicyearid',
        5 => 'app\\services\\curriculum\\schoolid',
      ),
      3 => 
      array (
      ),
    ),
    '/var/www/html/app/Services/Exam/ExamRecordService.php' => 
    array (
      0 => 'db8c087aa77f13f7b5c3762fd7796c96db43db35598909176348d37af9c4e60b',
      1 => 
      array (
        0 => 'app\\services\\exam\\examrecordservice',
      ),
      2 => 
      array (
        0 => 'app\\services\\exam\\__construct',
        1 => 'app\\services\\exam\\getallexamrecordsinsectionandsubject',
        2 => 'app\\services\\exam\\getallexamrecordsinsection',
        3 => 'app\\services\\exam\\getalluserexamrecordinexamforsubject',
        4 => 'app\\services\\exam\\getalluserexamrecordinacademicperiodforsubject',
        5 => 'app\\services\\exam\\getalluserexamrecordinacademicyear',
        6 => 'app\\services\\exam\\getalluserexamrecordinacademicperiod',
        7 => 'app\\services\\exam\\getallexamslotsinexams',
        8 => 'app\\services\\exam\\createexamrecord',
      ),
      3 => 
      array (
      ),
    ),
    '/var/www/html/app/Services/Exam/ExamService.php' => 
    array (
      0 => 'cc9bf20d8416cf9fa5a5f095bcd9a8a23ef7a6065e776f93cd6cd2f352cbc5c8',
      1 => 
      array (
        0 => 'app\\services\\exam\\examservice',
      ),
      2 => 
      array (
        0 => 'app\\services\\exam\\__construct',
        1 => 'app\\services\\exam\\getallexamsinacademicperiod',
        2 => 'app\\services\\exam\\getactiveexamsinacademicperiod',
        3 => 'app\\services\\exam\\getexambyid',
        4 => 'app\\services\\exam\\createexam',
        5 => 'app\\services\\exam\\updateexam',
        6 => 'app\\services\\exam\\setexamactivestatus',
        7 => 'app\\services\\exam\\setpublishresultstatus',
        8 => 'app\\services\\exam\\deleteexam',
        9 => 'app\\services\\exam\\totalmarksattainableinacademicperiodforsubject',
        10 => 'app\\services\\exam\\calculatestudenttotalmarksinsubject',
      ),
      3 => 
      array (
      ),
    ),
    '/var/www/html/app/Services/Exam/ExamSlotService.php' => 
    array (
      0 => 'af469435524678d78abd1eb0b47d9d8530e04cb850d4312d4dfc78964d48e1e3',
      1 => 
      array (
        0 => 'app\\services\\exam\\examslotservice',
      ),
      2 => 
      array (
        0 => 'app\\services\\exam\\getallexamslots',
        1 => 'app\\services\\exam\\getexamslotbyid',
        2 => 'app\\services\\exam\\createexamslot',
        3 => 'app\\services\\exam\\updateexamslot',
        4 => 'app\\services\\exam\\deleteexamslot',
      ),
      3 => 
      array (
      ),
    ),
    '/var/www/html/app/Services/Feature/FeatureManager.php' => 
    array (
      0 => '4f09afd1717ec20632726ed1419a481ed2fd09afa9830dca650a125bdb857bed',
      1 => 
      array (
        0 => 'app\\services\\feature\\featuremanager',
      ),
      2 => 
      array (
        0 => 'app\\services\\feature\\__construct',
        1 => 'app\\services\\feature\\enabled',
        2 => 'app\\services\\feature\\disabled',
        3 => 'app\\services\\feature\\config',
        4 => 'app\\services\\feature\\enable',
        5 => 'app\\services\\feature\\disable',
        6 => 'app\\services\\feature\\all',
        7 => 'app\\services\\feature\\forget',
        8 => 'app\\services\\feature\\set',
        9 => 'app\\services\\feature\\resolve',
        10 => 'app\\services\\feature\\settingfor',
        11 => 'app\\services\\feature\\schoolid',
      ),
      3 => 
      array (
      ),
    ),
    '/var/www/html/app/Services/Fee/FeeCategoryService.php' => 
    array (
      0 => '26b56506adf9f3904798f3708c792e550eb402cff2340ee2806f013310f83c85',
      1 => 
      array (
        0 => 'app\\services\\fee\\feecategoryservice',
      ),
      2 => 
      array (
        0 => 'app\\services\\fee\\storefeecategory',
        1 => 'app\\services\\fee\\updatefeecategory',
        2 => 'app\\services\\fee\\deletefeecategory',
      ),
      3 => 
      array (
      ),
    ),
    '/var/www/html/app/Services/Fee/FeeInvoiceRecordService.php' => 
    array (
      0 => 'f1d4a7e7ec7089faa88972152ee894e2a626ca70a9d133bff03a857c95d8592f',
      1 => 
      array (
        0 => 'app\\services\\fee\\feeinvoicerecordservice',
      ),
      2 => 
      array (
        0 => 'app\\services\\fee\\storefeeinvoicerecord',
        1 => 'app\\services\\fee\\updatefeeinvoicerecord',
        2 => 'app\\services\\fee\\deletefeeinvoicerecord',
        3 => 'app\\services\\fee\\addpayment',
        4 => 'app\\services\\fee\\ispaymenthigherthandue',
      ),
      3 => 
      array (
      ),
    ),
    '/var/www/html/app/Services/Fee/FeeInvoiceService.php' => 
    array (
      0 => '042a034efcfa2403cd9b77818c97d3be454dccc07ef416d530c6a8bce3d1c8b9',
      1 => 
      array (
        0 => 'app\\services\\fee\\feeinvoiceservice',
      ),
      2 => 
      array (
        0 => 'app\\services\\fee\\__construct',
        1 => 'app\\services\\fee\\storefeeinvoice',
        2 => 'app\\services\\fee\\chargethestudent',
        3 => 'app\\services\\fee\\updatefeeinvoice',
        4 => 'app\\services\\fee\\generateinvoicenumber',
        5 => 'app\\services\\fee\\printfeeinvoice',
        6 => 'app\\services\\fee\\deletefeeinvoice',
      ),
      3 => 
      array (
      ),
    ),
    '/var/www/html/app/Services/Fee/FeeService.php' => 
    array (
      0 => '9bc061ed18398006e19da612f5895c845d995db334fda0051d6b1ae19d0ea495',
      1 => 
      array (
        0 => 'app\\services\\fee\\feeservice',
      ),
      2 => 
      array (
        0 => 'app\\services\\fee\\storefee',
        1 => 'app\\services\\fee\\updatefee',
        2 => 'app\\services\\fee\\deletefee',
      ),
      3 => 
      array (
      ),
    ),
    '/var/www/html/app/Services/Finance/ChartOfAccounts.php' => 
    array (
      0 => '373ca0b60ed0c93c37a741901cb3371be2e7433664d1997912f2ee18f5b3695c',
      1 => 
      array (
        0 => 'app\\services\\finance\\chartofaccounts',
      ),
      2 => 
      array (
        0 => 'app\\services\\finance\\ensurefor',
        1 => 'app\\services\\finance\\account',
        2 => 'app\\services\\finance\\purposes',
      ),
      3 => 
      array (
      ),
    ),
    '/var/www/html/app/Services/Finance/StudentLedger.php' => 
    array (
      0 => '2ad86eeaa19ece694b09ab592dcef872ffd75361e13f6355b6163e5c96ed2071',
      1 => 
      array (
        0 => 'app\\services\\finance\\studentledger',
      ),
      2 => 
      array (
        0 => 'app\\services\\finance\\__construct',
        1 => 'app\\services\\finance\\balance',
        2 => 'app\\services\\finance\\unappliedcredit',
        3 => 'app\\services\\finance\\statement',
        4 => 'app\\services\\finance\\balanceon',
      ),
      3 => 
      array (
      ),
    ),
    '/var/www/html/app/Services/GradeSystem/GradeSystemService.php' => 
    array (
      0 => 'f70720b0dc2e29920e778a74e480de4b0400f5cf6eb09d7a0f76afa24d1ebb2e',
      1 => 
      array (
        0 => 'app\\services\\gradesystem\\gradesystemservice',
      ),
      2 => 
      array (
        0 => 'app\\services\\gradesystem\\getallgradesinclassgroup',
        1 => 'app\\services\\gradesystem\\getgrade',
        2 => 'app\\services\\gradesystem\\creategradesystem',
        3 => 'app\\services\\gradesystem\\updategradesystem',
        4 => 'app\\services\\gradesystem\\deletegradesystem',
        5 => 'app\\services\\gradesystem\\graderangeexists',
      ),
      3 => 
      array (
      ),
    ),
    '/var/www/html/app/Services/Gradebook/GradebookCalculator.php' => 
    array (
      0 => '6d3ea7b23754393e05f086042ca0c4e30535086d0bb71a0ca10e08e151f64fba',
      1 => 
      array (
        0 => 'app\\services\\gradebook\\gradebookcalculator',
      ),
      2 => 
      array (
        0 => 'app\\services\\gradebook\\calculate',
        1 => 'app\\services\\gradebook\\rowsfor',
        2 => 'app\\services\\gradebook\\aggregate',
        3 => 'app\\services\\gradebook\\aggregategroup',
        4 => 'app\\services\\gradebook\\sumshare',
      ),
      3 => 
      array (
      ),
    ),
    '/var/www/html/app/Services/Graduation/GraduationProgress.php' => 
    array (
      0 => 'c63c4e35dcefae0680eb7c766f5a3be500ae129f9b4dc69f8e875bac940c50ee',
      1 => 
      array (
        0 => 'app\\services\\graduation\\graduationprogress',
      ),
      2 => 
      array (
        0 => 'app\\services\\graduation\\for',
        1 => 'app\\services\\graduation\\iscomplete',
        2 => 'app\\services\\graduation\\stateof',
        3 => 'app\\services\\graduation\\resultfor',
      ),
      3 => 
      array (
      ),
    ),
    '/var/www/html/app/Services/Identity/AccountInvitationVisibility.php' => 
    array (
      0 => 'b253322a5ab3d3c69b5f95fc9ae2af08b21432852420b4cb1b2491b224c2efe7',
      1 => 
      array (
        0 => 'app\\services\\identity\\accountinvitationvisibility',
      ),
      2 => 
      array (
        0 => 'app\\services\\identity\\__construct',
        1 => 'app\\services\\identity\\allowsany',
        2 => 'app\\services\\identity\\schoolidsfor',
        3 => 'app\\services\\identity\\query',
        4 => 'app\\services\\identity\\allows',
        5 => 'app\\services\\identity\\schoolnamesfor',
        6 => 'app\\services\\identity\\organizationschoolids',
      ),
      3 => 
      array (
      ),
    ),
    '/var/www/html/app/Services/Import/CsvReader.php' => 
    array (
      0 => '2b1f681ead0b1785b97f3807f86f0d7fdcb53e2d68dec057dc0fd26c1941021e',
      1 => 
      array (
        0 => 'app\\services\\import\\csvreader',
      ),
      2 => 
      array (
        0 => 'app\\services\\import\\read',
        1 => 'app\\services\\import\\parse',
      ),
      3 => 
      array (
      ),
    ),
    '/var/www/html/app/Services/Import/ImportRegistry.php' => 
    array (
      0 => '14e401ea90300e0a19615ccb54d2c1c4b626fd4d122e6865d1ae34e40904bfb7',
      1 => 
      array (
        0 => 'app\\services\\import\\importregistry',
      ),
      2 => 
      array (
        0 => 'app\\services\\import\\get',
        1 => 'app\\services\\import\\all',
      ),
      3 => 
      array (
      ),
    ),
    '/var/www/html/app/Services/Import/ImportRunner.php' => 
    array (
      0 => 'e645727fb157bf6f4f3ebdba42480b7c3fca56fe5854d247340f90e21a649377',
      1 => 
      array (
        0 => 'app\\services\\import\\importrunner',
      ),
      2 => 
      array (
        0 => 'app\\services\\import\\__construct',
        1 => 'app\\services\\import\\stage',
        2 => 'app\\services\\import\\apply',
        3 => 'app\\services\\import\\cancel',
        4 => 'app\\services\\import\\write',
        5 => 'app\\services\\import\\check',
        6 => 'app\\services\\import\\sourceidof',
        7 => 'app\\services\\import\\failifcolumnsaremissing',
      ),
      3 => 
      array (
      ),
    ),
    '/var/www/html/app/Services/MyClass/MyClassService.php' => 
    array (
      0 => 'cc28d943bb4bdfed353573f5472cd092944a758c825ee4edc89f75c7712bb785',
      1 => 
      array (
        0 => 'app\\services\\myclass\\myclassservice',
      ),
      2 => 
      array (
        0 => 'app\\services\\myclass\\__construct',
        1 => 'app\\services\\myclass\\getallclasses',
        2 => 'app\\services\\myclass\\getallclassgroups',
        3 => 'app\\services\\myclass\\getclassbyid',
        4 => 'app\\services\\myclass\\getclassbyidorfail',
        5 => 'app\\services\\myclass\\getclassgroupbyid',
        6 => 'app\\services\\myclass\\createclass',
        7 => 'app\\services\\myclass\\createclassgroup',
        8 => 'app\\services\\myclass\\updateclass',
        9 => 'app\\services\\myclass\\updateclassgroup',
        10 => 'app\\services\\myclass\\deleteclassgroup',
        11 => 'app\\services\\myclass\\deleteclass',
      ),
      3 => 
      array (
      ),
    ),
    '/var/www/html/app/Services/Notice/NoticeAudience.php' => 
    array (
      0 => '89c6d3bcea73c8749cc2ac8ee7b5cd243fdecd5ccd35f439cfc093c90ef74fe8',
      1 => 
      array (
        0 => 'app\\services\\notice\\noticeaudience',
      ),
      2 => 
      array (
        0 => 'app\\services\\notice\\resolve',
        1 => 'app\\services\\notice\\userids',
      ),
      3 => 
      array (
      ),
    ),
    '/var/www/html/app/Services/Notice/NoticeService.php' => 
    array (
      0 => 'aa9c24cd42abdf1d534c85e826f361f99cfe1e7573d80a80f169987617486bf3',
      1 => 
      array (
        0 => 'app\\services\\notice\\noticeservice',
      ),
      2 => 
      array (
        0 => 'app\\services\\notice\\getallnotices',
        1 => 'app\\services\\notice\\getpresentnotices',
        2 => 'app\\services\\notice\\storenotice',
        3 => 'app\\services\\notice\\deletenotice',
      ),
      3 => 
      array (
      ),
    ),
    '/var/www/html/app/Services/Parent/ParentService.php' => 
    array (
      0 => '82c777d673c8ff6f34a329ff6b8deb6bfc5e5b37e57cea5ad5a1aa53bc652706',
      1 => 
      array (
        0 => 'app\\services\\parent\\parentservice',
      ),
      2 => 
      array (
        0 => 'app\\services\\parent\\__construct',
        1 => 'app\\services\\parent\\getallparents',
        2 => 'app\\services\\parent\\createparent',
        3 => 'app\\services\\parent\\updateparent',
        4 => 'app\\services\\parent\\deleteparent',
        5 => 'app\\services\\parent\\printprofile',
        6 => 'app\\services\\parent\\assignstudenttoparent',
      ),
      3 => 
      array (
      ),
    ),
    '/var/www/html/app/Services/Portal/PortalAccess.php' => 
    array (
      0 => '478e6773736390aed256830ed320d5a6eb676f193cc09f4ce44d1723b2a3662d',
      1 => 
      array (
        0 => 'app\\services\\portal\\portalaccess',
      ),
      2 => 
      array (
        0 => 'app\\services\\portal\\enrollmentsfor',
        1 => 'app\\services\\portal\\canread',
        2 => 'app\\services\\portal\\isopen',
        3 => 'app\\services\\portal\\areaisopen',
        4 => 'app\\services\\portal\\childuserids',
      ),
      3 => 
      array (
      ),
    ),
    '/var/www/html/app/Services/Portal/PortalSummary.php' => 
    array (
      0 => 'a9d15ea272016a8cc0df9094b17eede91af9d4bca026f13676e303234c107664',
      1 => 
      array (
        0 => 'app\\services\\portal\\portalsummary',
      ),
      2 => 
      array (
        0 => 'app\\services\\portal\\__construct',
        1 => 'app\\services\\portal\\results',
        2 => 'app\\services\\portal\\attendance',
        3 => 'app\\services\\portal\\timetable',
        4 => 'app\\services\\portal\\notices',
        5 => 'app\\services\\portal\\invoices',
      ),
      3 => 
      array (
      ),
    ),
    '/var/www/html/app/Services/Print/PrintService.php' => 
    array (
      0 => '4187b72cbad7a1fd7b2cfd8226896d830430efe625ebec885b421ad3ffb053d4',
      1 => 
      array (
        0 => 'app\\services\\print\\printservice',
      ),
      2 => 
      array (
        0 => 'app\\services\\print\\createpdffromview',
      ),
      3 => 
      array (
      ),
    ),
    '/var/www/html/app/Services/Ranking/ResultRanking.php' => 
    array (
      0 => '5b122c9bf10ab9f5ef7c9dffdbba76dc7928a86e5fd3889d03e131a412a8de30',
      1 => 
      array (
        0 => 'app\\services\\ranking\\resultranking',
      ),
      2 => 
      array (
        0 => 'app\\services\\ranking\\forcohort',
        1 => 'app\\services\\ranking\\forclass',
        2 => 'app\\services\\ranking\\rank',
        3 => 'app\\services\\ranking\\averages',
        4 => 'app\\services\\ranking\\withpositions',
      ),
      3 => 
      array (
      ),
    ),
    '/var/www/html/app/Services/Report/ReportRegistry.php' => 
    array (
      0 => 'ba89f9af6d2424a9772b8090fbf2fb92c0a718d23f1e4a643bc76d04da72b397',
      1 => 
      array (
        0 => 'app\\services\\report\\reportregistry',
      ),
      2 => 
      array (
        0 => 'app\\services\\report\\get',
        1 => 'app\\services\\report\\all',
      ),
      3 => 
      array (
      ),
    ),
    '/var/www/html/app/Services/School/SchoolContext.php' => 
    array (
      0 => 'fdb938b58eff3743b07a4ed60d523eb655eb967890a6fb9058f3fe2705c0ea5c',
      1 => 
      array (
        0 => 'app\\services\\school\\schoolcontext',
      ),
      2 => 
      array (
        0 => 'app\\services\\school\\__construct',
        1 => 'app\\services\\school\\school',
        2 => 'app\\services\\school\\id',
        3 => 'app\\services\\school\\has',
        4 => 'app\\services\\school\\schoolorfail',
        5 => 'app\\services\\school\\set',
        6 => 'app\\services\\school\\forget',
        7 => 'app\\services\\school\\resolvefor',
        8 => 'app\\services\\school\\isresolved',
        9 => 'app\\services\\school\\defaultschoolfor',
        10 => 'app\\services\\school\\schoolifallowed',
      ),
      3 => 
      array (
      ),
    ),
    '/var/www/html/app/Services/School/SchoolService.php' => 
    array (
      0 => 'cddd8df7b23971803c0544c4b1137700ec62162b2731f9e91434a2adc443eac3',
      1 => 
      array (
        0 => 'app\\services\\school\\schoolservice',
      ),
      2 => 
      array (
        0 => 'app\\services\\school\\__construct',
        1 => 'app\\services\\school\\getallschools',
        2 => 'app\\services\\school\\getschoolsforuser',
        3 => 'app\\services\\school\\getschoolbyid',
        4 => 'app\\services\\school\\createschool',
        5 => 'app\\services\\school\\updateschool',
        6 => 'app\\services\\school\\setschool',
        7 => 'app\\services\\school\\generateschoolcode',
        8 => 'app\\services\\school\\deleteschool',
      ),
      3 => 
      array (
      ),
    ),
    '/var/www/html/app/Services/Section/SectionService.php' => 
    array (
      0 => 'd70e753b78e6f67f544502f9e2a03ddaf02c196640baefb57babdf04cde4b220',
      1 => 
      array (
        0 => 'app\\services\\section\\sectionservice',
      ),
      2 => 
      array (
        0 => 'app\\services\\section\\__construct',
        1 => 'app\\services\\section\\getallsections',
        2 => 'app\\services\\section\\getsectionbyid',
        3 => 'app\\services\\section\\createsection',
        4 => 'app\\services\\section\\updatesection',
        5 => 'app\\services\\section\\deletesection',
      ),
      3 => 
      array (
      ),
    ),
    '/var/www/html/app/Services/Sharing/TransferPackageBuilder.php' => 
    array (
      0 => '1272c6877460ddf0d29f3b674804222d589f54dae6bbd558955e028307e7869f',
      1 => 
      array (
        0 => 'app\\services\\sharing\\transferpackagebuilder',
      ),
      2 => 
      array (
        0 => 'app\\services\\sharing\\__construct',
        1 => 'app\\services\\sharing\\build',
        2 => 'app\\services\\sharing\\partfor',
        3 => 'app\\services\\sharing\\identity',
        4 => 'app\\services\\sharing\\guardians',
        5 => 'app\\services\\sharing\\enrollment',
        6 => 'app\\services\\sharing\\results',
        7 => 'app\\services\\sharing\\health',
        8 => 'app\\services\\sharing\\discipline',
        9 => 'app\\services\\sharing\\safeguarding',
        10 => 'app\\services\\sharing\\incidents',
        11 => 'app\\services\\sharing\\wellbeing',
        12 => 'app\\services\\sharing\\finance',
      ),
      3 => 
      array (
      ),
    ),
    '/var/www/html/app/Services/Staff/StaffAvailability.php' => 
    array (
      0 => 'f0c0d4c3399f4f337fc04f198c26de28db3143a922b8d4e40547ab1a4ad65aa6',
      1 => 
      array (
        0 => 'app\\services\\staff\\staffavailability',
      ),
      2 => 
      array (
        0 => 'app\\services\\staff\\isfree',
        1 => 'app\\services\\staff\\isaway',
        2 => 'app\\services\\staff\\awayon',
        3 => 'app\\services\\staff\\freeon',
      ),
      3 => 
      array (
      ),
    ),
    '/var/www/html/app/Services/Student/StudentService.php' => 
    array (
      0 => '0e9d2dcda1f5b66b7b89dbcf424fd015f8c1e6d344647637565eedfc2879be9a',
      1 => 
      array (
        0 => 'app\\services\\student\\studentservice',
      ),
      2 => 
      array (
        0 => 'app\\services\\student\\__construct',
        1 => 'app\\services\\student\\getallstudents',
        2 => 'app\\services\\student\\getallactivestudents',
        3 => 'app\\services\\student\\getallgraduatedstudents',
        4 => 'app\\services\\student\\getstudentbyid',
        5 => 'app\\services\\student\\createstudent',
        6 => 'app\\services\\student\\createstudentrecord',
        7 => 'app\\services\\student\\updatestudent',
        8 => 'app\\services\\student\\deletestudent',
        9 => 'app\\services\\student\\generateadmissionnumber',
        10 => 'app\\services\\student\\printprofile',
        11 => 'app\\services\\student\\promotestudents',
        12 => 'app\\services\\student\\getallpromotions',
        13 => 'app\\services\\student\\getpromotionsbyacademicyearid',
        14 => 'app\\services\\student\\resetpromotion',
        15 => 'app\\services\\student\\graduatestudents',
        16 => 'app\\services\\student\\resetgraduation',
      ),
      3 => 
      array (
      ),
    ),
    '/var/www/html/app/Services/Subject/SubjectService.php' => 
    array (
      0 => '831f7bb97f49ce7a00bcc6e4061f056e64150188d96d3a20391209798a218a27',
      1 => 
      array (
        0 => 'app\\services\\subject\\subjectservice',
      ),
      2 => 
      array (
        0 => 'app\\services\\subject\\__construct',
        1 => 'app\\services\\subject\\getallsubjects',
        2 => 'app\\services\\subject\\getsubjectbyid',
        3 => 'app\\services\\subject\\createsubject',
        4 => 'app\\services\\subject\\updatesubject',
        5 => 'app\\services\\subject\\syncteachers',
        6 => 'app\\services\\subject\\deletesubject',
        7 => 'app\\services\\subject\\assignteachertosubjects',
      ),
      3 => 
      array (
      ),
    ),
    '/var/www/html/app/Services/Syllabus/SyllabusService.php' => 
    array (
      0 => '4a31e2d2634a27d88ab459c481dc35c45bd760e77bad322440da34f077ab01d3',
      1 => 
      array (
        0 => 'app\\services\\syllabus\\syllabusservice',
      ),
      2 => 
      array (
        0 => 'app\\services\\syllabus\\getallsyllabiinacademicperiodandclass',
        1 => 'app\\services\\syllabus\\getsyllabusbyid',
        2 => 'app\\services\\syllabus\\createsyllabus',
        3 => 'app\\services\\syllabus\\updatesyllabus',
        4 => 'app\\services\\syllabus\\deletesyllabus',
      ),
      3 => 
      array (
      ),
    ),
    '/var/www/html/app/Services/Teacher/TeacherService.php' => 
    array (
      0 => '45800aee2827f38f71c1934e8fa2ae2046de9c56830c32cf3dbff9bdf5fb9d4a',
      1 => 
      array (
        0 => 'app\\services\\teacher\\teacherservice',
      ),
      2 => 
      array (
        0 => 'app\\services\\teacher\\__construct',
        1 => 'app\\services\\teacher\\getallteachers',
        2 => 'app\\services\\teacher\\createteacher',
        3 => 'app\\services\\teacher\\updateteacher',
        4 => 'app\\services\\teacher\\deleteteacher',
        5 => 'app\\services\\teacher\\printprofile',
      ),
      3 => 
      array (
      ),
    ),
    '/var/www/html/app/Services/Timetable/TimeSlotService.php' => 
    array (
      0 => '135e20d6c261bced78876a9fd180537ef378f16d94d797b33fa67dceed0429f5',
      1 => 
      array (
        0 => 'app\\services\\timetable\\timeslotservice',
      ),
      2 => 
      array (
        0 => 'app\\services\\timetable\\createtimeslot',
        1 => 'app\\services\\timetable\\deletetimeslot',
        2 => 'app\\services\\timetable\\createtimetablerecord',
      ),
      3 => 
      array (
      ),
    ),
    '/var/www/html/app/Services/Timetable/TimetableConflictChecker.php' => 
    array (
      0 => '0000dab36c6f852d09399fa738bfe933d3a5f3bfc2e6e6c37df504e5ad2dc6da',
      1 => 
      array (
        0 => 'app\\services\\timetable\\timetableconflictchecker',
      ),
      2 => 
      array (
        0 => 'app\\services\\timetable\\conflicts',
        1 => 'app\\services\\timetable\\overlappingslots',
        2 => 'app\\services\\timetable\\teacherclashes',
        3 => 'app\\services\\timetable\\entriesof',
        4 => 'app\\services\\timetable\\overlaps',
      ),
      3 => 
      array (
      ),
    ),
    '/var/www/html/app/Services/Timetable/TimetableService.php' => 
    array (
      0 => '1f583ecc96fc392a660ab888ec4d9026766a4ea42eab98a739c344e5ee26d22e',
      1 => 
      array (
        0 => 'app\\services\\timetable\\timetableservice',
      ),
      2 => 
      array (
        0 => 'app\\services\\timetable\\getalltimetablesinacademicperiodandclass',
        1 => 'app\\services\\timetable\\createtimetable',
        2 => 'app\\services\\timetable\\updatetimetable',
        3 => 'app\\services\\timetable\\printtimetable',
        4 => 'app\\services\\timetable\\deletetimetable',
        5 => 'app\\services\\timetable\\getallcustomtimetableitem',
        6 => 'app\\services\\timetable\\createcustomtimetableitem',
        7 => 'app\\services\\timetable\\updatecustomtimetableitem',
        8 => 'app\\services\\timetable\\deletecustomtimetableitem',
      ),
      3 => 
      array (
      ),
    ),
    '/var/www/html/app/Services/User/UserService.php' => 
    array (
      0 => 'f3e2a31b1eed00391e43a86c70aaf2f89b20cc1be9f0b72cfcabc66601027c62',
      1 => 
      array (
        0 => 'app\\services\\user\\userservice',
      ),
      2 => 
      array (
        0 => 'app\\services\\user\\__construct',
        1 => 'app\\services\\user\\getallusers',
        2 => 'app\\services\\user\\getuserbyid',
        3 => 'app\\services\\user\\getusersbyrole',
        4 => 'app\\services\\user\\createuser',
        5 => 'app\\services\\user\\verifyrole',
        6 => 'app\\services\\user\\updateuser',
        7 => 'app\\services\\user\\deleteuser',
        8 => 'app\\services\\user\\verifyuserisofroleelsenotfound',
        9 => 'app\\services\\user\\suspenduseraccount',
        10 => 'app\\services\\user\\reinstateuseraccount',
      ),
      3 => 
      array (
      ),
    ),
    '/var/www/html/app/Traits/EnvEditorTrait.php' => 
    array (
      0 => 'cf7284cfdff587595c9a8f33d22275a0db161254808d5afd04bd3b0446a7fa21',
      1 => 
      array (
        0 => 'app\\traits\\enveditortrait',
      ),
      2 => 
      array (
        0 => 'app\\traits\\setenvironmentvalue',
      ),
      3 => 
      array (
      ),
    ),
    '/var/www/html/app/Traits/FeatureTestTrait.php' => 
    array (
      0 => '36d65e259dcdf87b615bfce73238280982294e2ce261ec25ba87eb8e9b619616',
      1 => 
      array (
        0 => 'app\\traits\\featuretesttrait',
      ),
      2 => 
      array (
        0 => 'app\\traits\\unauthorized_user',
        1 => 'app\\traits\\authorized_user',
        2 => 'app\\traits\\platform_admin',
        3 => 'app\\traits\\memberof',
        4 => 'app\\traits\\actingasmemberof',
        5 => 'app\\traits\\nonmember',
        6 => 'app\\traits\\workingschool',
      ),
      3 => 
      array (
      ),
    ),
    '/var/www/html/app/Traits/HasPeriodLifecycle.php' => 
    array (
      0 => 'b4e38dc353ad6742199d90621eaee18623bc21e5340f17e59ab23744458bd7a8',
      1 => 
      array (
        0 => 'app\\traits\\hasperiodlifecycle',
      ),
      2 => 
      array (
        0 => 'app\\traits\\statuschanges',
        1 => 'app\\traits\\scopeopen',
        2 => 'app\\traits\\scopeclosed',
        3 => 'app\\traits\\scopearchived',
        4 => 'app\\traits\\scopeoperational',
        5 => 'app\\traits\\scopescheduled',
        6 => 'app\\traits\\isopen',
        7 => 'app\\traits\\isclosed',
        8 => 'app\\traits\\isclosing',
        9 => 'app\\traits\\isarchived',
        10 => 'app\\traits\\isoperational',
        11 => 'app\\traits\\acceptsnewwork',
        12 => 'app\\traits\\statuslabel',
      ),
      3 => 
      array (
      ),
    ),
    '/var/www/html/app/Traits/InAcademicPeriod.php' => 
    array (
      0 => '22bae45352bb80948e9f9098168c2b5f03b7e0db2fab9c12c4306cb75de989d4',
      1 => 
      array (
        0 => 'app\\traits\\inacademicperiod',
      ),
      2 => 
      array (
        0 => 'app\\traits\\bootinacademicperiod',
        1 => 'app\\traits\\failifperiodisclosed',
        2 => 'app\\traits\\governingacademicperiod',
      ),
      3 => 
      array (
      ),
    ),
    '/var/www/html/app/Traits/InSchool.php' => 
    array (
      0 => 'bf37c21e75a8f1165ff5096fc5119ead4250f122e1cdb314fda7205e61ca183c',
      1 => 
      array (
        0 => 'app\\traits\\inschool',
      ),
      2 => 
      array (
        0 => 'app\\traits\\bootinschool',
        1 => 'app\\traits\\scopeinschool',
      ),
      3 => 
      array (
      ),
    ),
    '/var/www/html/app/Traits/MarkTabulationTrait.php' => 
    array (
      0 => '6d6681ccf0f1107c43411f44ccead77a1fe25fa281a64d78c7b1536fb0dae3af',
      1 => 
      array (
        0 => 'app\\traits\\marktabulationtrait',
      ),
      2 => 
      array (
        0 => 'app\\traits\\tabulatemarks',
      ),
      3 => 
      array (
      ),
    ),
    '/var/www/html/app/View/Components/InfoBox.php' => 
    array (
      0 => '87abfb84a01fbe402540707065b16f57f374488b2f9261545ee160cb7af3d098',
      1 => 
      array (
        0 => 'app\\view\\components\\infobox',
      ),
      2 => 
      array (
        0 => 'app\\view\\components\\__construct',
        1 => 'app\\view\\components\\render',
      ),
      3 => 
      array (
      ),
    ),
    '/var/www/html/app/helpers.php' => 
    array (
      0 => 'fd035a4b91db169634dd3b229502ec5b41ffd5560e5c7c387839802bf5a2d5ca',
      1 => 
      array (
      ),
      2 => 
      array (
        0 => 'school_context',
        1 => 'current_school',
        2 => 'current_school_id',
        3 => 'academic_period_context',
        4 => 'current_academic_year',
        5 => 'current_academic_year_id',
        6 => 'current_academic_period',
        7 => 'current_academic_period_id',
        8 => 'features',
        9 => 'instructional_model',
        10 => 'feature_enabled',
        11 => 'sidebar_open',
      ),
      3 => 
      array (
      ),
    ),
  ),
));