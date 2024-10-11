<?php

declare(strict_types=1);

namespace DayLaborers\LaravelMjml;

use DayLaborers\LaravelMjml\Contracts\MjmlProcedure;
use DOMDocument;
use DOMXPath;
use Illuminate\View\Compilers\BladeCompiler;

class MjmlCompiler extends BladeCompiler
{
    /** @var MjmlProcedure */
    protected MjmlProcedure $mjmlProcedure;

    /**
     * @param string $value
     *
     * @return string
     */
    public function compileString($value): string
    {
        // Render blade before mjml
        $mjml = parent::render($value);
        return parent::compileString(
            $this->compileMjml(
                $mjml,
                $this->isMjmlBodyWrapped($mjml),
            )
        );
    }

    /**
     * @param MjmlProcedure $procedure
     *
     * @return $this
     */
    public function setProcedure(MjmlProcedure $procedure): self
    {
        $this->mjmlProcedure = $procedure;

        return $this;
    }

    /**
     * @param string $value
     * @param bool   $wrapped
     *
     * @return string
     */
    protected function compileMjml(string $value, bool $wrapped): string
    {
        $mjml = $this->mjmlProcedure->render(
            $wrapped
                ? $value
                : sprintf('<mjml><mj-body>%s</mj-body></mjml>', $value)
        );

        return $wrapped ? $mjml : $this->extractMjmlContents($mjml);
    }

    /**
     * @param string $mjml
     *
     * @return string
     */
    protected function extractMjmlContents(string $mjml): string
    {
        $dom = new DOMDocument();
        $xPath = new DOMXPath($dom);

        $dom->loadHTML($mjml, LIBXML_NOERROR | LIBXML_NOWARNING);

        $nodes = $xPath->query('//body/div/node()');

        return implode(
            '',
            array_map(
                [$dom, 'saveHTML'],
                iterator_to_array($nodes)
            )
        );
    }

    /**
     * @param string $value
     *
     * @return bool
     */
    protected function isMjmlBodyWrapped(string $value): bool
    {
        return (bool) preg_match('/^<mjml(?:.|\n)*?<mj-body(?:.|\n)*?<\/mj-body>(?:.|\n)*?<\/mjml>$/', $value);
    }
}
