<?php

namespace Frostrain\TranslatorApi;

interface TranslatorInterface
{
    /**
     * @param string $content
     * @options array $options
     * @return string
     */
    public function translate($content, $options = []);
}