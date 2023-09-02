<?php

namespace HusamAwadhi\PowerParser\Parser;

use HusamAwadhi\PowerParser\Blueprint\Blueprint;
use HusamAwadhi\PowerParser\Exception\InvalidArgumentException;
use HusamAwadhi\PowerParser\Exception\UnsupportedExtensionException;
use HusamAwadhi\PowerParser\Parser\Extension\ParserPluginInterface;
use JsonSerializable;
use ReturnTypeWillChange;
use stdClass;

class Parser implements JsonSerializable, ParserInterface
{
    protected ParserPluginInterface $parsedContentPlugin;

    protected array $supportedExtensions;

    public function __construct(
        public readonly Blueprint $blueprint,
        public readonly string $content,
        /** @var ParserPluginInterface[] */
        protected array $plugins = []
    ) {
        foreach ($plugins as $className => $extension) {
            $extensionSupportedFormats = $extension->getSupportedExtensions();

            $this->supportedExtensions = array_merge(
                array_fill_keys($extensionSupportedFormats, $className),
                $this->supportedExtensions ?? []
            );
        }
    }

    public function getPlugins(): array
    {
        return $this->plugins;
    }

    public function getSupportedExtensions(): array
    {
        return array_keys($this->supportedExtensions);
    }

    public function getRecommendedPluginName(string $overridePlugin = ''): string
    {
        return $this->getParserExtension($overridePlugin)::class;
    }

    /**
     * @inheritDoc
     */
    public function parse(string $recommendedExtension = ''): self
    {
        $this->parsedContentPlugin = $this->getParserExtension($recommendedExtension)
            ->parse($this->content, $this->blueprint);

        return $this;
    }

    protected function getParserExtension(string $overridePlugin): ParserPluginInterface
    {
        if (!in_array($this->blueprint->extension, array_keys($this->supportedExtensions))) {
            throw new UnsupportedExtensionException(
                "Unsupported extension ({$this->blueprint->extension}). expected one of: "
                    . implode(', ', array_keys($this->supportedExtensions))
            );
        }

        $validRecommendation = (
            isset($this->plugins[$overridePlugin])
            && in_array($this->blueprint->extension, $this->plugins[$overridePlugin]->getSupportedExtensions())
        );

        return $this->plugins[$validRecommendation
            ? $overridePlugin
            : $this->supportedExtensions[$this->blueprint->extension]];
    }

    public function getParsedContentPlugin(): ParserPluginInterface
    {
        if (!isset($this->parsedContentPlugin)) {
            throw new InvalidArgumentException('content is not parsed yet.');
        }

        return $this->parsedContentPlugin;
    }

    /**
     * @inheritDoc
     */
    public function getAsObject(): stdClass
    {
        $json = json_encode($this->getAsArray());

        return $json === false ? new stdClass() : json_decode($json);
    }

    /**
     * @inheritDoc
     */
    public function getAsArray(): array
    {
        return $this->getParsedContentPlugin()->getFiltered();
    }

    #[ReturnTypeWillChange]
    public function jsonSerialize(): array
    {
        return $this->getAsArray();
    }
}
