<?php

declare(strict_types=1);

namespace DayLaborers\LaravelMjml\Mail;

use DayLaborers\LaravelMjml\MjmlRenderer;
use Illuminate\Mail\Mailable;
use ReflectionException;
use Soundasleep\Html2TextException;

class MjmlMailable extends Mailable
{
    /**
     * @return array|string
     * @throws ReflectionException|Html2TextException
     */
    protected function buildView(): array|string
    {
        if (
            isset($this->view)
            || isset($this->mjmlContent)
        ) {
            return $this->buildMjmlView();
        }

        return parent::buildView();
    }

    /**
     * @return array
     * @throws ReflectionException|Html2TextException
     */
    protected function buildMjmlView(): array
    {
        /** @var MjmlRenderer $mjml */
        $mjml = app(MjmlRenderer::class);
        $data = $this->buildViewData();

        return [
            'html' => $mjml->renderHtml($this->view, $data),
            'text' => $this->textView ?? $mjml->renderText($this->view, $data),
        ];
    }
}
