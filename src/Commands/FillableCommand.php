<?php

namespace Sheidin\Fillable\Commands;

use Illuminate\Console\Command;

class FillableCommand extends Command
{
    public $signature = 'model:fillable {name}';

    public $description = 'My command';

    public function handle(): int
    {
        $this->comment('All done');

        return self::SUCCESS;
    }
}
