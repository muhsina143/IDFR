<?php

namespace App\Console\Commands;

use App\Models\AdminActivity;
use App\Models\User;
use Illuminate\Console\Command;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Facades\Validator;

class CreateAdminCommand extends Command
{
    /**
     * The name and signature of the console command.
     *
     * @var string
     */
    protected $signature = 'admin:create
                            {--name= : Name of the administrator}
                            {--email= : Email address for the administrator}
                            {--password= : Password for the administrator}
                            {--update : Update existing account if email is already taken}';

    /**
     * The console command description.
     *
     * @var string
     */
    protected $description = 'Create or update a secure IDFR Administrator account';

    /**
     * Execute the console command.
     */
    public function handle(): int
    {
        $this->info('==========================================');
        $this->info('  IDFR FASHION RUNWAY — ADMIN ACCOUNT SETUP');
        $this->info('==========================================');

        $email = $this->option('email') ?: $this->ask('Enter Administrator Email');
        $email = strtolower(trim($email));

        $existingUser = User::where('email', $email)->first();

        if ($existingUser) {
            $shouldUpdate = $this->option('update') || $this->confirm("Account [{$email}] already exists. Would you like to update its password and admin role?", false);

            if (! $shouldUpdate) {
                $this->error("Account with email [{$email}] already exists. Operation aborted.");
                $this->line("Tip: Use --update flag or run 'php artisan admin:reset-password' to update an existing administrator.");

                return self::FAILURE;
            }

            $name = $this->option('name') ?: $this->ask('Enter Administrator Name', $existingUser->name);
            $password = $this->option('password') ?: $this->secret('Enter New Administrator Password (minimum 8 characters)');

            $validator = Validator::make([
                'name' => $name,
                'email' => $email,
                'password' => $password,
            ], [
                'name' => ['required', 'string', 'max:255'],
                'email' => ['required', 'string', 'email', 'max:255'],
                'password' => ['required', 'string', 'min:8'],
            ]);

            if ($validator->fails()) {
                $this->error('Failed to update administrator due to validation errors:');
                foreach ($validator->errors()->all() as $error) {
                    $this->line("  • {$error}");
                }

                return self::FAILURE;
            }

            $existingUser->update([
                'name' => $name,
                'password' => Hash::make($password),
                'is_admin' => true,
                'role' => 'admin',
            ]);

            AdminActivity::log(
                'admin_updated',
                $existingUser,
                $existingUser->email,
                "Administrator {$existingUser->name} updated via artisan admin:create"
            );

            $this->newLine();
            $this->info('✓ Administrator account updated successfully!');
            $this->table(
                ['ID', 'Name', 'Email', 'Role', 'Is Admin'],
                [[$existingUser->id, $existingUser->name, $existingUser->email, $existingUser->role, 'Yes']]
            );

            return self::SUCCESS;
        }

        $name = $this->option('name') ?: $this->ask('Enter Administrator Name');
        $password = $this->option('password') ?: $this->secret('Enter Administrator Password (minimum 8 characters)');

        $validator = Validator::make([
            'name' => $name,
            'email' => $email,
            'password' => $password,
        ], [
            'name' => ['required', 'string', 'max:255'],
            'email' => ['required', 'string', 'email', 'max:255', 'unique:users,email'],
            'password' => ['required', 'string', 'min:8'],
        ]);

        if ($validator->fails()) {
            $this->error('Failed to create administrator due to validation errors:');
            foreach ($validator->errors()->all() as $error) {
                $this->line("  • {$error}");
            }

            return self::FAILURE;
        }

        $user = User::create([
            'name' => $name,
            'email' => $email,
            'password' => Hash::make($password),
            'is_admin' => true,
            'role' => 'admin',
        ]);

        AdminActivity::log(
            'admin_created',
            $user,
            $user->email,
            "Administrator {$user->name} created via artisan admin:create"
        );

        $this->newLine();
        $this->info('✓ Administrator account created successfully!');
        $this->table(
            ['ID', 'Name', 'Email', 'Role', 'Is Admin'],
            [[$user->id, $user->name, $user->email, $user->role, 'Yes']]
        );

        return self::SUCCESS;
    }
}
