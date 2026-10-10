<?php

declare(strict_types=1);

function processPrompt(string $prompt): array
{
    return
    [
        'prompt' => $prompt,
        'response' => 'Backend received prompt.'
    ];
}
