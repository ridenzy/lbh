<?php

namespace loadBlogHelpers\Feeds;



final class IndexNow
{
    /**
     * Submit 1..N URLs via IndexNow.
     * @param array  $urls        absolute URLs to notify
     * @param string $host        e.g. "kingsmaking101.com"
     * @param string $key         your IndexNow API key (string)
     * @param string $keyLocation absolute URL to key file, e.g. "https://kingsmaking101.com/INDEXNOW_KEY.txt"
     * @return array{name:string,status:int|null,success:bool,body:?string}
     */
    private static function submit(array $urls, string $host, string $key, string $keyLocation): array
    {
        $payload = json_encode([
            'host'       => $host,
            'key'        => $key,
            'keyLocation'=> $keyLocation,
            'urlList'    => array_values($urls),
        ], JSON_UNESCAPED_SLASHES);

        // Use the fan-out endpoint so all participating engines get it
        $endpoint = 'https://api.indexnow.org/indexnow';

        // Prefer cURL; fallback to file_get_contents
        if (function_exists('curl_init')) {
            $ch = curl_init($endpoint);
            curl_setopt_array($ch, [
                CURLOPT_POST           => true,
                CURLOPT_HTTPHEADER     => ['Content-Type: application/json'],
                CURLOPT_POSTFIELDS     => $payload,
                CURLOPT_RETURNTRANSFER => true,
                CURLOPT_TIMEOUT        => 8,
            ]);
            $body   = curl_exec($ch);
            $status = curl_getinfo($ch, CURLINFO_RESPONSE_CODE) ?: null;
            curl_close($ch);
        } else {
            $context = stream_context_create([
                'http' => [
                    'method'  => 'POST',
                    'header'  => "Content-Type: application/json\r\n",
                    'content' => $payload,
                    'timeout' => 8,
                    'ignore_errors' => true,
                ]
            ]);
            $body   = @file_get_contents($endpoint, false, $context);
            $status = null;
            if (isset($http_response_header[0])
                && preg_match('{HTTP/\S*\s(\d{3})}', $http_response_header[0], $m)) {
                $status = (int)$m[1];
            }
        }

        return [
            'name'    => 'indexnow',
            'status'  => $status,
            'success' => $status && $status >= 200 && $status < 300,
            'body'    => $body ? substr(strip_tags($body), 0, 200) : null,
        ];
    }


    private static function loadJsonFileToArray(string $filePath): array 
    { //  Load urls
        if (!file_exists($filePath)) {
            echo "File not found: $filePath \n";
            return [];
        }

        /*
        initialize the JSON file with at least an empty array,
        to avoid PHP Fatal error:  Uncaught Exception: Failed to decode JSON: Syntax error
        */
        if(file_get_contents($filePath) === ""){
            $data = [];
        }else{
            $json = file_get_contents($filePath);
            $data = json_decode($json, true);
        }
        

        if ($data === null && json_last_error() !== JSON_ERROR_NONE) {
            echo 'Failed to decode JSON: ' . json_last_error_msg() . " \n";
            return [];
        }

        $sendBack = empty($data) ? [] : ($data);

        return $sendBack;
    }

    private static function saveArrayToJsonFile(array $data, string $filePath): bool 
    { // Save instructions
        // Convert array → JSON string with pretty formatting
        $json = json_encode($data, JSON_PRETTY_PRINT | JSON_UNESCAPED_UNICODE);

        // Handle encoding errors
        if ($json === false) {
            echo 'Failed to encode data to JSON: ' . json_last_error_msg() . " \n";
            return false;
        }

        // Try to write the file
        $result = file_put_contents($filePath, $json);

        // Return true if successfully written
        return $result !== false;
    }


    public static function indexMyWebsite(): array
    {


        $host = trim((string)readline("Step 1, Enter your website name [format--> example.com]: "));
        $key   = trim((string)readline("Step 2, Enter your indexnow api key downloaded from 'https://www.bing.com/indexnow/getstarted' [format--> xxxxxxx...32 alphanumeric string combinations]: "));
        echo "\n ---- Notice: save api key .txt file from 'step 2' to your public folder, so external calls to https://.example.com/xxxxxxx...(your 32 alphanumeric string combination).txt is possible-----\n";
        $keyUrl= "https://" . $host . "/" . $key . ".txt"; // key file location
        echo "\n ---- Notice: attempting to create a urls.json file -----\n";
        $filePath = "urls.json";
        $details = [
            "instruction_1"=>"Create a list of your website urls e.g: https://example.com/, https://example.com/abc etc..",
            "instruction_2"=>"Copy your list of urls and use to replace the empty 'urls' list below",
            "instruction_3"=>"Save file and go back to terminal",
            "urls"=>[]
        ];
        // Try to write the file
        self::saveArrayToJsonFile($details, $filePath);
        echo "\n ---- Notice: urls.json file created (open the file for instructions) -----\n";

        // Try to load the file
        $urls = loadJsonFileToArray($filePath)["urls"];

        // write a function, or import to confirm url string ( i.e encoding for potential loose urls, with spaces or special characters)


        $res = self::submit($urls, $host, $key, $keyUrl);

        return $res;


    }
}


// https://www.indexnow.com/



if (PHP_SAPI === 'cli' && basename(__FILE__) === basename($_SERVER['SCRIPT_FILENAME'])) {
    #$host  = 'kingsmaking101.com';
    #$key   = getenv('INDEXNOW_KEY'); // store in env | gotten from : https://www.bing.com/indexnow/getstarted
    #$keyUrl= "https://{$host}/INDEXNOW_KEY.txt"; // key file location
    #$urls  = ['https://kingsmaking101.com/blog/personalized-fragrance-generator'];

    $res = IndexNow::indexMyWebsite();
    print_r($res);
}