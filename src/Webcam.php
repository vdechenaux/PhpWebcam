<?php

namespace VDX\Webcam;

class Webcam
{
    private \FFI $ffi;
    private ?\FFI\CData $cvCamera = null;

    /**
     * @throws \FFI\Exception If OpenCV is not available
     */
    public function __construct()
    {
        $def = <<<DEF
            typedef struct CvCapture CvCapture;
            typedef struct IplImage IplImage;
            CvCapture* cvCreateCameraCapture(int index);
            IplImage* cvQueryFrame(CvCapture* capture);
            int cvSaveImage(const char *filename, const IplImage *image);
            void cvReleaseCapture (CvCapture **capture);
DEF;

        $this->ffi = \FFI::cdef($def, "libopencv_videoio.so");
    }

    public function __destruct()
    {
        $this->close();
    }

    public function open(): bool
    {
        if ($this->cvCamera !== null) {
            return false;
        }

        $this->cvCamera = $this->ffi->cvCreateCameraCapture(0);

        return $this->cvCamera !== null;
    }

    public function close(): void
    {
        if ($this->cvCamera === null) {
            return;
        }

        $this->ffi->cvReleaseCapture(\FFI::addr($this->cvCamera));
        $this->cvCamera = null;
    }

    public function saveFrame(string $filename): bool
    {
        return (bool) $this->ffi->cvSaveImage($filename, $this->ffi->cvQueryFrame($this->cvCamera));
    }
}
