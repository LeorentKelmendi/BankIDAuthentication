<?php

namespace Leo\BankIdAuthentication;

class Generator extends ViewActor
{

    /**
     * @param BlockStack $blockstack
     */
    public function generate(BlockStack $blockstack)
    {

        $views = $this->getViews();

        $this->makeViews($this->getViewNames($views), $blockStack->all());
    }
    /**
     * @param array $names
     * @param array $blocks
     */
    protected function makeViews(array $names, array $blocks)
    {
        $path = PathHelper::getPath($name);

        PathHelper::createIntermediateFolders($path);
        file_put_contents($path, $contents);
    }
}
