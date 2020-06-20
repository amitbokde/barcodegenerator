
<?php
//ini_set('display_errors', 1);
$barcode_prefix = '';
$start_number	= 0;
$end_number 	= 0;
$no_error 		= true;
if($_POST['create_barcode'])
{
	if(!empty($_POST['barcode_prefix']))
	{	$barcode_prefix	= $_POST['barcode_prefix'];  }
	else
	{
		echo '<span class="error">ERROR: Please select barcode prefix.</span>';
		$no_error 		= false;
	}

	if(!empty($_POST['barcode_start_number'])&&is_numeric($_POST['barcode_start_number'])&&$_POST['barcode_start_number']>0)
	{	$start_number	= $_POST['barcode_start_number'];  }
	else
	{
		echo '<br/><span class="error">ERROR: Please enter barcode start number in numeric.</span>';
		$no_error 		= false;
	}

	if(!empty($_POST['barcode_end_number'])&&is_numeric($_POST['barcode_end_number'])&&$_POST['barcode_end_number']>0)
	{	$end_number	= $_POST['barcode_end_number'];  }
	else
	{
		echo '<br/><span class="error">ERROR: Please enter barcode end number in numeric.</span>';
		$no_error 		= false;
	}

	if(($_POST['barcode_end_number'] - $_POST['barcode_start_number'])>1000)
	{	
		echo '<br/><span class="error">ERROR: Barcode range can not exceed 1000. You can create max 1000 barcode at once. Please reset barcode start & end number</span>';
		$no_error 		= false;
	}

	if($no_error)
	{
		//$files = glob('../barcode/images/*.png'); 
		createBarCode($barcode_prefix,$start_number,$end_number);
		create_pdf($barcode_prefix,$start_number,$end_number);
		$files = glob('barcode/images/*');
		foreach($files as $file)
		{
			if(is_file($file))
    			unlink($file);
		}
	}
}

?>


<!DOCTYPE html>
<html>
<head>
	<link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/4.7.0/css/font-awesome.min.css">
	<title>Barcode Generator</title>
	<style type="text/css">
	.error
	{
		color: #DE0F06;
		margin-left: 40%;
	}
	.labelcss
	{
		font-family: "Muli", -apple-system, BlinkMacSystemFont, "Segoe UI", Roboto, "Helvetica Neue", Arial, "Noto Sans", sans-serif, "Apple Color Emoji", "Segoe UI Emoji", "Segoe UI Symbol", "Noto Color Emoji";
	}
		.form-control
		{
			
		    border-width: 2px;
		    font-family: "Muli", -apple-system, BlinkMacSystemFont, "Segoe UI", Roboto, "Helvetica Neue", Arial, "Noto Sans", sans-serif, "Apple Color Emoji", "Segoe UI Emoji", "Segoe UI Symbol", "Noto Color Emoji";
		    display: block;
		    width: 100%;
		    height: calc(1.5em + 0.75rem + 2px);
		    padding: 0.375rem 0.75rem;
		    font-size: 1rem;
		    font-weight: 400;
		    line-height: 1.5;
		    color: #495057;
		    background-color: #fff;
		    background-clip: padding-box;
		    border: 1px solid #ced4da;
		    border-radius: 0.25rem;
		    transition: border-color 0.15s ease-in-out, box-shadow 0.15s ease-in-out, -webkit-box-shadow 0.15s ease-in-out;
		}
		.buttoncss
		{
                    text-transform: uppercase;
                    font-size: 12px;
                    font-weight: 900;
                    padding-left: 3rem!important;
                    padding-right: 3rem!important;
                    padding-bottom: 1rem!important;
                    padding-top : 1rem!important;
                        overflow: visible;
                        color: #fff;
                    background-color: #352961;
                    border-color: #352961;
                    display: block;
                    width:100%;
                    cursor: pointer;

                    line-height: 1.5;
                    border-radius: 0.25rem;
		}
	</style>
</head>
<body>
	<form name="barcode" method="post">
		<table align="center" style="margin-top: 10%; width: 40%">
			<tbody>
				<tr><td><label class="labelcss">Select Barcode Prefix</label></td>
					<td><select class="form-control" name="barcode_prefix" style="width: 110%;">
						<option value="">Select Prefix</option>
								<?php
								foreach(range('A', 'Z') as $letter)
								{
									$select = ($barcode_prefix==$letter)?'selected="SELECTED"':"";

									?><option value="<?php echo $letter; ?>" <?php echo $select; ?> ><?php echo $letter; ?></option><?php
								}
								?>
						</select>
					</td>
				</tr>
				<tr><td><label class="labelcss">Enter Barcode Start No.</label></td><td><input class="form-control" type="text" name="barcode_start_number" maxlength="8" value="<?php echo $start_number; ?>"></td></tr>
				<tr><td><label class="labelcss">Enter Barcode End No.</label></td><td><input class="form-control" type="text" name="barcode_end_number" maxlength="8" value="<?php echo $end_number; ?>"></td></tr>
				<tr><td></td><td>
					<button class="buttoncss" type="submit" name="create_barcode" value="Create Barcode"><i class="fa fa-barcode" aria-hidden="true"></i> Create Barcode</button></td></tr>
			</tbody>
		</table>
	</form>
</body>
</html>
<?php
//ini_set('display_errors', 1);
function createBarCode($barcode_prefix,$start_number,$end_number)
{
    //$this->barcode(base_url('uploads/barcode_img/barcode.html'),'A000001',40);
    $destination_path 	= "barcode/images/";//A000001.png";
    $image_name 		= $barcode_prefix;
    for($i=$start_number;$i<=$end_number;$i++)
    {
        $barcode_number = $i;
        $barcode_number = str_pad($barcode_number,6,"0",STR_PAD_LEFT);
        $barcode_number = $image_name.$barcode_number;
        $image_path     = $destination_path.$barcode_number;
        barcode($image_path,$barcode_number,104,'horizontal','code128',True,1.71);
    }
}

function barcode( $filepath="", $text="0", $size="20", $orientation="horizontal", $code_type="code128", $print=false, $SizeFactor=1 )
{
    $code_string = "";
    // Translate the $text into barcode the correct $code_type
    if ( in_array(strtolower($code_type), array("code128", "code128b")) )
    {
            $chksum = 104;
            // Must not change order of array elements as the checksum depends on the array's key to validate final code
            $code_array = array(" "=>"212222","!"=>"222122","\""=>"222221","#"=>"121223","$"=>"121322","%"=>"131222","&"=>"122213","'"=>"122312","("=>"132212",")"=>"221213","*"=>"221312","+"=>"231212",","=>"112232","-"=>"122132","."=>"122231","/"=>"113222","0"=>"123122","1"=>"123221","2"=>"223211","3"=>"221132","4"=>"221231","5"=>"213212","6"=>"223112","7"=>"312131","8"=>"311222","9"=>"321122",":"=>"321221",";"=>"312212","<"=>"322112","="=>"322211",">"=>"212123","?"=>"212321","@"=>"232121","A"=>"111323","B"=>"131123","C"=>"131321","D"=>"112313","E"=>"132113","F"=>"132311","G"=>"211313","H"=>"231113","I"=>"231311","J"=>"112133","K"=>"112331","L"=>"132131","M"=>"113123","N"=>"113321","O"=>"133121","P"=>"313121","Q"=>"211331","R"=>"231131","S"=>"213113","T"=>"213311","U"=>"213131","V"=>"311123","W"=>"311321","X"=>"331121","Y"=>"312113","Z"=>"312311","["=>"332111","\\"=>"314111","]"=>"221411","^"=>"431111","_"=>"111224","\`"=>"111422","a"=>"121124","b"=>"121421","c"=>"141122","d"=>"141221","e"=>"112214","f"=>"112412","g"=>"122114","h"=>"122411","i"=>"142112","j"=>"142211","k"=>"241211","l"=>"221114","m"=>"413111","n"=>"241112","o"=>"134111","p"=>"111242","q"=>"121142","r"=>"121241","s"=>"114212","t"=>"124112","u"=>"124211","v"=>"411212","w"=>"421112","x"=>"421211","y"=>"212141","z"=>"214121","{"=>"412121","|"=>"111143","}"=>"111341","~"=>"131141","DEL"=>"114113","FNC 3"=>"114311","FNC 2"=>"411113","SHIFT"=>"411311","CODE C"=>"113141","FNC 4"=>"114131","CODE A"=>"311141","FNC 1"=>"411131","Start A"=>"211412","Start B"=>"211214","Start C"=>"211232","Stop"=>"2331112");
            $code_keys = array_keys($code_array);
            $code_values = array_flip($code_keys);
            for ( $X = 1; $X <= strlen($text); $X++ ) {
                    $activeKey = substr( $text, ($X-1), 1);
                    $code_string .= $code_array[$activeKey];
                    $chksum=($chksum + ($code_values[$activeKey] * $X));
            }
            $code_string .= $code_array[$code_keys[($chksum - (intval($chksum / 103) * 103))]];

            $code_string = "211214" . $code_string . "2331112";
    }
    elseif ( strtolower($code_type) == "code128a" )
    {
            $chksum = 103;
            $text = strtoupper($text); // Code 128A doesn't support lower case
            // Must not change order of array elements as the checksum depends on the array's key to validate final code
            $code_array = array(" "=>"212222","!"=>"222122","\""=>"222221","#"=>"121223","$"=>"121322","%"=>"131222","&"=>"122213","'"=>"122312","("=>"132212",")"=>"221213","*"=>"221312","+"=>"231212",","=>"112232","-"=>"122132","."=>"122231","/"=>"113222","0"=>"123122","1"=>"123221","2"=>"223211","3"=>"221132","4"=>"221231","5"=>"213212","6"=>"223112","7"=>"312131","8"=>"311222","9"=>"321122",":"=>"321221",";"=>"312212","<"=>"322112","="=>"322211",">"=>"212123","?"=>"212321","@"=>"232121","A"=>"111323","B"=>"131123","C"=>"131321","D"=>"112313","E"=>"132113","F"=>"132311","G"=>"211313","H"=>"231113","I"=>"231311","J"=>"112133","K"=>"112331","L"=>"132131","M"=>"113123","N"=>"113321","O"=>"133121","P"=>"313121","Q"=>"211331","R"=>"231131","S"=>"213113","T"=>"213311","U"=>"213131","V"=>"311123","W"=>"311321","X"=>"331121","Y"=>"312113","Z"=>"312311","["=>"332111","\\"=>"314111","]"=>"221411","^"=>"431111","_"=>"111224","NUL"=>"111422","SOH"=>"121124","STX"=>"121421","ETX"=>"141122","EOT"=>"141221","ENQ"=>"112214","ACK"=>"112412","BEL"=>"122114","BS"=>"122411","HT"=>"142112","LF"=>"142211","VT"=>"241211","FF"=>"221114","CR"=>"413111","SO"=>"241112","SI"=>"134111","DLE"=>"111242","DC1"=>"121142","DC2"=>"121241","DC3"=>"114212","DC4"=>"124112","NAK"=>"124211","SYN"=>"411212","ETB"=>"421112","CAN"=>"421211","EM"=>"212141","SUB"=>"214121","ESC"=>"412121","FS"=>"111143","GS"=>"111341","RS"=>"131141","US"=>"114113","FNC 3"=>"114311","FNC 2"=>"411113","SHIFT"=>"411311","CODE C"=>"113141","CODE B"=>"114131","FNC 4"=>"311141","FNC 1"=>"411131","Start A"=>"211412","Start B"=>"211214","Start C"=>"211232","Stop"=>"2331112");
            $code_keys = array_keys($code_array);
            $code_values = array_flip($code_keys);
            for ( $X = 1; $X <= strlen($text); $X++ ) {
                    $activeKey = substr( $text, ($X-1), 1);
                    $code_string .= $code_array[$activeKey];
                    $chksum=($chksum + ($code_values[$activeKey] * $X));
            }
            $code_string .= $code_array[$code_keys[($chksum - (intval($chksum / 103) * 103))]];

            $code_string = "211412" . $code_string . "2331112";
    }
    elseif ( strtolower($code_type) == "code39" )
    {
            $code_array = array("0"=>"111221211","1"=>"211211112","2"=>"112211112","3"=>"212211111","4"=>"111221112","5"=>"211221111","6"=>"112221111","7"=>"111211212","8"=>"211211211","9"=>"112211211","A"=>"211112112","B"=>"112112112","C"=>"212112111","D"=>"111122112","E"=>"211122111","F"=>"112122111","G"=>"111112212","H"=>"211112211","I"=>"112112211","J"=>"111122211","K"=>"211111122","L"=>"112111122","M"=>"212111121","N"=>"111121122","O"=>"211121121","P"=>"112121121","Q"=>"111111222","R"=>"211111221","S"=>"112111221","T"=>"111121221","U"=>"221111112","V"=>"122111112","W"=>"222111111","X"=>"121121112","Y"=>"221121111","Z"=>"122121111","-"=>"121111212","."=>"221111211"," "=>"122111211","$"=>"121212111","/"=>"121211121","+"=>"121112121","%"=>"111212121","*"=>"121121211");

            // Convert to uppercase
            $upper_text = strtoupper($text);

            for ( $X = 1; $X<=strlen($upper_text); $X++ ) {
                    $code_string .= $code_array[substr( $upper_text, ($X-1), 1)] . "1";
            }

            $code_string = "1211212111" . $code_string . "121121211";
    }
    elseif ( strtolower($code_type) == "code25" )
    {
            $code_array1 = array("1","2","3","4","5","6","7","8","9","0");
            $code_array2 = array("3-1-1-1-3","1-3-1-1-3","3-3-1-1-1","1-1-3-1-3","3-1-3-1-1","1-3-3-1-1","1-1-1-3-3","3-1-1-3-1","1-3-1-3-1","1-1-3-3-1");

            for ( $X = 1; $X <= strlen($text); $X++ ) {
                    for ( $Y = 0; $Y < count($code_array1); $Y++ ) {
                            if ( substr($text, ($X-1), 1) == $code_array1[$Y] )
                                    $temp[$X] = $code_array2[$Y];
                    }
            }

            for ( $X=1; $X<=strlen($text); $X+=2 ) {
                    if ( isset($temp[$X]) && isset($temp[($X + 1)]) ) {
                            $temp1 = explode( "-", $temp[$X] );
                            $temp2 = explode( "-", $temp[($X + 1)] );
                            for ( $Y = 0; $Y < count($temp1); $Y++ )
                                    $code_string .= $temp1[$Y] . $temp2[$Y];
                    }
            }

            $code_string = "1111" . $code_string . "311";
    }
    elseif ( strtolower($code_type) == "codabar" )
    {
            $code_array1 = array("1","2","3","4","5","6","7","8","9","0","-","$",":","/",".","+","A","B","C","D");
            $code_array2 = array("1111221","1112112","2211111","1121121","2111121","1211112","1211211","1221111","2112111","1111122","1112211","1122111","2111212","2121112","2121211","1121212","1122121","1212112","1112122","1112221");

            // Convert to uppercase
            $upper_text = strtoupper($text);

            for ( $X = 1; $X<=strlen($upper_text); $X++ ) {
                    for ( $Y = 0; $Y<count($code_array1); $Y++ ) {
                            if ( substr($upper_text, ($X-1), 1) == $code_array1[$Y] )
                                    $code_string .= $code_array2[$Y] . "1";
                    }
            }
            $code_string = "11221211" . $code_string . "1122121";
    }

    // Pad the edges of the barcode
    $code_length = 20;
    if ($print) {
            $text_height = 18.5;
    } else {
            $text_height = 0;
    }

    for ( $i=1; $i <= strlen($code_string); $i++ ){
            $code_length = $code_length + (integer)(substr($code_string,($i-1),1));
    }

    if ( strtolower($orientation) == "horizontal" ) {
            $img_width = $code_length*$SizeFactor;
            $img_height = $size;
    } else {
            $img_width = $size;
            $img_height = $code_length*$SizeFactor;
    }

    //$img_width = 225;
    //$img_height = 192;
    
    $image = imagecreate($img_width, $img_height + $text_height);
    $black = imagecolorallocate ($image, 0, 0, 0);
    $white = imagecolorallocate ($image, 255, 255, 255);

    imagefill( $image, 0, 0, $white );
    if ( $print ) {
            imagestring($image, 5, 80, $img_height, $text, $black );
    }

    $location = 10;
    for ( $position = 1 ; $position <= strlen($code_string); $position++ ) {
            $cur_size = $location + ( substr($code_string, ($position-1), 1) );
            if ( strtolower($orientation) == "horizontal" )
                    imagefilledrectangle( $image, $location*$SizeFactor, 35, $cur_size*$SizeFactor, $img_height, ($position % 2 == 0 ? $white : $black) );
            else
                    imagefilledrectangle( $image, 0, $location*$SizeFactor, $img_width, $cur_size*$SizeFactor, ($position % 2 == 0 ? $white : $black) );
            $location = $cur_size;
    }

    // Draw barcode to the screen or save in a file
    if ( $filepath=="" ) {
            header ('Content-type: image/png');
            imagepng($image);
            imagedestroy($image);
    } else {
            imagepng($image,$filepath);
            //imagepng($image,"uploads/barcode_img/barcodeimage.png"); 
            imagedestroy($image);		
    }
}

function create_pdf($barcode_prefix,$start_number,$end_number)
{
	$html='<html ><body style="margin:0; padding:0" margin:0>';
	$html.='<table cellspacing="0" cellpadding="0" border="0" ><tbody><tr valign="top">';
	$height=82.5;
	for($i=($start_number-1);$i<$end_number;$i++)
	{
	    $barcode_number = str_pad($i+1,6,"0",STR_PAD_LEFT);
	    if($i%4 == 0 && $i>($start_number-1))$html.='</tr><tr valign="top">';
	    $html.='<td height="'.$height.'px"><img src="barcode/images/'.$barcode_prefix.$barcode_number.'" align="top" ></td>';
	    $height= $height - 1.4;
	    if($i + 1 %40==0)$height=82.5;
	}
	$html.='</tr></tbody></table>';
	$html.='</body></html>';

	//echo $html;die();
	require_once('TCPDF/tcpdf.php');  
	$obj_pdf = new TCPDF('P', PDF_UNIT, PDF_PAGE_FORMAT, true, 'UTF-8', false);  
	$obj_pdf->SetMargins(0, 0, 0);
	$obj_pdf->SetFooterMargin(0); 
	$obj_pdf->SetAutoPageBreak(TRUE, 0);
	$obj_pdf->setPrintHeader(false);  
	$obj_pdf->setPrintFooter(false);
	$obj_pdf->AddPage();  
	$obj_pdf->writeHTMLCell(0, 0, '', '', $html, 0, 1, 0, true, '', true);
	ob_end_clean();
	$obj_pdf->Output('barcode-1-4000.pdf', 'I'); 
}
?>