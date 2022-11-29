<?php

namespace Marshmallow\NovaFormbuilder\Commands;

use Illuminate\Console\Command;

class NovaFormbuilderCommand extends Command
{
    public $signature = 'nova-formbuilder';

    public $description = 'My command';

    public function handle(): int
    {
        $this->comment('All done');

        return self::SUCCESS;
    }
}
