<?php
namespace App\DataProvider;

interface ReducerProviderInterface
{
    public function reduceMethod(array $nodes): float;
}