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

    protected function generateHTML()
    {

        return <<<HTML
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <title>Login BankID</title>
    <!-- Latest compiled and minified CSS -->
<link rel="stylesheet" href="https://maxcdn.bootstrapcdn.com/bootstrap/3.3.7/css/bootstrap.min.css" integrity="sha384-BVYiiSIFeK1dGmJRAkycuHAHRg32OmUcww7on3RYdg4Va+PmSTsz/K68vbdEjh4u" crossorigin="anonymous">
</head>
<style> body{padding-top:20px;} </style>
<body>
  <div class="container">
    <div class="row">

    <div class="col-md-4 col-md-offset-4">
        <div class="panel panel-default">
            <div class="panel-heading">
                <h3 class="panel-title">Please login with personal number</h3>
            </div>
            <div class="panel-body">
                    <form action="{{ route('login') }}" method="post">
                        {{ csrf_field() }}
                        <input type="text" class="form-control" name="ssn" placeholder="YYYY-MM-DD-NNNN">
                        <input class="btn btn-lg btn-success btn-block" type="button"  onclick="submitRequest()" value="Login">
                        <label id="message"></label>
                    </form>
                 </div>
           </div>
       </div>
  </div>
</div>
</body>
<script
  src="https://code.jquery.com/jquery-2.2.4.min.js"
  integrity="sha256-BbhdlvQf/xTY9gja0Dq3HiwQF8LaCRTXxZKRutelT44="
  crossorigin="anonymous"></script>
<!-- Latest compiled and minified JavaScript -->
<script src="https://maxcdn.bootstrapcdn.com/bootstrap/3.3.7/js/bootstrap.min.js" integrity="sha384-Tc5IQib027qvyjSMfHjOMaLkfuWVxZxUPnCJA7l2mCWNIpG9mGCD8wGNIcPD7Txa" crossorigin="anonymous">
</script>
<script src="https://unpkg.com/axios/dist/axios.min.js"></script>


<script type="text/javascript">



    let orderRef = null;

    function submitRequest(){

        let ssn = $("input[name=ssn]").val();

        console.log(ssn);

        axios.post('/loginBankID', {ssn:ssn}).then(function(response){

            orderRef = response.data.data;
            console.log(orderRef+'first');

        });
       setTimeout(function (){ checkStatus(orderRef);}, 500);

    }

    function checkStatus(){
        console.log(orderRef+'from post');
        axios.post('/login-status', {order:orderRef}).then(function(response){

            console.log(response.data.message);
            $('#message').text(response.data.message);

            if(response.data.message === 'COMPLETE'){
                window.location.href='/test1';
                return false;
            }
            setTimeout(function(){
                checkStatus();
            },1000);
        });

    }

</script>
</html>
HTML;
    }
}
