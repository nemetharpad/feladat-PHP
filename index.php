<?php

require_once('homework_input.php');

class Felvetelizo {

    public function __construct($adatok){
        $this->adatok = $adatok;
    }

    private function aTargy20alatt() {
        foreach ($this->adatok['erettsegi-eredmenyek'] as $value) {
            if(rtrim($value['eredmeny'], "%") < 20){
                return $value['nev'];
            }          
        }
    }

    private function hianyzikKotelezoTargy() {
        $kotelezoTargyak = ["magyar nyelv és irodalom", "történelem", "matematika"];
        $tmp = [];
        foreach ($this->adatok['erettsegi-eredmenyek'] as $value) {
            array_push($tmp, $value['nev']);     
        }
        return array_diff($kotelezoTargyak, $tmp);
    }

    private function kotelezoenValaszthatoTargyak() {
        $kotelezoTargyak = ["magyar nyelv és irodalom", "történelem", "matematika"];
        $tmp = [];
        foreach ($this->adatok['erettsegi-eredmenyek'] as $value) {
            array_push($tmp, $value['nev']);     
        }
        return array_diff($tmp, $kotelezoTargyak);
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


    }
}

$felvetelizo = new Felvetelizo($exampleData4);

$felvetelizo->pontszamitas();




