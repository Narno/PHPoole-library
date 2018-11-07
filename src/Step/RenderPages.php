<?php
/*
 * Copyright (c) Arnaud Ligny <arnaud@ligny.org>
 *
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 */

namespace PHPoole\Step;

use PHPoole\Collection\Page\Page;
use PHPoole\Exception\Exception;
use PHPoole\Renderer\Layout;
use PHPoole\Renderer\Twig as Twig;

/**
 * Pages rendering.
 */
class RenderPages extends AbstractStep
{
    /**
     * {@inheritdoc}
     *
     * @throws Exception
     */
    public function init($options)
    {
        if (!is_dir($this->config->getLayoutsPath()) && !$this->config->hasTheme()) {
            throw new Exception(sprintf(
                "'%s' is not a valid layouts directory",
                $this->config->getLayoutsPath()
            ));
        }

        $this->process = true;
    }

    /**
     * {@inheritdoc}
     *
     * @throws Exception
     */
    public function process()
    {
        // prepares renderer
        $this->phpoole->setRenderer(new Twig($this->getLayoutsPaths(), $this->config));

        // add globals variables
        $this->addGlobals();

        // start rendering
        call_user_func_array($this->phpoole->getMessageCb(), ['RENDER', 'Rendering pages']);

        /* @var $page Page */
        $filteredPages = $this->phpoole->getPages()->filter(function (Page $page) {
            return !empty($page->getVariable('published'));
        });
        $max = count($filteredPages);

        $count = 0;
        foreach ($filteredPages as $page) {
            $count++;

            $rendered = $this->phpoole->getRenderer()->render(
                (new Layout())->finder($page, $this->config),
                ['page' => $page]
            );
            $page->setVariable('rendered', $rendered);
            $this->phpoole->getPages()->replace($page->getId(), $page);

            $message = $page->getPathname() ?: 'index';
            call_user_func_array($this->phpoole->getMessageCb(), ['RENDER_PROGRESS', $message, $count, $max]);
        }
    }

    /**
     * Return an array of layouts directories.
     *
     * @return array Layouts directory
     */
    protected function getLayoutsPaths()
    {
        $paths = [];

        if (is_dir($this->config->getLayoutsPath())) {
            $paths[] = $this->config->getLayoutsPath();
        }
        // theme
        if ($this->config->hasTheme()) {
            $paths[] = $this->config->getThemePath($this->config->get('theme'));
        }
        // internal
        if (is_dir($this->config->getInternalLayoutsPath())) {
            $paths[] = $this->config->getInternalLayoutsPath();
        }

        return $paths;
    }

    /**
     * Add globals variables.
     */
    protected function addGlobals()
    {
        // adds global variables
        $this->phpoole->getRenderer()->addGlobal('site', array_merge(
            $this->config->get('site'),
            ['menus' => $this->phpoole->getMenus()],
            ['pages' => $this->phpoole->getPages()->filter(function (Page $page) {
                return $page->getVariable('published');
            })],
            ['time' => time()]
        ));
        $this->phpoole->getRenderer()->addGlobal('phpoole', [
            'url'       => sprintf('https://phpoole.org/#%s', $this->phpoole->getVersion()),
            'version'   => $this->phpoole->getVersion(),
            'poweredby' => sprintf('PHPoole (library v%s)', $this->phpoole->getVersion()),
        ]);
    }
}
