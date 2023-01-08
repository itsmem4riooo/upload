<?php 

namespace Sources\Classes\Helpers;

/*
* Class responsible for uploading images files
* @author Gabriel Teixera
*/

class Upload { 

//FILE PATH
private $Dir;

//IMAGE FILE
private $File;

//IMAGE NAME
private $Name;

//SAVE ERRORS
private $Error;

//TYPE OF RETURN, EX: JPG,PNG,GIF...
private $TypeReturn;

//DEFINE AND CREATE UPLOAD FILE IF NOT EXISTS
function __construct($Dir = 'upload'){

$this->Dir = $Dir;
$this->checkDir();

}

//RESPONSIBLE FOR UPLOAD IMAGE FILE
function image(array $File,int $Size,array $Types,$Width = 1200,$Dir = null,$Name = null,$TypeReturn = null){
	
	$this->File  = $File;
	$this->Name  = (!empty($Name) ? $Name : $this->File['name']);
	$this->TypeReturn  = (!empty($TypeReturn) ? $TypeReturn : 'jpeg');
	$Dir   = $this->Dir.'/'.(!empty($Dir) ? $Dir : 'images');

	$this->checkDir($Dir);
	$Dir .= '/'.date('Y');
	$this->checkDir($Dir);
	$Dir .= '/'.date('m'); 
	$this->checkDir($Dir);

	//VALIDATIONS
	$this->checkType($Types);
	$this->checkSize($Size);
	$this->checkName();

	if($this->Error):
	  return false;
    endif;
	
	$this->exeImage($Width,$Dir); 

}

private function checkDir($Dir = null){

	$Dir = (!empty($Dir) ? $Dir : $this->Dir);
	
	if(!is_dir($Dir)): mkdir($Dir , 0777); endif;
	
}

//CHECK FILE SIZE 
private function checkSize($Size){
	
	$Size = $Size*1000;
	
	if($this->File['size'] > $Size):
		$this->Error = "Error: file too large to be uploaded";
	endif;	

}

//CHECK FILE TYPE
private function checkType($Types){
	if(!in_array($this->File['type'], $Types)):
		$this->Error = "Error: invalid file type";
	endif;	

}

//CHECK THE FILE NAME AND TRANSLATE 
private function checkName(){	
               
	$translate = ['á'=>'a','à'=>'a','ã'=>'a','â'=>'a','Á'=>'a','À'=>'a','Ã'=>'a','Â'=>'a',	
		       'é'=>'e','è'=>'e','ê'=>'e','É'=>'e','È'=>'e','Ê'=>'e','í'=>'i','ì'=>'i',
                       'î'=>'i','Í'=>'i','Ì'=>'i','Î'=>'i','ó'=>'o','ò'=>'o','ô'=>'o','õ'=>'o',
		       'Ó'=>'o','Ò'=>'o','Ô'=>'o','Õ'=>'o','ú'=>'u','ù'=>'u','û'=>'u','ç'=>'c',
		       'Ú'=>'u','Ù'=>'u','Û'=>'u','\\'=>'','/'=>'','!'=>'','@'=>'','#'=>'','$'=>''
                        ,'%'=>'','¨'=>'','&'=>'','*'=>'','('=>'',')'=>'','´'=>'','`'=>'','^'=>'',
						'~'=>'','<'=>'','>'=>'','='=>'','"'=>'','-'=>''];
						
    $this->Name = substr($this->Name , 0 , strripos($this->Name,'.'));
	$this->Name = strtr($this->Name, $translate);        
	$this->Name = str_replace(['    ','   ','  ',' ','____','___','__','_','....','...','..','.'], '-' , $this->Name);
    $this->Name = strtolower($this->Name);

}	

//CRIA IMAGEM 

private function exeImage($Width,$Dir){

	$x = getimagesize($this->File['tmp_name']);
	
	if($x[0] > $Width):
		//RESIZE HEIGTH IMAGE PROPORTIONALLY WITH WIDTH DEFINED SIZE
		$Heigth = round(($Width*$x[1])/$x[0]);
	else:
		
		$Width  = $x[0];
		$Heigth = $x[1];	

	endif;	
	
	$img[0] = imagecreatetruecolor($Width, $Heigth);
	
        
        if($this->File['type'] !== 'image/png' || $this->TypeReturn !== 'png'){
            $white = imagecolorallocate($img[0], 255, 255 , 255);
            imagefill($img[0],0,0 ,$white);
        }else{
            imagesavealpha($img[0], true);
            imagealphablending($img[0], false);
            $white = imagecolorallocatealpha($img[0], 255, 255, 255, 127);
            imagefill($img[0],0,0,$white);
        }
        
	switch ($this->File['type']){
			case 'image/jpg':
				$img[1] = @imagecreatefromjpeg($this->File['tmp_name']);
			break;
			case 'image/jpeg':
				$img[1] = @imagecreatefromjpeg($this->File['tmp_name']);
			break;
			case 'image/png':
				$img[1] = @imagecreatefrompng($this->File['tmp_name']);
			break;
            case 'image/webp':
				$img[1] = @imagecreatefromwebp($this->File['tmp_name']);
			break;
			case 'image/gif':
				$img[1] = @imagecreatefromgif($this->File['tmp_name']);
			break;
		}

	imagecopyresampled($img[0],$img[1], 0, 0, 0, 0, $Width, $Heigth, $x[0], $x[1]);	
        
        
	if(!file_exists($Dir.'/'.$this->Name.'.'.$this->TypeReturn)):
        $this->{'imagecreate'.$this->TypeReturn}($img[0],$Dir.'/'.$this->Name.'.'.$this->TypeReturn);
	else:
	  $this->Name = $this->Name.time();
	  if(file_exists($Dir.'/'.$this->Name.'.'.$this->TypeReturn)):
		$this->Name = $this->Name.md5($this->Name);	
	   endif;	
           $this->{'imagecreate'.$this->TypeReturn}($img[0],$Dir.'/'.$this->Name.'.'.$this->TypeReturn);
	endif;
	
	imagedestroy($img[0]); imagedestroy($img[1]);
	
	$this->File['name'] = str_replace(
    ['../','./'],'',$Dir).'/'.$this->Name.'.'.$this->TypeReturn ;

	}


    private function imagecreatewebp($img,$dir){
        imagewebp($img,$dir,100);
    }
        
    private function imagecreatejpeg($img,$dir){
        imagejpeg($img,$dir,100);
    }
        
    private function imagecreatepng($img,$dir){
        imagepng($img,$dir,9);
    }
	
    private function imagecreategif($img,$dir){
        imagegif($img,$dir,100);
    }

    function getFile(){
		return $this->File;
	}

	function getError(){
		return $this->Error;
	}
        
}