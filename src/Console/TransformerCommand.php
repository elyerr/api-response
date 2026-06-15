<?php

namespace Elyerr\ApiResponse\Console;

class TransformerCommand extends \Spatie\Fractal\Console\Commands\TransformerMakeCommand
{
    protected $name = 'api-response:transformer';

    protected function getStub()
    {
        return __DIR__ . '/../../stubs/transformer.stub';
    }
}
