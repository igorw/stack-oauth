<?php

namespace common;

use Pimple;
use Symfony\Component\HttpFoundation\Session\Storage\MockFileSessionStorage;
use Symfony\Component\HttpKernel\HttpKernelInterface;

abstract class TestCase extends \PHPUnit_Framework_TestCase
{
}