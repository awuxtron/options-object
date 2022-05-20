<?php

namespace Awuxtron\OptionsObject\Attributes;

use Attribute;

/**
 * Will triggers a silenced deprecation notice for the option.
 */
#[Attribute(Attribute::TARGET_PROPERTY)]
final class Deprecated extends AbstractAttribute
{
    /**
     * Values to insert in the message using printf() formatting.
     *
     * @var array<mixed>
     */
    protected array $args;

    public function __construct(protected string $package, protected string $version, protected string $message = '', mixed ...$args)
    {
        $this->args = $args;
    }

    /**
     * Triggers a silenced deprecation notice.
     */
    public function boot(): void
    {
        if (empty($this->message)) {
            $this->message = "The option '{$this->getOptionName()}' is deprecated.";
        }

        trigger_deprecation($this->package, $this->version, $this->message, $this->args);
    }
}
