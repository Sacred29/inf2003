<?php
require 'vendor/autoload.php';
// Connect to Database
$mongoUri = getenv('MONGODB_URI') ?: 'mongodb://localhost:27017';
$client = new MongoDB\Client($mongoUri);

$db = $client->eLibDatabase;
$borrowedCollection = $db->Borrowed;

// Find records where borrowedDate or expiryDate is a string
$records = $borrowedCollection->find([
    '$or' => [
        ['borrowedDate' => ['$type' => 'string']],
        ['expiryDate' => ['$type' => 'string']]
    ]
]);

// Iterate over the records and convert the string dates to UTCDateTime objects
foreach ($records as $record) {
    $updateFields = [];

    // Convert borrowedDate if it's a string
    if (isset($record['borrowedDate']) && is_string($record['borrowedDate'])) {
        $timestamp = strtotime($record['borrowedDate']) * 1000;
        $updateFields['borrowedDate'] = new MongoDB\BSON\UTCDateTime($timestamp);
    }

    // Convert expiryDate if it's a string
    if (isset($record['expiryDate']) && is_string($record['expiryDate'])) {
        $timestamp = strtotime($record['expiryDate']) * 1000;
        $updateFields['expiryDate'] = new MongoDB\BSON\UTCDateTime($timestamp);
    }

    // Update the document with the converted dates
    if (!empty($updateFields)) {
        $borrowedCollection->updateOne(
            ['_id' => $record['_id']],
            ['$set' => $updateFields]
        );
        echo "Updated record with ID: " . $record['_id'] . "\n";
    }
}

echo "Migration completed.\n";

