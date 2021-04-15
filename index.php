<?php
error_reporting(E_ALL);
ini_set('display_errors', 1);
$invalidCity = null;
$responseData = null;
if(!empty($_POST)){

    if(!empty($_POST['city'])){
        $to = '+30 6978745957';
        $message = "Your name and Temperature <more_or_less> than 20C. <the actual temperature>";
        $invalidCity = '';
        $responseData = '';
        require_once('WheatherApi.php');
        require_once('SMSApi.php');
        try{
            $wheatherTemp = new WheatherApi();
            $temprature = $wheatherTemp->getCurrentWheather($_POST['city']);
            if($temprature != 'INVALID_DATA'){
                $obj = SMSApi::getInstance();
                if($temprature > 20){
                    $message = strtr($message, [
                        '<more_or_less>' => 'more',
                        '<the actual temperature>' => $temprature.'C'
                    ]);
                }else{
                    $message = strtr($message, [
                        '<more_or_less>' => 'less',
                        '<the actual temperature>' => $temprature.'C'
                    ]);
                }
            }else{
                throw new \Exception('Error in wheather response');
            }
            $responseData = $obj::send($to, $message);
        }catch(\Exception $e){
            $responseData = $e->getMessage();
        }
    }else{
        $invalidCity = 'Please enter city';
    }
}
?>
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta http-equiv="X-UA-Compatible" content="IE=edge">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Send SMS based on the temprature collected for a city</title>
</head>
<body>
    <center>
    <form action="" method="post">
        <label>City:</label>
        <input type="text" name="city" />
        <?php echo $invalidCity ?>
        <?php echo $responseData ?>
        <br /><br />
        <input type="submit" name="submit" />
    </form>
    </center>
</body>
</html>