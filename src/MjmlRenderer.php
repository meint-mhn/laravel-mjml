<?php

declare(strict_types=1);

namespace DayLaborers\LaravelMjml;

use DayLaborers\LaravelMjml\Contracts\MjmlProcedure;
use Illuminate\Contracts\View\Factory;
use Illuminate\Support\HtmlString;
use Soundasleep\Html2Text;
use Soundasleep\Html2TextException;

class MjmlRenderer
{
    /**
     * @param Factory       $view
     * @param MjmlProcedure $procedure
     */
    public function __construct(
        protected Factory $view,
        protected MjmlProcedure $procedure
    ) {}

    /**
     * @param string $view
     * @param array  $data
     *
     * @return HtmlString
     */
    public function renderHtml(string $view, array $data = []): HtmlString
    {
        return once(function () use ($view, $data) {
            $this->view->flushFinderCache();

            return new HtmlString($this->view->make($view, $data)->render());
        });
    }

    /**
     * @param string $view
     * @param array  $data
     *
     * @return HtmlString
     * @throws Html2TextException
     */
    public function renderText(string $view, array $data = []): HtmlString
    {
        return new HtmlString(
            html_entity_decode(
                preg_replace(
                    "/[\r\n]{2,}/",
                    "\n\n",
                    Html2Text::convert((string) $this->renderHtml($view, $data), [
                        'ignore_errors' => true,
                    ])
            ),
            ENT_QUOTES,
            'UTF-8',
        ));
    }
}
