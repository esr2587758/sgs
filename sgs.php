<?php
define('DS',DIRECTORY_SEPARATOR);
/**
 * 用GD库完成一个生成三国杀的函数
 * @todo 1. 血量勾玉，(2010409 done)
 *       2. 神卡牌
 *       3. 上传图片的大小
 *       4. 不同文字坐标的位置 
 *       5. 技能数量修正
 * */
class sgs{
    public $path = "./";
    public $upload_dir = "./upload/";
    public $art_dir = "./art/";
    public $art_path;
    public $width = 366; 
    public $height = 514; 
    
    function __construct($fileSavePath = null){
        $this->getAbsolutePath();
        if($fileSavePath){
            $this->destination = $fileSavePath;
            $this->im = $this->getImageFromPath($this->destination);
        }else{
            if($this->saveFile()){
                $this->im = $this->getImageFromPath($this->destination);
            }else{
                die("上传失败");
            }
        } 
        //判断图片类型
        $this->resizeImage();
      //  header('content-type:image/png');
      // imagejpeg($this->im);
        
    }

    /**
     * 生成最后的图片
     * */
    function render(){
        $this->setPos(); 
        $this->addFrame();
        $this->addBlood();
        $this->addName();
        $this->addChenghao();
        $this->addSkill();
       // header('content-type:image/png');
        $this->saveArt();
    }
    
    function addFrame(){
        $frame_path = 'template/'.$this->params['race'].'.png';
        $frame = imagecreatefrompng($frame_path);
        $this->image($frame,0,0,0,0,366,514,100);
    }

    function addblood(){
        if($this->params['blood'] > 3){
            $rounds = $this->params['blood'] - 3;
            $blood_path = 'template/'.$this->params['race'].'blood.png';
            $blood = imagecreatefrompng($blood_path);
            $x = $this->blood_x;
            for($i=0; $i< $rounds;$i++){
                $x_offset = 20;
                if($i>0){
                $x = $x + $x_offset;
                }
            $this->image($blood,$x,18,0,0,25,25,100);
            }
        }
    }

    function addName(){
        $font = $this->absolute_path.'Fzxkfw.ttf';
        $font_size = 33;
        $this->params['name'] = $this->processString($this->params['name'],1);
        //添加姓名的阴影
        $this->text($font_size,0,35, $this->name_y ,'black',$font,$this->params['name']);
        //添加姓名
        $font_size = 31;
        $this->text($font_size,0,37, $this->name_y +2 ,'white',$font,$this->params['name']);
    }

    function addChenghao(){
        $font = $this->absolute_path.'FZXZTFW.TTF';
        $font_size = 18;
        $this->params['chenghao'] = $this->processString($this->params['chenghao'],1);
        $color = $this->chenghaoColor(); 
        $this->text($font_size,0,44, $this->chenghao_y ,$color,$font,$this->params['chenghao']);
    }

    /**
     * 添加技能
     * 这里奇怪的是必须把技能名字的图层单独先覆盖。这样技能名字才会覆盖到图片上。
     * */
    function addSkill(){
        $skill_path = 'template/'.$this->params['race'].'skill.png';
        $skill_image = imagecreatefrompng($skill_path);
        //添加技能名字的背景
        foreach($this->params['skill'] as $index => $skill){
              if(!empty($skill['desc'])){
                  $this->image($skill_image,28,$skill['y'],0,0,76,35,100);
              }
        }
        //添加技能名字与描述
        foreach($this->params['skill'] as $index => $skill){
            if(!empty($skill['desc'])){
                  $this->addSingleSkill($skill,$index);
            }
        }
    }

    function addSingleSkill($skill,$index){
        //添加技能名
        $font = $this->absolute_path.'FZLSFW.TTF';
        $font_size = 15;
        $this->text($font_size,0,40,$skill['y'] + 20,'black',$font,$skill['title']);

        //添加技能描述
        $font = $this->absolute_path.'youyuan.TTF';
        $font_size = 8;
        $skill['desc'] = $this->processString($skill['desc'],20);
        $this->text($font_size,0,100,$skill['y'] + 13,'black',$font,$skill['desc']);
    }

    /**
     * 写入文字到模板
     * */
    function text($font_size,$angle,$x,$y,$color,$font,$text){
        imagettftext($this->im,$font_size,$angle,$x,$y,$this->getColor($color),$font,$text);
    }

    /**
     * 覆盖一个图层
     * */
    function image($src_im,$dst_x,$dst_y,$src_x,$src_y,$src_width,$src_height,$opacity){
//        imagecopymerge($this->im,$src_im,$dst_x,$dst_y,$src_x,$src_y,$src_width,$src_height,$opacity); 
        $this->imagecopymerge_alpha($this->im,$src_im,$dst_x,$dst_y,$src_x,$src_y,$src_width,$src_height,$opacity); 
    }


    /**
     * 获取颜色的handle
     * */
    function getColor($color){
        switch ($color){
            case 'black':
                $color_handle = imagecolorallocate($this->im,0,0,0);
            break;
            case 'white':
                $color_handle = imagecolorallocate($this->im,255,255,255);
            break;
            case 'gold':
                $color_handle = imagecolorallocate($this->im,221,192,75);
            break;
            case 'blue':
                $color_handle = imagecolorallocate($this->im,86,112,221);
            break;
            case 'gold':
                $color_handle = imagecolorallocate($this->im,250,201,106);
            break;

            default:
            break;
        } 
        return $color_handle;
    }    

    /**
     * 获取图片
     * */ 
    function getImageFromPath($path){
        $im = imagecreatefromjpeg($path);
        return $im;
    }

    function getAbsolutePath(){
        $this->absolute_path = dirname($_SERVER['SCRIPT_FILENAME']).DS;
    }

    /**
     * 格式化字符串，使之换行来正确显示
     *
     * */
    function processString($string,$offset){
        $start = 0;
        $new_str = '';
        $count = ceil(mb_strlen($string,'UTF-8')/$offset);
        for($i = 0;$i < $count;$i++){
            $temp = mb_substr($string,$start,$offset,'UTF-8');
            $new_str .= $temp."\r\n";
            $start += $offset;
        }
        return $new_str; 
    }

    function chenghaoColor(){
        switch ($this->params['race']){
            case 'shu':
                $color = 'gold';
            break;
            case 'wei':
                $color = 'blue';
            break;
            case 'wu':
                $color = 'gold';
            break;
            case 'qx':
                $color = 'gold';
            break;
            default:
                $color = 'gold';
            break;
        }
        return $color;
        
    }

    /**
     * 保存上传图片
     * @return boolean 
     * */
    function saveFile(){
        if(!empty($_FILES)){
            if($_FILES['pic']['error'] === 0){
                $this->setDestination();
                if(move_uploaded_file($_FILES['pic']['tmp_name'],$this->destination)){
                    return true;  
                }
            }
        }
        return false;
    }

    /**
     * 设置上传图片的途径
     * */
    function setDestination(){
        $this->save_dir = $this->upload_dir.date('Ymd');
        if(!file_exists($this->save_dir)){
            mkdir($this->save_dir); 
        }
        $this->filename = md5($_FILES['pic']['name']);
        $this->destination = $this->save_dir.'/'.$this->filename;
    }

    /**
     * 保存最后的图片
     * */
    function saveArt(){
        $this->art_dir = $this->art_dir.date('Ymd');
        if(!file_exists($this->art_dir)){
            mkdir($this->art_dir); 
        }
        $this->art_path = $this->art_dir.DS.md5($_FILES['pic']['name']);
        imagepng($this->im,$this->art_path);
        $this->art_path = "http://".$_SERVER['HTTP_HOST']. dirname($_SERVER['SCRIPT_NAME']).'/art/'.date('Ymd').'/'.md5($_FILES['pic']['name']);
    }

    /**
     * 调整上传图片的大小。目前比较粗糙。
     * */
    function resizeImage(){
        list($width,$height) = getimagesize($this->destination);
        $old_im = $this->im;
        $this->im = imagecreatetruecolor($this->width, $this->height);
        imagecopyresampled ( $this->im, $old_im,0,0,0,0,$this->width,$this->height,$width,$height);
    }

    /**
     * 设置计算坐标的位置
     * */
    function setPos(){
        $this->blood_x = 154;
        $name_len = mb_strlen($this->params['name'],'UTF-8');
        $chenghao_len = mb_strlen($this->params['chenghao'],'UTF-8');
        if($name_len == 3){
            $this->name_y = 245;
        }elseif($name_len == 2){
            $this->name_y = 270;
        }else{
            $this->name_y = 245;
        }
        if($chenghao_len == 3){
            $this->chenghao_y = 140;
        }elseif($chenghao_len == 4){
            $this->chenghao_y = 120;
        }else{
            $this->chenghao_y = 140;
        }

        //设置技能的位置参数
        $skill_1_rows =  $this->getSkillDescRows(1);
        $skill_2_rows =  $this->getSkillDescRows(2);
        $skill_3_rows =  $this->getSkillDescRows(3);

        $this->params['skill'][0]['y'] = 390;
        $this->params['skill'][1]['y'] = $this->params['skill'][0]['y'] + ($skill_1_rows ) * 14;
        $this->params['skill'][2]['y'] = $this->params['skill'][0]['y']  + ($skill_1_rows + $skill_2_rows ) * 14;

        //根据行数删除多出的技能
        if(($skill_1_rows +$skill_2_rows +$skill_3_rows)>6 ){
            unset( $this->params['skill'][2]);
        }
        if(($skill_1_rows +$skill_2_rows)>6 ){
            unset( $this->params['skill'][1]);
        }

    }

    /**
     * 获取技能描述的行数
     * */
    function getSkillDescRows($num,$offset = 20){
        if(isset( $this->params['skill'][$num-1]['desc'])
            && !empty( $this->params['skill'][$num-1]['desc'])
        ){
            $rows = ceil(mb_strlen($this->params['skill'][$num-1]['desc'],'UTF-8')/$offset);
            return ($rows<2)?2:$rows;
        }
    }

    /**
     * 兼容PNG-24的图层合并方法
     * */
    function imagecopymerge_alpha($dst_im, $src_im, $dst_x, $dst_y, $src_x, $src_y, $src_w, $src_h, $pct){ 
        // creating a cut resource 
        $cut = imagecreatetruecolor($src_w, $src_h); 
        // copying relevant section from background to the cut resource 
        imagecopy($cut, $dst_im, 0, 0, $dst_x, $dst_y, $src_w, $src_h); 
        // copying relevant section from watermark to the cut resource 
        imagecopy($cut, $src_im, 0, 0, $src_x, $src_y, $src_w, $src_h); 
        // insert cut resource to destination image 
        imagecopymerge($dst_im, $cut, $dst_x, $dst_y, 0, 0, $src_w, $src_h, $pct); 
    } 
    
}

?>
