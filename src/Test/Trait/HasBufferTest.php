<?php

namespace GrinWay\Service\Test\Trait;

trait HasBufferTest
{
    protected static function assertOutputBufferWasNotUsed(?string $dopMessage = null): void
    {
        $dopMessage ??= '';

        $bufferListStatus = \ob_get_status(full_status: true);
        foreach ($bufferListStatus as $bufferStatus) {
            static::assertSame(
                0,
                $bufferStatus['buffer_used'] ?? 0,
                message: \sprintf('Output buffer was supposed not to be used%s', $dopMessage),
            );
        }
    }
}
