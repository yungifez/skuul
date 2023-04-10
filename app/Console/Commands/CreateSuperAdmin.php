<?php

namespace App\Console\Commands;

use App\Actions\Fortify\PasswordValidationRules;
use App\Models\User;
use Illuminate\Console\Command;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Facades\Validator;

class CreateSuperAdmin extends Command
{
    use PasswordValidationRules;

    /**
     * The name and signature of the console command.
     *
     * @var string
     */
    protected $signature = 'skuul:create-super-admin';

    /**
     * The console command description.
     *
     * @var string
     */
    protected $description = 'Creates a super admin';

    /**
     * Execute the console command.
     *
     * @return int
     */
    public function handle()
    {
        try {
            $this->alert('Creating super admin');
            $this->info('Fill in the following details. You can modify rest of profile at a later stage. Values can not be null');
            do {
                // code...

                //don't allow null values hence do while
                $firstName = null;
                do {
                    $firstName = $this->ask('First Name');
                } while (is_null($firstName));
                $lastName = null;
                do {
                    $lastName = $this->ask('Last Name');
                } while (is_null($lastName));
                $email = null;
                do {
                    $email = $this->ask('Email');
                } while (is_null($email));
                do {
                    $password = $this->secret('Password');
                } while (is_null($password));
                do {
                    $passwordConfirmation = $this->secret('Confirm Password');
                } while (is_null($passwordConfirmation));

                //validate the input
                $validator = Validator::make([
                    'first_name'            => $firstName,
                    'last_name'             => $lastName,
                    'email'                 => $email,
                    'password'              => $password,
                    'password_confirmation' => $passwordConfirmation,
                ], [
                    'first_name' => ['required', 'string', 'max:511'],
                    'last_name'  => ['required', 'string', 'max:511'],
                    'email'      => ['required', 'string', 'email', 'max:511', 'unique:users'],
                    'password'   => $this->passwordRules(),
                ]);

                //display validation error
                foreach ($validator->errors()->all() as $error) {
                    $this->error($error);
                }
            } while ($validator->fails());

            //create super admin
            $superAdmin = User::firstOrCreate([
                'name'        => "$firstName $lastName",
                'email'       => $email,
                'password'    => Hash::make($password),
                'address'     => 'super admin street',
                'birthday'    => '22/04/04',
                'nationality' => 'nigeria',
                'state'       => 'lagos',
                'city'        => 'lagos',
                'blood_group' => 'A+',
                'gender'      => 'male',
            ]);

            //assign role
            $superAdmin->assignRole('super-admin');
            $superAdmin->save();

            $this->line('Created super admin successfully');
        } catch (\Throwable $th) {
            $this->error("Could not create super admin \n".$th);
        }

        return 0;
    }
}
