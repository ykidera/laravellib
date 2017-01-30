<?php

namespace Ykidera\Laravellib;

use Illuminate\Console\Command;

class HelperConsole extends Command
{
    /**
     * The name and signature of the console command.
     *
     * @var string
     */
    protected $signature = 'make:helper {name}';

    /** @var string  */
    protected $description = 'laravel make helper file';

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
        $name = $this->argument('name');
    }
    
    /**
     * @param Book $book
    public function handle()
    {
        $result = $book->getData();

        $formatHelper = new FormatterHelper();
        $formattedLine = $formatHelper->formatSection(
            'authors',
            implode(', ', $result['authors'])
        );
        $this->output->writeln($formattedLine);
        $this->output->writeln("<fg=cyan;bg=black>{$result['title']}</> <fg=yellow;bg=black>{$result['sub_title']}</>");
        $this->table($result['chapters'], $result['contents']);

        $this->error("JPY " . number_format("{$result['price']}", 2));
    }
     */
}
