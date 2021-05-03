<?php

require_once('homework_input.php');

class Felvetelizo {

    public function __construct($adatok){
        $this->adatok = $adatok;
    }

    private static $kotelezoTargyak = ["magyar nyelv és irodalom", "történelem", "matematika"];
    private static $szakok = [
            'szak' => [
            'egyetem' => 'ELTE',
            'kar' => 'IK',
            'szak' => 'Programtervező informatikus',
            'kotelezo' => 'matematika',
            'kotelezoen-valaszthato'=> ["biológia", "fizika", "informatika", "kémia"],
            ],
            'szak' => [
                'egyetem' => 'PPKE',
                'kar' => 'BTK',
                'szak' => 'Anglisztika',
                'kotelezo' => 'angol (emelt szinten)',
                'kotelezoen-valaszthato'=> ["francia", "német", "olasz", "orosz", "spanyol", "történelem"],
            ],
    ];

    private function aTargy20alatt() {
        foreach ($this->adatok['erettsegi-eredmenyek'] as $value) {
            if(rtrim($value['eredmeny'], "%") < 20){
                return $value['nev'];
            }          
        }
    }

    private function hianyzikKotelezoTargy() {
        $tmp = [];
        foreach ($this->adatok['erettsegi-eredmenyek'] as $value) {
            array_push($tmp, $value['nev']);     
        }
        return array_diff(self::$kotelezoTargyak, $tmp);
    }

    private function kotelezoenValaszthatoTargyak() {
        $tmp = [];
        foreach ($this->adatok['erettsegi-eredmenyek'] as $value) {
            array_push($tmp, $value['nev']);     
        }
        return array_diff($tmp, self::$kotelezoTargyak);
    }

    private function legnagyobbKotelezoenValaszthato(){
        $max = 0;
        foreach ($this->adatok['erettsegi-eredmenyek'] as $value) {
            if(!in_array($value['nev'],self::$kotelezoTargyak) && rtrim($value['eredmeny'], "%") > $max){
                $max = rtrim($value['eredmeny'], "%");
            }    
        }
        return $max;
    }


    public function pontszamitas(){
        if($this -> aTargy20alatt()){
            echo "hiba, nem lehetséges a pontszámítás a ". $this -> aTargy20alatt() ." tárgyból elért 20% alatti eredmény miatt";
            return;
        }

        if($this -> hianyzikKotelezoTargy()){
            echo "hiba, nem lehetséges a pontszámítás a kötelező érettségi tárgyak hiánya miatt";
            return;
        }

        if(!$this->kotelezoenValaszthatoTargyak()){
            echo "hiba,  egy kötelezően választható tárgyat mindenképpen választani kell";
            return;
        }

        echo $this->legnagyobbKotelezoenValaszthato();


    }
}

$felvetelizo = new Felvetelizo($exampleData0);

$felvetelizo->pontszamitas();




