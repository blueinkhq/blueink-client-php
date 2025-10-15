<?php
namespace Blueink\ClientSDK;
# TODO need class description
class Paginated {
    public mixed $paged_function;
    public ?int $page;
    public ?int $per_page;
    public ?int $item_per_page;
    public ?array $additional_data;
    public ?int $total_pages;
    public ?int $next_page;
    # TODO need function description
    /**
     *
     */
    public function __construct(mixed $paged_function, ?int $page = 1, ?int $per_page = 50, ?array $additional_data = null) {
        $this->paged_function = $paged_function;
        $this->page = $page;
        $this->per_page = $per_page;
        $this->additional_data = $additional_data;
        $this->total_pages = null;
        $this->next_page = $page;
        $this->item_per_page = $per_page;
    }
    # TODO need description & testing function
    /**
     * 
     */
    public function next() {
        if (is_null($this->total_pages) && $this->next_page > $this->total_pages) {
            throw new \ErrorException("Stop Iteration");
        }

        try {
            $api_response = $this->paged_function([
                "page" => $this->next_page,
                "per_page" => $this->item_per_page,
                "additional_data" => $this->additional_data
            ]);
        } catch (\ErrorException $e) {
            throw new \ErrorException($e);
        }
        
        if (is_null($api_response['pagination'])) {
            throw new \ErrorException('Stop Iteration');
        }

        if (is_null($this->total_pages)) {
            $this->total_pages = $api_response['pagination']->total_pages;
        }

        $this->next_page = $this->next_page + 1;

        return $api_response;
    }
}