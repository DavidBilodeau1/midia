<?php
namespace App\Command;

use App\Service\ComicLoader;
use Symfony\Component\Console\Command\Command;
use Symfony\Component\Console\Input\InputInterface;
use Symfony\Component\Console\Output\OutputInterface;

class refreshCommand extends Command {

    private $comicLoader;

    public function __construct(ComicLoader $comicLoader)
    {
        $this->comicLoader = $comicLoader;

        parent::__construct();
    }

    protected function configure()
    {
        $this->setName('comic:refresh');
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
        $files = rglob("G:/livres/**.cb*");
        $files = array_filter($files, function ($file) { return filemtime($file) >= time() - 10*60;});


        $this->comicLoader->getSeries();

         return Command::SUCCESS;
    }

}
