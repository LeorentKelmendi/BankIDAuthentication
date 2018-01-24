<?php

namespace Leo\BankIdAuthentication\Commands;

use File;
use Illuminate\Console\Command;

class MakeBankID extends Command
{

    /**
     *
     * @var string
     */
    protected $name = "make:BankidView";

    /**
     * @var string
     */
    protected $description = "Create a new view to provide login fields for BANKID";

    public function __construct()
    {
        parent::__construct();
    }

    /**
     * Execute the console command.
     *
     * @return mixed
     */
    public function handle()
    {
        // $view = $this->argument('view');
        $view = "loginbankID";
        $path = $this->viewPath($view);
        $this->createDir($path);
        if (File::exists($path)) {
            $this->error("File {$path} already exists!");
            return;
        }
        File::put($path, $this->generateHTML());
        $this->info("File {$path} created.");
    }

    protected function generateHTML()
    {

        return '<!DOCTYPE html>
                <html lang="en">
                <head>
                    <meta charset="UTF-8">
                    <title>Login BankID</title>
                </head>
                <body>
                  <div class="container">
                        <div class="login-container">
                                <div id="output"></div>
                                <div class="avatar"></div>
                                <div class="form-box">
                                    <form action="" method="">
                                        <input name="user" type="text" placeholder="YYYY-MM-DD-NNNN">
                                        <button class="btn btn-info btn-block login" type="submit">Login</button>
                                    </form>
                                </div>
                            </div>
                    </div>
                </body>
         </html>';
    }

    /**
     * @param $path
     */
    public function createDir($path)
    {
        $dir = dirname($path);
        if (!file_exists($dir)) {
            mkdir($dir, 0777, true);
        }
    }
    /**
     * @param $view
     */
    public function viewPath($view)
    {
        $view = str_replace('.', '/', $view) . '.blade.php';
        $path = "resources/views/{$view}";
        return $path;
    }
}
