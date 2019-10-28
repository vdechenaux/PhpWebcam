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
if ($webcam->open()) {
    $webcam->saveFrame('/tmp/test.jpg');
    $webcam->close();
}
```
