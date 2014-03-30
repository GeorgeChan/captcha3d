<?php

class captcha3d {
    //随机验证码基础字符表
    //Random CAPTCHA based character chart
    protected $baseChar = "ABCDEFGHIJKLMNPQRSTUVXYZ23456789";
    //验证码
    //CAPTCHA
    protected $captcha = "";
    //定义图像宽度
    //Define image width
    protected $width = 120;
    //定义图像高度
    //Define image high
    protected $high = 37;
    //定义立体图形厚度
    //Define thickness of stereoscopic graphics
    protected $thickness = 2;
    //定义立体线条增长方向,取值 1 或 -1
    //Define Stereo-line growth orientation，Value 1 or -1
    protected $direction = 1;
    //定义最低显示厚度
    //Define minimum display thick
    protected $thicknessShow = 0;
    //定义线条间隔
    //Define line interval
    protected $interval = 3;
    //定义倾斜角度
    //Define angled
    protected $angle = 0;
    //定义字体文件名
    //Define font file
    protected $font = "fonts/ariali.ttf";
    //定义字体大小
    //Define font size
    protected $fontsize = 29;
    //定义颜色，默认为黑色
    //Define color，Default: Black
    protected $color = [0,0,0];
    //定义工作图层
    //Defining layers
    protected $im;
    //定义字体图层
    //Defines the font layers
    protected $fim;


    //Function: setCaptcha
    function setCaptcha($captcha){
        // 如未定义，则生成4位验证码
        if (empty($captcha)) {
            $this->captcha = "";
            for ($i=1;$i<=4;$i++){
                $this->captcha .= $this->baseChar[mt_rand(0,strlen($this->baseChar)-1)];
            }
        }else{
            $this->captcha = $captcha;
        }
    }

    //Function: getCaptcha
    function getCaptcha(){
            return $this->captcha;
    }


    //构造函数，初始化
    //parameters：
    // $captcha    string  校验码字符串，空则自动生成4位校验码
    //                     Check code string, leave blank to auto-generate a 4-bit CAPTCHA
    // $fontsize   int     字体大小
    //                     Font size
    // $color      array   颜色，数组 [r,g,b]
    //                     Color，array [red,green,blue]
    // $width      int     图像宽度
    //                     Image width
    // $high       int     图像高度
    //                     Image high
    // $thickness  int     立体图形厚度
    //                     Thickness of stereoscopic graphics
    // $direction  int     立体线条增长方向,取值 1 或 -1
    //                     Stereo-line growth orientation，Value 1 or -1
    // $thicknessShow int  定义最低显示厚度,取值 0,1,2...
    //                     Minimum display thick,Value 0,1,2...
    // $interval   int     线条间隔,取值 2,3,4...
    //                     Line interval,Value 2,3,4...
    // $angle      int     倾斜角度,最佳取值 -15 到 15 之间,0 表示水平
    //                     Angle, best value between 15 and-15, 0 is horizontally
    // $font       string  字体文件名, 包含路径
    //                     Font file, include path



    function __construct($captcha,$fontsize,$color,$width,$high,$thickness,$direction,$thicknessShow,$interval,$angle,$font) {
        $this->setCaptcha($captcha);

        if (!empty($captcha)) {
            $this->width=$width;
        }

        if (!empty($fontsize)) {
            $this->fontsize=$fontsize;
        }

        if (!empty($color)) {
            $this->color=$color;
        }

        if (!empty($width)) {
            $this->width=$width;
        }

        if (!empty($high)) {
            $this->high=$high;
        }

        if (!empty($thickness)) {
            $this->thickness=$thickness;
        }

        if (!empty($direction)) {
            $this->direction=$direction;
        }

        if (!empty($thicknessShow)){
            $this->thicknessShow=$thicknessShow;
        }

        if (!empty($interval)) {
            $this->interval=$interval;
        }

        if (!empty($angle)) {
            $this->angle=$angle;
        }

        if (!empty($font)) {
            $this->font=$font;
        }

    }

    public function render(){
        $this->im = @imagecreate($this->width, $this->high)
            or die("Cannot Initialize new GD image stream");

        //Set default colors
        $white = imagecolorallocate($this->im, 255, 255, 255);
        $penColor = imagecolorallocate($this->im, $this->color[0],$this->color[1],$this->color[2]);

        $this->fim = @imagecreate($this->width, $this->high)
            or die("Cannot Initialize new GD image stream");

        //Set default colors
        $fwhite = imagecolorallocate($this->fim, 255, 255, 255);
        $fblack = imagecolorallocate($this->fim, 0, 0, 0);

        @imagettftext($this->fim,$this->fontsize,0,5,$this->high-5,$fblack,$this->font,$this->captcha);

        // y轴 行循环
        $yTan = tan(deg2rad($this->angle));

        for ($y=$this->high-1;$y>0;$y-=$this->interval){
            $yInte=$y;
            $yThickness=0;
            $xMark=0;
            for ($x=0;$x<$this->width;$x++){
                $cIndex = ImageColorAt($this->fim,$x,$y);
                if ($cIndex!=0){
                    $xMark=1;
                    $yThickness ++;
                    // 检查是否超过规定厚度
                    $yThickness = ($yThickness>$this->thickness?$this->thickness:$yThickness);
                }else{
                    $yThickness --;
                    // 检查是否低于规定厚度
                    $yThickness = ($yThickness<0?0:$yThickness);
                }
                if ($yThickness>=$this->thicknessShow){

                    $yn=intval($yThickness*$this->direction);
                    @imagesetpixel($this->im,$x,$yInte+$yn,$penColor);
                }
                $yInte-=$yTan;
            }
            // 检测字符顶部后，退出划线
            if ($xMark == 0 and $y <($this->high-10)) {
                break;
            }
        }
        return $this->im;
    }
}
?>