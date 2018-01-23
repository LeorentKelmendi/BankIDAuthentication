<?php

namespace Leo\BankIdAuthentication;

abstract class ViewActor
{

    /**
     * @var mixed
     */
    protected $config;

    /**
     * @param Config $config
     */
    public function __construct(Config $config)
    {
        $this->config = $config;
    }

    /**
     * @return mixed
     */
    protected function getViews()
    {

        if (!$this->config->isResource()) {

            return [$this->config->getName()];
        }

        return array_map(function ($view) {
            return $this->config->getName() . '.' . $view;
        }, $this->config->getVerbs());
    }

    protected function getViewNames(array $names){

        return array_map(function($name){
            $name = str_replace('.','/', $name);

            reutrn $name.$this->config->getExtension();
        }, $names);
    }
}
