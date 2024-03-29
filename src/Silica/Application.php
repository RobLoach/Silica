<?php

namespace Silica;

use Silex\Application as SilexApplication;
use Silex\Provider\TwigServiceProvider;
use Symfony\Component\Yaml\Yaml;
use Symfony\Component\Finder\Finder;

class Application extends SilexApplication {
    public function __construct($configfile = 'config.yml') {
        // Load all the options and inject them into the Application.
        $this['config'] = Yaml::parse($configfile);
        foreach ($this['config'] as $option => $value) {
            $this[$option] = $value;
        }

        // Make sure to retrieve Silica app path relative from the config.
        if (!isset($this['silica.path'])) {
            $this['silica.path'] = dirname(realpath($configfile));
        }

        // Prepare Silex.
        parent::__construct();

        // Set up all the providers.
        $this->registerProviders();
        $this->setupPages();
    }

    private function registerProviders() {
        // Set up Twig.
        if (!isset($this['twig.path'])) {
            $this['twig.path'] = $this['silica.path'] . '/' . $this['silica.templates'];
        }
        $this->register(new TwigServiceProvider(), (array)$this);
    }

    private function setupPages() {
        // Find all the pages to set up.
        $app = $this;
        $finder = new Finder();
        $iterator = $finder->files()
            ->name('*.yml')
            ->in($this['silica.path'] . '/' . $this['silica.pages']);

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
                $template = isset($options['template']) ? $options['template'] : 'default.html';
                $variables = array_merge($app['config'], $options);

                // Have Twig render the page.
                return $app['twig']->render($template, $variables);
            });
        }
    }
}
