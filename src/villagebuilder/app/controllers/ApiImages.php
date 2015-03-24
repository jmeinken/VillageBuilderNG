<?php


/**
 * An account corresponds to a single set of login credentials on the site.
 * For historic reasons, the table for account info is called 'Users'.
 * 
 * The definition of Account is a little confused.  Some of these methods are
 * for USER accounts, others are specifically for PERSON accounts.
 * 
 */
class ApiImages extends BaseController {
    
    const STATUS_OK = 200;
    const STATUS_FORBIDDEN = 403;
    const STATUS_BAD_REQUEST = 400;
    const STATUS_NOT_FOUND = 404;
    const STATUS_INTERNAL_SERVER_ERROR = 500;
    
   /**
     * Accepts two image files ('thumb' and 'large').  Saves these files to 
     * disk and returns the file path to the files.
     * 
     * @return json string
     */
    public function postUserImage() {
        //return Response::json(['message' => $ct], self::STATUS_OK);
        if (Input::hasFile('thumb') && Input::hasFile('large')) {
            $ct = $this->getNextNumber(Config::get('constants.profilePicFilePath') . 'count.txt');
            $thumbFileName = 'user_thumb' . $ct . '.jpg';
            $largeFileName = 'user_large' . $ct . '.jpg';
            Input::file('thumb')->move(Config::get('constants.profilePicFilePath'), $thumbFileName);
            Input::file('large')->move(Config::get('constants.profilePicFilePath'), $largeFileName);
            return Response::json([
                    'pic_small' => [
                        'name' => $thumbFileName,
                        'path' => Config::get('constants.profilePicUrlPath') . $thumbFileName
                    ],
                    'pic_large' => [
                        'name' => $largeFileName,
                        'path' => Config::get('constants.profilePicUrlPath') . $largeFileName
                    ]
                ], self::STATUS_OK);
        } else {
            return Response::json(['errorMessage' => 'Photo not sent.  Please try again.'], self::STATUS_BAD_REQUEST);
        }
    }
    
    private function getNextNumber($fileLoc) {
        //$myfile = fopen($fileLoc, "r+");
        $count = file_get_contents($fileLoc);
        $count++;
        file_put_contents($fileLoc, $count);
        //fclose($myfile);
        //$count = (int)file_get_contents($fileLoc);
        //$count+=1;
        //file_put_contents($fileLoc,$count);
        return $count;
    }
    
}