<?
namespace Diagram;

class dia
{
    /*
     * @param array $result
     * associative array WebColor=>SomeNumber
     * @return picture
    */
    public function generate($result = array('808080'=>33, '800080'=>33, '000000'=>33))
    {
        $sum=0;
       
        /*Calculate sum of all elements*/
        foreach($result AS $row)
        {
            $sum += $row;
        }
        
        $image = imagecreatetruecolor(500, 500);
        imagefilledrectangle($image, 0, 0, 500, 500, imagecolorallocate($image, 255, 255, 255));
        
        $old_grad=0;
        foreach($result AS $key => $row)
        {
            /*split array*/
            list($r, $g, $b) = $this->_html2rgb($key);
            
            /*Calculate sectors in degrees*/
            $grad = $old_grad + round(($row*360)/$sum);
            
            /*LibGD rounds degrees.*/
            if(round($old_grad) == round($grad))
                continue;
            $color = imagecolorallocate($image, $r, $g, $b);
            imagefilledarc($image, 250, 250, 500, 500, $old_grad, $grad, $color, IMG_ARC_PIE);
            $old_grad = $grad;
        }
        
        header('Content-type: image/gif');
        imagegif($image);
        imagedestroy($image);
    }
    
    /*
     * @param $color
     *   WebColor
     * 
     * @return array
     *   Color in RGB notation
     */
    private function _html2rgb($color)
    {
        if ($color[0] == '#')
            $color = substr($color, 1);
        if (strlen($color) == 6)
            list($r, $g, $b) = str_split($color, 2);
        else
            return false;
        
        $r = hexdec($r); $g = hexdec($g); $b = hexdec($b);
        return array($r, $g, $b);
    }
}
?>