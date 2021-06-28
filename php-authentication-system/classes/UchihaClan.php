<?php

// 忍者クラスを継承したうちは一族
class UchihaClan extends Ninja
{    
    // メソッド
    public function executeSharingan()
    {
        $this->amount_chakra = $this->amount_chakra  - 30;
        print( "<br>" . "写輪眼を発動!!" .  "残りチャクラは" . $this->amount_chakra . "です");

    }
}