<?php

namespace Bytexr\QueueableBulkActions\Filament\Actions;

class ActionResponse
{
    public function __construct(private bool $success, private ?string $message)
    {
    }

    public static function make(bool $success = true, ?string $message = null): static
    {
        return new ActionResponse($success, $message);
    }

    public function success(): static
    {
        $this->success = true;

        return $this;
    }

    public function failure(): static
    {
        $this->success = false;

        return $this;
    }

    public function message(?string $message = null): static
    {
        $this->message = $message;

        return $this;
    }

    public function isSuccess(): bool
    {
        return $this->success;
    }

    public function getMessage(): ?string
    {
        return $this->message;
    }
}
