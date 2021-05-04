<?php

require_once('homework_input.php');

class Felvetelizo {

    public function __construct($adatok){
        $this->adatok = $adatok;
    }

    private static $kotelezoTargyak = ["magyar nyelv és irodalom", "történelem", "matematika"];
    private static $szakok = [
            [
            'egyetem' => 'ELTE',
            'kar' => 'IK',
            'szak' => 'Programtervező informatikus',
            'kotelezo' => 'matematika',
            'kotelezoen-valaszthato'=> ["biológia", "fizika", "informatika", "kémia"],
            ],
            [
                'egyetem' => 'PPKE',
                'kar' => 'BTK',
                'szak' => 'Anglisztika',
                'kotelezo' => 'angol (emelt szinten)',
                'kotelezoen-valaszthato'=> ["francia", "német", "olasz", "orosz", "spanyol", "történelem"],
            ]
    ];
   // private static $nyelvVizsgak = ['angol', 'német'];

    private function aTargy20alatt() {
        foreach ($this->adatok['erettsegi-eredmenyek'] as $value) {
            if(rtrim($value['eredmeny'], "%") < 20){
                return $value['nev'];
            }          
        }
    }

    private function hianyzikErettsegiKotelezoTargy() {
        $tmp = [];
        foreach ($this->adatok['erettsegi-eredmenyek'] as $value) {
            array_push($tmp, $value['nev']);     
        }
        return array_diff(self::$kotelezoTargyak, $tmp);
    }

    // //megcsinalni szakonkent
    // private function kotelezoenValaszthatoTargyak() {
    //     $tmp = [];
    //     foreach ($this->adatok['erettsegi-eredmenyek'] as $value) {
    //         array_push($tmp, $value['nev']);     
    //     }
    //     return array_diff($tmp, self::$kotelezoTargyak);
    // }

    // //+szak
    // private function legnagyobbKotelezoenValaszthato(){
    //     $max = 0;
    //     foreach ($this->adatok['erettsegi-eredmenyek'] as $value) {
    //         if(!in_array($value['nev'],self::$kotelezoTargyak) && rtrim($value['eredmeny'], "%") > $max){
    //             $max = rtrim($value['eredmeny'], "%");
    //         }    
    //     }
    //     return $max;
    // }

    private function szakKotelezoTargyPontok(){
            foreach(self::$szakok as $szak){ 
                 $a = array_diff_assoc($this->adatok['valasztott-szak'], $szak);
                 if(!$a){
                     foreach($this->adatok['erettsegi-eredmenyek'] as $eredmeny){
                         if($eredmeny['nev'] == $szak['kotelezo']){
                             return rtrim($eredmeny['eredmeny'], '%');
                         }
                     }
                 }
            }
        
    }

    private function szakLegnagyobbKotelezoenValaszthatoTargyPontok(){
        foreach(self::$szakok as $szak){ 
             $a = array_diff_assoc($this->adatok['valasztott-szak'], $szak);
             if(!$a){
                $max = 0;
                 foreach($this->adatok['erettsegi-eredmenyek'] as $eredmeny){
                     foreach($szak['kotelezoen-valaszthato'] as $targy){
                        if($eredmeny['nev'] == $targy){
                            $max= rtrim($eredmeny['eredmeny'], '%');
                        }
                    }
                 }
                 return $max;
             }
        }
    }

    private function nyelvVizsgaTobbletPontok(){
        $nyelvVizsgak = ['angol', 'német'];
        $arr = ['angol'=>"", 'német'=>""];
        $osszPontok=0;

        foreach($this->adatok['tobbletpontok'] as $x){
            foreach($nyelvVizsgak as $nyelv){
                if($x['nyelv'] == $nyelv){
                    if($arr[$nyelv] != "C1"){
                        $arr[$nyelv] = $x['tipus'];
                    }
                }
            }
        }

        foreach($arr as $x){
            if($x == "C1"){
                $osszPontok = $osszPontok + 40;
            }elseif($x == "B2"){
                $osszPontok = $osszPontok + 28;
            }
        }

        return $osszPontok;
    }

    private function emeltSzintuVizsgaPontok(){
        $pontok=0;
        foreach($this->adatok['erettsegi-eredmenyek'] as $x){
            if($x['tipus']=='emelt'){
                $pontok = $pontok + 50;
            }
        }
        return $pontok;
    }

    private function alapPontok(){
        return ($this->szakKotelezoTargyPontok() + $this->szakLegnagyobbKotelezoenValaszthatoTargyPontok())*2;
    }

    private function tobbletPontok(){
        return min($this -> nyelvVizsgaTobbletPontok() + $this->emeltSzintuVizsgaPontok(), 100);
    }

    public function pontszamitas(){
        if($this -> aTargy20alatt()){
            echo "hiba, nem lehetséges a pontszámítás a ". $this -> aTargy20alatt() ." tárgyból elért 20% alatti eredmény miatt";
            return;
        }

        if($this -> hianyzikErettsegiKotelezoTargy()){
            echo "hiba, nem lehetséges a pontszámítás a kötelező érettségi tárgyak hiánya miatt";
            return;
        }

        if(!$this->szakKotelezoTargyPontok()){
            echo "hiba, szakhoz kapcsolódó kötelező tárgyat mindenképpen választani kell";
            return;
        }

        if(!$this->szakLegnagyobbKotelezoenValaszthatoTargyPontok()){
            echo "hiba, 1 kötelezően választható tárgyat mindenképpen választani kell";
            return;
        }
        
        //echo "alappontok: " . ($this->szakKotelezoTargyPontok() + $this->szakLegnagyobbKotelezoenValaszthatoTargyPontok())*2;

        //echo $this->nyelvVizsgaTobbletPontok();
        //echo $this->emeltSzintuVizsgaPontok();
        //echo $this -> nyelvVizsgaTobbletPontok();

        echo $this->alapPontok() + $this->tobbletPontok() . " (" . $this->alapPontok() . " alappont + " . $this->tobbletPontok() . " tobbletpont)";
    }
}

$felvetelizo = new Felvetelizo($exampleData6);

$felvetelizo->pontszamitas();




