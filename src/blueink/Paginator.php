<?php

namespace Blueink\ClientSDK;

/**
 * Iterator that lazily walks the pages of a Blueink list endpoint.
 *
 * The supplied callable is invoked with an associative array
 * ['page' => int, 'per_page' => int, 'additional_data' => array|null]
 * and is expected to return a NormalizedResponse with a populated
 * `pagination` field (driven by the X-Blueink-Pagination header).
 */
class Paginated implements \Iterator
{
    private $paged_function;
    private int $start_page;
    private int $per_page;
    private ?array $additional_data;
    private int $current_page;
    private ?int $total_pages = null;
    private ?NormalizedResponse $current_response = null;

    public function __construct(callable $paged_function, int $page = 1, int $per_page = 50, ?array $additional_data = null)
    {
        $this->paged_function  = $paged_function;
        $this->start_page      = $page;
        $this->per_page        = $per_page;
        $this->additional_data = $additional_data;
        $this->current_page    = $page;
    }

    /**
     * Fetch the next page and return its NormalizedResponse, or null when
     * the last page has been consumed.
     */
    public function nextPage(): ?NormalizedResponse
    {
        if (!is_null($this->total_pages) && $this->current_page > $this->total_pages) {
            $this->current_response = null;

            return null;
        }

        $response = ($this->paged_function)([
            'page' => $this->current_page,
            'per_page' => $this->per_page,
            'additional_data' => $this->additional_data,
        ]);

        if ($response instanceof NormalizedResponse && $response->pagination) {
            $this->total_pages = $response->pagination->total_pages;
        }

        $this->current_page++;
        $this->current_response = $response;

        return $response;
    }

    public function current(): mixed
    {
        return $this->current_response;
    }

    public function key(): int
    {
        return $this->current_page - 1;
    }

    public function next(): void
    {
        $this->nextPage();
    }

    public function rewind(): void
    {
        $this->current_page = $this->start_page;
        $this->total_pages = null;
        $this->current_response = null;
        $this->nextPage();
    }

    public function valid(): bool
    {
        return $this->current_response !== null;
    }
}
