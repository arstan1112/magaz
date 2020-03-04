<?php


namespace App\Service;

use App\Service\ForTestService;

class TestService
{
//    public function forProviderCheck()
//    {
//        return null;
//    }
//
//    public function forIdenticalCheck(ForTestService $gateway)
//    {
//        return null;
//    }
//
//    public function forCallbackCheck(int $i, string $s, ForTestService $gateway)
//    {
//        return null;
//    }
//
    public function forConsecutiveArgs(string $s, int $i): self
    {
        return $this;
    }

    public function forProphecyCheck(string $s)
    {
        return null;
    }
}
