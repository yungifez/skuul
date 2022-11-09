<?php 

namespace App\Services\AccountApplication;

use App\Models\User;
use App\Models\AccountApplication;
use App\Services\User\UserService;
use App\Services\Student\StudentService;

class AccountApplicationService  
{
    /**
     * User service instance
     *
     * @var UserService
     */
    public UserService $userService;

    /**
     * Student service instance
     *
     * @var StudentService
     */
    public StudentService $studentService;

    public function __construct(UserService $userService, StudentService $studentService)
    {
        $this->userService = $userService;
        $this->studentService = $studentService;
    }

    /**
     * Get all open applicants application records in the school
     *
     * @return void
     */
    public function getAllOpenApplicantsAndApplicationRecords()
    {
        return $this->userService->getUsersByRole('applicant')->load('accountApplication', 'accountApplication.statuses')->filter(function($applicant){
            $status = $applicant->accountApplication->status ?? null;
            
            if ($status != 'rejected') {
                return true;
            }else{
                return false;
            }
        });
    }

     /**
     * Get all  applicants application records in the school
     *
     * @return void
     */
    public function getAllRejectedApplicantsAndApplicationRecords()
    {
        return $this->userService->getUsersByRole('applicant')->load('accountApplication', 'accountApplication.statuses')->filter(function($applicant){
            $status = $applicant->accountApplication->status ?? null;
            
            if ($status == 'rejected') {
                return true;
            }else{
                return false;
            }
        });
    }
    
    /**
     * Create application record
     *
     * @param integer $userId
     * @param integer $roleId
     * 
     * @return AccountApplication
     */
    public function createAccountApplication(int $userId,int $roleId)
    {
        return AccountApplication::create([
            'role_id' => $roleId,
            'user_id' => $userId
        ]);
    }

    /**
     * Update account application
     *
     * @param User $user
     * @param object $record
     * @return void
     */
    public function updateAccountApplication(User $applicant, object|array $record)
    {
        $applicant = $this->userService->updateUser($applicant, $record, 'applicant');
        
        //create record if record doesn't exist somehow else update
        $applicant->accountApplication()->updateOrCreate([],[
            'role_id' => $record['role_id'],
        ]);
        
        session()->flash('success', 'Application updated successfully');

        return;
    }

    /**
     * Change application status or process accountcreation
     *
     * @param User $applicant
     * @param $record
     * @return void
     */
    public function changeStatus(User $applicant, $record)
    {
        $applicant->accountApplication->setStatus($record['status'], $record['reason'] ?? null );

        if ($applicant->accountApplication->status == 'approved') {

            //create assosciated user records
            switch ($applicant->accountApplication->role->name) {
                case 'student':
                    $this->studentService->createStudentRecord($applicant, $record); 
                    break;
                case 'parent':
                    $applicant->parentRecord()->create(); 
                    break;
                case 'teacher':
                    $applicant->teacherRecord()->create();
                    break; 
            }

            //add supplied role and delete application record
            $applicant->syncRoles([$applicant->accountApplication->role->name]);
            $applicant->accountApplication->delete();

            session()->flash('success' , 'Application approved successfully');
        }else{
            session()->flash('success' , 'Application status changed successfully');
        } 

        return;
    }

    /**
     * Delete user and application
     *
     * @param User $applicant
     * @return void
     */
    public function deleteAccountApplicant(User $applicant)
    {
        $applicant->delete();
        session()->flash('success', 'Student Deleted Successfully');

        return;
    }
}
