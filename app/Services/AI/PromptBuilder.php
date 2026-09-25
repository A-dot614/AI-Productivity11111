<?php

namespace App\Services\AI;

use App\Services\AI\DataTransferObjects\Prompt;
use InvalidArgumentException;

class PromptBuilder
{
    /**
     * Build a Prompt from a named template in config/ai-prompts.php.
     *
     * @param  array<string, mixed>  $params
     */
    public function build(string $template, array $params = []): Prompt
    {
        $definition = config("ai-prompts.{$template}");

        if (! is_array($definition)) {
            throw new InvalidArgumentException("Unknown AI prompt template [{$template}].");
        }

        $system = $this->fill($definition['system'] ?? '', $params);
        $user = $this->fill($definition['user'] ?? '', $params);

        return new Prompt($system, $user);
    }

    /**
     * Replace {placeholder} tokens with their values.
     *
     * @param  array<string, mixed>  $params
     */
    protected function fill(string $text, array $params): string
    {
        foreach ($params as $key => $value) {
            $text = str_replace(
                '{'.$key.'}',
                is_string($value) || is_numeric($value) ? (string) $value : json_encode($value),
                $text
            );
        }

        return $text;
    }
}
