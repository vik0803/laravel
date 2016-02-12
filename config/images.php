<?php

return [

    'rootDirectory' => 'images',
    'chunksDirectory' => 'chunks',
    'originalDirectory' => 'o',
    'thumbnailSmallDirectory' => 's',
    'thumbnailMediumDirectory' => 'm',
    'thumbnailLargeDirectory' => 'l',
    'sliderDirectory' => 'slider',

    'extensions' => ['jpg', 'png', 'gif', 'jpeg'],
    'quality' => 90,

    'imageMaxWidth' => 1920,
    'imageMaxHeight' => 1920,

    'thumbnailSmallWidth' => 320,
    'thumbnailSmallHeight' => 240,
    'thumbnailMediumWidth' => 600,
    'thumbnailMediumHeight' => 480,
    'thumbnailLargeWidth' => 800,
    'thumbnailLargeHeight' => 600,
    'thumbnailCanvasBackground' => [255, 255, 255, 0], // transparent [png] or white [jpg]

    'sliderWidth' => 1920,
    'sliderHeight' => 500,

    'watermark' => storage_path('app/images/watermark.png'),
    'watermarkPosition' => 'bottom-right',
    'watermarkOffsetX' => 25,
    'watermarkOffsetY' => 25,

];
