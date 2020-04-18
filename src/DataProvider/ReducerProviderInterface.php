<?php
namespace Alxvng\QATracker\DataProvider;

interface ReducerProviderInterface
{
    public function reduceMethod(array $nodes): float;
}