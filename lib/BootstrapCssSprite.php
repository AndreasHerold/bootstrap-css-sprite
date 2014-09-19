<?php

/**
 * BootstrapCssSprite
 *
 * Displays multiple images as a sprite in a Bootstrap 3 style: <span class="img-kitty"></span>
 *
 * To merges all images from a given directory to one image and
 * to creates CSS file call generate() method.
 *
 * @author Oleg Poludnenko <oleg@poludnenko.info>
 * @version 0.6.7
 */
class BootstrapCssSprite
{
    /**
     * List of errors
     */
    const ERROR_NO_SOURCE_IMAGES        = 'no-source-images';
    const ERROR_SPRITE_EQUALS_TO_SOURCE = 'sprite-equals-to-source';
    const ERROR_WRONG_IMAGE_FORMAT      = 'wrong-image-format';
    const ERROR_UNKNOWN_IMAGE_EXT       = 'unknown-image-ext';

    /**
     * List of magic actions (file suffix and CSS prefix)
     *
     * @link http://webdesign.tutsplus.com/tutorials/htmlcss-tutorials/quick-tip-easy-css3-checkboxes-and-radio-buttons/
     *       Example of :checked usage
     * @var array
     */
    public static $magicActions = array('hover', 'active', 'target', 'checked', 'disabled');

    /**
     * @var array
     */
    protected $_imgList = array();

    /**
     * @var bool
     */
    protected $_checkSubDirs = true;

    /**
     * Path to source images
     * @var string
     */
    protected $_imgSourcePath;

    /**
     * List of source image's extensions to process
     * @var string
     */
    protected $_imgSourceExt = 'jpg,jpeg,gif,png';

    /**
     * Image size (width or height) wich is greater, will be skipped
     * @var int
     */
    protected $_imgSourceSkipSize;

    /**
     * Path to result image
     * @var string
     */
    protected $_imgDestPath;

    /**
     * Path to result CSS file
     * @var string
     */
    protected $_cssPath;

    /**
     * Namespace (prefix) for CSS classes
     * @var string
     */
    protected $_cssNamespace = 'img';

    /**
     * @var bool
     */
    protected $_compactImage = false;

    /**
     * Result image URL in the CSS file
     * @var string
     */
    protected $_cssImgUrl;

    /**
     * @var string
     */
    protected $_initialCssSelectors = 'default';

    /**
     * @var string
     */
    protected $_initialCssStyleCompact = false;

    /**
     * @var string
     */
    protected $_stylesHeight = '64px';

    /**
     * @var string
     */
    protected $_stylesWidth = '64px';

    /**
     * @var string
     */
    protected $_stylesVerticalAlign = 'middle';

    /**
     * @var string
     */
    protected $_stylesDisplay = 'inline-block';

    /**
     * @var string
     */
    protected $_stylesBackgroundRepeat = 'no-repeat';

    /**
     * @var string
     */
    protected $_stylesBackgroundPosition = '0 0';

    /**
     * List of generated tag (can be used for example)
     * @var array
     */
    protected $_tagList = array();

    /**
     * List of errors
     * @var array
     */
    protected $_errors = array();

    /**
     * Flag to set if checkModificationTime-method has to be executed
     *
     * @var bool
     */
    protected $_checkIfImageIsCreated = true;

    /**
     * Constructor
     *
     * @param array $config
     */
    public function __construct(array $config = array())
    {
        // Initial configuration
        foreach ($config as $key => $value) {
            $key = '_' . $key;
            if (property_exists($this, $key)) {
                $this->$key = $value;
            }
        }
    }

    /**
     * @param boolean $checkIfImageIsCreated
     *
     * @return BootstrapCssSprite
     */
    public function setCheckIfImageIsCreated($checkIfImageIsCreated)
    {
        $this->_checkIfImageIsCreated = $checkIfImageIsCreated;
        return $this;
    }

    /**
     * @param string $cssImgUrl
     * @return BootstrapCssSprite
     */
    public function setCssImgUrl($cssImgUrl)
    {
        $this->_cssImgUrl = $cssImgUrl;
        return $this;
    }

    /**
     * @param string $cssNamespace
     * @return BootstrapCssSprite
     */
    public function setCssNamespace($cssNamespace)
    {
        $this->_cssNamespace = $cssNamespace;
        return $this;
    }

    /**
     * @param boolean $compactImage
     *
     * @return BootstrapCssSprite
     */
    public function setCompactImage($compactImage)
    {
        $this->_compactImage = $compactImage;
        return $this;
    }

    /**
     * @param string $cssPath
     * @return BootstrapCssSprite
     */
    public function setCssPath($cssPath)
    {
        $this->_cssPath = $cssPath;
        return $this;
    }

    /**
     * @param string $imgDestPath
     * @return BootstrapCssSprite
     */
    public function setImgDestPath($imgDestPath)
    {
        $this->_imgDestPath = $imgDestPath;
        return $this;
    }

    /**
     * @param string $imgSourceExt
     * @return BootstrapCssSprite
     */
    public function setImgSourceExt($imgSourceExt)
    {
        $this->_imgSourceExt = $imgSourceExt;
        return $this;
    }

    /**
     * @param boolean $checkSubDirs
     * @return BootstrapCssSprite
     */
    public function setCheckSubDirs($checkSubDirs)
    {
        $this->_checkSubDirs = $checkSubDirs;
        return $this;
    }

    /**
     * @param string $imgSourcePath
     * @return BootstrapCssSprite
     */
    public function setImgSourcePath($imgSourcePath)
    {
        $this->_imgSourcePath = $imgSourcePath;
        return $this;
    }

    /**
     * @param int $imgSourceSkipSize
     * @return BootstrapCssSprite
     */
    public function setImgSourceSkipSize($imgSourceSkipSize)
    {
        $this->_imgSourceSkipSize = $imgSourceSkipSize;
        return $this;
    }

    /**
     * @param array $tagList
     * @return BootstrapCssSprite
     */
    public function setTagList($tagList)
    {
        $this->_tagList = $tagList;
        return $this;
    }

    /**
     * @param string $stylesBackgroundPosition
     * @return BootstrapCssSprite
     */
    public function setStylesBackgroundPosition($stylesBackgroundPosition)
    {
        $this->_stylesBackgroundPosition = $stylesBackgroundPosition;
        return $this;
    }

    /**
     * @param string $stylesBackgroundRepeat
     * @return BootstrapCssSprite
     */
    public function setStylesBackgroundRepeat($stylesBackgroundRepeat)
    {
        $this->_stylesBackgroundRepeat = $stylesBackgroundRepeat;
        return $this;
    }

    /**
     * @param string $stylesDisplay
     * @return BootstrapCssSprite
     */
    public function setStylesDisplay($stylesDisplay)
    {
        $this->_stylesDisplay = $stylesDisplay;
        return $this;
    }

    /**
     * @param string $stylesHeight
     * @return BootstrapCssSprite
     */
    public function setStylesHeight($stylesHeight)
    {
        $this->_stylesHeight = $stylesHeight;
        return $this;
    }

    /**
     * @param string $initialCssSelectors
     *
     * @return BootstrapCssSprite
     */
    public function setInitialCssSelectors($initialCssSelectors)
    {
        $this->_initialCssSelectors = $initialCssSelectors;
        return $this;
    }

    /**
     * @param string $initialCssStyleCompact
     *
     * @return BootstrapCssSprite
     */
    public function setInitialCssStyleCompact($initialCssStyleCompact)
    {
        $this->_initialCssStyleCompact = $initialCssStyleCompact;
        return $this;
    }

    /**
     * @param string $stylesVerticalAlign
     * @return BootstrapCssSprite
     */
    public function setStylesVerticalAlign($stylesVerticalAlign)
    {
        $this->_stylesVerticalAlign = $stylesVerticalAlign;
        return $this;
    }

    /**
     * @param string $stylesWidth
     * @return BootstrapCssSprite
     */
    public function setStylesWidth($stylesWidth)
    {
        $this->_stylesWidth = $stylesWidth;
        return $this;
    }

    /**
     * Returns tags
     *
     * @return array
     */
    public function getTagList()
    {
        return $this->_tagList;
    }

    /**
     * Add an error
     *
     * @param int $type
     * @param string $message
     */
    public function addError($type, $message = '')
    {
        $this->_errors[] = array(
            'type'      => $type,
            'message'   => $message,
        );
    }

    /**
     * @param array $errors
     * @return BootstrapCssSprite
     */
    public function setErrors($errors)
    {
        $this->_errors = $errors;
        return $this;
    }

    /**
     * Returns errors
     *
     * @return array
     */
    public function getErrors()
    {
        return $this->_errors;
    }

    /**
     * Merges images and generates CSS file
     */
    public function generate()
    {
        // Clear errors
        $this->setErrors(array());

        // Normalize destination image path
        $this->_imgDestPath = str_replace(array('\\', '/'), DIRECTORY_SEPARATOR, $this->_imgDestPath);

        if ($this->_checkIfImageIsCreated) {
            $this->_checkModificationTime();
        }

        $imgWidth = $imgHeight = $xOffset = 0;
        $this->_getImageList($this->_imgSourcePath, $xOffset, $imgWidth, $imgHeight);
        if (count($this->_imgList) === 0) {
            $this->addError(static::ERROR_NO_SOURCE_IMAGES);
            return;
        }

        //Create Transparent Image
        $dest = $this->_initDestImage($imgWidth, $imgHeight);

        // Init CSS
        $cssList = $this->_initCssList();

        // Copy all images, create CSS file and list of tags
        foreach ($this->_imgList as $imgPath => $imgData) {

            // Copy image
            if (!$this->_copyImageToDestImage($imgData, $imgPath, $dest)) {
                continue;
            }
            // Append CSS (if not a magic action)
            $class = $this->_getImageClassName($imgPath, $imgData);
            $isMagicAction = $this->_isMagicAction($class);

            if (!$isMagicAction) {
                $this->_setImageCssData($cssList, $class, $imgData);

                $this->_setStyleForMagicActionImage($cssList, $imgPath, $imgData, $class);

                // Append tag
                if ($this->_initialCssSelectors != 'default') {
                    $class = mb_substr($this->_initialCssSelectors, 1) . ' ' . mb_substr($class, 1);
                } else {
                    $class = mb_substr($class, 1);
                }
                $this->_tagList[] = '<span class="' . $class . '"></span>';
            }
        }

        $this->_saveImageFile($dest);
        imagedestroy($dest);

        $this->_saveCssFile($cssList);
    }

    /**
     * Check modification time
     *
     * @return void
     */
    protected function _checkModificationTime()
    {
        if ((is_dir($this->_imgSourcePath)) && (is_file($this->_imgDestPath))) {
            $imgSourceStat = stat($this->_imgSourcePath);
            $imgDestStat = stat($this->_imgDestPath);
            if ($imgSourceStat['mtime'] <= $imgDestStat['mtime']) {
                $this->addError(static::ERROR_SPRITE_EQUALS_TO_SOURCE);
                return;
            }
        }
    }

    /**
     * Create transparent image
     *
     * @param int $imgWidth
     * @param int $imgHeight
     * @return resource
     */
    protected function _initDestImage($imgWidth, $imgHeight)
    {
        $dest = imagecreatetruecolor($imgWidth, $imgHeight);
        if ($this->_compactImage) {
            // Convert to palette-based with no dithering and 255 colors with alpha
            imagetruecolortopalette($dest, false, 255);
        } else {
            imagesavealpha($dest, true);
        }
        $trans_colour = imagecolorallocatealpha($dest, 0, 0, 0, 127);
        imagefill($dest, 0, 0, $trans_colour);
        return $dest;
    }

    /**
     * Get list of images in $dir
     *
     * @param $dir
     * @param $xOffset
     * @param $imgWidth
     * @param $imgHeight
     */
    protected function _getImageList($dir, &$xOffset, &$imgWidth, &$imgHeight)
    {
        $imageList = $this->_getFileList($dir);
        foreach ($imageList as $imagePath) {

            // Skip previously generated sprite
            if ($imagePath === $this->_imgDestPath) {
                continue;
            }

            // Get image sizes
            $imageSize = @getimagesize($imagePath);
            if ($imageSize === false) {
                $this->addError($this::ERROR_WRONG_IMAGE_FORMAT, $imagePath);
                continue;
            } else {
                list($itemWidth, $itemHeight, $itemType) = $imageSize;
            }

            // Check size
            if ($this->_imgSourceSkipSize) {
                if (($itemWidth > $this->_imgSourceSkipSize) || ($itemHeight > $this->_imgSourceSkipSize)) {
                    continue;
                }
            }

            // Inc sprite size
            $imgWidth += $itemWidth;
            if ($itemHeight > $imgHeight) {
                $imgHeight = $itemHeight;
            }

            // Push image to the list
            $this->_imgList[$imagePath] = array(
                'width'     => $itemWidth,
                'height'    => $itemHeight,
                'x'         => $xOffset,
                'ext'       => image_type_to_extension($itemType, false),
            );

            $xOffset += $itemWidth;
        }

        if ($this->_checkSubDirs) {
            $subdirList = glob($dir . DIRECTORY_SEPARATOR . '*', GLOB_ONLYDIR);
            foreach ($subdirList as $subdir) {
                $this->_getImageList($subdir, $xOffset, $imgWidth, $imgHeight);
            }
        }
    }

    /**
     * Puts all the files in $dir with the extension given by $_imgSourceExt in an array
     *
     * @param string $dir
     * @return array
     */
    protected function _getFileList($dir)
    {
        $fileList = glob($dir . DIRECTORY_SEPARATOR . '*.{' . $this->_imgSourceExt . '}', GLOB_BRACE);
        return $fileList;
    }

    /**
     * Initialises the css list
     *
     * @param array $cssList
     *
     * @return array
     */
    protected function _initCssList($cssList = array())
    {
        if ($this->_initialCssSelectors == 'default') {
            $initSelectors = array(
                '[class^="' . $this->_cssNamespace . '-"]',
                '[class*=" ' . $this->_cssNamespace . '-"]',
            );
        } else {
            $initSelectors = array($this->_initialCssSelectors);
        }
        if ($this->_initialCssStyleCompact) {
            $initStyles = array(
                'background'      => implode(' ', array(
                        'url("' . $this->_cssImgUrl . '")',
                        $this->_stylesBackgroundRepeat,
                        $this->_stylesBackgroundPosition,
                    )),
                'display'         => $this->_stylesDisplay,
                'height'          => $this->_stylesHeight,
                'vertical-align'  => $this->_stylesVerticalAlign,
                'width'           => $this->_stylesWidth,
            );
        } else {
            $initStyles = array(
                'background-image'    => 'url("' . $this->_cssImgUrl . '")',
                'background-position' => $this->_stylesBackgroundPosition,
                'background-repeat'   => $this->_stylesBackgroundRepeat,
                'display'             => $this->_stylesDisplay,
                'height'              => $this->_stylesHeight,
                'vertical-align'      => $this->_stylesVerticalAlign,
                'width'               => $this->_stylesWidth,
            );
        }

        $cssList[] = array(
            'selectors' => $initSelectors,
            'styles' => $initStyles
        );
        return $cssList;
    }

    /**
     * Copies the image $imgPath in the resource $dest
     *
     * @param array $imgData
     * @param string $imgPath
     * @param resource $dest
     * @return bool
     */
    protected function _copyImageToDestImage($imgData, $imgPath, $dest)
    {
        $imgCreateFunc = 'imagecreatefrom' . $imgData['ext'];
        if (!function_exists($imgCreateFunc)) {
            return false;
        }
        $src = $imgCreateFunc($imgPath);
        imagealphablending($src, true);
        imagesavealpha($src, true);
        imagecopy($dest, $src, $imgData['x'], 0, 0, 0, $imgData['width'], $imgData['height']);
        imagedestroy($src);

        return true;
    }

    /**
     * Generates the style class name for the given image $imagePath
     *
     * @param string $imgPath
     * @param array $imgData
     *
     * @return string
     */
    protected function _getImageClassName($imgPath, $imgData)
    {
        $sourcePathLeng = mb_strlen($this->_imgSourcePath);
        $class = '.' . $this->_cssNamespace . '-' . mb_substr($imgPath, $sourcePathLeng + 1);
        $class = mb_substr($class, 0, mb_strlen($class) - mb_strlen($imgData['ext']) - 1);
        $class = str_replace(DIRECTORY_SEPARATOR, '-', $class);
        return $class;
    }

    /**
     * Checks if $class has a magic action like .image-foo.hover in it.
     *
     * @param $class
     *
     * @return bool
     */
    protected function _isMagicAction($class)
    {
        foreach (static::$magicActions as $magicAction) {
            $isMagicAction = (mb_substr($class, -mb_strlen('.' . $magicAction)) === '.' . $magicAction);
            if ($isMagicAction) {
                return true;
            }
        }
        return false;
    }

    /**
     * Creates the css style data for the given Image ($imgData)
     *
     * @param $cssList
     * @param $class
     * @param $imgData
     * @return array
     */
    protected function _setImageCssData(&$cssList, $class, $imgData)
    {
        $styles = array(
            'background-position'   => '-' . $imgData['x'] . 'px 0'
        );

        $this->_addSizeToStyle($cssList, $styles, $imgData);


        $cssList[] = array(
            'selectors' => array($class),
            'styles' => $styles,
        );
    }

    /**
     * Sets the additional style data in $cssList for the magic action image like hover and so on.
     *
     * @param array $cssList
     * @param string $imgPath
     * @param array $imgData
     * @param string $class
     */
    protected function _setStyleForMagicActionImage(&$cssList, $imgPath, $imgData, $class)
    {
        $extPos = mb_strrpos($imgPath, $imgData['ext']);
        foreach (static::$magicActions as $magicAction) {
            $magicActionPath = '';
            if ($extPos !== false) {
                // Check if image has magic action image (active, hover, target)
                $magicActionPath = substr_replace($imgPath,
                    $magicAction . '.' . $imgData['ext'],
                    $extPos,
                    strlen($imgData['ext'])
                );
                $hasMagicAction = isset($this->_imgList[$magicActionPath]);
            } else {
                $hasMagicAction = false;
            }
            if ($hasMagicAction) {
                $magicActionData = $this->_imgList[$magicActionPath];
                $css = array();
                if (in_array($magicAction, array('checked', 'disabled'))) {
                    $css['selectors'] = array(
                        "input:{$magicAction} + {$class}",
                        "{$class}.{$magicAction}",
                    );
                } else {
                    $css['selectors'] = array(
                        "{$class}:{$magicAction}",
                        "{$class}.{$magicAction}",
                        ".wrap-{$this->_cssNamespace}:{$magicAction} {$class}",
                        ".wrap-{$this->_cssNamespace}.{$magicAction} {$class}",
                    );
                }
                $css['styles'] = array(
                    'background-position'   => '-' . $magicActionData['x'] . 'px 0',
                    'background-position-x' => '-' . $magicActionData['x'] . 'px',
                );

                $this->_addSizeToStyle($cssList, $css, $magicActionData);

                $cssList[] = $css;
            }
        }
    }

    /**
     * Checks the size data of the image with the given base style data given by $cssList.
     * If there any differences in height or width, the size will be added as a style otherwise not
     *
     * @param array $cssList styledatalist for the whole css file
     * @param array $style styledatalist for the current element
     * @param array $imgData data for the image
     */
    protected function _addSizeToStyle($cssList, &$style, $imgData)
    {
        //If the image has the same size as given in base-css data skip entry for the size
        if ($cssList[0]['styles']['height'] != $imgData['height'] . 'px') {
            $style['height'] = $imgData['height'] . 'px';
        }

        if ($cssList[0]['styles']['width'] != $imgData['width'] . 'px') {
            $style['width'] = $imgData['width'] . 'px';
        }
    }

    /**
     * Saves the image file to imgDestPath
     * @param resource $dest
     * @return void
     */
    protected function _saveImageFile($dest)
    {
        // Save image to file
        $imgDestExt = mb_strtolower(mb_substr($this->_imgDestPath, mb_strrpos($this->_imgDestPath, '.') + 1));
        switch ($imgDestExt) {
            case 'jpg':
            case 'jpeg':
                imagejpeg($dest, $this->_imgDestPath);
                break;
            case 'gif':
                imagegif($dest, $this->_imgDestPath);
                break;
            case 'png':
                imagepng($dest, $this->_imgDestPath);
                break;
            default:
                $this->addError(static::ERROR_UNKNOWN_IMAGE_EXT, $this->_imgDestPath);
                return;
                break;
        }
    }
    /**
     * Saves the cssList to the file given by cssPath
     *
     * @param array $cssList
     */
    protected function _saveCssFile($cssList)
    {
        // Save CSS file
        $cssString = '';
        foreach ($cssList as $css) {
            $cssString .= implode(',', $css['selectors']) . '{';
            foreach ($css['styles'] as $key => $value) {
                $cssString .= $key . ':'  .$value . ';';
            }
            $cssString .= '}';
        }
        file_put_contents($this->_cssPath, $cssString);
    }
}
