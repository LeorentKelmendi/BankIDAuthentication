<?php

namespace Leo\BankIdAuthentication\Commands;

use Illuminate\Console\Command;

class MakeBankID extends Command
{

    /**
     * @var string
     */
    protected $name = "make:BankidView";

    /**
     * @var string
     */
    protected $description = "Create a new view to provide login fields for BANKID";

    public function handle()
    {

        $generator = new Generator($this->getConfig());

        $generator->generate((new BlockStack)->build($this->input));

        $this->info("BankID login view created successfully");
    }

    public function getConfig()
    {

        return (new Config)
            ->setName($this->argument('name'))
            ->setExtension($this->option('extension'))
            ->setResource($this->option('resource'))
            ->setVerbs(...$this->option('verb'));
    }

    protected function getOptions()
    {
        return [
            ['extension', null, InputOption::VALUE_OPTIONAL, 'The extension of the generated view.', 'blade.php'],
            ['resource', 'r', InputOption::VALUE_NONE, 'Whether or not a RESTful resource should be created.'],
            ['verb', null, InputOption::VALUE_IS_ARRAY | InputOption::VALUE_REQUIRED, 'The HTTP verb(s) to generate views for.', ['index', 'show', 'create', 'edit']],
            ['section', null, InputOption::VALUE_IS_ARRAY | InputOption::VALUE_REQUIRED, 'A list of "@section"s to define in the created view(s).'],
            ['extends', null, InputOption::VALUE_OPTIONAL, 'The view to "@extend" from the created view(s).'],
            ['with-yields', 'y', InputOption::VALUE_NONE, 'Whether or not to add all "@yield" sections from extended template (if "--extends" was provided)'],
            ['with-stacks', 's', InputOption::VALUE_NONE, 'Whether or not to add all "@stacks" from extended template as @push (if "--extends" was provided)'],
        ];
    }

    protected function getArguments()
    {
        return [
            ['name', InputArgument::REQUIRED, 'The name of the view to create.'],
        ];
    }

}
