# pipefy-api
PHP wrapper for the Pipefy API

## Requirements

PHP 5.3+

## Usage

### Get cards from the specified Pipe and Phase

```php
// Set our API key to access the API
Pipefy::init("User's API Key", "User's Email");

// Create Pipe definition; 
$pipe = new Pipe();

// Fetch data from Pipefy for the specified pipe ID
$pipe->fetch(1234);

// Find phase in pipe
$phase = $pipe->get_phase_by_name("Phase name");

// Fetch data for Phase. It's not necessary, but it will provide more detailed info about the phase and its cards.
$phase->fetch();

// This code can be shorten like this
// $phase = (new Pipe())->fetch(1234)->get_phase_by_name("Phase name")->fetch();

foreach ($phase->cards as $key => $card) {
  echo ($card->title);
}
```

### Create card

#### Via pipe object
```php
// Set our API key to access the API
Pipefy::init("User's API Key", "User's Email");

// Get data for the pipe with id 1234; 
$pipe = (new Pipe())->fetch(1234);

// Create some fields definition
$fields = array();
$fields[] = array(
    "field_id" => 54321,  // you can get needed field ids from the Phase object
    "value" => "val"
);
$fields[] = array(
    "field_id" => 123,
    "value" => "val"
);

// Create card and fetch its data
// provide null as the last parameter to create a general (not connected) card
$card = $pipe->create_card("Card title", $fields, null);
```

#### Directly
```php
// Set our API key to access the API
Pipefy::init("User's API Key", "User's Email");

// Create some fields definition
$fields = array();
$fields[] = array(
    "field_id" => 54321,  // you can get needed field ids from the Phase object
    "value" => "val"
);
$fields[] = array(
    "field_id" => 123,
    "value" => "val"
);

// Create a card definition
$card = new Card();

// Create card in Pipefy and fetch it data
// 1234 - id of the pipe where you want to create a card
// provide null as the last parameter to create a general (not connected) card
$card->create_card("Card title", 1234, $fields, null);   
```

### Create connected card  (not documented function of the Pipefy official API)

#### Via pipe object
```php
// Set our API key to access the API
Pipefy::init("User's API Key", "User's Email");

// Get data for the pipe with id 1234; 
$pipe = (new Pipe())->fetch(1234);

// Create some fields definition
$fields = array();
$fields[] = array(
    "field_id" => 54321,  // you can get needed field ids from the Phase object
    "value" => "val"
);
$fields[] = array(
    "field_id" => 123,
    "value" => "val"
);

// Create card and fetch its data
// provide parent card id as the last parameter to create connected card
$card = $pipe->create_card("Card title", $fields, 5555);
```

#### Directly
The same as when you create a general card. Just replace **null** in the last parameter of the "create_card" function with the parent card id.
