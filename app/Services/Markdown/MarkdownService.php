<?php

namespace App\Services\Markdown;

use League\CommonMark\Environment\Environment;
use League\CommonMark\Extension\CommonMark\CommonMarkCoreExtension;
use League\CommonMark\Extension\DisallowedRawHtml\DisallowedRawHtmlExtension;
use League\CommonMark\Extension\GithubFlavoredMarkdownExtension;
use League\CommonMark\Extension\TaskList\TaskListExtension;
use League\CommonMark\MarkdownConverter;

class MarkdownService
{
    protected ?MarkdownConverter $converter = null;

    /**
     * Render sanitized, GitHub-flavored markdown to safe HTML.
     */
    public function render(?string $markdown): string
    {
        if (blank($markdown)) {
            return '<p class="text-slate-400 dark:text-slate-500">This note is empty.</p>';
        }

        return $this->converter()->convert((string) $markdown)->getContent();
    }

    protected function converter(): MarkdownConverter
    {
        if ($this->converter !== null) {
            return $this->converter;
        }

        $environment = new Environment([
            'html_input' => 'escape',
            'allow_unsafe_links' => false,
            'max_nesting_level' => 100,
        ]);

        $environment->addExtension(new CommonMarkCoreExtension);
        $environment->addExtension(new GithubFlavoredMarkdownExtension);
        $environment->addExtension(new TaskListExtension);
        $environment->addExtension(new DisallowedRawHtmlExtension);

        return $this->converter = new MarkdownConverter($environment);
    }
}
