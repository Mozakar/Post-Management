<?php

namespace App\Console\Commands;

use Illuminate\Console\Command;

class Setup extends Command
{
    /**
     * The name and signature of the console command.
     *
     * @var string
     */
    protected $signature = 'setup';

    /**
     * The console command description.
     *
     * @var string
     */
    protected $description = 'Command description';

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
     * @return int
     */
    public function handle()
    {
        $this->getOutput()->progressStart(8);
        $this->getOutput()->newLine();
        $timeStart = microtime(true);

        $this->call('migrate:fresh');
        $this->getOutput()->progressAdvance();
        $this->getOutput()->newLine();

        $this->call('db:seed');

        $this->info('fresh app seed Successfully');
        $this->getOutput()->progressAdvance();
        $this->getOutput()->newLine();




        $this->call('passport:install');
        $this->getOutput()->progressAdvance();
        $this->getOutput()->newLine();

        $this->call('cache:clear');
        $this->getOutput()->progressAdvance();
        $this->getOutput()->newLine();

        $diff = microtime(true) - $timeStart;
        $sec = (int)$diff;
        $this->line('=================================================');
        $this->line('Took ' . gmdate('H:i:s', $sec) . ' to run');

        $this->getOutput()->newLine();
        $this->getOutput()->progressFinish();
    }
}
