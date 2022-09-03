<?php

namespace App\Console\Commands;

use Illuminate\Console\Command;
use App\Models\Fish;
use Illuminate\Support\Facades\Http;

class SearchFish extends Command
{
    /**
     * The name and signature of the console command.
     *
     * @var string
     */
    protected $signature = 'fish';
    protected $html = [];
    protected $fill = [];

    /**
     * The console command description.
     *
     * @var string
     */
    protected $description = 'Command description';

    /**
     * Create a new command instance.
     *
     * @return void
     */
    public function __construct()
    {
        parent::__construct();
    }

    /**
     * Execute the console command.
     *
     * @return int
     */
    public function handle()
    {
        $data = Fish::whereNull("scientific_name")->get();

        foreach ($data as $fish) {

            $this->fill = [];

            $name = explode(' - ',$fish->name)[0];

            if (strpos($name, "’") !== false) $name = str_replace("’", "", $name);
            if (strpos($name, '"') !== false) $name = str_replace('"', "", $name);

            $completeName = str_replace(" ", "-", trim(strtolower($name)));

            $response = Http::get("https://en.aqua-fish.net/fish/{$completeName}");

            $this->html = explode("\n", $response->body());

            $this->getPh();
            $this->getScientificName();
            $this->getCommonName();
            $this->getFamily();
            $this->getTemperature();
            $this->getSize();

            $fish->fill($this->fill);
            $fish->save();
            echo "{$name} updated \n";
        }
    }

    function search($input = "")
    {
        $result = array_filter($this->html, function ($item) use ($input) {
            if (stripos($item, $input) !== false) return true;
            return false;
        });

        return $result;
    }

    function getBetweenString($string, $start, $end){
        $string = ' ' . $string;
        $ini = strpos($string, $start);
        if ($ini == 0) return '';
        $ini += strlen($start);
        $len = strpos($string, $end, $ini) - $ini;
        return substr($string, $ini, $len);
    }

    function getPh()
    {
        $result = $this->search("Recommended pH range for the species");

        if (!$result) return false;

        $parsed = $this->getBetweenString(reset($result), 'Recommended pH range for the species:', '</p>');
        $array = explode(' - ', trim($parsed));

        if (count($array) < 2) return false; 

        $this->fill['ph_min'] = $array[0];
        $this->fill['ph_max'] = $array[1];

        return true;
    }

    function getScientificName()
    {
        $result = $this->search("Scientific name");

        $parsed = $this->getBetweenString(reset($result), 'Scientific name: <em>', '</em>');
        $string = explode(' - ', trim($parsed));

        if (!$string) return false;

        $this->fill['scientific_name'] = $string[0];

        return true;
    }

    function getCommonName()
    {
        $result = $this->search("Common name");

        $parsed = $this->getBetweenString(reset($result), 'Common name: <strong>', '</strong>');
        $string = explode(' - ', trim($parsed));

        if (!$string) return false;

        $this->fill['common_name'] = $string[0];

        return true;
    }

    function getFamily()
    {
        $result = $this->search("Family: ");

        $parsed = $this->getBetweenString(reset($result), 'Family: <em>', '</em>');
        $string = explode(' - ', trim($parsed));

        if (!$string) return false;

        $this->fill['family'] = $string[0];

        return true;
    }

    function getTemperature()
    {
        $result = $this->search("Recommended temperature:");

        if (!$result) return false;

        $parsed = $this->getBetweenString(reset($result), 'Recommended temperature:', '°C');
        $array = explode(' - ', trim($parsed));

        if (count($array) < 2) return false; 

        $this->fill['temperature_min'] = $array[0];
        $this->fill['temperature_max'] = $array[1];

        return true;
    }

    function getSize()
    {
        $result = $this->search("Usual size in fish tanks:");

        if (!$result) return false;

        $parsed = $this->getBetweenString(reset($result), 'Usual size in fish tanks:', 'cm');
        $array = explode(' - ', trim($parsed));

        if (count($array) < 2) return false; 

        $this->fill['size_min'] = $array[0];
        $this->fill['size_max'] = $array[1];

        return true;
    }
}
