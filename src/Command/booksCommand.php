<?php
namespace App\Command;

use App\Service\BookLoader;
use Symfony\Component\Console\Command\Command;
use Symfony\Component\Console\Input\InputInterface;
use Symfony\Component\Console\Output\OutputInterface;

class booksCommand extends Command {

    private $bookLoader;

    public function __construct(BookLoader $bookLoader)
    {
        $this->bookLoader = $bookLoader;

        parent::__construct();
    }

    protected function configure()
    {
        $this->setName('books:refresh');
    }

    protected function execute(InputInterface $input, OutputInterface $output)
    {
        function rglob($pattern, $flags = 0) {
            $files = glob($pattern, $flags);
            foreach (glob(dirname($pattern).'/*', GLOB_ONLYDIR|GLOB_NOSORT) as $dir) {
                $files = array_merge($files, rglob($dir.'/'.basename($pattern), $flags));
            }
            return $files;
        }
        $files = rglob("W:/Livres/**");
        $files = array_filter($files, function ($file) { return filemtime($file) >= time() - 10*60;});


        $this->bookLoader->getBooks();

         return Command::SUCCESS;
    }

}
