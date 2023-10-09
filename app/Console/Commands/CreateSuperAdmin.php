<?php

namespace App\Console\Commands;

use App\Actions\Fortify\PasswordValidationRules;
use App\Models\User;
use Illuminate\Console\Command;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Facades\Validator;

use function Laravel\Prompts\password;
use function Laravel\Prompts\text;

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
            $this->newLine();
            $this->alert('Creating super admin');
            $this->info('Fill in the following details. You can modify the profile later with other information.');
            do {
                //don't allow null values hence do while
                $firstName = text('First name?', required: true, validate: fn (string $value) => match (true) {
                    strlen($value) < 3   => 'The name must be at least 3 characters.',
                    strlen($value) > 255 => 'The name must not exceed 255 characters.',
                    default              => null
                });
                $lastName = text('Last name?', required: true, validate: fn (string $value) => match (true) {
                    strlen($value) < 3   => 'The name must be at least 3 characters.',
                    strlen($value) > 255 => 'The name must not exceed 255 characters.',
                    default              => null
                });
                $email = text('Email?', required: true, validate: fn (string $value) => match (true) {
                    strlen($value) < 3                                  => 'The email must be at least 3 characters.',
                    strlen($value) > 511                                => 'The email must not exceed 255 characters.',
                    filter_var($value, FILTER_VALIDATE_EMAIL) === false => 'The email must be a valid email address.',
                    default                                             => null
                });
                $password = password(
                    'What is your password?',
                    required:true,
                    placeholder: 'Minimum 8 characters...',
                    validate: fn (string $value) => match (true) {
                        strlen($value) < 8 => 'The password must be at least 8 characters.',
                        default            => null
                    }
                );
                $passwordConfirmation = password(
                    'Confirm your password?',
                    required:true,
                    placeholder: 'Input the same password...',
                    validate: fn (string $value) => match (true) {
                        $value !== $password => 'The password confirmation does not match.',
                        default              => null
                    }
                );

                $validator = Validator::make([
                    'first_name'            => $firstName,
                    'last_name'             => $lastName,
                    'email'                 => $email,
                    'password'              => $password,
                    'password_confirmation' => $passwordConfirmation,
                ], [
                    'first_name' => ['required', 'string', 'max:255'],
                    'last_name'  => ['required', 'string', 'max:255'],
                    'email'      => ['required', 'string', 'email', 'max:511', 'unique:users'],
                    'password'   => $this->passwordRules(),
                ]);

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
                'birthday'    => '1/1/1970',
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
