<?php

// 忍者クラスを継承した日向一族
class HyugaClan extends Ninja
{    
    // メソッド
    public function executeByakugan()
    {
        $this->amount_chakra = $this->amount_chakra  - 30;
        print( "<br>" . "白眼を発動!!" .  "残りチャクラは" . $this->amount_chakra . "です");

    }
}