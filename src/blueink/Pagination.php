<?php
namespace Blueink\ClientSDK;

/**
 * Pagination fields parsed out for a Blueink paged response from
 * the X-Blueink-Pagination header.
 *
 * Format: "page_number,total_pages,per_page,total_results"
 */
class Pagination
{
    public int $page_number;
    public int $total_pages;
    public int $per_page;
    public int $total_results;

    public function __construct(string $pagination_header)
    {
        $parts = explode(',', $pagination_header);
        $this->page_number   = (int) ($parts[0] ?? 0);
        $this->total_pages   = (int) ($parts[1] ?? 0);
        $this->per_page      = (int) ($parts[2] ?? 0);
        $this->total_results = (int) ($parts[3] ?? 0);
    }

    public function paginationAsString(): string
    {
        return "page_number: " . $this->page_number
            . ", per_page:" . $this->per_page
            . ", total_pages:" . $this->total_pages
            . ", total_results: " . $this->total_results;
    }

    public function __toString(): string
    {
        return $this->paginationAsString();
    }
}
