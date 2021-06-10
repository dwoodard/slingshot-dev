<?php

namespace Dwoodard\Slingshot\Console\Commands;

use Illuminate\Console\Command;

class slingshot extends Command
{
    /**
     * The name and signature of the console command.
     *
     * @var string
     */
    protected $signature = 'slingshot';

    /**
     * The console command description.
     *
     * @var string
     */
    protected $description = 'Command description';

    /**
     * Create a new command instance.
     *
     * @return void
     */
    public function __construct()
    {
        parent::__construct();
    }

    /**
     * Execute the console command.
     *
     * @return int
     */
    public function handle()
    {
        $installs = [
            'laradock',
            'composer packages',
            'Package Json files',
            'deploy',
            'Auth',
            'linter',
            'helpers',
        ];

        $slingshot = $this->choice('SLINGSHOT', ['all', ...$installs], 'all');


        if ($slingshot) {
            if ($slingshot == 'all') {
                foreach ($installs as $install) {
                    $this->installSwitch($install);
                }
            } else {
                $this->installSwitch($slingshot);
            }
        }

        return 0;
    }

    /**
     * @param $slingshot
     */
    private function installSwitch($slingshot): void
    {
        $this->line(' ');
        switch ($slingshot) {
            case 'laradock':
                $this->info('Installing Laradock');

                $this->info('- Clone laradock');
                if (!file_exists(base_path() . '/laradock')) {
                    shell_exec("git clone https://github.com/Laradock/laradock.git");
                    shell_exec("rm -rf laradock/.git");
                    $this->info('   - removed laradock/.git');
                    chdir(base_path() . '/laradock');
                    copy('.env.example', '.env',);
                } else {
                    $this->info('   - laradock was already cloned');
                }


                //update .env for laradock
                $laradockEnv = file_get_contents(base_path() . '/laradock/.env');
                if (!str_contains($laradockEnv, 'DATA_PATH_HOST=../data')) {
                    $laradockEnv = preg_replace("/DATA_PATH_HOST=.*/", "DATA_PATH_HOST=../data", $laradockEnv);
                    $php_version = $this->anticipate('What version of php (8.0 - 7.4 - 7.3)',
                        ['8.0', '7.4', '7.3'], '7.4'
                    );
                    $laradockEnv = preg_replace("/PHP_VERSION=.*/", "PHP_VERSION=" . $php_version, $laradockEnv);
                    file_put_contents(base_path() . '/laradock/.env', $laradockEnv);
                    $this->info('   - laradock .env was updated');
                } else {
                    $this->info('   - laradock .env was already updated');
                }


                //update .env for laravel
                $filename = base_path() . '/.env';
                $env = file_get_contents($filename);
                if (!str_contains($env, 'DB_HOST=mysql')) {
                    //Update envs
                    $env = preg_replace("/DB_HOST=.*/", "DB_HOST=mysql", $env);
                    $env = preg_replace("/REDIS_HOST=.*/", "REDIS_HOST=redis", $env);
                    $env = preg_replace("/DB_USERNAME=.*/", "DB_USERNAME=root", $env);
                    $env = preg_replace("/DB_PASSWORD=.*/", "DB_PASSWORD=root", $env);
                    file_put_contents(base_path() . '/.env', $env);
                    $this->info('   - laravel .env has been saved');

                    //Add envs
                    if (!str_contains($env, 'QUEUE_HOST=beanstalkd')) {
                        file_put_contents($filename, 'QUEUE_HOST=beanstalkd' . "\n", FILE_APPEND | LOCK_EX);
                    }

                } else {
                    $this->info('   - laravel .env was already updated');
                }

                //Add data directory
                $filename = base_path() . '/data';
                if (!file_exists($filename)) {
                    mkdir($filename, 0777, true);
                    $this->info('- data directory added');
                } else {
                    $this->info('- data directory exists');
                }

                //add .gitignore items
                $this->info("- Check git " . base_path() . "/.gitignore");
                $gitignore = file_get_contents(base_path() . '/.gitignore');
                $gitignoreChecks = [
                    '/data/',
                    '!/laradock/.env',
                ];
                foreach ($gitignoreChecks as $check) {
                    if (!str_contains($gitignore, $check)) {
                        file_put_contents(base_path() . '/.gitignore', $check . "\n", FILE_APPEND | LOCK_EX);
                    }
                }


                break;
            default:
                $this->info("Installing $slingshot");
                break;
        }

    }
}
