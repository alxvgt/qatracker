<?php
namespace Alxvng\QATracker\DataProvider;

interface DataProviderInterface
{
    public function fetchData(): float;
}