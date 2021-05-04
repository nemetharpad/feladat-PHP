<?php

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
            'kotelezo_minSzint' => 'közép',
            'kotelezoen-valaszthato'=> ["biológia", "fizika", "informatika", "kémia"],
            ],
            [
                'egyetem' => 'PPKE',
                'kar' => 'BTK',
                'szak' => 'Anglisztika',
                'kotelezo' => 'angol',
                'kotelezo_minSzint' => 'emelt',
                'kotelezoen-valaszthato'=> ["francia", "német", "olasz", "orosz", "spanyol", "történelem"],
            ]
    ];

    private function aTargyak20alatt() {
        $tmp = [];
        foreach ($this->adatok['erettsegi-eredmenyek'] as $value) {
            if(rtrim($value['eredmeny'], "%") < 20){
                array_push($tmp, $value['nev']);
            }          
        }
        return $tmp;
    }

    private function hianyzikErettsegiKotelezoTargy() {
        $tmp = [];
        foreach ($this->adatok['erettsegi-eredmenyek'] as $value) {
            array_push($tmp, $value['nev']);     
        }
        return array_diff(self::$kotelezoTargyak, $tmp);
    }

    private function szakKotelezoTargyPontok(){
        foreach(self::$szakok as $szak){ 
            $a = array_diff_assoc($this->adatok['valasztott-szak'], $szak);
            if(!$a){
                foreach($this->adatok['erettsegi-eredmenyek'] as $eredmeny){
                    if($eredmeny['nev'] == $szak['kotelezo']){
                        if($szak['kotelezo_minSzint'] == $eredmeny['tipus'] || $szak['kotelezo_minSzint'] == 'közép'){
                        return rtrim($eredmeny['eredmeny'], '%');
                        }
                        
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

    private static function arrayJoin(array $list, $conjunction = 'és') {
        $last = array_pop($list);
        if ($list) {
          return '"' . implode('", "', $list) . '"' . ' ' . $conjunction . ' ' . '"'. $last .'"';
        }
        return '"'. $last .'"';
    }

    public function pontszamitas(){
        if($this -> aTargyak20alatt()){
            $a = $this -> aTargyak20alatt();
            $c = count($a)>1 ? " tárgyakból ":" tárgyból ";
            echo "Hiba, nem lehetséges a pontszámítás a ". self::arrayJoin($a) . $c . "elért 20% alatti eredmény miatt.";
            echo "<br>";
            return;
        }

        if($this -> hianyzikErettsegiKotelezoTargy()){
            echo "Hiba, nem lehetséges a pontszámítás a kötelező érettségi ". self::arrayJoin($this -> hianyzikErettsegiKotelezoTargy()) ." hiánya miatt.";
            echo "<br>";
            return;
        }

        if(!$this->szakKotelezoTargyPontok()){
            echo "Hiba, a szakhoz kapcsolódó kötelező tárgyat mindenképpen választani kell.";
            echo "<br>";
            return;
        }

        if(!$this->szakLegnagyobbKotelezoenValaszthatoTargyPontok()){
            echo "Hiba, legalább egy kötelezően választható tárgyat mindenképpen választani kell.";
            echo "<br>";
            return;
        }
        
        echo $this->alapPontok() + $this->tobbletPontok() . " (" . $this->alapPontok() . " alappont + " . $this->tobbletPontok() . " többletpont)";
        echo "<br>";
    }
}


require_once('homework_input.php');

$felvetelizo0 = new Felvetelizo($exampleData0);
$felvetelizo0->pontszamitas();


// $felvetelizo1 = new Felvetelizo($exampleData1);
// $felvetelizo2 = new Felvetelizo($exampleData2);
// $felvetelizo3 = new Felvetelizo($exampleData3);
// $felvetelizo4 = new Felvetelizo($exampleData4);
// $felvetelizo5 = new Felvetelizo($exampleData5);
// $felvetelizo6 = new Felvetelizo($exampleData6);
// $felvetelizo7 = new Felvetelizo($exampleData7);

// $felvetelizo1->pontszamitas();
// $felvetelizo2->pontszamitas();
// $felvetelizo3->pontszamitas();
// $felvetelizo4->pontszamitas();
// $felvetelizo5->pontszamitas();
// $felvetelizo6->pontszamitas();
// $felvetelizo7->pontszamitas();




