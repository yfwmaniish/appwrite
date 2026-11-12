<?php

declare(strict_types=1);

namespace Tests\Unit\Screenshots;

use Appwrite\Screenshots\Exception;
use PHPUnit\Framework\Attributes\DataProvider;
use PHPUnit\Framework\TestCase;

final class ExceptionTest extends TestCase
{
    private const string UNROUTABLE = "Host '10.0.0.1' is not publicly routable.";

    /**
     * @return array<string, array{string, int, bool}>
     */
    public static function failures(): array
    {
        return [
            'page over memory budget' => ['Page exceeded the memory budget and was closed.', 507, true],
            'blocked navigation' => ["Navigation to 'http://10.0.0.1/' was blocked: the destination is not publicly routable.", 403, true],
            'disallowed scheme' => ["Scheme 'file:' is not allowed.", 403, true],
            'unroutable host' => [self::UNROUTABLE, 403, true],
            'unroutable host from an older browser image' => [self::UNROUTABLE, 400, true],
            'browser at capacity' => ['Browser is at capacity. Retry after the Retry-After delay.', 503, false],
            'capture evicted' => ['Capture was closed to keep the browser within its memory budget. Retry after the Retry-After delay.', 503, false],
            'timeout' => ['Timeout exceeded', 400, false],
            'bad gateway' => ['Screenshot failed with status 502', 502, false],
        ];
    }

    #[DataProvider('failures')]
    public function testIsPermanent(string $message, int $code, bool $permanent): void
    {
        $this->assertSame($permanent, (new Exception($message, $code))->isPermanent());
    }
}
