# Php Webcam

This is a PHP library to capture webcam frames.

## Demo

You can see this library in action [here](https://github.com/vdechenaux/PhpWebcamMjpeg) with a simple Mjpeg stream implementation.

## Requirements

- PHP > 7.4

- Ext FFI

- OpenCV (e.g. `libopencv-videoio-dev` on APT based systems)

## Installation

```
composer require vdechenaux/webcam
```

## Usage

```php
$webcam = new \VDX\Webcam\Webcam();

// It can produce an other size if your webcam does not support the provided size
$webcam->setDesiredSize(1280, 720);

if ($webcam->open()) {
    $webcam->saveFrame('/tmp/test.jpg'/*, true*/); // It accepts a second parameter to mirror the image
    $webcam->close();
}
```
