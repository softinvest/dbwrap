<?php

declare(strict_types=1);

namespace SoftInvest\Console\Commands;

use Illuminate\Console\Command;
use SoftInvest\Helpers\DBWrap;

class LoadDbDumpCommand extends Command
{
    /**
     * The name and signature of the console command.
     *
     * @var string
     */
    protected $signature = 'load-dbdump {path?} {connection?}';

    /**
     * The console command description.
     *
     * @var string
     */
    protected $description = 'Load dump';

    private const DEFAULT_PATH = 'public/dump';

    /**
     * Create a new command instance.
     *
     * @return void
     */
    public function __construct()
    {
        parent::__construct();
    }

    /**
     * Execute the console command.
     *
     * @return mixed
     */
    public function handle()
    {
        $path = self::DEFAULT_PATH;
        if (!(($this->input === null) || !$this->hasArgument('path'))){
            $path = $this->argument('path') ?: self::DEFAULT_PATH;
        }

        if (($this->input !== null) && $this->hasArgument('connection')){
            $conn = $this->argument('connection');
            if ($conn) {
                DBWrap::getDB()::setDefaultConnection($conn);
            }
        }

        $contents = file_get_contents('database/schemas/' . $path . '.sql');
        if ($contents) {
            DBWrap::getDB()::unprepared($contents);
        }

        return 0;
    }
}
