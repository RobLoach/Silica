<?php

namespace Silica;

use Silex\Application as SilexApplication;
use Silex\Provider\TwigServiceProvider;
use Symfony\Component\Yaml\Yaml;
use Symfony\Component\Finder\Finder;

class Application extends SilexApplication {
    public function __construct($configfile = 'config.yml') {
        // Load all the options and inject them into the Application.
        $options = Yaml::parse($configfile);
        foreach ($options as $option => $value) {
            $this[$option] = $value;
        }

        // Prepare Silex.
        parent::__construct();

        // Set up all the providers.
        $this->registerProviders();
        $this->setupPages();
    }

    private function registerProviders() {
        $this->register(new TwigServiceProvider(), (array)$this);
    }

    private function setupPages() {
        // Find all the pages to set up.
        $app = $this;
        $finder = new Finder();
        $iterator = $finder->files()
            ->name('*.yml')
            ->in(realpath($this['silica.path']));

        // Iterate through each page.
        foreach ($iterator as $file) {
            // Parse the file name for information.
            $path = $file->getRelativePathname();
            $path = str_replace('index', '', $path);
            $path = str_replace('.yml', '', $path);

            // Register the page to the web path.
            $this->get($path, function () use ($app, $file) {
                // Parse the file for page information.
                $options = Yaml::parse($file->getRealPath());

                // Inject any required variables.
                $template = isset($options['template']) ? $options['template'] : 'default.twig';

                // Have Twig render the page.
                return $app['twig']->render($template, $options);
            });
        }
    }
}
