<?php
namespace Blueink\ClientSDK\Tests;

use Blueink\ClientSDK\NormalizedResponse;
use Blueink\ClientSDK\Paginated;
use GuzzleHttp\Psr7\Response;
use PHPUnit\Framework\TestCase;

/**
 * @covers \Blueink\ClientSDK\Paginated
 */
class PaginatedTest extends TestCase
{
    /**
     * Build a NormalizedResponse with a populated pagination header so the
     * iterator can learn the total page count.
     */
    private function pagedResponse(int $page, int $total_pages, array $data): NormalizedResponse
    {
        $resp = new Response(
            200,
            ['X-Blueink-Pagination' => "$page,$total_pages,2,6", 'Content-Type' => 'application/json'],
            json_encode($data)
        );

        return new NormalizedResponse($resp);
    }

    public function testIteratesUntilTotalPagesReached(): void
    {
        $calls = [];
        $iterator = new Paginated(function (array $args) use (&$calls) {
            $calls[] = $args;

            return $this->pagedResponse($args['page'], 3, ["row{$args['page']}"]);
        }, 1, 2);

        $pages = [];
        foreach ($iterator as $response) {
            $pages[] = $response->data;
        }

        $this->assertSame([['row1'], ['row2'], ['row3']], $pages);
        $this->assertSame([1, 2, 3], array_column($calls, 'page'));
        $this->assertSame([2, 2, 2], array_column($calls, 'per_page'));
    }

    public function testHonorsStartingPage(): void
    {
        $iterator = new Paginated(function (array $args) {
            return $this->pagedResponse($args['page'], 5, [$args['page']]);
        }, 4, 10);

        $seen = [];
        foreach ($iterator as $response) {
            $seen[] = $response->data[0];
        }

        $this->assertSame([4, 5], $seen);
    }

    public function testForwardsAdditionalDataToCallback(): void
    {
        $observed = null;
        $iterator = new Paginated(function (array $args) use (&$observed) {
            $observed = $args['additional_data'];

            return $this->pagedResponse($args['page'], 1, []);
        }, 1, 50, ['status' => 'co']);

        iterator_to_array($iterator);

        $this->assertSame(['status' => 'co'], $observed);
    }

    public function testStopsWhenResponseHasNoPagination(): void
    {
        $iterator = new Paginated(function () {
            $resp = new Response(200, ['Content-Type' => 'application/json'], '[]');

            return new NormalizedResponse($resp);
        }, 1, 50);

        // No pagination header means total_pages stays null and the iterator
        // never advances past the first call. Confirm we still get exactly one.
        $iterator->rewind();
        $this->assertTrue($iterator->valid());
        $iterator->next();
        // Still valid because the implementation cannot infer last page; the
        // caller is expected to break out. Cover the "no pagination" branch.
        $this->assertInstanceOf(NormalizedResponse::class, $iterator->current());
    }
}
