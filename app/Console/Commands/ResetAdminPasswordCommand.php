<?php

namespace App\Console\Commands;

use App\Models\AdminActivity;
use App\Models\User;
use Illuminate\Console\Command;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Facades\Validator;

class ResetAdminPasswordCommand extends Command
{
    /**
     * The name and signature of the console command.
     *
     * @var string
     */
    protected $signature = 'admin:reset-password
                            {--email= : Email address of the administrator}
                            {--password= : New password for the administrator}';

    /**
     * The console command description.
     *
     * @var string
     */
    protected $description = 'Reset password and verify admin role for an IDFR Administrator account';

    /**
     * Execute the console command.
     */
    public function handle(): int
    {
        $this->info('==================================================');
        $this->info('  IDFR FASHION RUNWAY — RESET ADMIN PASSWORD');
        $this->info('==================================================');

        $email = $this->option('email') ?: $this->ask('Enter Administrator Email');
        $email = strtolower(trim($email));

        $user = User::where('email', $email)->first();

        if (! $user) {
            $this->error("No account found with email [{$email}].");
            $this->line('To create a new administrator account, run: php artisan admin:create');

            return self::FAILURE;
        }

        $password = $this->option('password') ?: $this->secret('Enter New Administrator Password (minimum 8 characters)');

        $validator = Validator::make([
            'password' => $password,
        ], [
            'password' => ['required', 'string', 'min:8'],
        ]);

        if ($validator->fails()) {
            $this->error('Failed to update administrator password due to validation errors:');
            foreach ($validator->errors()->all() as $error) {
                $this->line("  • {$error}");
            }

            return self::FAILURE;
        }

        $user->update([
            'password' => Hash::make($password),
            'is_admin' => true,
            'role' => 'admin',
        ]);

        AdminActivity::log(
            'admin_password_reset',
            $user,
            $user->email,
            "Password reset and admin privileges verified for {$user->name} via CLI"
        );

        $this->newLine();
        $this->info('✓ Administrator password updated and privileges verified successfully!');
        $this->table(
            ['ID', 'Name', 'Email', 'Role', 'Is Admin'],
            [[$user->id, $user->name, $user->email, $user->role, 'Yes']]
        );

        return self::SUCCESS;
    }
}
