# BlueInk API Client

A PHP client to interact with the BlueInk REST API. 
For an overview of the API, see the [API v2 Documentation](https://blueink.com/esignature-api/api-docs/).  

This client library relies on [guzzle](http://docs.guzzlephp.org/en/stable/) 
and [snorlax](https://github.com/ezdeliveryco/snorlax), under the hood.

## Getting Started

```php
use BlueInk\ApiClient\Client;

$client = new Client('<API_KEY_HERE>');

// Get a list of Bundles
$bundle_list = $client->bundles->list();

// $bundle_list is the parsed data from the response. To 
// get the actual response object, do:
$response = $client->bundles->getLastResponse();

// Retrieve a single Bundle
$bundle_id = $bundle_list[0]->id;
$bundle = $client->bundles->retrieve($bundle_id);

// Get a list of Templates
$template_list = $client->templates->list();

// Assume there was at least one Document Template setup in the account
$template_01 = $template_list[0];
// Save the $role for later, so we can map our signer to
// this role in the template.
$role = $template_01->roles[0];

// Setup data for a request to create a new Bundle,
// using an existing template.
$request_data = [
    'label' => 'A Test Bundle',
    'is_test' => true,
    'packets' => [
         {
             'name' => 'Peter Gibbons',
             'email' => 'peter.gibbons@example.com',
             'key' => 'signer-1',
         }
    ],
    'documents' => [
        'key' => 'doc-01',
        'template_id' => $template_01->id,
        'assignments' => [
            'role' => $role,
            'signer' => 'signer-01'
        ]
    ],
];
// Create and send a new Bundle. 
// Note that we pass the request data as 'json', which results in
// in the request body being sent as application/json data
$new_bundle = $client->bundles->create([ 'json' => $request_data ]);
```

## Error Handling and Exceptions 

Requests raise exceptions if an error is encountered. This includes networking
errors (connection timeout, DNS errors, etc), server errors (5XX status codes), 
and application-level errors (4XX status codes). 

See [documentation on Guzzle exceptions](http://docs.guzzlephp.org/en/stable/quickstart.html#exceptions)

All exceptions extend from GuzzleHttp\Exception\TransferException.

You can handle specific classes of errors as follows:

All exceptions are in the namespace GuzzleHttp\Exception\.

- 4XX errors: ClientException 
- 5XX errors: ServerException 
- Networking errors: ConnectException 
- Too many redirects: TooManyRedirectsException 

Or catch multiple classes of exceptions:

- BadResponseException: 4XX and 5XX
- RequestException: 4XX, 5XX and networking errors
- TransferException: All errors that can be thrown during a request / response

### Handle Multiple Errors by Catching a RequestException
```php
try {
    $client->bundles->create($new_bundle_data);
} catch (RequestException $e) {
    // A 4XX, 5XX or networking error occured
    echo 'Got an exception';
    $response = $e->getResponse();
    echo "Status Code: " . $response->getStatusCode() . "\n";
    echo "Reason: " . $response->getReasonPhrase() . "\n";
    
    // Dump the error details, which are formatted
    // as described in the APIv2 documentation.
    var_export($response->getBody()->getContents());
}
```

### Handle Different Error Types Individually

```php
try {
    $client->bundles->create($new_bundle_data);
} catch (ClientException $e) {
    // handle 4XX error
} catch (ServerException $e) {
    // handle 5xx error
} catch (RequestException $e) {
    // handle any other error
}

```

## Pagination

API operations that return lists of data (e.g. `/bundles/`, `/persons/`)
are paginated. 

```php
$bundles = $client->bundles->list();
$response = $client->bundles->getLastResponse();


```

## Code Conventions

We use the following naming conventions in this code base:

- ClassName
- methodName
- propertyName
- function_name (meant for global functions)
- $variable_name
- CONSTANT_NAME (created with define(...))