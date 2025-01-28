<?php
// Its called magic, beacuse it contains the magic numbers :)
// Realistically, you probably could put more shared logic in here, but this is fine for now.
//  - Or you could move these into taskManager.php and expose them publically.


// Status Constants

define("STATUS_TODO", 0);
define("STATUS_IN_PROGRESS", 1);
define("STATUS_DONE", 2);

// Status map
const STATUS_MAP = [
    STATUS_TODO => "Todo",
    STATUS_IN_PROGRESS => "In progress",
    STATUS_DONE => "Done",
];