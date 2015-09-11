<?php

return [

    'clientSideLimit' => 10000,

    // number of pages to pipeline when using server side ajax loading
    'pipeline' => 10,

    'pagingTypeSmall' => 'numbers',
    'pagingTypeMedium' => 'simple_numbers',
    'pagingTypeLarge' => 'full_numbers',

    'pageLengthSmall' => 25,
    'pageLengthMedium' => 50,
    'pageLengthLarge' => 100,

    'lengthMenuSmall' => '[[10, 25, 50, -1], [10, 25, 50, "all"]]',
    'lengthMenuMedium' => '[[10, 25, 50, 100, 250, 500, -1], [10, 25, 50, 100, 250, 500, "all"]]',
    'lengthMenuLarge' => '[[10, 25, 50, 100, 250, 500, 1000, -1], [10, 25, 50, 100, 250, 500, 1000, "all"]]',

];
