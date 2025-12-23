# Current RMS PHP Client

A framework-agnostic PHP library for interacting with the Current RMS API. Provides both a flexible generic HTTP client and typed endpoint classes for common operations.

```bash
composer require wjbecker/current-rms-php
```

## Features

- **Framework Agnostic**: Works standalone or with Laravel
- **Type-Safe DTOs**: Strongly-typed data objects
- **Guzzle HTTP Client**: Built on Guzzle for reliable HTTP requests
- **Laravel Integration**: Optional service provider with auto-discovery
- **Facade Support**: Clean Laravel facade for easy access
- **Custom Query Encoding**: Handles Current RMS array parameters correctly
- **Full CRUD Operations**: Complete endpoint coverage

---

## Installation

```bash
composer require wjbecker/current-rms-php
```

---

## Quick Start

### Standalone Usage (No Laravel)

```php
use Wjbecker\CurrentRms\Client\Auth\ApiKeyAuth;
use Wjbecker\CurrentRms\Client\CurrentRmsClient;

// Create authentication
$auth = new ApiKeyAuth(
    subdomain: 'yourcompany',
    apiToken: 'your-api-token-here'
);

// Create client
$client = new CurrentRmsClient(
    baseUrl: 'https://api.current-rms.com/api/v1',
    auth: $auth
);

// Use the client
$opportunities = $client->opportunities()->list();
$opportunity = $client->opportunities()->find(123);
```

### Laravel Usage

#### 1. Publish Configuration (Optional)

```bash
php artisan vendor:publish --tag=current-rms-config
```

#### 2. Configure Environment

Add to your `.env` file:

```env
CURRENT_RMS_API_BASE_URL=https://api.current-rms.com/api/v1
CURRENT_RMS_AUTH_TYPE=api_key
CURRENT_RMS_SUBDOMAIN=yourcompany
CURRENT_RMS_API_TOKEN=your-api-token-here
```

#### 3. Use the Client

**Via Dependency Injection:**

```php
use Wjbecker\CurrentRms\Client\CurrentRmsClient;

class OpportunityController extends Controller
{
    public function __construct(
        private CurrentRmsClient $client
    ) {}

    public function index()
    {
        $opportunities = $this->client->opportunities()->list();
        return view('opportunities.index', compact('opportunities'));
    }
}
```

**Via Facade:**

```php
use Wjbecker\CurrentRms\Facades\CurrentRms;

// List opportunities
$opportunities = CurrentRms::opportunities()->list();

// Find specific opportunity
$opportunity = CurrentRms::opportunities()->find(123);

// Create opportunity
$opportunity = CurrentRms::opportunities()->create([
    'subject' => 'New Event',
    'member_id' => 456
]);

// Direct API calls
$orders = CurrentRms::get('/orders', ['status' => 'active']);
```

---

## API Reference

### Opportunities Endpoint

```php
// List all opportunities
$opportunities = $client->opportunities()->list();

// List with filters
$opportunities = $client->opportunities()->list([
    'q' => ['state_eq' => 1]
]);

// Find specific opportunity
$opportunity = $client->opportunities()->find(123);

// Find with includes
$opportunity = $client->opportunities()->find(123, ['owner', 'member']);

// Create opportunity
$opportunity = $client->opportunities()->create([
    'subject' => 'New Project',
    'member_id' => 456,
    'starts_at' => '2025-01-15T08:00:00.000Z',
    'ends_at' => '2025-01-16T18:00:00.000Z',
]);

// Update opportunity
$opportunity = $client->opportunities()->update(123, [
    'subject' => 'Updated Project Name'
]);

// Delete opportunity
$client->opportunities()->destroy(123);

// Checkout (convert to order)
$opportunity = $client->opportunities()->checkout([
    'opportunity_id' => 123
]);

// Clone opportunity
$newOpportunity = $client->opportunities()->clone(123);
```

### Opportunity Items Endpoint

```php
// List all opportunity items
$items = $client->opportunityItems()->list();

// List items for specific opportunity (scoped)
$items = $client->opportunities()->items(123)->list();

// Find specific item
$item = $client->opportunities()->items(123)->find(456);

// Create item
$item = $client->opportunities()->items(123)->create([
    'product_id' => 789,
    'quantity' => 5
]);

// Update item
$item = $client->opportunities()->items(123)->update(456, [
    'quantity' => 10
]);

// Delete item
$client->opportunities()->items(123)->destroy(456);
```

### Generic HTTP Methods

```php
// GET request
$data = $client->get('/endpoint', ['param' => 'value']);

// POST request
$data = $client->post('/endpoint', ['key' => 'value']);

// PUT request
$data = $client->put('/endpoint', ['key' => 'value']);

// PATCH request
$data = $client->patch('/endpoint', ['key' => 'value']);

// DELETE request
$client->delete('/endpoint');
```

---

## Configuration Options

When creating the client manually:

```php
$client = new CurrentRmsClient(
    baseUrl: 'https://api.current-rms.com/api/v1',  // required
    auth: $auth,                                      // optional
    timeout: 30,                                      // optional, default: 30
    connectTimeout: 10,                               // optional, default: 10
    verifySsl: true                                   // optional, default: true
);
```

---

## Data Objects

### OpportunityData

```php
$opportunity->id;
$opportunity->subject;
$opportunity->state;
$opportunity->status;
$opportunity->starts_at;
$opportunity->ends_at;

// Helper methods
$opportunity->isDraft();     // Check if in draft state
$opportunity->isOpen();      // Check if status is open
$opportunity->getTitle();    // Get subject/title
$opportunity->getMemberName();
$opportunity->getOwnerName();
$opportunity->getCustomField('key');
```

### OpportunityItemData

```php
$item->id;
$item->opportunity_id;
$item->product_id;
$item->quantity;
$item->item_type;
$item->name;
$item->price;

// Helper methods
$item->isRental();
$item->isSale();
$item->isService();
$item->getProductName();
$item->getProductSku();
$item->getCustomField('key');
```

---

## Testing

```bash
# Run tests from the package directory
./vendor/bin/pest

# Or from Laravel project
./vendor/bin/pest tests/Unit/CurrentRms
```

---

## Package Structure

```
.
├── src/
│   ├── Client/
│   │   ├── CurrentRmsClient.php
│   │   ├── Auth/
│   │   │   ├── AuthManager.php
│   │   │   ├── ApiKeyAuth.php
│   │   │   └── TokenStorage.php
│   │   └── Exceptions/
│   ├── Data/
│   │   ├── OpportunityData.php
│   │   └── OpportunityItemData.php
│   ├── Endpoints/
│   │   ├── BaseEndpoint.php
│   │   ├── OpportunitiesEndpoint.php
│   │   ├── OpportunityItemsEndpoint.php
│   │   └── ScopedOpportunityItemsEndpoint.php
│   ├── Facades/
│   │   └── CurrentRms.php
│   └── CurrentRmsServiceProvider.php
├── config/
│   └── current-rms.php
├── tests/
├── LICENSE
├── README.md
└── composer.json
```

---

## License

MIT License. See [LICENSE](LICENSE) for more information.

---

## Credits

- **Author**: William Becker
- **Package**: wjbecker/current-rms-php
