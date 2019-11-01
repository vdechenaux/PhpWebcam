<?php

namespace VDX\Webcam;

class Webcam
{
    private \FFI $ffi;
    private ?\FFI\CData $cvCamera = null;
    private ?int $desiredWidth = null;
    private ?int $desiredHeight = null;

    /**
     * @throws \FFI\Exception If OpenCV is not available
     */
    public function __construct()
    {
        $def = <<<DEF
            enum {
                CV_CAP_ANY = 0
            };
            enum {
                 CV_CAP_PROP_FRAME_WIDTH = 3,
                 CV_CAP_PROP_FRAME_HEIGHT = 4
            };
        
            typedef struct CvCapture CvCapture;
            typedef struct IplImage IplImage;
            CvCapture* cvCreateCameraCapture(int index);
            IplImage* cvQueryFrame(CvCapture* capture);
            int cvSaveImage(const char *filename, const IplImage *image);
            void cvReleaseCapture (CvCapture **capture);
            int cvSetCaptureProperty(CvCapture* capture, int property_id, double value);
            void cvFlip(const IplImage* src, IplImage* dst, int flip_mode);
DEF;

        $this->ffi = \FFI::cdef($def, "libopencv_videoio.so");
    }

    public function __destruct()
    {
        $this->close();
    }

    public function setDesiredSize(int $desiredWidth, int $desiredHeight): void
    {
        $this->desiredWidth = $desiredWidth;
        $this->desiredHeight = $desiredHeight;

        if ($this->cvCamera !== null) {
            $this->ffi->cvSetCaptureProperty($this->cvCamera, $this->ffi->CV_CAP_PROP_FRAME_WIDTH, $this->desiredWidth);
            $this->ffi->cvSetCaptureProperty($this->cvCamera, $this->ffi->CV_CAP_PROP_FRAME_HEIGHT, $this->desiredHeight);
        }
    }

    public function open(): bool
    {
        if ($this->cvCamera !== null) {
            return false;
        }

        $this->cvCamera = $this->ffi->cvCreateCameraCapture($this->ffi->CV_CAP_ANY);

        if ($this->cvCamera === null) {
            return false;
        }

        if ($this->desiredWidth !== null && $this->desiredHeight !== null) {
            $this->ffi->cvSetCaptureProperty($this->cvCamera, $this->ffi->CV_CAP_PROP_FRAME_WIDTH, $this->desiredWidth);
            $this->ffi->cvSetCaptureProperty($this->cvCamera, $this->ffi->CV_CAP_PROP_FRAME_HEIGHT, $this->desiredHeight);
        }

        return true;
    }

    public function close(): void
    {
        if ($this->cvCamera === null) {
            return;
        }

        $this->ffi->cvReleaseCapture(\FFI::addr($this->cvCamera));
        $this->cvCamera = null;
    }

    public function saveFrame(string $filename, bool $mirror = false): bool
    {
        if ($this->cvCamera === null) {
            return false;
        }

        $image = $this->ffi->cvQueryFrame($this->cvCamera);

        if ($image === null) {
            return false;
        }

        if ($mirror) {
            $this->ffi->cvFlip($image, null, 1);
        }

        return (bool) $this->ffi->cvSaveImage($filename, $image);
    }
}
