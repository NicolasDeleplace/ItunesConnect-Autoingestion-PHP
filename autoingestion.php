<?php

class ItunesAutoIngestion
{
  private $username;
  private $password;
  private $vndnumber;
  public $filename;
  public $date;
  public $data = [];

  function __construct($username, $password, $vndnumber)
  {
    $this->username = $username;
    $this->password = $password;
    $this->vndnumber = $vndnumber;
  }

  /**
 * Function to extract the .gz file
 */
  function extractGz()
  {
    if (filesize("$this->filename.gz"))
    {
        file_put_contents($this->filename, gzdecode(file_get_contents($this->filename.".gz")));
    }
  }

  /**
 * Reading the information contained in the file
 */
  function readFile()
  {
    $this->extractGz($this->filename);

    if(($file = fopen($this->filename, 'r')) !== false)
    {
        $columns = fgetcsv($file, 0, "\t");

        while (($data = fgetcsv($file, 0, "\t")) !== false)
        {
            //Stockage des données récupérer dans un tableau
            $this->data[$this->date][] = array_combine($columns, $data);
        }

        fclose($file);
    }
    else
    {
        echo "Ouverture du fichier $this->filename impossible ".PHP_EOL;
    }
}

/**
* Get data
*
* @param string $reportType Sales or Newsstand
* @param string $reportSubType Summary, Detailed, or Opt-In
* @param string $dateType Daily, Weekly, Monthly, Yearly
* @param date $date YYYYMMDD (Daily or Weekly) YYYYMM (Monthly) YYYY (Yearly)
*
* @return $data[] report data
*/

  function getData($reportType, $reportSubType, $dateType, $date)
  {
    $url = "https://reportingitc.apple.com/autoingestion.tft";
    $this->date = $date;

    //$ini = parse_ini_file('autoingestion.ini');

    $parameters =  http_build_query([
        'USERNAME'     => $this->username,
        'PASSWORD'     => $this->password,
        'VNDNUMBER'    => $this->vndnumber,
        'TYPEOFREPORT' => $reportType,
        'DATETYPE'     => $dateType,
        'REPORTTYPE'   => $reportSubType,
        'REPORTDATE'   => $this->date,
    ]);

    $ch = curl_init();

    $this->filename=$this->date."-".$this->vndnumber;

    $fp = fopen($this->filename.".gz", 'w');

    curl_setopt($ch, CURLOPT_URL, $url);
    curl_setopt($ch, CURLOPT_POST, 7);
    curl_setopt($ch, CURLOPT_POSTFIELDS, $parameters);
    curl_setopt($ch, CURLOPT_RETURNTRANSFER, true);
    curl_setopt($ch, CURLOPT_SSL_VERIFYPEER, false);
    curl_setopt($ch, CURLOPT_FILE, $fp);

    $server_output = curl_exec($ch);

    if(!$server_output)
    {
      echo("Connection error");
    }
    else
    {
      $this->readFile($this->filename, $date);
      fclose($fp);
    }

    curl_close ($ch);

    $this->rmTmp();

    return $this->data;
  }

  /**
  * Deleting temporary file
  */
  function rmTmp()
  {
    if (file_exists($this->filename.".gz"))
    {
      unlink($this->filename.".gz");
    }
    if (file_exists($this->filename))
    {
      unlink($this->filename);
    }
  }
}

