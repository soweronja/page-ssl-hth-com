<?php

/**
 * Site metadata management with description generation.
 * 
 * This utility provides a structured way to manage site metadata
 * and generate short descriptive text for pages or sections.
 */

class SiteMeta
{
    private array $metadata;
    private string $defaultSite;

    public function __construct(array $metadata = [], string $defaultSite = 'default')
    {
        $this->metadata = $metadata;
        $this->defaultSite = $defaultSite;
    }

    /**
     * Add or update metadata for a given site key.
     */
    public function set(string $key, array $data): void
    {
        $this->metadata[$key] = $data;
    }

    /**
     * Retrieve metadata for a given site key.
     */
    public function get(string $key): ?array
    {
        return $this->metadata[$key] ?? null;
    }

    /**
     * Generate a short description text based on metadata.
     *
     * @param string $key Site key to use.
     * @param int $maxLength Maximum length of description.
     * @return string Generated description.
     */
    public function generateDescription(string $key, int $maxLength = 160): string
    {
        $data = $this->get($key);
        if ($data === null) {
            return '';
        }

        $title = $data['title'] ?? '';
        $tagline = $data['tagline'] ?? '';
        $keywords = $data['keywords'] ?? [];
        $url = $data['url'] ?? '';

        $parts = [];
        if ($title !== '') {
            $parts[] = $title;
        }
        if ($tagline !== '') {
            $parts[] = $tagline;
        }
        if (!empty($keywords)) {
            $parts[] = implode(', ', $keywords);
        }
        if ($url !== '') {
            $parts[] = $url;
        }

        $description = implode(' | ', $parts);

        // Trim to max length, trying to cut at word boundaries.
        if (mb_strlen($description) > $maxLength) {
            $description = mb_substr($description, 0, $maxLength);
            $lastSpace = mb_strrpos($description, ' ');
            if ($lastSpace !== false) {
                $description = mb_substr($description, 0, $lastSpace);
            }
        }

        return htmlspecialchars($description, ENT_QUOTES, 'UTF-8');
    }

    /**
     * Get all site keys.
     */
    public function getKeys(): array
    {
        return array_keys($this->metadata);
    }

    /**
     * Get default site metadata.
     */
    public function getDefault(): ?array
    {
        return $this->get($this->defaultSite);
    }
}

// Example usage with sample metadata.
$sites = [
    'hth' => [
        'title' => 'HTH Platform',
        'tagline' => 'Your gateway to interactive experiences',
        'keywords' => ['hth', 'entertainment', 'digital'],
        'url' => 'https://page-ssl-hth.com',
        'language' => 'en',
    ],
    'blog' => [
        'title' => 'Tech Insights',
        'tagline' => 'Exploring modern web development',
        'keywords' => ['php', 'metadata', 'web'],
        'url' => 'https://example.com/blog',
        'language' => 'en',
    ],
];

$metaManager = new SiteMeta($sites, 'hth');

// Generate and output descriptions.
foreach ($metaManager->getKeys() as $key) {
    $desc = $metaManager->generateDescription($key);
    echo "Description for '{$key}': {$desc}\n";
}

// Output default site info.
$default = $metaManager->getDefault();
if ($default !== null) {
    echo "\nDefault site: " . $default['title'] . "\n";
}