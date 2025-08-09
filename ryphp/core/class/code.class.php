<?php

class code{
    private $img;
    public $width = 100;
    public $height = 35;
    public $background = '#ffffff';
    public $code ;
    public $code_string  = 'abcdefghkmnprstuvwyzABCDEFGHKLMNPRSTUVWYZ23456789';
    public $code_len = 4;
    public $font;
    public $font_size = 20;
    public $font_color;

    public function __construct(){
        $this->font = RYPHP_ROOT.'common/data/font/elephant.ttf';
        if(!is_file($this->font)) showmsg("验证码字体文件不存在!",'stop');
        if(!$this->check_gd()) showmsg('PHP扩展GD库未开启!','stop');
    }


    private function create_code(){
        $code = '';
        for($i =0; $i < $this ->code_len; $i++){
            $code .= $this->code_string[mt_rand(0,strlen($this->code_string) - 1)];
        }
        $this->code = $code;
    }

    public function get_code(){
        return strtolower($this->code);
    }


    private function check_gd(){
        return extension_loaded('gd') && function_exists('imagepng');
    }


    /**
     * Creates a new image with the specified dimensions and background color
     * This method initializes the image resource and fills it with the background color
     */
        /**
         * Creates a new true color image resource with specified width and height
         * @var resource $this->img The image resource
         */
        
        /**
         * Allocates the background color for the image
         * Converts hex color code to RGB values:
         * - substr($this->background,1,2) gets the red component
         * - substr($this->background,3,2) gets the green component 
         * - substr($this->background,5,2) gets the blue component
         * hexdec() converts hex values to decimal
         * @var int $background The color identifier
         */
        
        /**
         * Fills the entire image with the allocated background color
         * Parameters:
         * - $this->img: target image
         * - 0,0: starting x,y coordinates (top left)
         * - $this->width,$this->height: ending coordinates (bottom right)
         * - $background: fill color
         */
    public function create(){
        // 创建一个真彩色图片，返回图片资源标识符
        // $this->width 和 $this->height 定义了图片的宽度和高度
        $this->img = imagecreatetruecolor($this->width,$this -> height);

        // 为图片分配颜色
        // substr($this->background,1,2) 从十六进制颜色代码中提取红色分量
        // substr($this->background,3,2) 从十六进制颜色代码中提取绿色分量
        // substr($this->background,5,2) 从十六进制颜色代码中提取蓝色分量
        // hexdec() 将十六进制转换为十进制
        $background =  $this->allocate_color($this->background);

        // 在图片中填充一个矩形
        // 参数分别是：图片资源，起始x坐标，起始y坐标，结束x坐标，结束y坐标，颜色
        imagefilledrectangle($this->img,0,0,$this->width,$this->height,$background);
        $this->create_line();
        $this->create_font();
        $this->create_pix();
    }

    private function create_line() {
        $line_color = "#dcdcdc";
        $color = $this->allocate_color($line_color);

        //Draw horizontal lines
        $vertical_spacing = 5;
        $vertical_lines = floor($this -> height / $vertical_spacing);
        for($i = 1; $i < $vertical_lines; $i++){
            imageline($this -> img, 0, $i * $vertical_spacing,  $this-> width,$i * $vertical_spacing, $color);
        }

        //Draw vertical lines
        $horizontal_spacing = 10;
        $horizontal_lines = floor($this ->width / $horizontal_spacing);
        for($i = 1; $i < $horizontal_lines; $i++){
            imageline($this ->img, $i * $horizontal_spacing, 0, $i * $horizontal_spacing, $this-> height, $color);
        }
    }

    private function create_font() {
        $this->create_code();
        
        // Pre-calculate font color if specified
        $font_color = null;
        if (!empty($this->font_color)) {
            $font_color = $this->allocate_color($this->font_color);
        }
        
        // Calculate base x position once
        $x_base = intval(($this->width - 10) / $this->code_len);
        $y_min = intval($this->height / 1.3);
        $y_max = $this->height - 5;
        
        // Draw each character
        for ($i = 0; $i < $this->code_len; $i++) {
            // Generate random color if not specified
            if ($font_color === null) {
                $font_color = imagecolorallocate($this->img, 
                    mt_rand(50, 155),
                    mt_rand(50, 155),
                    mt_rand(50, 155)
                );
            }
            
            imagettftext(
                $this->img, 
                $this->font_size,
                mt_rand(-30, 30),  // random angle
                $x_base * $i + mt_rand(6, 10),  // x position
                mt_rand($y_min, $y_max),  // y position
                $font_color,
                $this->font,
                $this->code[$i]
            );
        }
        
        $this->font_color = $font_color;
    }




    private function create_pix() {
        $pix_color = $this->font_color;
        $width = $this->width;
        $height = $this->height;
        
        // Batch generate random points
        $points = array_map(function() use ($width, $height) {
            return [mt_rand(0, $width), mt_rand(0, $height)];
        }, range(1, 50));
        
        // Draw pixels
        foreach ($points as $point) {
            imagesetpixel($this->img, $point[0], $point[1], $pix_color);
        }

        // Draw lines
        for ($i = 0; $i < 2; $i++) {
            imageline(
                $this->img, 
                mt_rand(0, $width), 
                mt_rand(0, $height), 
                mt_rand(0, $width), 
                mt_rand(0, $height), 
                $pix_color
            );
        }

        // Draw arc
        imagearc(
            $this->img,
            mt_rand(0, $width), 
            mt_rand(0, $height),
            mt_rand(0, $width), 
            mt_rand(0, $height),
            mt_rand(0, 160), 
            mt_rand(0, 200), 
            $pix_color
        );
    }
	


    /**
     * 显示验证码
     */
    public function show_code() {
        // Create image if not already created
        if (!isset($this->img)) {
            $this->create();
        }
        
        // Set cache control headers to prevent caching
        header('Cache-Control: no-store, no-cache, must-revalidate, max-age=0');
        header('Pragma: no-cache');
        
        // Set content type header
        header('Content-Type: image/png');
        
        // Output image with maximum compression (9)
        imagepng($this->img, null, 9);
        
        // Clean up resources
        imagedestroy($this->img);
        $this->img = null;
    }

    // Helper method to allocate color from hex string
    private function allocate_color($hex_color) {
        return imagecolorallocate($this->img,
            hexdec(substr($hex_color, 1, 2)),
            hexdec(substr($hex_color, 3, 2)),
            hexdec(substr($hex_color, 5, 2))
        );
    }
}