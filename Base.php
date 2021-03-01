<?PHP
class Base{

private static $Instance;

	private function __construct(){
	}
	
	public static function Instance(){
		 if (is_null(self::$Instance)){
			self::$Instance = new self();
		}
		return self::$Instance;
	}
	
	
	/* hash two images and return an index of their similarty as a percentage. */
	public function Compare($res1, $res2, $rot=0, $precision = 1){
		
		$hash1 = $this->HashImage($res1); // this one should never be rotated
		$hash2 = $this->HashImage($res2, $rot);
		
		$similarity = count($hash1);
		
		// take the hamming distance between the hashes.
		foreach($hash1 as $key=>$val){
			if($hash1[$key] != $hash2[$key]){
				$similarity--;
			}
		}
		$percentage = round(($similarity/count($hash1)*100), $precision);
		return $percentage;
	}
	
	public function ArrayAverage($arr){
		return floor(array_sum($arr) / count($arr));
	}
	
		public function HashImage($res, $rot=0, $mir=0, $size = 8, $WhichHash = 'aHash'){
		
		$res = $this->NormalizeAsResource($res); // make sure this is a resource
		$rescached = imagecreatetruecolor($size, $size);
		
		imagecopyresampled($rescached, $res, 0, 0, 0, 0, $size, $size, imagesx($res), imagesy($res));
		imagecopymergegray($rescached, $res, 0, 0, 0, 0, $size, $size, 50);
		
		$w = imagesx($rescached);
		$h = imagesy($rescached);
		
		$pixels = array();

		for($y = 0; $y < $size; $y++) {
		
			for($x = 0; $x < $size; $x++) { 
				
						
				switch($rot){
					case 90:	$rx=(($h-1)-$y);	$ry=$x;			break;
					case 180:	$rx=($w-$x)-1;		$ry=($h-1)-$y;	break;
					case 270:	$rx=$y;				$ry=($h-$x)-1;	break;
					default:	$rx=$x;				$ry=$y;
				}
				
				switch($mir){
					case 1: $rx = (($w-$rx)-1); break;
					case 2: $ry = ($h-$ry); 	break;
					case 3: $rx = (($w-$rx)-1);
							$ry = ($h-$ry); 	break;
					default: 					break;
				}
				
				$rgb = imagecolorsforindex($rescached, imagecolorat($rescached, $rx, $ry));
				
    			$r = $rgb['red'];
				$g = $rgb['green'];
				$b = $rgb['blue'];
				
				$gs = (($r*0.299)+($g*0.587)+($b*0.114));
				$gs = floor($gs);
				
				$pixels[] = $gs; 
				//$index++;
			}
		}		
		
		// find the average value in the array
		$avg = $this->ArrayAverage($pixels);
		
		// create a hash (1 for pixels above the mean, 0 for average or below)
		$index = 0;
			if($WhichHash == 'dHash') 
		{
			foreach($pixels as $ind => $px)
			{
				// Legendante - Uses the original 8*8 comparison originally suggested to Dr. Krawetz
				// not the modified 9*8 as suggested by Dr. Krawetz
				if(!isset($pixels[($ind + 1)]))
					$ind = -1;
				if($px > $pixels[($ind + 1)])
					$hash[] = 1;
				else
					$hash[] = 0;
			}
		}
		
		else
		{
			foreach($pixels as $px){
				if($px > $avg){
					$hash[$index] = 1;
				}
				else{
					$hash[$index] = 0;
				}
				$index += 1;
			}
		}
		// return the array
		return $hash;
	}
	
	/* if $resource is a filename pointing to an image, make it an image resource. Otherwise
		return the resource. */
		
	private function NormalizeAsResource($resource){
		if(gettype($resource) == 'resource'){
			return $resource;
		}
		else{
			if(file_exists(realpath($resource)) &&  getimagesize($resource)){
				return imagecreatefromstring(file_get_contents($resource));
			}
		}
	}

}
