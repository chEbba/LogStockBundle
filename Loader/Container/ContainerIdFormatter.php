<?php
/*
 * Copyright (c) 2012
 * Kirill chEbba Cheunin <iam@chebba.org>
 *
 * This source file is subject to the MIT license that is bundled
 * with this package in the file LICENSE.
 */

namespace Che\Bundle\LogStockBundle\Loader\Container;

use Che\LogStock\Loader\Container\ServiceNameFormatter;

/**
 * Service name formatting for Symfony Container
 *
 * @author Kirill chEbba Chebunin <iam@chebba.org>
 * @license http://www.opensource.org/licenses/mit-license.php MIT License
 */
class ContainerIdFormatter implements ServiceNameFormatter
{
    /**
     * @var string Id template
     */
    private $template;
    /**
     * @var string Replaceable separator
     */
    private $separator;

    /**
     * Constructor with modification parameters
     *
     * @param string $template sprintf() format with one '%s' placeholder
     * @param string $separator String to be replaced with the default Symfony separator
     */
    public function __construct($template = '%s', $separator = '')
    {
        $this->template = $template;
        $this->separator = $separator;
    }

    /**
     * Replace separator with the default one and format by template
     *
     * @param string $name String for normalization
     * @param string $template sprintf() format with one '%s' placeholder
     * @param string $separator String for replacemnt
     *
     * @return string
     */
    static public function normalizeName($name, $template = '%s', $separator = '')
    {
        // Replace separator with Symfony default ('.') and apply template
        return trim(sprintf($template, strtolower($separator ? str_replace($separator, '.', $name) : $name)), '.');
    }

    public function formatServiceName($name)
    {
        return self::normalizeName($name, $this->template, $this->separator);
    }
}
