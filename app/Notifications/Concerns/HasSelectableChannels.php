<?php

namespace App\Notifications\Concerns;

trait HasSelectableChannels
{
    /** @var array<int, string>|null */
    protected ?array $forcedChannels = null;

    /**
     * @param  array<int, string>  $channels
     */
    public function viaChannels(array $channels): static
    {
        $this->forcedChannels = array_values($channels);

        return $this;
    }

    /**
     * @return array<int, string>
     */
    protected function resolveChannels(object $notifiable, array $defaults = ['database', 'mail']): array
    {
        return $this->forcedChannels ?? $defaults;
    }
}
